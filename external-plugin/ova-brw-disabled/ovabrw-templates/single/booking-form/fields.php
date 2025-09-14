<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Rental type
$rental_type = $product->get_rental_type();

// Date format
$date_format = OVABRW()->options->get_date_format();

// Time format
$time_format = OVABRW()->options->get_time_format();

// Pick-up location
$pickup_location = ovabrw_get_meta_data( 'pickup_location', $_GET );

// Drop-off location
$dropoff_location = ovabrw_get_meta_data( 'pickoff_location', $_GET );

// Pick-up date
$pickup_date = ovabrw_get_meta_data( 'pickup_date', $_GET );

// Drop-off date
$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $_GET );

// Location fields
if ( in_array( $rental_type, [ 'day', 'hour', 'mixed', 'hotel', 'period_time' ] ) ):
	// Pick-up location
	if ( $product->show_location_field() ): ?>
		<div class="rental_item">
			<label>
				<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
			</label>
			<?php echo $product->get_html_location( 'pickup', $product->get_meta_key( 'pickup_location' ), 'ovabrw-input-required', $pickup_location ); ?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; // End Pick-up location

	// Drop-off location
	if ( $product->show_location_field( 'dropoff' ) ): ?>
		<div class="rental_item">
			<label>
				<?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
			</label>
			<?php echo $product->get_html_location( 'dropoff', $product->get_meta_key( 'dropoff_location' ), 'ovabrw-input-required', $dropoff_location ); ?>
			<div class="ovabrw-other-location"></div>
		</div>
	<?php endif; // End Drop-off location ?>
<?php endif; // End Location fields ?>

