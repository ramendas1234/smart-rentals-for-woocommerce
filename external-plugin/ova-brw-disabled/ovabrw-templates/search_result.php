<?php if ( !defined( 'ABSPATH' ) ) exit();

// Add woocommerce class
add_filter( 'body_class', function( $classes ) {
	return array_merge( $classes, [ 'woocommerce' ] );
});

// Get products
$products = OVABRW()->options->get_available_items_from_search( $_GET );

// Get columns
$columns = wc_get_loop_prop( 'columns' );

// Get cart template
$card = ovabrw_get_card_template();
if ( 'modern' !== ovabrw_get_setting( 'search_template', 'modern' ) ) $card = '';
if ( in_array( $card , ['card5', 'card6'] ) ) $columns = 1;
if ( isset( $GLOBALS['woocommerce_loop'], $GLOBALS['woocommerce_loop']['columns'] ) ) {
	$GLOBALS['woocommerce_loop']['columns'] = $columns;
}

// Get header
get_header('shop');

/**
 * Hook: woocommerce_before_main_content.
 */
do_action( 'woocommerce_before_main_content' );

/**
 * Hook: woocommerce_shop_loop_header.
 */
do_action( 'woocommerce_shop_loop_header' );

if ( $products && $products->have_posts() ):
	/**
	 * Hook: woocommerce_before_shop_loop.
	 */
	do_action( 'woocommerce_before_shop_loop' );

	// Loop start
	woocommerce_product_loop_start();

	// Loop
	while ( $products->have_posts() ) : $products->the_post();
		global $product;

		// Ensure visibility.
		if ( !$product || !$product->is_visible() ) continue;

		if ( $card ):
			$thumbnail_type = ovabrw_get_option( 'glb_'.sanitize_file_name( $card ).'_thumbnail_type', 'slider' );
		?>
			<li <?php wc_product_class( 'item', $product ); ?>>
				<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.sanitize_file_name( $card ).'.php', [
					'thumbnail_type' => $thumbnail_type
				]); ?>
			</li>
		<?php else:
			wc_get_template_part( 'content', 'product' );
		endif;
	endwhile; wp_reset_postdata(); // END

	// Loop end
	woocommerce_product_loop_end();

	/**
	 * Hook: woocommerce_after_shop_loop.
	 */
	do_action( 'woocommerce_after_shop_loop' );
else:
	do_action( 'woocommerce_no_products_found' );
endif;

/**
 * Hook: woocommerce_after_main_content.
 */
do_action( 'woocommerce_after_main_content' );

/**
 * Hook: woocommerce_sidebar.
 */
do_action( 'woocommerce_sidebar' );

// Get footer
get_footer('shop');