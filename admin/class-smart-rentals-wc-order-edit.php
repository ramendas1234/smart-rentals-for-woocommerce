<?php
/**
 * Smart Rentals WC Order Edit
 * Handles editing rental data from WooCommerce order edit screen
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Order_Edit' ) ) {

    class Smart_Rentals_WC_Order_Edit {

        /**
         * Constructor
         */
        public function __construct() {
            // Add custom meta box to order edit screen
            add_action( 'add_meta_boxes', [ $this, 'add_rental_edit_meta_box' ] );
            
            // Save rental data
            add_action( 'save_post', [ $this, 'save_rental_data' ] );
            
            // Enqueue admin scripts
            add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
            
            // AJAX handlers
            add_action( 'wp_ajax_smart_rentals_update_rental_item', [ $this, 'ajax_update_rental_item' ] );
            add_action( 'wp_ajax_smart_rentals_save_order_item_rental_data', [ $this, 'ajax_save_order_item_rental_data' ] );
            
            // WooCommerce order edit hooks
            add_action( 'woocommerce_admin_order_item_headers', [ $this, 'admin_order_item_headers' ] );
            add_action( 'woocommerce_admin_order_item_values', [ $this, 'admin_order_item_values' ], 10, 3 );
            
            // Save order item data
            add_action( 'woocommerce_saved_order_items', [ $this, 'saved_order_items' ], 10, 2 );
        }

        /**
         * Add rental edit meta box
         */
        public function add_rental_edit_meta_box() {
            global $post;
            
            if ( !$post || $post->post_type !== 'shop_order' ) {
                return;
            }
            
            $order = wc_get_order( $post->ID );
            if ( !$order ) {
                return;
            }
            
            // Check if order has rental items
            $has_rental_items = false;
            foreach ( $order->get_items() as $item ) {
                if ( $this->is_rental_item( $item ) ) {
                    $has_rental_items = true;
                    break;
                }
            }
            
            if ( $has_rental_items ) {
                add_meta_box(
                    'smart-rentals-edit-box',
                    __( 'Edit Rental Details', 'smart-rentals-wc' ),
                    [ $this, 'rental_edit_meta_box_content' ],
                    'shop_order',
                    'normal',
                    'high'
                );
            }
        }

        /**
         * Check if item is a rental item (robust detection)
         */
        private function is_rental_item( $item ) {
            $is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
            $pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
            $dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
            $rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) );
            
            return ( $is_rental === 'yes' || ( $pickup_date && $dropoff_date ) || $rental_quantity );
        }

        /**
         * Rental edit meta box content
         */
        public function rental_edit_meta_box_content( $post ) {
            $order = wc_get_order( $post->ID );
            if ( !$order ) {
                return;
            }
            
            wp_nonce_field( 'smart_rentals_edit_rental_data', 'smart_rentals_edit_nonce' );
            
            // Get rental items
            $rental_items = [];
            foreach ( $order->get_items() as $item_id => $item ) {
                if ( $this->is_rental_item( $item ) ) {
                    $rental_items[$item_id] = $item;
                }
            }
            
            if ( empty( $rental_items ) ) {
                echo '<p>' . __( 'No rental items found in this order.', 'smart-rentals-wc' ) . '</p>';
                return;
            }
            
            ?>
            <div class="smart-rentals-edit-container">
                <div class="smart-rentals-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: #f1f1f1; border-radius: 5px;">
                    <h4 style="margin: 0;"><?php _e( 'Rental Order Modification', 'smart-rentals-wc' ); ?></h4>
                    <div class="smart-rentals-actions">
                        <button type="button" id="check-all-availability" class="button button-secondary">
                            <?php _e( 'Check All Availability', 'smart-rentals-wc' ); ?>
                        </button>
                        <button type="button" id="reset-all-changes" class="button button-secondary">
                            <?php _e( 'Reset All Changes', 'smart-rentals-wc' ); ?>
                        </button>
                    </div>
                </div>
                
                <div class="smart-rentals-notice" style="margin-bottom: 20px; padding: 10px; background: #fff3cd; border: 1px solid #ffeaa7; border-radius: 4px;">
                    <p style="margin: 0;">
                        <strong><?php _e( 'Important:', 'smart-rentals-wc' ); ?></strong> 
                        <?php _e( 'Modifying rental details will recalculate pricing and check availability. Changes will be logged and customers will be notified if email notifications are enabled.', 'smart-rentals-wc' ); ?>
                    </p>
                </div>
                
                <?php foreach ( $rental_items as $item_id => $item ) : ?>
                    <?php
                    $pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
                    $dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
                    $rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: 1;
                    $product_id = $item->get_product_id();
                    $product = wc_get_product( $product_id );
                    $current_price = $item->get_total();
                    $rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' ) ?: 1;
                    ?>
                    
                    <div class="rental-item-edit" style="border: 2px solid #ddd; padding: 20px; margin: 15px 0; background: #fff; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                        <div class="rental-item-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 1px solid #eee;">
                            <h5 style="margin: 0; color: #333;">
                                <?php echo esc_html( $item->get_name() ); ?>
                                <span style="font-size: 12px; color: #666; font-weight: normal;">(Item #<?php echo $item_id; ?>)</span>
                            </h5>
                            <div class="rental-item-status">
                                <span class="rental-stock-info" style="font-size: 12px; color: #666;">
                                    <?php printf( __( 'Stock: %d', 'smart-rentals-wc' ), $rental_stock ); ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="rental-edit-fields">
                            <table class="form-table" style="margin: 0;">
                                <tr>
                                    <th style="width: 200px; padding: 10px 0;">
                                        <label for="pickup_date_<?php echo $item_id; ?>">
                                            <?php _e( 'Pickup Date & Time', 'smart-rentals-wc' ); ?>
                                            <span class="required" style="color: red;">*</span>
                                        </label>
                                    </th>
                                    <td style="padding: 10px 0;">
                                        <input type="datetime-local" 
                                               id="pickup_date_<?php echo $item_id; ?>"
                                               name="rental_data[<?php echo $item_id; ?>][pickup_date]" 
                                               value="<?php echo esc_attr( $pickup_date ? date( 'Y-m-d\TH:i', strtotime( $pickup_date ) ) : '' ); ?>" 
                                               class="rental-date-input"
                                               style="width: 250px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" 
                                               required />
                                        <p class="description" style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                                            <?php _e( 'Current:', 'smart-rentals-wc' ); ?> 
                                            <?php echo $pickup_date ? date( 'M j, Y g:i A', strtotime( $pickup_date ) ) : __( 'Not set', 'smart-rentals-wc' ); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 200px; padding: 10px 0;">
                                        <label for="dropoff_date_<?php echo $item_id; ?>">
                                            <?php _e( 'Dropoff Date & Time', 'smart-rentals-wc' ); ?>
                                            <span class="required" style="color: red;">*</span>
                                        </label>
                                    </th>
                                    <td style="padding: 10px 0;">
                                        <input type="datetime-local" 
                                               id="dropoff_date_<?php echo $item_id; ?>"
                                               name="rental_data[<?php echo $item_id; ?>][dropoff_date]" 
                                               value="<?php echo esc_attr( $dropoff_date ? date( 'Y-m-d\TH:i', strtotime( $dropoff_date ) ) : '' ); ?>" 
                                               class="rental-date-input"
                                               style="width: 250px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" 
                                               required />
                                        <p class="description" style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                                            <?php _e( 'Current:', 'smart-rentals-wc' ); ?> 
                                            <?php echo $dropoff_date ? date( 'M j, Y g:i A', strtotime( $dropoff_date ) ) : __( 'Not set', 'smart-rentals-wc' ); ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 200px; padding: 10px 0;">
                                        <label for="rental_quantity_<?php echo $item_id; ?>">
                                            <?php _e( 'Rental Quantity', 'smart-rentals-wc' ); ?>
                                        </label>
                                    </th>
                                    <td style="padding: 10px 0;">
                                        <input type="number" 
                                               id="rental_quantity_<?php echo $item_id; ?>"
                                               name="rental_data[<?php echo $item_id; ?>][quantity]" 
                                               value="<?php echo esc_attr( $rental_quantity ); ?>" 
                                               min="1" 
                                               max="<?php echo $rental_stock; ?>"
                                               class="rental-quantity-input"
                                               style="width: 100px; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
                                        <p class="description" style="margin: 5px 0 0 0; font-size: 12px; color: #666;">
                                            <?php _e( 'Current:', 'smart-rentals-wc' ); ?> <?php echo $rental_quantity; ?> | 
                                            <?php _e( 'Max:', 'smart-rentals-wc' ); ?> <?php echo $rental_stock; ?>
                                        </p>
                                    </td>
                                </tr>
                                <tr>
                                    <th style="width: 200px; padding: 10px 0;">
                                        <label><?php _e( 'Current Price', 'smart-rentals-wc' ); ?></label>
                                    </th>
                                    <td style="padding: 10px 0;">
                                        <span class="current-price" style="font-weight: bold; color: #333;">
                                            <?php echo smart_rentals_wc_price( $current_price ); ?>
                                        </span>
                                        <span class="new-price" style="margin-left: 10px; font-weight: bold; color: #28a745; display: none;">
                                            <?php _e( 'New Price:', 'smart-rentals-wc' ); ?> <span class="new-price-value"></span>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="2" style="padding: 15px 0;">
                                        <button type="button" 
                                                class="button button-primary rental-check-availability" 
                                                data-item-id="<?php echo $item_id; ?>" 
                                                data-product-id="<?php echo $product_id; ?>"
                                                style="margin-right: 10px;">
                                            <span class="dashicons dashicons-search" style="vertical-align: middle; margin-right: 5px;"></span>
                                            <?php _e( 'Check Availability & Update Price', 'smart-rentals-wc' ); ?>
                                        </button>
                                        <button type="button" 
                                                class="button button-secondary rental-reset-item" 
                                                data-item-id="<?php echo $item_id; ?>"
                                                style="margin-right: 10px;">
                                            <span class="dashicons dashicons-undo" style="vertical-align: middle; margin-right: 5px;"></span>
                                            <?php _e( 'Reset to Original', 'smart-rentals-wc' ); ?>
                                        </button>
                                        <div id="availability_result_<?php echo $item_id; ?>" class="rental-availability-result" style="margin-top: 15px;"></div>
                                    </td>
                                </tr>
                            </table>
                            
                            <input type="hidden" name="rental_data[<?php echo $item_id; ?>][product_id]" value="<?php echo $product_id; ?>" />
                            <input type="hidden" name="rental_data[<?php echo $item_id; ?>][item_id]" value="<?php echo $item_id; ?>" />
                            <input type="hidden" name="rental_data[<?php echo $item_id; ?>][original_pickup]" value="<?php echo esc_attr( $pickup_date ); ?>" />
                            <input type="hidden" name="rental_data[<?php echo $item_id; ?>][original_dropoff]" value="<?php echo esc_attr( $dropoff_date ); ?>" />
                            <input type="hidden" name="rental_data[<?php echo $item_id; ?>][original_quantity]" value="<?php echo esc_attr( $rental_quantity ); ?>" />
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="smart-rentals-footer" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 5px; border: 1px solid #dee2e6;">
                    <p style="margin: 0; font-size: 13px; color: #666;">
                        <span class="dashicons dashicons-info" style="vertical-align: middle; margin-right: 5px;"></span>
                        <?php _e( 'All changes will be validated for availability and pricing will be recalculated automatically. An order note will be added for each modification.', 'smart-rentals-wc' ); ?>
                    </p>
                </div>
                
                <?php
                // Display modification history
                $admin_instance = Smart_Rentals_WC_Admin::instance();
                $admin_instance->display_order_modification_history( $order );
                ?>
            </div>
            
            <script type="text/javascript">
            jQuery(document).ready(function($) {
                console.log('Smart Rentals Enhanced Order Edit Script Loaded');
                
                // Check all availability
                $('#check-all-availability').on('click', function(e) {
                    e.preventDefault();
                    $('.rental-check-availability').each(function() {
                        $(this).trigger('click');
                    });
                });
                
                // Reset all changes
                $('#reset-all-changes').on('click', function(e) {
                    e.preventDefault();
                    if (confirm('<?php _e( 'Are you sure you want to reset all changes? This will restore all rental items to their original values.', 'smart-rentals-wc' ); ?>')) {
                        $('.rental-reset-item').each(function() {
                            $(this).trigger('click');
                        });
                    }
                });
                
                // Reset individual item
                $('.rental-reset-item').on('click', function(e) {
                    e.preventDefault();
                    var itemId = $(this).data('item-id');
                    var $container = $(this).closest('.rental-item-edit');
                    
                    // Get original values
                    var originalPickup = $container.find('input[name="rental_data[' + itemId + '][original_pickup]"]').val();
                    var originalDropoff = $container.find('input[name="rental_data[' + itemId + '][original_dropoff]"]').val();
                    var originalQuantity = $container.find('input[name="rental_data[' + itemId + '][original_quantity]"]').val();
                    
                    // Reset form fields
                    $('#pickup_date_' + itemId).val(originalPickup ? new Date(originalPickup).toISOString().slice(0, 16) : '');
                    $('#dropoff_date_' + itemId).val(originalDropoff ? new Date(originalDropoff).toISOString().slice(0, 16) : '');
                    $('#rental_quantity_' + itemId).val(originalQuantity);
                    
                    // Clear results and hide new price
                    $('#availability_result_' + itemId).html('');
                    $container.find('.new-price').hide();
                    $container.removeClass('has-changes');
                    $container.css('border-color', '#ddd');
                });
                
                // Check availability for individual item
                $('.rental-check-availability').on('click', function(e) {
                    e.preventDefault();
                    console.log('Check availability clicked from meta box');
                    
                    var itemId = $(this).data('item-id');
                    var productId = $(this).data('product-id');
                    var $button = $(this);
                    var $result = $('#availability_result_' + itemId);
                    var $container = $button.closest('.rental-item-edit');
                    
                    var pickupDate = $('#pickup_date_' + itemId).val();
                    var dropoffDate = $('#dropoff_date_' + itemId).val();
                    var quantity = $('#rental_quantity_' + itemId).val() || 1;
                    
                    console.log('Data:', {itemId, productId, pickupDate, dropoffDate, quantity});
                    
                    if (!pickupDate || !dropoffDate) {
                        $result.html('<div class="notice notice-error inline"><p><strong><?php _e( 'Error:', 'smart-rentals-wc' ); ?></strong> <?php _e( 'Please select both pickup and dropoff dates.', 'smart-rentals-wc' ); ?></p></div>');
                        return;
                    }
                    
                    // Validate date range
                    var pickup = new Date(pickupDate);
                    var dropoff = new Date(dropoffDate);
                    if (pickup >= dropoff) {
                        $result.html('<div class="notice notice-error inline"><p><strong><?php _e( 'Error:', 'smart-rentals-wc' ); ?></strong> <?php _e( 'Dropoff date must be after pickup date.', 'smart-rentals-wc' ); ?></p></div>');
                        return;
                    }
                    
                    $button.prop('disabled', true).html('<span class="dashicons dashicons-update" style="vertical-align: middle; margin-right: 5px; animation: spin 1s linear infinite;"></span><?php _e( 'Checking...', 'smart-rentals-wc' ); ?>');
                    $result.html('<div class="notice notice-info inline"><p><?php _e( 'Checking availability and calculating new price...', 'smart-rentals-wc' ); ?></p></div>');
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'POST',
                        data: {
                            action: 'smart_rentals_check_order_edit_availability',
                            nonce: '<?php echo wp_create_nonce( 'smart-rentals-order-edit' ); ?>',
                            product_id: productId,
                            pickup_date: pickupDate,
                            dropoff_date: dropoffDate,
                            quantity: quantity,
                            exclude_order_id: <?php echo $post->ID; ?>
                        },
                        success: function(response) {
                            $button.prop('disabled', false).html('<span class="dashicons dashicons-search" style="vertical-align: middle; margin-right: 5px;"></span><?php _e( 'Check Availability & Update Price', 'smart-rentals-wc' ); ?>');
                            
                            if (response.success) {
                                var message = '<div class="notice notice-success inline"><p>';
                                message += '<strong>✓ <?php _e( 'Available', 'smart-rentals-wc' ); ?></strong><br>';
                                message += '<?php _e( 'Available quantity:', 'smart-rentals-wc' ); ?> ' + response.data.available_quantity + '<br>';
                                message += '<?php _e( 'New price:', 'smart-rentals-wc' ); ?> ' + response.data.formatted_price + '<br>';
                                message += '<?php _e( 'Duration:', 'smart-rentals-wc' ); ?> ' + response.data.duration_text;
                                message += '</p></div>';
                                $result.html(message);
                                
                                // Update price display
                                var $newPrice = $container.find('.new-price');
                                var $newPriceValue = $container.find('.new-price-value');
                                $newPriceValue.text(response.data.formatted_price);
                                $newPrice.show();
                                
                                // Add visual indicator that item has changes
                                $container.addClass('has-changes');
                                $container.css('border-color', '#28a745');
                            } else {
                                var message = '<div class="notice notice-error inline"><p>';
                                message += '<strong>✗ <?php _e( 'Not Available', 'smart-rentals-wc' ); ?></strong><br>';
                                message += response.data.message;
                                message += '</p></div>';
                                $result.html(message);
                                
                                // Hide new price and remove change indicator
                                $container.find('.new-price').hide();
                                $container.removeClass('has-changes');
                                $container.css('border-color', '#ddd');
                            }
                        },
                        error: function() {
                            $button.prop('disabled', false).html('<span class="dashicons dashicons-search" style="vertical-align: middle; margin-right: 5px;"></span><?php _e( 'Check Availability & Update Price', 'smart-rentals-wc' ); ?>');
                            $result.html('<div class="notice notice-error inline"><p><?php _e( 'Error checking availability. Please try again.', 'smart-rentals-wc' ); ?></p></div>');
                        }
                    });
                });
                
                // Auto-check availability when dates change (with debounce)
                var checkTimeout;
                $('.rental-date-input, .rental-quantity-input').on('change', function() {
                    var $container = $(this).closest('.rental-item-edit');
                    var $checkButton = $container.find('.rental-check-availability');
                    var $result = $container.find('.rental-availability-result');
                    
                    // Clear previous timeout
                    clearTimeout(checkTimeout);
                    
                    // Clear previous result
                    $result.html('');
                    
                    // Auto-trigger availability check after a delay
                    checkTimeout = setTimeout(function() {
                        $checkButton.trigger('click');
                    }, 1000);
                });
                
                // Add CSS for spin animation
                $('<style>')
                    .prop('type', 'text/css')
                    .html('@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }')
                    .appendTo('head');
            });
            </script>
            <?php
        }

        /**
         * Admin order item headers
         */
        public function admin_order_item_headers( $order ) {
            if ( !$order ) return;

            // Debug log
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                smart_rentals_wc_log( 'admin_order_item_headers called for order #' . $order->get_id() );
            }
            
            // Add debug comment to HTML
            echo '<!-- Smart Rentals: admin_order_item_headers called for order #' . $order->get_id() . ' -->';

            // Check if order has rental items (support both old and new orders)
            $has_rental_items = false;
            foreach ( $order->get_items() as $item ) {
                // Check multiple ways to detect rental items
                $is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
                $pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
                $dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
                $rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) );
                
                // Item is rental if it has the rental flag OR has rental dates
                if ( $is_rental === 'yes' || ( $pickup_date && $dropoff_date ) || $rental_quantity ) {
                    $has_rental_items = true;
                    break;
                }
            }

            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                smart_rentals_wc_log( 'Order #' . $order->get_id() . ' has rental items: ' . ($has_rental_items ? 'yes' : 'no') );
            }

            if ( $has_rental_items ) {
                echo '<th class="item-rental-dates">' . __( 'Rental Details', 'smart-rentals-wc' ) . '</th>';
                // Add debug comment in HTML
                echo '<!-- Smart Rentals: Rental Details column added -->';
            }
        }

        /**
         * Admin order item values
         */
        public function admin_order_item_values( $product, $item, $item_id ) {
            // Debug log
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                smart_rentals_wc_log( 'admin_order_item_values called for item #' . $item_id );
            }
            
            // Add debug comment to HTML
            echo '<!-- Smart Rentals: admin_order_item_values called for item #' . $item_id . ' -->';

            // Check if this is a rental item (support both old and new orders)
            $is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
            $pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
            $dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
            $rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) );
            
            // Item is rental if it has the rental flag OR has rental dates
            $is_rental_item = ( $is_rental === 'yes' || ( $pickup_date && $dropoff_date ) || $rental_quantity );
            
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                smart_rentals_wc_log( sprintf( 
                    'Item #%d rental detection: is_rental=%s, pickup_date=%s, dropoff_date=%s, rental_quantity=%s, result=%s',
                    $item_id, 
                    $is_rental ?: 'empty',
                    $pickup_date ?: 'empty',
                    $dropoff_date ?: 'empty',
                    $rental_quantity ?: 'empty',
                    $is_rental_item ? 'yes' : 'no'
                ) );
            }

            if ( !$is_rental_item ) {
                echo '<td class="item-rental-dates"></td>'; // Empty cell for non-rental items
                return;
            }

            // Add debug comment
            echo '<!-- Smart Rentals: Processing rental item #' . $item_id . ' -->';

            // Get rental data
            $pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
            $dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
            $pickup_time = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_time' ) );
            $dropoff_time = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_time' ) );
            $rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) );
            $security_deposit = $item->get_meta( smart_rentals_wc_meta_key( 'security_deposit' ) );

            // Get product ID for availability checking
            $product_id = $item->get_product_id();

            ?>
            <td class="item-rental-dates">
                <div class="rental-edit-container" style="min-width: 350px;">
                    <!-- Display Mode -->
                    <div class="rental-display-info">
                        <div style="margin-bottom: 8px;">
                            <strong><?php _e( 'Pickup:', 'smart-rentals-wc' ); ?></strong>
                            <span class="pickup-date-display">
                                <?php echo $pickup_date ? date( 'M j, Y g:i A', strtotime( $pickup_date ) ) : __( 'Not set', 'smart-rentals-wc' ); ?>
                            </span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong><?php _e( 'Dropoff:', 'smart-rentals-wc' ); ?></strong>
                            <span class="dropoff-date-display">
                                <?php echo $dropoff_date ? date( 'M j, Y g:i A', strtotime( $dropoff_date ) ) : __( 'Not set', 'smart-rentals-wc' ); ?>
                            </span>
                        </div>
                        <div style="margin-bottom: 8px;">
                            <strong><?php _e( 'Quantity:', 'smart-rentals-wc' ); ?></strong>
                            <span class="rental-quantity-display"><?php echo esc_html( $rental_quantity ?: 1 ); ?></span>
                        </div>
                        <?php if ( $security_deposit ) : ?>
                            <div style="margin-bottom: 8px;">
                                <strong><?php _e( 'Deposit:', 'smart-rentals-wc' ); ?></strong>
                                <span class="security-deposit-display"><?php echo smart_rentals_wc_price( $security_deposit ); ?></span>
                            </div>
                        <?php endif; ?>
                        <div style="margin-bottom: 8px;">
                            <strong><?php _e( 'Total:', 'smart-rentals-wc' ); ?></strong>
                            <span class="rental-total-display"><?php echo smart_rentals_wc_price( $item->get_total() ); ?></span>
                        </div>
                        <button type="button" class="button button-small rental-edit-toggle" data-item-id="<?php echo $item_id; ?>">
                            <span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 3px;"></span>
                            <?php _e( 'Edit Details', 'smart-rentals-wc' ); ?>
                        </button>
                    </div>
                    
                    <!-- Edit Mode -->
                    <div class="rental-edit-fields" style="display: none;">
                        <table class="form-table" style="margin: 0; width: 100%;">
                            <tr>
                                <td style="padding: 3px 0; width: 30%;">
                                    <label for="rental_pickup_date_<?php echo $item_id; ?>" style="font-weight: bold; font-size: 12px;">
                                        <?php _e( 'Pickup Date & Time', 'smart-rentals-wc' ); ?>
                                    </label>
                                </td>
                                <td style="padding: 3px 0;">
                                    <input type="datetime-local" 
                                           id="rental_pickup_date_<?php echo $item_id; ?>"
                                           name="rental_pickup_date[<?php echo $item_id; ?>]" 
                                           value="<?php echo esc_attr( $pickup_date ? date( 'Y-m-d\TH:i', strtotime( $pickup_date ) ) : '' ); ?>" 
                                           class="rental-date-input"
                                           style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;" />
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0;">
                                    <label for="rental_dropoff_date_<?php echo $item_id; ?>" style="font-weight: bold; font-size: 12px;">
                                        <?php _e( 'Dropoff Date & Time', 'smart-rentals-wc' ); ?>
                                    </label>
                                </td>
                                <td style="padding: 3px 0;">
                                    <input type="datetime-local" 
                                           id="rental_dropoff_date_<?php echo $item_id; ?>"
                                           name="rental_dropoff_date[<?php echo $item_id; ?>]" 
                                           value="<?php echo esc_attr( $dropoff_date ? date( 'Y-m-d\TH:i', strtotime( $dropoff_date ) ) : '' ); ?>" 
                                           class="rental-date-input"
                                           style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;" />
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0;">
                                    <label for="rental_quantity_<?php echo $item_id; ?>" style="font-weight: bold; font-size: 12px;">
                                        <?php _e( 'Quantity', 'smart-rentals-wc' ); ?>
                                    </label>
                                </td>
                                <td style="padding: 3px 0;">
                                    <input type="number" 
                                           id="rental_quantity_<?php echo $item_id; ?>"
                                           name="rental_quantity[<?php echo $item_id; ?>]" 
                                           value="<?php echo esc_attr( $rental_quantity ?: 1 ); ?>" 
                                           min="1" 
                                           class="rental-quantity-input"
                                           style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;" />
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 3px 0;">
                                    <label for="rental_security_deposit_<?php echo $item_id; ?>" style="font-weight: bold; font-size: 12px;">
                                        <?php _e( 'Security Deposit', 'smart-rentals-wc' ); ?>
                                    </label>
                                </td>
                                <td style="padding: 3px 0;">
                                    <input type="number" 
                                           id="rental_security_deposit_<?php echo $item_id; ?>"
                                           name="rental_security_deposit[<?php echo $item_id; ?>]" 
                                           value="<?php echo esc_attr( $security_deposit ?: 0 ); ?>" 
                                           min="0" 
                                           step="0.01" 
                                           class="rental-deposit-input"
                                           style="width: 100%; padding: 4px; border: 1px solid #ddd; border-radius: 3px; font-size: 12px;" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" style="padding: 8px 0;">
                                    <button type="button" 
                                            class="button button-primary rental-check-availability" 
                                            data-item-id="<?php echo $item_id; ?>" 
                                            data-product-id="<?php echo $product_id; ?>"
                                            style="margin-right: 5px; font-size: 11px; padding: 4px 8px;">
                                        <span class="dashicons dashicons-search" style="vertical-align: middle; margin-right: 2px; font-size: 12px;"></span>
                                        <?php _e( 'Check & Update', 'smart-rentals-wc' ); ?>
                                    </button>
                                    <button type="button" 
                                            class="button button-secondary rental-save-item" 
                                            data-item-id="<?php echo $item_id; ?>"
                                            style="margin-right: 5px; font-size: 11px; padding: 4px 8px;">
                                        <span class="dashicons dashicons-yes" style="vertical-align: middle; margin-right: 2px; font-size: 12px;"></span>
                                        <?php _e( 'Save', 'smart-rentals-wc' ); ?>
                                    </button>
                                    <button type="button" 
                                            class="button button-secondary rental-edit-toggle" 
                                            data-item-id="<?php echo $item_id; ?>"
                                            style="font-size: 11px; padding: 4px 8px;">
                                        <span class="dashicons dashicons-no" style="vertical-align: middle; margin-right: 2px; font-size: 12px;"></span>
                                        <?php _e( 'Cancel', 'smart-rentals-wc' ); ?>
                                    </button>
                                </td>
                            </tr>
                        </table>
                        
                        <div class="rental-availability-result" style="margin-top: 8px; font-size: 11px;"></div>
                    </div>
                </div>
            </td>
            <?php
        }

        /**
         * Save rental data from meta box
         */
        public function save_rental_data( $post_id ) {
            // Check if this is an order
            if ( get_post_type( $post_id ) !== 'shop_order' ) {
                return;
            }
            
            // Verify nonce
            if ( !isset( $_POST['smart_rentals_edit_nonce'] ) || !wp_verify_nonce( $_POST['smart_rentals_edit_nonce'], 'smart_rentals_edit_rental_data' ) ) {
                return;
            }
            
            // Check permissions
            if ( !current_user_can( 'edit_shop_orders' ) ) {
                return;
            }
            
            // Check if rental data was submitted
            if ( !isset( $_POST['rental_data'] ) || !is_array( $_POST['rental_data'] ) ) {
                return;
            }
            
            $order = wc_get_order( $post_id );
            if ( !$order ) {
                return;
            }
            
            $changes_made = false;
            $modified_items = [];
            $total_price_change = 0;
            $old_total = $order->get_total();
            
            foreach ( $_POST['rental_data'] as $item_id => $rental_data ) {
                $item = $order->get_item( $item_id );
                if ( !$item || !$this->is_rental_item( $item ) ) {
                    continue;
                }
                
                $new_pickup = sanitize_text_field( $rental_data['pickup_date'] ?? '' );
                $new_dropoff = sanitize_text_field( $rental_data['dropoff_date'] ?? '' );
                $new_quantity = intval( $rental_data['quantity'] ?? 1 );
                
                // Get original values for comparison
                $original_pickup = sanitize_text_field( $rental_data['original_pickup'] ?? '' );
                $original_dropoff = sanitize_text_field( $rental_data['original_dropoff'] ?? '' );
                $original_quantity = intval( $rental_data['original_quantity'] ?? 1 );
                
                // Skip if no changes were made
                if ( $new_pickup === $original_pickup && $new_dropoff === $original_dropoff && $new_quantity === $original_quantity ) {
                    continue;
                }
                
                if ( !$new_pickup || !$new_dropoff ) {
                    $order->add_order_note( sprintf( 
                        __( 'Failed to update rental details for %s: Missing pickup or dropoff date', 'smart-rentals-wc' ),
                        $item->get_name()
                    ) );
                    continue;
                }
                
                $pickup_timestamp = strtotime( $new_pickup );
                $dropoff_timestamp = strtotime( $new_dropoff );
                
                if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
                    $order->add_order_note( sprintf( 
                        __( 'Failed to update rental details for %s: Invalid date range', 'smart-rentals-wc' ),
                        $item->get_name()
                    ) );
                    continue;
                }
                
                $product_id = $item->get_product_id();
                
                // Check availability
                $available = $this->check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $new_quantity, $post_id );
                
                if ( $available ) {
                    // Get old values for comparison
                    $old_pickup = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
                    $old_dropoff = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
                    $old_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: 1;
                    $old_price = $item->get_total();
                    
                    // Update rental data
                    $item->update_meta_data( smart_rentals_wc_meta_key( 'pickup_date' ), date( 'Y-m-d H:i:s', $pickup_timestamp ) );
                    $item->update_meta_data( smart_rentals_wc_meta_key( 'dropoff_date' ), date( 'Y-m-d H:i:s', $dropoff_timestamp ) );
                    $item->update_meta_data( smart_rentals_wc_meta_key( 'rental_quantity' ), $new_quantity );
                    
                    // Add is_rental flag if missing (for old orders)
                    if ( !$item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) ) ) {
                        $item->update_meta_data( smart_rentals_wc_meta_key( 'is_rental' ), 'yes' );
                    }
                    
                    // Update duration text
                    $duration_text = Smart_Rentals_WC()->options->get_rental_duration_text( $pickup_timestamp, $dropoff_timestamp );
                    $item->update_meta_data( smart_rentals_wc_meta_key( 'duration_text' ), $duration_text );
                    
                    // Update item quantity
                    $item->set_quantity( $new_quantity );
                    
                    // Recalculate price
                    $new_price = Smart_Rentals_WC()->options->calculate_rental_price( 
                        $product_id, 
                        $pickup_timestamp, 
                        $dropoff_timestamp, 
                        $new_quantity 
                    );
                    
                    // Add security deposit
                    $security_deposit = smart_rentals_wc_get_security_deposit( $product_id );
                    $total_with_deposit = $new_price + ( $security_deposit * $new_quantity );
                    
                    if ( $total_with_deposit > 0 ) {
                        $item->set_subtotal( $total_with_deposit );
                        $item->set_total( $total_with_deposit );
                    }
                    
                    $item->save();
                    
                    // Update custom booking table
                    $this->update_custom_booking_table( $post_id, $item_id, $pickup_timestamp, $dropoff_timestamp, $new_quantity );
                    
                    // Track changes for audit trail
                    $changes = [];
                    if ( $old_pickup !== date( 'Y-m-d H:i:s', $pickup_timestamp ) ) {
                        $changes[] = sprintf( 'Pickup: %s → %s', 
                            $old_pickup ? date( 'M j, Y g:i A', strtotime( $old_pickup ) ) : 'Not set',
                            date( 'M j, Y g:i A', $pickup_timestamp )
                        );
                    }
                    if ( $old_dropoff !== date( 'Y-m-d H:i:s', $dropoff_timestamp ) ) {
                        $changes[] = sprintf( 'Dropoff: %s → %s', 
                            $old_dropoff ? date( 'M j, Y g:i A', strtotime( $old_dropoff ) ) : 'Not set',
                            date( 'M j, Y g:i A', $dropoff_timestamp )
                        );
                    }
                    if ( intval( $old_quantity ) !== $new_quantity ) {
                        $changes[] = sprintf( 'Quantity: %d → %d', intval( $old_quantity ), $new_quantity );
                    }
                    
                    if ( !empty( $changes ) ) {
                        // Create detailed order note
                        $order->add_order_note( sprintf( 
                            __( 'Rental details updated for %s: %s. Price: %s → %s', 'smart-rentals-wc' ),
                            $item->get_name(),
                            implode( ', ', $changes ),
                            smart_rentals_wc_price( $old_price ),
                            smart_rentals_wc_price( $total_with_deposit )
                        ) );
                        
                        // Track modified items for customer notification
                        $modified_items[] = [
                            'item_name' => $item->get_name(),
                            'changes' => $changes,
                            'old_price' => $old_price,
                            'new_price' => $total_with_deposit,
                            'pickup_date' => date( 'M j, Y g:i A', $pickup_timestamp ),
                            'dropoff_date' => date( 'M j, Y g:i A', $dropoff_timestamp ),
                            'quantity' => $new_quantity
                        ];
                        
                        $total_price_change += ( $total_with_deposit - $old_price );
                        $changes_made = true;
                    }
                    
                } else {
                    $order->add_order_note( sprintf( 
                        __( 'Failed to update rental details for %s: Product not available for selected dates/quantity', 'smart-rentals-wc' ),
                        $item->get_name()
                    ) );
                }
            }
            
            // Recalculate order totals if changes were made
            if ( $changes_made ) {
                $order->calculate_totals();
                $order->save();
                
                $new_total = $order->get_total();
                $total_change = $new_total - $old_total;
                
                // Add summary order note
                $order->add_order_note( sprintf( 
                    __( 'Order totals recalculated after rental details update. Total change: %s', 'smart-rentals-wc' ),
                    $total_change >= 0 ? '+' . smart_rentals_wc_price( $total_change ) : smart_rentals_wc_price( $total_change )
                ) );
                
                // Send customer notification if enabled
                $this->send_customer_notification( $order, $modified_items, $total_change );
                
                // Log modification for audit trail
                $this->log_order_modification( $order, $modified_items, $total_change );
            }
        }

        /**
         * Save order items - Handle rental data updates
         */
        public function saved_order_items( $order_id, $items ) {
            // Debug log
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                smart_rentals_wc_log( 'saved_order_items called for order #' . $order_id );
            }

            if ( !isset( $_POST['rental_pickup_date'] ) && !isset( $_POST['rental_dropoff_date'] ) && !isset( $_POST['rental_quantity'] ) ) {
                return; // No rental data to update
            }

            $order = wc_get_order( $order_id );
            if ( !$order ) {
                return;
            }

            $changes_made = false;

            foreach ( $items['order_item_id'] as $item_id ) {
                $item = $order->get_item( $item_id );
                if ( !$item ) {
                    continue;
                }

                // Check if this item has rental data to update (support old and new orders)
                $is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
                $existing_pickup = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
                $existing_dropoff = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
                $existing_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) );
                
                // Item is rental if it has the rental flag OR has rental dates
                $is_rental_item = ( $is_rental === 'yes' || ( $existing_pickup && $existing_dropoff ) || $existing_quantity );
                
                if ( !$is_rental_item ) {
                    continue;
                }

                // Get current rental data
                $current_pickup = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
                $current_dropoff = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
                $current_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: 1;

                // Get new rental data
                $new_pickup = isset( $_POST['rental_pickup_date'][$item_id] ) ? sanitize_text_field( $_POST['rental_pickup_date'][$item_id] ) : '';
                $new_dropoff = isset( $_POST['rental_dropoff_date'][$item_id] ) ? sanitize_text_field( $_POST['rental_dropoff_date'][$item_id] ) : '';
                $new_quantity = isset( $_POST['rental_quantity'][$item_id] ) ? intval( $_POST['rental_quantity'][$item_id] ) : $current_quantity;

                // Check if any changes were made
                $pickup_changed = ( $new_pickup && $new_pickup !== $current_pickup );
                $dropoff_changed = ( $new_dropoff && $new_dropoff !== $current_dropoff );
                $quantity_changed = ( $new_quantity !== intval( $current_quantity ) );

                if ( !$pickup_changed && !$dropoff_changed && !$quantity_changed ) {
                    continue; // No changes for this item
                }

                // Debug log
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    smart_rentals_wc_log( sprintf( 
                        'Processing rental changes for item #%d: pickup_changed=%s, dropoff_changed=%s, quantity_changed=%s',
                        $item_id,
                        $pickup_changed ? 'yes' : 'no',
                        $dropoff_changed ? 'yes' : 'no',
                        $quantity_changed ? 'yes' : 'no'
                    ) );
                }

                // Use new dates if provided, otherwise keep current
                $final_pickup = $new_pickup ?: $current_pickup;
                $final_dropoff = $new_dropoff ?: $current_dropoff;

                if ( $final_pickup && $final_dropoff ) {
                    // Validate dates
                    $pickup_timestamp = strtotime( $final_pickup );
                    $dropoff_timestamp = strtotime( $final_dropoff );

                    if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
                        $order->add_order_note( sprintf( 
                            __( 'Failed to update rental details for %s: Invalid date range', 'smart-rentals-wc' ),
                            $item->get_name()
                        ) );
                        continue;
                    }

                    // Get product ID for availability checking
                    $product_id = $item->get_product_id();

                    // Check availability for new dates (excluding current booking)
                    $available = $this->check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $new_quantity, $order_id );

                    if ( $available ) {
                        // Store old values for comparison
                        $old_pickup = $current_pickup;
                        $old_dropoff = $current_dropoff;
                        $old_quantity = $current_quantity;

                        // Update rental data
                        $item->update_meta_data( smart_rentals_wc_meta_key( 'pickup_date' ), date( 'Y-m-d H:i:s', $pickup_timestamp ) );
                        $item->update_meta_data( smart_rentals_wc_meta_key( 'dropoff_date' ), date( 'Y-m-d H:i:s', $dropoff_timestamp ) );
                        $item->update_meta_data( smart_rentals_wc_meta_key( 'rental_quantity' ), $new_quantity );

                        // Update duration text
                        $duration_text = Smart_Rentals_WC()->options->get_rental_duration_text( $pickup_timestamp, $dropoff_timestamp );
                        $item->update_meta_data( smart_rentals_wc_meta_key( 'duration_text' ), $duration_text );

                        // Update item quantity if changed
                        if ( $new_quantity != $item->get_quantity() ) {
                            $item->set_quantity( $new_quantity );
                        }

                        // Recalculate price based on new dates and quantity
                        $new_price = Smart_Rentals_WC()->options->calculate_rental_price( 
                            $product_id, 
                            $pickup_timestamp, 
                            $dropoff_timestamp, 
                            $new_quantity 
                        );

                        // Add security deposit to the price
                        $security_deposit = smart_rentals_wc_get_security_deposit( $product_id );
                        $total_with_deposit = $new_price + ( $security_deposit * $new_quantity );

                        if ( $total_with_deposit > 0 ) {
                            $unit_price = $total_with_deposit / $new_quantity;
                            $item->set_subtotal( $total_with_deposit );
                            $item->set_total( $total_with_deposit );
                        }

                        $item->save();

                        // Update custom bookings table if exists
                        $this->update_custom_booking_table( $order_id, $item_id, $pickup_timestamp, $dropoff_timestamp, $new_quantity );

                        // Create detailed order note
                        $changes = [];
                        if ( $pickup_changed ) {
                            $changes[] = sprintf( 'Pickup: %s → %s', 
                                $old_pickup ? date( 'Y-m-d H:i', strtotime( $old_pickup ) ) : 'Not set',
                                date( 'Y-m-d H:i', $pickup_timestamp )
                            );
                        }
                        if ( $dropoff_changed ) {
                            $changes[] = sprintf( 'Dropoff: %s → %s', 
                                $old_dropoff ? date( 'Y-m-d H:i', strtotime( $old_dropoff ) ) : 'Not set',
                                date( 'Y-m-d H:i', $dropoff_timestamp )
                            );
                        }
                        if ( $quantity_changed ) {
                            $changes[] = sprintf( 'Quantity: %d → %d', $old_quantity, $new_quantity );
                        }

                        $order->add_order_note( sprintf( 
                            __( 'Rental details updated for %s: %s. New price: %s', 'smart-rentals-wc' ),
                            $item->get_name(),
                            implode( ', ', $changes ),
                            smart_rentals_wc_price( $total_with_deposit )
                        ) );

                        $changes_made = true;

                        // Debug log
                        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                            smart_rentals_wc_log( sprintf( 
                                'Successfully updated rental item #%d: %s',
                                $item_id,
                                implode( ', ', $changes )
                            ) );
                        }

                    } else {
                        // Add error notice
                        $order->add_order_note( sprintf( 
                            __( 'Failed to update rental details for %s: Product not available for selected dates/quantity (%s to %s, Qty: %d)', 'smart-rentals-wc' ),
                            $item->get_name(),
                            date( 'Y-m-d H:i', $pickup_timestamp ),
                            date( 'Y-m-d H:i', $dropoff_timestamp ),
                            $new_quantity
                        ) );

                        // Debug log
                        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                            smart_rentals_wc_log( sprintf( 
                                'Failed to update rental item #%d: Availability conflict',
                                $item_id
                            ) );
                        }
                    }
                } else {
                    $order->add_order_note( sprintf( 
                        __( 'Failed to update rental details for %s: Missing or invalid dates', 'smart-rentals-wc' ),
                        $item->get_name()
                    ) );
                }
            }

            // Recalculate order totals if any changes were made
            if ( $changes_made ) {
                $order->calculate_totals();
                $order->save();

                // Add success notice
                $order->add_order_note( __( 'Order totals recalculated after rental details update', 'smart-rentals-wc' ) );
            }
        }

        /**
         * Check availability excluding current order
         */
        public function check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity, $exclude_order_id ) {
            global $wpdb;

            $rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
            $total_stock = $rental_stock ? intval( $rental_stock ) : 1;

            $booked_quantity = 0;

            // Check custom bookings table (excluding current order)
            $table_name = $wpdb->prefix . 'smart_rentals_bookings';
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
                $bookings = $wpdb->get_results( $wpdb->prepare("
                    SELECT pickup_date, dropoff_date, quantity
                    FROM $table_name
                    WHERE product_id = %d
                    AND order_id != %d
                    AND status IN ('pending', 'confirmed', 'active', 'processing', 'completed')
                ", $product_id, $exclude_order_id ));

                foreach ( $bookings as $booking ) {
                    $booking_pickup = strtotime( $booking->pickup_date );
                    $booking_dropoff = strtotime( $booking->dropoff_date );
                    
                    // Get turnaround time
                    $turnaround_hours = smart_rentals_wc_get_turnaround_time( $product_id );
                    $turnaround_seconds = $turnaround_hours * 3600;
                    $booking_dropoff_with_turnaround = $booking_dropoff + $turnaround_seconds;
                    
                    // Check overlap
                    if ( $booking_pickup < $dropoff_timestamp && $booking_dropoff_with_turnaround > $pickup_timestamp ) {
                        $booked_quantity += intval( $booking->quantity );
                    }
                }
            }

            // Also check WooCommerce orders (excluding current order)
            $order_status = Smart_Rentals_WC()->options->get_booking_order_status();
            $status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );

            $order_bookings = $wpdb->get_results( $wpdb->prepare("
                SELECT 
                    pickup_date.meta_value as pickup_date,
                    dropoff_date.meta_value as dropoff_date,
                    quantity.meta_value as quantity
                FROM {$wpdb->prefix}woocommerce_order_items AS items
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pickup_date 
                    ON items.order_item_id = pickup_date.order_item_id 
                    AND pickup_date.meta_key = %s
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS dropoff_date 
                    ON items.order_item_id = dropoff_date.order_item_id 
                    AND dropoff_date.meta_key = %s
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS quantity 
                    ON items.order_item_id = quantity.order_item_id 
                    AND quantity.meta_key = %s
                LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta 
                    ON items.order_item_id = product_meta.order_item_id 
                    AND product_meta.meta_key = '_product_id'
                LEFT JOIN {$wpdb->posts} AS orders 
                    ON items.order_id = orders.ID
                WHERE 
                    product_meta.meta_value = %d
                    AND orders.ID != %d
                    AND orders.post_status IN ('{$status_placeholders}')
                    AND pickup_date.meta_value IS NOT NULL
                    AND dropoff_date.meta_value IS NOT NULL
            ",
                smart_rentals_wc_meta_key( 'pickup_date' ),
                smart_rentals_wc_meta_key( 'dropoff_date' ),
                smart_rentals_wc_meta_key( 'rental_quantity' ),
                $product_id,
                $exclude_order_id
            ));

            foreach ( $order_bookings as $booking ) {
                if ( $booking->pickup_date && $booking->dropoff_date ) {
                    $booking_pickup = strtotime( $booking->pickup_date );
                    $booking_dropoff = strtotime( $booking->dropoff_date );
                    
                    // Get turnaround time
                    $turnaround_hours = smart_rentals_wc_get_turnaround_time( $product_id );
                    $turnaround_seconds = $turnaround_hours * 3600;
                    $booking_dropoff_with_turnaround = $booking_dropoff + $turnaround_seconds;
                    
                    // Check overlap
                    if ( $booking_pickup < $dropoff_timestamp && $booking_dropoff_with_turnaround > $pickup_timestamp ) {
                        $booked_quantity += intval( $booking->quantity ?: 1 );
                    }
                }
            }

            $available_quantity = max( 0, $total_stock - $booked_quantity );
            return $available_quantity >= $quantity;
        }

        /**
         * Send customer notification for order modifications
         */
        private function send_customer_notification( $order, $modified_items, $total_change ) {
            // Check if notifications are enabled
            $notify_customer = smart_rentals_wc_get_option( 'notify_customer_on_modification', 'yes' );
            if ( $notify_customer !== 'yes' ) {
                return;
            }
            
            if ( empty( $modified_items ) ) {
                return;
            }
            
            $customer_email = $order->get_billing_email();
            if ( !$customer_email ) {
                return;
            }
            
            $order_number = $order->get_order_number();
            $site_name = get_bloginfo( 'name' );
            
            // Prepare email content
            $subject = sprintf( __( 'Your rental order #%s has been updated', 'smart-rentals-wc' ), $order_number );
            
            $message = sprintf( __( 'Hello %s,', 'smart-rentals-wc' ), $order->get_billing_first_name() ) . "\n\n";
            $message .= sprintf( __( 'Your rental order #%s has been updated by our team. Here are the details of the changes:', 'smart-rentals-wc' ), $order_number ) . "\n\n";
            
            foreach ( $modified_items as $item ) {
                $message .= sprintf( __( 'Product: %s', 'smart-rentals-wc' ), $item['item_name'] ) . "\n";
                $message .= implode( "\n", $item['changes'] ) . "\n";
                $message .= sprintf( __( 'New pickup date: %s', 'smart-rentals-wc' ), $item['pickup_date'] ) . "\n";
                $message .= sprintf( __( 'New dropoff date: %s', 'smart-rentals-wc' ), $item['dropoff_date'] ) . "\n";
                $message .= sprintf( __( 'Quantity: %d', 'smart-rentals-wc' ), $item['quantity'] ) . "\n";
                $message .= sprintf( __( 'Price: %s → %s', 'smart-rentals-wc' ), 
                    smart_rentals_wc_price( $item['old_price'] ), 
                    smart_rentals_wc_price( $item['new_price'] ) 
                ) . "\n\n";
            }
            
            if ( $total_change != 0 ) {
                $message .= sprintf( __( 'Total order change: %s', 'smart-rentals-wc' ), 
                    $total_change >= 0 ? '+' . smart_rentals_wc_price( $total_change ) : smart_rentals_wc_price( $total_change )
                ) . "\n\n";
            }
            
            $message .= __( 'If you have any questions about these changes, please contact us.', 'smart-rentals-wc' ) . "\n\n";
            $message .= sprintf( __( 'Thank you for choosing %s!', 'smart-rentals-wc' ), $site_name );
            
            // Send email
            $headers = [ 'Content-Type: text/plain; charset=UTF-8' ];
            wp_mail( $customer_email, $subject, $message, $headers );
            
            // Add order note about notification
            $order->add_order_note( sprintf( 
                __( 'Customer notification sent to %s about order modifications', 'smart-rentals-wc' ),
                $customer_email
            ) );
        }
        
        /**
         * Log order modification for audit trail
         */
        private function log_order_modification( $order, $modified_items, $total_change ) {
            global $wpdb;
            
            $table_name = $wpdb->prefix . 'smart_rentals_order_modifications';
            
            // Create table if it doesn't exist
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
                $charset_collate = $wpdb->get_charset_collate();
                
                $sql = "CREATE TABLE $table_name (
                    id bigint(20) NOT NULL AUTO_INCREMENT,
                    order_id bigint(20) NOT NULL,
                    modified_by bigint(20) NOT NULL,
                    modified_at datetime DEFAULT CURRENT_TIMESTAMP,
                    changes longtext NOT NULL,
                    total_change decimal(10,2) DEFAULT 0,
                    PRIMARY KEY (id),
                    KEY order_id (order_id),
                    KEY modified_by (modified_by),
                    KEY modified_at (modified_at)
                ) $charset_collate;";
                
                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
            }
            
            // Insert modification record
            $wpdb->insert(
                $table_name,
                [
                    'order_id' => $order->get_id(),
                    'modified_by' => get_current_user_id(),
                    'modified_at' => current_time( 'mysql' ),
                    'changes' => json_encode( $modified_items ),
                    'total_change' => $total_change
                ],
                [ '%d', '%d', '%s', '%s', '%f' ]
            );
        }
        
        /**
         * Update custom booking table
         */
        private function update_custom_booking_table( $order_id, $item_id, $pickup_timestamp, $dropoff_timestamp, $quantity ) {
            global $wpdb;

            $table_name = $wpdb->prefix . 'smart_rentals_bookings';
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
                // Update existing booking record
                $wpdb->update(
                    $table_name,
                    [
                        'pickup_date' => gmdate( 'Y-m-d H:i:s', $pickup_timestamp ),
                        'dropoff_date' => gmdate( 'Y-m-d H:i:s', $dropoff_timestamp ),
                        'quantity' => $quantity,
                    ],
                    [
                        'order_id' => $order_id,
                    ],
                    [
                        '%s', // pickup_date
                        '%s', // dropoff_date
                        '%d', // quantity
                    ],
                    [
                        '%d', // order_id
                    ]
                );
            }
        }

        /**
         * Enqueue admin scripts
         */
        public function admin_scripts( $hook ) {
            // Check if we're on the order edit screen
            if ( 'post.php' !== $hook && 'edit.php' !== $hook ) {
                return;
            }

            global $post;
            if ( !$post || $post->post_type !== 'shop_order' ) {
                return;
            }

            // Always enqueue for order edit pages (we'll check for rental items in JS)
            wp_enqueue_script( 
                'smart-rentals-order-edit', 
                SMART_RENTALS_WC_PLUGIN_URI . 'assets/js/admin-order-edit.js', 
                [ 'jquery' ], 
                SMART_RENTALS_WC_VERSION, 
                true 
            );
            
            wp_localize_script( 'smart-rentals-order-edit', 'smartRentalsOrderEdit', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'smart-rentals-order-edit' ),
                'strings' => [
                    'checking' => __( 'Checking availability...', 'smart-rentals-wc' ),
                    'available' => __( 'Available', 'smart-rentals-wc' ),
                    'not_available' => __( 'Not available', 'smart-rentals-wc' ),
                    'error' => __( 'Error checking availability', 'smart-rentals-wc' ),
                    'edit' => __( 'Edit Rental Details', 'smart-rentals-wc' ),
                    'cancel' => __( 'Cancel Edit', 'smart-rentals-wc' ),
                ]
            ]);

            wp_enqueue_style( 
                'smart-rentals-order-edit', 
                SMART_RENTALS_WC_PLUGIN_URI . 'assets/css/admin-order-edit.css', 
                [], 
                SMART_RENTALS_WC_VERSION 
            );

            // JavaScript is handled by the external admin-order-edit.js file
        }

        /**
         * AJAX handler for updating rental item
         */
        public function ajax_update_rental_item() {
            // Verify nonce
            if ( !wp_verify_nonce( $_POST['nonce'], 'smart-rentals-order-edit' ) ) {
                wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
            }

            // Check permissions
            if ( !current_user_can( 'edit_shop_orders' ) ) {
                wp_send_json_error( [ 'message' => __( 'Insufficient permissions', 'smart-rentals-wc' ) ] );
            }

            $item_id = intval( $_POST['item_id'] );
            $pickup_date = sanitize_text_field( $_POST['pickup_date'] );
            $dropoff_date = sanitize_text_field( $_POST['dropoff_date'] );
            $quantity = intval( $_POST['quantity'] );

            if ( !$item_id || !$pickup_date || !$dropoff_date ) {
                wp_send_json_error( [ 'message' => __( 'Missing required parameters', 'smart-rentals-wc' ) ] );
            }

            $pickup_timestamp = strtotime( $pickup_date );
            $dropoff_timestamp = strtotime( $dropoff_date );

            if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
                wp_send_json_error( [ 'message' => __( 'Invalid date range', 'smart-rentals-wc' ) ] );
            }

            // Get order item
            global $wpdb;
            $order_item = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d",
                $item_id
            ) );

            if ( !$order_item ) {
                wp_send_json_error( [ 'message' => __( 'Order item not found', 'smart-rentals-wc' ) ] );
            }

            $order = wc_get_order( $order_item->order_id );
            if ( !$order ) {
                wp_send_json_error( [ 'message' => __( 'Order not found', 'smart-rentals-wc' ) ] );
            }

            $item = $order->get_item( $item_id );
            if ( !$item ) {
                wp_send_json_error( [ 'message' => __( 'Order item not found', 'smart-rentals-wc' ) ] );
            }

            // Check availability
            $product_id = $item->get_product_id();
            $available = $this->check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity, $order->get_id() );

            if ( !$available ) {
                wp_send_json_error( [ 'message' => __( 'Product not available for selected dates and quantity', 'smart-rentals-wc' ) ] );
            }

            // Update rental data
            $item->update_meta_data( smart_rentals_wc_meta_key( 'pickup_date' ), date( 'Y-m-d H:i:s', $pickup_timestamp ) );
            $item->update_meta_data( smart_rentals_wc_meta_key( 'dropoff_date' ), date( 'Y-m-d H:i:s', $dropoff_timestamp ) );
            $item->update_meta_data( smart_rentals_wc_meta_key( 'rental_quantity' ), $quantity );

            // Recalculate price
            $new_price = Smart_Rentals_WC()->options->calculate_rental_price( 
                $product_id, 
                $pickup_timestamp, 
                $dropoff_timestamp, 
                $quantity 
            );

            // Add security deposit
            $security_deposit = smart_rentals_wc_get_security_deposit( $product_id );
            $total_with_deposit = $new_price + ( $security_deposit * $quantity );

            if ( $total_with_deposit > 0 ) {
                $item->set_subtotal( $total_with_deposit );
                $item->set_total( $total_with_deposit );
            }

            $item->save();

            // Update custom booking table
            $this->update_custom_booking_table( $order->get_id(), $item_id, $pickup_timestamp, $dropoff_timestamp, $quantity );

            // Recalculate order totals
            $order->calculate_totals();
            $order->save();

            wp_send_json_success( [
                'message' => __( 'Rental item updated successfully', 'smart-rentals-wc' ),
                'new_price' => $total_with_deposit,
                'formatted_price' => smart_rentals_wc_price( $total_with_deposit )
            ] );
        }

        /**
         * AJAX handler for saving order item rental data
         */
        public function ajax_save_order_item_rental_data() {
            // Verify nonce
            if ( !wp_verify_nonce( $_POST['nonce'], 'smart-rentals-order-edit' ) ) {
                wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
            }

            // Check permissions
            if ( !current_user_can( 'edit_shop_orders' ) ) {
                wp_send_json_error( [ 'message' => __( 'Insufficient permissions', 'smart-rentals-wc' ) ] );
            }

            $item_id = intval( $_POST['item_id'] );
            $pickup_date = sanitize_text_field( $_POST['pickup_date'] ?? '' );
            $dropoff_date = sanitize_text_field( $_POST['dropoff_date'] ?? '' );
            $quantity = intval( $_POST['quantity'] ?? 1 );
            $security_deposit = floatval( $_POST['security_deposit'] ?? 0 );

            if ( !$item_id ) {
                wp_send_json_error( [ 'message' => __( 'Missing item ID', 'smart-rentals-wc' ) ] );
            }

            // Get order item
            global $wpdb;
            $order_item = $wpdb->get_row( $wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}woocommerce_order_items WHERE order_item_id = %d",
                $item_id
            ) );

            if ( !$order_item ) {
                wp_send_json_error( [ 'message' => __( 'Order item not found', 'smart-rentals-wc' ) ] );
            }

            $order = wc_get_order( $order_item->order_id );
            if ( !$order ) {
                wp_send_json_error( [ 'message' => __( 'Order not found', 'smart-rentals-wc' ) ] );
            }

            $item = $order->get_item( $item_id );
            if ( !$item ) {
                wp_send_json_error( [ 'message' => __( 'Order item not found', 'smart-rentals-wc' ) ] );
            }

            // Validate dates if provided
            if ( $pickup_date && $dropoff_date ) {
                $pickup_timestamp = strtotime( $pickup_date );
                $dropoff_timestamp = strtotime( $dropoff_date );

                if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
                    wp_send_json_error( [ 'message' => __( 'Invalid date range', 'smart-rentals-wc' ) ] );
                }

                // Check availability
                $product_id = $item->get_product_id();
                $available = $this->check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity, $order->get_id() );

                if ( !$available ) {
                    wp_send_json_error( [ 'message' => __( 'Product not available for selected dates and quantity', 'smart-rentals-wc' ) ] );
                }

                // Update rental data
                $item->update_meta_data( smart_rentals_wc_meta_key( 'pickup_date' ), date( 'Y-m-d H:i:s', $pickup_timestamp ) );
                $item->update_meta_data( smart_rentals_wc_meta_key( 'dropoff_date' ), date( 'Y-m-d H:i:s', $dropoff_timestamp ) );
                
                // Update duration text
                $duration_text = Smart_Rentals_WC()->options->get_rental_duration_text( $pickup_timestamp, $dropoff_timestamp );
                $item->update_meta_data( smart_rentals_wc_meta_key( 'duration_text' ), $duration_text );

                // Recalculate price
                $new_price = Smart_Rentals_WC()->options->calculate_rental_price( 
                    $product_id, 
                    $pickup_timestamp, 
                    $dropoff_timestamp, 
                    $quantity 
                );

                // Add security deposit
                $total_with_deposit = $new_price + ( $security_deposit * $quantity );

                if ( $total_with_deposit > 0 ) {
                    $item->set_subtotal( $total_with_deposit );
                    $item->set_total( $total_with_deposit );
                }

                // Update custom booking table
                $this->update_custom_booking_table( $order->get_id(), $item_id, $pickup_timestamp, $dropoff_timestamp, $quantity );
            }

            // Update quantity
            $item->set_quantity( $quantity );
            $item->update_meta_data( smart_rentals_wc_meta_key( 'rental_quantity' ), $quantity );

            // Update security deposit
            $item->update_meta_data( smart_rentals_wc_meta_key( 'security_deposit' ), $security_deposit );

            // Add is_rental flag if missing
            if ( !$item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) ) ) {
                $item->update_meta_data( smart_rentals_wc_meta_key( 'is_rental' ), 'yes' );
            }

            $item->save();

            // Recalculate order totals
            $order->calculate_totals();
            $order->save();

            // Add order note
            $order->add_order_note( sprintf( 
                __( 'Rental details updated for %s via order edit', 'smart-rentals-wc' ),
                $item->get_name()
            ) );

            wp_send_json_success( [
                'message' => __( 'Rental item saved successfully', 'smart-rentals-wc' ),
                'new_price' => $item->get_total(),
                'formatted_price' => smart_rentals_wc_price( $item->get_total() )
            ] );
        }
    }
}