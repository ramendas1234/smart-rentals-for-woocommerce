<?php
/**
 * Single Product Booking Form Template (Based on External Plugin)
 */

if ( !defined( 'ABSPATH' ) ) exit();

// Get product from global or args
global $product;
if ( !$product && isset( $args['product_id'] ) ) {
    $product = wc_get_product( $args['product_id'] );
}

if ( !$product || !smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
    return;
}

$product_id = $product->get_id();

?>

<div class="smart-rentals-booking-form-container" id="smart-rentals-booking-form-container">
	<h3 class="title"><?php esc_html_e( 'Booking Form', 'smart-rentals-wc' ); ?></h3>
	<form
		id="smart_rentals_booking_form"
		class="form smart-rentals-form"
		action="<?php echo esc_url( home_url('/') ); ?>"
		method="POST"
		enctype="multipart/form-data"
		data-run_ajax="true"
		autocomplete="off">
		<div class="smart-rentals-container wrap_fields">
			<div class="smart-rentals-row">
				<div class="wrap-item two_column">
					<!-- Display Booking Form Fields -->
					<?php
						/**
						 * Hook: smart_rentals_booking_form
						 * @hooked: rental_booking_form_fields - 5
						 * @hooked: rental_booking_form_total - 30
						 */
						do_action( 'smart_rentals_booking_form', $product_id );
					?>
				</div>
			</div>
		</div>
		
		<button type="submit" class="submit btn_tran smart-rentals-book-now">
			<?php esc_html_e( 'Book Now', 'smart-rentals-wc' ); ?>
		</button>
		
		<!-- Hidden Fields -->
		<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" />
	</form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    'use strict';
    
    // Form submission handler (following external plugin pattern)
    $('#smart_rentals_booking_form').on('submit', function(e) {
        e.preventDefault();
        
        var $form = $(this);
        var $submitButton = $('.smart-rentals-book-now');
        var originalText = $submitButton.text();
        
        // Basic validation
        var pickupDate = $('#pickup_date').val();
        var dropoffDate = $('#dropoff_date').val();
        var quantity = $('#smart_rentals_quantity').val() || 1;
        
        if (!pickupDate || !dropoffDate) {
            showMessage('Please select pickup and drop-off dates.', 'error');
            return false;
        }
        
        var pickup = new Date(pickupDate);
        var dropoff = new Date(dropoffDate);
        
        if (pickup >= dropoff) {
            showMessage('Drop-off date must be after pickup date.', 'error');
            return false;
        }
        
        // Get cart data from the total calculator
        var cartData = $form.data('cart-item');
        if (!cartData) {
            showMessage('Please wait for price calculation to complete.', 'error');
            // Try to trigger calculation
            if (typeof window.smartRentalsCalculateTotal === 'function') {
                window.smartRentalsCalculateTotal();
                setTimeout(function() {
                    cartData = $form.data('cart-item');
                    if (cartData) {
                        $form.trigger('submit');
                    } else {
                        showMessage('Unable to calculate rental price. Please try refreshing the page.', 'error');
                    }
                }, 2000);
            }
            return false;
        }
        
        // Update cart data with current form values
        cartData.pickup_date = pickupDate;
        cartData.dropoff_date = dropoffDate;
        cartData.quantity = quantity;
        cartData.rental_data.pickup_date = pickupDate;
        cartData.rental_data.dropoff_date = dropoffDate;
        cartData.rental_data.rental_quantity = quantity;
        
        // Check if AJAX is available
        if (typeof ajax_object === 'undefined') {
            console.error('AJAX not available, using standard form submission');
            showMessage('Booking system not available. Please refresh the page.', 'error');
            return false;
        }
        
        // Show loading state
        $submitButton.prop('disabled', true).text('Processing...');
        
        // Submit via AJAX (following external plugin pattern)
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'smart_rentals_add_to_cart',
                security: ajax_object.security,
                product_id: <?php echo intval( $product_id ); ?>,
                product_url: window.location.href,
                cart_item: cartData
            },
            success: function(response) {
                console.log('Add to cart response:', response);
                
                if (response.success) {
                    showMessage('Product added to cart successfully!', 'success');
                    
                    // Redirect to cart after short delay
                    setTimeout(function() {
                        if (response.data && response.data.cart_url) {
                            window.location.href = response.data.cart_url;
                        } else {
                            window.location.reload();
                        }
                    }, 1500);
                } else {
                    var errorMsg = (response.data && response.data.message) ? 
                        response.data.message : 
                        'Failed to add product to cart.';
                    showMessage(errorMsg, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Add to cart error:', { xhr: xhr, status: status, error: error });
                showMessage('Server error occurred. Please try again.', 'error');
            },
            complete: function() {
                $submitButton.prop('disabled', false).text(originalText);
            }
        });
        
        return false;
    });
    
    // Message display function
    function showMessage(message, type) {
        $('.smart-rentals-message').remove();
        
        var messageClass = 'smart-rentals-message smart-rentals-' + type;
        var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';
        $('.smart-rentals-booking-form-container').prepend(messageHtml);
        
        if (type === 'success') {
            setTimeout(function() {
                $('.smart-rentals-message.smart-rentals-success').fadeOut();
            }, 5000);
        }
    }
    
    console.log('Smart Rentals Booking Form Submission Handler Initialized');
});
</script>