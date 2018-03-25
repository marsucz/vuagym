<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */
// Start at 16/6/2016
if(!function_exists('s7upf_vc_header_meta'))
{
    function s7upf_vc_header_meta($attr)
    {
        $html = '';
        extract(shortcode_atts(array(
            'style'             => 'check-cart4',
            'check_list'        => '',
            'check_list8'        => '',
            'ac_icon'           => 'fa-lock',
            'ac_icon_login'     => 'fa-lock',
            'wl_icon'           => 'fa-heart-o',
            'wl_link'           => '#',
            'co_icon'           => 'fa-check-square-o',
            'co_link'           => '#',
            'cart_icon'         => 'fa-shopping-basket',
            'account_content'   => '',
            'account_content_login'   => '',
        ),$attr));
        switch ($style) {
            case 'home-8':
            case 'cart11':
                if($style == 'home-8') $style = 'cart8';
                $check_array = explode(',', $check_list8);
                $html .=    '<ul class="whistlist-'.esc_attr($style).' list-none">';        
                if(in_array('cart', $check_array)){
                    $html .=    '<li>
                                    <div class="mini-cart-box mini-'.esc_attr($style).'">
                                        <a class="mini-cart-link" href="'.esc_url(wc_get_cart_url()).'">
                                            <span class="mini-cart-icon"><i class="fa '.esc_attr($cart_icon).'" aria-hidden="true"></i></span>
                                            <span class="mini-cart-number cart-item-count">0</span>
                                        </a>';
                    $html .=            '<div class="mini-cart-content content-mini-cart">                                    
                                            <div class="mini-cart-main-content">'.s7upf_mini_cart().'</div>                    
                                            <input class="num-decimal" type="hidden" value="'.get_option("woocommerce_price_num_decimals").'">
                                            <input class="get-currency" type="hidden" value=".'.get_option("woocommerce_currency").'">
                                        </div>';
                    $html .=        '</div>
                                </li>';
                }
                if(in_array('wishlist', $check_array)){
                    $html .=    '<li>
                                    <a href="'.esc_url($wl_link).'" class="wishlist-top-link"><i class="fa '.esc_attr($wl_icon).'" aria-hidden="true"></i></a>
                                </li>';
                }
                if(in_array('checkout', $check_array)){
                    $html .=    '<li>
                                    <a href="'.esc_url($co_link).'" class="wishlist-top-link"><i class="fa '.esc_attr($co_icon).'" aria-hidden="true"></i></a>
                                </li>';
                }
                $html .=    '</ul>';
                break;
            
            default:
                $check_array = explode(',', $check_list);
                $html .=    '<div class="check-cart '.esc_attr($style).'">';        
                if(in_array('cart', $check_array)){
                    $cart_html =    '<div class="mini-cart-box">
                                    <a class="mini-cart-link" href="'.esc_url(wc_get_cart_url()).'">
                                        <span class="mini-cart-icon"><i class="fa '.esc_attr($cart_icon).'" aria-hidden="true"></i></span>
                                        <span class="mini-cart-number cart-item-count">0</span>
                                    </a>';
                    $cart_html .=        '<div class="mini-cart-content content-mini-cart">                                    
                                        <div class="mini-cart-main-content">'.s7upf_mini_cart().'</div>                    
                                        <input class="num-decimal" type="hidden" value="'.get_option("woocommerce_price_num_decimals").'">
                                        <input class="get-currency" type="hidden" value=".'.get_option("woocommerce_currency").'">
                                    </div>';
                    $cart_html .=    '</div>';
                    $html .= apply_filters('s7upf_tempalte_mini_cart',$cart_html);;                    
                }
                if(in_array('wishlist', $check_array)){
                    $html .=    '<div class="wishlist-box">
                                    <a href="'.esc_url($wl_link).'" class="wishlist-top-link"><i class="fa '.esc_attr($wl_icon).'" aria-hidden="true"></i></a>
                                </div>';
                }
                if(in_array('account', $check_array)){
                    if(is_user_logged_in() && !empty($account_content_login)){
                        $html .=    '<div class="checkout-box">
                                        <a href="#" class="checkout-link"><i class="fa '.esc_attr($ac_icon_login).'" aria-hidden="true"></i></a>';
                        if(!empty($account_content_login)){
                            $html .=    '<ul class="list-checkout list-unstyled">';
                            parse_str(urldecode($account_content_login), $account_list);
                            foreach ($account_list as $value) {
                                $icon_html = '';
                                if(!empty($value['icon'])){
                                    if(strpos($value['icon'],'lnr') !== false) $icon_html = '<span class="lnr '.$value['icon'].'"></span>';
                                    else $icon_html =   '<i class="fa '.$value['icon'].'"></i>';
                                }
                                $html .=            '<li><a href="'.esc_url($value['url']).'">'.$icon_html.' '.$value['title'].'</a></li>';
                            }
                            $html .=    '<li><a href="'.wp_logout_url( get_permalink() ).'"><i class="fa fa-sign-out" aria-hidden="true"></i> '.esc_html__("Logout","kuteshop").'</a></li>';
                            $html .=        '</ul>';
                        }
                        $html .=    '</div>';
                    }
                    else{
                        $html .=    '<div class="checkout-box">
                                        <a href="#" class="checkout-link"><i class="fa '.esc_attr($ac_icon).'" aria-hidden="true"></i></a>';
                        if(!empty($account_content)){
                            $html .=    '<ul class="list-checkout list-unstyled">';
                            parse_str(urldecode($account_content), $account_list);
                            foreach ($account_list as $value) {
                                $icon_html = '';
                                if(!empty($value['icon'])){
                                    if(strpos($value['icon'],'lnr') !== false) $icon_html = '<span class="lnr '.$value['icon'].'"></span>';
                                    else $icon_html =   '<i class="fa '.$value['icon'].'"></i>';
                                }
                                $html .=            '<li><a href="'.esc_url($value['url']).'">'.$icon_html.' '.$value['title'].'</a></li>';
                            }
                            $html .=        '</ul>';
                        }
                        $html .=    '</div>';
                    }
                }
                $html .=    '</div>';
                break;
        }        
        return $html;
    }
}

