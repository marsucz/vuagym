<?php
/**
 * Created by PhpStorm.
 * User: Your Inspiration
 * Date: 18/03/2015
 * Time: 13:53
 */
global $YIT_Tab_Manager;

$desc_priority = sprintf('%s: %s: 10, %s: 20, %s: 30',
                __('WooCommerce Tabs are this priority','yith-woocommerce-tab-manager' ),
                __('Description','yith-woocommerce-tab-manager'),
                __('Additional Information','yith-woocommerce-tab-manager'),
                __('Reviews','yith-woocommerce-tab-manager') );
$args	=	array (
    'label'    => __( 'Tab Settings', 'yith-woocommerce-tab-manager' ),
    'pages'    => 'ywtm_tab', //or array( 'post-type1', 'post-type2')
    'context'  => 'normal', //('normal', 'advanced', or 'side')
    'priority' => 'default',
    'tabs'     => array(
        'settings' => array(
            'label'  => __( 'Settings', 'yith-woocommerce-tab-manager' ),
            'fields' => apply_filters( 'ywtm_options_metabox', array(

                    'ywtm_tab_type' =>  array(
                        'label' => __( 'Tab Type', 'yith-woocommerce-tab-manager' ),
                        'desc'  => __( 'Choose the type of the tab', 'yith-woocommerce-tab-manager' ),
                        'type'  => 'select',
                        'options' => $YIT_Tab_Manager->get_tab_types(),
                        'std'   => 'global' ),

                    /*Option "chosen" if  tab_type=category*/
                    'ywtm_tab_category' => array(
                        'label' =>  __('Choose Product Category','yith-woocommerce-tab-manager'),
                        'desc'  =>  __('Choose the product categories in which you want to show the tab','yith-woocommerce-tab-manager'),
                        'type'  =>  'ywctab-ajax-category',
                        'multiple' => true,
                        'ajax-action' => 'yith_tab_manager_json_search_product_categories',
                        'deps'     => array(
                            'ids'    => '_ywtm_tab_type',
                            'values' => 'category',
                                ),
                        ),

                    /*Option "chosen" if tab_type=product*/
                    'ywtm_tab_product' => array(
                        'label' =>  __('Choose Product','yith-woocommerce-tab-manager'),
                        'desc'  =>  __('Choose the Products in which you want to show the tab','yith-woocommerce-tab-manager'),
                        'type'  =>  'ywctab-ajax-product',
                        'multiple' => true,
                        'std'      =>   array(),
                        'options'   => array(),
                        'id' => 'ajax_ywtm_tab_product',
                        'deps'     => array(
                            'ids'    => '_ywtm_tab_type',
                            'values' => 'product',
                        ),
                    ),

                    'ywtm_show_tab' => array(
                        'label' => __( 'Enable Tab', 'yith-woocommerce-tab-manager' ),
                        'desc'  => __('Show the tab in the front end', 'yith-woocommerce-tab-manager'),
                        'type'  => 'checkbox',
                        'std'   =>  1 ),

                    'ywtm_order_tab'	=>	array(
                        'label' 	=> __( 'Priority Tab', 'yith-woocommerce-tab-manager' ),
                        'desc'  	=> $desc_priority,
                        'type'  	=> 'number',
                        'std'		=>  1,
                        'min'		=>  1,
                        'max'		=>  99	),

                   'ywtm_icon_tab' =>  array(
                        'label' =>  __( 'Icon Tab', 'yith-woocommerce-tab-manager' ),
                        'desc'  =>  '',
                        'type'  =>  'iconlist',
                        'options' => array(
                            'select' => array(
                                'icon'   => __( 'Theme Icon', 'yit' ),
                                'custom' => __( 'Custom Icon', 'yit' ),
                                'none'   => __( 'None', 'yit' )
                            ),
                            'icon'   => ''
                            ),
                        'std'     => array(
                            'select' => 'icon',
                            'icon'   => 'retinaicon-font:retina-the-essentials-082',
                            'custom' => ''
                        )
                    ),
                )

            ),

        ),


    )

);



return $args;