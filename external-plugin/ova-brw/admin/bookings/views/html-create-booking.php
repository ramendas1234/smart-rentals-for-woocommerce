<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product ids
$product_ids = OVABRW()->options->get_rental_product_ids();

// Available gateways
$available_gateways = WC()->payment_gateways()->get_available_payment_gateways();

// Multi currency
$currencies = [];

// WPML multi currency
if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
    $setting    = WOOMULTI_CURRENCY_F_Data::get_ins();
    $currencies = $setting->get_list_currencies();
}

// WPML multi currency
if ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
    global $woocommerce_wpml;

    if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
        if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

        $currencies     = $woocommerce_wpml->get_setting( 'currency_options' );
        $current_lang   = apply_filters( 'wpml_current_language', NULL );

        if ( $current_lang != 'all' && ovabrw_array_exists( $currencies ) ) {
            foreach ( $currencies as $k => $data ) {
                if ( isset( $currencies[$k]['languages'][$current_lang] ) && $currencies[$k]['languages'][$current_lang] ) {
                    continue;
                } else {
                    unset( $currencies[$k] );
                }
            }
        }
    }
}

// Default billing country
$default_country = apply_filters( OVABRW_PREFIX.'create_booking_default_country', 'US' );

// Default billing state
$default_state = apply_filters( OVABRW_PREFIX.'create_booking_default_state', 'CA' );

