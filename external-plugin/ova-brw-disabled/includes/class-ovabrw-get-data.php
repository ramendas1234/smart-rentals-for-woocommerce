<?php use Automattic\WooCommerce\Utilities\OrderUtil;

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Get Data class.
 */
if ( !class_exists( 'OVABRW_Get_Data' ) ) {

	class OVABRW_Get_Data {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Get date format
		 */
		public function get_date_format() {
			return apply_filters( OVABRW_PREFIX.'get_date_format', ovabrw_get_setting( 'booking_form_date_format', 'Y-m-d' ) );
		}

		/**
		 * Get date placeholder
		 */
		public function get_date_placeholder() {
			$placeholder = '';
			$date_format = $this->get_date_format();

			switch ( $date_format ) {
				case 'd-m-Y':
					$placeholder = esc_html__( 'd-m-Y', 'ova-brw' );
					break;
				case 'm/d/Y':
					$placeholder = esc_html__( 'm/d/Y', 'ova-brw' );
					break;
				case 'Y/m/d':
					$placeholder = esc_html__( 'Y/m/d', 'ova-brw' );
					break;
				case 'Y-m-d':
					$placeholder = esc_html__( 'Y-m-d', 'ova-brw' );
					break;
				default:
					$placeholder = esc_html__( 'Y-m-d', 'ova-brw' );
					break;
			}

			return apply_filters( OVABRW_PREFIX.'get_date_placeholder', $placeholder );
		}

		/**
		 * Get time format
		 */
		public function get_time_format() {
			// Get time format
			$time_format = ovabrw_get_setting( 'calendar_time_format', 'H:i' );

			// For old version
			if ( '12' == $time_format || 'h:i' == $time_format ) {
				$time_format = 'h:i a';
			} elseif ( 'g:i' == $time_format ) {
				$time_format = 'g:i a';
			} elseif ( '24' == $time_format ) {
				$time_format = 'H:i';
			}

			return apply_filters( OVABRW_PREFIX.'get_time_format', $time_format );
		}

		/**
		 * Get time placeholder
		 */
		public function get_time_placeholder() {
			$placeholder = '';
			$time_format = $this->get_time_format();

			switch ( $time_format ) {
				case 'H:i':
					$placeholder = esc_html__( 'H:i', 'ova-brw' );
					break;
				case 'h:i':
					$placeholder = esc_html__( 'h:i', 'ova-brw' );
					break;
				case 'h:i a':
					$placeholder = esc_html__( 'h:i a', 'ova-brw' );
					break;
				case 'h:i A':
					$placeholder = esc_html__( 'h:i A', 'ova-brw' );
					break;
				case 'g:i':
					$placeholder = esc_html__( 'g:i', 'ova-brw' );
					break;
				case 'g:i a':
					$placeholder = esc_html__( 'g:i a', 'ova-brw' );
					break;
				case 'g:i A':
					$placeholder = esc_html__( 'g:i A', 'ova-brw' );
					break;
				default:
					$placeholder = esc_html__( 'H:i', 'ova-brw' );
					break;
			}

			return apply_filters( OVABRW_PREFIX.'get_time_placeholder', $placeholder );
		}

		/**
		 * Get date time format
		 */
		public function get_datetime_format() {
			return apply_filters( OVABRW_PREFIX.'get_datetime_format', $this->get_date_format() . ' ' . $this->get_time_format() );
		}

		/**
		 * Get date time placeholder
		 */
		public function get_datetime_placeholder() {
			return apply_filters( OVABRW_PREFIX.'get_datetime_placeholder', $this->get_date_placeholder() . ' ' . $this->get_time_placeholder() );
		}

		/**
		 * Get string date
		 */
		public function get_string_date( $strtotime_date = '', $strtotime_hour = '', $date_format = '', $time_format = '' ) {
			// init date
			$string_date = '';

			// Date format
			if ( !$date_format ) $date_format = $this->get_date_format();

			// Time format
			if ( !$time_format ) $time_format = $this->get_time_format();

			if ( $strtotime_date && $strtotime_hour ) {
				$string_date = gmdate( $date_format, $strtotime_date ).' '.gmdate( $time_format, $strtotime_hour );
			} elseif ( $strtotime_date && !$strtotime_hour ) {
				$string_date = gmdate( $date_format, $strtotime_date );
			} elseif ( !$strtotime_date && $strtotime_hour ) {
				$string_date = gmdate( $time_format, $strtotime_hour );
			}

			return apply_filters( OVABRW_PREFIX.'get_string_date', $string_date, $strtotime_date, $strtotime_hour, $date_format, $time_format );
		}

		/**
         * Get string day of week
         */
        public function get_string_dayofweek( $strtotime ) {
            if ( !$strtotime ) return false;

            $numberof_day   = gmdate( 'N', $strtotime );
            $dayofweek      = '';

            switch ( $numberof_day ) {
                case '1':
                    $dayofweek = 'monday';
                    break;
                case '2':
                    $dayofweek = 'tuesday';
                    break;
                case '3':
                    $dayofweek = 'wednesday';
                    break;
                case '4':
                    $dayofweek = 'thursday';
                    break;
                case '5':
                    $dayofweek = 'friday';
                    break;
                case '6':
                    $dayofweek = 'saturday';
                    break;
                case '7':
                    $dayofweek = 'sunday';
                    break;
                default:
                    break;
            }

            return apply_filters( OVABRW_PREFIX.'get_string_dayofweek', $dayofweek, $strtotime );
        }

		/**
		 * Get time step
		 */
		public function get_time_step() {
			return apply_filters( OVABRW_PREFIX.'get_time_step', ovabrw_get_setting( 'booking_form_step_time', 30 ) );
		}

		/**
		 * Get string time
		 */
		public function get_string_time( $time = '', $time_format = '' ) {
			// Time string
			$time_string = '';

			// Time format
			if ( !$time_format ) $time_format = $this->get_time_format();

			if ( is_numeric( $time ) ) {
				$time_string = gmdate( $time_format, $time );
			} elseif ( strtotime( $time ) ) {
				$time_string = gmdate( $time_format, strtotime( $time ) );
			}

			return apply_filters( OVABRW_PREFIX.'get_string_time', $time_string, $time, $time_format );
		}

		/**
		 * Get default time
		 */
		public function get_default_time( $type = 'pickup' ) {
			$default_time = '';

			if ( 'pickup' === $type ) {
				$default_time = ovabrw_get_setting( 'booking_form_default_hour', '07:00' );
			} elseif ( 'dropoff' === $type ) {
				$default_time = ovabrw_get_setting( 'booking_form_default_hour_end_date', '07:00' );
			}

			// Validation
			if ( strtotime( $default_time ) ) {
				$default_time = gmdate( $this->get_time_format(), strtotime( $default_time ) );
			} else {
				$default_time = '';
			}

			return apply_filters( OVABRW_PREFIX.'get_default_time', $default_time, $type );
		}

		/**
		 * Get time group
		 */
		public function get_time_group( $type = 'pickup' ) {
			$time_group = '';

			if ( 'pickup' == $type ) {
				$time_group = ovabrw_get_setting( 'calendar_time_to_book', '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00' );
			} elseif ( 'dropoff' == $type ) {
				$time_group = ovabrw_get_setting( 'calendar_time_to_book_for_end_date', '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00' );
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
					return gmdate( $this->get_time_format(), strtotime( $time ) );
				}, array_filter( $time_group, function( $time ) {
					return strtotime( $time ) !== false;
				}));
			}

			// re-index
			if ( ovabrw_array_exists( $time_group ) ) {
				$time_group = array_values( $time_group );
			}

			return apply_filters( OVABRW_PREFIX.'get_time_group', $time_group );
		}

		/**
         * Get timezone string
         */
        public function get_timezone_string() {
            $current_offset = get_option( 'gmt_offset' );
            $tzstring       = get_option( 'timezone_string' );

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

            return apply_filters( OVABRW_PREFIX.'get_timezone_string', $tzstring );
        }

        /**
         * Overcome disabled dates
         */
        public function overcome_disabled_dates() {
        	if ( 'yes' === ovabrw_get_setting( 'booking_form_disable_week_day', 'yes' ) ) {
        		return true;
        	}

        	return false;
        }

        /**
         * Datepicker global CSS
         */
        public function datepicker_global_css() {
        	// CSS
            $css = '';

            // Global
            $light_color    = ovabrw_get_option( 'glb_light_color', '#C3C3C3' );
            $primary_color  = ovabrw_get_option( 'glb_primary_color', '#E56E00' );

            $primary_background     = ovabrw_get_option( 'primary_background_calendar', '#00BB98' );
            $color_streak           = ovabrw_get_option( 'color_streak', '#FFFFFF' );
            $color_available        = ovabrw_get_option( 'color_available', '#222222' );
            $background_available   = ovabrw_get_option( 'background_available', '#FFFFFF' );
            $color_disable      	= ovabrw_get_option( 'color_not_available', '#FFFFFF' );
            $background_disable 	= ovabrw_get_option( 'background_not_available', '#E56E00' );
            $color_booked      		= ovabrw_get_option( 'color_booked_date', '#FFFFFF' );
            $background_booked 		= ovabrw_get_option( 'background_booked_date', '#E56E00' );
            

            $css .= "--ovabrw-primary-color:{$primary_color};";
            $css .= "--ovabrw-light-color:{$light_color};";

            $css .= "--ovabrw-primary-calendar:{$primary_background};";
            $css .= "--ovabrw-color-streak:{$color_streak};";
            $css .= "--ovabrw-available-color:{$color_available};";
            $css .= "--ovabrw-available-background:{$background_available};";
            $css .= "--ovabrw-disable-color:{$color_disable};";
            $css .= "--ovabrw-disable-background:{$background_disable};";
            $css .= "--ovabrw-booked-color:{$color_booked};";
            $css .= "--ovabrw-booked-background:{$background_booked};";

            return apply_filters( OVABRW_PREFIX.'datepicker_global_css', $css );
        }

        /**
         * Get timepicker options
         */
        public function get_timepicker_options() {
        	return (array)apply_filters( OVABRW_PREFIX.'timepicker_options', [
                'timeFormat'        => $this->get_time_format(),
                'step'              => $this->get_time_step(),
                'scrollDefault'     => '07:00',
                'forceRoundTime'    => true,
                'disableTextInput'  => true,
                'autoPickTime'      => true,
                'defaultStartTime'  => $this->get_default_time(),
                'defaultEndTime'    => $this->get_default_time( 'dropoff' ),
                'allowTimes'        => [],
                'allowStartTimes'   => $this->get_time_group(),
                'allowEndTimes'     => $this->get_time_group( 'dropoff' ),
                'lang'              => apply_filters( OVABRW_PREFIX.'timepicker_options_lang', [
                    'am'        => 'am',
                    'pm'        => 'pm',
                    'AM'        => 'AM',
                    'PM'        => 'PM',
                    'decimal'   => '.',
                    'mins'      => 'mins',
                    'hr'        => 'hr',
                    'hrs'       => 'hrs',
                    'pickUp'    => esc_html__( 'Pick-up', 'ova-brw' ),
                    'dropOff'   => esc_html__( 'Drop-off', 'ova-brw' )
                ]),
                'dailyPrices'       => apply_filters( OVABRW_PREFIX.'datepicker_daily_prices', [] ),
                'regularPrice'      => apply_filters( OVABRW_PREFIX.'datepicker_regular_prices', [] ),
                'specialPrices'     => apply_filters( OVABRW_PREFIX.'datepicker_special_prices', [] ),
            ]);
        }

        /**
         * Get datepicker options
         */
        public function get_datepicker_options() {
            // Date format
            $date_format = $this->get_date_format();

            // Time format
            $time_format = $this->get_time_format();

            // Min year, Max year
            $min_year = (int)ovabrw_get_setting( 'booking_form_year_start', gmdate('Y') );
            $max_year = (int)ovabrw_get_setting( 'booking_form_year_end', gmdate('Y')+3 );

            // Get min date
            $min_date = gmdate( $date_format, current_time( 'timestamp' ) );
            if ( $min_year && $min_year > gmdate('Y') ) {
                $min_date = gmdate( $date_format, strtotime( "$min_year-01-01" ) );
            }

            // Get max date
            $max_date = '';
            if ( $max_year ) {
                $december_date = new DateTime("$max_year-12-01");
                $december_date->modify('last day of this month');

                // Get max date
                $max_date = $december_date->format($date_format);
            }

            // Start date when calendar show
            $start_date = '';

            if ( $min_date && strtotime( $min_date ) > current_time( 'timestamp' ) ) {
                $start_date = $min_date;
            }

            // Language
            $language = apply_filters( OVABRW_PREFIX.'datepicker_language', ovabrw_get_setting( 'calendar_language_general', 'en-GB' ) );

            if ( apply_filters( 'wpml_current_language', NULL ) ) { // WPML
                $language = apply_filters( 'wpml_current_language', NULL );
            } elseif ( function_exists('pll_current_language') ) { // Polylang
                $language = pll_current_language();
            }

            // Overcome disabled dates
            $inseparable = ovabrw_get_setting( 'booking_form_disable_week_day' );
            if ( 'yes' == $inseparable ) {
                $inseparable = false;
            } else {
                $inseparable = true;
            }

            // Datepicker CSS
            $datepciker_css = [
                OVABRW_PLUGIN_URI.'assets/libs/easepick/easepick.min.css',
                OVABRW_PLUGIN_URI.'assets/css/datepicker/datepicker.css'
            ];

            // Get customize calendar
            $customize_calendar = ovabrw_get_option( 'customize_calendar' );
            if ( 'yes' == $customize_calendar ) {
                $css_path = OVABRW_PLUGIN_PATH.'assets/css/datepicker/customize.css';

                if ( file_exists( $css_path ) ) {
                    $datepciker_css[] = OVABRW_PLUGIN_URI.'assets/css/datepicker/customize.css';
                } else {
                    $additional_css = ovabrw_get_option( 'additional_css' );
                    $numberof_byte  = file_put_contents( OVABRW_PLUGIN_PATH.'assets/css/datepicker/customize.css', (string)$additional_css );

                    if ( $numberof_byte ) {
                        $datepciker_css[] = OVABRW_PLUGIN_URI.'assets/css/datepicker/customize.css';
                    }
                }
            }

            return (array)apply_filters( OVABRW_PREFIX.'datepicker_options', [
                'css'           => apply_filters( OVABRW_PREFIX.'datepicker_css', $datepciker_css ),
                'firstDay'      => (int)ovabrw_get_setting( 'calendar_first_day', 1 ),
                'lang'          => $language,
                'format'        => $date_format,
                'grid'          => 2,
                'calendars'     => 2,
                'zIndex'        => 999999999,
                'inline'        => false,
                'readonly'      => true,
                'header'        => apply_filters( OVABRW_PREFIX.'datepicker_header', '' ),
                'autoApply'     => true,
                'locale'        => apply_filters( OVABRW_PREFIX.'datepicker_locale', [
                    'cancel'    => esc_html__( 'Cancel', 'ova-brw' ),
                    'apply'     => esc_html__( 'Apply', 'ova-brw' )
                ]),
                'AmpPlugin'     => apply_filters( OVABRW_PREFIX.'datepicker_amp_plugin', [
                	'dropdown'  => [
                    	'months'    => true,
                        'years'     => true,
                        'minYear'   => $min_year ? $min_year : gmdate('Y'),
                        'maxYear'   => $max_year ? $max_year : gmdate('Y')+3
                    ],
                    'resetButton'   => true,
                    'darkMode'      => false
                ]),
                'RangePlugin'   => apply_filters( OVABRW_PREFIX.'datepicker_range_plugin', [
                    'repick'    => false,
                    'strict'    => true,
                    'tooltip'   => true,
                    'locale'    => [
                        'zero'  => '',
                        'one'   => esc_html__( 'day', 'ova-brw' ),
                        'two'   => '',
                        'many'  => '',
                        'few'   => '',
                        'other' => esc_html__( 'days', 'ova-brw' )
                    ]
                ]),
                'LockPlugin'    => apply_filters( OVABRW_PREFIX.'datepicker_lock_plugin', [
                    'minDate'           => $min_date,
                    'maxDate'           => $max_date,
                    'minDays'           => '',
                    'maxDays'           => '',
                    'selectForward'     => false,
                    'selectBackward'    => false,
                    'presets'           => true,
                    'inseparable'       => apply_filters( OVABRW_PREFIX.'datepicker_inseparable', $inseparable )
                ]),
                'PresetPlugin'      => apply_filters( OVABRW_PREFIX.'datepicker_preset_plugin', [
                    'position'      => 'left',
                    'customLabels'  => [
                        esc_html__( 'Today', 'ova-brw' ),
                        esc_html__( 'Yesterday', 'ova-brw' ),
                        esc_html__( 'Last 7 Days', 'ova-brw' ),
                        esc_html__( 'Last 30 Days', 'ova-brw' ),
                        esc_html__( 'This Month', 'ova-brw' ),
                        esc_html__( 'Last Month', 'ova-brw' )
                    ],
                    'customPreset'  => ovabrw_get_predefined_ranges()
                ]),
                'plugins' => apply_filters( OVABRW_PREFIX.'datepicker_plugins', [
                    'AmpPlugin',
                    'RangePlugin',
                    'LockPlugin'
                ]),
                'disableWeekDays'   => apply_filters( OVABRW_PREFIX.'datepicker_disable_weekdays', [] ),
                'disableDates'      => apply_filters( OVABRW_PREFIX.'datepicker_disable_dates', [] ),
                'bookedDates'       => apply_filters( OVABRW_PREFIX.'datepicker_booked_dates', [] ),
                'allowedDates'      => apply_filters( OVABRW_PREFIX.'datepicker_allowed_dates', [] ),
                'regularPrice'      => apply_filters( OVABRW_PREFIX.'datepicker_regular_prices', '' ),
                'dailyPrices'       => apply_filters( OVABRW_PREFIX.'datepicker_daily_prices', [] ),
                'specialPrices'     => apply_filters( OVABRW_PREFIX.'datepicker_special_prices', [] ),
                'startDate'         => $start_date,
            ]);
        }

        /**
         * Get rental product ids
         */
        public function get_rental_product_ids( $args = [] ) {
        	// Rental type
        	$rental_type = ovabrw_get_meta_data( 'type', $args );

        	// Include rental type
        	$include_type = ovabrw_get_meta_data( 'include_type', $args );

        	// Exclude rental type
        	$exclude_type = ovabrw_get_meta_data( 'exclude_type', $args );

        	// Base query
        	$base_query = [
                'post_type'         => 'product',
                'posts_per_page'    => '-1',
                'post_status'       => 'publish',
                'fields'            => 'ids', 
                'tax_query'         => [
                    [
                        'taxonomy' => 'product_type',
                        'field'    => 'slug',
                        'terms'    => OVABRW_RENTAL
                    ]
                ]
            ];

            // Check activate WCFM is vendor
		    if ( function_exists( 'wcfm_is_vendor' ) && wcfm_is_vendor( get_current_user_id() ) ) {
		    	$base_query['author'] = get_current_user_id();
		    }

            // Meta query
            $meta_query = [];

            // Filter by rental type
            if ( $rental_type ) {
            	array_push( $meta_query , [
            		'key'     => ovabrw_meta_key( 'price_type' ),
                    'value'   => $rental_type,
                    'compare' => '='
            	]);
            }

            // Include rental type
            if ( ovabrw_array_exists( $include_type ) ) {
            	array_push( $meta_query , [
            		'key'     => ovabrw_meta_key( 'price_type' ),
                    'value'   => $include_type,
                    'compare' => 'IN'
            	]);
            }

            // Exclude rental type
            if ( ovabrw_array_exists( $exclude_type ) ) {
            	array_push( $meta_query , [
            		'key'     => ovabrw_meta_key( 'price_type' ),
                    'value'   => $exclude_type,
                    'compare' => 'NOT IN'
            	]);
            }

            // Add meta query
            if ( ovabrw_array_exists( $meta_query ) ) {
            	$meta_query['relation'] 	= 'AND';
            	$base_query['meta_query'] 	= $meta_query;
            }

            // Get product IDs
            $product_ids = get_posts( $base_query );
            if ( !ovabrw_array_exists( $product_ids ) ) $product_ids = [];

            return apply_filters( OVABRW_PREFIX.'get_rental_product_ids', $product_ids, $args );
        }

        /**
         * Get location IDs
         */
        public function get_location_ids() {
        	$location_ids = get_posts([
                'post_type'         => 'location',
                'post_status'       => 'publish',
                'posts_per_page'    => '-1',
                'fields'            => 'ids'
            ]);

            return apply_filters( OVABRW_PREFIX.'get_location_ids', $location_ids );
        }

        /**
         * Get locations
         */
        public function get_locations() {
        	// init
        	$location_names = [];

        	// Location IDs
        	$location_ids = $this->get_location_ids();

        	if ( ovabrw_array_exists( $location_ids ) ) {
        		foreach ( $location_ids as $location_id ) {
        			$location_title = get_the_title( $location_id );
        			if ( !$location_title ) continue;

        			// Convert HTML entities
        			$location_title = html_entity_decode( trim( $location_title ) );

        			// Add location names
        			$location_names[$location_title] = $location_title;
        		}
        	}

        	return apply_filters( OVABRW_PREFIX.'get_locations', $location_names );
        }

        /**
         * Get location HTML
         */
        public function get_html_location( $type = 'pickup', $name = '', $class = '', $selected = '' ) {
        	// HTML
	    	$html = '<select name="' . esc_attr( $name ) . '" class="' . esc_attr( $class ) . '">';
	    		$html .= '<option value="">' . esc_html__( 'Select Location', 'ova-brw' ) . '</option>';

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

        	$html .= '</select>'; // END HTML

	    	return apply_filters( OVABRW_PREFIX.'get_html_location', $html, $type, $name, $class, $selected, $this );
        }

        /**
         * Get vehicle IDs
         */
        public function get_vehicle_ids() {
        	$vehicle_ids = get_posts( apply_filters( OVABRW_PREFIX.'query_get_vehicle_ids', [
                'post_type'         => 'vehicle',
                'post_status'       => 'publish',
                'posts_per_page'    => '-1',
                'fields'            => 'ids'
            ]));

            if ( !ovabrw_array_exists( $vehicle_ids ) ) $vehicle_ids = [];

            return apply_filters( OVABRW_PREFIX.'get_vehicle_ids', $vehicle_ids );
        }

        /**
         * Get vehicles
         */
        public function get_vehicldes() {
        	// init
        	$vehicles = [];

        	// Get vehicle ids
        	$vehicle_ids = $this->get_vehicle_ids();

        	if ( ovabrw_array_exists( $vehicle_ids ) ) {
        		foreach ( $vehicle_ids as $id ) {
        			$vehicle_id = ovabrw_get_post_meta( $id, 'id_vehicle' );

        			if ( $vehicle_id ) {
        				$vehicles[$vehicle_id] = get_the_title( $id );
        			}
        		}
        	}

        	return apply_filters( OVABRW_PREFIX.'get_vehicles', $vehicles );
        }

        /**
         * Get product ids - multi language
         */
        public function get_product_ids_multi_lang( $product_id = false ) {
            if ( !$product_id ) return [];

            // Product IDs
            $product_ids = [];

            if ( is_plugin_active( 'polylang/polylang.php' ) || is_plugin_active( 'polylang-pro/polylang.php' ) ) {
                $languages = pll_languages_list();

                if ( ovabrw_array_exists( $languages ) ) {
                    foreach ( $languages as $lang ) {
                        $p_id = pll_get_post( $product_id, $lang );

                        if ( $p_id ) $product_ids[] = $p_id;
                    }
                }
            } elseif ( is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ) ) {
                global $sitepress;

                if ( $sitepress && is_object( $sitepress ) ) {
                    $trid           = $sitepress->get_element_trid( $product_id, 'post_product' );
                    $translations   = $sitepress->get_element_translations( $trid, 'product' );

                    if ( ovabrw_array_exists( $translations ) ) {
                        foreach ( $translations as $lang => $translation ) {
                            $p_id = $translation->element_id;

                            if ( $p_id ) $product_ids[] = $p_id;
                        }
                    }
                }
            }

            // Check product IDs
            if ( !ovabrw_array_exists( $product_ids ) ) {
                $product_ids[] = $product_id;
            }

            return apply_filters( OVABRW_PREFIX.'get_product_ids_multi_lang', $product_ids, $product_id );
        }

        /**
         * Custom orders table usage is enabled
         */
        public function custom_orders_table_usage() {
        	if ( OrderUtil::custom_orders_table_usage_is_enabled() ) return true;
        	return false;
        }

        /**
         * Get order booked ids
         */
        public function get_order_booked_ids( $product_id, $order_status = [] ) {
        	if ( !$product_id ) return false;

        	// Order start
        	if ( !ovabrw_array_exists( $order_status ) ) {
        		$order_status = ovabrw_get_order_status();
        	}

        	// Product ids
        	$product_ids = $this->get_product_ids_multi_lang( $product_id );

        	// init
        	$order_ids = [];

        	global $wpdb;

        	if ( $this->custom_orders_table_usage() ) {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT o.id
                    FROM {$wpdb->prefix}wc_orders AS o
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi
                        ON o.id = oi.order_id
                        AND oi.order_item_type = %s
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
                        ON oi.order_item_id = oim.order_item_id
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim2
                        ON oi.order_item_id = oim2.order_item_id
                    WHERE o.type = %s
                    	AND oim.meta_key = %s
                    	AND oim.meta_value IN (%s)
                        AND oim2.meta_key = %s
                        AND oim2.meta_value >= %d
                        AND o.status IN ('". implode( "','", array_map( 'esc_sql', $order_status ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'_product_id',
                    	implode( ',', $product_ids ),
                    	'ovabrw_pickoff_date_strtotime',
                    	current_time( 'timestamp' )
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	} else {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT oitems.order_id
                    FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                        ON oitems.order_item_id = oitem_meta.order_item_id
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta2
                        ON oitems.order_item_id = oitem_meta2.order_item_id
                    LEFT JOIN {$wpdb->posts} AS p
                        ON oitems.order_id = p.ID
                    WHERE oitems.order_item_type = %s
                        AND p.post_type = %s
                        AND oitem_meta.meta_key = %s
                        AND oitem_meta.meta_value IN (%s)
                        AND oitem_meta2.meta_key = %s
                        AND oitem_meta2.meta_value >= %d
                        AND p.post_status IN ('". implode( "','", array_map( 'esc_sql', $order_status ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'_product_id',
                    	implode( ',', $product_ids ),
                    	'ovabrw_pickoff_date_strtotime',
                    	current_time( 'timestamp' )
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	}

        	return apply_filters( OVABRW_PREFIX.'get_order_booked_ids', $order_ids, $product_id, $order_status );
        }

        /**
         * Get order booked ids by pick-up date
         */
        public function get_order_booked_ids_by_pickup( $product_id, $pickup_date, $order_status = [] ) {
        	if ( !$product_id || !$pickup_date ) return false;

        	// Order start
        	if ( !ovabrw_array_exists( $order_status ) ) {
        		$order_status = ovabrw_get_order_status();
        	}

        	// Product ids
        	$product_ids = $this->get_product_ids_multi_lang( $product_id );

        	// init
        	$order_ids = [];

        	global $wpdb;

        	if ( $this->custom_orders_table_usage() ) {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT o.id
                    FROM {$wpdb->prefix}wc_orders AS o
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi
                        ON o.id = oi.order_id
                        AND oi.order_item_type = %s
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
                        ON oi.order_item_id = oim.order_item_id
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim2
                        ON oi.order_item_id = oim2.order_item_id
                    WHERE o.type = %s
                    	AND oim.meta_key = %s
                    	AND oim.meta_value IN (%s)
                        AND oim2.meta_key = %s
                        AND oim2.meta_value >= %d
                        AND o.status IN ('". implode( "','", array_map( 'esc_sql', $order_status ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'_product_id',
                    	implode( ',', $product_ids ),
                    	'ovabrw_pickup_date_strtotime',
                    	current_time( 'timestamp' )
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	} else {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT oitems.order_id
                    FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                        ON oitems.order_item_id = oitem_meta.order_item_id
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta2
                        ON oitems.order_item_id = oitem_meta2.order_item_id
                    LEFT JOIN {$wpdb->posts} AS p
                        ON oitems.order_id = p.ID
                    WHERE oitems.order_item_type = %s
                        AND p.post_type = %s
                        AND oitem_meta.meta_key = %s
                        AND oitem_meta.meta_value IN (%s)
                        AND oitem_meta2.meta_key = %s
                        AND oitem_meta2.meta_value >= %d
                        AND p.post_status IN ('". implode( "','", array_map( 'esc_sql', $order_status ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'_product_id',
                    	implode( ',', $product_ids ),
                    	'ovabrw_pickup_date_strtotime',
                    	current_time( 'timestamp' )
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	}

        	return apply_filters( OVABRW_PREFIX.'get_order_booked_ids_by_pickup', $order_ids, $pickup_date, $product_id, $order_status );
        }

        /**
         * Get future order ids
         */
        public function get_future_order_ids() {
        	// init
        	$order_ids = [];

        	global $wpdb;

        	if ( $this->custom_orders_table_usage() ) {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT o.id
                    FROM {$wpdb->prefix}wc_orders AS o
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi
                        ON o.id = oi.order_id
                        AND oi.order_item_type = %s
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
                        ON oi.order_item_id = oim.order_item_id
                    WHERE o.type = %s
                        AND oim.meta_key = %s
                        AND oim.meta_value >= %d
                        AND o.status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_pickup_date_strtotime',
                    	current_time( 'timestamp' )
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	} else {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT oitems.order_id
                    FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                        ON oitems.order_item_id = oitem_meta.order_item_id
                    LEFT JOIN {$wpdb->posts} AS p
                        ON oitems.order_id = p.ID
                    WHERE oitems.order_item_type = %s
                        AND p.post_type = %s
                        AND oitem_meta.meta_key = %s
                        AND oitem_meta.meta_value >= %d
                        AND p.post_status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_pickup_date_strtotime',
                    	current_time( 'timestamp' )
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	}

        	return apply_filters( OVABRW_PREFIX.'get_future_order_ids', $order_ids );
        }

        /**
         * Get present order ids
         */
        public function get_present_order_ids() {
        	// init
        	$order_ids = [];

        	global $wpdb;

        	if ( $this->custom_orders_table_usage() ) {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT o.id
                    FROM {$wpdb->prefix}wc_orders AS o
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi
                        ON o.id = oi.order_id
                        AND oi.order_item_type = %s
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
                        ON oi.order_item_id = oim.order_item_id
                    WHERE o.type = %s
                        AND oim.meta_key = %s
                        AND oim.meta_value >= %d
                        AND o.status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_pickoff_date_strtotime',
                    	current_time( 'timestamp' )
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	} else {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT oitems.order_id
                    FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                        ON oitems.order_item_id = oitem_meta.order_item_id
                    LEFT JOIN {$wpdb->posts} AS p
                        ON oitems.order_id = p.ID
                    WHERE oitems.order_item_type = %s
                        AND p.post_type = %s
                        AND oitem_meta.meta_key = %s
                        AND oitem_meta.meta_value >= %d
                        AND p.post_status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_pickoff_date_strtotime',
                    	current_time( 'timestamp' )
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	}

        	return apply_filters( OVABRW_PREFIX.'get_present_order_ids', $order_ids );
        }

        /**
         * Get order ids no remaining
         */
        public function get_order_ids_no_remaining() {
        	global $wpdb;

        	if ( $this->custom_orders_table_usage() ) {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT o.id
                    FROM {$wpdb->prefix}wc_orders AS o
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi
                        ON o.id = oi.order_id
                        AND oi.order_item_type = %s
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim
                        ON oi.order_item_id = oim.order_item_id
                    WHERE o.type = %s
                        AND oim.meta_key = %s
                        AND oim.meta_value != 0
                        AND o.status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_remaining_amount'
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	} else {
        		// phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        		$order_ids = $wpdb->get_col( $wpdb->prepare("
                    SELECT DISTINCT oitems.order_id
                    FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                        ON oitems.order_item_id = oitem_meta.order_item_id
                    LEFT JOIN {$wpdb->posts} AS p
                        ON oitems.order_id = p.ID
                    WHERE oitems.order_item_type = %s
                        AND p.post_type = %s
                        AND oitem_meta.meta_key = %s
                        AND oitem_meta.meta_value != 0
                        AND p.post_status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                    [
                    	'line_item',
                    	'shop_order',
                    	'ovabrw_remaining_amount'
                    ]
                ));
        		// phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
        	}

        	return apply_filters( OVABRW_PREFIX.'get_order_ids_no_remaining', $order_ids );
        }

        /**
         * Get draft order id
         */
        public function get_draft_order_id() {
        	if ( !wc()->session ) {
                wc()->initialize_session();
            }

            return wc()->session->get( 'store_api_draft_order', 0 );
        }

        /**
         * Enable insurance tax
         */
        public function enable_insurance_tax() {
            if ( wc_tax_enabled() && 'yes' === ovabrw_get_setting( 'enable_insurance_tax', 'no' ) ) {
                return true;
            }

            return false;
        }

        /**
         * Get insurance name
         */
        public function get_insurance_name() {
        	return apply_filters( OVABRW_PREFIX.'get_insurance_name', esc_html__( 'Insurance fees', 'ova-brw' ) );
        }

        /**
         * Get insurance tax class
         */
        public function get_insurance_tax_class() {
            return apply_filters( OVABRW_PREFIX.'get_insurance_tax_class', '' );
        }

        /**
         * Get insurance inclusive tax
         */
        public function get_insurance_inclusive_tax( $price = 0 ) {
            $tax_display = get_option( 'woocommerce_tax_display_cart' );

            if ( wc_tax_enabled() && 'incl' == $tax_display ) {
                $price += $this->get_insurance_tax_amount( $price );
            }

            return apply_filters( OVABRW_PREFIX.'get_insurance_inclusive_tax', $price );
        }

        /**
         * Get insurance tax amount
         */
        public function get_insurance_tax_amount( $price ) {
            $tax_amount = 0;

            if ( $this->enable_insurance_tax() ) {
                $tax_rates  = WC_Tax::get_rates( $this->get_insurance_tax_class() );
                $taxes      = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                $tax_amount += WC_Tax::get_tax_total( $taxes );
            }

            return apply_filters( OVABRW_PREFIX.'get_insurance_tax_amount', $tax_amount, $price );
        }

        /**
         * Remaining amount incl insurance
         */
        public function remaining_amount_incl_insurance() {
            if ( 'yes' != ovabrw_get_setting( 'only_add_insurance_to_deposit', 'no' ) ) {
                return true;
            }

            return false;
        }

        /**
         * Get HTML custom checkout fields
         */
        public function get_html_cckf( $cckf = [], $qtys = [], $args = [] ) {
        	// init
        	$html = '';

        	// Check show extra setting
        	if ( 'no' === ovabrw_get_setting( 'booking_form_show_extra', 'no' ) ) return $html;

        	if ( ovabrw_array_exists( $cckf ) ) {
        		// Product ID
        		$product_id = (int)ovabrw_get_meta_data( 'product_id', $args );

        		// Get product
        		$product = wc_get_product( $product_id );
        		if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) return $html;

        		// Product quantity
        		$product_qty = (int)ovabrw_get_meta_data( 'quantity', $args, 1 );

        		// Order ID
        		$order_id = (int)ovabrw_get_meta_data( 'order_id', $args );

        		// Currency
        		$currency_code = '';
        		if ( $order_id ) {
        			// Get order
        			$order = wc_get_order( $order_id );
        			if ( $order ) $currency_code = $order->get_currency();
        		}

        		// All cckf
        		$all_cckf = ovabrw_get_option( 'booking_form', [] );

        		foreach ( $cckf as $k => $val ) {
        			if ( ovabrw_get_meta_data( $k, $all_cckf ) ) {
        				// Type
        				$type = $all_cckf[$k]['type'];

        				if ( 'radio' === $type ) {
        					// Option values
        					$opt_values = isset( $all_cckf[$k]['ova_values'] ) ? (array)$all_cckf[$k]['ova_values'] : [];

        					// Get index
        					$index 		= array_search( $val, $opt_values );
        					$opt_qty 	= (int)ovabrw_get_meta_data( $k, $qtys, 1 );

        					if ( false !== $index ) {
        						$opt_price = isset( $all_cckf[$k]['ova_prices'][$index] ) ? ovabrw_convert_price_in_admin( $all_cckf[$k]['ova_prices'][$index], $currency_code ) : 0;
        						if ( !$opt_price ) continue;

        						// Option quantity
        						if ( $opt_qty ) $opt_price *= $opt_qty;

        						// Get price html
        						$opt_price = ovabrw_wc_price( $opt_price * $product_qty, [ 'currency' => $currency_code ] );

        						$html .= '<dt>'.sprintf( esc_html__( '%s : ', 'ova-brw' ), $val ).'</dt>';
        						$html .= '<dd>'. wp_kses_post( $opt_price ) .'</dd>';
        					}
        				} elseif ( 'checkbox' === $type ) {
        					// Option keys
        					$opt_keys = isset( $all_cckf[$k]['ova_checkbox_key'] ) ? (array)$all_cckf[$k]['ova_checkbox_key'] : [];

        					if ( ovabrw_array_exists( $val ) ) {
        						foreach ( $val as $opt_val ) {
        							$index 		= array_search( $opt_val, $opt_keys );
        							$opt_qty 	= (int)ovabrw_get_meta_data( $opt_val, $qtys, 1 );

        							if ( false !== $index ) {
        								$opt_name = isset( $all_cckf[$k]['ova_checkbox_text'][$index] ) ? $all_cckf[$k]['ova_checkbox_text'][$index] : '';
        								$opt_price = isset( $all_cckf[$k]['ova_checkbox_price'][$index] ) ? ovabrw_convert_price_in_admin( $all_cckf[$k]['ova_checkbox_price'][$index], $currency_code ) : 0;
        								if ( !$opt_price ) continue;

        								// Option quantity
		        						if ( $opt_qty ) $opt_price *= $opt_qty;

		        						// Get price html
        								$opt_price = ovabrw_wc_price( $opt_price * $product_qty, [ 'currency' => $currency_code ] );

		        						$html .= '<dt>'.sprintf( esc_html__( '%s : ', 'ova-brw' ), $opt_name ).'</dt>';
        								$html .= '<dd>'. wp_kses_post( $opt_price ) .'</dd>';
        							}
        						}
        					}
        				} elseif ( 'select' === $type ) {
        					// Option keys
        					$opt_keys = isset( $all_cckf[$k]['ova_options_key'] ) ? (array)$all_cckf[$k]['ova_options_key'] : [];

        					$index 		= array_search( $val, $opt_keys );
        					$opt_qty 	= (int)ovabrw_get_meta_data( $val, $qtys, 1 );

        					if ( false !== $index ) {
        						$opt_name 	= isset( $all_cckf[$k]['ova_options_text'][$index] ) ? $all_cckf[$k]['ova_options_text'][$index] : '';
        						$opt_price 	= isset( $all_cckf[$k]['ova_options_price'][$index] ) ? ovabrw_convert_price_in_admin( $all_cckf[$k]['ova_options_price'][$index], $currency_code ) : 0;
								if ( !$opt_price ) continue;

								// Option quantity
        						if ( $opt_qty ) $opt_price *= $opt_qty;

        						// Get price html
								$opt_price = ovabrw_wc_price( $opt_price * $product_qty, [ 'currency' => $currency_code ] );

        						$html .= '<dt>'.sprintf( esc_html__( '%s : ', 'ova-brw' ), $opt_name ).'</dt>';
								$html .= '<dd>'. wp_kses_post( $opt_price ) .'</dd>';
        					}
        				}
        			}
        		}
        	}

        	return apply_filters( OVABRW_PREFIX.'get_html_cckf', $html, $cckf, $qtys, $args );
        }

        /**
         * Get HTML resources
         */
        public function get_html_resources( $resources = [], $qtys = [], $args = [] ) {
        	// init
        	$html = '';

        	// Check show extra setting
        	if ( 'no' === ovabrw_get_setting( 'booking_form_show_extra', 'no' ) ) return $html;

        	if ( ovabrw_array_exists( $resources ) ) {
        		// Product ID
        		$product_id = (int)ovabrw_get_meta_data( 'product_id', $args );

        		// Get product
        		$product = wc_get_product( $product_id );
        		if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) return $html;

        		// Pick-up date
        		$pickup_date = (int)ovabrw_get_meta_data( 'pickup_date', $args );

        		// Drop-off date
        		$dropoff_date = (int)ovabrw_get_meta_data( 'dropoff_date', $args );

        		// Product quantity
        		$product_qty = (int)ovabrw_get_meta_data( 'quantity', $args, 1 );

        		// Order ID
        		$order_id = (int)ovabrw_get_meta_data( 'order_id', $args );

        		// Currency
        		$currency_code = '';
        		if ( $order_id ) {
        			// Get order
        			$order = wc_get_order( $order_id );
        			if ( $order ) $currency_code = $order->get_currency();
        		}

        		// Get rental time
        		$rental_time = $product->get_rental_time( $pickup_date, $dropoff_date );

        		// Options IDs
        		$opt_ids = $product->get_meta_value( 'resource_id', [] );

        		// Option names
        		$opt_names = $product->get_meta_value( 'resource_name', [] );

        		// Option prices
        		$opt_prices = $product->get_meta_value( 'resource_price', [] );

        		// Option types
        		$opt_types = $product->get_meta_value( 'resource_duration_type', [] );

        		foreach ( $resources as $opt_id ) {
        			if ( !$opt_id ) continue;

        			// Search option ID
        			$index = array_search( $opt_id, $opt_ids );

        			if ( false !== $index ) {
        				// Option name
        				$opt_name = ovabrw_get_meta_data( $index, $opt_names );

        				// Option price
        				$opt_price = (float)ovabrw_get_meta_data( $index, $opt_prices );

        				// Option type
        				$opt_type = ovabrw_get_meta_data( $index, $opt_types );

        				if ( 'total' === $opt_type ) {
        					$opt_price *= $product_qty;
        				} elseif ( 'days' === $opt_type ) {
        					// Get number of rental days
        					$numberof_rental_days = (int)ovabrw_get_meta_data( 'numberof_rental_days', $rental_time );
        					$opt_price *= $numberof_rental_days * $product_qty;
        				} elseif ( 'hours' === $opt_type ) {
        					// Get number of rental hours
        					$numberof_rental_hours = (int)ovabrw_get_meta_data( 'numberof_rental_hours', $rental_time );
        					$opt_price *= $numberof_rental_hours * $product_qty;
        				}

        				// Convert price
        				$opt_price = ovabrw_convert_price_in_admin( $opt_price, $currency_code );

        				// Option qty
        				$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $qtys );
        				if ( $opt_qty ) {
        					$opt_price .= sprintf( esc_html__( ' (x%d)', 'ova-brw' ), $opt_qty );
        				}

        				$html .= '<dt>'.sprintf( esc_html__( '%s: ', 'ova-brw' ), $opt_name ).'</dt>';
        				$html .= '<dd>'.ovabrw_wc_price( $opt_price, [ 'currency' => $currency_code ] ).'</dd>';
        			}
        		}
        	}

        	return apply_filters( OVABRW_PREFIX.'get_html_resources', $html, $resources, $qtys, $args );
        }

        /**
         * Get HTML services
         */
        public function get_html_services( $services = [], $qtys = [], $args = [] ) {
        	// init
        	$html = '';

        	// Check show extra setting
        	if ( 'no' === ovabrw_get_setting( 'booking_form_show_extra', 'no' ) ) return $html;

        	if ( ovabrw_array_exists( $services ) ) {
        		// Product ID
        		$product_id = (int)ovabrw_get_meta_data( 'product_id', $args );

        		// Get product
        		$product = wc_get_product( $product_id );
        		if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) return $html;

        		// Pick-up date
        		$pickup_date = (int)ovabrw_get_meta_data( 'pickup_date', $args );

        		// Drop-off date
        		$dropoff_date = (int)ovabrw_get_meta_data( 'dropoff_date', $args );

        		// Product quantity
        		$product_qty = (int)ovabrw_get_meta_data( 'quantity', $args, 1 );

        		// Order ID
        		$order_id = (int)ovabrw_get_meta_data( 'order_id', $args );

        		// Currency
        		$currency_code = '';
        		if ( $order_id ) {
        			// Get order
        			$order = wc_get_order( $order_id );
        			if ( $order ) $currency_code = $order->get_currency();
        		}

        		// Get rental time
        		$rental_time = $product->get_rental_time( $pickup_date, $dropoff_date );

        		// Option IDs
        		$opt_ids = $product->get_meta_value( 'service_id', [] );

        		// Option names
        		$opt_names = $product->get_meta_value( 'service_name', [] );

        		// Option prices
        		$opt_prices = $product->get_meta_value( 'service_price', [] );

        		// Option type
        		$opt_types = $product->get_meta_value( 'service_duration_type', [] );
        		
        		foreach ( $services as $opt_id ) {
        			if ( !$opt_id ) continue;

        			foreach ( $opt_ids as $k => $opt_values ) {
        				// Search option ID
	        			$index = array_search( $opt_id, $opt_values );

	        			if ( false !== $index ) {
	        				// Option name
	        				$opt_name = isset( $opt_names[$k][$index] ) ? $opt_names[$k][$index] : '';

	        				// Option price
	        				$opt_price = isset( $opt_prices[$k][$index] ) ? (float)$opt_prices[$k][$index] : 0;

	        				// Option type
	        				$opt_type = isset( $opt_types[$k][$index] ) ? $opt_types[$k][$index] : '';

	        				if ( 'total' === $opt_type ) {
	        					$opt_price *= $product_qty;
	        				} elseif ( 'days' === $opt_type ) {
	        					// Get number of rental days
	        					$numberof_rental_days = (int)ovabrw_get_meta_data( 'numberof_rental_days', $rental_time );
	        					$opt_price *= $numberof_rental_days * $product_qty;
	        				} elseif ( 'hours' === $opt_type ) {
	        					// Get number of rental hours
	        					$numberof_rental_hours = (int)ovabrw_get_meta_data( 'numberof_rental_hours', $rental_time );
	        					$opt_price *= $numberof_rental_hours * $product_qty;
	        				}

	        				// Convert price
	        				$opt_price = ovabrw_convert_price_in_admin( $opt_price, $currency_code );

	        				// Option qty
	        				$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $qtys );
	        				if ( $opt_qty ) {
	        					$opt_price .= sprintf( esc_html__( ' (x%d)', 'ova-brw' ), $opt_qty );
	        				}

	        				$html .= '<dt>'.sprintf( esc_html__( '%s: ', 'ova-brw' ), $opt_name ).'</dt>';
        					$html .= '<dd>'.ovabrw_wc_price( $opt_price, [ 'currency' => $currency_code ] ).'</dd>';
	        			}
        			}
        		}
        	}

        	return apply_filters( OVABRW_PREFIX.'get_html_services', $html, $services, $qtys, $args );
        }

        /**
         * Get extra HTML
         */
        public function get_html_extra( $html_cckf = '', $html_resources = '', $html_services = '' ) {
        	$html = '';

            if ( $html_cckf || $html_resources || $html_services ) {
                $html .= '<dl class="variation ovabrw_extra_item">';
                $html .= $html_cckf;
                $html .= $html_resources;
                $html .= $html_services;
                $html .= '</dl>';
            }

            return apply_filters( OVABRW_PREFIX.'get_html_extra', $html, $html_cckf, $html_resources, $html_services );
        }

        /**
         * Get taxes by price
         */
        public function get_taxes_by_price( $product, $price ) {
            if ( !$product || !$price ) return 0;

            // Tax amount
            $taxes = 0;

            if ( $product->is_taxable() ) {
                $tax_rates = WC_Tax::get_rates( $product->get_tax_class() );

                if ( wc_prices_include_tax() ) {
                    $incl_tax = WC_Tax::calc_inclusive_tax( $price, $tax_rates );
                    $taxes    = wc_round_tax_total( array_sum( $incl_tax ) );
                } else {
                    $excl_tax = WC_Tax::calc_exclusive_tax( $price, $tax_rates );
                    $taxes    = wc_round_tax_total( array_sum( $excl_tax ) );
                }
            }

            return apply_filters( OVABRW_PREFIX.'get_taxes_by_price', $taxes, $product, $price );
        }

        /**
         * Get tax amount by tax rates
         */
        public function get_tax_amount_by_tax_rates( $price = 0, $tax_rates = 0, $prices_incl_tax = '' ) {
            if ( !$price || !$prices_incl_tax ) return 0;

            if ( wc_tax_enabled() ) {
                if ( 'yes' == $prices_incl_tax ) {
                    $tax_amount = round( $price - ( $price / ( ( $tax_rates / 100 ) + 1 ) ), wc_get_price_decimals() );
                } else {
                    $tax_amount = round( $price * ( $tax_rates / 100 ), wc_get_price_decimals() );
                }
            }

            return apply_filters( OVABRW_PREFIX.'get_tax_amount_by_tax_rates', $tax_amount );
        }

        /**
         * Get average product review by category
         */
        public function get_average_product_review_by_cagegory( $category_id = false ) {
            if ( !$category_id ) return;

            $average = $count = $total_rating = 0;

            // Get product ids
            $product_ids = get_posts([
                'post_type'         => 'product',
                'post_status'       => 'publish',
                'posts_per_page'    => -1,
                'orderby'           => 'date',
                'order'             => 'DESC',
                'fields'            => 'ids',
                'tax_query'         => [
                    [
                        'taxonomy'  => 'product_cat',
                        'field'     => 'term_id',
                        'terms'     => $category_id,
                        'operator'  => 'IN'
                    ]
                ]
            ]);

            if ( ovabrw_array_exists( $product_ids ) ) {
                foreach ( $product_ids as $product_id ) {
                    $product    = wc_get_product( $product_id );
                    $rating     = $product->get_average_rating();

                    if ( $rating ) {
                        $total_rating += floatval( $rating );
                        $count += 1;
                    }
                }
            }

            if ( floatval( $total_rating ) > 0 && absint( $count ) > 0 ) {
                $average = number_format( $total_rating / $count, 1, '.', ',' );
            }
            
            return apply_filters( OVABRW_PREFIX.'get_average_product_review_by_cagegory', $average, $category_id );
        }
        
        /**
         * Get calendar price HTML
         */
        public function get_calendar_price_html( $price ) {
        	$args = apply_filters( OVABRW_PREFIX.'calendar_price_args', [
                'currency'           => '',
                'decimal_separator'  => wc_get_price_decimal_separator(),
                'decimals'           => (int)wc_get_price_decimals()
            ]);

            // Convert to float
            $price = (float)$price;
            $price = ovabrw_convert_price( $price );

            // Get divide
            $divide = '';

            if ( $price >= 1000000000000 ) { // Trillion
                $price  = number_format( $price / 1000000000000, $args['decimals'], $args['decimal_separator'] );
                $divide = '<span>'.esc_html__( 'T', 'ova-brw-booking' ).'</span>';
            } elseif ( $price >= 1000000000 ) { // Billion
                $price  = number_format( $price / 1000000000, $args['decimals'], $args['decimal_separator'] );
                $divide = '<span>'.esc_html__( 'B', 'ova-brw-booking' ).'</span>';
            } elseif ( $price >= 1000000 ) {  // Million
                $price  = number_format( $price / 1000000, $args['decimals'], $args['decimal_separator'] );
                $divide = '<span>'.esc_html__( 'M', 'ova-brw-booking' ).'</span>';
            } elseif ( $price >= 1000 ) {  // Thousand
                $price  = number_format( $price / 1000, $args['decimals'], $args['decimal_separator'] );
                $divide = '<span>'.esc_html__( 'K', 'ova-brw-booking' ).'</span>';
            }

            // Formats price
            $price = round( (float)$price, $args['decimals'] );

            // Remove trailing zeros and decimal point if needed
            $price = (string) $price;
            if ( strpos( $price, $args['decimal_separator'] ) !== false ) {
                $price = rtrim( rtrim( $price, '0' ), $args['decimal_separator'] );
            }

            // Symbol
            $currency_symbol = '';
            if ( apply_filters( OVABRW_PREFIX.'calendar_price_show_currency_symbol', true ) ) {
                $currency_symbol = apply_filters( OVABRW_PREFIX.'calendar_price_currency_symbol_html', '<span class="currency-symbol">' . get_woocommerce_currency_symbol( $args['currency'] ) . '</span>', $args );
            }

            // HTML price
            $html_price = '<small class="ovabrw-day-price">';
                $html_price .= sprintf( '%s%s%s', $currency_symbol, $price, $divide );
            $html_price .= '</small>';

            return apply_filters( OVABRW_PREFIX.'get_calendar_price_html', wp_kses_post( $html_price ), $args );
        }

        /**
         * Get recaptcha form
         */
        public function get_recaptcha_form( $form = '' ) {
        	if ( 'yes' !== ovabrw_get_setting( 'recapcha_enable', 'no' ) ) return false;
        	if ( 'both' === ovabrw_get_setting( 'recapcha_form' ) ) return true;
        	if ( $form === ovabrw_get_setting( 'recapcha_form' ) ) return true;

        	return false;
        }

        /**
         * Get recaptcha type
         */
        public function get_recaptcha_type() {
        	return apply_filters( OVABRW_PREFIX.'get_recaptcha_type', ovabrw_get_setting( 'recapcha_type', 'v3' ) );
        }

        /**
         * Get recaptcha site key
         */
        public function get_recaptcha_site_key() {
        	// init
        	$site_key = '';

        	// Type
        	$type = $this->get_recaptcha_type();
        	if ( 'v3' === $type ) {
        		$site_key = ovabrw_get_setting( 'recapcha_v3_site_key' );
        	} elseif ( 'v2' === $type ) {
        		$site_key = ovabrw_get_setting( 'recapcha_v2_site_key' );
        	}

        	return apply_filters( OVABRW_PREFIX.'get_recaptcha_site_key', $site_key );
        }

        /**
         * Get recaptcha secret key
         */
        public function get_recaptcha_secret_key() {
        	// init
        	$secret_key = '';

        	// Type
        	$type = $this->get_recaptcha_type();
        	if ( 'v3' === $type ) {
        		$secret_key = ovabrw_get_setting( 'recapcha_v3_secret_key' );
        	} elseif ( 'v2' === $type ) {
        		$secret_key = ovabrw_get_setting( 'recapcha_v2_secret_key' );
        	}

        	return apply_filters( OVABRW_PREFIX.'get_recaptcha_secret_key', $secret_key );
        }

        /**
         * Get recaptcha client IP
         */
        public function get_recaptcha_client_ip() {
        	if ( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
				$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
			} elseif ( !empty( $_SERVER['REMOTE_ADDR'] ) ) {
				$ip = $_SERVER['REMOTE_ADDR'];
			} elseif ( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
				$ip = $_SERVER['HTTP_CLIENT_IP'];
			} else {
				$ip = '0.0.0.0';
			}

			return apply_filters( OVABRW_PREFIX.'get_recaptcha_client_ip', $ip );
        }

        /**
         * Get recaptcha host
         */
        public function get_recaptcha_host() {
        	$host 	= '';
			$url 	= parse_url( site_url() );

			if ( isset( $url['host'] ) && $url['host'] ) {
				$host = $url['host'];
			}

			return apply_filters( OVABRW_PREFIX.'get_recaptcha_host', $host );
        }

        /**
         * Get recaptcha error
         */
        public function get_recaptcha_error_mesg( $code = '' ) {
        	$mesg = apply_filters( OVABRW_PREFIX.'recaptcha_error_message', [
				'default' 					=> esc_html__( 'An error occurred with reCAPTCHA. Please try again later.', 'ova-brw' ),
				'missing-input-secret' 		=> esc_html__( 'The secret parameter is missing.', 'ova-brw' ),
				'invalid-input-secret' 		=> esc_html__( 'The secret parameter is invalid or malformed.', 'ova-brw' ),
				'missing-input-response' 	=> esc_html__( 'The response parameter is missing.', 'ova-brw' ),
				'invalid-input-response' 	=> esc_html__( 'The response parameter is invalid or malformed.', 'ova-brw' ),
				'bad-request' 				=> esc_html__( 'The request is invalid or malformed.', 'ova-brw' ),
				'timeout-or-duplicate' 		=> esc_html__( 'The response is no longer valid: either is too old or has been used previously.', 'ova-brw' )
			]);

			// Get error mesg
			$error = ovabrw_get_meta_data( $code, $mesg, $mesg['default'] );

			return apply_filters( OVABRW_PREFIX.'get_recaptcha_error', $error, $code );
        }

        /**
         * Get recaptcha api response
         */
        public function get_recaptcha_api_response( $token = '' ) {
        	// Params
			$params = [
				'secret'   => $this->get_recaptcha_secret_key(),
				'response' => $token,
				'remoteip' => $this->get_recaptcha_client_ip()
			];

			// Options
			$opts = [
				'http' => [
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => http_build_query( $params )
				]
			];

			$context = stream_context_create( $opts );
			$res     = file_get_contents( 'https://www.google.com/recaptcha/api/siteverify', false, $context );
			$res     = json_decode( $res, true );

			return apply_filters( OVABRW_PREFIX.'recaptcha_api_response', $res, $token );
        }

        /**
         * Verify recaptcha version 2
         */
        public function verify_recaptcha_v2( $token ) {
        	// Get recaptcha error
			$error = $this->get_recaptcha_error_mesg();

			// Get api response
			if ( $token ) {
				$response 	= $this->get_recaptcha_api_response( $token );
				$success 	= ovabrw_get_meta_data( 'success', $response );
				$hostname 	= ovabrw_get_meta_data( 'hostname', $response );

				if ( $success && $hostname == $this->get_recaptcha_host() ) {
					$error = '';
				} else {
					if ( isset( $res['error-codes'][0] ) && $res['error-codes'][0] ) {
				        $error = $this->get_recaptcha_error_mesg( $res['error-codes'][0] );
				    }
				}
			}

			return apply_filters( OVABRW_PREFIX.'verify_recaptcha_v2', $error, $token );
        }

        /**
         * Verify recaptcha version 3
         */
        public function verify_recaptcha_v3( $token ) {
        	// Get recaptcha error
			$error = $this->get_recaptcha_error_mesg();

			// Score
			$score = apply_filters( OVABRW_PREFIX.'recaptcha_score', 0.5 );

			// Get api response
			if ( $token ) {
				$response 	= $this->get_recaptcha_api_response( $token );
				$success 	= ovabrw_get_meta_data( 'success', $response );
				$hostname 	= ovabrw_get_meta_data( 'hostname', $response );
				$action 	= ovabrw_get_meta_data( 'action', $response );
				$res_score 	= (float)ovabrw_get_meta_data( 'score', $response );

				if ( $success && $hostname == $this->get_recaptcha_host() && $action == 'ovabrwVerifyForm' && $res_score > $score ) {
					$error = '';
				} else {
					if ( isset( $res['error-codes'][0] ) && $res['error-codes'][0] ) {
				        $error = $this->get_recaptcha_error_mesg( $res['error-codes'][0] );
				    }
				}
			}

			return apply_filters( OVABRW_PREFIX.'verify_recaptcha_v3', $error, $token );
        }

        /**
         * Loading recaptcha
         */
        public function loading_recaptcha() {
			if ( 'yes' === ovabrw_get_setting( 'recapcha_enable', 'no' ) ) {
				$type 		= $this->get_recaptcha_type();
				$site_key 	= $this->get_recaptcha_site_key();

				wp_enqueue_script( 'ovabrw_recapcha_loading', OVABRW_PLUGIN_URI.'assets/js/frontend/ova-brw-recaptcha.min.js', [], false, false );
				wp_localize_script( 'ovabrw_recapcha_loading', 'ovabrw_recaptcha', [
					'site_key' 	=> $site_key,
					'form' 		=> ovabrw_get_setting( 'recapcha_form' )
				]);

				if ( 'v3' === $type ) {
					wp_enqueue_script( 'ovabrw_recaptcha', 'https://www.google.com/recaptcha/api.js?onload=ovabrwLoadingReCAPTCHAv3&render='. esc_attr( $site_key ), [], false, false );
				} elseif ( 'v2' === $type ) {
					wp_enqueue_script( 'ovabrw_recaptcha', 'https://www.google.com/recaptcha/api.js?onload=ovabrwLoadingReCAPTCHAv2&render=explicit', [], false, false );
				}
			}
        }

		/**
		 * Main OVABRW_Get_Data Instance.
		 */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
        }

        /**
         * Get HTML category dropdown
         */
        public function get_html_dropdown_categories( $selected = '', $required = '', $exclude_id = '', $label = '', $include_id = '' ) {
        	if ( !$label ) {
                $label = esc_html__( 'Select Category', 'ova-brw' );
            }
            
            // Agruments
            $args = [
            	'show_option_all'    => '',
                'show_option_none'   => $label,
                'option_none_value'  => '',
                'orderby'            => 'ID',
                'order'              => 'ASC',
                'show_count'         => 0,
                'hide_empty'         => 0,
                'child_of'           => 0,
                'exclude'            => $exclude_id,
                'include'            => $include_id,
                'echo'               => 0,
                'selected'           => $selected,
                'hierarchical'       => 1,
                'name'               => 'cat',
                'id'                 => '',
                'class'              => 'postform '.$required,
                'depth'              => 0,
                'tab_index'          => 0,
                'taxonomy'           => 'product_cat',
                'hide_if_empty'      => false,
                'value_field'        => 'slug'
            ];

            return urldecode( wp_dropdown_categories( $args ) );
        }

        /**
         * Get HTML attributes dropdown
         */
        public function get_html_dropdown_attributes( $label = '' ) {
        	if ( !$label ) $label = esc_html__( 'Select Attribute', 'ova-brw' );

        	// Attribute HTML
        	$html_attr = '';

        	// Attribute value HTML
        	$html_attr_value = '';
            
            // Get attributes
            $attributes = wc_get_attribute_taxonomies();
            if ( ovabrw_array_exists( $attributes ) ) {
            	// Default attribute
            	$default_attr = ovabrw_get_meta_data( 'attribute', $_REQUEST );

            	// Attribute HTML
            	$html_attr .= '<select name="attribute" class="ovabrw_attribute">';
            		$html_attr .= '<option value="">'. esc_html( $label ) .'</option>';

                foreach ( $attributes as $attr ) {
                    if ( taxonomy_exists( wc_attribute_taxonomy_name( $attr->attribute_name ) ) ) {
                        $html_attr .= '<option value="'.esc_attr( $attr->attribute_name ).'"'.ovabrw_selected( $default_attr, $attr->attribute_name, false ).'>';
                        	$html_attr .= esc_html__( $attr->attribute_label );
                        $html_attr .= '</option>';

                        // Get attribute terms
                        $attr_terms = get_terms( wc_attribute_taxonomy_name( $attr->attribute_name ), 'orderby=name&hide_empty=0' );
                        if ( ovabrw_array_exists( $attr_terms ) ) {
                        	// Default attribute value
                        	$default_attr_value = ovabrw_get_meta_data( $attr->attribute_name, $_REQUEST );

                            $html_attr_value .= '<div class="label_search s_field ovabrw-value-attribute" id="'. esc_attr( $attr->attribute_name ) .'">';
                            	$html_attr_value .= '<select name="'. esc_attr( $attr->attribute_name ) .'">';

                            	// Loop
	                            foreach ( $attr_terms as $attr_value ) {
	                                $html_attr_value .= '<option value="'. esc_attr( $attr_value->slug ) .'"'. ovabrw_selected( $default_attr_value, $attr_value->slug, false ) .'>';
	                                	$html_attr_value .= esc_html( $attr_value->name );
	                                $html_attr_value .= '</option>';
	                            } // END foreach

                            	$html_attr_value .= '</select>';
                            $html_attr_value .= '</div>';
                        }// END if
                    } // END if
                } // END foreach

                $html_attr .= '</select>';
            }

            return apply_filters( OVABRW_PREFIX.'get_html_dropdown_attributes', [
            	'html_attr' 		=> $html_attr,
            	'html_attr_value' 	=> $html_attr_value
            ], $label );
        }

        /**
         * Get taxonomies search HTML dropdown
         */
        public function get_html_dropdown_taxonomies_search( $slug = '', $name = '', $selected = '', $class = '' ) {
            $args = [
            	'show_option_all'    => '',
                'show_option_none'   => esc_html__( 'Select ', 'ova-brw' ) . esc_html( $name ) ,
                'option_none_value'  => '',
                'orderby'            => 'ID',
                'order'              => 'ASC',
                'show_count'         => 0,
                'hide_empty'         => 0,
                'child_of'           => 0,
                'exclude'            => '',
                'include'            => '',
                'echo'               => 0,
                'selected'           => $selected,
                'hierarchical'       => 1,
                'name'               => $slug.'_name',
                'id'                 => '',
                'class'              => 'custom_taxonomy '.$class,
                'depth'              => 0,
                'tab_index'          => 0,
                'taxonomy'           => $slug,
                'hide_if_empty'      => false,
                'value_field'        => 'slug'
            ];

            return apply_filters( OVABRW_PREFIX.'get_html_dropdown_taxonomies_search', wp_dropdown_categories( $args ), $slug, $name, $selected );
        }

        /**
         * Get pagination HTML
         */
        public function get_html_pagination_ajax( $max_num_pages, $limit, $current ) {
            $html   = '';
            $pages  = ceil( $max_num_pages / $limit );

            if ( $pages > 1 ) {
                $html .= '<ul>';

                if ( $current > 1 ) {
                    $html .= '<li><span data-paged="'.( $current - 1 ).'" class="prev page-numbers" >'.esc_html__( 'Previous', 'ova-brw' ).'</span></li>';
                }

                for ( $i = 1; $i <= $pages; $i++ ) {
                    if ( $current == $i ) {
                        $html .='<li><span data-paged="'. esc_attr( $i ) .'" class="prev page-numbers current" >'.esc_html( $i ).'</span></li>';
                    } else {
                        $html .= '<li><span data-paged="'. esc_attr( $i ) .'" class="prev page-numbers" >'.esc_html( $i ).'</span></li>';
                    }
                }

                if ( $current < $pages ) {
                    $html .= '<li><span data-paged="'. ( $current + 1 ) .'" class="next page-numbers" >'.esc_html__( 'Next', 'ova-brw' ).'</span></li>';
                }
            }

            return apply_filters( OVABRW_PREFIX.'get_pagination_ajax', $html, $max_num_pages, $limit, $current );
        }

        /**
         * Get product from search
         */
        public function get_product_from_search( $data = [] ) {
            $per_page 	= ovabrw_get_meta_data( 'posts_per_page', $data, 12 );
            $paged 		= ovabrw_get_meta_data( 'paged', $data, 1 );
            $orderby    = ovabrw_get_meta_data( 'orderby', $data, 'date' );
            $order      = ovabrw_get_meta_data( 'order', $data, 'DESC' );

            // Base query
            $base_query = [
            	'post_type'         => 'product',
                'post_status'       => 'publish',
                'posts_per_page'    => $per_page,
                'paged'    			=> $paged,
                'orderby'           => $orderby,
                'order'             => $order,
                'tax_query'         => [
                    [
                        'taxonomy' => 'product_type',
                        'field'    => 'slug',
                        'terms'    => 'ovabrw_car_rental', 
                    ]
                ]
            ];

            // Category ids
            $category_ids = ovabrw_get_meta_data( 'category_ids', $data );
            if ( ovabrw_array_exists( $category_ids ) ) {
            	$cat_query['tax_query'] = [
                    [
                        'taxonomy'  => 'product_cat',
                        'field'     => 'term_id',
                        'terms'     => $category_ids,
                        'operator'  => 'IN'
                    ]
                ];

                $base_query = array_merge_recursive( $base_query, $cat_query );
            }

            // Category slug
            $category_slug = ovabrw_get_meta_data( 'category_slug', $data );
            if ( ovabrw_array_exists( $category_slug ) ) {
            	$cat_query['tax_query'] = [
                    [
                        'taxonomy'  => 'product_cat',
                        'field'     => 'slug',
                        'terms'     => $category_ids,
                        'operator'  => 'IN'
                    ]
                ];

                $base_query = array_merge_recursive( $base_query, $cat_query );
            }

            // Term ID
            if ( ovabrw_get_meta_data( 'term_id', $data ) ) {
                $args_taxonomy['tax_query'] = [
                    [
                        'taxonomy'  => 'product_cat',
                        'field'     => 'term_id',
                        'terms'     => $data['term_id'],
                        'operator'  => 'IN'
                    ]
                ];

                $base_query = array_merge_recursive( $base_query, $args_taxonomy );
            }

            // Get products
            $products = new WP_Query( $base_query );

            return apply_filters( OVABRW_PREFIX.'get_product_from_search', $products, $data );
        }
        
        /**
         * Get post vehicle
         */
        public function get_post_vehicle( $vehicle_id ) {
        	if ( !$vehicle_id ) return false;

        	// Vehicle data
        	$vehicle_data = [];

        	// Get post ids
        	$vehicle_ids = get_posts([
        		'post_type'         => 'vehicle',
	            'post_status'       => 'publish',
	            'posts_per_page'    => 1,
	            'fields'            => 'ids',
	            'meta_query'        => [
	            	[
	                	'key'     => 'ovabrw_id_vehicle',
	                    'value'   => $vehicle_id,
	                    'compare' => '='
	                ]
	            ]
        	]);

        	if ( ovabrw_array_exists( $vehicle_ids ) ) {
        		// Post id
	    		$post_id = reset( $vehicle_ids );

				// ID
				$vehicle_data['id'] = ovabrw_get_post_meta( $post_id, 'id_vehicle' );

				// Title
				$vehicle_data['title'] = get_the_title( $post_id );

				// Required location
				$vehicle_data['required_location'] = ovabrw_get_post_meta( $post_id, 'vehicle_require_location' );

				// Location
				$vehicle_data['location'] = ovabrw_get_post_meta( $post_id, 'id_vehicle_location' );

				// Disabled dates
				$disabled_dates = ovabrw_get_post_meta( $post_id, 'id_vehicle_untime_from_day' );

				// Disabled start date
				$vehicle_data['disabled_start'] = strtotime( ovabrw_get_meta_data( 'startdate', $disabled_dates ) );

				// Disabled end date
				$vehicle_data['disabled_end'] = strtotime( ovabrw_get_meta_data( 'enddate', $disabled_dates ) );
        	} // END if

        	return apply_filters( OVABRW_PREFIX.'get_post_vehicle', $vehicle_data, $vehicle_id );
        }

        /**
         * Get available items from search
         */
        public function get_available_items_from_search( $args ) {
        	if ( !ovabrw_array_exists( $args ) ) return false;

        	// Product name
        	$product_name = sanitize_text_field( ovabrw_get_meta_data( 'product_name', $args ) );

        	// Pick-up location
        	$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $args ) );

        	// Drop-off location
        	$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $args ) );

        	// Pick-up date
        	$pickup_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'pickup_date', $args ) ) );

        	// Drop-off date
        	$dropoff_date = strtotime( sanitize_text_field( ovabrw_get_meta_data( 'dropoff_date', $args ) ) );
        	if ( !$dropoff_date && $pickup_date ) {
        		$dropoff_date = $pickup_date + 1;
        	}

        	// Map duration
        	$duration = (int)ovabrw_get_meta_data( 'duration', $args );
        	if ( $duration && $pickup_date ) {
        		$dropoff_date = $pickup_date + $duration;
        	}

        	// Orderby
        	$orderby = sanitize_text_field( ovabrw_get_meta_data( 'orderby', $args, 'date' ) );

        	// Order
        	$order = sanitize_text_field( ovabrw_get_meta_data( 'order', $args, 'DESC' ) );

        	// Attribute
        	$attribute = sanitize_text_field( ovabrw_get_meta_data( 'attribute', $args ) );

        	// Attribute value
        	$attribute_value = sanitize_text_field( ovabrw_get_meta_data( $attribute, $args ) );

        	// Category
        	$cat = sanitize_text_field( ovabrw_get_meta_data( 'cat', $args ) );

        	// Product tag
        	$product_tag = sanitize_text_field( ovabrw_get_meta_data( 'product_tag', $args ) );

        	// Quantity
        	$quantity = (int)ovabrw_get_meta_data( 'quantity', $args, 1 );

        	// Number of adults
        	$numberof_adults = sanitize_text_field( ovabrw_get_meta_data( 'adults', $args ) );

        	// Number of children
        	$numberof_children = sanitize_text_field( ovabrw_get_meta_data( 'children', $args ) );

        	// Number of babies
        	$numberof_babies = sanitize_text_field( ovabrw_get_meta_data( 'babies', $args ) );

        	// Number of seats
        	$seats = (int)ovabrw_get_meta_data( 'seats', $args );

        	// Taxonomies
        	$taxonomy_queries = [];

        	// Get taxonomies
            $taxonomies = ovabrw_create_type_taxonomies();
            if ( ovabrw_array_exists( $taxonomies ) ) {
                foreach ( $taxonomies as $taxonomy ) {
                    $slug = ovabrw_get_meta_data( 'slug', $taxonomy );

                    if ( $slug ) {
                        $value = sanitize_text_field( ovabrw_get_meta_data( $slug.'_name', $args ) );
                        if ( $value ) {
                            $taxonomy_queries[] = [
                                'taxonomy'  => $slug,
                                'field'     => 'slug',
                                'terms'     => $value
                            ];
                        }
                    }
                }
            }

            // Item ids
            $item_ids = $tax_query = [];

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

            // Product name
            if ( $product_name ) {
            	$args_base['s'] = preg_replace( "/[^a-zA-Z]+/", " ", $product_name );
            }

            // Product cateogory
            if ( $cat ) {
            	$taxonomy_queries[] = [
                    'taxonomy'  => 'product_cat',
                    'field'     => 'slug',
                    'terms'     => $cat
                ];
            } // END if

           	// Attribute
           	if ( $attribute && $attribute_value ) {
           		$taxonomy_queries[] = [
                    'taxonomy'  => 'pa_' . $attribute,
                    'field'     => 'slug',
                    'terms'     => [ $attribute_value ],
                    'operator'  => 'IN',
                ];
           	} // END if

           	// Product tag
            if ( $product_tag ) {
                $taxonomy_queries[] = [
                    'taxonomy'  => 'product_tag',
                    'field'     => 'name',
                    'terms'     => $product_tag
                ];
            }

           	// Tax query
           	if ( ovabrw_array_exists( $taxonomy_queries ) ) {
           		$tax_query = [
                    'tax_query' => [
                        $taxonomy_queries
                    ]
                ];
           	}

           	// Meta query
           	$meta_query = $args_meta_query = [];

           	// Adults
           	if ( $numberof_adults ) {
           		$args_meta_query[] = [
           			'key'     => 'ovabrw_max_adults',
                    'value'   => $numberof_adults,
                    'type'    => 'numeric',
                    'compare' => '>=',
           		];
           	} // END if

           	// Children
           	if ( $numberof_children ) {
           		$args_meta_query[] = [
           			'key'     => 'ovabrw_max_adults',
                    'value'   => $numberof_children,
                    'type'    => 'numeric',
                    'compare' => '>=',
           		];
           	} // END if

           	// Babies
           	if ( $numberof_babies ) {
           		$args_meta_query[] = [
           			'key'     => 'ovabrw_max_babies',
                    'value'   => $numberof_babies,
                    'type'    => 'numeric',
                    'compare' => '>=',
           		];
           	} // END if

           	// Number of seats
           	if ( $seats ) {
           		$args_meta_query[] = [
                    'key'     => 'ovabrw_max_seats',
                    'value'   => $seats,
                    'type'    => 'numeric',
                    'compare' => '>=',
                ];
           	} // END if

           	// Meta query
           	if ( ovabrw_array_exists( $args_meta_query ) ) {
           		$meta_query = [
                    'meta_query' => [
                        'relation'  => 'AND',
                        $args_meta_query
                    ]
                ];
           	}

           	// Merge queries
           	$args_base = array_merge_recursive( $args_base, $tax_query, $meta_query );

           	// Get product ids
           	$product_ids = get_posts( $args_base );
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

	                // Get items available
	                if ( $pickup_date && $dropoff_date ) {
	                	$items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'search' );
	                	if ( is_array( $items_available ) ) $items_available = count( $items_available );
	                	if ( $items_available >= $quantity ) {
	                		array_push( $item_ids, $product_id );
	                	}
	                } else {
	                	array_push( $item_ids, $product_id );
	                } // END if
                } // END foreach
            } // END if

            // Item ids
            if ( ovabrw_array_exists( $item_ids ) ) {
            	// Get paged
            	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1;

            	// Get posts per page
                $posts_per_page = wc_get_default_products_per_row() * wc_get_default_product_rows_per_page();

                // Query
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
                    $args_query['orderby']  = 'meta_value_num';
                    $args_query['meta_key'] = '_wc_average_rating';
                } elseif ( 'rental_order' == $args_query['orderby'] ) {
                    $args_query['orderby']  = 'meta_value_num';
                    $args_query['meta_key'] = 'ovabrw_car_order';
                }

                // Get products
                $products = new WP_Query( $args_query );

                return apply_filters( OVABRW_PREFIX.'get_available_items_from_search', $products, $args );
            }

        	return apply_filters( OVABRW_PREFIX.'get_available_items_from_search', false, $args );
        }

        /**
	     * Get full days
	     */
	    public function get_full_days( $part_day, $date_format = 'Y-m-d' ) {
	    	if ( !ovabrw_array_exists( $part_day ) ) return false;

	    	// init full days
	    	$full_days = [];

	    	// Daily
	    	$daily = [];

	    	// Loop
	    	foreach ( $part_day as $event ) {
	    		// Start date
	    		$start = strtotime( $event['start'] );
	    		if ( !$start ) continue;

	    		// Get date
	    		$date = gmdate( 'Y-m-d', $start );

	    		// End date
	    		$end = strtotime( $event['end'] );
	    		if ( !$end ) continue;

	    		// Convert to Y-m-d strtotime
	    		$start 	= strtotime( gmdate( 'Y-m-d', $start ) );
	    		$end 	= strtotime( gmdate( 'Y-m-d', $end ) );

	    		// Check start & end dates
	    		if ( $start != $end ) continue;

	    		$daily[$start][] = [
	    			'start' => $event['start'],
	    			'end' 	=> $event['end'],
	    			'qty' 	=> $event['quantity']
	    		];
	    	} // END loop

	    	// Loop daily
	    	if ( ovabrw_array_exists( $daily ) ) {
	    		foreach ( $daily as $timestamp => $periods ) {
	    			// Get date
	    			$date = gmdate( $date_format, $timestamp );

	    			// Check is fully
	    			if ( $this->is_day_fully( $periods, $date ) ) {
	    				// Get quantity
	    				$qty = array_sum( array_column( $periods, 'qty' ) );
						$qty = floor( $qty / count( $periods ) );

						// Add date
	    				$full_days[$date] = $qty;
	    			}
	    		}
	    	} // END

	    	return apply_filters( OVABRW_PREFIX.'get_full_days', $full_days, $part_day );
	    }

        /**
         * is day fully
         */
        public function is_day_fully( $periods, $date ) {
        	// init
        	$result = false;

	    	// Start of day
	    	$startof_day = strtotime( $date . ' 00:00' );

	    	// End of day
    		$endof_day = strtotime( $date. ' 23:59' );

    		// Convert to timestamps and sort
		    $intervals = array_map( function( $event ) {
		        return [
		            'start' => strtotime( $event[ 'start' ] ),
		            'end' 	=> strtotime( $event[ 'end' ] )
		        ];
		    }, $periods );

		    // Sort
		    usort( $intervals, fn($a, $b) => $a['start'] <=> $b['start']);

		    // Current date
		    $current = $startof_day;

		    // Loop
		    foreach ( $intervals as $item ) {
		    	// Check start date
		    	if ( $item['start'] > $current ) {
		    		$result = false;

		    		// Break out of the loop
		    		break;
		    	}

		    	// Update current date
		    	if ( $item['end'] > $current ) {
		    		$current = $item['end'];
		    	}
		    } // END loop

		    // Check is day fully
		    if ( $current >= $endof_day ) $result = true;

    		return apply_filters( OVABRW_PREFIX.'is_day_fully', $result, $periods, $date );
	    }
	}
}