stp_reg_shortcode('sv_header_meta','s7upf_vc_header_meta');

vc_map( array(
    "name"      => esc_html__("SV Header meta", 'kuteshop'),
    "base"      => "sv_header_meta",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "dropdown",
            "holder" => "div",
            "heading" => esc_html__("Style",'kuteshop'),
            "param_name" => "style",
            "value"     => array(
                esc_html__("Default",'kuteshop')   => 'check-cart4',
                esc_html__("Home 8",'kuteshop')   => 'home-8',
                esc_html__("Home 11",'kuteshop')   => 'cart11',
                )
        ),
        array(
            "type" => "checkbox",
            "holder" => "div",
            "heading" => esc_html__("Box Display",'kuteshop'),
            "param_name" => "check_list8",
            "value"     => array(
                esc_html__("Mini Cart",'kuteshop') => 'cart',
                esc_html__("Wishlist",'kuteshop')  => 'wishlist',
                esc_html__("CheckOut",'kuteshop')   => 'checkout',
                ),
            "dependency"    => array(
                "element"   => "style",
                "value"   => array("home-8","cart11"),
                )
        ),
        array(
            "type" => "checkbox",
            "holder" => "div",
            "heading" => esc_html__("Box Display",'kuteshop'),
            "param_name" => "check_list",
            "value"     => array(
                esc_html__("Mini Cart",'kuteshop') => 'cart',
                esc_html__("Wishlist",'kuteshop')  => 'wishlist',
                esc_html__("Account",'kuteshop')   => 'account',
                ),
            "dependency"    => array(
                "element"   => "style",
                "value"   => "check-cart4",
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Cart Icon",'kuteshop'),
            "param_name" => "cart_icon",
            'edit_field_class'=>'vc_col-sm-12 vc_column sv_iconpicker',
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Wishlist Icon",'kuteshop'),
            "param_name" => "wl_icon",
            'edit_field_class'=>'vc_col-sm-12 vc_column sv_iconpicker',
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Wishlist Link",'kuteshop'),
            "param_name" => "wl_link",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Checkout Icon",'kuteshop'),
            "param_name" => "co_icon",
            'edit_field_class'=>'vc_col-sm-12 vc_column sv_iconpicker',
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Checkout Link",'kuteshop'),
            "param_name" => "co_link",
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Account Icon",'kuteshop'),
            "param_name" => "ac_icon",
            'edit_field_class'=>'vc_col-sm-12 vc_column sv_iconpicker',
            "dependency"    => array(
                "element"   => "style",
                "value"   => "check-cart4",
                )
        ),
        array(
            "type" => "textfield",
            "heading" => esc_html__("Account Icon Login",'kuteshop'),
            "param_name" => "ac_icon_login",
            'edit_field_class'=>'vc_col-sm-12 vc_column sv_iconpicker',
            "dependency"    => array(
                "element"   => "style",
                "value"   => "check-cart4",
                )
        ),
        array(
            "type" => "add_icon_link",
            "heading" => esc_html__("Account Content",'kuteshop'),
            "param_name" => "account_content",
            "dependency"    => array(
                "element"   => "style",
                "value"   => "check-cart4",
                )
        ),
        array(
            "type" => "add_icon_link",
            "heading" => esc_html__("Account Content Login",'kuteshop'),
            "param_name" => "account_content_login",
            "dependency"    => array(
                "element"   => "style",
                "value"   => "check-cart4",
                )
        ),
    )
));