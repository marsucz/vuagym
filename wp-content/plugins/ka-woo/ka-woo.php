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
    
    set_time_limit(1200);
    kawoo_do_permission();

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
                                <option value="3"' . ($show_type == 3 ? 'selected' : '') . '>Lọc các sản phẩm luôn hiện</option>
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

    set_time_limit(1200);
    
    if (isset($_POST['kawoo-roles'])) {
        update_option('kawoo_roles', $_POST['kawoo-roles']);
    }
    
    $message = '';
    
//    if (isset($_POST)) {
//        
//        echo "<pre>";
//        print_r($_POST);
//        echo "</pre>";
//        
//    }
    
    if (isset($_POST['action_type'])) {
        if ($_POST['action_type'] == 'hide_outofstock_products') {
            
            $dbModel = new WooDbModel();
            
            $perPage = 40;
            $currentPage = 0;
            
            $list_hidden = array();
            $list_show = array();
            $list_show_always = array();
            
            $term_only_search = 'exclude-from-catalog';
            $term_visible = array();
            
            while (1) {
                
                $currentPage++;
                $list_product = $dbModel->kawoo_get_all_products_to_sethidden($perPage, $currentPage);

                if (count($list_product) == 0) {
                    break;
                }
                
                foreach ($list_product as $prod) {
                    
                    $product_id = $prod['ID'];
                    if ($prod['show_status'] == 'yes') {
                        wp_set_object_terms($product_id, $term_visible, 'product_visibility');
                        $list_show_always[] = $product_id;
                        continue;
                    }
                    
                    $product = wc_get_product($product_id);
                    if ($product && $product->is_type('variable')) {
                        $check_in_stock = $dbModel->check_stock_by_parent_id($product_id);
                        if ($check_in_stock) {
                            wp_set_object_terms($product_id, $term_visible, 'product_visibility');
                            $list_show[] = $product_id;
                        } else {
                            wp_set_object_terms( $product_id, $term_only_search, 'product_visibility' );
                            $list_hidden[] = $product_id;
                        }
                    } else {
                        if ($prod['stock_status'] == 'outofstock') {
                            wp_set_object_terms( $product_id, $term_only_search, 'product_visibility' );
                            $list_hidden[] = $product_id;
                        } else {
                            wp_set_object_terms( $product_id, $term_visible, 'product_visibility' );
                            $list_show[] = $product_id;
                        }
                    }
                }
            }
            
            $message = "Đã ẩn (" . count($list_hidden) . " sản phẩm): " . implode(', ', $list_hidden);
            $message .= "<br/>Đã hiện (" . count($list_show) . " sản phẩm): ". implode(', ', $list_show);
            $message .= "<br/>Luôn hiện (" . count($list_show_always) . " sản phẩm): ". implode(', ', $list_show_always);
        }
        
        
        if ($_POST['action_type'] == 'update_custom_price_field') {
            
            $dbModel = new WooDbModel();
            
            $perPage = 40;
            $currentPage = 0;
            
            $max_int = 999999999;
            $min_int = -999999999;
                    
            $list_processed = array();
            
            while (1) {
                
                $currentPage++;
                $list_product = $dbModel->kawoo_get_all_products($perPage, $currentPage);

                if (count($list_product) == 0) {
                    break;
                }
                
                foreach ($list_product as $prod) {
                    $product_id = $prod['ID'];
                    $product = wc_get_product($product_id);
                    if ($product && $product->is_type('variable')) {
    
                        $childrens = $dbModel->get_children_ids($product_id);

                        $price_min = $max_int;
                        $price_max = $min_int;

                        $id_min = 0;
                        $id_max = 0;

                        foreach ($childrens as $child) {
                            $child_id = $child['ID'];
                            $child_prod = wc_get_product($child_id);
                            $temp_price = $child_prod->get_price();
                            if ($temp_price) {
                                if ($temp_price < $price_min) {
                                    $price_min = $temp_price;
                                    $id_min = $child_id;
                                }

                                if ($temp_price > $price_max) {
                                    $price_max = $temp_price;
                                    $id_max = $child_id;
                                }
                            }
                        }

                        $udata = array();
                        if ($id_min && $id_max) {
                            $udata['min'] = $id_min;
                            $udata['max'] = $id_max;
                        }
                        if (!empty($udata)) {
                            update_post_meta($product_id, '_kapos_custom_price', $udata);
                            $list_processed[] = $product_id;
                        }
                    } 
                }
            }
            
            $message = "Đã tính lại giá hiển thị biến thể (" . count($list_processed) . " sản phẩm): " . implode(', ', $list_processed);
        }
    }
    
    if (!empty($message)) {
        $message = '<div class="alert alert-success">' . $message . '</div>';
    }
    
    kawoo_load_assets_common_admin();

    echo '<div class="wrap">
        <div class="row">
                <div class="col-lg-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Cài Đặt KA WOO Tools
                        </div>
                        <div class="panel-body">
                            ' . $message . '
                            <div class="row">
                                <div class="col-lg-6">
                                    <form role="form" method="POST">
                                        <div class="col-lg-12">
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
                                            
                                <div class="col-lg-6">
                                    <div class="form-group">
                                        <form role="form" method="POST">
                                            <div class="col-lg-12">
                                                    <input class="hidden" id="action_type" type="text" name="action_type" value="hide_outofstock_products">
                                                    <button type="submit" name="action_type" value="hide_outofstock_products" class="btn btn-danger">Cập nhật lại Mức độ hiển thị catalog</button>
                                                    <button type="submit" name="action_type" value="update_custom_price_field" class="btn btn-danger">Tính lại giá hiển thị biến thể</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            </div>';
}

function function_kawoo_testing_page() {
    echo 'TESTING PAGE';
}