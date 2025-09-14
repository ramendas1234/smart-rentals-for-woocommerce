<?php defined( 'ABSPATH' ) || exit();

// Get specifications
$specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

// Get data
$label 		= isset( $specifications[$name]['label'] ) ? $specifications[$name]['label'] : '';
$icon_font 	= isset( $specifications[$name]['icon-font'] ) ? $specifications[$name]['icon-font'] : '';
$default 	= isset( $specifications[$name]['default'] ) ? $specifications[$name]['default'] : '';
$class 		= isset( $specifications[$name]['class'] ) ? $specifications[$name]['class'] : '';
$options 	= isset( $specifications[$name]['options'] ) ? $specifications[$name]['options'] : '';
$enable 	= isset( $specifications[$name]['enable'] ) ? $specifications[$name]['enable'] : '';
$show_label = isset( $specifications[$name]['show_label'] ) ? $specifications[$name]['show_label'] : '';
$show_in_card = isset( $specifications[$name]['show_in_card'] ) ? $specifications[$name]['show_in_card'] : '';
?>

<form action="" method="post" id="ovabrw-popup-specification-form" enctype="multipart/form-data" autocomplete="off">
	<?php ovabrw_wp_text_input([
		'type' 	=> 'hidden',
		'name' 	=> 'ovabrw-specificaiton-action',
		'value' => $action
	]); ?>
	<button class="popup-specification-close">X</button>
	<table width="100%">
		<tbody>
			<?php add_action( OVABRW_PREFIX.'before_popup_edit_specification', $action, $type, $name ); ?>
			<tr class="type">
				<td class="ovabrw-required label">
					<?php esc_html_e( 'Select type', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_select_input([
						'id' 		=> 'type',
						'name' 		=> 'type',
						'value' 	=> $type,
						'options' 	=> $this->get_types(),
						'required' 	=> true
					]); ?>
				</td>
			</tr>
			<tr class="name">
				<td class="ovabrw-required label">
					<?php esc_html_e( 'Name', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_text_input([
						'name' 		=> 'name',
						'value' 	=> $name,
						'required' 	=> true,
						'attrs' 	=> [
							'autocomplete' => 'off'
						]
					]); ?>
					<?php ovabrw_wp_text_input([
						'type' 		=> 'hidden',
						'name' 		=> 'old_name',
						'value' 	=> $name,
						'required' 	=> true,
						'attrs' 	=> [
							'autocomplete' => 'off'
						]
					]); ?>
					<span>
						<em>
							<?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>
						</em>
					</span>
				</td>
			</tr>
			<tr class="label">
				<td class="ovabrw-required label">
					<?php esc_html_e( 'Label', 'ova-brw' ); ?>
				</td>
				<td>
					<?php ovabrw_wp_text_input([
						'name' 		=> 'label',
						'value' 	=> $label,
						'required' 	=> true,
						'attrs' 	=> [
							'autocomplete' => 'off'
						]
					]); ?>
				</td>
			</tr>
			<?php if ( in_array( $type, ['radio', 'checkbox', 'select'] ) ): // Radio ?>
				<tr class="radio-options specification-options">
					<td class="label"><?php esc_html_e( 'Options(*)', 'ova-brw' ); ?></td>
					<td>
						<table class="widefat" width="100%">
							<thead>
								<tr>
									<th class="ovabrw-required">
										<?php esc_html_e( 'Options', 'ova-brw' ); ?>
									</th>
									<th><?php esc_html_e( 'Actions', 'ova-brw' ); ?></th>
								</tr>
							</thead>
							<tbody class="option-sortable">
							<?php if ( ovabrw_array_exists( $options ) ): ?>
								<?php foreach ( $options as $v ): ?>
									<tr>
										<td>
											<?php ovabrw_wp_text_input([
												'name' 		=> 'options[]',
												'value' 	=> $v,
												'required' 	=> true,
												'attrs' 	=> [
													'autocomplete' => 'off'
												]
											]); ?>
										</td>
										<td>
											<span class="btn btn-add-new">+</span>
											<span class="btn btn-add-remove">x</span>
											<span class="dashicons dashicons-menu-alt3"></span>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td>
										<?php ovabrw_wp_text_input([
											'name' 		=> 'options[]',
											'required' 	=> true,
											'attrs' 	=> [
												'autocomplete' => 'off'
											]
										]); ?>
									</td>
									<td>
										<span class="btn btn-add-new">+</span>
										<span class="btn btn-add-remove">x</span>
										<span class="dashicons dashicons-menu-alt3"></span>
									</td>
								</tr>
							<?php endif; ?>
							</tbody>
						</table>
					</td>
				</tr>
			<?php endif; // End radio ?>
			<tr class="icon-font">
				<td class="label"><?php esc_html_e( 'Icon font', 'ova-brw' ); ?></td>
				<td>
					<?php ovabrw_wp_text_input([
						'name' 		=> 'icon-font',
						'value' 	=> $icon_font,
						'attrs' 	=> [
							'autocomplete' => 'off'
						]
					]); ?>
					<span>
						<em>
							<?php esc_html_e( 'Please enter an icon font class (e.g: flaticon-users)', 'ova-brw' ); ?>
						</em>
					</span>
				</td>
			</tr>
			<tr class="default">
				<td class="label"><?php esc_html_e( 'Default', 'ova-brw' ); ?></td>
				<td>
					<?php if ( 'date' === $type ):
						ovabrw_wp_text_input([
							'id' 	=> ovabrw_unique_id( 'specification_date' ),
							'class' => 'ovabrw-datepicker',
							'name' 	=> 'default',
							'value' => $default,
							'attrs' => [
								'autocomplete' => 'off'
							]
						]);
					elseif ( 'link' === $type ):
						ovabrw_wp_text_input([
							'type' 	=> 'url',
							'name' 	=> 'default',
							'value' => $default,
							'attrs' => [
								'autocomplete' => 'off'
							]
						]);
					elseif ( 'number' === $type ):
						ovabrw_wp_text_input([
							'type' 	=> 'number',
							'name' 	=> 'default',
							'value' => $default,
							'attrs' => [
								'autocomplete' => 'off'
							]
						]);
					elseif ( 'email' === $type ):
						ovabrw_wp_text_input([
							'type' 	=> 'email',
							'name' 	=> 'default',
							'value' => $default,
							'attrs' => [
								'autocomplete' => 'off'
							]
						]);
					elseif ( 'color' === $type ):
						ovabrw_wp_text_input([
							'type' 	=> 'color',
							'name' 	=> 'default',
							'value' => $default
						]);
					elseif ( 'file' === $type ):
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
						<?php ovabrw_wp_text_input([
							'type' 	=> 'hidden',
							'name' 	=> 'default',
							'value' => $default
						]);
					else:
						ovabrw_wp_text_input([
							'name' 	=> 'default',
							'value' => $default,
							'attrs' => [
								'autocomplete' => 'off'
							]
						]);
					endif; ?>
				</td>
			</tr>
			<tr class="class">
				<td class="label"><?php esc_html_e( 'Class', 'ova-brw' ); ?></td>
				<td>
					<?php ovabrw_wp_text_input([
						'name' 	=> 'class',
						'value' => $class
					]); ?>
				</td>
			</tr>
			<?php add_action( OVABRW_PREFIX.'after_popup_edit_specification', $action, $type, $name ); ?>
			<tr class="status">
				<td class="label"></td>
				<td>
					<?php if ( 'select' == $type ):
						$multiple = isset( $specifications[$name]['multiple'] ) ? $specifications[$name]['multiple'] : '';
					?>
						<label>
							<input type="checkbox" name="multiple"<?php checked( 'on', $multiple ); ?>>
							<?php esc_html_e( 'Multiple', 'ova-brw' ); ?>
						</label>
					<?php endif; ?>
					<label>
						<input type="checkbox" name="enable"<?php checked( 'on', $enable ); ?>>
						<?php esc_html_e( 'Enable', 'ova-brw' ); ?>
					</label>
					<label>
						<input type="checkbox" name="show_label"<?php checked( 'on', $show_label ); ?>>
						<?php esc_html_e( 'Show label', 'ova-brw' ); ?>
					</label>
					<label>
						<input type="checkbox" name="show_in_card" <?php checked( 'on', $show_in_card ); ?>>
						<?php esc_html_e( 'Show in Card template', 'ova-brw' ); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	<button type="submit" class="button button-primary"><?php esc_html_e( 'Save', 'ova-brw' ); ?></button>
</form>