<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get locations
$locations = OVABRW()->options->get_locations();
if ( !ovabrw_array_exists( $locations ) ) $locations = [];

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Location Price', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-locations-price">
			<table class="widefat">
				<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Price', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Time(minute)', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( ovabrw_array_exists( $locations ) ):
					$pickup_locations 	= $this->get_meta_value( 'pickup_location' );
					$dropoff_locations 	= $this->get_meta_value( 'dropoff_location' );
					$location_prices 	= $this->get_meta_value( 'price_location' );
					$location_times 	= $this->get_meta_value( 'location_time' );

					if ( ovabrw_array_exists( $pickup_locations ) ):
						foreach ( $pickup_locations as $i => $pickup_location ):
							$dropoff_location 	= ovabrw_get_meta_data( $i, $dropoff_locations );
							$location_price 	= ovabrw_get_meta_data( $i, $location_prices );
							$location_time 		= ovabrw_get_meta_data( $i, $location_times );
						?>
							<tr>
							    <td width="30%">
							    	<?php ovabrw_wp_select_input([
							    		'class' 		=> 'ovabrw-input-required',
							    		'name' 			=> $this->get_meta_name( 'pickup_location[]' ),
							    		'value' 		=> $pickup_location,
							    		'placeholder' 	=> esc_html__( 'Select Location', 'ova-brw' ),
							    		'options' 		=> $locations
							    	]); ?>
							    </td>
							    <td width="30%">
							    	<?php ovabrw_wp_select_input([
							    		'class' 		=> 'ovabrw-input-required',
							    		'name' 			=> $this->get_meta_name( 'dropoff_location[]' ),
							    		'value' 		=> $dropoff_location,
							    		'placeholder' 	=> esc_html__( 'Select Location', 'ova-brw' ),
							    		'options' 		=> $locations
							    	]); ?>
							    </td>
							    <td width="20%" class="ovabrw-input-price">
							    	<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'price_location[]' ),
										'value' 		=> $location_price,
										'data_type' 	=> 'price',
										'placeholder' 	=> '10'
									]); ?>
							    </td>
							    <td width="19%" class="ovabrw-input-price">
							    	<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'location_time[]' ),
										'value' 		=> $location_time,
										'data_type' 	=> 'price',
										'placeholder' 	=> '60'
									]); ?>
							    </td>
							    <td width="1%">
							    	<button class="button ovabrw-remove-location-price">x</button>
							    </td>
							</tr>
						<?php endforeach;
					endif;
				endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5">
							<button class="button ovabrw-add-location-price" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-price-location-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add Location Price', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>