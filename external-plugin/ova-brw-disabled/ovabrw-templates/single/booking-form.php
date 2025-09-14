<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get product ID
$product_id = $product->get_id();

// Loading reCAPTCHA
OVABRW()->options->loading_recaptcha();

// Terms & conditions
$terms_conditions 	= ovabrw_get_option( 'booking_form_terms_conditions' );
$terms_content 		= ovabrw_get_option( 'booking_form_terms_conditions_content' );

?>

<div class="ovabrw_booking_form" id="ovabrw_booking_form">
	<h3 class="title"><?php esc_html_e( 'Booking Form', 'ova-brw' ); ?></h3>
	<form
		id="booking_form"
		class="form ovabrw-form"
		action="<?php home_url('/'); ?>"
		method="POST"
		enctype="multipart/form-data"
		data-run_ajax="<?php echo esc_attr( apply_filters( OVABRW_PREFIX.'booking_form_run_ajax', true ) ); ?>"
		autocomplete="off">
		<div class="ovabrw-container wrap_fields">
			<div class="ovabrw-row">
				<div class="wrap-item two_column">
					<!-- Display Booking Form -->
					<?php
						/**
						 * Hook: ovabrw_booking_form
						 * @hooked: ovabrw_booking_form_fields - 5
						 * @hooked: ovabrw_booking_form_extra_fields - 10
						 * @hooked: ovabrw_booking_form_resource - 15
						 * @hooked: ovabrw_booking_form_services - 20
						 * @hooked: ovabrw_booking_form_deposit - 25
						 * @hooked: ovabrw_booking_form_ajax_total - 30
						 */
						do_action( 'ovabrw_booking_form', $product_id );
					?>
				</div>
			</div>
		</div>
		<?php if ( 'yes' === $terms_conditions && $terms_content ): ?>
			<div class="terms-conditions">
				<label>
					<input
						type="checkbox"
						class="ovabrw-conditions ovabrw-input-required"
						name="ovabrw-booking-terms-conditions"
						value="yes"
					/>
					<span class="terms-conditions-content">
						<?php echo wp_kses_post( $terms_content ); ?>
						<span class="terms-conditions-required">*</span>
					</span>
				</label>
			</div>
		<?php endif; ?>
		<?php if ( OVABRW()->options->get_recaptcha_form( 'booking' ) ): ?>
			<div class="ovabrw-recaptcha">
			    <div id="ovabrw-g-recaptcha-booking"></div>
			    <input
			        type="text"
			        id="ovabrw-recaptcha-booking-token"
			        class="ovabrw-recaptcha-token ovabrw-input-required"
			        name="ovabrw-recaptcha-token"
			    />
			</div>
		<?php endif; ?>
		<button type="submit" class="submit btn_tran">
			<?php esc_html_e( 'Booking', 'ova-brw' ); ?>
		</button>
		<?php ovabrw_text_input([
			'type' 	=> 'hidden',
			'name' 	=> 'product_id',
			'value' => $product_id
		]); ?>
		<?php ovabrw_text_input([
			'type' 	=> 'hidden',
			'name' 	=> 'add-to-cart',
			'value' => $product_id
		]); ?>
	</form>
</div>