<?php if ( !defined( 'ABSPATH' ) ) exit();

// Check show service for request booking form
if ( 'yes' !== ovabrw_get_setting( 'request_booking_form_show_service', 'yes' ) ) return;

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get service labels
$serv_labels = $product->get_meta_value( 'label_service' );

if ( ovabrw_array_exists( $serv_labels ) ):
	$serv_required 	= $product->get_meta_value( 'service_required' );
	$serv_ids 		= $product->get_meta_value( 'service_id' );
	$serv_names 	= $product->get_meta_value( 'service_name' );
	$ser_qtys 		= $product->get_meta_value( 'service_qty' );
?>
	<div class="ovabrw-services">
		<label>
			<?php esc_html_e( 'Services', 'ova-brw' ); ?>
		</label>
		<div class="ovabrw-service">
			<?php foreach ( $serv_labels as $k => $label ):
				$required = ovabrw_get_meta_data( $k, $serv_required );
				if ( 'yes' != $required ) $required = '';

				// Option ID
				$opt_ids = ovabrw_get_meta_data( $k, $serv_ids, [] );

				// Option name
				$opt_names = ovabrw_get_meta_data( $k, $serv_names, [] );

				// Option quantity
				$opt_qtys = ovabrw_get_meta_data( $k, $ser_qtys, [] );

				// Options
				$options = [];

				// Option quantity
				$quantities = [];

				foreach ( $opt_ids as $i => $opt_id ) {
					$opt_name 	= ovabrw_get_meta_data( $i, $opt_names );
					$opt_qty 	= ovabrw_get_meta_data( $i, $opt_qtys );

					if ( $opt_id && $opt_name ) {
						$options[$opt_id] = $opt_name;

						if ( '' != $opt_qty ) $quantities[$opt_id] = (int)$opt_qty;
					}
				}

				if ( !ovabrw_array_exists( $options ) ) continue;
			?>
				<div class="rental_item">
					<?php ovabrw_select_input([
						'name' 			=> $product->get_meta_key( 'service[]' ),
						'name_qty' 		=> $product->get_meta_key( 'service' ),
						'placeholder' 	=> sprintf( esc_html__( 'Select %s', 'ova-brw' ), $label ),
						'options' 		=> $options,
						'quantities' 	=> $quantities,
						'required' 		=> $required
					]); ?>
				</div>
			<?php endforeach; ?>
		</div>
	</div>
<?php endif;