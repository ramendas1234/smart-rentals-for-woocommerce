<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get max seats
$max_seats = $this->get_meta_value( 'max_seats' );

woocommerce_wp_text_input([
	'id' 			=> $this->get_meta_name( 'max_seats' ),
	'class' 		=> 'short ',
	'label' 		=> esc_html__( 'Maximum Seats', 'ova-brw' ),
	'placeholder' 	=> '4',
	'type' 			=> 'number',
	'value' 		=> $max_seats,
	'desc_tip' 		=> 'true',
	'description' 	=> esc_html__( 'Use for search form', 'ova-brw' )
]);