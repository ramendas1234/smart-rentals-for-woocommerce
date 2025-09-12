<?php
/**
 * Booking Form Fields Template (Modern UI with daterangepicker)
 */

if ( !defined( 'ABSPATH' ) ) exit();

// Get product
$product = smart_rentals_wc_get_rental_product( $args['product_id'] );
if ( !$product ) return;

$product_id = $product->get_id();

// Get rental configuration
$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
$min_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'min_rental_period' );
$max_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'max_rental_period' );

// Date format
$date_format = Smart_Rentals_WC()->options->get_date_format();
$time_format = Smart_Rentals_WC()->options->get_time_format();

// Get values from URL parameters (for pre-filling)
$pickup_date = smart_rentals_wc_get_meta_data( 'pickup_date', $_GET );
$dropoff_date = smart_rentals_wc_get_meta_data( 'dropoff_date', $_GET );

// Check if we need time picker
$has_timepicker = in_array( $rental_type, [ 'hour', 'mixed', 'appointment' ] );

?>

<!-- Date fields in side-by-side layout -->
<?php if ( in_array( $rental_type, [ 'day', 'hour', 'mixed', 'hotel', 'appointment' ] ) ): ?>
<div class="rental_item date-fields-row">
    <div class="date-fields-container">
        <!-- Pick-up date -->
        <div class="date-field pickup-field">
            <label for="pickup_date">
                <?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?>
                <span class="required">*</span>
            </label>
            <div class="date-input-wrapper">
                <input
                    type="text"
                    id="pickup_date"
                    class="pickup-date smart-rentals-input-required daterangepicker-field"
                    name="pickup_date"
                    value="<?php echo esc_attr( $pickup_date ); ?>"
                    required
                    data-type="<?php echo $has_timepicker ? 'datetimepicker' : 'datepicker'; ?>"
                    placeholder="<?php _e( 'Select pickup date', 'smart-rentals-wc' ); ?>"
                    readonly
                />
                <i class="date-icon dashicons dashicons-calendar-alt"></i>
            </div>
        </div>

        <!-- Drop-off date -->
        <div class="date-field dropoff-field">
            <label for="dropoff_date">
                <?php _e( 'Drop-off Date', 'smart-rentals-wc' ); ?>
                <span class="required">*</span>
            </label>
            <div class="date-input-wrapper">
                <input
                    type="text"
                    id="dropoff_date"
                    class="dropoff-date smart-rentals-input-required daterangepicker-field"
                    name="dropoff_date"
                    value="<?php echo esc_attr( $dropoff_date ); ?>"
                    required
                    data-type="<?php echo $has_timepicker ? 'datetimepicker' : 'datepicker'; ?>"
                    placeholder="<?php _e( 'Select drop-off date', 'smart-rentals-wc' ); ?>"
                    readonly
                />
                <i class="date-icon dashicons dashicons-calendar-alt"></i>
            </div>
        </div>
    </div>
    
    <!-- Loading indicator -->
    <div class="smart-rentals-loader-date">
        <i class="dashicons dashicons-update-alt" aria-hidden="true"></i>
    </div>
</div><!-- End Date fields -->
<?php endif; ?>

<!-- Quantity -->
<div class="rental_item">
	<label for="smart_rentals_quantity">
		<?php _e( 'Quantity', 'smart-rentals-wc' ); ?>
	</label>
	<input
		type="number"
		id="smart_rentals_quantity"
		name="smart_rentals_quantity"
		class="quantity"
		value="1"
		min="1"
		max="<?php echo esc_attr( smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' ) ?: 10 ); ?>"
	/>
</div>

