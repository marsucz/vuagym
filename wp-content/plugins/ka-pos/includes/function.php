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

function get_sku_store_phu($sku) {
    $prefix = get_option('kiotviet2_prefix');
    $new_key = substr_replace($sku, $prefix, 0, 2);
    return $new_key;
}

function get_sku_store_main($sku) {
    $prefix = 'SP';
    $new_key = substr_replace($sku, $prefix, 0, 2);
    return $new_key;
}

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

if ( ! function_exists( 'mypos_get_variation_title' ) ) {
    function mypos_get_variation_title($product) {
        
        if ($product->is_type( 'simple' ) || $product->is_type( 'variable' ) ) {
            return $product->get_name();
        }
        
        $attributes = $product->get_attributes();
        if ( ! $attributes ) {
            return $product->get_name();
        }
        
        $product_name = $product->get_title();
        
        foreach ($attributes as $key => $attr) {
            if (empty($attr)) {
                $tax = get_taxonomy($key);
                if (is_object($tax)) {
                    $tax_labels = get_taxonomy_labels($tax);
                    if (is_object($tax_labels)) {
                        $tax_name = $tax_labels->singular_name;
                        $product_name .= ' - ' . $tax_name . ' bất kì';
                        continue;
                    } 
                }
                $product_name .= ' - Thuộc tính bất kì';
            } else {
                $product_name .= ' - ' . mypos_attribute_slug_to_title($key, $attr);
            }
        }

        return $product_name;
    }
}

if ( ! function_exists( 'mypos_attribute_slug_to_title' ) ) {
    function mypos_attribute_slug_to_title( $attribute ,$slug ) {
            global $woocommerce;
            if ( taxonomy_exists( esc_attr( str_replace( 'attribute_', '', $attribute ) ) ) ) {
                    $term = get_term_by( 'slug', $slug, esc_attr( str_replace( 'attribute_', '', $attribute ) ) );
                    if ( ! is_wp_error( $term ) && $term->name )
                            $value = $term->name;
            } else {
                    $value = apply_filters( 'woocommerce_variation_option_name', $value );
            }
            return $value;
    }
}

function build_list_attribute_taxonomies($selected = "", $default = "0") {
    $dbModel = new DbModel();
    $terms = $dbModel->get_attribute_taxonomies();
    
    if (!$selected) $selected = $default;
    $html = '<option value="0">None</option>';
    foreach ($terms as $term) {
        if ($term['attribute_name'] == $selected) {
            $html .= '<option value="' . $term['attribute_name'] . '" selected>' . $term['attribute_label'] . '</option>';
        } else {
            $html .= '<option value="' . $term['attribute_name'] . '">' . $term['attribute_label'] . '</option>';
        }
    }
    
    return $html;
}

function build_list_attribute_dang_cap_nhat($selected = "", $default = "0") {
    
    $tt_hansudung = get_option('mypos_tt_han_su_dung');
    if (!$tt_hansudung) {
        $tt_hansudung = 'han-su-dung';
    }
    
    $tt_hansudung = "pa_" . $tt_hansudung;
    
    $terms = get_terms(array('taxonomy' => $tt_hansudung, 'hide_empty' => false));
    
    if (!$selected) $selected = trim($default);
    
    $html = '<option value="0">Chưa chọn</option>';
    foreach ($terms as $term) {
        if ($term->term_id == $selected) {
            $html .= '<option value="' . $term->term_id . '" selected>' . $term->name . '</option>';
        } else {
            $html .= '<option value="' . $term->term_id . '">' . $term->name . '</option>';
        }
    }
    
    return $html;
}
