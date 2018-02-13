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
    define('KV_RETAILER', 'vuagymtest');
}

if (!defined('KV_CLIENT_ID')) {
    define('KV_CLIENT_ID', '619e8b7f-3b68-4635-8760-bdb90c1d8a66');
}

if (!defined('KV_CLIENT_SECRET')) {
    define('KV_CLIENT_SECRET', 'D0AFE74F413FB339B8F8F81C71AEE7B460E20A0F');
}

require_once('autoload.php');
require_once('includes/add_to_cart.php');

add_action('plugins_loaded', 'kiotviet_tools_plugin_init');

register_activation_hook(__FILE__, 'kiotviet_product_create_db');

function kiotviet_tools_plugin_init() {
    add_action('admin_menu', 'kiotviet_tools_admin_menu');
    add_action('login_init', 'send_frame_options_header', 10, 0);
    add_action('admin_init', 'send_frame_options_header', 10, 0);
    
    add_option('mypos_enabled', 1);
    add_option('mypos_max_quantity', 250);
    add_option('preorder_max_quantity', 150);
    
    add_option('kiotviet_retailer', KV_RETAILER);
    add_option('kiotviet_client_id', KV_CLIENT_ID);
    add_option('kiotviet_client_secret', KV_CLIENT_SECRET);
}

function kiotviet_tools_admin_menu() {
    //Maketing Tools
    add_menu_page('KiotViet Tools', 'KiotViet Tools', 'manage_options', 'kiotviet-tools', 'function_kiotviet_tools_page', 'dashicons-admin-multisite', 4);
    add_submenu_page('kiotviet-tools', __('KiotViet'), __('KiotViet'), 'manage_options', 'kiotviet-tools');
    add_submenu_page('kiotviet-tools', __('Testing'), __('Testing'), 'manage_options', 'kiotviet-testing', 'function_testing_page');
    add_submenu_page('kiotviet-tools', __('Kết Nối Mã Sản Phẩm KiotViet'), __('Kết Nối Mã SP KiotViet'), 'manage_options', 'get-kiotviet-products', 'function_match_sku');
    add_submenu_page('kiotviet-tools', __('Sync KiotViet'), __('Sync KiotViet'), 'manage_options', 'kiotviet-sync', 'function_compare_manual');
    add_submenu_page('kiotviet-tools', __('Cài Đặt'), __('Cài Đặt'), 'manage_options', 'kiotviet-options', 'function_mypos_options_page');
}

function function_kiotviet_tools_page() {
    $product_id = '2063479';
    
    $api = new KiotViet_API();
    
    $result = $api->get_product_quantity_byKiotvietProductID($product_id);
    
    echo $result;
    exit;
    
}

function function_testing_page() {
    $api = new KiotViet_API();
    
    $url = 'https://public.kiotapi.com/products/1234';
    $test = $api->api_call($url);
    
    echo '<pre>';
    echo print_r($test);
    echo '<pre>';
    exit;
}