<!-- Rental Period Info -->
<?php if ( $min_rental_period || $max_rental_period ) : ?>
<div class="rental_item rental-period-info">
	<?php if ( $min_rental_period ) : ?>
		<p class="min-period">
			<strong><?php _e( 'Minimum:', 'smart-rentals-wc' ); ?></strong>
			<?php 
			if ( $rental_type === 'hour' ) {
				printf( _n( '%d hour', '%d hours', $min_rental_period, 'smart-rentals-wc' ), $min_rental_period );
			} else {
				printf( _n( '%d day', '%d days', $min_rental_period, 'smart-rentals-wc' ), $min_rental_period );
			}
			?>
		</p>
	<?php endif; ?>

	<?php if ( $max_rental_period ) : ?>
		<p class="max-period">
			<strong><?php _e( 'Maximum:', 'smart-rentals-wc' ); ?></strong>
			<?php 
			if ( $rental_type === 'hour' ) {
				printf( _n( '%d hour', '%d hours', $max_rental_period, 'smart-rentals-wc' ), $max_rental_period );
			} else {
				printf( _n( '%d day', '%d days', $max_rental_period, 'smart-rentals-wc' ), $max_rental_period );
			}
			?>
		</p>
	<?php endif; ?>
</div>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function($) {
    'use strict';
    
    var hasTimepicker = <?php echo $has_timepicker ? 'true' : 'false'; ?>;
    var minRentalPeriod = <?php echo intval( $min_rental_period ); ?>;
    var maxRentalPeriod = <?php echo intval( $max_rental_period ); ?>;
    var rentalType = '<?php echo esc_js( $rental_type ); ?>';
    
    // Initialize modern date range picker
    function initDateRangePicker() {
        if (typeof $.fn.daterangepicker === 'undefined') {
            console.error('Daterangepicker library not loaded, falling back to basic date inputs');
            initBasicDatePickers();
            return;
        }
        
        // Configuration based on rental type
        var dateRangeConfig = {
            singleDatePicker: false,
            showDropdowns: true,
            showWeekNumbers: false,
            showISOWeekNumbers: false,
            timePicker: hasTimepicker,
            timePicker24Hour: true,
            timePickerIncrement: 30,
            autoApply: true,
            linkedCalendars: false,
            showCustomRangeLabel: false,
            alwaysShowCalendars: true,
            opens: 'center',
            drops: 'auto',
            buttonClasses: 'btn btn-sm',
            applyButtonClasses: 'btn-primary',
            cancelButtonClasses: 'btn-secondary',
            locale: {
                format: hasTimepicker ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD',
                separator: ' to ',
                applyLabel: '<?php _e( 'Apply', 'smart-rentals-wc' ); ?>',
                cancelLabel: '<?php _e( 'Cancel', 'smart-rentals-wc' ); ?>',
                fromLabel: '<?php _e( 'From', 'smart-rentals-wc' ); ?>',
                toLabel: '<?php _e( 'To', 'smart-rentals-wc' ); ?>',
                customRangeLabel: '<?php _e( 'Custom', 'smart-rentals-wc' ); ?>',
                weekLabel: 'W',
                daysOfWeek: [
                    '<?php _e( 'Su', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'Mo', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'Tu', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'We', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'Th', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'Fr', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'Sa', 'smart-rentals-wc' ); ?>'
                ],
                monthNames: [
                    '<?php _e( 'January', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'February', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'March', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'April', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'May', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'June', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'July', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'August', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'September', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'October', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'November', 'smart-rentals-wc' ); ?>',
                    '<?php _e( 'December', 'smart-rentals-wc' ); ?>'
                ],
                firstDay: 1
            },
            minDate: moment(),
            maxDate: moment().add(1, 'year'),
            startDate: pickup_date ? moment(pickup_date) : moment(),
            endDate: dropoff_date ? moment(dropoff_date) : moment().add(1, 'day')
        };
        
        // Add predefined ranges for quick selection
        if (!hasTimepicker) {
            dateRangeConfig.ranges = {
                '<?php _e( 'Today', 'smart-rentals-wc' ); ?>': [moment(), moment()],
                '<?php _e( 'Tomorrow', 'smart-rentals-wc' ); ?>': [moment().add(1, 'day'), moment().add(1, 'day')],
                '<?php _e( 'Next 3 Days', 'smart-rentals-wc' ); ?>': [moment(), moment().add(2, 'days')],
                '<?php _e( 'Next 7 Days', 'smart-rentals-wc' ); ?>': [moment(), moment().add(6, 'days')],
                '<?php _e( 'Next 14 Days', 'smart-rentals-wc' ); ?>': [moment(), moment().add(13, 'days')],
                '<?php _e( 'Next 30 Days', 'smart-rentals-wc' ); ?>': [moment(), moment().add(29, 'days')]
            };
        }
        
        // Initialize date range picker on a hidden input that will control both fields
        $('<input type="hidden" id="daterange_control" />').insertAfter('#dropoff_date');
        
        $('#daterange_control').daterangepicker(dateRangeConfig, function(start, end, label) {
            // Update individual date fields with proper format
            var pickupFormatted = start.format(hasTimepicker ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD');
            var dropoffFormatted = end.format(hasTimepicker ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD');
            
            $('#pickup_date').val(pickupFormatted);
            $('#dropoff_date').val(dropoffFormatted);
            
            // Add success animation
            $('#pickup_date, #dropoff_date').addClass('date-selected');
            setTimeout(function() {
                $('#pickup_date, #dropoff_date').removeClass('date-selected');
            }, 1000);
            
            // Show duration
            showRangeDuration(start, end);
            
            // Trigger calculation
            if (typeof window.smartRentalsCalculateTotal === 'function') {
                setTimeout(window.smartRentalsCalculateTotal, 200);
            }
        });
        
        // Make both date fields open the date range picker
        $('#pickup_date, #dropoff_date, .date-icon').on('click', function() {
            $('#daterange_control').data('daterangepicker').show();
        });
        
        console.log('Modern daterangepicker initialized for rental type:', rentalType);
    }
    
    // Fallback for basic date pickers
    function initBasicDatePickers() {
        $('input[data-type="datepicker"]').attr('type', 'date').removeAttr('readonly');
        $('input[data-type="datetimepicker"]').attr('type', 'datetime-local').removeAttr('readonly');
        
        var today = new Date().toISOString().split('T')[0];
        $('#pickup_date').attr('min', today);
        
        $('#pickup_date').on('change', function() {
            var pickupDate = $(this).val();
            if (pickupDate) {
                var minDropoff = new Date(pickupDate);
                minDropoff.setDate(minDropoff.getDate() + 1);
                $('#dropoff_date').attr('min', minDropoff.toISOString().split('T')[0]);
            }
        });
        
        console.log('Basic date pickers initialized for rental type:', rentalType);
    }
    
    // Show range duration with enhanced UI
    function showRangeDuration(start, end) {
        var durationMs = end.diff(start);
        var durationDays = Math.ceil(moment.duration(durationMs).asDays());
        var durationHours = Math.ceil(moment.duration(durationMs).asHours());
        
        var durationText = '';
        var icon = '';
        
        if (hasTimepicker && durationHours < 24) {
            durationText = durationHours + ' ' + (durationHours === 1 ? '<?php _e( 'hour', 'smart-rentals-wc' ); ?>' : '<?php _e( 'hours', 'smart-rentals-wc' ); ?>');
            icon = 'dashicons-clock';
        } else {
            durationText = durationDays + ' ' + (durationDays === 1 ? '<?php _e( 'day', 'smart-rentals-wc' ); ?>' : '<?php _e( 'days', 'smart-rentals-wc' ); ?>');
            icon = 'dashicons-calendar-alt';
        }
        
        // Remove existing duration display
        $('.range-duration').remove();
        
        // Add new duration display with enhanced styling
        var durationHtml = '<div class="range-duration"><i class="dashicons ' + icon + '"></i> <span class="duration-text">' + durationText + '</span><span class="duration-label"><?php _e( 'rental period', 'smart-rentals-wc' ); ?></span></div>';
        $('.date-fields-container').append(durationHtml);
        
        // Auto-hide after 4 seconds
        setTimeout(function() {
            $('.range-duration').fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }
    
    // Trigger calculation when dates change
    $('#pickup_date, #dropoff_date, #smart_rentals_quantity').on('change', function() {
        if (typeof window.smartRentalsCalculateTotal === 'function') {
            setTimeout(window.smartRentalsCalculateTotal, 100);
        }
    });
    
    // Initialize date pickers based on availability
    setTimeout(function() {
        if (typeof moment !== 'undefined' && typeof $.fn.daterangepicker !== 'undefined') {
            initDateRangePicker();
        } else {
            console.warn('Daterangepicker or Moment.js not available, using fallback');
            initBasicDatePickers();
        }
    }, 100);
    
    console.log('Smart Rentals Form Fields with Modern daterangepicker UI Initialized');
});
</script>

<style>
/* Side-by-side date fields layout */
.date-fields-container {
    display: flex;
    gap: 20px;
    margin-bottom: 15px;
    position: relative;
}

.date-field {
    flex: 1;
    min-width: 0;
}

.date-field label {
    display: block;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
    font-size: 14px;
}

.date-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.date-input-wrapper input {
    width: 100%;
    padding: 15px 45px 15px 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 16px;
    font-weight: 500;
    background: linear-gradient(145deg, #ffffff 0%, #f8f9fa 100%);
    transition: all 0.3s ease;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    cursor: pointer;
}

.date-input-wrapper input:focus,
.date-input-wrapper input:hover {
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1), 0 4px 20px rgba(0,0,0,0.1);
    background: white;
}

.date-input-wrapper .date-icon {
    position: absolute;
    right: 15px;
    color: #667eea;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 2;
}

.date-input-wrapper .date-icon:hover {
    color: #764ba2;
    transform: scale(1.1);
}

/* Date selected animation */
.date-selected {
    animation: dateSelectedPulse 0.6s ease-out;
}

@keyframes dateSelectedPulse {
    0% { transform: scale(1); border-color: #e9ecef; }
    50% { transform: scale(1.02); border-color: #46b450; box-shadow: 0 0 0 4px rgba(70, 180, 80, 0.2); }
    100% { transform: scale(1); border-color: #667eea; }
}

/* Range duration display */
.range-duration {
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    margin-top: 10px;
    text-align: center;
    font-weight: 600;
    animation: slideInUp 0.3s ease-out;
    font-size: 14px;
    white-space: nowrap;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
}

.range-duration i {
    margin-right: 8px;
}

.range-duration .duration-text {
    font-weight: 700;
    margin-right: 5px;
}

.range-duration .duration-label {
    opacity: 0.9;
    font-size: 12px;
}

/* Loading indicator positioning */
.smart-rentals-loader-date {
    position: absolute;
    top: 50%;
    right: -30px;
    transform: translateY(-50%);
    display: none;
    z-index: 3;
}

.smart-rentals-loader-date i {
    animation: spin 1s linear infinite;
    color: #667eea;
    font-size: 20px;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .date-fields-container {
        flex-direction: column;
        gap: 15px;
    }
    
    .date-field {
        width: 100%;
    }
    
    .range-duration {
        position: static;
        transform: none;
        margin: 15px 0;
    }
    
    .smart-rentals-loader-date {
        position: static;
        text-align: center;
        margin: 10px 0;
        transform: none;
    }
}

/* Enhanced daterangepicker styling */
.daterangepicker {
    border: none;
    border-radius: 15px;
    box-shadow: 0 15px 50px rgba(0,0,0,0.15);
    padding: 0;
    overflow: hidden;
}

.daterangepicker .calendar-table {
    background: white;
    border: none;
}

.daterangepicker .calendar-table th,
.daterangepicker .calendar-table td {
    border: none;
    padding: 8px;
}

.daterangepicker .calendar-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 11px;
    letter-spacing: 1px;
}

.daterangepicker .calendar-table td.available:hover {
    background: #f0f4ff;
    color: #667eea;
}

.daterangepicker .calendar-table td.in-range {
    background: linear-gradient(90deg, rgba(102, 126, 234, 0.15) 0%, rgba(118, 75, 162, 0.15) 100%);
    color: #667eea;
}

.daterangepicker .calendar-table td.start-date,
.daterangepicker .calendar-table td.end-date {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    font-weight: 600;
}

.daterangepicker .calendar-table td.today {
    background: #fff3cd;
    color: #856404;
    font-weight: 600;
}

.daterangepicker .ranges {
    background: #f8f9fa;
    border-right: 1px solid #e9ecef;
}

.daterangepicker .ranges li {
    color: #495057;
    border-radius: 8px;
    margin: 2px 8px;
    transition: all 0.2s ease;
}

.daterangepicker .ranges li:hover {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.daterangepicker .ranges li.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}
</style>