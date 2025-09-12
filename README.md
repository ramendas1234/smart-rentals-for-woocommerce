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

1. Upload the plugin files to `/wp-content/plugins/smart-rentals-for-woocommerce/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure the plugin settings under 'Smart Rentals' in your admin menu

## Requirements

- WordPress 5.0 or higher
- WooCommerce 4.0 or higher
- PHP 7.4 or higher

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