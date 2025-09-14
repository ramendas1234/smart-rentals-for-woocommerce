<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Get product specifications
$specifications 		= $product->get_specifications();
$specifications_data 	= $product->get_meta_value( 'specifications' );

if ( ovabrw_array_exists( $specifications_data ) ): ?>
	<ul class="ovabrw-product-specifications">
		<?php foreach ( $specifications_data as $name => $value ):
			if ( ! array_key_exists( $name, $specifications ) || empty( $value ) ) continue;
			if ( ! isset( $specifications[$name]['enable'] ) || ! $specifications[$name]['enable'] ) continue;
			$type 		= isset( $specifications[$name]['type'] ) ? $specifications[$name]['type'] : '';
			$label 		= isset( $specifications[$name]['label'] ) ? $specifications[$name]['label'] : '';
			$icon_font 	= isset( $specifications[$name]['icon-font'] ) ? $specifications[$name]['icon-font'] : '';
			$show_label = isset( $specifications[$name]['show_label'] ) ? $specifications[$name]['show_label'] : '';
		?>
			<li class="item-specification">
				<?php if ( $icon_font ): ?>
					<i aria-hidden="true" class="<?php echo esc_attr( $icon_font ); ?>"></i>
				<?php endif; ?>
				<?php if ( $label && 'on' === $show_label ): ?>
					<label><?php echo esc_html( $label ); ?>: </label>
				<?php endif; ?>
				<?php if ( $type === 'file' ):
					$attachment_title 	= get_the_title( $value );
					$attachment_url 	= get_permalink( $value );
				?>
					<span>
						<a href="<?php echo esc_url( $attachment_url ); ?>" target="_blank">
							<?php echo esc_html( $attachment_title ); ?>
						</a>
					</span>
				<?php elseif ( in_array( $type, ['radio', 'checkbox', 'select'] ) ):
					if ( empty( $value ) || ! is_array( $value ) ) $value = [];
				?>
					<span><?php echo join( ', ', $value ); ?></span>
				<?php elseif ( $type === 'color' ): ?>
					<span class="specification-color" style="background-color: <?php echo esc_attr( $value ) ?>;"></span>
				<?php elseif ( $type === 'link' ): ?>
					<span>
						<a href="<?php echo esc_url( $value ); ?>" target="_blank">
							<?php echo esc_html( $label ); ?>
						</a>
					</span>
				<?php elseif ( $type === 'email' ): ?>
					<span>
						<a href="mailto:<?php echo esc_attr( $value ); ?>">
							<?php echo esc_html( $value ); ?>
						</a>
					</span>
				<?php elseif ( $type === 'tel' ):
					$phone_number = preg_replace('/[^0-9]/', '', $value );
				?>
					<span>
						<a href="tel:<?php echo esc_attr( $phone_number ); ?>">
							<?php echo esc_html( $value ); ?>
						</a>
					</span>
				<?php else: ?>
					<span><?php echo esc_html( $value ); ?></span>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php endif; ?>