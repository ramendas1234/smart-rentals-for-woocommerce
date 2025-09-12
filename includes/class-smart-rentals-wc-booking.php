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

			// Order item display meta key
			add_filter( 'woocommerce_order_item_display_meta_key', [ $this, 'order_item_display_meta_key' ], 11, 3 );

			// Order item display meta value
			add_filter( 'woocommerce_order_item_display_meta_value', [ $this, 'order_item_display_meta_value' ], 11, 3 );

			// Hide item meta fields
			add_filter( 'woocommerce_order_item_get_formatted_meta_data', [ $this, 'hide_item_meta_fields' ], 11, 2 );

			// After checkout validation
			add_action( 'woocommerce_after_checkout_validation', [ $this, 'after_checkout_validation' ], 11, 2 );
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

			// Validate dates
			$pickup_timestamp = strtotime( $pickup_date );
			$dropoff_timestamp = strtotime( $dropoff_date );

			if ( !$pickup_timestamp || !$dropoff_timestamp ) {
				wc_add_notice( __( 'Invalid date format. Please select valid dates.', 'smart-rentals-wc' ), 'error' );
				return false;
			}

			if ( $pickup_timestamp >= $dropoff_timestamp ) {
				wc_add_notice( __( 'Drop-off date must be after pickup date.', 'smart-rentals-wc' ), 'error' );
				return false;
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
				wc_add_notice( __( 'This product is not available for the selected dates and quantity.', 'smart-rentals-wc' ), 'error' );
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

			// Add rental data
			$pickup_date = isset( $_POST['pickup_date'] ) ? sanitize_text_field( $_POST['pickup_date'] ) : '';
			$dropoff_date = isset( $_POST['dropoff_date'] ) ? sanitize_text_field( $_POST['dropoff_date'] ) : '';
			$pickup_time = isset( $_POST['pickup_time'] ) ? sanitize_text_field( $_POST['pickup_time'] ) : '';
			$dropoff_time = isset( $_POST['dropoff_time'] ) ? sanitize_text_field( $_POST['dropoff_time'] ) : '';

			if ( $pickup_date && $dropoff_date ) {
				$cart_item_data['rental_data'] = [
					'pickup_date' => $pickup_date,
					'dropoff_date' => $dropoff_date,
					'pickup_time' => $pickup_time,
					'dropoff_time' => $dropoff_time,
					'product_id' => $product_id,
					'rental_quantity' => $quantity,
				];

				// Make each rental booking unique
				$cart_item_data['unique_key'] = md5( microtime() . rand() );
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
				$item_data[] = [
					'key' => __( 'Pickup Date', 'smart-rentals-wc' ),
					'value' => $rental_data['pickup_date'] . ( !empty( $rental_data['pickup_time'] ) ? ' ' . $rental_data['pickup_time'] : '' ),
					'display' => '',
				];
			}

			// Drop-off date
			if ( !empty( $rental_data['dropoff_date'] ) ) {
				$item_data[] = [
					'key' => __( 'Drop-off Date', 'smart-rentals-wc' ),
					'value' => $rental_data['dropoff_date'] . ( !empty( $rental_data['dropoff_time'] ) ? ' ' . $rental_data['dropoff_time'] : '' ),
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

			$total_price = Smart_Rentals_WC()->options->calculate_rental_price(
				$product_id,
				$rental_data['pickup_date'],
				$rental_data['dropoff_date'],
				$rental_data['rental_quantity']
			);

			if ( $total_price > 0 ) {
				return wc_price( $total_price / $rental_data['rental_quantity'] );
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

				$total_price = Smart_Rentals_WC()->options->calculate_rental_price(
					$product_id,
					$rental_data['pickup_date'],
					$rental_data['dropoff_date'],
					$rental_data['rental_quantity']
				);

				if ( $total_price > 0 ) {
					$cart_item['data']->set_price( $total_price / $rental_data['rental_quantity'] );
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
			$item->add_meta_data( smart_rentals_wc_meta_key( 'is_rental' ), 'yes', true );

			// Calculate and save total price
			$total_price = Smart_Rentals_WC()->options->calculate_rental_price(
				$rental_data['product_id'],
				$rental_data['pickup_date'],
				$rental_data['dropoff_date'],
				$rental_data['rental_quantity']
			);

			if ( $total_price > 0 ) {
				$item->add_meta_data( smart_rentals_wc_meta_key( 'rental_total' ), $total_price, true );
			}

			// Add duration text
			$duration_text = Smart_Rentals_WC()->options->get_rental_duration_text( 
				$rental_data['pickup_date'], 
				$rental_data['dropoff_date'] 
			);
			
			if ( $duration_text ) {
				$item->add_meta_data( smart_rentals_wc_meta_key( 'duration' ), $duration_text, true );
			}
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
				return wc_price( $meta_value );
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

				// Final availability check
				if ( !Smart_Rentals_WC()->options->check_availability( 
					$product_id, 
					$rental_data['pickup_date'], 
					$rental_data['dropoff_date'], 
					$rental_data['rental_quantity'] 
				) ) {
					$product = wc_get_product( $product_id );
					$errors->add( 'validation', sprintf( 
						__( '%s is no longer available for the selected dates. Please choose different dates.', 'smart-rentals-wc' ), 
						$product->get_name() 
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