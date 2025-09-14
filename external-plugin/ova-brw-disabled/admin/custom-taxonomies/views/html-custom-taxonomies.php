<?php if ( !defined( 'ABSPATH' ) ) exit();

// Custom taxonomies
$taxonomies = ovabrw_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

// Data
$post_data = [];
if ( isset( $_POST ) && $_POST ) {
	$post_data = ovabrw_replace( '\\', '', $_POST );
}

// Action
$action = sanitize_text_field( ovabrw_get_meta_data( 'ova_action', $post_data ) );

// Slug
$slug = sanitize_text_field( ovabrw_sanitize_title( ovabrw_get_meta_data( 'slug', $post_data ) ) );
if ( 'new' === $action ) {
    $slug = apply_filters( OVABRW_PREFIX.'prefix_custom_taxonomy', 'brw_' ) . $slug;
}

if ( ovabrw_array_exists( $post_data ) ) {
	// Name
	$name = sanitize_text_field( ovabrw_get_meta_data( 'name', $post_data ) );

	// Singular name
	$singular_name = sanitize_text_field( ovabrw_get_meta_data( 'singular_name', $post_data ) );

	// Label frontend
	$label_frontend = sanitize_text_field( ovabrw_get_meta_data( 'label_frontend', $post_data ) );

	// Enabled
	$enabled = ovabrw_get_meta_data( 'enabled', $post_data );

	// Show listing
	$show_listing = ovabrw_get_meta_data( 'show_listing', $post_data );

	if ( current_user_can( 'publish_posts' ) ) {
		if ( $slug && $name && $singular_name ) {
			$taxonomies[$slug] = [
				'name' 				=> $name,
                'singular_name' 	=> $singular_name,
                'label_frontend'    => $label_frontend,
                'enabled'           => $enabled,
                'show_listing'      => $show_listing
			];
		}

		if ( 'new' === $action ) {
            update_option( OVABRW_PREFIX.'custom_taxonomy', $taxonomies );
        } elseif ( 'edit' === $action ) {
            $old_slug = ovabrw_get_meta_data( 'ova_old_slug', $post_data );

            if ( $old_slug && array_key_exists( $old_slug, $taxonomies ) && $old_slug != $slug  ) {
                unset( $taxonomies[$old_slug] );
            }
            if ( !$slug ) unset( $taxonomies[$slug] );

            update_option( OVABRW_PREFIX.'custom_taxonomy', $taxonomies );
        }

	    // Update in Listing custom post type (Remove, Enable, Disable)
	    $action_update = sanitize_text_field( ovabrw_get_meta_data( ovabrw_meta_key( 'update_table' ), $post_data ) );

	    if ( 'update_table' === $action_update ) {
	    	// Remove
	        if ( 'remove' === ovabrw_get_meta_data( 'remove', $post_data ) ) {
	            $select_field = ovabrw_get_meta_data( 'select_field', $post_data, [] );

	            if ( ovabrw_array_exists( $select_field ) ) {
	                foreach ( $select_field as $field ) {
	                    if ( array_key_exists( $field, $taxonomies ) ) {
	                        unset( $taxonomies[$field] );
	                    }
	                }
	            }
	        }

	        // Enable
	        if ( 'enable' === ovabrw_get_meta_data( 'enable', $post_data ) ) {
	            $select_field = ovabrw_get_meta_data( 'select_field', $post_data, [] );

	            if ( ovabrw_array_exists( $select_field ) ) {
	                foreach ( $select_field as $field ) {
	                    if ( array_key_exists( $field, $taxonomies ) ) {
	                        $taxonomies[$field]['enabled'] = 'on';
	                    }
	                }
	            }
	        }

	        // Disable
	        if ( 'disable' === ovabrw_get_meta_data( 'disable', $post_data ) ) {
	            $select_field = ovabrw_get_meta_data( 'select_field', $post_data, [] );

	            if ( ovabrw_array_exists( $select_field ) ) {
	                foreach ( $select_field as $field ) {
	                    if ( array_key_exists( $field, $taxonomies ) ) {
	                        $taxonomies[$field]['enabled'] = '';
	                    }
	                }
	            }
	        }

	        update_option( OVABRW_PREFIX.'custom_taxonomy', $taxonomies );
	    }
	} else {
	    if ( 'new' === $action && !array_key_exists( $slug, $taxonomies ) ) {
	        if ( $slug && $name && $singular_name ) {
	            $taxonomies[$slug] = [
	            	'name'              => $name,
	                'singular_name'     => $singular_name,
	                'label_frontend'    => $label_frontend,
	                'enabled'           => '',
	                'show_listing'      => $show_listing
	            ];

	            update_option( OVABRW_PREFIX.'custom_taxonomy', $taxonomies );
	        }
	    }
	}
}
	
