<?php

function get_product_info($theid) {
    
    $product = wc_get_product($theid);
    $new_product = array();
    // its a variable product
    if( get_post_type() == 'product_variation' ){
            $new_product['id'] = $theid;
            $new_product['sku'] = $product->get_sku();
            $new_product['title'] = $product->get_title();
            $new_product['name'] = $product->get_name();
            $new_product['price'] = $product->get_price();
            $new_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
            $new_product['quantity'] = $product->get_stock_quantity();

    // its a simple product
    } else {
        //Product is a main of variations
        if ($product->has_child()) {
            // skip this
        } else {
            $new_product['id'] = $theid;
            $new_product['sku'] = $product->get_sku();
            $new_product['title'] = $product->get_title();
            $new_product['name'] = $product->get_name();
            $new_product['price'] = $product->get_price();
            $new_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
            $new_product['quantity'] = $product->get_stock_quantity();
        }

    }
    
    return $new_product;
}

function get_woocommerce_product_list() {
	$full_product_list = array();
	$loop = new WP_Query( array( 'post_type' => array('product', 'product_variation'), 'posts_per_page' => -1 ) );
 
	while ( $loop->have_posts() ) : $loop->the_post();
                
                $new_product = array();
		$theid = get_the_ID();
                
                $product = get_product_info($theid);

        // add product to array but don't add the parent of product variations
        if (!empty($product)) {
            $full_product_list[] = $new_product;
        }
        
    endwhile; 
    wp_reset_query();

    return $full_product_list;
}


