<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
    <?php if ( apply_filters( OVABRW_PREFIX.'show_icon_features', true ) ): ?>
        <td width="30%">
            <?php ovabrw_wp_text_input([
                'type'          => 'text',
                'name'          => $this->get_meta_name( 'features_icons[]' ),
                'placeholder'   => esc_html__( 'Icon-Class', 'ova-brw' )
            ]); ?>
        </td>
    <?php endif; ?>
    <td width="20%">
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'name'          => $this->get_meta_name( 'features_label[]' ),
            'placeholder'   => esc_html__( 'Label', 'ova-brw' )
        ]); ?>
    </td>
    <td width="<?php echo apply_filters( OVABRW_PREFIX.'show_icon_features', true ) ? esc_attr( '29%' ) : esc_attr( '59%' ); ?>">
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'name'          => $this->get_meta_name( 'features_desc[]' ),
            'placeholder'   => esc_html__( 'Description', 'ova-brw' )
        ]); ?>
    </td>
    <td width="10%">
        <?php ovabrw_wp_select_input([
            'name'      => $this->get_meta_name( 'features_special[]' ),
            'options'   => [
                'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                'no'    => esc_html__( 'No', 'ova-brw' )
            ]
        ]); ?>
    </td>
    <td width="10%">
        <?php ovabrw_wp_select_input([
            'name'      => $this->get_meta_name( 'features_featured[]' ),
            'options'   => [
                'yes'   => esc_html__( 'Yes', 'ova-brw' ),
                'no'    => esc_html__( 'No', 'ova-brw' )
            ]
        ]); ?>
    </td>
    <td width="1%">
        <button class="button ovabrw-remove-feature">x</button>
    </td>
</tr>