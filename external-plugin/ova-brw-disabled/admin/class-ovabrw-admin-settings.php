<?php if ( !defined( 'ABSPATH' ) ) exit();

/**
 * OVABRW Admin Settings class
 */
if ( !class_exists( 'OVABRW_Admin_Settings' ) ) {

	class OVABRW_Admin_Settings {

		/**
	     * Constructor
	     */
	    public function __construct() {
	        // Add settings pages
	        add_filter( 'woocommerce_get_settings_pages', [ $this, 'add_setting_page' ] );

	        // Global typography settings
			add_action( 'woocommerce_settings_ovabrw_global_typography_after', [ $this, 'global_typography_after' ] );

			// Save global typography options
			add_action( 'woocommerce_update_option', [ $this, 'save_global_typography_options' ] );

			// Add textarea output
			add_action( 'woocommerce_admin_field_ovabrw_textarea', [ $this, 'textarea_output' ] );

			// Add editor output
			add_action( 'woocommerce_admin_field_ovabrw_editor', [ $this, 'editor_output' ] );

			// Save custom output field
			add_filter( 'woocommerce_admin_settings_sanitize_option', [ $this, 'save_custom_output_field' ], 10, 3 );

			// Before accordion output field
			add_action( 'woocommerce_admin_field_ovabrw_before_accordion', [ $this, 'before_accordion_output' ] );

			// After accordion output field
			add_action( 'woocommerce_admin_field_ovabrw_after_accordion', [ $this, 'after_accordion_output' ] );

			// Before output field
			add_action( 'woocommerce_admin_field_ovabrw_before', [ $this, 'before_output_field' ] );

			// After output field
			add_action( 'woocommerce_admin_field_ovabrw_after', [ $this, 'after_output_field' ] );
	    }

	    /**
	     * Add setting page
	     */
	    public function add_setting_page( $settings ) {
	    	$settings[] = include( OVABRW_PLUGIN_ADMIN . 'settings/class-ovabrw-rental-settings.php' );

		  	return $settings;
	    }

	    /**
	     * Global typography settings
	     */
	    public function global_typography_after() {
	    	include( OVABRW_PLUGIN_ADMIN . 'settings/views/html-wcst-global-typography.php' );
	    }

	    /**
	     * Save global typography options
	     */
	    public function save_global_typography_options() {
	    	// Get data
	    	$data = $_POST;

			if ( !ovabrw_array_exists( $data ) ) return false;

			$update_options = [];

			// Font
			if ( isset( $data[$this->get_name( 'glb_primary_font' )] ) ) {
				$update_options[$this->get_name( 'glb_primary_font' )] = trim( $data[$this->get_name( 'glb_primary_font' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_primary_font_weight' )] ) ) {
				$update_options[$this->get_name( 'glb_primary_font_weight' )] = $data[$this->get_name( 'glb_primary_font_weight' )];
			}
			if ( isset( $data[$this->get_name( 'glb_custom_font' )] ) ) {
				$update_options[$this->get_name( 'glb_custom_font' )] = trim( $data[$this->get_name( 'glb_custom_font' )] );
			} // END Font

			// Color
			if ( isset( $data[$this->get_name( 'glb_primary_color' )] ) ) {
				$update_options[$this->get_name( 'glb_primary_color' )] = trim( $data[$this->get_name( 'glb_primary_color' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_light_color' )] ) ) {
				$update_options[$this->get_name( 'glb_light_color' )] = trim( $data[$this->get_name( 'glb_light_color' )] );
			} // END Color

			// Heading
			if ( isset( $data[$this->get_name( 'glb_heading_font_size' )] ) ) {
				$update_options[$this->get_name( 'glb_heading_font_size' )] = trim( $data[$this->get_name( 'glb_heading_font_size' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_heading_font_weight' )] ) ) {
				$update_options[$this->get_name( 'glb_heading_font_weight' )] = trim( $data[$this->get_name( 'glb_heading_font_weight' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_heading_line_height' )] ) ) {
				$update_options[$this->get_name( 'glb_heading_line_height' )] = trim( $data[$this->get_name( 'glb_heading_line_height' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_heading_color' )] ) ) {
				$update_options[$this->get_name( 'glb_heading_color' )] = trim( $data[$this->get_name( 'glb_heading_color' )] );
			} // END Heading
			
			// Second Heading
			if ( isset( $data[$this->get_name( 'glb_second_heading_font_size' )] ) ) {
				$update_options[$this->get_name( 'glb_second_heading_font_size' )] = trim( $data[$this->get_name( 'glb_second_heading_font_size' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_second_heading_font_weight' )] ) ) {
				$update_options[$this->get_name( 'glb_second_heading_font_weight' )] = trim( $data[$this->get_name( 'glb_second_heading_font_weight' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_second_heading_line_height' )] ) ) {
				$update_options[$this->get_name( 'glb_second_heading_line_height' )] = trim( $data[$this->get_name( 'glb_second_heading_line_height' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_second_heading_color' )] ) ) {
				$update_options[$this->get_name( 'glb_second_heading_color' )] = trim( $data[$this->get_name( 'glb_second_heading_color' )] );
			} // END Second Heading
			
			// Label
			if ( isset( $data[$this->get_name( 'glb_label_font_size' )] ) ) {
				$update_options[$this->get_name( 'glb_label_font_size' )] = trim( $data[$this->get_name( 'glb_label_font_size' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_label_font_weight' )] ) ) {
				$update_options[$this->get_name( 'glb_label_font_weight' )] = trim( $data[$this->get_name( 'glb_label_font_weight' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_label_line_height' )] ) ) {
				$update_options[$this->get_name( 'glb_label_line_height' )] = trim( $data[$this->get_name( 'glb_label_line_height' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_label_color' )] ) ) {
				$update_options[$this->get_name( 'glb_label_color' )] = trim( $data[$this->get_name( 'glb_label_color' )] );
			} // END Label
			
			// Text
			if ( isset( $data[$this->get_name( 'glb_text_font_size' )] ) ) {
				$update_options[$this->get_name( 'glb_text_font_size' )] = trim( $data[$this->get_name( 'glb_text_font_size' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_text_font_weight' )] ) ) {
				$update_options[$this->get_name( 'glb_text_font_weight' )] = trim( $data[$this->get_name( 'glb_text_font_weight' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_text_line_height' )] ) ) {
				$update_options[$this->get_name( 'glb_text_line_height' )] = trim( $data[$this->get_name( 'glb_text_line_height' )] );
			}
			if ( isset( $data[$this->get_name( 'glb_text_color' )] ) ) {
				$update_options[$this->get_name( 'glb_text_color' )] = trim( $data[$this->get_name( 'glb_text_color' )] );
			} // END Text
			
			// Card
			if ( isset( $data[$this->get_name( 'glb_card_template' )] ) ) {
				$update_options[$this->get_name( 'glb_card_template' )] = trim( $data[$this->get_name( 'glb_card_template' )] );
			}
			
			// Get all card templates
			$card_templates = ovabrw_get_card_templates();
    		if ( !ovabrw_array_exists( $card_templates ) ) $card_templates = [];

    		// Cart item settings
			$card_prefix = 'ovabrw_glb_';

			foreach ( $card_templates as $card => $label ) {
				// Featured
				if ( isset( $data[$card_prefix.$card.'_featured'] ) ) {
					$update_options[$card_prefix.$card.'_featured'] = $data[$card_prefix.$card.'_featured'];
				}

				// Special
				if ( isset( $data[$card_prefix.$card.'_feature_featured'] ) ) {
					$update_options[$card_prefix.$card.'_feature_featured'] = $data[$card_prefix.$card.'_feature_featured'];
				}

				// Thumbnail type
				if ( isset( $data[$card_prefix.$card.'_thumbnail_type'] ) ) {
					$update_options[$card_prefix.$card.'_thumbnail_type'] = $data[$card_prefix.$card.'_thumbnail_type'];
				}

				// Thumbnail size
				if ( isset( $data[$card_prefix.$card.'_thumbnail_size'] ) ) {
					$update_options[$card_prefix.$card.'_thumbnail_size'] = $data[$card_prefix.$card.'_thumbnail_size'];
				}

				// Thumbnail height
				if ( isset( $data[$card_prefix.$card.'_thumbnail_height'] ) ) {
					$update_options[$card_prefix.$card.'_thumbnail_height'] = $data[$card_prefix.$card.'_thumbnail_height'];
				}

				// Display thumbnail
				if ( isset( $data[$card_prefix.$card.'_display_thumbnail'] ) ) {
					$update_options[$card_prefix.$card.'_display_thumbnail'] = $data[$card_prefix.$card.'_display_thumbnail'];
				}

				// Price
				if ( isset( $data[$card_prefix.$card.'_price'] ) ) {
					$update_options[$card_prefix.$card.'_price'] = $data[$card_prefix.$card.'_price'];
				}

				// Specifications
				if ( isset( $data[$card_prefix.$card.'_specifications'] ) ) {
					$update_options[$card_prefix.$card.'_specifications'] = $data[$card_prefix.$card.'_specifications'];
				}

				// Features
				if ( isset( $data[$card_prefix.$card.'_features'] ) ) {
					$update_options[$card_prefix.$card.'_features'] = $data[$card_prefix.$card.'_features'];
				}

				// Custom Taxonomy
				if ( isset( $data[$card_prefix.$card.'_custom_taxonomy'] ) ) {
					$update_options[$card_prefix.$card.'_custom_taxonomy'] = $data[$card_prefix.$card.'_custom_taxonomy'];
				}

				// Attribute
				if ( isset( $data[$card_prefix.$card.'_attribute'] ) ) {
					$update_options[$card_prefix.$card.'_attribute'] = $data[$card_prefix.$card.'_attribute'];
				}

				// Short description
				if ( isset( $data[$card_prefix.$card.'_short_description'] ) ) {
					$update_options[$card_prefix.$card.'_short_description'] = $data[$card_prefix.$card.'_short_description'];
				}

				// Review
				if ( isset( $data[$card_prefix.$card.'_review'] ) ) {
					$update_options[$card_prefix.$card.'_review'] = $data[$card_prefix.$card.'_review'];
				}

				// Button
				if ( isset( $data[$card_prefix.$card.'_button'] ) ) {
					$update_options[$card_prefix.$card.'_button'] = $data[$card_prefix.$card.'_button'];
				}
			} // END Card

			// Update options
			foreach ( $update_options as $name => $value ) {
				update_option( $name, $value );
			}
	    }

	    /**
	     * Textarea output
	     */
	    public function textarea_output( $value ) {
	    	$option_value = ovabrw_get_meta_data( 'value', $value );

			// Custom attribute handling.
			$custom_attributes = [];

			// Attribute
			$attributes = ovabrw_get_meta_data( 'custom_attributes', $value );

			if ( ovabrw_array_exists( $attributes ) ) {
				foreach ( $attributes as $attr => $attr_value ) {
					$custom_attributes[] = esc_attr( $attr ) . '="' . esc_attr( $attr_value ) . '"';
				}
			}

			// Description handling.
			$field_description = \WC_Admin_Settings::get_field_description( $value );
			$description       = $field_description['description'];
			$tooltip_html      = $field_description['tooltip_html'];
			?>
			<tr class="<?php echo esc_attr( $value['row_class'] ); ?>">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>">
						<?php echo esc_html( $value['title'] ); ?>
						<?php echo $tooltip_html; // WPCS: XSS ok. ?>
					</label>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
					<textarea
						name="<?php echo esc_attr( $value['field_name'] ); ?>"
						id="<?php echo esc_attr( $value['id'] ); ?>"
						style="<?php echo esc_attr( $value['css'] ); ?>"
						class="<?php echo esc_attr( $value['class'] ); ?>"
						placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
						<?php echo implode( ' ', $custom_attributes ); // WPCS: XSS ok. ?>
						><?php echo esc_textarea( $option_value ); // WPCS: XSS ok. ?></textarea>
					<?php echo wp_kses_post( $description ); // WPCS: XSS ok. ?>
				</td>
			</tr>
			<?php
	    }

	    /**
	     * Editor output
	     */
	    public function editor_output( $value ) {
	    	// Description handling.
			$field_description = WC_Admin_Settings::get_field_description( $value );
			$description       = $field_description['description'];
			$tooltip_html      = $field_description['tooltip_html'];

			// editor settings
			$editor_settings = [
				'editor_class' 	=> $value['class'],
				'editor_css' 	=> $value['css'],
				'editor_height' => $value['height'] ? $value['height'] : '',
				'wpautop' 		=> false
			];

			?>
			<tr class="<?php echo esc_attr( $value['row_class'] ); ?>">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?> <?php echo wp_kses_post( $tooltip_html ); ?></label>
					<?php echo wp_kses_post( $description ); ?>
				</th>
				<td class="forminp forminp-<?php echo esc_attr( sanitize_title( $value['type'] ) ); ?>">
					<?php wp_editor( $value['value'], $value['id'], $editor_settings ); ?>
				</td>
			</tr>
			<?php
	    }

	    /**
	     * Save custom output field
	     */
	    public function save_custom_output_field( $value, $option, $raw_value ) {
	    	$type 	= ovabrw_get_meta_data( 'type', $option );
			$id 	= ovabrw_get_meta_data( 'id', $option );

			if ( 'ovabrw_textarea' == $type || 'ovabrw_editor' == $type ) {
				$id 	= isset( $option['id'] ) ? $option['id'] : '';
				$value 	= wp_kses_post( trim( $raw_value ) );

				if ( 'ovabrw_editor' == $type ) {
					$value = apply_filters( OVABRW_PREFIX.'the_content', $value );
				}
			}

			// Additional CSS
			if ( 'ovabrw_additional_css' == $id ) {
				$value = html_entity_decode( $raw_value, ENT_QUOTES, 'UTF-8' );
				
				$customize_calendar = ovabrw_get_meta_data( OVABRW_PREFIX.'customize_calendar', $_POST );
				if ( $customize_calendar ) {
					// Save file
					file_put_contents( OVABRW_PLUGIN_PATH.'assets/css/datepicker/customize.css', (string)$value );
				}
			}

			return apply_filters( OVABRW_PREFIX.'save_custom_output_field', $value, $option, $raw_value );
	    }

	    /**
	     * Before accordion output
	     */
	    public function before_accordion_output( $value ) {
	    	do_action( OVABRW_PREFIX.'before_accordion_output', $value );
			?>
			<div id="<?php echo esc_attr( sanitize_title( $value['id'] ) ); ?>" class="ovabrw-accordion">
				<h2 class="ovabrw-accordion-title">
					<?php echo esc_html( $value['title'] ); ?>
				</h2>
				<div class="ovabrw-accordion-content">
			<?php
	    }

	    /**
	     * After accordion output
	     */
	    public function after_accordion_output( $value ) {
	    	?>
				</div>
			</div>
			<?php
			do_action( OVABRW_PREFIX.'after_accordion_output', $value );
	    }

	    /**
	     * Before output field
	     */
	    public function before_output_field( $value ) {
			do_action( OVABRW_PREFIX.'before_output_field', $value );
			?>
			<div id="<?php echo esc_attr( sanitize_title( $value['id'] ) ); ?>" class="<?php echo esc_attr( sanitize_title( $value['class'] ) ); ?>" style="<?php echo esc_attr( $value['css'] ); ?>">
			<?php
		}

		/**
	     * After output field
	     */
	    public function after_output_field( $value ) {
			?>
			</div>
			<?php
			do_action( OVABRW_PREFIX.'after_output_field', $value );
		}

	    /**
	     * Get name by prefix
	     */
	    public function get_name( $key = '', $display = false ) {
	    	if ( $key ) $key = OVABRW_PREFIX . sanitize_file_name( $key );
	    	if ( $display ) echo esc_attr( $key );

	        return apply_filters( OVABRW_PREFIX.'get_name_settings', $key );
	    }
	}

	new OVABRW_Admin_Settings();
}