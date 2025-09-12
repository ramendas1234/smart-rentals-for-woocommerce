<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product ids
$product_ids = OVABRW()->options->get_rental_product_ids();

?>

<div class="ovabrw-booking-item">
    <div class="item" meta-key="">
        <div class="sub-item">
            <h3 class="title"><?php esc_html_e( 'Product', 'ova-brw' ) ?></h3>
            <div class="rental_item">
                <select
                    class="ovabrw-input-required ovabrw-product-ids"
                    name="<?php ovabrw_meta_key( 'product_ids[]', true ); ?>"
                    data-placeholder="<?php esc_attr_e( 'Select a product...', 'ova-brw' ); ?>">
                    <option value="">
                        <?php esc_html_e( 'Select a product...', 'ova-brw' ); ?>
                    </option>
                    <?php foreach ( $product_ids as $product_id ): ?>
                        <option value="<?php echo esc_attr( $product_id ); ?>">
                            <?php echo esc_html( get_the_title( $product_id ) ); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="ovabrw-rental-type-loading">
                    <span class="dashicons-before dashicons-update-alt"></span>
                </div>
            </div>
        </div>
    </div>
    <span class="ovabrw-remove-item dashicons dashicons-no-alt"></span>
</div>