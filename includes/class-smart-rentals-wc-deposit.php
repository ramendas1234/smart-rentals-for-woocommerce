<?php
/**
 * Smart Rentals WC Deposit class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Deposit' ) ) {

	class Smart_Rentals_WC_Deposit {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add deposit fields to product
			add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_deposit_fields' ], 25 );
			
			// Save deposit fields
			add_action( 'woocommerce_process_product_meta', [ $this, 'save_deposit_fields' ], 15 );
			
			// Handle deposits in cart
			add_action( 'woocommerce_before_calculate_totals', [ $this, 'handle_deposits_in_cart' ], 20 );
		}

		/**
		 * Add deposit fields to product
		 */
		public function add_deposit_fields() {
			global $post;

			if ( !smart_rentals_wc_is_rental_product( $post->ID ) ) {
				return;
			}

			echo '<div class="smart-rentals-deposit-fields" style="display: none;">';
			echo '<h4>' . __( 'Deposit Settings', 'smart-rentals-wc' ) . '</h4>';

			// Enable deposits
			woocommerce_wp_checkbox([
				'id' => smart_rentals_wc_meta_key( 'enable_deposits' ),
				'label' => __( 'Enable Deposits', 'smart-rentals-wc' ),
				'description' => __( 'Allow customers to pay a deposit instead of full amount', 'smart-rentals-wc' ),
			]);

			// Deposit type
			woocommerce_wp_select([
				'id' => smart_rentals_wc_meta_key( 'deposit_type' ),
				'label' => __( 'Deposit Type', 'smart-rentals-wc' ),
				'options' => [
					'fixed' => __( 'Fixed Amount', 'smart-rentals-wc' ),
					'percentage' => __( 'Percentage', 'smart-rentals-wc' ),
				],
			]);

			// Deposit amount
			woocommerce_wp_text_input([
				'id' => smart_rentals_wc_meta_key( 'deposit_amount' ),
				'label' => __( 'Deposit Amount', 'smart-rentals-wc' ),
				'description' => __( 'Fixed amount or percentage (without % symbol)', 'smart-rentals-wc' ),
				'type' => 'number',
				'custom_attributes' => [
					'step' => '0.01',
					'min' => '0',
				],
			]);

			echo '</div>';
		}

		/**
		 * Save deposit fields
		 */
		public function save_deposit_fields( $post_id ) {
			if ( !smart_rentals_wc_is_rental_product( $post_id ) ) {
				return;
			}

			$fields = [
				'enable_deposits' => 'checkbox',
				'deposit_type' => 'text',
				'deposit_amount' => 'price',
			];

			foreach ( $fields as $field => $type ) {
				$meta_key = smart_rentals_wc_meta_key( $field );
				
				if ( isset( $_POST[$meta_key] ) ) {
					$value = $_POST[$meta_key];
					
					switch ( $type ) {
						case 'price':
							$value = smart_rentals_wc_format_price( $value );
							break;
						case 'checkbox':
							$value = 'yes';
							break;
						default:
							$value = sanitize_text_field( $value );
							break;
					}
					
					smart_rentals_wc_update_post_meta( $post_id, $field, $value );
				} elseif ( 'checkbox' === $type ) {
					smart_rentals_wc_update_post_meta( $post_id, $field, 'no' );
				}
			}
		}

		/**
		 * Handle deposits in cart
		 */
		public function handle_deposits_in_cart( $cart ) {
			if ( is_admin() && !defined( 'DOING_AJAX' ) ) {
				return;
			}

			foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
				if ( !isset( $cart_item['rental_data'] ) ) {
					continue;
				}

				$product_id = $cart_item['rental_data']['product_id'];
				
				if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
					continue;
				}

				$enable_deposits = smart_rentals_wc_get_post_meta( $product_id, 'enable_deposits' );
				
				if ( 'yes' !== $enable_deposits ) {
					continue;
				}

				// Check if customer chose deposit option
				$use_deposit = isset( $_POST['use_deposit_' . $product_id] ) ? $_POST['use_deposit_' . $product_id] : '';
				
				if ( 'yes' !== $use_deposit ) {
					continue;
				}

				$deposit_type = smart_rentals_wc_get_post_meta( $product_id, 'deposit_type' );
				$deposit_amount = smart_rentals_wc_get_post_meta( $product_id, 'deposit_amount' );

				if ( !$deposit_amount ) {
					continue;
				}

				$original_price = $cart_item['data']->get_price();
				$deposit_price = 0;

				if ( 'fixed' === $deposit_type ) {
					$deposit_price = $deposit_amount;
				} elseif ( 'percentage' === $deposit_type ) {
					$deposit_price = ( $original_price * $deposit_amount ) / 100;
				}

				if ( $deposit_price > 0 && $deposit_price < $original_price ) {
					$cart_item['data']->set_price( $deposit_price );
					
					// Store deposit information
					$cart_item['rental_data']['is_deposit'] = true;
					$cart_item['rental_data']['deposit_amount'] = $deposit_price;
					$cart_item['rental_data']['remaining_amount'] = $original_price - $deposit_price;
					$cart_item['rental_data']['full_amount'] = $original_price;
				}
			}
		}

		/**
		 * Calculate deposit amount
		 */
		public function calculate_deposit( $product_id, $total_amount ) {
			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				return 0;
			}

			$enable_deposits = smart_rentals_wc_get_post_meta( $product_id, 'enable_deposits' );
			
			if ( 'yes' !== $enable_deposits ) {
				return 0;
			}

			$deposit_type = smart_rentals_wc_get_post_meta( $product_id, 'deposit_type' );
			$deposit_amount = smart_rentals_wc_get_post_meta( $product_id, 'deposit_amount' );

			if ( !$deposit_amount ) {
				return 0;
			}

			if ( 'fixed' === $deposit_type ) {
				return min( $deposit_amount, $total_amount );
			} elseif ( 'percentage' === $deposit_type ) {
				return ( $total_amount * $deposit_amount ) / 100;
			}

			return 0;
		}

		/**
		 * Get remaining amount
		 */
		public function get_remaining_amount( $product_id, $total_amount ) {
			$deposit_amount = $this->calculate_deposit( $product_id, $total_amount );
			return $total_amount - $deposit_amount;
		}
	}

	new Smart_Rentals_WC_Deposit();
}