<?php
/**
 * Rental Calendar Template
 */

if ( !defined( 'ABSPATH' ) ) exit();

$enable_calendar = smart_rentals_wc_get_post_meta( $product_id, 'enable_calendar' );

if ( 'yes' !== $enable_calendar ) {
    return;
}
?>

<div class="smart-rentals-calendar-container">
    <h4><?php _e( 'Availability Calendar', 'smart-rentals-wc' ); ?></h4>
    <div id="smart-rentals-calendar-<?php echo esc_attr( $product_id ); ?>" class="smart-rentals-calendar" data-product-id="<?php echo esc_attr( $product_id ); ?>">
        <div class="calendar-loading">
            <p><?php _e( 'Loading calendar...', 'smart-rentals-wc' ); ?></p>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Simple calendar implementation
    var productId = <?php echo intval( $product_id ); ?>;
    var currentDate = new Date();
    var currentMonth = currentDate.getMonth();
    var currentYear = currentDate.getFullYear();
    
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
    });
    
    // Handle date selection
    $(document).on('click', '.calendar-day.available', function() {
        var selectedDate = $(this).data('date');
        
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
    align-items: center;
    justify-content: center;
    border: 1px solid #eee;
    cursor: pointer;
    position: relative;
    background: #fff;
    transition: all 0.2s ease;
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
    background: #f8d7da;
    color: #721c24;
    cursor: not-allowed;
}

.day-number {
    font-size: 14px;
}
</style>