<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Search_Map
 */
if ( !class_exists( 'OVABRW_Widget_Search_Map' ) ) {

	class OVABRW_Widget_Search_Map extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_search_map';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Ajax Search Map', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-google-maps';
		}

		/**
		 * Get widget categories
		 */
		public function get_categories() {
			return [ 'ovabrw-products' ];
		}

		/**
		 * Get script depends
		 */
		public function get_script_depends() {
			// Google API Key
			$api_key = ovabrw_get_setting( 'google_key_map', false );
			if ( $api_key ) {
				wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&libraries=places&loading=async&callback=Function.prototype', false, true );
			}

			wp_enqueue_script( 'google-marker', OVABRW_PLUGIN_URI.'assets/libs/google_map/markerclusterer.js', [ 'jquery' ], false, true );

			/* Override market google map when more product the same location*/
			wp_enqueue_script( 'oms', OVABRW_PLUGIN_URI.'assets/libs/google_map/oms.js', [ 'jquery' ], false, true );

			/* Jquery ui */
			wp_enqueue_script( 'jquery-ui', OVABRW_PLUGIN_URI.'assets/libs/jquery-ui/jquery-ui.min.js', [ 'jquery' ], false, true);
			wp_enqueue_style( 'jquery-ui', OVABRW_PLUGIN_URI.'assets/libs/jquery-ui/jquery-ui.min.css', [], null );
			
			return [ 'ovabrw-script-elementor' ];
		}

		/**
		 * Register controls
		 */
		protected function register_controls() {
			// Search fields
			$search_fields = [
				'' 					=> esc_html__( 'Choose field', 'ova-brw' ),
				'name' 				=> esc_html__( 'Product name', 'ova-brw' ),
				'category' 			=> esc_html__( 'Category', 'ova-brw' ),
				'location' 			=> esc_html__( 'Location', 'ova-brw' ),
				'start_location' 	=> esc_html__( 'Pick-up location', 'ova-brw' ),
				'end_location' 		=> esc_html__( 'Drop-off location', 'ova-brw' ),
				'start_date' 		=> esc_html__( 'Pick-up date', 'ova-brw' ),
				'end_date' 			=> esc_html__( 'Drop-off date', 'ova-brw' ),
				'attribute' 		=> esc_html__( 'Attribute', 'ova-brw' ),
				'tag' 				=> esc_html__( 'Product tag', 'ova-brw' ),
				'quantity' 			=> esc_html__( 'Quantity', 'ova-brw' )
			];
			
			$this->start_controls_section(
				'section_setting',
				[
					'label' => esc_html__( 'Settings', 'ova-brw' ),
				]
			);

				// Default card
				$default_card = [
					'' => esc_html__( 'Default', 'ova-brw' )
				];

				// Get card templates
				$card_templates = ovabrw_get_card_templates();
				if ( !ovabrw_array_exists( $card_templates ) ) $card_templates = [];
				if ( ovabrw_global_typography() ) {
					$this->add_control(
						'card',
						[
							'label'   => esc_html__( 'Choose Card', 'ova-brw' ),
							'type'    => \Elementor\Controls_Manager::SELECT,
							'options' => array_merge( $default_card, $card_templates ),
							'default' => 'card1',
						]
					);
				}

				$this->add_control(
					'posts_per_page',
					[
						'label' 	=> esc_html__( 'Per Page', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> -1,
						'max' 		=> 50,
						'step' 		=> 1,
						'default' 	=> 12,
					]
				);

				$this->add_control(
					'column',
					[
						'label'   => esc_html__( 'Column', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SELECT,
						'default' => 'two-column',
						'options' => [
							'one-column' 	=> esc_html__('1 Column', 'ova-brw'),
							'two-column' 	=> esc_html__('2 Columns', 'ova-brw'),
							'three-column' 	=> esc_html__('3 Columns', 'ova-brw'),
						],
					]
				);

				$orderby = apply_filters( OVABRW_PREFIX.'search_map_orderby', [
					'title' 		=> esc_html__( 'Title', 'ova-brw' ),
					'ID' 			=> esc_html__( 'ID', 'ova-brw' ),
					'date' 			=> esc_html__( 'Date', 'ova-brw' ),
					'modified' 		=> esc_html__( 'Modified', 'ova-brw' ),
					'rand' 			=> esc_html__( 'Random', 'ova-brw' ),
					'menu_order' 	=> esc_html__( 'Menu Order', 'ova-brw' )
				]);

				if ( 'yes' === get_option( 'woocommerce_enable_reviews' ) ) {
					$orderby['rating'] = esc_html__( 'Average rating', 'ova-brw' );
				}

				$this->add_control(
					'orderby',
					[
						'label'   => esc_html__( 'Order By', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SELECT,
						'default' => 'date',
						'options' => $orderby,
					]
				);

				$this->add_control(
					'order',
					[
						'label'   => esc_html__( 'Order', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SELECT,
						'default' => 'DESC',
						'options' => [
							'ASC' 	=> esc_html__('Ascending', 'ova-brw'),
							'DESC' 	=> esc_html__('Descending', 'ova-brw'),
						],
					]
				);

				$this->add_control(
					'show_filter',
					[
						'label' 		=> esc_html__( 'Show Filters', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'default' 		=> 'yes',
					]
				);

				$this->add_control(
					'show_featured',
					[
						'label' 		=> esc_html__( 'Show Featured', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'default' 		=> 'no',
					]
				);

				$this->add_control(
					'show_map',
					[
						'label' 		=> esc_html__( 'Show Map', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'default' 		=> 'yes',
					]
				);

				$this->add_control(
					'show_default_location',
					[
						'label' 		=> esc_html__( 'Default Location', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'default' 		=> 'no',
					]
				);

				$this->add_control(
					'zoom',
					[
						'label' 	=> esc_html__( 'Zoom Map', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 1,
						'max' 		=> 20,
						'step' 		=> 1,
						'default' 	=> 4,
						'condition' => [
							'show_map' => 'yes'
						]
					]
				);

				$this->add_control(
					'marker_option',
					[
						'label'   		=> esc_html__( 'Marker Select', 'ova-brw' ),
						'description' 	=> esc_html__( 'You should use Icon to display exactly position', 'ova-brw' ),
						'type'    		=> \Elementor\Controls_Manager::SELECT,
						'default' 		=> 'icon',
						'options' 		=> [
							'icon' 		=> esc_html__( 'Icon', 'ova-brw' ),
							'price' 	=> esc_html__( 'Price', 'ova-brw' ),
							'date' 		=> esc_html__( 'Start Date', 'ova-brw' ),
						],
					]
				);

				$this->add_control(
					'marker_icon',
					[
						'label' 	=> esc_html__( 'Choose Image', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::MEDIA,
						'condition' => [
							'marker_option' => 'icon'
						]
					]
				);

				$this->add_control(
					'field_1',
					[
						'label'   	=> esc_html__( 'Field 1', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'name',
						'separator' => 'before',
						'options' 	=> $search_fields,
						'condition' => [
							'show_filter' => 'yes'
						]
					]
				);

				$this->add_control(
					'field_2',
					[
						'label'   	=> esc_html__( 'Field 2', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'category',
						'separator' => 'before',
						'options' 	=> $search_fields,
						'condition' => [
							'show_filter' => 'yes'
						]
					]
				);

				$this->add_control(
					'field_3',
					[
						'label'   	=> esc_html__( 'Field 3', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'start_location',
						'separator' => 'before',
						'options' 	=> $search_fields,
						'condition' => [
							'show_filter' => 'yes'
						]
					]
				);

				$this->add_control(
					'field_4',
					[
						'label'   	=> esc_html__( 'Field 4', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'end_location',
						'separator' => 'before',
						'options' 	=> $search_fields,
						'condition' => [
							'show_filter' => 'yes'
						]
					]
				);

				$this->add_control(
					'field_5',
					[
						'label'   	=> esc_html__( 'Field 5', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'start_date',
						'separator' => 'before',
						'options' 	=> $search_fields,
						'condition' => [
							'show_filter' => 'yes'
						]
					]
				);

				$this->add_control(
					'field_6',
					[
						'label'   	=> esc_html__( 'Field 6', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'end_date',
						'separator' => 'before',
						'options' 	=> $search_fields,
						'condition' => [
							'show_filter' => 'yes'
						]
					]
				);

				$this->add_control(
					'field_7',
					[
						'label'   	=> esc_html__( 'Field 7', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> '',
						'separator' => 'before',
						'options' 	=> $search_fields,
						'condition' => [
							'show_filter' => 'yes'
						]
					]
				);

				$this->add_control(
					'field_8',
					[
						'label'   	=> esc_html__( 'Field 8', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> '',
						'separator' => 'before',
						'options' 	=> $search_fields,
						'condition' => [
							'show_filter' => 'yes'
						]
					]
				);

				$this->add_control(
					'field_9',
					[
						'label'   	=> esc_html__( 'Field 9', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> '',
						'separator' => 'before',
						'options' 	=> $search_fields,
						'condition' => [
							'show_filter' => 'yes'
						]
					]
				);

				// Data taxonomy
				$data_taxonomy[''] = esc_html__( 'Select Taxonomy', 'ova-brw' );

				// Get taxonomies
				$taxonomies = ovabrw_get_option( 'custom_taxonomy', [] );
				if ( ovabrw_array_exists( $taxonomies ) ) {
					foreach ( $taxonomies as $key => $value ) {
						$data_taxonomy[$key] = $value['name'];
					}
				}

				// New repeater
				$repeater = new \Elementor\Repeater();

				$repeater->add_control(
					'taxonomy_custom', [
						'label' 		=> esc_html__( 'Taxonomy Custom', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT,
						'label_block' 	=> true,
						'options' 		=> $data_taxonomy,
					]
				);

				$this->add_control(
					'list_taxonomy_custom',
					[
						'label' 	=> esc_html__( 'List Taxonomy Custom', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::REPEATER,
						'fields' 	=> $repeater->get_controls(),
						'condition' => [
							'show_filter' => 'yes'
						],
						'separator' => 'before'
					]
				);

				$this->add_control(
					'show_time',
					[
						'label' 		=> esc_html__( 'Show time', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'default' 		=> 'yes',
						'description' 	=> esc_html__( 'Show the time picker in the pick-up date and drop-off date fields.', 'ova-brw' ),
						'separator' 	=> 'before'
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_default_field',
				[
					'label' => esc_html__( 'Default Field', 'ova-brw' ),
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

				$this->add_control(
					'default_cat',
					[
						'label' 		=> esc_html__( 'Default Category', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::TEXT,
						'description' 	=> esc_html__( 'Enter the product slug category', 'ova-brw' ),
					]
				);

				$this->add_control(
					'default_pickup_loc',
					[
						'label' 		=> esc_html__( 'Default Pick-up Location', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::TEXT,
						'description' 	=> esc_html__( 'Enter the name Pick-up Location', 'ova-brw' ),
					]
				);

				$this->add_control(
					'default_dropoff_loc',
					[
						'label' 		=> esc_html__( 'Default Drop-off Location', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::TEXT,
						'description' 	=> esc_html__( 'Enter the name Drop-off Location', 'ova-brw' ),
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_inlucde_exclude',
				[
					'label' 	=> esc_html__( 'Exclude/Include Category', 'ova-brw' ),
					'condition' => [
						'show_filter' => 'yes'
					]
				]
			);

				$this->add_control(
					'inlucde_exclude_type',
					[
						'label' 	=> esc_html__( 'Type', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'default',
						'options' 	=> [
							'default' 		=> esc_html__( 'Default', 'ova-brw' ),
							'multi_select' 	=> esc_html__( 'Multi Select', 'ova-brw' ),
						]
					]
				);

				$this->add_control(
					'category_not_in',
					[
						'label'   		=> esc_html__( 'Category Not In', 'ova-brw' ),
						'type'    		=> \Elementor\Controls_Manager::TEXT,
						'description' 	=> esc_html__( 'Enter the product category IDs. IDs are separated by "|". Ex: 1|2|3.', 'ova-brw' ),
						'condition' 	=> [
							'inlucde_exclude_type' => 'default'
						]
					]
				);

				// Product categories
				$product_categories = [];

				// Get product categories
				$categories = get_categories([
					'taxonomy' 	=> 'product_cat',
					'orderby' 	=> 'name',
					'order' 	=> 'ASC'
				]);

				// Loop
			  	if ( ovabrw_array_exists( $categories ) ) {
				  	foreach ( $categories as $category ) {
					  	$product_categories[$category->term_id] = $category->name;
				  	}
			  	} else {
				  	$product_categories[''] = esc_html__( 'Category not found', 'ova-brw' );
			  	}

				$this->add_control(
					'category_not_in_select',
					[
						'label'   		=> esc_html__( 'Category Not In', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT2,
						'label_block' 	=> true,
						'multiple' 		=> true,
						'options' 		=> $product_categories,
						'condition' 	=> [
							'inlucde_exclude_type' => 'multi_select'
						]
					]
				);

				$this->add_control(
					'category_in',
					[
						'label'   		=> esc_html__( 'Category In', 'ova-brw' ),
						'type'    		=> \Elementor\Controls_Manager::TEXT,
						'description' 	=> esc_html__( 'Enter the product category IDs. IDs are separated by "|". Ex: 1|2|3.', 'ova-brw' ),
						'condition' 	=> [
							'inlucde_exclude_type' => 'default'
						]
					]
				);

				$this->add_control(
					'category_in_select',
					[
						'label'   		=> esc_html__( 'Category In', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT2,
						'label_block' 	=> true,
						'multiple' 		=> true,
						'options' 		=> $product_categories,
						'condition' 	=> [
							'inlucde_exclude_type' => 'multi_select'
						]
					]
				);
			
			$this->end_controls_section();
		}

		/**
		 * Render HTML
		 */
		protected function render() {
			// Get settings
			$settings = $this->get_settings_for_display();

			ob_start();
			ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_search_map', 'elementor/ovabrw-search-map.php', $settings ), $settings );
			echo ob_get_clean();
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Search_Map() );
}