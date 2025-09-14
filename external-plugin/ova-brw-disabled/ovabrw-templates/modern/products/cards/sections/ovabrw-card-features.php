<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get cart template
$card = ovabrw_get_meta_data( 'card_template', $args, ovabrw_get_card_template() );

if ( 'yes' !== ovabrw_get_setting( 'archive_product_show_features', 'yes' ) ) return;
if ( 'yes' !== ovabrw_get_option( 'glb_'.$card.'_features' , 'yes' ) ) return;

// Get features description
$features_desc = $product->get_meta_value( 'features_desc' );

if ( ovabrw_array_exists( $features_desc ) ):
	$features_icons 	= $product->get_meta_value( 'features_icons' );
	$features_special 	= $product->get_meta_value( 'features_special' );

	if ( !in_array( 'yes', $features_special ) ) return;
?>
	<ul class="ovabrw-features">
		<?php foreach ( $features_desc as $k => $desc ):
			$special = ovabrw_get_meta_data( $k, $features_special );
			if ( 'yes' !== $special ) continue;

			// Get icon class
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