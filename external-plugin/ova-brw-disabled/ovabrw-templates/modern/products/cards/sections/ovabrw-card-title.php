<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get rental product
$rental_product = OVABRW()->rental->get_rental_product( $product->get_id() );
if ( !$rental_product ) return;

// Get cart template
$card = ovabrw_get_meta_data( 'card_template', $args, ovabrw_get_card_template() );

// Product URL
$product_url = $rental_product->get_permalink();

// Product title
$product_title = $product->get_title();

if ( $product_title ): ?>
	<h2 class="ovabrw-title">
		<a href="<?php echo esc_url( $product_url ); ?>">
			<?php echo wp_kses_post( $product_title ); ?>
		</a>
	</h2>
<?php endif; ?>