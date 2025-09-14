<?php if ( !defined( 'ABSPATH' ) ) exit();

woocommerce_wp_text_input([
	'id' 			=> $this->get_meta_name( 'regul_price_taxi' ),
	'class' 		=> 'ovabrw-input-required',
	'wrapper_class' => 'ovabrw-required',
	'label' 		=> esc_html__( 'Price Per Kilometer', 'ova-brw' ),
	'placeholder' 	=> '',
	'desc_tip'    	=> 'true',
	'description' 	=> esc_html__( 'Price Per Kilometer', 'ova-brw' ),
	'data_type' 	=> 'price',
	'value' 		=> $this->get_meta_value( 'regul_price_taxi' )
]);

woocommerce_wp_text_input([
	'id' 			=> $this->get_meta_name( 'base_price' ),
	'label' 		=> esc_html__( 'Base Price', 'ova-brw' ),
	'placeholder' 	=> '',
	'desc_tip'    	=> 'true',
	'description' 	=> esc_html__( 'Minimum price for booking', 'ova-brw' ),
	'data_type' 	=> 'price',
	'value' 		=> $this->get_meta_value( 'base_price' )
]);