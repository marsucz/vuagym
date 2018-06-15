<?php

/*Unsed


// Custom get Min Price to show on catolog: NOT USE
if(!function_exists('tuandev_process_get_min_price_html')){
    function tuandev_process_get_min_price_html($product) {
        
        if ( $product && $product->is_type( 'variable' )) {
            $html = tuandev_get_min_price_html($product);
        } else {
            $html = $product->get_price_html();
        }
        return $html;
    }
}

if(!function_exists('tuandev_get_min_price_html')){
    function tuandev_get_min_price_html($product) {
        
        $max_int = 999999999;
        $min_int = -999999999;

        $regular_min = $max_int;
        $regular_max = $min_int;
        $sale_min = $max_int;
        $sale_max = $min_int;
        $vip_min = $max_int;
        $vip_max = $min_int;

        $regular_price_txt = get_option( 'ywcrbp_regular_price_txt' );
        if ($regular_price_txt) $regular_price_txt .= " ";
        $sale_price_txt = get_option( 'ywcrbp_sale_price_txt' );
        if ($sale_price_txt) $sale_price_txt .= " ";
        $your_price_txt = get_option( 'ywcrbp_your_price_txt' );
        if ($your_price_txt) $your_price_txt .= " ";
        
        if (class_exists('YITH_Role_Based_Prices_Product')) {
            $YITH_Role = YITH_Role_Based_Prices_Product();
        } else {
            $YITH_Role = null;
        }
        
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
//                $html_regular = '<span class="td-price">' . $regular_price_txt . kiotViet_formatted_price(0) . '</span>';
//                $html = '<div class="product-price">' . $html_regular . '</div>';
                $html = '';
                return $html;
            }

            if ($regular_min == $regular_max) {
                $html_regular = kiotViet_formatted_price($regular_max);
            } else {
//                $html_regular = kiotViet_formatted_price($regular_min) . ' - ' . kiotViet_formatted_price($regular_max);
                $html_regular = kiotViet_formatted_price($regular_min);
            }

            $html = '<span class="td-price">' . $html_regular . '</span>';

            if ($sale_min == $max_int || $sale_max == $min_int) {
    //            return $html;
                $html_sale = '';
                $html = $html_regular;
            } else {
//                $sale_min = min(array($regular_min, $sale_min));
                if ($sale_min == $sale_max) {
                    $html_sale = kiotViet_formatted_price($sale_min);
                } else {
                    $html_sale = kiotViet_formatted_price($sale_min);
//                    $html_sale = kiotViet_formatted_price($sale_min) . ' - ' . kiotViet_formatted_price($sale_max);
                }            
                $html = '<span class="td-price-del" style="color: #888 !important;">' . $regular_price_txt . '<del>từ ' . $html_regular . '</del></span>
                         <span class="td-price">' . $sale_price_txt . 'từ ' . $html_sale . '</span>';
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
//                    $html_vip = kiotViet_formatted_price($vip_min) . ' - ' . kiotViet_formatted_price($vip_max);
                    $html_vip = kiotViet_formatted_price($vip_min);
                }  

                if (empty($html_sale)) {
                    $html = '<span class="td-price-del" style="color: #888 !important;">' . $regular_price_txt . '<del>từ ' . $html_regular . '</del></span>
                             <span class="td-price">' . $your_price_txt . 'từ ' . $html_vip . '</span>';
                } else {
                    $html = '<span class="td-price-del" style="color: #888 !important;">' . $regular_price_txt . '<del>từ ' . $html_regular . '</del></span>
                             <span class="td-price-del" style="color: #888 !important;">' . $sale_price_txt . '<del>từ ' . $html_sale . '</del></span>
                             <span class="td-price">' . $your_price_txt . 'từ ' . $html_vip . '</span>';
                }

                if (!is_null($YITH_Role)) {
                    $temp_vip = $YITH_Role->get_role_based_price($child);
                }
            }

        } elseif ($product && $product->is_type( 'simple' )) {
            // Simple product
            $temp_regular = $product->get_regular_price();
            if ($temp_regular === '') {
//                $html = '<span class="td-price">' . kiotViet_formatted_price(0) . '</span>';
//                $html = '<div class="product-price">' . $html . '</div>';
                $html = '';
                return $html;
            } else {
                $html_regular = kiotViet_formatted_price($temp_regular);
            }

            $html = '<span class="td-price">' . $html_regular . '</span>';

            $temp_sale = $product->get_sale_price();
            if ($temp_sale !== '') {
                $html_sale = kiotViet_formatted_price($temp_sale);
                $html = '<span class="td-price-del" style="color: #888 !important;">' . $regular_price_txt . '<del>' . $html_regular . '</del></span>
                         <span class="td-price">' . $sale_price_txt . $html_sale .'</span>';
            } else {
                $html_sale = '';
    //            $html = $html_regular;
                $html = '<span class="td-price">' . $regular_price_txt . $html_regular . '</span>';
            }

            if (!is_null($YITH_Role)) {
                $temp_vip = $YITH_Role->get_role_based_price($product);

                if ($temp_vip !== 'no_price') {
                    $html_vip = kiotViet_formatted_price($temp_vip);
                    if (empty($html_sale)) {
                        $html = '<span class="td-price-del" style="color: #888 !important;">' . $regular_price_txt . '<del>' . $html_regular . '</del></span>
                                 <span class="td-price">' . $your_price_txt . $html_vip . '</span>';
                    } else {
                        $html = '<span class="td-price-del" style="color: #888 !important;">' . $regular_price_txt . '<del>' . $html_regular . '</del></span>
                                 <span class="td-price-del" style="color: #888 !important;">' . $sale_price_txt . '<del>' . $html_sale . '</del></span>
                                 <span class="td-price">' . $your_price_txt . $html_vip . '</span>';
                    }
                }
            }
        }

//        if (empty($html)) {
//            $html = '<span class="td-price">' . kiotViet_formatted_price(0) . '</span>'; // TODO just for testing, Should be 0
//            $html = '';
//        }
        if (!empty($html)) {
            $html = '<div class="product-price">' . $html . '</div>';
        } else {
            $html = '';
        }
        
        return $html;
    }
}


Unsed */

/* END Custom price on category page */