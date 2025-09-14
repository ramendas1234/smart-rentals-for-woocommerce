<?php defined( 'ABSPATH' ) || exit;

// Import variables
extract( $args );

// Date format
$date_format = OVABRW()->options->get_date_format();

// Time format
$time_format = OVABRW()->options->get_time_format();

// Modern template
if ( ovabrw_global_typography() ) {
    $class .= ' ovabrw-modern-product';
}

// Action
$action = home_url();

?>
<div class="ovabrw_wd_search">
    <form 
        action="<?php echo esc_url( $action ); ?>"
        class="ovabrw_search form_ovabrw row <?php echo esc_attr( $class ); ?>"
        enctype="multipart/form-data"
        autocomplete="off">
        <?php ovabrw_text_input([
            'type'  => 'hidden',
            'name'  => 'ovabrw_search_url',
            'value' => $action
        ]); ?>
        <div class="wrap_content <?php echo esc_attr( $column ); ?>">
            <?php if ( 'yes' === $show_name_product ): ?>
                <div class="s_field">
                    <div class="content">
                        <label>
                            <?php esc_html_e( 'Product name', 'ova-brw' ); ?>
                        </label>
                        <?php ovabrw_text_input([
                            'type'          => 'text',
                            'class'         => $name_product_required,
                            'name'          => 'product_name',
                            'value'         => $name_product,
                            'placeholder'   => esc_html__( 'Product Name', 'ova-brw' )
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( 'yes' === $show_cat ): ?>
                <div class="s_field">
                    <div class="content">
                        <label>
                            <?php esc_html_e( 'Category', 'ova-brw' ); ?>
                        </label>
                        <?php echo OVABRW()->options->get_html_dropdown_categories( $cat, $category_required, $remove_cats_id ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( 'yes' === $show_pickup_loc ): ?>
                <div class="s_field">
                    <div class="content">
                        <label>
                            <?php esc_html_e( 'Pick-up Location', 'ova-brw' ); ?>
                        </label>
                        <?php echo OVABRW()->options->get_html_location( 'pickup', 'pickup_location', $pickup_loc_required, $pickup_loc ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( 'yes' === $show_dropoff_loc ): ?>
                <div class="s_field">
                    <div class="content">
                        <label>
                            <?php esc_html_e( 'Drop-off Location', 'ova-brw' ); ?>
                        </label>
                        <?php echo OVABRW()->options->get_html_location( 'dropoff', 'dropoff_location', $dropoff_loc_required, $pickoff_loc ); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( 'yes' === $show_pickup_date ): ?>
                <div class="s_field">
                    <div class="content">
                        <label>
                            <?php esc_html_e( 'Pick-up Date', 'ova-brw' ); ?>
                        </label>
                        <?php ovabrw_text_input([
                            'type'      => 'text',
                            'id'        => ovabrw_unique_id( 'ovabrw_pickup_date' ),
                            'class'     => 'ovabrw_start_date',
                            'name'      => 'pickup_date',
                            'value'     => $pickup_date,
                            'data_type' => $timepicker ? 'datetimepicker-start' : 'datepicker-start',
                            'required'  => $pickup_date_required ? true : false,
                            'attrs'     => [
                                'data-date' => strtotime( $pickup_date ) ? gmdate( $date_format, strtotime( $pickup_date ) ) : '',
                                'data-time' => strtotime( $pickup_date ) ? gmdate( $time_format, strtotime( $pickup_date ) ) : ''
                            ]
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( 'yes' === $show_dropoff_date ): ?>
                <div class="s_field">
                    <div class="content">
                        <label>
                            <?php esc_html_e( 'Drop-off Date', 'ova-brw' ); ?>
                        </label>
                        <?php ovabrw_text_input([
                            'type'      => 'text',
                            'id'        => ovabrw_unique_id( 'ovabrw_pickoff_date' ),
                            'class'     => 'ovabrw_end_date',
                            'name'      => 'dropoff_date',
                            'value'     => $pickoff_date,
                            'data_type' => $timepicker ? 'datetimepicker-end' : 'datepicker-end',
                            'required'  => $dropoff_date_required ? true : false,
                            'attrs'     => [
                                'data-date' => strtotime( $pickoff_date ) ? gmdate( $date_format, strtotime( $pickoff_date ) ) : '',
                                'data-time' => strtotime( $pickoff_date ) ? gmdate( $time_format, strtotime( $pickoff_date ) ) : ''
                            ]
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>
            <?php if ( 'yes' === $show_attribute ): ?>
                <div class="s_field">
                    <div class="content">
                        <label>
                            <?php esc_html_e( 'Name Attribute', 'ova-brw' ); ?>
                        </label>
                        <?php echo $html_select_attribute; ?>
                    </div>
                </div>
                <?php echo $html_select_value_attribute; ?>
            <?php endif; ?>
            <?php if ( 'yes' === $show_tag_product ): ?>
                <div class="s_field">
                    <div class="content">
                        <label>
                            <?php esc_html_e( 'Product Tag', 'ova-brw' ); ?>
                        </label>
                        <?php ovabrw_text_input([
                            'type'          => 'text',
                            'class'         => $tag_product_required,
                            'name'          => 'product_tag',
                            'value'         => $tag_product,
                            'placeholder'   => esc_html__( 'Product Tag', 'ova-brw' )
                        ]); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php // Taxonomy
                $list_taxonomy      = ovabrw_get_meta_data( 'taxonomy_list_all', $taxonomy_list_wrap, [] );
                $require_taxonomy   = ovabrw_get_meta_data( 'taxonomy_require', $taxonomy_list_wrap, [] );
                $hide_taxonomy      = ovabrw_get_meta_data( 'taxonomy_hide', $taxonomy_list_wrap, [] );
                $get_taxonomy       = ovabrw_get_meta_data( 'taxonomy_get', $taxonomy_list_wrap, [] );

                if ( ovabrw_array_exists( $list_taxonomy ) && 'yes' == $show_tax ) {
                    foreach ( $list_taxonomy as $taxonomy ) {
                        $slug       = ovabrw_get_meta_data( 'slug', $taxonomy );
                        $name       = ovabrw_get_meta_data( 'name', $taxonomy );
                        $required   = '';

                        if ( 'required' == ovabrw_get_meta_data( $slug, $require_taxonomy ) ) {
                            $required = 'ovabrw-input-required';
                        } else {
                            $required = '';
                        }

                        if ( 'hide' != ovabrw_get_meta_data( $slug, $hide_taxonomy ) ): ?>
                            <div class="s_field s_field_cus_tax <?php echo esc_attr( $column. ' '. $slug ); ?>">
                                <div class="content">
                                    <label>
                                        <?php echo esc_html( $name ); ?>
                                    </label>
                                    <?php echo OVABRW()->options->get_html_dropdown_taxonomies_search( $slug, $name, $get_taxonomy[$slug], $required ); ?>
                                </div>
                            </div>
                        <?php endif;
                    }
                }

                ovabrw_text_input([
                    'type'  => 'hidden',
                    'name'  => 'order',
                    'value' => $order
                ]);
                ovabrw_text_input([
                    'type'  => 'hidden',
                    'name'  => 'orderby',
                    'value' => $orderby
                ]);
            ?>
        </div>
        <div class="s_submit">
            <button class="ovabrw_btn_submit" type="submit">
                <?php esc_html_e( 'Search', 'ova-brw' ); ?>
            </button>
        </div>
        <?php ovabrw_text_input([
            'type'  => 'hidden',
            'name'  => 'ovabrw_search',
            'value' => 'search_item'
        ]); ?>
    </form>
</div>