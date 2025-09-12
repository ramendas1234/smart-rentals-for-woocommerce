<?php
/**
 * Smart Rentals WC CPT class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_CPT' ) ) {

	class Smart_Rentals_WC_CPT {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Register custom post types
			add_action( 'init', [ $this, 'register_location' ] );
			add_action( 'init', [ $this, 'register_resource' ] );
		}

		/**
		 * Register Location post type
		 */
		public function register_location() {
			$labels = [
				'name' => __( 'Locations', 'smart-rentals-wc' ),
				'singular_name' => __( 'Location', 'smart-rentals-wc' ),
				'menu_name' => __( 'Locations', 'smart-rentals-wc' ),
				'add_new' => __( 'Add New Location', 'smart-rentals-wc' ),
				'add_new_item' => __( 'Add New Location', 'smart-rentals-wc' ),
				'edit_item' => __( 'Edit Location', 'smart-rentals-wc' ),
				'new_item' => __( 'New Location', 'smart-rentals-wc' ),
				'view_item' => __( 'View Location', 'smart-rentals-wc' ),
				'search_items' => __( 'Search Locations', 'smart-rentals-wc' ),
				'not_found' => __( 'No locations found', 'smart-rentals-wc' ),
				'not_found_in_trash' => __( 'No locations found in trash', 'smart-rentals-wc' ),
			];

			register_post_type( 'rental_location', [
				'labels' => $labels,
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => 'smart-rentals-wc',
				'capability_type' => 'post',
				'supports' => [ 'title', 'editor', 'thumbnail' ],
				'menu_icon' => 'dashicons-location',
			]);
		}

		/**
		 * Register Resource post type
		 */
		public function register_resource() {
			$labels = [
				'name' => __( 'Resources', 'smart-rentals-wc' ),
				'singular_name' => __( 'Resource', 'smart-rentals-wc' ),
				'menu_name' => __( 'Resources', 'smart-rentals-wc' ),
				'add_new' => __( 'Add New Resource', 'smart-rentals-wc' ),
				'add_new_item' => __( 'Add New Resource', 'smart-rentals-wc' ),
				'edit_item' => __( 'Edit Resource', 'smart-rentals-wc' ),
				'new_item' => __( 'New Resource', 'smart-rentals-wc' ),
				'view_item' => __( 'View Resource', 'smart-rentals-wc' ),
				'search_items' => __( 'Search Resources', 'smart-rentals-wc' ),
				'not_found' => __( 'No resources found', 'smart-rentals-wc' ),
				'not_found_in_trash' => __( 'No resources found in trash', 'smart-rentals-wc' ),
			];

			register_post_type( 'rental_resource', [
				'labels' => $labels,
				'public' => false,
				'show_ui' => true,
				'show_in_menu' => 'smart-rentals-wc',
				'capability_type' => 'post',
				'supports' => [ 'title', 'editor', 'thumbnail' ],
				'menu_icon' => 'dashicons-admin-tools',
			]);
		}
	}

}