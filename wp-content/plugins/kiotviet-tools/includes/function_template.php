<?php

function write_logs($file_name, $text) {
    
    $file_path = WC_PLUGIN_DIR . '/logs/' . $file_name;
    
    $file = fopen($file_path, "a");
    
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $date = date('Y-m-d H:i:s', time());
    
    $body = "\n" . $date . ' ';
    $body .= $text;
    
    fwrite($file, $body);
    fclose($file);
    
}

if(!function_exists('kiotviet_addToCart_alert_message')){
    function kiotviet_addToCart_alert_message($message = '') {
    return '  <div id="alert-box" class="alert alert-danger alert-box" style="display:none;">
                <button id="hide-alert" type="button" class="close">×</button>
                ' . $message . '
            </div>';
    }
}

if(!function_exists('kiotviet_addToCart_success_message')){
    function kiotviet_addToCart_success_message($message = '') {
    return '  <div id="alert-box" class="alert alert-success alert-box" style="display:none;">
                <button id="hide-alert" type="button" class="close">×</button>
                ' . $message . '
            </div>';
    }
}

if(!function_exists('kiotviet_addToCart_alert_modal')){
    function kiotviet_addToCart_alert_modal($message = '', $carts_table = '', $refresh_button = false){
        $return = '        
        <div class="modal fade" id="addToCartModal" tabindex="-1" role="dialog" aria-labelledby="addToCartModalLabel" aria-hidden="true" style="padding-top: 5%;">
            <div class="modal-dialog modal-lg" id="mypos-modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="mypos-modal-header">
                            ' . $message . '
                        </div>
                    </div>
                    <div class="modal-body cart-title-body">
                    ' . $carts_table . '
                    </div>
                    <div class="modal-footer">';
                    if ($refresh_button) {
                        $return .= '<button type="button" class="btn btn-primary mypos-btn-editcart" onclick="window.location.reload()">Refresh lại trang</button>';
                    } else {
                        $return .= '<button type="button" class="btn btn-primary mypos-btn-close" data-dismiss="modal">Tiếp tục mua hàng</button>';
                    }
                   $return .= '<button type="button" class="btn btn-primary mypos-btn-editcart" onclick="window.location.href=\'' . wc_get_cart_url() . '\';">Chỉnh sửa giỏ hàng</button>
                        <button type="button" class="btn btn-success mypos-btn-dathang" onclick="window.location.href=\'' . wc_get_checkout_url() . '\';">Đặt Hàng</button>
                    </div>
                </div>
            </div>
        </div>
        ';
        return $return;
    }
}

if(!function_exists('kiotviet_checkout_alert_modal')){
    function kiotviet_checkout_alert_modal($message = ''){

        $modal_id = 'checkoutModal';
        
        return '        
        <div class="modal fade" id="' . $modal_id . '" tabindex="-1" role="dialog" aria-labelledby="' . $modal_id . 'Label" aria-hidden="true" style="padding-top: 2%;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="' . $modal_id . 'Label">Thông báo</h4>
                    </div>
                    <div class="modal-body"> ' . $message . '
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Thoát</button>
                        <button type="button" class="btn btn-primary" onclick="window.location.href=\'' . wc_get_cart_url() . '\';">Xem Giỏ Hàng</button>
                    </div>
                </div>
            </div>
        </div>
        ';
    }
}

if(!function_exists('kiotviet_UpdateCart_alert_modal')){
    function kiotviet_UpdateCart_alert_modal($message = '', $cart_item_key = ''){
        $result = '        
        <div class="modal fade" id="updateCartModal" tabindex="-1" role="dialog" aria-labelledby="updateCartModal" aria-hidden="true" style="padding-top: 10%;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="updateCartModal">Thông báo</h4>
                    </div>
                    <div class="modal-body">
                    ' . $message . '
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>';
        if (!empty($cart_item_key)) {
            $result .=  '<button type="button" class="btn btn-primary" id="setMaxQuantity">Đặt Tối Đa</button>';
        }
        $result .= '</div>
                </div>
            </div>
        </div>
        ';
        return $result;
    }
}