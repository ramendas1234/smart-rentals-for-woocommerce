<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Calendar
 */
if ( !class_exists( 'OVABRW_Widget_Product_Calendar' ) ) {

	class OVABRW_Widget_Product_Calendar extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_calendar';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Calendar', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-calendar';
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
			// Fullcalendar
		    wp_enqueue_script( 'ovabrw-calendar', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/index.global.min.js', [ 'jquery' ], null, true );
		    wp_enqueue_script( 'ovabrw-calendar-locales', OVABRW_PLUGIN_URI.'assets/libs/fullcalendar/locales-all.global.min.js', [ 'jquery' ], null, true );
			    
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
				'section_calendar_style',
				[
					'label' 	=> esc_html__( 'Calendar', 'ova-brw' ),
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
					'calendar_bg',
					[
						'label'  	=> esc_html__( 'Background', 'ova-brw' ),
						'type' 	 	=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.woocommerce {{WRAPPER}} .wrap_calendar' => 'background-color: {{VALUE}}',
						],
					]
				);

				$this->add_responsive_control(
					'calendar_padding',
					[
						'label' 	 => esc_html__( 'Padding', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .wrap_calendar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'calendar_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .wrap_calendar' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			// Title + Arrow Calendar
			$this->start_controls_section(
				'section_title_calendar_style',
				[
					'label' 	=> esc_html__( 'Title', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_responsive_control(
					'title_calendar_align',
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
						'selectors' => [
							'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar' => 'align-items: {{VALUE}}',
						],
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'title_calendar_typography',
						'selector' 	=> '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button',
					]
				);

				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					[
						'name' 		=> 'title_button_border',
						'selector' 	=> '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button',
					]
				);

				$this->add_responsive_control(
					'title_buttton_border_radius',
					[
						'label' 	 => esc_html__( 'Border Radius', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
						],
					]
				);

				$this->add_control(
					'button_action_color',
					[
						'label'  	=> esc_html__( 'Background Button Active', 'ova-brw' ),
						'type' 	 	=> \Elementor\Controls_Manager::COLOR,
						'selectors' => [
							'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button-active' => 'background-color: {{VALUE}};',
							'.fc .fc-button-primary:focus, .fc .fc-button-primary:not(:disabled).fc-button-active:focus, .fc .fc-button-primary:not(:disabled):active:focus' => 'box-shadow: unset;',
						],
					]
				);

				$this->start_controls_tabs( 'tabs_title_button_style' );

					$this->start_controls_tab(
			            'title_calendar_style_normal',
			            [
			                'label' => esc_html__( 'Normal', 'ova-brw' ),
			            ]
			        );

			        	$this->add_control(
				            'title_button_color',
				            [
				                'label' 	=> esc_html__( 'Color', 'ova-brw' ),
				                'type' 		=> \Elementor\Controls_Manager::COLOR,
				                'selectors' => [
				                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'color: {{VALUE}};',
				                ],
				            ]
				        );

				        $this->add_control(
				            'title_button_bg',
				            [
				                'label' 	=> esc_html__( 'Background', 'ova-brw' ),
				                'type' 		=> \Elementor\Controls_Manager::COLOR,
				                'selectors' => [
				                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'background-color: {{VALUE}};',
				                ],
				            ]
				        );

			        $this->end_controls_tab();

			        $this->start_controls_tab(
			            'title_calendar_style_hover',
			            [
			                'label' => esc_html__( 'Hover', 'ova-brw' ),
			            ]
			        );

			        	$this->add_control(
				            'title_button_color_hover',
				            [
				                'label' 	=> esc_html__( 'Color', 'ova-brw' ),
				                'type' 		=> \Elementor\Controls_Manager::COLOR,
				                'selectors' => [
				                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button:hover' => 'color: {{VALUE}};',
				                ],
				            ]
				        );

				        $this->add_control(
				            'title_button_bg_hover',
				            [
				                'label' 	=> esc_html__( 'Background', 'ova-brw' ),
				                'type' 		=> \Elementor\Controls_Manager::COLOR,
				                'selectors' => [
				                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button:hover' => 'background-color: {{VALUE}};',
				                ],
				            ]
				        );

			        $this->end_controls_tab();

				$this->end_controls_tabs();

				$this->add_responsive_control(
					'title_button_padding',
					[
						'label' 	 => esc_html__( 'Padding', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'title_button_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar .fc-button-group .fc-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();
			// End

			// Month -Year
			$this->start_controls_section(
				'section_month_year_style',
				[
					'label' 	=> esc_html__( 'Month Year', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'my_calendar_typography',
						'selector' 	=> '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar h2',
					]
				);

				$this->add_control(
		            'my_color',
		            [
		                'label' 	=> esc_html__( 'Color', 'ova-brw' ),
		                'type' 		=> \Elementor\Controls_Manager::COLOR,
		                'selectors' => [
		                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar h2' => 'color: {{VALUE}};',
		                ],
		            ]
		        );

		        $this->add_responsive_control(
					'my_padding',
					[
						'label' 	 => esc_html__( 'Padding', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar h2' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_responsive_control(
					'my_margin',
					[
						'label' 	 => esc_html__( 'Margin', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .wrap_calendar .fc-header-toolbar h2' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

			$this->end_controls_section();

			// Day
			$this->start_controls_section(
				'section_day_style',
				[
					'label' 	=> esc_html__( 'Day', 'ova-brw' ),
					'tab' 		=> \Elementor\Controls_Manager::TAB_STYLE,
					'condition' => [
						'product_template' => 'classic'
					]
				]
			);

				$this->add_control(
		            'today_bg',
		            [
		                'label' 	=> esc_html__( 'Today Background', 'ova-brw' ),
		                'type' 		=> \Elementor\Controls_Manager::COLOR,
		                'selectors' => [
		                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-day-today' => 'background-color: {{VALUE}} !important;',
		                ],
		            ]
		        );

		        $this->add_control(
		            'day_past_bg',
		            [
		                'label' 	=> esc_html__( 'Day Past Background', 'ova-brw' ),
		                'type' 		=> \Elementor\Controls_Manager::COLOR,
		                'selectors' => [
		                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-day-past' => 'background-color: {{VALUE}} !important;',
		                ],
		            ]
		        );

				$this->add_group_control(
					\Elementor\Group_Control_Typography::get_type(),
					[
						'name' 		=> 'day_calendar_typography',
						'selector' 	=> '.woocommerce {{WRAPPER}} .wrap_calendar .fc-col-header-cell-cushion',
					]
				);

				$this->add_control(
		            'header_bg',
		            [
		                'label' 	=> esc_html__( 'Header Background', 'ova-brw' ),
		                'type' 		=> \Elementor\Controls_Manager::COLOR,
		                'selectors' => [
		                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-col-header-cell' => 'background-color: {{VALUE}};',
		                ],
		            ]
		        );

				$this->add_control(
		            'header_color',
		            [
		                'label' 	=> esc_html__( 'Header Color', 'ova-brw' ),
		                'type' 		=> \Elementor\Controls_Manager::COLOR,
		                'selectors' => [
		                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-col-header-cell-cushion' => 'color: {{VALUE}};',
		                ],
		            ]
		        );

		        $this->add_responsive_control(
					'header_padding',
					[
						'label' 	 => esc_html__( 'Header Padding', 'ova-brw' ),
						'type' 		 => \Elementor\Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
						'selectors'  => [
							'.woocommerce {{WRAPPER}} .wrap_calendar .fc-col-header-cell-cushion' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);

				$this->add_control(
		            'body_bg',
		            [
		                'label' 	=> esc_html__( 'Body Background', 'ova-brw' ),
		                'type' 		=> \Elementor\Controls_Manager::COLOR,
		                'selectors' => [
		                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-daygrid-day' => 'background-color: {{VALUE}};',
		                ],
		            ]
		        );

				$this->add_control(
		            'body_color',
		            [
		                'label' 	=> esc_html__( 'Body Color', 'ova-brw' ),
		                'type' 		=> \Elementor\Controls_Manager::COLOR,
		                'selectors' => [
		                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-daygrid-day-number' => 'color: {{VALUE}};',
		                ],
		            ]
		        );

		        $this->add_control(
		            'price_color',
		            [
		                'label' 	=> esc_html__( 'Price Color', 'ova-brw' ),
		                'type' 		=> \Elementor\Controls_Manager::COLOR,
		                'selectors' => [
		                    '.woocommerce {{WRAPPER}} .wrap_calendar .fc-daygrid-day-bg span' => 'color: {{VALUE}};',
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
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_calendar', 'modern/single/detail/ovabrw-product-calendar.php', $settings ), $settings ); ?>
				</div>
			<?php else: ?>
				<div class="elementor-calendar">
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_calendar', 'single/calendar.php', $settings ), [
						'product_id' => $product->get_id()
					]); ?>
				</div>
			<?php endif;
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Calendar() );
}