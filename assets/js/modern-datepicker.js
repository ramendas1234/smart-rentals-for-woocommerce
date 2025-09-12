/**
 * Smart Rentals Modern Date Picker
 * Enhanced UI with Flatpickr integration
 */

(function($) {
    'use strict';
    
    // Modern Date Picker Class
    window.SmartRentalsModernDatePicker = {
        
        // Configuration
        config: {
            dateFormat: 'Y-m-d',
            enableTime: false,
            time_24hr: true,
            mode: 'range',
            showMonths: 2,
            minDate: 'today',
            maxDate: new Date().fp_incr(365), // 1 year from today
            locale: {
                firstDayOfWeek: 1,
                rangeSeparator: ' to ',
                weekdays: {
                    shorthand: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
                    longhand: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']
                },
                months: {
                    shorthand: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    longhand: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December']
                }
            },
            plugins: [],
            animate: true
        },
        
        // Initialize date picker
        init: function(pickupSelector, dropoffSelector, options) {
            if (typeof flatpickr === 'undefined') {
                console.error('Flatpickr library not available');
                return this.initFallback(pickupSelector, dropoffSelector);
            }
            
            var self = this;
            var config = Object.assign({}, this.config, options || {});
            
            // Responsive configuration
            if (window.innerWidth <= 768) {
                config.showMonths = 1;
                config.static = true;
            }
            
            // Enhanced configuration
            config.onReady = function(selectedDates, dateStr, instance) {
                self.onReady(selectedDates, dateStr, instance);
            };
            
            config.onChange = function(selectedDates, dateStr, instance) {
                self.onChange(selectedDates, dateStr, instance, pickupSelector, dropoffSelector);
            };
            
            config.onOpen = function(selectedDates, dateStr, instance) {
                self.onOpen(selectedDates, dateStr, instance);
            };
            
            config.onClose = function(selectedDates, dateStr, instance) {
                self.onClose(selectedDates, dateStr, instance);
            };
            
            // Initialize Flatpickr
            var picker = flatpickr(pickupSelector, config);
            
            // Make dropoff field also open the picker
            $(dropoffSelector).on('click', function() {
                picker.open();
            });
            
            // Add click handler for date icons
            $('.date-icon').on('click', function() {
                picker.open();
            });
            
            return picker;
        },
        
        // On ready callback
        onReady: function(selectedDates, dateStr, instance) {
            // Add custom classes
            instance.calendarContainer.classList.add('smart-rentals-calendar');
            
            // Add range information
            this.addRangeInfo(instance);
            
            // Add custom controls
            this.addCustomControls(instance);
        },
        
        // On change callback
        onChange: function(selectedDates, dateStr, instance, pickupSelector, dropoffSelector) {
            if (selectedDates.length === 2) {
                var pickup = selectedDates[0];
                var dropoff = selectedDates[1];
                
                // Update input values
                $(pickupSelector).val(this.formatDate(pickup, instance.config.dateFormat));
                $(dropoffSelector).val(this.formatDate(dropoff, instance.config.dateFormat));
                
                // Show duration
                this.showDuration(pickup, dropoff);
                
                // Trigger calculation
                this.triggerCalculation();
                
                // Add success animation
                this.addSuccessAnimation(pickupSelector, dropoffSelector);
                
            } else if (selectedDates.length === 1) {
                var pickup = selectedDates[0];
                $(pickupSelector).val(this.formatDate(pickup, instance.config.dateFormat));
                $(dropoffSelector).val('');
                
                // Show pickup selected state
                this.showPickupSelected();
            }
        },
        
        // On open callback
        onOpen: function(selectedDates, dateStr, instance) {
            // Add opening animation
            instance.calendarContainer.style.transform = 'scale(0.95) translateY(-10px)';
            instance.calendarContainer.style.opacity = '0';
            
            setTimeout(function() {
                instance.calendarContainer.style.transition = 'all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1)';
                instance.calendarContainer.style.transform = 'scale(1) translateY(0)';
                instance.calendarContainer.style.opacity = '1';
            }, 10);
        },
        
        // On close callback
        onClose: function(selectedDates, dateStr, instance) {
            // Add closing animation
            instance.calendarContainer.style.transition = 'all 0.2s ease-in';
            instance.calendarContainer.style.transform = 'scale(0.95) translateY(-5px)';
            instance.calendarContainer.style.opacity = '0.8';
        },
        
        // Add range information
        addRangeInfo: function(instance) {
            var rangeInfo = document.createElement('div');
            rangeInfo.className = 'range-info';
            rangeInfo.innerHTML = '<span class="pickup-label">📅 Pickup</span><span class="dropoff-label">📅 Drop-off</span>';
            instance.calendarContainer.appendChild(rangeInfo);
        },
        
        // Add custom controls
        addCustomControls: function(instance) {
            // Add quick select buttons
            var quickSelect = document.createElement('div');
            quickSelect.className = 'quick-select';
            quickSelect.innerHTML = `
                <button type="button" class="quick-btn" data-days="1">1 Day</button>
                <button type="button" class="quick-btn" data-days="3">3 Days</button>
                <button type="button" class="quick-btn" data-days="7">1 Week</button>
                <button type="button" class="quick-btn" data-days="30">1 Month</button>
            `;
            
            instance.calendarContainer.appendChild(quickSelect);
            
            // Add event listeners for quick select
            $(quickSelect).on('click', '.quick-btn', function() {
                var days = parseInt($(this).data('days'));
                var today = new Date();
                var endDate = new Date(today);
                endDate.setDate(endDate.getDate() + days);
                
                instance.setDate([today, endDate]);
            });
        },
        
        // Format date
        formatDate: function(date, format) {
            if (typeof flatpickr !== 'undefined' && flatpickr.formatDate) {
                return flatpickr.formatDate(date, format);
            }
            
            // Fallback formatting
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');
            
            if (format.includes('H:i')) {
                var hours = String(date.getHours()).padStart(2, '0');
                var minutes = String(date.getMinutes()).padStart(2, '0');
                return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
            }
            
            return year + '-' + month + '-' + day;
        },
        
        // Show duration
        showDuration: function(pickup, dropoff) {
            var durationMs = dropoff - pickup;
            var durationDays = Math.ceil(durationMs / (1000 * 60 * 60 * 24));
            var durationHours = Math.ceil(durationMs / (1000 * 60 * 60));
            
            var durationText = '';
            if (this.config.enableTime && durationHours < 24) {
                durationText = durationHours + ' ' + (durationHours === 1 ? 'hour' : 'hours');
            } else {
                durationText = durationDays + ' ' + (durationDays === 1 ? 'day' : 'days');
            }
            
            // Remove existing duration display
            $('.range-duration').remove();
            
            // Add new duration display
            var durationHtml = '<div class="range-duration"><i class="dashicons dashicons-clock"></i> ' + durationText + '</div>';
            $('.smart-rentals-container').append(durationHtml);
            
            // Auto-hide after 4 seconds
            setTimeout(function() {
                $('.range-duration').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 4000);
        },
        
        // Show pickup selected state
        showPickupSelected: function() {
            $('.pickup-selected-indicator').remove();
            var indicator = '<div class="pickup-selected-indicator">✅ Pickup date selected. Now choose drop-off date.</div>';
            $('.smart-rentals-container').append(indicator);
            
            setTimeout(function() {
                $('.pickup-selected-indicator').fadeOut(300, function() {
                    $(this).remove();
                });
            }, 3000);
        },
        
        // Add success animation
        addSuccessAnimation: function(pickupSelector, dropoffSelector) {
            $(pickupSelector + ', ' + dropoffSelector).addClass('success-selected');
            
            setTimeout(function() {
                $(pickupSelector + ', ' + dropoffSelector).removeClass('success-selected');
            }, 1000);
        },
        
        // Trigger calculation
        triggerCalculation: function() {
            if (typeof window.smartRentalsCalculateTotal === 'function') {
                setTimeout(window.smartRentalsCalculateTotal, 300);
            }
        },
        
        // Fallback initialization
        initFallback: function(pickupSelector, dropoffSelector) {
            console.log('Initializing fallback date pickers');
            
            $(pickupSelector + ', ' + dropoffSelector).attr('type', 'date').removeAttr('readonly');
            
            var today = new Date().toISOString().split('T')[0];
            $(pickupSelector).attr('min', today);
            
            $(pickupSelector).on('change', function() {
                var pickupDate = $(this).val();
                if (pickupDate) {
                    var minDropoff = new Date(pickupDate);
                    minDropoff.setDate(minDropoff.getDate() + 1);
                    $(dropoffSelector).attr('min', minDropoff.toISOString().split('T')[0]);
                }
            });
            
            // Trigger calculation on change
            $(pickupSelector + ', ' + dropoffSelector).on('change', this.triggerCalculation);
        }
    };
    
})(jQuery);