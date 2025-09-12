jQuery(document).ready(function($) {
    'use strict';
    
    // Cart page rental functionality
    var SmartRentalsCart = {
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            // Handle rental item updates
            $(document).on('change', '.rental-quantity-input', this.updateRentalQuantity);
            
            // Handle rental date changes (if editable)
            $(document).on('change', '.rental-date-input', this.updateRentalDates);
        },
        
        updateRentalQuantity: function() {
            // Handle quantity updates for rental items
            var $input = $(this);
            var cartItemKey = $input.data('cart-item-key');
            var newQuantity = $input.val();
            
            // Add loading state
            $input.prop('disabled', true);
            
            // Update cart via AJAX
            $.ajax({
                url: wc_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_rental_cart_item',
                    cart_item_key: cartItemKey,
                    quantity: newQuantity,
                    security: wc_cart_params.update_cart_nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Reload cart fragments
                        $(document.body).trigger('wc_fragment_refresh');
                    } else {
                        alert(response.data.message || 'Error updating cart');
                    }
                },
                error: function() {
                    alert('Error updating cart');
                },
                complete: function() {
                    $input.prop('disabled', false);
                }
            });
        },
        
        updateRentalDates: function() {
            // Handle rental date updates
            var $input = $(this);
            var cartItemKey = $input.data('cart-item-key');
            var dateType = $input.data('date-type'); // pickup or dropoff
            var newDate = $input.val();
            
            // Add validation
            if (!newDate) {
                alert('Please select a valid date');
                return;
            }
            
            // Add loading state
            $input.prop('disabled', true);
            
            // Update cart via AJAX
            $.ajax({
                url: wc_cart_params.ajax_url,
                type: 'POST',
                data: {
                    action: 'update_rental_dates',
                    cart_item_key: cartItemKey,
                    date_type: dateType,
                    date_value: newDate,
                    security: wc_cart_params.update_cart_nonce
                },
                success: function(response) {
                    if (response.success) {
                        // Reload cart fragments
                        $(document.body).trigger('wc_fragment_refresh');
                    } else {
                        alert(response.data.message || 'Error updating dates');
                    }
                },
                error: function() {
                    alert('Error updating dates');
                },
                complete: function() {
                    $input.prop('disabled', false);
                }
            });
        }
    };
    
    // Initialize cart functionality
    SmartRentalsCart.init();
});