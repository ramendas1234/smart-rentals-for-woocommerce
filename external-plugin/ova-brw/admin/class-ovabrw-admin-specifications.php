<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Specifications class.
 */
if ( !class_exists( 'OVABRW_Admin_Specifications' ) ) {

	class OVABRW_Admin_Specifications {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add sub-menu
            add_action( 'admin_menu', [ $this, 'add_submenu' ] );
		}

		/**
		 * Add sub-menu: Specifications
		 */
		public function add_submenu() {
			add_submenu_page(
                'ovabrw-settings',
                esc_html__( 'Specifications', 'ova-brw' ),
                esc_html__( 'Specifications', 'ova-brw' ),
                apply_filters( OVABRW_PREFIX.'submenu_specifications_capability' ,'edit_posts' ),
                'ovabrw-specifications',
                [ $this, 'view_specifications' ],
                7
            );
		}

		/**
		 * View specifications
		 */
		public function view_specifications() {
			include( OVABRW_PLUGIN_ADMIN . 'specifications/views/html-specifications.php' );
		}

		/**
		 * Popup form fields
		 */
		public function popup_form_fields( $action = 'new', $type = 'text', $name = '' ) {
			if ( 'edit' === $action ) {
                include( OVABRW_PLUGIN_ADMIN . 'specifications/views/html-popup-edit-specification-field.php' );
            } else {
                include( OVABRW_PLUGIN_ADMIN . 'specifications/views/html-popup-add-specification-field.php' );
            }
		}

		/**
		 * Sanitize keys
		 */
		public function sanitize_keys( $args = [], $default = [] ) {
            if ( ovabrw_array_exists( $args ) ) {
            	foreach ( $args as $k => $v ) {
	                if ( ! $v && isset( $default[$k] ) && $default[$k] ) {
	                    $v = $default[$k];
	                }

	                $args[$k] = sanitize_text_field( sanitize_title( $v ) );
	            }
            }

            return $args;
        }

        /**
         * Get field types
         */
        public function get_types() {
            $types = [
            	'text'      => esc_html__( 'Text', 'ova-brw' ),
                'link'      => esc_html__( 'Link', 'ova-brw' ),
                'number'    => esc_html__( 'Number', 'ova-brw' ),
                'tel'       => esc_html__( 'Tel', 'ova-brw' ),
                'email'     => esc_html__( 'Email', 'ova-brw' ),
                'radio'     => esc_html__( 'Radio', 'ova-brw' ),
                'checkbox'  => esc_html__( 'Checkbox', 'ova-brw' ),
                'select'    => esc_html__( 'Select', 'ova-brw' ),
                'date'      => esc_html__( 'Date', 'ova-brw' ),
                'color'     => esc_html__( 'Color', 'ova-brw' ),
                'file'      => esc_html__( 'File', 'ova-brw' )
            ];

            return apply_filters( OVABRW_PREFIX.'specification_get_types', $types );
        }

        /**
         * Add
         */
        public function add( $post_data = [] ) {
            if ( ovabrw_array_exists( $post_data ) && current_user_can('publish_posts') ) {
                // Get specifications
                $specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

                // Get name
                $name = sanitize_text_field( sanitize_title( ovabrw_get_meta_data( 'name', $post_data ) ) );

                if ( $name ) {
                    // Basic
                    $specifications[$name] = [
                    	'type'          => ovabrw_get_meta_data( 'type', $post_data ),
                        'label'         => ovabrw_get_meta_data( 'label', $post_data ),
                        'icon-font' 	=> ovabrw_get_meta_data( 'icon-font', $post_data ),
                        'default'       => ovabrw_get_meta_data( 'default', $post_data ),
                        'class'       	=> ovabrw_get_meta_data( 'class', $post_data ),
                        'enable'       	=> ovabrw_get_meta_data( 'enable', $post_data ),
                        'show_label' 	=> ovabrw_get_meta_data( 'show_label', $post_data ),
                        'show_in_card' 	=> ovabrw_get_meta_data( 'show_in_card', $post_data )
                    ];

                    // Options
                    if ( ovabrw_get_meta_data( 'options', $post_data ) ) {
                        $specifications[$name]['options'] = $post_data['options'];
                    }

                    // Multiple
                    if ( ovabrw_get_meta_data( 'multiple', $post_data ) ) {
                        $specifications[$name]['multiple'] = $post_data['multiple'];
                    }
                }

                update_option( ovabrw_meta_key( 'specifications' ), $specifications );
            }

            // Refresh
            wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
        }

        // Edit
        public function edit( $post_data = [] ) {
            if ( ovabrw_array_exists( $post_data ) && current_user_can('publish_posts') ) {
                // Get specifications
                $specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

                // Get name
                $new_name = sanitize_text_field( sanitize_title( ovabrw_get_meta_data( 'name', $post_data ) ) );
                $old_name = sanitize_text_field( ovabrw_get_meta_data( 'old_name', $post_data ) );

                if ( $new_name && $old_name ) {
                    $data = [
                    	'type'          => ovabrw_get_meta_data( 'type', $post_data ),
                        'label'         => ovabrw_get_meta_data( 'label', $post_data ),
                        'icon-font' 	=> ovabrw_get_meta_data( 'icon-font', $post_data ),
                        'default'       => ovabrw_get_meta_data( 'default', $post_data ),
                        'class'       	=> ovabrw_get_meta_data( 'class', $post_data ),
                        'enable'       	=> ovabrw_get_meta_data( 'enable', $post_data ),
                        'show_label' 	=> ovabrw_get_meta_data( 'show_label', $post_data ),
                        'show_in_card' 	=> ovabrw_get_meta_data( 'show_in_card', $post_data )
                    ];

                    // Options
                    if ( ovabrw_get_meta_data( 'options', $post_data ) ) {
                        $specifications[$name]['options'] = $post_data['options'];
                    }

                    // Multiple
                    if ( ovabrw_get_meta_data( 'multiple', $post_data ) ) {
                        $specifications[$name]['multiple'] = $post_data['multiple'];
                    }

                    // Change name
                    if ( $new_name != $old_name ) {
                        $specifications = array_map( function ( $key, $value ) use ( $old_name, $new_name ) {
                            if ( $key === $old_name ) {
                                return [$new_name => $value];
                            }

                            return [$key => $value];
                        }, array_keys( $specifications ), $specifications );

                        $specifications = call_user_func_array( 'array_merge', $specifications );

                        $specifications[$new_name] = $data;
                    } else {
                        $specifications[$old_name] = $data;
                    }
                }

                update_option( ovabrw_meta_key( 'specifications' ), $specifications );
            }

            // Refresh
            wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
        }

        // Delete
        public function delete( $post_data = [] ) {
            if ( ovabrw_array_exists( $post_data ) && current_user_can('publish_posts') ) {
                // Get specifications
                $specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

                // Get fields
                $fields = ovabrw_get_meta_data( 'fields', $post_data );

                if ( ovabrw_array_exists( $fields ) ) {
                    foreach ( $fields as $name ) {
                        // remove field from specifications
                        if ( isset( $specifications[$name] ) ) unset($specifications[$name]);
                    }
                }

                update_option( ovabrw_meta_key( 'specifications' ), $specifications );
            }

            // Refresh
            if ( ! wp_doing_ajax() ) wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
        }

        // Enable
        public function enable( $post_data = [] ) {
            if ( ovabrw_array_exists( $post_data ) && current_user_can('publish_posts') ) {
                // Get specifications
                $specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

                // Get fields
                $fields = ovabrw_get_meta_data( 'fields', $post_data );

                if ( ovabrw_array_exists( $fields ) ) {
                    foreach ( $fields as $name ) {
                        // remove field from specifications
                        if ( isset( $specifications[$name]['enable'] ) ) {
                            $specifications[$name]['enable'] = 'on';
                        }
                    }
                }

                update_option( ovabrw_meta_key( 'specifications' ), $specifications );
            }

            // Refresh
            if ( ! wp_doing_ajax() ) wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
        }

        // Disable
        public function disable( $post_data = [] ) {
            if ( ovabrw_array_exists( $post_data ) && current_user_can('publish_posts') ) {
                // Get specifications
                $specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

                // Get fields
                $fields = ovabrw_get_meta_data( 'fields', $post_data );

                if ( ovabrw_array_exists( $fields ) ) {
                    foreach ( $fields as $name ) {
                        // remove field from specifications
                        if ( isset( $specifications[$name]['enable'] ) ) {
                            $specifications[$name]['enable'] = '';
                        }
                    }
                }

                update_option( ovabrw_meta_key( 'specifications' ), $specifications );
            }

            // Refresh
            if ( ! wp_doing_ajax() ) wp_safe_redirect( $_SERVER['HTTP_REFERER'] );
        }

        // Sort
        public function sort( $args = [] ) {
            // Get fields
            $fields = ovabrw_get_meta_data( 'fields', $args );

            if ( ovabrw_array_exists( $fields ) && current_user_can('publish_posts') ) {
                // Get specifications
                $specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

                // New data
                $new_data = [];

                foreach ( $fields as $name ) {
                    if ( $name && array_key_exists( $name, $specifications ) ) {
                        $new_data[$name] = $specifications[$name];
                    }
                }

                if ( ovabrw_array_exists( $new_data ) ) {
                	update_option( ovabrw_meta_key( 'specifications' ), $new_data );
                }
            }
        }

		/**
		 * Main Specifications instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}

	new OVABRW_Admin_Specifications();
}