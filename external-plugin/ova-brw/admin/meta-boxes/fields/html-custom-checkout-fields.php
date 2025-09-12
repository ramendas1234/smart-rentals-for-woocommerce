<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Custom Checkout Field', 'ova-brw' ); ?>
	</strong>
	<?php 
		woocommerce_wp_select([
			'id' 			=> $this->get_meta_name( 'manage_custom_checkout_field' ),
			'label' 		=> esc_html__( 'Display custom fields from', 'ova-brw' ),
			'placeholder' 	=> '',
			'options' 		=> [
				'all' => esc_html__( 'Category Setting', 'ova-brw' ),
				'new' => esc_html__( 'Choose some fields for this product', 'ova-brw' )
			],
			'value' 		=> $this->get_meta_value( 'manage_custom_checkout_field', 'all' ),
			'desc_tip'		=> true,
			'description' 	=> esc_html__( '- Category Setting: Display all fields that setup per category. <br/>- Choose some fields: Only display some fields for this product', 'ova-brw' )
		]);

		woocommerce_wp_textarea_input([
			'id' 			=> $this->get_meta_name( 'product_custom_checkout_field' ),
	        'placeholder' 	=> esc_html__( 'ova_email_field, ova_address_field', 'ova-brw' ),
	        'label' 		=> '',
	        'value' 		=> $this->get_meta_value( 'product_custom_checkout_field' ),
	        'desc_tip'		=> true,
	        'description' 	=> esc_html__( 'Insert name of custom checkout field. Use comma between 2 fields. Ex: ova_email_field, ova_address_field', 'ova-brw' )
		]);
	?>
</div>