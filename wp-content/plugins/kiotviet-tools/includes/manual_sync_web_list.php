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
        
        $totalItems = $this->dbModel->get_count_woo_product();
        
        $list_product = array();
        $loop = new WP_Query( array( 'post_type' => array('product'), 'posts_per_page' => $perPage, 'paged' => $currentPage ) );
        
        $list_product = array();
        
        while ( $loop->have_posts() ) : $loop->the_post();
                
            $theid = get_the_ID();

            // add product to array but don't add the parent of product variations
            if ($theid) {
                $list_product[] = $theid;
            }
        
        endwhile; 
        wp_reset_query();
        
        
        $this->set_pagination_args( array(
            'total_items' => $totalItems,
            'per_page'    => $perPage
        ) );

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $list_product;
        
    }
    
    public function single_row( $product_id ) {
        
        $prod = wc_get_product( $product_id );
        
        if ( $prod && $prod->is_type( 'variable' ) && $prod->has_child() ) {
          
            $args = array(
                'post_type'     => 'product_variation',
                'post_status'   => array( 'private', 'publish' ),
                'post_parent'   => $product_id // 
            );
            
            $variations = get_posts( $args );
            
            foreach ($variations as $child) {
                if ( $child ) {
                    
                    $temp_item = array();
                    $temp_item['woo'] = $child->ID;
                    
                    $sku = get_post_meta($child->ID, '_sku', true);
                    
                    // KiotViet Process
                    if ($sku) {
                        $kv_product = $this->kv_api->get_product_info_by_productSKU($sku);
                    } else {
                        $kv_product = array();
                    }
                    
                    $temp_item['kv'] = $kv_product;
                    
                    $nothing_sync = false;
                    
                    if (!empty($kv_product)) {
                        
                        $product = wc_get_product($child->ID);
                        
                        $woo_product['price'] = $product->get_price();
                        $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
                        
                        if (($kv_product['stock'] == $woo_product['stock']) 
                            && ($kv_product['price'] == $woo_product['price'])) {
                            // nothing to show options
                                $nothing_sync = true;
                            }
                    }
                    
                    if ($nothing_sync) {
                        // nothing to sync => hide this row
                    } else {
                        echo '<tr>';
                        $this->single_row_columns( $temp_item );
                        echo '</tr>';
                    }
                    
                }
            }
            
        } elseif ($prod && $prod->is_type( 'simple' )) {
            
            $temp_item = array();
            $temp_item['woo'] = $product_id;
            
            $sku = get_post_meta($product_id, '_sku', true);
                    
            // KiotViet Process
            if ($sku) {
                $kv_product = $this->kv_api->get_product_info_by_productSKU($sku);
            } else {
                $kv_product = array();
            }

            $temp_item['kv'] = $kv_product;
            
            $nothing_sync = false;
                    
            if (!empty($kv_product)) {

                $product = wc_get_product($product_id);

                $woo_product['price'] = $product->get_price();
                $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;

                if (($kv_product['stock'] == $woo_product['stock']) 
                    && ($kv_product['price'] == $woo_product['price'])) {
                    // nothing to show options
                        $nothing_sync = true;
                    }
            }

            if ($nothing_sync) {
                // nothing to sync => hide this row
            } else {
                echo '<tr>';
                $this->single_row_columns( $temp_item );
                echo '</tr>';
            }
        }
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
        
        $product_id      = $item['woo'];
        $product         = wc_get_product( $product_id );
//        $product_type = '';
        
        if ($product->is_type( 'variation' )) {
            $base_product_id = $product->get_parent_id();
//            $product_type = 'Biến thể';
        } elseif ($product->is_type( 'simple' )) {
            $base_product_id = $product_id;
//            $product_type = 'SP Đơn';
        } else {
            $base_product_id = $product_id;
//            $product_type = 'SP Cha';
        }
        $edit_link       = get_edit_post_link( $base_product_id );
        $product_link   = get_permalink($base_product_id);
        
        $woo_product['id'] = $product->get_id();
        $woo_product['sku'] = $product->get_sku();
        $woo_product['name'] = $product->get_name();
        $woo_product['price'] = $product->get_price();
        $woo_product['stock'] = ($product->get_stock_status() == 'instock') ? true : false;
//        $woo_product['quantity'] = $product->get_stock_quantity();
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
        
        // KiotViet Process
        $kv_product = array();
        $kv_text = '';
        $option_text = '';
        
        if ($woo_product['sku']) {
            
            $kv_product = $item['kv'];
            
            if (!empty($kv_product)) {
                if ($kv_product['stock']) {
                    $kv_product['stock_status'] = '<span style="color:green; font-weight: bold;">Còn hàng</span>';
                } else {
                    $kv_product['stock_status'] = '<span style="color:red; font-weight: bold;">Hết hàng</span>';
                }
                
               $formated_price = kiotViet_formatted_price($kv_product['price']);
               $kv_text = "{$kv_product['name']}<br/>-Mã: <b>{$kv_product['sku']}</b> -{$kv_product['stock_status']} ({$kv_product['quantity']}) -Giá: {$formated_price}";
            } else {
                $option_text = 'SP không tồn tại trên KiotViet';
            }
                    
        } else {
            $option_text = 'Không có mã SP';
        }
        
        
        switch( $column_name ) {
            case 'id':
                $r = $product_id;
                break;
            case 'edit':
                $r .= '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>';
                $r .= '<a href="' . $product_link . '" target="_blank"><span class="dashicons dashicons-admin-site"></span></a>';
                break;
            case 'woo':
                $formated_price = kiotViet_formatted_price($woo_product['price']);
                $r = "{$woo_product['name']}<br/>-Mã: <b>{$woo_product['sku']}</b> -{$woo_product['stock_status']} -Giá: {$formated_price}";
                break;
            case 'kv':
                $r = $kv_text;
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
                        
                        $formated_price = kiotViet_formatted_price($kv_product['price']);
                        
                        $confirm_text = "Xác nhận sửa giá " . $woo_product['name'] . " thành " . number_format($kv_product['price'], 0, ',', '.') . " đ (theo kiotviet)?";
                        $r .= '  <button id="updateWebPrice_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-info" title="Cập nhật giá trên Web cho sản phẩm này theo giá trên KiotViet" onclick="updateWebPrice_byKVPrice('. $woo_product['id'] .',' . $kv_product['price'] . ',\'' . $confirm_text .  '\');"><i class="fa fa-anchor"></i>  Update giá web = ' . $formated_price . ' (theo Kiotviet)</button>';
                        // an de dung sau
//                        $r .= '  <button id="updateKVPrice_' . $kv_product['id'] . '" type="button" class="btn btn-mypos btn-warning" title="Cập nhật giá trên KiotViet cho sản phẩm này theo giá trên Web" onclick="updateKVPrice_byWebPrice('. $kv_product['id'] .',' . $woo_product['price'] . ');"><i class="fa fa-anchor"></i>  Cập nhật giá KiotViet theo Web</button>';
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
