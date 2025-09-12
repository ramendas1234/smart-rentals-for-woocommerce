<?php defined( 'ABSPATH' ) || exit;

// Date format
$date_format = OVABRW()->options->get_date_format();

// Time format
$time_format = OVABRW()->options->get_time_format();

// Pick-up location
$pickup_location = ovabrw_get_meta_data( 'pickup_location', $_REQUEST );

// Drop-off location
$dropoff_location = ovabrw_get_meta_data( 'dropoff_location', $_REQUEST );

// Origin location
$origin_location = stripslashes( stripslashes( ovabrw_get_meta_data( 'origin', $_REQUEST ) ) );

// Destination
$destination = stripslashes( stripslashes( ovabrw_get_meta_data( 'destination', $_REQUEST ) ) );

// Pick-up date
$pickup_date = ovabrw_get_meta_data( 'pickup_date', $_REQUEST );

// Category
$category = ovabrw_get_meta_data( 'cat', $_REQUEST );

// Number seats
$number_seats = ovabrw_get_meta_data( 'seats', $_REQUEST, apply_filters( OVABRW_PREFIX.'default_number_of_seats', 4 ) );

// Quantity
$quantity = ovabrw_get_meta_data( 'quantity', $_REQUEST, 1 );

// Duration
$duration = (int)ovabrw_get_meta_data( 'duration', $_REQUEST );

// Distance
$distance = (int)ovabrw_get_meta_data( 'distance', $_REQUEST );

// Column
$column = (int)ovabrw_get_meta_data( 'columns', $args, 4 );

// Fields
$search_fields = ovabrw_get_meta_data( 'fields', $args );

// Has location field
$has_location = false;

// Custom Taxonomies
$custom_taxonomies = ovabrw_get_meta_data( 'custom_taxonomies', $args );

// Card template
$card_template = ovabrw_get_meta_data( 'card_template', $args );

// Posts per page
$posts_per_page = (int)ovabrw_get_meta_data( 'posts_per_page', $args, 6 );

// Result column
$item_column = ovabrw_get_meta_data( 'column', $args, 'three-column' );
if ( in_array( $card_template , ['card5', 'card6'] ) ) {
	$item_column = 'one-column';
}

// Order by
$orderby = ovabrw_get_meta_data( 'orderby', $args, 'date' );

// Order
$order = ovabrw_get_meta_data( 'order', $args, 'DESC' );

// Category
$term = ovabrw_get_meta_data( 'term', $args );

// Default category
$default_category = ovabrw_get_meta_data( 'default_category', $args );
if ( $category ) $default_category = $category;
if ( $default_category ) $term = $default_category; 

