<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get price format HTML
$price_format = $product->get_price_html_from_format();

?>

<div class="ovabrw-product-price">
	<?php if ( $price_format ):
		echo wp_kses_post( $price_format );
	else:
		// Get rental type
		$rental_type = $product->get_rental_type();

		if ( 'day' === $rental_type ):
			$price 	= $product->get_meta_value( 'regular_price_day' );
			$unit 	= esc_html__( '/ Day', 'ova-brw' );

			if ( 'hotel' === $product->get_charged_by() ) {
				$unit = esc_html__( '/ Night', 'ova-brw' );
			}
		?>
			<span class="amount">
				<?php echo ovabrw_wc_price( $price ); ?>
			</span>
			<span class="unit">
				<?php echo esc_html( $unit ); ?>
			</span>
		<?php elseif ( 'hour' === $rental_type ):
			$price = $product->get_meta_value( 'regul_price_hour' )
		?>
			<span class="amount">
				<?php echo ovabrw_wc_price( $price ); ?>
			</span>
			<span class="unit">
				<?php esc_html_e( '/ Hour', 'ova-brw' ); ?>
			</span>
		<?php elseif ( 'mixed' === $rental_type ):
			$price = $product->get_meta_value( 'regul_price_hour' )
		?>
			<span class="unit">
				<?php esc_html_e( 'From', 'ova-brw' ); ?>
			</span>
			<span class="amount">
				<?php echo ovabrw_wc_price( $price ); ?>
			</span>
			<span class="unit">
				<?php esc_html_e( '/ Hour', 'ova-brw' ); ?>
			</span>
		<?php elseif ( 'period_time' == $rental_type ):
			$min = $max = 0;

			// Get prices
			$petime_price = $product->get_meta_value( 'petime_price' );

			if ( ovabrw_array_exists( $petime_price ) ) {
			    $min = min( $petime_price );
			    $max = max( $petime_price );
			}
		
			if ( $min && $max && $min == $max ): ?>
		        <span class="unit">
		        	<?php esc_html_e( 'From', 'ova-brw' ); ?>
		        </span>
				<span class="amount">
					<?php echo ovabrw_wc_price( $min ); ?>
				</span>
			<?php elseif ( $min && $max ): ?>
				<span class="amount">
					<?php echo ovabrw_wc_price( $min ); ?>
				</span>
		        <span class="unit">
		        	<?php esc_html_e( '-', 'ova-brw' ); ?>
		        </span>
				<span class="amount">
					<?php echo ovabrw_wc_price( $max ); ?>
				</span>
			<?php else: ?>
		        <span class="amount">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php esc_html_e( 'Option Price', 'ova-brw' ); ?>
					</a>
				</span>
			<?php endif;
		elseif ( 'transportation' === $rental_type ):
			$min = $max = 0;

			// Get prices
			$price_location = $product->get_meta_value( 'price_location' );

			if ( ovabrw_array_exists( $price_location ) ) {
			    $min = min( $price_location );
			    $max = max( $price_location );
			}
		
			if ( $min && $max && $min == $max ): ?>
		        <span class="unit">
		        	<?php esc_html_e( 'From', 'ova-brw' ); ?>
		        </span>
				<span class="amount">
					<?php echo ovabrw_wc_price( $min ); ?>
				</span>
			<?php elseif ( $min && $max ): ?>
				<span class="amount">
					<?php echo ovabrw_wc_price( $min ); ?>
				</span>
		        <span class="unit">
		        	<?php esc_html_e( '-', 'ova-brw' ); ?>
		        </span>
				<span class="amount">
					<?php echo ovabrw_wc_price( $max ); ?>
				</span>
			<?php else: ?>
		        <span class="amount">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php esc_html_e( 'Option Price', 'ova-brw' ); ?>
					</a>
				</span>
			<?php endif;
		elseif ( 'taxi' === $rental_type ):
			$price 		= $product->get_meta_value( 'regul_price_taxi' );
			$unit 		= esc_html__( '/ Km', 'ova-brw' );
			$price_by 	= $product->get_meta_value( 'map_price_by' );

			if ( 'mi' === $price_by ) {
				$unit = esc_html__( '/ Mi', 'ova-brw' );
			}
		?>
			<span class="amount">
				<?php echo ovabrw_wc_price( $price ); ?>
			</span>
			<span class="unit">
				<?php echo esc_html( $unit ); ?>
			</span>
		<?php elseif ( 'hotel' === $rental_type ):
			$price 	= $product->get_meta_value( 'regular_price_hotel' );
			$unit 	= esc_html__( '/ Night', 'ova-brw' );
		?>
			<span class="amount">
				<?php echo ovabrw_wc_price( $price ); ?>
			</span>
			<span class="unit">
				<?php echo esc_html( $unit ); ?>
			</span>
		<?php elseif ( 'appointment' === $rental_type ):
			$min = $max = '';

			// Get timeslot prices
			$timeslost_prices = $product->get_meta_value( 'time_slots_price' );

			if ( ovabrw_array_exists( $timeslost_prices ) ) {
			    foreach ( $timeslost_prices as $prices ) {
			    	// Min price
			    	$min_price = (float)min( $prices );
			    	if ( '' == $min ) $min = $min_price;
			    	if ( $min > $min_price ) $min = $min_price;

			    	$max_price = (float)max( $prices );
			    	if ( '' == $max ) $max = $max_price;
			    	if ( $max < $max_price ) $max = $max_price;
			    }
			}

			if ( $min && $max && $min == $max ): ?>
		        <span class="unit">
		        	<?php esc_html_e( 'From', 'ova-brw' ); ?>
		        </span>
				<span class="amount">
					<?php echo ovabrw_wc_price( $min ); ?>
				</span>
			<?php elseif ( $min && $max ): ?>
				<span class="amount">
					<?php echo ovabrw_wc_price( $min ); ?>
				</span>
		        <span class="unit">
		        	<?php esc_html_e( '-', 'ova-brw' ); ?>
		        </span>
				<span class="amount">
					<?php echo ovabrw_wc_price( $max ); ?>
				</span>
			<?php else: ?>
		        <span class="amount">
					<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
						<?php esc_html_e( 'Option Price', 'ova-brw' ); ?>
					</a>
				</span>
			<?php endif;
		else: ?>
			<span class="amount">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
					<?php esc_html_e( 'Option Price', 'ova-brw' ); ?>
				</a>
			</span>
		<?php endif;
	endif; ?>
</div>