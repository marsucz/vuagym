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

class KiotViet_ManualSyncKiotViet_List extends WP_List_Table {
    
//    private $kv_api;
    private $dbModel;
    private $kv_api;
    private $show_type = 1;
    private $show_products_per_page = 10;
    private $list_kv_product = array();
    
    function __construct($show_type, $show_products) {
        $args = array();
        parent::__construct($args);
        $this->kv_api = new KiotViet_API();
        $this->dbModel = new DbModel();
        $this->show_type = $show_type;
        $this->show_products_per_page = $show_products;
    }
    
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        
//        $perPage = $this->get_items_per_page('products_per_page', 10);
        $perPage = $this->show_products_per_page;
        
        $currentPage = $this->get_pagenum();
        
        $list_product = array();
        
        switch ($this->show_type) {
            
            case 0: // Hien thi tat ca cac san pham
                
                $result = $this->kv_api->get_product_paged($perPage, $currentPage);
        
                $totalItems = $result['total'];

                $this->set_pagination_args( array(
                    'total_items' => $totalItems,
                    'per_page'    => $perPage
                ) );
                
                foreach ($result['all_products'] as $kv_product) {
                    $list_product[] = $this->get_product_show_type_all($kv_product);
                }
                
                break;
            
            case 1: // Chi hien thi san pham khong dong bo
                
                $this->list_kv_product = $this->kv_api->get_all_products_multi();
                
//                $perPage = 50;
//                $currentPage = 0;
                
                // show product one times
                $show_products = $this->show_products_per_page;
                $count_product = 0;
                
//                while ($count_product < $show_products) {
//                    
//                    $currentPage++;
//                    $result = $this->kv_api->get_product_paged($perPage, $currentPage);
//                    
//                    if (empty($result)) { break; }
                    
                    foreach ($this->list_kv_product as $kv_product) {
                        
                        $new_item = $this->get_product_show_type_no_sync($kv_product);
                        if (!empty($new_item)) {
                            $list_product[] = $new_item;
                            $count_product++;
                        }
                        if ($count_product >= $show_products) {
                            break;
                        }
                    }
//                }
                break;
            
            default:
                break;
        }
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $list_product;
        
    }
    
    public function get_product_show_type_all($kv_product) {
        
        $new_item = array();
        
        $woo_product = array();
        if (!empty($kv_product['sku'])) {
            $woo_id = wc_get_product_id_by_sku($kv_product['sku']);
            $woo_product['id'] = $woo_id;
            // process
            if ($woo_id) {

                $product_id      = $woo_id;
                $product         = wc_get_product( $product_id );

                if ($product->is_type( 'variation' )) {
                    $base_product_id = $product->get_parent_id();
                } elseif ($product->is_type( 'simple' )) {
                    $base_product_id = $product_id;
                } else {
                    $base_product_id = $product_id;
                }
                
                $woo_product['base_id'] = $base_product_id;

                $woo_product['id'] = $product->get_id();
                $woo_product['sku'] = $product->get_sku();
                $woo_product['name'] = mypos_get_variation_title($product);
                $woo_product['price'] = $product->get_price();
                $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
                $woo_product['preorder'] = kiotViet_get_preOrder_status($woo_product['id']);
                
                if ($woo_product['preorder']) {
                    if ($woo_product['stock']) {
                        $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng-Pre Order</span>';
                    } else {
                        $woo_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng-Pre Order</span>';
                    }
                } else {
                    if ($woo_product['stock']) {
                        $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng</span>';
                    } else {
                        $woo_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng</span>';
                    }
                }

                $formated_price = kiotViet_formatted_price($woo_product['price']);
                $woo_product['woo_text'] = "{$woo_product['name']}<br/>-Mã: <b>{$woo_product['sku']}</b> -{$woo_product['stock_status']} -Giá: {$formated_price}";

            } else {
                $woo_product['option_text'] = 'SP không tồn tại trên Web';
            }
            
        } else {
            $woo_product['id'] = 0;
        }
        
        $new_item['kv'] = $kv_product;
        $new_item['woo'] = $woo_product;
        return $new_item;
    }
    
    public function get_product_show_type_no_sync($kv_product) {
        
        $new_item = array();
        $woo_product = array();
        
        if (!empty($kv_product['sku'])) {
            $woo_id = wc_get_product_id_by_sku($kv_product['sku']);
            $woo_product['id'] = $woo_id;
            // process
            if ($woo_id) {

                $product_id      = $woo_id;
                $product         = wc_get_product( $product_id );

                if ($product->is_type( 'variation' )) {
                    $base_product_id = $product->get_parent_id();
                } elseif ($product->is_type( 'simple' )) {
                    $base_product_id = $product_id;
                } else {
                    $base_product_id = $product_id;
                }
                
                $woo_product['base_id'] = $base_product_id;

                $woo_product['id'] = $product->get_id();
                $woo_product['sku'] = $product->get_sku();
                $woo_product['name'] = mypos_get_variation_title($product);
                $woo_product['price'] = $product->get_price();
                $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
                $woo_product['preorder'] = kiotViet_get_preOrder_status($woo_product['id']);
                
                $need_sync = false;
                    
                if (($kv_product['stock'] != $woo_product['stock'])
                    || ($kv_product['stock'] && $woo_product['preorder'])
                    || ($kv_product['price'] != $woo_product['price'])) 
                {
                    // nothing to show options
                        $need_sync = true;
                }
                
                if (!$need_sync) {
                    return $new_item;
                }
                
                if ($woo_product['preorder']) {
                    if ($woo_product['stock']) {
                        $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng-Pre Order</span>';
                    } else {
                        $woo_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng-Pre Order</span>';
                    }
                } else {
                    if ($woo_product['stock']) {
                        $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng</span>';
                    } else {
                        $woo_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng</span>';
                    }
                }
                
                $formated_price = kiotViet_formatted_price($woo_product['price']);
                $woo_product['woo_text'] = "{$woo_product['name']}<br/>-Mã: <b>{$woo_product['sku']}</b> -{$woo_product['stock_status']} -Giá: {$formated_price}";
                
                if ((trim($product->get_name()) == 'Header') || (trim($product->get_title()) == 'Header')) {
                    $woo_product['woo_text'] = '<span style="font-weight: bold; color: red;">SẢN PHẨM TRÊN WEB BỊ LỖI</span><br/>' . $woo_product['woo_text'];
                }

            } else {
//                return $new_item;
                $woo_product['id'] = 0;
            }
            
        } else {
//            return $new_item;
            $woo_product['id'] = 0;
        }
        
        $new_item['kv'] = $kv_product;
        $new_item['woo'] = $woo_product;
        
        return $new_item;
    }
    
    public function single_row( $new_item ) {
        
        echo '<tr>';
        $this->single_row_columns( $new_item );
        echo '</tr>';
        
    }
    
    public function get_columns()
    {
        $columns = array(
//            'no'        => 'STT',
            'id'            => 'ID',
            'edit'               => '<span class="dashicons dashicons-admin-generic"></span>',
            'kv'            => 'Cửa hàng (KiotViet)',
            'woo'           => 'Web (WordPress)',
            'options'        => 'Tùy Chọn',
        );
        return $columns;
        
    }
    
    public function get_hidden_columns()
    {
        return array('id');
    }
    
    public function get_sortable_columns()
    {
        return array();
    }
    
    public function column_default( $item, $column_name )
    {
        
        $r = '';

        // KiotViet Process
        $option_text = '';
        $kv_product = $item['kv'];
        
        if ($kv_product['stock']) {
            $kv_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng</span>';
        } else {
            $kv_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng</span>';
        }
        
        $formated_price = kiotViet_formatted_price($kv_product['price']);
        $kv_text = "{$kv_product['name']}<br/>-Mã: <b>{$kv_product['sku']}</b> -{$kv_product['stock_status']} ({$kv_product['quantity']}) -Giá: {$formated_price}";
        
        if (empty($kv_product['sku']) || is_null($kv_product['sku'])) {
            $option_text = 'Không có mã SP trên Kiotviet';
        }
        
//        $woo_product_id = ;
        
        //test
//        $woo_product_id = 0;
        
        $woo_product = $item['woo'];
        
        if ($woo_product['id']) {
            
        } else {
            if (empty($option_text)) {
                $option_text = 'SP không tồn tại trên Web';
            } else {
                $option_text .= ', SP không tồn tại trên Web';
            }
        }
        
        switch( $column_name ) {
            case 'id':
                if ($woo_product['id']) {
                    $r = $woo_product['id'];
                } else {
                    $r = '';
                }
                break;
            case 'edit':
                if ($woo_product['id']) {
                    $edit_link = get_edit_post_link($woo_product['base_id']);
                    $product_link = get_permalink($woo_product['base_id']);
                    $r .= '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>';
                    $r .= '<a href="' . $product_link . '" target="_blank"><span class="dashicons dashicons-admin-site"></span></a>';
                } else {
                    $r = '';
                }
                break;
            case 'kv':
                $r = $kv_text;
                break;
            case 'woo':
                if ($woo_product['id']) {
                    $r = $woo_product['woo_text'];
                } else {
                    $r = '';
                }
                break;
            case 'options':
                
                if ($woo_product['id']) {
                    
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

                    if ($kv_product['price'] != $woo_product['price']) {
                        
                        $confirm_text = "Xác nhận sửa giá " . $woo_product['name'] . " thành " . number_format($kv_product['price'], 0, ',', '.') . " đ (theo kiotviet)?";
                        $confirm_text = str_replace("'", "", $confirm_text);
                        $r .= '  <button id="updateWebPrice_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-info" title="Cập nhật giá trên Web cho sản phẩm này theo giá trên KiotViet" onclick="updateWebPrice_byKVPrice('. $woo_product['id'] .',' . $kv_product['price'] . ',\'' . $confirm_text .  '\');"><i class="fa fa-anchor"></i>  Cập nhật giá Web theo KiotViet</button>';
                        // An de dung sau
//                        $r .= '  <button id="updateKVPrice_' . $kv_product['id'] . '" type="button" class="btn btn-mypos btn-warning" title="Cập nhật giá trên KiotViet cho sản phẩm này theo giá trên Web" onclick="updateKVPrice_byWebPrice('. $kv_product['id'] .',' . $woo_product['price'] . ');"><i class="fa fa-anchor"></i>  Cập nhật giá KiotViet theo Web</button>';
                    }
                    
                } else {
                    $r = $option_text;
                }
                
                if (empty($r)) {
                    $r = '<span style="color:green; font-weight: bold;">SP ĐÃ ĐỒNG BỘ</span>';
                }
                
                break;
            default:
                return print_r( $item, true ) ;
        }
        
        return $r;
    }
    
}
