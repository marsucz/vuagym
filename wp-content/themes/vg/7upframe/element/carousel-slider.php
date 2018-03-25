<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 31/08/15
 * Time: 10:00 AM
 */
/************************************Main Carousel*************************************/
if(!function_exists('s7upf_vc_slide_carousel'))
{
    function s7upf_vc_slide_carousel($attr, $content = false)
    {
        $html = $css_class = '';
        extract(shortcode_atts(array(
            'item'      => '1',
            'speed'     => '',
            'itemres'   => '',
            'nav_slider'=> 'nav-hidden',
            'animation' => '',
            'custom_css' => '',
            'banner_bg' => '',
            'el_class' => '',
        ),$attr));
        if(!empty($custom_css)) $css_class = vc_shortcode_custom_css_class( $custom_css );
            $html .=    '<div class="'.esc_attr($nav_slider.' '.$banner_bg.' '.$el_class).'">';
            $html .=        '<div class="wrap-item sv-slider '.$css_class.'" data-item="'.$item.'" data-speed="'.$speed.'" data-itemres="'.$itemres.'" data-animation="'.$animation.'" data-nav="'.$nav_slider.'" data-prev="'.esc_attr__("Prev","kuteshop").'" data-next="'.esc_attr__("Next","kuteshop").'">';
            $html .=            wpb_js_remove_wpautop($content, false);
            $html .=        '</div>';
            $html .=    '</div>';
        return $html;
    }
}
stp_reg_shortcode('slide_carousel','s7upf_vc_slide_carousel');
vc_map(
    array(
        'name'     => esc_html__( 'Carousel Slider', 'kuteshop' ),
        'base'     => 'slide_carousel',
        'category' => esc_html__( '7Up-theme', 'kuteshop' ),
        'icon'     => 'icon-st',
        'as_parent' => array( 'only' => 'vc_column_text,vc_single_image,slide_banner_item,slide_cat_item,slide_service_item,slide_testimonial_item,sv_product_list_basic' ),
        'content_element' => true,
        'js_view' => 'VcColumnView',
        'params'   => array(                       
            array(
                'heading'     => esc_html__( 'Item slider display', 'kuteshop' ),
                'type'        => 'textfield',
                'description' => esc_html__( 'Enter number of item. Default is 1.', 'kuteshop' ),
                'param_name'  => 'item',
            ),
            array(
                'heading'     => esc_html__( 'Speed', 'kuteshop' ),
                'type'        => 'textfield',
                'description' => esc_html__( 'Enter time slider go to next item. Unit (ms). Example 5000. If empty this field autoPlay is false.', 'kuteshop' ),
                'param_name'  => 'speed',
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => esc_html__( 'Navigation style', 'kuteshop' ),
                'param_name'  => 'nav_slider',
                'value'       => array(
                    esc_html__( 'Hidden', 'kuteshop' )                  => 'nav-hidden',
                    esc_html__( 'Default Navigation', 'kuteshop' )      => 'banner-slider4',
                    esc_html__( 'Navigation home 1', 'kuteshop' )      => 'banner-slider1',
                    esc_html__( 'Navigation home 2', 'kuteshop' )      => 'banner-slider2',
                    esc_html__( 'Navigation home 3', 'kuteshop' )      => 'banner-slider3',
                    esc_html__( 'Navigation Radius', 'kuteshop' )       => 'hot-cat-slider',
                    esc_html__( 'More category style', 'kuteshop' )     => 'content-cat-color-more',
                    esc_html__( 'Left Top Navigation', 'kuteshop' )     => 'hotcat-slider2',
                    esc_html__( 'Left Top Navigation 6', 'kuteshop' )     => 'hotcat-slider2 hotcat-slider6',
                    esc_html__( 'Navigation arrow home 3', 'kuteshop' )     => 'arrow-style3',
                    esc_html__( 'Navigation small 1', 'kuteshop' )     => 'sub-banner-slider',
                    esc_html__( 'Pagination Testimonial', 'kuteshop' )     => 'testimo-slider',
                    esc_html__( 'Navigation home 5', 'kuteshop' )      => 'banner-slider banner-slider5',
                    esc_html__( 'Navigation home 6', 'kuteshop' )      => 'banner-slider banner-slider5 banner-slider6',
                    esc_html__( 'Navigation home 7', 'kuteshop' )      => 'banner-slider banner-slider7',
                    esc_html__( 'Navigation home 8', 'kuteshop' )      => 'banner-slider banner-slider8',
                    esc_html__( 'Navigation home 9', 'kuteshop' )      => 'banner-slider banner-slider9',
                    esc_html__( 'Navigation home 10', 'kuteshop' )      => 'banner-slider banner-slider10',
                    esc_html__( 'Right Top Navigation 10', 'kuteshop' )      => 'cat-slider10',
                    esc_html__( 'Navigation brand 10', 'kuteshop' )      => 'brand-slider10',
                    esc_html__( 'Navigation home 11', 'kuteshop' )      => 'banner-slider banner-slider11',
                    esc_html__( 'Pagination home 11', 'kuteshop' )      => 'superdeal-slider11',
                    esc_html__( 'Navigation home 12', 'kuteshop' )      => 'banner-slider banner-slider12',
                    esc_html__( 'Navigation home 13', 'kuteshop' )      => 'banner-slider banner-slider13',
                    esc_html__( 'Navigation home 14', 'kuteshop' )      => 'banner-slider banner-slider14',
                    esc_html__( 'Pagination home 14', 'kuteshop' )      => 'testimo-slider14',
                    esc_html__( 'Navigation home 15', 'kuteshop' )      => 'banner-slider banner-slider15',
                    esc_html__( 'Navigation home 16', 'kuteshop' )      => 'banner-slider banner-slider16',
                    esc_html__( 'Navigation home 18', 'kuteshop' )      => 'banner-slider banner-slider18 poly-slider',
                ),
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => esc_html__( 'Image style', 'kuteshop' ),
                'param_name'  => 'banner_bg',
                'value'       => array(
                    esc_html__( 'Default', 'kuteshop' )                  => '',
                    esc_html__( 'Banner Background', 'kuteshop' )      => 'bg-slider',
                ),
            ),
            array(
                'heading'     => esc_html__( 'Custom Item', 'kuteshop' ),
                'type'        => 'textfield',
                'description'   => esc_html__( 'Enter item for screen width(px) format is width:value and separate values by ",". Example is 0:2,600:3,1000:4. Default is auto.', 'kuteshop' ),
                'param_name'  => 'itemres',
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => esc_html__( 'Slider Animation', 'kuteshop' ),
                'param_name'  => 'animation',
                'value'       => array(
                    esc_html__( 'None', 'kuteshop' )        => '',
                    esc_html__( 'Fade', 'kuteshop' )        => 'fade',
                    esc_html__( 'BackSlide', 'kuteshop' )   => 'backSlide',
                    esc_html__( 'GoDown', 'kuteshop' )      => 'goDown',
                    esc_html__( 'FadeUp', 'kuteshop' )      => 'fadeUp',
                    )
            ),
            array(
                "type"          => "textfield",
                "heading"       => esc_html__("Add custom Class",'kuteshop'),
                "param_name"    => "el_class",
            ),
            array(
                "type"          => "css_editor",
                "heading"       => esc_html__("Custom Block",'kuteshop'),
                "param_name"    => "custom_css",
                'group'         => esc_html__('Advanced','kuteshop')
            )
        )
    )
);

