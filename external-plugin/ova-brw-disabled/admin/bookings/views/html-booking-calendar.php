<?php defined( 'ABSPATH' ) || exit;

// Datetimepicker
$datetimepicker = ovabrw_admin_datetimepicker_options();
$datetimepicker['datepicker']['LockPlugin']['minDate'] = gmdate( OVABRW()->options->get_date_format(), current_time( 'timestamp' ) );

// Language
$language = apply_filters( OVABRW_PREFIX.'admin_datepicker_language', ovabrw_get_setting( 'calendar_language_general', 'en-GB' ) );
if ( apply_filters( 'wpml_current_language', NULL ) ) { // WPML
    $language = apply_filters( 'wpml_current_language', NULL );
} elseif ( function_exists('pll_current_language') ) { // Polylang
    $language = pll_current_language();
}

// Get rental product IDs
$product_ids = OVABRW()->options->get_rental_product_ids();

// Get locations
$locations = OVABRW()->options->get_location_ids();

// Get data events
$events = $this->get_events();

?>

<div class="ovabrw-booking-calendar-wrap wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e( 'Booking Calendar', 'ova-brw' ); ?>
    </h1>
    <form class="ovabrw-booking-calendar-filter" method="POST" action="" autocomplete="off">
        <div class="ovabrw-filter-fields">
            <div class="ovabrw-field">
                <select
                    class="ovabrw-select2"
                    name="pid"
                    data-placeholder="<?php esc_attr_e( 'Select product...', 'ova-brw' ); ?>">
                    <option value="">
                        <?php esc_html_e( 'Select product...', 'ova-brw' ); ?>
                    </option>
                    <?php if ( ovabrw_array_exists( $product_ids ) ):
                        foreach ( $product_ids as $product_id ): ?>
                            <option value="<?php echo esc_attr( $product_id ); ?>">
                                <?php echo esc_html( get_the_title( $product_id ) ); ?>
                            </option>
                        <?php endforeach;
                    endif; ?>
                </select>
            </div>
            <?php ovabrw_wp_text_input([
                'type'  => 'submit',
                'class' => 'button filter',
                'name'  => 'filter',
                'value' => esc_attr__( 'Filter', 'ova-brw' ),
                'attrs' => [
                    'title' => esc_attr__( 'Filter', 'ova-brw' )
                ]
            ]); ?>
        </div>
    </form>
    <div class="ovabrw-calendar-container">
        <div id="ovabrw-calendar"></div>
    </div>
    <div class="ovabrw-backbone-modal-backdrop">
        <span class="dashicons dashicons-update-alt"></span>
    </div>
    <?php ovabrw_wp_text_input([
        'type'  => 'hidden',
        'name'  => 'ovabrw-data-events',
        'value' => json_encode( $events )
    ]); ?>
    <?php ovabrw_wp_text_input([
        'type'  => 'hidden',
        'name'  => 'ovabrw-datetimepicker-options',
        'value' => json_encode( $datetimepicker )
    ]); ?>
    <?php ovabrw_wp_text_input([
        'type'  => 'hidden',
        'name'  => 'ovabrw-calendar-options',
        'value' => json_encode([
            'initialView'       => 'dayGridMonth',
            'height'            => 'auto',
            'dayMaxEvents'      => 3,
            'firstDay'          => (int)ovabrw_get_setting( 'calendar_first_day', 1 ),
            'lang'              => $language,
            'eventTimeFormat'   => [
                'hour'      => '2-digit',
                'minute'    => '2-digit',
                'hour12'    => 'H:i' === OVABRW()->options->get_time_format() ? false : true
            ]
        ])
    ]); ?>
    <div class="ovabrw-search-available">
        <h1 class="wp-heading-inline">
            <?php esc_html_e( 'Check available', 'ova-brw' ); ?>
        </h1>
        <form class="ovabrw-booking-items-available" method="POST" action="" autocomplete="off">
            <div class="ovabrw-filter-fields">
                <div class="ovabrw-field">
                    <label>
                        <?php esc_html_e( 'Product', 'ova-brw' ); ?>
                    </label>
                    <select
                        class="ovabrw-select2 ovabrw-input-required"
                        name="pid"
                        data-placeholder="<?php esc_attr_e( 'Select product...', 'ova-brw' ); ?>">
                        <option value="">
                            <?php esc_html_e( 'Select product...', 'ova-brw' ); ?>
                        </option>
                        <?php if ( ovabrw_array_exists( $product_ids ) ):
                            foreach ( $product_ids as $product_id ): ?>
                                <option value="<?php echo esc_attr( $product_id ); ?>">
                                    <?php echo esc_html( get_the_title( $product_id ) ); ?>
                                </option>
                            <?php endforeach;
                        endif; ?>
                    </select>
                </div>
                <?php if ( ovabrw_array_exists( $locations ) ): ?>
                    <div class="ovabrw-field">
                        <label>
                            <?php esc_html_e( 'Pick-up location', 'ova-brw' ); ?>
                        </label>
                        <select
                            class="ovabrw-select2"
                            name="pickup_location"
                            data-placeholder="<?php esc_attr_e( '-- Pick-up location --', 'ova-brw' ); ?>"
                            >
                            <option value="">
                                <?php esc_html_e( '-- Pick-up location --', 'ova-brw' ); ?>
                            </option>
                            <?php foreach ( $locations as $location_id ):
                                $location_name = get_the_title( $location_id );
                                if ( $location_name ) $location_name = trim( $location_name );
                            ?>
                                <option value="<?php echo esc_attr( $location_name ); ?>">
                                    <?php echo esc_html( $location_name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="ovabrw-field">
                        <label>
                            <?php esc_html_e( 'Drop-off location', 'ova-brw' ); ?>
                        </label>
                        <select
                            class="ovabrw-select2"
                            name="dropoff_location"
                            data-placeholder="<?php esc_attr_e( '-- Drop-off location --', 'ova-brw' ); ?>"
                            >
                            <option value="">
                                <?php esc_html_e( '-- Drop-off location --', 'ova-brw' ); ?>
                            </option>
                            <?php foreach ( $locations as $location_id ):
                                $location_name = get_the_title( $location_id );
                                if ( $location_name ) $location_name = trim( $location_name );
                            ?>
                                <option value="<?php echo esc_attr( $location_name ); ?>">
                                    <?php echo esc_html( $location_name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                <?php endif; ?>
                <div class="ovabrw-field">
                    <label>
                        <?php esc_html_e( 'From date', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'type'      => 'text',
                        'id'        => ovabrw_unique_id( 'from_day' ),
                        'class'     => 'start-date ovabrw-input-required',
                        'name'      => 'from_date',
                        'data_type' => 'datetimepicker'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label>
                        <?php esc_html_e( 'To date', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'type'      => 'text',
                        'id'        => ovabrw_unique_id( 'to_day' ),
                        'class'     => 'end-date ovabrw-input-required',
                        'name'      => 'to_date',
                        'data_type' => 'datetimepicker'
                    ]); ?>
                </div>
                <?php ovabrw_wp_text_input([
                    'type'  => 'submit',
                    'class' => 'button filter',
                    'name'  => 'filter',
                    'value' => esc_attr__( 'Filter', 'ova-brw' ),
                    'attrs' => [
                        'title' => esc_attr__( 'Filter', 'ova-brw' )
                    ]
                ]); ?>
                <span class="dashicons dashicons-update-alt"></span>
            </div>
            <h3 class="wp-heading-inline items-available"></h3>
        </form>
    </div>
</div>