?>
<div class="wrap">
	<h1 class="wp-heading-inline">
        <?php esc_html_e( 'Custom Taxonomies', 'ova-brw' ); ?>
    </h1>
    <div class="ova-list-checkout-field">
        <form method="post" id="ova_update_form" action="" autocomplete="off">
        	<?php ovabrw_wp_text_input([
        		'type' 	=> 'hidden',
        		'name' 	=> ovabrw_meta_key( 'update_table' ),
        		'value' => 'update_table'
        	]); ?>
            <table cellspacing="0" cellpadding="10px">
                <thead>
                    <th colspan="6">
                        <button type="button" class="button button-primary" id="ovabrw_openform">
                            + <?php esc_html_e( 'Add taxonomy', 'ova-brw' ); ?>
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
                        <th class="slug">
                            <?php esc_html_e( 'Slug', 'ova-brw' ); ?>
                        </th>
                        <th class="name">
                            <?php esc_html_e( 'Name', 'ova-brw' ); ?>
                        </th>
                        <th class="singular_name">
                            <?php esc_html_e( 'Singular name', 'ova-brw' ); ?>
                        </th>
                         <th class="label_frontend">
                            <?php esc_html_e( 'Label Frontend', 'ova-brw' ); ?>
                        </th>
                        <th class="manage_tax">
                            <?php esc_html_e( 'Manage Taxonomy', 'ova-brw' ); ?>
                        </th>
                        <th class="status">
                            <?php esc_html_e( 'Enabled', 'ova-brw' ); ?>
                        </th>
                        <th class="status">
                            <?php esc_html_e( 'Show in Listing', 'ova-brw' ); ?>
                        </th>    
                        <th class="action">
                            <?php esc_html_e( 'Edit', 'ova-brw' ); ?>
                        </th>   
                    </tr>
                </thead>
                <tbody class="ovabrw-sortable-taxonomies-ajax">
                    <?php if ( ovabrw_array_exists( $taxonomies ) ):
                        foreach ( $taxonomies as $key => $field ):
                            $slug 					= $key;
                            $name 					= ovabrw_get_meta_data( 'name', $field );
                            $singular_name  		= ovabrw_get_meta_data( 'singular_name', $field );
                            $label_frontend 		= ovabrw_get_meta_data( 'label_frontend', $field );
                            $enabled        		= ovabrw_get_meta_data( 'enabled', $field );
                            $show_listing   		= ovabrw_get_meta_data( 'show_listing', $field );
                            $enabled_status 		= $enabled ? '<span class="dashicons dashicons-yes tips" data-tip="Yes"></span>' : '-';
                            $show_listing_status 	= $show_listing ? '<span class="dashicons dashicons-yes tips" data-tip="Yes"></span>' : '-';
                            $class_disable  		= !$enabled ? 'class="ova-disable"' : '';
                            $disable_button 		= !$enabled ? 'disabled' : '';
                            $value_enabled  		= $enabled == 'on' ? $slug : '';
                            
                            $data_edit = [
                                'slug'              => $slug,
                                'name'              => $name,
                                'singular_name'     => $singular_name,
                                'label_frontend'    => $label_frontend,
                                'show_listing'      => $show_listing,
                                'enabled'           => $enabled
                            ];
                        ?>
                    <tr <?php echo wp_kses_post( $class_disable ); ?>>
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
	                    		'value' => $slug
	                    	]); ?>
                        </td>
                        <td class="ova-slug">
                            <?php echo esc_html( $slug ); ?>
                        </td>
                        <td class="ova-name">
                            <?php echo esc_html( $name ); ?>
                        </td>
                        <td class="ova-singular_name">
                            <?php echo esc_html( $singular_name ); ?>
                        </td>
                        <td class="ova-label-frontend">
                            <?php echo esc_html( $label_frontend ); ?>
                        </td>
                        <td>
                        <?php
                            $terms = get_terms([
                            	'taxonomy' 		=> $slug,
                                'hide_empty'    => false
                            ]);
                        ?>
                            <a href="<?php echo esc_url( admin_url( 'edit-tags.php?post_type=product&taxonomy='.sanitize_file_name( $slug ) ) ); ?>" title="<?php esc_html_e( 'Manage Taxonomy: Add/Update value of taxonomy', 'ova-brw' ); ?>">
                                <i class="dashicons dashicons-category"></i>
                                (<?php echo ! is_wp_error( $terms ) ? count( $terms ): 0; ?>)    
                            </a>
                        </td>
                        <td class="ova-enable status">
                            <?php echo wp_kses_post( $enabled_status ); ?>
                        </td>
                        <td class="ova-show-listing status">
                            <?php echo wp_kses_post( $show_listing_status ); ?>
                        </td>
                        <td class="ova-edit edit">
                            <button type="button" <?php echo esc_attr( $disable_button ); ?> class="button ova-button ovabrw_edit_field_form" data-data_edit="<?php echo esc_attr( json_encode( $data_edit ) ); ?>" >
                                <?php esc_html_e( 'Edit', 'ova-brw' ); ?>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; endif; ?>           
                </tbody>
            </table>
        </form>
    </div>
    <div class="ova-wrap-popup-ckf">
        <div id="ova_new_field_form" class="ova-popup-wrapper">
            <a href="#" class="close_popup" id="ovabrw_close_popup">X</a>
            <?php $this->popup_form_fields( 'new', $taxonomies ); ?>
        </div>
    </div>
</div>