/*******************************************END MAIN*****************************************/


/**************************************BEGIN ITEM************************************/
//Banner item Frontend
if(!function_exists('s7upf_vc_slide_banner_item'))
{
    function s7upf_vc_slide_banner_item($attr, $content = false)
    {
        $html = $view_html = $view_html2 = '';
        extract(shortcode_atts(array(
            'style'         => 'item-banner4',
            'image'         => '',
            'link'          => '',            
            'thumb_animation' => '',
            'info_animation' => '',
            'info_align' => '',
            'info_color' => '',
            'info_el_class' => '',
        ),$attr));
        if(!empty($image)){
            $thumb_class = $info_class = '';
            if(!empty($thumb_animation)) $thumb_class = 'animated';
            if(!empty($info_animation)) $info_class = 'animated';
            switch ($style) {
                case 'item-banner14':
                    $html .=    '<div class="item-banner">
                                    <div class="banner-thumb '.esc_attr($thumb_class).'" data-animated="'.esc_attr($thumb_animation).'">
                                        <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,'full').'</a>
                                    </div>';
                    if(!empty($content)){
                    $html .=        '<div class="banner-info '.esc_attr($info_color).' '.esc_attr($info_align).' '.esc_attr($info_class).' '.esc_attr($info_el_class).'" data-animated="'.esc_attr($info_animation).'">
                                        <div class="inner-banner-info">
                                            '.wpb_js_remove_wpautop($content, true).'
                                        </div>
                                    </div>';
                                }
                    $html .=    '</div>';
                    break;

                case 'item-banner7':
                case 'item-banner5':
                    $html .=    '<div class="'.esc_attr($style).'">
                                    <div class="banner-thumb '.esc_attr($thumb_class).'" data-animated="'.esc_attr($thumb_animation).'">
                                        <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,'full').'</a>
                                    </div>';
                    if(!empty($content)){
                    $html .=        '<div class="banner-info '.esc_attr($info_color).' '.esc_attr($info_align).' '.esc_attr($info_class).' '.esc_attr($info_el_class).'" data-animated="'.esc_attr($info_animation).'">
                                        '.wpb_js_remove_wpautop($content, true).'
                                    </div>';
                                }
                    $html .=    '</div>';
                    break;

                case 'item-df':
                case 'item-banner1':
                    $html .=    '<div class="item-banner '.esc_attr($style).'">
                                    <div class="banner-thumb '.esc_attr($thumb_class).'" data-animated="'.esc_attr($thumb_animation).'">
                                        <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,'full').'</a>
                                    </div>';
                    if(!empty($content)){
                    $html .=        '<div class="banner-info '.esc_attr($info_color).' '.esc_attr($info_align).' '.esc_attr($info_class).' '.esc_attr($info_el_class).'" data-animated="'.esc_attr($info_animation).'">
                                        '.wpb_js_remove_wpautop($content, true).'
                                    </div>';
                                }
                    $html .=    '</div>';
                    break;

                case 'home3-right':
                    $html .=    '<div class="item-bn '.esc_attr($style).'">
                                    <div class="banner-thumb '.esc_attr($thumb_class).'" data-animated="'.esc_attr($thumb_animation).'">
                                        <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,'full').'</a>
                                    </div>';
                    if(!empty($content)){
                    $html .=        '<div class="banner-info style2 color-dark '.esc_attr($info_color).' '.esc_attr($info_align).' '.esc_attr($info_class).' '.esc_attr($info_el_class).'" data-animated="'.esc_attr($info_animation).'">
                                        <div class="inner-banner-info">
                                            '.wpb_js_remove_wpautop($content, true).'
                                        </div>
                                    </div>';
                                }
                    $html .=    '</div>';
                    break;
                
                default:
                    $html .=    '<div class="item-bn '.esc_attr($style).'">
                                    <div class="banner-thumb '.esc_attr($thumb_class).'" data-animated="'.esc_attr($thumb_animation).'">
                                        <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,'full').'</a>
                                    </div>';
                    if(!empty($content)){
                    $html .=        '<div class="banner-info '.esc_attr($info_color).' '.esc_attr($info_align).' '.esc_attr($info_class).' '.esc_attr($info_el_class).'" data-animated="'.esc_attr($info_animation).'">
                                        <div class="inner-banner-info">
                                            '.wpb_js_remove_wpautop($content, true).'
                                        </div>
                                    </div>';
                                }
                    $html .=    '</div>';
                    break;
            }            
        }
        return $html;
    }
}
stp_reg_shortcode('slide_banner_item','s7upf_vc_slide_banner_item');

