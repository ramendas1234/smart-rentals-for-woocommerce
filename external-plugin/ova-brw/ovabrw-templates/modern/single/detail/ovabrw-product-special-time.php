<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

switch ( $product->get_rental_type() ) {
	case 'day':
		ovabrw_get_template( 'modern/single/detail/special-time/special-by-day.php' );
		break;
	case 'hour':
		ovabrw_get_template( 'modern/single/detail/special-time/special-by-hour.php' );
		break;
	case 'mixed':
		ovabrw_get_template( 'modern/single/detail/special-time/special-by-mixed.php' );
		break;
	case 'taxi':
		ovabrw_get_template( 'modern/single/detail/special-time/special-by-taxi.php' );
		break;
	case 'hotel':
		ovabrw_get_template( 'modern/single/detail/special-time/special-by-hotel.php' );
		break;
	case 'appointment':
		ovabrw_get_template( 'modern/single/detail/special-time/special-by-appointment.php' );
		break;
	default:
		// code...
		break;
}