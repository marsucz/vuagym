<?php

function kawoo_write_logs($file_name, $text) {
    
    $folder_path = KAWOO_PLUGIN_DIR . '/logs';
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

if ( ! function_exists( 'delete_post_cache' ) ) {
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
}
if ( ! function_exists( 'kiotViet_formatted_price' ) ) {
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

if ( ! function_exists( 'tuandev_update_price_variation_field' ) ) {
    function tuandev_update_price_variation_field( $product_id ) {
        
        $product = wc_get_product($product_id);
        if ($product->is_type('simple')) {
            return;
        }
        if ($product->is_type( 'variation' )) {
            $base_product_id = $product->get_parent_id();
        } else {
            $base_product_id = $product_id;
        }
        
        $dbModel = new DbModel();
        $childrens = $dbModel->get_children_ids($base_product_id);

        $max_int = 999999999;
        $min_int = -999999999;
        $price_min = $max_int;
        $price_max = $min_int;

        $id_min = 0;
        $id_max = 0;

        foreach ($childrens as $child) {
            $child_id = $child['ID'];
            $child_prod = wc_get_product($child_id);
            $temp_price = $child_prod->get_price();
            if ($temp_price) {
                if ($temp_price < $price_min) {
                    $price_min = $temp_price;
                    $id_min = $child_id;
                }

                if ($temp_price > $price_max) {
                    $price_max = $temp_price;
                    $id_max = $child_id;
                }
            }
        }

        $udata = array();
        if ($id_min && $id_max) {
            $udata['min'] = $id_min;
            $udata['max'] = $id_max;
        }
        if (!empty($udata)) {
            update_post_meta($base_product_id, '_kapos_custom_price', $udata);
        }
    }
}