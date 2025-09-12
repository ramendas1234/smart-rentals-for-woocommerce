<?php
/**
 * Smart Rentals WC Product class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Product' ) ) {

	class Smart_Rentals_WC_Product {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add rental product data to frontend
			add_action( 'wp_footer', [ $this, 'add_rental_product_data' ] );
		}

		/**
		 * Add rental product data to frontend
		 */
		public function add_rental_product_data() {
			if ( is_product() ) {
				global $post;
				
				if ( $post && smart_rentals_wc_is_rental_product( $post->ID ) ) {
					$rental_type = smart_rentals_wc_get_post_meta( $post->ID, 'rental_type' );
					$daily_price = smart_rentals_wc_get_post_meta( $post->ID, 'daily_price' );
					$hourly_price = smart_rentals_wc_get_post_meta( $post->ID, 'hourly_price' );
					$min_rental_period = smart_rentals_wc_get_post_meta( $post->ID, 'min_rental_period' );
					$max_rental_period = smart_rentals_wc_get_post_meta( $post->ID, 'max_rental_period' );

					?>
					<script type="text/javascript">
					window.smartRentalsProductData = {
						productId: <?php echo intval( $post->ID ); ?>,
						rentalType: '<?php echo esc_js( $rental_type ); ?>',
						dailyPrice: <?php echo floatval( $daily_price ); ?>,
						hourlyPrice: <?php echo floatval( $hourly_price ); ?>,
						minPeriod: <?php echo intval( $min_rental_period ); ?>,
						maxPeriod: <?php echo intval( $max_rental_period ); ?>,
						currencySymbol: '<?php echo function_exists( 'get_woocommerce_currency_symbol' ) ? esc_js( get_woocommerce_currency_symbol() ) : '$'; ?>'
					};
					</script>
					<?php
				}
			}
		}
	}

}