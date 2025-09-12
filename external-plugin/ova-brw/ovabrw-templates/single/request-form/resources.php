<?php if ( !defined( 'ABSPATH' ) ) exit();

// Check show resources for request booking form
if ( 'yes' !== ovabrw_get_setting( 'request_booking_form_show_extra_service', 'yes' ) ) return;

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get resource IDs
$res_ids = $product->get_meta_value( 'resource_id' );

if ( ovabrw_array_exists( $res_ids ) ):
	$res_names 	= $product->get_meta_value( 'resource_name' );
	$res_prices = $product->get_meta_value( 'resource_price' );
	$res_types 	= $product->get_meta_value( 'resource_duration_type' );
	$res_qtys 	= $product->get_meta_value( 'resource_quantity' );
?>
	<div class="ovabrw-extra-services">
		<label>
			<?php esc_html_e( 'Resources', 'ova-brw' ); ?>
		</label>
		<div class="ovabrw_resource">
			<?php foreach ( $res_ids as $k => $id ):
				$name 	= ovabrw_get_meta_data( $k, $res_names );
				$price 	= ovabrw_get_meta_data( $k, $res_prices );
				$type 	= ovabrw_get_meta_data( $k, $res_types );
				$qty 	= (int)ovabrw_get_meta_data( $k, $res_qtys );

				if ( 'days' === $type ) $type = esc_html__( '/day', 'ova-brw' );
				if ( 'hours' === $type ) $type = esc_html__( '/hour', 'ova-brw' );
				if ( 'total' === $type ) $type = esc_html__( '/order', 'ova-brw' );

				if ( $id && $name ): ?>
					<div class="item">
						<div class="res-left">
							<label class="ovabrw-label-field">
								<?php echo esc_html( $name ); ?>
								<?php ovabrw_text_input([
									'type' 	=> 'checkbox',
									'name' 	=> $product->get_meta_key( 'resource_checkboxs['.esc_attr( $id ).']' ),
									'value' => $id,
									'attrs' => [
										'data-id' => $id
									]
								]); ?>
								<span class="checkmark"></span>
							</label>
						</div>
						<div class="res-right">
							<div class="res-unit">
								<span class="res-price">
									<?php echo ovabrw_wc_price( $price); ?>
								</span>
								<span class="res-type">
									<?php echo esc_html( $type ); ?>
								</span>
							</div>
							<?php if ( $qty > 1 ): ?>
								<div class="checkbox-item-qty" data-option="<?php echo esc_attr( $id ); ?>">
									<span class="checkbox-qty">1</span>
									<?php ovabrw_text_input([
										'type' 	=> 'text',
										'class' => 'checkbox-input-qty',
										'name' 	=> $product->get_meta_key( 'resource_quantity['.esc_attr( $id ).']' ),
										'value'	=> 1,
										'attrs' => [
											'min' => 1,
											'max' => $qty
										]
									]); ?>
									<div class="ovabrw-checkbox-icon">
										<i class="brwicon2-up-arrow" aria-hidden="true"></i>
										<i class="brwicon2-down-arrow" aria-hidden="true"></i>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif;
			endforeach; ?>
		</div>
	</div>
<?php endif;