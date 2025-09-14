<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
	<td width="30%" class="ovabrw-input-price">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required ovabrw-special-distance-discount-from',
			'name' 			=> $this->get_meta_name( 'st_discount_distance[index][from][]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> esc_html__( 'Number', 'ova-brw' )
		]); ?>
	</td>
	<td width="30%" class="ovabrw-input-price">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required ovabrw-special-distance-discount-to',
			'name' 			=> $this->get_meta_name( 'st_discount_distance[index][to][]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> esc_html__( 'Number', 'ova-brw' )
		]); ?>
	</td>
	<td width="30%" class="ovabrw-input-price">
		<?php ovabrw_wp_text_input([
			'type' 			=> 'text',
			'class' 		=> 'ovabrw-input-required ovabrw-special-distance-discount-price',
			'name' 			=> $this->get_meta_name( 'st_discount_distance[index][price][]' ),
			'data_type' 	=> 'price',
			'placeholder' 	=> esc_html__( 'Price', 'ova-brw' )
		]); ?>
	</td>
	<td width="1%">
		<button class="button ovabrw-remove-st-discount-distance">x</button>
	</td>
</tr>