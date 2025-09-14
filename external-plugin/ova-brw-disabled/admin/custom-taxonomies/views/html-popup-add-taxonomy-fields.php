<?php if ( !defined( 'ABSPATH' ) ) exit(); ?>

<form method="post" id="ova_popup_field_form" action="" autocomplete="off">
	<?php ovabrw_wp_text_input([
		'type' 	=> 'hidden',
		'name' 	=> 'ova_action',
		'value' => isset( $type ) ? $type : ''
	]); ?>
	<?php ovabrw_wp_text_input([
		'type' 	=> 'hidden',
		'name' 	=> 'ova_old_name'
	]); ?>
	<?php ovabrw_wp_text_input([
		'type' 	=> 'hidden',
		'name' 	=> 'ova_old_slug'
	]); ?>
    <table width="100%">
        <tr><td colspan="2" class="err_msgs"></td></tr>
        <tr class="ova-row-slug">
            <td class="ovabrw-required label">
                <?php esc_html_e( 'Slug', 'ova-brw' ); ?>
            </td>
            <td>
            	<?php ovabrw_wp_text_input([
					'name' 			=> 'slug',
					'placeholder' 	=> esc_html__( 'taxonomy_1', 'ova-brw' ),
					'required' 		=> true
				]); ?>
                <br>
                <span>
                    <?php esc_html_e( 'Taxonomy key, must not exceed 32 characters', 'ova-brw' ); ?>
                </span>
            </td>
        </tr>
        <tr class="ova-row-name">
            <td class="ovabrw-required label">
                <?php esc_html_e( 'Name', 'ova-brw' ); ?>
            </td>
            <td>
            	<?php ovabrw_wp_text_input([
					'name' 			=> 'name',
					'placeholder' 	=> esc_html__( 'Taxonomys 1', 'ova-brw' ),
					'required' 		=> true
				]); ?>
            </td>
        </tr>
        <tr class="ova-row-sigular-name">
            <td class="label">
                <?php esc_html_e( 'Singular name', 'ova-brw' ); ?>
            </td>
            <td>
            	<?php ovabrw_wp_text_input([
					'name' 			=> 'singular_name',
					'placeholder' 	=> esc_html__( 'Taxonomy 1', 'ova-brw' ),
					'required' 		=> true
				]); ?>
            </td>
        </tr>
        <tr class="ova-row-label-frontend">
            <td class="label">
                <?php esc_html_e( 'Label frontend', 'ova-brw' ); ?>
            </td>
            <td>
            	<?php ovabrw_wp_text_input([
					'name' 			=> 'label_frontend',
					'placeholder' 	=> esc_html__( 'Label', 'ova-brw' )
				]); ?>
            </td>
        </tr>
        <tr class="row-required">
            <td>&nbsp;</td>
            <td class="check-box">
            	<?php ovabrw_wp_text_input([
            		'type' 		=> 'checkbox',
            		'id' 		=> 'ova_enable',
					'name' 		=> 'enabled',
					'value' 	=> 'on',
					'checked' 	=> true
				]); ?>
                <label for="ova_enable">
                    <?php esc_html_e( 'Enable', 'ova-brw' ); ?>
                </label>
                <br/>
            </td>                     
            <td class="label"></td>
        </tr>
        <tr class="row-show-listing">
            <td>&nbsp;</td>
            <td class="check-box">
            	<?php ovabrw_wp_text_input([
            		'type' 		=> 'checkbox',
            		'id' 		=> 'show_listing',
					'name' 		=> 'show_listing',
					'value' 	=> 'on',
					'checked' 	=> true
				]); ?>
                <label for="show_listing">
                    <?php esc_html_e( 'Show in Listing', 'ova-brw' ); ?>
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