// Banner item
vc_map(
    array(
        'name'     => esc_html__( 'Banner Item', 'kuteshop' ),
        'base'     => 'slide_banner_item',
        'icon'     => 'icon-st',
        'content_element' => true,
        'as_child' => array('only' => 'slide_carousel'),
        'params'   => array(
            array(
                'type'        => 'dropdown',
                'heading'     => esc_html__( 'Style', 'kuteshop' ),
                'param_name'  => 'style',
                'value'       => array(
                    esc_html__( 'Default', 'kuteshop' ) => 'item-banner4',
                    esc_html__( 'Default 2', 'kuteshop' ) => 'item-df',
                    esc_html__( 'Home 1', 'kuteshop' ) => 'item-banner1',
                    esc_html__( 'Home 2(3)', 'kuteshop' ) => 'item-banner',
                    esc_html__( 'Home 3 right', 'kuteshop' ) => 'home3-right',
                    esc_html__( 'Home 5', 'kuteshop' ) => 'item-banner5',
                    esc_html__( 'Home 7', 'kuteshop' ) => 'item-banner7',
                    esc_html__( 'Home 14', 'kuteshop' ) => 'item-banner14',
                    )
            ),
            array(
                'type'        => 'attach_image',
                'heading'     => esc_html__( 'Image', 'kuteshop' ),
                'param_name'  => 'image',
            ),
            array(
                'type'        => 'textfield',
                'heading'     => esc_html__( 'Link Banner', 'kuteshop' ),
                'param_name'  => 'link',
            ),            
            array(
                    'type' => 'dropdown',
                    'heading' => esc_html__( 'Image Animation', 'kuteshop' ),
                    'param_name' => 'thumb_animation',
                    'value' => s7upf_get_list_animation()
                ),
            array(
                    'type' => 'dropdown',
                    'heading' => esc_html__( 'Info Animation', 'kuteshop' ),
                    'param_name' => 'info_animation',
                    'value' => s7upf_get_list_animation()
                ),
            array(
                'type'        => 'dropdown',
                'heading'     => esc_html__( 'Info Align', 'kuteshop' ),
                'param_name'  => 'info_align',
                'value'       => array(
                    esc_html__( 'None', 'kuteshop' ) => '',
                    esc_html__( 'Left', 'kuteshop' ) => 'text-left',
                    esc_html__( 'Right', 'kuteshop' ) => 'text-right',
                    esc_html__( 'Center', 'kuteshop' ) => 'text-center',
                    )
            ),
            array(
                'type'        => 'dropdown',
                'heading'     => esc_html__( 'Info Color', 'kuteshop' ),
                'param_name'  => 'info_color',
                'value'       => array(
                    esc_html__( 'Default', 'kuteshop' ) => '',
                    esc_html__( 'White', 'kuteshop' ) => 'white',
                    )
            ),
            array(
                'type'        => 'textfield',
                'heading'     => esc_html__( 'Info Extra Class', 'kuteshop' ),
                'param_name'  => 'info_el_class',
            ),  
            array(
                "type" => "textarea_html",
                "holder" => "div",
                "heading" => esc_html__("Content",'kuteshop'),
                "param_name" => "content",
            ),
        )
    )
);

