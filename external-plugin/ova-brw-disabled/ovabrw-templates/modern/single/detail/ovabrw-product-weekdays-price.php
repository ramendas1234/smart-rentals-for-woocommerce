<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Daily prices
$daily_prices = $product->get_daily_prices();

if ( ovabrw_array_exists( $daily_prices ) ): ?>
	<div class="ovabrw-product-weekdays-price">
		<label class="ovabrw-label">
			<?php esc_html_e( 'Price by day of the week', 'ova-brw' ); ?>
		</label>
		<table class="ovabrw-table">
			<thead>
				<tr>
					<th><?php esc_html_e( 'Weekdays', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $daily_prices as $k => $price ):
					$day = '';

					switch ( $k ) {
						case 'monday':
							$day = esc_html__( 'Monday', 'ova-brw' );
							break;
						case 'tuesday':
							$day = esc_html__( 'Tuesday', 'ova-brw' );
							break;
						case 'wednesday':
							$day = esc_html__( 'Wednesday', 'ova-brw' );
							break;
						case 'thursday':
							$day = esc_html__( 'Thursday', 'ova-brw' );
							break;
						case 'friday':
							$day = esc_html__( 'Friday', 'ova-brw' );
							break;
						case 'saturday':
							$day = esc_html__( 'Saturday', 'ova-brw' );
							break;	
						case 'sunday':
							$day = esc_html__( 'Sunday', 'ova-brw' );
							break;		
						default:
							$day = '';
							break;
					}
				?>
					<tr>
						<td><?php echo esc_html( $day ); ?></td>
						<td><?php echo ovabrw_wc_price( $price ); ?></td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>