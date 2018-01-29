<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_coupon')){
    function s7upf_vc_coupon($attr, $content = false){
        $html = '';
        extract(shortcode_atts(array(
            'title'      => '',
            'image'      => '',
        ),$attr));
        $coupon = s7upf_get_option('enable_coupon');
        $new_in = s7upf_get_option('new_in');
        $newuser = s7upf_is_newuser($new_in);
        $coupon_amount = s7upf_get_option('coupon_amount');
        $default_coupon = s7upf_get_option('default_coupon');
        if(!empty($image)) $image = S7upf_Assets::build_css('background-image:url('.wp_get_attachment_image_url($image,'full').');');
        if($coupon == 'on' && $newuser){
            $html = '<div class="wrap-check-cart19 coupon-element">
                        <a href="#coupon-light-box" class="btn-get-coupon">'.esc_html($title).'</a>
                        <div id="coupon-light-box" class="coupon-light-box white bg-color '.esc_attr($image).'" style="display:none">
                            <a href="#" class="close-light-box"><i class="fa fa-close"></i></a>
                            <div class="inner-coupon">
                                '.wpb_js_remove_wpautop($content, true).'
                                <div class="text-center">
                                    <a data-code="'.$default_coupon.'" href="#" class="apply-coupon get-coupon-button">'.esc_html__("get your coupons","kuteshop").'</a>
                                </div>
                            </div>
                        </div>
                    </div>';
        }
        return $html;
    }
}

stp_reg_shortcode('s7upf_coupon','s7upf_vc_coupon');

vc_map( array(
    "name"      => esc_html__("SV Coupon", 'kuteshop'),
    "base"      => "s7upf_coupon",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "textfield",
            "holder" => "h4",
            "heading" => esc_html__("Title",'kuteshop'),
            "param_name" => "title",
        ),
        array(
            "type" => "attach_image",
            "heading" => esc_html__("Image background",'kuteshop'),
            "param_name" => "image",
        ),
        array(
            "type" => "textarea_html",
            "holder" => "div",
            "heading" => esc_html__("Content",'kuteshop'),
            "param_name" => "content",
        )
    )
));