/**************************************END ITEM************************************/


/**************************************BEGIN ITEM************************************/
//Banner item Frontend
if(!function_exists('s7upf_vc_slide_cat_item'))
{
    function s7upf_vc_slide_cat_item($attr, $content = false)
    {
        $html = $term_link = $term_title = '';
        extract(shortcode_atts(array(
            'style'           => '',
            'cat'           => '',
            'image'         => '',
            'size'         => '',
            'title'         => '',
            'link'          => '',
            'price'         => '',
        ),$attr));
        if(!empty($cat)) $cat = str_replace(' ', '', $cat);
        $term = get_term_by( 'slug',$cat, 'product_cat' );
        if(!empty($term) && is_object($term)){
            $term_link = get_term_link( $term->term_id, 'product_cat' );
            $term_title = $term->name;
        }
        if(!empty($link)) $term_link = $link;
        if(!empty($title)) $term_title = $title;
        if(!empty($size)) $size = explode('x', $size);
        switch ($style) {
            case 'cat-adv16':
                if(empty($size)) $size = 'full';
                $html .=    '<div class="item-adv16">
                                <div class="product-thumb">
                                    <a href="'.esc_url($term_link).'" class="product-thumb-link">'.wp_get_attachment_image($image,$size).'</a>
                                </div>
                                <h3 class="product-title"><a href="'.esc_url($term_link).'">'.esc_html($term_title).'</a></h3>
                            </div>';
                break;

            case 'cat-adv14':
                if(empty($size)) $size = 'full';
                $html .=    '<div class="item-popcat14 item-deal14">
                                <h2 class="title14">'.esc_html($term_title).'</h2>
                                <div class="banner-box">
                                    <a href="'.esc_url($term_link).'" class="link-banner-box">'.wp_get_attachment_image($image,$size).'</a>
                                    <div class="info-banner-hotdeal text-uppercase white">
                                        '.wpb_js_remove_wpautop($content, true).'
                                    </div>
                                </div>
                            </div>';
                break;

            case 'cat-list-link14':
                if(empty($size)) $size = 'full';
                $html .=    '<div class="item-popcat14">
                                <h2 class="title14">'.esc_html($term_title).'</h2>
                                <div class="product-thumb">
                                    <a href="'.esc_url($term_link).'" class="product-thumb-link">
                                        '.wp_get_attachment_image($image,$size).'
                                    </a>
                                </div>
                                '.wpb_js_remove_wpautop($content, true).'
                            </div>';
                break;

            case 'cat-home10':
                if(empty($size)) $size = 'full';
                $html .=    '<div class="item-cat10">
                                <div class="cat-thumb10">
                                    <a href="'.esc_url($term_link).'">
                                        '.wp_get_attachment_image($image,$size).'
                                    </a>
                                </div>
                                <a href="'.esc_url($term_link).'">'.esc_html($term_title).'</a>
                            </div>';
                break;

            case 'cat-list-link5':
                if(empty($size)) $size = 'full';
                $html .=    '<div class="item-more5">
                                <div class="item-morecat5">
                                    <div class="product-thumb">
                                        <a href="'.esc_url($term_link).'" class="product-thumb-link">
                                            '.wp_get_attachment_image($image,$size).'
                                        </a>
                                    </div>
                                    <div class="morecat-info5">
                                        <h2 class="title14">'.esc_html($term_title).'</h2>
                                        '.wpb_js_remove_wpautop($content, true).'
                                    </div>
                                </div>
                            </div>';
                break;

            case 'cat-list-link3':
                if(empty($size)) $size = 'full';
                $html .=    '<div class="item-popcat3">
                                <div class="cat-thumb">
                                    <a href="'.esc_url($term_link).'">'.wp_get_attachment_image($image,$size).'</a>
                                </div>
                                <span class="white bg-color text-uppercase">'.esc_html($term_title).'</span>
                                '.wpb_js_remove_wpautop($content, true).'
                            </div>';
                break;

            case 'cat-list-link2':
                if(empty($size)) $size = 'full';
                $html .=    '<div class="item-hotcat2 radius">
                                <h2 class="title14">'.esc_html($term_title).'</h2>
                                <a href="'.esc_url($term_link).'" class="viewmore">'.esc_html__("view more","kuteshop").'</a>
                                <div class="product-thumb">
                                    <a class="product-thumb-link" href="'.esc_url($term_link).'">
                                        '.wp_get_attachment_image($image,$size).'
                                    </a>
                                </div>
                                '.wpb_js_remove_wpautop($content, true).'
                            </div>';
                break;

            case 'cat-list-link':
                if(empty($size)) $size = 'full';
                $html .=    '<div class="item-cat-color-more">
                                <div class="cat-thumb">
                                    <a href="'.esc_url($term_link).'">'.wp_get_attachment_image($image,$size).'</a>
                                </div>
                                <h2 class="title18"><a href="'.esc_url($term_link).'">'.esc_html($term_title).'</a></h2>
                                '.wpb_js_remove_wpautop($content, true).'
                            </div>';
                break;
            
            default:
                if(empty($size)) $size = array(100,100);
                $html .=    '<div class="item-hot-cat">
                                <div class="hot-cat-info">
                                    <h3 class="product-title"><a href="'.esc_url($term_link).'">'.esc_html($term_title).'</a></h3>';
                if(!empty($price)) $html .=    '<p class="price-from">'.esc_html__("from:","kuteshop").'<span>'.esc_html($price).'</span></p>';
                $html .=    '</div>
                                <div class="hot-cat-thumb">'.wp_get_attachment_image($image,$size).'<a href="'.esc_url($term_link).'" class="shopnow"><span>'.esc_html__("shop now","kuteshop").'</span></a></div>
                            </div>';
                break;
        }
        return $html;
    }
}
stp_reg_shortcode('slide_cat_item','s7upf_vc_slide_cat_item');