function function_mypos_options_page() {
    
    if (!empty($_POST['mypos-enabled'])) {
        update_option('mypos_enabled', $_POST['mypos-enabled']);
        update_option('mypos_max_quantity', $_POST['mypos-max-quantity']);
        update_option('preorder_max_quantity', $_POST['preorder-max-quantity']);
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
                                                <p class="help-block">Bật tắt các chức năng: add-to-cart/cart-modify/checkout</p>
                                                <select class="form-control" id="mypos-enabled" name="mypos-enabled" required>';
                                                if (get_option('mypos_enabled')) {
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
    echo '';
}

function function_match_sku() {
    
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
                        
    if (isset($_POST['process_updateAllProducts'])) {
        
        $count = $api->get_count_all_products();
        
        echo '<div class="alert alert-success">
                        <strong> Đã lấy mới ' . $count . ' Mã sản phẩm.
                        </strong>
            </div>';
        
        }
        
    echo '          <form role="form" method="post" align="center">
                                <input type="hidden" id="process_updateAllProducts" name="process_updateAllProducts">
                                <button type="submit" class="btn btn-success">Kết Nối Mã Sản Phẩm</button>
                    </form>
                    <div class="alert alert-warning" style="margin-top: 15px; margin-bottom: 0!important">
                        Chú ý: Chỉ cần "Kết Nối Mã Sản Phẩm" khi có Mã Sản Phẩm MỚI trên KiotViet.
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

function function_compare_manual() {
    
    load_assets_compare_manual();
    
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
//        $count = $api->get_count_all_products();
//        
//        echo '<div class="alert alert-success">
//                        <strong> Đã lấy mới ' . $count . ' Mã sản phẩm.
//                        </strong>
//            </div>';
//        
//        }
        
    echo '          <form role="form" method="post" align="center">
                                <input type="hidden" id="process_updateAllProducts" name="process_updateAllProducts">
                                <button type="submit" class="btn btn-success">Kết Nối Mã Sản Phẩm</button>
                    </form>
                    <div class="alert alert-warning" style="margin-top: 15px; margin-bottom: 0!important">
                        Chú ý: Chỉ cần "Kết Nối Mã Sản Phẩm" khi có Mã Sản Phẩm MỚI trên KiotViet.
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
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Cửa hàng (KiotViet)</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Web (Wordpress)</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Tùy Chọn</th>
                                </tr>
                             </thead>
                                <tbody>';
    
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
    
//    echo '<pre>';
//    print_r($matched_products);
//    echo '</pre>';
//    exit;
    
    
    foreach($matched_products as $product) {
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['kv']['stock']) {
                $kv_stock_status = "Còn hàng";
            } else {
                $kv_stock_status = "Hết hàng";
            }
            
            echo "<td>{$product['kv']['name']}-Mã:<b>{$product['kv']['sku']}</b>-TT:{$kv_stock_status}-SL:{$product['kv']['quantity']}-Giá:{$product['kv']['price']}</td>";
            
            if ($product['woo']['stock']) {
                $woo_stock_status = "Còn hàng";
            } else {
                $woo_stock_status = "Hết hàng";
            }
                
            echo "<td>{$product['woo']['name']}-Mã:<b>{$product['woo']['sku']}</b>-TT:{$woo_stock_status}-SL:{$product['woo']['quantity']}-Giá:{$product['woo']['price']}</td>";
            echo '<td>';
            echo '  <button id="updateInStock_' . $product['woo']['id'] . '" type="button" class="btn btn-success" title="Cập nhật có hàng trên Web cho sản phẩm này" onclick="updateInStock('. $product['woo']['id'] .');"><i class="fa fa-tasks"></i>  Cập nhật có hàng</button>';
            echo '</td>';
            echo '</tr>';
    }
    
    foreach($kiotviet_all_products as $product) {
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['stock']) {
                $kv_stock_status = "Còn hàng";
            } else {
                $kv_stock_status = "Hết hàng";
            }
            
            echo "<td>{$product['name']}-Mã:<b>{$product['sku']}</b>-TT:{$kv_stock_status}-SL:{$product['quantity']}-Giá:{$product['price']}</td>";
            echo "<td>Không có sản phẩm</td>";
            if (empty($product['sku'])) {
                echo '<td>Không có Mã SP</td>';
            } else {
                echo '<td>None</td>';
            }
            echo '</tr>';
    }
    
    foreach($woo_all_products as $product) {
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            echo "<td>Không có sản phẩm</td>";
            
            if ($product['stock']) {
                $woo_stock_status = "Còn hàng";
            } else {
                $woo_stock_status = "Hết hàng";
            }

            echo "<td>{$product['name']}-Mã:<b>{$product['sku']}</b>-TT:{$woo_stock_status}-SL:{$product['quantity']}-Giá:{$product['price']}</td>";
            if (empty($product['sku'])) {
                echo '<td>Không có Mã SP</td>';
            } else {
                echo '<td>None</td>';
            }
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


function get_woocommerce_product_list() {
	$full_product_list = array();
	$loop = new WP_Query( array( 'post_type' => array('product', 'product_variation'), 'posts_per_page' => -1 ) );
 
	while ( $loop->have_posts() ) : $loop->the_post();
                
                $new_product = array();
		$theid = get_the_ID();
                
//		$product = new WC_Product($theid);
                $product = wc_get_product($theid);

                // its a variable product
		if( get_post_type() == 'product_variation' ){
//			$parent_id = wp_get_post_parent_id($theid );
//			$product_sku = get_post_meta($theid, '_sku', true );
//			$product_name = get_the_title( $parent_id);
                        $new_product['id'] = $theid;
                        $new_product['sku'] = $product->get_sku();
                        $new_product['title'] = $product->get_title();
                        $new_product['name'] = $product->get_name();
                        $new_product['price'] = $product->get_price();
                        $new_product['quantity'] = ($product->get_stock_quantity() == 'instock') ? true : false;
                        $new_product['stock'] = $product->get_stock_status();
                        
                // its a simple product
                } else {
                    //Product is a main of variations
                    if ($product->has_child()) {
                        
                    } else {
                        $new_product['id'] = $theid;
                        $new_product['sku'] = $product->get_sku();
                        $new_product['title'] = $product->get_title();
                        $new_product['name'] = $product->get_name();
                        $new_product['price'] = $product->get_price();
                        $new_product['quantity'] = ($product->get_stock_quantity() == 'instock') ? true : false;
                        $new_product['stock'] = $product->get_stock_status();
                    }
                    
                }
                
        // add product to array but don't add the parent of product variations
        if (!empty($new_product)) {
            $full_product_list[] = $new_product;
        }
        
    endwhile; wp_reset_query();
    // sort into alphabetical order, by title
//    sort($full_product_list);
    return $full_product_list;
}

?>