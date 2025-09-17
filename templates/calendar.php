<?php
/**
 * Rental Calendar Template
 */

if ( !defined( 'ABSPATH' ) ) exit();

$enable_calendar = smart_rentals_wc_get_post_meta( $product_id, 'enable_calendar' );

if ( 'yes' !== $enable_calendar ) {
    return;
}

// Get rental type and pricing for display
$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
$daily_price = smart_rentals_wc_get_post_meta( $product_id, 'daily_price' );
$hourly_price = smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' );
$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );

// Debug: Log the values to see what's being retrieved
if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
    smart_rentals_wc_log( "Calendar Debug - Product ID: $product_id, Rental Type: '$rental_type', Daily Price: '$daily_price', Hourly Price: '$hourly_price'" );
    
    // Also check the raw meta values
    $raw_hourly = get_post_meta( $product_id, 'smart_rentals_hourly_price', true );
    $raw_daily = get_post_meta( $product_id, 'smart_rentals_daily_price', true );
    $raw_type = get_post_meta( $product_id, 'smart_rentals_rental_type', true );
    smart_rentals_wc_log( "Raw Meta Values - Type: '$raw_type', Daily: '$raw_daily', Hourly: '$raw_hourly'" );
}
?>

<div class="smart-rentals-calendar-container">
    <h4><?php _e( 'Availability Calendar', 'smart-rentals-wc' ); ?></h4>
    
    <!-- Debug Information (only show if WP_DEBUG is enabled) -->
    <?php if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) : ?>
        <div class="calendar-debug-info" style="background: #f0f0f0; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; font-size: 12px;">
            <strong>Debug Info:</strong><br>
            Rental Type: <?php echo esc_html( $rental_type ); ?><br>
            Daily Price: <?php echo esc_html( $daily_price ); ?><br>
            Hourly Price: <?php echo esc_html( $hourly_price ); ?><br>
            Raw Hourly: <?php echo esc_html( get_post_meta( $product_id, 'smart_rentals_hourly_price', true ) ); ?><br>
            Raw Daily: <?php echo esc_html( get_post_meta( $product_id, 'smart_rentals_daily_price', true ) ); ?><br>
            Raw Type: <?php echo esc_html( get_post_meta( $product_id, 'smart_rentals_rental_type', true ) ); ?>
        </div>
    <?php endif; ?>

    <!-- Pricing Information -->
    <div class="calendar-pricing-info">
        <?php if ( $rental_type === 'hour' || $rental_type === 'appointment' ) : ?>
            <div class="price-display">
                <span class="price-label"><?php _e( 'Hourly Rate:', 'smart-rentals-wc' ); ?></span>
                <span class="price-value">
                    <?php if ( $hourly_price > 0 ) : ?>
                        <?php echo smart_rentals_wc_price( $hourly_price ); ?> <?php _e( 'per hour', 'smart-rentals-wc' ); ?>
                    <?php else : ?>
                        <?php _e( 'Price not set', 'smart-rentals-wc' ); ?>
                    <?php endif; ?>
                </span>
            </div>
        <?php elseif ( $rental_type === 'day' || $rental_type === 'hotel' ) : ?>
            <div class="price-display">
                <span class="price-label"><?php _e( 'Daily Rate:', 'smart-rentals-wc' ); ?></span>
                <span class="price-value">
                    <?php if ( $daily_price > 0 ) : ?>
                        <?php echo smart_rentals_wc_price( $daily_price ); ?> <?php _e( 'per day', 'smart-rentals-wc' ); ?>
                    <?php else : ?>
                        <?php _e( 'Price not set', 'smart-rentals-wc' ); ?>
                    <?php endif; ?>
                </span>
            </div>
        <?php elseif ( $rental_type === 'mixed' ) : ?>
            <div class="price-display">
                <span class="price-label"><?php _e( 'Rates:', 'smart-rentals-wc' ); ?></span>
                <span class="price-value">
                    <?php if ( $daily_price > 0 ) : ?>
                        <?php echo smart_rentals_wc_price( $daily_price ); ?> <?php _e( 'per day', 'smart-rentals-wc' ); ?>
                    <?php endif; ?>
                    <?php if ( $daily_price > 0 && $hourly_price > 0 ) : ?> / <?php endif; ?>
                    <?php if ( $hourly_price > 0 ) : ?>
                        <?php echo smart_rentals_wc_price( $hourly_price ); ?> <?php _e( 'per hour', 'smart-rentals-wc' ); ?>
                    <?php endif; ?>
                    <?php if ( $daily_price <= 0 && $hourly_price <= 0 ) : ?>
                        <?php _e( 'Prices not set', 'smart-rentals-wc' ); ?>
                    <?php endif; ?>
                </span>
            </div>
        <?php else : ?>
            <!-- Fallback for unknown rental types -->
            <div class="price-display">
                <span class="price-label"><?php _e( 'Rates:', 'smart-rentals-wc' ); ?></span>
                <span class="price-value">
                    <?php if ( $daily_price > 0 ) : ?>
                        <?php echo smart_rentals_wc_price( $daily_price ); ?> <?php _e( 'per day', 'smart-rentals-wc' ); ?>
                    <?php endif; ?>
                    <?php if ( $daily_price > 0 && $hourly_price > 0 ) : ?> / <?php endif; ?>
                    <?php if ( $hourly_price > 0 ) : ?>
                        <?php echo smart_rentals_wc_price( $hourly_price ); ?> <?php _e( 'per hour', 'smart-rentals-wc' ); ?>
                    <?php endif; ?>
                    <?php if ( $daily_price <= 0 && $hourly_price <= 0 ) : ?>
                        <?php _e( 'Prices not set', 'smart-rentals-wc' ); ?>
                    <?php endif; ?>
                </span>
            </div>
        <?php endif; ?>
        
        <?php if ( $rental_stock > 1 ) : ?>
            <div class="stock-info">
                <span class="stock-label"><?php _e( 'Available Units:', 'smart-rentals-wc' ); ?></span>
                <span class="stock-value"><?php echo intval( $rental_stock ); ?></span>
            </div>
        <?php endif; ?>
    </div>
    
    <div id="smart-rentals-calendar-<?php echo esc_attr( $product_id ); ?>" class="smart-rentals-calendar" data-product-id="<?php echo esc_attr( $product_id ); ?>" data-rental-type="<?php echo esc_attr( $rental_type ); ?>">
        <div class="calendar-loading">
            <p><?php _e( 'Loading calendar...', 'smart-rentals-wc' ); ?></p>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Enhanced calendar implementation for hourly rentals
    var productId = <?php echo intval( $product_id ); ?>;
    var rentalType = '<?php echo esc_js( $rental_type ); ?>';
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth();
    var currentYear = currentDate.getFullYear();
    
    // Load availability data for the calendar
    function loadAvailabilityData(month, year) {
        var startDate = year + '-' + String(month + 1).padStart(2, '0') + '-01';
        var endDate = year + '-' + String(month + 1).padStart(2, '0') + '-' + new Date(year, month + 1, 0).getDate();
        
        // For hourly rentals, we need to check availability for each day
        if (rentalType === 'hour' || rentalType === 'appointment' || rentalType === 'mixed') {
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_batch_availability',
                    security: ajax_object.security,
                    product_id: productId,
                    dates: getDatesInRange(startDate, endDate)
                },
                success: function(response) {
                    if (response.success) {
                        updateCalendarWithAvailability(response.data);
                    }
                },
                error: function() {
                    console.error('Failed to load availability data');
                }
            });
        }
    }
    
    function getDatesInRange(startDate, endDate) {
        var dates = [];
        var current = new Date(startDate);
        var end = new Date(endDate);
        
        while (current <= end) {
            dates.push(current.toISOString().split('T')[0]);
            current.setDate(current.getDate() + 1);
        }
        
        return dates;
    }
    
    function updateCalendarWithAvailability(availabilityData) {
        $('.calendar-day').each(function() {
            var date = $(this).data('date');
            if (availabilityData[date]) {
                var available = availabilityData[date].available_quantity;
                var total = availabilityData[date].total_stock;
                
                $(this).removeClass('available booked fully-booked');
                
                if (available === 0) {
                    $(this).addClass('fully-booked');
                    $(this).find('.availability-info').remove();
                    $(this).append('<div class="availability-info">Fully Booked</div>');
                } else if (available < total) {
                    $(this).addClass('booked');
                    $(this).find('.availability-info').remove();
                    $(this).append('<div class="availability-info">' + available + '/' + total + ' available</div>');
                } else {
                    $(this).addClass('available');
                    $(this).find('.availability-info').remove();
                    if (total > 1) {
                        $(this).append('<div class="availability-info">' + available + ' available</div>');
                    }
                }
            }
        });
    }
    
    function generateCalendar(month, year) {
        var firstDay = new Date(year, month, 1).getDay();
        var daysInMonth = new Date(year, month + 1, 0).getDate();
        var today = new Date();
        
        var html = '<div class="calendar-header">';
        html += '<button type="button" class="prev-month" data-month="' + (month - 1) + '" data-year="' + year + '">‹</button>';
        html += '<h5>' + getMonthName(month) + ' ' + year + '</h5>';
        html += '<button type="button" class="next-month" data-month="' + (month + 1) + '" data-year="' + year + '">›</button>';
        html += '</div>';
        
        html += '<div class="calendar-grid">';
        html += '<div class="calendar-weekdays">';
        var weekdays = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
        for (var i = 0; i < weekdays.length; i++) {
            html += '<div class="weekday">' + weekdays[i] + '</div>';
        }
        html += '</div>';
        
        html += '<div class="calendar-days">';
        
        // Empty cells for days before month starts
        for (var i = 0; i < firstDay; i++) {
            html += '<div class="calendar-day empty"></div>';
        }
        
        // Days of the month
        for (var day = 1; day <= daysInMonth; day++) {
            var date = new Date(year, month, day);
            var dateStr = year + '-' + String(month + 1).padStart(2, '0') + '-' + String(day).padStart(2, '0');
            var classes = ['calendar-day'];
            
            if (date < today) {
                classes.push('past');
            } else {
                classes.push('available');
            }
            
            if (date.toDateString() === today.toDateString()) {
                classes.push('today');
            }
            
            html += '<div class="' + classes.join(' ') + '" data-date="' + dateStr + '">';
            html += '<span class="day-number">' + day + '</span>';
            html += '</div>';
        }
        
        html += '</div>';
        html += '</div>';
        
        return html;
    }
    
    function getMonthName(month) {
        var months = [
            '<?php echo esc_js( __( 'January', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'February', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'March', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'April', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'May', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'June', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'July', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'August', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'September', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'October', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'November', 'smart-rentals-wc' ) ); ?>',
            '<?php echo esc_js( __( 'December', 'smart-rentals-wc' ) ); ?>'
        ];
        return months[month];
    }
    
    // Initialize calendar
    var calendarContainer = $('#smart-rentals-calendar-' + productId);
    calendarContainer.html(generateCalendar(currentMonth, currentYear));
    
    // Load availability data for current month
    loadAvailabilityData(currentMonth, currentYear);
    
    // Handle navigation
    $(document).on('click', '.prev-month, .next-month', function() {
        var month = parseInt($(this).data('month'));
        var year = parseInt($(this).data('year'));
        
        if (month < 0) {
            month = 11;
            year--;
        } else if (month > 11) {
            month = 0;
            year++;
        }
        
        calendarContainer.html(generateCalendar(month, year));
        loadAvailabilityData(month, year);
    });
    
    // Handle date selection
    $(document).on('click', '.calendar-day.available, .calendar-day.booked', function() {
        var selectedDate = $(this).data('date');
        var isFullyBooked = $(this).hasClass('fully-booked');
        
        // Don't allow selection of fully booked days
        if (isFullyBooked) {
            return;
        }
        
        if (!$('#pickup_date').val()) {
            $('#pickup_date').val(selectedDate).trigger('change');
            $('.calendar-day').removeClass('selected-pickup');
            $(this).addClass('selected-pickup');
        } else if (!$('#dropoff_date').val()) {
            $('#dropoff_date').val(selectedDate).trigger('change');
            $('.calendar-day').removeClass('selected-dropoff');
            $(this).addClass('selected-dropoff');
        } else {
            // Reset and start over
            $('#pickup_date').val(selectedDate).trigger('change');
            $('#dropoff_date').val('');
            $('.calendar-day').removeClass('selected-pickup selected-dropoff');
            $(this).addClass('selected-pickup');
        }
    });
});
</script>

