<?php
/*
Plugin Name: BRW - Booking & Rental Plugin
Plugin URI: https://themeforest.net/user/ovatheme/portfolio
Description: OvaTheme Booking, Rental WooCommerce Plugin.
Author: Ovatheme
Version: 1.9.3
Author URI: https://themeforest.net/user/ovatheme
Text Domain: ova-brw
Domain Path: /languages/
Requires Plugins: woocommerce
*/

if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW class.
 */
if ( !class_exists( 'OVABRW' ) ) {

	final class OVABRW {
		/**
		 * OVABRW version.
		 *
		 * @var string
		 */
		protected $version = null;

		/**
		 * The single instance of the class.
		 *
		 * @var TourBooking
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
		 * OVABRW Constructor
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
			define( 'OVABRW_PLUGIN_FILE', __FILE__ );
			define( 'OVABRW_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
			define( 'OVABRW_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
			define( 'OVABRW_PLUGIN_ADMIN', OVABRW_PLUGIN_PATH . 'admin/' );
			define( 'OVABRW_PLUGIN_INC', OVABRW_PLUGIN_PATH . 'includes/' );
			define( 'OVABRW_PLUGIN_RENTAL', OVABRW_PLUGIN_PATH . 'rental-types/' );

			// Global prefix
			define( 'OVABRW_PREFIX', 'ovabrw_' );
			define( 'OVABRW_PREFIX_OPTIONS', 'ova_brw_' );
			define( 'OVABRW_RENTAL', 'ovabrw_car_rental' );
		}

		/**
		 * Includes
		 */
		public function includes() {
			// Core functions
			require_once OVABRW_PLUGIN_INC . 'ovabrw-core-functions.php';

			// Get data
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-get-data.php';
			if ( class_exists( 'OVABRW_Get_Data', false ) ) {
				$this->options = OVABRW_Get_Data::instance();
			}

			// Abstract rental types
			require_once( OVABRW_PLUGIN_RENTAL . 'abstracts/abstract-ovabrw-rental-types.php' );

			// Load rental types
			ovabrw_autoload( OVABRW_PLUGIN_RENTAL . 'types/class-ovabrw-rental-by-*.php' );

			// Rental
			require_once( OVABRW_PLUGIN_RENTAL . 'class-ovabrw-rental-factory.php' );
			if ( class_exists( 'OVABRW_Rental_Factory', false ) ) {
				$this->rental = OVABRW_Rental_Factory::instance();
			}

			// Booking
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-booking.php';
			if ( class_exists( 'OVABRW_Booking', false ) ) {
				$this->booking = OVABRW_Booking::instance();
			}

			// Admin access
			add_action( 'init', function() {
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin.php';
			});

			// Assets
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-assets.php';

			// Hooks
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-hooks.php';

			// Ajaxs
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-ajax.php';

			// Deposit
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-deposit.php';

			// CPT
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-cpt.php';

			// Shortcodes
			require_once OVABRW_PLUGIN_INC. 'class-ovabrw-shortcodes.php';

			// Mail
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-mail.php';

			// Cron
			require_once OVABRW_PLUGIN_INC .  'class-ovabrw-cron.php';

			// Templates
			require_once( OVABRW_PLUGIN_INC . 'ovabrw-template-functions.php' );
			require_once( OVABRW_PLUGIN_INC . 'ovabrw-template-hooks.php' );

			// Elementor
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				require_once( OVABRW_PLUGIN_INC . 'class-ovabrw-elementor.php' );
			}
		}

		/**
		 * Load textdomain
		 */
		public function load_textdomain() {
			if ( is_textdomain_loaded( 'ova-brw' ) ) return;

			load_plugin_textdomain( 'ova-brw', false, basename( dirname( __FILE__ ) ) .'/languages' );
		}

		/**
		 * Woocommerce loaded
		 */
		public function woocommerce_loaded() {
			// init rental product
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-rental-product.php';

			// Cart & Checkout Blocks Integrations
			require_once OVABRW_PLUGIN_INC . 'class-ovabrw-blocks.php';

			// Register IntegrationInterface
			if ( class_exists( 'OVABRW_Blocks' ) ) {
				add_action(
				    'woocommerce_blocks_mini-cart_block_registration',
				    function( $integration_registry ) {
				        $integration_registry->register( new OVABRW_Blocks() );
				    }
				);
				add_action(
				    'woocommerce_blocks_cart_block_registration',
				    function( $integration_registry ) {
				        $integration_registry->register( new OVABRW_Blocks() );
				    }
				);
				add_action(
				    'woocommerce_blocks_checkout_block_registration',
				    function( $integration_registry ) {
				        $integration_registry->register( new OVABRW_Blocks() );
				    }
				);
			}
		}

		/**
		 * Main OVABRW Instance.
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

// Returns the main instance of OVABRW.
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
	function OVABRW() {
		return OVABRW::instance();
	}

	// Global for backwards compatibility.
	$GLOBALS['OVABRW'] = OVABRW();
}