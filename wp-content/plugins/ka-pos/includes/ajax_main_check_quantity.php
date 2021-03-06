<?php

require_once 'kiotviet_api.php';
require_once 'function_template.php';
require_once 'ajax_admin_button.php';

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
            } else {
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
            
            $item_id = $product_data->get_id();
            $product_sku = $product_data->get_sku();
            $product_name = mypos_get_variation_title($product_data);
            
            $store = $product_data->get_meta('_mypos_other_store', true);
            if ($store && $store == 'yes') {
                $product_sku = get_sku_store_main($product_sku);
                $kiotviet_api = new KiotViet_API(2);
            } else {
                $kiotviet_api = new KiotViet_API();
            }
            
            $max_quantity = $kiotviet_api->get_product_quantity_by_ProductSKU($product_sku, $item_id);
            
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
            
//            if ($return['current_quantity'] !== 0 && $return['current_quantity'] > $max_quantity) {
        if ($return['current_quantity'] !== 0){
                $mark_red = true;
            } else {
                $mark_red = false;
            }
            
            if ($return['status'] == 0) {   // check quantity false
                if ($max_quantity == 0) {
                    $message = '<span class="alert-message">S???n ph???m b???n ?????t ???? h???t h??ng. Mong b???n vui l??ng quay l???i sau.</span>';
                    
                    $process_id = $variation_id ? $variation_id : $product_id;
                    $return['outofstock'] = process_update_outofstock($process_id);
                    $return['deleted'] = delete_post_cache($product_id);
                } else {
//                    if ($mark_red) {
//                        $message = '<span class="alert-message"><b>' . $product_name . '</b> ch??? cho ph??p ?????t t???i ??a <b>' . $max_quantity . ' s???n ph???m</b>. <br/> B???n ???? c?? <b>' . $return['current_quantity'] . ' s???n ph???m</b> n??y trong gi??? h??ng. B???n vui l??ng c???p nh???t s??? l?????ng t???i <a href="' . wc_get_cart_url() . '" class="mypos-alert-link">Gi??? H??ng</a>.</span>';
//                    } else {
                        $message = '<span class="alert-message"><b>' . $product_name . '</b> ch??? cho ph??p ?????t t???i ??a <b>' . $max_quantity . ' s???n ph???m</b>.</span>';
//                    }
                    
                }
                $return['alert'] = kiotviet_addToCart_alert_message($message);
                
                $carts_table = build_html_table_carts($item_id, $mark_red, 'red');
                $return['popup'] = kiotviet_addToCart_alert_modal($message, $carts_table);
            } else { // $return['status'] = 1 check quantity successful
                //Check quantity is available
                if($product_type == 'product_variation'){
                    $cart_success = WC()->cart->add_to_cart($product_id,$quantity,$variation_id,$attribute_values );
                } else {
                    $cart_success = WC()->cart->add_to_cart($product_id,$quantity);
                }
                
                if ($cart_success) {
//                    $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
//                    $product_sku = $product_data->get_sku();
                    $return['status'] = 2; // Add sucessful
                    $message = '<span class="alert-success-message"><b>Th??m v??o gi??? h??ng th??nh c??ng!</b></span>';
                    $return['alert'] = kiotviet_addToCart_success_message($message);
                    
                    $carts_table = build_html_table_carts($item_id, 1, 'green');
                    $return['popup'] = kiotviet_addToCart_alert_modal($message, $carts_table);
                } else {
                    $return['status'] = 1; // Add failed
                    $message = '<span>C?? l???i trong qu?? tr??nh th??m s???n ph???m. Mong b???n refresh (F5) tr??nh duy???t v?? th??? l???i.</span>';
                    $return['alert'] = kiotviet_addToCart_alert_message($message);
                    $return['popup'] = kiotviet_addToCart_alert_modal($message, '', true);
                }
                    
            }
            
            if ($return['status'] == 2) {
                ob_start();
                woocommerce_mini_cart();
                $mini_cart = ob_get_clean();
                $return['fragments'] = apply_filters( 'woocommerce_add_to_cart_fragments', array('widget_shopping_cart_content' => '<div class="widget_shopping_cart_content">' . $mini_cart . '</div>'));
            } else {
                $return['fragments'] = '';
            } 
            
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
                    ????n h??ng c???a b???n v?????t qu?? s??? l?????ng kho h??ng c???a ch??ng t??i. B???n vui l??ng c???p nh???t s??? l?????ng t???i <a href="' . wc_get_cart_url() . '" class="alert-link">Gi??? H??ng</a>.
                </div>
                <div class="table-responsive top-buffer">          
                                <table class="table">
                                <thead class="thead-default">
                                    <tr style="white-space: nowrap;">
                                      <th>T??n S???n Ph???m</th>
                                      <th>S??? L?????ng</th>
                                      <th>T???i ??a</th>
                                </tr>
                                </thead>
                                <tbody>';
                                //<th>M?? S???n Ph???m</th>
            // Find the cart item key in the existing cart.
            $cart_items  = WC()->cart->get_cart();
            foreach ($cart_items as $item => $product) {
                $wc_product = $product['data'];
                $product_sku = $wc_product->get_sku();
                $product_name = $wc_product->get_name();
                $product_quantity = $product['quantity'];
                
                $store = $wc_product->get_meta('_mypos_other_store', true);
                if ($store && $store == 'yes') {
                    $product_sku = get_sku_store_main($product_sku);
                    $kiotviet_api = new KiotViet_API(2);
                } else {
                    $kiotviet_api = new KiotViet_API();
                }
                
                $max_quantity = $kiotviet_api->get_product_quantity_by_ProductSKU($product_sku, $wc_product->get_id());
                
                if ($product_quantity > $max_quantity) {
                    $form_submit = false;
                    $result_string .= "<tr><td>{$product_name}</td>";
//                    $result_string .= "<td>{$product_sku}</td>";
                    $result_string .= "<td><span style='color:red; font-weight: bold'>{$product_quantity}</span></td>";
                    $result_string .= "<td>{$max_quantity}</td></tr>";
                } else {
//                    $result_string .= "<td><span style='color:green; font-weight: bold'>{$product_quantity}</span></td>";
                }
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

function ja_ajax_mypos_update_cart() {
    //Form Input Values
    $cart_key 		= sanitize_text_field($_POST['cart_item_key']);
    $cart_quantity      = intval($_POST['quantity']);
    $cart_maxQuantity   = intval($_POST['max_quantity']);
    
    //If empty return error
    if(!$cart_key){
            wp_send_json(array('error' => __('Missing Cart Key!')));
    }
		
    $cart_detail = WC()->cart->get_cart_item($cart_key);
    $product_data = $cart_detail['data'];
    
    $item_id = $product_data->get_id();
    $product_sku = $product_data->get_sku();
    $product_name = mypos_get_variation_title($product_data);
    
    if ($cart_maxQuantity < 0) {
        
        $store = $product_data->get_meta('_mypos_other_store', true);
        if ($store && $store == 'yes') {
            $product_sku = get_sku_store_main($product_sku);
            $kiotviet_api = new KiotViet_API(2);
        } else {
            $kiotviet_api = new KiotViet_API();
        }
        
        $max_quantity = $kiotviet_api->get_product_quantity_by_ProductSKU($product_sku, $item_id);
    } else {
        $max_quantity = $cart_maxQuantity;
    }
    
    $return['status'] = true;
    $return['max_quantity'] = $max_quantity;
    $return['current_quantity'] = $cart_detail['quantity'];
    
    if ($cart_quantity > $max_quantity) {
        $return['status'] = false;
        $message = '<span class="alert-message"><b>' . $product_name . '</b> ch??? cho ph??p ?????t t???i ??a <b>' . $max_quantity . ' s???n ph???m</b>.</span>';
        $return['alert'] = kiotviet_UpdateCart_alert_modal($message);
    } else {
        $cart_success = WC()->cart->set_quantity($cart_key, $cart_quantity, true);
        if (!$cart_success) {
            $return['status'] = false;
            $message = '<span class="alert-message"><b>C?? l???i trong qu?? tr??nh c???p nh???t s??? l?????ng s???n ph???m. B???n vui l??ng th??? l???i.</b></span>';
            $return['alert'] = kiotviet_UpdateCart_alert_modal($message);
        } else {
            $cart_detail = WC()->cart->get_cart_item($cart_key);
            $return['new_quantity'] = $cart_quantity;
            $return['item_totalprice'] = WC()->cart->get_product_subtotal($product_data, $cart_quantity);
            
            $return['cart_subtotal'] = WC()->cart->get_cart_subtotal();
            $return['cart_total'] = WC()->cart->get_cart_total();
        }
    }
    
    
    
    wp_send_json_success( $return );
    
}

add_action( 'wp_ajax_mypos_update_cart', 'ja_ajax_mypos_update_cart' );
add_action( 'wp_ajax_nopriv_mypos_update_cart', 'ja_ajax_mypos_update_cart' );

// Load main js 
add_action( 'wp_enqueue_scripts', 'global_admin_ajax' );
function global_admin_ajax() {
    
    $pluginLoaded = false;
    //first check that woo exists to prevent fatal errors
    if ( function_exists( 'is_woocommerce' ) ) {
        
        // Style
        wp_register_style('mypos-css', WC_PLUGIN_URL . 'assets/css/mypos.css');
        wp_enqueue_style('mypos-css');
        
        // Scripts
        wp_register_script( 'mypos-singleproduct', WC_PLUGIN_URL . 'assets/js/mypos_singleproduct.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'mypos-singleproduct' );
        wp_register_script( 'mypos-ajaxcart', WC_PLUGIN_URL . 'assets/js/mypos_ajaxcart.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'mypos-ajaxcart' );
        wp_register_script( 'mypos-checkout', WC_PLUGIN_URL . 'assets/js/mypos_checkout.js', array( 'jquery' ), '1.0', true );
        wp_enqueue_script( 'mypos-checkout' );

        if (!is_product() || !get_option('mypos_add_to_cart')) {
            $pluginLoaded = true;
            wp_dequeue_script( 'mypos-singleproduct' );
        }
        
        if (!is_cart() || !get_option('mypos_ajax_cart')) {
            $pluginLoaded = true;
            wp_dequeue_script( 'mypos-ajaxcart' );
        }

        if (!is_checkout() || !get_option('mypos_checkout')) {
            $pluginLoaded = true;
            wp_dequeue_script( 'mypos-checkout' );
        }
        
        if ($pluginLoaded == false) {
            wp_dequeue_style( 'mypos-css' );
        }
        
    }
}

function my_js_variables(){
      echo '<script type="text/javascript">';
        echo 'var global = ' . json_encode( array("ajax" => admin_url("admin-ajax.php")) );
      echo '</script>';
}

add_action ( 'wp_head', 'my_js_variables' );
