<?php

/**
 * Plugin Name: KiotViet Tools
 * Plugin URI: http://minhtuanit.me
 * Description: A small tools to help you manage your KiotViet - Wordpress
 * Version: 1.0
 * Author: Tuan Dao
 * Author URI: http://minhtuanit.me
 * License: GPL2
 * Created On: 24-01-2018
 * Updated On: 24-01-2018
 */
// Define WC_PLUGIN_DIR.
if (!defined('WC_PLUGIN_DIR')) {
    define('WC_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Define WC_PLUGIN_FILE.
if (!defined('WC_PLUGIN_URL')) {
    define('WC_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('KV_RETAILER')) {
    define('KV_RETAILER', 'vuagymtest2');
}

if (!defined('KV_CLIENT_ID')) {
    define('KV_CLIENT_ID', '713f68d1-ecbb-4a27-8a2a-342f94bc16c1');
}

if (!defined('KV_CLIENT_SECRET')) {
    define('KV_CLIENT_SECRET', '76137D87B075C1417B744E71FA636FF6C91A3E94');
}

require_once('autoload.php');

add_action('plugins_loaded', 'kiotviet_tools_plugin_init');

register_activation_hook(__FILE__, 'kiotviet_product_create_db');

function kiotviet_tools_plugin_init() {
    add_action('admin_menu', 'kiotviet_tools_admin_menu');
    add_action('login_init', 'send_frame_options_header', 10, 0);
    add_action('admin_init', 'send_frame_options_header', 10, 0);
    
    add_option('mypos_add_to_cart', 1);
    add_option('mypos_ajax_cart', 1);
    add_option('mypos_checkout', 1);
    
    add_option('mypos_max_quantity', 100);
    add_option('preorder_max_quantity', 150);
    add_option('mypos_error_max_quantity', 200);
    
    add_option('sync_by_web_show_type', 0);
    
    add_option('kiotviet_retailer', KV_RETAILER);
    add_option('kiotviet_client_id', KV_CLIENT_ID);
    add_option('kiotviet_client_secret', KV_CLIENT_SECRET);
}

function kiotviet_tools_admin_menu() {
    //Maketing Tools
    add_menu_page('KiotViet Tools', 'KiotViet Tools', 'manage_options', 'kiotviet-tools', 'function_kiotviet_tools_page', 'dashicons-admin-multisite', 4);
    add_submenu_page('kiotviet-tools', __('KiotViet'), __('KiotViet'), 'manage_options', 'kiotviet-tools');
    
    add_submenu_page('kiotviet-tools', __('Testing'), __('Testing'), 'manage_options', 'kiotviet-testing', 'function_testing_page');
    
    $sync_product = add_submenu_page('kiotviet-tools', __('Đông bộ sản phẩm'), __('Đông bộ sản phẩm'), 'manage_options', 'mypos-sync', 'function_mypos_sync_page');
    add_action("load-$sync_product", "manual_sync_page_options");
    
    $manual_sync_web_page = add_submenu_page('kiotviet-tools', __('Manual Sync: Web'), __('Manual Sync: Web'), 'manage_options', 'mypos-manual-sync-web', 'function_manual_sync_web');
    add_action("load-$manual_sync_web_page", "manual_sync_page_options");
    
    $manual_sync_web_page = add_submenu_page('kiotviet-tools', __('Manual Sync: KiotViet'), __('Manual Sync: KiotViet'), 'manage_options', 'mypos-manual-sync-kiotviet', 'function_manual_sync_kiotviet');
    add_action("load-$manual_sync_web_page", "manual_sync_page_options");
            
//    add_submenu_page('kiotviet-tools', __('Sync KiotViet'), __('Sync KiotViet'), 'manage_options', 'kiotviet-sync', 'function_kiotviet_sync_page');
    add_submenu_page('kiotviet-tools', __('Cài Đặt'), __('Cài Đặt'), 'manage_options', 'kiotviet-options', 'function_mypos_options_page');
    add_submenu_page('kiotviet-tools', __('Lấy Mã SP KiotViet'), __('Lấy Mã SP KiotViet'), 'manage_options', 'get-kiotviet-products', 'function_get_sku_kiotviet');
    add_submenu_page('kiotviet-tools', __('Send SMS'), __('Send SMS'), 'manage_options', 'mypos-single-sms', 'send_single_sms_page');
}

function function_kiotviet_tools_page() {
    
    load_assets_common_admin();
    
    echo '<div class="wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-plus-circle fa-fw"></i>
                            <strong><font color="blue">KiotViet Tools</font></strong>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-success">
                                    <b>KiotViet Tools for Woocommerce:</b> Công cụ để quản lý KiotViet từ Wordpress
                            </div>
                        ';
                        
    echo '</div></div></div></div></div>';
}

function function_mypos_options_page() {
    
    if (isset($_POST['mypos-add-to-cart'])) {
        
        update_option('mypos_add_to_cart', intval($_POST['mypos-add-to-cart']));
        update_option('mypos_ajax_cart', intval($_POST['mypos-ajax-cart']));
        update_option('mypos_checkout', intval($_POST['mypos-checkout']));
        
        update_option('mypos_max_quantity', $_POST['mypos-max-quantity']);
        update_option('preorder_max_quantity', $_POST['preorder-max-quantity']);
        update_option('mypos_error_max_quantity', $_POST['mypos-error-max-quantity']);
        
        update_option('kiotviet_retailer', $_POST['kiotviet-retailer']);
        update_option('kiotviet_client_id', $_POST['kiotviet-client-id']);
        update_option('kiotviet_client_secret', $_POST['kiotviet-client-secret']);
       
    }
    
    load_assets_page_options();
    
    echo '<div class="wrap"><div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Cài Đặt KiotViet Plugin
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" method="POST">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Bật/Tắt Plugin</label>
                                                <p class="help-block">Bật tắt chức năng: Thêm Sản Phẩm trên trang sản phẩm</p>
                                                <select class="form-control" id="mypos-add-to-cart" name="mypos-add-to-cart" required>';
                                                if (get_option('mypos_add_to_cart')) {
                                                    echo '<option value="1" selected>Bật</option>
                                                    <option value="0">Tắt</option>';
                                                } else {
                                                    echo '<option value="1">Bật</option>
                                                    <option value="0" selected>Tắt</option>';
                                                }
                                                echo '</select>
                                                    
                                                <p class="help-block">Bật tắt chức năng: Tự động kiểm tra và cập nhật trên trang giỏ hàng</p>
                                                <select class="form-control" id="mypos-ajax-cart" name="mypos-ajax-cart" required>';
                                                if (get_option('mypos_ajax_cart')) {
                                                    echo '<option value="1" selected>Bật</option>
                                                    <option value="0">Tắt</option>';
                                                } else {
                                                    echo '<option value="1">Bật</option>
                                                    <option value="0" selected>Tắt</option>';
                                                }
                                                echo '</select>

                                                <p class="help-block">Bật tắt chức năng: Kiểm tra trên trang thanh toán</p>
                                                <select class="form-control" id="mypos-checkout" name="mypos-checkout" required>';
                                                if (get_option('mypos_checkout')) {
                                                    echo '<option value="1" selected>Bật</option>
                                                    <option value="0">Tắt</option>';
                                                } else {
                                                    echo '<option value="1">Bật</option>
                                                    <option value="0" selected>Tắt</option>';
                                                }
                                                echo '</select>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Số lượng SP Tối đa khi Pre-Order</label>
                                                <input class="form-control" type="number" id="preorder-max-quantity" name="preorder-max-quantity" value="' . get_option('preorder_max_quantity') . '" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Số lượng SP Tối đa khi không tồn tại Mã SP</label>
                                                <input class="form-control" type="number" id="mypos-max-quantity" name="mypos-max-quantity" value="' . get_option('mypos_max_quantity') . '" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Số lượng SP Tối đa khi đặt hàng lỗi (lỗi KiotViet API)</label>
                                                <input class="form-control" type="number" id="mypos-error-max-quantity" name="mypos-error-max-quantity" value="' . get_option('mypos_error_max_quantity') . '" required>
                                            </div>                                            
                                        </div>
                                        <div class="col-lg-6">
                                                <div class="form-group">
                                                    <label>KiotViet Retailer</label>
                                                    <input class="form-control" type="text" id="kiotviet-retailer" name="kiotviet-retailer" value="' . get_option('kiotviet_retailer') . '" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>KiotViet Client ID</label>
                                                    <input class="form-control" type="text" id="kiotviet-client-id" name="kiotviet-client-id" value="' . get_option('kiotviet_client_id') . '" required>
                                                </div>
                                                <div class="form-group">
                                                    <label>KiotViet Client Secret</label>
                                                    <input class="form-control" type="text" id="kiotviet-client-secret" name="kiotviet-client-secret" value="' . get_option('kiotviet_client_secret') . '" required>
                                                </div>
                                        </div>
                                        <div class="col-lg-12">
                                            <button type="submit" class="btn btn-primary">Lưu Cài Đặt</button>
                                            <button type="reset" class="btn btn-default">Nhập Lại</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>';
}

function function_get_sku_kiotviet() {
    
    load_assets_match_sku();
    
    $dbModel = new DbModel();
    $api = new KiotViet_API();
    
    echo '<div class="wrap">';
    echo '<div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-plus-circle fa-fw"></i>
                            <strong><font color="blue">Kết Nối Website với Mã Sản Phẩm trên KiotViet</font></strong>
                        </div>
                        <div class="panel-body">';
                        
//    if (isset($_POST['process_updateAllProducts'])) {
//        
//        $count = $api->get_all_product_sku();
//        
//        echo '<div class="alert alert-success">
//                        <strong> Đã lấy mới ' . $count . ' Mã sản phẩm.
//                        </strong>
//            </div>';
//        
//    }
    
    if (isset($_POST['process_deleteAllProducts'])) {
        
        $dbModel = new DbModel();
        
        $result = $dbModel->kiotviet_delete_all_products_sku();
        
        if ($result) {
            $count = $api->get_all_product_sku();
            
            if ($count > 0) {
                echo '<div class="alert alert-success">
                        <strong> Đã cập nhật MÃ SẢN PHẨM từ KiotViet thành công!
                        </strong>
                </div>';
            } else {
                echo '<div class="alert alert-danger">
                        <strong> Có lỗi trong quá trình cập nhật. Vui lòng thực hiện lại.
                        </strong>
                </div>';
            }
        }
    }
        
//    echo '          <form role="form" method="post" align="center">
//                                <input type="hidden" id="process_updateAllProducts" name="process_updateAllProducts">
//                                <button type="submit" class="btn btn-success btn-mypos-width">Kết Nối Mã Sản Phẩm</button>
//                    </form>';
    echo '          <form role="form" method="post" align="center">
                                <input type="hidden" id="process_deleteAllProducts" name="process_deleteAllProducts">
                                <button type="submit" class="btn btn-success btn-mypos-width">Cập nhật MÃ SẢN PHẨM</button>
                    </form>
                    <div class="alert alert-warning" style="margin-top: 15px; margin-bottom: 0!important">
                        Chú ý: Chỉ cần "Cập nhật MÃ SẢN PHẨM" khi có mã sản phẩm <b>MỚI</b> trên KiotViet.
                    </div>';
    
    echo '</div></div></div></div>';
        echo '<div class="row"> 
            <div class="col-lg-12">';
        echo '<div class="panel panel-default">
                        <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i>
                            Danh sách Mã Sản Phẩm trên KiotViet
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                <div class="row">
                            
                            </div></div>
                            <div class="row">
                            <div class="col-sm-12">
                            <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                               <thead>
                                <tr role="row">
                                   <th class="sorting_desc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;" aria-sort="descending" >STT</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">ID Sản Phẩm</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Mã Sản Phẩm</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Thời gian lấy Mã SP</th>
                                </tr>
                             </thead>
                                <tbody>';
    
    $count = 0;
    $all_products = $dbModel->kiotviet_get_count_all_products();
    foreach($all_products as $product) {
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            
            echo '<td id="product_id_' . $count . '">' . $product['product_id'] . '</td>';
            echo '<td id="product_code_' . $count . '">' . $product['product_code'] . '</td>';
            echo '<td id="product_updated_' . $count . '" >' . $product['product_updated'] . '</td>';
            echo '</tr>';
    }
                    echo '</tbody>
                            </table></div></div>
                            <!-- <div class="row"><div class="col-sm-6"><div class="dataTables_info" id="dataTables-example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div><div class="col-sm-6"><div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate"><ul class="pagination"><li class="paginate_button previous disabled" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_previous"><a href="#">Previous</a></li><li class="paginate_button active" aria-controls="dataTables-example" tabindex="0"><a href="#">1</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">2</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">3</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">4</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">5</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">6</a></li><li class="paginate_button next" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_next"><a href="#">Next</a></li></ul></div></div></div></div> --> 
                            <!-- /.table-responsive -->
                            
                        </div>
                        <!-- /.panel-body -->
                    </div>';
    
    echo '</div></div></div>';
}

function function_kiotviet_sync_page() {
    
    load_assets_sync_page();
    
    echo '<div class="wrap">';
    echo '<div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-plus-circle fa-fw"></i>
                            <strong><font color="blue">Sync KiotViet</font></strong>
                        </div>
                        <div class="panel-body">
                    <form role="form" method="post" align="center">
                                <input type="hidden" id="process_manual_sync_web" name="process_manual_sync_web">
                                <button type="submit" class="btn btn-success btn-mypos-width">Manual Sync: WEB</button>
                    </form>
                    <form role="form" method="post" align="center">
                                <input type="hidden" id="process_manual_sync_kiotviet" name="process_manual_sync_kiotviet">
                                <button type="submit" class="btn btn-success btn-mypos-width">Manual Sync: KIOTVIET</button>
                    </form>
                    <form role="form" method="post" align="center">
                                <input type="hidden" id="process_auto_sync" name="process_auto_sync">
                                <button type="submit" class="btn btn-success btn-mypos-width">Auto Sync</button>
                    </form>
                    ';
    
    echo '</div></div></div></div>';
    
    if (isset($_POST['process_manual_sync_web'])) {
//        manual_sync_kiotviet();
    }
    
    if (isset($_POST['process_manual_sync_kiotviet'])) {
        manual_sync_kiotviet();
    }
    
    if (isset($_POST['process_auto_sync'])) {
        auto_sync_kiotviet();
    }
    
}

function auto_sync_kiotviet() {
    
    //ini_set('memory_limit', '-1');
    set_time_limit(3600);
    
    $file_name = 'KiotViet_autosync_' . date("d-m-Y_H-i-s") . '.csv';
    
    
    $file_path = WC_PLUGIN_DIR . 'logs/' . $file_name;
    $file_url = plugin_dir_url(__FILE__) . 'logs/' . $file_name;
    
    $file = fopen($file_path, "w");
    
    $title[0] = 'STT';
    $title[1] = 'KiotViet';
    $title[2] = 'Web';
    $title[3] = 'KiotViet còn hàng nhưng Web hết hàng';
    $title[4] = 'KiotViet hết hàng nhưng Web còn hàng';
    $title[5] = 'KiotViet có SP này nhưng Web không có';
    $title[6] = 'Web có SP này nhưng KiotViet không có';
    $title[7] = 'Exception';
    
    $title = array_map("utf8_decode", $title);
    fputcsv($file, $title);
    
    $dbModel = new DbModel();
    $api = new KiotViet_API();
    
    $count = 0;
    
    $woo_all_products = get_woocommerce_product_list();
    $kiotviet_all_products = $api->get_all_products();
    
    $matched_products = array();
    
    foreach ($woo_all_products as $woo_key => $woo_single) {
        $match = false;
        $temp_prd = array();
        foreach ($kiotviet_all_products as $kv_key => $kv_single) {
            if ($kv_single['sku'] == $woo_single['sku']) {
                $match = true;
                $temp_prd['kv'] = $kiotviet_all_products[$kv_key];
                unset($kiotviet_all_products[$kv_key]);
                break;
            }
        }
        
        if ($match) {
            $temp_prd['woo'] = $woo_all_products[$woo_key];
            unset($woo_all_products[$woo_key]);
            $matched_products[] = $temp_prd;
        }
    }
    
    foreach($matched_products as $product) {
        
//             Skip if have nothing to change / everything is ok
            if ($product['kv']['stock'] == $product['woo']['stock']) {
                continue;
            }
            
            $line_product = array();
            
            
            $count++;
            $line_product[0] = $count;
//            echo '<tr role="row" row_id="'. $count .'">';
//            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['kv']['stock']) {
                $kv_stock_status = "Còn hàng";
            } else {
                $kv_stock_status = "Hết hàng";
            }
            
            $line_product[1] = "{$product['kv']['name']}-Mã:{$product['kv']['sku']}-TT:{$kv_stock_status}-SL:{$product['kv']['quantity']}-Giá:{$product['kv']['price']}";
            
            if ($product['woo']['stock']) {
                $woo_stock_status = "Còn hàng";
            } else {
                $woo_stock_status = "Hết hàng";
            }
            
            $edit_link = get_permalink($product['woo']['id']);
            $line_product[2] = "{$product['woo']['name']}-Mã:{$product['woo']['sku']}-TT:{$woo_stock_status}-SL:{$product['woo']['quantity']}-Giá:{$product['woo']['price']}";
//            echo '<td>';
            
            if ($product['kv']['stock'] && !$product['woo']['stock']) {
                
                $product_id = $product['woo']['id'];
                $product_temp = wc_get_product($product_id);
                $product_temp->set_stock_status('instock');
                $product_temp->save();

                $pre_order = new YITH_Pre_Order_Product( $product_id );

                if ( 'yes' == $pre_order->get_pre_order_status() ) {
                    $pre_order->set_pre_order_status('no');
                }
                
                $line_product[3] = 'Done';
//                echo '  <button id="updateInStock_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-success" title="Cập nhật có hàng trên Web cho sản phẩm này" onclick="updateInStock('. $product['woo']['id'] .');"><i class="fa fa-tasks"></i>  Cập nhật có hàng</button>';
            } else {
                $line_product[3] = '';
            }
            
            if (!$product['kv']['stock'] && $product['woo']['stock']) {
                $product_id = $product['woo']['id'];
                $product_temp = wc_get_product($product_id);
                $product_temp->set_stock_status('outofstock');
                $product_temp->save();
                $line_product[4] = 'Done';
//                echo '  <button id="updateOutOfStock_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-danger" title="Cập nhật hết hàng trên Web cho sản phẩm này" onclick="updateOutOfStock('. $product['woo']['id'] .');"><i class="fa fa-tasks"></i>  Cập nhật hết hàng</button>';
            } else {
                $line_product[4] = '';
            }
            
//            if ($product['kv']['price'] != $product['woo']['price']) {
//                echo '  <button id="updateWebPrice_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-info" title="Cập nhật giá trên Web cho sản phẩm này theo giá trên KiotViet" onclick="updateWebPrice_byKVPrice('. $product['woo']['id'] .',' . $product['kv']['price'] . ');"><i class="fa fa-anchor"></i>  Cập nhật giá Web theo KiotViet</button>';
//                echo '  <button id="updateKVPrice_' . $product['kv']['id'] . '" type="button" class="btn btn-mypos btn-warning" title="Cập nhật giá trên KiotViet cho sản phẩm này theo giá trên Web" onclick="updateKVPrice_byWebPrice('. $product['kv']['id'] .',' . $product['woo']['price'] . ');"><i class="fa fa-anchor"></i>  Cập nhật giá KiotViet theo Web</button>';
//            }
            
            
//            echo '</td>';
//            echo '</tr>';
            $line_product[5] = '';
            $line_product[6] = '';
            $line_product[7] = '';
            
            $line_product = array_map("utf8_decode", $line_product);
            fputcsv($file, $line_product);
            
    }
    
    foreach($kiotviet_all_products as $product) {
        
            $line_product = array();
        
            $count++;
            $line_product[0] = $count;
//            echo '<tr role="row" row_id="'. $count .'">';
//            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['stock']) {
                $kv_stock_status = "Còn hàng";
            } else {
                $kv_stock_status = "Hết hàng";
            }
            
            $line_product[1] = "ID:{$product['id']}-{$product['name']}-Mã:{$product['sku']}-TT:{$kv_stock_status}-SL:{$product['quantity']}-Giá:{$product['price']}";
            $line_product[2] = 'X';
            
            $line_product[3] = '';
            $line_product[4] = '';
            $line_product[5] = 'Miss';
            $line_product[6] = '';
//            echo "<td>Không có sản phẩm</td>";
            if (empty($product['sku'])) {
                $line_product[7] = 'Không có Mã SP';
            } else {
                $line_product[7] = 'Không tồn tại SP trên Web';
            }
//            echo '</tr>';
            
            $line_product = array_map("utf8_decode", $line_product);
            fputcsv($file, $line_product);
    }
    
    
    
    foreach($woo_all_products as $product) {
            $count++;
            $line_product[0] = $count;
            
            if ($product['stock']) {
                $woo_stock_status = "Còn hàng";
            } else {
                $woo_stock_status = "Hết hàng";
            }

            $line_product[1] = 'X';
            
            $line_product[2] = "{$product['name']}-Mã:{$product['sku']}-TT:{$woo_stock_status}-SL:{$product['quantity']}-Giá:{$product['price']}";
            
            $line_product[3] = '';
            $line_product[4] = '';
            $line_product[5] = '';
            $line_product[6] = 'Miss';
            
            if (empty($product['sku'])) {
                $line_product[7] = 'Không có Mã SP';
            } else {
                $line_product[7] = 'Không tồn tại SP trên KiotViet';
            }
            
            $line_product = array_map("utf8_decode", $line_product);
            fputcsv($file, $line_product);
    }
    
    fclose($file);
    
    
    echo '<div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-plus-circle fa-fw"></i>
                            <strong><font color="blue">Sync Hoàn Tất!</font></strong>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-success">
                                    Quá trình Sync hoàn tất, bạn có thể tải về báo cáo <a href="'. $file_url .'" class="alert-link">tại đây</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
}

function manual_sync_page_options() {
//    $option = 'per_page';
//    $args = array(
//        'label' => 'Số sản phẩm trên mỗi trang ',
//        'default' => 10,
//        'option' => 'products_per_page'
//    );
//    
//    add_screen_option( $option, $args );
    
    $option = 'per_page2';
    $args = array(
        'label' => 'Số sản phẩm trên mỗi trang ',
        'default' => 10,
        'option' => 'products_per_page2'
    );
    
    add_screen_option( $option, $args );
}

function manual_sync_set_options($status, $option, $value) {
	if ( 'products_per_page' == $option ) return $value;
}
add_filter('set-screen-option', 'manual_sync_set_options', 10, 3);


function function_manual_sync_web() {
    
    load_assets_manual_sync_table();
    
    echo '<div class="wrap">
        <h2>KiotViet Manual Sync: theo sản phẩm trên Web</h2>
        <form method="post">';
    
    $myListTable = new KiotViet_ManualSyncWeb_List();
    $myListTable->prepare_items();
    $myListTable->display();
    
    echo '</form></div>';
    
}

function function_testing_page() {
    
//    $kv = new KiotViet_API();
//    
//    $products = $kv->get_product_paged(20, 0);
//    
//    echo '<pre>';
//    print_r($products['all_products']);
//    echo '</pre>';
//    exit;
    
//    $prod = wc_get_product_id_by_sku('SP000655');
//    
//    echo '<pre>';
//    print_r($prod);
//    echo '</pre>';
//    exit;
        
    $product = wc_get_product(6547);
    
    echo $product->get_sale_price();
    
}

function function_manual_sync_kiotviet() {
    
    load_assets_manual_sync_table();
    
    echo '<div class="wrap">
        <h2>KiotViet Manual Sync: theo sản phẩm trên KiotViet</h2>
        <form method="post">';
    
    $myListTable = new KiotViet_ManualSyncKiotViet_List();
    $myListTable->prepare_items();
    $myListTable->display();
    
    echo '</form></div>';
    
}

function function_mypos_sync_page() {
    
    load_assets_manual_sync_table();
    
    echo '<div class="wrap">
        <h2>ĐỒNG BỘ HÓA SẢN PHẨM</h2>';
        
    $active_tab = '';
    
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    }
    
    if (isset($_POST['sync_by_web_show_type'])) {
        update_option('sync_by_web_show_type', $_POST['sync_by_web_show_type']);
    }
    
    echo '<h2 class="nav-tab-wrapper">
            <a href="?page=mypos-sync" class="nav-tab ' . ($active_tab == "" ? "nav-tab-active" : "") . '">Welcome</a>
            <a href="?page=mypos-sync&tab=sync_by_web" class="nav-tab ' . ($active_tab == "sync_by_web" ? "nav-tab-active" : "") . '">Đồng bộ hóa thủ công theo Web</a>
            <a href="?page=mypos-sync&tab=sync_by_kiotviet" class="nav-tab ' . ($active_tab == "sync_by_kiotviet" ? "nav-tab-active" : "") . '">Đồng bộ hóa thủ công theo KiotViet</a>
         </h2>';
    
    switch ($active_tab) {
        
        case 'sync_by_web':
            
            $show_type = get_option('sync_by_web_show_type');
            
            echo '  <div class="wrap">
                    <form method="post" id="sync-by-web-form" method=”POST”>
                        <label>Bộ lọc </label>
                        <select id="sync_by_web_show_type" name="sync_by_web_show_type">
                            <option value="0"' . ($show_type == 0 ? 'selected' : '') . '>Hiển thị tất cả sản phẩm</option>
                            <option value="1"' . ($show_type == 1 ? 'selected' : '') . '>Chỉ hiển thị sản phẩm không đồng bộ</option>
                            <option value="2"' . ($show_type == 2 ? 'selected' : '') . '>Hiển thị tất cả sản phẩm Pre Order</option>
                        </select>
                        <input type="submit" class="button" value="Áp dụng">
                    </form>
                    </div>';
            
            $myListTable = new KiotViet_ManualSyncWeb_List();
            $myListTable->prepare_items();
            $myListTable->display();
            
            echo '</form>';
            break;
        case 'sync_by_kiotviet':
//            echo '<form method="post"> id="sync-by-kiotviet-form"';
//            $myListTable = new KiotViet_ManualSyncKiotViet_List();
//            $myListTable->prepare_items();
//            $myListTable->display();
//            echo '</form>';
            break;
        default:
            
//            echo '<div class="wrap">
//                    <form>
//                        <label>Bộ lọc </label>
//                        <select id="show_type" name="show_type">
//                            <option value="0">Chỉ hiển thị sản phẩm không đồng bộ</option>
//                            <option value="1">Hiển thị tất cả sản phẩm</option>
//                            <option value="2">Hiển thị tất cả sản phẩm Pre Order</option>
//                        </select>
//                        <input type="submit" class="button" value="Áp dụng">
//                    </form>
//                </div>';
            
            break;
    }
    
    echo '</div>';
}

?>