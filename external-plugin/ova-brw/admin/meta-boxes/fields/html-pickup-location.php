<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field manager-show-pickup-location">
	<strong class="ovabrw_heading_section">
		<?php if ( $this->is_type( 'appointment' ) ) {
			esc_html_e( 'Location', 'ova-brw' );
		} else {
			esc_html_e( 'Pick-up Location', 'ova-brw' );
		} ?>
	</strong>
	<?php
		woocommerce_wp_select([
			'id' 			=> $this->get_meta_name( 'show_pickup_location_product' ),
			'label' 		=> esc_html__( 'Show', 'ova-brw' ),
			'placeholder' 	=> '',
			'options' 		=> [
				'in_setting' 	=> esc_html__( 'Category Setting', 'ova-brw' ),
				'yes' 			=> esc_html__( 'Yes', 'ova-brw' ),
				'no' 			=> esc_html__( 'No', 'ova-brw' )
			],
			'value' 		=> $this->get_meta_value( 'show_pickup_location_product', 'in_setting' ),
			'desc_tip'		=> true,
			'description'	=> esc_html__( 'Category Setting: Setup in per category', 'ova-brw' )
		]);

		woocommerce_wp_select([
			'id' 			=> $this->get_meta_name( 'show_other_location_pickup_product' ),
			'label' 		=> esc_html__( 'Enter another location in form', 'ova-brw' ),
			'placeholder' 	=> '',
			'options' 		=> [
				'yes' 	=> esc_html__( 'Yes', 'ova-brw' ),
				'no' 	=> esc_html__( 'No', 'ova-brw' )
			],
			'value' 		=> $this->get_meta_value( 'show_other_location_pickup_product', 'yes' ),
			'desc_tip'		=> true,
			'description'	=> esc_html__( 'Enter another location in booking or request booking form', 'ova-brw' )
		]);
	?>
</div>