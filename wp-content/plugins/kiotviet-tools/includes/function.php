<?php

function write_logs($file_name, $text) {
    
    $file_path = WC_PLUGIN_DIR . '/logs/' . $file_name;
    
    $file = fopen($file_path, "a");
    
    $date = date('Y-m-d H:i:s');
    
    $body = "\n" . $date . ' ';
    $body .= $text;
    
    fwrite($file, $body);
    fclose($file);
    
}