function manual_sync_kiotviet() {
    
    //ini_set('memory_limit', '-1');
    set_time_limit(3600);
    
    $dbModel = new DbModel();
    $api = new KiotViet_API();
    
        echo '<div class="row"> 
            <div class="col-lg-12">';
        echo '<div class="panel panel-default">
                        <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i>
                            Manual Sync: Danh s??ch S???n Ph???m L???i
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                <div class="row">
                            
                            </div></div>
                            <div class="row">
                            <div class="col-sm-12">
                            <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                               <thead>
                                <tr role="row">
                                   <th class="sorting_desc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;" aria-sort="descending" >STT</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">C???a h??ng (KiotViet)</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Web (Wordpress)</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">T??y Ch???n</th>
                                </tr>
                             </thead>
                                <tbody>';
    
    $count = 0;
    
    $woo_all_products = get_woocommerce_product_list();
    $kiotviet_all_products = $api->get_all_products();
    
    $matched_products = array();
    
    foreach ($woo_all_products as $woo_key => $woo_single) {
        $match = false;
        $temp_prd = array();
        foreach ($kiotviet_all_products as $kv_key => $kv_single) {
            if ($kv_single['sku'] == $woo_single['sku']) {
                $match = true;
                $temp_prd['kv'] = $kiotviet_all_products[$kv_key];
                unset($kiotviet_all_products[$kv_key]);
                break;
            }
        }
        
        if ($match) {
            $temp_prd['woo'] = $woo_all_products[$woo_key];
            unset($woo_all_products[$woo_key]);
            $matched_products[] = $temp_prd;
        }
    }
    
    foreach($matched_products as $product) {
        
//             Skip if have nothing to change / everything is ok
            if (($product['kv']['stock'] == $product['woo']['stock']) 
                && ($product['kv']['price'] == $product['woo']['price'])) {
                continue;
            }   
        
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['kv']['stock']) {
                $kv_stock_status = "C??n h??ng";
            } else {
                $kv_stock_status = "H???t h??ng";
            }
            
            echo "<td>{$product['kv']['name']}-M??:<b>{$product['kv']['sku']}</b>-TT:{$kv_stock_status}-SL:{$product['kv']['quantity']}-Gi??:{$product['kv']['price']}</td>";
            
            if ($product['woo']['stock']) {
                $woo_stock_status = "C??n h??ng";
            } else {
                $woo_stock_status = "H???t h??ng";
            }
            
            $edit_link = get_permalink($product['woo']['id']);
            echo "<td><a href='{$edit_link}'>{$product['woo']['name']}-M??:<b>{$product['woo']['sku']}</b>-TT:{$woo_stock_status}-SL:{$product['woo']['quantity']}-Gi??:{$product['woo']['price']}</a></td>";
            echo '<td>';
            
            if ($product['kv']['stock'] && !$product['woo']['stock']) {
                echo '  <button id="updateInStock_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-success" title="C???p nh???t c?? h??ng tr??n Web cho s???n ph???m n??y" onclick="updateInStock('. $product['woo']['id'] .');"><i class="fa fa-tasks"></i>  C???p nh???t c?? h??ng</button>';
            }
            
            if (!$product['kv']['stock'] && $product['woo']['stock']) {
                echo '  <button id="updateOutOfStock_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-danger" title="C???p nh???t h???t h??ng tr??n Web cho s???n ph???m n??y" onclick="updateOutOfStock('. $product['woo']['id'] .');"><i class="fa fa-tasks"></i>  C???p nh???t h???t h??ng</button>';
            }
            
            if ($product['kv']['price'] != $product['woo']['price']) {
                echo '  <button id="updateWebPrice_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-info" title="C???p nh???t gi?? tr??n Web cho s???n ph???m n??y theo gi?? tr??n KiotViet" onclick="updateWebPrice_byKVPrice('. $product['woo']['id'] .',' . $product['kv']['price'] . ');"><i class="fa fa-anchor"></i>  C???p nh???t gi?? Web theo KiotViet</button>';
                echo '  <button id="updateKVPrice_' . $product['kv']['id'] . '" type="button" class="btn btn-mypos btn-warning" title="C???p nh???t gi?? tr??n KiotViet cho s???n ph???m n??y theo gi?? tr??n Web" onclick="updateKVPrice_byWebPrice('. $product['kv']['id'] .',' . $product['woo']['price'] . ');"><i class="fa fa-anchor"></i>  C???p nh???t gi?? KiotViet theo Web</button>';
            }
            
            
            echo '</td>';
            echo '</tr>';
    }
    
    foreach($kiotviet_all_products as $product) {
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['stock']) {
                $kv_stock_status = "C??n h??ng";
            } else {
                $kv_stock_status = "H???t h??ng";
            }
            
            echo "<td>ID:{$product['id']}-{$product['name']}-M??:<b>{$product['sku']}</b>-TT:{$kv_stock_status}-SL:{$product['quantity']}-Gi??:{$product['price']}</td>";
            echo "<td>Kh??ng c?? s???n ph???m</td>";
            if (empty($product['sku'])) {
                echo '<td>Kh??ng c?? M?? SP</td>';
            } else {
                echo '<td>Kh??ng t???n t???i SP tr??n Web</td>';
            }
            echo '</tr>';
    }
    
    foreach($woo_all_products as $product) {
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            echo "<td>Kh??ng c?? s???n ph???m</td>";
            
            if ($product['stock']) {
                $woo_stock_status = "C??n h??ng";
            } else {
                $woo_stock_status = "H???t h??ng";
            }

            echo "<td>{$product['name']}-M??:<b>{$product['sku']}</b>-TT:{$woo_stock_status}-SL:{$product['quantity']}-Gi??:{$product['price']}</td>";
            if (empty($product['sku'])) {
                echo '<td>Kh??ng c?? M?? SP</td>';
            } else {
                echo '<td>Kh??ng t???n t???i SP tr??n KiotViet</td>';
            }
            echo '</tr>';
    }
    
    
                    echo '</tbody>
                            </table></div></div>
                            <!-- <div class="row"><div class="col-sm-6"><div class="dataTables_info" id="dataTables-example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div><div class="col-sm-6"><div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate"><ul class="pagination"><li class="paginate_button previous disabled" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_previous"><a href="#">Previous</a></li><li class="paginate_button active" aria-controls="dataTables-example" tabindex="0"><a href="#">1</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">2</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">3</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">4</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">5</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">6</a></li><li class="paginate_button next" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_next"><a href="#">Next</a></li></ul></div></div></div></div> --> 
                            <!-- /.table-responsive -->
                            
                        </div>
                        <!-- /.panel-body -->
                    </div>';
    
    echo '</div></div></div>';
}

