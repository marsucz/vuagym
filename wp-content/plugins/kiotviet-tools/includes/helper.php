<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function load_assets_sync_page() {
    
    load_assets_common_admin();
    load_assets_dataTable();
    
    wp_enqueue_script(
		'global',
		WC_PLUGIN_URL . 'assets/admin/js/compare_manual.js',
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

function load_assets_match_sku() {
    
    load_assets_common_admin();
    load_assets_dataTable();
    
    wp_enqueue_script(
		'global',
		WC_PLUGIN_URL . 'assets/admin/js/match_sku.js',
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

function load_assets_single_send_sms() {
    
    load_assets_common_admin();
    load_assets_dataTable();
    
    wp_enqueue_script(
		'global',
		WC_PLUGIN_URL . 'assets/admin/js/page_options.js',
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


function load_assets_page_options() {
    
    load_assets_common_admin();
    load_assets_dataTable();
    
    wp_enqueue_script(
		'global',
		WC_PLUGIN_URL . 'assets/admin/js/page_options.js',
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

function load_assets_manual_sync_table() {
    
    // JS
    wp_register_script('prefix_jquery', WC_PLUGIN_URL . 'assets/admin/lib/jquery-3.3.1.min.js');
    wp_enqueue_script('prefix_jquery');
    
    // CSS
//    wp_register_style('prefix_bootstrap', WC_PLUGIN_URL . 'assets/admin/css/bootstrap.min.css');
//    wp_enqueue_style('prefix_bootstrap');
//    wp_register_style('prefix_toggle', WC_PLUGIN_URL . 'assets/admin/css/bootstrap-toggle.min.css');
//    wp_enqueue_style('prefix_toggle');
//    wp_enqueue_style('my-styles', WC_PLUGIN_URL . 'assets/admin/css/styles.css' );
    wp_enqueue_style('font-awesome', WC_PLUGIN_URL . 'assets/admin/font-awesome/css/font-awesome.min.css' );
    
    wp_enqueue_style('my-styles', WC_PLUGIN_URL . 'assets/admin/css/my_tables.css' );
    
    wp_enqueue_script(
		'global',
		WC_PLUGIN_URL . 'assets/admin/js/manual_sync_web.js',
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

function load_assets_common_admin() {
    
    // JS
    wp_register_script('prefix_bootstrap', WC_PLUGIN_URL . 'assets/admin/lib/bootstrap.min.js');
    wp_enqueue_script('prefix_bootstrap');
    wp_register_script('prefix_jquery', WC_PLUGIN_URL . 'assets/admin/lib/jquery-3.3.1.min.js');
    wp_enqueue_script('prefix_jquery');
    
    // CSS
    wp_register_style('prefix_bootstrap', WC_PLUGIN_URL . 'assets/admin/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');
    wp_enqueue_style('my-styles', WC_PLUGIN_URL . 'assets/admin/css/styles.css' );
    wp_enqueue_style('font-awesome', WC_PLUGIN_URL . 'assets/admin/font-awesome/css/font-awesome.min.css' );
}

function load_assets_dataTable() {
    wp_register_script('prefix_datatable', WC_PLUGIN_URL . 'assets/admin/lib/jquery.dataTables.min.js');
    wp_enqueue_script('prefix_datatable');
    wp_register_style('prefix_datatable', WC_PLUGIN_URL . 'assets/admin/css/jquery.dataTables.min.css');
    wp_enqueue_style('prefix_datatable');
}

?>