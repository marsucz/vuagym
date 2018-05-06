<?php
/**
 */
if(class_exists("woocommerce")){
    if(!function_exists('ka_vc_user_login'))
    {
        function ka_vc_user_login()
        {
            $current_user = wp_get_current_user();
            
            if (is_user_logged_in()) {
                $html = '<div class="user-cp">
                                <div class="ka-user"><strong><i class="fa fa-user">&nbsp; </i><a href="' . esc_url(home_url('/tai-khoan')) . '"> ' . $current_user->display_name . '</a></strong></div>';
                $html .= '  <div class="tooltip-cp" id="cp_tooltip_text" style="top: 75px; right: 22px;">
                                <ul>
                                    <li>
                                        <a href="' . esc_url(home_url('/tai-khoan')) . '">Thông tin tài khoản</a>
                                    </li>
                                    <li>
                                        <a href="'.esc_url(home_url('/tai-khoan/orders')).'">Đơn hàng</a>
                                    </li>
                                    <li>
                                        <a href="'.esc_url(wc_get_cart_url()).'">Giỏ hàng</a>
                                    </li>
                                    <li>
                                        <a href="' . wp_logout_url(get_permalink()) . '">Đăng xuất</a>
                                    </li>
                                </ul>
                            </div>
                        </div>';
            } else {
                $html = '<div class="user-cp"><div class="ka-user"><strong><a href="' . esc_url(home_url('/tai-khoan')) . '" title="Đăng nhập">Đăng nhập</a> | <a href="' . esc_url(home_url('/tai-khoan')) . '" title="Đăng ký">Đăng ký</a></strong></div></div>';
            }
            return $html;
        }
    }

    stp_reg_shortcode('ka_user_login','ka_vc_user_login');

    vc_map( array(
        "name"      => esc_html__("KA User Login", 'kuteshop'),
        "base"      => "ka_user_login",
        "icon"      => "icon-st",
        "category"  => '7Up-theme',
    ));
}   