<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Rental Settings class
 */
if ( !class_exists( 'OVABRW_Rental_Settings' ) ) {

	class OVABRW_Rental_Settings extends WC_Settings_Page {

		/**
	     * Constructor
	     */
	    public function __construct() {
	    	// Set ID
	        $this->id = 'ova_brw';

	        // Set label
	        $this->label = esc_html__( 'Booking & Rental', 'ova-brw' );

	        parent::__construct();
	    }

	    /**
	     * Get own sections
	     */
	    protected function get_own_sections() {
	        return apply_filters( OVABRW_PREFIX.'get_own_sections', [
	            ''              => esc_html__( 'General', 'ova-brw' ),
	            'archive'       => esc_html__( 'Product Archive', 'ova-brw' ),
	            'detail'        => esc_html__( 'Product Details', 'ova-brw' ),
	            'recaptcha'     => esc_html__( 'reCAPTCHA', 'ova-brw' ),
	            'deposit'       => esc_html__( 'Deposit', 'ova-brw' ),
	            'search'        => esc_html__( 'Search', 'ova-brw' ),
	            'cancel'        => esc_html__( 'Cancellation Policy', 'ova-brw' ),
	            'reminder'      => esc_html__( 'Reminder', 'ova-brw' ),
	            'order'         => esc_html__( 'Order Settings', 'ova-brw' ),
	            'typography'    => esc_html__( 'Typography & Color', 'ova-brw' )
	        ]);
	    }

	    /**
	     * General section.
	     */
	    protected function get_settings_for_default_section() {
	        // Get timezone string
	        $timezone_string = '<strong>'.OVABRW()->options->get_timezone_string().'</strong>';
	        $timezone_string .= '<p><a href="'.admin_url('options-general.php').'#timezone_string">'.esc_html__( 'Change timezone', 'ova-brw' ).'</a></p>';

	        $settings = [
	        	// Booking Conditions
	        	[
	            	'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'booking_conditions_options',
	                'title' => esc_html__( 'Booking Conditions', 'ova-brw' )
	            ],
	                [
	                	'title'             => esc_html__( 'Order status', 'ova-brw' ),
	                    'type'              => 'multiselect',
	                    'id'                => OVABRW_PREFIX.'order_status',
	                    'class'             => 'wc-enhanced-select-nostd',
	                    'options'           => apply_filters( OVABRW_PREFIX.'order_status_options', [
	                    	'wc-completed'  => esc_html__( 'Completed', 'ova-brw' ),
	                        'wc-processing' => esc_html__( 'Processing', 'ova-brw' ),
	                        'wc-on-hold'    => esc_html__( 'On-hold', 'ova-brw' ),
	                        'wc-pending' 	=> esc_html__( 'Pending payment', 'ova-brw' )
	                    ]),
	                    'default'           => [ 'wc-completed', 'wc-processing' ],
	                    'desc'              => esc_html__( 'Order statuses will be accepted and reserved.', 'ova-brw' ),
	                    'desc_tip'          => false,
	                    'custom_attributes' => [
	                    	'required'          => 'required',
	                        'data-placeholder'  => esc_html__( 'Select order statuses...', 'ova-brw' )
	                    ]
	                ],
	                [
	                	'type'      => 'multiselect',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'calendar_disable_week_day',
	                    'class'     => 'wc-enhanced-select-nostd',
	                    'name'      => esc_html__( 'Disable weekdays', 'ova-brw' ),
	                    'options'   => [
	                    	'1' => esc_html__( 'Monday' ),
	                        '2' => esc_html__( 'Tuesday' ),
	                        '3' => esc_html__( 'Wednesday' ),
	                        '4' => esc_html__( 'Thursday' ),
	                        '5' => esc_html__( 'Friday' ),
	                        '6' => esc_html__( 'Saturday' ),
	                        '7' => esc_html__( 'Sunday' )
	                    ],
	                    'desc'              => esc_html__( 'For example: The store will be closed on Saturdays and Sundays.', 'ova-brw' ),
	                    'desc_tip'          => true,
	                    'custom_attributes' => [
	                    	'data-placeholder' => esc_html__( 'Select day of the week', 'ova-brw' )
	                    ]
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_disable_week_day',
	                    'name'          => esc_html__( 'Overcome disable weekdays', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'desc'          => '<p>'.esc_html__( 'For example: Thursday is disabled, so you can book from Monday to Friday.', 'ova-brw' ).'</p>',
	                    'tooltip'       => esc_html__( 'Allow to select date range including disable weekdays.', 'ova-brw' ),
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'enable_insurance_tax',
	                    'name'          => esc_html__( 'Apply Tax for Insurance Amount', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start',
	                    'desc'          => '<p>'.esc_html__( 'Tax will be calculated for insurance during checkout (required enable Taxes).', 'ova-brw' ).'</p>'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'only_add_insurance_to_deposit',
	                    'name'          => esc_html__( 'Insurance amount will be paid once', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start',
	                    'desc'          => '<p>'.esc_html__( 'Insurance amount will be paid in full in the first payment.', 'ova-brw' ).'</p>'
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'booking_conditions_options'
	            ], // END Booking Conditions

	            // Date & Time format
	            [
	            	'title' => esc_html__( 'Date & Time format', 'ova-brw' ),
	                'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'datetime_options'
	            ],
	                [
	                	'type'      => 'select',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'booking_form_date_format',
	                    'name'      => esc_html__( 'Date format', 'ova-brw' ),
	                    'options'   => ovabrw_date_format(),
	                    'default'   => OVABRW()->options->get_date_format(),
	                    'desc'      => esc_html__( 'This sets the date format for the date input.', 'ova-brw' ),
	                    'desc_tip'  => true
	                ],
	                [
	                	'type'      => 'select',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'calendar_time_format',
	                    'name'      => esc_html__( 'Time format', 'ova-brw' ),
	                    'options'   => ovabrw_time_format(),
	                    'default'   => OVABRW()->options->get_time_format(),
	                    'desc'      => esc_html__( 'This sets the time format for the time input.', 'ova-brw' ),
	                    'desc_tip'  => true
	                ],
	                [
	                	'type'              => 'number',
	                    'id'                => OVABRW_PREFIX_OPTIONS.'booking_form_step_time',
	                    'name'              => esc_html__( 'Time slot step (minutes)', 'ova-brw' ),
	                    'default'           => 30,
	                    'desc'              => esc_html__( 'Set 15 minutes as the default time slot step, the working hours will be divided by a grid of 15 minutes: 07:15, 07:30, 07:45.', 'ova-brw' ),
	                    'desc_tip'          => true,
	                    'custom_attributes' => [
	                    	'min'   => 1
	                    ]
	                ],
	                [
	                	'type'      => 'text',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'calendar_time_to_book',
	                    'name'      => esc_html__( 'The group of time for pick-up date', 'ova-brw' ),
	                    'default'   => '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00',
	                    'desc'      => esc_html__( 'Insert time format: 24 hour. Like 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 17:00, 17:30, 18:00','ova-brw' ),
	                    'desc_tip'  => false
	                ],
	                [
	                	'type'      => 'text',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'booking_form_default_hour',
	                    'name'      => esc_html__( 'Default time for pick-up date', 'ova-brw' ),
	                    'default'   => '07:00',
	                    'desc'      => esc_html__( 'Insert time format: 24 hour. Example: 06:00', 'ova-brw' ),
	                    'desc_tip'  => false
	                ],
	                [
	                	'type'      => 'text',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'calendar_time_to_book_for_end_date',
	                    'name'      => esc_html__( 'The group of time for drop-off date', 'ova-brw' ),
	                    'default'   => '07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 11:00, 11:30, 12:00, 12:30, 13:00, 13:30, 14:00, 14:30, 15:00, 15:30, 16:00, 16:30, 17:00, 17:30, 18:00',
	                    'desc'      => esc_html__( 'Insert time format: 24hour. Like 07:00, 07:30, 08:00, 08:30, 09:00, 09:30, 10:00, 10:30, 17:00, 17:30, 18:00','ova-brw' ),
	                    'desc_tip'  => false
	                ],
	                [
	                	'type'      => 'text',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'booking_form_default_hour_end_date',
	                    'name'      => esc_html__( 'Default time for drop-off date', 'ova-brw' ),
	                    'default'   => '07:00',
	                    'desc'      => esc_html__( 'Insert time format: 24hour. Example: 06:00', 'ova-brw' ),
	                    'desc_tip'  => false
	                ],
	                [
	                	'type'  => 'info',
	                    'id'    => OVABRW_PREFIX_OPTIONS.'booking_form_timezone',
	                    'name'  => esc_html__( 'Timezone', 'ova-brw' ),
	                    'text'  => $timezone_string
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'datetime_options'
	            ], // END Date & Time format

	            // Calendar
	            [
	            	'title' => esc_html__( 'Input Calendar', 'ova-brw' ),
	                'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'calendar_options'
	            ],
	                [
	                	'type'              => 'select',
	                    'id'                => OVABRW_PREFIX_OPTIONS.'calendar_language_general',
	                    'class'             => 'wc-enhanced-select-nostd',
	                    'name'              => esc_html__( 'Language', 'ova-brw' ),
	                    'options'           => ovabrw_calendar_languages(),
	                    'default'           => 'en-GB',
	                    'desc'              => esc_html__( 'This sets language for the calendar.', 'ova-brw' ),
	                    'desc_tip'          => true,
	                    'custom_attributes' => [
	                    	'data-placeholder' => esc_html__( 'Select a language...', 'ova-brw' )
	                    ]
	                ],
	                [
	                	'type'      => 'select',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'calendar_first_day',
	                    'class'     => 'wc-enhanced-select-nostd',
	                    'name'      => esc_html__( 'The first day of the week', 'ova-brw' ),
	                    'options'   => [
	                        '1' => esc_html__( 'Monday', 'ova-brw' ),
	                        '2' => esc_html__( 'Tuesday', 'ova-brw' ),
	                        '3' => esc_html__( 'Wednesday', 'ova-brw' ),
	                        '4' => esc_html__( 'Thursday', 'ova-brw' ),
	                        '5' => esc_html__( 'Friday', 'ova-brw' ),
	                        '6' => esc_html__( 'Saturday ', 'ova-brw' ),
	                        '7' => esc_html__( 'Sunday', 'ova-brw' ),
	                    ],
	                    'default'           => '1',
	                    'desc'              => esc_html__( 'The first day of the week.', 'ova-brw' ),
	                    'desc_tip'          => true,
	                    'custom_attributes' => [
	                    	'data-placeholder'  => esc_html__( 'Select day...', 'ova-brw' )
	                    ]
	                ],
	                [
	                	'type'              => 'number',
	                    'id'                => OVABRW_PREFIX_OPTIONS.'booking_form_year_start',
	                    'name'              => esc_html__( 'Minimum year', 'ova-brw' ),
	                    'placeholder'       => gmdate('Y'),
	                    'desc'              => sprintf( esc_html__( 'Start value for fast Year selector. Example: %s', 'ova-brw' ), gmdate('Y') ),
	                    'desc_tip'          => true,
	                    'custom_attributes' => [
	                    	'min'  => gmdate('Y'),
	                        'step' => 'any'
	                    ]
	                ],
	                [
	                	'type'              => 'number',
	                    'id'                => OVABRW_PREFIX_OPTIONS.'booking_form_year_end',
	                    'name'              => esc_html__( 'Maximum year', 'ova-brw' ),
	                    'placeholder'       => gmdate('Y')+3,
	                    'desc'              => sprintf( esc_html__( 'End value for fast Year selector. Example: %s', 'ova-brw' ), gmdate('Y') + 3 ),
	                    'desc_tip'          => true,
	                    'custom_attributes' => [
	                    	'min'  => gmdate('Y'),
	                        'step' => 'any'
	                    ]
	                ],
	                [
	                	'title'         => esc_html__( 'Primary background', 'ova-brw' ),
	                    'type'          => 'color',
	                    'id'            => OVABRW_PREFIX.'primary_background_calendar',
	                    'row_class'     => OVABRW_PREFIX.'colorpick',
	                    'default'       => '#00BB98',
	                    'placeholder'   => '#00BB98',
	                    'desc'          => esc_html__( 'The primary background of the input Calendar.', 'ova-brw' ),
	                    'desc_tip'      => true,
	                    'css'           => 'max-width:365px;'
	                ],
	                [
	                	'title'         => esc_html__( 'Text color of available dates', 'ova-brw' ),
	                    'type'          => 'color',
	                    'id'            => OVABRW_PREFIX.'color_available',
	                    'row_class'     => OVABRW_PREFIX.'colorpick',
	                    'default'       => '#222222',
	                    'placeholder'   => '#222222',
	                    'desc'          => esc_html__( 'The text color of the available dates in the input Calendar.', 'ova-brw' ),
	                    'desc_tip'      => true,
	                    'css'           => 'max-width:365px;'
	                ],
	                [
	                	'title'         => esc_html__( 'Background of available dates', 'ova-brw' ),
	                    'type'          => 'color',
	                    'id'            => OVABRW_PREFIX.'background_available',
	                    'row_class'     => OVABRW_PREFIX.'colorpick',
	                    'default'       => '#FFFFFF',
	                    'placeholder'   => '#FFFFFF',
	                    'desc'          => esc_html__( 'The background of the available dates in the input Calendar.', 'ova-brw' ),
	                    'desc_tip'      => true,
	                    'css'           => 'max-width:365px;'
	                ],
	                [
	                	'title'         => esc_html__( 'Text color of disabled dates', 'ova-brw' ),
	                    'type'          => 'color',
	                    'id'            => OVABRW_PREFIX.'color_not_available',
	                    'row_class'     => OVABRW_PREFIX.'colorpick',
	                    'default'       => '#FFFFFF',
	                    'placeholder'   => '#FFFFFF',
	                    'desc'          => esc_html__( 'The text color of the disabled dates in the input Calendar.', 'ova-brw' ),
	                    'desc_tip'      => true,
	                    'css'           => 'max-width:365px;'
	                ],
	                [
	                	'title'         => esc_html__( 'Background of disabled dates', 'ova-brw' ),
	                    'type'          => 'color',
	                    'id'            => OVABRW_PREFIX.'background_not_available',
	                    'row_class'     => OVABRW_PREFIX.'colorpick',
	                    'default'       => '#E56E00',
	                    'placeholder'   => '#E56E00',
	                    'desc'          => esc_html__( 'The background of the disabled dates in the input Calendar.', 'ova-brw' ),
	                    'desc_tip'      => true,
	                    'css'           => 'max-width:365px;'
	                ],
	                [
	                	'title'         => esc_html__( 'Text color of booked dates', 'ova-brw' ),
	                    'type'          => 'color',
	                    'id'            => OVABRW_PREFIX.'color_booked_date',
	                    'row_class'     => OVABRW_PREFIX.'colorpick',
	                    'default'       => '#FFFFFF',
	                    'placeholder'   => '#FFFFFF',
	                    'desc'          => esc_html__( 'The text color of the booked dates in the input Calendar.', 'ova-brw' ),
	                    'desc_tip'      => true,
	                    'css'           => 'max-width:365px;'
	                ],
	                [
	                	'title'         => esc_html__( 'Background of booked dates', 'ova-brw' ),
	                    'type'          => 'color',
	                    'id'            => OVABRW_PREFIX.'background_booked_date',
	                    'row_class'     => OVABRW_PREFIX.'colorpick',
	                    'default'       => '#E56E00',
	                    'placeholder'   => '#E56E00',
	                    'desc'          => esc_html__( 'The background of the booked dates in the input Calendar.', 'ova-brw' ),
	                    'desc_tip'      => true,
	                    'css'           => 'max-width:365px;'
	                ],
	                [
	                	'title'         => esc_html__( 'Color streak for disabled dates', 'ova-brw' ),
	                    'type'          => 'color',
	                    'id'            => OVABRW_PREFIX.'color_streak',
	                    'row_class'     => OVABRW_PREFIX.'colorpick',
	                    'default'       => '#FFFFFF',
	                    'placeholder'   => '#FFFFFF',
	                    'desc'          => esc_html__( 'The Color streak for disabled dates in the input Calendar.', 'ova-brw' ),
	                    'desc_tip'      => true,
	                    'css'           => 'max-width:365px;'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX.'show_price_input_calendar',
	                    'name'          => esc_html__( 'Show price', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'tooltip'       => esc_html__( 'The price in the input Calendar.', 'ova-brw' ),
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'title'         => esc_html__( 'Customize CSS', 'ova-brw' ),
	                    'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX.'customize_calendar',
	                    'class'         => 'ovabrw-dependent',
	                    'tooltip'       => esc_html__( 'Enable additional CSS for Calendar.', 'ova-brw' ),
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'title'             => esc_html__( 'Additional CSS', 'ova-brw' ),
	                    'type'              => 'textarea',
	                    'id'                => OVABRW_PREFIX.'additional_css',
	                    'class'             => 'ovabrw-required',
	                    'row_class'         => 'ovabrw-hidden',
	                    'default'           => '/* Calendar */ .calendar>.days-grid>.day { color: #444444; background-color: #FFFFFF; border: 1px solid #FFFFFF; }',
	                    'placeholder'       => esc_html__( 'Insert custom CSS here', 'ova-brw' ),
	                    'desc_tip'          => esc_html__( 'You can insert custom CSS for the Calendar.', 'ova-brw' ),
	                    'custom_attributes' => [
	                    	'rows'          => '10',
	                        'data-required' => OVABRW_PREFIX.'customize_calendar'
	                    ]
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'calendar_options'
	            ], // END Calendar

	            // Custom Taxonomy
	            [
	            	'title' => esc_html__( 'Custom Taxonomy', 'ova-brw' ),
	                'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'custom_taxonomy'
	            ],
	                [
	                	'type'      => 'select',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'search_show_tax_depend_cat',
	                    'name'      => esc_html__( 'Show Custom Taxonomies for products', 'ova-brw' ),
	                    'default'   => 'yes',
	                    'options'   => [
	                        'yes'   => esc_html__( 'Based on each category', 'ova-brw' ),
	                        'no'    => esc_html__( 'All', 'ova-brw' )
	                    ]
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'custom_taxonomy'
	            ], // END Custom Taxonomy

	            // Google Maps
	            [
	            	'title' => esc_html__( 'Google Maps', 'ova-brw' ),
	                'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'google_maps'
	            ],
	                [
	                	'type'  => 'text',
	                    'id'    => 'ova_brw_google_key_map',
	                    'name'  => esc_html__( 'API Key', 'ova-brw' ),
	                    'desc'  => sprintf( esc_html__( 'You can get API Key %s', 'ova-brw' ), '<a href="https://developers.google.com/maps/documentation/javascript/get-api-key" target="_blank">here</a>' )
	                ],
	                [
	                	'type'      		=> 'number',
	                    'id'        		=> OVABRW_PREFIX_OPTIONS.'google_map_zoom',
	                    'name'      		=> esc_html__( 'Zoom', 'ova-brw' ),
	                    'default'   		=> 17,
	                    'custom_attributes' => [
	                    	'min' => 0
	                    ]
	                ],
	                [
	                	'type'      => 'text',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'latitude_map_default',
	                    'class'     => OVABRW_PREFIX_OPTIONS.'latitude_map_default',
	                    'name'      => esc_html__( 'Latitude Map default', 'ova-brw' ),
	                    'default'   => 39.177972,
	                    'desc'      => esc_html__( 'The default latitude of map when the product do not exist','ova-brw' ),
	                    'desc_tip'  => true
	                ],
	                [
	                	'type'      => 'text',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'longitude_map_default',
	                    'class'     => OVABRW_PREFIX_OPTIONS.'longitude_map_default',
	                    'name'      => esc_html__( 'Longitude Map default', 'ova-brw' ),
	                    'default'   => -100.363750,
	                    'desc'      => esc_html__( 'The default longitude of map when the product do not exist','ova-brw' ),
	                    'desc_tip'  => true
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'google_maps'
	            ] // END Google Maps
	        ];

	        return apply_filters( OVABRW_PREFIX.'get_settings_for_default_section', $settings );
	    }

	    /**
	     * Product Archive section.
	     */
	    protected function get_settings_for_archive_section() {
	        $settings = [
	        	// Archive options
	        	[
	        		'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'archive_options',
	                'title' => esc_html__( 'Archive options', 'ova-brw' )
	        	],
	                [
	                	'type'      => 'select',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'archive_product_show_features',
	                    'name'      => esc_html__( 'Show Features', 'ova-brw' ),
	                    'options'   => [
	                        'yes'   => esc_html__( 'Yes', 'ova-brw' ),
	                        'no'    => esc_html__( 'No', 'ova-brw' ),
	                    ],
	                    'default'   => 'yes'
	                ],
	                [
	                	'type'      => 'select',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'archive_product_show_special_features',
	                    'name'      => esc_html__( 'Show Special in Features', 'ova-brw' ),
	                    'options'   => [
	                        'yes'   => esc_html__( 'Yes', 'ova-brw' ),
	                        'no'    => esc_html__( 'No', 'ova-brw' ),
	                    ],
	                    'default'   => 'yes'
	                ],
	                [
	                	'type'      => 'select',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'archive_product_show_attribute',
	                    'name'      => esc_html__( 'Show Attributes', 'ova-brw' ),
	                    'options'   => [
	                        'yes'   => esc_html__( 'Yes', 'ova-brw' ),
	                        'no'    => esc_html__( 'No', 'ova-brw' ),
	                    ],
	                    'default'   => 'yes'
	                ],
	                [
	                	'type'          => 'select',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'display_product_taxonomy',
	                    'name'          => esc_html__( 'Display Product Taxonomy', 'ova-brw' ),
	                    'options'       => [
	                        'rental'    => esc_html__( 'Rental', 'ova-brw' ),
	                        'shop'      => esc_html__( 'Shop', 'ova-brw' ),
	                    ],
	                    'default'       => 'rental'
	                ],
	                [
	                	'type'          => 'select',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'display_shop_page',
	                    'name'          => esc_html__( 'Display Shop Page', 'ova-brw' ),
	                    'options'       => [
	                        'rental'    => esc_html__( 'Rental', 'ova-brw' ),
	                        'shop'      => esc_html__( 'Shop', 'ova-brw' ),
	                    ],
	                    'default'       => 'rental'
	                ],
	                [
	                	'type'      => OVABRW_PREFIX.'textarea',
	                    'id'        => OVABRW_PREFIX.'archive_price_format',
	                    'name'      => esc_html__( 'Display Price In Format', 'ova-brw' ),
	                    'desc'      => __( 'For example: [regular_price] / [unit]<br>
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
	                        <em>[max_price]</em>: Display maximum timeslot price (rental type: Appointment)', 'ova-brw' ),
	                    'custom_attributes' => [
	                        'rows' => '5',
	                    ]
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'archive_options'
	            ]
	        ];

	        return apply_filters( OVABRW_PREFIX.'get_settings_for_archive_section', $settings );
	    }

	    /**
	     * Product details section.
	     */
	    protected function get_settings_for_detail_section() {
	        // Get templates from elementor
	        $templates = get_posts([
	        	'post_type'     => 'elementor_library', 
	            'meta_key'      => '_elementor_template_type', 
	            'meta_value'    => 'page',
	            'numberposts'   => -1
	        ]);
	        
	        // Default template
	        $default_template = 'default';

	        $list_templates = [ 'default' => esc_html__( 'Classic', 'ova-brw' ) ];

	        if ( ovabrw_global_typography() ) {
	            $list_templates['modern'] 	= esc_html__( 'Modern', 'ova-brw' );
	            $default_template 			= 'modern';
	        }

	        if ( ovabrw_array_exists( $templates ) ) {
	            foreach ( $templates as $template ) {
	                $template_id    = $template->ID;
	                $template_title = $template->post_title;

	                // Push
	                $list_templates[$template_id] = $template_title;
	            }
	        }

	        // Get list page
	        $all_pages      = get_pages();
	        $list_page['']  = esc_html__( 'Select a page...', 'ova-brw' );

	        if ( ovabrw_array_exists( $all_pages ) ) {
	            foreach ( $all_pages as $page ) {
	                $page_id    = $page->ID;
	                $page_title = $page->post_title;
	                $page_link  = get_page_link( $page_id );

	                // Add page
	                $list_page[$page_link] = $page_title;
	            }
	        }

	        $settings = [
	        	// Product template
	        	[
	        		'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'detail_options'
	        	],
	                [
	                	'type'      => 'select',
	                    'id'        => 'ova_brw_template_elementor_template',
	                    'name'      => esc_html__( 'Product Template', 'ova-brw' ),
	                    'desc'      => esc_html__( 'Classic/Modern or Other (made in Templates of Elementor )', 'ova-brw' ),
	                    'class'     => '',
	                    'options'   => $list_templates,
	                    'default'   => $default_template
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'detail_options'
	            ], // END Product template

	            // Product detail tab
	            [
	            	'type'  => OVABRW_PREFIX.'before_accordion',
	                'title' => esc_html__( 'General Setting', 'ova-brw' )
	            ],
	                // General
	                [
	                	'type'  => 'title',
	                    'title' => ''
	                ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_feature_image',
	                        'name'          => esc_html__( 'Show Feature Image/ Gallery', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_title',
	                        'name'          => esc_html__( 'Show Title', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_price',
	                        'name'          => esc_html__( 'Show Price', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_meta',
	                        'name'          => esc_html__( 'Show Product Meta', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_review_product',
	                        'name'          => esc_html__( 'Show Product Review', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_related_product',
	                        'name'          => esc_html__( 'Show Related Product', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_specifications',
	                        'name'          => esc_html__( 'Show Specifications', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_feature',
	                        'name'          => esc_html__( 'Show Feature', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_special_feature',
	                        'name'          => esc_html__( 'Show Special in Feature', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_table_price',
	                        'name'          => esc_html__( 'Show Price Table', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_open_table_price',
	                        'name'          => esc_html__( 'Always Open Price Table', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_maintenance',
	                        'name'          => esc_html__( 'Show Disabled Dates', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          => 'checkbox',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_place',
	                        'name'          => esc_html__( 'Show Place', 'ova-brw' ),
	                        'default'       => 'yes',
	                        'tooltip'       => esc_html__( 'You must enter a Google Maps API Key from Dashboard >> Woocommerce >> Settings >> Booking & Rental >> General' ),
	                        'checkboxgroup' => 'start'
	                    ],
	                    [
	                    	'type'          	=> 'number',
	                        'id'            	=> OVABRW_PREFIX_OPTIONS.'product_place_priority',
	                        'name'          	=> esc_html__( 'Position display for Place', 'ova-brw' ),
	                        'default'       	=> '22',
	                        'desc'      		=> esc_html__( 'Insert number. 9 - The first tab. 11 - behind Description tab. 21 - beside  Additional information  Tab . 31 - behind Review Tab ', 'ova-brw' ),
	                        'custom_attributes' => [
	                        	'min' => 0
	                        ]
	                    ],
	                    [
	                    	'type'      => OVABRW_PREFIX.'textarea',
	                        'id'        => OVABRW_PREFIX.'single_price_format',
	                        'name'      => esc_html__( 'Display Price In Format', 'ova-brw' ),
	                        'desc'      => __( 'For example: [regular_price] / [unit]<br>
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
	                            <em>[max_price]</em>: Display maximum timeslot price (rental type: Appointment)', 'ova-brw' ),
	                        'custom_attributes' => [
	                            'rows' => '5'
	                        ]
	                    ],
	                [
	                	'type' => 'sectionend'
	                ], // END General
	                
	                // Extra tab
	                [
	                	'type'  => OVABRW_PREFIX.'before',
	                    'class' => 'ovabrw-checkbox-options'
	                ],
		                [
		                	'type' => 'title',
		                ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_extra_tab',
		                        'name'          => esc_html__( 'Show Extra Tab', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                [
		                	'type' => 'sectionend'
		                ],
	                [
	                	'type' => OVABRW_PREFIX.'after'
	                ],
	                
	                [
	                	'type'  => OVABRW_PREFIX.'before',
	                    'id'    => 'ovabrw-extra-tab-options',
	                    'css'   => 'display: none'
	                ],
		                [
		                	'type' => 'title'
		                ],
			                [
			                	'type'      => OVABRW_PREFIX.'textarea',
			                    'id'        => OVABRW_PREFIX_OPTIONS.'extra_tab_shortcode_form',
			                    'row_class' => 'ovabrw-extra-tab',
			                    'name'      => esc_html__( 'Display content', 'ova-brw' ),
			                    'desc'      => esc_html__( 'Insert a shortcode or text', 'ova-brw' )
			                ],
			                [
			                	'type'      => 'text',
			                    'id'        => OVABRW_PREFIX_OPTIONS.'extra_tab_order_tab',
			                    'row_class' => 'ovabrw-extra-tab',
			                    'name'      => esc_html__( 'Position display', 'ova-brw' ),
			                    'default'   => '30',
			                    'desc'      => esc_html__( 'Insert number. 9 - The first tab. 11 - behind Description tab. 21 - beside  Additional information  Tab . 31 - behind Review Tab ', 'ova-brw' )
			                ],
		                [
		                	'type' => 'sectionend'
		                ],
	                [
	                	'type'  => OVABRW_PREFIX.'after'
	                ], // END Extra tab

	                // Calendar
	                [
	                	'type'  => OVABRW_PREFIX.'before',
	                    'class' => 'ovabrw-checkbox-options'
	                ],
		                [
		                	'type' => 'title'
		                ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_calendar',
		                        'name'          => esc_html__( 'Show Calendar', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                [
		                	'type' => 'sectionend'
		                ],
	                [
	                	'type' => OVABRW_PREFIX.'after'
	                ],

	                [
	                	'type'  => OVABRW_PREFIX.'before',
	                    'id'    => 'ovabrw-calendar-options',
	                    'css'   => 'display: none'
	                ],
		                [
		                	'type' => 'title'
		                ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'calendar_show_nav_month',
		                        'name'          => esc_html__( 'Show Month View', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'calendar_show_nav_week',
		                        'name'          => esc_html__( 'Show Week View', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'calendar_show_nav_day',
		                        'name'          => esc_html__( 'Show Day View', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'calendar_show_nav_list',
		                        'name'          => esc_html__( 'Show List View', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'      => 'radio',
		                        'id'        => OVABRW_PREFIX_OPTIONS.'calendar_default_view',
		                        'row_class' => 'ovabrw-calendar-default-view ovabrw-radio',
		                        'name'      => esc_html__( 'Default View', 'ova-brw' ),
		                        'options'   => [
		                            'dayGridMonth'  => esc_html__( 'Month', 'ova-brw' ),
		                            'timeGridWeek'  => esc_html__( 'Week', 'ova-brw' ),
		                            'timeGridDay'   => esc_html__( 'Day', 'ova-brw' ),
		                            'listWeek'      => esc_html__( 'List', 'ova-brw' )
		                        ],
		                        'default'   => 'dayGridMonth'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_time_in_calendar',
		                        'name'          => esc_html__( 'Show booked time', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start',
		                        'desc'          => '<p>'.esc_html__( 'Show the following time are booked', 'ova-brw' ).'</p>'
		                    ],
		                    [
		                    	'title'         => esc_html__( 'Text color of available dates', 'ova-brw' ),
		                        'type'          => 'color',
		                        'id'            => OVABRW_PREFIX.'color_available_calendar',
		                        'row_class'     => OVABRW_PREFIX.'colorpick',
		                        'default'       => '#222222',
		                        'placeholder'   => '#222222',
		                        'desc'          => esc_html__( 'The text color of the available dates in the Calendar.', 'ova-brw' ),
		                        'desc_tip'      => true,
		                        'css'           => 'max-width:365px;'
		                    ],
		                    [
		                    	'name'          => esc_html__( 'Background of available dates', 'ova-brw' ),
		                        'type'          => 'color',
		                        'id'            => OVABRW_PREFIX.'bg_calendar_available',
		                        'row_class'     => OVABRW_PREFIX.'colorpick',
		                        'default'       => '#fff',
		                        'placeholder'   => '#FFFFFF',
		                        'desc'          => esc_html__( 'The background of the available dates in the Calendar.', 'ova-brw' ),
		                        'desc_tip'      => true,
		                        'css'           => 'max-width:365px;'
		                    ],
		                    [
		                    	'title'         => esc_html__( 'Text color of disable dates', 'ova-brw' ),
		                        'type'          => 'color',
		                        'id'            => OVABRW_PREFIX.'color_disable_calendar',
		                        'row_class'     => OVABRW_PREFIX.'colorpick',
		                        'default'       => '#FFFFFF',
		                        'placeholder'   => '#FFFFFF',
		                        'desc'          => esc_html__( 'The text color of the disable dates in the Calendar.', 'ova-brw' ),
		                        'desc_tip'      => true,
		                        'css'           => 'max-width:365px;'
		                    ],
		                    [
		                    	'type'          => 'color',
		                        'id'            => OVABRW_PREFIX.'bg_disable_calendar',
		                        'name'          => esc_html__( 'Background of disabled dates', 'ova-brw' ),
		                        'default'       => '#E56E00',
		                        'placeholder'   => '#E56E00',
		                        'desc'          => esc_html__( 'The background of the disable dates in the Calendar.', 'ova-brw' ),
		                        'desc_tip'      => true,
		                        'css'           => 'max-width:365px;'
		                    ],
		                    [
		                    	'title'         => esc_html__( 'Text color of booked dates', 'ova-brw' ),
		                        'type'          => 'color',
		                        'id'            => OVABRW_PREFIX.'color_booked_calendar',
		                        'row_class'     => OVABRW_PREFIX.'colorpick',
		                        'default'       => '#FFFFFF',
		                        'placeholder'   => '#FFFFFF',
		                        'desc'          => esc_html__( 'The text color of the booked dates in the Calendar.', 'ova-brw' ),
		                        'desc_tip'      => true,
		                        'css'           => 'max-width:365px;'
		                    ],
		                    [
		                    	'type'          => 'color',
		                        'id'            => OVABRW_PREFIX.'bg_booked_calendar',
		                        'name'          => esc_html__( 'Background of booked dates', 'ova-brw' ),
		                        'default'       => '#E56E00',
		                        'placeholder'   => '#E56E00',
		                        'desc'          => esc_html__( 'The background of the booked dates in the Calendar.', 'ova-brw' ),
		                        'desc_tip'      => true,
		                        'css'           => 'max-width:365px;'
		                    ],
		                [
		                	'type' => 'sectionend'
		                ],
	                [
	                	'type' => OVABRW_PREFIX.'after'
	                ], // END Calendar
	            [
	            	'type' => OVABRW_PREFIX.'after_accordion'
	            ], // END product tab
	            
	            // Booking form tab
	            [
	            	'type'  => OVABRW_PREFIX.'before_accordion',
	                'title' => esc_html__( 'Booking Form', 'ova-brw' )
	            ],
	                // Show booking form options
	                [
	                	'type'  => OVABRW_PREFIX.'before',
	                    'class' => 'ovabrw-checkbox-options'
	                ],
		                [
		                	'type' => 'title'
		                ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_booking_form',
		                        'row_class'     => 'ovabrw-radio-accordion',
		                        'name'          => esc_html__( 'Show Booking Form', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                [
		                	'type' => 'sectionend'
		                ],
	                [
	                	'type'  => OVABRW_PREFIX.'after',
	                    'class' => 'ovabrw-checkbox-options'
	                ], // END Show booking form options

	                // Fields
	                [
	                	'type'  => OVABRW_PREFIX.'before',
	                    'id'    => 'ovabrw-booking-options',
	                    'css'   => 'display: none'
	                ],
	                	// Show fields
		                [
		                	'type' => 'title'
		                ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_show_number_vehicle',
		                        'name'          => esc_html__( 'Show Quantity', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_show_pickup_location',
		                        'name'          => esc_html__( 'Show Pick-up Location', 'ova-brw' ),
		                        'default'       => 'no',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_show_pickoff_location',
		                        'name'          => esc_html__( 'Show Drop-off Location', 'ova-brw' ),
		                        'default'       => 'no',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_show_dropoff_date',
		                        'name'          => esc_html__( 'Show Drop-off Date', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_show_extra_resource',
		                        'name'          => esc_html__( 'Show Resource', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_show_extra_service',
		                        'name'          => esc_html__( 'Show Service', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_show_extra',
		                        'name'          => esc_html__( 'Show Resource & Service', 'ova-brw' ),
		                        'default'       => 'no',
		                        'checkboxgroup' => 'start',
		                        'desc'          => '<p>'.esc_html__( 'Apply for in Cart, Checkout, Order Detail pages', 'ova-brw' ).'</p>'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_show_availables_vehicle',
		                        'name'          => esc_html__( 'Show Items available', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start',
		                        'desc'          => '<p>'.esc_html__( 'Display number of items available after entering infomation', 'ova-brw' ).'</p>'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_show_insurance_amount',
		                        'name'          => esc_html__( 'Show Insurance Amount', 'ova-brw' ),
		                        'default'       => 'no',
		                        'checkboxgroup' => 'start',
		                        'desc'          => '<p>'.esc_html__( 'Display insurance amount after entering infomation', 'ova-brw' ).'</p>'
		                    ],
		                [
		                	'type' => 'sectionend'
		                ], // END Show fields

		                // Condition
		                [
		                	'type' => 'title'
		                ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'booking_form_terms_conditions',
		                        'name'          => esc_html__( 'Show Terms and conditions', 'ova-brw' ),
		                        'default'       => 'no',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'      		=> OVABRW_PREFIX.'textarea',
		                        'id'        		=> OVABRW_PREFIX_OPTIONS.'booking_form_terms_conditions_content',
		                        'name'      		=> '',
		                        'desc'      		=> esc_html__( 'You can insert text or HTML', 'ova-brw' ),
		                        'default'   		=> sprintf( esc_html__( 'I have read and agree to the website %s', 'ova-brw' ), '<a href="https://demo.ovatheme.com/brw/" target="_blank">terms and conditions</a>' ),
		                        'custom_attributes' => [
		                            'rows' => '5'
		                        ]
		                    ],
		                [
		                	'type' => 'sectionend'
		                ], // END Condition

		                // Email
		                [
		                	'type' => 'title'
		                ],
		                    [
		                    	'type'          => 'text',
		                        'id'            => OVABRW_PREFIX.'booking_recipient',
		                        'name'          => esc_html__( 'Recipient(s)', 'ova-brw' ),
		                        'desc_tip'      => true,
		                        'desc'          => esc_html__( 'Emails separated by ",".', 'ova-brw' ),
		                        'placeholder'   => esc_html__( 'email_1@gmail.com, email_2@gmail.com', 'ova-brw' )
		                    ],
		                [
		                	'type' => 'sectionend'
		                ], // END Email                
	                [
	                	'type' => OVABRW_PREFIX.'after'
	                ],
	            [
	            	'type' => OVABRW_PREFIX.'after_accordion'
	            ],
	            // End booking form tab
	            
	            // Request form tab
	            [
	            	'type'  => OVABRW_PREFIX.'before_accordion',
	                'title' => esc_html__( 'Request Form', 'ova-brw' )
	            ],
	                // Show request form checkbox
	                [
	                	'type'  => OVABRW_PREFIX.'before',
	                    'class' => 'ovabrw-checkbox-options'
	                ],
		                [
		                	'type' => 'title'
		                ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'template_show_request_booking',
		                        'row_class'     => 'ovabrw-radio-accordion',
		                        'name'          => esc_html__( 'Show Request Form', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                [
		                	'type' => 'sectionend'
		                ],
	                [
	                	'type'  => OVABRW_PREFIX.'after',
	                    'class' => 'ovabrw-checkbox-options'
	                ], // END Show request form checkbox

	                // Request form options
	                [
	                	'type'  => OVABRW_PREFIX.'before',
	                    'id'    => 'ovabrw-request-options',
	                    'css'   => 'display: none'
	                ],
	                	// Show fields
		                [
		                	'type' => 'title',
		                ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_form_show_number_vehicle',
		                        'name'          => esc_html__( 'Show Quantity', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_form_show_pickup_location',
		                        'name'          => esc_html__( 'Show Pick-up Location', 'ova-brw' ),
		                        'default'       => 'no',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_form_show_pickoff_location',
		                        'name'          => esc_html__( 'Show Drop-off Location', 'ova-brw' ),
		                        'default'       => 'no',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_form_show_pickoff_date',
		                        'name'          => esc_html__( 'Show Drop-off Date', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_form_show_extra_service',
		                        'name'          => esc_html__( 'Show Resource', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_form_show_service',
		                        'name'          => esc_html__( 'Show Service', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_form_show_number',
		                        'name'          => esc_html__( 'Show Number Phone', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_form_show_address',
		                        'name'          => esc_html__( 'Show Address', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_form_show_extra_info',
		                        'name'          => esc_html__( 'Show Extra Info', 'ova-brw' ),
		                        'default'       => 'yes',
		                        'checkboxgroup' => 'start'
		                    ],
		                [
		                	'type' => 'sectionend'
		                ], // END Show fields

		                // Condition
		                [
		                	'type' => 'title'
		                ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_terms_conditions',
		                        'name'          => esc_html__( 'Show Terms and conditions', 'ova-brw' ),
		                        'default'       => 'no',
		                        'checkboxgroup' => 'start'
		                    ],
		                    [
		                    	'type'      		=> OVABRW_PREFIX.'textarea',
		                        'id'        		=> OVABRW_PREFIX_OPTIONS.'request_booking_terms_conditions_content',
		                        'name'      		=> '',
		                        'desc'      		=> esc_html__( 'You can insert text or HTML', 'ova-brw' ),
		                        'default'   		=> sprintf( esc_html__( 'I have read and agree to the website %s', 'ova-brw' ), '<a href="https://demo.ovatheme.com/brw/" target="_blank">terms and conditions</a>' ),
		                        'custom_attributes' => [
		                            'rows' => '5'
		                        ]
		                    ],
		                    [
		                    	'type'      => 'text',
		                        'id'        => OVABRW_PREFIX_OPTIONS.'request_booking_form_order_tab',
		                        'name'      => esc_html__( 'Position display', 'ova-brw' ),
		                        'default'   => '9',
		                        'desc'      => esc_html__( 'Only Apply for Classic Template. Insert number. 9 - The first tab. 11 - behind Description tab. 31 - behind Review Tab', 'ova-brw' )
		                    ],
		                [
		                	'type' => 'sectionend'
		                ], // END Condition

		                // Email
		                [
		                	'type'  => 'title',
		                    'title' => esc_html__( 'Email Settings', 'ova-brw' )
		                ],
		                    [
		                    	'type'      => 'radio',
		                        'id'        => OVABRW_PREFIX.'request_booking_send_to',
		                        'row_class' => ' ovabrw-radio',
		                        'name'      => esc_html__( 'Send email to', 'ova-brw' ),
		                        'options'   => [
		                            'admin'     => esc_html__( 'Admin', 'ova-brw' ),
		                            'customer'  => esc_html__( 'Customer', 'ova-brw' ),
		                            'both'      => esc_html__( 'Both', 'ova-brw' )
		                        ],
		                        'default'   => 'both'
		                    ],
		                    [
		                    	'type'      => 'text',
		                        'id'        => OVABRW_PREFIX_OPTIONS.'request_booking_mail_subject',
		                        'name'      => esc_html__( 'Subject', 'ova-brw' ),
		                        'desc'      => esc_html__( 'The subject displays in the email list', 'ova-brw' ),
		                        'default'   => esc_html__( 'Request For Booking', 'ova-brw' )
		                    ],
		                    [
		                    	'type'      => 'text',
		                        'id'        => OVABRW_PREFIX_OPTIONS.'request_booking_mail_from_name',
		                        'name'      => esc_html__( 'From name', 'ova-brw' ),
		                        'desc'      => esc_html__( 'The subject displays in mail detail', 'ova-brw' ),
		                        'default'   => esc_html__( 'Request For Booking', 'ova-brw' )
		                    ],
		                    [
		                    	'type'      => 'text',
		                        'id'        => OVABRW_PREFIX_OPTIONS.'request_booking_mail_from_email',
		                        'name'      => esc_html__( 'Send from email', 'ova-brw' ),
		                        'desc'      => esc_html__( 'The customer will know them to receive mail from which email address is', 'ova-brw' ),
		                        'default'   => get_option( 'admin_email' )
		                    ],
		                    [
		                    	'type'          => 'text',
		                        'id'            => OVABRW_PREFIX.'request_booking_recipient',
		                        'name'          => esc_html__( 'Recipient(s)', 'ova-brw' ),
		                        'desc_tip'      => true,
		                        'desc'          => esc_html__( 'Emails separated by ",".', 'ova-brw' ),
		                        'placeholder'   => esc_html__( 'email_1@gmail.com, email_2@gmail.com', 'ova-brw' )
		                    ],
		                    [
		                    	'title'     => esc_html__( 'Email Content', 'ova-brw' ),
		                        'type'      => OVABRW_PREFIX.'editor',
		                        'id'        => 'ova_brw_request_booking_mail_content',
		                        'default'   => 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] to [ovabrw_order_pickoff_date].<br>[ovabrw_order_details]',
		                        'height'    => 300,
		                        'desc'      => __( 'You can use shortcodes:<br>
		                            [ovabrw_vehicle_name]<br>
		                            [ovabrw_order_pickup_date]<br>
		                            [ovabrw_order_pickoff_date]<br>
		                            [ovabrw_order_details]<br>', 'ova-brw' ),
		                        'desc_tip'  => esc_html__( 'This is the content email in the reminder email.', 'ova-brw' )
		                    ],
		                [
		                	'type' => 'sectionend'
		                ], // END Email

		                // Create Other Settings Request Booking
		                [
		                	'type'  => 'title',
		                    'title' => esc_html__( 'Other Settings', 'ova-brw' )
		                ],
		                    [
		                    	'type'      		=> 'select',
		                        'id'        		=> OVABRW_PREFIX_OPTIONS.'request_booking_form_thank_page',
		                        'class'     		=> 'wc-enhanced-select-nostd',
		                        'name'      		=> esc_html__( 'Thank you page', 'ova-brw' ),
		                        'options'   		=> $list_page,
		                        'desc'      		=> esc_html__( 'The system will redirect to the "thank you" page after sending successfully', 'ova-brw' ),
		                        'css'       		=> 'min-width:300px;',
		                        'custom_attributes' => [
		                        	'data-placeholder' => esc_html__( 'Select a page...', 'ova-brw' )
		                        ]
		                    ],
		                    [
		                    	'type'      		=> 'select',
		                        'id'        		=> OVABRW_PREFIX_OPTIONS.'request_booking_form_error_page',
		                        'class'     		=> 'wc-enhanced-select-nostd',
		                        'name'      		=> esc_html__( 'Error Page', 'ova-brw' ),
		                        'options'   		=> $list_page,
		                        'css'       		=> 'min-width:300px;',
		                        'desc'      		=> esc_html__( 'The system will redirect to the "Error" page if sending is\'t successfull.', 'ova-brw' ),
		                        'custom_attributes' => [
		                        	'data-placeholder' => esc_html__( 'Select a page...', 'ova-brw' )
		                        ]
		                    ],
		                    [
		                    	'type'          => 'checkbox',
		                        'id'            => OVABRW_PREFIX_OPTIONS.'request_booking_create_order',
		                        'name'          => esc_html__( 'Allows creating new orders', 'ova-brw' ),
		                        'default'       => 'no',
		                        'checkboxgroup' => 'start',
		                        'desc'          => '<p>'.esc_html__( 'After the form is sent, the system will create new order', 'ova-brw' ).'</p>'
		                    ],
		                    [
		                    	'type'      => 'radio',
		                        'id'        => OVABRW_PREFIX_OPTIONS.'request_booking_order_status',
		                        'row_class' => 'order-status ovabrw-radio',
		                        'name'      => esc_html__( 'Order Status', 'ova-brw' ),
		                        'options'   => [
		                            'wc-pending'    => esc_html__( 'Pending payment', 'ova-brw' ),
		                            'wc-processing' => esc_html__( 'Processing', 'ova-brw' ),
		                            'wc-on-hold'    => esc_html__( 'On hold', 'ova-brw' ),
		                            'wc-completed'  => esc_html__( 'Completed', 'ova-brw' ),
		                            'wc-cancelled'  => esc_html__( 'Cancelled', 'ova-brw' )
		                        ],
		                        'default'   => 'wc-on-hold'
		                    ],
		                [
		                	'type' => 'sectionend'
		                ], // END Create Other Settings Request Booking
	                [
	                	'type' => OVABRW_PREFIX.'after'
	                ], // END Request form options
	            [
	            	'type' => OVABRW_PREFIX.'after_accordion'
	            ], // End request form tab
	        ];

	        return apply_filters( OVABRW_PREFIX.'get_settings_for_detail_section', $settings );
	    }

	    /**
	     * reCAPTCHA section.
	     */
	    protected function get_settings_for_recaptcha_section() {
	        $settings = [
	        	[
	        		'type'  => 'title',
	                'title' => esc_html__( 'reCAPTCHA', 'ova-brw' ),
	                'id'    => OVABRW_PREFIX.'recaptcha_options'
	        	],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'recapcha_enable',
	                    'name'          => esc_html__( 'Enable', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'recaptcha_options'
	            ],

	            // reCAPTCHA type
	            [
	            	'type'  => 'ovabrw_before',
	                'id'    => 'ovabrw-recapcha-options',
	                'css'   => 'display: none'
	            ],
	                [
	                	'type'  => 'title'
	                ],
	                    [
	                    	'type'      => 'radio',
	                        'id'        => OVABRW_PREFIX_OPTIONS.'recapcha_form',
	                        'row_class' => 'ovabrw-recapcha-form ovabrw-radio',
	                        'name'      => esc_html__( 'Display in', 'ova-brw' ),
	                        'options'   => [
	                            'booking'   => esc_html__( 'Booking form', 'ova-brw' ),
	                            'request'   => esc_html__( 'Request form', 'ova-brw' ),
	                            'both'      => esc_html__( 'Both', 'ova-brw' )
	                        ],
	                        'default'   => 'both'
	                    ],
	                    [
	                    	'type'      => 'radio',
	                        'id'        => OVABRW_PREFIX_OPTIONS.'recapcha_type',
	                        'row_class' => 'ovabrw-recapcha-type ovabrw-radio',
	                        'name'      => esc_html__( 'reCAPTCHA type', 'ova-brw' ),
	                        'options'   => [
	                            'v3' => esc_html__( 'Score based (v3)', 'ova-brw' ),
	                            'v2' => esc_html__( 'Challenge (v2)', 'ova-brw' ),
	                        ],
	                        'default'   => 'v3'
	                    ],
	                [
	                	'type' => 'sectionend',
	                ],
	            [
	            	'type'  => 'ovabrw_after',
	            ], // End reCAPTCHA type

	            // reCAPTCHA v3
	            [
	            	'type'  => 'ovabrw_before',
	                'id'    => 'ovabrw-recapcha-v3-options',
	                'css'   => 'display: none'
	            ],
	                [
	                	'type'  => 'title',
	                    'title' => esc_html__( 'reCAPTCHA v3', 'ova-brw' )
	                ],
	                    [
	                    	'type'          => 'text',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'recapcha_v3_site_key',
	                        'name'          => esc_html__( 'Site key', 'ova-brw' ),
	                        'placeholder'   => esc_html__( 'Insert your site key' )
	                    ],
	                    [
	                    	'type'          => 'text',
	                        'id'            => OVABRW_PREFIX_OPTIONS.'recapcha_v3_secret_key',
	                        'name'          => esc_html__( 'Secret key', 'ova-brw' ),
	                        'placeholder'   => esc_html__( 'Insert your secret key' )
	                    ],
	                [
	                	'type' => 'sectionend',
	                ],
	            [
	            	'type'  => 'ovabrw_after',
	            ], // End reCAPTCHA v3

	            // reCAPTCHA v2
	            [
	            	'type'  => 'ovabrw_before',
	                'id'    => 'ovabrw-recapcha-v2-options',
	                'css'   => 'display: none'
	            ],
	                [
	                	'type'  => 'title',
	                    'title' => esc_html__( 'reCAPTCHA v2', 'ova-brw' ),
	                ],
	                    [
	                    	'type'          => 'text',
	                        'id'            => 'ova_brw_recapcha_v2_site_key',
	                        'name'          => esc_html__( 'Site key', 'ova-brw' ),
	                        'placeholder'   => esc_html__( 'Insert your site key' )
	                    ],
	                    [
	                    	'type'          => 'text',
	                        'id'            => 'ova_brw_recapcha_v2_secret_key',
	                        'name'          => esc_html__( 'Secret key', 'ova-brw' ),
	                        'placeholder'   => esc_html__( 'Insert your secret key' )
	                    ],
	                [
	                	'type' => 'sectionend',
	                ],
	            [
	            	'type'  => 'ovabrw_after',
	            ] // End reCAPTCHA v2
	        ];

	        return apply_filters( OVABRW_PREFIX.'get_settings_for_recaptcha_section', $settings );
	    }

	    /**
	     * Deposit section.
	     */
	    protected function get_settings_for_deposit_section() {
	        $settings = [
	        	[
	            	'title' => esc_html__( 'Remaining Amount', 'ova-brw' ),
	                'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'remaining_options'
	            ],
	                [
	                	'title'     => esc_html__( 'Send order detail to customer', 'ova-brw' ),
	                    'id'        => 'send_email_remaining_invoice_enable',
	                    'default'   => 'yes',
	                    'type'      => 'checkbox',
	                    'tooltip'   => esc_html__( 'Email the order for the remaining amount to the customer.', 'ova-brw' )
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'remaining_cron_options',
	            ],
	            [
	            	'title' => esc_html__( 'Automatically Create Order Details for Remaining Amount', 'ova-brw' ),
	                'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'remaining_cron_options',
	                'desc'  => esc_html__( 'X days before the customer\'s Check-in date, the order detail for Remaining Amount will be automatically created and sent to the customer\'s email (If the order detail has not been created manually).', 'ova-brw' )
	            ],
	            [
	            	'type'  => 'title'
	            ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => 'remaining_invoice_enable',
	                    'name'          => esc_html__( 'Enable', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	            [
	            	'type' => 'sectionend'
	            ],

	            // Remaining amount options
	            [
	            	'type'  => 'ovabrw_before',
	                'id'    => 'ovabrw-remaining-invoice-options',
	                'css'   => 'display: none;'
	            ],
	                [
	                	'type'  => 'title',
	                ],
	                    [
	                    	'type'      => 'text',
	                        'id'        => 'remaining_invoice_before_xday',
	                        'class'     => 'ovabrw-input-float',
	                        'name'      => esc_html__( 'X day before pick-up date', 'ova-brw' ),
	                        'default'   => 1
	                    ],
	                    [
	                    	'type'      		=> 'number',
	                        'id'        		=> 'remaining_invoice_per_seconds',
	                        'name'      		=> esc_html__( 'Check periodically every X seconds for creating a remaining invoice', 'ova-brw' ),
	                        'default'   		=> 86400,
	                        'custom_attributes' => [
	                        	'min' => 0
	                        ]
	                    ],
	                [
	                	'type' => 'sectionend',
	                ],
	            [
	            	'type'  => 'ovabrw_after',
	                'id'    => 'ovabrw-remaining-invoice-options'
	            ] // END remaining amount options
	        ];

	        return apply_filters( OVABRW_PREFIX.'get_settings_for_deposit_section', $settings );
	    }

	    /**
	     * Search section.
	     */
	    protected function get_settings_for_search_section() {
	        $settings = [
	        	[
	            	'title' => esc_html__( 'Search Setting', 'ova-brw' ),
	                'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'search_options',
	                'desc'  => esc_html__( 'When you use [ovabrw_search /] and don\'t insert params, the shortcode will use value here.', 'ova-brw' )
	            ],
	                [
	                	'type'      => 'radio',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'search_template',
	                    'row_class' => 'ovabrw-search-template ovabrw-radio',
	                    'name'      => esc_html__( 'Template', 'ova-brw' ),
	                    'default'   => 'modern',
	                    'options'   => [
	                        'modern'    => esc_html__( 'Modern', 'ova-brw' ),
	                        'classic'   => esc_html__( 'Classic', 'ova-brw' )
	                    ]
	                ],
	                [
	                	'type'      => 'radio',
	                    'id'        => OVABRW_PREFIX_OPTIONS.'search_column',
	                    'row_class' => 'ovabrw-search-column ovabrw-radio',
	                    'name'      => esc_html__( 'Column', 'ova-brw' ),
	                    'default'   => 'one-column',
	                    'options'   => [
	                        'one-column'    => esc_html__( 'One Column', 'ova-brw' ),
	                        'two-column'    => esc_html__( 'Two Column', 'ova-brw' ),
	                        'three-column'  => esc_html__( 'Three Column', 'ova-brw' ),
	                        'four-column'   => esc_html__( 'Four Column', 'ova-brw' ),
	                        'five-column'   => esc_html__( 'Five Column', 'ova-brw' ),
	                    ]
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_name_product',
	                    'name'          => esc_html__( 'Show Product Name', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_attribute',
	                    'name'          => esc_html__( 'Show Attribute', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_tag_product',
	                    'name'          => esc_html__( 'Show Product Tag', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_pick_up_location',
	                    'name'          => esc_html__( 'Show Pick-up Location', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_drop_off_location',
	                    'name'          => esc_html__( 'Show Drop-off Location', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_pick_up_date',
	                    'name'          => esc_html__( 'Show Pick-up Date', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_drop_off_date',
	                    'name'          => esc_html__( 'Show Drop-off Date', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_hour',
	                    'name'          => esc_html__( 'Show Hour', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_category',
	                    'name'          => esc_html__( 'Show Category', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type' => 'text',
	                    'id'   => OVABRW_PREFIX_OPTIONS.'search_cat_remove',
	                    'name' => esc_html__( 'Remove Categories in dropdown', 'ova-brw' ),
	                    'desc' => esc_html__( 'Insert ID of category. Example 42, 15', 'ova-brw' )
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_show_taxonomy',
	                    'name'          => esc_html__( 'Show Taxonomy', 'ova-brw' ),
	                    'default'       => 'yes',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'  			=> 'ovabrw_textarea',
	                    'id'    			=> OVABRW_PREFIX_OPTIONS.'search_hide_taxonomy_slug',
	                    'name'  			=> esc_html__( 'Hide Taxonomy List', 'ova-brw' ),
	                    'desc'  			=> esc_html__( 'Insert slug here and separated by ","' ),
	                    'custom_attributes' => [
	                        'rows' => '3',
	                    ]
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_require_name_product',
	                    'name'          => esc_html__( 'Require Product Name', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_require_attribute',
	                    'name'          => esc_html__( 'Require Attribute', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_require_tag_product',
	                    'name'          => esc_html__( 'Require Product Tag', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_require_pick_up_location',
	                    'name'          => esc_html__( 'Require Pick-up Location', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_require_drop_off_location',
	                    'name'          => esc_html__( 'Require Drop-off Location', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_require_pick_up_date',
	                    'name'          => esc_html__( 'Require Pick-up Date', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_require_drop_off_date',
	                    'name'          => esc_html__( 'Require Drop-off Date', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX_OPTIONS.'search_require_category',
	                    'name'          => esc_html__( 'Require Category', 'ova-brw' ),
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	                [
	                	'type'  			=> 'ovabrw_textarea',
	                    'id'    			=> OVABRW_PREFIX_OPTIONS.'search_require_taxonomy_slug',
	                    'name'  			=> esc_html__( 'Require Taxonomy List', 'ova-brw' ),
	                    'desc'  			=> esc_html__( 'Insert slug here and separated by "," ','ova-brw' ),
	                    'custom_attributes' => [
	                        'rows' => '3',
	                    ]
	                ],
	            [
	            	'type' => 'sectionend',
	                'id'   => OVABRW_PREFIX.'search_options'
	            ]
	        ];

	        return apply_filters( OVABRW_PREFIX.'get_settings_for_search_section', $settings );
	    }

	    /**
	     * Cancel section.
	     */
	    protected function get_settings_for_cancel_section() {
	        $settings = [
	        	[
	            	'title' => esc_html__( 'Cancellation Policy', 'ova-brw' ),
	                'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'cancel_options'
	            ],
	                [
	                	'name'          => esc_html__( 'Minimum time required before canceling (hours)', 'ova-brw' ),
	                    'type'          => 'text',
	                    'default'       => 0,
	                    'id'            => OVABRW_PREFIX_OPTIONS.'cancel_before_x_hours',
	                    'class'         => 'ovabrw-input-float',
	                    'placeholder'   => 1.5
	                ],
	                [
	                	'name'          => esc_html__( 'Cancellation is accepted if the total order is less than x amount', 'ova-brw' ),
	                    'type'          => 'text',
	                    'default'       => 1,
	                    'id'            => OVABRW_PREFIX_OPTIONS.'cancel_condition_total_order',
	                    'class'         => 'ovabrw-input-float',
	                    'placeholder'   => 10.5
	                ],
	            [
	            	'type'  => 'sectionend',
	                'id'    => OVABRW_PREFIX.'cancel_options'
	            ]
	        ];

	        return apply_filters( OVABRW_PREFIX_OPTIONS.'get_settings_for_cancel_section', $settings );
	    }

	    /**
	     * Reminder section.
	     */
	    protected function get_settings_for_reminder_section() {
	        $settings = [
	        	// Reminder of Pick-up date
	            [
	            	'type'  => 'title',
	                'title' => esc_html__( 'Reminder of Pick-up date', 'ova-brw' ),
	                'id'    => OVABRW_PREFIX.'reminder_pickup_options'
	            ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => 'remind_mail_enable',
	                    'name'          => esc_html__( 'Enable', 'ova-brw' ),
	                    'desc'          => '<p>'.esc_html__( 'Allow to send mail to customer', 'ova-brw' ).'</p>',
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	            [
	            	'type'  => 'sectionend',
	                'id'    => OVABRW_PREFIX.'reminder_pickup_options'
	            ],
	            [
	            	'type'  => OVABRW_PREFIX.'before',
	                'id'    => 'ovabrw-reminder-options',
	                'css'   => 'display: none;'
	            ],
	                [
	                	'type'  => 'title',
	                ],
	                    [
	                    	'name'      => esc_html__( 'X day before pick-up date', 'ova-brw' ),
	                        'type'      => 'text',
	                        'class'     => 'ovabrw-input-float',
	                        'default'   => 1,
	                        'id'        => 'remind_mail_before_xday'
	                    ],
	                    [
	                    	'name'      => esc_html__( 'Send a recurring email every X seconds after the initial one.', 'ova-brw' ),
	                        'type'      => 'number',
	                        'class'     => '',
	                        'default'   => 86400,
	                        'id'        => 'remind_mail_send_per_seconds',
	                    ],
	                    [
	                    	'name'      => esc_html__( 'Subject', 'ova-brw' ),
	                        'type'      => 'text',
	                        'desc'      => esc_html__( 'The subject displays in the email list', 'ova-brw' ),
	                        'default'   => esc_html__( 'Remind Pick-up date', 'ova-brw' ) ,
	                        'id'        => 'reminder_mail_subject',
	                    ],
	                    [
	                    	'name'      => esc_html__( 'From name', 'ova-brw' ),
	                        'type'      => 'text',
	                        'desc'      => esc_html__( 'The subject displays in mail detail', 'ova-brw' ),
	                        'default'   => esc_html__( 'Remind Pick-up date', 'ova-brw' ) ,
	                        'id'        => 'reminder_mail_from_name',
	                    ],
	                    [
	                    	'name'      => esc_html__( 'Send from email', 'ova-brw' ),
	                        'type'      => 'text',
	                        'desc'      => esc_html__( 'The customer will know them to receive mail from which email address is', 'ova-brw' ),
	                        'default'   => get_option( 'admin_email' ),
	                        'id'        => 'reminder_mail_from_email',
	                    ],
	                    [
	                    	'title'     => esc_html__( 'Email Content', 'ova-brw' ),
	                        'type'      => 'ovabrw_editor',
	                        'id'        => 'reminder_mail_content',
	                        'default'   => 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date]',
	                        'height'    => 300,
	                        'desc'      => __( 'Use tags to generate email template.<br>For example: You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] and returned the vehicle at [ovabrw_order_dropoff_date].', 'ova-brw' ),
	                        'desc_tip'  => esc_html__( 'This is the content email in the reminder of pick-up date email.', 'ova-brw' )
	                    ],
	                [
	                	'type' => 'sectionend',
	                ],
	            [
	            	'type'  => OVABRW_PREFIX.'after',
	                'id'    => 'ovabrw-reminder-options'
	            ], // End reminder of Pick-up date

	            // Reminder of Dropoff-up date
	            [
	            	'type'  => 'title',
	                'title' => esc_html__( 'Reminder of Drop-off date', 'ova-brw' ),
	                'id'    => OVABRW_PREFIX.'reminder_dropoff_options'
	            ],
	                [
	                	'type'          => 'checkbox',
	                    'id'            => OVABRW_PREFIX.'remind_dropoff_date_mail',
	                    'name'          => esc_html__( 'Enable', 'ova-brw' ),
	                    'desc'          => '<p>'.esc_html__( 'Allow to send mail to customer', 'ova-brw' ).'</p>',
	                    'default'       => 'no',
	                    'checkboxgroup' => 'start'
	                ],
	            [
	            	'type'  => 'sectionend',
	                'id'    => OVABRW_PREFIX.'reminder_dropoff_options'
	            ],
	            [
	            	'type'  => OVABRW_PREFIX.'before',
	                'id'    => 'ovabrw-reminder-dropoff-date-options',
	                'css'   => 'display: none;'
	            ],
	                [
	                	'type'  => 'title',
	                ],
	                    [
	                    	'name'      => esc_html__( 'X day before drop-off date', 'ova-brw' ),
	                        'type'      => 'text',
	                        'class'     => 'ovabrw-input-float',
	                        'default'   => 1,
	                        'id'        => 'remind_dropoff_date_mail_before_xday',
	                    ],
	                    [
	                    	'name'      => esc_html__( 'Send a recurring email every X seconds after the initial one.', 'ova-brw' ),
	                        'type'      => 'number',
	                        'class'     => '',
	                        'default'   => 86400,
	                        'id'        => 'remind_dropoff_date_mail_send_per_seconds',
	                    ],
	                    [
	                    	'name'      => esc_html__( 'Subject', 'ova-brw' ),
	                        'type'      => 'text',
	                        'desc'      => esc_html__( 'The subject displays in the email list', 'ova-brw' ),
	                        'default'   => esc_html__( 'Remind Drop-off date', 'ova-brw' ) ,
	                        'id'        => 'reminder_dropoff_date_mail_subject',
	                    ],
	                    [
	                    	'name'      => esc_html__( 'From name', 'ova-brw' ),
	                        'type'      => 'text',
	                        'desc'      => esc_html__( 'The subject displays in mail detail', 'ova-brw' ),
	                        'default'   => esc_html__( 'Remind Drop-off date', 'ova-brw' ) ,
	                        'id'        => 'reminder_dropoff_date_mail_from_name',
	                    ],
	                    [
	                    	'name'      => esc_html__( 'Send from email', 'ova-brw' ),
	                        'type'      => 'text',
	                        'desc'      => esc_html__( 'The customer will know them to receive mail from which email address is', 'ova-brw' ),
	                        'default'   => get_option( 'admin_email' ),
	                        'id'        => 'reminder_dropoff_date_mail_from_email',
	                    ],
	                    [
	                    	'title'     => esc_html__( 'Email Content', 'ova-brw' ),
	                        'type'      => 'ovabrw_editor',
	                        'id'        => 'reminder_dropoff_date_mail_content',
	                        'default'   => 'You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] and returned the vehicle at [ovabrw_order_dropoff_date].',
	                        'height'    => 300,
	                        'desc'      => __( 'Use tags to generate email template.<br>For example: You have hired a vehicle: [ovabrw_vehicle_name] at [ovabrw_order_pickup_date] and returned the vehicle at [ovabrw_order_dropoff_date].', 'ova-brw' ),
	                        'desc_tip'  => esc_html__( 'This is the content email in the reminder of drop-off date email.', 'ova-brw' )
	                    ],
	                [
	                	'type' => 'sectionend',
	                ],
	            [
	            	'type'  => OVABRW_PREFIX.'after',
	                'id'    => 'ovabrw-reminder-dropoff-date-options'
	            ], // End reminder of Dropoff-up date
	        ];

	        return apply_filters( OVABRW_PREFIX.'get_settings_for_reminder_section', $settings );
	    }

	    /**
	     * Manage order section.
	     */
	    protected function get_settings_for_order_section() {
	        $settings = [
	        	[
	            	'type'  => 'title',
	                'title' => esc_html__( 'Admin Settings', 'ova-brw' ),
	                'id'    => OVABRW_PREFIX.'manage_order_option',
	                'desc'  => esc_html__( 'The fields are sorted ascending. To hide the field, enter the number: 0 or empty', 'ova-brw' )
	            ],
	                [
	                	'name'      => esc_html__( 'Show ID', 'ova-brw' ),
	                    'type'      => 'number',
	                    'class'     => '',
	                    'default'   => 1,
	                    'id'        => 'admin_manage_order_show_id',
	                ],
	                [
	                	'name'      => esc_html__( 'Show Customer', 'ova-brw' ),
	                    'type'      => 'number',
	                    'class'     => '',
	                    'default'   => 2,
	                    'id'        => 'admin_manage_order_show_customer',
	                ],
	                [
	                	'name'      => esc_html__( 'Show Time', 'ova-brw' ),
	                    'type'      => 'number',
	                    'class'     => '',
	                    'default'   => 3,
	                    'id'        => 'admin_manage_order_show_time',
	                ],
	                [
	                	'name'      => esc_html__( 'Show Location', 'ova-brw' ),
	                    'type'      => 'number',
	                    'class'     => '',
	                    'default'   => 4,
	                    'id'        => 'admin_manage_order_show_location',
	                ],
	                [
	                	'name'      => esc_html__( 'Show Deposit Status', 'ova-brw' ),
	                    'type'      => 'number',
	                    'class'     => '',
	                    'default'   => 5,
	                    'id'        => 'admin_manage_order_show_deposit',
	                ],
	                [
	                	'name'      => esc_html__( 'Show Insurance Status', 'ova-brw' ),
	                    'type'      => 'number',
	                    'class'     => '',
	                    'default'   => 6,
	                    'id'        => 'admin_manage_order_show_insurance',
	                ],
	                [
	                	'name'      => esc_html__( 'Show Vehicle', 'ova-brw' ),
	                    'type'      => 'number',
	                    'class'     => '',
	                    'default'   => 7,
	                    'id'        => 'admin_manage_order_show_vehicle',
	                ],
	                [
	                	'name'      => esc_html__( 'Show Product', 'ova-brw' ),
	                    'type'      => 'number',
	                    'class'     => '',
	                    'default'   => 8,
	                    'id'        => 'admin_manage_order_show_product',
	                ],
	                [
	                	'name'      => esc_html__( 'Show Order Status', 'ova-brw' ),
	                    'type'      => 'number',
	                    'class'     => '',
	                    'default'   => 9,
	                    'id'        => 'admin_manage_order_show_order_status',
	                ],
	            [
	            	'type'  => 'sectionend',
	                'id'    => OVABRW_PREFIX.'manage_order_option',
	            ]
	        ];

	        return apply_filters( OVABRW_PREFIX.'get_settings_for_order_section', $settings );
	    }

	    /**
	     * Get settings for the typography section.
	     */
	    protected function get_settings_for_typography_section() {
	        $settings = [
	        	[
	        		'type'  => 'title',
	                'id'    => OVABRW_PREFIX.'global_typography',
	                'title' => esc_html__( 'Global Typography & Color', 'ova-brw' )
	        	],
	                [
	                	'type'      => 'checkbox',
	                    'id'        => OVABRW_PREFIX.'enable_global_typography',
	                    'class'     => OVABRW_PREFIX.'enable_global_typography',
	                    'title'     => esc_html__( 'Enable Typography & Color', 'ova-brw' ),
	                    'default'   => 'yes'
	                ],
	            [
	            	'type'  => 'sectionend',
	                'id'    => OVABRW_PREFIX.'global_typography',
	            ],
	        ];

	        return apply_filters( OVABRW_PREFIX.'get_settings_for_typography_section', $settings );
	    }
	}

	new OVABRW_Rental_Settings();
}