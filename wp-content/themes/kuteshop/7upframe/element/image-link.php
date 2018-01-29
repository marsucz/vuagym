<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */
if(!function_exists('s7upf_vc_payment'))
{
    function s7upf_vc_payment($attr, $content = false)
    {
        $html = $icon_html = '';
        extract(shortcode_atts(array(
            'style'         => 'text-center',
            'title'         => '',
            'icon'          => '',
            'list'          => '',
            'des'           => '',
            'link'          => '',
        ),$attr));
        $data = (array) vc_param_group_parse_atts( $list );
        switch ($style) {
            case 'list-img18':
                $item = 5;
                $speed = 5000;
                $item_res = '0:1,480:2,640:3,768:4,1024:5,1200:6';
                $html .=    '<div class="brand-slider18">';
                $html .=        '<div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<div class="item-brand18 text-center">
                                        <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full',0,array('class'=> 'wobble-to-bottom-right')).'</a>
                                    </div>';
                    }
                }
                $html .=        '</div>
                            </div>';
                break;

            case 'list-img17':
                $item = 5;
                $speed = 5000;
                $item_res = '0:1,360:2,768:3,980:4,1200:5';
                $html .=    '<div class="brand-box17">';
                $html .=        '<div class="brand-slider17 owl-slider17">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        if(($key+1) % 3 == 1) $html .=  '<div class="list-brand17">';
                        $html .=        '<div class="item-brand17">
                                            <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full',0,array('class'=> 'wobble-to-bottom-right')).'</a>
                                        </div>';
                        if(($key+1) % 3 == 0 || ($key+1) == count($data)) $html .=  '</div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'list-img13':
                $item = 5;
                $speed = 5000;
                $item_res = '0:1,360:2,768:3,980:4,1200:5';
                $html .=    '<div class="popcat13">';
                if(!empty($title)) $html .=        '<div class="title-cat13"><h2 class="title14">'.esc_html($title).'</h2></div>';
                $html .=        '<div class="popcat-slider13">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=        '<div class="item-cat13">
                                            <a href="'.esc_url($value['link']).'">
                                                '.wp_get_attachment_image($value['image'],'full',0,array('class'=> 'pulse'));
                        if(isset($value['title'])) $html .=            '<span>'.esc_html($value['title']).'</span>';
                        $html .=            '</a>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'list-adv12':
                $html .=    '<div class="list-adv12">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<div class="adv-thumb adv12">
                                        <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a>
                                        <h2 class="title14"><a href="'.esc_url($value['link']).'">'.esc_html($value['title']).'</a></h2>
                                    </div>';
                    }
                }
                $html .=     '</div>';
                break;

            case 'list-brand11':
                $html .=    '<div class="box-side11 hot-brand11">';
                if(!empty($title)) $html .=    '<h2 class="title24 bg-color text-center white">'.esc_html($title).'</h2>';
                $html .=        '<div class="list-brand11">
                                    <div class="clearfix">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full',0,array('class'=>'pulse-shrink')).'</a>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'testimonials-slider10':
                $html .=    '<div class="testimo-box10">
                                <div class="testimo-slider10">
                                    <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="true" data-navigation="">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<div class="item-testimo10">
                                        <div class="testimo-thumb10">
                                            <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a>
                                        </div>
                                        <div class="testimo-info10">
                                            <p class="desc">'.esc_html($value['des']).'</p>
                                            <h3 class="title18"><a href="'.esc_url($value['link']).'">'.esc_html($value['title']).'</a></h3>
                                        </div>
                                    </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'list-adv-home10':
                $html .=    '<div class="list-detail-adv">';                
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<div class="detail-adv">
                                        <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full',0,array('class'=>'wobble-horizontal')).'</a>
                                    </div>';
                    }
                }
                $html .=    '</div>';
                break;

            case 'testimonials-slider7':
                $html .=    '<div class="testimo7 white">';
                if(!empty($title)) $html .=        '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=        '<div class="testimo-slider7">
                                    <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="true" data-navigation="">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<div class="item-testimo7">
                                        <p>'.esc_html($value['des']).'</p>
                                    </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'service6':
                $html .=    '<div class="why-choise7 white clearfix">';
                if(!empty($title)) $html .=    '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=       '<ul class="list-choise7 list-none">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $count = $key + 1;
                        if($count % 2 == 1) $html .= '<li>';
                        $html .=    '<div class="item-chose7">
                                        <ul class="list-none">
                                            <li>
                                                <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a>
                                            </li>
                                            <li>
                                                <h3>'.esc_html($value['title']).'</h3>
                                                <p>'.esc_html($value['des']).'</p>
                                            </li>
                                        </ul>
                                    </div>';
                        if($count % 2 == 0 || $count == count($data)) $html .= '</li>';
                    }
                }
                $html .=        '</ul>
                            </div>';
                break;

            case 'img-trend6':
                $html .=    '<div class="trending-box6 border">';
                if(!empty($title)){
                    $html .=    '<h2 class="title24">';
                    if(!empty($icon)) $html .=    '<i class="fa '.esc_attr($icon).'" aria-hidden="true"></i>';
                    $html .=        '<span>'.esc_html($title).'</span>
                                </h2>';
                }
                if(!empty($des)) $html .=   '<p class="color">'.esc_html($des).'</p>';
                $html .=       '<ul class="list-none">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<li>
                                        <div class="product-thumb">
                                            <a class="product-thumb-link" href="'.esc_url($value['link']).'">
                                                '.wp_get_attachment_image($value['image'],array(50,50)).'
                                            </a>
                                        </div>
                                        <div class="product-info">
                                            <a href="'.esc_url($value['link']).'">'.esc_html($value['title']).'</a>
                                        </div>
                                    </li>';
                    }
                }
                $html .=        '</ul>';
                if(!empty($link)) $html .=    '<a href="'.esc_url($link).'" class="seeall">'.esc_html__("See All","kuteshop").' <i class="fa fa-angle-double-right" aria-hidden="true"></i></a>';
                $html .=    '</div>';
                break;

            case 'img-footer6':
                $html .=    '<div class="shopping-footer-box footer-box footer-box6">';
                if(!empty($title)) $html .=    '<h2 class="title14">'.esc_html($title).'</h2>';
                $html .=       '<ul class="list-none">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<li>
                                        <div class="shop-thumb6">
                                            <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a>
                                        </div>
                                        <div class="shop-info6">
                                            <strong>'.esc_html($value['title']).'</strong>
                                            <p>'.esc_html($value['des']).'</p>
                                        </div>
                                    </li>';
                    }
                }
                $html .=        '</ul>
                            </div>';
                break;

            case 'brands-list5':
                $html .=    '<div class="category-color category-color5 color-more">';
                $html .=        '<div class="header-cat-color">
                                    <h2 class="title18">'.esc_html($title).'</h2>
                                </div>';
                $html .=        '<div class="content-shop-brand clearfix">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=    '<a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full',0,array('class'=>'wobble-horizontal')).'</a>';
                     }
                }
                $html .=        '</div>
                            </div>';
                break;

            case 'cat-slider5':
                $html .=    '<div class="bncat-slider5">
                                <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $count = $key+1;
                        if($count % 6 == 1) $html .= '<div class="list-bn-cat5">';
                        $html .=    '<div class="item-bn-cat5">
                                        <div class="bncat-thumb5">
                                            <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full',0,array('class'=>'pulse-shrink')).'</a>
                                        </div>
                                        <a href="'.esc_url($value['link']).'">'.esc_html($value['title']).'</a>
                                    </div>';
                        if($count % 6 == 0 || $count == count($data)) $html .= '</div>';
                    }
                }
                $html .=        '</div>
                            </div>';
                break;

            case 'img-slider3':
                $html .=    '<div class="outstore3 box-side">';
                if(!empty($title)) $html .=    '<h2 class="title14 white bg-color title-side">'.esc_html($title).'</h2>';
                $html .=        '<div class="content-side">
                                    <div class="arrow-style3">
                                        <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=            '<div class="outstore-link">
                                                <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full',0,array('class'=>'pulse')).'</a>
                                            </div>';
                    }
                }
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'testimonials-slider3':
                $html .=    '<div class="testimo3 box-side">';
                if(!empty($title)) $html .=    '<h2 class="title14 white bg-color title-side">'.esc_html($title).'</h2>';
                $html .=        '<div class="content-side">
                                    <div class="arrow-style3">
                                        <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=            '<div class="item-testimo3">
                                                <div class="testimo-icon"><a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a></div>
                                                <p class="desc">'.esc_html($value['des']).'</p>
                                                <h3 class="title14"><a href="'.esc_url($value['link']).'">'.esc_html($value['title']).'</a></h3>
                                            </div>';
                    }
                }
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'advs-slider3':
                $html .=    '<div class="advert3 box-side">';
                if(!empty($title)) $html .=    '<h2 class="title14 white bg-color title-side">'.esc_html($title).'</h2>';
                $html .=        '<div class="content-side">
                                    <div class="advert-slider3 arrow-style3">
                                        <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $html .=            '<div class="item-advert3">
                                                <div class="banner-zoom">
                                                    <a class="thumb-zoom" href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a>
                                                </div>
                                                <h2 class="title14">'.esc_html($value['title']).'</h2>
                                                <p>'.esc_html($value['des']).'</p>
                                                <a href="'.esc_url($value['link']).'" class="btn-rect radius white bg-color">'.esc_html__("Shop now!","kuteshop").'</a>
                                            </div>';
                    }
                }
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'brands-slider3':
                $html .=    '<div class="top-brand3 box-side">';
                if(!empty($title)) $html .=    '<h2 class="title14 white bg-color title-side">'.esc_html($title).'</h2>';
                $html .=        '<div class="content-side">
                                    <div class="brand-slider3 arrow-style3">
                                        <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $count = $key + 1;
                        if($count % 6 == 1) $html .= '<div class="list-brand3">';
                        $html .=            '<div class="logo-brand">
                                                <a class="pulse" href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a>
                                            </div>';
                        if($count % 6 == 0 || $count == count($data)) $html .= '</div>';
                    }
                }
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            default:
                if(is_array($data)){
                    foreach ($data as $key => $value) {
                        $icon_html .= '<a class="wobble-vertical" href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a>';
                    }
                }
                $html .=    '<div class="payment-method '.esc_attr($style).'">';
                $html .=        $icon_html;
                $html .=    '</div>';
                break;
        }
          
		return  $html;
    }
}

