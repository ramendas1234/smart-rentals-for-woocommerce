<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get term ID
$term_id = $term->term_id;

// Display rental
$display_rental = $this->get_meta( $term_id, 'cat_dis' );

// Price format for single page
$single_price_format_type 	= $this->get_meta( $term_id, 'select_single_price_format' );
$single_price_format_new 	= $this->get_meta( $term_id, 'single_new_price_format' );

// Price format for archive page
$archive_price_format_type 	= $this->get_meta( $term_id, 'select_archive_price_format' );
$archive_price_format_new 	= $this->get_meta( $term_id, 'archive_new_price_format' );

// All custom taxonomies
$taxonomies = ovabrw_replace( '\\', '', ovabrw_get_option( 'custom_taxonomy', [] ) );

// Get custom taxonomies
$custom_taxonomies = $this->get_meta( $term_id, 'custom_tax' );
if ( !ovabrw_array_exists( $custom_taxonomies ) ) $custom_taxonomies = [];

// Sort custom taxonomies
$sort_taxonomies = [];

if ( ovabrw_array_exists( $custom_taxonomies ) ) {
	foreach ( $custom_taxonomies as $tax_slug ) {
		if ( array_key_exists( $tax_slug, $taxonomies ) ) {
			$sort_taxonomies[$tax_slug] = $taxonomies[$tax_slug];

			// Remove slug in taxonomies
			unset( $taxonomies[$tax_slug] );
		}
	}
}

// Merge taxonomies
$sort_taxonomies = array_merge( $sort_taxonomies, $taxonomies );

// All custom checkout fields
$cckf = ovabrw_replace( '\\', '', ovabrw_get_option( 'booking_form', [] ) );

// Custom checkout fields type
$cckf_type = $this->get_meta( $term_id, 'choose_custom_checkout_field' );

// Get custom checkout fields
$cckf_term = $this->get_meta( $term_id, 'custom_checkout_field' );
if ( !ovabrw_array_exists( $cckf_term ) ) $cckf_term = [];

// Sort custom checkout fields
$sort_cckf = [];

if ( ovabrw_array_exists( $cckf_term ) ) {
	foreach ( $cckf_term as $cckf_slug ) {
		if ( array_key_exists( $cckf_slug, $cckf ) ) {
			$sort_cckf[$cckf_slug] = $cckf[$cckf_slug];

			// Remove slug in custom checkout fields
			unset( $cckf[$cckf_slug] );
		}
	}
}

// Merge taxonomies
$sort_cckf = array_merge( $sort_cckf, $cckf );

// Get all specifications
$specifications = ovabrw_replace( '\\', '', ovabrw_get_option( 'specifications', [] ) );

// Specification type
$specification_type = $this->get_meta( $term_id, 'choose_specifications' );

// Get specification for term
$specification_term = $this->get_meta( $term_id, 'specifications' );
if ( !ovabrw_array_exists( $specification_term ) ) $specification_term = [];

// Sort specifications
$sort_specifications = [];

if ( ovabrw_array_exists( $specification_term ) ) {
	foreach ( $specification_term as $specification_slug ) {
		if ( array_key_exists( $specification_slug, $specifications ) ) {
			$sort_specifications[$specification_slug] = $specifications[$specification_slug];

			// Remove slug in specifications
			unset( $specifications[$specification_slug] );
		}
	}
}

// Merge specifications
$sort_specifications = array_merge( $sort_specifications, $specifications );

// Show locations
$show_locations = $this->get_meta( $term_id, 'show_loc_booking_form' );
if ( !ovabrw_array_exists( $show_locations ) ) $show_locations = [];

// Get label pick-up, drop-off dates
$label_pickup_date   = $this->get_meta( $term_id, 'lable_pickup_date' );
$lable_dropoff_date  = $this->get_meta( $term_id, 'lable_dropoff_date' );

// Get product template
$global_template 	= ovabrw_get_setting( 'template_elementor_template', 'default' );
$product_template 	= $this->get_meta( $term_id, 'product_templates' );
$templates 			= get_posts([
	'post_type' 	=> 'elementor_library',
	'meta_key' 		=> '_elementor_template_type',
	'meta_value' 	=> 'page',
	'numberposts'   => -1
]);

$list_templates             = [];
$list_templates['global']  	= esc_html__( 'Global Setting', 'ova-brw' );

if ( ovabrw_array_exists( $templates ) ) {
    foreach ( $templates as $template ) {
        if ( $template->ID == $global_template ) continue;
        $list_templates[$template->ID] = $template->post_title;
    }
} // End product template

// Card
$card_template = $this->get_meta( $term_id, 'card_template' );

?>

