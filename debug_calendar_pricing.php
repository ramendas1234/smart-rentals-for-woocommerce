<?php
/**
 * Debug Calendar Pricing - Test Script
 * 
 * This script helps debug calendar pricing issues by checking meta values
 * Run this on a product page to see what values are being retrieved
 */

// Only run if accessed directly and WP is loaded
if ( !defined( 'ABSPATH' ) ) {
    // Try to load WordPress
    $wp_load_paths = [
        '../../../wp-load.php',
        '../../wp-load.php',
        '../wp-load.php',
        'wp-load.php'
    ];
    
    foreach ( $wp_load_paths as $path ) {
        if ( file_exists( $path ) ) {
            require_once $path;
            break;
        }
    }
    
    if ( !defined( 'ABSPATH' ) ) {
        die( 'WordPress not found. Please run this from a WordPress environment.' );
    }
}

// Get product ID from URL parameter
$product_id = isset( $_GET['product_id'] ) ? intval( $_GET['product_id'] ) : 0;

if ( !$product_id ) {
    die( 'Please provide a product_id parameter. Example: ?product_id=123' );
}

echo "<h2>Calendar Pricing Debug for Product ID: $product_id</h2>";

// Check if product exists
$product = wc_get_product( $product_id );
if ( !$product ) {
    die( "Product with ID $product_id not found." );
}

echo "<h3>Product Information</h3>";
echo "Product Name: " . esc_html( $product->get_name() ) . "<br>";
echo "Product Type: " . esc_html( $product->get_type() ) . "<br>";

// Check if it's a rental product
$is_rental = smart_rentals_wc_is_rental_product( $product_id );
echo "Is Rental Product: " . ( $is_rental ? 'Yes' : 'No' ) . "<br>";

if ( !$is_rental ) {
    die( "This product is not configured as a rental product." );
}

echo "<h3>Meta Values</h3>";

// Check rental type
$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
$raw_rental_type = get_post_meta( $product_id, 'smart_rentals_rental_type', true );
echo "Rental Type (via function): '$rental_type'<br>";
echo "Rental Type (raw): '$raw_rental_type'<br>";

// Check daily price
$daily_price = smart_rentals_wc_get_post_meta( $product_id, 'daily_price' );
$raw_daily_price = get_post_meta( $product_id, 'smart_rentals_daily_price', true );
echo "Daily Price (via function): '$daily_price'<br>";
echo "Daily Price (raw): '$raw_daily_price'<br>";

// Check hourly price
$hourly_price = smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' );
$raw_hourly_price = get_post_meta( $product_id, 'smart_rentals_hourly_price', true );
echo "Hourly Price (via function): '$hourly_price'<br>";
echo "Hourly Price (raw): '$raw_hourly_price'<br>";

// Check rental stock
$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
$raw_rental_stock = get_post_meta( $product_id, 'smart_rentals_rental_stock', true );
echo "Rental Stock (via function): '$rental_stock'<br>";
echo "Rental Stock (raw): '$raw_rental_stock'<br>";

// Check enable calendar
$enable_calendar = smart_rentals_wc_get_post_meta( $product_id, 'enable_calendar' );
$raw_enable_calendar = get_post_meta( $product_id, 'smart_rentals_enable_calendar', true );
echo "Enable Calendar (via function): '$enable_calendar'<br>";
echo "Enable Calendar (raw): '$raw_enable_calendar'<br>";

echo "<h3>Calendar Logic Test</h3>";

// Test the calendar logic
if ( $rental_type === 'hour' || $rental_type === 'appointment' ) {
    echo "✓ Should show HOURLY pricing<br>";
    if ( $hourly_price > 0 ) {
        echo "✓ Hourly price is set: " . smart_rentals_wc_price( $hourly_price ) . " per hour<br>";
    } else {
        echo "✗ Hourly price is not set or is 0<br>";
    }
} elseif ( $rental_type === 'day' || $rental_type === 'hotel' ) {
    echo "✓ Should show DAILY pricing<br>";
    if ( $daily_price > 0 ) {
        echo "✓ Daily price is set: " . smart_rentals_wc_price( $daily_price ) . " per day<br>";
    } else {
        echo "✗ Daily price is not set or is 0<br>";
    }
} elseif ( $rental_type === 'mixed' ) {
    echo "✓ Should show MIXED pricing<br>";
    if ( $daily_price > 0 || $hourly_price > 0 ) {
        echo "✓ Mixed prices: Daily: " . smart_rentals_wc_price( $daily_price ) . ", Hourly: " . smart_rentals_wc_price( $hourly_price ) . "<br>";
    } else {
        echo "✗ Neither daily nor hourly price is set<br>";
    }
} else {
    echo "✗ Unknown rental type: '$rental_type'<br>";
}

echo "<h3>All Meta Keys for This Product</h3>";
$all_meta = get_post_meta( $product_id );
foreach ( $all_meta as $key => $value ) {
    if ( strpos( $key, 'smart_rentals_' ) === 0 ) {
        echo "<strong>$key:</strong> " . ( is_array( $value ) ? implode( ', ', $value ) : $value[0] ) . "<br>";
    }
}

echo "<h3>Instructions</h3>";
echo "1. Make sure the product has rental enabled<br>";
echo "2. Set the rental type to 'hour' or 'appointment'<br>";
echo "3. Set an hourly price (e.g., 25.00)<br>";
echo "4. Enable calendar display<br>";
echo "5. Save the product<br>";
echo "6. View the product page to see the calendar<br>";

echo "<h3>Quick Fix Test</h3>";
echo "If hourly pricing is not showing, try:<br>";
echo "1. Go to the product edit page<br>";
echo "2. Change rental type to 'Daily' and save<br>";
echo "3. Change rental type back to 'Hourly' and save<br>";
echo "4. Set hourly price and save<br>";
echo "5. Refresh this debug page<br>";
?>