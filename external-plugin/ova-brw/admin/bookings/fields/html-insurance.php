<?php defined( 'ABSPATH' ) || exit;

$currency   = ovabrw_get_meta_data( 'currency', $args );
$insurance  = (float)$this->get_meta_value( 'amount_insurance' );

?>
<div class="rental_item">
    <label for="ovabrw-amount-insurance">
        <?php esc_html_e( 'Insurance Amount', 'ova-brw' ); ?>
    </label>
    <?php ovabrw_text_input([
        'type'          => 'text',
        'id'            => 'ovabrw-amount-insurance',
        'class'         => 'ovabrw_amount_insurance',
        'name'          => 'ovabrw_amount_insurance',
        'key'           => 'ovabrw-item-key',
        'value'         => ovabrw_convert_price( $insurance, [ 'currency' => $currency ] ),
        'placeholder'   => 0,
        'readonly'      => true
    ]); ?>
</div>