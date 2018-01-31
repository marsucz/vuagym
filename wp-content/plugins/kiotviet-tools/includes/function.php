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
        <div class="modal fade" id="addToCartModal" tabindex="-1" role="dialog" aria-labelledby="addToCartModalLabel" aria-hidden="true" style="padding-top: 10%;">
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
                        <button type="button" class="btn btn-default" data-dismiss="modal">Thoát</button>
                        <button type="button" class="btn btn-primary" onclick="window.location.href=\'' . wc_get_cart_url() . '\';">Xem Giỏ Hàng</button>
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
        <div class="modal fade" id="' . $modal_id . '" tabindex="-1" role="dialog" aria-labelledby="' . $modal_id . 'Label" aria-hidden="true" style="padding-top: 5%;">
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