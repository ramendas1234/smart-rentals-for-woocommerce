# Debug Availability Calculation

## Test Case
- Product with 5 stock
- Booking 1 unit from Sep 25 to Sep 26
- Expected: Sep 25 shows 4 available, Sep 26 shows 5 available

## Debug Steps

1. **Date Format Check**
   - Calendar passes date as: `2024-09-25` (Y-m-d format)
   - Booking dates stored as: `2024-09-25 00:00:00` (Y-m-d H:i:s format)

2. **Timestamp Comparison**
   - Day timestamp for Sep 25: `strtotime('2024-09-25 00:00:00')`
   - Booking pickup: `strtotime('2024-09-25 00:00:00')`
   - Booking dropoff: `strtotime('2024-09-26 00:00:00')`
   
3. **Logic for Daily Rentals**
   ```php
   // The day is booked if: pickup <= day < dropoff
   if ( $day_timestamp >= $booking_pickup && $day_timestamp < $booking_dropoff )
   ```
   
   - Sep 25 (1727222400) >= Sep 25 (1727222400) ✓ AND Sep 25 < Sep 26 (1727308800) ✓ = BOOKED
   - Sep 26 (1727308800) >= Sep 25 (1727222400) ✓ AND Sep 26 < Sep 26 (1727308800) ✗ = NOT BOOKED

## Potential Issues

1. **Timezone Mismatch**: If dates are stored with different timezones
2. **Date Format**: If booking dates include time components that affect comparison
3. **Query Results**: If the booking query returns unexpected data

## Debug Code to Add

```php
// In get_calendar_day_availability, after line 515:
error_log("=== AVAILABILITY DEBUG ===");
error_log("Checking date: $date_string (timestamp: $day_timestamp)");
error_log("Booking: {$booking->pickup_date} to {$booking->dropoff_date}");
error_log("Pickup timestamp: $booking_pickup, Dropoff timestamp: $booking_dropoff");
error_log("Comparison: $day_timestamp >= $booking_pickup = " . ($day_timestamp >= $booking_pickup ? 'true' : 'false'));
error_log("Comparison: $day_timestamp < $booking_dropoff = " . ($day_timestamp < $booking_dropoff ? 'true' : 'false'));
error_log("Is booked: " . (($day_timestamp >= $booking_pickup && $day_timestamp < $booking_dropoff) ? 'YES' : 'NO'));
```