stp_reg_shortcode('sv_payment','s7upf_vc_payment');


vc_map( array(
    "name"      => esc_html__("SV Image link", 'kuteshop'),
    "base"      => "sv_payment",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type"          => "dropdown",
            "heading"       => esc_html__("Style",'kuteshop'),
            "param_name"    => "style",
            "value"         => array(
                esc_html__("Payment footer",'kuteshop')    => 'text-center',
                esc_html__("Brands slider home 3",'kuteshop')    => 'brands-slider3',
                esc_html__("Advs slider home 3",'kuteshop')    => 'advs-slider3',
                esc_html__("Testimonials slider home 3",'kuteshop')    => 'testimonials-slider3',
                esc_html__("Images slider home 3",'kuteshop')    => 'img-slider3',
                esc_html__("Images slider home 5",'kuteshop')    => 'cat-slider5',
                esc_html__("Brands list home 5",'kuteshop')    => 'brands-list5',
                esc_html__("Payment footer 6",'kuteshop')    => 'payment-method6',
                esc_html__("Images footer 6",'kuteshop')    => 'img-footer6',
                esc_html__("Images trending 6",'kuteshop')    => 'img-trend6',
                esc_html__("Services Home 7",'kuteshop')    => 'service6',
                esc_html__("Testimonials slider home 7",'kuteshop')    => 'testimonials-slider7',
                esc_html__("Payment footer 9",'kuteshop')    => 'payment-method9',                
                esc_html__("List Adv home 10",'kuteshop')    => 'list-adv-home10',
                esc_html__("Testimonials slider home 10",'kuteshop')    => 'testimonials-slider10',
                esc_html__("List brands home 11",'kuteshop')    => 'list-brand11',
                esc_html__("List advs home 12",'kuteshop')    => 'list-adv12',
                esc_html__("List images home 13",'kuteshop')    => 'list-img13',
                esc_html__("Payment footer 15",'kuteshop')    => 'payment-method15',
                esc_html__("List images home 17",'kuteshop')    => 'list-img17',
                esc_html__("List images home 18",'kuteshop')    => 'list-img18',
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Title",'kuteshop'),
            "param_name" => "title",
            "dependency"    => array(
                "element"   => 'style',
                "value"   => array('list-img13','list-brand11','testimonials-slider7','brands-slider3','advs-slider3','testimonials-slider3','img-slider3','brands-list5','img-footer6','img-trend6','service6'),
                )
        ),
        array(
            'type' => 'iconpicker',
            'heading' => esc_html__( 'Icon', 'kuteshop' ),
            'param_name' => 'icon',
            'value' => '',
            'settings' => array(
                'emptyIcon' => true,
                'iconsPerPage' => 4000,
            ),
            'dependency' => array(
                'element' => 'style',
                'value' => 'img-trend6',
            ),
            'description' => esc_html__( 'Select icon from library.', 'kuteshop' ),
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Description",'kuteshop'),
            "param_name" => "des",
            "dependency"    => array(
                "element"   => 'style',
                "value"   => array('img-trend6'),
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("View link",'kuteshop'),
            "param_name" => "link",
            "dependency"    => array(
                "element"   => 'style',
                "value"   => array('img-trend6'),
                )
        ),
		array(
            "type" => "param_group",
            "heading" => esc_html__("Add Image List",'kuteshop'),
            "param_name" => "list",
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
                    "type" => "textfield",
                    "heading" => esc_html__("Link",'kuteshop'),
                    "param_name" => "link",
                ),
            )
        )
    )
));