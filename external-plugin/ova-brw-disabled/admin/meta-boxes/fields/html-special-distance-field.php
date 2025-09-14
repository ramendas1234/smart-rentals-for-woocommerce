<?php if ( !defined( 'ABSPATH' ) ) exit();

// Price by km/mi
$price_by = $this->get_meta_value( 'map_price_by', 'km' );
if ( !$price_by ) $price_by = 'km';

?>

<tr>
	<td width="20%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'specialFromUniqueID',
			'class' 	=> 'ovabrw-input-required start-date',
			'name' 		=> $this->get_meta_name( 'st_pickup_distance[]' ),
			'data_type' => 'datetimepicker',
		]); ?>
	</td>
	<td width="20%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'specialToUniqueID',
			'class' 	=> 'ovabrw-input-required end-date',
			'name' 		=> $this->get_meta_name( 'st_pickoff_distance[]' ),
			'data_type' => 'datetimepicker'
		]); ?>
	</td>
	<td width="14%" class="ovabrw-input-price">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name('st_price_distance[]'),
			'data_type' 	=> 'price',
			'placeholder' 	=> esc_html__( 'Price', 'ova-brw' )
		]); ?>
	</td>
	<td width="45%" class="ovabrw-special-distance-discount">
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
			<tbody></tbody>
			<tfoot>
				<tr>
					<th colspan="4">
						<button class="button ovabrw-add-st-discount-distance">
							<?php esc_html_e( 'Add Discount', 'ova-brw' ); ?>
						</button>
					</th>
				</tr>
			</tfoot>
		</table>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-st-distance">x</button>
	</td>
</tr>