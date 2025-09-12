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

			// Fix WooCommerce validation by setting product prices
			add_filter( 'woocommerce_product_get_price', [ $this, 'get_rental_product_price' ], 10, 2 );
			add_filter( 'woocommerce_product_get_regular_price', [ $this, 'get_rental_product_price' ], 10, 2 );
			add_filter( 'woocommerce_is_purchasable', [ $this, 'rental_product_purchasable' ], 10, 2 );

			// Ensure rental products have proper WooCommerce meta when viewed
			add_action( 'woocommerce_single_product_summary', [ $this, 'ensure_rental_product_meta' ], 1 );

			// Remove quantity field for rental products by making them sold individually
			add_filter( 'woocommerce_is_sold_individually', [ $this, 'rental_product_sold_individually' ], 10, 2 );
			
			// Handle cart quantity display for rental products
			add_filter( 'woocommerce_cart_item_quantity', [ $this, 'rental_cart_item_quantity' ], 10, 3 );
			add_filter( 'woocommerce_checkout_cart_item_quantity', [ $this, 'rental_checkout_cart_item_quantity' ], 10, 3 );
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

		/**
		 * Get rental product price for WooCommerce validation
		 */
		public function get_rental_product_price( $price, $product ) {
			if ( smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				// If no regular price is set, use rental price for WooCommerce validation
				if ( !$price || $price <= 0 ) {
					$rental_type = smart_rentals_wc_get_post_meta( $product->get_id(), 'rental_type' );
					$daily_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'daily_price' );
					$hourly_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'hourly_price' );

					// Use daily price as default, or hourly price if no daily price
					if ( $daily_price > 0 ) {
						return $daily_price;
					} elseif ( $hourly_price > 0 ) {
						return $hourly_price;
					}
				}
			}
			
			return $price;
		}

		/**
		 * Make rental products purchasable
		 */
		public function rental_product_purchasable( $purchasable, $product ) {
			if ( smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				// Rental products are always purchasable through our booking form
				return true;
			}
			
			return $purchasable;
		}

		/**
		 * Ensure rental product has proper WooCommerce meta
		 */
		public function ensure_rental_product_meta() {
			global $product;
			
			if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				$product_id = $product->get_id();
				
				// Check if WooCommerce price is set
				$wc_price = get_post_meta( $product_id, '_price', true );
				
				if ( !$wc_price || $wc_price <= 0 ) {
					// Set WooCommerce price from rental price
					$daily_price = smart_rentals_wc_get_post_meta( $product_id, 'daily_price' );
					$hourly_price = smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' );

					$price_to_set = 0;
					if ( $daily_price > 0 ) {
						$price_to_set = $daily_price;
					} elseif ( $hourly_price > 0 ) {
						$price_to_set = $hourly_price;
					}

					if ( $price_to_set > 0 ) {
						update_post_meta( $product_id, '_regular_price', $price_to_set );
						update_post_meta( $product_id, '_price', $price_to_set );
						update_post_meta( $product_id, '_virtual', 'yes' );
						update_post_meta( $product_id, '_stock_status', 'instock' );
						
						smart_rentals_wc_log( "Auto-set WooCommerce price for rental product {$product_id}: {$price_to_set}" );
					}
				}
			}
		}

		/**
		 * Make rental products sold individually (removes quantity field)
		 */
		public function rental_product_sold_individually( $sold_individually, $product ) {
			if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				return true; // This removes the quantity field from product page
			}
			return $sold_individually;
		}

		/**
		 * Handle cart quantity display for rental products
		 */
		public function rental_cart_item_quantity( $product_quantity, $cart_item_key, $cart_item ) {
			// Check if this is a rental product
			$product_id = isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;
			
			if ( $product_id && smart_rentals_wc_is_rental_product( $product_id ) ) {
				// For rental products, show static quantity without input field
				$quantity = isset( $cart_item['quantity'] ) ? $cart_item['quantity'] : 1;
				
				// Check if we have rental-specific quantity data
				if ( isset( $cart_item['rental_data'] ) ) {
					$rental_data = $cart_item['rental_data'];
					
					// Show rental duration instead of quantity for better UX
					if ( isset( $rental_data['duration_text'] ) && $rental_data['duration_text'] ) {
						return '<span class="rental-duration">' . esc_html( $rental_data['duration_text'] ) . '</span>';
					}
				}
				
				// Fallback: show static quantity
				return '<span class="rental-quantity">' . sprintf( __( 'Qty: %d', 'smart-rentals-wc' ), $quantity ) . '</span>';
			}
			
			return $product_quantity;
		}

		/**
		 * Handle checkout cart quantity display for rental products
		 */
		public function rental_checkout_cart_item_quantity( $product_quantity, $cart_item, $cart_item_key ) {
			// Check if this is a rental product
			$product_id = isset( $cart_item['product_id'] ) ? $cart_item['product_id'] : 0;
			
			if ( $product_id && smart_rentals_wc_is_rental_product( $product_id ) ) {
				// For rental products on checkout, show static quantity without input field
				$quantity = isset( $cart_item['quantity'] ) ? $cart_item['quantity'] : 1;
				
				// Check if we have rental-specific quantity data
				if ( isset( $cart_item['rental_data'] ) ) {
					$rental_data = $cart_item['rental_data'];
					
					// Show rental duration instead of quantity for better UX
					if ( isset( $rental_data['duration_text'] ) && $rental_data['duration_text'] ) {
						return '<span class="rental-duration">' . esc_html( $rental_data['duration_text'] ) . '</span>';
					}
				}
				
				// Fallback: show static quantity with x prefix like WooCommerce does
				return '<span class="rental-quantity">' . sprintf( __( '&times; %d', 'smart-rentals-wc' ), $quantity ) . '</span>';
			}
			
			return $product_quantity;
		}
	}
}