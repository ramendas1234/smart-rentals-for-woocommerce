<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Tabs
 */
if ( !class_exists( 'OVABRW_Widget_Product_Tabs' ) ) {

	class OVABRW_Widget_Product_Tabs extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_tabs';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Tabs', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-product-tabs';
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
				'section_content',
				[
					'label' => esc_html__( 'Content', 'ova-brw' ),
				]
			);

				$this->add_control(
					'panel_alert',
					[
						'type' 			=> \Elementor\Controls_Manager::ALERT,
						'alert_type' 	=> 'warning',
						'content' 		=> esc_html__( 'This widget only works on single product detail pages.', 'ova-brw' ),
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
		}

		/**
		 * Render HTML
		 */
		protected function render() {
			// Get settings
			$settings = $this->get_settings_for_display();

			// Global product
			global $product;

			// Check rental product
	    	if ( !$product || !$product->is_type( OVABRW_RENTAL ) ): ?>
				<div class="ovabrw_elementor_no_product">
					<span><?php echo wp_kses_post( $this->get_title() ); ?></span>
				</div>
				<?php return;
			endif;

			// Get template
			if ( 'modern' === ovabrw_get_meta_data( 'product_template', $settings ) ):
				add_filter( OVABRW_PREFIX.'show_request_booking_in_product_tabs', '__return_false' );

				// Check query product
				if ( is_singular( 'product' ) ): ?>
				    <div class="ovabrw-modern-product">
						<?php wc_get_template( 'single-product/tabs/tabs.php' ); ?>
					</div>
				<?php else: ?>
				    <div class="empty-item">
				        <h4>
				            <?php esc_html_e( 'This widget just works on the product detail page', 'ova-brw' ); ?>
				        </h4>
				    </div>
				<?php endif;
			else:
				if ( is_singular( 'product' ) ): ?>
				    <div class="elementor-tabs">
						<?php wc_get_template( 'single-product/tabs/tabs.php' ); ?>
					</div>
				<?php else: ?>
				    <div class="empty-item">
				        <h4>
				            <?php esc_html_e( 'This widget just works on the product detail page', 'ova-brw' ); ?>
				        </h4>
				    </div>
				<?php endif;
			endif;
		}
	}

	// Register new widget
	$widgets_manager->register( new OVABRW_Widget_Product_Tabs() );
}