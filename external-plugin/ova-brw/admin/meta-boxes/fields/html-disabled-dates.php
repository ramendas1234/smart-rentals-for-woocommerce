<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get disabled from
$disabled_from = $this->get_meta_value( 'untime_startdate' );

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label"><?php esc_html_e( 'Disabled Dates', 'ova-brw' ); ?></h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-unavailable-time">
			<table class="widefat">
				<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Start Date', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'End Date', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
				<?php if ( ovabrw_array_exists( $disabled_from ) ):
					// Date format
					$date_format = OVABRW()->options->get_date_format();

					// Time format
					$time_format = OVABRW()->options->get_time_format();

					// Disabled to
					$disabled_to = $this->get_meta_value( 'untime_enddate' );

					foreach ( $disabled_from as $k => $from ):
						$to = ovabrw_get_meta_data( $k, $disabled_to );
				?>
					<tr>
						<?php if ( $this->is_type( 'hotel' ) ): ?>
							<td width="49.5%">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'id' 		=> ovabrw_unique_id( 'disabled_from' ),
									'class' 	=> 'ovabrw-input-required start-date',
									'name' 		=> $this->get_meta_name( 'untime_startdate[]' ),
									'value' 	=> $from,
									'data_type' => 'datepicker'
								]); ?>
						    </td>
						    <td width="49.5%">
						    	<?php ovabrw_wp_text_input([
						    		'type' 		=> 'text',
									'id' 		=> ovabrw_unique_id( 'disabled_to' ),
									'class' 	=> 'ovabrw-input-required end-date',
									'name' 		=> $this->get_meta_name( 'untime_enddate[]' ),
									'value' 	=> $to,
									'data_type' => 'datepicker'
						    	]); ?>
						    </td>
						<?php else: ?>
							<td width="49.5%">
								<?php ovabrw_wp_text_input([
									'type' 		=> 'text',
									'id' 		=> ovabrw_unique_id( 'disabled_from' ),
									'class' 	=> 'ovabrw-input-required start-date',
									'name' 		=> $this->get_meta_name( 'untime_startdate[]' ),
									'value' 	=> $from,
									'data_type' => 'datetimepicker',
									'attrs' 	=> [
										'data-date' => strtotime( $from ) ? gmdate( $date_format, strtotime( $from ) ) : '',
										'data-time' => strtotime( $from ) ? gmdate( $time_format, strtotime( $from ) ) : ''
									]
								]); ?>
						    </td>
						    <td width="49.5%">
						    	<?php ovabrw_wp_text_input([
						    		'type' 		=> 'text',
									'id' 		=> ovabrw_unique_id( 'disabled_to' ),
									'class' 	=> 'ovabrw-input-required end-date',
									'name' 		=> $this->get_meta_name( 'untime_enddate[]' ),
									'value' 	=> $to,
									'data_type' => 'datetimepicker',
									'attrs' 	=> [
										'data-date' => strtotime( $to ) ? gmdate( $date_format, strtotime( $to ) ) : '',
										'data-time' => strtotime( $to ) ? gmdate( $time_format, strtotime( $to ) ) : ''
									]
						    	]); ?>
						    </td>
						<?php endif; ?>
					    <td width="1%">
					    	<button class="button ovabrw-remove-disabled-date">x</button>
					    </td>
					</tr>
				<?php endforeach; endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">
							<button class="button ovabrw-add-disabled-date" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-disabled-date-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add DD', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>