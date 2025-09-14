<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Rental Factory class.
 */
if ( !class_exists( 'OVABRW_Rental_Factory', false ) ) {

	class OVABRW_Rental_Factory {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Get rental product
		 */
		public function get_rental_product( $id = false, $type = '' ) {
			// Get rental id
			$id = $this->get_rental_id( $id );
			if ( !$id ) return false;

			// Get rental type
			if ( !$type ) $type = $this->get_rental_type( $id );

			// Get class name
			$classname = $this->get_rental_class_name( $id, $type );

			if ( $classname ) {
				try {
					return new $classname( $id );
				} catch ( Exception $e ) {
					return false;
				}
			}
			
			return false;
		}

		/**
		 * Get rental id
		 */
		public function get_rental_id( $id ) {
			global $post;

			if ( false === $id && isset( $post, $post->ID ) && 'product' === get_post_type( $post->ID ) ) {
				return absint( $post->ID );
			} elseif ( is_numeric( $id ) ) {
				return $id;
			} else {
				return false;
			}
		}

		/**
		 * Get rental type
		 */
		public function get_rental_type( $id ) {
			if ( !$id ) return false;

			// Get product
			$product = wc_get_product( $id );

			if ( $product && $product->is_type( OVABRW_RENTAL ) ) {
				$type = $product->get_meta_value( 'price_type' );

				if ( $type ) return $type;
			}

			return false;
		}

		/**
		 * Get rental classname
		 */
		public function get_rental_class_name( $id, $type ) {
			$class_name = $type ? 'OVABRW_Rental_By_'.implode( '_', array_map( 'ucfirst', explode( '_', $type ) ) ) : false;

			return apply_filters( OVABRW_PREFIX.'get_rental_class_name', $class_name, $id, $type );
		}
		
		/**
		 * Main OVABRW_Rental_Factory Instance.
		 */
        public static function instance() {
            if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
        }
	}
}