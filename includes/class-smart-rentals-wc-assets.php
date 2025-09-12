<?php
/**
 * Smart Rentals WC Assets class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Assets' ) ) {

	class Smart_Rentals_WC_Assets {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Enqueue frontend scripts
			add_action( 'wp_enqueue_scripts', [ $this, 'frontend_scripts' ] );
			
			// Enqueue frontend styles
			add_action( 'wp_enqueue_scripts', [ $this, 'frontend_styles' ] );
		}

		/**
		 * Frontend scripts
		 */
		public function frontend_scripts() {
			// Load Moment.js (required for daterangepicker)
			wp_enqueue_script(
				'moment',
				'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js',
				[ 'jquery' ],
				'2.29.4',
				true
			);

			// Load daterangepicker library
			wp_enqueue_script(
				'daterangepicker',
				'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js',
				[ 'jquery', 'moment' ],
				'3.14.1',
				true
			);

			// Always load frontend scripts for AJAX functionality
			wp_enqueue_script(
				'smart-rentals-wc-frontend',
				SMART_RENTALS_WC_PLUGIN_URI . 'assets/js/frontend.js',
				[ 'jquery', 'moment', 'daterangepicker' ],
				Smart_Rentals_WC()->get_version(),
				true
			);

			// Error messages
			wp_localize_script( 'smart-rentals-wc-frontend', 'smartRentalsErrorMessages', [
				'select_dates' => __( 'Please select pickup and drop-off dates.', 'smart-rentals-wc' ),
				'invalid_dates' => __( 'Drop-off date must be after pickup date.', 'smart-rentals-wc' ),
				'loading' => __( 'Loading...', 'smart-rentals-wc' ),
				'error' => __( 'An error occurred. Please try again.', 'smart-rentals-wc' ),
				'validation_failed' => __( 'Please fill in all required fields.', 'smart-rentals-wc' ),
				'add_to_cart_success' => __( 'Product added to cart successfully!', 'smart-rentals-wc' ),
				'add_to_cart_failed' => __( 'Failed to add product to cart.', 'smart-rentals-wc' ),
			]);

			// AJAX object
			wp_localize_script( 'smart-rentals-wc-frontend', 'ajax_object', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'smart-rentals-security-ajax' ),
			]);

			// Date picker options
			wp_localize_script( 'smart-rentals-wc-frontend', 'datePickerOptions', [
				'format' => Smart_Rentals_WC()->options->get_date_format(),
				'timeFormat' => Smart_Rentals_WC()->options->get_time_format(),
				'firstDay' => 1,
				'minDate' => gmdate( 'Y-m-d' ),
			]);

			// Load on cart and checkout pages
			if ( is_cart() || is_checkout() ) {
				wp_enqueue_script(
					'smart-rentals-wc-cart',
					SMART_RENTALS_WC_PLUGIN_URI . 'assets/js/cart.js',
					[ 'jquery' ],
					Smart_Rentals_WC()->get_version(),
					true
				);
			}
		}

		/**
		 * Frontend styles
		 */
		public function frontend_styles() {
			// Load daterangepicker styles
			wp_enqueue_style(
				'daterangepicker',
				'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css',
				[],
				'3.14.1'
			);

			wp_enqueue_style(
				'smart-rentals-wc-frontend',
				SMART_RENTALS_WC_PLUGIN_URI . 'assets/css/frontend.css',
				[ 'daterangepicker' ],
				Smart_Rentals_WC()->get_version()
			);

			// Add custom CSS
			$custom_css = $this->get_custom_css();
			if ( $custom_css ) {
				wp_add_inline_style( 'smart-rentals-wc-frontend', $custom_css );
			}
		}

		/**
		 * Get custom CSS
		 */
		private function get_custom_css() {
			$css = '';
			
			// Add any dynamic CSS based on settings
			$settings = smart_rentals_wc_get_option( 'settings', [] );
			
			// You can add custom CSS generation based on settings here
			
			return apply_filters( 'smart_rentals_wc_custom_css', $css );
		}
	}

}