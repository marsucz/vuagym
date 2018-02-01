<?php

/*
 * Return:
 * 0: Het hang
 * 1: Sap Co Hang
 * 2: Con hang
 */

function kiotViet_get_preOrder_status($item_id) {
    
    $product = wc_get_product( $item_id );
    
    $pre_order_status = -1;
    $pre_order = new YITH_Pre_Order_Product( $item_id );
    
    if ( 'yes' == $pre_order->get_pre_order_status() ) {
            $pre_order_status = 1;  // Sắp có hàng
    } elseif ($product->is_in_stock()) {
            $pre_order_status = 2;  // Còn hàng
    } else {
            $pre_order_status = 0;  // Hết hàng
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
        if(!$price)
                return;
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