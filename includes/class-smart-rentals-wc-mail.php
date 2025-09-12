<?php
/**
 * Smart Rentals WC Mail class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Mail' ) ) {

	class Smart_Rentals_WC_Mail {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Order status change hooks
			add_action( 'woocommerce_order_status_completed', [ $this, 'send_rental_confirmation' ] );
			add_action( 'woocommerce_order_status_processing', [ $this, 'send_rental_confirmation' ] );
			
			// Rental reminder emails
			add_action( 'smart_rentals_wc_send_pickup_reminder', [ $this, 'send_pickup_reminder' ] );
			add_action( 'smart_rentals_wc_send_return_reminder', [ $this, 'send_return_reminder' ] );
		}

		/**
		 * Send rental confirmation email
		 */
		public function send_rental_confirmation( $order_id ) {
			$order = wc_get_order( $order_id );
			
			if ( !$order ) {
				return;
			}

			$has_rental_items = false;
			
			foreach ( $order->get_items() as $item ) {
				$is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
				if ( 'yes' === $is_rental ) {
					$has_rental_items = true;
					break;
				}
			}

			if ( !$has_rental_items ) {
				return;
			}

			// Send confirmation email
			$this->send_email( $order, 'rental_confirmation' );
		}

		/**
		 * Send pickup reminder email
		 */
		public function send_pickup_reminder( $order_id ) {
			$order = wc_get_order( $order_id );
			
			if ( !$order ) {
				return;
			}

			$this->send_email( $order, 'pickup_reminder' );
		}

		/**
		 * Send return reminder email
		 */
		public function send_return_reminder( $order_id ) {
			$order = wc_get_order( $order_id );
			
			if ( !$order ) {
				return;
			}

			$this->send_email( $order, 'return_reminder' );
		}

		/**
		 * Send email
		 */
		private function send_email( $order, $type ) {
			$customer_email = $order->get_billing_email();
			$customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
			
			$subject = $this->get_email_subject( $type, $order );
			$message = $this->get_email_message( $type, $order );
			$headers = $this->get_email_headers();

			wp_mail( $customer_email, $subject, $message, $headers );
		}

		/**
		 * Get email subject
		 */
		private function get_email_subject( $type, $order ) {
			$subjects = [
				'rental_confirmation' => __( 'Rental Booking Confirmed - Order #%s', 'smart-rentals-wc' ),
				'pickup_reminder' => __( 'Rental Pickup Reminder - Order #%s', 'smart-rentals-wc' ),
				'return_reminder' => __( 'Rental Return Reminder - Order #%s', 'smart-rentals-wc' ),
			];

			$subject = isset( $subjects[$type] ) ? $subjects[$type] : $subjects['rental_confirmation'];
			
			return sprintf( $subject, $order->get_order_number() );
		}

		/**
		 * Get email message
		 */
		private function get_email_message( $type, $order ) {
			$customer_name = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
			
			$message = '';
			
			switch ( $type ) {
				case 'rental_confirmation':
					$message = sprintf(
						__( 'Dear %s,

Your rental booking has been confirmed! Here are the details:

Order Number: %s
Order Date: %s

Rental Items:
%s

Thank you for choosing our rental service!

Best regards,
%s', 'smart-rentals-wc' ),
						$customer_name,
						$order->get_order_number(),
						$order->get_date_created()->format( 'Y-m-d H:i:s' ),
						$this->get_rental_items_text( $order ),
						get_bloginfo( 'name' )
					);
					break;

				case 'pickup_reminder':
					$message = sprintf(
						__( 'Dear %s,

This is a reminder that your rental pickup is coming up soon.

Order Number: %s

Rental Items:
%s

Please make sure to arrive on time for pickup.

Best regards,
%s', 'smart-rentals-wc' ),
						$customer_name,
						$order->get_order_number(),
						$this->get_rental_items_text( $order ),
						get_bloginfo( 'name' )
					);
					break;

				case 'return_reminder':
					$message = sprintf(
						__( 'Dear %s,

This is a reminder that your rental return is due soon.

Order Number: %s

Rental Items:
%s

Please make sure to return the items on time to avoid late fees.

Best regards,
%s', 'smart-rentals-wc' ),
						$customer_name,
						$order->get_order_number(),
						$this->get_rental_items_text( $order ),
						get_bloginfo( 'name' )
					);
					break;
			}

			return apply_filters( 'smart_rentals_wc_email_message', $message, $type, $order );
		}

		/**
		 * Get rental items text
		 */
		private function get_rental_items_text( $order ) {
			$items_text = '';
			
			foreach ( $order->get_items() as $item ) {
				$is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
				
				if ( 'yes' !== $is_rental ) {
					continue;
				}

				$pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
				$dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
				$pickup_time = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_time' ) );
				$dropoff_time = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_time' ) );

				$items_text .= sprintf(
					"- %s (Qty: %d)\n  Pickup: %s%s\n  Drop-off: %s%s\n\n",
					$item->get_name(),
					$item->get_quantity(),
					$pickup_date,
					$pickup_time ? ' ' . $pickup_time : '',
					$dropoff_date,
					$dropoff_time ? ' ' . $dropoff_time : ''
				);
			}

			return $items_text;
		}

		/**
		 * Get email headers
		 */
		private function get_email_headers() {
			$headers = [
				'Content-Type: text/plain; charset=UTF-8',
				'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>',
			];

			return apply_filters( 'smart_rentals_wc_email_headers', $headers );
		}
	}

}