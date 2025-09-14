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

			if ( !smart_rentals_wc_array_exists( $product_ids ) ) {
				$product_ids = [];
			}

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

			// First check our custom bookings table
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$booked_dates = $wpdb->get_results( $wpdb->prepare("
					SELECT 
						pickup_date,
						dropoff_date,
						quantity,
						status
					FROM $table_name
					WHERE 
						product_id = %d
						AND status IN ('confirmed', 'active', 'pending', 'processing', 'completed')
				", $product_id ));

				if ( smart_rentals_wc_array_exists( $booked_dates ) ) {
					// Debug logging
					if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						smart_rentals_wc_log( "Found " . count( $booked_dates ) . " bookings for product $product_id from custom table" );
						foreach ( $booked_dates as $booking ) {
							smart_rentals_wc_log( "Booking: {$booking->pickup_date} to {$booking->dropoff_date}, Qty: {$booking->quantity}, Status: {$booking->status}" );
						}
					}
					return apply_filters( 'smart_rentals_wc_get_booked_dates', $booked_dates, $product_id );
				}
			}

			// Fallback to order meta data
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
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS is_rental 
					ON items.order_item_id = is_rental.order_item_id 
					AND is_rental.meta_key = %s
				LEFT JOIN {$wpdb->posts} AS orders 
					ON items.order_id = orders.ID
				WHERE 
					product_id.meta_value = %d
					AND orders.post_status IN ('{$status_placeholders}')
					AND is_rental.meta_value = 'yes'
					AND pickup_date.meta_value IS NOT NULL
					AND dropoff_date.meta_value IS NOT NULL
			",
				smart_rentals_wc_meta_key( 'pickup_date' ),
				smart_rentals_wc_meta_key( 'dropoff_date' ),
				smart_rentals_wc_meta_key( 'rental_quantity' ),
				smart_rentals_wc_meta_key( 'is_rental' ),
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

			// Check disabled weekdays (only for usage period, not return date)
			$disabled_weekdays = smart_rentals_wc_get_post_meta( $product_id, 'disabled_weekdays' );
			if ( is_array( $disabled_weekdays ) && !empty( $disabled_weekdays ) ) {
				$pickup_timestamp = is_numeric( $pickup_date ) ? $pickup_date : strtotime( $pickup_date );
				$dropoff_timestamp = is_numeric( $dropoff_date ) ? $dropoff_date : strtotime( $dropoff_date );
				
				// Get rental type to determine validation logic
				$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
				
				// For daily rentals (hotel logic), exclude the return date from validation
				$validation_end_timestamp = $dropoff_timestamp;
				if ( $rental_type === 'day' ) {
					$validation_end_timestamp = $dropoff_timestamp - 86400; // Exclude return day
				}
				
				// Check if pickup date falls on disabled weekday
				$pickup_weekday = date( 'w', $pickup_timestamp );
				if ( in_array( intval( $pickup_weekday ), array_map( 'intval', $disabled_weekdays ) ) ) {
					return false;
				}
				
				// For hourly/mixed rentals, also check dropoff date
				if ( $rental_type !== 'day' ) {
					$dropoff_weekday = date( 'w', $dropoff_timestamp );
					if ( in_array( intval( $dropoff_weekday ), array_map( 'intval', $disabled_weekdays ) ) ) {
						return false;
					}
				}
				
				// Check usage period only (excluding return date for daily rentals)
				if ( $pickup_timestamp !== $validation_end_timestamp ) {
					$current_date = $pickup_timestamp;
					while ( $current_date <= $validation_end_timestamp ) {
						$current_weekday = date( 'w', $current_date );
						if ( in_array( intval( $current_weekday ), array_map( 'intval', $disabled_weekdays ) ) ) {
							return false;
						}
						$current_date += 86400; // Add one day
					}
				}
			}

			// Get booked dates
			$booked_dates = $this->get_booked_dates( $product_id );
			
			$pickup_timestamp = is_numeric( $pickup_date ) ? $pickup_date : strtotime( $pickup_date );
			$dropoff_timestamp = is_numeric( $dropoff_date ) ? $dropoff_date : strtotime( $dropoff_date );

			$booked_quantity = 0;

		// Get rental type for proper overlap logic
		$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
		
		// For daily rentals (hotel logic), exclude return date from overlap checking
		$validation_end_timestamp = $dropoff_timestamp;
		if ( $rental_type === 'day' ) {
			$validation_end_timestamp = $dropoff_timestamp - 86400; // Exclude return day
		}

		foreach ( $booked_dates as $booking ) {
			$booking_pickup = strtotime( $booking->pickup_date );
			$booking_dropoff = strtotime( $booking->dropoff_date );
			
			// For daily rentals, exclude return dates from both bookings
			$booking_validation_end = $booking_dropoff;
			if ( $rental_type === 'day' ) {
				$booking_validation_end = $booking_dropoff - 86400; // Exclude return day from existing booking
			}

			// Enhanced overlap logic: Only check usage periods, not return dates
			if ( $pickup_timestamp <= $booking_validation_end && $validation_end_timestamp >= $booking_pickup ) {
				$booked_quantity += intval( $booking->quantity );
				
				// Debug logging for rental time logic
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					smart_rentals_wc_log( "Booking overlap detected in check_availability (usage period only): Booking {$booking->pickup_date} to {$booking->dropoff_date}, Qty: {$booking->quantity}" );
				}
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
						$days = max( 1, intval( ceil( $duration_days ) ) );
						$total_price = $daily_price * $days * $quantity;
					}
					break;

				case 'hour':
				case 'appointment':
					if ( $hourly_price > 0 ) {
						$hours = max( 1, intval( ceil( $duration_hours ) ) );
						$total_price = $hourly_price * $hours * $quantity;
					}
					break;

				case 'mixed':
					// Use daily pricing if rental is 24+ hours, otherwise hourly
					if ( $duration_hours >= 24 && $daily_price > 0 ) {
						$days = max( 1, intval( ceil( $duration_days ) ) );
						$total_price = $daily_price * $days * $quantity;
					} elseif ( $hourly_price > 0 ) {
						$hours = max( 1, intval( ceil( $duration_hours ) ) );
						$total_price = $hourly_price * $hours * $quantity;
					}
					break;

				case 'period_time':
					// Package-based pricing - use daily as base
					if ( $daily_price > 0 ) {
						$days = max( 1, intval( ceil( $duration_days ) ) );
						$total_price = $daily_price * $days * $quantity;
					}
					break;

				case 'transportation':
					// Transportation pricing - use daily as base
					if ( $daily_price > 0 ) {
						$total_price = $daily_price * $quantity; // Fixed price per trip
					}
					break;

				case 'taxi':
					// Distance-based pricing - use hourly as base for time
					if ( $hourly_price > 0 ) {
						$hours = max( 1, intval( ceil( $duration_hours ) ) );
						$total_price = $hourly_price * $hours * $quantity;
					}
					break;

				default:
					// For other types, use daily pricing as fallback
					if ( $daily_price > 0 ) {
						$days = max( 1, intval( ceil( $duration_days ) ) );
						$total_price = $daily_price * $days * $quantity;
					}
					break;
			}

			return apply_filters( 'smart_rentals_wc_calculate_rental_price', $total_price, $product_id, $pickup_date, $dropoff_date, $quantity );
		}

		/**
		 * Get available quantity for a specific calendar day
		 * More robust method specifically for calendar display
		 */
		public function get_calendar_day_availability( $product_id, $date_string ) {
			if ( !$product_id || !$date_string ) {
				return 0;
			}
			
			// Validate and normalize date format
			$date_parts = explode( '-', $date_string );
			if ( count( $date_parts ) !== 3 ) {
				smart_rentals_wc_log( "Invalid date format passed to get_calendar_day_availability: $date_string" );
				return 0;
			}

			// Check if this weekday is disabled
			$disabled_weekdays = smart_rentals_wc_get_post_meta( $product_id, 'disabled_weekdays' );
			if ( is_array( $disabled_weekdays ) && !empty( $disabled_weekdays ) ) {
				$timestamp = strtotime( $date_string );
				$weekday = date( 'w', $timestamp );
				if ( in_array( intval( $weekday ), array_map( 'intval', $disabled_weekdays ) ) ) {
					return 0; // Disabled weekday = 0 availability
				}
			}

			// Check if this specific date is disabled
			$disabled_start_dates = smart_rentals_wc_get_post_meta( $product_id, 'disabled_start_dates' );
			$disabled_end_dates = smart_rentals_wc_get_post_meta( $product_id, 'disabled_end_dates' );
			
			if ( is_array( $disabled_start_dates ) && is_array( $disabled_end_dates ) && !empty( $disabled_start_dates ) ) {
				$timestamp = strtotime( $date_string );
				
				foreach ( $disabled_start_dates as $index => $disabled_start ) {
					$disabled_end = isset( $disabled_end_dates[$index] ) ? $disabled_end_dates[$index] : $disabled_start;
					
					if ( !empty( $disabled_start ) ) {
						$disabled_start_timestamp = strtotime( $disabled_start );
						$disabled_end_timestamp = strtotime( $disabled_end );
						
						if ( $timestamp >= $disabled_start_timestamp && $timestamp <= $disabled_end_timestamp ) {
							return 0; // Disabled date = 0 availability
						}
					}
				}
			}

			// Get product stock
			$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
			$total_stock = $rental_stock ? intval( $rental_stock ) : 1;

			// Convert date string to timestamps for the entire day
			// Ensure we're working with the start of the day (00:00:00)
			$day_timestamp = strtotime( date( 'Y-m-d', strtotime( $date_string ) ) . ' 00:00:00' );
			$day_start = $day_timestamp; // 00:00:00
			$day_end = $day_timestamp + 86400 - 1; // 23:59:59

			$booked_quantity = 0;

			// Check custom bookings table first
			global $wpdb;
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$bookings = $wpdb->get_results( $wpdb->prepare("
					SELECT pickup_date, dropoff_date, quantity, status
					FROM $table_name
					WHERE product_id = %d
					AND status IN ('pending', 'confirmed', 'active', 'processing', 'completed')
				", $product_id ));
				
				// Debug: Log all bookings found
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG && !empty( $bookings ) ) {
					smart_rentals_wc_log( sprintf(
						"Found %d bookings for product %d when checking date %s",
						count( $bookings ),
						$product_id,
						$date_string
					));
				}

				foreach ( $bookings as $booking ) {
					// Normalize booking dates to start of day for daily rentals
					$booking_pickup = strtotime( date( 'Y-m-d', strtotime( $booking->pickup_date ) ) . ' 00:00:00' );
					$booking_dropoff = strtotime( date( 'Y-m-d', strtotime( $booking->dropoff_date ) ) . ' 00:00:00' );
					
					// Get rental type for proper logic
					$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
					
					// For daily rentals (hotel logic), the dropoff date is NOT included in the booking period
					// Example: Booking from Sep 25 to Sep 26 means the guest uses Sep 25 only, checks out on Sep 26
					if ( $rental_type === 'day' ) {
						// For daily rentals, exclude the dropoff day from the booking period
						// The day is only booked if: pickup <= day < dropoff
						// CRITICAL: Ensure both timestamps are normalized to midnight for accurate comparison
						$is_within_booking = ( $day_timestamp >= $booking_pickup && $day_timestamp < $booking_dropoff );
						
						if ( $is_within_booking ) {
							$booked_quantity += intval( $booking->quantity );
							
							// Debug logging
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								smart_rentals_wc_log( sprintf(
									"Calendar (Daily): Date %s (timestamp: %d) is within booking period %s (timestamp: %d) to %s (timestamp: %d), Qty: %d",
									$date_string,
									$day_timestamp,
									$booking->pickup_date,
									$booking_pickup,
									$booking->dropoff_date,
									$booking_dropoff,
									$booking->quantity
								));
							}
						}
					} else {
						// For hourly/mixed rentals, include turnaround time
						$turnaround_hours = smart_rentals_wc_get_turnaround_time( $product_id );
						$turnaround_seconds = $turnaround_hours * 3600;
						$booking_dropoff_with_turnaround = $booking_dropoff + $turnaround_seconds;
						
						// Check overlap including turnaround
						if ( $booking_pickup <= $day_end && $booking_dropoff_with_turnaround >= $day_start ) {
							$booked_quantity += intval( $booking->quantity );
							
							// Debug logging
							if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
								smart_rentals_wc_log( "Calendar (Hourly/Mixed): Date $date_string conflicts with booking {$booking->pickup_date} to {$booking->dropoff_date} (+ {$turnaround_hours}h turnaround), Qty: {$booking->quantity}" );
							}
						}
					}
				}
			}

			// Also check WooCommerce orders as fallback
			if ( $booked_quantity === 0 ) {
				$order_status = $this->get_booking_order_status();
				$status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );

				$order_bookings = $wpdb->get_results( $wpdb->prepare("
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
					LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta 
						ON items.order_item_id = product_meta.order_item_id 
						AND product_meta.meta_key = '_product_id'
					LEFT JOIN {$wpdb->posts} AS orders 
						ON items.order_id = orders.ID
					WHERE 
						product_meta.meta_value = %d
						AND orders.post_status IN ('{$status_placeholders}')
						AND pickup_date.meta_value IS NOT NULL
						AND dropoff_date.meta_value IS NOT NULL
				",
					smart_rentals_wc_meta_key( 'pickup_date' ),
					smart_rentals_wc_meta_key( 'dropoff_date' ),
					smart_rentals_wc_meta_key( 'rental_quantity' ),
					$product_id
				));

				foreach ( $order_bookings as $booking ) {
					if ( $booking->pickup_date && $booking->dropoff_date ) {
						// Normalize booking dates to start of day for daily rentals
						$booking_pickup = strtotime( date( 'Y-m-d', strtotime( $booking->pickup_date ) ) . ' 00:00:00' );
						$booking_dropoff = strtotime( date( 'Y-m-d', strtotime( $booking->dropoff_date ) ) . ' 00:00:00' );
						$booking_quantity = intval( $booking->quantity ) ?: 1; // Default to 1 if not set
						
						// Get rental type for proper logic
						$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
						
						// For daily rentals (hotel logic), the dropoff date is NOT included in the booking period
						if ( $rental_type === 'day' ) {
							// For daily rentals, exclude the dropoff day from the booking period
							// The day is only booked if: pickup <= day < dropoff
							if ( $day_timestamp >= $booking_pickup && $day_timestamp < $booking_dropoff ) {
								$booked_quantity += $booking_quantity;
								
								// Debug logging
								if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
									smart_rentals_wc_log( sprintf(
										"Calendar Order (Daily): Date %s (timestamp: %d) is within booking period %s (timestamp: %d) to %s (timestamp: %d), Qty: %d",
										$date_string,
										$day_timestamp,
										$booking->pickup_date,
										$booking_pickup,
										$booking->dropoff_date,
										$booking_dropoff,
										$booking_quantity
									));
								}
							}
						} else {
							// For hourly/mixed rentals, include turnaround time
							$turnaround_hours = smart_rentals_wc_get_turnaround_time( $product_id );
							$turnaround_seconds = $turnaround_hours * 3600;
							$booking_dropoff_with_turnaround = $booking_dropoff + $turnaround_seconds;
							
							// Check overlap including turnaround
							if ( $booking_pickup <= $day_end && $booking_dropoff_with_turnaround >= $day_start ) {
								$booked_quantity += $booking_quantity;
								
								// Debug logging
								if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
									smart_rentals_wc_log( "Calendar Order (Hourly/Mixed): Date $date_string conflicts with order booking {$booking->pickup_date} to {$booking->dropoff_date} (+ {$turnaround_hours}h turnaround), Qty: {$booking_quantity}" );
								}
							}
						}
					}
				}
			}

			$available_quantity = max( 0, $total_stock - $booked_quantity );
			
			// Enhanced debug logging
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				smart_rentals_wc_log( sprintf(
					"=== CALENDAR AVAILABILITY ===\nDate: %s (timestamp: %d)\nProduct: %d\nRental Type: %s\nTotal Stock: %d\nBooked Quantity: %d\nAvailable: %d\n===",
					$date_string,
					$day_timestamp,
					$product_id,
					smart_rentals_wc_get_post_meta( $product_id, 'rental_type' ),
					$total_stock,
					$booked_quantity,
					$available_quantity
				));
			}
			
			return $available_quantity;
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

			// More precise duration calculation
			if ( $duration_days >= 1 ) {
				$days = intval( ceil( $duration_days ) );
				if ( $duration_hours > 24 && ( fmod( $duration_hours, 24 ) > 0 ) ) {
					// Show days and hours for mixed durations (use fmod for float modulo)
					$extra_hours = intval( ceil( fmod( $duration_hours, 24 ) ) );
					return sprintf( 
						__( '%d days, %d hours', 'smart-rentals-wc' ), 
						intval( floor( $duration_days ) ), 
						$extra_hours 
					);
				} else {
					return sprintf( _n( '%d day', '%d days', $days, 'smart-rentals-wc' ), $days );
				}
			} else {
				$hours = intval( ceil( $duration_hours ) );
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