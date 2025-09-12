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

			// Get security deposit
			$security_deposit = smart_rentals_wc_get_post_meta( $product_id, 'security_deposit' );

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
	}
}