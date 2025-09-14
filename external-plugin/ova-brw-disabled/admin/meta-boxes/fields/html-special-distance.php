<?php if ( !defined( 'ABSPATH' ) ) exit();

// Special time start date
$special_startdate = $this->get_meta_value( 'st_pickup_distance' );

// Price by km/mi
$price_by = $this->get_meta_value( 'map_price_by', 'km' );
if ( !$price_by ) $price_by = 'km';

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Special Time', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-special-distance">
			<table class="widefat">
				<thead>
					<th class="ovabrw-required">
						<?php esc_html_e( 'Start Date', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required">
						<?php esc_html_e( 'End Date', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required">
						<?php echo sprintf( esc_html__( 'Price/%s', 'ova-brw' ), $price_by ); ?>
					</th>
					<th class="ovabrw-required">
						<?php esc_html_e( 'Discounts', 'ova-brw' ); ?>
					</th>
					<th></th>
				</thead>
				<tbody class="ovabrw-special-distance-item">
				<?php if ( ovabrw_array_exists( $special_startdate ) ):
					// Date format
					$date_format = OVABRW()->options->get_date_format();

					// Time format
					$time_format = OVABRW()->options->get_time_format();

					// Special time end date
					$special_enddate = $this->get_meta_value( 'st_pickoff_distance' );

					// Special time price
					$special_price = $this->get_meta_value( 'st_price_distance' );

					// Speical time discount
					$special_discounts = $this->get_meta_value( 'st_discount_distance' );

					foreach ( $special_startdate as $k => $start_date ):
						$end_date 	= ovabrw_get_meta_data( $k, $special_enddate );
						$price 		= ovabrw_get_meta_data( $k, $special_price );
						$dsc_from 	= isset( $special_discounts[$k]['from'] ) ? $special_discounts[$k]['from'] : '';
						$dsc_to 	= isset( $special_discounts[$k]['to'] ) ? $special_discounts[$k]['to'] : '';
						$dsc_price 	= isset( $special_discounts[$k]['price'] ) ? $special_discounts[$k]['price'] : '';
					?>
						<tr>
							<td width="20%">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'id' 		=> ovabrw_unique_id( 'special_from' ),
									'class' 	=> 'ovabrw-input-required start-date',
									'name' 		=> $this->get_meta_name( 'st_pickup_distance[]' ),
									'value' 	=> $start_date,
									'data_type' => 'datetimepicker',
									'attrs' 	=> [
										'data-date' => strtotime( $start_date ) ? gmdate( $date_format, strtotime( $start_date ) ) : '',
										'data-time' => strtotime( $start_date ) ? gmdate( $time_format, strtotime( $start_date ) ) : ''
									]
								]); ?>
							</td>
							<td width="20%">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'id' 		=> ovabrw_unique_id( 'special_to' ),
									'class' 	=> 'ovabrw-input-required end-date',
									'name' 		=> $this->get_meta_name( 'st_pickoff_distance[]' ),
									'value' 	=> $end_date,
									'data_type' => 'datetimepicker',
									'attrs' 	=> [
										'data-date' => strtotime( $end_date ) ? gmdate( $date_format, strtotime( $end_date ) ) : '',
										'data-time' => strtotime( $end_date ) ? gmdate( $time_format, strtotime( $end_date ) ) : ''
									]
								]); ?>
							</td>
							<td width="14%" class="ovabrw-input-price">
								<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name('st_price_distance[]'),
									'value' 		=> $price,
									'data_type' 	=> 'price',
									'placeholder' 	=> esc_html__( 'Price', 'ova-brw' )
								]); ?>
							</td>
							<td width="45%" class="ovabrw-special-distance-discount">
								<table class="widefat">
									<thead>
										<tr>
											<th class="ovabrw-required">
												<?php echo sprintf( esc_html__( 'From (%s)', 'ova-brw' ), $price_by ); ?>
											</th>
											<th class="ovabrw-required">
												<?php echo sprintf( esc_html__( 'To (%s)', 'ova-brw' ), $price_by ); ?>
											</th>
											<th class="ovabrw-required">
												<?php echo sprintf( esc_html__( 'Price/%s', 'ova-brw' ), $price_by ); ?>
											</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
									<?php if ( ovabrw_array_exists( $dsc_from ) ):
										foreach ( $dsc_from as $k_dsc => $from ):
											$to 	= ovabrw_get_meta_data( $k_dsc, $dsc_to );
											$price 	= ovabrw_get_meta_data( $k_dsc, $dsc_price );
										?>
											<tr>
												<td width="30%" class="ovabrw-input-price">
													<?php ovabrw_wp_text_input([
														'type' 			=> 'text',
														'class' 		=> 'ovabrw-input-required ovabrw-special-distance-discount-from',
														'name' 			=> $this->get_meta_name( 'st_discount_distance['.esc_attr( $k ).'][from][]' ),
														'value' 		=> $from,
														'data_type' 	=> 'price',
														'placeholder' 	=> esc_html__( 'Number', 'ova-brw' )
													]); ?>
												</td>
												<td width="30%" class="ovabrw-input-price">
													<?php ovabrw_wp_text_input([
														'type' 			=> 'text',
														'class' 		=> 'ovabrw-input-required ovabrw-special-distance-discount-to',
														'name' 			=> $this->get_meta_name( 'st_discount_distance['.esc_attr( $k ).'][to][]' ),
														'value' 		=> $to,
														'data_type' 	=> 'price',
														'placeholder' 	=> esc_html__( 'Number', 'ova-brw' )
													]); ?>
												</td>
												<td width="30%" class="ovabrw-input-price">
													<?php ovabrw_wp_text_input([
														'type' 			=> 'text',
														'class' 		=> 'ovabrw-input-required ovabrw-special-distance-discount-price',
														'name' 			=> $this->get_meta_name( 'st_discount_distance['.esc_attr( $k ).'][price][]' ),
														'value' 		=> $price,
														'data_type' 	=> 'price',
														'placeholder' 	=> esc_html__( 'Price', 'ova-brw' )
													]); ?>
												</td>
												<td width="1%">
													<button class="button ovabrw-remove-st-discount-distance">x</button>
												</td>
											</tr>
										<?php endforeach;
									endif; ?>
									</tbody>
									<tfoot>
										<tr>
											<th colspan="4">
												<button class="button ovabrw-add-st-discount-distance">
													<?php esc_html_e( 'Add Discount', 'ova-brw' ); ?>
												</button>
											</th>
										</tr>
									</tfoot>
								</table>
							</td>
							<td width="1%">
								<button class="button ovabrw-remove-st-distance">x</button>
							</td>
						</tr>
					<?php endforeach;
				endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5">
							<button class="button ovabrw-add-st-distance" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-special-distance-field.php' );
								echo esc_attr( ob_get_clean() );
							?>" data-add-new-discount="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-special-distance-discount.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add Special Time', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>