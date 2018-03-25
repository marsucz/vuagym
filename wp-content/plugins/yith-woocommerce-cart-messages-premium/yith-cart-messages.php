<?php
/*
* Plugin Name: YITH WooCommerce Cart Messages Premium
* Plugin URI: http://yithemes.com/themes/plugins/yith-woocommerce-cart-messages
* Description: YITH WooCommerce Cart Messages Premium is a plugin for add custom messages to your customers
* Author: YITHEMES
* Text Domain: yith-woocommerce-cart-messages
* Version: 1.5.0
* Author URI: http://yithemes.com/
* Domain Path: /languages/
* WC requires at least: 3.0.0
* WC tested up to: 3.2.5
*/


if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! function_exists( 'is_plugin_active' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

// Define constants ________________________________________
if ( ! defined( 'YITH_YWCM_DIR' ) ) {
    define( 'YITH_YWCM_DIR', plugin_dir_path( __FILE__ ) );
}

if ( ! defined( 'YITH_YWCM_PREMIUM' ) ) {
    define( 'YITH_YWCM_PREMIUM', '1' );
}

if ( defined( 'YITH_YWCM_VERSION' ) ) {
    return;
}else{
    define( 'YITH_YWCM_VERSION', '1.5.0' );
}

if ( ! defined( 'YITH_YWCM_SUFFIX' ) ) {
    $suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
    define( 'YITH_YWCM_SUFFIX', $suffix );
}

if ( ! defined( 'YITH_YWCM_FILE' ) ) {
    define( 'YITH_YWCM_FILE', __FILE__ );
}

if ( ! defined( 'YITH_YWCM_URL' ) ) {
    define( 'YITH_YWCM_URL', plugins_url( '/', __FILE__ ) );
}

if ( ! defined( 'YITH_YWCM_ASSETS_URL' ) ) {
    define( 'YITH_YWCM_ASSETS_URL', YITH_YWCM_URL . 'assets' );
}

if ( ! defined( 'YITH_YWCM_TEMPLATE_PATH' ) ) {
    define( 'YITH_YWCM_TEMPLATE_PATH', YITH_YWCM_DIR . 'templates' );
}

if ( ! defined( 'YITH_YWCM_INIT' ) ) {
    define( 'YITH_YWCM_INIT', plugin_basename( __FILE__ ) );
}

if ( ! defined( 'YITH_YWCM_SLUG' ) ) {
    define( 'YITH_YWCM_SLUG', 'yith-woocommerce-cart-messages' );
}

if ( ! defined( 'YITH_YWCM_SECRET_KEY' ) ) {
    define( 'YITH_YWCM_SECRET_KEY', 't0mx22f19jkH2AP9TSPU' );
}


// Free version deactivation if installed __________________
if( ! function_exists( 'yit_deactive_free_version' ) ) {
    require_once 'plugin-fw/yit-deactive-plugin.php';
}
yit_deactive_free_version( 'YITH_YWCM_FREE_INIT', plugin_basename( __FILE__ ) );

// Yith jetpack deactivation if installed __________________
if ( function_exists( 'yith_deactive_jetpack_module' ) ) {
    global $yith_jetpack_1;
    yith_deactive_jetpack_module( $yith_jetpack_1, 'YITH_YWCM_PREMIUM', plugin_basename( __FILE__ ) );
}


// Plugin Framework Version Check __________________
if( ! function_exists( 'yit_maybe_plugin_fw_loader' ) && file_exists( YITH_YWCM_DIR . 'plugin-fw/init.php' ) ) {
    require_once( YITH_YWCM_DIR . 'plugin-fw/init.php' );
}
yit_maybe_plugin_fw_loader( YITH_YWCM_DIR  );



if( !function_exists('yith_ywcm_install_woocommerce_admin_notice')){
    function yith_ywcm_install_woocommerce_admin_notice() {
        ?>
        <div class="updated">
            <p><?php _e( 'You can\'t activate the plugin if you haven\'t activate woocommerce in advance.', 'yith-woocommerce-cart-messages' ); ?></p>
        </div>
        <?php
    }
}

if ( ! function_exists( 'yith_ywcm_premium_install' ) ) {
    function yith_ywcm_premium_install() {
        if ( !function_exists( 'WC' ) ) {
            add_action( 'admin_notices', 'yith_ywcm_install_woocommerce_admin_notice' );
        } else {
            do_action( 'yith_ywcm_init' );
        }
    }

    add_action( 'plugins_loaded', 'yith_ywcm_premium_install', 11 );
}

// Load required classes and functions _________________________
function yith_ywcm_premium_constructor(){

    // Woocommerce installation check _________________________
    if ( ! function_exists( 'WC' ) ) {
        add_action( 'admin_notices', 'yith_ywcm_install_woocommerce_admin_notice' );
        return ;
    }

    // Load YWCM text domain ___________________________________
    load_plugin_textdomain( 'yith-woocommerce-cart-messages', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    require_once( YITH_YWCM_DIR . 'yith-cart-messages-functions.php' );
    require_once( YITH_YWCM_DIR . 'class.yith-woocommerce-cart-messages.php' );
    require_once( YITH_YWCM_DIR . 'class.yith-woocommerce-cart-messages-premium.php' );
    require_once( YITH_YWCM_DIR . 'class.yith-woocommerce-cart-message.php' );

    global $YWCM_Instance;
    $YWCM_Instance = new YWCM_Cart_Messages_Premium();
}
add_action( 'yith_ywcm_init', 'yith_ywcm_premium_constructor' );