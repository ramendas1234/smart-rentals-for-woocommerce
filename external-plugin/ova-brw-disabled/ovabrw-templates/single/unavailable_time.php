<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Date time format
$date_format = OVABRW()->options->get_datetime_format();

// Disabled dates
$start_date = $product->get_meta_value( 'untime_startdate' );
$end_date 	= $product->get_meta_value( 'untime_enddate' );

if ( ovabrw_array_exists( $start_date ) ): ?>
	<div class="ovacrs_single_untime">
		<h3><?php esc_html_e( 'You can\'t rent product in this time', 'ova-brw' ); ?></h3>
		<ul>
			<?php foreach ( $start_date as $k => $start ):
				$start = strtotime( $start );
				if ( !$start ) continue;

				// End
				$end = strtotime( ovabrw_get_meta_data( $k, $end_date ) );
				if ( !$end ) continue;
			?>
				<li>
					<?php echo sprintf( esc_html__( '%s - %s', 'ova-brw' ), gmdate( $date_format, $start ), gmdate( $date_format, $end ) ); ?>
				</li>						
			<?php endforeach; ?>
		</ul>
	</div>
<?php endif; ?>
