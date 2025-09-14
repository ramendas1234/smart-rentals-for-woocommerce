<?php defined( 'ABSPATH' ) || exit;

// Get package IDs
$package_ids = $this->get_meta_value( 'petime_id', [] );

?>

<div class="rental_item ovabrw-package">
    <label for="ovabrw-package">
        <?php esc_html_e( 'Package', 'ova-brw' ); ?>
    </label>
    <span class="ovabrw-package-span">
        <select name="ovabrw_package_id[ovabrw-item-key]" id="ovabrw-package-id" class="ovabrw-input-required ovabrw-package-id">
            <option value="">
                <?php esc_html_e( 'Select Package', 'ova-brw' ); ?>
            </option>
            <?php if ( ovabrw_array_exists( $package_ids ) ):
                $package_labels = $this->get_meta_value( 'petime_label', [] );

                foreach ( $package_ids as $k => $package_id ):
                    $label = ovabrw_get_meta_data( $k, $package_labels );
            ?>
                <option value="<?php echo esc_attr( $package_id ); ?>">
                    <?php echo esc_html( $label ); ?>
                </option>
            <?php endforeach;
            endif; ?>
        </select>
    </span>
</div>