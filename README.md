# Smart Rentals For WooCommerce

A comprehensive WooCommerce rental and booking plugin with advanced features for equipment rental, car rental, hotel booking, and more.

## Features

### 1. Fast Rental Product Setup
- Convert any WooCommerce product to rental with a simple checkbox
- Multiple rental types: Daily, Hourly, Mixed, Package/Period, Transportation, Hotel, Appointment, Taxi/Distance
- Flexible pricing options with daily and hourly rates
- Minimum and maximum rental periods

### 2. Advanced Pricing & Deposits
- Fixed or percentage-based pricing
- Security deposits with refundable options
- Partial payment deposits on checkout
- Dynamic pricing calculation based on rental duration

### 3. Live Booking & Availability Tools
- Real-time availability checking
- Calendar integration for date selection
- Inventory management with stock control
- Automatic price calculation

### 4. Dashboard & Admin Controls
- Comprehensive rental dashboard
- Booking management and overview
- Product filtering and bulk actions
- Rental statistics and reporting

### 5. Automation & Emails
- Automatic rental confirmation emails
- Pickup and return reminder emails
- Scheduled tasks via WordPress cron
- Customizable email templates

## Installation

### Method 1: Manual Installation
1. Download the plugin files from the repository
2. Upload the entire `smart-rentals-for-woocommerce` folder to `/wp-content/plugins/`
3. Activate the plugin through the 'Plugins' menu in WordPress
4. The plugin will automatically create necessary database tables
5. Configure the plugin settings under 'Smart Rentals' in your admin menu

### Method 2: Git Clone
```bash
cd /wp-content/plugins/
git clone https://github.com/ramendas1234/smart-rentals-for-woocommerce.git
git checkout plugin-development
```

### Post-Installation Steps
1. Go to **Smart Rentals → Settings** and configure basic settings
2. Visit **Smart Rentals → Debug Info** to verify installation
3. Create your first rental product (see Usage section below)

## Requirements

- WordPress 5.0 or higher
- WooCommerce 4.0 or higher
- PHP 7.4 or higher
- MySQL 5.6 or higher

## Troubleshooting

### Common Issues

**Issue: "WC_Product class not found"**
- **Solution**: Ensure WooCommerce is installed and activated before our plugin
- **Check**: Go to Smart Rentals → Debug Info to verify WooCommerce status

**Issue: "Rental fields not showing"**
- **Solution**: Make sure you're editing a product and have checked the "Rental Product" checkbox
- **Check**: Look for JavaScript errors in browser console

**Issue: "Database tables missing"**
- **Solution**: Deactivate and reactivate the plugin, or visit Debug Info page and click "Recreate Database Tables"

**Issue: "Booking form not appearing"**
- **Solution**: Check that the product has "Rental Product" checkbox enabled and rental type is selected

## Usage

### Making a Product Rentable

1. Edit any WooCommerce product
2. In the General tab, check "Rental Product"
3. Configure rental settings:
   - Select rental type
   - Set daily/hourly prices
   - Configure minimum/maximum rental periods
   - Set rental stock quantity
   - Add security deposit if needed

### Step-by-Step Testing Guide

#### 1. **Create Your First Rental Product**
```
1. Go to Products → Add New (or edit existing product)
2. In General tab, check ☑️ "Rental Product"
3. Select "Rental Type" (e.g., "Daily")
4. Set "Daily Price" (e.g., 50.00)
5. Set "Rental Stock" (e.g., 5)
6. Optionally set "Security Deposit" (e.g., 100.00)
7. Check ☑️ "Show Calendar" if desired
8. Save/Update the product
```

#### 2. **Test Frontend Booking**
```
1. Visit the product page on frontend
2. You should see "Rental Details" section with date fields
3. Select a pickup date (today or later)
4. Select a drop-off date (after pickup date)
5. Watch the price calculate automatically
6. Click "Add to Cart"
7. Check cart - should show rental details
8. Complete checkout process
```

