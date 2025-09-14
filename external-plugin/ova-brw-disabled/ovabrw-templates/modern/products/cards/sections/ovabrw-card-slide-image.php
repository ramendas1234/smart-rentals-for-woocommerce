<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get rental product
$rental_product = OVABRW()->rental->get_rental_product( $product->get_id() );
if ( !$rental_product ) return;

// Product URL
$product_url = $rental_product->get_permalink();

// Get all images product
$image_ids 		= $product->get_all_image_ids();
$data_gallery 	= [];

// Get data options
$data_options = [];
if ( isset( $args['data_options'] ) ) {
    $data_options = $args['data_options'];
} else {
    $data_options   = apply_filters( OVABRW_PREFIX.'product_slideshow_options', [
    	'items'                 => 1,
        'slideBy'               => 1,
        'margin'                => 1,
        'autoplayHoverPause'    => true,
        'loop'                  => true,
        'autoplay'              => false,
        'autoplayTimeout'       => 3000,
        'smartSpeed'            => 500,
        'autoWidth'             => false,
        'center'                => false,
        'lazyLoad'              => true,
        'dots'                  => true,
        'dotsEach'              => apply_filters( OVABRW_PREFIX.'owlCarousel_dotsEach', 1 ),
        'nav'                   => true,
        'rtl'                   => is_rtl() ? true: false,
        'nav_left'              => 'brwicon-left',
        'nav_right'             => 'brwicon-right-1',
        'responsive' 			=> [
        	'0' => [
        		'items' 	=> 1,
        		'nav' 		=> false,
        		'slideBy' 	=> 1
        	],
        	'769' => [
        		'items' 	=> 1,
        		'nav' 		=> true,
        		'slideBy' 	=> 1
        	]
        ]
    ]);
}

// Get cart template
$card = ovabrw_get_meta_data( 'card_template', $args, ovabrw_get_card_template() );

// Thumbnail type
$thumbnail_type = ovabrw_get_meta_data( 'thumbnail_type', $args, ovabrw_get_option( 'glb_'.$card.'_thumbnail_type', 'slider' ) );

// is slide
$is_slide = false;
if ( 'slider' === $thumbnail_type ) $is_slide = true;

// Thumbnail size
$thumbnail_size = ovabrw_get_option( 'glb_'.$card.'_thumbnail_size', 'woocommerce_thumbnail' );

if ( ovabrw_array_exists( $image_ids ) ): ?>
    <div class="ovabrw-gallery-popup">
    	<?php if ( $is_slide ): ?>
	        <div class="ovabrw-gallery-slideshow owl-carousel owl-theme" data-options="<?php echo esc_attr( json_encode( $data_options ) ); ?>">
	        	<?php if ( 1 === count( $image_ids ) ):
	        		$img_alt = trim( strip_tags( get_post_meta( $image_ids[0], '_wp_attachment_image_alt', true ) ) );
	        	?>
	        		<div class="item">
	        			<a href="<?php echo esc_url( $product_url ); ?>">
	        				<?php if ( $thumbnail_size === 'custom_height' ): ?>
                        		<img src="<?php echo esc_url( wp_get_attachment_image_url( $image_ids[0], 'full' ) ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" class="ovabrw-card-height">
	                        <?php else: ?>
	                        	<img src="<?php echo esc_url( wp_get_attachment_image_url( $image_ids[0], $thumbnail_size ) ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
	                        <?php endif; ?>
	        			</a>
	        		</div>
	        	<?php else:
	        		foreach ( $image_ids as $k => $img_id ):
		                $gallery_url = wp_get_attachment_url( $img_id );
		                $gallery_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

		                if ( !$gallery_alt ) {
		                    $gallery_alt = get_the_title( $img_id );
		                }

		                array_push( $data_gallery, [
		                	'src'       => $gallery_url,
		                    'caption'   => $gallery_alt,
		                    'thumb'     => $gallery_url
		                ]);

		                $img_alt = trim( strip_tags( get_post_meta( $img_id, '_wp_attachment_image_alt', true ) ) );
		            ?>
		                <div class="item">
		                    <a class="gallery-fancybox" data-index="<?php echo esc_attr( $k ); ?>" href="javascript:void(0)">
		                        <?php if ( $thumbnail_size === 'custom_height' ): ?>
	                        		<img src="<?php echo esc_url( wp_get_attachment_image_url( $img_id, 'full' ) ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" class="ovabrw-card-height">
		                        <?php else: ?>
		                        	<img src="<?php echo esc_url( wp_get_attachment_image_url( $img_id, $thumbnail_size ) ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
		                        <?php endif; ?>
		                    </a>
		                </div>
		            <?php endforeach;
		        endif; ?>
	        </div>
	        <input
	        	type="hidden"
	        	class="ovabrw-data-gallery"
	        	data-gallery="<?php echo esc_attr( json_encode( $data_gallery ) ); ?>"
	        />
	    <?php else: ?>
	    	<div class="ovabrw-product-img-feature">
	    		<a href="<?php echo esc_url( $product_url ); ?>" class="ovabrw-product-link">
		    		<?php $img_feature = reset( $image_ids );
	    			$img_alt = trim( strip_tags( get_post_meta( $img_feature, '_wp_attachment_image_alt', true ) ) );

	            	if ( 'custom_height' === $thumbnail_size ): ?>
	            		<img src="<?php echo esc_url( wp_get_attachment_image_url( $img_feature, 'full' ) ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>" class="ovabrw-card-height">
	            	<?php else: ?>
	            		<img src="<?php echo esc_url( wp_get_attachment_image_url( $img_feature, $thumbnail_size ) ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
	            	<?php endif; ?>
            	</a>
	    	</div>
	    <?php endif; ?>
    </div>
<?php endif; ?>
<div style="display: none !important;">
	<?php woocommerce_show_product_images(); // Woo prodduct images ?>
</div>