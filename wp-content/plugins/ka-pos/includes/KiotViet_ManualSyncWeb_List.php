<?php

/**
 * Description of KiotViet_ManualSyncWeb_List
 *
 * @author dmtuan
 */

require_once('DbModel.php');
require_once('kiotviet_api.php');

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class KiotViet_ManualSyncWeb_List extends WP_List_Table {

//    private $kv_api;
    private $dbModel;
    private $kv_api;
    private $kv2_api;
    private $show_type = 1;
    private $show_products_per_page = 10;
    private $list_kv_product = array();
    private $list_kv2_product = array();

    function __construct($show_type = 1, $show_products = 10) {
        $args = array();
        parent::__construct($args);
        $this->kv_api = new KiotViet_API(1);
        $this->kv2_api = new KiotViet_API(2);
        $this->dbModel = new DbModel();
        $this->show_type = $show_type;
        $this->show_products_per_page = $show_products;
    }

    public function get_kv_product_by_code($kv_code) {
        foreach ($this->list_kv_product as $kv_product) {
            if ($kv_product['sku'] == $kv_code) {
                return $kv_product;
            }
        }
        return [];
    }
    
    public function get_kv2_product_by_code($kv_code) {
        foreach ($this->list_kv2_product as $kv_product) {
            if ($kv_product['sku'] == $kv_code) {
                return $kv_product;
            }
        }
        return [];
    }

    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

//        $perPage = $this->get_items_per_page('sync_by_web_products', 10);
        $perPage = $this->show_products_per_page;

        $currentPage = $this->get_pagenum();

        $totalItems = $this->dbModel->get_count_woo_product();

        $list_product = array();
        $this->list_kv_product = $this->kv_api->get_all_products_multi();
        $this->list_kv2_product = $this->kv2_api->get_all_products_multi();
        
        switch ($this->show_type) {
            case 0: // Hien thi tat ca cac san pham

                $loop = new WP_Query( array( 'post_type' => array('product'), 'posts_per_page' => $perPage, 'paged' => $currentPage ) );

                while ( $loop->have_posts() ) : $loop->the_post();

                    $theid = get_the_ID();

                    // add product to array but don't add the parent of product variations
                    if ($theid) {
                        $temp_products = $this->get_product_show_type_all($theid);
                        if (!empty($temp_products)) {
                            $list_product = array_merge($list_product, $temp_products);
                        }
                    }

                endwhile;
                wp_reset_query();

                $this->set_pagination_args( array(
                    'total_items' => $totalItems,
                    'per_page'    => $perPage
                ) );

                break;

            case 1: // Chi hien thi cac san pham chua dong bo

                $perPage = 50;
                $currentPage = 0;

                // show product one times
                $show_products = $this->show_products_per_page;
                $count_product = 0;

                while ($count_product < $show_products) {

                    $currentPage++;

                    $loop = new WP_Query( array( 'post_type' => array('product'), 'posts_per_page' => $perPage, 'paged' => $currentPage ) );

                    if (!$loop->post_count || $loop->post_count == 0) {
                        break;
                    }

                    while ( $loop->have_posts() ) : $loop->the_post();

                        $theid = get_the_ID();

                        // add product to array but don't add the parent of product variations
                        if ($theid) {
                            $temp_products = $this->get_product_show_type_only_not_sync($theid);

                            if (!empty($temp_products)) {
                                $list_product = array_merge($list_product, $temp_products);
                                $count_product++;
                            }
                        }

                        if ($count_product >= $show_products) {
                            break;
                        }

                    endwhile;
                    wp_reset_query();
                    
                }

                break;

            case 2: // Chi hien thi san pham Pre-Order

                $perPage = 20;
                $currentPage = 0;

                // show product one times
                $show_products = $this->show_products_per_page;
                $count_product = 0;

                while ($count_product < $show_products) {

                    $currentPage++;

                    $loop = new WP_Query( array( 'post_type' => array('product'), 'posts_per_page' => $perPage, 'paged' => $currentPage ) );

                    if (!$loop->post_count) {
                        break;
                    }

                    while ( $loop->have_posts() ) : $loop->the_post();

                        $theid = get_the_ID();

                        // add product to array but don't add the parent of product variations
                        if ($theid) {
                            $temp_products = $this->get_product_show_type_pre_order($theid);

                            if (!empty($temp_products)) {
                                $list_product = array_merge($list_product, $temp_products);
                                $count_product++;
                            }
                        }

                        if ($count_product >= $show_products) {
                            break;
                        }

                    endwhile;
                    wp_reset_query();
                }

                break;
            default:
                break;
        }

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $list_product;
    }

    public function single_row($item) {
        echo '<tr>';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    public function get_product_show_type_all($product_id) {

        $return_products = array();

        $prod = wc_get_product( $product_id );

        if ( $prod && $prod->is_type( 'variable' ) && $prod->has_child() ) {

//            $args = array(
//                'post_type'     => 'product_variation',
//                'post_status'   => array( 'private', 'publish' ),
//                'post_parent'   => $product_id // 
//            );
//            $variations = get_posts( $args );
            $variations = $this->dbModel->get_children_ids($product_id);
            foreach ($variations as $child) {
                if ( $child ) {

                    $temp_item = array();
                    $temp_item['woo'] = $child['ID'];

                    $sku = get_post_meta($child['ID'], '_sku', true);

                    // KiotViet Process
                    if ($sku) {
                        $store = get_post_meta($child['ID'], '_mypos_other_store', true);
                        if ($store && $store == 'yes') {
                            $sku = get_sku_store_main($sku);
                            $kv_product = $this->get_kv2_product_by_code($sku);
                        } else {
                            $kv_product = $this->get_kv_product_by_code($sku);
                        }
                    } else {
                        $kv_product = array();
                    }

                    $temp_item['kv'] = $kv_product;

                    $return_products[] = $temp_item;
                    
                }
            }

        } elseif ($prod && $prod->is_type( 'simple' )) {
            
            $temp_item = array();
            $temp_item['woo'] = $product_id;

            $sku = get_post_meta($product_id, '_sku', true);

            // KiotViet Process
            if ($sku) {
                $store = get_post_meta($product_id, '_mypos_other_store', true);
                if ($store && $store == 'yes') {
                    $sku = get_sku_store_main($sku);
                    $kv_product = $this->get_kv2_product_by_code($sku);
                } else {
                    $kv_product = $this->get_kv_product_by_code($sku);
                }
            } else {
                $kv_product = array();
            }

            $temp_item['kv'] = $kv_product;

            $return_products[] = $temp_item;
        }

        return $return_products;
    }

    public function get_product_show_type_only_not_sync($product_id) {

        $return_products = array();

        $prod = wc_get_product($product_id);

        if ($prod && $prod->is_type('variable') && $prod->has_child()) {

//            $args = array(
//                'post_type'     => 'product_variation',
//                'post_status'   => array( 'private', 'publish' ),
//                'post_parent'   => $product_id // 
//            );
//            $variations = get_posts( $args );

            $variations = $this->dbModel->get_children_ids($product_id);
            foreach ($variations as $child) {
                if ($child) {

                    $temp_item = array();
                    $temp_item['woo'] = $child['ID'];

                    $sku = get_post_meta($child['ID'], '_sku', true);

                    // KiotViet Process
                    if ($sku) {
                        $store = get_post_meta($child['ID'], '_mypos_other_store', true);
                        if ($store && $store == 'yes') {
//                            echo $sku;
                            $sku = get_sku_store_main($sku);
//                            echo $sku;
                            $kv_product = $this->get_kv2_product_by_code($sku);
//                            echo '<pre>';
//                            print_r($kv_product);
//                            echo '<pre>';
//                            exit;
                        } else {
                            $kv_product = $this->get_kv_product_by_code($sku);
                        }
                    } else {
                        $kv_product = array();
                    }

                    $temp_item['kv'] = $kv_product;

                    $need_sync = false;

                    if (!empty($kv_product)) {

                        $product = wc_get_product($child['ID']);

                        $woo_product['price'] = $product->get_price();
                        $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
                        $woo_product['preorder'] = kiotViet_get_preOrder_status($child['ID']);
                        $woo_product['status'] = $product->get_status();

                        if (($kv_product['stock'] != $woo_product['stock']) || ($kv_product['stock'] && $woo_product['stock'] && ($woo_product['status'] == 'private')) || ($kv_product['stock'] && $woo_product['preorder']) || ($kv_product['price'] != $woo_product['price'])) {
                            // nothing to show options
                            $need_sync = true;
                        }
                    } else {
                        $need_sync = true;
                    }

                    if ($need_sync) {
                        $return_products[] = $temp_item;
                    }
                }
            }
        } elseif ($prod && $prod->is_type('simple')) {

            $temp_item = array();
            $temp_item['woo'] = $product_id;

            $sku = get_post_meta($product_id, '_sku', true);

            // KiotViet Process
            if ($sku) {
                $store = get_post_meta($product_id, '_mypos_other_store', true);
                if ($store && $store == 'yes') {
                    $sku = get_sku_store_main($sku);
                    $kv_product = $this->get_kv2_product_by_code($sku);
                } else {
                    $kv_product = $this->get_kv_product_by_code($sku);
                }
            } else {
                $kv_product = array();
            }

            $temp_item['kv'] = $kv_product;

            $need_sync = false;

            if (!empty($kv_product)) {

                $product = wc_get_product($product_id);

                $woo_product['price'] = $product->get_price();
                $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
                $woo_product['preorder'] = kiotViet_get_preOrder_status($product_id);
                $woo_product['status'] = $product->get_status();

                if (($kv_product['stock'] != $woo_product['stock']) || ($kv_product['stock'] && $woo_product['stock'] && ($woo_product['status'] == 'private')) || ($kv_product['stock'] && $woo_product['preorder']) || ($kv_product['price'] != $woo_product['price'])) {
                    // nothing to show options
                    $need_sync = true;
                }
            } else {
                $need_sync = true;
            }

            if ($need_sync) {
                $return_products[] = $temp_item;
            }
        }

        return $return_products;
    }

    public function get_product_show_type_pre_order($product_id) {

        $return_products = array();

        $prod = wc_get_product($product_id);

        if ($prod && $prod->is_type('variable') && $prod->has_child()) {

//            $args = array(
//                'post_type'     => 'product_variation',
//                'post_status'   => array( 'private', 'publish' ),
//                'post_parent'   => $product_id // 
//            );
//            $variations = get_posts( $args );
            $variations = $this->dbModel->get_children_ids($product_id);
            foreach ($variations as $child) {
                if ($child) {

                    $temp_item = array();
                    $temp_item['woo'] = $child['ID'];

                    $preOrder_status = kiotViet_get_preOrder_status($child['ID']);

                    // Chi hien thi cac san pham Pre-Order
                    if ($preOrder_status) {
                        $sku = get_post_meta($child['ID'], '_sku', true);

                        // KiotViet Process
                        if ($sku) {
                            $store = get_post_meta($child['ID'], '_mypos_other_store', true);
                            if ($store && $store == 'yes') {
                                $sku = get_sku_store_main($sku);
                                $kv_product = $this->get_kv2_product_by_code($sku);
                            } else {
                                $kv_product = $this->get_kv_product_by_code($sku);
                            }
                        } else {
                            $kv_product = array();
                        }

                        $temp_item['kv'] = $kv_product;
                        $return_products[] = $temp_item;
                    }
                }
            }
        } elseif ($prod && $prod->is_type('simple')) {

            $temp_item = array();
            $temp_item['woo'] = $product_id;

            $preOrder_status = kiotViet_get_preOrder_status($product_id);

            // Chi hien thi cac san pham Pre-Order
            if ($preOrder_status) {

                $sku = get_post_meta($product_id, '_sku', true);

                // KiotViet Process
                if ($sku) {
                    $store = get_post_meta($product_id, '_mypos_other_store', true);
                    if ($store && $store == 'yes') {
                        $sku = get_sku_store_main($sku);
                        $kv_product = $this->get_kv2_product_by_code($sku);
                    } else {
                        $kv_product = $this->get_kv_product_by_code($sku);
                    }
                } else {
                    $kv_product = array();
                }

                $temp_item['kv'] = $kv_product;
                $return_products[] = $temp_item;
            }
        }

        return $return_products;
    }

    public function get_columns() {
        $columns = array(
//            'no'        => 'STT',
            'id' => 'ID',
            'edit' => '<span class="dashicons dashicons-admin-generic"></span>',
            'kv' => 'C???a h??ng (KiotViet)',
            'woo' => 'Web (WordPress)',
            'store' => 'Kho h??ng',
            'options' => 'T??y Ch???n',
        );
        return $columns;
    }

    public function get_hidden_columns() {
        return array('id');
    }

    public function get_sortable_columns() {
        return array();
    }

    public function column_default($item, $column_name) {
        $r = '';

        $product_id      = $item['woo'];
        $product         = wc_get_product( $product_id );
        
        $product_is_variation = false;
        if ($product->is_type( 'variation' )) {
            $base_product_id = $product->get_parent_id();
            $product_is_variation = true;
        } elseif ($product->is_type( 'simple' )) {
            $base_product_id = $product_id;
        } else {
            $base_product_id = $product_id;
        }
        $edit_link       = get_edit_post_link( $base_product_id );
        $product_link   = get_permalink($base_product_id);

        $woo_product['id'] = $product->get_id();
        $woo_product['sku'] = $product->get_sku();
        $woo_product['name'] = mypos_get_variation_title($product);
        $woo_product['price'] = $product->get_price();
        $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
        $woo_product['preorder'] = kiotViet_get_preOrder_status($woo_product['id']);
        $woo_product['status'] = $product->get_status();

        if ($woo_product['preorder']) {
            if ($woo_product['stock']) {
                if ($woo_product['status'] == 'private' && $product_is_variation) {
                    $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">C??n h??ng-Pre Order</span>-<span style="color:red; font-weight: bold;">???? ???n</span>';
                } else {
                    $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">C??n h??ng-Pre Order</span>';
                }
            } else {
                $woo_product['stock_status'] = '<span style="color:red; font-weight: bold;">H???t h??ng-Pre Order</span>';
            }
        } else {
            if ($woo_product['stock']) {
                if ($woo_product['status'] == 'private' && $product_is_variation) {
                    $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">C??n h??ng</span>-<span style="color:red; font-weight: bold;">???? ???n</span>';
                } else {
                    $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">C??n h??ng</span>';
                }
            } else {
                $woo_product['stock_status'] = '<span style="color:red; font-weight: bold;">H???t h??ng</span>';
            }
        }

        // KiotViet Process
        $kv_product = array();
        $kv_text = '';
        $option_text = '';

        if ($woo_product['sku']) {

            $kv_product = $item['kv'];

            if (!empty($kv_product)) {
                if ($kv_product['stock']) {
                    $kv_product['stock_status'] = '<span style="color:green; font-weight: bold;">C??n h??ng</span>';
                } else {
                    $kv_product['stock_status'] = '<span style="color:red; font-weight: bold;">H???t h??ng</span>';
                }

                $formated_price = kiotViet_formatted_price($kv_product['price']);
                $kv_text = "{$kv_product['name']}<br/>-M??: <b>{$kv_product['sku']}</b> -{$kv_product['stock_status']} ({$kv_product['quantity']}) -Gi??: {$formated_price}";
            } else {
                $option_text = 'SP kh??ng t???n t???i tr??n KiotViet';
            }
        } else {
            $option_text = 'Kh??ng c?? m?? SP';
        }

        switch ($column_name) {
            case 'id':
                $r = $product_id;
                break;
            case 'edit':
                $r .= '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>';
                $r .= '<a href="' . $product_link . '" target="_blank"><span class="dashicons dashicons-admin-site"></span></a>';
                break;
            case 'woo':
                $formated_price = kiotViet_formatted_price($woo_product['price']);
                $r = "{$woo_product['name']}<br/>-M??: <b>{$woo_product['sku']}</b> -{$woo_product['stock_status']} -Gi??: {$formated_price}";
                break;
            case 'kv':
                $r = $kv_text;
                break;
            case 'store':
                $store = get_post_meta($woo_product['id'], '_mypos_other_store', true);
                if ($store && $store == 'yes') {
                    $r = get_option('kiotviet2_name');
                } else {
                    $r = get_option('kiotviet_name');
                }
                break;
            case 'options':

                if (!empty($kv_product)) {

                    $show_updateInStock = false;

                    if (($kv_product['stock'] && !$woo_product['stock'])
                            || ($kv_product['stock'] && $woo_product['preorder'])
                            ){
                        $show_updateInStock = true;
                    }

                    if ($show_updateInStock) {
                        $r .= '  <button id="updateInStock_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-success" title="C???p nh???t c?? h??ng tr??n Web cho s???n ph???m n??y" onclick="updateInStock('. $woo_product['id'] .');"><i class="fa fa-tasks"></i>  C???p nh???t c?? h??ng</button>';
                    }

                    //==================
                    $show_updateOutOfStock = false;

                    if (!$kv_product['stock'] && $woo_product['stock']) {
                        $show_updateOutOfStock = true;
                    }

                    if ($show_updateOutOfStock) {
                        $r.= '  <button id="updateOutOfStock_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-danger" title="C???p nh???t h???t h??ng tr??n Web cho s???n ph???m n??y" onclick="updateOutOfStock('. $woo_product['id'] .');"><i class="fa fa-tasks"></i>  C???p nh???t h???t h??ng</button>';
                    }
                    
                    //==================
                    $show_enableProduct = false;

                    if ($kv_product['stock'] && $woo_product['stock'] && $woo_product['status'] == 'private') {
                        $show_enableProduct = true;
                    }

                    if ($show_enableProduct && $product_is_variation) {
                        $r.= '  <button id="enableProduct_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-success" title="B???t s???n ph???m n??y tr??n Web" onclick="enableProduct('. $woo_product['id'] .');"><i class="fa fa-tasks"></i>  Hi???n bi???n th???</button>';
                    }

                    if ($kv_product['price'] != $woo_product['price']) {

                        $formated_price = kiotViet_formatted_price($kv_product['price']);

                        $name_string = str_replace("'", "", $woo_product['name']);
                        $confirm_text = "X??c nh???n s???a gi?? " . $name_string . " th??nh " . number_format($kv_product['price'], 0, ',', '.') . " ?? (theo kiotviet)?";
                        $r .= '  <button id="updateWebPrice_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-info" title="C???p nh???t gi?? tr??n Web cho s???n ph???m n??y theo gi?? tr??n KiotViet" onclick="updateWebPrice_byKVPrice('. $woo_product['id'] .',' . $kv_product['price'] . ',\'' . $confirm_text .  '\');"><i class="fa fa-anchor"></i>  Update gi?? web = ' . $formated_price . ' (theo Kiotviet)</button>';
                        // an de dung sau
//                        $r .= '  <button id="updateKVPrice_' . $kv_product['id'] . '" type="button" class="btn btn-mypos btn-warning" title="C???p nh???t gi?? tr??n KiotViet cho s???n ph???m n??y theo gi?? tr??n Web" onclick="updateKVPrice_byWebPrice('. $kv_product['id'] .',' . $woo_product['price'] . ');"><i class="fa fa-anchor"></i>  C???p nh???t gi?? KiotViet theo Web</button>';
                    }
                } else {
                    $r = $option_text;
                }

                if (empty($r)) {
                    $r = '<span style="color:green; font-weight: bold;">SP ???? ?????NG B???</span>';
                }

                break;
            default:
                return print_r($item, true);
        }

        return $r;
    }

}
