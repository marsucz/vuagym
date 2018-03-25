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