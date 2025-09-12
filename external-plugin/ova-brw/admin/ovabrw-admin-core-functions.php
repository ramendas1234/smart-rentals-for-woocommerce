<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * Output a text input box.
 */
if ( !function_exists( 'ovabrw_wp_text_input' ) ) {
	function ovabrw_wp_text_input( $field ) {
		$field['type'] 			= ovabrw_get_meta_data( 'type', $field, 'text' );
		$field['id'] 			= ovabrw_get_meta_data( 'id', $field );
		$field['class'] 		= ovabrw_get_meta_data( 'class', $field );
		$field['name'] 			= ovabrw_get_meta_data( 'name', $field );
		$field['value'] 		= ovabrw_get_meta_data( 'value', $field );
		$field['placeholder']   = ovabrw_get_meta_data( 'placeholder', $field );
		$field['required'] 		= ovabrw_get_meta_data( 'required', $field );
		$field['checked'] 		= ovabrw_get_meta_data( 'checked', $field );
		$field['readonly'] 		= ovabrw_get_meta_data( 'readonly', $field );
		$field['attrs'] 		= ovabrw_get_meta_data( 'attrs', $field );
		
		// Data type
		$data_type = ovabrw_get_meta_data( 'data_type', $field );

		switch ( $data_type ) {
			case 'price':
				// Add class
				$field['class'] .= ' wc_input_price';

				// Convert value
				$field['value'] = wc_format_localized_price( $field['value'] );

				// Placeholder
				if ( ! $field['placeholder'] ) {
					$field['placeholder'] = esc_html__( 'price', 'ova-brw' );
				}
				break;
			case 'decimal':
				// Add class
				$field['class'] .= ' wc_input_decimal';

				// Convert value
				$field['value'] = wc_format_localized_decimal( $field['value'] );

				// Placeholder
				if ( ! $field['placeholder'] ) {
					$field['placeholder'] = esc_html__( 'price', 'ova-brw' );
				}
				break;
			case 'timepicker':
				// Add class
				$field['class'] .= ' ovabrw-timepicker';

				// Time format
				$time_format = OVABRW()->options->get_time_format();

				// Convert value
				$field['value'] = strtotime( $field['value'] ) ? gmdate( $time_format, strtotime( $field['value'] ) ) : '';

				// Placeholder
				if ( ! $field['placeholder'] ) {
					$field['placeholder'] = OVABRW()->options->get_time_placeholder();
				}
				break;
			case 'datepicker':
				// Add class
				$field['class'] .= ' ovabrw-datepicker';

				// Date format
				$date_format = OVABRW()->options->get_date_format();

				// Convert valie
				$field['value'] = strtotime( $field['value'] ) ? gmdate( $date_format, strtotime( $field['value'] ) ) : '';

				// Placeholder
				if ( ! $field['placeholder'] ) {
					$field['placeholder'] = OVABRW()->options->get_date_placeholder();
				}
				break;
			case 'datetimepicker':
				// Add class
				$field['class'] .= ' ovabrw-datetimepicker';

				// Get date time format
				$datetime_format = OVABRW()->options->get_datetime_format();

				// Convert value
				$field['value'] = strtotime( $field['value'] ) ? gmdate( $datetime_format, strtotime( $field['value'] ) ) : '';

				// Placeholder
				if ( ! $field['placeholder'] ) {
					$field['placeholder'] = OVABRW()->options->get_datetime_placeholder();
				}
				break;
			case 'number':
				// Convert value
				$field['value'] = ( '' !== $field['value'] ) ? (int)$field['value'] : '';

				// Placeholder
				if ( ! $field['placeholder'] ) {
					$field['placeholder'] = esc_html__( 'number', 'ova-brw' );
				}
			default:
				break;
		}

		// Custom attribute handling
		$attrs = [];

		if ( ovabrw_array_exists( $field['attrs'] ) ) {
			foreach ( $field['attrs'] as $attr => $value ) {
				if ( $value === '' ) continue;
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Required
		if ( $field['required'] ) {
			$attrs[] = 'required';
		}

		// Checked
		if ( $field['checked'] ) {
			$attrs[] = 'checked';
		}

		// Read only
		if ( $field['readonly'] ) {
			$attrs[] = 'readonly';
		}

		do_action( OVABRW_PREFIX.'before_wp_text_input', $field );

		if ( $field['id'] ) {
			echo '<input type="' . esc_attr( $field['type'] ) . '" id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . wp_kses_post( implode( ' ', $attrs ) ) . ' />';
		} else {
			echo '<input type="' . esc_attr( $field['type'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" value="' . esc_attr( $field['value'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . wp_kses_post( implode( ' ', $attrs ) ) . ' />';
		}

		do_action( OVABRW_PREFIX.'after_wp_text_input', $field );
	}
}

/**
 * Output a textarea box.
 */
if ( !function_exists( 'ovabrw_wp_textarea' ) ) {
	function ovabrw_wp_textarea( $field ) {
		$field['type'] 			= ovabrw_get_meta_data( 'type', $field, 'text' );
		$field['id'] 			= ovabrw_get_meta_data( 'id', $field );
		$field['class'] 		= ovabrw_get_meta_data( 'class', $field );
		$field['name'] 			= ovabrw_get_meta_data( 'name', $field );
		$field['value'] 		= ovabrw_get_meta_data( 'value', $field );
		$field['placeholder']   = ovabrw_get_meta_data( 'placeholder', $field );
		$field['required'] 		= ovabrw_get_meta_data( 'required', $field );
		$field['readonly'] 		= ovabrw_get_meta_data( 'readonly', $field );
		$field['attrs'] 		= ovabrw_get_meta_data( 'attrs', $field );

		// Custom attribute handling
		$attrs = [];

		if ( ovabrw_array_exists( $field['attrs'] ) ) {
			foreach ( $field['attrs'] as $attr => $value ) {
				if ( $value === '' ) continue;
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Required
		if ( $field['required'] ) {
			$attrs[] = 'required';
		}

		// Read only
		if ( $field['readonly'] ) {
			$attrs[] = 'readonly';
		}

		do_action( OVABRW_PREFIX.'before_wp_textarea', $field );

		if ( $field['id'] ) {
			echo '<textarea id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . wp_kses_post( implode( ' ', $attrs ) ) . ' >' . esc_html( $field['value'] ) . '</textarea>';
		} else {
			echo '<textarea class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['name'] ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" ' . wp_kses_post( implode( ' ', $attrs ) ) . ' >' . esc_html( $field['value'] ) . '</textarea>';
		}

		do_action( OVABRW_PREFIX.'after_wp_textarea', $field );
	}
}

/**
 * Output a select input box.
 */
if ( !function_exists( 'ovabrw_wp_select_input' ) ) {
	function ovabrw_wp_select_input( $field ) {
		$field['id'] 			= ovabrw_get_meta_data( 'id', $field );
		$field['class'] 		= ovabrw_get_meta_data( 'class', $field );
		$field['name'] 			= ovabrw_get_meta_data( 'name', $field );
		$field['value'] 		= ovabrw_get_meta_data( 'value', $field );
		$field['placeholder'] 	= ovabrw_get_meta_data( 'placeholder', $field );
		$field['options'] 		= ovabrw_get_meta_data( 'options', $field );
		$field['required'] 		= ovabrw_get_meta_data( 'required', $field );
		$field['disabled'] 		= ovabrw_get_meta_data( 'disabled', $field );
		$field['multiple'] 		= ovabrw_get_meta_data( 'multiple', $field );
		$field['attrs'] 		= ovabrw_get_meta_data( 'attrs', $field );

		// Custom attribute handling
		$attrs = [];

		if ( ovabrw_array_exists( $field['attrs'] ) ) {
			foreach ( $field['attrs'] as $attr => $value ) {
				$attrs[] = esc_attr( $attr ) . '="' . esc_attr( $value ) . '"';
			}
		}

		// Required
		if ( $field['required'] ) {
			$attrs[] = 'required';
		}

		// Disabled
		if ( $field['disabled'] ) {
			$attrs[] = 'disabled';
		}

		// Multiple
		if ( $field['multiple'] ) {
			$attrs[] = 'multiple';
		}

		do_action( OVABRW_PREFIX.'before_wp_select_input', $field );

		if ( $field['id'] ) {
			echo '<select name="' . esc_attr( $field['name'] ) . '" id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr( $field['class'] ) . '" ' . wp_kses_post( implode( ' ', $attrs ) ) . ' >';
				if ( $field['placeholder'] ) {
					echo '<option value="">' . esc_html( $field['placeholder'] ) . '</option>';
				}

				foreach ( $field['options'] as $key => $value ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . ovabrw_selected( $key, $field['value'], false ) . '>' . esc_html( $value ) . '</option>';
				}
			echo '</select>';
		} else {
			echo '<select name="' . esc_attr( $field['name'] ) . '" class="' . esc_attr( $field['class'] ) . '" ' . wp_kses_post( implode( ' ', $attrs ) ) . ' >';
				if ( $field['placeholder'] ) {
					echo '<option value="">' . esc_html( $field['placeholder'] ) . '</option>';
				}

				foreach ( $field['options'] as $key => $value ) {
					echo '<option value="' . esc_attr( $key ) . '" ' . ovabrw_selected( $key, $field['value'], false ) . '>' . esc_html( $value ) . '</option>';
				}
			echo '</select>';
		}
		
		do_action( OVABRW_PREFIX.'before_wp_select_input', $field );
	}
}

/**
 * Get timepicker options
 */
if ( !function_exists( 'ovabrw_admin_timepicker_options' ) ) {
	function ovabrw_admin_timepicker_options() {
		return apply_filters( OVABRW_PREFIX.'admin_timepicker_options', [
			'timeFormat' 		=> OVABRW()->options->get_time_format(),
			'step'				=> OVABRW()->options->get_time_step(),
			'scrollDefault' 	=> '07:00',
	        'forceRoundTime' 	=> true,
	        'disableTextInput' 	=> true,
	        'autoPickTime' 		=> true,
	        'defaultStartTime' 	=> OVABRW()->options->get_default_time(),
	        'defaultEndTime' 	=> OVABRW()->options->get_default_time( 'dropoff' ),
	        'allowTimes' 		=> [],
	        'allowStartTimes' 	=> OVABRW()->options->get_time_group(),
	        'allowEndTimes' 	=> OVABRW()->options->get_time_group( 'dropoff' ),
	        'lang' 				=> apply_filters( OVABRW_PREFIX.'admin_timepicker_options_lang', [
	        	'am' 		=> 'am',
	        	'pm' 		=> 'pm',
	        	'AM' 		=> 'AM',
	        	'PM' 		=> 'PM',
	        	'decimal' 	=> '.',
	        	'mins' 		=> 'mins',
	        	'hr' 		=> 'hr',
	        	'hrs' 		=> 'hrs',
	        	'pickUp' 	=> esc_html__( 'Pick-up', 'ova-brw' ),
	        	'dropOff' 	=> esc_html__( 'Drop-off', 'ova-brw' )
	        ])
		]);
	}
}

/**
 * Get datepicker options
 */
if ( !function_exists( 'ovabrw_admin_datepicker_options' ) ) {
	function ovabrw_admin_datepicker_options() {
		// Date format
		$date_format = OVABRW()->options->get_date_format();

		// Min year, Max year
		$min_year = (int)ovabrw_get_setting( 'booking_form_year_start', gmdate('Y') );
		$max_year = (int)ovabrw_get_setting( 'booking_form_year_end', gmdate('Y')+3 );

		// Min date, Max date
		$min_date = $max_date = '';

		if ( $min_year ) {
			$min_date = gmdate( $date_format, strtotime( "$min_year-01-01" ) );
		}
		if ( $max_year ) {
			$december_date = new DateTime("$max_year-12-01");
			$december_date->modify('last day of this month');

			// Get max date
			$max_date = $december_date->format($date_format);
		}

		// Start date when calendar show
		$start_date = '';

		if ( $min_date && strtotime( $min_date ) > current_time( 'timestamp' ) ) {
			$start_date = $min_date;
		}

		// Language
		$language = apply_filters( OVABRW_PREFIX.'admin_datepicker_language', ovabrw_get_setting( 'calendar_language_general', 'en-GB' ) );
		if ( apply_filters( 'wpml_current_language', NULL ) ) { // WPML
            $language = apply_filters( 'wpml_current_language', NULL );
        } elseif ( function_exists('pll_current_language') ) { // Polylang
            $language = pll_current_language();
        }

		// Disable weekdays
		$disable_weekdays = [];

		if ( apply_filters( OVABRW_PREFIX.'admin_use_disable_weekdays', true ) ) {
			$disable_weekdays = ovabrw_get_setting( 'calendar_disable_week_day', [] );

			if ( ovabrw_array_exists( $disable_weekdays ) ) {
	        	$key = array_search( '0', $disable_weekdays );
				if ( $key !== false ) $disable_weekdays[$key] = '7';
	        } else {
	        	if ( $disable_weekdays && !is_array( $disable_weekdays ) ) {
	        		$disable_weekdays = explode( ',', $disable_weekdays );
	        		$disable_weekdays = array_map( 'trim', $disable_weekdays );
	        	}
	        }
		}

		// Datepicker CSS
		$datepciker_css = [
			OVABRW_PLUGIN_URI.'assets/libs/easepick/easepick.min.css',
			OVABRW_PLUGIN_URI.'assets/css/datepicker/datepicker.css'
		];

		// Get customize calendar
		$customize_calendar = ovabrw_get_option( 'customize_calendar' );
		if ( 'yes' == $customize_calendar ) {
			$css_path = OVABRW_PLUGIN_PATH.'assets/css/datepicker/customize.css';

			if ( file_exists( $css_path ) ) {
				$datepciker_css[] = OVABRW_PLUGIN_URI.'assets/css/datepicker/customize.css';
			} else {
				$additional_css = get_option( OVABRW_PREFIX.'additional_css' );
				$numberof_byte 	= file_put_contents( OVABRW_PLUGIN_PATH.'assets/css/datepicker/customize.css', (string)$additional_css );

				if ( $numberof_byte ) {
					$datepciker_css[] = OVABRW_PLUGIN_URI.'assets/css/datepicker/customize.css';
				}
			}
		}

		return apply_filters( OVABRW_PREFIX.'admin_datepicker_options', [
			'css' 			=> apply_filters( OVABRW_PREFIX.'admin_datepicker_css', $datepciker_css ),
			'firstDay' 		=> (int)ovabrw_get_setting( 'calendar_first_day', 1 ),
			'lang' 			=> $language,
			'format' 		=> $date_format,
			'grid' 			=> 2,
			'calendars' 	=> 2,
			'zIndex' 		=> 999999999,
			'inline' 		=> false,
			'readonly' 		=> true,
			'header' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_header', '' ),			
			'autoApply' 	=> true,
			'locale' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_locale', [
				'cancel' 	=> esc_html__( 'Cancel', 'ova-brw' ),
	        	'apply' 	=> esc_html__( 'Apply', 'ova-brw' )
			]),
			'AmpPlugin' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_amp_plugin', [
				'dropdown' 	=> [
					'months' 	=> true,
					'years' 	=> true,
					'minYear' 	=> $min_year ? $min_year : gmdate('Y'),
					'maxYear' 	=> $max_year ? $max_year : gmdate('Y')+3
				],
				'resetButton' 	=> true,
				'darkMode' 		=> false
			]),
			'RangePlugin' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_range_plugin', [
				'repick' 	=> false,
				'strict' 	=> true,
				'tooltip' 	=> true,
				'locale' 	=> [
					'zero' 	=> '',
					'one' 	=> esc_html__( 'day', 'ova-brw' ),
					'two' 	=> '',
					'many' 	=> '',
					'few' 	=> '',
					'other' => esc_html__( 'days', 'ova-brw' )
				]
			]),
			'LockPlugin' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_lock_plugin', [
				'minDate' 			=> $min_date,
				'maxDate' 			=> $max_date,
				'minDays' 			=> '',
				'maxDays' 			=> '',
				'selectForward' 	=> false,
				'selectBackward' 	=> false,
				'presets' 			=> false,
				'inseparable' 		=> false
			]),
			'PresetPlugin' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_preset_plugin', [
				'position' 		=> 'left',
				'customLabels' 	=> [
					'Today',
					'Yesterday',
					'Last 7 Days',
					'Last 30 Days',
					'This Month',
					'Last Month'
				],
				'customPreset' 	=> ovabrw_get_predefined_ranges()
			]),
			'plugins' => apply_filters( OVABRW_PREFIX.'admin_datepicker_plugins', [
				'AmpPlugin',
				'RangePlugin',
				'LockPlugin',
				'PresetPlugin'
			]),
			'disableWeekDays' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_disable_weekdays', $disable_weekdays ),
			'disableDates' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_disable_dates', [] ),
			'bookedDates' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_booked_dates', [] ),
			'allowedDates' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_allowed_dates', [] ),
			'startDate' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_start_date', $start_date )
		]);
	}
}

