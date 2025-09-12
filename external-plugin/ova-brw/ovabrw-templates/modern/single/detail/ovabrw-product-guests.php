<?php if ( !defined( 'ABSPATH' ) ) exit();

// Get rental product
$product = ovabrw_get_rental_product( $args );
if ( !$product ) return;

// Total max guests
$max_guests = $product->get_meta_value( 'max_guests' );

// Total min guests
$min_guests = $product->get_meta_value( 'min_guests' );

// Total number of guests
$numberof_guests = 0;

// Adults
$beside_adults      = apply_filters( OVABRW_PREFIX.'beside_adults', '', $product );  
$max_adults         = $product->get_meta_value( 'max_adults' );
$min_adults         = (int)$product->get_meta_value( 'min_adults' );
$numberof_adults    = (int)ovabrw_get_meta_data( 'adults', $_GET, $min_adults );
$numberof_guests    += (int)$numberof_adults;

// Children
if ( apply_filters( OVABRW_PREFIX.'show_children', true ) ) {
    $beside_children    = apply_filters( OVABRW_PREFIX.'beside_children', '', $product );
    $max_children       = $product->get_meta_value( 'max_children' );
    $min_children       = (int)$product->get_meta_value( 'min_children' );
    $numberof_children  = (int)ovabrw_get_meta_data( 'children', $_GET, $min_children );
    $numberof_guests    += (int)$numberof_children;
}

// Babies
if ( apply_filters( OVABRW_PREFIX.'show_babies', true ) ) {
    $beside_babies      = apply_filters( OVABRW_PREFIX.'beside_babies', '', $product );
    $max_babies         = $product->get_meta_value( 'max_babies' );
    $min_babies         = (int)$product->get_meta_value( 'min_babies' );
    $numberof_babies    = (int)ovabrw_get_meta_data( 'babies', $_GET, $min_babies );
    $numberof_guests    += (int)$numberof_babies;
}

?>

