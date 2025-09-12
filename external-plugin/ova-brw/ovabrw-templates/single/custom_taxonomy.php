<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get custom taxonomies
$taxonomies = $product->get_custom_taxonomies();

if ( ovabrw_array_exists( $taxonomies ) ): ?>
	<ul class="ovabrw_cus_taxonomy">
		<?php foreach ( $taxonomies as $k => $taxonomy ):
			$name 	= ovabrw_get_meta_data( 'name', $taxonomy );
			$value 	= ovabrw_get_meta_data( 'value', $taxonomy, [] );
		?>
			<li class="<?php echo esc_attr( $k ); ?>">
				<label><?php echo esc_html( $name.':' ); ?></label>
				<span><?php echo implode( ', ', $value ); ?></span>
			</li>
		<?php endforeach; ?>			
	</ul>
<?php endif; ?>