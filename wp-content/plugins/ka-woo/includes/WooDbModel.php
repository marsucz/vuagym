<?php

/**
 *
 * @author MT
 */

class WooDbModel {

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
    
    // Get Products LIST
    public function kawoo_get_all_products_to_sethidden($perpage = 40, $currentpage = 1) {
        
        if (!$currentpage) $currentpage = 1;
        $offset = ($currentpage - 1) * $perpage;
        
        $query = "  SELECT 
                        pm.meta_value as stock_status, 
                        pm2.meta_value as show_status, 
                        p.ID
                    FROM
                        {$this->prefix}posts p
                            INNER JOIN
                        {$this->prefix}postmeta pm ON pm.post_id = p.ID
                            AND pm.meta_key = '_stock_status'
                            LEFT JOIN
                        {$this->prefix}postmeta pm2 ON pm2.post_id = p.ID
                            AND pm2.meta_key = '_mypos_show_always'
                    WHERE
                        post_type = 'product' 
                    LIMIT $perpage OFFSET $offset";
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }
        
        return $return;
        
    }
    
    // Get Products LIST
    public function kawoo_get_all_products($perpage = 40, $currentpage = 1) {
        
        if (!$currentpage) $currentpage = 1;
        $offset = ($currentpage - 1) * $perpage;
        
        $query = "  SELECT 
                        p.ID
                    FROM
                        {$this->prefix}posts p
                    WHERE
                        post_type = 'product' 
                    LIMIT $perpage OFFSET $offset";
        
        $result = mysqli_query($this->link, $query);

        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }
        
        return $return;
        
    }
}
        
