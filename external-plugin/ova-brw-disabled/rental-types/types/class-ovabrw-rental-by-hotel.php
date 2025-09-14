<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Rental By Hotel
 */
if ( !class_exists( 'OVABRW_Rental_By_Hotel' ) ) {

	class OVABRW_Rental_By_Hotel extends OVABRW_Rental_Types {

		/**
		 * Constructor
		 */
		public function __construct( $rental_id = 0 ) {
			parent::__construct( $rental_id );
		}

		/**
		 * Get type
		 */
		public function get_type() {
			return 'hotel';
		}

		/**
		 * Get meta fields
		 */
		public function get_meta_fields() {
			return (array)apply_filters( $this->prefix.$this->get_type().'_get_meta_fields', [
				'rental-type',
				'price-hotel',
				'insurance',
				'deposit',
				'inventory',
				'guests',
				'daily-price',
				'specifications',
				'features',
				'discounts',
				'special-times',
				'resources',
				'services',
				'allowed-dates',
				'disabled-dates',
				'advanced-start',
				'product-templates',
				'booking-conditions',
				'custom-checkout-fields',
				'show-quantity',
				'pickup-date',
				'dropoff-date',
				'extra-tab',
				'price-format',
				'frontend-order',
				'advanced-end',
				'map'
			]);
		}

		/**
		 * Get create booking meta fields
		 */
		public function get_create_booking_meta_fields() {
			return (array)apply_filters( $this->prefix.$this->get_type().'_get_create_booking_meta_fields', [
				'price',
				'pickup-date',
				'dropoff-date',
				'guests',
				'quantity',
				'custom-checkout-fields',
				'resources',
				'services',
				'insurance',
				'deposit',
				'remaining',
				'total',
				'error'
			], $this );
		}

		/**
		 * Get html price
		 */
		public function get_price_html( $price_html = '', $currency = '' ) {
			$price = (float)$this->get_meta_value( 'regular_price_hotel' );
			if ( !$price ) {
				$price = (float)get_post_meta( $this->get_id(), '_regular_price', true );
			}

			// New price
			$new_price = sprintf( esc_html__( '%s / Night', 'ova-brw' ), ovabrw_wc_price( $price, [ 'currency' => $currency ] ) );

			return apply_filters( $this->prefix.'get_product_price_html', $new_price, $price_html, $currency, $this );
		}

		/**
	     * Get datepicker options
	     */
	    public function get_datepicker_options() {
	    	// Get datepicker options
        	$datepicker = OVABRW()->options->get_datepicker_options();

        	// Date format
	    	$date_format = OVABRW()->options->get_date_format();

	    	// Time format
	    	$time_format = OVABRW()->options->get_time_format();

	    	// Rental type
	    	$datepicker['rentalType'] = $this->get_type();

	        // RangePlugin
        	$datepicker['RangePlugin']['locale']['one'] 	= esc_html__( 'night', 'ova-brw' );
        	$datepicker['RangePlugin']['locale']['other'] 	= esc_html__( 'nights', 'ova-brw' );

        	// Min days
        	$min_days = floor( (float)$this->get_meta_value( 'rent_day_min' ) );
        	if ( $min_days > 1 ) {
        		$min_days += 1;
        	} else {
        		$min_days = 2;
        	}

        	// Add min days
        	$datepicker['LockPlugin']['minDays'] = $min_days;

        	// Max days
        	$max_days = floor( (float)$this->get_meta_value( 'rent_day_max' ) );
        	if ( $max_days ) {
        		// Add max days
        		$datepicker['LockPlugin']['maxDays'] = $max_days + 1;
        	}

        	// Min date
        	$min_date = $datepicker['LockPlugin']['minDate'];
        	if ( !$min_date || strtotime( $min_date ) < current_time( 'timestamp' ) ) {
	            $min_date = gmdate( $date_format, current_time( 'timestamp' ) );
	        }

	        // Preparation time
	        $preparation_time = $this->get_preparation_time();
	        if ( $preparation_time && strtotime( $preparation_time ) > strtotime( $min_date ) ) {
	        	$min_date = $preparation_time;
	        }

	        // Update min date & start date
	        $datepicker['LockPlugin']['minDate']    = $min_date;
	        $datepicker['startDate']                = $min_date;
	        $datepicker['timestamp'] 				= time();

	        // Disable weekdays
	        $disable_weekdays = $this->get_disable_weekdays();
	        if ( ovabrw_array_exists( $disable_weekdays ) ) {
	            $datepicker['disableWeekDays'] = $disable_weekdays;
	        }

	        // Allowed dates
	        $allowed_dates = $this->get_allowed_dates( $date_format );
	        if ( ovabrw_array_exists( $allowed_dates ) ) {
	        	$datepicker['allowedDates'] = ovabrw_array_merge_unique( $datepicker['allowedDates'], $allowed_dates );

	        	// Get start date
				$start_date = $this->get_start_date( $date_format );
				if ( $start_date ) $datepicker['startDate'] = $start_date;
	        }

	        // Disabled dates
	        $disabled_dates = $this->get_disabled_dates();
	        if ( ovabrw_array_exists( ovabrw_get_meta_data( 'full_day', $disabled_dates ) ) ) {
	        	$datepicker['disableDates'] = ovabrw_array_merge_unique( $datepicker['disableDates'], $disabled_dates['full_day'] );
	        }

	        // Booked dates
	        $booked_dates = $this->get_booked_dates();
	        if ( ovabrw_array_exists( ovabrw_get_meta_data( 'full_day', $booked_dates ) ) ) {
	        	$datepicker['bookedDates'] = ovabrw_array_merge_unique( $datepicker['bookedDates'], $booked_dates['full_day'] );
	        }

	        // Show price on Calendar
	        if ( 'yes' === ovabrw_get_option( 'show_price_input_calendar', 'yes' ) ) {
	        	// Regular price
				$datepicker['regularPrice'] = $this->get_calendar_regular_price();

				// Daily prices
	            $daily_prices = $this->get_calendar_daily_prices();
	            if ( ovabrw_array_exists( $daily_prices ) ) {
	                $datepicker['dailyPrices'] = $daily_prices;
	            }

	            // Special prices
	            $special_prices = $this->get_calendar_special_prices();
	            if ( ovabrw_array_exists( $special_prices ) ) {
	                $datepicker['specialPrices'] = $special_prices;
	            }
	        }

        	return apply_filters( $this->prefix.'get_datepicker_options', $datepicker, $this );
	    }

	    /**
	     * Get regular rental price
	     */
	    public function get_calendar_regular_price() {
	    	// init
	    	$regular_price = $this->get_meta_value( 'regular_price_hotel' );

	    	// Get price html
	    	if ( $regular_price ) {
	    		$regular_price = OVABRW()->options->get_calendar_price_html( $regular_price );
	    	}

	    	return apply_filters( $this->prefix.'get_calendar_regular_price', $regular_price, $this );
	    }

	    /**
	     * Get calendar daily prices
	     */
	    public function get_calendar_daily_prices() {
	    	// init
	    	$daily_prices = [];

	    	// Days of the week
	    	$days_of_week = [
		        '1' => 'monday',
		        '2' => 'tuesday',
		        '3' => 'wednesday',
		        '4' => 'thursday',
		        '5' => 'friday',
		        '6' => 'saturday',
		        '7' => 'sunday'
	    	];

	    	// Filter price
	    	$filter_price = apply_filters( $this->prefix.'show_price_calendar_type', 'highest' );

	    	foreach ( $days_of_week as $number_day => $str_day ) {
    			$price = $this->get_meta_value( 'daily_'.$str_day );

    			if ( '' !== $price ) {
                	$daily_prices[$number_day] = OVABRW()->options->get_calendar_price_html( $price );
                }
    		}

	    	return apply_filters( $this->prefix.'get_calendar_daily_prices', $daily_prices, $this );
	    }

	    /**
	     * Get calendar special prices
	     */
	    public function get_calendar_special_prices() {
	    	// init
	    	$special_prices = [];

	    	// Prices
			$prices = $this->get_meta_value( 'rt_price' );

			// From dates
			$from_dates = $this->get_meta_value( 'rt_startdate' );

			// To dates
			$to_dates = $this->get_meta_value( 'rt_enddate' );

	    	// Loop
	    	if ( ovabrw_array_exists( $prices ) ) {
				foreach ( $prices as $k => $price ) {
					$from 	= strtotime( ovabrw_get_meta_data( $k, $from_dates ) );
					$to 	= strtotime( ovabrw_get_meta_data( $k, $to_dates ) );

					if ( '' === $price || !$from || !$to ) continue;

					$special_prices[] = [
						'from' 	=> $from,
						'to' 	=> $to,
						'price' => OVABRW()->options->get_calendar_price_html( $price )
					];
				}
			} // END loop

	    	return apply_filters( $this->prefix.'get_calendar_special_prices', $special_prices, $this );
	    }

		/**
	     * Get disabled dates
	     */
	    public function get_disabled_dates( $view = '' ) {
	    	// Disabled full day
	    	$full_day = [];

	    	// Disabled part of day
	    	$part_day = [];

	    	// From dates
	    	$from_dates = $this->get_meta_value( 'untime_startdate' );

	    	// To dates
	    	$to_dates = $this->get_meta_value( 'untime_enddate' );

	    	if ( ovabrw_array_exists( $from_dates ) ) {
	    		// Date format
	    		$date_format = '';
	    		if ( 'calendar' === $view ) $date_format = 'Y-m-d';

	    		foreach ( $from_dates as $k => $from ) {
	    			// From date
	    			$from = strtotime( $from );
	    			if ( !$from ) continue;

	    			// To date
	    			$to = strtotime( ovabrw_get_meta_data( $k, $to_dates ) );
	    			if ( !$to || $to < current_time( 'timestamp' ) ) continue;

	    			// Number of days between
	    			$days_between = ovabrw_numberof_days_between( $from, $to );

	    			if ( 0 == $days_between ) {
    					$full_day[] = gmdate( $date_format, $from );
    				} elseif ( 1 == $days_between ) {
    					$full_day[] = gmdate( $date_format, $from );
    					$full_day[] = gmdate( $date_format, $to );
    				} else {
    					// Get date range
    					$date_range = ovabrw_get_date_range( $from, $to, $date_format );

    					if ( ovabrw_array_exists( $date_range ) ) {
    						$full_day = ovabrw_array_merge_unique( $full_day, $date_range );
    					}
    				}
    			}
	    	}

	    	// Results
	    	$results = [
	    		'full_day' => $full_day,
	    		'part_day' => $part_day
	    	];

	    	return apply_filters( $this->prefix.'get_disabled_dates', $results, $view, $this );
	    }

	    /**
	     * Get new date
	     */
	    public function get_new_date( $args = [] ) {
	    	// Pick-up date
	    	$pickup_date = ovabrw_get_meta_data( 'pickup_date', $args );

	    	// Drop-off date
	    	$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $args );

	    	// Check dates exists
	    	if ( !$pickup_date && !$dropoff_date ) return false;

	    	// Drop-off date not exists
	    	if ( !$dropoff_date && !$this->product->show_date_field( 'dropoff' ) ) {
	    		$dropoff_date = $pickup_date;
	    	}

	    	// Check pick-up & drop-off dates
	    	if ( !$pickup_date || !$dropoff_date ) return false;

	    	return apply_filters( $this->prefix.'get_new_date', [
	    		'pickup_date' 	=> $pickup_date,
	    		'dropoff_date' 	=> $dropoff_date
	    	], $args );
	    }

	    /**
	     * Booking validation
	     */
	    public function booking_validation( $pickup_date, $dropoff_date, $args = [] ) {
	    	// Hook name
	    	$hook_name = $this->prefix.'booking_validation';

	    	// Pick-up date
	    	$pickup_label = $this->product->get_date_label();
	    	if ( !$pickup_date ) {
	    		$mesg = sprintf( esc_html__( '%s is required', 'ova-brw' ), $pickup_label );
	    		return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
	    	}

	    	// Current time
			$current_time = $this->get_current_time();
			if ( $pickup_date < $current_time ) {
				$mesg = sprintf( esc_html__( '%s must be greater than current time', 'ova-brw' ), $pickup_label );
	    		return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

	    	// Drop-off date
	    	$dropoff_label = $this->product->get_date_label( 'dropoff' );
	    	if ( !$dropoff_date ) {
				$mesg = sprintf( esc_html__( '%s is required', 'ova-brw' ), $dropoff_label );
	    		return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Pick-up & Drop-off dates
			if ( $pickup_date > $dropoff_date ) {
				$mesg = sprintf( esc_html__( '%s must be greater than %s', 'ova-brw' ), $dropoff_label, $pickup_label );
	    		return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Min days validation
			$mesg = $this->min_days_validation( $pickup_date, $dropoff_date );
			if ( $mesg ) {
				return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Max days validation
			$mesg = $this->max_days_validation( $pickup_date, $dropoff_date );
			if ( $mesg ) {
				return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Preparation time
			$mesg = $this->preparation_time_validation( $pickup_date, $dropoff_date );
			if ( $mesg ) {
				return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Disable weekdays
			$mesg = $this->disable_weekdays_validation( $pickup_date, $dropoff_date );
			if ( $mesg ) {
				return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Disabled dates
			$mesg = $this->disabled_dates_validation( $pickup_date, $dropoff_date );
			if ( $mesg ) {
				return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			// Guests validation
			$mesg = $this->guests_validation( $args );
			if ( $mesg ) {
				return apply_filters( $hook_name, $mesg, $pickup_date, $dropoff_date, $args, $this );
			}

			return apply_filters( $hook_name, false, $pickup_date, $dropoff_date, $args, $this );
	    }

	    /**
	     * Min days validation
	     */
	    public function min_days_validation( $pickup_date, $dropoff_date ) {
	    	// init
	    	$mesg = false;

	    	// Get min days
	    	$min_days = (float)$this->get_meta_value( 'rent_day_min' );
	    	if ( $min_days ) {
		    	if ( $min_days*86400 > ( $dropoff_date - $pickup_date ) ) {
		    		if ( 1 == $min_days ) {
		    			$mesg = sprintf( esc_html__( 'Min rental period: %s night', 'ova-brw' ), $min_days );
		    		} else {
		    			$mesg = sprintf( esc_html__( 'Min rental period: %s nights', 'ova-brw' ), $min_days );
		    		}
		    	}
	    	}

	    	return apply_filters( $this->prefix.'min_days_validation', $mesg, $pickup_date, $dropoff_date, $this );
	    }

	    /**
	     * Max days validation
	     */
	    public function max_days_validation( $pickup_date, $dropoff_date ) {
	    	// init
	    	$mesg = false;

	    	// Get max days
	    	$max_days = (float)$this->get_meta_value( 'rent_day_max' );
	    	if ( $max_days ) {
	    		if ( $max_days*86400 < ( $dropoff_date - $pickup_date ) ) {
	    			if ( 1 == $max_days ) {
	    				$mesg = sprintf( esc_html__( 'Max rental period: %s night', 'ova-brw' ), $max_days );
	    			} else {
	    				$mesg = sprintf( esc_html__( 'Max rental period: %s nights', 'ova-brw' ), $max_days );
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'max_days_validation', $mesg, $pickup_date, $dropoff_date, $this );
	    }

	    /**
	     * Guests validation
	     */
	    public function guests_validation( $args = [] ) {
	    	// Hook name
	    	$hook_name = $this->prefix.'guests_validation';

	    	// init
	    	$mesg = false;

	    	// Number of guests
	    	$numberof_guests = 0;

	    	// Adults
	    	$numberof_adults = (int)ovabrw_get_meta_data( 'adults', $args );
	    	if ( !$numberof_adults ) {
	    		$numberof_adults = (int)ovabrw_get_meta_data( 'ovabrw_adults', $args );
	    	}

	    	// Update number of guests
	    	$numberof_guests += $numberof_adults;

	    	// Minimum number of adults
	    	$min_adults = (int)$this->get_meta_value( 'min_adults' );
	    	if ( $min_adults && $min_adults > $numberof_adults ) {
	    		$mesg = sprintf( esc_html__( 'Minimum number of adults: %d', 'ova-brw' ), $min_adults );
	    		return apply_filters( $hook_name, $mesg, $args, $this );
	    	}

	    	// Maximum number of adults
	    	$max_adults = (int)$this->get_meta_value( 'max_adults' );
	    	if ( $max_adults && $max_adults < $numberof_adults ) {
	    		$mesg = sprintf( esc_html__( 'Maximum number of adults: %d', 'ova-brw' ), $max_adults );
	    		return apply_filters( $hook_name, $mesg, $args, $this );
	    	}

	    	// Show children
	    	if ( apply_filters( OVABRW_PREFIX.'show_children', true ) ) {
	    		// Number of children
	    		$numberof_children = (int)ovabrw_get_meta_data( 'children', $args );
	    		if ( !$numberof_children ) {
		    		$numberof_children = (int)ovabrw_get_meta_data( 'ovabrw_children', $args );
		    	}

		    	// Update number of guests
	    		$numberof_guests += $numberof_children;

	    		// Minimum number of children
	    		$min_children = (int)$this->get_meta_value( 'min_children' );
	    		if ( $min_children && $min_children > $numberof_children ) {
	    			$mesg = sprintf( esc_html__( 'Minimum number of children: %d', 'ova-brw' ), $min_children );
	    			return apply_filters( $hook_name, $mesg, $args, $this );
	    		}

	    		// Maximum number of children
	    		$max_children = (int)$this->get_meta_value( 'max_children' );
	    		if ( $max_children && $max_children < $numberof_children ) {
	    			$mesg = sprintf( esc_html__( 'Maximum number of children: %d', 'ova-brw' ), $max_children );
	    			return apply_filters( $hook_name, $mesg, $args, $this );
	    		}
	    	}

	    	// Show babies
	    	if ( apply_filters( OVABRW_PREFIX.'show_baby', true ) ) {
	    		// Number of babies
	    		$numberof_babies = (int)ovabrw_get_meta_data( 'babies', $args );
	    		if ( !$numberof_babies ) {
		    		$numberof_babies = (int)ovabrw_get_meta_data( 'ovabrw_babies', $args );
		    	}

		    	// Update number of guests
	    		$numberof_guests += $numberof_babies;

	    		// Minimum number of babies
	    		$min_babies = (int)$this->get_meta_value( 'min_babies' );
	    		if ( $min_babies && $min_babies > $numberof_babies ) {
	    			$mesg = sprintf( esc_html__( 'Minimum number of babies: %d', 'ova-brw' ), $min_babies );
	    			return apply_filters( $hook_name, $mesg, $args, $this );
	    		}

	    		// Maximum number of babies
	    		$max_babies = (int)$this->get_meta_value( 'max_babies' );
	    		if ( $max_babies && $max_babies < $numberof_babies ) {
	    			$mesg = sprintf( esc_html__( 'Maximum number of babies: %d', 'ova-brw' ), $max_babies );
	    			return apply_filters( $hook_name, $mesg, $args, $this );
	    		}
	    	}

	    	// Number of guests required
	    	if ( !$numberof_guests ) {
	    		$mesg = esc_html__( 'Number of guests is required', 'ova-brw' );
	    		return apply_filters( $hook_name, $mesg, $args, $this );
	    	}

	    	// Minimum number of guests
	    	$min_guests = (int)$this->get_meta_value( 'min_guests' );
	    	if ( $min_guests && $min_guests > $numberof_guests ) {
    			$mesg = sprintf( esc_html__( 'Minimum number of guests: %d', 'ova-brw' ), $min_guests );
    			return apply_filters( $hook_name, $mesg, $args, $this );
    		}

	    	// Maximim number of guests
	    	$max_guests = (int)$this->get_meta_value( 'max_guests' );
	    	if ( $max_guests && $max_guests < $numberof_guests ) {
	    		$mesg = sprintf( esc_html__( 'Maximum number of guests: %d', 'ova-brw' ), $max_guests );
    			return apply_filters( $hook_name, $mesg, $args, $this );
	    	}

	    	return apply_filters( $hook_name, $mesg, $args, $this );
	    }

	    /**
	     * Get rental calculations
	     */
	    public function get_rental_calculations( $args = [] ) {
	    	// Pick-up date
	    	$pickup_date = ovabrw_get_meta_data( 'pickup_date', $args );
	    	if ( !$pickup_date ) return 0;

	    	// Drop-off date
	    	$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $args );
	    	if ( !$dropoff_date ) return 0;

	    	// init
	    	$rental_price = 0;

	    	// Get number of rental nights
	    	$numberof_rental_nights = $this->get_number_of_rental_nights( $pickup_date, $dropoff_date );

	    	// Regular price
	    	$regular_price = (float)$this->get_meta_value( 'regular_price_hotel' );

	    	// Get special prices
	    	$special_prices = $this->get_special_prices( $pickup_date, $dropoff_date, $numberof_rental_nights );

	    	// Special total price
	    	$special_total = (float)ovabrw_get_meta_data( 'total', $special_prices );

	    	// Number of rental special nights
	    	$special_qty = (int)ovabrw_get_meta_data( 'quantity', $special_prices );

	    	if ( $numberof_rental_nights <= $special_qty ) {
	    		$rental_price = $special_total;
	    	} else {
	    		// Daily prices
				$daily_prices = [
					'monday' 	=> (float)$this->get_meta_value( 'daily_monday', $regular_price ),
					'tuesday' 	=> (float)$this->get_meta_value( 'daily_tuesday', $regular_price ),
					'wednesday' => (float)$this->get_meta_value( 'daily_wednesday', $regular_price ),
					'thursday' 	=> (float)$this->get_meta_value( 'daily_thursday', $regular_price ),
					'friday' 	=> (float)$this->get_meta_value( 'daily_friday', $regular_price ),
					'saturday' 	=> (float)$this->get_meta_value( 'daily_saturday', $regular_price ),
					'sunday' 	=> (float)$this->get_meta_value( 'daily_sunday', $regular_price )
				];

				// Get discount prices
				$discount_prices = $this->get_discount_prices( $numberof_rental_nights - $special_qty );
				if ( $discount_prices ) {
					$daily_prices = [
						'monday' 	=> (float)$discount_prices,
						'tuesday' 	=> (float)$discount_prices,
						'wednesday' => (float)$discount_prices,
						'thursday' 	=> (float)$discount_prices,
						'friday' 	=> (float)$discount_prices,
						'saturday' 	=> (float)$discount_prices,
						'sunday' 	=> (float)$discount_prices
					];
				}

				// Week start
				$weekstart = gmdate( 'N', $pickup_date );

				// Total weekdays
				$total_weekdays = $this->get_price_by_weekday_start( $weekstart, $numberof_rental_nights, $daily_prices );

				// Total weekdays in special time
				$special_total_weekdays = 0;
				if ( $special_qty ) {
					$special_total_weekdays = $this->get_special_total_weekdays( $pickup_date, $dropoff_date, $daily_prices );
				}

				// Total
				$rental_price = (float)$total_weekdays + (float)$special_total - (float)$special_total_weekdays;
	    	}

	    	return apply_filters( $this->prefix.'get_rental_calculations', $rental_price, $args, $this );
	    }

	    /**
	     * Get number of rental nights
	     */
	    public function get_number_of_rental_nights( $pickup_date, $dropoff_date ) {
	    	if ( !$pickup_date || !$dropoff_date ) return 0;

	    	// Number of rental nights
	    	$numberof_rental_nights = ceil( ( $dropoff_date - $pickup_date ) / 86400 );
	    	if ( !$numberof_rental_nights && ( !$this->product->show_date_field( 'dropoff' ) || $pickup_date === $dropoff_date ) ) {
	    		$numberof_rental_nights = 1;
	    	}

	    	return apply_filters( $this->prefix.'get_number_of_rental_nights', $numberof_rental_nights, $pickup_date, $dropoff_date, $this );
	    }

	    /**
	     * Get special prices
	     */
	    public function get_special_prices( $pickup_date, $dropoff_date, $numberof_rental_nights ) {
	    	if ( !$pickup_date || !$dropoff_date ) return [];

	    	// init
	    	$results = [
	    		'total' 	=> 0,
	    		'quantity' 	=> 0
	    	];

	    	// Get special prices
	    	$special_prices = $this->get_meta_value( 'rt_price' );

	    	if ( ovabrw_array_exists( $special_prices ) ) {
	    		// Start date
	    		$start_date = $this->get_meta_value( 'rt_startdate' );

	    		// End date
	    		$end_date = $this->get_meta_value( 'rt_enddate' );

	    		// Discounts
	    		$discounts = $this->get_meta_value( 'rt_discount' );

	    		foreach ( $special_prices as $i => $price ) {
	    			// From date
	    			$from_date = strtotime( ovabrw_get_meta_data( $i, $start_date ) );
	    			if ( !$from_date ) continue;

	    			// To date
	    			$to_date = strtotime( ovabrw_get_meta_data( $i, $end_date ) );
	    			if ( !$to_date ) continue;

	    			// Discount
	    			$discount = ovabrw_get_meta_data( $i, $discounts );

	    			// Rental period
	    			$rental_period = $discount_price = 0;

	    			if ( $pickup_date >= $from_date && $dropoff_date <= $to_date ) {
	    				$rental_period = $this->get_number_of_rental_nights( $pickup_date, $dropoff_date );
	    			} elseif ( $pickup_date < $from_date && $dropoff_date >= $from_date && $dropoff_date <= $to_date ) {
	    				$rental_period = $this->get_number_of_rental_nights( $from_date, $dropoff_date );
	    			} elseif ( $pickup_date >= $from_date && $pickup_date <= $to_date && $dropoff_date > $to_date ) {
	    				$rental_period = $this->get_number_of_rental_nights( $pickup_date, $to_date + 86400 );
	    			} elseif ( $pickup_date < $from_date && $dropoff_date > $to_date ) {
	    				$rental_period = $this->get_number_of_rental_nights( $from_date, $to_date +86400 );
	    			}

	    			// Check number of rental nights
	    			if ( $numberof_rental_nights <= ( $results['quantity'] + $rental_period ) ) {
	    				$rental_period = $numberof_rental_nights - $results['quantity'];

	    				// Get discount price
	    				$discount_price = (float)$this->get_special_discount_prices( $discount, $price, $rental_period );

	    				$results['total'] 		+= $discount_price * $rental_period;
	    				$results['quantity'] 	+= $numberof_rental_nights;

	    				// Break out of the loop
	    				break;
	    			}

	    			// Get discount price
	    			$discount_price = (float)$this->get_special_discount_prices( $discount, $price, $rental_period );
	    			$results['total'] 		+= $discount_price * $rental_period;
	    			$results['quantity'] 	+= $rental_period;
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_special_prices', $results, $pickup_date, $dropoff_date, $numberof_rental_nights, $this );
	    }

	    /**
	     * Get special discount prices
	     */
	    public function get_special_discount_prices( $discount, $price, $rental_period ) {
	    	if ( !ovabrw_array_exists( $discount ) ) return $price;

	    	// init
	    	$results = $price;

	    	// Discount price
	    	$disc_price = ovabrw_get_meta_data( 'price', $discount );

	    	// Discount from
	    	$disc_from = ovabrw_get_meta_data( 'min', $discount );

	    	// Discount to
	    	$disc_to = ovabrw_get_meta_data( 'max', $discount );

	    	if ( ovabrw_array_exists( $disc_price ) ) {
	    		foreach ( $disc_price as $i => $p ) {
	    			$from 	= (float)ovabrw_get_meta_data( $i, $disc_from );
	    			$to 	= (float)ovabrw_get_meta_data( $i, $disc_to );

	    			if ( $rental_period >= $from && $rental_period <= $to ) {
	    				$results = (float)$p;

	    				// Break out of the loop
	    				break;
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_special_discount_prices', $results, $discount, $price, $rental_period, $this );
	    }

	    /**
	     * Get discount prices
	     */
	    public function get_discount_prices( $numberof_rental_nights ) {
	    	if ( !$numberof_rental_nights ) return 0;

	    	// init
	    	$price = 0;

	    	// Discount price
	    	$disc_price = $this->get_meta_value( 'global_discount_price' );

	    	if ( ovabrw_array_exists( $disc_price ) ) {
	    		// Discount from
	    		$disc_from = $this->get_meta_value( 'global_discount_duration_val_min' );

	    		// Discount to
	    		$disc_to = $this->get_meta_value( 'global_discount_duration_val_max' );

	    		// Loop
	    		foreach ( $disc_price as $i => $p ) {
	    			$from 	= (float)ovabrw_get_meta_data( $i, $disc_from );
	    			$to 	= (float)ovabrw_get_meta_data( $i, $disc_to );

	    			if ( $numberof_rental_nights >= $from && $numberof_rental_nights <= $to ) {
	    				$price = (float)$p;

	    				// Break out of the loop
	    				break;
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_discount_prices', $price, $numberof_rental_nights, $this );
	    }

	    /**
	     * Total weekdays in special time
	     */
	    public function get_special_total_weekdays( $pickup_date, $dropoff_date, $daily_prices ) {
	    	if ( !$pickup_date || !$dropoff_date || !ovabrw_array_exists( $daily_prices ) ) {
	    		return 0;
	    	}

	    	// init
	    	$special_total = 0;

	    	// Get special prices
	    	$special_prices = $this->get_meta_value( 'rt_price' );

	    	if ( ovabrw_array_exists( $special_prices ) ) {
	    		// Start date
	    		$start_date = $this->get_meta_value( 'rt_startdate' );

	    		// End date
	    		$end_date = $this->get_meta_value( 'rt_enddate' );

	    		foreach ( $special_prices as $i => $price ) {
	    			// From date
	    			$from_date = strtotime( ovabrw_get_meta_data( $i, $start_date ) );
	    			if ( !$from_date ) continue;

	    			// To date
	    			$to_date = strtotime( ovabrw_get_meta_data( $i, $end_date ) );
	    			if ( !$to_date ) continue;

	    			// init
	    			$weekstart = $numberof_days = '';

	    			if ( $pickup_date >= $from_date && $dropoff_date <= $to_date ) {
	    				$weekstart 		= gmdate( 'N', $pickup_date );
	    				$numberof_days 	= $this->get_number_of_rental_nights( $pickup_date, $dropoff_date );
	    			} elseif ( $pickup_date < $from_date && $dropoff_date >= $from_date && $dropoff_date <= $to_date ) {
	    				$weekstart 		= gmdate( 'N', $from_date );
	    				$numberof_days 	= $this->get_number_of_rental_nights( $from_date, $dropoff_date );
	    			} elseif ( $pickup_date >= $from_date && $pickup_date <= $to_date && $dropoff_date > $to_date ) {
	    				$weekstart 		= gmdate( 'N', $pickup_date );
	    				$numberof_days 	= $this->get_number_of_rental_nights( $pickup_date, $to_date + 86400 );
	    			} elseif ( $pickup_date < $from_date && $dropoff_date > $to_date ) {
	    				$weekstart 		= gmdate( 'N', $from_date );
	    				$numberof_days 	= $this->get_number_of_rental_nights( $from_date, $to_date + 86400 );
	    			}

	    			$special_total += $this->get_price_by_weekday_start( $weekstart, $numberof_days, $daily_prices );
	    		}
	    	}

	    	return apply_filters( OVABRW_PREFIX.'get_special_total_weekdays', $special_total, $pickup_date, $dropoff_date, $daily_prices, $this );
	    }

	    /**
		 * Add rental cart item data
		 */
		public function add_rental_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
			// Rental type
	    	$cart_item_data['rental_type'] = $this->get_type();

	    	// Pick-up date
	    	if ( !ovabrw_get_meta_data( 'pickup_date', $cart_item_data ) ) {
	    		// Get pick-up date
	    		$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_date', $_REQUEST ) );

	    		// Add cart item data
		    	$cart_item_data['pickup_date'] = $pickup_date;
	    	} // END if

	    	// Drop-off date
	    	if ( !ovabrw_get_meta_data( 'dropoff_date', $cart_item_data ) ) {
	    		// Get drop-off date
	    		$dropoff_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_dropoff_date', $_REQUEST ) );
		    	if ( !$dropoff_date ) $dropoff_date = $cart_item_data['pickup_date'];

		    	// Add cart item data
		    	$cart_item_data['dropoff_date'] = $dropoff_date;
	    	} // END if

	    	// Number of adults
	    	if ( !ovabrw_get_meta_data( 'numberof_adults', $cart_item_data ) ) {
	    		$numberof_adults = (int)ovabrw_get_meta_data( 'ovabrw_adults', $_REQUEST );
		    	$cart_item_data['numberof_adults'] = $numberof_adults;
	    	} // END if

	    	// Number of children
	    	if ( !ovabrw_get_meta_data( 'numberof_children', $cart_item_data ) ) {
	    		$numberof_children = (int)ovabrw_get_meta_data( 'ovabrw_children', $_REQUEST );
		    	$cart_item_data['numberof_children'] = $numberof_children;
	    	} // END if

	    	// Number of babies
	    	if ( !ovabrw_get_meta_data( 'numberof_babies', $cart_item_data ) ) {
	    		$numberof_babies = (int)ovabrw_get_meta_data( 'ovabrw_babies', $_REQUEST );
		    	$cart_item_data['numberof_babies'] = $numberof_babies;
	    	} // END if

	    	// Get real data
	    	$read_data = $this->get_real_data( strtotime( $cart_item_data['pickup_date'] ), strtotime( $cart_item_data['dropoff_date'] ) );

	    	// Pick-up real date
	    	$cart_item_data['pickup_real'] = ovabrw_get_meta_data( 'pickup_real', $read_data, $cart_item_data['pickup_date'] );

	    	// Drop-off real dates
	    	$cart_item_data['dropoff_real'] = ovabrw_get_meta_data( 'dropoff_real', $read_data, $cart_item_data['dropoff_date'] );

	    	// Quantity real
	    	$cart_item_data['quantity_real'] = ovabrw_get_meta_data( 'quantity_real', $read_data );

	    	// Price real
	    	$cart_item_data['price_real'] = ovabrw_get_meta_data( 'price_real', $read_data );

			return apply_filters( $this->prefix.'add_rental_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity, $this );
		}

		/**
		 * Get rental cart item data
		 */
		public function get_rental_cart_item_data( $item_data, $cart_item ) {
			if ( !ovabrw_array_exists( $item_data ) ) $item_data = [];

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $cart_item );
			if ( $pickup_date ) {
				$item_data[] = [
					'key'     => $this->product->get_date_label(),
		            'value'   => wc_clean( $pickup_date ),
		            'display' => wc_clean( $pickup_date ),
				];
			}

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $cart_item );
			if ( $dropoff_date ) {
				$item_data[] = [
					'key'     => $this->product->get_date_label( 'dropoff' ),
		            'value'   => wc_clean( $dropoff_date ),
		            'display' => wc_clean( $dropoff_date ),
		            'hidden'  => !$this->product->show_date_field( 'dropoff' ) ? true : false
				];
			}

			// Number of adults
			$numberof_adults = (int)ovabrw_get_meta_data( 'numberof_adults', $cart_item );
			if ( $numberof_adults ) {
				$item_data[] = [
					'key'     => esc_html__( 'Number of Adults', 'ova-brw' ),
                    'value'   => wc_clean( $numberof_adults ),
                    'display' => wc_clean( $numberof_adults )
				];
			}

			// Number of children
			$numberof_children = (int)ovabrw_get_meta_data( 'numberof_children', $cart_item );
			if ( $numberof_children ) {
				$item_data[] = [
					'key' 		=> esc_html__( 'Number of Children', 'ova-brw' ),
                    'value' 	=> wc_clean( $numberof_children ),
                    'display' 	=> wc_clean( $numberof_children ),
                    'hidden' 	=> apply_filters( OVABRW_PREFIX.'show_children', true )
				];
			}

			// Number of babies
			$numberof_babies = (int)ovabrw_get_meta_data( 'numberof_babies', $cart_item );
			if ( $numberof_babies ) {
				$item_data[] = [
					'key' 		=> esc_html__( 'Number of Children', 'ova-brw' ),
                    'value' 	=> wc_clean( $numberof_babies ),
                    'display' 	=> wc_clean( $numberof_babies ),
                    'hidden' 	=> apply_filters( OVABRW_PREFIX.'show_babies', true )
				];
			}

			// Quantity
			$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item, 1 );
			if ( $quantity ) {
				$item_data[] = [
					'key'     => esc_html__( 'Quantity', 'ova-brw' ),
		            'value'   => wc_clean( $quantity ),
		            'display' => wc_clean( $quantity ),
		            'hidden'  => !$this->product->show_quantity() ? true : false
				];
			}

			return apply_filters( $this->prefix.'get_rental_cart_item_data', $item_data, $cart_item, $this );
		}

		/**
		 * Get real data
		 */
		public function get_real_data( $pickup_date, $dropoff_date ) {
			if ( !$pickup_date || !$dropoff_date ) return false;

			// Real data
			$real_data = [];

			// Date format
			$date_format = OVABRW()->options->get_date_format();

			// Pick-up real date
			$real_data['pickup_real'] = gmdate( $date_format, $pickup_date );

			// Drop-off real date
			$real_data['dropoff_real'] = gmdate( $date_format, $dropoff_date );

			// Number of rental days
			$numberof_rental_days = $this->get_number_of_rental_nights( $pickup_date, $dropoff_date );

			// Quatity real
			$real_data['quantity_real'] = '';

			// Price real
			$real_data['price_real'] = '';

			return apply_filters( $this->prefix.'get_real_data', $real_data, $pickup_date, $dropoff_date, $this );
		}

		/**
		 * Get request booking mail content
		 */
		public function get_request_booking_mail_content( $data = [] ) {
			// Order details
			$order_details = '<h2>' . esc_html__( 'Order details: '. 'ova-brw' ) . '</h2>';

			// Open <table> tag
			$order_details .= '<table>';

			// Product link
			$product_link = '<a href="' . esc_url( $this->product->get_permalink() ) . '">' . wp_kses_post( $this->product->get_title() ) . '</a>';
			$order_details .= '<tr>';
    			$order_details .= '<td style="width: 15%">' . esc_html__( 'Product: ', 'ova-brw' ) . '</td>';
    			$order_details .= '<td style="width: 85%">';
    				$order_details .= $product_link;
    			$order_details .= '</td>';
    		$order_details .= '</tr>';

    		// Customer name
    		$customer_name = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_name', $data ) );
    		if ( $customer_name ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Name: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $customer_name ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Customer email
    		$customer_email = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_email', $data ) );
    		if ( $customer_email ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Email: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $customer_email ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Customer phone
    		$customer_phone = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_phone', $data ) );
    		if ( $customer_phone && 'yes' === ovabrw_get_setting( 'request_booking_form_show_number', 'yes' ) ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Phone: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $customer_phone ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Customer address
    		$customer_address = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_address', $data ) );
    		if ( $customer_address && 'yes' === ovabrw_get_setting( 'request_booking_form_show_address', 'yes' ) ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Address: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $customer_address ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Pick-up location
    		$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_location', $data ) );
    		if ( $this->product->show_location_field( 'pickup', 'request' ) && $pickup_location ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Pick-up Location: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $pickup_location ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Drop-off location
    		$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_dropoff_location', $data ) );
    		if ( $this->product->show_location_field( 'dropoff', 'request' ) && $dropoff_location ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Drop-off Location: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $dropoff_location ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Pick-up date
    		$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_date', $data ) );
    		if ( $pickup_date ) {
    			$order_details .= '<tr>';
					$order_details .= '<td>' . esc_html( $this->product->get_date_label() ) . ':</td>';
					$order_details .= '<td>' . esc_html( $pickup_date ) . '</td>';
				$order_details .= '</tr>';
    		}

    		// Drop-off date
    		$dropoff_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_dropoff_date', $data ) );
    		if ( $this->product->show_date_field( 'dropoff', 'request' ) && $dropoff_date ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html( $this->product->get_date_label( 'dropoff' ) ) . ':</td>';
    				$order_details .= '<td>' . esc_html( $dropoff_date ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Number of adults
    		$numberof_adults = (int)ovabrw_get_meta_data( 'ovabrw_adults', $data );
    		if ( $numberof_adults ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Number of Adults: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $numberof_adults ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Number of children
    		$numberof_children = (int)ovabrw_get_meta_data( 'ovabrw_children', $data );
    		if ( $numberof_children && apply_filters( OVABRW_PREFIX.'show_children', true ) ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Number of Children: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $numberof_children ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Number of babies
    		$numberof_babies = (int)ovabrw_get_meta_data( 'ovabrw_babies', $data );
    		if ( $numberof_babies && apply_filters( OVABRW_PREFIX.'show_babies', true ) ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Number of Babies: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $numberof_babies ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Quantity
    		$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $data, 1 );
    		if ( $this->product->show_quantity( 'request' ) ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Quantity: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $quantity ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Custom checkout fields
			$cckf = $cckf_qty = $cckf_value = [];

			// Get product cckf
	    	$product_cckf = $this->product->get_cckf();
	    	if ( ovabrw_array_exists( $product_cckf ) ) {
	    		// Loop
	    		foreach ( $product_cckf as $name => $fields ) {
	    			if ( 'on' !== ovabrw_get_meta_data( 'enabled', $fields ) ) continue;

	    			// Get type
	    			$type = ovabrw_get_meta_data( 'type', $fields );

	    			// Label
	    			$label = ovabrw_get_meta_data( 'label', $fields );

	    			if ( 'file' === $type ) {
	    				// Get file
	    				$files = ovabrw_get_meta_data( $name, $_FILES );

    					// File name
    					$file_name = ovabrw_get_meta_data( 'name', $files );
    					if ( $file_name ) {
    						// File size
    						$file_size = (int)ovabrw_get_meta_data( 'size', $files );
    						$file_size = $file_size / 1048576;

    						// Max size
    						$max_size = (float)ovabrw_get_meta_data( 'max_file_size', $fields );
    						if ( $max_size < $file_size ) continue;

    						// File type
	                        $accept = apply_filters( OVABRW_PREFIX.'accept_file_upload', '.jpg, .jpeg, .png, .pdf, .doc, .docx', $this );

	                        // Get file extension
	                        $extension = pathinfo( $file_name, PATHINFO_EXTENSION );
	                        if ( strpos( $accept, $extension ) === false ) continue;

	                         // Upload file
	                        $overrides = [ 'test_form' => false ];

	                        if ( ! function_exists( 'wp_handle_upload' ) ) {
								require_once( ABSPATH . 'wp-admin/includes/file.php' );
							}

							// Upload
							$upload = wp_handle_upload( $files, $overrides );
							if ( ovabrw_get_meta_data( 'error', $upload ) ) continue;

	                        // File url
	                        $file_url = '<a href="'.esc_url( $upload['url'] ).'" target="_blank">';
								$file_url .= basename( $upload['file'] );
							$file_url .= '</a>';

							// Order details
							$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>';
	                        		$order_details .= $file_url;
	                        	$order_details .= '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = $file_url;
    					}
	    			} elseif ( 'checkbox' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options values
	    				$opt_values = ovabrw_get_meta_data( $name, $data );

	    				if ( ovabrw_array_exists( $opt_values ) ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_values;

	    					// Option quantities
	    					$opt_qtys = ovabrw_get_meta_data( $name.'_qty', $data );
	    					if ( ovabrw_array_exists( $opt_qtys ) ) {
	    						$cckf_qty = array_merge( $cckf_qty, $opt_qtys );
	    					}

	    					// Option keys
	    					$opt_keys = ovabrw_get_meta_data( 'ova_checkbox_key', $fields, [] );

	    					// Option texts
	    					$opt_texts = ovabrw_get_meta_data( 'ova_checkbox_text', $fields, [] );

	    					// Loop
	    					foreach ( $opt_values as $val ) {
	    						$val = sanitize_text_field( $val );

	    						// Get index option
	    						$index = array_search( $val, $opt_keys );
	    						if ( is_bool( $index ) ) continue;

	    						// Option text
	    						$opt_text = ovabrw_get_meta_data( $index, $opt_texts );

	    						// Option qty
	    						$opt_qty = (int)ovabrw_get_meta_data( $val, $cckf_qty );

	    						if ( $opt_qty ) {
	    							array_push( $opt_names, sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_text, $opt_qty ) );
	    						} else {
	    							array_push( $opt_names, $opt_text );
	    						}
	    					} // END loop
	    				}

	    				if ( ovabrw_array_exists( $opt_names ) ) {
	    					$opt_names = implode( ', ', $opt_names );

	    					// Order details
	    					$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>' . esc_html( $opt_names ) . '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = esc_html( $opt_names );
	    				}
	    			} elseif ( 'radio' === $type ) {
	    				// Get option value
	    				$opt_value = sanitize_text_field( ovabrw_get_meta_data( $name, $data ) );
	    				if ( $opt_value ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_value;

	    					// Get option quantities
	    					$opt_qtys = ovabrw_get_meta_data( $name.'_qty', $data, [] );

	    					// Option qty
	    					$opt_qty = (int)ovabrw_get_meta_data( $opt_value, $opt_qtys );

	    					// Add cart item data
	    					if ( $opt_qty ) {
	    						// Add cckf quantity
	    						$cckf_qty[$name] 	= $opt_qty;
	    						$opt_value 			= sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_value, $opt_qty );
	    					}

	    					// Order details
	    					$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>' . esc_html( $opt_value ) . '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = esc_html( $opt_value );
	    				}
	    			} elseif ( 'select' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options value
	    				$opt_value = sanitize_text_field( ovabrw_get_meta_data( $name, $data ) );
	    				if ( $opt_value ) {
	    					// Option keys
	    					$opt_keys = ovabrw_get_meta_data( 'ova_options_key', $fields );

	    					// Option texts
	    					$opt_texts = ovabrw_get_meta_data( 'ova_options_text', $fields );

	    					// Option quantities
	    					$opt_qtys = ovabrw_get_meta_data( $name.'_qty', $data );
	    					
	    					// Option quantity
	    					$opt_qty = (int)ovabrw_get_meta_data( $opt_value, $opt_qtys );

	    					// index option
	    					$index = array_search( $opt_value, $opt_keys );
	    					if ( is_bool( $index ) ) continue;

	    					// Add cckf
	    					$cckf[$name] = $opt_value;

	    					// Option text
	    					$opt_text = ovabrw_get_meta_data( $index, $opt_texts );

	    					// Add cart item data
	    					if ( $opt_qty ) {
	    						// Add cckf quantity
	    						$cckf_qty[$name] 	= $opt_qty;
	    						$opt_text 			= sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_text, $opt_qty );
	    					}

	    					// Order details
	    					$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>' . esc_html( $opt_text ) . '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = esc_html( $opt_text );
	    				}
	    			} else {
	    				// Option value
	    				$opt_value = sanitize_text_field( ovabrw_get_meta_data( $name, $data ) );

	    				if ( $opt_value ) {
	    					// Order details
	    					$order_details .= '<tr>';
	                        	$order_details .= '<td>' . esc_html( $label ) . ':</td>';
	                        	$order_details .= '<td>' . esc_html( $opt_value ) . '</td>';
	                        $order_details .= '</tr>';

	                        // Cckf value
	                        $cckf_value[$name] = esc_html( $opt_value );
	    				}
	    			}
	    		} // END loop
	    	}

	    	// Resources
	    	$resc 			= ovabrw_get_meta_data( 'ovabrw_resource_checkboxs', $data );
	    	$resc_qtys 		= ovabrw_get_meta_data( 'ovabrw_resource_quantity', $data );
	    	$resc_values 	= [];
	    	if ( ovabrw_array_exists( $resc ) ) {
	    		// Get resource ids
	    		$resc_ids = $this->get_meta_value( 'resource_id', [] );

	    		// Get resource names
	    		$resc_names = $this->get_meta_value( 'resource_name', [] );

	    		// Loop
	    		foreach ( $resc as $opt_id ) {
	    			$opt_id = sanitize_text_field( $opt_id );

	    			// Get index option
	    			$index = array_search( $opt_id, $resc_ids );
	    			if ( is_bool( $index ) ) continue;

	    			// Option name
	    			$opt_name = ovabrw_get_meta_data( $index, $resc_names );
	    			if ( !$opt_name ) continue;
	    			
	    			// Get option quantity
	    			$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $resc_qtys );
	    			if ( $opt_qty ) {
	    				$opt_name = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_name, $opt_qty );
	    			}

	    			// Resource values
	    			array_push( $resc_values, $opt_name );
	    		} // END loop

	    		// Resource value
	    		if ( ovabrw_array_exists( $resc_values ) ) {
	    			// Order details
	    			if ( 'yes' === ovabrw_get_setting( 'request_booking_form_show_extra_service', 'yes' ) ) {
	    				$order_details .= '<tr>';
	    					$order_details .= '<td>' . sprintf( _n( 'Resource%s', 'Resources%s', count( $resc_values ), 'ova-brw' ), ':' ) . '</td>';
		    			$order_details .= '</tr>';
	    			}
	    		}
	    	} // END resources

	    	// Services
	    	$services 		= ovabrw_get_meta_data( 'ovabrw_service', $data );
	    	$services_qty 	= ovabrw_get_meta_data( 'ovabrw_service_qty', $data );

	    	// init
    		$serv_opts = $serv_qtys = $serv_values = [];
	    	if ( ovabrw_array_exists( $services ) ) {
	    		// Get service labels
	    		$serv_labels = $this->get_meta_value( 'label_service', [] );

	    		// Get option ids
	    		$opt_ids = $this->get_meta_value( 'service_id', [] );

	    		// Get option names
	    		$opt_names = $this->get_meta_value( 'service_name', [] );

	    		foreach ( $services as $opt_id ) {
	    			$opt_id = sanitize_text_field( $opt_id );
	    			if ( $opt_id ) {
	    				$serv_opts[] = $opt_id;

	    				// Service qty
	    				$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $services_qty );
	    				if ( $opt_qty ) {
	    					$serv_qtys[$opt_id] = $opt_qty;
	    				}

	    				// Loop option ids
		    			foreach ( $opt_ids as $i => $ids ) {
		    				// Option index
							$index = array_search( $opt_id, $ids );
							if ( is_bool( $index ) ) continue;

							// Service label
							$label = ovabrw_get_meta_data( $i, $serv_labels );
							if ( !$label ) continue;

							// Option name
							$opt_name = isset( $opt_names[$i][$index] ) ? $opt_names[$i][$index] : '';
							if ( !$opt_name ) continue;

							// Add item data
							if ( $opt_qty ) {
								$opt_name = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_name, $opt_qty );
							}

							// Order details
							if ( 'yes' === ovabrw_get_setting( 'request_booking_form_show_service', 'yes' ) ) {
								$order_details .= '<tr>';
					    			$order_details .= '<td>' . esc_html( $label ) . ':</td>';
					    			$order_details .= '<td>' . esc_html( $opt_name ) . '</td>';  
				    			$order_details .= '</tr>';
							}

							// Service values
							$serv_values[$label] = $opt_name;

							// Break out of the loop
							break;
		    			} // END loop option ids
	    			}
	    		}
	    	} // END services

	    	// Customer note
	    	$customer_note = sanitize_text_field( ovabrw_get_meta_data( 'extra', $data ) );
	    	if ( 'yes' === ovabrw_get_setting( 'request_booking_form_show_extra_info', 'yes' ) && $customer_note ) {
	    		$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Extra: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $customer_note ) . '</td>';
    			$order_details .= '</tr>';
	    	}

    		// Close <table> tag
			$order_details .= '</table>';

			// Create new order
			if ( 'yes' === ovabrw_get_setting( 'request_booking_create_order', 'no' ) ) {
				$order_data = [
					'customer_name' 	=> $customer_name,
					'customer_email' 	=> $customer_email,
					'customer_phone' 	=> $customer_phone,
					'customer_address' 	=> $customer_address,
					'customer_note' 	=> $customer_note,
					'pickup_location' 	=> $pickup_location,
					'dropoff_location' 	=> $dropoff_location,
					'pickup_date' 		=> $pickup_date,
					'dropoff_date' 		=> $dropoff_date,
					'numberof_adults' 	=> $numberof_adults,
					'numberof_children' => $numberof_children,
					'numberof_babies' 	=> $numberof_babies,
					'quantity' 			=> $quantity,
					'cckf' 				=> $cckf,
					'cckf_qty' 			=> $cckf_qty,
					'cckf_value' 		=> $cckf_value,
					'resources' 		=> $resc,
					'resources_qty' 	=> $resc_qtys,
					'resources_value' 	=> $resc_values,
					'services' 			=> $serv_opts,
					'services_qty' 		=> $serv_qtys,
					'services_value' 	=> $serv_values
				];

				// Create new order
				$order_id = $this->request_booking_create_new_order( $order_data, $data );
			}

			return apply_filters( $this->prefix.'get_request_booking_mail_content', $order_details, $data, $this );
		}

		/**
		 * Request booking create new order
		 */
		public function request_booking_create_new_order( $data = [], $args = [] ) {
			if ( !ovabrw_array_exists( $data ) ) return false;

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $data );

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $data );

			// Number of adults
			$numberof_adults = (int)ovabrw_get_meta_data( 'numberof_adults', $data );

			// Number of children
			$numberof_children = (int)ovabrw_get_meta_data( 'numberof_children', $data );

			// Number of babies
			$numberof_babies = (int)ovabrw_get_meta_data( 'numberof_babies', $data );

			// Quantity
			$quantity = (int)ovabrw_get_meta_data( 'quantity', $data, 1 );

			// Custom checkout fields
			$cckf 		= ovabrw_get_meta_data( 'cckf', $data );
			$cckf_qty 	= ovabrw_get_meta_data( 'cckf_qty', $data );

			// Resources
			$resources 		= ovabrw_get_meta_data( 'resources', $data );
			$resources_qty 	= ovabrw_get_meta_data( 'resources_qty', $data );

			// Services
			$services 		= ovabrw_get_meta_data( 'services', $data );
			$services_qty 	= ovabrw_get_meta_data( 'services_qty', $data );

			// Subtotal
	        $subtotal = ovabrw_convert_price( $this->get_total([
	        	'pickup_date' 		=> strtotime( $pickup_date ),
	        	'dropoff_date' 		=> strtotime( $dropoff_date ),
	        	'quantity' 			=> $quantity,
	        	'cckf'  			=> $cckf,
	        	'cckf_qty' 			=> $cckf_qty,
	        	'resources' 		=> $resources,
	        	'resources_qty' 	=> $resources_qty,
	        	'services' 			=> $services,
	        	'services_qty' 		=> $services_qty
	        ]));
	        
	        // Set order total
	        $order_total = $subtotal;

	        // Insurance amount
	        $insurance = (float)$this->get_meta_value( 'amount_insurance' ) * $quantity;

	        // Create new order
	        $new_order = wc_create_order([
	        	'status'        => '',
	        	'customer_note' => ovabrw_get_meta_data( 'customer_note', $data )
	        ]);

	        // Order id
	        $order_id = $new_order->get_id();

	        // Billing
	        $new_order->set_address([
	        	'first_name' 	=> ovabrw_get_meta_data( 'customer_name', $data ), // First name
			    'last_name' 	=> '', // Last name
			    'company'       => '', // Company name
			    'address_1'     => ovabrw_get_meta_data( 'customer_address', $data ), // Address line 1
			    'address_2'     => '', // Address line 2
			    'city'          => '', // City
			    'state'         => '', // State or county
			    'postcode'      => '', // Postcode or ZIP
			    'country'       => '', // Country code (ISO 3166-1 alpha-2)
			    'email'         => ovabrw_get_meta_data( 'customer_email', $data ), // Email address
			    'phone'         => ovabrw_get_meta_data( 'customer_phone', $data ) // Phone number
	        ], 'billing' );

	        // Set customer
            $user = get_user_by( 'email', ovabrw_get_meta_data( 'customer_email', $data ) );
            if ( $user ) {
                $new_order->set_customer_id( $user->ID );
            }

            // Tax enabled
	        $tax_amount = $tax_rate_id = 0;

	        // Taxable
            $item_taxes = false;
	        if ( wc_tax_enabled() ) {
	        	// Get tax rates
	        	$tax_rates = WC_Tax::get_rates( $this->product->get_tax_class() );

	        	// Tax rate id
                if ( ovabrw_array_exists( $tax_rates ) ) {
		            $tax_rate_id = key( $tax_rates );
		        }

		        // Prices include tax
	        	if ( wc_prices_include_tax() ) {
		        	$taxes 		= WC_Tax::calc_inclusive_tax( $subtotal, $tax_rates );
		        	$tax_amount = WC_Tax::get_tax_total( $taxes );
                    $subtotal 	-= $tax_amount;
	        	} else {
	        		$taxes 		= WC_Tax::calc_exclusive_tax( $subtotal, $tax_rates );
                    $tax_amount = WC_Tax::get_tax_total( $taxes );
                    $order_total += $tax_amount;
	        	}

	        	// Item taxes
	        	$item_taxes = [
	        		'total'    => $taxes,
                    'subtotal' => $taxes
	        	];
	        }

	        // Handle items
	        $item_id = $new_order->add_product( $this->product, $quantity, [
	        	'totals' => [
	        		'subtotal' 	=> $subtotal,
	                'total' 	=> $subtotal
	        	]
	        ]);

	        // Get order line item
        	$line_item = $new_order->get_item( $item_id );
        	if ( $line_item ) {
        		// Rental type
        		$line_item->add_meta_data( 'rental_type', $this->get_type(), true );

        		// Pick-up date
        		if ( $pickup_date ) {
        			$line_item->add_meta_data( 'ovabrw_pickup_date', $pickup_date, true );
        			$line_item->add_meta_data( 'ovabrw_pickup_date_strtotime', strtotime( $pickup_date ), true );
        		}

        		// Drop-off date
        		if ( $dropoff_date ) {
        			$line_item->add_meta_data( 'ovabrw_pickoff_date', $dropoff_date, true );
        			$line_item->add_meta_data( 'ovabrw_pickoff_date_strtotime', strtotime( $dropoff_date ), true );
        		}

        		// Get real data
		    	$read_data = $this->get_real_data( strtotime( $pickup_date ), strtotime( $dropoff_date ) );

		    	// Pick-up real date
		    	$pickup_real = ovabrw_get_meta_data( 'pickup_real', $read_data, $pickup_date );
		    	$line_item->add_meta_data( 'ovabrw_pickup_date_real', $pickup_real, true );

		    	// Drop-off real dates
		    	$dropoff_real = ovabrw_get_meta_data( 'dropoff_real', $read_data, $dropoff_date );
		    	$line_item->add_meta_data( 'ovabrw_pickoff_date_real', $dropoff_real, true );

		    	// Number of adults
		    	if ( $numberof_adults ) {
		    		$line_item->add_meta_data( 'ovabrw_adults', $numberof_adults, true );
		    	}

		    	// Number of children
		    	if ( $numberof_children && apply_filters( OVABRW_PREFIX.'show_children', true ) ) {
		    		$line_item->add_meta_data( 'ovabrw_children', $numberof_children, true );
		    	}

		    	// Number of babies
		    	if ( $numberof_babies && apply_filters( OVABRW_PREFIX.'show_babies', true ) ) {
		    		$line_item->add_meta_data( 'ovabrw_babies', $numberof_babies, true );
		    	}

		    	// Quantity
		    	$line_item->add_meta_data( 'ovabrw_number_vehicle', $quantity, true );

		    	// Custom checkout fields
		    	if ( ovabrw_array_exists( $cckf ) ) {
		    		// CCKF value
		    		$cckf_value = ovabrw_get_meta_data( 'cckf_value', $data );
		    		if ( ovabrw_array_exists( $cckf_value ) ) {
		    			foreach ( $cckf_value as $name => $value ) {
		    				$line_item->add_meta_data( $name, $value, true );
		    			}
		    		}

		    		// CCKF
		    		$line_item->add_meta_data( 'ovabrw_custom_ckf', $cckf, true );

		    		// CCKF quantity
		    		if ( ovabrw_array_exists( $cckf_qty ) ) {
		    			$line_item->add_meta_data( 'ovabrw_custom_ckf_qty', $cckf_qty, true );
		    		}
		    	} // END if

		    	// Resources
		    	if ( ovabrw_array_exists( $resources ) ) {
		    		// Resource values
		    		$resc_values = ovabrw_get_meta_data( 'resources_value', $data );
		    		if ( ovabrw_array_exists( $resc_values ) ) {
		    			$line_item->add_meta_data( sprintf( _n( 'Resource%s', 'Resources%s', count( $resc_values ), 'ova-brw' ), '' ), implode( ', ', $resc_values ), true );
		    		}

		    		// Add resources
		    		$line_item->add_meta_data( 'ovabrw_resources', $resources, true );

		    		// Add resources quantity
		    		if ( ovabrw_array_exists( $resources_qty ) ) {
		    			$line_item->add_meta_data( 'ovabrw_resources_qty', $resources_qty, true );
		    		}
		    	} // END if

		    	// Services
		    	if ( ovabrw_array_exists( $services ) ) {
		    		// Service values
		    		$serv_values = ovabrw_get_meta_data( 'services_value', $data );
		    		if ( ovabrw_array_exists( $serv_values ) ) {
		    			foreach ( $serv_values as $label => $opt_name ) {
		    				$line_item->add_meta_data( $label, $opt_name, true );
		    			}
		    		}

		    		// Add services
		    		$line_item->add_meta_data( 'ovabrw_services', $services, true );

		    		// Add services quantity
		    		if ( ovabrw_array_exists( $services_qty ) ) {
		    			$line_item->add_meta_data( 'ovabrw_services_qty', $services_qty, true );
		    		}
		    	} // END if

		    	// Update item tax
	            $line_item->set_props([
	            	'taxes' => $item_taxes
	            ]);

	            // Save item
	            $line_item->save();
        	}

        	// Insurance
        	if ( $insurance ) {
        		// Update order total
        		$order_total += $insurance;

        		// Add insurance amount meta data
	        	$new_order->add_meta_data( '_ova_insurance_amount', $insurance, true );

	        	// Get insurance name
	        	$insurance_name = OVABRW()->options->get_insurance_name();

	        	// Add item fee
	        	$item_fee = new WC_Order_Item_Fee();
                $item_fee->set_props([
                	'name'      => $insurance_name,
                    'tax_class' => 0,
                    'total'     => $insurance,
                    'order_id'  => $order_id
                ]);
                $item_fee->save();

                // Add item fee
                $new_order->add_item( $item_fee );

                // Add insurance key
                $new_order->add_meta_data( '_ova_insurance_key', sanitize_title( $insurance_name ), true );
        	} // END if

        	// Set order tax
	        if ( wc_tax_enabled() && $tax_amount ) {
	        	// New order item tax
	            $item_tax = new WC_Order_Item_Tax();

	            // Set taxes
	            $item_tax->set_props([
	            	'rate_id'            => $tax_rate_id,
	                'tax_total'          => $tax_amount,
	                'shipping_tax_total' => 0,
	                'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
	                'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
	                'compound'           => WC_Tax::is_compound( $tax_rate_id ),
	                'rate_percent'       => WC_Tax::get_rate_percent_value( $tax_rate_id )
	            ]);

	            // Save
	            $item_tax->save();
	            $new_order->add_item( $item_tax );
	            $new_order->set_cart_tax( $tax_amount );
	        } // END if

	        // Set order status
	        $new_order->set_status( ovabrw_get_setting( 'request_booking_order_status', 'wc-on-hold' ) );

	        // Set date created
	        $new_order->set_date_created( gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );

	        // Set total
	        $new_order->set_total( $order_total );

	        // Save
	        $new_order->save();

			return apply_filters( $this->prefix.'request_booking_create_new_order', $order_id, $data, $args, $this );
		}

		/**
		 * New booking handle item
		 */
		public function new_booking_handle_item( $meta_key, $args, $order ) {
			// init
			$results = [
				'is_deposit' 		=> false,
				'deposit_amount' 	=> 0,
				'remaining_amount' 	=> 0,
				'remaining_tax' 	=> 0,
				'insurance_amount' 	=> 0,
				'insurance_tax' 	=> 0,
				'tax_rate_id' 		=> 0,
				'tax_amount' 		=> 0,
				'subtotal' 			=> 0
			];

			// Item meta data
			$item_meta = [];

			// Rental type
			$item_meta['rental_type'] = $this->get_type();

			// Pick-up date
			$pickup_date = isset( $args['ovabrw_pickup_date'][$meta_key] ) ? $args['ovabrw_pickup_date'][$meta_key] : '';
			if ( $pickup_date ) {
				$item_meta['ovabrw_pickup_date'] 			= $pickup_date;
				$item_meta['ovabrw_pickup_date_strtotime'] 	= strtotime( $pickup_date );
			}

			// Drop-off date
			$dropoff_date = isset( $args['ovabrw_dropoff_date'][$meta_key] ) ? $args['ovabrw_dropoff_date'][$meta_key] : '';
			if ( !$dropoff_date ) $dropoff_date = $pickup_date;
			if ( $dropoff_date ) {
				$item_meta['ovabrw_pickoff_date'] 			= $dropoff_date;
				$item_meta['ovabrw_pickoff_date_strtotime'] = strtotime( $dropoff_date );
			}

			// Get real data
	    	$read_data = $this->get_real_data( strtotime( $pickup_date ), strtotime( $dropoff_date ) );
	    	if ( ovabrw_array_exists( $read_data ) ) {
	    		// Pick-up date real
	    		$item_meta['ovabrw_pickup_date_real'] = ovabrw_get_meta_data( 'pickup_real', $read_data, $pickup_date );

	    		// Drop-off date real
	    		$item_meta['ovabrw_pickoff_date_real'] = ovabrw_get_meta_data( 'dropoff_real', $read_data, $dropoff_date );

	    		// Quantity real
				$quantity_real = ovabrw_get_meta_data( 'quantity_real', $read_data );
				if ( $quantity_real ) {
					$item_meta['ovabrw_total_days'] = str_replace( '<br>', ', ', $quantity_real );
				}

				// Price real
				$price_real = ovabrw_get_meta_data( 'price_real', $read_data );
				if ( $price_real ) {
					$item_meta['ovabrw_price_detail'] = str_replace( '<br>', ', ', $price_real );
				}
	    	}

	    	// Number of adults
	    	$numberof_adults = isset( $args['ovabrw_adults'][$meta_key] ) ? (int)$args['ovabrw_adults'][$meta_key] : '';
	    	if ( $numberof_adults ) {
	    		$item_meta['ovabrw_adults'] = $numberof_adults;
	    	}

	    	// Number of children
	    	$numberof_children = isset( $args['ovabrw_children'][$meta_key] ) ? (int)$args['ovabrw_children'][$meta_key] : '';
	    	if ( $numberof_children ) {
	    		$item_meta['ovabrw_children'] = $numberof_children;
	    	}

	    	// Number of babies
	    	$numberof_babies = isset( $args['ovabrw_babies'][$meta_key] ) ? (int)$args['ovabrw_babies'][$meta_key] : '';
	    	if ( $numberof_babies ) {
	    		$item_meta['ovabrw_babies'] = $numberof_babies;
	    	}

			// Quantity
			$quantity = isset( $args['ovabrw_quantity'][$meta_key] ) ? (int)$args['ovabrw_quantity'][$meta_key] : 1;
			if ( $quantity ) {
				$item_meta['ovabrw_number_vehicle'] = $quantity;
			}

			// Vehicle ID
			$vehicle_id = isset( $args['ovabrw_vehicle_id'][$meta_key] ) ? $args['ovabrw_vehicle_id'][$meta_key] : '';
			if ( $vehicle_id ) {
				$item_meta['id_vehicle'] = $vehicle_id;
			}

			// Custom checkout fields
			$cckf = $cckf_qty = [];

			// Get product cckf
	    	$product_cckf = $this->product->get_cckf();
	    	if ( ovabrw_array_exists( $product_cckf ) ) {
	    		// Loop
	    		foreach ( $product_cckf as $name => $fields ) {
	    			if ( 'on' !== ovabrw_get_meta_data( 'enabled', $fields ) ) continue;

	    			// Get type
	    			$type = ovabrw_get_meta_data( 'type', $fields );

	    			if ( 'file' === $type ) {
	    				// Get file
	    				$files = ovabrw_get_meta_data( sanitize_file_name( $name.'_'.$meta_key ), $_FILES );

    					// File name
    					$file_name = ovabrw_get_meta_data( 'name', $files );
    					if ( $file_name ) {
    						// File size
    						$file_size = (int)ovabrw_get_meta_data( 'size', $files );
    						$file_size = $file_size / 1048576;

    						// Max size
    						$max_size = (float)ovabrw_get_meta_data( 'max_file_size', $fields );
    						if ( $max_size < $file_size ) continue;

    						// File type
	                        $accept = apply_filters( OVABRW_PREFIX.'accept_file_upload', '.jpg, .jpeg, .png, .pdf, .doc, .docx', $this );

	                        // Get file extension
	                        $extension = pathinfo( $file_name, PATHINFO_EXTENSION );
	                        if ( strpos( $accept, $extension ) === false ) continue;

	                         // Upload file
	                        $overrides = [ 'test_form' => false ];

	                        if ( ! function_exists( 'wp_handle_upload' ) ) {
								require_once( ABSPATH . 'wp-admin/includes/file.php' );
							}

							// Upload
							$upload = wp_handle_upload( $files, $overrides );
							if ( ovabrw_get_meta_data( 'error', $upload ) ) continue;

	                        // File url
	                        $file_url = '<a href="'.esc_url( $upload['url'] ).'" target="_blank">';
								$file_url .= basename( $upload['file'] );
							$file_url .= '</a>';

							// Add cart item data
							$item_meta[$name] = $file_url;
    					}
	    			} elseif ( 'checkbox' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options values
	    				$opt_values = isset( $args[$name][$meta_key] ) ? $args[$name][$meta_key] : '';

	    				if ( ovabrw_array_exists( $opt_values ) ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_values;

	    					// Option quantities
	    					$opt_qtys = isset( $args[$name.'_qty'][$meta_key] ) ? $args[$name.'_qty'][$meta_key] : '';
	    					if ( ovabrw_array_exists( $opt_qtys ) ) {
	    						$cckf_qty = array_merge( $cckf_qty, $opt_qtys );
	    					}

	    					// Option keys
	    					$opt_keys = ovabrw_get_meta_data( 'ova_checkbox_key', $fields, [] );

	    					// Option texts
	    					$opt_texts = ovabrw_get_meta_data( 'ova_checkbox_text', $fields, [] );

	    					// Loop
	    					foreach ( $opt_values as $val ) {
	    						// Get index option
	    						$index = array_search( $val, $opt_keys );
	    						if ( is_bool( $index ) ) continue;

	    						// Option text
	    						$opt_text = ovabrw_get_meta_data( $index, $opt_texts );

	    						// Option qty
	    						$opt_qty = (int)ovabrw_get_meta_data( $val, $cckf_qty );

	    						if ( $opt_qty ) {
	    							array_push( $opt_names, sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_text, $opt_qty ) );
	    						} else {
	    							array_push( $opt_names, $opt_text );
	    						}
	    					} // END loop
	    				}

	    				// Add cart item data
	    				if ( ovabrw_array_exists( $opt_names ) ) {
	    					$item_meta[$name] = implode( ', ', $opt_names );
	    				}
	    			} elseif ( 'radio' === $type ) {
	    				// Get option value
	    				$opt_value = isset( $args[$name][$meta_key] ) ? $args[$name][$meta_key] : '';
	    				if ( $opt_value ) {
	    					// Add cckf
	    					$cckf[$name] = $opt_value;

	    					// Get option quantities
	    					$opt_qtys = isset( $args[$name.'_qty'][$meta_key] ) ? $args[$name.'_qty'][$meta_key] : '';

	    					// Option qty
	    					$opt_qty = (int)ovabrw_get_meta_data( $opt_value, $opt_qtys );

	    					// Add cart item data
	    					if ( $opt_qty ) {
	    						// Add cckf quantity
	    						$cckf_qty[$name] = $opt_qty;
	    						$item_meta[$name] = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_value, $opt_qty );
	    					} else {
	    						$item_meta[$name] = $opt_value;
	    					}
	    				}
	    			} elseif ( 'select' === $type ) {
	    				// Option names
	    				$opt_names = [];

	    				// Get options value
	    				$opt_value = isset( $args[$name][$meta_key] ) ? $args[$name][$meta_key] : '';
	    				if ( $opt_value ) {
	    					// Option keys
	    					$opt_keys = ovabrw_get_meta_data( 'ova_options_key', $fields );

	    					// Option texts
	    					$opt_texts = ovabrw_get_meta_data( 'ova_options_text', $fields );

	    					// Option quantities
	    					$opt_qtys = isset( $args[$name.'_qty'][$meta_key] ) ? $args[$name.'_qty'][$meta_key] : '';
	    					
	    					// Option quantity
	    					$opt_qty = (int)ovabrw_get_meta_data( $opt_value, $opt_qtys );

	    					// index option
	    					$index = array_search( $opt_value, $opt_keys );
	    					if ( is_bool( $index ) ) continue;

	    					// Add cckf
	    					$cckf[$name] = $opt_value;

	    					// Option text
	    					$opt_text = ovabrw_get_meta_data( $index, $opt_texts );

	    					// Add cart item data
	    					if ( $opt_qty ) {
	    						// Add cckf quantity
	    						$cckf_qty[$name] = $opt_qty;
	    						$item_meta[$name] = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_text, $opt_qty );
	    					} else {
	    						$item_meta[$name] = $opt_text;
	    					}
	    				}
	    			} else {
	    				// Option value
	    				$opt_value = isset( $args[$name][$meta_key] ) ? $args[$name][$meta_key] : '';

	    				if ( $opt_value ) {
	    					// Add cart item data
	    					$item_meta[$name] = $opt_value;
	    				}
	    			}
	    		} // END loop
	    	}

	    	// Add cckf to cart item data
	    	if ( ovabrw_array_exists( $cckf ) ) {
	    		$item_meta['ovabrw_custom_ckf'] 	= $cckf;
	    		$item_meta['ovabrw_custom_ckf_qty'] = $cckf_qty;
	    	}

			// Resources
			$resources = isset( $args['ovabrw_resource_checkboxs'][$meta_key] ) ? $args['ovabrw_resource_checkboxs'][$meta_key] : '';
			if ( ovabrw_array_exists( $resources ) ) {
				// Resources quantity
				$resources_qty = isset( $args['ovabrw_resource_quantity'][$meta_key] ) ? $args['ovabrw_resource_quantity'][$meta_key] : '';

	    		// Get resource ids
	    		$resc_ids = $this->get_meta_value( 'resource_id', [] );

	    		// Get resource names
	    		$resc_names = $this->get_meta_value( 'resource_name', [] );

	    		// init option names
	    		$opt_names = [];

	    		foreach ( $resources as $opt_id ) {
	    			// Get index option
	    			$index = array_search( $opt_id, $resc_ids );
	    			if ( is_bool( $index ) ) continue;

	    			// Option name
	    			$opt_name = ovabrw_get_meta_data( $index, $resc_names );
	    			if ( !$opt_name ) continue;

	    			// Option quantity
	    			$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $resources_qty );
	    			if ( $opt_qty ) {
	    				$opt_names[] = sprintf( esc_html__( '%s (%s)', 'ova-brw' ), $opt_name, $opt_qty );
	    			} else {
	    				$opt_names[] = $opt_name;
	    			}
	    		}

	    		// Add option names
	    		if ( ovabrw_array_exists( $opt_names ) ) {
	    			$item_meta[sprintf( _n( 'Resource%s', 'Resources%s', count( $opt_names ), 'ova-brw' ), '' )] = implode( ', ', $opt_names );
	    		}

	    		// Add resources
	    		$item_meta['ovabrw_resources'] = $resources;

	    		// Add resources quantity
	    		if ( ovabrw_array_exists( $resources_qty ) ) {
	    			$item_meta['ovabrw_resources_qty'] = $resources_qty;
	    		}
	    	} // END if

			// Services
			$services = isset( $args['ovabrw_service'][$meta_key] ) ? $args['ovabrw_service'][$meta_key] : '';
			if ( ovabrw_array_exists( $services ) ) {
	    		// Services quantity
				$services_qty = isset( $args['ovabrw_service_qty'][$meta_key] ) ? $args['ovabrw_service_qty'][$meta_key] : '';

	    		// Get service labels
	    		$serv_labels = $this->get_meta_value( 'label_service', [] );

	    		// Get option ids
	    		$opt_ids = $this->get_meta_value( 'service_id', [] );

	    		// Get option names
	    		$opt_names = $this->get_meta_value( 'service_name', [] );

	    		// Loop
	    		foreach ( $services as $opt_id ) {
	    			// Option quantity
	    			$opt_qty = (int)ovabrw_get_meta_data( $opt_id, $services_qty );

	    			// Loop option ids
	    			foreach ( $opt_ids as $i => $ids ) {
	    				// Get index option
	    				$index = array_search( $opt_id, $ids );
	    				if ( is_bool( $index ) ) continue;

	    				// Service label
	    				$label = ovabrw_get_meta_data( $i, $serv_labels );
	    				if ( !$label ) continue;

	    				// Option name
	    				$opt_name = isset( $opt_names[$i][$index] ) ? $opt_names[$i][$index] : '';
	    				if ( !$opt_name ) continue;

	    				// Opt qty
	    				if ( $opt_qty ) {
	    					$opt_name = sprintf( esc_html__( '%s (x%s)', 'ova-brw' ), $opt_name, $opt_qty );
	    				}

	    				// Add option name
	    				$item_meta[$label] = $opt_name;
	    			}
	    			// END loop option ids
	    		} // END loop services

	    		// Add services
	    		$item_meta['ovabrw_services'] = $services;

	    		// Add services quantity
	    		if ( ovabrw_array_exists( $services_qty ) ) {
	    			$item_meta['ovabrw_services_qty'] = $services_qty;
	    		}
	    	} // END if

	    	// Insurance
			$insurance = isset( $args['ovabrw_amount_insurance'][$meta_key] ) ? (float)$args['ovabrw_amount_insurance'][$meta_key] : '';
			if ( $insurance ) {
				// Add item meta
				$item_meta['ovabrw_insurance_amount'] = $insurance;

				// Item data
				$results['insurance_amount'] += $insurance;

				// Get insurance tax
				$insurance_tax = OVABRW()->options->get_insurance_tax_amount( $insurance );
				if ( $insurance_tax ) {
					// Add item meta
					$item_meta['ovabrw_insurance_tax'] = $insurance_tax;

					// Item data
					$results['insurance_tax'] += $insurance_tax;
				}
			}

			// Deposit
			$deposit = isset( $args['ovabrw_amount_deposite'][$meta_key] ) ? (float)$args['ovabrw_amount_deposite'][$meta_key] : '';

			// Remaining
			$remaining = isset( $args['ovabrw_amount_remaining'][$meta_key] ) ? (float)$args['ovabrw_amount_remaining'][$meta_key] : '';

			// Subtotal
			$subtotal = isset( $args['ovabrw_total'][$meta_key] ) ? (float)$args['ovabrw_total'][$meta_key] : 0;
			if ( $insurance ) $subtotal -= $insurance;
			if ( $deposit ) {
				// Item data
				$results['is_deposit'] = true;

				// Deposit amount
				$results['deposit_amount'] = $deposit;

				// Remaining amount
				$results['remaining_amount'] = $remaining;

				// Subtotal
				$results['subtotal'] = $deposit;

				// Deposit type
				$item_meta['ovabrw_deposit_type'] = 'value';

				// Deposit value
				$item_meta['ovabrw_deposit_value'] = $deposit;

				// Deposit amount
				$item_meta['ovabrw_deposit_amount'] = $deposit;

				// Remaining
				$item_meta['ovabrw_remaining_amount'] = $remaining;

				// Total payable
				$item_meta['ovabrw_total_payable'] = $subtotal;

				// Subtotal
				$subtotal = $deposit;
			} else {
				// Subtotal
				$results['subtotal'] = $subtotal;
			} // END if

			// Taxable
			$taxes = false;
			if ( wc_tax_enabled() ) {
				$tax_rates = WC_Tax::get_rates( $this->product->get_tax_class() );
                if ( ovabrw_array_exists( $tax_rates ) ) {
                    $results['tax_rate_id'] = key( $tax_rates );
                }

                // Remaining tax
                $remaining_tax = OVABRW()->options->get_taxes_by_price( $this->product, $remaining );
                if ( $remaining_tax ) {
                	// Item data
                	$results['remaining_tax'] = $remaining_tax;

                	// Item meta
                	$item_meta['ovabrw_remaining_tax'] = $remaining_tax;
                }

                // Prices include tax
                if ( wc_prices_include_tax() ) {
                	// Get taxes
                    $taxes = WC_Tax::calc_inclusive_tax( $subtotal, $tax_rates );

                    // Tax total
                    $tax_total = WC_Tax::get_tax_total( $taxes );

                    // Subtotal
                    $subtotal -= $tax_total;

                    // Item data
                    $results['tax_amount'] += $tax_total;
                } else {
                	// Get taxes
                    $taxes = WC_Tax::calc_exclusive_tax( $item_subtotal, $tax_rates );

                    // Tax total
                    $tax_total = WC_Tax::get_tax_total( $taxes );
                    
                    // Item data
                    $results['tax_amount'] += $tax_total;

                    // Item data
                    $results['subtotal'] += $remaining_tax;
                }

                // Taxes
                $taxes = [
                	'total'    => $taxes,
                    'subtotal' => $taxes
                ];
			} // END if

			// Get item id
            $item_id = $order->add_product( $this->product, $quantity, [
                'totals' => [
                    'subtotal'  => $subtotal,
                    'total'     => $subtotal
                ],
                'taxes'  => $taxes
            ]);

            // Get order line item
            $item = $order->get_item( $item_id );

            // Update item meta data
            foreach ( $item_meta as $meta_key => $meta_value ) {
                $item->add_meta_data( $meta_key, $meta_value, true );
            }

            // Save item
            $item->save();

			return apply_filters( $this->prefix.'new_booking_handle_item', $results, $meta_key, $args, $order, $this );
		}

		/**
	     * Get add to cart data
	     */
	    public function get_add_to_cart_data() {
	    	// Cart item
	    	$cart_item_data = [];

	    	// Pick-up date
	    	$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $_REQUEST ) );
	    	if ( !$pickup_date ) return false;

	    	// Drop-off date
	    	$dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $_REQUEST ) );
	    	if ( $this->product->show_date_field( 'dropoff' ) ) {
	    		if ( !$dropoff_date ) return false;
	    	} else {
	    		if ( !$dropoff_date ) $dropoff_date = $pickup_date;
	    	}

	    	// Get date format
	    	$date_format = OVABRW()->options->get_date_format();

	    	// Get time format
	    	$time_format = OVABRW()->options->get_time_format();

	    	// Add pick-up date
	    	$cart_item_data['pickup_date'] = gmdate( $date_format, $pickup_date );

	    	// Add drop-off date
	    	$cart_item_data['dropoff_date'] = gmdate( $date_format, $dropoff_date );

	    	// Number of guests
	    	$numberof_guests = 0;

	    	// Number of adults
	    	$numberof_adults = (int)ovabrw_get_meta_data( 'adults', $_REQUEST );
	    	if ( $numberof_adults ) {
	    		$numberof_guests += $numberof_adults;
	    		$cart_item_data['numberof_adults'] = $numberof_adults;
	    	} // END if

	    	// Number of children
	    	$numberof_children = (int)ovabrw_get_meta_data( 'children', $_REQUEST );
	    	if ( $numberof_children ) {
	    		$numberof_guests += $numberof_children;
	    		$cart_item_data['numberof_children'] = $numberof_children;
	    	} // END if

	    	// Number of babies
	    	$numberof_babies = (int)ovabrw_get_meta_data( 'babies', $_REQUEST );
	    	if ( $numberof_babies ) {
	    		$numberof_guests += $numberof_babies;
	    		$cart_item_data['numberof_babies'] = $numberof_babies;
	    	} // END if

	    	// Check number of guests
	    	if ( !$numberof_guests ) return false;

	    	// Add quantity
	    	$cart_item_data['ovabrw_quantity'] = ovabrw_get_meta_data( 'quantity', $_REQUEST, 1 );

	    	return apply_filters( $this->prefix.'get_add_to_cart_data', $cart_item_data, $this );
	    }
	}
}