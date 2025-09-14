<?php defined( 'ABSPATH' ) || exit;

// Get extra time
$extra_time = $this->get_meta_value( 'extra_time_hour', [] );

?>
<div class="rental_item ovabrw-extra-time">
    <label>
        <?php esc_html_e( 'Extra Time', 'ova-brw' ); ?>
    </label>
    <select name="ovabrw_extra_time[ovabrw-item-key]">
        <option value="">
            <?php esc_html_e( 'Select Time', 'ova-brw' ); ?>
        </option>
        <?php if ( ovabrw_array_exists( $extra_time ) ):
        	$extra_label = $this->get_meta_value( 'extra_time_label', [] );

        	foreach ( $extra_time as $k => $time ):
        		$label = ovabrw_get_meta_data( $k, $extra_label );
        ?>
        	<option value="<?php echo esc_attr( $time ); ?>">
        		<?php echo esc_html( $label ); ?>
        	</option>
        <?php endforeach;
        endif; ?>
    </select>
</div>