<?php if ( !defined( 'ABSPATH' ) ) exit();

woocommerce_wp_select([
	'id' 			=> $this->get_meta_name( 'define_1_day' ),
	'class' 		=> 'short ovabrw-input-required',
	'wrapper_class' => 'ovabrw-required',
	'label' 		=> esc_html__( 'Charged by', 'ova-brw' ),
	'desc_tip'    	=> 'true',
	'options' 		=> [
		'day'	=> esc_html__( 'Day', 'ova-brw' ),
		'hotel'	=> esc_html__( 'Night', 'ova-brw' ),
		'hour'	=> esc_html__( 'Hour', 'ova-brw' )
	],
	'description' 	=> esc_html__( 'Calculate rental period:<br/> <strong>- Day</strong>: (Drop-off date) - (Pick-up date) + 1 <br/> <strong>- Night</strong>: (Drop-off date) - (Pick-up date) <br/> <strong>- Hour</strong>: (Drop-off date) - (Pick-up date) + X <br/> X = 1:  if (Drop-off Time) - (Pick-up Time) > 0 <br/>X = 0:  if (Drop-off Time) - (Pick-up Time) < 0', 'ova-brw' ),
	'value' 		=> $this->get_meta_value( 'define_1_day', 'day' )
]);