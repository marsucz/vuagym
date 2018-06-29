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

class KiotViet_SyncShoppe_List extends WP_List_Table {

//    private $kv_api;
    private $dbModel;
    private $kv_api;
    private $kv2_api;
    private $show_type = 1;
    private $show_products_per_page = 10;
    private $list_kv_product = array();
    private $list_kv2_product = array();
    private $list_shoppe = array();
    private $worksheet;

    function __construct($show_type = 1, $list_shoppe = array(), &$spreadsheet) {
        $args = array();
        parent::__construct($args);
        $this->kv_api = new KiotViet_API(1);
        $this->kv2_api = new KiotViet_API(2);
        $this->dbModel = new DbModel();
        $this->show_type = $show_type;
        $this->worksheet = $spreadsheet->getActiveSheet();
        
        $this->list_shoppe = $list_shoppe;
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

    public function prepare_items() {
        
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
            case 1: // Hien thi tat ca cac san pham

                foreach ($this->list_shoppe as $shoppe) {
                    // add product to array but don't add the parent of product variations
                    $temp_products = $this->get_product_show_type_all($shoppe);
                    if (!empty($temp_products)) {
                        $list_product = array_merge($list_product, $temp_products);
                    }
                }
                break;

            case 2: // Chi hien thi cac san pham chua dong bo

                foreach ($this->list_shoppe as $shoppe) {
                    // add product to array but don't add the parent of product variations
                    $temp_products = $this->get_product_show_type_only_not_sync($shoppe);
                    if (!empty($temp_products)) {
                        $list_product = array_merge($list_product, $temp_products);
                    }
                }
                break;

            default:
                break;
        }

        $this->_column_headers = array($columns, $hidden, $sortable);
        
//        echo "<pre>";
//        print_r($list_product);
//        echo "</pre>";
//        exit;
        
        $this->items = $list_product;
    }

    public function single_row($item) {
        echo '<tr>';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    public function get_product_show_type_all($shoppe) {

        $return_products = array();
        
        if ($shoppe['sku'] == '') {
            $temp_item['shoppe'] = $shoppe;
            $temp_item['woo'] = array();
            $temp_item['kv'] = array();
            
            $return_products[] = $temp_item;
            return $return_products;
        }
        
        $product_id = wc_get_product_id_by_sku($shoppe['sku']);

        $prod = wc_get_product( $product_id );

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
        $temp_item['shoppe'] = $shoppe;

        $return_products[] = $temp_item;

        return $return_products;
    }

    public function get_product_show_type_only_not_sync($shoppe) {

        $return_products = array();
        
        if ($shoppe['sku'] == '') {
            $temp_item['shoppe'] = $shoppe;
            $temp_item['woo'] = array();
            $temp_item['kv'] = array();
            
            $return_products[] = $temp_item;
            return $return_products;
        }
        
        $product_id = wc_get_product_id_by_sku($shoppe['sku']);

        $prod = wc_get_product($product_id);

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
        $temp_item['shoppe'] = $shoppe;

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
        
        if ($shoppe['price'] != $kv_product['price'] || $shoppe['quantity'] != $kv_product['quantity']) {
            $need_sync = true;
        }

        if ($need_sync) {
            $return_products[] = $temp_item;
        }

        return $return_products;
    }

    public function get_columns() {
        $columns = array(
//            'no'        => 'STT',
            'id' => 'ID',
            'edit' => '<span class="dashicons dashicons-admin-generic"></span>',
            'sp_kv' => 'Cửa hàng (KiotViet)',
            'sp_woo' => 'Web (WordPress)',
            'sp_shoppe' => 'Shoppe',
            'sp_store' => 'Kho hàng',
            'sp_options' => 'Tùy Chọn',
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
        
        $shoppe = $item['shoppe'];
        
        $product_id      = $item['woo'];
        
        if ($product_id) {
        
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
                        $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng-Pre Order</span>-<span style="color:red; font-weight: bold;">Đã ẩn</span>';
                    } else {
                        $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng-Pre Order</span>';
                    }
                } else {
                    $woo_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng-Pre Order</span>';
                }
            } else {
                if ($woo_product['stock']) {
                    if ($woo_product['status'] == 'private' && $product_is_variation) {
                        $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng</span>-<span style="color:red; font-weight: bold;">Đã ẩn</span>';
                    } else {
                        $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng</span>';
                    }
                } else {
                    $woo_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng</span>';
                }
            }
        }

        // KiotViet Process
        $kv_product = array();
        $kv_text = '';
        $option_text = '';
        
        if (!$product_id) {
            $option_text = 'SP không tồn tại trên Web';
        } elseif ($woo_product['sku'] == '') {
            $option_text = 'Không có mã SP trên Web';
        }
        
        $kv_product = $item['kv'];
        if (empty($kv_product)) {
            if ($option_text) $option_text .= '<br/>';
            $option_text .= '<span style="color:red; font-weight: bold;">Mã sản phẩm ' . $shoppe['sku'] . ' không tồn tại trong kiot</span>';
        } else {
            if ($kv_product['stock']) {
                $kv_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng</span>';
            } else {
                $kv_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng</span>';
            }

            $formated_price = kiotViet_formatted_price($kv_product['price']);
            $kv_text = "{$kv_product['name']}<br/>-Mã: <b>{$kv_product['sku']}</b> -{$kv_product['stock_status']} ({$kv_product['quantity']}) -Giá: {$formated_price}";
        }
        
        if ($shoppe['sku'] == '') {
            if ($option_text) $option_text .= '<br/>';
            $option_text .= '<span style="color:red; font-weight: bold;">Sản phẩm ID ' . $shoppe['id'] . ' không có mã SP</span>';
        }
        
