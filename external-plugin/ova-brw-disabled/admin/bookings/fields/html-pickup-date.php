<?php defined( 'ABSPATH' ) || exit(); ?>

<div class="rental_item ovabrw-pickup">
    <label for="ovabrw-pickup-date">
        <?php echo esc_html( $this->product->get_date_label() ); ?>
    </label>
    <?php if ( 'appointment' == $this->get_type() ) {
    	ovabrw_text_input([
			'type' 		=> 'text',
	        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
	        'class' 	=> 'pickup-date',
	        'name' 		=> 'ovabrw_pickup_date',
	        'key' 		=> 'ovabrw-item-key',
	        'required' 	=> true,
	        'data_type' => 'datepicker'
		]);
    } elseif ( $this->product->has_timepicker() ) {
    	ovabrw_text_input([
			'type' 		=> 'text',
	        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
	        'class' 	=> 'pickup-date',
	        'name' 		=> 'ovabrw_pickup_date',
	        'key' 		=> 'ovabrw-item-key',
	        'required' 	=> true,
	        'data_type' => 'datetimepicker'
		]);
    } else {
    	ovabrw_text_input([
			'type' 		=> 'text',
	        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
	        'class' 	=> 'pickup-date',
	        'name' 		=> 'ovabrw_pickup_date',
	        'key' 		=> 'ovabrw-item-key',
	        'required' 	=> true,
	        'data_type' => 'datepicker'
		]);
    } ?>
    <span class="ovabrw-loader-date">
    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
    </span>
</div>