// Banner item
if(isset($_GET['return'])) $check_add = $_GET['return'];
if(empty($check_add)) add_action( 'vc_before_init_base','sv_admin_category_product_item',10,100 );
if ( ! function_exists( 'sv_admin_category_product_item' ) ) {
    function sv_admin_category_product_item(){
        vc_map(
            array(
                'name'     => esc_html__( 'Product category Item', 'kuteshop' ),
                'base'     => 'slide_cat_item',
                'icon'     => 'icon-st',
                'content_element' => true,
                'as_child' => array('only' => 'slide_carousel'),
                'params'   => array(
                    array(
                        'type'        => 'dropdown',
                        'holder'      => 'div',
                        'heading'     => esc_html__( 'Style', 'kuteshop' ),
                        'param_name'  => 'style',
                        'value'       => array(
                            esc_html__( 'Price Category', 'kuteshop' )              => 'price-cat',
                            esc_html__( 'Category with list links', 'kuteshop' )     => 'cat-list-link',
                            esc_html__( 'Category with list links(home 2)', 'kuteshop' )     => 'cat-list-link2',
                            esc_html__( 'Category with list links(home 3)', 'kuteshop' )     => 'cat-list-link3',
                            esc_html__( 'Category with list links(home 5)', 'kuteshop' )     => 'cat-list-link5',
                            esc_html__( 'Category home 10', 'kuteshop' )     => 'cat-home10',
                            esc_html__( 'Category with list links(home 14)', 'kuteshop' )     => 'cat-list-link14',
                            esc_html__( 'Category adv home 14', 'kuteshop' )     => 'cat-adv14',
                            esc_html__( 'Category adv home 16', 'kuteshop' )     => 'cat-adv16',
                            ),
                    ),
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
                        'type'        => 'attach_image',
                        'heading'     => esc_html__( 'Image', 'kuteshop' ),
                        'param_name'  => 'image',
                    ),
                    array(
                        "type"          => "textfield",
                        "heading"       => esc_html__("Image Size",'kuteshop'),
                        "param_name"    => "size",
                        'description'   => esc_html__( 'Enter site thumbnail to crop. [width]x[height]. Example is 300x300', 'kuteshop' ),
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Price', 'kuteshop' ),
                        'param_name'  => 'price',
                        'dependency'  => array(
                                'element'   => 'style',
                                'value'   => 'price-cat',
                            )
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
                        'type'        => 'textarea_html',
                        'heading'     => esc_html__( 'Content', 'kuteshop' ),
                        'param_name'  => 'content',
                        'dependency'  => array(
                                'element'   => 'style',
                                'value'   => array('cat-list-link14','cat-list-link','cat-list-link2','cat-list-link3','cat-list-link5'),
                            )
                    ),
                )
            )
        );
    }
}

