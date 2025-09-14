<?php defined( 'ABSPATH' ) || exit;
    
// Search heading
$search_heading = ovabrw_get_meta_data( 'search_heading', $args, esc_html__( 'Search Form', 'ova-brw' ) );

// Search position
$search_position = ovabrw_get_meta_data( 'form_search_position', $args, 'left' );

// Column
$column = 'column3';
if ( 'top' != $search_position ) $column = 'column1';

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

// Label button
$label_button = ovabrw_get_meta_data( 'field_button', $args, esc_html__( 'Search', 'ova-brw' ) );

// Label adults
$label_adults = ovabrw_get_meta_data( 'adults_label', $args, esc_html__('Adults','ova-brw') );

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

// Product name
$product_name = sanitize_text_field( ovabrw_get_meta_data( 'product_name', $_GET ) );

// Default category
$default_category = sanitize_text_field( ovabrw_get_meta_data( 'cat', $_GET ) );
if ( '' == $default_category ) {
	$default_category = sanitize_text_field( ovabrw_get_meta_data( 'default_cat', $args ) );
}

// Pick-up date
$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'pickup_date', $_GET ) );

// Drop-off date
$pickoff_date = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_date', $_GET ) );

// Tag product
$product_tag = sanitize_text_field( ovabrw_get_meta_data( 'product_tag', $_GET ) );

// Query data
$order 			= ovabrw_get_meta_data( 'order', $args, 'DESC' );
$orderby 		= ovabrw_get_meta_data( 'orderby', $args, 'date' );
$posts_per_page = ovabrw_get_meta_data( 'posts_per_page', $args, 6 );

// Get products
$products = OVABRW()->options->get_product_from_search([
	'orderby' 			=> $orderby,
	'order' 			=> $order,
	'posts_per_page' 	=> $posts_per_page
]);

// Card Template
$card = ovabrw_get_meta_data( 'card', $args, 'card1' );

// Columns
$result_column = $args['result_column'];
if ( 'card5' == $card || 'card6' == $card ) $result_column = 'one-column';

