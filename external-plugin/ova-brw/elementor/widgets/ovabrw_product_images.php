<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Images
 */
if ( !class_exists( 'OVABRW_Widget_Product_Images' ) ) {

	class OVABRW_Widget_Product_Images extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_images';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Images', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-product-images';
		}

		/**
		 * Get widget categories
		 */
		public function get_categories() {
			return [ 'ovabrw-product' ];
		}

		/**
		 * Get script depends
		 */
		public function get_script_depends() {
			// Fancybox
			wp_enqueue_script( 'fancybox', OVABRW_PLUGIN_URI.'/assets/libs/fancybox/fancybox.umd.js', [ 'jquery' ], null, true );
			wp_enqueue_style( 'fancybox', OVABRW_PLUGIN_URI.'/assets/libs/fancybox/fancybox.css', [], null );

			// Carosel
			wp_enqueue_style( 'carousel', OVABRW_PLUGIN_URI.'assets/libs/carousel/owl.carousel.min.css' );
			wp_enqueue_script( 'carousel', OVABRW_PLUGIN_URI.'assets/libs/carousel/owl.carousel.min.js', [ 'jquery' ], false, true );

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
				'section_product_gallery_style',
				[
					'label' 	=> esc_html__( 'Style', 'ova-brw' ),
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
						'raw' 	=> esc_html__( 'The style of this widget is often affected by your theme and <p></p>lugins. If you experience any such issue, try to switch to a basic theme and deactivate related plugins.', 'ova-brw' ),
						'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'image_border',
						'selector' 	=> '.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-viewport',
						'separator' => 'before',
					]
				);

				$this->add_responsive_control(
					'image_border_radius',
					[
						'label' 	 => esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-viewport' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						],
					]
				);

				$this->add_control(
					'spacing',
					[
						'label' 	 => esc_html__( 'Spacing Image', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-viewport' => 'margin-bottom: {{SIZE}}{{UNIT}}',
						],
					]
				);

				$this->add_control(
					'heading_thumbs_style',
					[
						'label' 	=> esc_html__( 'Thumbnails', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'thumbs_border',
						'selector' 	=> '.woocommerce {{WRAPPER}} .flex-control-thumbs img',
					]
				);

				$this->add_responsive_control(
					'thumbs_border_radius',
					[
						'label' 	 => esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .flex-control-thumbs img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						],
					]
				);

				$this->add_control(
					'spacing_thumbs',
					[
						'label' 	 => esc_html__( 'Spacing Thumbnails', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::SLIDER,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .flex-control-thumbs li' => 'padding-right: calc({{SIZE}}{{UNIT}} / 2); padding-left: calc({{SIZE}}{{UNIT}} / 2); padding-bottom: {{SIZE}}{{UNIT}}',
							'.woocommerce {{WRAPPER}} .flex-control-thumbs' => 'margin-right: calc(-{{SIZE}}{{UNIT}} / 2); margin-left: calc(-{{SIZE}}{{UNIT}} / 2)',
						],
					]
				);

				$this->add_responsive_control(
					'thumbnails_align',
					[
						'label' 	=> esc_html__( 'Alignment', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::CHOOSE,
						'options' 	=> [
							'flex-start' 	=> [
								'title' 	=> esc_html__( 'Left', 'ova-brw' ),
								'icon' 		=> 'eicon-text-align-left',
							],
							'center' 	=> [
								'title' => esc_html__( 'Center', 'ova-brw' ),
								'icon' 	=> 'eicon-text-align-center',
							],
							'flex-end' 	=> [
								'title' => esc_html__( 'Right', 'ova-brw' ),
								'icon' 	=> 'eicon-text-align-right',
							],
							'space-between' => [
								'title' 	=> esc_html__( 'Justified', 'ova-brw' ),
								'icon' 		=> 'eicon-text-align-justify',
							],
						],
						'selectors' => [
							'.woocommerce {{WRAPPER}} .woocommerce-product-gallery .flex-control-nav.flex-control-thumbs' => 'justify-content: {{VALUE}}',
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
				<div class="elementor-product-image ovabrw-modern-product">
				<?php
					// Product images template
					ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_images', 'modern/single/detail/ovabrw-product-images.php', $settings ), $settings );

					// Product is featured template
					ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_is_featured', 'modern/single/detail/ovabrw-product-is-featured.php', $settings ), $settings );
				?>
				</div>
			<?php elseif ( is_product() ): ?>
				<div class="elementor-product-image">
					<?php wc_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_images', 'single-product/product-image.php', $settings ), [
						'product_id' => $product->get_id()
					]); ?>
				</div>
			<?php endif;
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Images() );
}