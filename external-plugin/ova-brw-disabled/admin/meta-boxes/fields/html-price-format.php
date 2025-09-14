<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ovabrw-form-field">
	<strong class="ovabrw_heading_section">
		<?php esc_html_e( 'Display Price In Format', 'ova-brw' ); ?>
	</strong>
	<?php
		woocommerce_wp_select([
			'id' 			=> $this->get_meta_name( 'single_price_format' ),
			'label' 		=> esc_html__( 'In Product Template', 'ova-brw' ),
			'options' 		=> [
				'global' 	=> esc_html__( 'Category Setting', 'ova-brw' ),
				'new' 		=> esc_html__( 'New Format', 'ova-brw' )
			],
			'desc_tip'		=> true,
	        'description' 	=> esc_html__( 'Category Setting: Setup in per category.', 'ova-brw' ),
			'value' 		=> $this->get_meta_value( 'single_price_format', 'global' )
		]);

	   	woocommerce_wp_textarea_input([
	   		'id' 			=> $this->get_meta_name( 'single_price_new_format' ),
	        'placeholder' 	=> esc_html__( 'Add new format', 'ova-brw' ),
	        'label' 		=> '',
	        'value' 		=> $this->get_meta_value( 'single_price_new_format' ),
	        'rows' 			=> 3,
	        'desc_tip'		=> true,
	        'description' 	=> __( 'For example: [regular_price] / [unit]<br>
                You can insert text or HTML<br>
                Use shortcodes:<br>
                <em>[unit]</em>: Display Day or Night or Hour or Km or Mi<br>
                <em>[regular_price]</em>: Display regular price by day<br>
                <em>[hour_price]</em>: Display regular price by hour<br>
                <em>[min_daily_price]</em>: Display minimum daily price<br>
                <em>[max_daily_price]</em>: Display maximum daily price<br>
                <em>[min_package_price]</em>: Display minimum package price (rental type: Period)<br>
                <em>[max_package_price]</em>: Display maximum package price (rental type: Period)<br>
                <em>[min_location_price]</em>: Display minimum location price (rental type: Transportation)<br>
                <em>[max_location_price]</em>: Display maximum location price (rental type: Transportation)<br>
                <em>[min_price]</em>: Display minimum timeslot price (rental type: Appointment)<br>
                <em>[max_price]</em>: Display maximum timeslot price (rental type: Appointment)', 'ova-brw' )
	   	]);

		woocommerce_wp_select([
			'id' 			=> $this->get_meta_name( 'archive_price_format' ),
			'label' 		=> esc_html__( 'In Card Template', 'ova-brw' ),
			'options' 		=> [
				'global' 	=> esc_html__( 'Category Setting', 'ova-brw' ),
				'new' 		=> esc_html__( 'New format', 'ova-brw' )
			],
			'desc_tip'		=> true,
	        'description' 	=> esc_html__( 'Category Setting: Setup in per category.', 'ova-brw' ),
			'value' 		=> $this->get_meta_value( 'archive_price_format', 'global' )
		]);

	   	woocommerce_wp_textarea_input([
	   		'id' 			=> $this->get_meta_name( 'archive_price_new_format' ),
	        'placeholder' 	=> esc_html__( 'Add new format', 'ova-brw' ),
	        'label' 		=> '',
	        'value' 		=> $this->get_meta_value( 'archive_price_new_format' ),
	        'rows' 			=> 3,
	        'desc_tip'		=> true,
	        'description' 	=> __( 'For example: [regular_price] / [unit]<br>
                You can insert text or HTML<br>
                Use shortcodes:<br>
                <em>[unit]</em>: Display Day or Night or Hour or Km or Mi<br>
                <em>[regular_price]</em>: Display regular price by day<br>
                <em>[hour_price]</em>: Display regular price by hour<br>
                <em>[min_daily_price]</em>: Display minimum daily price<br>
                <em>[max_daily_price]</em>: Display maximum daily price<br>
                <em>[min_package_price]</em>: Display minimum package price (rental type: Period)<br>
                <em>[max_package_price]</em>: Display maximum package price (rental type: Period)<br>
                <em>[min_location_price]</em>: Display minimum location price (rental type: Transportation)<br>
                <em>[max_location_price]</em>: Display maximum location price (rental type: Transportation)<br>
                <em>[min_price]</em>: Display minimum timeslot price (rental type: Appointment)<br>
                <em>[max_price]</em>: Display maximum timeslot price (rental type: Appointment)', 'ova-brw' )
	   	]);
	?>
</div>