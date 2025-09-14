<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get product ID
$product_id = ovabrw_get_meta_data( 'id', $args, get_the_id() );

// Get product
$product = wc_get_product( $product_id );

// Get header
get_header( 'shop' );

// Loop
while ( have_posts() ): the_post();
	if ( $product && $product->is_type( OVABRW_RENTAL ) ) {
		// Product template
		$template = ovabrw_get_meta_data( 'template', $args );
		if ( !$template ) $template = $product->get_template();

		do_action( 'woocommerce_before_single_product' );
		echo Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $template );
		do_action( 'woocommerce_after_single_product' );
	} else {
		wc_get_template_part( 'content', 'single-product' );
	}
endwhile; // END of the loop.

// Get footer
get_footer( 'shop' );