<?php
/**
 * Rental Product Availability Calendar Template
 * 
 * Displays a monthly calendar showing availability and daily pricing
 * This is for informational purposes only and does not affect the booking form
 */

if ( !defined( 'ABSPATH' ) ) exit;

// Get product data
$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
$daily_price = smart_rentals_wc_get_post_meta( $product_id, 'daily_price' );
$hourly_price = smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' );
$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );

// Get current month data
$current_month = isset( $_GET['cal_month'] ) ? intval( $_GET['cal_month'] ) : date( 'n' );
$current_year = isset( $_GET['cal_year'] ) ? intval( $_GET['cal_year'] ) : date( 'Y' );

// Validate month and year
if ( $current_month < 1 || $current_month > 12 ) {
    $current_month = date( 'n' );
}
if ( $current_year < date( 'Y' ) || $current_year > date( 'Y' ) + 2 ) {
    $current_year = date( 'Y' );
}

// Get month information
$first_day_of_month = mktime( 0, 0, 0, $current_month, 1, $current_year );
$days_in_month = date( 't', $first_day_of_month );
$first_day_of_week = date( 'w', $first_day_of_month ); // 0 = Sunday
$month_name = date( 'F Y', $first_day_of_month );

// Navigation URLs
$prev_month = $current_month - 1;
$prev_year = $current_year;
if ( $prev_month < 1 ) {
    $prev_month = 12;
    $prev_year--;
}

$next_month = $current_month + 1;
$next_year = $current_year;
if ( $next_month > 12 ) {
    $next_month = 1;
    $next_year++;
}

$base_url = get_permalink( $product_id );
$prev_url = add_query_arg( [ 'cal_month' => $prev_month, 'cal_year' => $prev_year ], $base_url );
$next_url = add_query_arg( [ 'cal_month' => $next_month, 'cal_year' => $next_year ], $base_url );

?>

<div class="smart-rentals-calendar-container">
    <div class="smart-rentals-calendar-header">
        <h3 class="calendar-title">
            <i class="dashicons dashicons-calendar-alt"></i>
            <?php _e( 'Availability Calendar', 'smart-rentals-wc' ); ?>
        </h3>
        <p class="calendar-description">
            <?php _e( 'View availability and daily pricing. This calendar is for information only.', 'smart-rentals-wc' ); ?>
        </p>
    </div>

    <div class="smart-rentals-calendar">
        <!-- Calendar Navigation -->
        <div class="calendar-nav">
            <a href="<?php echo esc_url( $prev_url ); ?>" class="nav-prev">
                <i class="dashicons dashicons-arrow-left-alt2"></i>
                <?php _e( 'Previous', 'smart-rentals-wc' ); ?>
            </a>
            <h4 class="current-month"><?php echo esc_html( $month_name ); ?></h4>
            <a href="<?php echo esc_url( $next_url ); ?>" class="nav-next">
                <?php _e( 'Next', 'smart-rentals-wc' ); ?>
                <i class="dashicons dashicons-arrow-right-alt2"></i>
            </a>
        </div>

        <!-- Calendar Grid -->
        <div class="calendar-grid">
            <!-- Day Headers -->
            <div class="calendar-header-row">
                <div class="day-header"><?php _e( 'Sun', 'smart-rentals-wc' ); ?></div>
                <div class="day-header"><?php _e( 'Mon', 'smart-rentals-wc' ); ?></div>
                <div class="day-header"><?php _e( 'Tue', 'smart-rentals-wc' ); ?></div>
                <div class="day-header"><?php _e( 'Wed', 'smart-rentals-wc' ); ?></div>
                <div class="day-header"><?php _e( 'Thu', 'smart-rentals-wc' ); ?></div>
                <div class="day-header"><?php _e( 'Fri', 'smart-rentals-wc' ); ?></div>
                <div class="day-header"><?php _e( 'Sat', 'smart-rentals-wc' ); ?></div>
            </div>

            <!-- Calendar Days -->
            <div class="calendar-body">
                <?php
                // Add empty cells for days before the first day of the month
                for ( $i = 0; $i < $first_day_of_week; $i++ ) {
                    echo '<div class="calendar-day empty"></div>';
                }

                // Add days of the month
                for ( $day = 1; $day <= $days_in_month; $day++ ) {
                    $date = sprintf( '%04d-%02d-%02d', $current_year, $current_month, $day );
                    $timestamp = mktime( 0, 0, 0, $current_month, $day, $current_year );
                    $is_today = ( $date === date( 'Y-m-d' ) );
                    $is_past = ( $timestamp < strtotime( 'today' ) );
                    
                    // Check availability for this date
                    $available_quantity = Smart_Rentals_WC()->options->get_available_quantity( $product_id, $timestamp, $timestamp + 86400 );
                    $is_available = ( $available_quantity > 0 );
                    
                    // Get price for this day
                    $price_display = '';
                    if ( $daily_price > 0 ) {
                        $price_display = smart_rentals_wc_price( $daily_price );
                    } elseif ( $hourly_price > 0 ) {
                        $price_display = smart_rentals_wc_price( $hourly_price ) . '/' . __( 'hour', 'smart-rentals-wc' );
                    }
                    
                    // Determine CSS classes
                    $classes = [ 'calendar-day' ];
                    if ( $is_today ) $classes[] = 'today';
                    if ( $is_past ) $classes[] = 'past';
                    if ( !$is_available || $is_past ) $classes[] = 'unavailable';
                    else $classes[] = 'available';
                    
                    echo '<div class="' . implode( ' ', $classes ) . '" data-date="' . $date . '">';
                    echo '<span class="day-number">' . $day . '</span>';
                    
                    if ( !$is_past ) {
                        if ( $is_available ) {
                            echo '<span class="availability-indicator available-count">' . $available_quantity . ' ' . __( 'available', 'smart-rentals-wc' ) . '</span>';
                            if ( $price_display ) {
                                echo '<span class="day-price">' . $price_display . '</span>';
                            }
                        } else {
                            echo '<span class="availability-indicator unavailable-text">' . __( 'Unavailable', 'smart-rentals-wc' ) . '</span>';
                        }
                    }
                    
                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- Calendar Legend -->
        <div class="calendar-legend">
            <div class="legend-item">
                <span class="legend-color available"></span>
                <span class="legend-text"><?php _e( 'Available', 'smart-rentals-wc' ); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color unavailable"></span>
                <span class="legend-text"><?php _e( 'Unavailable', 'smart-rentals-wc' ); ?></span>
            </div>
            <div class="legend-item">
                <span class="legend-color today"></span>
                <span class="legend-text"><?php _e( 'Today', 'smart-rentals-wc' ); ?></span>
            </div>
        </div>
    </div>
</div>