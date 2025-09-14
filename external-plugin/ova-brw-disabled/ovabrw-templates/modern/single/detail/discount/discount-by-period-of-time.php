<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Package prices
$package_prices = $product->get_meta_value( 'petime_price' );

if ( ovabrw_array_exists( $package_prices ) ):
	// Package names
	$package_names = $product->get_meta_value( 'petime_label' );

	// Discounts
	$discounts = $product->get_meta_value( 'petime_discount' );

	// Datetime format
	$date_format = OVABRW()->options->get_datetime_format();
?>
	<div class="ovabrw-product-discount">
		<?php foreach ( $package_prices as $k => $price ):
			$name = ovabrw_get_meta_data( $k, $package_names );

			// Discount prices
			$disc_prices 	= isset( $discounts[$k]['price'] ) ? $discounts[$k]['price'] : '';
			$disc_start 	= isset( $discounts[$k]['start_time'] ) ? $discounts[$k]['start_time'] : '';
			$disc_end 		= isset( $discounts[$k]['end_time'] ) ? $discounts[$k]['end_time'] : '';
		?>
			<label class="ovabrw-label">
				<?php echo sprintf( esc_html__( '%s: %s', 'ova-brw' ), $name, ovabrw_wc_price( $price ) ); ?>
			</label>
			<?php if ( ovabrw_array_exists( $disc_prices ) ): ?>
				<table class="ovabrw-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Price', 'ova-brw' ); ?></th>
							<th><?php esc_html_e( 'Start Date', 'ova-brw' ); ?></th>
							<th><?php esc_html_e( 'End Date', 'ova-brw' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $disc_prices as $i => $disc_price ):
							$start 	= strtotime( ovabrw_get_meta_data( $i, $disc_start ) );
							$end 	= strtotime( ovabrw_get_meta_data( $i, $disc_end ) );

							if ( '' !== $disc_price && $start && $end ): ?>
								<tr>
									<td><?php echo ovabrw_wc_price( $disc_price ); ?></td>
									<td><?php echo gmdate( $date_format, $start ); ?></td>
									<td><?php echo gmdate( $date_format, $end ); ?></td>
								</tr>
						<?php endif;
						endforeach; ?>
					</tbody>
				</table>
			<?php endif;
		endforeach; ?>
	</div>
<?php endif;