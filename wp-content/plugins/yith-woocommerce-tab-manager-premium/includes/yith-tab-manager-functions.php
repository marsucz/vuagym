<?php

if( !function_exists( 'get_html_icon' ) && class_exists( 'YWTM_Icon' ) ) {
    /**Print the html code for admin
     * @param $tab
     * @author YITHEMES
     * @since 1.0.0
     * @return string
     */
    function get_html_icon( $tab )
    {


        $icon = get_post_meta( $tab, '_ywtm_icon_tab', true );
        $tab_icon = '';
        if( !empty( $icon ) ) {


            switch ( $icon['select'] ) {
                case 'icon' :
                    $tab_icon = sprintf( '<span class="ywtm_icon" %s style="padding-right:10px;"></span>', YWTM_Icon()->get_icon_data( $icon['icon'] ) );
                    break;
                case 'custom' :
                    $tab_icon = '<span class="ywtm_custom_icon" style="padding-right:10px;" ><img src="' . $icon['custom'] . '" style="max-width :27px;max-height: 25px;"/></span>';
                    break;
            }
        }


        return $tab_icon;
    }
}

if( !function_exists( 'ywtm_get_default_tab' ) ) {

    function ywtm_get_default_tab( $product_id )
    {

        global $post;

        $tabs = array();
        $product = wc_get_product( $product_id );
        // Description tab - shows product content
        if( $post->post_content ) {
            $tabs['description'] = array(
                'title' => __( 'Description', 'woocommerce' ),
                'priority' => 10,
                'callback' => 'woocommerce_product_description_tab'
            );
        }

        // Additional information tab - shows attributes
        if( $product && ( $product->has_attributes() || ( $product->enable_dimensions_display() && ( $product->has_dimensions() || $product->has_weight() ) ) ) ) {
            $tabs['additional_information'] = array(
                'title' => __( 'Additional Information', 'woocommerce' ),
                'priority' => 20,
                'callback' => 'woocommerce_product_additional_information_tab'
            );
        }

        // Reviews tab - shows comments
        if( comments_open() ) {
            $tabs['reviews'] = array(
                'title' => __( 'Reviews', 'woocommerce' ),
                'priority' => 30,
                'callback' => 'comments_template'
            );
        }
        return $tabs;
    }
}

if( !function_exists( 'ywtm_get_tab_ppl_language' ) ) {
    function ywtm_get_tab_ppl_language( $args )
    {
        
            global $post;

            if( isset( $post ) ) {
                $lang = pll_get_post_language( $post->ID );
                $args['lang'] = $lang;

            }
            return $args;
             
    }
}

if( ! function_exists( 'ywctab_json_search_product_categories') ) {

    function ywctab_json_search_product_categories( $x = '', $taxonomy_types = array('product_cat') ) {




        global $wpdb;
        $term = (string)urldecode(stripslashes(strip_tags($_GET['term'])));
        $term = "%" . $term . "%";


        $query_cat = $wpdb->prepare("SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name, {$wpdb->terms}.slug
                                   FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                                   WHERE {$wpdb->term_taxonomy}.taxonomy IN (%s) AND {$wpdb->terms}.name LIKE %s", implode(",", $taxonomy_types), $term);

        $product_categories = $wpdb->get_results($query_cat);

        $to_json = array();

        foreach ( $product_categories as $product_category ) {

            $to_json[$product_category->term_id] = "#" . $product_category->term_id . "-" . $product_category->name;
        }

        wp_send_json( $to_json );


    }
}
add_action('wp_ajax_yith_tab_manager_json_search_product_categories',  'ywctab_json_search_product_categories', 10);

 function ywtm_get_meta( $tab_id, $meta_key ){

    $value = get_post_meta( $tab_id, $meta_key, true );

    if( !empty( $value ) && !is_array( $value ) ){
        $value = explode(',',$value);
    }

    return $value;
}