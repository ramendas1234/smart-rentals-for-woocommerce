<?php defined( 'ABSPATH' ) || exit;

// Get rental type
$rental_type = $this->get_type();

if ( $this->product->show_location_field( 'dropoff' ) ): ?>
    <div class="rental_item ovabrw-location-field">
        <label>
            <?php esc_html_e( 'Pick-off Location', 'ova-brw' ); ?>
        </label>
        <?php if ( in_array( $rental_type, [ 'day', 'hour', 'mixed', 'hotel', 'period_time', 'transportation' ] ) ):
            echo $this->product->get_html_location( 'dropoff', 'ovabrw_dropoff_location[ovabrw-item-key]', 'ovabrw-input-required' ); ?>
                <div class="ovabrw-other-location"></div>
        <?php elseif ( 'taxi' === $rental_type ): ?>
            <span class="location-field">
                <?php ovabrw_text_input([
                    'type'          => 'text',
                    'id'            => 'ovabrw_dropoff_location_'.esc_attr( $this->get_id() ),
                    'class'         => 'ovabrw_dropoff_location',
                    'name'          => 'ovabrw_dropoff_location',
                    'key'           => 'ovabrw-item-key',
                    'placeholder'   => esc_html__( 'Enter a location', 'ova-brw' ),
                    'required'      => true
                ]); ?>
                <?php ovabrw_text_input([
                    'type'      => 'hidden',
                    'id'        => 'ovabrw_destination',
                    'name'      => 'ovabrw_destination',
                    'key'       => 'ovabrw-item-key',
                    'required'  => true
                ]); ?>
            </span>
        <?php endif; ?>
    </div>
<?php endif;