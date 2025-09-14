<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Extra tab
$extra_tab = $product->get_meta_value( 'manage_extra_tab' );
if ( 'no' === $extra_tab ) return;

// Shortcode
$shortcode = ovabrw_get_setting( 'extra_tab_shortcode_form' );

if ( 'new_form' === $extra_tab ) {
	$shortcode = $product->get_meta_value( 'extra_tab_shortcode' );
}

if ( $shortcode ): ?>
	<div class="ova-contact-form-tabs-update">
		<?php echo do_shortcode( htmlspecialchars_decode( $shortcode ) ) ; ?>
	</div>
<?php endif; ?>