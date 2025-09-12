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
		<?php if ( $has_timepicker ) : ?>
			<input
				type="text"
				id="pickup_date"
				class="pickup-date smart-rentals-input-required"
				name="pickup_date"
				value="<?php echo esc_attr( $pickup_date ); ?>"
				required
				data-type="datetimepicker"
				data-date="<?php echo esc_attr( $pickup_date ? gmdate( $date_format, strtotime( $pickup_date ) ) : '' ); ?>"
				data-time="<?php echo esc_attr( $pickup_date ? gmdate( $time_format, strtotime( $pickup_date ) ) : '' ); ?>"
				placeholder="<?php echo esc_attr( Smart_Rentals_WC()->options->get_date_placeholder() . ' ' . Smart_Rentals_WC()->options->get_time_placeholder() ); ?>"
				readonly
			/>
		<?php else : ?>
			<input
				type="text"
				id="pickup_date"
				class="pickup-date smart-rentals-input-required"
				name="pickup_date"
				value="<?php echo esc_attr( $pickup_date ); ?>"
				required
				data-type="datepicker"
				data-date="<?php echo esc_attr( $pickup_date ? gmdate( $date_format, strtotime( $pickup_date ) ) : '' ); ?>"
				placeholder="<?php echo esc_attr( Smart_Rentals_WC()->options->get_date_placeholder() ); ?>"
				readonly
			/>
		<?php endif; ?>
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
		<?php if ( $has_timepicker ) : ?>
			<input
				type="text"
				id="dropoff_date"
				class="dropoff-date smart-rentals-input-required"
				name="dropoff_date"
				value="<?php echo esc_attr( $dropoff_date ); ?>"
				required
				data-type="datetimepicker"
				data-date="<?php echo esc_attr( $dropoff_date ? gmdate( $date_format, strtotime( $dropoff_date ) ) : '' ); ?>"
				data-time="<?php echo esc_attr( $dropoff_date ? gmdate( $time_format, strtotime( $dropoff_date ) ) : '' ); ?>"
				placeholder="<?php echo esc_attr( Smart_Rentals_WC()->options->get_date_placeholder() . ' ' . Smart_Rentals_WC()->options->get_time_placeholder() ); ?>"
				readonly
			/>
		<?php else : ?>
			<input
				type="text"
				id="dropoff_date"
				class="dropoff-date smart-rentals-input-required"
				name="dropoff_date"
				value="<?php echo esc_attr( $dropoff_date ); ?>"
				required
				data-type="datepicker"
				data-date="<?php echo esc_attr( $dropoff_date ? gmdate( $date_format, strtotime( $dropoff_date ) ) : '' ); ?>"
				placeholder="<?php echo esc_attr( Smart_Rentals_WC()->options->get_date_placeholder() ); ?>"
				readonly
			/>
		<?php endif; ?>
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
    
    // Initialize date pickers (simplified approach)
    function initDatePickers() {
        // Convert readonly text inputs to date/datetime inputs
        $('input[data-type="datepicker"]').each(function() {
            $(this).attr('type', 'date').removeAttr('readonly');
            
            // Set minimum date to today
            var today = new Date().toISOString().split('T')[0];
            $(this).attr('min', today);
        });
        
        $('input[data-type="datetimepicker"]').each(function() {
            $(this).attr('type', 'datetime-local').removeAttr('readonly');
            
            // Set minimum datetime to now
            var now = new Date();
            var minDateTime = now.toISOString().slice(0, 16);
            $(this).attr('min', minDateTime);
        });
        
        // Update dropoff minimum when pickup changes
        $('#pickup_date').on('change', function() {
            var pickupDate = $(this).val();
            if (pickupDate) {
                var minDropoff = new Date(pickupDate);
                minDropoff.setDate(minDropoff.getDate() + 1);
                
                if ($(this).attr('type') === 'date') {
                    $('#dropoff_date').attr('min', minDropoff.toISOString().split('T')[0]);
                } else {
                    $('#dropoff_date').attr('min', minDropoff.toISOString().slice(0, 16));
                }
            }
        });
    }
    
    // Trigger calculation when dates change
    $('#pickup_date, #dropoff_date, #smart_rentals_quantity').on('change', function() {
        // Trigger the main form's calculation
        if (typeof window.smartRentalsCalculateTotal === 'function') {
            setTimeout(window.smartRentalsCalculateTotal, 100);
        }
    });
    
    // Initialize
    initDatePickers();
    
    console.log('Smart Rentals Form Fields Initialized');
});
</script>