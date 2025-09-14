<?php
/**
 * Test script to verify availability calculation logic
 * 
 * Test case: 5 stock, 1 unit booked from Sep 25 to Sep 26
 * Expected: Sep 25 = 4 available, Sep 26 = 5 available
 */

// Simulate the date normalization
function test_availability_logic() {
    echo "=== Testing Availability Logic ===\n\n";
    
    // Test data
    $total_stock = 5;
    $booking_pickup_date = '2024-09-25 00:00:00';
    $booking_dropoff_date = '2024-09-26 00:00:00';
    $booking_quantity = 1;
    
    // Dates to check
    $test_dates = [
        '2024-09-24',
        '2024-09-25', 
        '2024-09-26',
        '2024-09-27'
    ];
    
    foreach ($test_dates as $date_string) {
        echo "Checking date: $date_string\n";
        
        // Normalize the date to check (start of day)
        $day_timestamp = strtotime(date('Y-m-d', strtotime($date_string)) . ' 00:00:00');
        
        // Normalize booking dates
        $booking_pickup = strtotime(date('Y-m-d', strtotime($booking_pickup_date)) . ' 00:00:00');
        $booking_dropoff = strtotime(date('Y-m-d', strtotime($booking_dropoff_date)) . ' 00:00:00');
        
        echo "  Day timestamp: $day_timestamp (" . date('Y-m-d H:i:s', $day_timestamp) . ")\n";
        echo "  Pickup timestamp: $booking_pickup (" . date('Y-m-d H:i:s', $booking_pickup) . ")\n";
        echo "  Dropoff timestamp: $booking_dropoff (" . date('Y-m-d H:i:s', $booking_dropoff) . ")\n";
        
        // Apply hotel logic: booked if pickup <= day < dropoff
        $is_booked = ($day_timestamp >= $booking_pickup && $day_timestamp < $booking_dropoff);
        $booked_quantity = $is_booked ? $booking_quantity : 0;
        $available = $total_stock - $booked_quantity;
        
        echo "  Is booked: " . ($is_booked ? 'YES' : 'NO') . "\n";
        echo "  Available: $available\n";
        echo "  ---\n";
        
        // Verify expected results
        if ($date_string == '2024-09-25' && $available != 4) {
            echo "  ERROR: Sep 25 should show 4 available, but shows $available\n";
        }
        if ($date_string == '2024-09-26' && $available != 5) {
            echo "  ERROR: Sep 26 should show 5 available, but shows $available\n";
        }
    }
    
    echo "\n=== Test Complete ===\n";
}

test_availability_logic();