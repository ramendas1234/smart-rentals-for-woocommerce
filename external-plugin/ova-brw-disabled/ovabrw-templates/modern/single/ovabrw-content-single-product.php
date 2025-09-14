<?php if ( !defined( 'ABSPATH' ) ) exit();

global $product;

do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<div id="product-<?php the_ID(); ?>" class="product ovabrw-modern-product">
	<div class="ovabrw-modern-product-container">
		<div class="ovabrw-modern-product-left">
			<?php
			/**
			 * Hook: ovabrw_modern_product_left.
			 *
			 * @hooked ovabrw_modern_product_images - 10
			 * @hooked ovabrw_modern_product_table_price - 10
			 * @hooked ovabrw_modern_product_calendar - 10
			 */
			do_action( OVABRW_PREFIX.'modern_product_left' );
			?>
		</div>
		<div class="ovabrw-modern-product-right">
			<div class="ovabrw-modern-product-head">
				<div class="ovabrw-head-left">
					<?php do_action( OVABRW_PREFIX.'modern_product_title' ); ?>
					<?php do_action( OVABRW_PREFIX.'modern_product_review' ); ?>
				</div>
				<div class="ovabrw-head-right">
					<?php do_action( OVABRW_PREFIX.'modern_product_price' ); ?>
				</div>
			</div>
			<?php
			/**
			 * Hook: ovabrw_modern_product_right.
			 *
			 * @hooked ovabrw_modern_product_features - 10
			 * @hooked ovabrw_modern_product_categories - 10
			 * @hooked ovabrw_modern_product_attributes - 10
			 * @hooked ovabrw_modern_product_short_description - 10
			 * @hooked ovabrw_modern_product_form - 10
			 */
			do_action( OVABRW_PREFIX.'modern_product_right' );
			?>
		</div>
	</div>
	<?php
		/**
		 * Hook: woocommerce_after_single_product_summary.
		 *
		 * @hooked woocommerce_output_product_data_tabs - 10
		 * @hooked woocommerce_upsell_display - 15
		 * @hooked woocommerce_output_related_products - 20
		 */
		do_action( OVABRW_PREFIX.'modern_after_product' );
	?>
</div>
<?php do_action( 'woocommerce_after_single_product' ); ?>