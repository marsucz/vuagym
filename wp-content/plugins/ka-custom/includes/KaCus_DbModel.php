<?php

/**
 * @author MT
 */

class KaCus_DbModel {

    private $link;
    private $prefix;

    public function __construct() {
        $this->link = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        mysqli_set_charset($this->link, "utf8");
        global $wpdb;
        $this->prefix = $wpdb->base_prefix;
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
                $res = array();
                foreach ($return as $id) {
                    $res[] = $id['ID'];
                }
                return $res;
            } else {
                return [];
            }
        } else {
            return [];
        }
        
    }
    
    public function get_stock_status_by_children_list($child_list) {
        
        if (!$child_list) return false;
        
        $query = "  SELECT post_id, meta_value
                    FROM {$this->prefix}postmeta
                    WHERE post_id IN ($child_list)
                    AND meta_key = '_stock_status'";
		
        $result = mysqli_query($this->link, $query);
        
        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
            
            if ($return) {
                $res = array();
                foreach ($return as $id) {
                    $res[$id['post_id']] = $id['meta_value'];
                }
                return $res;
            } else {
                return [];
            }
        } else {
            return [];
        }
    }
    
    // Update post status: "private" or "publish"
    public function update_status_variations_by_list($child_list, $status = 'publish') {

        if (!$child_list) return false;
        
        $query = "  UPDATE {$this->prefix}posts 
                        SET post_status = '{$status}'
                    WHERE 
                       ID IN ({$child_list})";
        
        $result = mysqli_query($this->link, $query);
        return $result;
    }
    
}

