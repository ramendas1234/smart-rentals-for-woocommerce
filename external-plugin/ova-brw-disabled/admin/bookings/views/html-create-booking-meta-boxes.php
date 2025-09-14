<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="sub-item ovabrw-meta">
	<h3 class="title"><?php esc_html_e( 'Add Meta', 'ova-brw' ); ?></h3>
	<?php
		// Before view meta boxes
		do_action( $this->prefix.'before_create_booking_view_meta_boxes' );

		// Loop
		foreach ( $this->get_create_booking_meta_fields() as $field ) {
			// Show/hide field
			if ( !apply_filters( OVABRW_PREFIX."create_booking_{$field}_field", true ) ) {
				continue;
			}

			$file_path = OVABRW_PLUGIN_ADMIN . "bookings/fields/html-{$field}.php";

			if ( file_exists( $file_path ) ) {
				include $file_path;
			}
		} // END loop

		// After view meta boxes
		do_action( $this->prefix.'after_create_booking_view_meta_boxes' );
	?>
</div>