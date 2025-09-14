<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Mail class.
 */
if ( !class_exists( 'OVABRW_Mail', false ) ) {

	class OVABRW_Mail {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Woo email headers
			add_filter( 'woocommerce_email_headers', [ $this, 'add_booking_email_recipients' ], 10, 3 );
		}

		/**
		 * Add booking email recipients
		 */
		public function add_booking_email_recipients( $headers, $email_id, $order ) {
			// Email recipients
		    $recipients = ovabrw_get_option( 'booking_recipient' );
		    if ( !$recipients ) return $headers;

		    // Get items
		    $items = method_exists( $order, 'get_items' ) ? $order->get_items() : '';
		    if ( !ovabrw_array_exists( $items ) ) return $headers;

		    // Rental type
		    $rental_type = '';

		    // Loop items
		    foreach ( $items as $item ) {
		        $rental_type = $item->get_meta( 'rental_type' );
		        if ( $rental_type ) break;
		    } // END loop

		    if ( !$rental_type || !in_array( $email_id, [ 'new_order', 'cancelled_order', 'failed_order' ] ) ) {
		        return $headers;
		    }

		    // Headers
		    if ( !$headers ) $headers = 'Content-Type: text/html; charset=UTF-8' . "\r\n";

		    // Recipient(s)
		    $recipients = explode( ',', $recipients );
		    if ( ovabrw_array_exists( $recipients ) ) {
		    	foreach ( $recipients as $recipient ) {
			        $recipient = sanitize_email( trim( $recipient ) );

			        if ( is_email( $recipient ) ) {
			            $headers .= 'Cc: ' . $recipient . "\r\n";
			        }
			    }
		    }

		    return $headers;
		}

		/**
		 * Send reminder email for pick-up date
		 */
		public function send_reminder_pickup_date_mail( $args = [] ) {
			// Get data
			$order 			= ovabrw_get_meta_data( 'order', $args );
			$customer_mail 	= ovabrw_get_meta_data( 'customer_mail', $args );
			$product_id 	= ovabrw_get_meta_data( 'product_id', $args );
			$product_name 	= ovabrw_get_meta_data( 'product_name', $args );
			$pickup_date 	= ovabrw_get_meta_data( 'pickup_date', $args );
			$dropoff_date 	= ovabrw_get_meta_data( 'dropoff_date', $args );

			// Subject mail
			$subject = get_option( 'reminder_mail_subject', esc_html__( 'Remind Pick-up date', 'ova-brw' ) );

			// Body mail
			$body = get_option( 'reminder_mail_content', esc_html__( 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date]', 'ova-brw' ) );
			$body = str_replace( '[ovabrw_vehicle_name]', '<a href="'.get_permalink( $product_id ).'" target="_blank">'. esc_html( $product_name ) .'</a>', $body );
			$body = str_replace( '[ovabrw_order_pickup_date]', $pickup_date, $body );
			$body = str_replace( '[ovabrw_order_dropoff_date]', $dropoff_date, $body );
			$body = apply_filters( OVABRW_PREFIX.'reminder_content_mail', $body, $args );

			// Headers mail
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=".get_bloginfo( 'charset' )."\r\n";

			add_filter( 'wp_mail_from', [ $this, 'mail_reminder_get_mail_from' ]);
			add_filter( 'wp_mail_from_name', [ $this, 'mail_reminder_get_from_name' ]);

			if ( wp_mail( $customer_mail, $subject, $body, $headers ) ) {
				$result = true;
			} else {
				$result = false;
			}

			remove_filter( 'wp_mail_from', [ $this, 'mail_reminder_get_mail_from' ]);
			remove_filter( 'wp_mail_from_name', [ $this, 'mail_reminder_get_from_name' ]);

			return $result;
		}

		/**
		 * Reminder email from
		 */
		public function mail_reminder_get_mail_from() {
			$send_from = get_option( 'reminder_mail_from_email', get_option( 'admin_email' ) );
			if ( !$send_from ) return get_option( 'admin_email' );

			return apply_filters( OVABRW_PREFIX.'mail_reminder_get_mail_from', $send_from );
		}

		/**
		 * Reminder email from name
		 */
		public function mail_reminder_get_from_name() {
			$from_name = get_option( 'reminder_mail_from_name' );
			if ( !$from_name ) $from_name = esc_html__( 'Remind Pick-up date', 'ova-brw' );

			return apply_filters( OVABRW_PREFIX.'mail_reminder_get_from_name', $from_name );
		}
		
		/**
		 * Send reminder email by drop-off date
		 */
		public function send_reminder_dropoff_date_mail( $args = [] ) {
			// Get data
			$order 			= ovabrw_get_meta_data( 'order', $args );
			$customer_mail 	= ovabrw_get_meta_data( 'customer_mail', $args );
			$product_id 	= ovabrw_get_meta_data( 'product_id', $args );
			$product_name 	= ovabrw_get_meta_data( 'product_name', $args );
			$pickup_date 	= ovabrw_get_meta_data( 'pickup_date', $args );
			$dropoff_date 	= ovabrw_get_meta_data( 'dropoff_date', $args );

			// Subject mail
			$subject = get_option( 'reminder_dropoff_date_mail_subject', esc_html__( 'Remind Drop-off date', 'ova-brw' ) );

			// Body mail
			$body = get_option( 'reminder_dropoff_date_mail_content', esc_html__( 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] and returned the vehicle at [ovabrw_order_dropoff_date].', 'ova-brw' ) );
			$body = str_replace( '[ovabrw_vehicle_name]', '<a href="'.get_permalink( $product_id ).'" target="_blank">'. esc_html( $product_name ) .'</a>', $body );
			$body = str_replace( '[ovabrw_order_pickup_date]', $pickup_date, $body );
			$body = str_replace( '[ovabrw_order_dropoff_date]', $dropoff_date, $body );
			$body = apply_filters( OVABRW_PREFIX.'reminder_content_mail', $body, $args );

			// Headers mail
			$headers = "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=".get_bloginfo( 'charset' )."\r\n";

			add_filter( 'wp_mail_from', [ $this, 'mail_reminder_dropoff_date_get_mail_from' ]);
			add_filter( 'wp_mail_from_name', [ $this, 'mail_reminder_dropoff_date_get_from_name' ]);

			if ( wp_mail( $customer_mail, $subject, $body, $headers ) ) {
				$result = true;
			} else {
				$result = false;
			}

			remove_filter( 'wp_mail_from', [ $this, 'mail_reminder_dropoff_date_get_mail_from' ]);
			remove_filter( 'wp_mail_from_name', [ $this, 'mail_reminder_dropoff_date_get_from_name' ]);

			return $result;
		}

		/**
		 * Reminder email from
		 */
		public function mail_reminder_dropoff_date_get_mail_from() {
			$send_from = get_option( 'reminder_dropoff_date_mail_from_email', get_option( 'admin_email' ) );
			if ( !$send_from ) return get_option( 'admin_email' );

			return apply_filters( OVABRW_PREFIX.'reminder_dropoff_date_mail_from_email', $send_from );
		}

		/**
		 * Reminder email from name
		 */
		public function mail_reminder_dropoff_date_get_from_name() {
			$from_name = get_option( 'reminder_dropoff_date_mail_from_name' );
			if ( !$from_name ) $from_name = esc_html__( 'Remind Drop-off date', 'ova-brw' );

			return apply_filters( OVABRW_PREFIX.'reminder_dropoff_date_mail_from_name', $from_name );
		}

		/**
		 * Send mail renquest booking
		 */
		public function mail_request_booking( $data = [] ) {
			$product_id = ovabrw_get_meta_data( 'product_id', $data );
			if ( !$product_id ) return false;

			// Get rental product
        	$rental_product = OVABRW()->rental->get_rental_product( $product_id );
        	if ( !$rental_product ) return false;

        	// Get email content
        	$body = $rental_product->get_request_booking_mail_content( $data );
        	if ( !$body ) return false;

        	// Get subject
        	$subject = apply_filters( OVABRW_PREFIX.'request_booking_email_subject', ovabrw_get_setting( 'request_booking_mail_subject', esc_html__( 'Request For Booking', 'ova-brw' ) ), $data );

        	// Result
        	$result = false;

        	// Send to
        	$send_to = ovabrw_get_option( 'request_booking_send_to', 'both' );

        	if ( 'both' === $send_to && $this->send_request_booking_to_admin( $subject, $body, $data ) && $this->send_request_booking_to_customer( $subject, $body, $data ) ) {
        		$result = true;
        	} elseif ( 'admin' === $send_to && $this->send_request_booking_to_admin( $subject, $body, $data ) ) {
        		$result = true;
        	} elseif ( 'customer' === $send_to && $this->send_request_booking_to_customer( $subject, $body, $data ) ) {
        		$result = true;
        	}

        	return apply_filters( OVABRW_PREFIX.'mail_request_booking', $result, $data );
		}

		/**
		 * Send request booking email to Admin
		 */
		public function send_request_booking_to_admin( $subject, $body, $data ) {
			// Get headers
			$headers = $this->get_headers_email_to_admin( $data );

			// Mail to
			$mail_to = apply_filters( OVABRW_PREFIX.'request_booking_mail_to_admin', get_option( 'admin_email' ), $data );

			return apply_filters( OVABRW_PREFIX.'send_request_booking_to_admin', wp_mail( $mail_to, $subject, $body, $headers ), $subject, $body, $data );
		}

		/**
		 * Get headers email to Admin
		 */
		public function get_headers_email_to_admin( $data ) {
			$headers = [
				'MIME-Version: 1.0' . "\r\n",
				'Content-type: text/html; charset='.get_bloginfo( 'charset' ). "\r\n"
			];

			// Get email from name
			$from_name = $this->get_email_from_name_to_admin( $data );
			if ( $from_name ) $headers[] = $from_name;

			// Reply to
			$reply_to = ovabrw_get_meta_data( 'ovabrw_email', $data );
			if ( !$reply_to ) $reply_to = get_option( 'admin_email' );
			$headers[] = 'Reply-To: '. esc_html( $reply_to );

			// Recipient(s)
        	$recipients = ovabrw_get_option( 'request_booking_recipient' );
        	if ( $recipients ) $headers[] = 'Cc: '. esc_html( $recipients );
			
			return apply_filters( OVABRW_PREFIX.'request_booking_get_headers_email_to_admin', $headers, $data );
		}

		/**
		 * Get email from name to admin
		 */
		public function get_email_from_name_to_admin( $data ) {
			// Get from name
			$from_name = ovabrw_get_setting( 'request_booking_mail_from_name', esc_html__( 'Request For Booking', 'ova-brw' ) );

			// Get from email
			$from_email = ovabrw_get_setting( 'ova_brw_request_booking_mail_from_email', get_option( 'admin_email' ) );

			$from_name .= '<'. esc_attr( $from_email ) .'>';
			$from_name 	= sprintf( "From: %s", $from_name );

			return apply_filters( OVABRW_PREFIX.'request_booking_get_email_from_name_to_admin', $from_name, $data );
		}

		/**
		 * Send request booking email to Customer
		 */
		public function send_request_booking_to_customer( $subject, $body, $data ) {
			// Get headers
			$headers = $this->get_headers_email_to_customer( $data );

			// Mail to
			$mail_to = apply_filters( OVABRW_PREFIX.'request_booking_mail_to_customer', ovabrw_get_meta_data( 'ovabrw_email', $data ), $data );

			return apply_filters( OVABRW_PREFIX.'send_request_booking_to_customer', wp_mail( $mail_to, $subject, $body, $headers ), $subject, $body, $data );
		}

		/**
		 * Get headers email to Customer
		 */
		public function get_headers_email_to_customer( $data ) {
			$headers = [
				'MIME-Version: 1.0' . "\r\n",
				'Content-type: text/html; charset='.get_bloginfo( 'charset' ). "\r\n"
			];

			// Get email from name
			$from_name = $this->get_email_from_name_to_customer( $data );
			if ( $from_name ) $headers[] = $from_name;

			// Reply to
			$headers[] = 'Reply-To: '.get_option( 'admin_email' );
			
			return apply_filters( OVABRW_PREFIX.'request_booking_get_headers_email_to_customer', $headers, $data );
		}

		/**
		 * Get email from name to customer
		 */
		public function get_email_from_name_to_customer( $data ) {
			// Get from name
			$from_name = ovabrw_get_setting( 'request_booking_mail_from_name', esc_html__( 'Request For Booking', 'ova-brw' ) );

			// Get from email
			$from_email = ovabrw_get_setting( 'request_booking_mail_from_email', get_option( 'admin_email' ) );

			$from_name .= '<'. esc_attr( $from_email ) .'>';
			$from_name 	= sprintf( "From: %s", $from_name );

			return apply_filters( OVABRW_PREFIX.'request_booking_get_email_from_name_to_customer', $from_name, $data );
		}

		/**
		 * Main OVABRW_Mail instance
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}

	new OVABRW_Mail();
}