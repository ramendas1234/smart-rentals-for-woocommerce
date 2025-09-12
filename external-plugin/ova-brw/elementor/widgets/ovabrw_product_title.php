<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Title
 */
if ( !class_exists( 'OVABRW_Widget_Product_Title' ) ) {

	class OVABRW_Widget_Product_Title extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_title';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Title', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-t-letter';
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
				'section_title',
				[
					'label' 	=> esc_html__( 'Title', 'ova-brw' ),
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);
			
				$this->add_control(
					'link',
					[
						'label' 	=> esc_html__( 'Link', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::URL,
						'dynamic' 	=> [
							'active' => true,
						],
						'default' => [
							'url' => '',
						],
						'separator' => 'before',
					]
				);

				$this->add_control(
					'header_size',
					[
						'label' 	=> esc_html__( 'HTML Tag', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'options' 	=> [
							'h1' 	=> 'H1',
							'h2' 	=> 'H2',
							'h3' 	=> 'H3',
							'h4' 	=> 'H4',
							'h5' 	=> 'H5',
							'h6' 	=> 'H6',
							'div' 	=> 'div',
							'span' 	=> 'span',
							'p' 	=> 'p',
						],
						'default' 	=> 'h2',
					]
				);

				$this->add_responsive_control(
					'align',
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
							'justify' => [
								'title' => esc_html__( 'Justified', 'ova-brw' ),
								'icon' 	=> 'eicon-text-align-justify',
							],
						],
						'default' 	=> '',
						'selectors' => [
							'{{WRAPPER}} .ovabrw_product_title' => 'text-align: {{VALUE}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_title_style',
				[
					'label' 	=> esc_html__( 'Title', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
					'title_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw_product_title .ovabrw_title' => 'color: {{VALUE}};',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 	 	=> 'typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw_product_title .ovabrw_title',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Text_Shadow::get_type(),
					[
						'name' 		=> 'text_shadow',
						'selector' 	=> '{{WRAPPER}} .ovabrw_product_title .ovabrw_title',
					]
				);

				$this->add_control(
					'blend_mode',
					[
						'label' 	=> esc_html__( 'Blend Mode', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'options' 	=> [
							'' 				=> esc_html__( 'Normal', 'ova-brw' ),
							'multiply' 	  	=> 'Multiply',
							'screen' 	  	=> 'Screen',
							'overlay' 	  	=> 'Overlay',
							'darken' 	  	=> 'Darken',
							'lighten' 	  	=> 'Lighten',
							'color-dodge' 	=> 'Color Dodge',
							'saturation'  	=> 'Saturation',
							'color' 	  	=> 'Color',
							'difference'  	=> 'Difference',
							'exclusion'   	=> 'Exclusion',
							'hue' 		  	=> 'Hue',
							'luminosity'  	=> 'Luminosity'
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw_product_title .ovabrw_title' => 'mix-blend-mode: {{VALUE}}',
						],
						'separator' => 'none',
					]
				);

				$this->add_responsive_control(
					'title_margin',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Margin', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw_product_title .ovabrw_title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-title.php' ); ?>
				</div>
			<?php else:
				// Get product title
				$title = $product->get_title();

				if ( '' === $title ): ?>
					<div class="ovabrw_elementor_no_product">
						<span><?php echo wp_kses_post( $this->get_title() ); ?></span>
					</div>
					<?php return;
				endif;

				// Get header_size
				$header_size = ovabrw_get_meta_data( 'header_size', $settings );

				// Get link
				$link 	= isset( $settings['link']['url'] ) ? $settings['link']['url'] : '';
				$blank 	= '_self';
				$target = isset( $settings['link']['is_external'] ) ? $settings['link']['is_external'] : '';
				if ( $target ) $blank = '_blank';
			?>
				<div class="ovabrw_product_title">
					<?php if ( $link ): ?>
						<a href="<?php echo esc_url( $link ); ?>" target="<?php echo esc_attr( $blank ); ?>">
							<<?php echo esc_attr( $header_size ); ?> class="ovabrw_title">
								<?php echo wp_kses_post( $title ); ?>
							</<?php echo esc_attr( $header_size ); ?>>
						</a>
					<?php else: ?>
						<<?php echo esc_attr( $header_size ); ?> class="ovabrw_title">
							<?php echo wp_kses_post( $title ); ?>
						</<?php echo esc_attr( $header_size ); ?>>
					<?php endif; ?>
				</div>
			<?php endif;
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Title() );
}