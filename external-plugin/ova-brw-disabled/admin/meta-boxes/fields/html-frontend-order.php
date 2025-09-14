<?php if ( !defined( 'ABSPATH' ) ) exit();

woocommerce_wp_text_input([
	'id' 			=> $this->get_meta_name( 'car_order' ),
	'class' 		=> 'short ',
	'label' 		=> esc_html__( 'Product Display Position', 'ova-brw' ),
	'placeholder' 	=> '1',
	'desc_tip' 		=> 'true',
	'description' 	=> esc_html__( 'Product display position in the  product listing page. Use in some elements.', 'ova-brw' ),
	'type' 			=> 'number',
	'value' 		=> $this->get_meta_value( 'car_order' )
]);