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
        
        $parent_product->set_catalog_visibility('visible');
        
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
        
        $product->set_catalog_visibility('visible');
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


function process_update_outofstock($product_id) {
    
    $product = wc_get_product($product_id);
    $product->set_stock_status('outofstock');
    $product->save();
    
    if ($product->is_type( 'variation' )) {
        $base_product_id = $product->get_parent_id();
        $parent_product = wc_get_product($base_product_id);
        
        $show_always = $parent_product->get_meta('_mypos_show_always', true);
        if ($show_always != 'yes') {
            $parent_product->set_catalog_visibility('search');
        }
        
        // Init information
        $dbModel = new DbModel();
        $p_stock = $dbModel->check_stock_by_parent_id($base_product_id);
        $p_preorder = $dbModel->check_preorder_by_parent_id($base_product_id);
        
        $return['p_stock'] = $p_stock;
        $return['p_preorder'] = $p_preorder;
        
        //Nếu tất cả các biến thể đều hết hàng và không có biến thể nào "pre-order" 
        //thì e remove cả 2 danh mục "sắp có hàng" và "Hàng mới về", chuyển tất cả các giá trị của thuộc tính "hạn sử dụng" thành "Đang cập nhật"
        if (!$p_stock && !$p_preorder) {
            $return['case'] = 1;
            $p_categories = $parent_product->get_category_ids();
            foreach ($p_categories as $key => $ca) {
                if ($ca == get_option('mypos_category_sapcohang') || $ca == get_option('mypos_category_hangmoive')) { // Danh muc: Sap co hang
                    unset($p_categories[$key]);
                }
            }
            $parent_product->set_category_ids($p_categories);
            
            if (get_option('mypos_tt_han_su_dung') != '0') {
                $attributes = $parent_product->get_attributes();
                if (isset($attributes["pa_" . get_option('mypos_tt_han_su_dung')])) {
                    wp_set_object_terms( $base_product_id, intval(get_option('mypos_tt_dang_cap_nhat')), "pa_" . get_option('mypos_tt_han_su_dung') , false);
                }
            }
                
        } //Nếu tồn tại 1 biến thể còn hàng và không có biến thể nào "pre-order" 
          //thì e remove danh mục "sắp có hàng", danh mục "Hàng mới về" e ko đụng tới
        elseif ($p_stock && !$p_preorder) {
            $return['case'] = 2;
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
            $return['case'] = 3;
            $p_categories = $parent_product->get_category_ids();
            foreach ($p_categories as $key => $ca) {
                if ($ca == get_option('mypos_category_hangmoive')) { // Danh muc: Hang moi ve
                    unset($p_categories[$key]);
                }
            }
            $p_categories[] = get_option('mypos_category_sapcohang'); // Danh muc: Sap co hang
            $parent_product->set_category_ids($p_categories);
            
            if (get_option('mypos_tt_han_su_dung') != '0') {
                $attributes = $parent_product->get_attributes();
                if (isset($attributes["pa_" . get_option('mypos_tt_han_su_dung')])) {
                    wp_set_object_terms( $base_product_id, intval(get_option('mypos_tt_dang_cap_nhat')), "pa_" . get_option('mypos_tt_han_su_dung') , false);
                }
            }
            
        } //Nếu tồn tại biến thể còn hàng, và tồn tại biến thể "pre-order" 
          //thì giống 2.2.1 (remove danh mục "Sắp có hàng", danh mục "Hàng mới về" không đụng đến)
        elseif ($p_stock && $p_preorder) {
            $return['case'] = 4;
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
        $return['case'] = 'simple';
        // San pham don gian
        $categories = $product->get_category_ids();
        foreach ($categories as $key => $ca) {
            if ($ca == get_option('mypos_category_sapcohang') || $ca == get_option('mypos_category_hangmoive')) { // Danh muc: Sap co hang
                unset($categories[$key]);
            }
        }
        
        $product->set_category_ids($categories);
        
        if (get_option('mypos_tt_han_su_dung') != '0') {
            $attributes = $product->get_attributes();
            if (isset($attributes["pa_" . get_option('mypos_tt_han_su_dung')])) {
                wp_set_object_terms( $product_id, intval(get_option('mypos_tt_dang_cap_nhat')), "pa_" . get_option('mypos_tt_han_su_dung') , false);
            }
        }
        
        $show_always = $product->get_meta('_mypos_show_always', true);
        if ($show_always != 'yes') {
            $product->set_catalog_visibility('search');
        }
        
    }
    
    $result = $product->save();
    
    if ($result) {
        $return['status'] = true;
    } else {
        $return['status'] = false;
    }
    
    return $return;
}

function ja_ajax_mypos_update_product_outofstock() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $return = process_update_outofstock($product_id);
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


function ja_ajax_mypos_set_pre_order() {
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
        
        $parent_product->set_catalog_visibility('visible');
        
        $dbModel = new DbModel();
        $variations = $dbModel->get_children_ids($base_product_id);
        $check_condition = true;
        // Sp biến thể thì nếu KHÔNG tồn tại 1 biến thể nào là còn hàng (còn hàng là trạng thái còn hàng, đã bật và ko tick chọn pre-order)
        // thì remove danh mục hàng mới về, tick chọn danh mục sắp có hàng
        foreach ($variations as $child) {
            $child_product = wc_get_product($child['ID']);
            $pre_order = new YITH_Pre_Order_Product( $child['ID'] );
            if ($child_product->is_in_stock() && $child_product->get_status() == 'publish' && $pre_order->get_pre_order_status() == 'no') {
                $check_condition = false;
                $return['ID_is_instock'] = $child['ID'];
                break;
            }
        }
        
        if ($check_condition) {
            $return['variation_process'] = 'yes';
            $p_categories = $parent_product->get_category_ids();
            foreach ($p_categories as $key => $ca) {
                if ($ca == get_option('mypos_category_hangmoive')) { // Danh muc: Hang moi ve
                    unset($p_categories[$key]);
                }
            }
            $p_categories[] = get_option('mypos_category_sapcohang'); // Danh muc: Sap co hang
            $parent_product->set_category_ids($p_categories);
        }
        
        $parent_product->save();
    } else {
        $product->set_catalog_visibility('visible');
        
        $categories = $product->get_category_ids();
        foreach ($categories as $key => $ca) {
            if ($ca == get_option('mypos_category_hangmoive')) { // Danh muc: Hang moi ve
                unset($categories[$key]);
            }
        }
        $categories[] = get_option('mypos_category_sapcohang'); // Danh muc: Sap co hang
        $product->set_category_ids($categories);
    }
    
    $product->set_stock_status('instock');
    $product->set_date_created(current_time('timestamp',7));
    $product->set_date_modified(current_time('timestamp',7));
    
    if ('private' === $product->get_status()) {
        $product->set_status('publish');
    }
    
    $result = $product->save();
    
    $pre_order = new YITH_Pre_Order_Product( $product_id );
    
    if ( 'no' == $pre_order->get_pre_order_status() ) {
        $pre_order->set_pre_order_status('yes');
    }
    
    if ($result) {
        $return['status'] = true;
    } else {
        $return['status'] = false;
    }
    
    $return['deleted'] = delete_post_cache($product_id);
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_set_pre_order', 'ja_ajax_mypos_set_pre_order' );
add_action( 'wp_ajax_nopriv_mypos_set_pre_order', 'ja_ajax_mypos_set_pre_order' );

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

function ja_ajax_mypos_import_file_detail() {
    //Form Input Values
    $import_file_name     = trim($_POST['file_name']);

    $dbModel = new DbModel();
    $rows = $dbModel->kapos_get_importfile_detail($import_file_name);
    
    $html = build_html_table_import_detail($rows);
    $html = kapos_import_detail_modal($import_file_name, $html);
    
    $return['html'] = $html;
    
    wp_send_json_success( $return );
}

add_action( 'wp_ajax_mypos_import_file_detail', 'ja_ajax_mypos_import_file_detail' );
add_action( 'wp_ajax_nopriv_mypos_import_file_detail', 'ja_ajax_mypos_import_file_detail' );

function ja_ajax_mypos_check_exists_file() {
    //Form Input Values
    $import_file_name     = trim($_POST['file_name']);
    
    $upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    $upload_file_path = $upload_dir . '/import-files/' . $import_file_name;
    
    if (file_exists($upload_file_path)) {
       $return['status'] = true;
    } else {
       $return['status'] = false;
    }
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_check_exists_file', 'ja_ajax_mypos_check_exists_file' );
add_action( 'wp_ajax_nopriv_mypos_check_exists_file', 'ja_ajax_mypos_check_exists_file' );


function ja_ajax_mypos_delete_import_file() {
    //Form Input Values
    $import_file_name     = trim($_POST['file_name']);
    
    $upload = wp_upload_dir();
    $upload_dir = $upload['basedir'];
    $upload_file_path = $upload_dir . '/import-files/' . $import_file_name;
    
    if (file_exists($upload_file_path)) {
       $return['exists'] = true;
       if (!unlink($upload_file_path)) {
            $return['status'] = false;
            $return['message'] = "Có lỗi trong quá trình xóa file, vui lòng thử lại.";
       } else {
            $return['status'] = true;
       }
    } else {
       $return['exists'] = false;
       $return['message'] = "File không tồn tại trên hệ thống.";
    }
    
    if ($return['status']) {
        $dbModel = new DbModel();
        $dbModel->kapos_delete_imports($import_file_name);
    }
    wp_send_json_success( $return );
}

add_action( 'wp_ajax_mypos_delete_import_file', 'ja_ajax_mypos_delete_import_file' );
add_action( 'wp_ajax_nopriv_mypos_delete_import_file', 'ja_ajax_mypos_delete_import_file' );


function kapos_set_product_name_modal($product_id, $web_productname, $kv_productname) {
    
    $return = '        
        <div class="modal fade" id="setNameModal" tabindex="-1" role="dialog" aria-labelledby="setNameModalLabel" aria-hidden="true" style="padding-top: 5%;">
            <div class="modal-dialog modal-lg" id="mypos-modal-dialog">
                <div class="modal-content" style="margin-top: 10%">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="setNameModalLabel">Cập nhật tên sản phẩm</h4>
                    </div>
                    <form method="post" id="setNameForm" name="setNameForm" class="set-name-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <input class="form-control" type="hidden" id="product_id" name="product_id" value="' . $product_id . '">
                            <span><strong>KiotViet:</strong> ' . $kv_productname . '</span>
                        </div>
                        <div class="form-group">
                            <span><strong>Website:</strong> ' . $web_productname . '</span>
                        </div>
                        <div class="form-group">
                            <label for="heading">Tên mới:</label>
                            <input class="form-control" type="text" value="' . $web_productname . '" id="new_productname" name="new_productname" placeholder="Nhập tên sản phẩm mới" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        ';
        return $return;
}

function ja_ajax_kapos_get_rename_popup() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    $kv_name     = $_POST['kv_name'];
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    if ($product->is_type( 'variation' )) {
        $base_product_id = $product->get_parent_id();
    } elseif ($product->is_type( 'simple' )) {
        $base_product_id = $product_id;
    } else {
        $base_product_id = $product_id;
    }
    
    if ($base_product_id != $product_id) {
        $cproduct = wc_get_product($base_product_id);
        $product_name = $cproduct->get_name();
    } else {
        $product_name = $product->get_name();
    }
    
    $template = kapos_set_product_name_modal($product_id, $product_name, $kv_name);
    
    $return = $template;
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_kapos_get_rename_popup', 'ja_ajax_kapos_get_rename_popup' );
add_action( 'wp_ajax_nopriv_kapos_get_rename_popup', 'ja_ajax_kapos_get_rename_popup' );

function ja_ajax_kapos_set_product_name() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    $new_name     = $_POST['new_name'];
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    if ($product->is_type( 'variation' )) {
        $base_product_id = $product->get_parent_id();
    } elseif ($product->is_type( 'simple' )) {
        $base_product_id = $product_id;
    } else {
        $base_product_id = $product_id;
    }
    
    if ($base_product_id != $product) {
        $product = wc_get_product($base_product_id);
    }
    
    $product->set_name($new_name);
    
    $return['status'] = $product->save();
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_kapos_set_product_name', 'ja_ajax_kapos_set_product_name' );
add_action( 'wp_ajax_nopriv_kapos_set_product_name', 'ja_ajax_kapos_set_product_name' );