jQuery(document).ready(function($) {
    'use strict';
    
    // Smart Rentals Frontend JavaScript
    var SmartRentals = {
        init: function() {
            this.bindEvents();
            this.initDatepickers();
            this.initCalendar();
        },
        
        bindEvents: function() {
            // Date change events
            $(document).on('change', '#pickup_date, #dropoff_date', this.onDateChange);
            
            // Time change events
            $(document).on('change', '#pickup_time, #dropoff_time', this.onTimeChange);
            
            // Add to cart validation
            $('form.cart').on('submit', this.validateForm);
            
            // Quantity change for rental products
            $(document).on('change', '.qty', this.onQuantityChange);
        },
        
        initDatepickers: function() {
            // Set minimum date to today
            var today = new Date().toISOString().split('T')[0];
            $('#pickup_date').attr('min', today);
            
            // Set minimum dropoff date when pickup changes
            $('#pickup_date').on('change', function() {
                var pickupDate = $(this).val();
                if (pickupDate) {
                    var minDropoff = new Date(pickupDate);
                    minDropoff.setDate(minDropoff.getDate() + 1);
                    $('#dropoff_date').attr('min', minDropoff.toISOString().split('T')[0]);
                }
            });
        },
        
        initCalendar: function() {
            // Initialize calendar if present
            if ($('#smart-rentals-calendar').length) {
                // Calendar initialization would go here
                // This could integrate with libraries like FullCalendar
                console.log('Calendar initialization placeholder');
            }
        },
        
        onDateChange: function() {
            SmartRentals.calculatePrice();
            SmartRentals.checkAvailability();
        },
        
        onTimeChange: function() {
            SmartRentals.calculatePrice();
        },
        
        onQuantityChange: function() {
            SmartRentals.calculatePrice();
            SmartRentals.checkAvailability();
        },
        
        calculatePrice: function() {
            var pickupDate = $('#pickup_date').val();
            var dropoffDate = $('#dropoff_date').val();
            var pickupTime = $('#pickup_time').val() || '00:00';
            var dropoffTime = $('#dropoff_time').val() || '23:59';
            var quantity = $('.qty').val() || 1;
            
            if (!pickupDate || !dropoffDate) {
                $('#rental-price-display').hide();
                return;
            }
            
            var pickup = new Date(pickupDate + ' ' + pickupTime);
            var dropoff = new Date(dropoffDate + ' ' + dropoffTime);
            
            if (pickup >= dropoff) {
                $('#rental-price-display').hide();
                return;
            }

            // Check if AJAX is available
            if (typeof smart_rentals_wc_ajax !== 'undefined') {
                // AJAX call to calculate price on server
                $.ajax({
                    url: smart_rentals_wc_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'smart_rentals_calculate_price',
                        nonce: smart_rentals_wc_ajax.nonce,
                        product_id: $('button[name="add-to-cart"]').val() || $('input[name="add-to-cart"]').val(),
                        pickup_date: pickupDate,
                        dropoff_date: dropoffDate,
                        pickup_time: pickupTime,
                        dropoff_time: dropoffTime,
                        quantity: quantity
                    },
                    success: function(response) {
                        if (response.success && response.data.price > 0) {
                            $('#rental-price-amount').html(response.data.formatted_price);
                            $('#rental-duration-text').text(response.data.duration_text);
                            $('#rental-price-display').show();
                        } else {
                            $('#rental-price-display').hide();
                        }
                    },
                    error: function() {
                        console.log('Error calculating rental price');
                        $('#rental-price-display').hide();
                    }
                });
            }
        },
        
        checkAvailability: function() {
            var pickupDate = $('#pickup_date').val();
            var dropoffDate = $('#dropoff_date').val();
            var quantity = $('.qty').val() || 1;
            
            if (!pickupDate || !dropoffDate) {
                return;
            }

            // Check if AJAX is available
            if (typeof smart_rentals_wc_ajax === 'undefined') {
                return;
            }
            
            // Show loading state
            $('.single_add_to_cart_button').prop('disabled', true).text(smart_rentals_wc_ajax.messages.loading);
            
            // AJAX call to check availability
            $.ajax({
                url: smart_rentals_wc_ajax.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_check_availability',
                    nonce: smart_rentals_wc_ajax.nonce,
                    product_id: $('button[name="add-to-cart"]').val() || $('input[name="add-to-cart"]').val(),
                    pickup_date: pickupDate,
                    dropoff_date: dropoffDate,
                    quantity: quantity
                },
                success: function(response) {
                    if (response.success) {
                        if (response.data.available) {
                            $('.single_add_to_cart_button').prop('disabled', false).text(response.data.button_text);
                            SmartRentals.showAvailabilityMessage(response.data.message, 'success');
                        } else {
                            $('.single_add_to_cart_button').prop('disabled', true).text(response.data.button_text);
                            SmartRentals.showAvailabilityMessage(response.data.message, 'error');
                        }
                    } else {
                        $('.single_add_to_cart_button').prop('disabled', true).text('Add to cart');
                        SmartRentals.showAvailabilityMessage(smart_rentals_wc_ajax.messages.error, 'error');
                    }
                },
                error: function() {
                    $('.single_add_to_cart_button').prop('disabled', true).text('Add to cart');
                    SmartRentals.showAvailabilityMessage(smart_rentals_wc_ajax.messages.error, 'error');
                }
            });
        },
        
        showAvailabilityMessage: function(message, type) {
            // Remove existing messages
            $('.smart-rentals-message').remove();
            
            if (message) {
                var messageClass = 'smart-rentals-message smart-rentals-' + type;
                var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';
                $('.smart-rentals-booking-form').append(messageHtml);
                
                // Auto-hide success messages after 5 seconds
                if (type === 'success') {
                    setTimeout(function() {
                        $('.smart-rentals-message.smart-rentals-success').fadeOut();
                    }, 5000);
                }
            }
        },
        
        validateForm: function(e) {
            var pickupDate = $('#pickup_date').val();
            var dropoffDate = $('#dropoff_date').val();
            
            if (!pickupDate || !dropoffDate) {
                e.preventDefault();
                var message = (typeof smart_rentals_wc_ajax !== 'undefined') ? 
                    smart_rentals_wc_ajax.messages.select_dates : 
                    'Please select pickup and drop-off dates.';
                alert(message);
                return false;
            }
            
            var pickup = new Date(pickupDate);
            var dropoff = new Date(dropoffDate);
            
            if (pickup >= dropoff) {
                e.preventDefault();
                var message = (typeof smart_rentals_wc_ajax !== 'undefined') ? 
                    smart_rentals_wc_ajax.messages.invalid_dates : 
                    'Drop-off date must be after pickup date.';
                alert(message);
                return false;
            }
            
            return true;
        }
    };
    
    // Initialize Smart Rentals
    SmartRentals.init();
    
    // Make SmartRentals globally available
    window.SmartRentals = SmartRentals;
});