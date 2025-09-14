<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Table_Price
 */
if ( !class_exists( 'OVABRW_Widget_Product_Table_Price' ) ) {

	class OVABRW_Widget_Product_Table_Price extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_table_price';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Price table', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-price-table';
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
				'section_price_table_style',
				[
					'label' 	=> esc_html__( 'Title', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'wc_style_warning',
					[
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					]
				);

				$this->add_control(
					'title_color',
					[
						'label'  	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 	 	=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'title_typography',
						'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according',
					]
				);

				$this->add_responsive_control(
					'title_padding',
					[
						'label' 	 => esc_html__( 'Padding', 'ova-framework' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'title_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-framework' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw-according' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_content_style',
				[
					'label' 	=> esc_html__( 'Content', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'wc_style_warning_content',
					[
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					]
				);

				$this->add_control(
					'content_title_color',
					[
						'label'  	=> esc_html__( 'Color Title', 'ova-brw' ),
						'type' 	 	=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'content_title_typography',
						'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label',
					]
				);

				$this->add_responsive_control(
					'content_title_padding',
					[
						'label' 	 => esc_html__( 'Padding', 'ova-framework' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'content_title_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-framework' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_table_value_style',
				[
					'label' 	=> esc_html__( 'Table', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'wc_style_warning_table',
					[
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					]
				);

				$this->add_control(
					'content_title_table_bg',
					[
						'label'  	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 	 	=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'content_title_table_color',
					[
						'label'  	=> esc_html__( 'Color Title', 'ova-brw' ),
						'type' 	 	=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'content_title_table_typography',
						'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th',
					]
				);

				$this->add_responsive_control(
					'content_title_table_padding',
					[
						'label' 	 => esc_html__( 'Padding', 'ova-framework' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'content_title_table_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-framework' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table thead tr th' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_table_style',
				[
					'label' 	=> esc_html__( 'Table Value', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'wc_style_warning_table_value',
					[
						'type' => \Elementor\Controls_Manager::RAW_HTML,
						'raw'  => esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					]
				);

				$this->add_control(
					'table_value_color',
					[
						'label'  	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 	 	=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'table_value_typography',
						'selector' 	=> '{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td',
					]
				);

				$this->add_responsive_control(
					'table_value_padding',
					[
						'label' 	 => esc_html__( 'Padding', 'ova-framework' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'table_value_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-framework' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'{{WRAPPER}} .product_table_price .ovacrs_price_rent .ovabrw_collapse_content .price_table table tbody tr td' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_table_price', 'modern/single/detail/ovabrw-product-table-price.php', $settings ), $settings ); ?>
				</div>
			<?php else: ?>
				<div class="elementor-table-price">
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_table_price', 'single/table_price.php', $settings ), [
						'product_id' => $product->get_id()
					]); ?>
				</div>
			<?php endif;
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Table_Price() );
}