/**************************************END ITEM************************************/

/**************************************BEGIN ITEM************************************/
//Banner item Frontend
if(!function_exists('s7upf_vc_slide_service_item'))
{
    function s7upf_vc_slide_service_item($attr, $content = false)
    {
        $html = $term_link = $term_title = '';
        extract(shortcode_atts(array(
            'style'           => '',
            'image'         => '',
            'title'         => '',
            'price'         => '',
            'link'          => '',
            'des'         => '',
            'color'         => '',
        ),$attr));        
        switch ($style) {
            case 'adv-home9':
                $html .=    '<div class="item-adv9">
                                <div class="adv-thumb">
                                    <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,'full').'</a>
                                </div>
                                <div class="adv-info9">
                                    <div class="product-price">
                                        <label>'.esc_html__("From","kuteshop").'</label>
                                        <ins><span>'.esc_html($price).'</span></ins>
                                    </div>
                                    <h3 class="product-title"><a href="'.esc_url($link).'">'.esc_html($title).'</a></h3>
                                    <p>'.esc_html($des).'</p>
                                    <a href="'.esc_url($link).'" class="shopnow">'.esc_html__("shop now","kuteshop").' <i class="fa fa-hand-o-right" aria-hidden="true"></i></a>
                                </div>
                            </div>';
                break;

            case 'item-sub-banner':
                $html .=    '<div class="item-sub-banner">
                                <div class="product-thumb">
                                    <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,'full',0,array('class'=>'wobble-horizontal')).'</a>
                                </div>
                                <div class="product-info">
                                    <h3 class="product-title text-capitalize"><a href="'.esc_url($link).'">'.esc_html($title).'</a></h3>
                                    <strong class="color">'.esc_html($des).'</strong>
                                </div>
                            </div>';
                break;

            case 'item-coupon':
                if(!empty($color)) $color = S7upf_Assets::build_css('background-color:'.$color);
                $html .=    '<div class="item-coupon">
                                <a href="'.esc_url($link).'" class="logo-coupon">'.wp_get_attachment_image($image,'full').'</a>
                                <p>'.esc_html($title).' </p>
                                <a href="'.esc_url($link).'" class="btn-coupon wobble-horizontal '.esc_attr($color).'">'.esc_html__("Get this Coupon","kuteshop").'</a>
                            </div>';
                break;
            
            default:
                $html .=    '<div class="item-service3 white">
                                <a class="service-icon wobble-horizontal" href="'.esc_url($link).'">'.wp_get_attachment_image($image,'full').'</a>
                                <h2 class="title14"><a href="'.esc_url($link).'">'.esc_html($title).'</a></h2>
                                <p>'.esc_html($des).'</p>
                            </div>';
                break;
        }
        return $html;
    }
}
stp_reg_shortcode('slide_service_item','s7upf_vc_slide_service_item');

