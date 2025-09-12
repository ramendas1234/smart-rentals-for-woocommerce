<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Imports class.
 */
if ( !class_exists( 'OVABRW_Admin_Imports' ) ) {

	class OVABRW_Admin_Imports {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Admin Menu
            add_action( 'admin_menu', [ $this, 'add_submenu' ] );

            // Create new order manually
            add_action( 'admin_init', [ $this, 'import_locations_manully' ] );
		}

		/**
		 * Add sub menu - Import Locations
		 */
		public function add_submenu() {
			add_submenu_page(
                'ovabrw-settings',
                esc_html__( 'Import locations', 'ova-brw' ),
                '',
                apply_filters( OVABRW_PREFIX.'import_locations_cap', 'edit_posts' ),
                'ovabrw-import-location',
                [ $this, 'view_import_locations' ]
            );
		}

		/**
		 * View import locations
		 */
		public function view_import_locations() {
			include( OVABRW_PLUGIN_ADMIN . 'imports/views/html-import-locations.php' );
		}

		/**
		 * Import locaiton manully
		 */
		public function import_locations_manully() {
			// Get action
			$action = sanitize_text_field( ovabrw_get_meta_data( 'action_import', $_POST ) );

            // Rental: Transportation
            if ( 'import_locations' == $action ) {
                // Check Permission
                if ( !current_user_can( apply_filters( OVABRW_PREFIX.'import_locations' ,'publish_posts' ) ) ) {
                    $_POST['error'] = esc_html__( 'You don\'t have permission to import locations.', 'ova-brw' );
                    return;
                }

                // Get product ID
                $product_id = absint( ovabrw_get_meta_data( 'product_id', $_POST ) );

                if ( !$product_id ) {
                    $_POST['error'] = esc_html__( 'Please select a product', 'ova-brw' );
                    return;
                }

                $file = isset( $_FILES['location_file'] ) && $_FILES['location_file'] ? $_FILES['location_file'] : '';

                $upload_overrides = [
                    'test_form' => false,
                    'mimes'     => [
                        'csv' => 'text/csv',
                        'txt' => 'text/plain'
                    ]
                ];

                try {
                    $upload = wp_handle_upload( $file, $upload_overrides );
                } catch ( Exception $e ) {
                    $_POST['error'] = $e->getMessage();
                    return;
                }
                
                if ( isset( $upload['error'] ) && $upload['error'] ) {
                    $_POST['error'] = $upload['error'];
                    return;
                } else {
                    // Construct the object array.
                    $object = [
                        'post_title'     => basename( $upload['file'] ),
                        'post_content'   => $upload['url'],
                        'post_mime_type' => $upload['type'],
                        'guid'           => $upload['url'],
                        'context'        => 'import',
                        'post_status'    => 'private'
                    ];

                    // Save the data.
                    $id = wp_insert_attachment( $object, $upload['file'] );
                    wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', [ $id ] );

                    $file_url = $upload['file'];
                    $handle = fopen($file_url,"r");

                    while ( !feof( $handle ) ) {
                        $data[] = fgetcsv( $handle );
                    }
                    fclose( $handle );

                    if ( ovabrw_array_exists( $data ) ) {
                        if ( count( $data ) === 1 ) {
                            $_POST['error'] = esc_html__( 'File empty!', 'ova-brw' );
                            return;
                        } else {
                            array_shift( $data );

                            $pickup_location    = [];
                            $dropoff_location   = [];
                            $location_time      = [];
                            $price_location     = [];

                            foreach ( $data as $items ) {
                                $pickup_loc = ovabrw_get_meta_data( 0, $items );
                                if ( !$pickup_loc ) continue;
                                
                                $dropoff_loc = ovabrw_get_meta_data( 1, $items );
                                if ( !$dropoff_loc ) continue;
                                
                                $time_loc = (float)ovabrw_get_meta_data( 2, $items );
                                if ( !$time_loc ) continue;

                                $price_loc = (float)ovabrw_get_meta_data( 3, $items );

                                // Check loaction if don't have
                                $pickup_post = ovabrw_get_page_by_title( $pickup_loc, OBJECT, 'location' );
                                if ( $pickup_post ) {
                                    if ( $pickup_post->post_status === 'trash' ) {
                                        wp_update_post([
                                            'ID'            => $pickup_post->ID,
                                            'post_status'   => 'publish'
                                        ]);
                                    }
                                    $pickup_loc_id = $pickup_post->ID;
                                } else {
                                    $pickup_loc_id = wp_insert_post([
                                        'post_title'  => wp_strip_all_tags( $pickup_loc ),
                                        'post_status' => 'publish',
                                        'post_type'   => 'location'
                                    ]);
                                }

                                $dropoff_post = ovabrw_get_page_by_title( $dropoff_loc, OBJECT, 'location' );
                                if ( $dropoff_post ) {
                                    if ( $dropoff_post->post_status === 'trash' ) {
                                        wp_update_post([
                                            'ID'            => $dropoff_post->ID,
                                            'post_status'   => 'publish'
                                        ]);
                                    }
                                    $dropoff_loc_id = $dropoff_post->ID;
                                } else {
                                    $dropoff_loc_id = wp_insert_post([
                                        'post_title'  => wp_strip_all_tags( $dropoff_loc ),
                                        'post_status' => 'publish',
                                        'post_type'   => 'location',
                                    ]);
                                }

                                array_push( $pickup_location , $pickup_loc );
                                array_push( $dropoff_location , $dropoff_loc );
                                array_push( $location_time , $time_loc );
                                array_push( $price_location , $price_loc );
                            }

                            if ( !empty( $pickup_location ) && !empty( $dropoff_location ) && !empty( $location_time ) && !empty( $price_location ) ) {
                                $update_pickup_location     = [];
                                $update_dropoff_location    = [];
                                $update_location_time       = [];
                                $update_price_location      = [];

                                // Current pick-up locations
                                $current_pickup_location = get_post_meta( $product_id, 'ovabrw_pickup_location', true );
                                if ( ovabrw_array_exists( $current_pickup_location ) ) {
                                    $update_pickup_location = array_merge( $current_pickup_location, $pickup_location );
                                } else {
                                    $update_pickup_location = $pickup_location;
                                }

                                // Current drop-off locations
                                $current_dropoff_location = get_post_meta( $product_id, 'ovabrw_dropoff_location', true );
                                if ( ovabrw_array_exists( $current_dropoff_location ) ) {
                                    $update_dropoff_location = array_merge( $current_dropoff_location, $dropoff_location );
                                } else {
                                    $update_dropoff_location = $dropoff_location;
                                }
                                
                                // Current time locations
                                $current_location_time = get_post_meta( $product_id, 'ovabrw_location_time', true );
                                if ( ovabrw_array_exists( $current_location_time ) ) {
                                    $update_location_time = array_merge( $current_location_time, $location_time );
                                } else {
                                    $update_location_time = $location_time;
                                }

                                // Current price location
                                $current_price_location = get_post_meta( $product_id, 'ovabrw_price_location', true );
                                if ( ovabrw_array_exists( $current_price_location ) ) {
                                    $update_price_location = array_merge( $current_price_location, $price_location );
                                } else {
                                    $update_price_location = $price_location;
                                }

                                update_post_meta( $product_id, 'ovabrw_pickup_location', $update_pickup_location );
                                update_post_meta( $product_id, 'ovabrw_dropoff_location', $update_dropoff_location );
                                update_post_meta( $product_id, 'ovabrw_location_time', $update_location_time );
                                update_post_meta( $product_id, 'ovabrw_price_location', $update_price_location );

                                $_POST['success'] = esc_html__( 'Imported locations success!', 'ova-brw' );
                                add_action( 'admin_notices', [ $this, 'notice_success' ] );
                                return;
                            } else {
                                $_POST['error'] = esc_html__( 'Imported locations fails!', 'ova-brw' );
                                return;
                            }
                        }
                    } else {
                        $_POST['error'] = esc_html__( 'File empty!', 'ova-brw' );
                        return;
                    }
                }
            } elseif ( 'import_setup_locations' == $action ) { // Rental: Day, Hour, Mixed, Period of Time
                // Check Permission
                if ( !current_user_can( apply_filters( OVABRW_PREFIX.'import_locations' ,'publish_posts' ) ) ) {
                    $_POST['error'] = esc_html__( 'You don\'t have permission to import locations.', 'ova-brw' );
                    return;
                }

                // Get product ID
                $product_id = absint( ovabrw_get_meta_data( 'product_id', $_POST ) );

                if ( !$product_id ) {
                    $_POST['error'] = esc_html__( 'Please select a product', 'ova-brw' );
                    return;
                }

                $file = isset( $_FILES['location_file'] ) && $_FILES['location_file'] ? $_FILES['location_file'] : '';

                $upload_overrides = [
                    'test_form' => false,
                    'mimes'     => [
                        'csv' => 'text/csv',
                        'txt' => 'text/plain'
                    ]
                ];

                try {
                    $upload = wp_handle_upload( $file, $upload_overrides );
                } catch ( Exception $e ) {
                    $_POST['error'] = $e->getMessage();
                    return;
                }
                
                if ( isset( $upload['error'] ) && $upload['error'] ) {
                    $_POST['error'] = $upload['error'];
                    return;
                } else {
                    // Construct the object array.
                    $object = [
                        'post_title'     => basename( $upload['file'] ),
                        'post_content'   => $upload['url'],
                        'post_mime_type' => $upload['type'],
                        'guid'           => $upload['url'],
                        'context'        => 'import',
                        'post_status'    => 'private'
                    ];

                    // Save the data.
                    $id = wp_insert_attachment( $object, $upload['file'] );
                    wp_schedule_single_event( time() + DAY_IN_SECONDS, 'importer_scheduled_cleanup', [ $id ] );

                    $file_url   = $upload['file'];
                    $handle     = fopen($file_url,"r");

                    while ( !feof( $handle ) ) {
                        $data[] = fgetcsv( $handle );
                    }

                    fclose( $handle );

                    if ( ovabrw_array_exists( $data ) ) {
                        if ( count( $data ) === 1 ) {
                            $_POST['error'] = esc_html__( 'File empty!', 'ova-brw' );
                            return;
                        } else {
                            array_shift( $data );

                            $st_pickup_loc      = [];
                            $st_dropoff_loc     = [];
                            $st_price_location  = [];

                            foreach ( $data as $items ) {
                                $pickup_loc = ovabrw_get_meta_data( 0, $items );
                                if ( !$pickup_loc ) continue;
                                
                                $dropoff_loc = ovabrw_get_meta_data( 1, $items );
                                if ( !$dropoff_loc ) continue;

                                $price_loc = ovabrw_get_meta_data( 2, $items );

                                // Check loaction if don't have
                                $pickup_post = ovabrw_get_page_by_title( $pickup_loc, OBJECT, 'location' );
                                if ( $pickup_post ) {
                                    if ( $pickup_post->post_status === 'trash' ) {
                                        wp_update_post([
                                            'ID'            => $pickup_post->ID,
                                            'post_status'   => 'publish'
                                        ]);
                                    }

                                    $pickup_loc_id = $pickup_post->ID;
                                } else {
                                    $pickup_loc_id = wp_insert_post([
                                        'post_title'  => wp_strip_all_tags( $pickup_loc ),
                                        'post_status' => 'publish',
                                        'post_type'   => 'location'
                                    ]);
                                }

                                $dropoff_post = ovabrw_get_page_by_title( $dropoff_loc, OBJECT, 'location' );
                                if ( $dropoff_post ) {
                                    if ( 'trash' === $dropoff_post->post_status ) {
                                        wp_update_post([
                                            'ID'            => $dropoff_post->ID,
                                            'post_status'   => 'publish'
                                        ]);
                                    }
                                    $dropoff_loc_id = $dropoff_post->ID;
                                } else {
                                    $dropoff_loc_id = wp_insert_post([
                                        'post_title'  => wp_strip_all_tags( $dropoff_loc ),
                                        'post_status' => 'publish',
                                        'post_type'   => 'location'
                                    ]);
                                }

                                array_push( $st_pickup_loc , $pickup_loc );
                                array_push( $st_dropoff_loc , $dropoff_loc );
                                array_push( $st_price_location , $price_loc );
                            }

                            if ( !empty( $st_pickup_loc ) && !empty( $st_dropoff_loc ) && !empty( $st_price_location ) ) {
                                $update_pickup_location     = [];
                                $update_dropoff_location    = [];
                                $update_price_location      = [];

                                // Current pick-up location
                                $current_pickup_location = get_post_meta( $product_id, 'ovabrw_st_pickup_loc', true );
                                if ( ovabrw_array_exists( $current_pickup_location ) ) {
                                    $update_pickup_location = array_merge( $current_pickup_location, $st_pickup_loc );
                                } else {
                                    $update_pickup_location = $st_pickup_loc;
                                }

                                // Current drop-off location
                                $current_dropoff_location = get_post_meta( $product_id, 'ovabrw_st_dropoff_loc', true );
                                if ( ovabrw_array_exists( $current_dropoff_location ) ) {
                                    $update_dropoff_location = array_merge( $current_dropoff_location, $st_dropoff_loc );
                                } else {
                                    $update_dropoff_location = $st_dropoff_loc;
                                }

                                // Current price location
                                $current_price_location = get_post_meta( $product_id, 'ovabrw_st_price_location', true );
                                if ( ovabrw_array_exists( $current_price_location ) ) {
                                    $update_price_location = array_merge( $current_price_location, $st_price_location );
                                } else {
                                    $update_price_location = $st_price_location;
                                }
                                
                                update_post_meta( $product_id, 'ovabrw_st_pickup_loc', $update_pickup_location );
                                update_post_meta( $product_id, 'ovabrw_st_dropoff_loc', $update_dropoff_location );
                                update_post_meta( $product_id, 'ovabrw_st_price_location', $update_price_location );
                                
                                $_POST['success'] = esc_html__( 'Imported locations success!', 'ova-brw' );
                                add_action( 'admin_notices', [ $this, 'notice_success' ] );
                                return;
                            } else {
                                $_POST['error'] = esc_html__( 'Imported locations fails!', 'ova-brw' );
                                return;
                            }
                        }
                    } else {
                        $_POST['error'] = esc_html__( 'File empty!', 'ova-brw' );
                        return;
                    }
                }
            } elseif ( 'remove_locations' == $action ) { // Remove locations
                // Check Permission
                if ( !current_user_can( apply_filters( OVABRW_PREFIX.'import_locations' ,'publish_posts' ) ) ) {
                    $_POST['error'] = esc_html__( 'You don\'t have permission to remove locations.', 'ova-brw' );
                    return;
                }

                // Get product ID
                $product_id = absint( ovabrw_get_meta_data( 'product_id', $_POST ) );

                if ( !$product_id ) {
                    $_POST['error'] = esc_html__( 'Please select a product', 'ova-brw' );
                    return;
                }

                $product_type = get_post_meta( $product_id, 'ovabrw_price_type', true );

                if ( 'transportation' != $product_type ) {
                    update_post_meta( $product_id, 'ovabrw_st_pickup_loc', '' );
                    update_post_meta( $product_id, 'ovabrw_st_dropoff_loc', '' );
                    update_post_meta( $product_id, 'ovabrw_st_price_location', '' );
                } else {
                    update_post_meta( $product_id, 'ovabrw_pickup_location', '' );
                    update_post_meta( $product_id, 'ovabrw_dropoff_location', '' );
                    update_post_meta( $product_id, 'ovabrw_location_time', '' );
                    update_post_meta( $product_id, 'ovabrw_price_location', '' );
                }

                $_POST['success'] = esc_html__( 'Removed locations success!', 'ova-brw' );
                add_action( 'admin_notices', [ $this, 'notice_success' ] );
                return;
            }
		}

		/**
		 * Notice success
		 */
		public function notice_success() {
            if ( isset( $_POST['success'] ) && $_POST['success'] ): ?>
                <div class="notice notice-success is-dismissible">
                    <p><?php esc_html_e( $_POST['success'] ); ?></p>
                </div>
            <?php endif;
        }

        /**
         * Notice error
         */
        public function notice_error() {
            if ( isset( $_POST['error'] ) && $_POST['error'] ): ?>
                <div class="notice notice-error is-dismissible">
                    <p><?php esc_html_e( $_POST['error'] ); ?></p>
                </div>
            <?php endif;
        }
	}

	new OVABRW_Admin_Imports();
}