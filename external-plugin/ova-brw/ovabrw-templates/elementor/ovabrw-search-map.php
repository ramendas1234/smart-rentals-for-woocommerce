<?php if ( !defined( 'ABSPATH' ) ) exit();

// Extract
extract( $args );

$date_format = OVABRW()->options->get_date_format();
$time_format = OVABRW()->options->get_time_format();
$lat_default = ovabrw_get_setting( 'latitude_map_default', '39.177972' );
$lng_default = ovabrw_get_setting( 'longitude_map_default', '-100.36375' );

$exclude_id = $include_id = '';
if ( ovabrw_get_meta_data( 'category_not_in', $args ) ) {
	$exclude_id = explode( '|', $args['category_not_in'] );
}
if ( ovabrw_get_meta_data( 'category_not_in_select', $args ) ) {
	$exclude_id = $args['category_not_in_select'];
}
if ( ovabrw_get_meta_data( 'category_in', $args ) ) {
	$include_id = explode( '|', $args['category_in'] );
}
if ( ovabrw_get_meta_data( 'category_in_select', $args ) ) {
	$include_id = $args['category_in_select'];
}

// Product name
$product_name = sanitize_text_field( ovabrw_get_meta_data( 'product_name', $_GET ) );

// Default_cat
$default_cat = sanitize_text_field( ovabrw_get_meta_data( 'cat', $_GET ) );
if ( !$default_cat ) $default_cat = ovabrw_get_meta_data( 'default_cat', $args );

// Pick-up location
$pickup_location = sanitize_text_field( ovabrw_get_meta_data( 'pickup_location', $_GET ) );
if ( !$pickup_location ) $pickup_location = ovabrw_get_meta_data( 'default_pickup_loc', $args );

// Drop-off location
$dropoff_location = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_location', $_GET ) );
if ( !$dropoff_location ) $dropoff_location = ovabrw_get_meta_data( 'default_dropoff_loc', $args );

// Pick-up date
$pickup_date = sanitize_text_field( ovabrw_get_meta_data( 'pickup_date', $_GET ) );

// Drop-off date
$dropoff_date = sanitize_text_field( ovabrw_get_meta_data( 'dropoff_date', $_GET ) );

// Product tag
$product_tag = sanitize_text_field( ovabrw_get_meta_data( 'product_tag', $_GET ) );

// Quantity
$quantity = (int)ovabrw_get_meta_data( 'quantity', $_GET, 1 );

// Custom taxonomy
$custom_taxonomies = ovabrw_get_meta_data( 'list_taxonomy_custom', $args, [] );

// Map
$map_lat 		= sanitize_text_field( ovabrw_get_meta_data( 'map_lat', $_GET ) );
$map_lng 		= sanitize_text_field( ovabrw_get_meta_data( 'map_lng', $_GET ) );
$map_address 	= sanitize_text_field( ovabrw_get_meta_data( 'map_address', $_GET ) );
$map_name 		= sanitize_text_field( ovabrw_get_meta_data( 'map_name', $_GET ) );

// Get products
$products = OVABRW()->options->get_product_from_search([
	'orderby' 			=> $orderby,
	'order' 			=> $order,
	'posts_per_page' 	=> $posts_per_page
]);

// Class
$class = '';
if ( 'yes' === $show_map ) {
	$class .= ' ova_have_map';
}

// Show time picker
$show_time = ovabrw_get_meta_data( 'show_time', $args );

// Card Template
$card = ovabrw_get_meta_data( 'card', $args );
if ( $card ) {
	$class .= ' ovabrw-search-modern';
}
if ( $card === 'card5' || $card === 'card6' ) $column = 'one-column';

?>

