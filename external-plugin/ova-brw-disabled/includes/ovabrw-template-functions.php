<?php if ( !defined( 'ABSPATH' ) ) exit();

/* === Product Detail === */

// Images
if ( !function_exists( 'ovabrw_modern_product_images' ) ) {
	function ovabrw_modern_product_images() {
		if ( get_option( 'ova_brw_template_feature_image', 'yes' ) === 'yes' ) {
			ovabrw_get_template( 'modern/single/detail/ovabrw-product-images.php' );
		}
	}
}

// Tabel price
if ( !function_exists( 'ovabrw_modern_product_table_price' ) ) {
	function ovabrw_modern_product_table_price() {
		ovabrw_get_template( 'modern/single/detail/ovabrw-product-table-price.php' );
	}
}

// Unavailabel
if ( !function_exists( 'ovabrw_modern_product_unavailabel' ) ) {
	function ovabrw_modern_product_unavailabel() {
		ovabrw_get_template( 'modern/single/detail/ovabrw-product-unavailable.php' );
	}
}

// Calendar
if ( !function_exists( 'ovabrw_modern_product_calendar' ) ) {
	function ovabrw_modern_product_calendar() {
		ovabrw_get_template( 'modern/single/detail/ovabrw-product-calendar.php' );
	}
}

// Is featured
if ( !function_exists( 'ovabrw_modern_product_is_featured' ) ) {
	function ovabrw_modern_product_is_featured() {
		ovabrw_get_template( 'modern/single/detail/ovabrw-product-is-featured.php' );
	}
}

// Title
if ( !function_exists( 'ovabrw_modern_product_title' ) ) {
	function ovabrw_modern_product_title() {
		if ( get_option( 'ova_brw_template_show_title', 'yes' ) === 'yes' ) {
			ovabrw_get_template( 'modern/single/detail/ovabrw-product-title.php' );
		}
	}
}

// Review
if ( !function_exists( 'ovabrw_modern_product_review' ) ) {
	function ovabrw_modern_product_review() {
		if ( get_option( 'ova_brw_template_show_review_product', 'yes' ) === 'yes' ) {
			ovabrw_get_template( 'modern/single/detail/ovabrw-product-review.php' );
		}
	}
}

// Price
if ( !function_exists( 'ovabrw_modern_product_price' ) ) {
	function ovabrw_modern_product_price() {
		if ( get_option( 'ova_brw_template_show_price', 'yes' ) === 'yes' ) {
			ovabrw_get_template( 'modern/single/detail/ovabrw-product-price.php' );
		}
	}
}

// Specifications
if ( !function_exists( 'ovabrw_modern_product_specifications' ) ) {
	function ovabrw_modern_product_specifications() {
		ovabrw_get_template( 'modern/single/detail/ovabrw-product-specifications.php' );
	}
}

// Features
if ( !function_exists( 'ovabrw_modern_product_features' ) ) {
	function ovabrw_modern_product_features() {
		ovabrw_get_template( 'modern/single/detail/ovabrw-product-features.php' );
	}
}

// Categories
if ( !function_exists( 'ovabrw_modern_product_categories' ) ) {
	function ovabrw_modern_product_categories() {
		if ( get_option( 'ova_brw_template_show_meta', 'yes' ) === 'yes' ) {
			ovabrw_get_template( 'modern/single/detail/ovabrw-product-categories.php' );
		}
	}
}

// Custom taxonomy
if ( !function_exists( 'ovabrw_modern_product_custom_taxonomy' ) ) {
	function ovabrw_modern_product_custom_taxonomy() {
		if ( get_option( 'ova_brw_template_show_meta', 'yes' ) === 'yes' ) {
			ovabrw_get_template( 'modern/single/detail/ovabrw-product-custom-taxonomy.php' );
		}
	}
}

// Attributes
if ( !function_exists( 'ovabrw_modern_product_attributes' ) ) {
	function ovabrw_modern_product_attributes() {
		if ( get_option( 'ova_brw_template_show_meta', 'yes' ) === 'yes' ) {
			ovabrw_get_template( 'modern/single/detail/ovabrw-product-attributes.php' );
		}
	}
}

