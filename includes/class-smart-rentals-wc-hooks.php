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
			// Product hooks
			add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_rental_badge' ], 5 );
			
			// Shop loop hooks
			add_action( 'woocommerce_shop_loop_item_title', [ $this, 'add_rental_badge_to_loop' ], 15 );
			
			// Single product hooks
			add_action( 'woocommerce_single_product_summary', [ $this, 'add_rental_notice' ], 15 );
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
			global $post;
			
			if ( smart_rentals_wc_is_rental_product( $post->ID ) ) {
				echo '<span class="smart-rentals-badge">' . __( 'Rental', 'smart-rentals-wc' ) . '</span>';
			}
		}

		/**
		 * Add rental notice to single product
		 */
		public function add_rental_notice() {
			global $post;
			
			if ( smart_rentals_wc_is_rental_product( $post->ID ) ) {
				$rental_type = smart_rentals_wc_get_post_meta( $post->ID, 'rental_type' );
				$rental_types = Smart_Rentals_WC()->rental->get_rental_types();
				$rental_type_name = isset( $rental_types[$rental_type] ) ? $rental_types[$rental_type] : ucfirst( $rental_type );
				
				echo '<div class="smart-rentals-notice">';
				echo '<p><strong>' . __( 'Rental Type:', 'smart-rentals-wc' ) . '</strong> ' . esc_html( $rental_type_name ) . '</p>';
				echo '</div>';
			}
		}
	}

}