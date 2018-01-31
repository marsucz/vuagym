<?php

if (!defined('KV_RETAILER')) {
    define('KV_RETAILER', 'vuagymtest');
}

if (!defined('KV_CLIENT_ID')) {
    define('KV_CLIENT_ID', '619e8b7f-3b68-4635-8760-bdb90c1d8a66');
}

if (!defined('KV_CLIENT_SECRET')) {
    define('KV_CLIENT_SECRET', 'D0AFE74F413FB339B8F8F81C71AEE7B460E20A0F');
}

include_once 'DbModel.php';
include_once 'function.php';

class KiotViet_API {
    
    private $access_token = '';
    
    public function get_product_quantity_by_ProductSKU($product_sku) {
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
            $result = MAX_QUANTITY;
        } else {
            $result = $this->get_product_quantity_byKiotvietProductID($product[0]['product_id']);
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
            $quality = MAX_QUANTITY;
        }

        return $quality;
    }

    public function get_product_info($product_id) {

        $url = 'https://public.kiotapi.com/products/' . $product_id;

        $result = $this->api_call($url);

        if ($result !== false && isset($result['id'])) {
//        if ($result) {
            return $result;
        } else {
            
            if (is_array($result)) {
                $result = json_encode($result);
            }
            
            $t = date('Ymd');
            $log_file = "KiotVietAPI_Errors_{$t}.txt";
            $log_text = "URL Get: " . $url;
            $log_text .= "\n KiotViet API response error format: " . $result;

            write_logs($log_file, $log_text);
            
            return false;
        }

    }

    public function get_all_products() {

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
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => "GET",
         CURLOPT_HTTPHEADER => array(
           "Retailer: " . KV_RETAILER,
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
            $log_text .= "\n KiotViet not response: " . $result;
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
            'client_id' => KV_CLIENT_ID,
            'client_secret' => KV_CLIENT_SECRET
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

        if ($access_token) {
            $this->access_token = $access_token['access_token'];
        } else {
            $this->access_token = '';
        }
        
        return $this->access_token;
    }
}