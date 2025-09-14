<?php if ( !defined( 'ABSPATH' ) ) exit();

$extra_time_hour 	= $this->get_meta_value( 'extra_time_hour' );
$extra_time_label 	= $this->get_meta_value( 'extra_time_label' );
$extra_time_price 	= $this->get_meta_value( 'extra_time_price' );

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Extra Time', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-extra-time">
			<table class="widefat">
				<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Time (hour)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Label', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php echo sprintf( esc_html__( 'Additional cost (%s)', 'ova-brw' ), get_woocommerce_currency_symbol() ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ovabrw_array_exists( $extra_time_hour ) ):
						foreach ( $extra_time_hour as $i => $time ):
							$label = ovabrw_get_meta_data( $i, $extra_time_label );
							$price = ovabrw_get_meta_data( $i, $extra_time_price );
					?>
						<tr>
						    <td width="20%" class="ovabrw-input-price">
						    	<?php ovabrw_wp_text_input([
						    		'type' 			=> 'text',
						    		'class' 		=> 'ovabrw-input-required',
						    		'name' 			=> $this->get_meta_name( 'extra_time_hour[]' ),
						    		'value' 		=> $time,
						    		'placeholder' 	=> esc_html__( 'Number', 'ova-brw' ),
						    		'data_type' 	=> 'price'
						    	]); ?>
						    </td>
						    <td width="20%">
						    	<?php ovabrw_wp_text_input([
						    		'type' 			=> 'text',
						    		'class' 		=> 'ovabrw-input-required',
						    		'name' 			=> $this->get_meta_name( 'extra_time_label[]' ),
						    		'value' 		=> $label,
						    		'placeholder' 	=> esc_html__( 'Text', 'ova-brw' )
						    	]); ?>
						    </td>
						    <td width="20%" class="ovabrw-input-price">
						    	<?php ovabrw_wp_text_input([
						    		'type' 			=> 'text',
						    		'class' 		=> 'ovabrw-input-required',
						    		'name' 			=> $this->get_meta_name( 'extra_time_price[]' ),
						    		'value' 		=> $price,
						    		'placeholder' 	=> esc_html__( 'Price', 'ova-brw' ),
						    		'data_type' 	=> 'price'
						    	]); ?>
						    </td>
						    <td width="1%">
						    	<button class="button ovabrw-remove-extra-time">x</button>
						    </td>
						</tr>
					<?php endforeach; endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4">
							<button class="button ovabrw-add-extra-time" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-extra-time-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add Time', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>