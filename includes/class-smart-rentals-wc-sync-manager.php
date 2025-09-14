<?php
/**
 * Smart Rentals WC Sync Manager class
 * Ensures tight synchronization between frontend and backend
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Sync_Manager' ) ) {

	class Smart_Rentals_WC_Sync_Manager {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Real-time sync hooks
			add_action( 'woocommerce_add_to_cart', [ $this, 'sync_cart_addition' ], 10, 6 );
			add_action( 'woocommerce_remove_cart_item', [ $this, 'sync_cart_removal' ], 10, 2 );
			add_action( 'woocommerce_cart_item_restored', [ $this, 'sync_cart_restoration' ], 10, 2 );
			add_action( 'woocommerce_checkout_order_created', [ $this, 'sync_order_creation' ], 10, 1 );
			add_action( 'woocommerce_order_status_changed', [ $this, 'sync_order_status_change' ], 10, 4 );
			add_action( 'before_delete_post', [ $this, 'sync_order_deletion' ], 10, 1 );
			add_action( 'wp_trash_post', [ $this, 'sync_order_trash' ], 10, 1 );
			add_action( 'untrashed_post', [ $this, 'sync_order_restore' ], 10, 1 );
			
			// Admin modification sync
			add_action( 'woocommerce_saved_order_items', [ $this, 'sync_admin_order_modification' ], 10, 2 );
			
			// AJAX endpoints for real-time sync
			add_action( 'wp_ajax_smart_rentals_sync_availability', [ $this, 'ajax_sync_availability' ] );
			add_action( 'wp_ajax_nopriv_smart_rentals_sync_availability', [ $this, 'ajax_sync_availability' ] );
			add_action( 'wp_ajax_smart_rentals_validate_booking', [ $this, 'ajax_validate_booking' ] );
			add_action( 'wp_ajax_nopriv_smart_rentals_validate_booking', [ $this, 'ajax_validate_booking' ] );
			
			// Session-based temporary booking locks
			add_action( 'init', [ $this, 'init_session' ] );
			add_action( 'wp_logout', [ $this, 'clear_user_locks' ] );
			add_action( 'wp_login', [ $this, 'clear_expired_locks' ], 10, 2 );
			
			// Cleanup cron
			add_action( 'smart_rentals_cleanup_locks', [ $this, 'cleanup_expired_locks' ] );
			if ( !wp_next_scheduled( 'smart_rentals_cleanup_locks' ) ) {
				wp_schedule_event( time(), 'hourly', 'smart_rentals_cleanup_locks' );
			}
		}

		/**
		 * Initialize session for temporary locks
		 */
		public function init_session() {
			if ( !session_id() && !headers_sent() ) {
				session_start();
			}
		}

		/**
		 * Sync cart addition with real-time availability check
		 */
		public function sync_cart_addition( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {
			// Only process rental items
			if ( !isset( $cart_item_data['rental_data'] ) ) {
				return;
			}
			
			$rental_data = $cart_item_data['rental_data'];
			
			// Create temporary lock for this booking
			$this->create_temporary_lock( 
				$product_id, 
				$rental_data['pickup_date'], 
				$rental_data['dropoff_date'], 
				$quantity,
				$cart_item_key
			);
			
			// Log the cart addition for sync
			$this->log_sync_event( 'cart_add', [
				'product_id' => $product_id,
				'pickup_date' => $rental_data['pickup_date'],
				'dropoff_date' => $rental_data['dropoff_date'],
				'quantity' => $quantity,
				'cart_item_key' => $cart_item_key,
				'session_id' => session_id(),
				'user_id' => get_current_user_id()
			]);
		}

		/**
		 * Sync cart removal
		 */
		public function sync_cart_removal( $cart_item_key, $cart ) {
			// Get cart item before removal
			$cart_item = WC()->cart->get_cart_item( $cart_item_key );
			
			if ( $cart_item && isset( $cart_item['rental_data'] ) ) {
				// Remove temporary lock
				$this->remove_temporary_lock( $cart_item_key );
				
				// Log the removal
				$this->log_sync_event( 'cart_remove', [
					'cart_item_key' => $cart_item_key,
					'product_id' => $cart_item['product_id'],
					'session_id' => session_id()
				]);
			}
		}

		/**
		 * Sync cart restoration
		 */
		public function sync_cart_restoration( $cart_item_key, $cart ) {
			$cart_item = WC()->cart->get_cart_item( $cart_item_key );
			
			if ( $cart_item && isset( $cart_item['rental_data'] ) ) {
				$rental_data = $cart_item['rental_data'];
				
				// Re-validate availability
				$available = Smart_Rentals_WC()->options->check_availability(
					$cart_item['product_id'],
					strtotime( $rental_data['pickup_date'] ),
					strtotime( $rental_data['dropoff_date'] ),
					$cart_item['quantity']
				);
				
				if ( !$available ) {
					// Remove from cart if no longer available
					WC()->cart->remove_cart_item( $cart_item_key );
					wc_add_notice( __( 'The rental item is no longer available for the selected dates.', 'smart-rentals-wc' ), 'error' );
					return;
				}
				
				// Recreate temporary lock
				$this->create_temporary_lock( 
					$cart_item['product_id'], 
					$rental_data['pickup_date'], 
					$rental_data['dropoff_date'], 
					$cart_item['quantity'],
					$cart_item_key
				);
			}
		}

		/**
		 * Sync order creation
		 */
		public function sync_order_creation( $order ) {
			foreach ( $order->get_items() as $item ) {
				if ( $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) ) === 'yes' ) {
					// Ensure booking record exists
					$this->ensure_booking_record( $order, $item );
					
					// Convert temporary locks to permanent bookings
					$this->convert_locks_to_bookings( $order );
				}
			}
		}

		/**
		 * Sync order status change
		 */
		public function sync_order_status_change( $order_id, $from_status, $to_status, $order ) {
			// Update booking records based on status
			global $wpdb;
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return;
			}
			
			$booking_status = $this->map_order_status_to_booking_status( $to_status );
			
			$wpdb->update(
				$table_name,
				[ 
					'status' => $booking_status,
					'updated_at' => current_time( 'mysql' )
				],
				[ 'order_id' => $order_id ],
				[ '%s', '%s' ],
				[ '%d' ]
			);
			
			// Clear any remaining locks for this order
			if ( in_array( $to_status, ['cancelled', 'refunded', 'failed'] ) ) {
				$this->clear_order_locks( $order_id );
			}
			
			// Log sync event
			$this->log_sync_event( 'order_status_change', [
				'order_id' => $order_id,
				'from_status' => $from_status,
				'to_status' => $to_status,
				'booking_status' => $booking_status
			]);
		}

		/**
		 * Sync order deletion
		 */
		public function sync_order_deletion( $post_id ) {
			if ( get_post_type( $post_id ) !== 'shop_order' ) {
				return;
			}
			
			// Mark bookings as deleted
			global $wpdb;
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$wpdb->update(
					$table_name,
					[ 'status' => 'deleted' ],
					[ 'order_id' => $post_id ],
					[ '%s' ],
					[ '%d' ]
				);
			}
			
			// Clear locks
			$this->clear_order_locks( $post_id );
		}

		/**
		 * Sync order trash
		 */
		public function sync_order_trash( $post_id ) {
			if ( get_post_type( $post_id ) !== 'shop_order' ) {
				return;
			}
			
			// Mark bookings as trashed
			global $wpdb;
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$wpdb->update(
					$table_name,
					[ 'status' => 'trashed' ],
					[ 'order_id' => $post_id ],
					[ '%s' ],
					[ '%d' ]
				);
			}
		}

		/**
		 * Sync order restore from trash
		 */
		public function sync_order_restore( $post_id ) {
			if ( get_post_type( $post_id ) !== 'shop_order' ) {
				return;
			}
			
			$order = wc_get_order( $post_id );
			if ( !$order ) {
				return;
			}
			
			// Restore booking status based on order status
			$booking_status = $this->map_order_status_to_booking_status( $order->get_status() );
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$wpdb->update(
					$table_name,
					[ 'status' => $booking_status ],
					[ 'order_id' => $post_id ],
					[ '%s' ],
					[ '%d' ]
				);
			}
		}

		/**
		 * Sync admin order modification
		 */
		public function sync_admin_order_modification( $order_id, $items ) {
			$order = wc_get_order( $order_id );
			if ( !$order ) {
				return;
			}
			
			global $wpdb;
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return;
			}
			
			// Process each item
			foreach ( $order->get_items() as $item_id => $item ) {
				if ( $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) ) === 'yes' ) {
					$pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
					$dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
					$quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: $item->get_quantity();
					
					// Check if booking record exists
					$booking_id = $wpdb->get_var( $wpdb->prepare(
						"SELECT id FROM $table_name WHERE order_id = %d AND product_id = %d",
						$order_id,
						$item->get_product_id()
					));
					
					if ( $booking_id ) {
						// Update existing booking
						$wpdb->update(
							$table_name,
							[
								'pickup_date' => $pickup_date,
								'dropoff_date' => $dropoff_date,
								'quantity' => $quantity,
								'updated_at' => current_time( 'mysql' )
							],
							[ 'id' => $booking_id ],
							[ '%s', '%s', '%d', '%s' ],
							[ '%d' ]
						);
					} else {
						// Create new booking record
						$this->ensure_booking_record( $order, $item );
					}
				}
			}
		}

		/**
		 * AJAX: Sync availability in real-time
		 */
		public function ajax_sync_availability() {
			// Verify nonce
			if ( !wp_verify_nonce( $_POST['security'], 'smart-rentals-security-ajax' ) ) {
				wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
			}
			
			$product_id = intval( $_POST['product_id'] );
			$date = sanitize_text_field( $_POST['date'] );
			
			if ( !$product_id || !$date ) {
				wp_send_json_error( [ 'message' => __( 'Invalid parameters', 'smart-rentals-wc' ) ] );
			}
			
			// Get real-time availability including temporary locks
			$available_quantity = $this->get_real_time_availability( $product_id, $date );
			
			// Get all bookings for this date
			$bookings = $this->get_bookings_for_date( $product_id, $date );
			
			wp_send_json_success([
				'available_quantity' => $available_quantity,
				'bookings' => $bookings,
				'timestamp' => current_time( 'timestamp' ),
				'date' => $date
			]);
		}

		/**
		 * AJAX: Validate booking before submission
		 */
		public function ajax_validate_booking() {
			// Verify nonce
			if ( !wp_verify_nonce( $_POST['security'], 'smart-rentals-security-ajax' ) ) {
				wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
			}
			
			$product_id = intval( $_POST['product_id'] );
			$pickup_date = sanitize_text_field( $_POST['pickup_date'] );
			$dropoff_date = sanitize_text_field( $_POST['dropoff_date'] );
			$quantity = intval( $_POST['quantity'] );
			
			// Comprehensive validation
			$validation_result = $this->validate_booking_comprehensive(
				$product_id,
				$pickup_date,
				$dropoff_date,
				$quantity
			);
			
			if ( $validation_result['valid'] ) {
				// Create provisional lock
				$lock_id = $this->create_provisional_lock(
					$product_id,
					$pickup_date,
					$dropoff_date,
					$quantity
				);
				
				$validation_result['lock_id'] = $lock_id;
			}
			
			wp_send_json_success( $validation_result );
		}

		/**
		 * Create temporary lock for cart items
		 */
		private function create_temporary_lock( $product_id, $pickup_date, $dropoff_date, $quantity, $cart_item_key ) {
			$lock_data = [
				'product_id' => $product_id,
				'pickup_date' => $pickup_date,
				'dropoff_date' => $dropoff_date,
				'quantity' => $quantity,
				'cart_item_key' => $cart_item_key,
				'session_id' => session_id(),
				'user_id' => get_current_user_id(),
				'created_at' => current_time( 'timestamp' ),
				'expires_at' => current_time( 'timestamp' ) + 900 // 15 minutes
			];
			
			// Store in transient with unique key
			$lock_key = 'smart_rentals_lock_' . $cart_item_key;
			set_transient( $lock_key, $lock_data, 900 );
			
			// Also store in session for quick access
			if ( !isset( $_SESSION['smart_rentals_locks'] ) ) {
				$_SESSION['smart_rentals_locks'] = [];
			}
			$_SESSION['smart_rentals_locks'][$cart_item_key] = $lock_key;
			
			return $lock_key;
		}

		/**
		 * Remove temporary lock
		 */
		private function remove_temporary_lock( $cart_item_key ) {
			if ( isset( $_SESSION['smart_rentals_locks'][$cart_item_key] ) ) {
				$lock_key = $_SESSION['smart_rentals_locks'][$cart_item_key];
				delete_transient( $lock_key );
				unset( $_SESSION['smart_rentals_locks'][$cart_item_key] );
			}
		}

		/**
		 * Get real-time availability including locks
		 */
		private function get_real_time_availability( $product_id, $date ) {
			// Get base availability
			$base_availability = Smart_Rentals_WC()->options->get_calendar_day_availability( $product_id, $date );
			
			// Get active locks for this product and date
			$locks_quantity = $this->get_active_locks_quantity( $product_id, $date );
			
			// Subtract locked quantity
			$real_availability = max( 0, $base_availability - $locks_quantity );
			
			return $real_availability;
		}

		/**
		 * Get active locks quantity
		 */
		private function get_active_locks_quantity( $product_id, $date ) {
			global $wpdb;
			
			$total_locked = 0;
			$date_timestamp = strtotime( $date );
			$date_start = $date_timestamp;
			$date_end = $date_timestamp + 86400 - 1;
			
			// Search all lock transients
			$lock_transients = $wpdb->get_results( 
				"SELECT option_name, option_value 
				FROM {$wpdb->options} 
				WHERE option_name LIKE '_transient_smart_rentals_lock_%' 
				AND option_name NOT LIKE '_transient_timeout_%'"
			);
			
			foreach ( $lock_transients as $transient ) {
				$lock_data = maybe_unserialize( $transient->option_value );
				
				if ( is_array( $lock_data ) && 
					 $lock_data['product_id'] == $product_id &&
					 $lock_data['expires_at'] > current_time( 'timestamp' ) ) {
					
					$pickup_timestamp = strtotime( $lock_data['pickup_date'] );
					$dropoff_timestamp = strtotime( $lock_data['dropoff_date'] );
					
					// Check if lock period overlaps with the date
					if ( $pickup_timestamp <= $date_end && $dropoff_timestamp >= $date_start ) {
						$total_locked += intval( $lock_data['quantity'] );
					}
				}
			}
			
			return $total_locked;
		}

		/**
		 * Validate booking comprehensively
		 */
		private function validate_booking_comprehensive( $product_id, $pickup_date, $dropoff_date, $quantity ) {
			$result = [
				'valid' => true,
				'message' => '',
				'details' => []
			];
			
			// Check product is rental
			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				$result['valid'] = false;
				$result['message'] = __( 'Product is not available for rental', 'smart-rentals-wc' );
				return $result;
			}
			
			// Validate dates
			$pickup_timestamp = strtotime( $pickup_date );
			$dropoff_timestamp = strtotime( $dropoff_date );
			
			if ( !$pickup_timestamp || !$dropoff_timestamp ) {
				$result['valid'] = false;
				$result['message'] = __( 'Invalid date format', 'smart-rentals-wc' );
				return $result;
			}
			
			if ( $pickup_timestamp >= $dropoff_timestamp ) {
				$result['valid'] = false;
				$result['message'] = __( 'Drop-off date must be after pickup date', 'smart-rentals-wc' );
				return $result;
			}
			
			// Check real-time availability
			$available = Smart_Rentals_WC()->options->check_availability(
				$product_id,
				$pickup_timestamp,
				$dropoff_timestamp,
				$quantity
			);
			
			if ( !$available ) {
				$available_quantity = Smart_Rentals_WC()->options->get_available_quantity(
					$product_id,
					$pickup_timestamp,
					$dropoff_timestamp
				);
				
				$result['valid'] = false;
				$result['message'] = sprintf(
					__( 'Only %d units available for selected dates', 'smart-rentals-wc' ),
					$available_quantity
				);
				$result['details']['available_quantity'] = $available_quantity;
				return $result;
			}
			
			// Check for active locks
			$locks_quantity = 0;
			$current_date = $pickup_timestamp;
			while ( $current_date <= $dropoff_timestamp ) {
				$date_string = date( 'Y-m-d', $current_date );
				$day_locks = $this->get_active_locks_quantity( $product_id, $date_string );
				$locks_quantity = max( $locks_quantity, $day_locks );
				$current_date += 86400;
			}
			
			if ( $locks_quantity > 0 ) {
				$result['details']['active_locks'] = $locks_quantity;
			}
			
			// All checks passed
			$result['message'] = __( 'Booking is available', 'smart-rentals-wc' );
			$result['details']['rental_stock'] = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
			
			return $result;
		}

		/**
		 * Ensure booking record exists
		 */
		private function ensure_booking_record( $order, $item ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return;
			}
			
			$order_id = $order->get_id();
			$product_id = $item->get_product_id();
			
			// Check if record exists
			$exists = $wpdb->get_var( $wpdb->prepare(
				"SELECT COUNT(*) FROM $table_name WHERE order_id = %d AND product_id = %d",
				$order_id,
				$product_id
			));
			
			if ( !$exists ) {
				// Create booking record
				$pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
				$dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
				$quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: $item->get_quantity();
				
				$wpdb->insert(
					$table_name,
					[
						'order_id' => $order_id,
						'product_id' => $product_id,
						'pickup_date' => $pickup_date,
						'dropoff_date' => $dropoff_date,
						'quantity' => $quantity,
						'status' => $this->map_order_status_to_booking_status( $order->get_status() ),
						'created_at' => current_time( 'mysql' ),
						'updated_at' => current_time( 'mysql' )
					],
					[ '%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s' ]
				);
			}
		}

		/**
		 * Map order status to booking status
		 */
		private function map_order_status_to_booking_status( $order_status ) {
			$status_map = [
				'pending' => 'pending',
				'processing' => 'confirmed',
				'on-hold' => 'pending',
				'completed' => 'completed',
				'cancelled' => 'cancelled',
				'refunded' => 'cancelled',
				'failed' => 'failed'
			];
			
			return isset( $status_map[$order_status] ) ? $status_map[$order_status] : 'pending';
		}

		/**
		 * Log sync event
		 */
		private function log_sync_event( $event_type, $data ) {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				smart_rentals_wc_log( sprintf(
					'Sync Event: %s | Data: %s',
					$event_type,
					json_encode( $data )
				));
			}
		}

		/**
		 * Cleanup expired locks
		 */
		public function cleanup_expired_locks() {
			global $wpdb;
			
			// Delete expired lock transients
			$wpdb->query( 
				"DELETE FROM {$wpdb->options} 
				WHERE option_name LIKE '_transient_timeout_smart_rentals_lock_%' 
				AND option_value < " . time()
			);
			
			smart_rentals_wc_log( 'Cleaned up expired rental locks' );
		}

		/**
		 * Clear user locks on logout
		 */
		public function clear_user_locks() {
			if ( isset( $_SESSION['smart_rentals_locks'] ) ) {
				foreach ( $_SESSION['smart_rentals_locks'] as $cart_item_key => $lock_key ) {
					delete_transient( $lock_key );
				}
				unset( $_SESSION['smart_rentals_locks'] );
			}
		}

		/**
		 * Get bookings for specific date
		 */
		private function get_bookings_for_date( $product_id, $date ) {
			global $wpdb;
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return [];
			}
			
			$date_start = $date . ' 00:00:00';
			$date_end = $date . ' 23:59:59';
			
			$bookings = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM $table_name 
				WHERE product_id = %d 
				AND status IN ('pending', 'confirmed', 'active', 'processing', 'completed')
				AND (
					(pickup_date <= %s AND dropoff_date >= %s) OR
					(pickup_date >= %s AND pickup_date <= %s) OR
					(dropoff_date >= %s AND dropoff_date <= %s)
				)
				ORDER BY pickup_date ASC",
				$product_id,
				$date_end, $date_start,
				$date_start, $date_end,
				$date_start, $date_end
			));
			
			return $bookings;
		}

		/**
		 * Convert temporary locks to permanent bookings
		 */
		private function convert_locks_to_bookings( $order ) {
			// Clear all locks for items in this order
			foreach ( $order->get_items() as $item ) {
				if ( $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) ) === 'yes' ) {
					// Clear any locks for this product/date combination
					$this->clear_product_locks( 
						$item->get_product_id(),
						$item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) ),
						$item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) )
					);
				}
			}
		}

		/**
		 * Clear product locks
		 */
		private function clear_product_locks( $product_id, $pickup_date, $dropoff_date ) {
			global $wpdb;
			
			// Find and delete matching locks
			$lock_transients = $wpdb->get_results( 
				"SELECT option_name, option_value 
				FROM {$wpdb->options} 
				WHERE option_name LIKE '_transient_smart_rentals_lock_%'"
			);
			
			foreach ( $lock_transients as $transient ) {
				$lock_data = maybe_unserialize( $transient->option_value );
				
				if ( is_array( $lock_data ) && 
					 $lock_data['product_id'] == $product_id &&
					 $lock_data['pickup_date'] == $pickup_date &&
					 $lock_data['dropoff_date'] == $dropoff_date ) {
					
					delete_transient( str_replace( '_transient_', '', $transient->option_name ) );
				}
			}
		}

		/**
		 * Clear all locks for an order
		 */
		private function clear_order_locks( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( !$order ) {
				return;
			}
			
			foreach ( $order->get_items() as $item ) {
				if ( $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) ) === 'yes' ) {
					$this->clear_product_locks(
						$item->get_product_id(),
						$item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) ),
						$item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) )
					);
				}
			}
		}

		/**
		 * Create provisional lock for validation
		 */
		private function create_provisional_lock( $product_id, $pickup_date, $dropoff_date, $quantity ) {
			$lock_id = 'provisional_' . uniqid();
			$lock_data = [
				'product_id' => $product_id,
				'pickup_date' => $pickup_date,
				'dropoff_date' => $dropoff_date,
				'quantity' => $quantity,
				'type' => 'provisional',
				'session_id' => session_id(),
				'user_id' => get_current_user_id(),
				'created_at' => current_time( 'timestamp' ),
				'expires_at' => current_time( 'timestamp' ) + 300 // 5 minutes
			];
			
			set_transient( 'smart_rentals_lock_' . $lock_id, $lock_data, 300 );
			
			return $lock_id;
		}

		/**
		 * Clear expired locks on login
		 */
		public function clear_expired_locks( $user_login, $user ) {
			$this->cleanup_expired_locks();
		}

		/**
		 * Main instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
}