<?php
/**
 * Smart Rentals WC Shortcodes class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Shortcodes' ) ) {

	class Smart_Rentals_WC_Shortcodes {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_shortcode( 'smart_rentals_products', [ $this, 'rental_products' ] );
			add_shortcode( 'smart_rentals_search', [ $this, 'rental_search' ] );
			add_shortcode( 'smart_rentals_calendar', [ $this, 'rental_calendar' ] );
		}

		/**
		 * Rental products shortcode
		 */
		public function rental_products( $atts ) {
			$atts = shortcode_atts([
				'limit' => 12,
				'columns' => 4,
				'orderby' => 'date',
				'order' => 'DESC',
				'rental_type' => '',
			], $atts );

			$args = [
				'post_type' => 'product',
				'posts_per_page' => intval( $atts['limit'] ),
				'orderby' => sanitize_text_field( $atts['orderby'] ),
				'order' => sanitize_text_field( $atts['order'] ),
				'meta_query' => [
					[
						'key' => smart_rentals_wc_meta_key( 'enable_rental' ),
						'value' => 'yes',
						'compare' => '='
					]
				]
			];

			if ( $atts['rental_type'] ) {
				$args['meta_query'][] = [
					'key' => smart_rentals_wc_meta_key( 'rental_type' ),
					'value' => sanitize_text_field( $atts['rental_type'] ),
					'compare' => '='
				];
			}

			$products = new WP_Query( $args );

			if ( !$products->have_posts() ) {
				return '<p>' . __( 'No rental products found.', 'smart-rentals-wc' ) . '</p>';
			}

			ob_start();
			?>
			<div class="smart-rentals-products woocommerce columns-<?php echo esc_attr( $atts['columns'] ); ?>">
				<ul class="products">
					<?php while ( $products->have_posts() ) : $products->the_post(); ?>
						<?php wc_get_template_part( 'content', 'product' ); ?>
					<?php endwhile; ?>
				</ul>
			</div>
			<?php
			wp_reset_postdata();
			
			return ob_get_clean();
		}

		/**
		 * Rental search shortcode
		 */
		public function rental_search( $atts ) {
			$atts = shortcode_atts([
				'show_location' => 'yes',
				'show_dates' => 'yes',
				'show_times' => 'no',
				'show_quantity' => 'yes',
			], $atts );

			ob_start();
			?>
			<div class="smart-rentals-search-form">
				<form method="get" action="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>">
					<div class="search-fields">
						<?php if ( 'yes' === $atts['show_dates'] ) : ?>
						<div class="search-field">
							<label for="pickup_date"><?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?></label>
							<input type="date" name="pickup_date" id="pickup_date" value="<?php echo esc_attr( $_GET['pickup_date'] ?? '' ); ?>" />
						</div>
						
						<div class="search-field">
							<label for="dropoff_date"><?php _e( 'Drop-off Date', 'smart-rentals-wc' ); ?></label>
							<input type="date" name="dropoff_date" id="dropoff_date" value="<?php echo esc_attr( $_GET['dropoff_date'] ?? '' ); ?>" />
						</div>
						<?php endif; ?>

						<?php if ( 'yes' === $atts['show_times'] ) : ?>
						<div class="search-field">
							<label for="pickup_time"><?php _e( 'Pickup Time', 'smart-rentals-wc' ); ?></label>
							<input type="time" name="pickup_time" id="pickup_time" value="<?php echo esc_attr( $_GET['pickup_time'] ?? '' ); ?>" />
						</div>
						
						<div class="search-field">
							<label for="dropoff_time"><?php _e( 'Drop-off Time', 'smart-rentals-wc' ); ?></label>
							<input type="time" name="dropoff_time" id="dropoff_time" value="<?php echo esc_attr( $_GET['dropoff_time'] ?? '' ); ?>" />
						</div>
						<?php endif; ?>

						<?php if ( 'yes' === $atts['show_location'] ) : ?>
						<div class="search-field">
							<label for="location"><?php _e( 'Location', 'smart-rentals-wc' ); ?></label>
							<input type="text" name="location" id="location" placeholder="<?php _e( 'Enter location', 'smart-rentals-wc' ); ?>" value="<?php echo esc_attr( $_GET['location'] ?? '' ); ?>" />
						</div>
						<?php endif; ?>

						<?php if ( 'yes' === $atts['show_quantity'] ) : ?>
						<div class="search-field">
							<label for="quantity"><?php _e( 'Quantity', 'smart-rentals-wc' ); ?></label>
							<input type="number" name="quantity" id="quantity" min="1" value="<?php echo esc_attr( $_GET['quantity'] ?? '1' ); ?>" />
						</div>
						<?php endif; ?>

						<div class="search-field">
							<input type="submit" value="<?php _e( 'Search Rentals', 'smart-rentals-wc' ); ?>" class="button" />
						</div>
					</div>
					<input type="hidden" name="rental_search" value="1" />
				</form>
			</div>
			<?php
			return ob_get_clean();
		}

		/**
		 * Rental calendar shortcode
		 */
		public function rental_calendar( $atts ) {
			$atts = shortcode_atts([
				'product_id' => 0,
				'height' => '600px',
			], $atts );

			if ( !$atts['product_id'] ) {
				return '<p>' . __( 'Product ID is required for calendar shortcode.', 'smart-rentals-wc' ) . '</p>';
			}

			if ( !smart_rentals_wc_is_rental_product( $atts['product_id'] ) ) {
				return '<p>' . __( 'Invalid rental product ID.', 'smart-rentals-wc' ) . '</p>';
			}

			ob_start();
			?>
			<div class="smart-rentals-calendar-shortcode" style="height: <?php echo esc_attr( $atts['height'] ); ?>;">
				<div id="smart-rentals-calendar-<?php echo esc_attr( $atts['product_id'] ); ?>" class="smart-rentals-calendar" data-product-id="<?php echo esc_attr( $atts['product_id'] ); ?>">
					<?php _e( 'Loading calendar...', 'smart-rentals-wc' ); ?>
				</div>
			</div>
			<?php
			return ob_get_clean();
		}
	}

}