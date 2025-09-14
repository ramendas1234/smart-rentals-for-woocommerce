<?php if ( !defined( 'ABSPATH' ) ) exit();

// Service labels
$serv_labels = $this->get_meta_value( 'label_service' );

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Services', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-services">
			<span style="display: block; margin-bottom: 10px;">
		        <em><?php esc_html_e( 'Quantity: maximum per booking', 'ova-brw' ) ?></em>
		    </span>
			<div class="ovabrw-service-items">
			<?php if ( ovabrw_array_exists( $serv_labels ) ):
		    	$serv_required 	= $this->get_meta_value( 'service_required' );
		    	$serv_ids 		= $this->get_meta_value( 'service_id' );
				$serv_names 	= $this->get_meta_value( 'service_name' );
				$serv_prices 	= $this->get_meta_value( 'service_price' );
				$serv_qtys 		= $this->get_meta_value( 'service_qty' );
				$serv_types 	= $this->get_meta_value( 'service_duration_type' );

				// Durations
				$durations = [
					'total' => esc_html__( '/Order', 'ova-brw' )
				];

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
				}

				foreach ( $serv_labels as $i => $label ):
					$required 	= ovabrw_get_meta_data( $i, $serv_required, 'no' );
					$ids 		= ovabrw_get_meta_data( $i, $serv_ids );
					$names 		= ovabrw_get_meta_data( $i, $serv_names );
					$prices 	= ovabrw_get_meta_data( $i, $serv_prices );
					$qtys 		= ovabrw_get_meta_data( $i, $serv_qtys );
					$types 		= ovabrw_get_meta_data( $i, $serv_types );
				?>
					<div class="ovabrw-service-item">
						<div class="ovabrw-service-head">
							<div class="ovabrw-service-label">
								<span class="ovabrw-required">
									<?php esc_html_e( 'Label', 'ova-brw' ); ?>
								</span>
								<?php ovabrw_wp_text_input([
									'type' 	=> 'text',
									'class' => 'ovabrw-input-required',
									'name' 	=> $this->get_meta_name( 'label_service[]' ),
									'value' => $label
								]); ?>
								<span class="ovabrw-required">
									<?php esc_html_e( 'Required', 'ova-brw' ); ?>
								</span>
								<?php ovabrw_wp_select_input([
									'class' 	=> 'ovabrw-input-required',
									'name' 		=> $this->get_meta_name( 'service_required[]' ),
									'value' 	=> $required,
									'options' 	=> [
										'yes' 	=> esc_html__( 'Yes', 'ova-brw' ),
										'no' 	=> esc_html__( 'No', 'ova-brw' ),
									]
								]); ?>
							</div>
							<button class="button ovabrw-remove-service-group">x</button>
						</div>
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
							<?php if ( ovabrw_array_exists( $ids ) ):
								foreach ( $ids as $k => $id ): ?>
									<tr>
									    <td width="15%">
									    	<?php ovabrw_wp_text_input([
									    		'type' 			=> 'text',
									    		'class' 		=> 'ovabrw-input-required ovabrw-service-id',
									    		'name' 			=> $this->get_meta_name( 'service_id['.esc_attr( $i ).'][]' ),
									    		'value' 		=> $id,
									    		'placeholder' 	=> esc_html__( 'Not space', 'ova-brw' )
									    	]); ?>
									    </td>
									    <td width="34%">
									    	<?php ovabrw_wp_text_input([
									    		'type' 			=> 'text',
									    		'class' 		=> 'ovabrw-input-required ovabrw-service-name',
									    		'name' 			=> $this->get_meta_name( 'service_name['.esc_attr( $i ).'][]' ),
									    		'value' 		=> ovabrw_get_meta_data( $k, $names ),
									    		'placeholder' 	=> esc_html__( 'Name', 'ova-brw' )
									    	]); ?>
									    </td>
									    <td width="20%" class="ovabrw-input-price">
									    	<?php ovabrw_wp_text_input([
									    		'type' 			=> 'text',
									    		'class' 		=> 'ovabrw-input-required ovabrw-service-price',
									    		'name' 			=> $this->get_meta_name( 'service_price['.esc_attr( $i ).'][]' ),
									    		'value' 		=> ovabrw_get_meta_data( $k, $prices ),
									    		'data_type' 	=> 'price',
									    		'placeholder' 	=> esc_html__( 'Price', 'ova-brw' )
									    	]); ?>
									    </td>
									    <td width="15%">
									    	<?php ovabrw_wp_text_input([
									    		'type' 			=> 'number',
									    		'class' 		=> 'ovabrw-service-qty',
									    		'name' 			=> $this->get_meta_name( 'service_qty['.esc_attr( $i ).'][]' ),
									    		'value' 		=> ovabrw_get_meta_data( $k, $qtys ),
									    		'placeholder' 	=> esc_html__( 'Number', 'ova-brw' )
									    	]); ?>
									    </td>
									    <td width="15%">
									    	<?php ovabrw_wp_select_input([
									    		'class' 	=> 'ovabrw-input-required ovabrw-service-duration',
									    		'name' 		=> $this->get_meta_name( 'service_duration_type['.esc_attr( $i ).'][]' ),
									    		'value' 	=> ovabrw_get_meta_data( $k, $types ),
									    		'options' 	=> $durations
									    	]); ?>
									    </td>
									    <td width="1%">
									    	<button class="button ovabrw-remove-service-option">x</button>
									    </td>
									</tr>
								<?php endforeach;
							endif; ?>
							</tbody>
							<tfoot>
								<tr>
									<th colspan="6">
										<button class="button ovabrw-add-service-group" >
											<?php esc_html_e( 'Add Option', 'ova-brw' ); ?>
										</button>
									</th>
								</tr>
							</tfoot>
						</table>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
			</div>
			<div class="ovabrw-service-add">
				<button class="button ovabrw-add-service" data-add-new="<?php
					ob_start();
					include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-service-field.php' );
					echo esc_attr( ob_get_clean() );
				?>" data-add-new-option="<?php
					ob_start();
					include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-service-option.php' );
					echo esc_attr( ob_get_clean() );
				?>">
					<?php esc_html_e( 'Add Service', 'ova-brw' ); ?>
				</button>
			</div>
		</div>
	</div>
</div>