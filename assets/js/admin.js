jQuery(document).ready(function($) {
    'use strict';
    
    // Handle rental checkbox toggle
    var rentalCheckbox = $('#smart_rentals_enable_rental');
    var rentalFields = $('#smart-rentals-fields');
    
    // Initial state
    if (rentalCheckbox.is(':checked')) {
        rentalFields.show();
    }
    
    // Toggle rental fields when checkbox changes
    rentalCheckbox.on('change', function() {
        if ($(this).is(':checked')) {
            rentalFields.slideDown();
        } else {
            rentalFields.slideUp();
        }
    });
    
    // Rental type specific field toggles
    var rentalType = $('#smart_rentals_rental_type');
    var dailyPriceField = $('#smart_rentals_daily_price').closest('.form-field');
    var hourlyPriceField = $('#smart_rentals_hourly_price').closest('.form-field');
    
    function togglePriceFields() {
        var selectedType = rentalType.val();
        
        // Hide all price fields first
        dailyPriceField.hide();
        hourlyPriceField.hide();
        
        // Show relevant fields based on rental type
        switch (selectedType) {
            case 'day':
            case 'hotel':
                dailyPriceField.show();
                break;
            case 'hour':
            case 'appointment':
                hourlyPriceField.show();
                break;
            case 'mixed':
                dailyPriceField.show();
                hourlyPriceField.show();
                break;
            case 'period_time':
            case 'transportation':
            case 'taxi':
                // These will have their own specific fields
                break;
        }
    }
    
    // Initial toggle
    togglePriceFields();
    
    // Toggle on change
    rentalType.on('change', togglePriceFields);
    
    // Add visual indicators
    $('.rental-enabled').css({
        'color': '#46b450',
        'font-weight': 'bold'
    });
    
    $('.rental-disabled').css({
        'color': '#dc3232'
    });
    
    // Enhanced form validation
    $('#post').on('submit', function(e) {
        if (rentalCheckbox.is(':checked')) {
            var rentalTypeVal = rentalType.val();
            var hasError = false;
            var errorMessage = '';
            
            if (!rentalTypeVal) {
                hasError = true;
                errorMessage += 'Please select a rental type.\n';
            }
            
            // Check for required price fields
            if (rentalTypeVal === 'day' || rentalTypeVal === 'mixed') {
                var dailyPrice = $('#smart_rentals_daily_price').val();
                if (!dailyPrice || parseFloat(dailyPrice) <= 0) {
                    hasError = true;
                    errorMessage += 'Please enter a valid daily price.\n';
                }
            }
            
            if (rentalTypeVal === 'hour' || rentalTypeVal === 'mixed') {
                var hourlyPrice = $('#smart_rentals_hourly_price').val();
                if (!hourlyPrice || parseFloat(hourlyPrice) <= 0) {
                    hasError = true;
                    errorMessage += 'Please enter a valid hourly price.\n';
                }
            }
            
            if (hasError) {
                e.preventDefault();
                alert('Rental Product Validation Errors:\n\n' + errorMessage);
                return false;
            }
        }
    });
    
    // Auto-calculate mixed pricing suggestions
    $('#smart_rentals_daily_price').on('blur', function() {
        var dailyPrice = parseFloat($(this).val());
        var hourlyPrice = parseFloat($('#smart_rentals_hourly_price').val());
        
        if (dailyPrice > 0 && !hourlyPrice && rentalType.val() === 'mixed') {
            var suggestedHourly = (dailyPrice / 8).toFixed(2); // Assume 8 hours per day
            $('#smart_rentals_hourly_price').val(suggestedHourly);
            
            // Show suggestion message
            if (!$('.pricing-suggestion').length) {
                $('#smart_rentals_hourly_price').after(
                    '<p class="pricing-suggestion" style="color: #666; font-style: italic; margin-top: 5px;">' +
                    'Auto-calculated based on 8 hours per day. You can adjust this value.' +
                    '</p>'
                );
                
                setTimeout(function() {
                    $('.pricing-suggestion').fadeOut(function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        }
    });
    
    // Stock validation
    $('#smart_rentals_rental_stock').on('change', function() {
        var stock = parseInt($(this).val());
        if (stock < 1) {
            $(this).val(1);
            alert('Rental stock must be at least 1.');
        }
    });
    
    // Rental period validation
    $('#smart_rentals_max_rental_period').on('change', function() {
        var maxPeriod = parseInt($(this).val());
        var minPeriod = parseInt($('#smart_rentals_min_rental_period').val());
        
        if (maxPeriod > 0 && minPeriod > 0 && maxPeriod < minPeriod) {
            alert('Maximum rental period cannot be less than minimum rental period.');
            $(this).val(minPeriod);
        }
    });
    
    $('#smart_rentals_min_rental_period').on('change', function() {
        var minPeriod = parseInt($(this).val());
        var maxPeriod = parseInt($('#smart_rentals_max_rental_period').val());
        
        if (maxPeriod > 0 && minPeriod > 0 && minPeriod > maxPeriod) {
            alert('Minimum rental period cannot be greater than maximum rental period.');
            $(this).val(maxPeriod);
        }
    });
});