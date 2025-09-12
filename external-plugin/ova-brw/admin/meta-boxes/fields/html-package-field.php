<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
	<td width="10%">
		<?php ovabrw_wp_text_input([
			'class' 		=> 'ovabrw-input-required',
			'type' 			=> 'text',
			'name' 			=> $this->get_meta_name( 'petime_id[]' ),
			'value' 		=> '[packageID]',
			'placeholder' 	=> esc_html__( 'Not space', 'ova-brw' )
		]); ?>
    </td>
    <td width="10%" class="ovabrw-input-price">
    	<?php ovabrw_wp_text_input([
    		'class' 		=> 'ovabrw-input-required',
			'type' 			=> 'text',
			'name' 			=> $this->get_meta_name( 'petime_price[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '10.5'
		]); ?>
    </td>
    <td width="18%" class="ovabrw-input-price">
    	<?php ovabrw_wp_select_input([
    		'class' 	=> 'ovabrw-input-required',
			'name' 		=> $this->get_meta_name( 'package_type[]' ),
			'options' 	=> [
				'inday' => esc_html__( 'Hour', 'ova-brw' ),
				'other' => esc_html__( 'Day', 'ova-brw' )
			]
		]); ?>
    	<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-hidden',
			'name' 			=> $this->get_meta_name( 'petime_days[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> esc_html__( 'Total Day', 'ova-brw' )
		]); ?>
        <div class="ovabrw-period-hours">
        	<?php ovabrw_wp_text_input([
				'type' 			=> 'text',
				'class' 		=> 'ovabrw-hidden start-time',
				'name' 			=> $this->get_meta_name( 'pehour_start_time[]' ),
				'data_type' 	=> 'timepicker',
				'placeholder' 	=> esc_html__( 'Start Hour', 'ova-brw' )
			]); ?>
			<?php ovabrw_wp_text_input([
				'type' 			=> 'text',
				'class' 		=> 'ovabrw-hidden end-time',
				'name' 			=> $this->get_meta_name( 'pehour_end_time[]' ),
				'data_type' 	=> 'timepicker',
				'placeholder' 	=> esc_html__( 'End Hour', 'ova-brw' )
			]); ?>
        </div>
        <?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-hidden',
			'name' 			=> $this->get_meta_name( 'pehour_unfixed[]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> esc_html__( 'Total Hour', 'ova-brw' )
		]); ?>
    </td>
    <td width="20%">
    	<?php ovabrw_wp_text_input([
    		'class' 		=> 'ovabrw-input-required',
			'type' 			=> 'text',
			'name' 			=> $this->get_meta_name( 'petime_label[]' ),
			'placeholder' 	=> esc_html__( 'Text', 'ova-brw' )
		]); ?>
    </td>
    <td width="41%" class="ovabrw-period-discounts">
    	<table class="widefat">
	      	<thead>
				<tr>
					<th class="ovabrw-required">
						<?php esc_html_e( 'Price', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required">
						<?php esc_html_e( 'Start Time', 'ova-brw' ); ?>
					</th>
					<th class="ovabrw-required">
						<?php esc_html_e( 'End Time', 'ova-brw' ); ?>
					</th>
					<th></th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot>
				<tr>
					<th colspan="4">
						<button class="button ovabrw-add-pt-discount">
							<?php esc_html_e( 'Add Discount', 'ova-brw' ); ?>
						</button>
					</th>
				</tr>
			</tfoot>
      	</table>
    </td>
    <td width="1%">
    	<button class="button ovabrw-remove-package">x</button>
    </td>
</tr>