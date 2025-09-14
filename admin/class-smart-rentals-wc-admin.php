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
			
			// AJAX handlers
			add_action( 'wp_ajax_check_rental_availability', [ $this, 'ajax_check_rental_availability' ] );

			// Add rental columns to product list
			add_filter( 'manage_product_posts_columns', [ $this, 'add_product_columns' ] );
			add_action( 'manage_product_posts_custom_column', [ $this, 'product_column_content' ], 10, 2 );

			// Add rental filter to product list
			add_action( 'restrict_manage_posts', [ $this, 'add_rental_filter' ] );
			add_filter( 'parse_query', [ $this, 'filter_products_by_rental' ] );

			// Product price display - handled by Smart_Rentals_WC_Rental class
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
				__( 'Manage Bookings', 'smart-rentals-wc' ),
				__( 'Manage Bookings', 'smart-rentals-wc' ),
				'manage_woocommerce',
				'smart-rentals-wc-bookings',
				[ $this, 'manage_bookings_page' ]
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
		 * Manage Bookings page
		 */
		public function manage_bookings_page() {
			// Check if we're modifying a rental
			if ( isset( $_GET['action'] ) && $_GET['action'] === 'modify_rental' && isset( $_GET['id'] ) ) {
				$this->modify_rental_page( intval( $_GET['id'] ) );
				return;
			}
			
			// Enqueue WordPress datepicker
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_style( 'wp-admin' );
			
			// Include the booking list class
			require_once SMART_RENTALS_WC_PLUGIN_PATH . 'admin/class-smart-rentals-wc-booking-list.php';
			
			// Create booking list instance
			$booking_list = new Smart_Rentals_WC_Booking_List();
			$booking_list->prepare_items();
			
			// Get rental product IDs for filter
			$rental_products = Smart_Rentals_WC()->options->get_rental_product_ids();
			
			// Get filter values
			$order_id = isset( $_GET['order_id'] ) ? intval( $_GET['order_id'] ) : '';
			$customer_name = isset( $_GET['customer_name'] ) ? sanitize_text_field( $_GET['customer_name'] ) : '';
			$product_id = isset( $_GET['product_id'] ) ? intval( $_GET['product_id'] ) : '';
			$order_status = isset( $_GET['order_status'] ) ? sanitize_text_field( $_GET['order_status'] ) : '';
			$from_date = isset( $_GET['from_date'] ) ? sanitize_text_field( $_GET['from_date'] ) : '';
			$to_date = isset( $_GET['to_date'] ) ? sanitize_text_field( $_GET['to_date'] ) : '';
			$search_by = isset( $_GET['search_by'] ) ? sanitize_text_field( $_GET['search_by'] ) : '';
			
			?>
			<div class="wrap">
				<h1><?php _e( 'Manage Bookings', 'smart-rentals-wc' ); ?></h1>
				
				<!-- Filters -->
				<div class="booking-filters" style="background: #fff; padding: 20px; margin: 20px 0; border: 1px solid #ccd0d4;">
					<form method="GET" action="">
						<input type="hidden" name="page" value="smart-rentals-wc-bookings" />
						
						<div style="display: flex; flex-wrap: wrap; gap: 15px; align-items: end;">
							<!-- Order ID -->
							<div class="filter-field">
								<label for="order_id"><?php _e( 'Order ID', 'smart-rentals-wc' ); ?></label><br>
								<input type="number" name="order_id" id="order_id" value="<?php echo esc_attr( $order_id ); ?>" placeholder="<?php _e( 'Order ID', 'smart-rentals-wc' ); ?>" />
							</div>
							
							<!-- Customer Name -->
							<div class="filter-field">
								<label for="customer_name"><?php _e( 'Customer Name', 'smart-rentals-wc' ); ?></label><br>
								<input type="text" name="customer_name" id="customer_name" value="<?php echo esc_attr( $customer_name ); ?>" placeholder="<?php _e( 'Customer Name', 'smart-rentals-wc' ); ?>" />
							</div>
							
							<!-- Product -->
							<div class="filter-field">
								<label for="product_id"><?php _e( 'Product', 'smart-rentals-wc' ); ?></label><br>
								<select name="product_id" id="product_id">
									<option value=""><?php _e( '-- All Products --', 'smart-rentals-wc' ); ?></option>
									<?php if ( smart_rentals_wc_array_exists( $rental_products ) ) : ?>
										<?php foreach ( $rental_products as $pid ) : ?>
											<option value="<?php echo esc_attr( $pid ); ?>" <?php selected( $product_id, $pid ); ?>>
												<?php echo esc_html( get_the_title( $pid ) ); ?>
											</option>
										<?php endforeach; ?>
									<?php endif; ?>
								</select>
							</div>
							
							<!-- Order Status -->
							<div class="filter-field">
								<label for="order_status"><?php _e( 'Order Status', 'smart-rentals-wc' ); ?></label><br>
								<select name="order_status" id="order_status">
									<option value=""><?php _e( '-- All Statuses --', 'smart-rentals-wc' ); ?></option>
									<option value="wc-pending" <?php selected( $order_status, 'wc-pending' ); ?>><?php _e( 'Pending Payment', 'smart-rentals-wc' ); ?></option>
									<option value="wc-processing" <?php selected( $order_status, 'wc-processing' ); ?>><?php _e( 'Processing', 'smart-rentals-wc' ); ?></option>
									<option value="wc-on-hold" <?php selected( $order_status, 'wc-on-hold' ); ?>><?php _e( 'On Hold', 'smart-rentals-wc' ); ?></option>
									<option value="wc-completed" <?php selected( $order_status, 'wc-completed' ); ?>><?php _e( 'Completed', 'smart-rentals-wc' ); ?></option>
									<option value="wc-cancelled" <?php selected( $order_status, 'wc-cancelled' ); ?>><?php _e( 'Cancelled', 'smart-rentals-wc' ); ?></option>
									<option value="wc-refunded" <?php selected( $order_status, 'wc-refunded' ); ?>><?php _e( 'Refunded', 'smart-rentals-wc' ); ?></option>
									<option value="wc-failed" <?php selected( $order_status, 'wc-failed' ); ?>><?php _e( 'Failed', 'smart-rentals-wc' ); ?></option>
								</select>
							</div>
							
							<!-- Search By Date -->
							<div class="filter-field">
								<label for="search_by"><?php _e( 'Search By', 'smart-rentals-wc' ); ?></label><br>
								<select name="search_by" id="search_by">
									<option value=""><?php _e( '-- Search By --', 'smart-rentals-wc' ); ?></option>
									<option value="pickup_date" <?php selected( $search_by, 'pickup_date' ); ?>><?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?></option>
									<option value="dropoff_date" <?php selected( $search_by, 'dropoff_date' ); ?>><?php _e( 'Dropoff Date', 'smart-rentals-wc' ); ?></option>
								</select>
							</div>
							
							<!-- From Date -->
							<div class="filter-field">
								<label for="from_date"><?php _e( 'From Date', 'smart-rentals-wc' ); ?></label><br>
								<input type="text" name="from_date" id="from_date" value="<?php echo esc_attr( $from_date ); ?>" placeholder="<?php _e( 'Select date', 'smart-rentals-wc' ); ?>" class="datepicker" readonly />
							</div>
							
							<!-- To Date -->
							<div class="filter-field">
								<label for="to_date"><?php _e( 'To Date', 'smart-rentals-wc' ); ?></label><br>
								<input type="text" name="to_date" id="to_date" value="<?php echo esc_attr( $to_date ); ?>" placeholder="<?php _e( 'Select date', 'smart-rentals-wc' ); ?>" class="datepicker" readonly />
							</div>
							
							<!-- Filter Button -->
							<div class="filter-field">
								<button type="submit" class="button button-primary"><?php _e( 'Filter', 'smart-rentals-wc' ); ?></button>
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=smart-rentals-wc-bookings' ) ); ?>" class="button"><?php _e( 'Clear', 'smart-rentals-wc' ); ?></a>
							</div>
						</div>
					</form>
				</div>
				
				<!-- Bookings Table -->
				<form method="post">
					<?php $booking_list->display(); ?>
				</form>
			</div>
			
			<style>
			.filter-field {
				min-width: 150px;
			}
			.filter-field label {
				font-weight: 600;
				margin-bottom: 5px;
				display: block;
			}
			.filter-field input,
			.filter-field select {
				width: 100%;
				max-width: 200px;
			}
			.filter-field input.datepicker {
				cursor: pointer;
				background: #f0f8ff;
				border: 1px solid #0073aa;
				padding: 6px 10px;
				border-radius: 3px;
			}
			.filter-field input.datepicker:focus {
				background: #e6f3ff;
				border-color: #005177;
				outline: none;
				box-shadow: 0 0 0 1px #005177;
			}
			.order-status {
				display: inline-block;
				padding: 4px 8px;
				border-radius: 3px;
				font-size: 11px;
				font-weight: 600;
				text-align: center;
				color: #fff;
				background: #999;
			}
			.order-status.status-pending { background: #ffba00; }
			.order-status.status-processing { background: #c6e1c6; color: #5b841b; }
			.order-status.status-on-hold { background: #f8dda7; color: #94660c; }
			.order-status.status-completed { background: #c8d7e1; color: #2e4453; }
			.order-status.status-cancelled { background: #eba3a3; color: #761919; }
			.order-status.status-refunded { background: #eba3a3; color: #761919; }
			.order-status.status-failed { background: #eba3a3; color: #761919; }
			
			/* WordPress Datepicker Styling */
			.ui-datepicker {
				font-size: 13px;
				width: auto;
				border: 2px solid #0073aa;
				border-radius: 6px;
				box-shadow: 0 4px 12px rgba(0, 115, 170, 0.15);
				background: #ffffff;
			}
			.ui-datepicker-header {
				background: linear-gradient(135deg, #0073aa 0%, #005177 100%);
				color: #fff;
				border: none;
				border-radius: 4px 4px 0 0;
				padding: 10px;
			}
			.ui-datepicker-title {
				color: #fff;
				font-weight: 600;
				text-align: center;
			}
			.ui-datepicker-prev,
			.ui-datepicker-next {
				background: rgba(255, 255, 255, 0.2);
				border: 1px solid rgba(255, 255, 255, 0.3);
				border-radius: 3px;
				color: #fff;
				cursor: pointer;
			}
			.ui-datepicker-prev:hover,
			.ui-datepicker-next:hover {
				background: rgba(255, 255, 255, 0.3);
			}
			.ui-datepicker table {
				background: #fff;
			}
			.ui-datepicker td {
				border: none;
				padding: 1px;
			}
			.ui-datepicker td a {
				padding: 8px;
				text-align: center;
				border-radius: 4px;
				color: #333;
				text-decoration: none;
				display: block;
			}
			.ui-datepicker td a:hover {
				background: #e6f3ff;
				color: #0073aa;
			}
			.ui-datepicker td .ui-state-active {
				background: #0073aa;
				color: #fff;
			}
			.ui-datepicker .ui-datepicker-today a {
				background: #f0f8ff;
				border: 2px solid #0073aa;
				color: #0073aa;
				font-weight: bold;
			}
			.ui-datepicker .ui-datepicker-buttonpane {
				background: #f8f9fa;
				border-top: 1px solid #e1e5e9;
				padding: 8px;
				text-align: center;
			}
			.ui-datepicker .ui-datepicker-buttonpane button {
				margin: 0 5px;
				padding: 6px 12px;
				border-radius: 3px;
				border: 1px solid #0073aa;
				background: #0073aa;
				color: #fff;
				cursor: pointer;
			}
			.ui-datepicker .ui-datepicker-buttonpane button:hover {
				background: #005177;
				border-color: #005177;
			}
			</style>
			
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				// Initialize WordPress datepickers
				$('.datepicker').datepicker({
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					showButtonPanel: true,
					currentText: '<?php _e( 'Today', 'smart-rentals-wc' ); ?>',
					closeText: '<?php _e( 'Close', 'smart-rentals-wc' ); ?>',
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
					monthNamesShort: [
						'<?php _e( 'Jan', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Feb', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Mar', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Apr', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'May', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Jun', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Jul', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Aug', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Sep', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Oct', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Nov', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Dec', 'smart-rentals-wc' ); ?>'
					],
					dayNames: [
						'<?php _e( 'Sunday', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Monday', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Tuesday', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Wednesday', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Thursday', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Friday', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Saturday', 'smart-rentals-wc' ); ?>'
					],
					dayNamesMin: [
						'<?php _e( 'Su', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Mo', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Tu', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'We', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Th', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Fr', 'smart-rentals-wc' ); ?>',
						'<?php _e( 'Sa', 'smart-rentals-wc' ); ?>'
					],
					beforeShow: function(input, inst) {
						// Add clear button functionality
						setTimeout(function() {
							var buttonPane = $(input).datepicker('widget').find('.ui-datepicker-buttonpane');
							var clearBtn = $('<button class="ui-datepicker-clear" type="button"><?php _e( 'Clear', 'smart-rentals-wc' ); ?></button>');
							clearBtn.click(function() {
								$(input).val('');
								$(input).datepicker('hide');
							});
							buttonPane.find('.ui-datepicker-clear').remove();
							buttonPane.prepend(clearBtn);
						}, 1);
					}
				});
			});
			</script>
			<?php
		}

		/**
		 * Modify Rental page
		 */
		public function modify_rental_page( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( !$order ) {
				wp_die( __( 'Order not found', 'smart-rentals-wc' ) );
			}
			
			// Check permissions
			if ( !current_user_can( 'edit_shop_orders' ) ) {
				wp_die( __( 'Insufficient permissions', 'smart-rentals-wc' ) );
			}
			
			// Handle form submission
			if ( isset( $_POST['submit'] ) && wp_verify_nonce( $_POST['modify_rental_nonce'], 'modify_rental_' . $order_id ) ) {
				$this->save_rental_modifications( $order );
			}
			
			// Get rental items
			$rental_items = [];
			foreach ( $order->get_items() as $item_id => $item ) {
				if ( $this->is_rental_item( $item ) ) {
					$rental_items[$item_id] = $item;
				}
			}
			
			// Enqueue scripts and styles
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_enqueue_script( 'jquery-ui-timepicker-addon', 'https://cdn.jsdelivr.net/npm/jquery-ui-timepicker-addon@1.6.3/dist/jquery-ui-timepicker-addon.min.js', ['jquery', 'jquery-ui-datepicker'], '1.6.3', true );
			wp_enqueue_style( 'wp-admin' );
			wp_enqueue_style( 'jquery-ui-css', 'https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css' );
			wp_enqueue_style( 'jquery-ui-timepicker-css', 'https://cdn.jsdelivr.net/npm/jquery-ui-timepicker-addon@1.6.3/dist/jquery-ui-timepicker-addon.min.css', ['jquery-ui-css'] );
			
			?>
			<div class="wrap">
				<h1><?php printf( __( 'Modify Rental - Order #%s', 'smart-rentals-wc' ), $order_id ); ?></h1>
				
				<div class="rental-modification-header" style="background: #f1f1f1; padding: 15px; margin: 20px 0; border-radius: 5px;">
					<p><strong><?php _e( 'Order ID:', 'smart-rentals-wc' ); ?></strong> #<?php echo $order_id; ?></p>
					<p><strong><?php _e( 'Customer:', 'smart-rentals-wc' ); ?></strong> <?php echo $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(); ?></p>
					<p><strong><?php _e( 'Order Status:', 'smart-rentals-wc' ); ?></strong> <?php echo wc_get_order_status_name( $order->get_status() ); ?></p>
					<p><strong><?php _e( 'Order Total:', 'smart-rentals-wc' ); ?></strong> <?php echo $order->get_formatted_order_total(); ?></p>
				</div>
				
				<?php if ( empty( $rental_items ) ) : ?>
					<div class="notice notice-warning">
						<p><?php _e( 'No rental items found in this order.', 'smart-rentals-wc' ); ?></p>
					</div>
				<?php else : ?>
					<form method="POST" action="">
						<?php wp_nonce_field( 'modify_rental_' . $order_id, 'modify_rental_nonce' ); ?>
						
						<div class="rental-items-container">
							<?php foreach ( $rental_items as $item_id => $item ) : ?>
								<?php
								$pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
								$dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
								$rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: 1;
								$security_deposit = $item->get_meta( smart_rentals_wc_meta_key( 'security_deposit' ) ) ?: 0;
								$product_id = $item->get_product_id();
								$product = wc_get_product( $product_id );
								?>
								
								<div class="rental-item-card" style="border: 1px solid #ddd; margin-bottom: 20px; border-radius: 8px; overflow: hidden; background: #fff;">
									<div class="rental-item-header" style="background: #f8f9fa; padding: 15px; border-bottom: 1px solid #ddd;">
										<h3 style="margin: 0; color: #333;">
											<?php echo esc_html( $item->get_name() ); ?>
											<span style="font-size: 12px; color: #666; font-weight: normal;">(Item #<?php echo $item_id; ?>)</span>
										</h3>
									</div>
									
									<div class="rental-item-content" style="padding: 20px;">
										<div class="rental-form-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
											<div class="form-group">
												<label for="pickup_date_<?php echo $item_id; ?>" style="display: block; margin-bottom: 5px; font-weight: bold;">
													<?php _e( 'Pickup Date & Time', 'smart-rentals-wc' ); ?>
												</label>
												<input type="text" 
													   id="pickup_date_<?php echo $item_id; ?>"
													   name="rental_data[<?php echo $item_id; ?>][pickup_date]" 
													   value="<?php echo esc_attr( $pickup_date ? date( 'Y-m-d H:i', strtotime( $pickup_date ) ) : '' ); ?>" 
													   class="datetime-picker"
													   data-product-id="<?php echo $product_id; ?>"
													   data-item-id="<?php echo $item_id; ?>"
													   data-field-type="pickup"
													   placeholder="<?php _e( 'Select pickup date and time', 'smart-rentals-wc' ); ?>"
													   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
												<div id="pickup_availability_<?php echo $item_id; ?>" class="availability-message" style="margin-top: 5px; font-size: 12px;"></div>
											</div>
											
											<div class="form-group">
												<label for="dropoff_date_<?php echo $item_id; ?>" style="display: block; margin-bottom: 5px; font-weight: bold;">
													<?php _e( 'Dropoff Date & Time', 'smart-rentals-wc' ); ?>
												</label>
												<input type="text" 
													   id="dropoff_date_<?php echo $item_id; ?>"
													   name="rental_data[<?php echo $item_id; ?>][dropoff_date]" 
													   value="<?php echo esc_attr( $dropoff_date ? date( 'Y-m-d H:i', strtotime( $dropoff_date ) ) : '' ); ?>" 
													   class="datetime-picker"
													   data-product-id="<?php echo $product_id; ?>"
													   data-item-id="<?php echo $item_id; ?>"
													   data-field-type="dropoff"
													   placeholder="<?php _e( 'Select dropoff date and time', 'smart-rentals-wc' ); ?>"
													   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
												<div id="dropoff_availability_<?php echo $item_id; ?>" class="availability-message" style="margin-top: 5px; font-size: 12px;"></div>
											</div>
											
											<div class="form-group">
												<label for="quantity_<?php echo $item_id; ?>" style="display: block; margin-bottom: 5px; font-weight: bold;">
													<?php _e( 'Quantity', 'smart-rentals-wc' ); ?>
												</label>
												<input type="number" 
													   id="quantity_<?php echo $item_id; ?>"
													   name="rental_data[<?php echo $item_id; ?>][quantity]" 
													   value="<?php echo esc_attr( $rental_quantity ); ?>" 
													   min="1" 
													   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
											</div>
											
											<div class="form-group">
												<label for="security_deposit_<?php echo $item_id; ?>" style="display: block; margin-bottom: 5px; font-weight: bold;">
													<?php _e( 'Security Deposit', 'smart-rentals-wc' ); ?>
												</label>
												<input type="number" 
													   id="security_deposit_<?php echo $item_id; ?>"
													   name="rental_data[<?php echo $item_id; ?>][security_deposit]" 
													   value="<?php echo esc_attr( $security_deposit ); ?>" 
													   min="0" 
													   step="0.01" 
													   style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
											</div>
										</div>
										
										<div class="rental-item-info" style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 4px;">
											<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
												<div>
													<strong><?php _e( 'Current Total:', 'smart-rentals-wc' ); ?></strong><br>
													<span id="current_total_<?php echo $item_id; ?>"><?php echo smart_rentals_wc_price( $item->get_total() ); ?></span>
												</div>
												<div>
													<strong><?php _e( 'Price per Unit:', 'smart-rentals-wc' ); ?></strong><br>
													<span id="unit_price_<?php echo $item_id; ?>"><?php echo smart_rentals_wc_price( $product->get_price() ); ?></span>
												</div>
												<div>
													<strong><?php _e( 'Calculated Total:', 'smart-rentals-wc' ); ?></strong><br>
													<span id="calculated_total_<?php echo $item_id; ?>" style="color: #0073aa; font-weight: bold;">
														<?php echo smart_rentals_wc_price( $product->get_price() * $rental_quantity ); ?>
													</span>
												</div>
												<div>
													<strong><?php _e( 'Product ID:', 'smart-rentals-wc' ); ?></strong><br>
													<?php echo $product_id; ?>
												</div>
												<div>
													<strong><?php _e( 'Item ID:', 'smart-rentals-wc' ); ?></strong><br>
													<?php echo $item_id; ?>
												</div>
											</div>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						
						<div class="rental-form-actions" style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
							<button type="submit" name="submit" class="button button-primary button-large">
								<?php _e( 'Save Rental Modifications', 'smart-rentals-wc' ); ?>
							</button>
							<a href="<?php echo admin_url( 'admin.php?page=smart-rentals-wc-bookings' ); ?>" class="button button-secondary button-large">
								<?php _e( 'Back to Bookings', 'smart-rentals-wc' ); ?>
							</a>
						</div>
					</form>
				<?php endif; ?>
			</div>
			
			<style>
			.rental-item-card {
				box-shadow: 0 2px 4px rgba(0,0,0,0.1);
				transition: box-shadow 0.3s ease;
			}
			.rental-item-card:hover {
				box-shadow: 0 4px 8px rgba(0,0,0,0.15);
			}
			.form-group input:focus {
				outline: none;
				border-color: #0073aa;
				box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.1);
			}
			.rental-form-actions {
				text-align: center;
			}
			.rental-form-actions .button {
				margin: 0 10px;
			}
			.calculated-total {
				color: #0073aa;
				font-weight: bold;
				background: #e7f3ff;
				padding: 2px 6px;
				border-radius: 3px;
			}
			.availability-message {
				padding: 5px 8px;
				border-radius: 4px;
				font-weight: 500;
				margin-top: 5px;
			}
			.availability-success {
				background: #d4edda;
				color: #155724;
				border: 1px solid #c3e6cb;
			}
			.availability-warning {
				background: #fff3cd;
				color: #856404;
				border: 1px solid #ffeaa7;
			}
			.availability-error {
				background: #f8d7da;
				color: #721c24;
				border: 1px solid #f5c6cb;
			}
			.datetime-picker {
				background: #fff;
				cursor: pointer;
			}
			.datetime-picker:focus {
				border-color: #0073aa;
				box-shadow: 0 0 0 2px rgba(0, 115, 170, 0.1);
			}
			</style>
			
			<script>
			jQuery(document).ready(function($) {
				// Initialize date-time pickers
				$('.datetime-picker').datetimepicker({
					dateFormat: 'yy-mm-dd',
					timeFormat: 'HH:mm',
					showButtonPanel: true,
					changeMonth: true,
					changeYear: true,
					yearRange: 'c-1:c+2',
					onSelect: function(dateText, inst) {
						checkAvailability($(this));
					}
				});
				
				// Update calculated total when quantity changes
				$('input[name*="[quantity]"]').on('input change', function() {
					var itemId = $(this).attr('name').match(/\[(\d+)\]/)[1];
					var quantity = parseInt($(this).val()) || 1;
					var unitPrice = parseFloat($('#unit_price_' + itemId).text().replace(/[^\d.-]/g, '')) || 0;
					var calculatedTotal = unitPrice * quantity;
					
					// Format the price (simple formatting)
					var formattedPrice = '$' + calculatedTotal.toFixed(2);
					
					$('#calculated_total_' + itemId).text(formattedPrice).addClass('calculated-total');
					
					// Highlight if quantity changed
					if (quantity > 1) {
						$('#calculated_total_' + itemId).css('background', '#fff3cd').css('color', '#856404');
					} else {
						$('#calculated_total_' + itemId).css('background', '#e7f3ff').css('color', '#0073aa');
					}
					
					// Check availability when quantity changes
					checkAvailability($(this));
				});
				
				// Check availability function
				function checkAvailability(element) {
					var itemId = element.attr('name').match(/\[(\d+)\]/)[1];
					var productId = element.data('product-id') || $('input[name*="[pickup_date]"]').filter('[data-item-id="' + itemId + '"]').data('product-id');
					var pickupDate = $('#pickup_date_' + itemId).val();
					var dropoffDate = $('#dropoff_date_' + itemId).val();
					var quantity = parseInt($('#quantity_' + itemId).val()) || 1;
					var fieldType = element.data('field-type') || 'pickup';
					var orderId = <?php echo $order_id; ?>;
					
					// Only check if both dates are selected
					if (!pickupDate || !dropoffDate) {
						$('#pickup_availability_' + itemId).html('').removeClass('availability-success availability-warning availability-error');
						$('#dropoff_availability_' + itemId).html('').removeClass('availability-success availability-warning availability-error');
						return;
					}
					
					// Show loading
					$('#pickup_availability_' + itemId).html('<span style="color: #666;">Checking availability...</span>').removeClass('availability-success availability-warning availability-error');
					$('#dropoff_availability_' + itemId).html('<span style="color: #666;">Checking availability...</span>').removeClass('availability-success availability-warning availability-error');
					
					// AJAX call to check availability
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						data: {
							action: 'check_rental_availability',
							nonce: '<?php echo wp_create_nonce( 'check_rental_availability' ); ?>',
							product_id: productId,
							pickup_date: pickupDate,
							dropoff_date: dropoffDate,
							quantity: quantity,
							exclude_order_id: orderId
						},
						success: function(response) {
							if (response.success) {
								var data = response.data;
								var messageClass = 'availability-' + data.type;
								var icon = data.type === 'success' ? '✓' : (data.type === 'warning' ? '⚠' : '✗');
								
								$('#pickup_availability_' + itemId).html(icon + ' ' + data.message).removeClass('availability-success availability-warning availability-error').addClass(messageClass);
								$('#dropoff_availability_' + itemId).html(icon + ' ' + data.message).removeClass('availability-success availability-warning availability-error').addClass(messageClass);
							} else {
								$('#pickup_availability_' + itemId).html('✗ Error checking availability').removeClass('availability-success availability-warning availability-error').addClass('availability-error');
								$('#dropoff_availability_' + itemId).html('✗ Error checking availability').removeClass('availability-success availability-warning availability-error').addClass('availability-error');
							}
						},
						error: function() {
							$('#pickup_availability_' + itemId).html('✗ Error checking availability').removeClass('availability-success availability-warning availability-error').addClass('availability-error');
							$('#dropoff_availability_' + itemId).html('✗ Error checking availability').removeClass('availability-success availability-warning availability-error').addClass('availability-error');
						}
					});
				}
				
				// Check availability when date fields change
				$('.datetime-picker').on('change', function() {
					checkAvailability($(this));
				});
				
				// Initialize calculated totals on page load
				$('input[name*="[quantity]"]').each(function() {
					$(this).trigger('change');
				});
			});
			</script>
			<?php
		}

		/**
		 * Check if item is a rental item
		 */
		private function is_rental_item( $item ) {
			$is_rental = $item->get_meta( smart_rentals_wc_meta_key( 'is_rental' ) );
			$pickup_date = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
			$dropoff_date = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
			$rental_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) );
			
			return ( $is_rental === 'yes' || ( $pickup_date && $dropoff_date ) || $rental_quantity );
		}

		/**
		 * Save rental modifications
		 */
		private function save_rental_modifications( $order ) {
			if ( !isset( $_POST['rental_data'] ) || !is_array( $_POST['rental_data'] ) ) {
				return;
			}
			
			$modified_items = [];
			$price_changes = [];
			
			foreach ( $_POST['rental_data'] as $item_id => $rental_data ) {
				$item = $order->get_item( $item_id );
				if ( !$item ) {
					continue;
				}
				
				$original_pickup = $item->get_meta( smart_rentals_wc_meta_key( 'pickup_date' ) );
				$original_dropoff = $item->get_meta( smart_rentals_wc_meta_key( 'dropoff_date' ) );
				$original_quantity = $item->get_meta( smart_rentals_wc_meta_key( 'rental_quantity' ) ) ?: 1;
				$original_deposit = $item->get_meta( smart_rentals_wc_meta_key( 'security_deposit' ) ) ?: 0;
				
				$pickup_date = sanitize_text_field( $rental_data['pickup_date'] ?? '' );
				$dropoff_date = sanitize_text_field( $rental_data['dropoff_date'] ?? '' );
				$quantity = intval( $rental_data['quantity'] ?? 1 );
				$security_deposit = floatval( $rental_data['security_deposit'] ?? 0 );
				
				// Check if any changes were made
				$has_changes = false;
				if ( $pickup_date && $pickup_date !== $original_pickup ) {
					$has_changes = true;
				}
				if ( $dropoff_date && $dropoff_date !== $original_dropoff ) {
					$has_changes = true;
				}
				if ( $quantity !== $original_quantity ) {
					$has_changes = true;
				}
				if ( $security_deposit != $original_deposit ) {
					$has_changes = true;
				}
				
				if ( !$has_changes ) {
					continue;
				}
				
				// Store original price for comparison
				$original_total = $item->get_total();
				$original_subtotal = $item->get_subtotal();
				
				// Update rental data
				if ( $pickup_date ) {
					$item->update_meta_data( smart_rentals_wc_meta_key( 'pickup_date' ), date( 'Y-m-d H:i:s', strtotime( $pickup_date ) ) );
				}
				if ( $dropoff_date ) {
					$item->update_meta_data( smart_rentals_wc_meta_key( 'dropoff_date' ), date( 'Y-m-d H:i:s', strtotime( $dropoff_date ) ) );
				}
				
				// Update quantity and recalculate prices
				$item->set_quantity( $quantity );
				$item->update_meta_data( smart_rentals_wc_meta_key( 'rental_quantity' ), $quantity );
				$item->update_meta_data( smart_rentals_wc_meta_key( 'security_deposit' ), $security_deposit );
				$item->update_meta_data( smart_rentals_wc_meta_key( 'is_rental' ), 'yes' );
				
				// Recalculate item prices based on new quantity
				$product = $item->get_product();
				if ( $product ) {
					// Get the base price per unit
					$base_price = $product->get_price();
					
					// Calculate new subtotal based on quantity
					$new_subtotal = $base_price * $quantity;
					
					// Set the new subtotal and total
					$item->set_subtotal( $new_subtotal );
					$item->set_total( $new_subtotal );
					
					// Store price change info
					$price_changes[] = [
						'item_name' => $item->get_name(),
						'quantity' => $quantity,
						'old_total' => $original_total,
						'new_total' => $new_subtotal,
						'price_per_unit' => $base_price
					];
				}
				
				$item->save();
				$modified_items[] = $item->get_name();
				
				// Update the custom bookings table for frontend synchronization
				$this->update_custom_booking_table( $order->get_id(), $item_id, strtotime( $pickup_date ), strtotime( $dropoff_date ), $quantity );
			}
			
			if ( !empty( $modified_items ) ) {
				// Recalculate order totals (this will handle tax calculation)
				$order->calculate_totals();
				$order->save();
				
				// Add detailed order note
				$note_parts = [ __( 'Rental details modified via admin:', 'smart-rentals-wc' ) ];
				$note_parts[] = implode( ', ', $modified_items );
				
				if ( !empty( $price_changes ) ) {
					$note_parts[] = "\n" . __( 'Price changes:', 'smart-rentals-wc' );
					foreach ( $price_changes as $change ) {
						$note_parts[] = sprintf( 
							'- %s: Qty %d × %s = %s (was %s)',
							$change['item_name'],
							$change['quantity'],
							smart_rentals_wc_price( $change['price_per_unit'] ),
							smart_rentals_wc_price( $change['new_total'] ),
							smart_rentals_wc_price( $change['old_total'] )
						);
					}
				}
				
				$order->add_order_note( implode( "\n", $note_parts ) );
				
				// Show success message with price change info
				$message = __( 'Rental modifications saved successfully!', 'smart-rentals-wc' );
				if ( !empty( $price_changes ) ) {
					$message .= ' ' . __( 'Order totals have been recalculated.', 'smart-rentals-wc' );
				}
				
				add_action( 'admin_notices', function() use ( $message ) {
					echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( $message ) . '</p></div>';
				});
			}
		}

		/**
		 * AJAX handler for checking rental availability
		 */
		public function ajax_check_rental_availability() {
			// Verify nonce
			if ( !wp_verify_nonce( $_POST['nonce'], 'check_rental_availability' ) ) {
				wp_send_json_error( [ 'message' => __( 'Security check failed', 'smart-rentals-wc' ) ] );
			}
			
			$product_id = intval( $_POST['product_id'] );
			$pickup_date = sanitize_text_field( $_POST['pickup_date'] );
			$dropoff_date = sanitize_text_field( $_POST['dropoff_date'] );
			$quantity = intval( $_POST['quantity'] );
			$exclude_order_id = intval( $_POST['exclude_order_id'] ?? 0 );
			
			if ( !$product_id || !$pickup_date || !$dropoff_date ) {
				wp_send_json_error( [ 'message' => __( 'Missing required data', 'smart-rentals-wc' ) ] );
			}
			
			// Debug logging
			error_log( "Smart Rentals Availability Check - Product ID: $product_id, Pickup: $pickup_date, Dropoff: $dropoff_date, Quantity: $quantity, Exclude Order: $exclude_order_id" );
			
			// Check availability using the booking system
			$availability = $this->check_product_availability( $product_id, $pickup_date, $dropoff_date, $quantity, $exclude_order_id );
			
			// Debug logging
			error_log( "Smart Rentals Availability Result: " . print_r( $availability, true ) );
			
			wp_send_json_success( $availability );
		}
		
		/**
		 * Check product availability for given dates using proper rental logic with order exclusion
		 */
		private function check_product_availability( $product_id, $pickup_date, $dropoff_date, $quantity, $exclude_order_id = 0 ) {
			// Convert dates to timestamps
			$pickup_timestamp = strtotime( $pickup_date );
			$dropoff_timestamp = strtotime( $dropoff_date );
			
			if ( !$pickup_timestamp || !$dropoff_timestamp || $pickup_timestamp >= $dropoff_timestamp ) {
				return [
					'available' => false,
					'message' => __( 'Invalid date range', 'smart-rentals-wc' ),
					'type' => 'error',
					'available_quantity' => 0,
					'booked_quantity' => 0,
					'total_stock' => 0
				];
			}
			
			// Use the proper availability checking with order exclusion
			$is_available = $this->check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity, $exclude_order_id );
			$available_quantity = $this->get_available_quantity_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $exclude_order_id );
			
			// Get rental stock for display
			$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
			$booked_quantity = $rental_stock - $available_quantity;
			
			// Debug logging
			error_log( "Smart Rentals Rental Availability (Excluding Order $exclude_order_id) - Product: $product_id, Rental Stock: $rental_stock, Available: $available_quantity, Booked: $booked_quantity, Required: $quantity" );
			
			if ( $is_available ) {
				error_log( "Smart Rentals: Rental availability check passed (excluding order $exclude_order_id)" );
				return [
					'available' => true,
					'message' => sprintf( 
						__( 'Available: %d units free on selected dates', 'smart-rentals-wc' ), 
						$available_quantity 
					),
					'type' => 'success',
					'available_quantity' => $available_quantity,
					'booked_quantity' => $booked_quantity,
					'total_stock' => $rental_stock
				];
			} else {
				// Check if it's a stock issue or booking conflict
				if ( $rental_stock < 1 ) {
					error_log( "Smart Rentals: No rental stock available" );
					return [
						'available' => false,
						'message' => __( 'Product has no rental stock available', 'smart-rentals-wc' ),
						'type' => 'error',
						'available_quantity' => 0,
						'booked_quantity' => 0,
						'total_stock' => 0
					];
				} else {
					error_log( "Smart Rentals: Rental availability check failed (excluding order $exclude_order_id) - Available: $available_quantity < Required: $quantity" );
					return [
						'available' => false,
						'message' => sprintf( 
							__( 'WARNING: Only %d units available on selected dates (%d already booked)', 'smart-rentals-wc' ), 
							$available_quantity,
							$booked_quantity
						),
						'type' => 'warning',
						'available_quantity' => $available_quantity,
						'booked_quantity' => $booked_quantity,
						'total_stock' => $rental_stock
					];
				}
			}
		}
		
		/**
		 * Check availability excluding specific order (for admin modifications)
		 */
		private function check_availability_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $quantity, $exclude_order_id ) {
			global $wpdb;
			
			$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
			if ( !$rental_stock || $rental_stock < 1 ) {
				return false;
			}
			
			// Check disabled weekdays
			$disabled_weekdays = smart_rentals_wc_get_post_meta( $product_id, 'disabled_weekdays' );
			if ( is_array( $disabled_weekdays ) && !empty( $disabled_weekdays ) ) {
				$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
				
				// For daily rentals, exclude return date from validation
				$validation_end_timestamp = $dropoff_timestamp;
				if ( $rental_type === 'day' ) {
					$validation_end_timestamp = $dropoff_timestamp - 86400; // Exclude return day
				}
				
				// Check if pickup date falls on disabled weekday
				$pickup_weekday = date( 'w', $pickup_timestamp );
				if ( in_array( intval( $pickup_weekday ), array_map( 'intval', $disabled_weekdays ) ) ) {
					return false;
				}
				
				// For hourly/mixed rentals, also check dropoff date
				if ( $rental_type !== 'day' ) {
					$dropoff_weekday = date( 'w', $dropoff_timestamp );
					if ( in_array( intval( $dropoff_weekday ), array_map( 'intval', $disabled_weekdays ) ) ) {
						return false;
					}
				}
				
				// Check usage period only (excluding return date for daily rentals)
				if ( $pickup_timestamp !== $validation_end_timestamp ) {
					$current_date = $pickup_timestamp;
					while ( $current_date <= $validation_end_timestamp ) {
						$current_weekday = date( 'w', $current_date );
						if ( in_array( intval( $current_weekday ), array_map( 'intval', $disabled_weekdays ) ) ) {
							return false;
						}
						$current_date += 86400; // Add one day
					}
				}
			}
			
			// Get booked dates excluding the specified order
			$booked_dates = $this->get_booked_dates_excluding_order( $product_id, $exclude_order_id );
			
			$booked_quantity = 0;
			$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
			
			// For daily rentals (hotel logic), exclude return date from overlap checking
			$validation_end_timestamp = $dropoff_timestamp;
			if ( $rental_type === 'day' ) {
				$validation_end_timestamp = $dropoff_timestamp - 86400; // Exclude return day
			}
			
			foreach ( $booked_dates as $booking ) {
				$booking_pickup = strtotime( $booking->pickup_date );
				$booking_dropoff = strtotime( $booking->dropoff_date );
				
				// For daily rentals, exclude return dates from both bookings
				$booking_validation_end = $booking_dropoff;
				if ( $rental_type === 'day' ) {
					$booking_validation_end = $booking_dropoff - 86400; // Exclude return day from existing booking
				}
				
				// Enhanced overlap logic: Only check usage periods, not return dates
				if ( $pickup_timestamp <= $booking_validation_end && $validation_end_timestamp >= $booking_pickup ) {
					$booked_quantity += intval( $booking->quantity );
				}
			}
			
			$available_quantity = $rental_stock - $booked_quantity;
			return $available_quantity >= $quantity;
		}
		
		/**
		 * Get available quantity excluding specific order
		 */
		private function get_available_quantity_excluding_order( $product_id, $pickup_timestamp, $dropoff_timestamp, $exclude_order_id ) {
			$rental_stock = smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' );
			if ( !$rental_stock || $rental_stock < 1 ) {
				return 0;
			}
			
			$booked_dates = $this->get_booked_dates_excluding_order( $product_id, $exclude_order_id );
			$booked_quantity = 0;
			
			foreach ( $booked_dates as $booking ) {
				$booking_pickup = strtotime( $booking->pickup_date );
				$booking_dropoff = strtotime( $booking->dropoff_date );
				
				// Check if dates overlap
				if ( $pickup_timestamp < $booking_dropoff && $dropoff_timestamp > $booking_pickup ) {
					$booked_quantity += intval( $booking->quantity );
				}
			}
			
			return max( 0, $rental_stock - $booked_quantity );
		}
		
		/**
		 * Get booked dates excluding specific order
		 */
		private function get_booked_dates_excluding_order( $product_id, $exclude_order_id ) {
			global $wpdb;
			
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return [];
			}
			
			return $wpdb->get_results( $wpdb->prepare( "
				SELECT pickup_date, dropoff_date, quantity
				FROM $table_name
				WHERE product_id = %d
				AND order_id != %d
				AND status IN ('pending', 'confirmed', 'active', 'processing', 'completed')
			", $product_id, $exclude_order_id ) );
		}

		/**
		 * Update custom booking table for frontend synchronization
		 */
		private function update_custom_booking_table( $order_id, $item_id, $pickup_timestamp, $dropoff_timestamp, $quantity ) {
			global $wpdb;
			
			$table_name = $wpdb->prefix . 'smart_rentals_bookings';
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return;
			}
			
			// Check if booking record exists
			$existing_booking = $wpdb->get_row( $wpdb->prepare( "
				SELECT id FROM $table_name 
				WHERE order_id = %d AND product_id = (
					SELECT meta_value FROM {$wpdb->postmeta} 
					WHERE post_id = %d AND meta_key = '_product_id'
				)
			", $order_id, $item_id ) );
			
			if ( $existing_booking ) {
				// Update existing booking record
				$wpdb->update(
					$table_name,
					[
						'pickup_date' => date( 'Y-m-d H:i:s', $pickup_timestamp ),
						'dropoff_date' => date( 'Y-m-d H:i:s', $dropoff_timestamp ),
						'quantity' => $quantity,
						'updated_at' => current_time( 'mysql' )
					],
					[ 'id' => $existing_booking->id ],
					[ '%s', '%s', '%d', '%s' ],
					[ '%d' ]
				);
				
				error_log( "Smart Rentals: Updated booking record ID {$existing_booking->id} for order $order_id" );
			} else {
				// Get product ID from order item
				$order = wc_get_order( $order_id );
				$item = $order->get_item( $item_id );
				$product_id = $item ? $item->get_product_id() : 0;
				
				if ( $product_id ) {
					// Create new booking record
					$wpdb->insert(
						$table_name,
						[
							'order_id' => $order_id,
							'product_id' => $product_id,
							'pickup_date' => date( 'Y-m-d H:i:s', $pickup_timestamp ),
							'dropoff_date' => date( 'Y-m-d H:i:s', $dropoff_timestamp ),
							'quantity' => $quantity,
							'status' => 'confirmed',
							'created_at' => current_time( 'mysql' ),
							'updated_at' => current_time( 'mysql' )
						],
						[ '%d', '%d', '%s', '%s', '%d', '%s', '%s', '%s' ]
					);
					
					error_log( "Smart Rentals: Created new booking record for order $order_id, product $product_id" );
				}
			}
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
						<tr>
							<th scope="row">
								<label for="global_security_deposit"><?php _e( 'Global Security Deposit', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<input type="number" name="global_security_deposit" id="global_security_deposit" value="<?php echo esc_attr( smart_rentals_wc_get_meta_data( 'global_security_deposit', $settings, '' ) ); ?>" min="0" step="0.01" class="regular-text" />
								<p class="description"><?php _e( 'Default security deposit amount for all rental products. Leave empty for no global deposit. Individual products can override this amount.', 'smart-rentals-wc' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="rental_order_status"><?php _e( 'Set order status after booking rental product', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<select name="rental_order_status" id="rental_order_status">
									<option value=""><?php _e( '-- Use WooCommerce Default --', 'smart-rentals-wc' ); ?></option>
									<?php
									// Only show specific order statuses for rentals
									$allowed_statuses = array(
										'wc-processing' => __( 'Processing', 'smart-rentals-wc' ),
										'wc-completed' => __( 'Completed', 'smart-rentals-wc' ),
										'wc-on-hold' => __( 'On Hold', 'smart-rentals-wc' ),
										'wc-pending' => __( 'Pending Payment', 'smart-rentals-wc' ),
									);
									
									$selected_status = smart_rentals_wc_get_meta_data( 'rental_order_status', $settings, '' );
									
									foreach ( $allowed_statuses as $status_key => $status_name ) : ?>
										<option value="<?php echo esc_attr( $status_key ); ?>" <?php selected( $selected_status, $status_key ); ?>>
											<?php echo esc_html( $status_name ); ?>
										</option>
									<?php endforeach; ?>
								</select>
								<p class="description"><?php _e( 'Set the default order status for rental product bookings. If empty, WooCommerce default order status will be used. This applies to all rental types.', 'smart-rentals-wc' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="default_turnaround_time"><?php _e( 'Default Turnaround Time (Hours)', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<input type="number" name="default_turnaround_time" id="default_turnaround_time" value="<?php echo esc_attr( smart_rentals_wc_get_meta_data( 'default_turnaround_time', $settings, '2' ) ); ?>" min="0" step="0.5" class="regular-text" />
								<p class="description"><?php _e( 'Time needed to prepare rental items after return (cleaning, maintenance, inspection). Items become available again after this time. Individual products can override this setting.', 'smart-rentals-wc' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="notify_customer_on_modification"><?php _e( 'Customer Notifications', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<input type="checkbox" 
									   id="notify_customer_on_modification" 
									   name="notify_customer_on_modification" 
									   value="yes" 
									   <?php checked( smart_rentals_wc_get_meta_data( 'notify_customer_on_modification', $settings, 'yes' ), 'yes' ); ?> />
								<label for="notify_customer_on_modification"><?php _e( 'Send email notifications to customers when rental details are modified', 'smart-rentals-wc' ); ?></label>
								<p class="description"><?php _e( 'When enabled, customers will receive email notifications when admin modifies pickup/dropoff dates, quantities, or other rental details.', 'smart-rentals-wc' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="enable_order_modification_log"><?php _e( 'Order Modification Log', 'smart-rentals-wc' ); ?></label>
							</th>
							<td>
								<input type="checkbox" 
									   id="enable_order_modification_log" 
									   name="enable_order_modification_log" 
									   value="yes" 
									   <?php checked( smart_rentals_wc_get_meta_data( 'enable_order_modification_log', $settings, 'yes' ), 'yes' ); ?> />
								<label for="enable_order_modification_log"><?php _e( 'Enable detailed logging of order modifications', 'smart-rentals-wc' ); ?></label>
								<p class="description"><?php _e( 'When enabled, all order modifications will be logged with timestamps, user information, and change details for audit purposes.', 'smart-rentals-wc' ); ?></p>
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
				'global_security_deposit' => floatval( $_POST['global_security_deposit'] ?? 0 ),
				'rental_order_status' => sanitize_text_field( $_POST['rental_order_status'] ?? '' ),
				'default_turnaround_time' => floatval( $_POST['default_turnaround_time'] ?? 2 ),
				'notify_customer_on_modification' => isset( $_POST['notify_customer_on_modification'] ) ? 'yes' : 'no',
				'enable_order_modification_log' => isset( $_POST['enable_order_modification_log'] ) ? 'yes' : 'no',
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
			$global_deposit = smart_rentals_wc_get_meta_data( 'global_security_deposit', smart_rentals_wc_get_option( 'settings', [] ), 0 );
			$deposit_description = __( 'Security deposit amount for this product.', 'smart-rentals-wc' );
			if ( $global_deposit > 0 ) {
				$deposit_description .= ' ' . sprintf( __( 'Leave empty to use global default (%s).', 'smart-rentals-wc' ), smart_rentals_wc_price( $global_deposit ) );
			} else {
				$deposit_description .= ' ' . __( 'Set a global default in Smart Rentals Settings.', 'smart-rentals-wc' );
			}
			
			woocommerce_wp_text_input([
				'id' => smart_rentals_wc_meta_key( 'security_deposit' ),
				'label' => __( 'Security Deposit', 'smart-rentals-wc' ) . ' (' . ( function_exists( 'get_woocommerce_currency_symbol' ) ? get_woocommerce_currency_symbol() : '$' ) . ')',
				'placeholder' => $global_deposit > 0 ? number_format( $global_deposit, 2, '.', '' ) : '0.00',
				'type' => 'number',
				'custom_attributes' => [
					'step' => '0.01',
					'min' => '0',
				],
				'desc_tip' => true,
				'description' => $deposit_description,
			]);

			echo '</div>';
			echo '<div class="options_group">';

			// Turnaround Time
			$global_turnaround = smart_rentals_wc_get_meta_data( 'default_turnaround_time', smart_rentals_wc_get_option( 'settings', [] ), 2 );
			$turnaround_description = __( 'Time needed to prepare this item after return (cleaning, maintenance, inspection).', 'smart-rentals-wc' );
			if ( $global_turnaround > 0 ) {
				$turnaround_description .= ' ' . sprintf( __( 'Leave empty to use global default (%s hours).', 'smart-rentals-wc' ), $global_turnaround );
			} else {
				$turnaround_description .= ' ' . __( 'Set a global default in Smart Rentals Settings.', 'smart-rentals-wc' );
			}
			
			woocommerce_wp_text_input([
				'id' => smart_rentals_wc_meta_key( 'turnaround_time' ),
				'label' => __( 'Turnaround Time (Hours)', 'smart-rentals-wc' ),
				'placeholder' => $global_turnaround > 0 ? number_format( $global_turnaround, 1, '.', '' ) : '2.0',
				'type' => 'number',
				'custom_attributes' => [
					'step' => '0.5',
					'min' => '0',
				],
				'desc_tip' => true,
				'description' => $turnaround_description,
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
					<option value="0" <?php selected( in_array( '0', $disabled_weekdays ) || in_array( 0, $disabled_weekdays ), true ); ?>><?php _e( 'Sunday', 'smart-rentals-wc' ); ?></option>
					<option value="1" <?php selected( in_array( '1', $disabled_weekdays ) || in_array( 1, $disabled_weekdays ), true ); ?>><?php _e( 'Monday', 'smart-rentals-wc' ); ?></option>
					<option value="2" <?php selected( in_array( '2', $disabled_weekdays ) || in_array( 2, $disabled_weekdays ), true ); ?>><?php _e( 'Tuesday', 'smart-rentals-wc' ); ?></option>
					<option value="3" <?php selected( in_array( '3', $disabled_weekdays ) || in_array( 3, $disabled_weekdays ), true ); ?>><?php _e( 'Wednesday', 'smart-rentals-wc' ); ?></option>
					<option value="4" <?php selected( in_array( '4', $disabled_weekdays ) || in_array( 4, $disabled_weekdays ), true ); ?>><?php _e( 'Thursday', 'smart-rentals-wc' ); ?></option>
					<option value="5" <?php selected( in_array( '5', $disabled_weekdays ) || in_array( 5, $disabled_weekdays ), true ); ?>><?php _e( 'Friday', 'smart-rentals-wc' ); ?></option>
					<option value="6" <?php selected( in_array( '6', $disabled_weekdays ) || in_array( 6, $disabled_weekdays ), true ); ?>><?php _e( 'Saturday', 'smart-rentals-wc' ); ?></option>
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
					'turnaround_time' => 'number',
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
					
					// Debug logging for array fields
					if ( $type === 'array' && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
						smart_rentals_wc_log( "Processing array field '$field' with meta_key '$meta_key'" );
						smart_rentals_wc_log( "POST data: " . print_r( $_POST[$meta_key] ?? 'NOT SET', true ) );
					}
					
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
									// Remove empty values but keep '0' (Sunday)
									$value = array_filter( $value, function( $item ) {
										return $item !== '' && $item !== null;
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
						
						// Debug logging for array fields
						if ( $type === 'array' && defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							smart_rentals_wc_log( "Saved array field '$field' with value: " . print_r( $value, true ) );
						}
					} elseif ( 'checkbox' === $type ) {
						smart_rentals_wc_update_post_meta( $post_id, $field, 'no' );
					} elseif ( 'array' === $type ) {
						// Save empty array for array fields when not set
						smart_rentals_wc_update_post_meta( $post_id, $field, [] );
						
						if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
							smart_rentals_wc_log( "Saved empty array for field '$field'" );
						}
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
						'backgroundColor' => smart_rentals_wc_get_booking_color( $booking->status ),
						'borderColor' => smart_rentals_wc_get_booking_color( $booking->status ),
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
						'backgroundColor' => smart_rentals_wc_get_booking_color( $booking_status ),
						'borderColor' => smart_rentals_wc_get_booking_color( $booking_status ),
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
		 * Get order modification history
		 */
		public function get_order_modification_history( $order_id ) {
			global $wpdb;
			
			$table_name = $wpdb->prefix . 'smart_rentals_order_modifications';
			if ( $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) != $table_name ) {
				return [];
			}
			
			$modifications = $wpdb->get_results( $wpdb->prepare(
				"SELECT * FROM $table_name WHERE order_id = %d ORDER BY modified_at DESC",
				$order_id
			) );
			
			foreach ( $modifications as &$modification ) {
				$modification->changes = json_decode( $modification->changes, true );
				$modification->modified_by_user = get_userdata( $modification->modified_by );
			}
			
			return $modifications;
		}
		
		/**
		 * Display order modification history in order edit screen
		 */
		public function display_order_modification_history( $order ) {
			$modifications = $this->get_order_modification_history( $order->get_id() );
			
			if ( empty( $modifications ) ) {
				return;
			}
			
			?>
			<div class="smart-rentals-modification-history" style="margin-top: 20px; padding: 15px; background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
				<h4 style="margin: 0 0 15px 0; color: #333;">
					<span class="dashicons dashicons-history" style="vertical-align: middle; margin-right: 5px;"></span>
					<?php _e( 'Order Modification History', 'smart-rentals-wc' ); ?>
				</h4>
				
				<div class="modification-list" style="max-height: 300px; overflow-y: auto;">
					<?php foreach ( $modifications as $modification ) : ?>
						<div class="modification-item" style="margin-bottom: 15px; padding: 10px; background: #fff; border: 1px solid #ddd; border-radius: 4px;">
							<div class="modification-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
								<span style="font-weight: bold; color: #333;">
									<?php printf( __( 'Modified by %s', 'smart-rentals-wc' ), 
										$modification->modified_by_user ? $modification->modified_by_user->display_name : __( 'Unknown User', 'smart-rentals-wc' )
									); ?>
								</span>
								<span style="font-size: 12px; color: #666;">
									<?php echo date( 'M j, Y g:i A', strtotime( $modification->modified_at ) ); ?>
								</span>
							</div>
							
							<?php if ( !empty( $modification->changes ) ) : ?>
								<div class="modification-changes">
									<?php foreach ( $modification->changes as $item ) : ?>
										<div style="margin-bottom: 8px;">
											<strong><?php echo esc_html( $item['item_name'] ); ?></strong>
											<ul style="margin: 5px 0 0 20px; padding: 0;">
												<?php foreach ( $item['changes'] as $change ) : ?>
													<li style="margin-bottom: 3px;"><?php echo esc_html( $change ); ?></li>
												<?php endforeach; ?>
											</ul>
										</div>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
							
							<?php if ( $modification->total_change != 0 ) : ?>
								<div style="margin-top: 10px; padding: 8px; background: #e9ecef; border-radius: 3px; font-weight: bold;">
									<?php printf( __( 'Total Change: %s', 'smart-rentals-wc' ), 
										$modification->total_change >= 0 ? '+' . smart_rentals_wc_price( $modification->total_change ) : smart_rentals_wc_price( $modification->total_change )
									); ?>
								</div>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>
				</div>
			</div>
			<?php
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
}