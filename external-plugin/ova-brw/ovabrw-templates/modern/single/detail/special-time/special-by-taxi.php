<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get special prices
$special_price 	= $product->get_meta_value( 'st_price_distance' );
$price_by 		= $product->get_meta_value( 'map_price_by' );
if ( !$price_by ) $price_by = 'km';

if ( ovabrw_array_exists( $special_price ) ):
	// Date time format
	$datetime_format 	= OVABRW()->options->get_datetime_format();

	$special_startdate 	= $product->get_meta_value( 'st_pickup_distance' );
	$special_enddate 	= $product->get_meta_value( 'st_pickoff_distance' );
	$special_discount 	= $product->get_meta_value( 'st_discount_distance' );
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
					<?php if ( 'km' === $price_by ): ?>
						<th><?php esc_html_e( 'Price/Km', 'ova-brw' ); ?></th>
					<?php else: ?>
						<th><?php esc_html_e( 'Price/Mi', 'ova-brw' ); ?></th>
					<?php endif; ?>
					<?php if ( ovabrw_array_exists( $special_discount ) ): ?>
						<th><?php esc_html_e( 'Discount', 'ova-brw' ); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
			<?php foreach ( $special_price as $k => $price ):
				// Start date
				$start_date = strtotime( ovabrw_get_meta_data( $k, $special_startdate ) );
				if ( !$start_date ) continue;
				$start_date = gmdate( $datetime_format, $start_date );

				// End date
				$end_date = strtotime( ovabrw_get_meta_data( $k, $special_enddate ) );
				if ( !$end_date ) continue;
				$end_date = gmdate( $datetime_format, $end_date );

				// Discounts
				$discounts = ovabrw_get_meta_data( $k, $special_discount );

				if ( '' !== $price && $start_date && $end_date ): ?>
					<tr>
						<td><?php echo esc_html( $start_date ); ?></td>
						<td><?php echo esc_html( $end_date ); ?></td>
						<td><?php echo ovabrw_wc_price( $price ); ?></td>
						<?php if ( ovabrw_array_exists( $special_discount ) ): ?>
						<td>
							<?php if ( ovabrw_array_exists( $discounts ) ):
								$dsc_price 	= ovabrw_get_meta_data( 'price', $discounts );
								$dsc_from 	= ovabrw_get_meta_data( 'from', $discounts );
								$dsc_to 	= ovabrw_get_meta_data( 'to', $discounts );
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
												<?php echo esc_html( $start_date ); ?>
											</span>
											<span class="seperate">-</span>
											<span class="end-time">
												<?php echo esc_html( $end_date ); ?>
											</span>
										</div>
										<?php if ( $dsc_price ): ?>
											<table class="ovabrw-table">
												<thead>
													<tr>
														<?php if ( 'km' === $price_by ): ?>
															<th><?php esc_html_e( 'From - To (Km)', 'ova-brw' ); ?></th>
															<th><?php esc_html_e( 'Price/Km', 'ova-brw' ); ?></th>
														<?php else: ?>
															<th><?php esc_html_e( 'From - To (Mi)', 'ova-brw' ); ?></th>
															<th><?php esc_html_e( 'Price/Mi', 'ova-brw' ); ?></th>
														<?php endif; ?>
													</tr>
												</thead>
												<tbody>
												<?php foreach ( $dsc_price as $dsc_k => $dsc_v_price ):
													$dsc_v_from = ovabrw_get_meta_data( $dsc_k, $dsc_from );
													$dsc_v_to 	= ovabrw_get_meta_data( $dsc_k, $dsc_to );

													if ( $dsc_v_from != '' && $dsc_v_to != '' && $dsc_v_price != '' ): ?>
														<tr>
															<td>
																<span>
																	<?php echo esc_html( $dsc_v_from ); ?>
																</span>
																<span>-</span>
																<span>
																	<?php echo esc_html( $dsc_v_to ); ?>
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