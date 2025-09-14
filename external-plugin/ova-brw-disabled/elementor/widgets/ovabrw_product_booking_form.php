<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Booking_Form
 */
if ( !class_exists( 'OVABRW_Widget_Product_Booking_Form' ) ) {

	class OVABRW_Widget_Product_Booking_Form extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_booking_form';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Booking Form', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-form-horizontal';
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
			// Enqueue Google API Key Map
			$api_key = ovabrw_get_setting( 'google_key_map', false );
			if ( $api_key && !wp_script_is( 'google_map' ) ) {
				wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&libraries=places&loading=async&callback=Function.prototype', false, true );
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

				$this->add_control(
					'show_form',
					[
						'label' 	=> esc_html__( 'Show Form', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'both',
						'options' 	=> [
							'booking' 	=> esc_html__( 'Booking Form', 'ova-brw' ),
							'request' 	=> esc_html__( 'Request Form', 'ova-brw' ),
							'both' 		=> esc_html__( 'Both', 'ova-brw' )
						],
						'condition' => [
							'product_template' => 'modern'
						]
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_head_tab_style',
				[
					'label' 	=> esc_html__( 'Head Tab', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'modern'
					]
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'title_typography',
						'selector' 	=> '{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-head .item-tab',
					]
				);

				$this->add_control(
					'title_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-head .item-tab' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'title_active_color',
					[
						'label' 	=> esc_html__( 'Color Active', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-head .item-tab.active' => 'color: {{VALUE}}; border-color: {{VALUE}}'
						],
					]
				);

				$this->add_control(
					'head_bgcolor',
					[
						'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-head' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'head_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-head' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'head_border_radius',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-head' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_form_tab_style',
				[
					'label' 	=> esc_html__( 'Form', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'modern'
					]
				]
			);

				$this->add_control(
					'form_bgcolor',
					[
						'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #booking_form, .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #request_booking' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'form_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #booking_form, .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #request_booking' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'form_border_radius',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Border Radius', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #booking_form, .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #request_booking' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			$this->start_controls_section(
				'section_button_style',
				[
					'label' 	=> esc_html__( 'Button', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'modern'
					]
				]
			);

				$this->add_control(
					'button_color',
					[
						'label' 	=> esc_html__( 'Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #booking_form button.submit, .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #request_booking button.submit' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_color_hover',
					[
						'label' 	=> esc_html__( 'Color Hover', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #booking_form button.submit:hover, .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #request_booking button.submit:hover' => 'color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_bgcolor',
					[
						'label' 	=> esc_html__( 'Background Color', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #booking_form button.submit, .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #request_booking button.submit' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_control(
					'button_bgcolor_hover',
					[
						'label' 	=> esc_html__( 'Background Color Hover', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #booking_form button.submit:hover, .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #request_booking button.submit:hover' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'button_padding',
					[
						'type' 			=> \Elementor\Controls_Manager::DIMENSIONS,
						'label' 		=> esc_html__( 'Padding', 'ova-brw' ),
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors' 	=> [
							'{{WRAPPER}} .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #booking_form button.submit, .ovabrw-modern-product .ovabrw-product-form-tabs .ovabrw-tab-content #request_booking button.submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_booking_form', 'modern/single/detail/ovabrw-product-form-tabs.php', $settings ), $settings ); ?>
				</div>
			<?php else: ?>
				<div class="elementor-booking-form">
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_booking_form', 'single/booking-form.php', $settings ), [
						'product_id' => $product->get_id()
					]); ?>
				</div>
			<?php endif;
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Booking_Form() );
}