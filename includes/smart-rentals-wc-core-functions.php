<?php
/**
 * Smart Rentals WC Core Functions
 */

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Check if WooCommerce is available
 */
if ( !function_exists( 'smart_rentals_wc_is_woocommerce_active' ) ) {
    function smart_rentals_wc_is_woocommerce_active() {
        return class_exists( 'WooCommerce' ) && function_exists( 'WC' );
    }
}

/**
 * Get meta key with prefix
 */
if ( !function_exists( 'smart_rentals_wc_meta_key' ) ) {
    function smart_rentals_wc_meta_key( $key = '' ) {
        if ( $key ) $key = SMART_RENTALS_WC_META_PREFIX . $key;
        return apply_filters( 'smart_rentals_wc_meta_key', $key );
    }
}

/**
 * Get meta data from array
 */
if ( !function_exists( 'smart_rentals_wc_get_meta_data' ) ) {
    function smart_rentals_wc_get_meta_data( $key = '', $data = [], $default = '' ) {
        if ( !$key ) return $default;
        
        if ( is_array( $data ) && array_key_exists( $key, $data ) ) {
            return $data[$key];
        }
        
        return $default;
    }
}

/**
 * Check if array exists and not empty
 */
if ( !function_exists( 'smart_rentals_wc_array_exists' ) ) {
    function smart_rentals_wc_array_exists( $array ) {
        return is_array( $array ) && !empty( $array );
    }
}

/**
 * Parse datetime string with multiple format support
 */
if ( !function_exists( 'smart_rentals_wc_parse_datetime_string' ) ) {
    function smart_rentals_wc_parse_datetime_string( $datetime_string ) {
        if ( empty( $datetime_string ) ) {
            return false;
        }

        // Try different datetime formats
        $formats = [
            'Y-m-d H:i',     // 2025-09-14 10:00
            'Y-m-d H:i:s',   // 2025-09-14 10:00:00
            'Y-m-d',         // 2025-09-14
            'm/d/Y H:i',     // 09/14/2025 10:00
            'm/d/Y',         // 09/14/2025
            'd-m-Y H:i',     // 14-09-2025 10:00
            'd-m-Y',         // 14-09-2025
        ];

        foreach ( $formats as $format ) {
            $timestamp = DateTime::createFromFormat( $format, $datetime_string );
            if ( $timestamp && $timestamp->format( $format ) === $datetime_string ) {
                smart_rentals_wc_log( 'Successfully parsed datetime: ' . $datetime_string . ' with format: ' . $format );
                return $timestamp->getTimestamp();
            }
        }

        // Fallback to strtotime
        $timestamp = strtotime( $datetime_string );
        if ( $timestamp ) {
            smart_rentals_wc_log( 'Parsed datetime with strtotime: ' . $datetime_string . ' -> ' . $timestamp );
            return $timestamp;
        }

		smart_rentals_wc_log( 'Failed to parse datetime: ' . $datetime_string );
		return false;
	}
}

/**
 * Get booking color based on status
 */
if ( !function_exists( 'smart_rentals_wc_get_booking_color' ) ) {
	function smart_rentals_wc_get_booking_color( $status ) {
		$colors = [
			'pending' => '#ffc107',     // Yellow
			'confirmed' => '#28a745',   // Green
			'active' => '#17a2b8',      // Blue
			'processing' => '#fd7e14',  // Orange
			'completed' => '#6f42c1',   // Purple
			'cancelled' => '#dc3545',   // Red
		];
		
		return isset( $colors[$status] ) ? $colors[$status] : '#6c757d';
	}
}

/**
 * Get post meta with prefix
 */
if ( !function_exists( 'smart_rentals_wc_get_post_meta' ) ) {
    function smart_rentals_wc_get_post_meta( $post_id, $key, $single = true ) {
        return get_post_meta( $post_id, smart_rentals_wc_meta_key( $key ), $single );
    }
}

/**
 * Update post meta with prefix
 */
if ( !function_exists( 'smart_rentals_wc_update_post_meta' ) ) {
    function smart_rentals_wc_update_post_meta( $post_id, $key, $value ) {
        return update_post_meta( $post_id, smart_rentals_wc_meta_key( $key ), $value );
    }
}

/**
 * Delete post meta with prefix
 */
if ( !function_exists( 'smart_rentals_wc_delete_post_meta' ) ) {
    function smart_rentals_wc_delete_post_meta( $post_id, $key ) {
        return delete_post_meta( $post_id, smart_rentals_wc_meta_key( $key ) );
    }
}

/**
 * Check if product is rental
 */
