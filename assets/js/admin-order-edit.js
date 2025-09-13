/**
 * Smart Rentals WC Order Edit Admin JavaScript
 */

jQuery(document).ready(function($) {
    'use strict';

    // Debug log
    console.log('Smart Rentals Order Edit script loaded');
    console.log('Found rental edit toggles:', $('.rental-edit-toggle').length);

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
            $fields.hide();
            $display.show();
            $(this).text(smartRentalsOrderEdit.strings.edit || 'Edit Rental Details');
            console.log('Switched to display mode');
        } else {
            // Show edit fields, hide display
            $fields.show();
            $display.hide();
            $(this).text(smartRentalsOrderEdit.strings.cancel || 'Cancel Edit');
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