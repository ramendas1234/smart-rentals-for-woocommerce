<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get special prices
$special_prices = $this->get_meta_value( 'rt_price' );

if ( $this->is_type( 'hour' ) ) {
	$special_prices = $this->get_meta_value( 'rt_price_hour' );
}

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Special Time (ST)', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-special-time">
			<span style="float: right;">
				<?php esc_html_e( 'Note: ST doesn\'t use GD, it will use DST', 'ova-brw' ); ?>
			</span>
			<table class="widefat">
				<thead>
					<tr>
						<?php if ( $this->is_type( 'day' ) ): ?>
							<th class="ovabrw-required">
								<?php esc_html_e( 'Price/Day', 'ova-brw' ); ?>
							</th>
						<?php elseif ( $this->is_type( 'hour' ) ): ?>
							<th class="ovabrw-required">
								<?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?>
							</th>
						<?php elseif ( $this->is_type( 'mixed' ) ): ?>
							<th class="ovabrw-required">
								<?php esc_html_e( 'Price/Day', 'ova-brw' ); ?>
							</th>
							<th class="ovabrw-required">
								<?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?>
							</th>
						<?php elseif ( $this->is_type( 'hotel' ) ): ?>
							<th class="ovabrw-required">
								<?php esc_html_e( 'Price/Night', 'ova-brw' ); ?>
							</th>
						<?php endif; ?>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Start Date', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'End Date', 'ova-brw' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Discount in Special Time (DST)', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody class="ovabrw-special-time-item">
				<?php if ( ovabrw_array_exists( $special_prices ) ):
					// Date format
					$date_format = OVABRW()->options->get_date_format();

					// Time format
					$time_format = OVABRW()->options->get_time_format();

					// Get special data
					$special_startdate 	= $this->get_meta_value( 'rt_startdate' );
					$special_starttime 	= $this->get_meta_value( 'rt_starttime' );
					$special_enddate 	= $this->get_meta_value( 'rt_enddate' );
					$special_endtime 	= $this->get_meta_value( 'rt_endtime' );
					$special_discount 	= $this->get_meta_value( 'rt_discount' );

					if ( $this->is_type( 'day' ) ):
						foreach ( $special_prices as $i => $price ):
							// Start date
							$start_date = ovabrw_get_meta_data( $i, $special_startdate );

							// End date
							$end_date = ovabrw_get_meta_data( $i, $special_enddate );
							
							// Start time
							$start_time = ovabrw_get_meta_data( $i, $special_starttime );
							if ( !$start_time ) {
								$start_time = strtotime( $start_date ) ? gmdate( $time_format, strtotime( $start_date ) ) : '';
							}

							// End time
							$end_time = ovabrw_get_meta_data( $i, $special_endtime );
							if ( !$end_time ) {
								$end_time = strtotime( $end_date ) ? gmdate( $time_format, strtotime( $end_date ) ) : '';
							}

							// Discounts
							$discounts = ovabrw_get_meta_data( $i, $special_discount );
						?>
							<tr>
								<td width="13%" class="ovabrw-input-price">
									<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'rt_price[]' ),
										'value' 		=> $price,
										'data_type' 	=> 'price',
										'placeholder' 	=> esc_html__( 'Price/Day', 'ova-brw' )
									]); ?>
								</td>
								<td width="19%">
									<?php ovabrw_wp_text_input([
										'type' 		=> 'text',
										'id' 		=> ovabrw_unique_id( 'special_from' ),
										'class' 	=> 'ovabrw-input-required start-date',
										'name' 		=> $this->get_meta_name( 'rt_startdate[]' ),
										'value' 	=> $start_date,
										'data_type' => 'datetimepicker',
										'attrs' 	=> [
											'data-date' => strtotime( $start_date ) ? gmdate( $date_format, strtotime( $start_date ) ) : '',
											'data-time' => strtotime( $start_time ) ? gmdate( $time_format, strtotime( $start_time ) ) : ''
										]
									]); ?>
							    </td>
							    <td width="19%">
							    	<?php ovabrw_wp_text_input([
							    		'type' 		=> 'text',
										'id' 		=> ovabrw_unique_id( 'special_to' ),
										'class' 	=> 'ovabrw-input-required end-date',
										'name' 		=> $this->get_meta_name( 'rt_enddate[]' ),
										'value' 	=> $end_date,
										'data_type' => 'datetimepicker',
										'attrs' 	=> [
											'data-date' => strtotime( $end_date ) ? gmdate( $date_format, strtotime( $end_date ) ) : '',
											'data-time' => strtotime( $end_time ) ? gmdate( $time_format, strtotime( $end_time ) ) : ''
										]
							    	]); ?>
							    </td>
							    <td width="48%" class="ovabrw-special-discounts">
							    	<table width="100%" class="widefat">
								      	<thead>
											<tr>
												<th class="ovabrw-required">
													<?php esc_html_e( 'Price/Day', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'From (number)', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'To (number)', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'Duration', 'ova-brw' ); ?>
												</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php if ( ovabrw_array_exists( $discounts ) ):
												$dsc_prices 	= ovabrw_get_meta_data( 'price', $discounts );
												$dsc_min 		= ovabrw_get_meta_data( 'min', $discounts );
												$dsc_max 		= ovabrw_get_meta_data( 'max', $discounts );
												$dsc_duration 	= ovabrw_get_meta_data( 'duration_type', $discounts );

												if ( ovabrw_array_exists( $dsc_prices ) ):
													foreach ( $dsc_prices as $k => $dsc_price ):
														$min 		= ovabrw_get_meta_data( $k, $dsc_min );
														$max 		= ovabrw_get_meta_data( $k, $dsc_max );
														$duration 	= ovabrw_get_meta_data( $k, $dsc_duration );
													?>
														<tr>
															<td width="25%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-price',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][price][]' ),
																	'value' 		=> $dsc_price,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '10.5'
																]); ?>
															</td>
															<td width="26%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-min',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][min][]' ),
																	'value' 		=> $min,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '1'
																]); ?>
															</td>
															<td width="26%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-max',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][max][]' ),
																	'value' 		=> $max,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '2'
																]); ?>
															</td>
															<td width="22%">
																<?php ovabrw_wp_select_input([
																	'class' 	=> 'ovabrw-input-required ovabrw-special-discount-duration',
																	'name' 		=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][duration_type][]' ),
																	'value' 	=> $duration,
																	'options' 	=> [
																		'days' 	=> esc_html__( 'Day(s)', 'ova-brw' )
																	]
																]); ?>
															</td>
															<td width="1%">
																<button class="button ovabrw-remove-st-discount">x</button>
															</td>
														</tr>
													<?php endforeach;
												endif; 
											endif; ?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="5">
													<button class="button ovabrw-add-st-discount">
														<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
													</button>
												</th>
											</tr>
										</tfoot>
							      	</table>
							    </td>
							    <td width="1%">
							    	<button class="button ovabrw-remove-special-time">x</button>
							    </td>
							</tr>
						<?php endforeach;
					elseif ( $this->is_type( 'hotel' ) ):
						foreach ( $special_prices as $i => $price ):
							// Start date
							$start_date = ovabrw_get_meta_data( $i, $special_startdate );

							// End date
							$end_date = ovabrw_get_meta_data( $i, $special_enddate );

							// Discounts
							$discounts = ovabrw_get_meta_data( $i, $special_discount );
						?>
							<tr>
								<td width="13%" class="ovabrw-input-price">
									<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'rt_price[]' ),
										'value' 		=> $price,
										'data_type' 	=> 'price',
										'placeholder' 	=> esc_html__( 'Price/Night', 'ova-brw' )
									]); ?>
								</td>
								<td width="19%">
									<?php ovabrw_wp_text_input([
										'type' 		=> 'text',
										'id' 		=> ovabrw_unique_id( 'special_from' ),
										'class' 	=> 'ovabrw-input-required start-date',
										'name' 		=> $this->get_meta_name( 'rt_startdate[]' ),
										'value' 	=> $start_date,
										'data_type' => 'datepicker'
									]); ?>
							    </td>
							    <td width="19%">
									<?php ovabrw_wp_text_input([
										'type' 		=> 'text',
										'id' 		=> ovabrw_unique_id( 'special_to' ),
										'class' 	=> 'ovabrw-input-required end-date',
										'name' 		=> $this->get_meta_name( 'rt_enddate[]' ),
										'value' 	=> $end_date,
										'data_type' => 'datepicker'
									]); ?>
							    </td>
							    <td width="48%" class="ovabrw-special-discounts">
							    	<table width="100%" class="widefat">
								      	<thead>
											<tr>
												<th class="ovabrw-required">
													<?php esc_html_e( 'Price/Night', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'From (number)', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'To (number)', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'Duration', 'ova-brw' ); ?>
												</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php if ( ovabrw_array_exists( $discounts ) ):
												$dsc_prices 	= ovabrw_get_meta_data( 'price', $discounts );
												$dsc_min 		= ovabrw_get_meta_data( 'min', $discounts );
												$dsc_max 		= ovabrw_get_meta_data( 'max', $discounts );
												$dsc_duration 	= ovabrw_get_meta_data( 'duration_type', $discounts );

												if ( ovabrw_array_exists( $dsc_prices ) ):
													foreach ( $dsc_prices as $k => $dsc_price ):
														$min 		= ovabrw_get_meta_data( $k, $dsc_min );
														$max 		= ovabrw_get_meta_data( $k, $dsc_max );
														$duration 	= ovabrw_get_meta_data( $k, $dsc_duration );
													?>
														<tr>
															<td width="25%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-price',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][price][]' ),
																	'value' 		=> $dsc_price,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '10.5'
																]); ?>
															</td>
															<td width="26%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-min',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][min][]' ),
																	'value' 		=> $min,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '1'
																]); ?>
															</td>
															<td width="26%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-max',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][max][]' ),
																	'value' 		=> $max,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '2'
																]); ?>
															</td>
															<td width="22%">
																<?php ovabrw_wp_select_input([
																	'class' 	=> 'ovabrw-input-required ovabrw-special-discount-duration',
																	'name' 		=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][duration_type][]' ),
																	'value' 	=> $duration,
																	'options' 	=> [
																		'days' 	=> esc_html__( 'Night(s)', 'ova-brw' )
																	]
																]); ?>
															</td>
															<td width="1%">
																<button class="button ovabrw-remove-st-discount">x</button>
															</td>
														</tr>
													<?php endforeach; 
												endif; 
											endif; ?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="5">
													<button class="button ovabrw-add-st-discount">
														<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
													</button>
												</th>
											</tr>
										</tfoot>
							      	</table>
							    </td>
							    <td width="1%">
							    	<button class="button ovabrw-remove-special-time">x</button>
							    </td>
							</tr>
						<?php endforeach;
					elseif ( $this->is_type( 'hour' ) ):
						foreach ( $special_prices as $i => $price ):
							// Start date
							$start_date = ovabrw_get_meta_data( $i, $special_startdate );

							// End date
							$end_date = ovabrw_get_meta_data( $i, $special_enddate );
							
							// Start time
							$start_time = ovabrw_get_meta_data( $i, $special_starttime );
							if ( !$start_time ) {
								$start_time = strtotime( $start_date ) ? gmdate( $time_format, strtotime( $start_date ) ) : '';
							}

							// End time
							$end_time = ovabrw_get_meta_data( $i, $special_endtime );
							if ( !$end_time ) {
								$end_time = strtotime( $end_date ) ? gmdate( $time_format, strtotime( $end_date ) ) : '';
							}

							// Discounts
							$discounts = ovabrw_get_meta_data( $i, $special_discount );
						?>
							<tr>
								<td width="13%" class="ovabrw-input-price">
									<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'rt_price_hour[]' ),
										'value' 		=> $price,
										'data_type' 	=> 'price',
										'placeholder' 	=> esc_html__( 'Price/Hour', 'ova-brw' )
									]); ?>
								</td>
								<td width="19%">
									<?php ovabrw_wp_text_input([
										'type' 		=> 'text',
										'id' 		=> ovabrw_unique_id( 'special_from' ),
										'class' 	=> 'ovabrw-input-required start-date',
										'name' 		=> $this->get_meta_name( 'rt_startdate[]' ),
										'value' 	=> $start_date,
										'data_type' => 'datetimepicker',
										'attrs' 	=> [
											'data-date' => strtotime( $start_date ) ? gmdate( $date_format, strtotime( $start_date ) ) : '',
											'data-time' => strtotime( $start_time ) ? gmdate( $time_format, strtotime( $start_time ) ) : ''
										]
									]); ?>
							    </td>
							    <td width="19%">
							    	<?php ovabrw_wp_text_input([
							    		'type' 		=> 'text',
										'id' 		=> ovabrw_unique_id( 'special_to' ),
										'class' 	=> 'ovabrw-input-required end-date',
										'name' 		=> $this->get_meta_name( 'rt_enddate[]' ),
										'value' 	=> $end_date,
										'data_type' => 'datetimepicker',
										'attrs' 	=> [
											'data-date' => strtotime( $end_date ) ? gmdate( $date_format, strtotime( $end_date ) ) : '',
											'data-time' => strtotime( $end_time ) ? gmdate( $time_format, strtotime( $end_time ) ) : ''
										]
							    	]); ?>
							    </td>
							    <td width="48%" class="ovabrw-special-discounts">
							    	<table width="100%" class="widefat">
								      	<thead>
											<tr>
												<th class="ovabrw-required">
													<?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'From (number)', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'To (number)', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'Duration', 'ova-brw' ); ?>
												</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php if ( ovabrw_array_exists( $discounts ) ):
												$dsc_prices 	= ovabrw_get_meta_data( 'price', $discounts );
												$dsc_min 		= ovabrw_get_meta_data( 'min', $discounts );
												$dsc_max 		= ovabrw_get_meta_data( 'max', $discounts );
												$dsc_duration 	= ovabrw_get_meta_data( 'duration_type', $discounts );

												if ( ovabrw_array_exists( $dsc_prices ) ):
													foreach ( $dsc_prices as $k => $dsc_price ):
														$min 		= ovabrw_get_meta_data( $k, $dsc_min );
														$max 		= ovabrw_get_meta_data( $k, $dsc_max );
														$duration 	= ovabrw_get_meta_data( $k, $dsc_duration );
													?>
														<tr>
															<td width="25%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-price',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][price][]' ),
																	'value' 		=> $dsc_price,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '10.5'
																]); ?>
															</td>
															<td width="26%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-min',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][min][]' ),
																	'value' 		=> $min,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '1'
																]); ?>
															</td>
															<td width="26%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-max',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][max][]' ),
																	'value' 		=> $max,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '2'
																]); ?>
															</td>
															<td width="22%">
																<?php ovabrw_wp_select_input([
																	'class' 	=> 'ovabrw-input-required ovabrw-special-discount-duration',
																	'name' 		=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][duration_type][]' ),
																	'value' 	=> $duration,
																	'options' 	=> [
																		'hours' => esc_html__( 'Hour(s)', 'ova-brw' )
																	]
																]); ?>
															</td>
															<td width="1%">
																<button class="button ovabrw-remove-st-discount">x</button>
															</td>
														</tr>
													<?php endforeach;
												endif; 
											endif; ?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="5">
													<button class="button ovabrw-add-st-discount">
														<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
													</button>
												</th>
											</tr>
										</tfoot>
							      	</table>
							    </td>
							    <td width="1%">
							    	<button class="button ovabrw-remove-special-time">x</button>
							    </td>
							</tr>
						<?php endforeach;
					else:
						// Get special price by hour
						$special_price_hour = $this->get_meta_value( 'rt_price_hour', '' );

						foreach ( $special_prices as $i => $price_day ):
							// Price by hour
							$price_hour = ovabrw_get_meta_data( $i, $special_price_hour );

							// Start date
							$start_date = ovabrw_get_meta_data( $i, $special_startdate );

							// End date
							$end_date = ovabrw_get_meta_data( $i, $special_enddate );
							
							// Start time
							$start_time = ovabrw_get_meta_data( $i, $special_starttime );
							if ( !$start_time ) {
								$start_time = strtotime( $start_date ) ? gmdate( $time_format, strtotime( $start_date ) ) : '';
							}

							// End time
							$end_time = ovabrw_get_meta_data( $i, $special_endtime );
							if ( !$end_time ) {
								$end_time = strtotime( $end_date ) ? gmdate( $time_format, strtotime( $end_date ) ) : '';
							}

							// Discounts
							$discounts = ovabrw_get_meta_data( $i, $special_discount );
						?>
							<tr>
								<td width="10%" class="ovabrw-input-price">
									<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'rt_price[]' ),
										'value' 		=> $price_day,
										'data_type' 	=> 'price',
										'placeholder' 	=> esc_html__( 'Price/Day', 'ova-brw' )
									]); ?>
								</td>
								<td width="10%" class="ovabrw-input-price">
									<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'rt_price_hour[]' ),
										'value' 		=> $price_hour,
										'data_type' 	=> 'price',
										'placeholder' 	=> esc_html__( 'Price/Hour', 'ova-brw' )
									]); ?>
								</td>
								<td width="16%">
									<?php ovabrw_wp_text_input([
										'type' 		=> 'text',
										'id' 		=> ovabrw_unique_id( 'special_from' ),
										'class' 	=> 'ovabrw-input-required start-date',
										'name' 		=> $this->get_meta_name( 'rt_startdate[]' ),
										'value' 	=> $start_date,
										'data_type' => 'datetimepicker',
										'attrs' 	=> [
											'data-date' => strtotime( $start_date ) ? gmdate( $date_format, strtotime( $start_date ) ) : '',
											'data-time' => strtotime( $start_time ) ? gmdate( $time_format, strtotime( $start_time ) ) : ''
										]
									]); ?>
							    </td>
							    <td width="16%">
							    	<?php ovabrw_wp_text_input([
							    		'type' 		=> 'text',
										'id' 		=> ovabrw_unique_id( 'special_to' ),
										'class' 	=> 'ovabrw-input-required end-date',
										'name' 		=> $this->get_meta_name( 'rt_enddate[]' ),
										'value' 	=> $end_date,
										'data_type' => 'datetimepicker',
										'attrs' 	=> [
											'data-date' => strtotime( $end_date ) ? gmdate( $date_format, strtotime( $end_date ) ) : '',
											'data-time' => strtotime( $end_time ) ? gmdate( $time_format, strtotime( $end_time ) ) : ''
										]
							    	]); ?>
							    </td>
							    <td width="47%" class="ovabrw-special-discounts">
							    	<table width="100%" class="widefat">
								      	<thead>
											<tr>
												<th class="ovabrw-required">
													<?php esc_html_e( 'Price', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'From (number)', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'To (number)', 'ova-brw' ); ?>
												</th>
												<th class="ovabrw-required">
													<?php esc_html_e( 'Duration', 'ova-brw' ); ?>
												</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											<?php if ( ovabrw_array_exists( $discounts ) ):
												$dsc_prices 	= ovabrw_get_meta_data( 'price', $discounts );
												$dsc_min 		= ovabrw_get_meta_data( 'min', $discounts );
												$dsc_max 		= ovabrw_get_meta_data( 'max', $discounts );
												$dsc_duration 	= ovabrw_get_meta_data( 'duration_type', $discounts );

												if ( ovabrw_array_exists( $dsc_prices ) ):
													foreach ( $dsc_prices as $k => $dsc_price ):
														$min 		= ovabrw_get_meta_data( $k, $dsc_min );
														$max 		= ovabrw_get_meta_data( $k, $dsc_max );
														$duration 	= ovabrw_get_meta_data( $k, $dsc_duration );
													?>
														<tr>
															<td width="25%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-price',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][price][]' ),
																	'value' 		=> $dsc_price,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '10.5'
																]); ?>
															</td>
															<td width="26%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-min',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][min][]' ),
																	'value' 		=> $min,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '1'
																]); ?>
															</td>
															<td width="26%" class="ovabrw-input-price">
																<?php ovabrw_wp_text_input([
																	'type' 			=> 'text',
																	'class' 		=> 'ovabrw-input-required ovabrw-special-discount-max',
																	'name' 			=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][max][]' ),
																	'value' 		=> $max,
																	'data_type' 	=> 'price',
																	'placeholder' 	=> '2'
																]); ?>
															</td>
															<td width="22%">
																<?php ovabrw_wp_select_input([
																	'class' 	=> 'ovabrw-input-required ovabrw-special-discount-duration',
																	'name' 		=> $this->get_meta_name( 'rt_discount['.esc_attr( $i ).'][duration_type][]' ),
																	'value' 	=> $duration,
																	'options' 	=> [
																		'days' 	=> esc_html__( 'Day(s)', 'ova-brw' ),
																		'hours' => esc_html__( 'Hour(s)', 'ova-brw' )
																	]
																]); ?>
															</td>
															<td width="1%">
																<button class="button ovabrw-remove-st-discount">x</button>
															</td>
														</tr>
													<?php endforeach;
												endif;
											endif; ?>
										</tbody>
										<tfoot>
											<tr>
												<th colspan="5">
													<button class="button ovabrw-add-st-discount">
														<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
													</button>
												</th>
											</tr>
										</tfoot>
							      	</table>
							    </td>
							    <td width="1%">
							    	<button class="button ovabrw-remove-special-time">x</button>
							    </td>
							</tr>
					<?php endforeach;
					endif; ?>
				<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<?php if ( $this->is_type( 'mixed' ) ): ?>
							<th colspan="6">
						<?php else: ?>
							<th colspan="5">
						<?php endif; ?>
							<button class="button ovabrw-add-special-time" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-special-time-field.php' );
								echo esc_attr( ob_get_clean() );
							?>" data-add-new-discount="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-special-time-discount-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add ST', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>