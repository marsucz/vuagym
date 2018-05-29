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

class Mypos_ImportProduct_List extends WP_List_Table {
    
    private $dbModel;
    private $show_type = 0;
    private $show_products_per_page = 10;
    
    function __construct($show_type, $show_products) {
        $args = array();
        parent::__construct($args);
        $this->dbModel = new DbModel($this->store);
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
                
                $list_import_product = $this->dbModel->kapos_get_all_import_product($perPage, $currentPage);
                
                $totalItems = $this->dbModel->kapos_get_count_import_product();
                
                $this->set_pagination_args( array(
                    'total_items' => $totalItems,
                    'per_page'    => $perPage
                ) );
                
                foreach ($list_import_product as $ip_product) {
                    $list_product[] = $this->get_importproduct_show_all($ip_product);
                }
                
                break;
            
        }
        
        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $list_product;
        
    }
    
    public function get_importproduct_show_all($ip_product) {
        
        $new_item = array();
        $woo_product = array();
        
        if (!empty($ip_product['product_code'])) {
            $woo_id = wc_get_product_id_by_sku($ip_product['product_code']);
            $woo_product['id'] = $woo_id;
            // 
//            if ($this->store != 1) {
//                $store = get_post_meta($woo_id, '_mypos_other_store', true);
//                if ($store && $store == 'yes') {
//                    
//                } else {
//                    $woo_id = 0;
//                }
//            }
            // process
            if ($woo_id) {

                $product_id      = $woo_id;
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
                
                $woo_product['base_id'] = $base_product_id;

                $woo_product['id'] = $product->get_id();
                $woo_product['sku'] = $product->get_sku();
                $woo_product['name'] = mypos_get_variation_title($product);
                $woo_product['price'] = $product->get_price();
                $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
                $woo_product['preorder'] = kiotViet_get_preOrder_status($woo_product['id']);
                $woo_product['status'] = $product->get_status();
                $woo_product['is_variation'] = $product_is_variation;
                
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

                $formated_price = kiotViet_formatted_price($woo_product['price']);
                $woo_product['woo_text'] = "{$woo_product['name']}<br/>-Mã: <b>{$woo_product['sku']}</b> -{$woo_product['stock_status']} -Giá: {$formated_price}";

            } else {
                $woo_product['option_text'] = 'SP không tồn tại trên Web';
            }
            
        } else {
            $woo_product['id'] = 0;
        }
        
        $new_item['ip'] = $ip_product;
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
            'sku'           => "Mã sản phẩm",
            'name'           => "Tên sản phẩm",
            'quantity'           => "Số lượng",
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
        $ip_product = $item['ip'];
        $woo_product = $item['woo'];
        
        if ($woo_product['id']) {
            
        } else {
            $option_text = 'SP không tồn tại trên Web';
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
            case 'sku':
                $r = $ip_product['product_code'];
                break;
            case 'name':
                $r = $ip_product['product_name'];
                break;
            case 'quantity':
                $r = $ip_product['amount_info'];
                break;
            case 'woo':
                if ($woo_product['id']) {
                    $r = $woo_product['woo_text'];
                } else {
                    $r = '<span style="color:red; font-weight: bold;">KHÔNG TỒN TẠI TRÊN WEB</span>';
                }
                break;
            case 'options':
                
                if ($woo_product['id']) {
                    
                    $show_setPreOrder = false;
                    
                    if (!$woo_product['stock']) {
                        $show_setPreOrder = true;
                    }
                    
                    if ($show_setPreOrder) {
                        $r .= '  <button id="setPreOrder_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-success" title="Cập nhật có hàng trên Web cho sản phẩm này" onclick="setPreOrder('. $woo_product['id'] .');"><i class="fa fa-tasks"></i>  Set Pre-Order</button>';
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
