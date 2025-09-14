<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

$product_id 		= $product->get_id();
$features_featured 	= $product->get_meta_value( 'features_featured' );
$card 				= ovabrw_get_card_template();

if ( $product && $product->is_featured() ): ?>
	<span class="ovabrw-product-is-featured">
		<?php esc_html_e( 'Featured', 'ova-brw' ); ?>
	</span>
<?php endif;

if ( 'yes' === ovabrw_get_setting( 'template_show_special_feature', 'yes' ) && ovabrw_array_exists( $features_featured ) ):
	foreach ( $features_featured as $k => $val ):
		if ( 'yes' === $val ):
			$features_desc 	= $product->get_meta_value( 'features_desc' );
			$desc 			= ovabrw_get_meta_data( $k, $features_desc );

			if ( !$desc ) continue;
		?>
			<span class="ovabrw-product-features-is-featured">
				<?php echo esc_html( $desc ); ?>
			</span>
		<?php break;
		endif;
	endforeach;
endif; ?>