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
			// Always load frontend scripts for AJAX functionality
			wp_enqueue_script(
				'smart-rentals-wc-frontend',
				SMART_RENTALS_WC_PLUGIN_URI . 'assets/js/frontend.js',
				[ 'jquery' ],
				Smart_Rentals_WC()->get_version(),
				true
			);

			wp_localize_script( 'smart-rentals-wc-frontend', 'smart_rentals_wc_ajax', [
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( 'smart_rentals_wc_nonce' ),
				'messages' => [
					'select_dates' => __( 'Please select pickup and drop-off dates.', 'smart-rentals-wc' ),
					'invalid_dates' => __( 'Drop-off date must be after pickup date.', 'smart-rentals-wc' ),
					'loading' => __( 'Loading...', 'smart-rentals-wc' ),
					'error' => __( 'An error occurred. Please try again.', 'smart-rentals-wc' ),
				]
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
			wp_enqueue_style(
				'smart-rentals-wc-frontend',
				SMART_RENTALS_WC_PLUGIN_URI . 'assets/css/frontend.css',
				[],
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