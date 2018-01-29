<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:20 AM
 */

add_action('admin_init', 's7upf_custom_meta_boxes');
if(!function_exists('s7upf_custom_meta_boxes')){
    function s7upf_custom_meta_boxes(){
        //Format content
        $format_metabox = array(
            'id' => 'block_format_content',
            'title' => esc_html__('Format Settings', 'kuteshop'),
            'desc' => '',
            'pages' => array('post'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(                
                array(
                    'id' => 'format_image',
                    'label' => esc_html__('Upload Image', 'kuteshop'),
                    'type' => 'upload',
                ),
                array(
                    'id' => 'format_gallery',
                    'label' => esc_html__('Add Gallery', 'kuteshop'),
                    'type' => 'Gallery',
                ),
                array(
                    'id' => 'format_media',
                    'label' => esc_html__('Link Media', 'kuteshop'),
                    'type' => 'text',
                )
            ),
        );
        // SideBar
    	$sidebar_metabox_default = array(
            'id'        => 's7upf_sidebar_option',
            'title'     => 'Advanced Settings',
            'desc'      => '',
            'pages'     => array( 'page','post','product'),
            'context'   => 'side',
            'priority'  => 'low',
            'fields'    => array(
                array(
                    'id'          => 's7upf_sidebar_position',
                    'label'       => esc_html__('Sidebar position ','kuteshop'),
                    'type'        => 'select',
                    'std' => '',
                    'choices'     => array(
                        array(
                            'label'=>esc_html__('--Select--','kuteshop'),
                            'value'=>'',
                        ),
                        array(
                            'label'=>esc_html__('No Sidebar','kuteshop'),
                            'value'=>'no'
                        ),
                        array(
                            'label'=>esc_html__('Left sidebar','kuteshop'),
                            'value'=>'left'
                        ),
                        array(
                            'label'=>esc_html__('Right sidebar','kuteshop'),
                            'value'=>'right'
                        ),
                    ),

                ),
                array(
                    'id'        =>'s7upf_select_sidebar',
                    'label'     =>esc_html__('Selects sidebar','kuteshop'),
                    'type'      =>'sidebar-select',
                    'condition' => 's7upf_sidebar_position:not(no),s7upf_sidebar_position:not()',
                ),
                array(
                    'id'          => 's7upf_show_breadrumb',
                    'label'       => esc_html__('Show Breadcrumb','kuteshop'),
                    'type'        => 'select',
                    'choices'     => array(
                        array(
                            'label'=>esc_html__('--Select--','kuteshop'),
                            'value'=>'',
                        ),
                        array(
                            'label'=>esc_html__('Yes','kuteshop'),
                            'value'=>'yes'
                        ),
                        array(
                            'label'=>esc_html__('No','kuteshop'),
                            'value'=>'no'
                        ),
                    ),

                ),
                array(
                    'id'          => 's7upf_header_page',
                    'label'       => esc_html__('Choose page header','kuteshop'),
                    'type'        => 'select',
                    'choices'     => s7upf_list_header_page()
                ),
                array(
                    'id'          => 's7upf_footer_page',
                    'label'       => esc_html__('Choose page footer','kuteshop'),
                    'type'        => 'page-select'
                ),
                array(
                    'id'          => 'body_bg',
                    'label'       => esc_html__('Body Background','kuteshop'),
                    'type'        => 'colorpicker',
                ), 
            )
        );
        //Show page title
        $show_page_title = array(
            'id' => 'page_title_setting',
            'title' => esc_html__('Page setting', 'kuteshop'),
            'pages' => array('page'),
            'context' => 'normal',
            'priority' => 'high',
            'fields' => array(
                array(
                    'id' => 'show_title_page',
                    'label' => esc_html__('Show title', 'kuteshop'),
                    'type' => 'on-off',
                    'std'   => 'on',
                ),
                array(
                    'id'          => 'main_color',
                    'label'       => esc_html__('Main color','kuteshop'),
                    'type'        => 'colorpicker',
                ),
                array(
                    'id' => 'shop_ajax',
                    'label' => esc_html__('Shop Ajax', 'kuteshop'),
                    'type' => 'select',
                    'std'   => '',
                    'choices'     => array(
                        array(
                            'label'=>esc_html__('--Select--','kuteshop'),
                            'value'=>'',
                        ),
                        array(
                            'label'=>esc_html__('On','kuteshop'),
                            'value'=>'on'
                        ),
                        array(
                            'label'=>esc_html__('Off','kuteshop'),
                            'value'=>'off'
                        ),
                    ),
                ),
                array(
                    'id'          => 'show_header_page',
                    'label'       => esc_html__('Header page image','kuteshop'),
                    'type'        => 'select',
                    'choices'     => array(
                        array(
                            'label'=>esc_html__('--Select--','kuteshop'),
                            'value'=>'',
                        ),
                        array(
                            'label'=>esc_html__('Yes','kuteshop'),
                            'value'=>'on'
                        ),
                        array(
                            'label'=>esc_html__('No','kuteshop'),
                            'value'=>'off'
                        ),
                    ),
                ),
                array(
                    'id'          => 'header_page_image',
                    'label'       => esc_html__('Header page Image','kuteshop'),
                    'type'        => 'list-item',
                    'condition'   => 'show_header_page:is(on)',
                    'settings'    => array( 
                        array(
                            'id'          => 'header_image',
                            'label'       => esc_html__('Header image','kuteshop'),
                            'type'        => 'upload',
                        ),
                        array(
                            'id'          => 'header_des',
                            'label'       => esc_html__('Description','kuteshop'),
                            'type'        => 'text',
                        ),
                        array(
                            'id'          => 'header_link',
                            'label'       => esc_html__('Link','kuteshop'),
                            'type'        => 'text',
                        ),
                    ),
                ),
            )
        );
        $product_custom_tab = array(
            'id' => 'block_product_custom_tab',
            'title' => esc_html__('Product Display', 'kuteshop'),
            'desc' => '',
            'pages' => array('product'),
            'context' => 'normal',
            'priority' => 'low',
            'fields' => array(                
                array(
                    'id'          => 'product_tab_data',
                    'label'       => esc_html__('Custom Tab','kuteshop'),
                    'type'        => 'list-item',
                    'settings'    => array(
                        array(
                            'id' => 'tab_content',
                            'label' => esc_html__('Content', 'kuteshop'),
                            'type' => 'textarea',
                        ),
                    )
                ),                
                array(
                    'id' => 'des_content',
                    'label' => esc_html__('Special Short Description', 'kuteshop'),
                    'type' => 'textarea',
                ),
            ),
        );
        $product_trendding = array(
            'id' => 'product_trendding',
            'title' => esc_html__('Product Type', 'kuteshop'),
            'desc' => '',
            'pages' => array('product'),
            'context' => 'side',
            'priority' => 'high',
            'fields' => array(                
                array(
                    'id'    => 'trending_product',
                    'label' => esc_html__('Product Trendding', 'kuteshop'),
                    'type'        => 'on-off',
                    'std'         => 'off'
                ),
            ),
        );
        $product_metabox = array(
            'id' => 'block_product_thumb_hover',
            'title' => esc_html__('Product Settings', 'kuteshop'),
            'desc' => '',
            'pages' => array('product'),
            'context' => 'side',
            'priority' => 'low',
            'fields' => array(                
                array(
                    'id'    => 'product_thumb_hover',
                    'label' => esc_html__('Product hover image', 'kuteshop'),
                    'type'  => 'upload',
                ),
                array(
                    'id'          => 'attribute_style',
                    'label'       => esc_html__('Attributes Style','kuteshop'),
                    'type'        => 'select',
                    'choices'     => array(  
                        array(
                            'value'=> '',
                            'label'=> esc_html__("-- Select --", 'kuteshop'),
                        ),                                                  
                        array(
                            'value'=> 'default',
                            'label'=> esc_html__("Default", 'kuteshop'),
                        ),
                        array(
                            'value'=> 'special',
                            'label'=> esc_html__("Special", 'kuteshop'),
                        ),
                    )
                ),
                array(
                    'id'          => 'product_tab_detail',
                    'label'       => esc_html__('Product Tab Style','kuteshop'),
                    'type'        => 'select',
                    'choices'     => array(  
                        array(
                            'value'=> '',
                            'label'=> esc_html__("Normal", 'kuteshop'),
                        ),
                        array(
                            'value'=> 'tab-toggle',
                            'label'=> esc_html__("Tab Toggle", 'kuteshop'),
                        ),
                    )
                ),
            ),
        );
        if (function_exists('ot_register_meta_box')){
            ot_register_meta_box($format_metabox);
            ot_register_meta_box($sidebar_metabox_default);            
            ot_register_meta_box($show_page_title);
            ot_register_meta_box($product_custom_tab);
            ot_register_meta_box($product_trendding);
            ot_register_meta_box($product_metabox);
        }
    }
}
?>