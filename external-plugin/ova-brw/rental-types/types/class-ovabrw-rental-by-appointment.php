<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Rental By Appointment
 */
if ( !class_exists( 'OVABRW_Rental_By_Appointment' ) ) {

	class OVABRW_Rental_By_Appointment extends OVABRW_Rental_Types {

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
			return 'appointment';
		}

		/**
		 * Get meta fields
		 */
		public function get_meta_fields() {
			return (array)apply_filters( $this->prefix.$this->get_type().'_get_fields', [
				'rental-type',
				'insurance',
				'time-slots',
				'deposit',
				'specifications',
				'features',
				'special-timeslots',
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
				'time-slots',
				'dropoff-date',
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
			$min = $max = '';

			// Get timeslot prices
			$timeslost_prices = $this->get_meta_value( 'time_slots_price' );

			if ( ovabrw_array_exists( $timeslost_prices ) ) {
			    foreach ( $timeslost_prices as $prices ) {
			    	// Min price
			    	$min_price = (float)min( $prices );
			    	if ( '' == $min ) $min = $min_price;
			    	if ( $min > $min_price ) $min = $min_price;

			    	$max_price = (float)max( $prices );
			    	if ( '' == $max ) $max = $max_price;
			    	if ( $max < $max_price ) $max = $max_price;
			    }
			}

			// New price
			$new_price = '';

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
	     * Get booked dates
	     */
	    public function get_booked_dates( $view = '' ) {
	    	// Get booking dates
	    	$booking_dates = $this->get_booking_dates( $view );

	    	return apply_filters( $this->prefix.'get_booked_dates', [
	    		'full_day' => ovabrw_get_meta_data( 'full_day', $booking_dates, [] ),
	    		'part_day' => ovabrw_get_meta_data( 'part_day', $booking_dates, [] )
	    	], $view, $this );
	    }

	    /**
	     * Get booking dates
	     */
	    public function get_booking_dates( $view = '' ) {
	    	// init
	    	$full_day = $part_day = [];

	    	// Order booked
	    	$order_booked = [];

	    	// Booked dates
	    	$booked_dates = [];

	    	// Get order booked ids
	    	$order_ids = OVABRW()->options->get_order_booked_ids( $this->get_id() );
	    	if ( ovabrw_array_exists( $order_ids ) ) {
	    		// Get product ids multi language
	    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

	    		// Date format
	    		$date_format = OVABRW()->options->get_date_format();
	    		if ( 'calendar' === $view ) $date_format = 'Y-m-d';

	    		// Loop order ids
	    		foreach ( $order_ids as $order_id ) {
	    			// Get order
	    			$order = wc_get_order( $order_id );
	    			if ( !$order ) continue;

	    			// Get order items
	    			$items = $order->get_items();
	    			if ( !ovabrw_array_exists( $items ) ) continue;

	    			// Loop order items
	    			foreach ( $items as $item_id => $item ) {
	    				// Get produdct id
	    				$product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';
	    				if ( !in_array( $product_id, $product_ids ) ) continue;

	    				// Get pick-up date
	    				$pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date_real' ) );
	    				if ( !$pickup_date || $pickup_date <= current_time( 'timestamp' ) ) continue;

	    				// Get drop-off date
	    				$dropoff_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date_real' ) );
	    				if ( !$dropoff_date ) continue;

	    				// Get booked quantity
	    				$booked_qty = (int)$item->get_meta( 'ovabrw_number_vehicle' );

                        // Add booked dates
	    				$booked_dates[$pickup_date] = $dropoff_date;

                        if ( array_key_exists( $pickup_date, $order_booked ) ) {
                            $order_booked[$pickup_date] += $booked_qty;
                        } else {
                            $order_booked[$pickup_date] = $booked_qty;
                        }
	    			} // END loop order items
	    		} // END loop
	    	} // END if

	    	// Get booked dates
	    	if ( ovabrw_array_exists( $order_booked ) ) {
	    		// Get timeslots start
	    		$timeslots_start = $this->get_meta_value( 'time_slots_start', [] );

	    		// Get timeslots quantities
	    		$timeslots_qtys = $this->get_meta_value( 'time_slots_quantity', [] );

	    		// Loop
	    		foreach ( $order_booked as $pickup_date => $booked_qty ) {
	    			// String day of week
                    $dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

                    // Get start times
                    $start_times = ovabrw_get_meta_data( $dayofweek, $timeslots_start );

                    // Get quantites
                    $qtys = ovabrw_get_meta_data( $dayofweek, $timeslots_qtys );

                    // Start times
                    if ( ovabrw_array_exists( $start_times ) ) {
                        $item_available = 0;

                        // Loop start times
                        foreach ( $start_times as $k => $start_time ) {
                            $start_date = strtotime( OVABRW()->options->get_string_date( $pickup_date, strtotime( $start_time ) ) );

                            if ( $start_date > current_time( 'timestamp' ) ) {
                                $item_available += (int)ovabrw_get_meta_data( $k, $qtys );
                            }
                        } // END loop start times

                        // Check quantity available
                        if ( $item_available <= $booked_qty ) {
                            array_push( $full_day, gmdate( $date_format, $pickup_date ) );
                        } else {
                        	// Get drop-off date
                        	$dropoff_date = ovabrw_get_meta_data( $pickup_date, $booked_dates );
                        	if ( !$pickup_date || !$dropoff_date ) continue;

                        	// Get booked events
		    				$booked_events = $this->get_calendar_events( $pickup_date, $dropoff_date, $date_format, 'booked' );
		    				if ( ovabrw_array_exists( $booked_events ) ) {
		    					// Loop part day
								foreach ( $booked_events['part_day'] as $item ) {
									$start 	= ovabrw_get_meta_data( 'start', $item );
									$end 	= ovabrw_get_meta_data( 'end', $item );

									// Get index
									$index = array_search( true, array_map( function( $item ) use ( $start, $end ) {
									    return $item['start'] === $start && $item['end'] === $end;
									}, $part_day ) );

									// Add part day
									if ( is_bool( $index ) ) {
										$item['quantity'] = $booked_qty;
										array_push( $part_day, $item );
									} else {
										$part_day[$index]['quantity'] += $booked_qty;
									}
								} // END loop
		    				} // END if
                        } // END if
                    } // END if
	    		} // END loop
	    	} // END if

	    	return apply_filters( $this->prefix.'get_booking_dates', [
	    		'full_day' => $full_day,
	    		'part_day' => $part_day
	    	], $view, $this );
	    }

	    /**
	     * Get disable weekdays
	     */
	    public function get_disable_weekdays() {
	    	// Disable weekdays
			$disable_weekdays = $this->get_meta_value( 'product_disable_week_day' );
			if ( !$disable_weekdays ) {
			    $disable_weekdays = ovabrw_get_setting( 'calendar_disable_week_day', '' );
			}

			if ( ovabrw_array_exists( $disable_weekdays ) ) {
				$key = array_search( '7', $disable_weekdays );
				if ( $key !== false ) $disable_weekdays[$key] = '7';
			} else {
				if ( $disable_weekdays && !is_array( $disable_weekdays ) ) {
					$disable_weekdays = explode( ',', $disable_weekdays );
					$disable_weekdays = array_map( 'trim', $disable_weekdays );
				}
			}
			if ( !ovabrw_array_exists( $disable_weekdays ) ) $disable_weekdays = [];

			// Get time slots
			$timeslots_start 	= $this->get_meta_value( 'time_slots_start', [] );
        	$timeslots_end 		= $this->get_meta_value( 'time_slots_end', [] );
        	$timeslots_qtys 	= $this->get_meta_value( 'time_slots_quantity', [] );

        	// Weekdays array
	        $weekdays = [
	        	'1' => 'monday',
	            '2' => 'tuesday',
	            '3' => 'wednesday',
	            '4' => 'thursday',
	            '5' => 'friday',
	            '6' => 'saturday',
	            '7' => 'sunday'
	        ];

	        // Today
	        $current_time = current_time( 'timestamp' );

	        // Today of week
	        $todayofweek = OVABRW()->options->get_string_dayofweek( $current_time );

	        // Loop weekdays
	        foreach ( $weekdays as $number_day => $string_day ) {
	        	if ( !in_array( $number_day, $disable_weekdays ) ) {
	        		$start_times 	= ovabrw_get_meta_data( $string_day, $timeslots_start, [] );
	                $quantities 	= ovabrw_get_meta_data( $string_day, $timeslots_qtys, [] );

	                if ( !ovabrw_array_exists( $start_times ) || !ovabrw_array_exists( $quantities ) ) {
	                    $disable_weekdays[] = (string)$number_day;
	                } else {
	                	$is_blocked = false;

	                	foreach ( $start_times as $k => $start_time ) {
	                        $quantity = (int)ovabrw_get_meta_data( $k, $quantities );

	                        if ( $quantity ) {
	                        	$is_blocked = true;

	                        	// Break out of the loop
	                        	break;
	                        }
	                    }

	                    // is blocked
	                    if ( !$is_blocked ) {
	                        $disable_weekdays[] = (string)$number_day;
	                    }
	                }
	        	}
	        } // End Loop

			return apply_filters( $this->prefix.'get_disable_weekdays', $disable_weekdays, $this );
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

	    	// Timeslot prices
    		$timeslot_prices = $this->get_meta_value( 'time_slots_price' );

    		foreach ( $days_of_week as $number_day => $str_day ) {
    			// Get prices
    			$prices = ovabrw_get_meta_data( $str_day, $timeslot_prices );
    			if ( !ovabrw_array_exists(  $prices) ) continue;

    			switch ( $filter_price ) {
                    case 'highest':
                        $price = max( $prices );
                        break;
                    case 'average':
                        $price = array_sum( $prices ) / count( $prices );
                        break;
                    case 'lowest':
                        $price = min( $prices );
                        break;
                    default:
                        $price = max( $prices );
                }

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
			$prices = $this->get_meta_value( 'special_price' );

			// From dates
			$from_dates = $this->get_meta_value( 'special_startdate' );

			// To dates
			$to_dates = $this->get_meta_value( 'special_enddate' );

	    	// Loop
	    	if ( ovabrw_array_exists( $prices ) ) {
				foreach ( $prices as $k => $price ) {
					$from 	= strtotime( ovabrw_get_meta_data( $k, $from_dates ) );
					$to 	= strtotime( ovabrw_get_meta_data( $k, $to_dates ) );

					if ( '' === $price || !$from || !$to ) continue;

					$special_prices[] = [
						'from' 	=> strtotime( gmdate( 'Y-m-d', $from ) ),
						'to' 	=> strtotime( gmdate( 'Y-m-d', $to ) ),
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
	     * Get stock quantity
	     */
	    public function get_stock_quantity( $pickup_date, $dropoff_date, $location ) {
	    	if ( !$pickup_date || !$dropoff_date ) return 0;

	    	// init
	    	$quantity = 0;

	    	// Time format
	    	$time_format = OVABRW()->options->get_time_format();

	    	// Pick-up time
	    	$pickup_time = gmdate( $time_format, $pickup_date );

	    	// Drop-off time
	    	$dropoff_time = gmdate( $time_format, $dropoff_date );

	    	// String day of week
	    	$dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

			// Use location
			$use_location = $this->get_meta_value( 'use_location' );

			// Get time slots data
			$timeslots_location = $this->get_meta_value( 'time_slots_location' );
			$timeslots_start 	= $this->get_meta_value( 'time_slots_start' );
			$timeslots_end 		= $this->get_meta_value( 'time_slots_end' );
			$timeslots_qtys 	= $this->get_meta_value( 'time_slots_quantity' );

			// Get time slots by day of week
			$locations 		= ovabrw_get_meta_data( $dayofweek, $timeslots_location );
			$start_times 	= ovabrw_get_meta_data( $dayofweek, $timeslots_start );
			$end_times 		= ovabrw_get_meta_data( $dayofweek, $timeslots_end );
			$qtys 			= ovabrw_get_meta_data( $dayofweek, $timeslots_qtys );

			if ( ovabrw_array_exists( $start_times ) ) {
				foreach ( $start_times as $k => $start_time ) {
					$item_loc = ovabrw_get_meta_data( $k, $locations );
					$end_time = ovabrw_get_meta_data( $k, $end_times, $start_time );

					// Check is time format
					if ( !strtotime( $start_time ) || !strtotime( $end_time ) ) continue;

					// Convert by time format
					$start_time = gmdate( $time_format, strtotime( $start_time ) );
					$end_time 	= gmdate( $time_format, strtotime( $end_time ) );

					if ( $pickup_time == $start_time && $dropoff_time == $end_time ) {
						if ( $use_location ) {
							if ( $location == $item_loc ) {
								$quantity = (int)ovabrw_get_meta_data( $k, $qtys );

								// Break out of the loop
								break;
							}
						} else {
							$quantity = (int)ovabrw_get_meta_data( $k, $qtys );

							// Break out of the loop
							break;
						}
					}
				}
			}

	    	return apply_filters( $this->prefix.'get_stock_quantity', $quantity, $pickup_date, $dropoff_date, $location, $this );
	    }

	    /**
	     * Get items available
	     */
	    public function get_items_available( $pickup_date, $dropoff_date, $pickup_location = '', $dropoff_location = '', $validation = 'cart' ) {
	    	// init
	    	$items_available = 0;

	    	// Check pick-up & drop-off date
	    	if ( !$pickup_date || !$dropoff_date ) {
	    		return apply_filters( $this->prefix.'get_items_available', $items_available, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $validation, $this );
	    	}

	    	// Get stock quantity
    		$stock_qty = $this->get_stock_quantity( $pickup_date, $dropoff_date, $pickup_location );
    		if ( !$stock_qty && 'search' === $validation && $pickup_date === $dropoff_date ) {
    			$stock_qty = 1;
    		}

    		// Items booked from Cart
    		$cart_booked = 0;

    		if ( 'cart' === $validation ) {
    			$cart_booked = $this->get_items_booked_from_cart( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location );
    		}

    		// Items booled from Order
    		$order_booked = $this->get_items_booked_from_order( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location );

    		// Items available
    		$items_available = $stock_qty - ( $cart_booked + $order_booked );
    		if ( $items_available < 0 ) $items_available = 0;

	    	return apply_filters( $this->prefix.'get_items_available', $items_available, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $validation, $this );
	    }

	    /**
	     * Get items booked from Cart
	     */
	    public function get_items_booked_from_cart( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location ) {
	    	if ( !$pickup_date || !$dropoff_date ) return 0;

	    	// Items booked
	    	$items_booked = 0;

	    	if ( ovabrw_array_exists( WC()->cart->get_cart() ) ) {
	    		// Get product ids multi language
	    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

	    		// Loop
	    		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
	    			// Get product ID
					$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );

					if ( in_array( $product_id, $product_ids ) ) {
						// Use location
						$use_location = ovabrw_get_post_meta( $product_id, 'use_location' );

						// Item location
						$item_location = ovabrw_get_meta_data( 'pickup_location', $cart_item );

						// Item pick-up date
						$item_pickup = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );
						if ( !$item_pickup || $item_pickup <= current_time( 'timestamp' ) ) continue;

						// Qty Cart
						$cart_qty = absint( ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item ) );
						if ( !$cart_qty ) continue;

						// Check date & location
						if ( $item_pickup == $pickup_date ) {
							if ( $use_location ) {
								if ( $pickup_location == $item_location ) {
									$items_booked += $cart_qty;
								}
							} else {
								$items_booked += $cart_qty;
							}
						}
					}
				} // END loop
	    	}

	    	return apply_filters( $this->prefix.'get_items_booked_from_cart', $items_booked, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $this );
	    }

	    /**
	     * Get items booked from Order
	     */
	    public function get_items_booked_from_order( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location ) {
	    	if ( !$pickup_date || !$dropoff_date ) return 0;

	    	// Items booked
	    	$items_booked = 0;

	    	// Get order booked ids
	    	$order_ids = OVABRW()->options->get_order_booked_ids_by_pickup( $this->get_id(), $pickup_date );

	    	if ( ovabrw_array_exists( $order_ids ) ) {
	    		// Get product ids multi language
	    		$product_ids = OVABRW()->options->get_product_ids_multi_lang( $this->get_id() );

	    		// Time between leases
	    		$time_between_leases = $this->get_time_between_leases();

	    		// Loop order ids
	    		foreach ( $order_ids as $order_id ) {
	    			// Get order
	    			$order = wc_get_order( $order_id );
	    			if ( !$order || !is_object( $order ) ) continue;

	    			// Get order items
	    			$items = $order->get_items();
	    			if ( !ovabrw_array_exists( $items ) ) continue;

	    			// Loop order items
	    			foreach ( $items as $item_id => $item ) {
	    				// Product ID
	    				$product_id = $item->get_product_id();

	    				if ( in_array( $product_id, $product_ids ) ) {
	    					// Use location
							$use_location = ovabrw_get_post_meta( $product_id, 'use_location' );

							// Item location
            				$item_location = $item->get_meta( 'ovabrw_location' );

	    					// Item pick-up date
            				$item_pickup = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );

            				if ( !$item_pickup || $item_pickup <= current_time( 'timestamp' ) ) continue;

            				// Booked qty
        					$booked_qty = absint( $item->get_meta( 'ovabrw_number_vehicle' ) );
        					if ( !$booked_qty ) continue;

        					// Check date & location
        					if ( $item_pickup == $pickup_date ) {
        						if ( $use_location ) {
        							if ( $pickup_location == $item_location ) {
        								$items_booked += $booked_qty;
        							}
        						} else {
        							$items_booked += $booked_qty;
        						}
        					}
	    				}
	    			} // END loop order items
	    		} // END loop order ids
	    	}

	    	return apply_filters( $this->prefix.'get_items_booked_from_order', $items_booked, $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, $this );
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

			return apply_filters( $hook_name, false, $pickup_date, $dropoff_date, $args, $this );
	    }

	    /**
	     * Get time slots use location
	     */
	    public function get_time_slots_use_location( $pickup_date ) {
	    	if ( !$pickup_date ) return false;

	    	// init
	    	$time_slots = [];

	    	// Get string day of week
            $dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

            // Get time slots lable
            $timeslots_label = $this->get_meta_value( 'time_slots_label' );

            // Get time slots location
            $timeslots_location = $this->get_meta_value( 'time_slots_location' );

            // Get time slots start time
            $timeslots_start = $this->get_meta_value( 'time_slots_start' );

            // Get time slots end time
            $timeslots_end = $this->get_meta_value( 'time_slots_end' );

            // Get time slots quantity
            $timeslots_qty = $this->get_meta_value( 'time_slots_quantity' );

            // Time slots by day of week
            $labels         = ovabrw_get_meta_data( $dayofweek, $timeslots_label );
            $locations      = ovabrw_get_meta_data( $dayofweek, $timeslots_location );
            $start_times    = ovabrw_get_meta_data( $dayofweek, $timeslots_start );
            $end_times      = ovabrw_get_meta_data( $dayofweek, $timeslots_end );
            $qtys     		= ovabrw_get_meta_data( $dayofweek, $timeslots_qty );

            if ( ovabrw_array_exists( $start_times ) ) {
                // Get time format
                $time_format = OVABRW()->options->get_time_format();

                foreach ( $start_times as $k => $start_time ) {
                    $label      = ovabrw_get_meta_data( $k, $labels );
                    $location   = ovabrw_get_meta_data( $k, $locations );
                    $start_time = strtotime( $start_time );
                    $end_time   = strtotime( ovabrw_get_meta_data( $k, $end_times ) );
                    $quantity   = (int)ovabrw_get_meta_data( $k, $qtys );
                    $disabled   = false;

                    // Check location
                    if ( !$location ) continue;

                    // Check start time
                    if ( !$start_time ) continue;

                    // Get start date
                    $start_date = strtotime( OVABRW()->options->get_string_date( $pickup_date, $start_time ) );
                    if ( !$start_date ) continue;

                    // Get end date
                    $end_date = '';

                    if ( $end_time ) {
                        // Get string end date
                        if ( $start_time > $end_time ) {
                            // $end_date = $checkin_date + 1 day
                            $end_date = strtotime( OVABRW()->options->get_string_date( $pickup_date + 86400, $end_time ) );
                        } else {
                            $end_date = strtotime( OVABRW()->options->get_string_date( $pickup_date, $end_time ) );
                        }

                        // Check end date > current time
                        if ( !$end_date ) continue;
                    } else {
                        $end_date = $start_date;
                    }

                    // Get Label
                    if ( !$label ) {
                        if ( $end_time ) {
                            $label = sprintf( esc_html__( '%s - %s', 'ova-brw' ), gmdate( $time_format, $start_time ), gmdate( $time_format, $end_time ) );
                        } else {
                            $label = gmdate( $time_format, $start_date );
                        }
                    }

                    // Check quantity
                    if ( $quantity < 1 ) $disabled = true;

                    // Check start date
                    if ( !$disabled && $start_date <= current_time( 'timestamp' ) || $end_date <= current_time( 'timestamp' ) ) {
                        $disabled = true;
                    }

                    // Validation
                    $validation_booking = $this->booking_validation( $start_date, $end_date );
                    if ( !$disabled && $validation_booking ) {
                        $disabled = true;
                    }

                    // Get items available
                    $items_available = $this->get_items_available( $start_date, $end_date, $location, '', 'cart' );
                    if ( !$disabled && !$items_available ) {
                        $disabled = true;
                    }

                    // Add time slots
                    $time_slots[$location][] = [
                        'label'         => $label,
                        'start_date'    => $start_date,
                        'end_date'      => $end_date,
                        'disabled'      => $disabled ? 1 : ''
                    ];
                }
            }

	    	return apply_filters( $this->prefix.'get_time_slots_use_location', $time_slots, $pickup_date, $this );
	    }

	    /**
	     * Get time slots
	     */
	    public function get_time_slots( $pickup_date ) {
	    	if ( !$pickup_date ) return false;

	    	// init
            $time_slots = [];

            // Get string day of week
            $dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

            // Get time slots lable
            $timeslots_label = $this->get_meta_value( 'time_slots_label' );

            // Get time slots start time
            $timeslots_start = $this->get_meta_value( 'time_slots_start' );

            // Get time slots end time
            $timeslots_end = $this->get_meta_value( 'time_slots_end' );

            // Get time slots quantity
            $timeslots_qty = $this->get_meta_value( 'time_slots_quantity' );

            // Time slots by day of week
            $labels         = ovabrw_get_meta_data( $dayofweek, $timeslots_label );
            $start_times    = ovabrw_get_meta_data( $dayofweek, $timeslots_start );
            $end_times      = ovabrw_get_meta_data( $dayofweek, $timeslots_end );
            $quantities     = ovabrw_get_meta_data( $dayofweek, $timeslots_qty );

            if ( ovabrw_array_exists( $start_times ) ) {
                // Get time format
                $time_format = OVABRW()->options->get_time_format();

                foreach ( $start_times as $k => $start_time ) {
                    $label      = ovabrw_get_meta_data( $k, $labels );
                    $start_time = strtotime( $start_time );
                    $end_time   = strtotime( ovabrw_get_meta_data( $k, $end_times ) );
                    $quantity   = (int)ovabrw_get_meta_data( $k, $quantities );
                    $disabled   = false;

                    // Check start time
                    if ( !$start_time ) continue;

                    // Get start date
                    $start_date = strtotime( OVABRW()->options->get_string_date( $pickup_date, $start_time ) );
                    if ( !$start_date ) continue;

                    // Get end date
                    $end_date = '';

                    if ( $end_time ) {
                        // Get string end date
                        if ( $start_time > $end_time ) {
                            // $end_date = $checkin_date + 1 day
                            $end_date = strtotime( OVABRW()->options->get_string_date( $pickup_date + 86400, $end_time ) );
                        } else {
                            $end_date = strtotime( OVABRW()->options->get_string_date( $pickup_date, $end_time ) );
                        }

                        // Check end date > current time
                        if ( !$end_date ) continue;
                    } else {
                        $end_date = $start_date;
                    }

                    // Get Label
                    if ( !$label ) {
                        if ( $end_time ) {
                            $label = sprintf( esc_html__( '%s - %s', 'ova-brw' ), gmdate( $time_format, $start_time ), gmdate( $time_format, $end_time ) );
                        } else {
                            $label = gmdate( $time_format, $start_date );
                        }
                    }

                    // Check quantity
                    if ( $quantity < 1 ) $disabled = true;

                    // Check start date
                    if ( !$disabled && $start_date <= current_time( 'timestamp' ) || $end_date <= current_time( 'timestamp' ) ) {
                        $disabled = true;
                    }

                    // Validation
                    $validation_booking = $this->booking_validation( $start_date, $end_date );
                    if ( !$disabled && $validation_booking ) {
                        $disabled = true;
                    }

                    // Get items available
                    $items_available = $this->get_items_available( $start_date, $end_date, '', '', 'cart' );
                    if ( !$disabled && !$items_available ) {
                        $disabled = true;
                    }

                    // Add time slots
                    $time_slots[] = [
                        'label'         => $label,
                        'start_date'    => $start_date,
                        'end_date'      => $end_date,
                        'disabled'      => $disabled ? 1 : ''
                    ];
                }
            }

            return apply_filters( $this->prefix.'get_time_slots', $time_slots, $pickup_date, $this );
	    }

	    /**
	     * Get HTML time slots
	     */
	    public function get_time_slots_html( $time_slots = [], $name = '' ) {
	    	// init
	    	$html = '';

	    	// Check has active
            $has_active = false;

            // Default start date, end date
            $default_start = $default_end = '';

            if ( ovabrw_array_exists( $time_slots ) ) {
                // Date format
                $date_format = OVABRW()->options->get_date_format();

                // Time format
                $time_format = OVABRW()->options->get_time_format();

                // Field name
                if ( !$name ) $name = OVABRW_PREFIX.'start_time';

                ob_start();

                foreach ( $time_slots as $time_slot ):
                    // Start time
                    $start_time = gmdate( $time_format, $time_slot['start_date'] );

                    // End date
                    $end_date = ovabrw_get_meta_data( 'end_date', $time_slot );
                    if ( $end_date ) {
                        $end_date = gmdate( $date_format .' '. $time_format, $end_date );
                    }

                    // Disabled
                    $disabled = ovabrw_get_meta_data( 'disabled', $time_slot );

                    // Time slot class
                    $class = '';
                    if ( $disabled ) {
                        $class = 'disabled';
                    } else {
                        if ( !$has_active ) {
                            $class      = 'active';
                            $has_active = true;

                            // Add default date
                            $default_start  = $time_slot['start_date'];
                            $default_end    = $time_slot['end_date'];
                        }
                    }
                ?>
                    <label class="time-slot <?php echo esc_attr( $class ); ?>">
                        <?php echo esc_html( $time_slot['label'] ); ?>
                        <?php ovabrw_text_input([
                            'type'  	=> 'radio',
                            'name'  	=> $name,
                            'value' 	=> $start_time,
                            'checked' 	=> 'active' == $class ? true : false,
                            'disabled' 	=> $disabled ? true : false,
                            'attrs' 	=> [
                                'data-end-date' => $end_date
                            ]
                        ]); ?>
                    </label>
                <?php endforeach;

                $html = ob_get_contents();
                ob_end_clean();
            }

            return apply_filters( $this->prefix.'get_time_slots_html', [
                'timeslots'     => $html,
                'start_date'    => $default_start,
                'end_date'      => $default_end
            ], $time_slots, $name, $this );
	    }

	    /**
	     * Get HTML time slots location
	     */
	    public function get_time_slots_location_html( $time_slots = [], $name = '' ) {
	    	// init html
            $html = '';

            // init default timeslots
            $default_timeslots = [];

            // Has active
            $has_active = false;

            if ( ovabrw_array_exists( $time_slots ) ) {
                // Field name
                if ( !$name ) $name = OVABRW_PREFIX.'location';

                ob_start(); ?>
                    <select name="<?php echo esc_attr( $name ); ?>" data-timeslots="<?php echo esc_attr( wp_json_encode( $time_slots ) ); ?>">
                        <?php foreach ( $time_slots as $location => $timeslots ):
                            // Selected
                            $selected = false;

                            // Disabled
                            $disabled = false;

                            // Has timeslots
                            $has_time = !empty( array_filter( $timeslots, function( $item ) {
                                return !$item['disabled'];
                            }));
                            if ( !$has_time ) {
                            	$disabled = true;
                            } else {
                            	if ( !$has_active ) {
                            		// Update has selected
	                                $selected = true;

	                                // Update has active
                                    $has_active = true;

                                    // Set default timeslots
                                    $default_timeslots = $timeslots;
	                            }
                            }
                        ?>
                            <option value="<?php echo esc_attr( $location ); ?>"<?php selected( $selected, true ); disabled( $disabled, true ); ?>>
                                <?php echo esc_html( $location ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php $html = ob_get_contents();
                ob_end_clean();
            }

            return apply_filters( $this->prefix.'get_time_slots_location_html', [
                'locations'         => $html,
                'default_timeslots' => $default_timeslots
            ], $time_slots, $name, $this );
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

	    	// Get location
	    	$location = ovabrw_get_meta_data( 'pickup_location', $args );

	    	// init
	    	$rental_price = 0;

	    	// Get discount prices
	    	$discount_prices = $this->get_discount_prices( $pickup_date, $dropoff_date );
	    	if ( $discount_prices ) {
	    		$rental_price = $discount_prices;
	    	} else {
	    		// Time format
				$time_format = OVABRW()->options->get_time_format();

				// Pick-up time
				$pickup_time = gmdate( $time_format, $pickup_date );

				// Drop-off time
				$dropoff_time = gmdate( $time_format, $dropoff_date );

				// String day of week
				$dayofweek = OVABRW()->options->get_string_dayofweek( $pickup_date );

				// Get time slots data
				$timeslots_location = $this->get_meta_value( 'time_slots_location' );
				$timeslots_start 	= $this->get_meta_value( 'time_slots_start' );
				$timeslots_end 		= $this->get_meta_value( 'time_slots_end' );
				$timeslots_price 	= $this->get_meta_value( 'time_slots_price' );

				// Get time slots by day of week
				$locations 		= ovabrw_get_meta_data( $dayofweek, $timeslots_location );
				$start_times 	= ovabrw_get_meta_data( $dayofweek, $timeslots_start );
				$end_times 		= ovabrw_get_meta_data( $dayofweek, $timeslots_end );
				$prices 		= ovabrw_get_meta_data( $dayofweek, $timeslots_price );

				if ( ovabrw_array_exists( $start_times ) ) {
					foreach ( $start_times as $k => $start_time ) {
						// Location
						$item_loc = ovabrw_get_meta_data( $k, $locations );
						if ( $location && $location != $item_loc ) continue;

						// End time
						$end_time = ovabrw_get_meta_data( $k, $end_times, $start_time );

						// Check is time format
						if ( !strtotime( $start_time ) || !strtotime( $end_time ) ) continue;

						// Convert by time format
						$start_time = gmdate( $time_format, strtotime( $start_time ) );
						$end_time 	= gmdate( $time_format, strtotime( $end_time ) );

						if ( $pickup_time == $start_time && $dropoff_time == $end_time ) {
							$rental_price = (int)ovabrw_get_meta_data( $k, $prices );

							// Break out of the loop
							break;
						}
					}
				}
	    	}

	    	return apply_filters( $this->prefix.'get_rental_calculations', $rental_price, $args, $this );
	    }

	    /**
	     * Get discount prices
	     */
	    public function get_discount_prices( $pickup_date, $dropoff_date ) {
	    	if ( !$pickup_date || !$dropoff_date ) return 0;

	    	// Discount prices
	    	$disc_prices = 0;

	    	// Special prices
	    	$special_prices = $this->get_meta_value( 'special_price' );

	    	// Special startdate
	    	$special_startdate = $this->get_meta_value( 'special_startdate' );

	    	// Special enddate
	    	$special_enddate = $this->get_meta_value( 'special_enddate' );

	    	if ( ovabrw_array_exists( $special_prices ) ) {
	    		foreach ( $special_prices as $i => $price ) {
	    			// Start date
	    			$start_date = strtotime( ovabrw_get_meta_data( $i, $special_startdate ) );
	    			if ( !$start_date ) continue;

	    			// End date
	    			$end_date = strtotime( ovabrw_get_meta_data( $i, $special_enddate ) );
	    			if ( !$end_date ) continue;

	    			if ( $pickup_date >= $start_date && $pickup_date <= $end_date ) {
	    				$disc_prices = (float)$price;

	    				// Break out of the loop
	    				break;
	    			}
	    		}
	    	}

	    	return apply_filters( $this->prefix.'get_discount_prices', $disc_prices, $pickup_date, $dropoff_date, $this );
	    }

	    /**
		 * Add rental cart item data
		 */
		public function add_rental_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
			// Rental type
	    	$cart_item_data['rental_type'] = $this->get_type();

			// Location
	    	$location = trim( sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_location', $_REQUEST ) ) );
	    	$cart_item_data['pickup_location'] = $location;

	    	// Pick-up date
	    	$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_date', $_REQUEST ) );

	    	// Start time
	    	$start_time = trim( sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_start_time', $_REQUEST ) ) );
	    	if ( $start_time ) {
	    		$pickup_date .= ' ' . $start_time;
	    	}

	    	// Add pick-up date
	    	$cart_item_data['pickup_date'] = $pickup_date;

	    	// Drop-off date
	    	$dropoff_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_dropoff_date', $_REQUEST ) );
	    	if ( !$dropoff_date ) $dropoff_date = $pickup_date;
	    	$cart_item_data['dropoff_date'] = $dropoff_date;

	    	// Pick-up real date
	    	$cart_item_data['pickup_real'] = $pickup_date;

	    	// Drop-off real dates
	    	$cart_item_data['dropoff_real'] = $dropoff_date;

	    	// Price real
	    	$price_real = $this->get_rental_calculations([
	    		'pickup_location' 	=> $location,
	    		'pickup_date' 		=> strtotime( $pickup_date ),
	    		'dropoff_date' 		=> strtotime( $dropoff_date )
	    	]);
	    	
	    	$cart_item_data['price_real'] = ovabrw_wc_price( wc_get_price_including_tax( $this->product, [ 'price' => $price_real ] ) );

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
					'key' 		=> esc_html__( 'Location', 'ova-brw' ),
					'value' 	=> wc_clean( $pickup_location ),
					'display' 	=> wc_clean( $pickup_location ),
					'hidden' 	=> apply_filters( OVABRW_PREFIX.'cart_show_location_field', false )
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
		 * Get request booking order details
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
    		$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_location', $data ) );
    		if ( $pickup_location ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Location: ', 'ova-brw' ) . '</td>';
    				$order_details .= '<td>' . esc_html( $pickup_location ) . '</td>';
    			$order_details .= '</tr>';
    		}

    		// Start time
    		$start_time = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_start_time', $data ) );

    		// Pick-up date
    		$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'ovabrw_pickup_date', $data ) );
    		if ( strtotime( $start_time ) ) $pickup_date .= ' ' . $start_time;

    		// Pick-up date
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

    		// Quantity
    		$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $data, 1 );
    		if ( $this->product->show_quantity( 'request' ) ) {
    			$order_details .= '<tr>';
    				$order_details .= '<td>' . esc_html__( 'Quantity: ', 'ova-brw' ) . ':</td>';
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
	                        $cckf_value[$name] = wp_kses_post( $opt_text );
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
    				$order_details .= '<td>'.esc_html__( 'Extra: ', 'ova-brw' ).'</td>';
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
					'pickup_date' 		=> $pickup_date,
					'dropoff_date' 		=> $dropoff_date,
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

			// Pick-up date
			$pickup_date = ovabrw_get_meta_data( 'pickup_date', $data );

			// Drop-off date
			$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $data );

			// Location prices
			$location_prices = ovabrw_get_meta_data( 'location_prices', $data );

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
        			$line_item->add_meta_data( 'ovabrw_location', $pickup_location, true );
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
		    			$line_item->add_meta_data( sprintf( _n( 'Resource%s', 'Resources%s', count( $opt_names ), 'ova-brw' ), '' ), implode( ', ', $resc_values ), true );
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

			// Location
			$location = isset( $args['ovabrw_location'][$meta_key] ) ? $args['ovabrw_location'][$meta_key] : '';
			if ( $location ) {
				$item_meta['ovabrw_location'] = $pickup_location;
			}

			// Pick-up date
			$pickup_date = isset( $args['ovabrw_pickup_date'][$meta_key] ) ? $args['ovabrw_pickup_date'][$meta_key] : '';

			// Start time
			$start_time = isset( $args['ovabrw_start_time'][$meta_key] ) ? $args['ovabrw_start_time'][$meta_key] : '';
			if ( strtotime( $start_time ) ) {
				$pickup_date .= ' ' . $start_time;
			}

			// Pick-up date
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

	    	// Pick-up date real
    		$item_meta['ovabrw_pickup_date_real'] = $pickup_date;

    		// Drop-off date real
    		$item_meta['ovabrw_pickoff_date_real'] = $pickup_date;

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
	}
}