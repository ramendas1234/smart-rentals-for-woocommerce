<?php defined( 'ABSPATH' ) || exit();

// Currency
$currency = ovabrw_get_meta_data( 'currency', $args );

// Get resource IDs
$res_ids = $this->get_meta_value( 'resource_id' );

if ( ovabrw_array_exists( $res_ids ) ):
    $res_names  = $this->get_meta_value( 'resource_name' );
    $res_prices = $this->get_meta_value( 'resource_price' );
    $res_types  = $this->get_meta_value( 'resource_duration_type' );
    $res_qtys   = $this->get_meta_value( 'resource_quantity' );
?>
    <div class="rental_item">
        <label>
            <?php esc_html_e( 'Resources', 'ova-brw' ); ?>
        </label>
        <div class="ovabrw-resources">
            <?php foreach ( $res_ids as $k => $id ):
                $name   = ovabrw_get_meta_data( $k, $res_names );
                $price  = ovabrw_get_meta_data( $k, $res_prices );
                $type   = ovabrw_get_meta_data( $k, $res_types );
                $qty    = (int)ovabrw_get_meta_data( $k, $res_qtys );

                if ( 'days' == $type ) $type = esc_html__( '/day', 'ova-brw' );
                if ( 'hours' == $type ) $type = esc_html__( '/hour', 'ova-brw' );
                if ( 'total' == $type ) $type = esc_html__( '/order', 'ova-brw' );

                if ( $id ): ?>
                    <div class="item">
                        <div class="res-left">
                            <label class="ovabrw-label-field">
                                <?php echo esc_html( $name ); ?>
                                <?php ovabrw_text_input([
                                    'type'  => 'checkbox',
                                    'name'  => 'ovabrw_resource_checkboxs[ovabrw-item-key][]',
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
                                    <?php echo ovabrw_wc_price( $price, [
                                        'currency' => $currency
                                    ]); ?>
                                </span>
                                <span class="res-type">
                                    <?php echo esc_html( $type ); ?>
                                </span>
                            </div>
                            <?php if ( $qty > 1 ): ?>
                                <div class="checkbox-item-qty" data-option="<?php echo esc_attr( $id ); ?>">
                                    <span class="checkbox-qty">1</span>
                                    <?php ovabrw_text_input([
                                        'type'          => 'text',
                                        'id'            => '',
                                        'class'         => 'checkbox-input-qty',
                                        'name'          => 'ovabrw_resource_quantity[ovabrw-item-key]['.esc_attr( $id ).']',
                                        'value'         => 1,
                                        'attrs'         => [
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
<?php endif; ?>