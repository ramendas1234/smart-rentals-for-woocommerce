<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Discount prices
$disc_prices 	= $product->get_meta_value( 'discount_distance_price' );
$price_by 		= $product->get_meta_value( 'map_price_by' );

if ( !$price_by ) $price_by = 'km';

if ( ovabrw_array_exists( $disc_prices ) ):
	$disc_from 	= $product->get_meta_value( 'discount_distance_from' );
	$disc_to 	= $product->get_meta_value( 'discount_distance_to' );
?>
	<div class="ovabrw-product-discount">
		<label class="ovabrw-label"><?php esc_html_e( 'Global Discount', 'ova-brw' ); ?></label>
		<table class="ovabrw-table">
			<thead>
				<tr>
					<?php if ( 'km' === $price_by ): ?>
						<th><?php esc_html_e( 'From (Km)', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'To (Km)', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Price/Km', 'ova-brw' ); ?></th>
					<?php else: ?>
						<th><?php esc_html_e( 'From (Mi)', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'To (Mi)', 'ova-brw' ); ?></th>
						<th><?php esc_html_e( 'Price/Mi', 'ova-brw' ); ?></th>
					<?php endif; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $disc_prices as $k => $price ):
					$from 	= ovabrw_get_meta_data( $k, $disc_from );
					$to 	= ovabrw_get_meta_data( $k, $disc_to );
				?>
					<?php if ( '' !== $price && '' !== $from && '' !== $to ): ?>
						<tr>
							<td><?php echo esc_html( $from ); ?></td>
							<td><?php echo esc_html( $to ); ?></td>
							<td><?php echo ovabrw_wc_price( $price ); ?></td>
						</tr>
				<?php endif;
				endforeach; ?>
			</tbody>
		</table>
	</div>
<?php endif; ?>