/**
 * Get datetimepicker options
 */
if ( !function_exists( 'ovabrw_admin_datetimepicker_options' ) ) {
	function ovabrw_admin_datetimepicker_options() {
		// Date format
		$date_format = OVABRW()->options->get_date_format();

		// Min year, Max year
		$min_year = (int)ovabrw_get_setting( 'booking_form_year_start', gmdate('Y') );
		$max_year = (int)ovabrw_get_setting( 'booking_form_year_end', gmdate('Y')+3 );

		// Min date, Max date
		$min_date = $max_date = '';

		if ( $min_year ) {
			$min_date = gmdate( $date_format, strtotime( "$min_year-01-01" ) );
		}
		if ( $max_year ) {
			$december_date = new DateTime("$max_year-12-01");
			$december_date->modify('last day of this month');

			// Get max date
			$max_date = $december_date->format($date_format);
		}

		// Start date when calendar show
		$start_date = '';

		if ( $min_date && strtotime( $min_date ) > current_time( 'timestamp' ) ) {
			$start_date = $min_date;
		}

		// Language
		$language = apply_filters( OVABRW_PREFIX.'admin_datepicker_language', ovabrw_get_setting( 'calendar_language_general', 'en-GB' ) );
		if ( apply_filters( 'wpml_current_language', NULL ) ) { // WPML
            $language = apply_filters( 'wpml_current_language', NULL );
        } elseif ( function_exists('pll_current_language') ) { // Polylang
            $language = pll_current_language();
        }

		// Disable weekdays
		$disable_weekdays = [];
		if ( apply_filters( OVABRW_PREFIX.'admin_use_disable_weekdays', true ) ) {
			$disable_weekdays = ovabrw_get_setting( 'calendar_first_day', [] );
		}

		// Datepicker CSS
		$datepciker_css = [
			OVABRW_PLUGIN_URI.'assets/libs/easepick/easepick.min.css',
			OVABRW_PLUGIN_URI.'assets/css/datepicker/datepicker.css'
		];

		// Get customize calendar
		$customize_calendar = ovabrw_get_option( 'customize_calendar' );
		if ( 'yes' == $customize_calendar ) {
			$css_path = OVABRW_PLUGIN_PATH.'assets/css/datepicker/customize.css';

			if ( file_exists( $css_path ) ) {
				$datepciker_css[] = OVABRW_PLUGIN_URI.'assets/css/datepicker/customize.css';
			} else {
				$additional_css = ovabrw_get_option( 'additional_css' );
				$numberof_byte 	= file_put_contents( OVABRW_PLUGIN_PATH.'assets/css/datepicker/customize.css', (string)$additional_css );

				if ( $numberof_byte ) {
					$datepciker_css[] = OVABRW_PLUGIN_URI.'assets/css/datepicker/customize.css';
				}
			}
		}

		return apply_filters( OVABRW_PREFIX.'admin_datetimepicker_options', [
			'datepicker' => [
				'css' 			=> apply_filters( OVABRW_PREFIX.'admin_datepicker_css', $datepciker_css ),
				'firstDay' 		=> (int)ovabrw_get_setting( 'calendar_first_day', 1 ),
				'lang' 			=> $language,
				'format' 		=> $date_format,
				'grid' 			=> 2,
				'calendars' 	=> 2,
				'zIndex' 		=> 999999999,
				'inline' 		=> false,
				'readonly' 		=> true,
				'header' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_header', '' ),			
				'autoApply' 	=> false,
				'locale' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_locale', [
					'cancel' 	=> esc_html__( 'Cancel', 'ova-brw' ),
		        	'apply' 	=> esc_html__( 'Apply', 'ova-brw' )
				]),
				'AmpPlugin' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_amp_plugin', [
					'dropdown' 	=> [
						'months' 	=> true,
						'years' 	=> true,
						'minYear' 	=> $min_year ? $min_year : gmdate('Y'),
						'maxYear' 	=> $max_year ? $max_year : gmdate('Y')+3,
					],
					'resetButton' 	=> true,
					'darkMode' 		=> false
				]),
				'RangePlugin' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_range_plugin', [
					'repick' 	=> false,
					'strict' 	=> true,
					'tooltip' 	=> true,
					'locale' 	=> [
						'zero' 	=> '',
						'one' 	=> esc_html__( 'day', 'ova-brw' ),
						'two' 	=> '',
						'many' 	=> '',
						'few' 	=> '',
						'other' => esc_html__( 'days', 'ova-brw' )
					]
				]),
				'LockPlugin' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_lock_plugin', [
					'minDate' 			=> $min_date,
					'maxDate' 			=> $max_date,
					'minDays' 			=> '',
					'maxDays' 			=> '',
					'selectForward' 	=> false,
					'selectBackward' 	=> false,
					'presets' 			=> false,
					'inseparable' 		=> false
				]),
				'PresetPlugin' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_preset_plugin', [
					'position' 		=> 'left',
					'customLabels' 	=> [
						'Today',
						'Yesterday',
						'Last 7 Days',
						'Last 30 Days',
						'This Month',
						'Last Month'
					],
					'customPreset' 	=> ovabrw_get_predefined_ranges()
				]),
				'plugins' => apply_filters( OVABRW_PREFIX.'admin_datepicker_plugins', [
					'AmpPlugin',
					'RangePlugin',
					'LockPlugin',
					'PresetPlugin',
					'TimePlugin'
				]),
				'disableWeekDays' 	=> apply_filters( OVABRW_PREFIX.'admin_datepicker_disable_weekdays', $disable_weekdays ),
				'disableDates' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_disable_dates', [] ),
				'bookedDates' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_booked_dates', [] ),
				'allowedDates' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_allowed_dates', [] ),
				'startDate' 		=> apply_filters( OVABRW_PREFIX.'admin_datepicker_start_date', $start_date )
			],
			'timepicker' => [
				'timeFormat' 		=> OVABRW()->options->get_time_format(),
				'step'				=> OVABRW()->options->get_time_step(),
				'scrollDefault' 	=> '07:00',
		        'forceRoundTime' 	=> true,
		        'disableTextInput' 	=> true,
		        'autoPickTime' 		=> true,
		        'useSelect' 		=> true,
		        'scrollSelect' 		=> '07:00',
		        'defaultStartTime' 	=> OVABRW()->options->get_default_time(),
		        'defaultEndTime' 	=> OVABRW()->options->get_default_time( 'dropoff' ),
		        'allowTimes' 		=> [],
		        'allowStartTimes' 	=> OVABRW()->options->get_time_group(),
		        'allowEndTimes' 	=> OVABRW()->options->get_time_group( 'dropoff' ),
		        'lang' 				=> apply_filters( OVABRW_PREFIX.'admin_timepicker_options_lang', [
		        	'am' 		=> 'am',
		        	'pm' 		=> 'pm',
		        	'AM' 		=> 'AM',
		        	'PM' 		=> 'PM',
		        	'decimal' 	=> '.',
		        	'mins' 		=> 'mins',
		        	'hr' 		=> 'hr',
		        	'hrs' 		=> 'hrs',
		        	'pickUp' 	=> esc_html__( 'Pick-up', 'ova-brw' ),
		        	'dropOff' 	=> esc_html__( 'Drop-off', 'ova-brw' )
		        ])
			]
		]);
	}
}