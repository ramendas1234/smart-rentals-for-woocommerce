<?php
/**
 * Smart Rentals WC Debug class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Debug' ) ) {

	class Smart_Rentals_WC_Debug {

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add debug info to admin
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				add_action( 'admin_notices', [ $this, 'debug_notices' ] );
				add_action( 'wp_footer', [ $this, 'frontend_debug' ] );
			}

			// Add debug page
			add_action( 'admin_menu', [ $this, 'add_debug_page' ] );
		}

		/**
		 * Debug notices
		 */
		public function debug_notices() {
			$screen = get_current_screen();
			
			if ( 'product' === $screen->post_type ) {
				global $post;
				
				if ( smart_rentals_wc_is_rental_product( $post->ID ) ) {
					$rental_type = smart_rentals_wc_get_post_meta( $post->ID, 'rental_type' );
					$daily_price = smart_rentals_wc_get_post_meta( $post->ID, 'daily_price' );
					$hourly_price = smart_rentals_wc_get_post_meta( $post->ID, 'hourly_price' );
					
					echo '<div class="notice notice-info">';
					echo '<p><strong>Smart Rentals Debug:</strong> ';
					echo sprintf( 'Product ID: %d, Type: %s, Daily: %s, Hourly: %s', 
						$post->ID, 
						$rental_type ?: 'Not set', 
						$daily_price ?: 'Not set', 
						$hourly_price ?: 'Not set' 
					);
					echo '</p>';
					echo '</div>';
				}
			}
		}

		/**
		 * Frontend debug
		 */
		public function frontend_debug() {
			if ( is_product() ) {
				global $post;
				
				if ( smart_rentals_wc_is_rental_product( $post->ID ) ) {
					echo '<div id="smart-rentals-debug" style="position: fixed; bottom: 10px; right: 10px; background: #333; color: #fff; padding: 10px; border-radius: 4px; font-size: 12px; z-index: 9999; max-width: 300px;">';
					echo '<strong>Smart Rentals Debug</strong><br>';
					echo 'Product ID: ' . $post->ID . '<br>';
					echo 'Is Rental: ' . ( smart_rentals_wc_is_rental_product( $post->ID ) ? 'Yes' : 'No' ) . '<br>';
					echo 'Type: ' . ( smart_rentals_wc_get_post_meta( $post->ID, 'rental_type' ) ?: 'Not set' ) . '<br>';
					echo 'Daily Price: ' . ( smart_rentals_wc_get_post_meta( $post->ID, 'daily_price' ) ?: 'Not set' ) . '<br>';
					echo 'Hourly Price: ' . ( smart_rentals_wc_get_post_meta( $post->ID, 'hourly_price' ) ?: 'Not set' ) . '<br>';
					echo '<button onclick="this.parentElement.style.display=\'none\'" style="background: #dc3232; color: #fff; border: none; padding: 2px 6px; border-radius: 2px; cursor: pointer;">×</button>';
					echo '</div>';
				}
			}
		}

		/**
		 * Add debug page
		 */
		public function add_debug_page() {
			add_submenu_page(
				'smart-rentals-wc',
				__( 'Debug Info', 'smart-rentals-wc' ),
				__( 'Debug Info', 'smart-rentals-wc' ),
				'manage_options',
				'smart-rentals-wc-debug',
				[ $this, 'debug_page' ]
			);
		}

		/**
		 * Debug page
		 */
		public function debug_page() {
			?>
			<div class="wrap">
				<h1><?php _e( 'Smart Rentals Debug Information', 'smart-rentals-wc' ); ?></h1>
				
				<h2><?php _e( 'Plugin Status', 'smart-rentals-wc' ); ?></h2>
				<table class="form-table">
					<tr>
						<th><?php _e( 'Plugin Version', 'smart-rentals-wc' ); ?></th>
						<td><?php echo esc_html( Smart_Rentals_WC()->get_version() ); ?></td>
					</tr>
					<tr>
						<th><?php _e( 'WooCommerce Version', 'smart-rentals-wc' ); ?></th>
						<td><?php echo esc_html( WC()->version ); ?></td>
					</tr>
					<tr>
						<th><?php _e( 'WordPress Version', 'smart-rentals-wc' ); ?></th>
						<td><?php echo esc_html( get_bloginfo( 'version' ) ); ?></td>
					</tr>
					<tr>
						<th><?php _e( 'PHP Version', 'smart-rentals-wc' ); ?></th>
						<td><?php echo esc_html( PHP_VERSION ); ?></td>
					</tr>
				</table>

				<h2><?php _e( 'Database Tables', 'smart-rentals-wc' ); ?></h2>
				<?php
				global $wpdb;
				$tables = [
					'smart_rentals_bookings',
					'smart_rentals_availability', 
					'smart_rentals_resources'
				];
				?>
				<table class="form-table">
					<?php foreach ( $tables as $table ) : ?>
					<?php 
					$table_name = $wpdb->prefix . $table;
					$exists = $wpdb->get_var( "SHOW TABLES LIKE '$table_name'" ) == $table_name;
					?>
					<tr>
						<th><?php echo esc_html( $table ); ?></th>
						<td>
							<?php if ( $exists ) : ?>
								<span style="color: green;">✓ Exists</span>
								<?php 
								$count = $wpdb->get_var( "SELECT COUNT(*) FROM $table_name" );
								echo ' (' . intval( $count ) . ' records)';
								?>
							<?php else : ?>
								<span style="color: red;">✗ Missing</span>
							<?php endif; ?>
						</td>
					</tr>
					<?php endforeach; ?>
				</table>

				<h2><?php _e( 'Rental Products', 'smart-rentals-wc' ); ?></h2>
				<?php
				$rental_products = Smart_Rentals_WC()->options->get_rental_product_ids();
				?>
				<p><?php printf( __( 'Total rental products: %d', 'smart-rentals-wc' ), count( $rental_products ) ); ?></p>
				
				<?php if ( smart_rentals_wc_array_exists( $rental_products ) ) : ?>
					<table class="wp-list-table widefat fixed striped">
						<thead>
							<tr>
								<th><?php _e( 'ID', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Product Name', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Rental Type', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Daily Price', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Hourly Price', 'smart-rentals-wc' ); ?></th>
								<th><?php _e( 'Stock', 'smart-rentals-wc' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( array_slice( $rental_products, 0, 10 ) as $product_id ) : ?>
							<?php $product = wc_get_product( $product_id ); ?>
							<tr>
								<td><?php echo esc_html( $product_id ); ?></td>
								<td><?php echo esc_html( $product ? $product->get_name() : 'Unknown' ); ?></td>
								<td><?php echo esc_html( smart_rentals_wc_get_post_meta( $product_id, 'rental_type' ) ?: 'Not set' ); ?></td>
								<td><?php echo esc_html( smart_rentals_wc_get_post_meta( $product_id, 'daily_price' ) ?: 'Not set' ); ?></td>
								<td><?php echo esc_html( smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' ) ?: 'Not set' ); ?></td>
								<td><?php echo esc_html( smart_rentals_wc_get_post_meta( $product_id, 'rental_stock' ) ?: 'Not set' ); ?></td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>

				<h2><?php _e( 'Test Functions', 'smart-rentals-wc' ); ?></h2>
				<p>
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=smart-rentals-wc-debug&test=create_tables' ) ); ?>" class="button">
						<?php _e( 'Recreate Database Tables', 'smart-rentals-wc' ); ?>
					</a>
				</p>

				<?php
				// Handle test actions
				if ( isset( $_GET['test'] ) ) {
					switch ( $_GET['test'] ) {
						case 'create_tables':
							Smart_Rentals_WC_Install::install();
							echo '<div class="notice notice-success"><p>' . __( 'Database tables recreated!', 'smart-rentals-wc' ) . '</p></div>';
							break;
					}
				}
				?>
			</div>
			<?php
		}
	}

	new Smart_Rentals_WC_Debug();
}