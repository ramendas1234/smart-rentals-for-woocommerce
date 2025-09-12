<?php if ( !defined( 'ABSPATH' ) ) exit();

// Durations
$durations = [
	'total' => esc_html__( '/Order', 'ova-brw' )
];

if ( $this->is_type( 'day' ) ) {
	$durations = [
		'days' 	=> esc_html__( '/Day', 'ova-brw' ),
		'total' => esc_html__( '/Order', 'ova-brw' )
	];
} elseif ( $this->is_type( 'hotel' ) ) {
	$durations = [
		'days' 	=> esc_html__( '/Night', 'ova-brw' ),
		'total' => esc_html__( '/Order', 'ova-brw' )
	];
} elseif ( $this->is_type( 'hour' ) ) {
	$durations = [
		'hours' => esc_html__( '/Hour', 'ova-brw' ),
		'total' => esc_html__( '/Order', 'ova-brw' )
	];
} elseif ( $this->is_type( 'mixed' ) || $this->is_type( 'period_time' ) ) {
	$durations = [
		'days' 	=> esc_html__( '/Day', 'ova-brw' ),
		'hours' => esc_html__( '/Hour', 'ova-brw' ),
		'total' => esc_html__( '/Order', 'ova-brw' )
	];
}

?>
<tr>
	<td width="15%">
      	<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'value' 		=> '[resourceID]',
			'name' 			=> $this->get_meta_name( 'resource_id[]' ),
			'placeholder' 	=> esc_html__( 'Not space', 'ova-brw' )
		]); ?>
    </td>
    <td width="29%">
      	<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'resource_name[]' ),
			'placeholder' 	=> esc_html__( 'Name', 'ova-brw' )
		]); ?>
    </td>
    <td width="25%" class="ovabrw-input-price">
      	<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'resource_price[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '10.5'
		]); ?>
    </td>
    <td width="15%">
      	<?php ovabrw_wp_text_input([
			'type' 			=> 'number',
			'name' 			=> $this->get_meta_name( 'resource_quantity[]' ),
			'placeholder' 	=> esc_html__( 'Number', 'ova-brw' )
		]); ?>
    </td>
    <td width="15%">
    	<?php ovabrw_wp_select_input([
    		'class' 	=> 'ovabrw-input-required',
    		'name' 		=> $this->get_meta_name( 'resource_duration_type[]' ),
    		'options' 	=> $durations
    	]); ?>
    </td>
    <td width="1%">
    	<button class="button ovabrw-remove-resource">x</button>
    </td>
</tr>