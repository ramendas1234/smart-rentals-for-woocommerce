<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get locations
$locations = OVABRW()->options->get_locations();

?>

<tr>
    <td width="40%">
        <?php ovabrw_wp_select_input([
            'class'         => 'ovabrw-input-required',
            'name'          => $this->get_meta_name( 'st_pickup_loc[]' ),
            'placeholder'   => esc_html__( 'Select Location', 'ova-brw' ),
            'options'       => $locations
        ]); ?>
    </td>
    <td width="40%">
        <?php ovabrw_wp_select_input([
            'class'         => 'ovabrw-input-required',
            'name'          => $this->get_meta_name( 'st_dropoff_loc[]' ),
            'placeholder'   => esc_html__( 'Select Location', 'ova-brw' ),
            'options'       => $locations,
            'disabled'      => true
        ]); ?>
    </td>
    <td width="19%" class="ovabrw-input-price">
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'class'         => 'ovabrw-input-price',
            'name'          => $this->get_meta_name('st_price_location[]'),
            'data_type'     => 'price',
            'placeholder'   => '10'
        ]); ?>
    </td>
    <td width="1%">
        <button class="button ovabrw-remove-location">x</button>
    </td>
</tr>