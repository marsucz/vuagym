<?php

/**
 *
 * @author MT
 */

class Woo_DbModel {

    private $link;
    private $prefix;    

    public function __construct() {
        $this->link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
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
                    FROM {$this->prefix}postmeta
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
}
        
