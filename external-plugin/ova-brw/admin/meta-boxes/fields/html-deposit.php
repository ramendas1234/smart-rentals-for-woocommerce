<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Deposit', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field">
			<?php
				woocommerce_wp_select([
					'id' 			=> $this->get_meta_name( 'enable_deposit' ),
					'label' 		=> esc_html__( 'Enable deposit', 'ova-brw' ),
					'desc_tip' 		=> 'true',
					'description' 	=> esc_html__( 'An advance payment', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> [
						'no' 	=> esc_html__( 'No', 'ova-brw' ),
						'yes' 	=> esc_html__( 'Yes', 'ova-brw' )
					],
					'value' 		=> $this->get_meta_value( 'enable_deposit', 'no' )
				]);
				woocommerce_wp_select([
					'id' 			=> $this->get_meta_name( 'force_deposit' ),
					'label' 		=> esc_html__( 'Show Full Payment', 'ova-brw' ),
					'desc_tip' 		=> 'true',
					'description' 	=> esc_html__( '- Yes: show full payment option in booking form. <br/>- No: Only deposit payment option', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> [
						'no' 	=> esc_html__( 'No', 'ova-brw' ),
						'yes' 	=> esc_html__( 'Yes', 'ova-brw' )
					],
					'value' 		=> $this->get_meta_value( 'force_deposit', 'no' )
				]);
				woocommerce_wp_select([
					'id' 			=> $this->get_meta_name( 'type_deposit' ),
					'label' 		=> esc_html__( 'Deposit type', 'ova-brw' ),
					'placeholder' 	=> '',
					'options' 		=> [
						'percent'	=> esc_html__( 'a percentage amount of payment ', 'ova-brw' ),
						'value'		=> esc_html__( 'a fixed amount of payment', 'ova-brw' )
					],
					'value' 		=> $this->get_meta_value( 'type_deposit', 'percent' )
				]);
				woocommerce_wp_text_input([
					'id' 					=> $this->get_meta_name( 'amount_deposit' ),
					'label'					=> '',
					'desc_tip'				=> true,
					'description' 			=> esc_html__( 'Insert deposit amount', 'ova-brw' ),
					'placeholder' 			=> '50',
					'data_type' 			=> 'price',
					'value' 				=> $this->get_meta_value( 'amount_deposit' ),
					'custom_attributes' 	=> [
						'data-percent-unit'	=> '%',
						'data-fixed-unit'	=> get_woocommerce_currency_symbol()
					]
				]);
			?>
		</div>
	</div>
</div>