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

add_action('plugins_loaded', 'ka_woo_tools_plugin_init');

function ka_woo_tools_plugin_init() {
    add_action('admin_menu', 'ka_woo_tools_admin_menu');
    add_action('login_init', 'send_frame_options_header', 10, 0);
    add_action('admin_init', 'send_frame_options_header', 10, 0);
}

function ka_woo_tools_admin_menu() {
    add_menu_page('KA WOO', 'KA WOO', 'manage_options', 'ka-woo-tools', 'function_ka_woo_tools_page', 'dashicons-admin-multisite', 4);
//    add_submenu_page('ka-woo-tools', __('KA WOO'), __('KA WOO'), 'manage_options', 'ka-woo-options', 'function_mypos_options_page');
}

function function_ka_woo_tools_page() {
    echo '<div class="wrap">
            <b>KA WOO Tools for Woocommerce:</b> Công cụ để tùy chỉnh các chức năng của WooCommerce
            </div>';
}

?>