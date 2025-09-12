<?php
/**
 * Smart Rentals WC Admin class.
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Admin' ) ) {

	class Smart_Rentals_WC_Admin {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Admin menu
			add_action( 'admin_menu', [ $this, 'admin_menu' ] );

			// Add rental checkbox to product general tab
			add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_rental_checkbox' ] );

			// Add rental fields after checkbox
			add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_rental_fields' ] );

			// Save rental meta
			add_action( 'woocommerce_process_product_meta', [ $this, 'save_rental_meta' ], 11, 2 );

			// Enqueue admin scripts
			add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_scripts' ] );

			// Add rental columns to product list
			add_filter( 'manage_product_posts_columns', [ $this, 'add_product_columns' ] );
			add_action( 'manage_product_posts_custom_column', [ $this, 'product_column_content' ], 10, 2 );

			// Add rental filter to product list
			add_action( 'restrict_manage_posts', [ $this, 'add_rental_filter' ] );
			add_filter( 'parse_query', [ $this, 'filter_products_by_rental' ] );

			// Modify product price display in admin
			add_filter( 'woocommerce_get_price_html', [ $this, 'get_rental_price_html' ], 11, 2 );
		}

		/**
		 * Admin menu
		 */
		public function admin_menu() {
			add_menu_page(
				__( 'Smart Rentals', 'smart-rentals-wc' ),
				__( 'Smart Rentals', 'smart-rentals-wc' ),
				'manage_woocommerce',
				'smart-rentals-wc',
				[ $this, 'admin_page' ],
				'dashicons-calendar-alt',
				56
			);

			add_submenu_page(
				'smart-rentals-wc',
				__( 'Settings', 'smart-rentals-wc' ),
				__( 'Settings', 'smart-rentals-wc' ),
				'manage_woocommerce',
				'smart-rentals-wc-settings',
				[ $this, 'settings_page' ]
			);

			add_submenu_page(
				'smart-rentals-wc',
				__( 'Bookings', 'smart-rentals-wc' ),
				__( 'Bookings', 'smart-rentals-wc' ),
				'manage_woocommerce',
				'smart-rentals-wc-bookings',
				[ $this, 'bookings_page' ]
			);

			add_submenu_page(
				'smart-rentals-wc',
				__( 'Booking Calendar', 'smart-rentals-wc' ),
				__( 'Booking Calendar', 'smart-rentals-wc' ),
				'manage_woocommerce',
				'smart-rentals-wc-booking-calendar',
				[ $this, 'booking_calendar_page' ]
			);
		}

		/**
		 * Admin page
		 */
		public function admin_page() {
			$stats = Smart_Rentals_WC()->options->get_rental_statistics();
			$upcoming_rentals = Smart_Rentals_WC()->booking->get_upcoming_rentals( 5 );
			$active_rentals = Smart_Rentals_WC()->booking->get_active_rentals( 5 );
			
			?>
			<div class="wrap smart-rentals-dashboard">
				<h1><?php _e( 'Smart Rentals Dashboard', 'smart-rentals-wc' ); ?></h1>
				
				<div class="smart-rentals-stats">
					<div class="smart-rentals-stat-box">
						<h3><?php echo esc_html( $stats['total_rentals'] ); ?></h3>
						<p><?php _e( 'Total Rentals', 'smart-rentals-wc' ); ?></p>
					</div>
					<div class="smart-rentals-stat-box">
						<h3><?php echo esc_html( $stats['active_rentals'] ); ?></h3>
						<p><?php _e( 'Active Rentals', 'smart-rentals-wc' ); ?></p>
					</div>
					<div class="smart-rentals-stat-box">
						<h3><?php echo esc_html( $stats['upcoming_rentals'] ); ?></h3>
						<p><?php _e( 'Upcoming Rentals', 'smart-rentals-wc' ); ?></p>
					</div>
					<div class="smart-rentals-stat-box">
						<h3><?php echo function_exists( 'wc_price' ) ? wc_price( $stats['total_revenue'] ) : '$' . number_format( $stats['total_revenue'], 2 ); ?></h3>
						<p><?php _e( 'Total Revenue', 'smart-rentals-wc' ); ?></p>
					</div>
				</div>

				<div class="smart-rentals-content">
					<div class="smart-rentals-section">
						<h2><?php _e( 'Upcoming Rentals', 'smart-rentals-wc' ); ?></h2>
						<?php if ( smart_rentals_wc_array_exists( $upcoming_rentals ) ) : ?>
							<table class="wp-list-table widefat fixed striped">
								<thead>
									<tr>
										<th><?php _e( 'Product', 'smart-rentals-wc' ); ?></th>
										<th><?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?></th>
										<th><?php _e( 'Drop-off Date', 'smart-rentals-wc' ); ?></th>
										<th><?php _e( 'Order', 'smart-rentals-wc' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $upcoming_rentals as $rental ) : ?>
									<tr>
										<td><?php echo esc_html( $rental->product_name ); ?></td>
										<td><?php echo esc_html( $rental->pickup_date ); ?></td>
										<td><?php echo esc_html( $rental->dropoff_date ); ?></td>
										<td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $rental->order_id . '&action=edit' ) ); ?>">#<?php echo esc_html( $rental->order_id ); ?></a></td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<p><?php _e( 'No upcoming rentals.', 'smart-rentals-wc' ); ?></p>
						<?php endif; ?>
					</div>

					<div class="smart-rentals-section">
						<h2><?php _e( 'Active Rentals', 'smart-rentals-wc' ); ?></h2>
						<?php if ( smart_rentals_wc_array_exists( $active_rentals ) ) : ?>
							<table class="wp-list-table widefat fixed striped">
								<thead>
									<tr>
										<th><?php _e( 'Product', 'smart-rentals-wc' ); ?></th>
										<th><?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?></th>
										<th><?php _e( 'Drop-off Date', 'smart-rentals-wc' ); ?></th>
										<th><?php _e( 'Order', 'smart-rentals-wc' ); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ( $active_rentals as $rental ) : ?>
									<tr>
										<td><?php echo esc_html( $rental->product_name ); ?></td>
										<td><?php echo esc_html( $rental->pickup_date ); ?></td>
										<td><?php echo esc_html( $rental->dropoff_date ); ?></td>
										<td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $rental->order_id . '&action=edit' ) ); ?>">#<?php echo esc_html( $rental->order_id ); ?></a></td>
									</tr>
									<?php endforeach; ?>
								</tbody>
							</table>
						<?php else : ?>
							<p><?php _e( 'No active rentals.', 'smart-rentals-wc' ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			</div>
			<?php
		}

		/**
		 * Settings page
		 */
		public function settings_page() {
			if ( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'smart_rentals_wc_settings' ) ) {
				$this->save_settings();
			}

			$settings = smart_rentals_wc_get_option( 'settings', [] );
			?>
			<div class="wrap">
				<h1><?php _e( 'Smart Rentals Settings', 'smart-rentals-wc' ); ?></h1>
				<form method="post" action="">
					<?php wp_nonce_field( 'smart_rentals_wc_settings' ); ?>
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="date_format"><?php _e( 'Date Format', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<select name="date_format" id="date_format">
									<option value="Y-m-d" <?php selected( smart_rentals_wc_get_meta_data( 'date_format', $settings, 'Y-m-d' ), 'Y-m-d' ); ?>>Y-m-d</option>
									<option value="m/d/Y" <?php selected( smart_rentals_wc_get_meta_data( 'date_format', $settings ), 'm/d/Y' ); ?>>m/d/Y</option>
									<option value="d-m-Y" <?php selected( smart_rentals_wc_get_meta_data( 'date_format', $settings ), 'd-m-Y' ); ?>>d-m-Y</option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="time_format"><?php _e( 'Time Format', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<select name="time_format" id="time_format">
									<option value="H:i" <?php selected( smart_rentals_wc_get_meta_data( 'time_format', $settings, 'H:i' ), 'H:i' ); ?>>H:i (24h)</option>
									<option value="h:i a" <?php selected( smart_rentals_wc_get_meta_data( 'time_format', $settings ), 'h:i a' ); ?>>h:i a (12h)</option>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="enable_calendar"><?php _e( 'Enable Calendar', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="enable_calendar" id="enable_calendar" value="yes" <?php checked( smart_rentals_wc_get_meta_data( 'enable_calendar', $settings, 'yes' ), 'yes' ); ?> />
								<label for="enable_calendar"><?php _e( 'Show calendar on product pages', 'smart-rentals-wc' ); ?></label>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="enable_deposits"><?php _e( 'Enable Deposits', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<input type="checkbox" name="enable_deposits" id="enable_deposits" value="yes" <?php checked( smart_rentals_wc_get_meta_data( 'enable_deposits', $settings, 'no' ), 'yes' ); ?> />
								<label for="enable_deposits"><?php _e( 'Allow partial payments and deposits', 'smart-rentals-wc' ); ?></label>
							</td>
						</tr>
					</table>
					<?php submit_button(); ?>
				</form>
			</div>
			<?php
		}

		/**
		 * Save settings
		 */
		private function save_settings() {
			$settings = [
				'date_format' => sanitize_text_field( $_POST['date_format'] ?? 'Y-m-d' ),
				'time_format' => sanitize_text_field( $_POST['time_format'] ?? 'H:i' ),
				'enable_calendar' => isset( $_POST['enable_calendar'] ) ? 'yes' : 'no',
				'enable_deposits' => isset( $_POST['enable_deposits'] ) ? 'yes' : 'no',
			];

			smart_rentals_wc_update_option( 'settings', $settings );
			
			echo '<div class="notice notice-success"><p>' . __( 'Settings saved!', 'smart-rentals-wc' ) . '</p></div>';
		}

		/**
		 * Bookings page
		 */
		public function bookings_page() {
			global $wpdb;
			
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			$bookings = [];
			
			// Get bookings from database if table exists
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$bookings = $wpdb->get_results("
					SELECT b.*, p.post_title as product_name 
					FROM $table_name b
					LEFT JOIN {$wpdb->posts} p ON b.product_id = p.ID
					ORDER BY b.pickup_date DESC
					LIMIT 50
				");
			}

			?>
			<div class="wrap">
				<h1><?php _e( 'Rental Bookings', 'smart-rentals-wc' ); ?></h1>
				
				<?php if ( smart_rentals_wc_array_exists( $bookings ) ) : ?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php _e( 'ID', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Product', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Drop-off Date', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Quantity', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Status', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Total', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Order', 'smart-rentals-wc' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $bookings as $booking ) : ?>
							<tr>
								<td><?php echo esc_html( $booking->id ); ?></td>
								<td><?php echo esc_html( $booking->product_name ); ?></td>
								<td><?php echo esc_html( $booking->pickup_date ); ?></td>
								<td><?php echo esc_html( $booking->dropoff_date ); ?></td>
								<td><?php echo esc_html( $booking->quantity ); ?></td>
								<td>
									<span class="status-<?php echo esc_attr( $booking->status ); ?>">
										<?php echo esc_html( ucfirst( $booking->status ) ); ?>
									</span>
								</td>
								<td><?php echo function_exists( 'wc_price' ) ? wc_price( $booking->total_price ) : '$' . number_format( $booking->total_price, 2 ); ?></td>
								<td><a href="<?php echo esc_url( admin_url( 'post.php?post=' . $booking->order_id . '&action=edit' ) ); ?>">#<?php echo esc_html( $booking->order_id ); ?></a></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p><?php _e( 'No rental bookings found.', 'smart-rentals-wc' ); ?></p>
				<?php endif; ?>
			</div>
			<?php
		}

		/**
		 * Add rental checkbox to product general tab
		 */
		public function add_rental_checkbox() {
			global $post;

			// Only show checkbox if WooCommerce functions are available
			if ( !function_exists( 'woocommerce_wp_checkbox' ) ) {
				return;
			}

			woocommerce_wp_checkbox([
				'id' => smart_rentals_wc_meta_key( 'enable_rental' ),
				'label' => __( 'Rental Product', 'smart-rentals-wc' ),
				'description' => __( 'Enable rental/booking functionality for this product', 'smart-rentals-wc' ),
				'desc_tip' => true,
			]);
		}

		/**
		 * Add rental fields after checkbox
		 */
		public function add_rental_fields() {
			global $post;

			// Only show fields if WooCommerce functions are available
			if ( !function_exists( 'woocommerce_wp_select' ) || !function_exists( 'woocommerce_wp_text_input' ) ) {
				return;
			}

			echo '<div id="smart-rentals-fields" style="display: none;">';
			
			// Rental type
			woocommerce_wp_select([
				'id' => smart_rentals_wc_meta_key( 'rental_type' ),
				'label' => __( 'Rental Type', 'smart-rentals-wc' ),
				'options' => [
					'' => __( 'Select rental type', 'smart-rentals-wc' ),
					'day' => __( 'Daily', 'smart-rentals-wc' ),
					'hour' => __( 'Hourly', 'smart-rentals-wc' ),
					'mixed' => __( 'Mixed (Daily/Hourly)', 'smart-rentals-wc' ),
					// Disabled options (kept for future use)
					'period_time' => __( 'Package/Period', 'smart-rentals-wc' ) . ' - ' . __( 'Coming Soon', 'smart-rentals-wc' ),
					'transportation' => __( 'Transportation', 'smart-rentals-wc' ) . ' - ' . __( 'Coming Soon', 'smart-rentals-wc' ),
					'hotel' => __( 'Hotel/Accommodation', 'smart-rentals-wc' ) . ' - ' . __( 'Coming Soon', 'smart-rentals-wc' ),
					'appointment' => __( 'Appointment', 'smart-rentals-wc' ) . ' - ' . __( 'Coming Soon', 'smart-rentals-wc' ),
					'taxi' => __( 'Taxi/Distance', 'smart-rentals-wc' ) . ' - ' . __( 'Coming Soon', 'smart-rentals-wc' ),
				],
				'desc_tip' => true,
				'description' => __( 'Select the rental pricing type. Currently only Daily, Hourly, and Mixed types are fully supported.', 'smart-rentals-wc' ),
			]);

			// Daily price
			woocommerce_wp_text_input([
				'id' => smart_rentals_wc_meta_key( 'daily_price' ),
				'label' => __( 'Daily Price', 'smart-rentals-wc' ) . ' (' . ( function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$' ) . ')',
				'placeholder' => '0.00',
				'type' => 'number',
				'custom_attributes' => [
					'step' => '0.01',
					'min' => '0',
				],
			]);

			// Hourly price
			woocommerce_wp_text_input([
				'id' => smart_rentals_wc_meta_key( 'hourly_price' ),
				'label' => __( 'Hourly Price', 'smart-rentals-wc' ) . ' (' . ( function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$' ) . ')',
				'placeholder' => '0.00',
				'type' => 'number',
				'custom_attributes' => [
					'step' => '0.01',
					'min' => '0',
				],
			]);

			// Minimum rental period (optional)
			woocommerce_wp_text_input([
				'id' => smart_rentals_wc_meta_key( 'min_rental_period' ),
				'label' => __( 'Min Rental Period', 'smart-rentals-wc' ) . ' (' . __( 'Optional', 'smart-rentals-wc' ) . ')',
				'placeholder' => __( 'Leave blank for no minimum', 'smart-rentals-wc' ),
				'type' => 'number',
				'custom_attributes' => [
					'min' => '0',
				],
				'desc_tip' => true,
				'description' => __( 'Minimum rental duration in days/hours. Leave blank to allow any duration.', 'smart-rentals-wc' ),
			]);

			// Maximum rental period (optional)
			woocommerce_wp_text_input([
				'id' => smart_rentals_wc_meta_key( 'max_rental_period' ),
				'label' => __( 'Max Rental Period', 'smart-rentals-wc' ) . ' (' . __( 'Optional', 'smart-rentals-wc' ) . ')',
				'placeholder' => __( 'Leave blank for no maximum', 'smart-rentals-wc' ),
				'type' => 'number',
				'custom_attributes' => [
					'min' => '0',
				],
				'desc_tip' => true,
				'description' => __( 'Maximum rental duration in days/hours. Leave blank to allow unlimited duration.', 'smart-rentals-wc' ),
			]);

			// Inventory/Stock
			woocommerce_wp_text_input([
				'id' => smart_rentals_wc_meta_key( 'rental_stock' ),
				'label' => __( 'Rental Stock', 'smart-rentals-wc' ),
				'placeholder' => '1',
				'type' => 'number',
				'custom_attributes' => [
					'min' => '1',
				],
				'desc_tip' => true,
				'description' => __( 'Number of items available for rental', 'smart-rentals-wc' ),
			]);

			// Security deposit
			woocommerce_wp_text_input([
				'id' => smart_rentals_wc_meta_key( 'security_deposit' ),
				'label' => __( 'Security Deposit', 'smart-rentals-wc' ) . ' (' . ( function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$' ) . ')',
				'placeholder' => '0.00',
				'type' => 'number',
				'custom_attributes' => [
					'step' => '0.01',
					'min' => '0',
				],
				'desc_tip' => true,
				'description' => __( 'Security deposit amount', 'smart-rentals-wc' ),
			]);

			// Show Availability Calendar option
			woocommerce_wp_checkbox([
				'id' => smart_rentals_wc_meta_key( 'show_calendar' ),
				'label' => __( 'Show Availability Calendar', 'smart-rentals-wc' ),
				'desc_tip' => true,
				'description' => __( 'Display a monthly calendar showing availability and daily pricing below the product image. This is for informational purposes only and does not affect the booking form.', 'smart-rentals-wc' ),
			]);

			echo '</div>';
		}

		/**
		 * Save rental meta
		 */
		public function save_rental_meta( $post_id, $post ) {
			// Enable rental
			$enable_rental = isset( $_POST[smart_rentals_wc_meta_key( 'enable_rental' )] ) ? 'yes' : 'no';
			smart_rentals_wc_update_post_meta( $post_id, 'enable_rental', $enable_rental );

			// Only save other fields if rental is enabled
			if ( 'yes' === $enable_rental ) {
				// Validate rental type (only allow active types)
				$rental_type = isset( $_POST[smart_rentals_wc_meta_key( 'rental_type' )] ) ? sanitize_text_field( $_POST[smart_rentals_wc_meta_key( 'rental_type' )] ) : '';
				$allowed_types = [ 'day', 'hour', 'mixed' ];
				
				if ( $rental_type && !in_array( $rental_type, $allowed_types ) ) {
					// Reset to empty if invalid type selected
					$rental_type = '';
					add_action( 'admin_notices', function() {
						echo '<div class="notice notice-error"><p>' . __( 'Invalid rental type selected. Please choose Daily, Hourly, or Mixed.', 'smart-rentals-wc' ) . '</p></div>';
					});
				}
				
				$fields = [
					'rental_type' => 'text',
					'daily_price' => 'price',
					'hourly_price' => 'price',
					'min_rental_period' => 'number',
					'max_rental_period' => 'number',
					'rental_stock' => 'number',
					'security_deposit' => 'price',
					'show_calendar' => 'checkbox',
				];

				foreach ( $fields as $field => $type ) {
					// Special handling for rental_type validation
					if ( $field === 'rental_type' && isset( $_POST[smart_rentals_wc_meta_key( $field )] ) ) {
						$submitted_type = sanitize_text_field( $_POST[smart_rentals_wc_meta_key( $field )] );
						$allowed_types = [ 'day', 'hour', 'mixed' ];
						
						if ( !in_array( $submitted_type, $allowed_types ) ) {
							// Don't save invalid rental type
							continue;
						}
					}
					$meta_key = smart_rentals_wc_meta_key( $field );
					
					if ( isset( $_POST[$meta_key] ) ) {
						$value = $_POST[$meta_key];
						
						switch ( $type ) {
							case 'price':
								$value = smart_rentals_wc_format_price( $value );
								break;
							case 'number':
								// Allow empty values for optional period fields
								if ( in_array( $field, [ 'min_rental_period', 'max_rental_period' ] ) && empty( $value ) ) {
									$value = '';
								} else {
									$value = smart_rentals_wc_format_number( $value );
								}
								break;
							case 'checkbox':
								$value = 'yes';
								break;
							default:
								$value = sanitize_text_field( $value );
								break;
						}
						
						smart_rentals_wc_update_post_meta( $post_id, $field, $value );
					} elseif ( 'checkbox' === $type ) {
						smart_rentals_wc_update_post_meta( $post_id, $field, 'no' );
					}
				}

				// Set WooCommerce regular price for validation
				$this->set_woocommerce_price( $post_id );
			}
		}

		/**
		 * Set WooCommerce regular price for rental products
		 */
		private function set_woocommerce_price( $post_id ) {
			$daily_price = smart_rentals_wc_get_post_meta( $post_id, 'daily_price' );
			$hourly_price = smart_rentals_wc_get_post_meta( $post_id, 'hourly_price' );

			// Set regular price for WooCommerce validation
			$price_to_set = 0;
			
			if ( $daily_price > 0 ) {
				$price_to_set = $daily_price;
			} elseif ( $hourly_price > 0 ) {
				$price_to_set = $hourly_price;
			}

			if ( $price_to_set > 0 ) {
				// Set WooCommerce regular price
				update_post_meta( $post_id, '_regular_price', $price_to_set );
				update_post_meta( $post_id, '_price', $price_to_set );
				
				// Mark as virtual product (rentals typically are)
				update_post_meta( $post_id, '_virtual', 'yes' );
				
				// Set stock status
				update_post_meta( $post_id, '_stock_status', 'instock' );
				
				smart_rentals_wc_log( "Set WooCommerce price for rental product {$post_id}: {$price_to_set}" );
			}
		}

		/**
		 * Enqueue admin scripts
		 */
		public function enqueue_admin_scripts( $hook ) {
			global $post_type;
			
			if ( 'product' === $post_type ) {
				wp_enqueue_script(
					'smart-rentals-wc-admin',
					SMART_RENTALS_WC_PLUGIN_URI . 'assets/js/admin.js',
					[ 'jquery' ],
					Smart_Rentals_WC()->get_version(),
					true
				);
				
				wp_enqueue_style(
					'smart-rentals-wc-admin',
					SMART_RENTALS_WC_PLUGIN_URI . 'assets/css/admin.css',
					[],
					Smart_Rentals_WC()->get_version()
				);
			}
		}

		/**
		 * Add rental columns to product list
		 */
		public function add_product_columns( $columns ) {
			$new_columns = [];
			
			foreach ( $columns as $key => $value ) {
				$new_columns[$key] = $value;
				
				if ( 'name' === $key ) {
					$new_columns['rental'] = __( 'Rental', 'smart-rentals-wc' );
				}
			}
			
			return $new_columns;
		}

		/**
		 * Product column content
		 */
		public function product_column_content( $column, $post_id ) {
			if ( 'rental' === $column ) {
				if ( smart_rentals_wc_is_rental_product( $post_id ) ) {
					$rental_type = smart_rentals_wc_get_post_meta( $post_id, 'rental_type' );
					echo '<span class="rental-enabled">' . __( 'Yes', 'smart-rentals-wc' ) . '</span>';
					if ( $rental_type ) {
						echo '<br><small>' . esc_html( ucfirst( $rental_type ) ) . '</small>';
					}
				} else {
					echo '<span class="rental-disabled">' . __( 'No', 'smart-rentals-wc' ) . '</span>';
				}
			}
		}

		/**
		 * Add rental filter to product list
		 */
		public function add_rental_filter() {
			global $typenow;
			
			if ( 'product' === $typenow ) {
				$current = isset( $_GET['rental_filter'] ) ? $_GET['rental_filter'] : '';
				
				echo '<select name="rental_filter">';
				echo '<option value="">' . __( 'All Products', 'smart-rentals-wc' ) . '</option>';
				echo '<option value="rental" ' . selected( $current, 'rental', false ) . '>' . __( 'Rental Products', 'smart-rentals-wc' ) . '</option>';
				echo '<option value="regular" ' . selected( $current, 'regular', false ) . '>' . __( 'Regular Products', 'smart-rentals-wc' ) . '</option>';
				echo '</select>';
			}
		}

		/**
		 * Filter products by rental
		 */
		public function filter_products_by_rental( $query ) {
			global $pagenow, $typenow;
			
			if ( 'edit.php' === $pagenow && 'product' === $typenow && isset( $_GET['rental_filter'] ) && $_GET['rental_filter'] ) {
				$meta_query = $query->get( 'meta_query' ) ?: [];
				
				if ( 'rental' === $_GET['rental_filter'] ) {
					$meta_query[] = [
						'key' => smart_rentals_wc_meta_key( 'enable_rental' ),
						'value' => 'yes',
						'compare' => '='
					];
				} elseif ( 'regular' === $_GET['rental_filter'] ) {
					$meta_query[] = [
						'relation' => 'OR',
						[
							'key' => smart_rentals_wc_meta_key( 'enable_rental' ),
							'value' => 'no',
							'compare' => '='
						],
						[
							'key' => smart_rentals_wc_meta_key( 'enable_rental' ),
							'compare' => 'NOT EXISTS'
						]
					];
				}
				
				$query->set( 'meta_query', $meta_query );
			}
		}

		/**
		 * Get rental price HTML
		 */
		public function get_rental_price_html( $price, $product ) {
			if ( smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				$rental_type = smart_rentals_wc_get_post_meta( $product->get_id(), 'rental_type' );
				$daily_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'daily_price' );
				$hourly_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'hourly_price' );
				
				$rental_price = '';
				
				if ( 'day' === $rental_type && $daily_price ) {
					$rental_price = smart_rentals_wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
				} elseif ( 'hour' === $rental_type && $hourly_price ) {
					$rental_price = smart_rentals_wc_price( $hourly_price ) . ' ' . __( 'per hour', 'smart-rentals-wc' );
				} elseif ( 'mixed' === $rental_type ) {
					if ( $daily_price && $hourly_price ) {
						$rental_price = smart_rentals_wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' ) . ' / ' . smart_rentals_wc_price( $hourly_price ) . ' ' . __( 'per hour', 'smart-rentals-wc' );
					} elseif ( $daily_price ) {
						$rental_price = smart_rentals_wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
					} elseif ( $hourly_price ) {
						$rental_price = smart_rentals_wc_price( $hourly_price ) . ' ' . __( 'per hour', 'smart-rentals-wc' );
					}
				}
				
				if ( $rental_price ) {
					return $rental_price;
				}
			}
			
			return $price;
		}

		/**
		 * Booking calendar page
		 */
		public function booking_calendar_page() {
			// Enqueue required assets
			wp_enqueue_script( 'jquery' );
			
			// Get rental product IDs
			$product_ids = Smart_Rentals_WC()->options->get_rental_product_ids();
			
			// Get events for calendar
			$events = $this->get_calendar_events();
			
			// Include the calendar template
			include SMART_RENTALS_WC_PLUGIN_PATH . 'admin/views/booking-calendar.php';
		}

		/**
		 * Get calendar events for admin booking calendar
		 */
		private function get_calendar_events() {
			global $wpdb;
			
			$events = [];
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			
			// Check if table exists
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$bookings = $wpdb->get_results("
					SELECT 
						b.*, 
						p.post_title as product_name
					FROM $table_name b
					LEFT JOIN {$wpdb->posts} p ON b.product_id = p.ID
					WHERE b.status IN ('pending', 'confirmed', 'active', 'processing', 'completed')
					ORDER BY b.pickup_date ASC
				");

				foreach ( $bookings as $booking ) {
					$events[] = [
						'id' => $booking->id,
						'title' => $booking->product_name . ' (' . $booking->quantity . ')',
						'start' => $booking->pickup_date,
						'end' => $booking->dropoff_date,
						'backgroundColor' => $this->get_booking_color( $booking->status ),
						'borderColor' => $this->get_booking_color( $booking->status ),
						'textColor' => '#ffffff',
						'extendedProps' => [
							'booking_id' => $booking->id,
							'product_id' => $booking->product_id,
							'quantity' => $booking->quantity,
							'status' => $booking->status,
							'total_price' => $booking->total_price,
							'security_deposit' => $booking->security_deposit,
						]
					];
				}
			}
			
			return $events;
		}

		/**
		 * Get booking color based on status
		 */
		private function get_booking_color( $status ) {
			$colors = [
				'pending' => '#ffc107',     // Yellow
				'confirmed' => '#28a745',   // Green
				'active' => '#17a2b8',      // Blue
				'processing' => '#fd7e14',  // Orange
				'completed' => '#6f42c1',   // Purple
				'cancelled' => '#dc3545',   // Red
			];
			
			return isset( $colors[$status] ) ? $colors[$status] : '#6c757d';
		}

		/**
		 * Main Smart_Rentals_WC_Admin Instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}

	Smart_Rentals_WC_Admin::instance();
}