<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Special price
$special_prices = $product->get_meta_value( 'special_price' );
	
if ( ovabrw_array_exists( $special_prices ) ):
	// Date time format
	$datetime_format = OVABRW()->options->get_datetime_format();

	// Special start date
	$special_startdate = $product->get_meta_value( 'special_startdate' );

	// Special end date
	$special_enddate = $product->get_meta_value( 'special_enddate' );
?>
	<div class="ovabrw-product-special-time">
		<label class="ovabrw-label">
			<?php esc_html_e( 'Special Time', 'ova-brw' ); ?>
		</label>
		<table class="ovabrw-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $special_prices as $k => $price ):
				// Start date
				$start_date = strtotime( ovabrw_get_meta_data( $k, $special_startdate ) );

				// End date
				$end_date = strtotime( ovabrw_get_meta_data( $k, $special_enddate ) );
				
				if ( $price != '' && $start_date && $end_date ): ?>
					<tr>
						<td>
							<?php echo esc_html( gmdate( $datetime_format, $start_date ) ); ?>
						</td>
						<td>
							<?php echo esc_html( gmdate( $datetime_format, $end_date ) ); ?>
						</td>
						<td>
							<?php echo ovabrw_wc_price( $price ); ?>
						</td>
					</tr>
			<?php endif;
			endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>