<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field manager-show-pickup-date">
	<strong class="ovabrw_heading_section">
		<?php  esc_html_e( 'Pick-up Date', 'ova-brw' ); ?>
	</strong>
	<?php
		// Has time group
		$has_time_group = [ 'day', 'hour', 'mixed', 'period_time', 'transportation' ];

		// Has label pick-up date
		$has_label = [ 'day', 'hour', 'mixed', 'period_time', 'taxi', 'transportation', 'hotel', 'appointment' ];

		if ( in_array( $this->get_type(), $has_time_group ) ) {
			woocommerce_wp_select([
				'id' 			=> $this->get_meta_name( 'manage_time_book_start' ),
				'label' 		=> esc_html__( 'Show the group of time', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 		=> [
					'in_setting' 	=> esc_html__( 'Global Setting', 'ova-brw' ),
					'new_time'		=> esc_html__( 'Choose new group time', 'ova-brw' ),
					'no'			=> esc_html__( 'No', 'ova-brw' )
				],
				'value' 		=> $this->get_meta_value( 'manage_time_book_start', 'in_setting' ),
				'desc_tip' 		=> true,
				'description' 	=> esc_html__( 'Global Setting: WooCommerce >> Settings >> Booking & Rental >> General Tab', 'ova-brw' )
			]);

		   	woocommerce_wp_textarea_input([
		   		'id' 			=> $this->get_meta_name( 'product_time_to_book_start' ),
		        'placeholder' 	=> esc_html__( '07:00, 07:30, 13:00, 18:00', 'ova-brw' ),
		        'label' 		=> '',
		        'value' 		=> $this->get_meta_value( 'product_time_to_book_start' ),
		        'desc_tip' 		=> true,
		        'description' 	=> esc_html__( 'Insert time format: 24hour. Like 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00', 'ova-brw' )
		   	]);

			woocommerce_wp_select([
				'id' 			=> $this->get_meta_name( 'manage_default_hour_start' ),
				'label' 		=> esc_html__( 'Defaul Time', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 		=> [
					'in_setting' 	=> esc_html__( 'Global Setting', 'ova-brw' ),
					'new_time'		=> esc_html__( 'Choose new time', 'ova-brw' )
				],
				'value' 		=> $this->get_meta_value( 'manage_default_hour_start', 'in_setting' ),
				'desc_tip' 		=> true,
				'description' 	=> esc_html__( 'Global Setting: WooCommerce >> Settings >> Booking & Rental >> General Tab', 'ova-brw' )
			]);

		   	woocommerce_wp_text_input([
		   		'id' 			=> $this->get_meta_name( 'product_default_hour_start' ),
		   		'class' 		=> 'ovabrw-timepicker',
		        'placeholder' 	=> esc_html__( '07:00', 'ova-brw' ),
		        'label' 		=> '',
		        'value' 		=> $this->get_meta_value( 'product_default_hour_start' ),
		        'desc_tip' 		=> true,
		        'description' 	=> esc_html__('Insert time format 24hour. Example: 09:00', 'ova-brw')
		   	]);
		}
		
		// Has label
		if ( in_array( $this->get_type(), $has_label ) ) {
			woocommerce_wp_select([
				'id'          	=> $this->get_meta_name( 'label_pickup_date_product' ),
				'label'       	=> esc_html__( 'Rename "Pick-up Date" title by', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 	 	=> [
					'category' 	=> esc_html__( 'Category Setting', 'ova-brw' ),
					'new' 		=> esc_html__( 'New Title', 'ova-brw' )
				],
				'description' 	=> esc_html__('- Category Setting: Get title per Category. <br/> - New Title: Ex. check-in date', 'ova-brw'),
				'desc_tip' 		=> true,
				'value' 		=> $this->get_meta_value( 'label_pickup_date_product', 'category' )
			]);

			woocommerce_wp_text_input([
				'id' 			=> $this->get_meta_name( 'new_pickup_date_product' ),
		        'placeholder' 	=> esc_html__( 'New title', 'ova-brw' ),
		        'label' 		=> '',
		        'value' 		=> $this->get_meta_value( 'new_pickup_date_product' ),
		        'desc_tip' 		=> true,
		        'description' 	=> esc_html__( 'Example: check-in date', 'ova-brw' )
			]);
		}
	?>
</div>