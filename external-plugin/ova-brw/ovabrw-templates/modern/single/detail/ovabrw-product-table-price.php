<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Show table price
if ( 'yes' !== ovabrw_get_setting( 'template_show_table_price', 'yes' ) ) return;

// Check price list available
if ( !$product->price_list_available() ) return;

?>

<div class="ovabrw-product-table-price">
	<h2 class="title">
		<?php esc_html_e( 'Price Table', 'ova-brw' ); ?>
	</h2>
	<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-weekdays-price.php' ); ?>
	<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-global-discount.php' ); ?>
	<?php ovabrw_get_template( 'modern/single/detail/ovabrw-product-special-time.php' ); ?>
</div>