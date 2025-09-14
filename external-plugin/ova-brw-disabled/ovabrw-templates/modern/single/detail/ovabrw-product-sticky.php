<?php if ( !defined( 'ABSPATH' ) ) exit();

// Price format
$price_format = ovabrw_get_meta_data( 'price_format', $args );

// Rental product
$rental_product = ovabrw_get_meta_data( 'rental_product', $args );

// Button link
$link = ovabrw_get_meta_data( 'link', $args );

?>

<div class="ovabrw-product-sticky">
    <div class="ovabrw-sticky-content">
        <div class="ovabrw-product-price">
            <span class="ovabrw-regular-price">
                <?php if ( $price_format ):
                    echo wp_kses_post( $price_format );
                else: ?>
                    <label>
                        <?php esc_html_e( 'From', 'ova-brw' ); ?>
                    </label>
                    <?php if ( $rental_product ):
                        echo wp_kses_post( $rental_product->get_price_html() );
                    endif;
                endif; ?>
            </span>
        </div>
        <div class="ovabrw-product-btn">
            <a href="<?php echo esc_attr( $link ); ?>">
                <?php esc_html_e( 'Booking', 'ova-brw' ); ?>
            </a>
        </div>
    </div>
</div>
