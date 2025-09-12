<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Custom taxonomies
 */
add_action( 'init', 'ovabrw_create_type_taxonomies', 0 );
if ( !function_exists( 'ovabrw_create_type_taxonomies' ) ) {
	function ovabrw_create_type_taxonomies() {
		// Get Custom Taxonomy from Database
		$custom_taxonomy 	= ovabrw_get_option( 'custom_taxonomy', '' );
		$name_taxonomy 		= [];
		$new_taxonomies 	= [];

		if ( ovabrw_array_exists( $custom_taxonomy ) ) {
			$i = 1;

			foreach ( $custom_taxonomy as $slug => $value ) {
				$labels = [
					'name'              => _x( $value['name'], 'taxonomy general name', 'ova-brw' ),
					'singular_name'     => _x( $value['singular_name'], 'taxonomy singular name', 'ova-brw' ),
					'search_items'      => esc_html__( 'Search ' . $value['name'], 'ova-brw' ),
					'all_items'         => esc_html__( 'All ' . $value['name'], 'ova-brw' ),
					'parent_item'       => esc_html__( 'Parent ' . $value['name'], 'ova-brw' ),
					'parent_item_colon' => esc_html__( 'Parent ' . $value['name'] .': ', 'ova-brw' ),
					'edit_item'         => esc_html__( 'Edit ' . $value['name'], 'ova-brw' ),
					'update_item'       => esc_html__( 'Update ' . $value['name'], 'ova-brw' ),
					'add_new_item'      => esc_html__( 'Add New ' . $value['name'], 'ova-brw' ),
					'new_item_name'     => esc_html__( 'New ' . $value['name'] .' Name', 'ova-brw' ),
					'menu_name'         => esc_html__( 'Custom ' . $value['name'], 'ova-brw' ),
				];

				$args = [
					'hierarchical'      => true,
					'labels'            => $labels,
					'show_ui'           => apply_filters( OVABRW_PREFIX.'show_ui_custom_tax', true ),
					'show_admin_column' => false,
					'query_var'         => true,
					'show_in_nav_menus' => false,
					'show_in_menu'		=> false,
					'rewrite'           => [ 'slug' => $slug ]
				];

				if ( 'on' === $value['enabled'] ) {
					register_taxonomy( $slug, [ 'product' ], $args );
				}
				
				$new_taxonomies[$i]['slug'] = $slug;
				$new_taxonomies[$i]['name'] = $value['name'];

				if ( !empty( $value['label_frontend'] ) ) {
					$new_taxonomies[$i]['name'] = $value['label_frontend'];
				} else {
					$new_taxonomies[$i]['name'] = $value['name'];
				}
				
				$i++;
			}
		}

		// Get Custom Taxonomy from Code
		// Add new taxonomy, make it hierarchical (like categories)
		$number_taxonomy = ovabrw_get_setting( 'number_taxonomy', 0 );
		if ( $number_taxonomy > 0 ) {
			for ( $i = 1; $number_taxonomy >= $i; $i++ ) {
				$param_arr = [];
				$param_arr = apply_filters( 'register_taxonomy_ovabrw_' . $i, $param_arr ) ;

				if ( empty( $param_arr ) || !is_array( $param_arr ) ) {
					$labels = [
						'name'              => _x( 'Custom Taxonomy ' . $i, 'taxonomy general name', 'ova-brw' ),
						'singular_name'     => _x( 'taxonomy' . $i, 'taxonomy singular name', 'ova-brw' ),
						'search_items'      => esc_html__( 'Search Taxonomy ' . $i, 'ova-brw' ),
						'all_items'         => esc_html__( 'All Taxonomy ' . $i, 'ova-brw' ),
						'parent_item'       => esc_html__( 'Parent Taxonomy ' . $i, 'ova-brw' ),
						'parent_item_colon' => esc_html__( 'Parent Taxonomy ' . $i .': ', 'ova-brw' ),
						'edit_item'         => esc_html__( 'Edit Taxonomy ' . $i, 'ova-brw' ),
						'update_item'       => esc_html__( 'Update Taxonomy ' . $i, 'ova-brw' ),
						'add_new_item'      => esc_html__( 'Add New Taxonomy ' . $i, 'ova-brw' ),
						'new_item_name'     => esc_html__( 'New Taxonomy ' . $i .' Name', 'ova-brw' ),
						'menu_name'         => esc_html__( 'Custom Taxonomy ' . $i, 'ova-brw' ),
						'type'         		=> 'taxonomy_default' . $i
					];

					$args = [
						'hierarchical'      => true,
						'labels'            => $labels,
						'show_ui'           => true,
						'show_admin_column' => false,
						'query_var'         => true,
						'rewrite'           => [ 'slug' => 'taxonomy_default' . $i ]
					];
				} else {
					$labels = [
						'name'              => _x( $param_arr['name'], 'taxonomy general name', 'ova-brw' ),
						'singular_name'     => _x( $param_arr['slug'], 'taxonomy singular name', 'ova-brw' ),
						'search_items'      => esc_html__( 'Search ' . $param_arr['name'], 'ova-brw' ),
						'all_items'         => esc_html__( 'All ' . $param_arr['name'], 'ova-brw' ),
						'parent_item'       => esc_html__( 'Parent ' . $param_arr['name'], 'ova-brw' ),
						'parent_item_colon' => esc_html__( 'Parent ' . $param_arr['name'] .': ', 'ova-brw' ),
						'edit_item'         => esc_html__( 'Edit ' . $param_arr['name'], 'ova-brw' ),
						'update_item'       => esc_html__( 'Update ' . $param_arr['name'], 'ova-brw' ),
						'add_new_item'      => esc_html__( 'Add New ' . $param_arr['name'], 'ova-brw' ),
						'new_item_name'     => esc_html__( 'New ' . $param_arr['name'] , 'ova-brw' ),
						'menu_name'         => esc_html__( $param_arr['name'], 'ova-brw' ),
						'type'         		=> $param_arr['slug']
					];

					$args = [
						'hierarchical'      => true,
						'labels'            => $labels,
						'show_ui'           => true,
						'show_admin_column' => false,
						'query_var'         => true,
						'rewrite'           => [ 'slug' => $param_arr['slug'] ]
					];
				}

				$name_taxonomy[$i]['slug'] = $args['labels']['type'];
				$name_taxonomy[$i]['name'] = $args['labels']['name'];

				register_taxonomy( $args['labels']['type'], [ 'product' ], $args );
			}
		}

		// Name taxonomy
		$name_taxonomy = array_merge_recursive( $name_taxonomy, $new_taxonomies);

		return apply_filters( OVABRW_PREFIX.'create_type_taxonomies', $name_taxonomy );
	}
}

/**
 * Dump & die
 */
if ( !function_exists( 'dd' ) ) {
    function dd( ...$args ) {
        echo '<pre>';
        var_dump( ...$args );
        echo '</pre>';
        die;
    }
}

/**
 * Get locate_template
 */
if ( !function_exists( 'ovabrw_locate_template' ) ) {
	function ovabrw_locate_template( $template_name = '', $template_path = '', $default_path = '' ) {
		// Set variable to search in ovabrw-templates folder of theme.
		if ( ! $template_path ) :
			$template_path = 'ovabrw-templates/';
		endif;

		// Set default plugin templates path.
		if ( ! $default_path ) :
			$default_path = OVABRW_PLUGIN_PATH . 'ovabrw-templates/'; // Path to the template folder
		endif;

		// Search template file in theme folder.
		$template = locate_template([ $template_path . $template_name ]);

		// Get plugins template file.
		if ( ! $template ) :
			$template = $default_path . $template_name;
		endif;

		return apply_filters( OVABRW_PREFIX.'locate_template', $template, $template_name, $template_path, $default_path );
	}
}

/**
 * Get template
 */
if ( !function_exists( 'ovabrw_get_template' ) ) {
	function ovabrw_get_template( $template_name = '', $args = [], $tempate_path = '', $default_path = '' ) {
		if ( is_array( $args ) && isset( $args ) ) {
			extract( $args );
		}

		$template_file = ovabrw_locate_template( $template_name, $tempate_path, $default_path );
		if ( ! file_exists( $template_file ) ) :
			_doing_it_wrong( __FUNCTION__, sprintf( '<code>%s</code> does not exist.', $template_file ), '1.0.0' );
			return;
		endif;

		include $template_file;
	}
}

