<?php if ( !defined( 'ABSPATH' ) ) exit();

// Discount prices
$dsc_prices = $this->get_meta_value( 'global_discount_price' );

?>

<div class="ovabrw-advanced-settings">
	<div class="advanced-header">
		<h3 class="advanced-label">
			<?php esc_html_e( 'Global Discount (GD)', 'ova-brw' ); ?>
		</h3>
		<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
		<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
	</div>
	<div class="advanced-content">
		<div class="ovabrw-form-field ovabrw-global-discount">
			<table class="widefat">
				<thead>
					<tr>
						<th class="ovabrw-required">
							<?php if ( $this->is_type( 'day' ) ) {
								esc_html_e( 'Price/Day', 'ova-brw' );
							} elseif ( $this->is_type( 'hour' ) ) {
								esc_html_e( 'Price/Hour', 'ova-brw' );
							} elseif ( $this->is_type( 'mixed' ) ) {
								esc_html_e( 'Price', 'ova-brw' );
							} elseif ( $this->is_type( 'hotel' ) ) {
								esc_html_e( 'Price/Night', 'ova-brw' );
							} else {
								esc_html_e( 'Price/Day', 'ova-brw' );
							} ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'From (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'To (number)', 'ova-brw' ); ?>
						</th>
						<th class="ovabrw-required">
							<?php esc_html_e( 'Duration', 'ova-brw' ); ?>
						</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( ovabrw_array_exists( $dsc_prices ) ):
						// Discount from
						$dsc_from = $this->get_meta_value( 'global_discount_duration_val_min' );

						// Discount to
						$dsc_to = $this->get_meta_value( 'global_discount_duration_val_max' );

						// Discount duration
						$dsc_duration = $this->get_meta_value( 'global_discount_duration_type' );

						// Durations
						$durations = [
							'days' 	=> esc_html__( 'Day(s)', 'ova-brw' ),
							'hours' => esc_html__( 'Hour(s)', 'ova-brw' )
						];

						if ( $this->is_type( 'day' ) ) {
							$durations = [
								'days' 	=> esc_html__( 'Day(s)', 'ova-brw' )
							];
						} elseif ( $this->is_type( 'hotel' ) ) {
							$durations = [
								'days' 	=> esc_html__( 'Night(s)', 'ova-brw' )
							];
						} elseif ( $this->is_type( 'hour' ) ) {
							$durations = [
								'hours' => esc_html__( 'Hour(s)', 'ova-brw' )
							];
						}

						// Loop
						foreach ( $dsc_prices as $i => $price ): ?>
							<tr>
							    <td width="39%" class="ovabrw-input-price">
							    	<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'global_discount_price[]' ),
										'value' 		=> $price,
										'data_type' 	=> 'price',
										'placeholder' 	=> '10.5'
									]); ?>
							    </td>
							    <td width="20%" class="ovabrw-input-price">
							    	<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'global_discount_duration_val_min[]' ),
										'value' 		=> ovabrw_get_meta_data( $i, $dsc_from ),
										'data_type' 	=> 'price',
										'placeholder' 	=> '1'
									]); ?>
							    </td>
							    <td width="20%" class="ovabrw-input-price">
							    	<?php ovabrw_wp_text_input([
										'type' 			=> 'text',
										'class' 		=> 'ovabrw-input-required',
										'name' 			=> $this->get_meta_name( 'global_discount_duration_val_max[]' ),
										'value' 		=> ovabrw_get_meta_data( $i, $dsc_to ),
										'data_type' 	=> 'price',
										'placeholder' 	=> '2'
									]); ?>
							    </td>
							    <td width="20%">
							    	<?php ovabrw_wp_select_input([
							    		'class' 	=> 'ovabrw-input-required',
										'name' 		=> $this->get_meta_name( 'global_discount_duration_type[]' ),
										'value' 	=> ovabrw_get_meta_data( $i, $dsc_duration ),
										'options' 	=> $durations
									]); ?>
							    </td>
							    <td width="1%">
							    	<button class="button ovabrw-remove-gb-discount">x</button>
							    </td>
							</tr>
						<?php endforeach;
					endif; ?>
				</tbody>
				<tfoot>
					<tr>
						<th colspan="5">
							<button class="button ovabrw-add-gb-discount" data-add-new="<?php
								ob_start();
								include( OVABRW_PLUGIN_ADMIN . 'meta-boxes/fields/html-discount-field.php' );
								echo esc_attr( ob_get_clean() );
							?>">
								<?php esc_html_e( 'Add GD', 'ova-brw' ); ?>
							</button>
						</th>
					</tr>
				</tfoot>
			</table>
		</div>
	</div>
</div>