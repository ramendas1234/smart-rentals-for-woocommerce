<?php if ( !defined( 'ABSPATH' ) ) exit();
/**
 * Variables used in this file.
 *
 * @var int   	$product_id 	The product ID
 * @var int   	$zoom 			The zoom map
 * @var int   	$latitude 		The latitude map
 * @var int   	$longitude 		The longitude map
 * @var string 	$address 		The address map
 */

?>

<div class="ovabrw-product-map">
	<div id="ovabrw-show-map" class="ovabrw-show-map"></div>
	<?php ovabrw_text_input([
	    'type'      => 'hidden',
	    'class'     => 'ovabrw-data-product-map',
	    'attrs' 	=> [
	    	'data-zoom' => $zoom,
	    	'latitude' 	=> $latitude,
	    	'longitude' => $longitude
	    ]
	]); ?>
	<?php ovabrw_text_input([
	    'type'      => 'hidden',
	    'class'     => 'pac-input',
	    'name'     	=> 'pac-input',
	    'value' 	=> $address
	]); ?>
</div>