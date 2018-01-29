<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 05/09/15
 * Time: 10:00 AM
 */
if(class_exists("woocommerce")){
    if(!function_exists('s7upf_vc_product_list_tab'))
    {
        function s7upf_vc_product_list_tab($attr, $content = false)
        {
            $html = $el_class = $html_wl = $html_cp = '';
            extract(shortcode_atts(array(
                'style'             => 'home1',
                'large_pos'         => 'left-justify',
                'title'             => '',
                'adv_image'             => '',
                'adv_title'             => '',
                'adv_pos'             => 'top',
                'adv_link'             => '',
                'tabs'              => '',
                'number'            => '',
                'cats'              => '',
                'brands'            => '',
                'order_by'          => 'date',
                'order'             => 'DESC',
                'color'             => '',
                'color2'             => '',
                'color3'             => 'block-df',
                'box_index'             => '',
                'link'             => '#',
                'item'          => '',
                'item_res'      => '',
                'speed'         => '',
                'size'          => '',
                'list_advs'     => '',
            ),$attr));
            if(!empty($size)) $size = explode('x', $size);
            if(!empty($cats)) $cats = str_replace(' ', '', $cats);
            if(!empty($brands)) $brands = str_replace(' ', '', $brands);
            $args=array(
                'post_type'         => 'product',
                'posts_per_page'    => $number,
                'orderby'           => $order_by,
                'order'             => $order
            );
            if(!empty($cats)) {
                $custom_list = explode(",",$cats);
                $args['tax_query'][]=array(
                    'taxonomy'=>'product_cat',
                    'field'=>'slug',
                    'terms'=> $custom_list
                );
            }
            if(!empty($brands)) {
                $custom_brands = explode(",",$brands);
                $args['tax_query']['relation'] =  'AND';
                $args['tax_query'][]=array(
                    'taxonomy'=>'product_brand',
                    'field'=>'slug',
                    'terms'=> $custom_brands
                );
            }
            $pre = rand(1,100);
            if(!empty($tabs)){
                $tabs = explode(',', $tabs);
                $tab_html = $content_html = '';
                foreach ($tabs as $key => $tab) {
                    switch ($tab) {
                        case 'bestsell':
                            $tab_title =    esc_html__("Popular","kuteshop");
                            $args['meta_key'] = 'total_sales';
                            $args['orderby'] = 'meta_value_num';
                            break;

                        case 'toprate':
                            $tab_title =    esc_html__("Most review","kuteshop");
                            unset($args['meta_key']);
                            $args['meta_key'] = '_wc_average_rating';
                            $args['orderby'] = 'meta_value_num';
                            $args['meta_query'] = WC()->query->get_meta_query();
                            $args['tax_query'][] = WC()->query->get_tax_query();
                            break;
                        
                        case 'mostview':
                            $tab_title =    esc_html__("Most View","kuteshop");
                            unset($args['no_found_rows']);
                            unset($args['meta_query']);
                            unset($args['tax_query']);
                            if(!empty($cats)) {
                                $custom_list = explode(",",$cats);
                                $args['tax_query'][]=array(
                                    'taxonomy'=>'product_cat',
                                    'field'=>'slug',
                                    'terms'=> $custom_list
                                );
                            }
                            $args['meta_key'] = 'post_views';
                            $args['orderby'] = 'meta_value_num';
                            break;

                        case 'featured':
                            $tab_title =    esc_html__("Featured","kuteshop");
                            $args['orderby'] = $order_by;
                            $args['tax_query'][] = array(
                                'taxonomy' => 'product_visibility',
                                'field'    => 'name',
                                'terms'    => 'featured',
                                'operator' => 'IN',
                            );
                            break;

                        case 'trendding':
                            unset($args['meta_key']);
                            unset($args['meta_value']);
                            $tab_title =    esc_html__("Tredding","kuteshop");
                            $args['meta_query'][] = array(
                                'key'     => 'trending_product',
                                'value'   => 'on',
                                'compare' => '=',
                            );
                            break;
                        
                        case 'onsale':
                            $tab_title =    esc_html__("On sale","kuteshop");
                            unset($args['meta_query']);
                            unset($args['meta_key']);
                            unset($args['meta_value']);
                            $args['meta_query']['relation']= 'OR';
                            $args['meta_query'][]=array(
                                'key'   => '_sale_price',
                                'value' => 0,
                                'compare' => '>',                
                                'type'          => 'numeric'
                            );
                            $args['meta_query'][]=array(
                                'key'   => '_min_variation_sale_price',
                                'value' => 0,
                                'compare' => '>',                
                                'type'          => 'numeric'
                            );
                            break;
                        
                        default:
                            $tab_title =    esc_html__("New arrivals","kuteshop");
                            $args['orderby'] = 'date';
                            break;
                    }
                    if($key == 0) $f_class = 'active';
                    else $f_class = '';
                    $product_query = new WP_Query($args);
                    $count = 1;
                    $count_query = $product_query->post_count;
                    switch ($style) {
                        case 'home12':
                            if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,768:3,980:4,1200:5';
                            if(empty($item)) $item = 5;
                            if(empty($size)) $size = array(300,300);
                            $tab_html .=    '<li class="'.$f_class.'"><a href="'.esc_url('#'.$pre.$tab).'" data-toggle="tab">'.$tab_title.'</a></li>';
                            $content_html .=    '<div id="'.$pre.$tab.'" class="tab-pane clearfix '.$f_class.'">
                                                    <div class="product-slider12">
                                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="">';
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    global $product;
                                    $content_html .=   '<div class="item-product">
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus">'.esc_html__("quick view","kuteshop").'</a>
                                                                '.s7upf_product_link('','hidden-text').'
                                                            </div>
                                                            <div class="product-info">
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_price_html().'
                                                                '.s7upf_get_rating_html().'
                                                            </div>
                                                        </div>';
                                }
                            }
                            $content_html .=            '</div>
                                                    </div>
                                                </div>';
                            break;

                        case 'home11':
                            if(empty($item) && empty($item_res)) $item_res = '0:1,500:2,992:3,1200:4';
                            if(empty($item)) $item = 4;
                            if(empty($size)) $size = array(300,300);
                            $tab_html .=    '<li class="'.$f_class.'"><a href="'.esc_url('#'.$pre.$tab).'" data-toggle="tab">'.$tab_title.'</a></li>';
                            $content_html .=    '<div id="'.$pre.$tab.'" class="tab-pane clearfix '.$f_class.'">
                                                    <div class="product-slider11">
                                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="">';
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    global $product;
                                    $content_html .=   '<div class="item-product11">
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                            </div>
                                                            <div class="product-info">
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_price_html().'
                                                                '.s7upf_get_rating_html().'
                                                                '.s7upf_product_link('product-extra-link5').'
                                                            </div>
                                                        </div>';
                                }
                            }
                            $content_html .=            '</div>
                                                    </div>
                                                </div>';
                            break;

                        case 'home10-2':
                            if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,640:3,980:4,1200:5';
                            if(empty($item)) $item = 5;
                            if(empty($size)) $size = array(300,300);
                            $tab_html .=    '<li class="'.$f_class.'"><a href="'.esc_url('#'.$pre.$tab).'" data-toggle="tab">'.$tab_title.'</a></li>';
                            $content_html .=    '<div id="'.$pre.$tab.'" class="tab-pane clearfix '.$f_class.'">
                                                    <div class="product-slider10">
                                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    global $product;
                                    $content_html .=   '<div class="item-product10">
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                            </div>
                                                            <div class="product-info">
                                                                '.s7upf_get_price_html().'
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_rating_html().'
                                                                '.s7upf_product_link('home10').'
                                                            </div>
                                                        </div>';
                                }
                            }
                            $content_html .=            '</div>
                                                    </div>
                                                </div>';
                            break;

                        case 'home10':
                            if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,1200:3';
                            if(empty($item)) $item = 3;
                            if(empty($size)) $size = array(300,300);
                            $tab_html .=    '<li class="'.$f_class.'"><a href="'.esc_url('#'.$pre.$tab).'" data-toggle="tab">'.$tab_title.'</a></li>';
                            $content_html .=    '<div id="'.$pre.$tab.'" class="tab-pane clearfix '.$f_class.'">
                                                    <div class="product-type-slider10">
                                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    global $product;
                                    if($count % 3 == 1) $content_html .=    '<div class="item-product-type10">';
                                    $content_html .=   '<div class="item-product10">
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                            </div>
                                                            <div class="product-info">
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_price_html().'
                                                                '.s7upf_get_rating_html().'
                                                                '.s7upf_product_link('home10').'
                                                            </div>
                                                        </div>';
                                    if($count % 3 == 0 || $count == $count_query) $content_html .=    '</div>';
                                    $count++;
                                }
                            }
                            $content_html .=            '</div>
                                                    </div>
                                                </div>';
                            break;
                        
                        default:
                            if(empty($size)) $size = array(200,200);
                            $sizel = array(371,371);
                            $tab_html .=    '<li class="'.$f_class.'"><a href="'.esc_url('#'.$pre.$tab).'" data-toggle="tab">'.$tab_title.'</a></li>';
                            $content_html .=    '<div id="'.$pre.$tab.'" class="tab-pane clearfix '.$f_class.'">';
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    global $product;
                                    $large_item =   '<div class="main-box1">
                                                        <div class="main-product1">
                                                            <div class="product-info">
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_price_html().'
                                                                '.s7upf_get_rating_html().'
                                                                '.s7upf_product_link('shop-list').'
                                                            </div>
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$sizel).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link pos-bottom"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                            </div>
                                                        </div>
                                                    </div>';
                                    $small_item =   '<div class="item-product1">
                                                        <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                        '.s7upf_get_price_html().'
                                                        <div class="product-thumb">
                                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                            </a>
                                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link pos-bottom"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                        </div>
                                                        <div class="product-info">
                                                           '.s7upf_get_rating_html().'
                                                            '.s7upf_product_link('shop-list').'
                                                        </div>
                                                    </div>';
                                    if($large_pos != 'left-justify'){
                                        if($large_pos == 'main-left-right'){
                                            if($count % 5 == 1) $content_html .= $large_item;
                                            else{
                                                if($count % 5 == 2) $content_html .= '<div class="left-box1">';
                                                if($count % 5 == 4 || $count % 5 == 4) $content_html .= '<div class="right-box1">';
                                                $content_html .= $small_item;
                                                if($count % 5 == 3 || $count % 5 == 0 || $count == $count_query) $content_html .= '</div>';
                                            }
                                        }
                                        if($large_pos == 'left-main-right'){
                                            if($count % 5 == 3) $content_html .= $large_item;
                                            else{
                                                if($count % 5 == 1) $content_html .= '<div class="left-box1">';
                                                if($count % 5 == 4) $content_html .= '<div class="right-box1">';
                                                $content_html .= $small_item;
                                                if($count % 5 == 2 || $count % 5 == 0 || $count == $count_query) $content_html .= '</div>';
                                            }
                                        }
                                        if($large_pos == 'left-right-main'){
                                            if($count % 5 == 0) $content_html .= $large_item;
                                            else{
                                                if($count % 5 == 1) $content_html .= '<div class="left-box1">';
                                                if($count % 5 == 3) $content_html .= '<div class="right-box1">';
                                                $content_html .= $small_item;
                                                if($count % 5 == 2 || $count % 5 == 4 || $count == $count_query) $content_html .= '</div>';
                                            }
                                        }
                                    }
                                    else{
                                        if($count % 2 == 1) $content_html .= '<div class="justify-box1">';
                                        $content_html .= $small_item;
                                        if($count % 2 == 0 || $count == $count_query) $content_html .= '</div>';
                                    }
                                    $count++;
                                }
                            }
                            $content_html .=    '</div>';
                            break;
                    }                    
                }
                switch ($style) {
                    case 'home12':
                        $data = (array) vc_param_group_parse_atts( $list_advs );
                        $html .=    '<div class="product-tab-box12 '.esc_attr($color3).'">
                                        <div class="header-box12">';
                        if(!empty($title)) $html .=    '<h2 class="title24">'.esc_html($title).'</h2>';
                        $html .=            '<div class="row">
                                                <div class="col-lg-2 col-md-3 col-sm-12">
                                                    <ul class="list-none list-cat12">';
                        if(isset($custom_list)){
                            foreach ($custom_list as $key => $tab) {
                                $term = get_term_by( 'slug',$tab, 'product_cat' );
                                if(!empty($term) && is_object($term)){
                                    $term_link = get_term_link( $term->term_id, 'product_cat' );
                                    $cat_thumb_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
                                    $html .=            '<li>
                                                            <a href="'.esc_url($term_link).'">
                                                                '.wp_get_attachment_image($cat_thumb_id,array(20,20)).'
                                                                <span>'.$term->name.'</span>
                                                            </a>
                                                        </li>';
                                }
                            }
                        }
                        $html .=                    '</ul>
                                                </div>';
                        if(isset($data[0]['image'])){
                        $html .=                '<div class="col-lg-7 col-md-9 col-sm-12">
                                                    <div class="banner-box banner-box12">
                                                        <a href="'.esc_url($data[0]['link']).'" class="link-banner-box">'.wp_get_attachment_image($data[0]['image'],'full').'</a>
                                                        <div class="banner-info '.esc_attr($data[0]['color']).'">
                                                            <h2 class="title24">'.esc_html($data[0]['title']).'</h2>
                                                            <h3 class="title18 color">'.esc_html($data[0]['des']).'</h3>
                                                            <a href="'.esc_url($data[0]['link']).'" class="shopnow">'.esc_html__("Shop now","kuteshop").'</a>
                                                        </div>
                                                    </div>
                                                </div>';
                        }
                        if(isset($data[1]['image']) || isset($data[2]['image'])){
                        $html .=                '<div class="col-lg-3 col-md-12 col-sm-12">
                                                    <div class="list-banner-zoom12">
                                                        <div class="row">
                                                            <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6">
                                                                <div class="banner-zoom banner-zoom12">
                                                                    <a href="'.esc_url($data[1]['link']).'" class="thumb-zoom">'.wp_get_attachment_image($data[1]['image'],'full').'</a>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6">
                                                                <div class="banner-zoom banner-zoom12">
                                                                    <a href="'.esc_url($data[2]['link']).'" class="thumb-zoom">'.wp_get_attachment_image($data[2]['image'],'full').'</a>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                        }
                        $html .=            '</div>
                                        </div>';
                        $html .=        '<div class="product-tab12">
                                            <div class="title-tab12">
                                                <ul class="list-none">
                                                    '.$tab_html.'
                                                </ul>';
                        $html .=            '</div>
                                            <div class="tab-content">
                                                '.$content_html.'
                                            </div>
                                        </div>
                                    </div>';
                        break;

                    case 'home11':
                        $html .=    '<div class="product-box11 '.esc_attr($color2).'">
                                        <div class="title-box11">';
                        if(!empty($title)){
                            $html .=        '<div class="shape-title white text-uppercase">
                                                <div class="inner-shape-title">
                                                    <div class="shape-text">
                                                        <strong class="index-box">'.esc_html($box_index).'</strong>
                                                        <h2 class="title18">'.esc_html($title).'</h2>
                                                    </div>
                                                </div>
                                            </div>';
                        }
                        $html .=            '<ul class="list-none">
                                                '.$tab_html.'
                                            </ul>';
                        $html .=        '</div>
                                        <div class="tab-content">
                                            '.$content_html.'
                                        </div>
                                    </div>';
                        break;

                    case 'home10-2':
                        $html .=    '<div class="tab-product10">
                                        <div class="title-tab10">
                                            <ul class="list-none">
                                                '.$tab_html.'
                                            </ul>';
                        $html .=    '<a href="'.esc_url($link).'" class="seeall">'.esc_html__("See All","kuteshop").' <i class="fa fa-angle-right" aria-hidden="true"></i></a>';
                        $html .=        '</div>
                                        <div class="tab-content">
                                            '.$content_html.'
                                        </div>
                                    </div>';
                        break;

                    case 'home10':
                        $html .=    '<div class="product-type10">
                                        <div class="title-product-type10">
                                            <ul class="list-none">
                                                '.$tab_html.'
                                            </ul>
                                        </div>
                                        <div class="tab-content">
                                            '.$content_html.'
                                        </div>
                                    </div>';
                        break;
                    
                    default:
                        $html .=    '<div class="product-box1 clearfix '.esc_attr($color).'">
                                        <div class="title-box1">';
                        $html .=        '<h2 class="title30"><span>'.substr($title, 0, 1).'</span><a href="'.esc_url($link).'">'.esc_html($title).'</a></h2>
                                            <ul class="list-none">
                                                '.$tab_html.'
                                            </ul>
                                        </div>
                                        <div class="content-box1">
                                            <div class="banner-box1">
                                                <div class="banner-box">
                                                    <a href="'.esc_url($adv_link).'" class="link-banner-box">'.wp_get_attachment_image($adv_image,'full').'</a>
                                                    <div class="info-banner-box1 '.esc_attr($adv_pos).' white">
                                                        <h2>'.esc_html($adv_title).'</h2>
                                                        <a href="'.esc_url($adv_link).'" class="shopnow border radius white">'.esc_html__("Shop now!","kuteshop").'</a>
                                                    </div>
                                                </div>
                                                <div class="category-box1">
                                                    '.wpb_js_remove_wpautop($content, true).'
                                                </div>
                                            </div>
                                            <div class="content-pro-box1 tab-content '.esc_attr($large_pos).'">
                                                '.$content_html.'
                                            </div>
                                        </div>
                                    </div>';
                        break;
                }                
            }
            wp_reset_postdata();
            return $html;
        }
    }

    stp_reg_shortcode('sv_product_list_tab','s7upf_vc_product_list_tab');
    if(isset($_GET['return'])) $check_add = $_GET['return'];
    if(empty($check_add)) add_action( 'vc_before_init_base','s7upf_add_product_list_tab',10,100 );
    if ( ! function_exists( 's7upf_add_product_list_tab' ) ) {
        function s7upf_add_product_list_tab(){
            vc_map( array(
                "name"      => esc_html__("SV Product Tab 2", 'kuteshop'),
                "base"      => "sv_product_list_tab",
                "icon"      => "icon-st",
                "category"  => '7Up-theme',
                "params"    => array(
                    array(
                        "type" => "dropdown",
                        "heading" => esc_html__("Style",'kuteshop'),
                        "param_name" => "style",
                        "value"     => array(
                            esc_html__("Home 1",'kuteshop')   => 'home1',
                            esc_html__("Home 10",'kuteshop')   => 'home10',
                            esc_html__("Home 10(2)",'kuteshop')   => 'home10-2',
                            esc_html__("Home 11",'kuteshop')   => 'home11',
                            esc_html__("Home 12",'kuteshop')   => 'home12',
                            )
                    ),
                    array(
                        'heading'     => esc_html__( 'Box Index', 'kuteshop' ),
                        'type'        => 'textfield',
                        'param_name'  => 'box_index',
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home11',
                            )
                    ),
                    array(
                        'heading'     => esc_html__( 'Large Item Possition', 'kuteshop' ),
                        'type'        => 'dropdown',
                        'description' => esc_html__( 'Enter number of Large item in product list. Empty is not set.', 'kuteshop' ),
                        'param_name'  => 'large_pos',
                        'value'       => array(
                            esc_html__( 'None', 'kuteshop' )    => '',
                            esc_html__( 'Left', 'kuteshop' )    => 'main-left-right',
                            esc_html__( 'Center', 'kuteshop' )    => 'left-main-right',
                            esc_html__( 'Right', 'kuteshop' )    => 'left-right-main',
                            ),
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home1',
                            )
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => esc_html__("Color",'kuteshop'),
                        "param_name" => "color3",
                        "value"     => array(
                            esc_html__("Default",'kuteshop')    => 'block-df',
                            esc_html__("Orange",'kuteshop')       => 'block-orange',
                            esc_html__("Green",'kuteshop')       => 'block-green',
                            esc_html__("Yellow",'kuteshop')       => 'block-yellow',
                            esc_html__("Purple",'kuteshop')      => 'block-purple',
                            ),
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home12',
                            )
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => esc_html__("Color",'kuteshop'),
                        "param_name" => "color2",
                        "value"     => array(
                            esc_html__("Default",'kuteshop')    => '',
                            esc_html__("Blue",'kuteshop')       => 'box-blue',
                            esc_html__("Pink",'kuteshop')       => 'box-pink',
                            esc_html__("Cyan",'kuteshop')       => 'box-cyan',
                            esc_html__("Green",'kuteshop')      => 'box-green',
                            ),
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home11',
                            )
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => esc_html__("Color",'kuteshop'),
                        "param_name" => "color",
                        "value"     => array(
                            esc_html__("Default",'kuteshop')    => '',
                            esc_html__("Blue",'kuteshop')       => 'color-blue',
                            esc_html__("Red",'kuteshop')        => 'color-red',
                            esc_html__("Orange",'kuteshop')     => 'color-orange',
                            esc_html__("Green",'kuteshop')      => 'color-green',
                            esc_html__("Maroon",'kuteshop')     => 'color-maroon',
                            ),
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home1',
                            )
                    ),
                    array(
                        'heading'     => esc_html__( 'Number', 'kuteshop' ),
                        'type'        => 'textfield',
                        'description' => esc_html__( 'Enter number of product. Default is 10.', 'kuteshop' ),
                        'param_name'  => 'number',
                    ),
                    array(
                        'heading'     => esc_html__( 'Title', 'kuteshop' ),
                        'holder'      => 'h4',
                        'type'        => 'textfield',
                        'param_name'  => 'title',
                    ),
                    array(
                        'heading'     => esc_html__( 'Link More', 'kuteshop' ),
                        'type'        => 'textfield',
                        'param_name'  => 'link',
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => array('home1','home10-2'),
                            )
                    ),
                    array(
                        'heading'     => esc_html__( 'Adv Image', 'kuteshop' ),
                        'type'        => 'attach_image',
                        'param_name'  => 'adv_image',
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home1',
                            )
                    ),
                    array(
                        'heading'     => esc_html__( 'Adv Title', 'kuteshop' ),
                        'type'        => 'textfield',
                        'param_name'  => 'adv_title',
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home1',
                            )
                    ),
                    array(
                        'heading'     => esc_html__( 'Adv Info Possition', 'kuteshop' ),
                        'type'        => 'dropdown',
                        'param_name'  => 'adv_pos',
                        'value'       => array(
                            esc_html__( 'Top', 'kuteshop' )     => 'top',
                            esc_html__( 'Bottom', 'kuteshop' )  => 'bottom',
                            ),
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home1',
                            )
                    ),
                    array(
                        'heading'     => esc_html__( 'Adv Link', 'kuteshop' ),
                        'type'        => 'textfield',
                        'param_name'  => 'adv_link',
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home1',
                            )
                    ),
                    array(
                        'holder'     => 'div',
                        'heading'     => esc_html__( 'Product Categories', 'kuteshop' ),
                        'type'        => 'autocomplete',
                        'param_name'  => 'cats',
                        'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'values' => s7upf_get_product_taxonomy(),
                        ),
                        'save_always' => true,
                        'description' => esc_html__( 'List of product categories', 'kuteshop' ),
                    ),
                    array(
                        'holder'     => 'div',
                        'heading'     => esc_html__( 'Product Brands', 'kuteshop' ),
                        'type'        => 'autocomplete',
                        'param_name'  => 'brands',
                        'settings' => array(
                            'multiple' => true,
                            'sortable' => true,
                            'values' => s7upf_get_product_taxonomy('product_brand'),
                        ),
                        'save_always' => true,
                        'description' => esc_html__( 'List of product brands', 'kuteshop' ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__( 'Order By', 'kuteshop' ),
                        'value' => s7upf_get_order_list(),
                        'param_name' => 'orderby',
                        'description' => esc_html__( 'Select Orderby Type ', 'kuteshop' ),
                        'edit_field_class'=>'vc_col-sm-6 vc_column',
                    ),
                    array(
                        'heading'     => esc_html__( 'Order', 'kuteshop' ),
                        'type'        => 'dropdown',
                        'param_name'  => 'order',
                        'value' => array(                   
                            esc_html__('Desc','kuteshop')  => 'DESC',
                            esc_html__('Asc','kuteshop')  => 'ASC',
                        ),
                        'description' => esc_html__( 'Select Order Type ', 'kuteshop' ),
                        'edit_field_class'=>'vc_col-sm-6 vc_column',
                    ),
                    array(
                        "type" => "checkbox",
                        "heading" => esc_html__("Tabs",'kuteshop'),
                        "param_name" => "tabs",
                        "value" => array(
                            esc_html__("New Arrivals",'kuteshop')    => 'newarrival',
                            esc_html__("Best Seller",'kuteshop')     => 'bestsell',
                            esc_html__("Most Review",'kuteshop')     => 'toprate',
                            esc_html__("Most View",'kuteshop')       => 'mostview',
                            esc_html__("Featured",'kuteshop')        => 'featured',
                            esc_html__("Trendding",'kuteshop')       => 'trendding',
                            esc_html__("On Sale",'kuteshop')         => 'onsale',
                            ),
                    ),
                    array(
                        "type" => "textarea_html",
                        "holder" => "div",
                        "heading" => esc_html__("Content",'kuteshop'),
                        "param_name" => "content",
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => 'home1',
                            )
                    ),
                    array(
                        "type"          => "textfield",
                        "heading"       => esc_html__("Item / Slider",'kuteshop'),
                        "param_name"    => "item",
                        "group"         => esc_html__("Slider Settings",'kuteshop'),
                        'description' => esc_html__( 'Enter number of item. Default is auto with style display.', 'kuteshop' ),
                    ),
                    array(
                        "type"          => "textfield",
                        "heading"       => esc_html__("Item Responsive",'kuteshop'),
                        "param_name"    => "item_res",
                        "group"         => esc_html__("Slider Settings",'kuteshop'),
                        'description'   => esc_html__( 'Enter item for screen width(px) format is width:value and separate values by ",". Example is 0:2,600:3,1000:4. Default is auto.', 'kuteshop' ),
                    ),
                    array(
                        "type"          => "textfield",
                        "heading"       => esc_html__("Speed",'kuteshop'),
                        "param_name"    => "speed",
                        "group"         => esc_html__("Slider Settings",'kuteshop'),                    
                    ),
                    array(
                        "type"          => "textfield",
                        "heading"       => esc_html__("Size Thumbnail",'kuteshop'),
                        "param_name"    => "size",
                        "group"         => esc_html__("Thumb Settings",'kuteshop'),
                        'description'   => esc_html__( 'Enter site thumbnail to crop. [width]x[height]. Example is 300x300', 'kuteshop' ),
                    ),
                    array(
                        "type" => "param_group",
                        "heading" => esc_html__("Add Image List",'kuteshop'),
                        "param_name" => "list_advs",
                        'dependency'    => array(
                            'element'   => 'style',
                            'value'   => array('home12'),
                            ),
                        "params"    => array(                        
                            array(
                                "type" => "attach_image",
                                "heading" => esc_html__("Image",'kuteshop'),
                                "param_name" => "image",
                            ),
                            array(
                                "type" => "textfield",
                                "heading" => esc_html__("Title",'kuteshop'),
                                "param_name" => "title",
                            ),
                            array(
                                "type" => "textfield",
                                "heading" => esc_html__("Description",'kuteshop'),
                                "param_name" => "des",
                            ),
                            array(
                                "type" => "dropdown",
                                "heading" => esc_html__("Color",'kuteshop'),
                                "param_name" => "color",
                                "value"     => array(
                                    esc_html__("Default",'kuteshop')    => 'df-color',
                                    esc_html__("White",'kuteshop')    => 'white',
                                    )
                            ),
                            array(
                                "type" => "textfield",
                                "heading" => esc_html__("Link",'kuteshop'),
                                "param_name" => "link",
                            ),
                        )
                    ),
                )
            ));
        }
    }
}