function manual_sync_web() {
    
    //ini_set('memory_limit', '-1');
    set_time_limit(3600);
    
    $dbModel = new DbModel();
    $api = new KiotViet_API();
    
        echo '<div class="row"> 
            <div class="col-lg-12">';
        echo '<div class="panel panel-default">
                        <div class="panel-heading">
                        <i class="fa fa-bar-chart-o fa-fw"></i>
                            Manual Sync: Danh s??ch S???n Ph???m L???i
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div id="dataTables-example_wrapper" class="dataTables_wrapper form-inline dt-bootstrap no-footer">
                                <div class="row">
                            
                            </div></div>
                            <div class="row">
                            <div class="col-sm-12">
                            <table width="100%" class="table table-striped table-bordered table-hover dataTable no-footer dtr-inline" id="dataTables-example" role="grid" aria-describedby="dataTables-example_info" style="width: 100%;">
                               <thead>
                                <tr role="row">
                                   <th class="sorting_desc" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;" aria-sort="descending" >STT</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">C???a h??ng (KiotViet)</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">Web (Wordpress)</th>
                                   <th class="sorting" tabindex="0" aria-controls="dataTables-example" rowspan="1" colspan="1" style="width: 5px;">T??y Ch???n</th>
                                </tr>
                             </thead>
                                <tbody>';
    
    $count = 0;
    
    
    
    
//    $woo_all_products = get_woocommerce_product_list();
//    $kiotviet_all_products = $api->get_all_products();
//    
//    $matched_products = array();
//    
//    foreach ($woo_all_products as $woo_key => $woo_single) {
//        $match = false;
//        $temp_prd = array();
//        foreach ($kiotviet_all_products as $kv_key => $kv_single) {
//            if ($kv_single['sku'] == $woo_single['sku']) {
//                $match = true;
//                $temp_prd['kv'] = $kiotviet_all_products[$kv_key];
//                unset($kiotviet_all_products[$kv_key]);
//                break;
//            }
//        }
//        
//        if ($match) {
//            $temp_prd['woo'] = $woo_all_products[$woo_key];
//            unset($woo_all_products[$woo_key]);
//            $matched_products[] = $temp_prd;
//        }
//    }
    
    $loop = new WP_Query( array( 'post_type' => array('product', 'product_variation'), 'posts_per_page' => -1 ) );
    
    // start query
    while ( $loop->have_posts() ) : $loop->the_post();

            $new_product = array();
            $theid = get_the_ID();

            $product = get_product_info($theid);

            // add product to array but don't add the parent of product variations
            if (!empty($product)) {
                // process
            }
    
    // end query
    endwhile; 
    wp_reset_query();
    
    foreach($matched_products as $product) {
        
//             Skip if have nothing to change / everything is ok
            if (($product['kv']['stock'] == $product['woo']['stock']) 
                && ($product['kv']['price'] == $product['woo']['price'])) {
                continue;
            }   
        
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['kv']['stock']) {
                $kv_stock_status = "C??n h??ng";
            } else {
                $kv_stock_status = "H???t h??ng";
            }
            
            echo "<td>{$product['kv']['name']}-M??:<b>{$product['kv']['sku']}</b>-TT:{$kv_stock_status}-SL:{$product['kv']['quantity']}-Gi??:{$product['kv']['price']}</td>";
            
            if ($product['woo']['stock']) {
                $woo_stock_status = "C??n h??ng";
            } else {
                $woo_stock_status = "H???t h??ng";
            }
            
            $edit_link = get_permalink($product['woo']['id']);
            echo "<td><a href='{$edit_link}'>{$product['woo']['name']}-M??:<b>{$product['woo']['sku']}</b>-TT:{$woo_stock_status}-SL:{$product['woo']['quantity']}-Gi??:{$product['woo']['price']}</a></td>";
            echo '<td>';
            
            if ($product['kv']['stock'] && !$product['woo']['stock']) {
                echo '  <button id="updateInStock_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-success" title="C???p nh???t c?? h??ng tr??n Web cho s???n ph???m n??y" onclick="updateInStock('. $product['woo']['id'] .');"><i class="fa fa-tasks"></i>  C???p nh???t c?? h??ng</button>';
            }
            
            if (!$product['kv']['stock'] && $product['woo']['stock']) {
                echo '  <button id="updateOutOfStock_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-danger" title="C???p nh???t h???t h??ng tr??n Web cho s???n ph???m n??y" onclick="updateOutOfStock('. $product['woo']['id'] .');"><i class="fa fa-tasks"></i>  C???p nh???t h???t h??ng</button>';
            }
            
            if ($product['kv']['price'] != $product['woo']['price']) {
                echo '  <button id="updateWebPrice_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-info" title="C???p nh???t gi?? tr??n Web cho s???n ph???m n??y theo gi?? tr??n KiotViet" onclick="updateWebPrice_byKVPrice('. $product['woo']['id'] .',' . $product['kv']['price'] . ');"><i class="fa fa-anchor"></i>  C???p nh???t gi?? Web theo KiotViet</button>';
                echo '  <button id="updateKVPrice_' . $product['kv']['id'] . '" type="button" class="btn btn-mypos btn-warning" title="C???p nh???t gi?? tr??n KiotViet cho s???n ph???m n??y theo gi?? tr??n Web" onclick="updateKVPrice_byWebPrice('. $product['kv']['id'] .',' . $product['woo']['price'] . ');"><i class="fa fa-anchor"></i>  C???p nh???t gi?? KiotViet theo Web</button>';
            }
            
            
            echo '</td>';
            echo '</tr>';
    }
    
    foreach($kiotviet_all_products as $product) {
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['stock']) {
                $kv_stock_status = "C??n h??ng";
            } else {
                $kv_stock_status = "H???t h??ng";
            }
            
            echo "<td>ID:{$product['id']}-{$product['name']}-M??:<b>{$product['sku']}</b>-TT:{$kv_stock_status}-SL:{$product['quantity']}-Gi??:{$product['price']}</td>";
            echo "<td>Kh??ng c?? s???n ph???m</td>";
            if (empty($product['sku'])) {
                echo '<td>Kh??ng c?? M?? SP</td>';
            } else {
                echo '<td>Kh??ng t???n t???i SP tr??n Web</td>';
            }
            echo '</tr>';
    }
    
    foreach($woo_all_products as $product) {
            $count++;
            echo '<tr role="row" row_id="'. $count .'">';
            echo '<td class="sorting_1">' . $count . '</td>';
            echo "<td>Kh??ng c?? s???n ph???m</td>";
            
            if ($product['stock']) {
                $woo_stock_status = "C??n h??ng";
            } else {
                $woo_stock_status = "H???t h??ng";
            }

            echo "<td>{$product['name']}-M??:<b>{$product['sku']}</b>-TT:{$woo_stock_status}-SL:{$product['quantity']}-Gi??:{$product['price']}</td>";
            if (empty($product['sku'])) {
                echo '<td>Kh??ng c?? M?? SP</td>';
            } else {
                echo '<td>Kh??ng t???n t???i SP tr??n KiotViet</td>';
            }
            echo '</tr>';
    }
    
    
                    echo '</tbody>
                            </table></div></div>
                            <!-- <div class="row"><div class="col-sm-6"><div class="dataTables_info" id="dataTables-example_info" role="status" aria-live="polite">Showing 1 to 10 of 57 entries</div></div><div class="col-sm-6"><div class="dataTables_paginate paging_simple_numbers" id="dataTables-example_paginate"><ul class="pagination"><li class="paginate_button previous disabled" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_previous"><a href="#">Previous</a></li><li class="paginate_button active" aria-controls="dataTables-example" tabindex="0"><a href="#">1</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">2</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">3</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">4</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">5</a></li><li class="paginate_button " aria-controls="dataTables-example" tabindex="0"><a href="#">6</a></li><li class="paginate_button next" aria-controls="dataTables-example" tabindex="0" id="dataTables-example_next"><a href="#">Next</a></li></ul></div></div></div></div> --> 
                            <!-- /.table-responsive -->
                            
                        </div>
                        <!-- /.panel-body -->
                    </div>';
    
    echo '</div></div></div>';
}


