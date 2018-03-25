<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 26/12/15
 * Time: 10:00 AM
 */

if(!function_exists('sv_vc_mailchimp'))
{
    function sv_vc_mailchimp($attr, $content = false)
    {
        $html = $bg_class = '';
        extract(shortcode_atts(array(
            'style'         => 'footer-box',
            'title'         => '',
            'des'           => '',
            'placeholder'   => '',
            'submit'        => '',
            'form_id'       => '',
        ),$attr));
        $form_html = apply_filters('sv_remove_autofill',do_shortcode('[mc4wp_form id="'.$form_id.'"]'));
        switch ($style) {
            case 'home9':
                $html .=    '<div class="global-event">
                                <div class="clearfix">
                                    <div class="item-global-event">';
                if(!empty($title)) $html .=    '<h2 class="title18">'.esc_html($title).'</h2>';
                if(!empty($des)) $html .=    '<p>'.esc_html($title).'</p>';
                $html .=                '<div class="event-form sv-mailchimp-form" data-placeholder="'.$placeholder.'" data-submit="'.$submit.'">
                                            '.$form_html.'
                                        </div>
                                    </div>
                                    <div class="item-global-event">
                                        '.wpb_js_remove_wpautop($content, true).'
                                    </div>
                                </div>
                            </div>';
                break;
            
            default:
                $html .=    '<div class="newsletter-form '.esc_attr($style).' sv-mailchimp-form" data-placeholder="'.$placeholder.'" data-submit="'.$submit.'">
                                <h2 class="title14">'.esc_html($title).'</h2>
                                '.$form_html.'
                            </div>';
                break;
        }        
        return $html;
    }
}

stp_reg_shortcode('sv_mailchimp','sv_vc_mailchimp');

vc_map( array(
    "name"      => esc_html__("SV MailChimp", 'kuteshop'),
    "base"      => "sv_mailchimp",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "dropdown",
            'holder'      => 'div',
            "heading" => esc_html__("Style",'kuteshop'),
            "param_name" => "style",
            "value"     => array(
                esc_html__("Default",'kuteshop')     => 'footer-box',
                esc_html__("Footer Home 6",'kuteshop')     => 'footer-box footer-box6',
                esc_html__("Home 9",'kuteshop')     => 'home9',
                esc_html__("Home 17",'kuteshop')     => 'newsletter-form17 footer-box',
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Form ID",'kuteshop'),
            "param_name" => "form_id",
        ),
        array(
            "type" => "textfield",
            'holder'      => 'div',
            "heading" => esc_html__("Title",'kuteshop'),
            "param_name" => "title",
        ),
        array(
            "type" => "textfield",
            'holder'      => 'div',
            "heading" => esc_html__("Description",'kuteshop'),
            "param_name" => "des",
            "dependency"     => array(
                "element"   => 'style',
                "value"   => 'home9',
                )
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
            "type" => "textarea_html",
            "heading" => esc_html__("More Info",'kuteshop'),
            "param_name" => "content",
            "dependency"     => array(
                "element"   => 'style',
                "value"   => 'home9',
                )
        ),
    )
));