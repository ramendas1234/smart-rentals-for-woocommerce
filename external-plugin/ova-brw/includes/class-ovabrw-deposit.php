<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Deposit class
 */
if ( !class_exists( 'OVABRW_Deposit' ) ) {

	class OVABRW_Deposit {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Cart item subtotal
            add_filter( 'woocommerce_cart_item_subtotal', [ $this, 'cart_item_subtotal' ], 11, 3 );

            // Cart totals before order total
            add_action( 'woocommerce_cart_totals_before_order_total', [ $this, 'cart_totals_before_order_total' ] );

            // Review order after order total
            add_action( 'woocommerce_review_order_before_order_total', [ $this, 'cart_totals_before_order_total' ] );

            // Cart totals order total html
            add_filter( 'woocommerce_cart_totals_order_total_html', [ $this, 'cart_totals_order_total_payable_html' ] );

            // Checkout order processed - Save order meta fields
            add_action( 'woocommerce_checkout_order_processed', [ $this, 'checkout_order_processed' ], 10, 3 );

            // Checkout order processed - Cart and Checkout Blocks
            add_action( 'woocommerce_store_api_checkout_order_processed', [ $this, 'store_api_checkout_order_processed' ] );

            // Order formatted line subtotal
            add_filter( 'woocommerce_order_formatted_line_subtotal', [ $this, 'order_formatted_line_subtotal' ], 11, 3 );

            // Get order item totals
            add_filter( 'woocommerce_get_order_item_totals', [ $this, 'get_order_item_totals' ], 10, 3 );

            // Admin order item headers
            add_action( 'woocommerce_admin_order_item_headers', [ $this, 'admin_order_item_headers' ] );

            // Admin order item values
            add_action( 'woocommerce_admin_order_item_values', [ $this, 'admin_order_item_values' ], 10, 3 );

            // Admin order totals after tax
            add_action( 'woocommerce_admin_order_totals_after_tax', [ $this, 'admin_order_totals_after_tax' ] );

            // Button pay full and create remaining invoice
            add_action( 'woocommerce_after_order_itemmeta', [ $this, 'after_order_itemmeta' ], 10, 3 );

            // Action pay full and create remaining invoice
            add_action( 'admin_init', [ $this, 'order_item_action' ] );

            // Saved order items
            add_action( 'woocommerce_saved_order_items', [ $this, 'saved_order_items' ], 10, 2 );

            // Hidden order itemmeta
            add_filter( 'woocommerce_hidden_order_itemmeta', [ $this, 'hidden_order_itemmeta' ] );

            // Manager order - Add custom column
            add_filter( 'manage_edit-shop_order_columns', [ $this, 'add_custom_columns' ] );
            add_filter( 'woocommerce_shop_order_list_table_columns', [ $this, 'add_custom_columns' ] );
            add_action( 'manage_shop_order_posts_custom_column', [ $this, 'posts_custom_column' ], 10, 2 );
            add_action( 'manage_woocommerce_page_wc-orders_custom_column', [ $this, 'posts_custom_column' ], 10, 2 );

            // Before saved order items
            add_action( 'woocommerce_before_save_order_item', [ $this, 'before_save_order_item' ] );
		}

		/**
		 * Cart item subtotal
		 */
		public function cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
			// Get product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $cart_item );
			if ( !$product_id ) return $subtotal;

			// Get rental product
			$rental_product = OVABRW()->rental->get_rental_product( $product_id );
			if ( !$rental_product ) return $subtotal;

			// init
			$new_subtotal = $subtotal;

			// Item quantity
			$quantity = (int)ovabrw_get_meta_data( 'ovabrw_quantity', $cart_item );

			// Pick-up date
			$pickup_date = strtotime( ovabrw_get_meta_data( 'pickup_date', $cart_item ) );

			// Drop-off date
			$dropoff_date = strtotime( ovabrw_get_meta_data( 'dropoff_date', $cart_item ) );

			// Custom checkout fields
			$cckf = ovabrw_get_meta_data( 'cckf', $cart_item, [] );

			// Custom checkout fields quantity
			$cckf_qty = ovabrw_get_meta_data( 'cckf_qty', $cart_item, [] );

			// HTML cckf
			$html_cckf = OVABRW()->options->get_html_cckf( $cckf, $cckf_qty, [
				'product_id' 	=> $product_id,
				'quantity' 		=> $quantity
			]);

			// Resources
			$resources = ovabrw_get_meta_data( 'resources', $cart_item, [] );

			// Resources quantity
			$resources_qty = ovabrw_get_meta_data( 'resources_qty', $cart_item, [] );

			// HTML resources
			$html_resources = OVABRW()->options->get_html_resources( $resources, $resources_qty, [
				'product_id' 	=> $product_id,
				'pickup_date' 	=> $pickup_date,
				'dropoff_date' 	=> $dropoff_date,
				'quantity' 		=> $quantity
			]);

			// Services
			$services = ovabrw_get_meta_data( 'services', $cart_item, [] );

			// Services quantity
			$services_qty = ovabrw_get_meta_data( 'services_qty', $cart_item, [] );

			// HTML services
			$html_services = OVABRW()->options->get_html_services( $services, $services_qty, [
				'product_id' 	=> $product_id,
				'pickup_date' 	=> $pickup_date,
				'dropoff_date' 	=> $dropoff_date,
				'quantity' 		=> $quantity
			]);

			// Get HTML extra prices
            $html_extra = OVABRW()->options->get_html_extra( $html_cckf, $html_resources, $html_services );

            // is deposit
            $is_deposit = $cart_item['data']->get_meta( 'is_deposit' );
            if ( $is_deposit ) {
            	// Get total payable
                $total_payable = (float)$cart_item['data']->get_meta( 'total_payable' );
                if ( $total_payable ) {
                	// Deposit type
                	$deposit_type = $cart_item['data']->get_meta( 'deposit_type' );

                	// Depost value
                	$deposit_value = $cart_item['data']->get_meta( 'deposit_value' );

                	// Taxable
                    if ( $cart_item['data']->is_taxable() ) {
                        if ( WC()->cart->display_prices_including_tax() ) {
                            if ( ! wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                                $total_payable = wc_get_price_including_tax( $cart_item['data'], [ 'price' => $total_payable ] );
                            }
                        } else {
                            if ( wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                                $total_payable = wc_get_price_excluding_tax( $cart_item['data'], [ 'price' => $total_payable ] );
                            }
                        }
                    }

                    if ( 'percent' === $deposit_type ) {
                        $new_subtotal .= ' <small class="tax_label">' . sprintf( esc_html__( '(%s%% deposit of %s)', 'ova-brw' ), $deposit_value, ovabrw_wc_price( $total_payable, [], false ) ) . '</small>';
                    } else {
                        $new_subtotal .= ' <small class="tax_label">' . sprintf( esc_html__( '(deposit of %s)', 'ova-brw' ), ovabrw_wc_price( $total_payable, [], false ) ) . '</small>';
                    }
                } // END if

                // HMTL extra
                if ( $html_extra ) $new_subtotal .= $html_extra;

                $new_subtotal .= '<dl class="variation">';

                // Deposit
                $new_subtotal .= $this->get_cart_item_subdeposit( $cart_item );

                // Remaining
                $new_subtotal .= $this->get_cart_item_subremaining( $cart_item );

                // Total payable
                $new_subtotal .= $this->get_cart_item_subtotal_payable( $cart_item );

                $new_subtotal .= '</dl>';
            } else {
            	// HMTL extra
                if ( $html_extra ) $new_subtotal .= $html_extra;

                // Insurance
                $insurance_amount = (float)$cart_item['data']->get_meta( 'insurance_amount' );

                // View insurance
                if ( $insurance_amount ) {
                    $new_subtotal .= '<dl class="variation">';
                    $new_subtotal .= '<dt>'.esc_html__( 'Insurance Fee:', 'ova-brw' ).'</dt>';
                    $new_subtotal .= '<dd>'.ovabrw_wc_price( OVABRW()->options->get_insurance_inclusive_tax( $insurance_amount ), [], false ).'</dd>';
                    $new_subtotal .= '</dl>';
                }
            }// END if

			return apply_filters( OVABRW_PREFIX.'cart_item_subtotal', $new_subtotal, $subtotal, $cart_item, $cart_item_key, $this );
		}

		/**
		 * Get cart item sub-deposit
		 */
		public function get_cart_item_subdeposit( $cart_item ) {
			// init
			$subdeposit = '';

			// Get deposit amount
			$deposit_amount = (float)$cart_item['data']->get_meta( 'deposit_amount' );
			if ( $deposit_amount ) {
				// Insurance string
                $insurance_string = '';

                // Tax string
                $tax_string = '';

                // Deposit HTML
                $subdeposit .= '<dt>'.esc_html__( 'Deposit:', 'ova-brw' ).'</dt>';
                $subdeposit .= '<dd>';

                // Convert price
                $deposit_price = ovabrw_wc_price( $deposit_amount, [], false );

                // Get insurance amount
                $insurance_amount = (float)$cart_item['data']->get_meta( 'insurance_amount' );
                if ( $insurance_amount ) {
                    $insurance_amount   = OVABRW()->options->get_insurance_inclusive_tax( $insurance_amount );
                    $deposit_price      = ovabrw_wc_price( $deposit_amount + $insurance_amount, [], false );
                    $insurance_string   = ' <small class="tax_label">' . sprintf( esc_html__( '(incl. %s insurance fee)', 'ova-brw' ), ovabrw_wc_price( $insurance_amount, [], false ) ) . '</small>';
                }

                // Taxable
                if ( $cart_item['data']->is_taxable() ) {
                    if ( WC()->cart->display_prices_including_tax() ) {
                        if ( ! wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                            $row_price = wc_get_price_including_tax( $cart_item['data'], [ 'price' => $deposit_amount ] );
                            $row_price += $insurance_amount;

                            $deposit_price  = ovabrw_wc_price( $row_price, [], false );
                            $tax_string     = ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                        }
                    } else {
                        if ( wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                            $row_price = wc_get_price_excluding_tax( $cart_item['data'], [ 'price' => $deposit_amount ] );
                            $row_price += $insurance_amount;

                            $deposit_price  = ovabrw_wc_price( $row_price, [], false );
                            $tax_string     = ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                        }
                    }
                }

                $subdeposit .= $deposit_price.$insurance_string.$tax_string;
                $subdeposit .= '</dd>';
			}

			return apply_filters( OVABRW_PREFIX.'get_cart_item_subdeposit', $subdeposit, $cart_item );
		}

		/**
		 * Get cart item sub-remaining
		 */
		public function get_cart_item_subremaining( $cart_item ) {
			// init
			$subremaining = '';

			// Get remaining amount
			$remaining_amount = (float)$cart_item['data']->get_meta( 'remaining_amount' );
			if ( $remaining_amount ) {
                // Insurance string
                $insurance_string = '';

                // Tax string
                $tax_string = '';

                // Remaining HTML
                $subremaining .= '<dt>'.esc_html__( 'Remaining:', 'ova-brw' ).'</dt>';
                $subremaining .= '<dd>';

                // Convert price
                $remaining_price = ovabrw_wc_price( $remaining_amount, [], false );

                // Get insurance amount
                $insurance_amount = floatval( $cart_item['data']->get_meta( 'remaining_insurance' ) );
                if ( $insurance_amount ) {
                    $insurance_amount   = OVABRW()->options->get_insurance_inclusive_tax( $insurance_amount );
                    $remaining_price    = ovabrw_wc_price( $remaining_amount + $insurance_amount, [], false );
                    $insurance_string   = ' <small class="tax_label">' . sprintf( esc_html__( '(incl. %s insurance fee)', 'ova-brw' ), ovabrw_wc_price( $insurance_amount, [], false ) ) . '</small>';
                }

                // Taxable
                if ( $cart_item['data']->is_taxable() ) {
                    if ( WC()->cart->display_prices_including_tax() ) {
                        if ( ! wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                            $row_price = wc_get_price_including_tax( $cart_item['data'], [ 'price' => $remaining_amount ] );

                            $row_price          += $insurance_amount;
                            $remaining_price    = ovabrw_wc_price( $row_price, [], false );
                            $tax_string         = ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                        }
                    } else {
                        if ( wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                            $row_price = wc_get_price_excluding_tax( $cart_item['data'], [ 'price' => $remaining_amount ] );

                            $row_price          += $insurance_amount;
                            $remaining_price    = ovabrw_wc_price( $row_price, [], false );
                            $tax_string         = ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                        }
                    }
                }

                $subremaining .= $remaining_price.$insurance_string.$tax_string;
                $subremaining .= '</dd>';
            }

            return apply_filters( OVABRW_PREFIX.'get_cart_item_subremaining', $subremaining, $cart_item );
		}

		/**
		 * Get cart item subtotal payable
		 */
		public function get_cart_item_subtotal_payable( $cart_item ) {
			// init
			$subtotal_payable = '';

            // Deposit amount
            $deposit_amount = (float)$cart_item['data']->get_meta( 'deposit_amount' );

            // Remaning amount
            $remaining_amount = (float)$cart_item['data']->get_meta( 'remaining_amount' );

            if ( $deposit_amount || $remaining_amount ) {
                // Get insurance amount
                $insurance_amount = (float)$cart_item['data']->get_meta( 'insurance_amount' );
                if ( $insurance_amount ) {
                    $insurance_amount = OVABRW()->options->get_insurance_inclusive_tax( $insurance_amount );
                }

                // Get remaining insurance amount
                $remaining_insurance = (float)$cart_item['data']->get_meta( 'remaining_insurance' );
                if ( $remaining_insurance ) {
                    $remaining_insurance = OVABRW()->options->get_insurance_inclusive_tax( $remaining_insurance );
                }

                // Payable HTML
                $subtotal_payable .= '<dt>'.esc_html__( 'Total payable:', 'ova-brw' ).'</dt>';
                $subtotal_payable .= '<dd>';

                // Convert price
                $total_payable = ovabrw_wc_price( $deposit_amount + $remaining_amount + $insurance_amount + $remaining_insurance, [], false );

                // Taxable
                $tax_string = '';

                if ( $cart_item['data']->is_taxable() ) {
                    if ( WC()->cart->display_prices_including_tax() ) {
                        if ( !wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                            $row_deposit    = round( wc_get_price_including_tax( $cart_item['data'], [ 'price' => $deposit_amount ] ), wc_get_price_decimals() );
                            $row_remaining  = round( wc_get_price_including_tax( $cart_item['data'], [ 'price' => $remaining_amount ] ), wc_get_price_decimals() );
                            $row_price      = $row_deposit + $row_remaining + $insurance_amount + $remaining_insurance;
                            $total_payable  = ovabrw_wc_price( $row_price, [], false );
                            $tax_string     = ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                        }
                    } else {
                        if ( wc_prices_include_tax() && WC()->cart->get_subtotal_tax() > 0 ) {
                            $row_deposit    = round( wc_get_price_excluding_tax( $cart_item['data'], [ 'price' => $deposit_amount ] ), wc_get_price_decimals() );
                            $row_remaining  = round( wc_get_price_excluding_tax( $cart_item['data'],  [ 'price' => $remaining_amount ] ), wc_get_price_decimals() );
                            $row_price      = $row_deposit + $row_remaining + $insurance_amount + $remaining_insurance;
                            $total_payable  = ovabrw_wc_price( $row_price, [], false );
                            $tax_string     = ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                        }
                    }
                }

                $subtotal_payable .= $total_payable.$tax_string;
                $subtotal_payable .= '</dd>';
            }

            return apply_filters( OVABRW_PREFIX.'get_cart_item_subtotal_payable', $subtotal_payable, $deposit_amount, $remaining_amount );
		}

        /**
         * Cart totals before order total
         */
        public function cart_totals_before_order_total() {
            // Has deposit
            $has_deposit = isset( WC()->cart->deposit_info[ 'has_deposit' ] ) ? WC()->cart->deposit_info[ 'has_deposit' ] : false;
            if ( $has_deposit ) {
                $deposit_amount     = isset( WC()->cart->deposit_info[ 'deposit_amount' ] ) ? (float)WC()->cart->deposit_info[ 'deposit_amount' ] : 0;
                $remaining_amount   = isset( WC()->cart->deposit_info[ 'remaining_amount' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_amount' ] : 0;
                $remaining_tax      = isset( WC()->cart->deposit_info[ 'remaining_tax' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_tax' ] : 0;
                $remaining_insurance_amount = isset( WC()->cart->deposit_info[ 'remaining_insurance' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_insurance' ] : 0;
                $remaining_insurance_tax    = isset( WC()->cart->deposit_info[ 'remaining_insurance_tax' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_insurance_tax' ] : 0;
            ?>
                <tr class="order-paid">
                    <th><?php esc_html_e( 'Deposit','ova-brw' ); ?></th>
                    <td data-title="<?php esc_html_e( 'Deposit','ova-brw' ); ?>">
                        <?php echo wp_kses_post( $this->get_cart_total_deposit_html() ); ?>
                    </td>
                </tr>
                <?php if ( $remaining_amount ): ?>
                    <tr class="order-remaining">
                        <th><?php esc_html_e( 'Remaining','ova-brw' ); ?></th>
                        <td data-title="<?php esc_html_e( 'Remaining','ova-brw' ); ?>">
                            <?php echo wp_kses_post( $this->get_cart_total_remaining_html( $remaining_amount, $remaining_tax, $remaining_insurance_amount, $remaining_insurance_tax ) ); ?>
                        </td>
                    </tr>
                <?php endif;
            }
        }

        /**
         * Get cart totals deposit HTML
         */
        public function get_cart_total_deposit_html() {
            // Total deposit amount
            $total_deposit = '<strong>' . WC()->cart->get_total() . '</strong> ';

            // Tax enabled
            if ( wc_tax_enabled() && WC()->cart->display_prices_including_tax() ) {
                $tax_string = [];
                $tax_totals = WC()->cart->get_tax_totals();

                if ( ovabrw_array_exists( $tax_totals ) ) {
                    if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
                        foreach ( $tax_totals as $code => $tax ) {
                            $tax_string[] = sprintf( '%s %s', $tax->formatted_amount, $tax->label );
                        }
                    } else {
                        $tax_string[] = sprintf( '%s %s', wc_price( WC()->cart->get_taxes_total( true, true ) ), WC()->countries->tax_or_vat() );
                    }
                }

                // Tax string array
                if ( ovabrw_array_exists( $tax_string ) ) {
                    // Get taxable address
                    $taxable_address = WC()->customer->get_taxable_address();

                    if ( WC()->customer->is_customer_outside_base() && !WC()->customer->has_calculated_shipping() ) {
                        $country = WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ];
                        $tax_text = wp_kses_post( sprintf( __( '(includes %1$s estimated for %2$s)', 'ova-brw' ), implode( ', ', $tax_string ), $country ) );
                    } else {
                        $tax_text = wp_kses_post( sprintf( __( '(includes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) );
                    }

                    $total_deposit .= '<small class="includes_tax">' . $tax_text . '</small>';
                }
            }

            return apply_filters( OVABRW_PREFIX.'get_cart_total_deposit_html', $total_deposit );
        }

        /**
         * Get cart total remaining HTML
         */
        public function get_cart_total_remaining_html( $remaining_amount, $remaining_tax, $insurance_amount, $insurance_tax ) {
            // Total remaining
            $total_remaining = '';

            // Insurance string
            $insurance_string = '';

            // Insurance amount
            if ( $insurance_amount ) {
                $remaining_amount += floatval( $insurance_amount );
                $insurance_string = ' <small class="includes_tax">';

                if ( WC()->cart->display_prices_including_tax() ) {
                    if ( OVABRW()->options->enable_insurance_tax() && $insurance_tax ) {
                        $insurance_amount += $insurance_tax;
                    }
                    $insurance_string .= sprintf( __( '(includes %s insurance fee)', 'ova-brw' ), ovabrw_wc_price( $insurance_amount, [], false ) );
                } else {
                    $insurance_string .= sprintf( __( '(includes %s insurance fee)', 'ova-brw' ), ovabrw_wc_price( $insurance_amount, [], false ) );
                }

                $insurance_string .= '</small>';
            }

            // Taxable
            $tax_text = '';
            if ( wc_tax_enabled() && $remaining_tax ) {
                // Insurance tax amount
                if ( OVABRW()->options->enable_insurance_tax() && $insurance_tax ) {
                    $remaining_tax += floatval( $insurance_tax );
                }

                // Tax string
                $tax_string = sprintf( '%s %s', wc_price( $remaining_tax ), WC()->countries->tax_or_vat() );

                if ( wc_prices_include_tax() ) {
                    // Insurance tax amount
                    if ( OVABRW()->options->enable_insurance_tax() && $insurance_tax ) {
                        $remaining_amount += floatval( $insurance_tax );
                    }

                    if ( WC()->cart->display_prices_including_tax() ) {
                        $total_remaining .= '<strong>'.ovabrw_wc_price( $remaining_amount, [], false ).'</strong>';

                        // Tax text
                        $tax_text = wp_kses_post( sprintf( __( '(includes %s)', 'ova-brw' ), $tax_string ) );
                    } else {
                        $remaining_amount   -= $remaining_tax;
                        $total_remaining    .= '<strong>'.ovabrw_wc_price( $remaining_amount, [], false ).'</strong>';

                        // Tax text
                        $tax_text = wp_kses_post( sprintf( __( '(excludes %s)', 'ova-brw' ), $tax_string ) );
                    }
                } else {
                    if ( WC()->cart->display_prices_including_tax() ) {
                        $remaining_amount   += $remaining_tax;
                        $total_remaining    .= '<strong>'.ovabrw_wc_price( $remaining_amount, [], false ).'</strong>';

                        // Tax text
                        $tax_text = wp_kses_post( sprintf( __( '(includes %s)', 'ova-brw' ), $tax_string ) );
                    } else {
                        $total_remaining .= '<strong>'.ovabrw_wc_price( $remaining_amount, [], false ).'</strong>';

                        // Tax text
                        $tax_text = wp_kses_post( sprintf( __( '(excludes %s)', 'ova-brw' ), $tax_string ) );
                    }
                }
            } else {
                $total_remaining .= '<strong>'.ovabrw_wc_price( $remaining_amount, [], false ).'</strong>';
            }

            $total_remaining .= $insurance_string;
            if ( $tax_text ) $total_remaining .= ' <small class="includes_tax">' . $tax_text . '</small>';
            
            return apply_filters( OVABRW_PREFIX.'get_cart_total_remaining_html', $total_remaining, $remaining_amount, $remaining_tax, $insurance_amount, $insurance_tax );
        }

        /**
         * Cart totals order total payable html
         */
        public function cart_totals_order_total_payable_html( $total_payable ) {
            // Get has deposit
            $has_deposit = isset( WC()->cart->deposit_info[ 'has_deposit' ] ) ? WC()->cart->deposit_info[ 'has_deposit' ] : false;

            if ( $has_deposit ) {
                // Order total
                $order_totals   = WC()->cart->get_totals();
                $order_total    = isset( $order_totals['total'] ) ? round( $order_totals['total'], wc_get_price_decimals() ) : 0;

                // Remaining
                $remaining_amount   = isset( WC()->cart->deposit_info[ 'remaining_amount' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_amount' ] : 0;
                $remaining_amount   = ovabrw_convert_price( $remaining_amount, [], false );
                $order_total        += $remaining_amount;

                // Remaining insurance
                $remaining_insurance = isset( WC()->cart->deposit_info[ 'remaining_insurance' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_insurance' ] : 0;
                $remaining_insurance = ovabrw_convert_price( $remaining_insurance, [], false );
                $order_total         += $remaining_insurance;

                if ( wc_tax_enabled() ) {
                    // Total tax
                    $total_tax = isset( $order_totals['total_tax'] ) ? round( $order_totals['total_tax'], wc_get_price_decimals() ) : 0;

                    // Get remaining tax amount
                    $remaining_tax = isset( WC()->cart->deposit_info[ 'remaining_tax' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_tax' ] : 0;

                    // Get remaining insurance tax amount
                    $remaining_insurance_tax = isset( WC()->cart->deposit_info[ 'remaining_insurance_tax' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_insurance_tax' ] : 0;
                    if ( OVABRW()->options->enable_insurance_tax() && $remaining_insurance_tax ) {
                        $remaining_tax += $remaining_insurance_tax;
                    }

                    // Prices include tax
                    if ( wc_prices_include_tax() ) {
                        // Remaining insurance tax amount
                        if ( OVABRW()->options->enable_insurance_tax() && $remaining_insurance_tax ) {
                            $order_total += $remaining_insurance_tax;
                        }

                        // Display prices including tax
                        if ( WC()->cart->display_prices_including_tax() ) {
                            // Total tax
                            $total_tax += $remaining_tax;

                            // Total payable
                            $total_payable = '<strong>' . wc_price( $order_total ) . '</strong> ';

                            // Tax string
                            $tax_string = sprintf( '%s %s', wc_price( $total_tax ), WC()->countries->tax_or_vat() );
                            $tax_text   = wp_kses_post( sprintf( __( '(includes %s)', 'ova-brw' ), $tax_string ) );

                            // Add tax string
                            $total_payable .= ' <small class="includes_tax">' . $tax_text . '</small>';
                        } else {
                            // Order tatal
                            $order_total -= $remaining_tax;

                            // Total payable
                            $total_payable = '<strong>'.wc_price( $order_total ).'</strong>';

                            // Tax string
                            $tax_string = sprintf( '%s %s', wc_price( $remaining_tax ), WC()->countries->tax_or_vat() );
                            $tax_text   = wp_kses_post( sprintf( __( '(excludes %s)', 'ova-brw' ), $tax_string ) );

                            // Add tax string
                            $total_payable .= ' <small class="includes_tax">' . $tax_text . '</small>';
                        }
                    } else {
                        if ( WC()->cart->display_prices_including_tax() ) {
                            // Order total
                            $order_total += $remaining_tax;

                            // Total tax
                            $total_tax += $remaining_tax;

                            // Total payable
                            $total_payable = '<strong>'.wc_price( $order_total ).'</strong>';

                            // Tax string
                            $tax_string = sprintf( '%s %s', wc_price( $total_tax ), WC()->countries->tax_or_vat() );
                            $tax_text   = wp_kses_post( sprintf( __( '(includes %s)', 'ova-brw' ), $tax_string ) );

                            // Add tax string
                            $total_payable .= ' <small class="includes_tax">' . $tax_text . '</small>';
                        } else {
                            // Total payable
                            $total_payable = '<strong>'.wc_price( $order_total ).'</strong>';

                            // Tax string
                            $tax_string = sprintf( '%s %s', wc_price( $remaining_tax ), WC()->countries->tax_or_vat() );
                            $tax_text   = wp_kses_post( sprintf( __( '(excludes %s)', 'ova-brw' ), $tax_string ) );

                            // Total payable
                            $total_payable .= ' <small class="includes_tax">' . $tax_text . '</small>';
                        }
                    }
                } else {
                    $total_payable = '<strong>' . ovabrw_wc_price( $order_total, [], false ) . '</strong> ';
                }
            }

            return apply_filters( OVABRW_PREFIX.'cart_totals_order_total_payable_html', $total_payable );
        }

        /**
         * Checkout order processed - Save order meta fields
         */
        public function checkout_order_processed( $order_id, $posted_data, $order ) {
            // Has deposit
            $has_deposit = isset( WC()->cart->deposit_info[ 'has_deposit' ] ) ? WC()->cart->deposit_info[ 'has_deposit' ] : '';
            if ( $has_deposit ) {
                // Deposit amount
                $deposit_amount = isset( WC()->cart->deposit_info['deposit_amount'] ) ? (float)WC()->cart->deposit_info['deposit_amount'] : 0;

                // Remaining amount
                $remaining_amount = isset( WC()->cart->deposit_info['remaining_amount'] ) ? (float)WC()->cart->deposit_info['remaining_amount'] : 0;

                // Remaining tax amount
                $remaining_tax = isset( WC()->cart->deposit_info[ 'remaining_tax' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_tax' ] : 0;

                // Remaining insurance amount
                $remaining_insurance = isset( WC()->cart->deposit_info[ 'remaining_insurance' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_insurance' ] : 0;

                // Remaining insurance tax amount
                $remaining_insurance_tax = isset( WC()->cart->deposit_info[ 'remaining_insurance_tax' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_insurance_tax' ] : 0;

                // Prices include tax
                if ( wc_prices_include_tax() ) {
                    $order->add_meta_data( '_ova_prices_include_tax', 1, true );
                }

                // Add has deposit
                $order->add_meta_data( '_ova_has_deposit', 1, true );
                
                // Add deposit amount
                if ( $deposit_amount ) {
                    $order->add_meta_data( '_ova_deposit_amount', ovabrw_convert_price( $deposit_amount, [], false ), true );
                }

                // Add remaining amount
                if ( $remaining_amount ) {
                    $order->add_meta_data( '_ova_remaining_amount', ovabrw_convert_price( $remaining_amount, [], false ), true );
                }

                // Add remaining tax amount
                if ( $remaining_tax ) {
                    $order->add_meta_data( '_ova_remaining_tax', $remaining_tax, true );
                }

                // Add remaining insurance amount
                if ( $remaining_insurance ) {
                    $order->add_meta_data( '_ova_remaining_insurance', $remaining_insurance, true );
                }

                // Add remaining insurance tax
                if ( $remaining_insurance_tax ) {
                    $order->add_meta_data( '_ova_remaining_insurance_tax', $remaining_insurance_tax, true );
                }
            } else {
                // Delete meta data
                $order->delete_meta_data( '_ova_has_deposit' );
                $order->delete_meta_data( '_ova_deposit_amount' );
                $order->delete_meta_data( '_ova_remaining_amount' );
                $order->delete_meta_data( '_ova_remaining_tax' );
                $order->delete_meta_data( '_ova_remaining_insurance' );
                $order->delete_meta_data( '_ova_remaining_insurance_tax' );
            }

            // Insurance amount
            $insurance_amount = isset( WC()->cart->deposit_info[ 'insurance_amount' ] ) ? (float)WC()->cart->deposit_info[ 'insurance_amount' ] : 0;

            // Insurance tax amount
            $insurance_tax = isset( WC()->cart->deposit_info[ 'insurance_tax' ] ) ? (float)WC()->cart->deposit_info[ 'insurance_tax' ] : 0;

            if ( $insurance_amount ) {
                // Add insurance amount
                $order->add_meta_data( '_ova_insurance_amount', ovabrw_convert_price( $insurance_amount, [], false ), true );

                // Add insurance tax amount
                if ( $insurance_tax ) {
                    $order->add_meta_data( '_ova_insurance_tax', $insurance_tax, true );
                }
            } else {
                $order->delete_meta_data( '_ova_insurance_amount' );
                $order->delete_meta_data( '_ova_insurance_tax' );
            }

            // Save order
            $order->save();
        }

        /**
         * Checkout order processed - Cart and Checkout Blocks
         */
        public function store_api_checkout_order_processed( $order ) {
            // Reserve stock
            OVABRW()->booking->reserve_stock_for_order( $order );

            // Has deposit
            $has_deposit = isset( WC()->cart->deposit_info[ 'has_deposit' ] ) ? WC()->cart->deposit_info[ 'has_deposit' ] : '';

            if ( $has_deposit ) {
                // Deposit amount
                $deposit_amount = isset( WC()->cart->deposit_info['deposit_amount'] ) ? (float)WC()->cart->deposit_info['deposit_amount'] : 0;

                // Remaining amount
                $remaining_amount = isset( WC()->cart->deposit_info['remaining_amount'] ) ? (float)WC()->cart->deposit_info['remaining_amount'] : 0;

                // Remaining tax amount
                $remaining_tax = isset( WC()->cart->deposit_info[ 'remaining_tax' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_tax' ] : 0;

                // Remaining insurance amount
                $remaining_insurance = isset( WC()->cart->deposit_info[ 'remaining_insurance' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_insurance' ] : 0;

                // Remaining insurance tax amount
                $remaining_insurance_tax = isset( WC()->cart->deposit_info[ 'remaining_insurance_tax' ] ) ? (float)WC()->cart->deposit_info[ 'remaining_insurance_tax' ] : 0;

                // Prices include tax
                if ( wc_prices_include_tax() ) {
                    $order->add_meta_data( '_ova_prices_include_tax', 1, true );
                }

                // Add has deposit
                $order->add_meta_data( '_ova_has_deposit', 1, true );
                    
                // Add deposit amount
                if ( $deposit_amount ) {
                    $order->add_meta_data( '_ova_deposit_amount', ovabrw_convert_price( $deposit_amount, [], false ), true );
                }

                // Add remaining amount
                if ( $remaining_amount ) {
                    $order->add_meta_data( '_ova_remaining_amount', ovabrw_convert_price( $remaining_amount, [], false ), true );
                }

                // Add remaining tax amount
                if ( $remaining_tax ) {
                    $order->add_meta_data( '_ova_remaining_tax', $remaining_tax, true );
                }

                // Add remaining insurance amount
                if ( $remaining_insurance ) {
                    $order->add_meta_data( '_ova_remaining_insurance', $remaining_insurance, true );
                }

                // Add remaining insurance tax amount
                if ( $remaining_insurance_tax ) {
                    $order->add_meta_data( '_ova_remaining_insurance_tax', $remaining_insurance_tax, true );
                }
            } else {
                // Delete meta data
                $order->delete_meta_data( '_ova_has_deposit' );
                $order->delete_meta_data( '_ova_deposit_amount' );
                $order->delete_meta_data( '_ova_remaining_amount' );
                $order->delete_meta_data( '_ova_remaining_tax' );
                $order->delete_meta_data( '_ova_remaining_insurance' );
                $order->delete_meta_data( '_ova_remaining_insurance_tax' );
            }

            // Insurance amount
            $insurance_amount = isset( WC()->cart->deposit_info[ 'insurance_amount' ] ) ? (float)WC()->cart->deposit_info[ 'insurance_amount' ] : 0;

            // Insurance tax amount
            $insurance_tax = isset( WC()->cart->deposit_info[ 'insurance_tax' ] ) ? (float)WC()->cart->deposit_info[ 'insurance_tax' ] : 0;

            // Add insurance amount
            if ( $insurance_amount ) {
                $order->add_meta_data( '_ova_insurance_amount', ovabrw_convert_price( $insurance_amount, [], false ), true );

                // Add insurance tax
                if ( $insurance_tax ) {
                    $order->add_meta_data( '_ova_insurance_tax', $insurance_tax, true );
                }
            } else {
                $order->delete_meta_data( '_ova_insurance_amount' );
                $order->delete_meta_data( '_ova_insurance_tax' );
            }

            // Save
            $order->save();
        }

        /**
         * Order formatted line subtotal
         */
        public function order_formatted_line_subtotal( $subtotal, $item, $order ) {
            // init
            $new_subtotal = $subtotal;

            // Order ID
            $order_id = $order->get_id();

            // Product ID
            $product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';

            // Pick-up date
            $pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );

            // Drop-off date
            $dropoff_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );

            // Quantity
            $quantity = (int)$item->get_meta( 'ovabrw_number_vehicle' );

            // Custom checkout fields HTML
            $html_cckf = '';

            // Get item cckf data
            $cckf     = $item->get_meta( 'ovabrw_custom_ckf' );
            $cckf_qty = $item->get_meta( 'ovabrw_custom_ckf_qty' );

            // Get custom checkout fields html
            if ( ovabrw_array_exists( $cckf ) ) {
                $html_cckf = OVABRW()->options->get_html_cckf( $cckf, $cckf_qty, [
                    'product_id'    => $product_id,
                    'quantity'      => $quantity,
                    'order_id'      => $order_id
                ]);
            }

            // HTML resources
            $html_resources = '';

            // Get item resources data
            $resources      = $item->get_meta( 'ovabrw_resources' );
            $resources_qty  = $item->get_meta( 'ovabrw_resources_qty' );

            // Get resources HTML
            if ( ovabrw_array_exists( $resources ) ) {
                $html_resources = OVABRW()->options->get_html_resources( $resources, $resources_qty, [
                    'product_id'    => $product_id,
                    'pickup_date'   => $pickup_date,
                    'dropoff_date'  => $dropoff_date,
                    'quantity'      => $quantity,
                    'order_id'      => $order_id
                ]);
            }

            // Services HTML
            $html_services = '';

            // Get item services data
            $services       = $item->get_meta( 'ovabrw_services' );
            $services_qty   = $item->get_meta( 'ovabrw_services_qty' );

            // Get services html
            if ( ovabrw_array_exists( $services ) ) {
                $html_services = OVABRW()->options->get_html_services( $services, $services_qty, [
                    'product_id'    => $product_id,
                    'pickup_date'   => $pickup_date,
                    'dropoff_date'  => $dropoff_date,
                    'quantity'      => $quantity,
                    'order_id'      => $order_id
                ]);
            }

            // Check exist resource_html and service_html
            $extra_html = OVABRW()->options->get_html_extra( $html_cckf, $html_resources, $html_services );

            // Is deposit
            $is_deposit = $order->get_meta( '_ova_has_deposit' );
            if ( $is_deposit ) {
                // Get total payable
                $total_payable = (float)$item->get_meta( 'ovabrw_total_payable' );
                
                if ( $total_payable ) {
                    $deposit_type   = $item->get_meta( 'ovabrw_deposit_type' );
                    $deposit_value  = $item->get_meta( 'ovabrw_deposit_value' );

                    // Tax enabled
                    if ( wc_tax_enabled() ) {
                        $prices_incl_tax    = $order->get_meta( '_ova_prices_include_tax' );
                        $tax_display        = get_option( 'woocommerce_tax_display_cart' );
                        $rates              = WC_Tax::get_rates( $item->get_tax_class() );

                        if ( $prices_incl_tax ) {
                            if ( 'excl' === $tax_display ) {
                                $incl_tax   = WC_Tax::calc_inclusive_tax( $total_payable, $rates );
                                $tax        = round( array_sum( $incl_tax ), wc_get_price_decimals() );

                                // Update total payable
                                $total_payable -= $tax;
                            }
                        } else {
                            if ( 'incl' === $tax_display ) {
                                $excl_tax   = WC_Tax::calc_exclusive_tax( $total_payable, $rates );
                                $tax        = round( array_sum( $excl_tax ), wc_get_price_decimals() );

                                // Update total payable
                                $total_payable += $tax;
                            }
                        }
                    }

                    // Deposit type
                    if ( 'percent' === $deposit_type ) {
                        $new_subtotal .= ' <small class="tax_label">' . sprintf( esc_html__( '(%s%% deposit of %s)', 'ova-brw' ), $deposit_value, wc_price( $total_payable, [ 'currency' => $order->get_currency() ] ) ) . '</small>';
                    } else {
                        $new_subtotal .= ' <small class="tax_label">' . sprintf( esc_html__( '(deposit of %s)', 'ova-brw' ), wc_price( $total_payable, [ 'currency' => $order->get_currency() ] ) ) . '</small>';
                    }
                }

                // Extra HTML
                if ( $extra_html ) $new_subtotal .= $extra_html;

                $new_subtotal .= '<dl class="variation">';
                
                // Deposit
                $new_subtotal .= $this->get_order_formatted_line_subdeposit( $item, $order );

                // Remaining
                $new_subtotal .= $this->get_order_formatted_line_subremaining( $item, $order );

                // Total payable
                $new_subtotal .= $this->get_order_formatted_line_subtotal_payable( $item, $order );

                $new_subtotal .= '</dl>';
            } else {
                // Extra HTML
                if ( $extra_html ) $new_subtotal .= $extra_html;
                
                // Insurance amount
                $insurance_amount = (float)$item->get_meta( 'ovabrw_insurance_amount' );
                if ( $insurance_amount ) {
                    $new_subtotal .= '<dl>';
                    $new_subtotal .= '<dt>'.esc_html__( 'Insurance fee:', 'ova-brw' ).'</dt>';
                    $new_subtotal .= '<dd>'.wc_price( OVABRW()->options->get_insurance_inclusive_tax( $insurance_amount ), [ 'currency' => $order->get_currency() ] ).'</dd>';
                    $new_subtotal .= '</dl>';
                }
            }

            return apply_filters( OVABRW_PREFIX.'order_show_subtotal', $new_subtotal, $subtotal, $item, $order );
        }

        /**
         * Get order formatted line sub-deposit amount
         */
        public function get_order_formatted_line_subdeposit( $item, $order ) {
            // init
            $subdeposit = '';

            // Deposit amount
            $deposit_amount = (float)$item->get_meta( 'ovabrw_deposit_amount' );
            if ( $deposit_amount ) {
                // Insurance string
                $insurance_string = '';

                // Tax string
                $tax_string = '';

                // Deposit HTML
                $subdeposit .= '<dt>'.esc_html__( 'Deposit:', 'ova-brw' ).'</dt>';
                $subdeposit .= '<dd>';

                // Convert price
                $deposit_price = wc_price( $deposit_amount, [
                    'currency' => $order->get_currency()
                ]);

                // Get insurance amount
                $insurance_amount = (float)$item->get_meta( 'ovabrw_insurance_amount' );
                if ( $insurance_amount ) {
                    // Insurance include tax
                    $insurance_amount = OVABRW()->options->get_insurance_inclusive_tax( $insurance_amount );

                    // Deposit price
                    $deposit_price = wc_price( $deposit_amount + $insurance_amount, [
                        'currency' => $order->get_currency()
                    ]);

                    // Insurance string
                    $insurance_string = ' <small class="tax_label">' . sprintf( esc_html__( '(incl. %s insurance fee)', 'ova-brw' ), wc_price( $insurance_amount, [ 'currency' => $order->get_currency() ] ) ) . '</small>';
                }

                // Tax anabled
                if ( wc_tax_enabled() ) {
                    $prices_incl_tax    = $order->get_meta( '_ova_prices_include_tax' );
                    $tax_display        = get_option( 'woocommerce_tax_display_cart' );
                    $item_tax           = $item->get_total_tax();

                    if ( $prices_incl_tax ) {
                        if ( 'excl' === $tax_display ) {
                            $deposit_price  = wc_price( $deposit_amount - $item_tax + $insurance_amount, [ 'currency' => $order->get_currency() ]);
                            $tax_string     = ' <small class="tax_label">'.esc_html__( '(ex. tax)', 'ova-brw' ).'</small>';
                        }
                    } else {
                        if ( 'incl' === $tax_display ) {
                            $deposit_price  = wc_price( $deposit_amount + $item_tax + $insurance_amount, [ 'currency' => $order->get_currency() ]);
                            $tax_string     = ' <small class="tax_label">'.esc_html__( '(incl. tax)', 'ova-brw' ).'</small>';
                        }
                    }
                }

                $subdeposit .= $deposit_price . $insurance_string . $tax_string;
                $subdeposit .= '</dd>';
            }

            return apply_filters( OVABRW_PREFIX.'get_order_formatted_line_subdeposit', $subdeposit, $item, $order );
        }

        /**
         * Get order formatted line sub-remaining amount
         */
        public function get_order_formatted_line_subremaining( $item, $order ) {
            // init
            $subremaining = '';

            // Get remaining amount
            $remaining_amount = (float)$item->get_meta( 'ovabrw_remaining_amount' );
            if ( $remaining_amount ) {
                // Insurance string
                $insurance_string = '';

                // Tax string
                $tax_string = '';

                // Sub remaining amount
                $subremaining .= '<dt>'.esc_html__( 'Remaining:', 'ova-brw' ).'</dt>';
                $subremaining .= '<dd>';

                // Convert price
                $remaining_price = wc_price( $remaining_amount, [
                    'currency' => $order->get_currency()
                ]);

                // Get insurance amount
                $insurance_amount = (float)$item->get_meta( 'ovabrw_remaining_insurance' );
                if ( $insurance_amount ) {
                    // Insurance include tax
                    $insurance_amount = OVABRW()->options->get_insurance_inclusive_tax( $insurance_amount );

                    // Remaining price
                    $remaining_price = wc_price( $remaining_amount + $insurance_amount, [ 'currency' => $order->get_currency() ]);

                    // Insurance string
                    $insurance_string = ' <small class="tax_label">' . sprintf( esc_html__( '(incl. %s insurance fee)', 'ova-brw' ), wc_price( $insurance_amount, [ 'currency' => $order->get_currency() ] ) ) . '</small>';
                }

                // Taxable
                if ( wc_tax_enabled() ) {
                    $prices_incl_tax    = $order->get_meta( '_ova_prices_include_tax' );
                    $tax_display        = get_option( 'woocommerce_tax_display_cart' );
                    $remaining_tax      = floatval( $item->get_meta( 'ovabrw_remaining_tax' ) );

                    if ( $prices_incl_tax ) {
                        if ( 'excl' === $tax_display ) {
                            $remaining_price    = wc_price( $remaining_amount - $remaining_tax + $insurance_amount, [ 'currency' => $order->get_currency() ] );
                            $tax_string         = ' <small class="tax_label">'.esc_html__( '(ex. tax)', 'ova-brw' ).'</small>';
                        }
                    } else {
                        if ( 'incl' === $tax_display ) {
                            $remaining_price    = wc_price( $remaining_amount + $remaining_tax + $insurance_amount, [ 'currency' => $order->get_currency() ] );
                            $tax_string         = ' <small class="tax_label">'.esc_html__( '(incl. tax)', 'ova-brw' ).'</small>';
                        }
                    }
                }

                $subremaining .= $remaining_price . $insurance_string . $tax_string;
                $subremaining .= '</dd>';
            }

            return apply_filters( OVABRW_PREFIX.'get_order_formatted_line_subremaining', $subremaining, $item, $order );
        }

        /**
         * Get order formatted line subtotal payabled amount
         */
        public function get_order_formatted_line_subtotal_payable( $item, $order ) {
            // init
            $subtotal_payable = '';

            // Get total payable
            $total_payable  = (float)$item->get_meta( 'ovabrw_total_payable' );
            if ( $total_payable ) {
                // Tax string
                $tax_string = '';

                // Insurance amount
                $insurance_amount = (float)$item->get_meta( 'ovabrw_insurance_amount' );
                if ( $insurance_amount ) {
                    // Insurance include tax
                    $insurance_amount = OVABRW()->options->get_insurance_inclusive_tax( $insurance_amount );

                    // Update total payable
                    $total_payable += $insurance_amount;
                }

                // Remaining insurance amount
                $remaining_insurance = (float)$item->get_meta( 'ovabrw_remaining_insurance' );
                if ( $remaining_insurance ) {
                    // Remaining insurance include tax
                    $remaining_insurance = OVABRW()->options->get_insurance_inclusive_tax( $remaining_insurance );

                    // Update total payable
                    $total_payable += $remaining_insurance;
                }

                // Subtotal payable
                $subtotal_payable .= '<dt>'.esc_html__( 'Total payable:', 'ova-brw' ).'</dt>';
                $subtotal_payable .= '<dd>';

                // Convert price
                $payable_price = wc_price( $total_payable, [
                    'currency' => $order->get_currency()
                ]);

                // Tax enabled
                if ( wc_tax_enabled() ) {
                    $prices_incl_tax    = $order->get_meta( '_ova_prices_include_tax' );
                    $tax_display        = get_option( 'woocommerce_tax_display_cart' );
                    $item_tax           = $item->get_total_tax();
                    $remaining_tax      = floatval( $item->get_meta( 'ovabrw_remaining_tax' ) );

                    if ( $prices_incl_tax ) {
                        if ( 'excl' === $tax_display ) {
                            $payable_price = wc_price( $total_payable - $item_tax - $remaining_tax, [ 'currency' => $order->get_currency() ] );
                            $tax_string = ' <small class="tax_label">'.esc_html__( '(ex. tax)', 'ova-brw' ).'</small>';
                        }
                    } else {
                        if ( 'incl' === $tax_display ) {
                            $payable_price = wc_price( $total_payable + $item_tax + $remaining_tax, [ 'currency' => $order->get_currency() ] );
                            $tax_string = ' <small class="tax_label">'.esc_html__( '(incl. tax)', 'ova-brw' ).'</small>';
                        }
                    }
                }

                $subtotal_payable .= $payable_price . $tax_string;
                $subtotal_payable .= '</dd>';
            }

            return apply_filters( OVABRW_PREFIX.'get_order_formatted_line_subtotal_payable', $subtotal_payable, $item, $order );
        }

        /**
         * Get order item totals
         */
        public function get_order_item_totals( $total_rows, $order, $tax_display ) {
            // is deposit
            $is_deposit = $order->get_meta( '_ova_has_deposit' );
            if ( $is_deposit ) {
                // Remove order_total
                if ( isset( $total_rows['order_total'] ) ) unset( $total_rows['order_total'] );

                // Deposit amount
                $deposit_amount = (float)$order->get_meta( '_ova_deposit_amount' );
                if ( $deposit_amount ) {
                    $total_rows['deposit_amount'] = [
                        'label' => esc_html__( 'Deposit:', 'ova-brw' ),
                        'value' => $this->get_formatted_order_deposit( $order )
                    ];
                }

                // Remaining amount
                $remaining_amount = (float)$order->get_meta( '_ova_remaining_amount' );
                if ( $remaining_amount ) {
                    $total_rows['remaining_amount'] = [
                        'label' => esc_html__( 'Remaining:', 'ova-brw' ),
                        'value' => $this->get_formatted_order_remaining( $order )
                    ];
                }

                // Total payable amount
                $total_rows['order_total'] = [
                    'label' => esc_html__( 'Total:', 'ova-brw' ),
                    'value' => $this->get_formatted_order_total_payable( $order )
                ];
            }

            return apply_filters( OVABRW_PREFIX.'get_order_item_totals', $total_rows, $order, $tax_display );
        }

        /**
         * Get formatted order deposit amount
         */
        public function get_formatted_order_deposit( $order ) {
            $order_deposit = wc_price( $order->get_total(), [
                'currency' => $order->get_currency()
            ]);

            // Tax text
            $tax_text = '';

            // Tax for inclusive prices.
            if ( wc_tax_enabled() && 'incl' === get_option( 'woocommerce_tax_display_cart' ) ) {
                $tax_string = [];
                $tax_totals = $order->get_tax_totals();

                if ( ovabrw_array_exists( $tax_totals ) ) {
                    if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
                        foreach ( $tax_totals as $code => $tax ) {
                            $tax_amount     = $tax->formatted_amount;
                            $tax_string[]   = sprintf( '%s %s', $tax_amount, $tax->label );
                        }
                    } else {
                        $tax_amount     = $order->get_total_tax();
                        $tax_string[]   = sprintf( '%s %s', wc_price( $tax_amount, [ 'currency' => $order->get_currency() ] ), WC()->countries->tax_or_vat() );
                    }
                }

                // Update tax text
                if ( ovabrw_array_exists( $tax_string ) ) {
                    $tax_text = ' <small class="includes_tax">' . sprintf( __( '(includes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) . '</small>';
                }
            }

            // Add tax text
            $order_deposit .= $tax_text;

            return apply_filters( OVABRW_PREFIX.'get_formatted_order_deposit', $order_deposit, $order );
        }

        /**
         * Get formatted order remaining amount
         */
        public function get_formatted_order_remaining( $order ) {
            // Get remaining amount
            $remaining_amount = (float)$order->get_meta( '_ova_remaining_amount' );

            // Get remaining tax amount
            $remaining_tax = (float)$order->get_meta( '_ova_remaining_tax' );

            // Get remaining insurance amount
            $remaining_insurance = (float)$order->get_meta( '_ova_remaining_insurance' );
            if ( $remaining_insurance ) {
                $remaining_amount += $remaining_insurance;
            }

            // Get remaining insurance tax amount
            $remaining_insurance_tax = (float)$order->get_meta( '_ova_remaining_insurance_tax' );
            if ( OVABRW()->options->enable_insurance_tax() && $remaining_insurance_tax ) {
                $remaining_tax += $remaining_insurance_tax;
            }

            // Order remaining
            $order_remaining = wc_price( $remaining_amount, [ 'currency' => $order->get_currency() ] );

            // Insurance string
            $insurance_string = '';

            // Tax text
            $tax_text       = '';
            $tax_string     = [];
            $tax_display    = get_option( 'woocommerce_tax_display_cart' );

            // Remaining insurance amount
            if ( $remaining_insurance ) {
                // Remaining insurance tax amount
                if ( OVABRW()->options->enable_insurance_tax() && $remaining_insurance_tax && 'incl' === $tax_display ) {
                    $remaining_insurance += $remaining_insurance_tax;
                }

                // Insurance string
                $insurance_string = ' <small class="includes_tax">';
                $insurance_string .= sprintf( __( '(includes %s insurance fee)', 'ova-brw' ), wc_price( $remaining_insurance, [ 'currency' => $order->get_currency() ] ) );
                $insurance_string .= ' </small>';
            }

            // Taxable
            if ( wc_tax_enabled() ) {
                // Prices include tax
                $prices_incl_tax = $order->get_meta( '_ova_prices_include_tax' );

                // Tax string
                $tax_string[] = sprintf( '%s %s', wc_price( $remaining_tax, [ 'currency' => $order->get_currency() ] ), WC()->countries->tax_or_vat() );

                if ( $prices_incl_tax ) {
                    // Remaining insurance tax amount
                    if ( OVABRW()->options->enable_insurance_tax() && $remaining_insurance_tax ) {
                        $remaining_amount   += $remaining_insurance_tax;
                        $order_remaining    = wc_price( $remaining_amount, [ 'currency' => $order->get_currency() ] );
                    }

                    if ( 'incl' === $tax_display ) {
                        $tax_text = ' <small class="includes_tax">' . sprintf( __( '(includes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) . '</small>';
                    } else {
                        $tax_text = ' <small class="includes_tax">' . sprintf( __( '(excludes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) . '</small>';

                        $remaining_amount   -= $remaining_tax;
                        $order_remaining    = wc_price( $remaining_amount, [ 'currency' => $order->get_currency() ] );
                    }
                } else {
                    if ( 'excl' === $tax_display ) {
                        // Tax text
                        $tax_text = ' <small class="includes_tax">' . sprintf( __( '(excludes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) . '</small>';

                        // Order remaining amount
                        $order_remaining = wc_price( $remaining_amount, [ 'currency' => $order->get_currency() ] );
                    } else {
                        // Tax text
                        $tax_text = ' <small class="includes_tax">' . sprintf( __( '(includes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) . '</small>';

                        // Order remaining amount
                        $remaining_amount   += $remaining_tax;
                        $order_remaining    = wc_price( $remaining_amount, [ 'currency' => $order->get_currency() ] );
                    }
                }
            }

            $order_remaining .= $insurance_string . $tax_text;

            return apply_filters( OVABRW_PREFIX.'get_formatted_order_total_payable', $order_remaining, $order );
        }

        /**
         * Get formatted order total payable amount
         */
        public function get_formatted_order_total_payable( $order ) {
            // Get order total
            $order_total = $order->get_total();

            // Get remaining amount
            $remaining_amount = (float)$order->get_meta( '_ova_remaining_amount' );
            if ( $remaining_amount ) {
                $order_total += $remaining_amount;
            }

            // Get remaining tax amount
            $remaining_tax = (float)$order->get_meta( '_ova_remaining_tax' );

            // Get remaining insurance amount
            $remaining_insurance = (float)$order->get_meta( '_ova_remaining_insurance' );
            if ( $remaining_insurance ) {
                $order_total += $remaining_insurance;
            }

            // Get remaining insurance tax amount
            $remaining_insurance_tax = (float)$order->get_meta( '_ova_remaining_insurance_tax' );
            if ( OVABRW()->options->enable_insurance_tax() && $remaining_insurance_tax ) {
                $remaining_tax          += $remaining_insurance_tax;
                $remaining_insurance    += $remaining_insurance_tax;
            }

            // Total payable
            $total_payable = wc_price( $order_total, [ 'currency' => $order->get_currency() ] );

            // Taxable
            $tax_text       = '';
            $tax_string     = [];
            $tax_display    = get_option( 'woocommerce_tax_display_cart' );

            if ( wc_tax_enabled() ) {
                // Prices include tax
                $prices_include_tax = $order->get_meta( '_ova_prices_include_tax' );

                // Get order total tax
                $total_tax = $order->get_total_tax();

                if ( $prices_include_tax ) {
                    // Remaining insurance tax amount
                    if ( OVABRW()->options->enable_insurance_tax() && $remaining_insurance_tax ) {
                        $order_total    += $remaining_insurance_tax;
                        $total_payable  = wc_price( $order_total, [ 'currency' => $order->get_currency() ] );
                    }

                    if ( 'incl' === $tax_display ) {
                        // Total tax
                        $total_tax += $remaining_tax;

                        // Tax string
                        $tax_string[]   = sprintf( '%s %s', wc_price( $total_tax, [ 'currency' => $order->get_currency() ] ), WC()->countries->tax_or_vat() );
                        $tax_text       = ' <small class="includes_tax">' . sprintf( __( '(includes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) . '</small>';
                    } else {
                        // Order total
                        $order_total -= $remaining_tax;

                        // Total payable
                        $total_payable = wc_price( $order_total, [ 'currency' => $order->get_currency() ] );

                        // Tax string
                        $tax_string[]   = sprintf( '%s %s', wc_price( $remaining_tax, [ 'currency' => $order->get_currency() ] ), WC()->countries->tax_or_vat() );
                        $tax_text       = ' <small class="includes_tax">' . sprintf( __( '(excludes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) . '</small>';
                    }
                } else {
                    if ( 'excl' === $tax_display ) {
                        $tax_string[]   = sprintf( '%s %s', wc_price( $remaining_tax, [ 'currency' => $order->get_currency() ] ), WC()->countries->tax_or_vat() );
                        $tax_text       = ' <small class="includes_tax">' . sprintf( __( '(excludes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) . '</small>';
                    } else {
                        // Order total
                        $order_total += $remaining_tax;

                        // Total tax
                        $total_tax += $remaining_tax;

                        // Total payable
                        $total_payable = wc_price( $order_total, [ 'currency' => $order->get_currency() ] );

                        // Tax string
                        $tax_string[]   = sprintf( '%s %s', wc_price( $total_tax, [ 'currency' => $order->get_currency() ] ), WC()->countries->tax_or_vat() );
                        $tax_text       = ' <small class="includes_tax">' . sprintf( __( '(includes %s)', 'ova-brw' ), implode( ', ', $tax_string ) ) . '</small>';
                    }
                }
            }

            $total_payable .= $tax_text;

            return apply_filters( OVABRW_PREFIX.'get_formatted_order_total_payable', $total_payable, $order );
        }

        /**
         * Admin order item header
         */
        public function admin_order_item_headers( $order ) {
            if ( !$order ) return;

            // Has deposit
            $has_deposit = $order->get_meta( '_ova_has_deposit' );

            // Extra price
            if ( 'yes' === ovabrw_get_setting( 'booking_form_show_extra', 'no' ) ) {
                echo '<th class="ovabrw-extra-price">'.esc_html__( 'Extra Price' , 'ova-brw' ).'</th>';
            }

            // Deposit
            if ( $has_deposit ) {
                echo '<th class="deposit-amount">'.esc_html__( 'Deposit' , 'ova-brw' ).'</th>';
                echo '<th class="remaining-amount">'.esc_html__( 'Remaining' , 'ova-brw' ).'</th>';
            } else {
                // Get insurance amount
                $insurance_amount = (float)$order->get_meta( '_ova_insurance_amount' );
                if ( $insurance_amount ) {
                    echo '<th class="insurance-amount">'.esc_html__( 'Insurance' , 'ova-brw' ).'</th>';
                }
            }
        }

        /**
         * Admin order item values
         */
        public function admin_order_item_values( $product, $item, $item_id ) {
            // Get item type
            $item_type = method_exists( $item, 'get_type' ) ? $item->get_type() : '';

            if ( in_array( $item_type, [ 'fee', 'shop_order_refund', 'shipping' ] ) ) {
                // Parent order
                $parent_order = false;

                if ( 'shop_order_refund' === $item->get_type() ) {
                    $parent_order = wc_get_order( $item->get_parent_id() );
                }
                if ( 'fee' === $item->get_type() || 'shipping' === $item->get_type() ) {
                    $parent_order = $item->get_order();
                }

                if ( $parent_order && is_object( $parent_order ) ) {
                    if ( 'yes' === ovabrw_get_setting( 'booking_form_show_extra', 'no' ) ): ?>
                        <td class="ovabrw-extra-price" width="12%"></td>
                    <?php endif;

                    // Has deposit
                    if ( $parent_order->get_meta( '_ova_has_deposit' ) ): ?>
                        <td class="ovabrw-deposit-amount" width="12%"></td>
                        <td class="ovabrw-remaining-amount" width="12%"></td>
                    <?php else:
                        // Get insurance amount
                        $insurance_amount = (float)$parent_order->get_meta( '_ova_insurance_amount' );
                        if ( $insurance_amount ): ?>
                            <td class="ovabrw-insurance-amount" width="10%"></td>
                        <?php endif;
                    endif;
                }

                return;
            }

            // Get order
            $order = method_exists( $item, 'get_order' ) ? $item->get_order() : '';
            if ( !$order ) return;

            // Get order id
            $order_id = $order->get_id();

            if ( 'yes' === ovabrw_get_setting( 'booking_form_show_extra', 'no' ) ):
                // Product ID
                $product_id = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';

                // Pick-up date
                $pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );

                // Drop-off date
                $dropoff_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );

                // Quantity
                $quantity = (int)$item->get_meta( 'ovabrw_number_vehicle' );
                if ( !$quantity ) $quantity = 1;

                // Get custom checkout fields data
                $cckf       = $item->get_meta( 'ovabrw_custom_ckf' );
                $cckf_qty   = $item->get_meta( 'ovabrw_custom_ckf_qty' );

                // Custom checkout fields HTML
                $html_cckf = '';
                if ( ovabrw_array_exists( $cckf ) ) {
                    $html_cckf = OVABRW()->options->get_html_cckf( $cckf, $cckf_qty, [
                        'product_id'    => $product_id,
                        'quantity'      => $quantity,
                        'order_id'      => $order_id
                    ]);
                }

                // Get resources data
                $resources      = $item->get_meta( 'ovabrw_resources' );
                $resources_qty  = $item->get_meta( 'ovabrw_resources_qty' );

                // Resources HTML
                $html_resources = '';
                if ( ovabrw_array_exists( $resources ) ) {
                    $html_resources = OVABRW()->options->get_html_resources( $resources, $resources_qty, [
                        'product_id'    => $product_id,
                        'pickup_date'   => $pickup_date,
                        'dropoff_date'  => $dropoff_date,
                        'quantity'      => $quantity,
                        'order_id'      => $order_id
                    ]);
                }

                // Get services data
                $services       = $item->get_meta( 'ovabrw_services' );
                $services_qty   = $item->get_meta( 'ovabrw_services_qty' );

                // Services HTML
                $html_services = '';
                if ( ovabrw_array_exists( $services ) ) {
                    $html_services = OVABRW()->options->get_html_services( $services, $services_qty, [
                        'product_id'    => $product_id,
                        'pickup_date'   => $pickup_date,
                        'dropoff_date'  => $dropoff_date,
                        'quantity'      => $quantity,
                        'order_id'      => $order_id
                    ]);
                }

                // Check exist resource_html and service_html
                $html_extra = OVABRW()->options->get_html_extra( $html_cckf, $html_resources, $html_services ); ?>
                <td class="ovabrw-extra-price" width="12%">
                    <div class="view">
                        <?php echo wp_kses_post( $html_extra ); ?>
                    </div>
                </td>
            <?php endif;

            // Get item insurance amount
            $item_insurance     = (float)$item->get_meta( 'ovabrw_insurance_amount' );
            $item_insurance_tax = (float)$item->get_meta( 'ovabrw_insurance_tax' );

            // Get item remaining insurance amount
            $item_remaining_insurance     = (float)$item->get_meta( 'ovabrw_remaining_insurance' );
            $item_remaining_insurance_tax = (float)$item->get_meta( 'ovabrw_remaining_insurance_tax' );

            // Has deposit
            $has_deposit = $order->get_meta( '_ova_has_deposit' );
            if ( $has_deposit ):
                $deposit_amount     = (float)$item->get_meta( 'ovabrw_deposit_amount' );
                $remaining_amount   = (float)$item->get_meta( 'ovabrw_remaining_amount' );
                
                // Prices including tax
                $prices_incl_tax = $order->get_meta( '_ova_prices_include_tax' );
            ?>
                <td class="ovabrw-deposit-amount" width="12%">
                    <?php if ( $deposit_amount ):
                        $deposit_amount += $item_insurance;

                        // Get item total tax
                        $item_total_tax = $item->get_total_tax();
                        if ( !$prices_incl_tax ) $deposit_amount += $item_total_tax;

                        if ( wc_tax_enabled() ) {
                            $deposit_amount += $item_insurance_tax;
                            $item_total_tax += $item_insurance_tax;
                        }
                    ?>
                        <div class="view">
                            <?php
                                $deposit_html = wc_price( $deposit_amount, [ 'currency' => $order->get_currency() ]);

                                if ( $item_insurance ) {
                                    $deposit_html .= '<small class="includes_tax">';
                                        $deposit_html .= wp_kses_post( sprintf( __( '(incl. %s insurance fee)', 'ova-brw' ), ovabrw_wc_price( $item_insurance, [ 'currency' => $order->get_currency() ], false )));
                                    $deposit_html .= '</small>';
                                }

                                if ( wc_tax_enabled() && $item_total_tax ) {
                                    $tax_string = '<small class="includes_tax">';
                                    $tax_string .= wp_kses_post( sprintf( __( '(incl. %s %s)' ), wc_price( $item_total_tax, [ 'currency' => $order->get_currency() ] ), WC()->countries->tax_or_vat() ));
                                    $tax_string .= '</small>';

                                    $deposit_html .= $tax_string;
                                }

                                echo wp_kses_post( $deposit_html );
                            ?>
                        </div>
                    <?php endif; ?>
                </td>
                <td class="ovabrw-remaining-amount" width="12%">
                    <?php if ( $remaining_amount ):
                        if ( $item_remaining_insurance ) {
                            $remaining_amount += $item_remaining_insurance;
                        }

                        $remanining_tax = (float)$item->get_meta( 'ovabrw_remaining_tax' );
                    ?>
                        <div class="view">
                            <?php
                                $remaining_html = wc_price( $remaining_amount, [
                                    'currency' => $order->get_currency()
                                ]);

                                if ( wc_tax_enabled() && $item_remaining_insurance_tax ) {
                                    $remaining_amount   += $item_remaining_insurance_tax;
                                    $remanining_tax     += $item_remaining_insurance_tax;

                                    $remaining_html = wc_price( $remaining_amount, [
                                        'currency' => $order->get_currency()
                                    ]);

                                    $remaining_html .= '<small class="includes_tax">';
                                        $remaining_html .= wp_kses_post( sprintf( __( '(incl. %s insurance fee)', 'ova-brw' ), ovabrw_wc_price( $item_remaining_insurance, [ 'currency' => $order->get_currency() ], false )));
                                    $remaining_html .= '</small>';
                                }

                                // Remaining tax
                                if ( wc_tax_enabled() && $remanining_tax ) {
                                    $tax_string = '<small class="includes_tax">';

                                    if ( $prices_incl_tax ) {
                                        $tax_string .= wp_kses_post( sprintf( __( '(incl. %s %s)' ), ovabrw_wc_price( $remanining_tax, [ 'currency' => $order->get_currency() ], false ), WC()->countries->tax_or_vat() ));
                                    } else {
                                        $tax_string .= wp_kses_post( sprintf( __( '(ex. %s %s)' ), ovabrw_wc_price( $remanining_tax, [ 'currency' => $order->get_currency() ], false ), WC()->countries->tax_or_vat() ));
                                    }
                                    $tax_string .= '</small>';

                                    $remaining_html .= $tax_string;
                                }

                                echo wp_kses_post( $remaining_html );
                            ?>
                        </div>
                    <?php endif; ?>
                </td>
            <?php else:
                // Get insurance amount
                $insurance_amount = (float)$order->get_meta( '_ova_insurance_amount' );

                if ( $insurance_amount ): ?>
                    <td class="ovabrw-insurance-amount" width="10%">
                        <?php if ( $item_insurance ):
                            $insurance_html = wc_price( $item_insurance, [
                                'currency' => $order->get_currency()
                            ]);

                            // Get insurance tax
                            $insurance_tax = floatval( $item->get_meta( 'ovabrw_insurance_tax' ) );

                            if ( wc_tax_enabled() && $insurance_tax ) {
                                $tax_string = '<br>';
                                $tax_string .= '<small class="includes_tax">';
                                $tax_string .= wp_kses_post( sprintf( __( '(ex. %s %s)' ), wc_price( wc_round_tax_total( $insurance_tax ), [ 'currency' => $order->get_currency() ] ), WC()->countries->tax_or_vat() ));
                                $tax_string .= '</small>';

                                $insurance_html .= $tax_string;
                            }
                        ?>
                            <div class="view">
                                <?php echo wp_kses_post( $insurance_html ); ?>
                            </div>
                        <?php endif; ?>
                    </td>
                    <?php
                endif; // END if
            endif; // END if
        }

        /**
         * Admin order totals after tax
         */
        public function admin_order_totals_after_tax( $order_id ) {
            $order = wc_get_order( $order_id );
            if ( !$order ) return;

            // Has deposit
            $has_deposit = $order->get_meta( '_ova_has_deposit' );
            if ( $has_deposit ) {
                // Deposit amount
                $deposit_amount = (float)$order->get_meta( '_ova_deposit_amount' );

                // Remaining amount
                $remaining_amount = (float)$order->get_meta( '_ova_remaining_amount' );

                // Insurance amount
                $insurance_amount = (float)$order->get_meta( '_ova_insurance_amount' );

                // Insurance tax amount
                $insurance_tax = (float)$order->get_meta( '_ova_insurance_tax' );

                // Remaining insurance amount
                $remaining_insurance = (float)$order->get_meta( '_ova_remaining_insurance' );

                // Remaining insurance tax amount
                $remaining_insurance_tax = (float)$order->get_meta( '_ova_remaining_insurance_tax' );

                // Remaining insurance amount
                if ( $remaining_insurance ) {
                    $remaining_amount += $remaining_insurance;
                }

                // Remaining insurance tax amount
                if ( wc_tax_enabled() && $remaining_insurance_tax ) {
                    $remaining_amount += $remaining_insurance_tax;
                }

                // Total payable
                $total_payable = $deposit_amount + $remaining_amount + $insurance_amount;

                // Remaining HTML
                $remaining_html = ovabrw_wc_price( $remaining_amount, [ 'currency' => $order->get_currency() ], false );

                // Payable HTML
                $payable_html = ovabrw_wc_price( $total_payable , [ 'currency' => $order->get_currency() ], false );

                // Get total tax
                $total_tax = $order->get_total_tax();
                if ( wc_tax_enabled() && $total_tax ) {
                    $text_tax = '';

                    // Remaining tax
                    $remaining_tax = (float)$order->get_meta( '_ova_remaining_tax' );

                    // Remaining insurance tax amount
                    if ( wc_tax_enabled() && $remaining_insurance_tax ) {
                        $remaining_tax += $remaining_insurance_tax;
                    }

                    // Prices including tax
                    if ( $order->get_meta( '_ova_prices_include_tax' ) ) {
                        $text_tax = esc_html__( '(includes %s %s)', 'ova-brw' );

                        // Update total payable
                        $total_payable += $insurance_tax;

                        // Payable HTML
                        $payable_html = ovabrw_wc_price( $total_payable, [ 'currency' => $order->get_currency() ], false );

                        // Remaining
                        if ( $remaining_insurance ) {
                            $tax_string = '<small class="includes_tax">';
                                $tax_string .= wp_kses_post( sprintf( $text_tax, ovabrw_wc_price( $remaining_insurance, [ 'currency' => $order->get_currency() ], false ), esc_html__( 'insurance fee', 'ova-brw' ) ));
                            $tax_string .= '</small>';

                            $remaining_html .= $tax_string;
                        }

                        if ( $remaining_tax ) {
                            $tax_string = '<small class="includes_tax">';
                                $tax_string .= wp_kses_post( sprintf( $text_tax, ovabrw_wc_price( $remaining_tax, [ 'currency' => $order->get_currency() ], false ), WC()->countries->tax_or_vat() ));
                            $tax_string .= '</small>';

                            $remaining_html .= $tax_string;
                        }
                    } else {
                        $text_tax = esc_html__( '(excludes %s %s)', 'ova-brw' );

                        // Update total payable
                        $total_payable += $total_tax;

                        // Payable HTML
                        $payable_html = ovabrw_wc_price( $total_payable, [ 'currency' => $order->get_currency() ], false );

                        if ( $remaining_insurance ) {
                            $tax_string = '<small class="includes_tax">';
                                $tax_string .= wp_kses_post( sprintf( $text_tax, ovabrw_wc_price( $remaining_insurance, [ 'currency' => $order->get_currency() ], false ), esc_html__( 'insurance', 'ova-brw' ) ));
                            $tax_string .= '</small>';

                            $remaining_html .= $tax_string;
                        }

                        // Remaining
                        if ( $remaining_tax ) {
                            $tax_string = '<small class="includes_tax">';
                                $tax_string .= wp_kses_post( sprintf( $text_tax, ovabrw_wc_price( $remaining_tax, [ 'currency' => $order->get_currency() ], false ), WC()->countries->tax_or_vat() ));
                            $tax_string .= '</small>';

                            if ( $remaining_amount ) {
                                $remaining_html .= $tax_string;
                            }
                            if ( $total_payable ) {
                                $payable_html .= $tax_string;
                            }
                        }
                    }
                } else {
                    if ( $remaining_insurance ) {
                        $text_tax   = esc_html__( '(includes %s %s)', 'ova-brw' );
                        $tax_string = '<small class="includes_tax">';
                            $tax_string .= wp_kses_post( sprintf( $text_tax, ovabrw_wc_price( $remaining_insurance, [ 'currency' => $order->get_currency() ], false ), esc_html__( 'insurance fee', 'ova-brw' ) ));
                        $tax_string .= '</small>';

                        $remaining_html .= $tax_string;
                    }
                } ?>
                <tr>
                    <td class="label"><?php esc_html_e( 'Total Deposit:' , 'ova-brw' ); ?></td>
                    <td width="1%"></td>
                    <td class="total">
                        <div class="view">
                            <?php echo wc_price( $order->get_total(), [ 'currency' => $order->get_currency() ] ); ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td class="label"><?php esc_html_e( 'Total Remaining:' , 'ova-brw' ); ?></td>
                    <td width="1%"></td>
                    <td class="total">
                        <div class="view">
                            <?php echo wp_kses_post( $remaining_html ); ?>
                        </div>
                    </td>
                </tr>
                <tr class="ovabrw-total-payable">
                    <td class="label"><?php esc_html_e( 'Total Payable:' , 'ova-brw' ); ?></td>
                    <td width="1%"></td>
                    <td class="total">
                        <div class="view">
                            <?php echo wp_kses_post( $payable_html ); ?>
                        </div>
                    </td>
                </tr>
                <?php
            }
        }

        /**
         * Button pay full & remaining invoice
         */
        public function after_order_itemmeta( $item_id, $item, $product ) {
            $order          = $item->get_order();
            $order_id       = $item->get_order_id();
            $order_status   = $order->get_status();

            // Allow insurance refund when order status
            $statuses = apply_filters( OVABRW_PREFIX.'order_status_to_refund_insurance_amount', [ 'processing', 'completed' ] );
            if ( !ovabrw_array_exists( $statuses ) ) $statuses = [];

            // Get remaining amount
            $remaining_amount = (float)$item->get_meta( 'ovabrw_remaining_amount' );

            // Get remaining balance order ID
            $balance_id = (int)$item->get_meta( 'ovabrw_remaining_balance_order_id' );

            // Get parent order ID
            $parent_order_id = (int)$item->get_meta( 'ovabrw_parent_order_id' );

            // Get insurance amount
            $insurance_amount   = (float)$item->get_meta( 'ovabrw_insurance_amount' );
            $insurance_tax      = (float)$item->get_meta( 'ovabrw_insurance_tax' );

            if ( $remaining_amount && $remaining_amount > 0 ): ?>
                <div class="ova_pay_full">
                <?php if ( $balance_id && $balance_order = wc_get_order( $balance_id ) ):
                    $balance_order_status = $balance_order->get_status();
                ?>
                    <a href="<?php echo esc_url( $balance_order->get_edit_order_url() ); ?>" class="button" target="_blank">
                        <?php 
                            echo wp_kses_post( sprintf( __( 'Remaining - Invoice #%1$s', 'ova-brw' ), $balance_order->get_order_number() ) );
                        ?>
                    </a>
                    <?php if ( $insurance_amount && in_array( $balance_order_status, $statuses ) && in_array( $order_status, $statuses ) ):
                        $insurance_tax = (float)$item->get_meta( 'ovabrw_insurance_tax' );
                    ?>
                        <div class="ovabrw-update-insurance">
                            <button type="button" class="button ovabrw-update-insurance-btn">
                                <?php esc_html_e( 'Update Insurance', 'ova-brw' ); ?>
                            </button>
                            <div class="update-insurance-input">
                                <div class="ovabrw-input-price">
                                    <small>
                                        <strong>
                                            <?php esc_html_e( 'Amount', 'ova-brw' ); ?>
                                        </strong>
                                    </small>
                                    <input
                                        type="text"
                                        class="wc_input_price"
                                        name="<?php echo esc_attr( 'ovabrw_insurance_amount' ); ?>"
                                        value="<?php echo esc_attr( $insurance_amount ); ?>"
                                    />
                                </div>
                                <?php if ( wc_tax_enabled() && $insurance_tax ): ?>
                                    <div class="ovabrw-input-price">
                                        <small>
                                            <strong>
                                                <?php esc_html_e( 'Tax', 'ova-brw' ); ?>
                                            </strong>
                                        </small>
                                        <input
                                            type="text"
                                            class="wc_input_price"
                                            name="<?php echo esc_attr( 'ovabrw_insurance_tax' ); ?>"
                                            value="<?php echo esc_attr( $insurance_tax ); ?>"
                                        />
                                    </div>
                                <?php endif; ?>
                                <button
                                    type="button"
                                    class="button button-primary ovabrw-update-insurance-submit"
                                    data-order_item_id="<?php echo esc_attr( $item_id ); ?>"
                                    data-order_id="<?php echo esc_attr( $order_id ); ?>">
                                    <?php esc_html_e( 'Save', 'ova-brw' ); ?>
                                </button>
                                <button type="button" class="button button-primary ovabrw-update-insurance-cancel" title="<?php esc_attr_e( 'Cancel', 'ova-brw' ); ?>">X</button>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else:
                    $remaining_invoice_url = wp_nonce_url(
                        add_query_arg(
                            [ 'create_remaining_invoice' => $item_id ],
                            $order->get_edit_order_url()
                        ),
                        'create_remaining_invoice',
                        'create_remaining_invoice_nonce'
                    );
                    $pay_full_url = wp_nonce_url(
                        add_query_arg(
                            [ 'pay_full' => $item_id ],
                            $order->get_edit_order_url()
                        ),
                        'pay_full',
                        'pay_full_nonce'
                    );
                ?>
                    <a href="<?php echo esc_url( $remaining_invoice_url ); ?>" class="button">
                        <?php esc_html_e( 'Create Remaining Invoice', 'ova-brw' ); ?>
                    </a>
                    <a href="<?php echo esc_url( $pay_full_url ); ?>" class="button">
                        <?php esc_html_e( 'Pay Full (offline)', 'ova-brw' ); ?>
                    </a>
                <?php endif; ?>
                </div>
            <?php elseif ( $parent_order_id && $parent_order = wc_get_order( $parent_order_id ) ): ?>
                <div class="ovabrw_deposit_btn">
                    <a href="<?php echo esc_url( $parent_order->get_edit_order_url() ); ?>" class="button" target="_blank">
                        <?php esc_html_e( 'View Original Order', 'ova-brw' ); ?>
                    </a>
                </div>
                <?php if ( $insurance_amount && in_array( $order_status, $statuses ) ): ?>
                    <div class="ovabrw-update-insurance">
                        <button type="button" class="button ovabrw-update-insurance-btn">
                            <?php esc_html_e( 'Update Insurance', 'ova-brw' ); ?>
                        </button>
                        <div class="update-insurance-input">
                            <div class="ovabrw-input-price">
                                <small>
                                    <strong>
                                        <?php esc_html_e( 'Amount', 'ova-brw' ); ?>
                                    </strong>
                                </small>
                                <input
                                    type="text"
                                    class="wc_input_price"
                                    name="<?php echo esc_attr( 'ovabrw_insurance_amount' ); ?>"
                                    value="<?php echo esc_attr( $insurance_amount ); ?>"
                                />
                            </div>
                            <?php if ( wc_tax_enabled() && $insurance_tax ): ?>
                                <div class="ovabrw-input-price">
                                    <small>
                                        <strong>
                                            <?php esc_html_e( 'Tax', 'ova-brw' ); ?>
                                        </strong>
                                    </small>
                                    <input
                                        type="text"
                                        class="wc_input_price"
                                        name="<?php echo esc_attr( 'ovabrw_insurance_tax' ); ?>"
                                        value="<?php echo esc_attr( $insurance_tax ); ?>"
                                    />
                                </div>
                            <?php endif; ?>
                            <button
                                type="button"
                                class="button button-primary ovabrw-update-insurance-submit"
                                data-order_item_id="<?php echo esc_attr( $item_id ); ?>"
                                data-order_id="<?php echo esc_attr( $order_id ); ?>">
                                <?php esc_html_e( 'Save', 'ova-brw' ); ?>
                            </button>
                            <button type="button" class="button button-primary ovabrw-update-insurance-cancel" title="<?php esc_attr_e( 'Cancel', 'ova-brw' ); ?>">X</button>
                        </div>
                    </div>
                <?php endif; ?>
            <?php elseif ( $insurance_amount && in_array( $order_status, $statuses ) ): ?>
                <div class="ovabrw-update-insurance">
                    <button type="button" class="button ovabrw-update-insurance-btn">
                        <?php esc_html_e( 'Update Insurance', 'ova-brw' ); ?>
                    </button>
                    <div class="update-insurance-input">
                        <div class="ovabrw-input-price">
                            <small>
                                <strong>
                                    <?php esc_html_e( 'Amount', 'ova-brw' ); ?>
                                </strong>
                            </small>
                            <input
                                type="text"
                                class="wc_input_price"
                                name="<?php echo esc_attr( 'ovabrw_insurance_amount' ); ?>"
                                value="<?php echo esc_attr( $insurance_amount ); ?>"
                            />
                        </div>
                        <?php if ( wc_tax_enabled() && $insurance_tax ): ?>
                            <div class="ovabrw-input-price">
                                <small>
                                    <strong>
                                        <?php esc_html_e( 'Tax', 'ova-brw' ); ?>
                                    </strong>
                                </small>
                                <input
                                    type="text"
                                    class="wc_input_price"
                                    name="<?php echo esc_attr( 'ovabrw_insurance_tax' ); ?>"
                                    value="<?php echo esc_attr( $insurance_tax ); ?>"
                                />
                            </div>
                        <?php endif; ?>
                        <button
                            type="button"
                            class="button button-primary ovabrw-update-insurance-submit"
                            data-order_item_id="<?php echo esc_attr( $item_id ); ?>"
                            data-order_id="<?php echo esc_attr( $order_id ); ?>">
                            <?php esc_html_e( 'Save', 'ova-brw' ); ?>
                        </button>
                        <button type="button" class="button button-primary ovabrw-update-insurance-cancel" title="<?php esc_attr_e( 'Cancel', 'ova-brw' ); ?>">X</button>
                    </div>
                </div>
            <?php endif;
        }

        /**
         * Action pay full & create remaining invoice
         */
        public function order_item_action() {
            $action = $item_id = false;

            // Get action & item_id
            if ( ovabrw_get_meta_data( 'pay_full', $_GET ) ) {
                $action = 'pay_full';
                $nonce  = isset( $_GET['pay_full_nonce'] ) ? wp_verify_nonce( $_GET['pay_full_nonce'], 'pay_full' ) : false;

                if ( $nonce ) $item_id = absint( $_GET['pay_full'] );
            } elseif ( ovabrw_get_meta_data( 'create_remaining_invoice', $_GET ) ) {
                $action = 'create_remaining_invoice';
                $nonce  = isset( $_GET['create_remaining_invoice_nonce'] ) ? wp_verify_nonce( $_GET['create_remaining_invoice_nonce'], 'create_remaining_invoice' ) : false;

                if ( $nonce ) $item_id = absint( $_GET['create_remaining_invoice'] );
            }

            // Check item id
            if ( !$item_id ) return;

            // Get Order Item
            $item = new WC_Order_Item_Product( absint( $item_id ) );
            if ( !$item ) return;

            // Get Order
            $order = $item->get_order();
            if ( !$order ) return;

            // Order ID
            $order_id = $order->get_id();

            // Get item remaining amount
            $item_remaining = (float)$item->get_meta( 'ovabrw_remaining_amount' );
            if ( !$item_remaining ) return;

            // Get item remaining tax amount
            $item_remaining_tax = (float)$item->get_meta( 'ovabrw_remaining_tax' );

            // Get item remaining insurance amount
            $item_remaining_insurance = (float)$item->get_meta( 'ovabrw_remaining_insurance' );

            // Get item remaining insurance tax amount
            $item_remaining_insurance_tax = (float)$item->get_meta( 'ovabrw_remaining_insurance_tax' );

            if ( 'pay_full' === $action ) {
                // Get order total
                $order_total = (float)$order->get_total();

                // Get order deposit amount
                $order_deposit = (float)$order->get_meta( '_ova_deposit_amount' );

                // Get order remaining amount
                $order_remaining = (float)$order->get_meta( '_ova_remaining_amount' );

                // Order total
                $order_total += $item_remaining;

                // Order total deposit
                $order_deposit += $item_remaining;

                // Order total remaining
                $order_remaining -= $item_remaining;
                if ( $order_remaining < 0 ) $order_remaining = 0;

                // Get item deposit amount
                $item_deposit = (float)$item->get_meta( 'ovabrw_deposit_amount' );

                // Item deposit amount
                $item_deposit += $item_remaining;

                // Item total
                $item_total = $item_deposit;

                // Taxable
                $item_taxes = false;

                if ( wc_tax_enabled() ) {
                    // Prices include tax
                    $prices_incl_tax = $order->get_meta( '_ova_prices_include_tax' );

                    // Get order remaining tax
                    $order_remaining_tax = (float)$order->get_meta( '_ova_remaining_tax' );
                    // Get item remaining tax
                    $item_remaining_tax = (float)$item->get_meta( 'ovabrw_remaining_tax' );
                    $order_remaining_tax -= $item_remaining_tax;
                    if ( $order_remaining_tax < 0 ) $order_remaining_tax = 0;

                    // Calculate tax
                    $calculate_tax_for  = $order->get_taxable_location();
                    $tax_rates          = WC_Tax::find_rates( $calculate_tax_for );

                    if ( $prices_incl_tax ) {
                        $taxes      = WC_Tax::calc_inclusive_tax( $item_total, $tax_rates );
                        $item_total -= WC_Tax::get_tax_total( $taxes );
                    } else {
                        $taxes = WC_Tax::calc_exclusive_tax( $item_total, $tax_rates );
                        $order_total += $item_remaining_tax;
                    }

                    // Item taxes
                    $item_taxes = [
                        'total'    => $taxes,
                        'subtotal' => $taxes
                    ];

                    // Update tax
                    $item->update_meta_data( 'ovabrw_remaining_tax', 0 );
                    $order->update_meta_data( '_ova_remaining_tax', $order_remaining_tax );
                }

                // Update item meta
                $item->set_props(
                    [
                        'total'     => $item_total,
                        'subtotal'  => $item_total,
                        'taxes'     => $item_taxes
                    ]
                );

                // Update item deposit amount
                $item->update_meta_data( 'ovabrw_deposit_amount', $item_deposit );

                // Update item remaining amount
                $item->update_meta_data( 'ovabrw_remaining_amount', 0 );

                // Update order fee ( insurance fees )
                if ( $item_remaining_insurance ) {
                    // Insurance key
                    $insurance_key = $order->get_meta( '_ova_insurance_key' );

                    // Order insurance
                    $order_insurance        = (float)$order->get_meta( '_ova_insurance_amount' );
                    $order_insurance_tax    = (float)$order->get_meta( '_ova_insurance_tax' );

                    // Order remaining insurance
                    $order_remaining_insurance      = (float)$order->get_meta( '_ova_remaining_insurance' );
                    $order_remaining_insurance_tax  = (float)$order->get_meta( '_ova_remaining_insurance_tax' );

                    // Item insurance
                    $item_insurance = (float)$item->get_meta( 'ovabrw_insurance_amount' );

                    // Item insurance tax
                    $item_insurance_tax = (float)$item->get_meta( 'ovabrw_insurance_tax' );

                    // Get item fees
                    $fees = $order->get_fees();

                    // Update order insurance amount
                    if ( ovabrw_array_exists( $fees ) ) {
                        foreach ( $fees as $item_fee_id => $item_fee ) {
                            $fee_key = sanitize_title( $item_fee->get_name() );

                            if ( $fee_key === $insurance_key ) {
                                // Update order total
                                $order_total += $item_remaining_insurance;

                                // Item insurance amount
                                $item_insurance     += $item_remaining_insurance;
                                $item_insurance_tax += $item_remaining_insurance_tax;

                                // Order insurance amount
                                $order_insurance        += $item_remaining_insurance;
                                $order_insurance_tax    += $item_remaining_insurance_tax;

                                // Order remaining insurance amount
                                $order_remaining_insurance      -= $item_remaining_insurance;
                                $order_remaining_insurance_tax  -= $item_remaining_insurance_tax;

                                // Update item fee
                                if ( wc_tax_enabled() ) {
                                    // Update order total
                                    $order_total += $item_remaining_insurance_tax;

                                    $order_taxes = $order->get_taxes();
                                    $tax_item_id = 0;

                                    foreach ( $order_taxes as $tax_item ) {
                                        $tax_item_id = $tax_item->get_rate_id();
                                        if ( $tax_item_id ) break;
                                    }

                                    // Update item fee
                                    $item_fee->set_props(
                                        [
                                            'total'     => $order_insurance,
                                            'subtotal'  => $order_insurance,
                                            'total_tax' => $order_insurance_tax,
                                            'taxes'     => [
                                                'total' => [
                                                    $tax_item_id => $order_insurance_tax
                                                ]
                                            ]
                                        ]
                                    );

                                    // Update item meta data
                                    $item->update_meta_data( 'ovabrw_remaining_insurance_tax', 0 );
                                    $item->update_meta_data( 'ovabrw_insurance_tax', $item_insurance_tax );

                                    // Update order meta data
                                    $order->update_meta_data( '_ova_remaining_insurance_tax', $order_remaining_insurance_tax );
                                    $order->update_meta_data( '_ova_insurance_tax', $order_insurance_tax );
                                } else {
                                    // Update item fee
                                    $item_fee->set_props(
                                        [
                                            'total'     => $order_insurance,
                                            'subtotal'  => $order_insurance
                                        ]
                                    );
                                }

                                $item_fee->set_amount( $order_insurance );
                                $item_fee->save();
                            }

                            // Update item meta data
                            $item->update_meta_data( 'ovabrw_remaining_insurance', 0 );
                            $item->update_meta_data( 'ovabrw_insurance_amount', $item_insurance );

                            // Update order meta data
                            $order->update_meta_data( '_ova_remaining_insurance', $order_remaining_insurance );
                            $order->update_meta_data( '_ova_insurance_amount', $order_insurance );
                        }
                    }
                }

                // Save item
                $item->save();

                // Update order deposit amount
                $order->update_meta_data( '_ova_deposit_amount', $order_deposit );

                // Update order remaining amount
                $order->update_meta_data( '_ova_remaining_amount', $order_remaining );

                // Order set total
                $order->set_total( $order_total );

                // Order update taxes
                $order->update_taxes();

                // Order save
                $order->save();

                // Send email
                if ( apply_filters( OVABRW_PREFIX.'send_email_pay_full', true ) ) {
                    $emails = WC_Emails::instance();
                    $emails->customer_invoice( $order );
                }

                wp_safe_redirect( admin_url( 'post.php?post=' . absint( $order_id ) . '&action=edit' ) );
                exit;
            } elseif ( 'create_remaining_invoice' === $action ) {
                // Taxable
                if ( wc_tax_enabled() ) {
                    // Prices include tax
                    $prices_incl_tax = $order->get_meta( '_ova_prices_include_tax' );

                    if ( $prices_incl_tax ) {
                        // Calculate tax
                        $calculate_tax_for  = $order->get_taxable_location();
                        $tax_rates          = WC_Tax::find_rates( $calculate_tax_for );
                        $taxes              = WC_Tax::calc_inclusive_tax( $item_remaining, $tax_rates );
                        $item_remaining     -= WC_Tax::get_tax_total( $taxes );
                    }
                }

                // Data item
                $data_item = [
                    'product'   => $item->get_product(),
                    'quantity'  => $item->get_quantity(),
                    'subtotal'  => $item_remaining,
                    'total'     => $item_remaining
                ];

                // Remaining tax amount
                if ( $item_remaining_tax ) {
                    $data_item['remaining_tax'] = $item_remaining_tax;
                }

                // Remaining insurance amount
                if ( $item_remaining_insurance ) {
                    $data_item['insurance_amount'] = $item_remaining_insurance;
                }

                // Remaining insurance tax amount
                if ( $item_remaining_insurance_tax ) {
                    $data_item['insurance_tax'] = $item_remaining_insurance_tax;
                }

                // New order id
                $new_order_id = OVABRW()->booking->create_order_remaining( $order_id, $data_item );

                // New order
                $new_order = wc_get_order( $new_order_id );

                // Add new order meta data
                $new_order->add_meta_data( '_ova_original_id', $order_id );
                $new_order->add_meta_data( '_ova_original_item_id', $item_id );
                $new_order->save();

                // Add remaining balance order id
                $item->add_meta_data( 'ovabrw_remaining_balance_order_id', $new_order_id, true );
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

                // Update order remaining invoice ids
                $order->update_meta_data( '_ova_remaining_invoice_ids', $remaining_invoice_ids );
                $order->save();

                // Email invoice
                $send_email = get_option( 'send_email_remaining_invoice_enable', 'yes' );

                if ( 'yes' == $send_email ) {
                    $emails = WC_Emails::instance();
                    $emails->customer_invoice( wc_get_order( $new_order_id ) );
                }
                
                wp_safe_redirect( $new_order->get_edit_order_url() );
                exit;
            }
        }

        /**
         * Saved order items
         */
        public function saved_order_items( $order_id, $items ) {
            $order = wc_get_order( $order_id );
            if ( !$order ) return;

            // Order insurance amount
            if ( isset( $items['order_item_id'] ) && is_array( $items['order_item_id'] ) ) {
                // Insurance key
                $insurance_key = $order->get_meta( '_ova_insurance_key' );

                foreach ( $items['order_item_id'] as $item_id ) {
                    $item = WC_Order_Factory::get_order_item( absint( $item_id ) );
                    if ( !$item ) continue;

                    if ( 'fee' === $item->get_type() && isset( $items['line_total'][$item_id] ) ) {
                        $fee_key = sanitize_title( $item->get_name() );

                        if ( $fee_key === $insurance_key ) {
                            $insurance_amount = (float)$items['line_total'][$item_id];

                            $order->update_meta_data( '_ova_insurance_amount', $insurance_amount );
                            $order->save();
                        }
                    }
                }
            }
        }

        /**
         * Hidden itemmeta
         */
        public function hidden_order_itemmeta( $meta_keys ) {
            $meta_keys[] = 'ovabrw_original_order_id';
            $meta_keys[] = 'ovabrw_remaining_balance_order_id';

            return apply_filters( OVABRW_PREFIX.'hidden_order_itemmeta', $meta_keys );
        }

        /**
         * Manager order - Add custom columns
         */
        public function add_custom_columns( $add_column ) {
            if ( !ovabrw_array_exists( $add_column ) ) return $add_column;

            // New columns
            $new_columns = [];

            foreach ( $add_column as $key => $column ) {
                if ( 'order_total' === $key ) {
                    $new_columns['ovabrw_deposit_amount']   = esc_html__( 'Deposit', 'ova-brw' );
                    $new_columns['ovabrw_remaining_amount'] = esc_html__( 'Remaining', 'ova-brw' );
                    $new_columns['ovabrw_deposit_status']   = esc_html__( 'Deposit status', 'ova-brw');
                    $new_columns['ovabrw_insurance_status'] = esc_html__( 'Insurance', 'ova-brw');
                }

                $new_columns[$key] = $column;
            }

            return apply_filters( OVABRW_PREFIX.'add_custom_columns', $new_columns, $add_column );
        }

        /**
         * Manager order - Posts custom column
         */
        public function posts_custom_column( $column_id, $order ) {
            // For woocommerce order legacy
            if ( is_numeric( $order ) ) {
                $order = wc_get_order( $order );
            }
            // End

            if ( $order ) {
                // Order deposit amount
                if ( 'ovabrw_deposit_amount' === $column_id ) {
                    $deposit_amount = (float)$order->get_meta( '_ova_deposit_amount' );

                    if ( $deposit_amount ): ?>
                        <span class="ova_deposit_amount">
                            <?php echo wc_price( $order->get_total(), [ 'currency' => $order->get_currency() ] ); ?>
                        </span>
                    <?php endif;
                }

                // Order remaining amount
                if ( 'ovabrw_remaining_amount' === $column_id ) {
                    // Get remaining amount
                    $remaining_amount = (float)$order->get_meta( '_ova_remaining_amount' );

                    // Get remaining tax amount
                    $remaining_tax = (float)$order->get_meta( '_ova_remaining_tax' );

                    // Get remaining insurance amount
                    $remaining_insurance = (float)$order->get_meta( '_ova_remaining_insurance' );

                    // Get remaining insurance tax amount
                    $remaining_insurance_tax = (float)$order->get_meta( '_ova_remaining_insurance_tax' );

                    // Remaining insurance amount
                    if ( $remaining_insurance ) {
                        $remaining_amount += $remaining_insurance;
                    }

                    // Tax enabled
                    if ( wc_tax_enabled() ) {
                        // Remaining insurance tax amount
                        if ( $remaining_insurance_tax ) {
                            $remaining_amount += $remaining_insurance_tax;
                        }

                        // Remaining tax amount
                        if ( $remaining_tax && !$order->get_meta( '_ova_prices_include_tax' ) ) {
                            $remaining_amount += $remaining_tax;
                        }
                    }

                    if ( $remaining_amount ): ?>
                        <span class="ova_deposit_amount">
                            <?php echo ovabrw_wc_price( $remaining_amount, [ 'currency' => $order->get_currency() ] ); ?>
                        </span>
                    <?php endif;
                }

                // Order deposit status
                if ( 'ovabrw_deposit_status' === $column_id ) {
                    // Has deposit
                    $has_deposit = $order->get_meta( '_ova_has_deposit' );

                    if ( $has_deposit ) {
                        $remaining_amount = (float)$order->get_meta( '_ova_remaining_amount' );

                        if ( $remaining_amount ):
                            $is_remaining_invoice   = false;
                            $remaining_invoice_ids  = $order->get_meta( '_ova_remaining_invoice_ids' );

                            // Check remaining invoice ids
                            if ( ovabrw_array_exists( $remaining_invoice_ids ) ) {
                                foreach ( $remaining_invoice_ids as $remaining_invoice_id ) {
                                    if ( wc_get_order( $remaining_invoice_id ) ) {
                                        $is_remaining_invoice = true;
                                        break;
                                    }
                                }
                            }

                            if ( $is_remaining_invoice ): ?>
                                <mark class="ovabrw-order-status status-processing">
                                    <span class="ovabrw-deposit-status">
                                        <?php esc_html_e( 'Original Payment', 'ova-brw' ); ?>
                                    </span>
                                </mark>
                            <?php else: ?>
                                <mark class="ovabrw-order-status status-pending">
                                    <span class="ovabrw-deposit-status">
                                        <?php esc_html_e( 'Partial Payment', 'ova-brw' ); ?>
                                    </span>
                                </mark>
                            <?php endif; ?>
                        <?php else: ?>
                            <mark class="ovabrw-order-status status-processing">
                                <span class="ovabrw-deposit-status">
                                    <?php esc_html_e( 'Full Payment', 'ova-brw' ); ?>
                                </span>
                            </mark>
                        <?php endif;

                        // Remaining Invoice
                        $remaining_invoice_ids = $order->get_meta( '_ova_remaining_invoice_ids' );

                        if ( ovabrw_array_exists( $remaining_invoice_ids ) ) {
                            foreach ( $remaining_invoice_ids as $order_remaining_id ) {
                                $remaining_order = wc_get_order( $order_remaining_id );

                                if ( $remaining_order ): ?>
                                    <mark class="ovabrw-order-view">
                                        <a href="<?php echo esc_url( $remaining_order->get_edit_order_url() ); ?>" class="button" target="_blank">
                                            <?php 
                                                echo wp_kses_post( sprintf( __( 'Remaining Invoice #%1$s', 'ova-brw' ), $remaining_order->get_order_number() ) );
                                            ?>
                                        </a>
                                    </mark>
                                <?php endif;
                            }
                        }
                    }

                    // Original Order
                    $original_id = $order->get_meta( '_ova_original_id' );

                    if ( $original_id ) {
                        $original_order = wc_get_order( $original_id );

                        if ( $original_order ): ?>
                            <mark class="ovabrw-order-view">
                                <a href="<?php echo esc_url( $original_order->get_edit_order_url() ); ?>" class="button" target="_blank">
                                    <?php 
                                        echo wp_kses_post( sprintf( __( 'Original Order #%1$s', 'ova-brw' ), $original_order->get_order_number() ) );
                                    ?>
                                </a>
                            </mark>
                        <?php endif;
                    }
                }

                // Order insurance status
                if ( 'ovabrw_insurance_status' === $column_id ) {
                    $insurance_amount = $order->get_meta( '_ova_insurance_amount' );

                    if ( '' !== $insurance_amount ) {
                        $insurance_amount = (float)$insurance_amount;

                        if ( $insurance_amount > 0 ): ?>
                            <mark class="ovabrw-order-status status-on-hold">
                                <span class="ovabrw-insurance-status">
                                    <?php esc_html_e( 'Received', 'ova-brw' ); ?>
                                </span>
                            </mark>
                        <?php else: ?>
                            <mark class="ovabrw-order-status status-processing">
                                <span class="ovabrw-insurance-status">
                                    <?php esc_html_e( 'Paid for Customers', 'ova-brw' ); ?>
                                </span>
                            </mark>
                        <?php endif;
                    }
                }
            }
        }

        /**
         * Before saved order items
         */
        public function before_save_order_item( $item ) {
            // Get item id
            $item_id = method_exists( $item, 'get_id' ) ? $item->get_id() : '';

            // Get item object
            $item_obj = WC_Order_Factory::get_order_item( absint( $item_id ) );

            if ( $item_obj && apply_filters( OVABRW_PREFIX.'saved_order_item_custom_fields', true ) ) {
                // Define day
                $define_day = $item->get_meta( 'define_day' );

                // Pick-up date
                $pickup_date = $item->get_meta( 'ovabrw_pickup_date' );

                if ( strtotime( $pickup_date ) ) {
                    $item->update_meta_data( 'ovabrw_pickup_date_strtotime', strtotime( $pickup_date ) );

                    if ( 'hotel' === $define_day ) {
                        // Convert date
                        $pickup_date = gmdate( OVABRW()->options->get_date_format(), strtotime( $pickup_date ) );

                        // Update pick-up date
                        $item->update_meta_data( 'ovabrw_pickup_date_real', $pickup_date . ' ' . ovabrw_get_hotel_pickup_time() );
                    } elseif ( 'day' === $define_day ) {
                        // Convert date
                        $pickup_date = gmdate( OVABRW()->options->get_date_format(), strtotime( $pickup_date ) );

                        // Update pick-up date
                        $item->update_meta_data( 'ovabrw_pickup_date_real', $pickup_date . ' 00:00' );
                    } else {
                        $item->update_meta_data( 'ovabrw_pickup_date_real', $pickup_date );
                    }
                }

                // Drop-off date
                $dropoff_date = $item->get_meta( 'ovabrw_pickoff_date' );
                if ( strtotime( $dropoff_date ) ) {
                    $item->update_meta_data( 'ovabrw_pickoff_date_strtotime', strtotime( $dropoff_date ) );

                    if ( 'hotel' === $define_day ) {
                        // Convert date
                        $dropoff_date = gmdate( OVABRW()->options->get_date_format(), strtotime( $dropoff_date ) );

                        // Update drop-off date
                        $item->update_meta_data( 'ovabrw_pickoff_date_real', $dropoff_date . ' ' . ovabrw_get_hotel_dropoff_time() );
                    } elseif ( 'day' === $define_day ) {
                        // Convert date
                        $dropoff_date = gmdate( OVABRW()->options->get_date_format(), strtotime( $dropoff_date ) );

                        // Update drop-off date
                        $item->update_meta_data( 'ovabrw_pickoff_date_real', $dropoff_date . ' 24:00' );
                    } else {
                        $item->update_meta_data( 'ovabrw_pickoff_date_real', $dropoff_date );
                    }
                }
            }
        }
	}

	new OVABRW_Deposit();
}