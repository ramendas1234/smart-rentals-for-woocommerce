<?php if ( !defined( 'ABSPATH' ) ) exit();

// Product Detail
add_action( 'ovabrw_modern_product_left', 'ovabrw_modern_product_images', 10 );
add_action( 'ovabrw_modern_product_left', 'ovabrw_modern_product_table_price', 15 );
add_action( 'ovabrw_modern_product_left', 'ovabrw_modern_product_unavailabel', 15 );
add_action( 'ovabrw_modern_product_left', 'ovabrw_modern_product_calendar', 15 );

add_action( 'ovabrw_modern_product_title', 'ovabrw_modern_product_title', 10 );
add_action( 'ovabrw_modern_product_review', 'ovabrw_modern_product_review', 10 );
add_action( 'ovabrw_modern_product_price', 'ovabrw_modern_product_price', 10 );
add_action( 'ovabrw_modern_product_sticky', 'ovabrw_modern_product_sticky', 10 );

add_action( 'ovabrw_modern_product_right', 'ovabrw_modern_product_specifications', 10 );
add_action( 'ovabrw_modern_product_right', 'ovabrw_modern_product_features', 10 );
add_action( 'ovabrw_modern_product_right', 'ovabrw_modern_product_categories', 10 );
add_action( 'ovabrw_modern_product_right', 'ovabrw_modern_product_custom_taxonomy', 10 );
add_action( 'ovabrw_modern_product_right', 'ovabrw_modern_product_attributes', 10 );
add_action( 'ovabrw_modern_product_right', 'ovabrw_modern_product_short_description', 10 );
add_action( 'ovabrw_modern_product_right', 'ovabrw_modern_product_forms', 10 );

add_action( 'ovabrw_modern_after_product', 'woocommerce_output_product_data_tabs', 10 );
add_action( 'ovabrw_modern_after_product', 'ovabrw_modern_product_related', 20 );
// End