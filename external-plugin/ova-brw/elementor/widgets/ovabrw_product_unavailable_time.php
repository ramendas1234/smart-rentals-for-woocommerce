<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Unavailable_Time
 */
if ( !class_exists( 'OVABRW_Widget_Product_Unavailable_Time' ) ) {

	class OVABRW_Widget_Product_Unavailable_Time extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_unavailable_time';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Disabled Dates', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-table-of-contents';
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
				'section_unavailable_time_style',
				[
					'label' 	=> esc_html__( 'Unavailable Time', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'wc_style_warning',
					[
						'type' 	=> \Elementor\Controls_Manager::RAW_HTML,
						'raw' 	=> esc_html__( 'The style of this widget is often affected by your theme and plugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					]
				);

				$this->add_responsive_control(
					'text_align',
					[
						'label' 	=> esc_html__( 'Alignment', 'ova-brw' ),
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
						'selectors' => [
							'{{WRAPPER}}' => 'text-align: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'title_options',
					[
						'label' 	=> esc_html__( 'Title Options', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

					$this->add_control(
						'title_color',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .elementor-unavailable-time .ovacrs_single_untime h3' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' 	 	=> 'title_typography',
							'selector' 	=> '{{WRAPPER}} .elementor-unavailable-time .ovacrs_single_untime h3',
						]
					);

					$this->add_responsive_control(
						'title_margin',
						[
							'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
							'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
							'selectors'  => [
								'{{WRAPPER}} .elementor-unavailable-time .ovacrs_single_untime h3' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
							],
						]
					);

				$this->add_control(
					'time_options',
					[
						'label' 	=> esc_html__( 'Time Options', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

					$this->add_control(
						'time_color',
						[
							'label' 	=> esc_html__( 'Color', 'ova-brw' ),
							'type' 		=> \Elementor\Controls_Manager::COLOR,
							'selectors' => [
								'{{WRAPPER}} .elementor-unavailable-time .ovacrs_single_untime ul li' => 'color: {{VALUE}}',
							],
						]
					);

					$this->add_group_control(
						\Elementor\Group_Control_Typography::get_type(),
						[
							'name' 	 	=> 'time_typography',
							'selector' 	=> '{{WRAPPER}} .elementor-unavailable-time .ovacrs_single_untime ul li',
						]
					);

					$this->add_responsive_control(
						'time_margin',
						[
							'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
							'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
							'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
							'selectors'  => [
								'{{WRAPPER}} .elementor-unavailable-time .ovacrs_single_untime ul li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_unavailable_time', 'modern/single/detail/ovabrw-product-unavailable.php', $settings ), $settings ); ?>
				</div>
			<?php else: ?>
				<div class="elementor-unavailable-time">
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_unavailable_time', 'single/unavailable_time.php', $settings ), [
						'product_id' => $product->get_id()
					]); ?>
				</div>
			<?php endif;
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Unavailable_Time() );
}