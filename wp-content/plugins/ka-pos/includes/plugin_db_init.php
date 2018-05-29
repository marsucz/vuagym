<?php

if (!defined('DB_KIOTVIET_PRODUCTS')) {
    define('DB_KIOTVIET_PRODUCTS', 'vg_kiotviet_products');
}

if (!defined('DB_KAPOS_IMPORTS')) {
    define('DB_KAPOS_IMPORTS', 'kapos_imports');
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

function kapos_import_create_db() {
    global $wpdb;
    $db_name = $wpdb->base_prefix . DB_KAPOS_IMPORTS;
    $charset_collate = $wpdb->get_charset_collate();
    
    if($wpdb->get_var("show tables like '$db_name'") != $db_name) 
    {
            $sql = 'CREATE TABLE ' . $db_name . ' (
                    `id` INT NOT NULL AUTO_INCREMENT,
                    `filename` VARCHAR(512) NULL,
                    `product_code` VARCHAR(45) NULL,
                    `product_name` VARCHAR(512) NULL,
                    `product_quantity` INT NULL,
                    `created` DATETIME NULL DEFAULT NOW(),
                    PRIMARY KEY (`id`)
            )' . $charset_collate . ';';
                
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($sql);
    }
}