if ( !function_exists( 'smart_rentals_wc_is_rental_product' ) ) {
    function smart_rentals_wc_is_rental_product( $product_id = null ) {
        if ( !$product_id ) {
            global $post;
            $product_id = $post ? $post->ID : null;
        }
        
        if ( !$product_id ) return false;
        
        return 'yes' === smart_rentals_wc_get_post_meta( $product_id, 'enable_rental' );
    }
}

/**
 * Get option with default
 */
if ( !function_exists( 'smart_rentals_wc_get_option' ) ) {
    function smart_rentals_wc_get_option( $key, $default = '' ) {
        return get_option( SMART_RENTALS_WC_PREFIX . $key, $default );
    }
}

/**
 * Update option
 */
if ( !function_exists( 'smart_rentals_wc_update_option' ) ) {
    function smart_rentals_wc_update_option( $key, $value ) {
        return update_option( SMART_RENTALS_WC_PREFIX . $key, $value );
    }
}

/**
 * Delete option
 */
if ( !function_exists( 'smart_rentals_wc_delete_option' ) ) {
    function smart_rentals_wc_delete_option( $key ) {
        return delete_option( SMART_RENTALS_WC_PREFIX . $key );
    }
}

/**
 * Get setting with fallback
 */
if ( !function_exists( 'smart_rentals_wc_get_setting' ) ) {
    function smart_rentals_wc_get_setting( $key, $default = '' ) {
        $settings = smart_rentals_wc_get_option( 'settings', [] );
        return smart_rentals_wc_get_meta_data( $key, $settings, $default );
    }
}

/**
 * Format price
 */
if ( !function_exists( 'smart_rentals_wc_format_price' ) ) {
    function smart_rentals_wc_format_price( $price ) {
        if ( function_exists( 'wc_format_decimal' ) && function_exists( 'wc_get_price_decimals' ) ) {
            return wc_format_decimal( $price, wc_get_price_decimals() );
        }
        return floatval( $price );
    }
}

/**
 * Format date
 */
if ( !function_exists( 'smart_rentals_wc_format_date' ) ) {
    function smart_rentals_wc_format_date( $date ) {
        if ( !$date ) return '';
        
        $timestamp = is_numeric( $date ) ? $date : strtotime( $date );
        return $timestamp ? gmdate( 'Y-m-d', $timestamp ) : '';
    }
}

/**
 * Format number
 */
if ( !function_exists( 'smart_rentals_wc_format_number' ) ) {
    function smart_rentals_wc_format_number( $number ) {
        return is_numeric( $number ) ? floatval( $number ) : 0;
    }
}

/**
 * Get rental product
 */
if ( !function_exists( 'smart_rentals_wc_get_rental_product' ) ) {
    function smart_rentals_wc_get_rental_product( $product_id = null ) {
        if ( !function_exists( 'wc_get_product' ) ) {
            return false;
        }
        
        if ( !$product_id ) {
            global $post;
            $product_id = $post ? $post->ID : null;
        }
        
        if ( !$product_id ) return false;
        
        $product = wc_get_product( $product_id );
        
        if ( $product && smart_rentals_wc_is_rental_product( $product_id ) ) {
            return $product;
        }
        
        return false;
    }
}

/**
 * Get order status for bookings
 */
if ( !function_exists( 'smart_rentals_wc_get_order_status' ) ) {
    function smart_rentals_wc_get_order_status() {
        return apply_filters( 'smart_rentals_wc_order_status', [
            'wc-processing',
            'wc-completed',
            'wc-on-hold'
        ]);
    }
}

/**
 * Sanitize title
 */
if ( !function_exists( 'smart_rentals_wc_sanitize_title' ) ) {
    function smart_rentals_wc_sanitize_title( $title ) {
        return sanitize_title( $title );
    }
}

/**
 * Selected helper
 */
if ( !function_exists( 'smart_rentals_wc_selected' ) ) {
    function smart_rentals_wc_selected( $selected, $current = true, $echo = true ) {
        return selected( $selected, $current, $echo );
    }
}

/**
 * Checked helper
 */
if ( !function_exists( 'smart_rentals_wc_checked' ) ) {
    function smart_rentals_wc_checked( $checked, $current = true, $echo = true ) {
        return checked( $checked, $current, $echo );
    }
}

/**
 * Convert price for display
 */
if ( !function_exists( 'smart_rentals_wc_price' ) ) {
    function smart_rentals_wc_price( $price, $args = [], $format = true ) {
        if ( $format && function_exists( 'wc_price' ) ) {
            return wc_price( $price, $args );
        }
        return $price;
    }
}

/**
 * Get current user capability
 */
if ( !function_exists( 'smart_rentals_wc_current_user_can' ) ) {
    function smart_rentals_wc_current_user_can( $capability ) {
        return current_user_can( $capability );
    }
}

/**
 * Log function
 */
