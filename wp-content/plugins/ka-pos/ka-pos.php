<?php

/**
 * Plugin Name: KA POS Tools
 * Plugin URI: http://vuagym.com
 * Description: Công cụ quản lý KiotViet trên Wordpress
 * Version: 2.0
 * Author: Khoa Anh
 * Author URI: http://vuagym.com
 * License: GPL2
 * Created On: 24-01-2018
 * Updated On: 02-4-2018
 */
// Define WC_PLUGIN_DIR.
if (!defined('WC_PLUGIN_DIR')) {
    define('WC_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Define WC_PLUGIN_FILE.
if (!defined('WC_PLUGIN_URL')) {
    define('WC_PLUGIN_URL', plugin_dir_url(__FILE__));
}

if (!defined('KV_NAME'))                define('KV_NAME', 'Kho Chính');
if (!defined('KV_RETAILER'))            define('KV_RETAILER', 'vuagymtest2');
if (!defined('KV_CLIENT_ID'))           define('KV_CLIENT_ID', '713f68d1-ecbb-4a27-8a2a-342f94bc16c1');
if (!defined('KV_CLIENT_SECRET'))       define('KV_CLIENT_SECRET', '76137D87B075C1417B744E71FA636FF6C91A3E94');

if (!defined('KV2_NAME'))               define('KV2_NAME', 'Kho Phụ');
if (!defined('KV2_PREFIX'))             define('KV2_PREFIX', 'GG');
if (!defined('KV2_RETAILER'))           define('KV2_RETAILER', 'testtuanshop');
if (!defined('KV2_CLIENT_ID'))          define('KV2_CLIENT_ID', 'd9870bd1-8ed5-4c1b-97fc-9b066a0c90ab');
if (!defined('KV2_CLIENT_SECRET'))      define('KV2_CLIENT_SECRET', '8E97AC6E72F218918E3D906874F981B32242ADC3');

if (!defined('MYPOS_PER_PAGE')) define('MYPOS_PER_PAGE', 20);

require_once('autoload.php');

add_action('plugins_loaded', 'kiotviet_tools_plugin_init');

register_activation_hook(__FILE__, 'kiotviet_product_create_db');

$loader = new loader();

function kiotviet_tools_plugin_init() {
    
    add_action('admin_menu', 'kapos_tools_admin_menu');
    add_action('admin_menu', 'kapos_hide_admin_menu');
    
    add_action('login_init', 'send_frame_options_header', 10, 0);
    add_action('admin_init', 'send_frame_options_header', 10, 0);
    
    // Connection String cua kiotviet
    add_option('kiotviet_name', KV_NAME);
    add_option('kiotviet_retailer', KV_RETAILER);
    add_option('kiotviet_client_id', KV_CLIENT_ID);
    add_option('kiotviet_client_secret', KV_CLIENT_SECRET);
    
    // Connection String cua kiotviet Kho phụ
    add_option('kiotviet2_name', KV2_NAME);
    add_option('kiotviet2_prefix', KV2_PREFIX);
    add_option('kiotviet2_retailer', KV2_RETAILER);
    add_option('kiotviet2_client_id', KV2_CLIENT_ID);
    add_option('kiotviet2_client_secret', KV2_CLIENT_SECRET);
    
    // Bat tat tung plugin
    add_option('mypos_add_to_cart', 1);
    add_option('mypos_ajax_cart', 1);
    add_option('mypos_checkout', 1);
    
    // So luong san pham toi da mac dinh khi loi / Pre-order
    add_option('mypos_max_quantity', 3);
    add_option('preorder_max_quantity', 3);
    add_option('mypos_error_max_quantity', 3);
    
    // ID Danh muc sap co hang va hang moi ve
    add_option('mypos_category_sapcohang', 81);
    add_option('mypos_category_hangmoive', 86);
    
    // Trang dong bo san pham: Kieu dong bo va so luong san pham
    add_option('sync_by_web_show_type', 1);
    add_option('sync_by_web_products', MYPOS_PER_PAGE);
    
}

function kapos_tools_admin_menu() {
    add_menu_page('KA POS', 'KA POS', 'edit_posts', 'ka-pos-tools', 'function_kiotviet_tools_page', 'dashicons-admin-multisite', 4);
    add_submenu_page('ka-pos-tools', __('KA POS'), __('KA POS'), 'edit_posts', 'ka-pos-tools');
    add_submenu_page('ka-pos-tools', __('Testing'), __('Testing'), 'manage_options', 'ka-pos-testing', 'function_testing_page');
    add_submenu_page('ka-pos-tools', __('Đồng bộ sản phẩm'), __('Đồng bộ sản phẩm'), 'edit_posts', 'mypos-sync', 'function_mypos_sync_page');
    add_submenu_page('ka-pos-tools', __('Lấy Mã SP KiotViet'), __('Lấy Mã SP KiotViet'), 'edit_posts', 'get-kiotviet-products', 'function_get_sku_kiotviet');
//    add_submenu_page('ka-pos-tools', __('Send SMS'), __('Send SMS'), 'manage_options', 'mypos-single-sms', 'send_single_sms_page');
    add_submenu_page('ka-pos-tools', __('Cài Đặt'), __('Cài Đặt'), 'manage_options', 'ka-pos-options', 'function_mypos_options_page');
}

function kapos_hide_admin_menu() {
    
    if ( kapos_check_permission() ) {
        remove_menu_page( 'ka-pos-tools' );
    }
}

function kapos_check_permission() {
    
    if (current_user_can( 'administrator' )) return false;
    
    $remove = true;
    $roles = get_option('mypos_roles');
    if ($roles) {
        foreach ($roles as $role) {
            if (current_user_can($role)) {
                $remove = false;
                break;
            }
        }
    }
    
    return $remove;
}

function kapos_do_permission() {
    if ( kapos_check_permission() ) {
        exit("Sorry, you aren't allowed to access this page.");
    }
}

function function_kiotviet_tools_page() {
    
    kapos_do_permission();
    load_assets_common_admin();
    
    echo '<div class="wrap">
            <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-plus-circle fa-fw"></i>
                            <strong><font color="blue">KA POS Tools</font></strong>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-success">
                                    <b>KA POS Tools for Woocommerce:</b> Công cụ để quản lý KiotViet từ Wordpress
                            </div>
                        ';
                        
    echo '</div></div></div></div></div>';
}

function function_mypos_options_page() {
    
    kapos_do_permission();
    
    if (isset($_POST['mypos-add-to-cart'])) {
        
        update_option('mypos_add_to_cart', intval($_POST['mypos-add-to-cart']));
        update_option('mypos_ajax_cart', intval($_POST['mypos-ajax-cart']));
        update_option('mypos_checkout', intval($_POST['mypos-checkout']));
        
        update_option('mypos_category_sapcohang', $_POST['mypos-category-sapcohang']);
        update_option('mypos_category_hangmoive', $_POST['mypos-category-hangmoive']);
        
        update_option('mypos_roles', $_POST['mypos-roles']);
        
        update_option('mypos_max_quantity', $_POST['mypos-max-quantity']);
        update_option('preorder_max_quantity', $_POST['preorder-max-quantity']);
        update_option('mypos_error_max_quantity', $_POST['mypos-error-max-quantity']);
        
        update_option('kiotviet_name', $_POST['kiotviet-name']);
        update_option('kiotviet_retailer', $_POST['kiotviet-retailer']);
        update_option('kiotviet_client_id', $_POST['kiotviet-client-id']);
        update_option('kiotviet_client_secret', $_POST['kiotviet-client-secret']);
        
        update_option('kiotviet2_name', $_POST['kiotviet2-name']);
        update_option('kiotviet2_prefix', $_POST['kiotviet2-prefix']);
        update_option('kiotviet2_retailer', $_POST['kiotviet2-retailer']);
        update_option('kiotviet2_client_id', $_POST['kiotviet2-client-id']);
        update_option('kiotviet2_client_secret', $_POST['kiotviet2-client-secret']);
        
        update_option('mypos_tt_han_su_dung', $_POST['mypos-tt-han-su-dung']);
        update_option('mypos_tt_dang_cap_nhat', $_POST['mypos-tt-dang-cap-nhat']);
        update_option('mypos_tt_none', $_POST['mypos-tt-none']);
    }
    
    load_assets_page_options();
    
    echo '<div class="wrap"><div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Cài Đặt KA POS Tools
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" method="POST">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <h3>Bật/Tắt các chức năng</h3>
                                                <div class="form-group">
                                                    <label>Bật/Tắt chức năng: Thêm Sản Phẩm trên trang sản phẩm</label>
                                                    <select class="form-control" id="mypos-add-to-cart" name="mypos-add-to-cart" required>';
                                                    if (get_option('mypos_add_to_cart')) {
                                                        echo '<option value="1" selected>Bật</option>
                                                        <option value="0">Tắt</option>';
                                                    } else {
                                                        echo '<option value="1">Bật</option>
                                                        <option value="0" selected>Tắt</option>';
                                                    }
                                                    echo '</select>
                                                </div>
                                                
                                                <div class="form-group">
                                                <label>Bật/Tắt chức năng: Tự động kiểm tra và cập nhật trên trang giỏ hàng</label>
                                                <select class="form-control" id="mypos-ajax-cart" name="mypos-ajax-cart" required>';
                                                if (get_option('mypos_ajax_cart')) {
                                                    echo '<option value="1" selected>Bật</option>
                                                    <option value="0">Tắt</option>';
                                                } else {
                                                    echo '<option value="1">Bật</option>
                                                    <option value="0" selected>Tắt</option>';
                                                }
                                                echo '</select>
                                                </div>
                                                    
                                                <div class="form-group">
                                                    <label>Bật/Tắt chức năng: Kiểm tra trên trang thanh toán</label>
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
                                            </div>
                                            <h3>Tùy chỉnh số lượng sản phẩm</h3>
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
                                            <h3>Các tùy chỉnh khác</h3>
                                            <div class="form-group">
                                                <label>ID Danh mục: Sắp có hàng</label>
                                                <input class="form-control" type="number" id="mypos-category-sapcohang" name="mypos-category-sapcohang" value="' . get_option('mypos_category_sapcohang') . '" required>
                                            </div>
                                            <div class="form-group">
                                                <label>ID Danh mục: Hàng mới về</label>
                                                <input class="form-control" type="number" id="mypos-category-hangmoive" name="mypos-category-hangmoive" value="' . get_option('mypos_category_hangmoive') . '" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Các User Roles có thể dùng "Đồng bộ hóa sản phẩm"</label>';
    global $wp_roles;
    echo '<select multiple id="mypos-roles" name="mypos-roles[]" class="form-control">';
    
    $mypos_roles = get_option('mypos_roles');
    
    foreach ( $wp_roles->roles as $key=>$value ) {
        if ($key == 'administrator') continue;
        $selected = '';
        foreach ($mypos_roles as $role) {
            if ($role == $key) {
                $selected = 'selected';
                break;
            }
        }
        echo '<option value="' . $key .'" ' . $selected . '>' . $value['name'] . '</option>';
    }
    echo '</select>';   
                                            echo '<p class="help-block">Sử dụng Ctrl để chọn cùng lúc nhiều roles.</p>
                                                </div>    
                                        </div>
                                        <div class="col-lg-6">
                                            <h3>Thuộc tính Hạn sử dụng</h3>
                                                <div class="form-group">
                                                    <label>Vui lòng chọn thuộc tính "Hạn sử dụng"</label>
                                                    <select class="form-control" id="mypos-tt-han-su-dung" name="mypos-tt-han-su-dung" required>';
                                                        echo build_list_attribute_taxonomies(get_option('mypos_tt_han_su_dung'));
                                                    echo '</select>
                                                </div>
                                                
                                                <div class="form-group">
                                                <label>Vui lòng chọn thuộc tính "Đang cập nhật"</label>
                                                <select class="form-control" id="mypos-tt-dang-cap-nhat" name="mypos-tt-dang-cap-nhat" required>';
                                                        echo build_list_attribute_dang_cap_nhat(get_option('mypos_tt_dang_cap_nhat'));
                                                echo '</select>
                                                </div>
                                            <h3>Thiết lập kết nối KiotViet API</h3>
                                            <div class="form-group">
                                                <label>Tên Kho Hàng</label>
                                                <input class="form-control" type="text" id="kiotviet-name" name="kiotviet-name" value="' . get_option('kiotviet_name') . '" required>
                                            </div>
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
                                            <h3>Thiết lập kết nối KiotViet API Kho hàng phụ</h3>
                                            <div class="form-group">
                                                <label>Tên Kho Hàng</label>
                                                <input class="form-control" type="text" id="kiotviet2-name" name="kiotviet2-name" value="' . get_option('kiotviet2_name') . '" required>
                                            </div>
                                            <div class="form-group">
                                                <label>Prefix Kho Phụ</label>
                                                <input class="form-control" type="text" id="kiotviet2-prefix" name="kiotviet2-prefix" value="' . get_option('kiotviet2_prefix') . '" required>
                                            </div>
                                            <div class="form-group">
                                                <label>KiotViet Retailer</label>
                                                <input class="form-control" type="text" id="kiotviet2-retailer" name="kiotviet2-retailer" value="' . get_option('kiotviet2_retailer') . '" required>
                                            </div>
                                            <div class="form-group">
                                                <label>KiotViet Client ID</label>
                                                <input class="form-control" type="text" id="kiotviet2-client-id" name="kiotviet2-client-id" value="' . get_option('kiotviet2_client_id') . '" required>
                                            </div>
                                            <div class="form-group">
                                                <label>KiotViet Client Secret</label>
                                                <input class="form-control" type="text" id="kiotviet2-client-secret" name="kiotviet2-client-secret" value="' . get_option('kiotviet2_client_secret') . '" required>
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
    
    kapos_do_permission();
    set_time_limit(1800);
    
    load_assets_match_sku();
    
    $dbModel = new DbModel();
    $kv_api = new KiotViet_API(1);
    $kv2_api = new KiotViet_API(2);
    
    $remove = true;
    if (current_user_can('administrator')) {
        $remove = false;
    }
    
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
       
        $count = $kv_api->get_all_product_sku();
        $store_name = get_option('kiotviet_name');
        
        if ($count['count_insert']) {
            echo '<div class="alert alert-success">
                            <strong>' . $store_name . ': Đã <b>thêm mới</b> ' . $count['count_insert'] . ' mã sản phẩm.</strong>
                </div>';
        } 
        
        if ($count['count_update']) {
            echo '<div class="alert alert-success">
                            <strong>' . $store_name . ': Đã <b>cập nhật</b> ' . $count['count_update'] . ' mã sản phẩm.</strong>
                </div>';
        }
        
        if (!$count['count_insert'] && !$count['count_update']) {
            echo '<div class="alert alert-success">
                            <strong>' . $store_name . ': Quá trình hoàn tất. Các mã sản phẩm không có cập nhật mới.</strong>
                </div>';
        } 
        
        $count = $kv2_api->get_all_product_sku();
        $store_name = get_option('kiotviet2_name');
        
        if ($count['count_insert']) {
            echo '<div class="alert alert-success">
                            <strong>' . $store_name . ': Đã <b>thêm mới</b> ' . $count['count_insert'] . ' mã sản phẩm.</strong>
                </div>';
        } 
        
        if ($count['count_update']) {
            echo '<div class="alert alert-success">
                            <strong>' . $store_name . ': Đã <b>cập nhật</b> ' . $count['count_update'] . ' mã sản phẩm.</strong>
                </div>';
        }
        
        if (!$count['count_insert'] && !$count['count_update']) {
            echo '<div class="alert alert-success">
                            <strong>' . $store_name . ': Quá trình hoàn tất. Các mã sản phẩm không có cập nhật mới.</strong>
                </div>';
        } 
        
    }
    
    if (isset($_POST['process_deleteAllProducts'])) {
        
        $dbModel = new DbModel();
        
        $result = $dbModel->kiotviet_delete_all_products_sku();
        
        if ($result) {
            echo '<div class="alert alert-success">
                    <strong> Đã xóa tất cả các mã sản phẩm dùng để kết nối với KiotViet.
                    </strong>
            </div>';
        } else {
            echo '<div class="alert alert-danger">
                    <strong> Có lỗi trong quá trình xóa mã sản phẩm. Vui lòng thực hiện lại!
                    </strong>
            </div>';
        }
    }
        
    echo '          <form role="form" method="post" align="center">
                                <input type="hidden" id="process_updateAllProducts" name="process_updateAllProducts">
                                <button type="submit" class="btn btn-success btn-mypos-width">Cập nhật MÃ SẢN PHẨM</button>
                    </form>';
    if (!$remove) {
        echo '      <form role="form" method="post" align="center">
                                <input type="hidden" id="process_deleteAllProducts" name="process_deleteAllProducts">
                                <button type="submit" class="btn btn-danger btn-mypos-width">Xóa hết MÃ SẢN PHẨM</button>
                    </form>';
    }
    
    echo '          <div class="alert alert-warning" style="margin-top: 15px; margin-bottom: 0!important">
                        - "<b>Cập nhật MÃ SẢN PHẨM</b>": Thêm mới và cập nhật khi có mã SP mới hoặc mã SP bị thay đổi trên KiotViet. <br/>';
    if (!$remove) {
        echo '              - "<b>Xóa hết  MÃ SẢN PHẨM</b>": Xóa dữ liệu dùng để kết nối với Kiotviet để làm sạch dữ liệu khi thay đổi kho trên KiotViet. Vui lòng <b>Cập nhật</b> lại dữ liệu sau khi xóa.';
    }
            echo '</div>';
    
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
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Kho hàng</th>
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
            if ($product['product_store'] == 1) {
                $store_name = get_option('kiotviet_name');
            } else {
                $store_name = get_option('kiotviet2_name');
            }
            echo '<td id="product_store_' . $count . '">' . $store_name . '</td>';
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

function function_testing_page() {
    echo "TESTING PAGE";
}

function update_default_manual_sync_options() {
    update_option('sync_by_web_show_type', 1);
    update_option('sync_by_web_products', MYPOS_PER_PAGE);
    update_option('mypos_sync_store', 1);
}

function function_mypos_sync_page() {
    
    kapos_do_permission();
    set_time_limit(600);
    
    load_assets_manual_sync_table();
    
    echo '<div class="wrap">
        <h2>ĐỒNG BỘ HÓA SẢN PHẨM</h2>';
        
    $active_tab = '';
    
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    }
    
    if (isset($_POST['sync_by_web_show_type'])) {
        update_option('sync_by_web_show_type', $_POST['sync_by_web_show_type']);
        update_option('sync_by_web_products', $_POST['sync_by_web_products']);
        update_option('mypos_sync_store', $_POST['mypos_sync_store']);
    }

    if (empty($_POST) && !isset($_GET['paged'])) {
        update_default_manual_sync_options();
    }
    
    $show_type = get_option('sync_by_web_show_type');
    $show_products = get_option('sync_by_web_products');
    
    echo '<h2 class="nav-tab-wrapper">
            <a href="?page=mypos-sync" class="nav-tab ' . ($active_tab == "" ? "nav-tab-active" : "") . '">Welcome</a>
            <a href="?page=mypos-sync&tab=sync_by_web" class="nav-tab ' . ($active_tab == "sync_by_web" ? "nav-tab-active" : "") . '">Đồng bộ hóa thủ công theo Web</a>
            <a href="?page=mypos-sync&tab=sync_by_kiotviet" class="nav-tab ' . ($active_tab == "sync_by_kiotviet" ? "nav-tab-active" : "") . '">Đồng bộ hóa thủ công theo KiotViet</a>
         </h2>';
    
    switch ($active_tab) {
        
        case 'sync_by_web': // Dong bo thu cong theo Web
            
            echo '  <div class="wrap">
                    <form id="sync-by-web-form" method="POST">
                            <label>Bộ lọc </label>
                            <select id="sync_by_web_show_type" name="sync_by_web_show_type">
                                <option value="1"' . ($show_type == 1 ? 'selected' : '') . '>Chỉ hiển thị sản phẩm không đồng bộ</option>
                                <option value="0"' . ($show_type == 0 ? 'selected' : '') . '>Hiển thị tất cả sản phẩm</option>
                                <option value="2"' . ($show_type == 2 ? 'selected' : '') . '>Hiển thị tất cả sản phẩm Pre Order</option>
                            </select>
                            <label id="sync_by_web_products_label"> Số lượng SP </label>
                            <input type="number" id="sync_by_web_products" name="sync_by_web_products" value="' . $show_products . '" min="1" required>
                        <input type="submit" class="button" value="Áp dụng">
                    </form>
                    </div>';
            
            if (empty($_POST) && !isset($_GET['paged'])) {
                
            } else {
                echo '<form method="POST" id="sync-by-web-list">';
                $myListTable = new KiotViet_ManualSyncWeb_List($show_type, $show_products);
                $myListTable->prepare_items();
                $myListTable->display();
                echo '</form>';
            }
            break;
            
        case 'sync_by_kiotviet':
            
            $store_id = get_option('mypos_sync_store');
            
            echo '  <div class="wrap">
                    <form id="sync-by-web-form" method="POST">
                            <label>Kho hàng </label>
                            <select id="mypos_sync_store" name="mypos_sync_store">
                                <option value="1"' . ($store_id == 1 ? 'selected' : '') . '>' . get_option('kiotviet_name') . '</option>
                                <option value="2"' . ($store_id == 2 ? 'selected' : '') . '>' . get_option('kiotviet2_name') . '</option>
                            </select>
                            <label>Bộ lọc </label>
                            <select id="sync_by_web_show_type" name="sync_by_web_show_type">
                                <option value="1"' . ($show_type == 1 ? 'selected' : '') . '>Chỉ hiển thị sản phẩm không đồng bộ</option>
                                <option value="0"' . ($show_type == 0 ? 'selected' : '') . '>Hiển thị tất cả sản phẩm</option>
                            </select>
                            <label id="sync_by_web_products_label"> Số lượng SP </label>
                            <input type="number" id="sync_by_web_products" name="sync_by_web_products" value="' . $show_products . '" min="1" required>
                        <input type="submit" class="button" value="Áp dụng">
                    </form>
                    </div>';
            
            if (empty($_POST) && !isset($_GET['paged'])) {
                
            } else {
                echo '<form method="POST" id="sync-by-kv-list">';
                $myListTable = new KiotViet_ManualSyncKiotViet_List($show_type, $show_products, $store_id);
                $myListTable->prepare_items();
                $myListTable->display();
                echo '</form>';
            }
            break;
            
        default:
            echo '<div class="wrap">
                    <span>Vui lòng chọn các TAB chức năng.</span>
                  </div>';
                    
            update_default_manual_sync_options();
            break;
        
    }
    
    echo '</div>';
    
}

?>