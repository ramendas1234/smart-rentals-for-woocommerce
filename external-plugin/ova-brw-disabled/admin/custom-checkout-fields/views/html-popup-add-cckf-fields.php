<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<form method="post" id="ova_popup_field_form" action="" autocomplete="off">
    <?php ovabrw_wp_text_input([
        'type'  => 'hidden',
        'name'  => 'ova_action',
        'value' => isset( $type ) ? $type : ''
    ]); ?>
    <?php ovabrw_wp_text_input([
        'type'  => 'hidden',
        'name'  => 'ova_old_name',
        'value' => ''
    ]); ?>
    <table width="100%">
        <tr class="ova-row-type">
            <td class="ovabrw-required label"><?php esc_html_e( 'Type', 'ova-brw' ); ?></td>
            <td>
                <?php ovabrw_wp_select_input([
                    'id'        => 'ova_type',
                    'name'      => 'type',
                    'options'   => [
                        'text'      => esc_html__( 'Text', 'ova-brw' ),
                        'password'  => esc_html__( 'Password', 'ova-brw' ),
                        'email'     => esc_html__( 'Email', 'ova-brw' ),
                        'tel'       => esc_html__( 'Phone', 'ova-brw' ),
                        'textarea'  => esc_html__( 'Textarea', 'ova-brw' ),
                        'file'      => esc_html__( 'File', 'ova-brw' ),
                        'number'    => esc_html__( 'Number', 'ova-brw' ),
                        'date'      => esc_html__( 'Date', 'ova-brw' ),
                        'radio'     => esc_html__( 'Radio', 'ova-brw' ),
                        'checkbox'  => esc_html__( 'Checkbox', 'ova-brw' ),
                        'select'    => esc_html__( 'Select', 'ova-brw' )
                    ],
                    'required'  => true
                ]); ?>
                <span class="formats-file-size">
                    <em>
                        <?php esc_html_e( 'Formats: .jpg, .jpeg, .png, .pdf, .doc', 'ova-brw' ); ?>
                    </em>
                </span>
            </td>
        </tr>
        <tr class="row-options">
            <td width="30%" class="label" valign="top">
                <?php esc_html_e( 'Options', 'ova-brw' ); ?>
            </td>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="ova-sub-table">
                    <thead>
                        <tr>
                            <th class="ovabrw-required">
                                <?php esc_html_e( 'Value(unique)', 'ova-brw' ); ?>
                            </th>
                            <th class="ovabrw-required">
                                <?php esc_html_e( 'Text', 'ova-brw' ); ?>
                            </th>
                            <th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
                            <th><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'ova-brw' ); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="ovabrw-sortable">
                        <tr>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'name'          => 'ova_options_key[]',
                                    'placeholder'   => 'text',
                                    'required'      => true
                                ]); ?>
                            </td>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'name'          => 'ova_options_text[]',
                                    'placeholder'   => 'text',
                                    'required'      => true
                                ]); ?>
                            </td>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'name'          => 'ova_options_price[]',
                                    'placeholder'   => 'number'
                                ]); ?>
                            </td>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'type'          => 'number',
                                    'name'          => 'ova_options_price[]',
                                    'placeholder'   => 'number',
                                    'attrs'         => [
                                        'min' => 0
                                    ]
                                ]); ?>
                            </td>
                            <td class="ova-box">
                                <a href="#" class="ovabrw_addfield btn btn-blue" title="<?php esc_html_e( 'Add new option', 'ova-brw' ); ?>">+</a>
                            </td>
                            <td class="ova-box">
                                <a href="#" class="ovabrw_remove_row btn btn-red" title="<?php esc_html_e( 'Remove option', 'ova-brw' ); ?>">x</a>
                            </td>
                            <td class="ova-box sort">
                                <span class="dashicons dashicons-menu" title="<?php esc_html_e( 'Drag & Drop', 'ova-brw' ); ?>"></span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <span>
                                    <em>
                                        <?php esc_html_e( 'Quantity: maximum per booking', 'ova-brw' ); ?>
                                    </em>
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>                
            </td>
        </tr>
        <tr class="row-values">
            <td width="30%" class="label" valign="top">
                <?php esc_html_e( 'Values', 'ova-brw' ); ?>
            </td>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="ova-sub-table">
                    <thead>
                        <tr>
                            <th class="ovabrw-required">
                                <?php esc_html_e( 'Value(unique)', 'ova-brw' ); ?>
                            </th>
                            <th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
                            <th><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'ova-brw' ); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="ovabrw-sortable">
                        <tr>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'name'          => 'ova_values[]',
                                    'placeholder'   => 'text',
                                    'required'      => true
                                ]); ?>
                            </td>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'name'          => 'ova_prices[]',
                                    'placeholder'   => 'number'
                                ]); ?>
                            </td>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'type'          => 'number',
                                    'name'          => 'ova_values[]',
                                    'placeholder'   => 'number',
                                    'attrs'         => [
                                        'min' => 0
                                    ]
                                ]); ?>
                            </td>
                            <td class="ova-box">
                                <a href="#" class="ovabrw_add_value btn btn-blue" title="<?php esc_html_e( 'Add', 'ova-brw' ); ?>">+</a>
                            </td>
                            <td class="ova-box">
                                <a href="#" class="ovabrw_remove_value btn btn-red" title="<?php esc_html_e( 'Remove', 'ova-brw' ); ?>">x</a>
                            </td>
                            <td class="ova-box sort">
                                <span class="dashicons dashicons-menu" title="<?php esc_html_e( 'Drag & Drop', 'ova-brw' ); ?>"></span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <span>
                                    <em>
                                        <?php esc_html_e( 'Quantity: maximum per booking', 'ova-brw' ); ?>
                                    </em>
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>                
            </td>
        </tr>
        <tr class="row-checkbox-options">
            <td width="30%" class="label" valign="top">
                <?php esc_html_e( 'Options', 'ova-brw' ); ?>
            </td>
            <td>
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="ova-sub-table">
                    <thead>
                        <tr>
                            <th class="ovabrw-required">
                                <?php esc_html_e( 'Value(unique)', 'ova-brw' ); ?>
                            </th>
                            <th class="ovabrw-required">
                                <?php esc_html_e( 'Text', 'ova-brw' ); ?>
                            </th>
                            <th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
                            <th><?php esc_html_e( 'Quantity', 'ova-brw' ); ?></th>
                            <th><?php esc_html_e( 'Actions', 'ova-brw' ); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="ovabrw-sortable">
                        <tr>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'name'          => 'ova_checkbox_key[]',
                                    'placeholder'   => 'text',
                                    'required'      => true
                                ]); ?>
                            </td>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'name'          => 'ova_checkbox_text[]',
                                    'placeholder'   => 'text',
                                    'required'      => true
                                ]); ?>
                            </td>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'name'          => 'ova_checkbox_price[]',
                                    'placeholder'   => 'number'
                                ]); ?>
                            </td>
                            <td>
                                <?php ovabrw_wp_text_input([
                                    'type'          => 'number',
                                    'name'          => 'ova_checkbox_qty[]',
                                    'placeholder'   => 'number'
                                ]); ?>
                            </td>
                            <td class="ova-box">
                                <a href="#" class="ovabrw_add_checkbox_option btn btn-blue" title="<?php esc_html_e( 'Add new option', 'ova-brw' ); ?>">+</a>
                            </td>
                            <td class="ova-box">
                                <a href="#" class="ovabrw_remove_checkbox_option btn btn-red" title="<?php esc_html_e( 'Remove option', 'ova-brw' ); ?>">x</a>
                            </td>
                            <td class="ova-box sort">
                                <span class="dashicons dashicons-menu" title="<?php esc_html_e( 'Drag & Drop', 'ova-brw' ); ?>"></span>
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td>
                                <span>
                                    <em>
                                        <?php esc_html_e( 'Quantity: maximum per booking', 'ova-brw' ); ?>
                                    </em>
                                </span>
                            </td>
                        </tr>
                    </tfoot>
                </table>                
            </td>
        </tr>
        <tr class="ova-row-file-size">
            <td class="label">
                <?php esc_html_e( 'Max Size', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'name'  => 'max_file_size',
                    'value' => '20'
                ]); ?>
                <span><?php esc_html_e( 'Default: 20MB', 'ova-brw' ); ?></span>
            </td>
        </tr>
        <tr class="ova-row-name">
            <td class="ovabrw-required label">
                <?php esc_html_e( 'Name', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'name'      => 'name',
                    'required'  => true
                ]); ?>
                <span>
                    <em>
                        <?php esc_html_e( 'Unique, only lowercase, not space', 'ova-brw' ); ?>
                    </em>
                </span>
            </td>
        </tr>
        <tr class="ova-row-label">
            <td class="ovabrw-required label">
                <?php esc_html_e( 'Label', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'name'      => 'label',
                    'required'  => true
                ]); ?>
            </td>
        </tr>
        <tr class="ova-row-description">
            <td class="label">
                <?php esc_html_e( 'Description', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_textarea([
                    'name' => 'description'
                ]); ?>
            </td>
        </tr>
        <tr class="ova-row-placeholder">
            <td class="label">
                <?php esc_html_e( 'Placeholder', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'name' => 'placeholder'
                ]); ?>
            </td>
        </tr>
        <tr class="ova-row-default">
            <td class="label">
                <?php esc_html_e( 'Default value', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'name' => 'default'
                ]); ?>
            </td>
        </tr>
        <tr class="ova-row-min">
            <td class="label">
                <?php esc_html_e( 'Min', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'type'  => 'number',
                    'name'  => 'min',
                    'attrs' => [
                        'autocomplete'  => 'off',
                        'min'           => '0'
                    ]
                ]); ?>
            </td>
        </tr>
        <tr class="ova-row-max">
            <td class="label">
                <?php esc_html_e( 'Max', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'type'  => 'number',
                    'name'  => 'max',
                    'attrs' => [
                        'autocomplete'  => 'off',
                        'min'           => '0'
                    ]
                ]); ?>
            </td>
        </tr>
        <tr class="ova-row-default-date">
            <td class="label">
                <?php esc_html_e( 'Default', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'id'    => ovabrw_unique_id( 'cckf_default_date' ),
                    'class' => 'ovabrw-datepicker',
                    'name'  => 'default_date'
                ]); ?>
            </td>
        </tr>
        <tr class="ova-row-min-date">
            <td class="label">
                <?php esc_html_e( 'Min', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'id'    => ovabrw_unique_id( 'cckf_min_date' ),
                    'class' => 'ovabrw-datepicker',
                    'name'  => 'min_date'
                ]); ?>
            </td>
        </tr>
        <tr class="ova-row-max-date">
            <td class="label">
                <?php esc_html_e( 'Max', 'ova-brw' ); ?>
            </td>
            <td>
                <?php ovabrw_wp_text_input([
                    'id'    => ovabrw_unique_id( 'cckf_max_date' ),
                    'class' => 'ovabrw-datepicker',
                    'name'  => 'max_date'
                ]); ?>
            </td>
        </tr>
        <tr class="ova-row-class">
            <td class="label"><?php esc_html_e( 'Class', 'ova-brw' ); ?></td>
            <td>
                <?php ovabrw_wp_text_input([
                    'name'  => 'class'
                ]); ?>
            </td>
        </tr>
        <tr class="row-required">
            <td>&nbsp;</td>
            <td class="check-box">
                <?php ovabrw_wp_text_input([
                    'type'      => 'checkbox',
                    'id'        => 'ova_required',
                    'name'      => 'required',
                    'value'     => 'on',
                    'checked'   => true
                ]); ?>
                <label for="ova_required">
                    <?php esc_html_e( 'Required', 'ova-brw' ); ?>
                </label>
                <br/>
                <?php ovabrw_wp_text_input([
                    'type'      => 'checkbox',
                    'id'        => 'ova_enable',
                    'name'      => 'enabled',
                    'value'     => 'on',
                    'checked'   => true
                ]); ?>
                <label for="ova_enable">
                    <?php esc_html_e( 'Enable', 'ova-brw' ); ?>
                </label>
                <br/>
            </td>                     
            <td class="label"></td>
        </tr>
    </table>
    <button type='submit' class="button button-primary">
        <?php esc_html_e( 'Save', 'ova-brw' ); ?>
    </button>
</form>