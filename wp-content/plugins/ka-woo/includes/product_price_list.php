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

class Kawoo_Product_Price_List extends WP_List_Table {

    private $dbModel;
    private $show_type = 1;
    private $show_products_per_page = 10;
    private $selected_categories = [];

    function __construct($show_type = 1, $show_products = 10, $selected_categories = []) {
        $args = array();
        parent::__construct($args);
        $this->dbModel = new WooDbModel();
        $this->show_type = $show_type;
        $this->show_products_per_page = $show_products;
//        $this->image_link = $image_link;
        $this->selected_categories = $selected_categories;
    }
    
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $perPage = $this->show_products_per_page;
        $currentPage = $this->get_pagenum();
        $list_product = array();

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
                    $temp_products = $this->get_product_only_regular_price($theid);
                    if (!empty($temp_products)) {
                        $list_product = array_merge($list_product, $temp_products);
                        $count_product++;
                    }
                }

                if ($count_product >= $show_products) {
                    break;
                }

                if ($count_product >= $show_products) {
                    break;
                }

            endwhile;
            wp_reset_query();

        }

//                break;


        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $list_product;
    }
    
    public function get_product_only_regular_price($product_id) {

        $return_products = array();

        $prod = wc_get_product($product_id);

        if ($prod && $prod->is_type('variable') && $prod->has_child()) {

            $variations = $this->dbModel->get_children_ids($product_id);
            foreach ($variations as $child) {
                if ($child) {
                    
                    $product = wc_get_product($child['ID']);
                    
                    $regular_price = $product->get_regular_price();
                    $sale_price = $product->get_sale_price();
                    
                    if ($regular_price && !$sale_price) {
                        $return_products[] = $child['ID'];
                    }
                }
            }
        } elseif ($prod && $prod->is_type('simple')) {

            $temp_item = array();
            $temp_item['woo'] = $product_id;

            $product = wc_get_product($product_id);
            
            $regular_price = $product->get_regular_price();
            $sale_price = $product->get_sale_price();

            if ($regular_price && !$sale_price) {
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
            'regular_price' => 'Giá Gốc',
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
                $r = "{$woo_product['name']}<br/>-Mã: <b>{$woo_product['sku']}</b> -{$woo_product['stock_status']}";
                break;
            case 'store':
                $store = get_post_meta($woo_product['id'], '_mypos_other_store', true);
                if ($store && $store == 'yes') {
                    $r = get_option('kiotviet2_name');
                } else {
                    $r = get_option('kiotviet_name');
                }
                break;
            case 'regular_price':
                $regular_price = $product->get_regular_price();
                $r = '<b>' . kiotViet_formatted_price($regular_price) . '</b>';
                break;
            case 'price-options':
                $r = '  <button id="get_price_popup_' . $woo_product['id'] . '" type="button" class="btn btn-mypos btn-danger" title="Cập nhật giá gốc thành sale cho sản phẩm này" onclick="get_price_popup('. $woo_product['id'] .');"><i class="fa fa-tasks"></i> Chuyển giá gốc thành giá sale</button>';
                break;
            default:
                return print_r($item, true);
        }

        return $r;
    }

}
