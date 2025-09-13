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

// Check if we need time picker based on rental type
// Daily rentals use fixed global times, Hourly/Mixed allow time selection
$has_timepicker = in_array( $rental_type, [ 'hour', 'mixed' ] ); // Only hourly and mixed allow time selection
$use_fixed_times = ( $rental_type === 'day' ); // Daily rentals use fixed global times

// Get global default times
$settings = smart_rentals_wc_get_option( 'settings', [] );
$default_pickup_time = smart_rentals_wc_get_meta_data( 'default_pickup_time', $settings, '10:00' );
$default_dropoff_time = smart_rentals_wc_get_meta_data( 'default_dropoff_time', $settings, '09:30' );

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
    
    <!-- Return time notice area -->
    <div id="return-time-notice" class="return-time-notice" style="display: none;">
        <div class="notice-content">
            <span class="notice-icon">📅</span>
            <span class="notice-text"></span>
        </div>
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
    var useFixedTimes = <?php echo $use_fixed_times ? 'true' : 'false'; ?>;
    var minRentalPeriod = <?php echo intval( $min_rental_period ); ?>;
    var maxRentalPeriod = <?php echo intval( $max_rental_period ); ?>;
    var rentalType = '<?php echo esc_js( $rental_type ); ?>';
    var disabledWeekdays = <?php 
        $disabled_weekdays = smart_rentals_wc_get_post_meta( $product_id, 'disabled_weekdays' );
        echo json_encode( is_array( $disabled_weekdays ) ? array_map( 'intval', $disabled_weekdays ) : [] );
    ?>;
    var disabledDates = <?php 
        $disabled_start_dates = smart_rentals_wc_get_post_meta( $product_id, 'disabled_start_dates' );
        $disabled_end_dates = smart_rentals_wc_get_post_meta( $product_id, 'disabled_end_dates' );
        $disabled_ranges = [];
        
        if ( is_array( $disabled_start_dates ) && is_array( $disabled_end_dates ) ) {
            foreach ( $disabled_start_dates as $index => $start_date ) {
                $end_date = isset( $disabled_end_dates[$index] ) ? $disabled_end_dates[$index] : $start_date;
                if ( !empty( $start_date ) ) {
                    $disabled_ranges[] = [
                        'start' => $start_date,
                        'end' => $end_date
                    ];
                }
            }
        }
        echo json_encode( $disabled_ranges );
    ?>;
    
    // Get unavailable dates based on actual bookings and stock
    var unavailableDates = <?php 
        $unavailable_dates = [];
        $rental_stock = intval( smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' ) );
        
        if ( $rental_stock > 0 ) {
            // Check next 365 days for availability
            for ( $i = 0; $i < 365; $i++ ) {
                $check_date = date( 'Y-m-d', strtotime( '+' . $i . ' days' ) );
                $available_quantity = Smart_Rentals_WC()->options->get_calendar_day_availability( $product_id, $check_date );
                
                if ( $available_quantity <= 0 ) {
                    $unavailable_dates[] = $check_date;
                }
            }
        }
        
        echo json_encode( $unavailable_dates );
    ?>;
    
    // Initialize daterangepicker.com with Apply button
    function initDateRangePicker() {
        console.log('Initializing daterangepicker...');
        console.log('hasTimepicker:', hasTimepicker);
        console.log('rentalType:', rentalType);
        console.log('jQuery daterangepicker available:', typeof $.fn.daterangepicker !== 'undefined');
        console.log('Moment available:', typeof moment !== 'undefined');
        
        if (typeof $.fn.daterangepicker === 'undefined' || typeof moment === 'undefined') {
            console.error('Daterangepicker.com library not loaded, falling back to basic date inputs');
            initBasicDatePickers();
            return;
        }
        
        // Configure daterangepicker based on rental type
        console.log('Configuring daterangepicker - hasTimepicker:', hasTimepicker, 'useFixedTimes:', useFixedTimes);
        
        var dateRangeConfig = {
            // Core settings
            autoApply: false,
            autoUpdateInput: false,
            showDropdowns: true,
            timePicker: hasTimepicker,  // Only show time picker for hourly/mixed
            timePicker24Hour: true,
            timePickerIncrement: 30,
            timePickerSeconds: false,
            linkedCalendars: false,
            alwaysShowCalendars: true,
            opens: 'left',
            drops: 'down',
            
            // Format based on rental type
            locale: {
                format: hasTimepicker ? 'YYYY-MM-DD HH:mm' : 'YYYY-MM-DD',
                separator: ' - ',
                applyLabel: 'Apply',
                cancelLabel: 'Cancel',
                fromLabel: 'From',
                toLabel: 'To'
            },
            
            // Date constraints
            minDate: moment(),
            maxDate: moment().add(1, 'year')
        };
        
        // Add disabled dates validation (always needed for availability checking)
        dateRangeConfig.isInvalidDate = function(date) {
                // Check disabled weekdays
                if (disabledWeekdays && disabledWeekdays.length > 0) {
                    var dayOfWeek = date.day();
                    if (disabledWeekdays.indexOf(dayOfWeek) !== -1) {
                        return true;
                    }
                }
                
                // Check disabled date ranges
                if (disabledDates && disabledDates.length > 0) {
                    var currentDate = date.format('YYYY-MM-DD');
                    for (var i = 0; i < disabledDates.length; i++) {
                        var range = disabledDates[i];
                        if (currentDate >= range.start && currentDate <= range.end) {
                            return true;
                        }
                    }
                }
                
                // Check unavailable dates based on stock/bookings
                if (unavailableDates && unavailableDates.length > 0) {
                    var currentDate = date.format('YYYY-MM-DD');
                    if (unavailableDates.indexOf(currentDate) !== -1) {
                        return true;
                    }
                }
                
                return false;
            };
        
        // Set default start and end times based on rental type
        console.log('Setting default times - Pickup: <?php echo $default_pickup_time; ?>, Dropoff: <?php echo $default_dropoff_time; ?>');
        var pickupHour = parseInt('<?php echo date('H', strtotime($default_pickup_time)); ?>', 10);
        var pickupMinute = parseInt('<?php echo date('i', strtotime($default_pickup_time)); ?>', 10);
        var dropoffHour = parseInt('<?php echo date('H', strtotime($default_dropoff_time)); ?>', 10);
        var dropoffMinute = parseInt('<?php echo date('i', strtotime($default_dropoff_time)); ?>', 10);
        
        if (useFixedTimes) {
            // For daily rentals: Default to 1 night (today to tomorrow in calendar)
            dateRangeConfig.startDate = moment();
            dateRangeConfig.endDate = moment().add(1, 'day');
        } else {
            // For hourly/mixed: Default to same day with times
            dateRangeConfig.startDate = moment().hour(pickupHour).minute(pickupMinute);
            dateRangeConfig.endDate = moment().hour(dropoffHour).minute(dropoffMinute);
        }
        
        // Add ranges based on rental type
        if (useFixedTimes) {
            // For daily rentals (hotel logic): ranges represent nights
            dateRangeConfig.ranges = {
                '<?php _e( '1 Night', 'smart-rentals-wc' ); ?>': [moment(), moment().add(1, 'day')],
                '<?php _e( '2 Nights', 'smart-rentals-wc' ); ?>': [moment(), moment().add(2, 'days')],
                '<?php _e( '3 Nights', 'smart-rentals-wc' ); ?>': [moment(), moment().add(3, 'days')],
                '<?php _e( '1 Week', 'smart-rentals-wc' ); ?>': [moment(), moment().add(7, 'days')]
            };
        } else {
            // For hourly/mixed rentals: ranges with times
            dateRangeConfig.ranges = {
                '<?php _e( '2 Hours', 'smart-rentals-wc' ); ?>': [moment(), moment().add(2, 'hours')],
                '<?php _e( '4 Hours', 'smart-rentals-wc' ); ?>': [moment(), moment().add(4, 'hours')],
                '<?php _e( '8 Hours', 'smart-rentals-wc' ); ?>': [moment(), moment().add(8, 'hours')],
                '<?php _e( '1 Day', 'smart-rentals-wc' ); ?>': [moment(), moment().add(1, 'day')]
            };
        }
        
        // Initialize daterangepicker on a single input that controls both fields
        console.log('Daterangepicker config:', dateRangeConfig);
        console.log('Pickup date element found:', $('#pickup_date').length);
        
        try {
            console.log('Attempting to initialize daterangepicker...');
            $('#pickup_date').daterangepicker(dateRangeConfig);
            console.log('Daterangepicker initialized successfully');
            
            // Test if daterangepicker is actually working
            setTimeout(function() {
                if ($('#pickup_date').data('daterangepicker')) {
                    console.log('Daterangepicker is working correctly');
                } else {
                    console.warn('Daterangepicker failed to initialize, using fallback');
                    initBasicDatePickers();
                }
            }, 100);
            
        } catch (error) {
            console.error('Error initializing daterangepicker:', error);
            console.log('Falling back to basic datetime inputs');
            initBasicDatePickers();
            return;
        }
        
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
            
            // Format dates based on rental type
            var pickupFormatted, dropoffFormatted;
            
            if (useFixedTimes) {
                // For daily rentals (hotel-style booking logic)
                var pickupDate = startDate.format('YYYY-MM-DD');
                var checkoutDate = endDate.format('YYYY-MM-DD');
                
                // Hotel logic: Return is +1 day from calendar selection at dropoff time
                var actualReturnDate = moment(checkoutDate).add(1, 'day').format('YYYY-MM-DD');
                
                pickupFormatted = pickupDate + ' ' + '<?php echo $default_pickup_time; ?>';
                dropoffFormatted = actualReturnDate + ' ' + '<?php echo $default_dropoff_time; ?>';
                
                console.log('Daily rental hotel logic - Calendar selection:', pickupDate, 'to', checkoutDate);
                console.log('Actual booking period:', pickupFormatted, 'to', dropoffFormatted);
            } else {
                // For hourly/mixed rentals, use selected times
                pickupFormatted = startDate.format('YYYY-MM-DD HH:mm');
                dropoffFormatted = endDate.format('YYYY-MM-DD HH:mm');
            }
            
            // Check rental duration based on type
            var isOneDayRental, isNextDayReturn, nightsBooked;
            
            if (useFixedTimes) {
                // For daily rentals (hotel logic): calendar selection represents nights
                nightsBooked = endDate.diff(startDate, 'days');
                isOneDayRental = (nightsBooked === 0); // Same date selected twice
                isNextDayReturn = (nightsBooked === 1); // One night (Sept 25-26)
                
                console.log('Hotel booking logic - Nights booked:', nightsBooked);
            } else {
                // For hourly/mixed rentals: actual datetime logic
                isOneDayRental = startDate.format('YYYY-MM-DD') === endDate.format('YYYY-MM-DD');
                isNextDayReturn = endDate.diff(startDate, 'days') === 1;
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
            
            // Show special notice based on rental type and duration
            var noticeArea = $('#return-time-notice');
            var noticeText = noticeArea.find('.notice-text');
            
            if (useFixedTimes) {
                // For daily rentals (hotel logic) - clean message without nights text
                var message = '<?php _e( 'Product must be returned by', 'smart-rentals-wc' ); ?> ' + dropoffFormatted;
                
                noticeText.html('<strong>' + message + '</strong>');
                noticeArea.fadeIn();
            } else {
                // For hourly/mixed rentals, show if needed
                if (isOneDayRental || isNextDayReturn) {
                    var message = isOneDayRental ? 
                        '<?php _e( 'Same-day rental: Product must be returned by', 'smart-rentals-wc' ); ?> ' + dropoffFormatted :
                        '<?php _e( 'One-day rental: Product must be returned by', 'smart-rentals-wc' ); ?> ' + dropoffFormatted;
                    
                    noticeText.html('<strong>' + message + '</strong>');
                    noticeArea.fadeIn();
                } else {
                    noticeArea.fadeOut();
                }
            }
            
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
    
    // Fallback for basic date pickers - respect rental type
    function initBasicDatePickers() {
        console.log('Initializing basic datetime pickers as fallback');
        console.log('useFixedTimes:', useFixedTimes, 'hasTimepicker:', hasTimepicker);
        
        if (useFixedTimes) {
            // For daily rentals, use date inputs with fixed times
            $('#pickup_date, #dropoff_date').attr('type', 'date').removeAttr('readonly');
            
            var today = new Date().toISOString().split('T')[0];
            $('#pickup_date').attr('min', today);
        } else {
            // For hourly/mixed rentals, use datetime-local inputs
            $('#pickup_date, #dropoff_date').attr('type', 'datetime-local').removeAttr('readonly');
            
            var now = new Date();
            var minDateTime = now.toISOString().slice(0, 16);
            $('#pickup_date').attr('min', minDateTime);
        }
        
        // Set default values based on rental type
        if (useFixedTimes) {
            // For daily rentals, set date only (times are fixed)
            var today = moment().format('YYYY-MM-DD');
            var tomorrow = moment().add(1, 'day').format('YYYY-MM-DD');
            
            if (!$('#pickup_date').val()) {
                $('#pickup_date').val(today);
            }
            if (!$('#dropoff_date').val()) {
                $('#dropoff_date').val(tomorrow);
            }
        } else {
            // For hourly/mixed rentals, set datetime
            var pickupHour = parseInt('<?php echo date('H', strtotime($default_pickup_time)); ?>', 10);
            var pickupMinute = parseInt('<?php echo date('i', strtotime($default_pickup_time)); ?>', 10);
            var dropoffHour = parseInt('<?php echo date('H', strtotime($default_dropoff_time)); ?>', 10);
            var dropoffMinute = parseInt('<?php echo date('i', strtotime($default_dropoff_time)); ?>', 10);
            
            var defaultPickupDateTime = moment().hour(pickupHour).minute(pickupMinute).format('YYYY-MM-DDTHH:mm');
            var defaultDropoffDateTime = moment().add(1, 'day').hour(dropoffHour).minute(dropoffMinute).format('YYYY-MM-DDTHH:mm');
            
            if (!$('#pickup_date').val()) {
                $('#pickup_date').val(defaultPickupDateTime);
            }
            if (!$('#dropoff_date').val()) {
                $('#dropoff_date').val(defaultDropoffDateTime);
            }
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
                
                // For daily rentals with fixed times, apply hotel booking logic
                var finalPickupVal = pickupVal;
                var finalDropoffVal = dropoffVal;
                
                if (useFixedTimes) {
                    // Hotel logic: Add +1 day to checkout date for actual return
                    if (pickupVal.indexOf(':') === -1) {
                        finalPickupVal = pickupVal + ' ' + '<?php echo $default_pickup_time; ?>';
                    }
                    if (dropoffVal.indexOf(':') === -1) {
                        // Add +1 day to dropoff date for hotel logic
                        var dropoffMoment = moment(dropoffVal).add(1, 'day');
                        finalDropoffVal = dropoffMoment.format('YYYY-MM-DD') + ' ' + '<?php echo $default_dropoff_time; ?>';
                    }
                    
                    console.log('Hotel logic applied - Original dropoff:', dropoffVal, 'Actual return:', finalDropoffVal);
                }
                
                // Show return time notice for fallback inputs too
                var pickupMoment = moment(finalPickupVal);
                var dropoffMoment = moment(finalDropoffVal);
                var isOneDayRental = pickupMoment.format('YYYY-MM-DD') === dropoffMoment.format('YYYY-MM-DD');
                var isNextDayReturn = dropoffMoment.diff(pickupMoment, 'days') === 1;
                
                var noticeArea = $('#return-time-notice');
                var noticeText = noticeArea.find('.notice-text');
                
                if (isOneDayRental || isNextDayReturn) {
                    var message = isOneDayRental ? 
                        '<?php _e( 'Same-day rental: Product must be returned by', 'smart-rentals-wc' ); ?> ' + finalDropoffVal :
                        '<?php _e( 'One-day rental: Product must be returned by', 'smart-rentals-wc' ); ?> ' + finalDropoffVal;
                    
                    noticeText.html('<strong>' + message + '</strong>');
                    noticeArea.fadeIn();
                } else {
                    noticeArea.fadeOut();
                }
                
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
    console.log('Starting daterangepicker initialization check...');
    setTimeout(function() {
        console.log('Timeout reached, checking libraries...');
        console.log('Moment available:', typeof moment !== 'undefined');
        console.log('Daterangepicker available:', typeof $.fn.daterangepicker !== 'undefined');
        console.log('Pickup date element exists:', $('#pickup_date').length > 0);
        
        if (typeof moment !== 'undefined' && typeof $.fn.daterangepicker !== 'undefined') {
            console.log('Libraries available, calling initDateRangePicker...');
            initDateRangePicker();
        } else {
            console.warn('Daterangepicker.com or Moment.js not available, using fallback');
            console.log('Available libraries:', {
                moment: typeof moment,
                daterangepicker: typeof $.fn.daterangepicker,
                jquery: typeof $
            });
            initBasicDatePickers();
        }
    }, 500); // Increased timeout to ensure libraries are loaded
    
    console.log('Smart Rentals Form Fields with daterangepicker.com (Apply button) initialized');
});
</script>