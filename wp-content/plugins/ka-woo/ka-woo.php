<?php

/**
 * Plugin Name: KA Woo Tools
 * Plugin URI: http://vuagym.com
 * Description: Công cụ tùy chỉnh WooCommerce
 * Version: 1.0
 * Author: Khoa Anh
 * Author URI: http://vuagym.com
 * License: GPL2
 * Created On: 22-03-2018
 * Updated On: 22-03-2018
 */
// Define KAWOO_PLUGIN_DIR.
if (!defined('KAWOO_PLUGIN_DIR')) {
    define('KAWOO_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// Define KAWOO_PLUGIN_URL.
if (!defined('KAWOO_PLUGIN_URL')) {
    define('KAWOO_PLUGIN_URL', plugin_dir_url(__FILE__));
}

require_once('autoload.php');

add_action('plugins_loaded', 'ka_woo_tools_plugin_init');

function ka_woo_tools_plugin_init() {
    add_action('admin_menu', 'ka_woo_tools_admin_menu');
    add_action('admin_menu', 'ka_woo_hide_admin_menu');
    add_action('login_init', 'send_frame_options_header', 10, 0);
    add_action('admin_init', 'send_frame_options_header', 10, 0);
}

function ka_woo_tools_admin_menu() {
    add_menu_page('KA WOO', 'KA WOO', 'edit_posts', 'ka-woo-tools', 'function_ka_woo_tools_page', 'dashicons-admin-multisite', 4);
//    add_submenu_page('ka-woo-tools', __('KA WOO'), __('KA WOO'), 'edit_posts', 'ka-woo-options');
    add_submenu_page('ka-woo-tools', __('Manager Tabs'), __('Manager Tabs'), 'edit_posts', 'kawoo-manager-tabs', 'function_manager_tabs_page');
    add_submenu_page('ka-woo-tools', __('Testing'), __('Testing'), 'manage_options', 'ka-woo-testing', 'function_kawoo_testing_page');
    add_submenu_page('ka-woo-tools', __('Cài Đặt'), __('Cài Đặt'), 'manage_options', 'ka-woo-options', 'function_kawoo_options_page');
}

function ka_woo_hide_admin_menu() {

    if (kawoo_check_permission()) {
        remove_menu_page('ka-woo-tools');
    }
}

function kawoo_check_permission() {

    if (current_user_can('administrator'))
        return false;

    $remove = true;
    $roles = get_option('kawoo_roles');
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

function kawoo_do_permission() {
    if (kawoo_check_permission()) {
        exit("Sorry, you aren't allowed to access this page.");
    }
}

function function_ka_woo_tools_page() {
    kawoo_do_permission();
    echo '<div class="wrap">
            <b>KA WOO Tools for Woocommerce:</b> Công cụ để tùy chỉnh các chức năng của WooCommerce
            </div>';
}

function kawoo_update_default_manager_tabs_options() {
    update_option('kawoo_show_type', 1);
    update_option('kawoo_number_of_products', MYPOS_PER_PAGE);
    update_option('kawoo_image_link', '');
    update_option('kawoo_selected_text', '');
}

function function_manager_tabs_page() {

    kawoo_do_permission();
    set_time_limit(600);

    echo '<div class="wrap">
        <h2>Quản lý Woocommerce</h2>';

    $active_tab = '';

    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    }

    if (isset($_POST['kawoo_number_of_products'])) {

        update_option('kawoo_show_type', $_POST['kawoo_show_type']);
        update_option('kawoo_number_of_products', $_POST['kawoo_number_of_products']);
        update_option('kawoo_image_link', $_POST['kawoo_image_link']);

        update_option('kawoo_selected_categories', isset($_POST['kawoo_selected_categories']) ? $_POST['kawoo_selected_categories'] : '');
        update_option('kawoo_selected_text', isset($_POST['kawoo_finding_code_text']) ? $_POST['kawoo_finding_code_text'] : '');
    }

    if (empty($_POST) && !isset($_GET['paged'])) {
        kawoo_update_default_manager_tabs_options();
    }

    echo '<h2 class="nav-tab-wrapper">
            <a href="?page=kawoo-manager-tabs" class="nav-tab ' . ($active_tab == "" ? "nav-tab-active" : "") . '">Welcome</a>
            <a href="?page=kawoo-manager-tabs&tab=product_picture_manager" class="nav-tab ' . ($active_tab == "product_picture_manager" ? "nav-tab-active" : "") . '">Quản lý ảnh sản phẩm</a>
            <a href="?page=kawoo-manager-tabs&tab=product_category_manager" class="nav-tab ' . ($active_tab == "product_category_manager" ? "nav-tab-active" : "") . '">Quản lý danh mục sản phẩm</a>
            <a href="?page=kawoo-manager-tabs&tab=product_price_manager" class="nav-tab ' . ($active_tab == "product_price_manager" ? "nav-tab-active" : "") . '">Quản lý giá sản phẩm</a>
            <a href="?page=kawoo-manager-tabs&tab=product_search_manager" class="nav-tab ' . ($active_tab == "product_search_manager" ? "nav-tab-active" : "") . '">Quản lý sản phẩm</a>
            <a href="?page=kawoo-manager-tabs&tab=product_content_manager" class="nav-tab ' . ($active_tab == "product_content_manager" ? "nav-tab-active" : "") . '">Quản lý nội dung</a>
         </h2>';

    $show_type = get_option('kawoo_show_type');
    $show_products = get_option('kawoo_number_of_products');

    switch ($active_tab) {

        case 'product_picture_manager': // Hien thi cac san pham khong co anh

            kawoo_load_assets_tab_picture();

            $image_link = get_option('kawoo_image_link');

            echo '  <div class="wrap">
                    <form id="product-image-manager-form" method="POST">
                            <label>Bộ lọc &nbsp</label>
                            <select id="kawoo_show_type" name="kawoo_show_type">
                                <option value="1"' . ($show_type == 1 ? 'selected' : '') . '>Chưa có "Ảnh sản phẩm"</option>
                                <option value="2"' . ($show_type == 2 ? 'selected' : '') . '>Dùng link ảnh ...</option>
                            </select>
                            <label id="kawoo_product_numbers_label">Số lượng SP hiển thị &nbsp</label>
                            <input type="number" id="kawoo_number_of_products" name="kawoo_number_of_products" value="' . $show_products . '" min="1" required>
                            <label id="image_link_label">Link Ảnh &nbsp</label>
                            <input type="text" id="kawoo_image_link" name="kawoo_image_link" value="' . $image_link . '" required>
                        <input type="submit" class="button" value="Áp dụng">
                    </form>
                    </div>';

            if (empty($_POST) && !isset($_GET['paged'])) {
                
            } else {
                echo '<form method="POST" id="product-image-manager-list">';
                $myListTable = new Kawoo_Product_Image_List($show_type, $show_products, $image_link);
                $myListTable->prepare_items();
                $myListTable->display();
                echo '</form>';
            }
            break;

        case 'product_category_manager':    // Tab quan ly danh muc
            kawoo_load_assets_tab_category();

            $selected_categories = get_option('kawoo_selected_categories');

            $args = array(
                'hide_empty' => 0,
                'taxonomy' => 'product_cat',
                'hierarchical' => 1,
                'echo' => 0,
                'name' => 'kawoo_selected_categories[]',
                'id' => 'kawoo_selected_categories'
            );

            $cats = wp_dropdown_categories($args);

            echo '  <div class="wrap">
                    <form id="product-image-manager-form" method="POST">
                            <label>Bộ lọc &nbsp</label>
                            <select id="kawoo_show_type" name="kawoo_show_type" style="width: 19%">
                                <option value="1"' . ($show_type == 1 ? 'selected' : '') . '>Sản phẩm chỉ thuộc danh mục</option>
                                <option value="2"' . ($show_type == 2 ? 'selected' : '') . '>Sản phẩm thuộc danh mục</option>
                                <option value="3"' . ($show_type == 3 ? 'selected' : '') . '>Sản phẩm thuộc danh mục con mà không thuộc danh mục cha</option>
                            </select>
                            <label id="kawoo_product_numbers_label">Số lượng SP hiển thị &nbsp</label>
                            <input type="number" id="kawoo_number_of_products" name="kawoo_number_of_products" value="' . $show_products . '" min="1" required>
                            <label id="kawoo_selected_categories_label">Danh mục &nbsp</label>
                            ' . $cats . '
                        <input type="submit" class="button" value="Áp dụng">
                    </form>
                    </div>';

            if (empty($_POST) && !isset($_GET['paged'])) {
                
            } else {
                echo '<form method="POST" id="product-category-manager-list">';
                $myListTable = new Kawoo_Product_Category_List($show_type, $show_products, $selected_categories);
                $myListTable->prepare_items();
                $myListTable->display();
                echo '</form>';
            }
            break;
        case 'product_price_manager':
            kawoo_load_assets_tab_price();
            echo '  <div class="wrap">
                    <form id="product-image-manager-form" method="POST">
                            <label>Bộ lọc &nbsp</label>
                            <select id="kawoo_show_type" name="kawoo_show_type">
                                <option value="1"' . ($show_type == 1 ? 'selected' : '') . '>Hiện sản phẩm chưa có giá sale</option>
                            </select>
                            <label id="kawoo_product_numbers_label">Số lượng SP hiển thị &nbsp</label>
                            <input type="number" id="kawoo_number_of_products" name="kawoo_number_of_products" value="' . $show_products . '" min="1" required>
                        <input type="submit" class="button" value="Áp dụng">
                    </form>
                    </div>';

            if (empty($_POST) && !isset($_GET['paged'])) {
                
            } else {
                echo '<form method="POST" id="product-price-manager-list">';
                $myListTable = new Kawoo_Product_Price_List($show_type, $show_products, $selected_categories);
                $myListTable->prepare_items();
                $myListTable->display();
                echo '</form>';
            }
            break;
        case 'product_search_manager': // Tim kiem va hien cac san pham thuoc kho phu

            kawoo_load_assets_tab_search();

            $finding_product_code = get_option('kawoo_selected_text');

            echo '  <div class="wrap">
                    <form id="product-image-manager-form" method="POST">
                            <label>Bộ lọc &nbsp</label>
                            <select id="kawoo_show_type" name="kawoo_show_type">
                                <option value="1"' . ($show_type == 1 ? 'selected' : '') . '>Tìm kiếm theo Mã sản phẩm</option>
                                <option value="2"' . ($show_type == 2 ? 'selected' : '') . '>Hiện tất cả sản phẩm kho phụ</option>
                            </select>
                            <label id="kawoo_product_numbers_label">Số lượng SP hiển thị &nbsp</label>
                            <input type="number" id="kawoo_number_of_products" name="kawoo_number_of_products" value="' . $show_products . '" min="1" required>
                            <label id="kawoo_finding_code_label">Mã sản phẩm &nbsp</label>
                            <input type="text" id="kawoo_finding_code_text" name="kawoo_finding_code_text" value="' . $finding_product_code . '" required>
                        <input type="submit" class="button" value="Áp dụng">
                    </form>
                    </div>';

            if (empty($_POST) && !isset($_GET['paged'])) {
                
            } else {
                echo '<form method="POST" id="product-search-manager-list">';
                $myListTable = new Kawoo_Product_Search_List($show_type, $show_products, $finding_product_code);
                $myListTable->prepare_items();
                $myListTable->display();
                echo '</form>';
            }
            break;
        case 'product_content_manager': // Tim kiem va hien cac san pham thuoc kho phu

            kawoo_load_assets_tab_content();

            $selected_categories = get_option('kawoo_selected_categories');
            $finding_product_code = get_option('kawoo_selected_text');

            $args = array(
                'hide_empty' => 0,
                'taxonomy' => 'product_cat',
                'hierarchical' => 1,
                'echo' => 0,
                'name' => 'kawoo_selected_categories[]',
                'id' => 'kawoo_selected_categories',
                'show_option_all' => 'Tất cả danh mục',
            );

            $cats = wp_dropdown_categories($args);

            $tab_args = array(
                'post_type' => 'ywtm_tab',
                'post_status' => 'publish',
                'posts_per_page' => -1
            );

            $tab_list = get_posts($tab_args);

            $tab_list_view = '';
            foreach ($tab_list as $tab) {
                $tab_list_view .= '<option value="' . $tab->ID . '"' . ($show_type == $tab->ID ? 'selected' : '') . '>' . $tab->post_title . '</option>';
            }

            echo '  <div class="wrap">
                    <form id="product-content-manager-form" method="POST">
                    <div class="tablenav top">
                            <label id="kawoo_selected_categories_label">SP thuộc danh mục &nbsp</label>
                            ' . $cats . '
                            <label> &nbsp mà &nbsp</label>
                            <select id="kawoo_show_type" name="kawoo_show_type">
                                <option value="1"' . ($show_type == 1 ? 'selected' : '') . '>Sản phẩm có mô tả ngắn</option>
                                <option value="2"' . ($show_type == 2 ? 'selected' : '') . '>Sản phẩm có mô tả</option>
                                    ' . $tab_list_view . '
                            </select>
                            <label id="kawoo_finding_code_label">&nbsp có nội dung &nbsp</label>
                            <input style="width: 260px" type="text" id="kawoo_finding_code_text" name="kawoo_finding_code_text" value="' . $finding_product_code . '" placeholder="Bỏ trống nếu rỗng hoặc ít hơn 3 ký tự">
                        <div class="tablenav top">
                            <label id="kawoo_product_numbers_label">Số lượng SP hiển thị &nbsp</label>
                            <input type="number" id="kawoo_number_of_products" name="kawoo_number_of_products" value="' . $show_products . '" min="1" required>
                            <input type="submit" class="button" value="Áp dụng">
                        </div>
                     </div>
                    </form>
                    </div>';

            if (empty($_POST) && !isset($_GET['paged'])) {
                
            } else {
                echo '<form method="POST" id="product-content-manager-list">';
                $myListTable = new Kawoo_Product_Content_List($show_type, $show_products, $selected_categories, $finding_product_code);
                $myListTable->prepare_items();
                $myListTable->display();
                echo '</form>';
            }
            break;
        default:
            echo '<div class="wrap">
                    <span>Vui lòng chọn các TAB chức năng.</span>
                  </div>';

            kawoo_update_default_manager_tabs_options();
            break;
    }

    echo '</div>';
}

function function_kawoo_options_page() {

    if (isset($_POST['kawoo-roles'])) {
        update_option('kawoo_roles', $_POST['kawoo-roles']);
    }

    kawoo_load_assets_common_admin();

    echo '<div class="wrap"><div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Cài Đặt KA WOO Tools
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" method="POST">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label>Các User Roles có thể dùng "KA WOO Tools"</label>';
    global $wp_roles;
    echo '<select multiple id="kawoo-roles" name="kawoo-roles[]" class="form-control">';

    $mypos_roles = get_option('kawoo_roles');

    foreach ($wp_roles->roles as $key => $value) {
        if ($key == 'administrator')
            continue;
        $selected = '';
        foreach ($mypos_roles as $role) {
            if ($role == $key) {
                $selected = 'selected';
                break;
            }
        }
        echo '<option value="' . $key . '" ' . $selected . '>' . $value['name'] . '</option>';
    }
    echo '</select>';
    echo '<p class="help-block">Sử dụng Ctrl để chọn cùng lúc nhiều roles.</p>
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

function function_kawoo_testing_page() {

//    $content = new Kawoo_Product_Content_List();
//    $product = wc_get_product(6553);
//    $product2 = wc_get_product(8448);
//    
//    echo "<pre>";
//    print_r($product);
//    print_r($product2);
//    echo "</pre>";

    $outofstock_term = get_term_by('name', 'outofstock', 'product_visibility');
    echo "<pre>";
    print_r($outofstock_term);
    echo "</pre>";
}

add_action('pre_get_posts', 'kawoo_hide_out_of_stock_products');

function kawoo_hide_out_of_stock_products($q) {

    if (!$q->is_main_query() || is_admin()) {
        return;
    }
    
    $tax_query = (array) $q->get('tax_query');
    foreach ($tax_query as $key => $tax_q) {
        if (isset($tax_q['taxonomy']) && ($tax_q['taxonomy'] == 'product_visibility') ) {
            unset($tax_query[$key]);
        }
    }
    
    if (count($tax_query) == 1 && isset($tax_query['relation'])) {
        $tax_query = [];
    }
    
    $q->set('tax_query', $tax_query);

    $meta_query = (array) $q->get('meta_query');

    $meta_query[] = array(
        'relation' => 'OR',
        array(
            'key' => '_stock_status',
            'value' => 'outofstock',
            'compare' => 'NOT IN'
        ),
        array(
            'key' => '_mypos_show_always',
            'value' => 'yes',
            'compare' => '='
    ));

    $q->set('meta_query', $meta_query);
    
    remove_action('pre_get_posts', 'kawoo_hide_out_of_stock_products');
}

?>