// Banner item

vc_map(
    array(
        'name'     => esc_html__( 'Product Service Item', 'kuteshop' ),
        'base'     => 'slide_service_item',
        'icon'     => 'icon-st',
        'content_element' => true,
        'as_child' => array('only' => 'slide_carousel'),
        'params'   => array(
            array(
                'type'        => 'dropdown',
                'holder'      => 'div',
                'heading'     => esc_html__( 'Style', 'kuteshop' ),
                'param_name'  => 'style',
                'value'       => array(
                    esc_html__( 'Home 3', 'kuteshop' )              => '',
                    esc_html__( 'Item coupon(3)', 'kuteshop' )              => 'item-coupon',
                    esc_html__( 'Advantage home 1', 'kuteshop' )              => 'item-sub-banner',
                    esc_html__( 'Advantage home 9', 'kuteshop' )              => 'adv-home9',
                    ),
            ),
            array(
                'type'        => 'attach_image',
                'heading'     => esc_html__( 'Image', 'kuteshop' ),
                'param_name'  => 'image',
            ),
            array(
                'type'        => 'textfield',
                'heading'     => esc_html__( 'Price', 'kuteshop' ),
                'param_name'  => 'price',
                'dependency'  => array(
                    'element'   => 'style',
                    'value'   => array('adv-home9'),
                    )
            ),
            array(
                'type'        => 'textfield',
                'holder'      => 'div',
                'heading'     => esc_html__( 'Title', 'kuteshop' ),
                'param_name'  => 'title',
            ),
            array(
                'type'        => 'textarea',
                'heading'     => esc_html__( 'Description', 'kuteshop' ),
                'param_name'  => 'des',
                'dependency'  => array(
                    'element'   => 'style',
                    'value'   => array('','item-sub-banner','adv-home9'),
                    )
            ),
            array(
                'type'        => 'textfield',
                'heading'     => esc_html__( 'Link', 'kuteshop' ),
                'param_name'  => 'link',
            ),
            array(
                'type'        => 'colorpicker',
                'heading'     => esc_html__( 'Color button', 'kuteshop' ),
                'param_name'  => 'color',
                'dependency'  => array(
                    'element'   => 'style',
                    'value'   => 'item-coupon',
                    )
            ),
        )
    )
);

