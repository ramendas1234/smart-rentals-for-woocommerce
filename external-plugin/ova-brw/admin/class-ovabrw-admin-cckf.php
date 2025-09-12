<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Custom Checkout Fields class.
 */
if ( !class_exists( 'OVABRW_Admin_CCKF' ) ) {

	class OVABRW_Admin_CCKF {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add sub-menu
            add_action('admin_menu', [ $this, 'add_submenu' ] );
		}

		/**
		 * Add sub-menu: Custom checkout fields
		 */
		public function add_submenu() {
			add_submenu_page(
                'ovabrw-settings',
                esc_html__( 'Custom checkout fields', 'ova-brw' ),
                esc_html__( 'Custom checkout fields', 'ova-brw' ),
                apply_filters( OVABRW_PREFIX.'submenu_cckf_capability', 'edit_posts' ),
                'ovabrw-custom-checkout-field',
                [ $this, 'view_custom_checkout_field' ],
                6
            );
		}

		/**
		 * View custom checkout field
		 */
		public function view_custom_checkout_field() {
			include( OVABRW_PLUGIN_ADMIN . 'custom-checkout-fields/views/html-custom-checkout-fields.php' );
		}

		/**
		 * Popup form fields
		 */
		public function popup_form_fields( $type = '', $fields = [] ) {
			include( OVABRW_PLUGIN_ADMIN . 'custom-checkout-fields/views/html-popup-add-cckf-fields.php' );
		}

		/**
		 * Sanitize keys
		 */
		public function sanitize_keys( $args = [], $default = [] ) {
            if ( ovabrw_array_exists( $args ) ) {
            	foreach ( $args as $k => $v ) {
	                if ( !$v && ovabrw_get_meta_data( $k, $default ) ) {
	                    $v = $default[$k];
	                }

	                $args[$k] = sanitize_text_field( sanitize_title( $v ) );
	            }
            }

            return $args;
        }
	}

	new OVABRW_Admin_CCKF();
}