<?php
/**
 * Smart Rentals WC Booking class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Booking' ) ) {

	class Smart_Rentals_WC_Booking {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Cart validation
			add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'add_to_cart_validation' ], 11, 3 );

			// Add cart item data
			add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 11, 4 );

			// Get cart item data
			add_filter( 'woocommerce_get_item_data', [ $this, 'get_item_data' ], 11, 2 );

			// Cart item price
			add_filter( 'woocommerce_cart_item_price', [ $this, 'cart_item_price' ], 11, 3 );

			// Before calculate totals
			add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_calculate_totals' ], 11 );

			// Checkout create order line item
			add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'checkout_create_order_line_item' ], 11, 4 );
			
			// Set rental order status after order creation
			add_action( 'woocommerce_checkout_order_created', [ $this, 'set_rental_order_status' ], 20, 1 );
			
			// Also try after order is processed (for different payment methods)
			add_action( 'woocommerce_order_status_changed', [ $this, 'maybe_set_rental_order_status' ], 10, 4 );
			
			// Try after payment is complete
			add_action( 'woocommerce_payment_complete', [ $this, 'set_rental_status_after_payment' ], 20, 1 );
			
			// Try after thankyou page (final attempt)
			add_action( 'woocommerce_thankyou', [ $this, 'final_rental_status_check' ], 20, 1 );

			// Order item display meta key
			add_filter( 'woocommerce_order_item_display_meta_key', [ $this, 'order_item_display_meta_key' ], 11, 3 );

			// Order item display meta value
			add_filter( 'woocommerce_order_item_display_meta_value', [ $this, 'order_item_display_meta_value' ], 11, 3 );

			// Hide item meta fields
			add_filter( 'woocommerce_order_item_get_formatted_meta_data', [ $this, 'hide_item_meta_fields' ], 11, 2 );

			// After checkout validation
			add_action( 'woocommerce_after_checkout_validation', [ $this, 'after_checkout_validation' ], 11, 2 );

			// Update booking status when order status changes
			add_action( 'woocommerce_order_status_completed', [ $this, 'update_booking_status' ] );
			add_action( 'woocommerce_order_status_processing', [ $this, 'update_booking_status' ] );
			add_action( 'woocommerce_order_status_cancelled', [ $this, 'cancel_booking' ] );
			add_action( 'woocommerce_order_status_refunded', [ $this, 'cancel_booking' ] );
			
			// Handle order deletion
			add_action( 'before_delete_post', [ $this, 'handle_order_deletion' ] );
			add_action( 'wp_trash_post', [ $this, 'handle_order_deletion' ] );
		}

		/**
		 * Add to cart validation
		 */
		public function add_to_cart_validation( $passed, $product_id, $quantity ) {
			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				return $passed;
			}

			// Check if rental dates are provided
			$pickup_date = isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
			$dropoff_date = isset( $_POST['dropoff_date'] ) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';

			if ( empty( $pickup_date ) || empty( $dropoff_date ) ) {
				wc_add_notice( __( 'Please select pickup and drop-off dates for rental products.', 'smart-rentals-wc' ), 'error' );
				return false;
			}

			// Enhanced datetime validation with multiple format support
			$pickup_timestamp = $this->parse_datetime_string( $pickup_date );
			$dropoff_timestamp = $this->parse_datetime_string( $dropoff_date );

			if ( !$pickup_timestamp ) {
				smart_rentals_wc_log( 'Invalid pickup date format: ' . $pickup_date );
				wc_add_notice( __( 'Invalid pickup date format. Please select a valid date.', 'smart-rentals-wc' ), 'error' );
				return false;
			}

			if ( !$dropoff_timestamp ) {
				smart_rentals_wc_log( 'Invalid dropoff date format: ' . $dropoff_date );
				wc_add_notice( __( 'Invalid drop-off date format. Please select a valid date.', 'smart-rentals-wc' ), 'error' );
				return false;
			}

			if ( $pickup_timestamp >= $dropoff_timestamp ) {
				smart_rentals_wc_log( 'Invalid date range - Pickup: ' . $pickup_date . ' (' . $pickup_timestamp . '), Dropoff: ' . $dropoff_date . ' (' . $dropoff_timestamp . ')' );
				wc_add_notice( __( 'Drop-off date must be after pickup date.', 'smart-rentals-wc' ), 'error' );
				return false;
			}

			smart_rentals_wc_log( 'Valid date range for cart - Pickup: ' . $pickup_date . ', Dropoff: ' . $dropoff_date );

			// Check disabled weekdays (only for usage period, not return date)
			$disabled_weekdays = smart_rentals_wc_get_post_meta( $product_id, 'disabled_weekdays' );
			if ( is_array( $disabled_weekdays ) && !empty( $disabled_weekdays ) ) {
				$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
				
				// For daily rentals (hotel logic), exclude the return date from validation
				$validation_end_timestamp = $dropoff_timestamp;
				if ( $rental_type === 'day' ) {
					// For daily rentals, only check usage period (exclude return date)
					$validation_end_timestamp = $dropoff_timestamp - 86400; // Exclude return day
					smart_rentals_wc_log( "Daily rental: Excluding return date from disabled weekday validation" );
				}
				
				$pickup_weekday = date( 'w', $pickup_timestamp );
				
				// Always check pickup date
				if ( in_array( $pickup_weekday, $disabled_weekdays ) ) {
					$weekday_name = $this->get_weekday_name( $pickup_weekday );
					wc_add_notice( sprintf( __( 'Pickup date falls on %s which is disabled for bookings.', 'smart-rentals-wc' ), $weekday_name ), 'error' );
					return false;
				}
				
				// For hourly/mixed rentals, also check dropoff date
				if ( $rental_type !== 'day' ) {
					$dropoff_weekday = date( 'w', $dropoff_timestamp );
					if ( in_array( $dropoff_weekday, $disabled_weekdays ) ) {
						$weekday_name = $this->get_weekday_name( $dropoff_weekday );
						wc_add_notice( sprintf( __( 'Drop-off date falls on %s which is disabled for bookings.', 'smart-rentals-wc' ), $weekday_name ), 'error' );
						return false;
					}
				}
				
				// Check usage period (excluding return date for daily rentals)
				if ( $pickup_timestamp !== $validation_end_timestamp ) {
					$current_date = $pickup_timestamp;
					while ( $current_date <= $validation_end_timestamp ) {
						$current_weekday = date( 'w', $current_date );
						if ( in_array( $current_weekday, $disabled_weekdays ) ) {
							$weekday_name = $this->get_weekday_name( $current_weekday );
							$date_formatted = date( 'Y-m-d', $current_date );
							wc_add_notice( sprintf( __( 'Your rental usage period includes %s (%s) which is disabled for bookings.', 'smart-rentals-wc' ), $weekday_name, $date_formatted ), 'error' );
							return false;
						}
						$current_date += 86400; // Add one day
					}
				}
			}

			// Check disabled dates (only for usage period, not return date)
			$disabled_start_dates = smart_rentals_wc_get_post_meta( $product_id, 'disabled_start_dates' );
			$disabled_end_dates = smart_rentals_wc_get_post_meta( $product_id, 'disabled_end_dates' );
			
			if ( is_array( $disabled_start_dates ) && is_array( $disabled_end_dates ) && !empty( $disabled_start_dates ) ) {
				$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
				
				// For daily rentals (hotel logic), exclude the return date from validation
				$validation_end_timestamp = $dropoff_timestamp;
				if ( $rental_type === 'day' ) {
					// For daily rentals, only check usage period (exclude return date)
					$validation_end_timestamp = $dropoff_timestamp - 86400; // Exclude return day
					smart_rentals_wc_log( "Daily rental: Excluding return date from disabled dates validation" );
				}
				
				foreach ( $disabled_start_dates as $index => $disabled_start ) {
					$disabled_end = isset( $disabled_end_dates[$index] ) ? $disabled_end_dates[$index] : $disabled_start;
					
					if ( !empty( $disabled_start ) ) {
						$disabled_start_timestamp = strtotime( $disabled_start );
						$disabled_end_timestamp = strtotime( $disabled_end );
						
						// Check if the USAGE period overlaps with any disabled date range (not return date)
						if ( $pickup_timestamp <= $disabled_end_timestamp && $validation_end_timestamp >= $disabled_start_timestamp ) {
							$start_formatted = date( 'Y-m-d', $disabled_start_timestamp );
							$end_formatted = date( 'Y-m-d', $disabled_end_timestamp );
							
							if ( $disabled_start === $disabled_end ) {
								wc_add_notice( sprintf( __( 'The date %s is disabled for bookings.', 'smart-rentals-wc' ), $start_formatted ), 'error' );
							} else {
								wc_add_notice( sprintf( __( 'Your rental usage period overlaps with disabled dates (%s to %s).', 'smart-rentals-wc' ), $start_formatted, $end_formatted ), 'error' );
							}
							return false;
						}
					}
				}
			}

			// Check minimum rental period
			$min_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'min_rental_period' );
			if ( $min_rental_period ) {
				$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
				$duration_seconds = $dropoff_timestamp - $pickup_timestamp;
				
				if ( $rental_type === 'hour' ) {
					$duration_hours = $duration_seconds / 3600;
					if ( $duration_hours < $min_rental_period ) {
						wc_add_notice( sprintf( __( 'Minimum rental period is %d hours.', 'smart-rentals-wc' ), $min_rental_period ), 'error' );
						return false;
					}
				} else {
					$duration_days = $duration_seconds / 86400;
					if ( $duration_days < $min_rental_period ) {
						wc_add_notice( sprintf( __( 'Minimum rental period is %d days.', 'smart-rentals-wc' ), $min_rental_period ), 'error' );
						return false;
					}
				}
			}

			// Check maximum rental period
			$max_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'max_rental_period' );
			if ( $max_rental_period ) {
				$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
				$duration_seconds = $dropoff_timestamp - $pickup_timestamp;
				
				if ( $rental_type === 'hour' ) {
					$duration_hours = $duration_seconds / 3600;
					if ( $duration_hours > $max_rental_period ) {
						wc_add_notice( sprintf( __( 'Maximum rental period is %d hours.', 'smart-rentals-wc' ), $max_rental_period ), 'error' );
						return false;
					}
				} else {
					$duration_days = $duration_seconds / 86400;
					if ( $duration_days > $max_rental_period ) {
						wc_add_notice( sprintf( __( 'Maximum rental period is %d days.', 'smart-rentals-wc' ), $max_rental_period ), 'error' );
						return false;
					}
				}
			}

			// Check availability
			if ( !Smart_Rentals_WC()->options->check_availability( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity ) ) {
				$product = wc_get_product( $product_id );
				$product_name = $product ? $product->get_name() : __( 'Product', 'smart-rentals-wc' );
				wc_add_notice( sprintf( __( '%s is not available for the selected dates and quantity.', 'smart-rentals-wc' ), $product_name ), 'error' );
				return false;
			}

			return $passed;
		}

		/**
		 * Add cart item data
		 */
		public function add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				return $cart_item_data;
			}

			// Check if this is from our AJAX add to cart
			if ( isset( $_POST['action'] ) && 'smart_rentals_add_to_cart' === $_POST['action'] ) {
				// Data comes from AJAX call, already processed
				$cart_item_from_ajax = smart_rentals_wc_get_meta_data( 'cart_item', $_POST );
				if ( smart_rentals_wc_array_exists( $cart_item_from_ajax ) ) {
					// Merge the AJAX cart item data
					$cart_item_data = array_merge( $cart_item_data, $cart_item_from_ajax );
					smart_rentals_wc_log( 'AJAX cart item data: ' . print_r( $cart_item_data, true ) );
				}
				return $cart_item_data;
			}

			// Handle standard form submission (fallback)
			$pickup_date = isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
			$dropoff_date = isset( $_POST['dropoff_date'] ) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
			$pickup_time = isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';
			$dropoff_time = isset( $_POST['dropoff_time'] ) ? sanitize_text_field( $_POST['dropoff_time'] ) : '';

			if ( $pickup_date && $dropoff_date ) {
				// Calculate duration text for display in cart
				$duration_text = Smart_Rentals_WC()->options->get_rental_duration_text( 
					$pickup_date, 
					$dropoff_date 
				);
				
				// Get security deposit (with global fallback)
				$security_deposit = smart_rentals_wc_get_security_deposit( $product_id );
				
				$cart_item_data['rental_data'] = [
					'pickup_date' => $pickup_date,
					'dropoff_date' => $dropoff_date,
					'pickup_time' => $pickup_time,
					'dropoff_time' => $dropoff_time,
					'product_id' => $product_id,
					'rental_quantity' => $quantity,
					'pickup_location' => isset( $_POST['pickup_location'] ) ? sanitize_text_field( $_POST['pickup_location'] ) : '',
					'dropoff_location' => isset( $_POST['dropoff_location'] ) ? sanitize_text_field( $_POST['dropoff_location'] ) : '',
					'duration_text' => $duration_text,
					'security_deposit' => $security_deposit
				];

				// Make each rental booking unique
				$cart_item_data['unique_key'] = md5( microtime() . rand() . $product_id );
				
				smart_rentals_wc_log( 'Standard cart item data: ' . print_r( $cart_item_data['rental_data'], true ) );
			} else {
				// If no rental dates provided, prevent adding to cart
				wc_add_notice( __( 'Please select pickup and drop-off dates for rental products.', 'smart-rentals-wc' ), 'error' );
				return false;
			}

			return $cart_item_data;
		}

		/**
		 * Get item data
		 */
		public function get_item_data( $item_data, $cart_item ) {
			if ( !isset( $cart_item['rental_data'] ) ) {
				return $item_data;
			}

			$rental_data = $cart_item['rental_data'];

			// Pickup date
			if ( !empty( $rental_data['pickup_date'] ) ) {
				$pickup_display = $rental_data['pickup_date'];
				if ( !empty( $rental_data['pickup_time'] ) ) {
					$pickup_display .= ' ' . $rental_data['pickup_time'];
				}
				
				$item_data[] = [
					'key' => __( 'Pickup Date', 'smart-rentals-wc' ),
					'value' => $pickup_display,
					'display' => '',
				];
			}

			// Drop-off date
			if ( !empty( $rental_data['dropoff_date'] ) ) {
				$dropoff_display = $rental_data['dropoff_date'];
				if ( !empty( $rental_data['dropoff_time'] ) ) {
					$dropoff_display .= ' ' . $rental_data['dropoff_time'];
				}
				
				$item_data[] = [
					'key' => __( 'Drop-off Date', 'smart-rentals-wc' ),
					'value' => $dropoff_display,
					'display' => '',
				];
			}

			// Duration
			if ( !empty( $rental_data['pickup_date'] ) && !empty( $rental_data['dropoff_date'] ) ) {
				$duration_text = Smart_Rentals_WC()->options->get_rental_duration_text( 
					$rental_data['pickup_date'], 
					$rental_data['dropoff_date'] 
				);
				
				if ( $duration_text ) {
					$item_data[] = [
						'key' => __( 'Duration', 'smart-rentals-wc' ),
						'value' => $duration_text,
						'display' => '',
					];
				}
			}

			// Security deposit
			if ( isset( $rental_data['security_deposit'] ) && $rental_data['security_deposit'] > 0 ) {
				$item_data[] = [
					'key' => __( 'Security Deposit', 'smart-rentals-wc' ),
					'value' => smart_rentals_wc_price( $rental_data['security_deposit'] ),
					'display' => '',
				];
			}

			return $item_data;
		}

		/**
		 * Cart item price
		 */
		public function cart_item_price( $product_price, $cart_item, $cart_item_key ) {
			if ( !isset( $cart_item['rental_data'] ) ) {
				return $product_price;
			}

			$rental_data = $cart_item['rental_data'];
			$product_id = $rental_data['product_id'];

			$pickup_timestamp = strtotime( $rental_data['pickup_date'] );
			$dropoff_timestamp = strtotime( $rental_data['dropoff_date'] );

			$total_price = Smart_Rentals_WC()->options->calculate_rental_price(
				$product_id,
				$pickup_timestamp,
				$dropoff_timestamp,
				$rental_data['rental_quantity']
			);

			if ( $total_price > 0 ) {
				$unit_price = $total_price / $rental_data['rental_quantity'];
				return smart_rentals_wc_price( $unit_price );
			}

			return $product_price;
		}

		/**
		 * Before calculate totals
		 */
		public function before_calculate_totals( $cart ) {
			if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
				return;
			}

			foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( !isset( $cart_item['rental_data'] ) ) {
					continue;
				}

				$rental_data = $cart_item['rental_data'];
				$product_id = $rental_data['product_id'];

				$pickup_timestamp = strtotime( $rental_data['pickup_date'] );
				$dropoff_timestamp = strtotime( $rental_data['dropoff_date'] );

				$total_price = Smart_Rentals_WC()->options->calculate_rental_price(
					$product_id,
					$pickup_timestamp,
					$dropoff_timestamp,
					$rental_data['rental_quantity']
				);

				// Add security deposit to the total price (with global fallback)
				$security_deposit = smart_rentals_wc_get_security_deposit( $product_id );
				
				$total_with_deposit = $total_price + $security_deposit;

				if ( $total_with_deposit > 0 ) {
					$unit_price = $total_with_deposit / $rental_data['rental_quantity'];
					$cart_item['data']->set_price( $unit_price );
					
					// Store security deposit info for display
					if ( !isset( $cart_item['rental_data']['security_deposit'] ) ) {
						$cart->cart_contents[$cart_item_key]['rental_data']['security_deposit'] = $security_deposit;
					}
				}
			}
		}

		/**
		 * Checkout create order line item
		 */
		public function checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
			if ( !isset( $values['rental_data'] ) ) {
				return;
			}

			$rental_data = $values['rental_data'];

			// Add rental meta data to order item
			$item->add_meta_data( smart_rentals_wc_meta_key( 'pickup_date' ), $rental_data['pickup_date'], true );
			$item->add_meta_data( smart_rentals_wc_meta_key( 'dropoff_date' ), $rental_data['dropoff_date'], true );
			
			if ( !empty( $rental_data['pickup_time'] ) ) {
				$item->add_meta_data( smart_rentals_wc_meta_key( 'pickup_time' ), $rental_data['pickup_time'], true );
			}
			
			if ( !empty( $rental_data['dropoff_time'] ) ) {
				$item->add_meta_data( smart_rentals_wc_meta_key( 'dropoff_time' ), $rental_data['dropoff_time'], true );
			}

			$item->add_meta_data( smart_rentals_wc_meta_key( 'rental_quantity' ), $rental_data['rental_quantity'], true );
			
			// Add security deposit if present
			if ( isset( $rental_data['security_deposit'] ) && $rental_data['security_deposit'] > 0 ) {
				$item->add_meta_data( smart_rentals_wc_meta_key( 'security_deposit' ), smart_rentals_wc_price( $rental_data['security_deposit'] ), true );
			}
			
			// Mark this item as a rental (crucial for admin calendar)
			$item->add_meta_data( smart_rentals_wc_meta_key( 'is_rental' ), 'yes', true );

			// Calculate and save total price
			$pickup_timestamp = strtotime( $rental_data['pickup_date'] );
			$dropoff_timestamp = strtotime( $rental_data['dropoff_date'] );
			
			$total_price = Smart_Rentals_WC()->options->calculate_rental_price(
				$rental_data['product_id'],
				$pickup_timestamp,
				$dropoff_timestamp,
				$rental_data['rental_quantity']
			);

			if ( $total_price > 0 ) {
				$item->add_meta_data( smart_rentals_wc_meta_key( 'rental_total' ), $total_price, true );
			}

			// Add duration text
			$duration_text = Smart_Rentals_WC()->options->get_rental_duration_text( 
				$pickup_timestamp, 
				$dropoff_timestamp 
			);
			
			if ( $duration_text ) {
				$item->add_meta_data( smart_rentals_wc_meta_key( 'duration' ), $duration_text, true );
			}

			// Save booking to database
			$this->save_booking_to_database( $order, $item, $rental_data );
		}

		/**
		 * Save booking to database
		 */
		private function save_booking_to_database( $order, $item, $rental_data ) {
			global $wpdb;

			$table_name = $wpdb->prefix . 'smart_rentals_bookings';

			// Check if table exists
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return;
			}

			$pickup_timestamp = strtotime( $rental_data['pickup_date'] );
			$dropoff_timestamp = strtotime( $rental_data['dropoff_date'] );

			$total_price = Smart_Rentals_WC()->options->calculate_rental_price(
				$rental_data['product_id'],
				$pickup_timestamp,
				$dropoff_timestamp,
				$rental_data['rental_quantity']
			);

			$security_deposit = smart_rentals_wc_get_security_deposit( $rental_data['product_id'] );

			$wpdb->insert(
				$table_name,
				[
					'order_id' => $order->get_id(),
					'product_id' => $rental_data['product_id'],
					'pickup_date' => gmdate( 'Y-m-d H:i:s', $pickup_timestamp ),
					'dropoff_date' => gmdate( 'Y-m-d H:i:s', $dropoff_timestamp ),
					'pickup_location' => $rental_data['pickup_location'] ?? '',
					'dropoff_location' => $rental_data['dropoff_location'] ?? '',
					'quantity' => $rental_data['rental_quantity'],
					'status' => 'pending',
					'total_price' => $total_price,
					'security_deposit' => $security_deposit,
				],
				[
					'%d', // order_id
					'%d', // product_id
					'%s', // pickup_date
					'%s', // dropoff_date
					'%s', // pickup_location
					'%s', // dropoff_location
					'%d', // quantity
					'%s', // status
					'%f', // total_price
					'%f', // security_deposit
				]
			);
		}

		/**
		 * Order item display meta key
		 */
		public function order_item_display_meta_key( $display_key, $meta, $item ) {
			$rental_meta_keys = [
				smart_rentals_wc_meta_key( 'pickup_date' ) => __( 'Pickup Date', 'smart-rentals-wc' ),
				smart_rentals_wc_meta_key( 'dropoff_date' ) => __( 'Drop-off Date', 'smart-rentals-wc' ),
				smart_rentals_wc_meta_key( 'pickup_time' ) => __( 'Pickup Time', 'smart-rentals-wc' ),
				smart_rentals_wc_meta_key( 'dropoff_time' ) => __( 'Drop-off Time', 'smart-rentals-wc' ),
				smart_rentals_wc_meta_key( 'duration' ) => __( 'Duration', 'smart-rentals-wc' ),
				smart_rentals_wc_meta_key( 'rental_total' ) => __( 'Rental Total', 'smart-rentals-wc' ),
			];

			if ( isset( $rental_meta_keys[$meta->key] ) ) {
				return $rental_meta_keys[$meta->key];
			}

			return $display_key;
		}

		/**
		 * Order item display meta value
		 */
		public function order_item_display_meta_value( $meta_value, $meta, $item ) {
		// Format rental total as price
		if ( smart_rentals_wc_meta_key( 'rental_total' ) === $meta->key ) {
			return smart_rentals_wc_price( $meta_value );
		}

			return $meta_value;
		}

		/**
		 * Hide item meta fields
		 */
		public function hide_item_meta_fields( $meta_data, $item ) {
			$hide_fields = [
				smart_rentals_wc_meta_key( 'is_rental' ),
				smart_rentals_wc_meta_key( 'rental_quantity' ),
			];

			$new_meta = [];

			if ( smart_rentals_wc_array_exists( $meta_data ) ) {
				foreach ( $meta_data as $id => $meta ) {
					if ( in_array( $meta->key, $hide_fields ) ) {
						continue;
					}

					$new_meta[$id] = $meta;
				}
			}

			return $new_meta;
		}

		/**
		 * After checkout validation
		 */
		public function after_checkout_validation( $data, $errors ) {
			if ( !method_exists( WC()->cart, 'get_cart' ) || !smart_rentals_wc_array_exists( WC()->cart->get_cart() ) ) {
				return;
			}

			foreach ( WC()->cart->get_cart() as $cart_item ) {
				if ( !isset( $cart_item['rental_data'] ) ) {
					continue;
				}

				$rental_data = $cart_item['rental_data'];
				$product_id = $rental_data['product_id'];

				// Final availability check with enhanced datetime parsing
				$pickup_timestamp = $this->parse_datetime_string( $rental_data['pickup_date'] );
				$dropoff_timestamp = $this->parse_datetime_string( $rental_data['dropoff_date'] );
				
				if ( !Smart_Rentals_WC()->options->check_availability( 
					$product_id, 
					$pickup_timestamp, 
					$dropoff_timestamp, 
					$rental_data['rental_quantity'] 
				) ) {
					$product = wc_get_product( $product_id );
					$product_name = $product ? $product->get_name() : __( 'Product', 'smart-rentals-wc' );
					$errors->add( 'validation', sprintf( 
						__( '%s is no longer available for the selected dates. Please choose different dates.', 'smart-rentals-wc' ), 
						$product_name 
					) );
				}
			}
		}

		/**
		 * Get upcoming rentals
		 */
		public function get_upcoming_rentals( $limit = 10 ) {
			global $wpdb;

			$current_time = smart_rentals_wc_current_time();
			$order_status = Smart_Rentals_WC()->options->get_booking_order_status();
			$status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );

			$results = $wpdb->get_results( $wpdb->prepare("
				SELECT 
					items.order_item_name as product_name,
					pickup_date.meta_value as pickup_date,
					dropoff_date.meta_value as dropoff_date,
					orders.ID as order_id,
					orders.post_date as order_date
				FROM {$wpdb->prefix}woocommerce_order_items AS items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pickup_date 
					ON items.order_item_id = pickup_date.order_item_id 
					AND pickup_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS dropoff_date 
					ON items.order_item_id = dropoff_date.order_item_id 
					AND dropoff_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS is_rental 
					ON items.order_item_id = is_rental.order_item_id 
					AND is_rental.meta_key = %s
				LEFT JOIN {$wpdb->posts} AS orders 
					ON items.order_id = orders.ID
				WHERE 
					is_rental.meta_value = 'yes'
					AND orders.post_status IN ('{$status_placeholders}')
					AND UNIX_TIMESTAMP(pickup_date.meta_value) > %d
				ORDER BY pickup_date.meta_value ASC
				LIMIT %d
			",
				smart_rentals_wc_meta_key( 'pickup_date' ),
				smart_rentals_wc_meta_key( 'dropoff_date' ),
				smart_rentals_wc_meta_key( 'is_rental' ),
				$current_time,
				$limit
			));

			return apply_filters( 'smart_rentals_wc_get_upcoming_rentals', $results, $limit );
		}

		/**
		 * Get active rentals
		 */
		public function get_active_rentals( $limit = 10 ) {
			global $wpdb;

			$current_time = smart_rentals_wc_current_time();
			$order_status = Smart_Rentals_WC()->options->get_booking_order_status();
			$status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );

			$results = $wpdb->get_results( $wpdb->prepare("
				SELECT 
					items.order_item_name as product_name,
					pickup_date.meta_value as pickup_date,
					dropoff_date.meta_value as dropoff_date,
					orders.ID as order_id,
					orders.post_date as order_date
				FROM {$wpdb->prefix}woocommerce_order_items AS items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pickup_date 
					ON items.order_item_id = pickup_date.order_item_id 
					AND pickup_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS dropoff_date 
					ON items.order_item_id = dropoff_date.order_item_id 
					AND dropoff_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS is_rental 
					ON items.order_item_id = is_rental.order_item_id 
					AND is_rental.meta_key = %s
				LEFT JOIN {$wpdb->posts} AS orders 
					ON items.order_id = orders.ID
				WHERE 
					is_rental.meta_value = 'yes'
					AND orders.post_status IN ('{$status_placeholders}')
					AND UNIX_TIMESTAMP(pickup_date.meta_value) <= %d
					AND UNIX_TIMESTAMP(dropoff_date.meta_value) >= %d
				ORDER BY pickup_date.meta_value ASC
				LIMIT %d
			",
				smart_rentals_wc_meta_key( 'pickup_date' ),
				smart_rentals_wc_meta_key( 'dropoff_date' ),
				smart_rentals_wc_meta_key( 'is_rental' ),
				$current_time,
				$current_time,
				$limit
			));

			return apply_filters( 'smart_rentals_wc_get_active_rentals', $results, $limit );
		}

		/**
		 * Update booking status
		 */
		public function update_booking_status( $order_id ) {
			global $wpdb;

			$table_name = $wpdb->prefix . 'smart_rentals_bookings';

			// Check if table exists
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return;
			}

			$order = wc_get_order( $order_id );
			if ( !$order ) {
				return;
			}

			$status = 'confirmed';
			if ( 'completed' === $order->get_status() ) {
				$status = 'active';
			}

			$wpdb->update(
				$table_name,
				[ 'status' => $status ],
				[ 'order_id' => $order_id ],
				[ '%s' ],
				[ '%d' ]
			);
		}

		/**
		 * Cancel booking
		 */
		public function cancel_booking( $order_id ) {
			global $wpdb;

			$table_name = $wpdb->prefix . 'smart_rentals_bookings';

			// Check if table exists
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return;
			}

			$wpdb->update(
				$table_name,
				[ 'status' => 'cancelled' ],
				[ 'order_id' => $order_id ],
				[ '%s' ],
				[ '%d' ]
			);
			
			smart_rentals_wc_log( "Booking cancelled for order: $order_id" );
		}

		/**
		 * Handle order deletion (trash or permanent delete)
		 */
		public function handle_order_deletion( $post_id ) {
			// Check if this is a WooCommerce order
			if ( get_post_type( $post_id ) !== 'shop_order' ) {
				return;
			}

			// Cancel the booking when order is deleted/trashed
			$this->cancel_booking( $post_id );
			
			smart_rentals_wc_log( "Order deleted/trashed, booking cancelled: $post_id" );
		}

		/**
		 * Get weekday name from number
		 */
		private function get_weekday_name( $weekday_number ) {
			$weekdays = [
				0 => __( 'Sunday', 'smart-rentals-wc' ),
				1 => __( 'Monday', 'smart-rentals-wc' ),
				2 => __( 'Tuesday', 'smart-rentals-wc' ),
				3 => __( 'Wednesday', 'smart-rentals-wc' ),
				4 => __( 'Thursday', 'smart-rentals-wc' ),
				5 => __( 'Friday', 'smart-rentals-wc' ),
				6 => __( 'Saturday', 'smart-rentals-wc' ),
			];
			
			return isset( $weekdays[ $weekday_number ] ) ? $weekdays[ $weekday_number ] : __( 'Unknown', 'smart-rentals-wc' );
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
					smart_rentals_wc_log( 'Successfully parsed datetime in booking: ' . $datetime_string . ' with format: ' . $format );
					return $timestamp->getTimestamp();
				}
			}

			// Fallback to strtotime
			$timestamp = strtotime( $datetime_string );
			if ( $timestamp ) {
				smart_rentals_wc_log( 'Parsed datetime in booking with strtotime: ' . $datetime_string . ' -> ' . $timestamp );
				return $timestamp;
			}

			smart_rentals_wc_log( 'Failed to parse datetime in booking: ' . $datetime_string );
			return false;
		}

		/**
		 * Set rental order status after order creation
		 */
		public function set_rental_order_status( $order ) {
			// Debug log
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				smart_rentals_wc_log( 'set_rental_order_status called for order #' . $order->get_id() . ' with current status: ' . $order->get_status() );
			}
			
			$this->apply_rental_order_status( $order, 'order_created' );
		}

		/**
		 * Maybe set rental order status when order status changes (only for new frontend bookings)
		 */
		public function maybe_set_rental_order_status( $order_id, $from_status, $to_status, $order ) {
			// Debug log
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				smart_rentals_wc_log( sprintf( 
					'maybe_set_rental_order_status called for order #%d - from: %s, to: %s', 
					$order_id, 
					$from_status, 
					$to_status 
				) );
			}
			
			// Only apply to NEW orders from frontend, not manual admin changes
			// Check if this is a manual admin change
			if ( is_admin() && !wp_doing_ajax() ) {
				// This is likely a manual admin change, don't interfere
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					smart_rentals_wc_log( 'Skipping order status change - manual admin change detected for order #' . $order_id );
				}
				return;
			}
			
			// Check if we've already processed this order
			$already_processed = $order->get_meta( '_smart_rentals_status_processed' );
			if ( $already_processed === 'yes' ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					smart_rentals_wc_log( 'Skipping order status change - already processed for order #' . $order_id );
				}
				return;
			}
			
			// Only act on specific status transitions that indicate new orders
			$valid_transitions = [
				'pending' => ['processing', 'completed', 'on-hold'],
				'auto-draft' => ['pending', 'processing', 'completed', 'on-hold'],
			];
			
			$is_valid_transition = false;
			if ( isset( $valid_transitions[$from_status] ) && in_array( $to_status, $valid_transitions[$from_status] ) ) {
				$is_valid_transition = true;
			}
			
			if ( !$is_valid_transition ) {
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					smart_rentals_wc_log( 'Skipping order status change - invalid transition from ' . $from_status . ' to ' . $to_status . ' for order #' . $order_id );
				}
				return;
			}
			
			$this->apply_rental_order_status( $order, 'status_changed' );
		}

		/**
		 * Set rental status after payment is complete
		 */
		public function set_rental_status_after_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( !$order ) {
				return;
			}
			
			// Debug log
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				smart_rentals_wc_log( 'set_rental_status_after_payment called for order #' . $order_id . ' with current status: ' . $order->get_status() );
			}
			
			$this->apply_rental_order_status( $order, 'payment_complete' );
		}

		/**
		 * Final rental status check on thankyou page
		 */
		public function final_rental_status_check( $order_id ) {
			if ( !$order_id ) {
				return;
			}
			
			$order = wc_get_order( $order_id );
			if ( !$order ) {
				return;
			}
			
			// Debug log
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				smart_rentals_wc_log( 'final_rental_status_check called for order #' . $order_id . ' with current status: ' . $order->get_status() );
			}
			
			$this->apply_rental_order_status( $order, 'thankyou_page' );
		}

		/**
		 * Apply rental order status (consolidated logic)
		 */
		private function apply_rental_order_status( $order, $context = 'general' ) {
			// Check if order has rental items
			$has_rental_items = false;
			foreach ( $order->get_items() as $item ) {
				$is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
				if ( $is_rental === 'yes' ) {
					$has_rental_items = true;
					break;
				}
			}
			
			// Debug log
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				smart_rentals_wc_log( sprintf( 
					'apply_rental_order_status (context: %s) - Order #%d has rental items: %s, current status: %s', 
					$context,
					$order->get_id(), 
					$has_rental_items ? 'yes' : 'no',
					$order->get_status()
				) );
			}
			
			// If no rental items, return
			if ( !$has_rental_items ) {
				return;
			}
			
			// Get rental order status setting
			$settings = smart_rentals_wc_get_option( 'settings', [] );
			$rental_order_status = smart_rentals_wc_get_meta_data( 'rental_order_status', $settings, '' );
			
			// Debug log
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				smart_rentals_wc_log( sprintf( 
					'Rental order status setting: "%s" for order #%d (context: %s)', 
					$rental_order_status,
					$order->get_id(),
					$context
				) );
			}
			
			// If rental order status is set and different from current status
			if ( !empty( $rental_order_status ) ) {
				$desired_status = str_replace( 'wc-', '', $rental_order_status );
				$current_status = $order->get_status();
				
				if ( $current_status !== $desired_status ) {
					// Check if we've already tried to set this status multiple times
					$attempt_count = intval( $order->get_meta( '_smart_rentals_status_attempts' ) );
					
					if ( $attempt_count < 3 ) { // Limit attempts to avoid infinite loops
						// Increment attempt counter
						$order->update_meta_data( '_smart_rentals_status_attempts', $attempt_count + 1 );
						$order->update_meta_data( '_smart_rentals_desired_status', $desired_status );
						
						// Mark as processed to prevent future interference
						$order->update_meta_data( '_smart_rentals_status_processed', 'yes' );
						$order->save();
						
						// Update order status
						$order->update_status( 
							$desired_status, 
							sprintf( 
								__( 'Order status set to %s by Smart Rentals plugin (context: %s, attempt: %d).', 'smart-rentals-wc' ), 
								$rental_order_status,
								$context,
								$attempt_count + 1
							) 
						);
						
						// Log the status change
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							smart_rentals_wc_log( sprintf( 
								'Order #%d status changed from %s to %s (context: %s, attempt: %d) - marked as processed', 
								$order->get_id(),
								$current_status,
								$desired_status,
								$context,
								$attempt_count + 1
							) );
						}
					} else {
						// Log that we've exceeded attempts
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							smart_rentals_wc_log( sprintf( 
								'Order #%d exceeded status change attempts. Current: %s, Desired: %s', 
								$order->get_id(),
								$current_status,
								$desired_status
							) );
						}
					}
				} else {
					// Status is already correct
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						smart_rentals_wc_log( sprintf( 
							'Order #%d already has correct status: %s (context: %s)', 
							$order->get_id(),
							$current_status,
							$context
						) );
					}
				}
			}
		}

		/**
		 * Main Smart_Rentals_WC_Booking Instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
}