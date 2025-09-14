<?php defined( 'ABSPATH' ) || exit();

// Get service labels
$serv_labels = $this->get_meta_value( 'label_service' );

if ( ovabrw_array_exists( $serv_labels ) ):
    $serv_required  = $this->get_meta_value( 'service_required' );
    $serv_ids       = $this->get_meta_value( 'service_id' );
    $serv_names     = $this->get_meta_value( 'service_name' );
    $ser_qtys       = $this->get_meta_value( 'service_qty' );
?>
    <div class="rental_item">
        <label for="ovabrw-services">
            <?php esc_html_e( 'Services', 'ova-brw' ); ?>
        </label>
        <div class="ovabrw-services">
            <?php foreach ( $serv_labels as $k => $label ):
                $required = ovabrw_get_meta_data( $k, $serv_required );
                if ( 'yes' != $required ) $required = '';

                // Option ID
                $opt_ids = ovabrw_get_meta_data( $k, $serv_ids );

                // Option name
                $opt_names = ovabrw_get_meta_data( $k, $serv_names );

                // Option quantity
                $opt_qtys = ovabrw_get_meta_data( $k, $ser_qtys );

                // Quantities
                $quantities = [];
                
                if ( ovabrw_array_exists( $opt_ids ) ):
            ?>
                <div class="item">
                    <div class="ovabrw-select">
                        <select name="<?php echo esc_attr('ovabrw_service[ovabrw-item-key][]'); ?>">
                            <option value="">
                                <?php echo sprintf( esc_html__( 'Select %s', 'ova-brw' ), $label ); ?>
                            </option>
                            <?php foreach ( $opt_ids as $i => $opt_id ):
                                $opt_name   = ovabrw_get_meta_data( $i, $opt_names );
                                $opt_qty    = (int)ovabrw_get_meta_data( $i, $opt_qtys );

                                if ( $opt_qty > 1 ) $quantities[$opt_id] = $opt_qty;
                            ?>
                                <option value="<?php echo esc_attr( $opt_id ); ?>">
                                    <?php echo esc_html( $opt_name ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ( ovabrw_array_exists( $quantities ) ):
                            foreach ( $quantities as $opt_id => $opt_qty ):
                        ?>
                            <div class="select-item-qty" data-option="<?php echo esc_attr( $opt_id ); ?>">
                                <span class="select-qty">1</span>
                                <input
                                    type="text"
                                    class="select-input-qty"
                                    name="<?php echo esc_attr('ovabrw_service_qty[ovabrw-item-key]['.esc_attr( $opt_id ).']'); ?>"
                                    value="1"
                                    min="1"
                                    max="<?php echo esc_attr( $opt_qty ); ?>"
                                />
                                <div class="ovabrw-select-icon">
                                    <i class="brwicon2-up-arrow" aria-hidden="true"></i>
                                    <i class="brwicon2-down-arrow" aria-hidden="true"></i>
                                </div>
                            </div>
                        <?php endforeach;
                        endif; ?>
                    <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>