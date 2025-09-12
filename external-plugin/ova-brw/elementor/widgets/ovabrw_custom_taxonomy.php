<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Custom_Taxonomy
 */
if ( !class_exists( 'OVABRW_Widget_Custom_Taxonomy' ) ) {

	class OVABRW_Widget_Custom_Taxonomy extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_custom_taxonomy';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Custom Taxonomy', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-editor-list-ul';
		}

		/**
		 * Get widget categories
		 */
		public function get_categories() {
			return [ 'ovabrw-product' ];
		}

		/**
		 * Register controls
		 */
		protected function register_controls() {

			$this->start_controls_section(
				'section_demo',
				[
					'label' => esc_html__( 'Demo', 'ova-brw' )
				]
			);

				// Product demo
				$product_demo = [
					'0' => esc_html__( 'Choose Product', 'ova-brw' )
				];

				// Default product
				$default_product = '';

				// Get rental products
				$rental_products = OVABRW()->options->get_rental_product_ids();
				if ( ovabrw_array_exists( $rental_products ) ) {
					foreach ( $rental_products as $product_id ) {
						$product_demo[$product_id] = get_the_title( $product_id );

						// Default product
						if ( '' === $default_product ) $default_product = $product_id;
					}
				}

				$this->add_control(
					'product_id',
					[
						'label' 	=> esc_html__( 'Choose Product', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> $default_product,
						'options' 	=> $product_demo
					]
				);

				// Product templates
				$default_template = 'classic';
				$product_template = [
					'classic' => esc_html__( 'Classic', 'ova-brw' )
				];

				if ( ovabrw_global_typography() ) {
					$product_template['modern'] = esc_html__( 'Modern', 'ova-brw' );
					$default_template 			= 'modern';
				}

				$this->add_control(
					'product_template',
					[
						'label' 	=> esc_html__( 'Style', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> $default_template,
						'options' 	=> $product_template
					]
				);

			$this->end_controls_section();
			
			$this->start_controls_section(
				'section_custom_taxonomy_style',
				[
					'label' 	=> esc_html__( 'Taxonomy', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'taxonomy_display',
					[
						'label' 	=> esc_html__( 'Display', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'block',
						'options' 	=> [
							'block' 		=> esc_html__( 'Block', 'ova-brw' ),
							'inline-block' 	=> esc_html__( 'Auto', 'ova-brw' ),
						],
						'selectors' => [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy' => 'display: {{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'taxonomy_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'taxonomy_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_items_taxonomy_style',
				[
					'label' 	=> esc_html__( 'Items', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'items_taxonomy_background',
					[
						'label' 	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'items_border',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li',
					]
				);

				$this->add_control(
					'items_border_first_options',
					[
						'label' 	=> esc_html__( 'Border Items (first)', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'items_border_first',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li:first-child',
					]
				);

				$this->add_control(
					'items_border_last_options',
					[
						'label' 	=> esc_html__( 'Border Items (last)', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'items_border_last',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li:last-child',
					]
				);

				$this->add_responsive_control(
					'items_taxonomy_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'items_taxonomy_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_label_taxonomy_style',
				[
					'label' 	=> esc_html__( 'Label', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'label_taxonomy_typography',
						'selector' 	=> '{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li label',
					]
				);

				$this->add_control(
					'label_taxonomy_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li label' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'label_taxonomy_min_width',
					[
						'label' 		=> esc_html__( 'Min Width', 'ova-brw' ),
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
						'selectors' 	=> [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li label' => 'min-width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'label_taxonomy_border',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li label',
					]
				);

				$this->add_responsive_control(
					'label_taxonomy_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'label_taxonomy_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_value_taxonomy_style',
				[
					'label' 	=> esc_html__( 'Value', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'value_taxonomy_typography',
						'selector' 	=> '{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li span',
					]
				);

				$this->add_control(
					'value_taxonomy_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li span' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'value_taxonomy_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'value_taxonomy_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .elementor-custom-taxonomy ul.ovabrw_cus_taxonomy li span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
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

			// Get product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $settings );

			// Global product
			global $product;
			if ( !$product ) {
				$product = wc_get_product( $product_id );
			}

			// Check rental product
	    	if ( !$product || !$product->is_type( OVABRW_RENTAL ) ): ?>
				<div class="ovabrw_elementor_no_product">
					<span><?php echo wp_kses_post( $this->get_title() ); ?></span>
				</div>
				<?php return;
			endif;

			// Get template
			if ( 'modern' === ovabrw_get_meta_data( 'product_template', $settings ) ): ?>
				<div class="ovabrw-modern-product">
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_custom_taxonomy', 'modern/single/detail/ovabrw-product-custom-taxonomy.php', $settings ), $settings ); ?>
				</div>
			<?php else: ?>
				<div class="elementor-custom-taxonomy">
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_custom_taxonomy', 'single/custom_taxonomy.php', $settings ), [
						'product_id' => $product->get_id()
					]); ?>
				</div>
			<?php endif;
		}
	}

	// Regiser new widget
	$widgets_manager->register( new OVABRW_Widget_Custom_Taxonomy() );
}