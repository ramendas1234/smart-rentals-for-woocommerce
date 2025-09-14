/**
 * Smart Rentals WC Order Edit Admin JavaScript
 */

jQuery(document).ready(function($) {
    'use strict';

    // Debug log
    console.log('Smart Rentals Order Edit script loaded');
    console.log('Found rental edit toggles:', $('.rental-edit-toggle').length);
    console.log('Smart Rentals Order Edit object:', typeof smartRentalsOrderEdit !== 'undefined' ? smartRentalsOrderEdit : 'undefined');
    
    // Additional debugging
    console.log('All rental edit containers:', $('.rental-edit-container').length);
    console.log('All rental display info:', $('.rental-display-info').length);
    console.log('All rental edit fields:', $('.rental-edit-fields').length);

    // Toggle edit fields
    $(document).on('click', '.rental-edit-toggle', function(e) {
        e.preventDefault();
        console.log('Edit button clicked');
        
        var itemId = $(this).data('item-id');
        var $container = $(this).closest('.rental-edit-container');
        var $fields = $container.find('.rental-edit-fields');
        var $display = $container.find('.rental-display-info');

        console.log('Container found:', $container.length);
        console.log('Fields found:', $fields.length);
        console.log('Display found:', $display.length);

        if ($fields.is(':visible')) {
            // Hide edit fields, show display
            $fields.slideUp(200);
            $display.slideDown(200);
            $(this).html('<span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 3px;"></span>Edit Details');
            console.log('Switched to display mode');
        } else {
            // Show edit fields, hide display
            $display.slideUp(200);
            $fields.slideDown(200);
            $(this).html('<span class="dashicons dashicons-no" style="vertical-align: middle; margin-right: 3px;"></span>Cancel');
            console.log('Switched to edit mode');
        }
    });

    // Check availability
    $(document).on('click', '.rental-check-availability', function(e) {
        e.preventDefault();
        console.log('Check availability clicked');
        var itemId = $(this).data('item-id');
        var productId = $(this).data('product-id');
        var $container = $(this).closest('.rental-edit-container');
        var $result = $container.find('.rental-availability-result');
        
        var pickupDate = $container.find('input[name="rental_pickup_date[' + itemId + ']"]').val();
        var dropoffDate = $container.find('input[name="rental_dropoff_date[' + itemId + ']"]').val();
        var quantity = $container.find('input[name="rental_quantity[' + itemId + ']"]').val() || 1;

        if (!pickupDate || !dropoffDate) {
            $result.html('<div class="notice notice-error inline"><p>Please select both pickup and dropoff dates.</p></div>');
            return;
        }

        // Show loading
        $result.html('<div class="notice notice-info inline"><p>' + (smartRentalsOrderEdit.strings.checking || 'Checking availability...') + '</p></div>');

        // AJAX call to check availability
        $.ajax({
            url: smartRentalsOrderEdit.ajax_url,
            type: 'POST',
            data: {
                action: 'smart_rentals_check_order_edit_availability',
                nonce: smartRentalsOrderEdit.nonce,
                product_id: productId,
                pickup_date: pickupDate,
                dropoff_date: dropoffDate,
                quantity: quantity,
                exclude_order_id: $('#post_ID').val() || 0
            },
            success: function(response) {
                if (response.success) {
                    var message = '<div class="notice notice-success inline"><p>';
                    message += '<strong>' + (smartRentalsOrderEdit.strings.available || 'Available') + '</strong><br>';
                    message += 'Available quantity: ' + response.data.available_quantity + '<br>';
                    message += 'New price: ' + response.data.formatted_price + '<br>';
                    message += 'Duration: ' + response.data.duration_text;
                    message += '</p></div>';
                    $result.html(message);
                } else {
                    $result.html('<div class="notice notice-error inline"><p><strong>' + (smartRentalsOrderEdit.strings.not_available || 'Not available') + '</strong><br>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $result.html('<div class="notice notice-error inline"><p>' + (smartRentalsOrderEdit.strings.error || 'Error checking availability') + '</p></div>');
            }
        });
    });

    // Save rental item button
    $(document).on('click', '.rental-save-item', function(e) {
        e.preventDefault();
        console.log('Save rental item clicked');
        
        var $button = $(this);
        var itemId = $button.data('item-id');
        var $container = $button.closest('.rental-edit-container');
        var $result = $container.find('.rental-availability-result');
        
        var pickupDate = $container.find('input[name="rental_pickup_date[' + itemId + ']"]').val();
        var dropoffDate = $container.find('input[name="rental_dropoff_date[' + itemId + ']"]').val();
        var quantity = $container.find('input[name="rental_quantity[' + itemId + ']"]').val() || 1;
        var securityDeposit = $container.find('input[name="rental_security_deposit[' + itemId + ']"]').val() || 0;

        // Show loading
        $result.html('<div class="notice notice-info inline"><p>Saving rental details...</p></div>');
        $button.prop('disabled', true);

        // AJAX call
        $.ajax({
            url: smartRentalsOrderEdit.ajax_url,
            type: 'POST',
            data: {
                action: 'smart_rentals_save_order_item_rental_data',
                nonce: smartRentalsOrderEdit.nonce,
                item_id: itemId,
                pickup_date: pickupDate,
                dropoff_date: dropoffDate,
                quantity: quantity,
                security_deposit: securityDeposit
            },
            success: function(response) {
                $button.prop('disabled', false);
                
                if (response.success) {
                    var message = '<div class="notice notice-success inline"><p>';
                    message += '<strong>✓ Saved Successfully</strong><br>';
                    message += 'New price: ' + response.data.formatted_price;
                    message += '</p></div>';
                    $result.html(message);
                    
                    // Update display values
                    $container.find('.pickup-date-display').text(pickupDate ? new Date(pickupDate).toLocaleString() : 'Not set');
                    $container.find('.dropoff-date-display').text(dropoffDate ? new Date(dropoffDate).toLocaleString() : 'Not set');
                    $container.find('.rental-quantity-display').text(quantity);
                    $container.find('.security-deposit-display').text(securityDeposit > 0 ? '$' + parseFloat(securityDeposit).toFixed(2) : 'None');
                    $container.find('.rental-total-display').text(response.data.formatted_price);
                    
                    // Switch back to display mode
                    $container.find('.rental-edit-fields').slideUp(200);
                    $container.find('.rental-display-info').slideDown(200);
                    $container.find('.rental-edit-toggle').html('<span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 3px;"></span>Edit Details');
                    
                    // Refresh the page after a short delay to update order totals
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    $result.html('<div class="notice notice-error inline"><p><strong>✗ Save Failed</strong><br>' + response.data.message + '</p></div>');
                }
            },
            error: function() {
                $button.prop('disabled', false);
                $result.html('<div class="notice notice-error inline"><p>Error saving rental details</p></div>');
            }
        });
    });

    // Auto-check availability when dates change
    $(document).on('change', '.rental-date-input, .rental-quantity-input', function() {
        var $container = $(this).closest('.rental-edit-container');
        var $checkButton = $container.find('.rental-check-availability');
        var $result = $container.find('.rental-availability-result');
        
        // Clear previous result
        $result.html('');
        
        // Auto-trigger availability check after a short delay
        setTimeout(function() {
            $checkButton.trigger('click');
        }, 500);
    });
});