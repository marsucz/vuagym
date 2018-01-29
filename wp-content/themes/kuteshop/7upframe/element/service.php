<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_service'))
{
    function s7upf_vc_service($attr,$content = false)
    {
        $html = '';
        extract(shortcode_atts(array(
            'style'      => '',
            'icon'       => '',
            'image'      => '',
            'link'       => '',
        ),$attr));
        switch ($style) {
            case 'style1':
            case 'style2':
            case 'style3':
                $html .=    '<div class="item-about18 white text-center '.esc_attr($style).'">
                                <div class="icon">
                                    <a href="'.esc_url($link).'" class="wobble-horizontal"><i class="fa '.esc_attr($icon).'" aria-hidden="true"></i></a>
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;
            
            default:
                $html .=    '<div class="item-service-footer">
                                <div class="service-icon">
                                    <a href="'.esc_url($link).'" class="wobble-horizontal">'.wp_get_attachment_image($image,'full').'</a>
                                </div>
                                <div class="service-info">
                                    '.wpb_js_remove_wpautop($content, true).'
                                </div>
                            </div>';
                break;
        }        
        
        return $html;
    }
}

stp_reg_shortcode('s7upf_service','s7upf_vc_service');

vc_map( array(
    "name"      => esc_html__("SV Service", 'kuteshop'),
    "base"      => "s7upf_service",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type"          => "dropdown",
            "holder"        => "div",
            "heading"       => esc_html__("Style Post",'kuteshop'),
            "param_name"    => "style",
            "value"         => array(
                esc_html__("Default","kuteshop")   => '',
                esc_html__("Home 18(yellow)","kuteshop")   => 'style1',
                esc_html__("Home 18(green)","kuteshop")   => 'style2',
                esc_html__("Home 18(blue)","kuteshop")   => 'style3',
                ),
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
                'value' => array('style1','style2','style3'),
            ),
            'description' => esc_html__( 'Select icon from library.', 'kuteshop' ),
        ),
        array(
            "type" => "attach_image",
            "heading" => esc_html__("Image",'kuteshop'),
            "param_name" => "image",
            'dependency' => array(
                'element' => 'style',
                'value' => array(''),
            ),
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Link",'kuteshop'),
            "param_name" => "link",
        ),
        array(
            "type" => "textarea_html",
            "holder" => "div",
            "heading" => esc_html__("Content",'kuteshop'),
            "param_name" => "content",
        )
    )
));