<!-- Display -->
<tr class="form-field">
    <th scope="row" valign="top">
        <label for="<?php ovabrw_meta_key( 'cat_dis', true ); ?>">
            <?php esc_html_e('Display', 'ova-brw'); ?>
        </label>
    </th>
    <td>
    	<?php ovabrw_wp_select_input([
	    	'id' 		=> ovabrw_meta_key( 'cat_dis' ),
	    	'name' 		=> ovabrw_meta_key( 'cat_dis' ),
	    	'value' 	=> $display_rental,
	    	'options' 	=> [
	    		'shop' 		=> esc_html__( 'Shop', 'ova-brw' ),
	    		'rental' 	=> esc_html__( 'Rental', 'ova-brw' )
	    	]
	    ]); ?>
    </td>
</tr>

<!-- Price Format -->
<tr class="form-field ovabrw-single-price-format">
	<th scope="row" valign="top">
      	<label <?php ovabrw_meta_key( 'select_single_price_format', true ); ?>>
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
    </th>
    <td>
    	<?php ovabrw_wp_select_input([
        	'id' 		=> ovabrw_meta_key( 'select_single_price_format' ),
        	'name' 		=> ovabrw_meta_key( 'select_single_price_format' ),
        	'value' 	=> $single_price_format_type,
        	'options' 	=> [
        		'global' 	=> esc_html__( 'Global setting', 'ova-brw' ),
        		'new' 		=> esc_html__( 'New format', 'ova-brw' )
        	]
        ]); ?>
    </td>
</tr>
<tr class="form-field single-price-format-wrap">
	<th scope="row" valign="top"></th>
    <td>
    	<?php ovabrw_wp_textarea([
    		'name' 			=> ovabrw_meta_key( 'single_new_price_format' ),
    		'value' 		=> $single_price_format_new,
    		'placeholder' 	=> esc_html__( 'Add new format', 'ova-brw' ),
    		'attrs' 		=> [
    			'rows' => 5,
    			'cols' => 40
    		]
    	]); ?>
    </td>
</tr>
<tr class="form-field ovabrw-archive-price-format">
	<th scope="row" valign="top">
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
    </th>
    <td>
    	<?php ovabrw_wp_select_input([
        	'id' 		=> ovabrw_meta_key( 'select_archive_price_format' ),
        	'name' 		=> ovabrw_meta_key( 'select_archive_price_format' ),
        	'value' 	=> $archive_price_format_type,
        	'options' 	=> [
        		'global' 	=> esc_html__( 'Global setting', 'ova-brw' ),
        		'new' 		=> esc_html__( 'New format', 'ova-brw' )
        	]
        ]); ?>
    </td>
</tr>
<tr class="form-field archive-price-format-wrap">
	<th scope="row" valign="top"></th>
    <td>
    	<?php ovabrw_wp_textarea([
    		'name' 			=> ovabrw_meta_key( 'archive_new_price_format' ),
    		'value' 		=> $archive_price_format_new,
    		'placeholder' 	=> esc_html__( 'Add new format', 'ova-brw' ),
    		'attrs' 		=> [
    			'rows' => 5,
    			'cols' => 40
    		]
    	]); ?>
    </td>
</tr>

<!-- Custom Taxonomies -->
<?php if ( 'yes' === ovabrw_get_setting( 'search_show_tax_depend_cat', 'yes' ) ): ?>
    <tr class="form-field">
        <th scope="row" valign="top">
            <label for="<?php ovabrw_meta_key( 'custom_tax', true ); ?>">
                <?php esc_html_e('Choose custom taxonomies', 'ova-brw'); ?>
            </label>
        </th>
        <td>
            <select
            	id="<?php ovabrw_meta_key( 'custom_tax', true ); ?>"
	            class="<?php ovabrw_meta_key( 'custom_tax_with_cat', true ); ?>"
	            name="<?php ovabrw_meta_key( 'custom_tax[]', true ); ?>"
	            data-placeholder="..."
	            multiple="multiple">
	            <?php foreach ( $sort_taxonomies as $slug => $field ):
	            	if ( !isset( $field['enabled'] ) || !$field['enabled'] ) continue;

	                $name = array_key_exists( 'name', $field ) ? $field['name'] : '';
	            ?>
	                <option value="<?php echo esc_attr( $slug ); ?>"<?php ovabrw_selected( $slug, $custom_taxonomies ); ?>>
	                    <?php echo esc_html( $name.' ('.$slug.')' ); ?>
	                </option>
	            <?php endforeach; ?>
            </select>
        </td>
    </tr>
<?php endif; ?>