if ( !function_exists( 'smart_rentals_wc_log' ) ) {
    function smart_rentals_wc_log( $message, $level = 'info' ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            $logger = wc_get_logger();
            $logger->log( $level, $message, [ 'source' => 'smart-rentals-wc' ] );
        }
    }
}

/**
 * Get template part
 */
if ( !function_exists( 'smart_rentals_wc_get_template_part' ) ) {
    function smart_rentals_wc_get_template_part( $slug, $name = null, $args = [] ) {
        $template = '';
        
        if ( $name ) {
            $template = locate_template( "smart-rentals-wc/{$slug}-{$name}.php" );
        }
        
        if ( !$template ) {
            $template = locate_template( "smart-rentals-wc/{$slug}.php" );
        }
        
        if ( !$template ) {
            if ( $name ) {
                $template = SMART_RENTALS_WC_PLUGIN_TEMPLATES . "{$slug}-{$name}.php";
            } else {
                $template = SMART_RENTALS_WC_PLUGIN_TEMPLATES . "{$slug}.php";
            }
        }
        
        if ( file_exists( $template ) ) {
            if ( smart_rentals_wc_array_exists( $args ) ) {
                extract( $args );
            }
            include $template;
        }
    }
}

/**
 * Get template
 */
if ( !function_exists( 'smart_rentals_wc_get_template' ) ) {
    function smart_rentals_wc_get_template( $template_name, $args = [], $template_path = '', $default_path = '' ) {
        if ( smart_rentals_wc_array_exists( $args ) ) {
            extract( $args );
        }
        
        $located = smart_rentals_wc_locate_template( $template_name, $template_path, $default_path );
        
        if ( !file_exists( $located ) ) {
            return;
        }
        
        include $located;
    }
}

/**
 * Locate template
 */
if ( !function_exists( 'smart_rentals_wc_locate_template' ) ) {
    function smart_rentals_wc_locate_template( $template_name, $template_path = '', $default_path = '' ) {
        if ( !$template_path ) {
            $template_path = 'smart-rentals-wc/';
        }
        
        if ( !$default_path ) {
            $default_path = SMART_RENTALS_WC_PLUGIN_TEMPLATES;
        }
        
        // Look within passed path within the theme
        $template = locate_template( trailingslashit( $template_path ) . $template_name );
        
        // Get default template
        if ( !$template ) {
            $template = trailingslashit( $default_path ) . $template_name;
        }
        
        return apply_filters( 'smart_rentals_wc_locate_template', $template, $template_name, $template_path );
    }
}

/**
 * Array merge unique
 */
if ( !function_exists( 'smart_rentals_wc_array_merge_unique' ) ) {
    function smart_rentals_wc_array_merge_unique( $array1, $array2 ) {
        return array_unique( array_merge( $array1, $array2 ) );
    }
}

/**
 * Get current timestamp
 */
if ( !function_exists( 'smart_rentals_wc_current_time' ) ) {
    function smart_rentals_wc_current_time() {
        return current_time( 'timestamp' );
    }
}

/**
 * Get security deposit for a product with global fallback
 */
if ( !function_exists( 'smart_rentals_wc_get_security_deposit' ) ) {
    function smart_rentals_wc_get_security_deposit( $product_id ) {
        // First check if product has its own security deposit
        $product_deposit = smart_rentals_wc_get_post_meta( $product_id, 'security_deposit' );
        
        if ( !empty( $product_deposit ) && floatval( $product_deposit ) > 0 ) {
            return floatval( $product_deposit );
        }
        
        // Fall back to global security deposit
        $settings = smart_rentals_wc_get_option( 'settings', [] );
        $global_deposit = smart_rentals_wc_get_meta_data( 'global_security_deposit', $settings, 0 );
        
        return floatval( $global_deposit );
    }
}

/**
 * Get turnaround time for hourly rentals
 */
if ( !function_exists( 'smart_rentals_wc_get_turnaround_time' ) ) {
    function smart_rentals_wc_get_turnaround_time( $product_id ) {
        // First check if product has its own turnaround time
        $product_turnaround = smart_rentals_wc_get_post_meta( $product_id, 'turnaround_time' );
        
        if ( !empty( $product_turnaround ) && floatval( $product_turnaround ) > 0 ) {
            return floatval( $product_turnaround );
        }
        
        // Fall back to global turnaround time (default 1 hour)
        $settings = smart_rentals_wc_get_option( 'settings', [] );
        $global_turnaround = smart_rentals_wc_get_meta_data( 'global_turnaround_time', $settings, 1 );
        
        return floatval( $global_turnaround );
    }
}

/**
 * Get current time
 */
if ( !function_exists( 'smart_rentals_wc_current_time' ) ) {
    function smart_rentals_wc_current_time() {
        return current_time( 'timestamp' );
    }
}