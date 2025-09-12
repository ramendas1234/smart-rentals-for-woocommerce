<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Rental Product class.
 */
if ( !class_exists( 'WC_Product_Ovabrw_Car_Rental' ) ) {

	class WC_Product_Ovabrw_Car_Rental extends WC_Product {

		/**
		 * Prefix product
		 */
		protected $prefix = OVABRW_PREFIX.'product_';

		/**
		 * Init rental product
		 */
		public function __construct( $product = 0 ) {
	        parent::__construct( $product );
	    }

	    /**
	     * Get product type
	     */
	    public function get_type() {
	        return OVABRW_RENTAL;
	    }

	    /**
	     * Get tour meta key
	     */
	    public function get_meta_key( $key = '' ) {
	        if ( $key ) $key = OVABRW_PREFIX.$key;

	        return apply_filters( $this->prefix.'get_meta_key', $key );
	    }

	    /**
	     * Get meta value by key
	     */
	    public function get_meta_value( $key = '', $default = false ) {
	        $value = $this->get_meta( $this->get_meta_key( $key ) );

	        if ( !$value && $default !== false ) $value = $default;

	        return apply_filters( $this->prefix.'get_meta_value', $value, $key, $default, $this );
	    }

	    /**
	     * Get rental type
	     */
	    public function get_rental_type() {
	    	return $this->get_meta_value( 'price_type' );
	    }

	    /**
	     * is rental type
	     */
	    public function is_rental_type( $type ) {
	    	if ( $type === $this->get_rental_type() ) return true;
	    	return false;
	    }

	    /**
	     * Get template
	     */
	    public function get_template() {
	    	// Global
	    	$template = ovabrw_get_setting( 'template_elementor_template', 'modern' );

	    	// Get categories
	    	$categories = $this->get_category_ids();

	    	if ( ovabrw_array_exists( $categories ) ) {
		        $term_id 		= reset( $categories );
		        $term_template 	= get_term_meta( $term_id, 'ovabrw_product_templates', true );

		        if ( $term_template && $term_template != 'global' ) {
		        	$template = $term_template;
		        }
		    }

		    // Multi language
			$object_id = '';

	        if ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
	            $object_id = apply_filters( 'wpml_object_id', $template, 'elementor_library', TRUE  );
	        } elseif ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ) ) {
	            $object_id = pll_get_post( $template );
	        }

	        if ( $object_id ) {
	        	$template = $object_id;
	        }

			return apply_filters( $this->prefix.'get_current_product_template', $template, $this );
	    }

	    /**
	     * Get charged by
	     */
	    public function get_charged_by() {
	    	if ( $this->is_rental_type( 'day' ) ) {
	    		return apply_filters( $this->prefix.'product_get_charged_by', $this->get_meta_value( 'define_1_day' ), $this );
	    	}
	    	
	    	return false;
	    }

	    /**
	     * Show date field
	     */
	    public function show_date_field( $type = 'pickup', $form = 'booking' ) {
	    	// init
	    	$result = true;

	    	// Show date
	    	$product_show_date = $global_show_date = '';

	    	if ( 'dropoff' == $type ) {
	    		if ( $this->is_rental_type( 'transportation' ) ) {
	    			$product_show_date = 'no';

	    			if ( 'yes' === $this->get_meta_value( 'dropoff_date_by_setting' ) ) {
	    				$product_show_date = $this->get_meta_value( 'show_pickoff_date_product', 'in_setting' );

	    				if ( 'booking' === $form ) {
	    					$global_show_date = ovabrw_get_setting( 'booking_form_show_dropoff_date', 'yes' );
	    				} elseif ( 'request' === $form ) {
	    					$global_show_date = ovabrw_get_setting( 'request_booking_form_show_pickoff_date', 'yes' );
	    				}
	    			}
	    		} else {
	    			$product_show_date = $this->get_meta_value( 'show_pickoff_date_product', 'in_setting' );

	    			if ( 'booking' === $form ) {
    					$global_show_date = ovabrw_get_setting( 'booking_form_show_dropoff_date', 'yes' );
    				} elseif ( 'request' === $form ) {
    					$global_show_date = ovabrw_get_setting( 'request_booking_form_show_pickoff_date', 'yes' );
    				}
	    		}
	    	}

	    	// Check show date field
	    	if ( 'no' === $product_show_date || ( 'in_setting' === $product_show_date && 'yes' !== $global_show_date ) ) {
	    		$result = false;
	    	}

			return apply_filters( $this->prefix.'show_date_field', $result, $type, $form, $this );
	    }

	    /**
	     * Show location field
	     */
	    public function show_location_field( $type = 'pickup', $form = 'booking' ) {
	    	// init
	    	$result = true;

	    	// Rental type
	    	$rental_type = $this->get_rental_type();

	    	// For rental type: hotel
	    	if ( 'hotel' === $rental_type ) {
	    		$result = false;
	    	} elseif ( !in_array( $rental_type , [ 'transportation', 'taxi' ] ) ) {
	    		// From category
		    	$category_ids 		= $this->get_category_ids();
		    	$category_location 	= ovabrw_array_exists( $category_ids ) ? get_term_meta( reset( $category_ids ), 'ovabrw_show_loc_booking_form', true ) : [];

		    	// Show location
		    	$product_show_location = $show_location = '';

		    	if ( 'pickup' === $type ) {
		    		// From product
		    		$product_show_location = $this->get_meta_value( 'show_pickup_location_product', 'in_setting' );

		    		if ( ovabrw_array_exists( $category_location ) && in_array( 'pickup_loc', $category_location ) ) {
						$show_location = 'yes';
					} elseif ( ovabrw_array_exists( $category_location ) && !in_array( 'pickup_loc', $category_location ) ) {
						$show_location = 'no';
					} else {
						if ( 'booking' === $form ) {
							$show_location = ovabrw_get_setting( 'booking_form_show_pickup_location', 'no' );
						} elseif ( 'request' ) {
							$show_location = ovabrw_get_setting( 'request_booking_form_show_pickup_location', 'no' );
						}
					}
		    	} elseif ( 'dropoff' === $type ) {
		    		// From product
		    		$product_show_location = $this->get_meta_value( 'show_pickoff_location_product', 'in_setting' );
				
					if ( ovabrw_array_exists( $category_location ) && in_array( 'dropoff_loc', $category_location ) ) {
						$show_location = 'yes';
					} elseif ( ovabrw_array_exists( $category_location ) && !in_array( 'dropoff_loc', $category_location ) ) {
						$show_location = 'no';
					} else {
						if ( 'booking' === $form ) {
							$show_location = ovabrw_get_setting( 'booking_form_show_pickoff_location', 'no' );
						} elseif ( 'request' ) {
							$show_location = ovabrw_get_setting( 'request_booking_form_show_pickoff_location', 'no' );
						}
					}
		    	}

		    	if ( 'no' === $product_show_location || ( 'in_setting' === $product_show_location && 'no' === $show_location ) ) {
		    		$result = false;
		    	}
	    	}

	    	return apply_filters( $this->prefix.'show_location_field', $result, $type, $form, $this );
	    }

	    /**
	     * Show quantity
	     */
	    public function show_quantity( $form = 'booking' ) {
	    	// init
	    	$result = false;

	    	// Show quantity
	    	$show_quantity = $this->get_meta_value( 'show_number_vehicle' );

	    	// Global show quantity
	    	$global_show_quantity = '';
	    	if ( 'booking' === $form ) {
	    		$global_show_quantity = ovabrw_get_setting( 'booking_form_show_number_vehicle', 'yes' );
	    	} elseif ( 'request' === $form ) {
	    		$global_show_quantity = ovabrw_get_setting( 'booking_form_show_number_vehicle', 'yes' );
	    	}

	    	switch ( $show_quantity ) {
				case 'in_setting':
					if ( 'yes'  == $global_show_quantity ) {
						$result = true;
					} else {
						$result = false;
					}
					break;
				case 'yes':
					$result = true;
					break;
				case 'no':
					$result = false;
					break;
				default:
					break;
			}

			return apply_filters( $this->prefix.'show_quantity', $result, $form, $this );
	    }

	    /**
	     * Get manage store
	     */
	    public function get_manage_store() {
	    	return apply_filters( $this->prefix.'get_manage', $this->get_meta_value( 'manage_store', 'store' ), $this );
	    }

	    /**
	     * Get quantity
	     */
	    public function get_number_quantity() {
	    	// init
	    	$quantity = 0;

	    	if ( $this->is_rental_type( 'appointment' ) ) {
	    		$quantity = 1;
	    	} else {
	    		// Manage store
		    	$manage_store = $this->get_manage_store();

		    	if ( 'store' === $manage_store ) {
		    		$quantity = (int)$this->get_meta_value( 'car_count' );
		    	} elseif ( 'id_vehicle' === $manage_store ) {
		    		$vehicle_ids = $this->get_meta_value( 'id_vehicles' );
	                if ( ovabrw_array_exists( $vehicle_ids ) ) {
	                	$quantity = count( $vehicle_ids );
	                }
		    	}
	    	}

	    	return apply_filters( $this->prefix.'get_number_quantity', $quantity, $this );
	    }

	    /**
	     * Show other location
	     */
	    public function show_other_location( $type = 'pickup' ) {
	    	// init
	    	$show_other_location = false;

	    	if ( !$this->is_rental_type( 'transportation' ) ) {
	    		if ( 'pickup' === $type && 'yes' === $this->get_meta_value( 'show_other_location_pickup_product' ) ) {
	    			$show_other_location = true;
	    		} elseif ( 'dropoff' === $type && 'yes' === $this->get_meta_value( 'show_other_location_dropoff_product' ) ) {
	    			$show_other_location = true;
	    		}
	    	}

	    	return apply_filters( $this->prefix.'show_other_location', $show_other_location, $type, $this );
	    }

	    /**
	     * Get HTML location
	     */
	    public function get_html_location( $type = 'pickup', $name = '', $class = '', $selected = '' ) {
	    	if ( !$name ) return '';

	    	// Get couple location
	    	$couple_location = $this->get_couple_location();
	    	if ( ovabrw_array_exists( $couple_location ) ) $class .= ' autocomplete-location';

	    	// HTML
	    	$html = '<select name="' . esc_attr( $name ) . '" class="' . esc_attr( $class ) . '">';
	    		$html .= '<option value="">' . esc_html__( 'Select Location', 'ova-brw' ) . '</option>';

	    	if ( ovabrw_array_exists( $couple_location ) ) {
	    		foreach ( $couple_location as $pickup => $dropoff ) {
	    			if ( 'pickup' === $type ) {
	    				$html .= '<option data-dropoff="' . esc_attr( json_encode( $dropoff ) ) . '" value="' . esc_attr( $pickup ) . '" ' . ovabrw_selected( $pickup, $selected, false ) . '>' . esc_html( $pickup ) . '</option>';
	    			} elseif ( 'dropoff' === $type ) {
	    				// do nothing...
	    			}
	    		}
	    	} else {
	    		// Get location ids
            	$location_ids = OVABRW()->options->get_location_ids();

            	if ( ovabrw_array_exists( $location_ids ) ) {
            		foreach ( $location_ids as $location_id ) {
            			// Title
            			$location_title = get_the_title( $location_id );

            			if ( $location_title ) {
            				$location_title = trim( $location_title );

            				$html .= '<option value="' . esc_attr( $location_title ) . '" ' . ovabrw_selected( $location_title, $selected, false ) . '>' . esc_html( $location_title ) . '</option>';
            			}
            		}
            	}
	    	}

	    	// Other location
	    	$other_location = $this->show_other_location( $type );
	    	if ( $other_location ) {
	    		$html .= '<option value="other_location" ' . ovabrw_selected( 'other_location', $selected, false ) . '>' . esc_html__( 'Other Location', 'ova-brw' ) . '</option>';
	    	}

	    	$html .= '</select>'; // END HTML

	    	return apply_filters( $this->prefix.'get_html_location', $html, $type, $name, $class, $selected, $this );
	    }

	    /**
	     * Get couple location
	     */
	    public function get_couple_location() {
	    	// Couple location
	    	$couple_location = [];

	    	// Pick-up location & Drop-off location
	    	$pickup_location = $dropoff_location = [];

	    	if ( $this->is_rental_type( 'transportation' ) ) {
	    		$pickup_location 	= $this->get_meta_value( 'pickup_location' );
	    		$dropoff_location 	= $this->get_meta_value( 'dropoff_location' );
	    	} else {
	    		$pickup_location 	= $this->get_meta_value( 'st_pickup_loc' );
	    		$dropoff_location 	= $this->get_meta_value( 'st_dropoff_loc' );
	    	}

	    	// Loop
	    	if ( ovabrw_array_exists( $pickup_location ) ) {
	    		foreach ( $pickup_location as $k => $pickup ) {
	    			$pickup  = $pickup ? trim( $pickup ) : '';
	    			$dropoff = trim( ovabrw_get_meta_data( $k, $dropoff_location ) );

	    			if ( $pickup && $dropoff ) {
	    				$couple_location[$pickup][] = $dropoff;
	    				$couple_location[$pickup] 	= array_unique( $couple_location[$pickup] );
	    			}
	    		}
	    	} // END loop

	    	return apply_filters( $this->prefix.'get_couple_location', $couple_location, $this );
	    }

	    /**
	     * Get date label
	     */
	    public function get_date_label( $type = 'pickup' ) {
	    	// init
	    	$date_label = '';

	    	if ( 'pickup' === $type ) {
	    		// Label type
	    		$label_type = $this->get_meta_value( 'label_pickup_date_product' );

	    		if ( 'new' === $label_type ) {
	    			$date_label = $this->get_meta_value( 'new_pickup_date_product' );
	    		} elseif ( 'category' === $label_type ) {
	    			$terms = wp_get_post_terms( $this->id, 'product_cat', [ 'fields'=>'ids' ] );

	                if ( ovabrw_array_exists( $terms ) ) {
	                    $term_id 	= reset( $terms );
	                    $date_label = get_term_meta( $term_id, ovabrw_meta_key( 'lable_pickup_date' ), true );
	                }
	    		}

	    		if ( !$date_label ) {
	    			$date_label = esc_html__( 'Pick-up Date', 'ova-brw' );

                	if ( $this->is_rental_type( 'hotel' ) ) {
                		$date_label = esc_html__( 'Check in', 'ova-brw' );
                	}
	    		}
	    	} elseif ( 'dropoff' === $type ) {
	    		// Label type
	    		$label_type = $this->get_meta_value( 'label_dropoff_date_product' );

	    		if ( 'new' === $label_type ) {
	    			$date_label = $this->get_meta_value( 'new_dropoff_date_product' );
	    		} elseif ( 'category' === $label_type ) {
	    			$terms = wp_get_post_terms( $this->id, 'product_cat', [ 'fields'=>'ids' ] );

	                if ( ovabrw_array_exists( $terms ) ) {
	                    $term_id 	= reset( $terms );
	                    $date_label = get_term_meta( $term_id, ovabrw_meta_key( 'lable_dropoff_date' ), true );
	                }
	    		}

	    		if ( !$date_label ) {
	    			$date_label = esc_html__( 'Drop-off Date', 'ova-brw' );

                	if ( $this->is_rental_type( 'hotel' ) ) {
                		$date_label = esc_html__( 'Check out', 'ova-brw' );
                	}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_date_label', $date_label, $type, $this );
	    }

	    /**
	     * Has time picker
	     */
	    public function has_timepicker( $type = 'pickup' ) {
	    	// init
	    	$timepicker = false;

	    	// Rental type
	    	$rental_type = $this->get_rental_type();

	    	// Get time group
    		$time_group = 'no';
    		if ( 'pickup' === $type ) {
	    		$time_group = $this->get_meta_value( 'manage_time_book_start' );
    		} elseif ( 'dropoff' === $type ) {
    			$time_group = $this->get_meta_value( 'manage_time_book_end' );
    		}

    		// Check by rental type
    		if ( 'no' != $time_group ) {
    			if ( 'day' === $rental_type ) {
    				if ( 'hotel' != $this->get_charged_by() ) $timepicker = true;
    			} elseif ( in_array( $rental_type, [ 'hour', 'mixed', 'transportation', 'appointment', 'taxi' ] ) ) {
    				$timepicker = true;
    			} elseif ( 'period_time' === $rental_type ) {
    				// Unfixed time
    				$unfixed_time = $this->get_meta_value( 'unfixed_time' );
    				if ( 'yes' === $unfixed_time ) $timepicker = true;
    			}
    		}

	    	return apply_filters( $this->prefix.'has_timepicker', $timepicker, $type, $this );
	    }

	    /**
	     * Get rental time
	     */
	    public function get_rental_time( $from_date, $to_date ) {
	    	if ( !$from_date || !$to_date ) return false;

	    	// Rental time
	    	$rental_time = [];

	    	if ( 'day' === $this->get_charged_by() ) {
				$from_date 	= strtotime( gmdate( 'Y-m-d', $from_date ) );
        		$to_date 	= strtotime( gmdate( 'Y-m-d', $to_date ) ) + 86400 - 1;
			} elseif ( 'hotel' === $this->get_charged_by() ) {
				$from_date 	= strtotime( gmdate( 'Y-m-d', $from_date ) );
        		$to_date 	= strtotime( gmdate( 'Y-m-d', $to_date ) ) ;
			}

			$rental_time['numberof_rental_days'] 	= ceil( ( $to_date - $from_date ) / 86400 );
			$rental_time['numberof_rental_hours'] 	= ceil( ( $to_date - $from_date ) / 3600 );

			return apply_filters( $this->prefix.'get_rental_time', $rental_time, $from_date, $to_date );
	    }

	    /**
	     * Get price html from search
	     */
	    public function get_price_html_from_search( $data = [] ) {
	    	// init
	    	$price_html = '';

	    	// Pick-up date
            $pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $data ) );

            // Drop-off data
            $dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $data ) );

            // Duration
            $duration = ovabrw_get_meta_data( 'duration', $data );
            if ( $pickup_date && $duration ) {
                $dropoff_date = $pickup_date + $duration;
            }

            // Distance
            $distance = ovabrw_get_meta_data( 'distance', $data );

            if ( $pickup_date && $dropoff_date ) {
                // Pick-up location
                $pickup_location = ovabrw_get_meta_data( 'pickup_location', $data );

                // Drop-off location
                $dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $data );

                // Rental product
                $rental_product = OVABRW()->rental->get_rental_product( $this->get_id() );

                if ( $rental_product ) {
                    // Get line total
                    $line_total = $rental_product->get_total([
                        'pickup_date' 		=> $pickup_date,
                        'dropoff_date' 		=> $dropoff_date,
                        'pickup_location' 	=> $pickup_location,
                        'dropoff_location' 	=> $dropoff_location,
                        'distance' 			=> $distance
                    ]);

                    if ( $line_total ) {
                        $insurance_amount = (float)$rental_product->get_meta_value( 'amount_insurance' );
                        if ( $insurance_amount ) $line_total += $insurance_amount;

                        $price_html .= '<span class="ovabrw-price-search">';
                            $price_html .= '<span class="unit">'.esc_html__( 'From ', 'ova-brw' ).'</span>';
                            $price_html .= '<span class="amount">'.ovabrw_wc_price( $line_total, [], false ).'</span>';
                        $price_html .= '</span>';
                    }
                }
            }

            return apply_filters( $this->prefix.'get_price_html_from_search', $price_html, $data, $this );
	    }

	    /**
	     * Get price html from format
	     */
	    public function get_price_html_from_format( $view = 'single' ) {
	    	// init
	    	$price_format = '';

	    	if ( 'single' === $view ) {
	    		// Get price format
	    		$single_price_format = $this->get_meta_value( 'single_price_format' );

	    		if ( 'new' === $single_price_format ) {
	    			$price_format = $this->get_meta_value( 'single_price_new_format' );
	    		}

	    		if ( !$price_format ) {
	    			// Get category ids
	    			$category_ids 	= $this->get_category_ids();
	    			$category_id 	= ovabrw_array_exists( $category_ids ) ? reset( $category_ids ) : '';

	    			// Get price format from category
	    			$term_price_format = $category_id ? get_term_meta( $category_id, 'ovabrw_select_single_price_format', true ) : '';

	    			if ( 'new' == $term_price_format ) {
	                    $price_format = get_term_meta( $category_id, 'ovabrw_single_new_price_format', true );

	                    if ( !$price_format ) return false;
	                }
	    		}

	    		// Get price format form woo settings
	    		if ( !$price_format ) {
	    			$price_format = ovabrw_get_option( 'single_price_format' );
	    		}
	    		if ( !$price_format ) return false;
	    	} elseif ( 'archive' === $view ) {
	    		// Get price format
	    		$archive_price_format = $this->get_meta_value( 'archive_price_format' );

	    		if ( 'new' === $archive_price_format ) {
	    			$price_format = $this->get_meta_value( 'archive_price_new_format' );
	    		}

	    		if ( !$price_format ) {
	    			// Get category ids
	    			$category_ids 	= $this->get_category_ids();
	    			$category_id 	= ovabrw_array_exists( $category_ids ) ? reset( $category_ids ) : '';

	    			// Get price format from category
	    			$term_price_format = $category_id ? get_term_meta( $category_id, 'ovabrw_select_archive_price_format', true ) : '';

	    			if ( 'new' == $term_price_format ) {
	                    $price_format = get_term_meta( $category_id, 'ovabrw_archive_new_price_format', true );

	                    if ( !$price_format ) return false;
	                }
	    		}

	    		// Get price format form woo settings
	    		if ( !$price_format ) {
	    			$price_format = ovabrw_get_option( 'archive_price_format' );
	    		}
	    		if ( !$price_format ) return false;
	    	}

	    	// Get price format data
    		$price_data = $this->get_price_format_data();

    		// Unit
    		$price_format = str_replace( '[unit]', ovabrw_get_meta_data( 'unit', $price_data ), $price_format );

    		// Regular price
            if ( ovabrw_get_meta_data( 'regular_price', $price_data ) ) {
                $price_format = str_replace( '[regular_price]', ovabrw_wc_price( $price_data['regular_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[regular_price]', '', $price_format );
            }

            // Hour price
            if ( ovabrw_get_meta_data( 'hour_price', $price_data ) ) {
                $price_format = str_replace( '[hour_price]', ovabrw_wc_price( $price_data['hour_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[hour_price]', '', $price_format );
            }

            // Minimum daily price
            if ( ovabrw_get_meta_data( 'min_daily_price', $price_data ) ) {
                $price_format = str_replace( '[min_daily_price]', ovabrw_wc_price( $price_data['min_daily_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[min_daily_price]', '', $price_format );
            }

            // Maximum daily price
            if ( ovabrw_get_meta_data( 'max_daily_price', $price_data ) ) {
                $price_format = str_replace( '[max_daily_price]', ovabrw_wc_price( $price_data['max_daily_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[max_daily_price]', '', $price_format );
            }

            // Minimum package price
            if ( ovabrw_get_meta_data( 'min_package_price', $price_data ) ) {
                $price_format = str_replace( '[min_package_price]', ovabrw_wc_price( $price_data['min_package_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[min_package_price]', '', $price_format );
            }

            // Maximum package price
            if ( ovabrw_get_meta_data( 'max_package_price', $price_data ) ) {
                $price_format = str_replace( '[max_package_price]', ovabrw_wc_price( $price_data['max_package_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[max_package_price]', '', $price_format );
            }

            // Minimum location price
            if ( ovabrw_get_meta_data( 'min_location_price', $price_data ) ) {
                $price_format = str_replace( '[min_location_price]', ovabrw_wc_price( $price_data['min_location_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[min_location_price]', '', $price_format );
            }

            // Maximum location price
            if ( ovabrw_get_meta_data( 'max_location_price', $price_data ) ) {
                $price_format = str_replace( '[max_location_price]', ovabrw_wc_price( $price_data['max_location_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[max_location_price]', '', $price_format );
            }

            // Minimum price
            if ( ovabrw_get_meta_data( 'min_price', $price_data ) ) {
                $price_format = str_replace( '[min_price]', ovabrw_wc_price( $price_data['min_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[min_price]', '', $price_format );
            }

            // Maximum price
            if ( ovabrw_get_meta_data( 'max_price', $price_data ) ) {
                $price_format = str_replace( '[max_price]', ovabrw_wc_price( $price_data['max_price'], [], false ), $price_format );
            } else {
                $price_format = str_replace( '[max_price]', '', $price_format );
            }
	    	
	    	return apply_filters( $this->prefix.'get_price_html_from_format', $price_format, $view, $this );
	    }

	    /**
	     * Get price format data
	     */
	    public function get_price_format_data() {
	    	// init
            $price_data = [
                'unit'                  => '',
                'regular_price'         => '',
                'hour_price'            => '',
                'min_price'             => '',
                'max_price'             => '',
                'min_daily_price'       => '',
                'max_daily_price'       => '',
                'min_package_price'     => '',
                'max_package_price'     => '',
                'min_location_price'    => '',
                'max_location_price'    => ''
            ];

            // Rental type
            $rental_type = $this->get_rental_type();

            if ( 'day' === $rental_type ) {
            	// Charged by
            	$charged_by = $this->get_charged_by();

                if ( 'hotel' === $charged_by ) {
                	$price_data['unit'] = esc_html__( 'Night', 'ova-brw' );
                } else {
                	$price_data['unit'] = esc_html__( 'Day', 'ova-brw' );
                }

                $price_data['regular_price'] = $this->get_meta_value( 'regular_price_day' );
            } elseif ( 'hour' === $rental_type ) {
            	$price_data['unit']             = esc_html__( 'Hour', 'ova-brw' );
                $price_data['regular_price']    = $this->get_meta_value( 'regul_price_hour' );
            } elseif ( 'mixed' === $rental_type ) {
            	$price_data['unit']             = esc_html__( 'Day', 'ova-brw' );
                $price_data['regular_price']    = $this->get_meta_value( 'regular_price_day' );
                $price_data['hour_price']       = $this->get_meta_value( 'regul_price_hour' );
            } elseif ( 'period_time' === $rental_type ) {
            	$petime_price = $this->get_meta_value( 'petime_price' );

                if ( ovabrw_array_exists( $petime_price ) ) {
                    $price_data['min_package_price'] = min( $petime_price );
                    $price_data['max_package_price'] = max( $petime_price );
                }
            } elseif ( 'transportation' === $rental_type ) {
            	$price_location = $this->get_meta_value( 'price_location' );

                if ( ovabrw_array_exists( $price_location ) ) {
                    $price_data['min_location_price'] = min( $price_location );
                    $price_data['max_location_price'] = max( $price_location );
                }
            } elseif ( 'taxi' === $rental_type ) {
                // Get price by
                $price_by = $this->get_meta_value( 'map_price_by' );

                if ( 'mi' === $price_by ) {
                    $prices['unit'] = esc_html__( 'Mi', 'ova-brw' );
                } else {
                    $prices['unit'] = esc_html__( 'Km', 'ova-brw' );
                }

                $price_data['regular_price'] = $this->get_meta_value( 'regul_price_taxi' );
            } elseif ( 'hotel' === $rental_type ) {
            	$price_data['unit']             = esc_html__( 'Night', 'ova-brw' );
            	$price_data['regular_price']    = $this->get_meta_value( 'regular_price_hotel' );
            } elseif ( 'appointment' === $rental_type ) {
            	// Get price timeslots
                $price_timeslots = $this->get_meta_value( 'time_slots_price' );

                if ( ovabrw_array_exists( $price_timeslots ) ) {
                    foreach ( $price_timeslots as $items ) {
                        // Minimum price
                        $min_price = (float)min( $items );
                        if ( '' == $price_data['min_price'] ) {
                        	$price_data['min_price'] = $min_price;
                        }
                        if ( $price_data['min_price'] > $min_price ) {
                        	$price_data['min_price'] = $min_price;
                        }

                        // Maximum
                        $max_price = (float)max( $items );
                        if ( '' == $price_data['max_price'] ) {
                        	$price_data['max_price'] = $max_price;
                        }
                        if ( $price_data['max_price'] < $max_price ) {
                        	$price_data['max_price'] = $max_price;
                        }
                    }
                }
            }

            // Daily price
            if ( in_array( $rental_type, [ 'day', 'mixed', 'hotel' ] ) ) {
            	// Daily price
                $daily_price = [];

                // Monday price
                $monday_price = (float)$this->get_meta_value( 'daily_monday' );
                if ( $monday_price ) array_push( $daily_price, $monday_price );

                // Tuesday price
                $tuesday_price = (float)$this->get_meta_value( 'daily_tuesday' );
                if ( $tuesday_price ) array_push( $daily_price, $tuesday_price );

                // Wednesday price
                $wednesday_price = (float)$this->get_meta_value( 'daily_wednesday' );
                if ( $wednesday_price ) array_push( $daily_price, $wednesday_price );

                // Thursday price
                $thursday_price = (float)$this->get_meta_value( 'daily_thursday' );
                if ( $thursday_price ) array_push( $daily_price, $thursday_price );

                // Friday price
                $friday_price = (float)$this->get_meta_value( 'daily_friday' );
                if ( $friday_price ) array_push( $daily_price, $friday_price );

                // Saturday price
                $saturday_price = (float)$this->get_meta_value( 'daily_saturday' );
                if ( $saturday_price ) array_push( $daily_price, $saturday_price );

                // Sunday price
                $sunday_price = (float)$this->get_meta_value( 'daily_sunday' );
                if ( $sunday_price ) array_push( $daily_price, $sunday_price );

                if ( ovabrw_array_exists( $daily_price ) ) {
                    $prices['min_daily_price'] = min( $daily_price );
                    $prices['max_daily_price'] = max( $daily_price );
                }
            }

            return apply_filters( $this->prefix.'get_price_format_data', $price_data, $this );
	    }

	    /**
	     * Get custom taxonomies
	     */
	    public function get_custom_taxonomies( $view = '' ) {
	    	// init
	    	$custom_taxonomies = [];

	    	// All taxonomies
	    	$all_taxonomies = ovabrw_get_option( 'custom_taxonomy', [] );

	    	// Taxonomy depend category
	    	$depend_category = ovabrw_get_setting( 'search_show_tax_depend_cat', 'yes' );

	    	if ( 'yes' === $depend_category ) {
	    		// Custom taxonomies from category
	    		$taxonomies = [];

	    		// Get product category ids
	    		$category_ids = $this->get_category_ids();

	    		if ( ovabrw_array_exists( $category_ids ) ) {
	    			foreach ( $category_ids as $cat_id ) {
	    				// Get custom taxonomies
	    				$custom_tax = get_term_meta( $cat_id, 'ovabrw_custom_tax', true );

	    				if ( ovabrw_array_exists( $custom_tax ) ) {
	    					$taxonomies = ovabrw_array_merge_unique( $taxonomies, $custom_tax );
	    				}
	    			}
	    		}

	    		if ( ovabrw_array_exists( $taxonomies ) ) {
	    			foreach ( $taxonomies as $slug ) {
	    				// Check enabled
	    				if ( empty( $all_taxonomies[$slug]['enabled'] ) ) continue;

	    				// Check view
	    				if ( 'archive' === $view && empty( $all_taxonomies[$slug]['show_listing'] ) ) {
	    					continue;
	    				}

	    				// Name
	    				$name = !empty( $all_taxonomies[$slug]['label_frontend'] ) ? $all_taxonomies[$slug]['label_frontend'] : '';
	    				if ( !$name ) {
	    					$name = !empty( $all_taxonomies[$slug]['name'] ) ? $all_taxonomies[$slug]['name'] : '';
	    				}

	    				// Get terms
	    				$terms = get_the_terms( $this->get_id(), $slug );

	    				if ( ovabrw_array_exists( $terms ) ) {
	    					foreach ( $terms as $term ) {
	    						$term_slug = $term->slug;
	    						$term_name = $term->name;
	    						$term_link = get_term_link( $term->term_id );
	    						if ( is_wp_error( $term_link ) ) $term_link = '';

	    						if ( array_key_exists( $slug, $custom_taxonomies ) ) {
	    							array_push( $custom_taxonomies[$slug]['value'], $term_name );
	    							array_push( $custom_taxonomies[$slug]['link'], $term_link );
	    						} else {
	    							$custom_taxonomies[$slug] = [
	    								'name' 	=> $name,
	    								'value' => [ $term_name ],
	    								'link' 	=> [ $term_link ]
	    							];
	    						}
	    					}
	    				}
	    			}
	    		}
	    	} else {
	    		foreach ( $all_taxonomies as $slug => $item ) {
	    			// Check enabled
    				if ( empty( $item['enabled'] ) ) continue;

    				// Check view
    				if ( 'archive' === $view && empty( $item['show_listing'] ) ) {
    					continue;
    				}

    				// Name
    				$name = ovabrw_get_meta_data( 'label_frontend', $item );
    				if ( !$name ) {
    					$name = ovabrw_get_meta_data( 'name', $item );
    				}

    				// Get terms
    				$terms = get_the_terms( $this->get_id(), $slug );

    				if ( ovabrw_array_exists( $terms ) ) {
    					foreach ( $terms as $term ) {
    						$term_slug = $term->slug;
    						$term_name = $term->name;
    						$term_link = get_term_link( $term->term_id );

    						if ( array_key_exists( $slug, $custom_taxonomies ) ) {
    							array_push( $custom_taxonomies[$slug]['value'], $term_name );
    							array_push( $custom_taxonomies[$slug]['link'], $term_link );
    						} else {
    							$custom_taxonomies[$slug] = [
    								'name' 	=> $name,
    								'value' => [ $term_name ],
    								'link' 	=> [ $term_link ]
    							];
    						}
    					}
    				}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_custom_taxonomies', $custom_taxonomies, $this );
	    }

	    /**
	     * Get custom checkout fields
	     */
	    public function get_cckf() {
	    	// init
	    	$cckf = [];

	    	// All cckf
	    	$all_cckf = ovabrw_get_option( 'booking_form', [] );
	    	if ( !ovabrw_array_exists( $all_cckf ) ) $all_cckf = [];

	    	// From product
	    	$product_cckf_type = $this->get_meta_value( 'manage_custom_checkout_field' );

	    	// Product category ids
	    	$category_ids = $this->get_category_ids();

	    	// Category id
	    	$cat_id = ovabrw_array_exists( $category_ids ) ? reset( $category_ids ) : '';

	    	// From category
	    	$category_cckf_type = $cat_id ? get_term_meta( $cat_id, 'ovabrw_choose_custom_checkout_field', true ) : '';
	    	$category_cckf 		= $cat_id ? get_term_meta( $cat_id, 'ovabrw_custom_checkout_field', true ) : '';

	    	if ( 'new' === $product_cckf_type ) {
	    		$product_cckf = $this->get_meta_value( 'product_custom_checkout_field' );
	    		$product_cckf = explode( ',', $product_cckf );
				$product_cckf = array_map( 'trim', $product_cckf );

				if ( ovabrw_array_exists( $product_cckf ) ) {
					foreach ( $product_cckf as $name ) {
						if ( array_key_exists( $name, $all_cckf ) ) {
							$cckf[$name] = $all_cckf[$name];
						}
					}
				}
	    	} elseif ( 'all' === $category_cckf_type ) {
	    		$cckf = $all_cckf;
	    	} elseif ( 'special' === $category_cckf_type ) {
	    		if ( ovabrw_array_exists( $category_cckf ) ) {
	    			foreach ( $category_cckf as $name ) {
	    				if ( array_key_exists( $name, $all_cckf ) ) {
							$cckf[$name] = $all_cckf[$name];
						}
	    			}
	    		}
	    	} else {
	    		$cckf = $all_cckf;
	    	}

	    	return apply_filters( $this->prefix.'get_cckf', $cckf, $this );
	    }

	    /**
	     * Get sale price today
	     */
	    public function get_sale_price_today( $data = [] ) {
	    	// Sale price
	    	$sale_price = '';

	    	// init special start, end, price
	    	$special_start = $special_end = $special_price = [];

	    	// Current time
	    	$current_time = strtotime( ovabrw_get_meta_data( 'pickup_date', $data ) );
	    	if ( !$current_time ) $current_time = current_time( 'timestamp' );

	    	// Rental type
	    	$rental_type = $this->get_rental_type();

	    	if ( 'day' === $rental_type || 'hotel' === $rental_type ) {
	    		$special_start 	= $this->get_meta_value( 'rt_startdate' );
	    		$special_end 	= $this->get_meta_value( 'rt_enddate' );
	    		$special_price 	= $this->get_meta_value( 'rt_price' );
	    	} elseif ( 'hour' === $rental_type || 'mixed' === $rental_type ) {
	    		$special_start 	= $this->get_meta_value( 'rt_startdate' );
	    		$special_end 	= $this->get_meta_value( 'rt_enddate' );
	    		$special_price 	= $this->get_meta_value( 'rt_price_hour' );
	    	} elseif ( 'taxi' === $rental_type ) {
	    		$special_start 	= $this->get_meta_value( 'st_pickup_distance' );
	    		$special_end 	= $this->get_meta_value( 'st_pickoff_distance' );
	    		$special_price 	= $this->get_meta_value( 'st_price_distance' );
	    	} elseif ( 'appointment' === $rental_type ) {
	    		$special_start 	= $this->get_meta_value( 'special_startdate' );
	    		$special_end 	= $this->get_meta_value( 'special_enddate' );
	    		$special_price 	= $this->get_meta_value( 'special_price' );
	    	}

	    	// Get sale price
	    	if ( ovabrw_array_exists( $special_start ) ) {
	    		foreach ( $special_start as $k => $start_date ) {
	    			// Start date
	    			$start_date = strtotime( $start_date );
	    			if ( !$start_date ) continue;

	    			// End date
	    			$end_date = strtotime( ovabrw_get_meta_data( $k, $special_end ) );
	    			if ( !$end_date ) continue;

	    			// Price
	    			$price = (float)ovabrw_get_meta_data( $k, $special_price );
	    			if ( !$price ) continue;

	    			if ( $start_date <= $current_time && $current_time >= $end_date ) {
	    				$sale_prce = $price;
	    				break;
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_sale_price_today', $sale_price, $data, $this );
	    }

	    /**
	     * Get all image ids
	     */
	    public function get_all_image_ids() {
	    	// init
	    	$image_ids = [];

	    	// Get image id
	    	$image_id = $this->get_image_id();
            if ( $image_id ) {
                array_push( $image_ids, $image_id );
            }

            // Get gallery ids
            $gallery_ids = $this->get_gallery_image_ids();
            if ( ovabrw_array_exists( $gallery_ids ) ) {
                $image_ids = array_merge( $image_ids, $gallery_ids );
            }

	    	return apply_filters( $this->prefix.'get_all_image_ids', $image_ids, $this );
	    }

	    /**
	     * Get specifications
	     */
	    public function get_specifications() {
	    	// Results
        	$results = [];

        	// Get specifications option
        	$specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );
        	if ( !ovabrw_array_exists( $specifications ) ) $specifications = [];

        	// Get category ids
        	$category_ids 	= $this->get_category_ids();
	        $term_id 		= ovabrw_array_exists( $category_ids ) ? reset( $category_ids ) : '';
	        $term_choose 	= $term_id ? get_term_meta( $term_id, 'ovabrw_choose_specifications', true ) : '';

	        if ( 'all' === $term_choose ) {
	            $results = $specifications;
	        } elseif ( 'special' === $term_choose ) {
	            $special_specifications = get_term_meta( $term_id, 'ovabrw_specifications', true );

	            if ( ovabrw_array_exists( $special_specifications ) ) {
	                foreach ( $special_specifications as $name ) {
	                    if ( array_key_exists( $name, $specifications ) ) {
	                        $results[$name] = $specifications[$name];
	                    }
	                }
	            }
	        } else {
	            $results = $specifications;
	        }

	        // Filter product specifications
	        if ( ovabrw_array_exists( $results ) ) {
	        	$results = array_filter( $results, function ( $item ) {
				    return ( isset( $item['enable'] ) && $item['enable'] );
				});
	        }

        	return apply_filters( $this->prefix.'get_specifications', $results, $this );
	    }

	    /**
	     * Get default time
	     */
	    public function get_default_time( $type = 'pickup' ) {
	    	// init
	    	$default_time = '';

	    	if ( 'pickup' === $type ) {
	    		$product_default_time = $this->get_meta_value( 'manage_default_hour_start' );
	    		if ( 'new_time' === $product_default_time ) {
	    			$default_time = $this->get_meta_value( 'product_default_hour_start' );
	    		} else {
	    			$default_time = ovabrw_get_setting( 'booking_form_default_hour', '07:00' );
	    		}
	    	} elseif ( 'dropoff' === $type ) {
	    		$product_default_time = $this->get_meta_value( 'manage_default_hour_end' );
	    		if ( 'new_time' === $product_default_time ) {
	    			$default_time = $this->get_meta_value( 'product_default_hour_end' );
	    		} else {
	    			$default_time = ovabrw_get_setting( 'booking_form_default_hour_end_date', '07:00' );
	    		}
	    	}

	    	// Validation
			if ( strtotime( $default_time ) ) {
				$default_time = gmdate( OVABRW()->options->get_time_format(), strtotime( $default_time ) );
			} else {
				$default_time = '';
			}

	    	return apply_filters( $this->prefix.'get_default_time', $default_time, $type, $this );
	    }

	    /**
	     * Get time group
	     */
	    public function get_time_group( $type = 'pickup' ) {
	    	// init
	    	$time_group = [];

	    	if ( 'pickup' === $type ) {
	    		$product_time_group = $this->get_meta_value( 'manage_time_book_start' );
	    		if ( 'new_time' === $product_time_group ) {
	    			$time_group = $this->get_meta_value( 'product_time_to_book_start' );
	    		} elseif ( 'in_setting' === $product_time_group ) {
	    			$time_group = ovabrw_get_setting( 'calendar_time_to_book', '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00' );
	    		}
	    	} elseif ( 'dropoff' === $type ) {
	    		$product_time_group = $this->get_meta_value( 'manage_time_book_end' );
	    		if ( 'new_time' === $product_time_group ) {
	    			$time_group = $this->get_meta_value( 'product_time_to_book_end' );
	    		} elseif ( 'in_setting' === $product_time_group ) {
	    			$time_group = ovabrw_get_setting( 'calendar_time_to_book_for_end_date', '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00' );
	    		}
	    	}

	    	// String to array
			if ( $time_group ) {
				$time_group = array_map( 'trim', explode( ',', $time_group ) );
			} else {
				$time_group = [];
			}

			// Convert by time format
			if ( ovabrw_array_exists( $time_group ) ) {
				$time_group = array_map( function( $time ) {
					return gmdate( OVABRW()->options->get_time_format(), strtotime( $time ) );
				}, array_filter( $time_group, function( $time ) {
					return strtotime( $time ) !== false;
				}));
			}

			// re-index
			if ( ovabrw_array_exists( $time_group ) ) {
				$time_group = array_values( $time_group );
			}

	    	return apply_filters( $this->prefix.'get_time_group', $time_group, $type, $this );
	    }

	    /**
	     * Get calendar data
	     */
	    public function get_calendar_options() {
	    	// Get rental product
	    	$rental_product = OVABRW()->rental->get_rental_product( $this->get_id() );
	    	if ( !$rental_product ) return [];

	    	// Language
            $language = apply_filters( OVABRW_PREFIX.'datepicker_language', ovabrw_get_setting( 'calendar_language_general', 'en-GB' ) );

            if ( apply_filters( 'wpml_current_language', NULL ) ) { // WPML
                $language = apply_filters( 'wpml_current_language', NULL );
            } elseif ( function_exists('pll_current_language') ) { // Polylang
                $language = pll_current_language();
            }

            // Min date
            $min_date = gmdate( 'Y-m-d', current_time( 'timestamp' ) );

            // Preparation time
	        $preparation_time = $rental_product->get_preparation_time( 'Y-m-d' );
	        if ( $preparation_time && strtotime( $preparation_time ) > strtotime( $min_date ) ) {
	        	$min_date = $preparation_time;
	        }

	        // Get start date
			$start_date = $rental_product->get_start_date();
			if ( !$start_date || strtotime( $start_date ) < strtotime( $min_date ) ) {
				$start_date = $min_date;
			}

	    	// init
	    	$calendar_options = [
	    		'productID' 		=> $this->get_id(),
				'rentalType' 		=> $this->get_rental_type(),
				'chargedBy' 		=> $this->get_charged_by(),
				'defaultView' 		=> ovabrw_get_setting( 'calendar_default_view', 'dayGridMonth' ),
				'disabledColor' 	=> ovabrw_get_option( 'color_disable_calendar', '#FFFFFF' ),
				'disabledBg' 		=> ovabrw_get_option( 'bg_disable_calendar', '#E56E00' ),
				'availableColor' 	=> ovabrw_get_option( 'color_available_calendar', '#222222' ),
				'availableBg' 		=> ovabrw_get_option( 'bg_calendar_available', '#FFFFFF' ),
				'bookedColor' 		=> ovabrw_get_option( 'color_booked_calendar', '#FFFFFF' ),
				'bookedBg' 			=> ovabrw_get_option( 'bg_booked_calendar', '#F60808' ),
				'dateFormat' 		=> OVABRW()->options->get_date_format(),
				'timeFormat' 		=> OVABRW()->options->get_time_format(),
				'timeStep'			=> OVABRW()->options->get_time_step(),
				'firstDay' 			=> (int)ovabrw_get_setting( 'calendar_first_day', 1 ),
				'lang' 				=> $language,
				'defaultStartTime' 	=> $this->get_default_time(),
				'defaultEndTime' 	=> $this->get_default_time( 'dropoff' ),
				'allowStartTimes' 	=> $this->get_time_group(),
				'allowEndTimes' 	=> $this->get_time_group( 'dropoff' ),
				'disableWeekDays' 	=> $rental_product->get_disable_weekdays(),
				'disabledDates'		=> $rental_product->get_disabled_dates( 'calendar' ),
				'bookedDates' 		=> $rental_product->get_booked_dates( 'calendar' ),
				'eventRows' 		=> apply_filters( $this->prefix.'calendar_event_rows', 2 ),
				'allowedDates' 		=> $rental_product->get_allowed_dates(),
				'startDate'			=> $start_date,
				'showTimeBooked' 	=> ovabrw_get_setting( 'template_show_time_in_calendar', 'yes' ),
	            'slotLabelInterval' => [
	            	'hours' => OVABRW()->options->get_time_step() / 60
	            ],
	            'slotLabelFormat'   => [
	                'hour' 		=> '2-digit',
	                'minute' 	=> '2-digit',
	            	'meridiem' 	=> false,
	            	'hour12' 	=> false
	            ],
				// 'customLocale' 		=> [ // Custom locale example
				// 	'code' => 'custom-locale',
				// 	'week' => [
				// 		'dow' => 0,
				// 		'dow' => 6,
				// 	],
				// 	'buttonText' 	=> [
				// 		'day' 		=> 'Day',
				// 		'list' 		=> 'List',
				// 		'month' 	=> 'Month',
				// 		'next' 		=> 'Next',
				// 		'prev' 		=> 'Previous',
				// 		'today' 	=> 'Today',
				// 		'week' 		=> 'Week',
				// 		'year' 		=> 'Year'
				// 	],
				// 	'dayHeaderFormat' => [
				// 		'weekday' 	=> 'short'
				// 	],
				// 	'allDayText' 	=> 'All Day',
				// 	'moreLinkText' 	=> 'Show [number] more events', // is function callback
				// 	'noEventsText' 	=> 'No events to display'
				// ]
	    	];

	    	// Nav
	    	$nav = [];

	    	if ( 'yes' === ovabrw_get_setting( 'calendar_show_nav_month', 'yes' ) ) {
	    		$nav[] = 'dayGridMonth';
	    	}
	    	if ( 'yes' === ovabrw_get_setting( 'calendar_show_nav_week', 'yes' ) ) {
	    		$nav[] = 'timeGridWeek';
	    	}
	    	if ( 'yes' === ovabrw_get_setting( 'calendar_show_nav_day', 'yes' ) ) {
	    		$nav[] = 'timeGridDay';
	    	}
	    	if ( 'yes' === ovabrw_get_setting( 'calendar_show_nav_list', 'yes' ) ) {
	    		$nav[] = 'listWeek';
	    	}

	    	$calendar_options['nav'] = implode( ',', array_filter( $nav ) );

	    	// Show prices on Calendar
	    	if ( 'yes' === ovabrw_get_option( 'show_price_input_calendar', 'yes' ) ) {
	    		// Regular prices
	    		$calendar_options['regularPrice'] = $rental_product->get_calendar_regular_price();

	    		// Daily prices
	    		$calendar_options['dailyPrices'] = $rental_product->get_calendar_daily_prices();

	    		// Special prices
	    		$calendar_options['specialPrices'] = $rental_product->get_calendar_special_prices();
	    	}

	    	return apply_filters( $this->prefix.'get_calendar_options', $calendar_options, $this );
	    }

	    /**
	     * Get daily prices
	     */
	    public function get_daily_prices() {
	    	// Monday price
	    	$monday_price = $this->get_meta_value( 'daily_monday' );
	    	if ( !$monday_price ) return false;

	    	// Tuesday price
	    	$tuesday_price = $this->get_meta_value( 'daily_tuesday' );
	    	if ( !$tuesday_price ) return false;

	    	// Wednesday
	    	$wednesday_price = $this->get_meta_value( 'daily_wednesday' );
	    	if ( !$wednesday_price ) return false;

	    	// Thursday price
	    	$thursday_price = $this->get_meta_value( 'daily_thursday' );
	    	if ( !$thursday_price ) return false;

	    	// Friday price
	    	$friday_price = $this->get_meta_value( 'daily_friday' );
	    	if ( !$friday_price ) return false;

	    	// Saturday price
	    	$saturday_price = $this->get_meta_value( 'daily_saturday' );
	    	if ( !$saturday_price ) return false;

	    	// Sunday price
        	$sunday_price = $this->get_meta_value( 'daily_sunday' );
        	if ( !$sunday_price ) return false;

        	if ( in_array( $this->get_rental_type(), [ 'day', 'mixed', 'hotel' ] ) ) {
        		$daily_prices = [
        			'monday' 	=> $monday_price, 
					'tuesday' 	=> $tuesday_price, 
					'wednesday' => $wednesday_price, 
					'thursday' 	=> $thursday_price, 
					'friday' 	=> $friday_price, 
					'saturday' 	=> $saturday_price, 
					'sunday' 	=> $sunday_price
        		];

        		return apply_filters( $this->prefix.'get_daily_prices', $daily_prices, $this );
        	}

        	return false;
	    }

	    /**
	     * Price list available
	     */
	    public function price_list_available() {
	    	// Daily prices
	    	$daily_prices = $this->get_daily_prices();
			if ( ovabrw_array_exists( $daily_prices ) ) return true;

			// Get rental type
			$rental_type = $this->get_rental_type();

			// Discounts
			$discounts = [];
			if ( in_array( $rental_type, [ 'day', 'hour', 'mixed' ] ) ) {
				$discounts = $this->get_meta_value( 'global_discount_price' );
			} elseif ( 'period_time' === $rental_type ) {
				$discounts = $this->get_meta_value( 'petime_discount' );
			} elseif ( 'taxi' === $rental_type ) {
				$discounts = $this->get_meta_value( 'discount_distance_price' );
			} elseif ( 'hotel' === $rental_type ) {
				$discounts = $this->get_meta_value( 'global_discount_price' );
			}

			if ( ovabrw_array_exists( $discounts ) ) return true;
			// END

			// Special prices
			$special_prices = [];
			if ( in_array( $rental_type, [ 'day', 'hotel' ] ) ) {
				$special_prices = $this->get_meta_value( 'rt_price' );
			} elseif ( 'hour' === $rental_type ) {
				$special_prices = $this->get_meta_value( 'rt_price_hour' );
			} elseif ( 'mixed' === $rental_type ) {
				$special_prices = $this->get_meta_value( 'rt_price' );
				if ( !ovabrw_array_exists( $special_prices ) ) {
					$special_prices = $this->get_meta_value( 'rt_price_hour' );
				}
			} elseif ( 'taxi' === $rental_type ) {
				$special_prices = $this->get_meta_value( 'st_price_distance' );
			} elseif ( 'appointment' === $rental_type ) {
				$special_prices = $this->get_meta_value( 'special_price' );
			}

			if ( ovabrw_array_exists( $special_prices ) ) return true;
			// END

			return apply_filters( $this->prefix.'price_list_available', false, $this );
	    }

	    /**
	     * Get package id from duration
	     */
	    public function get_package_id( $duration = '' ) {
	    	// init
	    	$package_id = '';

	    	if ( $duration ) {
	    		$unfixed_time   = $this->get_meta_value( 'unfixed_time' );
	            $petime_ids     = $this->get_meta_value( 'petime_id', [] );
	            $petime_types   = $this->get_meta_value( 'package_type', [] );
	            $petime_days    = $this->get_meta_value( 'petime_days', [] );
	            $petime_hours   = $this->get_meta_value( 'pehour_unfixed', [] );
	            $petime_start   = $this->get_meta_value( 'pehour_start_time', [] );
	            $petime_end     = $this->get_meta_value( 'pehour_end_time', [] );

	            foreach ( $petime_ids as $k => $p_id ) {
	                $type = ovabrw_get_meta_data( $k, $petime_types );

	                if ( 'other' === $type ) {
	                    $number_day     = ovabrw_get_meta_data( $k, $petime_days );
	                    $number_second  = $number_day ? $number_day*86400 : '';

	                    if ( $number_second == $duration ) {
	                    	$package_id = $p_id;
	                    	break;
	                    }
	                } else {
	                    if ( 'yes' == $unfixed_time ) {
	                        $number_hour    = ovabrw_get_meta_data( $k, $petime_hours );
	                        $number_second  = $number_hour ? $number_hour*3600 : '';

	                        if ( $number_second == $duration ) {
	                        	$package_id = $p_id;
	                    		break;
	                        }
	                    } else {
	                        $start_time = strtotime( ovabrw_get_meta_data( $k, $petime_start ) );
	                        $end_time   = strtotime( ovabrw_get_meta_data( $k, $petime_end ) );
	                        
	                        if ( $start_time && $end_time ) {
	                            $number_second = $end_time - $start_time;

	                            if ( $number_second == $duration ) {
	                            	$package_id = $p_id;
	                    			break;
	                            }
	                        }
	                    }
	                }
	            }
	    	}

	    	return apply_filters( $this->prefix.'get_package_id', $package_id, $duration, $this );
	    }

	    /**
	     * Get datepicker options
	     */
	    public function get_datepicker_options() {
	    	// Get rental product
	    	$rental_product = OVABRW()->rental->get_rental_product( $this->get_id() );
	    	if ( !$rental_product ) return false;

	    	// Get datepicker options
	    	$datepicker = $rental_product->get_datepicker_options();

	    	return apply_filters( $this->prefix.'get_datepicker_options', $datepicker, $this );
	    }

	    /**
	     * Get timepicker options
	     */
	    public function get_timepicker_options() {
	    	// Get datepicker options
	        $timepicker = OVABRW()->options->get_timepicker_options();

	        // Allow start times
	        $timepicker['allowStartTimes'] = $this->get_time_group();

	        // Allow end times
	        $timepicker['allowEndTimes'] = $this->get_time_group( 'dropoff' );

	        // Default start time
	        $timepicker['defaultStartTime'] = $this->get_default_time();

	        // Default end time
	        $timepicker['defaultEndTime'] = $this->get_default_time( 'dropoff' );

	        return apply_filters( $this->prefix.'get_timepicker_options', $timepicker, $this );
	    }
	}
}