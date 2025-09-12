<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Search_Taxi_Ajax
 */
if ( !class_exists( 'OVABRW_Widget_Search_Taxi_Ajax' ) ) {

	class OVABRW_Widget_Search_Taxi_Ajax extends \Elementor\Widget_Base {
		
		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_search_taxi_ajax';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Search Taxi Ajax', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-search-results';
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
			// Get Google API Key Map
			$api_key = ovabrw_get_setting( 'google_key_map', false );
			if ( $api_key ) {
				wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&libraries=places&loading=async&callback=Function.prototype', false, true );
			}

			// Carosel
			wp_enqueue_style( 'carousel', OVABRW_PLUGIN_URI.'assets/libs/carousel/owl.carousel.min.css' );
			wp_enqueue_script( 'carousel', OVABRW_PLUGIN_URI.'assets/libs/carousel/owl.carousel.min.js', [ 'jquery' ], false, true );
			
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
					'columns',
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
						'label' 	=> esc_html__( 'Fields', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::REPEATER,
						'fields' 	=> $repeater_fields->get_controls(),
						'default' 	=> [
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

				// Get product categories
				$categories = get_categories([
			  		'taxonomy' 	=> 'product_cat',
					'orderby' 	=> 'name',
					'order' 	=> 'ASC'
			  	]);
			  	
			  	// Loop
			  	if ( ovabrw_array_exists( $categories ) ) {
				  	foreach ( $categories as $category ) {
					  	$category_slugs[$category->slug] = $category->name;
					  	$category_ids[$category->term_id] = $category->name;
				  	}
			  	} // END loop

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

				// Default card
				$default_card = [
					'' => esc_html__( 'Default', 'ova-brw' )
				];

				// Get card templates
				$card_templates = ovabrw_get_card_templates();
				if ( !ovabrw_array_exists( $card_templates ) ) $card_templates = [];

				$this->add_control(
					'card_template',
					[
						'label' 	=> esc_html__( 'Card template', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'card1',
						'options' 	=> array_merge( $default_card, $card_templates ),
					]
				);

				$this->add_control(
					'posts_per_page',
					[
						'label'   => esc_html__( 'Posts per page', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::NUMBER,
						'min'     => -1,
						'default' => 6,
					]
				);

				$this->add_control(
					'column',
					[
						'label'   => esc_html__( 'Column', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SELECT,
						'default' => 'three-column',
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
						'options' => $orderby
					]
				);

				$this->add_control(
					'order',
					[
						'label'   => esc_html__( 'Order', 'ova-brw' ),
						'type'    => \Elementor\Controls_Manager::SELECT,
						'default' => 'DESC',
						'options' => [
							'ASC' 	=> esc_html__( 'Ascending', 'ova-brw' ),
							'DESC' 	=> esc_html__( 'Descending', 'ova-brw' ),
						],
					]
				);

				$this->add_control(
					'term',
					[
						'label'   		=> esc_html__( 'Term', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SELECT,
						'label_block' 	=> true,
						'options' 		=> $category_slugs,
						'default' 		=> ''
					]
				);

				$this->add_control(
					'pagination',
					[
						'label' 		=> esc_html__( 'Show Pagination', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'return_value' 	=> 'yes',
						'default' 		=> 'yes',
					]
				);

			$this->end_controls_section();

			// Form Style
			$this->start_controls_section(
				'form_style',
				[
					'label' => esc_html__( 'Form', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_STYLE,
				]
			);

				$this->add_control(
					'content_background',
					[
						'label' 	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'content_border',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form',
					]
				);

				$this->add_control(
					'content_border_radius',
					[
						'label' 	=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				
				$this->add_responsive_control(
					'content_padding',
					[
						'label' 	=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'content_margin',
					[
						'label' 	=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field label',
					]
				);

				$this->add_control(
					'label_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field label' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'label_margin',
					[
						'label' 	=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field input,
						{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field select',
					]
				);

				$this->add_control(
					'field_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field input' => 'color: {{VALUE}}',
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field select' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'field_border',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field input,
						{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field select',
					]
				);

				$this->add_control(
					'field_border_radius',
					[
						'label' 	=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'field_padding',
					[
						'label' 	=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field input' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'fields_height',
					[
						'label' 		=> esc_html__( 'Height', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 100,
								'step' 	=> 1,
							]
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field input' => 'height: {{SIZE}}{{UNIT}};',
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field select' => 'height: {{SIZE}}{{UNIT}};',
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
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field > i',
					]
				);

				$this->add_control(
					'icon_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field > i' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
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
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field > i' => 'top: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
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
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field > i' => 'right: {{SIZE}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section(); // End
			
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
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field.search-field-btn .search-taxi-btn',
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
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field.search-field-btn .search-taxi-btn' => 'color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'button_background',
							[
								'label' 	=> esc_html__( 'Background', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field.search-field-btn .search-taxi-btn' => 'background-color: {{VALUE}}',
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
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field.search-field-btn .search-taxi-btn:hover' => 'color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'button_hover_background',
							[
								'label' 	=> esc_html__( 'Background', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field.search-field-btn .search-taxi-btn:hover' => 'background-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'button_box_shadow',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field.search-field-btn .search-taxi-btn',
					]
				);

				$this->add_responsive_control(
					'button_padding',
					[
						'label' 	=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::DIMENSIONS,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .search-taxi-form .search-taxi-fields .search-field.search-field-btn .search-taxi-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();
			// End
			
			// Lable Style
			$this->start_controls_section(
				'pagination_style',
				[
					'label' 	=> esc_html__( 'Pagination', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'pagination!' => ''
					]
				]
			);

				$this->add_control(
					'pagination_align',
					[
						'label' 	=> esc_html__( 'Alignment', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::CHOOSE,
						'options' 	=> [
							'flex-start' 	=> [
								'title' 	=> esc_html__( 'Left', 'ova-brw' ),
								'icon' 		=> 'eicon-text-align-left',
							],
							'center' 		=> [
								'title' 	=> esc_html__( 'Center', 'ova-brw' ),
								'icon' 		=> 'eicon-text-align-center',
							],
							'flex-end' 		=> [
								'title' 	=> esc_html__( 'Right', 'ova-brw' ),
								'icon' 		=> 'eicon-text-align-right',
							],
						],
						'toggle' 	=> true,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination' => 'justify-content: {{VALUE}};',
						],
					]
				);

				$this->add_control(
					'pagination_padding',
					[
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						]
					]
				);

				$this->add_control(
					'pagination_margin',
					[
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						]
					]
				);

				$this->add_control(
					'pagination_item_options',
					[
						'label' 	=> esc_html__( 'Item Options', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'pagination_item_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers',
					]
				);

				$this->add_control(
					'pagination_item_size',
					[
						'label' 		=> esc_html__( 'Size', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' => [
								'min' 	=> 0,
								'max' 	=> 100,
								'step' 	=> 1,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->start_controls_tabs(
					'pagination_item_tabs'
				);

					$this->start_controls_tab(
						'pagination_item_normal_tab',
						[
							'label' => esc_html__( 'Normal', 'ova-brw' ),
						]
					);

						$this->add_control(
							'pagination_item_color',
							[
								'label' 	=> esc_html__( 'Color', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers' => 'color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'pagination_item_background',
							[
								'label' 	=> esc_html__( 'Background', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers' => 'background-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'pagination_item_current_tab',
						[
							'label' => esc_html__( 'Current', 'ova-brw' ),
						]
					);

						$this->add_control(
							'pagination_item_current_color',
							[
								'label' 	=> esc_html__( 'Color', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers.current' => 'color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'pagination_item_current_background',
							[
								'label' 	=> esc_html__( 'Background', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers.current' => 'background-color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'pagination_item_current_border_color',
							[
								'label' 	=> esc_html__( 'Border Color', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers.current' => 'border-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

					$this->start_controls_tab(
						'pagination_item_hover_tab',
						[
							'label' => esc_html__( 'Hover', 'ova-brw' ),
						]
					);

						$this->add_control(
							'pagination_item_hover_color',
							[
								'label' 	=> esc_html__( 'Color', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers:hover' => 'color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'pagination_item_hover_background',
							[
								'label' 	=> esc_html__( 'Background', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers:hover' => 'background-color: {{VALUE}}',
								],
							]
						);

						$this->add_control(
							'pagination_item_hover_border_color',
							[
								'label' 	=> esc_html__( 'Border Color', 'ova-brw' ),
								'type' 		=> \Elementor\Controls_Manager::COLOR,
								'selectors' => [
									'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers:hover' => 'border-color: {{VALUE}}',
								],
							]
						);

					$this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'pagination_item_border',
						'selector' 	=> '{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers',
					]
				);

				$this->add_control(
					'pagination_item_border_radius',
					[
						'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						]
					]
				);

				$this->add_control(
					'pagination_item_margin',
					[
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-search-taxi-ajax .ovabrw-pagination li .page-numbers' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						]
					]
				);

			$this->end_controls_section(); // End
		}

		// Render HTML
		protected function render() {
			// Get settings
			$settings = $this->get_settings_for_display();

			ob_start();
			ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_search_taxi_ajax', 'elementor/ovabrw-search-taxi-ajax.php', $settings ), $settings );
			echo ob_get_clean();
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Search_Taxi_Ajax() );
}