<?php defined( 'ABSPATH' ) || exit;

// Get rental type
$rental_type = $this->get_type();

if ( $this->product->show_location_field() ): ?>
    <div class="rental_item ovabrw-location-field">
        <label>
            <?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
        </label>
        <?php if ( in_array( $rental_type, [ 'day', 'hour', 'mixed', 'hotel', 'period_time', 'transportation' ] ) ):
            echo $this->product->get_html_location( 'pickup', 'ovabrw_pickup_location[ovabrw-item-key]', 'ovabrw-input-required' ); ?>
                <div class="ovabrw-other-location"></div>
        <?php elseif ( 'taxi' === $rental_type ): ?>
            <span class="location-field">
                <?php ovabrw_text_input([
                    'type'          => 'text',
                    'id'            => 'ovabrw_pickup_location_'.esc_attr( $this->get_id() ),
                    'class'         => 'ovabrw_pickup_location',
                    'name'          => 'ovabrw_pickup_location',
                    'key'           => 'ovabrw-item-key',
                    'placeholder'   => esc_html__( 'Enter a location', 'ova-brw' ),
                    'required'      => true
                ]); ?>
                <?php ovabrw_text_input([
                    'type'      => 'hidden',
                    'id'        => 'ovabrw_origin',
                    'name'      => 'ovabrw_origin',
                    'key'       => 'ovabrw-item-key',
                    'required'  => true
                ]); ?>
                <i class="flaticon-add btn-add-waypoint" aria-hidden="true"></i>
            </span>
        <?php endif; ?>
    </div>
<?php endif;