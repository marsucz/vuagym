<?php

require_once('KaCus_DbModel.php');

// Custom Default Variation

if(!function_exists('tuandev_process_default_product_variation')){
    function tuandev_process_default_product_variation() {
    
        global $product;
        
        if ( $product && $product->is_type( 'variable' )) {
            
            $dbModel = new KaCus_DbModel();
            
            $children_ids = $dbModel->get_children_ids($product->get_id());
            $children_string = implode(',', $children_ids);
            
            $stock_status_list = $dbModel->get_stock_status_by_children_list($children_string);
            $all_out_of_stock = true;
            foreach ($stock_status_list as $child_stock_status) {
                if ($child_stock_status == 'instock') {
                    $all_out_of_stock = false;
                    break;
                }
            }
            // Nếu tất cả các biến thể đều hết hàng thì hiện tất cả các biến thể tránh trường hợp hiển thị giá 0đ
            if ($all_out_of_stock) {
                $dbModel->update_status_variations_by_list($children_string, 'publish');
                return; // Không xử lý gì thêm
            }
            
            // Lấy danh sách ẩn và hiện các biển thế
            // Còn hàng thì hiện, hết hàng thì ẩn
            $update_hide = array();
            $update_show = array();
            foreach ($stock_status_list as $key => $child_stock_status) {
                if ($child_stock_status == 'instock') {
                    $update_show[] = $key;  // variation_id
                } elseif($child_stock_status == 'outofstock') { // Chuẩn bị cho trạng thái "Chờ hàng"
                    $update_hide[] = $key;  // variation_id
                }
            }
            // Hiện các biến thể còn hàng
            if (!empty($update_show)) {
                $show_string = implode(',', $update_show);
                $dbModel->update_status_variations_by_list($show_string, 'publish');
            }
            // Ẩn các biến thể hết hàng
            if (!empty($update_hide)) {
                $hide_string = implode(',', $update_hide);
                $dbModel->update_status_variations_by_list($hide_string, 'private');
            }
            
            
            // Lấy biến thể mặc định và kiểm tra có cần thay đổi hay không
            $change_variation = false;
            $default_variation_id = tuandev_get_default_product_variation($product);
            if (!$default_variation_id) {
                $change_variation = true;
            } else {
//                $stock_status = get_post_meta($default_variation_id, '_stock_status', true);
                $stock_status = $stock_status_list[$default_variation_id];
                $pre_order = get_post_meta($default_variation_id, '_ywpo_preorder', true);
                if ($stock_status == 'outofstock' || $pre_order == 'yes') {
                    $change_variation = true;
                }
            }
            
            // Thay đổi biến thể mặc định
            if ($change_variation) {
                $temp_default_attributes = null;
                foreach ($update_show as $child_id) {
                    $child = wc_get_product($child_id);
                    $pre_order = get_post_meta($child_id, '_ywpo_preorder', true);
                    if ($pre_order == 'yes') {
                        $temp_default_attributes = $child->get_attributes();
                    } else {
                        // Get the attributes of the product has instock
                        $new_default_attributes = $child->get_attributes();
                        // Update the new attributes to parent product
                        $product->set_default_attributes($new_default_attributes);
                        $product->save();
                        $updated_default = true;
                        break;
                    }
                }

                if (!$updated_default && $temp_default_attributes) {
                    $product->set_default_attributes($temp_default_attributes);
                    $product->save();
                }
            }
        }
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


