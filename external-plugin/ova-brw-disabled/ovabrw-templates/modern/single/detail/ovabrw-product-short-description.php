<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get short description
$short_description = $product->get_short_description();

if ( $short_description ): ?>
	<div class="ovabrw-product-short-description">
		<?php echo apply_filters( 'woocommerce_short_description', $short_description ); ?>
	</div>
<?php endif; ?>