// Short description
if ( !function_exists( 'ovabrw_modern_product_short_description' ) ) {
	function ovabrw_modern_product_short_description() {
		ovabrw_get_template( 'modern/single/detail/ovabrw-product-short-description.php' );
	}
}

// Forms
if ( !function_exists( 'ovabrw_modern_product_forms' ) ) {
	function ovabrw_modern_product_forms() {
		ovabrw_get_template( 'modern/single/detail/ovabrw-product-form-tabs.php' );
	}
}

// Related
if ( !function_exists( 'ovabrw_modern_product_related' ) ) {
	function ovabrw_modern_product_related() {
		if ( get_option( 'ova_brw_template_show_related_product', 'yes' ) === 'yes' ) {
			ovabrw_get_template( 'modern/single/detail/ovabrw-product-related.php' );
		}
	}
}

// Product sticky
if ( !function_exists( 'ovabrw_modern_product_sticky' ) ) {
    function ovabrw_modern_product_sticky( $args ) { 
        if ( !empty( $args ) ) {
            ovabrw_get_template( 'modern/single/detail/ovabrw-product-sticky.php', $args );
        }
    }
}


/* === End Product Detail === */

/**
 * Output the text input
 *
 * @param  array 	$args Args for the text input.
 * @param  boolean 	$echo Whether to return or echo|string.
 */
