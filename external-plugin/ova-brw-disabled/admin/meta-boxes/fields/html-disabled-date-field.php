<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
	<?php if ( $this->is_type( 'hotel' ) ): ?>
		<td width="49.5%">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'id' 		=> 'disabledFromUniqueID',
				'class' 	=> 'ovabrw-input-required start-date',
				'name' 		=> $this->get_meta_name( 'untime_startdate[]' ),
				'data_type' => 'datepicker'
			]); ?>
	    </td>
	    <td width="49.5%">
	    	<?php ovabrw_wp_text_input([
	    		'type' 		=> 'text',
				'id' 		=> 'disabledToUniqueID',
				'class' 	=> 'ovabrw-input-required end-date',
				'name' 		=> $this->get_meta_name( 'untime_enddate[]' ),
				'data_type' => 'datepicker'
	    	]); ?>
	    </td>
	<?php else: ?>
		<td width="49.5%">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'id' 		=> 'disabledFromUniqueID',
				'class' 	=> 'ovabrw-input-required start-date',
				'name' 		=> $this->get_meta_name( 'untime_startdate[]' ),
				'data_type' => 'datetimepicker'
			]); ?>
	    </td>
	    <td width="49.5%">
	    	<?php ovabrw_wp_text_input([
	    		'type' 		=> 'text',
				'id' 		=> 'disabledToUniqueID',
				'class' 	=> 'ovabrw-input-required end-date',
				'name' 		=> $this->get_meta_name( 'untime_enddate[]' ),
				'data_type' => 'datetimepicker'
	    	]); ?>
	    </td>
	<?php endif; ?>
    <td width="1%">
    	<button class="button ovabrw-remove-disabled-date">x</button>
    </td>
</tr>