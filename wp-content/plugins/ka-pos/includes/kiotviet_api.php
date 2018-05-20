<?php

include_once 'DbModel.php';
include_once 'function.php';
include_once 'function_template.php';

if (!defined('KV_API_MAX_ERROR')) {
    define('KV_API_MAX_ERROR', 20);
}

class KiotViet_API {
    
    private $access_token = '';
    private $count_error = 0;
    private $stop = false;       
    private $kiotviet_retailer = '';
    private $kiotviet_client_id = '';
    private $kiotviet_client_secret = '';
    private $store = 1;
    
    function __construct($store = 1) {
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $this->store = $store;
        if ($store == 1) {
            $this->kiotviet_retailer = get_option('kiotviet_retailer');
            $this->kiotviet_client_id = get_option('kiotviet_client_id');
            $this->kiotviet_client_secret = get_option('kiotviet_client_secret');
            
        } else {
            $this->kiotviet_retailer = get_option('kiotviet2_retailer');
            $this->kiotviet_client_id = get_option('kiotviet2_client_id');
            $this->kiotviet_client_secret = get_option('kiotviet2_client_secret');
        }
    }
    
    public function change_store($store = 1) {
        if ($store == 1) {
            $this->kiotviet_retailer = get_option('kiotviet_retailer');
            $this->kiotviet_client_id = get_option('kiotviet_client_id');
            $this->kiotviet_client_secret = get_option('kiotviet_client_secret');
        } else {
            $this->kiotviet_retailer = get_option('kiotviet2_retailer');
            $this->kiotviet_client_id = get_option('kiotviet2_client_id');
            $this->kiotviet_client_secret = get_option('kiotviet2_client_secret');
        }
    }

    public function check_process_stop($error_details = '') {
        if ($this->stop) {
            echo "<br/><span style='font-weight: bold; color: red;'>Xuất hiện lỗi khi kết nối tới API. Dừng quá trình!";
            if (!empty($error_details)){
                echo "=> Chi tiết: " . json_encode($error_details);
            }
            echo "</span>";
            exit;
        }
    }
    
    public function get_product_info_by_productSKU($product_sku = '') {
        
        $dbModel = new DbModel($this->store);
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
                $single_product['price'] = (int)$product_info['basePrice'];
                
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
        $dbModel = new DbModel($this->store);
        
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
                if ($preOder_status) { // Sap co hang
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
            // manual set by user
            $quality = get_option('mypos_error_max_quantity');
        }

        return $quality;
    }

    public function get_product_info($product_id) {

        $url = 'https://public.kiotapi.com/products/' . trim($product_id);

        $result = $this->api_call_prod($url);

        if ($result !== false && isset($result['id'])) {
//             $t = date('Ymd');
//            $log_file = "KiotVietAPI_ProductInfo_{$t}.txt";
//            $log_text = "URL Get: " . $url;
//            $log_text .= "\n Good response: " . json_encode($result);
//            write_logs($log_file, $log_text);
            
            return $result;
        } else {    // Double check if the response is still bad
            $t = date('Ymd');
            $log_file = "KiotVietAPI_ProductInfo_{$t}.txt";
            $log_text = "URL Get: " . $url;
            $log_text .= "\n Bad response: " . json_encode($result);
            write_logs($log_file, $log_text);
            return false;
//            $result = $this->api_call_prod($url, '', 2);
//            if ($result !== false && isset($result['id'])) {
//                return $result;
//            } else {
//                $t = date('Ymd');
//                $log_file = "KiotVietAPI_DoubleCall_{$t}.txt";
//                $log_text = "URL Get: " . $url;
//                $log_text .= "\n Double Call to API but bad response: " . json_encode($result);
//
//                write_logs($log_file, $log_text);
//                return false;
//            }
        }
        
        return false;
    }

