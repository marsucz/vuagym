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

class Kawoo_Product_Content_List extends WP_List_Table {

    private $dbModel;
    private $show_type = 1;
    private $show_products_per_page = 10;
    private $selected_categories = [];
    private $finding_text = '';

    function __construct($show_type = 1, $show_products = 10, $selected_categories = [], $finding_text = '') {
        $args = array();
        parent::__construct($args);
        $this->dbModel = new DbModel();
        $this->show_type = $show_type;
        $this->show_products_per_page = $show_products;
        $this->finding_text = !empty($finding_text) ? trim($finding_text) : '';
        
        $this->selected_categories = $selected_categories;
        if (empty($selected_categories)) {
            $this->selected_categories = 0;
        } else {
            foreach ($selected_categories as $cat) {
                if ($cat == 0) {
                    $this->selected_categories = 0;
                    break;
                }
            }
        }
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
            
            case 1: // Sản phẩm chưa có mô tả ngắn
                
                $perPage = 50;
                $currentPage = 0;

                // show product one times
                $show_products = $this->show_products_per_page;
                $count_product = 0;

                while ($count_product < $show_products) {

                    $currentPage++;
                    
                    $loop_args = array( 'post_type' => array('product'), 
                                'posts_per_page' => $perPage, 
                                'paged' => $currentPage );
                    
                    if ($this->selected_categories != 0) {
                        $loop_args['tax_query'] = array(
                                array(
                                'taxonomy' => 'product_cat',
                                'field' => 'term_id',
                                'terms' => array_values($this->selected_categories),
                                'operator' => 'IN'
                                )
                        );
                    }
                    
                    $loop = new WP_Query( $loop_args );
                    
                    if (!$loop->post_count || $loop->post_count == 0) {
                        break;
                    }

                    while ( $loop->have_posts() ) : $loop->the_post();
                        
                        $theid = get_the_ID();
                        $post_temp = get_post($theid);
                        
                        if (empty($this->finding_text)) {
                            $check_empty = empty($post_temp->post_excerpt);
                            if (!$check_empty && is_string($post_temp->post_excerpt)) {
                                $check_empty = strlen($post_temp->post_excerpt) < 3 ? true : false;
                            }
                            if ($post_temp && $check_empty) {
                                $list_product[] = $theid;
                                $count_product++;
                            }
                        } else {
                            if ($post_temp && strpos($post_temp->post_excerpt, $this->finding_text) !== false) {
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
                break;

            case 2: // Sản phẩm chưa có mô tả
        
                $perPage = 50;
                $currentPage = 0;

                // show product one times
                $show_products = $this->show_products_per_page;
                $count_product = 0;

                while ($count_product < $show_products) {

                    $currentPage++;

                    $loop_args = array( 'post_type' => array('product'), 
                                'posts_per_page' => $perPage, 
                                'paged' => $currentPage );
                    
                    if ($this->selected_categories != 0) {
                        $loop_args['tax_query'] = array(
                                array(
                                'taxonomy' => 'product_cat',
                                'field' => 'term_id',
                                'terms' => array_values($this->selected_categories),
                                'operator' => 'IN'
                                )
                        );
                    }
                    
                    $loop = new WP_Query( $loop_args );

                    if (!$loop->post_count || $loop->post_count == 0) {
                        break;
                    }

                    while ( $loop->have_posts() ) : $loop->the_post();

                        $theid = get_the_ID();
                        
                        $post_temp = get_post($theid);
                        
                        if (empty($this->finding_text)) {
                            $check_empty = empty($post_temp->post_content);
                            if (!$check_empty && is_string($post_temp->post_content)) {
                                $check_empty = strlen($post_temp->post_content) < 3 ? true : false;
                            }
                            if ($post_temp && $check_empty) {
                                $list_product[] = $theid;
                                $count_product++;
                            }
                        } else {
                            if ($post_temp && strpos($post_temp->post_content, $this->finding_text) !== false) {
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
                
            default:
                
                $yith_key = $this->show_type;
                
                $perPage = 50;
                $currentPage = 0;

                // show product one times
                $show_products = $this->show_products_per_page;
                $count_product = 0;

                while ($count_product < $show_products) {

                    $currentPage++;
                    
                    $loop_args = array( 'post_type' => array('product'), 
                                'posts_per_page' => $perPage, 
                                'paged' => $currentPage );
                    
                    if ($this->selected_categories != 0) {
                        $loop_args['tax_query'] = array(
                                array(
                                'taxonomy' => 'product_cat',
                                'field' => 'term_id',
                                'terms' => array_values($this->selected_categories),
                                'operator' => 'IN'
                                )
                        );
                    }
                    
                    $loop = new WP_Query( $loop_args );
                    
                    if (!$loop->post_count || $loop->post_count == 0) {
                        break;
                    }

                    while ( $loop->have_posts() ) : $loop->the_post();
                        
                        $theid = get_the_ID();
                        $product = wc_get_product();
                        
                        if (empty($this->finding_text)) {
                            if ($this->yit_tab_is_empty($product, $yith_key)) {
                                $list_product[] = $theid;
                                $count_product++;
                            }
                        } else {
                            if ($this->yit_tab_exists_string($product, $yith_key, $this->finding_text)) {
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
                break; // break default
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
            'category' => 'Danh Mục',
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
    
    public function yit_tab_exists_string( $product, $key, $finding_text = '' )
    {

        if( substr( $key, 0, 4 ) === 'ywtm' ) {
            $key = explode( '_', $key );
            $key = $key[1];

        }
        $type_content = get_post_meta( $key, '_ywtm_enable_custom_content', true );
        $type_layout = get_post_meta( $key, '_ywtm_layout_type', true );
        $args = array();

        $is_exists = false;

        switch ( $type_layout ) {

            case 'download' :

                if( true == $type_content ) {
                    $args['download'] = get_post_meta( $key, '_ywtm_download', true );
                }
                else {
                    $args['download'] = yit_get_prop( $product, $key . '_custom_list_file', true );
                }
                
                $is_exists = strpos($args['download'], $finding_text);
                break;

            case 'faq' :

                if( true == $type_content ) {
                    $args['faqs'] = get_post_meta( $key, '_ywtm_faqs', true );
                }

                else {
                    $args['faqs'] = yit_get_prop( $product, $key . '_custom_list_faqs', true );
                }
                
                $is_exists = strpos($args['faqs'], $finding_text);
                break;

            case 'map' :

                if( true == $type_content ) {
                    $address = get_post_meta( $key, '_ywtm_google_map_overlay_address', true );

                }
                else {
                    $args['map'] =  yit_get_prop( $product, $key . '_custom_map', true );
                    $address = isset( $args['map']['addr'] ) ? $args['map']['addr'] : '';
                }
                
                $is_exists = strpos($address, $finding_text);
                break;

            case 'contact':

                if( true == $type_content ) {
                    $args['form'] = get_post_meta( $key, '_ywtm_form_tab', true );
                }
                else {
                    $args['form'] =  yit_get_prop( $product, $key . '_custom_form', true );
                }
                
                $is_exists = strpos($args['form'], $finding_text);
                break;

            case 'gallery':

                if( true == $type_content ) {

                    $gallery = get_post_meta( $key, '_ywtm_gallery', true );

                }
                else {

                    $result =  yit_get_prop( $product, $key . '_custom_gallery', true );
                    $gallery = isset( $result['images'] ) && !empty( $result['images'] ) ? 'gallery' : '';

                }
                $is_exists = strpos($gallery, $finding_text);
                break;

            case 'video':

                if( true == $type_content ) {
                    $result = get_post_meta( $key, '_ywtm_video', true );
                    $video = $result['video_info'];


                }
                else {

                    $result =  yit_get_prop( $product, $key . '_custom_video', true );
                    $video = $result ? 'video' : '';
                }
                
                $is_exists = strpos($video, $finding_text);
                break;

            case 'shortcode':
                if( true == $type_content ) {

                    $args['shortcode'] = get_post_meta( $key, '_ywtm_shortcode_tab', true );
                }
                else {
                    $args['shortcode'] =  yit_get_prop( $product, $key . '_custom_shortcode', true );
                }
                
                $is_exists = strpos($args['shortcode'], $finding_text);
                break;

            default :

                if( true == $type_content ) {
                    $args['content'] = get_post_meta( $key, '_ywtm_text_tab', true );
                }
                else {

                    $args['content'] =  yit_get_prop( $product, $key . '_default_editor', true );
                }
                
                $is_exists = strpos($args['content'], $finding_text);
                
                break;
        }
        
        if ($is_exists !== false) 
            return true; 
        else 
            return false;
    }
    
    public function yit_tab_is_empty( $product, $key )
    {

        if( substr( $key, 0, 4 ) === 'ywtm' ) {
            $key = explode( '_', $key );
            $key = $key[1];

        }
        $type_content = get_post_meta( $key, '_ywtm_enable_custom_content', true );
        $type_layout = get_post_meta( $key, '_ywtm_layout_type', true );
        $args = array();

        $is_empty = false;

        switch ( $type_layout ) {

            case 'download' :

                if( true == $type_content ) {
                    $args['download'] = get_post_meta( $key, '_ywtm_download', true );
                }
                else {
                    $args['download'] = yit_get_prop( $product, $key . '_custom_list_file', true );
                }

                $is_empty = empty( $args['download'] ) ;
                if (!$is_empty && is_string($args['download'])) {
                    $is_empty = strlen($args['download']) < 3 ? true : false;
                }
                break;

            case 'faq' :

                if( true == $type_content ) {
                    $args['faqs'] = get_post_meta( $key, '_ywtm_faqs', true );
                }

                else {
                    $args['faqs'] = yit_get_prop( $product, $key . '_custom_list_faqs', true );
                }

                $is_empty = empty( $args['faqs'] );
                if (!$is_empty && is_string($args['faqs'])) {
                    $is_empty = strlen($args['faqs']) < 3 ? true : false;
                }
                break;

            case 'map' :

                if( true == $type_content ) {
                    $address = get_post_meta( $key, '_ywtm_google_map_overlay_address', true );

                }
                else {
                    $args['map'] =  yit_get_prop( $product, $key . '_custom_map', true );
                    $address = isset( $args['map']['addr'] ) ? $args['map']['addr'] : '';
                }

                $is_empty = empty( $address );
                if (!$is_empty && is_string($address)) {
                    $is_empty = strlen($address) < 3 ? true : false;
                }
                break;

            case 'contact':

                if( true == $type_content ) {
                    $args['form'] = get_post_meta( $key, '_ywtm_form_tab', true );
                }
                else {
                    $args['form'] =  yit_get_prop( $product, $key . '_custom_form', true );
                }

                $is_empty = empty( $args['form'] );
                if (!$is_empty && is_string($args['form'])) {
                    $is_empty = strlen($args['form']) < 3 ? true : false;
                }
                break;

            case 'gallery':

                if( true == $type_content ) {

                    $gallery = get_post_meta( $key, '_ywtm_gallery', true );

                }
                else {

                    $result =  yit_get_prop( $product, $key . '_custom_gallery', true );
                    $gallery = isset( $result['images'] ) && !empty( $result['images'] ) ? 'gallery' : '';

                }
                $is_empty = empty( $gallery );
                if (!$is_empty && is_string($gallery)) {
                    $is_empty = strlen($gallery) < 3 ? true : false;
                }
                break;

            case 'video':

                if( true == $type_content ) {
                    $result = get_post_meta( $key, '_ywtm_video', true );
                    $video = $result['video_info'];


                }
                else {

                    $result =  yit_get_prop( $product, $key . '_custom_video', true );
                    $video = $result ? 'video' : '';
                }

                $is_empty = empty( $video );
                if (!$is_empty && is_string($video)) {
                    $is_empty = strlen($video) < 3 ? true : false;
                }
                break;

            case 'shortcode':
                if( true == $type_content ) {

                    $args['shortcode'] = get_post_meta( $key, '_ywtm_shortcode_tab', true );
                }
                else {
                    $args['shortcode'] =  yit_get_prop( $product, $key . '_custom_shortcode', true );
                }

                $is_empty = empty( $args['shortcode'] );
                if (!$is_empty && is_string($args['shortcode'])) {
                    $is_empty = strlen($args['shortcode']) < 3 ? true : false;
                }
                break;

            default :

                if( true == $type_content ) {
                    $args['content'] = get_post_meta( $key, '_ywtm_text_tab', true );
                }
                else {

                    $args['content'] =  yit_get_prop( $product, $key . '_default_editor', true );
                }

                $is_empty = empty( $args['content'] );
                if (!$is_empty && is_string($args['content'])) {
                    $is_empty = strlen($args['content']) < 3 ? true : false;
                }
                
                break;
        }
        
        return $is_empty;
    }
}
