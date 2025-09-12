<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get custom checkout fields
$cckf = ovabrw_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

// Data
$post_data = [];
if ( isset( $_POST ) && $_POST ) {
	$post_data = ovabrw_replace( '\\', '', $_POST );
}

// Action
$action = sanitize_text_field( ovabrw_get_meta_data( 'ova_action', $post_data ) );

// Name
$name = sanitize_text_field( ovabrw_sanitize_title( ovabrw_get_meta_data( 'name', $post_data ) ) );

// Update popup
if ( ovabrw_array_exists( $post_data ) ) {
    if ( current_user_can( 'publish_posts' ) ) {
    	// Get type
        $type = sanitize_text_field( ovabrw_get_meta_data( 'type', $post_data ) );

        if ( $name ) {
            $cckf[$name] = [
            	'type'          => $type,
            	'label' 		=> sanitize_text_field( ovabrw_get_meta_data( 'label', $post_data ) ),
            	'description' 	=> sanitize_text_field( ovabrw_get_meta_data( 'description', $post_data ) ),
            	'default' 		=> sanitize_text_field( ovabrw_get_meta_data( 'default', $post_data ) ),
            	'placeholder' 	=> sanitize_text_field( ovabrw_get_meta_data( 'placeholder', $post_data ) ),
            	'class' 		=> sanitize_text_field( ovabrw_get_meta_data( 'class', $post_data ) ),
            	'required' 		=> sanitize_text_field( ovabrw_get_meta_data( 'required', $post_data ) ),
            	'enabled' 		=> sanitize_text_field( ovabrw_get_meta_data( 'enabled', $post_data ) ),
            	'show_in_email' => sanitize_text_field( ovabrw_get_meta_data( 'show_in_email', $post_data ) ),
            	'show_in_order' => sanitize_text_field( ovabrw_get_meta_data( 'show_in_order', $post_data ) )
            ];
        }

        // Select
        if ( 'select' === $type ) {
        	// Option keys
        	$opt_keys = ovabrw_get_meta_data( 'ova_options_key', $post_data );

        	// Option texts
        	$opt_texts = ovabrw_get_meta_data( 'ova_options_text', $post_data );

        	// Option prices
        	$opt_prices = ovabrw_get_meta_data( 'ova_options_price', $post_data );

        	// Option qtys
        	$opt_qtys = ovabrw_get_meta_data( 'ova_options_qty', $post_data );

        	$cckf[$name]['ova_options_key'] 	= $this->sanitize_keys( $opt_keys, $opt_texts );
        	$cckf[$name]['ova_options_text'] 	= $opt_texts;
        	$cckf[$name]['ova_options_price'] 	= $opt_prices;
        	$cckf[$name]['ova_options_qty'] 	= $opt_qtys;
        	$cckf[$name]['placeholder'] 		= '';
        }

        // Radio
        if ( 'radio' === $type ) {
            $cckf[$name]['ova_values']   = ovabrw_get_meta_data( 'ova_values', $post_data );
            $cckf[$name]['ova_prices']   = ovabrw_get_meta_data( 'ova_prices', $post_data );
            $cckf[$name]['ova_qtys']     = ovabrw_get_meta_data( 'ova_qtys', $post_data );
            $cckf[$name]['placeholder']  = '';
        }

        // Checkbox
        if ( 'checkbox' === $type ) {
        	// Option keys
        	$opt_keys = ovabrw_get_meta_data( 'ova_checkbox_key', $post_data );

        	// Option texts
        	$opt_texts = ovabrw_get_meta_data( 'ova_checkbox_text', $post_data );

        	// Option prices
        	$opt_prices = ovabrw_get_meta_data( 'ova_checkbox_price', $post_data );

        	// Option qtys
        	$opt_qtys = ovabrw_get_meta_data( 'ova_checkbox_qty', $post_data );

            $cckf[$name]['ova_checkbox_key']     = $this->sanitize_keys( $opt_keys, $opt_texts );
            $cckf[$name]['ova_checkbox_text']    = $opt_texts;
            $cckf[$name]['ova_checkbox_price']   = $opt_prices;
            $cckf[$name]['ova_checkbox_qty']     = $opt_qtys;
            $cckf[$name]['placeholder']          = '';
        }

        // File
        if ( 'file' === $type ) {
            $cckf[$name]['placeholder']      = '';
            $cckf[$name]['default']          = '';
            $cckf[$name]['max_file_size']    = ovabrw_get_meta_data( 'max_file_size', $post_data );
        }

        // Number
        if ( 'number' === $type ) {
            $cckf[$name]['min'] = ovabrw_get_meta_data( 'min', $post_data );
            $cckf[$name]['max'] = ovabrw_get_meta_data( 'max', $post_data );
        }

        if ( 'date' === $type ) {
            $cckf[$name]['default_date'] = ovabrw_get_meta_data( 'default_date', $post_data );
            $cckf[$name]['min_date']     = ovabrw_get_meta_data( 'min_date', $post_data );
            $cckf[$name]['max_date']     = ovabrw_get_meta_data( 'max_date', $post_data );
        }

        if ( 'new' === $action ) {
            update_option( ovabrw_meta_key( 'booking_form' ), $cckf );
        } elseif ( 'edit' === $action ) {
            $old_name = ovabrw_get_meta_data( 'ova_old_name', $post_data );

            if ( $old_name && array_key_exists( $old_name, $cckf ) && $old_name != $name  ) {
                unset( $cckf[$old_name] );
            }

            if ( !$name ) {
                unset( $cckf[$name] );
            }

            update_option( ovabrw_meta_key( 'booking_form' ), $cckf );
        }

        // Update action
        $update_action = sanitize_text_field( ovabrw_get_meta_data( ovabrw_meta_key( 'update_table' ), $post_data ) );

        if ( 'update_table' === $update_action ) {
        	// Remove
            if ( 'remove' === ovabrw_get_meta_data( 'remove', $post_data ) ) {
                $select_field = ovabrw_get_meta_data( 'select_field', $post_data, [] );

                if ( ovabrw_array_exists( $select_field ) ) {
                    foreach ( $select_field as $field ) {
                        if ( array_key_exists( $field, $cckf ) ) {
                            unset( $cckf[$field] );
                        }
                    }
                }
            }

            // Enable
            if ( 'enable' === ovabrw_get_meta_data( 'enable', $post_data ) ) {
                $select_field = ovabrw_get_meta_data( 'select_field', $post_data, [] );

                if ( ovabrw_array_exists( $select_field ) ) {
                    foreach ( $select_field as $field ) {
                        if ( !empty( $field ) && array_key_exists( $field, $cckf ) ) {
                            $cckf[$field]['enabled'] = 'on';
                        }
                    }
                }
            }

            // Disable
            if ( 'disable' === ovabrw_get_meta_data( 'disable', $post_data ) ) {
                $select_field = ovabrw_get_meta_data( 'select_field', $post_data, [] );

                if ( ovabrw_array_exists( $select_field ) ) {
                    foreach ( $select_field as $field ) {
                        if ( !empty( $field ) && array_key_exists( $field, $cckf ) ) {
                            $cckf[$field]['enabled'] = '';
                        }
                    }
                }
            }

            update_option( ovabrw_meta_key( 'booking_form' ), $cckf );
        }
    } else {
        if ( $name && !array_key_exists( $name, $cckf ) && 'new' == $action ) {
        	// Get type
        	$type = sanitize_text_field( ovabrw_get_meta_data( 'type', $post_data ) );

            $cckf[$name] = [
            	'type'          => $type,
            	'label' 		=> sanitize_text_field( ovabrw_get_meta_data( 'label', $post_data ) ),
            	'description' 	=> sanitize_text_field( ovabrw_get_meta_data( 'description', $post_data ) ),
            	'default' 		=> sanitize_text_field( ovabrw_get_meta_data( 'default', $post_data ) ),
            	'placeholder' 	=> sanitize_text_field( ovabrw_get_meta_data( 'placeholder', $post_data ) ),
            	'class' 		=> sanitize_text_field( ovabrw_get_meta_data( 'class', $post_data ) ),
            	'required' 		=> sanitize_text_field( ovabrw_get_meta_data( 'required', $post_data ) ),
            	'enabled' 		=> sanitize_text_field( ovabrw_get_meta_data( 'enabled', $post_data ) ),
            	'show_in_email' => sanitize_text_field( ovabrw_get_meta_data( 'show_in_email', $post_data ) ),
            	'show_in_order' => sanitize_text_field( ovabrw_get_meta_data( 'show_in_order', $post_data ) )
            ];

            // Select
	        if ( 'select' === $type ) {
	        	// Option keys
	        	$opt_keys = ovabrw_get_meta_data( 'ova_options_key', $post_data );

	        	// Option texts
	        	$opt_texts = ovabrw_get_meta_data( 'ova_options_text', $post_data );

	        	// Option prices
	        	$opt_prices = ovabrw_get_meta_data( 'ova_options_price', $post_data );

	        	// Option qtys
	        	$opt_qtys = ovabrw_get_meta_data( 'ova_options_qty', $post_data );

	        	$cckf[$name]['ova_options_key'] 	= $this->sanitize_keys( $opt_keys, $opt_texts );
	        	$cckf[$name]['ova_options_text'] 	= $opt_texts;
	        	$cckf[$name]['ova_options_price'] 	= $opt_prices;
	        	$cckf[$name]['ova_options_qty'] 	= $opt_qtys;
	        	$cckf[$name]['placeholder'] 		= '';
	        }

	        // Radio
	        if ( 'radio' === $type ) {
	            $cckf[$name]['ova_values']   = ovabrw_get_meta_data( 'ova_values', $post_data );
	            $cckf[$name]['ova_prices']   = ovabrw_get_meta_data( 'ova_prices', $post_data );
	            $cckf[$name]['ova_qtys']     = ovabrw_get_meta_data( 'ova_qtys', $post_data );
	            $cckf[$name]['placeholder']  = '';
	        }

	        // Checkbox
	        if ( 'checkbox' === $type ) {
	        	// Option keys
	        	$opt_keys = ovabrw_get_meta_data( 'ova_checkbox_key', $post_data );

	        	// Option texts
	        	$opt_texts = ovabrw_get_meta_data( 'ova_checkbox_text', $post_data );

	        	// Option prices
	        	$opt_prices = ovabrw_get_meta_data( 'ova_checkbox_price', $post_data );

	        	// Option qtys
	        	$opt_qtys = ovabrw_get_meta_data( 'ova_checkbox_qty', $post_data );

	            $cckf[$name]['ova_checkbox_key']     = $this->sanitize_keys( $opt_keys, $opt_texts );
	            $cckf[$name]['ova_checkbox_text']    = $opt_texts;
	            $cckf[$name]['ova_checkbox_price']   = $opt_prices;
	            $cckf[$name]['ova_checkbox_qty']     = $opt_qtys;
	            $cckf[$name]['placeholder']          = '';
	        }

	        // File
	        if ( 'file' === $type ) {
	            $cckf[$name]['placeholder']      = '';
	            $cckf[$name]['default']          = '';
	            $cckf[$name]['max_file_size']    = ovabrw_get_meta_data( 'max_file_size', $post_data );
	        }

	        // Number
	        if ( 'number' === $type ) {
	            $cckf[$name]['min'] = ovabrw_get_meta_data( 'min', $post_data );
	            $cckf[$name]['max'] = ovabrw_get_meta_data( 'max', $post_data );
	        }

	        if ( 'date' === $type ) {
	            $cckf[$name]['default_date'] = ovabrw_get_meta_data( 'default_date', $post_data );
	            $cckf[$name]['min_date']     = ovabrw_get_meta_data( 'min_date', $post_data );
	            $cckf[$name]['max_date']     = ovabrw_get_meta_data( 'max_date', $post_data );
	        }

            update_option( ovabrw_meta_key( 'booking_form' ), $cckf );
        }
    }
}

