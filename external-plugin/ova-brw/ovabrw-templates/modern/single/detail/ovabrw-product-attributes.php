<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get product attributes
$attributes = $product->get_attributes();
$attr_args 	= [];

if ( ovabrw_array_exists( $attributes ) ) {
	foreach ( $attributes as $attr_name => $obj_attr ) {
		$attr_label = wc_attribute_label( $attr_name, $product );
		$attr_text 	= $product->get_attribute( $attr_name );

		if ( $attr_label && $attr_text ) {
			$attr_args[$attr_label] = $attr_text;
		}
	}
}

if ( ovabrw_array_exists( $attr_args ) ): ?>
	<ul class="ovabrw-product-attributes">
		<?php foreach ( $attr_args as $label => $val ): ?>
			<li class="item-attribute">
				<span class="label">
					<?php echo sprintf( esc_html__( '%s:', 'ova-brw' ), $label ); ?>
				</span>
				<span class="value">
					<?php echo esc_html( $val ); ?>
				</span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>