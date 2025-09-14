<?php if ( !defined( 'ABSPATH' ) ) exit();

// Show feature
if ( 'yes' !== ovabrw_get_setting( 'template_show_feature', 'yes' ) ) return;

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Features description
$features_desc = $product->get_meta_value( 'features_desc' );

if ( ovabrw_array_exists( $features_desc ) ):
	$features_icons 	= $product->get_meta_value( 'features_icons' );
	$features_special 	= $product->get_meta_value( 'features_special' );

	if ( !in_array( 'yes', $features_special ) ) return;
?>
	<ul class="ovabrw-product-features">
		<?php foreach ( $features_desc as $k => $desc ):
			$special 	= ovabrw_get_meta_data( $k, $features_special );
			$icon_class = ovabrw_get_meta_data( $k, $features_icons );
		?>
			<li class="item-feature">
				<?php if ( $icon_class ): ?>
					<i aria-hidden="true" class="<?php echo esc_attr( $icon_class ); ?>"></i>
				<?php endif; ?>
				<span><?php echo esc_html( $desc ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>