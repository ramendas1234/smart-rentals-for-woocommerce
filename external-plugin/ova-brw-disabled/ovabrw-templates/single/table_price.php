<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Rental type
$rental_type = $product->get_rental_type();

if ( 'yes' === ovabrw_get_setting( 'template_show_open_table_price', 'yes' ) ) {
	$tab_show 		= '';
	$content_show 	= 'show';
} else {
	$tab_show 		= 'ovabrw-collapsed';
	$content_show 	= '';
}

if ( $product->price_list_available() ): ?>
	<div class="product_table_price">
		<!-- Period time -->
		<?php if ( 'period_time' === $rental_type ): ?>
			<div class="ovacrs_price_rent ovacrs_hourly_rent">
				<h3 class="ovabrw-according <?php echo esc_attr( $tab_show ); ?>" href="#" role="button" >
					<?php esc_html_e( 'Price Table & Discount', 'ova-brw' ); ?>
				</h3>
				<div class="ovabrw_collapse_content <?php echo esc_attr( $content_show ); ?>" >
					<?php do_action( OVABRW_PREFIX.'table_price_period_time', $product->get_id() ); ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- Hourly Rent -->
		<?php if ( 'hour' === $rental_type || 'mixed' === $rental_type ): ?>
			<div class="ovacrs_price_rent ovacrs_hourly_rent">
				<h3 class="ovabrw-according <?php echo esc_attr( $tab_show ); ?>" >
					<?php esc_html_e( 'Price Table - Hour', 'ova-brw' ); ?>
				</h3>
				<!-- Regular Price Hour -->
				<div class="ovabrw_collapse_content <?php echo esc_attr( $content_show ); ?>" >
					<!-- Global Discount -->
					<?php do_action( OVABRW_PREFIX.'table_price_global_discount_hour', $product->get_id() ); ?>
					<?php do_action( OVABRW_PREFIX.'table_price_seasons_hour', $product->get_id() ); ?>
				</div>
			</div>
		<?php endif; ?>

		<!-- Daily Rent -->
		<?php if ( 'day' === $rental_type || 'mixed' === $rental_type || 'hotel' === $rental_type ): ?>
			<div class="ovacrs_price_rent ovacrs_daily_rent">
				<h3 class="ovabrw-according <?php echo esc_attr( $tab_show ); ?>" >
					<?php esc_html_e( 'Price Table - Day', 'ova-brw' ); ?>
				</h3>
				<!-- Regular Price Hour -->
				<div class="ovabrw_collapse_content <?php echo esc_attr( $content_show ); ?>">
					<?php do_action( OVABRW_PREFIX.'table_price_weekdays', $product->get_id() ); ?>
					<?php do_action( OVABRW_PREFIX.'table_price_global_discount_day', $product->get_id() ); ?>
					<?php do_action( OVABRW_PREFIX.'table_price_seasons_day', $product->get_id() ); ?>
				</div>
			</div>
		<?php endif; ?>
	</div>
<?php endif; ?>