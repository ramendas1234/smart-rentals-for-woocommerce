<?php if ( !defined( 'ABSPATH' ) ) exit();

// Date format
$date_format = OVABRW()->options->get_date_format();

// Time format
$time_format = OVABRW()->options->get_time_format();

// Search fields
$fields = ovabrw_get_meta_data( 'fields', $args );

// Field columns
$field_columns = ovabrw_get_meta_data( 'field_columns', $args, 4 );

// Get card template
$card = ovabrw_get_meta_data( 'card_template', $args, 'card1' );

// Columns
$columns = ovabrw_get_meta_data( 'columns', $args, 3 );
if ( 'card5' == $card || 'card6' == $card ) $columns = 1;

// Position
$position = ovabrw_get_meta_data( 'position', $args, 'top' );
if ( 'top' != $position ) $field_columns = 1;

// Show time
$show_time = ovabrw_get_meta_data( 'show_time', $args, 'yes' );

// Default category
$default_category = ovabrw_get_meta_data( 'cat', $_GET );
if ( !$default_category ) {
	$default_category = ovabrw_get_meta_data( 'default_category', $args );
}

// Include category
$incl_category = ovabrw_get_meta_data( 'incl_category', $args );

// Exclude category
$excl_category = ovabrw_get_meta_data( 'excl_category', $args );

// Custom taxonomies
$custom_taxonomies = ovabrw_get_meta_data( 'custom_taxonomies', $args );

// Show results found
$show_results_found = ovabrw_get_meta_data( 'show_results_found', $args );

// Show sort by
$show_sort_by = ovabrw_get_meta_data( 'show_sort_by', $args );

// Orderby
$orderby = ovabrw_get_meta_data( 'orderby', $args, 'date' );

// Order
$order = ovabrw_get_meta_data( 'order', $args, 'DESC' );

// Pagination
$pagination = ovabrw_get_meta_data( 'pagination', $args, 'yes' );

// Product name
$product_name = ovabrw_get_meta_data( 'product_name', $_GET );

// Product tag
$product_tag = ovabrw_get_meta_data( 'product_tag', $_GET );

// Pick-up location
$pickup_location = ovabrw_get_meta_data( 'pickup_location', $_GET );
if ( !$pickup_location ) {
	$pickup_location = ovabrw_get_meta_data( 'default_pickup_location', $args );
}

// Drop-off location
$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $_GET );
if ( !$dropoff_location ) {
	$dropoff_location = ovabrw_get_meta_data( 'default_dropoff_location', $args );
}

// Pick-up date
$pickup_date = ovabrw_get_meta_data( 'pickup_date', $_GET );

// Drop-off date
$dropoff_date = ovabrw_get_meta_data( 'dropoff_date', $_GET );

// Quantity
$quantity = (int)ovabrw_get_meta_data( 'quantity', $_GET, 1 );

