<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get package ids
$package_ids = $this->get_meta_value( 'petime_id' );

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Packages', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>

	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-period-time">
			<?php woocommerce_wp_select([
				'id' 			=> $this->get_meta_name('unfixed_time'),
				'class' 		=> 'ovabrw-input-required',
				'wrapper_class' => 'ovabrw-required',
				'label' 		=> esc_html__( 'Package Type', 'ova-brw' ),
				'placeholder' 	=> '',
				'options' 		=> [
					'no' 	=> esc_html__( 'Fixed Hour', 'ova-brw' ),
					'yes' 	=> esc_html__( 'UnFixed Hour', 'ova-brw' )
				],
				'desc_tip'		=> true,
				'description'	=> esc_html__( 'Fixed Hour: you cannot choose hour in booking form. UnFixed Hour: You can choose hour in booking form', 'ova-brw' ),
				'value' 		=> $this->get_meta_value( 'unfixed_time', 'no' )
			]); ?>
			<table class="widefat">
				<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'ID', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Price', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Type', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Name', 'ova-brw' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Discounts', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody class="ovabrw-period-time-item">
				<?php if ( ovabrw_array_exists( $package_ids ) ):
					// Date & Time format
					$date_format = OVABRW()->options->get_date_format();
					$time_format = OVABRW()->options->get_time_format();

					// Package data
					$package_prices 	= $this->get_meta_value( 'petime_price' );
					$package_types 		= $this->get_meta_value( 'package_type' );
					$package_days 		= $this->get_meta_value( 'petime_days' );
					$package_hours 		= $this->get_meta_value( 'pehour_unfixed' );
					$package_start_time = $this->get_meta_value( 'pehour_start_time' );
					$package_end_time 	= $this->get_meta_value( 'pehour_end_time' );
					$package_labels 	= $this->get_meta_value( 'petime_label' );
					$package_discounts 	= $this->get_meta_value( 'petime_discount');

					foreach ( $package_ids as $i => $id ):
						$price 		= ovabrw_get_meta_data( $i, $package_prices );
						$type 		= ovabrw_get_meta_data( $i, $package_types );
						$day 		= ovabrw_get_meta_data( $i, $package_days );
						$hour 		= ovabrw_get_meta_data( $i, $package_hours );
						$start_time = ovabrw_get_meta_data( $i, $package_start_time );
						$end_time 	= ovabrw_get_meta_data( $i, $package_end_time );
						$label 		= ovabrw_get_meta_data( $i, $package_labels );
						$discounts 	= ovabrw_get_meta_data( $i, $package_discounts );
					?>
						<tr>
							<td width="10%">
								<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name( 'petime_id[]' ),
									'value' 		=> $id,
									'placeholder' 	=> esc_html__( 'Not space', 'ova-brw' )
								]); ?>
						    </td>
						    <td width="10%" class="ovabrw-input-price">
						    	<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name( 'petime_price[]' ),
									'value' 		=> $price,
									'data_type' 	=> 'price',
									'placeholder' 	=> '10.5'
								]); ?>
						    </td>
						    <td width="18%" class="ovabrw-input-price">
						    	<?php ovabrw_wp_select_input([
						    		'class' 	=> 'ovabrw-input-required',
									'name' 		=> $this->get_meta_name( 'package_type[]' ),
									'value' 	=> $type,
									'options' 	=> [
										'inday' => esc_html__( 'Hour', 'ova-brw' ),
										'other' => esc_html__( 'Day', 'ova-brw' )
									]
								]); ?>
						    	<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-hidden',
									'name' 			=> $this->get_meta_name( 'petime_days[]' ),
									'value' 		=> $day,
									'data_type' 	=> 'price',
									'placeholder' 	=> esc_html__( 'Total Day', 'ova-brw' )
								]); ?>
						        <div class="ovabrw-period-hours">
						        	<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-hidden start-time',
										'name' 			=> $this->get_meta_name( 'pehour_start_time[]' ),
										'value' 		=> $start_time,
										'data_type' 	=> 'timepicker',
										'placeholder' 	=> esc_html__( 'Start Hour', 'ova-brw' )
									]); ?>
									<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-hidden end-time',
										'name' 			=> $this->get_meta_name( 'pehour_end_time[]' ),
										'value' 		=> $end_time,
										'data_type' 	=> 'timepicker',
										'placeholder' 	=> esc_html__( 'End Hour', 'ova-brw' )
									]); ?>
						        </div>
						        <?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-hidden',
									'name' 			=> $this->get_meta_name( 'pehour_unfixed[]' ),
									'value' 		=> $hour,
									'data_type' 	=> 'price',
									'placeholder' 	=> esc_html__( 'Total Hour', 'ova-brw' )
								]); ?>
						    </td>
						    <td width="20%">
						    	<?php ovabrw_wp_text_input([
						    		'class' 		=> 'ovabrw-input-required',
									'type' 			=> 'text',
									'name' 			=> $this->get_meta_name( 'petime_label[]' ),
									'value' 		=> $label,
									'placeholder' 	=> esc_html__( 'Text', 'ova-brw' )
								]); ?>
						    </td>
						    <td width="41%" class="ovabrw-period-discounts">
						    	<table class="widefat">
							      	<thead>
										<tr>
											<th class="ovabrw-required">
												<?php esc_html_e( 'Price', 'ova-brw' ); ?>
											</th>
											<th class="ovabrw-required">
												<?php esc_html_e( 'Start Time', 'ova-brw' ); ?>
											</th>
											<th class="ovabrw-required">
												<?php esc_html_e( 'End Time', 'ova-brw' ); ?>
											</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
									<?php if ( ovabrw_array_exists( $discounts ) ):
										// Discount prices
										$dsc_prices = ovabrw_get_meta_data( 'price', $discounts );

										// Discount start date
										$dsc_start_date = ovabrw_get_meta_data( 'start_time', $discounts );

										// Discount end date
										$dsc_end_date = ovabrw_get_meta_data( 'end_time', $discounts );

										if ( ovabrw_array_exists( $dsc_prices ) ):
											foreach ( $dsc_prices as $k => $dsc_price ):
												$dsc_start 	= ovabrw_get_meta_data( $k, $dsc_start_date );
												$dsc_end 	= ovabrw_get_meta_data( $k, $dsc_end_date );
											?>
												<tr>				
													<td width="20%" class="ovabrw-input-price">
														<?php ovabrw_wp_text_input([
															'type' 			=> 'text',
															'class' 		=> 'ovabrw-input-required ovabrw-period-discount-price',
															'name' 			=> $this->get_meta_name( 'petime_discount['.esc_attr( $i ).'][price][]' ),
															'value' 		=> $dsc_price,
															'data_type' 	=> 'price',
															'placeholder' 	=> '10.5'
														]); ?>
													</td>
													<td width="39.5%">
														<?php ovabrw_wp_text_input([
															'type' 		=> 'text',
															'id' 		=> ovabrw_unique_id( 'period_from' ),
															'class' 	=> 'ovabrw-input-required start-date',
															'name' 		=> $this->get_meta_name( 'petime_discount['.esc_attr( $i ).'][start_time][]' ),
															'value' 	=> strtotime( $dsc_start ) ? gmdate( $date_format . ' ' .$time_format, strtotime( $dsc_start ) ) : '',
															'data_type' => 'datetimepicker',
															'attrs' 	=> [
																'data-date' => strtotime( $dsc_start ) ? gmdate( $date_format, strtotime( $dsc_start ) ) : '',
																'data-time' => strtotime( $dsc_start ) ? gmdate( $time_format, strtotime( $dsc_start ) ) : ''
															]
														]); ?>
													</td>
													<td width="39.5%">
														<?php ovabrw_wp_text_input([
															'type' 		=> 'text',
															'id' 		=> ovabrw_unique_id( 'period_to' ),
															'class' 	=> 'ovabrw-input-required end-date',
															'name' 		=> $this->get_meta_name( 'petime_discount['.esc_attr( $i ).'][end_time][]' ),
															'value' 	=> strtotime( $dsc_end ) ? gmdate( $date_format . ' ' .$time_format, strtotime( $dsc_end ) ) : '',
															'data_type' => 'datetimepicker',
															'attrs' 	=> [
																'data-date' => strtotime( $dsc_end ) ? gmdate( $date_format, strtotime( $dsc_end ) ) : '',
																'data-time' => strtotime( $dsc_end ) ? gmdate( $time_format, strtotime( $dsc_end ) ) : ''
															]
														]); ?>
													</td>
													<td width="1%">
														<button class="button ovabrw-remove-pt-discount">x</button>
													</td>
												</tr>
											<?php endforeach;
										endif; 
									endif; ?>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="4">
												<button class="button ovabrw-add-pt-discount">
													<?php esc_html_e( 'Add Discount', 'ova-brw' ); ?>
												</button>
											</th>
										</tr>
									</tfoot>
						      	</table>
						    </td>
						    <td width="1%">
						    	<button class="button ovabrw-remove-package">x</button>
						    </td>
						</tr>
					<?php endforeach;
				endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">
							<button class="button ovabrw-add-package" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-package-field.php' );
								echo esc_attr( ob_get_clean() );
							?>" data-add-new-discount="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-package-discount.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add Package', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>