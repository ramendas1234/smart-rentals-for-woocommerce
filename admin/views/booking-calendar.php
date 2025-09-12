<?php
/**
 * Admin Booking Calendar View
 * Based on external plugin's booking calendar implementation
 */

if ( !defined( 'ABSPATH' ) ) exit;

// Enqueue FullCalendar and dependencies
wp_enqueue_script( 'fullcalendar', 'https://cdn.jsdelivr.net/npm/fullcalendar@6.1.9/index.global.min.js', [], '6.1.9', true );

?>

<div class="smart-rentals-booking-calendar-wrap wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e( 'Booking Calendar', 'smart-rentals-wc' ); ?>
        <span class="title-count">(<?php echo count( $events ); ?> <?php _e( 'bookings', 'smart-rentals-wc' ); ?>)</span>
    </h1>
    
    <form class="smart-rentals-booking-calendar-filter" method="POST" action="" autocomplete="off">
        <div class="filter-fields">
            <div class="filter-field">
                <label for="product-filter"><?php _e( 'Filter by Product:', 'smart-rentals-wc' ); ?></label>
                <select id="product-filter" name="pid" class="smart-rentals-select">
                    <option value=""><?php _e( 'All Products', 'smart-rentals-wc' ); ?></option>
                    <?php if ( !empty( $product_ids ) ) : ?>
                        <?php foreach ( $product_ids as $product_id ) : ?>
                            <option value="<?php echo esc_attr( $product_id ); ?>">
                                <?php echo esc_html( get_the_title( $product_id ) ); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="filter-field">
                <label for="status-filter"><?php _e( 'Filter by Status:', 'smart-rentals-wc' ); ?></label>
                <select id="status-filter" name="status" class="smart-rentals-select">
                    <option value=""><?php _e( 'All Statuses', 'smart-rentals-wc' ); ?></option>
                    <option value="pending"><?php _e( 'Pending', 'smart-rentals-wc' ); ?></option>
                    <option value="confirmed"><?php _e( 'Confirmed', 'smart-rentals-wc' ); ?></option>
                    <option value="active"><?php _e( 'Active', 'smart-rentals-wc' ); ?></option>
                    <option value="processing"><?php _e( 'Processing', 'smart-rentals-wc' ); ?></option>
                    <option value="completed"><?php _e( 'Completed', 'smart-rentals-wc' ); ?></option>
                    <option value="cancelled"><?php _e( 'Cancelled', 'smart-rentals-wc' ); ?></option>
                </select>
            </div>
            
            <button type="button" class="button filter-calendar" id="filter-calendar">
                <?php _e( 'Filter', 'smart-rentals-wc' ); ?>
            </button>
            
            <button type="button" class="button reset-filter" id="reset-filter">
                <?php _e( 'Reset', 'smart-rentals-wc' ); ?>
            </button>
            
            <span class="spinner" id="calendar-spinner"></span>
        </div>
    </form>
    
    <div class="smart-rentals-calendar-container">
        <div id="smart-rentals-admin-calendar"></div>
    </div>
    
    <!-- Calendar Legend -->
    <div class="calendar-legend">
        <h3><?php _e( 'Status Legend:', 'smart-rentals-wc' ); ?></h3>
        <div class="legend-items">
            <div class="legend-item">
                <span class="legend-color" style="background-color: #ffc107;"></span>
                <span class="legend-text"><?php _e( 'Pending', 'smart-rentals-wc' ); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #28a745;"></span>
                <span class="legend-text"><?php _e( 'Confirmed', 'smart-rentals-wc' ); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #17a2b8;"></span>
                <span class="legend-text"><?php _e( 'Active', 'smart-rentals-wc' ); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #fd7e14;"></span>
                <span class="legend-text"><?php _e( 'Processing', 'smart-rentals-wc' ); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #6f42c1;"></span>
                <span class="legend-text"><?php _e( 'Completed', 'smart-rentals-wc' ); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color" style="background-color: #dc3545;"></span>
                <span class="legend-text"><?php _e( 'Cancelled', 'smart-rentals-wc' ); ?></span>
            </div>
        </div>
    </div>
    
    <!-- Booking Details Modal -->
    <div id="booking-details-modal" class="smart-rentals-modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2><?php _e( 'Booking Details', 'smart-rentals-wc' ); ?></h2>
                <span class="close-modal">&times;</span>
            </div>
            <div class="modal-body">
                <div id="booking-details-content">
                    <!-- Booking details will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    'use strict';
    
    // Calendar events data
    var calendarEvents = <?php echo json_encode( $events ); ?>;
    var allEvents = calendarEvents; // Keep original events for filtering
    
    // Initialize FullCalendar
    var calendarEl = document.getElementById('smart-rentals-admin-calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        events: calendarEvents,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
        },
        dayMaxEvents: 3,
        eventClick: function(info) {
            showBookingDetails(info.event);
        },
        eventMouseEnter: function(info) {
            var event = info.event;
            var tooltip = '<div class="booking-tooltip">' +
                '<strong>' + event.title + '</strong><br>' +
                '<strong><?php _e( 'Status:', 'smart-rentals-wc' ); ?></strong> ' + event.extendedProps.status + '<br>' +
                '<strong><?php _e( 'Dates:', 'smart-rentals-wc' ); ?></strong> ' + 
                event.start.toLocaleDateString() + ' - ' + 
                (event.end ? event.end.toLocaleDateString() : event.start.toLocaleDateString()) +
                '</div>';
            
            $(info.el).attr('title', '').tooltip({
                content: tooltip,
                show: { effect: 'fadeIn', duration: 200 },
                hide: { effect: 'fadeOut', duration: 200 },
                position: { my: 'center bottom-20', at: 'center top' }
            }).tooltip('open');
        },
        eventMouseLeave: function(info) {
            $(info.el).tooltip('close');
        }
    });
    
    calendar.render();
    
    // Filter functionality
    $('#filter-calendar').on('click', function() {
        var productId = $('#product-filter').val();
        var status = $('#status-filter').val();
        
        $('#calendar-spinner').addClass('is-active');
        
        var filteredEvents = allEvents.filter(function(event) {
            var matchProduct = !productId || event.extendedProps.product_id == productId;
            var matchStatus = !status || event.extendedProps.status == status;
            return matchProduct && matchStatus;
        });
        
        calendar.removeAllEvents();
        calendar.addEventSource(filteredEvents);
        
        setTimeout(function() {
            $('#calendar-spinner').removeClass('is-active');
        }, 500);
    });
    
    // Reset filter
    $('#reset-filter').on('click', function() {
        $('#product-filter').val('');
        $('#status-filter').val('');
        calendar.removeAllEvents();
        calendar.addEventSource(allEvents);
    });
    
    // Show booking details modal
    function showBookingDetails(event) {
        var props = event.extendedProps;
        var startDate = event.start.toLocaleDateString();
        var endDate = event.end ? event.end.toLocaleDateString() : startDate;
        
        var content = '<div class="booking-details">' +
            '<div class="detail-row"><strong><?php _e( 'Product:', 'smart-rentals-wc' ); ?></strong> ' + event.title.split(' (')[0] + '</div>' +
            '<div class="detail-row"><strong><?php _e( 'Booking ID:', 'smart-rentals-wc' ); ?></strong> #' + props.booking_id + '</div>' +
            '<div class="detail-row"><strong><?php _e( 'Status:', 'smart-rentals-wc' ); ?></strong> <span class="status-badge status-' + props.status + '">' + props.status + '</span></div>' +
            '<div class="detail-row"><strong><?php _e( 'Pickup Date:', 'smart-rentals-wc' ); ?></strong> ' + startDate + '</div>' +
            '<div class="detail-row"><strong><?php _e( 'Dropoff Date:', 'smart-rentals-wc' ); ?></strong> ' + endDate + '</div>' +
            '<div class="detail-row"><strong><?php _e( 'Quantity:', 'smart-rentals-wc' ); ?></strong> ' + props.quantity + '</div>' +
            '<div class="detail-row"><strong><?php _e( 'Total Price:', 'smart-rentals-wc' ); ?></strong> ' + (props.total_price ? '<?php echo function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$'; ?>' + props.total_price : 'N/A') + '</div>' +
            '<div class="detail-row"><strong><?php _e( 'Security Deposit:', 'smart-rentals-wc' ); ?></strong> ' + (props.security_deposit ? '<?php echo function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$'; ?>' + props.security_deposit : 'N/A') + '</div>' +
            '</div>';
        
        $('#booking-details-content').html(content);
        $('#booking-details-modal').fadeIn();
    }
    
    // Close modal
    $('.close-modal, #booking-details-modal').on('click', function(e) {
        if (e.target === this) {
            $('#booking-details-modal').fadeOut();
        }
    });
    
    // Prevent modal close when clicking inside modal content
    $('.modal-content').on('click', function(e) {
        e.stopPropagation();
    });
});
</script>