/**
 * Check array exists
 */
if ( !function_exists( 'ovabrw_array_exists' ) ) {
	function ovabrw_array_exists( $arr ) {
		if ( !empty( $arr ) && is_array( $arr ) ) {
			return true;
		}

		return false;
	}
}

/**
 * Require once
 */
if ( !function_exists( 'ovabrw_require_once' ) ) {
	function ovabrw_require_once( $files = [] ) {
		if ( ovabrw_array_exists( $files ) ) {
			foreach ( $files as $file ) {
				if ( file_exists( $file ) ) {
					try {
						require_once $file;
					} catch ( Exception $e ) {
						return;
					}
				}
			}
		}
	}
}

/**
 * Autoload
 */
if ( !function_exists( 'ovabrw_autoload' ) ) {
	function ovabrw_autoload( $pattern = '' ) {
		if ( $pattern ) {
			ovabrw_require_once( glob( $pattern ) );
		}
	}
}

/**
 * Random unique id
 */
if ( !function_exists( 'ovabrw_unique_id' ) ) {
	function ovabrw_unique_id( $id = '' ) {
		$unique_id = OVABRW_PREFIX.$id . '_' . time() . '_' . mt_rand();

		return apply_filters( OVABRW_PREFIX.'unique_id', $unique_id, $id );
	}
}

/**
 * Selected
 */
if ( !function_exists( 'ovabrw_selected' ) ) {
	function ovabrw_selected( $value = '', $options = [], $display = true ) {
		$result = '';

		if ( is_array( $options ) ) {
			$options = array_map( 'strval', $options );
			$result  = selected( in_array( (string)$value, $options, true ), true, $display );
		} else {
			$result = selected( $value, $options, $display );
		}

		if ( $display ) {
			echo esc_html( $result );
		}

		return apply_filters( OVABRW_PREFIX.'selected', $result, $value, $options, $display );
	}
}

/**
 * Checked
 */
if ( !function_exists( 'ovabrw_checked' ) ) {
	function ovabrw_checked( $value = '', $options = [], $display = true ) {
		$result = '';

		if ( is_array( $options ) ) {
			$options = array_map( 'strval', $options );
			$result  = checked( in_array( (string) $value, $options, true ), true, $display );
		} else {
			$result = checked( $value, $options, $display );
		}

		if ( $display ) {
			echo esc_html( $result );
		}

		return apply_filters( OVABRW_PREFIX.'checked', $result, $value, $options, $display );
	}
}

/**
 * Disabled
 */
if ( !function_exists( 'ovabrw_disabled' ) ) {
	function ovabrw_disabled( $value = '', $options = [], $display = true ) {
		$result = '';

		if ( is_array( $options ) ) {
			$options = array_map( 'strval', $options );
			$result  = disabled( in_array( (string)$value, $options, true ), true, $display );
		} else {
			$result = disabled( $value, $options, $display );
		}

		if ( $display ) {
			echo esc_html( $result );
		}

		return apply_filters( OVABRW_PREFIX.'disabled', $result, $value, $options, $display );
	}
}

/**
 * Recursive array replace \\
 */
if ( !function_exists( 'ovabrw_replace' ) ) {
    function ovabrw_replace( $find, $replace, $array ) {
        if ( !is_array( $array ) ) {
            return str_replace( $find, $replace, $array );
        }

        foreach ( $array as $key => $value ) {
            $array[$key] = ovabrw_replace( $find, $replace, $value );
        }

        return apply_filters( OVABRW_PREFIX.'replace', $array, $find, $replace );
    }
}

/**
 * Format price
 */
if ( !function_exists( 'ovabrw_format_price' ) ) {
	function ovabrw_format_price( $args = [] ) {
		if ( empty( $args ) ) return $args;

		if ( !is_array( $args ) ) {
			return wc_format_decimal( $args );
		}

		foreach ( $args as $k => $v ) {
			$args[$k] = ovabrw_format_price( $v );
		}

		return apply_filters( OVABRW_PREFIX.'format_price', $args );
	}
}

/**
 * Format date
 */
if ( !function_exists( 'ovabrw_format_date' ) ) {
	function ovabrw_format_date( $args = null ) {
		if ( !$args ) return $args;
		if ( !is_array( $args ) ) {
			return strtotime( $args ) ? $args : '';
		}

		foreach ( $args as $k => $v ) {
			$args[$k] = ovabrw_format_date( $v );
		}

		return apply_filters( OVABRW_PREFIX.'format_date', $args );
	}
}

/**
 * Format number
 */
if ( !function_exists( 'ovabrw_format_number' ) ) {
	function ovabrw_format_number( $args = null ) {
		if ( empty( $args ) ) return $args;
		if ( !is_array( $args ) ) {
			return absint( $args );
		}

		foreach ( $args as $k => $v ) {
			$args[$k] = ovabrw_format_number( $v );
		}

		return apply_filters( OVABRW_PREFIX.'format_number', $args );
	}
}

/**
 * Sanitize title
 */
if ( !function_exists( 'ovabrw_sanitize_title' ) ) {
	function ovabrw_sanitize_title( $args = [] ) {
		if ( empty( $args ) ) return $args;

		if ( !is_array( $args ) ) {
			return sanitize_title( $args );
		}

		foreach ( $args as $k => $v ) {
			$args[$k] = ovabrw_sanitize_title( $v );
		}

		return apply_filters( OVABRW_PREFIX.'sanitize_title', $args );
	}
}

/**
 * Round a number
 */
if ( !function_exists( 'ovabrw_round' ) ) {
	function ovabrw_round( $val, int $precision = 0, int $mode = PHP_ROUND_HALF_UP ) {
		if ( !is_numeric( $val ) ) {
			$val = floatval( $val );
		}

		return apply_filters( OVABRW_PREFIX.'round', round( $val, $precision, $mode ), $val, $precision, $mode );
	}
}

/**
 * Merge unique
 */
if ( !function_exists( 'ovabrw_array_merge_unique' ) ) {
	function ovabrw_array_merge_unique( ...$arrays ) {
		$valid_arrays = [];

		// Filter out non-array elements
	    foreach ( $arrays as $array ) {
	        if ( is_array( $array ) ) {
	            $valid_arrays[] = $array;
	        }
	    }

	    // Merge valid arrays
	    $merged_array = array_merge(...$valid_arrays );
	    $unique_array = array_unique( $merged_array );

	    // Reindex array keys
	    $unique_array = array_values( $unique_array );

	    return apply_filters( OVABRW_PREFIX.'array_merge_unique', $unique_array, ...$arrays );
	}
}

/**
 * Get option
 */
if ( !function_exists( 'ovabrw_get_option' ) ) {
	function ovabrw_get_option( $name, $default = false, $prefix = OVABRW_PREFIX ) {
		$value = '';

		// Get option value
		if ( $name ) {
			$value = get_option( $prefix.$name );
		}

		// Set defalt
		if ( '' == $value && $default !== false ) {
			$value = $default;
		}

		return apply_filters( OVABRW_PREFIX.'get_option', $value, $name, $default, $prefix );
	}
}

/**
 * Get option setting
 */
if ( !function_exists( 'ovabrw_get_setting' ) ) {
	function ovabrw_get_setting( $name, $default = false, $prefix = OVABRW_PREFIX_OPTIONS ) {
		$value = '';

		// Get option value
		if ( $name ) {
			$value = get_option( $prefix.$name );
		}

		// Set defalt
		if ( '' == $value && $default !== false ) {
			$value = $default;
		}

		return apply_filters( OVABRW_PREFIX.'get_setting', $value, $name, $default, $prefix );
	}
}

/**
 * Date format
 */
