<?php

include_once 'DbModel.php';
include_once 'function.php';
include_once 'function_template.php';

class KiotViet_API {
    
    private $access_token = '';
    
    public function get_product_info_by_productSKU($product_sku = '') {
        
//        $t = date('Ymd');
//        $log_file = "CountRequest-{$t}.txt";
//        $log_text = $product_sku;
//        write_logs($log_file, $log_text);
        
        $dbModel = new DbModel();
        $single_product = array();
        
        if (!empty($product_sku)) {
            $product = $dbModel->get_productInfo_byProductCode($product_sku);
        } else {
            return $single_product;
        }
        
        if (count($product) > 0) {
            $product_info = $this->get_product_info($product[0]['product_id']);
            
            if ($product_info) {
                
                $single_product['id'] = $product_info['id'];
                $single_product['sku'] = $product_info['code'];
                $single_product['name'] = isset($product_info['fullName']) ? $product_info['fullName'] : $product_info['name'];
                $single_product['price'] = $product_info['basePrice'];
                
                $quantity = 0;
                if (isset($product_info['inventories']) && count($product_info['inventories']) > 0) {
                    foreach ($product_info['inventories'] as $inventory) {
                        $quantity += (int)$inventory['onHand'];
                    }
                }
                $single_product['quantity'] = $quantity;
                if ($quantity > 0) {
                    $single_product['stock'] = true;
                } else {
                    $single_product['stock'] = false;
                }
                
            } 
        }
        
        return $single_product;
    }
    
    public function get_product_quantity_by_ProductSKU($product_sku, $item_id = 0) {
        $dbModel = new DbModel();
        
        if (!empty($product_sku)) {
            $product = $dbModel->get_productInfo_byProductCode($product_sku);
        } else {
            $product = array();
        }
        
        if (count($product) == 0) {
            if (!empty($product_sku)) {
                $t = date('Ymd');
                $log_file = "KiotViet-{$t}.txt";
                $log_text = "SKU: {$product_sku} not exists on KiotViet or You haven't updated the database.";
                write_logs($log_file, $log_text);
            }
            $result = get_option('mypos_max_quantity');
        } else {
            
            if ($item_id != 0) {
                $preOder_status = kiotViet_get_preOrder_status($item_id);
                if ($preOder_status == 1) { // Sap co hang
                    $result = get_option('preorder_max_quantity');
                } else {
                    $result = $this->get_product_quantity_byKiotvietProductID($product[0]['product_id']);
                }
            } else {
                $result = $this->get_product_quantity_byKiotvietProductID($product[0]['product_id']);
            }
        }
        
        return $result;
    }
    
    public function get_product_quantity_byKiotvietProductID($kiotviet_product_id) {
        
        $quality = 0;
        
        $product = $this->get_product_info($kiotviet_product_id);

        if ($product !== false && isset($product['id'])) {
            if (isset($product['inventories']) && count($product['inventories']) > 0) {
                foreach ($product['inventories'] as $inventory) {
                    $quality += (int)$inventory['onHand'];
                }
            }
        } else {
            $quality = get_option('mypos_max_quantity');
        }

        return $quality;
    }

    public function get_product_info($product_id) {

        $url = 'https://public.kiotapi.com/products/' . trim($product_id);

        $result = $this->api_call($url);

        if ($result !== false && isset($result['id'])) {
            return $result;
        } else {    // Double check if the response is still bad
            
            $result = $this->api_call($url);
            if ($result !== false && isset($result['id'])) {
                return $result;
            } else {
                
                $t = date('Ymd');
                $log_file = "KiotVietAPI_DoubleCall_{$t}.txt";
                $log_text = "URL Get: " . $url;
                $log_text .= "\n Double Call to API but bad response: " . json_encode($result);

                write_logs($log_file, $log_text);

                return false;
            }
            
        } 
//        else {    // API response error
//            
//            $t = date('Ymd');
//            $log_file = "KiotVietAPI_Errors_{$t}.txt";
//            $log_text = "URL Get: " . $url;
//            $log_text .= "\n KiotViet API response error format: " . json_encode($result);
//
//            write_logs($log_file, $log_text);
//            
//            return false;
//        }

    }

    public function get_count_all_products() {

        set_time_limit(0);

        $dbModel = new DbModel();

        $url = 'https://public.kiotapi.com/products';

        // Get example data to get number of products
        $data['pageSize'] = 1;
        $data['currentItem'] = 0;
        $result = $this->api_call($url, $data);

        $data['pageSize'] = 20;
        $number_products = $result['total'];
        $number_pages = (int) ($number_products / $data['pageSize']) + 1;

        $count = 0;
        for ($i=0; $i<=$number_pages; $i++) {
            $data['currentItem'] = $data['pageSize'] * $i;
            $result = $this->api_call($url, $data);
            foreach ($result['data'] as $product) {
                $result = $dbModel->kiotviet_insert_product($product['id'], $product['code']);
                if ($result) {
                    $count++;
                }
            }
        }

        return $count;
    }
    
