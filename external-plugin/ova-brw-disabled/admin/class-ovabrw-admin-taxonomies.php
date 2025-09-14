<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Custom Taxonomies class.
 */
if ( !class_exists( 'OVABRW_Admin_Taxonomies', false ) ) {

	class OVABRW_Admin_Taxonomies {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add sub-menu
            add_action( 'admin_menu', [ $this, 'add_submenu' ] );
		}

		/**
		 * Add sub-menu: Custom taxonomies
		 */
		public function add_submenu() {
			add_submenu_page(
                'ovabrw-settings',
                esc_html__( 'Custom taxonomies', 'ova-brw' ),
                esc_html__( 'Custom taxonomies', 'ova-brw' ),
                apply_filters( OVABRW_PREFIX.'submenu_custom_taxonomies_capability', 'edit_posts' ),
                'ovabrw-custom-taxonomy',
                [ $this, 'view_custom_taxonomy' ],
                3
            );
		}

		/**
		 * View custom taxonomies
		 */
		public function view_custom_taxonomy() {
			include( OVABRW_PLUGIN_ADMIN . 'custom-taxonomies/views/html-custom-taxonomies.php' );
		}

		/**
		 * Popup form fields
		 */
		public function popup_form_fields( $type = '', $fields = [] ) {
			include( OVABRW_PLUGIN_ADMIN . 'custom-taxonomies/views/html-popup-add-taxonomy-fields.php' );
		}
	}

	new OVABRW_Admin_Taxonomies();
}