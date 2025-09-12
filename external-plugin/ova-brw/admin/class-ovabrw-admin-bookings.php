<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Bookings class.
 */
if ( !class_exists( 'OVABRW_Admin_Bookings' ) ) {

	class OVABRW_Admin_Bookings {

		/**
		 * Instance
		 */
		protected static $_instance = null;
		
		/**
		 * Constructor
		 */
		public function __construct() {
			// Add sub-menu
			add_action( 'admin_menu', [ $this, 'add_submenu' ] );

            // Add noteces
            add_action( 'admin_notices', [ $this, 'notice_error' ] );

            // Create new booking manually
            add_action( 'admin_init', [ $this, 'create_new_booking_manually' ] );
		}

		/**
		 * Add sub-menu: Booking Calendar
		 */
		public function add_submenu() {
            // Manage bookings
            add_submenu_page(
                'ovabrw-settings',
                esc_html__( 'Manage bookings', 'ova-brw' ),
                esc_html__( 'Manage bookings', 'ova-brw' ),
                apply_filters( OVABRW_PREFIX.'submenu_manage_bookings_capability' ,'edit_posts' ),
                'ovabrw-manage-bookings',
                [ $this, 'view_manage_bookings' ],
                3
            );

            // Add new booking
            add_submenu_page(
                'ovabrw-settings',
                esc_html__( 'Add new booking', 'ova-brw' ),
                esc_html__( 'Add new booking', 'ova-brw' ),
                apply_filters( OVABRW_PREFIX.'submenu_add_new_booking_capability' ,'edit_posts' ),
                'ovabrw-create-booking',
                [ $this, 'view_create_booking' ],
                4
            );

            // Booking calendar
			add_submenu_page(
	            'ovabrw-settings',
	            esc_html__( 'Booking calendar', 'ova-brw' ),
	            esc_html__( 'Booking calendar', 'ova-brw' ),
	            apply_filters( OVABRW_PREFIX.'submenu_booking_calendar_capability' ,'edit_posts' ),
	            'ovabrw-booking-calendar',
	            [ $this, 'view_booking_calendar' ],
                5
	        );
		}

        /**
         * View create booking
         */
        public function view_create_booking() {
            include( OVABRW_PLUGIN_ADMIN . 'bookings/views/html-create-booking.php' );
        }

        /**
         * View manage bookings
         */
        public function view_manage_bookings() {
            include( OVABRW_PLUGIN_ADMIN . 'bookings/views/html-manage-bookings.php' );
        }

		/**
		 * View booking calendar
		 */
		public function view_booking_calendar() {
			include( OVABRW_PLUGIN_ADMIN . 'bookings/views/html-booking-calendar.php' );
		}

		/**
		 * Get events
		 */
		public function get_events( $product_id = '', $month = '', $year = '' ) {
			// init
            $events = [];

            // Get booked order ids
            $order_ids = $this->get_booked_order_ids( $product_id, $month, $year );

            // Check products
            $check_products = [];
            if ( $product_id ) {
                $check_products = OVABRW()->options->get_product_ids_multi_lang( $product_id );
            }

            if ( ovabrw_array_exists( $order_ids ) ) {
                // Date format
                $date_format = OVABRW()->options->get_date_format();

                // Datetime format
                $datetime_format = OVABRW()->options->get_datetime_format();

                foreach ( $order_ids as $order_id ) {
                    // Get order
                    $order = wc_get_order( $order_id );
                    if ( !$order ) continue;

                    // Get order permalink
                    $order_permalink = get_edit_post_link( $order_id );
                    
                    // WCFM
                    if ( !is_admin() && function_exists( 'get_wcfm_view_order_url' ) ) {
                        $order_permalink = get_wcfm_view_order_url( $order_id );
                    }

                    // Get items
                    $items = $order->get_items();
                    if ( !ovabrw_array_exists( $items ) ) continue;

                    // Loop items
                    foreach ( $items as $item_id => $item ) {
                        // Product ID
                        $pid = method_exists( $item, 'get_product_id' ) ? $item->get_product_id() : '';

                        // Get product
                        $product = wc_get_product( $pid );
                        if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) continue;
                        if ( ovabrw_array_exists( $check_products ) && !in_array( $pid , $check_products ) ) continue;

                        // Check vendor
                        if ( function_exists( 'wcfm_is_vendor_product' ) && !wcfm_is_vendor_product( $pid ) ) {
                            continue;
                        }

                        // Pick-up date
                        $pickup_date = strtotime( $item->get_meta( 'ovabrw_pickup_date' ) );
                        if ( !$pickup_date ) continue;

                        // Drop-off date
                        $dropoff_date = strtotime( $item->get_meta( 'ovabrw_pickoff_date' ) );
                        if ( !$dropoff_date ) continue;

                        // Start date
                        $start_date = '';
                        if ( $product->has_timepicker() ) {
                            $start_date = gmdate( 'Y-m-d\TH:i:s', $pickup_date );
                        } else {
                            if ( 'hotel' === $product->get_charged_by() ) {
                                $start_date = gmdate( 'Y-m-d\TH:i:s', strtotime( gmdate( 'Y-m-d', $pickup_date ) . ' ' .apply_filters('brw_real_pickup_time_hotel', '14:00') ) );
                            } else {
                                $start_date = gmdate( 'Y-m-d', $pickup_date );
                            }
                        }

                        // End date
                        $end_date = '';
                        if ( $product->has_timepicker( 'dropoff' ) ) {
                            $end_date = gmdate( 'Y-m-d\TH:i:s', $dropoff_date );
                        } else {
                            if ( 'hotel' == $product->get_charged_by() ) {
                                $end_date = gmdate( 'Y-m-d\TH:i:s', strtotime( gmdate( 'Y-m-d', $dropoff_date ) . ' ' . apply_filters('brw_real_dropoff_time_hotel', '11:00') ) );

                            } else {
                                $end_date = gmdate( 'Y-m-d', $dropoff_date + 86400 );
                            }
                        }

                        $events[] = [
                            'title'             => $product->get_title(),
                            'start'             => $start_date,
                            'end'               => $end_date,
                            'classNames'        => 'ovabrw-calendar-event',
                            'url'               => $order_permalink,
                            'backgroundColor'   => ovabrw_get_option( 'glb_primary_color', '#E56E00' ),
                            'borderColor'       => ovabrw_get_option( 'glb_primary_color', '#E56E00' ),
                            'textColor'         => '#FFFFFF',
                            'extendedProps'     => [
                                'htmlEvent' => $this->get_html_popup_event_data( $item, $order )
                            ]
                        ];
                    } // END Loop items
                }
            }

            return apply_filters( OVABRW_PREFIX.'booking_calendar_get_events', $events, $product_id, $month = '', $year );
		}

		/**
		 * Get booked order ids
		 */
		public function get_booked_order_ids( $product_id = '', $month = '', $year = '' ) {
			// Date range
            $date_range = $this->get_date_range( $month, $year );

            // First day last month
            $first_day_last_month = strtotime( $date_range['first_day_last_month'] );

            // Last day next month
            $last_day_next_month = strtotime( $date_range['last_day_next_month'] );

            // init
            $order_ids = [];

            // WordPress Database
            global $wpdb;

            if ( OVABRW()->options->custom_orders_table_usage() ) {
                // Get order IDs by product ID
                if ( $product_id ) {
                    // Get product IDs multi-lang
                    $product_ids = OVABRW()->options->get_product_ids_multi_lang( $product_id );

                    // Get order IDs
                    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                    $order_ids = $wpdb->get_col( $wpdb->prepare( "
                        SELECT DISTINCT o.id
                        FROM {$wpdb->prefix}wc_orders AS o
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi
                            ON o.id = oi.order_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim1
                            ON oi.order_item_id = oim1.order_item_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim2
                            ON oi.order_item_id = oim2.order_item_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim3
                            ON oi.order_item_id = oim3.order_item_id
                        WHERE o.type = %s
                            AND oi.order_item_type = %s
                            AND oim1.meta_key = %s
                            AND oim1.meta_value IN (" . implode( ',', array_map( 'esc_sql', $product_ids ) ) . ")
                            AND oim2.meta_key = %s
                            AND oim2.meta_value >= %d
                            AND oim3.meta_key = %s
                            AND oim3.meta_value <= %d
                            AND o.status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                        [
                            'shop_order',
                            'line_item',
                            '_product_id',
                            'ovabrw_pickup_date_strtotime',
                            $first_day_last_month,
                            'ovabrw_pickoff_date_strtotime',
                            $last_day_next_month
                        ]
                    ));
                    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                } else {
                    // Get order IDs
                    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                    $order_ids = $wpdb->get_col( $wpdb->prepare( "
                        SELECT DISTINCT o.id
                        FROM {$wpdb->prefix}wc_orders AS o
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_items AS oi
                            ON o.id = oi.order_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim1
                            ON oi.order_item_id = oim1.order_item_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim2
                            ON oi.order_item_id = oim2.order_item_id
                        WHERE o.type = %s
                            AND oi.order_item_type = %s
                            AND oim1.meta_key = %s
                            AND oim1.meta_value >= %d
                            AND oim2.meta_key = %s
                            AND oim2.meta_value <= %d
                            AND o.status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                        [
                            'shop_order',
                            'line_item',
                            'ovabrw_pickup_date_strtotime',
                            $first_day_last_month,
                            'ovabrw_pickoff_date_strtotime',
                            $last_day_next_month
                        ]
                    ));
                    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                }
            } else {
                // Get order IDs by product ID
                if ( $product_id ) {
                    // Get product IDs multi-lang
                    $product_ids = OVABRW()->options->get_product_ids_multi_lang( $product_id );

                    // Get order IDs
                    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                    $order_ids = $wpdb->get_col( $wpdb->prepare( "
                        SELECT DISTINCT oi.order_id
                        FROM {$wpdb->prefix}woocommerce_order_items AS oi
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim1
                        ON oi.order_item_id = oim1.order_item_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim2
                        ON oi.order_item_id = oim2.order_item_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim3
                        ON oi.order_item_id = oim3.order_item_id
                        LEFT JOIN {$wpdb->posts} AS p
                        ON oi.order_id = p.ID
                        WHERE p.post_type = %s
                        AND oi.order_item_type = %s
                        AND oim1.meta_key = %s
                        AND oim1.meta_value IN (" . implode( ',', array_map( 'esc_sql', $product_ids ) ) . ")
                        AND oim2.meta_key = %s
                        AND oim2.meta_value >= %d
                        AND oim3.meta_key = %s
                        AND oim3.meta_value <= %d
                        AND p.post_status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                        [
                            'shop_order',
                            'line_item',
                            '_product_id',
                            'ovabrw_pickup_date_strtotime',
                            $first_day_last_month,
                            'ovabrw_pickoff_date_strtotime',
                            $last_day_next_month
                        ]
                    ));
                    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                } else {
                    // phpcs:disable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                    $order_ids = $wpdb->get_col( $wpdb->prepare( "
                        SELECT DISTINCT oi.order_id
                        FROM {$wpdb->prefix}woocommerce_order_items AS oi
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim1
                        ON oi.order_item_id = oim1.order_item_id
                        LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS oim2
                        ON oi.order_item_id = oim2.order_item_id
                        LEFT JOIN {$wpdb->posts} AS p
                        ON oi.order_id = p.ID
                        WHERE p.post_type = %s
                        AND oi.order_item_type = %s
                        AND oim1.meta_key = %s
                        AND oim1.meta_value >= %d
                        AND oim2.meta_key = %s
                        AND oim2.meta_value <= %d
                        AND p.post_status IN ('". implode( "','", array_map( 'esc_sql', ovabrw_get_order_status() ) ) . "')",
                        [
                            'shop_order',
                            'line_item',
                            'ovabrw_pickup_date_strtotime',
                            $first_day_last_month,
                            'ovabrw_pickoff_date_strtotime',
                            $last_day_next_month
                        ]
                    ));
                    // phpcs:enable WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.PreparedSQL.NotPrepared
                }
            }

            return apply_filters( OVABRW_PREFIX.'booking_calendar_get_booked_order_ids', $order_ids, $product_id, $month, $year );
		}

		/**
		 * Get date range
		 */
		public function get_date_range( $month, $year ) {
			// Current month
            if ( !$month ) $month = gmdate('m');

            // Current year
            if ( !$year ) $year = gmdate('Y');

            // First day last month
            $first_day_last_month = (new DateTime("$year-$month-01"))->modify('-1 month')->format('Y-m-01');

            // Last day next month
            $last_day_next_month = (new DateTime("$year-$month-01"))->modify('+2 month')->modify('-1 day')->format('Y-m-d');

            return (array)apply_filters( OVABRW_PREFIX.'booking_calendar_get_date_range', [
                'first_day_last_month'  => $first_day_last_month,
                'last_day_next_month'   => $last_day_next_month
            ], $month, $year );
		}

		/**
		 * Get HTML popup event data
		 */
		public function get_html_popup_event_data( $item, $order ) {
            ob_start();

            include( OVABRW_PLUGIN_ADMIN . 'bookings/views/html-popup-event-data.php' );

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'booking_calendar_get_html_popup_event_data', $html, $item, $order );
		}

        /**
         * Create new booking manually
         */
        public function create_new_booking_manually() {
            // New booking
            $new_booking = ovabrw_get_meta_data( 'ovabrw_create_new_booking', $_POST );
            if ( 'new_booking' === $new_booking ) {
                // Before create new booking
                do_action( OVABRW_PREFIX.'before_create_new_booking' );

                // Validation
                if ( !$this->validation() ) return;

                // Create new order
                try {
                    $args = [
                        'status'        => '',
                        'customer_note' => ovabrw_get_meta_data( 'order_comments', $_POST )
                    ];
                    
                    $new_order  = wc_create_order( $args ); // Create new order
                    $order_id   = $new_order->get_id(); // Get order id

                    // Payment method
                    $payment_method = ovabrw_get_meta_data( 'payment_method', $_POST );
                    if ( $payment_method ) {
                        $new_order->set_payment_method( $payment_method );

                        // Payment title
                        $payment_title = ovabrw_get_meta_data( 'payment_method_title', $_POST );
                        $new_order->set_payment_method_title( $payment_title );
                    }

                    // Billing
                    $billing = [
                        'first_name'    => ovabrw_get_meta_data( 'billing_first_name', $_POST ), // First name
                        'last_name'     => ovabrw_get_meta_data( 'billing_last_name', $_POST ), // Last name
                        'company'       => ovabrw_get_meta_data( 'billing_company', $_POST ), // Company name
                        'address_1'     => ovabrw_get_meta_data( 'billing_address_1', $_POST ), // Address line 1
                        'address_2'     => ovabrw_get_meta_data( 'billing_address_2', $_POST ), // Address line 2
                        'city'          => ovabrw_get_meta_data( 'billing_city', $_POST ), // City
                        'state'         => ovabrw_get_meta_data( 'billing_state', $_POST ), // State or county
                        'postcode'      => ovabrw_get_meta_data( 'billing_postcode', $_POST ), // Postcode or ZIP
                        'country'       => ovabrw_get_meta_data( 'billing_country', $_POST ), // Country code (ISO 3166-1 alpha-2)
                        'email'         => ovabrw_get_meta_data( 'billing_email', $_POST ), // Email address
                        'phone'         => ovabrw_get_meta_data( 'billing_phone', $_POST ) // Phone number
                    ];

                    // Set billing address
                    $new_order->set_address( $billing, 'billing' );

                    // Set customer
                    $user = get_user_by( 'email', ovabrw_get_meta_data( 'billing_email', $_POST ) );
                    if ( $user ) {
                        $new_order->set_customer_id( $user->ID );
                    }

                    // Set shipping address
                    if ( ovabrw_get_meta_data( 'shipping_enable', $_POST ) ) {
                        // Shipping
                        $shipping = [
                            'first_name'    => ovabrw_get_meta_data( 'shipping_first_name', $_POST ), // First name
                            'last_name'     => ovabrw_get_meta_data( 'shipping_last_name', $_POST ), // Last name
                            'company'       => ovabrw_get_meta_data( 'shipping_company', $_POST ), // Company name
                            'address_1'     => ovabrw_get_meta_data( 'shipping_address_1', $_POST ), // Address line 1
                            'address_2'     => ovabrw_get_meta_data( 'shipping_address_2', $_POST ), // Address line 2
                            'city'          => ovabrw_get_meta_data( 'shipping_city', $_POST ), // City
                            'state'         => ovabrw_get_meta_data( 'shipping_state', $_POST ), // State or county
                            'postcode'      => ovabrw_get_meta_data( 'shipping_postcode', $_POST ), // Postcode or ZIP
                            'country'       => ovabrw_get_meta_data( 'shipping_country', $_POST ), // Country code (ISO 3166-1 alpha-2)
                            'email'         => ovabrw_get_meta_data( 'billing_email', $_POST ), // Email address
                            'phone'         => ovabrw_get_meta_data( 'billing_phone', $_POST ) // Phone number
                        ];

                        $new_order->set_address( $shipping, 'shipping' );
                    }

                    // Set date created
                    $new_order->set_date_created( date_i18n( 'Y-m-d H:i:s', current_time( 'timestamp' ) ) );

                    // Has deposit
                    $has_deposit = false;

                    // Total order
                    $total_order = 0;

                    // Deposit
                    $total_deposit = 0;

                    // Remaining
                    $total_remaining = $remaining_tax = 0;

                    // Insurance
                    $total_insurance = $insurance_tax = 0;

                    // Tax
                    $tax_rate_id = $total_tax = 0;

                    // Get product ids
                    $product_ids = ovabrw_get_meta_data( 'ovabrw_product_ids', $_POST, [] );

                    // Loop
                    foreach ( $product_ids as $meta_key => $product_id ) {
                        // Get rental product
                        $rental_product = OVABRW()->rental->get_rental_product( $product_id );
                        if ( !$rental_product ) continue;

                        // Handle item
                        $handle_item = $rental_product->new_booking_handle_item( $meta_key, $_POST, $new_order );
                        if ( ovabrw_array_exists( $handle_item ) ) {
                            // Has deposit
                            if ( ovabrw_get_meta_data( 'is_deposit', $handle_item ) ) {
                                $has_deposit = true;
                            }

                            // Total order
                            $total_order += (float)ovabrw_get_meta_data( 'subtotal', $handle_item );

                            // Deposit
                            $total_deposit += (float)ovabrw_get_meta_data( 'deposit_amount', $handle_item );

                            // Remaining
                            $total_remaining += (float)ovabrw_get_meta_data( 'remaining_amount', $handle_item );

                            // Remaining tax
                            $remaining_tax = (float)ovabrw_get_meta_data( 'remaining_tax', $handle_item );

                            // Insurance
                            $total_insurance = (float)ovabrw_get_meta_data( 'insurance_amount', $handle_item );

                            // Insurance tax
                            $insurance_tax = (float)ovabrw_get_meta_data( 'insurance_tax', $handle_item );

                            // Tax rate id
                            if ( !$tax_rate_id ) {
                                $tax_rate_id = (int)ovabrw_get_meta_data( 'tax_rate_id', $handle_item );
                            }

                            // Tax amount
                            $total_tax = (float)ovabrw_get_meta_data( 'tax_amount', $handle_item );
                        }
                    } // END loop

                    // Deposit
                    if ( $has_deposit ) {
                        // Has deposit
                        $new_order->add_meta_data( '_ova_has_deposit', 1, true );

                        // Deposit amount
                        $new_order->add_meta_data( '_ova_deposit_amount', $total_deposit, true );

                        // Remaining amount
                        $new_order->add_meta_data( '_ova_remaining_amount', $total_remaining, true );

                        // Remaining tax
                        if ( $remaining_tax ) {
                            $new_order->add_meta_data( '_ova_remaining_tax', $remaining_tax, true );
                        }
                    } // END if

                    // Insurance
                    if ( $total_insurance ) {
                        // Total order
                        $total_order += $total_insurance;

                        // Add order meta
                        $new_order->add_meta_data( '_ova_insurance_amount', $total_insurance, true );

                        // Insurace tax
                        if ( $insurance_tax ) {
                            // Total order
                            $total_order += $insurance_tax;

                            // Add order meta
                            $new_order->add_meta_data( '_ova_insurance_tax', $insurance_tax, true );
                        }

                        // Get insurance name
                        $insurance_name = OVABRW()->options->get_insurance_name();

                        // Get item fee
                        $item_fee = new WC_Order_Item_Fee();

                        // Set data
                        $item_fee->set_props([
                            'name'      => $insurance_name,
                            'tax_class' => 0,
                            'amount'    => $total_insurance,
                            'total'     => $total_insurance,
                            'total_tax' => $insurance_tax,
                            'taxes'     => [
                                'total' => [
                                    $tax_rate_id => $insurance_tax
                                ]
                            ],
                            'order_id'  => $order_id
                        ]);

                        // Save
                        $item_fee->save();

                        // Add item fee to order
                        $new_order->add_item( $item_fee );

                        // Add order meta
                        $new_order->add_meta_data( '_ova_insurance_key', sanitize_title( $insurance_name ), true );
                    } // END if

                    // Tax enabled
                    if ( wc_tax_enabled() ) {
                        // Get item tax
                        $item_tax = new WC_Order_Item_Tax();

                        // Set data
                        $item_tax->set_props([
                            'rate_id'            => $tax_rate_id,
                            'tax_total'          => $total_tax,
                            'shipping_tax_total' => 0,
                            'rate_code'          => WC_Tax::get_rate_code( $tax_rate_id ),
                            'label'              => WC_Tax::get_rate_label( $tax_rate_id ),
                            'compound'           => WC_Tax::is_compound( $tax_rate_id ),
                            'rate_percent'       => WC_Tax::get_rate_percent_value( $tax_rate_id )
                        ]);

                        // Save
                        $item_tax->save();

                        // Add item tax to new order
                        $new_order->add_item( $item_tax );
                        $new_order->set_cart_tax( $total_tax );

                        if ( wc_prices_include_tax() ) {
                            $new_order->add_meta_data( '_ova_prices_include_tax', 1, true );
                        }
                    } // END if

                    // Set total order
                    $new_order->set_total( $total_order );

                    // Set order status
                    $order_status = ovabrw_get_meta_data( 'order_status', $_POST, 'pending' );
                    $new_order->set_status( $order_status );

                    // Save order
                    $new_order->save();

                    // After create new booking
                    do_action( OVABRW_PREFIX.'after_create_new_booking' );

                    // Redirect to order detail
                    if ( $order_id ) {
                        wp_safe_redirect( $new_order->get_edit_order_url() );
                        exit();
                    }
                } catch ( Exception $e ) {
                    $_POST['error'] = $e;
                    return;
                } // END create new order
            } // END if
        }

        /**
         * Validation fields
         */
        public function validation() {
            // Permission
            if ( !current_user_can( apply_filters( OVABRW_PREFIX.'create_new_booking_cap' ,'publish_posts' ) ) ) {
                $_POST['error'] = esc_html__( 'You don\'t have permission to create order', 'ova-brw' );
                return false;
            }

            // Order status
            if ( !ovabrw_get_meta_data( 'order_status', $_POST ) ) {
                $_POST['error'] = esc_html__( 'Order status is required.', 'ova-brw' );
                return false;
            }

            // Billing first name
            if ( !ovabrw_get_meta_data( 'billing_first_name', $_POST ) ) {
                $_POST['error'] = esc_html__( 'Billing first name is required.', 'ova-brw' );
                return false;
            }

            // Billing last name
            if ( !ovabrw_get_meta_data( 'billing_last_name', $_POST ) ) {
                $_POST['error'] = esc_html__( 'Billing last name is required.', 'ova-brw' );
                return false;
            }

            // Billing email
            if ( !ovabrw_get_meta_data( 'billing_email', $_POST ) ) {
                $_POST['error'] = esc_html__( 'Billing email is required.', 'ova-brw' );
                return false;
            }

            // Billing address
            if ( !ovabrw_get_meta_data( 'billing_address_1', $_POST ) ) {
                $_POST['error'] = esc_html__( 'Billing street address is required.', 'ova-brw' );
                return false;
            }

            // Billing city
            if ( !ovabrw_get_meta_data( 'billing_city', $_POST ) ) {
                $_POST['error'] = esc_html__( 'Billing city is required.', 'ova-brw' );
                return false;
            }

            // Billing country
            if ( !ovabrw_get_meta_data( 'billing_country', $_POST ) ) {
                $_POST['error'] = esc_html__( 'Billing country is required.', 'ova-brw' );
                return false;
            }

            // Shipping enabled
            $shipping_enabled = ovabrw_get_meta_data( 'ship_to_different_address', $_POST );
            if ( $shipping_enabled ) {
                // Shipping first name
                if ( !ovabrw_get_meta_data( 'shipping_first_name', $_POST ) ) {
                    $_POST['error'] = esc_html__( 'Shipping first name is required.', 'ova-brw' );
                    return false;
                }

                // Shipping last name
                if ( !ovabrw_get_meta_data( 'shipping_last_name', $_POST ) ) {
                    $_POST['error'] = esc_html__( 'Shipping last name is required.', 'ova-brw' );
                    return false;
                }

                // Shipping email
                if ( !ovabrw_get_meta_data( 'shipping_email', $_POST ) ) {
                    $_POST['error'] = esc_html__( 'Shipping email is required.', 'ova-brw' );
                    return false;
                }

                // Shipping address
                if ( !ovabrw_get_meta_data( 'shipping_address_1', $_POST ) ) {
                    $_POST['error'] = esc_html__( 'Shipping street address is required.', 'ova-brw' );
                    return false;
                }

                // Shipping city
                if ( !ovabrw_get_meta_data( 'shipping_city', $_POST ) ) {
                    $_POST['error'] = esc_html__( 'Shipping city is required.', 'ova-brw' );
                    return false;
                }

                // Shipping country
                if ( !ovabrw_get_meta_data( 'shipping_country', $_POST ) ) {
                    $_POST['error'] = esc_html__( 'Shipping country is required.', 'ova-brw' );
                    return false;
                }
            } // END if

            return true;
        }

        /**
         * Notice error
         */
        public function notice_error() {
            if ( isset( $_POST['error'] ) && $_POST['error'] ): ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php esc_html_e( $_POST['error'] ); ?></p>
                </div>
            <?php endif;
        }

		/**
		 * Main Booking Calendar instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}

	new OVABRW_Admin_Bookings();
}