    public function get_all_product_sku() {

        set_time_limit(0);

        $dbModel = new DbModel($this->store);

        $url = 'https://public.kiotapi.com/products';

        // Get example data to get number of products
        $data['pageSize'] = 1;
        $data['currentItem'] = 0;
        $result = $this->api_call($url, $data);
        
        if (!$result) {
            $result = $this->api_call($url, $data);
        }
        
        $count_insert = 0;
        $count_update = 0;
        
        if ($result) {
            $data['pageSize'] = 20;
            $number_products = $result['total'];
            $number_pages = (int) ($number_products / $data['pageSize']) + 1;

            for ($i=0; $i<=$number_pages; $i++) {
                $data['currentItem'] = $data['pageSize'] * $i;
                $result = $this->api_call($url, $data);
                foreach ($result['data'] as $product) {
                    $result = $dbModel->kiotviet_insert_product($product['id'], $product['code']);
                    if ($result) {
                        $count_insert++;
                    } else {
                        $product_temp = $dbModel->get_productInfo_byProductID($product['id']);
                        if (!empty($product_temp)) {
                            if ($product_temp[0]['product_code'] != $product['code']) {
                                $update_result = $dbModel->kiotviet_update_productcode_by_productid($product['id'], $product['code']);
                                if ($update_result) {
                                    $count_update++;
                                }
                            }
                        }
                    }
                }
            }
        }
        
        $return['count_insert'] = $count_insert;
        $return['count_update'] = $count_update;
        
        return $return;
    }
    
    public function get_count_all_products() {

        set_time_limit(0);

        $dbModel = new DbModel($this->store);

        $url = 'https://public.kiotapi.com/products';

        // Get example data to get number of products
        $data['pageSize'] = 1;
        $data['currentItem'] = 0;
        $result = $this->api_call($url, $data);
        
        if ($result && isset($result['total'])) {
            return $result['total'];
        } else {
            return 0;
        }
        
    }
    
    public function get_all_products() {

        set_time_limit(300);

        $dbModel = new DbModel($this->store);

        $url = 'https://public.kiotapi.com/products';

        // Get example data to get number of products
        $data['pageSize'] = 1;
        $data['currentItem'] = 0;
        
        $result = $this->api_call($url, $data);
        
        $data['includeInventory'] = true;
        $data['includePricebook'] = true;
        $data['pageSize'] = 100;
        
        $number_products = $result['total'];
        $number_pages = (int) ($number_products / $data['pageSize']) + 1;

        $count = 0;
        
        $all_products = array();
        
        for ($i=0; $i<=$number_pages; $i++) {
            $data['currentItem'] = $data['pageSize'] * $i;
            $result = $this->api_call($url, $data);
            
            $converted_products = $this->convert_products($result);
            if (count($converted_products) > 0) {
                $all_products = array_merge($all_products, $converted_products);
            }
        }

        return $all_products;
    }
    
        public function get_all_products_multi() {

        set_time_limit(300);

        $dbModel = new DbModel($this->store);

        $url = 'https://public.kiotapi.com/products';

        // Get example data to get number of products
        $data['pageSize'] = 1;
        $data['currentItem'] = 0;
        
        $result = $this->api_call($url, $data);
        
        $data['includeInventory'] = true;
        $data['includePricebook'] = true;
        $data['pageSize'] = 100;
        
        $number_products = $result['total'];
        $number_pages = (int) ($number_products / $data['pageSize']) + 1;

        $count = 0;
        
        $all_products = array();
        $url_array = array();
        
        for ($i=0; $i<=$number_pages; $i++) {
            $data['currentItem'] = $data['pageSize'] * $i;
            $url_array[] = $url . '?' . http_build_query($data, '', '&');
        }
        
        if (count($url_array) > 0) {
            $all_products = $this->runRequests($url_array);
        }
        
        return $all_products;
    }
    
