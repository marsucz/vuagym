<?php

/* START Custom price on category page */
if(!function_exists('tuandev_check_variable_price')){
    function tuandev_check_variable_price($parent_id) {
        global $wpdb;
        $query = "  SELECT *
                    FROM {$wpdb->prefix}postmeta
                    WHERE post_id IN (  SELECT ID
                                            FROM {$wpdb->prefix}posts 
                                            WHERE post_parent = {$parent_id}
                                            AND post_type = 'product_variation')
                    AND meta_key = '_regular_price'
                    AND meta_value > 0
                    LIMIT 1";

        $results = $wpdb->get_results($query, ARRAY_A );

        if (count($results) > 0) {
            return true;
        } else {
            return false;
        }
    }
}

if(!function_exists('tuandev_process_price_html')){
    function tuandev_process_price_html($product) {
        
        if ( $product && $product->is_type( 'variable' )) {
            
            $price_data = get_post_meta($product->get_id(), '_kapos_custom_price', true);
            
            if ($price_data && is_array($price_data)) {
                
                if (class_exists('YITH_Role_Based_Prices_Product')) {
                    $YITH_Role = YITH_Role_Based_Prices_Product();
                } else {
                    $YITH_Role = null;
                }
                
                $html = '';
                
                if ($price_data['min'] == $price_data['max']) {
                    $child_min = wc_get_product($price_data['min']);
                    $html = $child_min->get_price_html();
                } else {
                    
                    $child_min = wc_get_product($price_data['min']);
                    $child_max = wc_get_product($price_data['max']);
                    
                    $has_vip = false;
                    // Xu ly gia VIP
                    if (!is_null($YITH_Role)) {
                        
                        $vip_min = $YITH_Role->get_role_based_price($child_min);
                        $vip_max = $YITH_Role->get_role_based_price($child_max);
                        
                        $has_vip = true;
                        if ($vip_min !== 'no_price' && $vip_max !== 'no_price') {
                            $html = kiotViet_formatted_price($vip_min) . ' - ' . kiotViet_formatted_price($vip_max);
                        } elseif ($vip_min !== 'no_price') {
                            $html = kiotViet_formatted_price($vip_min);
                        } elseif ($vip_max !== 'no_price') {
                            $html = kiotViet_formatted_price($vip_max);
                        } else {
                            $has_vip = false;
                        }
                    }
                    
                    if ($has_vip) {
                        $your_price_txt = get_option( 'ywcrbp_your_price_txt' );
                        if ($your_price_txt) $your_price_txt .= " ";
                        $html = '<span class="td-price">' . $your_price_txt . $html . '</span>';
                    } else {
                        $min_price = $child_min->get_price();
                        $max_price = $child_max->get_price();
                        
                        $html = kiotViet_formatted_price($min_price) . ' - ' . kiotViet_formatted_price($max_price);
                        
                        if ($child_min->is_on_sale() && $child_max->is_on_sale()) {
                            $sale_price_txt = get_option( 'ywcrbp_sale_price_txt' );
                            if ($sale_price_txt) $sale_price_txt .= " ";
                            $html = '<span class="td-price">' . $sale_price_txt . $html . '</span>';
                        } else {
                            $regular_price_txt = get_option( 'ywcrbp_regular_price_txt' );
                            if ($regular_price_txt) $regular_price_txt .= " ";
                            $html = '<span class="td-price">' . $regular_price_txt . $html . '</span>';
                        }
                    }
                }
            } else {
                $check_price = tuandev_check_variable_price($product->get_id());
                if (!$check_price) {
                    $html = '';
                } else {
                    $html = $product->get_price_html();
                }
            }
            
        } else {
            if ($product->get_regular_price()) {
                $html = $product->get_price_html();
            } else {
                $html = '';
            }
        }
        
        $html = '<div class="product-price">' . $html . '</div>';
        
        return $html;
    }
}
/* END Custom price on category page */