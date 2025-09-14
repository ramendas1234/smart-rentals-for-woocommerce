# Calendar Availability Test Cases

## Fixed Issues

### Issue 1: Incorrect Quantity Display After Booking
**Problem**: When 2 out of 5 items were booked for Sep 25-26, the calendar showed:
- Sep 25: 1 available (should be 3)
- Sep 26: 1 available (should be 5)

**Root Cause**: The availability calculation was incorrectly including the dropoff date as a booked day for daily rentals.

**Fix Applied**: 
- For daily rentals, the dropoff date is now excluded from the booking period (hotel logic)
- A booking from Sep 25 to Sep 26 only blocks Sep 25, not Sep 26
- The guest checks out on Sep 26, making it available for new bookings

### Issue 2: Admin Modification Not Updating Availability
**Problem**: When admin changes booking dates from Sep 25-26 to Sep 27-28:
- Old dates (Sep 25-26) didn't show restored availability
- New dates (Sep 27-28) didn't show reduced availability

**Root Cause**: The sync manager wasn't tracking and invalidating both old and new date ranges when bookings were modified.

**Fix Applied**:
- Track both old and new date ranges when bookings are modified
- Invalidate cache for all affected dates
- Trigger real-time updates for calendar refresh
- Proper handling of daily rental logic (dropoff date not included)

## Test Scenarios

### Test 1: Basic Daily Rental Booking
```
Product: Daily Rental Item
Stock: 5 units
Booking: Sep 25 to Sep 26 (2 units)

Expected Calendar Display:
- Sep 24: 5 available ✓
- Sep 25: 3 available ✓ (5 - 2 booked)
- Sep 26: 5 available ✓ (dropoff date, not blocked)
- Sep 27: 5 available ✓
```

### Test 2: Multiple Overlapping Bookings
```
Product: Daily Rental Item
Stock: 5 units
Booking 1: Sep 25 to Sep 27 (2 units)
Booking 2: Sep 26 to Sep 28 (1 unit)

Expected Calendar Display:
- Sep 24: 5 available ✓
- Sep 25: 3 available ✓ (5 - 2 from Booking 1)
- Sep 26: 2 available ✓ (5 - 2 from Booking 1 - 1 from Booking 2)
- Sep 27: 4 available ✓ (5 - 1 from Booking 2, Booking 1 ends)
- Sep 28: 5 available ✓ (all returned)
```

### Test 3: Admin Modification
```
Product: Daily Rental Item
Stock: 5 units
Original Booking: Sep 25 to Sep 26 (2 units)
Admin Changes To: Sep 27 to Sep 28 (2 units)

Expected Calendar Display After Modification:
- Sep 25: 5 available ✓ (restored to full stock)
- Sep 26: 5 available ✓ (was never blocked)
- Sep 27: 3 available ✓ (5 - 2 newly booked)
- Sep 28: 5 available ✓ (dropoff date)
```

### Test 4: Hourly/Mixed Rentals (Different Logic)
```
Product: Hourly Rental Item
Stock: 5 units
Booking: Sep 25 09:00 to Sep 26 15:00 (2 units)
Turnaround Time: 2 hours

Expected Calendar Display:
- Sep 25: 3 available ✓ (blocked for usage)
- Sep 26: 3 available ✓ (blocked until 17:00 including turnaround)
- Sep 27: 5 available ✓ (fully available)
```

## Debug Commands

Enable debug logging to verify the fix:
```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

Check logs for availability calculations:
```bash
tail -f wp-content/debug.log | grep -E "Calendar|Availability"
```

## Code Changes Summary

1. **`class-smart-rentals-wc-get-data.php`**:
   - Modified `get_calendar_day_availability()` method
   - Added rental type check for daily vs hourly/mixed
   - For daily rentals: only count days where `pickup <= day < dropoff`
   - For hourly/mixed: maintain existing overlap logic with turnaround

2. **`class-smart-rentals-wc-sync-manager.php`**:
   - Enhanced `sync_admin_order_modification()` method
   - Track both old and new date ranges
   - Invalidate cache for all affected dates
   - Added `invalidate_availability_cache()` method

3. **Calendar Display Logic**:
   - Daily rentals follow hotel booking pattern
   - Checkout date is available for new bookings
   - Proper quantity calculation based on actual usage days

## Verification Steps

1. Create a daily rental product with 5 stock
2. Book 2 units from Sep 25 to Sep 26
3. Verify calendar shows:
   - Sep 25: 3 available
   - Sep 26: 5 available

4. In admin, modify the booking to Sep 27 to Sep 28
5. Verify calendar updates to show:
   - Sep 25: 5 available (restored)
   - Sep 26: 5 available (unchanged)
   - Sep 27: 3 available (new booking)
   - Sep 28: 5 available (checkout day)

6. Check debug log for proper calculations

## Notes

- The fix maintains backward compatibility
- Hourly and mixed rental types continue to work as before
- The solution properly handles turnaround times for non-daily rentals
- Cache invalidation ensures real-time updates
- Database integrity is maintained throughout modifications