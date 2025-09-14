<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Enable deposit
$enable_deposit = $product->get_meta_value( 'enable_deposit' );

if ( 'yes' === $enable_deposit ):
	$pay_full 	= $product->get_meta_value( 'force_deposit' );
	$type 		= $product->get_meta_value( 'type_deposit' );
	$value 		= $product->get_meta_value( 'amount_deposit' );

	if ( 'value' === $type ) {
		$value = ovabrw_wc_price( $value );
	} elseif ( 'percent' === $type ) {
		$value .= '%';
	}
?>
	<div class="ovabrw-modern-deposit">
		<?php if ( 'yes' === $pay_full ): ?>
			<div class="deposit-label pay-full active">
				<?php esc_html_e( 'Pay 100%', 'ova-brw' ); ?>
			</div>
			<div class="deposit-label pay-deposit">
				<?php echo sprintf( esc_html__( 'Deposit Option %s Per item', 'ova-brw' ), wc_format_localized_price( $value ) ); ?>
			</div>
		<?php else: ?>
			<div class="deposit-label pay-deposit active">
				<?php echo sprintf( esc_html__( 'Deposit Option %s Per item', 'ova-brw' ), wc_format_localized_price( $value ) ); ?>
			</div>
		<?php endif; ?>
		<div class="deposit-type">
			<?php if ( 'yes' === $pay_full ): ?>
				<label class="ovabrw-label-field">
					<?php ovabrw_text_input([
						'type' 		=> 'radio',
						'class' 	=> 'pay-full',
						'name' 		=> $product->get_meta_key( 'type_deposit' ),
						'value' 	=> 'full',
						'checked' 	=> true
					]); ?>
					<span class="checkmark">
						<?php esc_html_e( 'Full Payment', 'ova-brw' ); ?>
					</span>
				</label>
				<label class="ovabrw-label-field">
					<?php ovabrw_text_input([
						'type' 		=> 'radio',
						'class' 	=> 'pay-deposit',
						'name' 		=> $product->get_meta_key( 'type_deposit' ),
						'value' 	=> 'deposit'
					]); ?>
					<span class="checkmark">
						<?php esc_html_e( 'Deposit Payment', 'ova-brw' ); ?>
					</span>
				</label>
			<?php else: ?>
				<label class="ovabrw-label-field">
					<?php ovabrw_text_input([
						'type' 		=> 'radio',
						'class' 	=> 'pay-deposit',
						'name' 		=> $product->get_meta_key( 'type_deposit' ),
						'value' 	=> 'deposit',
						'checked' 	=> true
					]); ?>
					<span class="checkmark">
						<?php esc_html_e( 'Deposit Payment', 'ova-brw' ); ?>
					</span>
				</label>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>