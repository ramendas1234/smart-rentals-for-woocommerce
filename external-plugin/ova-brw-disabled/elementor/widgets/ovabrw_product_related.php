<?php defined( 'ABSPATH' ) || exit;

/**
 * Class OVABRW_Widget_Product_Related
 */
if ( !class_exists( 'OVABRW_Widget_Product_Related' ) ) {

	class OVABRW_Widget_Product_Related extends \Elementor\Widget_Base {

		/**
		 * Get widget name
		 */
		public function get_name() {
			return 'ovabrw_product_related';
		}

		/**
		 * Get widget title
		 */
		public function get_title() {
			return esc_html__( 'Product Related', 'ova-brw' );
		}

		/**
		 * Get widget icon
		 */
		public function get_icon() {
			return 'eicon-product-related';
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

				// Default card
				$default_card = [
					'' => esc_html__( 'Default', 'ova-brw' )
				];

				// Get card templates
				$card_templates = ovabrw_get_card_templates();
				if ( !ovabrw_array_exists( $card_templates ) ) $card_templates = [];

				$this->add_control(
					'card_template',
					[
						'label' 	=> esc_html__( 'Card template', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'card1',
						'options' 	=> array_merge( $default_card, $card_templates ),
						'condition' => [
							'product_template' => 'modern'
						]
					]
				);

				$this->add_control(
					'posts_per_page',
					[
						'label' 	=> esc_html__( 'Products Per Page', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'default' 	=> 3,
					]
				);

				$this->add_responsive_control(
					'columns',
					[
						'label' 	=> esc_html__( 'Columns', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::NUMBER,
						'default' 	=> 3,
					]
				);

				$this->add_control(
					'orderby',
					[
						'label' 	=> esc_html__( 'Order By', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'date',
						'options' 	=> [
							'date' 			=> esc_html__( 'Date', 'ova-brw' ),
							'title' 		=> esc_html__( 'Title', 'ova-brw' ),
							'price' 		=> esc_html__( 'Price', 'ova-brw' ),
							'popularity' 	=> esc_html__( 'Popularity', 'ova-brw' ),
							'rating' 		=> esc_html__( 'Rating', 'ova-brw' ),
							'rand' 			=> esc_html__( 'Random', 'ova-brw' ),
							'menu_order' 	=> esc_html__( 'Menu Order', 'ova-brw' ),
						],
					]
				);

				$this->add_control(
					'order',
					[
						'label' 	=> esc_html__( 'Order', 'ova-brw' ),
						'type' 		=> \Elementor\Controls_Manager::SELECT,
						'default' 	=> 'desc',
						'options' 	=> [
							'asc' 	=> esc_html__( 'ASC', 'ova-brw' ),
							'desc' 	=> esc_html__( 'DESC', 'ova-brw' ),
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

			// Arguments
			$args = [
				'posts_per_page' 	=> ovabrw_get_meta_data( 'posts_per_page', $settings, 3 ),
				'columns' 			=> ovabrw_get_meta_data( 'columns', $settings, 3 ),
				'orderby' 			=> ovabrw_get_meta_data( 'orderby', $settings, 'date' ),
				'order' 			=> ovabrw_get_meta_data( 'order', $settings, 'DESC' ),
				'card_template'     => ovabrw_get_meta_data( 'card_template', $settings, 'card1' )
			];

			// Get template
			if ( 'modern' === ovabrw_get_meta_data( 'product_template', $settings ) ): ?>
				<div class="ovabrw-modern-product ovabrw-product-related-widget">
					<?php ovabrw_get_template( apply_filters( OVABRW_PREFIX.'widget_template_product_related', 'modern/single/detail/ovabrw-product-related.php', $settings ), $args ); ?>
				</div>
			<?php else:
				setup_postdata( $product->get_id() );

				// Set posts per page
				if ( !empty( $settings['posts_per_page'] ) ) {
					$args['posts_per_page'] = $settings['posts_per_page'];
				}

				// Set columns
				if ( !empty( $settings['columns'] ) ) {
					$args['columns'] = $settings['columns'];
				}

				// Get visible related products then sort them at random.
				$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

				// Handle orderby.
				$args['related_products'] = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );
			?>
				<div class="elementor-ralated">
					<?php wc_get_template( 'single-product/related.php', $args ); ?>
				</div>
			<?php endif;
		}
	}

	$widgets_manager->register( new OVABRW_Widget_Product_Related() );
}