if ( !function_exists( 'ovabrw_date_format' ) ) {
	function ovabrw_date_format() {
		return (array)apply_filters( OVABRW_PREFIX.'date_format', [
			'd-m-Y' => sprintf( esc_html__( 'dd-mm-yyyy (%s)', 'ova-brw' ), gmdate( 'd-m-Y', current_time( 'timestamp' ) ) ),
			'm/d/Y' => sprintf( esc_html__( 'mm/dd/yyyy (%s)', 'ova-brw' ), gmdate( 'm/d/Y', current_time( 'timestamp' ) ) ),
			'Y/m/d' => sprintf( esc_html__( 'yyyy/mm/dd (%s)', 'ova-brw' ), gmdate( 'Y/m/d', current_time( 'timestamp' ) ) ),
			'Y-m-d' => sprintf( esc_html__( 'yyyy-mm-dd (%s)', 'ova-brw' ), gmdate( 'Y-m-d', current_time( 'timestamp' ) ) )
		]);
	}
}

/**
 * Time format
 */
if ( !function_exists( 'ovabrw_time_format' ) ) {
	function ovabrw_time_format() {
		return (array)apply_filters( OVABRW_PREFIX.'time_format', [
			'H:i' 	=> esc_html__( 'H:i (24 hour time)', 'ova-brw' ),
			'h:i a' => sprintf( esc_html__( 'h:i a (%s)', 'ova-brw' ), gmdate( 'h:i a', current_time( 'timestamp' ) ) ),
			'h:i A' => sprintf( esc_html__( 'h:i A (%s)', 'ova-brw' ), gmdate( 'h:i A', current_time( 'timestamp' ) ) ),
			'g:i a' => sprintf( esc_html__( 'g:i a (%s)', 'ova-brw' ), gmdate( 'g:i a', current_time( 'timestamp' ) ) ),
			'g:i A' => sprintf( esc_html__( 'g:i A (%s)', 'ova-brw' ), gmdate( 'g:i A', current_time( 'timestamp' ) ) )
		]);
	}
}

/**
 * Get date range
 */
if ( !function_exists( 'ovabrw_get_date_range' ) ) {
	function ovabrw_get_date_range( $start, $end, $format = 'Y-m-d H:i' ) {
		// init
		$dates = [];

		if ( is_numeric( $start ) && is_numeric( $end ) ) {
			while ( $start <= $end ) {
		        array_push( $dates, gmdate( $format, $start ) );
		        $start += 86400;
		    }
		}

		return apply_filters( OVABRW_PREFIX.'get_date_range', $dates, $start, $end, $format );
	}
}

/**
 * Get number of days between
 */
if ( !function_exists( 'ovabrw_numberof_days_between' ) ) {
	function ovabrw_numberof_days_between( $start, $end ) {
		// init
		$numberof_days = 0;

		if ( is_numeric( $start ) && is_numeric( $end ) ) {
			$numberof_days = floor( abs( $end - $start ) / 86400 );
		}

		return apply_filters( OVABRW_PREFIX.'numberof_days_between', $numberof_days, $start, $end );
	}
}

/**
 * Get meta data
 */
if ( !function_exists( 'ovabrw_get_meta_data' ) ) {
	function ovabrw_get_meta_data( $key = '', $args = [], $default = false ) {
		$value = '';

		if ( '' !== $key && ovabrw_array_exists( $args ) ) {
			$value = isset( $args[$key] ) ? $args[$key] : '';
		}

		// Set default
		if ( empty( $value ) && false !== $default ) {
			$value = $default;
		}

		return apply_filters( OVABRW_PREFIX.'get_meta_data', $value, $key, $args, $default );
	}
}

/**
 * Get meta key
 */
if ( !function_exists( 'ovabrw_meta_key' ) ) {
	function ovabrw_meta_key( $key = '', $display = false ) {
        if ( $key ) $key = OVABRW_PREFIX.$key;

        if ( $display ) {
        	echo esc_attr( $key );
        }

        return apply_filters( OVABRW_PREFIX.'meta_key', $key );
    }
}

/**
 * Get post meta
 */
if ( !function_exists( 'ovabrw_get_post_meta' ) ) {
	function ovabrw_get_post_meta( $id = null, $name = '', $default = false ) {
		$value = '';

		if ( $id && $name ) {
			$value = get_post_meta( $id, OVABRW_PREFIX.$name, true );

			if ( empty( $value ) && $default !== false ) {
				$value = $default;
			}
		}

		return apply_filters( OVABRW_PREFIX.'get_post_meta', $value, $id, $name, $default );
	}
}

/**
 * Get predefined ranges
 */
if ( !function_exists( 'ovabrw_get_predefined_ranges' ) ) {
	function ovabrw_get_predefined_ranges() {
		$today 			= esc_html__( 'Today', 'ova-brw' );
		$next_day 		= esc_html__( 'Next Day', 'ova-brw' );
		$this_week 		= esc_html__( 'This Week', 'ova-brw' );
		$next_week 		= esc_html__( 'Next Week', 'ova-brw' );
		$this_month 	= esc_html__( 'This Month', 'ova-brw' );
		$next_month 	= esc_html__( 'Next Month', 'ova-brw' );
		$date_format 	= 'Y-m-d';
		$start_of_week 	= get_option( 'ova_brw_calendar_first_day', 1 );
		$day_of_week 	= gmdate('l', strtotime("Sunday +{$start_of_week} days"));
		// Next month
		$month_number 	= gmdate( 'm', strtotime( 'next month' ) );
		$year_number 	= gmdate( 'Y', strtotime( 'next month' ) );
		$day_number 	= gmdate( 't', strtotime( 'next month' ) );

		// Predefined ranges
		$predefined_ranges = [
			$today 		=> [
				gmdate( $date_format ),
				gmdate( $date_format )
			],
			$next_day 	=> [
				gmdate( $date_format, strtotime('+1 day') ),
				gmdate( $date_format, strtotime('+1 day') )
			],
			$this_week 	=> [
				gmdate( $date_format, strtotime( "last {$day_of_week}" ) ),
				gmdate( $date_format, strtotime( "next {$day_of_week} -1 day" ) )
			],
			$next_week 	=> [
				gmdate( $date_format, strtotime( "next {$day_of_week}" ) ),
				gmdate( $date_format, strtotime( "next {$day_of_week} +6 days" ))
			],
			$this_month => [
				gmdate( $date_format, strtotime( gmdate('Y').'-'.gmdate('m').'-01') ),
				gmdate( $date_format, strtotime( gmdate('Y').'-'.gmdate('m').'-'.gmdate('t') ) )
			],
			$next_month => [
				gmdate( $date_format, strtotime( $year_number.'-'.$month_number.'-01') ),
				gmdate( $date_format, strtotime( $year_number.'-'.$month_number.'-'.$day_number ) )
			]
		];

		return apply_filters( OVABRW_PREFIX.'get_predefined_ranges', $predefined_ranges );
	}
}

/**
 * Validation messages
 */
