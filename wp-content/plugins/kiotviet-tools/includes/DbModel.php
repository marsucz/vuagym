<?php

/**
 *
 * @author MT
 */

class DbModel {

    private $link;

    public function __construct() {
        $this->link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    }
    
    public function query($query) {
        $result = mysqli_query($this->link, $query);
        return $result;
    }

    public function kiotviet_insert_product($product_id, $product_code) {
        
        $query = '  INSERT INTO ' . DB_KIOTVIET_PRODUCTS . '(product_id, product_code, product_updated)
                        VALUES (
                        ' . $product_id . ',
                        "' . $product_code . '",
                        Now())';
        
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
        
        $query = '  SELECT * FROM ' . DB_KIOTVIET_PRODUCTS . ' WHERE product_code = "' . $product_code . '"';
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }

        return $return;
        
    }
}

