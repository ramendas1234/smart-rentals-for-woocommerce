<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Ajax class.
 */
if ( !class_exists( 'OVABRW_Admin_Ajax', false ) ) {

	class OVABRW_Admin_Ajax {

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
			// Define All Ajax function
			$arr_ajax = [
				'update_order_status',
				'get_custom_tax_in_cat',
				'update_cckf',
				'update_custom_taxonomies',
				'change_rental_type',
				'create_booking_load_product_fields',
				'create_booking_change_country',
				'create_booking_calculate_total',
				'add_specification',
				'edit_specification',
				'delete_specification',
				'sort_specifications',
				'enable_specification',
				'disable_specification',
				'change_type_specification',
				'update_insurance',
				'booking_calendar_get_events',
				'booking_calendar_get_available_items'
			];

			foreach ( $arr_ajax as $name ) {
				add_action( 'wp_ajax_'.OVABRW_PREFIX.$name, [ $this, OVABRW_PREFIX.$name ] );
				add_action( 'wp_ajax_nopriv_'.OVABRW_PREFIX.$name, [ $this, OVABRW_PREFIX.$name ] );
			}
		}

		/**
		 * Update order status
		 */
		public function ovabrw_update_order_status() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$order_id 		= sanitize_text_field( ovabrw_get_meta_data( 'order_id', $_POST ) );
			$order_status 	= sanitize_text_field( ovabrw_get_meta_data( 'new_order_status', $_POST ) );

			if ( $order_id && $order_status ) {
				$order = wc_get_order( $order_id );

				if ( !current_user_can( apply_filters( OVABRW_PREFIX.'update_order_status' ,'publish_posts' ) ) ) {
					echo 'error_permission';	
				} elseif ( $order->update_status( $order_status ) ) {
					echo 'true';
				} else {
					echo 'false';
				}
			} else {
				echo 'false';
			}
			
			wp_die();
		}

		/**
		 * Get Custom Taxonomy choosed in Category
		 */
		public function ovabrw_get_custom_tax_in_cat() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$term_ids 	= ovabrw_get_meta_data( 'term_ids', $_POST );
			$taxonomies = [];
			
			if ( ovabrw_array_exists( $term_ids ) ) {
				foreach ( $term_ids as $term_id ) {
					$custom_tax = get_term_meta( $term_id, 'ovabrw_custom_tax', true );
					
					if ( $custom_tax ) {
						foreach ( $custom_tax as $slug ) {
							if ( $slug && !in_array( $slug, $taxonomies ) ) {
								array_push( $taxonomies, $slug );
							}
						}
					}
				}
			}
			
			echo implode(",", $taxonomies ); 
			wp_die();
		}

		/**
		 * Update custom checkout fields when sortable
		 */
		public function ovabrw_update_cckf() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			if ( ovabrw_array_exists( $fields ) ) {
				$new_fields = [];
				$cckf 		= ovabrw_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

				foreach ( $fields as $field ) {
                    if ( !empty( $field ) && array_key_exists( $field, $cckf ) ) {
                        $new_fields[$field] = $cckf[$field];
                    }
                }

                if ( ovabrw_array_exists( $new_fields ) ) {
                    $cckf = $new_fields;
                }

                update_option( ovabrw_meta_key( 'booking_form' ), $cckf );
			}

			wp_die();
		}

		/**
		 * Update custom taxonomies when sortable
		 */
		public function ovabrw_update_custom_taxonomies() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Get fields
			$fields = ovabrw_get_meta_data( 'fields', $_POST );

			if ( ovabrw_array_exists( $fields ) ) {
				$new_fields = [];
				$taxonomies = ovabrw_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

				foreach ( $fields as $field ) {
                    if ( !empty( $field ) && array_key_exists( $field, $taxonomies ) ) {
                        $new_fields[$field] = $taxonomies[$field];
                    }
                }

                if ( ovabrw_array_exists( $new_fields ) ) {
                    $taxonomies = $new_fields;
                }

                update_option( ovabrw_meta_key( 'custom_taxonomy' ), $taxonomies );
			}

			wp_die();
		}

		/**
		 * Change Rental Type
		 */
		public function ovabrw_change_rental_type() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $_POST );
			if ( !$product_id ) wp_die();

			// Rental type
			$rental_type = ovabrw_get_meta_data( 'rental_type', $_POST );

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id, $rental_type );

			if ( $rental_product ) {
				ob_start();
				include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/views/html-rental-data-general.php' );
				echo ob_get_clean();
			}

			wp_die();
		}

		/**
		 * Create new booking: Load product fields
		 */
		public function ovabrw_create_booking_load_product_fields() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Product ID
			$product_id = trim( sanitize_text_field( ovabrw_get_meta_data( 'product_id', $_POST ) ) );
			if ( !$product_id ) wp_die();

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) wp_die();

			// Currency
			$currency = sanitize_text_field( ovabrw_get_meta_data( 'currency', $_POST ) );

			// Item key
			$key = wp_create_nonce( $product_id.current_time( 'timestamp' ) );

			echo wp_json_encode([
				'html' 	=> str_replace( 'ovabrw-item-key', $key, $rental_product->create_booking_view_meta_boxes([ 'currency' => $currency ]) ),
				'key' 	=> $key
			]);
			wp_die();

			wp_die();
		}

		/**
		 * Create booking change country
		 */
		public function ovabrw_create_booking_change_country() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Get country
			$country = ovabrw_get_meta_data( 'country', $_POST );
			if ( !$country ) wp_die();			

			$key = ovabrw_get_meta_data( 'key', $_POST, 'billing' );

			// Get country locale
			$address_field = WC()->countries->get_address_fields( $country, $key );

			// Get states
			$states = WC()->countries->get_states( $country );

			$label_state 	= esc_html__( 'State', 'ova-brw' );
			$required_state = false;

			// Postcode
			$label_postcode 	= esc_html__( 'ZIP Code', 'ova-brw' );
			$required_postcode 	= false;

			// Default billing state
			if (  'billing' === $key ) {
				// Default billing state
				$default_state = apply_filters( OVABRW_PREFIX.'create_booking_default_country', 'CA' );

				// Label state
				if ( isset( $address_field['billingstate']['label'] ) && $address_field['billingstate']['label'] ) {
					$label_state = $address_field['billingstate']['label'];
				}

				// Required state
				if ( isset( $address_field['billingstate']['required'] ) && $address_field['billingstate']['required'] ) {
					$required_state = true;
				}

				// Label postcode
				if ( isset( $address_field['billingpostcode']['label'] ) && $address_field['billingpostcode']['label'] ) {
					$label_postcode = $address_field['billingpostcode']['label'];
				}

				// Required postcode
				if ( isset( $address_field['billingpostcode']['required'] ) && $address_field['billingpostcode']['required'] ) {
					$required_postcode = true;
				}
			} elseif ( 'shipping' === $key ) {
				// Default shipping state
				$default_state = apply_filters( OVABRW_PREFIX.'create_booking_default_country', 'CA' );

				// Label state
				if ( isset( $address_field['shippingstate']['label'] ) && $address_field['shippingstate']['label'] ) {
					$label_state = $address_field['shippingstate']['label'];
				}

				// Required state
				if ( isset( $address_field['shippingstate']['required'] ) && $address_field['shippingstate']['required'] ) {
					$required_state = true;
				}

				// Label postcode
				if ( isset( $address_field['shippingpostcode']['label'] ) && $address_field['shippingpostcode']['label'] ) {
					$label_postcode = $address_field['shippingpostcode']['label'];
				}

				// Required postcode
				if ( isset( $address_field['shippingpostcode']['required'] ) && $address_field['shippingpostcode']['required'] ) {
					$required_postcode = true;
				}
			} else {
				wp_die();
			}

			// Label state
			if ( !$required_state ) {
				$label_state = sprintf( esc_html__( '%s (option)', 'ova-brw' ), $label_state );
			}

			// Label postcode
			if ( !$required_postcode ) {
				$label_postcode = sprintf( esc_html__( '%s (option)', 'ova-brw' ), $label_postcode );
			}

			ob_start();
			?>
				<div class="ovabrw-field">
					<label for="<?php echo esc_attr( $key.'-state' ); ?>" class="<?php echo $required_state ? 'ovabrw-required' : ''; ?>">
						<?php echo esc_html( $label_state ); ?>
					</label>
					<?php if ( ovabrw_array_exists( $states ) ): ?>
						<select name="<?php echo esc_attr( $key.'_state' ); ?>" id="<?php echo esc_attr( $key.'-state' ); ?>" class="<?php echo $required_state ? 'ovabrw-select2 ovabrw-input-required' : 'ovabrw-select2'; ?>" data-placeholder="<?php esc_attr_e( 'Select an option...', 'ova-brw' ); ?>" required>
							<option value="">
								<?php esc_html_e( 'Select an option...', 'ova-brw' ); ?>
							</option>
							<?php foreach ( $states as $skey => $svalue ): ?>
								<option value="<?php echo esc_attr( $skey ); ?>"<?php ovabrw_selected( $skey, $default_state ); ?>>
									<?php echo esc_html( $svalue ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					<?php else: ?>
						<?php ovabrw_wp_text_input([
							'type' 	=> 'text',
							'id' 	=> esc_attr( $key.'-state' ),
							'class' => $required_state ? 'ovabrw-input-required' : '',
							'name' 	=> esc_attr( $key.'_state' ),
							'attrs' => [ 'autocomplete' => 'off' ]
						]); ?>
					<?php endif; ?>
				</div>
				<div class="ovabrw-field">
					<label for="<?php echo esc_attr( $key.'-postcode' ); ?>" class="<?php echo $required_postcode ? 'ovabrw-required' : ''; ?>">
						<?php echo esc_html( $label_postcode ); ?>
					</label>
					<?php ovabrw_wp_text_input([
						'type' 	=> 'text',
						'id' 	=> esc_attr( $key.'-postcode' ),
						'class' => $required_postcode ? 'ovabrw-input-required' : '',
						'name' 	=> esc_attr( $key.'_postcode' ),
						'attrs' => [ 'autocomplete' => 'off' ]
					]); ?>
				</div>
			<?php

			echo wp_json_encode([ 'html' => ob_get_clean() ]);
			wp_die();
		}

		/**
		 * Create new booking: Calculate total
		 */
		public function ovabrw_create_booking_calculate_total() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			if ( !ovabrw_array_exists( $_POST ) ) wp_die();

			// Product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $_POST );
			if ( !$product_id ) wp_die();

			// Currency
			$currency = sanitize_text_field( ovabrw_get_meta_data( 'currency', $_POST ) );

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
			$quantity = (int)ovabrw_get_meta_data( 'quantity', $_POST, 1 );

			// Vehicle ID
			$vehicle_id = sanitize_text_field( ovabrw_get_meta_data( 'vehicle_id', $_POST ) );

			// Extra time
			$extra_time = sanitize_text_field( ovabrw_get_meta_data( 'extra_time', $_POST ) );

			// Duration map
			$duration_map = sanitize_text_field( ovabrw_get_meta_data( 'duration_map', $_POST ) );

			// Duration
			$duration = sanitize_text_field( ovabrw_get_meta_data( 'duration', $_POST ) );

			// Distance
			$distance = sanitize_text_field( ovabrw_get_meta_data( 'distance', $_POST ) );

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
			$items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'check' );

			// Vehicles available
			if ( is_array( $items_available ) ) {
				// Check vehicle id
				if ( $vehicle_id && !in_array( $vehicle_id, $items_available ) ) {
					echo json_encode([
						'error' => sprintf( esc_html__( 'Vehicle ID: %s is not available!', 'ova-brw' ), $vehicle_id )
					]); wp_die();
				}

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
		    $insurance = (float)$rental_product->product->get_meta_value( 'amount_insurance' ) * $quantity;

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
        		// Line total
                $line_total = ovabrw_convert_price( $line_total, [ 'currency' => $currency ] );

                // Insurance amount
                $insurance = ovabrw_convert_price( $insurance, [ 'currency' => $currency ] );
            }

            // Insurance
            $line_total += $insurance;

            if ( $line_total <= 0 && apply_filters( OVABRW_PREFIX.'required_total', false ) ) {
				wp_die();
			} else {
				echo json_encode([
					'items_available' 	=> $items_available,
					'insurance' 		=> round( $insurance, wc_get_price_decimals() ),
					'line_total' 		=> round( $line_total, wc_get_price_decimals() )
				]);
			}

			wp_die();
		}

		/**
		 * Add new specification
		 */
		public function ovabrw_add_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			ob_start();

			// include html add new
			OVABRW_Admin_Specifications::instance()->popup_form_fields();
			$html = ob_get_contents();

			ob_clean();

			echo $html;

			wp_die();
		}

		/**
		 * Edit specification
		 */
		public function ovabrw_edit_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$name = ovabrw_get_meta_data( 'name', $_POST );
			$type = ovabrw_get_meta_data( 'type', $_POST );

			if ( !$name || !$type ) wp_die();

			ob_start();

			// include html add new
			OVABRW_Admin_Specifications::instance()->popup_form_fields( 'edit', $type, $name );
			$html = ob_get_contents();

			ob_clean();

			echo $html;

			wp_die();
		}

		/**
		 * Delete specification
		 */
		public function ovabrw_delete_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$name = ovabrw_get_meta_data( 'name', $_POST );

			if ( !$name ) wp_die();

			$post['fields'] = [ $name ];

			OVABRW_Admin_Specifications::instance()->delete( $post );

			echo 1;
			wp_die();
		}

		/**
		 * Sort specifications
		 */
		public function ovabrw_sort_specifications() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			OVABRW_Admin_Specifications::instance()->sort( $_POST ); wp_die();
		}

		/**
		 * Enable specification
		 */
		public function ovabrw_enable_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$name = ovabrw_get_meta_data( 'name', $_POST );

			if ( !$name ) wp_die();

			OVABRW_Admin_Specifications::instance()->enable([
				'fields' => [ $name ]
			]);

			echo 1;
			wp_die();
		}

		/**
		 * Disable specification
		 */
		public function ovabrw_disable_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$name = ovabrw_get_meta_data( 'name', $_POST );

			if ( !$name ) wp_die();

			OVABRW_Admin_Specifications::instance()->disable([
				'fields' => [ $name ]
			]);

			echo 1;
			wp_die();
		}

		/**
		 * Change type specification
		 */
		public function ovabrw_change_type_specification() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Get type
			$type = ovabrw_get_meta_data( 'type', $_POST );

			if ( !$type ) wp_die();

			ob_start();

			// include html add new
			OVABRW_Admin_Specifications::instance()->popup_form_fields( 'new', $type );
			$html = ob_get_contents();

			ob_clean();

			echo $html;

			wp_die();
		}

		/**
		 * Update insurance amount
		 */
		public function ovabrw_update_insurance() {
			// Check security
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

			$order_id 	= (int)ovabrw_get_meta_data( 'order_id', $_POST );
			$item_id 	= (int)ovabrw_get_meta_data( 'item_id', $_POST );
			$amount 	= floatval( ovabrw_get_meta_data( 'amount', $_POST ) );
			$tax 		= floatval( ovabrw_get_meta_data( 'tax', $_POST ) );

			if ( !$order_id || !$item_id || $amount < 0 || $tax < 0 ) wp_die();

			$order = wc_get_order( $order_id );
            if ( !$order ) wp_die();

			$item = WC_Order_Factory::get_order_item( absint( $item_id ) );
            if ( !$item ) wp_die();

            // Insurance key
           	$insurance_key = $order->get_meta( '_ova_insurance_key' );

            // Total insurance
            $order_insurance 		= floatval( $order->get_meta( '_ova_insurance_amount' ) );
            $order_insurance_tax 	= floatval( $order->get_meta( '_ova_insurance_tax' ) );

            // Item insurance
            $item_insurance = floatval( $item->get_meta( 'ovabrw_insurance_amount' ) );

            // Item insurance tax
            $item_insurance_tax = floatval( $item->get_meta( 'ovabrw_insurance_tax' ) );

            // Original order and item
            $original_order 	= $original_item = false;
            $original_item_id 	= $order->get_meta( '_ova_original_item_id' );

            if ( absint( $original_item_id ) ) {
            	$original_item = WC_Order_Factory::get_order_item( absint( $original_item_id ) );

            	if ( $original_item ) {
            		$original_order = $original_item->get_order();
            	}
            }

            // Get fees
            $fees = $order->get_fees();
            
            // Update order insurance amount
            if ( ! empty( $fees ) && is_array( $fees ) ) {
            	foreach ( $fees as $item_fee_id => $item_fee ) {
            		$fee_key = sanitize_title( $item_fee->get_name() );

            		if ( $fee_key === $insurance_key ) {
            			$order_insurance -= $item_insurance;
            			$order_insurance += $amount;

            			$order_insurance_tax -= $item_insurance_tax;
            			$order_insurance_tax += $tax;

            			if ( $order_insurance < 0 ) $order_insurance = 0;
            			if ( $order_insurance_tax < 0 ) $order_insurance_tax = 0;

            			// Update item fee
            			if ( wc_tax_enabled() ) {
                            $order_taxes = $order->get_taxes();
                            $tax_item_id = 0;

                            foreach ( $order_taxes as $tax_item ) {
                                $tax_item_id = $tax_item->get_rate_id();

                                if ( $tax_item_id ) break;
                            }

                            $item_fee->set_props(
								[
									'total'     => $order_insurance,
									'subtotal'  => $order_insurance,
									'total_tax' => $order_insurance_tax,
									'taxes'     => [
										'total' => [ $tax_item_id => $order_insurance_tax ]
									]
								]
							);

                            // Update original item
                            if ( $original_item ) {
                            	// Get original item remaining insurance amount
                            	$item_remaining_insurance = floatval( $original_item->get_meta( 'ovabrw_remaining_insurance' ) );

                            	// Get original item remaining insurance tax amount
                            	$item_remaining_insurance_tax = floatval( $original_item->get_meta( 'ovabrw_remaining_insurance_tax' ) );

                            	// Update original item meta data
                            	$original_item->update_meta_data( 'ovabrw_remaining_insurance', $order_insurance );
                            	$original_item->update_meta_data( 'ovabrw_remaining_insurance_tax', $order_insurance_tax );
                            	$original_item->save();

                            	// Update original order
	                            if ( $original_order ) {
	                            	// Get original order remaining insurance amount
	                            	$order_remaining_insurance = floatval( $original_order->get_meta( '_ova_remaining_insurance' ) );
	                            	$order_remaining_insurance -= $item_remaining_insurance;
	                            	$order_remaining_insurance += $order_insurance;

	                            	// Get original order remaining insurance tax amount
	                            	$order_remaining_insurance_tax = floatval( $original_order->get_meta( '_ova_remaining_insurance_tax' ) );
	                            	$order_remaining_insurance_tax -= $item_remaining_insurance_tax;
	                            	$order_remaining_insurance_tax += $order_insurance_tax;

	                            	// Update original order meta data
	                            	$original_order->update_meta_data( '_ova_remaining_insurance', $order_remaining_insurance );
	                            	$original_order->update_meta_data( '_ova_remaining_insurance_tax', $order_remaining_insurance_tax );
	                            	$original_order->save();
	                            }
                            }
            			} else {
            				$item_fee->set_props([
            					'total'     => $order_insurance,
								'subtotal'  => $order_insurance
            				]);

							// Update original order and item
                            if ( $original_item ) {
                            	// Get original item remaining insurance amount
                            	$item_remaining_insurance = floatval( $original_item->get_meta( 'ovabrw_remaining_insurance' ) );

                            	// Update original item meta data
                            	$original_item->update_meta_data( 'ovabrw_remaining_insurance', $order_insurance );
                            	$original_item->save();

                            	// Update original order
	                            if ( $original_order ) {
	                            	// Get original order remaining insurance amount
	                            	$order_remaining_insurance = floatval( $original_order->get_meta( '_ova_remaining_insurance' ) );
	                            	$order_remaining_insurance -= $item_remaining_insurance;
	                            	$order_remaining_insurance += $order_insurance;

	                            	// Update original order meta data
	                            	$original_order->update_meta_data( '_ova_remaining_insurance', $order_remaining_insurance );
	                            	$original_order->save();
	                            }
                            }
            			}

            			$item_fee->set_amount( $order_insurance );
            			$item_fee->save();

            			// Update item insurance
        				$item->update_meta_data( 'ovabrw_insurance_amount', $amount );
        				$item->update_meta_data( 'ovabrw_insurance_tax', $tax );
        				$item->save();

        				// Update order insurance
        				$order->update_meta_data( '_ova_insurance_amount', $order_insurance );
        				$order->update_meta_data( '_ova_insurance_tax', $order_insurance_tax );
        				$order->update_taxes();
        				$order->calculate_totals( false );
            		}
            	}
            }

            wp_die();
		}

		/**
		 * Booking calendar filter by product ID
		 */
		public function ovabrw_booking_calendar_get_events() {
            check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Product ID
		    $product_id = (int)ovabrw_get_meta_data( 'product_id', $_POST );

		    // Current month
		    $current_month = ovabrw_get_meta_data( 'current_month', $_POST );

		    // Current year
		    $current_year = ovabrw_get_meta_data( 'current_year', $_POST );

		    // Get events
		    $events = OVABRW_Admin_Bookings::instance()->get_events( $product_id, $current_month, $current_year );

			echo json_encode([
				'events' => $events
			]);
			wp_die();
		}

		/**
		 * Booking calendar get available items
		 */
		public function ovabrw_booking_calendar_get_available_items() {
			check_admin_referer( 'ovabrw-security-ajax', 'security' );

            // Product ID
		    $product_id = (int)ovabrw_get_meta_data( 'product_id', $_POST );
		    if ( !$product_id ) wp_die();

		    // Get rental product
		    $rental_product = OVABRW()->rental->get_rental_product( $product_id );
		    if ( !$rental_product ) wp_die();

		    // Pick-up location
		    $pickup_location = ovabrw_get_meta_data( 'pickup_location', $_POST );

		    // Drop-off location
		    $dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $_POST );

		    // Pick-up date
		    $pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $_POST ) );

		    // Drop-off date
		    $dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $_POST ) );

		    // Get items available
			$items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'checkout' );

			// Vehicles available
			if ( is_array( $items_available ) ) {
				echo sprintf( esc_html__( 'Items available: %s', 'ova-brw' ), implode( ', ', $items_available ) );
			} else {
				echo sprintf( esc_html__( 'Items available: %s', 'ova-brw' ), $items_available );
			}

			wp_die();
		}
	}

	new OVABRW_Admin_Ajax();
}