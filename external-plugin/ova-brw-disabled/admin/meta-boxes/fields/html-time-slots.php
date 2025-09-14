<?php if ( !defined( 'ABSPATH' ) ) exit();

// Use location
$use_location = $this->get_meta_value( 'use_location' );

// Daily
$daily = [
	'monday' 	=> esc_html__( 'Monday', 'ova-brw' ),
	'tuesday'	=> esc_html__( 'Tuesday', 'ova-brw' ),
	'wednesday' => esc_html__( 'Wednesday', 'ova-brw' ),
	'thursday' 	=> esc_html__( 'Thursday', 'ova-brw' ),
	'friday'	=> esc_html__( 'Friday', 'ova-brw' ),
	'saturday'	=> esc_html__( 'Saturday', 'ova-brw' ),
	'sunday'	=> esc_html__( 'Sunday', 'ova-brw' )
];

// Get data time slots
$timeslots_labels 	= $this->get_meta_value( 'time_slots_label' );
$timeslots_location = $this->get_meta_value( 'time_slots_location' );
$timeslots_start 	= $this->get_meta_value( 'time_slots_start' );
$timeslots_end 		= $this->get_meta_value( 'time_slots_end' );
$timeslots_prices 	= $this->get_meta_value( 'time_slots_price' );
$timeslots_quantity = $this->get_meta_value( 'time_slots_quantity' );

?>

<div id="ovabrw-time-slots" class="ovabrw-time-slots">
	<label class="ovabrw-use-location">
		<input
			type="checkbox"
			name="<?php echo esc_attr( $this->get_meta_name( 'use_location' ) ); ?>"
			value="1"
			<?php checked( $use_location, 1 ); ?>
		/>
		<?php esc_html_e( 'Do you want to use location?', 'ova-brw' ); ?>
	</label>
	<?php foreach ( $daily as $day => $label_day ): // Loop daily
		$labels 	= ovabrw_get_meta_data( $day, $timeslots_labels, [] );
		$locations 	= ovabrw_get_meta_data( $day, $timeslots_location, [] );
		$start_time = ovabrw_get_meta_data( $day, $timeslots_start, [] );
		$end_time 	= ovabrw_get_meta_data( $day, $timeslots_end, [] );
		$prices 	= ovabrw_get_meta_data( $day, $timeslots_prices, [] );
		$qtys 		= ovabrw_get_meta_data( $day, $timeslots_quantity, [] );
	?>
		<div id="ovabrw-every-<?php echo esc_attr( $day ); ?>" class="ovabrw-daily">
			<h3 class="ovabrw-daily-label">
				<?php echo esc_html( $label_day ); ?>
			</h3>
			<table class="widefat">
				<thead>
					<th>
						<?php esc_html_e( 'Label (option)', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required heading-timeslot-location">
						<?php esc_html_e( 'Location', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required">
						<?php esc_html_e( 'Start', 'ova-brw' ); ?>
					</th>
					<th>
						<?php esc_html_e( 'End', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required">
						<?php echo sprintf( esc_html__( 'Price (%s)', 'ova-brw' ), esc_html( get_woocommerce_currency_symbol() ) ); ?>
					</th>
					<th class="ovabrw-required">
						<?php esc_html_e( 'Quantity', 'ova-brw' ); ?>
					</th>
					<th></th>
					<th></th>
				</thead>
				<tbody class="ovabrw-sortable">
				<?php if ( ovabrw_array_exists( $start_time ) ):
					foreach ( $start_time as $k => $start ):
						$label 		= ovabrw_get_meta_data( $k, $labels );
						$location 	= ovabrw_get_meta_data( $k, $locations );
						$end 		= ovabrw_get_meta_data( $k, $end_time );
						$price 		= ovabrw_get_meta_data( $k, $prices );
						$qty 		= ovabrw_get_meta_data( $k, $qtys );
					?>
						<tr>
							<td width="21%" class="timeslot-label">
						    	<?php ovabrw_wp_text_input([
						    		'type' 			=> 'text',
									'class' 		=> 'timeslot-label',
									'name' 			=> $this->get_meta_name( 'time_slots_label['.esc_attr( $day ).'][]' ),
									'value' 		=> $label,
									'placeholder' 	=> esc_html__( '...', 'ova-brw' )
						    	]); ?>
						    </td>
						    <td width="21%" class="timeslot-location">
						    	<?php ovabrw_wp_select_input([
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name( 'time_slots_location['.esc_attr( $day ).'][]' ),
									'value' 		=> $location,
									'options' 		=> OVABRW()->options->get_locations(),
									'placeholder' 	=> esc_html__( 'Select location', 'ova-brw' )
						    	]); ?>
						    </td>
						    <td width="14%" class="timeslot-start">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'class' 	=> 'ovabrw-input-required start-time',
									'name' 		=> $this->get_meta_name( 'time_slots_start['.esc_attr( $day ).'][]' ),
									'value' 	=> $start,
									'data_type' => 'timepicker'
								]); ?>
							</td>
							<td width="14%" class="timeslot-end">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'class' 	=> 'end-time',
									'name' 		=> $this->get_meta_name( 'time_slots_end['.esc_attr( $day ).'][]' ),
									'value' 	=> $end,
									'data_type' => 'timepicker'
								]); ?>
							</td>
							<td width="14%" class="ovabrw-input-price timeslot-price">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'class' 	=> 'ovabrw-input-required',
									'name' 		=> $this->get_meta_name( 'time_slots_price['.esc_attr( $day ).'][]' ),
									'value' 	=> $price,
									'data_type' => 'price'
								]); ?>
							</td>
							<td width="14%" class="ovabrw-input-price timeslot-quantity">
								<?php ovabrw_wp_text_input([
									'type' 			=> 'number',
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name( 'time_slots_quantity['.esc_attr( $day ).'][]' ),
									'value' 		=> $qty,
									'placeholder' 	=> 10
								]); ?>
							</td>
							<td width="1%" class="ovabrw-sort-icon">
								<span class="dashicons dashicons-menu"></span>
							</td>
							<td width="1%">
								<button class="button ovabrw-remove-time-slot">X</button>
							</td>
						</tr>
					<?php endforeach;
				endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="7">
							<button class="button ovabrw-add-time-slot">
								<?php esc_html_e( 'Add time slot', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
				<input
					type="hidden"
					name="ovabrw-day-of-week"
					value="<?php echo esc_attr( $day ); ?>"
				/>
			</table>
		</div>
	<?php endforeach; // End loop ?>
	<input
		type="hidden"
		name="ovabrw-time-slots-row"
		data-row="
		<?php
			ob_start();
			include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-time-slots-field.php' );
			echo esc_attr( ob_get_clean() );
		?>"
	/>
</div>