<?php

require_once 'kiotviet_api.php';

function ja_ajax_mypos_update_product_instock() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    
    $product->set_stock_status('instock');
    $product->set_date_created(current_time('timestamp',7));
    $product->set_date_modified(current_time('timestamp',7));
    
    $categories = $product->get_category_ids();
    foreach ($categories as $key => $ca) {
        if ($ca == 81) { // Danh muc: Sap co hang
            unset($categories[$key]);
        }
    }
    $categories[] = 86; // Danh muc: Hang moi ve
    $product->set_category_ids($categories);
    if ('publish' !== $product->get_status()) {
        $product->set_status('publish');
    }
    
    $result = $product->save();
    
    $pre_order = new YITH_Pre_Order_Product( $product_id );
    
    if ( 'yes' == $pre_order->get_pre_order_status() ) {
        $pre_order->set_pre_order_status('no');
    }
    
    if ($result) {
        $return['status'] = true;
    } else {
        $return['status'] = false;
    }
    
//    try {
//        $del_result = delete_post_cache($product_id);
//        if ($del_result) {
//            $return['deleted'] = true;
//        } else {
//            $return['deleted'] = false;
//        }
//    } catch (Exception $ex) {
//        $return['deleted'] = $ex->getMessage();
//    }
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_product_instock', 'ja_ajax_mypos_update_product_instock' );
add_action( 'wp_ajax_nopriv_mypos_update_product_instock', 'ja_ajax_mypos_update_product_instock' );


function ja_ajax_mypos_update_product_outofstock() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    $product->set_stock_status('outofstock');
    $product->set_date_created(current_time('timestamp',7));
    $product->set_date_modified(current_time('timestamp',7));
    $result = $product->save();
    
    if ($result) {
        $return['status'] = true;
    } else {
        $return['status'] = false;
    }
    
//    try {
//        $del_result = delete_post_cache($product_id);
//        $return = array_merge($return, $del_result);
//    } catch (Exception $ex) {
//        $return['deleted'] = $ex->getMessage();
//    }
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_product_outofstock', 'ja_ajax_mypos_update_product_outofstock' );
add_action( 'wp_ajax_nopriv_mypos_update_product_outofstock', 'ja_ajax_mypos_update_product_outofstock' );


function ja_ajax_mypos_update_webprice_by_kvprice() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    $price     = intval($_POST['price']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    $product->set_date_created(current_time('timestamp',7));
    $product->set_date_modified(current_time('timestamp',7));
    
    $sale_price = $product->get_sale_price();
    if ( !$sale_price || empty($sale_price) || is_null($sale_price)) {
//        $product->set_price($price);
        $product->set_regular_price($price);
    } else {
        $product->set_sale_price($price);
    }
    
    $result = $product->save();
    
    if ($result) {
        $return['status'] = true;
    } else {
        $return['status'] = false;
    }

//    try {
//        $del_result = delete_post_cache($product_id);
//        $return = array_merge($return, $del_result);
//    } catch (Exception $ex) {
//        $return['deleted'] = $ex->getMessage();
//    }
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_webprice_by_kvprice', 'ja_ajax_mypos_update_webprice_by_kvprice' );
add_action( 'wp_ajax_nopriv_mypos_update_webprice_by_kvprice', 'ja_ajax_mypos_update_webprice_by_kvprice' );


function ja_ajax_mypos_update_kvprice_by_webprice() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    $price     = intval($_POST['price']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $api = new KiotViet_API();
    
    $result = $api->set_product_price($product_id, $price);
    
    if ($result) {
        $return['status'] = true;
    } else {
        $return['status'] = false;
    }

//    try {
//        $del_result = delete_post_cache($product_id);
//        $return = array_merge($return, $del_result);
//    } catch (Exception $ex) {
//        $return['deleted'] = $ex->getMessage();
//    }
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_kvprice_by_webprice', 'ja_ajax_mypos_update_kvprice_by_webprice' );
add_action( 'wp_ajax_nopriv_mypos_update_kvprice_by_webprice', 'ja_ajax_mypos_update_kvprice_by_webprice' );