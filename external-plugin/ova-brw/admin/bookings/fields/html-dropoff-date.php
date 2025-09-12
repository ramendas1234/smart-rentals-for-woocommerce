<?php defined( 'ABSPATH' ) || exit();

if ( $this->product->show_date_field( 'dropoff' ) ): ?>
	<div class="rental_item ovabrw-dropoff">
	    <label for="ovabrw-pickup-date">
	        <?php echo esc_html( $this->product->get_date_label( 'dropoff' ) ); ?>
	    </label>
	    <?php if ( 'appointment' == $this->get_type() ) {
	    	ovabrw_text_input([
				'type' 			=> 'text',
		        'class' 		=> 'appointment-dropoff-date',
		        'name' 			=> 'ovabrw_dropoff_date',
		        'key' 			=> 'ovabrw-item-key',
		        'required' 		=> true,
		        'placeholder' 	=> OVABRW()->options->get_datetime_placeholder(),
		        'readonly' 		=> true
			]);
	    } elseif ( $this->product->has_timepicker() ) {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> ovabrw_unique_id( 'dropoff_date' ),
		        'class' 	=> 'dropoff-date',
		        'name' 		=> 'ovabrw_dropoff_date',
		        'key' 		=> 'ovabrw-item-key',
		        'required' 	=> true,
		        'data_type' => 'datetimepicker'
			]);
		} else {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> ovabrw_unique_id( 'dropoff_date' ),
		        'class' 	=> 'dropoff-date',
		        'name' 		=> 'ovabrw_dropoff_date',
		        'key' 		=> 'ovabrw-item-key',
		        'required' 	=> true,
		        'data_type' => 'datepicker'
			]);
		} ?>
		<span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div>
<?php else:
	if ( 'appointment' === $this->get_type() ) {
		ovabrw_text_input([
			'type' 	=> 'hidden',
	        'name' 	=> 'ovabrw_dropoff_date',
	        'key' 	=> 'ovabrw-item-key',
		]);
	}
endif; ?>