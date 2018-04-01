<?php

if (!defined('DB_KIOTVIET_PRODUCTS')) {
    define('DB_KIOTVIET_PRODUCTS', 'vgd_kiotviet_products');
}

function kiotviet_product_create_db() {
    global $wpdb;
    $db_name = DB_KIOTVIET_PRODUCTS;
    $charset_collate = $wpdb->get_charset_collate();
    
    if($wpdb->get_var("show tables like '$db_name'") != $db_name) 
    {
            $sql = 'CREATE TABLE ' . $db_name . ' (
                    `product_id` INT NOT NULL,
                    `product_store` smallint(6) NOT NULL,
                    `product_code` VARCHAR(25) NOT NULL,
                    `product_updated` DATETIME NULL,
                    PRIMARY KEY (`product_id`,`product_code`,`product_store`)
            )' . $charset_collate . ';';
                
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
    }
}
