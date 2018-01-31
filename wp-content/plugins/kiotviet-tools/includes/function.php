<?php

/*
 * Return:
 * 0: Het hang
 * 1: Sap Co Hang
 * 2: Con hang
 */

function kiotViet_get_preOrder_status($item_id) {
    
    $product = wc_get_product( $item_id );
    
    $pre_order_status = -1;
    $pre_order = new YITH_Pre_Order_Product( $item_id );
    
    if ( 'yes' == $pre_order->get_pre_order_status() ) {
            $pre_order_status = 1;  // Sắp có hàng
    } elseif ($product->is_in_stock()) {
            $pre_order_status = 2;  // Còn hàng
    } else {
            $pre_order_status = 0;  // Hết hàng
    }
    
    return $pre_order_status;
    
}