<?php if ( !defined( 'ABSPATH' ) ) exit();

// Post ID
$post_id = get_the_id();

// Vehicle ID
$vehicle_id = ovabrw_get_post_meta( $post_id, 'id_vehicle' );

// Required location
$require_location = ovabrw_get_post_meta( $post_id, 'vehicle_require_location', 'no' );

// Vehicle location
$vehicle_location 	= ovabrw_get_post_meta( $post_id, 'id_vehicle_location' );

// Vehicle untime
$vehicle_untime = ovabrw_get_post_meta( $post_id, 'id_vehicle_untime_from_day' );

// Date time format
$date_format = OVABRW()->options->get_date_format();
$time_format = OVABRW()->options->get_time_format();

// Unavailable time
$start_date = $end_date = '';

if ( ovabrw_array_exists( $vehicle_untime ) ) {
    $start_date = ovabrw_get_meta_data( 'startdate', $vehicle_untime );
    $end_date   = ovabrw_get_meta_data( 'enddate', $vehicle_untime );
}

// Get locations
$locations = OVABRW()->options->get_locations();

?>
<div class="ovabrw-vehicle-id">
    <div class="ovabrw-row">
        <label for="<?php ovabrw_meta_key( 'id_vehicle', true ); ?>">
            <?php esc_html_e( 'Id Vehicle (Unique)', 'ova-brw' ); ?>
        </label>
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'id'            => ovabrw_meta_key( 'id_vehicle' ),
            'name'          => ovabrw_meta_key( 'id_vehicle' ),
            'value'         => $vehicle_id,
            'placeholder'   => esc_html__( 'ID Vehicle', 'ova-brw' )
        ]); ?>
    </div>
    <div class="ovabrw-row require_location">
        <label for="ovabrw-require_location">
            <?php esc_html_e( 'Require Location', 'ova-brw' ); ?>
        </label>
        <label class="label_radio" for="ovabrw_loc_yes">
            <?php esc_html_e( 'Yes', 'ova-brw' ); ?>
        </label>
        <?php ovabrw_wp_text_input([
            'type'      => 'radio',
            'id'        => 'ovabrw_loc_yes',
            'name'      => ovabrw_meta_key( 'vehicle_require_location' ),
            'value'     => 'yes',
            'checked'   => 'yes' === $require_location ? true : false
        ]); ?>
        <label class="label_radio" for="ovabrw_loc_no">
            <?php esc_html_e('No', 'ova-brw'); ?>
        </label>
        <?php ovabrw_wp_text_input([
            'type'      => 'radio',
            'id'        => 'ovabrw_loc_no',
            'name'      => ovabrw_meta_key( 'vehicle_require_location' ),
            'value'     => 'no',
            'checked'   => 'no' === $require_location ? true : false
        ]); ?>
    </div>
    <div class="ovabrw-row location_vehicle">
        <label class="loc" for="ovabrw-location">
            <?php esc_html_e( 'Vehicle location', 'ova-brw' ); ?>
        </label>
        <?php ovabrw_wp_select_input([
            'id'            => 'ovabrw-location',
            'name'          => ovabrw_meta_key( 'id_vehicle_location' ),
            'value'         => $vehicle_location,
            'options'       => $locations,
            'placeholder'   => esc_html__( 'Select location', 'ova-brw' )
        ]); ?>
    </div>
    <div class="ovabrw-row ovabrw-unavailable-time">
        <label for="ovabrw-unavailable">
            <?php esc_html_e( 'Unavailable Time', 'ova-brw' ); ?>
        </label>
        <div class="unavailable-field">
            <span class="from">
                <?php esc_html_e( 'From: ', 'ova-brw' ); ?>
            </span>
            <?php ovabrw_wp_text_input([
                'type'      => 'text',
                'id'        => ovabrw_unique_id( 'disabled_from' ),
                'class'     => 'start-date',
                'name'      => ovabrw_meta_key( 'id_vehicle_untime_from_day[startdate]' ),
                'value'     => $start_date,
                'data_type' => 'datetimepicker',
                'attrs'     => [
                    'data-date' => strtotime( $start_date ) ? gmdate( $date_format, strtotime( $start_date ) ) : '',
                    'data-time' => strtotime( $start_date ) ? gmdate( $time_format, strtotime( $start_date ) ) : ''
                ]
            ]); ?>
        </div>
        <div class="unavailable-field">
            <span class="to">
                <?php esc_html_e( 'To: ', 'ova-brw' ); ?>
            </span>
            <?php ovabrw_wp_text_input([
                'type'      => 'text',
                'id'        => ovabrw_unique_id( 'disabled_to' ),
                'class'     => 'end-date',
                'name'      => ovabrw_meta_key( 'id_vehicle_untime_from_day[enddate]' ),
                'value'     => $end_date,
                'data_type' => 'datetimepicker',
                'attrs'     => [
                    'data-date' => strtotime( $end_date ) ? gmdate( $date_format, strtotime( $end_date ) ) : '',
                    'data-time' => strtotime( $end_date ) ? gmdate( $time_format, strtotime( $end_date ) ) : ''
                ]
            ]); ?>
        </div>
    </div>
    <?php ovabrw_wp_text_input([
        'type'  => 'hidden',
        'name'  => 'ovabrw-datetimepicker-options',
        'value' => wp_json_encode( ovabrw_admin_datetimepicker_options() )
    ]); ?>
</div>