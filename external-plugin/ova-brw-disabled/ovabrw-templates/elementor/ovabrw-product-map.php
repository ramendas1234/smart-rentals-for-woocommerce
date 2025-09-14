<?php if ( !defined( 'ABSPATH' ) ) exit();

$product_id = ovabrw_get_meta_data( 'id', $args, get_the_id() );
$zoom       = ovabrw_get_meta_data( 'zoom', $args, 17 );
$address    = get_post_meta( $product_id, 'ovabrw_address', true );
$latitude   = get_post_meta( $product_id, 'ovabrw_latitude', true );
$longitude  = get_post_meta( $product_id, 'ovabrw_longitude', true );

if ( ! $latitude ) $latitude 	= ovabrw_get_setting( 'latitude_map_default', '39.177972' );
if ( ! $longitude ) $longitude 	= ovabrw_get_setting( 'longitude_map_default', '-100.36375' );

?>
<div class="ovabrw-product-map">
	<div id="ovabrw-show-map" class="ovabrw-show-map"></div>
	<?php ovabrw_text_input([
		'type' 	=> 'hidden',
		'class' => 'ovabrw-data-product-map',
		'attrs' => [
			'data-zoom' => $zoom,
			'latitude' 	=> $latitude,
			'longitude' => $longitude
		]
	]); ?>
	<?php ovabrw_text_input([
		'type' 	=> 'hidden',
		'id' 	=> 'pac-input',
		'class' => 'pac-input',
		'name' 	=> 'pac-input',
		'value' => $address
	]); ?>
</div>