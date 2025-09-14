<?php defined( 'ABSPATH' ) || exit;

// Currency
$currency = ovabrw_get_meta_data( 'currency', $args );

?>

<div class="rental_item ovabrw-price-detial">
    <label for="ovabrw-price-detail">
        <?php esc_html_e( 'Price detail', 'ova-brw' ); ?>
    </label>
    <span class="ovabrw-price">
        <?php echo wp_kses_post( $this->get_price_html( $currency ) ); ?>
    </span>
</div>