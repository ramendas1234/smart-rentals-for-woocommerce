<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Hooks class
 */
if ( !class_exists( 'OVABRW_Hooks', false ) ) {

	class OVABRW_Hooks {

		/**
		 * Constructs
		 */
		public function __construct() {
			// Rental product detail - Remove action
			add_action( 'init', [ $this, 'rental_product_detail_remove_action' ] );

			// Get title search results page
			add_filter( 'pre_get_document_title', [ $this, 'get_title_search_results' ] );
			add_filter( 'woocommerce_page_title', [ $this, 'get_title_search_results' ] );

			// Get search URL
			add_action( 'template_redirect', [ $this, 'get_search_url' ] );

			// Show Wysiwyg Editor
			add_filter( 'ovabrw_the_content', 'do_blocks', 9 );
			add_filter( 'ovabrw_the_content', 'wptexturize' );
			add_filter( 'ovabrw_the_content', 'convert_smilies', 20 );
			add_filter( 'ovabrw_the_content', 'wpautop' );
			add_filter( 'ovabrw_the_content', 'shortcode_unautop' );
			add_filter( 'ovabrw_the_content', 'prepend_attachment' );
			add_filter( 'ovabrw_the_content', 'wp_filter_content_tags' );
			add_filter( 'ovabrw_the_content', 'wp_replace_insecure_home_url' );
			add_filter( 'ovabrw_the_content', 'do_shortcode', 11 );

			// Template include
			add_filter( 'template_include', [ $this, 'template_include' ], 99 );

			// Support Apple and Google Pay Button
			add_filter( 'wcpay_payment_request_supported_types', [ $this, 'payment_supported_types' ] );

			// Rental product tabs
			add_filter( 'woocommerce_product_tabs', [ $this, 'rental_product_tabs' ], 11 );

			// Item rental product price
			add_action( 'woocommerce_after_shop_loop_item_title', [ $this, 'item_rental_product_price' ], 9 );

			// Item rental product featured
			add_action( 'woocommerce_after_shop_loop_item', [ $this, 'item_rental_product_featured' ], 9 );

			// Item rental product taxonomies
			add_action( 'woocommerce_after_shop_loop_item', [ $this, 'item_rental_product_taxonomies' ], 9 );

			// Item rental product attributes
			add_action( 'woocommerce_after_shop_loop_item', [ $this, 'item_rental_product_attributes' ], 8 );

			// Rental product price
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_price' ], 9 );

			// Rental product custom taxonomies
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_custom_taxonomies' ], 65 );

			// Rental product specifications
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_specifications' ], 70 );

			// Rental product featured
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_featured' ], 70 );

			// Rental product table price
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_table_price' ], 71 );

			// Rental product disabled dates
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_disabled_dates' ], 72 );

			// Rental product calendar
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_calendar' ], 73 );

			// Rental product booking form
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_booking_form' ], 74 );

			// Rental booking form fields
			add_action( 'ovabrw_booking_form', [ $this, 'rental_booking_form_fields' ], 5 );

			// Rental booking form extra fields
			add_action( 'ovabrw_booking_form', [ $this, 'rental_booking_form_extra_fields' ], 10 );

			// Rental booking form resources
			add_action( 'ovabrw_booking_form', [ $this, 'rental_booking_form_resources' ], 15 );

			// Rental booking form services
			add_action( 'ovabrw_booking_form', [ $this, 'rental_booking_form_services' ], 20 );

			// Rental booking form deposit
			add_action( 'ovabrw_booking_form', [ $this, 'rental_booking_form_deposit' ], 25 );

			// Rental booking form total
			add_action( 'ovabrw_booking_form', [ $this, 'rental_booking_form_total' ], 30 );

			// Rental weekdays table price
			add_action( 'ovabrw_table_price_weekdays', [ $this, 'rental_weekdays_table_price' ], 10 );

			// Rental discount by day
			add_action( 'ovabrw_table_price_global_discount_day', [ $this, 'rental_discount_by_day' ], 10 );

			// Rental discount by hour
			add_action( 'ovabrw_table_price_global_discount_hour', [ $this, 'rental_discount_by_hour' ], 10 );

			// Rental seasons day
			add_action( 'ovabrw_table_price_seasons_day', [ $this, 'rental_seasons_day' ], 10 );

			// Rental seasons hour
			add_action( 'ovabrw_table_price_seasons_hour', [ $this, 'rental_seasons_hour' ], 10 );

			// Rental period time
			add_action( 'ovabrw_table_price_period_time', [ $this, 'rental_period_time' ], 10 );

			// Rental request booking form
			add_action( 'ovabrw_request_booking_form', [ $this, 'rental_request_booking_form' ], 10 );

			// Add woo order status: Closed
			add_filter( 'wc_order_statuses', [ $this, 'add_woo_order_status' ] );

			// Rental product link
			add_filter( 'woocommerce_loop_product_link', [ $this, 'rental_product_link' ], 10 );

			// Rental add to cart link
			add_filter( 'woocommerce_loop_add_to_cart_link', [ $this, 'rental_add_to_cart_link' ], 10, 3 );

			// Rental cancel booking
			add_filter( 'woocommerce_valid_order_statuses_for_cancel', [ $this, 'rental_cancel_booking' ], 10, 2 );

			// Check booked orders when product hide drop-off date
			add_filter( 'ovabrw_check_equal_booking_times', [ $this, 'check_equal_booking_times' ], 10, 2 );

			// Woo webhook - Add request booking
			add_filter( 'woocommerce_webhook_topic_hooks', [ $this, 'add_webhook_request_booking' ], 10, 2 );

			// Woo valid webhook resource
			add_filter( 'woocommerce_valid_webhook_resources', [ $this, 'valid_webhook_resources' ] );

			// Woo valid webhook events
			add_filter( 'woocommerce_valid_webhook_events', [ $this, 'valid_webhook_events' ] );

			// Woo webhook - View request booking
			add_filter( 'woocommerce_webhook_topics', [ $this, 'view_webhook_request_booking' ] );

			// Woo webhook - Send data request
			add_action( 'woocommerce_webhook_payload', [ $this, 'webbook_send_data_request' ], 10, 4 );
		}

		/**
		 * Rental product detail - Remove action
		 */
		public function rental_product_detail_remove_action() {
			// Feature Image/ Gallery
		    if ( ovabrw_get_setting( 'template_feature_image', 'yes' ) !== 'yes' ) {
		        remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
		    }

		    // Title
		    if ( ovabrw_get_setting( 'template_show_title', 'yes' ) !== 'yes' ) {
		        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
		    }

		    // Price
		    if ( ovabrw_get_setting( 'template_show_price', 'yes' ) !== 'yes' ) {
		        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
		    }

		    // Meta
		    if ( ovabrw_get_setting( 'template_show_meta', 'yes' ) !== 'yes' ) {
		        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
		    }

		    // Review
		    if ( ovabrw_get_setting( 'template_show_review_product', 'yes' ) !== 'yes' ) {
		        remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
		    }

		    // Related
		    if ( ovabrw_get_setting( 'template_show_related_product', 'yes' ) !== 'yes' ) {
		        remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );
		    }
		}

		/**
		 * Get title search results page
		 */
		public function get_title_search_results( $title ) {
			// Search
			$search = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_search', $_GET ) );
			if ( 'search_item' === $search ) {
				return apply_filters( OVABRW_PREFIX.'get_title_search_results', esc_html__( 'Search Results', 'ova-brw' ), $title );
			}

			return $title;
		}

		/**
		 * Get search URL
		 */
		public function get_search_url() {
			// Search URL
			$search_url = ovabrw_get_meta_data( 'ovabrw_search_url', $_REQUEST );
			if ( $search_url ) {
				// Product name
				$product_name = sanitize_text_field( ovabrw_get_meta_data( 'product_name', $_GET ) );
				if ( $product_name ) {
					$search_url = add_query_arg( 'product_name', $product_name, $search_url );
				}

				// Pick-up location
				$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $_GET ) );
				if ( $pickup_location ) {
					$search_url = add_query_arg( 'pickup_location', $pickup_location, $search_url );
				}

				// Origin location
		    	$origin = stripslashes( stripslashes( ovabrw_get_meta_data( 'origin', $_GET ) ) );
		    	if ( $origin ) {
		    		$search_url = add_query_arg( 'origin', $origin, $search_url );
		    	}

				// Drop-off location
				$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $_GET ) );
				if ( $dropoff_location ) {
					$search_url = add_query_arg( 'dropoff_location', $dropoff_location , $search_url );
				}

				// Destination
	            $destination = stripslashes( stripslashes( ovabrw_get_meta_data( 'destination', $_GET ) ) );
	            if ( $destination ) {
	                $search_url = add_query_arg( 'destination', $destination, $search_url );
	            }

				// Pick-up date
				$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'pickup_date', $_GET ) );
				if ( $pickup_date ) {
					$search_url = add_query_arg( 'pickup_date', $pickup_date, $search_url );
				}

				// Drop-off date
				$dropoff_date = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_date', $_GET ) );
				if ( $dropoff_date ) {
					$search_url = add_query_arg( 'dropoff_date', $dropoff_date, $search_url );
				}

				// Category
				$cat = sanitize_text_field( ovabrw_get_meta_data( 'cat', $_GET ) );
				if ( $cat ) {
					$search_url = add_query_arg( 'cat', $cat, $search_url );
				}

				// Attribute
	        	$attribute = sanitize_text_field( ovabrw_get_meta_data( 'attribute', $_GET ) );

	        	// Attribute value
	        	$attribute_value = sanitize_text_field( ovabrw_get_meta_data( $attribute, $_GET ) );
	        	if ( $attribute && $attribute_value ) {
	        		$search_url = add_query_arg( 'attribute', $attribute, $search_url );
	        		$search_url = add_query_arg( $attribute, $attribute_value, $search_url );
	        	}

	        	// Package
	            $package = (int)sanitize_text_field( ovabrw_get_meta_data( 'package', $_GET ) );
	            if ( $package ) {
	                $search_url = add_query_arg( 'package', $package, $search_url );
	            }

				// Duration
				$duration = (int)sanitize_text_field( ovabrw_get_meta_data( 'duration', $_GET ) );
				if ( $duration ) {
					$search_url = add_query_arg( 'duration', $duration, $search_url );
				}

				// Distance
	            $distance = (float)sanitize_text_field( ovabrw_get_meta_data( 'distance', $_GET ) );
	            if ( $distance ) {
	                $search_url = add_query_arg( 'distance', $distance, $search_url );
	            }

	        	// Number of adults
	        	$numberof_adults = sanitize_text_field( ovabrw_get_meta_data( 'adults', $_GET ) );
	        	if ( $numberof_adults ) {
	        		$search_url = add_query_arg( 'adults', $numberof_adults, $search_url );
	        	}

	        	// Number of children
	        	$numberof_children = sanitize_text_field( ovabrw_get_meta_data( 'children', $_GET ) );
	        	if ( $numberof_children ) {
	        		$search_url = add_query_arg( 'children', $numberof_children, $search_url );
	        	}

	        	// Number of babies
	        	$numberof_babies = sanitize_text_field( ovabrw_get_meta_data( 'babies', $_GET ) );
	        	if ( $numberof_babies ) {
	        		$search_url = add_query_arg( 'babies', $numberof_babies, $search_url );
	        	}

	        	// Number of seats
	        	$seats = (int)sanitize_text_field( ovabrw_get_meta_data( 'seats', $_GET ) );
	        	if ( $seats ) {
	        		$search_url = add_query_arg( 'seats', $seats, $search_url );
	        	}

	        	// Product tag
        		$product_tag = sanitize_text_field( ovabrw_get_meta_data( 'product_tag', $_GET ) );
        		if ( $product_tag ) {
        			$search_url = add_query_arg( 'product_tag', $product_tag, $search_url );
        		}

	        	// Quantity
        		$quantity = (int)sanitize_text_field( ovabrw_get_meta_data( 'quantity', $_GET ) );
        		if ( $quantity ) {
        			$search_url = add_query_arg( 'quantity', $quantity, $search_url );
        		}

        		// Taxonomies
        		$taxonomies = ovabrw_get_option( 'custom_taxonomy', [] );
        		if ( ovabrw_array_exists( $taxonomies ) ) {
        			foreach ( $taxonomies as $slug => $taxo ) {
        				if ( 'on' !== ovabrw_get_meta_data( 'enabled', $taxo ) ) {
        					continue;
        				}

        				// Get taxo value
        				$value = sanitize_text_field( ovabrw_get_meta_data( $slug.'_name', $_GET ) );
        				if ( $value ) {
        					$search_url = add_query_arg( $slug.'_name', $value, $search_url );
        				}
        			}
        		}
				
				// Action
				$action = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_search', $_GET ) );
				if ( $action ) {
					$search_url = add_query_arg( 'ovabrw_search', $action, $search_url );
				}
				
				wp_safe_redirect( $search_url ); exit();
			}
		}

		/**
		 * Template include
		 */
		public function template_include( $template ) {
			// is search
			$is_search = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_search', $_REQUEST ) );

			// is request
	        $is_request = sanitize_text_field( ovabrw_get_meta_data( 'request_booking', $_REQUEST ) );

	        // Get product template options
	        $product_template = ovabrw_get_setting( 'template_elementor_template', 'modern' );

	        // Single Product
	        if ( is_product() ) {
	            $product_id = get_the_id();
	            $product    = wc_get_product( $product_id );

	            if ( $product && $product->is_type( OVABRW_RENTAL ) ) {
	                // Get product template
	                $prod_template = ovabrw_get_post_meta( $product_id, 'product_template' );

	                if ( $prod_template && 'global' != $prod_template ) {
	                    if ( 'default' == $prod_template ) {
	                        return $template;
	                    } elseif ( ovabrw_global_typography() && 'modern' == $prod_template ) {
	                        return ovabrw_get_template( 'modern/single/ovabrw-single-product.php' );
	                    } else {
	                        return ovabrw_get_template( 'ovabrw_single_product.php', [
	                        	'template' => $prod_template
	                        ]);
	                    }
	                }

	                // Woo Settings
	                if ( 'default' == $product_template ) {
	                    $term_template = $product->get_template();

	                    if ( $term_template && 'default' != $term_template ) {
	                        $template = ovabrw_get_template( 'ovabrw_single_product.php', [
	                        	'template' => $term_template
	                        ]);
	                    }
	                } elseif ( ovabrw_global_typography() && 'modern' == $product_template ) {
	                    $term_template = $product->get_template();

	                    if ( $term_template && 'modern' != $term_template ) {
	                        $template = ovabrw_get_template( 'ovabrw_single_product.php', [
	                        	'template' => $term_template
	                        ]);
	                    } else {
	                        $template = ovabrw_get_template( 'modern/single/ovabrw-single-product.php' );
	                    }
	                } else {
	                    $template = ovabrw_get_template( 'ovabrw_single_product.php' );
	                }
	            }
	        }

	        // Archive Product
	        if ( ovabrw_is_archive_product() && '' == $is_search ) {
	            ovabrw_get_template( 'modern/products/ovabrw-archive-product.php' );

	            return false;
	        }
	        
	        // Search Form
	        if ( '' != $is_search ) {
	            return ovabrw_get_template( 'search_result.php' );
	        }
	        
	        // Request Booking Form
	        if ( '' != $is_request ) {
	            if ( OVABRW_Mail::instance()->mail_request_booking( $_REQUEST ) ) {
	                // Webhook request booking success
	                do_action( OVABRW_PREFIX.'mail_request_booking_sent', $_REQUEST );

	                $thank_page = ovabrw_get_setting( 'request_booking_form_thank_page' );
	                if ( !$thank_page ) $thank_page = home_url();

	                $object_id  = '';

	                // Multi language
	                if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
	                    $thank_page_id  = url_to_postid( $thank_page );
	                    $object_id      = apply_filters( 'wpml_object_id', $thank_page_id, 'page', TRUE  );
	                } elseif ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ) ) {
	                    $thank_page_id  = url_to_postid( $thank_page );
	                    $object_id      = pll_get_post( $thank_page_id );
	                }

	                if ( $object_id ) {
	                    $thank_page = get_permalink( $object_id );
	                }

	                wp_safe_redirect( $thank_page );
	            } else {
	                $error_page = ovabrw_get_setting( 'request_booking_form_error_page' );
	                if ( !$error_page ) $error_page = home_url();

	                $object_id  = '';

	                // Multi language
	                if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
	                    $error_page_id  = url_to_postid( $error_page );
	                    $object_id      = apply_filters( 'wpml_object_id', $error_page_id, 'page', TRUE  );
	                } elseif ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ) ) {
	                    $error_page_id  = url_to_postid( $error_page );
	                    $object_id      = pll_get_post( $error_page_id );
	                }

	                if ( $object_id ) {
	                    $error_page = get_permalink( $object_id );
	                }
	                
	                wp_safe_redirect( $error_page );
	            }

	            exit();
	        }

	        return $template;
		}

		/**
		 * Support Apple and Google Pay Button
		 */
		public function payment_supported_types( $product_types ) {
			if ( ovabrw_array_exists( $product_types ) ) {
				array_push( $product_types, OVABRW_RENTAL );
			}

			return $product_types;
		}

		/**
		 * Rental product tabs content
		 */
		public function rental_product_tabs( $tabs ) {
			// Add Request Booking Tab
	        $product_id = get_the_id();
	        $product    = wc_get_product( $product_id );

	        if ( $product && $product->is_type( OVABRW_RENTAL ) ) {
	            $flag = true;

	            // Get product template
	            $product_template = $product->get_template();

	            if ( 'global' === $product_template ) {
	                $product_template = ovabrw_get_setting( 'template_elementor_template', 'modern' );

	                if ( ovabrw_global_typography() && 'modern' === $product_template ) {
	                    $term_template = $product->get_template();

	                    if ( $term_template && 'modern' !== $term_template ) {
	                        $flag = true;
	                    } else {
	                        $flag = false;
	                    }
	                }
	            } elseif ( 'modern' === $product_template ) {
	            	$flag = false;
	            }

	            // Add request booking tab
	            if ( 'yes' == ovabrw_get_setting( 'template_show_request_booking', 'yes' ) && apply_filters( OVABRW_PREFIX.'show_request_booking_in_product_tabs', $flag ) ) {
	                $tabs['ovabrw_reqest_booking'] = [
	                	'title'     => esc_html__( 'Request for booking', 'ova-brw' ),
	                    'priority'  => (int)ovabrw_get_setting( 'request_booking_form_order_tab', 9 ),
	                    'callback'  => [ $this, 'rental_request_booking_tab' ]
	                ];
	            }

	            // Add Extra Tab
	            $extra_tab = $product->get_meta_value( 'manage_extra_tab' );

	            switch( $extra_tab ) {
	                case 'in_setting' : {
	                    $short_code_form = ovabrw_get_setting( 'extra_tab_shortcode_form' );
	                    break;
	                }
	                case 'new_form' : {
	                    $short_code_form = $product->get_meta_value( 'extra_tab_shortcode' );
	                    break;
	                }
	                case 'no' : {
	                    $short_code_form = '';
	                    break;
	                }
	                default: {
	                    $short_code_form = ovabrw_get_setting( 'extra_tab_shortcode_form' );
	                    break;
	                }
	            }

	            // Extra tab
	            if ( 'yes' == get_option( 'ova_brw_template_show_extra_tab', 'yes' ) && $short_code_form != ''   ) {
	                $tabs['ovabrw_extra_tab'] = [
	                	'title'     => esc_html__( 'Extra Tab', 'ova-brw' ),
	                    'priority'  => (int)get_option( 'ova_brw_extra_tab_order_tab', 21 ),
	                    'callback'  => [ $this, 'rental_product_extra_tab' ]
	                ];
	            }

	            // Product place
	            if ( 'yes' === ovabrw_get_setting( 'template_show_place', 'yes' ) && ovabrw_get_setting( 'google_key_map' ) ) {
	                $latitude   = $product->get_meta_value( 'latitude' );
	                $longitude  = $product->get_meta_value( 'longitude' );
	                $api_key 	= ovabrw_get_setting( 'google_key_map', false );

	                if ( $latitude && $longitude && $api_key ) {
	                    // Enqueue Map
	                    wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&loading=async&callback=Function.prototype&libraries=places', false, true );

	                    $tabs['ovabrw_product_place'] = [
	                    	'title'     => esc_html__( 'Place', 'ova-brw' ),
	                        'priority'  => (int)ovabrw_get_setting( 'product_place_priority', 22 ),
	                        'callback'  => [ $this, 'rental_product_place_tab' ]
	                    ];
	                }
	            }

	            // Product review tab
	            if ( 'yes' !== ovabrw_get_setting( 'template_show_review_product', 'yes' ) && isset( $tabs['reviews'] ) ) {
	            	unset( $tabs['reviews'] );
	            }
	        }

	        return $tabs;
		}

		/**
		 * Rental request booking tab
		 */
		public function rental_request_booking_tab() {
			return ovabrw_get_template( 'single/request_booking.php' );
		}

		/**
		 * Rental product extra tab
		 */
		public function rental_product_extra_tab() {
			return ovabrw_get_template( 'single/contact_form.php' );
		}

		/**
		 * Rental product place tab
		 */
		public function rental_product_place_tab() {
			global $product;

	        if ( $product && $product->is_type( OVABRW_RENTAL ) ) {
	            $product_id = $product->get_id();
	            $zoom       = (int)ovabrw_get_setting( 'google_map_zoom', 17 );
	            $address    = $product->get_meta_value( 'address' );
	            $latitude   = $product->get_meta_value( 'latitude' );
	            $longitude  = $product->get_meta_value( 'longitude' );

	            // Check latitude & longitude
	            if ( !$latitude || !$longitude ) return;

	            return ovabrw_get_template( 'modern/single/detail/ovabrw-product-place.php', [
	            	'product_id'    => $product_id,
	                'zoom'          => $zoom,
	                'latitude'      => $latitude,
	                'longitude'     => $longitude,
	                'address'       => $address
	            ]);
	        }
		}

		/**
		 * Item rental product price
		 */
		public function item_rental_product_price() {
			return ovabrw_get_template( 'loop/price.php' );
		}

		/**
		 * Item rental product featured
		 */
		public function item_rental_product_featured() {
			if ( 'yes' == ovabrw_get_setting( 'archive_product_show_features', 'yes' ) ) {
	            return ovabrw_get_template( 'loop/featured.php' );
	        }
		}

		/**
		 * Item rental product taxonomies
		 */
		public function item_rental_product_taxonomies() {
			return ovabrw_get_template( 'loop/taxonomy.php' );
		}

		/**
		 * Item rental product attributes
		 */
		public function item_rental_product_attributes() {
			if ( 'yes' === ovabrw_get_setting( 'archive_product_show_attribute', 'yes' ) ) {
	            return ovabrw_get_template( 'loop/attributes.php' );
	        }
		}

		/**
		 * Rental product price
		 */
		public function rental_product_price() {
			if ( 'yes' === ovabrw_get_setting( 'template_show_price', 'yes' ) ) {
	            return ovabrw_get_template( 'single/price.php' );
	        }
		}

		/**
		 * Rental product custom taxonomies
		 */
		public function rental_product_custom_taxonomies() {
			if ( 'yes' === ovabrw_get_setting( 'template_show_cus_tax', 'yes' ) ) {
	            return ovabrw_get_template( 'single/custom_taxonomy.php' );
	        }
		}

		/**
		 * Rental product specifications
		 */
		public function rental_product_specifications() {
			if ( 'yes' === ovabrw_get_setting( 'template_show_specifications', 'yes' ) ) {
	            return ovabrw_get_template( 'single/specifications.php' );
	        }
		}

		/**
		 * Rental product featured
		 */
		public function rental_product_featured() {
			if ( 'yes' === ovabrw_get_setting( 'template_show_feature', 'yes' ) ) {
	            return ovabrw_get_template( 'single/features.php' );
	        }
		}

		/**
		 * Rental product table price
		 */
		public function rental_product_table_price() {
			if ( 'yes' == ovabrw_get_setting( 'template_show_table_price', 'yes' ) ) {
	            return ovabrw_get_template( 'single/table_price.php' );
	        }
		}

		/**
		 * Rental product disabled dates
		 */
		public function rental_product_disabled_dates() {
			if ( 'yes' == ovabrw_get_setting( 'template_show_maintenance', 'yes' ) ) {
	            return ovabrw_get_template( 'single/unavailable_time.php' );
	        }
		}

		/**
		 * Rental product calendar
		 */
		public function rental_product_calendar() {
			if ( 'yes' == ovabrw_get_setting( 'template_show_calendar', 'yes' ) ) { 
	            return ovabrw_get_template( 'single/calendar.php' );
	        }
		}

		/**
		 * Rental product booking form
		 */
		public function rental_product_booking_form() {
			if ( 'yes' == ovabrw_get_setting( 'template_show_booking_form', 'yes' ) ) { 
	            return ovabrw_get_template( 'single/booking-form.php' );
	        }
		}

		/**
		 * Rental booking form fields
		 */
		public function rental_booking_form_fields( $product_id ) {
			return ovabrw_get_template( 'single/booking-form/fields.php', [
	            'product_id' => $product_id
	        ]);
		}

		/**
		 * Rental booking form extra fields
		 */
		public function rental_booking_form_extra_fields( $product_id ) {
			return ovabrw_get_template( 'single/booking-form/extra_fields.php', [
	            'product_id' => $product_id
	        ]);
		}

		/**
		 * Rental booking form resources
		 */
		public function rental_booking_form_resources( $product_id ) {
			if ( 'yes' === ovabrw_get_setting( 'booking_form_show_extra_resource', 'yes' ) ) {
	            return ovabrw_get_template( 'single/booking-form/resource.php', [
	                'product_id' => $product_id
	            ]);
	        }
		}

		/**
		 * Rental booking form services
		 */
		public function rental_booking_form_services( $product_id ) {
			if ( 'yes' === ovabrw_get_setting( 'booking_form_show_extra_service', 'yes' ) ) {
	            return ovabrw_get_template( 'single/booking-form/services.php', [
	                'product_id' => $product_id
	            ]);
	        }
		}

		/**
		 * Rental booking form deposit
		 */
		public function rental_booking_form_deposit( $product_id ) {
			$enable_deposit = ovabrw_get_post_meta ( $product_id, 'enable_deposit' );

	        if ( 'yes' === $enable_deposit ) {
	            return ovabrw_get_template( 'single/booking-form/deposit.php', [
	                'product_id' => $product_id
	            ]);
	        }
	        
	        return;
		}

		/**
		 * Rental booking form total
		 */
		public function rental_booking_form_total( $product_id ) {
			return ovabrw_get_template( 'single/booking-form/ajax_total.php', [
	            'product_id' => $product_id
	        ]);
		}

		/**
		 * Rental weekdays table price
		 */
		public function rental_weekdays_table_price( $product_id ) {
			$product = ovabrw_get_rental_product([
				'product_id' => $product_id
			]);

			if ( $product ) {
				// Get rental type
				$rental_type = $product->get_rental_type();

				// Daily prices
				$monday 	= $product->get_meta_value( 'daily_monday' );
				$tuesday 	= $product->get_meta_value( 'daily_tuesday' );
				$wednesday 	= $product->get_meta_value( 'daily_wednesday' );
				$thursday 	= $product->get_meta_value( 'daily_thursday' );
				$friday 	= $product->get_meta_value( 'daily_friday' );
				$saturday 	= $product->get_meta_value( 'daily_saturday' );
				$sunday 	= $product->get_meta_value( 'daily_sunday' );

				if ( in_array( $rental_type, ['day', 'mixed', 'hotel'] ) && $monday && $tuesday && $wednesday && $thursday && $friday && $saturday && $sunday ) {
					return ovabrw_get_template( 'single/table-price/weekdays.php', [
		                'product_id' => $product->get_id()
		            ]);
				}
			}
		}

		/**
		 * Rental discount by day
		 */
		public function rental_discount_by_day( $product_id ) {
			return ovabrw_get_template( 'single/table-price/global_discount_day.php', [
	            'product_id' => $product_id
	        ]);
		}

		/**
		 * Rental discount by hour
		 */
		public function rental_discount_by_hour( $product_id ) {
			return ovabrw_get_template( 'single/table-price/global_discount_hour.php', [
	            'product_id' => $product_id
	        ]);
		}

		/**
		 * Rental seasons day
		 */
		public function rental_seasons_day( $product_id ) {
			return ovabrw_get_template( 'single/table-price/seasons_day.php', [
	            'product_id' => $product_id
	        ]);
		}

		/**
		 * Rental seasons hour
		 */
		public function rental_seasons_hour( $product_id ) {
			return ovabrw_get_template( 'single/table-price/seasons_hour.php', [
	            'product_id' => $product_id
	        ]);
		}

		/**
		 * Rental period time
		 */
		public function rental_period_time( $product_id ) {
			return ovabrw_get_template( 'single/table-price/period_time.php', [
	            'product_id' => $product_id
	        ]);
		}

		/**
		 * Rental request booking form
		 */
		public function rental_request_booking_form() {
			return ovabrw_get_template( 'single/request_booking.php' );
		}

		/**
		 * Add woo order status: Closed
		 */
		public function add_woo_order_status( $order_statuses ) {
			$order_statuses['wc-closed'] = _x( 'Closed', 'Order status', 'ova-brw' );

        	return $order_statuses;
		}

		/**
		 * Rental product link
		 */
		public function rental_product_link( $product_link ) {
			if ( isset( $_GET['ovabrw_search'] ) ) {
	            if ( !empty( $_GET['ovabrw_pickup_date'] ) ) {
	                $product_link = add_query_arg( 'pickup_date', $_GET['ovabrw_pickup_date'], $product_link );
	            }
	            if ( !empty( $_GET['ovabrw_pickoff_date'] ) ) {
	                $product_link = add_query_arg( 'dropoff_date', $_GET['ovabrw_pickoff_date'], $product_link );
	            }
	            if ( !empty( $_GET['ovabrw_pickup_loc'] ) ) {
	                $product_link = add_query_arg( 'pickup_loc', $_GET['ovabrw_pickup_loc'], $product_link );
	            }
	            if ( !empty( $_GET['ovabrw_pickoff_loc'] ) ) {
	                $product_link = add_query_arg( 'pickoff_loc', $_GET['ovabrw_pickoff_loc'], $product_link );
	            }
	        }

	        return $product_link;
		}

		/**
		 * Rental add to cart link
		 */
		public function rental_add_to_cart_link( $link, $product, $args ) {
			$cart_url 	= $product->add_to_cart_url();
			$cart_text 	= $product->add_to_cart_text();

			// Quantity
			$quantity = (int)ovabrw_get_meta_data( 'quantity', $args, 1 );

			// Class
			$class = ovabrw_get_meta_data( 'class', $args, 'button' );

			// Attributes
			$attributes = ovabrw_get_meta_data( 'attributes', $args );
			$attributes = ovabrw_array_exists( $attributes ) ? wc_implode_html_attributes( $attributes ) : '';

			if ( ovabrw_get_meta_data( 'ovabrw_search', $_GET ) ) {
	            if ( ovabrw_get_meta_data( 'ovabrw_pickup_date', $_GET ) ) {
	                $cart_url = add_query_arg( 'pickup_date', $_GET['ovabrw_pickup_date'], $cart_url );
	            }
	            if ( ovabrw_get_meta_data( 'ovabrw_pickoff_date', $_GET ) ) {
	                $cart_url = add_query_arg( 'dropoff_date', $_GET['ovabrw_pickoff_date'], $cart_url );
	            }
	            if ( ovabrw_get_meta_data( 'ovabrw_pickup_loc', $_GET ) ) {
	                $cart_url = add_query_arg( 'pickup_loc', $_GET['ovabrw_pickup_loc'], $cart_url );
	            }
	            if ( ovabrw_get_meta_data( 'ovabrw_pickoff_loc', $_GET ) ) {
	                $cart_url = add_query_arg( 'pickoff_loc', $_GET['ovabrw_pickoff_loc'], $cart_url );
	            }
	        }
	        
	        return sprintf(
	            '<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
	            esc_url( $cart_url ),
	            $quantity,
	            $class,
	            $attributes,
	            $cart_text
	        );
		}

		/**
		 * Rental cancel booking
		 */
		public function rental_cancel_booking( $status, $order ) {
			$order_status_can_cancel = $time_can_cancel = $other_condition = $total_order_valid = true;

			if ( in_array( $order->get_status(), [ 'pending', 'failed' ] ) ) {
	            return [ 'pending', 'failed' ];
	        }

	        // Check order status can order
	        if ( !in_array( $order->get_status(), apply_filters( OVABRW_PREFIX.'order_status_can_cancel', [ 'completed', 'processing', 'on-hold', 'pending', 'failed' ] ) ) ) {
	            $order_status_can_cancel = false;
	        }
	        
	        // Validate before x hours can cancel
	        // Get Meta Data type line_item of Order
	        $order_line_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );

	        foreach ( $order_line_items as $item_id => $item ) {
	            $product_id = $item->get_product_id();
	            $product    = wc_get_product( $product_id );

	            if ( !$product ) continue;

	            $cancel_valid_minutes   = floatval( ovabrw_get_setting( 'cancel_before_x_hours', 0 ) );
	            $cancel_valid_total     = floatval( ovabrw_get_setting( 'cancel_condition_total_order', 1 ) );

	            // Check if product type is rental
	            if ( $product->is_type( OVABRW_RENTAL ) ) {
	                // Get value of pickup date, pickoff date
	                if ( $item && is_object( $item ) ) {
	                    $ovabrw_pickup_date = strtotime( $item->get_meta('ovabrw_pickup_date') );

	                    if ( ! ( $ovabrw_pickup_date > current_time( 'timestamp' ) && $ovabrw_pickup_date - current_time( 'timestamp' ) > $cancel_valid_minutes*60*60  ) ) {
	                       $time_can_cancel = false;
	                       break;
	                    }
	                }
	            }
	        }

	        // Cancel by total order
	        if ( empty( $cancel_valid_total ) ) {
	            $total_order_valid = true;
	        } else if ( $order->get_total() > floatval( $cancel_valid_total ) ) {
	            $total_order_valid = false;
	        }
	        
	        // Other condition
	        $other_condition = apply_filters( OVABRW_PREFIX.'other_condition_to_cancel_order', true, $order );
	        if ( $order_status_can_cancel && $time_can_cancel && $total_order_valid && $other_condition ) {
	            return [ 'completed', 'processing', 'on-hold', 'pending', 'failed' ];
	        } else {
	            return [];
	        }
		}

		/**
		 * Check booked orders when product hide drop-off date
		 */
		public function check_equal_booking_times( $result, $product_id ) {
			// Get product
			$product = wc_get_product( $product_id );

			if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) return $result;

			// Show drop-off date
			$show_dropoff_date = $product->show_date_field( 'dropoff' );
			if ( !$show_dropoff_date ) return true;

			return $result;
		}

		/**
		 * Woo webhook - Add request booking
		 */
		public function add_webhook_request_booking( $topic_hooks, $wc_webhook ) {
			$topic_hooks['ovabrw.request_booking'] = [
		        'ovabrw_mail_request_booking_sent'
		    ];
		    return $topic_hooks;
		}

		/**
		 * Woo valid webhook resource
		 */
		public function valid_webhook_resources( $valid_resources ) {
			$valid_resources[] = 'ovabrw';
    		return $valid_resources;
		}

		/**
		 * Woo valid webhook events
		 */
		public function valid_webhook_events( $valid_events ) {
			$valid_events[] = 'request_booking';
    		return $valid_events;
		}

		/**
		 * View webhook request booking
		 */
		public function view_webhook_request_booking( $topics ) {
			$topics['ovabrw.request_booking'] = esc_html__( 'Booking request successful', 'ova-brw' );
    		return $topics;
		}

		/**
		 * Woo webhook - Send data request
		 */
		public function webbook_send_data_request( $payload, $resource, $resource_id, $id ) {
			if ( 'ovabrw' === $resource ) $payload = $resource_id;
		    return $payload;
		}
	}

	new OVABRW_Hooks();
}