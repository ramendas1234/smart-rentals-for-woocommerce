<?php
/**
 * Booking Form Fields Template (Based on External Plugin)
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

<!-- Date fields -->
<?php if ( in_array( $rental_type, [ 'day', 'hour', 'mixed', 'hotel', 'appointment' ] ) ): ?>
	<!-- Pick-up date -->
	<div class="rental_item">
		<label>
			<?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?>
			<span class="required">*</span>
		</label>
		<div class="date-input-wrapper">
			<input
				type="text"
				id="pickup_date"
				class="pickup-date smart-rentals-input-required flatpickr-input"
				name="pickup_date"
				value="<?php echo esc_attr( $pickup_date ); ?>"
				required
				data-type="<?php echo $has_timepicker ? 'datetimepicker' : 'datepicker'; ?>"
				placeholder="<?php echo esc_attr( $has_timepicker ? Smart_Rentals_WC()->options->get_date_placeholder() . ' ' . Smart_Rentals_WC()->options->get_time_placeholder() : Smart_Rentals_WC()->options->get_date_placeholder() ); ?>"
				readonly
			/>
			<i class="date-icon dashicons dashicons-calendar-alt"></i>
		</div>
	    <span class="smart-rentals-loader-date">
	    	<i class="dashicons dashicons-update-alt" aria-hidden="true"></i>
	    </span>
	</div><!-- End Pick-up date -->

	<!-- Drop-off date -->
	<div class="rental_item">
		<label>
			<?php _e( 'Drop-off Date', 'smart-rentals-wc' ); ?>
			<span class="required">*</span>
		</label>
		<div class="date-input-wrapper">
			<input
				type="text"
				id="dropoff_date"
				class="dropoff-date smart-rentals-input-required flatpickr-input"
				name="dropoff_date"
				value="<?php echo esc_attr( $dropoff_date ); ?>"
				required
				data-type="<?php echo $has_timepicker ? 'datetimepicker' : 'datepicker'; ?>"
				placeholder="<?php echo esc_attr( $has_timepicker ? Smart_Rentals_WC()->options->get_date_placeholder() . ' ' . Smart_Rentals_WC()->options->get_time_placeholder() : Smart_Rentals_WC()->options->get_date_placeholder() ); ?>"
				readonly
			/>
			<i class="date-icon dashicons dashicons-calendar-alt"></i>
		</div>
	    <span class="smart-rentals-loader-date">
	    	<i class="dashicons dashicons-update-alt" aria-hidden="true"></i>
	    </span>
	</div><!-- End Drop-off date -->
<?php endif; ?>

<!-- Quantity -->
<div class="rental_item">
	<label>
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
    
    // Initialize modern date pickers using our enhanced class
    function initModernDatePickers() {
        if (typeof window.SmartRentalsModernDatePicker === 'undefined') {
            console.error('Modern date picker class not available, falling back to basic');
            initBasicDatePickers();
            return;
        }
        
        // Configuration for our rental date picker
        var datePickerConfig = {
            dateFormat: hasTimepicker ? "Y-m-d H:i" : "Y-m-d",
            enableTime: hasTimepicker,
            time_24hr: true,
            mode: "range",
            showMonths: window.innerWidth > 768 ? 2 : 1,
            minDate: "today",
            maxDate: new Date().fp_incr(365), // 1 year from today
            altInput: true,
            altFormat: hasTimepicker ? "F j, Y at H:i" : "F j, Y",
            ariaDateFormat: "F j, Y",
            animate: true,
            position: "auto",
            static: window.innerWidth <= 768
        };
        
        // Add minimum rental period validation
        if (minRentalPeriod > 0) {
            datePickerConfig.minDate = "today";
            datePickerConfig.disable = [
                function(date) {
                    // Disable dates that would result in rental period less than minimum
                    var today = new Date();
                    var diffDays = Math.ceil((date - today) / (1000 * 60 * 60 * 24));
                    return diffDays < 0; // Only disable past dates, let validation handle minimum period
                }
            ];
        }
        
        // Initialize the modern date picker
        var picker = window.SmartRentalsModernDatePicker.init('#pickup_date', '#dropoff_date', datePickerConfig);
        
        console.log('Modern date picker initialized with enhanced UI');
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
        
        console.log('Basic date pickers initialized');
    }
    
    // Show range duration
    function showRangeDuration(pickup, dropoff) {
        var durationMs = dropoff - pickup;
        var durationDays = Math.ceil(durationMs / (1000 * 60 * 60 * 24));
        var durationHours = Math.ceil(durationMs / (1000 * 60 * 60));
        
        var durationText = '';
        if (hasTimepicker && durationHours < 24) {
            durationText = durationHours + ' ' + (durationHours === 1 ? 'hour' : 'hours');
        } else {
            durationText = durationDays + ' ' + (durationDays === 1 ? 'day' : 'days');
        }
        
        // Show duration in the UI
        $('.range-duration').remove();
        $('.smart-rentals-container').append('<div class="range-duration"><i class="dashicons dashicons-clock"></i> ' + durationText + '</div>');
        
        setTimeout(function() {
            $('.range-duration').fadeOut();
        }, 3000);
    }
    
    // Trigger calculation when dates change
    $('#pickup_date, #dropoff_date, #smart_rentals_quantity').on('change', function() {
        if (typeof window.smartRentalsCalculateTotal === 'function') {
            setTimeout(window.smartRentalsCalculateTotal, 100);
        }
    });
    
    // Initialize date pickers
    setTimeout(initModernDatePickers, 100);
    
    console.log('Smart Rentals Form Fields with Modern UI Initialized');
});
</script>