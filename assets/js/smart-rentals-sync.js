/**
 * Smart Rentals Real-time Sync Manager
 * Ensures tight synchronization between frontend and backend
 */

(function($) {
    'use strict';

    window.SmartRentalsSync = {
        // Configuration
        config: {
            syncInterval: 30000, // 30 seconds
            lockDuration: 900000, // 15 minutes
            validationDelay: 500, // 500ms debounce
            maxRetries: 3,
            retryDelay: 1000
        },

        // State management
        state: {
            currentProduct: null,
            selectedDates: {},
            availabilityCache: {},
            syncTimer: null,
            validationTimer: null,
            lockId: null,
            retryCount: 0
        },

        // Initialize sync manager
        init: function() {
            this.bindEvents();
            this.startPeriodicSync();
            this.initializeHeartbeat();
            
            // Listen for visibility changes
            document.addEventListener('visibilitychange', this.handleVisibilityChange.bind(this));
            
            // Listen for online/offline events
            window.addEventListener('online', this.handleOnline.bind(this));
            window.addEventListener('offline', this.handleOffline.bind(this));
            
            // Initialize WebSocket if available
            this.initializeWebSocket();
            
            console.log('Smart Rentals Sync Manager initialized');
        },

        // Bind events
        bindEvents: function() {
            var self = this;

            // Date selection changes
            $(document).on('change', '#pickup_date, #dropoff_date', function() {
                self.debounceValidation();
            });

            // Quantity changes
            $(document).on('change', '#quantity, .rental-quantity', function() {
                self.debounceValidation();
            });

            // Form submission
            $(document).on('submit', '#smart_rentals_booking_form', function(e) {
                return self.validateBeforeSubmit(e);
            });

            // Cart updates
            $(document.body).on('updated_cart_totals', function() {
                self.syncCartAvailability();
            });

            // Add to cart success
            $(document.body).on('added_to_cart', function(e, fragments, cart_hash, $button) {
                self.handleAddToCartSuccess($button);
            });

            // Calendar navigation
            $(document).on('click', '.smart-rentals-calendar-nav', function() {
                self.syncCalendarAvailability();
            });
        },

        // Debounce validation
        debounceValidation: function() {
            clearTimeout(this.state.validationTimer);
            this.state.validationTimer = setTimeout(() => {
                this.validateCurrentSelection();
            }, this.config.validationDelay);
        },

        // Validate current selection
        validateCurrentSelection: function() {
            var productId = $('#smart_rentals_booking_form').find('input[name="product_id"]').val();
            var pickupDate = $('#pickup_date').val();
            var dropoffDate = $('#dropoff_date').val();
            var quantity = $('#quantity').val() || 1;

            if (!productId || !pickupDate || !dropoffDate) {
                return;
            }

            // Show loading state
            this.showLoadingState();

            // Perform validation
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_validate_booking',
                    security: ajax_object.security,
                    product_id: productId,
                    pickup_date: pickupDate,
                    dropoff_date: dropoffDate,
                    quantity: quantity
                },
                success: (response) => {
                    if (response.success && response.data.valid) {
                        this.handleValidationSuccess(response.data);
                    } else {
                        this.handleValidationError(response.data);
                    }
                },
                error: () => {
                    this.handleConnectionError();
                }
            });
        },

        // Handle validation success
        handleValidationSuccess: function(data) {
            // Store lock ID if provided
            if (data.lock_id) {
                this.state.lockId = data.lock_id;
            }

            // Update UI
            this.hideLoadingState();
            this.showAvailability(true, data.message);
            
            // Update price display
            if ($('#rental-price-display').length) {
                this.updatePriceDisplay();
            }

            // Enable submit button
            $('.smart-rentals-book-now').prop('disabled', false);
        },

        // Handle validation error
        handleValidationError: function(data) {
            this.hideLoadingState();
            this.showAvailability(false, data.message || 'Not available');
            
            // Show detailed information if available
            if (data.details && data.details.available_quantity !== undefined) {
                this.showAvailableQuantity(data.details.available_quantity);
            }

            // Disable submit button
            $('.smart-rentals-book-now').prop('disabled', true);
        },

        // Validate before submit
        validateBeforeSubmit: function(e) {
            var $form = $(e.target);
            var isValid = true;

            // Check if we have a valid lock
            if (!this.state.lockId) {
                e.preventDefault();
                this.validateCurrentSelection();
                return false;
            }

            // Final availability check
            var productId = $form.find('input[name="product_id"]').val();
            var pickupDate = $('#pickup_date').val();
            var dropoffDate = $('#dropoff_date').val();
            var quantity = $('#quantity').val() || 1;

            // Synchronous validation (last resort)
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                async: false,
                data: {
                    action: 'smart_rentals_check_availability',
                    security: ajax_object.security,
                    product_id: productId,
                    pickup_date: pickupDate,
                    dropoff_date: dropoffDate,
                    quantity: quantity
                },
                success: function(response) {
                    if (!response.success || !response.data.available) {
                        isValid = false;
                        alert(response.data.message || 'This rental is no longer available. Please refresh and try again.');
                    }
                }
            });

            return isValid;
        },

        // Sync cart availability
        syncCartAvailability: function() {
            $('.cart_item').each(function() {
                var $item = $(this);
                if ($item.find('.rental-item-details').length) {
                    // This is a rental item, validate it
                    SmartRentalsSync.validateCartItem($item);
                }
            });
        },

        // Validate cart item
        validateCartItem: function($item) {
            var cartItemKey = $item.data('cart-item-key');
            var productId = $item.data('product-id');
            var pickupDate = $item.find('.rental-pickup-date').text();
            var dropoffDate = $item.find('.rental-dropoff-date').text();
            var quantity = $item.find('.qty').val();

            if (!productId || !pickupDate || !dropoffDate) {
                return;
            }

            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_check_availability',
                    security: ajax_object.security,
                    product_id: productId,
                    pickup_date: pickupDate,
                    dropoff_date: dropoffDate,
                    quantity: quantity
                },
                success: function(response) {
                    if (!response.success || !response.data.available) {
                        // Mark item as unavailable
                        $item.addClass('rental-unavailable');
                        $item.find('.product-name').append(
                            '<span class="rental-warning">' + 
                            (response.data.message || 'No longer available') + 
                            '</span>'
                        );
                    }
                }
            });
        },

        // Start periodic sync
        startPeriodicSync: function() {
            // Clear existing timer
            if (this.state.syncTimer) {
                clearInterval(this.state.syncTimer);
            }

            // Set up periodic sync
            this.state.syncTimer = setInterval(() => {
                this.performSync();
            }, this.config.syncInterval);
        },

        // Perform sync
        performSync: function() {
            // Sync calendar if visible
            if ($('.smart-rentals-calendar').is(':visible')) {
                this.syncCalendarAvailability();
            }

            // Sync current form if present
            if ($('#smart_rentals_booking_form').length) {
                this.validateCurrentSelection();
            }

            // Sync cart items
            if ($('.woocommerce-cart').length) {
                this.syncCartAvailability();
            }
        },

        // Sync calendar availability
        syncCalendarAvailability: function() {
            var $calendar = $('.smart-rentals-calendar');
            if (!$calendar.length) return;

            var productId = $calendar.data('product-id');
            var currentMonth = $calendar.data('current-month');
            var currentYear = $calendar.data('current-year');

            // Get all dates in current view
            var dates = [];
            $calendar.find('.calendar-day').each(function() {
                var date = $(this).data('date');
                if (date) {
                    dates.push(date);
                }
            });

            // Batch sync availability for all dates
            this.batchSyncAvailability(productId, dates);
        },

        // Batch sync availability
        batchSyncAvailability: function(productId, dates) {
            if (!dates.length) return;

            // Check cache first
            var uncachedDates = dates.filter(date => {
                var cacheKey = productId + '_' + date;
                var cached = this.state.availabilityCache[cacheKey];
                return !cached || (Date.now() - cached.timestamp > 60000); // 1 minute cache
            });

            if (!uncachedDates.length) {
                // All dates are cached, update UI from cache
                this.updateCalendarFromCache(productId, dates);
                return;
            }

            // Fetch uncached dates
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'smart_rentals_batch_availability',
                    security: ajax_object.security,
                    product_id: productId,
                    dates: uncachedDates
                },
                success: (response) => {
                    if (response.success) {
                        // Update cache
                        Object.keys(response.data).forEach(date => {
                            var cacheKey = productId + '_' + date;
                            this.state.availabilityCache[cacheKey] = {
                                data: response.data[date],
                                timestamp: Date.now()
                            };
                        });

                        // Update calendar UI
                        this.updateCalendarFromCache(productId, dates);
                    }
                }
            });
        },

        // Update calendar from cache
        updateCalendarFromCache: function(productId, dates) {
            dates.forEach(date => {
                var cacheKey = productId + '_' + date;
                var cached = this.state.availabilityCache[cacheKey];
                
                if (cached && cached.data) {
                    var $day = $('.calendar-day[data-date="' + date + '"]');
                    var availability = cached.data.available_quantity;
                    
                    // Update availability display
                    $day.find('.availability-count').text(availability);
                    
                    // Update CSS class
                    $day.removeClass('available partially-available unavailable');
                    if (availability === 0) {
                        $day.addClass('unavailable');
                    } else if (availability < cached.data.total_stock) {
                        $day.addClass('partially-available');
                    } else {
                        $day.addClass('available');
                    }
                }
            });
        },

        // Initialize heartbeat
        initializeHeartbeat: function() {
            // Use WordPress heartbeat API if available
            if (typeof wp !== 'undefined' && wp.heartbeat) {
                $(document).on('heartbeat-send', (e, data) => {
                    if (this.state.lockId) {
                        data.smart_rentals_locks = [this.state.lockId];
                    }
                });

                $(document).on('heartbeat-tick', (e, data) => {
                    if (data.smart_rentals_refresh) {
                        this.performSync();
                    }
                });
            }
        },

        // Initialize WebSocket for real-time updates
        initializeWebSocket: function() {
            if (!window.WebSocket || !ajax_object.websocket_url) {
                return;
            }

            try {
                this.ws = new WebSocket(ajax_object.websocket_url);
                
                this.ws.onopen = () => {
                    console.log('Smart Rentals WebSocket connected');
                };

                this.ws.onmessage = (event) => {
                    try {
                        var data = JSON.parse(event.data);
                        this.handleRealtimeUpdate(data);
                    } catch (e) {
                        console.error('WebSocket message parse error:', e);
                    }
                };

                this.ws.onerror = (error) => {
                    console.error('WebSocket error:', error);
                };

                this.ws.onclose = () => {
                    // Attempt reconnection after delay
                    setTimeout(() => {
                        this.initializeWebSocket();
                    }, 30000);
                };
            } catch (e) {
                console.error('WebSocket initialization error:', e);
            }
        },

        // Handle realtime update
        handleRealtimeUpdate: function(data) {
            switch (data.type) {
                case 'availability_change':
                    this.handleAvailabilityChange(data);
                    break;
                case 'booking_created':
                    this.handleBookingCreated(data);
                    break;
                case 'booking_cancelled':
                    this.handleBookingCancelled(data);
                    break;
            }
        },

        // Handle availability change
        handleAvailabilityChange: function(data) {
            // Invalidate cache for affected product/dates
            if (data.product_id && data.dates) {
                data.dates.forEach(date => {
                    var cacheKey = data.product_id + '_' + date;
                    delete this.state.availabilityCache[cacheKey];
                });
            }

            // Trigger immediate sync if viewing affected product
            var currentProductId = $('#smart_rentals_booking_form').find('input[name="product_id"]').val();
            if (currentProductId == data.product_id) {
                this.performSync();
            }
        },

        // Handle visibility change
        handleVisibilityChange: function() {
            if (!document.hidden) {
                // Page became visible, perform sync
                this.performSync();
            }
        },

        // Handle online event
        handleOnline: function() {
            console.log('Connection restored, syncing...');
            this.performSync();
            
            // Restart periodic sync
            this.startPeriodicSync();
        },

        // Handle offline event
        handleOffline: function() {
            console.log('Connection lost');
            
            // Stop periodic sync
            if (this.state.syncTimer) {
                clearInterval(this.state.syncTimer);
            }
            
            // Show offline warning
            this.showOfflineWarning();
        },

        // Show loading state
        showLoadingState: function() {
            $('.smart-rentals-loader-date').show();
            $('.rental-availability-status').html(
                '<span class="checking">Checking availability...</span>'
            );
        },

        // Hide loading state
        hideLoadingState: function() {
            $('.smart-rentals-loader-date').hide();
        },

        // Show availability status
        showAvailability: function(available, message) {
            var $status = $('.rental-availability-status');
            if (!$status.length) {
                $status = $('<div class="rental-availability-status"></div>');
                $('#smart_rentals_booking_form').prepend($status);
            }

            if (available) {
                $status.html('<span class="available">' + message + '</span>');
                $status.removeClass('error').addClass('success');
            } else {
                $status.html('<span class="unavailable">' + message + '</span>');
                $status.removeClass('success').addClass('error');
            }
        },

        // Show available quantity
        showAvailableQuantity: function(quantity) {
            var $info = $('.rental-quantity-info');
            if (!$info.length) {
                $info = $('<div class="rental-quantity-info"></div>');
                $('.rental-availability-status').after($info);
            }

            $info.html('Available: ' + quantity + ' units');
        },

        // Update price display
        updatePriceDisplay: function() {
            // Trigger price calculation
            if (typeof calculateTotal === 'function') {
                calculateTotal();
            }
        },

        // Show offline warning
        showOfflineWarning: function() {
            var $warning = $('#offline-warning');
            if (!$warning.length) {
                $warning = $('<div id="offline-warning" class="notice notice-warning">' +
                    'You are currently offline. Availability information may not be up to date.' +
                    '</div>');
                $('body').prepend($warning);
            }
            $warning.show();
        },

        // Handle connection error
        handleConnectionError: function() {
            this.state.retryCount++;
            
            if (this.state.retryCount < this.config.maxRetries) {
                // Retry after delay
                setTimeout(() => {
                    this.validateCurrentSelection();
                }, this.config.retryDelay * this.state.retryCount);
            } else {
                // Max retries reached
                this.showAvailability(false, 'Connection error. Please check your internet connection.');
                this.state.retryCount = 0;
            }
        },

        // Handle add to cart success
        handleAddToCartSuccess: function($button) {
            // Clear any locks
            this.state.lockId = null;
            
            // Clear validation state
            $('.rental-availability-status').empty();
            
            // Reset form if needed
            if ($button.closest('#smart_rentals_booking_form').length) {
                // Keep dates selected for convenience
                // Just clear the availability check
                this.performSync();
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        SmartRentalsSync.init();
    });

})(jQuery);