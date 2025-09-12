<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Categories class.
 */
if ( !class_exists( 'OVABRW_Admin_Categories' ) ) {

	class OVABRW_Admin_Categories {

		/**
		 * Card templates
		 */
		protected $card_templates = [];

		/**
		 * Constructor
		 */
		public function __construct() {
			// Set card templates
			$this->set_card_templates();

			// Add new fields
            add_action( 'product_cat_add_form_fields', [ $this, 'add_form_fields' ] );

            // Edit fields
            add_action( 'product_cat_edit_form_fields', [ $this, 'edit_form_fields' ] );

            // Save fields
            add_action( 'create_product_cat', [ $this, 'save_fields' ] );
            add_action( 'edited_product_cat', [ $this, 'save_fields' ] );
		}

		/**
		 * Get term meta
		 */
		public function get_meta( $term_id, $name, $default = false ) {
			$value = '';

			if ( $term_id && $name ) {
				$value = get_term_meta( $term_id, OVABRW_PREFIX.$name, true );
			}
			if ( !$value && false !== $default ) {
				$value = $default;
			}

			return apply_filters( OVABRW_PREFIX.'get_term_meta', $value, $term_id, $name, $default );
		}

		/**
		 * Update term meta
		 */
		public function update_meta( $term_id, $name ) {
			if ( $term_id && $name ) {
				$value = ovabrw_get_meta_data( ovabrw_meta_key( $name ), $_REQUEST );
				update_term_meta( $term_id, ovabrw_meta_key( $name ), $value );
			}
		}

		/**
		 * Set card templates
		 */
		public function set_card_templates() {
			$this->card_templates = ovabrw_get_card_templates();
		}

		/**
		 * Add form fields
		 */
		public function add_form_fields() {
        	include( OVABRW_PLUGIN_ADMIN . 'categories/views/html-add-fields.php' );
        }

        /**
         * Edit form fields
         */
        public function edit_form_fields( $term ) {
        	include( OVABRW_PLUGIN_ADMIN . 'categories/views/html-edit-fields.php' );
        }

        /**
         * Save form fields
         */
        public function save_fields( $term_id ) {
        	$fields = [
        		'cat_dis',
        		'select_single_price_format',
        		'single_new_price_format',
        		'select_archive_price_format',
        		'archive_new_price_format',
        		'custom_tax',
        		'choose_custom_checkout_field',
        		'custom_checkout_field',
        		'choose_specifications',
        		'specifications',
        		'show_loc_booking_form',
        		'lable_pickup_date',
        		'lable_dropoff_date',
        		'product_templates',
        		'card_template'
        	];

        	foreach ( $fields as $name ) {
        		$this->update_meta( $term_id, $name );
        	}
	    }
	}

	new OVABRW_Admin_Categories();
}