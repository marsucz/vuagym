<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function prefix_enqueue() {
    // JS
    wp_register_script('prefix_bootstrap', WC_PLUGIN_URL . 'assets/lib/bootstrap.min.js');
    wp_enqueue_script('prefix_bootstrap');
    
    // CSS
    wp_register_style('prefix_bootstrap', WC_PLUGIN_URL . 'assets/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');
    
    wp_enqueue_style('my-styles', WC_PLUGIN_URL . 'assets/styles.css' );
    
}

function load_assets_get_kiotviet_products() {
    
    load_assets_common();
    
    wp_enqueue_script(
		'global',
		WC_PLUGIN_URL . 'assets/js/get_kiotviet_products.js',
		array( 'jquery' ),
		'1.0.0',
		true
    );
    
    wp_localize_script(
		'global',
		'global',
		array(
			'ajax' => admin_url( 'admin-ajax.php' ),
		)
	);
    
}

//function load_assets_datetime_picker() {
//    
//    wp_register_script('prefix_moment', WC_PLUGIN_URL . 'assets/moment.min.js');
//    wp_enqueue_script('prefix_moment');
//    
//    wp_register_script('prefix_datetime', '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js');
//    wp_enqueue_script('prefix_datetime');
//    
//    wp_enqueue_style('prefix_datetime', '//cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css' );
//}

function load_assets_common() {
    
    // JS
    wp_register_script('prefix_bootstrap', WC_PLUGIN_URL . 'assets/lib/bootstrap.min.js');
    wp_enqueue_script('prefix_bootstrap');
    wp_register_script('prefix_jquery', WC_PLUGIN_URL . 'assets/lib/jquery-3.3.1.min.js');
    wp_enqueue_script('prefix_jquery');
    wp_register_script('prefix_datatable', WC_PLUGIN_URL . 'assets/lib/jquery.dataTables.min.js');
    wp_enqueue_script('prefix_datatable');
    wp_register_script('prefix_toggle', WC_PLUGIN_URL . 'assets/lib/bootstrap-toggle.js');
    wp_enqueue_script('prefix_toggle');
    
//    
    // CSS
    wp_register_style('prefix_bootstrap', WC_PLUGIN_URL . 'assets/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');
    wp_register_style('prefix_datatable', WC_PLUGIN_URL . 'assets/css/jquery.dataTables.min.css');
    wp_enqueue_style('prefix_datatable');
    wp_register_style('prefix_toggle', WC_PLUGIN_URL . 'assets/css/bootstrap-toggle.min.css');
    wp_enqueue_style('prefix_toggle');
    
    wp_enqueue_style('my-styles', WC_PLUGIN_URL . 'assets/styles.css' );
    wp_enqueue_style('font-awesome', WC_PLUGIN_URL . 'assets/font-awesome/css/font-awesome.min.css' );
}

//function get_time2query($POST) {
//    $return['query_timetype'] = $POST['query_timetype'];
//    switch ($POST['query_timetype']) {
//        case 'time_custom':
//            if (isset($POST['time_start']) && !empty($POST['time_start'])) {
//                $return['time_start'] = date('Y-m-d H:i:s', strtotime($POST['time_start']));
//            }
//            if (isset($POST['time_end']) && !empty($POST['time_end'])) {
//                $return['time_end'] = date('Y-m-d H:i:s', strtotime($POST['time_end']));
//            }
//            break;
//    }
//    return $return;
//}
 
?>