    public function get_all_products() {

        set_time_limit(0);

        $dbModel = new DbModel();

        $url = 'https://public.kiotapi.com/products';

        // Get example data to get number of products
        $data['pageSize'] = 1;
        $data['currentItem'] = 0;
        $data['includeInventory'] = true;
        $data['includePricebook'] = true;
        $result = $this->api_call($url, $data);

        $data['pageSize'] = 20;
        $number_products = $result['total'];
        $number_pages = (int) ($number_products / $data['pageSize']) + 1;

        $count = 0;
        
        $all_products = array();
        
        for ($i=0; $i<=$number_pages; $i++) {
            $data['currentItem'] = $data['pageSize'] * $i;
            $result = $this->api_call($url, $data);
            
            
            foreach ($result['data'] as $product) {
                $single_product = array();
                $single_product['id'] = $product['id'];
                $single_product['sku'] = $product['code'];
                $single_product['name'] = $product['name'];
                $single_product['price'] = $product['basePrice'];
                
                $quantity = 0;
                if (isset($product['inventories']) && count($product['inventories']) > 0) {
                    foreach ($product['inventories'] as $inventory) {
                        $quantity += (int)$inventory['onHand'];
                    }
                }
                $single_product['quantity'] = $quantity;
                if ($quantity > 0) {
                    $single_product['stock'] = true;
                } else {
                    $single_product['stock'] = false;
                }
                $all_products[] = $single_product;
            }
            
        }

        return $all_products;
    }

    public function api_call($url, $data = []) {

        if (!empty($data) && is_array($data)) {
            $url = $url . '?' . http_build_query($data, '', '&');
        } 
        
        $access_token = $this->get_access_token();
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => "",
         CURLOPT_MAXREDIRS => 5,
         CURLOPT_TIMEOUT => 5,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => "GET",
         CURLOPT_HTTPHEADER => array(
           "Retailer: " . get_option('kiotviet_retailer'),
           "Authorization: Bearer " . $access_token
         ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        $result = json_decode($response, true);
        if ($result) {
            return $result;
        } else {
            $t = date('Ymd');
            $log_file = "KiotVietAPI_Errors_{$t}.txt";
            $log_text = "URL Get: " . $url;
            $log_text .= "\n KiotViet not response: " . json_encode($response);
            write_logs($log_file, $log_text);
            return false;
        }
    }

        public function api_call_put($url, $data = []) {

//        if (!empty($data) && is_array($data)) {
//            $url = $url . '?' . http_build_query($data, '', '&');
//        } 
        
        $access_token = $this->get_access_token();
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => "",
         CURLOPT_MAXREDIRS => 5,
         CURLOPT_TIMEOUT => 5,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => "PUT",
         CURLOPT_HTTPHEADER => array(
           "Retailer: " . get_option('kiotviet_retailer'),
           "Authorization: Bearer " . $access_token
         ),
         CURLOPT_POSTFIELDS => http_build_query($data),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        $result = json_decode($response, true);
        if ($result) {
            return $result;
        } else {
            $t = date('Ymd');
            $log_file = "KiotVietAPI_Errors_{$t}.txt";
            $log_text = "URL Get: " . $url;
            $log_text .= "\n KiotViet not response: " . json_encode($response);
            write_logs($log_file, $log_text);
            return false;
        }
    }
    
    public function get_access_token() {
        
        if (!empty($this->access_token)) {
            return $this->access_token;
        }
        
        $data = array(
            'scopes' => 'PublicApi.Access',
            'grant_type' => 'client_credentials',
            'client_id' => get_option('kiotviet_client_id'),
            'client_secret' => get_option('kiotviet_client_secret')
        );

        $post_string = http_build_query($data, '', '&');
        $post_url = 'https://id.kiotviet.vn/connect/token';

        $curl = curl_init();

        curl_setopt_array($curl, array(
         CURLOPT_URL => $post_url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => "",
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => "POST",
         CURLOPT_POSTFIELDS => $post_string,
         CURLOPT_HTTPHEADER => array(
           "accept: application/json",
           "cache-control: no-cache",
           "content-type: application/x-www-form-urlencoded"
         ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);

        $access_token = json_decode($response, true);
        
//        echo '<pre>';
//        print_r($access_token);
//        echo '</pre>';
//        exit;

        if ($access_token) {
            $this->access_token = $access_token['access_token'];
        } else {
            $this->access_token = '';
        }
        
        return $this->access_token;
    }
    
    public function set_product_price($product_id, $price) {

        $url = 'https://public.kiotapi.com/products/' . trim($product_id);

        $data['basePrice'] = $price;
        
        $result = $this->api_call_put($url, $data);
        
        if ($result) {
            return $result;
        } else {    // Double check if the response is still bad
            
            $t = date('Ymd');
            $log_file = "KiotVietAPI_SetPrice_{$t}.txt";
            $log_text = "URL Put: " . $url;
            $log_text .= "\n Set Price Error: " . json_encode($result);

            write_logs($log_file, $log_text);

            return false;
            
        } 
    }
}