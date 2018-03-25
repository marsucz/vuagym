<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


return array(

	'settings' => array(

		'section_general_settings'     => array(
			'name' => __( 'General settings', 'yith-woocommerce-cart-messages' ),
			'type' => 'title',
			'id'   => 'ywcm_section_general'
		),

        'show_in_cart' => array(
            'name'    => __( 'Show in cart page', 'yith-woocommerce-cart-messages' ),
            'desc'    => '',
            'id'      => 'ywcm_show_in_cart',
            'default' => 'yes',
            'type'    => 'checkbox'
        ),

        'show_in_checkout' => array(
            'name'    => __( 'Show in checkout page', 'yith-woocommerce-cart-messages' ),
            'desc'    => '',
            'id'      => 'ywcm_show_in_checkout',
            'default' => 'yes',
            'type'    => 'checkbox'
        ),

        'show_in_shop_page' => array(
            'name'    => __( 'Show in shop page', 'yith-woocommerce-cart-messages' ),
            'desc'    => '',
            'id'      => 'ywcm_show_in_shop_page',
            'default' => 'no',
            'type'    => 'checkbox'
        ),

        'show_in_single_product' => array(
            'name'    => __( 'Show in single product page', 'yith-woocommerce-cart-messages' ),
            'desc'    => '',
            'id'      => 'ywcm_show_in_single_product',
            'default' => 'no',
            'type'    => 'checkbox'
        ),


		'section_general_settings_end' => array(
			'type' => 'sectionend',
			'id'   => 'ywcm_section_general_end'
		)
	)
);