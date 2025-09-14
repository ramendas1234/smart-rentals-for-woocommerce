<?php
/**
 * Smart Rentals WC Order Edit V2
 * Enhanced order edit functionality with reliable JavaScript
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Order_Edit_V2' ) ) {

    class Smart_Rentals_WC_Order_Edit_V2 {

        /**
         * Constructor
         */
        public function __construct() {
            // Add custom meta box to order edit screen
            add_action( 'add_meta_boxes', [ $this, 'add_rental_edit_meta_box' ] );
            
            // Enqueue admin scripts
            add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
            
            // AJAX handlers
            add_action( 'wp_ajax_smart_rentals_get_order_rental_data', [ $this, 'ajax_get_order_rental_data' ] );
            add_action( 'wp_ajax_smart_rentals_save_order_rental_data', [ $this, 'ajax_save_order_rental_data' ] );
            add_action( 'wp_ajax_smart_rentals_check_rental_availability', [ $this, 'ajax_check_rental_availability' ] );
        }

        /**
         * Add rental edit meta box
         */
        public function add_rental_edit_meta_box() {
            global $post;
            
            // Debug logging
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'Smart Rentals V2: add_rental_edit_meta_box called' );
                error_log( 'Post: ' . ( $post ? $post->ID . ' (' . $post->post_type . ')' : 'null' ) );
            }
            
            if ( !$post || $post->post_type !== 'shop_order' ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( 'Smart Rentals V2: Not a shop order, skipping' );
                }
                return;
            }
            
            $order = wc_get_order( $post->ID );
            if ( !$order ) {
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( 'Smart Rentals V2: Order not found for post ' . $post->ID );
                }
                return;
            }
            
            // Check if order has rental items
            $has_rental_items = false;
            $rental_items_found = [];
            
            foreach ( $order->get_items() as $item_id => $item ) {
                $is_rental = $this->is_rental_item( $item );
                if ( $is_rental ) {
                    $has_rental_items = true;
                    $rental_items_found[] = [
                        'item_id' => $item_id,
                        'name' => $item->get_name(),
                        'is_rental' => $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) ),
                        'pickup_date' => $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) ),
                        'dropoff_date' => $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) ),
                        'rental_quantity' => $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ),
                    ];
                }
            }
            
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'Smart Rentals V2: Has rental items: ' . ( $has_rental_items ? 'YES' : 'NO' ) );
                error_log( 'Smart Rentals V2: Rental items found: ' . print_r( $rental_items_found, true ) );
            }
            
            // Always add a test meta box first to verify the system works
            add_meta_box(
                'smart-rentals-test',
                __( 'Smart Rentals Debug Test', 'smart-rentals-wc' ),
                [ $this, 'test_meta_box_content' ],
                'shop_order',
                'normal',
                'high'
            );
            
            if ( $has_rental_items ) {
                add_meta_box(
                    'smart-rentals-edit-v2',
                    __( 'Rental Order Management', 'smart-rentals-wc' ),
                    [ $this, 'rental_edit_meta_box_content' ],
                    'shop_order',
                    'normal',
                    'high'
                );
                
                if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                    error_log( 'Smart Rentals V2: Meta box added successfully' );
                }
            }
        }

        /**
         * Test meta box content for debugging
         */
        public function test_meta_box_content( $post ) {
            $order = wc_get_order( $post->ID );
            if ( !$order ) {
                echo '<p>Order not found</p>';
                return;
            }
            
            echo '<div style="padding: 20px; background: #f0f0f0; border: 1px solid #ccc;">';
            echo '<h3>Smart Rentals Debug Information</h3>';
            echo '<p><strong>Order ID:</strong> ' . $order->get_id() . '</p>';
            echo '<p><strong>Order Status:</strong> ' . $order->get_status() . '</p>';
            echo '<p><strong>Total Items:</strong> ' . count( $order->get_items() ) . '</p>';
            
            echo '<h4>Order Items:</h4>';
            echo '<ul>';
            foreach ( $order->get_items() as $item_id => $item ) {
                echo '<li>';
                echo '<strong>' . $item->get_name() . '</strong> (ID: ' . $item_id . ')<br>';
                echo 'is_rental: ' . $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) ) . '<br>';
                echo 'pickup_date: ' . $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) ) . '<br>';
                echo 'dropoff_date: ' . $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) ) . '<br>';
                echo 'rental_quantity: ' . $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) . '<br>';
                echo 'Is rental item: ' . ( $this->is_rental_item( $item ) ? 'YES' : 'NO' );
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        }

        /**
         * Check if item is a rental item
         */
        private function is_rental_item( $item ) {
            $is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
            $pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
            $dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
            $rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) );
            
            $result = ( $is_rental === 'yes' || ( $pickup_date && $dropoff_date ) || $rental_quantity );
            
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'Smart Rentals V2: Checking item - ' . $item->get_name() );
                error_log( 'Smart Rentals V2: is_rental=' . $is_rental . ', pickup=' . $pickup_date . ', dropoff=' . $dropoff_date . ', qty=' . $rental_quantity . ', result=' . ( $result ? 'YES' : 'NO' ) );
            }
            
            return $result;
        }

        /**
         * Rental edit meta box content
         */
        public function rental_edit_meta_box_content( $post ) {
            $order = wc_get_order( $post->ID );
            if ( !$order ) {
                return;
            }
            
            wp_nonce_field( 'smart_rentals_edit_rental_data_v2', 'smart_rentals_edit_nonce_v2' );
            
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
            <div id="smart-rentals-order-edit-v2">
                <div class="smart-rentals-header" style="margin-bottom: 20px; padding: 15px; background: #f1f1f1; border-radius: 5px;">
                    <h3 style="margin: 0 0 10px 0;"><?php _e( 'Rental Order Management', 'smart-rentals-wc' ); ?></h3>
                    <p style="margin: 0; color: #666;">
                        <?php _e( 'Modify rental details, dates, quantities, and pricing for this order.', 'smart-rentals-wc' ); ?>
                    </p>
                </div>
                
                <div id="rental-items-container">
                    <?php foreach ( $rental_items as $item_id => $item ) : ?>
                        <?php
                        $pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
                        $dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
                        $rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: 1;
                        $security_deposit = $item->get_meta( smart_rentals_wc_meta_key( 'security_deposit' ) ) ?: 0;
                        $product_id = $item->get_product_id();
                        $product = wc_get_product( $product_id );
                        $current_price = $item->get_total();
                        ?>
                        
                        <div class="rental-item-card" data-item-id="<?php echo $item_id; ?>" style="border: 1px solid #ddd; margin-bottom: 20px; border-radius: 8px; overflow: hidden; background: #fff;">
                            <div class="rental-item-header" style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <h4 style="margin: 0; color: #333;">
                                        <?php echo esc_html( $item->get_name() ); ?>
                                        <span style="font-size: 12px; color: #666; font-weight: normal;">(Item #<?php echo $item_id; ?>)</span>
                                    </h4>
                                    <div class="rental-item-actions">
                                        <button type="button" class="button button-primary rental-edit-toggle" data-item-id="<?php echo $item_id; ?>">
                                            <span class="dashicons dashicons-edit" style="vertical-align: middle; margin-right: 5px;"></span>
                                            <?php _e( 'Edit Details', 'smart-rentals-wc' ); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Display Mode -->
                            <div class="rental-display-mode" style="padding: 20px;">
                                <div class="rental-info-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                    <div class="rental-info-item">
                                        <strong><?php _e( 'Pickup Date & Time:', 'smart-rentals-wc' ); ?></strong>
                                        <div class="rental-pickup-display">
                                            <?php echo $pickup_date ? date( 'M j, Y g:i A', strtotime( $pickup_date ) ) : __( 'Not set', 'smart-rentals-wc' ); ?>
                                        </div>
                                    </div>
                                    <div class="rental-info-item">
                                        <strong><?php _e( 'Dropoff Date & Time:', 'smart-rentals-wc' ); ?></strong>
                                        <div class="rental-dropoff-display">
                                            <?php echo $dropoff_date ? date( 'M j, Y g:i A', strtotime( $dropoff_date ) ) : __( 'Not set', 'smart-rentals-wc' ); ?>
                                        </div>
                                    </div>
                                    <div class="rental-info-item">
                                        <strong><?php _e( 'Quantity:', 'smart-rentals-wc' ); ?></strong>
                                        <div class="rental-quantity-display"><?php echo esc_html( $rental_quantity ); ?></div>
                                    </div>
                                    <div class="rental-info-item">
                                        <strong><?php _e( 'Security Deposit:', 'smart-rentals-wc' ); ?></strong>
                                        <div class="rental-deposit-display">
                                            <?php echo $security_deposit > 0 ? smart_rentals_wc_price( $security_deposit ) : __( 'None', 'smart-rentals-wc' ); ?>
                                        </div>
                                    </div>
                                    <div class="rental-info-item">
                                        <strong><?php _e( 'Total Price:', 'smart-rentals-wc' ); ?></strong>
                                        <div class="rental-total-display"><?php echo smart_rentals_wc_price( $current_price ); ?></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Edit Mode -->
                            <div class="rental-edit-mode" style="display: none; padding: 20px; background: #f8f9fa; border-top: 1px solid #ddd;">
                                <form class="rental-edit-form" data-item-id="<?php echo $item_id; ?>">
                                    <div class="rental-form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px;">
                                        <div class="form-group">
                                            <label for="pickup_date_<?php echo $item_id; ?>" style="display: block; margin-bottom: 5px; font-weight: bold;">
                                                <?php _e( 'Pickup Date & Time', 'smart-rentals-wc' ); ?>
                                            </label>
                                            <input type="datetime-local" 
                                                   id="pickup_date_<?php echo $item_id; ?>"
                                                   name="pickup_date" 
                                                   value="<?php echo esc_attr( $pickup_date ? date( 'Y-m-d\TH:i', strtotime( $pickup_date ) ) : '' ); ?>" 
                                                   class="rental-date-input"
                                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="dropoff_date_<?php echo $item_id; ?>" style="display: block; margin-bottom: 5px; font-weight: bold;">
                                                <?php _e( 'Dropoff Date & Time', 'smart-rentals-wc' ); ?>
                                            </label>
                                            <input type="datetime-local" 
                                                   id="dropoff_date_<?php echo $item_id; ?>"
                                                   name="dropoff_date" 
                                                   value="<?php echo esc_attr( $dropoff_date ? date( 'Y-m-d\TH:i', strtotime( $dropoff_date ) ) : '' ); ?>" 
                                                   class="rental-date-input"
                                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="quantity_<?php echo $item_id; ?>" style="display: block; margin-bottom: 5px; font-weight: bold;">
                                                <?php _e( 'Quantity', 'smart-rentals-wc' ); ?>
                                            </label>
                                            <input type="number" 
                                                   id="quantity_<?php echo $item_id; ?>"
                                                   name="quantity" 
                                                   value="<?php echo esc_attr( $rental_quantity ); ?>" 
                                                   min="1" 
                                                   class="rental-quantity-input"
                                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="security_deposit_<?php echo $item_id; ?>" style="display: block; margin-bottom: 5px; font-weight: bold;">
                                                <?php _e( 'Security Deposit', 'smart-rentals-wc' ); ?>
                                            </label>
                                            <input type="number" 
                                                   id="security_deposit_<?php echo $item_id; ?>"
                                                   name="security_deposit" 
                                                   value="<?php echo esc_attr( $security_deposit ); ?>" 
                                                   min="0" 
                                                   step="0.01" 
                                                   class="rental-deposit-input"
                                                   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
                                        </div>
                                    </div>
                                    
                                    <div class="rental-form-actions" style="display: flex; gap: 10px; align-items: center;">
                                        <button type="button" class="button button-primary rental-check-availability" data-item-id="<?php echo $item_id; ?>" data-product-id="<?php echo $product_id; ?>">
                                            <span class="dashicons dashicons-search" style="vertical-align: middle; margin-right: 5px;"></span>
                                            <?php _e( 'Check Availability & Price', 'smart-rentals-wc' ); ?>
                                        </button>
                                        
                                        <button type="button" class="button button-success rental-save-item" data-item-id="<?php echo $item_id; ?>">
                                            <span class="dashicons dashicons-yes" style="vertical-align: middle; margin-right: 5px;"></span>
                                            <?php _e( 'Save Changes', 'smart-rentals-wc' ); ?>
                                        </button>
                                        
                                        <button type="button" class="button button-secondary rental-cancel-edit" data-item-id="<?php echo $item_id; ?>">
                                            <span class="dashicons dashicons-no" style="vertical-align: middle; margin-right: 5px;"></span>
                                            <?php _e( 'Cancel', 'smart-rentals-wc' ); ?>
                                        </button>
                                    </div>
                                    
                                    <div class="rental-feedback" style="margin-top: 15px;"></div>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <style>
            .rental-item-card {
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
                transition: box-shadow 0.3s ease;
            }
            .rental-item-card:hover {
                box-shadow: 0 4px 8px rgba(0,0,0,0.15);
            }
            .rental-info-item {
                padding: 10px;
                background: #fff;
                border-radius: 4px;
                border: 1px solid #eee;
            }
            .rental-form-actions button {
                margin-right: 10px;
            }
            .rental-feedback .notice {
                margin: 0;
            }
            </style>
            <?php
        }

        /**
         * Enqueue admin scripts
         */
        public function admin_scripts( $hook ) {
            // Check if we're on the order edit screen
            if ( 'post.php' !== $hook ) {
                return;
            }

            global $post;
            if ( !$post || $post->post_type !== 'shop_order' ) {
                return;
            }

            // Enqueue scripts
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_style( 'jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css' );
            
            wp_enqueue_script( 
                'smart-rentals-order-edit-v2', 
                SMART_RENTALS_WC_PLUGIN_URI . 'assets/js/admin-order-edit-v2.js', 
                [ 'jquery', 'jquery-ui-datepicker' ], 
                SMART_RENTALS_WC_VERSION, 
                true 
            );
            
            wp_enqueue_style( 
                'smart-rentals-order-edit-v2', 
                SMART_RENTALS_WC_PLUGIN_URI . 'assets/css/admin-order-edit-v2.css', 
                [], 
                SMART_RENTALS_WC_VERSION 
            );
            
            wp_localize_script( 'smart-rentals-order-edit-v2', 'smartRentalsOrderEditV2', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'smart-rentals-order-edit-v2' ),
                'order_id' => $post->ID,
                'strings' => [
                    'checking' => __( 'Checking availability...', 'smart-rentals-wc' ),
                    'available' => __( 'Available', 'smart-rentals-wc' ),
                    'not_available' => __( 'Not available', 'smart-rentals-wc' ),
                    'saving' => __( 'Saving changes...', 'smart-rentals-wc' ),
                    'saved' => __( 'Changes saved successfully', 'smart-rentals-wc' ),
                    'error' => __( 'An error occurred', 'smart-rentals-wc' ),
                    'edit' => __( 'Edit Details', 'smart-rentals-wc' ),
                    'cancel' => __( 'Cancel', 'smart-rentals-wc' ),
                ]
            ]);
        }

        /**
         * AJAX handler for getting order rental data
         */
        public function ajax_get_order_rental_data() {
            // Verify nonce
            if ( !wp_verify_nonce( $_POST['nonce'], 'smart-rentals-order-edit-v2' ) ) {
                wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
            }

            // Check permissions
            if ( !current_user_can( 'edit_shop_orders' ) ) {
                wp_send_json_error( [ 'message' => __( 'Insufficient permissions', 'smart-rentals-wc' ) ] );
            }

            $order_id = intval( $_POST['order_id'] );
            $order = wc_get_order( $order_id );
            
            if ( !$order ) {
                wp_send_json_error( [ 'message' => __( 'Order not found', 'smart-rentals-wc' ) ] );
            }

            $rental_items = [];
            foreach ( $order->get_items() as $item_id => $item ) {
                if ( $this->is_rental_item( $item ) ) {
                    $rental_items[$item_id] = [
                        'pickup_date' => $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) ),
                        'dropoff_date' => $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) ),
                        'quantity' => $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: 1,
                        'security_deposit' => $item->get_meta( smart_rentals_wc_meta_key( 'security_deposit' ) ) ?: 0,
                        'total' => $item->get_total(),
                        'product_id' => $item->get_product_id(),
                    ];
                }
            }

            wp_send_json_success( $rental_items );
        }

        /**
         * AJAX handler for saving order rental data
         */
        public function ajax_save_order_rental_data() {
            // Verify nonce
            if ( !wp_verify_nonce( $_POST['nonce'], 'smart-rentals-order-edit-v2' ) ) {
                wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
            }

            // Check permissions
            if ( !current_user_can( 'edit_shop_orders' ) ) {
                wp_send_json_error( [ 'message' => __( 'Insufficient permissions', 'smart-rentals-wc' ) ] );
            }

            $order_id = intval( $_POST['order_id'] );
            $item_id = intval( $_POST['item_id'] );
            $pickup_date = sanitize_text_field( $_POST['pickup_date'] ?? '' );
            $dropoff_date = sanitize_text_field( $_POST['dropoff_date'] ?? '' );
            $quantity = intval( $_POST['quantity'] ?? 1 );
            $security_deposit = floatval( $_POST['security_deposit'] ?? 0 );

            $order = wc_get_order( $order_id );
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
                $available = $this->check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity, $order_id );

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
                $this->update_custom_booking_table( $order_id, $item_id, $pickup_timestamp, $dropoff_timestamp, $quantity );
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
                __( 'Rental details updated for %s via order management', 'smart-rentals-wc' ),
                $item->get_name()
            ) );

            wp_send_json_success( [
                'message' => __( 'Rental item saved successfully', 'smart-rentals-wc' ),
                'new_price' => $item->get_total(),
                'formatted_price' => smart_rentals_wc_price( $item->get_total() )
            ] );
        }

        /**
         * AJAX handler for checking rental availability
         */
        public function ajax_check_rental_availability() {
            // Verify nonce
            if ( !wp_verify_nonce( $_POST['nonce'], 'smart-rentals-order-edit-v2' ) ) {
                wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
            }

            // Check permissions
            if ( !current_user_can( 'edit_shop_orders' ) ) {
                wp_send_json_error( [ 'message' => __( 'Insufficient permissions', 'smart-rentals-wc' ) ] );
            }

            $product_id = intval( $_POST['product_id'] );
            $pickup_date = sanitize_text_field( $_POST['pickup_date'] );
            $dropoff_date = sanitize_text_field( $_POST['dropoff_date'] );
            $quantity = intval( $_POST['quantity'] );
            $order_id = intval( $_POST['order_id'] );

            if ( !$product_id || !$pickup_date || !$dropoff_date ) {
                wp_send_json_error( [ 'message' => __( 'Missing required parameters', 'smart-rentals-wc' ) ] );
            }

            $pickup_timestamp = strtotime( $pickup_date );
            $dropoff_timestamp = strtotime( $dropoff_date );

            if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
                wp_send_json_error( [ 'message' => __( 'Invalid date range', 'smart-rentals-wc' ) ] );
            }

            // Check availability
            $available = $this->check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity, $order_id );

            if ( $available ) {
                // Calculate new price
                $new_price = Smart_Rentals_WC()->options->calculate_rental_price( 
                    $product_id, 
                    $pickup_timestamp, 
                    $dropoff_timestamp, 
                    $quantity 
                );

                // Get duration text
                $duration_text = Smart_Rentals_WC()->options->get_rental_duration_text( 
                    $pickup_timestamp, 
                    $dropoff_timestamp 
                );

                // Get available quantity
                $available_quantity = Smart_Rentals_WC()->options->get_available_quantity( 
                    $product_id, 
                    $pickup_timestamp, 
                    $dropoff_timestamp 
                );

                wp_send_json_success( [
                    'available_quantity' => $available_quantity,
                    'new_price' => $new_price,
                    'formatted_price' => smart_rentals_wc_price( $new_price ),
                    'duration_text' => $duration_text,
                ] );
            } else {
                wp_send_json_error( [ 'message' => __( 'Product not available for selected dates and quantity', 'smart-rentals-wc' ) ] );
            }
        }

        /**
         * Check availability excluding current order
         */
        private function check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity, $exclude_order_id ) {
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
                LEFT JOIN {$wpdb->prefix}posts AS orders 
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
                        'pickup_date' => date( 'Y-m-d H:i:s', $pickup_timestamp ),
                        'dropoff_date' => date( 'Y-m-d H:i:s', $dropoff_timestamp ),
                        'quantity' => $quantity,
                        'updated_at' => current_time( 'mysql' )
                    ],
                    [
                        'order_id' => $order_id,
                        'item_id' => $item_id
                    ],
                    [ '%s', '%s', '%d', '%s' ],
                    [ '%d', '%d' ]
                );
            }
        }
    }
}