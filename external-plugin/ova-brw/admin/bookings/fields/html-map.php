<?php if ( !defined( 'ABSPATH' ) ) exit();

$zoom_map       = $this->get_meta_value( 'zoom_map' );
$latitude       = $this->get_meta_value( 'latitude' );
$longitude      = $this->get_meta_value( 'longitude' );
$price_by       = $this->get_meta_value( 'map_price_by' );
$map_types      = $this->get_meta_value( 'ovabrw_map_types' );
$bounds         = $this->get_meta_value( 'ovabrw_bounds' );
$bounds_lat     = $this->get_meta_value( 'bounds_lat' );
$bounds_lng     = $this->get_meta_value( 'bounds_lng' );
$bounds_radius  = $this->get_meta_value( 'bounds_radius' );
$restrictions   = $this->get_meta_value( 'restrictions' );

if ( ! $price_by ) $price_by = 'km';
if ( ! $map_types ) $map_types = [ 'geocode' ];
if ( ! $restrictions ) $restrictions = [];

?>

<div class="ovabrw-directions">
    <div id="<?php echo esc_attr( 'ovabrw_map_'.$this->get_id() ); ?>" class="ovabrw_map"></div>
    <div class="directions-info">
        <div class="distance-sum">
            <h3 class="label"><?php esc_html_e( 'Total Distance', 'ova-brw' ); ?></h3>
            <span class="distance-value">0</span>
            <span class="distance-unit"><?php esc_html_e( 'km', 'ova-brw' ); ?></span>
        </div>
        <div class="duration-sum">
            <h3 class="label"><?php esc_html_e( 'Total Time', 'ova-brw' ); ?></h3>
            <span class="hour">0</span>
            <span class="unit"><?php esc_html_e( 'h', 'ova-brw' ); ?></span>
            <span class="minute">0</span>
            <span class="unit"><?php esc_html_e( 'm', 'ova-brw' ); ?></span>
        </div>
    </div>
</div>
<?php
	// Map data
	ovabrw_text_input([
	    'type' 	=> 'hidden',
	    'name' 	=> 'ovabrw_map_data',
	    'key' 	=> 'ovabrw-item-key',
	    'attrs' => [
	    	'data-waypoint-text' 	=> esc_html__( 'Waypoint', 'ova-brw' ),
	    	'data-price-by' 		=> $price_by,
	    	'data-types' 			=> json_encode( $map_types ),
	    	'data-lat' 				=> $latitude,
	    	'data-lng' 				=> $longitude,
	    	'data-zoom' 			=> $zoom_map,
	    	'data-bounds' 			=> $bounds,
	    	'data-bounds-lat' 		=> $bounds_lat,
	    	'data-bounds-lng' 		=> $bounds_lng,
	    	'data-bounds-radius' 	=> $bounds_radius,
	    	'data-restrictions' 	=> json_encode( $restrictions )
	    ]
	]);

	// Duration map
	ovabrw_text_input([
	    'type' 	=> 'hidden',
	    'name' 	=> 'ovabrw_duration_map',
	    'key' 	=> 'ovabrw-item-key',
	    'value' => ''
	]);

	// Duration
	ovabrw_text_input([
	    'type' 	=> 'hidden',
	    'name' 	=> 'ovabrw_duration',
	    'key' 	=> 'ovabrw-item-key',
	    'value' => ''
	]);

	// Distance
	ovabrw_text_input([
	    'type' 	=> 'hidden',
	    'name' 	=> 'ovabrw_distance',
	    'key' 	=> 'ovabrw-item-key',
	    'value' => ''
	]);
?>