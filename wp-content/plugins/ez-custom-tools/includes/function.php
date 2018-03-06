<?php

// Custom Default Variation

if(!function_exists('tuandev_process_default_product_variation')){
    function tuandev_process_default_product_variation() {
    
        global $product;

        $updated_default = false;

        if ( $product && $product->is_type( 'variable' )) {

            $change_variation = false;
            $default_variation_id = tuandev_get_default_product_variation($product);
            if (!$default_variation_id) {
                $change_variation = true;
            } else {
                $default_variation = wc_get_product($default_variation_id);
                if ($default_variation->get_stock_status() == 'outofstock') {
                    $change_variation = true;
                }
            }

            // Uu tien cac san pham dang BAT
            $args = array(
                'post_type'     => 'product_variation',
                'post_status'   => array('publish'),    // San pham dang BAT
                'post_parent'   => $product->get_id()
            );

            $variations = get_posts( $args );

            foreach ($variations as $child_id) {
                if ( $child_id ) {
                    $child = wc_get_product($child_id->ID);

                    //Fix 3: An cac bien the da het hang
                    if ($child->get_stock_status() == 'outofstock') {
                        if ($child->get_status() == 'publish') {
                            $child->set_status('private');
                            $child->save();
                        }
                    } else {
                        if ($change_variation && !$updated_default) {
                            // Get the attributes of the product has instock
                            $new_default_attributes = $child->get_attributes();
                            // Update the new attributes to parent product
                            $product->set_default_attributes($new_default_attributes);
                            $product->save();
                            $change_variation = false;
                            $updated_default = true;
                        }
                    }
                }
            }

            // Da cap nhat default cho san pham con hang THANH CONG => Stop function
            if ($updated_default || !$change_variation) {
                return true;
            }
        }

        return $updated_default;
    }
}        

if(!function_exists('tuandev_get_default_product_variation')){
    function tuandev_get_default_product_variation($product) {
    
        $attributes = $product->get_default_attributes();
        foreach( $attributes as $key => $value ) {
                if( strpos( $key, 'attribute_' ) === 0 ) {
                        continue;
                }
                unset( $attributes[ $key ] );
                $attributes[ sprintf( 'attribute_%s', $key ) ] = $value;
        }

        $data_store = WC_Data_Store::load( 'product' );
        return $data_store->find_matching_product_variation( $product, $attributes );
    }
}


// Custom Get Price

if(!function_exists('tuandev_process_get_price_html')){
    function tuandev_process_get_price_html($product) {
    
        if ( $product && $product->is_type( 'variable' )) {
            if (!$product->has_child()) {
                $html = tuandev_get_price_html($product);
                return $html;
            }
            $go = true;
            $childrens = $product->get_children();
            foreach ($childrens as $child_id) {
                $child = wc_get_product($child_id);
                if ($child->get_stock_status() == 'instock') {
                    $go = false;
                    break;
                }
            }

            if ($go) {
                $html = tuandev_get_price_html($product);
                return $html;
            } else {
                $html = $product->get_price_html();
            }
        } else {
            $html = $product->get_price_html();
        }
        return $html;
    }
}

