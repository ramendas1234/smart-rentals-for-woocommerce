<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

?>

<div class="request_booking">
	<h3>
		<?php esc_html_e( 'Send your requirement to us. We will check email and contact you soon.', 'ova-brw' ); ?>
	</h3>
	<form class="form ovabrw-form" id="request_booking" action="<?php echo home_url('/'); ?>" method="post" enctype="multipart/form-data" autocomplete="off">
		<div class="ovabrw-container">
			<div class="ovabrw-row">
				<div class="wrap-item two_column">
					<?php ovabrw_get_template( 'single/request-form/fields.php' ); ?>
					<?php ovabrw_get_template( 'single/request-form/custom-checkout-fields.php' ); ?>
				</div>
			</div>
		</div>
		<?php ovabrw_get_template( 'single/request-form/resources.php' ); ?>
		<?php ovabrw_get_template( 'single/request-form/services.php' ); ?>
		<?php if ( 'yes' == ovabrw_get_setting( 'request_booking_form_show_extra_info', 'yes' ) ): ?>
			<div class="extra">
				<textarea name="extra" cols="50" rows="5" placeholder="<?php esc_html_e( 'Extra Information', 'ova-brw' ); ?>"></textarea>
			</div>
		<?php endif; ?>
		<?php ovabrw_get_template( 'single/request-form/submit.php' ); ?>
	</form>
</div>