<!-- Custom Checkout Field -->
<tr class="form-field choose_custom_checkout_field">
    <th scope="row" valign="top">
      <label for="<?php ovabrw_meta_key( 'choose_custom_checkout_field', true ); ?>">
        <?php esc_html_e('Choose custom checkout fields', 'ova-brw'); ?>
         <span>
        	<?php echo wc_help_tip( esc_html__( 'All: The all custom checkout fields will display', 'ova-brw' ), true ); ?>
        </span>
    </label>
    </th>
    <td>
    	<?php ovabrw_wp_select_input([
        	'id' 		=> ovabrw_meta_key( 'choose_custom_checkout_field' ),
        	'name' 		=> ovabrw_meta_key( 'choose_custom_checkout_field' ),
        	'value' 	=> $cckf_type,
        	'options' 	=> [
        		'all' 		=> esc_html__( 'All', 'ova-brw' ),
        		'special' 	=> esc_html__( 'Choose other fields', 'ova-brw' )
        	]
        ]); ?>
    </td>
</tr>
<tr class="form-field show_special_checkout_field">
    <th scope="row" valign="top"></th>
    <td>
        <select
        	id="<?php ovabrw_meta_key( 'custom_checkout_field', true ); ?>"
            class="<?php ovabrw_meta_key( 'custom_tax_with_cat', true ); ?>"
            name="<?php ovabrw_meta_key( 'custom_checkout_field[]', true ); ?>"
            data-placeholder="<?php esc_html_e( 'Choose other custom checkout fields', 'ova-brw' ) ?>"
            multiple="multiple">
	        <?php foreach ( $sort_cckf as $slug => $field ):
	        	if ( !isset( $field['enabled'] ) || !$field['enabled'] ) continue;

	            $label = array_key_exists( 'label', $field ) ? $field['label'] : '';
	        ?>
	            <option value="<?php echo esc_attr( $slug ); ?>"<?php ovabrw_selected( $slug, $cckf_term ); ?>>
	                <?php echo esc_html( $label.' ('.$slug.')' ); ?>
	            </option>
	        <?php endforeach; ?>
        </select>
    </td>
</tr>

<!-- Specifications -->
<tr class="form-field choose_specifications">
    <th scope="row" valign="top">
      <label for="<?php ovabrw_meta_key( 'choose_specifications', true ); ?>">
        <?php esc_html_e( 'Choose Specifications', 'ova-brw' ); ?>
        <span>
        	<?php echo wc_help_tip( esc_html__( 'All: The all specifications will display', 'ova-brw' ), true ); ?>
        </span>
    </label>
    </th>
    <td>
    	<?php ovabrw_wp_select_input([
        	'id' 		=> ovabrw_meta_key( 'choose_specifications' ),
        	'name' 		=> ovabrw_meta_key( 'choose_specifications' ),
        	'value' 	=> $specification_type,
        	'options' 	=> [
        		'all' 		=> esc_html__( 'All', 'ova-brw' ),
        		'special' 	=> esc_html__( 'Choose other fields', 'ova-brw' )
        	]
        ]); ?>
    </td>
</tr>
<tr class="form-field show_special_specifications">
    <th scope="row" valign="top"></th>
    <td>
        <select
        	id="<?php ovabrw_meta_key( 'specifications', true ); ?>"
        	name="<?php ovabrw_meta_key( 'specifications[]', true ); ?>"
        	class="<?php ovabrw_meta_key( 'custom_tax_with_cat', true ); ?>"
            data-placeholder="<?php esc_html_e( 'Choose other specifications', 'ova-brw' ); ?>"
        	multiple="multiple">
        	<?php foreach ( $sort_specifications as $slug => $field ):
            	if ( !isset( $field['enable'] ) || !$field['enable'] ) continue;

                $label = isset( $field['label'] ) ? $field['label'] : '';
            ?>
                <option value="<?php echo esc_attr( $slug ); ?>"<?php ovabrw_selected( $slug, $specification_term ); ?>>
                    <?php echo esc_html( $label.' ('.$slug.')' ); ?>
                </option>
        	<?php endforeach; ?>
        </select>
    </td>
</tr>

<!-- Show Location in booking form -->
<tr class="form-field">
    <th scope="row" valign="top">
        <label for="<?php ovabrw_meta_key( 'show_loc_booking_form', true ); ?>">
            <?php esc_html_e( 'Show Location in form', 'ova-brw' ); ?>
            <span>
            	<?php echo wc_help_tip( esc_html__( 'If Empty field will get value in WooCommerce >> Settings >> Booking & Rental >> Booking Details', 'ova-brw' ), true ); ?>	
            </span>
        </label>
    </th>
    <td>
    	<?php ovabrw_wp_select_input([
	        'id'        => ovabrw_meta_key( 'show_loc_booking_form' ),
	        'class'     => ovabrw_meta_key( 'custom_tax_with_cat' ),
	        'name'      => ovabrw_meta_key( 'show_loc_booking_form[]' ),
	        'value' 	=> $show_locations,
	        'options'   => [
	            'pickup_loc'    => esc_html__( 'Pick-up Location', 'ova-brw' ),
	            'dropoff_loc'   => esc_html__( 'Drop-off Location', 'ova-brw' )
	        ],
	        'multiple'  => true,
	        'attrs'     => [
	            'data-placeholder' => '...'
	        ]
	    ]); ?>
    </td>
