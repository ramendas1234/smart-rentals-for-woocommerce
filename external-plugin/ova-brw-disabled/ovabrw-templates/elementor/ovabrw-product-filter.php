<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get products
$products = OVABRW()->options->get_product_from_search([
	'posts_per_page' 	=> ovabrw_get_meta_data( 'posts_per_page', $args, 6 ),
	'orderby' 			=> ovabrw_get_meta_data( 'orderby', $args, 'ID' ),
	'order' 			=> ovabrw_get_meta_data( 'order', $args, 'DESC' ),
	'category_ids' 		=> ovabrw_get_meta_data( 'categories', $args )
]);

// Slide options
$slide_options = ovabrw_get_meta_data( 'slide_options', $args, [] );

if ( $products->have_posts() ): ?>
	<div class="ovabrw-product-filter">
		<div class="ovabrw-product-filter-slide owl-carousel owl-theme" data-options="<?php echo esc_attr( json_encode( $slide_options ) ); ?>">
			<?php while ( $products->have_posts() ): $products->the_post(); ?>
				<div class="item">
					<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.sanitize_file_name( $args['template'] ).'.php', [
						'thumbnail_type' => 'image'
					]); ?>
				</div>
			<?php endwhile; ?>
		</div>
	</div>
<?php else: ?>
	<div class="not-found">
		<?php esc_html_e( 'No product found.', 'ova-brw' ); ?>
	</div>
<?php endif; wp_reset_postdata(); ?>