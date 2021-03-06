<?php

function kawoo_load_assets_tab_category() {
    
    enqueue_select2_jquery();
    
    wp_enqueue_script(
		'global',
		KAWOO_PLUGIN_URL . 'assets/admin/js/tab_category.js',
		array( 'jquery' ),
		'1.0.0',
		true
    );
    
    kawoo_load_assets_common_tab();
    
}

function kawoo_load_assets_tab_picture() {
    wp_enqueue_script(
		'global',
		KAWOO_PLUGIN_URL . 'assets/admin/js/tab_picture.js',
		array( 'jquery' ),
		'1.0.0',
		true
    );
    
    kawoo_load_assets_common_tab();
}

function kawoo_load_assets_tab_price() {
    kawoo_load_assets_common_admin();
    wp_enqueue_script(
		'global',
		KAWOO_PLUGIN_URL . 'assets/admin/js/tab_price.js',
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

function kawoo_load_assets_tab_search() {
    wp_enqueue_script(
		'global',
		KAWOO_PLUGIN_URL . 'assets/admin/js/tab_search.js',
		array( 'jquery' ),
		'1.0.0',
		true
    );
    
    kawoo_load_assets_common_tab();
}

function kawoo_load_assets_tab_content() {
    enqueue_select2_jquery();
    wp_enqueue_script(
		'global',
		KAWOO_PLUGIN_URL . 'assets/admin/js/tab_content.js',
		array( 'jquery' ),
		'1.0.0',
		true
    );
    
    kawoo_load_assets_common_tab();
}

function kawoo_load_assets_common_tab() {
    // JS
    wp_register_script('prefix_jquery', KAWOO_PLUGIN_URL . 'assets/admin/lib/jquery-3.3.1.min.js');
    wp_enqueue_script('prefix_jquery');
    
    // CSS
    wp_enqueue_style('font-awesome', KAWOO_PLUGIN_URL . 'assets/    admin/font-awesome/css/font-awesome.min.css' );
    
    wp_enqueue_style('my-styles', KAWOO_PLUGIN_URL . 'assets/admin/css/my_tables.css' );
    
    wp_localize_script(
		'global',
		'global',
		array(
			'ajax' => admin_url( 'admin-ajax.php' ),
		)
    );
}

function kawoo_load_assets_common_admin() {
    
    // JS
    wp_register_script('prefix_jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js');
    wp_enqueue_script('prefix_jquery');
    wp_register_script('prefix_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js');
    wp_enqueue_script('prefix_bootstrap');
    
    // CSS
    wp_register_style('prefix_bootstrap', 'https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
    wp_enqueue_style('prefix_bootstrap');
    wp_enqueue_style('my-tables', KAWOO_PLUGIN_URL . 'assets/admin/css/my_tables.css' );
    wp_enqueue_style('my-styles', KAWOO_PLUGIN_URL . 'assets/admin/css/styles.css' );
    wp_enqueue_style('font-awesome', KAWOO_PLUGIN_URL . 'assets/admin/font-awesome/css/font-awesome.min.css' );
}

function enqueue_select2_jquery() {
    wp_register_style( 'kawoo_select2css', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.css', false, '1.0', 'all' );
    wp_register_script( 'kawoo_select2', '//cdnjs.cloudflare.com/ajax/libs/select2/3.4.8/select2.js', array( 'jquery' ), '1.0', true );
    wp_enqueue_style( 'kawoo_select2css' );
    wp_enqueue_script( 'kawoo_select2' );
}

function kawoo_load_assets_dataTable() {
    wp_register_script('prefix_datatable', KAWOO_PLUGIN_URL . 'assets/admin/lib/jquery.dataTables.min.js');
    wp_enqueue_script('prefix_datatable');
    wp_register_style('prefix_datatable', KAWOO_PLUGIN_URL . 'assets/admin/css/jquery.dataTables.min.css');
    wp_enqueue_style('prefix_datatable');
}

?>