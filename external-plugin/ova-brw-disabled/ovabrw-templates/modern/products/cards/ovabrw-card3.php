<?php if ( !defined( 'ABSPATH' ) ) exit();

// Set card template
if ( !isset( $args ) ) $args = [];
$args['card_template'] = 'card2';

?>

<div class="ovabrw-card-template ovabrw-card3">
	<div class="ovabrw-card-header">
		<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-slide-image.php', $args ); ?>
		<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-featured.php', $args ); ?>
	</div>
	<div class="ovabrw-card-content">
		<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-title.php', $args ); ?>
		<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-review.php', $args ); ?>
		<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-specifications.php', $args ); ?>
		<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-features.php', $args ); ?>
		<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-custom-taxonomy.php', $args ); ?>
		<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-attribute.php', $args ); ?>
		<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-short-description.php', $args ); ?>

		<div class="ovabrw-card-btn">
			<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-price.php', $args ); ?>
			<?php ovabrw_get_template( 'modern/products/cards/sections/ovabrw-card-button.php', $args ); ?>
		</div>
	</div>
</div>