if ( ovabrw_array_exists( $fields ) ): ?>
	<div class="ovabrw-seach-ajax-shortcode ovabrw_wd_search search-position-<?php echo esc_attr( $position ); ?>">
		<form action="<?php echo esc_url( home_url() ); ?>" method="POST" class="search-ajax-form search-col-<?php echo esc_attr( $field_columns ); ?>" autocomplete="off">
			<?php foreach ( $fields as $field_name ): ?>
				<div class="search-field">
					<?php if ( 'product-name' === $field_name ): ?>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php esc_html_e( 'Product Name', 'ova-brw' ); ?>
						</label>
						<?php ovabrw_text_input([
							'type' 			=> 'text',
							'id' 			=> $field_name,
							'name' 			=> 'product_name',
							'value' 		=> $product_name,
							'placeholder' 	=> esc_html__( 'Product name', 'ova-brw' )
						]); ?>
					<?php elseif ( 'category' === $field_name ): ?>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php esc_html_e( 'Category', 'ova-brw' ); ?>
						</label>
						<?php echo OVABRW()->options->get_html_dropdown_categories( $default_category, '', $excl_category, esc_html__( 'Select category', 'ova-brw' ), $incl_category );
						?>
						<i aria-hidden="true" class="brwicon3-car-1"></i>
					<?php elseif ( 'pickup-location' === $field_name ): ?>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
						</label>
						<?php echo OVABRW()->options->get_html_location( 'pickup', 'pickup_location', '', $pickup_location ); ?>
						<i aria-hidden="true" class="brwicon3-map"></i>
					<?php elseif ( 'dropoff-location' === $field_name ): ?>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
						</label>
						<?php echo OVABRW()->options->get_html_location( 'dropoff', 'dropoff_location', '', $dropoff_location ); ?>
						<i aria-hidden="true" class="brwicon3-map"></i>
					<?php elseif ( 'pickup-date' === $field_name ): ?>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php esc_html_e( 'Pick-up Date', 'ova-brw' ); ?>
						</label>
						<?php ovabrw_text_input([
                            'type'      	=> 'text',
                            'id'        	=> ovabrw_unique_id( $field_name ),
                            'class'     	=> 'ovabrw_start_date',
                            'name'      	=> 'pickup_date',
                            'value' 		=> $pickup_date,
                            'data_type' 	=> 'yes' == $show_time ? 'datetimepicker-start' : 'datepicker-start',
                            'attrs' 		=> [
								'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
								'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
							]
                        ]); ?>
						<i aria-hidden="true" class="brwicon3-calendar"></i>
					<?php elseif ( 'dropoff-date' === $field_name ): ?>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php esc_html_e( 'Drop-off Date', 'ova-brw' ); ?>
						</label>
						<?php ovabrw_text_input([
                            'type'      	=> 'text',
                            'id'        	=> ovabrw_unique_id( $field_name ),
                            'class'     	=> 'ovabrw_end_date',
                            'name'      	=> 'dropoff_date',
                            'value' 		=> $dropoff_date,
                            'data_type' 	=> 'yes' == $show_time ? 'datetimepicker-end' : 'datepicker-end',
                            'attrs' 		=> [
								'data-date' => strtotime( $dropoff_date ) ? gmdate( $date_format, strtotime( $dropoff_date ) ) : '',
								'data-time' => strtotime( $dropoff_date ) ? gmdate( $time_format, strtotime( $dropoff_date ) ) : ''
							]
                        ]); ?>
						<i aria-hidden="true" class="brwicon3-calendar"></i>
					<?php elseif ( 'product-tags' === $field_name ): ?>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php esc_html_e( 'Tag Product', 'ova-brw' ); ?>
						</label>
						<?php ovabrw_text_input([
							'type' 			=> 'text',
							'name' 			=> 'product_tag',
							'value' 		=> $product_tag,
							'placeholder' 	=> esc_html__( 'Tag product', 'ova-brw' )
						]); ?>
					<?php elseif ( 'quantity' === $field_name ): ?>
						<label for="<?php echo esc_attr( $field_name ); ?>">
							<?php esc_html_e( 'Quantity', 'ova-brw' ); ?>
						</label>
						<?php ovabrw_text_input([
							'type' 	=> 'number',
							'name' 	=> 'quantity',
							'value' => $quantity,
							'attrs' => [
								'min' => 1
							]
						]); ?>
					<?php elseif ( 'location' === $field_name ): ?>
						<label for="pac-input">
							<?php esc_html_e( 'Location', 'ova-brw' ); ?>
						</label>
						<?php ovabrw_text_input([
			            	'type' 	=> 'hidden',
			            	'name' 	=> 'map_lat'
			            ]); ?>
			            <?php ovabrw_text_input([
			            	'type' 	=> 'hidden',
			            	'name' 	=> 'map_lng'
			            ]); ?>
			            <?php ovabrw_text_input([
			            	'id' 			=> 'pac-input',
			            	'name' 			=> 'map_address',
			            	'placeholder' 	=> esc_html__( 'Enter location', 'ova-brw' ),
			            ]); ?>
						<i class="locate_me icon_circle-slelected" id="locate_me" title="<?php esc_attr_e( 'Use My Location', 'ova-brw' ); ?>"></i>
					<?php endif; ?>
				</div>
			<?php endforeach;

			// Custom taxonomy
			if ( ovabrw_array_exists( $custom_taxonomies ) ):
				$args_taxonomy 	= [];
				$taxonomies 	= ovabrw_get_option( 'custom_taxonomy', [] );

				foreach ( $custom_taxonomies as $term_slug ):
					if ( !ovabrw_get_meta_data( $term_slug, $taxonomies ) ) continue;

					$term_name = $taxonomies[$term_slug]['name'];
					$term_html = OVABRW()->options->get_html_dropdown_taxonomies_search( $term_slug, $term_name, '' );
					if ( !$term_name || !$term_html ) continue;

					$args_taxonomy[$term_slug] = $term_name;
			?>
				<div class="search-field">
					<label for="<?php echo esc_attr( $field_name ); ?>">
						<?php echo esc_html( $term_name ); ?>
					</label>
					<?php echo $term_html; ?>
				</div>
				<?php endforeach;
				if ( ovabrw_array_exists( $args_taxonomy ) ) {
					ovabrw_text_input([
						'type' 	=> 'hidden',
						'name' 	=> 'custom-taxonomies',
						'value' => json_encode( $args_taxonomy )
					]);
				}
			endif; ?>
		</form>
		<div class="search-ajax-results">
			<?php if ( 'yes' == $show_results_found || 'yes' == $show_sort_by ): ?>
				<div class="search-filter">
					<?php if ( 'yes' == $show_results_found ): ?>
						<div class="results-found"></div>
					<?php endif;

					// Show sort by
					if ( 'yes' == $show_sort_by ):
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
							<?php if ( 'yes' == get_option( 'woocommerce_enable_reviews' ) ): ?>
								<option value="rating"<?php selected( $sort, 'rating' ); ?>>
									<?php esc_html_e( 'Average rating', 'ova-brw' ); ?>
								</option>
							<?php endif; ?>
							<option value="a-z"<?php selected( $sort, 'a-z' ); ?>>
								<?php esc_html_e( 'A-Z', 'ova-brw' ); ?>
							</option>
							<option value="z-a"<?php selected( $sort, 'z-a' ); ?>>
								<?php esc_html_e( 'Z-A', 'ova-brw' ); ?>
							</option>
						</select>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<ul class="products ovabrw-product-list content-col-<?php echo esc_attr( $columns ); ?>"></ul>
			<span class="ovabrw-loader"></span>
			<ul class="ovabrw-pagination"></ul>
		</div>
		<?php ovabrw_text_input([
        	'type' 	=> 'hidden',
        	'name' 	=> 'ovabrw_search_queries',
        	'value' => json_encode([
        		'show_results_found' 	=> $show_results_found,
        		'card' 					=> $card,
        		'posts_per_page' 		=> $posts_per_page,
        		'orderby'				=> $orderby,
        		'order' 				=> $order,
        		'pagination' 			=> $pagination,
        		'incl_category' 		=> $incl_category,
        		'excl_category' 		=> $excl_category,
        	])
        ]); ?>
	</div>
<?php endif;