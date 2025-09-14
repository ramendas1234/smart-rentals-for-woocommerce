<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Loading reCAPTCHA
OVABRW()->options->loading_recaptcha();

?>

<form
	action=""
	method="POST"
	enctype="multipart/form-data"
	id="booking_form"
	class="ovabrw-form"
	data-run_ajax="<?php esc_attr_e( apply_filters( OVABRW_PREFIX.'booking_form_run_ajax', 'true' ) ); ?>"
	autocomplete="off">
	<div class="ovabrw-product-fields">
		<?php ovabrw_get_template( 'modern/single/detail/booking-form/booking-fields.php' ); ?>
		<?php ovabrw_get_template( 'modern/single/detail/booking-form/booking-custom-checkout-fields.php' ); ?>
	</div>
	<?php ovabrw_get_template( 'modern/single/detail/booking-form/booking-resources.php' ); ?>
	<?php ovabrw_get_template( 'modern/single/detail/booking-form/booking-services.php' ); ?>
	<?php ovabrw_get_template( 'modern/single/detail/booking-form/booking-deposit.php' ); ?>
	<?php ovabrw_get_template( 'modern/single/detail/booking-form/booking-show-total.php' ); ?>
	<?php ovabrw_get_template( 'modern/single/detail/booking-form/booking-submit.php' ); ?>
</form>