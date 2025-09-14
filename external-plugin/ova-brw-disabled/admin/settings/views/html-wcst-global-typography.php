<?php if ( ! defined( 'ABSPATH' ) ) exit(); ?>
<div class="ovabrw-wcst-global">
	<?php do_action( 'ovabrw-ac-wcst-global-before' ); ?>

	<!-- Font -->
	<?php include( OVABRW_PLUGIN_ADMIN . 'settings/views/html-wcst-global-font.php' ); ?>

	<!-- Color -->
	<?php include( OVABRW_PLUGIN_ADMIN . 'settings/views/html-wcst-global-color.php' ); ?>

	<!-- Heading -->
	<?php include( OVABRW_PLUGIN_ADMIN . 'settings/views/html-wcst-global-heading.php' ); ?>

	<!-- Second Heading -->
	<?php include( OVABRW_PLUGIN_ADMIN . 'settings/views/html-wcst-global-second-heading.php' ); ?>

	<!-- Label -->
	<?php include( OVABRW_PLUGIN_ADMIN . 'settings/views/html-wcst-global-label.php' ); ?>

	<!-- Text -->
	<?php include( OVABRW_PLUGIN_ADMIN . 'settings/views/html-wcst-global-text.php' ); ?>

	<!-- Card -->
	<?php include( OVABRW_PLUGIN_ADMIN . 'settings/views/html-wcst-global-card.php' ); ?>

	<?php do_action( 'ovabrw-ac-wcst-global-after' ); ?>
</div>