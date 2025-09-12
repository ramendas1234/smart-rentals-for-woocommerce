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
        <?php smart_rentals_wc_get_calendar( $product_id ); ?>
    <?php endif; ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    var rentalType = '<?php echo esc_js( $rental_type ); ?>';
    var dailyPrice = <?php echo floatval( smart_rentals_wc_get_post_meta( $product_id, 'daily_price' ) ); ?>;
    var hourlyPrice = <?php echo floatval( smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' ) ); ?>;
    var minPeriod = <?php echo intval( $min_rental_period ); ?>;
    var maxPeriod = <?php echo intval( $max_rental_period ); ?>;
    var currencySymbol = '<?php echo function_exists( 'get_woocommerce_currency_symbol' ) ? esc_js( get_woocommerce_currency_symbol() ) : '$'; ?>';
    var productId = <?php echo intval( $product_id ); ?>;

    function calculateRentalPrice() {
        var pickupDate = $('#pickup_date').val();
        var dropoffDate = $('#dropoff_date').val();
        var pickupTime = $('#pickup_time').val() || '00:00';
        var dropoffTime = $('#dropoff_time').val() || '23:59';

        if (!pickupDate || !dropoffDate) {
            $('#rental-price-display').hide();
            return;
        }

        var pickup = new Date(pickupDate + ' ' + pickupTime);
        var dropoff = new Date(dropoffDate + ' ' + dropoffTime);
        
        if (pickup >= dropoff) {
            $('#rental-price-display').hide();
            return;
        }

        var durationMs = dropoff - pickup;
        var durationHours = durationMs / (1000 * 60 * 60);
        var durationDays = durationMs / (1000 * 60 * 60 * 24);

        var totalPrice = 0;
        var durationText = '';

        switch (rentalType) {
            case 'day':
            case 'hotel':
                var days = Math.max(1, Math.ceil(durationDays));
                totalPrice = dailyPrice * days;
                durationText = days + ' ' + (days === 1 ? '<?php echo esc_js( __( 'day', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>');
                break;

            case 'hour':
            case 'appointment':
                var hours = Math.max(1, Math.ceil(durationHours));
                totalPrice = hourlyPrice * hours;
                durationText = hours + ' ' + (hours === 1 ? '<?php echo esc_js( __( 'hour', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>');
                break;

            case 'mixed':
                if (durationHours >= 24 && dailyPrice > 0) {
                    var days = Math.max(1, Math.ceil(durationDays));
                    totalPrice = dailyPrice * days;
                    durationText = days + ' ' + (days === 1 ? '<?php echo esc_js( __( 'day', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>');
                } else if (hourlyPrice > 0) {
                    var hours = Math.max(1, Math.ceil(durationHours));
                    totalPrice = hourlyPrice * hours;
                    durationText = hours + ' ' + (hours === 1 ? '<?php echo esc_js( __( 'hour', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>');
                }
                break;
        }

        if (totalPrice > 0) {
            $('#rental-price-amount').text(currencySymbol + totalPrice.toFixed(2));
            $('#rental-duration-text').text('<?php echo esc_js( __( 'Duration:', 'smart-rentals-wc' ) ); ?> ' + durationText);
            $('#rental-price-display').show();
        } else {
            $('#rental-price-display').hide();
        }
    }

    // Bind events
    $('#pickup_date, #dropoff_date, #pickup_time, #dropoff_time').on('change', function() {
        calculateRentalPrice();
        
        // Also trigger availability check if both dates are selected
        if ($('#pickup_date').val() && $('#dropoff_date').val()) {
            setTimeout(function() {
                if (typeof SmartRentals !== 'undefined' && SmartRentals.checkAvailability) {
                    SmartRentals.checkAvailability();
                }
            }, 500);
        }
    });

    // Set minimum dropoff date when pickup date changes
    $('#pickup_date').on('change', function() {
        var pickupDate = $(this).val();
        if (pickupDate) {
            var minDropoffDate = new Date(pickupDate);
            minDropoffDate.setDate(minDropoffDate.getDate() + 1);
            $('#dropoff_date').attr('min', minDropoffDate.toISOString().split('T')[0]);
        }
    });

    // Validation before add to cart
    $('form.cart').on('submit', function(e) {
        var pickupDate = $('#pickup_date').val();
        var dropoffDate = $('#dropoff_date').val();

        if (!pickupDate || !dropoffDate) {
            e.preventDefault();
            alert('<?php echo esc_js( __( 'Please select pickup and drop-off dates.', 'smart-rentals-wc' ) ); ?>');
            return false;
        }

        var pickup = new Date(pickupDate);
        var dropoff = new Date(dropoffDate);

        if (pickup >= dropoff) {
            e.preventDefault();
            alert('<?php echo esc_js( __( 'Drop-off date must be after pickup date.', 'smart-rentals-wc' ) ); ?>');
            return false;
        }

        // Check minimum and maximum periods
        var durationMs = dropoff - pickup;
        var durationHours = durationMs / (1000 * 60 * 60);
        var durationDays = durationMs / (1000 * 60 * 60 * 24);

        if (minPeriod > 0) {
            if ((rentalType === 'hour' && durationHours < minPeriod) || 
                (rentalType !== 'hour' && durationDays < minPeriod)) {
                e.preventDefault();
                var periodType = rentalType === 'hour' ? '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>';
                alert('<?php echo esc_js( __( 'Minimum rental period is', 'smart-rentals-wc' ) ); ?> ' + minPeriod + ' ' + periodType + '.');
                return false;
            }
        }

        if (maxPeriod > 0) {
            if ((rentalType === 'hour' && durationHours > maxPeriod) || 
                (rentalType !== 'hour' && durationDays > maxPeriod)) {
                e.preventDefault();
                var periodType = rentalType === 'hour' ? '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>';
                alert('<?php echo esc_js( __( 'Maximum rental period is', 'smart-rentals-wc' ) ); ?> ' + maxPeriod + ' ' + periodType + '.');
                return false;
            }
        }
    });
});
</script>