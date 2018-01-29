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
    function kiotviet_addToCart_alert_message() {
    echo '  <div id="alert-box" class="alert alert-danger" style="display:none;">
                <button id="hide-alert" type="button" class="close">×</button>
                <span id="alert-message"></span><span id="alert-max-quantity" style="font-weight: bold;"></span>
            </div>';
    }
}

if(!function_exists('kiotviet_addToCart_alert_modal')){
    function kiotviet_addToCart_alert_modal(){
        echo '        
        <!-- Kiotviet Plugin Modal Start -->
        <div class="modal fade" id="addToCartModal" tabindex="-1" role="dialog" aria-labelledby="addToCartModalLabel" aria-hidden="true" style="padding-top: 10%;">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title" id="addToCartModalLabel">Thông báo</h4>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Thoát</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Kiotviet Plugin Modal End-->
        ';
        
    }
}