<?php
/**
 * Smart Rentals WC Template Functions
 */

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Get rental booking form (following external plugin pattern)
 */
if ( !function_exists( 'smart_rentals_wc_get_booking_form' ) ) {
    function smart_rentals_wc_get_booking_form( $product_id ) {
        if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
            return;
        }

        // Include template with product_id in args (like external plugin)
        smart_rentals_wc_get_template( 'single/booking-form.php', [
            'product_id' => $product_id
        ]);
    }
}

/**
 * Get template (following external plugin pattern)
 */
if ( !function_exists( 'smart_rentals_wc_get_template' ) ) {
    function smart_rentals_wc_get_template( $template_name = '', $args = [], $template_path = '', $default_path = '' ) {
        if ( smart_rentals_wc_array_exists( $args ) ) {
            extract( $args );
        }

        $template_file = smart_rentals_wc_locate_template( $template_name, $template_path, $default_path );
        
        if ( !file_exists( $template_file ) ) {
            smart_rentals_wc_log( 'Template not found: ' . $template_file );
            return;
        }

        include $template_file;
    }
}

/**
 * Locate template (following external plugin pattern)
 */
if ( !function_exists( 'smart_rentals_wc_locate_template' ) ) {
    function smart_rentals_wc_locate_template( $template_name = '', $template_path = '', $default_path = '' ) {
        // Set variable to search in smart-rentals-wc folder of theme
        if ( !$template_path ) {
            $template_path = 'smart-rentals-wc/';
        }

        // Set default plugin templates path
        if ( !$default_path ) {
            $default_path = SMART_RENTALS_WC_PLUGIN_TEMPLATES;
        }

        // Search template file in theme folder
        $template = locate_template( [ $template_path . $template_name ] );

        // Get plugin template file
        if ( !$template ) {
            $template = $default_path . $template_name;
        }

        return apply_filters( 'smart_rentals_wc_locate_template', $template, $template_name, $template_path, $default_path );
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
                    <?php echo smart_rentals_wc_price( $security_deposit ); ?>
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