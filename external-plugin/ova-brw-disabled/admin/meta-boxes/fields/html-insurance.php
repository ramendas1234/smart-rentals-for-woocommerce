<?php if ( !defined( 'ABSPATH' ) ) exit();

woocommerce_wp_text_input([
	'id' 			=> $this->get_meta_name( 'amount_insurance' ),
	'label' 		=> esc_html__( 'Amount of insurance', 'ova-brw' ),
	'desc_tip' 		=> 'true',
	'description' 	=> esc_html__( 'This amount will be added to the cart.', 'ova-brw' ),
	'placeholder' 	=> '10.5',
	'data_type' 	=> 'price',
	'value' 		=> $this->get_meta_value( 'amount_insurance' )
]);