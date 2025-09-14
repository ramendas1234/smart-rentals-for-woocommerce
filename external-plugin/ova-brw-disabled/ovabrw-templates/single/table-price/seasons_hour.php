<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Special prices
$special_prices = $product->get_meta_value( 'rt_price_hour' );
if ( !ovabrw_array_exists( $special_prices ) ) return;

// Rental type
$rental_type = $product->get_rental_type();

// Special data
$special_startdate 	= $product->get_meta_value( 'rt_startdate' ); 
$special_enddate 	= $product->get_meta_value( 'rt_enddate' );
$special_starttime 	= $product->get_meta_value( 'rt_starttime' );
$special_endtime 	= $product->get_meta_value( 'rt_endtime' );
$special_discount 	= $product->get_meta_value( 'rt_discount' );

// Date format
$date_format = OVABRW()->options->get_date_format();

// Time format
$time_format = OVABRW()->options->get_time_format();

?>

<div class="price_table">
	<label>
		<?php esc_html_e( 'Special Time', 'ova-brw' ); ?>
	</label>
	<table>
		<thead>
			<tr>
				<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?></th>
				<th><?php esc_html_e( 'Special Discount', 'ova-brw' ); ?></th>
			</tr>
		</thead>
		<tbody>
		<?php foreach ( $special_prices as $k => $price ):
			// Start date
			$start_date = strtotime( ovabrw_get_meta_data( $k, $special_startdate ) );
			if ( !$start_date ) continue;
			$start = gmdate( $date_format, $start_date );

			// End date
			$end_date = strtotime( ovabrw_get_meta_data( $k, $special_enddate ) );
			if ( !$end_date ) continue;
			$end = gmdate( $date_format, $end_date );

			// Start time
			$start_time = strtotime( ovabrw_get_meta_data( $k, $special_starttime ) );
			if ( !$start_time ) {
				$start_time = strtotime( gmdate( $time_format, $start_date ) );
			}
			if ( $start_time ) $start .= ' '.gmdate( $time_format, $start_time );

			// End time
			$end_time = strtotime( ovabrw_get_meta_data( $k, $special_endtime ) );
			if ( !$end_time ) {
				$end_time = strtotime( gmdate( $time_format, $end_date ) );
			}
			if ( $end_time ) $end .= ' ' . gmdate( $time_format, $end_time );

			// Discounts
			$discounts = ovabrw_get_meta_data( $k, $special_discount );

			// Discount price
			$disc_price = ovabrw_get_meta_data( 'price', $discounts );
			$disc_from 	= ovabrw_get_meta_data( 'min', $discounts );
			$disc_to 	= ovabrw_get_meta_data( 'max', $discounts );
			$disc_type 	= ovabrw_get_meta_data( 'duration_type', $discounts );
		?>
			<tr class="<?php echo intval( $k%2 ) ? 'eve' : 'odd'; ?>">
				<td class="bold" data-title="<?php esc_html_e( 'Start Date', 'ova-brw' ); ?>">
					<?php echo esc_html( $start ); ?>
				</td>
				<td class="bold" data-title="<?php esc_html_e( 'End Date', 'ova-brw' ); ?>">
					<?php echo esc_html( $end ); ?>
				</td>
				<td data-title="<?php echo sprintf( esc_attr__( 'Price/Hour from %s - %s', 'ova-brw' ), $start, $end ); ?>">
					<?php echo ovabrw_wc_price( $price ); ?>
				</td>
				<td data-title="<?php esc_html_e( 'Special Discount', 'ova-brw' ); ?>">
					<a href="#" class="ovabrw_open_popup" data-popup-open="popup-ovacrs-rt-discount-<?php echo esc_attr( $k ); ?>">
						<?php esc_html_e( 'View Discount', 'ova-brw' ); ?>
						<div class="ovacrs_rt_discount popup" data-popup="popup-ovacrs-rt-discount-<?php echo esc_attr( $k ); ?>">
							<div class="popup-inner">
								<div class="price_table">
									<div class="time_discount">
										<span>
											<?php esc_html_e( 'Time Discount: ', 'ova-brw' ); ?>
										</span>
										<span class="time">
											<?php echo sprintf( esc_html__( '%s - %s', 'ova-brw' ), $start, $end ); ?>
										</span>
									</div>
									<?php if ( ovabrw_array_exists( $disc_from ) ): 
										asort( $disc_from );
									?>
										<table>
											<thead>
												<tr>
													<th><?php esc_html_e( 'Min - Max (Hours)', 'ova-brw' ); ?></th>
													<th><?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?></th>
												</tr>
											</thead>
											<tbody>
											<?php $n = 0;
											foreach ( $disc_from as $i => $from ):
												$to 	= ovabrw_get_meta_data( $i, $disc_to );
												$price 	= ovabrw_get_meta_data( $i, $disc_price );
												$type 	= ovabrw_get_meta_data( $i, $disc_type );

												if ( 'hours' === $type ): ?>
													<tr class="<?php echo intval( $n%2 ) ? 'eve' : 'odd'; $n++; ?>">
														<td class="bold" data-title="<?php esc_html_e( 'Min Duration (Hours)', 'ova-brw' ); ?>">
															<?php echo sprintf( esc_html__( '%s - %s', 'ova-brw' ), $from, $to ); ?>
														</td>
														<td data-title="<?php echo sprintf( esc_attr__( 'Price/Hour from %s - %s days', 'ova-brw' ), $from, $to ) ?>">
															<?php echo ovabrw_wc_price( $price ); ?>
														</td>
													</tr>
												<?php endif;
											endforeach; ?>
											</tbody>
										</table>
									<?php else: ?>
										<div class="no_discount">
											<?php esc_html_e( 'No Discount in this time', 'ova-brw' ); ?>
										</div>
									<?php endif; ?>
								</div>
								<div  class="close_discount">
									<a class="popup-close-2" data-popup-close="popup-ovacrs-rt-discount-<?php echo esc_attr( $k ); ?>" href="#">
										<?php esc_html_e( 'Close', 'ova-brw' ); ?>
									</a>
								</div>
								<a class="popup-close" data-popup-close="popup-ovacrs-rt-discount-<?php echo esc_attr( $k ); ?>" href="#">x</a>
							</div>
						</div>
					</a>
				</td>
			</tr>			
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
