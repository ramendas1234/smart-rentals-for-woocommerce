<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
	<td width="49.5%">
		<?php ovabrw_wp_text_input([
			'type' 		=> 'text',
			'id' 		=> 'allowedStartUniqueID',
			'class' 	=> 'ovabrw-input-required start-date',
			'name' 		=> $this->get_meta_name( 'allowed_startdate[]' ),
			'data_type' => 'datepicker'
		]); ?>
    </td>
    <td width="49.5%">
    	<?php ovabrw_wp_text_input([
    		'type' 		=> 'text',
			'id' 		=> 'allowedEndUniqueID',
			'class' 	=> 'ovabrw-input-required end-date',
			'name' 		=> $this->get_meta_name( 'allowed_enddate[]' ),
			'data_type' => 'datepicker'
    	]); ?>
    </td>
    <td width="1%">
    	<button class="button ovabrw-remove-allowed-date">x</button>
    </td>
</tr>