?>
<div class="ovabrw-product-search-ajax search-position-<?php echo esc_attr( $search_position ); ?>">
	<div class="ovabrw-search-hotel2 ovabrw_wd_search">
		<form class="product-search-form <?php echo esc_attr( $column ); ?>" method="POST" action="" autocomplete="off" autocapitalize="none">
			<div class="product-search-content wrap_content <?php echo esc_attr( $column ); ?>">
				<?php if ( $search_heading ): ?>
				    <h2 class="search-heading">
						<?php echo esc_html( $search_heading ); ?>
					</h2>
				<?php endif; ?>
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
									'value' 		=> $product_name,
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

								<?php if ( !empty(array_filter( $exclude_id )) ) : ?> 
								    <?php ovabrw_text_input([
						            	'type' 	=> 'hidden',
						            	'name' 	=> 'cat_exclude',
						            	'value' => json_encode( $exclude_id )
						            ]); ?> 
								<?php endif; ?> 

								<?php if ( !empty(array_filter( $include_id )) ) : ?>
									<?php ovabrw_text_input([
						            	'type' 	=> 'hidden',
						            	'name' 	=> 'cat_include',
						            	'value' => json_encode( $include_id )
						            ]); ?>
								<?php endif; ?>

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
										'value' 	=> $pickup_date,
										'data_type' => $show_time ? 'datetimepicker-start' : 'datepicker-start',
										'attrs' 	=> [
											'data-date' 		=> strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
											'data-time' 		=> strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : '',
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
										'value' 	=> $pickoff_date,
										'data_type' => $show_time ? 'datetimepicker-end' : 'datepicker-end',
										'attrs' 	=> [
											'data-date' => strtotime( $pickoff_date ) ? gmdate( $date_format, strtotime( $pickoff_date ) ) : '',
											'data-time' => strtotime( $pickoff_date ) ? gmdate( $time_format, strtotime( $pickoff_date ) ) : ''
										]
									]); ?>
									<i class="brwicon-calendar"></i>
								</div>
							</div>
						<?php break;
						case 'guest': ?>
					        <div class="label_search ovabrw-wrapper-guestspicker">
					        	<label class="field-label"><?php echo esc_html( $label_guests ); ?></label>
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
						                        	'type' 	=> 'hidden',
						                        	'name' 	=> 'ovabrw_min_babies',
						                        	'value' => $min_babies
						                        ]); ?>
						                        <?php ovabrw_text_input([
						                        	'type' 	=> 'hidden',
						                        	'name' 	=> 'ovabrw_max_babies',
						                        	'value' => $max_babies ? $max_babies : ''
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
									'value' 		=> $product_tag,
									'placeholder' 	=> $placeholder_tags
								]); ?>
							</div> 
						<?php break;

						default:
							break;
					}
				?>
				<?php endfor; ?>

				<?php
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
								if ( ! empty( $taxonomy_name ) && $html_taxonomy ):
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
					<input
						type="hidden"
						id="data_taxonomy_custom"
						name="data_taxonomy_custom"
						value="<?php echo esc_attr( json_encode( $args_taxonomy ) ); ?>"
					/>
				<?php } ?>

				<div class="product-search-submit">
					<button class="ovabrw_btn_submit" type="submit">
						<?php echo esc_html( $label_button ); ?>
					</button>
				</div>

	            <?php if ( defined( 'ICL_LANGUAGE_CODE' ) ): ?>
                	<input type="hidden" name="lang" value="'.ICL_LANGUAGE_CODE.'" />
                <?php endif; ?>
			</div>

		</form>
	</div>

	<div class="wrap_search_result">
		<!-- Search Filter Results -->
		<div class="wrap_search_filter">
			<div class="results_found">
				<?php if ( $products->found_posts == 1 ): ?>
				<span>
					<?php echo sprintf( esc_html__( '%s Result Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
				</span>
				<?php else: ?>
				<span>
					<?php echo sprintf( esc_html__( '%s Results Found', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
				</span>
				<?php endif; ?>

				<?php if ( 1 == ceil( $products->found_posts/ $products->query_vars['posts_per_page']) && $products->have_posts() ): ?>
					<span>
						<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->found_posts ) ); ?>
					</span>
				<?php elseif ( !$products->have_posts() ): ?>
					<span></span>
				<?php else: ?>
					<span>
						<?php echo sprintf( esc_html__( '(Showing 1-%s)', 'ova-brw' ), esc_html( $products->query_vars['posts_per_page'] ) ); ?>
					</span>
				<?php endif; ?>
			</div>

			<div id="search_sort">
				<?php
					$sort = apply_filters( 'search_sort_default', $orderby );

					if ( 'date' === $orderby && 'DESC' === $order ) {
						$sort = 'date-desc';
					} elseif ( 'date' === $orderby && 'ASC' === $order ) {
						$sort = 'date-asc';
					} elseif ( 'title' === $orderby && 'DESC' === $order ) {
						$sort = 'a-z';
					} elseif ( 'title' === $orderby && 'ASC' === $order ) {
						$sort = 'z-a';
					} elseif ( 'rating' === $orderby ) {
						$sort = 'rating';
					}
				?>
				<select name="sort">
					<option value=""><?php esc_html_e( 'Sort By', 'ova-brw' ); ?></option>
					<option value="date-desc"<?php selected( $sort, 'date-desc' ); ?>>
						<?php esc_html_e( 'Newest First', 'ova-brw' ); ?>
					</option>
					<option value="date-asc"<?php selected( $sort, 'date-asc' ); ?>>
						<?php esc_html_e( 'Oldest First', 'ova-brw' ); ?>
					</option>
					<?php if ( 'yes' === get_option( 'woocommerce_enable_reviews' ) ): ?>
						<option value="rating"<?php selected( $sort, 'rating' ); ?>>
							<?php esc_html_e( 'Average rating', 'ova-brw' ); ?>
						</option>
					<?php endif; ?>
					<option value="a-z" <?php selected( $sort, 'a-z' ); ?>>
						<?php esc_html_e( 'A-Z', 'ova-brw' ); ?>
					</option>
					<option value="z-a" <?php selected( $sort, 'z-a' ); ?>>
						<?php esc_html_e( 'Z-A', 'ova-brw' ); ?>
					</option>
				</select>
			</div>
		</div>

		<!-- Load more -->
		<div class="wrap_load_more" style="display: none;">
			<svg class="loader" width="50" height="50">
				<circle cx="25" cy="25" r="10" stroke="#FF3726"/>
				<circle cx="25" cy="25" r="20" stroke="#FF3726"/>
			</svg>
		</div>

		<!-- Search result -->
		<div
			id="search_result"
			class="search_result"
			data-card="<?php echo esc_attr( $card ); ?>"
			data-column="<?php echo esc_attr( $result_column ); ?>"
			data-order="<?php echo esc_attr( $order ); ?>"
			data-orderby="<?php echo esc_attr( $orderby ); ?>"
			data-per_page="<?php echo esc_attr( $posts_per_page ); ?>">
			<?php if (  $products->max_num_pages > 1 ): ?>
				<div class="ovabrw_pagination_ajax">
					<?php echo OVABRW()->options->get_html_pagination_ajax( $products->found_posts, $products->query_vars['posts_per_page'], 1 ); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>

</div>