<style>
/* Admin Booking Calendar Styles */
.smart-rentals-booking-calendar-wrap {
    margin: 20px 0;
}

.smart-rentals-booking-calendar-wrap .title-count {
    color: #666;
    font-weight: normal;
    font-size: 14px;
}

.smart-rentals-booking-calendar-filter {
    background: #f9f9f9;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin: 20px 0;
}

.filter-fields {
    display: flex;
    align-items: end;
    gap: 15px;
    flex-wrap: wrap;
}

.filter-field {
    display: flex;
    flex-direction: column;
}

.filter-field label {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.smart-rentals-select {
    min-width: 200px;
    padding: 8px;
    border: 1px solid #ddd;
    border-radius: 3px;
}

.smart-rentals-calendar-container {
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 20px;
    margin: 20px 0;
}

/* Calendar Legend */
.calendar-legend {
    background: #f9f9f9;
    padding: 20px;
    border: 1px solid #ddd;
    border-radius: 5px;
    margin: 20px 0;
}

.legend-items {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.legend-color {
    width: 16px;
    height: 16px;
    border-radius: 3px;
    border: 1px solid #ccc;
}

.legend-text {
    font-weight: 500;
    text-transform: capitalize;
}

/* Modal Styles */
.smart-rentals-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
    z-index: 100000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 600px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px;
    border-bottom: 1px solid #eee;
    background: #f8f9fa;
    border-radius: 8px 8px 0 0;
}

.modal-header h2 {
    margin: 0;
    color: #333;
}

.close-modal {
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    color: #aaa;
    line-height: 1;
}

.close-modal:hover {
    color: #000;
}

.modal-body {
    padding: 20px;
}

.booking-details .detail-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #eee;
}

.booking-details .detail-row:last-child {
    border-bottom: none;
}

.status-badge {
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    color: white;
}

.status-badge.status-pending { background: #ffc107; color: #333; }
.status-badge.status-confirmed { background: #28a745; }
.status-badge.status-active { background: #17a2b8; }
.status-badge.status-processing { background: #fd7e14; }
.status-badge.status-completed { background: #6f42c1; }
.status-badge.status-cancelled { background: #dc3545; }

/* FullCalendar customizations */
.fc-event {
    cursor: pointer;
    border-radius: 4px;
    padding: 2px;
}

.fc-event:hover {
    opacity: 0.8;
    transform: scale(1.02);
}

/* Responsive */
@media (max-width: 768px) {
    .filter-fields {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-field {
        margin-bottom: 10px;
    }
    
    .smart-rentals-select {
        min-width: auto;
        width: 100%;
    }
    
    .legend-items {
        flex-direction: column;
        gap: 10px;
    }
    
    .modal-content {
        width: 95%;
        margin: 10px;
    }
}
</style>