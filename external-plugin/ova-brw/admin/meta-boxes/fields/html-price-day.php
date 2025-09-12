<?php if ( !defined( 'ABSPATH' ) ) exit();

woocommerce_wp_text_input([
	'id' 			=> $this->get_meta_name( 'regular_price_day' ),
	'class' 		=> 'ovabrw-input-required',
	'wrapper_class' => 'ovabrw-required',
	'label' 		=> esc_html__( 'Regular price / Day', 'ova-brw' ),
	'placeholder' 	=> '',
	'desc_tip'    	=> true,
	'description' 	=> esc_html__( 'Regular price by day', 'ova-brw' ),
	'data_type' 	=> 'price',
	'value' 		=> $this->get_meta_value( 'regular_price_day' )
]);