if ( !function_exists( 'ovabrw_get_validation_messages' ) ) {
	function ovabrw_get_validation_messages() {
		return (array) apply_filters( OVABRW_PREFIX.'get_validation_messages', [
			'dateFormat' 	=> OVABRW()->options->get_date_format(),
			'required' 		=> esc_html__( 'This field is required.', 'ova-brw' ),
			'duplicateID' 	=> esc_html__( 'Duplicate ID.', 'ova-brw' ),
			'duplicateTime' => esc_html__( 'Duplicate time.', 'ova-brw' ),
			'duplicateDate' => esc_html__( 'Duplicate date.', 'ova-brw' ),
			'conditions' 	=> esc_html__( 'Please read and accept the terms and conditions to continue.', 'ova-brw' ),
			'reCAPTCHA'  	=> esc_html__( 'Please verify that you are not a robot.', 'ova-brw' ),
			'email' 		=> esc_html__( 'Please enter a valid email address.', 'ova-brw' ),
			'phone' 		=> esc_html__( 'Please enter a valid phone number.', 'ova-brw' ),
			'url' 			=> esc_html__( 'Please enter a valid URL.', 'ova-brw' ),
			'date' 			=> esc_html__( 'Please enter a valid date.', 'ova-brw' ),
			'number' 		=> esc_html__( 'Please enter a valid number.', 'ova-brw' ),
			'digits' 		=> esc_html__( 'Please enter only digits.', 'ova-brw' ),
			'size' 			=> esc_html__( 'Please select a file less than [size] MB.' ),
			'fileType' 		=> esc_html__( 'Invalid file type. Please upload an image file (.jpg, .jpeg, .png, .pdf, .doc).', 'ova-brw' ),
			'min' 			=> esc_html__( 'Please enter a value greater than or equal to [number].', 'ova-brw' ),
			'max' 			=> esc_html__( 'Please enter a value less than or equal to [number].', 'ova-brw' ),
			'minQty' 		=> esc_html__( 'Minimum quantity: [number].', 'ova-brw' ),
			'maxQty' 		=> esc_html__( 'Maximum quantity: [number].', 'ova-brw' ),
			'minGuests' 	=> esc_html__( 'Minimum number of guests: [number].', 'ova-brw' ),
			'maxGuests' 	=> esc_html__( 'Maximum number of guests: [number].', 'ova-brw' ),
			'minGuest' 		=> esc_html__( 'Minimum number of [guest]: [number].', 'ova-brw' ),
			'maxGuest' 		=> esc_html__( 'Maximum number of [guest]: [number].', 'ova-brw' ),
			'mapAPI' 		=> esc_html__( 'This page can\'t load Google Maps properly.', 'ovabrw-' ),

			// Custom checkout fields
			'placeholderText' 	=> esc_html__( 'text', 'ova-brw' ),
			'placeholderNumber' => esc_html__( 'number', 'ova-brw' ),
			'btnAddNew' 		=> esc_html__( 'Add new option', 'ova-brw' ),
			'btnRemove' 		=> esc_html__( 'Remove option', 'ova-brw' ),
			'confirmText' 		=> esc_html__( 'Are you sure?', 'ova-brw' ),
			'disabledMesg' 		=> esc_html__( 'You can\'t book on this day!', 'ova-brw' ),

			// Global color
			'lightColor' 	=> ovabrw_get_option( 'glb_light_color', '#C3C3C3' ),
			'textColor' 	=> ovabrw_get_option( 'glb_text_color', '#555555' ),
		]);
	}
}

/**
 * Rental type
 */
if ( !function_exists( 'ovabrw_rental_selector' ) ) {
	function ovabrw_rental_selector() {
		return (array)apply_filters( OVABRW_PREFIX.'rental_selector', [
			'day'				=> esc_html__( '1: Day', 'ova-brw' ),
			'hour'				=> esc_html__( '2: Hour', 'ova-brw' ),
			'mixed'				=> esc_html__( '3: Mixed (Day and Hour)', 'ova-brw' ),
			'period_time' 		=> esc_html__( '4: Period of Time ( 05:00 am - 10:00 am, 1 day, 2 days, 1 month, 6 months, 1 year... )', 'ova-brw' ),
			'transportation' 	=> esc_html__( '5: Transportation', 'ova-brw' ),
			'taxi' 				=> esc_html__( '6: Taxi', 'ova-brw' ),
			'hotel' 			=> esc_html__( '7: Hotel', 'ova-brw' ),
			'appointment' 		=> esc_html__( '8: Appointment', 'ova-brw' )
		]);
	}
}

/**
 * Get countries - ISO 3166-1 alpha-2 codes
 */
