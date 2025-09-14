<?php defined( 'ABSPATH' ) || exit; ?>

<div class="rental_item">
    <label for="ovabrw-amount-deposite">
        <?php esc_html_e( 'Deposit Amount', 'ova-brw' ); ?>
    </label>
    <?php ovabrw_text_input([
        'type'          => 'text',
        'id'            => 'ovabrw-amount-deposite',
        'class'         => 'ovabrw_amount_deposite',
        'name'          => 'ovabrw_amount_deposite',
        'key'           => 'ovabrw-item-key',
        'placeholder'   => 0,
        'attrs'         => [
            'data-error-text'   => esc_html__( 'Deposit amount must be greater than 0.', 'ova-brw' ),
            'data-error-total'  => esc_html__( 'Deposit amount must be less than or equal to Cost.', 'ova-brw' ),
        ]
    ]); ?>
</div>