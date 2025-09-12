<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

$img_url 		= $img_alt = '';
$img_id 		= $product->get_image_id();
$data_gallery 	= [];

if ( $img_id ) {
	$img_url = wp_get_attachment_url( $img_id );
	$img_alt = get_post_meta( $img_id, '_wp_attachment_image_alt', true );

	if ( ! $img_alt ) {
		$img_alt = get_the_title( $img_id );
	}
}

// Get gallery image
$gallery_ids = $product->get_gallery_image_ids();

// Carousel option
$carousel_options = apply_filters( OVABRW_PREFIX.'glb_carousel_2_options', [
	'items' 				=> 2.5,
	'items_tablet' 			=> 1.5,
	'items_mobile' 			=> 1,
	'slideBy' 				=> 1,
	'margin' 				=> 0,
	'autoWidth'				=> false,
	'autoplayHoverPause' 	=> true,
	'loop' 					=> true,
	'autoplay' 				=> true,
	'autoplayTimeout' 		=> 4500,
	'smartSpeed' 			=> 1500,
	'nav' 					=> true,
	'rtl' 					=> is_rtl() ? true: false
]);

if ( $img_url ): 
	array_push( $data_gallery, [
		'src' 		=> $img_url,
		'caption' 	=> $img_alt,
		'thumb' 	=> $img_url
	]);
?>
	<div class="ovabrw-product-images ovabrw-product-images-2">
		<?php if ( ovabrw_array_exists( $gallery_ids ) ): ?>
		<div class="gallery">
			<div class="product-gallery owl-carousel owl-theme" 
				data-options="<?php echo esc_attr( json_encode( $carousel_options ) ); ?>">
				<div class="gallery-item featured-img">
					<a class="gallery-fancybox" data-index='0' href="javascript:;">
						<img src="<?php echo esc_url( $img_url ); ?>" alt="<?php echo esc_attr( $img_alt ); ?>">
					</a>
				</div>
				<?php foreach( $gallery_ids as $k => $gallery_id ): 
					$gallery_url = wp_get_attachment_url( $gallery_id );
					$gallery_alt = get_post_meta( $gallery_id, '_wp_attachment_image_alt', true );

					if ( !$gallery_alt ) {
						$gallery_alt = get_the_title( $gallery_id );
					}

					array_push( $data_gallery, [
						'src' 		=> $gallery_url,
						'caption' 	=> $gallery_alt,
						'thumb' 	=> $gallery_url
					]);
				?>
					<div class="gallery-item">
						<a class="gallery-fancybox" data-index="<?php esc_attr_e( $k+1 ); ?>" href="javascript:;">
		  					<img src="<?php echo esc_url( $gallery_url ); ?>" alt="<?php echo esc_attr( $gallery_alt ); ?>">
		  				</a>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php endif; ?>
		<div class="data-gallery" data-gallery="<?php echo esc_attr( json_encode( $data_gallery ) ); ?>"></div>
	</div>
<?php endif; ?>
<div style="display: none !important;">
	<?php woocommerce_show_product_images(); // Woo prodduct images ?>
</div>