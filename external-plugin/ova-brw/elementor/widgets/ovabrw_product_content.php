<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Content
 */
if ( !class_exists( 'OVABRW_Widget_Product_Content' ) ) {

	class OVABRW_Widget_Product_Content extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_content';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Content', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-product-description';
		}

		/**
		 * Get widget categories
		 */
		public function get_categories() {
			return [ 'ovabrw-product' ];
		}

		/**
		 * Get register controls
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

			$this->end_controls_section();
		}

		/**
		 * Render HTML
		 */
		protected function render() {
			// Get settings
			$settings = $this->get_settings_for_display();

			// Check is product
	    	if ( is_product() ) {
	    		ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_content', 'modern/single/detail/ovabrw-product-content.php', $settings ), $settings );
			} else {
				// Get product ID
				$product_id = ovabrw_get_meta_data( 'product_id', $settings );

				// Get product
				$product = wc_get_product( $product_id );

				// Check rental product
		    	if ( !$product || !$product->is_type( OVABRW_RENTAL ) ): ?>
					<div class="ovabrw_elementor_no_product">
						<span><?php echo wp_kses_post( $this->get_title() ); ?></span>
					</div>
					<?php return;
				else:
					$description = apply_filters( 'woocommerce_short_description', $product->get_description() );
					echo wp_kses_post( $description );
				endif;
			}
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Content() );
}