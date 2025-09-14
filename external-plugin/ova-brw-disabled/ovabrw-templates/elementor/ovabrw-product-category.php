<?php if ( !defined( 'ABSPATH' ) ) exit();

// Check term id
if ( !ovabrw_get_meta_data( 'term_id', $args ) ) return;

// Get thumbnail
$thumbnail_id 	= get_term_meta( $args['term_id'], 'thumbnail_id', true );
$thumbnail_url 	= isset( $args['image']['default'] ) ? $args['image']['default'] : '';
$thumbnail_alt 	= '';

if ( $thumbnail_id ) {
	$thumbnail_url 	= wp_get_attachment_url( $thumbnail_id );
	$thumbnail_alt 	= get_post_meta( $thumbnail_id , '_wp_attachment_image_alt', true );
}

if ( 'yes' === ovabrw_get_meta_data( 'custom_image', $args ) ) {
	$thumbnail_url 	= isset( $args['image']['url'] ) ? $args['image']['url'] : '';
	$thumbnail_alt 	= isset( $args['image']['alt'] ) ? $args['image']['alt'] : '';
}
if ( !$thumbnail_url ) {
	$thumbnail_url = isset( $args['image']['default'] ) ? $args['image']['default'] : '';
}

// Get term object
$term_obj 	= get_term( $args['term_id'], 'product_cat' );
$term_link 	= get_term_link( $args['term_id'] );

if ( !$term_obj ) {
	$args['term_id'] 	= get_option( 'default_product_cat' );
	$term_obj 	 		= get_term( $args['term_id'], 'product_cat' );
	$term_link 			= '';
}

// Target link
$target_link = ovabrw_get_meta_data( 'target_link', $args );

if ( $term_obj ): ?>
	<div class="ovabrw-el-product-category template1">
		<a href="<?php echo esc_url( $term_link ); ?>"<?php echo 'yes' == $target_link ? 'target="_blank"' : ''; ?>>
			<?php if ( 'yes' == ovabrw_get_meta_data( 'background_overlay', $args ) ): ?>
				<div class="background-overlay"></div>
			<?php endif; ?>
			<img src="<?php echo esc_url( $thumbnail_url ); ?>" alt="<?php echo esc_attr( $thumbnail_alt ); ?>">
			<div class="info">
				<?php if ( 'yes' == ovabrw_get_meta_data( 'show_name', $args ) ): ?>
					<h2 class="name">
						<?php echo esc_html( $term_obj->name ); ?>
					</h2>
				<?php endif; ?>
				<div class="extra">
					<?php if ( 'yes' == ovabrw_get_meta_data( 'show_count', $args ) ): ?>
						<?php if ( $term_obj->count == 1 ): ?>
							<span class="count">
								<?php echo sprintf( esc_html__( '%s'.' '.$args['text_count'] ), $term_obj->count ); ?>
							</span>
						<?php else: ?>
							<span class="count">
								<?php echo sprintf( esc_html__( '%s'.' '.$args['text_count_many'] ), $term_obj->count ); ?>
							</span>
						<?php endif; ?>
					<?php endif; ?>
					<?php if ( 'yes' == $args['show_review'] ):
						$review_average = OVABRW()->options->get_average_product_review_by_cagegory( $args['term_id'] );
					?>
						<span class="review-average">
							<i aria-hidden="true" class="brwicon-star-2"></i>
							<span class="average">
								<?php echo esc_html( $review_average ); ?>
							</span>
						</span>
					<?php endif; ?>
				</div>
			</div>
		</a>
	</div>
<?php endif;