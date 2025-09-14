<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get special prices
$special_prices = $product->get_meta_value( 'rt_price' );
	
if ( ovabrw_array_exists( $special_prices ) ):
	// Date format
	$date_format = OVABRW()->options->get_date_format();

	// Time format
	$time_format = OVABRW()->options->get_time_format();

	$special_startdate 	= $product->get_meta_value( 'rt_startdate' );
	$special_enddate 	= $product->get_meta_value( 'rt_enddate' );
	$special_starttime 	= $product->get_meta_value( 'rt_starttime' );
	$special_endtime 	= $product->get_meta_value( 'rt_endtime' );
	$special_discount 	= $product->get_meta_value( 'rt_discount' );
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
					<th><?php esc_html_e( 'Price/Day', 'ova-brw' ); ?></th>
					<?php if ( ovabrw_array_exists( $special_discount ) ): ?>
						<th><?php esc_html_e( 'Discount', 'ova-brw' ); ?></th>
					<?php endif; ?>
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
				if ( $end_time ) $end .= ' '.gmdate( $time_format, $end_time );

				// Discounts
				$discounts = ovabrw_get_meta_data( $k, $special_discount );
				
				if ( '' !== $price && $start && $end ): ?>
					<tr>
						<td><?php echo esc_html( $start ); ?></td>
						<td><?php echo esc_html( $end ); ?></td>
						<td><?php echo ovabrw_wc_price( $price ); ?></td>
						<?php if ( ovabrw_array_exists( $special_discount ) ): ?>
						<td>
							<?php if ( ovabrw_array_exists( $discounts ) ):
								$dsc_price 	= ovabrw_get_meta_data( 'price', $discounts );
								$dsc_min 	= ovabrw_get_meta_data( 'min', $discounts );
								$dsc_max 	= ovabrw_get_meta_data( 'max', $discounts );
								$dsc_type 	= ovabrw_get_meta_data( 'duration_type', $discounts );
							?>
							<a href="#" class="ovabrw_open_popup">
								<?php esc_html_e( 'View', 'ova-brw' ); ?>
							</a>
							<div class="popup">
								<div class="popup-inner">
									<div class="price_table">
										<div class="time_discount">
											<span>
												<?php esc_html_e( 'Time: ', 'ova-brw' ); ?>
											</span>
											<span class="start-time">
												<?php echo esc_html( $start ); ?>
											</span>
											<span class="seperate">-</span>
											<span class="end-time">
												<?php echo esc_html( $end ); ?>
											</span>
										</div>
										<?php if ( ovabrw_array_exists( $dsc_price ) ): ?>
											<table class="ovabrw-table">
												<thead>
													<tr>
														<th>
															<?php esc_html_e( 'Min - Max (Days)', 'ova-brw' ); ?>
														</th>
														<th>
															<?php esc_html_e( 'Price/Day', 'ova-brw' ); ?>
														</th>
													</tr>
												</thead>
												<tbody>
												<?php foreach ( $dsc_price as $dsc_k => $dsc_v_price ):
													$dsc_v_type = ovabrw_get_meta_data( $dsc_k, $dsc_type );
													$dsc_v_min 	= ovabrw_get_meta_data( $dsc_k, $dsc_min );
													$dsc_v_max 	= ovabrw_get_meta_data( $dsc_k, $dsc_max );

													if ( 'days' === $dsc_v_type && $dsc_v_min != '' && $dsc_v_max != '' && $dsc_v_price != '' ):
												?>
														<tr>
															<td>
																<span>
																	<?php echo esc_html( $dsc_v_min ); ?>
																</span>
																<span>-</span>
																<span>
																	<?php echo esc_html( $dsc_v_max ); ?>
																</span>
															</td>
															<td>
																<?php echo ovabrw_wc_price( $dsc_v_price ); ?>
															</td>
														</tr>
												<?php endif;
												endforeach; ?>
												</tbody>
											</table>
										<?php endif; ?>
									</div>
									<div class="close_discount">
										<a class="popup-close-2" href="#">
											<?php esc_html_e( 'Close', 'ova-brw' ); ?>
										</a>
									</div>
									<a class="popup-close" href="#">x</a>
								</div>
							</div>
							<?php endif; ?>
						</td>
						<?php endif; ?>
					</tr>
			<?php endif;
			endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>