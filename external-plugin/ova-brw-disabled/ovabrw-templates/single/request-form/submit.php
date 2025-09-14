<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Terms & conditions
$terms_conditions 	= ovabrw_get_setting( 'request_booking_terms_conditions' );
$terms_content 		= ovabrw_get_setting( 'request_booking_terms_conditions_content' );

if ( 'yes' === $terms_conditions && $terms_content ): ?>
	<div class="terms-conditions">
		<label>
			<?php ovabrw_text_input([
				'type' 	=> 'checkbox',
				'class' => 'ovabrw-conditions ovabrw-input-required',
				'name' 	=> 'ovabrw-request_booking-terms-conditions',
				'value' => 'yes'
			]); ?>
			<span class="terms-conditions-content">
				<?php echo wp_kses_post( $terms_content ); ?>
				<span class="terms-conditions-required">*</span>
			</span>
		</label>
	</div>
<?php endif; ?>
<div class="ovabrw-request-form-error"></div>
<?php if ( OVABRW()->options->get_recaptcha_form( 'request' ) ): ?>
	<div class="ovabrw-recaptcha">
		<div id="ovabrw-g-recaptcha-request"></div>
		<?php ovabrw_text_input([
			'id' 	=> 'ovabrw-recaptcha-request-token',
			'class' => 'ovabrw-recaptcha-token ovabrw-input-required',
			'name' 	=> 'ovabrw-recaptcha-token'
		]); ?>
	</div>
<?php endif; ?>
<button type="submit" class="submit">
	<?php esc_html_e( 'Send', 'ova-brw' ); ?>
	<div class="ajax_loading">
		<div></div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
		<div></div><div></div><div></div><div></div>
	</div>
</button>
<?php ovabrw_text_input([
	'type' 	=> 'hidden',
	'name' 	=> 'product_name',
	'value' => $product->get_title()
]); ?>
<?php ovabrw_text_input([
	'type' 	=> 'hidden',
	'name' 	=> 'product_id',
	'value' => $product->get_id()
]); ?>
<?php ovabrw_text_input([
	'type' 	=> 'hidden',
	'name' 	=> 'request_booking',
	'value' => 'request_booking'
]); ?>