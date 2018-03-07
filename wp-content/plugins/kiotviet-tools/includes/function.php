<?php

function write_logs($file_name, $text) {
    
    $folder_path = WC_PLUGIN_DIR . '/logs';
    $file_path = $folder_path . '/' . $file_name;
    
    if (!file_exists($folder_path)) {
        mkdir($folder_path, 0755, true);
    }
    
    $file = fopen($file_path, "a");
    
    date_default_timezone_set('Asia/Ho_Chi_Minh');
    $date = date('Y-m-d H:i:s', time());
    
    $body = "\n" . $date . ' ';
    $body .= $text;
    
    fwrite($file, $body);
    fclose($file);
    
}

//function get_product_title_in_path($product_id) {
//    
//    $product = wc_get_product( $product_id );
//
//    if ($product->is_type( 'variation' )) {
//        $base_product_id = $product->get_parent_id();
//    } elseif ($product->is_type( 'simple' )) {
//        $base_product_id = $product_id;
//    } else {
//        $base_product_id = $product_id;
//    }
//
//    $permalink = urldecode(get_permalink($base_product_id));
//    //for trash contents
//    $permalink = rtrim($permalink, "/");
//    $permalink = preg_replace("/__trashed$/", "", $permalink);
//    
//    $matches = array();
//    preg_match("/[^\/]+$/", $permalink, $matches);
//    $last_word = $matches[0];
//    
//    return $last_word;
//}

function delete_post_cache($product_id) {
    
    if (!$product_id) {
        return false;
    }
    
    if (class_exists('WpFastestCache')) {
        $product = wc_get_product( $product_id );

        if ($product->is_type( 'variation' )) {
            $base_product_id = $product->get_parent_id();
        } elseif ($product->is_type( 'simple' )) {
            $base_product_id = $product_id;
        } else {
            $base_product_id = $product_id;
        }

        $wp_fast_cache = new WpFastestCache();
        $wp_fast_cache->singleDeleteCache(false, $base_product_id);
        
        return true;
    } else {
        return false;
    }
    
    return false;
}

//function delete_post_cache($product_id) {
//    
//    if (!$product_id) {
//        return false;
//    }
//    
//    $cache_danhmuc = WP_CONTENT_DIR . '/cache/all/danh-muc';
//    $del_dm = delete_directory($cache_danhmuc);
//    
//    $product_url = get_product_title_in_path($product_id);
//    if (!empty($product_url)) {
//        $cache_product = WP_CONTENT_DIR . '/cache/all/cua-hang/' . trim($product_url);
//        $del_pro = delete_directory($cache_product);
//    } else {
//        $del_pro = false;
//    }
//    
//    $return['danh-muc'] = $del_dm;
//    $return['san-pham'] = $del_pro;
//    
//    return $return;
//}

//function delete_directory($dirname) {
//    
//        if (is_dir($dirname)) {
//          $dir_handle = opendir($dirname);
//        } else {
//           $dir_handle = false;
//        }
//        if (!$dir_handle) {
//            return $dirname . ' Danh muc khong ton tai!';
//        }
//        while($file = readdir($dir_handle)) {
//              if ($file != "." && $file != "..") {
//                   if (!is_dir($dirname."/".$file)) {
//                       unlink($dirname."/".$file);
//                   } else {
//                       delete_directory($dirname.'/'.$file);
//                   }
//              }
//        }
//        closedir($dir_handle);
//        rmdir($dirname);    
//        return true;
//}

//function write_logs_api($url) {
//    
////    $folder_name = 
//    $file_path = WC_PLUGIN_DIR . '/logs/tuan' . $file_name;
//    
//    if (!file_exists('path/to/directory')) {
//    mkdir('path/to/directory', 0777, true);
//    }
//    
//    $file = fopen($file_path, "a");
//    
//    date_default_timezone_set('Asia/Ho_Chi_Minh');
//    $date = date('Y-m-d H:i:s', time());
//    
//    $body = "\n" . $date . ' ';
//    $body .= $text;
//    
//    fwrite($file, $body);
//    fclose($file);
//    
//}

function kiotViet_get_preOrder_status($item_id) {
    
    $pre_order_status = false;
    $pre_order = new YITH_Pre_Order_Product( $item_id );
    
    if ( 'yes' == $pre_order->get_pre_order_status() ) {
        $pre_order_status = true;  // Sắp có hàng
    } 
    
    return $pre_order_status;
    
}

/**
 * Formats the RAW woocommerce price
 *
 * @since     1.0.0
 * @param  	  int $price
 * @return    string 
 */

function kiotViet_formatted_price($price){
        if(!$price) {
            return '0 ₫';
        }
//        $options 	= get_option('xoo-wsc-gl-options');
//        $default_wc = isset( $options['sc-price-format']) ? $options['sc-price-format'] : 0;
//
//        if($default_wc == 1){
//                return wc_price($price);
//        }

        $thous_sep = wc_get_price_thousand_separator();
        $dec_sep   = wc_get_price_decimal_separator();
        $decimals  = wc_get_price_decimals();
        $price 	   = number_format( $price, $decimals, $dec_sep, $thous_sep );
        
        $format   = get_option( 'woocommerce_currency_pos' );
        $csymbol  = get_woocommerce_currency_symbol();

        switch ($format) {
                case 'left':
                        $fm_price = $csymbol.$price;
                        break;

                case 'left_space':
                        $fm_price = $csymbol.' '.$price;
                        break;

                case 'right':
                        $fm_price = $price.$csymbol;
                        break;

                case 'right_space':
                        $fm_price = $price.' '.$csymbol;
                        break;

                default:
                        $fm_price = $csymbol.$price;
                        break;
        }
        return $fm_price;
}