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
			add_action( 'init', [ $this, 'init_admin' ] );

			// Assets
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-assets.php';
			new Smart_Rentals_WC_Assets();

			// Hooks
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-hooks.php';
			new Smart_Rentals_WC_Hooks();

			// Ajax
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-ajax.php';
			new Smart_Rentals_WC_Ajax();

			// Deposit
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-deposit.php';
			new Smart_Rentals_WC_Deposit();

			// Note: Removed CPT class - not needed for our checkbox-based approach

			// Shortcodes
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-shortcodes.php';
			new Smart_Rentals_WC_Shortcodes();

			// Mail
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-mail.php';
			new Smart_Rentals_WC_Mail();

			// Cron
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-cron.php';
			new Smart_Rentals_WC_Cron();

			// Templates
			require_once( SMART_RENTALS_WC_PLUGIN_INC . 'smart-rentals-wc-template-functions.php' );
			require_once( SMART_RENTALS_WC_PLUGIN_INC . 'smart-rentals-wc-template-hooks.php' );

			// Product
			require_once( SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-product.php' );
			new Smart_Rentals_WC_Product();

			// Install
			require_once( SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-install.php' );

			// Note: Removed debug class - not needed for production

			// Elementor
			if ( defined( 'ELEMENTOR_VERSION' ) ) {
				// Elementor integration will be added in future version
				// require_once( SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-elementor.php' );
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
		 * Initialize admin
		 */
		public function init_admin() {
			require_once SMART_RENTALS_WC_PLUGIN_ADMIN . 'class-smart-rentals-wc-admin.php';
			new Smart_Rentals_WC_Admin();
			
			// Use the new order items edit class for inline editing
			require_once SMART_RENTALS_WC_PLUGIN_ADMIN . 'class-smart-rentals-wc-order-items-edit.php';
			new Smart_Rentals_WC_Order_Items_Edit();
		}


		/**
		 * Woocommerce loaded
		 */
		public function woocommerce_loaded() {
			// Cart & Checkout Blocks Integrations
			require_once SMART_RENTALS_WC_PLUGIN_INC . 'class-smart-rentals-wc-blocks.php';

			// Register IntegrationInterface only if blocks are available
			if ( class_exists( 'Smart_Rentals_WC_Blocks' ) && method_exists( 'Smart_Rentals_WC_Blocks', 'get_script_handles' ) ) {
				add_action( 'woocommerce_blocks_mini-cart_block_registration', [ $this, 'register_mini_cart_block' ] );
				add_action( 'woocommerce_blocks_cart_block_registration', [ $this, 'register_cart_block' ] );
				add_action( 'woocommerce_blocks_checkout_block_registration', [ $this, 'register_checkout_block' ] );
			}
		}

		/**
		 * Register mini cart block
		 */
		public function register_mini_cart_block( $integration_registry ) {
			if ( method_exists( $integration_registry, 'register' ) ) {
				$integration_registry->register( new Smart_Rentals_WC_Blocks() );
			}
		}

		/**
		 * Register cart block
		 */
		public function register_cart_block( $integration_registry ) {
			if ( method_exists( $integration_registry, 'register' ) ) {
				$integration_registry->register( new Smart_Rentals_WC_Blocks() );
			}
		}

		/**
		 * Register checkout block
		 */
		public function register_checkout_block( $integration_registry ) {
			if ( method_exists( $integration_registry, 'register' ) ) {
				$integration_registry->register( new Smart_Rentals_WC_Blocks() );
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

// Check if WooCommerce is active before initializing
add_action( 'plugins_loaded', 'smart_rentals_wc_init_plugin' );

/**
 * Initialize plugin
 */
function smart_rentals_wc_init_plugin() {
	if ( !class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'smart_rentals_wc_woocommerce_missing_notice' );
		return;
	}

	// Initialize plugin
	if ( !function_exists( 'Smart_Rentals_WC' ) ) {
		function Smart_Rentals_WC() {
			return Smart_Rentals_WC::instance();
		}
	}

	// Global for backwards compatibility.
	$GLOBALS['Smart_Rentals_WC'] = Smart_Rentals_WC();
}

/**
 * WooCommerce missing notice
 */
function smart_rentals_wc_woocommerce_missing_notice() {
	echo '<div class="notice notice-error"><p>';
	echo __( 'Smart Rentals for WooCommerce requires WooCommerce to be installed and activated.', 'smart-rentals-wc' );
	echo '</p></div>';
}

// Activation hook
register_activation_hook( __FILE__, 'smart_rentals_wc_activate_plugin' );

// Deactivation hook
register_deactivation_hook( __FILE__, 'smart_rentals_wc_deactivate_plugin' );

// Uninstall hook
register_uninstall_hook( __FILE__, 'smart_rentals_wc_uninstall_plugin' );

/**
 * Plugin activation callback
 */
function smart_rentals_wc_activate_plugin() {
	// Define constants first
	if ( !defined( 'SMART_RENTALS_WC_PLUGIN_FILE' ) ) {
		define( 'SMART_RENTALS_WC_PLUGIN_FILE', __FILE__ );
		define( 'SMART_RENTALS_WC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
		define( 'SMART_RENTALS_WC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'SMART_RENTALS_WC_PLUGIN_ADMIN', plugin_dir_path( __FILE__ ) . 'admin/' );
		define( 'SMART_RENTALS_WC_PLUGIN_INC', plugin_dir_path( __FILE__ ) . 'includes/' );
		define( 'SMART_RENTALS_WC_PLUGIN_TEMPLATES', plugin_dir_path( __FILE__ ) . 'templates/' );
		define( 'SMART_RENTALS_WC_PREFIX', 'smart_rentals_wc_' );
		define( 'SMART_RENTALS_WC_META_PREFIX', 'smart_rentals_' );
	}
	
	// Include core functions first
	require_once plugin_dir_path( __FILE__ ) . 'includes/smart-rentals-wc-core-functions.php';
	
	// Include install class
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-rentals-wc-install.php';
	
	if ( class_exists( 'Smart_Rentals_WC_Install' ) ) {
		Smart_Rentals_WC_Install::install();
	}
}

/**
 * Plugin deactivation callback
 */
function smart_rentals_wc_deactivate_plugin() {
	// Clear scheduled events
	wp_clear_scheduled_hook( 'smart_rentals_wc_daily_tasks' );
	wp_clear_scheduled_hook( 'smart_rentals_wc_hourly_tasks' );
}

/**
 * Plugin uninstall callback
 */
function smart_rentals_wc_uninstall_plugin() {
	// Define constants first
	if ( !defined( 'SMART_RENTALS_WC_PLUGIN_FILE' ) ) {
		define( 'SMART_RENTALS_WC_PLUGIN_FILE', __FILE__ );
		define( 'SMART_RENTALS_WC_PLUGIN_URI', plugin_dir_url( __FILE__ ) );
		define( 'SMART_RENTALS_WC_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
		define( 'SMART_RENTALS_WC_PLUGIN_ADMIN', plugin_dir_path( __FILE__ ) . 'admin/' );
		define( 'SMART_RENTALS_WC_PLUGIN_INC', plugin_dir_path( __FILE__ ) . 'includes/' );
		define( 'SMART_RENTALS_WC_PLUGIN_TEMPLATES', plugin_dir_path( __FILE__ ) . 'templates/' );
		define( 'SMART_RENTALS_WC_PREFIX', 'smart_rentals_wc_' );
		define( 'SMART_RENTALS_WC_META_PREFIX', 'smart_rentals_' );
	}
	
	// Include core functions first
	require_once plugin_dir_path( __FILE__ ) . 'includes/smart-rentals-wc-core-functions.php';
	
	// Include install class
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-smart-rentals-wc-install.php';
	
	if ( class_exists( 'Smart_Rentals_WC_Install' ) ) {
		Smart_Rentals_WC_Install::uninstall();
	}
}