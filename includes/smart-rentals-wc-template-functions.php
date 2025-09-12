<?php
/**
 * Smart Rentals WC Template Functions
 */

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Get rental booking form
 */
if ( !function_exists( 'smart_rentals_wc_get_booking_form' ) ) {
    function smart_rentals_wc_get_booking_form( $product_id ) {
        if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
            return;
        }

        smart_rentals_wc_get_template( 'booking-form.php', [
            'product_id' => $product_id,
        ]);
    }
}

/**
 * Get rental price display
 */
if ( !function_exists( 'smart_rentals_wc_get_price_display' ) ) {
    function smart_rentals_wc_get_price_display( $product_id ) {
        if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
            return;
        }

        smart_rentals_wc_get_template( 'price-display.php', [
            'product_id' => $product_id,
        ]);
    }
}

/**
 * Get rental calendar
 */
if ( !function_exists( 'smart_rentals_wc_get_calendar' ) ) {
    function smart_rentals_wc_get_calendar( $product_id ) {
        if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
            return;
        }

        smart_rentals_wc_get_template( 'calendar.php', [
            'product_id' => $product_id,
        ]);
    }
}