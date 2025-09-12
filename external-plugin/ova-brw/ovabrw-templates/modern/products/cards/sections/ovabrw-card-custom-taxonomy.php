<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get cart template
$card = ovabrw_get_meta_data( 'card_template', $args, ovabrw_get_card_template() );

// Check show custom taxonomy
if ( 'yes' !== ovabrw_get_option( 'glb_'.$card.'_custom_taxonomy' , 'yes' ) ) return;

// Get custom taxonomies
$taxonomies = $product->get_custom_taxonomies( 'archive' );

if ( ovabrw_array_exists( $taxonomies ) ): ?>
	<ul class="ovabrw-custom-taxonomy">
		<?php foreach ( $taxonomies as $term ):
			$name 	= ovabrw_get_meta_data( 'name', $term );
			$values = ovabrw_get_meta_data( 'value', $term, [] );
			$links 	= ovabrw_get_meta_data( 'link', $term, [] )
		?>
			<li class="item-taxonomy">
				<span class="label">
					<?php echo sprintf( esc_html__( '%s:', 'ova-brw' ), $name ); ?>
				</span>
				<span class="value">
					<?php if ( ovabrw_array_exists( $values ) ): ?>
						<?php foreach ( $values as $k => $val ):
							$link = ovabrw_get_meta_data( $k, $links );
						?>
							<a href="<?php echo esc_url( $link ); ?>">
								<?php echo esc_html( $val ); ?>
							</a>
						<?php if ( $k < count( $values ) - 1 ) {
							echo esc_html__( ',', 'ova-brw' );
						}
						endforeach;
					endif; ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>