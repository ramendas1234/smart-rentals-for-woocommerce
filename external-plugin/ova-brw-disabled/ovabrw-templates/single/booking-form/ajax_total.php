<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="ajax_show_total">
	<div class="ajax_loading"></div>
	<div class="show_ajax_content">
		<span class="ovabrw-total-amount"></span>
		<?php if ( 'yes' === ovabrw_get_setting( 'booking_form_show_insurance_amount', 'no' ) ): ?>
			<div class="ovabrw-insurance-amount">
				<span class="show-amount-insurance"></span>
			</div>
		<?php endif; ?>
		<?php if ( 'yes' === ovabrw_get_setting( 'booking_form_show_availables_vehicle', 'yes' ) ): ?>
			<span class="ovabrw-items-available">
				<?php esc_html_e( 'Items available:', 'ova-brw' ); ?>
				<span class="number-available"></span>
			</span>
		<?php endif; ?>
	</div>
	<div class="ajax-show-error"></div>
</div>