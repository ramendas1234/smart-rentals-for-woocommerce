<?php defined( 'ABSPATH' ) || exit();

// Get card template
$card = ovabrw_get_meta_data( 'card', $args );

// Columns
$columns = ovabrw_get_meta_data( 'columns', $args, 'column4' );

// Show time
$show_time = ovabrw_get_meta_data( 'show_time', $args );
if ( 'yes' == $show_time ) {
	$show_time = true;
} else {
	$show_time = false;
}

// Custom taxonomies
$custom_taxonomies = ovabrw_get_meta_data( 'list_taxonomy_custom', $args );

// Include, Exclude category
$exclude_id = ovabrw_get_meta_data( 'category_not_in', $args );
$include_id = ovabrw_get_meta_data( 'category_in', $args );

// Label product
$label_name = ovabrw_get_meta_data( 'field_name', $args, esc_html__( 'Product Name', 'ova-brw' ) );

// Label category
$label_category = ovabrw_get_meta_data( 'field_category', $args, esc_html__( 'Select Category', 'ova-brw' ) );

// Label pick-up date
$label_pickup_date = ovabrw_get_meta_data( 'field_pickup_date', $args, esc_html__( 'Pick-up Date', 'ova-brw' ) );

// Label drop-off date
$label_dropoff_date = ovabrw_get_meta_data( 'field_dropoff_date', $args, esc_html__( 'Drop-off Date', 'ova-brw' ) );

// Label attribute
$label_attribute = ovabrw_get_meta_data( 'field_attribute', $args, esc_html__( 'Attribute', 'ova-brw' ) );

// Label guests
$label_guests = ovabrw_get_meta_data( 'field_guest', $args, esc_html__( 'Guests', 'ova-brw' ) );

// Label tags
$label_tags = ovabrw_get_meta_data( 'field_tags', $args, esc_html__( 'Tags', 'ova-brw' ) );

// Label quantity
$label_quantity = ovabrw_get_meta_data( 'field_quantity', $args, esc_html__( 'Quantity', 'ova-brw' ) );

// Label adults
$label_adults = ovabrw_get_meta_data( 'adults_label', $args, esc_html__( 'Adults', 'ova-brw' ) );

// Label children
$label_children = ovabrw_get_meta_data( 'children_label', $args, esc_html__( 'Children', 'ova-brw' ) );

// Label babies
$label_babies = ovabrw_get_meta_data( 'babies_label', $args, esc_html__( 'Babies', 'ova-brw' ) );

// Placeholder
$placeholder_name 		= ovabrw_get_meta_data( 'placeholder_name', $args, $label_name );
$placeholder_category 	= ovabrw_get_meta_data( 'placeholder_category', $args, $label_category );
$placeholder_guests 	= ovabrw_get_meta_data( 'placeholder_guest', $args );
$placeholder_attribute 	= ovabrw_get_meta_data( 'placeholder_attribute', $args, $label_attribute );
$placeholder_tags 		= ovabrw_get_meta_data( 'placeholder_tags', $args, $label_tags );

// Date format
$date_format = OVABRW()->options->get_date_format();

// Time format
$time_format = OVABRW()->options->get_time_format();

// Minimum number of guests
$min_guests = ovabrw_get_meta_data( 'min_guests', $args, 1 );

// Maximum number of guests
$max_guests = ovabrw_get_meta_data( 'max_guests', $args );

// Total number of guests
$numberof_guests = 0;

// Minimum number of adults
$min_adults = ovabrw_get_meta_data( 'min_adults', $args, 0 );

// Maximum number of adults
$max_adults = ovabrw_get_meta_data( 'max_adults', $args );

// Number of adults
$numberof_adults = (int)sanitize_text_field( ovabrw_get_meta_data( 'adults', $_GET ) );
if ( !$numberof_adults ) {
	$numberof_adults = (int)ovabrw_get_meta_data( 'default_adult_number', $args, 1 );
}

// Update total number of guests
$numberof_guests += (int)$numberof_adults;

// Children
if ( apply_filters( OVABRW_PREFIX.'show_children', true ) ) {
	// Minimum number of children
    $min_children = ovabrw_get_meta_data( 'min_children', $args, 0 );

    // Maximun number of children
    $max_children = ovabrw_get_meta_data( 'max_children', $args );

    // Number of children
    $numberof_children = (int)sanitize_text_field( ovabrw_get_meta_data( 'children', $_GET ) );
    if ( !$numberof_children ) {
    	$numberof_children = ovabrw_get_meta_data( 'default_children_number', $args, 0 );
    }

    // Update total number of guests
    $numberof_guests += (int)$numberof_children;
} // END if

