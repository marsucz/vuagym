<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */
if(class_exists("woocommerce")){
    if(!function_exists('s7upf_vc_mini_cart'))
    {
        function s7upf_vc_mini_cart($attr,$content = false)
        {
            $html = $header_cart_html = $info_content = '';
            extract(shortcode_atts(array(
                'style'         => 'mini-cart1',
            ),$attr));
            switch ($style) {
                case 'mini-cart6':
                    $header_cart_html = '<a class="mini-cart-link border radius" href="'.esc_url(wc_get_cart_url()).'">
                                            <span class="mini-cart-icon color"><i class="fa fa-shopping-basket" aria-hidden="true"></i></span>
                                            <span class="mini-cart-number"><span class="cart-item-count">0</span> '.esc_html__("item:","kuteshop").' <b class="color total-mini-cart-price">'.WC()->cart->get_cart_total().'</b></span>
                                        </a>';
                    $html .=    '<div class="mini-cart-box '.$style.'">
                                    '.$header_cart_html.'
                                    <div class="mini-cart-content content-mini-cart">                                    
                                        <div class="mini-cart-main-content">'.s7upf_mini_cart().'</div>                    
                                        <input id="num-decimal" type="hidden" value="'.get_option("woocommerce_price_num_decimals").'">
                                        <input class="get-currency" type="hidden" value=".'.get_option("woocommerce_currency").'">
                                    </div>
                                </div>';
                    break;

                case 'mini-cart10':
                    $header_cart_html = '<a class="mini-cart-link" href="'.esc_url(wc_get_cart_url()).'">
                                            <span class="mini-cart-icon"><img src="'.esc_url(get_template_directory_uri().'/assets/css/images/theme/icon-cart.png').'" alt=""></span>
                                            <span class="mini-cart-number radius cart-item-count">0</span>
                                        </a>';
                    $html .=    '<div class="mini-cart-box '.$style.'">
                                    '.$header_cart_html.'
                                    <div class="mini-cart-content content-mini-cart">                                    
                                        <div class="mini-cart-main-content">'.s7upf_mini_cart().'</div>                    
                                        <input class="num-decimal" type="hidden" value="'.get_option("woocommerce_price_num_decimals").'">
                                        <input class="get-currency" type="hidden" value=".'.get_option("woocommerce_currency").'">
                                    </div>
                                </div>';
                    break;

                case 'mini-cart3':
                    $header_cart_html = '<a class="mini-cart-link" href="'.esc_url(wc_get_cart_url()).'">
                                            <span class="mini-cart-icon"><i class="fa fa-shopping-basket" aria-hidden="true"></i></span>
                                            <span class="mini-cart-number border radius">
                                                <strong class="text-uppercase">'.esc_html__("Cart:","kuteshop").'</strong>
                                                <span class="cart-item-count">0</span> '.esc_html__(" Item","kuteshop").' - <b class="color text-uppercase total-mini-cart-price">'.WC()->cart->get_cart_total().'</b>
                                            </span>
                                        </a>';
                    $html .=    '<div class="mini-cart-box mini-cart1 '.$style.'">
                                    '.$header_cart_html.'
                                    <div class="mini-cart-content content-mini-cart">                                    
                                        <div class="mini-cart-main-content">'.s7upf_mini_cart().'</div>                    
                                        <input class="num-decimal" type="hidden" value="'.get_option("woocommerce_price_num_decimals").'">
                                        <input class="get-currency" type="hidden" value=".'.get_option("woocommerce_currency").'">
                                    </div>
                                </div>';
                    break;

                case 'mini-cart18':
                case 'mini-cart17':
                    $header_cart_html = '<a class="mini-cart-link" href="'.esc_url(wc_get_cart_url()).'">
                                            <span class="mini-cart-icon"><i class="fa fa-shopping-basket" aria-hidden="true"></i></span>
                                            <span class="mini-cart-number"><sup class="cart-item-count">0</sup>'.esc_html__("My Cart","kuteshop").'</span>
                                        </a>';
                    $html .=    '<div class="mini-cart-box '.$style.'">
                                    '.$header_cart_html.'
                                    <div class="mini-cart-content content-mini-cart">                                    
                                        <div class="mini-cart-main-content">'.s7upf_mini_cart().'</div>                    
                                        <input class="num-decimal" type="hidden" value="'.get_option("woocommerce_price_num_decimals").'">
                                        <input class="get-currency" type="hidden" value=".'.get_option("woocommerce_currency").'">
                                    </div>
                                </div>';
                    break;
                
                case 'mini-cart-ms':
                    $header_cart_html = '<a class="mini-cart-link" href="'.esc_url(wc_get_cart_url()).'">
                                            <span class="mini-cart-icon"><img style="width: 1em; height: 1em; max-width: 100%; " src="'.esc_url(get_template_directory_uri().'/assets/css/images/theme/store-cart.png').'" alt=""></span>
                                            <span class="mini-cart-number radius cart-item-count">0</span>
                                            <span class="text">'.esc_html__("My Cart","kuteshop").'</span>
                                        </a>';
                    $html .=    '<div class="mini-cart-box '.$style.'">
                                    '.$header_cart_html.'
                                    <div class="mini-cart-content content-mini-cart">                                    
                                        <div class="mini-cart-main-content">'.s7upf_mini_cart().'</div>                    
                                        <input class="num-decimal" type="hidden" value="'.get_option("woocommerce_price_num_decimals").'">
                                        <input class="get-currency" type="hidden" value=".'.get_option("woocommerce_currency").'">
                                    </div>
                                </div>';
                    break;
                
                default:
                    $header_cart_html = '<a class="mini-cart-link" href="'.esc_url(wc_get_cart_url()).'">
                                            <span class="mini-cart-icon"><i class="fa fa-shopping-basket" aria-hidden="true"></i></span>
                                            <span class="mini-cart-number border radius cart-item-count">0</span>
                                        </a>';
                    $html .=    '<div class="mini-cart-box '.$style.'">
                                    '.$header_cart_html.'
                                    <div class="mini-cart-content content-mini-cart">                                    
                                        <div class="mini-cart-main-content">'.s7upf_mini_cart().'</div>                    
                                        <input class="num-decimal" type="hidden" value="'.get_option("woocommerce_price_num_decimals").'">
                                        <input class="get-currency" type="hidden" value=".'.get_option("woocommerce_currency").'">
                                    </div>
                                </div>';
                    break;
            }
            return apply_filters('s7upf_tempalte_mini_cart',$html);
        }
    }

    stp_reg_shortcode('s7upf_mini_cart','s7upf_vc_mini_cart');

    vc_map( array(
        "name"      => esc_html__("SV Mini Cart", 'kuteshop'),
        "base"      => "s7upf_mini_cart",
        "icon"      => "icon-st",
        "category"  => '7Up-theme',
        "params"    => array(
            array(
                "type" => "dropdown",
                "heading" => esc_html__("Style",'kuteshop'),
                "param_name" => "style",
                "value"     => array(
                    esc_html__("Default",'kuteshop')   => 'mini-cart1',
                    esc_html__("Home 3",'kuteshop')   => 'mini-cart3',
                    esc_html__("Home 6",'kuteshop')   => 'mini-cart6',
                    esc_html__("Home 10",'kuteshop')   => 'mini-cart10',
                    esc_html__("Home 17",'kuteshop')   => 'mini-cart17',
                    esc_html__("Home 18",'kuteshop')   => 'mini-cart18',
                    esc_html__("Ka MS",'kuteshop')   => 'mini-cart-ms',
                    )
            ),            
        )
    ));
}