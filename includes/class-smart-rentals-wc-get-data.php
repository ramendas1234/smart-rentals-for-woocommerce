<?php
/**
 * Smart Rentals WC Get Data class.
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Get_Data' ) ) {

	class Smart_Rentals_WC_Get_Data {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Get date format
		 */
		public function get_date_format() {
			return apply_filters( 'smart_rentals_wc_get_date_format', smart_rentals_wc_get_setting( 'date_format', 'Y-m-d' ) );
		}

		/**
		 * Get date placeholder
		 */
		public function get_date_placeholder() {
			$placeholder = '';
			$date_format = $this->get_date_format();

			switch ( $date_format ) {
				case 'd-m-Y':
					$placeholder = __( 'd-m-Y', 'smart-rentals-wc' );
					break;
				case 'm/d/Y':
					$placeholder = __( 'm/d/Y', 'smart-rentals-wc' );
					break;
				case 'Y/m/d':
					$placeholder = __( 'Y/m/d', 'smart-rentals-wc' );
					break;
				case 'Y-m-d':
					$placeholder = __( 'Y-m-d', 'smart-rentals-wc' );
					break;
				default:
					$placeholder = __( 'Y-m-d', 'smart-rentals-wc' );
					break;
			}

			return apply_filters( 'smart_rentals_wc_get_date_placeholder', $placeholder );
		}

		/**
		 * Get time format
		 */
		public function get_time_format() {
			return apply_filters( 'smart_rentals_wc_get_time_format', smart_rentals_wc_get_setting( 'time_format', 'H:i' ) );
		}

		/**
		 * Get time placeholder
		 */
		public function get_time_placeholder() {
			$placeholder = '';
			$time_format = $this->get_time_format();

			switch ( $time_format ) {
				case 'H:i':
					$placeholder = __( 'H:i', 'smart-rentals-wc' );
					break;
				case 'h:i a':
					$placeholder = __( 'h:i a', 'smart-rentals-wc' );
					break;
				case 'g:i a':
					$placeholder = __( 'g:i a', 'smart-rentals-wc' );
					break;
				default:
					$placeholder = __( 'H:i', 'smart-rentals-wc' );
					break;
			}

			return apply_filters( 'smart_rentals_wc_get_time_placeholder', $placeholder );
		}

		/**
		 * Get datetime format
		 */
		public function get_datetime_format() {
			return apply_filters( 'smart_rentals_wc_get_datetime_format', $this->get_date_format() . ' ' . $this->get_time_format() );
		}

		/**
		 * Get string date
		 */
		public function get_string_date( $timestamp = '', $date_format = '', $time_format = '' ) {
			if ( !$timestamp ) return '';

			if ( !$date_format ) $date_format = $this->get_date_format();
			if ( !$time_format ) $time_format = $this->get_time_format();

			$string_date = gmdate( $date_format . ' ' . $time_format, $timestamp );

			return apply_filters( 'smart_rentals_wc_get_string_date', $string_date, $timestamp, $date_format, $time_format );
		}

		/**
		 * Get rental product IDs
		 */
		public function get_rental_product_ids() {
			$product_ids = get_posts([
				'post_type' => 'product',
				'posts_per_page' => -1,
				'post_status' => 'publish',
				'fields' => 'ids',
				'meta_query' => [
					[
						'key' => smart_rentals_wc_meta_key( 'enable_rental' ),
						'value' => 'yes',
						'compare' => '='
					]
				]
			]);

			return apply_filters( 'smart_rentals_wc_get_rental_product_ids', $product_ids );
		}

		/**
		 * Get order status for bookings
		 */
		public function get_booking_order_status() {
			return apply_filters( 'smart_rentals_wc_booking_order_status', [
				'wc-processing',
				'wc-completed',
				'wc-on-hold'
			]);
		}

		/**
		 * Get booked dates for product
		 */
		public function get_booked_dates( $product_id ) {
			if ( !$product_id ) return [];

			global $wpdb;

			$order_status = $this->get_booking_order_status();
			$status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );

			$booked_dates = $wpdb->get_results( $wpdb->prepare("
				SELECT 
					pickup_date.meta_value as pickup_date,
					dropoff_date.meta_value as dropoff_date,
					quantity.meta_value as quantity
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
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_id 
					ON items.order_item_id = product_id.order_item_id 
					AND product_id.meta_key = '_product_id'
				LEFT JOIN {$wpdb->posts} AS orders 
					ON items.order_id = orders.ID
				WHERE 
					product_id.meta_value = %d
					AND orders.post_status IN ('{$status_placeholders}')
					AND pickup_date.meta_value IS NOT NULL
					AND dropoff_date.meta_value IS NOT NULL
			",
				smart_rentals_wc_meta_key( 'pickup_date' ),
				smart_rentals_wc_meta_key( 'dropoff_date' ),
				'_qty',
				$product_id
			));

			return apply_filters( 'smart_rentals_wc_get_booked_dates', $booked_dates, $product_id );
		}

		/**
		 * Check availability for dates
		 */
		public function check_availability( $product_id, $pickup_date, $dropoff_date, $quantity = 1 ) {
			if ( !$product_id || !$pickup_date || !$dropoff_date ) {
				return false;
			}

			// Get product stock
			$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
			if ( !$rental_stock || $rental_stock < 1 ) {
				return false;
			}

			// Get booked dates
			$booked_dates = $this->get_booked_dates( $product_id );
			
			$pickup_timestamp = is_numeric( $pickup_date ) ? $pickup_date : strtotime( $pickup_date );
			$dropoff_timestamp = is_numeric( $dropoff_date ) ? $dropoff_date : strtotime( $dropoff_date );

			$booked_quantity = 0;

			foreach ( $booked_dates as $booking ) {
				$booking_pickup = strtotime( $booking->pickup_date );
				$booking_dropoff = strtotime( $booking->dropoff_date );

				// Check if dates overlap
				if ( $pickup_timestamp < $booking_dropoff && $dropoff_timestamp > $booking_pickup ) {
					$booked_quantity += intval( $booking->quantity );
				}
			}

			$available_quantity = $rental_stock - $booked_quantity;

			return $available_quantity >= $quantity;
		}

		/**
		 * Get available quantity for dates
		 */
		public function get_available_quantity( $product_id, $pickup_date, $dropoff_date ) {
			if ( !$product_id || !$pickup_date || !$dropoff_date ) {
				return 0;
			}

			// Get product stock
			$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
			if ( !$rental_stock || $rental_stock < 1 ) {
				return 0;
			}

			// Get booked dates
			$booked_dates = $this->get_booked_dates( $product_id );
			
			$pickup_timestamp = is_numeric( $pickup_date ) ? $pickup_date : strtotime( $pickup_date );
			$dropoff_timestamp = is_numeric( $dropoff_date ) ? $dropoff_date : strtotime( $dropoff_date );

			$booked_quantity = 0;

			foreach ( $booked_dates as $booking ) {
				$booking_pickup = strtotime( $booking->pickup_date );
				$booking_dropoff = strtotime( $booking->dropoff_date );

				// Check if dates overlap
				if ( $pickup_timestamp < $booking_dropoff && $dropoff_timestamp > $booking_pickup ) {
					$booked_quantity += intval( $booking->quantity );
				}
			}

			return max( 0, $rental_stock - $booked_quantity );
		}

		/**
		 * Calculate rental price
		 */
		public function calculate_rental_price( $product_id, $pickup_date, $dropoff_date, $quantity = 1 ) {
			if ( !$product_id || !$pickup_date || !$dropoff_date ) {
				return 0;
			}

			$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
			$daily_price = smart_rentals_wc_get_post_meta( $product_id, 'daily_price' );
			$hourly_price = smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' );

			$pickup_timestamp = is_numeric( $pickup_date ) ? $pickup_date : strtotime( $pickup_date );
			$dropoff_timestamp = is_numeric( $dropoff_date ) ? $dropoff_date : strtotime( $dropoff_date );

			$duration_seconds = $dropoff_timestamp - $pickup_timestamp;
			$duration_hours = $duration_seconds / 3600;
			$duration_days = $duration_seconds / 86400;

			$total_price = 0;

			switch ( $rental_type ) {
				case 'day':
				case 'hotel':
					if ( $daily_price > 0 ) {
						$days = max( 1, ceil( $duration_days ) );
						$total_price = $daily_price * $days * $quantity;
					}
					break;

				case 'hour':
				case 'appointment':
					if ( $hourly_price > 0 ) {
						$hours = max( 1, ceil( $duration_hours ) );
						$total_price = $hourly_price * $hours * $quantity;
					}
					break;

				case 'mixed':
					// Use daily pricing if rental is 24+ hours, otherwise hourly
					if ( $duration_hours >= 24 && $daily_price > 0 ) {
						$days = max( 1, ceil( $duration_days ) );
						$total_price = $daily_price * $days * $quantity;
					} elseif ( $hourly_price > 0 ) {
						$hours = max( 1, ceil( $duration_hours ) );
						$total_price = $hourly_price * $hours * $quantity;
					}
					break;

				default:
					// For other types, use daily pricing as fallback
					if ( $daily_price > 0 ) {
						$days = max( 1, ceil( $duration_days ) );
						$total_price = $daily_price * $days * $quantity;
					}
					break;
			}

			return apply_filters( 'smart_rentals_wc_calculate_rental_price', $total_price, $product_id, $pickup_date, $dropoff_date, $quantity );
		}

		/**
		 * Get rental duration text
		 */
		public function get_rental_duration_text( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) {
				return '';
			}

			$pickup_timestamp = is_numeric( $pickup_date ) ? $pickup_date : strtotime( $pickup_date );
			$dropoff_timestamp = is_numeric( $dropoff_date ) ? $dropoff_date : strtotime( $dropoff_date );

			$duration_seconds = $dropoff_timestamp - $pickup_timestamp;
			$duration_hours = $duration_seconds / 3600;
			$duration_days = $duration_seconds / 86400;

			if ( $duration_days >= 1 ) {
				$days = ceil( $duration_days );
				return sprintf( _n( '%d day', '%d days', $days, 'smart-rentals-wc' ), $days );
			} else {
				$hours = ceil( $duration_hours );
				return sprintf( _n( '%d hour', '%d hours', $hours, 'smart-rentals-wc' ), $hours );
			}
		}

		/**
		 * Get timezone string
		 */
		public function get_timezone_string() {
			$current_offset = get_option( 'gmt_offset' );
			$tzstring = get_option( 'timezone_string' );

			if ( str_contains( $tzstring, 'Etc/GMT' ) ) {
				$tzstring = '';
			}

			if ( empty( $tzstring ) ) {
				if ( 0 == $current_offset ) {
					$tzstring = 'UTC+0';
				} elseif ( $current_offset < 0 ) {
					$tzstring = 'UTC' . $current_offset;
				} else {
					$tzstring = 'UTC+' . $current_offset;
				}
			}

			$tzstring = str_replace( [ '.25', '.5', '.75' ], [ ':15', ':30', ':45' ], $tzstring );

			return apply_filters( 'smart_rentals_wc_get_timezone_string', $tzstring );
		}

		/**
		 * Get rental statistics
		 */
		public function get_rental_statistics() {
			$stats = [
				'total_rentals' => 0,
				'active_rentals' => 0,
				'total_revenue' => 0,
				'upcoming_rentals' => 0,
			];

			// Get rental product IDs
			$rental_product_ids = $this->get_rental_product_ids();

			if ( smart_rentals_wc_array_exists( $rental_product_ids ) ) {
				global $wpdb;

				$order_status = $this->get_booking_order_status();
				$status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );
				$product_ids_string = implode( ',', array_map( 'intval', $rental_product_ids ) );

				// Total rentals
				$stats['total_rentals'] = $wpdb->get_var("
					SELECT COUNT(DISTINCT items.order_item_id)
					FROM {$wpdb->prefix}woocommerce_order_items AS items
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_id 
						ON items.order_item_id = product_id.order_item_id 
						AND product_id.meta_key = '_product_id'
					LEFT JOIN {$wpdb->posts} AS orders 
						ON items.order_id = orders.ID
					WHERE 
						product_id.meta_value IN ({$product_ids_string})
						AND orders.post_status IN ('{$status_placeholders}')
				");

				// Active rentals (currently ongoing)
				$current_time = smart_rentals_wc_current_time();
				$stats['active_rentals'] = $wpdb->get_var( $wpdb->prepare("
					SELECT COUNT(DISTINCT items.order_item_id)
					FROM {$wpdb->prefix}woocommerce_order_items AS items
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_id 
						ON items.order_item_id = product_id.order_item_id 
						AND product_id.meta_key = '_product_id'
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pickup_date 
						ON items.order_item_id = pickup_date.order_item_id 
						AND pickup_date.meta_key = %s
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS dropoff_date 
						ON items.order_item_id = dropoff_date.order_item_id 
						AND dropoff_date.meta_key = %s
					LEFT JOIN {$wpdb->posts} AS orders 
						ON items.order_id = orders.ID
					WHERE 
						product_id.meta_value IN ({$product_ids_string})
						AND orders.post_status IN ('{$status_placeholders}')
						AND UNIX_TIMESTAMP(pickup_date.meta_value) <= %d
						AND UNIX_TIMESTAMP(dropoff_date.meta_value) >= %d
				",
					smart_rentals_wc_meta_key( 'pickup_date' ),
					smart_rentals_wc_meta_key( 'dropoff_date' ),
					$current_time,
					$current_time
				));

				// Upcoming rentals
				$stats['upcoming_rentals'] = $wpdb->get_var( $wpdb->prepare("
					SELECT COUNT(DISTINCT items.order_item_id)
					FROM {$wpdb->prefix}woocommerce_order_items AS items
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_id 
						ON items.order_item_id = product_id.order_item_id 
						AND product_id.meta_key = '_product_id'
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pickup_date 
						ON items.order_item_id = pickup_date.order_item_id 
						AND pickup_date.meta_key = %s
					LEFT JOIN {$wpdb->posts} AS orders 
						ON items.order_id = orders.ID
					WHERE 
						product_id.meta_value IN ({$product_ids_string})
						AND orders.post_status IN ('{$status_placeholders}')
						AND UNIX_TIMESTAMP(pickup_date.meta_value) > %d
				",
					smart_rentals_wc_meta_key( 'pickup_date' ),
					$current_time
				));

				// Total revenue (simplified calculation)
				$revenue_result = $wpdb->get_var("
					SELECT SUM(orders.post_excerpt)
					FROM {$wpdb->prefix}woocommerce_order_items AS items
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_id 
						ON items.order_item_id = product_id.order_item_id 
						AND product_id.meta_key = '_product_id'
					LEFT JOIN {$wpdb->posts} AS orders 
						ON items.order_id = orders.ID
					WHERE 
						product_id.meta_value IN ({$product_ids_string})
						AND orders.post_status IN ('{$status_placeholders}')
				");

				$stats['total_revenue'] = $revenue_result ? floatval( $revenue_result ) : 0;
			}

			return apply_filters( 'smart_rentals_wc_get_rental_statistics', $stats );
		}

		/**
		 * Main Smart_Rentals_WC_Get_Data Instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
}