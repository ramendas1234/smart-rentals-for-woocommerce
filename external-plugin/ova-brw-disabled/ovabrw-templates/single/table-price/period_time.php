<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Package data
$package_price 		= $product->get_meta_value( 'petime_price' );
$package_name 		= $product->get_meta_value( 'petime_label' );
$package_discount 	= $product->get_meta_value( 'petime_discount' );

if ( !ovabrw_array_exists( $package_price ) ) return;

// Date time format
$date_format = OVABRW()->options->get_datetime_format();

// Text
$price_text 		= esc_html__( 'Price', 'ova-brw' );
$start_time_text 	= esc_html__( 'Start Time', 'ova-brw' );
$end_time_text 		= esc_html__( 'End Time', 'ova-brw' );

foreach ( $package_price as $k => $price ):
	$name = ovabrw_get_meta_data( $k, $package_name );

	// Discounts
	$discounts = ovabrw_get_meta_data( $k, $package_discount );

	// Discount prices
	$disc_prices 	= ovabrw_get_meta_data( 'price', $discounts );
	$disc_start 	= ovabrw_get_meta_data( 'start_time', $discounts );
	$disc_end 		= ovabrw_get_meta_data( 'end_time', $discounts );
?>
	<div class="price_table">
		<label>
			<?php echo sprintf( esc_html__( '%s : %s', 'ova-brw' ), $name, ovabrw_wc_price( $price ) ); ?>
		</label>
		<?php if ( ovabrw_array_exists( $disc_prices ) ): ?>
			<table>
				<thead>
					<tr>
						<th><?php echo esc_html( $price_text ); ?></th>
						<th><?php echo esc_html( $start_time_text ); ?></th>
						<th><?php echo esc_html( $end_time_text ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $disc_prices as $k => $price ):
						// Start date
						$start = strtotime( ovabrw_get_meta_data( $k, $disc_start ) );
						if ( !$start ) continue;

						// End date
						$end = strtotime( ovabrw_get_meta_data( $k, $disc_end ) );
						if ( !$end ) continue;
					?>
						<tr class="<?php echo intval( $k%2 ) ? 'eve' : 'odd'; ?>">
							<td class="bold" data-title="<?php echo esc_attr( $price_text ); ?>">
								<?php echo ovabrw_wc_price( $price ); ?>
							</td>
							<td data-title="<?php echo esc_attr( $start_time_text ); ?>">
								<?php echo esc_html( gmdate( $date_format, $start ) ); ?>
							</td>
							<td data-title="<?php echo esc_attr( $end_time_text ); ?>">
								<?php echo esc_html( gmdate( $date_format, $end ) ); ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>
	</div>
<?php endforeach; ?>
