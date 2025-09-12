<?php
/*
Plugin Name: Smart Rentals For WooCommerce
Plugin URI: https://github.com/your-repo/smart-rentals-for-woocommerce
Description: A comprehensive WooCommerce rental and booking plugin with advanced features for equipment rental, car rental, and more.
Author: Smart Rentals Team
Version: 1.0.0
Author URI: https://smartrentals.com
Text Domain: smart-rentals-wc
Domain Path: /languages/
Requires Plugins: woocommerce
*/

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Smart_Rentals_WC class.
 */
if ( !class_exists( 'Smart_Rentals_WC' ) ) {

	final class Smart_Rentals_WC {
		/**
		 * Smart_Rentals_WC version.
		 *
		 * @var string
		 */
		protected $version = null;

		/**
		 * The single instance of the class.
		 *
		 * @var Smart_Rentals_WC
		 * @since 1.0
		 */
		protected static $_instance = null;

		/**
		 * Get data
		 */
		public $options = null;

		/**
		 * Booking
		 */
		public $booking = null;

		/**
		 * Rental
		 */
		public $rental = null;

		/**
		 * Smart_Rentals_WC Constructor
		 */
		public function __construct() {
			// Version
			$this->set_version();

			// Define
			$this->define_constants();

			// Includes
			$this->includes();

			// Load textdomain
			add_action( 'init', [ $this, 'load_textdomain' ] );

			// Woocommerce loaded
			add_action( 'woocommerce_loaded', [ $this, 'woocommerce_loaded' ] );
		}

		/**
		 * Set plugin version
		 */
		private function set_version() {
			$plugin_data 	= get_plugin_data( __FILE__, false, false );
			$this->version 	= isset( $plugin_data['Version'] ) ? $plugin_data['Version'] : null;
		}

		/**
		 * Get plugin version
		 */
		public function get_version() {
			return $this->version;
		}

		/**
		 * Define constants
		 */
		public function define_constants() {
			define( 'SMART_RENTALS_WC_PLUGIN_FILE', __FILE__ );
			define( 'SMART_RENTALS_WC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
			define( 'SMART_RENTALS_WC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			define( 'SMART_RENTALS_WC_PLUGIN_ADMIN', SMART_RENTALS_WC_PLUGIN_PATH . 'admin/' );
			define( 'SMART_RENTALS_WC_PLUGIN_INC', SMART_RENTALS_WC_PLUGIN_PATH . 'includes/' );
			define( 'SMART_RENTALS_WC_PLUGIN_TEMPLATES', SMART_RENTALS_WC_PLUGIN_PATH . 'templates/' );

			// Global prefix
			define( 'SMART_RENTALS_WC_PREFIX', 'smart_rentals_wc_' );
			define( 'SMART_RENTALS_WC_META_PREFIX', 'smart_rentals_' );
		}

		/**
		 * Includes
		 */
		public function includes() {
			// Core functions
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'smart-rentals-wc-core-functions.php';

			// Get data
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-get-data.php';
			if ( class_exists( 'Smart_Rentals_WC_Get_Data', false ) ) {
				$this->options = Smart_Rentals_WC_Get_Data::instance();
			}

			// Booking
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-booking.php';
			if ( class_exists( 'Smart_Rentals_WC_Booking', false ) ) {
				$this->booking = Smart_Rentals_WC_Booking::instance();
			}

			// Rental
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-rental.php';
			if ( class_exists( 'Smart_Rentals_WC_Rental', false ) ) {
				$this->rental = Smart_Rentals_WC_Rental::instance();
			}

			// Admin access
			add_action( 'init', function() {
				require_once SMART_RENTALS_WC_PLUGIN_ADMIN . 'class-smart-rentals-wc-admin.php';
			});

			// Assets
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-assets.php';

			// Hooks
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-hooks.php';

			// Ajax
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-ajax.php';

			// Deposit
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-deposit.php';

			// CPT
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-cpt.php';

			// Shortcodes
			require_once SMART_RENTALS_WC_PLUGIN_INC. 'class-smart-rentals-wc-shortcodes.php';

			// Mail
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-mail.php';

			// Cron
			require_once SMART_RENTALS_WC_PLUGIN_INC .  'class-smart-rentals-wc-cron.php';

			// Templates
			require_once( SMART_RENTALS_WC_PLUGIN_INC . 'smart-rentals-wc-template-functions.php' );
			require_once( SMART_RENTALS_WC_PLUGIN_INC . 'smart-rentals-wc-template-hooks.php' );

			// Elementor
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once( SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-elementor.php' );
			}
		}

		/**
		 * Load textdomain
		 */
		public function load_textdomain() {
			if ( is_textdomain_loaded( 'smart-rentals-wc' ) ) return;

			load_plugin_textdomain( 'smart-rentals-wc', false, basename( dirname( __FILE__ ) ) .'/languages' );
		}

		/**
		 * Woocommerce loaded
		 */
		public function woocommerce_loaded() {
			// Cart & Checkout Blocks Integrations
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-blocks.php';

			// Register IntegrationInterface
			if ( class_exists( 'Smart_Rentals_WC_Blocks' ) ) {
				add_action(
				    'woocommerce_blocks_mini-cart_block_registration',
				    function( $integration_registry ) {
				        $integration_registry->register( new Smart_Rentals_WC_Blocks() );
				    }
				);
				add_action(
				    'woocommerce_blocks_cart_block_registration',
				    function( $integration_registry ) {
				        $integration_registry->register( new Smart_Rentals_WC_Blocks() );
				    }
				);
				add_action(
				    'woocommerce_blocks_checkout_block_registration',
				    function( $integration_registry ) {
				        $integration_registry->register( new Smart_Rentals_WC_Blocks() );
				    }
				);
			}
		}

		/**
		 * Main Smart_Rentals_WC Instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
}

// Require plugin.php to use is_plugin_active() below
if ( !function_exists( 'is_plugin_active' ) ) {
	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Returns the main instance of Smart_Rentals_WC.
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	function Smart_Rentals_WC() {
		return Smart_Rentals_WC::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['Smart_Rentals_WC'] = Smart_Rentals_WC();
}