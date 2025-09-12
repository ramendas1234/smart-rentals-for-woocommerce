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
            // Date change events for non-form date inputs
            $(document).on('change', 'input[type="date"]:not(.pickup-date):not(.dropoff-date)', this.onDateChange);
            
            // Quantity change for rental products
            $(document).on('change', '.qty', this.onQuantityChange);
            
            // Standard WooCommerce form validation (fallback)
            $('form.cart').on('submit', this.validateStandardForm);
        },
        
        initDatepickers: function() {
            // This is handled by the booking form template now
        },
        
        initCalendar: function() {
            // Initialize calendar if present
            if ($('.smart-rentals-calendar').length) {
                console.log('Calendar initialization');
            }
        },
        
        onDateChange: function() {
            // This is for non-booking form date inputs
        },
        
        onQuantityChange: function() {
            // This is for non-booking form quantity inputs
        },
        
        validateStandardForm: function(e) {
            // Only validate if this is NOT our custom booking form
            if ($(this).hasClass('smart-rentals-form')) {
                return true; // Let our custom form handle validation
            }
            
            // Check if this is a rental product page
            if (typeof window.smartRentalsProductData !== 'undefined') {
                var pickupDate = $('input[name="pickup_date"]').val();
                var dropoffDate = $('input[name="dropoff_date"]').val();
                
                if (!pickupDate || !dropoffDate) {
                    e.preventDefault();
                    alert(typeof smartRentalsErrorMessages !== 'undefined' ? 
                        smartRentalsErrorMessages.select_dates : 
                        'Please select pickup and drop-off dates.');
                    return false;
                }
                
                var pickup = new Date(pickupDate);
                var dropoff = new Date(dropoffDate);
                
                if (pickup >= dropoff) {
                    e.preventDefault();
                    alert(typeof smartRentalsErrorMessages !== 'undefined' ? 
                        smartRentalsErrorMessages.invalid_dates : 
                        'Drop-off date must be after pickup date.');
                    return false;
                }
            }
            
            return true;
        },
        
        // Utility functions
        showMessage: function(message, type) {
            $('.smart-rentals-message').remove();
            
            if (message) {
                var messageClass = 'smart-rentals-message smart-rentals-' + type;
                var messageHtml = '<div class="' + messageClass + '">' + message + '</div>';
                
                if ($('.smart-rentals-booking-form').length) {
                    $('.smart-rentals-booking-form').prepend(messageHtml);
                } else {
                    $('body').prepend(messageHtml);
                }
                
                // Auto-hide success messages
                if (type === 'success') {
                    setTimeout(function() {
                        $('.smart-rentals-message.smart-rentals-success').fadeOut();
                    }, 5000);
                }
            }
        },
        
        calculatePrice: function(productId, pickupDate, dropoffDate, quantity) {
            if (!productId || !pickupDate || !dropoffDate) {
                return;
            }
            
            return $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_calculate_total',
                    security: ajax_object.security,
                    product_id: productId,
                    pickup_date: pickupDate,
                    dropoff_date: dropoffDate,
                    quantity: quantity || 1
                }
            });
        },
        
        checkAvailability: function(productId, pickupDate, dropoffDate, quantity) {
            if (!productId || !pickupDate || !dropoffDate) {
                return;
            }
            
            return $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_check_availability',
                    security: ajax_object.security,
                    product_id: productId,
                    pickup_date: pickupDate,
                    dropoff_date: dropoffDate,
                    quantity: quantity || 1
                }
            });
        },
        
        addToCart: function(productId, cartData) {
            if (!productId || !cartData) {
                return;
            }
            
            return $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_add_to_cart',
                    security: ajax_object.security,
                    product_id: productId,
                    product_url: window.location.href,
                    cart_item: cartData
                }
            });
        }
    };
    
    // Initialize Smart Rentals
    SmartRentals.init();
    
    // Make SmartRentals globally available
    window.SmartRentals = SmartRentals;
    
    // Debug information
    if (typeof window.smartRentalsProductData !== 'undefined') {
        console.log('Smart Rentals Product Data:', window.smartRentalsProductData);
    }
    
    if (typeof ajax_object !== 'undefined') {
        console.log('Smart Rentals AJAX Object:', ajax_object);
    }
});