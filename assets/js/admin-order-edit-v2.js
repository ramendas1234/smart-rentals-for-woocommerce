/**
 * Smart Rentals WC Order Edit V2 JavaScript
 * Enhanced order edit functionality with reliable event handling
 */

jQuery(document).ready(function($) {
    'use strict';

    console.log('Smart Rentals Order Edit V2 script loaded');
    console.log('Order ID:', smartRentalsOrderEditV2.order_id);
    console.log('AJAX URL:', smartRentalsOrderEditV2.ajax_url);

    // Initialize the order edit interface
    initOrderEditInterface();

    function initOrderEditInterface() {
        console.log('Initializing order edit interface');
        
        // Bind event handlers
        bindEventHandlers();
        
        // Add visual enhancements
        addVisualEnhancements();
    }

    function bindEventHandlers() {
        console.log('Binding event handlers');
        
        // Edit toggle button
        $(document).on('click', '.rental-edit-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Edit toggle clicked');
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            var $card = $button.closest('.rental-item-card');
            var $displayMode = $card.find('.rental-display-mode');
            var $editMode = $card.find('.rental-edit-mode');
            
            console.log('Item ID:', itemId);
            console.log('Card found:', $card.length);
            console.log('Display mode found:', $displayMode.length);
            console.log('Edit mode found:', $editMode.length);
            
            if ($editMode.is(':visible')) {
                // Switch to display mode
                $editMode.slideUp(300);
                $displayMode.slideDown(300);
                $button.html('<span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 5px;"></span>' + smartRentalsOrderEditV2.strings.edit);
                console.log('Switched to display mode');
            } else {
                // Switch to edit mode
                $displayMode.slideUp(300);
                $editMode.slideDown(300);
                $button.html('<span class="dashicons dashicons-no" style="vertical-align: middle; margin-right: 5px;"></span>' + smartRentalsOrderEditV2.strings.cancel);
                console.log('Switched to edit mode');
            }
        });

        // Cancel edit button
        $(document).on('click', '.rental-cancel-edit', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Cancel edit clicked');
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            var $card = $button.closest('.rental-item-card');
            var $displayMode = $card.find('.rental-display-mode');
            var $editMode = $card.find('.rental-edit-mode');
            var $editToggle = $card.find('.rental-edit-toggle');
            
            // Switch to display mode
            $editMode.slideUp(300);
            $displayMode.slideDown(300);
            $editToggle.html('<span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 5px;"></span>' + smartRentalsOrderEditV2.strings.edit);
            
            // Clear any feedback
            $card.find('.rental-feedback').html('');
            
            console.log('Cancelled edit mode');
        });

        // Check availability button
        $(document).on('click', '.rental-check-availability', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Check availability clicked');
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            var productId = $button.data('product-id');
            var $card = $button.closest('.rental-item-card');
            var $feedback = $card.find('.rental-feedback');
            var $form = $card.find('.rental-edit-form');
            
            // Get form data
            var pickupDate = $form.find('input[name="pickup_date"]').val();
            var dropoffDate = $form.find('input[name="dropoff_date"]').val();
            var quantity = $form.find('input[name="quantity"]').val() || 1;
            
            console.log('Form data:', {itemId, productId, pickupDate, dropoffDate, quantity});
            
            if (!pickupDate || !dropoffDate) {
                showFeedback($feedback, 'error', 'Please select both pickup and dropoff dates.');
                return;
            }
            
            // Validate date range
            var pickup = new Date(pickupDate);
            var dropoff = new Date(dropoffDate);
            if (pickup >= dropoff) {
                showFeedback($feedback, 'error', 'Dropoff date must be after pickup date.');
                return;
            }
            
            // Show loading
            $button.prop('disabled', true);
            $button.html('<span class="dashicons dashicons-update" style="vertical-align: middle; margin-right: 5px; animation: spin 1s linear infinite;"></span>Checking...');
            showFeedback($feedback, 'info', smartRentalsOrderEditV2.strings.checking);
            
            // AJAX call
            $.ajax({
                url: smartRentalsOrderEditV2.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_check_rental_availability',
                    nonce: smartRentalsOrderEditV2.nonce,
                    product_id: productId,
                    pickup_date: pickupDate,
                    dropoff_date: dropoffDate,
                    quantity: quantity,
                    order_id: smartRentalsOrderEditV2.order_id
                },
                success: function(response) {
                    $button.prop('disabled', false);
                    $button.html('<span class="dashicons dashicons-search" style="vertical-align: middle; margin-right: 5px;"></span>Check Availability & Price');
                    
                    if (response.success) {
                        var message = '<strong>✓ ' + smartRentalsOrderEditV2.strings.available + '</strong><br>';
                        message += 'Available quantity: ' + response.data.available_quantity + '<br>';
                        message += 'New price: ' + response.data.formatted_price + '<br>';
                        message += 'Duration: ' + response.data.duration_text;
                        showFeedback($feedback, 'success', message);
                    } else {
                        showFeedback($feedback, 'error', '<strong>✗ Not Available</strong><br>' + response.data.message);
                    }
                },
                error: function() {
                    $button.prop('disabled', false);
                    $button.html('<span class="dashicons dashicons-search" style="vertical-align: middle; margin-right: 5px;"></span>Check Availability & Price');
                    showFeedback($feedback, 'error', smartRentalsOrderEditV2.strings.error);
                }
            });
        });

        // Save item button
        $(document).on('click', '.rental-save-item', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Save item clicked');
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            var $card = $button.closest('.rental-item-card');
            var $feedback = $card.find('.rental-feedback');
            var $form = $card.find('.rental-edit-form');
            
            // Get form data
            var formData = {
                pickup_date: $form.find('input[name="pickup_date"]').val(),
                dropoff_date: $form.find('input[name="dropoff_date"]').val(),
                quantity: $form.find('input[name="quantity"]').val() || 1,
                security_deposit: $form.find('input[name="security_deposit"]').val() || 0
            };
            
            console.log('Saving data:', formData);
            
            // Show loading
            $button.prop('disabled', true);
            $button.html('<span class="dashicons dashicons-update" style="vertical-align: middle; margin-right: 5px; animation: spin 1s linear infinite;"></span>Saving...');
            showFeedback($feedback, 'info', smartRentalsOrderEditV2.strings.saving);
            
            // AJAX call
            $.ajax({
                url: smartRentalsOrderEditV2.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_save_order_rental_data',
                    nonce: smartRentalsOrderEditV2.nonce,
                    order_id: smartRentalsOrderEditV2.order_id,
                    item_id: itemId,
                    pickup_date: formData.pickup_date,
                    dropoff_date: formData.dropoff_date,
                    quantity: formData.quantity,
                    security_deposit: formData.security_deposit
                },
                success: function(response) {
                    $button.prop('disabled', false);
                    $button.html('<span class="dashicons dashicons-yes" style="vertical-align: middle; margin-right: 5px;"></span>Save Changes');
                    
                    if (response.success) {
                        showFeedback($feedback, 'success', '<strong>✓ ' + smartRentalsOrderEditV2.strings.saved + '</strong><br>New price: ' + response.data.formatted_price);
                        
                        // Update display values
                        updateDisplayValues($card, formData, response.data.formatted_price);
                        
                        // Switch back to display mode
                        setTimeout(function() {
                            $card.find('.rental-edit-mode').slideUp(300);
                            $card.find('.rental-display-mode').slideDown(300);
                            $card.find('.rental-edit-toggle').html('<span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 5px;"></span>' + smartRentalsOrderEditV2.strings.edit);
                        }, 1500);
                        
                        // Refresh page after delay to update order totals
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    } else {
                        showFeedback($feedback, 'error', '<strong>✗ Save Failed</strong><br>' + response.data.message);
                    }
                },
                error: function() {
                    $button.prop('disabled', false);
                    $button.html('<span class="dashicons dashicons-yes" style="vertical-align: middle; margin-right: 5px;"></span>Save Changes');
                    showFeedback($feedback, 'error', smartRentalsOrderEditV2.strings.error);
                }
            });
        });

        // Auto-check availability when dates change
        var checkTimeout;
        $(document).on('change', '.rental-date-input, .rental-quantity-input', function() {
            var $card = $(this).closest('.rental-item-card');
            var $checkButton = $card.find('.rental-check-availability');
            var $feedback = $card.find('.rental-feedback');
            
            // Clear previous timeout
            clearTimeout(checkTimeout);
            
            // Clear previous feedback
            $feedback.html('');
            
            // Auto-trigger availability check after delay
            checkTimeout = setTimeout(function() {
                if ($checkButton.length) {
                    $checkButton.trigger('click');
                }
            }, 1000);
        });
    }

    function showFeedback($container, type, message) {
        var cssClass = 'notice notice-' + type + ' inline';
        var html = '<div class="' + cssClass + '"><p>' + message + '</p></div>';
        $container.html(html);
    }

    function updateDisplayValues($card, formData, newPrice) {
        // Update pickup date
        if (formData.pickup_date) {
            var pickupDate = new Date(formData.pickup_date);
            $card.find('.rental-pickup-display').text(pickupDate.toLocaleString());
        }
        
        // Update dropoff date
        if (formData.dropoff_date) {
            var dropoffDate = new Date(formData.dropoff_date);
            $card.find('.rental-dropoff-display').text(dropoffDate.toLocaleString());
        }
        
        // Update quantity
        $card.find('.rental-quantity-display').text(formData.quantity);
        
        // Update security deposit
        var depositText = formData.security_deposit > 0 ? '$' + parseFloat(formData.security_deposit).toFixed(2) : 'None';
        $card.find('.rental-deposit-display').text(depositText);
        
        // Update total price
        $card.find('.rental-total-display').text(newPrice);
    }

    function addVisualEnhancements() {
        // Add CSS for spin animation
        if (!$('#smart-rentals-spin-css').length) {
            $('<style id="smart-rentals-spin-css">')
                .prop('type', 'text/css')
                .html('@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }')
                .appendTo('head');
        }
        
        // Add hover effects
        $('.rental-item-card').hover(
            function() {
                $(this).css('box-shadow', '0 4px 8px rgba(0,0,0,0.15)');
            },
            function() {
                $(this).css('box-shadow', '0 2px 4px rgba(0,0,0,0.1)');
            }
        );
    }

    console.log('Smart Rentals Order Edit V2 initialization complete');
});