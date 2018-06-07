<?php

require_once('function.php');
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
    
    public function check_preorder_by_parent_id($parent_id) {
        
        if (!$parent_id) return false;
        
        $query = "  SELECT *
                    FROM {$this->prefix}postmeta
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
    
    // Get Products LIST
    
    public function kapos_get_all_products_to_sethidden($perpage = 40, $currentpage = 1) {
        
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
    
    
    // IMPORT MANAGERS
    
    public function kapos_get_importfile_detail($import_file_name) {
        
        $db_name = $this->prefix . DB_KAPOS_IMPORTS;
        
        $query = "SELECT * FROM $db_name WHERE filename = '" . trim($import_file_name) ."'";
        
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            write_logs('', $query);
        }
        
        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }
        
        return $return;
        
    }
    
    public function kapos_get_all_import_product($perpage = 20, $currentpage = 1) {
        
        $db_name = $this->prefix . DB_KAPOS_IMPORTS;
        
        if (!$currentpage) $currentpage = 1;
        
        $offset = ($currentpage - 1) * $perpage;
        
        $query = "SELECT 
                        ip.product_code, 
                        ip.product_name,
                        group_concat(concat(ip.product_quantity, ': ', ip.filename) separator '<br/>') as amount_info
                    FROM
                        $db_name ip
                    GROUP BY product_code, product_name
                    LIMIT $perpage OFFSET $offset";
        
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            write_logs('', $query);
        }
        
        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
        } else {
            $return = [];
        }
        
        return $return;
        
    }
    
    public function kapos_get_count_import_product() {
        
        $db_name = $this->prefix . DB_KAPOS_IMPORTS;
        
        $query = "  SELECT 
                        count(DISTINCT ip.product_code) as count
                    FROM
                        $db_name ip";
        
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            write_logs('', $query);
        }
        
        if ($result) {
            $return = mysqli_fetch_all($result, MYSQLI_ASSOC);
            $return = $return[0]['count'];
        } else {
            $return = 0;
        }
        
        return $return;
        
    }
    
    public function kapos_insert_imports($import_file_name, $import_rows) {
        
        $db_name = $this->prefix . DB_KAPOS_IMPORTS;

        $insert_values = array();
        foreach ($import_rows as $import) {
            $insert_values[] = "('" . mysqli_escape_string($this->link, $import_file_name) . "','" . mysqli_escape_string($this->link, trim($import[0])) . "','" . mysqli_escape_string($this->link, trim($import[1])) . "','".mysqli_escape_string($this->link, intval($import[2]))."')";
        }

        $query = "INSERT INTO {$db_name}(filename,product_code,product_name,product_quantity) VALUES " . implode(',', $insert_values);

        $result = mysqli_query($this->link, $query);
        if (!$result) {
            write_logs('', $query);
        }
        return $result;
        
    }
    
    public function kapos_delete_imports($import_file_name) {
        
        $db_name = $this->prefix . DB_KAPOS_IMPORTS;

        $query = "DELETE FROM {$db_name} WHERE filename = '" . trim($import_file_name) . "'";

        $result = mysqli_query($this->link, $query);
        if (!$result) {
            write_logs('', $query);
        }
        return $result;
        
    }
}

