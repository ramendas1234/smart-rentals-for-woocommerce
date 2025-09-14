<?php if ( !defined( 'ABSPATH' ) ) exit();

if ( $this->is_type( 'day' ) ): ?>
	<tr>
		<td width="13%" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 			=> 'text',
				'class' 		=> 'ovabrw-input-required',
				'name' 			=> $this->get_meta_name( 'rt_price[]' ),
				'data_type' 	=> 'price',
				'placeholder' 	=> esc_html__( 'Price/Day', 'ova-brw' )
			]); ?>
		</td>
		<td width="19%">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'id' 		=> 'specialFromUniqueID',
				'class' 	=> 'ovabrw-input-required start-date',
				'name' 		=> $this->get_meta_name( 'rt_startdate[]' ),
				'data_type' => 'datetimepicker'
			]); ?>
	    </td>
	    <td width="19%">
	    	<?php ovabrw_wp_text_input([
	    		'type' 		=> 'text',
				'id' 		=> 'specialToUniqueID',
				'class' 	=> 'ovabrw-input-required end-date',
				'name' 		=> $this->get_meta_name( 'rt_enddate[]' ),
				'data_type' => 'datetimepicker'
	    	]); ?>
	    </td>
	    <td width="48%" class="ovabrw-special-discounts">
	    	<table width="100%" class="widefat">
		      	<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Price/Day', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'From (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'To (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Duration', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot>
					<tr>
						<th colspan="5">
							<button class="button ovabrw-add-st-discount">
								<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
	      	</table>
	    </td>
	    <td width="1%">
	    	<button class="button ovabrw-remove-special-time">x</button>
	    </td>
	</tr>
<?php elseif ( $this->is_type( 'hotel' ) ): ?>
	<tr>
		<td width="13%" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 			=> 'text',
				'class' 		=> 'ovabrw-input-required',
				'name' 			=> $this->get_meta_name( 'rt_price[]' ),
				'data_type' 	=> 'price',
				'placeholder' 	=> esc_html__( 'Price/Night', 'ova-brw' )
			]); ?>
		</td>
		<td width="19%">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'id' 		=> 'specialFromUniqueID',
				'class' 	=> 'ovabrw-input-required start-date',
				'name' 		=> $this->get_meta_name( 'rt_startdate[]' ),
				'data_type' => 'datepicker'
			]); ?>
	    </td>
	    <td width="19%">
	    	<?php ovabrw_wp_text_input([
	    		'type' 		=> 'text',
				'id' 		=> 'specialToUniqueID',
				'class' 	=> 'ovabrw-input-required end-date',
				'name' 		=> $this->get_meta_name( 'rt_enddate[]' ),
				'data_type' => 'datepicker'
	    	]); ?>
	    </td>
	    <td width="48%" class="ovabrw-special-discounts">
	    	<table width="100%" class="widefat">
		      	<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Price/Night', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'From (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'To (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Duration', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot>
					<tr>
						<th colspan="5">
							<button class="button ovabrw-add-st-discount">
								<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
	      	</table>
	    </td>
	    <td width="1%">
	    	<button class="button ovabrw-remove-special-time">x</button>
	    </td>
	</tr>
<?php elseif ( $this->is_type( 'hour' ) ): ?>
	<tr>
		<td width="13%" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 			=> 'text',
				'class' 		=> 'ovabrw-input-required',
				'name' 			=> $this->get_meta_name( 'rt_price_hour[]' ),
				'data_type' 	=> 'price',
				'placeholder' 	=> esc_html__( 'Price/Hour', 'ova-brw' )
			]); ?>
		</td>
		<td width="19%">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'id' 		=> 'specialFromUniqueID',
				'class' 	=> 'ovabrw-input-required start-date',
				'name' 		=> $this->get_meta_name( 'rt_startdate[]' ),
				'data_type' => 'datetimepicker'
			]); ?>
	    </td>
	    <td width="19%">
	    	<?php ovabrw_wp_text_input([
	    		'type' 		=> 'text',
				'id' 		=> 'specialToUniqueID',
				'class' 	=> 'ovabrw-input-required end-date',
				'name' 		=> $this->get_meta_name( 'rt_enddate[]' ),
				'data_type' => 'datetimepicker'
	    	]); ?>
	    </td>
	    <td width="48%" class="ovabrw-special-discounts">
	    	<table width="100%" class="widefat">
		      	<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Price/Hour', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'From (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'To (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Duration', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot>
					<tr>
						<th colspan="5">
							<button class="button ovabrw-add-st-discount">
								<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
	      	</table>
	    </td>
	    <td width="1%">
	    	<button class="button ovabrw-remove-special-time">x</button>
	    </td>
	</tr>
<?php else: ?>
	<tr>
		<td width="10%" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 			=> 'text',
				'class' 		=> 'ovabrw-input-required',
				'name' 			=> $this->get_meta_name( 'rt_price[]' ),
				'data_type' 	=> 'price',
				'placeholder' 	=> esc_html__( 'Price/Day', 'ova-brw' )
			]); ?>
		</td>
		<td width="10%" class="ovabrw-input-price">
			<?php ovabrw_wp_text_input([
				'type' 			=> 'text',
				'class' 		=> 'ovabrw-input-required',
				'name' 			=> $this->get_meta_name( 'rt_price_hour[]' ),
				'data_type' 	=> 'price',
				'placeholder' 	=> esc_html__( 'Price/Hour', 'ova-brw' )
			]); ?>
		</td>
		<td width="16%">
			<?php ovabrw_wp_text_input([
				'type' 		=> 'text',
				'id' 		=> 'specialFromUniqueID',
				'class' 	=> 'ovabrw-input-required start-date',
				'name' 		=> $this->get_meta_name( 'rt_startdate[]' ),
				'data_type' => 'datetimepicker'
			]); ?>
	    </td>
	    <td width="16%">
	    	<?php ovabrw_wp_text_input([
	    		'type' 		=> 'text',
				'id' 		=> 'specialToUniqueID',
				'class' 	=> 'ovabrw-input-required end-date',
				'name' 		=> $this->get_meta_name( 'rt_enddate[]' ),
				'data_type' => 'datetimepicker'
	    	]); ?>
	    </td>
	    <td width="48%" class="ovabrw-special-discounts">
	    	<table width="100%" class="widefat">
		      	<thead>
					<tr>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Price', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'From (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'To (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Duration', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody></tbody>
				<tfoot>
					<tr>
						<th colspan="5">
							<button class="button ovabrw-add-st-discount">
								<?php esc_html_e( 'Add PST', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
	      	</table>
	    </td>
	    <td width="1%">
	    	<button class="button ovabrw-remove-special-time">x</button>
	    </td>
	</tr>
<?php endif;