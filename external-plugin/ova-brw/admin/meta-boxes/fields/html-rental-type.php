<?php if ( !defined( 'ABSPATH' ) ) exit();

woocommerce_wp_select([
	'id' 			=> $this->get_meta_name( 'price_type' ),
	'class' 		=> 'short ovabrw-input-required',
	'wrapper_class' => 'ovabrw-required',
	'label' 		=> esc_html__( 'Rental Type', 'ova-brw' ),
	'placeholder' 	=> '',
	'desc_tip' 		=> 'true',
	'description' 	=> esc_html__( 'Some fields will show/hide when change this field', 'ova-brw' ),
	'options' 		=> ovabrw_rental_selector(),
	'value' 		=> $this->get_type()
]);