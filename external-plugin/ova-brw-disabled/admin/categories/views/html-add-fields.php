<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get all custom taxonomies
$taxonomies = ovabrw_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

// Get all custom checkout fields
$cckf = ovabrw_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

// Get all specifications
$specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

// Global template
$global_template = ovabrw_get_setting( 'template_elementor_template', 'default' );

// Get templates from elementor
$templates = get_posts([
	'post_type' 	=> 'elementor_library',
	'meta_key' 		=> '_elementor_template_type',
	'meta_value' 	=> 'page',
	'numberposts'   => -1
]);

?>
<!-- Display -->
<div class="form-field">
    <label for="<?php ovabrw_meta_key( 'cat_dis', true ); ?>">
    	<?php esc_html_e( 'Display', 'ova-brw' ); ?>
    </label>
    <?php ovabrw_wp_select_input([
    	'id' 		=> ovabrw_meta_key( 'cat_dis' ),
    	'name' 		=> ovabrw_meta_key( 'cat_dis' ),
    	'options' 	=> [
    		'shop' 		=> esc_html__( 'Shop', 'ova-brw' ),
    		'rental' 	=> esc_html__( 'Rental', 'ova-brw' )
    	]
    ]); ?>
</div>

<!-- Display Price In Format -->
<div class="form-field">
    <div class="ovabrw-single-price-format">
        <label for="<?php ovabrw_meta_key( 'select_single_price_format', true ); ?>">
            <?php esc_html_e( 'Display Price In Format (product template)', 'ova-brw' ); ?>
            <span>
            	<?php echo wc_help_tip( __( 'For example: [regular_price] / [unit]<br>
                You can insert text or HTML<br>
                Use shortcodes:<br>
                <em>[unit]</em>: Display Day or Night or Hour or Km or Mi<br>
                <em>[regular_price]</em>: Display regular price by day<br>
                <em>[hour_price]</em>: Display regular price by hour<br>
                <em>[min_daily_price]</em>: Display minimum daily price<br>
                <em>[max_daily_price]</em>: Display maximum daily price<br>
                <em>[min_package_price]</em>: Display minimum package price (rental type: Period)<br>
                <em>[max_package_price]</em>: Display maximum package price (rental type: Period)<br>
                <em>[min_location_price]</em>: Display minimum location price (rental type: Transportation)<br>
                <em>[max_location_price]</em>: Display maximum location price (rental type: Transportation)<br>
                <em>[min_price]</em>: Display minimum timeslot price (rental type: Appointment)<br>
                <em>[max_price]</em>: Display maximum timeslot price (rental type: Appointment)', 'ova-brw' ), true ); ?>
            </span>
        </label>
        <?php ovabrw_wp_select_input([
        	'id' 		=> ovabrw_meta_key( 'select_single_price_format' ),
        	'name' 		=> ovabrw_meta_key( 'select_single_price_format' ),
        	'options' 	=> [
        		'global' 	=> esc_html__( 'Global setting', 'ova-brw' ),
        		'new' 		=> esc_html__( 'New format', 'ova-brw' )
        	]
        ]); ?>
    </div>
    <div class="form-field single-price-format-wrap" style="display: none;">
    	<?php ovabrw_wp_textarea([
    		'name' 			=> ovabrw_meta_key( 'single_new_price_format' ),
    		'placeholder' 	=> esc_html__( 'Add new format', 'ova-brw' ),
    		'attrs' 		=> [
    			'rows' => 5,
    			'cols' => 40
    		]
    	]); ?>
    </div>
</div>
<div class="form-field">
    <div class="ovabrw-archive-price-format">
        <label for="<?php ovabrw_meta_key( 'select_archive_price_format', true ); ?>">
            <?php esc_html_e( 'Display Price In Format (card template)', 'ova-brw' ); ?>
            <span>
            	<?php echo wc_help_tip( __( 'For example: [regular_price] / [unit]<br>
                You can insert text or HTML<br>
                Use shortcodes:<br>
                <em>[unit]</em>: Display Day or Night or Hour or Km or Mi<br>
                <em>[regular_price]</em>: Display regular price by day<br>
                <em>[hour_price]</em>: Display regular price by hour<br>
                <em>[min_daily_price]</em>: Display minimum daily price<br>
                <em>[max_daily_price]</em>: Display maximum daily price<br>
                <em>[min_package_price]</em>: Display minimum package price (rental type: Period)<br>
                <em>[max_package_price]</em>: Display maximum package price (rental type: Period)<br>
                <em>[min_location_price]</em>: Display minimum location price (rental type: Transportation)<br>
                <em>[max_location_price]</em>: Display maximum location price (rental type: Transportation)<br>
                <em>[min_price]</em>: Display minimum timeslot price (rental type: Appointment)<br>
                <em>[max_price]</em>: Display maximum timeslot price (rental type: Appointment)', 'ova-brw' ), true ); ?>
            </span>
        </label>
        <?php ovabrw_wp_select_input([
        	'id' 		=> ovabrw_meta_key( 'select_archive_price_format' ),
        	'name' 		=> ovabrw_meta_key( 'select_archive_price_format' ),
        	'options' 	=> [
        		'global' 	=> esc_html__( 'Global setting', 'ova-brw' ),
        		'new' 		=> esc_html__( 'New format', 'ova-brw' )
        	]
        ]); ?>
    </div>
    <div class="form-field archive-price-format-wrap" style="display: none;">
    	<?php ovabrw_wp_textarea([
    		'name' 			=> ovabrw_meta_key( 'archive_new_price_format' ),
    		'placeholder' 	=> esc_html__( 'Add new format', 'ova-brw' ),
    		'attrs' 		=> [
    			'rows' => 5,
    			'cols' => 40
    		]
    	]); ?>
    </div>
</div>

<!-- Custom Taxonomies -->
<?php if ( 'yes' === ovabrw_get_setting( 'search_show_tax_depend_cat', 'yes' ) ): ?>
    <div class="form-field">
        <label for="<?php ovabrw_meta_key( 'custom_tax', true ); ?>">
            <?php esc_html_e( 'Choose custom taxonomies', 'ova-brw' ); ?>
        </label>
        <select
            id="<?php ovabrw_meta_key( 'custom_tax', true ); ?>"
            class="<?php ovabrw_meta_key( 'custom_tax_with_cat', true ); ?>"
            name="<?php ovabrw_meta_key( 'custom_tax[]', true ); ?>"
            data-placeholder="..."
            multiple="multiple">
            <?php if ( ovabrw_array_exists( $taxonomies ) ):
                foreach ( $taxonomies as $slug => $field ):
                    $name = array_key_exists( 'name', $field ) ? $field['name'] : '';
    		?>
                    <option value="<?php echo esc_attr( $slug ); ?>">
                        <?php echo esc_html( $name.' ('.$slug.')' ); ?>
                    </option>
            <?php endforeach;
        	endif; ?>
        </select>
    </div>
<?php endif; ?>

<!-- Custom Checkout Fields -->
<div class="form-field">
    <div class="choose_custom_checkout_field">
        <label for="<?php ovabrw_meta_key( 'choose_custom_checkout_field', true ); ?>">
            <?php esc_html_e( 'Choose custom checkout fields', 'ova-brw' ); ?>
            <span>
            	<?php echo wc_help_tip( esc_html__( 'All: The all custom checkout fields will display', 'ova-brw' ), true ); ?>
            </span>
        </label>
        <?php ovabrw_wp_select_input([
        	'id' 		=> ovabrw_meta_key( 'choose_custom_checkout_field' ),
        	'name' 		=> ovabrw_meta_key( 'choose_custom_checkout_field' ),
        	'options' 	=> [
        		'all' 		=> esc_html__( 'All', 'ova-brw' ),
        		'special' 	=> esc_html__( 'Choose other fields', 'ova-brw' )
        	]
        ]); ?>
    </div>
    <div id="special_cus_fields" class="show_special_checkout_field">
        <br>
        <select
            id="<?php ovabrw_meta_key( 'custom_checkout_field', true ); ?>"
            class="<?php ovabrw_meta_key( 'custom_tax_with_cat', true ); ?>"
            name="<?php ovabrw_meta_key( 'custom_checkout_field[]', true ); ?>"
            data-placeholder="<?php esc_html_e( 'Choose other custom checkout fields', 'ova-brw' ) ?>"
            multiple="multiple">
            <?php if ( ovabrw_array_exists( $cckf ) ):
                foreach ( $cckf as $slug => $field ):
                    $label = array_key_exists( 'label', $field ) ? $field['label'] : '';
            ?>
                    <option value="<?php echo esc_attr( $slug ); ?>">
                        <?php echo esc_html( $label.' ('.$slug.')' ); ?>
                    </option>
            <?php endforeach; endif; ?>
        </select>
    </div>
</div>

<!-- Specifications -->
<div class="form-field">
    <div class="choose_specifications">
        <label for="<?php ovabrw_meta_key( 'choose_specifications', true ); ?>">
            <?php esc_html_e( 'Choose Specifications', 'ova-brw' ); ?>
            <span>
            	<?php echo wc_help_tip( esc_html__( 'All: The all specifications will display', 'ova-brw' ), true ); ?>
            </span>
        </label>
        <?php ovabrw_wp_select_input([
        	'id' 		=> ovabrw_meta_key( 'choose_specifications' ),
        	'name' 		=> ovabrw_meta_key( 'choose_specifications' ),
        	'options' 	=> [
        		'all' 		=> esc_html__( 'All', 'ova-brw' ),
        		'special' 	=> esc_html__( 'Choose other fields', 'ova-brw' )
        	]
        ]); ?>
    </div>
    <div id="show_special_specifications" class="show_special_specifications">
        <br>
        <select
        	id="<?php ovabrw_meta_key( 'specifications', true ); ?>"
        	name="<?php ovabrw_meta_key( 'specifications[]', true ); ?>"
        	class="<?php ovabrw_meta_key( 'custom_tax_with_cat', true ); ?>"
            data-placeholder="<?php esc_html_e( 'Choose other specifications', 'ova-brw' ); ?>"
        	multiple="multiple">
            <?php if ( ovabrw_array_exists( $specifications ) ):
                foreach ( $specifications as $name => $field ):
                	if ( !ovabrw_get_meta_data( 'enable', $field ) ) continue;

                    $label = isset( $field['label'] ) ? $field['label'] : '';
                ?>
                    <option value="<?php echo esc_attr( $name ); ?>">
                        <?php echo esc_html( $label ); ?>
                    </option>
            <?php endforeach;
        	endif; ?>
        </select>
    </div>
</div>

<!-- Show Location in booking form -->
<div class="form-field">
    <label for="<?php ovabrw_meta_key( 'show_loc_booking_form', true ); ?>">
        <?php esc_html_e( 'Show Location in form', 'ova-brw' ); ?>
        <span>
        	<?php echo wc_help_tip( esc_html__( 'If Empty field will get value in WooCommerce >> Settings >> Booking & Rental >> Booking Details', 'ova-brw' ), true ) ?>	
        </span>
    </label>
    <?php ovabrw_wp_select_input([
        'id'        => ovabrw_meta_key( 'show_loc_booking_form' ),
        'class'     => ovabrw_meta_key( 'custom_tax_with_cat' ),
        'name'      => ovabrw_meta_key( 'show_loc_booking_form[]' ),
        'options'   => [
            'pickup_loc'    => esc_html__( 'Pick-up Location', 'ova-brw' ),
            'dropoff_loc'   => esc_html__( 'Drop-off Location', 'ova-brw' )
        ],
        'multiple'  => true,
        'attrs'     => [
            'data-placeholder' => '...'
        ]
    ]); ?>
</div>

<!-- Label Pick-up Date -->
<div class="form-field">
    <label for="<?php ovabrw_meta_key( 'lable_pickup_date', true ); ?>">
    	<?php esc_html_e( 'Rename "Pick-up Date" title', 'ova-brw' ); ?>
    	<span>
        	<?php echo wc_help_tip( esc_html__( 'Example: Check-in', 'ova-brw' ), true ); ?>
        </span>
    </label>
    <?php ovabrw_wp_text_input([
    	'id'           => ovabrw_meta_key( 'lable_pickup_date' ),
    	'name'         => ovabrw_meta_key( 'lable_pickup_date' ),
        'placeholder'  => esc_html__( 'Add new title', 'ova-brw' ),
    	'attrs'        => [
    		'size' => 40
    	]
    ]); ?>
</div>

<!-- Label Drop-off Date -->
<div class="form-field">
    <label for="<?php ovabrw_meta_key( 'lable_dropoff_date', true ); ?>">
    	<?php esc_html_e( 'Rename "Drop-off Date" Title', 'ova-brw' ); ?>
    	<span>
        	<?php echo wc_help_tip( esc_html__( 'Example: Check-out', 'ova-brw' ), true ); ?>
        </span>
    </label>
    <?php ovabrw_wp_text_input([
    	'id'           => ovabrw_meta_key( 'lable_dropoff_date' ),
    	'name' 	       => ovabrw_meta_key( 'lable_dropoff_date' ),
        'placeholder'  => esc_html__( 'Add new title', 'ova-brw' ),
    	'attrs'        => [
    		'size' => 40
    	]
    ]); ?>
</div>

<!-- Product Templates -->
<div class="form-field">
    <label for="<?php ovabrw_meta_key( 'product_templates', true ); ?>">
    	<?php esc_html_e( 'Product template', 'ova-brw' ); ?>
    	<span>
    	 	<?php echo wc_help_tip( esc_html__( '- Global Setting: WooCommerce >> Settings >> Booking & Rental >> Product Details >> Product Template. <br/> - Other: Made in Templates of Elementor', 'ova-brw' ), true ); ?>
    	</span>
    </label>
    <select
    	id="<?php ovabrw_meta_key( 'product_templates', true ); ?>"
    	name="<?php ovabrw_meta_key( 'product_templates', true ); ?>">
    	<option value="">
    		<?php esc_html_e( 'Select template', 'ova-brw' ); ?>
    	</option>
        <?php if ( ovabrw_array_exists( $templates ) ):
        	foreach ( $templates as $template ):
        		if ( $template->ID == $global_template ) continue;
        ?>
        	<option value="<?php echo esc_attr( $template->ID ); ?>">
        		<?php echo esc_html( $template->post_title ); ?>
        	</option>
    	<?php endforeach;
    	endif; ?>
    </select>
</div>

<!-- Card Template -->
<?php if ( ovabrw_global_typography() ): ?>
    <div class="form-field">
        <label for="<?php ovabrw_meta_key( 'card_template', true ); ?>">
        	<?php esc_html_e( 'Card Template', 'ova-brw' ); ?>
        	<span>
            	<?php echo wc_help_tip( esc_html__( '- Card template is product template that display in listing product page. <br/> - Global Setting: WooCommerce >> Settings >> Booking & Rental >> Typography & Color >> Card', 'ova-brw' ), true );
            	?>
            </span>
        </label>
        <select
        	id="<?php ovabrw_meta_key( 'card_template', true ); ?>"
        	name="<?php ovabrw_meta_key( 'card_template', true ); ?>">
            <option value="">
                <?php esc_html_e( 'Global Setting', 'ova-brw' ); ?>
            </option>
	        <?php foreach ( $this->card_templates as $card => $label ): ?>
	        	<option value="<?php echo esc_attr( $card ); ?>">
	                <?php echo esc_html( $label ); ?>
	            </option>
	        <?php endforeach; ?>
        </select>
        <br>
    </div>
<?php endif;