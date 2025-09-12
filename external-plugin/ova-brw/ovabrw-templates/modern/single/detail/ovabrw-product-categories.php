<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get category ids
$category_ids = $product->get_category_ids();

// Product category
if ( ovabrw_array_exists( $category_ids ) ): ?>
	<div class="ovabrw-product-categories">
		<span class="label"><?php esc_html_e( 'Categories:', 'ova-brw' ); ?></span>
		<?php foreach ( $category_ids as $k => $term_id ):
			$term = get_term( $term_id );

			if ( $term && is_object( $term ) ):
				$term_name = $term->name;
				$term_link = get_term_link( $term_id );
				$separator = '';

				if ( $k < count( $category_ids ) - 1 ) $separator = ', ';
		?>
			<span class="name">
				<a href="<?php echo esc_url( $term_link ); ?>">
					<?php echo esc_html( $term_name ).$separator; ?>
				</a>
			</span>
		<?php endif;
		endforeach; ?>
	</div>
<?php endif;

// Get tag ids
$tag_ids = $product->get_tag_ids();

// Product tags
if ( ovabrw_array_exists( $tag_ids ) ): ?>
	<div class="ovabrw-product-categories">
		<span class="label"><?php esc_html_e( 'Tags:', 'ova-brw' ); ?></span>
		<?php foreach ( $tag_ids as $k => $term_id ):
			$term = get_term( $term_id );

			if ( $term && is_object( $term ) ):
				$term_name = $term->name;
				$term_link = get_term_link( $term_id );
				$separator = '';

				if ( $k < count( $category_ids ) - 1 ) $separator = ', ';
		?>
			<span class="name">
				<a href="<?php echo esc_url( $term_link ); ?>">
					<?php echo esc_html( $term_name ).$separator; ?>
				</a>
			</span>
		<?php endif;
		endforeach; ?>
	</div>
<?php endif; ?>