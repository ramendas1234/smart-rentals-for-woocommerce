<?php defined( 'ABSPATH' ) || exit;

// Global typography enabled
if ( ovabrw_global_typography() ) {
	$args['class'] .= ' ovabrw-modern-product';
}

global $product;

if ( ovabrw_get_meta_data( 'product_id', $args ) ) {
	$product = wc_get_product( $args['product_id'] );
}

if ( !$product ) return;

?>
<div class="<?php echo esc_attr( $args['class'] ); ?>">
	<?php if ( ovabrw_global_typography() ) {
		wc_get_template( 'single-product/tabs/tabs.php' );
	} ?>
</div>