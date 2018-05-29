<?php

function build_html_table_carts($item_id = '', $mark = false, $color = '') {
    $result_string = '
                <h3 class="cart-title">Giỏ hàng của bạn</h3>
                <div class="table-responsive top-buffer" style="border: 0px !important;">        
                                <table class="table" style="border: 0px !important;">
                                <thead class="thead-default">
                                    <tr style="white-space: nowrap;">
                                      <th style="text-align: center"></th>
                                      <th style="text-align: center">Tên Sản Phẩm</th>
                                      <th style="text-align: center">Giá</th>
                                      <th style="text-align: center">Số Lượng</th>
                                      <th style="text-align: center">Tổng Cộng</th>
                                    </tr>
                                </thead>
                                <tbody>';
                            //<th>Mã Sản Phẩm</th>
    $cart_items  = WC()->cart->get_cart();
    foreach ($cart_items as $item => $product) {
        $wc_product = $product['data'];
        $product_id = $wc_product->get_id();
//        $product_sku = $wc_product->get_sku();
        $product_name           = mypos_get_variation_title($wc_product);
        $product_quantity       = $product['quantity'];
        $product_link           = $wc_product->get_permalink();
        $product_price          = $wc_product->get_price();
        
        $old_price = '';
        
        $price_regular = $wc_product->get_regular_price();
        
        if ($price_regular && (int)$price_regular > (int)$product_price) {
            $formated_price_regular = kiotViet_formatted_price($price_regular);
            $old_price .= '<del>' . $formated_price_regular . '</del><br/>';
        }
        
        $price_sale = $wc_product->get_sale_price();
        if ($price_sale && (int)$price_sale > (int)$product_price) {
            $formated_price_sale = kiotViet_formatted_price($price_sale);
            $old_price .= '<del>' . $formated_price_sale . '</del><br/>';
        }
        
        $formated_product_price          = kiotViet_formatted_price($product_price);
        
        $product_image  	= $wc_product->get_image('shop_thumbnail');
        $product_total          = kiotViet_formatted_price($product_quantity*$product_price);
        
        $result_string .= "<tr>";
        $result_string .= "<td align='center'>{$product_image}</td>";
        
        if (!empty($item_id) && $mark && $item_id == $product_id) {
            $result_string .= "<td class='mypos-product-title'><span style='color: " . $color . "; font-weight: bold;'>" . $product_name . "</span></td>";
            $result_string .= "<td style='text-align: center'>{$old_price}<span style='color: " . $color . "; font-weight: bold; white-space: nowrap;'>" . $formated_product_price . "</span></td>";
            $result_string .= "<td style='text-align: center'><span style='color: " . $color . "; font-weight: bold'>" . $product_quantity . "</span></td>";
            $result_string .= "<td style='text-align: center'><span style='color: " . $color . "; font-weight: bold; white-space: nowrap;'>" . $product_total . "</span></td>";
        } else {
            $result_string .= "<td class='mypos-product-title'><span style='font-weight: bold;'>" . $product_name . "</span></td>";
            $result_string .= "<td style='text-align: center'>{$old_price}<span style='font-weight: bold; white-space: nowrap;'>" . $formated_product_price . "</span></td>";
            $result_string .= "<td style='text-align: center'><span style='font-weight: bold'>" . $product_quantity . "</span></td>";
            $result_string .= "<td style='text-align: center'><span style='font-weight: bold; white-space: nowrap;'>" . $product_total . "</span></td>";
        }
//        $result_string .= "<td>{$product_sku}</td>";
        
        $result_string .= "</tr>";
    }
    $result_string .= '</tbody></table></div>';
            
    return $result_string;
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
                    <div class="modal-header white">
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

if(!function_exists('kapos_import_detail_modal')){
    function kapos_import_detail_modal($header = '', $detail = ''){
        $return = '        
        <div class="modal fade" id="import-detail" tabindex="-1" role="dialog" aria-labelledby="ImportDetailLabel" aria-hidden="true" style="padding-top: 5%; padding-left: 10%;">
            <div class="modal-dialog modal-lg" id="mypos-modal-dialog">
                <div class="modal-content">
                    <div class="modal-header white">
                        <div class="mypos-modal-header" style="text-align: center">
                            ' . $header . '
                        </div>
                    </div>
                    <div class="modal-body cart-title-body">
                    ' . $detail . '
                    </div>
                </div>
            </div>
        </div>';
        return $return;
    }
}

function build_html_table_import_detail($rows) {
    $result_string = '
                <div class="table-responsive top-buffer" style="border: 0px !important;">        
                                <table class="table" style="border: 0px !important;">
                                <thead class="thead-default">
                                    <tr style="white-space: nowrap;">
                                      <th style="text-align: center">Mã sản phẩm</th>
                                      <th style="text-align: center">Tên sản phẩm</th>
                                      <th style="text-align: center">Số Lượng</th>
                                    </tr>
                                </thead>
                                <tbody>';
    $cart_items  = WC()->cart->get_cart();
    foreach ($rows as $product) {
        
        $result_string .= "<tr>";
        $result_string .= "<td align='center'>{$product['product_code']}</td>";
        $result_string .= "<td align='center'>{$product['product_name']}</td>";
        $result_string .= "<td align='center'>{$product['product_quantity']}</td>";
        $result_string .= "</tr>";
    }
    $result_string .= '</tbody></table></div>';
            
    return $result_string;
}