<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get product ids
$product_ids = OVABRW()->options->get_rental_product_ids();

// Transportation product
$rental_transportation_ids = OVABRW()->options->get_rental_product_ids( 'transportation' );

// Not rental Transportation
$rental_product_ids = OVABRW()->options->get_rental_product_ids([
	'exclude_type' => [ 'transportation' ]
]);

// Get config upload file
$bytes 		= wp_max_upload_size();
$size 		= size_format( $bytes );
$upload_dir = wp_upload_dir();

?>
<div class="ova-import-locations">
	<form class="import-locations" enctype="multipart/form-data" method="post">
		<h2 class="heading">
            <?php esc_html_e( 'Import locations from a CSV file', 'ova-brw' ); ?>
        </h2>
		<select name="product_id">
			<option value="">
                <?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?>
            </option>
            <?php if ( ovabrw_array_exists( $rental_transportation_ids ) ):
                foreach ( $rental_transportation_ids as $p_id ): ?>
                    <option value="<?php echo esc_attr( $p_id ); ?>">
                        <?php echo get_the_title( $p_id ); ?>
                    </option>
                <?php endforeach;
            endif; ?>
		</select>
		<p class="note">
            <?php esc_html_e( 'Only applicable for Rental: Transportation', 'ova-brw' ); ?>
        </p>
		<div class="import-locations-fields">
			<h4 for="upload">
				<?php esc_html_e( 'Choose a CSV file from your computer:', 'ova-brw' ); ?>
			</h4>
			<?php ovabrw_wp_text_input([
				'type' 		=> 'file',
				'id' 		=> 'upload',
				'name' 		=> 'location_file',
				// 'required' 	=> true,
				'attrs' 	=> [
					'size' 	=> 25
				]
			]); ?>
			<?php ovabrw_wp_text_input([
				'type' 	=> 'hidden',
				'name' 	=> 'action_import',
				'value' => 'import_locations'
			]); ?>
			<?php ovabrw_wp_text_input([
				'type' 	=> 'hidden',
				'name' 	=> 'max_file_size',
				'value' => $bytes
			]); ?>
			<br/>
			<span class="max-size">
				<?php echo sprintf(
					esc_html__( 'Maximum size: %s', 'ova-brw' ),
					esc_html( $size )
				); ?>
			</span>
			<a href="<?php echo esc_url( OVABRW_PLUGIN_URI . 'admin/imports/demo.csv' ); ?>" class="demo" download>
				<?php esc_html_e( 'Demo file.csv', 'ova-brw' ); ?>
			</a>
		</div>
		<div class="import-locations-submit">
			<button type="submit" class="button">
				<?php esc_html_e( 'Import', 'ova-brw' ); ?>
			</button>
		</div>
	</form>
	<form class="import-setup-locations" enctype="multipart/form-data" method="post">
		<select name="product_id">
			<option value="">
                <?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?>
            </option>
            <?php if ( ovabrw_array_exists( $rental_product_ids ) ):
                foreach ( $rental_product_ids as $p_id ): ?>
                    <option value="<?php echo esc_attr( $p_id ); ?>">
                        <?php echo get_the_title( $p_id ); ?>
                    </option>
                <?php endforeach;
            endif; ?>
		</select>
		<p class="note">
            <?php esc_html_e( 'Only applicable for Rental: Day, Hour, Mixed, Period of Time', 'ova-brw' ); ?>
        </p>
		<div class="import-locations-fields">
			<h4 for="upload">
				<?php esc_html_e( 'Choose a CSV file from your computer:', 'ova-brw' ); ?>
			</h4>
			<?php ovabrw_wp_text_input([
				'type' 		=> 'file',
				'id' 		=> 'upload',
				'name' 		=> 'location_file',
				// 'required' 	=> true,
				'attrs' 	=> [
					'size' => 25
				]
			]); ?>
			<?php ovabrw_wp_text_input([
				'type' 	=> 'hidden',
				'name' 	=> 'action_import',
				'value' => 'import_locations'
			]); ?>
			<?php ovabrw_wp_text_input([
				'type' 	=> 'hidden',
				'name' 	=> 'max_file_size',
				'value' => $bytes
			]); ?>
			<br/>
			<span class="max-size">
				<?php echo sprintf(
					esc_html__( 'Maximum size: %s', 'ova-brw' ),
					esc_html( $size )
				); ?>
			</span>
			<a href="<?php echo esc_url( OVABRW_PLUGIN_URI . 'admin/imports/demo2.csv' ); ?>" class="demo" download>
				<?php esc_html_e( 'Demo file.csv', 'ova-brw' ); ?>
			</a>
		</div>
		<div class="import-locations-submit">
			<button type="submit" class="button">
				<?php esc_html_e( 'Import', 'ova-brw' ); ?>
			</button>
		</div>
	</form>
	<form class="remove-locations" enctype="multipart/form-data" method="post">
		<h2 class="heading">
            <?php esc_html_e( 'Remove Locations', 'ova-brw' ); ?>
        </h2>
		<select name="product_id">
			<option value="">
                <?php esc_html_e( '-- Choose Product --', 'ova-brw' ); ?>
            </option>
            <?php if ( ovabrw_array_exists( $product_ids ) ):
                foreach ( $product_ids as $p_id ): ?>
                    <option value="<?php echo esc_attr( $p_id ); ?>">
                        <?php echo get_the_title( $p_id ); ?>
                    </option>
                <?php endforeach;
            endif; ?>
		</select>
		<?php ovabrw_wp_text_input([
			'type' 	=> 'hidden',
			'name' 	=> 'action_import',
			'value' => 'remove_locations'
		]); ?>
		<div class="remove-locations-submit">
			<button type="submit" class="button">
				<?php esc_html_e( 'Remove', 'ova-brw' ); ?>
			</button>
		</div>
	</form>
</div>