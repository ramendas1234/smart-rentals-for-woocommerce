<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Heading', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-headding">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_heading_font_size', true ); ?>">
                        <?php esc_html_e( 'Font size', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <?php ovabrw_wp_text_input([
                        'id'    => $this->get_name( 'glb_heading_font_size' ),
                        'class' => $this->get_name( 'glb_heading_font_size' ),
                        'name'  => $this->get_name( 'glb_heading_font_size' ),
                        'value' => ovabrw_get_option( 'glb_heading_font_size', '24px' ),
                        'attrs' => [
                            'placeholder' => '24px'
                        ]
                    ]); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_heading_font_weight', true ); ?>">
                        <?php esc_html_e( 'Font weight', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <?php ovabrw_wp_text_input([
                        'type'  => 'number',
                        'id'    => $this->get_name( 'glb_heading_font_weight' ),
                        'class' => $this->get_name( 'glb_heading_font_weight' ),
                        'name'  => $this->get_name( 'glb_heading_font_weight' ),
                        'value' => ovabrw_get_option( 'glb_heading_font_weight', '600' ),
                        'attrs' => [
                            'placeholder'   => '600',
                            'min'           => '100',
                        ]
                    ]); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_heading_line_height', true ); ?>">
                        <?php esc_html_e( 'Line height', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-text">
                    <?php ovabrw_wp_text_input([
                        'id'    => $this->get_name( 'glb_heading_line_height' ),
                        'class' => $this->get_name( 'glb_heading_line_height' ),
                        'name'  => $this->get_name( 'glb_heading_line_height' ),
                        'value' => ovabrw_get_option( 'glb_heading_line_height', '36px' ),
                        'attrs' => [
                            'placeholder'   => '36px',
                            'autocomplete'  => 'off'
                        ]
                    ]); ?>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_heading_color', true ); ?>">
                        <?php esc_html_e( 'Color', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-color">
                    <span class="colorpickpreview" style="background-color: <?php echo esc_attr( ovabrw_get_option( 'glb_heading_color', '#222222' ) ); ?>"></span>
                    <?php ovabrw_wp_text_input([
                        'id'    => $this->get_name( 'glb_heading_color' ),
                        'class' => 'colorpick',
                        'name'  => $this->get_name( 'glb_heading_color' ),
                        'value' => ovabrw_get_option( 'glb_heading_color', '222222' ),
                        'attrs' => [
                            'dir'           => 'ltr',
                            'placeholder'   => '#222222',
                            'autocomplete'  => 'off'
                        ]
                    ]); ?>
                    <div id="colorPickerDiv_ovabrw_glb_heading_color" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>