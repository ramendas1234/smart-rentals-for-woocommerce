<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

$features_desc 	= $product->get_meta_value( 'features_desc' );
$features_label = $product->get_meta_value( 'features_label' );
$features_icons = $product->get_meta_value( 'features_icons' );

if ( ovabrw_array_exists( $features_desc ) ): ?>
<ul class="ovabrw_woo_features">
	<?php foreach ( $features_desc as $k => $desc ):
		$label 		= ovabrw_get_meta_data( $k, $features_label );
		$icon_class = ovabrw_get_meta_data( $k, $features_icons );
	?>
		<li>
			<?php if ( $icon_class ): // Icon class ?>
				<i aria-hidden="true" class="<?php echo esc_attr( $icon_class ); ?>"></i>
			<?php endif;

			// Label
			if ( $label ): ?>
				<label><?php echo esc_html( $label ); ?>: </label>
			<?php endif;

			// Description
			if ( $desc ): ?>
				<span><?php echo esc_html( $desc ); ?></span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
<?php endif; ?>