if ( !function_exists( 'ovabrw_iso_alpha2' ) ) {
	function ovabrw_iso_alpha2() {
		$countries = [
		    'AD' => esc_html__( 'Andorra', 'ova-brw' ),
		    'AE' => esc_html__( 'United Arab Emirates', 'ova-brw' ),
		    'AF' => esc_html__( 'Afghanistan', 'ova-brw' ),
		    'AG' => esc_html__( 'Antigua and Barbuda', 'ova-brw' ),
		    'AI' => esc_html__( 'Anguilla', 'ova-brw' ),
		    'AL' => esc_html__( 'Albania', 'ova-brw' ),
		    'AM' => esc_html__( 'Armenia', 'ova-brw' ),
		    'AO' => esc_html__( 'Angola', 'ova-brw' ),
		    'AQ' => esc_html__( 'Antarctica', 'ova-brw' ),
		    'AR' => esc_html__( 'Argentina', 'ova-brw' ),
		    'AS' => esc_html__( 'American Samoa', 'ova-brw' ),
		    'AT' => esc_html__( 'Austria', 'ova-brw' ),
		    'AU' => esc_html__( 'Australia', 'ova-brw' ),
		    'AW' => esc_html__( 'Aruba', 'ova-brw' ),
		    'AX' => esc_html__( 'Åland Islands', 'ova-brw' ),
		    'AZ' => esc_html__( 'Azerbaijan', 'ova-brw' ),
		    'BA' => esc_html__( 'Bosnia and Herzegovina', 'ova-brw' ),
		    'BB' => esc_html__( 'Barbados', 'ova-brw' ),
		    'BD' => esc_html__( 'Bangladesh', 'ova-brw' ),
		    'BE' => esc_html__( 'Belgium', 'ova-brw' ),
		    'BF' => esc_html__( 'Burkina Faso', 'ova-brw' ),
		    'BG' => esc_html__( 'Bulgaria', 'ova-brw' ),
		    'BH' => esc_html__( 'Bahrain', 'ova-brw' ),
		    'BI' => esc_html__( 'Burundi', 'ova-brw' ),
		    'BJ' => esc_html__( 'Benin', 'ova-brw' ),
		    'BL' => esc_html__( 'Saint Barthélemy', 'ova-brw' ),
		    'BM' => esc_html__( 'Bermuda', 'ova-brw' ),
		    'BN' => esc_html__( 'Brunei Darussalam', 'ova-brw' ),
		    'BO' => esc_html__( 'Bolivia (Plurinational State of)', 'ova-brw' ),
		    'BQ' => esc_html__( 'Bonaire, Sint Eustatius and Saba', 'ova-brw' ),
		    'BR' => esc_html__( 'Brazil', 'ova-brw' ),
		    'BS' => esc_html__( 'Bahamas', 'ova-brw' ),
		    'BT' => esc_html__( 'Bhutan', 'ova-brw'),
		    'BV' => esc_html__( 'Bouvet Island', 'ova-brw' ),
		    'BW' => esc_html__( 'Botswana', 'ova-brw' ),
		    'BY' => esc_html__( 'Belarus', 'ova-brw' ),
		    'BZ' => esc_html__( 'Belize', 'ova-brw' ),
		    'CA' => esc_html__( 'Canada', 'ova-brw' ),
		    'CC' => esc_html__( 'Cocos (Keeling) Islands', 'ova-brw' ),
		    'CD' => esc_html__( 'Congo, Democratic Republic of the', 'ova-brw' ),
		    'CF' => esc_html__( 'Central African Republic', 'ova-brw' ),
		    'CG' => esc_html__( 'Congo', 'ova-brw' ),
		    'CH' => esc_html__( 'Switzerland', 'ova-brw' ),
		    'CI' => esc_html__( 'Côte d\'Ivoire', 'ova-brw' ),
		    'CK' => esc_html__( 'Cook Islands', 'ova-brw' ),
		    'CL' => esc_html__( 'Chile', 'ova-brw' ),
		    'CM' => esc_html__( 'Cameroon', 'ova-brw' ),
		    'CN' => esc_html__( 'China', 'ova-brw' ),
		    'CO' => esc_html__( 'Colombia', 'ova-brw' ),
		    'CR' => esc_html__( 'Costa Rica', 'ova-brw' ),
		    'CU' => esc_html__( 'Cuba', 'ova-brw' ),
		    'CV' => esc_html__( 'Cabo Verde', 'ova-brw' ),
		    'CW' => esc_html__( 'Curaçao', 'ova-brw' ),
		    'CX' => esc_html__( 'Christmas Island', 'ova-brw' ),
		    'CY' => esc_html__( 'Cyprus', 'ova-brw' ),
		    'CZ' => esc_html__( 'Czechia', 'ova-brw' ),
		    'DE' => esc_html__( 'Germany', 'ova-brw' ),
		    'DJ' => esc_html__( 'Djibouti', 'ova-brw' ),
		    'DK' => esc_html__( 'Denmark', 'ova-brw' ),
		    'DM' => esc_html__( 'Dominica', 'ova-brw' ),
		    'DO' => esc_html__( 'Dominican Republic', 'ova-brw' ),
		    'DZ' => esc_html__( 'Algeria', 'ova-brw' ),
		    'EC' => esc_html__( 'Ecuador', 'ova-brw' ),
		    'EE' => esc_html__( 'Estonia', 'ova-brw' ),
		    'EG' => esc_html__( 'Egypt', 'ova-brw' ),
		    'EH' => esc_html__( 'Western Sahara', 'ova-brw' ),
		    'ER' => esc_html__( 'Eritrea', 'ova-brw' ),
		    'ES' => esc_html__( 'Spain', 'ova-brw' ),
		    'ET' => esc_html__( 'Ethiopia', 'ova-brw' ),
		    'FI' => esc_html__( 'Finland', 'ova-brw' ),
		    'FJ' => esc_html__( 'Fiji', 'ova-brw' ),
		    'FK' => esc_html__( 'Falkland Islands (Malvinas)', 'ova-brw' ),
		    'FM' => esc_html__( 'Micronesia (Federated States of)', 'ova-brw' ),
		    'FO' => esc_html__( 'Faroe Islands', 'ova-brw' ),
		    'FR' => esc_html__( 'France', 'ova-brw' ),
		    'GA' => esc_html__( 'Gabon', 'ova-brw' ),
		    'GB' => esc_html__( 'United Kingdom of Great Britain and Northern Ireland', 'ova-brw' ),
		    'GD' => esc_html__( 'Grenada', 'ova-brw' ),
		    'GE' => esc_html__( 'Georgia', 'ova-brw' ),
		    'GF' => esc_html__( 'French Guiana', 'ova-brw' ),
		    'GG' => esc_html__( 'Guernsey', 'ova-brw' ),
		    'GH' => esc_html__( 'Ghana', 'ova-brw' ),
		    'GI' => esc_html__( 'Gibraltar', 'ova-brw' ),
		    'GL' => esc_html__( 'Greenland', 'ova-brw' ),
		    'GM' => esc_html__( 'Gambia', 'ova-brw' ),
		    'GN' => esc_html__( 'Guinea', 'ova-brw' ),
		    'GP' => esc_html__( 'Guadeloupe', 'ova-brw' ),
		    'GQ' => esc_html__( 'Equatorial Guinea', 'ova-brw' ),
		    'GR' => esc_html__( 'Greece', 'ova-brw' ),
		    'GS' => esc_html__( 'South Georgia and the South Sandwich Islands', 'ova-brw' ),
		    'GT' => esc_html__( 'Guatemala', 'ova-brw' ),
		    'GU' => esc_html__( 'Guam', 'ova-brw' ),
		    'GW' => esc_html__( 'Guinea-Bissau', 'ova-brw' ),
		    'GY' => esc_html__( 'Guyana', 'ova-brw' ),
		    'HK' => esc_html__( 'Hong Kong', 'ova-brw' ),
		    'HM' => esc_html__( 'Heard Island and McDonald Islands', 'ova-brw' ),
		    'HN' => esc_html__( 'Honduras', 'ova-brw' ),
		    'HR' => esc_html__( 'Croatia', 'ova-brw' ),
		    'HT' => esc_html__( 'Haiti', 'ova-brw' ),
		    'HU' => esc_html__( 'Hungary', 'ova-brw' ),
		    'ID' => esc_html__( 'Indonesia', 'ova-brw' ),
		    'IE' => esc_html__( 'Ireland', 'ova-brw' ),
		    'IL' => esc_html__( 'Israel', 'ova-brw' ),
		    'IM' => esc_html__( 'Isle of Man', 'ova-brw' ),
		    'IN' => esc_html__( 'India', 'ova-brw' ),
		    'IO' => esc_html__( 'British Indian Ocean Territory', 'ova-brw' ),
		    'IQ' => esc_html__( 'Iraq', 'ova-brw'),
		    'IR' => esc_html__( 'Iran (Islamic Republic of)', 'ova-brw' ),
		    'IS' => esc_html__( 'Iceland', 'ova-brw' ),
		    'IT' => esc_html__( 'Italy', 'ova-brw' ),
		    'JE' => esc_html__( 'Jersey', 'ova-brw' ),
		    'JM' => esc_html__( 'Jamaica', 'ova-brw' ),
		    'JO' => esc_html__( 'Jordan', 'ova-brw' ),
		    'JP' => esc_html__( 'Japan', 'ova-brw' ),
		    'KE' => esc_html__( 'Kenya', 'ova-brw' ),
		    'KG' => esc_html__( 'Kyrgyzstan', 'ova-brw' ),
		    'KH' => esc_html__( 'Cambodia', 'ova-brw' ),
		    'KI' => esc_html__( 'Kiribati', 'ova-brw' ),
		    'KM' => esc_html__( 'Comoros', 'ova-brw' ),
		    'KN' => esc_html__( 'Saint Kitts and Nevis', 'ova-brw' ),
		    'KP' => esc_html__( 'Korea (Democratic People\'s Republic of)', 'ova-brw' ),
		    'KR' => esc_html__( 'Korea, Republic of', 'ova-brw' ),
		    'KW' => esc_html__( 'Kuwait', 'ova-brw' ),
		    'KY' => esc_html__( 'Cayman Islands', 'ova-brw' ),
		    'KZ' => esc_html__( 'Kazakhstan', 'ova-brw' ),
		    'LA' => esc_html__( 'Lao People\'s Democratic Republic', 'ova-brw' ),
		    'LB' => esc_html__( 'Lebanon', 'ova-brw' ),
		    'LC' => esc_html__( 'Saint Lucia', 'ova-brw' ),
		    'LI' => esc_html__( 'Liechtenstein', 'ova-brw' ),
		    'LK' => esc_html__( 'Sri Lanka', 'ova-brw' ),
		    'LR' => esc_html__( 'Liberia', 'ova-brw' ),
		    'LS' => esc_html__( 'Lesotho', 'ova-brw' ),
		    'LT' => esc_html__( 'Lithuania', 'ova-brw' ),
		    'LU' => esc_html__( 'Luxembourg', 'ova-brw' ),
		    'LV' => esc_html__( 'Latvia', 'ova-brw' ),
		    'LY' => esc_html__( 'Libya', 'ova-brw' ),
		    'MA' => esc_html__( 'Morocco', 'ova-brw' ),
		    'MC' => esc_html__( 'Monaco', 'ova-brw' ),
		    'MD' => esc_html__( 'Moldova, Republic of', 'ova-brw' ),
		    'ME' => esc_html__( 'Montenegro', 'ova-brw'),
		    'MF' => esc_html__( 'Saint Martin (French part)', 'ova-brw' ),
		    'MG' => esc_html__( 'Madagascar', 'ova-brw' ),
		    'MH' => esc_html__( 'Marshall Islands', 'ova-brw' ),
		    'MK' => esc_html__( 'North Macedonia', 'ova-brw' ),
		    'ML' => esc_html__( 'Mali', 'ova-brw' ),
		    'MM' => esc_html__( 'Myanmar', 'ova-brw' ),
		    'MN' => esc_html__( 'Mongolia', 'ova-brw' ),
		    'MO' => esc_html__( 'Macao', 'ova-brw'),
		    'MP' => esc_html__( 'Northern Mariana Islands', 'ova-brw' ),
		    'MQ' => esc_html__( 'Martinique', 'ova-brw' ),
		    'MR' => esc_html__( 'Mauritania', 'ova-brw' ),
		    'MS' => esc_html__( 'Montserrat', 'ova-brw' ),
		    'MT' => esc_html__( 'Malta', 'ova-brw' ),
		    'MU' => esc_html__( 'Mauritius', 'ova-brw' ),
		    'MV' => esc_html__( 'Maldives', 'ova-brw' ), 
		    'MW' => esc_html__( 'Malawi', 'ova-brw' ),
		    'MX' => esc_html__( 'Mexico', 'ova-brw' ),
		    'MY' => esc_html__( 'Malaysia', 'ova-brw' ),
		    'MZ' => esc_html__( 'Mozambique', 'ova-brw' ),
		    'NA' => esc_html__( 'Namibia', 'ova-brw' ),
		    'NC' => esc_html__( 'New Caledonia', 'ova-brw' ),
		    'NE' => esc_html__( 'Niger', 'ova-brw' ),
		    'NF' => esc_html__( 'Norfolk Island', 'ova-brw' ),
		    'NG' => esc_html__( 'Nigeria', 'ova-brw' ),
		    'NI' => esc_html__( 'Nicaragua', 'ova-brw' ),
		    'NL' => esc_html__( 'Netherlands, Kingdom of the', 'ova-brw' ),
		    'NO' => esc_html__( 'Norway', 'ova-brw' ),
		    'NP' => esc_html__( 'Nepal', 'ova-brw' ),
		    'NR' => esc_html__( 'Nauru', 'ova-brw' ),
		    'NU' => esc_html__( 'Niue', 'ova-brw' ),
		    'NZ' => esc_html__( 'New Zealand', 'ova-brw' ),
		    'OM' => esc_html__( 'Oman', 'ova-brw' ),
		    'PA' => esc_html__( 'Panama', 'ova-brw' ),
		    'PE' => esc_html__( 'Peru', 'ova-brw' ),
		    'PF' => esc_html__( 'French Polynesia', 'ova-brw' ),
		    'PG' => esc_html__( 'Papua New Guinea', 'ova-brw' ),
		    'PH' => esc_html__( 'Philippines', 'ova-brw' ),
		    'PK' => esc_html__( 'Pakistan', 'ova-brw' ),
		    'PL' => esc_html__( 'Poland', 'ova-brw' ),
		    'PM' => esc_html__( 'Saint Pierre and Miquelon', 'ova-brw' ),
		    'PN' => esc_html__( 'Pitcairn', 'ova-brw' ),
		    'PR' => esc_html__( 'Puerto Rico', 'ova-brw' ),
		    'PS' => esc_html__( 'Palestine, State of', 'ova-brw' ),
		    'PT' => esc_html__( 'Portugal', 'ova-brw' ),
		    'PW' => esc_html__( 'Palau', 'ova-brw' ),
		    'PY' => esc_html__( 'Paraguay', 'ova-brw' ),
		    'QA' => esc_html__( 'Qatar', 'ova-brw' ),
		    'RE' => esc_html__( 'Réunion', 'ova-brw' ),
		    'RO' => esc_html__( 'Romania', 'ova-brw' ),
		    'RS' => esc_html__( 'Serbia', 'ova-brw' ),
		    'RU' => esc_html__( 'Russian Federation', 'ova-brw' ),
		    'RW' => esc_html__( 'Rwanda', 'ova-brw' ),
		    'SA' => esc_html__( 'Saudi Arabia', 'ova-brw' ),
		    'SB' => esc_html__( 'Solomon Islands', 'ova-brw' ),
		    'SC' => esc_html__( 'Seychelles', 'ova-brw' ),
		    'SD' => esc_html__( 'Sudan', 'ova-brw' ),
		    'SE' => esc_html__( 'Sweden', ' ova-brw' ),
		    'SG' => esc_html__( 'Singapore', 'ova-brw' ),
		    'SH' => esc_html__( 'Saint Helena, Ascension and Tristan da Cunha', 'ova-brw' ),
		    'SI' => esc_html__( 'Slovenia', 'ova-brw' ),
		    'SJ' => esc_html__( 'Svalbard and Jan Mayen', 'ova-brw' ),
		    'SK' => esc_html__( 'Slovakia', 'ova-brw' ),
		    'SL' => esc_html__( 'Sierra Leone', 'ova-brw' ),
		    'SM' => esc_html__( 'San Marino', 'ova-brw' ),
		    'SN' => esc_html__( 'Senegal', 'ova-brw' ),
		    'SO' => esc_html__( 'Somalia', 'ova-brw' ),
		    'SR' => esc_html__( 'Suriname', 'ova-brw' ),
		    'SS' => esc_html__( 'South Sudan', 'ova-brw' ),
		    'ST' => esc_html__( 'Sao Tome and Principe', 'ova-brw' ),
		    'SV' => esc_html__( 'El Salvador', 'ova-brw' ),
		    'SX' => esc_html__( 'Sint Maarten (Dutch part)', 'ova-brw' ),
		    'SY' => esc_html__( 'Syrian Arab Republic', 'ova-brw' ),
		    'SZ' => esc_html__( 'Eswatini', 'ova-brw'),
		    'TC' => esc_html__( 'Turks and Caicos Islands', 'ova-brw' ),
		    'TD' => esc_html__( 'Chad', 'ova-brw' ),
		    'TF' => esc_html__( 'French Southern Territories', 'ova-brw' ),
		    'TG' => esc_html__( 'Togo', 'ova-brw' ),
		    'TH' => esc_html__( 'Thailand', 'ova-brw' ),
		    'TJ' => esc_html__( 'Tajikistan', 'ova-brw' ),
		    'TK' => esc_html__( 'Tokelau', 'ova-brw' ),
		    'TL' => esc_html__( 'Timor-Leste', 'ova-brw' ),
		    'TM' => esc_html__( 'Turkmenistan', 'ova-brw' ),
		    'TN' => esc_html__( 'Tunisia', 'ova-brw' ),
		    'TO' => esc_html__( 'Tonga', 'ova-brw' ),
		    'TR' => esc_html__( 'Türkiye', 'ova-brw' ),
		    'TT' => esc_html__( 'Trinidad and Tobago', 'ova-brw' ),
		    'TV' => esc_html__( 'Tuvalu', 'ova-brw' ),
		    'TW' => esc_html__( 'Taiwan, Province of China', 'ova-brw' ),
		    'TZ' => esc_html__( 'Tanzania, United Republic of', 'ova-brw' ),
		    'UA' => esc_html__( 'Ukraine', 'ova-brw' ),
		    'UG' => esc_html__( 'Uganda', 'ova-brw' ),
		    'UM' => esc_html__( 'United States Minor Outlying Islands', 'ova-brw' ),
		    'US' => esc_html__( 'United States of America', 'ova-brw' ),
		    'UY' => esc_html__( 'Uruguay', 'ova-brw' ),
		    'UZ' => esc_html__( 'Uzbekistan', 'ova-brw' ),
		    'VA' => esc_html__( 'Holy See', 'ova-brw' ),
		    'VC' => esc_html__( 'Saint Vincent and the Grenadines', 'ova-brw' ),
		    'VE' => esc_html__( 'Venezuela (Bolivarian Republic of)', 'ova-brw' ),
		    'VG' => esc_html__( 'Virgin Islands (British)', 'ova-brw' ),
		    'VI' => esc_html__( 'Virgin Islands (U.S.)', 'ova-brw' ),
		    'VN' => esc_html__( 'Viet Nam', 'ova-brw' ),
		    'VU' => esc_html__( 'Vanuatu', 'ova-brw' ),
		    'WF' => esc_html__( 'Wallis and Futuna', 'ova-brw' ),
		    'WS' => esc_html__( 'Samoa', 'ova-brw' ),
		    'YE' => esc_html__( 'Yemen', 'ova-brw' ),
		    'YT' => esc_html__( 'Mayotte', 'ova-brw' ),
		    'ZA' => esc_html__( 'South Africa', 'ova-brw' ),
		    'ZM' => esc_html__( 'Zambia', 'ova-brw' ),
		    'ZW' => esc_html__( 'Zimbabwe', 'ova-brw' )
		];

		return (array)apply_filters( OVABRW_PREFIX.'get_countries_iso_alpha2', $countries );
	}
}

