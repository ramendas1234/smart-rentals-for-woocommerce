<?php
/**
 * Smart Rentals WC Rental class
 */

if ( !defined( 'ABSPATH' ) ) exit();

if ( !class_exists( 'Smart_Rentals_WC_Rental' ) ) {

	class Smart_Rentals_WC_Rental {

		/**
		 * Instance
		 */
		protected static $_instance = null;

		/**
		 * Constructor
		 */
		public function __construct() {
			// Add rental fields to product page
			add_action( 'woocommerce_before_add_to_cart_button', [ $this, 'add_rental_fields_to_product' ] );

			// Modify product price display
			add_filter( 'woocommerce_get_price_html', [ $this, 'get_rental_price_html' ], 11, 2 );

			// Add rental info to product summary
			add_action( 'woocommerce_single_product_summary', [ $this, 'add_rental_info' ], 25 );
		}

		/**
		 * Add rental fields to product page
		 */
		public function add_rental_fields_to_product() {
			global $product;

			if ( !smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				return;
			}

			$rental_type = smart_rentals_wc_get_post_meta( $product->get_id(), 'rental_type' );
			$enable_calendar = smart_rentals_wc_get_post_meta( $product->get_id(), 'enable_calendar' );
			$min_rental_period = smart_rentals_wc_get_post_meta( $product->get_id(), 'min_rental_period' );
			$max_rental_period = smart_rentals_wc_get_post_meta( $product->get_id(), 'max_rental_period' );

			?>
			<div class="smart-rentals-booking-form">
				<h3><?php _e( 'Rental Details', 'smart-rentals-wc' ); ?></h3>
				
				<div class="smart-rentals-dates">
					<div class="smart-rentals-date-field">
						<label for="pickup_date"><?php _e( 'Pickup Date', 'smart-rentals-wc' ); ?> <span class="required">*</span></label>
						<input type="date" id="pickup_date" name="pickup_date" required min="<?php echo esc_attr( date( 'Y-m-d' ) ); ?>" />
					</div>
					
					<div class="smart-rentals-date-field">
						<label for="dropoff_date"><?php _e( 'Drop-off Date', 'smart-rentals-wc' ); ?> <span class="required">*</span></label>
						<input type="date" id="dropoff_date" name="dropoff_date" required min="<?php echo esc_attr( date( 'Y-m-d', strtotime( '+1 day' ) ) ); ?>" />
					</div>
				</div>

				<?php if ( in_array( $rental_type, [ 'hour', 'mixed', 'appointment' ] ) ) : ?>
				<div class="smart-rentals-times">
					<div class="smart-rentals-time-field">
						<label for="pickup_time"><?php _e( 'Pickup Time', 'smart-rentals-wc' ); ?></label>
						<input type="time" id="pickup_time" name="pickup_time" />
					</div>
					
					<div class="smart-rentals-time-field">
						<label for="dropoff_time"><?php _e( 'Drop-off Time', 'smart-rentals-wc' ); ?></label>
						<input type="time" id="dropoff_time" name="dropoff_time" />
					</div>
				</div>
				<?php endif; ?>

				<div class="smart-rentals-info">
					<?php if ( $min_rental_period ) : ?>
						<p class="rental-period-info">
							<?php 
							if ( $rental_type === 'hour' ) {
								printf( __( 'Minimum rental period: %d hours', 'smart-rentals-wc' ), $min_rental_period );
							} else {
								printf( __( 'Minimum rental period: %d days', 'smart-rentals-wc' ), $min_rental_period );
							}
							?>
						</p>
					<?php endif; ?>

					<?php if ( $max_rental_period ) : ?>
						<p class="rental-period-info">
							<?php 
							if ( $rental_type === 'hour' ) {
								printf( __( 'Maximum rental period: %d hours', 'smart-rentals-wc' ), $max_rental_period );
							} else {
								printf( __( 'Maximum rental period: %d days', 'smart-rentals-wc' ), $max_rental_period );
							}
							?>
						</p>
					<?php endif; ?>

					<div id="rental-price-display" class="rental-price-display" style="display: none;">
						<p><strong><?php _e( 'Rental Price:', 'smart-rentals-wc' ); ?> <span id="rental-price-amount"></span></strong></p>
						<p><small id="rental-duration-text"></small></p>
					</div>
				</div>

				<?php if ( 'yes' === $enable_calendar ) : ?>
				<div class="smart-rentals-calendar" id="smart-rentals-calendar">
					<!-- Calendar will be loaded here via JavaScript -->
				</div>
				<?php endif; ?>
			</div>

			<?php
			// Add JavaScript for rental form
			$this->add_rental_form_script( $product->get_id() );
		}

		/**
		 * Add rental form script
		 */
		private function add_rental_form_script( $product_id ) {
			$rental_type = smart_rentals_wc_get_post_meta( $product_id, 'rental_type' );
			$daily_price = smart_rentals_wc_get_post_meta( $product_id, 'daily_price' );
			$hourly_price = smart_rentals_wc_get_post_meta( $product_id, 'hourly_price' );
			$min_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'min_rental_period' );
			$max_rental_period = smart_rentals_wc_get_post_meta( $product_id, 'max_rental_period' );

			?>
			<script type="text/javascript">
			jQuery(document).ready(function($) {
				var rentalType = '<?php echo esc_js( $rental_type ); ?>';
				var dailyPrice = <?php echo floatval( $daily_price ); ?>;
				var hourlyPrice = <?php echo floatval( $hourly_price ); ?>;
				var minPeriod = <?php echo intval( $min_rental_period ); ?>;
				var maxPeriod = <?php echo intval( $max_rental_period ); ?>;
				var currencySymbol = '<?php echo esc_js( get_woocommerce_currency_symbol() ); ?>';

				function calculateRentalPrice() {
					var pickupDate = $('#pickup_date').val();
					var dropoffDate = $('#dropoff_date').val();
					var pickupTime = $('#pickup_time').val() || '00:00';
					var dropoffTime = $('#dropoff_time').val() || '23:59';

					if (!pickupDate || !dropoffDate) {
						$('#rental-price-display').hide();
						return;
					}

					var pickup = new Date(pickupDate + ' ' + pickupTime);
					var dropoff = new Date(dropoffDate + ' ' + dropoffTime);
					
					if (pickup >= dropoff) {
						$('#rental-price-display').hide();
						return;
					}

					var durationMs = dropoff - pickup;
					var durationHours = durationMs / (1000 * 60 * 60);
					var durationDays = durationMs / (1000 * 60 * 60 * 24);

					var totalPrice = 0;
					var durationText = '';

					switch (rentalType) {
						case 'day':
						case 'hotel':
							var days = Math.max(1, Math.ceil(durationDays));
							totalPrice = dailyPrice * days;
							durationText = days + ' ' + (days === 1 ? '<?php echo esc_js( __( 'day', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>');
							break;

						case 'hour':
						case 'appointment':
							var hours = Math.max(1, Math.ceil(durationHours));
							totalPrice = hourlyPrice * hours;
							durationText = hours + ' ' + (hours === 1 ? '<?php echo esc_js( __( 'hour', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>');
							break;

						case 'mixed':
							if (durationHours >= 24 && dailyPrice > 0) {
								var days = Math.max(1, Math.ceil(durationDays));
								totalPrice = dailyPrice * days;
								durationText = days + ' ' + (days === 1 ? '<?php echo esc_js( __( 'day', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>');
							} else if (hourlyPrice > 0) {
								var hours = Math.max(1, Math.ceil(durationHours));
								totalPrice = hourlyPrice * hours;
								durationText = hours + ' ' + (hours === 1 ? '<?php echo esc_js( __( 'hour', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>');
							}
							break;
					}

					if (totalPrice > 0) {
						$('#rental-price-amount').text(currencySymbol + totalPrice.toFixed(2));
						$('#rental-duration-text').text('<?php echo esc_js( __( 'Duration:', 'smart-rentals-wc' ) ); ?> ' + durationText);
						$('#rental-price-display').show();
					} else {
						$('#rental-price-display').hide();
					}
				}

				// Bind events
				$('#pickup_date, #dropoff_date, #pickup_time, #dropoff_time').on('change', calculateRentalPrice);

				// Set minimum dropoff date when pickup date changes
				$('#pickup_date').on('change', function() {
					var pickupDate = $(this).val();
					if (pickupDate) {
						var minDropoffDate = new Date(pickupDate);
						minDropoffDate.setDate(minDropoffDate.getDate() + 1);
						$('#dropoff_date').attr('min', minDropoffDate.toISOString().split('T')[0]);
					}
				});

				// Validation before add to cart
				$('form.cart').on('submit', function(e) {
					var pickupDate = $('#pickup_date').val();
					var dropoffDate = $('#dropoff_date').val();

					if (!pickupDate || !dropoffDate) {
						e.preventDefault();
						alert('<?php echo esc_js( __( 'Please select pickup and drop-off dates.', 'smart-rentals-wc' ) ); ?>');
						return false;
					}

					var pickup = new Date(pickupDate);
					var dropoff = new Date(dropoffDate);

					if (pickup >= dropoff) {
						e.preventDefault();
						alert('<?php echo esc_js( __( 'Drop-off date must be after pickup date.', 'smart-rentals-wc' ) ); ?>');
						return false;
					}

					// Check minimum and maximum periods
					var durationMs = dropoff - pickup;
					var durationHours = durationMs / (1000 * 60 * 60);
					var durationDays = durationMs / (1000 * 60 * 60 * 24);

					if (minPeriod > 0) {
						if ((rentalType === 'hour' && durationHours < minPeriod) || 
							(rentalType !== 'hour' && durationDays < minPeriod)) {
							e.preventDefault();
							var periodType = rentalType === 'hour' ? '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>';
							alert('<?php echo esc_js( __( 'Minimum rental period is', 'smart-rentals-wc' ) ); ?> ' + minPeriod + ' ' + periodType + '.');
							return false;
						}
					}

					if (maxPeriod > 0) {
						if ((rentalType === 'hour' && durationHours > maxPeriod) || 
							(rentalType !== 'hour' && durationDays > maxPeriod)) {
							e.preventDefault();
							var periodType = rentalType === 'hour' ? '<?php echo esc_js( __( 'hours', 'smart-rentals-wc' ) ); ?>' : '<?php echo esc_js( __( 'days', 'smart-rentals-wc' ) ); ?>';
							alert('<?php echo esc_js( __( 'Maximum rental period is', 'smart-rentals-wc' ) ); ?> ' + maxPeriod + ' ' + periodType + '.');
							return false;
						}
					}
				});
			});
			</script>
			<?php
		}

		/**
		 * Get rental price HTML
		 */
		public function get_rental_price_html( $price, $product ) {
			if ( !smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				return $price;
			}

			$rental_type = smart_rentals_wc_get_post_meta( $product->get_id(), 'rental_type' );
			$daily_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'daily_price' );
			$hourly_price = smart_rentals_wc_get_post_meta( $product->get_id(), 'hourly_price' );

			$rental_price = '';

			switch ( $rental_type ) {
				case 'day':
				case 'hotel':
					if ( $daily_price > 0 ) {
						$rental_price = wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
					}
					break;

				case 'hour':
				case 'appointment':
					if ( $hourly_price > 0 ) {
						$rental_price = wc_price( $hourly_price ) . ' ' . __( 'per hour', 'smart-rentals-wc' );
					}
					break;

				case 'mixed':
					$price_parts = [];
					if ( $daily_price > 0 ) {
						$price_parts[] = wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
					}
					if ( $hourly_price > 0 ) {
						$price_parts[] = wc_price( $hourly_price ) . ' ' . __( 'per hour', 'smart-rentals-wc' );
					}
					if ( smart_rentals_wc_array_exists( $price_parts ) ) {
						$rental_price = implode( ' / ', $price_parts );
					}
					break;

				default:
					if ( $daily_price > 0 ) {
						$rental_price = wc_price( $daily_price ) . ' ' . __( 'per day', 'smart-rentals-wc' );
					}
					break;
			}

			if ( $rental_price ) {
				return '<span class="smart-rentals-price">' . $rental_price . '</span>';
			}

			return $price;
		}

		/**
		 * Add rental info to product summary
		 */
		public function add_rental_info() {
			global $product;

			if ( !smart_rentals_wc_is_rental_product( $product->get_id() ) ) {
				return;
			}

			$rental_stock = smart_rentals_wc_get_post_meta( $product->get_id(), 'rental_stock' );
			$security_deposit = smart_rentals_wc_get_post_meta( $product->get_id(), 'security_deposit' );

			?>
			<div class="smart-rentals-info">
				<?php if ( $rental_stock ) : ?>
					<p class="rental-availability">
						<strong><?php _e( 'Availability:', 'smart-rentals-wc' ); ?></strong>
						<?php printf( _n( '%d item available', '%d items available', $rental_stock, 'smart-rentals-wc' ), $rental_stock ); ?>
					</p>
				<?php endif; ?>

				<?php if ( $security_deposit > 0 ) : ?>
					<p class="security-deposit">
						<strong><?php _e( 'Security Deposit:', 'smart-rentals-wc' ); ?></strong>
						<?php echo wc_price( $security_deposit ); ?>
						<small><?php _e( '(refundable)', 'smart-rentals-wc' ); ?></small>
					</p>
				<?php endif; ?>
			</div>
			<?php
		}

		/**
		 * Get rental products
		 */
		public function get_rental_products( $args = [] ) {
			$default_args = [
				'post_type' => 'product',
				'post_status' => 'publish',
				'posts_per_page' => -1,
				'meta_query' => [
					[
						'key' => smart_rentals_wc_meta_key( 'enable_rental' ),
						'value' => 'yes',
						'compare' => '='
					]
				]
			];

			$args = wp_parse_args( $args, $default_args );

			return get_posts( $args );
		}

		/**
		 * Check if product can be rented
		 */
		public function can_be_rented( $product_id, $pickup_date = null, $dropoff_date = null, $quantity = 1 ) {
			if ( !smart_rentals_wc_is_rental_product( $product_id ) ) {
				return false;
			}

			// If no dates provided, just check if rental is enabled
			if ( !$pickup_date || !$dropoff_date ) {
				return true;
			}

			// Check availability
			return Smart_Rentals_WC()->options->check_availability( $product_id, $pickup_date, $dropoff_date, $quantity );
		}

		/**
		 * Get rental types
		 */
		public function get_rental_types() {
			return apply_filters( 'smart_rentals_wc_rental_types', [
				'day' => __( 'Daily', 'smart-rentals-wc' ),
				'hour' => __( 'Hourly', 'smart-rentals-wc' ),
				'mixed' => __( 'Mixed (Daily/Hourly)', 'smart-rentals-wc' ),
				'period_time' => __( 'Package/Period', 'smart-rentals-wc' ),
				'transportation' => __( 'Transportation', 'smart-rentals-wc' ),
				'hotel' => __( 'Hotel/Accommodation', 'smart-rentals-wc' ),
				'appointment' => __( 'Appointment', 'smart-rentals-wc' ),
				'taxi' => __( 'Taxi/Distance', 'smart-rentals-wc' ),
			]);
		}

		/**
		 * Main Smart_Rentals_WC_Rental Instance.
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
	}
}