<!-- Date fields -->
<?php if ( in_array( $rental_type, [ 'day', 'hour', 'mixed', 'hotel' ] ) ): ?>
	<!-- Pick-up date -->
	<div class="rental_item">
		<label>
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php if ( $product->has_timepicker() ) {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datetimepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
					'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} else {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} ?>
	    <span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div><!-- End Pick-up date -->

	<!-- Drop-off date -->
	<?php if ( $product->show_date_field( 'dropoff' ) ): ?>
		<div class="rental_item">
			<label>
				<?php echo esc_html( $product->get_date_label( 'dropoff' ) ); ?>
			</label>
			<?php if ( $product->has_timepicker( 'dropoff' ) ) {
				ovabrw_text_input([
					'type' 		=> 'text',
			        'id' 		=> ovabrw_unique_id( 'dropoff_date' ),
			        'class' 	=> 'dropoff-date',
			        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
			        'value' 	=> $dropoff_date,
			        'required' 	=> true,
			        'data_type' => 'datetimepicker',
			        'attrs' 	=> [
						'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : '',
						'data-time' => strtotime( $dropoff_date ) ? gmdate( $time_format, strtotime( $dropoff_date ) ) : ''
					]
				]);
			} else {
				ovabrw_text_input([
					'type' 		=> 'text',
			        'id' 		=> ovabrw_unique_id( 'dropoff_date' ),
			        'class' 	=> 'dropoff-date',
			        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
			        'value' 	=> $dropoff_date,
			        'required' 	=> true,
			        'data_type' => 'datepicker',
			        'attrs' 	=> [
						'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : ''
					]
				]);
			} ?>
			<span class="ovabrw-loader-date">
		    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
		    </span>
		</div>
	<?php endif; // End Drop-off date ?>

	<!-- Guests picker -->
	<?php if ( 'hotel' === $rental_type ) {
		ovabrw_get_template('modern/single/detail/ovabrw-product-guests.php');
	} // End Guests picker ?>
<?php endif; // End Date fields ?>

<?php if ( 'period_time' === $rental_type ): // Rental type: Period Time
	$package_ids 	= $product->get_meta_value( 'petime_id' );
	$package_names 	= $product->get_meta_value( 'petime_label' );
?>
	<!-- Pick-up date -->
	<div class="rental_item">
		<label>
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php if ( $product->has_timepicker() ) {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datetimepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
					'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} else {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} ?>
	    <span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div><!-- End Pick-up date -->

	<!-- Packages -->
	<?php if ( ovabrw_array_exists( $package_ids ) ):
		$default_package 	= '';
		$package_duration 	= ovabrw_get_meta_data( 'package', $_GET );
		
		if ( $package_duration ) {
			$default_package = $product->get_package_id( $package_duration );
		}
	?>
		<div class="rental_item">
			<label>
				<?php esc_html_e( 'Choose Package', 'ova-brw' ); ?>
			</label>
			<div class="period_package">
				<select
					name="<?php echo esc_attr( $product->get_meta_key( 'package_id' ) ); ?>"
					class="ovabrw-select2 ovabrw-input-required">
					<option value="">
						<?php esc_html_e( 'Select Package', 'ova-brw' ); ?>
					</option>
					<?php foreach ( $package_ids as $k => $package_id ):
						$package_name = ovabrw_get_meta_data( $k, $package_names );

						if ( !$package_id || !$package_name ) continue;
					?>
						<option value="<?php echo esc_attr( trim( $package_id ) ); ?>"<?php ovabrw_selected( $package_id, $default_package ); ?>> 
							<?php echo esc_html( $package_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	<?php endif; ?>
<?php endif; // End Period Time ?>

<?php if ( 'transportation' === $rental_type ): // Rental type: Transportation ?>
	<!-- Locations -->
	<div class="rental_item">
		<label>
			<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
		</label>
		<?php echo $product->get_html_location( 'pickup', $product->get_meta_key( 'pickup_location' ), 'ovabrw-input-required', $pickup_location ); ?>
		<div class="ovabrw-other-location"></div>
	</div>
	<div class="rental_item">
		<label>
			<?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
		</label>
		<?php echo $product->get_html_location( 'dropoff', $product->get_meta_key( 'dropoff_location' ), 'ovabrw-input-required', $dropoff_location ); ?>
		<div class="ovabrw-other-location"></div>
	</div>
	<!-- End Locations -->

	<!-- Pick-up date -->
	<div class="rental_item">
		<label>
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php if ( $product->has_timepicker() ) {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datetimepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
					'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} else {
			ovabrw_text_input([
				'type' 		=> 'text',
		        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
		        'class' 	=> 'pickup-date',
		        'name' 		=> $product->get_meta_key( 'pickup_date' ),
		        'value' 	=> $pickup_date,
		        'required' 	=> true,
		        'data_type' => 'datepicker',
		        'attrs' 	=> [
					'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
				]
			]);
		} ?>
	    <span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div><!-- End Pick-up date -->

	<!-- Drop-off date -->
	<?php if ( $product->show_date_field( 'dropoff' ) ): ?>
		<div class="rental_item">
			<label>
				<?php echo esc_html( $product->get_date_label( 'dropoff' ) ); ?>
			</label>
			<?php if ( $product->has_timepicker( 'dropoff' ) ) {
				ovabrw_text_input([
					'type' 		=> 'text',
			        'id' 		=> ovabrw_unique_id( 'dropoff_date' ),
			        'class' 	=> 'dropoff-date',
			        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
			        'value' 	=> $dropoff_date,
			        'required' 	=> true,
			        'data_type' => 'datetimepicker',
			        'attrs' 	=> [
						'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : '',
						'data-time' => strtotime( $dropoff_date ) ? gmdate( $time_format, strtotime( $dropoff_date ) ) : ''
					]
				]);
			} else {
				ovabrw_text_input([
					'type' 		=> 'text',
			        'id' 		=> ovabrw_unique_id( 'dropoff_date' ),
			        'class' 	=> 'dropoff-date',
			        'name' 		=> $product->get_meta_key( 'dropoff_date' ),
			        'value' 	=> $dropoff_date,
			        'required' 	=> true,
			        'data_type' => 'datepicker',
			        'attrs' 	=> [
						'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : ''
					]
				]);
			} ?>
			<span class="ovabrw-loader-date">
		    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
		    </span>
		</div>
	<?php endif; // End Drop-off date ?>
<?php endif; // End transportation ?>

<?php if ( 'taxi' === $rental_type ): // Rental type: Taxi
	$origin 		= ovabrw_get_meta_data( 'origin', $_GET );
	$destination 	= ovabrw_get_meta_data( 'destination', $_GET );
	$duration 		= ovabrw_get_meta_data( 'duration', $_GET );
	$distance 		= ovabrw_get_meta_data( 'distance', $_GET );

	// Get data by product ID
	$price_by 		= $product->get_meta_value( 'map_price_by' );
	$waypoint 		= $product->get_meta_value( 'waypoint' );
	$zoom_map 		= $product->get_meta_value( 'zoom_map' );
	$extra_hour 	= $product->get_meta_value( 'extra_time_hour' );
	$extra_label 	= $product->get_meta_value( 'extra_time_label' );
	$latitude 		= $product->get_meta_value( 'latitude' );
	$longitude 		= $product->get_meta_value( 'longitude' );

	// Price by
	if ( !$price_by ) $price_by = 'km';

	// Latitude
	if ( !$latitude ) {
		$latitude = ovabrw_get_setting( 'latitude_map_default', 39.177972 );
	}

	// Longitude
	if ( !$longitude ) {
		$longitude = ovabrw_get_setting( 'longitude_map_default', -100.36375 );
	}

	$max_waypoint 	= $product->get_meta_value( 'max_waypoint' );
	$map_types 		= $product->get_meta_value( 'map_types' );
	$bounds 		= $product->get_meta_value( 'bounds' );
	$bounds_lat 	= $product->get_meta_value( 'bounds_lat' );
	$bounds_lng 	= $product->get_meta_value( 'bounds_lng' );
	$bounds_radius 	= $product->get_meta_value( 'bounds_radius' );
	$restrictions 	= $product->get_meta_value( 'restrictions' );

	if ( !$map_types ) $map_types = [ 'geocode' ];
	if ( !$restrictions ) $restrictions = [];
?>
	<div class="rental_item full-width">
		<label>
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php ovabrw_text_input([
			'type'          => 'text',
	        'id'            => ovabrw_unique_id( 'pickup_date' ),
	        'class'         => 'pickup-date',
	        'name'          => $product->get_meta_key( 'pickup_date' ),
	        'value' 		=> $pickup_date,
	        'required'      => true,
	        'data_type'     => 'datetimepicker',
	        'attrs' 		=> [
				'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
				'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
			]
		]); ?>
		<span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div>
	<div class="rental_item form-location-field">
		<label>
			<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_text_input([
			'type'          => 'text',
	        'id'            => $product->get_meta_key( 'pickup_location' ),
	        'name'          => $product->get_meta_key( 'pickup_location' ),
	        'value' 		=> $pickup_location,
	        'placeholder' 	=> esc_html__( 'Enter your location', 'ova-brw' ),
	        'required'      => true
		]); ?>
		<?php ovabrw_text_input([
			'type' 		=> 'hidden',
	        'id' 		=> $product->get_meta_key( 'origin' ),
	        'name' 		=> $product->get_meta_key( 'origin' ),
	        'value' 	=> esc_attr( stripslashes( stripslashes( $origin ) ) ),
	        'required' 	=> true
		]); ?>
		<?php if ( 'on' === $waypoint ): ?>
			<i aria-hidden="true" class="flaticon-add btn-add-waypoint"></i>
		<?php endif; ?>
	</div>
	<div class="rental_item form-location-field">
		<label>
			<?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_text_input([
			'type'          => 'text',
	        'id'            => $product->get_meta_key( 'dropoff_location' ),
	        'name'          => $product->get_meta_key( 'dropoff_location' ),
	        'value' 		=> $dropoff_location,
	        'placeholder' 	=> esc_html__( 'Enter your location', 'ova-brw' ),
	        'required'      => true
		]); ?>
		<?php ovabrw_text_input([
			'type' 		=> 'hidden',
	        'id' 		=> $product->get_meta_key( 'destination' ),
	        'name' 		=> $product->get_meta_key( 'destination' ),
	        'value' 	=> esc_attr( stripslashes( stripslashes( $destination ) ) ),
	        'required' 	=> true
		]); ?>
	</div>
	<?php if ( ovabrw_array_exists( $extra_hour ) ): ?>
	<div class="rental_item">
		<label>
			<?php esc_html_e( 'Extra Time', 'ova-brw' ); ?>
		</label>
		<select name="<?php echo esc_attr( $product->get_meta_key( 'extra_time' ) ); ?>">
			<option value="">
				<?php esc_html_e( 'Select Time', 'ova-brw' ); ?>
			</option>
			<?php foreach ( $extra_hour as $k => $time ):
				$label = ovabrw_get_meta_data( $k, $extra_label );
			?>
				<option value="<?php echo esc_attr( $time ); ?>">
					<?php echo esc_html( $label ); ?>
				</option>
			<?php endforeach; ?>
		</select>
	</div>
	<?php endif; ?>
	<?php ovabrw_text_input([
		'type' 	=> 'hidden',
		'name' 	=> $product->get_meta_key( 'data_location' ),
		'attrs' => [
			'data-price-by' 		=> $price_by,
			'data-waypoint-text' 	=> esc_html__( 'Waypoint', 'ova-brw' ),
			'data-max-waypoint' 	=> $max_waypoint,
			'data-map-types' 		=> json_encode( $map_types ),
			'data-lat' 				=> $latitude,
			'data-lng' 				=> $longitude,
			'data-zoom' 			=> $zoom_map,
			'data-bounds' 			=> $bounds,
			'data-bounds-lat' 		=> $bounds_lat,
			'data-bounds-lng' 		=> $bounds_lng,
			'data-bounds-radius' 	=> $bounds_radius,
			'data-restrictions' 	=> json_encode( $restrictions )
		]
	]); ?>
	<?php ovabrw_text_input([
		'type' 	=> 'hidden',
		'name' 	=> $product->get_meta_key( 'duration_map' ),
		'value' => $duration
	]); ?>
	<?php ovabrw_text_input([
		'type' 	=> 'hidden',
		'name' 	=> $product->get_meta_key( 'duration' ),
		'value' => $duration
	]); ?>
	<?php ovabrw_text_input([
		'type' 	=> 'hidden',
		'name' 	=> $product->get_meta_key( 'distance' ),
		'value' => $distance
	]); ?>
	<div class="ovabrw-directions">
		<div id="ovabrw_map" class="ovabrw_map"></div>
		<div class="directions-info">
			<div class="distance-sum">
				<h3 class="label">
					<?php esc_html_e( 'Total Distance', 'ova-brw' ); ?>
				</h3>
				<span class="distance-value">0</span>
				<?php if ( $price_by === 'km' ): ?>
					<span class="distance-unit">
						<?php esc_html_e( 'km', 'ova-brw' ); ?>
					</span>
				<?php else: ?>
					<span class="distance-unit">
						<?php esc_html_e( 'mi', 'ova-brw' ); ?>
					</span>
				<?php endif; ?>
			</div>
			<div class="duration-sum">
				<h3 class="label">
					<?php esc_html_e( 'Total Time', 'ova-brw' ); ?>
				</h3>
				<span class="hour">0</span>
				<span class="unit">
					<?php esc_html_e( 'h', 'ova-brw' ); ?>
				</span>
				<span class="minute">0</span>
				<span class="unit">
					<?php esc_html_e( 'm', 'ova-brw' ); ?>
				</span>
			</div>
		</div>
	</div>
<?php endif; // End Taxi ?>

<?php if ( 'appointment' == $rental_type ): // Rental type: Appointment
	// Use location
	$use_location = $product->get_meta_value( 'use_location' );
?>
	<!-- Pick-up date -->
	<div class="rental_item">
		<label>
			<?php echo esc_html( $product->get_date_label() ); ?>
		</label>
		<?php ovabrw_text_input([
			'type' 		=> 'text',
	        'id' 		=> ovabrw_unique_id( 'pickup_date' ),
	        'class' 	=> 'pickup-date',
	        'name' 		=> $product->get_meta_key( 'pickup_date' ),
	        'value' 	=> $pickup_date,
	        'required' 	=> true,
	        'data_type' => 'datepicker',
	        'attrs' 	=> [
				'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : ''
			]
		]); ?>
	    <span class="ovabrw-loader-date">
	    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
	    </span>
	</div><!-- End Pick-up date -->
	<?php if ( $use_location ): ?>
		<div class="rental_item ovabrw-time-slots-location-field">
			<label>
				<?php esc_html_e( 'Select Location', 'ova-brw' ); ?>
			</label>
			<div class="ovabrw-time-slots-location"></div>
		</div>
	<?php endif; ?>
	<div class="rental_item full-width ovabrw-time-slots-field">
		<label>
			<?php esc_html_e( 'Select Time', 'ova-brw' ); ?>
		</label>
		<div class="ovabrw-time-slots ovabrw-input-required"></div>
	</div>
	<!-- Drop-off date -->
	<?php if ( $product->show_date_field( 'dropoff' ) ): ?>
		<div class="rental_item">
			<label>
				<?php echo esc_html( $product->get_date_label( 'dropoff' ) ); ?>
			</label>
			<?php ovabrw_text_input([
				'type' 			=> 'text',
		        'class' 		=> 'appointment-dropoff-date',
		        'name' 			=> $product->get_meta_key( 'dropoff_date' ),
		        'required' 		=> true,
		        'placeholder' 	=> OVABRW()->options->get_datetime_placeholder(),
		        'readonly' 		=> true
			]); ?>
			<span class="ovabrw-loader-date">
		    	<i class="brwicon2-spinner-of-dots" aria-hidden="true"></i>
		    </span>
		</div>
	<?php else:
		ovabrw_text_input([
			'type' => 'hidden',
	        'name' => $product->get_meta_key( 'dropoff_date' ),
		]);
	endif; // End Drop-off date
endif; // End Appointment ?>

<!-- Quatity -->
<?php if ( $product->show_quantity() ):
	// Get quantity
	$quantity = $product->get_number_quantity();

	// Get current quantity
	$default_quantity = ovabrw_get_meta_data( 'quantity', $_GET, 1 );
?>
	<div class="rental_item">
		<label>
			<?php esc_html_e( 'Quantity', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_text_input([
			'type'          => 'number',
	        'name'          => $product->get_meta_key( 'quantity' ),
	        'value' 		=> $default_quantity,
	        'required'      => true,
	        'data_type' 	=> 'number',
	        'attrs' 		=> [
	        	'min' => 1,
	        	'max' => $quantity ? $quantity : ''
	        ]
		]); ?>
	</div>
<?php endif; // End Quantity ?>