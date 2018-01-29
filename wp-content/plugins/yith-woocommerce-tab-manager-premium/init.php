<?php
/**
 * Plugin Name: YITH WooCommerce Tab Manager Premium
 * Plugin URI: http://yithemes.com/themes/plugins/yith-woocommerce-tab-manager/
 * Description: YITH WooCommerce Tab Manager allows you to add Tab to products.
 * Version: 1.1.25
 * Author: YITHEMES
 * Author URI: http://yithemes.com/
 * Text Domain: yith-woocommerce-tab-manager
 * Domain Path: /languages/
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Tab Manager
 * @version 1.1.25
 */
 
/*
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */


if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


    function yith_ywtm_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e( 'YITH WooCommerce Tab Manager is enabled but not effective. It requires WooCommerce in order to work.', 'yith-woocommerce-tab-manager' ); ?></p>
        </div>
    <?php
    }


if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YWTM_FREE_INIT', plugin_basename( __FILE__ ) );




if ( !defined( 'YWTM_VERSION' ) ) {
    define( 'YWTM_VERSION', '1.1.25' );
}

if ( ! defined( 'YWTM_PREMIUM' ) ) {
    define( 'YWTM_PREMIUM', '1' );
}

if ( !defined( 'YWTM_INIT' ) ) {
    define( 'YWTM_INIT', plugin_basename( __FILE__ ) );
}

if ( !defined( 'YWTM_FILE' ) ) {
    define( 'YWTM_FILE', __FILE__ );
}

if ( !defined( 'YWTM_DIR' ) ) {
    define( 'YWTM_DIR', plugin_dir_path( __FILE__ ) );
}

if ( !defined( 'YWTM_URL' ) ) {
    define( 'YWTM_URL', plugins_url( '/', __FILE__ ) );
}

if ( !defined( 'YWTM_ASSETS_URL' ) ) {
    define( 'YWTM_ASSETS_URL', YWTM_URL . 'assets/' );
}

if ( !defined( 'YWTM_ASSETS_PATH' ) ) {
    define( 'YWTM_ASSETS_PATH', YWTM_DIR . 'assets/' );
}

if ( !defined( 'YWTM_TEMPLATE_PATH' ) ) {
    define( 'YWTM_TEMPLATE_PATH', YWTM_DIR . 'templates/' );
}

if ( !defined( 'YWTM_INC' ) ) {
    define( 'YWTM_INC', YWTM_DIR . 'includes/' );
}

if( !defined('YWTM_SLUG' ) ){
    define( 'YWTM_SLUG', 'yith-woocommerce-tab-manager' );
}

if ( ! defined( 'YWTM_SECRET_KEY' ) ) {
    define( 'YWTM_SECRET_KEY', 'bcME6LxHr4vj3nCzP96i' );
}

if ( !function_exists( 'yith_plugin_registration_hook' ) ) {
    require_once 'plugin-fw/yit-plugin-registration-hook.php';
}

register_activation_hook( __FILE__, 'yith_plugin_registration_hook' );

if ( function_exists( 'yith_deactive_jetpack_module' ) ) {
    global $yith_jetpack_1;
    yith_deactive_jetpack_module( $yith_jetpack_1, 'YWTM_PREMIUM', plugin_basename( __FILE__ ) );
}

/* Plugin Framework Version Check */
if( !function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YWTM_DIR . 'plugin-fw/init.php' ) ) {
    require_once(YWTM_DIR . 'plugin-fw/init.php');
}
yit_maybe_plugin_fw_loader( YWTM_DIR  );

if ( ! function_exists( 'YITH_Tab_Manager_Premium_Init' ) ) {

    /* Load YWCM text domain */
    load_plugin_textdomain( 'yith-woocommerce-tab-manager', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    /**
     * Unique access to instance of YITH_Tab_Manager class
     *
     * @return YITH_Tab_Manager_Premium
     * @since 1.0.5
     */
    function YITH_Tab_Manager_Premium_Init() {
        // Load required classes and functions
        require_once( YWTM_INC . 'class.yith-woocommerce-tab-manager.php' );
        require_once( YWTM_INC . 'class.yith-woocommerce-tab-manager-premium.php' );

        global $YIT_Tab_Manager ;
        $YIT_Tab_Manager = YITH_WC_Tab_Manager_Premium::get_instance();
    }
}

add_action('yith_wc_tabmanager_premium_init', 'YITH_Tab_Manager_Premium_Init' );

if( !function_exists( 'yith_tab_manager_premium_install' ) ){
    /**
     * install tab manager
     * @author YIThemes
     * @since 1.0.5
     */
    function yith_tab_manager_premium_install(){

        if( !function_exists( 'WC' ) ){
            add_action( 'admin_notices', 'yith_ywtm_install_woocommerce_admin_notice' );
        }
        else
            do_action( 'yith_wc_tabmanager_premium_init' );

    }
}

add_action( 'plugins_loaded', 'yith_tab_manager_premium_install', 11 );