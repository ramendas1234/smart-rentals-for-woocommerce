<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Shortcodes
 */
if ( !class_exists( 'OVABRW_Shortcodes', false ) ) {

	class OVABRW_Shortcodes {
		
		/**
		 * Constructor
		 */
		public function __construct() {
			// Do shortcode in widget text
			add_filter( 'widget_text', 'do_shortcode' );

			// Register shortcodes
			$this->register_shortcodes();
		}

		/**
		 * Register shortcodes
		 */
		public function register_shortcodes() {
			$shortcodes = [
		        'search',
		        'st_booking_form',
		        'st_request_booking_form',
		        'st_product_calendar',
		        'st_table_price_product',
		        'st_feature_product',
		        'products',
		        'product_images',
		        'product_unavailable',
		        'product_title',
		        'product_price',
		        'product_review',
		        'product_taxonomy',
		        'product_meta',
		        'product_features',
		        'product_specifications',
		        'product_short_description',
		        'product_tabs',
		        'product_related',
		        'search_hotel',
		        'search_ajax_hotel',
		        'search_taxi',
		        'search_taxi_ajax',
		        'search_ajax'
		    ];

		    foreach ( $shortcodes as $name ) {
	            add_shortcode( OVABRW_PREFIX.$name, [ $this, $name.'_shortcode' ] );
	        }
		}

		/**
		 * Shortcode: Search
		 */
		public function search_shortcode( $atts = [] ) {
	        global $product;

	        // Attributes
	        $atts = extract( shortcode_atts([
	        	'template'                  => 'search_form_full',
	            'column'                    => '',
	            'show_name_product'         => '',
	            'show_attribute'            => '',
	            'show_tag_product'          => '',
	            'show_pickup_loc'           => '',
	            'show_dropoff_loc'          => '',
	            'show_pickup_date'          => '',
	            'show_dropoff_date'         => '',
	            'show_cat'                  => '',
	            'show_tax'                  => '',
	            'name_product_required'     => '',
	            'tag_product_required'      => '',
	            'pickup_loc_required'       => '',
	            'dropoff_loc_required'      => '',
	            'pickup_date_required'      => '',
	            'dropoff_date_required'     => '',
	            'category_required'         => '',
	            'attribute_required'        => '',
	            'hide_taxonomies_slug'      => '',
	            'remove_cats_id'            => '',
	            'taxonomies_slug_required'  => '',
	            'timepicker'                => '',
	            'dateformat'                => '',
	            'hour_default'              => '',
	            'time_step'                 => '',
	            'order'                     => 'DESC',
	            'orderby'                   => 'date',
	            'class'                     => ''
	        ], $atts ));

	        // Column
	        if ( '' === $column ) {
	            $column = ovabrw_get_setting( 'search_column', 'one-column' );
	        }

	        // Has timepicker
	        if ( '' === $timepicker ) {
	            $timepicker = 'yes' === ovabrw_get_setting( 'search_show_hour', 'yes' ) ? 'true' : 'false';
	        }

	        // Show product name
	        if ( '' === $show_name_product ) {
	            $show_name_product = ovabrw_get_setting( 'search_show_name_product', 'yes' );
	        }

	        // Show attribute
	        if ( '' === $show_attribute ) {
	            $show_attribute = ovabrw_get_setting( 'search_show_attribute', 'yes' );
	        }

	        // Show product tag
	        if ( '' === $show_tag_product ) {
	            $show_tag_product = ovabrw_get_setting( 'search_show_tag_product', 'yes' );
	        }

	        // Show pick-up location
	        if ( '' === $show_pickup_loc ) {
	            $show_pickup_loc = ovabrw_get_setting( 'search_show_pick_up_location', 'yes' );
	        }

	        // Show drop-off location
	        if ( '' === $show_dropoff_loc ) {
	            $show_dropoff_loc = ovabrw_get_setting( 'search_show_drop_off_location', 'yes' );
	        }

	        // Show pick-up date
	        if ( '' === $show_pickup_date ) {
	            $show_pickup_date = ovabrw_get_setting( 'search_show_pick_up_date', 'yes' );
	        }

	        // Show drop-off date
	        if ( '' === $show_dropoff_date ) {
	            $show_dropoff_date = ovabrw_get_setting( 'search_show_drop_off_date', 'yes' );
	        }

	        // Show category
	        if ( '' === $show_cat ) {
	            $show_cat = ovabrw_get_setting( 'search_show_category', 'yes' );
	        }

	        // Show taxonomies
	        if ( '' === $show_tax ) {
	            $show_tax = ovabrw_get_setting( 'search_show_taxonomy', 'yes' );
	        }

	        // Required product namge
	        if ( '' === $name_product_required ) {
	            $name_product_required = ( 'yes' === ovabrw_get_setting( 'search_require_name_product', 'no' ) ) ? 'ovabrw-input-required' : '';
	        } else {
	            if ( 'yes' === $name_product_required ) {
	                $name_product_required = 'ovabrw-input-required';
	            } else {
	                $name_product_required = '';
	            }
	        }

	        // Required product tag
	        if ( '' === $tag_product_required ) {
	            $tag_product_required = ( 'yes' === ovabrw_get_setting( 'search_require_tag_product', 'no' ) ) ? 'ovabrw-input-required' : '';
	        } else {
	            if ( 'yes' === $tag_product_required ) {
	                $tag_product_required = 'ovabrw-input-required';
	            } else {
	                $tag_product_required = '';
	            }
	        }

	        // Required pick-up location
	        if ( '' === $pickup_loc_required ) {
	            $pickup_loc_required = ( 'yes' === ovabrw_get_setting( 'search_require_pick_up_location', 'no' ) ) ? 'ovabrw-input-required' : '';
	        } else {
	            if ( 'yes' === $pickup_loc_required ) {
	                $pickup_loc_required = 'ovabrw-input-required';
	            } else {
	                $pickup_loc_required = '';
	            }
	        }

	        // Required drop-off location
	        if ( '' === $dropoff_loc_required ) {
	            $dropoff_loc_required = ( 'yes' === ovabrw_get_setting( 'search_require_drop_off_location', 'no' ) ) ? 'ovabrw-input-required' : '';
	        } else {
	            if ( 'yes' === $dropoff_loc_required ) {
	                $dropoff_loc_required = 'ovabrw-input-required';
	            } else {
	                $dropoff_loc_required = '';
	            }
	        }

	        // Required pick-up date
	        if ( '' === $pickup_date_required ) {
	            $pickup_date_required = ( 'yes' === ovabrw_get_setting( 'search_require_pick_up_date', 'no' ) ) ? 'ovabrw-input-required' : '';    
	        } else {
	            if ( 'yes' === $pickup_date_required ) {
	                $pickup_date_required = 'ovabrw-input-required';
	            } else {
	                $pickup_date_required = '';
	            }
	        }

	        // Required drop-off date
	        if ( '' === $dropoff_date_required ) {
	            $dropoff_date_required = ( 'yes' === ovabrw_get_setting( 'search_require_drop_off_date', 'no' ) ) ? 'ovabrw-input-required' : '';
	        } else {
	            if ( 'yes' === $dropoff_date_required ) {
	                $dropoff_date_required = 'ovabrw-input-required';
	            } else {
	                $dropoff_date_required = '';
	            }
	        }

	        // Required category
	        if ( '' === $category_required ) {
	            $category_required = ( 'yes' === ovabrw_get_setting( 'search_require_category', 'no' ) ) ? 'ovabrw-input-required' : '';
	        } else {
	            if ( 'yes' === $category_required ) {
	                $category_required = 'ovabrw-input-required';
	            } else {
	                $category_required = '';
	            }
	        }

	        // Required attribute
	        if ( '' === $attribute_required ) {
	            $attribute_required = ( 'yes' === ovabrw_get_setting( 'search_require_attribute', 'no' ) ) ? 'ovabrw-input-required' : '';
	        } else {
	            if ( 'yes' === $attribute_required ) {
	                $attribute_required = 'ovabrw-input-required';
	            } else {
	                $attribute_required = '';
	            }
	        }

	        // Date format
	        if ( '' === $dateformat ) {
	            $dateformat = OVABRW()->options->get_date_format();
	        }

	        // Default hour
	        if ( '' === $hour_default ) {
	            $hour_default = ovabrw_get_setting( 'booking_form_default_hour', '07:00' );
	        }

	        // Time step
	        if ( '' === $time_step ) {
	            $time_step = ovabrw_get_setting( 'booking_form_step_time', '30' );
	        }

	        // Hide taxonomies
	        if ( '' === $hide_taxonomies_slug ) {
	            $hide_taxonomies_slug = ovabrw_get_setting( 'search_hide_taxonomy_slug', '' );
	        }

	        // Required taxonomies
	        if ( '' === $taxonomies_slug_required ) {
	            $taxonomies_slug_required = ovabrw_get_setting( 'search_require_taxonomy_slug', '' );
	        }
	        
	        // Convert to array
	        $arr_hide_taxonomy      = array_map( 'trim',  explode( ',', $hide_taxonomies_slug ) );
	        $arr_require_taxonomy   = array_map( 'trim', explode( ',',  $taxonomies_slug_required ) );
	        $taxonomy_list_wrap     = [];

	        if ( ovabrw_array_exists( $arr_hide_taxonomy ) ) {
	            foreach ( $arr_hide_taxonomy as $key => $taxo) {
	                $taxonomy_list_wrap['taxonomy_hide'][$taxo] = 'hide';
	            }
	        }

	        if ( ovabrw_array_exists( $arr_require_taxonomy ) ) {
	            foreach ( $arr_require_taxonomy as $key => $taxo) {
	                $taxonomy_list_wrap['taxonomy_require'][$taxo] = 'require';
	            }
	        }

	        // Get taxonomies
	        $taxonomies = ovabrw_create_type_taxonomies();
	        $taxonomy_list_wrap['taxonomy_list_all'] = $taxonomies;

	        if ( ovabrw_array_exists( $taxonomies ) ) {
	            foreach( $taxonomies as $tax ) {
	                $taxonomy_list_wrap['taxonomy_get'][$tax['slug']] = isset( $_GET[$tax['slug'].'_name'] ) ? $_GET[$tax['slug'].'_name'] : '';
	            }
	        }

	        // Product name
	        $product_name = sanitize_text_field( ovabrw_get_meta_data( ovabrw_meta_key( 'product_name' ), $_GET ) );

	        // Attribute name
	        $name_attribute = sanitize_text_field( ovabrw_get_meta_data( ovabrw_meta_key( 'attribute' ), $_GET ) );

	        // Attribute value
	        $value_attribute = sanitize_text_field( ovabrw_get_meta_data( $name_attribute, $_GET ) );

	        // Product tag
	        $tag_product = sanitize_text_field( ovabrw_get_meta_data( ovabrw_meta_key( 'tag_product' ), $_GET ) );

	        // Pick-up location
	        $pickup_loc = sanitize_text_field( ovabrw_get_meta_data( ovabrw_meta_key( 'pickup_loc' ), $_GET ) );

	        // Drop-off location
	        $pickoff_loc = sanitize_text_field( ovabrw_get_meta_data( ovabrw_meta_key( 'pickoff_loc' ), $_GET ) );

	        // Pick-up date
	        $pickup_date = sanitize_text_field( ovabrw_get_meta_data( ovabrw_meta_key( 'pickup_date' ), $_GET ) );

	        // Pick-off date
	        $pickoff_date = sanitize_text_field( ovabrw_get_meta_data( ovabrw_meta_key( 'pickoff_date' ), $_GET ) );

	        // Category
	        $cat = sanitize_text_field( ovabrw_get_meta_data( 'cat', $_GET ) );

	        // Attribute taxonomues
	        $attribute_taxonomies   = wc_get_attribute_taxonomies();
	        $list_value_attribute   = $tax_attribute = [];
	        $html_select_attribute  = $html_select_value_attribute = '';

	        if ( $attribute_taxonomies ) :
	            $html_select_attribute .= '<select name="attribute" class="'. esc_attr( $attribute_required ) .'">';
	                $html_select_attribute .= '<option value="">'.esc_html__( 'Select Attribute', 'ova-brw' ).'</option>';
	            
	            foreach ( $attribute_taxonomies as $tax ):
	                if ( taxonomy_exists( wc_attribute_taxonomy_name( $tax->attribute_name ) ) ):
	                    $class_acctive      = ( $name_attribute == $tax->attribute_name ) ? 'active' : '';
	                    $checked_name_attr  = ( $name_attribute == $tax->attribute_name ) ? 'selected' : '';

	                    $html_select_value_attribute .= '<div class="s_field '. esc_attr( $column ) .' ovabrw-value-attribute '. esc_attr( $class_acctive ) .'" id="'. esc_attr( $tax->attribute_name ) .'">';
	                        $html_select_value_attribute .= '<div class="content">';
	                            $html_select_value_attribute .= '<label>'.esc_html__( 'Value Attribute', 'ova-brw' ).'</label>';
	                            $html_select_value_attribute .= '<select name="'. esc_attr( $tax->attribute_name ) .'">';
	                                $label_attribute = $tax->attribute_label;
	                                $tax_attribute[$tax->attribute_name] = $tax->attribute_label;
	                                $term_attributes = get_terms( wc_attribute_taxonomy_name( $tax->attribute_name ), 'orderby=name&hide_empty=0' );

	                                $html_select_attribute .= "<option ".$checked_name_attr." value='".$tax->attribute_name."'>".$tax->attribute_label."</option>";

	                                foreach ( $term_attributes as $attr ) {
	                                    $checked_value_attr = ( $value_attribute == $attr->slug ) ? "selected" : "";
	                                    $html_select_value_attribute .= '<option '. esc_attr( $checked_value_attr ) .' value="'. esc_attr( $attr->slug ) .'">'. esc_html( $attr->name ) .'</option>';
	                                }
	                            $html_select_value_attribute .= '</select>';
	                        $html_select_value_attribute .= '</div>';
	                    $html_select_value_attribute .= '</div>';
	                endif;
	            endforeach;
	            $html_select_attribute .= '</select>';
	        endif;

	        $remove_cats_id = $remove_cats_id == '' ? ovabrw_get_setting( 'search_cat_remove', '' ) : $remove_cats_id;

	        // Agruments
	        $args = [
	        	'column'                        => $column,
	            'template'                      => $template,
	            'show_name_product'             => $show_name_product,
	            'show_attribute'                => $show_attribute,
	            'show_tag_product'              => $show_tag_product,
	            'show_pickup_loc'               => $show_pickup_loc,
	            'show_dropoff_loc'              => $show_dropoff_loc,
	            'show_pickup_date'              => $show_pickup_date,
	            'show_dropoff_date'             => $show_dropoff_date,
	            'show_cat'                      => $show_cat,
	            'show_tax'                      => $show_tax,
	            'name_product_required'         => $name_product_required,
	            'tag_product_required'          => $tag_product_required,
	            'pickup_loc_required'           => $pickup_loc_required,
	            'dropoff_loc_required'          => $dropoff_loc_required,
	            'pickup_date_required'          => $pickup_date_required,
	            'dropoff_date_required'         => $dropoff_date_required,
	            'category_required'             => $category_required,
	            'attribute_required'            => $attribute_required,
	            'remove_cats_id'                => $remove_cats_id,
	            'dateformat'                    => $dateformat,
	            'hour_default'                  => $hour_default,
	            'time_step'                     => $time_step,
	            'order'                         => $order,
	            'orderby'                       => $orderby,
	            'class'                         => $class,
	            'timepicker'                    => 'true' == $timepicker ? true : false,
	            'name_product'                  => $product_name,
	            'name_attribute'                => $name_attribute,
	            'value_attribute'               => $value_attribute,
	            'tag_product'                   => $tag_product,        
	            'pickup_loc'                    => $pickup_loc,
	            'pickoff_loc'                   => $pickoff_loc,
	            'pickup_date'                   => $pickup_date,
	            'pickoff_date'                  => $pickoff_date,
	            'cat'                           => $cat,
	            'html_select_attribute'         => $html_select_attribute,
	            'html_select_value_attribute'   => $html_select_value_attribute,
	            'taxonomy_list_wrap'            => $taxonomy_list_wrap
	        ];

	        // HTML
	        $html = '';
	        ob_start();

	        // Taxonomies depend category
	        $tax_depend_cat = ovabrw_get_setting( 'search_show_tax_depend_cat', 'yes' );

	        echo '<script type="text/javascript">
	        	var ovabrwTaxDependCat = "'. esc_attr( $tax_depend_cat ) .'";
	        </script>';

	        // Template path
            $template_path = ovabrw_locate_template( 'shortcode/'.sanitize_file_name( $template ).'.php' );

	        if ( !file_exists( $template_path ) ){
	            esc_html_e( 'No templates found.', 'ova-brw' );
	        } else {
	            ovabrw_get_template( 'shortcode/'.sanitize_file_name( $template ).'.php', $args );
	        }
	        
	        $html = ob_get_contents();
	        ob_end_clean();

	        return apply_filters( OVABRW_PREFIX.'search_shortcode_html', $html, $atts );
	    }

	    /**
	     * Shortcode: Booking form
	     */
	    public function st_booking_form_shortcode( $atts = [] ) {
	        $atts = extract( shortcode_atts( [
	    		'id'    => '',
	            'class' => ''
	    	], $atts ));

	        // Agruments
	        $args = [
	        	'id'    => $id,
	            'class' => $class
	        ];

	        // HTML
	        $html = '';
	        ob_start();

	        // Template path
            $template_path = ovabrw_locate_template( 'shortcode/st-booking-form.php' );

	        if ( !file_exists( $template_path ) ) {
	            esc_html_e( 'No templates found.', 'ova-brw' );
	        } else {
	            ovabrw_get_template( 'shortcode/st-booking-form.php', $args );
	        }

	        $html = ob_get_contents();
	        ob_end_clean();

	        return apply_filters( OVABRW_PREFIX.'st_booking_form_shortcode_html', $html, $atts );
	    }

	    /**
	     * Shortcode: Request booking form
	     */
	    public function st_request_booking_form_shortcode( $atts = [] ) {
	        $atts = extract( shortcode_atts([
	        	'id'  	=> '',
	            'class' => ''
	        ], $atts ));

	        // Agruments
	        $args = [
	        	'id' 	=> $id,
	            'class' => $class
	        ];

	        // HTML
	        $html = '';
	        ob_start();

	        // Template path
            $template_path = ovabrw_locate_template( 'shortcode/st-request-booking.php' );

	        if ( !file_exists( $template_path ) ) {
	            esc_html_e( 'No templates found', 'ova-brw' );
	        } else {
	            ovabrw_get_template( 'shortcode/st-request-booking.php', $args );
	        }

	        $html = ob_get_contents();
	        ob_end_clean();

	        return apply_filters( OVABRW_PREFIX.'st_request_booking_form_shortcode_html', $html, $atts );
	    }

	    /**
	     * Shortcode: Product calendar
	     */
	    public function st_product_calendar_shortcode( $atts = [] ) {
	        $atts = extract( shortcode_atts([
	        	'id'    => '',
	            'class' => ''
	        ], $atts ));

	        // Agruments
	        $args = [
	        	'id'    => $id,
	            'class' => $class
	        ];

	        // HTML
	        $html = '';
	        ob_start();

	        // Template path
            $template_path = ovabrw_locate_template( 'shortcode/st-calendar.php' );

	        if ( !file_exists( $template_path ) ) {
	            esc_html_e( 'No templates found.', 'ova-brw' );
	        } else {
	            ovabrw_get_template( 'shortcode/st-calendar.php', $args );
	        }

	        $html = ob_get_contents();
	        ob_end_clean();

	        return apply_filters( OVABRW_PREFIX.'st_product_calendar_shortcode_html', $html, $atts );
	    }

	    /**
	     * Shortcode: Product table price
	     */
	    public function st_table_price_product_shortcode( $atts = [] ) {
	        $atts = extract( shortcode_atts( [
	        	'id'    => '',
	            'class' => ''
	        ], $atts ));

	        // Agruments
	        $args = [
	        	'id'    => $id,
	            'class' => $class
	        ];

	        // HTML
	        $html = '';
	        ob_start();

	        // Template path
            $template_path = ovabrw_locate_template( 'shortcode/st-table-price.php' );

	        if ( !file_exists( $template_path ) ) {
	            esc_html_e( 'No templates found.', 'ova-brw' );
	        } else {
	            ovabrw_get_template( 'shortcode/st-table-price.php', $args );
	        }

	        $html = ob_get_contents();
	        ob_end_clean();

	        return apply_filters( OVABRW_PREFIX.'st_table_price_product_shortcode_html', $html, $atts );
	    }

	    /**
	     * Shortcode: Product feature
	     */
	    public function st_feature_product_shortcode( $atts = [] ) {
	        $atts = extract( shortcode_atts([
	        	'id'    => '',
	            'class' => ''
	        ], $atts ));

	        // Agrumnets
	        $args = [
	        	'id'    => $id,
	            'class' => $class
	        ];

	        // HTML
	        $html = '';
	        ob_start();

	        // Template path
            $template_path = ovabrw_locate_template( 'shortcode/st-features.php' );

	        if ( !file_exists( $template_path ) ) {
	            esc_html_e( 'No templates found', 'ova-brw' );
	        } else {
	            ovabrw_get_template( 'shortcode/st-features.php', $args );
	        }

	        $html = ob_get_contents();
	        ob_end_clean();

	        return apply_filters( OVABRW_PREFIX.'st_feature_product_shortcode_html', $html, $atts );
	    }

	    /**
	     * Shortcode: Products
	     */
	    public function products_shortcode( $atts = [] ) {
	        $atts = extract( shortcode_atts([
	        	'class'             => '',
	            'posts_per_page'    => '',
	            'order'             => '',
	            'orderby'           => '',
	            'categories'        => '',
	            'card'              => '',
	            'column'            => ''
	        ], $atts ));

	        if ( $posts_per_page == '' ) $posts_per_page = 6;
	        if ( $order == '' ) $order = 'DESC';
	        if ( $orderby == '' ) $orderby = 'date';
	        if ( $card == '' ) $card = 'card1';
	        if ( $column == '' ) $column = 3;

	        // Agruments
	        $args = [
	        	'class'             => $class,
	            'posts_per_page'    => $posts_per_page,
	            'order'             => $order,
	            'orderby'           => $orderby,
	            'categories'        => $categories,
	            'card'              => $card,
	            'column'            => $column
	        ];

	        // HTML
	        $html = '';
	        ob_start();

	        // Template path
            $template_path = ovabrw_locate_template( 'shortcode/list-products.php' );

	        if ( !file_exists( $template_path ) ) {
	            esc_html_e( 'No templates found', 'ova-brw' );
	        } else {
	            ovabrw_get_template( 'shortcode/list-products.php', $args );
	        }

	        $html = ob_get_contents();
	        ob_end_clean();

	        return apply_filters( OVABRW_PREFIX.'products_shortcode_html', $html, $atts );
	    }

	    /**
	     * Shortcode: Product images
	     */
	    public function product_images_shortcode( $atts ) {
	        $atts = extract( shortcode_atts([
	        	'id'    => '',
                'class' => ''
	        ], $atts ));

	        // Agruments
	        $args = [
	        	'id'    => $id,
	            'class' => $class
	        ];

	        // HTML
	        $html = '';
	        ob_start();

	        // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-images.php' );

	        if ( !file_exists( $template_path ) ) {
	            esc_html_e( 'No templates found.', 'ova-brw' );
	        } else {
	            ovabrw_get_template( 'shortcode/product-images.php', $args );
	        }

	        $html = ob_get_contents();
	        ob_end_clean();

	        return apply_filters( OVABRW_PREFIX.'product_images_shortcode_html', $html, $atts );
	    }

	    /**
	     * Shortcode: Product unavailable dates
	     */
	    public function product_unavailable_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-unavailable.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-unavailable.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_unavailable_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product title
         */
        public function product_title_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-title.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-title.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_title_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product price
         */
        public function product_price_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-price.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-price.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_price_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product review
         */
        public function product_review_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-review.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-review.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_review_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product taxonomy
         */
        public function product_taxonomy_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-taxonomy.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-taxonomy.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_taxonomy_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product meta
         */
        public function product_meta_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-meta.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-meta.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_meta_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product features
         */
        public function product_features_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-features.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-features.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_features_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product specifications
         */
        public function product_specifications_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-specifications.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-specifications.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_specifications_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product description
         */
        public function product_short_description_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-short-description.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-short-description.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_short_description_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product tabs
         */
        public function product_tabs_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-tabs.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-tabs.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_tabs_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Product related
         */
        public function product_related_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'id'    => '',
                'class' => ''
            ], $atts ));

            // Agruments
            $args = [
            	'id'    => $id,
                'class' => $class
            ];

            // HTML
            $html = '';
            ob_start();

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/product-related.php' );

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/product-related.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'product_related_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Search hotel
         */
        public function search_hotel_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'field_1'            	=> 'category',
                'field_2'            	=> 'pickup_date',
                'field_3'            	=> 'dropoff_date',
                'field_4'            	=> 'guest',
                'field_5'            	=> '',
                'field_6'            	=> '',
                'field_7'            	=> '',
                'field_8'            	=> '',
                'default_cat'       	=> '', // term_slug
                'category_in'       	=> '', // term_id: 123|456
                'category_not_in'     	=> '', // term_id: 123|456
                'list_taxonomy_custom' 	=> '', // slug: test1|test2
                'card'              	=> 'card1',
                'columns'           	=> 'column4', // column1 or column2 or column3 or column4 or column5
                'orderby'           	=> 'date',
                'order'             	=> 'DESC',
                'result_url'        	=> '' // redirect to custom page
            ], $atts ));

            // Custom taxonomies
            $data_custom_taxonomies = [];
            $list_taxonomy_custom   = explode( '|', $list_taxonomy_custom );

            if ( ovabrw_array_exists( $list_taxonomy_custom ) ) {
                foreach ( $list_taxonomy_custom as $taxonomy_name ) {
                    if ( $taxonomy_name ) {
                        $data_custom_taxonomies[] = [
                            'taxonomy_custom' => trim( $taxonomy_name )
                        ];
                    }
                }
            }

            // Agruments
            $args = [
            	'field_1'            => $field_1,
                'field_2'            => $field_2,
                'field_3'            => $field_3,
                'field_4'            => $field_4,
                'field_5'            => $field_5,
                'field_6'            => $field_6,
                'field_7'            => $field_7,
                'field_8'            => $field_8,
                'default_cat'       => $default_cat,
                'category_in'       => explode( '|', $category_in ),
                'category_not_in'   => explode( '|', $category_not_in ),
                'list_taxonomy_custom' => $data_custom_taxonomies,
                'orderby'           => $orderby,
                'order'             => $order,
                'card'              => $card,
                'columns'           => $columns, // column1 or column2 or column3 or column4 or column5
                'result_url'        => $result_url // redirect to custom page
            ];

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/search-hotel.php' );

            // HTML
            $html = '';
            ob_start();

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/search-hotel.php', $args );
            }
            
            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'search_hotel_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Search ajax hotel
         */
        public function search_ajax_hotel_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
            	'field_1'            	=> 'name',
                'field_2'            	=> 'category',
                'field_3'            	=> 'pickup_date',
                'field_4'            	=> 'dropoff_date',
                'field_5'            	=> 'guest',
                'field_6'            	=> '',
                'field_7'            	=> '',
                'field_8'            	=> '',
                'form_search_position' 	=> 'left',
                'default_cat'       	=> '', // term_slug
                'category_in'       	=> '', // term_id: 123|456
                'category_not_in'     	=> '', // term_id: 123|456
                'list_taxonomy_custom' 	=> '',
                'card'              	=> 'card1',
                'posts_per_page'    	=> 6,
                'result_column'     	=> 'two-column', // one-column or two-column or three-column
                'orderby'           	=> 'date',
                'order'             	=> 'DESC',
            ], $atts ));

            // Custom taxonomies
            $data_custom_taxonomies = [];
            $list_taxonomy_custom   = explode( '|', $list_taxonomy_custom );

            if ( ovabrw_array_exists( $list_taxonomy_custom ) ) {
                foreach ( $list_taxonomy_custom as $taxonomy_name ) {
                    if ( $taxonomy_name ) {
                        $data_custom_taxonomies[] = [
                            'custom_taxonomy' => $taxonomy_name
                        ];
                    }
                }
            }

            // Agruments
            $args = [
            	'field_1'            	=> $field_1,
                'field_2'            	=> $field_2,
                'field_3'            	=> $field_3,
                'field_4'            	=> $field_4,
                'field_5'            	=> $field_5,
                'field_6'            	=> $field_6,
                'field_7'            	=> $field_7,
                'field_8'            	=> $field_8,
                'form_search_position' 	=> $form_search_position,
                'default_cat'       	=> $default_cat,
                'category_in'       	=> explode( '|', $category_in ),
                'category_not_in'   	=> explode( '|', $category_not_in ),
                'list_taxonomy_custom' 	=> $data_custom_taxonomies,
                'orderby'           	=> $orderby,
                'order'             	=> $order,
                'card'              	=> $card,
                'posts_per_page'    	=> $posts_per_page,
                'result_column'     	=> $result_column, // one-column or two-column or three-column
                'orderby'           	=> $orderby,
                'order'             	=> $order
            ];

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/search-ajax-hotel.php' );

            // HTML
            $html = '';
            ob_start();

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/search-ajax-hotel.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'search_ajax_hotel_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Search taxi
         */
        public function search_taxi_shortcode( $atts ) {
            // Enqueue google map
            $api_key = ovabrw_get_setting( 'google_key_map', false );
            if ( $api_key ) {
                wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&libraries=places&loading=async&callback=Function.prototype', false, true );
            }

            // Attributes
            $atts = extract( shortcode_atts([
            	'layout'            => 'layout1',
                'column'            => 2,
                'fields'            => 'pickup-location|dropoff-location|pickup-date|category|number-seats|quantity',
                'default_category'  => '', // term_slug
                'incl_category'     => '', // term_id: 123|456
                'excl_category'     => '', // term_id: 123|456
                'custom_taxonomies' => '',
                'result_url'        => '',
                'orderby'           => 'date',
                'order'             => 'DESC',
                'map_type'          => 'geocode',
                'bounds'            => '',
                'lat'               => '',
                'lng'               => '',
                'radius'            => '',
                'restrictions'      => '' // Ex: AU|US|FR|VI
            ], $atts ));

            // Convert data
            $data_field = [];
            $fields     = explode( '|', $fields );

            if ( ovabrw_array_exists( $fields ) ) {
                foreach ( $fields as $field_name ) {
                    $field_label = $field_placeholder = '';

                    switch ( $field_name ) {
                    	case 'pickup-location':
                    		$field_label        = esc_html__( 'Pick Up Location', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Type Location', 'ova-brw' );
                    		break;
                    	case 'dropoff-location':
                    		$field_label        = esc_html__( 'Drop Off Location', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Type Location', 'ova-brw' );
                    		break;
                    	case 'pickup-date':
                    		$field_label        = esc_html__( 'Pick Up Date', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Enter Date', 'ova-brw' );
                    		break;
                    	case 'category':
                    		$field_label        = esc_html__( 'Taxi - Type', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Select Type', 'ova-brw' );
                    		break;
                    	case 'number-seats':
                    		$field_label        = esc_html__( 'Number Of Seats', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Enter Seat Number', 'ova-brw' );
                    		break;
                    	case 'quantity':
                    		$field_label        = esc_html__( 'Quantity', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Enter Quantity', 'ova-brw' );
                    		break;
                    	default:
                    		// code...
                    		break;
                    }

                    $data_field[] = [
                        'field_name'        => $field_name,
                        'field_label'       => $field_label,
                        'field_placeholder' => $field_placeholder,
                    ];
                }
            }

            // Custom taxonomies
            $data_custom_taxonomies = [];
            $custom_taxonomies      = explode( '|', $custom_taxonomies );

            if ( ovabrw_array_exists( $custom_taxonomies ) ) {
                foreach ( $custom_taxonomies as $taxonomy_name ) {
                    if ( $taxonomy_name ) {
                        $data_custom_taxonomies[] = [
                            'custom_taxonomy' => trim( $taxonomy_name )
                        ];
                    }
                }
            }

            // Restrictions
            $data_restrictions  = [];
            $restrictions       = explode( '|', $restrictions );

            if ( ovabrw_array_exists( $restrictions ) ) {
                foreach ( $restrictions as $restriction ) {
                    if ( $restriction ) {
                        $data_restrictions[] = $restriction;
                    }
                }
            }

            if ( $incl_category ) {
                $incl_category = explode( '|', $incl_category );
                $incl_category = array_filter( $incl_category, function( $value ) {
                    return trim( $value ) !== '';
                });
            }

            if ( $excl_category ) {
                $excl_category = explode( '|', $excl_category );
                $excl_category = array_filter( $excl_category, function( $value ) {
                    return trim( $value ) !== '';
                });
            }

            // Agruments
            $args = [
            	'fields'            => $data_field,
                'default_category'  => $default_category,
                'incl_category'     => $incl_category,
                'excl_category'     => $excl_category,
                'custom_taxonomies' => $data_custom_taxonomies,
                'result_url'        => $result_url,
                'orderby'           => $orderby,
                'order'             => $order,
                'map_type'          => $map_type,
                'bounds'            => $bounds,
                'lat'               => $lat,
                'lng'               => $lng,
                'radius'            => $radius,
                'restrictions'      => $data_restrictions
            ];

            $template   = ovabrw_locate_template( 'shortcode/search-taxi.php' );
            $path       = 'shortcode/search-taxi.php';

            // Layout
            if ( 'layout1' === $layout ) {
                $args['layout1_columns'] = $column;
            }
            if ( 'layout2' === $layout ) {
                $args['layout2_columns'] = $column;

                $template   = ovabrw_locate_template( 'shortcode/search-taxi2.php' );
                $path       = 'shortcode/search-taxi2.php';
            }

            // HTML
            $html = '';
            ob_start();

            if ( !file_exists( $template ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( $path, $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'search_taxi_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Search taxi ajax
         */
        public function search_taxi_ajax_shortcode( $atts ) {
            // Enqueue google map
            $api_key = ovabrw_get_setting( 'google_key_map', false );
            if ( $api_key ) {
                wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&libraries=places&loading=async&callback=Function.prototype', false, true );
            }

            $atts = extract( shortcode_atts([
            	'fields'            => 'pickup-location|dropoff-location|pickup-date|category|number-seats|quantity',
                'columns'           => 4,
                'default_category'  => '', // term_slug
                'incl_category'     => '', // term_id: 123|456
                'excl_category'     => '', // term_id: 123|456
                'custom_taxonomies' => '',
                'map_type'          => 'geocode',
                'bounds'            => '',
                'lat'               => '',
                'lng'               => '',
                'radius'            => '',
                'restrictions'      => '', // Ex: AU|US|FR|VI
                'card_template'     => 'card1',
                'posts_per_page'    => 6,
                'column'            => 'three-column', // one-column or two-column or three-column
                'orderby'           => 'date',
                'order'             => 'DESC',
                'term'              => '', // term_slug
                'pagination'        => 'yes'
            ], $atts ));

            // Convert data
            $data_field = [];
            $fields     = explode( '|', $fields );

            if ( ovabrw_array_exists( $fields ) ) {
                foreach ( $fields as $field_name ) {
                    $field_label = $field_placeholder = '';

                    switch ( $field_name ) {
                    	case 'pickup-location':
                    		$field_label        = esc_html__( 'Pick Up Location', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Type Location', 'ova-brw' );
                    		break;
                    	case 'dropoff-location':
                    		$field_label        = esc_html__( 'Drop Off Location', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Type Location', 'ova-brw' );
                    		break;
                    	case 'pickup-date':
                    		$field_label        = esc_html__( 'Pick Up Date', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Enter Date', 'ova-brw' );
                    		break;
                    	case 'category':
                    		$field_label        = esc_html__( 'Taxi - Type', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Select Type', 'ova-brw' );
                    		break;
                    	case 'number-seats':
                    		$field_label        = esc_html__( 'Number Of Seats', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Enter Seat Number', 'ova-brw' );
                    		break;
                    	case 'quantity':
                    		$field_label        = esc_html__( 'Quantity', 'ova-brw' );
                        	$field_placeholder  = esc_html__( 'Enter Quantity', 'ova-brw' );
                    		break;
                    	default:
                    		// code...
                    		break;
                    }

                    $data_field[] = [
                        'field_name'        => $field_name,
                        'field_label'       => $field_label,
                        'field_placeholder' => $field_placeholder,
                    ];
                }
            }

            // Custom taxonomies
            $data_custom_taxonomies = [];
            $custom_taxonomies      = explode( '|', $custom_taxonomies );

            if ( ovabrw_array_exists( $custom_taxonomies ) ) {
                foreach ( $custom_taxonomies as $taxonomy_name ) {
                    if ( $taxonomy_name ) {
                        $data_custom_taxonomies[] = [
                            'custom_taxonomy' => $taxonomy_name
                        ];
                    }
                }
            }

            // Restrictions
            $data_restrictions  = [];
            $restrictions       = explode( '|', $restrictions );

            if ( ovabrw_array_exists( $restrictions ) ) {
                foreach ( $restrictions as $restriction ) {
                    if ( $restriction ) {
                        $data_restrictions[] = $restriction;
                    }
                }
            }

            // Agruments
            $args = [
            	'fields'            => $data_field,
                'default_category'  => $default_category,
                'incl_category'     => explode( '|', $incl_category ),
                'excl_category'     => explode( '|', $excl_category ),
                'custom_taxonomies' => $data_custom_taxonomies,
                'orderby'           => $orderby,
                'order'             => $order,
                'map_type'          => $map_type,
                'bounds'            => $bounds,
                'lat'               => $lat,
                'lng'               => $lng,
                'radius'            => $radius,
                'restrictions'      => $data_restrictions,
                'card_template'     => $card_template,
                'posts_per_page'    => $posts_per_page,
                'column'            => $column, // one-column or two-column or three-column
                'orderby'           => $orderby,
                'order'             => $order,
                'term'              => $term, // term_slug
                'pagination'        => $pagination
            ];

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/search-taxi-ajax.php' );

            // HTML
            $html = '';
            ob_start();

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
                ovabrw_get_template( 'shortcode/search-taxi-ajax.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'search_taxi_ajax_shortcode_html', $html, $atts );
        }

        /**
         * Shortcode: Search ajax
         */
        public function search_ajax_shortcode( $atts ) {
            $atts = extract( shortcode_atts([
                'fields'                => 'product-name|category|pickup-location|dropoff-location|pickup-date|dropoff-date|product-tags|quantity', // product-name|pickup-location|dropoff-location|pickup-date|dropoff-date|category|product-tags|quantity
                'field_columns'         => 4,
                'position'              => 'top', // top or left or right
                'show_time'             => 'yes', // yes or no
                'default_pickup_location' 	=> '', // default pick-up location
                'default_dropoff_location' 	=> '', // default drop-off location
                'default_category'      => '', // term_slug
                'incl_category'         => '', // term_id: 123|456
                'excl_category'         => '', // term_id: 123|456
                'custom_taxonomies'     => '', // term_slug: tax1|tax2
                'show_results_found'    => 'yes', // yes or no
                'show_sort_by'          => 'yes', // yes or no
                'card_template'         => 'card1', // card1 or card2 or ... card 6
                'posts_per_page'        => 6,
                'columns'               => 3, // 1 or 2 or 3 or 4
                'orderby'               => 'date',
                'order'                 => 'DESC',
                'pagination'            => 'yes'
            ], $atts ));

            // Arguments
            $args = [
                'fields'                => $fields ? explode( '|', $fields ) : '',
                'field_columns'         => $field_columns,
                'position'              => $position,
                'show_time'             => $show_time,
                'default_category'      => $default_category,
                'default_pickup_location' 	=> $default_pickup_location,
                'default_dropoff_location' 	=> $default_dropoff_location,
                'incl_category'         => $incl_category ? explode( '|', $incl_category ) : '',
                'excl_category'         => $excl_category ? explode( '|', $excl_category ) : '',
                'custom_taxonomies'     => $custom_taxonomies ? explode( '|', $custom_taxonomies ) : '',
                'show_results_found'    => $show_results_found,
                'show_sort_by'          => $show_sort_by,
                'orderby'               => $orderby,
                'order'                 => $order,
                'card_template'         => $card_template,
                'posts_per_page'        => $posts_per_page,
                'columns'               => $columns,
                'orderby'               => $orderby,
                'order'                 => $order,
                'pagination'            => $pagination
            ];

            // Template path
            $template_path = ovabrw_locate_template( 'shortcode/search-ajax.php' );

            // HTML
            $html = '';
            ob_start();

            if ( !file_exists( $template_path ) ) {
                esc_html_e( 'No templates found.', 'ova-brw' );
            } else {
            	if ( in_array( 'location', $args['fields'] ) ) {
            		// Get google api key maps
					$api_key = ovabrw_get_setting( 'google_key_map' );
					if ( $api_key ) {
						wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='.$api_key.'&libraries=places&loading=async&callback=Function.prototype', null, true );
					}
            	}

            	// Get template
                ovabrw_get_template( 'shortcode/search-ajax.php', $args );
            }

            $html = ob_get_contents();
            ob_end_clean();

            return apply_filters( OVABRW_PREFIX.'search_ajax_shortcode_html', $html, $atts );
        }
	}

	new OVABRW_Shortcodes();
}