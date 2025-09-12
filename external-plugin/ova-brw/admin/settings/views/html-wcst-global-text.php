<?php if ( ! defined( 'ABSPATH' ) ) exit(); ?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Text', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-text">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_text_font_size', true ); ?>">
                        <?php esc_html_e( 'Font size', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <?php ovabrw_wp_text_input([
                        'id'    => $this->get_name( 'glb_text_font_size' ),
                        'class' => $this->get_name( 'glb_text_font_size' ),
                        'name'  => $this->get_name( 'glb_text_font_size' ),
                        'value' => ovabrw_get_option( 'glb_text_font_size', '14px' ),
                        'attrs' => [
                            'placeholder'   => '14px',
                            'autocomplete'  => 'off'
                        ]
                    ]); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_text_font_weight', true ); ?>">
                        <?php esc_html_e( 'Font weight', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <?php ovabrw_wp_text_input([
                        'type'  => 'number',
                        'id'    => $this->get_name( 'glb_text_font_weight' ),
                        'class' => $this->get_name( 'glb_text_font_weight' ),
                        'name'  => $this->get_name( 'glb_text_font_weight' ),
                        'value' => ovabrw_get_option( 'glb_text_font_weight', '400' ),
                        'attrs' => [
                            'placeholder'   => '400',
                            'autocomplete'  => 'off',
                            'min'           => '100',
                        ]
                    ]); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_text_line_height', true ); ?>">
                        <?php esc_html_e( 'Line height', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <?php ovabrw_wp_text_input([
                        'id'    => $this->get_name( 'glb_text_line_height' ),
                        'class' => $this->get_name( 'glb_text_line_height' ),
                        'name'  => $this->get_name( 'glb_text_line_height' ),
                        'value' => ovabrw_get_option( 'glb_text_line_height', '22px' ),
                        'attrs' => [
                            'placeholder'   => '22px',
                            'autocomplete'  => 'off'
                        ]
                    ]); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_text_color', true ); ?>">
                        <?php esc_html_e( 'Color', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-color">
                    <span class="colorpickpreview" style="background-color: <?php echo esc_attr( ovabrw_get_option( 'glb_text_color', '#555555' ) ); ?>"></span>
                    <?php ovabrw_wp_text_input([
                        'id'    => $this->get_name( 'glb_text_color' ),
                        'class' => 'colorpick',
                        'name'  => $this->get_name( 'glb_text_color' ),
                        'value' => ovabrw_get_option( 'glb_text_color', '#555555' ),
                        'attrs' => [
                            'dir'           => 'ltr',
                            'placeholder'   => '#555555',
                            'autocomplete'  => 'off'
                        ]
                    ]); ?>
                    <div id="colorPickerDiv_ovabrw_glb_text_color" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>