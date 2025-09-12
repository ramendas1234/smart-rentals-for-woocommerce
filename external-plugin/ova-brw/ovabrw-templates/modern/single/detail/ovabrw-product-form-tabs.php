<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Loading reCAPTCHA
OVABRW()->options->loading_recaptcha();

// Options
$show_booking = ovabrw_get_setting( 'template_show_booking_form', 'yes' );
$show_request = ovabrw_get_setting( 'template_show_request_booking', 'yes' );

// Elementor settings
$show_form = ovabrw_get_meta_data( 'show_form', $args );

switch ( $show_form ) {
	case 'both':
		$show_booking = $show_request = 'yes';
		break;
	case 'booking':
		$show_booking = 'yes';
		$show_request = '';
		break;
	case 'request':
		$show_booking = '';
		$show_request = 'yes';
		break;
}

if ( 'yes' === $show_booking || 'yes' === $show_request ): ?>
	<div class="ovabrw-product-form-tabs">
		<div class="ovabrw-tab-head">
			<?php if ( 'yes' === $show_booking && 'yes' === $show_request ): ?>
				<div class="item-tab modern-booking-tab active" data-id="modern-booking">
					<?php esc_html_e( 'Booking Form', 'ova-brw' ); ?>
				</div>
				<div class="item-tab modern-request-tab" data-id="modern-request">
					<?php esc_html_e( 'Request Booking', 'ova-brw' ); ?>
				</div>
			<?php elseif ( 'yes' === $show_booking && 'yes' !== $show_request ): ?>
				<div class="item-tab modern-booking-tab active ovabrw-center">
					<?php esc_html_e( 'Booking Form', 'ova-brw' ); ?>
				</div>
			<?php elseif ( 'yes' !== $show_booking && 'yes' === $show_request ): ?>
				<div class="item-tab modern-request-tab active ovabrw-center">
					<?php esc_html_e( 'Request Booking', 'ova-brw' ); ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="ovabrw-tab-content">
			<?php if ( 'yes' === $show_booking && 'yes' === $show_request ): ?>
				<div class="item-content active" id="modern-booking">
					<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-booking.php' ); ?>
				</div>
				<div class="item-content" id="modern-request">
					<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-request.php' ); ?>
				</div>
			<?php elseif ( 'yes' === $show_booking && 'yes' !== $show_request ): ?>
				<div class="item-content active" id="modern-booking">
					<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-booking.php' ); ?>
				</div>
			<?php elseif ( 'yes' !== $show_booking && 'yes' === $show_request ): ?>
				<div class="item-content active" id="modern-request">
					<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-request.php' ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>