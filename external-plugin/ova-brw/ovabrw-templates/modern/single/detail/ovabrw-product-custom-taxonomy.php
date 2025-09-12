<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get custom taxonomies
$taxonomies = $product->get_custom_taxonomies();

if ( ovabrw_array_exists( $taxonomies ) ): ?>
	<ul class="ovabrw-product-custom-taxonomy">
		<?php foreach ( $taxonomies as $k => $taxonomy ):
			$name 	= ovabrw_get_meta_data( 'name', $taxonomy );
			$value 	= ovabrw_get_meta_data( 'value', $taxonomy, [] );
			$link 	= ovabrw_get_meta_data( 'link', $taxonomy, [] );
		?>
			<li class="item-taxonomy">
				<span class="label"><?php echo esc_html( $name.':' ); ?></span>
				<span class="value">
					<?php if ( ovabrw_array_exists( $value ) ): ?>
						<?php foreach ( $value as $k_v => $val ):
							$v_link 	= isset( $link[$k_v] ) ? $link[$k_v] : '';
							$separator 	= '';

							if ( $k_v < count( $value ) - 1 ) $separator = ',';
						?>
							<a href="<?php echo esc_url( $v_link ); ?>">
								<?php echo esc_html( $val ).$separator; ?>
							</a>
						<?php endforeach;
					endif; ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>