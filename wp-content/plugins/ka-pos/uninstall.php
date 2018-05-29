<?php

if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}
global $wpdb;

if (!defined('DB_KIOTVIET_PRODUCTS')) {
    $wpdb->query("DROP TABLE IF EXISTS vgd_kiotviet_products");
    $wpdb->query("DROP TABLE IF EXISTS vg_kiotviet_products");
} else {
    $wpdb->query("DROP TABLE IF EXISTS vgd_kiotviet_products");
    $wpdb->query("DROP TABLE IF EXISTS " . DB_KIOTVIET_PRODUCTS);
}

$db_name = $wpdb->base_prefix . 'kapos_imports';
$wpdb->query("DROP TABLE IF EXISTS {$db_name}");

echo "Uninstall Completed!";