function ja_ajax_mypos_add_to_cart(){
		
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
                $cart_success = WC()->cart->add_to_cart($product_id,$quantity,$variation_id,$attribute_values );
        }
        else{
                $product_id = $item_id;
                $cart_success = WC()->cart->add_to_cart($product_id,$quantity);
        }
        
        
        $cart_item_key = $cart_success;
        //Successfully added to cart.
        if($cart_success){  // is $cart_item_key
            $product_data = wc_get_product( $variation_id ? $variation_id : $product_id );
            $product_sku = $product_data->get_sku();
            
        }
        else{
                if(wc_notice_count('error') > 0){
                echo wc_print_notices();
                }
        }
        die();
}

add_action( 'wp_ajax_mypos_add_to_cart', 'ja_ajax_mypos_add_to_cart' );
add_action( 'wp_ajax_nopriv_mypos_add_to_cart', 'ja_ajax_mypos_add_to_cart' );

function auto_sync_kiotviet() {
    
    //ini_set('memory_limit', '-1');
    set_time_limit(3600);
    
    $file_name = 'KiotViet_autosync_' . date("d-m-Y_H-i-s") . '.csv';
    
    
    $file_path = WC_PLUGIN_DIR . 'logs/' . $file_name;
    $file_url = plugin_dir_url(__FILE__) . 'logs/' . $file_name;
    
    $file = fopen($file_path, "w");
    
    $title[0] = 'STT';
    $title[1] = 'KiotViet';
    $title[2] = 'Web';
    $title[3] = 'KiotViet c??n h??ng nh??ng Web h???t h??ng';
    $title[4] = 'KiotViet h???t h??ng nh??ng Web c??n h??ng';
    $title[5] = 'KiotViet c?? SP n??y nh??ng Web kh??ng c??';
    $title[6] = 'Web c?? SP n??y nh??ng KiotViet kh??ng c??';
    $title[7] = 'Exception';
    
    $title = array_map("utf8_decode", $title);
    fputcsv($file, $title);
    
    $dbModel = new DbModel();
    $api = new KiotViet_API();
    
    $count = 0;
    
    $woo_all_products = get_woocommerce_product_list();
    $kiotviet_all_products = $api->get_all_products();
    
    $matched_products = array();
    
    foreach ($woo_all_products as $woo_key => $woo_single) {
        $match = false;
        $temp_prd = array();
        foreach ($kiotviet_all_products as $kv_key => $kv_single) {
            if ($kv_single['sku'] == $woo_single['sku']) {
                $match = true;
                $temp_prd['kv'] = $kiotviet_all_products[$kv_key];
                unset($kiotviet_all_products[$kv_key]);
                break;
            }
        }
        
        if ($match) {
            $temp_prd['woo'] = $woo_all_products[$woo_key];
            unset($woo_all_products[$woo_key]);
            $matched_products[] = $temp_prd;
        }
    }
    
    foreach($matched_products as $product) {
        
//             Skip if have nothing to change / everything is ok
            if ($product['kv']['stock'] == $product['woo']['stock']) {
                continue;
            }
            
            $line_product = array();
            
            
            $count++;
            $line_product[0] = $count;
//            echo '<tr role="row" row_id="'. $count .'">';
//            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['kv']['stock']) {
                $kv_stock_status = "C??n h??ng";
            } else {
                $kv_stock_status = "H???t h??ng";
            }
            
            $line_product[1] = "{$product['kv']['name']}-M??:{$product['kv']['sku']}-TT:{$kv_stock_status}-SL:{$product['kv']['quantity']}-Gi??:{$product['kv']['price']}";
            
            if ($product['woo']['stock']) {
                $woo_stock_status = "C??n h??ng";
            } else {
                $woo_stock_status = "H???t h??ng";
            }
            
            $edit_link = get_permalink($product['woo']['id']);
            $line_product[2] = "{$product['woo']['name']}-M??:{$product['woo']['sku']}-TT:{$woo_stock_status}-SL:{$product['woo']['quantity']}-Gi??:{$product['woo']['price']}";
//            echo '<td>';
            
            if ($product['kv']['stock'] && !$product['woo']['stock']) {
                
                $product_id = $product['woo']['id'];
                $product_temp = wc_get_product($product_id);
                $product_temp->set_stock_status('instock');
                $product_temp->save();

                $pre_order = new YITH_Pre_Order_Product( $product_id );

                if ( 'yes' == $pre_order->get_pre_order_status() ) {
                    $pre_order->set_pre_order_status('no');
                }
                
                $line_product[3] = 'Done';
//                echo '  <button id="updateInStock_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-success" title="C???p nh???t c?? h??ng tr??n Web cho s???n ph???m n??y" onclick="updateInStock('. $product['woo']['id'] .');"><i class="fa fa-tasks"></i>  C???p nh???t c?? h??ng</button>';
            } else {
                $line_product[3] = '';
            }
            
            if (!$product['kv']['stock'] && $product['woo']['stock']) {
                $product_id = $product['woo']['id'];
                $product_temp = wc_get_product($product_id);
                $product_temp->set_stock_status('outofstock');
                $product_temp->save();
                $line_product[4] = 'Done';
//                echo '  <button id="updateOutOfStock_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-danger" title="C???p nh???t h???t h??ng tr??n Web cho s???n ph???m n??y" onclick="updateOutOfStock('. $product['woo']['id'] .');"><i class="fa fa-tasks"></i>  C???p nh???t h???t h??ng</button>';
            } else {
                $line_product[4] = '';
            }
            
//            if ($product['kv']['price'] != $product['woo']['price']) {
//                echo '  <button id="updateWebPrice_' . $product['woo']['id'] . '" type="button" class="btn btn-mypos btn-info" title="C???p nh???t gi?? tr??n Web cho s???n ph???m n??y theo gi?? tr??n KiotViet" onclick="updateWebPrice_byKVPrice('. $product['woo']['id'] .',' . $product['kv']['price'] . ');"><i class="fa fa-anchor"></i>  C???p nh???t gi?? Web theo KiotViet</button>';
//                echo '  <button id="updateKVPrice_' . $product['kv']['id'] . '" type="button" class="btn btn-mypos btn-warning" title="C???p nh???t gi?? tr??n KiotViet cho s???n ph???m n??y theo gi?? tr??n Web" onclick="updateKVPrice_byWebPrice('. $product['kv']['id'] .',' . $product['woo']['price'] . ');"><i class="fa fa-anchor"></i>  C???p nh???t gi?? KiotViet theo Web</button>';
//            }
            
            
//            echo '</td>';
//            echo '</tr>';
            $line_product[5] = '';
            $line_product[6] = '';
            $line_product[7] = '';
            
            $line_product = array_map("utf8_decode", $line_product);
            fputcsv($file, $line_product);
            
    }
    
    foreach($kiotviet_all_products as $product) {
        
            $line_product = array();
        
            $count++;
            $line_product[0] = $count;
//            echo '<tr role="row" row_id="'. $count .'">';
//            echo '<td class="sorting_1">' . $count . '</td>';
            
            if ($product['stock']) {
                $kv_stock_status = "C??n h??ng";
            } else {
                $kv_stock_status = "H???t h??ng";
            }
            
            $line_product[1] = "ID:{$product['id']}-{$product['name']}-M??:{$product['sku']}-TT:{$kv_stock_status}-SL:{$product['quantity']}-Gi??:{$product['price']}";
            $line_product[2] = 'X';
            
            $line_product[3] = '';
            $line_product[4] = '';
            $line_product[5] = 'Miss';
            $line_product[6] = '';
//            echo "<td>Kh??ng c?? s???n ph???m</td>";
            if (empty($product['sku'])) {
                $line_product[7] = 'Kh??ng c?? M?? SP';
            } else {
                $line_product[7] = 'Kh??ng t???n t???i SP tr??n Web';
            }
//            echo '</tr>';
            
            $line_product = array_map("utf8_decode", $line_product);
            fputcsv($file, $line_product);
    }
    
    
    
    foreach($woo_all_products as $product) {
            $count++;
            $line_product[0] = $count;
            
            if ($product['stock']) {
                $woo_stock_status = "C??n h??ng";
            } else {
                $woo_stock_status = "H???t h??ng";
            }

            $line_product[1] = 'X';
            
            $line_product[2] = "{$product['name']}-M??:{$product['sku']}-TT:{$woo_stock_status}-SL:{$product['quantity']}-Gi??:{$product['price']}";
            
            $line_product[3] = '';
            $line_product[4] = '';
            $line_product[5] = '';
            $line_product[6] = 'Miss';
            
            if (empty($product['sku'])) {
                $line_product[7] = 'Kh??ng c?? M?? SP';
            } else {
                $line_product[7] = 'Kh??ng t???n t???i SP tr??n KiotViet';
            }
            
            $line_product = array_map("utf8_decode", $line_product);
            fputcsv($file, $line_product);
    }
    
    fclose($file);
    
    echo '<div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-plus-circle fa-fw"></i>
                            <strong><font color="blue">Sync Ho??n T???t!</font></strong>
                        </div>
                        <div class="panel-body">
                            <div class="alert alert-success">
                                    Qu?? tr??nh Sync ho??n t???t, b???n c?? th??? t???i v??? b??o c??o <a href="'. $file_url .'" class="alert-link">t???i ????y</a>.
                            </div>
                        </div>
                    </div>
                </div>
            </div>';
}

