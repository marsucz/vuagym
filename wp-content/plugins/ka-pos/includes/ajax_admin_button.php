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
    
    if ($product->is_type( 'variation' )) {
        $base_product_id = $product->get_parent_id();
        $parent_product = wc_get_product($base_product_id);
        $parent_product->set_date_created(current_time('timestamp',7));
        $parent_product->set_date_modified(current_time('timestamp',7));
        
        $p_categories = $parent_product->get_category_ids();
        foreach ($p_categories as $key => $ca) {
            if ($ca == get_option('mypos_category_sapcohang')) { // Danh muc: Sap co hang
                unset($p_categories[$key]);
            }
        }
        $p_categories[] = get_option('mypos_category_hangmoive'); // Danh muc: Hang moi ve
        $parent_product->set_category_ids($p_categories);
        
        $parent_product->save();
    } else {
        $categories = $product->get_category_ids();
        foreach ($categories as $key => $ca) {
            if ($ca == get_option('mypos_category_sapcohang')) { // Danh muc: Sap co hang
                unset($categories[$key]);
            }
        }
        $categories[] = get_option('mypos_category_hangmoive'); // Danh muc: Hang moi ve
        $product->set_category_ids($categories);
    }
    
    $product->set_date_created(current_time('timestamp',7));
    $product->set_date_modified(current_time('timestamp',7));
    
    if ('private' === $product->get_status()) {
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
    
    $return['deleted'] = delete_post_cache($product_id);
    
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
//    $product->set_date_created(current_time('timestamp',7));
//    $product->set_date_modified(current_time('timestamp',7));
    
    if ($product->is_type( 'variation' )) {
        $base_product_id = $product->get_parent_id();
        $parent_product = wc_get_product($base_product_id);
        
        // Init information
        $p_stock = $parent_product->is_in_stock();
        $dbModel = new DbModel();
        $p_preorder = $dbModel->check_preorder_by_parent_id($base_product_id);
        
        //Nếu tất cả các biến thể đều hết hàng và không có biến thể nào "pre-order" 
        //thì e remove cả 2 danh mục "sắp có hàng" và "Hàng mới về", chuyển tất cả các giá trị của thuộc tính "hạn sử dụng" thành "Đang cập nhật"
        if (!$p_stock && !$p_preorder) {
            $p_categories = $parent_product->get_category_ids();
            foreach ($p_categories as $key => $ca) {
                if ($ca == get_option('mypos_category_sapcohang') || $ca == get_option('mypos_category_hangmoive')) { // Danh muc: Sap co hang
                    unset($p_categories[$key]);
                }
            }
            $parent_product->set_category_ids($p_categories);
            
            $attributes = $parent_product->get_attributes();
            $attr = &$attributes["pa_" . get_option('mypos_tt_han_su_dung')];
            $attr->set_options(get_option('mypos_tt_dang_cap_nhat'));
            $parent_product->set_attributes($attributes);
            
        } //Nếu tồn tại 1 biến thể còn hàng và không có biến thể nào "pre-order" 
          //thì e remove danh mục "sắp có hàng", danh mục "Hàng mới về" e ko đụng tới
        elseif ($p_stock && !$p_preorder) {
            $p_categories = $parent_product->get_category_ids();
            foreach ($p_categories as $key => $ca) {
                if ($ca == get_option('mypos_category_sapcohang')) { // Danh muc: Sap co hang
                    unset($p_categories[$key]);
                }
            }
            $parent_product->set_category_ids($p_categories);
            
        } //Nếu tất cả các biến thể đều là hết hàng và tồn tại 1 biến thể "pre-order" 
          //thì e remove danh mục "Hàng mới về", và tick chọn danh mục "Sắp có hàng", chuyển tất cả các giá trị của thuộc tính "hạn sử dụng" thành "Đang cập nhật"
        elseif (!$p_stock && $p_preorder) {
            $p_categories = $parent_product->get_category_ids();
            foreach ($p_categories as $key => $ca) {
                if ($ca == get_option('mypos_category_hangmoive')) { // Danh muc: Sap co hang
                    unset($p_categories[$key]);
                }
            }
            $p_categories[] = get_option('mypos_category_sapcohang'); // Danh muc: Hang moi ve
            $parent_product->set_category_ids($p_categories);
            
            $attributes = $parent_product->get_attributes();
            $attr = &$attributes["pa_" . get_option('mypos_tt_han_su_dung')];
            $attr->set_options(get_option('mypos_tt_dang_cap_nhat'));
            $parent_product->set_attributes($attributes);
        } //Nếu tồn tại biến thể còn hàng, và tồn tại biến thể "pre-order" 
          //thì giống 2.2.1 (remove danh mục "Sắp có hàng", danh mục "Hàng mới về" không đụng đến)
        elseif ($p_stock && $p_preorder) {
            $p_categories = $parent_product->get_category_ids();
            foreach ($p_categories as $key => $ca) {
                if ($ca == get_option('mypos_category_sapcohang')) { // Danh muc: Sap co hang
                    unset($p_categories[$key]);
                }
            }
            $parent_product->set_category_ids($p_categories);
        }
        
        
        // Luu parent product
        $parent_product->save();
        
    } else {
        // San pham don gian
        $categories = $product->get_category_ids();
        foreach ($categories as $key => $ca) {
            if ($ca == get_option('mypos_category_sapcohang') || $ca == get_option('mypos_category_hangmoive')) { // Danh muc: Sap co hang
                unset($p_categories[$key]);
            }
        }
        $product->set_category_ids($categories);
        
        $attributes = $product->get_attributes();
        $attr = &$attributes["pa_" . get_option('mypos_tt_han_su_dung')];
        $attr->set_options(get_option('mypos_tt_dang_cap_nhat'));
        $product->set_attributes($attributes);
    }
    
    $result = $product->save();
    
    if ($result) {
        $return['status'] = true;
    } else {
        $return['status'] = false;
    }
    
    $return['deleted'] = delete_post_cache($product_id);
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_product_outofstock', 'ja_ajax_mypos_update_product_outofstock' );
add_action( 'wp_ajax_nopriv_mypos_update_product_outofstock', 'ja_ajax_mypos_update_product_outofstock' );


function ja_ajax_mypos_update_product_enable() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    
    if ($product->is_type( 'variation' )) {
        $base_product_id = $product->get_parent_id();
        $parent_product = wc_get_product($base_product_id);
        $parent_product->set_date_created(current_time('timestamp',7));
        $parent_product->set_date_modified(current_time('timestamp',7));
        $parent_product->save();
    }
    
    $product->set_date_created(current_time('timestamp',7));
    $product->set_date_modified(current_time('timestamp',7));
    
    if ('private' === $product->get_status()) {
        $product->set_status('publish');
    }
    
    $result = $product->save();
    
    if ($result) {
        $return['status'] = true;
    } else {
        $return['status'] = false;
    }
    
    $return['deleted'] = delete_post_cache($product_id);
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_product_enable', 'ja_ajax_mypos_update_product_enable' );
add_action( 'wp_ajax_nopriv_mypos_update_product_enable', 'ja_ajax_mypos_update_product_enable' );

function ja_ajax_mypos_update_webprice_by_kvprice() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    $price     = intval($_POST['price']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    
    if ($product->is_type( 'variation' )) {
        $base_product_id = $product->get_parent_id();
        $parent_product = wc_get_product($base_product_id);
        $parent_product->set_date_created(current_time('timestamp',7));
        $parent_product->set_date_modified(current_time('timestamp',7));
        $parent_product->save();
    }
    
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

    $return['deleted'] = delete_post_cache($product_id);
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_webprice_by_kvprice', 'ja_ajax_mypos_update_webprice_by_kvprice' );
add_action( 'wp_ajax_nopriv_mypos_update_webprice_by_kvprice', 'ja_ajax_mypos_update_webprice_by_kvprice' );

/*
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

    $return['deleted'] = delete_post_cache($product_id);
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_kvprice_by_webprice', 'ja_ajax_mypos_update_kvprice_by_webprice' );
add_action( 'wp_ajax_nopriv_mypos_update_kvprice_by_webprice', 'ja_ajax_mypos_update_kvprice_by_webprice' );
 */