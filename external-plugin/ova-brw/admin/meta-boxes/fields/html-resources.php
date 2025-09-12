<?php if ( !defined( 'ABSPATH' ) ) exit();

// Resource IDs
$resc_ids = $this->get_meta_value( 'resource_id' );

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Resources', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-resources">
			<span style="display: block; margin-bottom: 10px;">
		        <em><?php esc_html_e( 'Quantity: maximum per booking', 'ova-brw' ) ?></em>
		    </span>
			<table class="widefat">
				<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Unique ID', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Name', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Price', 'ova-brw' ); ?>
						</th>
						<th><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Applicable', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( ovabrw_array_exists( $resc_ids ) ):
					$resc_names 	= $this->get_meta_value( 'resource_name' );
					$resc_prices 	= $this->get_meta_value( 'resource_price' );
					$resc_quantity 	= $this->get_meta_value( 'resource_quantity' );
					$resc_durations = $this->get_meta_value( 'resource_duration_type' );

					// Durations
					$durations = [];

					if ( $this->is_type( 'day' ) ) {
						$durations = [
							'days' 	=> esc_html__( '/Day', 'ova-brw' ),
							'total' => esc_html__( '/Order', 'ova-brw' )
						];
					} elseif ( $this->is_type( 'hotel' ) ) {
						$durations = [
							'days' 	=> esc_html__( '/Night', 'ova-brw' ),
							'total' => esc_html__( '/Order', 'ova-brw' )
						];
					} elseif ( $this->is_type( 'hour' ) ) {
						$durations = [
							'hours' => esc_html__( '/Hour', 'ova-brw' ),
							'total' => esc_html__( '/Order', 'ova-brw' )
						];
					} elseif ( $this->is_type( 'mixed' ) || $this->is_type( 'period_time' ) ) {
						$durations = [
							'days' 	=> esc_html__( '/Day', 'ova-brw' ),
							'hours' => esc_html__( '/Hour', 'ova-brw' ),
							'total' => esc_html__( '/Order', 'ova-brw' )
						];
					} else {
						$durations = [
							'total' => esc_html__( '/Order', 'ova-brw' )
						];
					}
					
					foreach ( $resc_ids as $i => $id ): ?>
						<tr>
							<td width="15%">
								<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name('resource_id[]'),
									'value' 		=> $id,
									'placeholder' 	=> esc_html__( 'Not space', 'ova-brw' )
								]); ?>
						    </td>
						    <td width="29%">
						      	<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name('resource_name[]'),
									'value' 		=> ovabrw_get_meta_data( $i, $resc_names ),
									'placeholder' 	=> esc_html__( 'Name', 'ova-brw' )
								]); ?>
						    </td>
						    <td width="25%" class="ovabrw-input-price">
						      	<?php ovabrw_wp_text_input([
									'type' 			=> 'text',
									'class' 		=> 'ovabrw-input-required',
									'name' 			=> $this->get_meta_name('resource_price[]'),
									'value' 		=> ovabrw_get_meta_data( $i, $resc_prices ),
									'data_type' 	=> 'price',
									'placeholder' 	=> '10.5'
								]); ?>
						    </td>
						    <td width="15%">
						    	<?php ovabrw_wp_text_input([
									'type' 			=> 'number',
									'name' 			=> $this->get_meta_name('resource_quantity[]'),
									'value' 		=> ovabrw_get_meta_data( $i, $resc_quantity ),
									'placeholder' 	=> esc_html__( 'Number', 'ova-brw' )
								]); ?>
						    </td>
						    <td width="15%">
						    	<?php ovabrw_wp_select_input([
						    		'class' 	=> 'ovabrw-input-required',
						    		'name' 		=> $this->get_meta_name( 'resource_duration_type[]' ),
						    		'value' 	=> ovabrw_get_meta_data( $i, $resc_durations ),
						    		'options' 	=> $durations
						    	]); ?>
						    </td>
						    <td width="1%">
						    	<button class="button ovabrw-remove-resource">x</button>
						    </td>
						</tr>
					<?php endforeach;
				endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">
							<button class="button ovabrw-add-resource" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-resource-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add Resource', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>