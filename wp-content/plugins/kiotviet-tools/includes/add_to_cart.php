<?php

require_once 'kiotviet_api.php';
require_once 'function.php';

function ja_ajax_check_quantity_cart(){

        try {
            //Form Input Values
            $item_id 		= intval($_POST['item_id']);
            $quantity 		= intval($_POST['quantity']);

            //If empty return error
            if(!$item_id){
                    wp_send_json(array('error' => __('Something went wrong','xoo-wsc')));
            }

            //Check product type
            $product_type = get_post_type($item_id);

            if($product_type == 'product_variation'){
                    $product_id = wp_get_post_parent_id($item_id);
                    $variation_id = $item_id;
                    $attribute_values = wc_get_product_variation_attributes($variation_id);
//                    $cart_success = WC()->cart->add_to_cart($product_id,$quantity,$variation_id,$attribute_values );
            }
            else{
                    $attribute_values = array();
                    $product_id = $item_id;
//                    $cart_success = WC()->cart->add_to_cart($product_id,$quantity);
            }
            
            // From Class-wc-cart.php
            $product_id   = absint( $product_id );
            $variation_id = absint( $variation_id );

//            // Ensure we don't add a variation to the cart directly by variation ID.
//            if ( 'product_variation' === get_post_type( $product_id ) ) {
//                    $variation_id = $product_id;
//                    $product_id   = wp_get_post_parent_id( $variation_id );
//            }

            $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
            
            $quantity     = apply_filters( 'woocommerce_add_to_cart_quantity', $quantity, $product_id );

            if ( $quantity <= 0 || ! $product_data || 'trash' === $product_data->get_status() ) {
                    return false;
            }

            // Load cart item data - may be added by other plugins.
            $cart_item_data = (array) apply_filters( 'woocommerce_add_cart_item_data', $cart_item_data, $product_id, $variation_id );

            // Generate a ID based on product ID, variation ID, variation data, and other cart item data.
            $cart_id        = WC()->cart->generate_cart_id( $product_id, $variation_id, $attribute_values, $cart_item_data );

            // Find the cart item key in the existing cart.
            $cart_item_key  = WC()->cart->find_product_in_cart( $cart_id );
            
            
            $product_sku = $product_data->get_sku();
            
            $kiotviet_api = new KiotViet_API();
            $max_quantity = $kiotviet_api->get_product_quantity_by_ProductSKU($product_sku);
            
            $return['status'] = 0;
            $return['sku'] = $product_sku;
            $return['max_quantity'] = $max_quantity;
            $return['request_quantity'] = $quantity;
            $return['current_quantity'] = 0;
            $return['new_quantity'] = 0;
            
            if ( $cart_item_key ) {
                $products_qty_in_cart = WC()->cart->get_cart_item_quantities();
                $new_quantity = $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ] + $quantity;
                
                $return['current_quantity'] = $products_qty_in_cart[ $product_data->get_stock_managed_by_id() ];
            } else {
                $new_quantity = $quantity;
            }
            
            $return['new_quantity'] = $new_quantity;
            
            if ($new_quantity <= $max_quantity) {
                $return['status'] = 1;
            }
            
//            if ($return['status'] == 0) {
//                if ($max_quantity == 0) {
//                    $message = '<span style="font-weight:bold;">Sản phẩm bạn đặt đã hết hàng. Mong bạn vui lòng quay lại sau.</span>';
//                } else {
//                    $message = '<span>Số lượng bạn đặt đã quá giới hạn kho hàng. Tối đa: </span><span style="font-weight: bold;">' . $new_quantity . '/' . $max_quantity . '</span>';
//                }
//                $return['alert'] = kiotviet_addToCart_alert_modal($message);
//                $return['popup'] = kiotviet_addToCart_alert_modal($message);
//            }
            
            wp_send_json_success( $return );
            
        } catch ( Exception $e ) {
                if ( $e->getMessage() ) {
                        wc_add_notice( $e->getMessage(), 'error' );
                }
                wp_send_json_error($e);
                return false;
        }
    }

add_action( 'wp_ajax_check_quantity_cart', 'ja_ajax_check_quantity_cart' );
add_action( 'wp_ajax_nopriv_check_quantity_cart', 'ja_ajax_check_quantity_cart' );



function ja_ajax_check_quantity_checkout(){

        try {
            $form_submit = true;
            $result_string = '
                <div class="alert alert-danger">
                    Đơn hàng của bạn vượt quá số lượng kho hàng của chúng tôi. Bạn vui lòng cập nhật số lượng tại <a href="' . wc_get_cart_url() . '" class="alert-link">Giỏ Hàng</a>.
                </div>
                <div class="table-responsive">          
                                <table class="table">
                                <thead class="thead-default">
                                    <tr>
                                      <th>Tên Sản Phẩm</th>
                                      <th>Mã Sản Phẩm</th>
                                      <th>Số Lượng</th>
                                      <th>Tối Đa</th>
                                </tr>
                                </thead>
                                <tbody>';
            // Find the cart item key in the existing cart.
            $cart_items  = WC()->cart->get_cart();
            foreach ($cart_items as $item => $product) {
                $wc_product = $product['data'];
                $product_sku = $wc_product->get_sku();
                $product_name = $wc_product->get_name();
                $product_quantity = $product['quantity'];
                
                $kiotviet_api = new KiotViet_API();
                $max_quantity = $kiotviet_api->get_product_quantity_by_ProductSKU($product_sku);
                
                $result_string .= "<tr><td>{$product_name}</td>";
                $result_string .= "<td>{$product_sku}</td>";
                    
                if ($product_quantity > $max_quantity) {
                    $form_submit = false;
                    $result_string .= "<td><span style='color:red; font-weight: bold'>{$product_quantity}</span></td>";
                } else {
                    $result_string .= "<td><span style='color:green; font-weight: bold'>{$product_quantity}</span></td>";
                }
                
                $result_string .= "<td>{$max_quantity}</td></tr>";
            }
            
            $result_string .= '</tbody></table></div>';
            
            $return['status'] = $form_submit;
            $return['message'] = kiotviet_checkout_alert_modal($result_string);
            
            wp_send_json_success( $return );
            
        } catch ( Exception $e ) {
                if ( $e->getMessage() ) {
                        wc_add_notice( $e->getMessage(), 'error' );
                }
                wp_send_json_error($e);
                return false;
        }
    }

add_action( 'wp_ajax_check_quantity_checkout', 'ja_ajax_check_quantity_checkout' );
add_action( 'wp_ajax_nopriv_check_quantity_checkout', 'ja_ajax_check_quantity_checkout' );