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
            // Admin order item headers
            add_action( 'woocommerce_admin_order_item_headers', [ $this, 'admin_order_item_headers' ] );

            // Admin order item values
            add_action( 'woocommerce_admin_order_item_values', [ $this, 'admin_order_item_values' ], 10, 3 );

            // Save order items
            add_action( 'woocommerce_saved_order_items', [ $this, 'saved_order_items' ], 10, 2 );

            // Enqueue admin scripts
            add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
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

            // Check if order has rental items
            $has_rental_items = false;
            foreach ( $order->get_items() as $item ) {
                $is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
                if ( $is_rental === 'yes' ) {
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

            // Check if this is a rental item
            $is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
            
            if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
                smart_rentals_wc_log( 'Item #' . $item_id . ' is_rental: ' . $is_rental );
            }

            if ( $is_rental !== 'yes' ) {
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
                <div class="rental-edit-container">
                    <div class="rental-edit-fields" style="display: none;">
                        <table class="rental-edit-table">
                            <tr>
                                <td><label><?php _e( 'Pickup Date:', 'smart-rentals-wc' ); ?></label></td>
                                <td>
                                    <input type="datetime-local" 
                                           name="rental_pickup_date[<?php echo $item_id; ?>]" 
                                           value="<?php echo esc_attr( $pickup_date ? date( 'Y-m-d\TH:i', strtotime( $pickup_date ) ) : '' ); ?>" 
                                           class="rental-date-input" />
                                </td>
                            </tr>
                            <tr>
                                <td><label><?php _e( 'Dropoff Date:', 'smart-rentals-wc' ); ?></label></td>
                                <td>
                                    <input type="datetime-local" 
                                           name="rental_dropoff_date[<?php echo $item_id; ?>]" 
                                           value="<?php echo esc_attr( $dropoff_date ? date( 'Y-m-d\TH:i', strtotime( $dropoff_date ) ) : '' ); ?>" 
                                           class="rental-date-input" />
                                </td>
                            </tr>
                            <tr>
                                <td><label><?php _e( 'Quantity:', 'smart-rentals-wc' ); ?></label></td>
                                <td>
                                    <input type="number" 
                                           name="rental_quantity[<?php echo $item_id; ?>]" 
                                           value="<?php echo esc_attr( $rental_quantity ?: 1 ); ?>" 
                                           min="1" 
                                           class="rental-quantity-input" />
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <button type="button" class="button rental-check-availability" data-item-id="<?php echo $item_id; ?>" data-product-id="<?php echo $product_id; ?>">
                                        <?php _e( 'Check Availability', 'smart-rentals-wc' ); ?>
                                    </button>
                                    <div class="rental-availability-result" style="margin-top: 10px;"></div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="rental-display-info">
                        <strong><?php _e( 'Rental Period:', 'smart-rentals-wc' ); ?></strong><br>
                        <span class="rental-dates">
                            <?php if ( $pickup_date && $dropoff_date ) : ?>
                                <?php echo esc_html( date( 'Y-m-d H:i', strtotime( $pickup_date ) ) ); ?> 
                                → 
                                <?php echo esc_html( date( 'Y-m-d H:i', strtotime( $dropoff_date ) ) ); ?>
                            <?php else : ?>
                                <?php _e( 'Not set', 'smart-rentals-wc' ); ?>
                            <?php endif; ?>
                        </span><br>
                        
                        <strong><?php _e( 'Quantity:', 'smart-rentals-wc' ); ?></strong> 
                        <span class="rental-quantity-display"><?php echo esc_html( $rental_quantity ?: 1 ); ?></span><br>
                        
                        <?php if ( $security_deposit ) : ?>
                            <strong><?php _e( 'Security Deposit:', 'smart-rentals-wc' ); ?></strong> 
                            <?php echo wp_kses_post( $security_deposit ); ?><br>
                        <?php endif; ?>
                        
                        <button type="button" class="button button-small rental-edit-toggle" data-item-id="<?php echo $item_id; ?>" onclick="console.log('Button clicked directly'); alert('Button works!');">
                            <?php _e( 'Edit Rental Details', 'smart-rentals-wc' ); ?>
                        </button>
                    </div>
                </div>
            </td>
            <?php
        }

        /**
         * Save order items
         */
        public function saved_order_items( $order_id, $items ) {
            if ( !isset( $_POST['rental_pickup_date'] ) ) {
                return;
            }

            $order = wc_get_order( $order_id );
            if ( !$order ) {
                return;
            }

            foreach ( $items['order_item_id'] as $item_id ) {
                $item = $order->get_item( $item_id );
                if ( !$item ) {
                    continue;
                }

                // Check if this item has rental data to update
                $is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
                if ( $is_rental !== 'yes' ) {
                    continue;
                }

                // Get new rental data
                $new_pickup = isset( $_POST['rental_pickup_date'][$item_id] ) ? sanitize_text_field( $_POST['rental_pickup_date'][$item_id] ) : '';
                $new_dropoff = isset( $_POST['rental_dropoff_date'][$item_id] ) ? sanitize_text_field( $_POST['rental_dropoff_date'][$item_id] ) : '';
                $new_quantity = isset( $_POST['rental_quantity'][$item_id] ) ? intval( $_POST['rental_quantity'][$item_id] ) : 1;

                if ( $new_pickup && $new_dropoff ) {
                    // Validate dates
                    $pickup_timestamp = strtotime( $new_pickup );
                    $dropoff_timestamp = strtotime( $new_dropoff );

                    if ( $pickup_timestamp && $dropoff_timestamp && $pickup_timestamp < $dropoff_timestamp ) {
                        // Get product ID for availability checking
                        $product_id = $item->get_product_id();

                        // Check availability for new dates (excluding current booking)
                        $available = $this->check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $new_quantity, $order_id );

                        if ( $available ) {
                            // Update rental data
                            $item->update_meta_data( smart_rentals_wc_meta_key( 'pickup_date' ), date( 'Y-m-d H:i:s', $pickup_timestamp ) );
                            $item->update_meta_data( smart_rentals_wc_meta_key( 'dropoff_date' ), date( 'Y-m-d H:i:s', $dropoff_timestamp ) );
                            $item->update_meta_data( smart_rentals_wc_meta_key( 'rental_quantity' ), $new_quantity );

                            // Update item quantity if changed
                            if ( $new_quantity != $item->get_quantity() ) {
                                $item->set_quantity( $new_quantity );
                            }

                            // Recalculate price based on new dates
                            $new_price = Smart_Rentals_WC()->options->calculate_rental_price( 
                                $product_id, 
                                $pickup_timestamp, 
                                $dropoff_timestamp, 
                                $new_quantity 
                            );

                            if ( $new_price > 0 ) {
                                $unit_price = $new_price / $new_quantity;
                                $item->set_subtotal( $new_price );
                                $item->set_total( $new_price );
                            }

                            $item->save();

                            // Update custom bookings table if exists
                            $this->update_custom_booking_table( $order_id, $item_id, $pickup_timestamp, $dropoff_timestamp, $new_quantity );

                            // Add order note
                            $order->add_order_note( sprintf( 
                                __( 'Rental details updated for %s: %s to %s (Qty: %d)', 'smart-rentals-wc' ),
                                $item->get_name(),
                                date( 'Y-m-d H:i', $pickup_timestamp ),
                                date( 'Y-m-d H:i', $dropoff_timestamp ),
                                $new_quantity
                            ) );

                        } else {
                            // Add error notice
                            $order->add_order_note( sprintf( 
                                __( 'Failed to update rental details for %s: Product not available for selected dates/quantity', 'smart-rentals-wc' ),
                                $item->get_name()
                            ) );
                        }
                    }
                }
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

            // Add inline script for immediate testing
            wp_add_inline_script( 'smart-rentals-order-edit', '
                console.log("Smart Rentals: Inline script loaded");
                jQuery(document).ready(function($) {
                    console.log("Smart Rentals: Document ready");
                    console.log("Smart Rentals: Looking for buttons...");
                    
                    // Test if buttons exist
                    setTimeout(function() {
                        var buttons = $(".rental-edit-toggle");
                        console.log("Smart Rentals: Found buttons:", buttons.length);
                        
                        if (buttons.length > 0) {
                            console.log("Smart Rentals: Buttons found, adding click handler");
                            buttons.on("click", function(e) {
                                e.preventDefault();
                                console.log("Smart Rentals: Button clicked!");
                                alert("Edit button is working!");
                                
                                var container = $(this).closest(".rental-edit-container");
                                var fields = container.find(".rental-edit-fields");
                                var display = container.find(".rental-display-info");
                                
                                if (fields.is(":visible")) {
                                    fields.hide();
                                    display.show();
                                    $(this).text("Edit Rental Details");
                                } else {
                                    fields.show();
                                    display.hide();
                                    $(this).text("Cancel Edit");
                                }
                            });
                        } else {
                            console.log("Smart Rentals: No buttons found in DOM");
                        }
                    }, 1000);
                });
            ' );
        }
    }
}