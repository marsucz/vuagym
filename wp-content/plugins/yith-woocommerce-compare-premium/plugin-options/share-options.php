<?php
/**
 * SHARE OPTIONS ARRAY
 *
 * @author Your Inspiration Themes
 * @package YITH Woocommerce Compare Premium
 * @version 2.0.0
 */

if ( ! defined( 'YITH_WOOCOMPARE' ) ) {
	exit;
} // Exit if accessed directly

$options = array(
    'share' => array(

	    array(
		    'title' => __( 'Share Options', 'yith-woocommerce-compare' ),
		    'type' => 'title',
		    'desc' => '',
		    'id' => 'yith-woocompare-share-options'
	    ),

	    array(
		    'id'        => 'yith-woocompare-enable-share',
		    'name'      => __( 'Enable Sharing', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'Check this option if you want to show the link to share the compare', 'yith-woocommerce-compare' ),
		    'type'      => 'checkbox',
		    'default'   => 'yes',
	    ),

	    array(
		    'name' => __( 'Show sharing in', 'yith-woocommerce-compare' ),
		    'desc' => __( 'Popup', 'yith-woocommerce-compare' ),
		    'id'   => 'yith-woocompare-share-in-popup',
		    'default' => 'yes',
		    'type' => 'checkbox',
		    'checkboxgroup' => 'start',
		    'custom_attributes' => array(
			    'data-deps' => 'yith-woocompare-enable-share'
		    )
	    ),

	    array(
		    'id'            => 'yith-woocompare-share-in-page',
		    'desc'          => __( 'Page', 'yith-woocommerce-compare' ),
		    'type'          => 'checkbox',
		    'default'       => 'yes',
		    'checkboxgroup' => 'end',
	    ),

	    array(
		    'id'              => 'yith-woocompare-share-socials',
		    'name'            => __( 'Select Social Network Sites', 'yith-woocommerce-compare' ),
		    'type'            => 'multiselect',
		    'options'         => array(
			    'facebook'      => __( 'Facebook', 'yith-woocommerce-compare' ),
			    'twitter'       => __( 'Twitter', 'yith-woocommerce-compare' ),
			    'google-plus'   => __( 'Google+', 'yith-woocommerce-compare' ),
			    'pinterest'     => __( 'Pinterest', 'yith-woocommerce-compare' ),
			    'mail'          => __( 'eMail', 'yith-woocommerce-compare' ),
		    ),
		    'class'           => 'yith-woocompare-chosen',
		    'default'         => array( 'facebook', 'twitter', 'google-plus', 'pinterest', 'mail' ),
		    'custom_attributes' => array(
			    'data-deps' => 'yith-woocompare-enable-share'
		    )
	    ),

	    array(
		    'id'        => 'yith_woocompare_socials_title',
		    'name'      => __( 'Title for Social Network Sharing', 'yith-woocommerce-compare' ),
		    'type'      => 'text',
		    'std'       => __( 'My Compare', 'yith-woocommerce-compare' ),
		    'default'   => __( 'My Compare', 'yith-woocommerce-compare' ),
		    'custom_attributes' => array(
			    'data-deps' => 'yith-woocompare-enable-share'
		    )
	    ),

	    array(
		    'id'        => 'yith_woocompare_socials_text',
		    'name'      => __( 'Text for Social Network Sharing', 'yith-woocommerce-compare' ),
		    'desc'      => __( 'It will be used on Facebook, Twitter and Pinterest. Use %compare_url% where you want to show the URL of your compare.', 'yith-woocommerce-compare' ),
		    'type'      => 'textarea',
		    'std'       => '',
		    'default'   => '',
		    'custom_attributes' => array(
			    'data-deps' => 'yith-woocompare-enable-share'
		    )
	    ),

	    array(
		    'type'      => 'sectionend',
		    'id'        => 'yith-woocompare-share-options-end'
	    ),

	    array(
			    'title' => __( 'Facebook Options', 'yith-woocommerce-compare' ),
			    'type' => 'title',
			    'desc' => '',
			    'id' => 'yith-woocompare-facebook-options'
	    ),

	    array(
			    'id'        => 'yith_woocompare_facebook_appid',
			    'name'      => __( 'Facebook App ID', 'yith-woocommerce-compare' ),
		        'desc'      => sprintf( __( 'Facebook App ID necessary to share contents. Read more in the official Facebook <a href="%s">documentation</a>', 'yith-woocommerce-compare' ), 'https://developers.facebook.com/docs/apps/register' ),
			    'type'      => 'text',
			    'std'       => '',
			    'default'   => ''
	    ),

	    array(
			    'name' => __( 'Facebook image', 'yith-woocommerce-compare' ),
			    'desc' => __( 'Select an image for Facebook sharing.', 'yith-woocommerce-compare' ),
			    'id'   => 'yith_woocompare_facebook_image',
			    'std'  => '',
			    'default'  => '',
			    'type' => 'yith_woocompare_upload'
	    ),

	    array(
		    'type'      => 'sectionend',
		    'id'        => 'yith-woocompare-facebook-options-end'
	    ),
    )
);

return apply_filters( 'yith_woocompare_share_settings', $options );
