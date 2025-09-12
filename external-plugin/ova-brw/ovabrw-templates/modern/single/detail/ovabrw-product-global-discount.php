<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

switch ( $product->get_rental_type() ) {
	case 'day':
		ovabrw_get_template( 'modern/single/detail/discount/discount-by-day.php' );
		break;
	case 'hour':
		ovabrw_get_template( 'modern/single/detail/discount/discount-by-hour.php' );
		break;
	case 'mixed':
		ovabrw_get_template( 'modern/single/detail/discount/discount-by-mixed.php' );
		break;
	case 'period_time':
		ovabrw_get_template( 'modern/single/detail/discount/discount-by-period-of-time.php' );
		break;
	case 'taxi':
		ovabrw_get_template( 'modern/single/detail/discount/discount-by-taxi.php' );
		break;
	case 'hotel':
		ovabrw_get_template( 'modern/single/detail/discount/discount-by-day.php' );
		break;
	default:
		// code...
		break;
}