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
class Kawoo_Product_SanTMDT_List extends WP_List_Table {

    private $dbModel;
    private $show_type = 1;
    private $show_products_per_page = 10;
    private $shoppe_advance = 1;

    function __construct($show_type = 1, $show_products = 10, $shoppe_advance = 1) {
        $args = array();
        parent::__construct($args);
        $this->dbModel = new WooDbModel();
        $this->show_type = $show_type;
        $this->show_products_per_page = $show_products;
        $this->shoppe_advance = $shoppe_advance;
    }
    
    public function prepare_items()
    {
        $columns = $this->get_columns();
        $hidden = $this->get_hidden_columns();
        $sortable = $this->get_sortable_columns();

        $perPage = $this->show_products_per_page;
        $list_product = array();
        
        switch ($this->shoppe_advance) {
            
            case 1: // Hien thi tat ca san pham
                
                $currentPage = $this->get_pagenum();
                $totalItems = $this->dbModel->get_count_woo_product();
                
                $loop = new WP_Query( array( 'post_type' => array('product'), 'posts_per_page' => $perPage, 'paged' => $currentPage ) );

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

                break;
            case 2: // Chi hien thi cac san pham chua co Shoppe
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
                            $shoppe = get_post_meta($theid, '_ka_shoppe', true);
                            if ($shoppe && $shoppe == 'yes') {
                                $ka_shoppe_type = get_post_meta($theid, '_ka_shoppe_type', true);
                                if ($ka_shoppe_type == 'link') {
                                    $shoppe_link = get_post_meta($theid, '_ka_shoppe_link', true);
                                    if (!$shoppe_link || $shoppe_link == '') {
                                        $list_product[] = $theid;
                                        $count_product++;
                                    }
                                } else {
                                    $shoppe_content = get_post_meta($theid, '_ka_shoppe_content', true);
                                    if (!$shoppe_content || $shoppe_content == '') {
                                        $list_product[] = $theid;
                                        $count_product++;
                                    }
                                }
                            } else {
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
                
                break;  // break case 2
                
            case 3: // Chi hien thi cac san pham da co Shoppe
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
                            $shoppe = get_post_meta($theid, '_ka_shoppe', true);
                            if ($shoppe && $shoppe == 'yes') {
                                $ka_shoppe_type = get_post_meta($theid, '_ka_shoppe_type', true);
                                if ($ka_shoppe_type == 'link') {
                                    $shoppe_link = get_post_meta($theid, '_ka_shoppe_link', true);
                                    if ($shoppe_link != '') {
                                        $list_product[] = $theid;
                                        $count_product++;
                                    }
                                } else {
                                    $shoppe_content = get_post_meta($theid, '_ka_shoppe_content', true);
                                    if ($shoppe_content != '') {
                                        $list_product[] = $theid;
                                        $count_product++;
                                    }
                                }
                            }
                        }

                        if ($count_product >= $show_products) {
                            break;
                        }

                    endwhile;
                    wp_reset_query();

                }
                
                break;  // break case 3
        }

        $this->_column_headers = array($columns, $hidden, $sortable);
        $this->items = $list_product;
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
            'tmdt_type'     => 'Loại Tab',
            'tmdt_content'     => 'Nội dung Shoppe',
            'tmdt_options' => 'Tùy Chọn',
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
            case 'tmdt_type':
                $shoppe = get_post_meta($woo_product['id'], '_ka_shoppe', true);
                if ($shoppe && $shoppe == 'yes') {
                    $r = 'Shoppe: <span style="color:green; font-weight: bold;">' . strtoupper(get_post_meta($woo_product['id'], '_ka_shoppe_type', true)) . '</span>';
                } else {
                    $r = "Chưa có Shoppe";
                }
                break;
            case 'tmdt_content':
                $shoppe = get_post_meta($woo_product['id'], '_ka_shoppe', true);
                if ($shoppe && $shoppe == 'yes') {
                    $shoppe_type = get_post_meta($woo_product['id'], '_ka_shoppe_type', true);
                    if ($shoppe_type == 'link') {
                        $r = get_post_meta($woo_product['id'], '_ka_shoppe_link', true);
                    } else {
                        $r = get_post_meta($woo_product['id'], '_ka_shoppe_content', true);
                    }
                } else {
                    $r = "";
                }
                break;
            case 'tmdt_options':
                $r = '';
                break;
            default:
                return print_r($item, true);
        }

        return $r;
    }

}
