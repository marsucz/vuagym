<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 05/09/15
 * Time: 10:00 AM
 */
if(class_exists("woocommerce")){
if(!function_exists('sv_vc_product_list'))
{
    function sv_vc_product_list($attr, $content = false)
    {
        $html = $view_html = '';
        extract(shortcode_atts(array(
            'style'         => 'item-product-ajax',
            'type'          => 'type-grid',
            'load_more'     => '',
            'title'         => '',
            'color_title'   => '',
            'sub_title'     => '',
            'des'           => '',
            'view_link'     => '',
            'number'        => '8',
            'cats'          => '',
            'brands'        => '',
            'color'         => '',
            'order_by'      => 'date',
            'order'         => 'DESC',
            'product_type'  => '',
            'item'          => '',
            'item_res'      => '',
            'speed'         => '',
            'size'          => '',
            'label'         => '',
            'thumb_style'   => '',
            'thumb_h_anmt'  => '',
            'quickview'     => 'show',
            'quickview_pos' => '',
            'quickview_style' => '',
            'extra_link'    => 'show',
            'extra_style'   => '',
            'adv_image'     => '',
            'adv_image_pos' => '',
            'adv_title'     => '',
            'adv_link'      => '',
            'adv_pos'       => '',
            'adv_image2'    => '',
            'adv_title2'    => '',
            'adv_link2'     => '',
            'adv_image3'    => '',
            'adv_title3'    => '',
            'adv_link3'     => '',
            'tags'          => '',
            'animation'     => '',
            'time_delay'    => '300',
            'prices'        => '',
        ),$attr));
        if(!empty($cats)) $cats = str_replace(' ', '', $cats);
        if(!empty($brands)) $brands = str_replace(' ', '', $brands);
        if(!empty($tags)) $tags = str_replace(' ', '', $tags);

        if($item > 10) $item = 10;
        $animation_class = $data ='';
        $time_plus = 0;
        $time = 0;
        if(!empty($animation)) {
            $animation_class = 'animate';
            $time_data = explode(',', $time_delay);
            $time = $time_data[0];
            if(isset($time_data[1])) $time_plus = $time_data[1];            
        }
        $data_load = array(
            'style'  => $style,
            'style_item'  => $style,
            'number'  => $number,
            'cats'  => $cats,
            'brands'  => $brands,
            'order'  => $order,
            'order_by'  => $order_by,
            'product_type'  => $product_type,
            'item'  => $item,
            'size'  => $size,
            'animation_class'  => $animation_class,
            'time'  => $time,
            'time_plus'  => $time_plus,
            'animation'  => $animation,
            'thumb_style'  => $thumb_style,
            'quickview'  => $quickview,
            'quickview_pos'  => $quickview_pos,
            'quickview_style'  => $quickview_style,
            'extra_link'  => $extra_link,
            'extra_style'  => $extra_style,
            'thumb_h_anmt'  => $thumb_h_anmt,
            'label'  => $label
            );
        $custom_list = array();
        $args = array(
            'post_type'         => 'product',
            'posts_per_page'    => $number,
            'orderby'           => $order_by,
            'order'             => $order,
            'paged'             => 1,
            );
        if($product_type == 'trendding'){
            $args['meta_query'][] = array(
                    'key'     => 'trending_product',
                    'value'   => 'on',
                    'compare' => '=',
                );
        }
        if($product_type == 'toprate'){
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['meta_query'] = WC()->query->get_meta_query();
            $args['tax_query'][] = WC()->query->get_tax_query();
        }
        if($product_type == 'mostview'){
            $args['meta_key'] = 'post_views';
            $args['orderby'] = 'meta_value_num';
        }
        if($product_type == 'bestsell'){
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
        }
        if($product_type=='onsale'){
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
        }
        if($product_type == 'featured'){
            $args['tax_query'][] = array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN',
            );
        }
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
        $product_query = new WP_Query($args);
        $count = 1;
        $count_query = $product_query->post_count;
        $max_page = $product_query->max_num_pages;
        if(!empty($size)) $size = explode('x', $size);
        else $size = array(195,260);
        $color_class = 'color-box-'.uniqid();
        switch ($style) {
            case 'item-product-trending2':
                if(!empty($color)) S7upf_Assets::add_css(
                                    '.'.$color_class.' .header-cat-color::after,
                                    .'.$color_class.' .btn-loadmore a:hover,
                                    .'.$color_class.' .product-extra-link a:hover{background:'.$color.';}
                                    .'.$color_class.' .product-title a:hover,
                                    .'.$color_class.' .product-price ins,.'.$color_class.' .product-price > span, .'.$color_class.' .quickview-link:hover,
                                    .'.$color_class.' .product-extra-link a{color:'.$color.';}.product-extra-link a:hover{color:#fff;}
                                    ');
                if(empty($item)) $item = 1;
                $data_load['item'] = $item;
                $data_loadjs = json_encode($data_load);
                $html .=    '<div class="trending-box2 ajax-loadmore-'.esc_attr($load_more).' '.esc_attr($color_class).'">';
                if(!empty($title)) $html .=     '<h2 class="title14 white bg-color" style="color:'.esc_attr($color_title).'">'.esc_html($title).'</h2>';
                $html .=        '<div class="trending-slider2 content-load-wrap">
                                    <div class="clearfix content-load-ajax '.esc_attr($type).'" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="true" data-navigation="">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $data_ani = ' data-anim-delay="'.$time.'" data-anim-type="'.$animation.'"';
                        if($count % 2 == 1) $html .= '<div class="item-trending2">';
                        $html .=    s7upf_product_item(
                                        $style,
                                        $item,
                                        $animation_class,
                                        $data_ani,
                                        $thumb_style,
                                        array(
                                            'quickview'     => array(
                                                'status'    => $quickview,
                                                'pos'       => $quickview_pos,
                                                'style'     => $quickview_style,
                                                ),
                                            'extra-link'    => array(
                                                'status'    => $extra_link,
                                                'style'     => $extra_style,
                                                )
                                            ),
                                        $size,
                                        $thumb_h_anmt,
                                        '',
                                        $label
                                    );
                        if($count % 2 == 0 || $count == $count_query) $html .= '</div>';
                        $time += $time_plus;
                        $count++;
                    }
                }
                $html .=            '</div>';
                if($max_page > 1 && $load_more == 'show' && $type == 'type-grid'){
                    $html .=         '<div class="btn-loadmore"><a href="#" class="load-ajax-btn" data-page="1" data-max_page="'.$max_page.'" data-load_data='."'".$data_loadjs.' '."'".'><i aria-hidden="true" class="fa fa-chevron-down"></i><strong> XEM THÊM</strong></a></div>';
                }
                $html .=        '</div>
                            </div>';
                break;

            case 'item-product4 item-more-info':
                if(!empty($color)) S7upf_Assets::add_css(
                                    '.'.$color_class.' .header-cat-color::after,.'.$color_class.' .banner-cat-color-info,
                                    .'.$color_class.' .btn-loadmore a:hover,
                                    .'.$color_class.' .owl-theme .owl-controls .owl-buttons div:hover,.'.$color_class.' .hotkey-cat-color a:hover,
                                    .'.$color_class.' .product-extra-link a:hover{background:'.$color.';}
                                    .'.$color_class.' .hotkey-cat-color a:hover,.'.$color_class.' .btn-loadmore a:hover{border-color: '.$color.';}
                                    .'.$color_class.' .header-cat-color .title18,.'.$color_class.' .product-title a:hover,
                                    .'.$color_class.' .adv-cat-color-info .more:hover,.'.$color_class.' .header-cat-color .cat-color-link:hover,
                                    .'.$color_class.' .product-price ins,.'.$color_class.' .product-price > span, .'.$color_class.' .quickview-link:hover,
                                    .'.$color_class.' .product-extra-link a{color:'.$color.';}.product-extra-link a:hover{color:#fff;}
                                    ');
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,992:3,1200:5';
                if(empty($item)) $item = 5;
                $data_load['item'] = $item;
                $data_loadjs = json_encode($data_load);
                $html .=    '<div class="category-color ajax-loadmore-'.esc_attr($load_more).' '.esc_attr($color_class).' banner-'.esc_attr($adv_image_pos).'">';
                if(!empty($title) || !empty($view_link)){
                    $html .=    '<div class="header-cat-color">';
                    if(!empty($title)) $html .=     '<h2 class="title18" style="color:'.esc_attr($color_title).'">'.esc_html($title).'</h2>';
                    if(!empty($view_link)) $html .= '<a href="'.esc_url($view_link).'" class="cat-color-link wobble-top">'.esc_html__("more","kuteshop").'</a>';
                    $html .=    '</div>';
                }
                $html .=        '<div class="content-cat-color">
                                    <div class="clearfix">';
                $html .=                '<div class="banner-cat-color">
                                            <div class="banner-cat-color-thumb">
                                                <a href="'.esc_url($adv_link).'">'.wp_get_attachment_image($adv_image,'full').'</a>
                                            </div>
                                            <div class="banner-cat-color-info">
                                                <h2>'.esc_html($adv_title).'</h2>
                                                <a href="'.esc_url($adv_link).'" class="pulse-grow">'.esc_html__("Shop now!","kuteshop").'</a>
                                            </div>
                                        </div>';
                $html .=                '<div class="slider-cat-color content-load-wrap">
                                            <div class="clearfix content-load-ajax '.esc_attr($type).'" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $data_ani = ' data-anim-delay="'.$time.'" data-anim-type="'.$animation.'"';
                        $html .=    s7upf_product_item(
                                        $style,
                                        $item,
                                        $animation_class,
                                        $data_ani,
                                        $thumb_style,
                                        array(
                                            'quickview'     => array(
                                                'status'    => $quickview,
                                                'pos'       => $quickview_pos,
                                                'style'     => $quickview_style,
                                                ),
                                            'extra-link'    => array(
                                                'status'    => $extra_link,
                                                'style'     => $extra_style,
                                                )
                                            ),
                                        $size,
                                        $thumb_h_anmt,
                                        '',
                                        $label
                                    );
                        $time += $time_plus;
                    }
                }
                $html .=                    '</div>';
                if($max_page > 1 && $load_more == 'show' && $type == 'type-grid'){
                    $html .=         '<div class="btn-loadmore"><a href="#" class="load-ajax-btn" data-page="1" data-max_page="'.$max_page.'" data-load_data='."'".$data_loadjs.' '."'".'><i aria-hidden="true" class="fa fa-chevron-down"></i><strong> XEM THÊM</strong></a></div>';
                }
                $html .=                '</div>';
                if(!empty($adv_image) || !empty($adv_image2) || !empty($adv_image3)){
                    $html .=                '<div class="list-cat-color-adv">
                                                <div class="adv-cat-color">
                                                    <div class="adv-cat-color-info">
                                                        <h3 class="product-title"><a href="'.esc_url($adv_link2).'">'.esc_html($adv_title2).'</a></h3>
                                                        <a href="'.esc_url($adv_link2).'" class="more wobble-top">'.esc_html__("more","kuteshop").'</a>
                                                    </div>
                                                    <div class="adv-cat-color-thumb product-thumb">
                                                        <a href="'.esc_url($adv_link2).'" class="product-thumb-link">'.wp_get_attachment_image($adv_image2,array(100,100)).'</a>
                                                    </div>
                                                </div>
                                                <div class="adv-cat-color">
                                                    <div class="adv-cat-color-info">
                                                        <h3 class="product-title"><a href="'.esc_url($adv_link3).'">'.esc_html($adv_title3).'</a></h3>
                                                        <a href="'.esc_url($adv_link3).'" class="more wobble-top">'.esc_html__("more","kuteshop").'</a>
                                                    </div>
                                                    <div class="adv-cat-color-thumb product-thumb">
                                                        <a href="'.esc_url($adv_link3).'" class="product-thumb-link">'.wp_get_attachment_image($adv_image3,array(100,100)).'</a>
                                                    </div>
                                                </div>';
                    if(!empty($tags)){
                        $html .=                '<div class="hotkey-cat-color">
                                                    <h2 class="title14">'.esc_html__("hot key words","kuteshop").'</h2>
                                                    <ul>';
                        $tags_list = explode(',', $tags);
                        foreach ($tags_list as $tag) {
                            $tag_obj = get_term_by( 'slug',$tag, 'product_tag' );
                            if(!empty($tag_obj) && is_object($tag_obj)){
                                $tag_link = get_term_link( $tag_obj->term_id, 'product_tag' );
                                $html .=                '<li><a href="'.esc_url($tag_link).'">'.$tag_obj->name.'</a></li>';
                            }
                        }
                        $html .=                    '</ul>
                                                </div>';
                    }
                    $html .=                '</div>';
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'item-product4':
                if(!empty($color)) S7upf_Assets::add_css(
                                    '.'.$color_class.' .flagship-link>a:hover,
                                    .'.$color_class.' .owl-theme .owl-controls .owl-buttons div:hover,
                                    .'.$color_class.' .product-extra-link a:hover{background:'.$color.';}
                                    .'.$color_class.' .product-title a:hover,
                                    .'.$color_class.' .product-price ins,.'.$color_class.' .product-price > span, .'.$color_class.' .quickview-link.plus:hover,
                                    .'.$color_class.' .product-extra-link a{color:'.$color.';}.product-extra-link a:hover{color:#fff;}
                                    ');
                if(empty($item)) $item = 3;
                $data_load['item'] = $item;
                $data_loadjs = json_encode($data_load);
                $html .=    '<div class="flagship-box ajax-loadmore-'.esc_attr($load_more).' '.esc_attr($color_class).'">
                                <div class="flagship-header">
                                    <div class="flagship-brand">
                                        <a href="'.esc_url($view_link).'">'.wp_get_attachment_image($adv_image,'full').'</a>
                                    </div>
                                    <div class="flagship-info">
                                        <h2><span style="color:'.esc_attr($color_title).'">'.esc_html($title).'</span> '.esc_html($sub_title).'</h2>
                                        <p>'.esc_html($des).'</p>
                                    </div>
                                    <div class="flagship-link">
                                        <a href="'.esc_url($view_link).'">'.esc_html__("Xem đầy đủ","kuteshop").'</a>
                                    </div>
                                </div>
                                <div class="flagship-content content-load-wrap">
                                    <div class="clearfix content-load-ajax '.esc_attr($type).'" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $data_ani = ' data-anim-delay="'.$time.'" data-anim-type="'.$animation.'"';
                        $html .=    s7upf_product_item(
                                        $style,
                                        $item,
                                        $animation_class,
                                        $data_ani,
                                        $thumb_style,
                                        array(
                                            'quickview'     => array(
                                                'status'    => $quickview,
                                                'pos'       => $quickview_pos,
                                                'style'     => $quickview_style,
                                                ),
                                            'extra-link'    => array(
                                                'status'    => $extra_link,
                                                'style'     => $extra_style,
                                                )
                                            ),
                                        $size,
                                        $thumb_h_anmt,
                                        '',
                                        $label
                                    );
                        $time += $time_plus;
                    }
                }
                $html .=            '</div>';
                if($max_page > 1 && $load_more == 'show' && $type == 'type-grid'){
                    $html .=         '<div class="btn-loadmore"><a href="#" class="load-ajax-btn" data-page="1" data-max_page="'.$max_page.'" data-load_data='."'".$data_loadjs.' '."'".'><i aria-hidden="true" class="fa fa-chevron-down"></i><strong> XEM THÊM</strong></a></div>';
                }
                $html .=        '</div>
                            </div>';
                break;

            case 'item-product-ajax tab-price':
                if(!empty($prices)){
                    if(!empty($color)) S7upf_Assets::add_css(
                                    '.'.$color_class.' .tab-pro-ajax-header li.active a::after,
                                    .'.$color_class.' .btn-loadmore a:hover,
                                    .'.$color_class.' .product-extra-link a:hover{background:'.$color.';}
                                    .'.$color_class.' .btn-loadmore a:hover{border-color:'.$color.';}
                                    .'.$color_class.' .tab-pro-ajax-header li.active a,.'.$color_class.' .product-title a:hover,
                                    .'.$color_class.' .product-price ins,.'.$color_class.' .product-price > span, .'.$color_class.' .quickview-link.plus:hover,
                                    .'.$color_class.' .tab-pro-ajax-header li a:hover,
                                    .'.$color_class.' .product-extra-link a{color:'.$color.';}.product-extra-link a:hover{color:#fff;}
                                    ');
                    if(empty($item)) $item = 5;
                    $pre = rand(1,100);
                    $tabs = explode(",",$prices);
                    $tab_html = $tab_content = '';
                    foreach ($tabs as $key => $tab) {
                        $tab_slug = 'price-'.$tab;
                        if($key < count($tabs)-1){
                            $tab_name = $tab.' - '.$tabs[$key+1];
                            $min = $tab;
                            $max = $tabs[$key+1]-1;
                        }
                        else {
                            $tab_name = '>'.$tab;
                            $max = 999999999999;
                        }
                        if($key == 0) $active = 'active';
                        else $active = '';
                        $key_adv = $key+1;
                        $tab_html .=    '<li class="'.esc_attr($active).'"><a href="#'.esc_attr($pre.$tab_slug).'" data-toggle="tab">'.$tab_name.'</a></li>';
                        $tab_content .=    '<div id="'.$pre.$tab_slug.'" class="tab-pane content-load-wrap '.$active.'">
                                                <div class="clearfix content-load-ajax '.esc_attr($type).'" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                        $data_load['price_min'] = $min;
                        $data_load['price_max'] = $max;
                        $args['post__in'] = s7upf_filter_price($min,$max);
                        $data_loadjs = json_encode($data_load);
                        $product_query = new WP_Query($args);
                        $max_page = $product_query->max_num_pages;
                        if($product_query->have_posts()) {
                            while($product_query->have_posts()) {
                                $product_query->the_post();
                                $data_ani = ' data-anim-delay="'.$time.'" data-anim-type="'.$animation.'"';
                                global $product;                                    
                                    $tab_content .=         s7upf_product_item(
                                                                $style,
                                                                $item,
                                                                $animation_class,
                                                                $data_ani,
                                                                $thumb_style,
                                                                array(
                                                                    'quickview'     => array(
                                                                        'status'    => $quickview,
                                                                        'pos'       => $quickview_pos,
                                                                        'style'     => $quickview_style,
                                                                        ),
                                                                    'extra-link'    => array(
                                                                        'status'    => $extra_link,
                                                                        'style'     => $extra_style,
                                                                        )
                                                                    ),
                                                                $size,
                                                                $thumb_h_anmt,
                                                                '',
                                                                $label
                                                            );
                                $time += $time_plus;
                            }
                        }
                        $tab_content .=         '</div>';
                        if($max_page > 1 && $load_more == 'show' && $type == 'type-grid'){
                        $tab_content .=         '<div class="btn-loadmore"><a href="#" class="load-ajax-btn" data-page="1" data-max_page="'.$max_page.'" data-load_data='."'".$data_loadjs.' '."'".'><i aria-hidden="true" class="fa fa-chevron-down"></i><strong> XEM THÊM</strong></a></div>';
                        }
                        $tab_content .=     '</div>';
                    }
                    $html .=    '<div class="product-tab-ajax ajax-loadmore-'.esc_attr($load_more).' '.esc_attr($color_class).'">
                                    <div class="tab-pro-ajax-header">';
                    if(!empty($title)) $html .= '<h2>'.esc_html($title).'</h2>';
                    if(!empty($des)) $html .= '<p>'.esc_html($des).'</p>';
                    $html .=            '<ul>
                                            '.$tab_html.'
                                        </ul>
                                    </div>
                                    <div class="tab-content">
                                        '.$tab_content.'
                                    </div>';
                    if(!empty($adv_image)) $html .= '<div class="banner-image banner-image7">
                                                        <a href="'.esc_url($adv_link).'">'.wp_get_attachment_image($adv_image,'full').'</a>
                                                    </div>';
                    $html .=    '</div>';
                }
                break;

            default:
                if(!empty($cats)){
                    if(!empty($color)) S7upf_Assets::add_css(
                                    '.'.$color_class.' .tab-pro-ajax-header li.active a::after,
                                    .'.$color_class.' .btn-loadmore a:hover,
                                    .'.$color_class.' .product-extra-link a:hover{background:'.$color.';}
                                    .'.$color_class.' .btn-loadmore a:hover{border-color:'.$color.';}
                                    .'.$color_class.' .tab-pro-ajax-header li.active a,.'.$color_class.' .product-title a:hover,
                                    .'.$color_class.' .product-price ins,.'.$color_class.' .product-price > span, .'.$color_class.' .quickview-link.plus:hover,
                                    .'.$color_class.' .tab-pro-ajax-header li a:hover,
                                    .'.$color_class.' .product-extra-link a{color:'.$color.';}.product-extra-link a:hover{color:#fff;}
                                    ');
                    // if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,667:3,768:2,1200:3';
                    if(empty($item)) $item = 5;
                    $pre = rand(1,100);
                    $tabs = explode(",",$cats);
                    $tab_html = $tab_content = '';
                    foreach ($tabs as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            if($key == 0) $active = 'active';
                            else $active = '';
                            $key_adv = $key+1;
                            $tab_html .=    '<li class="'.esc_attr($active).'"><a href="#'.esc_attr($pre.$term->slug).'" data-toggle="tab">'.$term->name.'</a></li>';
                            $tab_content .=    '<div id="'.$pre.$term->slug.'" class="tab-pane content-load-wrap '.$active.'">
                                                    <div class="clearfix content-load-ajax '.esc_attr($type).'" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                            unset($args['tax_query']);
                            $args['tax_query'][]=array(
                                'taxonomy'=>'product_cat',
                                'field'=>'slug',
                                'terms'=> $tab
                            );
                            $data_load['cats'] = $tab;
                            $data_loadjs = json_encode($data_load);
                            $product_query = new WP_Query($args);
                            $max_page = $product_query->max_num_pages;
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    $data_ani = ' data-anim-delay="'.$time.'" data-anim-type="'.$animation.'"';
                                    global $product;                                    
                                        $tab_content .=         s7upf_product_item(
                                                                    $style,
                                                                    $item,
                                                                    $animation_class,
                                                                    $data_ani,
                                                                    $thumb_style,
                                                                    array(
                                                                        'quickview'     => array(
                                                                            'status'    => $quickview,
                                                                            'pos'       => $quickview_pos,
                                                                            'style'     => $quickview_style,
                                                                            ),
                                                                        'extra-link'    => array(
                                                                            'status'    => $extra_link,
                                                                            'style'     => $extra_style,
                                                                            )
                                                                        ),
                                                                    $size,
                                                                    $thumb_h_anmt,
                                                                    '',
                                                                    $label
                                                                );
                                    $time += $time_plus;
                                }
                            }
                            $tab_content .=         '</div>';
                            if($max_page > 1 && $load_more == 'show' && $type == 'type-grid'){
                            $tab_content .=         '<div class="btn-loadmore"><a href="#" class="load-ajax-btn" data-page="1" data-max_page="'.$max_page.'" data-load_data='."'".$data_loadjs.' '."'".'><i aria-hidden="true" class="fa fa-chevron-down"></i><strong> XEM THÊM</strong></a></div>';
                            }
                            $tab_content .=     '</div>';
                        }
                    }
                    $html .=    '<div class="product-tab-ajax ajax-loadmore-'.esc_attr($load_more).' '.esc_attr($color_class).'">
                                    <div class="tab-pro-ajax-header">';
                    if(!empty($title)) $html .= '<h2>'.esc_html($title).'</h2>';
                    if(!empty($des)) $html .= '<p>'.esc_html($des).'</p>';
                    $html .=            '<ul>
                                            '.$tab_html.'
                                        </ul>
                                    </div>
                                    <div class="tab-content">
                                        '.$tab_content.'
                                    </div>';
                    if(!empty($adv_image)) $html .= '<div class="banner-image banner-image7">
                                                        <a href="'.esc_url($adv_link).'">'.wp_get_attachment_image($adv_image,'full').'</a>
                                                    </div>';
                    $html .=    '</div>';
                }
                break;
        }
        wp_reset_postdata();
        return $html;
    }
}

stp_reg_shortcode('sv_product_list','sv_vc_product_list');
if(isset($_GET['return'])) $check_add = $_GET['return'];
if(empty($check_add)) add_action( 'vc_before_init_base','sv_add_list_product',10,100 );
if ( ! function_exists( 'sv_add_list_product' ) ) {
    function sv_add_list_product(){
        vc_map( array(
            "name"      => esc_html__("SV Product list", 'kuteshop'),
            "base"      => "sv_product_list",
            "icon"      => "icon-st",
            "category"  => '7Up-theme',
            "params"    => array(
                array(
                    'heading'     => esc_html__( 'Style', 'kuteshop' ),
                    'holder'      => 'div',
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    'param_name'  => 'style',
                    'value'       => array(
                                        esc_html__('Tab Categories','kuteshop')     => 'item-product-ajax',
                                        esc_html__('Tab prices','kuteshop')         => 'item-product-ajax tab-price',
                                        esc_html__('Box Store','kuteshop')          => 'item-product4',
                                        esc_html__('Box More info','kuteshop')      => 'item-product4 item-more-info',
                                        esc_html__('Box Trending( home 2 )','kuteshop')      => 'item-product-trending2',
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Price tabs', 'kuteshop' ),
                    'type'        => 'textfield',
                    'description' => esc_html__( 'Enter price separate value by ",". Example 0,100,200', 'kuteshop' ),
                    'param_name'  => 'prices',
                    "dependency"  => array(
                                        "element"   => 'style',
                                        "value"   => array('item-product-ajax tab-price'),
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Display as type', 'kuteshop' ),
                    'holder'      => 'div',
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose type to display. Grid or Slider', 'kuteshop' ),
                    'param_name'  => 'type',
                    'value'       => array(
                                        esc_html__('Grid','kuteshop')     => 'type-grid',
                                        esc_html__('Slider','kuteshop')     => 'smart-slider',
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Box Color', 'kuteshop' ),
                    'type'        => 'colorpicker',
                    'param_name'  => 'color',
                ),
                array(
                    'heading'     => esc_html__( 'Load more button', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    'param_name'  => 'load_more',
                    'value'       => array(
                                        esc_html__('Hidden','kuteshop')        => '',
                                        esc_html__('Show','kuteshop')          => 'show',
                                    ),
                    "dependency"  => array(
                                        "element"   => 'type',
                                        "value"   => array(
                                            'type-grid',
                                            ),
                                        )
                ),
                array(
                    'heading'     => esc_html__( 'Title', 'kuteshop' ),
                    'type'        => 'textfield',
                    'param_name'  => 'title',
                ),                
                array(
                    'heading'     => esc_html__( 'Color Title', 'kuteshop' ),
                    'type'        => 'colorpicker',
                    'param_name'  => 'color_title',
                ),
                array(
                    'heading'     => esc_html__( 'Sub Title', 'kuteshop' ),
                    'type'        => 'textfield',
                    'param_name'  => 'sub_title',
                    "dependency"  => array(
                                        "element"   => 'style',
                                        "value"   => array(
                                            'item-product4',
                                            ),
                                        )
                ),
                array(
                    'heading'     => esc_html__( 'Description', 'kuteshop' ),
                    'type'        => 'textfield',
                    'param_name'  => 'des',
                ),
                array(
                    'heading'     => esc_html__( 'View link', 'kuteshop' ),
                    'type'        => 'textfield',
                    'param_name'  => 'view_link',
                ),
                array(
                    'heading'     => esc_html__( 'Number', 'kuteshop' ),
                    'type'        => 'textfield',
                    'description' => esc_html__( 'Enter number of product. Default is 8.', 'kuteshop' ),
                    'param_name'  => 'number',
                ),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Item / Slider(Row)",'kuteshop'),
                    "param_name"    => "item",
                    'description' => esc_html__( 'Enter number of item. Default is auto with style display.', 'kuteshop' ),
                ),
                array(
                    'heading'     => esc_html__( 'Product Type', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'param_name'  => 'product_type',
                    'value' => array(
                        esc_html__('Default','kuteshop')            => '',
                        esc_html__('Trendding','kuteshop')          => 'trendding',
                        esc_html__('Featured Products','kuteshop')  => 'featured',
                        esc_html__('Best Sellers','kuteshop')       => 'bestsell',
                        esc_html__('On Sale','kuteshop')            => 'onsale',
                        esc_html__('Top rate','kuteshop')           => 'toprate',
                        esc_html__('Most view','kuteshop')          => 'mostview',
                    ),
                    'description' => esc_html__( 'Select Product View Type', 'kuteshop' ),
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
                    'type' => 'dropdown',
                    'heading' => esc_html__( 'Animation appear', 'kuteshop' ),
                    'param_name' => 'animation',
                    'value' => s7upf_get_list_animation()
                ),
                array(
                    "type" => "textfield",
                    "heading" => esc_html__("Time Delay", 'kuteshop'),
                    "param_name" => "time_delay",
                    "description" => esc_html__( "Set time delay. Unit(ms). Example 500 or 500,300 to have different time for items inner element.", 'kuteshop' ),
                    "value" => '300',
                    "dependency"  => array(
                        "element"   => 'animation',
                        "not_empty"     => true     
                        )
                ),
                array(
                    'type'        => 'autocomplete',
                    'holder'      => 'div',
                    'heading'     => esc_html__( 'Product Tags', 'kuteshop' ),
                    'param_name'  => 'tags',
                    'settings' => array(
                        'multiple' => true,
                        'sortable' => true,
                        'values' => s7upf_get_product_taxonomy('product_tag'),
                    ),
                    'save_always' => true,
                    'description' => esc_html__( 'List of product brands', 'kuteshop' ),
                    'dependency'  => array(
                        'element'   => 'style',
                        'value'     => array(
                            'item-product4 item-more-info',
                            ),
                        )
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
                    'heading'     => esc_html__( 'Thumb Style', 'kuteshop' ),
                    'holder'      => 'div',
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'thumb_style',
                    'value'       => array(
                                        esc_html__('Default','kuteshop')                => '',
                                        esc_html__('Thumb second hover','kuteshop')     => 'thumb-hover',
                                        esc_html__('Thumb gallery','kuteshop')          => 'thumb-gallery',
                                    )
                ),
                array(
                    'type'          => 'dropdown',
                    'heading'       => esc_html__( 'Animation hover', 'kuteshop' ),
                    'param_name'    => 'thumb_h_anmt',
                    'value'         => s7upf_get_hover_animation(),
                    "group"         => esc_html__("Thumb Settings",'kuteshop'),
                ),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Size Thumbnail",'kuteshop'),
                    "param_name"    => "size",
                    "group"         => esc_html__("Thumb Settings",'kuteshop'),
                    'description'   => esc_html__( 'Enter site thumbnail to crop. [width]x[height]. Example is 300x300', 'kuteshop' ),
                ),
                array(
                    'heading'     => esc_html__( 'Label', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'label',
                    'value'       => array(
                                        esc_html__('Hidden','kuteshop')        => '',
                                        esc_html__('Show','kuteshop')          => 'show',
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Quickview', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'quickview',
                    'value'       => array(
                                        esc_html__('Show','kuteshop')          => 'show',
                                        esc_html__('Hidden','kuteshop')        => 'hidden',
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Quickview Style', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'quickview_style',
                    'value'       => array(
                                        esc_html__('Border bottom','kuteshop')          => '',
                                        esc_html__('Plus icon','kuteshop')              => 'plus',
                                    ),
                    'dependency'    => array(
                        'element'   => 'quickview',
                        'value'     => 'show',
                        )
                ),
                array(
                    'heading'     => esc_html__( 'Quickview Position', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'quickview_pos',
                    'value'       => array(
                                        esc_html__('Top','kuteshop')          => '',
                                        esc_html__('Middle','kuteshop')       => 'pos-middle',
                                        esc_html__('Bottom','kuteshop')       => 'pos-bottom',
                                    ),
                    'dependency'    => array(
                        'element'   => 'quickview',
                        'value'     => 'show',
                        )
                ),
                array(
                    'heading'     => esc_html__( 'Extra link', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'extra_link',
                    'value'       => array(
                                        esc_html__('Show','kuteshop')          => 'show',
                                        esc_html__('Hidden','kuteshop')        => 'hidden',
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Extra Style', 'kuteshop' ),
                    'holder'      => 'div',
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'extra_style',
                    'value'       => array(
                                        esc_html__('Style 1','kuteshop')          => '',
                                        esc_html__('Style 2','kuteshop')          => 'home6',
                                    ),
                    'dependency'    => array(
                        'element'   => 'extra_link',
                        'value'     => 'show',
                        )
                ),
                array(
                    "type"          => "attach_image",
                    "heading"       => esc_html__("Adv Image",'kuteshop'),
                    "param_name"    => "adv_image",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
                array(
                    "type"          => "dropdown",
                    "heading"       => esc_html__("Adv Image position",'kuteshop'),
                    "param_name"    => "adv_image_pos",
                    "value"         => array(
                        esc_html__("Left",'kuteshop')     => '',
                        esc_html__("Right",'kuteshop')     => 'right',
                        ),
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Adv Title",'kuteshop'),
                    "param_name"    => "adv_title",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Adv Link",'kuteshop'),
                    "param_name"    => "adv_link",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
                array(
                    "type"          => "dropdown",
                    "heading"       => esc_html__("Adv Position",'kuteshop'),
                    "param_name"    => "adv_pos",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                    'value'         => array(
                                        esc_html__('Left','kuteshop')          => '',
                                        esc_html__('Right','kuteshop')         => 'right',
                                    ),
                    "dependency"    => array(
                        "element"   => 'style',
                        "value"   => array(
                            'list-adv-pos',
                            ),
                        )
                ),
                array(
                    "type"          => "attach_image",
                    "heading"       => esc_html__("Adv Image 2",'kuteshop'),
                    "param_name"    => "adv_image2",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Adv Title 2",'kuteshop'),
                    "param_name"    => "adv_title2",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Adv Link 2",'kuteshop'),
                    "param_name"    => "adv_link2",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
                array(
                    "type"          => "attach_image",
                    "heading"       => esc_html__("Adv Image 3",'kuteshop'),
                    "param_name"    => "adv_image3",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Adv Title 3",'kuteshop'),
                    "param_name"    => "adv_title3",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Adv Link 3",'kuteshop'),
                    "param_name"    => "adv_link3",
                    "group"         => esc_html__("Advantage Settings",'kuteshop'),
                ),
            )
        ));
    }
}
add_action( 'wp_ajax_loadmore_product', 's7upf_loadmore_product' );
add_action( 'wp_ajax_nopriv_loadmore_product', 's7upf_loadmore_product' );
if(!function_exists('s7upf_loadmore_product')){
    function s7upf_loadmore_product() {
        $page = $_POST['page'];
        $load_data = $_POST['load_data'];
        $load_data = str_replace('\"', '"', $load_data);
        $load_data = json_decode($load_data,true);
        extract($load_data);
        $args = array(
            'post_type'         => 'product',
            'posts_per_page'    => $number,
            'orderby'           => $order_by,
            'order'             => $order,
            'paged'             => $page + 1,
            );
        if($product_type == 'trendding'){
            $args['meta_query'][] = array(
                    'key'     => 'trending_product',
                    'value'   => 'on',
                    'compare' => '=',
                );
        }
        if($product_type == 'toprate'){
            $args['meta_key'] = '_wc_average_rating';
            $args['orderby'] = 'meta_value_num';
            $args['meta_query'] = WC()->query->get_meta_query();
            $args['tax_query'][] = WC()->query->get_tax_query();
        }
        if($product_type == 'mostview'){
            $args['meta_key'] = 'post_views';
            $args['orderby'] = 'meta_value_num';
        }
        if($product_type == 'bestsell'){
            $args['meta_key'] = 'total_sales';
            $args['orderby'] = 'meta_value_num';
        }
        if($product_type=='onsale'){
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
        }
        if($product_type == 'featured'){
            $args['tax_query'][] = array(
                'taxonomy' => 'product_visibility',
                'field'    => 'name',
                'terms'    => 'featured',
                'operator' => 'IN',
            );
        }
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
        if(isset($price_min) && isset($price_max)){
            $args['post__in'] = s7upf_filter_price($price_min,$price_max);
        }
        // Khoa Anh add (de resize o trang chu luc load more)
		if(!empty($size)) $size = explode('x', $size);
        else $size = array(195,260);
		// End
        $product_query = new WP_Query($args);
        if($product_query->have_posts()) {
            while($product_query->have_posts()) {
                $product_query->the_post();
                $data_ani = ' data-anim-delay="'.$time.'" data-anim-type="'.$animation.'"';
                if($count % 2 == 1 && $style == 'item-product-trending2') echo '<div class="item-trending2">';
                echo s7upf_product_item(
                        $style_item,
                        $item,
                        $animation_class,
                        $data_ani,
                        $thumb_style,
                        array(
                            'quickview'     => array(
                                'status'    => $quickview,
                                'pos'       => $quickview_pos,
                                'style'     => $quickview_style,
                                ),
                            'extra-link'    => array(
                                'status'    => $extra_link,
                                'style'     => $extra_style,
                                )
                            ),
                        $size,
                        $thumb_h_anmt,
                        '',
                        $label
                    );
                if($count % 2 == 0 && $style == 'item-product-trending2') echo '</div>';
                $time += $time_plus;
                $count++;
            }
        }
        wp_reset_postdata();
    }
}
}