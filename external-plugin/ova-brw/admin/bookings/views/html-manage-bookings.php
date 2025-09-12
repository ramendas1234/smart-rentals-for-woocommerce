<?php if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'OVABRW_Admin_Booking_List' ) ) return;

// Date format
$date_format = OVABRW()->options->get_date_format();

// Time format
$time_format = OVABRW()->options->get_time_format();

// Get booking manage
$booking_manage = new OVABRW_Admin_Booking_List();
$booking_manage->prepare_items();

// Get rental product ids
$product_ids = OVABRW()->options->get_rental_product_ids();

// Get locations
$all_locations = OVABRW()->options->get_location_ids();

// Get vehicle ids
$vehicle_ids = OVABRW()->options->get_vehicle_ids();

// Order ID
$order_id = sanitize_text_field( ovabrw_get_meta_data( 'order_id', $_GET ) );

// Custom name
$customer_name = sanitize_text_field( ovabrw_get_meta_data( 'customer_name', $_GET ) );

// Product ID
$product_id = sanitize_text_field( ovabrw_get_meta_data( 'product_id', $_GET ) );

// Vehicle ID
$vehicle_id = sanitize_text_field( ovabrw_get_meta_data( 'vehicle_id', $_GET ) );

// Search by
$search_by = sanitize_text_field( ovabrw_get_meta_data( 'search_by', $_GET ) );

// From date
$from_date = sanitize_text_field( ovabrw_get_meta_data( 'from_date', $_GET ) );

// To date
$to_date = sanitize_text_field( ovabrw_get_meta_data( 'to_date', $_GET ) );

// Pick-up location
$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $_GET ) );

// Drop-off location
$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $_GET ) );

// Order status
$order_status = sanitize_text_field( ovabrw_get_meta_data( 'order_status', $_GET ) );

?>