// Babies
if ( apply_filters( OVABRW_PREFIX.'show_children', true ) ) {
	// Minimum number of babies
	$min_babies = ovabrw_get_meta_data( 'min_babies', $args, 0 );

	// Maximum number of babies
    $max_babies = ovabrw_get_meta_data( 'max_babies', $args );
    
    // Number of babies
    $numberof_babies = (int)sanitize_text_field( ovabrw_get_meta_data( 'babies', $_GET ) );
    if ( !$numberof_babies ) {
    	$numberof_babies = (int)ovabrw_get_meta_data( 'default_babies_number', $args, 0 );
    }

    // Update total number of guests
    $numberof_guests += (int)$numberof_babies;
}

// Default category
$default_category = sanitize_text_field( ovabrw_get_meta_data( 'default_cat', $args ) );

// Result URL
$result_url = ovabrw_get_meta_data( 'result_url', $args );

// Action
$action = home_url();
if ( $result_url ) $action = $result_url;

?>
<div class="ovabrw-search-hotel ovabrw_wd_search">
	<form class="product-search-form <?php echo esc_attr( $columns ); ?>" method="GET" action="<?php echo esc_url( $action ); ?>" autocomplete="off" autocapitalize="none">
		<?php ovabrw_text_input([
        	'type' 	=> 'hidden',
        	'name' 	=> 'ovabrw_search_url',
        	'value' => $action
        ]); ?>
		<div class="product-search-content wrap_content <?php echo esc_attr( $columns ); ?>">
			<?php for ( $i = 1; $i <= 8; $i++ ):
				$key = 'field_'.esc_attr( $i );

				switch ( $args[$key] ) {
					case 'name': ?>
						<div class="label_search ova-product-name">
							<label class="field-label">
								<?php echo esc_html( $label_name ); ?>
							</label>
							<?php ovabrw_text_input([
								'type' 			=> 'text',
								'name' 			=> 'product_name',
								'placeholder' 	=> $placeholder_name
							]); ?>
						</div>
					<?php break;
					case 'category': ?>
						<div class="label_search ova-category">
							<label class="field-label">
								<?php echo esc_html( $label_category ); ?>
							</label>
							<?php echo OVABRW()->options->get_html_dropdown_categories( $default_category, '', $exclude_id, $placeholder_category, $include_id ); ?>
						</div>
					<?php break;
					case 'pickup_date': ?>
						<div class="label_search ova-pickup-date">
							<label class="field-label">
								<?php echo esc_html( $label_pickup_date ); ?>
							</label>
							<div class="input-with-icon" style="position: relative;">
								<?php ovabrw_text_input([
									'type' 		=> 'text',
									'id' 		=> ovabrw_unique_id( 'ovabrw_pickup_date' ),
									'class' 	=> 'ovabrw_start_date',
									'name' 		=> 'pickup_date',
									'data_type' => $show_time ? 'datetimepicker-start' : 'datepicker-start',
									'attrs' 	=> [
										'data-rental-type' 	=> 'hotel',
										'data-locale-one' 	=> esc_html__( 'night', 'ova-brw' ),
										'data-locale-other' => esc_html__( 'nights', 'ova-brw' )
									]
								]); ?>
								<i class="brwicon-calendar"></i>
							</div>
						</div>
					<?php break;
					case 'dropoff_date': ?>
						<div class="label_search ova-dropoff-date">
							<label class="field-label">
								<?php echo esc_html( $label_dropoff_date ); ?>
							</label>
							<div class="input-with-icon" style="position: relative;">
								<?php ovabrw_text_input([
									'type' 		=> 'text',
									'id' 		=> ovabrw_unique_id( 'ovabrw_pickoff_date' ),
									'class' 	=> 'ovabrw_end_date',
									'name' 		=> 'dropoff_date',
									'data_type' => $show_time ? 'datetimepicker-end' : 'datepicker-end'
								]); ?>
								<i class="brwicon-calendar"></i>
							</div>
						</div>
					<?php break;
					case 'guest': ?>
				        <div class="label_search ovabrw-wrapper-guestspicker">
				        	<label class="field-label">
				        		<?php echo esc_html( $label_guests ); ?>
				        	</label>
					        <div class="ovabrw-guestspicker">
					            <div class="guestspicker">
					                <span class="gueststotal">
					                	<?php echo esc_html( $numberof_guests ); ?>
					                </span>
					                <?php if ( $placeholder_guests ): ?>
						             	<?php echo esc_html( $placeholder_guests ); ?>
						         	<?php endif; ?>
					            </div>
					        </div>
					        <div class="ovabrw-gueste-error"></div>
					        <div class="ovabrw-guestspicker-content">
					            <div class="guests-buttons">
					                <div class="guests-label">
					                    <label>
					                    	<?php echo esc_html( $label_adults ); ?>
					                    </label>
					                </div>
					                <div class="guests-button">
					                    <div class="guests-icon minus">
					                        <span class="flaticon flaticon-substract"></span>
					                    </div>
					                    <?php ovabrw_text_input([
					                    	'type' 		=> 'text',
					                    	'class' 	=> 'ovabrw_adults',
					                    	'name' 		=> 'adults',
					                    	'value' 	=> $numberof_adults,
					                    	'required' 	=> true,
					                    	'readonly' 	=> true
					                    ]); ?>
					                    <div class="guests-icon plus">
					                        <span class="flaticon flaticon-add"></span>
					                    </div>
					                    <?php ovabrw_text_input([
					                    	'type' 	=> 'hidden',
					                    	'name' 	=> 'ovabrw_min_adults',
					                    	'value' => $min_adults
					                    ]); ?>
					                    <?php ovabrw_text_input([
					                    	'type' 	=> 'hidden',
					                    	'name' 	=> 'ovabrw_max_adults',
					                    	'value' => $max_adults ? $max_adults : ''
					                    ]); ?>
					                </div>
					            </div>
					            <?php if ( apply_filters( OVABRW_PREFIX.'show_children', true ) ): ?>
					                <div class="guests-buttons">
					                    <div class="guests-label">
					                        <label>
					                        	<?php echo esc_html( $label_children ); ?>
					                        </label>
					                    </div>
					                    <div class="guests-button">
					                        <div class="guests-icon minus">
					                            <span class="flaticon flaticon-substract"></span>
					                        </div>
					                        <?php ovabrw_text_input([
					                        	'type' 		=> 'text',
					                        	'class' 	=> 'ovabrw_children',
					                        	'name' 		=> 'children',
					                        	'value' 	=> $numberof_children,
					                        	'required' 	=> true,
					                        	'readonly' 	=> true
					                        ]); ?>
					                        <div class="guests-icon plus">
					                            <span class="flaticon flaticon-add"></span>
					                        </div>
					                        <?php ovabrw_text_input([
					                        	'type' 	=> 'hidden',
					                        	'name' 	=> 'ovabrw_min_children',
					                        	'value' => $min_children
					                        ]); ?>
					                        <?php ovabrw_text_input([
					                        	'type' 	=> 'hidden',
					                        	'name' 	=> 'ovabrw_max_children',
					                        	'value' => $max_children ? $max_children : ''
					                        ]); ?>
					                    </div>
					                </div>
					            <?php endif; ?>
					            <?php if ( apply_filters( OVABRW_PREFIX.'show_babies', true ) ): ?>
					                <div class="guests-buttons">
					                    <div class="guests-label">
					                        <label>
					                        	<?php echo esc_html( $label_babies ); ?>
					                        </label>  
					                    </div>
					                    <div class="guests-button">
					                        <div class="guests-icon minus">
					                            <span class="flaticon flaticon-substract"></span>
					                        </div>
					                        <?php ovabrw_text_input([
					                        	'type' 		=> 'text',
					                        	'class' 	=> 'ovabrw_babies',
					                        	'name' 		=> 'babies',
					                        	'value' 	=> $numberof_babies,
					                        	'required' 	=> true,
					                        	'readonly' 	=> true
					                        ]); ?>
					                        <div class="guests-icon plus">
					                            <span class="flaticon flaticon-add"></span>
					                        </div>
					                        <?php ovabrw_text_input([
					                        	'type' 		=> 'hidden',
					                        	'name' 		=> 'ovabrw_min_babies',
					                        	'value' 	=> $min_babies
					                        ]); ?>
					                        <?php ovabrw_text_input([
					                        	'type' 		=> 'hidden',
					                        	'name' 		=> 'ovabrw_max_babies',
					                        	'value' 	=> $max_babies ? $max_babies : ''
					                        ]); ?>
					                    </div>
					                </div>
					            <?php endif; ?>
					            <?php ovabrw_text_input([
					            	'type' 	=> 'hidden',
					            	'name' 	=> 'ovabrw_min_guests',
					            	'value' => $min_guests
					            ]); ?>
					            <?php ovabrw_text_input([
					            	'type' 	=> 'hidden',
					            	'name' 	=> 'ovabrw_max_guests',
					            	'value' => $max_guests
					            ]); ?>
					        </div>
					    </div>
					<?php break;
					case 'attribute':
						$data_html_attr = OVABRW()->options->get_html_dropdown_attributes( $placeholder_attribute );

						if ( $data_html_attr['html_attr'] ): ?>
							<div class="label_search ova-attribute ovabrw_search">
								<label class="field-label">
									<?php echo esc_html( $label_attribute ); ?>
								</label>
								<?php echo $data_html_attr['html_attr']; ?>
							</div>
						<?php endif;

						if ( $data_html_attr['html_attr_value'] ) {
							echo $data_html_attr['html_attr_value'];
						}

						break;
					case 'quantity': ?>
						<div class="label_search ova-quantity">
							<label class="field-label">
								<?php echo esc_html( $label_quantity ); ?>
							</label>
							<div class="quantity-button">
								<?php ovabrw_text_input([
									'type' 	=> 'text',
									'id' 	=> 'ovabrw_quantity',
									'class' => 'ovabrw_quantity',
									'name' 	=> 'quantity',
									'value' => 1,
									'attrs' => [
										'min' => 1
									]
								]); ?>
								<div class="quantity-icon minus">
									<span class="flaticon flaticon-substract"></span>
								</div>
								<div class="quantity-icon plus">
									<span class="flaticon flaticon-add"></span>
								</div>
							</div>
						</div>
					<?php break;
					case 'tags': ?>
						<div class="label_search ova-tags">
							<label class="field-label">
								<?php echo esc_html( $label_tags ); ?>
							</label>
							<?php ovabrw_text_input([
								'type' 			=> 'text',
								'name' 			=> 'product_tag',
								'placeholder' 	=> $placeholder_tags
							]); ?>
						</div> 
					<?php break;

					default:
						break;
				}
			endfor;

			$args_taxonomy 	= [];
			$taxonomies 	= ovabrw_get_option( 'custom_taxonomy', [] );
			$show_taxonomy 	= ovabrw_get_setting( 'search_show_tax_depend_cat', 'yes' );

			if ( ovabrw_array_exists( $custom_taxonomies ) ) {
				foreach ( $custom_taxonomies as $obj_taxonomy ) {
					$taxonomy_slug 	= $obj_taxonomy['taxonomy_custom'];
					$selected 		= isset( $_GET[$taxonomy_slug.'_name'] ) ? $_GET[$taxonomy_slug.'_name'] : '';

					if ( isset( $taxonomies[$taxonomy_slug] ) && ! empty( $taxonomies[$taxonomy_slug] ) ) {
						$taxonomy_name = $taxonomies[$taxonomy_slug]['name'];
						$html_taxonomy = OVABRW()->options->get_html_dropdown_taxonomies_search( $taxonomy_slug, $taxonomy_name, $selected );
						if ( !empty( $taxonomy_name ) && $html_taxonomy ):
							$args_taxonomy[$taxonomy_slug] = $taxonomy_name;
						?>
							<div class="label_search wrap_search_taxonomies <?php echo esc_attr( $taxonomy_slug ); ?>">
								<label class="field-label"><?php echo esc_html( $taxonomy_name ); ?></label>
								<?php echo $html_taxonomy; ?>
							</div>
						<?php
						endif;
					}
				}
			?>
				<div class="show_taxonomy" data-show_taxonomy="<?php echo esc_attr( $show_taxonomy ); ?>"></div>
				<?php ovabrw_text_input([
					'type' 	=> 'hidden',
					'id' 	=> 'data_taxonomy_custom',
					'name' 	=> 'data_taxonomy_custom',
					'value' => json_encode( $args_taxonomy )
				]);
			} ?>
		</div>
		<?php if ( $card ) {
			ovabrw_text_input([
				'type' 	=> 'hidden',
				'name' 	=> 'card',
				'value' => $card
			]);
		} ?>
		<?php if ( !$result_url ): ?>
	        <div class="product-search-submit">
                <button class="ovabrw_btn_submit" type="submit">
                	<i class="flaticon flaticon-search-1"></i>
                </button>
            </div>
            <?php ovabrw_text_input([
            	'type' 	=> 'hidden',
            	'name' 	=> 'orderby',
            	'value' => $orderby
            ]); ?>
            <?php ovabrw_text_input([
            	'type' 	=> 'hidden',
            	'name' 	=> 'ovabrw_search',
            	'value' => 'search_item'
            ]); ?>
        <?php else: ?>
        	<div class="product-search-submit">
                <button class="ovabrw_btn_submit" type="submit">
                	<i class="flaticon flaticon-search-1"></i>
                </button>
            </div>
	    <?php endif; ?>
	</form>
</div>