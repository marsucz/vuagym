<?php

/**
 * Description of global_ajax
 *
 * @author MT
 */

require_once 'kiotviet_api.php';
require_once 'function_template.php';

//add_action( 'wp_enqueue_scripts', 'global_admin_ajax' );
//
//function global_admin_ajax() {
//    
////    wp_enqueue_script(
////            'jquery-min',
////            WC_PLUGIN_URL . 'assets/lib/jquery-3.3.1.min.js',
////             array( 'jquery' ),
////            '3.3.1',
////            true
////    );
////    
//    wp_enqueue_style('kiotviet-css', WC_PLUGIN_URL . 'assets/css/kiotviet.css' );
//    
////    wp_enqueue_script(
////            'jquery-validate',
////            WC_PLUGIN_URL . 'assets/lib/jquery.validate.min.js',
////             array( 'jquery' ),
////            '1.17.0',
////            true
////    );
//    
//
//        
//    wp_enqueue_script(
//		'global',
//		WC_PLUGIN_URL . 'assets/js/kiotviet.js',
//		array( 'jquery' ),
//		'1.0.0',
//		true
//    );
//    
//    wp_localize_script(
//            'global',
//            'global',
//            array(
//                    'ajax' => ,
//            )
//    );
//    
//
//}

add_action( 'wp_enqueue_scripts', 'global_admin_ajax' );
function global_admin_ajax() {

    wp_enqueue_style('mypos-css', WC_PLUGIN_URL . 'assets/css/mypos.css' );
    
    wp_register_script( 'mypos-js', WC_PLUGIN_URL . 'assets/js/mypos.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'mypos-js' );
    wp_register_script( 'mypos-ajaxcart', WC_PLUGIN_URL . 'assets/js/mypos_jaxcart.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_script( 'mypos-ajaxcart' );
    wp_localize_script(
        'mypos-js',
        'global',
        array(
                'ajax' => admin_url( 'admin-ajax.php' ),
            )
    );
}

//add_action( 'wp_enqueue_scripts', 'theme_enqueue_scripts', 10, 1 );
//function theme_enqueue_scripts() {
//    
//    
//}

//function ja_ajax_get_productquantity() {
//    
//    if (!isset($_POST['id']) || empty($_POST['id'])) {
//        return false;
//    }
//    
//    $dbModel = new DbModel();
//    $kiotviet_api = new KiotViet_API();
//    
//    $product = $dbModel->get_productInfo_byProductCode($_POST['id']);
//    if (count($product) == 0) {
//        $t = date('Ymd');
//        $log_file = "KiotViet-{$t}.txt";
//        $log_text = "SKU: {$_POST['id']} not exists on KiotViet or You haven't updated the database.";
//        write_logs($log_file, $log_text);
//        // Let clients apply their cart
//        $result = MAX_QUANTITY;
//    } else {
//        $result = $kiotviet_api->get_product_quantity_byKiotvietProductID($product[0]['product_id']);
//    }
//    
//    $return['status'] = 1;
//    $return['quantity'] = $result;
//    wp_send_json_success( $return );
//
//}
//
//add_action( 'wp_ajax_get_productquantity', 'ja_ajax_get_productquantity' );
//add_action( 'wp_ajax_nopriv_get_productquantity', 'ja_ajax_get_productquantity' );
