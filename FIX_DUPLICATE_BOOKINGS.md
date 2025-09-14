# Fix for Duplicate Booking Records

## Problem Description
When placing an order from the frontend, two rows were being created in the `wp_smart_rentals_bookings` table:
1. First row with `order_id = 0`
2. Second row with the actual order ID

This caused the availability calendar to show incorrect quantities because bookings were counted twice (e.g., booking 2 units would be treated as 4 units).

## Root Cause
There were two places attempting to create booking records:

1. **`class-smart-rentals-wc-booking.php`**: Using `woocommerce_checkout_order_created` hook
   - This hook fires BEFORE the order is saved to the database
   - At this point, `$order->get_id()` returns 0
   - Created bookings with `order_id = 0`

2. **`class-smart-rentals-wc-sync-manager.php`**: Also using `woocommerce_checkout_order_created` hook
   - Same issue - order ID was 0

## Fix Applied

### 1. Changed Hook in Sync Manager
- Changed from `woocommerce_checkout_order_created` to `woocommerce_checkout_order_processed`
- This hook fires AFTER the order is saved and has a valid ID
- Updated method signature to handle the different hook parameters

### 2. Disabled Duplicate Creation
- Commented out the booking creation in `class-smart-rentals-wc-booking.php`
- All booking records are now centrally managed by the Sync Manager

### 3. Added Safety Checks
- Added validation to prevent creating bookings with `order_id = 0`
- Added logic to detect and update orphaned bookings
- Added cleanup function for old orphaned bookings

### 4. Orphaned Booking Handling
- When creating a new booking, check for orphaned records (order_id = 0)
- If found, update the orphaned record with the correct order ID
- Scheduled hourly cleanup to remove old orphaned bookings

## Code Changes

### File: `/workspace/includes/class-smart-rentals-wc-sync-manager.php`

1. **Changed hook**:
```php
// Old:
add_action( 'woocommerce_checkout_order_created', [ $this, 'sync_order_creation' ], 10, 1 );

// New:
add_action( 'woocommerce_checkout_order_processed', [ $this, 'sync_order_creation_after_save' ], 10, 3 );
```

2. **Updated method with validation**:
```php
public function sync_order_creation_after_save( $order_id, $posted_data, $order ) {
    // Ensure we have a valid order ID
    if ( !$order_id || $order_id === 0 ) {
        $this->log_sync_event( 'order_creation_error', [
            'message' => 'Order ID is 0 or invalid'
        ]);
        return;
    }
    // ... rest of the method
}
```

3. **Enhanced ensure_booking_record**:
- Skip if order ID is 0
- Check for orphaned bookings and update them
- Log all actions for debugging

4. **Added cleanup function**:
```php
public function cleanup_orphaned_bookings() {
    // Delete bookings with order_id = 0 older than 1 hour
}
```

### File: `/workspace/includes/class-smart-rentals-wc-booking.php`

1. **Disabled duplicate creation**:
```php
// DISABLED: Booking records are now created by the Sync Manager
/*
if ( $has_rental_items ) {
    $this->create_booking_records( $order );
}
*/
```

2. **Added safety check**:
```php
// CRITICAL: Prevent creating bookings with order_id = 0
if ( !$order_id || $order_id === 0 ) {
    smart_rentals_wc_log( 'WARNING: Attempted to create booking with order_id = 0. Skipping.' );
    return;
}
```

## Manual Cleanup SQL

To clean up existing orphaned bookings in the database:

```sql
-- View orphaned bookings
SELECT * FROM wp_smart_rentals_bookings WHERE order_id = 0;

-- Delete old orphaned bookings (older than 1 hour)
DELETE FROM wp_smart_rentals_bookings 
WHERE order_id = 0 
AND created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- For recent orphaned bookings, you may want to manually match them to orders
-- based on product_id, pickup_date, dropoff_date, and quantity
```

## Testing

1. Place a new order with rental products
2. Check the `wp_smart_rentals_bookings` table
3. Verify only ONE record is created with the correct order ID
4. Verify the availability calendar shows correct quantities

## Benefits

1. **No more duplicate bookings**: Only one booking record per order item
2. **Correct availability**: Calendar shows accurate available quantities
3. **Better data integrity**: All bookings have valid order IDs
4. **Automatic cleanup**: Orphaned records are cleaned up hourly
5. **Comprehensive logging**: All actions are logged for debugging