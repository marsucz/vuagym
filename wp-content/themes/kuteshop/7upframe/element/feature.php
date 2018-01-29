<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_feature'))
{
    function s7upf_vc_feature($attr,$content = false)
    {
        $html = $icon_html = '';
        extract(shortcode_atts(array(
            'style'       => 'icon',
            'icon'        => '',
            'type'        => 'fontawesome',
            'icon_fontawesome'        => 'fa fa-adjust',
            'icon_openiconic'        => 'vc-oi vc-oi-dial',
            'icon_typicons'        => 'typcn typcn-adjust-brightness',
            'icon_entypo'        => 'entypo-icon entypo-icon-note',
            'icon_linecons'        => 'vc_li vc_li-heart',
            'title'       => '',
            'link'        => '',
        ),$attr));        
        switch ($style) {
            case 'variable':
                # code...
                break;
            
            default:
                $iconClass = isset( ${'icon_' . $type} ) ? esc_attr( ${'icon_' . $type} ) : 'fa fa-adjust';
                if(!empty($icon)) $icon_html = '<i class="fa '.esc_attr($icon).'" aria-hidden="true"></i>';
                else $icon_html = '<span class="vc_icon_element-icon '.esc_attr($iconClass).'"></span>';
                $html .=    '<div class="item-service2 table border">
                                <div class="service-icon"><a href="'.esc_url($link).'">'.$icon_html.'</a></div>
                                <div class="service-info text-uppercase">
                                    <p><a href="'.esc_url($link).'">'.esc_html($title).'</a></p>
                                </div>
                            </div>';
                break;
        }       
        
        return $html;
    }
}

stp_reg_shortcode('s7upf_feature','s7upf_vc_feature');

vc_map( array(
    "name"      => esc_html__("SV Feature", 'kuteshop'),
    "base"      => "s7upf_feature",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array_merge(s7upf_get_icon_params('style',array('icon')),array(
        array(
            "type" => "dropdown",
            "heading" => esc_html__("Style",'kuteshop'),
            "param_name" => "style",
            "value" => array(
                esc_html__("Icon",'kuteshop')  => 'icon',
                ),
        ),
        array(
            "type" => "textfield",
            "holder" => "div",
            "heading" => esc_html__("Title",'kuteshop'),
            "param_name" => "title",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Link",'kuteshop'),
            "param_name" => "link",
        ),
    ))
));