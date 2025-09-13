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

			// Add rental checkbox to product general tab (beside Virtual/Downloadable)
			add_action( 'woocommerce_product_options_general_product_data', [ $this, 'add_rental_checkbox' ], 15 );

			// Add Rental Options tab
			add_filter( 'woocommerce_product_data_tabs', [ $this, 'add_rental_options_tab' ] );
			
			// Add rental fields to Rental Options tab
			add_action( 'woocommerce_product_data_panels', [ $this, 'add_rental_options_panel' ] );

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
								<label for="default_pickup_time"><?php _e( 'Default Pickup Time', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<input type="time" name="default_pickup_time" id="default_pickup_time" value="<?php echo esc_attr( smart_rentals_wc_get_meta_data( 'default_pickup_time', $settings, '10:00' ) ); ?>" />
								<p class="description"><?php _e( 'Default time when customers pick up rental products. This affects availability calculations.', 'smart-rentals-wc' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="default_dropoff_time"><?php _e( 'Default Dropoff Time', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<input type="time" name="default_dropoff_time" id="default_dropoff_time" value="<?php echo esc_attr( smart_rentals_wc_get_meta_data( 'default_dropoff_time', $settings, '09:30' ) ); ?>" />
								<p class="description"><?php _e( 'Default time when customers must return rental products. For one-day rentals, this is the return time next day.', 'smart-rentals-wc' ); ?></p>
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
				'default_pickup_time' => sanitize_text_field( $_POST['default_pickup_time'] ?? '10:00' ),
				'default_dropoff_time' => sanitize_text_field( $_POST['default_dropoff_time'] ?? '09:30' ),
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
		 * Add rental checkbox beside Virtual/Downloadable checkboxes
		 */
		public function add_rental_checkbox() {
			global $post;

			// Only show checkbox if WooCommerce functions are available
			if ( !function_exists( 'woocommerce_wp_checkbox' ) ) {
				return;
			}

			// Add rental checkbox inline with Virtual/Downloadable
			woocommerce_wp_checkbox([
				'id' => smart_rentals_wc_meta_key( 'enable_rental' ),
				'wrapper_class' => 'show_if_simple',
				'label' => __( 'Enable Rental Product', 'smart-rentals-wc' ),
				'description' => __( 'Enable rental/booking functionality for this product', 'smart-rentals-wc' ),
				'desc_tip' => true,
			]);
		}

		/**
		 * Add Rental Options tab to product data tabs
		 */
		public function add_rental_options_tab( $tabs ) {
			$tabs['smart_rentals'] = [
				'label'    => __( 'Rental Options', 'smart-rentals-wc' ),
				'target'   => 'smart_rentals_product_data',
				'class'    => [ 'hide_if_grouped', 'hide_if_external', 'smart_rentals_options' ],
				'priority' => 25,
			];
			return $tabs;
		}

		/**
		 * Add Rental Options tab panel content
		 */
		public function add_rental_options_panel() {
			global $post;

			// Only show panel if WooCommerce functions are available
			if ( !function_exists( 'woocommerce_wp_select' ) || !function_exists( 'woocommerce_wp_text_input' ) ) {
				return;
			}

			echo '<div id="smart_rentals_product_data" class="panel woocommerce_options_panel hidden">';
			echo '<div class="options_group">';
			
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

			echo '</div>';
			echo '<div class="options_group">';

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
				'desc_tip' => true,
				'description' => __( 'Daily rental price', 'smart-rentals-wc' ),
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
				'desc_tip' => true,
				'description' => __( 'Hourly rental price', 'smart-rentals-wc' ),
			]);

			echo '</div>';
			echo '<div class="options_group">';

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

			echo '</div>';
			echo '<div class="options_group">';

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

			echo '</div>';
			echo '<div class="options_group">';

			// Disabled Weekdays
			$disabled_weekdays = smart_rentals_wc_get_post_meta( $post->ID, 'disabled_weekdays' );
			$disabled_weekdays = is_array( $disabled_weekdays ) ? $disabled_weekdays : [];
			
			?>
			<p class="form-field">
				<label for="<?php echo smart_rentals_wc_meta_key( 'disabled_weekdays' ); ?>"><?php _e( 'Disabled Weekdays', 'smart-rentals-wc' ); ?></label>
				<select 
					id="<?php echo smart_rentals_wc_meta_key( 'disabled_weekdays' ); ?>" 
					name="<?php echo smart_rentals_wc_meta_key( 'disabled_weekdays' ); ?>[]" 
					class="wc-enhanced-select" 
					multiple="multiple" 
					data-placeholder="<?php esc_attr_e( 'Select weekdays to disable...', 'smart-rentals-wc' ); ?>"
					style="width: 100%;">
					<option value="0" <?php selected( in_array( '0', $disabled_weekdays ), true ); ?>><?php _e( 'Sunday', 'smart-rentals-wc' ); ?></option>
					<option value="1" <?php selected( in_array( '1', $disabled_weekdays ), true ); ?>><?php _e( 'Monday', 'smart-rentals-wc' ); ?></option>
					<option value="2" <?php selected( in_array( '2', $disabled_weekdays ), true ); ?>><?php _e( 'Tuesday', 'smart-rentals-wc' ); ?></option>
					<option value="3" <?php selected( in_array( '3', $disabled_weekdays ), true ); ?>><?php _e( 'Wednesday', 'smart-rentals-wc' ); ?></option>
					<option value="4" <?php selected( in_array( '4', $disabled_weekdays ), true ); ?>><?php _e( 'Thursday', 'smart-rentals-wc' ); ?></option>
					<option value="5" <?php selected( in_array( '5', $disabled_weekdays ), true ); ?>><?php _e( 'Friday', 'smart-rentals-wc' ); ?></option>
					<option value="6" <?php selected( in_array( '6', $disabled_weekdays ), true ); ?>><?php _e( 'Saturday', 'smart-rentals-wc' ); ?></option>
				</select>
				<span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Select weekdays that should be blocked for bookings. Selected days will be unavailable every week.', 'smart-rentals-wc' ); ?>"></span>
			</p>
			<?php

			// Disabled Dates
			$this->render_disabled_dates_field( $post->ID );

			// Show Availability Calendar option
			woocommerce_wp_checkbox([
				'id' => smart_rentals_wc_meta_key( 'show_calendar' ),
				'label' => __( 'Show Availability Calendar', 'smart-rentals-wc' ),
				'desc_tip' => true,
				'description' => __( 'Display a monthly calendar showing availability and daily pricing below the product image. This is for informational purposes only and does not affect the booking form.', 'smart-rentals-wc' ),
			]);

			echo '</div>';
			echo '</div>';
		}

		/**
		 * Render disabled dates field with daterangepicker UI
		 */
		private function render_disabled_dates_field( $post_id ) {
			$disabled_start_dates = smart_rentals_wc_get_post_meta( $post_id, 'disabled_start_dates' );
			$disabled_end_dates = smart_rentals_wc_get_post_meta( $post_id, 'disabled_end_dates' );
			
			$disabled_start_dates = is_array( $disabled_start_dates ) ? $disabled_start_dates : [];
			$disabled_end_dates = is_array( $disabled_end_dates ) ? $disabled_end_dates : [];
			
			?>
			<div class="smart-rentals-disabled-dates">
				<h4><?php _e( 'Disabled Dates', 'smart-rentals-wc' ); ?>
					<span class="woocommerce-help-tip" data-tip="<?php esc_attr_e( 'Block specific date ranges for bookings. Use the date range picker to select periods that should be unavailable for all customers.', 'smart-rentals-wc' ); ?>"></span>
				</h4>
				<div class="disabled-dates-container">
					<table class="widefat disabled-dates-table">
						<thead>
							<tr>
								<th class="date-range-header"><?php _e( 'Disabled Date Range', 'smart-rentals-wc' ); ?></th>
								<th class="actions-header"><?php _e( 'Actions', 'smart-rentals-wc' ); ?></th>
							</tr>
						</thead>
						<tbody class="disabled-dates-rows">
							<?php if ( !empty( $disabled_start_dates ) ) : ?>
								<?php foreach ( $disabled_start_dates as $index => $start_date ) : ?>
									<?php 
									$end_date = isset( $disabled_end_dates[$index] ) ? $disabled_end_dates[$index] : $start_date;
									$range_display = $start_date === $end_date ? $start_date : $start_date . ' - ' . $end_date;
									?>
									<tr class="disabled-date-row">
										<td>
											<input 
												type="text" 
												name="disabled_date_range_<?php echo $index; ?>" 
												value="<?php echo esc_attr( $range_display ); ?>"
												class="disabled-date-range-picker"
												placeholder="<?php esc_attr_e( 'Click to select date range', 'smart-rentals-wc' ); ?>"
												readonly />
											<input type="hidden" name="<?php echo smart_rentals_wc_meta_key( 'disabled_start_dates' ); ?>[]" value="<?php echo esc_attr( $start_date ); ?>" class="hidden-start-date" />
											<input type="hidden" name="<?php echo smart_rentals_wc_meta_key( 'disabled_end_dates' ); ?>[]" value="<?php echo esc_attr( $end_date ); ?>" class="hidden-end-date" />
										</td>
										<td>
											<button type="button" class="button remove-disabled-date" title="<?php esc_attr_e( 'Remove', 'smart-rentals-wc' ); ?>">
												<span class="dashicons dashicons-trash"></span>
											</button>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php else : ?>
								<tr class="disabled-date-row">
									<td>
										<input 
											type="text" 
											name="disabled_date_range_0" 
											value=""
											class="disabled-date-range-picker"
											placeholder="<?php esc_attr_e( 'Click to select date range', 'smart-rentals-wc' ); ?>"
											readonly />
										<input type="hidden" name="<?php echo smart_rentals_wc_meta_key( 'disabled_start_dates' ); ?>[]" value="" class="hidden-start-date" />
										<input type="hidden" name="<?php echo smart_rentals_wc_meta_key( 'disabled_end_dates' ); ?>[]" value="" class="hidden-end-date" />
									</td>
									<td>
										<button type="button" class="button remove-disabled-date" title="<?php esc_attr_e( 'Remove', 'smart-rentals-wc' ); ?>">
											<span class="dashicons dashicons-trash"></span>
										</button>
									</td>
								</tr>
							<?php endif; ?>
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2">
									<button type="button" class="button add-disabled-date">
										<span class="dashicons dashicons-plus-alt"></span>
										<?php _e( 'Add Disabled Date Range', 'smart-rentals-wc' ); ?>
									</button>
								</td>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				var rowCounter = <?php echo count( $disabled_start_dates ); ?>;
				
				// Initialize daterangepicker for existing fields
				initDateRangePickers();
				
				function initDateRangePickers() {
					$('.disabled-date-range-picker').each(function() {
						if (!$(this).data('daterangepicker')) {
							$(this).daterangepicker({
								autoApply: false,
								autoUpdateInput: false,
								showDropdowns: true,
								timePicker: true,
								timePicker24Hour: true,
								timePickerIncrement: 30,
								timePickerSeconds: false,
								linkedCalendars: false,
								alwaysShowCalendars: true,
								opens: 'center',
								drops: 'auto',
								buttonClasses: 'btn btn-sm',
								applyButtonClasses: 'btn-primary',
								cancelButtonClasses: 'btn-secondary',
								locale: {
									format: 'YYYY-MM-DD HH:mm',
									separator: ' - ',
									applyLabel: '<?php _e( 'Apply', 'smart-rentals-wc' ); ?>',
									cancelLabel: '<?php _e( 'Cancel', 'smart-rentals-wc' ); ?>',
									fromLabel: '<?php _e( 'From', 'smart-rentals-wc' ); ?>',
									toLabel: '<?php _e( 'To', 'smart-rentals-wc' ); ?>',
									customRangeLabel: '<?php _e( 'Custom Range', 'smart-rentals-wc' ); ?>',
									weekLabel: 'W',
									daysOfWeek: [
										'<?php _e( 'Su', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'Mo', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'Tu', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'We', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'Th', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'Fr', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'Sa', 'smart-rentals-wc' ); ?>'
									],
									monthNames: [
										'<?php _e( 'January', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'February', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'March', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'April', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'May', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'June', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'July', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'August', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'September', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'October', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'November', 'smart-rentals-wc' ); ?>',
										'<?php _e( 'December', 'smart-rentals-wc' ); ?>'
									],
									firstDay: 1
								},
								minDate: moment(),
								maxDate: moment().add(2, 'years'),
								ranges: {
									'<?php _e( 'Today', 'smart-rentals-wc' ); ?>': [moment(), moment()],
									'<?php _e( 'Tomorrow', 'smart-rentals-wc' ); ?>': [moment().add(1, 'day'), moment().add(1, 'day')],
									'<?php _e( 'This Weekend', 'smart-rentals-wc' ); ?>': [moment().day(6), moment().day(7)],
									'<?php _e( 'Next Week', 'smart-rentals-wc' ); ?>': [moment().add(1, 'week').startOf('week'), moment().add(1, 'week').endOf('week')],
									'<?php _e( 'Next Month', 'smart-rentals-wc' ); ?>': [moment().add(1, 'month').startOf('month'), moment().add(1, 'month').endOf('month')]
								}
							});
							
							// Handle Apply button
							$(this).on('apply.daterangepicker', function(ev, picker) {
								var startDateTime = picker.startDate.format('YYYY-MM-DD HH:mm');
								var endDateTime = picker.endDate.format('YYYY-MM-DD HH:mm');
								
								// Smart display format
								var displayText;
								if (startDateTime === endDateTime) {
									displayText = startDateTime;
								} else {
									displayText = startDateTime + ' - ' + endDateTime;
								}
								
								$(this).val(displayText);
								$(this).siblings('.hidden-start-date').val(startDateTime);
								$(this).siblings('.hidden-end-date').val(endDateTime);
								
								// Add visual feedback
								$(this).addClass('range-selected');
								setTimeout(function() {
									$(this).removeClass('range-selected');
								}.bind(this), 1000);
							});
							
							// Handle Cancel button
							$(this).on('cancel.daterangepicker', function(ev, picker) {
								// Don't change the value on cancel
							});
						}
					});
				}
				
				// Add new disabled date row
				$('.add-disabled-date').on('click', function() {
					rowCounter++;
					var newRow = '<tr class="disabled-date-row">' +
						'<td>' +
							'<input type="text" name="disabled_date_range_' + rowCounter + '" value="" class="disabled-date-range-picker" placeholder="<?php esc_attr_e( 'Click to select date range', 'smart-rentals-wc' ); ?>" readonly />' +
							'<input type="hidden" name="<?php echo smart_rentals_wc_meta_key( 'disabled_start_dates' ); ?>[]" value="" class="hidden-start-date" />' +
							'<input type="hidden" name="<?php echo smart_rentals_wc_meta_key( 'disabled_end_dates' ); ?>[]" value="" class="hidden-end-date" />' +
						'</td>' +
						'<td>' +
							'<button type="button" class="button remove-disabled-date" title="<?php esc_attr_e( 'Remove', 'smart-rentals-wc' ); ?>">' +
								'<span class="dashicons dashicons-trash"></span>' +
							'</button>' +
						'</td>' +
						'</tr>';
					
					$('.disabled-dates-rows').append(newRow);
					
					// Initialize daterangepicker for new row
					setTimeout(function() {
						initDateRangePickers();
					}, 100);
				});
				
				// Remove disabled date row
				$(document).on('click', '.remove-disabled-date', function() {
					if ($('.disabled-date-row').length > 1) {
						$(this).closest('tr').remove();
					} else {
						// Clear the last row instead of removing it
						var row = $(this).closest('tr');
						row.find('.disabled-date-range-picker').val('');
						row.find('.hidden-start-date, .hidden-end-date').val('');
					}
				});
			});
			</script>
			
			<style>
			.smart-rentals-disabled-dates {
				margin: 20px 0;
				background: #f9f9f9;
				border: 1px solid #e1e1e1;
				border-radius: 6px;
				padding: 20px;
			}
			
			.smart-rentals-disabled-dates h4 {
				margin: 0 0 15px 0;
				font-size: 16px;
				font-weight: 600;
				color: #333;
				display: flex;
				align-items: center;
				gap: 8px;
			}
			
			.smart-rentals-disabled-dates h4::before {
				content: '\f508';
				font-family: dashicons;
				font-size: 18px;
				color: #666;
			}
			
			.disabled-dates-table {
				margin: 0;
				background: #fff;
				border: 1px solid #e1e1e1;
				border-radius: 4px;
				overflow: hidden;
			}
			
			.disabled-dates-table th {
				background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
				padding: 12px 15px;
				text-align: left;
				font-weight: 600;
				color: #495057;
				border-bottom: 2px solid #dee2e6;
			}
			
			.disabled-dates-table td {
				padding: 15px;
				text-align: left;
				border-bottom: 1px solid #f1f1f1;
			}
			
			.disabled-dates-table tbody tr:last-child td {
				border-bottom: none;
			}
			
			.disabled-date-range-picker {
				width: 100%;
				height: 45px;
				padding: 12px 15px;
				border: 2px solid #e1e1e1;
				border-radius: 6px;
				background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
				cursor: pointer;
				font-family: inherit;
				font-size: 14px;
				font-weight: 500;
				color: #495057;
				transition: all 0.3s ease;
				box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
			}
			
			.disabled-date-range-picker:hover {
				border-color: #007cba;
				background: #ffffff;
				box-shadow: 0 4px 8px rgba(0, 124, 186, 0.1);
				transform: translateY(-1px);
			}
			
			.disabled-date-range-picker:focus {
				border-color: #007cba;
				background: #ffffff;
				box-shadow: 0 0 0 3px rgba(0, 124, 186, 0.1), 0 4px 8px rgba(0, 0, 0, 0.1);
				outline: none;
				transform: translateY(-1px);
			}
			
			.disabled-date-range-picker.range-selected {
				border-color: #28a745;
				background: #d4edda;
				animation: rangeSelectedPulse 0.6s ease-out;
			}
			
			@keyframes rangeSelectedPulse {
				0% { transform: scale(1); }
				50% { transform: scale(1.02); box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.2); }
				100% { transform: scale(1); }
			}
			
			.disabled-date-range-picker::placeholder {
				color: #999;
				font-style: italic;
			}
			
			.remove-disabled-date {
				height: 40px;
				padding: 8px 12px;
				line-height: 1;
				background: #f8f9fa;
				border: 1px solid #dee2e6;
				border-radius: 4px;
				transition: all 0.3s ease;
			}
			
			.remove-disabled-date:hover {
				background: #dc3545;
				border-color: #dc3545;
				color: white;
				transform: scale(1.05);
			}
			
			.remove-disabled-date .dashicons {
				font-size: 16px;
				width: 16px;
				height: 16px;
			}
			
			.add-disabled-date {
				margin-top: 15px;
				height: 45px;
				padding: 12px 20px;
				background: linear-gradient(135deg, #007cba 0%, #005a87 100%);
				border: none;
				border-radius: 6px;
				color: white;
				font-weight: 600;
				transition: all 0.3s ease;
				box-shadow: 0 2px 4px rgba(0, 124, 186, 0.2);
			}
			
			.add-disabled-date:hover {
				background: linear-gradient(135deg, #005a87 0%, #007cba 100%);
				transform: translateY(-2px);
				box-shadow: 0 4px 12px rgba(0, 124, 186, 0.3);
				color: white;
			}
			
			.add-disabled-date .dashicons {
				margin-right: 8px;
				font-size: 18px;
			}
			
			.date-range-header {
				width: 75%;
			}
			
			.actions-header {
				width: 25%;
				text-align: center;
			}
			
			/* Professional table styling */
			.disabled-dates-table tfoot td {
				background: #f8f9fa;
				padding: 20px;
				text-align: center;
				border-top: 2px solid #dee2e6;
			}
			
			/* Responsive design */
			@media (max-width: 768px) {
				.smart-rentals-disabled-dates {
					padding: 15px;
				}
				
				.disabled-date-range-picker {
					height: 40px;
					padding: 10px 12px;
					font-size: 13px;
				}
				
				.add-disabled-date {
					height: 40px;
					padding: 10px 16px;
					font-size: 13px;
				}
				
				.disabled-dates-table th,
				.disabled-dates-table td {
					padding: 10px;
				}
			}
			</style>
			<?php
		}

		/**
		 * Add rental fields after checkbox (DEPRECATED - moved to Rental Options tab)
		 */
		public function add_rental_fields() {
			// This method is deprecated - all rental fields are now in the Rental Options tab
			// Keeping for backward compatibility, but it does nothing
			return;
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
					'disabled_weekdays' => 'array',
					'disabled_start_dates' => 'array',
					'disabled_end_dates' => 'array',
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
							case 'array':
								// Handle array fields like disabled_weekdays and disabled_dates
								if ( is_array( $value ) ) {
									$value = array_map( 'sanitize_text_field', $value );
									// Remove empty values
									$value = array_filter( $value, function( $item ) {
										return !empty( trim( $item ) );
									});
									// Reset array keys
									$value = array_values( $value );
								} else {
									$value = [];
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
					} elseif ( 'array' === $type ) {
						// Save empty array for array fields when not set
						smart_rentals_wc_update_post_meta( $post_id, $field, [] );
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
			
			// First, try to get from custom bookings table
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			$custom_bookings = [];
			
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name ) {
				$custom_bookings = $wpdb->get_results("
					SELECT 
						b.*, 
						p.post_title as product_name
					FROM $table_name b
					LEFT JOIN {$wpdb->posts} p ON b.product_id = p.ID
					WHERE b.status IN ('pending', 'confirmed', 'active', 'processing', 'completed')
					ORDER BY b.pickup_date ASC
				");

				foreach ( $custom_bookings as $booking ) {
					$events[] = [
						'id' => 'custom_' . $booking->id,
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
							'source' => 'custom_table'
						]
					];
				}
			}
			
			// Also get bookings from WooCommerce orders (like external plugin)
			$order_status = Smart_Rentals_WC()->options->get_booking_order_status();
			$status_placeholders = implode( "','", array_map( 'esc_sql', $order_status ) );
			
			$order_bookings = $wpdb->get_results( $wpdb->prepare("
				SELECT 
					orders.ID as order_id,
					orders.post_date as order_date,
					items.order_item_name as product_name,
					pickup_date.meta_value as pickup_date,
					dropoff_date.meta_value as dropoff_date,
					quantity.meta_value as quantity,
					product_meta.meta_value as product_id,
					orders.post_status as order_status
				FROM {$wpdb->prefix}woocommerce_order_items AS items
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS pickup_date 
					ON items.order_item_id = pickup_date.order_item_id 
					AND pickup_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS dropoff_date 
					ON items.order_item_id = dropoff_date.order_item_id 
					AND dropoff_date.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS quantity 
					ON items.order_item_id = quantity.order_item_id 
					AND quantity.meta_key = %s
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS product_meta 
					ON items.order_item_id = product_meta.order_item_id 
					AND product_meta.meta_key = '_product_id'
				LEFT JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS rental_check 
					ON items.order_item_id = rental_check.order_item_id 
					AND rental_check.meta_key = %s
				LEFT JOIN {$wpdb->posts} AS orders 
					ON items.order_id = orders.ID
				WHERE 
					rental_check.meta_value = 'yes'
					AND orders.post_status IN ('{$status_placeholders}')
					AND pickup_date.meta_value IS NOT NULL
					AND dropoff_date.meta_value IS NOT NULL
					AND product_meta.meta_value IS NOT NULL
				ORDER BY pickup_date.meta_value ASC
			",
				smart_rentals_wc_meta_key( 'pickup_date' ),
				smart_rentals_wc_meta_key( 'dropoff_date' ),
				smart_rentals_wc_meta_key( 'rental_quantity' ),
				smart_rentals_wc_meta_key( 'is_rental' )
			));

			foreach ( $order_bookings as $booking ) {
				if ( $booking->pickup_date && $booking->dropoff_date && $booking->product_id ) {
					// Convert order status to booking status
					$booking_status = 'pending';
					if ( $booking->order_status === 'wc-processing' ) {
						$booking_status = 'confirmed';
					} elseif ( $booking->order_status === 'wc-completed' ) {
						$booking_status = 'active';
					} elseif ( $booking->order_status === 'wc-on-hold' ) {
						$booking_status = 'pending';
					}
					
					$events[] = [
						'id' => 'order_' . $booking->order_id,
						'title' => $booking->product_name . ' (' . ($booking->quantity ?: 1) . ')',
						'start' => $booking->pickup_date,
						'end' => $booking->dropoff_date,
						'backgroundColor' => $this->get_booking_color( $booking_status ),
						'borderColor' => $this->get_booking_color( $booking_status ),
						'textColor' => '#ffffff',
						'extendedProps' => [
							'booking_id' => $booking->order_id,
							'product_id' => $booking->product_id,
							'quantity' => $booking->quantity ?: 1,
							'status' => $booking_status,
							'order_status' => $booking->order_status,
							'source' => 'woocommerce_order'
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