<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin class.
 */
if ( !class_exists( 'OVABRW_Admin' ) ) {

	class OVABRW_Admin {

		/**
		 * Contructor
		 */
		public function __construct() {
			// Core functions
			require_once OVABRW_PLUGIN_ADMIN . 'ovabrw-admin-core-functions.php';

			// Assets
			require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-assets.php';

			// Menus
			require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-menus.php';

			// Admin access
			if ( current_user_can( 'administrator' ) || current_user_can('edit_posts') ) {
				// Settings
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-settings.php';

				// Admin Ajax
                require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-ajax.php';

				// Categories
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-categories.php';

				// OVABRW_Admin_Booking_List
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-booking-list.php';

				// Booking
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-bookings.php';

				// Custom checkout fields
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-cckf.php';

				// Specifications
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-specifications.php';

				// Custom taxonomies
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-taxonomies.php';

				// Imports
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-imports.php';

				// Meta-boxes
				require_once OVABRW_PLUGIN_ADMIN . 'class-ovabrw-admin-meta-boxes.php';
			}
		}

	}

	new OVABRW_Admin();
}