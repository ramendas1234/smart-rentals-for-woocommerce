<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Cron class.
 */
if ( !class_exists( 'OVABRW_Cron', false ) ) {

	class OVABRW_Cron {

		/**
		 * Reminder of pick-up date
		 */
		public $hook_remind_pickup 			= 'ovabrw_cron_hook_remind_pickup_date';
		public $time_repeat_remind_pickup 	= 'time_repeat_remind_pickup_date';

		/**
		 * Reminder of drop-off date
		 */
		public $hook_remind_dropoff 		= 'ovabrw_cron_hook_remind_dropoff_date';
		public $time_repeat_remind_dropoff 	= 'time_repeat_remind_dropoff_date';

		/**
		 * Remaining amount
		 */
		public $hook_remaining_amount 			= 'ovabrw_cron_hook_remaining_amount';
		public $time_repeat_remaining_amount 	= 'time_repeat_remaining_amount';
		
		/**
		 * Constructor
		 */
		public function __construct() {
			add_filter( 'cron_schedules', [ $this, 'add_cron_schedules' ] );
			add_action( 'init', [ $this, 'check_schedules' ] );
			register_deactivation_hook( __FILE__, [ $this, 'deactivate_cron' ] ); 

			add_action( $this->hook_remind_pickup, [ $this, 'reminder_pickup_date_event_time' ] );
			add_action( $this->hook_remind_dropoff, [ $this, 'reminder_dropoff_date_event_time' ] );
			add_action( $this->hook_remaining_amount, [ $this, 'remaining_amount_event_time' ] );
		}

		/**
		 * Add cron schedules
		 */
		public function add_cron_schedules( $schedules ) {
			// Reminder pick-up date
			$time_repeat_remind_pickup = (int)get_option( 'remind_mail_send_per_seconds', 86400 );

		    $schedules[$this->time_repeat_remind_pickup] = [
		    	'interval' 	=> $time_repeat_remind_pickup,
		        'display' 	=> sprintf( esc_html__( 'Every %s seconds', 'ova-brw' ), $time_repeat_remind_pickup )
		    ];

		    // Reminder drop-off date
		    $time_repeat_remind_dropoff = (int)get_option( 'remind_dropoff_date_mail_send_per_seconds', 86400 );

		    $schedules[$this->time_repeat_remind_dropoff] = [
		    	'interval' 	=> $time_repeat_remind_dropoff,
		        'display' 	=> sprintf( esc_html__( 'Every %s seconds', 'ova-brw' ), $time_repeat_remind_dropoff )
		    ];

		    // Remaining amount
		    $time_repeat_remaining_amount = (int)get_option( 'remaining_invoice_per_seconds', 86400 );

		    $schedules[$this->time_repeat_remaining_amount] = [
		    	'interval' 	=> $time_repeat_remaining_amount,
		        'display' 	=> sprintf( esc_html__( 'Every %s seconds', 'ova-brw' ), $time_repeat_remaining_amount )
		    ];

		    return $schedules;
		}

		/**
		 * Check schedules
		 */
		public function check_schedules() {
			// Reminder pick-up date
			if ( !wp_next_scheduled( $this->hook_remind_pickup ) ) {
			    wp_schedule_event( time(), $this->time_repeat_remind_pickup, $this->hook_remind_pickup );
			}

			// Reminder drop-off date
			if ( !wp_next_scheduled( $this->hook_remind_dropoff ) ) {
			    wp_schedule_event( time(), $this->time_repeat_remind_dropoff, $this->hook_remind_dropoff );
			}

			// Remaining amount
			if ( !wp_next_scheduled( $this->hook_remaining_amount ) ) {
			    wp_schedule_event( time(), $this->time_repeat_remaining_amount, $this->hook_remaining_amount );
			}
		}

		public function deactivate_cron() {
			// Reminder pick-up date
		    $time_next_reminder_pickup = wp_next_scheduled( $this->hook_remind_pickup );
		    wp_unschedule_event( $time_next_reminder_pickup, $this->hook_remind_pickup );

		    // Reminder drop-off date
		    $time_next_reminder_dropoff = wp_next_scheduled( $this->hook_remind_dropoff );
		    wp_unschedule_event( $time_next_reminder_dropoff, $this->hook_remind_dropoff );

		    // Remaining amount
		    $time_next_remaining = wp_next_scheduled( $this->hook_remaining_amount );
		    wp_unschedule_event( $time_next_remaining, $this->hook_remaining_amount );
		}

		/**
		 * Reminder of Pick-up date
		 */
		public function reminder_pickup_date_event_time() {
			if ( 'yes' !== get_option( 'remind_mail_enable', 'no' ) ) return;

			// Get future order ids
			$order_ids = OVABRW()->options->get_future_order_ids();

			if ( ovabrw_array_exists( $order_ids ) ) {
				$send_x_day 	= (int)get_option( 'remind_mail_before_xday', 1 );
				$before_time 	= current_time( 'timestamp' ) + $send_x_day*86400;

				foreach ( $order_ids as $key => $order_id ) {
					// Get order
					$order = wc_get_order( $order_id );
					if ( !$order ) continue;

					// Get billing mail
					$customer_mail = $order->get_billing_email();

					// Get order items
	    	    	$items = $order->get_items();
	    	    	if ( !ovabrw_array_exists( $items ) ) continue;

					foreach ( $items as $item_id => $item ) {
						$product_name 	= $item->get_name();
						$product_id 	= $item->get_product_id();
						$pickup_date 	= $item->get_meta( ovabrw_meta_key( 'pickup_date' ) );
						$dropoff_date 	= $item->get_meta( ovabrw_meta_key( 'pickoff_date' ) );

						// Check dates
						if ( !strtotime( $pickup_date ) || !strtotime( $dropoff_date ) ) continue;

						if ( apply_filters( OVABRW_PREFIX.'reminder_pickup_date_other_condition', true, $item ) && strtotime( $pickup_date ) < $before_time ) {
                            OVABRW_Mail::instance()->send_reminder_pickup_date_mail([
                            	'order' 		=> $order,
                            	'customer_mail' => $customer_mail,
                            	'product_id' 	=> $product_id,
                            	'product_name' 	=> $product_name,
                            	'pickup_date' 	=> $pickup_date,
                            	'dropoff_date' 	=> $dropoff_date
                            ]);
                        }
					}
				}
			}
		}

		/**
		 * Reminder of Drop-off date
		 */
		public function reminder_dropoff_date_event_time() {
			if ( 'yes' !== ovabrw_get_option( 'remind_dropoff_date_mail', 'no' ) ) return;

			// Get present order ids
			$order_ids = OVABRW()->options->get_present_order_ids();

			if ( ovabrw_array_exists( $order_ids ) ) {
				$send_x_day 	= (int)get_option( 'remind_dropoff_date_mail_before_xday', 1 );
				$before_time 	= current_time( 'timestamp' ) + $send_x_day*86400;
				
				
				foreach ( $order_ids as $key => $order_id ) {
					// Get order
					$order = wc_get_order( $order_id );
					if ( !$order ) continue;

					// Get billing mail
					$customer_mail = $order->get_billing_email();

					// Get order items
	    	    	$items = $order->get_items();
	    	    	if ( !ovabrw_array_exists( $items ) ) continue;

					foreach ( $items as $item_id => $item ) {
						$product_name 	= $item->get_name();
						$product_id 	= $item->get_product_id();
						$pickup_date 	= $item->get_meta( ovabrw_meta_key( 'pickup_date' ) );
						$dropoff_date 	= $item->get_meta( ovabrw_meta_key( 'pickoff_date' ) );

						// Check dates
						if ( !strtotime( $pickup_date ) || !strtotime( $dropoff_date ) ) continue;

						if ( apply_filters( OVABRW_PREFIX.'reminder_dropoff_date_other_condition', true, $item ) && strtotime( $dropoff_date ) < $before_time ) {
                            OVABRW_Mail::instance()->send_reminder_dropoff_date_mail([
                            	'order' 		=> $order,
                            	'customer_mail' => $customer_mail,
                            	'product_id' 	=> $product_id,
                            	'product_name' 	=> $product_name,
                            	'pickup_date' 	=> $pickup_date,
                            	'dropoff_date' 	=> $dropoff_date
                            ]);
                        }
					}
				}
			}
		}

		/**
		 * Remaining amount
		 */
		public function remaining_amount_event_time() {
			if ( 'yes' !== get_option( 'remaining_invoice_enable', 'yes' ) ) return;

			// Get order ids no remaining
			$order_ids = OVABRW()->options->get_order_ids_no_remaining();
			
			if ( ovabrw_array_exists( $order_ids ) ) {
				$send_x_day 		= (int)get_option( 'remaining_invoice_before_xday', 1 );
				$send_email 		= get_option( 'send_email_remaining_invoice_enable', 'yes' );
				$before_time 		= current_time('timestamp') + $send_x_day*86400;

				foreach ( $order_ids as $order_id ) {
					// Get order
					$order = wc_get_order( $order_id );
					if ( !$order ) continue;

					// Get order items
	    	    	$items = $order->get_items();
	    	    	if ( !ovabrw_array_exists( $items ) ) continue;

					foreach ( $items as $item_id => $item ) {
						// Pick-up date
						$pickup_date = strtotime( $item->get_meta( ovabrw_meta_key( 'pickup_date' ) ) );

						// Drop-off date
						$dropoff_date = strtotime( $item->get_meta( ovabrw_meta_key( 'pickoff_date' ) ) );

						// Check dates
						if ( !$pickup_date || !$dropoff_date ) continue;
						if ( $pickup_date > $before_time || $pickup_date < current_time('timestamp') ) continue;

						// Get item remaining amount
            			$remaining_amount = floatval( $item->get_meta( ovabrw_meta_key( 'remaining_amount' ) ) );
            			if ( !$remaining_amount ) continue;

            			// Get item remaining invoice id
            			$remaining_invoice = absint( $item->get_meta( ovabrw_meta_key( 'remaining_balance_order_id' ) ) );
            			if ( $remaining_invoice ) continue;

            			// Get item remaining tax amount
			            $remaining_tax = floatval( $item->get_meta( ovabrw_meta_key( 'remaining_tax' ) ) );

			            // Get remaining insurance amount
			            $remaining_insurance = floatval( $item->get_meta( ovabrw_meta_key( 'remaining_insurance' ) ) );

			            // Get remaining insurance tax amount
			            $remaining_insurance_tax = floatval( $item->get_meta( ovabrw_meta_key( 'remaining_insurance_tax' ) ) );

            			// Taxable
		                if ( wc_tax_enabled() ) {
		                	// Prices include tax
                    		$prices_incl_tax = $order->get_meta( '_ova_prices_include_tax' );

		                    if ( $prices_incl_tax ) {
		                        // Calculate tax
		                        $calculate_tax_for  = $order->get_taxable_location();
		                        $tax_rates          = WC_Tax::find_rates( $calculate_tax_for );
		                        $taxes              = WC_Tax::calc_inclusive_tax( $remaining_amount, $tax_rates );
		                        $remaining_amount 	-= WC_Tax::get_tax_total( $taxes );
		                    }
		                }

		                // Item data
			            $item_data = [
			            	'product'   => $item->get_product(),
			                'quantity' 	=> $item->get_quantity(),
			                'subtotal'  => $remaining_amount,
			                'total'     => $remaining_amount
			            ];

			            if ( $remaining_tax ) {
		                    $item_data['remaining_tax'] = $remaining_tax;
		                }
		                if ( $remaining_insurance ) {
		                    $item_data['insurance_amount'] = $remaining_insurance;
		                }
		                if ( $remaining_insurance_tax ) {
		                    $item_data['insurance_tax'] = $remaining_insurance_tax;
		                }

		                // Create order remaining
			            $new_order_id = OVABRW()->booking->create_order_remaining( $order_id, $item_data );
			            if ( !$new_order_id ) continue;

			            // Get new order
			            $new_order = wc_get_order( $new_order_id );
		                $new_order->add_meta_data( '_ova_original_id', $order_id );
		                $new_order->save();

			            // Add remaining balance order id
		                $item->add_meta_data( ovabrw_meta_key( 'remaining_balance_order_id' ), $new_order_id, true );
		                $item->save();

		                // Order update remaining invoice IDs
		                $remaining_invoice_ids = $order->get_meta( '_ova_remaining_invoice_ids' );
		                if ( ! $remaining_invoice_ids ) $remaining_invoice_ids = [];

		                // Check order remaining invoice exists
		                foreach ( $remaining_invoice_ids as $k => $remaining_invoice_id ) {
		                    if ( !wc_get_order( $remaining_invoice_id ) ) {
		                        unset( $remaining_invoice_ids[$k] );
		                    }
		                }

		                // Update remaining invoice ids
		                if ( !in_array( $new_order_id, $remaining_invoice_ids ) ) {
		                    array_push( $remaining_invoice_ids, $new_order_id );
		                }

		                $order->update_meta_data( '_ova_remaining_invoice_ids', $remaining_invoice_ids );
		                $order->save();

			            if ( 'yes' === $send_email ) {
			                // Email invoice
			                $emails = WC_Emails::instance();
			                $emails->customer_invoice( wc_get_order( $new_order_id ) );
			            }
				    }
				}
			}
		}
	}

	new OVABRW_Cron();
}