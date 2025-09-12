<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get locations
$locations = OVABRW()->options->get_locations();
if ( !ovabrw_array_exists( $locations ) ) $locations = [];

?>

<tr>
    <td width="30%">
    	<?php ovabrw_wp_select_input([
    		'class' 		=> 'ovabrw-input-required',
    		'name' 			=> $this->get_meta_name( 'pickup_location[]' ),
    		'placeholder' 	=> esc_html__( 'Select Location', 'ova-brw' ),
    		'options' 		=> $locations
    	]); ?>
    </td>
    <td width="30%">
    	<?php ovabrw_wp_select_input([
    		'class' 		=> 'ovabrw-input-required',
    		'name' 			=> $this->get_meta_name( 'dropoff_location[]' ),
    		'placeholder' 	=> esc_html__( 'Select Location', 'ova-brw' ),
    		'options' 		=> $locations,
    		'disabled' 		=> true
    	]); ?>
    </td>
    <td width="20%" class="ovabrw-input-price">
    	<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'price_location[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '10'
		]); ?>
    </td>
    <td width="19%" class="ovabrw-input-price">
    	<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'location_time[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '60'
		]); ?>
    </td>
    <td width="1%">
    	<button class="button ovabrw-remove-location-price">x</button>
    </td>
</tr>