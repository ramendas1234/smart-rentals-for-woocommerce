<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Search_Hotel
 */
if ( !class_exists( 'OVABRW_Widget_Search_Hotel' ) ) {

	class OVABRW_Widget_Search_Hotel extends \Elementor\Widget_Base {
		
		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_search_hotel';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Search Hotel', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-site-search';
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
			// BRW icon
		    if ( apply_filters( OVABRW_PREFIX.'use_brwicon', true ) ) {
		    	wp_enqueue_style( 'ovabrw-icon', OVABRW_PLUGIN_URI.'assets/libs/flaticons/brwicon/font/flaticon_brw.css', [], null );
		    }

			return [ 'ovabrw-script-elementor' ];
		}

		/**
		 * Register controls
		 */
		protected function register_controls() {

			$this->start_controls_section(
				'section_search_form',
				[
					'label' => esc_html__( 'Search Form', 'ova-brw' ),
				]
			);

				$this->add_control(
					'columns',
					[
						'label'   	=> esc_html__( 'Column', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'column4',
						'options' 	=> [
							'column1' => esc_html__( 'Column 1', 'ova-brw' ),
							'column2' => esc_html__( 'Column 2', 'ova-brw' ),
							'column3' => esc_html__( 'Column 3', 'ova-brw' ),
							'column4' => esc_html__( 'Column 4', 'ova-brw' ),
							'column5' => esc_html__( 'Column 5', 'ova-brw' ),
						],
					]
				);

				// Search fields
				$search_fields = [
					'' 					=> esc_html__( 'Select field', 'ova-brw' ),
					'name' 				=> esc_html__( 'Product name', 'ova-brw' ),
					'category' 			=> esc_html__( 'Category', 'ova-brw' ),
					'pickup_date' 		=> esc_html__( 'Pick-up date', 'ova-brw' ),
					'dropoff_date' 		=> esc_html__( 'Drop-off date', 'ova-brw' ),
					'attribute' 		=> esc_html__( 'Attribute', 'ova-brw' ),
					'quantity' 			=> esc_html__( 'Quantity', 'ova-brw' ),
					'guest' 			=> esc_html__( 'Guest', 'ova-brw' ),
					'tags' 				=> esc_html__( 'Product tag', 'ova-brw' )
				];

				$this->add_control(
					'show_time',
					[
						'label' 	=> esc_html__( 'Show Time', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 	=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' => esc_html__( 'Hide', 'ova-brw' ),
						'default' 	=> 'no',
					]
				);

				$this->add_control(
					'field_1',
					[
						'label'   	=> esc_html__( 'Field 1', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'category',
						'separator' => 'before',
						'options' 	=> $search_fields,
					]
				);

				$this->add_control(
					'field_2',
					[
						'label'   	=> esc_html__( 'Field 2', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'pickup_date',
						'separator' => 'before',
						'options' 	=> $search_fields,
					]
				);

				$this->add_control(
					'field_3',
					[
						'label'   	=> esc_html__( 'Field 3', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'dropoff_date',
						'separator' => 'before',
						'options' 	=> $search_fields,
					]
				);

				$this->add_control(
					'field_4',
					[
						'label'   	=> esc_html__( 'Field 4', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'guest',
						'separator' => 'before',
						'options' 	=> $search_fields,
					]
				);

				$this->add_control(
					'field_5',
					[
						'label'   	=> esc_html__( 'Field 5', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> '',
						'separator' => 'before',
						'options' 	=> $search_fields,
					]
				);

				$this->add_control(
					'field_6',
					[
						'label'   	=> esc_html__( 'Field 6', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> '',
						'separator' => 'before',
						'options' 	=> $search_fields,
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
					]
				);

				// Data taxonomy
				$data_taxonomy[''] = esc_html__( 'Select Taxonomy', 'ova-brw' );

				// Get taxonomies
				$taxonomies = ovabrw_get_option( 'custom_taxonomy', [] );
				if ( ovabrw_array_exists( $taxonomies ) ) {
					foreach( $taxonomies as $key => $value ) {
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
						'default' 	=> [
							'' => esc_html__( 'Select Taxonomy', 'ova-brw' ),
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_guests',
				[
					'label' => esc_html__( 'Guests', 'ova-brw' ),
				]
			);

				$this->add_control(
					'max_guests',
					[
						'label' 	=> esc_html__( 'Maximum Guests', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 1,
						'step' 		=> 1,
						'default' 	=> 10,
					]
				);

				$this->add_control(
					'min_guests',
					[
						'label' 	=> esc_html__( 'Min Guests', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 1,
						'step' 		=> 1,
						'default' 	=> 1,
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_adults',
				[
					'label' => esc_html__( 'Adults', 'ova-brw' ),
				]
			);

				$this->add_control(
					'adults_label',
					[
						'label' 	=> esc_html__( 'Label', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Adults', 'ova-brw' ),
					]
				);

				$this->add_control(
					'default_adult_number',
					[
						'label' 	=> esc_html__( 'Default Adults Number', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 1,
						'step' 		=> 1,
						'default' 	=> 2,
					]
				);

				$this->add_control(
					'max_adults',
					[
						'label' 	=> esc_html__( 'Maximum Adults', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 1,
						'step' 		=> 1,
						'default' 	=> 10,
					]
				);

				$this->add_control(
					'min_adults',
					[
						'label' 	=> esc_html__( 'Minimum Adults', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 1,
						'step' 		=> 1,
						'default' 	=> 1,
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_childrens',
				[
					'label' => esc_html__( 'Children', 'ova-brw' ),
				]
			);

				$this->add_control(
					'children_label',
					[
						'label' 	=> esc_html__( 'Label', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Children', 'ova-brw' ),
					]
				);

				$this->add_control(
					'default_children_number',
					[
						'label' 	=> esc_html__( 'Default Children Number', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 0,
						'step' 		=> 1,
						'default' 	=> 0,
					]
				);

				$this->add_control(
					'max_children',
					[
						'label' 	=> esc_html__( 'Maximum Children', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 0,
						'step' 		=> 1,
						'default' 	=> 3,
					]
				);

				$this->add_control(
					'min_children',
					[
						'label' 	=> esc_html__( 'Minimum Children', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 0,
						'step' 		=> 1,
						'default' 	=> 0,
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_babies',
				[
					'label' => esc_html__( 'Babies', 'ova-brw' ),
				]
			);

				$this->add_control(
					'babies_label',
					[
						'label' 	=> esc_html__( 'Label', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Babies', 'ova-brw' ),
					]
				);

				$this->add_control(
					'default_babies_number',
					[
						'label' 	=> esc_html__( 'Default Babies Number', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 0,
						'step' 		=> 1,
						'default' 	=> 0,
					]
				);

				$this->add_control(
					'max_babies',
					[
						'label' 	=> esc_html__( 'Maximum Babies', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 0,
						'step' 		=> 1,
						'default' 	=> 3,
					]
				);

				$this->add_control(
					'min_babies',
					[
						'label' 	=> esc_html__( 'Minimum Babies', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'min' 		=> 0,
						'step' 		=> 1,
						'default' 	=> 0,
					]
				);

			$this->end_controls_section();

			/* Section Search Result */
			$this->start_controls_section(
				'section_search_result',
				[
					'label' => esc_html__( 'Search Result', 'ova-brw' ),
				]
			);

				$this->add_control(
					'search_result',
					[
						'label' 	=> esc_html__( 'Pages', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'default',
						'options' 	=> [
							'default'  	=> esc_html__( 'Default', 'ova-brw' ),
							'new_page' 	=> esc_html__( 'New Page', 'ova-brw' ),
						],
					]
				);

				$this->add_control(
					'search_result_url',
					[
						'label' 		=> esc_html__( 'Link', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::URL,
						'placeholder' 	=> esc_html__( 'https://your-link.com', 'ova-brw' ),
						'dynamic' 		=> [
							'active' => true,
						],
						'default' 		=> [
							'url' 			=> '#',
							'is_external' 	=> false,
							'nofollow' 		=> false,
						],
						'condition' 	=> [
							'search_result' => 'new_page',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_label_field',
				[
					'label' => esc_html__( 'Label Field', 'ova-brw' ),
				]
			);

				$this->add_control(
					'field_name',
					[
						'label' 	=> esc_html__( 'Label Name', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Product Name', 'ova-brw' ),
					]
				);

				$this->add_control(
					'field_category',
					[
						'label' 	=> esc_html__( 'Label Category', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Select Category', 'ova-brw' ),
					]
				);

				$this->add_control(
					'field_pickup_date',
					[
						'label' 	=> esc_html__( 'Label Pick-up Date', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Pick-up Date', 'ova-brw' ),
					]
				);

				$this->add_control(
					'field_dropoff_date',
					[
						'label' 	=> esc_html__( 'Label Drop-off Date', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Drop-off Date', 'ova-brw' ),
					]
				);

				$this->add_control(
					'field_attribute',
					[
						'label' 	=> esc_html__( 'Label Attribute', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Select Attribute', 'ova-brw' ),
					]
				);

				$this->add_control(
					'field_guest',
					[
						'label' 	=> esc_html__( 'Label Guest', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Guests', 'ova-brw' ),
					]
				);

				$this->add_control(
					'field_tags',
					[
						'label' 	=> esc_html__( 'Label Tags', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Product tags', 'ova-brw' ),
					]
				);

				$this->add_control(
					'field_quantity',
					[
						'label' 	=> esc_html__( 'Label Quantity', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'default' 	=> esc_html__( 'Quantity', 'ova-brw' ),
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_placeholder_field',
				[
					'label' => esc_html__( 'Placeholder Field', 'ova-brw' ),
				]
			);

				$this->add_control(
					'placeholder_name',
					[
						'label' => esc_html__( 'Placeholder Name', 'ova-brw' ),
						'type' 	=> \Elementor\Controls_Manager::TEXT,
					]
				);

				$this->add_control(
					'placeholder_category',
					[
						'label' => esc_html__( 'Placeholder Category', 'ova-brw' ),
						'type' 	=> \Elementor\Controls_Manager::TEXT,
					]
				);

				$this->add_control(
					'placeholder_guest',
					[
						'label' => esc_html__( 'Placeholder Guest', 'ova-brw' ),
						'type' 	=> \Elementor\Controls_Manager::TEXT,
					]
				);

				$this->add_control(
					'placeholder_attribute',
					[
						'label' => esc_html__( 'Placeholder Attribute', 'ova-brw' ),
						'type' 	=> \Elementor\Controls_Manager::TEXT,
					]
				);

				$this->add_control(
					'placeholder_tags',
					[
						'label' => esc_html__( 'Placeholder Tags', 'ova-brw' ),
						'type' 	=> \Elementor\Controls_Manager::TEXT,
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_default_field',
				[
					'label' => esc_html__( 'Default Field', 'ova-brw' ),
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

			$this->end_controls_section();

			$this->start_controls_section(
				'section_inlucde_exclude',
				[
					'label' => esc_html__( 'Exclude/Include Category', 'ova-brw' ),
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
					'category_not_in',
					[
						'label'   		=> esc_html__( 'Category Not In', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT2,
						'label_block' 	=> true,
						'multiple' 		=> true,
						'options' 		=> $product_categories,
					]
				);

				$this->add_control(
					'category_in',
					[
						'label'   		=> esc_html__( 'Category In', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT2,
						'label_block' 	=> true,
						'multiple' 		=> true,
						'options' 		=> $product_categories,
					]
				);
			
			$this->end_controls_section();

			$this->start_controls_section(
				'section_product_search_style',
				[
					'label' => esc_html__( 'Content', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_responsive_control(
					'content_max_width',
					[
						'label' 		=> esc_html__( 'Max Width', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%' ],
						'range' 		=> [
							'px' 	=> [
								'min' 	=> 300,
								'max' 	=> 1000,
								'step' 	=> 5,
							],
							'%' 	=> [
								'min' 	=> 30,
								'max' 	=> 100,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search' => 'max-width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'content_grap',
					[
						'label' 		=> esc_html__( 'Grap', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px' ],
						'range' 		=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 80,
								'step' 	=> 5,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content' => 'grid-gap: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'content_background',
					[
						'label' 	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'content_box_shadow',
						'label' 	=> esc_html__( 'Box Shadow', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'content_border',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form',
						'separator' => 'before',
					]
				);

				$this->add_responsive_control(
					'content_border_radius',
					[
						'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'content_padding',
					[
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_fields_style',
				[
					'label' => esc_html__( 'Fields', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'fields_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text], {{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search select'
					]
				);

				$this->add_control(
					'fileds_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text], {{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search select' => 'color: {{VALUE}}!important',
						],
					]
				);

				$this->add_control(
					'fileds_icon_color',
					[
						'label' 	=> esc_html__( 'Icon Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search i' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'fileds_placeholder_color',
					[
						'label' 	=> esc_html__( 'Placeholder Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text]::placeholder' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'fileds_background',
					[
						'label' 	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text]' => 'background-color: {{VALUE}}',
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search select' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'fields_border_radius',
					[
						'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'fields_height',
					[
						'label' 		=> esc_html__( 'Height', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px'],
						'range' 		=> [
							'px' 	=> [
								'min' 	=> 35,
								'max' 	=> 120,
								'step' 	=> 5,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search input[type=text]' => 'height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search select' => 'height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'fileds_label_heading',
					[
						'label' => esc_html__( 'Label', 'ova-brw' ),
						'type' 	=> \Elementor\Controls_Manager::HEADING,
					]
				);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' 		=> 'fields_label_typography',
							'selector' 	=> '{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search .field-label'
						]
					);

					$this->add_control(
						'fileds_label_color',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-content .label_search .field-label' => 'color: {{VALUE}}',
							],
						]
					);

			$this->end_controls_section();


			$this->start_controls_section(
				'section_button_style',
				[
					'label' => esc_html__( 'Button', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'button_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit',
					]
				);

				$this->add_responsive_control(
					'button_text_align',
					[
						'label' 	=> esc_html__( 'Text Align', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::CHOOSE,
						'options' 	=> [
							'left' 	=> [
								'title' => esc_html__( 'Left', 'ova-brw' ),
								'icon' 	=> 'eicon-text-align-left',
							],
							'center' => [
								'title' => esc_html__( 'Center', 'ova-brw' ),
								'icon' 	=> 'eicon-text-align-center',
							],
							'right' => [
								'title' => esc_html__( 'Right', 'ova-brw' ),
								'icon' 	=> 'eicon-text-align-right',
							],
						],
						'toggle' 	=> true,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'text-align: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'button_border',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit',
					]
				);

				$this->add_responsive_control(
					'button_padding',
					[
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'button_margin',
					[
						'label' 		=> esc_html__( 'Marin', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'button_width',
					[
						'label' 		=> esc_html__( 'Width', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%' ],
						'range' 		=> [
							'px' 	=> [
								'min' 	=> 80,
								'max' 	=> 600,
								'step' 	=> 5,
							],
							'%' 	=> [
								'min' 	=> 20,
								'max' 	=> 100,
							],
						],
						'default' 	=> [
							'unit' 	=> '%'
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'button_height',
					[
						'label' 		=> esc_html__( 'Height', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px'],
						'range' 		=> [
							'px' 	=> [
								'min' 	=> 35,
								'max' 	=> 135,
								'step' 	=> 5,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs(
					'style_button_tabs',
				);

					$this->start_controls_tab(
						'style_button_normal_tab',
						[
							'label' => esc_html__( 'Normal', 'ova-brw' ),
						]
					);

						$this->add_control(
							'button_color_normal',
							[
								'label' 	=> esc_html__( 'Color', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'button_background_normal',
							[
								'label' 	=> esc_html__( 'Background', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit' => 'background-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'style_button_hover_tab',
						[
							'label' => esc_html__( 'Hover', 'ova-brw' ),
						]
					);

						$this->add_control(
							'button_color_hover',
							[
								'label' 	=> esc_html__( 'Color', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit:hover' => 'color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'button_background_hover',
							[
								'label' 	=> esc_html__( 'Background', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-hotel.ovabrw_wd_search .product-search-form .product-search-submit .ovabrw_btn_submit:hover' => 'background-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();
				$this->end_controls_tabs();
			$this->end_controls_section();
		}

		/**
		 * Render HTML
		 */
		protected function render() {
			// Get settings
			$settings = $this->get_settings_for_display();

			ob_start();
			ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_search_hotel', 'elementor/ovabrw-search-hotel.php', $settings ), $settings );
			echo ob_get_clean();
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Search_Hotel() );
}