<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<tr>
    <td width="20%" class="ovabrw-input-price">
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'class'         => 'ovabrw-input-required',
            'name'          => $this->get_meta_name( 'extra_time_hour[]' ),
            'placeholder'   => esc_html__( 'Number', 'ova-brw' ),
            'data_type'     => 'price'
        ]); ?>
    </td>
    <td width="20%">
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'class'         => 'ovabrw-input-required',
            'name'          => $this->get_meta_name( 'extra_time_label[]' ),
            'placeholder'   => esc_html__( 'Text', 'ova-brw' )
        ]); ?>
    </td>
    <td width="20%" class="ovabrw-input-price">
        <?php ovabrw_wp_text_input([
            'type'          => 'text',
            'class'         => 'ovabrw-input-required',
            'name'          => $this->get_meta_name( 'extra_time_price[]' ),
            'placeholder'   => esc_html__( 'Price', 'ova-brw' ),
            'data_type'     => 'price'
        ]); ?>
    </td>
    <td width="1%">
        <button class="button ovabrw-remove-extra-time">x</button>
    </td>
</tr>