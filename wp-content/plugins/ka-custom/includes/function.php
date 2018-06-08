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
                $pre_order = get_post_meta($default_variation_id, '_ywpo_preorder', true);
                
                if ($default_variation->get_stock_status() == 'outofstock' || $pre_order == 'yes') {
                    $change_variation = true;
                }
            }

            $variations = $product->get_children();
            $temp_default_attributes = null;
            
            foreach ($variations as $child_id) {
                if ( $child_id ) {
                    $child = wc_get_product($child_id);
                    //Fix 3: An cac bien the da het hang
                    if ($child->get_stock_status() == 'outofstock' || !$child->get_regular_price()) {
                        if ($child->get_status() == 'publish') {
                            $child->set_status('private');
                            $child->save();
                        }
                    } else {
                        if ($change_variation && !$updated_default) {
                            $pre_order = get_post_meta($child_id, '_ywpo_preorder', true);
                            if ($pre_order == 'yes') {
                                // Get the attributes of the product has instock
                                $temp_default_attributes = $child->get_attributes();
                            } else {
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
            }
            
            if (!$updated_default && $temp_default_attributes) {
                $product->set_default_attributes($temp_default_attributes);
                $product->save();
                $updated_default = true;
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


