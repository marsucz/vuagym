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
    private $sync_type;
    private $is_submit;
    
    function __construct($show_type = 1, $show_products_per_page, $list_shoppe = array(), &$spreadsheet, $sync_type = 'manual', $is_submit = false) {
        $args = array();
        parent::__construct($args);
        $this->kv_api = new KiotViet_API(1);
        $this->kv2_api = new KiotViet_API(2);
        $this->dbModel = new DbModel();
        $this->show_type = $show_type;
        $this->worksheet = $spreadsheet->getActiveSheet();
        $this->show_products_per_page = $show_products_per_page;
        $this->list_shoppe = $list_shoppe;
        $this->sync_type = $sync_type;
        $this->is_submit = $is_submit;
        
        ini_set('memory_limit', '2048M');
        set_time_limit(1200);
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
    
    public function get_shoppe_product_by_code($shoppe_sku) {
        foreach ($this->list_shoppe as $key => $shoppe) {
            if ($shoppe['sku'] == $shoppe_sku) {
                $this->list_shoppe[$key]['checked'] = true;
                return $shoppe;
            }
        }
        return [];
    }
    
    // Get shoppe products not exists on Web/KiotViet
    public function get_shoppe_product_list() {
        $return_products = array();
        foreach ($this->list_shoppe as $key => $shoppe) {
            if (!isset($shoppe['checked'])) {
                
                $temp['woo'] = array();
                $temp['shoppe'] = $shoppe;
                if ($shoppe['sku']) {
                    $temp['kv'] = $this->get_kv_product_by_code($shoppe['sku']);
                    if (empty($temp['kv'])) {
                        $temp['kv'] = $this->get_kv2_product_by_code($shoppe['sku']);
                    }
                }
                
                $return_products[] = $temp;
            }
        }
        
        return $return_products;
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
        
        // Auto Sync
        if ($this->is_submit == true && $this->sync_type == 'auto') {
            
            $list_sync = array();
            foreach ($this->list_shoppe as $shoppe) {
                // add product to array but don't add the parent of product variations
                $sync_product = $this->process_shoppe_auto_sync($shoppe);
                if (!empty($sync_product)) {
                    $list_sync[] = $sync_product;
                }
            }
            
            foreach ($list_sync as $sync_product) {
                $shoppe = $sync_product['shoppe'];
                $kv_product = $sync_product['kv'];
                if (!empty($shoppe) && (!empty($kv_product))) {
                    if ($shoppe['price'] != $kv_product['price']) {
                        $this->worksheet->getCell($shoppe['price_pos'], false)->setValue($kv_product['price']);
                    }
                    if ($shoppe['quantity'] != $kv_product['quantity']) {
                        $this->worksheet->getCell($shoppe['quantity_pos'], false)->setValue($kv_product['quantity']);
                    }
                }
            }
            
        }
        
        switch ($this->show_type) {
            case 1: // Hien thi tat ca cac san pham
                $currentPage = $this->get_pagenum();
                // show product one times
                $show_products = $this->show_products_per_page;
                    
//                $loop = new WP_Query( array( 'post_type' => array('product'), 'posts_per_page' => $perPage, 'paged' => $currentPage ) );
                $loop = $this->dbModel->kapos_get_products($perPage, $currentPage);
//                    if (!$loop->post_count || $loop->post_count == 0) {
//                        break;
//                    }
                    
                    foreach ($loop as $product_id) {
//                        $theid = get_the_ID();
                        // add product to array but don't add the parent of product variations
//                        if ($theid) {
                            $temp_products = $this->get_product_show_type_all($product_id);
                            if (!empty($temp_products)) {
                                $list_product = array_merge($list_product, $temp_products);
                            }
//                        }
                    }
                    
                $this->set_pagination_args( array(
                    'total_items' => $totalItems,
                    'per_page'    => $perPage
                ) );
                
                break;

            case 2: // Chi hien thi cac san pham chua dong bo

                $currentPage = $this->get_pagenum();
                // show product one times
                $show_products = $this->show_products_per_page;
                    
                $loop = $this->dbModel->kapos_get_products($perPage, $currentPage);

//                    if (!$loop->post_count || $loop->post_count == 0) {
//                        break;
//                    }
                   foreach ($loop as $product_id) { 
//                    while ( $loop->have_posts() ) : $loop->the_post();
//                        $theid = get_the_ID();
                        // add product to array but don't add the parent of product variations
//                        if ($theid) {
                            $temp_products = $this->get_product_show_type_only_not_sync($product_id);
                            if (!empty($temp_products)) {
                                $list_product = array_merge($list_product, $temp_products);
                            }
//                        }
                   }
//                    endwhile;
                    
//                wp_reset_query();
                
                $this->set_pagination_args( array(
                    'total_items' => $totalItems,
                    'per_page'    => $perPage
                ) );
                break;

            default:
                break;
        }

        $this->_column_headers = array($columns, $hidden, $sortable);
        
//        echo "<pre>";
//        print_r($list_product);
//        print_r($this->list_shoppe);
//        echo "</pre>";
//        exit;
        
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

            $variations = $this->dbModel->get_children_ids($product_id);
            foreach ($variations as $child) {
                if ( $child ) {

                    $temp_item = array();
                    $temp_item['woo'] = $child['ID'];
                    $temp_item['base'] = $product_id;
                    
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
                        
                        // Shoppe
                        $shoppe_product = $this->get_shoppe_product_by_code($sku);
                    } else {
                        $kv_product = array();
                        $shoppe_product = array();
                    }

                    $temp_item['kv'] = $kv_product;
                    $temp_item['shoppe'] = $shoppe_product;

                    $return_products[] = $temp_item;
                    
                }
            }

        } elseif ($prod && $prod->is_type( 'simple' )) {
            
            $temp_item = array();
            $temp_item['woo'] = $product_id;
            $temp_item['base'] = $product_id;
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
                
                // Shoppe
                $shoppe_product = $this->get_shoppe_product_by_code($sku);
            } else {
                $kv_product = array();
            }

            $temp_item['kv'] = $kv_product;
            if (!empty($shoppe_product)) {
                $temp_item['shoppe'] = $shoppe_product;
            } else {
                $temp_item['shoppe'] = array();
            }
            
            $return_products[] = $temp_item;
        }

        return $return_products;
    }
    
    public function process_shoppe_auto_sync($shoppe) {

        $return_products = array();
        
        if ($shoppe['sku'] == '') {
            $temp_item['shoppe'] = $shoppe;
            $temp_item['woo'] = array();
            $temp_item['kv'] = array();
            
            $return_products = $temp_item;
            return $return_products;
        }
        
        $product_id = wc_get_product_id_by_sku($shoppe['sku']);

        $prod = wc_get_product( $product_id );

        $temp_item = array();
        $temp_item['woo'] = $product_id;

//        $sku = get_post_meta($product_id, '_sku', true);
        $sku = $shoppe['sku'];

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

        $return_products = $temp_item;

        return $return_products;
    }

    public function get_product_show_type_only_not_sync($product_id) {
        $return_products = array();

        $prod = wc_get_product( $product_id );

        if ( $prod && $prod->is_type( 'variable' ) && $prod->has_child() ) {

            $variations = $this->dbModel->get_children_ids($product_id);
            foreach ($variations as $child) {
                if ( $child ) {

                    $temp_item = array();
                    $temp_item['woo'] = $child['ID'];
                    $temp_item['base'] = $product_id;
                    
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
                        
                        // Shoppe
                        $shoppe_product = $this->get_shoppe_product_by_code($sku);
                    } else {
                        $kv_product = array();
                        $shoppe_product = array();
                    }

                    $need_sync = false;
                    if (empty($kv_product) || empty($shoppe_product)) {
                        $need_sync = true;
                    }
                    if (!empty($kv_product) && !empty($shoppe_product) && ($shoppe_product['price'] != $kv_product['price'] || $shoppe_product['quantity'] != $kv_product['quantity'])) {
                        $need_sync = true;
                    }
                    if ($need_sync) {
                        $temp_item['kv'] = $kv_product;
                        $temp_item['shoppe'] = $shoppe_product;

                        $return_products[] = $temp_item;
                    }
                }
            }

        } elseif ($prod && $prod->is_type( 'simple' )) {
            
            $temp_item = array();
            $temp_item['woo'] = $product_id;
            $temp_item['base'] = $product_id;
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
                
                // Shoppe
                $shoppe_product = $this->get_shoppe_product_by_code($sku);
            } else {
                $kv_product = array();
            }
            
            $need_sync = false;
            if (empty($kv_product) || empty($shoppe_product)) {
                $need_sync = true;
            }
            if (!empty($kv_product) && !empty($shoppe_product) && ($shoppe_product['price'] != $kv_product['price'] || $shoppe_product['quantity'] != $kv_product['quantity'])) {
                $need_sync = true;
            }
            if ($need_sync) {
                $temp_item['kv'] = $kv_product;
                if (!empty($shoppe_product)) {
                    $temp_item['shoppe'] = $shoppe_product;
                } else {
                    $temp_item['shoppe'] = array();
                }

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
        
        // KiotViet Process
        $kv_product = array();
        $kv_text = '';
        $option_text = '';
        
        if (!$product_id) {
            $option_text = 'SP không tồn tại trên Web';
        } else {
            $sku = get_post_meta($product_id, '_sku', true);
            if ($sku) {
                //
            } else {
                $option_text = 'Không có mã SP trên Web';
            }
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
        
        if ($shoppe['id'] != '') {
            if ($shoppe['sku'] == '') {
                if ($option_text) $option_text .= '<br/>';
                $option_text .= '<span style="color:red; font-weight: bold;">Sản phẩm ID ' . $shoppe['id'] . ' không có mã SP</span>';
            }
        } else {
            if ($option_text) $option_text .= '<br/>';
            $option_text .= '<span style="color:red; font-weight: bold;">Không có sản phẩm trên Shoppe</span>';
        }
        
        switch ($column_name) {
            case 'id':
                $r = $product_id;
                break;
            case 'edit':
                if ($product_id) {
                    $edit_link       = get_edit_post_link($item['base']);
                    $product_link   = get_permalink($item['base']);
                    $r .= '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>';
                    $r .= '<a href="' . $product_link . '" target="_blank"><span class="dashicons dashicons-admin-site"></span></a>';
                }
                break;
            case 'sp_woo':
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
                
                if ($shoppe['id'] != '') {
                    if ($shoppe['quantity'] > 0) {
                        $stock_text = '<span style="color:green; font-weight: bold;">Còn hàng</span> (' . $shoppe['quantity'] . ')';
                    } else {
                        $stock_text = '<span style="color:red; font-weight: bold;">Hết hàng</span> (' . $shoppe['quantity'] . ')';
                    }

                    $formated_price = kiotViet_formatted_price($shoppe['price']);
                    $r = "{$shoppe['name']}<br/>-Mã: <b>{$shoppe['sku']}</b> -{$stock_text} -Giá: {$formated_price}";
                } else {
                    $r = "";
                }
                
                break;
            case 'sp_store':
                if ($product_id) {
                    $store = get_post_meta($product_id, '_mypos_other_store', true);
                    if ($store && $store == 'yes') {
                        $r = get_option('kiotviet2_name');
                    } else {
                        $r = get_option('kiotviet_name');
                    }
                } else {
                    $r = 'Không xác định';
                }
                break;
            case 'sp_options':

                if ($this->sync_type != 'auto' && !empty($kv_product) && !empty($shoppe)) {
                    if ($shoppe['price'] != $kv_product['price']){
                        if (!isset($_SESSION['price'][$product_id]) || empty($_SESSION['price'][$product_id])) {
                            $name_string = str_replace("'", "", $shoppe['name']);
                            $confirm_text = "Xác nhận sửa giá shoppe " . $name_string . " thành " . number_format($kv_product['price'], 0, ',', '.') . " đ (theo kiotviet)?";
                            $r .= '  <button id="updateShoppePrice_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-warning" title="Cập nhật giá trên Shoppe cho sản phẩm này theo giá trên KiotViet" onclick="updateExcelCellPrice(this,\''. $product_id .'\',\'price\',\''. $shoppe['price_pos'] .'\',\'' . $kv_product['price'] . '\',\'' . $confirm_text .  '\');"><i class="fa fa-anchor"></i>  Update giá shoppe = ' . $formated_price . ' (theo Kiotviet)</button>';
                        } else {
                            $shoppe_price = kiotViet_formatted_price($shoppe['price']);
                            $kv_price = kiotViet_formatted_price($kv_product['price']);
                            if ($r) $r .= '<br/>';
                            $r .= "[{$shoppe['price_pos']}] Đã cập nhật giá bán SP từ {$shoppe_price} thành {$kv_price}";
                        }
                        
                    }
                    
                    if ($shoppe['quantity'] != $kv_product['quantity']) {
                        if (!isset($_SESSION['quantity'][$product_id]) || empty($_SESSION['quantity'][$product_id])) {
                            $r .= '  <button id="updateShoppeStock_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-info" title="Cập nhật số lượng trên Shoppe cho sản phẩm này theo số lượng trên KiotViet" onclick="updateExcelCell(this,\''. $product_id .'\',\'quantity\',\''. $shoppe['quantity_pos'] .'\',\'' . $kv_product['quantity'] . '\');"><i class="fa fa-tasks"></i>  Update tồn kho shoppe = ' . $kv_product['quantity'] . ' (theo Kiotviet)</button>';
                        } else {
                            if ($r) $r .= '<br/>';
                            $r .= "[{$shoppe['quantity_pos']}] Đã cập nhật tồn kho SP từ {$shoppe['quantity']} thành {$kv_product['quantity']}";
                        }
                    }
                }
                
                $r .= $option_text;

                $update_shoppe_text = '';
                // Update Excel file
                if ($this->sync_type == 'auto' && !empty($kv_product) && !empty($shoppe)) {
                    if ($shoppe['price'] != $kv_product['price']) {
//                        $this->worksheet->getCell($shoppe['price_pos'], false)->setValue($kv_product['price']);
                        $shoppe_price = kiotViet_formatted_price($shoppe['price']);
                        $kv_price = kiotViet_formatted_price($kv_product['price']);
                        if ($update_shoppe_text) $update_shoppe_text .= '<br/>';
                        $update_shoppe_text .= "[{$shoppe['price_pos']}] Đã cập nhật giá bán SP từ {$shoppe_price} thành {$kv_price}";
                    }
                    if ($shoppe['quantity'] != $kv_product['quantity']) {
//                        $this->worksheet->getCell($shoppe['quantity_pos'], false)->setValue($kv_product['quantity']);
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
