<?php
/**
 * Smart Rentals WC Hooks class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Hooks' ) ) {

	class Smart_Rentals_WC_Hooks {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Remove default WooCommerce actions for rental products
			add_action( 'init', [ $this, 'rental_product_remove_actions' ] );

			// Product hooks
			add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_rental_badge' ], 5 );
			
			// Shop loop hooks
			add_action( 'woocommerce_after_shop_loop_item_title', [ $this, 'add_rental_badge_to_loop' ], 15 );
			
			// Single product hooks - following external plugin pattern
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_price' ], 9 );
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_booking_form' ], 25 );

			// Rental booking form components (following external plugin)
			add_action( 'smart_rentals_booking_form', [ $this, 'rental_booking_form_fields' ], 5 );
			add_action( 'smart_rentals_booking_form', [ $this, 'rental_booking_form_total' ], 30 );
		}

		/**
		 * Remove default WooCommerce actions for rental products
		 */
		public function rental_product_remove_actions() {
			// Remove default add to cart for rental products
			if ( is_product() ) {
				global $post;
				if ( $post && smart_rentals_wc_is_rental_product( $post->ID ) ) {
					remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				}
			}
		}

		/**
		 * Add rental badge to admin
		 */
		public function add_rental_badge() {
			global $post;
			
			if ( smart_rentals_wc_is_rental_product( $post->ID ) ) {
				echo '<div class="smart-rentals-admin-badge">';
				echo '<span class="rental-badge">' . __( 'Rental Product', 'smart-rentals-wc' ) . '</span>';
				echo '</div>';
			}
		}

		/**
		 * Add rental badge to shop loop
		 */
		public function add_rental_badge_to_loop() {
			global $product;
			
			if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				echo '<span class="smart-rentals-badge">' . __( 'Rental', 'smart-rentals-wc' ) . '</span>';
			}
		}

		/**
		 * Rental product price (following external plugin pattern)
		 */
		public function rental_product_price() {
			global $product;
			
			if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				// Show rental price instead of regular price
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
				
				$rental_type = smart_rentals_wc_get_post_meta( $product->get_id(), 'rental_type' );
				$daily_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'daily_price' );
				$hourly_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'hourly_price' );

				echo '<div class="smart-rentals-price-display">';
				
				if ( $rental_type && ( $daily_price || $hourly_price ) ) {
					echo '<p class="price">';
					
					switch ( $rental_type ) {
						case 'day':
						case 'hotel':
							if ( $daily_price > 0 ) {
								echo smart_rentals_wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
							}
							break;
						case 'hour':
						case 'appointment':
							if ( $hourly_price > 0 ) {
								echo smart_rentals_wc_price( $hourly_price ) . ' ' . __( 'per hour', 'smart-rentals-wc' );
							}
							break;
						case 'mixed':
							$price_parts = [];
							if ( $daily_price > 0 ) {
								$price_parts[] = smart_rentals_wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
							}
							if ( $hourly_price > 0 ) {
								$price_parts[] = smart_rentals_wc_price( $hourly_price ) . ' ' . __( 'per hour', 'smart-rentals-wc' );
							}
							if ( smart_rentals_wc_array_exists( $price_parts ) ) {
								echo implode( ' / ', $price_parts );
							}
							break;
					}
					
					echo '</p>';
				}
				
				echo '</div>';
			}
		}

		/**
		 * Rental product booking form (following external plugin pattern)
		 */
		public function rental_product_booking_form() {
			global $product;
			
			if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				// Show booking form (like external plugin shows it)
				smart_rentals_wc_get_template( 'single/booking-form.php', [
					'product_id' => $product->get_id()
				]);
			}
		}

		/**
		 * Rental booking form fields (following external plugin pattern)
		 */
		public function rental_booking_form_fields( $product_id ) {
			smart_rentals_wc_get_template( 'single/booking-form/fields.php', [
				'product_id' => $product_id
			]);
		}

		/**
		 * Rental booking form total (following external plugin pattern)
		 */
		public function rental_booking_form_total( $product_id ) {
			smart_rentals_wc_get_template( 'single/booking-form/total.php', [
				'product_id' => $product_id
			]);
		}
	}
}