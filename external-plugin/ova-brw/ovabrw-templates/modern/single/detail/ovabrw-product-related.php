<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get category ids
$category_ids = $product->get_category_ids();
$url_category = '';

if ( ovabrw_array_exists( $category_ids ) ) {
    $term_id = reset( $category_ids );

    if ( $term_id ) {
    	$url_category = get_term_link( $term_id );
    }
}

// Card template
$card_template = ovabrw_get_meta_data( 'card_template', $args, 'card1' );

// Agruments
$args = [
	'posts_per_page' => (int)ovabrw_get_meta_data( 'posts_per_page', $args, 3 ),
	'columns'        => (int)ovabrw_get_meta_data( 'columns', $args, 3 ),
	'orderby'        => ovabrw_get_meta_data( 'orderby', $args, 'rand' ),
	'order'          => ovabrw_get_meta_data( 'order', $args, 'DESC' )
];

// Get visible related products then sort them at random.
$args['related_products'] = array_filter( array_map( 'wc_get_product', wc_get_related_products( $product->get_id(), $args['posts_per_page'], $product->get_upsell_ids() ) ), 'wc_products_array_filter_visible' );

// Handle orderby.
$related_products = wc_products_array_orderby( $args['related_products'], $args['orderby'], $args['order'] );

if ( $related_products ): ?>
	<div class="ovabrw-related-products">
		<div class="head-related">
			<h2><?php esc_html_e( 'Related products', 'ova-brw' ); ?></h2>
			<?php if ( $url_category && apply_filters( OVABRW_PREFIX.'related_product_view_all', true ) ): ?>
				<a href="<?php echo esc_url( $url_category ); ?>">
					<?php esc_html_e( 'View All', 'ova-brw' ); ?>
				</a>
			<?php endif; ?>
		</div>
		<?php if ( 'card5' === $card_template || 'card6' === $card_template ): ?>
			<div class="related-products-list ovabrw-column-1">
		<?php else: ?>
			<div class="related-products-list">
		<?php endif;
			foreach ( $related_products as $related_product ):
				if ( $related_product && $related_product->is_type( OVABRW_RENTAL ) ):
					$post_object = get_post( $related_product->get_id() );
					setup_postdata( $GLOBALS['post'] =& $post_object );
					?><div class="related-item"><?php
						ovabrw_get_template( 'modern/products/cards/ovabrw-'.sanitize_file_name( $card_template ).'.php' );
					?></div><?php
				endif;
			endforeach; ?>
		</div>
	</div>
	<?php
endif;

wp_reset_postdata();