<div class="rental_item">
    <label>
        <?php esc_html_e( 'Guests', 'ova-brw' ); ?>
    </label>
    <div class="ovabrw-wrapper-guestspicker">
        <div class="ovabrw-guestspicker">
            <div class="guestspicker">
                <span class="gueststotal"><?php echo esc_html( $numberof_guests ); ?></span>
            </div>
        </div>
        <div class="ovabrw-gueste-error"></div>
        <div class="ovabrw-guestspicker-content">
            <div class="guests-buttons">
                <div class="guests-label">
                    <label><?php esc_html_e( 'Adults', 'ova-brw' ); ?></label>
                    <?php if ( $beside_adults ): ?>
                        <span class="guests-beside beside-adults">
                            <?php echo esc_html( $beside_adults ); ?>
                        </span>
                    <?php endif; ?>
                </div>
                <div class="guests-button">
                    <div class="guests-icon minus">
                        <span class="flaticon flaticon-substract"></span>
                    </div>
                    <?php ovabrw_text_input([
                        'type'      => 'text',
                        'class'     => 'ovabrw_adults',
                        'name'      => $product->get_meta_key( 'adults' ),
                        'value'     => $numberof_adults,
                        'required'  => true,
                        'readonly'  => true,
                    ]); ?>
                    <div class="guests-icon plus">
                        <span class="flaticon flaticon-add"></span>
                    </div>
                    <?php ovabrw_text_input([
                        'type'      => 'hidden',
                        'name'      => $product->get_meta_key( 'min_adults' ),
                        'value'     => $min_adults ? $min_adults : '',
                        'attrs' => [
                            'data-error' => $min_adults ? sprintf( esc_html__( 'Minimum number of adults: %d', 'ova-brw' ), $min_adults ) : ''
                        ]
                    ]); ?>
                    <?php ovabrw_text_input([
                        'type'      => 'hidden',
                        'name'      => $product->get_meta_key( 'max_adults' ),
                        'value'     => $max_adults ? $max_adults : '',
                        'attrs' => [
                            'data-error' => $max_adults ? sprintf( esc_html__( 'Maximum number of adults: %d', 'ova-brw' ), $max_adults ) : ''
                        ]
                    ]); ?>
                </div>
            </div>
            <?php if ( apply_filters( OVABRW_PREFIX.'show_children', true ) ): ?>
                <div class="guests-buttons">
                    <div class="guests-label">
                        <label><?php esc_html_e( 'Children', 'ova-brw' ); ?></label>
                        <?php if ( $beside_children ): ?>
                            <span class="guests-beside beside-children">
                                <?php echo esc_html( $beside_children ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="guests-button">
                        <div class="guests-icon minus">
                            <span class="flaticon flaticon-substract"></span>
                        </div>
                        <?php ovabrw_text_input([
                            'type'      => 'text',
                            'class'     => 'ovabrw_children',
                            'name'      => $product->get_meta_key( 'children' ),
                            'value'     => $numberof_children,
                            'required'  => true,
                            'readonly'  => true,
                        ]); ?>
                        <div class="guests-icon plus">
                            <span class="flaticon flaticon-add"></span>
                        </div>
                        <?php ovabrw_text_input([
                            'type'      => 'hidden',
                            'name'      => $product->get_meta_key( 'min_children' ),
                            'value'     => $min_children ? $min_children : '',
                            'attrs' => [
                                'data-error' => $min_children ? sprintf( esc_html__( 'Minimum number of children: %d', 'ova-brw' ), $min_children ) : ''
                            ]
                        ]); ?>
                        <?php ovabrw_text_input([
                            'type'      => 'hidden',
                            'name'      => $product->get_meta_key( 'max_children' ),
                            'value'     => $max_children ? $max_children : '',
                            'attrs' => [
                                'data-error' => $max_children ? sprintf( esc_html__( 'Maximum number of children: %d', 'ova-brw' ), $max_children ) : ''
                            ]
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( apply_filters( OVABRW_PREFIX.'show_babies', true ) ): ?>
                <div class="guests-buttons">
                    <div class="guests-label">
                        <label><?php esc_html_e( 'Babies', 'ova-brw' ); ?></label>
                        <?php if ( $beside_babies ): ?>
                            <span class="guests-beside beside-babies">
                                <?php echo esc_html( $beside_babies ); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="guests-button">
                        <div class="guests-icon minus">
                            <span class="flaticon flaticon-substract"></span>
                        </div>
                        <?php ovabrw_text_input([
                            'type'      => 'text',
                            'class'     => 'ovabrw_babies',
                            'name'      => $product->get_meta_key( 'babies' ),
                            'value'     => $numberof_babies,
                            'required'  => true,
                            'readonly'  => true,
                        ]); ?>
                        <div class="guests-icon plus">
                            <span class="flaticon flaticon-add"></span>
                        </div>
                        <?php ovabrw_text_input([
                            'type'      => 'hidden',
                            'name'      => $product->get_meta_key( 'min_babies' ),
                            'value'     => $min_babies ? $min_babies : '',
                            'attrs' => [
                                'data-error' => $min_babies ? sprintf( esc_html__( 'Minimum number of babies: %d', 'ova-brw' ), $min_babies ) : ''
                            ]
                        ]); ?>
                        <?php ovabrw_text_input([
                            'type'      => 'hidden',
                            'name'      => $product->get_meta_key( 'max_babies' ),
                            'value'     => $max_babies ? $max_babies : '',
                            'attrs' => [
                                'data-error' => $max_babies ? sprintf( esc_html__( 'Maximum number of babies: %d', 'ova-brw' ), $max_babies ) : ''
                            ]
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php ovabrw_text_input([
                'type'      => 'hidden',
                'name'      => $product->get_meta_key( 'min_guests' ),
                'value'     => $min_guests,
                'attrs' => [
                    'data-error' => $min_guests ? sprintf( esc_html__( 'Minimum number of guests: %d', 'ova-brw' ), $min_guests ) : ''
                ]
            ]); ?>
            <?php ovabrw_text_input([
                'type'      => 'hidden',
                'name'      => $product->get_meta_key( 'max_guests' ),
                'value'     => $max_guests,
                'attrs' => [
                    'data-error' => $max_guests ? sprintf( esc_html__( 'Maximum number of guests: %d', 'ova-brw' ), $max_guests ) : ''
                ]
            ]); ?>
        </div>
    </div>
</div>