<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get cart template
$card = ovabrw_get_meta_data( 'card_template', $args, ovabrw_get_card_template() );

// Check show short description
if ( 'yes' !== ovabrw_get_option( 'glb_'.$card.'_short_description' , 'yes' ) ) return;

// Get short description
$short_description = $product->get_short_description();

if ( $short_description ): ?>
	<div class="ovabrw-short-description">
		<?php echo wp_kses_post( apply_filters( 'woocommerce_short_description', $short_description ) ); ?>
	</div>
<?php endif; ?>