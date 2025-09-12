<?php
/**
 * Smart Rentals WC Template Hooks
 */

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Product page hooks
 */
add_action( 'woocommerce_before_add_to_cart_button', 'smart_rentals_wc_add_booking_form', 10 );
add_action( 'woocommerce_single_product_summary', 'smart_rentals_wc_add_price_display', 25 );

/**
 * Shop loop hooks
 */
add_action( 'woocommerce_after_shop_loop_item_title', 'smart_rentals_wc_add_rental_badge', 15 );

/**
 * Add booking form to product page
 */
function smart_rentals_wc_add_booking_form() {
    global $product;
    if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
        smart_rentals_wc_get_booking_form( $product->get_id() );
    }
}

/**
 * Add price display to product page
 */
function smart_rentals_wc_add_price_display() {
    global $product;
    if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
        smart_rentals_wc_get_price_display( $product->get_id() );
    }
}

/**
 * Add rental badge to shop loop
 */
function smart_rentals_wc_add_rental_badge() {
    global $product;
    if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
        echo '<span class="smart-rentals-badge">' . __( 'Rental', 'smart-rentals-wc' ) . '</span>';
    }
}