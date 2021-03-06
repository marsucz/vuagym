<?php

/**
 * Description of KiotViet_ManualSyncWeb_List
 *
 * @author dmtuan
 */

require_once('WooDbModel.php');

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

// Tab: Quản lý sản phẩm
class Kawoo_Product_Search_List extends WP_List_Table {

    private $dbModel;
    private $show_type = 1;
    private $show_products_per_page = 10;
    private $finding_product_code = '';
    private $search_advance = 1;
    private $catalog_advance = 1;

    function __construct($show_type = 1, $show_products = 10, $finding_product_code = "", $search_advance = 1, $catalog_advance = 1) {
        $args = array();
        parent::__construct($args);
        $this->dbModel = new WooDbModel();
        $this->show_type = $show_type;
        $this->show_products_per_page = $show_products;
//        $this->image_link = $image_link;
        $this->finding_product_code = $finding_product_code;
        $this->search_advance = $search_advance;
        $this->catalog_advance = $catalog_advance;
    }
    
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $perPage = $this->show_products_per_page;
        $currentPage = $this->get_pagenum();
        $list_product = array();
        
        switch ($this->show_type) {
            
            case 1: // Tìm kiếm sản phẩm theo Mã sản phẩm
                if ($this->search_advance == 1) {
                        $product_id = wc_get_product_id_by_sku($this->finding_product_code);
                        if ($product_id) {
                            $list_product[] = $product_id;
                        }
                } else {
                        $list_prod = $this->dbModel->search_product_like_sku($this->finding_product_code);
                        if (count($list_prod) > 0) {
                            foreach ($list_prod as $prod) {
                                $list_product[] = $prod['post_id'];
                            }
                        }
                }
                break;

            case 2: // Hien các sản phẩm thuộc kho ngoài
        
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
                            $temp_products = $this->get_product_in_other_store($theid);
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
                
                break;  // break case 2
                
            case 3: // Lọc sản phẩm Luôn Hiện
        
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
                            
                            $show_always = get_post_meta($theid, '_mypos_show_always', true);
                            
                            if ($show_always == 'yes') {
                                $list_product[] = $theid;
                                $count_product++;
                            }
                        }

                        if ($count_product >= $show_products) {
                            break;
                        }

                    endwhile;
                    wp_reset_query();

                }
                
                break;  // break case 3
//            case 4: // Shoppe ở Class khác
            case 5: // Mức độ hiển thị catalog
        
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
                            $product = wc_get_product($theid);
                            $status = $product->get_catalog_visibility();
                            switch($this->catalog_advance) {
                                case 1: //Cửa hàng và kết quả tìm kiếm
                                    if ($status == 'visible') {
                                        $list_product[] = $theid;
                                        $count_product++;
                                    }
                                    break;
                                case 2: //Chỉ cửa hàng
                                    if ($status == 'catalog') {
                                        $list_product[] = $theid;
                                        $count_product++;
                                    }
                                    break;
                                case 3: //Chỉ tìm kiếm kết quả
                                    if ($status == 'search') {
                                        $list_product[] = $theid;
                                        $count_product++;
                                    }
                                    break;
                                case 4: //Ẩn
                                    if ($status == 'hidden') {
                                        $list_product[] = $theid;
                                        $count_product++;
                                    }
                                    break;
                            }
                        }

                        if ($count_product >= $show_products) {
                            break;
                        }

                    endwhile;
                    wp_reset_query();

                }
                break;  // break case 5    
        }

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $list_product;
    }
    
    public function get_product_in_other_store($product_id) {

        $return_products = array();

        $prod = wc_get_product($product_id);

        if ($prod && $prod->is_type('variable') && $prod->has_child()) {

            $variations = $this->dbModel->get_children_ids($product_id);
            foreach ($variations as $child) {
                if ($child) {
                    $store = get_post_meta($child['ID'], '_mypos_other_store', true);
                    if ($store && $store == 'yes') {
                        $return_products[] = $child['ID'];
                    }
                }
            }
        } elseif ($prod && $prod->is_type('simple')) {
            $store = get_post_meta($child['ID'], '_mypos_other_store', true);
            if ($store && $store == 'yes') {
                $return_products[] = $product_id;
            }
        }

        return $return_products;
    }
    
    public function single_row($item) {
        echo '<tr>';
        $this->single_row_columns($item);
        echo '</tr>';
    }

    public function get_columns() {
        $columns = array(
//            'no'        => 'STT',
            'id' => 'ID',
            'edit' => '<span class="dashicons dashicons-admin-generic"></span>',
            'product' => 'Sản Phẩm',
            'store'     => 'Kho Hàng',
            'price-options' => 'Tùy Chọn',
        );
        return $columns;
    }

    public function get_hidden_columns() {
        return array('id');
    }

    public function get_sortable_columns() {
        return array();
    }
    
    private function get_product_category_by_id( $category_id ) {
        $term = get_term_by( 'id', $category_id, 'product_cat', 'ARRAY_A' );
        return $term['name'];
    }

    public function column_default($item, $column_name) {
        $r = '';

        $product_id      = $item;
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

        

        switch ($column_name) {
            case 'id':
                $r = $product_id;
                break;
            case 'edit':
                $r .= '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>';
                $r .= '<a href="' . $product_link . '" target="_blank"><span class="dashicons dashicons-admin-site"></span></a>';
                break;
            case 'product':
                $formated_price = kiotViet_formatted_price($woo_product['price']);
                $r = "{$woo_product['name']}<br/>-Mã: <b>{$woo_product['sku']}</b> -{$woo_product['stock_status']} -Giá: {$formated_price}";
                break;
            case 'store':
                $store = get_post_meta($woo_product['id'], '_mypos_other_store', true);
                if ($store && $store == 'yes') {
                    $r = get_option('kiotviet2_name');
                } else {
                    $r = get_option('kiotviet_name');
                }
                break;
            case 'price-options':
                $r = '';
                break;
            default:
                return print_r($item, true);
        }

        return $r;
    }

}
