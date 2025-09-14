<?php if ( !defined( 'ABSPATH' ) ) exit();

// Require WP_List_Table class
if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * OVABRW Admin Booking List
 */
if ( !class_exists( 'OVABRW_Admin_Booking_List', false ) ) {

    class OVABRW_Admin_Booking_List extends WP_List_Table {

        /**
         * Constructor
         */
        public function __construct() {
            global $page;

            // Set parent defaults
            parent::__construct([
                'singular'  => 'bookings',
                'plural'    => 'bookings',
                'ajax'      => false
            ]);
        }

        /**
         * Column: Default
         */
        public function column_default( $item, $column_name ) {
            switch ( $column_name ) {
                case 'order_id':
                case 'customer':
                case 'time':
                case 'location':
                case 'deposit':
                case 'insurance':
                case 'vehicle':
                case 'product':
                case 'order_status':
                    return apply_filters( OVABRW_PREFIX.'booking_list_column_default', $item[$column_name], $item, $column_name );
                default:
                    // Show the whole array for troubleshooting purposes
                    return print_r( $item, true );
            }
        }

        /**
         * Column: Order status
         */
        public function column_order_status( $item ) {
            // Get order id
            $order_id = ovabrw_get_meta_data( 'order_id', $item );

            // Get order status
            $order_status = ovabrw_get_meta_data( 'order_status', $item );

            switch ( $order_status ) {
                case 'processing':
                    $status_text = esc_html__( 'Processing', 'ova-brw' );
                    break;
                case 'completed':
                    $status_text = esc_html__( 'Completed', 'ova-brw' );
                    break;
                case 'pending':
                    $status_text = esc_html__( 'Pending payment', 'ova-brw' );
                    break;
                case 'on-hold':
                    $status_text = esc_html__( 'On hold', 'ova-brw' );
                    break;
                case 'cancelled':
                    $status_text = esc_html__( 'Cancel', 'ova-brw' );
                    break;    
                case 'closed':
                    $status_text = esc_html__( 'Closed', 'ova-brw' );
                    break;        
                default:
                    $status_text = esc_html__( 'Update order', 'ova-brw' );
                    break;
            }

            ob_start(); ?>
                <mark class="ovabrw-order-status status-<?php echo esc_attr( $order_status ); ?> tips">
                    <span><?php echo esc_html( $status_text ); ?></span>
                </mark>
                <select
                    name="new_order_status"
                    class="update_order_status"
                    data-order_id="<?php echo esc_attr( $order_id ); ?>"
                    data-error-per-msg="<?php esc_attr_e( 'You don\'t have permission to update.', 'ova-brw' ); ?>"
                    data-error-update-msg="<?php esc_attr_e( 'Order status update failed.', 'ova-brw' ); ?>">
                    <option value="">
                        <?php esc_html_e( 'Update status', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-pending">
                        <?php esc_html_e( 'Pending payment', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-processing">
                        <?php esc_html_e( 'Processing', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-on-hold">
                        <?php esc_html_e( 'On hold', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-completed">
                        <?php esc_html_e( 'Completed', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-cancelled">
                        <?php esc_html_e( 'Cancelled', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-refunded">
                        <?php esc_html_e( 'Refunded', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-failed">
                        <?php esc_html_e( 'Failed', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-closed">
                        <?php esc_html_e( 'Closed', 'ova-brw' ); ?>
                    </option>
                    <option value="wc-checkout-draft">
                        <?php esc_html_e( 'Draft', 'ova-brw' ); ?>
                    </option>
                </select>
            <?php

            return ob_get_clean();
        }

        /**
         * Column: ID
         */
        public function column_order_id( $item ) {
            // Get order id
            $order_id = ovabrw_get_meta_data( 'order_id', $item );

            if ( current_user_can( apply_filters( OVABRW_PREFIX.'edit_order_woo_cap' ,'manage_options' ) ) ) {
                // Get edit order url
                $edit_url = get_edit_post_link( $order_id );

                return sprintf( '<span><a href="%s">%s</a></span>', $edit_url, $order_id );
            } else {
                return sprintf( '<span>%s</span>', $order_id );
            }
        }

        /**
         * Get columns
         */
        public function get_columns() {
            $options = $columns = [];

            // Order id
            $order_id = get_option( 'admin_manage_order_show_id', 1 );
            if ( $order_id ) $options['order_id'] = $order_id;

            // Customer
            $customer = get_option( 'admin_manage_order_show_customer', 2 );
            if ( $customer ) $options['customer'] = $customer;

            // Time
            $time = get_option( 'admin_manage_order_show_time', 3 );
            if ( $time ) $options['time'] = $time;

            // Location
            $location = get_option( 'admin_manage_order_show_location', 4 );
            if ( $location ) $options['location'] = $location;

            // Deposit
            $deposit = get_option( 'admin_manage_order_show_deposit', 5 );
            if ( $deposit ) $options['deposit'] = $deposit;

            // Insurance
            $insurance = get_option( 'admin_manage_order_show_insurance', 6 );
            if ( $insurance ) $options['insurance'] = $insurance;

            // Vehicle ID
            $vehicle = get_option( 'admin_manage_order_show_vehicle', 7 );
            if ( $vehicle ) $options['vehicle'] = $vehicle;

            // Product
            $product = get_option( 'admin_manage_order_show_product', 8 );
            if ( $product ) $options['product'] = $product;

            // Order status
            $status = get_option( 'admin_manage_order_show_order_status', 9 );
            if ( $status ) $options['order_status'] = $status;

            // Sort options
            if ( ovabrw_array_exists( $options ) ) asort( $options );

            foreach ( $options as $i => $value ) {
                switch ( $i ) {
                    case 'order_id':
                        $columns[$i] = esc_html__( 'Order', 'ova-brw' );
                        break;
                    case 'customer':
                        $columns[$i] = esc_html__( 'Customer', 'ova-brw' );
                        break;
                    case 'time':
                        $columns[$i] = esc_html__( 'Time', 'ova-brw' );
                        break;
                    case 'location':
                        $columns[$i] = esc_html__( 'Location', 'ova-brw' );
                        break;
                    case 'deposit':
                        $columns[$i] = esc_html__( 'Deposit', 'ova-brw' );
                        break;
                    case 'insurance':
                        $columns[$i] = esc_html__( 'Insurance', 'ova-brw' );
                        break;
                    case 'vehicle':
                        $columns[$i] = esc_html__( 'Vehicle', 'ova-brw' );
                        break;
                    case 'product':
                        $columns[$i] = esc_html__( 'Product', 'ova-brw' );
                        break;
                    case 'order_status':
                        $columns[$i] = esc_html__( 'Order status', 'ova-brw' );
                        break;
                    default:
                        break;
                }
            }

            return apply_filters( OVABRW_PREFIX.'list_orders_get_columns', $columns );
        }

        /**
         * Sortable columns
         */
        public function get_sortable_columns() {
            $sortable_columns = [
                'order_id' => [ 'order_id', true ]
            ];

            return apply_filters( OVABRW_PREFIX.'booking_list_get_sortable_columns', $sortable_columns );
        }

        /**
         * Prepare items
         */
        public function prepare_items() {
            global $wpdb;

            // Order data
            $order_data = [];

            // Per page
            $per_page = apply_filters( OVABRW_PREFIX.'booking_list_per_page', 20 );

            // Columns
            $columns = $this->get_columns();

            // Hidden columns
            $hidden = apply_filters( OVABRW_PREFIX.'booking_list_hidden_columns', [] );

            // Sortable
            $sortable = $this->get_sortable_columns();

            // Column headers
            $this->_column_headers = [ $columns, $hidden, $sortable ];

            // Product ID
            $product_id = sanitize_text_field( ovabrw_get_meta_data( 'product_id', $_GET ) );

            // Product condition
            $product_condition = $product_id ? 'AND oitem_meta.meta_value = '.$product_id : '';

            // Order status
            $order_status = sanitize_text_field( ovabrw_get_meta_data( 'order_status', $_GET ) );

            if ( $order_status ) {
                $order_status = (array)$order_status;
            } else {
                $order_status = [
                    'wc-pending',
                    'wc-processing',
                    'wc-on-hold',
                    'wc-completed',
                    'wc-cancelled',
                    'wc-refunded',
                    'wc-failed',
                    'wc-closed',
                    'wc-checkout-draft'
                ];    
            }

            // Order ID
            $order_id = (int)ovabrw_get_meta_data( 'order_id', $_GET );

            // Get order ids
            $order_ids = [];

            // Query
            if ( OVABRW()->options->custom_orders_table_usage() ) {
                // Order condition
                $order_condition = $order_id ? 'AND o.id = '.$order_id : '';

                // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                $order_ids = $wpdb->get_col( $wpdb->prepare( "
                    SELECT DISTINCT o.id
                    FROM {$wpdb->prefix}wc_orders AS o
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oitems
                    ON o.id = oitems.order_id
                    $order_condition
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                    ON oitems.order_item_id = oitem_meta.order_item_id
                    WHERE o.type = %s
                    AND oitems.order_item_type = %s
                    AND oitem_meta.meta_key = %s
                    $product_condition
                    AND o.status IN ('". implode( "','", array_map( 'esc_sql', $order_status ) ) . "') ORDER BY o.id DESC",
                    [
                        'shop_order',
                        'line_item',
                        '_product_id'
                    ]
                ));
                // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
            } else {
                // Order condition
                $order_condition = $order_id ? 'AND oitems.order_item_id = '.$order_id : '';

                // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                $order_ids = $wpdb->get_col( $wpdb->prepare( "
                    SELECT DISTINCT oitems.order_id
                    FROM {$wpdb->prefix}woocommerce_order_items AS oitems
                    LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oitem_meta
                    ON oitems.order_item_id = oitem_meta.order_item_id
                    $order_condition
                    LEFT JOIN {$wpdb->posts} AS posts ON oitems.order_id = posts.ID
                    WHERE posts.post_type = %s
                    AND oitems.order_item_type = %s
                    AND oitem_meta.meta_key = %s
                    $product_condition
                    AND posts.post_status IN ( '" . implode( "','", $order_status ) . "' ) ORDER BY oitems.order_id DESC",
                    [
                        'shop_order',
                        'line_item',
                        '_product_id'
                    ]
                ));
                // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
            }

            // Customer name
            $customer_name = sanitize_text_field( ovabrw_get_meta_data( 'customer_name', $_GET ) );

            // Search by
            $search_by = sanitize_text_field( ovabrw_get_meta_data( 'search_by', $_GET ) );

            // From date
            $from_date = strtotime( ovabrw_get_meta_data( 'from_date', $_GET ) );

            // To date
            $to_date = strtotime( ovabrw_get_meta_data( 'to_date', $_GET ) );
            
            // Vehicle id
            $vehicle_id = sanitize_text_field( ovabrw_get_meta_data( 'vehicle_id', $_GET ) );

            // Pick-up location
            $pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $_GET ) );

            // Drop-off location
            $dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $_GET ) );

            // Loop
            if ( ovabrw_array_exists( $order_ids ) ) {
                foreach ( $order_ids as $oid ) {
                    // Get order
                    $order = wc_get_order( $oid );
                    if ( !$order ) continue;

                    // Item data
                    $item_data = [
                        'order_id'      => $order->get_id(),
                        'customer'      => '',
                        'time'          => '',
                        'location'      => '',
                        'deposit'       => '',
                        'insurance'     => '',
                        'vehicle'       => '',
                        'product'       => '',
                        'order_status'  => $order->get_status(),
                    ];

                    // Get buyer
                    $buyer = '';
                    if ( ( method_exists( $order, 'get_billing_first_name' ) && $order->get_billing_first_name() ) || ( method_exists( $order, 'get_billing_last_name') && $order->get_billing_last_name() ) ) {
                        /* translators: 1: first name 2: last name */
                        $buyer = trim( sprintf( _x( '%1$s %2$s', 'full name', 'ova-brw' ), $order->get_billing_first_name(), $order->get_billing_last_name() ) );
                    } elseif ( method_exists( $order, 'get_billing_company' ) && $order->get_billing_company() ) {
                        $buyer = trim( $order->get_billing_company() );
                    } elseif ( method_exists( $order, 'get_customer_id' ) && $order->get_customer_id() ) {
                        $user  = get_user_by( 'id', $order->get_customer_id() );
                        $buyer = ucwords( $user->display_name );
                    }

                    // Customer name
                    if ( $customer_name && is_bool( strpos( $buyer, $customer_name ) ) ) continue;
                    $item_data['customer'] = $buyer;

                    // Get order items
                    $order_items = $order->get_items();
                    if ( !ovabrw_array_exists( $order_items ) ) continue;

                    // Loop order items
                    foreach ( $order_items as $item_id => $item ) {
                        // Get product
                        $product = method_exists( $item, 'get_product' ) ? $item->get_product() : '';
                        if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) continue;

                        // Product id
                        $pid = $product->get_id();
                        if ( $product_id && $product_id != $pid ) continue;

                        // Product name
                        if ( ovabrw_get_meta_data( 'product', $item_data ) ) {
                            $item_data['product'] .= '<br><br>&<br><br><a href="'. esc_url( get_edit_post_link( $pid ) ) .'">' . $item->get_name() . '</a>';
                        } else {
                            $item_data['product'] = '<a href="'. esc_url( get_edit_post_link( $pid ) ) .'">' . $item->get_name() . '</a>';
                        }

                        // Vehice id
                        $vehicle_item = $item->get_meta( 'id_vehicle' );
                        if ( $vehicle_id && $vehicle_id != $vehicle_item ) continue;
                        if ( ovabrw_get_meta_data( 'vehicle', $item_data ) ) {
                            $item_data['vehicle'] .= sprintf( ', %s', $vehicle_item );
                        } else {
                            $item_data['vehicle'] = $vehicle_item;
                        }

                        // Pick-up location
                        $pickup_location_item = $item->get_meta( 'ovabrw_pickup_loc' );
                        if ( $pickup_location && $pickup_location != $pickup_location_item ) continue;

                        // Drop-off location
                        $dropoff_location_item = $item->get_meta( 'ovabrw_pickoff_loc' );
                        if ( $dropoff_location && $dropoff_location != $dropoff_location_item ) continue;

                        // Location
                        if ( ovabrw_get_meta_data( 'location', $item_data ) ) {
                            if ( $pickup_location_item && $dropoff_location_item ) {
                                $item_data['location'] .= '<br><br>&<br><br>' . $pickup_location_item . '<br>@<br>' . $dropoff_location_item;
                            } elseif ( $pickup_location_item && !$dropoff_location_item ) {
                                $item_data['location'] .= '<br><br>&<br><br>' . $pickup_location_item;
                            } elseif ( !$pickup_location_item && $dropoff_location_item ) {
                                $item_data['location'] .= '<br><br>&<br><br>' . $dropoff_location_item;
                            }
                        } else {
                            if ( $pickup_location_item && $dropoff_location_item ) {
                                $item_data['location'] = $pickup_location_item . '<br>@<br>' . $dropoff_location_item;
                            } elseif ( $pickup_location_item && !$dropoff_location_item ) {
                                $item_data['location'] .= $pickup_location_item;
                            } elseif ( !$pickup_location_item && $dropoff_location_item ) {
                                $item_data['location'] .= $dropoff_location_item;
                            }
                        }

                        // Pick-up date
                        $pickup_date_item = $item->get_meta( 'ovabrw_pickup_date' );
                        $pickup_timestamp = strtotime( $pickup_date_item );

                        // Drop-off date
                        $dropoff_date_item = $item->get_meta( 'ovabrw_pickoff_date' );
                        $dropoff_timestamp = strtotime( $dropoff_date_item );

                        // Search by
                        if ( $from_date && $to_date ) {
                            if ( 'from_date' === $search_by ) {
                                if ( $from_date < $pickup_timestamp || $from_date > $dropoff_timestamp ) continue;
                            } elseif ( 'to_date' === $search_by ) {
                                if ( $to_date < $pickup_timestamp || $to_date > $dropoff_timestamp ) continue;
                            } else {
                                if ( $from_date > $dropoff_timestamp || $to_date < $pickup_timestamp ) continue;
                            }
                        }

                        // Item time
                        $item_time = '';
                        if ( $pickup_date_item && $dropoff_date_item ) {
                            $item_time = $pickup_date_item . '<br>@<br>' . $dropoff_date_item;
                        } elseif ( $pickup_date_item && !$dropoff_date_item ) {
                            $item_time = $pickup_date_item;
                        } elseif ( !$pickup_date_item && $dropoff_date_item ) {
                            $item_time = $dropoff_date_item;
                        }

                        // Parent Order
                        $parent_order_id = $item->get_meta( 'ovabrw_parent_order_id' );
                        if ( $parent_order_id ) {
                            $item_time = '<mark class="ovabrw-order-view">';
                                $item_time .= '<a href="'.esc_url( get_edit_post_link( $parent_order_id ) ).'" class="button">';
                                    $item_time .= sprintf( __( 'Original Order #%1$s', 'ova-brw' ), $parent_order_id );
                                $item_time .= '</a>';
                            $item_time .= '</mark>';
                        }

                        // Add item time
                        if ( ovabrw_get_meta_data( 'time', $item_data ) ) {
                            $item_data['time'] .= '<br><br>&<br><br>' . $item_time;
                        } else {
                            $item_data['time'] = $item_time;
                        }

                        // Deposit
                        $is_deposit = $order->get_meta( '_ova_has_deposit' );
                        if ( $is_deposit ) {
                            // Deposit status
                            $deposit_status = '';

                            $remaining_amount = (float)$item->get_meta( 'ovabrw_remaining_amount' );
                            if ( $remaining_amount ) {
                                $is_remaining_invoice   = false;
                                $remaining_invoice_ids  = $order->get_meta( '_ova_remaining_invoice_ids' );

                                // Check remaining invoice ids
                                if ( ovabrw_array_exists( $remaining_invoice_ids ) ) {
                                    foreach ( $remaining_invoice_ids as $remaining_invoice_id ) {
                                        $order_remaining_invoice = wc_get_order( $remaining_invoice_id );

                                        if ( !$order_remaining_invoice ) continue;

                                        $original_item_id = absint( $order_remaining_invoice->get_meta( '_ova_original_item_id' ) );

                                        if ( $original_item_id === $item_id ) {
                                            $is_remaining_invoice = true;

                                            // Break out of the loop
                                            break;
                                        }
                                    }
                                }

                                if ( $is_remaining_invoice ) {
                                    $deposit_status .= '<mark class="ovabrw-order-status status-processing">';
                                        $deposit_status .= '<span class="ovabrw-deposit-status">';
                                        $deposit_status .= esc_html__( 'Original Payment', 'ova-brw' );
                                        $deposit_status .= '</span>';
                                    $deposit_status .= '</mark>';
                                } else {
                                    $deposit_status .= '<mark class="ovabrw-order-status status-pending">';
                                        $deposit_status .= '<span class="ovabrw-deposit-status">';
                                        $deposit_status .= esc_html__( 'Partial Payment', 'ova-brw' );
                                        $deposit_status .= '</span>';
                                    $deposit_status .= '</mark>';
                                }
                            } else {
                                $deposit_status .= '<mark class="ovabrw-order-status status-processing">';
                                    $deposit_status .= '<span class="ovabrw-deposit-status">';
                                    $deposit_status .= esc_html__( 'Full Payment', 'ova-brw' );
                                    $deposit_status .= '</span>';
                                $deposit_status .= '</mark>';
                            }

                            // Remaining Invoice
                            $remaining_invoice_ids = $order->get_meta( '_ova_remaining_invoice_ids' );
                            if ( ovabrw_array_exists( $remaining_invoice_ids ) ) {
                                foreach ( $remaining_invoice_ids as $order_remaining_id ) {
                                    $order_remaining_invoice = wc_get_order( $order_remaining_id );

                                    if ( $order_remaining_invoice ) {
                                        $original_item_id = absint( $order_remaining_invoice->get_meta( '_ova_original_item_id' ) );

                                        if ( $original_item_id === $item_id ) {
                                            $deposit_status .= '<mark class="ovabrw-order-view">';
                                                $deposit_status .= '<a href="'.esc_url( $order_remaining_invoice->get_edit_order_url() ).'" class="button">';
                                                $deposit_status .= wp_kses_post( sprintf( __( 'Remaining Invoice #%1$s', 'ova-brw' ), $order_remaining_invoice->get_order_number() ) );
                                                $deposit_status .= '</a>';
                                            $deposit_status .= '</mark>';
                                            break;
                                        }
                                    }
                                }
                            } // END if

                            // Item deposit
                            if ( ovabrw_get_meta_data( 'deposit', $item_data ) ) {
                                $item_data['deposit'] .= '<br><br>&<br><br>' . $deposit_status;
                            } else {
                                $item_data['deposit'] = $deposit_status;
                            }
                        } // END is deposit
                    } // END loop

                    // Insurance
                    $item_data['insurance'] = '';

                    // Get order insurance
                    $order_insurance = $order->get_meta( '_ova_insurance_amount' );
                    if ( '' !== $order_insurance ) {
                        if ( (float)$order_insurance ) {
                            $item_data['insurance'] = '<mark class="ovabrw-order-status status-on-hold">';
                                $item_data['insurance'] .= '<span class="ovabrw-insurance-status">';
                                    $item_data['insurance'] .= esc_html__( 'Received', 'ova-brw' );
                                $item_data['insurance'] .= '</span>';
                            $item_data['insurance'] .= '</mark>';
                        } else {
                            $item_data['insurance'] = '<mark class="ovabrw-order-status status-processing">';
                                $item_data['insurance'] .= '<span class="ovabrw-insurance-status">';
                                    $item_data['insurance'] .= esc_html__( 'Paid for Customers', 'ova-brw' );
                                $item_data['insurance'] .= '</span>';
                            $item_data['insurance'] .= '</mark>';
                        }
                    }

                    // Add order data
                    if ( ovabrw_get_meta_data( 'time', $item_data ) ) {
                        array_push( $order_data, $item_data );
                    }
                } // END loop
            } // END if

            // Sort order data  
            usort( $order_data, function( $a, $b ) {
                // Get orderby
                $orderby = sanitize_text_field( ovabrw_get_meta_data( 'orderby', $_REQUEST, 'order_id' ) );

                // Get order
                $order = sanitize_text_field( ovabrw_get_meta_data( 'order', $_REQUEST, 'asc' ) );

                //If no order, default to asc
                $result = strcmp( $a[$orderby], $b[$orderby] ); //Determine sort order
                return $order === 'asc' ? -$result : $result; //Send final sort direction to usort
            });

            // Get current page
            $current_page   = $this->get_pagenum();
            $total_items    = count( $order_data );
            $order_data     = array_slice( $order_data, ( ( $current_page - 1 ) * $per_page ), $per_page );
           
            $this->items = $order_data;
            $this->set_pagination_args([
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page )
            ]);
        }
    }
}