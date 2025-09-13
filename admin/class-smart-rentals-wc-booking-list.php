<?php
/**
 * Smart Rentals WC Booking List Table
 */

if ( !defined( 'ABSPATH' ) ) exit();

// Require WP_List_Table class
if ( !class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

/**
 * Smart_Rentals_WC_Booking_List class
 */
if ( !class_exists( 'Smart_Rentals_WC_Booking_List' ) ) {

    class Smart_Rentals_WC_Booking_List extends WP_List_Table {

        /**
         * Constructor
         */
        public function __construct() {
            // Set parent defaults
            parent::__construct([
                'singular' => 'booking',
                'plural'   => 'bookings',
                'ajax'     => false
            ]);
        }

        /**
         * Get columns
         */
        public function get_columns() {
            return [
                'order_id'      => __( 'Order ID', 'smart-rentals-wc' ),
                'customer'      => __( 'Customer', 'smart-rentals-wc' ),
                'product'       => __( 'Product', 'smart-rentals-wc' ),
                'rental_period' => __( 'Rental Period', 'smart-rentals-wc' ),
                'quantity'      => __( 'Quantity', 'smart-rentals-wc' ),
                'total'         => __( 'Total', 'smart-rentals-wc' ),
                'order_status'  => __( 'Status', 'smart-rentals-wc' ),
                'actions'       => __( 'Actions', 'smart-rentals-wc' ),
            ];
        }

        /**
         * Get sortable columns
         */
        public function get_sortable_columns() {
            return [
                'order_id'      => [ 'order_id', false ],
                'customer'      => [ 'customer', false ],
                'product'       => [ 'product', false ],
                'rental_period' => [ 'pickup_date', false ],
                'order_status'  => [ 'order_status', false ],
            ];
        }

        /**
         * Column default
         */
        public function column_default( $item, $column_name ) {
            switch ( $column_name ) {
                case 'order_id':
                case 'customer':
                case 'product':
                case 'rental_period':
                case 'quantity':
                case 'total':
                case 'order_status':
                case 'actions':
                    return $item[$column_name];
                default:
                    return print_r( $item, true );
            }
        }

        /**
         * Column: Order ID
         */
        public function column_order_id( $item ) {
            $order_id = $item['order_id'];
            $edit_url = admin_url( 'post.php?post=' . $order_id . '&action=edit' );
            
            return sprintf(
                '<a href="%s" target="_blank"><strong>#%s</strong></a>',
                esc_url( $edit_url ),
                esc_html( $order_id )
            );
        }

        /**
         * Column: Customer
         */
        public function column_customer( $item ) {
            $customer_name = $item['customer'];
            $customer_email = $item['customer_email'] ?? '';
            
            $output = '<strong>' . esc_html( $customer_name ) . '</strong>';
            if ( $customer_email ) {
                $output .= '<br><small>' . esc_html( $customer_email ) . '</small>';
            }
            
            return $output;
        }

        /**
         * Column: Product
         */
        public function column_product( $item ) {
            $product_name = $item['product'];
            $product_id = $item['product_id'] ?? 0;
            
            if ( $product_id ) {
                $edit_url = admin_url( 'post.php?post=' . $product_id . '&action=edit' );
                return sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    esc_url( $edit_url ),
                    esc_html( $product_name )
                );
            }
            
            return esc_html( $product_name );
        }

        /**
         * Column: Rental Period
         */
        public function column_rental_period( $item ) {
            $pickup_date = $item['pickup_date'];
            $dropoff_date = $item['dropoff_date'];
            
            $output = '<strong>' . __( 'Pickup:', 'smart-rentals-wc' ) . '</strong> ' . esc_html( $pickup_date ) . '<br>';
            $output .= '<strong>' . __( 'Dropoff:', 'smart-rentals-wc' ) . '</strong> ' . esc_html( $dropoff_date );
            
            return $output;
        }

        /**
         * Column: Order Status
         */
        public function column_order_status( $item ) {
            $status = $item['order_status'];
            $status_name = $item['status_name'];
            
            $status_class = 'status-' . sanitize_html_class( str_replace( 'wc-', '', $status ) );
            
            return sprintf(
                '<mark class="order-status %s"><span>%s</span></mark>',
                esc_attr( $status_class ),
                esc_html( $status_name )
            );
        }

        /**
         * Column: Actions
         */
        public function column_actions( $item ) {
            $order_id = $item['order_id'];
            $edit_url = admin_url( 'post.php?post=' . $order_id . '&action=edit' );
            
            $actions = [
                'edit' => sprintf(
                    '<a href="%s" target="_blank">%s</a>',
                    esc_url( $edit_url ),
                    __( 'Edit Order', 'smart-rentals-wc' )
                ),
            ];
            
            return $this->row_actions( $actions );
        }

        /**
         * Prepare items
         */
        public function prepare_items() {
            global $wpdb;

            // Get parameters
            $per_page = 20;
            $current_page = $this->get_pagenum();
            $offset = ( $current_page - 1 ) * $per_page;

            // Get filters
            $order_id = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : 0;
            $customer_name = isset( $_GET['customer_name'] ) ? sanitize_text_field( $_GET['customer_name'] ) : '';
            $product_id = isset( $_GET['product_id'] ) ? intval( $_GET['product_id'] ) : 0;
            $order_status = isset( $_GET['order_status'] ) ? sanitize_text_field( $_GET['order_status'] ) : '';
            $from_date = isset( $_GET['from_date'] ) ? sanitize_text_field( $_GET['from_date'] ) : '';
            $to_date = isset( $_GET['to_date'] ) ? sanitize_text_field( $_GET['to_date'] ) : '';
            $search_by = isset( $_GET['search_by'] ) ? sanitize_text_field( $_GET['search_by'] ) : '';

            // Build query
            $where_conditions = [ "1=1" ];
            
            // Order ID filter
            if ( $order_id ) {
                $where_conditions[] = $wpdb->prepare( "p.ID = %d", $order_id );
            }
            
            // Customer name filter
            if ( $customer_name ) {
                $where_conditions[] = $wpdb->prepare( 
                    "(p.post_excerpt LIKE %s OR pm_billing_first_name.meta_value LIKE %s OR pm_billing_last_name.meta_value LIKE %s)",
                    '%' . $wpdb->esc_like( $customer_name ) . '%',
                    '%' . $wpdb->esc_like( $customer_name ) . '%',
                    '%' . $wpdb->esc_like( $customer_name ) . '%'
                );
            }
            
            // Product filter
            if ( $product_id ) {
                $where_conditions[] = $wpdb->prepare( "oim_product_id.meta_value = %d", $product_id );
            }
            
            // Order status filter
            if ( $order_status ) {
                $where_conditions[] = $wpdb->prepare( "p.post_status = %s", $order_status );
            }
            
            // Date filters
            if ( $from_date && $search_by ) {
                if ( $search_by === 'pickup_date' ) {
                    $where_conditions[] = $wpdb->prepare( "oim_pickup.meta_value >= %s", $from_date );
                } elseif ( $search_by === 'dropoff_date' ) {
                    $where_conditions[] = $wpdb->prepare( "oim_dropoff.meta_value >= %s", $from_date );
                }
            }
            
            if ( $to_date && $search_by ) {
                if ( $search_by === 'pickup_date' ) {
                    $where_conditions[] = $wpdb->prepare( "oim_pickup.meta_value <= %s", $to_date );
                } elseif ( $search_by === 'dropoff_date' ) {
                    $where_conditions[] = $wpdb->prepare( "oim_dropoff.meta_value <= %s", $to_date );
                }
            }

            $where_clause = implode( ' AND ', $where_conditions );

            // Main query
            $query = "
                SELECT DISTINCT
                    p.ID as order_id,
                    p.post_status as order_status,
                    p.post_date as order_date,
                    CONCAT(COALESCE(pm_billing_first_name.meta_value, ''), ' ', COALESCE(pm_billing_last_name.meta_value, '')) as customer_name,
                    pm_billing_email.meta_value as customer_email,
                    oi.order_item_name as product_name,
                    oi.order_item_id,
                    oim_pickup.meta_value as pickup_date,
                    oim_dropoff.meta_value as dropoff_date,
                    oim_quantity.meta_value as rental_quantity,
                    pm_order_total.meta_value as order_total,
                    oim_product_id.meta_value as product_id
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->prefix}woocommerce_order_items oi ON p.ID = oi.order_id
                INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim_is_rental 
                    ON oi.order_item_id = oim_is_rental.order_item_id 
                    AND oim_is_rental.meta_key = '" . smart_rentals_wc_meta_key( 'is_rental' ) . "'
                    AND oim_is_rental.meta_value = 'yes'
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim_pickup 
                    ON oi.order_item_id = oim_pickup.order_item_id 
                    AND oim_pickup.meta_key = '" . smart_rentals_wc_meta_key( 'pickup_date' ) . "'
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim_dropoff 
                    ON oi.order_item_id = oim_dropoff.order_item_id 
                    AND oim_dropoff.meta_key = '" . smart_rentals_wc_meta_key( 'dropoff_date' ) . "'
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim_quantity 
                    ON oi.order_item_id = oim_quantity.order_item_id 
                    AND oim_quantity.meta_key = '" . smart_rentals_wc_meta_key( 'rental_quantity' ) . "'
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim_product_id 
                    ON oi.order_item_id = oim_product_id.order_item_id 
                    AND oim_product_id.meta_key = '_product_id'
                LEFT JOIN {$wpdb->postmeta} pm_billing_first_name 
                    ON p.ID = pm_billing_first_name.post_id 
                    AND pm_billing_first_name.meta_key = '_billing_first_name'
                LEFT JOIN {$wpdb->postmeta} pm_billing_last_name 
                    ON p.ID = pm_billing_last_name.post_id 
                    AND pm_billing_last_name.meta_key = '_billing_last_name'
                LEFT JOIN {$wpdb->postmeta} pm_billing_email 
                    ON p.ID = pm_billing_email.post_id 
                    AND pm_billing_email.meta_key = '_billing_email'
                LEFT JOIN {$wpdb->postmeta} pm_order_total 
                    ON p.ID = pm_order_total.post_id 
                    AND pm_order_total.meta_key = '_order_total'
                WHERE {$where_clause}
                    AND p.post_type = 'shop_order'
                    AND p.post_status != 'trash'
                    AND oi.order_item_type = 'line_item'
                ORDER BY p.post_date DESC
                LIMIT %d OFFSET %d
            ";

            $results = $wpdb->get_results( $wpdb->prepare( $query, $per_page, $offset ), ARRAY_A );

            // Get total count
            $count_query = "
                SELECT COUNT(DISTINCT p.ID)
                FROM {$wpdb->posts} p
                INNER JOIN {$wpdb->prefix}woocommerce_order_items oi ON p.ID = oi.order_id
                INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim_is_rental 
                    ON oi.order_item_id = oim_is_rental.order_item_id 
                    AND oim_is_rental.meta_key = '" . smart_rentals_wc_meta_key( 'is_rental' ) . "'
                    AND oim_is_rental.meta_value = 'yes'
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim_pickup 
                    ON oi.order_item_id = oim_pickup.order_item_id 
                    AND oim_pickup.meta_key = '" . smart_rentals_wc_meta_key( 'pickup_date' ) . "'
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim_dropoff 
                    ON oi.order_item_id = oim_dropoff.order_item_id 
                    AND oim_dropoff.meta_key = '" . smart_rentals_wc_meta_key( 'dropoff_date' ) . "'
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta oim_product_id 
                    ON oi.order_item_id = oim_product_id.order_item_id 
                    AND oim_product_id.meta_key = '_product_id'
                LEFT JOIN {$wpdb->postmeta} pm_billing_first_name 
                    ON p.ID = pm_billing_first_name.post_id 
                    AND pm_billing_first_name.meta_key = '_billing_first_name'
                LEFT JOIN {$wpdb->postmeta} pm_billing_last_name 
                    ON p.ID = pm_billing_last_name.post_id 
                    AND pm_billing_last_name.meta_key = '_billing_last_name'
                LEFT JOIN {$wpdb->postmeta} pm_billing_email 
                    ON p.ID = pm_billing_email.post_id 
                    AND pm_billing_email.meta_key = '_billing_email'
                WHERE {$where_clause}
                    AND p.post_type = 'shop_order'
                    AND p.post_status != 'trash'
                    AND oi.order_item_type = 'line_item'
            ";

            $total_items = $wpdb->get_var( $count_query );

            // Format data
            $formatted_data = [];
            foreach ( $results as $row ) {
                $order_status = $row['order_status'];
                $status_name = wc_get_order_status_name( $order_status );
                
                $formatted_data[] = [
                    'order_id'      => $row['order_id'],
                    'customer'      => trim( $row['customer_name'] ),
                    'customer_email' => $row['customer_email'],
                    'product'       => $row['product_name'],
                    'product_id'    => $row['product_id'],
                    'pickup_date'   => $row['pickup_date'] ? date( 'Y-m-d H:i', strtotime( $row['pickup_date'] ) ) : '-',
                    'dropoff_date'  => $row['dropoff_date'] ? date( 'Y-m-d H:i', strtotime( $row['dropoff_date'] ) ) : '-',
                    'rental_period' => '', // Will be filled by column method
                    'quantity'      => $row['rental_quantity'] ?: 1,
                    'total'         => smart_rentals_wc_price( $row['order_total'] ?: 0 ),
                    'order_status'  => $order_status,
                    'status_name'   => $status_name,
                    'actions'       => '', // Will be filled by column method
                ];
            }

            // Set items and pagination
            $this->items = $formatted_data;
            
            $this->set_pagination_args([
                'total_items' => $total_items,
                'per_page'    => $per_page,
                'total_pages' => ceil( $total_items / $per_page )
            ]);

            // Set column headers
            $this->_column_headers = [
                $this->get_columns(),
                [],
                $this->get_sortable_columns()
            ];
        }
    }
}