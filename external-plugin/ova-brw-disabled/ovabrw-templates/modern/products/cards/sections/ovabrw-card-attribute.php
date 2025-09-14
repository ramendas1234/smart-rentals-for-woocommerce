<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get cart template
$card = ovabrw_get_meta_data( 'card_template', $args, ovabrw_get_card_template() );

// Check show attributes
if ( 'yes' !== ovabrw_get_option( 'glb_'.$card.'_attribute' , 'yes' ) ) return;

// Get attributes
$attributes = $product->get_attributes();
$attr_data 	= [];

if ( ovabrw_array_exists( $attributes ) ) {
	foreach ( $attributes as $attr_name => $obj_attr ) {
		$attr_label = wc_attribute_label( $attr_name, $product );
		$attr_text 	= $product->get_attribute( $attr_name );

		if ( $attr_label && $attr_text ) $attr_data[$attr_label] = $attr_text;
	}
}

if ( ovabrw_array_exists( $attr_data ) ): ?>
	<ul class="ovabrw-attributes">
		<?php foreach ( $attr_data as $label => $val ): ?>
			<li class="item-attribute">
				<span class="label"><?php echo esc_html( $label.':' ); ?></span>
				<span class="value"><?php echo esc_html( $val ); ?></span>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif;