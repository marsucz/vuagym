<?php

function write_logs($file_name, $text) {
    
    $file_path = WC_PLUGIN_DIR . '/logs/' . $file_name;
    
    $file = fopen($file_path, "a");
    
    $date = date('Y-m-d H:i:s');
    
    $body = "\n" . $date . ' ';
    $body .= $text;
    
    fwrite($file, $body);
    fclose($file);
    
}

if(!function_exists('kiotviet_addToCart_alert_message')){
    function kiotviet_addToCart_alert_message($message = '') {
    return '  <div id="alert-box" class="alert alert-danger" style="display:none;">
                <button id="hide-alert" type="button" class="close">×</button>
                ' . $message . '
            </div>';
    }
}

if(!function_exists('kiotviet_addToCart_success_message')){
    function kiotviet_addToCart_success_message($message = '') {
    return '  <div id="alert-box" class="alert alert-success" style="display:none;">
                <button id="hide-alert" type="button" class="close">×</button>
                ' . $message . '
            </div>';
    }
}

if(!function_exists('kiotviet_addToCart_alert_modal')){
    function kiotviet_addToCart_alert_modal($message = '', $carts_table = ''){
        return '        
        <div class="modal fade" id="addToCartModal" tabindex="-1" role="dialog" aria-labelledby="addToCartModalLabel" aria-hidden="true" style="padding-top: 2%;">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="addToCartModalLabel">Thông báo</h4>
                    </div>
                    <div class="modal-body">
                    ' . $message . $carts_table . '
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default mypos-btn-close" data-dismiss="modal">X</button>
                        <button type="button" class="btn btn-primary" onclick="window.location.href=\'' . wc_get_cart_url() . '\';">Chỉnh Sửa Giỏ Hàng</button>
                        <button type="button" class="btn btn-success" onclick="window.location.href=\'' . wc_get_checkout_url() . '\';">Đặt Hàng</button>
                    </div>
                </div>
            </div>
        </div>
        ';
        
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

if(!function_exists('kiotviet_online_support')){
    function kiotviet_online_support() {
        $result = '<div class="online-support">
                    <div class="dropup force-open">
                        <a class="btn btn--support" data-toggle="dropdown-2">
                            Hỗ trợ trực tuyến
                        </a>
                        <ul class="dropdown-2-menu dropdown-2-menu-right dropdown-2--support">
                            <li>
                                <a href="tel:18006122">
                                    <i class="icon-icon-phone"></i> GỌI HOTLINE
                                </a>
                            </li>
                            <li>
                                <a href="https://m.me/orchardvn" target="_blank" rel="noopener">
                                    <i class="icon-icon-chat"></i>
                                    CHAT FB</a>
                            </li>
                            <li>
                                <a href="http://zalo.me/3580274298351782783" target="_blank" rel="noopener">
                                    <i class="icon-icon-zalo"></i> CHAT ZALO
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>';

        return $result;
    }
}