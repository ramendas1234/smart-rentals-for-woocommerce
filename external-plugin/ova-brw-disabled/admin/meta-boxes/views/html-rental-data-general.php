<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div id="ovabrw-rental" class="options_group show_if_ovabrw_car_rental">
	<?php
		do_action( OVABRW_PREFIX.'before_view_meta_boxes' );

		// Load meta boxes
		$rental_product->view_meta_boxes();

		do_action( OVABRW_PREFIX.'after_view_meta_boxes' );
	?>
	<div class="ovabrw-rental-type-loading">
		<span class="dashicons-before dashicons-update-alt"></span>
	</div>
	<?php
		// Timepicker options
		ovabrw_wp_text_input([
			'type' 	=> 'hidden',
			'name' 	=> 'ovabrw-timepicker-options',
			'value' => wp_json_encode( ovabrw_admin_timepicker_options() )
		]);

		// Datepicker options
		ovabrw_wp_text_input([
			'type' 	=> 'hidden',
			'name' 	=> 'ovabrw-datepicker-options',
			'value' => wp_json_encode( ovabrw_admin_datepicker_options() )
		]);

		// Datetimepicker options
		ovabrw_wp_text_input([
			'type' 	=> 'hidden',
			'name' 	=> 'ovabrw-datetimepicker-options',
			'value' => wp_json_encode( ovabrw_admin_datetimepicker_options() )
		]);
	?>
</div>