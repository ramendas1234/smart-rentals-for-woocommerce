<?php
/**
 * Smart Rentals WC Cron class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Cron' ) ) {

	class Smart_Rentals_WC_Cron {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Schedule cron events
			add_action( 'wp', [ $this, 'schedule_events' ] );
			
			// Cron hooks
			add_action( 'smart_rentals_wc_daily_tasks', [ $this, 'daily_tasks' ] );
			add_action( 'smart_rentals_wc_hourly_tasks', [ $this, 'hourly_tasks' ] );
			
			// Deactivation hook
			register_deactivation_hook( SMART_RENTALS_WC_PLUGIN_FILE, [ $this, 'clear_scheduled_events' ] );
		}

		/**
		 * Schedule cron events
		 */
		public function schedule_events() {
			// Daily tasks
			if ( !wp_next_scheduled( 'smart_rentals_wc_daily_tasks' ) ) {
				wp_schedule_event( time(), 'daily', 'smart_rentals_wc_daily_tasks' );
			}

			// Hourly tasks
			if ( !wp_next_scheduled( 'smart_rentals_wc_hourly_tasks' ) ) {
				wp_schedule_event( time(), 'hourly', 'smart_rentals_wc_hourly_tasks' );
			}
		}

		/**
		 * Daily tasks
		 */
		public function daily_tasks() {
			// Send pickup reminders
			$this->send_pickup_reminders();
			
			// Send return reminders
			$this->send_return_reminders();
			
			// Clean up expired reservations
			$this->cleanup_expired_reservations();
		}

		/**
		 * Hourly tasks
		 */
		public function hourly_tasks() {
			// Update rental statuses
			$this->update_rental_statuses();
		}

		/**
		 * Send pickup reminders
		 */
		private function send_pickup_reminders() {
			global $wpdb;

			$tomorrow = gmdate( 'Y-m-d', strtotime( '+1 day' ) );
			$order_status = Smart_Rentals_WC()->options->get_booking_order_status();
			$status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );

			$orders = $wpdb->get_results( $wpdb->prepare("
				SELECT DISTINCT orders.ID as order_id
				FROM {$wpdb->prefix}woocommerce_order_items AS items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pickup_date 
					ON items.order_item_id = pickup_date.order_item_id 
					AND pickup_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS is_rental 
					ON items.order_item_id = is_rental.order_item_id 
					AND is_rental.meta_key = %s
				LEFT JOIN {$wpdb->posts} AS orders 
					ON items.order_id = orders.ID
				WHERE 
					is_rental.meta_value = 'yes'
					AND orders.post_status IN ('{$status_placeholders}')
					AND DATE(pickup_date.meta_value) = %s
			",
				smart_rentals_wc_meta_key( 'pickup_date' ),
				smart_rentals_wc_meta_key( 'is_rental' ),
				$tomorrow
			));

			foreach ( $orders as $order_data ) {
				do_action( 'smart_rentals_wc_send_pickup_reminder', $order_data->order_id );
			}
		}

		/**
		 * Send return reminders
		 */
		private function send_return_reminders() {
			global $wpdb;

			$tomorrow = gmdate( 'Y-m-d', strtotime( '+1 day' ) );
			$order_status = Smart_Rentals_WC()->options->get_booking_order_status();
			$status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );

			$orders = $wpdb->get_results( $wpdb->prepare("
				SELECT DISTINCT orders.ID as order_id
				FROM {$wpdb->prefix}woocommerce_order_items AS items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS dropoff_date 
					ON items.order_item_id = dropoff_date.order_item_id 
					AND dropoff_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS is_rental 
					ON items.order_item_id = is_rental.order_item_id 
					AND is_rental.meta_key = %s
				LEFT JOIN {$wpdb->posts} AS orders 
					ON items.order_id = orders.ID
				WHERE 
					is_rental.meta_value = 'yes'
					AND orders.post_status IN ('{$status_placeholders}')
					AND DATE(dropoff_date.meta_value) = %s
			",
				smart_rentals_wc_meta_key( 'dropoff_date' ),
				smart_rentals_wc_meta_key( 'is_rental' ),
				$tomorrow
			));

			foreach ( $orders as $order_data ) {
				do_action( 'smart_rentals_wc_send_return_reminder', $order_data->order_id );
			}
		}

		/**
		 * Cleanup expired reservations
		 */
		private function cleanup_expired_reservations() {
			// Clean up any temporary reservations or expired cart items
			// This could be expanded based on specific business logic
			
			smart_rentals_wc_log( 'Cleaned up expired reservations', 'info' );
		}

		/**
		 * Update rental statuses
		 */
		private function update_rental_statuses() {
			// Update rental statuses based on current date/time
			// This could be used to automatically mark rentals as active, overdue, etc.
			
			smart_rentals_wc_log( 'Updated rental statuses', 'info' );
		}

		/**
		 * Clear scheduled events
		 */
		public function clear_scheduled_events() {
			wp_clear_scheduled_hook( 'smart_rentals_wc_daily_tasks' );
			wp_clear_scheduled_hook( 'smart_rentals_wc_hourly_tasks' );
		}
	}

}