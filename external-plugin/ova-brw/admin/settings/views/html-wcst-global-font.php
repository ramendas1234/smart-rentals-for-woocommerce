<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get fonts
$all_fonts 		= ovabrw_get_all_fonts();
$default_font 	= apply_filters( OVABRW_PREFIX.'glb_default_font', 'Poppins' );
$current_font 	= ovabrw_get_option( 'glb_primary_font', $default_font );
$font_weight 	= ovabrw_get_option( 'glb_primary_font_weight', [
    "100",
    "100italic",
    "200",
    "200italic",
    "300",
    "300italic",
    "regular",
    "italic",
    "500",
    "500italic",
    "600",
    "600italic",
    "700",
    "700italic",
    "800",
    "800italic",
    "900",
    "900italic",
]); ?>

<div class="wcst-title">
    <h2><?php esc_html_e( 'Font', 'ova-brw' ); ?></h2>
    <span class="dashicons dashicons-plus-alt2 ovabrw-more"></span>
    <span class="dashicons dashicons-minus ovabrw-less"></span>
</div>
<div class="ovabrw-wcst-fields ovabrw-wcst-primary-font">
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_primary_font', true ); ?>">
                        <?php esc_html_e( 'Primary font', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-select">
                    <select
                        id="<?php $this->get_name( 'glb_primary_font', true ); ?>"
                        class="ovabrw_select2"
                        name="<?php $this->get_name( 'glb_primary_font', true ); ?>"
                        data-placeholder="<?php esc_attr_e( 'Select font', 'ova-brw' ); ?>"
                    >
                        <option value=""></option>
                        <?php if ( ovabrw_array_exists( $all_fonts ) ):
                            // Font Weight
                            $data_font_weight = [];

                            foreach ( $all_fonts as $font ):
                                $data_font_weight[$font->family] = $font->variants;
                        ?>
                            <option
                                value="<?php echo esc_attr( $font->family ); ?>"
                                data-variants="<?php echo esc_attr( json_encode( $font->variants ) ); ?>"
                                <?php ovabrw_selected( $current_font, $font->family ); ?>>
                                <?php echo esc_html( $font->family ); ?>
                            </option>
                        <?php endforeach; endif; ?>
                    </select>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_primary_font_weight', true ); ?>">
                        <?php esc_html_e( 'Load font weight', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-select">
                    <select
                        id="<?php $this->get_name( 'glb_primary_font_weight', true ); ?>"
                        class="ovabrw_select2"
                        name="<?php $this->get_name( 'glb_primary_font_weight[]', true ); ?>"
                        data-placeholder="<?php esc_attr_e( 'Select font weight', 'ova-brw' ); ?>"
                        multiple
                    >
                    <?php if ( isset( $data_font_weight[$current_font] ) && ! empty( $data_font_weight[$current_font] ) && is_array( $data_font_weight[$current_font] ) ):
                        foreach ( $data_font_weight[$current_font] as $weight ):
                    ?>
                            <option value="<?php echo esc_attr( $weight ); ?>"<?php ovabrw_selected( $weight, $font_weight ); ?>>
                                <?php echo esc_html( $weight ); ?>
                            </option>
                    <?php endforeach; endif; ?>
                    </select>
                    <button class="button ovabrw_glb_select_all_font_weight">
                        <?php esc_html_e( 'select all', 'ova-brw' ); ?>
                    </button>
                </td>
            </tr>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label for="<?php $this->get_name( 'glb_custom_font', true ); ?>">
                        <?php esc_html_e( 'Custom font', 'ova-brw' ); ?>
                    </label>
                </th>
                <td class="forminp forminp-select">
                    <?php ovabrw_wp_textarea([
                        'id'    => $this->get_name( 'glb_custom_font' ),
                        'name'  => $this->get_name( 'glb_custom_font' ),
                        'value' => str_replace( '\"', '"', ovabrw_get_option( 'glb_custom_font' ) ),
                        'attrs' => [
                            'rows' => 7
                        ]
                    ]); ?>
                    <p class="description">
                        <?php echo sprintf( esc_html__( 'Step 1: Insert font-face in style.css file: Refer %s.', 'ova-brw' ), '<a href="https://www.w3schools.com/cssref/css3_pr_font-face_rule.asp" target="_blank">https://www.w3schools.com/cssref/css3_pr_font-face_rule.asp</a>' ); ?>
                    </p>
                    <p class="description">
                        <?php esc_html_e( 'Step 2: Insert font-family and font-weight like format: ["Perpetua", "Regular:Bold:Italic:Light"] | ["Name-Font", "Regular:Bold:Italic:Light"].', 'ova-brw' ); ?>
                    </p>
                    <p class="description">
                        <?php esc_html_e( 'Step 3: Refresh customize page to display new font in dropdown font field.', 'ova-brw' ); ?>
                    </p>
                    <br/>
                </td>
            </tr>
        </tbody>
    </table>
</div>