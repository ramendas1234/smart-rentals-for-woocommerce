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
			// Calculate price AJAX
			add_action( 'wp_ajax_smart_rentals_calculate_price', [ $this, 'calculate_price' ] );
			add_action( 'wp_ajax_nopriv_smart_rentals_calculate_price', [ $this, 'calculate_price' ] );

			// Check availability AJAX
			add_action( 'wp_ajax_smart_rentals_check_availability', [ $this, 'check_availability' ] );
			add_action( 'wp_ajax_nopriv_smart_rentals_check_availability', [ $this, 'check_availability' ] );

			// Get calendar data AJAX
			add_action( 'wp_ajax_smart_rentals_get_calendar_data', [ $this, 'get_calendar_data' ] );
			add_action( 'wp_ajax_nopriv_smart_rentals_get_calendar_data', [ $this, 'get_calendar_data' ] );

			// Update rental cart item AJAX
			add_action( 'wp_ajax_update_rental_cart_item', [ $this, 'update_rental_cart_item' ] );
			add_action( 'wp_ajax_nopriv_update_rental_cart_item', [ $this, 'update_rental_cart_item' ] );

			// Update rental dates AJAX
			add_action( 'wp_ajax_update_rental_dates', [ $this, 'update_rental_dates' ] );
			add_action( 'wp_ajax_nopriv_update_rental_dates', [ $this, 'update_rental_dates' ] );
		}

		/**
		 * Verify nonce
		 */
		private function verify_nonce() {
			if ( !wp_verify_nonce( $_POST['nonce'], 'smart_rentals_wc_nonce' ) ) {
				wp_die( __( 'Security check failed', 'smart-rentals-wc' ), '', [ 'response' => 403 ] );
			}
		}

		/**
		 * Calculate rental price
		 */
		public function calculate_price() {
			$this->verify_nonce();

			$product_id = intval( $_POST['product_id'] );
			$pickup_date = sanitize_text_field( $_POST['pickup_date'] );
			$dropoff_date = sanitize_text_field( $_POST['dropoff_date'] );
			$pickup_time = sanitize_text_field( $_POST['pickup_time'] ?? '' );
			$dropoff_time = sanitize_text_field( $_POST['dropoff_time'] ?? '' );
			$quantity = intval( $_POST['quantity'] ?? 1 );

			if ( !$product_id || !$pickup_date || !$dropoff_date ) {
				wp_send_json_error( [ 'message' => __( 'Missing required parameters', 'smart-rentals-wc' ) ] );
			}

			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				wp_send_json_error( [ 'message' => __( 'Product is not a rental product', 'smart-rentals-wc' ) ] );
			}

			// Combine date and time
			$pickup_datetime = $pickup_date . ( $pickup_time ? ' ' . $pickup_time : '' );
			$dropoff_datetime = $dropoff_date . ( $dropoff_time ? ' ' . $dropoff_time : '' );

			$pickup_timestamp = strtotime( $pickup_datetime );
			$dropoff_timestamp = strtotime( $dropoff_datetime );

			if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
				wp_send_json_error( [ 'message' => __( 'Invalid date range', 'smart-rentals-wc' ) ] );
			}

			$total_price = Smart_Rentals_WC()->options->calculate_rental_price(
				$product_id,
				$pickup_timestamp,
				$dropoff_timestamp,
				$quantity
			);

			$duration_text = Smart_Rentals_WC()->options->get_rental_duration_text(
				$pickup_timestamp,
				$dropoff_timestamp
			);

			if ( $total_price > 0 ) {
				wp_send_json_success([
					'price' => $total_price,
					'formatted_price' => wc_price( $total_price ),
					'duration_text' => $duration_text,
				]);
			} else {
				wp_send_json_error( [ 'message' => __( 'Unable to calculate price', 'smart-rentals-wc' ) ] );
			}
		}

		/**
		 * Check availability
		 */
		public function check_availability() {
			$this->verify_nonce();

			$product_id = intval( $_POST['product_id'] );
			$pickup_date = sanitize_text_field( $_POST['pickup_date'] );
			$dropoff_date = sanitize_text_field( $_POST['dropoff_date'] );
			$quantity = intval( $_POST['quantity'] ?? 1 );

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
		 * Get calendar data
		 */
		public function get_calendar_data() {
			$this->verify_nonce();

			$product_id = intval( $_POST['product_id'] );
			$start_date = sanitize_text_field( $_POST['start_date'] ?? '' );
			$end_date = sanitize_text_field( $_POST['end_date'] ?? '' );

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

			// Add available dates (if needed)
			// This could be expanded to show pricing information

			wp_send_json_success([
				'events' => $events,
				'product_id' => $product_id,
			]);
		}

		/**
		 * Get product rental info (for admin)
		 */
		public function get_product_rental_info() {
			if ( !smart_rentals_wc_current_user_can( 'manage_woocommerce' ) ) {
				wp_die( __( 'Permission denied', 'smart-rentals-wc' ), '', [ 'response' => 403 ] );
			}

			$this->verify_nonce();

			$product_id = intval( $_POST['product_id'] );

			if ( !$product_id ) {
				wp_send_json_error( [ 'message' => __( 'Missing product ID', 'smart-rentals-wc' ) ] );
			}

			$is_rental = smart_rentals_wc_is_rental_product( $product_id );
			
			if ( !$is_rental ) {
				wp_send_json_success([
					'is_rental' => false,
					'message' => __( 'This is not a rental product', 'smart-rentals-wc' ),
				]);
			}

			$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
			$daily_price = smart_rentals_wc_get_post_meta( $product_id, 'daily_price' );
			$hourly_price = smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' );
			$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
			$security_deposit = smart_rentals_wc_get_post_meta( $product_id, 'security_deposit' );

			// Get booking statistics
			$stats = Smart_Rentals_WC()->options->get_rental_statistics();

			wp_send_json_success([
				'is_rental' => true,
				'rental_type' => $rental_type,
				'daily_price' => $daily_price,
				'hourly_price' => $hourly_price,
				'rental_stock' => $rental_stock,
				'security_deposit' => $security_deposit,
				'stats' => $stats,
			]);
		}

		/**
		 * Update rental cart item
		 */
		public function update_rental_cart_item() {
			if ( !wp_verify_nonce( $_POST['security'], 'wc_cart_nonce' ) ) {
				wp_die( __( 'Security check failed', 'smart-rentals-wc' ), '', [ 'response' => 403 ] );
			}

			$cart_item_key = sanitize_text_field( $_POST['cart_item_key'] );
			$quantity = intval( $_POST['quantity'] );

			if ( !$cart_item_key || $quantity < 1 ) {
				wp_send_json_error( [ 'message' => __( 'Invalid parameters', 'smart-rentals-wc' ) ] );
			}

			$cart_item = WC()->cart->get_cart_item( $cart_item_key );
			if ( !$cart_item || !isset( $cart_item['rental_data'] ) ) {
				wp_send_json_error( [ 'message' => __( 'Cart item not found', 'smart-rentals-wc' ) ] );
			}

			// Update quantity
			WC()->cart->set_quantity( $cart_item_key, $quantity );

			wp_send_json_success( [ 'message' => __( 'Cart updated', 'smart-rentals-wc' ) ] );
		}

		/**
		 * Update rental dates
		 */
		public function update_rental_dates() {
			if ( !wp_verify_nonce( $_POST['security'], 'wc_cart_nonce' ) ) {
				wp_die( __( 'Security check failed', 'smart-rentals-wc' ), '', [ 'response' => 403 ] );
			}

			$cart_item_key = sanitize_text_field( $_POST['cart_item_key'] );
			$date_type = sanitize_text_field( $_POST['date_type'] );
			$date_value = sanitize_text_field( $_POST['date_value'] );

			if ( !$cart_item_key || !$date_type || !$date_value ) {
				wp_send_json_error( [ 'message' => __( 'Invalid parameters', 'smart-rentals-wc' ) ] );
			}

			$cart_item = WC()->cart->get_cart_item( $cart_item_key );
			if ( !$cart_item || !isset( $cart_item['rental_data'] ) ) {
				wp_send_json_error( [ 'message' => __( 'Cart item not found', 'smart-rentals-wc' ) ] );
			}

			// Update rental data
			if ( 'pickup' === $date_type ) {
				WC()->cart->cart_contents[$cart_item_key]['rental_data']['pickup_date'] = $date_value;
			} elseif ( 'dropoff' === $date_type ) {
				WC()->cart->cart_contents[$cart_item_key]['rental_data']['dropoff_date'] = $date_value;
			}

			// Validate new dates
			$rental_data = WC()->cart->cart_contents[$cart_item_key]['rental_data'];
			$pickup_timestamp = strtotime( $rental_data['pickup_date'] );
			$dropoff_timestamp = strtotime( $rental_data['dropoff_date'] );

			if ( $pickup_timestamp >= $dropoff_timestamp ) {
				wp_send_json_error( [ 'message' => __( 'Drop-off date must be after pickup date', 'smart-rentals-wc' ) ] );
			}

			// Check availability for new dates
			if ( !Smart_Rentals_WC()->options->check_availability( 
				$rental_data['product_id'], 
				$pickup_timestamp, 
				$dropoff_timestamp, 
				$rental_data['rental_quantity'] 
			) ) {
				wp_send_json_error( [ 'message' => __( 'Product not available for selected dates', 'smart-rentals-wc' ) ] );
			}

			WC()->cart->set_session();

			wp_send_json_success( [ 'message' => __( 'Dates updated', 'smart-rentals-wc' ) ] );
		}
	}

	new Smart_Rentals_WC_Ajax();
}