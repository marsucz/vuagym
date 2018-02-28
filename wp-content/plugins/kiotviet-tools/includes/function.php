<?php

function kiotViet_get_preOrder_status($item_id) {
    
    $pre_order_status = false;
    $pre_order = new YITH_Pre_Order_Product( $item_id );
    
    if ( 'yes' == $pre_order->get_pre_order_status() ) {
        $pre_order_status = true;  // Sắp có hàng
    } 
    
    return $pre_order_status;
    
}

/**
 * Formats the RAW woocommerce price
 *
 * @since     1.0.0
 * @param  	  int $price
 * @return    string 
 */

function kiotViet_formatted_price($price){
        if(!$price) {
            return '0đ';
        }
        $options 	= get_option('xoo-wsc-gl-options');
        $default_wc = isset( $options['sc-price-format']) ? $options['sc-price-format'] : 0;

        if($default_wc == 1){
                return wc_price($price);
        }

        $thous_sep = wc_get_price_thousand_separator();
        $dec_sep   = wc_get_price_decimal_separator();
        $decimals  = wc_get_price_decimals();
        $price 	   = number_format( $price, $decimals, $dec_sep, $thous_sep );
        
        $format   = get_option( 'woocommerce_currency_pos' );
        $csymbol  = get_woocommerce_currency_symbol();

        switch ($format) {
                case 'left':
                        $fm_price = $csymbol.$price;
                        break;

                case 'left_space':
                        $fm_price = $csymbol.' '.$price;
                        break;

                case 'right':
                        $fm_price = $price.$csymbol;
                        break;

                case 'right_space':
                        $fm_price = $price.' '.$csymbol;
                        break;

                default:
                        $fm_price = $csymbol.$price;
                        break;
        }
        return $fm_price;
}