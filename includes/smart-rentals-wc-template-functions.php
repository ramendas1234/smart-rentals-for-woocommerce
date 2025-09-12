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

        $rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
        $enable_calendar = smart_rentals_wc_get_post_meta( $product_id, 'enable_calendar' );
        $min_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'min_rental_period' );
        $max_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'max_rental_period' );

        // Include template with variables
        include SMART_RENTALS_WC_PLUGIN_TEMPLATES . 'booking-form.php';
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

        $rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
        $security_deposit = smart_rentals_wc_get_post_meta( $product_id, 'security_deposit' );

        ?>
        <div class="smart-rentals-info">
            <?php if ( $rental_stock ) : ?>
                <p class="rental-availability">
                    <strong><?php _e( 'Availability:', 'smart-rentals-wc' ); ?></strong>
                    <?php printf( _n( '%d item available', '%d items available', $rental_stock, 'smart-rentals-wc' ), $rental_stock ); ?>
                </p>
            <?php endif; ?>

            <?php if ( $security_deposit > 0 ) : ?>
                <p class="security-deposit">
                    <strong><?php _e( 'Security Deposit:', 'smart-rentals-wc' ); ?></strong>
                    <?php echo wc_price( $security_deposit ); ?>
                    <small><?php _e( '(refundable)', 'smart-rentals-wc' ); ?></small>
                </p>
            <?php endif; ?>
        </div>
        <?php
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

        // Include template with variables
        include SMART_RENTALS_WC_PLUGIN_TEMPLATES . 'calendar.php';
    }
}