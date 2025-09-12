<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get custom checkout fields
$product_cckf = $product->get_cckf();

if ( ovabrw_array_exists( $product_cckf ) ):
	foreach ( $product_cckf as $name => $fields ):
		$enable = ovabrw_get_meta_data( 'enabled', $fields );
        if ( !$enable ) continue;

        $type           = ovabrw_get_meta_data( 'type', $fields );
        $label          = ovabrw_get_meta_data( 'label', $fields );
        $description 	= ovabrw_get_meta_data( 'description', $fields );
        $default 		= ovabrw_get_meta_data( 'default', $fields );
        $placeholder 	= ovabrw_get_meta_data( 'placeholder', $fields );
        $class 			= ovabrw_get_meta_data( 'class', $fields );
        $required 		= ovabrw_get_meta_data( 'required', $fields );
        $max_size 		= ovabrw_get_meta_data( 'max_file_size', $fields );
        $min 			= ovabrw_get_meta_data( 'min', $fields );
        $max 			= ovabrw_get_meta_data( 'max', $fields );
        $default_date 	= ovabrw_get_meta_data( 'default_date', $fields );
        $min_date 		= ovabrw_get_meta_data( 'min_date', $fields );
        $max_date 		= ovabrw_get_meta_data( 'max_date', $fields );
        $wrapper_class 	= 'ovabrw-ccfk-'.esc_attr( $type );

        // Textarea
        if ( 'textarea' === $type ) {
        	$wrapper_class .= ' full-width';
        }

        // Options
        $options = $quantities = [];

        // Radio
        if ( 'radio' === $type ) {
        	$opt_ids = ovabrw_get_meta_data( 'ova_values', $fields );
        	$opt_qty = ovabrw_get_meta_data( 'ova_qtys', $fields );

        	if ( ovabrw_array_exists( $opt_ids ) ) {
        		foreach ( $opt_ids as $k => $opt_id ) {
        			// Add option ID
                	$options[$opt_id] = $opt_id;

                	// Add option quantity
                	$qty = ovabrw_get_meta_data( $k, $opt_qty );
                	if ( '' != $qty ) $quantities[$opt_id] = (int)$qty;
        		}
        	}
        }

        // Checkbox
        if ( 'checkbox' === $type ) {
        	$opt_ids 	= ovabrw_get_meta_data( 'ova_checkbox_key', $fields );
        	$opt_names 	= ovabrw_get_meta_data( 'ova_checkbox_text', $fields );
        	$opt_qty 	= ovabrw_get_meta_data( 'ova_checkbox_qty', $fields );

        	if ( ovabrw_array_exists( $opt_ids ) ) {
        		foreach ( $opt_ids as $k => $opt_id ) {
        			// Option name
        			$opt_name = ovabrw_get_meta_data( $k, $opt_names );

        			// Add option ID
                	$options[$opt_id] = $opt_name;

                	// Add option quantity
                	$qty = ovabrw_get_meta_data( $k, $opt_qty );
                	if ( '' != $qty ) $quantities[$opt_id] = (int)$qty;
        		}
        	}
        }

        // Select
        if ( 'select' === $type ) {
        	// Placeholder
        	$placeholder = sprintf( esc_html__( 'Select %s', 'ova-brw' ), $label );

        	// Options
        	$opt_ids 	= ovabrw_get_meta_data( 'ova_options_key', $fields );
        	$opt_names 	= ovabrw_get_meta_data( 'ova_options_text', $fields );
        	$opt_qty 	= ovabrw_get_meta_data( 'ova_options_qty', $fields );

        	if ( ovabrw_array_exists( $opt_ids ) ) {
        		foreach ( $opt_ids as $k => $opt_id ) {
        			// Option name
        			$opt_name = ovabrw_get_meta_data( $k, $opt_names );

        			// Add option ID
                	$options[$opt_id] = $opt_name;

                	// Add option quantity
                	$qty = ovabrw_get_meta_data( $k, $opt_qty );
                	if ( '' != $qty ) $quantities[$opt_id] = (int)$qty;
        		}
        	}
        }
	?>
		<div class="rental_item <?php echo esc_attr( $wrapper_class ); ?>">
			<label>
				<?php echo esc_html( $label ); ?>
				<?php if ( $description ): ?>
	                <span class="ovabrw-description" aria-label="<?php echo esc_attr( $description ); ?>">
	                    <i class="brwicon2-question"></i>
	                </span>
	            <?php endif; ?>
			</label>
			<?php if ( 'textarea' === $type ) {
				ovabrw_textarea_input([
					'class' 		=> $class,
					'name' 			=> $name,
					'default' 		=> $default,
					'placeholder' 	=> $placeholder,
					'required' 		=> $required
				]);
			} elseif ( 'select' === $type ) {
				ovabrw_select_input([
					'class' 		=> $class,
					'name' 			=> $name,
					'default' 		=> $default,
					'placeholder' 	=> $placeholder,
					'options' 		=> $options,
					'quantities' 	=> $quantities,
					'required' 		=> $required
				]);
			} elseif ( 'radio' === $type ) {
				ovabrw_radio_input([
					'class' 		=> $class,
					'name' 			=> $name,
					'default' 		=> $default,
					'placeholder' 	=> $placeholder,
					'options' 		=> $options,
					'quantities' 	=> $quantities,
					'required' 		=> $required
				]);
			} elseif ( 'checkbox' === $type ) {
				ovabrw_checkbox_input([
					'class' 		=> $class,
					'name' 			=> $name,
					'default' 		=> $default,
					'placeholder' 	=> $placeholder,
					'options' 		=> $options,
					'quantities' 	=> $quantities,
					'required' 		=> $required
				]);
			} elseif ( 'file' === $type ) {
				ovabrw_file_input([
					'class' 	=> $class,
					'name' 		=> $name,
					'default' 	=> $default,
					'max_size' 	=> $max_size,
					'required' 	=> $required
				]);
			} elseif ( 'date' === $type ) {
				// Date format
                $date_format = OVABRW()->options->get_date_format();

                // Default date
                $default_date = strtotime( $default_date ) ? gmdate( $date_format, strtotime( $default_date ) ) : '';

                // Min date
                $min_date = strtotime( $min_date ) ? gmdate( $date_format, strtotime( $min_date ) ) : '';

                // Max date
                $max_date = strtotime( $max_date ) ? gmdate( $date_format, strtotime( $max_date ) ) : '';

				ovabrw_text_input([
					'type' 			=> 'text',
			        'id' 			=> ovabrw_unique_id( $name ),
			        'class' 		=> $class,
			        'name' 			=> $name,
			        'value' 		=> $default_date,
			        'placeholder' 	=> $placeholder,
			        'required' 		=> $required,
			        'data_type' 	=> 'datepicker-field',
			        'attrs' 		=> [
			        	'data-min-date' => $min_date,
			        	'data-max-date' => $max_date
			        ]
				]);
			} elseif ( 'number' === $type ) {
				ovabrw_text_input([
					'type' 			=> $type,
			        'class' 		=> $class,
			        'name' 			=> $name,
			        'value' 		=> $default,
			        'placeholder' 	=> $placeholder,
			        'required' 		=> $required,
			        'data_type' 	=> 'number',
			        'attrs' 		=> [
			        	'min' => $min,
			        	'max' => $max
			        ]
				]);
			} else {
				ovabrw_text_input([
					'type' 			=> $type,
			        'class' 		=> $class,
			        'name' 			=> $name,
			        'value' 		=> $default,
			        'placeholder' 	=> $placeholder,
			        'required' 		=> $required
				]);
			} ?>
		</div>
	<?php endforeach;
endif; ?>