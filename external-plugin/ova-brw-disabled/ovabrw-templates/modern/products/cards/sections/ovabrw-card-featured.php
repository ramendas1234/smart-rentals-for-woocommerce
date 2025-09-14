<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get card template
$card = ovabrw_get_meta_data( 'card_template', $args, ovabrw_get_card_template() );

// Get featured
$features_featured = $product->get_meta_value( 'features_featured' );

if ( $product->is_featured() && 'yes' === ovabrw_get_option( 'glb_'.$card.'_featured' , 'yes' ) ): ?>
	<span class="ovabrw-featured-product">
		<?php esc_html_e( 'Featured', 'ova-brw' ); ?>
	</span>
<?php endif;

if ( 'yes' === ovabrw_get_setting( 'archive_product_show_special_features' , 'yes' ) && 'yes' === ovabrw_get_option( 'glb_'.$card.'_feature_featured' , 'yes' ) && ovabrw_array_exists( $features_featured ) ):
	// Get description
	$features_desc = $product->get_meta_value( 'features_desc' );

	foreach ( $features_featured as $k => $val ):
		if ( 'yes' === $val ):
			$desc = ovabrw_get_meta_data( $k, $features_desc );
			if ( !$desc ) continue;
		?>
			<span class="ovabrw-features-featured">
				<?php echo esc_html( $desc ); ?>
			</span>
		<?php break;
		endif;
	endforeach;
endif; ?>