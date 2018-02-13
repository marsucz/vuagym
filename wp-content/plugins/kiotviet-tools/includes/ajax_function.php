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
    $product->save();
    
    $return['status'] = true;
    
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
    $product->save();
    
    $return['status'] = true;
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_product_outofstock', 'ja_ajax_mypos_update_product_outofstock' );
add_action( 'wp_ajax_nopriv_mypos_update_product_outofstock', 'ja_ajax_mypos_update_product_outofstock' );