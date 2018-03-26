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
    add_action('login_init', 'send_frame_options_header', 10, 0);
    add_action('admin_init', 'send_frame_options_header', 10, 0);
}

function ka_woo_tools_admin_menu() {
    add_menu_page('KA WOO', 'KA WOO', 'manage_options', 'ka-woo-tools', 'function_ka_woo_tools_page', 'dashicons-admin-multisite', 4);
//    add_submenu_page('ka-woo-tools', __('KA WOO'), __('KA WOO'), 'edit_posts', 'ka-woo-options');
    add_submenu_page('ka-woo-tools', __('Manager Tabs'), __('Manager Tabs'), 'manage_options', 'kawoo-manager-tabs', 'function_manager_tabs_page');
    add_submenu_page('ka-woo-tools', __('Testing'), __('Testing'), 'manage_options', 'ka-woo-testing', 'function_kawoo_testing_page');
}

function function_ka_woo_tools_page() {
    echo '<div class="wrap">
            <b>KA WOO Tools for Woocommerce:</b> Công cụ để tùy chỉnh các chức năng của WooCommerce
            </div>';
}

function kawoo_pdate_default_manager_tabs_options() {
    update_option('kawoo_show_type', 1);
    update_option('kawoo_number_of_products', MYPOS_PER_PAGE);
    update_option('kawoo_image_link', '');
}

function function_manager_tabs_page() {
    
    set_time_limit(600);
    
    kawoo_load_assets_tablist();
    
    echo '<div class="wrap">
        <h2>Quản lý Woocommerce</h2>';
        
    $active_tab = '';
    
    if (isset($_GET['tab'])) {
        $active_tab = $_GET['tab'];
    }
    
    if (isset($_POST['kawoo_show_type'])) {
        update_option('kawoo_show_type', $_POST['kawoo_show_type']);
        update_option('kawoo_number_of_products', $_POST['kawoo_number_of_products']);
        update_option('kawoo_image_link', $_POST['kawoo_image_link']);
    }

    if (empty($_POST) && !isset($_GET['paged'])) {
        kawoo_pdate_default_manager_tabs_options();
    }
    
    $show_type = get_option('kawoo_show_type');
    $show_products = get_option('kawoo_number_of_products');
    $image_link = get_option('kawoo_image_link');
    
    echo '<h2 class="nav-tab-wrapper">
            <a href="?page=mypos-sync" class="nav-tab ' . ($active_tab == "" ? "nav-tab-active" : "") . '">Welcome</a>
            <a href="?page=kawoo-manager-tabs&tab=product_picture_manager" class="nav-tab ' . ($active_tab == "product_picture_manager" ? "nav-tab-active" : "") . '">Quản lý ảnh sản phẩm</a>
            <a href="?page=kawoo-manager-tabs&tab=product_category_manager" class="nav-tab ' . ($active_tab == "product_category_manager" ? "nav-tab-active" : "") . '">Quản lý danh mục sản phẩm</a>
         </h2>';
    
    switch ($active_tab) {
        
        case 'product_picture_manager': // Hien thi cac san pham khong co anh
            
            echo '  <div class="wrap">
                    <form id="product-image-manager-form" method="POST">
                            <label>Bộ lọc </label>
                            <select id="kawoo_show_type" name="kawoo_show_type">
                                <option value="1"' . ($show_type == 1 ? 'selected' : '') . '>Chưa có "Ảnh sản phẩm"</option>
                                <option value="2"' . ($show_type == 2 ? 'selected' : '') . '>Dùng link ảnh ...</option>
                            </select>
                            <label id="kawoo_product_numbers_label"> Số lượng SP hiển thị </label>
                            <input type="number" id="kawoo_number_of_products" name="kawoo_number_of_products" value="' . $show_products . '" min="1" required>
                            <label id="image_link_label"> Link Ảnh </label>
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
            
        case 'product_category_manager':
            
            echo '  <div class="wrap">
                    <form id="product-image-manager-form" method="POST">
                            <label id="kawoo_product_numbers_label"> Số lượng SP hiển thị </label>
                            <input type="number" id="kawoo_number_of_products" name="kawoo_number_of_products" value="' . $show_products . '" min="1" required>
                        <input type="submit" class="button" value="Áp dụng">
                    </form>
                    </div>';
            
            if (empty($_POST) && !isset($_GET['paged'])) {
                
            } else {
                echo '<form method="POST" id="product-category-manager-list">';
                $myListTable = new Kawoo_Product_Category_List($show_products);
                $myListTable->prepare_items();
                $myListTable->display();
                echo '</form>';
            }
            break;
            
        default:
            echo '<div class="wrap">
                    <span>Vui lòng chọn các TAB chức năng.</span>
                  </div>';
                    
            kawoo_pdate_default_manager_tabs_options();
            break;
        
    }
    
    echo '</div>';
    
}



function function_kawoo_testing_page() {
    $args = array('hide_empty'=> 0,
    'taxonomy'=> 'product_cat',
    'hierarchical'=>1,
    'echo'=>0);

$cats = wp_category_checklist($args);
echo $cats;
}

?>