/**
 * Calendar languages
 */
if ( !function_exists( 'ovabrw_calendar_languages' ) ) {
	function ovabrw_calendar_languages() {
		return (array)apply_filters( OVABRW_PREFIX.'calendar_languages', [
			'en-GB' => esc_html__( 'English/UK', 'ova-brw' ),
			'af' 	=> esc_html__( 'Afrikaans', 'ova-brw' ),
			'ar-DZ' => esc_html__( 'Algerian Arabic', 'ova-brw' ),
			'ar' 	=> esc_html__( 'Arabic', 'ova-brw' ),
			'az' 	=> esc_html__( 'Azerbaijani', 'ova-brw' ),
			'be' 	=> esc_html__( 'Belarusian', 'ova-brw' ),
			'bg' 	=> esc_html__( 'Bulgarian', 'ova-brw' ),
			'bs' 	=> esc_html__( 'Bosnian', 'ova-brw' ),
			'ca' 	=> esc_html__( 'Inicialització', 'ova-brw' ),
			'cs' 	=> esc_html__( 'Czech', 'ova-brw' ),
			'cy-GB' => esc_html__( 'Welsh/UK', 'ova-brw' ),
			'da' 	=> esc_html__( 'Danish', 'ova-brw' ),
			'de' 	=> esc_html__( 'German', 'ova-brw' ),
			'el' 	=> esc_html__( 'Greek', 'ova-brw' ),
			'en-AU' => esc_html__( 'English/Australia', 'ova-brw' ),
			'en-NZ' => esc_html__( 'English/New Zealand', 'ova-brw' ),
			'eo' 	=> esc_html__( 'Esperanto', 'ova-brw' ),
			'es' 	=> esc_html__( 'Spanish', 'ova-brw' ),
			'et' 	=> esc_html__( 'Estonian', 'ova-brw' ),
			'eu' 	=> esc_html__( 'Karrikas-ek', 'ova-brw' ),
			'fa' 	=> esc_html__( 'Persian (Farsi)', 'ova-brw' ),
			'fi' 	=> esc_html__( 'Finnish', 'ova-brw' ),
			'fo' 	=> esc_html__( 'Faroese', 'ova-brw' ),
			'fr-CA' => esc_html__( 'Canadian-French', 'ova-brw' ),
			'fr-CH' => esc_html__( 'Swiss-French', 'ova-brw' ),
			'fr' 	=> esc_html__( 'French', 'ova-brw' ),
			'gl' 	=> esc_html__( 'Galician', 'ova-brw' ),
			'he' 	=> esc_html__( 'Hebrew', 'ova-brw' ),
			'hi' 	=> esc_html__( 'Hindi', 'ova-brw' ),
			'hr' 	=> esc_html__( 'Croatian', 'ova-brw' ),
			'hu' 	=> esc_html__( 'Hungarian', 'ova-brw' ),
			'hy' 	=> esc_html__( 'Armenian', 'ova-brw' ),
			'id' 	=> esc_html__( 'Indonesian', 'ova-brw' ),
			'is' 	=> esc_html__( 'Icelandic', 'ova-brw' ),
			'it-CH' => esc_html__( 'Italian', 'ova-brw' ),
			'ja' 	=> esc_html__( 'Japanese', 'ova-brw' ),
			'ka' 	=> esc_html__( 'Georgian', 'ova-brw' ),
			'kk' 	=> esc_html__( 'Kazakh', 'ova-brw' ),
			'km' 	=> esc_html__( 'Khmer', 'ova-brw' ),
			'ko' 	=> esc_html__( 'Korean', 'ova-brw' ),
			'ky' 	=> esc_html__( 'Kyrgyz', 'ova-brw' ),
			'lb' 	=> esc_html__( 'Luxembourgish', 'ova-brw' ),
			'lt' 	=> esc_html__( 'Lithuanian', 'ova-brw' ),
			'lv' 	=> esc_html__( 'Latvian', 'ova-brw' ),
			'mk' 	=> esc_html__( 'Macedonian', 'ova-brw' ),
			'ml' 	=> esc_html__( 'Malayalam', 'ova-brw' ),
			'ms' 	=> esc_html__( 'Malaysian', 'ova-brw' ),
			'nb' 	=> esc_html__( 'Norwegian Bokmål', 'ova-brw' ),
			'nl-BE' => esc_html__( 'Dutch (Belgium)', 'ova-brw' ),
			'nl' 	=> esc_html__( 'Dutch', 'ova-brw' ),
			'nn' 	=> esc_html__( 'Norwegian Nynorsk', 'ova-brw' ),
			'no' 	=> esc_html__( 'Norwegian', 'ova-brw' ),
			'pl' 	=> esc_html__( 'Polish', 'ova-brw' ),
			'pt-BR' => esc_html__( 'Brazilian', 'ova-brw' ),
			'pt' 	=> esc_html__( 'Portuguese', 'ova-brw' ),
			'rm' 	=> esc_html__( 'Romansh', 'ova-brw' ),
			'ro' 	=> esc_html__( 'Romanian', 'ova-brw' ),
			'ru' 	=> esc_html__( 'Russian', 'ova-brw' ),
			'sk' 	=> esc_html__( 'Slovak', 'ova-brw' ),
			'sl' 	=> esc_html__( 'Slovenian', 'ova-brw' ),
			'sq' 	=> esc_html__( 'Albanian', 'ova-brw' ),
			'sr' 	=> esc_html__( 'Serbian', 'ova-brw' ),
			'sv' 	=> esc_html__( 'Swedish', 'ova-brw' ),
			'ta' 	=> esc_html__( 'Tamil', 'ova-brw' ),
			'th' 	=> esc_html__( 'Thai', 'ova-brw' ),
			'tj' 	=> esc_html__( 'Tajiki', 'ova-brw' ),
			'tr' 	=> esc_html__( 'Turkish', 'ova-brw' ),
			'uk' 	=> esc_html__( 'Ukrainian', 'ova-brw' ),
			'vi' 	=> esc_html__( 'Vietnamese', 'ova-brw' ),
			'zh-CN' => esc_html__( 'Chinese', 'ova-brw' ),
			'zh-HK' => esc_html__( 'Chinese (Hong Kong)', 'ova-brw' ),
			'zh-TW' => esc_html__( 'Chinese (Taiwan)', 'ova-brw' )
		]);
	}
}