<div class="wrap">
    <form id="booking-filter" method="GET" action="<?php echo esc_url( get_admin_url( null, 'admin.php?page=ovabrw-manage-bookings' ) ); ?>">
        <h2><?php esc_html_e( 'Manage Bookings', 'ova-brw' ); ?></h2>
        <div class="booking_filter">
            <?php if ( get_option( 'admin_manage_order_show_id', 1 ) ): // Show order id ?>
                <div class="form-field">
                    <?php ovabrw_wp_text_input([
                        'name'          => 'order_id',
                        'value'         => $order_id,
                        'placeholder'   => esc_html__( 'Order ID', 'ova-brw' )
                    ]); ?>
                </div>
            <?php endif;

            // Show customer name
            if ( get_option( 'admin_manage_order_show_customer', 2 ) ): ?>
                <div class="form-field">
                    <?php ovabrw_wp_text_input([
                        'name'          => 'customer_name',
                        'value'         => $customer_name,
                        'placeholder'   => esc_html__( 'Customer name', 'ova-brw' )
                    ]); ?>
                </div>
            <?php endif;

            // Show filter by dates
            if ( get_option( 'admin_manage_order_show_time', 3 ) ): ?>
                <div class="form-field">
                    <select name="search_by">
                        <option value="">
                            <?php esc_html_e( '-- Search by --', 'ova-brw' ); ?>
                        </option>
                        <option value="from_date"<?php selected( $search_by, 'from_date' ); ?>>
                            <?php esc_html_e( 'From date', 'ova-brw' ); ?>
                        </option>
                        <option value="to_date"<?php selected( $search_by, 'to_date' ); ?>>
                            <?php esc_html_e( 'To date', 'ova-brw' ); ?>
                        </option>
                    </select>
                </div>
                <div class="form-field">
                    <?php ovabrw_wp_text_input([
                        'id'            => ovabrw_unique_id( 'from_date' ),
                        'class'         => 'start-date',
                        'name'          => 'from_date',
                        'value'         => $from_date,
                        'placeholder'   => esc_html__( 'From date', 'ova-brw' ),
                        'data_type'     => 'datetimepicker',
                        'attrs'         => [
                            'data-date' => strtotime( $from_date ) ? gmdate( $date_format, strtotime( $from_date ) ) : '',
                            'data-time' => strtotime( $from_date ) ? gmdate( $time_format, strtotime( $from_date ) ) : ''
                        ]
                    ]); ?>
                </div>
                <div class="form-field">
                    <?php ovabrw_wp_text_input([
                        'id'            => ovabrw_unique_id( 'to_date' ),
                        'class'         => 'end-date',
                        'name'          => 'to_date',
                        'value'         => $to_date,
                        'placeholder'   => esc_html__( 'To date', 'ova-brw' ),
                        'data_type'     => 'datetimepicker',
                        'attrs'         => [
                            'data-date' => strtotime( $to_date ) ? gmdate( $date_format, strtotime( $to_date ) ) : '',
                            'data-time' => strtotime( $to_date ) ? gmdate( $time_format, strtotime( $to_date ) ) : ''
                        ]
                    ]); ?>
                </div>
            <?php endif;

            // Show vehicle
            if ( get_option( 'admin_manage_order_show_vehicle', 7 ) ): ?>
                <div class="form-field">
                    <select name="vehicle_id">
                        <option value="">
                            <?php esc_html_e( '-- Vehicle --', 'ova-brw' ); ?>
                        </option>
                        <?php if ( ovabrw_array_exists( $vehicle_ids ) ):
                            foreach ( $vehicle_ids as $post_id ):
                                $v_id = ovabrw_get_post_meta( $post_id, 'id_vehicle' );
                            ?>
                                <option value="<?php echo esc_attr( $v_id ); ?>"<?php ovabrw_selected( $v_id, $vehicle_id ); ?>>
                                    <?php echo get_the_title( $post_id ); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            <?php endif;

            // Show locations
            if ( get_option( 'admin_manage_order_show_location', 4 ) ): ?>
                <div class="form-field">
                    <select name="pickup_location">
                        <option value="">
                            <?php esc_html_e( '-- Pick-up location --', 'ova-brw' ); ?>
                        </option>
                        <?php if ( ovabrw_array_exists( $all_locations ) ):
                            foreach ( $all_locations as $location_id ): ?>
                                <option value="<?php echo esc_attr( get_the_title( $location_id ) ); ?>"<?php ovabrw_selected( get_the_title( $location_id ), $pickup_location ); ?>>
                                    <?php echo esc_html( get_the_title( $location_id ) ); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
                <div class="form-field">
                    <select name="dropoff_location">
                        <option value="">
                            <?php esc_html_e( '-- Drop-off location --', 'ova-brw' ); ?>
                        </option>
                        <?php if ( ovabrw_array_exists( $all_locations ) ):
                            foreach ( $all_locations as $location_id ): ?>
                                <option value="<?php echo esc_attr( get_the_title( $location_id ) ); ?>"<?php ovabrw_selected( get_the_title( $location_id ), $dropoff_location ); ?>>
                                    <?php echo esc_html( get_the_title( $location_id ) ); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            <?php endif;

            // Show product
            if ( get_option( 'admin_manage_order_show_product', 8 ) ): ?>
                <div class="form-field">
                    <select name="product_id">
                        <option value="">
                            <?php esc_html_e( '-- Choose product --', 'ova-brw' ); ?>
                        </option>
                        <?php if ( ovabrw_array_exists( $product_ids ) ):
                            foreach ( $product_ids as $pid ): ?>
                                <option value="<?php echo esc_attr( $pid ); ?>"<?php ovabrw_selected( $pid, $product_id ); ?>>
                                    <?php echo get_the_title( $pid ); ?>
                                </option>
                        <?php endforeach;
                        endif; ?>
                    </select>
                </div>
            <?php endif;

            // Show order status
            if ( get_option( 'admin_manage_order_show_order_status', 9 ) ): ?>
                <div class="form-field">
                    <select name="order_status" >
                        <option value="">
                            <?php esc_html_e( '-- Order status --', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-completed"<?php ovabrw_selected( $order_status, 'wc-completed' ); ?>>
                            <?php esc_html_e( 'Completed', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-processing"<?php ovabrw_selected( $order_status, 'wc-processing' ); ?>>
                            <?php esc_html_e( 'Processing', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-pending"<?php ovabrw_selected( $order_status, 'wc-pending' ); ?>>
                            <?php esc_html_e( 'Pending payment', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-on-hold"<?php ovabrw_selected( $order_status, 'wc-on-hold' ); ?>>
                            <?php esc_html_e( 'On hold', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-cancelled"<?php ovabrw_selected( $order_status, 'wc-cancelled' ); ?>>
                            <?php esc_html_e( 'Cancel', 'ova-brw' ); ?>
                        </option>
                        <option value="wc-closed"<?php ovabrw_selected( $order_status, 'wc-closed' ); ?>>
                            <?php esc_html_e( 'Closed', 'ova-brw' ); ?>
                        </option>
                    </select>
                </div>
            <?php endif; ?>
            <div class="form-field">
                <button type="submit" class="button">
                    <?php esc_html_e( 'Filter', 'ova-brw' ); ?>
                </button>
            </div>
        </div>
        <?php ovabrw_wp_text_input([
        	'type' 	=> 'hidden',
        	'name' 	=> 'page',
        	'value' => 'ovabrw-manage-bookings'
        ]); ?>
        <?php $booking_manage->display(); ?>
    </form>
    <input
        type="hidden"
        name="ovabrw-datetimepicker-options"
        value="<?php echo esc_attr( wp_json_encode( ovabrw_admin_datetimepicker_options() ) ); ?>"
    />
</div>