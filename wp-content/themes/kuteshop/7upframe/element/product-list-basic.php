<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 05/09/15
 * Time: 10:00 AM
 */
if(class_exists("woocommerce")){
if(!function_exists('sv_vc_product_list_basic'))
{
    function sv_vc_product_list_basic($attr, $content = false)
    {
        $html = $view_html = '';
        extract(shortcode_atts(array(
            'style'         => '',
            'title'         => '',
            'title2'        => '',
            'icon'          => '',
            'des'           => '',
            'number'        => '8',
            'cats'          => '',
            'tab_active'    => '1',
            'brands'        => '',
            'order_by'      => 'date',
            'order'         => 'DESC',
            'product_type'  => '',
			'col'           => '',
            'item'          => '',
            'image'          => '',
            'item_res'      => '',
            'speed'         => '',
            'size'          => '',
            'color13'       => '',
            'color16'       => '',
            'list_advs'     => '',
            'time'          => '',
            'time2'          => '',
            'link'          => '',
            'info_pos'      => 'tags-left'
        ),$attr));
		if(!empty($col)) $col = (int)$col;
        if(!empty($cats)) $cats = str_replace(' ', '', $cats);
        if(!empty($brands)) $brands = str_replace(' ', '', $brands);
        $tab_active = (int)$tab_active - 1;
        if($item > 10) $item = 10;
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
        switch ($style) {
            case 'list-custom':
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="content-from-cat">';
                if(!empty($title)) $html .=    '<h3 class="text-uppercase">'.esc_html($title).'</h3>';
                $html .=        '<div class="row">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        global $product;
                        $from = $product->get_regular_price();
                        $to = $product->get_price();
                        $percent = $percent_html =  '';
                        if($from != $to && $from > 0){
                            $percent = round(($from-$to)/$from*100);
                            $percent_html = '<div class="discount"><span><b>'.$percent.'%</b> '.esc_html__("off","kuteshop").'</span></div>';
                        }
                        if($count % 4 == 1){
                            $html .=    '<div class="custom-item-large col-md-3 col-sm-6 col-xs-6">
                                            <div class="item-sp item-lg">
                                                <div class="product-item">
                                                    <a href="'.esc_url(get_the_permalink()).'">
                                                        <div class="thumb">
                                                            <div class="thumb-box">
                                                                <div class="thumb-wrap">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="info-custom">
                                                            <h4 class="custom-title-product">'.get_the_title().'</h4>
                                                            '.$percent_html.'
                                                            '.s7upf_get_rating_html(true).'
                                                            '.s7upf_get_price_html().'
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>';
                        }
                        else{
                            if($count % 4 == 2) $html .=    '<div class="custom-list-small col-md-3 col-sm-6 col-xs-6">';
                            $html .=    '<div class="item-sp item-sm">
                                            <div class="product-item">
                                                <a href="'.esc_url(get_the_permalink()).'">
                                                    <div class="thumb">
                                                        <div class="thumb-box">
                                                            <div class="thumb-wrap">
                                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="info-custom">
                                                        <h4 class="custom-title-product">'.get_the_title().'</h4>
                                                        '.$percent_html.'
                                                        '.s7upf_get_rating_html().'
                                                        '.s7upf_get_price_html().'
                                                    </div>
                                                </a>
                                            </div>
                                        </div>';
                            if($count % 4 == 0 || $count == $count_query) $html .=    '</div>';
                        }
                        $count++;
                    }
                }
                $html .=        '</div>
                            </div>';
                break;

            case 'list-slider18':
                if(empty($item) && empty($item_res)) $item_res = '0:1,560:2,980:3,1200:4';
                if(empty($item)) $item = 4;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="product-box18">';
                if(!empty($title)) $html .=    '<h2 class="title30 white text-center"><span class="bg-color">'.esc_html($title).'</span></h2>';
                $html .=        '<div class="product-slider18 border bg-white poly-slider">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $html .=        '<div class="item-product18">
                                            <div class="product-thumb">
                                                '.s7upf_thumb_hover_product($size,'only-image').'
                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                            </div>
                                            <h2 class="title14"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h2>
                                            <div class="product-info">                                                
                                                '.s7upf_get_rating_html().'
                                            </div>
                                            <div class="poly-box">
                                                '.s7upf_get_price_html().'
                                                '.s7upf_addtocart_link('home18').'
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'deal-slider18':
                if(empty($item) && empty($item_res)) $item_res = '0:1,667:2,1024:3';
                if(empty($item)) $item = 3;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="deal-box18">';
                if(!empty($title)) $html .=    '<h2 class="white title30 text-center"><span>'.esc_html($title).'</span></h2>';
                $html .=        '<div class="deal-slider18 poly-slider border bg-white">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $html .=        '<div class="deal-pro18">
                                            <div class="product-thumb">
                                                '.s7upf_thumb_hover_product($size,'only-image').'
                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                            </div>
                                            <h2 class="title14"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h2>
                                            <div class="product-info">
                                                '.s7upf_get_price_html().'
                                                '.s7upf_get_rating_html().'
                                            </div>
                                            <div class="deal-timer18 poly-box">';                        
                        if(!empty($time2)) $html .=     '<div class="flash-countdown" data-date="'.esc_attr($time2).'"></div>';
                        $html .=                s7upf_addtocart_link('home18').'
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'deal-slider17':
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,768:3,980:4,1200:5';
                if(empty($item)) $item = 5;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="product-box17">';
                if(!empty($title)) $html .=    '<h2 class="bg-color title18 title-box17"><span>'.esc_html($title).'</span></h2>';
                $html .=        '<div class="product-slider17 owl-slider17">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $html .=        '<div class="item-product deal-pro17">
                                            <div class="product-thumb">
                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                </a>
                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus">'.esc_html__("quick view","kuteshop").'</a>
                                                '.s7upf_product_link().'
                                            </div>
                                            <div class="product-info">';
                        if(!empty($time2)) $html .=     '<div class="detail-countdown" data-date="'.esc_attr($time2).'"></div>';
                        $html .=                '<h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                '.s7upf_get_price_html().'
                                                '.s7upf_get_rating_html().'
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'list-slider17':
                if(empty($item) && empty($item_res)) $item_res = '0:1,560:2,980:3,1200:4';
                if(empty($item)) $item = 4;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="product-box17">';
                if(!empty($title)) $html .=    '<h2 class="bg-color title18 title-box17"><span>'.esc_html($title).'</span></h2>';
                $html .=        '<div class="product-slider17 owl-slider17">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $html .=        '<div class="item-product17 deal-pro16">
                                            <div class="product-thumb">
                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                </a>
                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link pos-bottom"><span>'.esc_html__("quick view","kuteshop").'<span></a>
                                            </div>
                                            <div class="product-info">
                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                '.s7upf_get_price_html().'
                                                '.s7upf_saleoff_html().'
                                                '.s7upf_addtocart_link('home16').'
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'list-slider16':
                $data = (array) vc_param_group_parse_atts( $list_advs );
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,640:3,768:2,980:3,1200:4';
                if(empty($item)) $item = 4;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="product-box16 '.esc_attr($color16).'">
                                <div class="clearfix">';
                $html .=            '<div class="box-left16">
                                        <div class="bn-adv16 owl-slider16">
                                            <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                            if(is_array($data)){
                                foreach ($data as $key => $value) {
                                    $html .=    '<div class="item-bnadv16">
                                                    <div class="banner-thumb">
                                                        <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a>
                                                    </div>
                                                    <div class="banner-info '.esc_attr($value['color']).'">
                                                        <h2>'.esc_html($value['title']).'</h2>
                                                        <p>'.esc_html($value['des']).'</p>
                                                    </div>
                                                </div>';
                                }
                            }
                        $html .=            '</div>
                                        </div>
                                    </div>';
                $html .=            '<div class="box-right16">
                                        <div class="header-box16">';
                if(!empty($title)) $html .=    '<h2 class="title18 color"><span>'.esc_html($title).'</span></h2>';
                if(!empty($cats)){
                    $tabs = explode(",",$cats);
                    $html .=                '<div class="tabs16">';
                    foreach ($tabs as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            $term_link = get_term_link( $term->term_id, 'product_cat' );
                            $html .=            '<a href="'.esc_url($term_link).'">'.esc_html($term->name).'</a>';
                        }
                    }
                    $html .=                '</div>';
                }
                $html .=                '</div>';
                $html .=                '<div class="product-slider16 owl-slider16">
                                            <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $html .=                '<div class="item-product16">
                                                    <div class="product-thumb">
                                                        <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                            '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                        </a>
                                                        <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link pos-bottom"><span>'.esc_html__("quick view","kuteshop").'<span></a>
                                                    </div>
                                                    <div class="product-info">
                                                        <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                        '.s7upf_get_price_html().'
                                                        '.s7upf_get_rating_html().'
                                                        '.s7upf_addtocart_link('home16').'
                                                    </div>
                                                </div>';
                    }
                }
                $html .=                    '</div>';
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'cat-tab-home16':
                if(!empty($cats)){
                    if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,980:3';
                    if(empty($item)) $item = 3;
                    if(empty($size)) $size = array(195,260);
                    $pre = rand(1,100);
                    $tabs = explode(",",$cats);
                    if($tab_active > count($tabs)) $tab_active = 1;
                    $tab_html = $tab_content = '';
                    foreach ($tabs as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            if($key == $tab_active) $active = 'active';
                            else $active = '';
                            $key_adv = $key+1;
                            $tab_html .=    '<li class="'.esc_attr($active).'"><a href="#'.esc_attr($pre.$term->slug).'" data-toggle="tab">'.$term->name.'</a></li>';
                            $tab_content .=    '<div id="'.$pre.$term->slug.'" class="tab-pane '.$active.'">
                                                    <div class="dealpro-slider16">
                                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                            unset($args['tax_query']);
                            $args['tax_query'][]=array(
                                'taxonomy'=>'product_cat',
                                'field'=>'slug',
                                'terms'=> $tab
                            );
                            $product_query = new WP_Query($args);
                            $max_page = $product_query->max_num_pages;
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    global $product;         
                                    $tab_content .=         '<div class="deal-pro16">
                                                                <div class="product-thumb">
                                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                    </a>
                                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link pos-bottom">'.esc_html__("quick view","kuteshop").'</a>
                                                                </div>
                                                                <div class="product-info">
                                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                    '.s7upf_get_price_html().'
                                                                    '.s7upf_saleoff_html().'
                                                                    '.s7upf_addtocart_link('home16').'
                                                                </div>
                                                            </div>';;
                                }
                            }
                            $tab_content .=             '</div>
                                                    </div>
                                                </div>';
                        }
                    }
                    $html .=    '<div class="deal-box16">';
                    if(!empty($title)) $html .= '<h2 class="title-deal16 title24 bg-color white">'.esc_html($title).'</h2>';
                    $html .=        '<div class="title-tab16">
                                        <ul class="list-none">
                                            '.$tab_html.'
                                        </ul>';
                    if(!empty($link)) $html .=        '<a href="'.esc_url($link).'" class="viewall">'.esc_html__("View All","kuteshop").'</a>';
                    $html .=        '</div>';
                    if(!empty($time2)) $html .=        '<div class="deals-cowndown" data-date="'.esc_attr($time2).'"></div>';
                    $html .=        '<div class="tab-content">
                                        '.$tab_content.'
                                    </div>
                                </div>';
                }
                break;

            case 'side-slider15':
                if(empty($item) && empty($item_res)) $item_res = '0:1,560:2,768:1';
                if(empty($item)) $item = 1;
                if(empty($size)) $size = array(120,120);
				if(empty($col)) $col = 3;
                $html .=    '<div class="product-type15">';
                if(!empty($title)) $html .=    '<h2 class="title18 text-center white bg-color">'.esc_html($title).'</h2>';
                $html .=        '<div class="protype-slider15">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();                 
                        if($count % $col == 1 || $col == 1) $html .= '<div class="list-pro-seller">';                        
                        $html .=            '<div class="item-pro-seller">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                </div>
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html().'
                                                    '.s7upf_get_rating_html().'
                                                </div>
                                            </div>';
                        if($count % $col == 0 || $col == 1 || $count == $count_query) $html .= '</div>';
                        $count++;
                    }
                }
                $html .=            '</div>';
                $html .=        '</div>
                            </div>';
                break;

            case 'list-grid15':
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="top-selling15">
                                <div class="container">';
                if(!empty($title)) {
                    $html .=    '<div class="text-center title-box15 wow zoomIn">
                                    <h2 class="title30 color">'.esc_html($title).'</h2>';
                    if(!empty($des)) $html .=    '<p class="desc">'.esc_html($des).'</p>';
                    $html .=    '</div>';
                }
                $html .=            '<div class="list-topsale15">
                                        <div class="row">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();                 
                        $html .=            '<div class="col-md-6 col-sm-6 col-xs-12">
                                                <div class="item-product15 wow slideInUp">
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-12 col-xs-6">
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-12 col-xs-6">
                                                            <div class="product-info">
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_price_html().'
                                                                '.s7upf_get_rating_html().'
                                                                <p class="desc">'.s7upf_substr(get_the_excerpt(),0,60).'</p>
                                                                '.s7upf_product_link('product-extra-link5-2').'
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>';
                    }
                }
                $html .=                '</div>';
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'list-grid14':
                if(empty($size)) $size = array(360,360);
                $html .=    '<div class="product-grid14">';
                if(!empty($title)) {
                    $html .=    '<div class="box-title14">
                                    <h2 class="title60">'.esc_html($title).'</h2>';
                    if(!empty($des)) $html .=    '<p class="desc">'.esc_html($des).'</p>';
                    if(!empty($link)) $html .=    '<a href="'.esc_url($link).'" class="more"> '.esc_html("More","kuteshop").'</a>';
                    $html .=    '</div>';
                }
                $html .=        '<div class="list-product14">
                                    <div class="row">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();                 
                        $html .=        '<div class="col-md-4 col-sm-6 col-xs-12">
                                            <div class="item-product14 wow slideInUp">';
                        if(!empty($image)) $html .=        '<a href="'.esc_url(get_the_permalink()).'" class="logo-icon">'.wp_get_attachment_image($image,'full',0,array('class'=>'wobble-horizontal')).'</a>';
                        $html .=                s7upf_thumb_hover_product($size).'
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html().'
                                                    '.s7upf_get_rating_html().'
                                                </div>
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>';
                $html .=        '</div>
                            </div>';
                break;

            case 'list-slider14':
                if(empty($item) && empty($item_res)) $item_res = '0:1,640:2,980:3';
                if(empty($item)) $item = 1;
                if(empty($size)) $size = array(160,160);
                $html .=    '<div class="deal-box14">';
                if(!empty($title)) $html .=    '<div class="box-title14">
                                                    <h2 class="title60">'.esc_html($title).'</h2>
                                                    <p class="desc">'.esc_html($des).'</p>
                                                </div>';
                $html .=        '<div class="prodeal-slider14">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 2 == 1) $html .=    '<div class="item-deal14">';
                        $html .=    '<div class="pro-deal14">
                                        '.s7upf_saleoff_html().'
                                        <div class="row">
                                            <div class="col-xs-6">
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html().'
                                                    '.s7upf_get_rating_html().'
                                                    '.s7upf_product_link('','hidden-text').'
                                                </div>  
                                            </div>
                                            <div class="col-xs-6">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                        if($count % 2 == 0 || $count == $count_query) $html .=    '</div>';
                        $count++;
                    }
                }
                $html .=            '</div>';
                $html .=        '</div>
                            </div>';
                break;

            case 'list-grid13-2':
                if(empty($size)) $size = array(195,260);
                if(!empty($time)) $time = s7upf_get_deals_time($time);
                $data = (array) vc_param_group_parse_atts( $list_advs );

                $html .=    '<div class="product-box13 '.esc_attr($color13).'">';
                $html .=    '<div class="title-box13">
                                <h2 class="title30 white">'.esc_html($title).'</h2>
                            </div>';
                $html .=    '<div class="content-probox13">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 5 == 1){
                            $html .=    '<div class="top-probox13">
                                            <div class="clearfix">';
                            $html .=            '<div class="product-countdown">
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="product-info">
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_price_html('sale-style2').'
                                                                '.s7upf_get_rating_html();
                            if(!empty($time)){
                                $html .=                        '<div class="hotdeal5">
                                                                    <span>'.esc_html__("Ends in:","kuteshop").'</span>
                                                                    <div class="countdown-master flip-clock-wrapper" data-time="'.esc_attr($time).'"></div>
                                                                </div>';
                            }
                            $html .=                            '<a href="'.esc_url(get_the_permalink()).'" class="btn-link13 btn-rect title14 white bg-color radius">'.esc_html__("shop now","kuteshop").'</a>
                                                                '.s7upf_addtocart_link('home13').'
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                            if($count == $count_query) $html .=        '</div></div>';
                        }
                        if($count % 5 == 2){
                            $html .=            '<div class="product-brand">
                                                    <div class="item-product13">
                                                        <div class="product-hoz13">
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                                            </div>
                                                            <div class="product-info">
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_price_html('sale-style2').'
                                                                '.s7upf_get_rating_html().'
                                                            </div>
                                                        </div>
                                                    </div>';
                            if(isset($data[0]['link'])){
                            $html .=                '<div class="brand13">
                                                        <a href="'.esc_url($data[0]['link']).'">'.wp_get_attachment_image($data[0]['image'],'full',0,array('class'=>'wobble-horizontal')).'</a>
                                                    </div>';
                                                }
                            $html .=                '<a href="'.esc_url(get_the_permalink()).'" class="btn-link13 btn-rect title14 white bg-color radius">'.esc_html__("shop now","kuteshop").'</a>
                                                    '.s7upf_addtocart_link('home13').'
                                                </div>';
                            $html .=        '</div>
                                        </div>';
                        }
                        if($count % 5 == 3 || $count % 5 == 4 || $count % 5 == 0){
                            if($count % 5 == 3){
                            $html .=    '<div class="bottom-probox13 box-ver13">
                                            <div class="clearfix">
                                                <div class="product-list13">';
                            }
                            $html .=    '<div class="item-product13">
                                            <div class="product-ver13">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                                </div>
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html('sale-style2').'
                                                    '.s7upf_get_rating_html().'
                                                </div>
                                            </div>
                                        </div>';
                            if($count % 5 == 0 || $count_query == $count){
                                $html .=        '</div>';
                                if(isset($data[1]['link'])){
                                $html .=        '<div class="adv-box13">
                                                    <div class="adv-thumb">
                                                        <a href="'.esc_url($data[1]['link']).'">'.wp_get_attachment_image($data[1]['image'],'full').'</a>
                                                    </div>
                                                    <h2 class="title18 color">'.esc_html($data[1]['title']).'</h2>
                                                    <p class="desc">'.esc_html($data[1]['des']).'</p>
                                                    <a href="'.esc_url($data[1]['link']).'" class="shopnow">'.esc_html__("shop now","kuteshop").'</a>
                                                </div>';
                                }
                                $html .=    '</div>
                                        </div>';
                            }
                        }                        
                        $count++;
                    }
                }
                $html .=        '</div>';
                $html .=    '</div>';
                break;

            case 'list-grid13':
                if(empty($size)) $size = array(195,260);
                if(!empty($time)) $time = s7upf_get_deals_time($time);
                $data = (array) vc_param_group_parse_atts( $list_advs );

                $html .=    '<div class="product-box13 '.esc_attr($color13).'">';
                $html .=    '<div class="title-box13">
                                <h2 class="title30 white">'.esc_html($title).'</h2>
                            </div>';
                $html .=    '<div class="content-probox13">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 6 == 1){
                            $html .=    '<div class="top-probox13">
                                            <div class="clearfix">';
                            $html .=            '<div class="product-countdown">
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 col-xs-12">
                                                            <div class="product-info">
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_price_html('sale-style2').'
                                                                '.s7upf_get_rating_html();
                            if(!empty($time)){
                                $html .=                        '<div class="hotdeal5">
                                                                    <span>'.esc_html__("Ends in:","kuteshop").'</span>
                                                                    <div class="countdown-master flip-clock-wrapper" data-time="'.esc_attr($time).'"></div>
                                                                </div>';
                            }
                            $html .=                            '<a href="'.esc_url(get_the_permalink()).'" class="btn-link13 btn-rect title14 white bg-color radius">'.esc_html__("shop now","kuteshop").'</a>
                                                                '.s7upf_addtocart_link('home13').'
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>';
                            if($count == $count_query) $html .=        '</div></div>';
                        }
                        if($count % 6 == 2){
                            $html .=            '<div class="product-brand">
                                                    <div class="item-product13">
                                                        <div class="product-hoz13">
                                                            <div class="product-thumb">
                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                </a>
                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                                            </div>
                                                            <div class="product-info">
                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                '.s7upf_get_price_html('sale-style2').'
                                                                '.s7upf_get_rating_html().'
                                                            </div>
                                                        </div>
                                                    </div>';
                            if(isset($data[0]['link'])){
                            $html .=                '<div class="brand13">
                                                        <a href="'.esc_url($data[0]['link']).'">'.wp_get_attachment_image($data[0]['image'],'full',0,array('class'=>'wobble-horizontal')).'</a>
                                                    </div>';
                                                }
                            $html .=                '<a href="'.esc_url(get_the_permalink()).'" class="btn-link13 btn-rect title14 white bg-color radius">'.esc_html__("shop now","kuteshop").'</a>
                                                    '.s7upf_addtocart_link('home13').'
                                                </div>';
                            $html .=        '</div>
                                        </div>';
                        }
                        if($count % 6 == 3 || $count % 6 == 4 || $count % 6 == 5 || $count % 6 == 0){
                            if($count % 6 == 3){
                            $html .=    '<div class="bottom-probox13">
                                            <div class="clearfix">
                                                <div class="product-list13">';
                            }
                            $html .=    '<div class="item-product13">
                                            <div class="product-hoz13">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                                </div>
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html('sale-style2').'
                                                    '.s7upf_get_rating_html().'
                                                </div>
                                            </div>
                                        </div>';
                            if($count % 6 == 0 || $count_query == $count){
                                $html .=        '</div>';
                                if(isset($data[1]['link'])){
                                $html .=        '<div class="adv-box13">
                                                    <div class="adv-thumb">
                                                        <a href="'.esc_url($data[1]['link']).'">'.wp_get_attachment_image($data[1]['image'],'full').'</a>
                                                    </div>
                                                    <h2 class="title18 color">'.esc_html($data[1]['title']).'</h2>
                                                    <p class="desc">'.esc_html($data[1]['des']).'</p>
                                                    <a href="'.esc_url($data[1]['link']).'" class="shopnow">'.esc_html__("shop now","kuteshop").'</a>
                                                </div>';
                                }
                                $html .=    '</div>
                                        </div>';
                            }
                        }                        
                        $count++;
                    }
                }
                $html .=        '</div>';
                $html .=    '</div>';
                break;

            case 'list-slider13':
                if(empty($item) && empty($item_res)) $item_res = '0:1';
                if(empty($item)) $item = 1;
                if(empty($size)) $size = array(195,260);
                if(!empty($time)) $time = s7upf_get_deals_time($time);
                $html .=    '<div class="hotdeal-box13">';
                if(!empty($title)) $html .=    '<div class="title-box13">
                                                    <h2 class="title30 white"><span>'.esc_html($title).'</span></h2>
                                                </div>';
                $html .=        '<div class="dealpro-slider13 banner-slider13">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $des_content = get_post_meta(get_the_ID(),'des_content',true);
                        if(empty($des_content)) $des_content = get_the_excerpt();
                        $html .=        '<div class="content-deal13">
                                            <div class="row">
                                                <div class="col-md-7 col-sm-12 col-xs-12">
                                                    <div class="product-countdown">
                                                        <div class="row">
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <div class="product-thumb">
                                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                    </a>
                                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6 col-sm-6 col-xs-12">
                                                                <div class="product-info">
                                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                    '.s7upf_get_price_html('sale-style2').'
                                                                    '.s7upf_get_rating_html();
                        if(!empty($time)){
                            $html .=                                '<div class="hotdeal5">
                                                                        <span>'.esc_html__("Ends in:","kuteshop").'</span>
                                                                        <div class="countdown-master flip-clock-wrapper" data-time="'.esc_attr($time).'"></div>
                                                                    </div>';
                        }
                        $html .=                                    '<a href="'.esc_url(get_the_permalink()).'" class="btn-link13 btn-rect title14 white bg-color radius">'.esc_html__("shop now","kuteshop").'</a>
                                                                    '.s7upf_addtocart_link('home13').'
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-5 col-sm-12 col-xs-12">
                                                    <div class="product-moreinfo">
                                                        '.$des_content.'
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>';
                $html .=        '</div>
                            </div>';
                break;

            case 'list-slider12':
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,768:3,980:4,1200:5';
                if(empty($item)) $item = 5;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="hot-product12">';
                if(!empty($title)) $html .=    '<h2 class="title-hot12 title24 text-center"><span>'.esc_html($title).'</span></h2>';
                $html .=        '<div class="product-slider12">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();                 
                        $html .=        '<div class="item-product">
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
                $html .=            '</div>';
                $html .=        '</div>
                            </div>';
                break;

            case 'side-slider12':
                if(empty($item) && empty($item_res)) $item_res = '0:1';
                if(empty($item)) $item = 1;
                if(empty($size)) $size = array(90,90);
				if(empty($col)) $col = 4;
                // Khoa Anh
                $html .=    '<div class="supper-deal12 top-review11" style="background-color: white;">';
				// End
                if(!empty($title)) $html .=    '<h2 class="title14 title-top12">'.esc_html($title).'</h2>';
                $html .=        '<div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();               
                        if($count % $col == 1 || $col == 1) $html .= '<div class="list-pro-seller">';
                        $html .=            '<div class="item-pro-seller">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                </div>
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html('sale-style2').'
                                                </div>
                                            </div>';
                        if($count % $col == 0 || $col == 1 || $count == $count_query) $html .= '</div>';
                        $count++;
                    }
                }
                $html .=            '</div>';
                $data = (array) vc_param_group_parse_atts( $list_advs );
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<div class="banner-zoom">
                                        <a href="'.esc_url($value['link']).'" class="thumb-zoom">'.wp_get_attachment_image($value['image'],'full').'</a>
                                    </div>';
                    }
                }
                $html .=    '</div>';
                break;

            case 'side-slider11':
                if(empty($item) && empty($item_res)) $item_res = '0:1';
                if(empty($item)) $item = 1;
                if(empty($size)) $size = array(90,90);
				if(empty($col)) $col = 5;
                $html .=    '<div class="box-side11 top-review11">';
                if(!empty($title)) $html .=    '<h2 class="title24">'.esc_html($title).'</h2>';
                $html .=        '<div class="widget-content">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % $col == 1 || $col == 1) $html .= '<div class="list-pro-seller">';
                        $html .=            '<div class="item-pro-seller">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                </div>
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html().'
                                                    '.s7upf_get_rating_html().'
                                                </div>
                                            </div>';
                        if($count % $col == 0 || $col == 1 || $count == $count_query) $html .= '</div>';
                        $count++;
                    }
                }
                $html .=            '</div>';
                $html .=        '</div>
                            </div>';
                break;

            case 'list-slider11':
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,768:3,992:4,1200:5';
                if(empty($item)) $item = 5;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="best-sale11">';
                if(!empty($title)) $html .=    '<h2 class="title24">'.esc_html($title).'</h2>';
                $html .=        '<div class="bestsale-slider11">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();                 
                        $html .=        '<div class="item-product item-betsale11">
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
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>';
                $html .=        '</div>
                            </div>';
                break;

            case 'deals-home11':
                if(empty($item) && empty($item_res)) $item_res = '0:1,360:2,560:3,768:4,1024:2,1200:3';
                if(empty($item)) $item = 3;
                if(empty($size)) $size = array(105,105);
                $html .=    '<div class="item-superdeal11">';
                if(!empty($title)) $html .=    '<h2>'.esc_html($title).'</h2>';
                if(!empty($title2)) $html .=    '<p>'.esc_html($title2).'</p>';
                $html .=        '<div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();                 
                        $html .=    '<div class="deal-pro11">
                                        <div class="product-thumb">
                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                            </a>
                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link pos-bottom"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                        </div>
                                    </div>';
                    }
                }
                $html .=        '</div>';
                if(!empty($link)) $html .=    '<a href="'.esc_url($link).'" class="btn-rect radius text-uppercase">'.esc_html__("View All","kuteshop").'</a>';
                $html .=    '</div>';
                break;

            case 'side-slider10':
                if(empty($item) && empty($item_res)) $item_res = '0:1,568:2,768:1';
                if(empty($item)) $item = 1;
                if(empty($size)) $size = array(90,90);
				if(empty($col)) $col = 4;
                $html .=    '<div class="widget widget-seller">';
                if(!empty($title)) $html .=    '<h2 class="widget-title title14">'.esc_html($title).'</h2>';
                $html .=        '<div class="widget-content">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % $col == 1 || $col == 1) $html .= '<div class="list-pro-seller">';
                        $html .=            '<div class="item-pro-seller">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                </div>
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html().'
                                                    '.s7upf_get_rating_html().'
                                                </div>
                                            </div>';
                        if($count % $col == 0 || $col == 1 || $count == $count_query) $html .= '</div>';
                        $count++;
                    }
                }
                $html .=            '</div>';
                if(!empty($link)) $html .=    '<a href="'.esc_url($link).'" class="allreview">'.esc_html__("See All","kuteshop").'</a>';
                $html .=        '</div>
                            </div>';
                break;

            case 'deals-home10':
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,768:1';
                if(empty($item)) $item = 1;
                if(empty($size)) $size = array(195,260);
                // $time = s7upf_get_deals_time($time);
                $html .=    '<div class="deal-banner10">
                                <div class="deal-title10">';
                if(!empty($title)) $html .= '<h2 class="title18">'.esc_html($title).'</h2>';                
                if(!empty($time2)){
                    $html .=        '<div class="deal-countdown10">
                                        <div class="flash-countdown" data-date="'.esc_attr($time2).'"></div>
                                    </div>';
                }
                $html .=        '</div>';
                $html .=        '<div class="deal-product10">';                
                $html .=            '<div class="deal-slider10">
                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        global $product;
                        $html .=            '<div class="item-product item-product10">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus"><span>'.esc_html__("quick view","kuteshop").'</span></a>
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
                $html .=                '</div>
                                    </div>';
                if(!empty($link)) $html .=   '<a href="'.esc_url($link).'" class="alldeal">'.esc_html__("All Deals","kuteshop").'</a>';
                $html .=        '</div>
                            </div>';
                break;

            case 'list-megamenu':
                if(empty($item) && empty($item_res)) $item_res = '';
                if(empty($item)) $item =2;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="mega-new-arrival">';
                if(!empty($title)) $html .= '<h2 class="mega-menu-title">'.esc_html($title).'</h2>';
                $html .=        '<div class="mega-new-arrival-slider">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $html .=    '<div class="item">
                                        <div class="item-product-ajax item-product">
                                            <div class="product-thumb">
                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                </a>
                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                '.s7upf_product_link('','hidden-text').'
                                            </div>
                                            <div class="product-info">
                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                '.s7upf_get_price_html().'
                                            </div>
                                        </div>
                                    </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'cat-list-home9-3':
                if(empty($item) && empty($item_res)) $item_res = '';
                if(empty($item)) $item = 4;
                if(empty($size)) $size = array(170,170);
                $html .=    '<div class="product-box9">
                                <div class="header-box6 title-box6">
                                    <div class="clearfix">';
                if(!empty($title)) $html .= '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=                '<ul class="list-none">';
                if(isset($custom_list)){
                    foreach ($custom_list as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            $term_link = get_term_link( $term->term_id, 'product_cat' );
                            $cat_thumb_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
                            $html .=        '<li>
                                                <a href="'.esc_url($term_link).'">
                                                    '.wp_get_attachment_image($cat_thumb_id,array(20,20)).'
                                                    <span>'.$term->name.'</span>
                                                </a>
                                            </li>';
                        }
                    }
                }
                $html .=                '</ul>
                                    </div>
                                </div>';
                $html .=        '<div class="content-product-box9 clearfix">'; 
                $data = (array) vc_param_group_parse_atts( $list_advs );               
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 6 == 1) $html .=    '<div class="box-left9">';
                        if($count % 6 == 1 && isset($data[0])) $html .=    '<div class="large-banner9">
                                                            <div class="banner-box">
                                                                <a href="'.esc_url($data[0]['link']).'" class="link-banner-box">'.wp_get_attachment_image($data[0]['image'],'full').'</a>
                                                            </div>
                                                        </div>';
                        if($count % 6 == 5) $html .=    '<div class="box-right9">';
                        if($count % 6 == 5 && isset($data[1])) $html .=   '<div class="small-banner9">
                                            <div class="adv-thumb">
                                                <a href="'.esc_url($data[1]['link']).'">'.wp_get_attachment_image($data[1]['image'],'full').'</a>
                                            </div>
                                            <div class="info-banner-small9 '.esc_attr($data[1]['color']).'">
                                                <h2 class="title18">'.esc_html($data[1]['title']).'</h2>
                                                <p>'.esc_html($data[1]['des']).'</p>
                                            </div>
                                        </div>';
                        if($count % 6 == 5 && isset($data[2])) $html .=   '<div class="small-banner9">
                                            <div class="adv-thumb">
                                                <a href="'.esc_url($data[2]['link']).'">'.wp_get_attachment_image($data[2]['image'],'full').'</a>
                                            </div>
                                            <div class="info-banner-small9 '.esc_attr($data[1]['color']).'">
                                                <h2 class="title18">'.esc_html($data[2]['title']).'</h2>
                                                <p>'.esc_html($data[2]['des']).'</p>
                                            </div>
                                        </div>';
                        $html .=                '<div class="item-product9 item-trend5">
                                                    <div class="hoz-item">
                                                        <div class="product-thumb">
                                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                            </a>
                                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                        </div>
                                                        <div class="product-info">
                                                            <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                            '.s7upf_get_price_html('sale-style').'
                                                            '.s7upf_get_rating_html().'
                                                            '.s7upf_product_link('','hidden-text').'
                                                        </div>
                                                    </div>
                                                </div>';
                        if($count % 6 == 4 || $count % 6 == 0 || $count == $count_query) $html .=    '</div>';                        
                        $count++;
                    }
                }
                $html .=        '</div>
                            </div>';
                break;

            case 'cat-list-home9-2':
                if(empty($item) && empty($item_res)) $item_res = '';
                if(empty($item)) $item = 4;
                if(empty($size)) $size = array(170,170);
                $html .=    '<div class="product-box9">
                                <div class="header-box6 title-box6">
                                    <div class="clearfix">';
                if(!empty($title)) $html .= '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=                '<ul class="list-none">';
                if(isset($custom_list)){
                    foreach ($custom_list as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            $term_link = get_term_link( $term->term_id, 'product_cat' );
                            $cat_thumb_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
                            $html .=        '<li>
                                                <a href="'.esc_url($term_link).'">
                                                    '.wp_get_attachment_image($cat_thumb_id,array(20,20)).'
                                                    <span>'.$term->name.'</span>
                                                </a>
                                            </li>';
                        }
                    }
                }
                $html .=                '</ul>
                                    </div>
                                </div>';
                $html .=        '<div class="content-product-box9 clearfix">'; 
                $data = (array) vc_param_group_parse_atts( $list_advs );               
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 6 == 1) $html .=    '<div class="box-left9">';
                        if($count % 6 == 1 && isset($data[0])) $html .=    '<div class="large-banner9">
                                                            <div class="banner-box">
                                                                <a href="'.esc_url($data[0]['link']).'" class="link-banner-box">'.wp_get_attachment_image($data[0]['image'],'full').'</a>
                                                            </div>
                                                        </div>';
                        if($count % 6 == 1 && isset($data[1])) $html .=   '<div class="small-banner9">
                                            <div class="adv-thumb">
                                                <a href="'.esc_url($data[1]['link']).'">'.wp_get_attachment_image($data[1]['image'],'full').'</a>
                                            </div>
                                            <div class="info-banner-small9 '.esc_attr($data[1]['color']).'">
                                                <h2 class="title18">'.esc_html($data[1]['title']).'</h2>
                                                <p>'.esc_html($data[1]['des']).'</p>
                                            </div>
                                        </div>';
                        if($count % 6 == 1 && isset($data[2])) $html .=   '<div class="small-banner9">
                                            <div class="adv-thumb">
                                                <a href="'.esc_url($data[2]['link']).'">'.wp_get_attachment_image($data[2]['image'],'full').'</a>
                                            </div>
                                            <div class="info-banner-small9 '.esc_attr($data[1]['color']).'">
                                                <h2 class="title18">'.esc_html($data[2]['title']).'</h2>
                                                <p>'.esc_html($data[2]['des']).'</p>
                                            </div>
                                        </div>';
                        if($count % 6 == 3) $html .=    '<div class="box-right9">';
                        $html .=                '<div class="item-product9 item-trend5">
                                                    <div class="hoz-item">
                                                        <div class="product-thumb">
                                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                            </a>
                                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                        </div>
                                                        <div class="product-info">
                                                            <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                            '.s7upf_get_price_html('sale-style').'
                                                            '.s7upf_get_rating_html().'
                                                            '.s7upf_product_link('','hidden-text').'
                                                        </div>
                                                    </div>
                                                </div>';
                        if($count % 6 == 0 || $count % 6 == 2 || $count == $count_query) $html .=    '</div>';
                        $count++;
                    }
                }
                $html .=        '</div>
                            </div>';
                break;

            case 'cat-list-home9':
                if(empty($item) && empty($item_res)) $item_res = '';
                if(empty($item)) $item = 4;
                if(empty($size)) $size = array(170,170);
                $html .=    '<div class="product-box9">
                                <div class="header-box6 title-box6">
                                    <div class="clearfix">';
                if(!empty($title)) $html .= '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=                '<ul class="list-none">';
                if(isset($custom_list)){
                    foreach ($custom_list as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            $term_link = get_term_link( $term->term_id, 'product_cat' );
                            $cat_thumb_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
                            $html .=        '<li>
                                                <a href="'.esc_url($term_link).'">
                                                    '.wp_get_attachment_image($cat_thumb_id,array(20,20)).'
                                                    <span>'.$term->name.'</span>
                                                </a>
                                            </li>';
                        }
                    }
                }
                $html .=                '</ul>
                                    </div>
                                </div>';
                $html .=        '<div class="content-product-box9 clearfix">'; 
                $data = (array) vc_param_group_parse_atts( $list_advs );               
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 8 == 1) $html .=    '<div class="box-left9">';
                        if($count % 8 == 1 && isset($data[0])) $html .=    '<div class="large-banner9">
                                                            <div class="banner-box">
                                                                <a href="'.esc_url($data[0]['link']).'" class="link-banner-box">'.wp_get_attachment_image($data[0]['image'],'full').'</a>
                                                            </div>
                                                        </div>';
                        if($count % 8 == 5) $html .=    '<div class="box-right9">';
                        $html .=                '<div class="item-product9 item-trend5">
                                                    <div class="hoz-item">
                                                        <div class="product-thumb">
                                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                            </a>
                                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                        </div>
                                                        <div class="product-info">
                                                            <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                            '.s7upf_get_price_html('sale-style').'
                                                            '.s7upf_get_rating_html().'
                                                            '.s7upf_product_link('','hidden-text').'
                                                        </div>
                                                    </div>
                                                </div>';
                        if($count % 8 == 0 || $count % 8 == 4 || $count == $count_query) $html .=    '</div>';
                        $count++;
                    }
                }
                $html .=        '</div>
                            </div>';
                break;

            case 'deals-home9':
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,667:3,1200:1';
                if(empty($item)) $item = 1;
                if(empty($size)) $size = array(195,260);
                // $time = s7upf_get_deals_time($time);
                $html .=    '<div class="deal-banner9">
                                <div class="deal-title9">';
                if(!empty($title)) $html .= '<h2 class="title18"><span class="color">'.esc_html($title).'</span> '.esc_html($title2).'</h2>';
                if(!empty($link)) $html .=   '<a href="'.esc_url($link).'"><strong class="color">'.$number.'</strong> '.esc_html__("items","kuteshop").'</a>';
                $html .=        '</div>';
                $html .=        '<div class="deal-product9">';
                if(!empty($time)){
                    $html .=        '<div class="deal-countdown9">
                                        <span>'.esc_html__("Deals end in:","kuteshop").' </span>
                                        <div class="flash-countdown" data-date="'.esc_attr($time).'"></div>
                                    </div>';
                }
                $html .=            '<div class="deal-pro-slider9">
                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        global $product;
                        $from = $product->get_regular_price();
                        $to = $product->get_price();
                        $percent = $percent_html =  '';
                        if($from != $to && $from > 0){
                            $percent = round(($from-$to)/$from*100);            
                            $percent_html = '<div class="product-sale">
                                                <span>'.$percent.'% off</span>
                                            </div>';
                        }
                        $html .=            '<div class="item-product">
                                                <div class="product-thumb">
                                                     <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus">'.esc_html__("quick view","kuteshop").'</a>
                                                    '.s7upf_product_link('','hidden-text').'
                                                </div>
                                                <div class="product-info">
                                                    '.s7upf_get_price_html().'
                                                    '.$percent_html.'
                                                </div>
                                            </div>';
                    }
                }
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'list-home8':
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,667:3,1024:4,1200:5';
                if(empty($item)) $item =5;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="product-order8-wrap fix-slider-nav8">
                                <div class="title-product-order8">';
                if(!empty($title)) $html .= '<h2 class="title18"><span>'.esc_html($title).'</span></h2>';
                if(!empty($link)) $html .=    '<a href="'.esc_url($link).'" class="seeall wobble-top">'.esc_html__("See All","kuteshop").'</a>';
                $html .=        '</div>';
                $html .=        '<div class="content-product-order8 product-slider8">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 2 == 1) $html .=    '<div class="item-product-order8">';
                        $html .=    '<div class="item-product">
                                        <div class="product-thumb">
                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                            </a>
                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                            '.s7upf_product_link('','hidden-text').'
                                        </div>
                                        <div class="product-info">
                                            '.s7upf_get_price_html().'
                                            <div class="product-order">
                                                <span>'.get_post_meta(get_the_ID(),'total_sales',true).' '.esc_html__("Orders","kuteshop").'</span>
                                            </div>
                                        </div>
                                    </div>';
                        if($count % 2 == 0 || $count == $count_query) $html .=    '</div>';
                        $count++;
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'deals-home8':
                if(empty($item) && empty($item_res)) $item_res = '0:1,640:2,992:3';
                if(empty($item)) $item = 3;
                if(empty($size)) $size = array(195,260);
                $size_small = array(170,170);
                // $time = s7upf_get_deals_time($time);
                $html .=    '<div class="tab-product8 fix-slider-nav8">
                                <div class="tab-title8">';
                if(!empty($title)) $html .= '<h2 class="title18"><span>'.esc_html($title).'</span></h2>';
                if(!empty($time)){
                    $html .=        '<div class="deal-countdown8">
                                        <span>'.esc_html__("Deals end in:","kuteshop").' </span>
                                        <div class="flash-countdown" data-date="'.esc_attr($time).'"></div>
                                    </div>';
                }
                $html .=        '</div>';
                $html .=        '<div class="tab-content8 tab-content">
                                    <div class="trend-slider5 product-slider8">
                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 5 == 3){
                        $html .=            '<div class="item-trend5">
                                                <div class="ver-item">
                                                    <div class="product-thumb">
                                                        <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                            '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                        </a>
                                                        <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                    </div>
                                                    <div class="product-info">
                                                        <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                        '.s7upf_get_price_html('sale-style').'
                                                        '.s7upf_product_link('','hidden-text').'
                                                        '.s7upf_get_rating_html().'
                                                    </div>
                                                </div>
                                            </div>';
                        }
                        else{
                            if($count % 5 == 1 || $count % 5 == 4) $html .=        '<div class="item-trend5">';
                            $html .=            '<div class="hoz-item">
                                                    <div class="product-thumb">
                                                        <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                            '.get_the_post_thumbnail(get_the_ID(),$size_small).'
                                                        </a>
                                                        <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                    </div>
                                                    <div class="product-info">
                                                        <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                        '.s7upf_get_price_html('sale-style').'
                                                        '.s7upf_get_rating_html().'
                                                        '.s7upf_product_link('','hidden-text').'
                                                    </div>
                                                </div>';
                            if($count % 5 == 2 || $count % 5 == 0 || $count == $count_query) $html .=        '</div>';
                        }
                        $count++;
                    }
                }
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'cat-tab-home8-5':
            case 'cat-tab-home8-4':
            case 'cat-tab-home8-3':
            case 'cat-tab-home8-2':
            case 'cat-tab-home8':
                if(!empty($cats)){
                    if(empty($item) && empty($item_res)) $item_res = '0:1,640:2,992:3';
                    if(empty($item)) $item = 3;
                    if(empty($size)) $size = array(195,260);
                    $size_small = array(170,170);
                    $pre = rand(1,100);
                    $tabs = explode(",",$cats);
                    if($tab_active > count($tabs)) $tab_active = 1;
                    $tab_html = $tab_content = '';
                    foreach ($tabs as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            if($key == $tab_active) $active = 'active';
                            else $active = '';
                            $key_adv = $key+1;
                            $tab_html .=    '<li class="'.esc_attr($active).'"><a href="#'.esc_attr($pre.$term->slug).'" data-toggle="tab">'.$term->name.'</a></li>';
                            $tab_content .=    '<div id="'.$pre.$term->slug.'" class="tab-pane '.$active.'">
                                                    <div class="trend-slider5 product-slider8">
                                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                            unset($args['tax_query']);
                            $args['tax_query'][]=array(
                                'taxonomy'=>'product_cat',
                                'field'=>'slug',
                                'terms'=> $tab
                            );
                            $product_query = new WP_Query($args);
                            $count = 1;
                            $count_query = $product_query->post_count;
                            $max_page = $product_query->max_num_pages;
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    switch ($style) {
                                        case 'cat-tab-home8-5':
                                            $tab_content .=     '<div class="item-trend5">
                                                                    <div class="ver-item">
                                                                        <div class="product-thumb">
                                                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                            </a>
                                                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                        </div>
                                                                        <div class="product-info">
                                                                            <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                            '.s7upf_get_price_html('sale-style').'
                                                                            '.s7upf_product_link('','hidden-text').'
                                                                            '.s7upf_get_rating_html().'
                                                                        </div>
                                                                    </div>
                                                                </div>';
                                            break;

                                        case 'cat-tab-home8-4':
                                            if($count % 2 == 1) $tab_content .=        '<div class="item-trend5">';
                                            $tab_content .=         '<div class="hoz-item">
                                                                        <div class="product-thumb">
                                                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                                '.get_the_post_thumbnail(get_the_ID(),$size_small).'
                                                                            </a>
                                                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                        </div>
                                                                        <div class="product-info">
                                                                            <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                            '.s7upf_get_price_html('sale-style').'
                                                                            '.s7upf_get_rating_html().'
                                                                            '.s7upf_product_link('','hidden-text').'
                                                                        </div>
                                                                    </div>';
                                            if($count % 2 == 0 || $count == $count_query) $tab_content .=        '</div>';
                                            break;

                                        case 'cat-tab-home8-3':
                                            if($count % 4 == 1 || $count % 4 == 0){
                                            $tab_content .=     '<div class="item-trend5">
                                                                    <div class="ver-item">
                                                                        <div class="product-thumb">
                                                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                            </a>
                                                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                        </div>
                                                                        <div class="product-info">
                                                                            <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                            '.s7upf_get_price_html('sale-style').'
                                                                            '.s7upf_product_link('','hidden-text').'
                                                                            '.s7upf_get_rating_html().'
                                                                        </div>
                                                                    </div>
                                                                </div>';
                                            }
                                            else{
                                                if($count % 4 == 2) $tab_content .=        '<div class="item-trend5">';
                                                $tab_content .=         '<div class="hoz-item">
                                                                            <div class="product-thumb">
                                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                                    '.get_the_post_thumbnail(get_the_ID(),$size_small).'
                                                                                </a>
                                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                            </div>
                                                                            <div class="product-info">
                                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                                '.s7upf_get_price_html('sale-style').'
                                                                                '.s7upf_get_rating_html().'
                                                                                '.s7upf_product_link('','hidden-text').'
                                                                            </div>
                                                                        </div>';
                                                if($count % 4 == 3 || $count == $count_query) $tab_content .=        '</div>';
                                            }
                                            break;

                                        case 'cat-tab-home8-2':
                                            if($count % 5 == 0){
                                            $tab_content .=     '<div class="item-trend5">
                                                                    <div class="ver-item">
                                                                        <div class="product-thumb">
                                                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                            </a>
                                                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                        </div>
                                                                        <div class="product-info">
                                                                            <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                            '.s7upf_get_price_html('sale-style').'
                                                                            '.s7upf_product_link('','hidden-text').'
                                                                            '.s7upf_get_rating_html().'
                                                                        </div>
                                                                    </div>
                                                                </div>';
                                            }
                                            else{
                                                if($count % 5 == 1 || $count % 5 == 3) $tab_content .=        '<div class="item-trend5">';
                                                $tab_content .=         '<div class="hoz-item">
                                                                            <div class="product-thumb">
                                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                                    '.get_the_post_thumbnail(get_the_ID(),$size_small).'
                                                                                </a>
                                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                            </div>
                                                                            <div class="product-info">
                                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                                '.s7upf_get_price_html('sale-style').'
                                                                                '.s7upf_get_rating_html().'
                                                                                '.s7upf_product_link('','hidden-text').'
                                                                            </div>
                                                                        </div>';
                                                if($count % 5 == 2 || $count % 5 == 4 || $count == $count_query) $tab_content .=        '</div>';
                                            }
                                            break;

                                        case 'cat-tab-home8':
                                            if($count % 5 == 1){
                                            $tab_content .=     '<div class="item-trend5">
                                                                    <div class="ver-item">
                                                                        <div class="product-thumb">
                                                                            <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                                '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                            </a>
                                                                            <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                        </div>
                                                                        <div class="product-info">
                                                                            <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                            '.s7upf_get_price_html('sale-style').'
                                                                            '.s7upf_product_link('','hidden-text').'
                                                                            '.s7upf_get_rating_html().'
                                                                        </div>
                                                                    </div>
                                                                </div>';
                                            }
                                            else{
                                                if($count % 5 == 2 || $count % 5 == 4) $tab_content .=        '<div class="item-trend5">';
                                                $tab_content .=         '<div class="hoz-item">
                                                                            <div class="product-thumb">
                                                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                                    '.get_the_post_thumbnail(get_the_ID(),$size_small).'
                                                                                </a>
                                                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                            </div>
                                                                            <div class="product-info">
                                                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                                '.s7upf_get_price_html('sale-style').'
                                                                                '.s7upf_get_rating_html().'
                                                                                '.s7upf_product_link('','hidden-text').'
                                                                            </div>
                                                                        </div>';
                                                if($count % 5 == 0 || $count % 5 == 3 || $count == $count_query) $tab_content .=        '</div>';
                                            }
                                            break;
                                        
                                        default:
                                            # code...
                                            break;
                                    }
                                    $count++;
                                }
                            }
                            $tab_content .=             '</div>
                                                    </div>
                                                </div>';
                        }
                    }
                    $html .=    '<div class="tab-product8 fix-slider-nav8">
                                    <div class="tab-title8">';
                    if(!empty($title)) $html .= '<h2 class="title18"><span>'.esc_html($title).'</span></h2>';
                    $html .=            '<ul class="list-none">
                                            '.$tab_html.'
                                        </ul>
                                    </div>
                                    <div class="tab-content8 tab-content">
                                        '.$tab_content.'
                                    </div>
                                </div>';
                }
                break;

            case 'cat-more-home7':
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,840:3,1200:4';
                if(empty($item)) $item = 4;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="category-box7">';
                if(!empty($title)) $html .= '<div class="header-cat-color">
                                                <h2 class="title18">'.esc_html($title).'</h2>
                                                <a href="'.esc_url($link).'" class="cat-color-link wobble-top">'.esc_html__("more","kuteshop").'</a>
                                            </div>';
                $html .=        '<div class="content-catbox7 '.esc_attr($info_pos).'">
                                    <div class="clearfix">';
                $html .=                '<div class="banner-tags7">';
                $data = (array) vc_param_group_parse_atts( $list_advs );
                if(isset($data[0])){
                $html .=                    '<div class="banner-zoom">
                                                <a href="'.esc_url($data[0]['link']).'" class="thumb-zoom">'.wp_get_attachment_image($data[0]['image'],'full').'</a>
                                                <div class="adv-info7">
                                                    <h2>'.esc_html($data[0]['title']).'</h2>
                                                </div>
                                            </div>';
                                        }
                $html .=                    '<div class="hotkey-cat-color">
                                                '.wpb_js_remove_wpautop($content, true).'
                                            </div>';                
                $html .=                '</div>';
                $html .=                '<div class="content-cat7 content-pro-box1 left-justify">
                                            <div class="clearfix">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 2 == 1) $html .=  '<div class="justify-box1">';
                        $html .=                '<div class="item-product1">
                                                    <div class="product-thumb">
                                                        <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                            '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                        </a>
                                                        <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link pos-bottom"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                    </div>
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html().'
                                                    <div class="product-info">
                                                        '.s7upf_product_link('shop-list').'
                                                    </div>
                                                </div>';
                        if($count % 2 == 0 || $count == $count_query) $html .=  '</div>';
                        $count++;
                    }
                }
                $html .=                    '</div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'cat-list-home6':
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,840:3,1200:4';
                if(empty($item)) $item = 4;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="product-box6 '.esc_attr($info_pos).'">
                                <div class="header-box6 title-box6">
                                    <div class="clearfix">';
                if(!empty($title)) $html .= '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=                '<ul class="list-none">';
                if(isset($custom_list)){
                    foreach ($custom_list as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            $term_link = get_term_link( $term->term_id, 'product_cat' );
                            $cat_thumb_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
                            $html .=        '<li>
                                                <a href="'.esc_url($term_link).'">
                                                    '.wp_get_attachment_image($cat_thumb_id,array(20,20)).'
                                                    <span>'.$term->name.'</span>
                                                </a>
                                            </li>';
                        }
                    }
                }
                $html .=                '</ul>
                                    </div>
                                </div>';
                $html .=        '<div class="content-product6">
                                    <div class="clearfix">';
                $html .=                '<div class="tab-pop6">
                                            <div class="hotkey-cat-color">
                                                '.wpb_js_remove_wpautop($content, true).'
                                            </div>
                                            <div class="pro-pop6 item-deal3">';
                $html .=                        '<p class="text-uppercase color">'.esc_html__("Whats popular now","kuteshop").'</p>';
                $args2 = $args;
                $args2['posts_per_page'] = 1;
                $args2['meta_key'] = 'total_sales';
                $args2['order'] = 'DESC';
                $args2['orderby'] = 'meta_value_num';
                $product_query2 = new WP_Query($args2);
                if($product_query2->have_posts()) {
                    while($product_query2->have_posts()) {
                        $product_query2->the_post();
                        global $product;
                        $from = $product->get_regular_price();
                        $to = $product->get_price();
                        $percent = $percent_html =  '';
                        if($from != $to && $from > 0){
                            $percent = round(($from-$to)/$from*100);            
                            $percent_html = '<span class="sale-off">'.$percent.'% '.esc_html__("OFF","kuteshop").'</span>';
                        }
                        $html .=                '<div class="item-product3">
                                                    <div class="product-info">
                                                        '.$percent_html.'
                                                        <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                        '.s7upf_get_price_html().'
                                                        <span class="order-pro">'.get_post_meta(get_the_ID(),'total_sales',true).' '.esc_html__("Order","kuteshop").'</span>
                                                        '.s7upf_get_rating_html().'
                                                    </div>
                                                    <div class="product-thumb">
                                                        <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                            '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                        </a>
                                                        <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                    </div>
                                                </div>';
                    }
                }
                $html .=                    '</div>
                                        </div>';
                $html .=                '<div class="product-slider6">
                                            <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 2 == 1) $html .=  '<div class="item-slider6">';
                        $html .=                '<div class="item-product">
                                                    <div class="product-thumb">
                                                        <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                            '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                        </a>
                                                        <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                    </div>
                                                    <div class="product-info">
                                                        <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                        '.s7upf_get_price_html().'
                                                    </div>
                                                </div>';
                        if($count % 2 == 0 || $count == $count_query) $html .=  '</div>';
                        $count++;
                    }
                }
                $html .=                    '</div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'deals-home6':
                if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,1200:3';
                if(empty($item)) $item = 3;
                if(empty($size)) $size = array(195,260);
                if(!empty($time)) $time = s7upf_get_deals_time($time);
                $html .=    '<div class="hotdeal-box6 border">';
                if(!empty($title)){
                    $html .=    '<h2 class="title24">';
                    if(!empty($icon)) $html .=    '<i class="fa '.esc_attr($icon).'" aria-hidden="true"></i>';
                    $html .=        '<span>'.esc_html($title).'</span>
                                </h2>';
                }
                if(!empty($des)) $html .=   '<p class="color">'.esc_html($des).'</p>';
                if(!empty($time)){
                    $html .=        '<div class="countdown-master flip-clock-wrapper" data-time="'.esc_attr($time).'"></div>';
                }
                $html .=        '<div class="hotdeal-slider6">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $html .=        '<div class="item-hotdeal6">
                                            <div class="product-thumb">
                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                </a>
                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                            </div>
                                            <div class="product-info">
                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                '.s7upf_get_price_html().'
                                                '.s7upf_product_link('home3').'
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'slider-home5':
                if(empty($item) && empty($item_res)) $item_res = '0:1,640:2,992:3';
                if(empty($item)) $item = 3;
                if(empty($size)) $size = array(195,260);
                $size_small = array(170,170);
                $html .=    '<div class="product-tab5 trending-product5">
                                <div class="trend-box5">';
                $html .=    '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=            '<div class="trend-slider5">
                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 5 == 1){
                        $html .=        '<div class="item-trend5">
                                            <div class="ver-item">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                </div>
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html('sale-style').'
                                                    '.s7upf_product_link('','hidden-text').'
                                                    '.s7upf_get_rating_html().'
                                                </div>
                                            </div>
                                        </div>';
                        }
                        else{
                            if($count % 5 == 2 || $count % 5 == 4) $html .=        '<div class="item-trend5">';
                            $html .=            '<div class="hoz-item">
                                                    <div class="product-thumb">
                                                        <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                            '.get_the_post_thumbnail(get_the_ID(),$size_small).'
                                                        </a>
                                                        <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                    </div>
                                                    <div class="product-info">
                                                        <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                        '.s7upf_get_price_html('sale-style').'
                                                        '.s7upf_get_rating_html().'
                                                        '.s7upf_product_link('','hidden-text').'
                                                    </div>
                                                </div>';
                            if($count % 5 == 0 || $count % 5 == 3 || $count == $count_query) $html .=        '</div>';
                        }
                        $count++;
                    }
                }
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'deals-home5':
                if(empty($item) && empty($item_res)) $item_res = '0:1,360:2,568:3,840:4,1200:5';
                if(empty($item)) $item = 5;
                if(empty($size)) $size = array(195,260);
                $time = s7upf_get_deals_time($time);
                $html .=    '<div class="product-tab5 hotdeal-pro5 border">
                                <div class="title-tabpro5">';
                if(!empty($title)) $html .=    '<h2 class="title18 color">'.esc_html($title).'</h2>';
                if(!empty($time)){
                    $html .=        '<div class="hotdeal5">
                                        <span>'.esc_html__("End in:","kuteshop").'</span>
                                        <div class="countdown-master flip-clock-wrapper" data-time="'.esc_attr($time).'"></div>
                                    </div>';
                }
                $html .=        '</div>';
                $html .=        '<div class="tab-content">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $html .=        '<div class="item-product5 item-product">
                                            <div class="product-thumb">
                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                </a>
                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus">'.esc_html__("quick view","kuteshop").'</a>
                                                '.s7upf_product_link().'
                                            </div>
                                            <div class="product-info">
                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                '.s7upf_get_price_html().'
                                            </div>
                                        </div>';
                        $count++;
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'cat-tab-home5':
                if(!empty($cats)){
                    if(empty($item) && empty($item_res)) $item_res = '0:1,360:2,568:3,840:4,1200:5';
                    if(empty($item)) $item = 5;
                    if(empty($size)) $size = array(195,260);
                    $pre = rand(1,100);
                    $tabs = explode(",",$cats);
                    if($tab_active > count($tabs)) $tab_active = 1;
                    $tab_html = $tab_content = '';
                    foreach ($tabs as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            if($key == $tab_active) $active = 'active';
                            else $active = '';
                            $key_adv = $key+1;
                            $tab_html .=    '<li class="'.esc_attr($active).'"><a href="#'.esc_attr($pre.$term->slug).'" data-toggle="tab">'.$term->name.'</a></li>';
                            $tab_content .=    '<div id="'.$pre.$term->slug.'" class="tab-pane '.$active.'">
                                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                            unset($args['tax_query']);
                            $args['tax_query'][]=array(
                                'taxonomy'=>'product_cat',
                                'field'=>'slug',
                                'terms'=> $tab
                            );
                            $product_query = new WP_Query($args);
                            $max_page = $product_query->max_num_pages;
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    global $product;                                    
                                    $tab_content .=         '<div class="item-product5 item-product">
                                                                <div class="product-thumb">
                                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                    </a>
                                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus">'.esc_html__("quick view","kuteshop").'</a>
                                                                    '.s7upf_product_link().'
                                                                </div>
                                                                <div class="product-info">
                                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                    '.s7upf_get_price_html().'
                                                                </div>
                                                            </div>';
                                }
                            }
                            $tab_content .=         '</div>
                                                </div>';
                        }
                    }
                    $html .=    '<div class="product-tab5 border">
                                    <div class="title-tabpro5">';
                    if(!empty($title)) $html .= '<h2 class="title18">'.esc_html($title).'</h2>';
                    $html .=            '<ul class="list-none">
                                            '.$tab_html.'
                                        </ul>
                                    </div>
                                    <div class="tab-content">
                                        '.$tab_content.'
                                    </div>
                                </div>';
                }
                break;

            case 'deals-home1':
                if(!empty($cats)){
                     if(empty($item) && empty($item_res)) $item_res = '0:1,480:2,767:3,992:4,1200:5';
                    if(empty($item)) $item = 5;
                    if(empty($size)) $size = array(195,260);
                    $pre = rand(1,100);
                    $tabs = explode(",",$cats);
                    if($tab_active > count($tabs)) $tab_active = 1;
                    $tab_html = $tab_content = '';
                    foreach ($tabs as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            if($key == $tab_active) $active = 'active';
                            else $active = '';
                            $key_adv = $key+1;
                            $tab_html .=    '<li class="'.esc_attr($active).'"><a href="#'.esc_attr($pre.$term->slug).'" data-toggle="tab">'.$term->name.'</a></li>';
                            $tab_content .=    '<div id="'.$pre.$term->slug.'" class="tab-pane '.$active.'">
                                                    <div class="hotdeal-slider">
                                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                            unset($args['tax_query']);
                            $args['tax_query'][]=array(
                                'taxonomy'=>'product_cat',
                                'field'=>'slug',
                                'terms'=> $tab
                            );
                            $product_query = new WP_Query($args);
                            $max_page = $product_query->max_num_pages;
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    global $product;
                                    $from = $product->get_regular_price();
                                    $to = $product->get_price();
                                    $percent = $percent_html =  '';
                                    if($from != $to && $from > 0){
                                        $percent = round(($from-$to)/$from*100);            
                                        $percent_html = '<div class="deal-percent">
                                                            <span>'.$percent.'</span>
                                                            <sup>%</sup>
                                                        </div>';
                                    }
                                    $tab_content .=         '<div class="item-pro-hotdeal">
                                                                <div class="product-thumb">
                                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                    </a>
                                                                    <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link pos-bottom"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                </div>
                                                                <div class="product-info">
                                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                                    <div class="info-pro-hotdeal">
                                                                        '.$percent_html.'
                                                                        '.s7upf_get_price_html().'
                                                                        '.s7upf_get_rating_html().'
                                                                    </div>
                                                                    '.s7upf_product_link('shop-list').'
                                                                </div>
                                                            </div>';
                                }
                            }
                            $tab_content .=             '</div>
                                                    </div>
                                                </div>';
                        }
                    }
                    $html .=    '<div class="product-hotdeal">
                                    <div class="header-hotdeal">
                                        <div class="container">
                                            <div class="title-box1">';
                    if(!empty($title)) $html .= '<h2 class="title30"><span>'.substr($title, 0, 1).'</span><a href="'.esc_url($link).'">'.esc_html($title).'</a></h2>';
                    $html .=                    '<ul class="list-none">
                                                    '.$tab_html.'
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="content-hotdeal tab-content">
                                        '.$tab_content.'
                                    </div>
                                </div>';
                }
                break;

            case 'deals-home3':
                if(empty($item) && empty($item_res)) $item_res = '0:1,568:2,992:3';
                if(empty($item)) $item = 3;
                if(empty($size)) $size = array(120,120);
                $time = s7upf_get_deals_time($time);
                $html .=    '<div class="pro-deal3 border">
                                <div class="title-deal3">';
                if(!empty($title)) $html .=    '<strong>'.esc_html($title).'</strong>';
                if(!empty($time)){
                    $html .=        '<span>'.esc_html__("Deals End in:","kuteshop").'</span>';
                    $html .=        '<div class="countdown-master flip-clock-wrapper" data-time="'.esc_attr($time).'"></div>';
                }
                $html .=        '</div>';
                $html .=        '<div class="deal-slider3 arrow-style3">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        global $product;
                        $from = $product->get_regular_price();
                        $to = $product->get_price();
                        $percent = $percent_html =  '';
                        if($from != $to && $from > 0){
                            $percent = round(($from-$to)/$from*100);            
                            $percent_html = '<span class="sale-off">'.$percent.'% '.esc_html__("OFF","kuteshop").'</span>';
                        }
                        if($count % 2 == 1) $html .= '<div class="item-deal3">';                        
                        $html .=        '<div class="item-product3">
                                            <div class="product-info">';
                        $html .=                $percent_html;
                        $html .=                '<h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                '.s7upf_get_price_html().'
                                                <span class="order-pro">'.get_post_meta(get_the_ID(),'total_sales',true).' '.esc_html__("Order","kuteshop").'</span>
                                            </div>
                                            <div class="product-thumb">
                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                </a>
                                                <a data-product-id="'.get_the_ID().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle">'.esc_html__("quick view","kuteshop").'</a>
                                            </div>
                                        </div>';
                        if($count % 2 == 0 || $count == $count_query) $html .= '</div>';
                        $count++;
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'list-banner3':
                if(empty($item) && empty($item_res))$item_res = '';
                if(empty($item)) $item = 4;
                if(empty($size)) $size = array(180,180);
                $data = (array) vc_param_group_parse_atts( $list_advs );
                $html .=    '<div class="cat-pro3">';
                if(!empty($title)) $html .=    '<h2 class="title14 title-catpro3">'.esc_html($title).'</h2>';
                if(!empty($list_advs)){
                    $html .=        '<div class="catbn-slider3">
                                        <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="" data-navigation="">';
                    if(is_array($data)){
                        foreach ($data as $key => $value){
                            $html .=    '<div class="banner-zoom">
                                            <a href="'.esc_url($value['link']).'" class="thumb-zoom">'.wp_get_attachment_image($value['image'],'full').'</a>
                                        </div>';
                        }
                    }
                    $html .=            '</div>
                                    </div>';
                }
                $html .=        '<div class="catpro-slider3 arrow-style3">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        $html .=        '<div class="item-product3">
                                            <div class="product-thumb">
                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                </a>
                                                <a data-product-id="'.get_the_id().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link pos-bottom"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                            </div>
                                            <div class="product-info">
                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                '.s7upf_get_price_html().'
                                                '.s7upf_product_link('home3').'
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                                <a href="#" class="btn-control-banner hide-cat-banner">'.esc_html__("hidden","kuteshop").'</a>
                                <a href="#" class="btn-control-banner show-cat-banner">'.esc_html__("show","kuteshop").'</a>
                            </div>';
                break;

            case 'list-slider3':
                if(empty($item) && empty($item_res)) $item_res = '';
                if(empty($item)) $item = 3;
                if(empty($size)) $size = array(195,260);
                $html .=    '<div class="new-product3">';
                if(!empty($title)) $html .=    '<h2 class="title14 bg-color white">'.esc_html($title).'</h2>';
                $html .=        '<div class="newpro-slider3 arrow-style3">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % 2 == 1) $html .= '<div class="item-newpro3">';                        
                        $html .=        '<div class="item-product item-product3">
                                            '.s7upf_get_label_html().'
                                            <div class="product-thumb">
                                                <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                </a>
                                                <a data-product-id="'.get_the_id().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link">'.esc_html__("quick view","kuteshop").'</a>
                                                '.s7upf_product_link('hidden-text').'
                                            </div>
                                            <div class="product-info">
                                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                '.s7upf_get_price_html().'
                                            </div>
                                        </div>';
                        if($count % 2 == 0 || $count == $count_query) $html .= '</div>';
                        $count++;
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'side-slider3':
                if(empty($item) && empty($item_res)) $item_res = '';
                if(empty($item)) $item = 1;
                if(empty($size)) $size = array(121,121);
				if(empty($col)) $col = 3;
                $html .=    '<div class="box-side top-review3">';
                if(!empty($title)) $html .=    '<h2 class="title14 white bg-color title-side">'.esc_html($title).'</h2>';
                $html .=        '<div class="content-side">
                                    <div class="review-slider3 arrow-style3">
                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($product_query->have_posts()) {
                    while($product_query->have_posts()) {
                        $product_query->the_post();
                        if($count % $col == 1 || $col == 1) $html .= '<div class="list-review3">';
                        $html .=            '<div class="item-product3">
                                                <div class="product-thumb">
                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                    </a>
                                                    <a data-product-id="'.get_the_id().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                </div>
                                                <div class="product-info">
                                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                                    '.s7upf_get_price_html().'
                                                    '.s7upf_get_rating_html().'
                                                    '.s7upf_product_link('hidden-text').'
                                                </div>
                                            </div>';
                        if($count % $col == 0 || $col == 1 || $count == $count_query) $html .= '</div>';
                        $count++;
                    }
                }
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            default:
                if(!empty($cats)){                    
                    if(empty($item)) $item = 5;
                    if(empty($size)) $size = array(121,121);
                    $pre = rand(1,100);
                    $tabs = explode(",",$cats);
                    if($tab_active > count($tabs)) $tab_active = 1;
                    $tab_html = $tab_content = '';
                    foreach ($tabs as $key => $tab) {
                        $term = get_term_by( 'slug',$tab, 'product_cat' );
                        if(!empty($term) && is_object($term)){
                            if($key == $tab_active) $active = 'active';
                            else $active = '';
                            $tab_content .=    '<div class="item-toggle-tab '.esc_attr($active).'">
                                                    <h3 class="toggle-tab-title title14">'.$term->name.'</h3>
                                                    <div class="toggle-tab-content">
                                                        <div class="row">';
                            unset($args['tax_query']);
                            $args['tax_query'][]=array(
                                'taxonomy'=>'product_cat',
                                'field'=>'slug',
                                'terms'=> $tab
                            );
                            $product_query = new WP_Query($args);
                            $max_page = $product_query->max_num_pages;
                            if($product_query->have_posts()) {
                                while($product_query->have_posts()) {
                                    $product_query->the_post();
                                    if($count % 2 == 1) $tab_content .= '<div class="col-md-6 col-sm-6 col-xs-6">';                        
                                    $tab_content .=         '<div class="item-product3">
                                                                <div class="product-thumb">
                                                                    <a class="product-thumb-link" href="'.esc_url(get_the_permalink()).'">
                                                                        '.get_the_post_thumbnail(get_the_ID(),$size).'
                                                                    </a>
                                                                    <a data-product-id="'.get_the_id().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link plus pos-middle"><span>'.esc_html__("quick view","kuteshop").'</span></a>
                                                                </div>
                                                                <div class="product-info">
                                                                    '.s7upf_get_price_html().'
                                                                    <span class="order-pro">'.get_post_meta(get_the_ID(),'total_sales',true).' '.esc_html__("Order","kuteshop").'</span>
                                                                </div>
                                                            </div>';
                                    if($count % 2 == 0 || $count == $count_query) $tab_content .= '</div>';
                                    $count++;
                                }
                            }
                            $tab_content .=             '</div>';                            
                            $tab_content .=         '</div>
                                                </div>';
                        }
                    }
                    $html .=    '<div class="betpro3 box-side">';
                    if(!empty($title)) $html .= '<h2 class="title14 white bg-color title-side">'.esc_html($title).'</h2>';
                    $html .=        '<div class="content-side toggle-tab toggle-betsale">
                                        '.$tab_content.'
                                    </div>
                                </div>';
                }
                break;
        }
        wp_reset_postdata();
        return $html;
    }
}

