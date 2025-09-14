<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get daily prices
$daily_prices = $product->get_daily_prices();

if ( ovabrw_array_exists( $daily_prices ) ): ?>
	<div class="price_table">
		<label>
			<?php esc_html_e( 'Price by day of the week', 'ova-brw' ); ?>
		</label>
		<table>
			<thead>
				<tr>
					<th><?php esc_html_e( 'Weekdays', 'ova-brw' ); ?></th>
					<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $daily_prices as $dayofweek => $price ):
					switch ( $dayofweek ) {
						case 'monday':
							$day 	= esc_html__( 'Monday', 'ova-brw' );
							$class 	= 'eve';
							break;
						case 'tuesday':
							$day 	= esc_html__( 'Tuesday', 'ova-brw' );
							$class 	= 'odd';
							break;
						case 'wednesday':
							$day 	= esc_html__( 'Wednesday', 'ova-brw' );
							$class 	= 'eve';
							break;
						case 'thursday':
							$day 	= esc_html__( 'Thursday', 'ova-brw' );
							$class 	= 'odd';
							break;
						case 'friday':
							$day 	= esc_html__( 'Friday', 'ova-brw' );
							$class 	= 'eve';
							break;
						case 'saturday':
							$day 	= esc_html__( 'Saturday', 'ova-brw' );
							$class 	= 'odd';
							break;	
						case 'sunday':
							$day 	= esc_html__( 'Sunday', 'ova-brw' );
							$class 	= 'eve';
							break;		
						default:
							$day = $class = '';
							break;
					}
				?>
					<tr class="<?php echo esc_attr( $class ); ?>">
						<td class="bold">
							<?php echo esc_html( $day ); ?>
						</td>
						<td data-title="<?php echo esc_attr( $day ); ?>">
							<?php echo ovabrw_wc_price( $price ); ?>
						</td>
					</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>