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
    
    function __construct($args = array()) { 
        parent::__construct($args);
        $this->kv_api = new KiotViet_API();
        $this->dbModel = new DbModel();
    }
    
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();
        
        $perPage = $this->get_items_per_page('products_per_page', 10);
        
        $currentPage = $this->get_pagenum();
        
        $result = $this->kv_api->get_product_paged($perPage, $currentPage);
        
        $totalItems = $result['total'];
        
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $result['all_products'];
        
    }
    
    public function single_row( $kv_product ) {
        
        $new_item = array();
        
        $new_item['kv'] = $kv_product;
        
        $woo_product = array();
        if (!empty($kv_product['sku'])) {
            $woo_id = wc_get_product_id_by_sku($kv_product['sku']);
//            $new_item['woo']['id'] = $woo_id;
            $woo_product['id'] = $woo_id;
            // process
            if ($woo_id) {

                $product_id      = $woo_id;
                $product         = wc_get_product( $product_id );
                $product_type = '';

//                if ($product->is_type( 'variation' )) {
//                    $base_product_id = $product->get_parent_id();
//                    $product_type = 'Biến thể';
//                } elseif ($product->is_type( 'simple' )) {
//                    $base_product_id = $product_id;
//                    $product_type = 'SP Đơn';
//                } else {
//                    $base_product_id = $product_id;
//                    $product_type = 'SP Cha';
//                }
//                $edit_link       = get_edit_post_link( $base_product_id );
//                
//                $new_item['woo']['edit_link'] = $edit_link;

                $woo_product['id'] = $product->get_id();
                $woo_product['sku'] = $product->get_sku();
                $woo_product['name'] = $product->get_name();
                $woo_product['price'] = $product->get_price();
                $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
                $woo_product['quantity'] = $product->get_stock_quantity();

                if ($woo_product['stock']) {
                    $woo_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng</span>';
                } else {
                    $woo_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng</span>';
                }

                $woo_product['woo_text'] = "{$product_type}: {$woo_product['name']}<br/>-Mã:<b>{$woo_product['sku']}</b>-TT:{$woo_product['stock_status']}-SL:{$woo_product['quantity']}-Giá:{$woo_product['price']}";

            } else {
                $woo_product['option_text'] = 'SP không tồn tại trên Web';
            }
            
        } else {
            $woo_product['id'] = 0;
        }
        
        $new_item['woo'] = $woo_product;
        
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
        $kv_text = "{$kv_product['id']}-{$kv_product['name']}<br/>-Mã:<b>{$kv_product['sku']}</b>-TT:{$kv_product['stock_status']}-SL:{$kv_product['quantity']}-Giá:{$kv_product['price']}";
        
        if (empty($kv_product['sku']) || is_null($kv_product['sku'])) {
            $option_text = 'Thiếu mã SP trên KiotViet';
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
                    if (isset($woo_product['edit_link'])) {
                        $r = '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>';
                    } else {
                        $r = '';
                    }
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
                    
                    if ($kv_product['stock'] && !$woo_product['stock']) {
                        $r .= '  <button id="updateInStock_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-success" title="Cập nhật có hàng trên Web cho sản phẩm này" onclick="updateInStock('. $woo_product['id'] .');"><i class="fa fa-tasks"></i>  Cập nhật có hàng</button>';
                    }

                    if (!$kv_product['stock'] && $woo_product['stock']) {
                        $r.= '  <button id="updateOutOfStock_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-danger" title="Cập nhật hết hàng trên Web cho sản phẩm này" onclick="updateOutOfStock('. $woo_product['id'] .');"><i class="fa fa-tasks"></i>  Cập nhật hết hàng</button>';
                    }

                    if ($kv_product['price'] != $woo_product['price']) {
                        $r .= '  <button id="updateWebPrice_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-info" title="Cập nhật giá trên Web cho sản phẩm này theo giá trên KiotViet" onclick="updateWebPrice_byKVPrice('. $woo_product['id'] .',' . $kv_product['price'] . ');"><i class="fa fa-anchor"></i>  Cập nhật giá Web theo KiotViet</button>';
                        $r .= '  <button id="updateKVPrice_' . $kv_product['id'] . '" type="button" class="btn btn-mypos btn-warning" title="Cập nhật giá trên KiotViet cho sản phẩm này theo giá trên Web" onclick="updateKVPrice_byWebPrice('. $kv_product['id'] .',' . $woo_product['price'] . ');"><i class="fa fa-anchor"></i>  Cập nhật giá KiotViet theo Web</button>';
                    }
                    
                } else {
                    $r = $option_text;
                }
                
                break;
            default:
                return print_r( $item, true ) ;
        }
        
        return $r;
    }
    
}
