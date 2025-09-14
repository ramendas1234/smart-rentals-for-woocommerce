<?php if ( !defined( 'ABSPATH' ) ) exit();

// Media
wp_enqueue_media();

// Get specification option
$specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

// Data
$post_data = [];
if ( isset( $_POST ) && $_POST ) {
	$post_data = ovabrw_replace( '\\', '', $_POST );
}

// Action
$action = ovabrw_get_meta_data( 'ovabrw-specificaiton-action', $post_data );

if ( 'new' === $action ) {
	$this->add( $post_data );
} elseif ( 'edit' === $action ) {
	$this->edit( $post_data );
} elseif ( 'delete' === $action ) {
	$this->delete( $post_data );
} elseif ( 'enable' === $action ) {
	$this->enable( $post_data );
} elseif ( 'disable' === $action ) {
	$this->disable( $post_data );
}

?>

<div class="wrap ovabrw-specifications">
	<form action="" method="post" id="ovabrw-specification-form">
		<div class="heading">
			<h1 class="title"><?php esc_html_e( 'Specifications', 'ova-brw' ); ?></h1>
			<button type="button" class="button" name="ovabrw-specificaiton-action" value="add">
				<?php esc_html_e( 'Add new', 'ova-brw' ); ?>
			</button>
			<button type="submit" class="button" name="ovabrw-specificaiton-action" value="delete">
				<?php esc_html_e( 'Delete', 'ova-brw' ); ?>
			</button>
			<button type="submit" class="button" name="ovabrw-specificaiton-action" value="enable">
				<?php esc_html_e( 'Enable', 'ova-brw' ); ?>
			</button>
			<button type="submit" class="button" name="ovabrw-specificaiton-action" value="disable">
				<?php esc_html_e( 'Disable', 'ova-brw' ); ?>
			</button>
			<div class="ovabrw-loading">
				<span class="dashicons dashicons-update-alt"></span>
			</div>
		</div>
		<div class="content">
			<?php if ( ovabrw_array_exists( $specifications ) ): ?>
			<table class="widefat fixed">
				<thead>
					<tr>
						<td class="check-column"><input type="checkbox"></td>
						<th class="type"><?php esc_html_e( 'Type', 'ova-brw' ); ?></th>
						<th class="name"><?php esc_html_e( 'Name', 'ova-brw' ); ?></th>
						<th class="label"><?php esc_html_e( 'Label', 'ova-brw' ); ?></th>
						<th class="icon-font"><?php esc_html_e( 'Icon font', 'ova-brw' ); ?></th>
						<th class="enable"><?php esc_html_e( 'Enable', 'ova-brw' ); ?></th>
						<th class="actions"><?php esc_html_e( 'Actions', 'ova-brw' ); ?></th>
					</tr>
				</thead>
				<tbody class="specification-sortable">
				<?php foreach ( $specifications as $item_name => $item_data ): ?>	
					<?php if ( $item_data['enable'] ): ?>
						<tr>
					<?php else: ?>
						<tr class="disabled">
					<?php endif; ?>
						<th class="check-column">
							<?php ovabrw_wp_text_input([
								'type' 	=> 'checkbox',
								'name' 	=> 'fields[]',
								'value' => $item_name
							]); ?>
						</th>
						<td>
							<?php echo esc_html( $item_data['type'] ); ?>
							<?php ovabrw_wp_text_input([
								'type' 	=> 'hidden',
								'name' 	=> 'type',
								'value' => $item_data['type']
							]); ?>
						</td>
						<td><?php echo esc_html( $item_name ); ?></td>
						<td><?php echo esc_html( $item_data['label'] ); ?></td>
						<td><?php echo esc_html( $item_data['icon-font'] ); ?></td>
						<td class="enable">
							<?php if ( $item_data['enable'] ): ?>
								<span class="dashicons dashicons-yes tips" title="<?php esc_attr_e( 'Yes', 'ova-brw' ); ?>"></span>
							<?php else: ?>
								<span class="dashicons dashicons-yes tips" title="<?php esc_attr_e( 'Yes', 'ova-brw' ); ?>" style="display: none;"></span>
							<?php endif; ?>
						</td>
						<td class="actions">
							<span class="dashicons dashicons-edit" title="<?php esc_attr_e( 'Edit', 'ova-brw' ); ?>"></span>
							<span>|</span>
							<span class="dashicons dashicons-trash" title="<?php esc_attr_e( 'Delete', 'ova-brw' ); ?>"></span>
							<span>|</span>
							<?php if ( $item_data['enable'] ): ?>
								<span class="dashicons dashicons-visibility active" title="<?php esc_attr_e( 'Disable', 'ova-brw' ); ?>"></span>
								<span class="dashicons dashicons-hidden" title="<?php esc_attr_e( 'Enable', 'ova-brw' ); ?>"></span>
							<?php else: ?>
								<span class="dashicons dashicons-visibility" title="<?php esc_attr_e( 'Disable', 'ova-brw' ); ?>"></span>
								<span class="dashicons dashicons-hidden active" title="<?php esc_attr_e( 'Enable', 'ova-brw' ); ?>"></span>
							<?php endif; ?>
							
							<div class="ovabrw-loading">
								<span class="dashicons dashicons-update-alt"></span>
							</div>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
				<tfoot>
					<tr>
						<td class="check-column"><input type="checkbox"></td>
						<th class="type"><?php esc_html_e( 'Type', 'ova-brw' ); ?></th>
						<th class="name"><?php esc_html_e( 'Name', 'ova-brw' ); ?></th>
						<th class="label"><?php esc_html_e( 'Label', 'ova-brw' ); ?></th>
						<th class="icon-font"><?php esc_html_e( 'Icon font', 'ova-brw' ); ?></th>
						<th class="enable"><?php esc_html_e( 'Enable', 'ova-brw' ); ?></th>
						<th class="actions"><?php esc_html_e( 'Actions', 'ova-brw' ); ?></th>
					</tr>
				</tfoot>
			</table>
			<?php endif; ?>
		</div>
	</form>
	<div class="ovabrw-popup-specification">
		<div class="ovabrw-popup-specification-field"></div>
	</div>
	<input
		type="hidden"
		name="ovabrw-datepicker-options"
		value="<?php echo esc_attr( wp_json_encode( ovabrw_admin_datepicker_options() ) ); ?>"
	/>
</div>