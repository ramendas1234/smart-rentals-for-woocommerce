<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get rental product
$rental_product = OVABRW()->rental->get_rental_product( $product->get_id() );
if ( !$rental_product ) return;

// Get card template
$card = ovabrw_get_meta_data( 'card_template', $args, ovabrw_get_card_template() );

// Check show button
if ( 'yes' !== ovabrw_get_option( 'glb_' . $card . '_button', 'yes' ) ) return;

// Get cart item data
$cart_item_data = $rental_product->get_add_to_cart_data();

// Get product URL
$product_url = $rental_product->get_permalink();

if ( apply_filters( OVABRW_PREFIX.'ajax_add_to_cart', true ) && ovabrw_array_exists( $cart_item_data ) ) : ?>
    <button
        class="ovabrw-button ovabrw-add-to-cart"
        data-product-id="<?php echo esc_attr( $product->get_id() ); ?>"
        data-product-url="<?php echo esc_url( $product_url ); ?>"
        cart-item-data="<?php echo esc_attr( json_encode( $cart_item_data ) ); ?>">
        <?php esc_html_e( 'Add to cart', 'ova-brw' ); ?>
        <i class="brwicon-right" aria-hidden="true"></i>
        <i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
    </button>
<?php elseif ( $product_url ): ?>
    <a href="<?php echo esc_url( $product_url ); ?>" class="ovabrw-button">
        <?php esc_html_e( 'Book Now', 'ova-brw' ); ?>
        <i class="brwicon-right" aria-hidden="true"></i>
    </a>
<?php endif; ?>
