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
	<td width="25%" class="ovabrw-input-price">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required ovabrw-special-discount-price',
			'name' 			=> $this->get_meta_name( 'rt_discount[index][price][]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '10.5'
		]); ?>
	</td>
	<td width="26%" class="ovabrw-input-price">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required ovabrw-special-discount-min',
			'name' 			=> $this->get_meta_name( 'rt_discount[index][min][]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '1'
		]); ?>
	</td>
	<td width="26%" class="ovabrw-input-price">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required ovabrw-special-discount-max',
			'name' 			=> $this->get_meta_name( 'rt_discount[index][max][]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '2'
		]); ?>
	</td>
	<td width="22%">
		<?php ovabrw_wp_select_input([
			'class' 	=> 'ovabrw-input-required ovabrw-special-discount-duration',
			'name' 		=> $this->get_meta_name( 'rt_discount[index][duration_type][]' ),
			'options' 	=> $durations
		]); ?>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-st-discount">x</button>
	</td>
</tr>