<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Discount prices
$disc_prices = $product->get_meta_value( 'global_discount_price' );

if ( ovabrw_array_exists( $disc_prices ) ):
	$disc_from 	= $product->get_meta_value( 'global_discount_duration_val_min' );
	$disc_to 	= $product->get_meta_value( 'global_discount_duration_val_max' );
	$disc_type 	= $product->get_meta_value( 'global_discount_duration_type' );
?>
	<div class="ovabrw-product-discount">
		<label class="ovabrw-label"><?php esc_html_e( 'Global Discount', 'ova-brw' ); ?></label>
		<table class="ovabrw-table">
			<thead>
				<tr>
					<?php if ( $product->is_rental_type( 'hotel' ) ): ?>
						<th><?php esc_html_e( 'Min - Max (Nights)', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Price/Night', 'ova-brw' ); ?></th>
					<?php else: ?>
						<th><?php esc_html_e( 'Min - Max (Days)', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Price/Day', 'ova-brw' ); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $disc_prices as $k => $price ):
					$from 	= ovabrw_get_meta_data( $k, $disc_from );
					$to 	= ovabrw_get_meta_data( $k, $disc_to );
					$type 	= ovabrw_get_meta_data( $k, $disc_type );

					if ( 'days' === $type && $price != '' && $from != '' && $to != '' ): ?>
						<tr>
							<td>
								<span><?php echo esc_html( $from ); ?></span>
								<span> - </span>
								<span><?php echo esc_html( $to ); ?></span>
							</td>
							<td><?php echo ovabrw_wc_price( $price ); ?></td>
						</tr>
				<?php endif;
				endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>