stp_reg_shortcode('sv_product_list_basic','sv_vc_product_list_basic');
$check_add = '';
if(isset($_GET['return'])) $check_add = $_GET['return'];
if(empty($check_add)) add_action( 'vc_before_init_base','sv_add_list_product_basic',10,100 );
if ( ! function_exists( 'sv_add_list_product_basic' ) ) {
    function sv_add_list_product_basic(){
        vc_map( array(
            "name"      => esc_html__("SV Product list Basic", 'kuteshop'),
            "base"      => "sv_product_list_basic",
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
                        esc_html__('Categories Toggle','kuteshop')     => '',
                        esc_html__('Side slider home 3','kuteshop')     => 'side-slider3',
                        esc_html__('List Slider home 3','kuteshop')     => 'list-slider3',
                        esc_html__('List Banner home 3','kuteshop')     => 'list-banner3',
                        esc_html__('Deals home 3','kuteshop')     => 'deals-home3',
                        esc_html__('Deals home 1','kuteshop')     => 'deals-home1',
                        esc_html__('Tab Categories home 5','kuteshop')     => 'cat-tab-home5',
                        esc_html__('Deals home 5','kuteshop')     => 'deals-home5',
                        esc_html__('Special Slider home 5','kuteshop')     => 'slider-home5',
                        esc_html__('Deals home 6','kuteshop')     => 'deals-home6',
                        esc_html__('Categories list home 6','kuteshop')     => 'cat-list-home6',
                        esc_html__('Categories More link home 7','kuteshop')     => 'cat-more-home7',
                        esc_html__('Tab Categories home 8','kuteshop')     => 'cat-tab-home8',
                        esc_html__('Tab Categories home 8(right)','kuteshop')     => 'cat-tab-home8-2',
                        esc_html__('Tab Categories home 8(3)','kuteshop')     => 'cat-tab-home8-3',
                        esc_html__('Tab Categories home 8(4)','kuteshop')     => 'cat-tab-home8-4',
                        esc_html__('Tab Categories home 8(5)','kuteshop')     => 'cat-tab-home8-5',
                        esc_html__('Deals home 8','kuteshop')     => 'deals-home8',                        
                        esc_html__('List home 8','kuteshop')     => 'list-home8',
                        esc_html__('Deals home 9','kuteshop')     => 'deals-home9',
                        esc_html__('Categories list home 9','kuteshop')     => 'cat-list-home9',                        
                        esc_html__('Categories list home 9(3 adv)','kuteshop')     => 'cat-list-home9-2',                        
                        esc_html__('Categories list home 9(3 adv)(2)','kuteshop')     => 'cat-list-home9-3',
                        esc_html__('List slider mega menu','kuteshop')     => 'list-megamenu',
                        esc_html__('Deals home 10','kuteshop')     => 'deals-home10',
                        esc_html__('Side slider home 10','kuteshop')     => 'side-slider10',                      
                        esc_html__('Deals home 11','kuteshop')     => 'deals-home11',
                        esc_html__('List slider home 11','kuteshop')     => 'list-slider11',
                        esc_html__('Side slider home 11','kuteshop')     => 'side-slider11',
                        esc_html__('Side slider home 12','kuteshop')     => 'side-slider12',
                        esc_html__('List slider home 12','kuteshop')     => 'list-slider12',
                        esc_html__('List slider home 13','kuteshop')     => 'list-slider13',
                        esc_html__('List grid home 13','kuteshop')     => 'list-grid13',
                        esc_html__('List grid home 13(2)','kuteshop')     => 'list-grid13-2',
                        esc_html__('List slider home 14','kuteshop')     => 'list-slider14',
                        esc_html__('List grid home 14','kuteshop')     => 'list-grid14',
                        esc_html__('List grid home 15','kuteshop')     => 'list-grid15',
                        esc_html__('Side slider home 15','kuteshop')     => 'side-slider15',
                        esc_html__('Tab Categories home 16','kuteshop')     => 'cat-tab-home16',
                        esc_html__('List slider adv home 16','kuteshop')     => 'list-slider16',
                        esc_html__('List slider home 17','kuteshop')     => 'list-slider17',
                        esc_html__('Deals slider home 17','kuteshop')     => 'deal-slider17',
                        esc_html__('Deals slider home 18','kuteshop')     => 'deal-slider18',
                        esc_html__('List slider home 18','kuteshop')     => 'list-slider18',
                        esc_html__('List custom','kuteshop')     => 'list-custom',
                    )
                ),
                array(
                    "type" => "attach_image",
                    "heading" => esc_html__("Image Item",'kuteshop'),
                    "param_name" => "image",
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('list-grid14'),
                        )
                ),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Color",'kuteshop'),
                    "param_name" => "color13",
                    "value"     => array(
                        esc_html__("Default",'kuteshop')    => '',
                        esc_html__("Red",'kuteshop')       => 'red-block',
                        esc_html__("Blue",'kuteshop')       => 'blue-block',
                        esc_html__("Purple",'kuteshop')       => 'purple-block',
                        esc_html__("Green",'kuteshop')      => 'green-block',
                        esc_html__("Orange",'kuteshop')      => 'orange-block',
                        ),
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('list-grid13','list-grid13-2'),
                        )
                ),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Color",'kuteshop'),
                    "param_name" => "color16",
                    "value"     => array(
                        esc_html__("Default",'kuteshop')    => '',
                        esc_html__("Purple",'kuteshop')       => 'color-purple',
                        esc_html__("Cyan",'kuteshop')       => 'color-cyan',
                        esc_html__("Green",'kuteshop')      => 'color-green',
                        esc_html__("Yellow",'kuteshop')      => 'color-yellow',
                        ),
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('list-slider16'),
                        )
                ),
                array(
                    "type" => "textfield",
                    "heading" => esc_html__("View Link",'kuteshop'),
                    "param_name" => "link",
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('cat-tab-home16','list-grid14','deals-home11','deals-home1','cat-more-home7','list-home8','deals-home9','deals-home10','side-slider10'),
                        ),
                ),
                array(
                    "type" => "textfield",
                    "heading" => esc_html__("Time",'kuteshop'),
                    "param_name" => "time",
                    'description'   => esc_html__( 'Enter time(hours:minutes) to countdown. Format is hh:mm. Example 18:30.', 'kuteshop' ),
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('list-grid13','list-grid13-2','list-slider13','deals-home3','deals-home5','deals-home6','deals-home8','deals-home9'),
                        ),
                ),
                array(
                    "type" => "textfield",
                    "heading" => esc_html__("Time",'kuteshop'),
                    "param_name" => "time2",
                    'description'   => esc_html__( 'Entert time for countdown. Format is mm/dd/yyyy. Example: 12/15/2017', 'kuteshop' ),
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('deals-home10','cat-tab-home16','deal-slider17','deal-slider18'),
                        ),
                ),
                array(
                    "type" => "param_group",
                    "heading" => esc_html__("Add Image List",'kuteshop'),
                    "param_name" => "list_advs",
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('list-slider16','list-grid13','list-grid13-2','side-slider12','list-banner3','cat-more-home7','cat-list-home9','cat-list-home9-2','cat-list-home9-3'),
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
                array(
                    'heading'     => esc_html__( 'Title', 'kuteshop' ),
                    'holder'      => 'h3',
                    'type'        => 'textfield',
                    'param_name'  => 'title',
                ),
                array(
                    'heading'     => esc_html__( 'Title 2', 'kuteshop' ),
                    'holder'      => 'h3',
                    'type'        => 'textfield',
                    'param_name'  => 'title2',
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('deals-home9','deals-home11'),
                        ),
                ),
                array(
                    'type' => 'iconpicker',
                    'heading' => esc_html__( 'Icon Before Title', 'kuteshop' ),
                    'param_name' => 'icon',
                    'value' => '',
                    'settings' => array(
                        'emptyIcon' => true,
                        'iconsPerPage' => 4000,
                    ),
                    'dependency' => array(
                        'element' => 'style',
                        'value' => 'deals-home6',
                    ),
                    'description' => esc_html__( 'Select icon from library.', 'kuteshop' ),
                ),
                array(
                    'heading'     => esc_html__( 'Description', 'kuteshop' ),
                    'type'        => 'textfield',
                    'param_name'  => 'des',
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('deals-home6','list-slider14','list-grid14','list-grid15'),
                        ),
                ),
                array(
                    'heading'     => esc_html__( 'Number', 'kuteshop' ),
                    'type'        => 'textfield',
                    'description' => esc_html__( 'Enter number of product. Default is 8.', 'kuteshop' ),
                    'param_name'  => 'number',
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
                    'heading'     => esc_html__( 'Tab Active', 'kuteshop' ),
                    'type'        => 'textfield',
                    'description' => esc_html__( 'Enter number. Default is 1 (First tab).', 'kuteshop' ),
                    'param_name'  => 'tab_active',
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('','deals-home1','cat-tab-home5','cat-tab-home8','cat-tab-home8-2','cat-tab-home8-3','cat-tab-home8-4','cat-tab-home8-5'),
                        ),
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
                    "type"          => "textfield",
                    "heading"       => esc_html__("Item / Slider",'kuteshop'),
                    "param_name"    => "item",
                    "group"         => esc_html__("Slider Settings",'kuteshop'),
                    'description' => esc_html__( 'Enter number of item. Default is auto with style display.', 'kuteshop' ),
				),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Products / Item",'kuteshop'),
                    "param_name"    => "col",
                    "group"         => esc_html__("Slider Settings",'kuteshop'),
                    'description' => esc_html__( 'Enter number of product for a Item of slider. Default is auto with style display.', 'kuteshop' ),
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('side-slider12','side-slider10','side-slider3','side-slider15','side-slider11'),
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
                    "type"          => "textfield",
                    "heading"       => esc_html__("Size Thumbnail",'kuteshop'),
                    "param_name"    => "size",
                    "group"         => esc_html__("Thumb Settings",'kuteshop'),
                    'description'   => esc_html__( 'Enter site thumbnail to crop. [width]x[height]. Example is 300x300', 'kuteshop' ),
                ),
                array(
                    "type"          => "dropdown",
                    "heading"       => esc_html__("Info Position",'kuteshop'),
                    "param_name"    => "info_pos",
                    "value"         => array(
                        esc_html__("Left",'kuteshop')  => 'tags-left',
                        esc_html__("Right",'kuteshop')  => 'tags-right',
                        ),
                    "group"         => esc_html__("More Info",'kuteshop'),
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('cat-list-home6','cat-more-home7'),
                        ),
                ),
                array(
                    "type"          => "textarea_html",
                    "heading"       => esc_html__("More Info",'kuteshop'),
                    "param_name"    => "content",
                    "group"         => esc_html__("More Info",'kuteshop'),
                    'dependency'    => array(
                        'element'   => 'style',
                        'value'   => array('cat-list-home6','cat-more-home7'),
                        ),
                ),
            )
        ));
    }
}
}