?>

<div class="wrap">
    <h1 class="wp-heading-inline">
        <?php esc_html_e( 'Custom Checkout Fields', 'ova-brw' ); ?>
    </h1>
    <div class="ova-list-checkout-field">
        <form method="post" id="ova_update_form" action="">
        	<?php ovabrw_wp_text_input([
        		'type' 	=> 'hidden',
        		'name' 	=> ovabrw_meta_key( 'update_table' ),
        		'value' => 'update_table'
        	]); ?>
            <table cellspacing="0" cellpadding="10px">
                <thead>
                    <th colspan="6">
                        <button type="button" class="button button-primary" id="ovabrw_openform">
                            + <?php esc_html_e( 'Add field', 'ova-brw' ); ?>
                        </button>
                        <?php ovabrw_wp_text_input([
                        	'type' 	=> 'submit',
                        	'class' => 'button',
                        	'name' 	=> 'remove',
                        	'value' => 'remove'
                        ]); ?>
                        <?php ovabrw_wp_text_input([
                        	'type' 	=> 'submit',
                        	'class' => 'button',
                        	'name' 	=> 'enable',
                        	'value' => 'enable'
                        ]); ?>
                        <?php ovabrw_wp_text_input([
                        	'type' 	=> 'submit',
                        	'class' => 'button',
                        	'name' 	=> 'disable',
                        	'value' => 'disable'
                        ]); ?>
                        <div class="ovabrw-loading">
                            <span class="dashicons dashicons-update-alt"></span>
                        </div>
                    </th>
                    <tr>
                        <th class="check-column">
                        	<?php ovabrw_wp_text_input([
	                        	'type' 	=> 'checkbox',
	                        	'id' 	=> ovabrw_meta_key( 'select_all_field' ),
	                        	'attrs' => [
	                        		'style' => 'margin:0px 4px -1px -1px;'
	                        	]
	                        ]); ?>
                        </th>
                        <th class="name"><?php esc_html_e( 'Name', 'ova-brw' ); ?></th>
                        <th class="id"><?php esc_html_e( 'Type', 'ova-brw' ); ?></th>
                        <th><?php esc_html_e( 'Label', 'ova-brw' ); ?></th>
                        <th><?php esc_html_e( 'Placeholder', 'ova-brw' ); ?></th>
                        <th class="status"><?php esc_html_e( 'Required', 'ova-brw' ); ?></th>
                        <th class="status"><?php esc_html_e( 'Enabled', 'ova-brw' ); ?></th>    
                        <th class="action"><?php esc_html_e( 'Edit', 'ova-brw' ); ?></th> 
                    </tr>
                </thead>
                <tbody class="ovabrw-sortable-ajax">
                    <?php if ( ovabrw_array_exists( $cckf ) ): 
                        foreach ( $cckf as $key => $field ):
                            $name               = $key;
                            $type               = ovabrw_get_meta_data( 'type', $field );
                            $max_file_size      = ovabrw_get_meta_data( 'max_file_size', $field );
                            $label              = ovabrw_get_meta_data( 'label', $field );
                            $description        = ovabrw_get_meta_data( 'description', $field );
                            $placeholder        = ovabrw_get_meta_data( 'placeholder', $field );
                            $default            = ovabrw_get_meta_data( 'default', $field );
                            $min                = ovabrw_get_meta_data( 'min', $field );
                            $max                = ovabrw_get_meta_data( 'max', $field );
                            $default_date       = ovabrw_get_meta_data( 'default_date', $field );
                            $min_date           = ovabrw_get_meta_data( 'min_date', $field );
                            $max_date           = ovabrw_get_meta_data( 'max_date', $field );
                            $class              = ovabrw_get_meta_data( 'class', $field );
                            $required           = ovabrw_get_meta_data( 'required', $field );
                            $enabled            = ovabrw_get_meta_data( 'enabled', $field );
                            $opt_keys    		= ovabrw_get_meta_data( 'ova_options_key', $field, [] );
                            $opt_texts   		= ovabrw_get_meta_data( 'ova_options_text', $field, [] );
                            $opt_prices  		= ovabrw_get_meta_data( 'ova_options_price', $field, [] );
                            $opt_qtys  			= ovabrw_get_meta_data( 'ova_options_qty', $field, [] );
                            $radio_values 		= ovabrw_get_meta_data( 'ova_values', $field, [] );
                            $radio_prices 		= ovabrw_get_meta_data( 'ova_prices', $field, [] );
                            $radio_qtys 		= ovabrw_get_meta_data( 'ova_qtys', $field, [] );
                            $checkbox_keys   	= ovabrw_get_meta_data( 'ova_checkbox_key', $field, [] );
                            $checkbox_texts  	= ovabrw_get_meta_data( 'ova_checkbox_text', $field, [] );
                            $checkbox_prices 	= ovabrw_get_meta_data( 'ova_checkbox_price', $field, [] );
                            $checkbox_qtys   	= ovabrw_get_meta_data( 'ova_checkbox_qty', $field, [] );
                            $required_status 	= $required ? '<span class="dashicons dashicons-yes tips" data-tip="Yes"></span>' : '-';
                            $enabled_status 	= $enabled ? '<span class="dashicons dashicons-yes tips" data-tip="Yes"></span>' : '-';
                            $class_disable  	= ! $enabled ? 'class="ova-disable"' : '';
                            $disable_button 	= ! $enabled ? 'disabled' : '';
                            $value_enabled  	= $enabled == 'on' ? $name : '';
                            
                            $data_edit = [
                                'name'                  => $name,
                                'type'                  => $type,
                                'max_file_size'         => $max_file_size,
                                'label'                 => $label,
                                'description'           => $description,
                                'placeholder'           => $placeholder,
                                'default'               => $default,
                                'min'                   => $min,
                                'max'                   => $max,
                                'default_date'          => $default_date,
                                'min_date'              => $min_date,
                                'max_date'              => $max_date,
                                'class'                 => $class,
                                'ova_options_key'       => $opt_keys,
                                'ova_options_text'      => $opt_texts,
                                'ova_options_price'     => $opt_prices,
                                'ova_options_qty'       => $opt_qtys,
                                'ova_values'            => $radio_values,
                                'ova_prices'            => $radio_prices,
                                'ova_qtys'              => $radio_qtys,
                                'ova_checkbox_key'      => $checkbox_keys,
                                'ova_checkbox_text'     => $checkbox_texts,
                                'ova_checkbox_price'    => $checkbox_prices,
                                'ova_checkbox_qty'      => $checkbox_qtys,
                                'required'              => $required,
                                'enabled'               => $enabled
                            ];
                    ?>
                    <tr <?php echo wp_kses_post( $class_disable ); ?>>
                    	<?php ovabrw_wp_text_input([
                    		'type' 	=> 'hidden',
                    		'name' 	=> ovabrw_meta_key( 'fields[]' ),
                    		'value' => $name
                    	]); ?>
                    	<?php ovabrw_wp_text_input([
                    		'type' 	=> 'hidden',
                    		'name' 	=> 'remove_field[]',
                    		'value' => ''
                    	]); ?>
                    	<?php ovabrw_wp_text_input([
                    		'type' 	=> 'hidden',
                    		'name' 	=> 'enable_field[]',
                    		'value' => $value_enabled
                    	]); ?>
                        <td class="ova-checkbox">
                        	<?php ovabrw_wp_text_input([
	                    		'type' 	=> 'checkbox',
	                    		'name' 	=> 'select_field[]',
	                    		'value' => $name
	                    	]); ?>
                        </td>
                        <td class="ova-name"><?php echo esc_html( $key ); ?></td>
                        <td class="ova-type"><?php echo esc_html( $type ); ?></td>
                        <td class="ova-label"><?php echo esc_html( $label ); ?></td>
                        <td class="ova-placeholder"><?php echo esc_html( $placeholder ); ?></td>
                        <td class="ova-require status"><?php echo $required_status; ?></td>
                        <td class="ova-enable status"><?php echo $enabled_status; ?></td>
                        <td class="ova-edit edit">
                            <button type="button" <?php echo esc_attr( $disable_button ); ?> class="button ova-button ovabrw_edit_field_form" data-data_edit="<?php echo esc_attr( json_encode( $data_edit ) ); ?>">
                                <?php esc_html_e( 'Edit', 'ova-brw' ); ?>
                            </button>
                        </td>
                    </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </form>
    </div>
    <div class="ova-wrap-popup-ckf">
        <div id="ova_new_field_form" class="ova-popup-wrapper" title="<?php esc_html_e( 'New Checkout Field', 'ova-brw' ); ?>">
            <a href="#" id="ovabrw_close_popup" class="close_popup">X</a>
            <?php $this->popup_form_fields( 'new', $cckf ); ?>
        </div>
    </div>
    <input
        type="hidden"
        name="ovabrw-datepicker-options"
        value="<?php echo esc_attr( wp_json_encode( ovabrw_admin_datepicker_options() ) ); ?>"
    />
</div>