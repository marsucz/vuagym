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

// Define WC_PLUGIN_FILE.
if (!defined('MAX_QUANTITY')) {
    define('MAX_QUANTITY', 250);
}

// Define WC_PLUGIN_FILE.
if (!defined('CUSTOM_QUANTITY_PREORDER')) {
    define('CUSTOM_QUANTITY_PREORDER', 150);
}

require_once('autoload.php');
require_once('includes/add_to_cart.php');

add_action('plugins_loaded', 'kiotviet_tools_plugin_init');

register_activation_hook(__FILE__, 'kiotviet_product_create_db');

function kiotviet_tools_plugin_init() {
    add_action('admin_menu', 'kiotviet_tools_admin_menu');
    add_action('login_init', 'send_frame_options_header', 10, 0);
    add_action('admin_init', 'send_frame_options_header', 10, 0);
}

function kiotviet_tools_admin_menu() {
    //Maketing Tools
    add_menu_page('KiotViet Tools', 'KiotViet Tools', 'manage_options', 'kiotviet-tools', 'function_kiotviet_tools_page', 'dashicons-admin-multisite', 4);
    add_submenu_page('kiotviet-tools', __('KiotViet'), __('KiotViet'), 'manage_options', 'kiotviet-tools');
    add_submenu_page('kiotviet-tools', __('Testing'), __('Testing'), 'manage_options', 'kiotviet-testing', 'function_testing_page');
    add_submenu_page('kiotviet-tools', __('Get All Kiot Products'), __('Get All Kiot Products'), 'manage_options', 'get-kiotviet-products', 'function_get_kiotviet_products');
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


function function_get_kiotviet_products() {
    
    load_assets_get_kiotviet_products();
    
    $dbModel = new DbModel();
    $api = new KiotViet_API();
    
    echo '<div class="wrap">';
    echo '<div class="row">
                <div class="col-lg-3">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-plus-circle fa-fw"></i>
                            <strong><font color="blue">Kiot Viet Products Updater</font></strong>
                        </div>
                        <div class="panel-body">';
                        
    if (isset($_POST['process_updateAllProducts'])) {
        
        $count = $api->get_all_products();
        
        echo '<div class="alert alert-success">
                        <strong> Updated ' . $count . ' products.
                        </strong>
            </div>';
        
        }
        
    echo '          <form role="form" method="post">
                                <input type="hidden" id="process_updateAllProducts" name="process_updateAllProducts">
                                <button type="submit" class="btn btn-success" align="center">Add/Update Now</button>
                    </form>';
    
    echo '</div></div></div></div>';
    
    
        echo '<div class="row"> 
            <div class="col-lg-12">';
        echo '<div class="panel panel-default">
                        <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i>
                            Products List
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
                                   <th class="sorting_desc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;" aria-sort="descending" >No</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Product ID</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Product Code</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Updated</th>
                                </tr>
                             </thead>
                                <tbody>';
    
    $count = 0;
    $all_products = $dbModel->kiotviet_get_all_products();
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

?>