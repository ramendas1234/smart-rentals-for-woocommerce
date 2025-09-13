<?php
/**
 * Booking Form Fields Template (Using daterangepicker.com)
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

// Get values from URL parameters (for pre-filling)
$pickup_date = smart_rentals_wc_get_meta_data( 'pickup_date', $_GET );
$dropoff_date = smart_rentals_wc_get_meta_data( 'dropoff_date', $_GET );

// Check if we need time picker
$has_timepicker = in_array( $rental_type, [ 'hour', 'mixed', 'appointment' ] );

?>

<!-- Date fields in side-by-side layout -->
<?php if ( in_array( $rental_type, [ 'day', 'hour', 'mixed', 'hotel', 'appointment', 'period_time', 'transportation', 'taxi' ] ) ): ?>
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
                    class="pickup-date smart-rentals-input-required form-control"
                    name="pickup_date"
                    value="<?php echo esc_attr( $pickup_date ); ?>"
                    required
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
                    class="dropoff-date smart-rentals-input-required form-control"
                    name="dropoff_date"
                    value="<?php echo esc_attr( $dropoff_date ); ?>"
                    required
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
		class="quantity form-control"
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
    var disabledWeekdays = <?php 
        $disabled_weekdays = smart_rentals_wc_get_post_meta( $product_id, 'disabled_weekdays' );
        echo json_encode( is_array( $disabled_weekdays ) ? array_map( 'intval', $disabled_weekdays ) : [] );
    ?>;
    
    // Initialize daterangepicker.com with Apply button
    function initDateRangePicker() {
        if (typeof $.fn.daterangepicker === 'undefined' || typeof moment === 'undefined') {
            console.error('Daterangepicker.com library not loaded, falling back to basic date inputs');
            initBasicDatePickers();
            return;
        }
        
        // Configuration for daterangepicker.com
        var dateRangeConfig = {
            // Core settings
            autoApply: false,  // Shows Apply/Cancel buttons
            autoUpdateInput: false,  // Don't auto-update inputs until Apply is clicked
            showDropdowns: true,
            showWeekNumbers: false,
            showISOWeekNumbers: false,
            timePicker: hasTimepicker,
            timePicker24Hour: true,
            timePickerIncrement: 30,
            timePickerSeconds: false,
            linkedCalendars: false,
            showCustomRangeLabel: true,
            alwaysShowCalendars: true,
            opens: 'center',
            drops: 'auto',
            
            // Button styling
            buttonClasses: 'btn btn-sm',
            applyButtonClasses: 'btn-success',
            cancelButtonClasses: 'btn-secondary',
            
            // Date format and locale
            locale: {
                format: hasTimepicker ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD',
                separator: ' - ',
                applyLabel: '<?php _e( 'Apply', 'smart-rentals-wc' ); ?>',
                cancelLabel: '<?php _e( 'Cancel', 'smart-rentals-wc' ); ?>',
                fromLabel: '<?php _e( 'From', 'smart-rentals-wc' ); ?>',
                toLabel: '<?php _e( 'To', 'smart-rentals-wc' ); ?>',
                customRangeLabel: '<?php _e( 'Custom Range', 'smart-rentals-wc' ); ?>',
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
            
            // Date constraints
            minDate: moment(),
            maxDate: moment().add(1, 'year'),
            
            // Disable specific weekdays
            isInvalidDate: function(date) {
                if (disabledWeekdays && disabledWeekdays.length > 0) {
                    var dayOfWeek = date.day(); // 0 = Sunday, 1 = Monday, etc.
                    return disabledWeekdays.indexOf(dayOfWeek) !== -1;
                }
                return false;
            }
        };
        
        // Add ranges only for non-time picker modes (daily rentals)
        if (!hasTimepicker) {
            dateRangeConfig.ranges = {
                '<?php _e( 'Today', 'smart-rentals-wc' ); ?>': [moment(), moment()],
                '<?php _e( 'Tomorrow', 'smart-rentals-wc' ); ?>': [moment().add(1, 'day'), moment().add(1, 'day')],
                '<?php _e( 'Next 3 Days', 'smart-rentals-wc' ); ?>': [moment(), moment().add(2, 'days')],
                '<?php _e( 'Next Week', 'smart-rentals-wc' ); ?>': [moment(), moment().add(6, 'days')],
                '<?php _e( 'Next 2 Weeks', 'smart-rentals-wc' ); ?>': [moment(), moment().add(13, 'days')],
                '<?php _e( 'Next Month', 'smart-rentals-wc' ); ?>': [moment(), moment().add(29, 'days')]
            };
        } else {
            // For hourly rentals, provide time-based ranges
            dateRangeConfig.ranges = {
                '<?php _e( 'Next 2 Hours', 'smart-rentals-wc' ); ?>': [moment(), moment().add(2, 'hours')],
                '<?php _e( 'Next 4 Hours', 'smart-rentals-wc' ); ?>': [moment(), moment().add(4, 'hours')],
                '<?php _e( 'Next 8 Hours', 'smart-rentals-wc' ); ?>': [moment(), moment().add(8, 'hours')],
                '<?php _e( 'Next 12 Hours', 'smart-rentals-wc' ); ?>': [moment(), moment().add(12, 'hours')],
                '<?php _e( 'Next Day', 'smart-rentals-wc' ); ?>': [moment(), moment().add(1, 'day')]
            };
        }
        
        // Initialize daterangepicker on a single input that controls both fields
        $('#pickup_date').daterangepicker(dateRangeConfig);
        
        // Handle Apply button click - THIS IS KEY!
        $('#pickup_date').on('apply.daterangepicker', function(ev, picker) {
            console.log('Apply button clicked - triggering calculation');
            console.log('Start Date:', picker.startDate.format());
            console.log('End Date:', picker.endDate.format());
            
            var startDate = picker.startDate;
            var endDate = picker.endDate;
            
            // Validate the date range
            if (!startDate.isValid() || !endDate.isValid()) {
                console.error('Invalid dates selected');
                return;
            }
            
            if (startDate.isSameOrAfter(endDate)) {
                console.error('Start date must be before end date');
                return;
            }
            
            // Format dates for individual fields with proper timezone handling
            var pickupFormatted, dropoffFormatted;
            
            if (hasTimepicker) {
                // For hourly rentals, include time
                pickupFormatted = startDate.format('YYYY-MM-DD HH:mm');
                dropoffFormatted = endDate.format('YYYY-MM-DD HH:mm');
            } else {
                // For daily rentals, date only
                pickupFormatted = startDate.format('YYYY-MM-DD');
                dropoffFormatted = endDate.format('YYYY-MM-DD');
            }
            
            console.log('Formatted dates - Pickup:', pickupFormatted, 'Dropoff:', dropoffFormatted);
            
            // Update individual fields
            $('#pickup_date').val(pickupFormatted);
            $('#dropoff_date').val(dropoffFormatted);
            
            // Add success animation
            $('#pickup_date, #dropoff_date').addClass('date-selected');
            setTimeout(function() {
                $('#pickup_date, #dropoff_date').removeClass('date-selected');
            }, 1000);
            
            // Show duration
            showRangeDuration(startDate, endDate);
            
            // ONLY NOW trigger the calculation (no excessive AJAX calls!)
            if (typeof window.smartRentalsCalculateTotal === 'function') {
                console.log('Triggering calculation with dates:', pickupFormatted, dropoffFormatted);
                setTimeout(window.smartRentalsCalculateTotal, 300);
            } else {
                console.error('smartRentalsCalculateTotal function not available');
            }
        });
        
        // Handle Cancel button click
        $('#pickup_date').on('cancel.daterangepicker', function(ev, picker) {
            console.log('Cancel button clicked');
            // Could add any cancel logic here if needed
        });
        
        // Make dropoff field also open the same picker
        $('#dropoff_date').on('click', function() {
            $('#pickup_date').data('daterangepicker').show();
        });
        
        // Make date icons open the picker
        $('.date-icon').on('click', function() {
            $('#pickup_date').data('daterangepicker').show();
        });
        
        console.log('Daterangepicker.com initialized with Apply button for rental type:', rentalType);
    }
    
    // Fallback for basic date pickers
    function initBasicDatePickers() {
        if (hasTimepicker) {
            // For hourly rentals, use datetime-local inputs
            $('#pickup_date, #dropoff_date').attr('type', 'datetime-local').removeAttr('readonly');
            
            var now = new Date();
            var minDateTime = now.toISOString().slice(0, 16);
            $('#pickup_date').attr('min', minDateTime);
        } else {
            // For daily rentals, use date inputs
            $('#pickup_date, #dropoff_date').attr('type', 'date').removeAttr('readonly');
            
            var today = new Date().toISOString().split('T')[0];
            $('#pickup_date').attr('min', today);
        }
        
        $('#pickup_date').on('change', function() {
            var pickupDate = $(this).val();
            if (pickupDate) {
                if (hasTimepicker) {
                    // For datetime, ensure dropoff is at least 1 hour later
                    var pickupMoment = new Date(pickupDate);
                    pickupMoment.setHours(pickupMoment.getHours() + 1);
                    $('#dropoff_date').attr('min', pickupMoment.toISOString().slice(0, 16));
                } else {
                    // For date, ensure dropoff is at least 1 day later
                    var minDropoff = new Date(pickupDate);
                    minDropoff.setDate(minDropoff.getDate() + 1);
                    $('#dropoff_date').attr('min', minDropoff.toISOString().split('T')[0]);
                }
            }
        });
        
        // Only trigger calculation on change for fallback
        $('#pickup_date, #dropoff_date').on('change', function() {
            var pickupVal = $('#pickup_date').val();
            var dropoffVal = $('#dropoff_date').val();
            
            if (pickupVal && dropoffVal) {
                console.log('Fallback calculation with dates:', pickupVal, dropoffVal);
                if (typeof window.smartRentalsCalculateTotal === 'function') {
                    setTimeout(window.smartRentalsCalculateTotal, 100);
                }
            }
        });
        
        console.log('Basic date pickers initialized for rental type:', rentalType, 'with timepicker:', hasTimepicker);
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
        
        // Add new duration display
        var durationHtml = '<div class="range-duration"><i class="dashicons ' + icon + '"></i> <span class="duration-text">' + durationText + '</span><span class="duration-label"><?php _e( 'rental period', 'smart-rentals-wc' ); ?></span></div>';
        $('.date-fields-container').append(durationHtml);
        
        // Auto-hide after 4 seconds
        setTimeout(function() {
            $('.range-duration').fadeOut(300, function() {
                $(this).remove();
            });
        }, 4000);
    }
    
    // Trigger calculation when quantity changes (but NOT on date changes - only on Apply!)
    $('#smart_rentals_quantity').on('change', function() {
        // Only trigger if dates are already selected
        if ($('#pickup_date').val() && $('#dropoff_date').val()) {
            if (typeof window.smartRentalsCalculateTotal === 'function') {
                setTimeout(window.smartRentalsCalculateTotal, 100);
            }
        }
    });
    
    // Initialize date pickers based on library availability
    setTimeout(function() {
        if (typeof moment !== 'undefined' && typeof $.fn.daterangepicker !== 'undefined') {
            initDateRangePicker();
        } else {
            console.warn('Daterangepicker.com or Moment.js not available, using fallback');
            initBasicDatePickers();
        }
    }, 200); // Increased timeout to ensure libraries are loaded
    
    console.log('Smart Rentals Form Fields with daterangepicker.com (Apply button) initialized');
});
</script>