<style>
.smart-rentals-calendar-container {
    margin: 20px 0;
    padding: 15px;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.calendar-pricing-info {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 15px;
    padding: 10px;
    background: #f8f9fa;
    border-radius: 4px;
    border: 1px solid #e9ecef;
}

.price-display, .stock-info {
    display: flex;
    align-items: center;
    gap: 5px;
}

.price-label, .stock-label {
    font-weight: 600;
    color: #495057;
    font-size: 14px;
}

.price-value, .stock-value {
    color: #0073aa;
    font-weight: 600;
    font-size: 14px;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid #eee;
}

.calendar-header h5 {
    margin: 0;
    font-size: 16px;
    font-weight: 600;
}

.prev-month, .next-month {
    background: #0073aa;
    color: #fff;
    border: none;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.prev-month:hover, .next-month:hover {
    background: #005a87;
}

.calendar-weekdays {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
    margin-bottom: 5px;
}

.weekday {
    text-align: center;
    font-weight: 600;
    padding: 8px;
    background: #f0f0f0;
    font-size: 12px;
    color: #666;
}

.calendar-days {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 1px;
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    border: 1px solid #eee;
    cursor: pointer;
    position: relative;
    background: #fff;
    transition: all 0.2s ease;
    min-height: 60px;
}

.calendar-day.empty {
    background: #f9f9f9;
    cursor: default;
}

.calendar-day.past {
    background: #f5f5f5;
    color: #ccc;
    cursor: not-allowed;
}

.calendar-day.available:hover {
    background: #e8f4f8;
    border-color: #0073aa;
}

.calendar-day.today {
    background: #fff2cc;
    border-color: #f39c12;
    font-weight: bold;
}

.calendar-day.selected-pickup {
    background: #d4edda;
    border-color: #46b450;
    color: #155724;
    font-weight: bold;
}

.calendar-day.selected-dropoff {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
    font-weight: bold;
}

.calendar-day.booked {
    background: #fff3cd;
    border-color: #ffc107;
    color: #856404;
}

.calendar-day.fully-booked {
    background: #f8d7da;
    border-color: #dc3545;
    color: #721c24;
    cursor: not-allowed;
}

.day-number {
    font-size: 14px;
    font-weight: 600;
}

.availability-info {
    font-size: 10px;
    text-align: center;
    margin-top: 2px;
    font-weight: 500;
    line-height: 1.2;
}

.calendar-day.available .availability-info {
    color: #28a745;
}

.calendar-day.booked .availability-info {
    color: #856404;
}

.calendar-day.fully-booked .availability-info {
    color: #721c24;
}

/* Responsive design */
@media (max-width: 768px) {
    .calendar-pricing-info {
        flex-direction: column;
        gap: 8px;
    }
    
    .calendar-day {
        min-height: 50px;
    }
    
    .availability-info {
        font-size: 9px;
    }
}
</style>