// Render HTML
if ( ovabrw_array_exists( $search_fields ) ): ?>
	<div class="ovabrw-search-taxi-ajax">
		<form action="<?php echo esc_url( home_url() ); ?>" method="get" class="search-taxi-form" autocomplete="off">
			<div class="search-taxi-fields search-grid-<?php echo esc_attr( $column ); ?>">
				<?php foreach ( $search_fields as $field ): ?>
					<div class="search-field <?php echo esc_attr( $field['field_name'] ); ?>">
						<?php if ( $field['field_label'] ): ?>
							<label for="<?php echo esc_attr( $field['field_name'] ); ?>">
								<?php echo esc_html( $field['field_label'] ); ?>
							</label>
							<?php if ( 'pickup-location' == $field['field_name'] ):
								$has_location = true;

								ovabrw_text_input([
									'type' 			=> 'text',
									'id' 			=> $field['field_name'],
									'name' 			=> 'pickup_location',
									'value' 		=> $pickup_location,
									'placeholder' 	=> $field['field_placeholder']
								]);
								ovabrw_text_input([
									'type' 	=> 'hidden',
									'name' 	=> 'origin',
									'value' => $origin_location
								]);
							?>
								<i aria-hidden="true" class="brwicon3-map"></i>
							<?php elseif ( 'dropoff-location' == $field['field_name'] ):
								$has_location = true;

								ovabrw_text_input([
									'type' 			=> 'text',
									'id' 			=> $field['field_name'],
									'name' 			=> 'dropoff_location',
									'value' 		=> $dropoff_location,
									'placeholder' 	=> $field['field_placeholder']
								]);
								ovabrw_text_input([
									'type' 	=> 'hidden',
									'name' 	=> 'destination',
									'value' => $destination
								]);
							?>
								<i aria-hidden="true" class="brwicon3-map"></i>
							<?php elseif ( 'pickup-date' == $field['field_name'] ):
								ovabrw_text_input([
									'type' 			=> 'text',
									'id' 			=> ovabrw_unique_id( $field['field_name'] ),
									'name' 			=> 'pickup_date',
									'value' 		=> $pickup_date,
									'placeholder' 	=> $field['field_placeholder'],
									'data_type' 	=> 'datetimepicker-start',
									'attrs' 		=> [
										'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
										'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : '',
									]
								]);
							?>
								<i aria-hidden="true" class="brwicon3-calendar"></i>
							<?php elseif ( 'category' == $field['field_name'] ):
								$incl_category = ovabrw_get_meta_data( 'incl_category', $args );
								$excl_category = ovabrw_get_meta_data( 'excl_category', $args );

								echo OVABRW()->options->get_html_dropdown_categories( $default_category, '', $excl_category, $field['field_placeholder'], $incl_category );
							?> 
							
							<?php if ( !empty( array_filter( $incl_category ) ) ) : ?>
							    <?php ovabrw_text_input([
							        'type'  => 'hidden',
							        'name'  => 'incl_category',
							        'value' => json_encode( $incl_category ),
							    ]); ?>
							<?php endif; ?>

							<?php if ( !empty( array_filter( $excl_category ) ) ) : ?>
							    <?php ovabrw_text_input([
							        'type'  => 'hidden',
							        'name'  => 'excl_category',
							        'value' => json_encode( $excl_category ),
							    ]); ?>
							<?php endif; ?>

								<i aria-hidden="true" class="brwicon3-car-1"></i>
							<?php elseif ( 'number-seats' == $field['field_name'] ):
								ovabrw_text_input([
									'type' 			=> 'number',
									'id' 			=> $field['field_name'],
									'name' 			=> 'seats',
									'value' 		=> $number_seats,
									'placeholder' 	=> $field['field_placeholder'],
									'attrs' 		=> [
										'min' => 1
									]

								]);
							?>
								<i aria-hidden="true" class="brwicon3-user"></i>
							<?php elseif ( 'quantity' == $field['field_name'] ):
								ovabrw_text_input([
									'type' 			=> 'number',
									'id' 			=> $field['field_name'],
									'name' 			=> 'quantity',
									'value' 		=> $quantity,
									'placeholder' 	=> $field['field_placeholder'],
									'attrs' 		=> [
										'min' => 1
									]
								]);
							?>
								<i aria-hidden="true" class="flaticon-add"></i>
							<?php endif; ?>
						<?php endif; ?>
					</div>
				<?php endforeach;

				// Custom Taxonomies
				$args_taxonomy 	= [];
				$taxonomies 	= ovabrw_get_option( 'custom_taxonomy', [] );

				if ( ovabrw_array_exists( $custom_taxonomies ) ) {
					foreach ( $custom_taxonomies as $obj_taxonomy ) {
						$taxonomy_slug 	= $obj_taxonomy['custom_taxonomy'];
						$selected 		= ovabrw_get_meta_data( $taxonomy_slug.'_name', $_REQUEST );

						if ( ovabrw_get_meta_data( $taxonomy_slug, $taxonomies ) ) {
							$taxonomy_name = $taxonomies[$taxonomy_slug]['name'];
							$html_taxonomy = OVABRW()->options->get_html_dropdown_taxonomies_search( $taxonomy_slug, $taxonomy_name, $selected );

							if ( !empty( $taxonomy_name ) && $html_taxonomy ):
								$args_taxonomy[$taxonomy_slug] = $taxonomy_name;
							?>
								<div class="search-field wrap_search_taxonomies <?php echo esc_attr( $taxonomy_slug ); ?>">
									<label class="field-label">
										<?php echo esc_html( $taxonomy_name ); ?>
									</label>
									<?php echo $html_taxonomy; ?>
								</div>
							<?php
							endif;
						}
					}
				}

				ovabrw_text_input([
					'type' 	=> 'hidden',
					'name' 	=> 'custom-taxonomies',
					'value' => json_encode( $args_taxonomy )
				]); ?>
				<div class="search-field search-field-btn">
					<button class="search-taxi-btn" type="submit">
			        	<i class="flaticon-search-1"></i>
			        	<?php esc_html_e( 'Search Taxi', 'ova-brw' ); ?>
			        </button>
				</div>
				<?php if ( $has_location ) { // Config map
					$map_types 		= ovabrw_get_meta_data( 'map_type', $args );
					$bounds 		= 'yes' == ovabrw_get_meta_data( 'bounds', $args ) ? '1' : '';
					$bounds_lat 	= ovabrw_get_meta_data( 'lat', $args );
					$bounds_lng 	= ovabrw_get_meta_data( 'lng', $args );
					$radius 		= ovabrw_get_meta_data( 'radius', $args );
					$restrictions 	= ovabrw_get_meta_data( 'restrictions', $args, [] );

					ovabrw_text_input([
						'type' 	=> 'hidden',
						'name' 	=> 'search-taxi-options',
						'attrs' => [
							'data-map-types' 		=> json_encode( [$map_types] ),
							'data-bounds' 			=> $bounds,
							'data-bounds-lat' 		=> $bounds_lat,
							'data-bounds-lng' 		=> $bounds_lng,
							'data-bounds-radius' 	=> $radius,
							'data-restrictions' 	=> json_encode( $restrictions )
						]
					]);
					ovabrw_text_input([
						'type' 	=> 'hidden',
						'name' 	=> 'duration',
						'value' => $duration
					]);
					ovabrw_text_input([
						'type' 	=> 'hidden',
						'name' 	=> 'distance',
						'value' => $distance
					]);
				} ?>
			</div>
		</form>
		<div class="ovabrw-search-taxi-results">
			<ul class="products ovabrw-product-list <?php echo esc_attr( $item_column ); ?>"></ul>
			<span class="ovabrw-loader"></span>
		</div>
		<?php if ( 'yes' === $pagination ): ?>
			<ul class="ovabrw-pagination"></ul>
		<?php endif; ?>
		<?php ovabrw_text_input([
			'type' 	=> 'hidden',
			'name' 	=> 'ovabrw-search-query',
			'attrs' => [
				'data-card-template' 	=> $card_template,
				'data-posts-per-page' 	=> $posts_per_page,
				'data-column' 			=> $item_column,
				'data-orderby' 			=> $orderby,
				'data-order' 			=> $order,
				'data-term' 			=> $term,
				'data-pagination' 		=> $pagination
			]
		]); ?>
	</div>
<?php endif;