// Get page by title
if ( !function_exists( 'ovabrw_get_page_by_title' ) ) {
    function ovabrw_get_page_by_title( $page_title, $output = OBJECT, $post_type = 'page' ) {
        global $wpdb;

        if ( is_array( $post_type ) ) {
            $post_type           = esc_sql( $post_type );
            $post_type_in_string = "'" . implode( "','", $post_type ) . "'";
            $sql                 = $wpdb->prepare(
                "
                SELECT ID
                FROM $wpdb->posts
                WHERE post_title = %s
                AND post_type IN ($post_type_in_string)
            ",
                $page_title
            );
        } else {
            $sql = $wpdb->prepare(
                "
                SELECT ID
                FROM $wpdb->posts
                WHERE post_title = %s
                AND post_type = %s
            ",
                $page_title,
                $post_type
            );
        }

        $page = $wpdb->get_var( $sql );

        if ( $page ) {
            return apply_filters( OVABRW_PREFIX.'get_page_by_title', get_post( $page, $output ), $page_title, $output, $post_type );
        }

        return null;
    }
}

/**
 * Global typography
 */
if ( !function_exists( 'ovabrw_global_typography' ) ) {
	function ovabrw_global_typography() {
		if ( 'yes' === ovabrw_get_option( 'enable_global_typography', 'yes' ) ) return true;

		return false;
	}
}

/**
 * Get Cart Templates
 */
if ( !function_exists( 'ovabrw_get_card_templates' ) ) {
	function ovabrw_get_card_templates() {
		return (array)apply_filters( OVABRW_PREFIX.'get_card_templates', [
			'card1' => esc_html__( 'Card 1', 'ova-brw' ),
			'card2' => esc_html__( 'Card 2', 'ova-brw' ),
			'card3' => esc_html__( 'Card 3', 'ova-brw' ),
			'card4' => esc_html__( 'Card 4', 'ova-brw' ),
			'card5' => esc_html__( 'Card 5', 'ova-brw' ),
			'card6' => esc_html__( 'Card 6', 'ova-brw' )
		]);
	}
}

/**
 * Get all font
 */
if ( !function_exists( 'ovabrw_get_all_fonts' ) ) {
	function ovabrw_get_all_fonts() {
		$font_file 	= OVABRW_PLUGIN_URI . 'assets/libs/google_font/api/google-fonts-alphabetical.json';
        $request 	= wp_remote_get( $font_file );

        if ( is_wp_error( $request ) ) return [];

        // Get body
        $body = wp_remote_retrieve_body( $request );

        // Get content
        $content = json_decode( $body );
        if ( !isset( $content->items ) || !is_array( $content->items ) ) return [];

		// All fonts
        $all_fonts = $content->items;

        if ( '' != ovabrw_get_option( 'glb_custom_font', '' ) ) {
        	$glb_custom_font 	= str_replace( '\"', '"', ovabrw_get_option( 'glb_custom_font' ) );
            $list_custom_font 	= explode( '|', $glb_custom_font );

            foreach ( $list_custom_font as $key => $font ) {
                $cus_font 			= json_decode( $font );
                $cus_font_family 	= $cus_font['0'];
                $cus_font_weight 	= explode( ':', $cus_font['1'] );

                $all_fonts[] = json_decode( json_encode([
                	'kind'      => 'webfonts#webfont',
                    'family'    => $cus_font_family,
                    'category'  => 'sans-serif',
                    'variants'  => $cus_font_weight
                ]));
            }
        }
        
        return apply_filters( OVABRW_PREFIX.'get_all_fonts', $all_fonts );
	}
}

