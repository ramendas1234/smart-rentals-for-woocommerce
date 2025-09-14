<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Elementor
 */
if ( !class_exists( 'OVABRW_Elementor', false ) ) {

	class OVABRW_Elementor {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Register categories
			add_action( 'elementor/elements/categories_registered', [ $this, 'categories_registered' ] );

			// Register widgets
			add_action( 'elementor/widgets/register', [ $this, 'widgets_register' ] );

			// Register scripts
			add_action( 'elementor/frontend/after_register_scripts', [ $this, 'register_scripts' ] );
		}

		/**
		 * Register categories
		 */
		public function categories_registered() {
		    \Elementor\Plugin::instance()->elements_manager->add_category(
		        'ovabrw-product',
		        [
		            'title' => esc_html__( 'BRW Product', 'ova-brw' ),
		            'icon' 	=> 'fa fa-plug'
		        ]
		    );

		    \Elementor\Plugin::instance()->elements_manager->add_category(
		        'ovabrw-products',
		        [
		            'title' => esc_html__( 'BRW Products', 'ova-brw' ),
		            'icon' 	=> 'fa fa-plug'
		        ]
		    );
		}

		/**
		 * Register widgets
		 */
		public function widgets_register( $widgets_manager ) {
			$widget_files = glob( OVABRW_PLUGIN_PATH . 'elementor/widgets/*.php' );

			if ( ovabrw_array_exists( $widget_files ) ) {
				foreach ( $widget_files as $file ) {
		            $path = OVABRW_PLUGIN_PATH . 'elementor/widgets/' . wp_basename( $file );
		            if ( file_exists( $path ) ) {
		                require_once $path;
		            }
		        }
			}
		}

		/**
		 * Register scripts
		 */
		public function register_scripts() {
			wp_register_script( 'ovabrw-script-elementor', OVABRW_PLUGIN_URI . 'assets/js/frontend/ova-script-elementor.min.js' );
		}
	}

	new OVABRW_Elementor();
}