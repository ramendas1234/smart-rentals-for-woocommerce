<?php
/**
 * Smart Rentals WC Template Hooks
 */

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Template hooks are now handled by the Smart_Rentals_WC_Hooks class
 * This follows the external plugin's pattern exactly
 */

/**
 * Shop loop hooks
 */

//add_action( 'woocommerce_after_shop_loop_item_title', 'smart_rentals_wc_add_rental_badge', 15 );
add_filter( 'woocommerce_loop_add_to_cart_link', 'smart_rentals_wc_book_button', 10, 2 );

/**
 * Add rental badge to shop loop
 */
function smart_rentals_wc_add_rental_badge() {
    global $product;
    if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
        echo '<span class="smart-rentals-badge">' . __( 'Rental', 'smart-rentals-wc' ) . '</span>';
    }
}

/**
 * Change add to cart button text & behavior for Book products in shop page
 */
function smart_rentals_wc_book_button( $button, $product ) {

    // Check for your custom product type (replace 'book' with your actual type slug)
    if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
        $url  = get_permalink( $product->get_id() );
        $text = __( 'Book Now', 'smart-rentals-wc' );

        $button = sprintf(
            '<a href="%s" class="button product_type_book">%s</a>',
            esc_url( $url ),
            esc_html( $text )
        );
    }
    return $button;
}