<div class="elementor_search_map<?php echo esc_attr( $class ); ?>">
	<?php if ( 'yes' === $show_map ): ?>
		<div class="toggle_wrap">
			<span data-value="wrap_search" class="active">
				<?php esc_html_e( 'Results', 'ova-brw' ); ?>
			</span>
			<span data-value="wrap_map">
				<?php esc_html_e( 'Map', 'ova-brw' ); ?>
			</span>
		</div>
	<?php endif; ?>
	<div class="wrap_search_map">
		<!-- Search Map -->
		<div class="wrap_search">
			<?php if ( 'yes' === $show_filter ): ?>
			<div class="fields_search ovabrw_wd_search">
				<span class="toggle_filters ">
					<?php esc_html_e( 'Toggle Filters', 'ova-brw' ); ?>
					<i class="icon_down arrow_triangle-down"></i>
					<i class="icon_up arrow_triangle-up"></i>
				</span>
				<form class="form_search_map" autocomplete="off" autocorrect="off" autocapitalize="none">
					<div class="wrap_content field">
						<?php $show_radius = false;

							// Get fields
							for ( $i = 1; $i <= 9; $i++ ) {
								// Get field name
								$field_name = ovabrw_get_meta_data( 'field_'.esc_attr( $i ), $args );

								switch ( $field_name ) {
									case 'name': ?>
										<div class="label_search wrap_search_name">
											<?php ovabrw_text_input([
										        'name' 			=> 'product_name',
										        'value' 		=> $product_name,
										        'placeholder' 	=> esc_html__( 'Product Name', 'ova-brw' ),
										        'attrs' 		=> [
										        	'autocomplete' 		=> 'nope',
										        	'autocorrect' 		=> 'off',
										        	'autocapitalize' 	=> 'none'
										        ]
											]); ?>
										</div>
									<?php break;
									case 'category': ?>
										<div class="label_search wrap_search_category">
											<?php echo OVABRW()->options->get_html_dropdown_categories( $default_cat, '', $exclude_id, '', $include_id ); ?>
											<?php ovabrw_text_input([
								            	'type' 	=> 'hidden',
								            	'name' 	=> 'cat_exclude',
								            	'value' => json_encode( $exclude_id )
								            ]); ?>
								            <?php ovabrw_text_input([
								            	'type' 	=> 'hidden',
								            	'name' 	=> 'cat_include',
								            	'value' => json_encode( $include_id )
								            ]); ?>
										</div>
									<?php break;
									case 'location': ?>
										<div class="label_search wrap_search_location">
											<?php ovabrw_text_input([
								            	'type' 	=> 'hidden',
								            	'id' 	=> 'map_lat',
								            	'name' 	=> 'map_lat',
								            	'value' => $map_lat
								            ]); ?>
								            <?php ovabrw_text_input([
								            	'type' 	=> 'hidden',
								            	'id' 	=> 'map_lng',
								            	'name' 	=> 'map_lng',
								            	'value' => $map_lng
								            ]); ?>
								            <?php ovabrw_text_input([
								            	'id' 			=> 'pac-input',
								            	'class' 		=> 'controls',
								            	'name' 			=> 'map_address',
								            	'value' 		=> $map_address,
								            	'placeholder' 	=> esc_html__( 'Location', 'ova-brw' ),
								            	'attrs' 		=> [
								            		'autocomplete' 		=> 'nope',
								            		'autocorrect' 		=> 'off',
								            		'autocapitalize' 	=> 'none'
								            	]
								            ]); ?>
											<i class="locate_me icon_circle-slelected" id="locate_me" title="<?php esc_attr_e( 'Use My Location', 'ova-brw' ); ?>"></i>
											<?php ovabrw_text_input([
								            	'type' 	=> 'hidden',
								            	'id' 	=> 'map_name',
								            	'name' 	=> 'map_name',
								            	'value' => $map_name
								            ]); ?>
										</div>
									<?php break;
									case 'start_location': ?>
										<div class="label_search wrap_search_start_location">
											<?php echo OVABRW()->options->get_html_location( 'pickup', 'pickup_location', '', $pickup_location ); ?>
										</div>
									<?php break;
									case 'end_location': ?>
										<div class="label_search wrap_search_end_location">
											<?php echo OVABRW()->options->get_html_location( 'dropoff', 'dropoff_location', '', $dropoff_location ); ?>
										</div>
									<?php break;
									case 'start_date': ?>
										<div class="label_search wrap_search_start_date">
											<?php ovabrw_text_input([
												'type' 			=> 'text',
												'id' 			=> ovabrw_unique_id( 'ovabrw_start_date' ),
												'class' 		=> 'ovabrw_start_date',
												'name' 			=> 'pickup_date',
												'value' 		=> $pickup_date,
												'placeholder' 	=> esc_html__( 'Pick-up date ...', 'ova-brw' ),
												'data_type' 	=> 'yes' === $show_time ? 'datetimepicker-start' : 'datepicker-start',
												'attrs' 		=> [
													'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
													'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
												]
											]); ?>
										</div>
									<?php break;
									case 'end_date': ?>
										<div class="label_search wrap_search_end_date">
											<?php ovabrw_text_input([
												'type' 			=> 'text',
												'id' 			=> ovabrw_unique_id( 'ovabrw_dropoff_date' ),
												'class' 		=> 'ovabrw_end_date',
												'name' 			=> 'dropoff_date',
												'value' 		=> $dropoff_date,
												'placeholder' 	=> esc_html__( 'Drop-off date ...', 'ova-brw' ),
												'data_type' 	=> 'yes' === $show_time ? 'datetimepicker-end' : 'datepicker-end',
												'attrs' 		=> [
													'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : '',
													'data-time' => strtotime( $dropoff_date ) ? gmdate( $time_format, strtotime( $dropoff_date ) ) : ''
												]
											]); ?>
										</div>
									<?php break;
									case 'attribute':
										$data_html_attr = OVABRW()->options->get_html_dropdown_attributes();

										if ( $data_html_attr['html_attr'] ): ?>
											<div class="label_search wrap_search_attribute ovabrw_search">
												<?php echo $data_html_attr['html_attr']; ?>
											</div>
										<?php endif;

										if ( $data_html_attr['html_attr_value'] ): ?>
											<?php echo $data_html_attr['html_attr_value']; ?>
										<?php endif;
										break;
									case 'tag': ?>
										<div class="label_search wrap_search_tag ovabrw_wd_search">
											<?php ovabrw_text_input([
								            	'name' 			=> 'product_tag',
								            	'value' 		=> $product_tag,
								            	'placeholder' 	=> esc_html__( 'Product tag', 'ova-brw' ),
								            	'attrs' 		=> [
								            		'autocomplete' 		=> 'nope',
								            		'autocorrect' 		=> 'off',
								            		'autocapitalize' 	=> 'none'
								            	]
								            ]); ?>
										</div>
									<?php break;
									case 'quantity': ?>
										<div class="label_search wrap_search_quantity">
											<?php ovabrw_text_input([
												'type' 			=> 'number',
								            	'name' 			=> 'quantity',
								            	'value' 		=> $quantity,
								            	'placeholder' 	=> esc_html__( 'Quantity', 'ova-brw' ),
								            	'attrs' 		=> [
								            		'min' 		=> '1'
								            	]
								            ]); ?>
										</div>
									<?php break;
									default:
										// code...
										break;
								}
							} // END for

							// Taxonomies
							$args_taxonomy 	= [];
							$taxonomies 	= ovabrw_get_option( 'custom_taxonomy', [] );
							$show_taxonomy 	= ovabrw_get_setting( 'search_show_tax_depend_cat', 'yes' );

							if ( ovabrw_array_exists( $custom_taxonomies ) ) {
								foreach ( $custom_taxonomies as $obj_taxonomy ) {
									$taxonomy_slug = $obj_taxonomy['taxonomy_custom'];

									if ( isset( $taxonomies[$taxonomy_slug] ) && !empty( $taxonomies[$taxonomy_slug] ) ) {
										$taxonomy_name = $taxonomies[$taxonomy_slug]['name'];
										$html_taxonomy = OVABRW()->options->get_html_dropdown_taxonomies_search( $taxonomy_slug, $taxonomy_name );
										if ( !empty( $taxonomy_name ) && $html_taxonomy ):
											$args_taxonomy[$taxonomy_slug] = $taxonomy_name;
										?>
											<div class="label_search wrap_search_taxonomies <?php echo esc_attr( $taxonomy_slug ); ?>">
												<?php echo $html_taxonomy; ?>
											</div>
										<?php
										endif;
									}
								}
								?>
								<div class="show_taxonomy" data-show_taxonomy="<?php echo esc_html( $show_taxonomy ); ?>"></div>
								<?php
							}
							// End Taxonomies
						?>
						<input 	type="hidden" id="data_taxonomy_custom" name="data_taxonomy_custom" 
								value="<?php echo esc_attr( json_encode( $args_taxonomy ) ); ?>" />
					</div><!-- wrap_content -->

					<!-- Radius -->
					<div class="wrap_search_radius" 
						data-map_range_radius="<?php echo apply_filters( OVABRW_PREFIX.'map_range_radius', 50 ); ?>" 
						data-map_range_radius_min="<?php echo apply_filters( OVABRW_PREFIX.'map_range_radius_min', 0 ); ?>" 
						data-map_range_radius_max="<?php echo apply_filters( OVABRW_PREFIX.'map_range_radius_max', 100 ); ?>">
						<span>
							<?php esc_html_e( 'Radius:', 'ova-brw' ); ?>
						</span>
						<span class="result_radius">
							<?php echo apply_filters( OVABRW_PREFIX.'map_range_radius', 50 ); ?>
							<?php esc_html_e( 'km', 'ova-brw' ); ?>
						</span>
						<div id="wrap_pointer"></div>
						<input
							type="hidden"
							value=""
							name="radius"
						/>
					</div>
					<!-- End Radius -->

					<!-- Filter title -->
					<div class="wrap_search_filter_title">
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
								$sort = apply_filters( OVABRW_PREFIX.'search_sort_default', $orderby );

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
								<option value="">
									<?php esc_html_e( 'Sort By', 'ova-brw' ); ?>
								</option>
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
								<option value="z-a" <?php selected( $sort, 'z-a' ); ?> >
									<?php esc_html_e( 'Z-A', 'ova-brw' ); ?>
								</option>
							</select>
						</div>
					</div><!-- End filter title -->
				</form>
			</div><!-- fields_search -->
			<?php endif; ?>

			<!-- Load more -->
			<div class="wrap_load_more" style="display: none;">
				<svg class="loader" width="50" height="50">
					<circle cx="25" cy="25" r="10" stroke="#e86c60"/>
					<circle cx="25" cy="25" r="20" stroke="#e86c60"/>
				</svg>
			</div>
			<!-- End load more -->

			<!-- Search result -->
			<div
				id="search_result"
				class="search_result"
				data-card="<?php echo esc_attr( $card ); ?>"
				data-column="<?php echo esc_attr( $column ); ?>"
				data-zoom="<?php echo esc_attr( $zoom ); ?>"
				data-default-location="<?php echo esc_attr( $show_default_location ); ?>"
				data-order="<?php echo esc_attr( $order ); ?>"
				data-orderby="<?php echo esc_attr( $orderby ); ?>"
				data-per_page="<?php echo esc_attr( $posts_per_page ); ?>"
				data-lat="<?php echo esc_attr( $lat_default ); ?>"
				data-lng="<?php echo esc_attr( $lng_default ); ?>"
				data-marker_option="<?php echo esc_attr( $marker_option ); ?>"
				data-marker_icon="<?php echo esc_attr( $marker_icon['url'] ); ?>"
				data-show_featured="<?php echo esc_attr( $show_featured ); ?>">
				<?php
					$total = $products->max_num_pages;
					if (  $total > 1 ): ?>
						<div class="ovabrw_pagination_ajax">
						<?php echo OVABRW()->options->get_html_pagination_ajax( $products->found_posts, $products->query_vars['posts_per_page'], 1 ); ?>
						</div>
						<?php
					endif;
				?>
			</div><!-- search_result -->
		</div><!-- wrap_search -->

		<?php if ( 'yes' === $show_map ): ?>
			<div class="wrap_map">
				<div id="show_map"></div>
			</div>
		<?php endif; ?>
	</div>
</div>