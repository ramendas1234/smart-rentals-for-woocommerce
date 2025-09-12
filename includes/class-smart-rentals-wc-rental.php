<?php
/**
 * Smart Rentals WC Rental class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Rental' ) ) {

	class Smart_Rentals_WC_Rental {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Modify product price display
			add_filter( 'woocommerce_get_price_html', [ $this, 'get_rental_price_html' ], 11, 2 );
		}



		/**
		 * Get rental price HTML
		 */
		public function get_rental_price_html( $price, $product ) {
			if ( !smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				return $price;
			}

			$rental_type = smart_rentals_wc_get_post_meta( $product->get_id(), 'rental_type' );
			$daily_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'daily_price' );
			$hourly_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'hourly_price' );

			$rental_price = '';

			switch ( $rental_type ) {
				case 'day':
				case 'hotel':
					if ( $daily_price > 0 ) {
						$rental_price = wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
					}
					break;

				case 'hour':
				case 'appointment':
					if ( $hourly_price > 0 ) {
						$rental_price = wc_price( $hourly_price ) . ' ' . __( 'per hour', 'smart-rentals-wc' );
					}
					break;

				case 'mixed':
					$price_parts = [];
					if ( $daily_price > 0 ) {
						$price_parts[] = wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
					}
					if ( $hourly_price > 0 ) {
						$price_parts[] = wc_price( $hourly_price ) . ' ' . __( 'per hour', 'smart-rentals-wc' );
					}
					if ( smart_rentals_wc_array_exists( $price_parts ) ) {
						$rental_price = implode( ' / ', $price_parts );
					}
					break;

				default:
					if ( $daily_price > 0 ) {
						$rental_price = wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
					}
					break;
			}

			if ( $rental_price ) {
				return '<span class="smart-rentals-price">' . $rental_price . '</span>';
			}

			return $price;
		}

		/**
		 * Add rental info to product summary
		 */
		public function add_rental_info() {
			global $product;

			if ( !smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				return;
			}

			$rental_stock = smart_rentals_wc_get_post_meta( $product->get_id(), 'rental_stock' );
			$security_deposit = smart_rentals_wc_get_post_meta( $product->get_id(), 'security_deposit' );

			?>
			<div class="smart-rentals-info">
				<?php if ( $rental_stock ) : ?>
					<p class="rental-availability">
						<strong><?php _e( 'Availability:', 'smart-rentals-wc' ); ?></strong>
						<?php printf( _n( '%d item available', '%d items available', $rental_stock, 'smart-rentals-wc' ), $rental_stock ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $security_deposit > 0 ) : ?>
					<p class="security-deposit">
						<strong><?php _e( 'Security Deposit:', 'smart-rentals-wc' ); ?></strong>
						<?php echo wc_price( $security_deposit ); ?>
						<small><?php _e( '(refundable)', 'smart-rentals-wc' ); ?></small>
					</p>
				<?php endif; ?>
			</div>
			<?php
		}

		/**
		 * Get rental products
		 */
		public function get_rental_products( $args = [] ) {
			$default_args = [
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'meta_query' => [
					[
						'key' => smart_rentals_wc_meta_key( 'enable_rental' ),
						'value' => 'yes',
						'compare' => '='
					]
				]
			];

			$args = wp_parse_args( $args, $default_args );

			return get_posts( $args );
		}

		/**
		 * Check if product can be rented
		 */
		public function can_be_rented( $product_id, $pickup_date = null, $dropoff_date = null, $quantity = 1 ) {
			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				return false;
			}

			// If no dates provided, just check if rental is enabled
			if ( !$pickup_date || !$dropoff_date ) {
				return true;
			}

			// Check availability
			return Smart_Rentals_WC()->options->check_availability( $product_id, $pickup_date, $dropoff_date, $quantity );
		}

		/**
		 * Get rental types
		 */
		public function get_rental_types() {
			return apply_filters( 'smart_rentals_wc_rental_types', [
				'day' => __( 'Daily', 'smart-rentals-wc' ),
				'hour' => __( 'Hourly', 'smart-rentals-wc' ),
				'mixed' => __( 'Mixed (Daily/Hourly)', 'smart-rentals-wc' ),
				'period_time' => __( 'Package/Period', 'smart-rentals-wc' ),
				'transportation' => __( 'Transportation', 'smart-rentals-wc' ),
				'hotel' => __( 'Hotel/Accommodation', 'smart-rentals-wc' ),
				'appointment' => __( 'Appointment', 'smart-rentals-wc' ),
				'taxi' => __( 'Taxi/Distance', 'smart-rentals-wc' ),
			]);
		}

		/**
		 * Main Smart_Rentals_WC_Rental Instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
}