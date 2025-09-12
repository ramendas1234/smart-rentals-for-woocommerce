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
			// Add rental product class
			add_filter( 'woocommerce_product_class', [ $this, 'product_class' ], 10, 4 );
			
			// Modify product type for rental products
			add_filter( 'woocommerce_product_get_type', [ $this, 'get_product_type' ], 10, 2 );
			
			// Add rental product data to frontend
			add_action( 'wp_footer', [ $this, 'add_rental_product_data' ] );
		}

		/**
		 * Product class filter
		 */
		public function product_class( $classname, $product_type, $post_type, $product_id ) {
			if ( smart_rentals_wc_is_rental_product( $product_id ) ) {
				return 'Smart_Rentals_WC_Product_Rental';
			}
			
			return $classname;
		}

		/**
		 * Get product type
		 */
		public function get_product_type( $type, $product ) {
			if ( smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				return 'rental';
			}
			
			return $type;
		}

		/**
		 * Add rental product data to frontend
		 */
		public function add_rental_product_data() {
			if ( is_product() ) {
				global $post;
				
				if ( smart_rentals_wc_is_rental_product( $post->ID ) ) {
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
						currencySymbol: '<?php echo esc_js( get_woocommerce_currency_symbol() ); ?>'
					};
					</script>
					<?php
				}
			}
		}
	}

	new Smart_Rentals_WC_Product();
}

/**
 * Rental Product Class
 */
if ( !class_exists( 'Smart_Rentals_WC_Product_Rental' ) ) {
	
	class Smart_Rentals_WC_Product_Rental extends WC_Product {
		
		/**
		 * Get product type
		 */
		public function get_type() {
			return 'rental';
		}

		/**
		 * Check if product is virtual
		 */
		public function is_virtual() {
			return true; // Rental products are typically virtual
		}

		/**
		 * Check if product is downloadable
		 */
		public function is_downloadable() {
			return false;
		}

		/**
		 * Check if product needs shipping
		 */
		public function needs_shipping() {
			return false; // Most rental products don't need shipping
		}

		/**
		 * Get rental type
		 */
		public function get_rental_type() {
			return smart_rentals_wc_get_post_meta( $this->get_id(), 'rental_type' );
		}

		/**
		 * Get daily price
		 */
		public function get_daily_price() {
			return smart_rentals_wc_get_post_meta( $this->get_id(), 'daily_price' );
		}

		/**
		 * Get hourly price
		 */
		public function get_hourly_price() {
			return smart_rentals_wc_get_post_meta( $this->get_id(), 'hourly_price' );
		}

		/**
		 * Get rental stock
		 */
		public function get_rental_stock() {
			return smart_rentals_wc_get_post_meta( $this->get_id(), 'rental_stock' );
		}

		/**
		 * Get security deposit
		 */
		public function get_security_deposit() {
			return smart_rentals_wc_get_post_meta( $this->get_id(), 'security_deposit' );
		}

		/**
		 * Check if calendar is enabled
		 */
		public function is_calendar_enabled() {
			return 'yes' === smart_rentals_wc_get_post_meta( $this->get_id(), 'enable_calendar' );
		}

		/**
		 * Calculate rental price
		 */
		public function calculate_rental_price( $pickup_date, $dropoff_date, $quantity = 1 ) {
			return Smart_Rentals_WC()->options->calculate_rental_price( 
				$this->get_id(), 
				$pickup_date, 
				$dropoff_date, 
				$quantity 
			);
		}

		/**
		 * Check availability
		 */
		public function check_availability( $pickup_date, $dropoff_date, $quantity = 1 ) {
			return Smart_Rentals_WC()->options->check_availability( 
				$this->get_id(), 
				$pickup_date, 
				$dropoff_date, 
				$quantity 
			);
		}
	}
}