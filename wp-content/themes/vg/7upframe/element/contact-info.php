<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_contact_info'))
{
    function s7upf_vc_contact_info($attr)
    {
        $html = '';
        extract(shortcode_atts(array(
            'icon'       => '',
            'title'      => '',
            'link'       => '',
            'last_item'  => '',
        ),$attr));
        $html .=    '<div class="item-contact-info '.$last_item.'">
                        <a class="contact-icon" href="'.esc_url($link).'">
                            <i class="fa '.$icon.' before"></i>
                            <i class="fa '.$icon.'"></i>
                        </a>
                        <h2><a href="'.esc_url($link).'">'.$title.'</a></h2>
                    </div>';
        
        return $html;
    }
}

stp_reg_shortcode('sv_contact_info','s7upf_vc_contact_info');

vc_map( array(
    "name"      => esc_html__("SV Contact Info", 'kuteshop'),
    "base"      => "sv_contact_info",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "textfield",
            "heading" => esc_html__("Icon",'kuteshop'),
            "param_name" => "icon",
            'edit_field_class'=>'vc_col-sm-12 vc_column sv_iconpicker'
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
        array(
            "type" => "dropdown",
            "heading" => esc_html__("Last Item",'kuteshop'),
            "param_name" => "last_item",
            "value"     => array(
                esc_html__("No",'kuteshop')   => '',
                esc_html__("Yes",'kuteshop')   => 'last-item',
                )
        ),
    )
));