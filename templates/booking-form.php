<?php
/**
 * Rental Booking Form Template
 */

if ( !defined( 'ABSPATH' ) ) exit();

$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
$enable_calendar = smart_rentals_wc_get_post_meta( $product_id, 'enable_calendar' );
$min_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'min_rental_period' );
$max_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'max_rental_period' );
?>

<div class="smart-rentals-booking-form">
    <h3><?php _e( 'Rental Details', 'smart-rentals-wc' ); ?></h3>
    
    <div class="smart-rentals-dates">
        <div class="smart-rentals-date-field">
            <label for="pickup_date"><?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?> <span class="required">*</span></label>
            <input type="date" id="pickup_date" name="pickup_date" required min="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>" />
        </div>
        
        <div class="smart-rentals-date-field">
            <label for="dropoff_date"><?php _e( 'Drop-off Date', 'smart-rentals-wc' ); ?> <span class="required">*</span></label>
            <input type="date" id="dropoff_date" name="dropoff_date" required min="<?php echo esc_attr( date( 'Y-m-d', strtotime( '+1 day' ) ) ); ?>" />
        </div>
    </div>

    <?php if ( in_array( $rental_type, [ 'hour', 'mixed', 'appointment' ] ) ) : ?>
    <div class="smart-rentals-times">
        <div class="smart-rentals-time-field">
            <label for="pickup_time"><?php _e( 'Pickup Time', 'smart-rentals-wc' ); ?></label>
            <input type="time" id="pickup_time" name="pickup_time" />
        </div>
        
        <div class="smart-rentals-time-field">
            <label for="dropoff_time"><?php _e( 'Drop-off Time', 'smart-rentals-wc' ); ?></label>
            <input type="time" id="dropoff_time" name="dropoff_time" />
        </div>
    </div>
    <?php endif; ?>

    <div class="smart-rentals-info">
        <?php if ( $min_rental_period ) : ?>
            <p class="rental-period-info">
                <?php 
                if ( $rental_type === 'hour' ) {
                    printf( __( 'Minimum rental period: %d hours', 'smart-rentals-wc' ), $min_rental_period );
                } else {
                    printf( __( 'Minimum rental period: %d days', 'smart-rentals-wc' ), $min_rental_period );
                }
                ?>
            </p>
        <?php endif; ?>

        <?php if ( $max_rental_period ) : ?>
            <p class="rental-period-info">
                <?php 
                if ( $rental_type === 'hour' ) {
                    printf( __( 'Maximum rental period: %d hours', 'smart-rentals-wc' ), $max_rental_period );
                } else {
                    printf( __( 'Maximum rental period: %d days', 'smart-rentals-wc' ), $max_rental_period );
                }
                ?>
            </p>
        <?php endif; ?>

        <div id="rental-price-display" class="rental-price-display" style="display: none;">
            <p><strong><?php _e( 'Rental Price:', 'smart-rentals-wc' ); ?> <span id="rental-price-amount"></span></strong></p>
            <p><small id="rental-duration-text"></small></p>
        </div>
    </div>

    <?php if ( 'yes' === $enable_calendar ) : ?>
    <div class="smart-rentals-calendar" id="smart-rentals-calendar">
        <!-- Calendar will be loaded here via JavaScript -->
        <p><?php _e( 'Loading calendar...', 'smart-rentals-wc' ); ?></p>
    </div>
    <?php endif; ?>
</div>