if(!function_exists('tuandev_get_price_html')){
    function tuandev_get_price_html($product) {
    
        $max_int = 999999999;
        $min_int = -999999999;

        $regular_min = $max_int;
        $regular_max = $min_int;
        $sale_min = $max_int;
        $sale_max = $min_int;
        $vip_min = $max_int;
        $vip_max = $min_int;

        if (class_exists('YITH_Role_Based_Prices_Product')) {
            $YITH_Role = YITH_Role_Based_Prices_Product();
        } else {
            $YITH_Role = null;
        }
    //    $product = wc_get_product($product_id);

        if ( $product && $product->is_type( 'variable' )) {

            $args = array(
                'post_type'     => 'product_variation',
                'post_status'   => array( 'private', 'publish' ),
                'post_parent'   => $product->get_id()
            );

            $variations = get_posts( $args );
            
            foreach ($variations as $child_id) {
                if ( $child_id ) {
                    $child = wc_get_product($child_id->ID);

                    $temp_regular = $child->get_regular_price();
                    if ($temp_regular !== '') {
                        $regular_min = ($temp_regular < $regular_min) ? $temp_regular : $regular_min;
                        $regular_max = ($temp_regular > $regular_max) ? $temp_regular : $regular_max;
                    }

                    $temp_sale = $child->get_sale_price();
                    if ($temp_sale !== '') {
                        $sale_min = ($temp_sale < $sale_min) ? $temp_sale : $sale_min;
                        $sale_max = ($temp_sale > $sale_max) ? $temp_sale : $sale_max;
                    }

                    // Xu ly gia VIP
                    if (!is_null($YITH_Role)) {
                        $temp_vip = $YITH_Role->get_role_based_price($child);
                        if ($temp_vip !== 'no_price') {
                            $vip_min = ($temp_vip < $vip_min) ? $temp_vip : $vip_min;
                            $vip_max = ($temp_vip > $vip_max) ? $temp_vip : $vip_max;
                        }
                    } 
                }
            }
            
            if ($regular_min == $max_int || $regular_max == $min_int) {
                $html_regular = '<span class="td-price">Giá bán: ' . kiotViet_formatted_price(0) . '</span>';
                $html = '<div class="product-price">' . $html_regular . '</div>';
                return $html;
            }

            if ($regular_min == $regular_max) {
                $html_regular = kiotViet_formatted_price($regular_max);
            } else {
                $html_regular = kiotViet_formatted_price($regular_min) . ' - ' . kiotViet_formatted_price($regular_max);
            }

            $html = '<span class="td-price">Giá bán: ' . $html_regular . '</span>';

            if ($sale_min == $max_int || $sale_max == $min_int) {
    //            return $html;
                $html_sale = '';
                $html = $html_regular;
            } else {
                $sale_min = min(array($regular_min, $sale_min));
                if ($sale_min == $sale_max) {
                    $html_sale = kiotViet_formatted_price($sale_min);
                } else {
                    $html_sale = kiotViet_formatted_price($sale_min) . ' - ' . kiotViet_formatted_price($sale_max);
                }            
                $html = '<span class="td-price-del" style="color: #888 !important;">Giá gốc: <del>' . $html_regular . '</del></span>
                         <span class="td-price">Giá sale: ' . $html_sale . '</span>';
            }

            // Xu ly gia VIP
            if ($vip_min == $max_int || $vip_max == $min_int) {
    //            return $html;
    //            $html = $html_regular;
            } else {
                $vip_min = min(array($regular_min, $sale_min, $vip_min));
                if ($vip_min == $vip_max) {
                    $html_vip = kiotViet_formatted_price($vip_min);
                } else {
                    $html_vip = kiotViet_formatted_price($vip_min) . ' - ' . kiotViet_formatted_price($vip_max);
                }  

                if (empty($html_sale)) {
                    $html = '<span class="td-price-del" style="color: #888 !important;">Giá gốc: <del>' . $html_regular . '</del></span>
                             <span class="td-price">Giá VIP: ' . $html_vip . '</span>';
                } else {
                    $html = '<span class="td-price-del" style="color: #888 !important;">Giá gốc: <del>' . $html_regular . '</del></span>
                             <span class="td-price-del" style="color: #888 !important;">Giá sale: <del>' . $html_sale . '</del></span>
                             <span class="td-price">Giá VIP: ' . $html_vip . '</span>';
                }

                if (!is_null($YITH_Role)) {
                    $temp_vip = $YITH_Role->get_role_based_price($child);
                }
            }

        } elseif ($product && $product->is_type( 'simple' )) {
            // Simple product
            $temp_regular = $product->get_regular_price();
            if ($temp_regular === '') {
                $html = '<span class="td-price">Giá bán: ' . kiotViet_formatted_price(0) . '</span>';
                $html = '<div class="product-price">' . $html . '</div>';
                return $html;
            } else {
                $html_regular = kiotViet_formatted_price($temp_regular);
            }

            $html = '<span class="td-price">Giá bán: ' . $html_regular . '</span>';

            $temp_sale = $product->get_sale_price();
            if ($temp_sale !== '') {
                $html_sale = kiotViet_formatted_price($temp_sale);
                $html = '<span class="td-price-del" style="color: #888 !important;">Giá gốc: <del>' . $html_regular . '</del></span>
                         <span class="td-price">Giá sale: ' . $html_sale .'</span>';
            } else {
                $html_sale = '';
    //            $html = $html_regular;
                $html = '<span class="td-price">Giá gốc: ' . $html_regular . '</span>';
            }

            if (!is_null($YITH_Role)) {
                $temp_vip = $YITH_Role->get_role_based_price($product);

                if ($temp_vip !== 'no_price') {
                    $html_vip = kiotViet_formatted_price($temp_vip);
                    if (empty($html_sale)) {
                        $html = '<span class="td-price-del" style="color: #888 !important;">Giá gốc: <del>' . $html_regular . '</del></span>
                                 <span class="td-price">Giá VIP: ' . $html_vip . '</span>';
                    } else {
                        $html = '<span class="td-price-del" style="color: #888 !important;">Giá gốc: <del>' . $html_regular . '</del></span>
                                 <span class="td-price-del" style="color: #888 !important;">Giá sale: <del>' . $html_sale . '</del></span>
                                 <span class="td-price">Giá VIP: ' . $html_vip . '</span>';
                    }
                }
            }
        }

        if (empty($html)) {
            $html = '<span class="td-price">Giá bán: ' . kiotViet_formatted_price(0) . '</span>'; // TODO just for testing, Should be 0
        }

        $html = '<div class="product-price">' . $html . '</div>';
        return $html;
    }
}