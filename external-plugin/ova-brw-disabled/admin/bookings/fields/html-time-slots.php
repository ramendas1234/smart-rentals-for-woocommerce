<?php defined( 'ABSPATH' ) || exit;

// Use location
$use_location = $this->get_meta_value( 'use_location' );

if ( $use_location ): ?>
    <div class="rental_item ovabrw-time-slots-location-field">
        <label>
            <?php esc_html_e( 'Select Location', 'ova-brw' ); ?>
        </label>
        <div class="ovabrw-time-slots-location"></div>
    </div>
<?php endif; ?>
<div class="rental_item ovabrw-time-slots-field">
    <label for="ovabrw-pickup-date">
        <?php esc_html_e( 'Select Time', 'ova-brw' ); ?>
    </label>
    <div class="ovabrw-time-slots ovabrw-input-required"></div>
</div>