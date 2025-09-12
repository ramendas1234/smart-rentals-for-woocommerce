<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Extra Tab', 'ova-brw' ); ?>
	</strong>
	<?php 
		woocommerce_wp_select([
			'id' 			=> $this->get_meta_name( 'manage_extra_tab' ),
			'label' 		=> esc_html__( 'Display content by', 'ova-brw' ),
			'placeholder' 	=> '',
			'options' 		=> [
				'in_setting' 	=> esc_html__( 'Global Setting', 'ova-brw' ),
				'new_form' 		=> esc_html__( 'New Content', 'ova-brw' ),
				'no' 			=> esc_html__( 'Empty Content', 'ova-brw' )
			],
			'desc_tip'		=> true,
	        'description' 	=> esc_html__( '- Display Extra Tab beside Description & Reviews Tab. <br/>- Global Setting: WooCommerce >> Settings >> Booking & Rental >> Product Details <br/>- Empty Content: The tab will hide ', 'ova-brw' ),
			'value' 		=> $this->get_meta_value( 'manage_extra_tab', 'in_setting' )
		]);

		woocommerce_wp_textarea_input([
			'id' 			=> $this->get_meta_name('extra_tab_shortcode'),
	        'placeholder' 	=> esc_html__( '[contact-form-7 id="205" title="Contact form 1"]', 'ova-brw' ),
	        'label' 		=> '',
	        'value' 		=> $this->get_meta_value('extra_tab_shortcode'),
	        'desc_tip'		=> true,
	        'description' 	=> esc_html__( 'Insert Shortcode or Text', 'ova-brw' )
		]);
	?>
</div>