<?php
/**
 * Smart Rentals WC Install class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Install' ) ) {

	class Smart_Rentals_WC_Install {

		/**
		 * Install plugin
		 */
		public static function install() {
			// Create database tables
			self::create_tables();

			// Set default options
			self::set_default_options();

			// Create default pages
			self::create_pages();

			// Schedule cron events
			self::schedule_cron_events();

			// Set installed flag
			update_option( 'smart_rentals_wc_installed', true );
			
			// Get version from plugin file
			$plugin_data = get_plugin_data( SMART_RENTALS_WC_PLUGIN_FILE, false, false );
			$version = isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : '1.0.0';
			update_option( 'smart_rentals_wc_version', $version );
		}

		/**
		 * Create database tables
		 */
		private static function create_tables() {
			global $wpdb;

			$charset_collate = $wpdb->get_charset_collate();

			// Rental bookings table
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				order_id mediumint(9) NOT NULL,
				product_id mediumint(9) NOT NULL,
				pickup_date datetime NOT NULL,
				dropoff_date datetime NOT NULL,
				pickup_location text,
				dropoff_location text,
				quantity int(11) DEFAULT 1,
				status varchar(20) DEFAULT 'pending',
				total_price decimal(10,2) DEFAULT 0.00,
				deposit_amount decimal(10,2) DEFAULT 0.00,
				security_deposit decimal(10,2) DEFAULT 0.00,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY order_id (order_id),
				KEY product_id (product_id),
				KEY pickup_date (pickup_date),
				KEY dropoff_date (dropoff_date),
				KEY status (status)
			) $charset_collate;";

			require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
			dbDelta( $sql );

			// Rental availability table (for custom availability rules)
			$table_name = $wpdb->prefix . 'smart_rentals_availability';
			
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				product_id mediumint(9) NOT NULL,
				date_from date NOT NULL,
				date_to date NOT NULL,
				available_quantity int(11) DEFAULT 0,
				price_override decimal(10,2) DEFAULT NULL,
				notes text,
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY product_id (product_id),
				KEY date_from (date_from),
				KEY date_to (date_to)
			) $charset_collate;";

			dbDelta( $sql );

			// Rental resources table
			$table_name = $wpdb->prefix . 'smart_rentals_resources';
			
			$sql = "CREATE TABLE $table_name (
				id mediumint(9) NOT NULL AUTO_INCREMENT,
				name varchar(255) NOT NULL,
				description text,
				price decimal(10,2) DEFAULT 0.00,
				price_type varchar(20) DEFAULT 'fixed',
				status varchar(20) DEFAULT 'active',
				created_at datetime DEFAULT CURRENT_TIMESTAMP,
				PRIMARY KEY (id),
				KEY status (status)
			) $charset_collate;";

			dbDelta( $sql );
		}

		/**
		 * Set default options
		 */
		private static function set_default_options() {
			$default_settings = [
				'date_format' => 'Y-m-d',
				'time_format' => 'H:i',
				'enable_calendar' => 'yes',
				'enable_deposits' => 'no',
				'currency_position' => 'left',
				'decimal_separator' => '.',
				'thousand_separator' => ',',
				'number_of_decimals' => 2,
			];

			smart_rentals_wc_update_option( 'settings', $default_settings );

			// Email settings
			$email_settings = [
				'enable_confirmation_email' => 'yes',
				'enable_pickup_reminder' => 'yes',
				'enable_return_reminder' => 'yes',
				'pickup_reminder_hours' => 24,
				'return_reminder_hours' => 24,
			];

			smart_rentals_wc_update_option( 'email_settings', $email_settings );
		}

		/**
		 * Create default pages
		 */
		private static function create_pages() {
			// Create rental search page
			$search_page = wp_insert_post([
				'post_title' => __( 'Rental Search', 'smart-rentals-wc' ),
				'post_content' => '[smart_rentals_search]',
				'post_status' => 'publish',
				'post_type' => 'page',
				'post_author' => get_current_user_id(),
			]);

			if ( $search_page ) {
				smart_rentals_wc_update_option( 'search_page_id', $search_page );
			}
		}

		/**
		 * Schedule cron events
		 */
		private static function schedule_cron_events() {
			if ( !wp_next_scheduled( 'smart_rentals_wc_daily_tasks' ) ) {
				wp_schedule_event( time(), 'daily', 'smart_rentals_wc_daily_tasks' );
			}

			if ( !wp_next_scheduled( 'smart_rentals_wc_hourly_tasks' ) ) {
				wp_schedule_event( time(), 'hourly', 'smart_rentals_wc_hourly_tasks' );
			}
		}

		/**
		 * Uninstall plugin
		 */
		public static function uninstall() {
			global $wpdb;

			// Remove tables
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}smart_rentals_bookings" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}smart_rentals_availability" );
			$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}smart_rentals_resources" );

			// Remove options
			$wpdb->query( "DELETE FROM {$wpdb->options} WHERE option_name LIKE 'smart_rentals_wc_%'" );

			// Clear cron events
			wp_clear_scheduled_hook( 'smart_rentals_wc_daily_tasks' );
			wp_clear_scheduled_hook( 'smart_rentals_wc_hourly_tasks' );
		}
	}
}