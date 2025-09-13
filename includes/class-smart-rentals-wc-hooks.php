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

			// Product hooks (rental badge removed - using checkbox instead)
			
			// Shop loop hooks
			add_action( 'woocommerce_after_shop_loop_item_title', [ $this, 'add_rental_badge_to_loop' ], 15 );
			
			// Single product hooks - following external plugin pattern
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_price' ], 9 );
			add_action( 'woocommerce_single_product_summary', [ $this, 'rental_product_booking_form' ], 25 );
			
			// Calendar display after product gallery (below gallery, not beside)
			add_action( 'woocommerce_after_single_product_summary', [ $this, 'rental_product_calendar' ], 5 );

			// Rental booking form components (following external plugin)
			add_action( 'smart_rentals_booking_form', [ $this, 'rental_booking_form_fields' ], 5 );
			add_action( 'smart_rentals_booking_form', [ $this, 'rental_booking_form_total' ], 30 );

			// Fix WooCommerce validation by setting product prices
			add_filter( 'woocommerce_product_get_price', [ $this, 'get_rental_product_price' ], 10, 2 );
			add_filter( 'woocommerce_product_get_regular_price', [ $this, 'get_rental_product_price' ], 10, 2 );
			add_filter( 'woocommerce_is_purchasable', [ $this, 'rental_product_purchasable' ], 10, 2 );

			// Ensure rental products have proper WooCommerce meta when viewed
			add_action( 'woocommerce_single_product_summary', [ $this, 'ensure_rental_product_meta' ], 1 );

			// Remove default WooCommerce quantity field and add to cart button from product page
			add_filter( 'woocommerce_is_sold_individually', [ $this, 'rental_product_sold_individually' ], 10, 2 );
			
			// Handle cart quantity display for rental products
			add_filter( 'woocommerce_cart_item_quantity', [ $this, 'rental_cart_item_quantity' ], 10, 3 );
			add_filter( 'woocommerce_checkout_cart_item_quantity', [ $this, 'rental_checkout_cart_item_quantity' ], 10, 3 );
		}

		/**
		 * Remove default WooCommerce actions for rental products
		 */
		public function rental_product_remove_actions() {
			// Remove default add to cart for rental products on single product pages
			add_action( 'wp', [ $this, 'maybe_remove_add_to_cart' ] );
			add_action( 'woocommerce_single_product_summary', [ $this, 'maybe_remove_add_to_cart' ], 1 );
			
			// Also add CSS to hide any remaining add to cart elements
			add_action( 'wp_head', [ $this, 'hide_rental_add_to_cart_css' ] );
		}

		/**
		 * Maybe remove add to cart button for rental products
		 */
		public function maybe_remove_add_to_cart() {
			// Only run on single product pages
			if ( !is_product() ) {
				return;
			}
			
			global $product, $post;
			
			// Get product ID safely
			$product_id = 0;
			if ( $product && is_object( $product ) && method_exists( $product, 'get_id' ) ) {
				$product_id = $product->get_id();
			} elseif ( $post && isset( $post->ID ) ) {
				$product_id = $post->ID;
			} elseif ( function_exists( 'get_the_ID' ) ) {
				$product_id = get_the_ID();
			}
			
			// If we couldn't get a product ID, return
			if ( !$product_id ) {
				return;
			}
			
			// Check if it's a rental product
			if ( smart_rentals_wc_is_rental_product( $product_id ) ) {
				// Remove all WooCommerce add to cart related actions
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
				remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
				remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
				remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
				remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
				
				// Debug log
				if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
					smart_rentals_wc_log( 'Removed WooCommerce add to cart actions for rental product: ' . $product_id );
				}
			}
		}

		/**
		 * Hide add to cart elements with CSS for rental products
		 */
		public function hide_rental_add_to_cart_css() {
			if ( !is_product() ) {
				return;
			}
			
			global $product, $post;
			
			// Get product ID safely
			$product_id = 0;
			if ( $product && is_object( $product ) && method_exists( $product, 'get_id' ) ) {
				$product_id = $product->get_id();
			} elseif ( $post && isset( $post->ID ) ) {
				$product_id = $post->ID;
			} elseif ( function_exists( 'get_the_ID' ) ) {
				$product_id = get_the_ID();
			}
			
			// If we couldn't get a product ID, return
			if ( !$product_id ) {
				return;
			}
			
			// Check if it's a rental product
			if ( smart_rentals_wc_is_rental_product( $product_id ) ) {
				?>
				<style type="text/css">
				/* Hide ONLY WooCommerce default add to cart elements (NOT our custom booking form) */
				.single-product .product form.cart:not(.smart-rentals-form),
				.single-product .product .single_add_to_cart_button:not(.smart-rentals-button),
				.single-product .product .quantity:not(.smart-rentals-quantity):not(#smart_rentals_quantity),
				.single-product .product .variations_form:not(.smart-rentals-form),
				.single-product .product .woocommerce-variation-add-to-cart:not(.smart-rentals-variation) {
					display: none !important;
				}
				
				/* Ensure our custom booking form elements remain visible */
				.smart-rentals-booking-form-container,
				.smart-rentals-booking-form-container .rental_item,
				.smart-rentals-booking-form-container #smart_rentals_quantity,
				.smart-rentals-booking-form-container .quantity,
				.smart-rentals-booking-form-container .form-control {
					display: block !important;
					visibility: visible !important;
				}
				</style>
				<?php
			}
		}

		/**
		 * Add rental badge to admin (REMOVED - using checkbox instead)
		 */
		public function add_rental_badge() {
			// This method is no longer used - rental status is shown via checkbox
			// Keeping for backward compatibility
			return;
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
		 * Make rental products sold individually (removes default WC quantity field and add to cart button)
		 */
		public function rental_product_sold_individually( $sold_individually, $product ) {
			if ( $product && smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				return true; // This removes default WC quantity field and add to cart button from product page
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
				// For rental products, show disabled quantity field (read-only)
				$quantity = isset( $cart_item['quantity'] ) ? $cart_item['quantity'] : 1;
				
				// Create disabled quantity input that matches WooCommerce styling but is read-only
				$quantity_html = sprintf(
					'<div class="quantity">
						<input type="number" class="input-text qty text rental-qty-disabled" value="%d" min="1" readonly disabled title="%s" />
					</div>',
					$quantity,
					__( 'Quantity cannot be changed for rental products. Remove and re-book to change quantity.', 'smart-rentals-wc' )
				);
				
				// Add rental duration info below the quantity field
				if ( isset( $cart_item['rental_data'] ) ) {
					$rental_data = $cart_item['rental_data'];
					
					if ( isset( $rental_data['duration_text'] ) && $rental_data['duration_text'] ) {
						$quantity_html .= '<br><small class="rental-duration" style="color: #666; font-style: italic;">' . esc_html( $rental_data['duration_text'] ) . '</small>';
					}
				}
				
				return $quantity_html;
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
				// For rental products on checkout, show quantity with rental duration info
				$quantity = isset( $cart_item['quantity'] ) ? $cart_item['quantity'] : 1;
				
				// Start with standard quantity display
				$quantity_display = sprintf( __( '&times; %d', 'smart-rentals-wc' ), $quantity );
				
				// Check if we have rental-specific duration data
				if ( isset( $cart_item['rental_data'] ) ) {
					$rental_data = $cart_item['rental_data'];
					
					// Add rental duration info below quantity
					if ( isset( $rental_data['duration_text'] ) && $rental_data['duration_text'] ) {
						$quantity_display .= '<br><small class="rental-duration">' . esc_html( $rental_data['duration_text'] ) . '</small>';
					}
				}
				
				return $quantity_display;
			}
			
			return $product_quantity;
		}

		/**
		 * Display rental product availability calendar
		 */
		public function rental_product_calendar() {
			if ( !is_product() ) {
				return;
			}

			global $post;
			if ( !$post || !smart_rentals_wc_is_rental_product( $post->ID ) ) {
				return;
			}

			// Check if calendar is enabled for this product
			$show_calendar = smart_rentals_wc_get_post_meta( $post->ID, 'show_calendar' );
			if ( 'yes' !== $show_calendar ) {
				return;
			}

			// Include the calendar template
			$template_path = SMART_RENTALS_WC_PLUGIN_TEMPLATES . 'single/calendar.php';
			if ( file_exists( $template_path ) ) {
				$product_id = $post->ID;
				include $template_path;
			}
		}
	}
}