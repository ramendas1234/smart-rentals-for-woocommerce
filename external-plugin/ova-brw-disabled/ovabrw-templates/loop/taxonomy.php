<?php defined( 'ABSPATH' ) || exit;
/**
 * The template for displaying custom taxonomy content within loop
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/loop/taxonomy.php
 *
 */

global $product;
if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) return;

// Get custom taxonomies
$taxonomies = $product->get_custom_taxonomies( 'archive' );

if ( ovabrw_array_exists( $taxonomies ) ): ?>
	<ul class="product_listing_custom_tax">
		<?php foreach ( $taxonomies as $slug => $tax ):
			$name 	= ovabrw_get_meta_data( 'name', $tax );
			$value 	= ovabrw_get_meta_data( 'value', $tax, [] );
		?>
			<li class="<?php echo esc_attr( 'tax_'.$slug ); ?> ">
				<label><?php echo esc_html( $name.':' ); ?></label>
				<span><?php echo implode( ', ', $value ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>
