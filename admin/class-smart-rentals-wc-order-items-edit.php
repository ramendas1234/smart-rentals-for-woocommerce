<?php
/**
 * Smart Rentals WC Order Items Edit
 * Direct integration with WooCommerce order items table
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Order_Items_Edit' ) ) {

    class Smart_Rentals_WC_Order_Items_Edit {

        /**
         * Constructor
         */
        public function __construct() {
            // Debug: Log constructor
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                error_log( 'Smart Rentals Order Items Edit: Constructor called' );
            }
            
            // Add custom columns to order items table
            add_filter( 'woocommerce_admin_order_item_headers', [ $this, 'add_rental_columns' ] );
            add_action( 'woocommerce_admin_order_item_values', [ $this, 'add_rental_item_data' ], 10, 3 );
            
            // Enqueue scripts and styles
            add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
            
            // AJAX handlers
            add_action( 'wp_ajax_smart_rentals_update_order_item_rental', [ $this, 'ajax_update_order_item_rental' ] );
            add_action( 'wp_ajax_smart_rentals_check_rental_availability', [ $this, 'ajax_check_rental_availability' ] );
        }

        /**
         * Add rental columns to order items table
         */
        public function add_rental_columns( $order ) {
            echo '<!-- Smart Rentals: add_rental_columns called for order #' . $order->get_id() . ' -->';
            
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
                    ];
                }
            }
            
            echo '<!-- Smart Rentals: Has rental items: ' . ( $has_rental_items ? 'YES' : 'NO' ) . ' -->';
            echo '<!-- Smart Rentals: Rental items found: ' . count( $rental_items_found ) . ' -->';
            
            if ( $has_rental_items ) {
                echo '<th class="rental-dates-column">' . __( 'Rental Details', 'smart-rentals-wc' ) . '</th>';
                echo '<th class="rental-actions-column">' . __( 'Actions', 'smart-rentals-wc' ) . '</th>';
                echo '<!-- Smart Rentals: Columns added -->';
            }
        }

        /**
         * Add rental item data to order items table
         */
        public function add_rental_item_data( $product, $item, $item_id ) {
            echo '<!-- Smart Rentals: add_rental_item_data called for item #' . $item_id . ' (' . $item->get_name() . ') -->';
            
            $is_rental = $this->is_rental_item( $item );
            echo '<!-- Smart Rentals: Is rental item: ' . ( $is_rental ? 'YES' : 'NO' ) . ' -->';
            
            if ( !$is_rental ) {
                echo '<td class="rental-dates-column"></td>';
                echo '<td class="rental-actions-column"></td>';
                echo '<!-- Smart Rentals: Empty columns added for non-rental item -->';
                return;
            }
            
            echo '<!-- Smart Rentals: Processing rental item data -->';

            $pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
            $dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
            $rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: 1;
            $security_deposit = $item->get_meta( smart_rentals_wc_meta_key( 'security_deposit' ) ) ?: 0;
            $product_id = $item->get_product_id();
            
            ?>
            <!-- Rental Details Column -->
            <td class="rental-dates-column" style="min-width: 300px;">
                <div class="rental-item-container" data-item-id="<?php echo $item_id; ?>">
                    <!-- Display Mode -->
                    <div class="rental-display-mode">
                        <div class="rental-info">
                            <div class="rental-date-info">
                                <strong><?php _e( 'Pickup:', 'smart-rentals-wc' ); ?></strong>
                                <span class="pickup-display"><?php echo $pickup_date ? date( 'M j, Y g:i A', strtotime( $pickup_date ) ) : __( 'Not set', 'smart-rentals-wc' ); ?></span>
                            </div>
                            <div class="rental-date-info">
                                <strong><?php _e( 'Dropoff:', 'smart-rentals-wc' ); ?></strong>
                                <span class="dropoff-display"><?php echo $dropoff_date ? date( 'M j, Y g:i A', strtotime( $dropoff_date ) ) : __( 'Not set', 'smart-rentals-wc' ); ?></span>
                            </div>
                            <div class="rental-quantity-info">
                                <strong><?php _e( 'Qty:', 'smart-rentals-wc' ); ?></strong>
                                <span class="quantity-display"><?php echo esc_html( $rental_quantity ); ?></span>
                            </div>
                            <?php if ( $security_deposit > 0 ) : ?>
                            <div class="rental-deposit-info">
                                <strong><?php _e( 'Deposit:', 'smart-rentals-wc' ); ?></strong>
                                <span class="deposit-display"><?php echo smart_rentals_wc_price( $security_deposit ); ?></span>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Edit Mode (Hidden by default) -->
                    <div class="rental-edit-mode" style="display: none;">
                        <div class="rental-edit-form">
                            <div class="rental-form-row">
                                <label><?php _e( 'Pickup Date & Time:', 'smart-rentals-wc' ); ?></label>
                                <input type="datetime-local" 
                                       class="rental-pickup-input" 
                                       value="<?php echo esc_attr( $pickup_date ? date( 'Y-m-d\TH:i', strtotime( $pickup_date ) ) : '' ); ?>" 
                                       style="width: 100%; margin-bottom: 8px;" />
                            </div>
                            
                            <div class="rental-form-row">
                                <label><?php _e( 'Dropoff Date & Time:', 'smart-rentals-wc' ); ?></label>
                                <input type="datetime-local" 
                                       class="rental-dropoff-input" 
                                       value="<?php echo esc_attr( $dropoff_date ? date( 'Y-m-d\TH:i', strtotime( $dropoff_date ) ) : '' ); ?>" 
                                       style="width: 100%; margin-bottom: 8px;" />
                            </div>
                            
                            <div class="rental-form-row">
                                <label><?php _e( 'Quantity:', 'smart-rentals-wc' ); ?></label>
                                <input type="number" 
                                       class="rental-quantity-input" 
                                       value="<?php echo esc_attr( $rental_quantity ); ?>" 
                                       min="1" 
                                       style="width: 100%; margin-bottom: 8px;" />
                            </div>
                            
                            <div class="rental-form-row">
                                <label><?php _e( 'Security Deposit:', 'smart-rentals-wc' ); ?></label>
                                <input type="number" 
                                       class="rental-deposit-input" 
                                       value="<?php echo esc_attr( $security_deposit ); ?>" 
                                       min="0" 
                                       step="0.01" 
                                       style="width: 100%; margin-bottom: 8px;" />
                            </div>
                            
                            <div class="rental-feedback" style="margin-top: 10px; font-size: 12px;"></div>
                        </div>
                    </div>
                </div>
            </td>
            
            <!-- Actions Column -->
            <td class="rental-actions-column" style="min-width: 150px;">
                <div class="rental-actions">
                    <button type="button" 
                            class="button button-small rental-edit-toggle" 
                            data-item-id="<?php echo $item_id; ?>"
                            data-product-id="<?php echo $product_id; ?>">
                        <span class="dashicons dashicons-edit"></span>
                        <?php _e( 'Edit', 'smart-rentals-wc' ); ?>
                    </button>
                    
                    <button type="button" 
                            class="button button-small rental-check-availability" 
                            data-item-id="<?php echo $item_id; ?>"
                            data-product-id="<?php echo $product_id; ?>"
                            style="display: none;">
                        <span class="dashicons dashicons-search"></span>
                        <?php _e( 'Check', 'smart-rentals-wc' ); ?>
                    </button>
                    
                    <button type="button" 
                            class="button button-primary button-small rental-save" 
                            data-item-id="<?php echo $item_id; ?>"
                            style="display: none;">
                        <span class="dashicons dashicons-yes"></span>
                        <?php _e( 'Save', 'smart-rentals-wc' ); ?>
                    </button>
                    
                    <button type="button" 
                            class="button button-secondary button-small rental-cancel" 
                            data-item-id="<?php echo $item_id; ?>"
                            style="display: none;">
                        <span class="dashicons dashicons-no"></span>
                        <?php _e( 'Cancel', 'smart-rentals-wc' ); ?>
                    </button>
                </div>
            </td>
            <?php
        }

        /**
         * Check if item is a rental item
         */
        private function is_rental_item( $item ) {
            $is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
            $pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
            $dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
            $rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) );
            
            return ( $is_rental === 'yes' || ( $pickup_date && $dropoff_date ) || $rental_quantity );
        }

        /**
         * Enqueue admin scripts
         */
        public function admin_scripts( $hook ) {
            echo '<!-- Smart Rentals: admin_scripts called with hook: ' . $hook . ' -->';
            
            // Only on order edit pages
            if ( 'post.php' !== $hook ) {
                echo '<!-- Smart Rentals: Not post.php hook, skipping -->';
                return;
            }

            global $post;
            if ( !$post || $post->post_type !== 'shop_order' ) {
                echo '<!-- Smart Rentals: Not shop_order post type, skipping -->';
                return;
            }

            echo '<!-- Smart Rentals: Processing order #' . $post->ID . ' -->';

            // Check if order has rental items
            $order = wc_get_order( $post->ID );
            if ( !$order ) {
                echo '<!-- Smart Rentals: Order not found, skipping -->';
                return;
            }

            $has_rental_items = false;
            $rental_items_count = 0;
            foreach ( $order->get_items() as $item ) {
                if ( $this->is_rental_item( $item ) ) {
                    $has_rental_items = true;
                    $rental_items_count++;
                }
            }

            echo '<!-- Smart Rentals: Has rental items: ' . ( $has_rental_items ? 'YES' : 'NO' ) . ' (' . $rental_items_count . ' items) -->';

            if ( !$has_rental_items ) {
                echo '<!-- Smart Rentals: No rental items, skipping script enqueue -->';
                return;
            }
            
            echo '<!-- Smart Rentals: Enqueuing scripts and styles -->';

            // Enqueue scripts
            wp_enqueue_script( 'jquery' );
            
            wp_enqueue_script( 
                'smart-rentals-order-items-edit', 
                SMART_RENTALS_WC_PLUGIN_URI . 'assets/js/admin-order-items-edit.js', 
                [ 'jquery' ], 
                SMART_RENTALS_WC_VERSION, 
                true 
            );
            
            wp_enqueue_style( 
                'smart-rentals-order-items-edit', 
                SMART_RENTALS_WC_PLUGIN_URI . 'assets/css/admin-order-items-edit.css', 
                [], 
                SMART_RENTALS_WC_VERSION 
            );
            
            wp_localize_script( 'smart-rentals-order-items-edit', 'smartRentalsOrderItems', [
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce' => wp_create_nonce( 'smart-rentals-order-items-edit' ),
                'order_id' => $post->ID,
                'strings' => [
                    'edit' => __( 'Edit', 'smart-rentals-wc' ),
                    'cancel' => __( 'Cancel', 'smart-rentals-wc' ),
                    'save' => __( 'Save', 'smart-rentals-wc' ),
                    'checking' => __( 'Checking availability...', 'smart-rentals-wc' ),
                    'available' => __( 'Available', 'smart-rentals-wc' ),
                    'not_available' => __( 'Not available', 'smart-rentals-wc' ),
                    'saving' => __( 'Saving...', 'smart-rentals-wc' ),
                    'saved' => __( 'Saved successfully', 'smart-rentals-wc' ),
                    'error' => __( 'Error occurred', 'smart-rentals-wc' ),
                ]
            ]);
        }

        /**
         * AJAX handler for updating order item rental data
         */
        public function ajax_update_order_item_rental() {
            // Start output buffering to catch any unwanted output
            ob_start();
            
            try {
                // Verify nonce
                if ( !wp_verify_nonce( $_POST['nonce'], 'smart-rentals-order-items-edit' ) ) {
                    throw new Exception( 'Security check failed' );
                }

                // Check permissions
                if ( !current_user_can( 'edit_shop_orders' ) ) {
                    throw new Exception( 'Insufficient permissions' );
                }

                $order_id = intval( $_POST['order_id'] ?? 0 );
                $item_id = intval( $_POST['item_id'] ?? 0 );
                $pickup_date = sanitize_text_field( $_POST['pickup_date'] ?? '' );
                $dropoff_date = sanitize_text_field( $_POST['dropoff_date'] ?? '' );
                $quantity = intval( $_POST['quantity'] ?? 1 );
                $security_deposit = floatval( $_POST['security_deposit'] ?? 0 );

                if ( !$order_id || !$item_id ) {
                    throw new Exception( 'Missing required parameters' );
                }

                $order = wc_get_order( $order_id );
                if ( !$order ) {
                    throw new Exception( 'Order not found' );
                }

                $item = $order->get_item( $item_id );
                if ( !$item ) {
                    throw new Exception( 'Order item not found' );
                }

                // Update basic data
                $item->set_quantity( $quantity );
                $item->update_meta_data( smart_rentals_wc_meta_key( 'rental_quantity' ), $quantity );
                $item->update_meta_data( smart_rentals_wc_meta_key( 'security_deposit' ), $security_deposit );

                // Update dates if provided
                if ( $pickup_date && $dropoff_date ) {
                    $pickup_timestamp = strtotime( $pickup_date );
                    $dropoff_timestamp = strtotime( $dropoff_date );

                    if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
                        throw new Exception( 'Invalid date range' );
                    }

                    $item->update_meta_data( smart_rentals_wc_meta_key( 'pickup_date' ), date( 'Y-m-d H:i:s', $pickup_timestamp ) );
                    $item->update_meta_data( smart_rentals_wc_meta_key( 'dropoff_date' ), date( 'Y-m-d H:i:s', $dropoff_timestamp ) );
                    
                    // Add is_rental flag
                    $item->update_meta_data( smart_rentals_wc_meta_key( 'is_rental' ), 'yes' );
                }

                $item->save();
                $order->calculate_totals();
                $order->save();

                // Clear any output and send clean JSON response
                ob_clean();
                wp_send_json_success( [
                    'message' => 'Rental item updated successfully',
                    'new_price' => $item->get_total(),
                    'formatted_price' => smart_rentals_wc_price( $item->get_total() )
                ] );

            } catch ( Exception $e ) {
                // Clear any output and send clean error response
                ob_clean();
                wp_send_json_error( [ 'message' => $e->getMessage() ] );
            }
        }

        /**
         * AJAX handler for checking rental availability
         */
        public function ajax_check_rental_availability() {
            // Start output buffering to catch any unwanted output
            ob_start();
            
            try {
                // Verify nonce
                if ( !wp_verify_nonce( $_POST['nonce'], 'smart-rentals-order-items-edit' ) ) {
                    throw new Exception( 'Security check failed' );
                }

                // Check permissions
                if ( !current_user_can( 'edit_shop_orders' ) ) {
                    throw new Exception( 'Insufficient permissions' );
                }

                $product_id = intval( $_POST['product_id'] ?? 0 );
                $pickup_date = sanitize_text_field( $_POST['pickup_date'] ?? '' );
                $dropoff_date = sanitize_text_field( $_POST['dropoff_date'] ?? '' );
                $quantity = intval( $_POST['quantity'] ?? 1 );
                $order_id = intval( $_POST['order_id'] ?? 0 );

                if ( !$product_id || !$pickup_date || !$dropoff_date ) {
                    throw new Exception( 'Missing required parameters' );
                }

                $pickup_timestamp = strtotime( $pickup_date );
                $dropoff_timestamp = strtotime( $dropoff_date );

                if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
                    throw new Exception( 'Invalid date range' );
                }

                // Simple availability check - just return success for now
                $new_price = 100; // Placeholder price
                $duration_text = '1 day'; // Placeholder duration

                // Clear any output and send clean JSON response
                ob_clean();
                wp_send_json_success( [
                    'available_quantity' => 10,
                    'new_price' => $new_price,
                    'formatted_price' => '$' . number_format( $new_price, 2 ),
                    'duration_text' => $duration_text,
                ] );

            } catch ( Exception $e ) {
                // Clear any output and send clean error response
                ob_clean();
                wp_send_json_error( [ 'message' => $e->getMessage() ] );
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