<?php

/**
 *
 * @author MT
 */

class DbModel {

    private $link;
    private $store;
    private $prefix;

    public function __construct($store = 1) {
        $this->link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        mysqli_set_charset($this->link, "utf8");
        $this->store = $store;
        global $wpdb;
        $this->prefix = $wpdb->base_prefix;
    }
    
    public function query($query) {
        $result = mysqli_query($this->link, $query);
        return $result;
    }
    
    public function get_count_woo_product() {
        
        $query = "SELECT count(*) as 'count' FROM {$this->prefix}posts where post_type = 'product'";
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $return[0]['count'];
        } else {
            return 0;
        }
        
    }
    
    public function get_children_ids($parent_id) {
		
        $query = "  SELECT ID
                    FROM {$this->prefix}posts 
                    WHERE post_parent = {$parent_id}
                    AND post_type = 'product_variation'";
		
        $result = mysqli_query($this->link, $query);
		
        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
            if ($return) {
                return $return;
            } else {
                return [];
            }
        } else {
            return [];
        }
        
    }
    
    public function check_stock_by_parent_id($parent_id) {
        
//        if (!$parent_id) return false;
        $query = "  SELECT *
                    FROM db_vuagym.vg_postmeta
                    WHERE post_id IN (  SELECT ID
                                        FROM {$this->prefix}posts 
                                        WHERE post_parent = {$parent_id}
                                        AND post_type = 'product_variation')
                    AND meta_key = '_stock_status'
                    AND meta_value = 'instock'
                    LIMIT 1";
		
        $result = mysqli_query($this->link, $query);
        
        $count = mysqli_num_rows($result);
        
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }
    
    public function check_preorder_by_parent_id($parent_id) {
        
        if (!$parent_id) return false;
        
        $query = "  SELECT *
                    FROM db_vuagym.vg_postmeta
                    WHERE post_id IN (  SELECT ID
                                        FROM {$this->prefix}posts 
                                        WHERE post_parent = {$parent_id}
                                        AND post_type = 'product_variation')
                    AND meta_key = '_ywpo_preorder'
                    AND meta_value = 'yes'
                    LIMIT 1";
		
        $result = mysqli_query($this->link, $query);
        
        $count = mysqli_num_rows($result);
        
        if ($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public function kiotviet_insert_product($product_id, $product_code) {
        
        $query = '  INSERT INTO ' . DB_KIOTVIET_PRODUCTS . '(product_id, product_store, product_code, product_updated)
                        VALUES (
                        ' . $product_id . ',
                        "' . $this->store . '",
                        "' . $product_code . '",
                        Now())';
        
        $result = mysqli_query($this->link, $query);

        return $result;
        
    }
    
    public function kiotviet_update_productcode_by_productid($product_id, $product_code) {
        
        $query = '  UPDATE ' . DB_KIOTVIET_PRODUCTS . ' 
                    SET product_code = "' . $product_code . '",
                         product_updated = Now()
                    WHERE 
                        product_store = ' . $this->store . ' AND
                        product_id = ' . $product_id . ';';
        
        $result = mysqli_query($this->link, $query);

        return $result;
        
    }
    
    public function kiotviet_get_count_all_products() {
        
        $query = '  SELECT * FROM ' . DB_KIOTVIET_PRODUCTS;
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }

        return $return;
        
    }
    
    public function kiotviet_get_count_all_products_store() {
        
        $query = '  SELECT * FROM ' . DB_KIOTVIET_PRODUCTS . ' WHERE product_store = ' . $this->store;
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }

        return $return;
        
    }
    
    public function kiotviet_delete_all_products_sku() {
        
        $query = '  TRUNCATE TABLE ' . DB_KIOTVIET_PRODUCTS;
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            return true;
        } else {
            return false;
        }

    }
    
    public function get_productInfo_byProductCode($product_code) {
        
        $query = '  SELECT * FROM ' . DB_KIOTVIET_PRODUCTS . ' WHERE product_store = ' . $this->store . ' AND product_code = "' . $product_code . '"';
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }

        return $return;
        
    }
    
    public function get_productInfo_byProductID($product_id) {
        
        $query = '  SELECT * FROM ' . DB_KIOTVIET_PRODUCTS . ' WHERE product_store = ' . $this->store . ' AND product_id = "' . $product_id . '"';
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }

        return $return;
        
    }
    
    public function get_attribute_taxonomies() {
        
        $query = "SELECT * FROM {$this->prefix}woocommerce_attribute_taxonomies";
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }

        return $return;
    }
}

