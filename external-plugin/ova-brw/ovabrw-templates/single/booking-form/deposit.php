<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

if ( 'yes' === $product->get_meta_value( 'enable_deposit' ) ):
	$pay_full 	= $product->get_meta_value( 'force_deposit' );
	$type 		= $product->get_meta_value( 'type_deposit' );
	$value 		= $product->get_meta_value( 'amount_deposit' );

	if ( 'value' === $type ) {
		$value = ovabrw_wc_price( $value );
	} elseif ( 'percent' === $type ) {
		$value .= '%';
	}

	$rand = rand();
?>
	<div class="ovabrw-deposit">
		<div class="title-deposite">
			<span>
				<?php echo sprintf( esc_html__( 'Deposit Option %s Per item', 'ova-brw' ), $value ); ?>
			</span>
		</div>
		<div class="ovabrw-type-deposit">
			<?php if ( 'yes' === $pay_full ):
				ovabrw_text_input([
					'type' 		=> 'radio',
					'id' 		=> 'ovabrw-pay-full-'.esc_attr( $rand ),
					'class' 	=> 'ovabrw-pay-full',
					'name' 		=> $product->get_meta_key( 'type_deposit' ),
					'value' 	=> 'full',
					'checked' 	=> true
				]); ?>
				<label class="ovabrw-pay-full" for="ovabrw-pay-full-<?php echo esc_attr( $rand ); ?>">
					<?php esc_html_e( 'Full Payment', 'ova-brw' ); ?>
				</label>
				<?php ovabrw_text_input([
					'type' 	=> 'radio',
					'id' 	=> 'ovabrw-pay-deposit-'.esc_attr( $rand ),
					'class' => 'ovabrw-pay-deposit',
					'name' 	=> $product->get_meta_key( 'type_deposit' ),
					'value' => 'deposit'
				]); ?>
				<label class="ovabrw-pay-deposit" for="ovabrw-pay-deposit-<?php echo esc_attr( $rand ); ?>">
					<?php esc_html_e( 'Deposit Payment', 'ova-brw' ); ?>
				</label>
			<?php else:
				ovabrw_text_input([
					'type' 		=> 'radio',
					'id' 		=> 'ovabrw-pay-deposit-'.esc_attr( $rand ),
					'class' 	=> 'ovabrw-pay-deposit',
					'name' 		=> $product->get_meta_key( 'type_deposit' ),
					'value' 	=> 'deposit',
					'checked' 	=> true
				]); ?>
				<label class="ovabrw-pay-deposit" for="ovabrw-pay-deposit-<?php echo esc_attr( $rand ); ?>">
					<?php esc_html_e( 'Deposit Payment', 'ova-brw' ); ?>
				</label>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>