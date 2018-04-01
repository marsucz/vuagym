<?php

/**
 *
 * @author MT
 */

class DbModel {

    private $link;
    private $store;

    public function __construct($store = 1) {
        $this->link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $this->store = $store;
    }
    
    public function query($query) {
        $result = mysqli_query($this->link, $query);
        return $result;
    }
    
    public function get_count_woo_product() {
        
        $query = "SELECT count(*) as 'count' FROM vg_posts where post_type = 'product'";
        
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
                    FROM vg_posts 
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
}

