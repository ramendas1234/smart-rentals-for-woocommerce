<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>				
	<td width="20%" class="ovabrw-input-price">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required ovabrw-period-discount-price',
			'name' 			=> $this->get_meta_name( 'petime_discount[index][price][]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> '10.5'
		]); ?>
	</td>
	<td width="39.5%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'periodFromUniqueID',
			'class' 	=> 'ovabrw-input-required start-date',
			'name' 		=> $this->get_meta_name( 'petime_discount[index][start_time][]' ),
			'data_type' => 'datetimepicker'
		]); ?>
	</td>
	<td width="39.5%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'periodToUniqueID',
			'class' 	=> 'ovabrw-input-required end-date',
			'name' 		=> $this->get_meta_name( 'petime_discount[index][end_time][]' ),
			'data_type' => 'datetimepicker'
		]); ?>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-pt-discount">x</button>
	</td>
</tr>