if ( !function_exists( 'ovabrw_text_input' ) ) {
	function ovabrw_text_input( $args = [] ) {
		$args['type'] 			= ovabrw_get_meta_data( 'type', $args, 'text' );
		$args['id'] 			= ovabrw_get_meta_data( 'id', $args );
		$args['class'] 			= ovabrw_get_meta_data( 'class', $args );
		$args['name'] 			= ovabrw_get_meta_data( 'name', $args );
		$args['value'] 			= ovabrw_get_meta_data( 'value', $args );
		$args['default'] 		= ovabrw_get_meta_data( 'default', $args );
		$args['placeholder'] 	= ovabrw_get_meta_data( 'placeholder', $args );
		$args['description'] 	= ovabrw_get_meta_data( 'description', $args );
		$args['required'] 		= ovabrw_get_meta_data( 'required', $args );
		$args['readonly'] 		= ovabrw_get_meta_data( 'readonly', $args );
		$args['checked'] 		= ovabrw_get_meta_data( 'checked', $args );
		$args['disabled'] 		= ovabrw_get_meta_data( 'disabled', $args );
		$args['attrs'] 			= ovabrw_get_meta_data( 'attrs', $args );

		// Set value
		if ( ! $args['value'] && $args['default'] ) {
			$args['value'] = $args['default'];
		}

		// Required
		if ( $args['required'] ) {
			$args['class'] .= ' ovabrw-input-required';
		}

		// Data type
		$data_type = ovabrw_get_meta_data( 'data_type', $args );

		switch ( $data_type ) {
			case 'timepicker':
				// Add class
				$args['class'] .= ' ovabrw-timepicker';

				// Get time format
				$time_format = OVABRW()->options->get_time_format();

				// Set value
				$args['value'] = strtotime( $args['value'] ) ? gmdate( $time_format, strtotime( $args['value'] ) ) : '';

				// Set placeholder
				if ( !$args['placeholder'] ) {
					$args['placeholder'] = OVABRW()->options->get_time_placeholder();
				}
				break;
			case 'datepicker':
				// Add class
				$args['class'] .= ' ovabrw-datepicker';

				// Get date format
				$date_format = OVABRW()->options->get_date_format();

				// Set value
				$args['value'] 	= strtotime( $args['value'] ) ? gmdate( $date_format, strtotime( $args['value'] ) ) : '';

				// Set placeholder
				if ( !$args['placeholder'] ) {
					$args['placeholder'] = OVABRW()->options->get_date_placeholder();
				}
				break;
			case 'datepicker-field':
				// Add class
				$args['class'] .= ' ovabrw-datepicker-field';

				// Get date format
				$date_format = OVABRW()->options->get_date_format();

				// Set value
				$args['value'] 	= strtotime( $args['value'] ) ? gmdate( $date_format, strtotime( $args['value'] ) ) : '';

				// Set placeholder
				if ( !$args['placeholder'] ) {
					$args['placeholder'] = OVABRW()->options->get_date_placeholder();
				}
				break;
			case 'datepicker-start':
				// Add class
				$args['class'] .= ' ovabrw-datepicker-start';

				// Get date format
				$date_format = OVABRW()->options->get_date_format();

				// Set value
				$args['value'] 	= strtotime( $args['value'] ) ? gmdate( $date_format, strtotime( $args['value'] ) ) : '';

				// Set placeholder
				if ( !$args['placeholder'] ) {
					$args['placeholder'] = OVABRW()->options->get_date_placeholder();
				}
				break;
			case 'datepicker-end':
				// Add class
				$args['class'] .= ' ovabrw-datepicker-end';

				// Get date format
				$date_format = OVABRW()->options->get_date_format();

				// Set value
				$args['value'] 	= strtotime( $args['value'] ) ? gmdate( $date_format, strtotime( $args['value'] ) ) : '';

				// Set placeholder
				if ( !$args['placeholder'] ) {
					$args['placeholder'] = OVABRW()->options->get_date_placeholder();
				}
				break;
			case 'datetimepicker':
				// Add class
				$args['class'] .= ' ovabrw-datetimepicker';

				// Get date time format
				$datetime_format = OVABRW()->options->get_datetime_format();

				// Set value
				$args['value'] = strtotime( $args['value'] ) ? gmdate( $datetime_format, strtotime( $args['value'] ) ) : '';

				// Set placeholder
				if ( !$args['placeholder'] ) {
					$args['placeholder'] = OVABRW()->options->get_datetime_placeholder();
				}
				break;
			case 'datetimepicker-start':
				// Add class
				$args['class'] .= ' ovabrw-datetimepicker-start';

				// Get date time format
				$datetime_format = OVABRW()->options->get_datetime_format();

				// Set value
				$args['value'] = strtotime( $args['value'] ) ? gmdate( $datetime_format, strtotime( $args['value'] ) ) : '';

				// Set placeholder
				if ( !$args['placeholder'] ) {
					$args['placeholder'] = OVABRW()->options->get_datetime_placeholder();
				}
				break;
			case 'datetimepicker-end':
				// Add class
				$args['class'] .= ' ovabrw-datetimepicker-end';

				// Get date time format
				$datetime_format = OVABRW()->options->get_datetime_format();

				// Set value
				$args['value'] = strtotime( $args['value'] ) ? gmdate( $datetime_format, strtotime( $args['value'] ) ) : '';

				// Set placeholder
				if ( !$args['placeholder'] ) {
					$args['placeholder'] = OVABRW()->options->get_datetime_placeholder();
				}
				break;
			case 'number':
				// Set value
				$args['value'] = $args['value'] ? (int)$args['value'] : '';

				// Set placeholder
				if ( !$args['placeholder'] ) {
					$args['placeholder'] = esc_html__( 'number', 'ova-brw' );
				}
			default:
				break;
		}

		// Custom attribute handling
		$attrs = [];

		if ( ovabrw_array_exists( $args['attrs'] ) ) {
			foreach ( $args['attrs'] as $attr => $value ) {
				if ( !$value && $value !== 0 ) continue;
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Checked
		if ( $args['checked'] ) {
			$attrs[] = 'checked';
		}

		// Disabled
		if ( $args['disabled'] ) {
			$attrs[] = 'disabled';
		}

		// Read only
		if ( $args['readonly'] ) {
			$attrs[] = 'readonly';
		}

		// Input name
		$name = $args['name'];

		// Item key
		$key = ovabrw_get_meta_data( 'key', $args );
		if ( $key ) {
			$name = $args['name'].'['.esc_attr( $key ).']';
		}

		do_action( 'ovabrw_before_text_input', $args );

		if ( $args['id'] ) {
			echo '<input type="'.esc_attr( $args['type'] ).'" id="'.esc_attr( $args['id'] ).'" class="'.esc_attr( $args['class'] ).'" name="'.esc_attr( $name ).'" value="'.esc_attr( $args['value'] ).'" placeholder="'.esc_attr( $args['placeholder'] ).'" '.wp_kses_post( implode( ' ', $attrs ) ).' />';
		} else {
			echo '<input type="'.esc_attr( $args['type'] ).'" class="'.esc_attr( $args['class'] ).'" name="'.esc_attr( $name ).'" value="'.esc_attr( $args['value'] ).'" placeholder="'.esc_attr( $args['placeholder'] ).'" '.wp_kses_post( implode( ' ', $attrs ) ).' />';
		}

		// Description
		if ( $args['description'] ) {
			echo '<span class="description">'.wp_kses_post( $args['description'] ).'</span>';
		}

		do_action( 'ovabrw_after_text_input', $args );
	}
}

/**
 * Output the select input
 *
 * @param  array 	$args Args for the select input.
 * @param  boolean 	$echo Whether to return or echo|string.
 */
if ( !function_exists( 'ovabrw_select_input' ) ) {
	function ovabrw_select_input( $args = [] ) {
		$args['options'] = ovabrw_get_meta_data( 'options', $args, [] );
		if ( !ovabrw_array_exists( $args['options'] ) ) return;

		$args['id'] 			= ovabrw_get_meta_data( 'id', $args );
		$args['class'] 			= ovabrw_get_meta_data( 'class', $args );
		$args['name'] 			= ovabrw_get_meta_data( 'name', $args );
		$args['name_qty'] 		= ovabrw_get_meta_data( 'name_qty', $args );
		$args['value'] 			= ovabrw_get_meta_data( 'value', $args );
		$args['default'] 		= ovabrw_get_meta_data( 'default', $args );
		$args['placeholder'] 	= ovabrw_get_meta_data( 'placeholder', $args );
		$args['description'] 	= ovabrw_get_meta_data( 'description', $args );
		$args['required'] 		= ovabrw_get_meta_data( 'required', $args );
		$args['disabled'] 		= ovabrw_get_meta_data( 'disabled', $args );
		$args['attrs'] 			= ovabrw_get_meta_data( 'attrs', $args );

		// Set value
		if ( !$args['value'] && $args['default'] ) {
			$args['value'] = $args['default'];
		}

		// Required
		if ( $args['required'] ) {
			$args['class'] .= ' ovabrw-input-required';
		}

		// Option quantities
		$quantities = ovabrw_get_meta_data( 'quantities', $args );

		// Custom attribute handling
		$attrs = [];

		if ( ovabrw_array_exists( $args['attrs'] ) ) {
			foreach ( $args['attrs'] as $attr => $value ) {
				if ( !$value && $value !== 0 ) continue;
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Disabled
		if ( $args['disabled'] ) {
			$attrs[] = 'disabled';
		}

		// Item key
		$key = ovabrw_get_meta_data( 'key', $args );

		// Select name
		$name = $args['name'];
		if ( $key ) $name = $args['name'].'['.esc_attr( $key ).']';

		do_action( 'ovabrw_before_select_input', $args );

		echo '<div class="ovabrw-select">';
			// Select
			if ( $args['id'] ) {
				echo '<select id="'.esc_attr( $args['id'] ).'" class="'.esc_attr( $args['class'] ).'" name="'.esc_attr( $name ).'" '.wp_kses_post( implode( ' ', $attrs ) ).'>';
			} else {
				echo '<select class="'.esc_attr( $args['class'] ).'" name="'.esc_attr( $name ).'" '.wp_kses_post( implode( ' ', $attrs ) ).'>';
			}
				// Placeholder
				if ( $args['placeholder'] ) {
					echo '<option value="">'.esc_html( $args['placeholder'] ).'</option>';
				}

				// Loop
				foreach ( $args['options'] as $opt_id => $opt_value ) {
					$opt_qty = ovabrw_get_meta_data( $opt_id, $quantities );
					if ( 0 === $opt_qty ) continue;

					echo '<option value="'.esc_attr( $opt_id ).'" '.selected( $args['value'], $opt_id, false ).'>';
						echo esc_html( $opt_value );
					echo '</option>';
				}
				// End loop
			echo '</select>';

			// Quantity
			if ( ovabrw_array_exists( $quantities ) ) {
				foreach ( $quantities as $k => $opt_qty ) {
					if ( $opt_qty <= 1 ) continue;

					$active = '';
					if ( $args['value'] == $k ) $active = 'active';

					// Option name
					$opt_name = $args['name'].'_qty['.esc_attr( $k ).']';
					if ( $args['name_qty'] ) $opt_name = $args['name_qty'].'_qty['.esc_attr( $k ).']';
					if ( $key ) {
						if ( $args['name_qty'] ) {
							$opt_name = $args['name_qty'].'_qty['.esc_attr( $key ).']['.esc_attr( $k ).']';
						} else {
							$opt_name = $args['name'].'_qty['.esc_attr( $key ).']['.esc_attr( $k ).']';
						}
					}

					echo '<div class="select-item-qty '.esc_attr( $active ).'" data-option="'.esc_attr( $k ).'">';
						echo '<span class="select-qty">1</span>';
						ovabrw_text_input([
							'type' 			=> 'text',
							'id' 			=> '',
							'class' 		=> 'select-input-qty',
							'name' 			=> esc_attr( $opt_name ),
							'value' 		=> 1,
							'attrs' 		=> [
								'min' => 1,
								'max' => $opt_qty
							]
						]);
						echo '<div class="ovabrw-select-icon">';
							echo '<i class="brwicon2-up-arrow" aria-hidden="true"></i>';
							echo '<i class="brwicon2-down-arrow" aria-hidden="true"></i>';
						echo '</div>';
					echo '</div>';
				}
			}
		echo '</div>';

		// Description
		if ( $args['description'] ) {
			echo '<span class="description">'.wp_kses_post( $args['description'] ).'</span>';
		}

		do_action( 'ovabrw_after_select_input', $args );
	}
}

/**
 * Output the radio input
 *
 * @param  array 	$args Args for the radio input.
 * @param  boolean 	$echo Whether to return or echo|string.
 */
if ( !function_exists( 'ovabrw_radio_input' ) ) {
	function ovabrw_radio_input( $args = [] ) {
		$args['options'] = ovabrw_get_meta_data( 'options', $args, [] );
		if ( !ovabrw_array_exists( $args['options'] ) ) return;

		$args['id'] 			= ovabrw_get_meta_data( 'id', $args );
		$args['class'] 			= ovabrw_get_meta_data( 'class', $args );
		$args['name'] 			= ovabrw_get_meta_data( 'name', $args );
		$args['value'] 			= ovabrw_get_meta_data( 'value', $args );
		$args['default'] 		= ovabrw_get_meta_data( 'default', $args );
		$args['description'] 	= ovabrw_get_meta_data( 'description', $args );
		$args['required'] 		= ovabrw_get_meta_data( 'required', $args );
		$args['attrs'] 			= ovabrw_get_meta_data( 'attrs', $args );

		// Set value
		if ( !$args['value'] && $args['default'] ) {
			$args['value'] = $args['default'];
		}

		// Required
		$required_class = '';
		if ( $args['required'] ) {
			$required_class = ' ovabrw-input-required';
		}

		// Option quantities
		$quantities = ovabrw_get_meta_data( 'quantities', $args, [] );

		// Custom attribute handling
		$attrs = [];

		if ( ovabrw_array_exists( $args['attrs'] ) ) {
			foreach ( $args['attrs'] as $attr => $value ) {
				if ( !$value && $value !== 0 ) continue;
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Item key
		$key = ovabrw_get_meta_data( 'key', $args );

		do_action( 'ovabrw_before_radio_input', $args );

		if ( $args['id'] ) {
			echo '<div id="'.esc_attr( $args['id'] ).'" class="ovabrw-radio'.esc_attr( $required_class ).'">';
		} else {
			echo '<div class="ovabrw-radio'.esc_attr( $required_class ).'">';
		}

		// Loop
		foreach ( $args['options'] as $opt_id => $opt_value ) {
			$opt_qty = ovabrw_get_meta_data( $opt_id, $quantities );
			if ( $opt_qty === 0 ) continue;

			$active = '';
			if ( $args['value'] === $opt_id ) $active = 'active';

			// Option name
			$opt_name = $args['name'];
			if ( $key ) $opt_name = $args['name'].'['.esc_attr( $key ).']';

			echo '<div class="radio-item">';
				echo '<label class="ovabrw-label-field">';
					echo wp_kses_post( $opt_value );
					echo '<input type="radio" class="'.esc_attr( $args['class'] ).'" name="'.esc_attr( $opt_name ).'" value="'.esc_attr( $opt_id ).'" '.wp_kses_post( implode( ' ', $attrs ) ).' '.checked( $args['value'], $opt_id, false ).' />';
					echo '<span class="checkmark"></span>';
				echo '</label>';
				echo '<span class="ovabrw-remove-checked '.esc_attr( $active ).'">';
					echo '<i class="brwicon2-close"></i>';
				echo '</span>';

				// Quatity
				if ( $opt_qty && $opt_qty > 1 ) {
					// Option qty name
					$opt_qty_name = $args['name'].'_qty['.esc_attr( $opt_id ).']';
					if ( $key ) $opt_qty_name = $args['name'].'_qty['.esc_attr( $key ).']['.esc_attr( $opt_id ).']';

					echo '<div class="radio-item-qty '.esc_attr( $active ).'" data-option="'.esc_attr( $opt_id ).'">';
						echo '<span class="radio-qty">1</span>';
						ovabrw_text_input([
							'type' 			=> 'text',
							'id' 			=> '',
							'class' 		=> 'radio-input-qty',
							'name' 			=> esc_attr( $opt_qty_name ),
							'value' 		=> 1,
							'attrs' 		=> [
								'min' => 1,
								'max' => $opt_qty
							]
						]);
						echo '<div class="ovabrw-radio-icon">';
							echo '<i class="brwicon2-up-arrow" aria-hidden="true"></i>';
							echo '<i class="brwicon2-down-arrow" aria-hidden="true"></i>';
						echo '</div>';
					echo '</div>';
				}
			echo '</div>';
		}
		// End loop

		echo '</div>';

		// Description
		if ( $args['description'] ) {
			echo '<span class="description">'.wp_kses_post( $args['description'] ).'</span>';
		}

		do_action( 'ovabrw_after_radio_input', $args );
	}
}

/**
 * Output the checkbox input
 *
 * @param  array 	$args Args for the checkbox input.
 * @param  boolean 	$echo Whether to return or echo|string.
 *
 */
if ( !function_exists( 'ovabrw_checkbox_input' ) ) {
	function ovabrw_checkbox_input( $args = [] ) {
		$args['options'] = ovabrw_get_meta_data( 'options', $args, [] );
		if ( !ovabrw_array_exists( $args['options'] ) ) return;

		$args['id'] 			= ovabrw_get_meta_data( 'id', $args );
		$args['class'] 			= ovabrw_get_meta_data( 'class', $args );
		$args['name'] 			= ovabrw_get_meta_data( 'name', $args );
		$args['value'] 			= ovabrw_get_meta_data( 'value', $args );
		$args['default'] 		= ovabrw_get_meta_data( 'default', $args );
		$args['description'] 	= ovabrw_get_meta_data( 'description', $args );
		$args['required'] 		= ovabrw_get_meta_data( 'required', $args );
		$args['attrs'] 			= ovabrw_get_meta_data( 'attrs', $args );

		// Set value
		if ( ! $args['value'] && $args['default'] ) {
			$args['value'] = $args['default'];
		}

		// Required
		$required_class = '';
		if ( $args['required'] ) {
			$required_class = ' ovabrw-input-required';
		}

		// Options quantity
		$quantities = ovabrw_get_meta_data( 'quantities', $args, [] );

		// Custom attribute handling
		$attrs = [];

		if ( ovabrw_array_exists( $args['attrs'] ) ) {
			foreach ( $args['attrs'] as $attr => $value ) {
				if ( !$value && $value !== 0 ) continue;
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Item key
		$key = ovabrw_get_meta_data( 'key', $args );

		do_action( 'ovabrw_before_checkbox_input', $args );

		if ( $args['id'] ) {
			echo '<div id="'.esc_attr( $args['id'] ).'" class="ovabrw-checkbox'.esc_attr( $required_class ).'">';
		} else {
			echo '<div class="ovabrw-checkbox'.esc_attr( $required_class ).'">';
		}
			// Loop
			foreach ( $args['options'] as $opt_id => $opt_value ) {
				$opt_qty = ovabrw_get_meta_data( $opt_id, $quantities );
				if ( $opt_qty === 0 ) continue;

				$active = '';
				if ( $args['value'] === $opt_id ) $active = 'active';

				// Option name
				$opt_name = $args['name'].'[]';
				if ( $key ) $opt_name = $args['name'].'['.esc_attr( $key ).'][]';

				echo '<div class="checkbox-item">';
					echo '<label class="ovabrw-label-field">';
						echo esc_html( $opt_value );
						echo '<input type="checkbox" class="'.esc_attr( $args['class'] ).'" name="'.esc_attr( $opt_name ).'" value="'.esc_attr( $opt_id ).'" '.wp_kses_post( implode( ' ', $attrs ) ).' '.checked( $args['value'], $opt_id, false ).' />';
						echo '<span class="checkmark"></span>';
					echo '</label>';

					// Quantity
					if ( $opt_qty && $opt_qty > 1 ) {
						// Option qty name
						$opt_qty_name = $args['name'].'_qty['.esc_attr( $opt_id ).']';
						if ( $key ) $opt_qty_name = $args['name'].'_qty['.esc_attr( $key ).']['.esc_attr( $opt_id ).']';

						echo '<div class="checkbox-item-qty '.esc_attr( $active ).'" data-option="'.esc_attr( $opt_id ).'">';
							echo '<span class="checkbox-qty">1</span>';
							ovabrw_text_input([
								'type' 			=> 'text',
								'id' 			=> '',
								'class' 		=> 'checkbox-input-qty',
								'name' 			=> esc_attr( $opt_qty_name ),
								'value' 		=> 1,
								'attrs' 		=> [
									'min' => 1,
									'max' => $opt_qty
								]
							]);
							echo '<div class="ovabrw-checkbox-icon">';
								echo '<i class="brwicon2-up-arrow" aria-hidden="true"></i>';
								echo '<i class="brwicon2-down-arrow" aria-hidden="true"></i>';
							echo '</div>';
						echo '</div>';
					}
				echo '</div>';
			}
			// End loop
		echo '</div>';

		// Description
		if ( $args['description'] ) {
			echo '<span class="description">'.wp_kses_post( $args['description'] ).'</span>';
		}

		do_action( 'ovabrw_after_checkbox_input', $args );
	}
}

/**
 * Output the textarea input
 *
 * @param  array 	$args Args for the textarea input.
 * @param  boolean 	$echo Whether to return or echo|string.
 */
if ( !function_exists( 'ovabrw_textarea_input' ) ) {
	function ovabrw_textarea_input( $args = [] ) {
		$args['id'] 			= ovabrw_get_meta_data( 'id', $args );
		$args['class'] 			= ovabrw_get_meta_data( 'class', $args );
		$args['name'] 			= ovabrw_get_meta_data( 'name', $args );
		$args['value'] 			= ovabrw_get_meta_data( 'value', $args );
		$args['default'] 		= ovabrw_get_meta_data( 'default', $args );
		$args['placeholder'] 	= ovabrw_get_meta_data( 'placeholder', $args );
		$args['description'] 	= ovabrw_get_meta_data( 'description', $args );
		$args['required'] 		= ovabrw_get_meta_data( 'required', $args );
		$args['readonly'] 		= ovabrw_get_meta_data( 'readonly', $args );
		$args['attrs'] 			= ovabrw_get_meta_data( 'attrs', $args );

		// Set value
		if ( ! $args['value'] && $args['default'] ) {
			$args['value'] = $args['default'];
		}

		// Required
		if ( $args['required'] ) {
			$args['class'] .= ' ovabrw-input-required';
		}

		// Custom attribute handling
		$attrs = [];

		if ( ovabrw_array_exists( $args['attrs'] ) ) {
			foreach ( $args['attrs'] as $attr => $value ) {
				if ( !$value && $value !== 0 ) continue;
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Readonly
		$readonly = $args['readonly'] ? 'readonly' : '';

		// Input name
		$name = $args['name'];

		// Item key
		$key = ovabrw_get_meta_data( 'key', $args );
		if ( $key ) $name = $args['name'].'['.esc_attr( $key ).']';

		do_action( 'ovabrw_before_textarea_input', $args );

		if ( $args['id'] ) {
			echo '<textarea name="'.esc_attr( $name ).'" id="'.esc_attr( $args['id'] ).'" class="'.esc_attr( $args['class'] ).'" placeholder="'.esc_attr( $args['placeholder'] ).'" '.wp_kses_post( implode( ' ', $attrs ) ).' '.esc_attr( $readonly ).'>'.esc_html( $args['value'] ).'</textarea>';
		} else {
			echo '<textarea name="'.esc_attr( $name ).'" class="'.esc_attr( $args['class'] ).'" placeholder="'.esc_attr( $args['placeholder'] ).'" '.wp_kses_post( implode( ' ', $attrs ) ).' '.esc_attr( $readonly ).'>'.esc_html( $args['value'] ).'</textarea>';
		}

		// Description
		if ( $args['description'] ) {
			echo '<span class="description">'.wp_kses_post( $args['description'] ).'</span>';
		}

		do_action( 'ovabrw_after_textarea_input', $args );
	}
}

/**
 * Output the file input
 *
 * @param  array 	$args Args for the file input.
 * @param  boolean 	$echo Whether to return or echo|string.
 *
 */
if ( !function_exists( 'ovabrw_file_input' ) ) {
	function ovabrw_file_input( $args = [] ) {
		$args['id'] 			= ovabrw_get_meta_data( 'id', $args );
		$args['class'] 			= ovabrw_get_meta_data( 'class', $args );
		$args['name'] 			= ovabrw_get_meta_data( 'name', $args );
		$args['value'] 			= ovabrw_get_meta_data( 'value', $args );
		$args['default'] 		= ovabrw_get_meta_data( 'default', $args );
		$args['max_size'] 		= ovabrw_get_meta_data( 'max_size', $args );
		$args['placeholder'] 	= ovabrw_get_meta_data( 'placeholder', $args );
		$args['description'] 	= ovabrw_get_meta_data( 'description', $args );
		$args['required'] 		= ovabrw_get_meta_data( 'required', $args );
		$args['attrs'] 			= ovabrw_get_meta_data( 'attrs', $args );

		// Set value
		if ( ! $args['value'] && $args['default'] ) {
			$args['value'] = $args['default'];
		}

		// Required
		if ( $args['required'] ) {
			$args['class'] .= ' ovabrw-input-required';
		}

		// Get file name
		$file_name = '';
		if ( $args['value'] ) {
			$file_name = basename( get_post_meta( $args['value'], '_wp_attached_file', true ) );
		}

		// Custom attribute handling
		$attrs = [];

		if ( ovabrw_array_exists( $args['attrs'] ) ) {
			foreach ( $args['attrs'] as $attr => $value ) {
				if ( !$value && $value !== 0 ) continue;
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Input name
		$name = $args['name'];

		// Item key
		$key = ovabrw_get_meta_data( 'key', $args );
		if ( $key ) $name = $args['name'].'_'.esc_attr( $key );

		// Mimes
		$mimes = apply_filters( OVABRW_PREFIX.'file_mimes', [
            'jpg'   => 'image/jpeg',
            'jpeg'  => 'image/pjpeg',
            'png'   => 'image/png',
            'pdf'   => 'application/pdf',
            'doc'   => 'application/msword',
        ]);

        echo '<div class="ovabrw-modern-file">';
        	echo '<label class="ovabrw-label-field">';
        		echo '<div class="ovabrw-file-name">';
        			echo '<span class="placeholder">';
        				esc_html_e( 'Choose File', 'ova-brw' );
        			echo '</span>';
					echo '<span class="name"></span>';
        		echo '</div>';
        		ovabrw_text_input([
        			'type' 	=> 'file',
        			'id' 	=> $args['id'],
        			'class' => $args['class'],
        			'name' 	=> $name,
        			'attrs' => [
        				'data-max-file-size' 	=> $args['max_size'],
        				'data-file-mimes' 		=> json_encode( $mimes )
        			]
        		]);
        		echo '<i aria-hidden="true" class="brwicon-upload"></i>';
        	echo '</label>';
        echo '</div>';

		// Description
		echo '<span class="description">'.wp_kses_post( $args['description'] ).'</span>';
	}
}