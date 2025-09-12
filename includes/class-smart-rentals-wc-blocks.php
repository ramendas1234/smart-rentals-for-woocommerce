<?php
/**
 * Smart Rentals WC Blocks class
 */

if ( !defined( 'ABSPATH' ) ) exit();

use Automattic\WooCommerce\Blocks\Integrations\IntegrationInterface;

if ( !class_exists( 'Smart_Rentals_WC_Blocks' ) ) {

	class Smart_Rentals_WC_Blocks implements IntegrationInterface {

		/**
		 * Get integration name
		 */
		public function get_name() {
			return 'smart-rentals-wc';
		}

		/**
		 * Initialize integration
		 */
		public function initialize() {
			// Register block integration
		}

		/**
		 * Get script handles
		 */
		public function get_script_handles() {
			return [];
		}

		/**
		 * Get editor script handles
		 */
		public function get_editor_script_handles() {
			return [];
		}

		/**
		 * Get script data
		 */
		public function get_script_data() {
			return [];
		}
	}
}