<?php defined( 'ABSPATH' ) || exit; ?>

<div class="rental_item ovabrw-amount-remaining">
    <label for="ovabrw-amount-remaining">
        <?php esc_html_e( 'Remaining Amount', 'ova-brw' ); ?>
    </label>
    <?php ovabrw_text_input([
        'type'          => 'text',
        'class'         => 'ovabrw_amount_remaining',
        'id'            => 'ovabrw-amount-remaining',
        'name'          => 'ovabrw_amount_remaining',
        'key'           => 'ovabrw-item-key',
        'placeholder'   => 0,
        'readonly'      => apply_filters( $this->prefix.'create_booking_edit_remaining', 'readonly' ) ? true : false
    ]); ?>
</div>