/**
 * is archive product
 */
if ( !function_exists( 'ovabrw_is_archive_product' ) ) {
	function ovabrw_is_archive_product() {
		if ( ovabrw_global_typography() ) {
			if ( is_product_category() ) {
				$terms = get_queried_object();

				if ( !empty( $terms ) && is_object( $terms ) ) {
					$term_id = $terms->term_id;

					if ( $term_id ) {
						$display = get_term_meta( $term_id, OVABRW_PREFIX.'cat_dis', true );

						if ( 'rental' == $display ) {
							return true;
						} else {
							return false;
						}
					}
				}
			}

			if ( is_product_taxonomy() ) {
				$display = ovabrw_get_setting( 'display_product_taxonomy', 'rental' );

				if ( 'rental' == $display ) {
					return true;
				} else {
					return false;
				}
			}

			if ( is_shop() ) {
				$display = ovabrw_get_setting( 'display_shop_page', 'rental' );

				if ( 'rental' == $display ) {
					return true;
				} else {
					return false;
				}
			}
		}

		return false;
	}
}

/**
 * Get rental product
 */
if ( !function_exists( 'ovabrw_get_rental_product' ) ) {
	function ovabrw_get_rental_product( $args = [] ) {
		global $product;

		if ( !$product || !is_object( $product ) ) {
			// Get product ID
			$product_id = ovabrw_get_meta_data( 'product_id', $args );
			if ( !$product_id ) $product_id = ovabrw_get_meta_data( 'id', $args );

			// Get product
			$product = wc_get_product( $product_id );
		}

		// Check is rental product
		if ( !$product || !$product->is_type( OVABRW_RENTAL ) ) return false;

		return apply_filters( OVABRW_PREFIX.'get_rental_product', $product );
	}
}

/**
 * is rental product
 */
if ( !function_exists( 'ovabrw_is_rental_product' ) ) {
	function ovabrw_is_rental_product( $product_id = false ) {
		global $product;

		if ( $product_id ) $product = wc_get_product( $product_id );
		if ( $product && $product->is_type( 'ovabrw_car_rental' ) ) return true;

		return false;
	}
}

/**
 * Get card template
 */
if ( !function_exists( 'ovabrw_get_card_template' ) ) {
	function ovabrw_get_card_template() {
		if ( isset( $_GET['card'] ) && $_GET['card'] ) {
			$card_template = trim( $_GET['card'] );
		} else {
			$card_template = ovabrw_get_option( 'glb_card_template', 'card1' );

			if ( get_queried_object_id() ) {
				$term_template = get_term_meta( get_queried_object_id(), 'ovabrw_card_template', true );

				if ( $term_template ) $card_template = $term_template;
			}
		}

		return apply_filters( OVABRW_PREFIX.'get_card_template', $card_template );
	}
}

/**
 * Get order status for booking
 */
if ( !function_exists( 'ovabrw_get_order_status' ) ) {
	function ovabrw_get_order_status() {
		$order_status = ovabrw_get_option( 'order_status', [
			'wc-completed',
			'wc-processing'
		]);

		return apply_filters( OVABRW_PREFIX.'get_order_status_for_booking', $order_status );
	}
}

/**
 * OVABRW Price - Multi Currency
 */
if ( !function_exists( 'ovabrw_wc_price' ) ) {
	function ovabrw_wc_price( $price = null, $args = [], $convert = true ) {
		$new_price = (float)$price;	

		// Currency
		$current_currency = !empty( $args['currency'] ) ? $args['currency'] : false;

		// CURCY - Multi Currency for WooCommerce
		// WooCommerce Multilingual & Multicurrency
		if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
			$new_price = wmc_get_price( $price, $current_currency );
		} elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			if ( $convert ) {
				// WPML multi currency
	    		global $woocommerce_wpml;

	    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
	    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

			        $multi_currency     = $woocommerce_wpml->get_multi_currency();
			        $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
			        $WMCP   			= new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
			        $new_price  		= $WMCP->convert_price_amount( $price, $current_currency );
			    }
			}
		}
		
		return apply_filters( OVABRW_PREFIX.'wc_price', wc_price( $new_price, $args ), $price, $args, $convert );
	}
}

/**
 * Convert price
 */
if ( !function_exists( 'ovabrw_convert_price' ) ) {
	function ovabrw_convert_price( $price = null, $args = [], $convert = true ) {
		$new_price = (float)$price;

		// Currency
		$current_currency = !empty( $args['currency'] ) ? $args['currency'] : false;

		// CURCY - Multi Currency for WooCommerce
		// WooCommerce Multilingual & Multicurrency
		if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
			$new_price = wmc_get_price( $price, $current_currency );
		} elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			if ( $convert ) {
				// WPML multi currency
	    		global $woocommerce_wpml;

	    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
	    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

			        $multi_currency     = $woocommerce_wpml->get_multi_currency();
			        $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
			        $WMCP   			= new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
			        $new_price  		= $WMCP->convert_price_amount( $price, $current_currency );
			    }
			}
		}
		
		return apply_filters( OVABRW_PREFIX.'convert_price', $new_price, $price, $args, $convert );
	}
}

/**
 * Convert price in Admin
 */
if ( !function_exists( 'ovabrw_convert_price_in_admin' ) ) {
	function ovabrw_convert_price_in_admin( $price = null, $currency_code = '' ) {
		$new_price = (float)$price;

		if ( is_plugin_active( 'woo-multi-currency/woo-multi-currency.php' ) || is_plugin_active( 'woocommerce-multi-currency/woocommerce-multi-currency.php' ) ) {
			$setting = WOOMULTI_CURRENCY_F_Data::get_ins();

			/*Check currency*/
			$selected_currencies = $setting->get_list_currencies();
			$current_currency    = $setting->get_current_currency();

			if ( !$currency_code || $currency_code === $current_currency ) {
				return $new_price;
			}

			if ( $new_price ) {
				if ( $currency_code && isset( $selected_currencies[ $currency_code ] ) ) {
					$new_price = $price * (float)$selected_currencies[ $currency_code ]['rate'];
				} else {
					$new_price = $price * (float)$selected_currencies[ $current_currency ]['rate'];
				}
			}
		} elseif ( is_plugin_active( 'woocommerce-multilingual/wpml-woocommerce.php' ) ) {
			// WPML multi currency
    		global $woocommerce_wpml;

    		if ( $woocommerce_wpml && is_object( $woocommerce_wpml ) ) {
    			if ( wp_doing_ajax() ) add_filter( 'wcml_load_multi_currency_in_ajax', '__return_true' );

		        $multi_currency     = $woocommerce_wpml->get_multi_currency();
		        $currency_options   = $woocommerce_wpml->get_setting( 'currency_options' );
		        $WMCP   			= new WCML_Multi_Currency_Prices( $multi_currency, $currency_options );
		        $new_price  		= $WMCP->convert_price_amount( $price, $currency_code );
		    }
		}

		return apply_filters( OVABRW_PREFIX.'convert_price_in_admin', $new_price, $price, $currency_code );
	}
}

/**
 * Get hotel pick-up time
 */
if ( !function_exists( 'ovabrw_get_hotel_pickup_time' ) ) {
	function ovabrw_get_hotel_pickup_time() {
		return apply_filters( OVABRW_PREFIX.'get_hotel_pickup_time', '14:00' );
	}
}

/**
 * Get hotel drop-off time
 */
if ( !function_exists( 'ovabrw_get_hotel_dropoff_time' ) ) {
	function ovabrw_get_hotel_dropoff_time() {
		return apply_filters( OVABRW_PREFIX.'get_hotel_dropoff_time', '11:00' );
	}
}