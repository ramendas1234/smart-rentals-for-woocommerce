<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get all card templates
$card_templates = ovabrw_get_card_templates();

if ( !ovabrw_array_exists( $card_templates ) ) $card_templates = []; ?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Card', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-card">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_card_template', true ); ?>">
                        <?php esc_html_e( 'Default Card Template', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-select">
                    <?php ovabrw_wp_select_input([
                        'id'        => $this->get_name( 'glb_card_template' ),
                        'class'     => 'ovabrw_select2',
                        'name'      => $this->get_name( 'glb_card_template' ),
                        'options'   => $card_templates,
                        'value'     => ovabrw_get_option( 'glb_card_template' ),
                        'attrs'     => [
                            'data-placeholder' => esc_html__( 'Select Card Template', 'ova-brw' )
                        ]
                    ]); ?>
                    <br>
                    <span>
                        <?php  esc_html_e( 'Product Template in Product Listing Page', 'ova-brw' ); ?>
                    </span>
                </td>
            </tr>
        </tbody>
    </table>
    <div class="ovabrw-wcst-tabs">
        <div class="ovabrw-wcst-tab-nav">
        <?php $flag = 1;

            foreach ( $card_templates as $card => $label ):
                $nav_classs = 'ovabrw-wcst-tab-btn';

                if ( 1 === $flag ) $nav_classs = 'ovabrw-wcst-tab-btn active';
            ?>
                <a href="<?php echo esc_attr( '#'.$card ); ?>" data-id="<?php echo esc_attr( $card ); ?>" class="<?php echo esc_attr( $nav_classs ); ?>">
                    <?php echo esc_html( $label ); ?>
                </a>
        <?php $flag++; endforeach; ?>
        </div>
        <div class="ovabrw-wcst-tab-content">
        <?php $flag = 1;

            foreach ( $card_templates as $card => $label ):
                $table_class = 'form-table';

                if ( 1 === $flag ) $table_class = 'form-table active';
        ?>
            <table class="<?php echo esc_attr( $table_class ); ?>" data-id="<?php echo esc_attr( $card ); ?>">
                <tbody>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_featured', true ); ?>">
                                <?php esc_html_e( 'Show highlight', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_featured' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_featured' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_featured', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_feature_featured', true ); ?>">
                                <?php esc_html_e( 'Show special', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_feature_featured' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_feature_featured' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_feature_featured', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_thumbnail_type', true ); ?>">
                                <?php esc_html_e( 'Thumbnail type', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_thumbnail_type' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_thumbnail_type' ),
                                'options'   => [
                                    'slider'    => esc_html__( 'Slider', 'ova-brw' ),
                                    'image'     => esc_html__( 'Image', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_thumbnail_type', 'slider' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_thumbnail_size', true ); ?>">
                                <?php esc_html_e( 'Thumbnail size', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_thumbnail_size' ),
                                'class'     => 'ovabrw-glb-card-thumbnail-size',
                                'name'      => $this->get_name( 'glb_'.$card.'_thumbnail_size' ),
                                'options'   => [
                                    'woocommerce_thumbnail'         => esc_html__( 'Thumbnail', 'ova-brw' ),
                                    'woocommerce_single'            => esc_html__( 'Single', 'ova-brw' ),
                                    'woocommerce_gallery_thumbnail' => esc_html__( 'Gallery Thumbnail', 'ova-brw' ),
                                    'full'                          => esc_html__( 'Full', 'ova-brw' ),
                                    'custom_height'                 => esc_html__( 'Custom Height', 'ova-brw' ),
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_thumbnail_size', 'woocommerce_thumbnail' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top" class="ovabrw-glb-thumbnail-height">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_thumbnail_height', true ); ?>">
                                <?php esc_html_e( 'Thumbnail height', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_text_input([
                                'id'    => $this->get_name( 'glb_'.$card.'_thumbnail_height' ),
                                'name'  => $this->get_name( 'glb_'.$card.'_thumbnail_height' ),
                                'value' => ovabrw_get_option( 'glb_'.$card.'_thumbnail_height', '300px' ),
                                'attrs' => [
                                    'placeholder'   => '300px or 100%',
                                    'autocomplete'  => 'off',
                                ]
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_display_thumbnail', true ); ?>">
                                <?php esc_html_e( 'Display thumbnail', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_display_thumbnail' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_display_thumbnail' ),
                                'options'   => [
                                    'fill'          => esc_html__( 'Fill', 'ova-brw' ),
                                    'contain'       => esc_html__( 'Contain', 'ova-brw' ),
                                    'cover'         => esc_html__( 'Cover', 'ova-brw' ),
                                    'none'          => esc_html__( 'None', 'ova-brw' ),
                                    'scale-down'    => esc_html__( 'Scale Down', 'ova-brw' ),
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_display_thumbnail', 'cover' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_price', true ); ?>">
                                <?php esc_html_e( 'Show price', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_price' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_price' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_price', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_specifications', true ); ?>">
                                <?php esc_html_e( 'Show specifications', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_specifications' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_specifications' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_specifications', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_features', true ); ?>">
                                <?php esc_html_e( 'Show features', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_features' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_features' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_features', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_custom_taxonomy', true ); ?>">
                                <?php esc_html_e( 'Show Custom Taxonomy', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_custom_taxonomy' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_custom_taxonomy' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_custom_taxonomy', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_attribute', true ); ?>">
                                <?php esc_html_e( 'Show attribute', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_attribute' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_attribute' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_attribute', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_short_description', true ); ?>">
                                <?php esc_html_e( 'Show short description', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_short_description' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_short_description' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_short_description', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_review', true ); ?>">
                                <?php esc_html_e( 'Show review', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_review' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_review' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_review', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row" class="titledesc">
                            <label for="<?php $this->get_name( 'glb_'.$card.'_button', true ); ?>">
                                <?php esc_html_e( 'Show button', 'ova-brw' ); ?>
                            </label>
                        </th>
                        <td class="forminp forminp-select">
                            <?php ovabrw_wp_select_input([
                                'id'        => $this->get_name( 'glb_'.$card.'_button' ),
                                'name'      => $this->get_name( 'glb_'.$card.'_button' ),
                                'options'   => [
                                    'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                                    'no'    => esc_html__( 'No', 'ova-brw' )
                                ],
                                'value'     => ovabrw_get_option( 'glb_'.$card.'_button', 'yes' )
                            ]); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        <?php $flag++; endforeach; ?>
        </div>
    </div>
</div>