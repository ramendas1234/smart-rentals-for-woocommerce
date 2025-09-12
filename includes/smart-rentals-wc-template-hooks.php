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
add_action( 'woocommerce_after_shop_loop_item_title', 'smart_rentals_wc_add_rental_badge', 15 );

/**
 * Add rental badge to shop loop
 */
function smart_rentals_wc_add_rental_badge() {
    global $product;
    if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
        echo '<span class="smart-rentals-badge">' . __( 'Rental', 'smart-rentals-wc' ) . '</span>';
    }
}