<?php if ( !defined( 'ABSPATH' ) ) exit();

$discount_from 	= $this->get_meta_value( 'discount_distance_from' );
$discount_to 	= $this->get_meta_value( 'discount_distance_to' );
$discount_price = $this->get_meta_value( 'discount_distance_price' );

// Price by km/mi
$price_by = $this->get_meta_value( 'map_price_by', 'km' );
if ( !$price_by ) $price_by = 'km';

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Distance-based Discount', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-global-discount-distance">
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
					<?php if ( ovabrw_array_exists( $discount_from ) && ovabrw_array_exists( $discount_to ) && ovabrw_array_exists( $discount_price ) ):
						foreach ( $discount_price as $i => $price ):
							$from 	= ovabrw_get_meta_data( $i, $discount_from );
							$to 	= ovabrw_get_meta_data( $i, $discount_to );
					?>
						<tr>
						    <td width="30%" class="ovabrw-input-price">
						    	<?php ovabrw_wp_text_input([
						    		'type' 			=> 'text',
						    		'class' 		=> 'ovabrw-input-required',
						    		'name' 			=> $this->get_meta_name( 'discount_distance_from[]' ),
						    		'value' 		=> $from,
						    		'placeholder' 	=> esc_attr__( 'Number', 'ova-brw' ),
						    		'data_type' 	=> 'price'
						    	]); ?>
						    </td>
						    <td width="30%" class="ovabrw-input-price">
						    	<?php ovabrw_wp_text_input([
						    		'type' 			=> 'text',
						    		'class' 		=> 'ovabrw-input-required',
						    		'name' 			=> $this->get_meta_name( 'discount_distance_to[]' ),
						    		'value' 		=> $to,
						    		'placeholder' 	=> esc_attr__( 'Number', 'ova-brw' ),
						    		'data_type' 	=> 'price'
						    	]); ?>
						    </td>
						    <td width="30%" class="ovabrw-input-price">
						    	<?php ovabrw_wp_text_input([
						    		'type' 			=> 'text',
						    		'class' 		=> 'ovabrw-input-required',
						    		'name' 			=> $this->get_meta_name( 'discount_distance_price[]' ),
						    		'value' 		=> $price,
						    		'placeholder' 	=> esc_attr__( 'Price', 'ova-brw' ),
						    		'data_type' 	=> 'price'
						    	]); ?>
						    </td>
						    <td width="1%">
						    	<button class="button ovabrw-remove-discount-distance">x</button>
						    </td>
						</tr>
					<?php endforeach; endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="4">
							<button class="button ovabrw-add-discount-distance" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-discount-distance-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add Discount', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>