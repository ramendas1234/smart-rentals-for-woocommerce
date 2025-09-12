<?php
/**
 * Rental Booking Form Template
 */

if ( !defined( 'ABSPATH' ) ) exit();

$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
$enable_calendar = smart_rentals_wc_get_post_meta( $product_id, 'enable_calendar' );
$min_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'min_rental_period' );
$max_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'max_rental_period' );

// Get date format
$date_format = Smart_Rentals_WC()->options->get_date_format();
$time_format = Smart_Rentals_WC()->options->get_time_format();

// Get values from URL parameters
$pickup_date = isset( $_GET['pickup_date'] ) ? sanitize_text_field( $_GET['pickup_date'] ) : '';
$dropoff_date = isset( $_GET['dropoff_date'] ) ? sanitize_text_field( $_GET['dropoff_date'] ) : '';
$pickup_time = isset( $_GET['pickup_time'] ) ? sanitize_text_field( $_GET['pickup_time'] ) : '';
$dropoff_time = isset( $_GET['dropoff_time'] ) ? sanitize_text_field( $_GET['dropoff_time'] ) : '';
?>

<div class="smart-rentals-booking-form" id="smart-rentals-booking-form">
    <h3 class="title"><?php _e( 'Rental Details', 'smart-rentals-wc' ); ?></h3>
    <form
        id="smart_rentals_booking_form"
        class="form smart-rentals-form"
        action="<?php echo esc_url( home_url( '/' ) ); ?>"
        method="POST"
        enctype="multipart/form-data"
        data-run_ajax="true"
        autocomplete="off">
        
        <div class="smart-rentals-container wrap_fields">
            <div class="smart-rentals-row">
                <div class="wrap-item">
                    
                    <!-- Pickup Date -->
                    <div class="rental_item">
                        <label for="pickup_date">
                            <?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?>
                            <span class="required">*</span>
                        </label>
                        <?php if ( in_array( $rental_type, [ 'hour', 'mixed', 'appointment' ] ) ) : ?>
                            <input
                                type="text"
                                id="pickup_date"
                                name="pickup_date"
                                class="pickup-date smart-rentals-input-required"
                                value="<?php echo esc_attr( $pickup_date ); ?>"
                                required
                                data-type="datetimepicker"
                                data-date="<?php echo esc_attr( $pickup_date ? gmdate( $date_format, strtotime( $pickup_date ) ) : '' ); ?>"
                                data-time="<?php echo esc_attr( $pickup_time ? gmdate( $time_format, strtotime( $pickup_time ) ) : '' ); ?>"
                                placeholder="<?php echo esc_attr( Smart_Rentals_WC()->options->get_date_placeholder() . ' ' . Smart_Rentals_WC()->options->get_time_placeholder() ); ?>"
                                readonly
                            />
                        <?php else : ?>
                            <input
                                type="text"
                                id="pickup_date"
                                name="pickup_date"
                                class="pickup-date smart-rentals-input-required"
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
                    </div>

                    <!-- Dropoff Date -->
                    <div class="rental_item">
                        <label for="dropoff_date">
                            <?php _e( 'Drop-off Date', 'smart-rentals-wc' ); ?>
                            <span class="required">*</span>
                        </label>
                        <?php if ( in_array( $rental_type, [ 'hour', 'mixed', 'appointment' ] ) ) : ?>
                            <input
                                type="text"
                                id="dropoff_date"
                                name="dropoff_date"
                                class="dropoff-date smart-rentals-input-required"
                                value="<?php echo esc_attr( $dropoff_date ); ?>"
                                required
                                data-type="datetimepicker"
                                data-date="<?php echo esc_attr( $dropoff_date ? gmdate( $date_format, strtotime( $dropoff_date ) ) : '' ); ?>"
                                data-time="<?php echo esc_attr( $dropoff_time ? gmdate( $time_format, strtotime( $dropoff_time ) ) : '' ); ?>"
                                placeholder="<?php echo esc_attr( Smart_Rentals_WC()->options->get_date_placeholder() . ' ' . Smart_Rentals_WC()->options->get_time_placeholder() ); ?>"
                                readonly
                            />
                        <?php else : ?>
                            <input
                                type="text"
                                id="dropoff_date"
                                name="dropoff_date"
                                class="dropoff-date smart-rentals-input-required"
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
                    </div>

                    <!-- Quantity -->
                    <div class="rental_item">
                        <label for="quantity">
                            <?php _e( 'Quantity', 'smart-rentals-wc' ); ?>
                        </label>
                        <input
                            type="number"
                            id="quantity"
                            name="quantity"
                            class="quantity"
                            value="1"
                            min="1"
                            max="<?php echo esc_attr( smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' ) ?: 10 ); ?>"
                        />
                    </div>

                    <!-- Rental Info -->
                    <div class="smart-rentals-info">
                        <?php if ( $min_rental_period ) : ?>
                            <p class="rental-period-info">
                                <?php 
                                if ( $rental_type === 'hour' ) {
                                    printf( __( 'Minimum rental period: %d hours', 'smart-rentals-wc' ), $min_rental_period );
                                } else {
                                    printf( __( 'Minimum rental period: %d days', 'smart-rentals-wc' ), $min_rental_period );
                                }
                                ?>
                            </p>
                        <?php endif; ?>

                        <?php if ( $max_rental_period ) : ?>
                            <p class="rental-period-info">
                                <?php 
                                if ( $rental_type === 'hour' ) {
                                    printf( __( 'Maximum rental period: %d hours', 'smart-rentals-wc' ), $max_rental_period );
                                } else {
                                    printf( __( 'Maximum rental period: %d days', 'smart-rentals-wc' ), $max_rental_period );
                                }
                                ?>
                            </p>
                        <?php endif; ?>

                        <!-- Price Display -->
                        <div id="rental-price-display" class="rental-price-display" style="display: none;">
                            <div class="price-breakdown">
                                <div class="price-item">
                                    <span class="label"><?php _e( 'Rental Price:', 'smart-rentals-wc' ); ?></span>
                                    <span class="value" id="rental-price-amount">-</span>
                                </div>
                                <div class="price-item">
                                    <span class="label"><?php _e( 'Duration:', 'smart-rentals-wc' ); ?></span>
                                    <span class="value" id="rental-duration-text">-</span>
                                </div>
                                <div class="price-item security-deposit" id="security-deposit-display" style="display: none;">
                                    <span class="label"><?php _e( 'Security Deposit:', 'smart-rentals-wc' ); ?></span>
                                    <span class="value" id="security-deposit-amount">-</span>
                                </div>
                                <div class="price-item total">
                                    <span class="label"><?php _e( 'Total:', 'smart-rentals-wc' ); ?></span>
                                    <span class="value" id="total-amount">-</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calendar -->
                    <?php if ( 'yes' === $enable_calendar ) : ?>
                        <?php smart_rentals_wc_get_calendar( $product_id ); ?>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="submit btn_tran smart-rentals-book-now">
            <?php _e( 'Book Now', 'smart-rentals-wc' ); ?>
        </button>

        <!-- Hidden Fields -->
        <input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>" />
        <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product_id ); ?>" />
        <input type="hidden" name="rental_type" value="<?php echo esc_attr( $rental_type ); ?>" />
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    'use strict';
    
    var productId = <?php echo intval( $product_id ); ?>;
    var rentalType = '<?php echo esc_js( $rental_type ); ?>';
    var minPeriod = <?php echo intval( $min_rental_period ); ?>;
    var maxPeriod = <?php echo intval( $max_rental_period ); ?>;
    
    // Initialize date pickers
    function initDatePickers() {
        // Simple date picker initialization
        $('input[data-type="datepicker"]').attr('type', 'date').removeAttr('readonly');
        $('input[data-type="datetimepicker"]').attr('type', 'datetime-local').removeAttr('readonly');
        
        // Set minimum dates
        var today = new Date().toISOString().split('T')[0];
        $('#pickup_date').attr('min', today);
        
        // Update dropoff minimum when pickup changes
        $('#pickup_date').on('change', function() {
            var pickupDate = $(this).val();
            if (pickupDate) {
                var minDropoff = new Date(pickupDate);
                minDropoff.setDate(minDropoff.getDate() + 1);
                $('#dropoff_date').attr('min', minDropoff.toISOString().split('T')[0]);
            }
        });
    }
    
    // Fallback client-side price calculation
    function calculateClientSidePrice(pickupDate, dropoffDate, quantity) {
        var dailyPrice = <?php echo floatval( smart_rentals_wc_get_post_meta( $product_id, 'daily_price' ) ); ?>;
        var hourlyPrice = <?php echo floatval( smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' ) ); ?>;
        var securityDeposit = <?php echo floatval( smart_rentals_wc_get_post_meta( $product_id, 'security_deposit' ) ); ?>;
        var currencySymbol = '<?php echo function_exists( 'get_woocommerce_currency_symbol' ) ? esc_js( get_woocommerce_currency_symbol() ) : '$'; ?>';
        
        var pickup = new Date(pickupDate);
        var dropoff = new Date(dropoffDate);
        var durationMs = dropoff - pickup;
        var durationHours = durationMs / (1000 * 60 * 60);
        var durationDays = durationMs / (1000 * 60 * 60 * 24);
        
        var totalPrice = 0;
        var durationText = '';
        
        switch (rentalType) {
            case 'day':
            case 'hotel':
                var days = Math.max(1, Math.ceil(durationDays));
                totalPrice = dailyPrice * days * quantity;
                durationText = days + ' ' + (days === 1 ? '<?php echo esc_js( __( 'day', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>');
                break;
            case 'hour':
            case 'appointment':
                var hours = Math.max(1, Math.ceil(durationHours));
                totalPrice = hourlyPrice * hours * quantity;
                durationText = hours + ' ' + (hours === 1 ? '<?php echo esc_js( __( 'hour', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>');
                break;
            case 'mixed':
                if (durationHours >= 24 && dailyPrice > 0) {
                    var days = Math.max(1, Math.ceil(durationDays));
                    totalPrice = dailyPrice * days * quantity;
                    durationText = days + ' ' + (days === 1 ? '<?php echo esc_js( __( 'day', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>');
                } else if (hourlyPrice > 0) {
                    var hours = Math.max(1, Math.ceil(durationHours));
                    totalPrice = hourlyPrice * hours * quantity;
                    durationText = hours + ' ' + (hours === 1 ? '<?php echo esc_js( __( 'hour', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>');
                }
                break;
        }
        
        if (totalPrice > 0) {
            $('#rental-price-amount').text(currencySymbol + totalPrice.toFixed(2));
            $('#rental-duration-text').text(durationText);
            
            if (securityDeposit > 0) {
                $('#security-deposit-amount').text(currencySymbol + securityDeposit.toFixed(2));
                $('#security-deposit-display').show();
            }
            
            var total = totalPrice + securityDeposit;
            $('#total-amount').text(currencySymbol + total.toFixed(2));
            
            $('#rental-price-display').show();
            
            // Store cart data for form submission (fallback)
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
                }
            };
            
            $('#smart_rentals_booking_form').data('cart-item', cartData);
        }
    }

    // Calculate total price
    function calculateTotal() {
        var pickupDate = $('#pickup_date').val();
        var dropoffDate = $('#dropoff_date').val();
        var quantity = $('#quantity').val() || 1;
        
        if (!pickupDate || !dropoffDate) {
            $('#rental-price-display').hide();
            return;
        }
        
        // Check if AJAX is available
        if (typeof ajax_object === 'undefined') {
            console.error('AJAX object not available, using fallback calculation');
            // Fallback: simple client-side calculation
            calculateClientSidePrice(pickupDate, dropoffDate, quantity);
            return;
        }
        
        // Show loading
        $('.smart-rentals-loader-date').show();
        
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
                
                if (response.success && response.data) {
                    var data = response.data;
                    
                    $('#rental-price-amount').text(data.formatted_price);
                    $('#rental-duration-text').text(data.duration_text);
                    
                    if (data.security_deposit > 0) {
                        $('#security-deposit-amount').text(data.formatted_deposit);
                        $('#security-deposit-display').show();
                    } else {
                        $('#security-deposit-display').hide();
                    }
                    
                    var total = data.total_price + (data.security_deposit || 0);
                    $('#total-amount').text('<?php echo function_exists( 'get_woocommerce_currency_symbol' ) ? esc_js( get_woocommerce_currency_symbol() ) : '$'; ?>' + total.toFixed(2));
                    
                    $('#rental-price-display').show();
                    
                    // Store cart data for form submission
                    $('#smart_rentals_booking_form').data('cart-item', data.cart_data);
                    
                } else {
                    $('#rental-price-display').hide();
                    if (response.data && response.data.message) {
                        showMessage(response.data.message, 'error');
                    }
                }
            },
            error: function() {
                $('.smart-rentals-loader-date').hide();
                $('#rental-price-display').hide();
                showMessage(smartRentalsErrorMessages.error, 'error');
            }
        });
    }
    
    // Show message
    function showMessage(message, type) {
        $('.smart-rentals-message').remove();
        
        var messageClass = 'smart-rentals-message smart-rentals-' + type;
        var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';
        $('.smart-rentals-booking-form').prepend(messageHtml);
        
        if (type === 'success') {
            setTimeout(function() {
                $('.smart-rentals-message.smart-rentals-success').fadeOut();
            }, 5000);
        }
    }
    
    // Form submission
    $('#smart_rentals_booking_form').on('submit', function(e) {
        e.preventDefault();
        
        var pickupDate = $('#pickup_date').val();
        var dropoffDate = $('#dropoff_date').val();
        var quantity = $('#quantity').val() || 1;
        
        // Validation
        if (!pickupDate || !dropoffDate) {
            showMessage(smartRentalsErrorMessages.select_dates, 'error');
            return false;
        }
        
        var pickup = new Date(pickupDate);
        var dropoff = new Date(dropoffDate);
        
        if (pickup >= dropoff) {
            showMessage(smartRentalsErrorMessages.invalid_dates, 'error');
            return false;
        }
        
        // Get cart data
        var cartData = $(this).data('cart-item');
        if (!cartData) {
            showMessage('Please calculate the price first by selecting dates.', 'error');
            calculateTotal(); // Try to calculate now
            setTimeout(function() {
                cartData = $('#smart_rentals_booking_form').data('cart-item');
                if (!cartData) {
                    showMessage('Unable to calculate rental price. Please try refreshing the page.', 'error');
                }
            }, 1000);
            return false;
        }
        
        // Update cart data with current form values
        cartData.pickup_date = pickupDate;
        cartData.dropoff_date = dropoffDate;
        cartData.quantity = quantity;
        
        // Add rental data for cart
        cartData.rental_data = {
            pickup_date: pickupDate,
            dropoff_date: dropoffDate,
            pickup_time: '', // Will be extracted from datetime if needed
            dropoff_time: '', // Will be extracted from datetime if needed
            product_id: productId,
            rental_quantity: quantity,
        };
        
        // Show loading
        var $submitButton = $('.smart-rentals-book-now');
        var originalText = $submitButton.text();
        $submitButton.prop('disabled', true).text(typeof smartRentalsErrorMessages !== 'undefined' ? smartRentalsErrorMessages.loading : 'Loading...');
        
        // Check if AJAX is available
        if (typeof ajax_object === 'undefined') {
            console.error('AJAX not available, using standard form submission');
            // Fallback to standard form submission
            $(this).off('submit').submit();
            return;
        }
        
        // Submit via AJAX
        $.ajax({
            url: ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'smart_rentals_add_to_cart',
                security: ajax_object.security,
                product_id: productId,
                product_url: window.location.href,
                cart_item: cartData
            },
            success: function(response) {
                if (response.success) {
                    showMessage(smartRentalsErrorMessages.add_to_cart_success, 'success');
                    
                    // Redirect to cart after short delay
                    setTimeout(function() {
                        if (response.data && response.data.cart_url) {
                            window.location.href = response.data.cart_url;
                        } else {
                            window.location.reload();
                        }
                    }, 1500);
                } else {
                    showMessage(response.data ? response.data.message : smartRentalsErrorMessages.add_to_cart_failed, 'error');
                }
            },
            error: function() {
                showMessage(smartRentalsErrorMessages.error, 'error');
            },
            complete: function() {
                $submitButton.prop('disabled', false).text(originalText);
            }
        });
        
        return false;
    });
    
    // Bind events
    $('#pickup_date, #dropoff_date, #quantity').on('change', function() {
        setTimeout(calculateTotal, 100);
    });
    
    // Initialize
    initDatePickers();
    
    // Calculate initial price if dates are pre-filled
    if ($('#pickup_date').val() && $('#dropoff_date').val()) {
        setTimeout(calculateTotal, 500);
    }
    
    // Debug information
    console.log('Smart Rentals Booking Form Initialized');
    console.log('Product ID:', productId);
    console.log('Rental Type:', rentalType);
    
    if (typeof ajax_object !== 'undefined') {
        console.log('AJAX Object Available:', ajax_object);
    } else {
        console.error('AJAX object not found! This will cause booking issues.');
    }
    
    if (typeof smartRentalsErrorMessages !== 'undefined') {
        console.log('Error Messages Available:', Object.keys(smartRentalsErrorMessages));
    } else {
        console.error('Error messages not loaded!');
    }
});
</script>