?>
<div class="wrap">
    <form id="ovabrw-create-new-booking" class="ovabrw-create-new-booking" method="POST" action="<?php echo admin_url('admin.php?page=ovabrw-create-booking'); ?>" enctype="multipart/form-data" autocomplete="off">
        <h2>
            <?php esc_html_e( 'Add new booking', 'ova-brw' ); ?>
        </h2>
        <div class="ovabrw-wrap">
            <div class="ovabrw-row ovabrw-grid-columns">
                <div class="ovabrw-field ovabrw-order-status">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'Status', 'ova-brw' ); ?>
                    </label>
                    <select
                        id="order_status"
                        class="ovabrw-select2 ovabrw-input-required"
                        name="order_status"
                        data-placeholder="<?php esc_attr_e( 'Select order status...', 'ova-brw' ); ?>">
                        <option value="completed" selected>
                            <?php esc_html_e( 'Completed', 'ova-brw' ); ?>
                        </option>
                        <option value="processing">
                            <?php esc_html_e( 'Processing', 'ova-brw' ); ?>
                        </option>
                        <option value="pending">
                            <?php esc_html_e( 'Pending payment', 'ova-brw' ); ?>
                        </option>
                        <option value="on-hold">
                            <?php esc_html_e( 'On hold', 'ova-brw' ); ?>
                        </option>
                        <option value="cancelled">
                            <?php esc_html_e( 'Cancelled', 'ova-brw' ); ?>
                        </option>
                        <option value="refunded">
                            <?php esc_html_e( 'Refunded', 'ova-brw' ); ?>
                        </option>
                        <option value="failed">
                            <?php esc_html_e( 'Failed', 'ova-brw' ); ?>
                        </option>
                    </select>
                </div>
                <?php if ( ovabrw_array_exists( $available_gateways ) ): // Payment method
                    $default_method = apply_filters( OVABRW_PREFIX.'add_new_booking_default_payment_method', '', $available_gateways ); ?>
                    <div class="ovabrw-field ovabrw-payment-method">
                        <label for="ovabrw-payment-method">
                            <?php esc_html_e( 'Payment method (optional)', 'ova-tour-booking' ); ?>
                        </label>
                        <select
                            id="ovabrw-payment-method"
                            class="ovabrw-select2"
                            name="payment_method"
                            data-placeholder="<?php esc_attr_e( 'Select payment method...', 'ova-brw' ); ?>">
                            <option value="">
                                <?php esc_html_e( 'Select payment method...', 'ova-brw' ); ?>
                            </option>
                            <?php foreach ( $available_gateways as $gateway ): ?>
                                <option value="<?php echo esc_attr( $gateway->id ); ?>"<?php ovabrw_selected( $gateway->id, $default_method ); ?>>
                                    <?php echo esc_html( $gateway->get_title() ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="payment_method_title">
                    </div>
                <?php endif; ?>
                <?php if ( ovabrw_array_exists( $currencies ) ): // Multi currency
                    $current_currency = ovabrw_get_meta_data( 'currency', $_GET );
                    if ( !$current_currency ) $current_currency = array_key_first( $currencies ); 
                ?>
                    <div class="ovabrw-field ovabrw-currency">
                        <label class="ovabrw-required">
                            <?php esc_html_e( 'Currency', 'ova-brw' ); ?>
                        </label>
                        <select
                            id="ovabrw-currency"
                            name="currency">
                            <?php foreach ( $currencies as $currency => $rate ): ?>
                                <option value="<?php echo esc_attr( $currency ); ?>"<?php ovabrw_selected( $currency, $current_currency ); ?>>
                                    <?php esc_html_e( $currency ); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php ovabrw_wp_text_input([
                            'type'  => 'hidden',
                            'name'  => 'ovabrw-admin-url',
                            'value' => esc_url( get_admin_url() )
                        ]); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="ovabrw-grid-columns ovabrw-billing">
                <?php do_action( OVABRW_PREFIX.'before_create_new_order_billing' ); ?>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'First Name', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'class'         => 'ovabrw-input-required',
                        'name'          => 'billing_first_name',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'Last Name', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'class'         => 'ovabrw-input-required',
                        'name'          => 'billing_last_name',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label>
                        <?php esc_html_e( 'Company', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'name'          => 'billing_company',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label for="billing-country" class="ovabrw-required">
                        <?php esc_html_e( 'Country / Region', 'ova-brw' ); ?>
                    </label>
                    <select
                        id="billing-country"
                        class="ovabrw-select2 ovabrw-input-required ovabrw_country"
                        name="billing_country"
                        data-placeholder="<?php esc_attr_e( 'Select a country / region...', 'ova-brw' ); ?>">
                        <option value="">
                            <?php esc_html_e( 'Select a country / region...', 'ova-brw' ); ?>
                        </option>
                        <?php foreach ( WC()->countries->get_allowed_countries() as $ckey => $cvalue ): ?>
                            <option value="<?php echo esc_attr( $ckey ); ?>"<?php ovabrw_selected( $ckey, $default_country ); ?>>
                                <?php echo esc_html( $cvalue ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'Address 1', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'class'         => 'ovabrw-input-required',
                        'name'          => 'billing_address_1',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label>
                        <?php esc_html_e( 'Address 2', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'name'          => 'billing_address_2',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'City', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'class'         => 'ovabrw-input-required',
                        'name'          => 'billing_city',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label for="billing-state" class="ovabrw-required">
                        <?php esc_html_e( 'State', 'ova-brw' ); ?>
                    </label>
                    <select
                        id="billing-state"
                        class="ovabrw-select2 ovabrw-input-required"
                        name="billing_state"
                        data-placeholder="<?php esc_attr_e( 'Select an option...', 'ova-brw' ); ?>">
                        <option value="">
                            <?php esc_html_e( 'Select an option...', 'ova-brw' ); ?>
                        </option>
                        <?php foreach ( WC()->countries->get_states( $default_country ) as $skey => $svalue ): ?>
                            <option value="<?php echo esc_attr( $skey ); ?>"<?php ovabrw_selected( $skey, $default_state ); ?>>
                                <?php echo esc_html( $svalue ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ovabrw-field">
                    <label for="billing-postcode" class="ovabrw-required">
                        <?php esc_html_e( 'ZIP Code', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'type'          => 'text',
                        'id'            => 'billing-postcode',
                        'class'         => 'billing-postcode ovabrw-input-required',
                        'name'          => 'billing_postcode',
                        'placeholder'   => '...',
                        'attrs'         => [
                            'autocomplete' => 'off'
                        ]
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'Email', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'type'          => 'email',
                        'class'         => 'ovabrw-input-required',
                        'name'          => 'billing_email',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'Phone', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'class'         => 'ovabrw-input-required',
                        'type'          => 'tel',
                        'name'          => 'billing_phone',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label for="order-comments">
                        <?php esc_html_e( 'Order notes (optional)', 'ova-brw' ); ?>
                    </label>
                    <textarea name="order_comments" id="order-comments" placeholder="<?php esc_attr_e( 'Notes about your order, e.g. special notes for delivery.', 'ova-brw' ); ?>" rows="2" cols="5"></textarea>
                </div>
                <?php do_action( OVABRW_PREFIX.'after_create_new_order_billing' ); ?>
            </div>
            <div class="ship-to-different-address">
                <label class="ship-to-different-address-label">
                	<?php ovabrw_wp_text_input([
                        'type' 	=> 'checkbox',
                        'id' 	=> 'ship-to-different-address-checkbox',		
                        'class' => 'ship-to-different-address-checkbox input-checkbox',		
                        'name' 	=> 'shipping_enable',
                        'value' => '1'
                    ]); ?>
                    <span>
                        <?php echo esc_html_e( 'Ship to a different address?', 'ova-brw' ); ?>
                    </span>
                </label>
            </div>
            <div class="ovabrw-grid-columns ovabrw-shipping">
                <?php do_action( OVABRW_PREFIX.'before_create_new_order_shipping' ); ?>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'First Name', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'name'          => 'shipping_first_name',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'Last Name', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'name'          => 'shipping_last_name',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label>
                        <?php esc_html_e( 'Company', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'name'          => 'shipping_company',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label for="shipping-country" class="ovabrw-required">
                        <?php esc_html_e( 'Country / Region', 'ova-brw' ); ?>
                    </label>
                    <select
                        id="shipping-country"
                        class="ovabrw-select2 ovabrw_country"
                        name="shipping_country"
                        data-placeholder="<?php esc_attr_e( 'Select a country / region...', 'ova-brw' ); ?>">
                        <option value="">
                            <?php esc_html_e( 'Select a country / region...', 'ova-brw' ); ?>
                        </option>
                        <?php foreach ( WC()->countries->get_allowed_countries() as $ckey => $cvalue ): ?>
                            <option value="<?php echo esc_attr( $ckey ); ?>"<?php ovabrw_selected( $ckey, $default_country ); ?>>
                                <?php echo esc_html( $cvalue ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'Address 1', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'name'          => 'shipping_address_1',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label>
                        <?php esc_html_e( 'Address 2', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'name'          => 'shipping_address_2',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label class="ovabrw-required">
                        <?php esc_html_e( 'City', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'name'          => 'shipping_city',
                        'placeholder'   => '...'
                    ]); ?>
                </div>
                <div class="ovabrw-field">
                    <label for="shipping-state" class="ovabrw-required">
                        <?php esc_html_e( 'State', 'ova-brw' ); ?>
                    </label>
                    <select
                        id="shipping-state"
                        class="ovabrw-select2"
                        name="shipping_state"
                        data-placeholder="<?php esc_attr_e( 'Select an option...', 'ova-brw' ); ?>">
                        <option value="">
                            <?php esc_html_e( 'Select an option...', 'ova-brw' ); ?>
                        </option>
                        <?php foreach ( WC()->countries->get_states( $default_country ) as $skey => $svalue ): ?>
                            <option value="<?php echo esc_attr( $skey ); ?>"<?php ovabrw_selected( $skey, $default_state ); ?>>
                                <?php echo esc_html( $svalue ); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="ovabrw-field">
                    <label for="shipping-postcode" class="ovabrw-required">
                        <?php esc_html_e( 'ZIP Code', 'ova-brw' ); ?>
                    </label>
                    <?php ovabrw_wp_text_input([
                        'type'          => 'text',
                        'id'            => 'shipping-postcode',
                        'class'         => 'shipping-postcode ovabrw-input-required',
                        'name'          => 'shipping_postcode',
                        'placeholder'   => '...',
                        'attrs'         => [
                            'autocomplete' => 'off'
                        ]
                    ]); ?>
                </div>
                <?php do_action( OVABRW_PREFIX.'after_create_new_order_shipping' ); ?>
            </div>
            <div class="wrap_item">
                <div class="ovabrw-booking-item">
                    <div class="item" meta-key="">
                        <div class="sub-item">
                            <div class="rental_item">
                                <h3 class="title">
                                    <?php esc_html_e(  'Product', 'ova-brw' ); ?>
                                </h3>
                                <select
                                    class="ovabrw-select2 ovabrw-input-required ovabrw-product-ids"
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
            </div>
            <button class="button ovabrw-add-new-item" data-add-new="<?php
                ob_start();
                include( OVABRW_PLUGIN_ADMIN . 'bookings/views/html-create-booking-item.php' );
                echo esc_attr( ob_get_clean() );
            ?>">
                <?php esc_html_e( 'Add Item', 'ova-brw' ); ?>
            </button>
            <button type="submit" class="button">
                <?php esc_html_e( 'Create Order', 'ova-brw' ); ?>
            </button>
        </div>
        <?php ovabrw_wp_text_input([
        	'type' 	=> 'hidden',
        	'name'  => ovabrw_meta_key( 'create_new_booking' ),
            'value' => 'new_booking'
        ]); ?>
        <?php ovabrw_wp_text_input([
        	'type' 	=> 'hidden',
        	'name' 	=> 'page',
        	'value' => 'ovabrw-create-booking'
        ]); ?>
    </form>
</div>