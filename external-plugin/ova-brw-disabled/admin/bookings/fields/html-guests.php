<?php defined( 'ABSPATH' ) || exit();

// Adults
$min_adults = (int)$this->get_meta_value( 'min_adults' );
$max_adults = $this->get_meta_value( 'max_adults' );

// Children
if ( apply_filters( OVABRW_PREFIX.'show_children', true ) ) {
    $min_children = (int)$this->get_meta_value( 'min_children' );
    $max_children = $this->get_meta_value( 'max_children' );
}

// Babies
if ( apply_filters( OVABRW_PREFIX.'show_babies', true ) ) {
    $min_babies = (int)$this->get_meta_value( 'min_babies' );
    $max_babies = $this->get_meta_value( 'max_babies' );
}

?>

<div class="rental_item ovabrw-guests">
    <label>
    	<?php esc_html_e( 'Adults', 'ova-brw' ); ?>
    </label>
    <?php ovabrw_text_input([
    	'type' 		=> 'number',
    	'class' 	=> 'ovabrw_adults',
    	'name' 		=> 'ovabrw_adults',
    	'key' 		=> 'ovabrw-item-key',
    	'value' 	=> $min_adults,
    	'required' 	=> true,
    	'attrs' 	=> [
    		'min' 	=> $min_adults,
    		'max' 	=> $max_adults
    	]
    ]); ?>
</div>
<?php if ( apply_filters( OVABRW_PREFIX.'show_children', true ) ): ?>
	<div class="rental_item ovabrw-guests">
	    <label>
	    	<?php esc_html_e( 'Children', 'ova-brw' ); ?>
	    </label>
	    <?php ovabrw_text_input([
	    	'type' 		=> 'number',
	    	'class' 	=> 'ovabrw_children',
	    	'name' 		=> 'ovabrw_children',
	    	'key' 		=> 'ovabrw-item-key',
	    	'value' 	=> $min_children,
	    	'required' 	=> true,
	    	'attrs' 	=> [
	    		'min' 	=> $min_children,
	    		'max' 	=> $max_children
	    	]
	    ]); ?>
	</div>
<?php endif; ?>
<?php if ( apply_filters( OVABRW_PREFIX.'show_babies', true ) ): ?>
	<div class="rental_item ovabrw-guests">
	    <label>
	    	<?php esc_html_e( 'Babies', 'ova-brw' ); ?>
	    </label>
	    <?php ovabrw_text_input([
	    	'type' 		=> 'number',
	    	'class' 	=> 'ovabrw_babies',
	    	'name' 		=> 'ovabrw_babies',
	    	'key' 		=> 'ovabrw-item-key',
	    	'value' 	=> $min_babies,
	    	'required' 	=> true,
	    	'attrs' 	=> [
	    		'min' 	=> $min_babies,
	    		'max' 	=> $max_babies
	    	]
	    ]); ?>
	</div>
<?php endif; ?>