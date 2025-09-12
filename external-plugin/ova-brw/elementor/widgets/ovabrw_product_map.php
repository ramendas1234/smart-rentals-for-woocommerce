<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Map
 */
if ( !class_exists( 'OVABRW_Widget_Product_Map' ) ) {

	class OVABRW_Widget_Product_Map extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_map';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Map', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-google-maps';
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
			// Google API Key
			$api_key = ovabrw_get_setting( 'google_key_map', false );
			if ( $api_key ) {
				wp_enqueue_script( 'ovabrw-google-maps','https://maps.googleapis.com/maps/api/js?key='. esc_attr( $api_key ) .'&loading=async&callback=Function.prototype&libraries=places', false, true );
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

				$this->add_control(
					'zoom',
					[
						'label' 	=> esc_html__( 'Zoom', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'default' 	=> 17,
					]
				);

				$this->add_control(
					'height',
					[
						'label' 		=> esc_html__( 'Height', 'ova-brw' ),
						'type' 			=> \Elementor\Controls_Manager::SLIDER,
						'size_units' 	=> [ 'px', '%', 'em', 'rem', 'custom' ],
						'range' 		=> [
							'px' 	=> [
								'min' 	=> 0,
								'max' 	=> 1000,
								'step' 	=> 5,
							],
						],
						'default' 	=> [
							'unit' 	=> 'px',
							'size' 	=> 500,
						],
						'selectors' => [
							'{{WRAPPER}} .ovabrw-product-map #ovabrw-show-map' => 'height: {{SIZE}}{{UNIT}};',
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
			ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_map', 'elementor/ovabrw-product-map.php', $settings ), [
				'id' 	=> $product->get_id(),
				'zoom' 	=> ovabrw_get_meta_data( 'zoom', 17 )
			]);
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Map() );
}