    private function convert_products($raw_data) {
        
        $all_products = array();
        
        if (!isset($raw_data['data']) || empty($raw_data['data'])) {
            return $all_products;
        }
        
        foreach ($raw_data['data'] as $product) {
            $single_product = array();
            $single_product['id'] = $product['id'];
            $single_product['sku'] = $product['code'];
            $single_product['name'] = isset($product['fullName']) ? $product['fullName'] : $product['name'];
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
        
        return $all_products;
    }


    public function get_product_paged($per_page = 50, $paged = 0) {

        set_time_limit(0);
        
        $start_time = microtime(true);
        
        if ($paged != 0) {
            $paged = $paged - 1;
        }
        
        $dbModel = new DbModel($this->store);

        $url = 'https://public.kiotapi.com/products';

        // Get example data to get number of products
//        $data['pageSize'] = 1;
        $data['currentItem'] = 0;
        $data['includeInventory'] = true;
        $data['includePricebook'] = true;
//        $result = $this->api_call($url, $data);

        $data['pageSize'] = $per_page;
//        $number_products = $result['total'];
//        $number_pages = (int) ($number_products / $data['pageSize']) + 1;

        $count = 0;
        
        $all_products = array();
        
//        for ($i=0; $i<=$number_pages; $i++) {
            $data['currentItem'] = $data['pageSize'] * $paged;
            $result = $this->api_call($url, $data);
            
//             Try 1 more time if the request isn't response
            if (!$result || !isset($result['data'])) {
                $result = $this->api_call($url, $data);
            }
            
//            if (!$result || !isset($result['data'])) {
//                return $all_products;
//            }
            if (isset($result['data'])) {
                
                foreach ($result['data'] as $product) {
                    $single_product = array();
                    $single_product['id'] = $product['id'];
                    $single_product['sku'] = $product['code'];
                    $single_product['name'] = isset($product['fullName']) ? $product['fullName'] : $product['name'];
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
    
        // logs
        $end_time = microtime(true);    
        $time = $end_time - $start_time;
        
        $t = date('Ymd');
        $log_file = "LogPaged-{$t}.txt";
        $log_text = "{$url} During: {$time}";
        write_logs($log_file, $log_text);
        
        $return['all_products'] = $all_products;
        
        if (isset($result['total'])) {
            $return['total'] = $result['total'];
        } else {
            $return['total'] = 0;
        }
        
        return $return;
    }
    
    // API call for production: Set limit time 3s
    public function api_call_prod($url, $data = [], $time_limit = 3) {

        if (!empty($data) && is_array($data)) {
            $url = $url . '?' . http_build_query($data, '', '&');
        } 
        
        $access_token = $this->get_access_token();
        if (empty($access_token)) {
            return false;
        }
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_MAXREDIRS => 3,
         CURLOPT_TIMEOUT => $time_limit,
         CURLOPT_CUSTOMREQUEST => "GET",
         CURLOPT_HTTPHEADER => array(
           "Retailer: " . $this->kiotviet_retailer,
           "Authorization: Bearer " . $access_token
         ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        $result = json_decode($response, true);
        if ($result) {
            return $result;
        } else {
            return false;
        }
    }
    
    public function api_call($url, $data = []) {

        if (!empty($data) && is_array($data)) {
            $url = $url . '?' . http_build_query($data, '', '&');
        } 
        
        $access_token = $this->get_access_token();
        if (empty($access_token)) {
            return false;
        }
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_MAXREDIRS => 3,
         CURLOPT_TIMEOUT => 15,
         CURLOPT_CUSTOMREQUEST => "GET",
         CURLOPT_HTTPHEADER => array(
           "Retailer: " . $this->kiotviet_retailer,
           "Authorization: Bearer " . $access_token
         ),
        ));

        $response = curl_exec($curl);
        curl_close($curl);
        
        $result = json_decode($response, true);
        if ($result) {
            if (isset($result['responseStatus'])) {
                $this->count_error++;
                if ($this->count_error > KV_API_MAX_ERROR) {
                    $this->stop = true;
                    $this->check_process_stop($result['responseStatus']);
                }
            } else {
                return $result;
            }
            
        } else {
            
            $this->count_error = $this->count_error + 1;
            if ($this->count_error > KV_API_MAX_ERROR) {
                $this->stop = true;
                $this->check_process_stop();
            }
            
            $t = date('Ymd');
            $log_file = "KiotVietAPI_Errors_{$t}.txt";
            $log_text = "URL Get: " . $url;
            $log_text .= "\n KiotViet not response: " . json_encode($response);
            write_logs($log_file, $log_text);
            return false;
            
        }
    }

    public function api_call_put($url, $data = []) {

        $access_token = $this->get_access_token();
        if (empty($access_token)) {
            return false;
        }
            
        $curl = curl_init();

        curl_setopt_array($curl, array(
         CURLOPT_URL => $url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_MAXREDIRS => 5,
         CURLOPT_TIMEOUT => 15,
         CURLOPT_CUSTOMREQUEST => "PUT",
         CURLOPT_HTTPHEADER => array(
           "Retailer: " . $this->kiotviet_retailer,
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
            $log_file = "KiotVietAPI_SetPriceError_{$t}.txt";
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
            'client_id' => $this->kiotviet_client_id,
            'client_secret' => $this->kiotviet_client_secret
        );

        $post_string = http_build_query($data, '', '&');
        $post_url = 'https://id.kiotviet.vn/connect/token';
        
        $curl = curl_init();

        curl_setopt_array($curl, array(
         CURLOPT_URL => $post_url,
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_MAXREDIRS => 3,
         CURLOPT_TIMEOUT => 5,
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
            
//            $t = date('Ymd');
//            $log_file = "KiotVietAPI_GetToken_{$t}.txt";
//            $log_text = "Get Token Good: " . json_encode($response);
//            write_logs($log_file, $log_text);
            
        } else {
            $this->access_token = '';
            $t = date('Ymd');
            $log_file = "KiotVietAPI_GetToken_{$t}.txt";
            $log_text = "Get Token Error: " . json_encode($response);
            write_logs($log_file, $log_text);
            return false;
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
    
    public function runRequests($url_array, $thread_width = 8) {
        
        $access_token = $this->get_access_token();
        
        $threads = 0;
        $master = curl_multi_init();
        $curl_opts = array(
            CURLOPT_MAXREDIRS => 5,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => array(
                "Retailer: " . $this->kiotviet_retailer,
                "Authorization: Bearer " . $access_token
              ),
            CURLOPT_RETURNTRANSFER => TRUE);
        
        $all_products = array();

        $count = 0;
        foreach($url_array as $url) {

            $ch = curl_init();
            $curl_opts[CURLOPT_URL] = $url;

            curl_setopt_array($ch, $curl_opts);
            curl_multi_add_handle($master, $ch); //push URL for single rec send into curl stack

            $threads++;
            $count++;
            if($threads >= $thread_width) { //start running when stack is full to width
                while($threads >= $thread_width) {
                    usleep(100);
                    while(($execrun = curl_multi_exec($master, $running)) === -1){}
                    curl_multi_select($master);
                    while($done = curl_multi_info_read($master)) {
                                $api_result = curl_multi_getcontent($done['handle']);
                                $api_result = json_decode($api_result, true);
                                $converted_product = $this->convert_products($api_result);
                                $all_products = array_merge($all_products, $converted_product);
                        curl_multi_remove_handle($master, $done['handle']);
                        curl_close($done['handle']);
                        $threads--;
                    }
                }
            }
        }
        do { //finish sending remaining queue items when all have been added to curl
            usleep(100);
            while(($execrun = curl_multi_exec($master, $running)) === -1){}
            curl_multi_select($master);
            while($done = curl_multi_info_read($master)) {
                        $api_result = curl_multi_getcontent($done['handle']);
                        $api_result = json_decode($api_result, true);
                        $converted_product = $this->convert_products($api_result);
                        $all_products = array_merge($all_products, $converted_product);
                curl_multi_remove_handle($master, $done['handle']);
                curl_close($done['handle']);
                $threads--;
            }
        } while($running > 0);
        curl_multi_close($master);
        return $all_products;
    }
}