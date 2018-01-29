<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_advantage'))
{
    function s7upf_vc_advantage($attr,$content = false)
    {
        $html = $view_html = $link_item = '';
        extract(shortcode_atts(array(
            'style'      => 'item-adv4',
            'image'      => '',
            'size'       => '',
            'image2'      => '',
            'title'      => '',
            'link'       => '',
            'color'      => 'rgba(255,129,118,.95)',
            'info_color'      => '',
            'pos_info'   => 'info-left',
            'pos_info2'   => '',
            'sale_text'      => '',
            'time'      => '',
            'percent'      => '',
            'price'      => '',
            'el_class'      => '',
        ),$attr));
        if(!empty($size)) $size = explode('x', $size);
        else $size = 'full';        
        if(!empty($info_color)) $info_color = S7upf_Assets::build_css('color:'.$info_color);
        switch ($style) {
            case 'adv-home17-right':
                $html .=    '<div class="featured-shop17">
                                <h2 class="bg-color title18 title-box17 '.esc_attr($info_color).'">'.esc_html($title).'</h2>
                                <div class="banner-image">
                                    <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                </div>
                            </div>';
                break;

            case 'adv-home17-left':
                $html .=    '<div class="top-brand17">
                                <h2 class="bg-color title18 title-box17">'.esc_html($title).'</h2>
                                <div class="banner-image light '.esc_attr($info_color).'">
                                    <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                </div>
                            </div>';
                break;

            case 'adv-home17':
                $html .=    '<div class="banner-adv17 adv-thumb">
                                <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                <div class="banner-info '.esc_attr($pos_info2).' '.esc_attr($info_color).'">
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;

            case 'adv-zoomout':
                $html .=    '<div class="banner-image '.esc_attr($el_class).'">
                                <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                <div class="banner-info info-adv18 '.esc_attr($info_color).'">
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;

            case 'adv-zoomin':
                $html .=    '<div class="banner-image out-in '.esc_attr($el_class).'">
                                <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                <div class="banner-info info-adv18 '.esc_attr($info_color).'">
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;

            case 'adv-home14':
                $html .=    '<div class="banner-adv14 banner-zoom">
                                <a href="'.esc_url($link).'" class="thumb-zoom">'.wp_get_attachment_image($image,$size).'</a>
                                <div class="banner-info '.esc_attr($info_color).'">
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;

            case 'thumb-front':
            case 'thumb-behind':
                if(!empty($color)) $color = S7upf_Assets::build_css('background-color:'.$color);
                $html .=    '<div class="item-banner14 '.esc_attr($style).'">
                                <div class="banner-img">
                                    <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a> 
                                </div>
                                <div class="banner-background '.esc_attr($color).'">
                                    <div class="inner-text text-center white '.esc_attr($info_color).'">
                                        '.wpb_js_remove_wpautop($content, true).'
                                    </div>
                                </div>
                            </div>';
                break;

            case 'adv-megamenu':
                $html .=    '<div class="mega-adv">
                                <div class="banner-image">
                                    <a href="'.esc_url($link).'" class="thumb-zoom">'.wp_get_attachment_image($image,$size).'</a> 
                                </div>
                                <div class="mega-adv-info '.esc_attr($info_color).'">
                                    <h3 class="title18"><a href="'.esc_url($link).'">'.esc_html($title).'</a></h3>
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;

            case 'adv-home8':
                $html .=    '<div class="banner-zoom banner-adv8">
                                <a href="'.esc_url($link).'" class="thumb-zoom">'.wp_get_attachment_image($image,$size).'</a> 
                                <div class="banner-adv-info8 '.esc_attr($info_color).'">
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;

            case 'adv-home6':
                $html .=    '<div class="banner-zoom bn-adv2 border">
                                <a href="'.esc_url($link).'" class="thumb-zoom">'.wp_get_attachment_image($image,$size).'</a>
                                <div class="banner-info '.esc_attr($pos_info2).' '.esc_attr($info_color).'">
                                    <h2 class="title18">'.esc_html($title).'</h2>
                                    '.wpb_js_remove_wpautop($content, true).'
                                    <div class="deal-percent">
                                        <span>-'.$percent.'</span>
                                        <sup>%</sup>
                                    </div>
                                    <a href="'.esc_url($link).'"><u>'.esc_html__("Shop now!","kuteshop").'</u></a>
                                </div>
                            </div>';
                break;

            case 'adv-home-5':
                $html .=    '<div class="item-adv5">
                                <div class="banner-zoom">
                                    <a href="'.esc_url($link).'" class="thumb-zoom">'.wp_get_attachment_image($image,$size).'</a>
                                </div>
                                <div class="adv-info5">
                                    <ul class="list-none '.esc_attr($info_color).'">
                                        <li>
                                            <a href="'.esc_url($link).'">'.wp_get_attachment_image($image2,'full').'</a>
                                        </li>
                                        <li>
                                            '.wpb_js_remove_wpautop($content, true).'
                                        </li>
                                    </ul>
                                </div>
                            </div>';
                break;

            case 'adv-home5':
                if(!empty($image)) $image = S7upf_Assets::build_css('background-image:url('.wp_get_attachment_image_url($image,$size).');');
                $html .=    '<div class="banner-topheader '.esc_attr($image).'">
                                <div class="container">
                                    <div class="inner-top-banner '.esc_attr($info_color).'">
                                        '.wpb_js_remove_wpautop($content, true).'
                                        <a href="'.esc_url($link).'" class="shopnow radius white text-uppercase">'.esc_html__("shop now","kuteshop").'</a>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'adv-home3':
                $html .=    '<div class="banner-zoom">
                                <a class="thumb-zoom" href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                <div class="banner-info white '.esc_attr($info_color).'">
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;

            case 'flash-sale':
                $html .=    '<div class="banner-flash">
                                <div class="banner-zoom">
                                    <a class="thumb-zoom" href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                    <div class="flash-label">
                                        <span class="text-uppercase white">'.esc_html($sale_text).'</span>
                                    </div>
                                </div>
                                <div class="flash-info '.esc_attr($info_color).'">
                                    <ul class="list-none clearfix">
                                        <li>
                                            <div class="flash-timer">
                                                <p>'.esc_html($title).'</p>
                                                <div class="flash-countdown" data-date="'.esc_attr($time).'"></div>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="info-pro-hotdeal">
                                                <div class="deal-percent">
                                                    <span>'.esc_html($percent).'</span>
                                                    <sup>'.esc_html__("%","kuteshop").'</sup>
                                                </div>
                                                <div class="product-price">
                                                    <label>'.esc_html__("From","kuteshop").'</label>
                                                    <ins><span>'.esc_html($price).'</span></ins>
                                                </div>
                                            </div>
                                        </li>
                                        <li>
                                            <a href="'.esc_url($link).'" class="btn-rect white bg-color radius"><span>'.esc_html__("Shop now!","kuteshop").'</span></a>
                                        </li>
                                    </ul>
                                </div>
                            </div>';
                break;

            case 'bn-adv2':
                $html .=    '<div class="banner-zoom bn-adv2 border">
                                <a class="thumb-zoom" href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                <div class="banner-info text-right '.esc_attr($info_color).'">';
                $html .=            '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=            '<div class="deal-percent">
                                        '.wpb_js_remove_wpautop($content, true).'
                                    </div>
                                    <a href="'.esc_url($link).'"><u>'.esc_html__("Shop now!","kuteshop").'</u></a>
                                </div>
                            </div>';
                break;

            case 'item-adv-color':
                $color_class = 'color-'.uniqid();
                if(!empty($color)) S7upf_Assets::add_css(
                                    '.'.$color_class.' .adv-color-info,
                                    .'.$color_class.' .adv-color-info::after, .'.$color_class.' .adv-color-info::before{background:'.$color.';}');
                $html .=    '<div class="item-adv-color '.esc_attr($pos_info.' '.$color_class).'">
                                <div class="adv-color-thumb">
                                    <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                </div>
                                <div class="adv-color-info '.esc_attr($info_color).'">
                                    <div class="inner-adv-color-info">
                                        '.wpb_js_remove_wpautop($content, true).'
                                        <a class="shopnow" href="'.esc_url($link).'"><span>'.esc_html__("Shop now","kuteshop").'</span></a>
                                    </div>
                                </div>
                            </div>';
                break;
            
            default:        
                $html .=    '<div class="item-adv '.esc_attr($style).'">
                                <div class="adv-thumb">
                                    <a href="'.esc_url($link).'">'.wp_get_attachment_image($image,$size).'</a>
                                </div>
                                <div class="adv-info '.esc_attr($info_color).'">
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;
        }
        return $html;
    }
}

stp_reg_shortcode('sv_advantage','s7upf_vc_advantage');
vc_map( array(
    "name"      => esc_html__("SV Advantage", 'kuteshop'),
    "base"      => "sv_advantage",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "dropdown",
            "heading" => esc_html__("Style",'kuteshop'),
            "param_name" => "style",
            "value"     => array(
                esc_html__("Default",'kuteshop')   => 'item-adv4',
                esc_html__("Adv color",'kuteshop')   => 'item-adv-color',
                esc_html__("Adv home 2",'kuteshop')   => 'bn-adv2',
                esc_html__("Flash sale",'kuteshop')   => 'flash-sale',
                esc_html__("Adv home 3",'kuteshop')   => 'adv-home3',
                esc_html__("Adv home 5(top)",'kuteshop')   => 'adv-home5',
                esc_html__("Adv home 5",'kuteshop')   => 'adv-home-5',
                esc_html__("Adv home 6",'kuteshop')   => 'adv-home6',
                esc_html__("Adv home 8",'kuteshop')   => 'adv-home8',
                esc_html__("Adv mega menu",'kuteshop')   => 'adv-megamenu',
                esc_html__("Adv home 14",'kuteshop')   => 'thumb-behind',
                esc_html__("Adv home 14(2)",'kuteshop')   => 'thumb-front',
                esc_html__("Adv home 14(3)",'kuteshop')   => 'adv-home14',
                esc_html__("Adv zoom in",'kuteshop')   => 'adv-zoomin',
                esc_html__("Adv zoom out",'kuteshop')   => 'adv-zoomout',
                esc_html__("Adv home 17",'kuteshop')   => 'adv-home17',
                esc_html__("Adv home 17(left)",'kuteshop')   => 'adv-home17-left',
                esc_html__("Adv home 17(right)",'kuteshop')   => 'adv-home17-right',
                )
        ),
        array(
            "type"          => "textfield",
            "heading"       => esc_html__("Extra class",'kuteshop'),
            "param_name"    => "el_class",
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('adv-zoomout','adv-zoomin'),
                )
        ),
        array(
            "type"          => "colorpicker",
            "heading"       => esc_html__("Color",'kuteshop'),
            "param_name"    => "color",
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('item-adv-color','thumb-behind','thumb-front'),
                )
        ),
        array(
            "type"          => "colorpicker",
            "heading"       => esc_html__("Text Color",'kuteshop'),
            "param_name"    => "info_color",
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('item-adv-color'),
                )
        ),
        array(
            "type"          => "dropdown",
            "heading"       => esc_html__("Position Info",'kuteshop'),
            "param_name"    => "pos_info2",
            "value"         => array(
                esc_html__("None",'kuteshop')   => '',
                esc_html__("Left",'kuteshop')   => 'text-left',
                esc_html__("Right",'kuteshop')   => 'text-right',
                esc_html__("Center",'kuteshop')   => 'text-center',
                ),
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('adv-home6','adv-home17'),
                )
        ),
        array(
            "type"          => "dropdown",
            "heading"       => esc_html__("Position Info",'kuteshop'),
            "param_name"    => "pos_info",
            "value"         => array(
                esc_html__("Left",'kuteshop')   => 'info-left',
                esc_html__("Right",'kuteshop')   => 'info-right',
                ),
        ),
        array(
            "type" => "attach_image",
            "heading" => esc_html__("Image",'kuteshop'),
            "param_name" => "image",
        ),
        array(
            "type"          => "textfield",
            "heading"       => esc_html__("Image Size",'kuteshop'),
            "param_name"    => "size",
            'description'   => esc_html__( 'Enter site thumbnail to crop. [width]x[height]. Example is 300x300', 'kuteshop' ),
        ),
        array(
            "type" => "attach_image",
            "heading" => esc_html__("Image 2",'kuteshop'),
            "param_name" => "image2",
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('adv-home-5'),
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Link",'kuteshop'),
            "param_name" => "link",
        ),
        array(
            "type" => "textfield",
            "holder" => "div",
            "heading" => esc_html__("Title",'kuteshop'),
            "param_name" => "title",
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('bn-adv2','flash-sale','adv-home6','adv-megamenu','adv-home17-left','adv-home17-right'),
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Time",'kuteshop'),
            "param_name" => "time",
            "description"   => esc_html__("Example: 10/10/2018.","kuteshop"),
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('flash-sale'),
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Percent",'kuteshop'),
            "param_name" => "percent",
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('flash-sale','adv-home6'),
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Price",'kuteshop'),
            "param_name" => "price",
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('flash-sale'),
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Sale text",'kuteshop'),
            "param_name" => "sale_text",
            "dependency"    => array(
                "element"   => 'style',
                "value"     => array('flash-sale'),
                )
        ),
        array(
            "type" => "textarea_html",
            "holder" => "div",
            "heading" => esc_html__("Content",'kuteshop'),
            "param_name" => "content",
        ),
    )
));
