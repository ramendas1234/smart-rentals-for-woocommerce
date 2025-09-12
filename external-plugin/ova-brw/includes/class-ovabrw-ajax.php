<?php defined( 'ABSPATH' ) || exit();

if ( ! class_exists( 'OVABRW_Ajax' ) ) {

	class OVABRW_Ajax {

		/**
		 * Constructor
		 */
		public function __construct() {
			$this->init();
		}

		/**
		 * Init
		 */
		public function init() {
			$arr_ajax = [
				'load_name_product',
				'load_tag_product',
				'get_packages',
				'calculate_total',
				'get_custom_taxonomies',
				'search_map',
				'product_ajax_filter',
				'search_taxi_ajax',
				'search_ajax_shortcode',
				'verify_recaptcha',
				'loading_datetimepicker',
				'get_time_slots',
				'time_slots_location',
				'add_to_cart',
			];

			foreach ( $arr_ajax as $name ) {
				add_action( 'wp_ajax_'.OVABRW_PREFIX.$name, [ $this, OVABRW_PREFIX.$name ] );
				add_action( 'wp_ajax_nopriv_'.OVABRW_PREFIX.$name, [ $this, OVABRW_PREFIX.$name ] );
			}
		}

		/**
		 * Load product name
		 */
		public function ovabrw_load_name_product() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get keyword
			$keyword = sanitize_text_field( ovabrw_get_meta_data( 'keyword', $_POST ) );

			// Get product
			$products = new WP_Query([
				'post_type' 		=> 'product',
				's' 				=> preg_replace( "/[^a-zA-Z]+/", " ", $keyword ),
				'posts_per_page' 	=> '10',
				'tax_query'         => [
                    'relation'      => 'AND',
                    [
                        'taxonomy'  => 'product_type',
                        'field'     => 'slug',
                        'terms'     => 'ovabrw_car_rental'
                    ]
                ]
			]);

			// Title
			$title = [];

			if ( $products->have_posts() ) :
				while ( $products->have_posts() ): $products->the_post();
					$title[] = html_entity_decode( get_the_title() );
				endwhile;
				wp_reset_postdata();  
			endif;

			echo json_encode( $title );
			wp_die();
		}

		/**
		 * Load product tags
		 */
		public function ovabrw_load_tag_product() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get keyword
			$keyword = sanitize_text_field( ovabrw_get_meta_data( 'keyword', $_POST ) );

			// Get product tags
		    $product_tags = new WP_Term_Query([
		    	'taxonomy' 	=> 'product_tag',
		        'search' 	=> $keyword
		    ]);

		    // Title
		    $title = [];

		    if ( $product_tags->terms ) {
		        foreach ( $product_tags->terms as $term ) {
		            $title[] =  $term->name;
		        }
		        wp_reset_postdata();
		    }

			echo json_encode( $title );
			wp_die();
		}

		/**
		 * Get packages
		 */
		public function ovabrw_get_packages() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// No packages available
			$no_packages = [
				'options' => '<option value="">'.esc_html__( 'There are no packages available', 'ova-brw' ).'</option>'
			];

			// Product ID
			$product_id = sanitize_text_field( ovabrw_get_meta_data( 'product_id', $_POST ) );

			// Pick-up location
			$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $_POST ) );

			// Drop-off location
			$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $_POST ) );

			// Pick-up date
			$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $_POST ) );
			if ( !$product_id || !$pickup_date ) {
				echo json_encode($no_packages);
				wp_die();
			}

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) {
				echo json_encode($no_packages);
				wp_die();
			}

		    // Get packages
		    $packages = $rental_product->get_packages( $pickup_date, $pickup_location, $dropoff_location );
			
			echo json_encode([
				'options' => $rental_product->get_package_options_html( $packages )
			]);
			wp_die();
		}

		/**
		 * Calculate total
		 */
		public function ovabrw_calculate_total() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );
			
			// Product ID
			$product_id = sanitize_text_field( ovabrw_get_meta_data( 'product_id', $_POST ) );
			if ( !$product_id ) wp_die();

			// Pick-up location
			$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $_POST ) );

			// Drop-off location
			$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $_POST ) );

			// Pick-up date
			$pickup_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'pickup_date', $_POST ) ) );
			if ( !$pickup_date ) wp_die();

			// Drop-off date
			$dropoff_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'dropoff_date', $_POST ) ) );

			// Package ID
			$package_id = sanitize_text_field( ovabrw_get_meta_data( 'package_id', $_POST ) );

			// Quantity
			$quantity = sanitize_text_field( ovabrw_get_meta_data( 'quantity', $_POST, 1 ) );

			// Deposit
			$deposit = sanitize_text_field( ovabrw_get_meta_data( 'deposit', $_POST ) );

			// Custom checkout fields
			$cckf = ovabrw_replace( '\\', '', ovabrw_get_meta_data( 'cckf', $_POST ) );
			$cckf = (array)json_decode( $cckf );

			// Quantity custom checkout fields
			$cckf_qty = ovabrw_replace( '\\', '', ovabrw_get_meta_data( 'cckf_qty', $_POST ) );
			$cckf_qty = (array)json_decode( $cckf_qty );

			// Resources
			$resources = ovabrw_replace( '\\', '', ovabrw_get_meta_data( 'resources', $_POST ) );
			$resources = (array)json_decode( $resources );

			// Quantity resource
			$resources_qty = ovabrw_replace( '\\', '', ovabrw_get_meta_data( 'resources_qty', $_POST ) );
			$resources_qty = (array)json_decode( $resources_qty );

			// Services
			$services = ovabrw_replace( '\\', '', ovabrw_get_meta_data( 'services', $_POST ) );
			$services = (array)json_decode( $services );

			// Quantity services
			$services_qty = ovabrw_replace( '\\', '', ovabrw_get_meta_data( 'services_qty', $_POST ) );
			$services_qty = (array)json_decode( $services_qty );

			// Taxi
			$duration_map 	= sanitize_text_field( ovabrw_get_meta_data( 'duration_map', $_POST ) );
			$duration 		= sanitize_text_field( ovabrw_get_meta_data( 'duration', $_POST, 0 ) );
			$distance 		= sanitize_text_field( ovabrw_get_meta_data( 'distance', $_POST ) );
			$extra_time 	= sanitize_text_field( ovabrw_get_meta_data( 'extra_time', $_POST ) );

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) wp_die();

			// Post data
			$post_data = apply_filters( OVABRW_PREFIX.'post_data_calculate_total', [
				'pickup_location' 	=> $pickup_location,
				'dropoff_location' 	=> $dropoff_location,
				'pickup_date' 		=> $pickup_date,
				'dropoff_date' 		=> $dropoff_date,
				'package_id' 		=> $package_id,
				'duration' 			=> $duration
			], $_POST );

			// Get new date
			$new_date = $rental_product->get_new_date( $post_data );
			if ( !ovabrw_array_exists( $new_date ) ) wp_die();

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $new_date );

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $new_date );

			// Booking validation
			$booking_validation = $rental_product->booking_validation( $pickup_date, $dropoff_date, $_POST );
			if ( $booking_validation ) {
				echo json_encode([
					'error' => $booking_validation
				]); wp_die();
			}

			// Get items available
			$items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'cart' );

			// Vehicles available
			if ( is_array( $items_available ) ) {
				$items_available = count( $items_available );
			}

			// Check quantity
			if ( $items_available < $quantity ) {
				if ( $items_available > 0 ) {
					echo json_encode([
						'error' => sprintf( esc_html__( 'Items available: %s', 'ova-brw'  ), $items_available )
					]); wp_die();
				} else {
					echo json_encode([
						'error' => esc_html__( 'Out stock!', 'ova-brw' )
					]); wp_die();
				}
			}

	        // Qty available
		    $results['items_available'] = $items_available;

		    // Insurance amount
		    $insurance_amount = (float)$rental_product->product->get_meta_value( 'amount_insurance' ) * $quantity;

	        // Add Cart item
	        $cart_item = apply_filters( OVABRW_PREFIX.'cart_item_calculate_total', [
	        	'pickup_date' 		=> $pickup_date,
	        	'dropoff_date' 		=> $dropoff_date,
	        	'pickup_location' 	=> $pickup_location,
	        	'dropoff_location' 	=> $dropoff_location,
	        	'package_id' 		=> $package_id,
	        	'duration' 			=> $duration,
	        	'distance' 			=> $distance,
	        	'extra_time' 		=> $extra_time,
	        	'quantity' 			=> $quantity,
	        	'cckf'  			=> $cckf,
	        	'cckf_qty' 			=> $cckf_qty,
	        	'resources' 		=> $resources,
	        	'resources_qty' 	=> $resources_qty,
	        	'services' 			=> $services,
	        	'services_qty' 		=> $services_qty
	        ], $_POST );

	        // Get line total
	        $line_total = $rental_product->get_total( $cart_item );
	        if ( !$line_total ) $line_total = 0;

			// Multi Currency
        	if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
                $line_total 		= ovabrw_convert_price( $line_total );
                $insurance_amount 	= ovabrw_convert_price( $insurance_amount );
            }

            // Total amount
            $total_amount = $line_total;
            if ( $insurance_amount ) $total_amount += $insurance_amount;

            // Deposit
            if ( 'deposit' === $deposit ) {
            	$deposit_type 	= $rental_product->product->get_meta_value( 'type_deposit' );
            	$deposit_value 	= (float)$rental_product->product->get_meta_value( 'amount_deposit' );

            	// Calculate deposit
            	if ( 'percent' === $deposit_type ) { // Percent
            		$line_total = floatval( ( $line_total * $deposit_value ) / 100 );

            		if ( $insurance_amount && 'yes' !== ovabrw_get_setting( 'only_add_insurance_to_deposit', 'no' ) ) {
		            	$insurance_amount = floatval( ( $insurance_amount * $deposit_value ) / 100 );
		            }
            	} elseif ( 'value' === $deposit_type ) { // Fixed
            		$line_total = floatval( $deposit_value );
            	}
            }

            // Insurance amount
            if ( $insurance_amount ) {
            	$line_total += $insurance_amount;

            	$insurance_html = sprintf( esc_html__( '(includes %s insurance)', 'ova-brw' ), ovabrw_wc_price( $insurance_amount ) );

            	$results['insurance_amount'] = apply_filters( OVABRW_PREFIX.'ajax_insurance_html', $insurance_html, $product_id );
            }
			
			if ( $line_total <= 0 && apply_filters( OVABRW_PREFIX.'required_total', false ) ) {
				wp_die();
			} else {
				if ( 'deposit' === $deposit ) {
					$line_total = wp_kses_post( sprintf( __( 'Deposit: <span class="show_total">%s</span> (of %s)', 'ova-brw' ), ovabrw_wc_price( $line_total ), ovabrw_wc_price( $total_amount ) ) );
				} else {
					$line_total = wp_kses_post( sprintf( __( 'Total: <span class="show_total">%s</span>', 'ova-brw' ), ovabrw_wc_price( $line_total ) ) );
				}

				// Tax enabled
				if ( wc_tax_enabled() && apply_filters( OVABRW_PREFIX.'show_tax_label', true ) ) {
					$product = wc_get_product( $product_id );

					if ( $product->is_taxable() && !wc_prices_include_tax() ) {
						$line_total .= ' <small class="tax_label">'.esc_html__( '(excludes tax)', 'ova-brw' ).'</small>';
					}
				}

				$results['line_total'] = apply_filters( OVABRW_PREFIX.'ajax_total_filter', $line_total, $product_id );

				echo json_encode( $results );
			}

			wp_die();
		}

		/**
		 * Get custom taxonomies
		 */
		public function ovabrw_get_custom_taxonomies() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// init
			$results = [];

			// Category
			$category = ovabrw_get_meta_data( 'cat_val', $_POST );

			// Get term
			$get_term = get_term_by( 'slug', $category, 'product_cat' );

			// Term id
			$term_id = $get_term ? $get_term->term_id : '';

			// Get custom taxonomies
			$custom_taxonomies = get_term_meta( $term_id, 'ovabrw_custom_tax', true );
			if ( ovabrw_array_exists( $custom_taxonomies ) ) {
				foreach ( $custom_taxonomies as $key => $value ) {
					if ( $value && !in_array( $value, $results ) ) {
						array_push( $results, $value );
					}
				}
			}

			echo implode( ',', $results ); 
			wp_die();
		}

		/**
		 * Search map
		 */
		public function ovabrw_search_map() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Sort
			$sort = sanitize_text_field( ovabrw_get_meta_data( 'sort', $_POST ) );

			// Order
			$order = sanitize_text_field( ovabrw_get_meta_data( 'order', $_POST ) );

			// Order by
			$orderby = sanitize_text_field( ovabrw_get_meta_data( 'orderby', $_POST ) );

			// Posts per page
			$posts_per_page = sanitize_text_field( ovabrw_get_meta_data( 'posts_per_page', $_POST ) );

			// Paged
			$paged = (int)ovabrw_get_meta_data( 'paged', $_POST, 1 );

			// Product name
			$product_name = sanitize_text_field( ovabrw_get_meta_data( 'product_name', $_POST ) );

			// Pick-up location
		    $pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $_POST ) );

		    // Drop-off location
		    $dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $_POST ) );

		    // Pick-up date
		    $pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $_POST ) );

		    // Drop-off date
		    $dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $_POST ) );
		    if ( !$dropoff_date ) $dropoff_date = $pickup_date; 

		    // Duration
		    $duration = (int)ovabrw_get_meta_data( 'package', $_POST );
		    if ( $duration && $dropoff_date ) {
		    	$dropoff_date += $duration;
		    }

		    // Category
		    $category = sanitize_text_field( ovabrw_get_meta_data( 'cat', $_POST ) );

		    // Card template
		    $card = sanitize_text_field( ovabrw_get_meta_data( 'card', $_POST ) );

		    // Column
		    $column = sanitize_text_field( ovabrw_get_meta_data( 'column', $_POST ) );

		    // Attribute
		    $attribute = sanitize_text_field( ovabrw_get_meta_data( 'attribute', $_POST ) );

		    // Attribute value
		    $attr_value = sanitize_text_field( ovabrw_get_meta_data( 'attribute_value', $_POST ) );

		    // Product tag
		    $product_tag = sanitize_text_field( ovabrw_get_meta_data( 'product_tag', $_POST ) );

		    // Taxonomies
		    $taxonomies = sanitize_text_field( ovabrw_get_meta_data( 'taxonomies', $_POST ) );
		    $taxonomies = str_replace( '\\', '', $taxonomies );
			if ( $taxonomies ) {
				$taxonomies = json_decode( $taxonomies, true );
			}

		    // Quantity
		    $quantity = (int)sanitize_text_field( ovabrw_get_meta_data( 'quantity', $_POST , 1 ) );

		    // Number of adults
		    $adults = sanitize_text_field( ovabrw_get_meta_data( 'adults', $_POST ) );

		    // Number of children
            $children = sanitize_text_field( ovabrw_get_meta_data( 'children', $_POST ) );

            // Number of babies
            $babies = sanitize_text_field( ovabrw_get_meta_data( 'babies', $_POST ) );

            // Include category
		    $cat_include = sanitize_text_field( ovabrw_get_meta_data( 'cat_include', $_POST ) );
		    $cat_include = json_decode( stripslashes( $cat_include ) );

		    // Exclude category
		    $cat_exclude = sanitize_text_field( ovabrw_get_meta_data( 'cat_exclude', $_POST ) );
			$cat_exclude = json_decode( stripslashes( $cat_exclude ) );

			// Latitude
			$map_lat = (float)ovabrw_get_meta_data( 'map_lat', $_POST );

			// Longitude
			$map_lng = (float)ovabrw_get_meta_data( 'map_lng', $_POST );

			// Map radius
			$radius = (int)ovabrw_get_meta_data( 'radius', $_POST );

			// Get post in & distances
			$post_in = $distances = [];

			// Get rental product ids
			$product_ids = OVABRW()->options->get_rental_product_ids();

			// Map latitude & longitude
			if ( $map_lat && $map_lng && $radius ) {
				foreach ( $product_ids as $product_id ) {
					/* Latitude Longitude Search */
					$lat_search = deg2rad( $map_lat );
					$lng_search = deg2rad( $map_lng );

					/* Latitude Longitude Post */
					$lat_post = (float)ovabrw_get_post_meta( $product_id, 'latitude', '39.177972' );
					$lng_post = (float)ovabrw_get_post_meta( $product_id, 'longitude', '-100.36375' );

					// Check latitude & longitude
					if ( !$lat_post || !$lng_post ) continue;

					$lat_post = deg2rad( $lat_post );
					$lng_post = deg2rad( $lng_post );

					$lat_delta = $lat_post - $lat_search;
					$lon_delta = $lng_post - $lng_search;

					// $angle = 2 * asin(sqrt(pow(sin($lat_delta / 2), 2) + cos($lat_search) * cos($lat_post) * pow(sin($lon_delta / 2), 2)));
					$angle = acos( sin( $lat_search ) * sin( $lat_post ) + cos( $lat_search ) * cos( $lat_post ) * cos( $lng_search - $lng_post ) );

					/* 6371 = the earth's radius in km */
					/* 3958.8 = the earth's radius in mi */
					$distance = 6371 * $angle;

					if ( $distance <= $radius ) {
						array_push( $distances, $distance );
						array_push( $post_in, $product_id );
					}
				}

				wp_reset_postdata();
				array_multisort( $distances, $post_in );

				// Post in
				if ( !ovabrw_array_exists( $post_in ) ) $post_in = [''];
			} else {
				foreach ( $product_ids as $product_id )  {
					array_push( $post_in, $product_id );
				}
			} // END
		    
			// Base query
		    $args_base = [
		    	'post_type'      => 'product',
				'post_status'    => 'publish',
				'posts_per_page' => -1
		    ];

		    // sort
		    $args_orderby 	= $orderby ? [ 'orderby' => $orderby ] : [ 'orderby' => 'title' ];
		    $args_order 	= $order ? [ 'order' => $order ] : [ 'order' => 'DESC' ];

		    // Sort
		    switch ( $sort ) {
				case 'date-desc':
					$args_orderby 	= [ 'orderby' => 'date' ];
					$args_order 	= [ 'order' => 'DESC' ];
					$order 			= 'DESC';
					break;
				case 'date-asc':
					$args_orderby 	= [ 'orderby' => 'date' ];
					$args_order 	= [ 'order' => 'ASC' ];
					$order 			= 'ASC';
					break;
				case 'a-z':
					$args_orderby 	= [ 'orderby' => 'title' ];
					$args_order 	= [ 'order' => 'ASC' ];
					$order 			= 'ASC';
					break;
				case 'z-a':
					$args_orderby 	= [ 'orderby' => 'title' ];
					$args_order 	= [ 'order' => 'DESC' ];
					$order 			= 'DESC';
					break;
				case 'rating':
					$args_orderby = [
						'orderby' 	=> 'meta_value_num',
						'meta_key' 	=> '_wc_average_rating'
					];
					break;
				default:
					break;
			}

			// Query merge
			$args_base = array_merge_recursive( $args_base, $args_orderby, $args_order );

			// Query post in
			if ( $post_in ) {
				$args_base = array_merge_recursive( $args_base, [
					'post__in' => $post_in
				]);
			}

			// Query product name
			if ( $product_name ) {
				$args_base = array_merge_recursive( $args_base, [
					's' => preg_replace( "/[^a-zA-Z]+/", " ", $product_name )
				]);
			} 

			// Query categories
			$args_tax_attr = [];
			if ( $category ) {
				$args_tax_attr[] = [
		            'taxonomy' => 'product_cat',
		            'field'    => 'slug',
		            'terms'    => $category,
		        ];

		        // Exclude cat
		        if ( $cat_exclude ) {  
		            $args_tax_attr[] = [
		                'taxonomy' => 'product_cat',
		                'field'    => 'id',
		                'terms'    => $cat_exclude,
		                'operator' => 'NOT IN',
		            ];
		        } // END if
			} else {
				// Include cat
		        if ( $cat_include ) {  
		            $args_tax_attr[] = [
		                'taxonomy' => 'product_cat',
		                'field'    => 'id',
		                'terms'    => $cat_include,
		                'compare'  => 'IN',
		            ];
		        } // END if

		        // Exclude cat
		        if ( $cat_exclude ) {  
		            $args_tax_attr[] = [
		                'taxonomy' => 'product_cat',
		                'field'    => 'id',
		                'terms'    => $cat_exclude,
		                'operator' => 'NOT IN',
		            ];
		        } // END if
			} 

			// Query attribute
			if ( $attribute ) {
		        $args_tax_attr[] = [
		            'taxonomy' 	=> 'pa_' . $attribute,
		            'field' 	=> 'slug',
		            'terms' 	=> [$attr_value],
		            'operator'  => 'IN',
		        ];
		    }

		    // Query product tag
			if ( $product_tag ) {
				$args_tax_attr[] = [
		            'taxonomy' 	=> 'product_tag',
		            'field' 	=> 'name',
		            'terms' 	=> $product_tag
		        ];
			}

			// Query taxonomy custom
		    if ( ovabrw_array_exists( $taxonomies ) ) {
		    	foreach ( $taxonomies as $slug => $value) {
		    		$taxo_name = sanitize_text_field( ovabrw_get_meta_data( $slug, $_POST ) );
		    		if ( $taxo_name ) {
		    			$args_tax_attr[] = [
				            'taxonomy' 	=> $slug,
				            'field' 	=> 'slug',
				            'terms' 	=> $taxo_name
				        ];
		    		}
		    	}
		    } // END if

			// Query taxonomy
			if ( ovabrw_array_exists( $args_tax_attr ) ) {
		        $args_taxonomy = [
		        	'tax_query' => [
		        		'relation' => 'AND',
		                $args_tax_attr
		        	]
		        ];

		        // Query merge
				$args_base = array_merge_recursive( $args_base, $args_taxonomy );
		    }

		    // Meta Query
            $args_meta_query = [];

            // Number of adults
            if ( '' != $adults ) {
                $args_meta_query[] = [
                    'key'     => 'ovabrw_max_adults',
                    'value'   => $adults,
                    'type'    => 'numeric',
                    'compare' => '>=',
                ];
            }

            // Number of children
            if ( '' != $children ) {
                $args_meta_query[] = [
                    'key'     => 'ovabrw_max_children',
                    'value'   => $children,
                    'type'    => 'numeric',
                    'compare' => '>=',
                ];
            }

            // Number of babies
            if ( '' != $babies ) {
                $args_meta_query[] = [
                    'key'     => 'ovabrw_max_babies',
                    'value'   => $babies,
                    'type'    => 'numeric',
                    'compare' => '>=',
                ];
            }

            // Meta query
            if ( ovabrw_array_exists( $args_meta_query ) ) {
                $meta_query = [
                	'meta_query' => [
                		'relation' => 'AND',
                        $args_meta_query
                	]
                ];

                // Query merge
				$args_base = array_merge_recursive( $args_base, $meta_query );
            }

            // Product IDs
            $product_ids = [];

			// Get products
		    $products = new WP_Query( apply_filters( OVABRW_PREFIX.'query_get_products_from_search_map', $args_base, $_POST ) );

		    if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();
		        // Product ID
		        $pid = get_the_id();

		        // Get rental product
		        $rental_product = OVABRW()->rental->get_rental_product( $pid );
		        if ( !$rental_product ) continue;

		        // Get package id
		        if ( $duration ) {
		        	$package_id = $rental_product->product->get_package_id( $duration );
		        	if ( $package_id ) $_POST['package_id'] = $package_id;
		        }

		        // Location validation
                if ( $pickup_location || $dropoff_location ) {
                    if ( !$rental_product->location_validation( $pickup_location, $dropoff_location ) ) {
                    	continue;
                    }
                }

                // Date validation
                if ( $pickup_date && $dropoff_date ) {
                	// Booking validation
					$booking_validation = $rental_product->booking_validation( $pickup_date, $dropoff_date, $_POST );
					if ( $booking_validation ) continue;

					// Get items available
                	$items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'search' );
                	if ( is_array( $items_available ) ) $items_available = count( $items_available );

                	if ( $items_available >= $quantity ) {
                		array_push( $product_ids, $pid );
                	}
                } else {
                	array_push( $product_ids, $pid );
                } // END if
		    endwhile; else :
		        $result = '<div class="not_found_product">'. esc_html__( 'No product found', 'ova-brw' ) .'</div>';
		    	$results_found = '<div class="results_found"><span>'. esc_html__( '0 Result Found', 'ova-brw' ) .'</span></div>';
		    endif; wp_reset_postdata();

		    // Render HTML
		    if ( $product_ids ) {
		        $args_product = [
		        	'post_type' 		=> 'product',
		            'posts_per_page' 	=> $posts_per_page,
		            'paged' 			=> $paged,
		            'post_status' 		=> 'publish',
		            'post__in' 			=> $product_ids,
		            'orderby' 			=> 'post__in',
		            'order' 			=> $order ? $order : 'DESC'
		        ];

		        $products = new WP_Query( apply_filters( OVABRW_PREFIX.'query_search_map', $args_product, $_POST ) );

		        // Card
		        if ( $card == 'card5' || $card == 'card6' ) $column = 'one-column';

		        ob_start(); ?>
		        <div class="ovabrw_product_archive <?php echo esc_attr( $column ); ?>">
					<?php
						woocommerce_product_loop_start();
						if ( $products->have_posts() ) : while ( $products->have_posts() ) : $products->the_post();
							// Get product ID
							$pid = get_the_id();

							// Get rental product
							$rental_product = OVABRW()->rental->get_rental_product( $pid );
							if ( !$rental_product ) continue;

							// Get price html
							$price_html = $rental_product->get_price_html();
							if ( $price_html ) {
								$price_html = htmlentities( $price_html );
							} else {
								$price_html = '';
							}

							// Get template
							if ( $card ) {
								// Get thumbnail type
								$thumbnail_type = ovabrw_get_option( 'glb_'.sanitize_file_name( $card ).'_thumbnail_type', 'slider' );

								// Get template
								ovabrw_get_template( 'modern/products/cards/ovabrw-'.sanitize_file_name( $card ).'.php', [
									'product_id' 		=> $pid,
									'thumbnail_type' 	=> $thumbnail_type
								]);
							} else {
								wc_get_template_part( 'content', 'product' );
							}

							// Data product input
							ovabrw_text_input([
								'type' 	=> 'hidden',
								'class' => 'data_product',
								'attrs' => [
									'data-title' 			=> get_the_title(),
									'data-link' 			=> get_the_permalink(),
									'data-average-rating' 	=> $rental_product->product->get_average_rating(),
									'data-number-comment' 	=> get_comments_number( $pid ),
									'data-thumbnail' 		=> wp_get_attachment_image_url( get_post_thumbnail_id() , 'thumbnail' ),
									'data-lat' 				=> $rental_product->get_meta_value( 'latitude' ),
									'data-lng' 				=> $rental_product->get_meta_value( 'longitude' ),
									'data-price' 			=> $price_html
								]
							]);
						endwhile; else: ?>
							<div class="not_found_product">
								<?php esc_html_e( 'No product found.', 'ova-brw' ); ?>
							</div>
						<?php endif; wp_reset_postdata();
						woocommerce_product_loop_end();
					?>
				</div>
		        <?php
		        // Max number pages
		        $max_num_pages = $products->max_num_pages;

				if (  $max_num_pages > 1 ): ?>
					<div class="ovabrw_pagination_ajax">
					<?php
						echo wp_kses_post( OVABRW()->options->get_html_pagination_ajax( $products->found_posts, $products->query_vars['posts_per_page'], $paged ) );
					?>
					</div>
					<?php
				endif;

				$result = ob_get_contents(); 
				ob_end_clean();

				ob_start();
				?>
					<div class="results_found">
						<?php if ( $products->found_posts == 1 ): ?>
						<span>
							<?php echo sprintf( esc_html__( '%s Result Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
						</span>
						<?php else: ?>
						<span>
							<?php echo sprintf( esc_html__( '%s Results Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
						</span>
						<?php endif; ?>

						<?php if ( 1 == ceil( $products->found_posts/ $products->query_vars['posts_per_page']) && $products->have_posts() ): ?>
							<span>
								<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
							</span>
						<?php elseif ( !$products->have_posts() ): ?>
							<span></span>
						<?php else: ?>
							<span>
								<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->query_vars['posts_per_page'] ) ); ?>
							</span>
						<?php endif; ?>
					</div>

				<?php
				$results_found = ob_get_contents();
				ob_end_clean();

				echo json_encode([
					'result' 		=> $result,
					'results_found' => $results_found
				]);
				wp_die();
		    } else {
		    	$result = '<div class="not_found_product">'. esc_html__( 'No product found.', 'ova-brw' ) .'</div>';
		    	$results_found = '<div class="results_found"><span>'. esc_html__( '0 Result Found', 'ova-brw' ) .'</span></div>';
		    	echo json_encode([
		    		'result' 		=> $result,
		    		'results_found' => $results_found
		    	]);
		    	wp_die();
		    }
		}

		/**
		 * Product ajax filter
		 */
		public function ovabrw_product_ajax_filter() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Category ids
			$category_ids = [];

            // Get term id
			$term_id = (int)ovabrw_get_meta_data( 'term_id', $_POST );
			if ( $term_id ) {
				array_push( $category_ids, $term_id );
			}

			// Get template
			$template = ovabrw_get_meta_data( 'template', $_POST, 'card1' );

			// Get posts per page
			$posts_per_page = ovabrw_get_meta_data( 'posts_per_page', $_POST, 6 );

			// Get orderby
			$orderby = ovabrw_get_meta_data( 'orderby', $_POST, 'ID' );

			// Get order
			$order = ovabrw_get_meta_data( 'order', $_POST, 'DESC' );

			// Get pagination
			$pagination = ovabrw_get_meta_data( 'pagination', $_POST );

			// Get paged
			$paged = ovabrw_get_meta_data( 'paged', $_POST, 1 );

			$products = OVABRW()->options->get_product_from_search([
				'paged' 			=> $paged,
				'posts_per_page' 	=> $posts_per_page,
				'orderby' 			=> $orderby,
				'order' 			=> $order,
				'category_ids' 		=> $category_ids
			]);

			ob_start();
			if ( $products->have_posts() ) : while ( $products->have_posts() ):
				$products->the_post();

				if ( $template ): ?>
					<li class="item">
						<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.sanitize_file_name( $template ).'.php' ); ?>
					</li>
				<?php else:
					wc_get_template_part( 'content', 'product' );
				endif;
			endwhile; else : ?>
				<div class="not-found">
					<?php esc_html_e( 'No product found.', 'ova-brw' ); ?>
				</div>
			<?php endif; wp_reset_postdata();

			$result = ob_get_contents();
			ob_end_clean();

			ob_start();
			if ( 'yes' == $pagination ) {
				$pages 		= $products->max_num_pages;
				$limit 		= $products->query_vars['posts_per_page'];
				$current 	= $paged;

				if ( $pages > 1 ):
					for ( $i = 1; $i <= $pages; $i++ ): ?>
					<li>
						<span
							class="page-numbers<?php echo $i == $current ? ' current' : ''; ?>"
							data-paged="<?php echo esc_attr( $i ); ?>">
							<?php echo esc_html( $i ); ?>
						</span>
					</li>
				<?php endfor; endif;
			}

			$pagination = ob_get_contents();
			ob_end_clean();

			echo json_encode([
				'result' 		=> $result,
				'pagination' 	=> $pagination
			]);

			wp_die();
		}

		/**
		 * Search taxi ajax
		 */
		public function ovabrw_search_taxi_ajax() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Origin
			$origin = ovabrw_get_meta_data( 'origin', $_POST );

			// Destination
			$destination = ovabrw_get_meta_data( 'destination', $_POST );

			// Duration
			$duration = (int)ovabrw_get_meta_data( 'duration', $_POST );

			// Distance
			$distance = ovabrw_get_meta_data( 'distance', $_POST );

			// Pick-up date
			$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $_POST ) );

			// Drop-off date
			$dropoff_date = $pickup_date;

			// Category
			$cat = ovabrw_get_meta_data( 'cat', $_POST );

			// Number of seats
			$seats = ovabrw_get_meta_data( 'seats', $_POST );

			// Quantity
			$quantity = ovabrw_get_meta_data( 'quantity', $_POST, 1 );

			// Taxonomies
			$taxonomies = ovabrw_get_meta_data( 'taxonomies', $_POST );

			// cart
			$card = ovabrw_get_meta_data( 'card', $_POST, 'card1' );

			// Posts per page
			$posts_per_page = ovabrw_get_meta_data( 'posts_per_page', $_POST, 6 );

			// Column
			$column = ovabrw_get_meta_data( 'column', $_POST, 'three-column' );

			// Term
			$term = ovabrw_get_meta_data( 'term', $_POST );

			// Orderby
			$orderby = ovabrw_get_meta_data( 'orderby', $_POST, 'date' );

			// Order
			$order = ovabrw_get_meta_data( 'order', $_POST, 'DESC' );

			// Pagination
			$pagination = ovabrw_get_meta_data( 'pagination', $_POST );

			// Paged
			$paged = ovabrw_get_meta_data( 'paged', $_POST, 1 );

			// Include cateogory
		    $cat_include = ovabrw_get_meta_data( 'cat_include', $_POST );
		    $cat_include = json_decode( stripslashes( $cat_include ) );

		    // Exclude category
		    $cat_exclude = ovabrw_get_meta_data( 'cat_exclude', $_POST );
			$cat_exclude = json_decode( stripslashes( $cat_exclude ) );

			// Taxonomies
			$taxonomies 	= str_replace( '\\', '',  $taxonomies);
			if ( $taxonomies ) {
				$taxonomies = json_decode( $taxonomies, true );
			}

			// Init
			$item_ids = $tax_query = $taxonomies_query = [];

			// Base query
            $args_base = [
            	'post_type'         => 'product',
                'posts_per_page'    => '-1',
                'post_status'       => 'publish',
                'fields'            => 'ids',
                'tax_query'         => [
                	'relation'      => 'AND',
                    [
                    	'taxonomy'  => 'product_type',
                        'field'     => 'slug',
                        'terms'     => 'ovabrw_car_rental'
                    ]
                ]
            ];

            // Product category
            if ( $cat ) {
                $taxonomies_query[] = [
                    'taxonomy'  => 'product_cat',
                    'field'     => 'slug',
                    'terms'     => $cat
                ];

		        if ( $cat_exclude ) {  
		            $taxonomies_query[] = [
		                'taxonomy' => 'product_cat',
		                'field'    => 'id',
		                'terms'    => $cat_exclude,
		                'operator' => 'NOT IN',
		            ];
		        }
            } else {
            	if ( $term ) {
            		$taxonomies_query[] = [
	                    'taxonomy'  => 'product_cat',
	                    'field'     => 'slug',
	                    'terms'     => $term
	                ];
	                if ( $cat_exclude ) {  
			            $taxonomies_query[] = [
			                'taxonomy' => 'product_cat',
			                'field'    => 'id',
			                'terms'    => $cat_exclude,
			                'operator' => 'NOT IN',
			            ];
			        }

            	} else {
            		if ( $cat_exclude ) {  
			            $taxonomies_query[] = [
			                'taxonomy' => 'product_cat',
			                'field'    => 'id',
			                'terms'    => $cat_exclude,
			                'operator' => 'NOT IN',
			            ];
			        }

			        if ( $cat_include ) {  
			            $taxonomies_query[] = [
			                'taxonomy' => 'product_cat',
			                'field'    => 'id',
			                'terms'    => $cat_include,
			                'compare'  => 'IN',
			            ];
			        }
            	}
            }
            
            // Query taxonomy custom
		    if ( $taxonomies && is_array( $taxonomies ) ) {
		    	foreach ( $taxonomies as $slug => $value) {
		    		$taxonomy_name = isset( $_POST[$slug] ) ? $_POST[$slug] : '';

		    		if ( $taxonomy_name ) {
		    			$taxonomies_query[] = [
				            'taxonomy' 	=> $slug,
				            'field' 	=> 'slug',
				            'terms' 	=> $taxonomy_name
				        ];
		    		}
		    	}
		    }

		    // Tax query
            if ( ovabrw_array_exists( $taxonomies_query ) ) {
            	$tax_query = [
                    'tax_query' => [
                        $taxonomies_query
                    ]
                ];
            }

            // Meta Query
            $args_meta_query_arr = $meta_query = [];

            // Number seats
            if ( $seats ) {
                $args_meta_query_arr[] = [
                    'key'     => 'ovabrw_max_seats',
                    'value'   => $seats,
                    'type'    => 'numeric',
                    'compare' => '>=',
                ];
            }

            if ( ovabrw_array_exists( $args_meta_query_arr ) ) {
                $meta_query = [
                	'meta_query' => [
                		'relation'  => 'AND',
                        $args_meta_query_arr
                	]
                ];
            }

            // Merge query
            $args_query = array_merge_recursive( $args_base, $tax_query, $meta_query );

            // Get product ids
            $product_ids = get_posts( $args_query );

            // Taxi
            if ( $pickup_date && $duration ) {
            	$dropoff_date = $pickup_date + $duration;
            }

            if ( ovabrw_array_exists( $product_ids ) ) {
                foreach ( $product_ids as $product_id ) {
                    // Check dates
                    if ( $pickup_date && $dropoff_date ) {
                    	// Get rental product
                    	$rental_product = OVABRW()->rental->get_rental_product( $product_id );
                    	if ( !$rental_product ) continue;

                    	// Get available items
                        $items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, '', '', 'search' );
	                	if ( is_array( $items_available ) ) $items_available = count( $items_available );
	                	if ( $items_available >= $quantity ) {
	                		array_push( $item_ids, $product_id );
	                	}
                    } else {
                        array_push( $item_ids, $product_id );
                    }
                }
            }

            $products = '';
            // Get Products
            if ( $item_ids ) {
                $args_query = [
                	'post_type'         => 'product',
                    'posts_per_page'    => $posts_per_page,
                    'paged'             => $paged,
                    'post_status'       => 'publish',
                    'post__in'          => $item_ids,
                    'order'             => $order,
                    'orderby'           => $orderby
                ];

                // Orderby: rating
                if ( 'rating' === $orderby ) {
                	$args_query['orderby'] 	= 'meta_value_num';
                	$args_query['meta_key'] = '_wc_average_rating';
                }

                $products = new WP_Query( $args_query );
            }

            ob_start();
			if ( $products && $products->have_posts() ) : while ( $products->have_posts() ):
				$products->the_post();

				if ( $card ): ?>
					<li class="item">
						<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.sanitize_file_name( $card ).'.php' ); ?>
					</li>
				<?php else:
					wc_get_template_part( 'content', 'product' );
				endif;
			endwhile; else : ?>
				<div class="not-found">
					<?php esc_html_e( 'No product found.', 'ova-brw' ); ?>
				</div>
			<?php endif; wp_reset_postdata();

			$result = ob_get_contents();
			ob_end_clean();

			ob_start();
			if ( 'yes' === $pagination && $products ) {
				$pages 		= $products->max_num_pages;
				$limit 		= $products->query_vars['posts_per_page'];
				$current 	= $paged;

				if ( $pages > 1 ):
					for ( $i = 1; $i <= $pages; $i++ ): ?>
					<li>
						<span
							class="page-numbers<?php echo $i == $current ? ' current' : ''; ?>"
							data-paged="<?php echo esc_attr( $i ); ?>">
							<?php echo esc_html( $i ); ?>
						</span>
					</li>
				<?php endfor; endif;
			}

			$pagination = ob_get_contents();
			ob_end_clean();

			echo json_encode([
				'result' 		=> $result,
				'pagination' 	=> $pagination
			]);
			wp_die();
		}

		/**
		 * Search ajax shortcode
		 */
		public function ovabrw_search_ajax_shortcode() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Latitude
			$lat = (float)ovabrw_get_meta_data( 'lat', $_POST );

			// Longitude
			$lng = (float)ovabrw_get_meta_data( 'lng', $_POST );

			// Radius
			$radius = (int)apply_filters( OVABRW_PREFIX.'search_ajax_shortcode_radius', ovabrw_get_meta_data( 'radius', $_POST, 50 ) );

            // Sort
            $sort = ovabrw_get_meta_data( 'sort', $_POST );

            // Product name
            $product_name = ovabrw_get_meta_data( 'product_name', $_POST );

            // Pick-up location
            $pickup_location = ovabrw_get_meta_data( 'pickup_location', $_POST );

            // Drop-off location
            $dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $_POST );

            // Pick-up date
            $pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $_POST ) );

            // Drop-off date
            $dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $_POST ) );
            if ( !$dropoff_date && $pickup_date ) $dropoff_date = $pickup_date;

            // Category
            $category = ovabrw_get_meta_data( 'cat', $_POST );

            // Product tag
            $product_tag = ovabrw_get_meta_data( 'product_tag', $_POST );

            // Quantity
            $quantity = (int)ovabrw_get_meta_data( 'quantity', $_POST, 1 );

            // Paged
            $paged = (int)ovabrw_get_meta_data( 'paged', $_POST, 1 );

            // Data queries
            $data_queries = ovabrw_get_meta_data( 'data_queries', $_POST );
            if ( $data_queries ) {
				$data_queries = json_decode( str_replace( '\\', '',  $data_queries ), true );
			}

			// Show results found
			$show_results_found = ovabrw_get_meta_data( 'show_results_found', $data_queries );

			// Card template
			$card = ovabrw_get_meta_data( 'card', $data_queries, 'card1' );

			// Posts per page
			$posts_per_page = ovabrw_get_meta_data( 'posts_per_page', $data_queries );

			// Orderby
			$orderby = ovabrw_get_meta_data( 'orderby', $data_queries );

			// Order
			$order = ovabrw_get_meta_data( 'order', $data_queries );

			// Pagination
			$show_pagination = ovabrw_get_meta_data( 'pagination', $data_queries );

			// Include categories
			$incl_category = ovabrw_get_meta_data( 'incl_category', $data_queries );

			// Exclude categories
			$excl_category = ovabrw_get_meta_data( 'excl_category', $data_queries );

            // Taxonomies
            $taxonomies = ovabrw_get_meta_data( 'taxonomies', $_POST );
			if ( $taxonomies ) {
				$taxonomies = json_decode( str_replace( '\\', '',  $taxonomies ), true );
			}

			// Init
			$item_ids = $tax_query = $taxonomies_query = [];

			// Base query
            $args_base = [
            	'post_type'         => 'product',
                'posts_per_page'    => '-1',
                'post_status'       => 'publish',
                'fields'            => 'ids',
                'tax_query'         => [
                	'relation'      => 'AND',
                    [
                    	'taxonomy'  => 'product_type',
                        'field'     => 'slug',
                        'terms'     => 'ovabrw_car_rental'
                    ]
                ]
            ];

			// Get post in & distances
			$post_in = $distances = [];
			if ( $lat && $lng ) {
				foreach ( OVABRW()->options->get_rental_product_ids() as $product_id ) {
					/* Latitude Longitude Search */
					$lat_search = deg2rad( $lat );
					$lng_search = deg2rad( $lng );

					/* Latitude Longitude Post */
					$lat_post = (float)ovabrw_get_post_meta( $product_id, 'latitude', '39.177972' );
					$lng_post = (float)ovabrw_get_post_meta( $product_id, 'longitude', '-100.36375' );

					// Check latitude & longitude
					if ( !$lat_post || !$lng_post ) continue;

					$lat_post = deg2rad( $lat_post );
					$lng_post = deg2rad( $lng_post );

					$lat_delta = $lat_post - $lat_search;
					$lon_delta = $lng_post - $lng_search;

					// $angle = 2 * asin(sqrt(pow(sin($lat_delta / 2), 2) + cos($lat_search) * cos($lat_post) * pow(sin($lon_delta / 2), 2)));
					$angle = acos( sin( $lat_search ) * sin( $lat_post ) + cos( $lat_search ) * cos( $lat_post ) * cos( $lng_search - $lng_post ) );

					/* 6371 = the earth's radius in km */
					/* 3958.8 = the earth's radius in mi */
					$distance = 6371 * $angle;
					if ( $distance <= $radius ) {
						array_push( $distances, $distance );
						array_push( $post_in, $product_id );
					}
				}

				// Multisort
				array_multisort( $distances, $post_in );

				// Post in
				if ( !ovabrw_array_exists( $post_in ) ) $post_in = [''];
			}

			// Query post in
			if ( ovabrw_array_exists( $post_in ) ) {
				$args_base = array_merge_recursive( $args_base, [
					'post__in' => $post_in
				]);
			}

            // Product name
            $args_name = [];
			if ( $product_name ) {
				$args_name = [ 's' => preg_replace( "/[^a-zA-Z]+/", " ", $product_name ) ];
			}

			// Product tag
			if ( $product_tag ) {
				$taxonomies_query[] = [
		            'taxonomy' 	=> 'product_tag',
		            'field' 	=> 'name',
		            'terms' 	=> $product_tag
		        ];
			}

            // Product category
            if ( $category ) {
                $taxonomies_query[] = [
                    'taxonomy'  => 'product_cat',
                    'field'     => 'slug',
                    'terms'     => $category
                ];
            }

            // Include category
            if ( ovabrw_array_exists( $incl_category ) ) {  
	            $taxonomies_query[] = [
	                'taxonomy' => 'product_cat',
	                'field'    => 'id',
	                'terms'    => $incl_category,
	                'compare'  => 'IN',
	            ];
	        }

            // Exclude category
            if ( ovabrw_array_exists( $excl_category ) ) {  
	            $taxonomies_query[] = [
	                'taxonomy' => 'product_cat',
	                'field'    => 'id',
	                'terms'    => $excl_category,
	                'operator' => 'NOT IN',
	            ];
	        }

            // Query taxonomy custom
		    if ( ovabrw_array_exists( $taxonomies ) ) {
		    	foreach ( $taxonomies as $term_slug => $term_name ) {
		    		$term_value = ovabrw_get_meta_data( $term_slug, $_POST );

		    		if ( $term_value ) {
		    			$taxonomies_query[] = [
				            'taxonomy' 	=> $term_slug,
				            'field' 	=> 'slug',
				            'terms' 	=> $term_value
				        ];
		    		}
		    	}
		    }

            if ( ovabrw_array_exists( $taxonomies_query ) ) {
            	$tax_query = [
                    'tax_query' => [
                        $taxonomies_query
                    ]
                ];
            }

            // sort
		    $args_orderby 	= $orderby ? [ 'orderby' => $orderby ] : [ 'orderby' => 'title' ];
		    $args_order 	= $order ? [ 'order' => $order ] : [ 'order' => 'DESC' ];

		    switch ( $sort ) {
				case 'date-desc':
					$args_orderby 	= [ 'orderby' => 'date' ];
					$args_order 	= [ 'order' => 'DESC' ];
					$order 			= 'DESC';
					break;
				case 'date-asc':
					$args_orderby 	= [ 'orderby' => 'date' ];
					$args_order 	= [ 'order' => 'ASC' ];
					$order 			= 'ASC';
					break;
				case 'a-z':
					$args_orderby 	= [ 'orderby' => 'title' ];
					$args_order 	= [ 'order' => 'ASC' ];
					$order 			= 'ASC';
					break;
				case 'z-a':
					$args_orderby 	= [ 'orderby' => 'title' ];
					$args_order 	= [ 'order' => 'DESC' ];
					$order 			= 'DESC';
					break;
				case 'rating':
					$args_orderby = [
						'orderby' 	=> 'meta_value_num',
						'meta_key' 	=> '_wc_average_rating'
					];
					break;
				default:
					break;
			}

            // Merge query
            $args_query = array_merge_recursive( $args_base, $args_name, $tax_query, $args_orderby, $args_order );

            // Get product ids
            $product_ids = get_posts( $args_query );
            if ( ovabrw_array_exists( $product_ids ) ) {
                foreach ( $product_ids as $product_id ) {
                	// Get rental product
			        $rental_product = OVABRW()->rental->get_rental_product( $product_id );
			        if ( !$rental_product ) continue;
                	
                	// Location validation
	                if ( $pickup_location || $dropoff_location ) {
	                    if ( !$rental_product->location_validation( $pickup_location, $dropoff_location ) ) {
	                    	continue;
	                    }
	                } // END if

                    // Check dates
                    if ( $pickup_date && $dropoff_date ) {
                        // Get available items
                        $items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'search' );
	                	if ( is_array( $items_available ) ) $items_available = count( $items_available );
	                	if ( $items_available >= $quantity ) {
	                		array_push( $item_ids, $product_id );
	                	}
                    } else {
                        array_push( $item_ids, $product_id );
                    }
                }
            }

            $products = '';
            // Get Products
            if ( $item_ids ) {
                $args_query = [
                	'post_type'         => 'product',
                    'posts_per_page'    => $posts_per_page,
                    'paged'             => $paged,
                    'post_status'       => 'publish',
                    'post__in'          => $item_ids,
                    'orderby' 			=> 'post__in',
		            'order' 			=> $order ? $order : 'DESC'
                ];

                // Orderby: rating
                if ( 'rating' === $orderby ) {
                	$args_query['orderby'] 	= 'meta_value_num';
                	$args_query['meta_key'] = '_wc_average_rating';
                }

                $products = new WP_Query( $args_query );
            }

            ob_start();
			if ( $products && $products->have_posts() ) : while ( $products->have_posts() ):
				$products->the_post();

				if ( $card ): ?>
					<li class="item">
						<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.sanitize_file_name( $card ).'.php' ); ?>
					</li>
				<?php else:
					wc_get_template_part( 'content', 'product' );
				endif;
			endwhile; else : ?>
				<div class="not-found">
					<?php esc_html_e( 'No product found.', 'ova-brw' ); ?>
				</div>
			<?php endif; wp_reset_postdata();

			$results = ob_get_contents();
			ob_end_clean();

			// Results found
			$results_found = '';
			if ( 'yes' == $show_results_found && $products ) {
				ob_start();
				if ( $products->found_posts == 1 ): ?>
					<span>
						<?php echo sprintf( esc_html__( '%s Result Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
					</span>
				<?php else: ?>
					<span>
						<?php echo sprintf( esc_html__( '%s Results Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
					</span>
				<?php endif;

				if ( 1 == ceil( $products->found_posts/ $products->query_vars['posts_per_page']) && $products->have_posts() ): ?>
					<span>
						<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
					</span>
				<?php elseif ( !$products->have_posts() ): ?>
					<span></span>
				<?php else: ?>
					<span>
						<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->query_vars['posts_per_page'] ) ); ?>
					</span>
				<?php endif;
				$results_found = ob_get_contents();
				ob_end_clean();
			}

			// Pagination
			$pagination = '';
			if ( 'yes' === $show_pagination && $products ) {
				ob_start();
				$pages 		= $products->max_num_pages;
				$limit 		= $products->query_vars['posts_per_page'];
				$current 	= $paged;

				if ( $pages > 1 ):
					for ( $i = 1; $i <= $pages; $i++ ): ?>
					<li>
						<span
							class="page-numbers<?php echo $i == $current ? ' current' : ''; ?>"
							data-paged="<?php echo esc_attr( $i ); ?>">
							<?php echo esc_html( $i ); ?>
						</span>
					</li>
				<?php endfor; endif;

				$pagination = ob_get_contents();
				ob_end_clean();
			}

			echo json_encode([
				'results' 		=> $results,
				'results_found' => $results_found,
				'pagination' 	=> $pagination
			]);

			wp_die();
		}

		/**
		 * Verify recaptcha
		 */
		public function ovabrw_verify_recaptcha() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );
			
			if ( !ovabrw_array_exists( $_POST ) ) {
				echo esc_html( OVABRW()->options->get_recaptcha_error_mesg() );
				wp_die();
			}

			$token 	= ovabrw_get_meta_data( 'token', $_POST );
			$mess 	= '';

			if ( 'v2' == OVABRW()->options->get_recaptcha_type() ) {
				$mess = OVABRW()->options->verify_recaptcha_v2( $token );
			} elseif ( 'v3' == OVABRW()->options->get_recaptcha_type() ) {
				$mess = OVABRW()->options->verify_recaptcha_v3( $token );
			}

			echo esc_html( $mess );
			wp_die();
		}

		/**
		 * Loading datetimepicker
		 */
		public function ovabrw_loading_datetimepicker() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// Get product ID
			$product_id = absint( ovabrw_get_meta_data( 'product_id', $_POST ) );
			if ( !$product_id ) wp_die();

			// Get product object
			$product = wc_get_product( $product_id );
			if ( !$product || !$product->is_type( 'ovabrw_car_rental' ) ) wp_die();

			// Get datepicker options
			$datepicker_options = $product->get_datepicker_options();

			// Get timepicker options
			$timepicker_options = $product->get_timepicker_options();

			if ( ovabrw_array_exists( $datepicker_options ) ) {
				echo wp_json_encode([
					'datePickerOptions' => $datepicker_options,
					'timePickerOptions' => $timepicker_options
				]);
			}

			wp_die();
		}

		/**
		 * Get time slots
		 */
		public function ovabrw_get_time_slots() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// init results
			$results['error'] = esc_html__( 'There are no time slots available. Please choose another date.', 'ova-brw' );

			// Product ID
			$product_id = (int)ovabrw_get_meta_data( 'product_id', $_POST );

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) {
				echo wp_json_encode( $results );
				wp_die();
			}

			// Pick-up date
			$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $_POST ) );
			if ( !$pickup_date ) {
				echo wp_json_encode( $results );
				wp_die();
			}

			// Location name
			$location_name = ovabrw_get_meta_data( 'location_name', $_POST );

			// Timeslots name
			$timeslot_name = ovabrw_get_meta_data( 'timeslot_name', $_POST );

			// Use location
			$use_location = $rental_product->get_meta_value( 'use_location' );

			if ( $use_location ) {
				// Get time slots
				$time_slots = $rental_product->get_time_slots_use_location( $pickup_date );

				if ( ovabrw_array_exists( $time_slots ) ) {
					// Get timeslots location data
					$timeslots_location_data = $rental_product->get_time_slots_location_html( $time_slots, $location_name );

					// Time slots location HTML
					$timeslots_location = ovabrw_get_meta_data( 'locations', $timeslots_location_data );

					// Default time slots
					$default_timeslots = ovabrw_get_meta_data( 'default_timeslots', $timeslots_location_data );

					// Get time slots data
					$timeslots_data = $rental_product->get_time_slots_html( $default_timeslots, $timeslot_name );

					// Get time slots HTML
					$timeslots_html = ovabrw_get_meta_data( 'timeslots', $timeslots_data );

					if ( $timeslots_location && $timeslots_html ) {
						unset( $results['error'] );

						$results['timeslots_location'] 	= $timeslots_location;
						$results['timeslots_html'] 		= $timeslots_html;

						// Date format
						$date_format = OVABRW()->options->get_date_format();

						// Time format
						$time_format = OVABRW()->options->get_time_format();

						// Get drop-off date
						$results['dropoff_date'] = '';

						// Get end date
						$end_date = ovabrw_get_meta_data( 'end_date', $timeslots_data );

						if ( $end_date ) {
							$results['dropoff_date'] = gmdate( $date_format.' '.$time_format, $end_date );
						}
					}
				}
			} else {
				// Get time slots
				$time_slots = $rental_product->get_time_slots( $pickup_date );

				if ( ovabrw_array_exists( $time_slots ) ) {
					// Get time slots data
					$timeslots_data = $rental_product->get_time_slots_html( $time_slots, $timeslot_name );

					// Get time slots HTML
					$timeslots_html = ovabrw_get_meta_data( 'timeslots', $timeslots_data );

					if ( $timeslots_html ) {
						unset( $results['error'] );

						$results['timeslots_html'] = $timeslots_html;

						// Date format
						$date_format = OVABRW()->options->get_date_format();

						// Time format
						$time_format = OVABRW()->options->get_time_format();

						// Get drop-off date
						$results['dropoff_date'] = '';

						// Get end date
						$end_date = ovabrw_get_meta_data( 'end_date', $timeslots_data );

						if ( $end_date ) {
							$results['dropoff_date'] = gmdate( $date_format.' '.$time_format, $end_date );
						}
					}
				}
			}

			echo wp_json_encode( $results );

			wp_die();
		}

		/**
		 * Get time slots location
		 */
		public function ovabrw_time_slots_location() {
			// Check security
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

			// init results
			$results['error'] = esc_html__( 'There are no time slots available. Please choose another location.', 'ova-brw' );

			// Product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $_POST );

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) {
				echo wp_json_encode( $results );
				wp_die();
			}

			// Location
			$location = ovabrw_get_meta_data( 'location', $_POST );

			// Time slots data
			$timeslots_data = ovabrw_get_meta_data( 'time_slots', $_POST );

			// Get time slots
			$time_slots = ovabrw_get_meta_data( $location, $timeslots_data );

			if ( ovabrw_array_exists( $time_slots ) ) {
				// Timeslots name
				$timeslot_name = ovabrw_get_meta_data( 'timeslot_name', $_POST );

				// Get time slots data
				$timeslots_data = $rental_product->get_time_slots_html( $time_slots, $timeslot_name );

				// Get time slots HTML
				$timeslots_html = ovabrw_get_meta_data( 'timeslots', $timeslots_data );

				if ( $timeslots_html ) {
					// Timeslots HTML
					$results['timeslots_html'] = $timeslots_html;

					// Start date
					$start_date = ovabrw_get_meta_data( 'start_date', $timeslots_data );

					// Get end date
					$end_date = ovabrw_get_meta_data( 'end_date', $timeslots_data );

					if ( $start_date & $end_date ) {
						unset( $results['error'] );

						// Date format
						$date_format = OVABRW()->options->get_date_format();

						// Time format
						$time_format = OVABRW()->options->get_time_format();

						// Drop-off date
						$results['dropoff_date'] = gmdate( $date_format.' '.$time_format, $end_date );
					}
				}
			}

			echo wp_json_encode( $results );

			wp_die();
		}

		/**
		 * Add to cart
		 */
		public function ovabrw_add_to_cart() {
		    check_admin_referer( 'ovabrw-security-ajax', 'security' );

		    // Validattion
		    $passed_validation = apply_filters( OVABRW_PREFIX.'ajax_add_to_cart_validation', true, $_POST );
		    if ( !$passed_validation ) wp_die();

		    // Product ID
		    $product_id = absint( ovabrw_get_meta_data( 'product_id', $_POST ) );
		    if ( !$product_id ) wp_die();

		    // Product URL
		    $product_url = ovabrw_get_meta_data( 'product_url', $_POST );

		    // Get cart item
		    $cart_item = ovabrw_get_meta_data( 'cart_item', $_POST );
		    if ( ovabrw_array_exists( $cart_item ) ) {
		    	// Get quantity
		    	$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item, 1 );

		    	if ( false !== WC()->cart->add_to_cart( $product_id, $quantity, 0, [], $cart_item )) {
		    		// Get cart url
		    		$product_url = wc_get_cart_url();
		    	}
		    } // END if

		    echo esc_url( $product_url );

		    wp_die();
		}
	}

	new OVABRW_Ajax();
}