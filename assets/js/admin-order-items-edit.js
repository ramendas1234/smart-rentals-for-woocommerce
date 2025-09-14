/**
 * Smart Rentals WC Order Items Edit JavaScript
 * Inline editing for rental order items
 */

jQuery(document).ready(function($) {
    'use strict';

    console.log('=== Smart Rentals Order Items Edit Script Loading ===');
    console.log('Script loaded at:', new Date().toISOString());
    console.log('jQuery version:', $.fn.jquery);
    console.log('Order ID:', smartRentalsOrderItems ? smartRentalsOrderItems.order_id : 'undefined');
    console.log('AJAX URL:', smartRentalsOrderItems ? smartRentalsOrderItems.ajax_url : 'undefined');
    console.log('Nonce:', smartRentalsOrderItems ? smartRentalsOrderItems.nonce : 'undefined');
    
    // Show visible alert for debugging
    if (typeof smartRentalsOrderItems !== 'undefined') {
        console.log('Smart Rentals Order Items object found!');
        // Uncomment the line below to show a visible alert
        // alert('Smart Rentals Order Items Edit script loaded successfully!');
    } else {
        console.error('Smart Rentals Order Items object NOT found!');
        // Uncomment the line below to show a visible alert
        // alert('ERROR: Smart Rentals Order Items object not found!');
    }
    
    // Check if required elements exist
    console.log('Rental edit toggles found:', $('.rental-edit-toggle').length);
    console.log('Rental item containers found:', $('.rental-item-container').length);
    console.log('Order items table found:', $('.woocommerce_order_items').length);
    
    // Log all rental-related elements
    console.log('All rental elements:', {
        editToggles: $('.rental-edit-toggle').length,
        itemContainers: $('.rental-item-container').length,
        displayModes: $('.rental-display-mode').length,
        editModes: $('.rental-edit-mode').length,
        checkButtons: $('.rental-check-availability').length,
        saveButtons: $('.rental-save').length,
        cancelButtons: $('.rental-cancel').length
    });

    // Initialize the inline editing system
    initInlineEditing();

    function initInlineEditing() {
        console.log('Initializing inline editing system');
        
        // Bind event handlers
        bindEventHandlers();
        
        // Add visual enhancements
        addVisualEnhancements();
    }

    function bindEventHandlers() {
        console.log('Binding event handlers');
        
        // Edit toggle button
        $(document).on('click', '.rental-edit-toggle', function(e) {
            console.log('EDIT TOGGLE CLICKED');
            
            e.preventDefault();
            e.stopPropagation();
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            var $container = $button.closest('.rental-item-container');
            var $displayMode = $container.find('.rental-display-mode');
            var $editMode = $container.find('.rental-edit-mode');
            
            console.log('Item ID:', itemId);
            console.log('Container found:', $container.length);
            console.log('Display mode found:', $displayMode.length);
            console.log('Edit mode found:', $editMode.length);
            
            if ($container.length === 0) {
                console.error('Container not found!');
                return;
            }
            
            if ($editMode.length === 0) {
                console.error('Edit mode not found!');
                return;
            }
            
            // Show edit mode
            $displayMode.hide();
            $editMode.show();
            
            // Update button visibility
            $button.hide();
            $container.find('.rental-check-availability').show();
            $container.find('.rental-save').show();
            $container.find('.rental-cancel').show();
            
            console.log('Switched to edit mode successfully');
        });

        // Cancel edit button
        $(document).on('click', '.rental-cancel', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Cancel edit clicked');
            
            var $button = $(this);
            var $container = $button.closest('.rental-item-container');
            var $displayMode = $container.find('.rental-display-mode');
            var $editMode = $container.find('.rental-edit-mode');
            
            // Switch back to display mode
            $editMode.hide();
            $displayMode.show();
            
            // Update button visibility
            $container.find('.rental-edit-toggle').show();
            $container.find('.rental-check-availability').hide();
            $container.find('.rental-save').hide();
            $container.find('.rental-cancel').hide();
            
            // Clear feedback
            $container.find('.rental-feedback').html('');
            
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
            var $container = $button.closest('.rental-item-container');
            var $feedback = $container.find('.rental-feedback');
            
            // Get form data
            var pickupDate = $container.find('.rental-pickup-input').val();
            var dropoffDate = $container.find('.rental-dropoff-input').val();
            var quantity = $container.find('.rental-quantity-input').val() || 1;
            
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
            $button.html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Checking...');
            showFeedback($feedback, 'info', smartRentalsOrderItems.strings.checking);
            
            // AJAX call
            $.ajax({
                url: smartRentalsOrderItems.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_check_rental_availability',
                    nonce: smartRentalsOrderItems.nonce,
                    product_id: productId,
                    pickup_date: pickupDate,
                    dropoff_date: dropoffDate,
                    quantity: quantity,
                    order_id: smartRentalsOrderItems.order_id
                },
                success: function(response) {
                    console.log('Check availability response:', response);
                    $button.prop('disabled', false);
                    $button.html('<span class="dashicons dashicons-search"></span> Check');
                    
                    if (response.success) {
                        var message = '<strong>✓ ' + smartRentalsOrderItems.strings.available + '</strong><br>';
                        message += 'Available quantity: ' + response.data.available_quantity + '<br>';
                        message += 'New price: ' + response.data.formatted_price + '<br>';
                        message += 'Duration: ' + response.data.duration_text;
                        showFeedback($feedback, 'success', message);
                    } else {
                        showFeedback($feedback, 'error', '<strong>✗ Not Available</strong><br>' + response.data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Check availability AJAX error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    $button.prop('disabled', false);
                    $button.html('<span class="dashicons dashicons-search"></span> Check');
                    showFeedback($feedback, 'error', 'AJAX Error: ' + status + ' - ' + error);
                }
            });
        });

        // Save button
        $(document).on('click', '.rental-save', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Save clicked');
            
            var $button = $(this);
            var itemId = $button.data('item-id');
            var $container = $button.closest('.rental-item-container');
            var $feedback = $container.find('.rental-feedback');
            
            // Get form data
            var formData = {
                pickup_date: $container.find('.rental-pickup-input').val(),
                dropoff_date: $container.find('.rental-dropoff-input').val(),
                quantity: $container.find('.rental-quantity-input').val() || 1,
                security_deposit: $container.find('.rental-deposit-input').val() || 0
            };
            
            console.log('Saving data:', formData);
            
            // Show loading
            $button.prop('disabled', true);
            $button.html('<span class="dashicons dashicons-update" style="animation: spin 1s linear infinite;"></span> Saving...');
            showFeedback($feedback, 'info', smartRentalsOrderItems.strings.saving);
            
            // AJAX call
            $.ajax({
                url: smartRentalsOrderItems.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_update_order_item_rental',
                    nonce: smartRentalsOrderItems.nonce,
                    order_id: smartRentalsOrderItems.order_id,
                    item_id: itemId,
                    pickup_date: formData.pickup_date,
                    dropoff_date: formData.dropoff_date,
                    quantity: formData.quantity,
                    security_deposit: formData.security_deposit
                },
                success: function(response) {
                    console.log('Save rental response:', response);
                    $button.prop('disabled', false);
                    $button.html('<span class="dashicons dashicons-yes"></span> Save');
                    
                    if (response.success) {
                        showFeedback($feedback, 'success', '<strong>✓ ' + smartRentalsOrderItems.strings.saved + '</strong><br>New price: ' + response.data.formatted_price);
                        
                        // Update display values
                        updateDisplayValues($container, formData);
                        
                        // Switch back to display mode
                        setTimeout(function() {
                            $container.find('.rental-edit-mode').hide();
                            $container.find('.rental-display-mode').show();
                            
                            // Update button visibility
                            $container.find('.rental-edit-toggle').show();
                            $container.find('.rental-check-availability').hide();
                            $container.find('.rental-save').hide();
                            $container.find('.rental-cancel').hide();
                        }, 1500);
                        
                        // Refresh page after delay to update order totals
                        setTimeout(function() {
                            location.reload();
                        }, 3000);
                    } else {
                        showFeedback($feedback, 'error', '<strong>✗ Save Failed</strong><br>' + response.data.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Save rental AJAX error:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText,
                        statusCode: xhr.status
                    });
                    $button.prop('disabled', false);
                    $button.html('<span class="dashicons dashicons-yes"></span> Save');
                    showFeedback($feedback, 'error', 'AJAX Error: ' + status + ' - ' + error);
                }
            });
        });

        // Auto-check availability when dates change
        var checkTimeout;
        $(document).on('change', '.rental-pickup-input, .rental-dropoff-input, .rental-quantity-input', function() {
            var $container = $(this).closest('.rental-item-container');
            var $checkButton = $container.find('.rental-check-availability');
            var $feedback = $container.find('.rental-feedback');
            
            // Clear previous timeout
            clearTimeout(checkTimeout);
            
            // Clear previous feedback
            $feedback.html('');
            
            // Auto-trigger availability check after delay
            checkTimeout = setTimeout(function() {
                if ($checkButton.length && $checkButton.is(':visible')) {
                    $checkButton.trigger('click');
                }
            }, 1000);
        });
    }

    function showFeedback($container, type, message) {
        var cssClass = 'rental-feedback-' + type;
        var html = '<div class="' + cssClass + '">' + message + '</div>';
        $container.html(html);
    }

    function updateDisplayValues($container, formData) {
        // Update pickup date
        if (formData.pickup_date) {
            var pickupDate = new Date(formData.pickup_date);
            $container.find('.pickup-display').text(pickupDate.toLocaleString());
        }
        
        // Update dropoff date
        if (formData.dropoff_date) {
            var dropoffDate = new Date(formData.dropoff_date);
            $container.find('.dropoff-display').text(dropoffDate.toLocaleString());
        }
        
        // Update quantity
        $container.find('.quantity-display').text(formData.quantity);
        
        // Update security deposit
        var depositText = formData.security_deposit > 0 ? '$' + parseFloat(formData.security_deposit).toFixed(2) : 'None';
        $container.find('.deposit-display').text(depositText);
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
        $('.rental-item-container').hover(
            function() {
                $(this).css('background-color', '#f8f9fa');
            },
            function() {
                $(this).css('background-color', 'transparent');
            }
        );
    }

    console.log('Smart Rentals Order Items Edit initialization complete');
});