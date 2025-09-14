<?php defined( 'ABSPATH' ) || exit(); ?>

<form action="" method="post" id="ovabrw-popup-specification-form" enctype="multipart/form-data" autocomplete="off">
	<input
		type="hidden"
		name="ovabrw-specificaiton-action"
		value="<?php echo esc_attr( $action ); ?>"
	/>
	<button class="popup-specification-close">X</button>
	<table width="100%">
		<tbody>
			<?php add_action( 'ovabrw_before_popup_add_specification', $action, $type, $name ); ?>
			<tr class="type">
				<td class="ovabrw-required label">
					<?php esc_html_e( 'Select type', 'ova-brw' ); ?>
				</td>
				<td>
					<select name="type" id="type" required>
						<?php foreach ( $this->get_types() as $t => $v ): ?>
							<option value="<?php echo esc_attr( $t ); ?>"<?php selected( $t, $type ); ?>>
								<?php echo esc_html( $v ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>
			<tr class="name">
				<td class="ovabrw-required label">
					<?php esc_html_e( 'Name', 'ova-brw' ); ?>
				</td>
				<td>
					<input
						type="text"
						name="name"
						value="<?php echo esc_attr( $name ); ?>"
						autocomplete="off"
						required
					/>
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
					<input
						type="text"
						name="label"
						autocomplete="off"
						required
					/>
				</td>
			</tr>
			<?php if ( in_array( $type, ['radio', 'checkbox', 'select'] ) ): // Radio ?>
				<tr class="radio-options specification-options">
					<td class="ovabrw-required label">
						<?php esc_html_e( 'Options', 'ova-brw' ); ?>
					</td>
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
								<tr>
									<td><input type="text" name="options[]" required></td>
									<td>
										<span class="btn btn-add-new">+</span>
										<span class="btn btn-add-remove">x</span>
										<span class="dashicons dashicons-menu-alt3"></span>
									</td>
								</tr>
							</tbody>
						</table>
					</td>
				</tr>
			<?php endif; // End radio ?>
			<tr class="icon-font">
				<td class="label"><?php esc_html_e( 'Icon font', 'ova-brw' ); ?></td>
				<td>
					<input type="text" name="icon-font" autocomplete="off">
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
					<?php if ( $type === 'date' ): ?>
						<input
							type="text"
							id="<?php echo esc_attr( ovabrw_unique_id( 'specification_date' ) ); ?>"
							class="ovabrw-datepicker"
							name="default"
							autocomplete="off"
						/>
					<?php elseif ( $type === 'link' ): ?>
						<input
							type="url"
							name="default"
							placeholder="https://example.com"
							autocomplete="off"
						/>
					<?php elseif ( $type === 'number' ): ?>
						<input type="number" name="default" autocomplete="off">
					<?php elseif ( $type === 'email' ): ?>
						<input type="email" name="default" autocomplete="off">
					<?php elseif ( $type === 'color' ): ?>
						<input type="color" name="default">
					<?php elseif ( $type === 'file' ): ?>
						<button class="button button-primary specification-add-file">
							<?php esc_html_e( 'Add file', 'ova-brw' ); ?>
						</button>
						<p class="file-default">
							<a href="#" target="_blank"></a>
							<button class="btn">X</button>
						</p>
						<input type="hidden" name="default">
					<?php else: ?>
						<input type="text" name="default" autocomplete="off">
					<?php endif; ?>
				</td>
			</tr>
			<tr class="class">
				<td class="label"><?php esc_html_e( 'Class', 'ova-brw' ); ?></td>
				<td>
					<input type="text" name="class" autocomplete="off">
				</td>
			</tr>
			<?php add_action( 'ovabrw_after_popup_add_specification', $action, $type, $name ); ?>
			<tr class="status">
				<td class="label"></td>
				<td>
					<?php if ( $type === 'select' ): ?>
						<label>
							<input type="checkbox" name="multiple" checked>
							<?php esc_html_e( 'Multiple', 'ova-brw' ); ?>
						</label>
					<?php endif; ?>
					<label>
						<input type="checkbox" name="enable" checked>
						<?php esc_html_e( 'Enable', 'ova-brw' ); ?>
					</label>
					<label>
						<input type="checkbox" name="show_label">
						<?php esc_html_e( 'Show label', 'ova-brw' ); ?>
					</label>
					<label>
						<input type="checkbox" name="show_in_card" checked>
						<?php esc_html_e( 'Show in Card template', 'ova-brw' ); ?>
					</label>
				</td>
			</tr>
		</tbody>
	</table>
	<button type="submit" class="button button-primary"><?php esc_html_e( 'Save', 'ova-brw' ); ?></button>
</form>