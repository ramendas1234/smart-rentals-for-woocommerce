<?php
/**
 * Smart Rentals WC Ajax class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Ajax' ) ) {

	class Smart_Rentals_WC_Ajax {

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Initialize AJAX actions
		 */
		public function init() {
			$ajax_actions = [
				'calculate_total',
				'check_availability',
				'add_to_cart',
				'get_calendar_data',
				'load_calendar',
				'admin_calendar_events',
			];

			foreach ( $ajax_actions as $action ) {
				add_action( 'wp_ajax_smart_rentals_' . $action, [ $this, 'smart_rentals_' . $action ] );
				add_action( 'wp_ajax_nopriv_smart_rentals_' . $action, [ $this, 'smart_rentals_' . $action ] );
			}
		}

		/**
		 * Verify nonce
		 */
		private function verify_nonce() {
			$security = smart_rentals_wc_get_meta_data( 'security', $_POST );
			
			if ( !$security || !wp_verify_nonce( $security, 'smart-rentals-security-ajax' ) ) {
				smart_rentals_wc_log( 'Security check failed. Nonce: ' . $security );
				wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
			}
		}

		/**
		 * Parse datetime string with multiple format support
		 */
		private function parse_datetime_string( $datetime_string ) {
			if ( empty( $datetime_string ) ) {
				return false;
			}

			// Try different datetime formats
			$formats = [
				'Y-m-d H:i',     // 2025-09-14 10:00
				'Y-m-d H:i:s',   // 2025-09-14 10:00:00
				'Y-m-d',         // 2025-09-14
				'm/d/Y H:i',     // 09/14/2025 10:00
				'm/d/Y',         // 09/14/2025
				'd-m-Y H:i',     // 14-09-2025 10:00
				'd-m-Y',         // 14-09-2025
			];

			foreach ( $formats as $format ) {
				$timestamp = DateTime::createFromFormat( $format, $datetime_string );
				if ( $timestamp && $timestamp->format( $format ) === $datetime_string ) {
					smart_rentals_wc_log( 'Successfully parsed datetime: ' . $datetime_string . ' with format: ' . $format );
					return $timestamp->getTimestamp();
				}
			}

			// Fallback to strtotime
			$timestamp = strtotime( $datetime_string );
			if ( $timestamp ) {
				smart_rentals_wc_log( 'Parsed datetime with strtotime: ' . $datetime_string . ' -> ' . $timestamp );
				return $timestamp;
			}

			smart_rentals_wc_log( 'Failed to parse datetime: ' . $datetime_string );
			return false;
		}

		/**
		 * Calculate rental total
		 */
		public function smart_rentals_calculate_total() {
			// Debug logging
			smart_rentals_wc_log( 'Calculate total called with data: ' . print_r( $_POST, true ) );
			
			$this->verify_nonce();

			// Product ID
			$product_id = absint( smart_rentals_wc_get_meta_data( 'product_id', $_POST ) );
			if ( !$product_id ) {
				smart_rentals_wc_log( 'Calculate total failed: No product ID' );
				wp_send_json_error( [ 'message' => __( 'Product ID is required', 'smart-rentals-wc' ) ] );
			}

			// Check if it's a rental product
			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				smart_rentals_wc_log( 'Calculate total failed: Product ' . $product_id . ' is not a rental product' );
				wp_send_json_error( [ 'message' => __( 'Product is not a rental product', 'smart-rentals-wc' ) ] );
			}

			smart_rentals_wc_log( 'Processing rental calculation for product: ' . $product_id );

			// Get form data
			$pickup_date = sanitize_text_field( smart_rentals_wc_get_meta_data( 'pickup_date', $_POST ) );
			$dropoff_date = sanitize_text_field( smart_rentals_wc_get_meta_data( 'dropoff_date', $_POST ) );
			$quantity = intval( smart_rentals_wc_get_meta_data( 'quantity', $_POST, 1 ) );

			if ( !$pickup_date || !$dropoff_date ) {
				smart_rentals_wc_log( 'Missing dates - Pickup: ' . $pickup_date . ', Dropoff: ' . $dropoff_date );
				wp_send_json_error( [ 'message' => __( 'Pickup and drop-off dates are required', 'smart-rentals-wc' ) ] );
			}

			// Parse datetime strings properly with enhanced handling
			// Handle different datetime formats that might come from daterangepicker
			$pickup_timestamp = $this->parse_datetime_string( $pickup_date );
			$dropoff_timestamp = $this->parse_datetime_string( $dropoff_date );

			// Enhanced validation with detailed logging
			if ( !$pickup_timestamp ) {
				smart_rentals_wc_log( 'Invalid pickup date format: ' . $pickup_date );
				wp_send_json_error( [ 'message' => __( 'Invalid pickup date format', 'smart-rentals-wc' ) ] );
			}

			if ( !$dropoff_timestamp ) {
				smart_rentals_wc_log( 'Invalid dropoff date format: ' . $dropoff_date );
				wp_send_json_error( [ 'message' => __( 'Invalid drop-off date format', 'smart-rentals-wc' ) ] );
			}

			if ( $pickup_timestamp >= $dropoff_timestamp ) {
				smart_rentals_wc_log( 'Invalid date range - Pickup: ' . $pickup_date . ' (' . $pickup_timestamp . '), Dropoff: ' . $dropoff_date . ' (' . $dropoff_timestamp . ')' );
				wp_send_json_error( [ 'message' => __( 'Drop-off date must be after pickup date', 'smart-rentals-wc' ) ] );
			}

			smart_rentals_wc_log( 'Valid date range - Pickup: ' . $pickup_date . ', Dropoff: ' . $dropoff_date );

			// Calculate price
			$total_price = Smart_Rentals_WC()->options->calculate_rental_price(
				$product_id,
				$pickup_timestamp,
				$dropoff_timestamp,
				$quantity
			);

			// Get duration text
			$duration_text = Smart_Rentals_WC()->options->get_rental_duration_text(
				$pickup_timestamp,
				$dropoff_timestamp
			);

			// Get security deposit (with global fallback)
			$security_deposit = smart_rentals_wc_get_security_deposit( $product_id );

			// Check availability
			$available = Smart_Rentals_WC()->options->check_availability(
				$product_id,
				$pickup_timestamp,
				$dropoff_timestamp,
				$quantity
			);

			// Get available quantity for display
			$available_quantity = Smart_Rentals_WC()->options->get_available_quantity( $product_id, $pickup_timestamp, $dropoff_timestamp );

			if ( !$available ) {
				wp_send_json_error( [ 'message' => __( 'Product not available for selected dates', 'smart-rentals-wc' ) ] );
			}

			$response_data = [
				'success' => true,
				'total_price' => $total_price,
				'formatted_price' => smart_rentals_wc_price( $total_price ),
				'duration_text' => $duration_text,
				'security_deposit' => $security_deposit,
				'formatted_deposit' => smart_rentals_wc_price( $security_deposit ),
				'available' => $available,
				'available_quantity' => $available_quantity,
				'cart_data' => [
					'product_id' => $product_id,
					'pickup_date' => $pickup_datetime,
					'dropoff_date' => $dropoff_datetime,
					'quantity' => $quantity,
				]
			];

			// Debug logging
			smart_rentals_wc_log( 'Calculate total response: ' . print_r( $response_data, true ) );

			wp_send_json_success( $response_data );
		}

		/**
		 * Check availability
		 */
		public function smart_rentals_check_availability() {
			$this->verify_nonce();

			$product_id = absint( smart_rentals_wc_get_meta_data( 'product_id', $_POST ) );
			$pickup_date = sanitize_text_field( smart_rentals_wc_get_meta_data( 'pickup_date', $_POST ) );
			$dropoff_date = sanitize_text_field( smart_rentals_wc_get_meta_data( 'dropoff_date', $_POST ) );
			$quantity = intval( smart_rentals_wc_get_meta_data( 'quantity', $_POST, 1 ) );
			
			// Debug log for quantity
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				smart_rentals_wc_log( 'AJAX add_to_cart - Product: ' . $product_id . ', Quantity: ' . $quantity );
			}

			if ( !$product_id || !$pickup_date || !$dropoff_date ) {
				wp_send_json_error( [ 'message' => __( 'Missing required parameters', 'smart-rentals-wc' ) ] );
			}

			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				wp_send_json_error( [ 'message' => __( 'Product is not a rental product', 'smart-rentals-wc' ) ] );
			}

			$pickup_timestamp = strtotime( $pickup_date );
			$dropoff_timestamp = strtotime( $dropoff_date );

			if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
				wp_send_json_error( [ 'message' => __( 'Invalid date range', 'smart-rentals-wc' ) ] );
			}

			$available = Smart_Rentals_WC()->options->check_availability(
				$product_id,
				$pickup_timestamp,
				$dropoff_timestamp,
				$quantity
			);

			if ( $available ) {
				$available_quantity = Smart_Rentals_WC()->options->get_available_quantity(
					$product_id,
					$pickup_timestamp,
					$dropoff_timestamp
				);

				wp_send_json_success([
					'available' => true,
					'available_quantity' => $available_quantity,
					'message' => sprintf( 
						_n( 
							'%d item available for selected dates', 
							'%d items available for selected dates', 
							$available_quantity, 
							'smart-rentals-wc' 
						), 
						$available_quantity 
					),
					'button_text' => __( 'Add to cart', 'smart-rentals-wc' ),
				]);
			} else {
				wp_send_json_success([
					'available' => false,
					'available_quantity' => 0,
					'message' => __( 'Not available for selected dates', 'smart-rentals-wc' ),
					'button_text' => __( 'Not available', 'smart-rentals-wc' ),
				]);
			}
		}

		/**
		 * Add to cart via AJAX
		 */
		public function smart_rentals_add_to_cart() {
			$this->verify_nonce();

			// Validation
			$passed_validation = apply_filters( 'smart_rentals_ajax_add_to_cart_validation', true, $_POST );
			if ( !$passed_validation ) {
				wp_send_json_error( [ 'message' => __( 'Validation failed', 'smart-rentals-wc' ) ] );
			}

			// Product ID
			$product_id = absint( smart_rentals_wc_get_meta_data( 'product_id', $_POST ) );
			if ( !$product_id ) {
				wp_send_json_error( [ 'message' => __( 'Product ID is required', 'smart-rentals-wc' ) ] );
			}

			// Check if it's a rental product
			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				wp_send_json_error( [ 'message' => __( 'Product is not a rental product', 'smart-rentals-wc' ) ] );
			}

			// Get cart item data
			$cart_item = smart_rentals_wc_get_meta_data( 'cart_item', $_POST );
			
			if ( smart_rentals_wc_array_exists( $cart_item ) ) {
				// Get quantity from cart item
				$quantity = intval( smart_rentals_wc_get_meta_data( 'quantity', $cart_item, 1 ) );
				
				// Debug log for quantity
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					smart_rentals_wc_log( 'Add to cart - Quantity from cart_item: ' . $quantity );
					smart_rentals_wc_log( 'Add to cart - Cart item data: ' . print_r( $cart_item, true ) );
				}

				// Validate rental data
				$rental_data = smart_rentals_wc_get_meta_data( 'rental_data', $cart_item );
				if ( !smart_rentals_wc_array_exists( $rental_data ) ) {
					wp_send_json_error( [ 'message' => __( 'Rental data is missing', 'smart-rentals-wc' ) ] );
				}

				// Validate dates
				$pickup_date = smart_rentals_wc_get_meta_data( 'pickup_date', $rental_data );
				$dropoff_date = smart_rentals_wc_get_meta_data( 'dropoff_date', $rental_data );

				if ( !$pickup_date || !$dropoff_date ) {
					wp_send_json_error( [ 'message' => __( 'Pickup and drop-off dates are required', 'smart-rentals-wc' ) ] );
				}

				$pickup_timestamp = strtotime( $pickup_date );
				$dropoff_timestamp = strtotime( $dropoff_date );

				if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
					wp_send_json_error( [ 'message' => __( 'Invalid date range', 'smart-rentals-wc' ) ] );
				}

				// Final availability check
				if ( !Smart_Rentals_WC()->options->check_availability( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity ) ) {
					wp_send_json_error( [ 'message' => __( 'Product not available for selected dates', 'smart-rentals-wc' ) ] );
				}

				// Add to cart with rental data
				try {
					$cart_item_key = WC()->cart->add_to_cart( $product_id, $quantity, 0, [], $cart_item );
					
					if ( false !== $cart_item_key ) {
						// Success - redirect to cart
						$cart_url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart' );
						
						// Log successful cart addition
						smart_rentals_wc_log( 'Successfully added to cart: ' . $cart_item_key );
						
						wp_send_json_success([
							'message' => __( 'Product added to cart successfully', 'smart-rentals-wc' ),
							'cart_url' => $cart_url,
							'cart_item_key' => $cart_item_key,
						]);
					} else {
						// Get WooCommerce notices
						$notices = function_exists( 'wc_get_notices' ) ? wc_get_notices( 'error' ) : [];
						$error_message = __( 'Failed to add product to cart', 'smart-rentals-wc' );
						
						if ( smart_rentals_wc_array_exists( $notices ) ) {
							$error_message = $notices[0]['notice'] ?? $error_message;
						}
						
						smart_rentals_wc_log( 'Failed to add to cart: ' . $error_message );
						wp_send_json_error( [ 'message' => $error_message ] );
					}
				} catch ( Exception $e ) {
					smart_rentals_wc_log( 'Exception adding to cart: ' . $e->getMessage() );
					wp_send_json_error( [ 'message' => __( 'Error adding to cart: ', 'smart-rentals-wc' ) . $e->getMessage() ] );
				}
			} else {
				wp_send_json_error( [ 'message' => __( 'Cart item data is required', 'smart-rentals-wc' ) ] );
			}
		}

		/**
		 * Get calendar data
		 */
		public function smart_rentals_get_calendar_data() {
			$this->verify_nonce();

			$product_id = intval( smart_rentals_wc_get_meta_data( 'product_id', $_POST ) );
			$start_date = sanitize_text_field( smart_rentals_wc_get_meta_data( 'start_date', $_POST, '' ) );
			$end_date = sanitize_text_field( smart_rentals_wc_get_meta_data( 'end_date', $_POST, '' ) );

			if ( !$product_id ) {
				wp_send_json_error( [ 'message' => __( 'Missing product ID', 'smart-rentals-wc' ) ] );
			}

			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				wp_send_json_error( [ 'message' => __( 'Product is not a rental product', 'smart-rentals-wc' ) ] );
			}

			// Get booked dates
			$booked_dates = Smart_Rentals_WC()->options->get_booked_dates( $product_id );
			
			$events = [];
			
			foreach ( $booked_dates as $booking ) {
				$events[] = [
					'title' => sprintf( __( 'Booked (%d)', 'smart-rentals-wc' ), $booking->quantity ),
					'start' => $booking->pickup_date,
					'end' => $booking->dropoff_date,
					'className' => 'smart-rentals-booked',
					'allDay' => true,
				];
			}

			wp_send_json_success([
				'events' => $events,
				'product_id' => $product_id,
			]);
		}

		/**
		 * Load calendar via AJAX
		 */
		public function smart_rentals_load_calendar() {
			// Verify nonce
			if ( !wp_verify_nonce( $_POST['nonce'], 'smart_rentals_calendar_nonce' ) ) {
				wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
			}

			$product_id = intval( $_POST['product_id'] );
			$month = intval( $_POST['month'] );
			$year = intval( $_POST['year'] );

			if ( !$product_id || !smart_rentals_wc_is_rental_product( $product_id ) ) {
				wp_send_json_error( [ 'message' => __( 'Invalid product', 'smart-rentals-wc' ) ] );
			}

			// Validate month and year
			if ( $month < 1 || $month > 12 || $year < date( 'Y' ) || $year > date( 'Y' ) + 2 ) {
				wp_send_json_error( [ 'message' => __( 'Invalid date range', 'smart-rentals-wc' ) ] );
			}

			// Set GET parameters for the template
			$_GET['cal_month'] = $month;
			$_GET['cal_year'] = $year;

			// Capture the calendar template output
			ob_start();
			$template_path = SMART_RENTALS_WC_PLUGIN_TEMPLATES . 'single/calendar.php';
			if ( file_exists( $template_path ) ) {
				include $template_path;
			}
			$html = ob_get_clean();

			wp_send_json_success( [ 'html' => $html ] );
		}

		/**
		 * Get admin calendar events via AJAX
		 */
		public function smart_rentals_admin_calendar_events() {
			// Check permissions
			if ( !current_user_can( 'manage_woocommerce' ) ) {
				wp_send_json_error( [ 'message' => __( 'Permission denied', 'smart-rentals-wc' ) ] );
			}

			// Verify nonce
			if ( !wp_verify_nonce( $_POST['nonce'], 'smart_rentals_admin_calendar' ) ) {
				wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
			}

			$product_id = isset( $_POST['product_id'] ) ? intval( $_POST['product_id'] ) : 0;
			$status = isset( $_POST['status'] ) ? sanitize_text_field( $_POST['status'] ) : '';

			// Get events from both sources (like the main get_calendar_events method)
			$events = $this->get_admin_calendar_events_data( $product_id, $status );

			wp_send_json_success( [ 'events' => $events ] );
		}

		/**
		 * Get admin calendar events data (shared method)
		 */
		private function get_admin_calendar_events_data( $product_id = 0, $status = '' ) {
			global $wpdb;
			$events = [];
			
			// First, try custom bookings table
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$where_conditions = [ "b.status IN ('pending', 'confirmed', 'active', 'processing', 'completed')" ];
				$prepare_values = [];

				if ( $product_id ) {
					$where_conditions[] = "b.product_id = %d";
					$prepare_values[] = $product_id;
				}

				if ( $status ) {
					$where_conditions[] = "b.status = %s";
					$prepare_values[] = $status;
				}

				$where_clause = implode( ' AND ', $where_conditions );
				$query = "
					SELECT b.*, p.post_title as product_name
					FROM $table_name b
					LEFT JOIN {$wpdb->posts} p ON b.product_id = p.ID
					WHERE $where_clause
					ORDER BY b.pickup_date ASC
				";

				if ( !empty( $prepare_values ) ) {
					$bookings = $wpdb->get_results( $wpdb->prepare( $query, $prepare_values ) );
				} else {
					$bookings = $wpdb->get_results( $query );
				}

				foreach ( $bookings as $booking ) {
					$events[] = [
						'id' => 'custom_' . $booking->id,
						'title' => $booking->product_name . ' (' . $booking->quantity . ')',
						'start' => $booking->pickup_date,
						'end' => $booking->dropoff_date,
						'backgroundColor' => $this->get_booking_color( $booking->status ),
						'borderColor' => $this->get_booking_color( $booking->status ),
						'textColor' => '#ffffff',
						'extendedProps' => [
							'booking_id' => $booking->id,
							'product_id' => $booking->product_id,
							'quantity' => $booking->quantity,
							'status' => $booking->status,
							'total_price' => $booking->total_price,
							'security_deposit' => $booking->security_deposit,
							'source' => 'custom_table'
						]
					];
				}
			}
			
			// Also check WooCommerce orders (primary source like external plugin)
			$order_status = [ 'wc-processing', 'wc-completed', 'wc-on-hold', 'wc-pending' ];
			$status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );
			
			$where_conditions = [ "rental_check.meta_value = 'yes'" ];
			$where_conditions[] = "orders.post_status IN ('{$status_placeholders}')";
			$where_conditions[] = "pickup_date.meta_value IS NOT NULL";
			$where_conditions[] = "dropoff_date.meta_value IS NOT NULL";
			$where_conditions[] = "product_meta.meta_value IS NOT NULL";
			
			$prepare_values = [
				smart_rentals_wc_meta_key( 'pickup_date' ),
				smart_rentals_wc_meta_key( 'dropoff_date' ),
				smart_rentals_wc_meta_key( 'rental_quantity' ),
				smart_rentals_wc_meta_key( 'is_rental' ),
				'_product_id'
			];

			if ( $product_id ) {
				$where_conditions[] = "product_meta.meta_value = %d";
				$prepare_values[] = $product_id;
			}

			$where_clause = implode( ' AND ', $where_conditions );
			
			$order_bookings = $wpdb->get_results( $wpdb->prepare("
				SELECT 
					orders.ID as order_id,
					orders.post_date as order_date,
					items.order_item_name as product_name,
					pickup_date.meta_value as pickup_date,
					dropoff_date.meta_value as dropoff_date,
					quantity.meta_value as quantity,
					product_meta.meta_value as product_id,
					orders.post_status as order_status
				FROM {$wpdb->prefix}woocommerce_order_items AS items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pickup_date 
					ON items.order_item_id = pickup_date.order_item_id 
					AND pickup_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS dropoff_date 
					ON items.order_item_id = dropoff_date.order_item_id 
					AND dropoff_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS quantity 
					ON items.order_item_id = quantity.order_item_id 
					AND quantity.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS rental_check 
					ON items.order_item_id = rental_check.order_item_id 
					AND rental_check.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta 
					ON items.order_item_id = product_meta.order_item_id 
					AND product_meta.meta_key = %s
				LEFT JOIN {$wpdb->posts} AS orders 
					ON items.order_id = orders.ID
				WHERE $where_clause
				ORDER BY pickup_date.meta_value ASC
			", $prepare_values ));

			foreach ( $order_bookings as $booking ) {
				if ( $booking->pickup_date && $booking->dropoff_date && $booking->product_id ) {
					// Convert order status to booking status
					$booking_status = 'pending';
					if ( $booking->order_status === 'wc-processing' ) {
						$booking_status = 'confirmed';
					} elseif ( $booking->order_status === 'wc-completed' ) {
						$booking_status = 'active';
					} elseif ( $booking->order_status === 'wc-on-hold' ) {
						$booking_status = 'pending';
					}
					
					// Skip if filtering by status and it doesn't match
					if ( $status && $booking_status !== $status ) {
						continue;
					}
					
					$events[] = [
						'id' => 'order_' . $booking->order_id,
						'title' => $booking->product_name . ' (' . ($booking->quantity ?: 1) . ')',
						'start' => $booking->pickup_date,
						'end' => $booking->dropoff_date,
						'backgroundColor' => $this->get_booking_color( $booking_status ),
						'borderColor' => $this->get_booking_color( $booking_status ),
						'textColor' => '#ffffff',
						'extendedProps' => [
							'booking_id' => $booking->order_id,
							'product_id' => $booking->product_id,
							'quantity' => $booking->quantity ?: 1,
							'status' => $booking_status,
							'order_status' => $booking->order_status,
							'source' => 'woocommerce_order'
						]
					];
				}
			}
			
			return $events;
		}

		/**
		 * Get booking color based on status
		 */
		private function get_booking_color( $status ) {
			$colors = [
				'pending' => '#ffc107',     // Yellow
				'confirmed' => '#28a745',   // Green
				'active' => '#17a2b8',      // Blue
				'processing' => '#fd7e14',  // Orange
				'completed' => '#6f42c1',   // Purple
				'cancelled' => '#dc3545',   // Red
			];
			
			return isset( $colors[$status] ) ? $colors[$status] : '#6c757d';
		}
	}
}