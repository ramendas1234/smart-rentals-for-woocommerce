<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get product title
$title = $product->get_title();

if ( $title ): ?>
	<h2 class="ovabrw-product-title"><?php echo esc_html( $title ); ?></h2>
<?php endif; ?>