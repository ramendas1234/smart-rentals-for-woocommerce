<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Rental By Mixed
 */
if ( !class_exists( 'OVABRW_Rental_By_Period_Time' ) ) {

	class OVABRW_Rental_By_Period_Time extends OVABRW_Rental_Types {

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
			return 'period_time';
		}

		/**
		 * Get meta fields
		 */
		public function get_meta_fields() {
			return (array)apply_filters( $this->prefix.$this->get_type().'_get_meta_fields', [
				'rental-type',
				'insurance',
				'packages',
				'deposit',
				'inventory',
				'locations',
				'specifications',
				'features',
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
				'pickup-location',
				'dropoff-location',
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
				'pickup-location',
				'dropoff-location',
				'pickup-date',
				'package',
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
			// init
			$min = $max = 0;

            $petime_price = $this->get_meta_value( 'petime_price' );

            if ( ovabrw_array_exists( $petime_price ) ) {
                $min = min( $petime_price );
                $max = max( $petime_price );
            }

            if ( $min && $max && $min == $max ) {
                $new_price = sprintf( esc_html__( 'From %s', 'ova-brw' ), ovabrw_wc_price( $min, [ 'currency' => $currency ] ) );
            } elseif ( $min && $max ) {
                $new_price = sprintf( esc_html__( '%s - %s', 'ova-brw' ), ovabrw_wc_price( $min, [ 'currency' => $currency ] ), ovabrw_wc_price( $max, [ 'currency' => $currency ] ) );
            } else {
                $new_price = esc_html__( 'Option Price', 'ova-brw' );
            }

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
	    	$regular_price 	= '';
	    	$filter_price 	= apply_filters( $this->prefix.'show_price_calendar_type', 'highest' );

			// Package prices
			$package_prices = $this->get_meta_value( 'petime_price' );
			if ( !ovabrw_array_exists( $package_prices ) ) $package_prices = [];

			if ( 'highest' === $filter_price ) {
				$regular_price = max( $package_prices );
			} elseif ( 'average' === $filter_price ) {
				$regular_price = array_sum( $package_prices ) / count( $package_prices );
			} elseif ( 'lowest' === $filter_price ) {
				$regular_price = min( $package_prices );
			}

	    	// Get price html
	    	if ( $regular_price ) {
	    		$regular_price = OVABRW()->options->get_calendar_price_html( $regular_price );
	    	}

	    	return apply_filters( $this->prefix.'get_calendar_regular_price', $regular_price, $this );
	    }

	    /**
	     * Get calendar special prices
	     */
	    public function get_calendar_special_prices() {
	    	// init
	    	$special_prices = [];

	    	// Prices
			$prices = [];

			// From dates
			$from_dates = [];

			// To dates
			$to_dates = [];

			// Get discounts
			$discounts = $this->get_meta_value( 'petime_discount' );
			if ( ovabrw_array_exists( $discounts ) ) {
				// index
    			$index = '';

    			// Package prices
    			$package_prices = $this->get_meta_value( 'petime_price' );

    			// Filter price
    			$filter_price = apply_filters( $this->prefix.'show_price_calendar_type', 'highest' );
    			if ( 'highest' === $filter_price ) {
    				$index = array_search( max( $package_prices ), $package_prices );
    			} elseif ( 'lowest' === $filter_price ) {
    				$index = array_search( (string)min( $package_prices ), $package_prices );
    			}

    			if ( is_numeric( $index ) ) {
    				// Prices
    				$prices = isset( $discounts[$index]['price'] ) ? $discounts[$index]['price'] : [];

    				// From dates
    				$from_dates = isset( $discounts[$index]['start_time'] ) ? $discounts[$index]['start_time'] : [];

    				// To dates
    				$to_dates = isset( $discounts[$index]['end_time'] ) ? $discounts[$index]['end_time'] : [];
    			}
			}

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
	     * Get new date
	     */
	    public function get_new_date( $args = [] ) {
	    	// Pick-up date
	    	$pickup_date = ovabrw_get_meta_data( 'pickup_date', $args );
	    	if ( !$pickup_date ) return false;

	    	// Package ID
	    	$package_id = ovabrw_get_meta_data( 'package_id', $args );
	    	if ( !$package_id ) return false;
	    	
	    	// Get package data
	    	$package_data = $this->get_package_data( $pickup_date, $package_id );
	    	if ( ovabrw_array_exists( $package_data ) ) {
	    		$pickup_date 	= ovabrw_get_meta_data( 'start', $package_data );
	    		$dropoff_date 	= ovabrw_get_meta_data( 'end', $package_data );
	    	}

	    	// Check pick-up & drop-off dates
	    	if ( !$pickup_date || !$dropoff_date ) return false;

	    	return apply_filters( $this->prefix.'get_new_date', [
	    		'pickup_date' 	=> $pickup_date,
	    		'dropoff_date' 	=> $dropoff_date
	    	], $args );
	    }

	    /**
	     * Get package data
	     */
	    public function get_package_data( $pickup_date, $package_id ) {
	    	if ( !$pickup_date || !$package_id ) return false;

	    	// Timestamp
	    	$timestamp = '';

	    	// New dates
	    	$new_pickup = $new_dropoff = '';

	    	// Package price
	    	$package_price = 0;

	    	// Package type
	    	$package_type = '';

	    	// Package lable
	    	$package_label = '';

	    	// Get unfixed time
	    	$unfixed_time = $this->get_meta_value( 'unfixed_time' );
	    	if ( 'yes' === $unfixed_time ) {
	    		$timestamp = strtotime( gmdate( OVABRW()->options->get_datetime_format(), $pickup_date ) );
	    	} else {
	    		$timestamp = strtotime( gmdate( OVABRW()->options->get_date_format(), $pickup_date ) );
	    	}

	    	// Pick-up validation
	    	if ( !$timestamp ) return false;

	    	// Get package ids
	    	$pk_ids = $this->get_meta_value( 'petime_id' );
	    	if ( ovabrw_array_exists( $pk_ids ) ) {
	    		// Date format
	    		$date_format = OVABRW()->options->get_date_format();

	    		// Time format
	    		$time_format = OVABRW()->options->get_time_format();

	    		// Package prices
    			$pk_prices = $this->get_meta_value( 'petime_price' );

    			// Package type
    			$pk_type = $this->get_meta_value( 'package_type' );

    			// Package days
    			$pk_days = $this->get_meta_value( 'petime_days' );

    			// Package hours
    			$pk_hours = $this->get_meta_value( 'pehour_unfixed' );

    			// Package start time
    			$pk_start_time = $this->get_meta_value( 'pehour_start_time' );

    			// Package end time
    			$pk_end_time = $this->get_meta_value( 'pehour_end_time' );

    			// Package labels
    			$pk_labels = $this->get_meta_value( 'petime_label' );

    			// Package discounts
    			$pk_discounts = $this->get_meta_value( 'petime_discount' );

	    		foreach ( $pk_ids as $i => $pk_id ) {
	    			if ( $pk_id != $package_id ) continue;

	    			$package_price 	= (float)ovabrw_get_meta_data( $i, $pk_prices );
	    			$package_type 	= ovabrw_get_meta_data( $i, $pk_type );
	    			$package_label 	= ovabrw_get_meta_data( $i, $pk_labels );
	    			$discounts 		= ovabrw_get_meta_data( $i, $pk_discounts );

	    			if ( 'inday' === $package_type ) {
	    				if ( 'yes' === $unfixed_time ) {
	    					// Get number of hours
	    					$numberof_hours = (float)ovabrw_get_meta_data( $i, $pk_hours );

	    					// New pickup
	    					$new_pickup = $timestamp;

	    					// New dropoff
	    					$new_dropoff = $timestamp + $numberof_hours * 3600;
	    				} else {
	    					$start_time = strtotime( ovabrw_get_meta_data( $i, $pk_start_time ) );
	    					$end_time 	= strtotime( ovabrw_get_meta_data( $i, $pk_end_time ) );

	    					// New pickup
	    					$new_pickup = $start_time ? strtotime( gmdate( $date_format, $timestamp ) . ' ' . gmdate( $time_format, $start_time ) ) : 0;

	    					// New dropoff
	    					$new_dropoff = $end_time ? strtotime( gmdate( $date_format, $timestamp ) . ' ' . gmdate( $time_format, $end_time ) ) : 0;
	    				}
	    			} elseif ( 'other' === $package_type ) {
	    				// Get number of days
    					$numberof_days = (float)ovabrw_get_meta_data( $i, $pk_days );

    					// New pickup
    					$new_pickup = $timestamp;

    					// New dropoff
    					$new_dropoff = $timestamp + $numberof_days * 86400;
	    			}

	    			// Discounts
	    			if ( ovabrw_array_exists( $discounts ) ) {
	    				// Discount prices
	    				$disc_prices = ovabrw_get_meta_data( 'price', $discounts );

	    				// Discount from date
	    				$disc_from = ovabrw_get_meta_data( 'start_time', $discounts );

	    				// Discount to date
	    				$disc_to = ovabrw_get_meta_data( 'end_time', $discounts );

	    				if ( ovabrw_array_exists( $disc_prices ) ) {
	    					foreach ( $disc_prices as $j => $price ) {
	    						// From date
	    						$from = strtotime( ovabrw_get_meta_data( $j, $disc_from ) );
	    						if ( !$from ) continue;

	    						// To date
	    						$to = strtotime( ovabrw_get_meta_data( $j, $disc_to ) );
	    						if ( !$to ) continue;

	    						if ( $from <= $new_pickup && $new_pickup <= $to ) {
	    							$package_price = (float)$price;

	    							// Break out of the loop
	    							break;
	    						}
	    					}
	    				}
	    			} // END discounts

	    			// Break out of the loop
	    			break;
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_package_data', [
	    		'id' 		=> $package_id,
	    		'label' 	=> $package_label,
	    		'type' 		=> $package_type,
	    		'price' 	=> $package_price,
	    		'start' 	=> $new_pickup,
	    		'end' 		=> $new_dropoff
	    	], $pickup_date, $package_id, $this );
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

			// Package ID
			$package_id = ovabrw_get_meta_data( 'package_id', $args );
			if ( !$package_id ) $package_id = ovabrw_get_meta_data( 'ovabrw_package_id', $args );
			if ( !$package_id ) {
				$mesg = esc_html__( 'Package is required', 'ova-brw' );
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
		}

		/**
	     * Get time between leases
	     */
	    public function get_time_between_leases() {
	    	return apply_filters( $this->prefix.'get_time_between_leases', (float)$this->get_meta_value( 'prepare_vehicle' ) * 60, $this );
	    }

	    /**
	     * Get rental calculations
	     */
	    public function get_rental_calculations( $args = [] ) {
	    	// Pick-up date
	    	$pickup_date = ovabrw_get_meta_data( 'pickup_date', $args );
	    	if ( !$pickup_date ) return 0;

	    	// Package ID
	    	$package_id = ovabrw_get_meta_data( 'package_id', $args );
	    	if ( !$package_id ) return 0;

	    	// Drop-off date
	    	$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $args );
	    	if ( !$dropoff_date ) return 0;

	    	// init
	    	$rental_price = 0;

	    	// Get package ids
	    	$package_ids = $this->get_meta_value( 'petime_id' );
	    	if ( ovabrw_array_exists( $package_ids ) ) {
	    		// Get package prices
	    		$package_prices = $this->get_meta_value( 'petime_price' );

	    		// Get package discounts
	    		$package_discounts = $this->get_meta_value( 'petime_discount' );

	    		// Loop
	    		foreach ( $package_ids as $i => $pk_id ) {
	    			if ( $pk_id != $package_id ) continue;

	    			// Get package price
	    			$pk_price = (float)ovabrw_get_meta_data( $i, $package_prices );

	    			// Get discounts
	    			$discounts = ovabrw_get_meta_data( $i, $package_discounts );
	    			if ( ovabrw_array_exists( $discounts ) ) {
	    				$discount_prices = $this->get_discount_prices( $pickup_date, $dropoff_date, $discounts );
	    				if ( !is_bool( $discount_prices ) ) $pk_price = $discount_prices;
	    			}

	    			// Rental price
	    			$rental_price += $pk_price;

	    			// Break out of the loop
	    			break;
	    		} // END loop
	    	}

	    	// Pick-up location
	    	$pickup_location = ovabrw_get_meta_data( 'pickup_location', $args );

	    	// Drop-off location
	    	$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $args );

	    	// Location prices
	    	$location_prices = $this->get_location_prices( $pickup_location, $dropoff_location );
	    	if ( $location_prices ) $rental_price += $location_prices;

	    	return apply_filters( $this->prefix.'get_rental_calculations', $rental_price, $args, $this );
	    }

	    /**
	     * Get discount prices
	     */
	    public function get_discount_prices( $pickup_date, $dropoff_date, $discounts ) {
	    	// init
	    	$price = false;

	    	// Get discount prices
	    	$discount_prices = ovabrw_get_meta_data( 'price', $discounts );

	    	// Get discount start
	    	$discount_start = ovabrw_get_meta_data( 'start_time', $discounts );

	    	// Get discount end
	    	$discount_end = ovabrw_get_meta_data( 'end_time', $discounts );

	    	// Loop
	    	if ( ovabrw_array_exists( $discount_start ) ) {
	    		foreach ( $discount_start as $i => $start ) {
	    			// Start
	    			$start = strtotime( $start );
	    			if ( !$start ) continue;

	    			// End
	    			$end = strtotime( ovabrw_get_meta_data( $i, $discount_end ) );
	    			if ( !$end ) continue;

	    			if ( $start <= $pickup_date && $dropoff_date <= $end ) {
						$price = (float)ovabrw_get_meta_data( $i, $discount_prices );
						break;
					}
	    		}
	    	} // END loop

	    	return apply_filters( $this->prefix.'get_package_discount_prices', $price, $pickup_date, $dropoff_date, $discounts );
	    }

	    /**
	     * Get packages
	     */
	    public function get_packages( $pickup_date, $pickup_location, $dropoff_location ) {
	    	if ( !$pickup_date ) return false;

	    	// init
	    	$packages = [];

	    	// Package ids
	    	$package_ids = $this->get_meta_value( 'petime_id' );

	    	// Package labels
	    	$package_labels = $this->get_meta_value( 'petime_label' );

	    	if ( ovabrw_array_exists( $package_ids ) ) {
	    		foreach ( $package_ids as $i => $package_id ) {
	    			// Label
	    			$label = ovabrw_get_meta_data( $i, $package_labels );

	    			// Get new date
	    			$new_date = $this->get_new_date([
	    				'pickup_date' 	=> $pickup_date,
						'package_id' 	=> $package_id
	    			]);

	    			// Pick-up date
					$new_pickup_date = ovabrw_get_meta_data( 'pickup_date', $new_date );
					if ( !$new_pickup_date ) {
						array_push( $packages, [
							'id' 		=> $package_id,
							'label' 	=> $label,
							'disabled' 	=> true
						]);
						continue;
					}

					// Drop-off date
					$new_dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $new_date );
					if ( !$new_dropoff_date ) {
						array_push( $packages, [
							'id' 		=> $package_id,
							'label' 	=> $label,
							'disabled' 	=> true
						]);
						continue;
					}

					// Booking validation
					$booking_validation = $this->booking_validation( $new_pickup_date, $new_dropoff_date, [ 'package_id' => $package_id ] );
					if ( $booking_validation ) {
						array_push( $packages, [
							'id' 		=> $package_id,
							'label' 	=> $label,
							'disabled' 	=> true
						]);
						continue;
					}

					// Get items available
					$items_available = $this->get_items_available( $new_pickup_date, $new_dropoff_date, $pickup_location, $dropoff_location, 'cart' );

					// Vehicles available
					if ( is_array( $items_available ) ) {
						$items_available = count( $items_available );
					}

					if ( !$items_available ) {
						array_push( $packages, [
							'id' 		=> $package_id,
							'label' 	=> $label,
							'disabled' 	=> true
						]);
						continue;
					}

					// Add package
					array_push( $packages, [
						'id' 	=> $package_id,
						'label' => $label
					]);
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_packages', $packages, $pickup_date, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get HTML package options
	     */
	    public function get_package_options_html( $packages = [] ) {
	    	// init
	    	$option = '';

	    	if ( ovabrw_array_exists( $packages ) ) {
	    		// Disabled
	    		$disabled = true;

	    		foreach ( $packages as $package ) {
	    			// Disabled
	    			if ( !ovabrw_get_meta_data( 'disabled', $package ) ) {
	    				$disabled = false;
	    			}

	    			$option .= '<option value="' . $package['id'] . '"'.ovabrw_disabled( ovabrw_get_meta_data( 'disabled', $package ), true, false ).'>';
		    			$option .= $package['label'];
		    		$option .= '</option>';
	    		}

	    		if ( $disabled ) {
	    			$option = '<option value="">'. esc_html__( 'There are no packages available', 'ova-brw' ) . '</option>' . $option;
	    		}
	    	} else {
	    		$option .= '<option value="">';
	    			$option .= esc_html__( 'There are no packages available', 'ova-brw' );
	    		$option .= '</option>';
	    	}

	    	return apply_filters( $this->prefix.'get_package_options_html', $option, $packages, $this );
	    }

	    /**
		 * Add rental cart item data
		 */
		public function add_rental_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
			// Rental type
	    	$cart_item_data['rental_type'] = $this->get_type();

	    	// Get pick-up & drop-off dates
	    	if ( !ovabrw_get_meta_data( 'pickup_date', $cart_item_data ) || !ovabrw_get_meta_data( 'dropoff_date', $cart_item_data ) ) {
	    		// Pick-up date
		    	$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_date', $_REQUEST ) );

		    	// Package ID
		    	$package_id = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_package_id', $_REQUEST ) );
		    	$cart_item_data['package_id'] = $package_id;

		    	// Get package data
		    	$package_data = $this->get_package_data( strtotime( $pickup_date ), $package_id );
		    	if ( !ovabrw_array_exists( $package_data ) ) return $cart_item_data;

		    	// Package label
		    	$cart_item_data['package_label'] = ovabrw_get_meta_data( 'label', $package_data );

		    	// Package type
		    	$cart_item_data['package_type'] = ovabrw_get_meta_data( 'type', $package_data );

		    	// Package price
		    	$cart_item_data['package_price'] = ovabrw_get_meta_data( 'price', $package_data );

		    	// Unfixed time
		    	$unfixed_time = $this->get_meta_value( 'unfixed_time' );

		    	// Date format
		    	$date_format = OVABRW()->options->get_datetime_format();
		    	if ( 'inday' !== $cart_item_data['package_type'] && 'yes' !== $unfixed_time ) {
		    		$date_format = OVABRW()->options->get_date_format();
		    	}

		    	// Pick-up date
		    	$pickup_date = gmdate( $date_format, ovabrw_get_meta_data( 'start', $package_data ) );
		    	if ( !$pickup_date ) return $cart_item_data;
		    	$cart_item_data['pickup_date'] = $pickup_date;

		    	// Drop-off date
		    	$dropoff_date = gmdate( $date_format, ovabrw_get_meta_data( 'end', $package_data ) );
		    	if ( !$dropoff_date ) return $cart_item_data;
		    	$cart_item_data['dropoff_date'] = $dropoff_date;
	    	}

	    	// Pick-up real date
	    	$cart_item_data['pickup_real'] = $cart_item_data['pickup_date'];

	    	// Drop-off real dates
	    	$cart_item_data['dropoff_real'] = $cart_item_data['dropoff_date'];

			// Pick-up location
			if ( !ovabrw_get_meta_data( 'pickup_location', $cart_item_data ) ) {
				$pickup_location = trim( sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_location', $_REQUEST ) ) );
		    	$cart_item_data['pickup_location'] = $pickup_location;
			} // END if

	    	// Drop-off location
	    	if ( !ovabrw_get_meta_data( 'dropoff_location', $cart_item_data ) ) {
	    		$dropoff_location = trim( sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_dropoff_location', $_REQUEST ) ) );
		    	$cart_item_data['dropoff_location'] = $dropoff_location;
	    	} // END if

	    	// Location prices
	    	$location_prices = $this->get_location_prices( $cart_item_data['pickup_location'], $cart_item_data['dropoff_location'] );
	    	if ( $location_prices ) {
	    		$cart_item_data['location_prices'] = $location_prices;
	    	}

	    	// Quantity real
	    	$cart_item_data['quantity_real'] = ovabrw_get_meta_data( 'package_label', $cart_item_data );

	    	// Price real
	    	$cart_item_data['price_real'] = ovabrw_get_meta_data( 'package_price', $cart_item_data ) ? ovabrw_wc_price( wc_get_price_including_tax( $this->product, [ 'price' => $cart_item_data['package_price'] ] ) ) : 0;

			return apply_filters( $this->prefix.'add_rental_cart_item_data', $cart_item_data, $product_id, $variation_id, $quantity, $this );
		}

		/**
		 * Get rental cart item data
		 */
		public function get_rental_cart_item_data( $item_data, $cart_item ) {
			if ( !ovabrw_array_exists( $item_data ) ) $item_data = [];
			
			// Pick-up location
			$pickup_location = ovabrw_get_meta_data( 'pickup_location', $cart_item );
			if ( $pickup_location ) {
				$item_data[] = [
					'key' 		=> esc_html__( 'Pick-up Location', 'ova-brw' ),
					'value' 	=> wc_clean( $pickup_location ),
					'display' 	=> wc_clean( $pickup_location ),
					'hidden' 	=> !$this->product->show_location_field() ? true : false
				];
			}

			// Drop-off location
			$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $cart_item );
			if ( $dropoff_location ) {
				$item_data[] = [
					'key' 		=> esc_html__( 'Drop-off Location', 'ova-brw' ),
					'value' 	=> wc_clean( $dropoff_location ),
					'display' 	=> wc_clean( $dropoff_location ),
					'hidden' 	=> !$this->product->show_location_field( 'dropoff' ) ? true : false
				];
			}

			// Location price
			$location_price = (float)ovabrw_get_meta_data( 'location_prices', $cart_item );
			if ( $location_price ) {
				$item_data[] = [
					'key'     => esc_html__( 'Location Price', 'ova-brw' ),
		            'value'   => wc_clean( $location_price ),
		            'display' => ovabrw_wc_price( $location_price )
				];
			}

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

			// Package label
			$package_label = ovabrw_get_meta_data( 'package_label', $cart_item );
			if ( $package_label ) {
				$item_data[] = [
					'key'     => esc_html__( 'Package', 'ova-brw' ),
		            'value'   => wc_clean( $package_label ),
		            'display' => wc_clean( $package_label )
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

			// Vehicles available
			$vehicles_available = ovabrw_get_meta_data( 'vehicles_available', $cart_item );
			if ( $vehicles_available ) {
				$item_data[] = [
					'key'     => esc_html__( 'Vehicle ID(s)', 'ova-brw' ),
		            'value'   => wc_clean( $vehicles_available ),
		            'display' => wc_clean( $vehicles_available ),
		            'hidden'  => !$this->product->show_quantity() ? true : false
				];
			}

			return apply_filters( $this->prefix.'get_rental_cart_item_data', $item_data, $cart_item, $this );
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

    		// Location prices
    		$location_prices = $this->get_location_prices( $pickup_location, $dropoff_location );
    		if ( $location_prices ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Location price: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . ovabrw_wc_price( $location_prices ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Pick-up date
    		$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_date', $data ) );

    		// Drop-off date
    		$dropoff_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_dropoff_date', $data ) );

    		// Package data
    		$package_label = $package_price = $package_type = '';

    		// Package ID
    		$package_id = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_package_id', $data ) );
    		if ( $package_id ) {
    			// Date format
    			$date_format = OVABRW()->options->get_datetime_format();

    			// Unfixed time
	    		$unfixed_time = $this->get_meta_value( 'unfixed_time' );

	    		// Get package data
	    		$package_data = $this->get_package_data( strtotime( $pickup_date ), $package_id );
	    		if ( ovabrw_array_exists( $package_data ) ) {
	    			if ( 'yes' !== $unfixed_time && 'inday' !== ovabrw_get_meta_data( 'type', $package_data ) ) {
	    				$date_format = OVABRW()->options->get_date_format();
	    			}

	    			// Package label
	    			$package_label = ovabrw_get_meta_data( 'label', $package_data );

	    			// Package type
	    			$package_type = ovabrw_get_meta_data( 'type', $package_data );

	    			// Package price
	    			$package_price = ovabrw_get_meta_data( 'price', $package_data );

	    			// Package start
	    			$package_start = ovabrw_get_meta_data( 'start', $package_data );
	    			if ( $package_start ) {
	    				$pickup_date = gmdate( $date_format, $package_start );
	    			}

	    			// Package end
	    			$package_end = ovabrw_get_meta_data( 'end', $package_data );
	    			if ( $package_end ) {
	    				$dropoff_date = gmdate( $date_format, $package_end );
	    			}
	    		}
    		} // END if

    		// Pick-up date
    		if ( $pickup_date ) {
    			$order_details .= '<tr>';
					$order_details .= '<td>' . esc_html( $this->product->get_date_label() ) . ':</td>';
					$order_details .= '<td>' . esc_html( $pickup_date ) . '</td>';
				$order_details .= '</tr>';
    		}

    		// Drop-off date
    		if ( $this->product->show_date_field( 'dropoff', 'request' ) && $dropoff_date ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html( $this->product->get_date_label( 'dropoff' ) ) . ':</td>';
    				$order_details .= '<td>' . esc_html( $dropoff_date ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Package label
    		if ( $package_label ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Package: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $package_label ) . '</td>';
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
			    			$order_details .= '<td>' . implode( ', ', $resc_values ) . '</td>';  
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
					'location_prices' 	=> $location_prices,
					'pickup_date' 		=> $pickup_date,
					'dropoff_date' 		=> $dropoff_date,
					'package_id' 		=> $package_id,
					'package_label' 	=> $package_label,
					'package_type' 		=> $package_type,
					'package_price' 	=> $package_price,
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

			// Pick-up location
			$pickup_location = ovabrw_get_meta_data( 'pickup_location', $data );

			// Drop-off location
			$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $data );

			// Location prices
			$location_prices = ovabrw_get_meta_data( 'location_prices', $data );

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $data );

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $data );

			// Package ID
			$package_id = ovabrw_get_meta_data( 'package_id', $data );

			// Package label
			$package_label = ovabrw_get_meta_data( 'package_label', $data );

			// Package type
			$package_type = ovabrw_get_meta_data( 'package_type', $data );

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
	        	'pickup_location' 	=> $pickup_location,
	        	'dropoff_location' 	=> $dropoff_location,
	        	'package_id' 		=> $package_id,
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

        		// Pick-up location
        		if ( $pickup_location ) {
        			$line_item->add_meta_data( 'ovabrw_pickup_loc', $pickup_location, true );
        		}

        		// Drop-off location
        		if ( $dropoff_location ) {
        			$line_item->add_meta_data( 'ovabrw_pickoff_loc', $dropoff_location, true );
        		}

        		// Location prices
        		if ( $location_prices ) {
        			$line_item->add_meta_data( 'ovabrw_location_prices', $location_prices, true );
        		}

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

		    	// Pick-up real date
		    	$line_item->add_meta_data( 'ovabrw_pickup_date_real', $pickup_date, true );
		    	
		    	// Drop-off real dates
		    	$line_item->add_meta_data( 'ovabrw_pickoff_date_real', $dropoff_date, true );

		    	// Package ID
		    	if ( !$package_id ) {
		    		$line_item->add_meta_data( 'package_id', $package_id, true );
		    	}

		    	// Package label
		    	if ( !$package_label ) {
		    		$line_item->add_meta_data( 'period_label', $package_label, true );
		    	}

		    	// Package type
		    	if ( !$package_type ) {
		    		$line_item->add_meta_data( 'package_type', $package_type, true );
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

			// Pick-up location
			$pickup_location = isset( $args['ovabrw_pickup_location'][$meta_key] ) ? $args['ovabrw_pickup_location'][$meta_key] : '';
			if ( $pickup_location ) {
				$item_meta['ovabrw_pickup_loc'] = $pickup_location;
			}

			// Drop-off location
			$dropoff_location = isset( $args['ovabrw_dropoff_location'][$meta_key] ) ? $args['ovabrw_dropoff_location'][$meta_key] : '';
			if ( $dropoff_location ) {
				$item_meta['ovabrw_pickoff_loc'] = $dropoff_location;
			}

			// Pick-up date
			$pickup_date = isset( $args['ovabrw_pickup_date'][$meta_key] ) ? $args['ovabrw_pickup_date'][$meta_key] : '';

			// Package ID
	    	$package_id = isset( $args['ovabrw_package_id'][$meta_key] ) ? $args['ovabrw_package_id'][$meta_key] : '';

	    	// Get package data
	    	$package_data = $this->get_package_data( strtotime( $pickup_date ), $package_id );
	    	if ( !ovabrw_array_exists( $package_data ) ) return false;

	    	// Unfixed time
	    	$unfixed_time = $this->get_meta_value( 'unfixed_time' );

	    	// Date format
	    	$date_format = OVABRW()->options->get_datetime_format();
	    	if ( 'inday' !== ovabrw_get_meta_data( 'type', $package_data ) && 'yes' !== $unfixed_time ) {
	    		$date_format = OVABRW()->options->get_date_format();
	    	}

	    	// Pick-up date
	    	$pickup_date = gmdate( $date_format, ovabrw_get_meta_data( 'start', $package_data ) );
	    	if ( $pickup_date ) {
				$item_meta['ovabrw_pickup_date'] 			= $pickup_date;
				$item_meta['ovabrw_pickup_date_strtotime'] 	= strtotime( $pickup_date );
			}

	    	// Drop-off date
	    	$dropoff_date = gmdate( $date_format, ovabrw_get_meta_data( 'end', $package_data ) );
			if ( !$dropoff_date ) $dropoff_date = $pickup_date;
			if ( $dropoff_date ) {
				$item_meta['ovabrw_pickoff_date'] 			= $dropoff_date;
				$item_meta['ovabrw_pickoff_date_strtotime'] = strtotime( $dropoff_date );
			}

			// Pick-up real date
	    	$item_meta['ovabrw_pickup_date_real'] = $pickup_date;

	    	// Drop-off real dates
	    	$item_meta['ovabrw_pickoff_date_real'] = $dropoff_date;

	    	// Quantity real
	    	$item_meta['ovabrw_total_days'] = ovabrw_get_meta_data( 'label', $package_data );

	    	// Price real
	    	$item_meta['ovabrw_price_detail'] = ovabrw_get_meta_data( 'price', $package_data ) ? ovabrw_wc_price( wc_get_price_including_tax( $this->product, [ 'price' => $package_data['price'] ] ) ) : 0;

	    	// Package ID
	    	$item_meta['package_id'] = ovabrw_get_meta_data( 'id', $package_data );

	    	// Package type
	    	$item_meta['package_type'] = ovabrw_get_meta_data( 'type', $package_data );

	    	// Package label
	    	$item_meta['period_label'] = ovabrw_get_meta_data( 'label', $package_data );

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

	    	// Pick-up location
	    	$pickup_location = ovabrw_get_meta_data( 'pickup_location', $_REQUEST );
	    	if ( $this->product->show_location_field( 'pickup' ) ) {
	    		if ( !$pickup_location ) return false;
	    		$cart_item_data['pickup_location'] = $pickup_location;
	    	}

	    	// Drop-off location
	    	$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $_REQUEST );
	    	if ( $this->product->show_location_field( 'dropoff' ) ) {
	    		if ( !$dropoff_location ) return false;
	    		$cart_item_data['dropoff_location'] = $dropoff_location;
	    	}

	    	// Validation locations
	    	if ( ovabrw_get_meta_data( 'pickup_location', $cart_item_data ) && ovabrw_get_meta_data( 'dropoff_location', $cart_item_data ) ) {
	    		if ( !$this->location_validation( $cart_item_data['pickup_location'], $cart_item_data['dropoff_location'] ) ) {
	    			return false;
	    		}
	    	}

	    	// Pick-up date
	    	$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $_REQUEST ) );
	    	if ( !$pickup_date ) return false;

	    	// Package
	    	$package = (int)ovabrw_get_meta_data( 'package', $_REQUEST );
	    	if ( !$package ) return false;

	    	// Get package id
	    	$package_id = $this->product->get_package_id( $package );
	    	if ( !$package_id ) return false;
	    	$cart_item_data['package_id'] = $package_id;

	    	// Get package data
	    	$package_data = $this->get_package_data( $pickup_date, $package_id );
	    	if ( !ovabrw_array_exists( $package_data ) ) return false;

	    	// Package label
	    	$cart_item_data['package_label'] = ovabrw_get_meta_data( 'label', $package_data );

	    	// Package type
	    	$cart_item_data['package_type'] = ovabrw_get_meta_data( 'type', $package_data );

	    	// Package price
	    	$cart_item_data['package_price'] = ovabrw_get_meta_data( 'price', $package_data );

	    	// Unfixed time
	    	$unfixed_time = $this->get_meta_value( 'unfixed_time' );

	    	// Date format
	    	$date_format = OVABRW()->options->get_datetime_format();
	    	if ( 'inday' !== $cart_item_data['package_type'] && 'yes' !== $unfixed_time ) {
	    		$date_format = OVABRW()->options->get_date_format();
	    	}

	    	// Pick-up date
	    	$pickup_date = gmdate( $date_format, ovabrw_get_meta_data( 'start', $package_data ) );
	    	if ( !$pickup_date ) return false;
	    	$cart_item_data['pickup_date'] = $pickup_date;

	    	// Drop-off date
	    	$dropoff_date = gmdate( $date_format, ovabrw_get_meta_data( 'end', $package_data ) );
	    	if ( !$dropoff_date ) return false;
	    	$cart_item_data['dropoff_date'] = $dropoff_date;

	    	// Add quantity
	    	$cart_item_data['ovabrw_quantity'] = ovabrw_get_meta_data( 'quantity', $_REQUEST, 1 );

	    	return apply_filters( $this->prefix.'get_add_to_cart_data', $cart_item_data, $this );
	    }
	}
}