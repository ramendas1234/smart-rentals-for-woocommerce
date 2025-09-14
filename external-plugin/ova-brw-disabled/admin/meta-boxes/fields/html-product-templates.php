<?php if ( !defined( 'ABSPATH' ) ) exit();

$templates = [
	'global' 	=> esc_html__( 'Category Setting', 'ova-brw' ),
    'default' 	=> esc_html__( 'Classic', 'ova-brw' )
];

if ( ovabrw_global_typography() ) {
    $templates['modern'] = esc_html__( 'Modern', 'ova-brw' );
}

// Get templates from elementor
$elementor_templates = get_posts([
	'post_type'     => 'elementor_library', 
    'meta_key'      => '_elementor_template_type', 
    'meta_value'    => 'page',
    'numberposts'   => -1
]);

if ( ovabrw_array_exists( $elementor_templates ) ) {
    foreach ( $elementor_templates as $template ) {
        $template_id    = $template->ID;
        $template_title = $template->post_title;

        $templates[$template_id] = $template_title;
    }
}

?>

<div class="ovabrw-product-template">
	<?php woocommerce_wp_select([
    	'id' 				=> $this->get_meta_name( 'product_template' ),
		'label' 			=> esc_html__( 'Product Template', 'ova-brw' ),
		'options' 			=> $templates,
		'value' 			=> $this->get_meta_value( 'product_template', 'global' ),
		'desc_tip'			=> true,
		'description' 		=> esc_html__( 'Classic/Modern or Other (made in Templates of Elementor )', 'ova-brw' ),
		'custom_attributes' => [
			'data-placeholder' => esc_html__( 'Select a template...', 'ova-brw' )
		]
    ]); ?>
</div>