<?php defined( 'ABSPATH' ) || exit;

// Manage store
$manage_store = $this->product->get_manage_store();

if ( 'store' === $manage_store || 'appointment' == $this->get_type() ):
    if ( $this->product->show_quantity() ):
        // Get quantity
        $quantity = $this->product->get_number_quantity();
?>
    <div class="rental_item ovabrw-quantity">
        <label for="ovabrw-quantity">
            <?php esc_html_e( 'Quantity', 'ova-brw' ); ?>
        </label>
        <?php ovabrw_text_input([
            'type'      => 'number',
            'id'        => 'ovabrw-quantity',
            'class'     => 'ovabrw-quantity',
            'name'      => 'ovabrw_quantity',
            'key'       => 'ovabrw-item-key',
            'value'     => 1,
            'required'  => true,
            'attrs'     => [
                'min'   => 1,
                'max'   => $quantity
            ]
        ]); ?>
    </div>
    <?php endif; ?>
<?php else:
    $vehicle_ids = $this->get_meta_value( 'id_vehicles', [] );
?>
    <div class="rental_item ovabrw-id-vehicle">
        <label for="ovabrw-vehicle-id">
            <?php esc_html_e( 'Vehicle ID', 'ova-brw' ); ?>
        </label>
        <span class="ovabrw-id-vehicle-span">
            <select name="ovabrw_vehicle_id[ovabrw-item-key]" id="ovabrw-vehicle-id" class="ovabrw-input-required select_ovabrw_id_vehicle">
            <?php if ( ovabrw_array_exists( $vehicle_ids ) ):
                foreach ( $vehicle_ids as $vehicle_id ): ?>
                    <option value="<?php echo esc_attr( $vehicle_id ); ?>">
                        <?php echo esc_html( $vehicle_id ); ?>
                    </option>
                <?php endforeach;
                else: ?>
                <option value="">
                    <?php esc_html_e( 'No vehicle', 'ova-brw' ); ?>
                </option>
            <?php endif; ?>
            </select>
        </span>
    </div>
<?php endif;