<?php
/**
 * Booking Form Total Template (Based on External Plugin)
 */

if ( !defined( 'ABSPATH' ) ) exit();

$product_id = $args['product_id'];
$security_deposit = smart_rentals_wc_get_post_meta( $product_id, 'security_deposit' );

?>

<div class="rental_item total-section">
	<div id="smart-rentals-total-display" class="smart-rentals-total-display" style="display: none;">
		<div class="total-header">
			<h4 class="total-title"><?php _e( 'Rental Summary', 'smart-rentals-wc' ); ?></h4>
			<div class="availability-info">
				<span class="availability-label"><?php _e( 'Available:', 'smart-rentals-wc' ); ?></span>
				<span class="availability-count" id="rental-availability-count">-</span>
				<span class="availability-text" id="rental-availability-text"><?php _e( 'items', 'smart-rentals-wc' ); ?></span>
			</div>
		</div>
		
		<div class="total-breakdown">
			<div class="total-row duration-row">
				<span class="label"><?php _e( 'Duration:', 'smart-rentals-wc' ); ?></span>
				<span class="value" id="rental-duration-text">-</span>
			</div>
			
			<div class="total-row subtotal-row">
				<span class="label"><?php _e( 'Subtotal:', 'smart-rentals-wc' ); ?></span>
				<span class="value" id="rental-subtotal-amount">-</span>
			</div>
			
			<?php if ( $security_deposit > 0 ) : ?>
			<div class="total-row deposit-row">
				<span class="label"><?php _e( 'Security Deposit:', 'smart-rentals-wc' ); ?></span>
				<span class="value"><?php echo smart_rentals_wc_price( $security_deposit ); ?></span>
			</div>
			<?php endif; ?>
			
			<div class="total-row total-row-final">
				<span class="label"><?php _e( 'Total:', 'smart-rentals-wc' ); ?></span>
				<span class="value" id="rental-total-amount">-</span>
			</div>
		</div>
	</div>
	
	<div id="smart-rentals-error-display" class="smart-rentals-error-display" style="display: none;">
		<p class="error-message" id="rental-error-message"></p>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    'use strict';
    
    var productId = <?php echo intval( $product_id ); ?>;
    var securityDeposit = <?php echo floatval( $security_deposit ); ?>;
    var rentalType = '<?php echo esc_js( smart_rentals_wc_get_post_meta( $product_id, 'rental_type' ) ); ?>';
    var hasTimepicker = <?php echo in_array( smart_rentals_wc_get_post_meta( $product_id, 'rental_type' ), [ 'hour', 'mixed', 'appointment' ] ) ? 'true' : 'false'; ?>;
    
    // Make calculation function globally available
    window.smartRentalsCalculateTotal = function() {
        var pickupDate = $('#pickup_date').val();
        var dropoffDate = $('#dropoff_date').val();
        var quantity = $('#smart_rentals_quantity').val() || 1;
        
        console.log('Calculating total for:', {
            pickup: pickupDate,
            dropoff: dropoffDate,
            quantity: quantity,
            productId: productId,
            hasTimepicker: hasTimepicker
        });
        
        if (!pickupDate || !dropoffDate) {
            $('#smart-rentals-total-display').hide();
            $('#smart-rentals-error-display').hide();
            console.log('No dates provided, hiding displays');
            return;
        }
        
        // Enhanced date validation for datetime formats
        var pickup, dropoff;
        
        if (hasTimepicker) {
            // For datetime formats, parse as datetime
            pickup = new Date(pickupDate.replace(' ', 'T')); // Convert to ISO format
            dropoff = new Date(dropoffDate.replace(' ', 'T'));
        } else {
            // For date-only formats
            pickup = new Date(pickupDate);
            dropoff = new Date(dropoffDate);
        }
        
        console.log('Parsed dates:', { pickup: pickup, dropoff: dropoff });
        
        if (isNaN(pickup.getTime()) || isNaN(dropoff.getTime())) {
            console.error('Invalid date objects created');
            showError('Invalid date format. Please select valid dates.');
            return;
        }
        
        if (pickup >= dropoff) {
            console.error('Pickup date is not before dropoff date');
            showError('Drop-off date must be after pickup date.');
            return;
        }
        
        // Check if AJAX is available
        if (typeof ajax_object === 'undefined') {
            console.error('AJAX object not available!');
            showError('Booking system not available. Please refresh the page.');
            return;
        }
        
        // Show loading
        $('.smart-rentals-loader-date').show();
        $('#smart-rentals-total-display').hide();
        $('#smart-rentals-error-display').hide();
        
        // AJAX call to calculate total
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'smart_rentals_calculate_total',
                security: ajax_object.security,
                product_id: productId,
                pickup_date: pickupDate,
                dropoff_date: dropoffDate,
                quantity: quantity
            },
            success: function(response) {
                $('.smart-rentals-loader-date').hide();
                
                console.log('AJAX Response:', response);
                
                if (response.success && response.data) {
                    var data = response.data;
                    
                    // Update display (use .html() for formatted prices, .text() for plain text)
                    $('#rental-subtotal-amount').html(data.formatted_price || '0');
                    $('#rental-duration-text').text(data.duration_text || '-');
                    
                    // Update availability display
                    var availableItems = parseInt(data.available_quantity || 0);
                    $('#rental-availability-count').text(availableItems);
                    
                    // Update availability styling based on stock level
                    var $availabilityInfo = $('.availability-info');
                    $availabilityInfo.removeClass('low-stock out-of-stock in-stock');
                    
                    if (availableItems === 0) {
                        $availabilityInfo.addClass('out-of-stock');
                        $('#rental-availability-text').text('<?php _e( 'out of stock', 'smart-rentals-wc' ); ?>');
                    } else if (availableItems <= 3) {
                        $availabilityInfo.addClass('low-stock');
                        $('#rental-availability-text').text(availableItems === 1 ? '<?php _e( 'item left', 'smart-rentals-wc' ); ?>' : '<?php _e( 'items left', 'smart-rentals-wc' ); ?>');
                    } else {
                        $availabilityInfo.addClass('in-stock');
                        $('#rental-availability-text').text('<?php _e( 'items available', 'smart-rentals-wc' ); ?>');
                    }
                    
                    // Calculate and display total
                    var totalAmount = parseFloat(data.total_price || 0) + parseFloat(securityDeposit || 0);
                    var currencySymbol = '<?php echo function_exists( 'get_woocommerce_currency_symbol' ) ? esc_js( get_woocommerce_currency_symbol() ) : '$'; ?>';
                    $('#rental-total-amount').text(currencySymbol + totalAmount.toFixed(2));
                    
                    $('#smart-rentals-total-display').show();
                    
                    // Store cart data for form submission
                    var cartData = {
                        product_id: productId,
                        pickup_date: pickupDate,
                        dropoff_date: dropoffDate,
                        quantity: quantity,
                        rental_data: {
                            pickup_date: pickupDate,
                            dropoff_date: dropoffDate,
                            product_id: productId,
                            rental_quantity: quantity,
                            duration_text: data.duration_text
                        }
                    };
                    
                    $('#smart_rentals_booking_form').data('cart-item', cartData);
                    
                } else {
                    var errorMsg = (response.data && response.data.message) ? 
                        response.data.message : 
                        'Unable to calculate rental price.';
                    showError(errorMsg);
                }
            },
            error: function(xhr, status, error) {
                $('.smart-rentals-loader-date').hide();
                console.error('AJAX Error:', { xhr: xhr, status: status, error: error });
                showError('Server error occurred. Please try again.');
            }
        });
    };
    
    function showError(message) {
        $('#rental-error-message').text(message);
        $('#smart-rentals-error-display').show();
        $('#smart-rentals-total-display').hide();
    }
    
    // Bind events to trigger calculation
    $('#pickup_date, #dropoff_date, #smart_rentals_quantity').on('change', function() {
        setTimeout(window.smartRentalsCalculateTotal, 100);
    });
    
    console.log('Smart Rentals Total Calculator Initialized');
});
</script>