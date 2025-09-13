/**
 * Smart Rentals WC Order Edit Admin JavaScript
 */

jQuery(document).ready(function($) {
    'use strict';

    // Toggle edit fields
    $('.rental-edit-toggle').on('click', function() {
        var itemId = $(this).data('item-id');
        var $container = $(this).closest('.rental-edit-container');
        var $fields = $container.find('.rental-edit-fields');
        var $display = $container.find('.rental-display-info');

        if ($fields.is(':visible')) {
            // Hide edit fields, show display
            $fields.hide();
            $display.show();
            $(this).text(smartRentalsOrderEdit.strings.edit || 'Edit Rental Details');
        } else {
            // Show edit fields, hide display
            $fields.show();
            $display.hide();
            $(this).text('Cancel Edit');
        }
    });

    // Check availability
    $('.rental-check-availability').on('click', function() {
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
    $('.rental-date-input, .rental-quantity-input').on('change', function() {
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