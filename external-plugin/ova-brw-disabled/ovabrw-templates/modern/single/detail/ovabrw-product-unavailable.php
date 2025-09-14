<?php if ( !defined( 'ABSPATH' ) ) exit();

// Check show disabled dates
if ( 'yes' !== ovabrw_get_setting( 'template_show_maintenance', 'yes' ) ) return;

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get disabled start date
$disabled_startdate = $product->get_meta_value( 'untime_startdate' );

if ( ovabrw_array_exists( $disabled_startdate ) ):
	// Date format
	$date_format = OVABRW()->options->get_datetime_format();
	if ( $product->is_rental_type( 'hotel' ) ) {
		$date_format = OVABRW()->options->get_date_format();
	}

	// Get disabled end date
	$disabled_enddate = $product->get_meta_value( 'untime_enddate' );
?>
	<div class="ovabrw-product-unavailable">
		<label class="ovabrw-label">
			<?php esc_html_e( 'You can\'t rent product in this time', 'ova-brw' ); ?>
		</label>
		<table class="ovabrw-table">
			<tbody>
			<?php foreach ( $disabled_startdate as $k => $start_date ):
				$start_date = strtotime( $start_date );
				$end_date 	= strtotime( ovabrw_get_meta_data( $k, $disabled_enddate ) );

				if ( $start_date && $end_date ):
			?>
				<tr>
					<td>
						<?php echo sprintf( esc_html__( '%s - %s', 'ova-brw' ), gmdate( $date_format, $start_date ), gmdate( $date_format, $end_date ) ); ?>
					</td>
				</tr>
			<?php endif;
			endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>