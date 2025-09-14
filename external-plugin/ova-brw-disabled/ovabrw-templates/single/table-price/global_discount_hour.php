<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get discount data
$disc_price = $product->get_meta_value( 'global_discount_price' );
$disc_from 	= $product->get_meta_value( 'global_discount_duration_val_min' );
$disc_to 	= $product->get_meta_value( 'global_discount_duration_val_max' );
$disc_type 	= $product->get_meta_value( 'global_discount_duration_type' );

if ( !ovabrw_array_exists( $disc_from ) ) return;

// Sort
asort( $disc_from );

$title_text = esc_html__( 'Min - Max (Hours)', 'ova-brw' );
$price_text = esc_html__( 'Price/Hour', 'ova-brw' ); 

?>

<div class="price_table">
	<label>
		<?php esc_html_e( 'Global Discount', 'ova-brw' ); ?>
	</label>
	<table>
		<thead>
			<tr>
				<th><?php echo esc_html( $title_text ); ?></th>
				<th><?php echo esc_html( $price_text ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php $flag = 0;
			foreach ( $disc_from as $k => $from ):
				$to 	= ovabrw_get_meta_data( $k, $disc_to );
				$price 	= ovabrw_get_meta_data( $k, $disc_price );
				$type 	= ovabrw_get_meta_data( $k, $disc_type );

				if ( 'hours' === $type ): ?>
					<tr class="<?php echo intval( $flag%2 ) ? 'eve' : 'odd'; $flag++; ?>">
						<td class="bold" data-title="<?php echo esc_attr( $title_text ); ?>">
							<?php echo sprintf( esc_html__( '%s - %s', 'ova-brw' ), $from, $to ); ?>
						</td>
						<td data-title="<?php echo sprintf( esc_html__( '%s from %s - %s hours', 'ova-brw' ), $price_text, $from, $to ); ?>">
							<?php echo ovabrw_wc_price( $price ); ?>	
						</td>
					</tr>			
				<?php endif;
			endforeach; ?>
		</tbody>
	</table>
</div>