</tr>

<!-- Label Pick-up Date -->
<tr class="form-field ovabrw_lable_pickup_date">
    <th>
        <label for="<?php ovabrw_meta_key( 'lable_pickup_date', true ); ?>">
        	<?php esc_html_e( 'Rename "Pick-up Date" title', 'ova-brw' ); ?>
        	<span>
            	<?php echo wc_help_tip( esc_html__( 'Example: Check-in', 'ova-brw' ), true ); ?>
            </span>		
        </label>
    </th>
    <td>
    	<?php ovabrw_wp_text_input([
	    	'id'           	=> ovabrw_meta_key( 'lable_pickup_date' ),
	    	'name'         	=> ovabrw_meta_key( 'lable_pickup_date' ),
	    	'value' 		=> $label_pickup_date,
	        'placeholder'  	=> esc_html__( 'Add new title', 'ova-brw' ),
	    	'attrs'        	=> [
	    		'size' => 40
	    	]
	    ]); ?>
    </td>
</tr>

<!-- Label Drop-off Date -->
<tr class="form-field ovabrw_lable_dropoff_date">
    <th>
        <label for="<?php ovabrw_meta_key( 'lable_dropoff_date', true ); ?>">
        	<?php esc_html_e( 'Rename "Drop-off Date" title', 'ova-brw' ); ?>
        	<span>
            	<?php echo wc_help_tip( esc_html__( 'Example: Check-out', 'ova-brw' ), true ); ?>
            </span>		
        </label>
    </th>
    <td>
    	<?php ovabrw_wp_text_input([
	    	'id'           	=> ovabrw_meta_key( 'lable_dropoff_date' ),
	    	'name' 	       	=> ovabrw_meta_key( 'lable_dropoff_date' ),
	    	'value' 		=> $lable_dropoff_date,
	        'placeholder'  	=> esc_html__( 'Add new title', 'ova-brw' ),
	    	'attrs'        	=> [
	    		'size' => 40
	    	]
	    ]); ?>
    </td>
</tr>

<!-- Product template -->
<tr class="form-field ovabrw_product_templates">
    <th>
        <label for="<?php ovabrw_meta_key( 'product_templates', true ); ?>">
        	<?php esc_html_e( 'Product template', 'ova-brw' ); ?>
        	<span>
        	 	<?php echo wc_help_tip( esc_html__( '- Global Setting: WooCommerce >> Settings >> Booking & Rental >> Product Details >> Product Template. <br/> - Other: Made in Templates of Elementor', 'ova-brw' ), true ); ?>
        	 </span>		
        </label>
    </th>
    <td>
        <select
        	id="<?php ovabrw_meta_key( 'product_templates', true ); ?>"
    		name="<?php ovabrw_meta_key( 'product_templates', true ); ?>">
    		<?php if ( ovabrw_array_exists( $list_templates ) ):
    			foreach ( $list_templates as $template_id => $template_title ):
    		?>
    			<option value="<?php echo esc_attr( $template_id ); ?>"<?php ovabrw_selected( $template_id, $product_template ); ?>>
            		<?php echo esc_html( $template_title ); ?>
            	</option>
    		<?php endforeach;
    		endif; ?>
        </select>
    </td>
</tr>

<!-- Card template -->
<?php if ( ovabrw_global_typography() ): ?>
   	<tr class="form-field ovabrw_card_template">
        <th>
            <label for="<?php ovabrw_meta_key( 'card_template', true ); ?>">
            	<?php esc_html_e( 'Card template', 'ova-brw' ); ?>
            </label>
            <span>
            	<?php 
            	echo wc_help_tip( esc_html__( '- Card template is product template that display in listing product page. <br/> - Global Setting: WooCommerce >> Settings >> Booking & Rental >> Typography & Color >> Card', 'ova-brw' ), true );
            	?>
            </span>
        </th>
        <td>
            <select
            	id="<?php ovabrw_meta_key( 'card_template', true ); ?>"
        		name="<?php ovabrw_meta_key( 'card_template', true ); ?>">
                <option value="">
                    <?php esc_html_e( 'Global Setting', 'ova-brw' ); ?>
                </option>
                <?php foreach ( $this->card_templates as $card => $label ): ?>
                	<option value="<?php echo esc_attr( $card ); ?>"<?php ovabrw_selected( $card_template, $card ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </td>
    </tr>
<?php endif;