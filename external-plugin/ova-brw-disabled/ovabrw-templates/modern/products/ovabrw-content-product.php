<?php if ( !defined( 'ABSPATH' ) ) exit();

global $product;

// Ensure visibility.
if ( !$product || !$product->is_visible() ) return;

?>

<li <?php wc_product_class( '', $product ); ?>>
	<?php ovabrw_get_template( 'modern/products/cards/ovabrw-'.ovabrw_get_card_template().'.php', [
		'product_id' => $product->get_id()
	]); ?>
</li>