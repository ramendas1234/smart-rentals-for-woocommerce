<?php if ( !defined( 'ABSPATH' ) ) exit();

// Durations
$durations = [
	'total' => esc_html__( '/Order', 'ova-brw' )
];

if ( $this->is_type( 'day' ) ) {
	$durations = [
		'days' 	=> esc_html__( '/Day', 'ova-brw' ),
		'total' => esc_html__( '/Order', 'ova-brw' )
	];
} elseif ( $this->is_type( 'hotel' ) ) {
	$durations = [
		'days' 	=> esc_html__( '/Night', 'ova-brw' ),
		'total' => esc_html__( '/Order', 'ova-brw' )
	];
} elseif ( $this->is_type( 'hour' ) ) {
	$durations = [
		'hours' => esc_html__( '/Hour', 'ova-brw' ),
		'total' => esc_html__( '/Order', 'ova-brw' )
	];
} elseif ( $this->is_type( 'mixed' ) || $this->is_type( 'period_time' ) ) {
	$durations = [
		'days' 	=> esc_html__( '/Day', 'ova-brw' ),
		'hours' => esc_html__( '/Hour', 'ova-brw' ),
		'total' => esc_html__( '/Order', 'ova-brw' )
	];
}

?>

<tr>
    <td width="15%">
    	<?php ovabrw_wp_text_input([
    		'type' 			=> 'text',
    		'class' 		=> 'ovabrw-input-required ovabrw-service-id',
    		'name' 			=> $this->get_meta_name( 'service_id[index][]' ),
    		'value' 		=> '[serviceOptionID]',
    		'placeholder' 	=> esc_html__( 'Not space', 'ova-brw' )
    	]); ?>
    </td>
    <td width="34%">
    	<?php ovabrw_wp_text_input([
    		'type' 			=> 'text',
    		'class' 		=> 'ovabrw-input-required ovabrw-service-name',
    		'name' 			=> $this->get_meta_name( 'service_name[index][]' ),
    		'placeholder' 	=> esc_html__( 'Name', 'ova-brw' )
    	]); ?>
    </td>
    <td width="20%" class="ovabrw-input-price">
    	<?php ovabrw_wp_text_input([
    		'type' 			=> 'text',
    		'class' 		=> 'ovabrw-input-required ovabrw-service-price',
    		'name' 			=> $this->get_meta_name( 'service_price[index][]' ),
    		'data_type' 	=> 'price',
    		'placeholder' 	=> esc_html__( 'Price', 'ova-brw' )
    	]); ?>
    </td>
    <td width="15%">
    	<?php ovabrw_wp_text_input([
    		'type' 			=> 'number',
    		'class' 		=> 'ovabrw-service-qty',
    		'name' 			=> $this->get_meta_name( 'service_qty[index][]' ),
    		'placeholder' 	=> esc_html__( 'Number', 'ova-brw' )
    	]); ?>
    </td>
    <td width="15%">
      	<?php ovabrw_wp_select_input([
    		'class' 	=> 'ovabrw-input-required ovabrw-service-duration',
    		'name' 		=> $this->get_meta_name( 'service_duration_type[index][]' ),
    		'options' 	=> $durations
    	]); ?>
    </td>
    <td width="1%">
    	<button class="button ovabrw-remove-service-option">x</button>
    </td>
</tr>