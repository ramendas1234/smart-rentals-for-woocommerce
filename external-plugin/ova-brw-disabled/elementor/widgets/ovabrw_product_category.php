<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Category
 */
if ( !class_exists( 'OVABRW_Widget_Product_Category' ) ) {

	class OVABRW_Widget_Product_Category extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_category';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Category Thumbnail', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-product-categories';
		}

		/**
		 * Get widget categories
		 */
		public function get_categories() {
			return [ 'ovabrw-products' ];
		}

		/**
		 * Register controls
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'content_section',
				[
					'label' => esc_html__( 'Content', 'ova-brw' ),
					'tab' 	=> \Elementor\Controls_Manager::TAB_CONTENT,
				]
			);

				$this->add_control(
					'template',
					[
						'label' 	=> esc_html__( 'Select Template', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'template1',
						'options' 	=> [
							'template1' => esc_html__( 'Template 1', 'ova-brw' ),
							'template2' => esc_html__( 'Template 2', 'ova-brw' ),
						],
					]
				);

				// Product categories
				$product_categories = [];

				// Default category
				$default_category = '';

				// Get categories
				$categories = get_categories([
					'taxonomy' 	=> 'product_cat',
					'orderby' 	=> 'name',
					'order' 	=> 'ASC'
				]);
				
				// Loop
			  	if ( ovabrw_array_exists( $categories ) ) {
				  	foreach ( $categories as $category ) {
					  	$product_categories[$category->term_id] = $category->name;

					  	// Default category
					  	if ( '' === $default_category ) {
					  		$default_category = $category->term_id;
					  	}
				  	}
			  	} // END

			  	$this->add_control(
					'category',
					[
						'label' 	=> esc_html__( 'Select Category', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> $default_category,
						'options' 	=> $product_categories,
					]
				);

				$this->add_control(
					'target_link',
					[
						'label' 		=> esc_html__( 'Open in new window', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'return_value' 	=> 'yes',
						'default' 		=> 'no',
					]
				);

				$this->add_control(
					'show_name',
					[
						'label' 		=> esc_html__( 'Show Category Name', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'return_value' 	=> 'yes',
						'default' 		=> 'yes',
					]
				);

				$this->add_control(
					'show_count',
					[
						'label' 		=> esc_html__( 'Show Category Count', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'return_value' 	=> 'yes',
						'default' 		=> 'yes',
					]
				);

				$this->add_control(
					'text_count',
					[
						'label' 		=> esc_html__( 'Text Count', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::TEXT,
						'default' 		=>  esc_html__( 'Car', 'ova-brw' ),
						'condition'     => [
							'show_count' 	=> 'yes'
						]
					]
				);

				$this->add_control(
					'text_count_many',
					[
						'label' 		=> esc_html__( 'Text Count *Many', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::TEXT,
						'default' 		=>  esc_html__( 'Cars', 'ova-brw' ),
						'condition'     => [
							'show_count'	=> 'yes'
						]
					]
				);

				$this->add_control(
					'show_review',
					[
						'label' 		=> esc_html__( 'Show Category Review', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'return_value' 	=> 'yes',
						'default' 		=> 'yes',
					]
				);

				$this->add_control(
					'custom_image',
					[
						'label' 		=> esc_html__( 'Custom Category Image', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Yes', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'No', 'ova-brw' ),
						'return_value' 	=> 'yes',
						'default' 		=> '',
					]
				);

				$this->add_control(
					'image',
					[
						'label' 	=> esc_html__( 'Choose Image', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::MEDIA,
						'default' 	=> [
							'url' 	=> \Elementor\Utils::get_placeholder_image_src(),
						],
						'condition' => [ 'custom_image' => 'yes' ],
					]
				);

				$this->add_control(
					'show_background_overlay',
					[
						'label' 		=> esc_html__( 'Show Background Overlay', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SWITCHER,
						'label_on' 		=> esc_html__( 'Show', 'ova-brw' ),
						'label_off' 	=> esc_html__( 'Hide', 'ova-brw' ),
						'return_value' 	=> 'yes',
						'default' 		=> 'yes',
					]
				);

			$this->end_controls_section();

			// Template 1
			$this->start_controls_section(
				'background_overlay_template1',
				[
					'label' 	=> esc_html__( 'Content', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [ 'template' => 'template1' ],
				]
			);
				$this->add_control(
					'background_overlay',
					[
						'label' 	=> esc_html__( 'Background Overlay', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .background-overlay' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'content_height_template1',
					[
						'label' 	 	=> esc_html__( 'Height', 'ova-brw' ),
						'type' 		 	=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' 	=> [
								'min' 	=> 180,
								'max' 	=> 500,
								'step' 	=> 5,
							],
							'%' 	=> [
								'min' 	=> 20,
								'max' 	=> 100,
							],
						],
						'selectors'  	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template1' => 'height: {{SIZE}}{{UNIT}}',
						],
					]
				);

				$this->add_responsive_control(
					'content_padding_template1',
					[
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'content_border_radius_template1',
					[
						'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template1, .ovabrw-el-product-category.template1 .content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'name_template1',
				[
					'label' 	=> esc_html__( 'Name', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [ 'template' => 'template1' ],
				]
			);
				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'name_template1_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-el-product-category.template1 .info .name',
					]
				);

				$this->add_control(
					'name_color_template1',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .name' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'name_margin_template1',
					[
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
			$this->end_controls_section();

			$this->start_controls_section(
				'count_template1',
				[
					'label' 	=> esc_html__( 'Count', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [ 'template' => 'template1' ],
				]
			);
				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'count_template1_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .count',
					]
				);

				$this->add_control(
					'count_color_template1',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .count' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'count_background_template1',
					[
						'label' 	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .count' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'count_border_radius_template1',
					[
						'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .count' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'count_margin_template1',
					[
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .count' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'count_padding_template1',
					[
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
			$this->end_controls_section();

			$this->start_controls_section(
				'review_template1',
				[
					'label' 	=> esc_html__( 'Review', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [ 'template' => 'template1' ],
				]
			);
				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'review_template1_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .review-average .average',
					]
				);

				$this->add_control(
					'star_color_template1',
					[
						'label' 	=> esc_html__( 'Star Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .review-average i' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'review_color_template1',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .review-average .average' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'review_margin_template1',
					[
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template1 .info .extra .review-average' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
			$this->end_controls_section();
			// End
			
			// Template 2
			$this->start_controls_section(
				'background_overlay_template2',
				[
					'label' 	=> esc_html__( 'Content', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [ 'template' => 'template2' ],
				]
			);
				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					[
						'name' 		=> 'template2_box_shadow',
						'selector' 	=> '{{WRAPPER}} .ovabrw-el-product-category.template2 .content',
					]
				);

				$this->add_control(
					'background_overlay_2',
					[
						'label' 	=> esc_html__( 'Background Overlay', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template2 .content .background-overlay' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'content_margin_template2',
					[
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template2 .content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'content_padding_template2',
					[
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
					'content_border_radius_template2',
					[
						'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template2, .ovabrw-el-product-category.template2 .content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'name_template2',
				[
					'label' 	=> esc_html__( 'Name', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [ 'template' => 'template2' ],
				]
			);
				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'name_template2_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-el-product-category.template2 .info .name',
					]
				);

				$this->add_control(
					'name_color_template2',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template2 .info .name' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'name_margin_template2',
					[
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template2 .info .name' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'review_template2',
				[
					'label' 	=> esc_html__( 'Review', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [ 'template' => 'template2' ],
				]
			);
				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'review_template2_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-el-product-category.template2 .info .review-average .average',
					]
				);

				$this->add_control(
					'star_color_template2',
					[
						'label' 	=> esc_html__( 'Star Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template2 .info .review-average i' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'review_color_template2',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-el-product-category.template2 .info .review-average .average' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'review_margin_template2',
					[
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-el-product-category.template2 .info .review-average' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section(); // End
		}

		/**
		 * Render HTML
		 */
		protected function render() {
			// Get settings
			$settings = $this->get_settings_for_display();

			// Arguments
			$args = [
				'term_id' 			=> absint( ovabrw_get_meta_data( 'category', $settings ) ),
				'target_link' 		=> ovabrw_get_meta_data( 'target_link', $settings ),
				'show_name' 		=> ovabrw_get_meta_data( 'show_name', $settings ),
				'show_count' 		=> ovabrw_get_meta_data( 'show_count', $settings ),
				'text_count' 		=> ovabrw_get_meta_data( 'text_count', $settings ),
				'text_count_many' 	=> ovabrw_get_meta_data( 'text_count_many', $settings ),
				'show_review' 		=> ovabrw_get_meta_data( 'show_review', $settings ),
				'custom_image' 		=> ovabrw_get_meta_data( 'custom_image', $settings ),
				'image' 			=> [
					'default' 	=> \Elementor\Utils::get_placeholder_image_src(),
					'url' 		=> isset( $settings['image']['url'] ) ? $settings['image']['url'] : \Elementor\Utils::get_placeholder_image_src(),
					'alt' 		=> isset( $settings['image']['alt'] ) ? $settings['image']['alt'] : ''
				],
				'background_overlay' => ovabrw_get_meta_data( 'show_background_overlay', $settings )
			];
			
			// Get template
			if ( 'template1' === ovabrw_get_meta_data( 'template', $settings ) ) {
				ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_category', 'elementor/ovabrw-product-category.php', $settings ), $args );
			} else {
				ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_category', 'elementor/ovabrw-product-category2.php', $settings ), $args );
			}
		}
	}

	// Regiser new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Category() );
}