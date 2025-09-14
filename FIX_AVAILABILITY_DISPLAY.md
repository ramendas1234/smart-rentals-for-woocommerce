# Fix for Calendar Availability Display

## Problem
After booking 1 unit for Sep 25 (pickup) to Sep 26 (dropoff), the calendar was showing:
- Sep 25: 5 available (incorrect - should be 4)
- Sep 26: 4 available (incorrect - should be 5)

## Root Cause Analysis

The logic for daily rentals (hotel-style bookings) was implemented correctly:
- Pickup date (Sep 25) should be blocked
- Dropoff date (Sep 26) should be available (checkout day)

However, the display might have been affected by:
1. Date/time normalization issues
2. Timezone differences between stored and displayed dates
3. Caching of availability data

## Fixes Applied

### 1. Enhanced Date Normalization
- Ensured all dates are normalized to midnight (00:00:00) for consistent comparison
- Added explicit date format validation
- Improved timestamp calculations to avoid timezone issues

### 2. Improved Debug Logging
- Added comprehensive logging to track date comparisons
- Enhanced visibility into booking data and calculations
- Better tracking of which bookings affect which dates

### 3. Code Improvements
```php
// Before comparison, normalize all dates to start of day
$day_timestamp = strtotime( date( 'Y-m-d', strtotime( $date_string ) ) . ' 00:00:00' );
$booking_pickup = strtotime( date( 'Y-m-d', strtotime( $booking->pickup_date ) ) . ' 00:00:00' );
$booking_dropoff = strtotime( date( 'Y-m-d', strtotime( $booking->dropoff_date ) ) . ' 00:00:00' );

// Hotel logic: day is booked if pickup <= day < dropoff
$is_within_booking = ( $day_timestamp >= $booking_pickup && $day_timestamp < $booking_dropoff );
```

## Expected Behavior

For a booking from Sep 25 to Sep 26 with 1 unit (5 total stock):

| Date | Booked? | Available | Explanation |
|------|---------|-----------|-------------|
| Sep 24 | No | 5 | Before booking period |
| Sep 25 | Yes | 4 | Pickup date - guest occupies unit |
| Sep 26 | No | 5 | Dropoff date - guest checks out |
| Sep 27 | No | 5 | After booking period |

## Testing Steps

1. Enable WordPress debug logging:
   ```php
   define( 'WP_DEBUG', true );
   define( 'WP_DEBUG_LOG', true );
   ```

2. Create a test booking:
   - Product with 5 stock
   - Book 1 unit from Sep 25 to Sep 26

3. Check the calendar display:
   - Sep 25 should show "4 available"
   - Sep 26 should show "5 available"

4. Review debug logs for detailed calculation info:
   ```bash
   tail -f wp-content/debug.log | grep -E "CALENDAR AVAILABILITY|Calendar \(Daily\)"
   ```

## Additional Considerations

1. **Cache Clearing**: If using caching plugins, clear all caches after applying fixes
2. **Browser Cache**: Hard refresh (Ctrl+F5) to ensure latest JavaScript/CSS
3. **Timezone Settings**: Ensure WordPress timezone matches your business location
4. **Database Check**: Verify booking dates are stored correctly in `wp_smart_rentals_bookings`

## SQL to Verify Bookings

```sql
-- Check booking data
SELECT 
    order_id,
    product_id,
    pickup_date,
    dropoff_date,
    quantity,
    status
FROM wp_smart_rentals_bookings
WHERE product_id = [YOUR_PRODUCT_ID]
ORDER BY pickup_date DESC;
```

## Future Improvements

1. Add automated tests for availability calculations
2. Implement availability caching with proper invalidation
3. Add visual indicators in admin for booking conflicts
4. Create availability report for administrators