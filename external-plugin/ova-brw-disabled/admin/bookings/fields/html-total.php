<?php defined( 'ABSPATH' ) || exit;

// Currency
$currency = ovabrw_get_meta_data( 'currency', $args );

?>
<div class="rental_item ovabrw-total-cost">
    <label for="ovabrw-total">
        <?php esc_html_e( 'Cost', 'ova-brw' ); ?>
    </label>
    <?php ovabrw_text_input([
        'type'          => 'text',
        'class'         => 'ovabrw_total',
        'id'            => 'ovabrw-total',
        'name'          => 'ovabrw_total',
        'key'           => 'ovabrw-item-key',
        'placeholder'   => 0,
        'readonly'      => apply_filters( $this->prefix.'create_booking_edit_cost', 'readonly' ) ? true : false
    ]); ?>
    <span class="ovabrw-current-currency">
        <?php echo get_woocommerce_currency_symbol( $currency ); ?>
    </span>
</div>