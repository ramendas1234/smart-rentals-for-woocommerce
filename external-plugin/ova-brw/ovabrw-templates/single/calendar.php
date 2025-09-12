<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Show calendar
if ( 'yes' !== ovabrw_get_setting( 'template_show_calendar', 'yes' ) ) return;

// Calendar options
$calendar_options = $product->get_calendar_options();

// Booked dates
$booked_dates = ovabrw_get_meta_data( 'bookedDates', $calendar_options );

?>
<div class="wrap_calendar">
	<div class="ovabrw_product_calendar"></div>
	<?php ovabrw_text_input([
		'type' 	=> 'hidden',
		'name' 	=> 'ovabrw-calendar-options',
		'value' => json_encode( $calendar_options )
	]); ?>
</div>
<ul class="intruction_calendar">
	<li>
		<span class="white"></span>
		<span><?php esc_html_e( 'Available','ova-brw' ); ?></span>		
	</li>
	<?php if ( 'hotel' === $product->get_charged_by() ): ?>
		<li>
			<span class="maybe"></span>
			<span><?php esc_html_e( 'Maybe available', 'ova-brw' ); ?></span>
		</li>
	<?php endif; ?>
	<li>
		<span class="yellow"></span>
		<span><?php esc_html_e( 'Unavailable','ova-brw' ); ?></span>
	</li>
	<?php if ( ovabrw_array_exists( $booked_dates ) ) : ?>
		<li>
			<span class="booked"></span>
			<span><?php esc_html_e( 'Booked','ova-brw' ); ?></span>
		</li>
	<?php endif; ?>
</ul>