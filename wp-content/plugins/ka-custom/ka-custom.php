<?php

/**
 * Plugin Name: KA Custom Tools
 * Plugin URI: http://vuagym.com
 * Description: Công cụ để tùy chỉnh các chức năng nhỏ trên Wordpress
 * Version: 2.0
 * Author: Khoa Anh
 * Author URI: http://vuagym.com
 * License: GPL2
 * Created On: 28-02-2018
 * Updated On: 22-03-2018
 */
// Define CONST
if (!defined('EZ_CUSTOM_PLUGIN_DIR')) {
    define('EZ_CUSTOM_PLUGIN_DIR', plugin_dir_path(__FILE__));
}
if (!defined('EZ_CUSTOM_PLUGIN_URL')) {
    define('EZ_CUSTOM_PLUGIN_URL', plugin_dir_url(__FILE__));
}

require_once 'includes/ez_custom_loader.php';
require_once 'includes/function.php';
require_once 'includes/featured_image.php';

add_action('plugins_loaded', 'ez_custom_tools_plugin_init');
add_action('admin_enqueue_scripts', 'ez_custom_load_remove_notice_js');
add_action( 'woocommerce_before_single_product', 'tuandev_process_default_product_variation', 10 );
//add_filter( 'woocommerce_hide_invisible_variations', '__return_true', 10, 3 );

function ez_custom_tools_plugin_init() {
    add_action('admin_menu', 'ez_custom_tools_admin_menu');
    add_action('login_init', 'send_frame_options_header', 10, 0);
    add_action('admin_init', 'send_frame_options_header', 10, 0);
    
    add_option('ezcustom_remove_notice', 1);
    add_option('ezcustom_expand_categories_list', 2);
}

function ez_custom_tools_admin_menu() {
    add_menu_page('KA Custom', 'KA Custom', 'manage_options', 'ka-custom-tools', 'function_ez_custom_tools_page', 'dashicons-admin-tools', 4);
    add_submenu_page('ka_custom-tools', __('Cài Đặt'), __('Cài Đặt'), 'manage_options', 'ka-custom-options');
}

function function_ez_custom_tools_page() {
    
    if (isset($_POST['ezcustom-remove-notice'])) {
        update_option('ezcustom_remove_notice', intval($_POST['ezcustom-remove-notice']));
        update_option('ezcustom_expand_categories_list', floatval($_POST['ezcustom-expand-categories-list']));
    }
    
    $loader = new EZ_Custom_Loader();
    $loader->load_assets_page_options();
    
    echo '<div class="wrap"><div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            Cài Đặt KA Custom Tools
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <form role="form" method="POST">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label>Bật/tắt chức năng: Ẩn thông báo trên trang Admin</label>
                                                <select class="form-control" id="ezcustom-remove-notice" name="ezcustom-remove-notice" required>';
                                                if (get_option('ezcustom_remove_notice')) {
                                                    echo '<option value="1" selected>Bật</option>
                                                    <option value="0">Tắt</option>';
                                                } else {
                                                    echo '<option value="1">Bật</option>
                                                    <option value="0" selected>Tắt</option>';
                                                }
                                                echo '</select>
                                            </div>
                                            <div class="form-group">
                                                <label>Mở rộng khung danh mục: </label>
                                                <input class="form-control" type="number" step=".01" id="ezcustom-expand-categories-list" name="ezcustom-expand-categories-list" value="' . get_option('ezcustom_expand_categories_list') . '" required>
                                                    <p class="help-block">Nhập 0 để mở rộng toàn bộ khung danh sách danh mục.</p>
                                            </div>
                                            <div class="form-group">
                                                <label>Các chức năng khác:</label>
                                                <ul>
                                                    <li> - Overwrite lại cách hiển thị giá khi sản phẩm hết hàng hoặc bị tắt.</li>
                                                    <li> - Overwrite lại cách hiển thị biến thể.</li>
                                                    <li> - Featured image in post.</li>
                                                </ul>
                                            </div>
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

?>