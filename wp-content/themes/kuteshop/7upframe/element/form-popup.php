<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_form_popup'))
{
    function s7upf_vc_form_popup($attr)
    {
        $html = '';
        extract(shortcode_atts(array(
            'title'      => '',
            'title2'     => '',
            'title3'     => '',
            'image'      => '',
            'link'       => '',
            'link2'      => '',
            'placeholder'   => '',
            'submit'        => '',
            'form_id'       => '',
            'text'       => '',
            'text2'       => '',
        ),$attr));
        $form_html = apply_filters('sv_remove_autofill',do_shortcode('[mc4wp_form id="'.$form_id.'"]'));
        $html .=    '<div id="boxes-content">
                        <div class="window" id="dialog">
                            <div class="window-popup text-center"> 
                                <div class="content-popup">
                                    <a href="#" class="close-popup color"><i class="fa fa-times" aria-hidden="true"></i></a>
                                    <h2 class="title30">'.esc_html($title).'</h2>
                                    <h2 class="title30">'.esc_html($title2).'</h2>
                                    <h3 class="title18">'.esc_html($title3).'</h3>
                                    '.wp_get_attachment_image($image,'full',0,array('class'=>'image-popup')).'
                                    <div class="sv-mailchimp-form" data-placeholder="'.$placeholder.'" data-submit="'.$submit.'">
                                        '.$form_html.'
                                    </div>
                                    <div class="confirm-user clearfix">
                                        <a href="'.esc_url($link).'" class="pull-left">'.esc_html($text).'</a>
                                        <a href="'.esc_url($link2).'" class="pull-right">'.esc_html($text2).'</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="mask"></div>
                    </div>';
        return $html;
    }
}

stp_reg_shortcode('sv_form_popup','s7upf_vc_form_popup');

vc_map( array(
    "name"      => esc_html__("SV Form Popup", 'kuteshop'),
    "base"      => "sv_form_popup",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "textfield",
            "holder" => "div",
            "heading" => esc_html__("Title",'kuteshop'),
            "param_name" => "title",
        ),
        array(
            "type" => "textfield",
            "holder" => "div",
            "heading" => esc_html__("Title 2",'kuteshop'),
            "param_name" => "title2",
        ),
        array(
            "type" => "textfield",
            "holder" => "div",
            "heading" => esc_html__("Title 3",'kuteshop'),
            "param_name" => "title3",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Form ID",'kuteshop'),
            "param_name" => "form_id",
        ),
        array(
            "type" => "attach_image",
            "heading" => esc_html__("Image",'kuteshop'),
            "param_name" => "image",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Placeholder Input",'kuteshop'),
            "param_name" => "placeholder",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Submit Label",'kuteshop'),
            "param_name" => "submit",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Text footer",'kuteshop'),
            "param_name" => "text",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Link footer",'kuteshop'),
            "param_name" => "link",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Text footer 2",'kuteshop'),
            "param_name" => "text2",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Link footer 2",'kuteshop'),
            "param_name" => "link2",
        ),
    )
));