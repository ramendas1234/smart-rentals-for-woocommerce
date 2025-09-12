<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Custom Post Type class
 */
if ( !class_exists( 'OVABRW_Admin_CPT', false ) ) {

	class OVABRW_Admin_CPT {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Register Location post type
			add_action( 'init', [ $this, 'register_location' ] );

			// Register Vehicle post type
			add_action( 'init', [ $this, 'register_vehicle' ] );

			// Register order status: wc-closed
            add_action( 'init', [ $this, 'register_wc_closed_order_status' ] );

            // Add meta-boxes vehicle
            add_action( 'add_meta_boxes_vehicle', [ $this, 'add_vehicle_meta_box' ] );

            // Save
            add_action( 'save_post', [ $this, 'save_post_data' ], 11, 2 );
		}

		/**
		 * Register Location post type
		 */
		public function register_location() {
			$labels = [
                'name'               => esc_html__( 'Location', 'post type general name', 'ova-brw' ),
                'singular_name'      => esc_html__( 'Location', 'post type singular name', 'ova-brw' ),
                'menu_name'          => esc_html__( 'Location', 'admin menu', 'ova-brw' ),
                'name_admin_bar'     => esc_html__( 'Location', 'add new on admin bar', 'ova-brw' ),
                'add_new'            => esc_html__( 'Add new location', 'Location', 'ova-brw' ),
                'add_new_item'       => esc_html__( 'Add new location', 'ova-brw' ),
                'new_item'           => esc_html__( 'New location', 'ova-brw' ),
                'edit_item'          => esc_html__( 'Edit location', 'ova-brw' ),
                'view_item'          => esc_html__( 'View location', 'ova-brw' ),
                'all_items'          => esc_html__( 'Manage locations', 'ova-brw' ),
                'search_items'       => esc_html__( 'Search location', 'ova-brw' ),
                'parent_item_colon'  => esc_html__( 'Parent location:', 'ova-brw' ),
                'not_found'          => esc_html__( 'No locations found.', 'ova-brw' ),
                'not_found_in_trash' => esc_html__( 'No locations found in trash.', 'ova-brw' )
            ];

            register_post_type( 'location', [
                'labels'                => $labels,
                'public'                => true,
                'menu_icon'             => 'dashicons-location',
                'publicly_queryable'    => false,
                'show_ui'               => true,
                'show_in_menu'          => 'ovabrw-settings',
                'menu_position'         => 20,
                'query_var'             => true,
                'rewrite'               => [ 'slug' => 'location' ],
                'capability_type'       => 'post',
                'has_archive'           => true,
                'hierarchical'          => false,
                'supports'              => [ 'title', 'author', 'thumbnail' ]
            ]);
		}

		/**
		 * Register Vehicle post type
		 */
		public function register_vehicle() {
			$labels = [
                'name'               => esc_html__( 'Vehicle', 'post type general name', 'ova-brw' ),
                'singular_name'      => esc_html__( 'Vehicle', 'post type singular name', 'ova-brw' ),
                'menu_name'          => esc_html__( 'Managevehicle', 'ova-brw' ),
                'name_admin_bar'     => esc_html__( 'Vehicle', 'ova-brw' ),
                'add_new'            => esc_html__( 'Add new vehicle', 'ova-brw' ),
                'add_new_item'       => esc_html__( 'Add new vehicle', 'ova-brw' ),
                'new_item'           => esc_html__( 'New vehicle', 'ova-brw' ),
                'edit_item'          => esc_html__( 'Edit vehicle', 'ova-brw' ),
                'view_item'          => esc_html__( 'View vehicle', 'ova-brw' ),
                'all_items'          => esc_html__( 'Manage vehicles', 'ova-brw' ),
                'search_items'       => esc_html__( 'Search vehicle', 'ova-brw' ),
                'parent_item_colon'  => esc_html__( 'Parent vehicle:', 'ova-brw' ),
                'not_found'          => esc_html__( 'No vehicles found.', 'ova-brw' ),
                'not_found_in_trash' => esc_html__( 'No vehicles found in trash.', 'ova-brw' )
            ];

            register_post_type( 'vehicle', [
                'labels'                => $labels,
                'public'                => true,
                'menu_icon'             => 'dashicons-car',
                'publicly_queryable'    => false,
                'show_ui'               => true,
                'show_in_menu'          => 'ovabrw-settings',
                'menu_position'         => 20,
                'query_var'             => true,
                'rewrite'               => [ 'slug' => 'vehicle' ],
                'capability_type'       => 'post',
                'has_archive'           => true,
                'hierarchical'          => false,
                'supports'              => [ 'title', 'author' ]
            ]);
		}

		/**
		 * Register order status: wc-closed
		 */
		public function register_wc_closed_order_status() {
			register_post_status( 'wc-closed', [
                'label'                     => _x( 'Closed', 'Order status', 'ova-brw' ),
                'public'                    => true,
                'exclude_from_search'       => false,
                'show_in_admin_all_list'    => true,
                'show_in_admin_status_list' => true,
                'label_count'               => _n_noop( 'Closed <span class="count">(%s)</span>', 'Closed<span class="count">(%s)</span>', 'ova-brw' )
            ]);
		}

		/**
		 * Add meta-boxes vehicle
		 */
		public function add_vehicle_meta_box() {
			add_meta_box(
                'meta-box-id-vehicle',
                esc_html__( 'Settings', 'ova-brw' ),
                [ $this, 'vehicle_fields' ],
                'vehicle'
            );
		}

		/**
		 * Vehicle fields
		 */
		public function vehicle_fields() {
			include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/views/html-vehicle-fields.php' );
		}

		/**
		 * Save post data
		 */
		public function save_post_data( $post_id, $data ) {
			if ( !ovabrw_array_exists( $data ) ) $data = $_POST;

			// Get post type
            $post_type = ovabrw_get_meta_data( 'post_type', $data );

            if ( 'vehicle' == $post_type ) {
                // Vehicle ID
                $vehicle_id = ovabrw_get_meta_data( ovabrw_meta_key( 'id_vehicle' ), $data );
                if ( !$vehicle_id ) {
                    $vehicle_id = ovabrw_sanitize_title( ovabrw_get_meta_data( 'post_title', $data ) );
                }
                update_post_meta( $post_id, ovabrw_meta_key( 'id_vehicle' ), $vehicle_id );

                // Required Location
                $required_location = ovabrw_get_meta_data( ovabrw_meta_key( 'vehicle_require_location' ), $data, 'no' );
                update_post_meta( $post_id, ovabrw_meta_key( 'vehicle_require_location' ), $required_location );

                // Vehicle Location
                $vehicle_location = ovabrw_get_meta_data( ovabrw_meta_key( 'id_vehicle_location' ), $data );
                update_post_meta( $post_id, ovabrw_meta_key( 'id_vehicle_location' ), $vehicle_location );

                // Unavailable time
                $start_date = isset( $data[ovabrw_meta_key( 'id_vehicle_untime_from_day' )]['startdate'] ) ? $data[ovabrw_meta_key( 'id_vehicle_untime_from_day' )]['startdate'] : '';
                $end_date   = isset( $data[ovabrw_meta_key( 'id_vehicle_untime_from_day' )]['enddate'] ) ? $data[ovabrw_meta_key( 'id_vehicle_untime_from_day' )]['enddate'] : '';

                $unavilable_time = [];

                if ( $start_date && $end_date ) {
                    $unavilable_time = [
                    	'startdate' => $start_date,
                        'enddate'   => $end_date
                    ];
                }

                update_post_meta( $post_id, ovabrw_meta_key( 'id_vehicle_untime_from_day' ), $unavilable_time );
            }
		}
	}

	new OVABRW_Admin_CPT();
}