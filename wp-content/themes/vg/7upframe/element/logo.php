<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_logo'))
{
    function s7upf_vc_logo($attr)
    {
        $html = $logo = '';
        extract(shortcode_atts(array(
            'logo_img'      => '',
        ),$attr));
        if(!empty($logo_img)){
            $img = wp_get_attachment_image_src( $logo_img ,"full");
            $logo .= $img[0];
        }
        else{
            $logo .= s7upf_get_option('logo');
        }
        if(!empty($logo)){
            $html .=    '<div class="logo">
                            <h1 class="hidden">'.get_bloginfo('name', 'display').'</h1>
                            <a href="'.esc_url(get_home_url('/')).'"><img src="'.esc_url($logo).'" alt=""></a>   
                        </div>';             
        }
        
        return $html;
    }
}

stp_reg_shortcode('s7upf_logo','s7upf_vc_logo');

vc_map( array(
    "name"      => esc_html__("SV Logo", 'kuteshop'),
    "base"      => "s7upf_logo",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "attach_image",
            "holder" => "div",
            "heading" => esc_html__("Logo image",'kuteshop'),
            "param_name" => "logo_img",
        )
    )
));