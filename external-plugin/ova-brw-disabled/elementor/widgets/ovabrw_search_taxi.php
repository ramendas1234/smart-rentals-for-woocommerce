<?php defined( 'ABSPATH' ) || exit;

if ( !class_exists( 'OVABRW_Widget_Search_Taxi' ) ) {
	class OVABRW_Widget_Search_Taxi extends \Elementor\Widget_Base {
		
		public function get_name() {
			return 'ovabrw_search_taxi';
		}

		public function get_title() {
			return esc_html__( 'Search Taxi', 'ova-brw' );
		}

		public function get_icon() {
			return 'eicon-search-results';
		}

		public function get_categories() {
			return [ 'ovabrw-products' ];
		}

		public function get_script_depends() {
			// Get Google API Key Map
			$api_key = ovabrw_get_setting( 'google_key_map', false );
			if ( $api_key ) {
				wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&libraries=places&loading=async&callback=Function.prototype', false, true );
			}
			
			return [ 'ovabrw-script-elementor' ];
		}

		/**
		 * Register controls
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'section_settings',
				[
					'label' => esc_html__( 'Settings', 'ova-brw' ),
				]
			);

				$this->add_control(
					'layout',
					[
						'label'   	=> esc_html__( 'Layouts', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'layout1',
						'options' 	=> [
							'layout1' => esc_html__( 'Layout 1', 'ova-brw' ),
							'layout2' => esc_html__( 'Layout 2', 'ova-brw' )
						]
					]
				);

				$this->add_control(
					'layout1_columns',
					[
						'label'   	=> esc_html__( 'Columns', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> '2',
						'options' 	=> [
							'1' => esc_html__( '1', 'ova-brw' ),
							'2' => esc_html__( '2', 'ova-brw' ),
							'3' => esc_html__( '3', 'ova-brw' )
						],
						'condition' => [
							'layout' => 'layout1'
						]
					]
				);

				$this->add_control(
					'layout2_columns',
					[
						'label'   	=> esc_html__( 'Columns', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> '4',
						'options' 	=> [
							'1' => esc_html__( '1', 'ova-brw' ),
							'2' => esc_html__( '2', 'ova-brw' ),
							'3' => esc_html__( '3', 'ova-brw' ),
							'4' => esc_html__( '4', 'ova-brw' ),
							'5' => esc_html__( '5', 'ova-brw' )
						],
						'condition' => [
							'layout' => 'layout2'
						]
					]
				);

				$this->add_control(
					'map_type',
					[
						'label'   	=> esc_html__( 'Map Types', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'geocode',
						'options' 	=> [
							'geocode' 		=> esc_html__( 'Geocode', 'ova-brw' ),
							'address' 		=> esc_html__( 'Address', 'ova-brw' ),
							'establishment' => esc_html__( 'Establishment', 'ova-brw' )
						]
					]
				);

				$this->add_control(
					'bounds',
					[
						'label' 		=> esc_html__( 'Bounds', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Yes', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'No', 'ova-brw' ),
						'return_value' 	=> 'yes',
						'default' 		=> '',
					]
				);

				$this->add_control(
					'lat',
					[
						'label' 	=> esc_html__( 'Latitude', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'condition' => [
							'bounds' => 'yes'
						]
					]
				);

				$this->add_control(
					'lng',
					[
						'label' 	=> esc_html__( 'Longitude', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'condition' => [
							'bounds' => 'yes'
						]
					]
				);

				$this->add_control(
					'radius',
					[
						'label' 	=> esc_html__( 'Radius(meters)', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::TEXT,
						'condition' => [
							'bounds' => 'yes'
						]
					]
				);

				$this->add_control(
					'restrictions',
					[
						'label'   		=> esc_html__( 'Restrictions', 'ova-brw' ),
						'type'    		=> \Elementor\Controls_Manager::SELECT2,
						'multiple' 		=> true,
						'options' 		=> ovabrw_iso_alpha2(),
						'description' 	=> esc_html__( 'Maximum 5 countries', 'ova-brw' )
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_fields',
				[
					'label' => esc_html__( 'Fields', 'ova-brw' ),
				]
			);
				// Fields
				$repeater_fields = new \Elementor\Repeater();

				$repeater_fields->add_control(
					'field_name', [
						'label' 		=> esc_html__( 'Select Field', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT,
						'label_block' 	=> true,
						'options' 		=> [
							'pickup-location' 	=> esc_html__( 'Pick-up location' ),
							'dropoff-location' 	=> esc_html__( 'Drop-off location' ),
							'pickup-date' 		=> esc_html__( 'Pick-up date' ),
							'category' 			=> esc_html__( 'Category' ),
							'number-seats' 		=> esc_html__( 'Number of seats' ),
							'quantity' 			=> esc_html__( 'Quantity' )
						],
					]
				);

				$repeater_fields->add_control(
					'field_label',
					[
						'label' 		=> esc_html__( 'Label', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::TEXT,
						'label_block' 	=> true
					]
				);

				$repeater_fields->add_control(
					'field_placeholder',
					[
						'label' 		=> esc_html__( 'Placeholder', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::TEXT,
						'label_block' 	=> true
					]
				);

				$this->add_control(
					'fields',
					[
						'label' 		=> esc_html__( 'Fields', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::REPEATER,
						'fields' 		=> $repeater_fields->get_controls(),
						'default' => [
							[
								'field_name' 		=> 'pickup-location',
								'field_label' 		=> esc_html__( 'Pick Up Location', 'ova-brw' ),
								'field_placeholder' => esc_html__( 'Type Location', 'ova-brw' )
							],
							[
								'field_name' 		=> 'dropoff-location',
								'field_label' 		=> esc_html__( 'Drop Off Location', 'ova-brw' ),
								'field_placeholder' => esc_html__( 'Type Location', 'ova-brw' )
							],
							[
								'field_name' 		=> 'pickup-date',
								'field_label' 		=> esc_html__( 'Pick Up Date', 'ova-brw' ),
								'field_placeholder' => esc_html__( 'Enter Date', 'ova-brw' )
							],
							[
								'field_name' 		=> 'category',
								'field_label' 		=> esc_html__( 'Taxi - Type', 'ova-brw' ),
								'field_placeholder' => esc_html__( 'Select Type', 'ova-brw' )
							],
							[
								'field_name' 		=> 'number-seats',
								'field_label' 		=> esc_html__( 'Number Of Seats', 'ova-brw' ),
								'field_placeholder' => esc_html__( 'Enter Seat Number', 'ova-brw' )
							],
							[
								'field_name' 		=> 'quantity',
								'field_label' 		=> esc_html__( 'Quantity', 'ova-brw' ),
								'field_placeholder' => esc_html__( 'Enter Quantity', 'ova-brw' )
							]
						],
						'title_field' 	=> '{{{ field_label }}}',
					]
				);
			  	
			  	// Category slugs
			  	$category_slugs = [
			  		'' => esc_html__( 'None', 'ova-brw' )
			  	];

			  	// Category ids
			  	$category_ids = [];
			  	
			  	// Get product category
				$categories = get_categories([
			  		'taxonomy' 	=> 'product_cat',
					'orderby' 	=> 'name',
					'order' 	=> 'ASC'
			  	]);

			  	// Loop
			  	if ( ovabrw_array_exists( $categories ) ) {
				  	foreach ( $categories as $k => $category ) {
					  	$category_slugs[$category->slug] = $category->name;
					  	$category_ids[$category->term_id] = $category->name;
				  	}
			  	} // END if

			  	$this->add_control(
					'default_category',
					[
						'label'   		=> esc_html__( 'Default Category', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT,
						'label_block' 	=> true,
						'options' 		=> $category_slugs,
						'default' 		=> ''
					]
				);

			  	$this->add_control(
					'incl_category',
					[
						'label'   		=> esc_html__( 'Include Category', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT2,
						'label_block' 	=> true,
						'multiple' 		=> true,
						'options' 		=> $category_ids
					]
				);

				$this->add_control(
					'excl_category',
					[
						'label'   		=> esc_html__( 'Exclude Category', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT2,
						'label_block' 	=> true,
						'multiple' 		=> true,
						'options' 		=> $category_ids
					]
				);

				// Custom Taxonomies
				$custom_taxonomy[''] = esc_html__( 'None', 'ova-brw' );

				// Get custom taxonomies
				$taxonomies = ovabrw_get_option( 'custom_taxonomy', [] );
				if ( ovabrw_array_exists( $taxonomies ) ) {
					foreach ( $taxonomies as $key => $value ) {
						$custom_taxonomy[$key] = $value['name'];
					}
				}

				// Repeater taxonomies
				$repeater_taxonomies = new \Elementor\Repeater();

				$repeater_taxonomies->add_control(
					'custom_taxonomy', [
						'label' 		=> esc_html__( 'Select Taxonomy', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT,
						'label_block' 	=> true,
						'options' 		=> $custom_taxonomy,
					]
				);

				$this->add_control(
					'custom_taxonomies',
					[
						'label' 		=> esc_html__( 'Custom Taxonomies', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::REPEATER,
						'fields' 		=> $repeater_taxonomies->get_controls(),
						'title_field' 	=> '{{{ custom_taxonomy }}}',
					]
				);

			$this->end_controls_section();

			/* Section Search Result */
			$this->start_controls_section(
				'section_results',
				[
					'label' => esc_html__( 'Results', 'ova-brw' ),
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
					'result_url',
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
						'label'   	=> esc_html__( 'Order By', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'date',
						'options' 	=> $orderby,
						'condition' => [
							'search_result' => 'default',
						],
					]
				);

				$this->add_control(
					'order',
					[
						'label'   	=> esc_html__( 'Order', 'ova-brw' ),
						'type'    	=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'DESC',
						'options' 	=> [
							'ASC' 	=> esc_html__('Ascending', 'ova-brw'),
							'DESC' 	=> esc_html__('Descending', 'ova-brw'),
						],
						'condition' => [
							'search_result' => 'default',
						],
					]
				);

			$this->end_controls_section();

			// Content Style
			$this->start_controls_section(
				'content_style',
				[
					'label' => esc_html__( 'Content', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_control(
					'content_width',
					[
						'label' 		=> esc_html__( 'Width', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 2000,
								'step' 	=> 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi' => 'width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				// Layout 1
				$this->add_control(
					'column_grap_layout1',
					[
						'label' 		=> esc_html__( 'Column Gap', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1000,
								'step' 	=> 5,
							],
							'%' 	=> [
								'min' 	=> 0,
								'max' 	=> 100,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields' => 'column-gap: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'layout' => 'layout1'
						]
					]
				);

				$this->add_control(
					'row_grap_layout1',
					[
						'label' 		=> esc_html__( 'Row Gap', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 1000,
								'step' 	=> 5,
							],
							'%' => [
								'min' 	=> 0,
								'max' 	=> 100,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields' => 'row-gap: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'layout' => 'layout1'
						]
					]
				);
				// End
				
				// Layout 1
				$this->add_control(
					'column_grap_layout2',
					[
						'label' 		=> esc_html__( 'Column Gap', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 1000,
								'step' 	=> 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi.layout2 .search-taxi-form .search-taxi-fields' => 'column-gap: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'layout' => 'layout2'
						]
					]
				);

				$this->add_control(
					'row_grap_layout2',
					[
						'label' 		=> esc_html__( 'Row Gap', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 1000,
								'step' 	=> 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi.layout2 .search-taxi-form .search-taxi-fields' => 'row-gap: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'layout' => 'layout2'
						]
					]
				);
				// End
				
				$this->add_control(
					'content_background',
					[
						'label' 	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'content_border',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi',
					]
				);

				$this->add_responsive_control(
					'content_border_radius',
					[
						'label' 	=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				
				$this->add_responsive_control(
					'content_padding',
					[
						'label' 	=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'content_margin',
					[
						'label' 	=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();
			// End
			
			// Lable Style
			$this->start_controls_section(
				'label_style',
				[
					'label' => esc_html__( 'Label', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'label_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field label',
					]
				);

				$this->add_control(
					'label_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field label' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'label_margin',
					[
						'label' 	=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();
			// End
			
			// Field Style
			$this->start_controls_section(
				'field_style',
				[
					'label' => esc_html__( 'Field', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'field_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field input,
						{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field select',
					]
				);

				$this->add_control(
					'field_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field input' => 'color: {{VALUE}}',
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field select' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'field_border',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field input,
						{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field select',
					]
				);

				$this->add_responsive_control(
					'field_border_radius',
					[
						'label' 	=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'field_padding',
					[
						'label' 	=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'fields_height',
					[
						'label' 		=> esc_html__( 'Height', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 100,
								'step' 	=> 1,
							]
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field input' => 'height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field select' => 'height: {{SIZE}}{{UNIT}};',
						]
					]
				);

				$this->add_responsive_control(
					'space_bottom',
					[
						'label' 		=> esc_html__( 'Space Bottom', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 500,
								'step' 	=> 1,
							]
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields' => 'margin-bottom: {{SIZE}}{{UNIT}};',
						],
						'condition' => [
							'layout' => 'layout1'
						]
					]
				);

			$this->end_controls_section();
			// End
			
			// Icon Style
			$this->start_controls_section(
				'icon_style',
				[
					'label' => esc_html__( 'Icon', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'icon_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field > i',
					]
				);

				$this->add_control(
					'icon_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field > i' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'icon_top',
					[
						'label' 		=> esc_html__( 'Top', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 1000,
								'step' 	=> 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field > i' => 'top: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'icon_right',
					[
						'label' 		=> esc_html__( 'Right', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 1000,
								'step' 	=> 5,
							],
							'%' => [
								'min' => 0,
								'max' => 100,
							],
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-fields .search-field > i' => 'right: {{SIZE}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();
			// End
			
			// Button Style
			$this->start_controls_section(
				'button_style',
				[
					'label' => esc_html__( 'Button', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'button_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-btn',
					]
				);

				$this->start_controls_tabs(
					'button_style_tabs'
				);

					$this->start_controls_tab(
						'style_normal_tab',
						[
							'label' => esc_html__( 'Normal', 'ova-brw' ),
						]
					);

						$this->add_control(
							'button_color',
							[
								'label' 	=> esc_html__( 'Color', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-btn' => 'color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'button_background',
							[
								'label' 	=> esc_html__( 'Background', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-btn' => 'background-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'style_hover_tab',
						[
							'label' => esc_html__( 'Hover', 'ova-brw' ),
						]
					);

						$this->add_control(
							'button_hover_color',
							[
								'label' 	=> esc_html__( 'Color', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-btn:hover' => 'color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'button_hover_background',
							[
								'label' 	=> esc_html__( 'Background', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-btn:hover' => 'background-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'button_box_shadow',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-btn',
					]
				);

				$this->add_responsive_control(
					'button_padding',
					[
						'label' 	=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi .search-taxi-form .search-taxi-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search-taxi.layout2 .search-taxi-form .search-taxi-fields .search-field.search-field-btn .search-taxi-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section(); // END
		}

		/**
		 * Render HTML
		 */
		protected function render() {
			// Get settings
			$settings = $this->get_settings_for_display();

			// Template
			$template = apply_filters( OVABRW_PREFIX.'widget_template_search_taxi', 'elementor/ovabrw-search-taxi.php', $settings );
			if ( 'layout2' === ovabrw_get_meta_data( 'layout', $settings ) ) {
				$template = apply_filters( OVABRW_PREFIX.'widget_template_search_taxi', 'elementor/ovabrw-search-taxi2.php', $settings );
			}

			ob_start();
			ovabrw_get_template( $template, $settings );
			echo ob_get_clean();
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Search_Taxi() );
}