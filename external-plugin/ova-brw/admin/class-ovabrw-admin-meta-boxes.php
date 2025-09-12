<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Meta Boxes class.
 */
if ( !class_exists( 'OVABRW_Admin_Meta_Boxes' ) ) {

	class OVABRW_Admin_Meta_Boxes {

		/**
		 * Instance init
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Product rental selector
			add_filter( 'product_type_selector', [ $this, 'product_rental_selector' ] );

			// Default product rental query
			add_filter( 'woocommerce_product_type_query', [ $this, 'default_product_rental_query' ], 10, 2 );

			// Product options general
			add_action( 'woocommerce_product_options_general_product_data', [ $this, 'rental_options_general' ] );

			// Save rental meta
			add_action( 'woocommerce_process_product_meta', [ $this, 'save_rental_meta' ], 11, 2 );

			// Get rental price in dashboard
			add_filter( 'woocommerce_get_price_html', [ $this, 'get_rental_price_html' ], 11, 2 );
		}

		/**
		 * Product rental selector
		 */
		public function product_rental_selector( $product_types ) {
			if ( ovabrw_array_exists( $product_types ) ) {
				$product_types[OVABRW_RENTAL] = esc_html__( 'Rental', 'ova-brw' );
			}

	        return $product_types;
		}

		/**
		 * Default product rental query
		 */
		public function default_product_rental_query( $product_type, $product_id ) {
			global $pagenow, $post_type;

		    if ( 'post-new.php' == $pagenow && 'product' == $post_type ) {
		        return OVABRW_RENTAL;
		    }

		    return $product_type;
		}

		/**
		 * Update meta
		 */
		public function update_meta( $post_id = '', $name = '', $data = [], $type = '', $default = false ) {
			do_action( OVABRW_PREFIX.'before_update_meta', $post_id, $name, $data, $type, $default );

			if ( !$post_id || !$name ) return;

			// Meta key
			$meta_key = ovabrw_meta_key( $name );

			if ( '' !== ovabrw_get_meta_data( $meta_key, $data ) ) {
				if ( 'html' == $type ) {
					$meta_value = wp_kses_post( trim( $data[$meta_key] ) );
				} else {
					$meta_value = wc_clean( wp_unslash( $data[$meta_key] ) );
				}

				if ( !$meta_value && $default !== false ) {
					$meta_value = $default;
				}

				if ( '' !== $meta_value ) {
					if ( 'date' == $type ) {
						$meta_value = ovabrw_format_date( $meta_value );
					} elseif ( 'number' == $type ) {
						$meta_value = ovabrw_format_number( $meta_value );
					} elseif ( 'price' == $type ) {
						$meta_value = ovabrw_format_price( $meta_value );
					} elseif ( 'slug' == $type ) {
						$meta_value = ovabrw_sanitize_title( $meta_value );
					}

					update_post_meta( $post_id, $meta_key, $meta_value );
				} else {
					delete_post_meta( $post_id, $meta_key );
				}
			} else {
				delete_post_meta( $post_id, $meta_key );
			}

			do_action( OVABRW_PREFIX.'after_update_meta', $post_id, $name, $data, $type, $default );
		}

		/**
		 * Rental options general
		 */
		public function rental_options_general() {
			global $product_object, $rental_product;

			$rental_id = $product_object->get_id();

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $rental_id );
			if ( !$rental_product ) {
				$rental_product = OVABRW()->rental->get_rental_product( $rental_id, 'day' );
			}

			include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/views/html-rental-data-general.php' );
		}

		/**
		 * Save rental meta
		 */
		public function save_rental_meta( $post_id, $data ) {
			if ( !is_object( $data ) ) $_POST = $data;

			// Get product type
            $product_type = ovabrw_get_meta_data( 'product-type', $_POST );
            if ( !$product_type ) $product_type = ovabrw_get_meta_data( 'product_type', $_POST );

			// Rental type
			$rental_type = ovabrw_get_meta_data( ovabrw_meta_key( 'price_type' ), $_POST );

			if ( OVABRW_RENTAL === $product_type && $rental_type ) {
				// Update rental type
				$this->update_meta( $post_id, 'price_type', $_POST );

				// Update price
				switch ( $rental_type ) {
					case 'day':
						// Get regular price
						$regular_price = ovabrw_format_price( ovabrw_get_meta_data( ovabrw_meta_key( 'regular_price_day' ), $_POST ) );
						break;
					case 'hour':
						// Get regular price
			            $regular_price = ovabrw_format_price( ovabrw_get_meta_data( ovabrw_meta_key( 'regul_price_hour' ), $_POST ) );
						break;
					case 'mixed':
						// Get regular price
						$regular_price = ovabrw_format_price( ovabrw_get_meta_data( ovabrw_meta_key( 'regular_price_day' ), $_POST ) );
						break;
					case 'taxi':
						// Get regular price
			            $regular_price = ovabrw_format_price( ovabrw_get_meta_data( ovabrw_meta_key( 'regul_price_taxi' ), $_POST ) );
						break;
					case 'hotel':
						// Get regular price
						$regular_price = ovabrw_format_price( ovabrw_get_meta_data( ovabrw_meta_key( 'regular_price_hotel' ), $_POST ) );
						break;
					default:
						$regular_price = 0;
						break;
				}

				update_post_meta( $post_id, '_regular_price', $regular_price );
	            update_post_meta( $post_id, '_price', $regular_price );
				// End update regular price

				// Regular price by day
				$this->update_meta( $post_id, 'regular_price_day', $_POST, 'price' );

				// Regular price by hour
				$this->update_meta( $post_id, 'regul_price_hour', $_POST, 'price' );

				// Regular price by taxi
				$this->update_meta( $post_id, 'regul_price_taxi', $_POST, 'price' );
				$this->update_meta( $post_id, 'base_price', $_POST, 'price' );

				// Regular price by hotel
				$this->update_meta( $post_id, 'regular_price_hotel', $_POST, 'price' );

				// Charged by
				$this->update_meta( $post_id, 'define_1_day', $_POST );

				// Unfixed time
				$this->update_meta( $post_id, 'unfixed_time', $_POST );

				// Insurance amount
				$this->update_meta( $post_id, 'amount_insurance', $_POST, 'price' );

				// Use location
				$this->update_meta( $post_id, 'use_location', $_POST );

				// Time slots
				$this->update_meta( $post_id, 'time_slots_label', $_POST );
				$this->update_meta( $post_id, 'time_slots_location', $_POST );
				$this->update_meta( $post_id, 'time_slots_start', $_POST, 'date' );
				$this->update_meta( $post_id, 'time_slots_end', $_POST, 'date' );
				$this->update_meta( $post_id, 'time_slots_price', $_POST, 'price' );
				$this->update_meta( $post_id, 'time_slots_quantity', $_POST, 'number' );

				// Max seats
				$this->update_meta( $post_id, 'max_seats', $_POST, 'number' );

				// Enable deposit
				$this->update_meta( $post_id, 'enable_deposit', $_POST );

				// Show full payment
				$this->update_meta( $post_id, 'force_deposit', $_POST );

				// Deposit type
				$this->update_meta( $post_id, 'type_deposit', $_POST );

				// Deposit amount
				$this->update_meta( $post_id, 'amount_deposit', $_POST, 'price' );

				// Inventory
				$this->update_meta( $post_id, 'manage_store', $_POST );

				// Stock quantity
				$this->update_meta( $post_id, 'car_count', $_POST, 'number' );

				// Vehicle ids
				$this->update_meta( $post_id, 'id_vehicles', $_POST );

	            // Daily
	            $this->update_meta( $post_id, 'daily_monday', $_POST, 'price' );
	            $this->update_meta( $post_id, 'daily_tuesday', $_POST, 'price' );
	            $this->update_meta( $post_id, 'daily_wednesday', $_POST, 'price' );
	            $this->update_meta( $post_id, 'daily_thursday', $_POST, 'price' );
	            $this->update_meta( $post_id, 'daily_friday', $_POST, 'price' );
	            $this->update_meta( $post_id, 'daily_saturday', $_POST, 'price' );
	            $this->update_meta( $post_id, 'daily_sunday', $_POST, 'price' );

	            // Packages
	            $this->update_meta( $post_id, 'petime_id', $_POST, 'slug' );
	            $this->update_meta( $post_id, 'petime_price', $_POST, 'price' );
	            $this->update_meta( $post_id, 'package_type', $_POST );
	            $this->update_meta( $post_id, 'petime_days', $_POST, 'price' );
	            $this->update_meta( $post_id, 'pehour_start_time', $_POST, 'date' );
	            $this->update_meta( $post_id, 'pehour_end_time', $_POST, 'date' );
	            $this->update_meta( $post_id, 'pehour_unfixed', $_POST, 'price' );
	            $this->update_meta( $post_id, 'petime_label', $_POST );

	            // Package discounts
	            $key = ovabrw_meta_key( 'petime_discount' );
	            if ( ovabrw_get_meta_data( $key, $_POST ) ) {
	            	foreach ( ovabrw_get_meta_data( $key, $_POST ) as $k => $item ) {
	            		// Price
	            		if ( isset( $item['price'] ) ) {
	            			$_POST[$key][$k]['price'] = ovabrw_format_price( $item['price'] );
	            		}

	            		// Start time
	            		if ( isset( $item['start_time'] ) ) {
	            			$_POST[$key][$k]['start_time'] = ovabrw_format_date( $item['start_time'] );
	            		}

	            		// End time
	            		if ( isset( $item['end_time'] ) ) {
	            			$_POST[$key][$k]['end_time'] = ovabrw_format_date( $item['end_time'] );
	            		}
	            	}
	            }
	            $this->update_meta( $post_id, 'petime_discount', $_POST );
	            // End package discounts
	            
	            // Setup Map
	            $this->update_meta( $post_id, 'map_price_by', $_POST );
	            $this->update_meta( $post_id, 'waypoint', $_POST );
	            $this->update_meta( $post_id, 'max_waypoint', $_POST, 'number' );
	            $this->update_meta( $post_id, 'zoom_map', $_POST, 'number' );
	            $this->update_meta( $post_id, 'map_types', $_POST );
	            $this->update_meta( $post_id, 'bounds', $_POST );
	            $this->update_meta( $post_id, 'bounds_lat', $_POST, 'price' );
	            $this->update_meta( $post_id, 'bounds_lng', $_POST, 'price' );
	            $this->update_meta( $post_id, 'bounds_radius', $_POST, 'price' );
	            $this->update_meta( $post_id, 'restrictions', $_POST );

	            // Guests
	            $this->update_meta( $post_id, 'max_guests', $_POST, 'number' );
	            $this->update_meta( $post_id, 'min_guests', $_POST, 'number' );
	            $this->update_meta( $post_id, 'max_adults', $_POST, 'number' );
	            $this->update_meta( $post_id, 'min_adults', $_POST, 'number' );
	            $this->update_meta( $post_id, 'max_children', $_POST, 'number' );
	            $this->update_meta( $post_id, 'min_children', $_POST, 'number' );
	            $this->update_meta( $post_id, 'max_babies', $_POST, 'number' );
	            $this->update_meta( $post_id, 'min_babies', $_POST, 'number' );

	            // Locations
	            $this->update_meta( $post_id, 'st_pickup_loc', $_POST );
	            $this->update_meta( $post_id, 'st_dropoff_loc', $_POST );
	            $this->update_meta( $post_id, 'st_price_location', $_POST, 'price' );

	            // Location Price
	            $this->update_meta( $post_id, 'pickup_location', $_POST );
	            $this->update_meta( $post_id, 'dropoff_location', $_POST );
	            $this->update_meta( $post_id, 'price_location', $_POST, 'price' );
	            $this->update_meta( $post_id, 'location_time', $_POST, 'price' );

	            // Extra Time
	            $this->update_meta( $post_id, 'extra_time_hour', $_POST, 'price' );
	            $this->update_meta( $post_id, 'extra_time_label', $_POST );
	            $this->update_meta( $post_id, 'extra_time_price', $_POST, 'price' );

	            // Specifications
	            $this->update_meta( $post_id, 'specifications', $_POST );

	            // Features
	            $this->update_meta( $post_id, 'features_icons', $_POST );
	            $this->update_meta( $post_id, 'features_label', $_POST );
	            $this->update_meta( $post_id, 'features_desc', $_POST );
	            $this->update_meta( $post_id, 'features_special', $_POST );
	            $this->update_meta( $post_id, 'features_featured', $_POST );

	            // Global Discount
	            $this->update_meta( $post_id, 'global_discount_price', $_POST, 'price' );
	            $this->update_meta( $post_id, 'global_discount_duration_val_min', $_POST, 'price' );
	            $this->update_meta( $post_id, 'global_discount_duration_val_max', $_POST, 'price' );
	            $this->update_meta( $post_id, 'global_discount_duration_type', $_POST );

	            // Discount by Distance
	            $this->update_meta( $post_id, 'discount_distance_from', $_POST, 'price' );
	            $this->update_meta( $post_id, 'discount_distance_to', $_POST, 'price' );
	            $this->update_meta( $post_id, 'discount_distance_price', $_POST, 'price' );

	            // Special Time
	            $this->update_meta( $post_id, 'rt_price', $_POST, 'price' );
	            $this->update_meta( $post_id, 'rt_price_hour', $_POST, 'price' );
	            $this->update_meta( $post_id, 'rt_startdate', $_POST, 'date' );
	            $this->update_meta( $post_id, 'rt_starttime', $_POST, 'date' );
	            $this->update_meta( $post_id, 'rt_enddate', $_POST, 'date' );
	            $this->update_meta( $post_id, 'rt_endtime', $_POST, 'date' );

	            // Special time discounts
	            $key = ovabrw_meta_key( 'rt_discount' );
	            if ( ovabrw_get_meta_data( $key, $_POST ) ) {
	            	foreach ( ovabrw_get_meta_data( $key, $_POST ) as $k => $item ) {
	            		// Price
	            		if ( isset( $item['price'] ) ) {
	            			$_POST[$key][$k]['price'] = ovabrw_format_price( $item['price'] );
	            		}

	            		// From
	            		if ( isset( $item['min'] ) ) {
	            			$_POST[$key][$k]['min'] = ovabrw_format_price( $item['min'] );
	            		}

	            		// To
	            		if ( isset( $item['max'] ) ) {
	            			$_POST[$key][$k]['max'] = ovabrw_format_price( $item['max'] );
	            		}
	            	}
	            }
	            $this->update_meta( $post_id, 'rt_discount', $_POST );
	            // End special time discounts
	            
	            // Special Time by Distance
	            $this->update_meta( $post_id, 'st_pickup_distance', $_POST, 'date' );
	            $this->update_meta( $post_id, 'st_pickoff_distance', $_POST, 'date' );
	            $this->update_meta( $post_id, 'st_price_distance', $_POST, 'price' );

	            // Discount distance
	            $key = ovabrw_meta_key( 'st_discount_distance' );
	            if ( ovabrw_get_meta_data( $key, $_POST ) ) {
	            	foreach ( ovabrw_get_meta_data( $key, $_POST ) as $k => $item ) {
	            		// Price
	            		if ( isset( $item['from'] ) ) {
	            			$_POST[$key][$k]['from'] = ovabrw_format_price( $item['from'] );
	            		}

	            		// Start time
	            		if ( isset( $item['to'] ) ) {
	            			$_POST[$key][$k]['to'] = ovabrw_format_price( $item['to'] );
	            		}

	            		// End time
	            		if ( isset( $item['price'] ) ) {
	            			$_POST[$key][$k]['price'] = ovabrw_format_price( $item['price'] );
	            		}
	            	}
	            }
	            $this->update_meta( $post_id, 'st_discount_distance', $_POST );
	            // End discount distance
	            
	            // Special Time - Appointment
	            $this->update_meta( $post_id, 'special_price', $_POST, 'price' );
	            $this->update_meta( $post_id, 'special_startdate', $_POST, 'date' );
	            $this->update_meta( $post_id, 'special_enddate', $_POST, 'date' );

	            // Resources
	            $this->update_meta( $post_id, 'resource_id', $_POST, 'slug' );
	            $this->update_meta( $post_id, 'resource_name', $_POST );
	            $this->update_meta( $post_id, 'resource_price', $_POST, 'price' );
	            $this->update_meta( $post_id, 'resource_quantity', $_POST, 'number' );
	            $this->update_meta( $post_id, 'resource_duration_type', $_POST );

	            // Services
	            $this->update_meta( $post_id, 'label_service', $_POST );
	            $this->update_meta( $post_id, 'service_required', $_POST );
	            $this->update_meta( $post_id, 'service_id', $_POST, 'slug' );
	            $this->update_meta( $post_id, 'service_name', $_POST );
	            $this->update_meta( $post_id, 'service_price', $_POST, 'price' );
	            $this->update_meta( $post_id, 'service_qty', $_POST, 'number' );
	            $this->update_meta( $post_id, 'service_duration_type', $_POST );

	            // Allowed date
	            $this->update_meta( $post_id, 'allowed_startdate', $_POST, 'date' );
	            $this->update_meta( $post_id, 'allowed_enddate', $_POST, 'date' );

	            // Unavailable time
	            $this->update_meta( $post_id, 'untime_startdate', $_POST, 'date' );
	            $this->update_meta( $post_id, 'untime_enddate', $_POST, 'date' );

	            // Product template
	            $this->update_meta( $post_id, 'product_template', $_POST );

	            // Disable weekday
	            $this->update_meta( $post_id, 'product_disable_week_day', $_POST );

	            // Min rent day
	            $this->update_meta( $post_id, 'rent_day_min', $_POST, 'price' );

	            // Min rent hour
	            $this->update_meta( $post_id, 'rent_hour_min', $_POST, 'price' );

	            // Max rent day
	            $this->update_meta( $post_id, 'rent_day_max', $_POST, 'price' );

	            // Max rent hour
	            $this->update_meta( $post_id, 'rent_hour_max', $_POST, 'price' );

	            // Prepare vehicle day
				$this->update_meta( $post_id, 'prepare_vehicle_day', $_POST, 'price' );

				// Prepare vehicle hour
	            $this->update_meta( $post_id, 'prepare_vehicle', $_POST, 'price' );

				// Preparation time
	            $this->update_meta( $post_id, 'preparation_time', $_POST, 'price' );
	            
	            // Extra Tab
	            $this->update_meta( $post_id, 'manage_extra_tab', $_POST );
	            $this->update_meta( $post_id, 'extra_tab_shortcode', $_POST );

	            // Start Date For Booking
	            $this->update_meta( $post_id, 'manage_time_book_start', $_POST );
	            $this->update_meta( $post_id, 'product_time_to_book_start', $_POST );
	            $this->update_meta( $post_id, 'manage_default_hour_start', $_POST );
	            $this->update_meta( $post_id, 'product_default_hour_start', $_POST );

	            // End Date For Booking
	            $this->update_meta( $post_id, 'manage_time_book_end', $_POST );
	            $this->update_meta( $post_id, 'product_time_to_book_end', $_POST );
	            $this->update_meta( $post_id, 'manage_default_hour_end', $_POST );
	            $this->update_meta( $post_id, 'product_default_hour_end', $_POST );

	            // Custom Checkout Field
	            $this->update_meta( $post_id, 'manage_custom_checkout_field', $_POST );
	            $this->update_meta( $post_id, 'product_custom_checkout_field', $_POST );

	            // Show Pick-Up Location
	            $this->update_meta( $post_id, 'show_pickup_location_product', $_POST );
	            $this->update_meta( $post_id, 'show_other_location_pickup_product', $_POST );

	            // Show Drop-Off Location
	            $this->update_meta( $post_id, 'show_pickoff_location_product', $_POST );
	            $this->update_meta( $post_id, 'show_other_location_dropoff_product', $_POST );

	            // Show Pick-up Date
	            $this->update_meta( $post_id, 'show_pickup_date_product', $_POST );
	            $this->update_meta( $post_id, 'label_pickup_date_product', $_POST );
	            $this->update_meta( $post_id, 'new_pickup_date_product', $_POST );

	            // Show Drop-off Date
	            $this->update_meta( $post_id, 'dropoff_date_by_setting', $_POST );
	            $this->update_meta( $post_id, 'show_pickoff_date_product', $_POST );
	            $this->update_meta( $post_id, 'label_dropoff_date_product', $_POST );
	            $this->update_meta( $post_id, 'new_dropoff_date_product', $_POST );

	            // Show Quantity
	            $this->update_meta( $post_id, 'show_number_vehicle', $_POST );

	            // Display Price In Format
	            $this->update_meta( $post_id, 'single_price_format', $_POST );
	            $this->update_meta( $post_id, 'single_price_new_format', $_POST, 'html' );
	            $this->update_meta( $post_id, 'archive_price_format', $_POST );
	            $this->update_meta( $post_id, 'archive_price_new_format', $_POST, 'html' );

	            // Order Frontend
	            $this->update_meta( $post_id, 'car_order', $_POST, 'number' );

	            // Google map
	            if ( ovabrw_get_meta_data( 'pac-input', $_POST ) ) {
	            	$this->update_meta( $post_id, 'map_name', $_POST );
		            $this->update_meta( $post_id, 'address', $_POST );
		            $this->update_meta( $post_id, 'latitude', $_POST );
		            $this->update_meta( $post_id, 'longitude', $_POST );
	            } else {
	            	delete_post_meta( $post_id, OVABRW_PREFIX.'map_name' );
	            	delete_post_meta( $post_id, OVABRW_PREFIX.'address' );
	            	delete_post_meta( $post_id, OVABRW_PREFIX.'latitude' );
	            	delete_post_meta( $post_id, OVABRW_PREFIX.'longitude' );
	            }
			}
		}

		/**
		 * Rental price
		 */
		public function get_rental_price_html( $price_html, $product ) {
			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product->get_id() );
			if ( $rental_product ) {
				$price_html = apply_filters( OVABRW_PREFIX.'get_rental_price_html_in_dashboard', $rental_product->get_price_html( $price_html ) );
			}

			return $price_html;
		}

		/**
		 * Main OVABRW_Admin_Meta_Boxes instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}

	new OVABRW_Admin_Meta_Boxes();
}