function kiotViet_get_preOrder_status($item_id) {
    
    $product = wc_get_product( $item_id );
    
    $pre_order_status = -1;
    $pre_order = new YITH_Pre_Order_Product( $item_id );
    
    if ( 'yes' == $pre_order->get_pre_order_status() ) {
            $pre_order_status = 1;  // S???p c?? h??ng
    } elseif ($product->is_in_stock()) {
            $pre_order_status = 2;  // C??n h??ng
    } else {
            $pre_order_status = 0;  // H???t h??ng
    }
    
    return $pre_order_status;
    
}

function function_kiotviet_sync_page() {
    
    load_assets_sync_page();
    
    echo '<div class="wrap">';
    echo '<div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-plus-circle fa-fw"></i>
                            <strong><font color="blue">Sync KiotViet</font></strong>
                        </div>
                        <div class="panel-body">
                    <form role="form" method="post" align="center">
                                <input type="hidden" id="process_manual_sync_web" name="process_manual_sync_web">
                                <button type="submit" class="btn btn-success btn-mypos-width">Manual Sync: WEB</button>
                    </form>
                    <form role="form" method="post" align="center">
                                <input type="hidden" id="process_manual_sync_kiotviet" name="process_manual_sync_kiotviet">
                                <button type="submit" class="btn btn-success btn-mypos-width">Manual Sync: KIOTVIET</button>
                    </form>
                    <form role="form" method="post" align="center">
                                <input type="hidden" id="process_auto_sync" name="process_auto_sync">
                                <button type="submit" class="btn btn-success btn-mypos-width">Auto Sync</button>
                    </form>
                    ';
    
    echo '</div></div></div></div>';
    
    if (isset($_POST['process_manual_sync_web'])) {
//        manual_sync_kiotviet();
    }
    
    if (isset($_POST['process_manual_sync_kiotviet'])) {
        manual_sync_kiotviet();
    }
    
    if (isset($_POST['process_auto_sync'])) {
        auto_sync_kiotviet();
    }
    
}