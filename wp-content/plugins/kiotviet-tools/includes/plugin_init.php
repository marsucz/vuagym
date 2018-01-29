<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

if (!defined('DB_KIOTVIET_PRODUCTS')) {
    define('DB_KIOTVIET_PRODUCTS', 'vgd_kiotviet_products');
}

function kiotviet_product_create_db() {
    global $wpdb;
    $db_name = DB_KIOTVIET_PRODUCTS;
    $charset_collate = $wpdb->get_charset_collate();
    
    // create the ECPT metabox database table
    if($wpdb->get_var("show tables like '$db_name'") != $db_name) 
    {
            $sql = 'CREATE TABLE ' . $db_name . ' (
                    `product_id` INT NOT NULL,
                    `product_code` VARCHAR(25) NOT NULL,
                    `product_updated` DATETIME NULL,
                    PRIMARY KEY (`product_id`, `product_code`)
            )' . $charset_collate . ';';
                
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
    }
}

//function marketing_tools_uninstall_activate(){
//    register_uninstall_hook( __FILE__, 'marketing_tools_uninstall' );
//}
//register_activation_hook( __FILE__, 'marketing_tools_uninstall' );
// 
//function marketing_tools_uninstall_uninstall(){
//    $dbModel = new DbModel();
//    $query = 'DROP TABLE ' . DB_REDIRECTION . ';';
//    $dbModel->query($query);
//}