/**************************************END ITEM************************************/

/**************************************BEGIN ITEM************************************/
//Banner item Frontend
if(!function_exists('s7upf_vc_slide_testimonial_item'))
{
    function s7upf_vc_slide_testimonial_item($attr, $content = false)
    {
        $html = $term_link = $term_title = '';
        extract(shortcode_atts(array(
            'style'         => '',
            'image'         => '',
            'title'         => '',
            'link'          => '',
            'des'          => '',
            'time'          => '',
        ),$attr));
        switch ($style) {
            case 'home-14':
                $html .=    '<div class="item-testimo14">
                                <a href="'.esc_url($link).'"><strong>'.esc_html($title).'</strong></a>
                                <p class="desc">'.esc_html($des).'</p>
                                <span>'.esc_html($time).'</span>
                            </div>';
                break;
            
            default:
                $html .=    '<div class="item-testimo">
                                <div class="testimo-thumb">
                                    <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,'full').'</a>
                                </div>
                                <div class="testimo-info">';
                if(!empty($title)) $html .= '<h2 class="title14 color">'.esc_html($title).'</h2>';
                $html .=            '<p class="desc">'.esc_html($des).'</p>
                                </div>
                            </div>';
                break;
        }        
        return $html;
    }
}
stp_reg_shortcode('slide_testimonial_item','s7upf_vc_slide_testimonial_item');

// Banner item
add_action( 'vc_before_init_base','sv_admin_testimonial_item',10,100 );
if ( ! function_exists( 'sv_admin_testimonial_item' ) ) {
    function sv_admin_testimonial_item(){
        vc_map(
            array(
                'name'     => esc_html__( 'Product Testimonial Item', 'kuteshop' ),
                'base'     => 'slide_testimonial_item',
                'icon'     => 'icon-st',
                'content_element' => true,
                'as_child' => array('only' => 'slide_carousel'),
                'params'   => array(
                    array(
                        'type'        => 'dropdown',
                        'holder'      => 'div',
                        'heading'     => esc_html__( 'Style', 'kuteshop' ),
                        'param_name'  => 'style',
                        'value'       => array(
                            esc_html__( 'Default', 'kuteshop' )              => '',
                            esc_html__( 'Home 14', 'kuteshop' )              => 'home-14',
                            ),
                    ),
                    array(
                        'type'        => 'attach_image',
                        'heading'     => esc_html__( 'Image', 'kuteshop' ),
                        'param_name'  => 'image',
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Title', 'kuteshop' ),
                        'param_name'  => 'title',
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Link', 'kuteshop' ),
                        'param_name'  => 'link',
                    ),
                    array(
                        'type'        => 'textfield',
                        'heading'     => esc_html__( 'Time', 'kuteshop' ),
                        'param_name'  => 'time',
                        'dependency'  => array(
                            'element'   => 'style',
                            'value'   => 'home-14',
                            )
                    ),
                    array(
                        'type'        => 'textarea',
                        'holder'      => 'div',
                        'heading'     => esc_html__( 'Description', 'kuteshop' ),
                        'param_name'  => 'des',
                    ),
                )
            )
        );
    }
}

/**************************************END ITEM************************************/


//Your "container" content element should extend WPBakeryShortCodesContainer class to inherit all required functionality
if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_Slide_Carousel extends WPBakeryShortCodesContainer {
    }
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Slide_Banner_Item extends WPBakeryShortCode {}
    class WPBakeryShortCode_Slide_Cat_Item extends WPBakeryShortCode {}
    class WPBakeryShortCode_Slide_Service_Item extends WPBakeryShortCode {}
    class WPBakeryShortCode_Slide_Testimonial_Item extends WPBakeryShortCode {}
}