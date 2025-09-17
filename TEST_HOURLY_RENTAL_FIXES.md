# Test Hourly Rental Fixes

## Issues Fixed

### 1. Hourly Charge Display in Calendar
**Problem**: Hourly rental products didn't show pricing information in the availability calendar.

**Solution**: 
- Added pricing information section to calendar template
- Shows hourly rate for hourly/appointment rentals
- Shows daily rate for daily/hotel rentals  
- Shows both rates for mixed rentals
- Displays available units count

**Files Modified**:
- `templates/calendar.php` - Added pricing display section and enhanced styling

### 2. Availability Logic for Hourly Rentals
**Problem**: Calendar showed entire day as unavailable when only specific time slots were booked.

**Solution**:
- Created new `get_hourly_day_availability()` method for hourly rentals
- Enhanced `get_calendar_day_availability()` to handle different rental types
- Added turnaround time consideration for hourly rentals
- Improved availability calculation logic

**Files Modified**:
- `includes/class-smart-rentals-wc-get-data.php` - Added hourly availability logic
- `includes/smart-rentals-wc-core-functions.php` - Added turnaround time function

### 3. Stock Management for Multiple Bookings
**Problem**: When rental stock is 2 and two customers book at different times, calendar showed entire day as unavailable.

**Solution**:
- Enhanced calendar JavaScript to load availability data via AJAX
- Added visual indicators for partial availability (e.g., "1/2 available")
- Improved calendar styling to show availability status
- Added batch availability checking for better performance

**Files Modified**:
- `templates/calendar.php` - Enhanced JavaScript and CSS
- `includes/class-smart-rentals-wc-ajax.php` - Already had batch availability method

## Testing Instructions

### Test Case 1: Hourly Pricing Display
1. Create a product with:
   - Rental Type: Hourly
   - Hourly Price: $25.00
   - Rental Stock: 2
   - Enable Calendar: Yes

2. View the product page
3. **Expected Result**: Calendar should show "Hourly Rate: $25.00 per hour" and "Available Units: 2"

### Test Case 2: Multiple Hourly Bookings
1. Create a product with:
   - Rental Type: Hourly
   - Hourly Price: $25.00
   - Rental Stock: 2
   - Enable Calendar: Yes

2. Create two bookings:
   - Booking 1: Today 9:00 AM - 11:00 AM (1 unit)
   - Booking 2: Today 2:00 PM - 4:00 PM (1 unit)

3. View the product page calendar
4. **Expected Result**: 
   - Today should show "1/2 available" (not fully booked)
   - Calendar should allow selection of today for new bookings
   - Pricing should be visible

### Test Case 3: Mixed Rental Type
1. Create a product with:
   - Rental Type: Mixed
   - Daily Price: $100.00
   - Hourly Price: $25.00
   - Rental Stock: 3

2. View the product page
3. **Expected Result**: Calendar should show "Rates: $100.00 per day / $25.00 per hour" and "Available Units: 3"

### Test Case 4: Daily vs Hourly Logic
1. Create two products:
   - Product A: Daily rental, 1 unit, booked Sep 25-26
   - Product B: Hourly rental, 2 units, booked Sep 25 9AM-11AM (1 unit)

2. Check calendar for Sep 25:
   - Product A: Should show "Fully Booked" (daily logic)
   - Product B: Should show "1/2 available" (hourly logic)

## Debug Information

Enable WordPress debug logging to see detailed availability calculations:

```php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
```

Check logs for:
- "HOURLY CALENDAR AVAILABILITY" - Hourly rental calculations
- "CALENDAR AVAILABILITY" - Daily rental calculations
- "Hourly Calendar:" - Booking conflict detection

## Files Changed

1. `templates/calendar.php`
   - Added pricing information display
   - Enhanced JavaScript for availability loading
   - Improved CSS styling for availability indicators

2. `includes/class-smart-rentals-wc-get-data.php`
   - Added `get_hourly_day_availability()` method
   - Enhanced `get_calendar_day_availability()` method
   - Improved availability logic for different rental types

3. `includes/smart-rentals-wc-core-functions.php`
   - Added `smart_rentals_wc_get_turnaround_time()` function

## Expected Behavior Summary

| Rental Type | Stock | Bookings | Calendar Display |
|-------------|-------|----------|------------------|
| Hourly | 2 | 1 unit 9AM-11AM | "1/2 available" |
| Hourly | 2 | 2 units 9AM-11AM | "Fully Booked" |
| Daily | 2 | 1 unit Sep 25-26 | "1/2 available" on Sep 25 |
| Daily | 2 | 2 units Sep 25-26 | "Fully Booked" on Sep 25 |

## Notes

- Turnaround time is configurable per product or globally
- Calendar shows real-time availability via AJAX
- Pricing information is prominently displayed
- Visual indicators make availability status clear
- Responsive design works on mobile devices