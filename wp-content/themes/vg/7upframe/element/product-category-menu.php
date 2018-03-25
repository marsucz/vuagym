<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 31/08/15
 * Time: 10:00 AM
 */
/************************************Main Carousel*************************************/
if(class_exists("woocommerce")){
    if(!function_exists('sv_vc_category_menu'))
    {
        function sv_vc_category_menu($attr, $content = false)
        {
            $html = $css_class = '';
            extract(shortcode_atts(array(
                'style'      => '',
                'title'      => '',
                'link'      => '',
            ),$attr));
            switch ($style) {
                case 'wrap-cat-icon-hover wrap-cat-icon18':
                    $html .=    '<div class="wrap-cat-icon '.esc_attr($style).'">';
                    $html .=        '<h2 class="title14 title-cat-icon">';
                    $html .=        esc_html($title);
                    if(!empty($link)) $html .=    '<a href="'.esc_url($link).'">'.esc_html__("See All","kuteshop").'</a>';
                    $html .=        '</h2>';
                    $html .=        '<div class="wrap-list-cat-icon">
                                        <ul class="list-cat-icon">';
                    $html .=                wpb_js_remove_wpautop($content, false);
                    $html .=            '</ul>
                                    </div>';
                    $html .=    '</div>';
                    break;

                case 'wrap-cat-icon16':
                case 'wrap-cat-icon12':
                    $html .=    '<div class="wrap-cat-icon '.esc_attr($style).'">';
                    $html .=        '<h2 class="title14 title-cat-icon">';
                    $html .=        esc_html($title);
                    if(!empty($link)) $html .=    '<a href="'.esc_url($link).'">'.esc_html__("See All","kuteshop").'</a>';
                    $html .=        '</h2>';
                    $html .=        '<ul class="list-cat-icon">';
                    $html .=            wpb_js_remove_wpautop($content, false);
                    $html .=        '</ul>';
                    $html .=    '</div>';
                    break;

                case 'wrap-cat-icon-hover wrap-cat-icon6':
                case 'wrap-cat-icon-hover wrap-cat-icon5':
                case 'wrap-cat-icon10':
                case 'wrap-cat-icon9':
                case 'wrap-cat-icon8':
                case 'wrap-cat-icon1':
                case 'wrap-cat-icon17':
                case 'wrap-cat-icon2':
                    $html .=    '<div class="wrap-cat-icon '.esc_attr($style).'">';
                    $html .=    '<h2 class="title14 white bg-color title-cat-icon">';
                    $html .=        esc_html($title);
                    if(!empty($link)) $html .=    '<a href="'.esc_url($link).'">'.esc_html__("See All","kuteshop").'</a>';
                    $html .=    '</h2>';
                    $html .=        '<div class="wrap-list-cat-icon">
                                        <ul class="list-cat-icon">';
                    $html .=                wpb_js_remove_wpautop($content, false);
                    $html .=            '</ul>';
                    $html .=        '</div>
                                </div>';
                    break;
                
                default:
                    $html .=    '<div class="cat-icon4">
                                    <div class="wrap-cat-icon">
                                        <ul class="list-cat-icon">';
                    $html .=                wpb_js_remove_wpautop($content, false);
                    $html .=            '</ul>';
                    $html .=        '</div>';
                    $html .=    '</div>';
                    break;
            }            
            return $html;
        }
    }
    stp_reg_shortcode('sv_category_menu','sv_vc_category_menu');
    vc_map(
        array(
            'name'     => esc_html__( 'Product Category Menu', 'kuteshop' ),
            'base'     => 'sv_category_menu',
            'category' => esc_html__( '7Up-theme', 'kuteshop' ),
            'icon'     => 'icon-st',
            'as_parent' => array( 'only' => 'sv_category_item,sv_category_title_break_item' ),
            'content_element' => true,
            'js_view' => 'VcColumnView',
            'params'   => array(
                array(
                    'type'        => 'dropdown',
                    'heading'     => esc_html__( 'Style', 'kuteshop' ),
                    'param_name'  => 'style',
                    'value'       => array(
                        esc_html__( 'Default', 'kuteshop' )  => '',
                        esc_html__( 'Home 1', 'kuteshop' )  => 'wrap-cat-icon1',
                        esc_html__( 'Home 2', 'kuteshop' )  => 'wrap-cat-icon2',
                        esc_html__( 'Home 5', 'kuteshop' )  => 'wrap-cat-icon-hover wrap-cat-icon5',
                        esc_html__( 'Home 6', 'kuteshop' )  => 'wrap-cat-icon-hover wrap-cat-icon6',
                        esc_html__( 'Home 8', 'kuteshop' )  => 'wrap-cat-icon8',
                        esc_html__( 'Home 9', 'kuteshop' )  => 'wrap-cat-icon9',
                        esc_html__( 'Home 10', 'kuteshop' )  => 'wrap-cat-icon10',
                        esc_html__( 'Home 12', 'kuteshop' )  => 'wrap-cat-icon12',
                        esc_html__( 'Home 16', 'kuteshop' )  => 'wrap-cat-icon16',
                        esc_html__( 'Home 17', 'kuteshop' )  => 'wrap-cat-icon17',
                        esc_html__( 'Home 18', 'kuteshop' )  => 'wrap-cat-icon-hover wrap-cat-icon18',
                        ),
                ),                
                array(
                    'heading'     => esc_html__( 'Title', 'kuteshop' ),
                    'type'        => 'textfield',
                    'param_name'  => 'title',
                    "dependency"  => array(
                                        "element"   => 'style',
                                        "value"   => array('wrap-cat-icon-hover wrap-cat-icon18','wrap-cat-icon17','wrap-cat-icon16','wrap-cat-icon12','wrap-cat-icon10','wrap-cat-icon9','wrap-cat-icon8','wrap-cat-icon2','wrap-cat-icon1','wrap-cat-icon-hover wrap-cat-icon5','wrap-cat-icon-hover wrap-cat-icon6'),
                                        )
                ),
                array(
                    'heading'     => esc_html__( 'View link', 'kuteshop' ),
                    'type'        => 'textfield',
                    'param_name'  => 'link',
                    "dependency"  => array(
                                        "element"   => 'style',
                                        "value"   => array('wrap-cat-icon9'),
                                        )
                ), 
            )
        )
    );

    /*******************************************END MAIN*****************************************/

    /**************************************BEGIN ITEM************************************/
    //Banner item Frontend
    if(!function_exists('sv_vc_category_title_break_item'))
    {
        function sv_vc_category_title_break_item($attr, $content = false)
        {
            $html = $content_hover = $el_class = '';
            extract(shortcode_atts(array(
                'title'          => '',
            ),$attr));
            $html .=    '</ul>
                        <h2 class="title14 title-cat-icon">'.esc_html($title).'</h2>
                        <ul class="list-cat-icon">';
            return $html;
        }
    }
    stp_reg_shortcode('sv_category_title_break_item','sv_vc_category_title_break_item');

    // Banner item
    vc_map(
        array(
            'name'     => esc_html__( 'Title Break Item', 'kuteshop' ),
            'base'     => 'sv_category_title_break_item',
            'icon'     => 'icon-st',
            'content_element' => true,
            'as_child' => array('only' => 'sv_category_menu'),
            'params'   => array(
                array(
                    'type'        => 'textfield',
                    'holder'      => 'h4',
                    'heading'     => esc_html__( 'Title', 'kuteshop' ),
                    'param_name'  => 'title',
                ),
            )
        )
    );


    /**************************************BEGIN ITEM************************************/
    //Banner item Frontend
    if(!function_exists('sv_vc_category_item'))
    {
        function sv_vc_category_item($attr, $content = false)
        {
            $html = $content_hover = $el_class = '';
            extract(shortcode_atts(array(
                'cat'          => '',
                'icon'          => '',
                'mega_content'  => '',
                'page_id'       => '',
                'title'       => '',
                'link'       => '',
            ),$attr));
            if(!empty($cat)) $cat = str_replace(' ', '', $cat);
            // if(!empty($cat)){
                switch ($mega_content) {
                    case 'page':
                        $content_hover .=    '<div class="cat-mega-menu cat-mega-style1">';
                        $content_hover .=       balanceTags(S7upf_Template::get_vc_pagecontent($page_id));
                        $content_hover .=    '</div>';
                        break;
                    
                    case 'editor':
                        $content_hover .=    '<div class="cat-mega-menu cat-mega-style1">';
                        $content_hover .=       wpb_js_remove_wpautop($content, true);
                        $content_hover .=    '</div>';
                        break;
                    
                    case 'featured':
                        $content_hover .=       '<div class="cat-mega-menu cat-mega-style2">
                                                    <h2 class="title-cat-mega-menu">'.esc_html__("Special products","kuteshop").'</h2>
                                                    <div class="row">';
                        $args = array(
                            'post_type'         => 'product',
                            'posts_per_page'    => 3,
                            );
                        $args['tax_query'][] = array(
                            'taxonomy' => 'product_visibility',
                            'field'    => 'name',
                            'terms'    => 'featured',
                            'operator' => 'IN',
                        );
                        if(!empty($cat)){
                            $args['tax_query'][]=array(
                                'taxonomy'  => 'product_cat',
                                'field'     => 'slug',
                                'terms'     => $cat
                            );
                        }
                        $query = new WP_Query($args);
                        if($query->have_posts()) {
                            while($query->have_posts()) {
                                $query->the_post();
                                global $product;
                                $content_hover .=       s7upf_product_item(
                                                            'item-product-ajax first-item',
                                                            3,
                                                            '',
                                                            '',
                                                            '',
                                                            array(
                                                                'quickview'     => array(
                                                                    'status'    => 'show',
                                                                    'pos'       => 'pos-top',
                                                                    'style'     => 'plus',
                                                                    ),
                                                                'extra-link'    => array(
                                                                    'status'    => 'show',
                                                                    'style'     => '',
                                                                    )
                                                                ),
                                                            array(300,300),
                                                            '',
                                                            '',
                                                            'show'
                                                        );
                            }
                        }
                        $content_hover .=           '</div>
                                                </div>';
                        wp_reset_postdata();
                        break;

                    default:
                        # code...
                        break;
                }
                if(!empty($content_hover)) $el_class = 'has-cat-mega';
                $term = get_term_by( 'slug',$cat, 'product_cat' );
                $term_link = $term_title = '';
                if(!empty($term) && is_object($term)){
                    $term_link = get_term_link( $term->term_id, 'product_cat' );
                    $term_title = $term->name;
                }
                if(!empty($link)) $term_link = $link;
                if(!empty($title)) $term_title = $title;
                if(!empty($term_title) && !empty($term_link)){
                    $html .=    '<li class="'.$el_class.'">
                                    <a href="'.esc_url($term_link).'">'.$term_title.'
                                        '.wp_get_attachment_image($icon,'full').'
                                    </a>
                                    '.$content_hover.'
                                </li>';
                }
            // }
            return $html;
        }
    }
    stp_reg_shortcode('sv_category_item','sv_vc_category_item');

    // Banner item
    if(isset($_GET['return'])) $check_add = $_GET['return'];
    if(empty($check_add)) add_action( 'vc_before_init_base','sv_admin_category_item',10,100 );
    if ( ! function_exists( 'sv_admin_category_item' ) ) {
        function sv_admin_category_item(){
            vc_map(
                array(
                    'name'     => esc_html__( 'Category Item', 'kuteshop' ),
                    'base'     => 'sv_category_item',
                    'icon'     => 'icon-st',
                    'content_element' => true,
                    'as_child' => array('only' => 'sv_category_menu'),
                    'params'   => array(
                        array(
                            'holder'     => 'div',
                            'heading'     => esc_html__( 'Product Categories', 'kuteshop' ),
                            'type'        => 'autocomplete',
                            'param_name'  => 'cat',
                            'settings' => array(
                                'multiple' => false,
                                'sortable' => false,
                                'values' => s7upf_get_product_taxonomy(),
                            ),
                            'save_always' => true,
                            'description' => esc_html__( 'List of product categories', 'kuteshop' ),
                        ),                        
                        array(
                            'type'        => 'textfield',
                            'heading'     => esc_html__( 'Custom title', 'kuteshop' ),
                            'param_name'  => 'title',
                        ),
                        array(
                            'type'        => 'textfield',
                            'heading'     => esc_html__( 'Custom Link', 'kuteshop' ),
                            'param_name'  => 'link',
                        ),
                        array(
                            'type'        => 'attach_image',
                            'heading'     => esc_html__( 'Icon image', 'kuteshop' ),
                            'param_name'  => 'icon',
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__( 'Content hover', 'kuteshop' ),
                            'param_name'  => 'mega_content',
                            'value'       => array(
                                esc_html__( 'None', 'kuteshop' )             => '',
                                esc_html__( 'Content page', 'kuteshop' )     => 'page',
                                esc_html__( 'Content Editor', 'kuteshop' )   => 'editor',
                                esc_html__( 'Featured Product', 'kuteshop' ) => 'featured',
                                ),
                        ),
                        array(
                            'type'        => 'dropdown',
                            'heading'     => esc_html__( 'Choose page content', 'kuteshop' ),
                            'param_name'  => 'page_id',
                            'value'       => s7upf_list_all_page(),
                            'dependency'  => array(
                                'element'   => 'mega_content',
                                'value'   => 'page',
                                )
                        ),
                        array(
                            'type'        => 'textarea_html',
                            'heading'     => esc_html__( 'Content', 'kuteshop' ),
                            'param_name'  => 'content',
                            'dependency'  => array(
                                'element'   => 'mega_content',
                                'value'   => 'editor',
                                )
                        ),
                    )
                )
            );
        }
    }

    /**************************************END ITEM************************************/

    //Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
    if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
        class WPBakeryShortCode_Sv_Category_Menu extends WPBakeryShortCodesContainer {}
    }
    if ( class_exists( 'WPBakeryShortCode' ) ) {
        class WPBakeryShortCode_Sv_Category_Item extends WPBakeryShortCode {}
        class WPBakeryShortCode_Sv_Title_Break_Item extends WPBakeryShortCode {}
    }
}