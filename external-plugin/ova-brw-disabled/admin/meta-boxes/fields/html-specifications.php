<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get product
$product = wc_get_product( $this->get_id() );
if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) {
	$specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );
} else {
	// Get specifications
	$specifications = $product->get_specifications();
}

if ( ovabrw_array_exists( $specifications ) ):
	$current_specifications = $this->get_meta_value( 'specifications' );
?>
	<div class="ovabrw-advanced-settings">
		<div class="advanced-header">
			<h3 class="advanced-label"><?php esc_html_e( 'Specifications', 'ova-brw' ); ?></h3>
			<span aria-hidden="true" class="dashicons dashicons-arrow-up"></span>
			<span aria-hidden="true" class="dashicons dashicons-arrow-down"></span>
		</div>
		<div class="advanced-content">
			<div class="ovabrw-form-field ovabrw-specifications">
				<table class="widefat">
					<thead>
						<tr>
							<th width="30%"><?php esc_html_e( 'Label', 'ova-brw' ); ?></th>
							<th width="70%"><?php esc_html_e( 'Value', 'ova-brw' ); ?></th>
						</tr>
					</thead>
					<tbody>
					<?php foreach ( $specifications as $name => $item ):
						if ( !ovabrw_get_meta_data( 'enable', $item ) ) continue;

						$type 		= ovabrw_get_meta_data( 'type', $item );
						$label 		= ovabrw_get_meta_data( 'label', $item );
						$default 	= ovabrw_get_meta_data( 'default', $item );
						$options 	= ovabrw_get_meta_data( 'options', $item );

						if ( isset( $current_specifications[$name] ) ) {
							$default = $current_specifications[$name];
						}
					?>
						<tr>
							<td>
								<strong><?php echo esc_html( $label ); ?></strong>
							</td>
							<td>
							<?php if ( 'number' === $type ): ?>
								<input
									type="number"
									name="<?php echo esc_attr( 'ovabrw_specifications['.$name.']' ); ?>"
									value="<?php echo esc_attr( $default ); ?>"
									autocomplete="off"
								/>
							<?php elseif ( 'email' === $type ): ?>
								<input
									type="email"
									name="<?php echo esc_attr( 'ovabrw_specifications['.$name.']' ); ?>"
									value="<?php echo esc_attr( $default ); ?>"
									autocomplete="off"
								/>
							<?php elseif ( 'radio' === $type ): ?>
								<div class="specification-radio">
									<?php if ( ovabrw_array_exists( $options ) ):
										foreach ( $options as $option ):
											$checked = '';

											if ( is_string( $default ) && $default == $option ) {
												$checked = ' checked';
											} elseif ( is_array( $default ) && in_array( $option, $default ) ) {
												$checked = ' checked';
											}
									?>
										<label>
											<input
												type="radio"
												name="<?php echo esc_attr( 'ovabrw_specifications['.$name.'][]' ); ?>"
												value="<?php echo esc_attr( $option ); ?>"
												<?php echo esc_attr( $checked ); ?>
											/>
											<span><?php echo esc_html( $option ); ?></span>
										</label>
									<?php endforeach;
									endif; ?>	
								</div>
							<?php elseif ( 'checkbox' === $type ): ?>
								<div class="specification-checkbox">
									<?php if ( ovabrw_array_exists( $options ) ):
										foreach ( $options as $option ):
											$checked = '';

											if ( is_string( $default ) && $default == $option ) {
												$checked = ' checked';
											} elseif ( is_array( $default ) && in_array( $option, $default ) ) {
												$checked = ' checked';
											}
									?>
										<label>
											<input
												type="checkbox"
												name="<?php echo esc_attr( 'ovabrw_specifications['.$name.'][]' ); ?>"
												value="<?php echo esc_attr( $option ); ?>"
												<?php echo esc_attr( $checked ); ?>
											/>
											<span><?php echo esc_html( $option ); ?></span>
										</label>
									<?php endforeach;
									endif; ?>	
								</div>
							<?php elseif ( 'select' === $type ):
								$multiple = '';

								if ( isset( $item['multiple'] ) && $item['multiple'] ) {
									$multiple = ' multiple';
								}
							?>
								<select
									name="<?php echo esc_attr( 'ovabrw_specifications['.$name.'][]' ); ?>"
									class="ovabrw-select2"
									data-placeholder="..."
									<?php echo esc_attr( $multiple ); ?>
								>
								<?php if ( ovabrw_array_exists( $options ) ):
									foreach ( $options as $option ):
										$selected = '';

										if ( is_string( $default ) && $default == $option ) {
											$selected = ' selected';
										} elseif ( is_array( $default ) && in_array( $option, $default ) ) {
											$selected = ' selected';
										}
								?>
									<option value="<?php echo esc_attr( $option ); ?>"<?php echo esc_attr( $selected ); ?>>
										<?php echo esc_html( $option ); ?>
									</option>
								<?php endforeach;
								endif; ?>	
								</select>
							<?php elseif ( 'date' === $type ): ?>
								<input
									type="text"
									id="<?php echo esc_attr( ovabrw_unique_id( 'specification_date' ) ); ?>"
									class="ovabrw-datepicker start-date"
									name="<?php echo esc_attr( 'ovabrw_specifications['.$name.']' ); ?>"
									value="<?php echo esc_attr( $default ); ?>"
								/>
							<?php elseif ( 'color' === $type ): ?>
								<input
									type="color"
									name="<?php echo esc_attr( 'ovabrw_specifications['.$name.']' ); ?>"
									value="<?php echo esc_attr( $default ); ?>"
								/>
							<?php elseif ( 'file' === $type ):
								$attachment_title = $attachment_url = '';

								if ( $default ) {
									$attachment_title 	= get_the_title( $default );
									$attachment_url 	= get_edit_post_link( $default );
								}
							?>
								<button class="button button-primary specification-add-file">
									<?php esc_html_e( 'Add file', 'ova-brw' ); ?>
								</button>
								<p class="file-default" style="<?php echo $default ? 'display: block;' : ''; ?>">
									<a href="<?php echo esc_url( $attachment_url ); ?>" target="_blank">
										<?php echo esc_html( $attachment_title ); ?>
									</a>
									<button class="btn">X</button>
								</p>
								<input
									type="hidden"
									class="attachment-id"
									name="<?php echo esc_attr( 'ovabrw_specifications['.$name.']' ); ?>"
									value="<?php echo esc_attr( $default ); ?>"
								/>
							<?php else: ?>
								<input
									type="text"
									name="<?php echo esc_attr( 'ovabrw_specifications['.$name.']' ); ?>"
									value="<?php echo esc_attr( $default ); ?>"
									autocomplete="off"
								/>
							<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
<?php endif;