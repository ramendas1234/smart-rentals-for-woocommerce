<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get cart template
$card = ovabrw_get_meta_data( 'card_template', $args, ovabrw_get_card_template() );

// Check show specifications
if ( 'yes' !== ovabrw_get_option( 'glb_'.$card.'_specifications' , 'yes' ) ) return;

// Get all specifications
$specifications = $product->get_specifications();

// Get product specifications
$product_specifications = $product->get_meta_value( 'specifications' );

if ( ovabrw_array_exists( $product_specifications ) ): ?>
	<ul class="ovabrw-specifications">
		<?php foreach ( $product_specifications as $name => $value ):
			// Check value
			if ( !$value ) continue;

			// Check is specification
			if ( !ovabrw_get_meta_data( $name, $specifications ) ) continue;

			// Check enabled
			if ( empty( $specifications[$name]['enable'] ) ) continue;

			// Check show in card
			if ( empty( $specifications[$name]['show_in_card'] ) ) continue;

			// Get type
			$type = isset( $specifications[$name]['type'] ) ? $specifications[$name]['type'] : '';

			// Get label
			$label = isset( $specifications[$name]['label'] ) ? $specifications[$name]['label'] : '';

			// Get icon font
			$icon_font = isset( $specifications[$name]['icon-font'] ) ? $specifications[$name]['icon-font'] : '';

			// is show label
			$show_label = isset( $specifications[$name]['show_label'] ) ? $specifications[$name]['show_label'] : '';
		?>
			<li class="item-specification">
				<?php if ( $icon_font ): // icon font ?>
					<i aria-hidden="true" class="<?php echo esc_attr( $icon_font ); ?>"></i>
				<?php endif;

				// Label
				if ( $label && 'on' === $show_label ): ?>
					<span class="label">
						<?php echo sprintf( esc_html__( '%s:', 'ova-brw' ), $label ); ?>
					</span>
				<?php endif;

				// is file
				if ( 'file' === $type ):
					$attachment_title 	= get_the_title( $value );
					$attachment_url 	= get_permalink( $value );
				?>
					<span>
						<a href="<?php echo esc_url( $attachment_url ); ?>" target="_blank">
							<?php echo esc_html( $attachment_title ); ?>
						</a>
					</span>
				<?php elseif ( in_array( $type, ['radio', 'checkbox', 'select'] ) ):
					if ( !ovabrw_array_exists( $value ) ) $value = [];
				?>
					<span><?php echo join( ', ', $value ); ?></span>
				<?php elseif ( 'color' === $type ): // is color ?>
					<span class="specification-color" style="background-color: <?php echo esc_attr( $value ) ?>;"></span>
				<?php elseif ( 'link' === $type ): // is link ?>
					<span>
						<a href="<?php echo esc_url( $value ); ?>" target="_blank">
							<?php echo esc_html( $label ); ?>
						</a>
					</span>
				<?php elseif ( 'email' === $type ): // is email ?>
					<span>
						<a href="mailto:<?php echo esc_attr( $value ); ?>">
							<?php echo esc_html( $value ); ?>
						</a>
					</span>
				<?php elseif ( 'tel' === $type ): // is phone
					$phone_number = preg_replace('/[^0-9]/', '', $value );
				?>
					<span>
						<a href="tel:<?php echo esc_attr( $phone_number ); ?>">
							<?php echo esc_html( $value ); ?>
						</a>
					</span>
				<?php else: // other ?>
					<span><?php echo esc_html( $value ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>