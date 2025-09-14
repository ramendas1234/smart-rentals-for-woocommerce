<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get enabled from
$allowed_startdate = $this->get_meta_value( 'allowed_startdate' );

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label"><?php esc_html_e( 'Allowed Dates', 'ova-brw' ); ?></h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-available-time">
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
				<?php if ( ovabrw_array_exists( $allowed_startdate ) ):
					// Date format
					$date_format = OVABRW()->options->get_date_format();

					// End dates
					$allowed_enddate = $this->get_meta_value( 'allowed_enddate' );

					foreach ( $allowed_startdate as $k => $start_date ):
						$end_date = ovabrw_get_meta_data( $k, $allowed_enddate );
				?>
					<tr>
						<td width="49.5%">
							<?php ovabrw_wp_text_input([
								'type' 		=> 'text',
								'id' 		=> ovabrw_unique_id( 'allowed_startdate' ),
								'class' 	=> 'ovabrw-input-required start-date',
								'name' 		=> $this->get_meta_name( 'allowed_startdate[]' ),
								'value' 	=> $start_date,
								'data_type' => 'datepicker'
							]); ?>
					    </td>
					    <td width="49.5%">
					    	<?php ovabrw_wp_text_input([
					    		'type' 		=> 'text',
								'id' 		=> ovabrw_unique_id( 'allowed_enddate' ),
								'class' 	=> 'ovabrw-input-required end-date',
								'name' 		=> $this->get_meta_name( 'allowed_enddate[]' ),
								'value' 	=> $end_date,
								'data_type' => 'datepicker'
					    	]); ?>
					    </td>
					    <td width="1%">
					    	<button class="button ovabrw-remove-allowed-date">x</button>
					    </td>
					</tr>
				<?php endforeach; endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="6">
							<button class="button ovabrw-add-allowed-date" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-allowed-date-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add AD', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>