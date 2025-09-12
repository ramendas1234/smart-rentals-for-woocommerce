<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get locations
$locations = OVABRW()->options->get_locations();

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Locations', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-locations">
			<table class="widefat">
				<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
						</th>
						<th>
							<?php esc_html_e( 'Price', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( ovabrw_array_exists( $locations ) ):
					$pickup_locations 	= $this->get_meta_value( 'st_pickup_loc' );
					$dropoff_locations 	= $this->get_meta_value( 'st_dropoff_loc' );
					$price_locations 	= $this->get_meta_value( 'st_price_location' );

					if ( ovabrw_array_exists( $pickup_locations ) ):
						foreach ( $pickup_locations as $i => $pickup_location ):
							$dropoff_location 	= ovabrw_get_meta_data( $i, $dropoff_locations );
							$price_location 	= ovabrw_get_meta_data( $i, $price_locations );
				?>
							<tr>
								<td width="40%">
									<?php ovabrw_wp_select_input([
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'st_pickup_loc[]' ),
										'value' 		=> $pickup_location,
										'placeholder' 	=> esc_html__( 'Select Location', 'ova-brw' ),
										'options' 		=> $locations
									]); ?>
							    </td>
							    <td width="40%">
							    	<?php ovabrw_wp_select_input([
							    		'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'st_dropoff_loc[]' ),
										'value' 		=> $dropoff_location,
										'placeholder' 	=> esc_html__( 'Select Location', 'ova-brw' ),
										'options' 		=> $locations
									]); ?>
							    </td>
							    <td width="19%" class="ovabrw-input-price">
							    	<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-price',
										'name' 			=> $this->get_meta_name( 'st_price_location[]' ),
										'value' 		=> $price_location,
										'data_type' 	=> 'price',
										'placeholder' 	=> '10'
									]); ?>
							    </td>
							    <td width="1%">
							    	<button class="button ovabrw-remove-location">x</button>
							    </td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				<?php endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4">
							<button class="button ovabrw-add-location" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-location-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add Location', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>