#### 3. **Verify in Admin**
```
1. Go to Smart Rentals → Dashboard
2. Check statistics are updating
3. Go to Smart Rentals → Bookings
4. Verify your test booking appears
5. Go to Smart Rentals → Debug Info
6. Verify all systems are working
```

#### 4. **Test Different Rental Types**
```
- Daily: Set daily price, test multi-day rentals
- Hourly: Set hourly price, test with time fields  
- Mixed: Set both prices, test 24+ hour vs shorter rentals
- Hotel: Test nightly accommodation booking
- Appointment: Test precise time slot booking
- Period/Package: Test package-based pricing
- Transportation: Test fixed trip pricing
- Taxi: Test time/distance-based pricing
```

#### 5. **Test Modern Date Picker**
```
1. Click on pickup or dropoff date field
2. Beautiful daterangepicker.com calendar opens
3. Select pickup date → See start date highlighted
4. Select dropoff date → See range highlighting
5. Try quick preset buttons (Today, Next Week, etc.)
6. Click APPLY button → AJAX calculation triggers ONCE
7. Verify individual dates show in each field
8. Test on mobile for responsive design
```

### Rental Types

- **Daily**: Charged per day
- **Hourly**: Charged per hour
- **Mixed**: Automatically switches between daily/hourly based on duration
- **Package/Period**: Fixed packages with set durations
- **Transportation**: Location-based pricing
- **Hotel**: Accommodation booking with nightly rates
- **Appointment**: Time slot booking
- **Taxi/Distance**: Distance-based pricing

### Frontend Features

Customers can:
- Select pickup and drop-off dates
- Choose time slots (for hourly rentals)
- View real-time pricing
- Check availability
- See rental duration and total cost

### Admin Features

- Rental product management
- Booking calendar overview
- Order management with rental details
- Email automation settings
- Comprehensive reporting

## Shortcodes

### Display Rental Products
```
[smart_rentals_products limit="12" columns="4" rental_type="day"]
```

### Rental Search Form
```
[smart_rentals_search show_location="yes" show_dates="yes" show_times="no"]
```

### Product Calendar
```
[smart_rentals_calendar product_id="123" height="600px"]
```

## Hooks & Filters

### Actions
- `smart_rentals_wc_before_booking_form` - Before booking form display
- `smart_rentals_wc_after_booking_form` - After booking form display
- `smart_rentals_wc_send_pickup_reminder` - Send pickup reminder email
- `smart_rentals_wc_send_return_reminder` - Send return reminder email

### Filters
- `smart_rentals_wc_rental_types` - Modify available rental types
- `smart_rentals_wc_calculate_rental_price` - Modify price calculation
- `smart_rentals_wc_booking_order_status` - Modify order statuses for bookings
- `smart_rentals_wc_email_message` - Customize email messages

## Template Override

You can override plugin templates by copying them to your theme:

1. Create folder: `your-theme/smart-rentals-wc/`
2. Copy template files from `plugins/smart-rentals-for-woocommerce/templates/`
3. Customize as needed

## Development

### File Structure
```
smart-rentals-for-woocommerce/
├── admin/                     # Admin functionality
├── assets/                    # CSS, JS, images
├── includes/                  # Core classes
├── languages/                 # Translation files
├── templates/                 # Template files
└── smart-rentals-for-woocommerce.php
```

### Key Classes
- `Smart_Rentals_WC` - Main plugin class
- `Smart_Rentals_WC_Admin` - Admin functionality
- `Smart_Rentals_WC_Booking` - Booking management
- `Smart_Rentals_WC_Rental` - Rental functionality
- `Smart_Rentals_WC_Get_Data` - Data management

## Support

For support and feature requests, please visit our [support page](https://smartrentals.com/support).

## License

This plugin is licensed under the GPL v2 or later.

## Changelog

### 1.0.0
- Initial release
- Basic rental functionality
- Admin interface
- Frontend booking form
- Email notifications
- Calendar integration
- Multiple rental types support