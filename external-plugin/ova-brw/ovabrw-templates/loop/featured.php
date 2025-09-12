<?php if ( !defined( 'ABSPATH' ) ) exit();
/**
 * The template for displaying featured content within loop
 *
 * This template can be overridden by copying it to yourtheme/ovabrw-templates/loop/featured.php
 *
 */

global $product;

// if the product type isn't ovabrw_car_rental
if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) return;

$icons          = $product->get_meta_value( 'features_icons' );
$labels         = $product->get_meta_value( 'features_label' );
$descs          = $product->get_meta_value( 'features_desc' );
$show_in_cat    = $product->get_meta_value( 'features_special' );

if ( ovabrw_array_exists( $descs ) ):
    $d = 0;
?>
    <ul class="ovabrw-features">
        <?php foreach ( $descs as $k => $desc ):
            if ( 'yes' != ovabrw_get_meta_data( $k, $show_in_cat ) || !$desc ) continue;

            $icon   = ovabrw_get_meta_data( $k, $icons );
            $label  = ovabrw_get_meta_data( $k, $labels );
            $class  = ( $d%2 ) ? 'eve' : 'odd';
        ?>
            <li class="feature-item <?php echo esc_attr( $class ); ?>">
                <?php if ( apply_filters( OVABRW_PREFIX.'show_features_icon', true ) && $icon ): ?>
                    <i class="<?php echo esc_attr( $icon ); ?>"></i>
                <?php endif;

                if ( apply_filters( OVABRW_PREFIX.'show_features_label', false ) ): ?>    
                    <span class="label"><?php echo esc_html( $label ); ?></span>
                <?php endif;

                if ( apply_filters( OVABRW_PREFIX.'show_features_desc', true ) ): ?>    
                    <span class="desc"><?php echo esc_html( $desc ); ?></span>
                <?php endif; ?>
            </li>
        <?php $d++;
        endforeach; ?>
    </ul>
<?php endif;