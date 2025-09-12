<?php if ( !defined( 'ABSPATH' ) ) exit();

$map_price_by 	= $this->get_meta_value( 'map_price_by', 'km' );
$waypoint 		= $this->get_meta_value( 'waypoint', 'on' );
$max_waypoint 	= $this->get_meta_value( 'max_waypoint' );
$zoom_map 		= $this->get_meta_value( 'zoom_map', 4 );
$map_types 		= $this->get_meta_value( 'map_types' );
$bounds 		= $this->get_meta_value( 'bounds' );
$bounds_lat 	= $this->get_meta_value( 'bounds_lat' );
$bounds_lng 	= $this->get_meta_value( 'bounds_lng' );
$bounds_radius 	= $this->get_meta_value( 'bounds_radius' );
$countries 		= ovabrw_iso_alpha2();
$restrictions 	= $this->get_meta_value( 'restrictions' );

if ( !$map_types ) $map_types = [ 'geocode' ];

?>
<!-- Setup Map -->
<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Setup Map', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-taxi-map">
			<!-- Types -->
			<p class="form-field ovabrw_map_price_by_field">
				<label for="ovabrw_map_price_by">
					<?php esc_html_e( 'Price by per', 'ova-brw' ); ?>
				</label>
				<span class="map-price-by">
					<span class="map-price-by-input">
						<input
							type="radio"
							id="map-price-km"
							name="<?php echo esc_attr( $this->get_meta_name( 'map_price_by' ) ); ?>"
							value="km"
							<?php ovabrw_checked( $map_price_by, 'km' ); ?>
						/>
						<label class="label" for="map-price-km">
							<?php esc_html_e( 'km', 'ova-brw' ); ?>
						</label>
					</span>
					<span class="map-price-by-input">
						<input
							type="radio"
							id="map-price-mi"
							name="<?php echo esc_attr( $this->get_meta_name( 'map_price_by' ) ); ?>"
							value="mi"
							<?php ovabrw_checked( $map_price_by, 'mi' ); ?>
						/>
						<label class="label" for="map-price-mi">
							<?php esc_html_e( 'mi', 'ova-brw' ); ?>
						</label>
					</span>
				</span>
			</p>
			<p class="form-field ovabrw_waypoint_field ">
				<label for="ovabrw_waypoint">
					<?php esc_html_e( 'Waypoints', 'ova-brw' ); ?>
				</label>
				<input 
					type="checkbox"
					id="ovabrw_waypoint"
					name="<?php echo esc_attr( $this->get_meta_name( 'waypoint' ) ); ?>"
					<?php ovabrw_checked( $waypoint, 'on' ); ?>
				>
				<span class="max_waypoint">
					<span class="label"><?php esc_html_e( 'Maximum Waypoint' ); ?></span>
					<input
						type="number"
						name="<?php echo esc_attr( $this->get_meta_name( 'max_waypoint' ) ); ?>"
						value="<?php echo esc_attr( $max_waypoint ); ?>"
						placeholder="0"
						autocomplete="off"
					/>
				</span>
			</p>
			<?php woocommerce_wp_text_input([
		 		'id' 			=> $this->get_meta_name( 'zoom_map' ),
				'class' 		=> 'short ',
				'label' 		=> esc_html__( 'Zoom Map', 'ova-brw' ),
				'placeholder' 	=> '4',
				'type' 			=> 'number',
				'value' 		=> $zoom_map
		 	]); ?>

			<!-- Types -->
			<p class="form-field ovabrw_map_types_field ">
				<label for="ovabrw_map_types">
					<?php esc_html_e( 'Types', 'ova-brw' ); ?>
				</label>
				<span class="map-types">
					<span class="map-type-input">
						<input
							type="radio"
							id="map-geocode"
							name="<?php echo esc_attr( $this->get_meta_name( 'map_types[]' ) ); ?>"
							value="geocode"
							<?php ovabrw_checked( 'geocode', $map_types ); ?>
						/>
						<label class="label" for="map-geocode">
							<?php esc_html_e( 'Geocode', 'ova-brw' ); ?>
						</label>
					</span>
					<span class="map-type-input">
						<input
							type="radio"
							id="map-address"
							name="<?php echo esc_attr( $this->get_meta_name( 'map_types[]' ) ); ?>"
							value="address"
							<?php ovabrw_checked( 'address', $map_types ); ?>
						/>
						<label class="label" for="map-address">
							<?php esc_html_e( 'Address', 'ova-brw' ); ?>
						</label>
					</span>
					<span class="map-type-input">
						<input
							type="radio"
							id="map-establishment"
							name="<?php echo esc_attr( $this->get_meta_name( 'map_types[]' ) ); ?>"
							value="establishment"
							<?php ovabrw_checked( 'establishment', $map_types ); ?>
						/>
						<label class="label" for="map-establishment">
							<?php esc_html_e( 'Establishment', 'ova-brw' ); ?>
						</label>
					</span>
				</span>
			</p>
			<p class="form-field ovabrw_bounds_field ">
				<label for="ovabrw_bounds">
					<?php esc_html_e( 'Bounds', 'ova-brw' ); ?>
				</label>
				<input 
					type="checkbox"
					id="ovabrw_bounds"
					name="<?php echo esc_attr( $this->get_meta_name( 'bounds' ) ); ?>"
					<?php ovabrw_checked( $bounds, 'on' ); ?>
				>
				<span class="coordinates">
					<span class="bounds-lat">
						<span class="label">
							<?php esc_html_e( 'Latitude', 'ova-brw' ); ?>
						</span>
						<input
							type="text"
							name="<?php echo esc_attr( $this->get_meta_name( 'bounds_lat' ) ); ?>"
							value="<?php echo esc_attr( $bounds_lat ); ?>"
							autocomplete="off"
						/>
					</span>
					<span class="bounds-lng">
						<span class="label">
							<?php esc_html_e( 'Longitude', 'ova-brw' ); ?>
						</span>
						<input
							type="text"
							name="<?php echo esc_attr( $this->get_meta_name( 'bounds_lng' ) ); ?>"
							value="<?php echo esc_attr( $bounds_lng ); ?>"
							autocomplete="off"
						/>
					</span>
					<span class="bounds-radius">
						<span class="label">
							<?php esc_html_e( 'Radius(meters)', 'ova-brw' ); ?>
						</span>
						<input
							type="text"
							name="<?php echo esc_attr( $this->get_meta_name( 'bounds_radius' ) ); ?>"
							value="<?php echo esc_attr( $bounds_radius ); ?>"
							autocomplete="off"
						/>
					</span>
				</span>
			</p>

			<!-- Component Restrictions -->
			<p class="form-field ovabrw_restrictions_field ">
				<label for="ovabrw_restrictions">
					<?php esc_html_e( 'Restrictions', 'ova-brw' ); ?>
				</label>
				<select name="<?php echo esc_attr( $this->get_meta_name( 'restrictions[]' ) ); ?>" id="ovabrw_restrictions" data-placeholder="<?php esc_html_e( 'Select country', 'ova-brw' ); ?>" multiple>
					<?php foreach ( $countries as $country_code => $country_name ): ?>
						<option value="<?php echo esc_attr( $country_code ); ?>"<?php ovabrw_selected( $country_code, $restrictions ); ?>>
							<?php echo esc_html( $country_name ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</p>
		</div>
	</div>
</div>