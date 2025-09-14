<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Booking class
 */
if ( !class_exists( 'OVABRW_Booking' ) ) {

	class OVABRW_Booking {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// HTML Product Sticky
			add_action( 'woocommerce_after_single_product', [ $this, 'product_sticky' ] );

			// Cart validation
			add_filter( 'woocommerce_add_to_cart_validation', [ $this, 'add_to_cart_validation' ], 11, 3 );

			// Add cart item data
			add_filter( 'woocommerce_add_cart_item_data', [ $this, 'add_cart_item_data' ], 11, 4 );

			// Get cart item data
			add_filter( 'woocommerce_get_item_data', [ $this, 'get_item_data' ], 11, 2 );

			// Cart item price
			add_filter( 'woocommerce_cart_item_price', [ $this, 'cart_item_price' ], 11, 3 );

			// Cart item quantity
			add_filter( 'woocommerce_cart_item_quantity', [ $this, 'cart_item_quantity' ], 11, 3 );

			// Checkout cart item quantity
			add_filter( 'woocommerce_checkout_cart_item_quantity', [ $this, 'checkout_cart_item_quantity' ], 11, 3 );

			// Order item quantity html
			add_filter( 'woocommerce_order_item_quantity_html', [ $this, 'order_item_quantity_html' ], 11, 2 );

			// Before calculate totals
			add_action( 'woocommerce_before_calculate_totals', [ $this, 'before_calculate_totals' ], 11 );

			// After checkout Validate
			add_action( 'woocommerce_after_checkout_validation', [ $this, 'after_checkout_validation' ], 11, 2 );

			// Cart block validation
			add_action( 'woocommerce_store_api_cart_errors', [ $this, 'store_api_cart_errors' ], 11, 2 );

			// Checkout order created - Reserve stock for order
			add_action( 'woocommerce_checkout_order_created', [ $this, 'reserve_stock_for_order' ], 11 );

			// Checkout create order line item
			add_action( 'woocommerce_checkout_create_order_line_item', [ $this, 'checkout_create_order_line_item' ], 11, 4 );

			// Checkout create order fee item
			add_action( 'woocommerce_checkout_create_order_fee_item', [ $this, 'checkout_create_order_fee_item' ], 10, 4 );

			// Order item display meta key
			add_filter( 'woocommerce_order_item_display_meta_key', [ $this, 'order_item_display_meta_key' ], 11, 3 );

			// Order item display meta value
			add_filter( 'woocommerce_order_item_display_meta_value', [ $this, 'order_item_display_meta_value' ], 11, 3 );

			// Hide item meta fields
			add_filter( 'woocommerce_order_item_get_formatted_meta_data', [ $this, 'hide_item_meta_fields' ], 11, 2 );

			// Email order item quantity
			add_filter( 'woocommerce_email_order_item_quantity', [ $this, 'email_order_item_quantity' ], 11, 2 );
		}

		/**
		 * Product sticky
		 */
		public function product_sticky() {
			if ( !apply_filters( OVABRW_PREFIX.'before_product_sticky', true ) ) return;

			do_action( OVABRW_PREFIX.'before_product_sticky' );

			// Get rental product
			$product = ovabrw_get_rental_product();
			if ( !$product ) return;

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product->get_id() );
			if ( !$rental_product ) return;

			// Price format
			$price_format = $product->get_price_html_from_format();

			// Button link
        	$link = '#booking_form';
        	if ( 'yes' !== ovabrw_get_setting( 'template_show_booking_form', 'yes' ) ) {
        		if ( 'yes' === ovabrw_get_setting( 'template_show_request_booking', 'yes' ) ) {
        			$link = '#request_booking';
        		} else {
        			$link = '#';
        		}
        	}
        	
			$args = [
				'price_format' 		=> $price_format,
		        'rental_product' 	=> $rental_product,
		        'link'      		=> $link
			];

			// HTML product sticky
			do_action( OVABRW_PREFIX.'modern_product_sticky', $args );

			do_action( OVABRW_PREFIX.'after_product_sticky' );
		}

		/**
		 * Create order remaining
		 */
		public function create_order_remaining( $order_id, $item_data = [] ) {
			// Get order
			$order = wc_get_order( $order_id );
			if ( !$order ) return false;

			try {
	            $new_order = new WC_Order;
	            $new_order->set_props([
	            	'status'              => 'wc-pending',
	                'customer_id'         => $order->get_user_id(),
	                'customer_note'       => $order->get_customer_note(),
	                'billing_first_name'  => $order->get_billing_first_name(),
	                'billing_last_name'   => $order->get_billing_last_name(),
	                'billing_company'     => $order->get_billing_company(),
	                'billing_address_1'   => $order->get_billing_address_1(),
	                'billing_address_2'   => $order->get_billing_address_2(),
	                'billing_city'        => $order->get_billing_city(),
	                'billing_state'       => $order->get_billing_state(),
	                'billing_postcode'    => $order->get_billing_postcode(),
	                'billing_country'     => $order->get_billing_country(),
	                'billing_email'       => $order->get_billing_email(),
	                'billing_phone'       => $order->get_billing_phone(),
	                'shipping_first_name' => $order->get_shipping_first_name(),
	                'shipping_last_name'  => $order->get_shipping_last_name(),
	                'shipping_company'    => $order->get_shipping_company(),
	                'shipping_address_1'  => $order->get_shipping_address_1(),
	                'shipping_address_2'  => $order->get_shipping_address_2(),
	                'shipping_city'       => $order->get_shipping_city(),
	                'shipping_state'      => $order->get_shipping_state(),
	                'shipping_postcode'   => $order->get_shipping_postcode(),
	                'shipping_country'    => $order->get_shipping_country()
	            ]);
	            $new_order->set_currency( $order->get_currency() );
	            $new_order->save();
	        } catch ( Exception $e ) {
	            $order->add_order_note( sprintf( esc_html__( 'Error: Unable to create follow up payment (%s)', 'ova-brw' ), $e->getMessage() ) );
	            return;
	        }

	        // Order total
	        $order_total = ovabrw_get_meta_data( 'total', $item_data );

	        // Product
	        $item_product = ovabrw_get_meta_data( 'product', $item_data );

	        // Quantity
	        $item_quantity = (int)ovabrw_get_meta_data( 'quantity', $item_data, 1 );

	        // Item subtotal
	        $item_subtotal = (float)ovabrw_get_meta_data( 'subtotal', $item_data );

	        // Item total
	        $item_total = (float)ovabrw_get_meta_data( 'total', $item_data );

	        // Handle items
	        $item_id = $new_order->add_product( $item_product, $item_quantity, [
	        	'totals' => [
	            	'subtotal' 	=> $item_subtotal,
	                'total' 	=> $item_total
	            ]
	        ]);

	        // Get order line item
	        $line_item = $new_order->get_item( $item_id );

	        $new_order->set_parent_id( $order_id );
	        $new_order->set_date_created( gmdate( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );

	        // Get tax rate id
	        $tax_class 		= $item_product->get_tax_class();
	        $tax_rate_id 	= 0;
	        if ( wc_tax_enabled() ) {
	        	$tax_rates = WC_Tax::get_rates( $tax_class );

		        if ( ovabrw_array_exists( $tax_rates ) ) {
		            $tax_rate_id = key( $tax_rates );
		        }
	        }

	        // Remaining tax amount
	        $remaining_tax = (float)ovabrw_get_meta_data( 'remaining_tax', $item_data );

	        // Insurance amount
	        $insurance_amount 	= (float)ovabrw_get_meta_data( 'insurance_amount', $item_data );
	        $insurance_tax 		= (float)ovabrw_get_meta_data( 'insurance_tax', $item_data );

	        // Add item fee
	        if ( $insurance_amount ) {
	        	// Update order total
	        	$order_total += $insurance_amount;

		        // Get insurance name
		        $insurance_name = OVABRW()->options->get_insurance_name();

		        // Init order item fee
	        	$item_fee = new WC_Order_Item_Fee();

	        	// Item fee data
	        	$item_fee_data = [
	        		'name'      => $insurance_name,
	                'amount'    => $insurance_amount,
	                'total'     => $insurance_amount,
	                'order_id'  => $order_id
	        	];

	        	// Add item fee tax
	        	if ( wc_tax_enabled() && $insurance_tax ) {
	        		// Update order total
	            	$order_total += $insurance_tax;

	        		// Set tax for item fee
	        		$item_fee_data['tax_class'] = $tax_class ? $tax_class : 0;
	        		$item_fee_data['total_tax'] = $insurance_tax;
	        		$item_fee_data['taxes'] 	= [
	        			'total' => [
	        				$tax_rate_id => $insurance_tax
	        			]
	        		];

	        		// Order add meta insurance tax
	        		$new_order->add_meta_data( '_ova_insurance_tax', $insurance_tax );

	        		// Line item add meta insurance tax
	        		$line_item->add_meta_data( 'ovabrw_insurance_tax', $insurance_tax );
	        	}

	            $item_fee->set_props( $item_fee_data );
	            $item_fee->save();

	            // Order add item fee
	            $new_order->add_item( $item_fee );

	            // Order add meta data
	            $new_order->add_meta_data( '_ova_insurance_key', sanitize_title( $insurance_name ) );
	            $new_order->add_meta_data( '_ova_insurance_amount', $insurance_amount );

	            // Line item add meta data
	            $line_item->add_meta_data( ovabrw_meta_key( 'insurance_amount' ), $insurance_amount );
	            $line_item->save();
	        }

	    	// Add item tax
	        if ( wc_tax_enabled() && $remaining_tax ) {
	        	// Update order total
	        	$order_total += $remaining_tax;

	        	// Order tax amount
	        	$order_tax_amount = $remaining_tax + $insurance_tax;

	        	// Init order item tax
	            $item_tax = new WC_Order_Item_Tax();

	            $item_tax->set_props([
	            	'rate_id'            => $tax_rate_id,
	                'tax_total'          => $order_tax_amount,
	                'shipping_tax_total' => 0,
	                'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
	                'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
	                'compound'           => WC_Tax::is_compound( $tax_rate_id ),
	                'rate_percent'       => WC_Tax::get_rate_percent_value( $tax_rate_id )
	            ]);

	            $item_tax->save();
	            $new_order->add_item( $item_tax );
	            $new_order->set_cart_tax( $order_tax_amount );

	            // Set tax for line item
	            $line_item->set_props([
	            	'taxes' => [
	            		'total' 	=> [ $tax_rate_id => $remaining_tax ],
	            		'subtotal' 	=> [ $tax_rate_id => $remaining_tax ]
	            	]
	            ]);

	            $line_item->save();

	            // Prices include tax
	            $prices_incl_tax = $order->get_meta( '_ova_prices_include_tax' );
	            if ( $prices_incl_tax ) {
	                $new_order->update_meta_data( '_ova_prices_include_tax', $prices_incl_tax );
	            }
	        }
	        
	        // Order set total
	        $new_order->set_total( $order_total );
	        $new_order->save();

	        // Add parent order id
	        wc_add_order_item_meta( $item_id, ovabrw_meta_key( 'parent_order_id' ), $order_id );

	        // Update item meta
	        wc_update_order_item( $item_id, [ 'order_item_name' => sprintf( esc_html__( 'Payment remaining for %s', 'ova-brw' ), $item_product->get_title() ) ]);

	        return apply_filters( OVABRW_PREFIX.'create_order_remaining', $new_order->get_id(), $order_id, $item_data );
		}

		/**
		 * Add to cart validation
		 */
		public function add_to_cart_validation( $passed, $product_id, $quantity ) {
			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return $passed;

			// Get passed
			$passed = $rental_product->add_to_cart_validation( $passed, $product_id, $quantity );

			return $passed;
		}

		/**
		 * Add cart item data
		 */
		public function add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity ) {
			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return $cart_item_data;

			// Get cart item data
			$cart_item_data = $rental_product->add_cart_item_data( $cart_item_data, $product_id, $variation_id, $quantity );

			return $cart_item_data;
		}

		/**
		 * Get item data
		 */
		public function get_item_data( $item_data, $cart_item ) {
			// Product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
			if ( !$product_id ) return $item_data;

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return $item_data;

			// Get item data
			$item_data = $rental_product->get_cart_item_data( $item_data, $cart_item );

			return $item_data;
		}

		/**
		 * Cart item price
		 */
		public function cart_item_price( $product_price, $cart_item, $cart_item_key ) {
			// Product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
			if ( !$product_id ) return $product_price;

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return $product_price;

			return $rental_product->get_cart_item_price( $product_price, $cart_item, $cart_item_key );
		}

		/**
		 * Cart item quantity
		 */
		public function cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
			// Product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
			if ( !$product_id ) return $product_quantity;

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return $product_quantity;

			return $rental_product->get_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item );
		}

		/**
		 * Checkout cart item quantity
		 */
		public function checkout_cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ) {
			// Product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
			if ( !$product_id ) return $product_quantity;

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return $product_quantity;

			return $rental_product->get_checkout_cart_item_quantity( $product_quantity, $cart_item, $cart_item_key );
		}

		/**
		 * Order item quantity HTML
		 */
		public function order_item_quantity_html( $item_quantity, $item ) {
			// Get product ID
			$product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';
			if ( !$product_id ) return $item_quantity;

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return $item_quantity;

			return $rental_product->get_order_item_quantity_html( $item_quantity, $item );
		}

		/**
		 * Before calculate totals
		 */
		public function before_calculate_totals( $cart ) {
			// Deposit
			WC()->cart->deposit_info = [];

			$has_deposit 	= false;
			$deposit_amount = $remaining_amount = $remaining_tax = 0;

			// Insurance
			$insurance_amount = $insurance_tax = $remaining_insurance = $remaining_insurance_tax = 0;

			// Loop cart
			foreach ( $cart->get_cart()  as $cart_item_key => $cart_item ) {
				// Product ID
				$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
				if ( !$product_id ) continue;

				// Get rental product
				$rental_product = OVABRW()->rental->get_rental_product( $product_id );
				if ( !$rental_product ) continue;

				// Quantity
				$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item, 1 );

				// Sub-insurance amount
				$sub_insurance = (float)$rental_product->get_meta_value( 'amount_insurance' );
				$sub_insurance *= $quantity;

				// Sub remaining insurance amount
				$sub_remaining_insurance = 0;

				// Get sub-total
				$subtotal = $rental_product->get_total([
					'pickup_date' 		=> strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) ),
		        	'dropoff_date' 		=> strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) ),
		        	'pickup_location' 	=> ovabrw_get_meta_data( 'pickup_location', $cart_item ),
		        	'dropoff_location' 	=> ovabrw_get_meta_data( 'dropoff_location', $cart_item ),
		        	'package_id' 		=> ovabrw_get_meta_data( 'package_id', $cart_item ),
		        	'quantity' 			=> $quantity,
		        	'cckf'  			=> ovabrw_get_meta_data( 'cckf', $cart_item ),
		        	'cckf_qty' 			=> ovabrw_get_meta_data( 'cckf_qty', $cart_item ),
		        	'resources' 		=> ovabrw_get_meta_data( 'resources', $cart_item ),
		        	'resources_qty' 	=> ovabrw_get_meta_data( 'resources_qty', $cart_item ),
		        	'services' 			=> ovabrw_get_meta_data( 'services', $cart_item ),
		        	'services_qty' 		=> ovabrw_get_meta_data( 'services_qty', $cart_item ),
		        	'duration' 			=> ovabrw_get_meta_data( 'duration', $cart_item, 0 ),
		        	'distance' 			=> ovabrw_get_meta_data( 'distance', $cart_item ),
		        	'extra_time' 		=> ovabrw_get_meta_data( 'extra_time', $cart_item )
				]);

				// Multi currency
				if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
	                $subtotal 		= ovabrw_convert_price( $subtotal );
	                $sub_insurance 	= ovabrw_convert_price( $sub_insurance );
	            }

	            // is deposit
	            $is_deposit = ovabrw_get_meta_data( 'is_deposit', $cart_item );
	            if ( $is_deposit ) {
	            	$has_deposit = true;
	            	$sub_deposit = 0;

	            	// Deposit type
	            	$deposit_type = $rental_product->get_meta_value( 'type_deposit' );

	            	// Deposit value
	            	$deposit_value = (float)$rental_product->get_meta_value( 'amount_deposit' );

	            	// Calculate deposit
	            	if ( 'percent' === $deposit_type ) {
	            		$sub_deposit = apply_filters( OVABRW_PREFIX.'calculate_deposit_percent', ( $subtotal * $deposit_value ) / 100, $cart_item );

	            		// Insurance
	            		if ( $sub_insurance && OVABRW()->options->remaining_amount_incl_insurance() ) {
	            			$sub_remaining_insurance = $sub_insurance - floatval( ( $sub_insurance * $deposit_value ) / 100 );
			            	$sub_insurance = floatval( ( $sub_insurance * $deposit_value ) / 100 );
			            }
	            	} elseif ( 'value' === $deposit_type ) {
	            		$sub_deposit = apply_filters( OVABRW_PREFIX.'calculate_deposit_fixed', $deposit_value, $cart_item );
	            	}

	            	// Set item price
		            $cart_item['data']->set_price( round( $sub_deposit / $quantity, wc_get_price_decimals() ) );

	            	// Sub remaining
	            	$sub_remaining = floatval( $subtotal - $sub_deposit );

	            	// Coupons
			        $coupons = WC()->cart->get_applied_coupons();
			        if ( ovabrw_array_exists( $coupons ) ) {
			            foreach ( $coupons as $coupon_code ) {
			                $coupon = new WC_Coupon( $coupon_code );

			                // Valid coupon
			                if ( !$coupon->is_valid_for_product( $cart_item['data'] ) ) continue;

			                if ( $coupon && is_object( $coupon ) ) {
			                    $coupon_type    = $coupon->get_discount_type();
			                    $coupon_amount  = $coupon->get_amount();
			                    
			                    if ( $coupon_type && $coupon_amount ) {
			                        if ( 'fixed_cart' == $coupon_type || 'fixed_product' == $coupon_type ) {
			                            $sub_deposit -= $coupon_amount;
			                        } elseif ( 'percent' == $coupon_type ) {
			                            $sub_deposit 	-= $sub_deposit * ( $coupon_amount / 100 );
			                            $sub_remaining 	-= $sub_remaining * ( $coupon_amount / 100 );
			                        }
			                    }
			                }
			            }
			        }

			        // Remaining tax
	            	$sub_remaining_taxes 	= OVABRW()->options->get_taxes_by_price( $cart_item['data'], ovabrw_convert_price( $sub_remaining, [], false ) );
	            	$remaining_tax 			+= $sub_remaining_taxes;

	            	// Cart item add data
	            	$cart_item['data']->add_meta_data( 'is_deposit', $is_deposit, true );
	            	$cart_item['data']->add_meta_data( 'deposit_type', $deposit_type, true );
	            	$cart_item['data']->add_meta_data( 'deposit_value', $deposit_value, true );
	            	$cart_item['data']->add_meta_data( 'deposit_amount', round( $sub_deposit, wc_get_price_decimals() ), true );
		            $cart_item['data']->add_meta_data( 'remaining_amount', round( $sub_remaining, wc_get_price_decimals() ), true );
		            $cart_item['data']->add_meta_data( 'remaining_tax', round( $sub_remaining_taxes, wc_get_price_decimals() ), true );
		            $cart_item['data']->add_meta_data( 'total_payable', round( $subtotal, wc_get_price_decimals() ), true );

		            // Deposit amount
		            $deposit_amount += $sub_deposit;

		            // Remaining amount
	            	$remaining_amount += $sub_remaining;
	            } else {
	            	// Set item price
	            	$cart_item['data']->set_price( round( $subtotal / $quantity, wc_get_price_decimals() ) );
	            }

	            // Insurance
	            if ( $sub_insurance ) {
	            	$insurance_amount += $sub_insurance;
	            	$cart_item['data']->add_meta_data( 'insurance_amount', round( $sub_insurance, wc_get_price_decimals() ), true );

	            	$sub_insurance_tax = OVABRW()->options->get_insurance_tax_amount( ovabrw_convert_price( $sub_insurance, [], false ) );

	            	if ( $sub_insurance_tax ) {
	            		$insurance_tax += $sub_insurance_tax;

	            		$cart_item['data']->add_meta_data( 'insurance_tax', round( $sub_insurance_tax, wc_get_price_decimals() ), true );
	            	}
	            }

	            // Remaining insurance
	            if ( $sub_remaining_insurance ) {
	            	$remaining_insurance += $sub_remaining_insurance;
	            	$cart_item['data']->add_meta_data( 'remaining_insurance', round( $sub_remaining_insurance, wc_get_price_decimals() ), true );

	            	$sub_remaining_insurance_tax = OVABRW()->options->get_insurance_tax_amount( ovabrw_convert_price( $sub_remaining_insurance, [], false ) );

	            	if ( $sub_remaining_insurance_tax ) {
	            		$remaining_insurance_tax += $sub_remaining_insurance_tax;

	            		$cart_item['data']->add_meta_data( 'remaining_insurance_tax', round( $sub_remaining_insurance_tax, wc_get_price_decimals() ), true );
	            	}
	            }

	            // Set quantity
            	$cart->cart_contents[$cart_item_key]['quantity'] = $quantity;
			} // END loop

			// Deposit info
			if ( $has_deposit ) {
				WC()->cart->deposit_info[ 'has_deposit' ] 		= $has_deposit;
	            WC()->cart->deposit_info[ 'deposit_amount' ] 	= round( $deposit_amount, wc_get_price_decimals() );
	            WC()->cart->deposit_info[ 'remaining_amount' ]  = round( $remaining_amount, wc_get_price_decimals() );
	            WC()->cart->deposit_info[ 'remaining_tax' ]   	= round( $remaining_tax, wc_get_price_decimals() );
			}

			// Cart fee - Insurance
			if ( $insurance_amount ) {
				$insurance_name 		= OVABRW()->options->get_insurance_name();
				$enable_insurance_tax 	= OVABRW()->options->enable_insurance_tax();
				$tax_class 				= OVABRW()->options->get_insurance_tax_class();

				WC()->cart->add_fee( $insurance_name, ovabrw_convert_price( $insurance_amount, [], false ), $enable_insurance_tax, $tax_class );

				WC()->cart->deposit_info[ 'insurance_amount' ] 	= $insurance_amount;
				WC()->cart->deposit_info[ 'insurance_tax' ] 	= $insurance_tax;
				WC()->cart->deposit_info[ 'insurance_key' ] 	= sanitize_title( $insurance_name );
			}
			if ( $remaining_insurance ) {
				WC()->cart->deposit_info[ 'remaining_insurance' ] 		= $remaining_insurance;
				WC()->cart->deposit_info[ 'remaining_insurance_tax' ] 	= $remaining_insurance_tax;
			}
		}

		/**
		 * After checkout validations
		 */
		public function after_checkout_validation( $data, $errors ) {
			if ( method_exists( WC()->cart, 'get_cart' ) && ovabrw_array_exists( WC()->cart->get_cart() ) ) {
				foreach ( WC()->cart->get_cart() as $cart_item ) {
					// Product ID
					$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
					if ( !$product_id ) continue;

					// Get rental product
					$rental_product = OVABRW()->rental->get_rental_product( $product_id );
					if ( !$rental_product ) continue;

					// Input quantity
					$input_quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item, 1 );

					// Pick-up location
					$pickup_location = ovabrw_get_meta_data( 'pickup_location', $cart_item );

					// Drop-off location
					$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $cart_item );

					// Pick-up date
					$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

					// Drop-off date
					$dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );

					// Package ID
					$package_id = ovabrw_get_meta_data( 'package_id', $cart_item );

					// Duration
					$duration = ovabrw_get_meta_data( 'duration', $cart_item, 0 );

					// Get new date
					$new_date = $rental_product->get_new_date([
						'pickup_location' 	=> $pickup_location,
						'dropoff_location' 	=> $dropoff_location,
						'pickup_date' 		=> $pickup_date,
						'dropoff_date' 		=> $dropoff_date,
						'package_id' 		=> $package_id,
						'duration' 			=> $duration
					]);
					if ( !ovabrw_array_exists( $new_date ) ) wp_die();

					// Pick-up date
					$pickup_date = ovabrw_get_meta_data( 'pickup_date', $new_date );

					// Drop-off date
					$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $new_date );

					// Get items available
					$items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'checkout' );

					// Vehicles available
					if ( is_array( $items_available ) ) {
						$items_available = count( $items_available );
					}

					// Get reserved items
	        		$reserved_items = $rental_product->get_reserved_items( $pickup_date, $dropoff_date );
	        		if ( $reserved_items ) $items_available -= $reserved_items;

	        		// Check quantity
	        		if ( $items_available < $input_quantity ) {
	        			$errors->add( 'validation', sprintf( esc_html__( '%s isn\'t available for this time, Please book other time.', 'ova-brw' ), $rental_product->product->get_title() ) );
	        		}
				} // END loop
			} // END if
		}

		/**
		 * Cart block validation
		 */
		public function store_api_cart_errors( $cart_errors, $cart ) {
			if ( method_exists( $cart, 'cart_contents' ) && ovabrw_array_exists( $cart->cart_contents ) ) {
				foreach ( $cart->cart_contents as $cart_item ) {
					// Product ID
					$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
					if ( !$product_id ) continue;

					// Get rental product
					$rental_product = OVABRW()->rental->get_rental_product( $product_id );
					if ( !$rental_product ) continue;

					// Input quantity
					$input_quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item, 1 );

					// Pick-up location
					$pickup_location = ovabrw_get_meta_data( 'pickup_location', $cart_item );

					// Drop-off location
					$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $cart_item );

					// Pick-up date
					$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

					// Drop-off date
					$dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );

					// Package ID
					$package_id = ovabrw_get_meta_data( 'package_id', $cart_item );

					// Duration
					$duration = ovabrw_get_meta_data( 'duration', $cart_item, 0 );

					// Get new date
					$new_date = $rental_product->get_new_date([
						'pickup_location' 	=> $pickup_location,
						'dropoff_location' 	=> $dropoff_location,
						'pickup_date' 		=> $pickup_date,
						'dropoff_date' 		=> $dropoff_date,
						'package_id' 		=> $package_id,
						'duration' 			=> $duration
					]);
					if ( !ovabrw_array_exists( $new_date ) ) wp_die();

					// Pick-up date
					$pickup_date = ovabrw_get_meta_data( 'pickup_date', $new_date );

					// Drop-off date
					$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $new_date );

					// Get items available
					$items_available = $rental_product->get_items_available( $pickup_date, $dropoff_date, $pickup_location, $dropoff_location, 'checkout' );

					// Vehicles available
					if ( is_array( $items_available ) ) {
						$items_available = count( $items_available );
					}

					// Get reserved items
	        		$reserved_items = $rental_product->get_reserved_items( $pickup_date, $dropoff_date );
	        		if ( $reserved_items ) $items_available -= $reserved_items;

	        		// Check quantity
	        		if ( $items_available < $input_quantity ) {
	        			$errors->add( 'validation', sprintf( esc_html__( '%s isn\'t available for this time, Please book other time.', 'ova-brw' ), $rental_product->product->get_title() ) );
	        		}
				} // END loop
			} // END if
		}

		/**
		 * Reserve stock for order
		 */
		public function reserve_stock_for_order( $order ) {
			do_action( OVABRW_PREFIX.'before_reserve_stock_for_order', $order );

			// Order hold stock minutes
            $minutes = apply_filters( OVABRW_PREFIX.'order_hold_stock_minutes', (int)get_option( 'woocommerce_hold_stock_minutes', 60 ), $order );
            if ( !$minutes ) return;

            try {
                $items = array_filter( $order->get_items(), function ( $item ) {
                    return $item->is_type( 'line_item' ) && $item->get_product() instanceof \WC_Product && $item->get_quantity() > 0;
                });

                foreach ( $items as $item ) {
                	// Get product
                    $product = method_exists( $item, 'get_product' ) ? $item->get_product() : '';
                    if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) continue;

                    // Get product ID
                    $product_id = $product->get_stock_managed_by_id();
                    
                    // Add reserve stock for product
                    $this->reserve_stock_for_product( $product_id, 0, $order, $minutes );
                }
            } catch ( Exception $e ) {
                $this->release_stock_for_order( $order );
            }

            do_action( OVABRW_PREFIX.'after_reserve_stock_for_order', $order );
		}

		/**
         * Reserve stock for product
         */
        public function reserve_stock_for_product( $product_id, $stock_quantity, $order, $minutes ) {
        	do_action( OVABRW_PREFIX.'before_reserve_stock_for_product', $product_id, $stock_quantity, $order, $minutes );

            global $wpdb;

            // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
            $result = $wpdb->query(
                $wpdb->prepare(
                    "
                    INSERT INTO {$wpdb->wc_reserved_stock} ( `order_id`, `product_id`, `stock_quantity`, `timestamp`, `expires` )
                    SELECT %d, %d, %d, NOW(), ( NOW() + INTERVAL %d MINUTE ) FROM DUAL
                    ON DUPLICATE KEY UPDATE `timestamp` = VALUES( `timestamp` ), `expires` = VALUES( `expires` ), `stock_quantity` = VALUES( `stock_quantity` )
                    ",
                    $order->get_id(),
                    $product_id,
                    $stock_quantity,
                    $minutes
                )
            );
            // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
            
            do_action( OVABRW_PREFIX.'after_reserve_stock_for_product', $product_id, $stock_quantity, $order, $minutes );
        }

        /**
         * Release stock for order
         */
        public function release_stock_for_order( $order ) {
        	do_action( OVABRW_PREFIX.'before_release_stock_for_order', $order );

            global $wpdb;
            $wpdb->delete( $wpdb->wc_reserved_stock, [ 'order_id' => $order->get_id() ] );

            do_action( OVABRW_PREFIX.'after_release_stock_for_order', $order );
        }

        /**
         * Checkout create order line item
         */
		public function checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
			// Get product ID
			$product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';
			if ( !$product_id ) return;

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return;

			// Save order line item
			$rental_product->save_order_line_item( $item, $values );
		}

		/**
		 * Checkout create order fee item
		 */
		public function checkout_create_order_fee_item( $item, $fee_key, $fee, $order ) {
			// Get insurance key
			$insurance_key = isset( WC()->cart->deposit_info[ 'insurance_key' ] ) ? WC()->cart->deposit_info[ 'insurance_key' ] : '';

			if ( $insurance_key == $fee_key ) {
				$order->add_meta_data( '_ova_insurance_key', $insurance_key, true );
			}
		}

		/**
		 * Order item display meta key
		 */
		public function order_item_display_meta_key( $display_key, $meta, $item ) {
			// Get product ID
			$product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );

			// Location
			if ( 'ovabrw_location' === $meta->key ) {
				$display_key = esc_html__( 'Location', 'ova-brw' );
			}

			// Pick-up location
			if ( 'ovabrw_pickup_loc' === $meta->key ) {
				$display_key = esc_html__( 'Pick-up Location', 'ova-brw' );
			}

			// Drop-off location
        	if ( 'ovabrw_pickoff_loc' === $meta->key ) {
        		$display_key = esc_html__( 'Drop-off Location', 'ova-brw' );
        	}

			// Location prices
        	if ( 'ovabrw_location_prices' === $meta->key ) {
        		$display_key = esc_html__( 'Location Price', 'ova-brw' );
        	}

        	// Pick-up date
        	if ( 'ovabrw_pickup_date' === $meta->key ) {
        		$display_key = $rental_product ? $rental_product->product->get_date_label() : esc_html__( 'Pick-up Date', 'ova-brw' );
        	}

        	// Drop-off date
        	if ( 'ovabrw_pickoff_date' === $meta->key ) {
        		$display_key = $rental_product ? $rental_product->product->get_date_label( 'dropoff' ) : esc_html__( 'Drop-off Date', 'ova-brw' );
        	}

        	// Package label
        	if ( 'period_label' === $meta->key ) {
        		$display_key = esc_html__(' Package', 'ova-brw' );
        	}

        	// Quantity
        	if ( 'ovabrw_number_vehicle' === $meta->key ) {
        		$display_key = esc_html__( 'Quantity', 'ova-brw' );
        	}

        	// Vebicle ID
        	if ( 'id_vehicle' === $meta->key ) {
        		$display_key = esc_html__( 'Vehicle ID(s)', 'ova-brw' );
        	}

        	// Total time
        	if ( 'ovabrw_total_days' === $meta->key ) {
        		$display_key = esc_html__( 'Total Time', 'ova-brw' );
        	}

        	// Distance
        	if ( 'ovabrw_distance' === $meta->key ) {
        		$display_key = esc_html__( 'Distance', 'ova-brw' );
        	}

        	// Extra time
	        if ( 'ovabrw_extra_time' === $meta->key ) {
	        	$display_key = esc_html__( 'Extra Time', 'ova-brw' );
	        }

	        // Duration
	        if ( 'ovabrw_duration' === $meta->key ) {
	        	$display_key = esc_html__( 'Duration', 'ova-brw' );
	        }

	        // Origina order ID
	        if ( 'ovabrw_original_order_id' === $meta->key ) {
	        	$display_key = esc_html__( 'Original Order', 'ova-brw' );
	        }

	        // Ramaining order
	        if ( 'ovabrw_remaining_balance_order_id' === $meta->key ) {
	        	$display_key = esc_html__( 'Remaining Order', 'ova-brw' );
	        }

	        // Number of adults
	        if ( 'ovabrw_adults' === $meta->key ) {
	        	$display_key = esc_html__( 'Number of Adults', 'ova-brw' );
	        }

	        // Number of children
	        if ( 'ovabrw_children' === $meta->key ) {
	        	$display_key = esc_html__( 'Number of Children', 'ova-brw' );
	        }

	        // Number of babies
	        if ( 'ovabrw_babies' === $meta->key ) {
	        	$display_key = esc_html__( 'Number of Babies', 'ova-brw' );
	        }

        	// Custom Checkout Fields
        	$cckf = ovabrw_get_option( 'booking_form', [] );
	        if ( ovabrw_array_exists( $cckf ) ) {
	            foreach ( $cckf as $key => $fields ) {
	                if ( $key === $meta->key ) {
	                    $display_key = ovabrw_get_meta_data( 'label', $fields );
	                }
	            }
	        }

	        // Deposit
	        $tax_text = $remaining_tax_text = '';

	        if ( wc_tax_enabled() ) {
	        	// Get order
	            $order = $item->get_order();
	            if ( $order ) {
	            	// Get meta data
		            $remaining_item  = $item->get_meta( 'ovabrw_remaining_amount_product' );
		            $is_tax_included = $order->get_meta( '_ova_tax_display_cart', true );
		            $remaining_taxes = $order->get_meta( '_ova_remaining_taxes', true );
		            $tax_message     = $is_tax_included ? esc_html__( '(incl. tax)', 'ova-brw' ) : esc_html__( '(excl. tax)', 'ova-brw' );

		            if ( $remaining_taxes ) {
		                $tax_tex = ' <small class="tax_label">' . $tax_message . '</small>';

		                // Remaining item
		                if ( $remaining_item ) {
		                	$remaining_tax_text = ' <small class="tax_label">' . $tax_message . '</small>';
		                }
		            } // END if
	            } // END if
	        } // END if

	        // Insurance amount
	        if ( 'ovabrw_amount_insurance_product' === $meta->key ) {
	        	$display_key = esc_html__( 'Amount Of Insurance', 'ova-brw' );
	        }

	        // Deposit amount
	        if ( 'ovabrw_deposit_amount_product' === $meta->key ) {
	        	$display_key = esc_html__( 'Deposit Amount', 'ova-brw' ).$tax_text;
	        }

	        // Reaming amount
	        if ( 'ovabrw_remaining_amount_product' === $meta->key ) {
	        	$display_key = esc_html__( 'Remaining Amount', 'ova-brw' ).$remaining_tax_text;
	        }

	        // Full amount
	        if ( 'ovabrw_deposit_full_amount' === $meta->key ) {
	        	$display_key = esc_html__( 'Full Amount', 'ova-brw' ).$tax_text;
	        }

			return apply_filters( OVABRW_PREFIX.'order_item_display_meta_key', $display_key, $meta, $item );
		}

		/**
		 * Order item display meta value
		 */
		public function order_item_display_meta_value( $meta_value, $meta, $item ) {
			// Currency
			$currency = '';

			// Get order
			$order = method_exists( $item, 'get_order' ) ? $item->get_order() : '';
			if ( $order ) {
				$currency = method_exists( $order, 'get_currency' ) ? $order->get_currency() : '';
			}

			// Location prices
			if ( 'ovabrw_location_prices' === $meta->key ) { 
	            $meta_value = wc_price( $meta->value, [ 'currency' => $currency ] );
	        }

	        // Insurance amount
			if ( 'ovabrw_amount_insurance_product' === $meta->key ) { 
	            $meta_value = wc_price( $meta->value, [ 'currency' => $currency ] );
	        }

	        // Deposit amount
	        if ( 'ovabrw_deposit_amount_product' === $meta->key ) { 
	            $meta_value = wc_price( $meta->value, [ 'currency' => $currency ] );
	        }

	        // Remaining amount
	        if ( 'ovabrw_remaining_amount_product' === $meta->key ) { 
	            $meta_value = wc_price( $meta->value, [ 'currency' => $currency ] );
	        }

	        // Deposit full amount
	        if ( 'ovabrw_deposit_full_amount' === $meta->key ) { 
	            $meta_value = wc_price( $meta->value, [ 'currency' => $currency ] );
	        }

	        // Remaining balance order id
	        if ( 'ovabrw_remaining_balance_order_id' === $meta->key ) {
	        	if ( $order && method_exists( $order, 'get_view_order_url' ) ) {
	        		$meta_value = '<a href="'.esc_url( $order->get_view_order_url() ).'" title="'.esc_html__( 'View detail', 'ova-brw' ).'">'.$meta_value.'</a>';
	        	}
	        }

			return apply_filters( OVABRW_PREFIX.'order_item_display_meta_value', $meta_value, $meta, $item );
		}

		/**
		 * Hide item meta fields
		 */
		public function hide_item_meta_fields( $meta_data, $item ) {
			// Get product ID
			$product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';
			if ( !$product_id ) return $meta_data;

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return $meta_data;

			// init
			$hide_fields = [
				'rental_type',
	            'ovabrw_price_detail',
	            'ovabrw_total_days',
	            'package_id',
	            'package_type',
	            'define_day',
	            'ovabrw_pickup_date_real',
	            'ovabrw_pickoff_date_real',
	            'ovabrw_pickup_date_strtotime',
	            'ovabrw_pickoff_date_strtotime',
	            'ovabrw_insurance_amount',
	            'ovabrw_remaining_insurance',
	            'ovabrw_remaining_insurance_tax',
	            'ovabrw_insurance_tax',
	            'ovabrw_deposit_type',
	            'ovabrw_deposit_value',
	            'ovabrw_deposit_amount',
	            'ovabrw_remaining_amount',
	            'ovabrw_remaining_tax',
	            'ovabrw_total_payable',
	            'ovabrw_parent_order_id'
			];

			// Pick-up location
			if ( !$rental_product->product->show_location_field() ) {
				$hide_fields[] = 'ovabrw_pickup_loc';
			}

			// Drop-off location
			if ( !$rental_product->product->show_location_field( 'dropoff' ) ) {
				$hide_fields[] = 'ovabrw_pickoff_loc';
			}

			// Drop-off date
			if ( !$rental_product->product->show_date_field( 'dropoff' ) ) {
				$hide_fields[] = 'ovabrw_pickoff_date';
			}

			// Quantity
			if ( !$rental_product->product->show_quantity() ) {
				$hide_fields[] = 'ovabrw_number_vehicle';
				$hide_fields[] = 'id_vehicle';
			}

			// Filter
			$hide_fields = apply_filters( OVABRW_PREFIX.'hide_item_meta_fields', $hide_fields, $meta_data, $item );

			// New meta
			$new_meta = [];

			if ( ovabrw_array_exists( $meta_data ) ) {
				foreach ( $meta_data as $id => $meta ) {
					if ( in_array( $meta->key, $hide_fields ) ) continue;

					// Add new meta
					$new_meta[$id] = $meta;
				} // END loop
			} // END if

			return apply_filters( OVABRW_PREFIX.'item_meta_fields', $new_meta, $meta_data, $item );
		}

		/**
		 * Email order item quantity
		 */
		public function email_order_item_quantity( $item_quantity, $item ) {
			// Get total days
			$total_days = $item->get_meta( 'ovabrw_total_days' );
			if ( $total_days ) {
				return apply_filters( OVABRW_PREFIX.'email_order_item_quantity', $total_days, $item_quantity, $item );
			}

			return $item_quantity;
		}

		/**
		 * Main OVABRW_Booking install
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
}