        switch ($column_name) {
            case 'id':
                $r = $product_id;
                break;
            case 'edit':
                if ($product_id) {
                    $r .= '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>';
                    $r .= '<a href="' . $product_link . '" target="_blank"><span class="dashicons dashicons-admin-site"></span></a>';
                }
                break;
            case 'sp_woo':
                if ($product_id) {
                    $formated_price = kiotViet_formatted_price($woo_product['price']);
                    $r = "{$woo_product['name']}<br/>-Mã: <b>{$woo_product['sku']}</b> -{$woo_product['stock_status']} -Giá: {$formated_price}";
                } else {
                    $r = '';
                }
                break;
            case 'sp_kv':
                $r = $kv_text;
                break;
            case 'sp_shoppe':
                $formated_price = kiotViet_formatted_price($shoppe['price']);
                $r = "{$shoppe['name']}<br/>-Mã: <b>{$shoppe['sku']}</b> -Số lượng: {$shoppe['quantity']} -Giá: {$formated_price}";
                break;
            case 'sp_store':
                $store = get_post_meta($woo_product['id'], '_mypos_other_store', true);
                if ($store && $store == 'yes') {
                    $r = get_option('kiotviet2_name');
                } else {
                    $r = get_option('kiotviet_name');
                }
                break;
            case 'sp_options':

                if (!empty($kv_product) && $product_id) {

                    $show_updateInStock = false;

                    if (($kv_product['stock'] && !$woo_product['stock'])
                            || ($kv_product['stock'] && $woo_product['preorder'])
                            ){
                        $show_updateInStock = true;
                    }

                    if ($show_updateInStock) {
                        $r .= '  <button id="updateInStock_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-success" title="Cập nhật có hàng trên Web cho sản phẩm này" onclick="updateInStock('. $woo_product['id'] .');"><i class="fa fa-tasks"></i>  Cập nhật có hàng</button>';
                    }

                    //==================
                    $show_updateOutOfStock = false;

                    if (!$kv_product['stock'] && $woo_product['stock']) {
                        $show_updateOutOfStock = true;
                    }

                    if ($show_updateOutOfStock) {
                        $r.= '  <button id="updateOutOfStock_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-danger" title="Cập nhật hết hàng trên Web cho sản phẩm này" onclick="updateOutOfStock('. $woo_product['id'] .');"><i class="fa fa-tasks"></i>  Cập nhật hết hàng</button>';
                    }
                    
                    //==================
                    $show_enableProduct = false;

                    if ($kv_product['stock'] && $woo_product['stock'] && $woo_product['status'] == 'private') {
                        $show_enableProduct = true;
                    }

                    if ($show_enableProduct && $product_is_variation) {
                        $r.= '  <button id="enableProduct_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-success" title="Bật sản phẩm này trên Web" onclick="enableProduct('. $woo_product['id'] .');"><i class="fa fa-tasks"></i>  Hiện biến thể</button>';
                    }

                    if ($kv_product['price'] != $woo_product['price']) {

                        $formated_price = kiotViet_formatted_price($kv_product['price']);

                        $name_string = str_replace("'", "", $woo_product['name']);
                        $confirm_text = "Xác nhận sửa giá " . $name_string . " thành " . number_format($kv_product['price'], 0, ',', '.') . " đ (theo kiotviet)?";
                        $r .= '  <button id="updateWebPrice_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-info" title="Cập nhật giá trên Web cho sản phẩm này theo giá trên KiotViet" onclick="updateWebPrice_byKVPrice('. $woo_product['id'] .',' . $kv_product['price'] . ',\'' . $confirm_text .  '\');"><i class="fa fa-anchor"></i>  Update giá web = ' . $formated_price . ' (theo Kiotviet)</button>';
                        // an de dung sau
//                        $r .= '  <button id="updateKVPrice_' . $kv_product['id'] . '" type="button" class="btn btn-mypos btn-warning" title="Cập nhật giá trên KiotViet cho sản phẩm này theo giá trên Web" onclick="updateKVPrice_byWebPrice('. $kv_product['id'] .',' . $woo_product['price'] . ');"><i class="fa fa-anchor"></i>  Cập nhật giá KiotViet theo Web</button>';
                    }
                } else {
                    $r = $option_text;
                }

                $update_shoppe_text = '';
                // Update Excel file
                if (!empty($kv_product)) {
                    if ($shoppe['price'] != $kv_product['price']) {
                        $this->worksheet->getCell($shoppe['price_pos'], false)->setValue($kv_product['price']);
                        $shoppe_price = kiotViet_formatted_price($shoppe['price']);
                        $kv_price = kiotViet_formatted_price($kv_product['price']);
                        if ($update_shoppe_text) $update_shoppe_text .= '<br/>';
                        $update_shoppe_text .= "[{$shoppe['price_pos']}] Đã cập nhật giá bán SP từ {$shoppe_price} thành {$kv_price}";
                    }
                    if ($shoppe['quantity'] != $kv_product['quantity']) {
                        $this->worksheet->getCell($shoppe['quantity_pos'], false)->setValue($kv_product['quantity']);
                        if ($update_shoppe_text) $update_shoppe_text .= '<br/>';
                        $update_shoppe_text .= "[{$shoppe['quantity_pos']}] Đã cập nhật tồn kho SP từ {$shoppe['quantity']} thành {$kv_product['quantity']}";
                    }
                }
                
                if ($update_shoppe_text) $update_shoppe_text .= '<br/>';
                $r = $update_shoppe_text . $r;
                
                if (empty($r)) {
                    $r = '<span style="color:green; font-weight: bold;">SP ĐÃ ĐỒNG BỘ</span>';
                }
                
                break;
            default:
                return print_r($item, true);
        }

        return $r;
    }

}
