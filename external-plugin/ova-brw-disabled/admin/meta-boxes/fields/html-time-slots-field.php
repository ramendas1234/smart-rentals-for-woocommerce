<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
	<td width="21%" class="timeslot-label">
    	<?php ovabrw_wp_text_input([
    		'type' 			=> 'text',
			'name' 			=> $this->get_meta_name( 'time_slots_label[dayOfWeek][]' ),
			'placeholder' 	=> esc_html__( '...', 'ova-brw' )
    	]); ?>
    </td>
    <td width="21%" class="timeslot-location">
    	<?php ovabrw_wp_select_input([
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'time_slots_location[dayOfWeek][]' ),
			'options' 		=> OVABRW()->options->get_locations(),
			'placeholder' 	=> esc_html__( 'Select location', 'ova-brw' )
    	]); ?>
    </td>
    <td width="14%" class="timeslot-start">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'class' 	=> 'ovabrw-input-required start-time',
			'name' 		=> $this->get_meta_name( 'time_slots_start[dayOfWeek][]' ),
			'data_type' => 'timepicker'
		]); ?>
	</td>
	<td width="14%" class="timeslot-end">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'class' 	=> 'end-time',
			'name' 		=> $this->get_meta_name( 'time_slots_end[dayOfWeek][]' ),
			'data_type' => 'timepicker'
		]); ?>
	</td>
	<td width="14%" class="ovabrw-input-price timeslot-price">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'class' 	=> 'ovabrw-input-required',
			'name' 		=> $this->get_meta_name( 'time_slots_price[dayOfWeek][]' ),
			'data_type' => 'price'
		]); ?>
	</td>
	<td width="14%" class="ovabrw-input-price timeslot-quantity">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'number',
			'class' 		=> 'ovabrw-input-required',
			'name' 			=> $this->get_meta_name( 'time_slots_quantity[dayOfWeek][]' ),
			'placeholder' 	=> 10
		]); ?>
	</td>
	<td width="1%" class="ovabrw-sort-icon">
		<span class="dashicons dashicons-menu"></span>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-time-slot">X</button>
	</td>
</tr>