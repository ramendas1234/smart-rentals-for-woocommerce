<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get special price
$special_prices = $this->get_meta_value( 'special_price' );

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
			<table class="widefat">
				<thead>
					<th class="ovabrw-required">
						<?php esc_html_e( 'Price', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required">
						<?php esc_html_e( 'Start Date', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required">
						<?php esc_html_e( 'End Date', 'ova-brw' ); ?>
					</th>
					<th></th>
				</thead>
				<tbody>
				<?php if ( ovabrw_array_exists( $special_prices ) ):
					// Date format
					$date_format = OVABRW()->options->get_date_format();

					// Time format
					$time_format = OVABRW()->options->get_time_format();

					// Start date
					$special_startdate = $this->get_meta_value( 'special_startdate' );

					// End date
					$special_enddate = $this->get_meta_value( 'special_enddate' );

					foreach ( $special_prices as $k => $price ):
						$start_date = ovabrw_get_meta_data( $k, $special_startdate );
						$end_date 	= ovabrw_get_meta_data( $k, $special_enddate );
				?>
					<tr>
						<td width="33%" class="ovabrw-input-price">
							<?php ovabrw_wp_text_input([
								'type' 			=> 'text',
								'class' 		=> 'ovabrw-input-required',
								'name' 			=> $this->get_meta_name( 'special_price[]' ),
								'value' 		=> $price,
								'data_type' 	=> 'price',
								'placeholder' 	=> 10.00
							]); ?>
						</td>
						<td width="33%">
							<?php ovabrw_wp_text_input([
								'type' 		=> 'text',
								'id' 		=> ovabrw_unique_id( 'special_startdate' ),
								'class' 	=> 'ovabrw-input-required start-date',
								'name' 		=> $this->get_meta_name( 'special_startdate[]' ),
								'value' 	=> $start_date,
								'data_type' => 'datetimepicker',
								'attrs' 	=> [
									'data-date' => strtotime( $start_date ) ? gmdate( $date_format, strtotime( $start_date ) ) : '',
									'data-time' => strtotime( $start_date ) ? gmdate( $time_format, strtotime( $start_date ) ) : ''
								]
							]); ?>
					    </td>
					    <td width="33%">
							<?php ovabrw_wp_text_input([
								'type' 		=> 'text',
								'id' 		=> ovabrw_unique_id( 'special_enddate' ),
								'class' 	=> 'ovabrw-input-required end-date',
								'name' 		=> $this->get_meta_name( 'special_enddate[]' ),
								'value' 	=> $end_date,
								'data_type' => 'datetimepicker',
								'attrs' 	=> [
									'data-date' => strtotime( $end_date ) ? gmdate( $date_format, strtotime( $end_date ) ) : '',
									'data-time' => strtotime( $end_date ) ? gmdate( $time_format, strtotime( $end_date ) ) : ''
								]
							]); ?>
					    </td>
					    <td width="1%">
					    	<button class="button ovabrw-appointment-remove-special-time">x</button>
					    </td>
					</tr>
				<?php endforeach;
				endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4">
							<button class="button ovabrw-appointment-add-special-time" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-special-timeslots-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add special time', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>