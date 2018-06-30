<?php

require_once 'function.php';

function kawoo_set_regular_price_modal($product_id, $product_name, $regular_price) {
    
    $return = '        
        <div class="modal fade" id="setPriceModal" tabindex="-1" role="dialog" aria-labelledby="setPriceModalLabel" aria-hidden="true" style="padding-top: 5%;">
            <div class="modal-dialog modal-lg" id="mypos-modal-dialog">
                <div class="modal-content" style="margin-top: 10%">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="setPriceModalLabel">Cập nhật giá sản phẩm</h4>
                    </div>
                    <form method="post" id="setPriceForm" name="setPriceForm" class="set-price-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <input class="form-control" type="hidden" id="product_id" name="product_id" value="' . $product_id . '">
                            <label for="heading">Tên sản phẩm</label>
                            <p class="form-control-static">' . $product_name . '</p>
                        </div>
                        <div class="form-group">
                            <label for="heading">Giá gốc mới:</label>
                            <input class="form-control" type="number" id="new_regular" name="new_regular" placeholder="Nhập giá sản phẩm mới" required>
                        </div>
                        <div class="form-group">
                            <label for="heading">Giá sale mới:</label>
                            <input class="form-control" id="new_sale" name="new_sale" value="' . $regular_price .'" readonly>
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

function ja_ajax_kawoo_get_price_popup() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    $product_name = mypos_get_variation_title($product);
    $regular_price = $product->get_regular_price();
    
    $template = kawoo_set_regular_price_modal($product_id, $product_name, $regular_price);
    
    $return = $template;
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_kawoo_get_price_popup', 'ja_ajax_kawoo_get_price_popup' );
add_action( 'wp_ajax_nopriv_kawoo_get_price_popup', 'ja_ajax_kawoo_get_price_popup' );

function ja_ajax_kawoo_set_prices() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    
    //If empty return error
    if(!$product_id){
            wp_send_json(array('error' => __('Missing Product ID!')));
    }
    
    $regular     = intval($_POST['new_regular']);
    $sale        = intval($_POST['new_sale']);
    
    $return[] = update_post_meta( $product_id, '_regular_price', $regular );
    $return[] = update_post_meta( $product_id, '_sale_price', $sale );
    
    tuandev_update_price_variation_field($product_id);
    $return['deleted'] = delete_post_cache($product_id);
    
    wp_send_json_success( $return );
}

add_action( 'wp_ajax_kawoo_set_prices', 'ja_ajax_kawoo_set_prices' );
add_action( 'wp_ajax_nopriv_kawoo_set_prices', 'ja_ajax_kawoo_set_prices' );

function ja_ajax_kawoo_set_categories() {
    //Form Input Values
    $product_id     = intval($_POST['product_id']);
    
    //If empty return error
    if(!$product_id){
        wp_send_json(array('error' => __('Missing Product ID!')));
    }
		
    $product = wc_get_product($product_id);
    
    $categories = $product->get_category_ids();
    
    if ($categories) {
        $new_categories = $categories;
    
        foreach ($categories as $cate) {
            $parents = get_ancestors($cate, 'product_cat');
            foreach ($parents as $cat_parent) {
                if (in_array($cat_parent, $categories)) {
                } else {
                    $new_categories[] = $cat_parent;
                }
            }
        }

        $product->set_category_ids($new_categories);

        $result = $product->save();

        if ($result) {
            $return['status'] = true;
            $return['deleted'] = delete_post_cache($product_id);
        } else {
            $return['status'] = false;
        }
    } else {
        $return['status'] = false;
    }
    
    wp_send_json_success( $return );
}

add_action( 'wp_ajax_kawoo_set_categories', 'ja_ajax_kawoo_set_categories' );
add_action( 'wp_ajax_nopriv_kawoo_set_categories', 'ja_ajax_kawoo_set_categories' );

function ja_ajax_kawoo_get_bulk_price_popup() {
    //Form Input Values
    $product_list = $_POST['list_product'];
    
    $accepted_products = array();
    foreach ($product_list as $product_id) {
        if (ctype_digit($product_id)) {
            $accepted_products[] = $product_id;
        }
    }
    
    $template = kawoo_set_bulk_price_modal($accepted_products);
    
    $return = $template;
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_kawoo_get_bulk_price_popup', 'ja_ajax_kawoo_get_bulk_price_popup' );
add_action( 'wp_ajax_nopriv_kawoo_get_bulk_price_popup', 'ja_ajax_kawoo_get_bulk_price_popup' );

function kawoo_set_bulk_price_modal($product_list) {
    
    $list_view = implode(', ', $product_list);
    $list_post = implode(' ', $product_list);
    
    $return = '        
        <div class="modal fade" id="setBulkPriceModal" tabindex="-1" role="dialog" aria-labelledby="setBulkPriceModal" aria-hidden="true" style="padding-top: 5%;">
            <div class="modal-dialog modal-lg" id="mypos-modal-dialog">
                <div class="modal-content" style="margin-top: 10%">
                    <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="setBulkPriceModalLabel">Cập nhật giá nhiều sản phẩm</h4>
                    </div>
                    <form method="post" id="setBulkPriceForm" name="setBulkPriceForm" class="set-bulk-price-form">
                    <div class="modal-body">
                        <div class="form-group">
                            <input class="form-control" type="hidden" id="product_list" name="product_list" value="' . $list_post . '">
                            <label for="heading">Danh sách sản phẩm đã chọn</label>
                            <p class="form-control-static">' . $list_view . '</p>
                        </div>
                        <div class="form-group">
                            <label for="heading">Tỷ giá gốc so với giá sale:</label>
                            <input class="form-control" type="number" step="0.01" id="ty_gia" name="ty_gia" placeholder="Nhập tỷ giá so với giá sale: 1.2, 1.5, 2 ..." required>
                        </div>
                        <div class="form-group">
                            <label for="heading">Làm tròn lên:</label>
                            <input class="form-control" type="number" id="lam_tron" name="lam_tron" placeholder="Nhập giá làm tròn: 10 20 50 ..." required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button id="bulk-price-submit" type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
                </div>
            </div>
        </div>
        ';
        return $return;
}

function ja_ajax_kawoo_set_bulk_prices() {
    //Form Input Values
    
    $product_list = $_POST['product_list'];
    $ty_gia = $_POST['ty_gia'];
    $lam_tron = $_POST['lam_tron'];
    $lam_tron = $lam_tron * 1000;
    $products = explode(' ', $product_list);
    
    foreach ($products as $product_id) {
        $regular_price = get_post_meta($product_id, '_regular_price', true);
        $sale_price = $ty_gia * $regular_price;
        
        if ($sale_price % $lam_tron != 0) {
            $temp = (int)($sale_price/$lam_tron);
//            $temp_return['temp'] = $temp;
//            $temp_return['lam_tron'] = $lam_tron;
            $sale_price = ($temp + 1) * $lam_tron;
        }
        update_post_meta( $product_id, '_sale_price', $sale_price);
        tuandev_update_price_variation_field($product_id);
        delete_post_cache($product_id);
        
        $temp_return['id'] = $product_id;
        $temp_return['new_sale'] = $sale_price;
        $temp_return['format_sale'] = kiotViet_formatted_price($sale_price);
        $return[] = $temp_return;
    }
    
    wp_send_json_success( $return );
}

add_action( 'wp_ajax_kawoo_set_bulk_prices', 'ja_ajax_kawoo_set_bulk_prices' );
add_action( 'wp_ajax_nopriv_kawoo_set_bulk_prices', 'ja_ajax_kawoo_set_bulk_prices' );