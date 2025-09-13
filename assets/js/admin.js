jQuery(document).ready(function($) {
    'use strict';
    
    // Handle rental checkbox toggle for new tab system
    var rentalCheckbox = $('input[name="smart_rentals_enable_rental"]');
    
    // Function to toggle rental tab visibility
    function toggleRentalTab() {
        // Target all possible rental tab selectors
        var rentalTab = $('.wc-tabs li').filter(function() {
            return $(this).find('a[href="#smart_rentals_product_data"]').length > 0 ||
                   $(this).hasClass('smart_rentals_options') ||
                   $(this).hasClass('smart_rentals_tab') ||
                   $(this).attr('data-tab') === 'smart_rentals';
        });
        
        var rentalPanel = $('#smart_rentals_product_data');
        
        console.log('Found rental tabs:', rentalTab.length);
        console.log('Rental checkbox checked:', rentalCheckbox.is(':checked'));
        
        if (rentalCheckbox.is(':checked')) {
            // Show rental tab
            rentalTab.show();
            $('body').addClass('rental-product-enabled');
            console.log('Rental enabled - showing tab');
        } else {
            // Hide rental tab and switch to General tab if currently on Rental tab
            if (rentalPanel.is(':visible')) {
                $('.general_tab a, .general_options a').trigger('click');
            }
            rentalTab.hide();
            $('body').removeClass('rental-product-enabled');
            console.log('Rental disabled - hiding tab');
        }
    }
    
    // Initial state with delay to ensure DOM is ready
    setTimeout(function() {
        toggleRentalTab();
    }, 100);
    
    // Toggle rental tab when checkbox changes
    rentalCheckbox.on('change', function() {
        setTimeout(function() {
            toggleRentalTab();
        }, 50);
    });
    
    // Also check when page loads and when product type changes
    $(window).on('load', function() {
        setTimeout(function() {
            toggleRentalTab();
        }, 200);
    });
    
    // Handle product type changes to show/hide rental elements
    $('#product-type').on('change', function() {
        var productType = $(this).val();
        
        if (productType === 'simple' || productType === 'variable') {
            rentalCheckbox.closest('p').show();
        } else {
            rentalCheckbox.closest('p').hide();
            rentalCheckbox.prop('checked', false);
        }
        
        // Always check tab visibility after product type change
        setTimeout(function() {
            toggleRentalTab();
        }, 100);
    });
    
    // Rental type specific field toggles
    var rentalType = $('select[name="smart_rentals_rental_type"]');
    var dailyPriceField = $('input[name="smart_rentals_daily_price"]').closest('.form-field');
    var hourlyPriceField = $('input[name="smart_rentals_hourly_price"]').closest('.form-field');
    
    // Disable "Coming Soon" rental types
    function disableComingSoonTypes() {
        var disabledTypes = ['period_time', 'transportation', 'hotel', 'appointment', 'taxi'];
        
        disabledTypes.forEach(function(type) {
            rentalType.find('option[value="' + type + '"]').prop('disabled', true).css({
                'color': '#999',
                'font-style': 'italic'
            });
        });
    }
    
    // Apply disabled styling
    disableComingSoonTypes();
    
    function togglePriceFields() {
        var selectedType = rentalType.val();
        
        // Check if selected type is disabled
        var disabledTypes = ['period_time', 'transportation', 'hotel', 'appointment', 'taxi'];
        if (disabledTypes.includes(selectedType)) {
            alert('This rental type is not yet available. Please select Daily, Hourly, or Mixed.');
            rentalType.val('');
            return;
        }
        
        // Hide all price fields first
        dailyPriceField.hide();
        hourlyPriceField.hide();
        
        // Show relevant fields based on rental type (only active types)
        switch (selectedType) {
            case 'day':
                dailyPriceField.show();
                break;
            case 'hour':
                hourlyPriceField.show();
                break;
            case 'mixed':
                dailyPriceField.show();
                hourlyPriceField.show();
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
            
            // Check if rental type is selected and valid
            var validTypes = ['day', 'hour', 'mixed'];
            if (!rentalTypeVal) {
                hasError = true;
                errorMessage += 'Please select a rental type.\n';
            } else if (!validTypes.includes(rentalTypeVal)) {
                hasError = true;
                errorMessage += 'Please select a supported rental type (Daily, Hourly, or Mixed).\n';
            }
            
            // Check for required price fields (only for active types)
            if (rentalTypeVal === 'day' || rentalTypeVal === 'mixed') {
                var dailyPrice = $('input[name="smart_rentals_daily_price"]').val();
                if (!dailyPrice || parseFloat(dailyPrice) <= 0) {
                    hasError = true;
                    errorMessage += 'Please enter a valid daily price.\n';
                }
            }
            
            if (rentalTypeVal === 'hour' || rentalTypeVal === 'mixed') {
                var hourlyPrice = $('input[name="smart_rentals_hourly_price"]').val();
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
    
    // Initialize WooCommerce enhanced select (Select2)
    if (typeof $.fn.selectWoo !== 'undefined') {
        $('.wc-enhanced-select').selectWoo();
    } else if (typeof $.fn.select2 !== 'undefined') {
        $('.wc-enhanced-select').select2();
    }
    
    // Add custom CSS for rental tab
    if (!$('#smart-rentals-admin-css').length) {
        $('head').append('<style id="smart-rentals-admin-css">' +
            '/* Rental tab visibility - HIDDEN BY DEFAULT */' +
            '.wc-tabs li.smart_rentals_options, ' +
            '.wc-tabs li.smart_rentals_tab, ' +
            '.wc-tabs li[data-tab="smart_rentals"] { display: none !important; }' +
            '/* Show only when rental is enabled */' +
            'body.rental-product-enabled .wc-tabs li.smart_rentals_options, ' +
            'body.rental-product-enabled .wc-tabs li.smart_rentals_tab, ' +
            'body.rental-product-enabled .wc-tabs li[data-tab="smart_rentals"] { display: block !important; }' +
            '/* Tab icon */' +
            '.wc-tabs li.smart_rentals_options a::before, ' +
            '.wc-tabs li.smart_rentals_tab a::before, ' +
            '.wc-tabs li[data-tab="smart_rentals"] a::before { content: "\\f508"; font-family: dashicons; margin-right: 5px; }' +
            '/* Panel styling */' +
            '#smart_rentals_product_data .options_group { border-bottom: 1px solid #eee; padding-bottom: 15px; margin-bottom: 15px; }' +
            '#smart_rentals_product_data .options_group:last-child { border-bottom: none; margin-bottom: 0; }' +
            '/* Rental checkbox inline styling */' +
            'p.form-field._rental_field { display: inline-block; margin-right: 20px; }' +
            '</style>');
    }
    
    // Auto-calculate mixed pricing suggestions
    $('input[name="smart_rentals_daily_price"]').on('blur', function() {
        var dailyPrice = parseFloat($(this).val());
        var hourlyPrice = parseFloat($('input[name="smart_rentals_hourly_price"]').val());
        
        if (dailyPrice > 0 && !hourlyPrice && rentalType.val() === 'mixed') {
            var suggestedHourly = (dailyPrice / 8).toFixed(2); // Assume 8 hours per day
            $('input[name="smart_rentals_hourly_price"]').val(suggestedHourly);
            
            // Show suggestion message
            if (!$('.pricing-suggestion').length) {
                $('input[name="smart_rentals_hourly_price"]').after(
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
    $('input[name="smart_rentals_rental_stock"]').on('change', function() {
        var stock = parseInt($(this).val());
        if (stock < 1) {
            $(this).val(1);
            alert('Rental stock must be at least 1.');
        }
    });
    
    // Rental period validation
    $('input[name="smart_rentals_max_rental_period"]').on('change', function() {
        var maxPeriod = parseInt($(this).val());
        var minPeriod = parseInt($('input[name="smart_rentals_min_rental_period"]').val());
        
        if (maxPeriod > 0 && minPeriod > 0 && maxPeriod < minPeriod) {
            alert('Maximum rental period cannot be less than minimum rental period.');
            $(this).val(minPeriod);
        }
    });
    
    $('input[name="smart_rentals_min_rental_period"]').on('change', function() {
        var minPeriod = parseInt($(this).val());
        var maxPeriod = parseInt($('input[name="smart_rentals_max_rental_period"]').val());
        
        if (maxPeriod > 0 && minPeriod > 0 && minPeriod > maxPeriod) {
            alert('Minimum rental period cannot be greater than maximum rental period.');
            $(this).val(maxPeriod);
        }
    });
});