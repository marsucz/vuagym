<?php

/**
 * Description of KiotViet_ManualSyncWeb_List
 *
 * @author dmtuan
 */

require_once('DbModel.php');

if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class Kawoo_Product_Category_List extends WP_List_Table {

//    private $dbModel;
    private $show_type = 1;
    private $show_products_per_page = 10;
    private $selected_categories = [];

    function __construct($show_type = 1, $show_products = 10, $selected_categories = []) {
        $args = array();
        parent::__construct($args);
//        $this->dbModel = new DbModel();
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
                    $product = wc_get_product($theid);
                    $categories = $product->get_category_ids();
                    
                    switch ($this->show_type) {
                        case 1: // San pham chi thuoc danh muc
                            if (count($categories) == 1) {
                                if (!empty($this->selected_categories)) {
                                    foreach ($this->selected_categories as $selected_cate) {
                                        if ($categories[0] == $selected_cate) {
                                            $list_product[] = $theid;
                                            $count_product++;
                                        }
                                    }
                                } else {
                                    $list_product[] = $theid;
                                    $count_product++;
                                }
                            }
                            break;
                        case 2: // San pham thuoc danh muc
                            if (!empty($this->selected_categories)) {
                                foreach ($this->selected_categories as $selected_cate) {
                                    if ($categories[0] == $selected_cate) {
                                        $list_product[] = $theid;
                                        $count_product++;
                                    }
                                }
                            } else {
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

//                break;


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
            'category' => 'Danh Mục',
            'options' => 'Tùy Chọn',
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
        $woo_product['name'] = mypos_get_variation_title($product);
        

        

        switch ($column_name) {
            case 'id':
                $r = $product_id;
                break;
            case 'edit':
                $r .= '<a href="' . $edit_link . '" target="_blank"><span class="dashicons dashicons-admin-generic"></span></a>';
                $r .= '<a href="' . $product_link . '" target="_blank"><span class="dashicons dashicons-admin-site"></span></a>';
                break;
            case 'product':
                $r = $woo_product['name'];
                break;
            case 'category':
                $categories = $product->get_category_ids();
                $category_name = '';
                if ($categories) {
                    $category_obj = [];
                    foreach ($categories as $cate) {
                        $category_obj[] = $this->get_product_category_by_id($cate);
                    }
                    $category_name = implode(', ', $category_obj);
                }
                $r = $category_name;
                break;
            case 'options':
                
                break;
            default:
                return print_r($item, true);
        }

        return $r;
    }

}
