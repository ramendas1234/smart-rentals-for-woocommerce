<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field ovabrw_show_number_vehicle_wrap">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Quantity', 'ova-brw' ); ?>
	</strong>
	<?php woocommerce_wp_select([
		'id' 			=> $this->get_meta_name( 'show_number_vehicle' ),
		'label' 		=> esc_html__( 'Show Quantity', 'ova-brw' ),
		'placeholder' 	=> '',
		'options' 		=> [
			'in_setting' 	=> esc_html__( 'Global Setting', 'ova-brw' ),
			'yes'			=> esc_html__( 'Yes', 'ova-brw' ),
			'no'			=> esc_html__( 'No', 'ova-brw' ),
		],
		'value' 		=> $this->get_meta_value( 'show_number_vehicle', 'in_setting' ),
		'desc_tip'		=> true,
		'description'	=> esc_html__( 'Global Setting: Go to WooCommerce >> Settings >> Booking & Rental >> Product Details', 'ova-brw' )
	]); ?>
</div>