<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Color', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-primary-color">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_primary_color', true ); ?>">
                        <?php esc_html_e( 'Primary color', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-color">
                    <span class="colorpickpreview" style="background-color: <?php echo esc_attr( ovabrw_get_option( 'glb_primary_color', '#E56E00' ) ); ?>"></span>
                    <?php ovabrw_wp_text_input([
                        'id'    => $this->get_name( 'glb_primary_color' ),
                        'class' => 'colorpick',
                        'name'  => $this->get_name( 'glb_primary_color' ),
                        'value' => ovabrw_get_option( 'glb_primary_color', '#E56E00' ),
                        'attrs' => [
                            'dir'           => 'ltr',
                            'placeholder'   => '#E56E00'
                        ]
                    ]); ?>
                    <div id="colorPickerDiv_ovabrw_glb_primary_color" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_light_color', true ); ?>">
                        <?php esc_html_e( 'Light color', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-color">
                    <span class="colorpickpreview" style="background-color: <?php echo esc_attr( ovabrw_get_option( 'glb_light_color', '#C3C3C3' ) ); ?>"></span>
                    <?php ovabrw_wp_text_input([
                        'id'    => $this->get_name( 'glb_light_color' ),
                        'class' => 'colorpick',
                        'name'  => $this->get_name( 'glb_light_color' ),
                        'value' => ovabrw_get_option( 'glb_light_color', '#C3C3C3' ),
                        'attrs' => [
                            'dir'           => 'ltr',
                            'placeholder'   => '#C3C3C3'
                        ]
                    ]); ?>
                    <div id="colorPickerDiv_ovabrw_glb_light_color" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
                </td>
            </tr>
        </tbody>
    </table>
</div>