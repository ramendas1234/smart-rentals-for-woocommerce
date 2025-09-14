<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Features
 */
if ( !class_exists( 'OVABRW_Widget_Product_Features' ) ) {

	class OVABRW_Widget_Product_Features extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_features';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Features', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-product-meta';
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
					'label' => esc_html__( 'Demo', 'ova-brw' ),
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
				'section_features_style',
				[
					'label' 	=> esc_html__( 'Features', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'features_display',
					[
						'label' 	=> esc_html__( 'Display', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'block',
						'options' 	=> [
							'block' 		=> esc_html__( 'Block', 'ova-brw' ),
							'inline-block' 	=> esc_html__( 'Auto', 'ova-brw' ),
						],
						'selectors' => [
							'{{WRAPPER}} ul.ovabrw_woo_features' => 'display: {{UNIT}};',
						],
					]
				);

				$this->add_control(
					'features_background',
					[
						'label' 	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.ovabrw_woo_features' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'features_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'features_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_features_items_style',
				[
					'label' 	=> esc_html__( 'Items', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'features_items_background',
					[
						'label' 	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.ovabrw_woo_features li' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'features_items_border',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li',
					]
				);

				$this->add_control(
					'features_items_border_first_options',
					[
						'label' 	=> esc_html__( 'Border Items (first)', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'features_items_border_first',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li:first-child',
					]
				);

				$this->add_control(
					'features_items_border_last_options',
					[
						'label' 	=> esc_html__( 'Border Items (last)', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'features_items_border_last',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li:last-child',
					]
				);

				$this->add_responsive_control(
					'features_items_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'features_items_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_features_icons_style',
				[
					'label' 	=> esc_html__( 'Icons', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'icon_typography',
						'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li i:before',
					]
				);

				$this->add_control(
					'icon_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.ovabrw_woo_features li i:before' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'icon_border',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li i:before',
					]
				);

				$this->add_responsive_control(
					'icon_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features li i:before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'icon_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features li i:before' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_features_label_style',
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
						'name' 		=> 'label_typography',
						'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li label',
					]
				);

				$this->add_control(
					'label_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.ovabrw_woo_features li label' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'label_min_width',
					[
						'label' 		=> esc_html__( 'Min Width', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1000,
								'step' 	=> 5,
							],
							'%' 	=> [
								'min' => 0,
								'max' => 100,
							],
						],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features li label' => 'min-width: {{SIZE}}{{UNIT}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'label_border',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li label',
					]
				);

				$this->add_responsive_control(
					'label_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features li label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'label_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features li label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_features_value_style',
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
						'name' 		=> 'value_typography',
						'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li span',
					]
				);

				$this->add_control(
					'value_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} ul.ovabrw_woo_features li span' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'value_border',
						'label' 	=> esc_html__( 'Border', 'ova-brw' ),
						'selector' 	=> '{{WRAPPER}} ul.ovabrw_woo_features li span',
					]
				);

				$this->add_responsive_control(
					'value_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features li span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'value_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} ul.ovabrw_woo_features li span' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_features', 'modern/single/detail/ovabrw-product-features.php', $settings ), $settings ); ?>
				</div>
			<?php else: ?>
				<div class="elementor-features">
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_features', 'single/features.php', $settings ), [
						'product_id' => $product->get_id()
					]); ?>
				</div>
			<?php endif;
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Features() );
}