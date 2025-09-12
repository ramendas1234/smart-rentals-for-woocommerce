<?php if ( !defined( 'ABSPATH' ) ) exit();

// Disable Week Days
$disable_weekdays = $this->get_meta_value( 'product_disable_week_day' );

if ( ovabrw_array_exists( $disable_weekdays ) ) {
	$disable_weekdays = str_replace( '0', '7', $disable_weekdays );
	$disable_weekdays = array_map( 'trim', $disable_weekdays );
} else {
	$disable_weekdays = '' != $disable_weekdays ? explode( ',', $disable_weekdays ) : [];
}

?>

<div class="ovabrw_rent_time_min_wrap">
	<p class=" form-field ovabrw_product_disable_week_day_field">
		<label for="ovabrw_product_disable_week_day">
			<?php esc_html_e( 'Disable Weekdays', 'ova-brw' ); ?>
		</label>
		<?php ovabrw_wp_select_input([
			'id' 		=> $this->get_meta_name( 'product_disable_week_day' ),
			'class' 	=> 'ovabrw-select2',
			'name' 		=> $this->get_meta_name( 'product_disable_week_day[]' ),
			'value' 	=> $disable_weekdays,
			'options' 	=> [
				'1' => esc_html__( 'Monday', 'ova-brw' ),
				'2' => esc_html__( 'Tuesday', 'ova-brw' ),
				'3' => esc_html__( 'Wednesday', 'ova-brw' ),
				'4' => esc_html__( 'Thursday', 'ova-brw' ),
				'5' => esc_html__( 'Friday', 'ova-brw' ),
				'6' => esc_html__( 'Saturday', 'ova-brw' ),
				'7' => esc_html__( 'Sunday', 'ova-brw' )
			],
			'multiple' 	=> true
		]); ?>
	</p>
	<?php if ( $this->is_type( 'day' ) ) {
			// Rent Day min
			woocommerce_wp_text_input([
				'id'            => $this->get_meta_name( 'rent_day_min' ),
			    'class'         => 'short ',
			    'label'         => esc_html__( 'Minimum Booking Days', 'ova-brw' ),
			    'placeholder'   => esc_html__( '1', 'ova-brw' ),
			    'desc_tip'      => 'true',
			    'value' 		=> $this->get_meta_value( 'rent_day_min' ),
			    'data_type' 	=> 'price'
			]);

			// Rent Day max
			woocommerce_wp_text_input([
				'id'            => $this->get_meta_name('rent_day_max'),
			    'class'         => 'short ',
			    'label'         => esc_html__( 'Maximum Booking Days', 'ova-brw' ),
			    'placeholder'   => esc_html__( '1', 'ova-brw' ),
			    'desc_tip'      => 'true',
			    'value' 		=> $this->get_meta_value('rent_day_max'),
			    'data_type' 	=> 'price'
			]);
		} elseif ( $this->is_type( 'hotel' ) ) {
			// Rent hotel min
			woocommerce_wp_text_input([
				'id'            => $this->get_meta_name( 'rent_day_min' ),
			    'class'         => 'short ',
			    'label'         => esc_html__( 'Minimum Booking Nights', 'ova-brw' ),
			    'placeholder'   => esc_html__( '1', 'ova-brw' ),
			    'desc_tip'      => 'true',
			    'value' 		=> $this->get_meta_value( 'rent_day_min' ),
			    'data_type' 	=> 'price'
			]);

			// Rent hotel max
			woocommerce_wp_text_input([
				'id'            => $this->get_meta_name( 'rent_day_max' ),
			    'class'         => 'short ',
			    'label'         => esc_html__( 'Maximum Booking Nights', 'ova-brw' ),
			    'placeholder'   => esc_html__( '1', 'ova-brw' ),
			    'desc_tip'      => 'true',
			    'value' 		=> $this->get_meta_value( 'rent_day_max' ),
			    'data_type' 	=> 'price'
			]);
		} elseif ( $this->is_type( 'hour' ) || $this->is_type( 'mixed' ) ) {
			// Rent Hour min
			woocommerce_wp_text_input([
				'id'            => $this->get_meta_name( 'rent_hour_min' ),
			    'class'         => 'short ',
			    'label'         => esc_html__( 'Minimum Booking Hours', 'ova-brw' ),
			    'placeholder'   => esc_html__( '1', 'ova-brw' ),
			    'desc_tip'      => 'true',
			    'value' 		=> $this->get_meta_value( 'rent_hour_min' ),
			    'data_type' 	=> 'price'
			]);

			// Rent Hour max
			woocommerce_wp_text_input([
				'id'            => $this->get_meta_name( 'rent_hour_max' ),
			    'class'         => 'short ',
			    'label'         => esc_html__( 'Maximum Booking Hours', 'ova-brw' ),
			    'placeholder'   => esc_html__( '1', 'ova-brw' ),
			    'desc_tip'      => 'true',
			    'value' 		=> $this->get_meta_value( 'rent_hour_max' ),
			    'data_type' 	=> 'price'
			]);
		}

		// Time between 2 leases
		if ( $this->is_type( 'day' ) || $this->is_type( 'transportation' ) ) {
			woocommerce_wp_text_input([
				'id' 			=> $this->get_meta_name( 'prepare_vehicle_day' ),
				'class' 		=> 'short',
				'label' 		=> esc_html__( 'Time between 2 leases (Day)', 'ova-brw' ),
				'desc_tip' 		=> 'true',
				'description' 	=> esc_html__( 'For example: if the car is delivered on 01/01/2024, I set 1 day to prepare the car so the car is available again to book on 03/01/2024', 'ova-brw' ),
				'placeholder' 	=> '0',
				'value' 		=> $this->get_meta_value( 'prepare_vehicle_day' ),
				'data_type' 	=> 'price'
			]);
		} elseif ( $this->is_type( 'hour' ) || $this->is_type( 'mixed' ) || $this->is_type( 'period_time' ) || $this->is_type( 'taxi' ) ) {
			woocommerce_wp_text_input([
				'id' 			=> $this->get_meta_name( 'prepare_vehicle' ),
				'class' 		=> 'short',
				'label' 		=> esc_html__( 'Time between 2 leases (Minutes)', 'ova-brw' ),
				'desc_tip' 		=> 'true',
				'description' 	=> esc_html__( 'For example: if the car is delivered at 9:00, I set 60 minutes to prepare the car so the car is available again to book at 10:00', 'ova-brw' ),
				'placeholder' 	=> '60',
				'value' 		=> $this->get_meta_value( 'prepare_vehicle' ),
				'data_type' 	=> 'price'
			]);
		}

		woocommerce_wp_text_input([
			'id' 			=> $this->get_meta_name( 'preparation_time' ),
			'class' 		=> 'short',
			'label' 		=> esc_html__( 'Preparation Time', 'ova-brw' ),
			'desc_tip' 		=> 'true',
			'description' 	=> esc_html__( 'Book in advance X days from the current date', 'ova-brw' ),
			'placeholder' 	=> esc_html__( 'Number of days', 'ova-brw' ),
			'value' 		=> $this->get_meta_value( 'preparation_time' ),
			'custom_attributes' => [
				'min' => 0
			],
			'data_type' 	=> 'price'
		]);
	?>
</div>