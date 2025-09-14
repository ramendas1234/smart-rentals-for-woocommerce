<?php if ( !defined( 'ABSPATH' ) ) exit();

// Durations
$durations = [
	'days' 	=> esc_html__( 'Day(s)', 'ova-brw' ),
	'hours' => esc_html__( 'Hour(s)', 'ova-brw' )
];

if ( $this->is_type( 'day' ) ) {
	$durations = [
		'days' 	=> esc_html__( 'Day(s)', 'ova-brw' )
	];
} elseif ( $this->is_type( 'hotel' ) ) {
	$durations = [
		'days' 	=> esc_html__( 'Night(s)', 'ova-brw' )
	];
} elseif ( $this->is_type( 'hour' ) ) {
	$durations = [
		'hours' => esc_html__( 'Hour(s)', 'ova-brw' )
	];
}

?>

<tr>
    <td width="39%" class="ovabrw-input-price">
        <?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'global_discount_price[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '10.5'
		]); ?>
    </td>
    <td width="20%" class="ovabrw-input-price">
    	<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'global_discount_duration_val_min[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '1'
		]); ?>
    </td>
    <td width="20%" class="ovabrw-input-price">
    	<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'global_discount_duration_val_max[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '2'
		]); ?>
    </td>
    <td width="20%">
    	<?php ovabrw_wp_select_input([
    		'class' 	=> 'ovabrw-input-required',
			'name' 		=> $this->get_meta_name( 'global_discount_duration_type[]' ),
			'options' 	=> $durations
		]); ?>
    </td>
    <td width="1%">
    	<button class="button ovabrw-remove-gb-discount">x</button>
    </td>
</tr>