<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:20 AM
 */
if(!function_exists('s7upf_set_theme_config')){
    function s7upf_set_theme_config(){
        global $s7upf_dir,$s7upf_config;
        /**************************************** BEGIN ****************************************/
        $s7upf_dir = get_template_directory_uri() . '/7upframe';
        $s7upf_config = array();
        $s7upf_config['dir'] = $s7upf_dir;
        $s7upf_config['css_url'] = $s7upf_dir . '/assets/css/';
        $s7upf_config['js_url'] = $s7upf_dir . '/assets/js/';
        $s7upf_config['nav_menu'] = array(
            'primary' => esc_html__( 'Primary Navigation', 'kuteshop' ),
        );
        $s7upf_config['mega_menu'] = '1';
        $s7upf_config['sidebars']=array(
            array(
                'name'              => esc_html__( 'Blog Sidebar', 'kuteshop' ),
                'id'                => 'blog-sidebar',
                'description'       => esc_html__( 'Widgets in this area will be shown on all blog page.', 'kuteshop'),
                'before_title'      => '<h3 class="widget-title">',
                'after_title'       => '</h3>',
                'before_widget'     => '<div id="%1$s" class="sidebar-widget widget %2$s">',
                'after_widget'      => '</div>',
            )
        );
        $s7upf_config['import_config'] = array(
                'homepage_default'          => 'Home',
                'blogpage_default'          => 'Blog',
                'menu_locations'            => array("Main Menu" => "primary"),
                'set_woocommerce_page'      => 1
            );
        $s7upf_config['import_theme_option'] = 'YTo2ODp7czoxNzoiczd1cGZfaGVhZGVyX3BhZ2UiO3M6MzoiMTY2IjtzOjE3OiJzN3VwZl9mb290ZXJfcGFnZSI7czoxOiI4IjtzOjE0OiJzN3VwZl80MDRfcGFnZSI7czo0OiIxOTU4IjtzOjE2OiJzaG93X2hlYWRlcl9wYWdlIjtzOjI6Im9uIjtzOjE3OiJoZWFkZXJfcGFnZV9zdHlsZSI7czowOiIiO3M6MTc6ImhlYWRlcl9wYWdlX2ltYWdlIjthOjI6e2k6MDthOjQ6e3M6NToidGl0bGUiO3M6MjQ6IkJlc3QgU2VsbGluZyBTbWFydCBQaG9uZSI7czoxMjoiaGVhZGVyX2ltYWdlIjtzOjgzOiJodHRwOi8vN3VwdGhlbWUuY29tL3dvcmRwcmVzcy9rdXRlc2hvcC93cC1jb250ZW50L3VwbG9hZHMvMjAxNi8xMi9ibi1ncmlkLWJveGVkLmpwZyI7czoxMDoiaGVhZGVyX2RlcyI7czoxODoiQXBwbGUsIFNhbXN1bmcsIExHIjtzOjExOiJoZWFkZXJfbGluayI7czoxOiIjIjt9aToxO2E6NDp7czo1OiJ0aXRsZSI7czoxODoiRmFzaGlvbiBzdHlsZSAyMDE2IjtzOjEyOiJoZWFkZXJfaW1hZ2UiO3M6ODI6Imh0dHA6Ly83dXB0aGVtZS5jb20vd29yZHByZXNzL2t1dGVzaG9wL3dwLWNvbnRlbnQvdXBsb2Fkcy8yMDE2LzEyL2JuLWdyaWQtYWpheC5qcGciO3M6MTA6ImhlYWRlcl9kZXMiO3M6Mjc6IkNhbmlmYSwgTWluYWxvLCBOZXcgRmFzaGlvbiI7czoxMToiaGVhZGVyX2xpbmsiO3M6MToiIyI7fX1zOjEwOiJlbmFibGVfcnRsIjtzOjM6Im9mZiI7czoyMDoiczd1cGZfc2hvd19icmVhZHJ1bWIiO3M6Mjoib24iO3M6MTk6InM3dXBmX2JnX2JyZWFkY3J1bWIiO3M6MDoiIjtzOjE1OiJzaG93X3Njcm9sbF90b3AiO3M6Mjoib24iO3M6MTA6Im1haW5fY29sb3IiO3M6MDoiIjtzOjExOiJtYXBfYXBpX2tleSI7czozOToiQUl6YVN5QlgySWlFQmctMGxRS1FRNndrNnNXUkdRbldJN2lvZ2YwIjtzOjEwOiJjdXN0b21fY3NzIjtzOjA6IiI7czo0OiJsb2dvIjtzOjc2OiJodHRwOi8vN3VwdGhlbWUuY29tL3dvcmRwcmVzcy9rdXRlc2hvcC93cC1jb250ZW50L3VwbG9hZHMvMjAxNi8xMi9sb2dvLTEucG5nIjtzOjc6ImZhdmljb24iO3M6NzM6Imh0dHA6Ly83dXB0aGVtZS5jb20vd29yZHByZXNzL2t1dGVzaG9wL3dwLWNvbnRlbnQvdXBsb2Fkcy8yMDE2LzEyLzd1cC5qcGciO3M6MTY6InM3dXBmX21lbnVfZml4ZWQiO3M6Mjoib24iO3M6MTY6InM3dXBmX21lbnVfY29sb3IiO3M6MDoiIjtzOjIyOiJzN3VwZl9tZW51X2NvbG9yX2hvdmVyIjtzOjA6IiI7czoyMzoiczd1cGZfbWVudV9jb2xvcl9hY3RpdmUiO3M6MDoiIjtzOjE3OiJzN3VwZl9tZW51X2NvbG9yMiI7czowOiIiO3M6MjM6InM3dXBmX21lbnVfY29sb3JfaG92ZXIyIjtzOjA6IiI7czoyNDoiczd1cGZfbWVudV9jb2xvcl9hY3RpdmUyIjtzOjA6IiI7czoyNzoiczd1cGZfc2lkZWJhcl9wb3NpdGlvbl9ibG9nIjtzOjQ6ImxlZnQiO3M6MTg6InM3dXBmX3NpZGViYXJfYmxvZyI7czoxMjoiYmxvZy1zaWRlYmFyIjtzOjEzOiJzdl9zdHlsZV9ibG9nIjtzOjc6ImNvbnRlbnQiO3M6Mjc6InM3dXBmX3NpZGViYXJfcG9zaXRpb25fcGFnZSI7czoyOiJubyI7czoxODoiczd1cGZfc2lkZWJhcl9wYWdlIjtzOjA6IiI7czozNToiczd1cGZfc2lkZWJhcl9wb3NpdGlvbl9wYWdlX2FyY2hpdmUiO3M6NDoibGVmdCI7czoyNjoiczd1cGZfc2lkZWJhcl9wYWdlX2FyY2hpdmUiO3M6MTI6ImJsb2ctc2lkZWJhciI7czoyNzoiczd1cGZfc2lkZWJhcl9wb3NpdGlvbl9wb3N0IjtzOjQ6ImxlZnQiO3M6MTg6InM3dXBmX3NpZGViYXJfcG9zdCI7czoxMjoiYmxvZy1zaWRlYmFyIjtzOjE3OiJzN3VwZl9hZGRfc2lkZWJhciI7YToxOntpOjA7YToyOntzOjU6InRpdGxlIjtzOjE5OiJXb29jb21tZXJjZSBTaWRlYmFyIjtzOjIwOiJ3aWRnZXRfdGl0bGVfaGVhZGluZyI7czoyOiJoMyI7fX1zOjEyOiJnb29nbGVfZm9udHMiO2E6MTp7aTowO2E6MTp7czo2OiJmYW1pbHkiO3M6MDoiIjt9fXM6MjY6InM3dXBmX3NpZGViYXJfcG9zaXRpb25fd29vIjtzOjQ6ImxlZnQiO3M6MTc6InM3dXBmX3NpZGViYXJfd29vIjtzOjE5OiJ3b29jb21tZXJjZS1zaWRlYmFyIjtzOjE1OiJzdl9zZXRfdGltZV93b28iO3M6MDoiIjtzOjk6InNob3BfYWpheCI7czozOiJvZmYiO3M6MTU6Indvb19zaG9wX251bWJlciI7czoyOiIyMCI7czoxNToid29vX3Nob3BfY29sdW1uIjtzOjE6IjQiO3M6MTQ6InNob3BfYm94X3N0eWxlIjtzOjE4OiJjb250ZW50LWdyaWQtYm94ZWQiO3M6MTA6InNob3Bfc3R5bGUiO3M6MDoiIjtzOjE4OiJwcm9kdWN0X2l0ZW1fc3R5bGUiO3M6MTQ6Iml0ZW0tcHJvLWNvbG9yIjtzOjE4OiJwcm9kdWN0X3NpemVfdGh1bWIiO3M6MDoiIjtzOjE3OiJwcm9kdWN0X3F1aWNrdmlldyI7czo0OiJzaG93IjtzOjIzOiJwcm9kdWN0X3F1aWNrdmlld19zdHlsZSI7czo0OiJwbHVzIjtzOjIxOiJwcm9kdWN0X3F1aWNrdmlld19wb3MiO3M6MDoiIjtzOjE4OiJwcm9kdWN0X2V4dHJhX2xpbmsiO3M6NjoiaGlkZGVuIjtzOjE5OiJwcm9kdWN0X2V4dHJhX3N0eWxlIjtzOjA6IiI7czoxMzoicHJvZHVjdF9sYWJlbCI7czo0OiJzaG93IjtzOjMwOiJzdl9zaWRlYmFyX3Bvc2l0aW9uX3dvb19zaW5nbGUiO3M6NDoibGVmdCI7czoyMToic3Zfc2lkZWJhcl93b29fc2luZ2xlIjtzOjE5OiJ3b29jb21tZXJjZS1zaWRlYmFyIjtzOjE1OiJhdHRyaWJ1dGVfc3R5bGUiO3M6Nzoic3BlY2lhbCI7czoxOToid29vX2F0dHJfYmFja2dyb3VuZCI7YTo5OntpOjA7YTozOntzOjU6InRpdGxlIjtzOjU6IkJsYWNrIjtzOjk6ImF0dHJfc2x1ZyI7czo1OiJibGFjayI7czo3OiJhdHRyX2JnIjtzOjc6IiM0MDQwNDAiO31pOjE7YTozOntzOjU6InRpdGxlIjtzOjY6Ik9yYW5nZSI7czo5OiJhdHRyX3NsdWciO3M6Njoib3JhbmdlIjtzOjc6ImF0dHJfYmciO3M6NzoiI2ZmYmI1MSI7fWk6MjthOjM6e3M6NToidGl0bGUiO3M6NDoiQmx1ZSI7czo5OiJhdHRyX3NsdWciO3M6NDoiYmx1ZSI7czo3OiJhdHRyX2JnIjtzOjc6IiM4NjhmZmYiO31pOjM7YTozOntzOjU6InRpdGxlIjtzOjQ6IkN5YW4iO3M6OToiYXR0cl9zbHVnIjtzOjQ6ImN5YW4iO3M6NzoiYXR0cl9iZyI7czo3OiIjODBlNmZmIjt9aTo0O2E6Mzp7czo1OiJ0aXRsZSI7czo1OiJHcmVlbiI7czo5OiJhdHRyX3NsdWciO3M6NToiZ3JlZW4iO3M6NzoiYXR0cl9iZyI7czo3OiIjMzhjZjQ2Ijt9aTo1O2E6Mzp7czo1OiJ0aXRsZSI7czo0OiJQaW5rIjtzOjk6ImF0dHJfc2x1ZyI7czo0OiJwaW5rIjtzOjc6ImF0dHJfYmciO3M6NzoiI2ZmOGZmOCI7fWk6NjthOjM6e3M6NToidGl0bGUiO3M6MzoiUmVkIjtzOjk6ImF0dHJfc2x1ZyI7czozOiJyZWQiO3M6NzoiYXR0cl9iZyI7czo3OiIjZmY1OTZkIjt9aTo3O2E6Mzp7czo1OiJ0aXRsZSI7czo1OiJXaGl0ZSI7czo5OiJhdHRyX3NsdWciO3M6NToid2hpdGUiO3M6NzoiYXR0cl9iZyI7czo3OiIjZmZmZmZmIjt9aTo4O2E6Mzp7czo1OiJ0aXRsZSI7czo2OiJZZWxsb3ciO3M6OToiYXR0cl9zbHVnIjtzOjY6InllbGxvdyI7czo3OiJhdHRyX2JnIjtzOjc6IiNmZmRiMzMiO319czoxODoicHJvZHVjdF90YWJfZGV0YWlsIjtzOjA6IiI7czoxODoic2hvd19zaW5nbGVfbnVtYmVyIjtzOjA6IiI7czoyNToicHJvZHVjdF9pdGVtX3N0eWxlX3NpbmdsZSI7czoxNDoiaXRlbS1wcm8tY29sb3IiO3M6MjU6InByb2R1Y3RfZXh0cmFfbGlua19zaW5nbGUiO3M6NDoic2hvdyI7czoyNjoicHJvZHVjdF9leHRyYV9zdHlsZV9zaW5nbGUiO3M6MDoiIjtzOjE5OiJzaG93X3NpbmdsZV9sYXN0ZXN0IjtzOjM6Im9mZiI7czoxODoic2hvd19zaW5nbGVfdXBzZWxsIjtzOjI6Im9uIjtzOjE4OiJzaG93X3NpbmdsZV9yZWxhdGUiO3M6Mzoib2ZmIjtzOjExOiJ3b29fY2F0ZWxvZyI7czozOiJvZmYiO3M6MTE6ImhpZGVfZGV0YWlsIjtzOjM6Im9mZiI7czoxNToiaGlkZV9vdGhlcl9wYWdlIjtzOjM6Im9mZiI7czoxMDoiaGlkZV9hZG1pbiI7czozOiJvZmYiO3M6MTA6ImhpZGVfcHJpY2UiO3M6Mzoib2ZmIjtzOjEzOiJoaWRlX21pbmljYXJ0IjtzOjM6Im9mZiI7czo5OiJoaWRlX3Nob3AiO3M6Mzoib2ZmIjt9';
        $s7upf_config['import_widget'] = '{"blog-sidebar":{"categories-2":{"title":"BLOG CATEGORIES","count":0,"hierarchical":0,"dropdown":0},"s7upf_listpostswidget-2":{"title":"RECENT POSTS","posts_per_page":"3","category":"0","order":"desc","order_by":"Post Date"},"tag_cloud-2":{"title":"TAG CLOUD","taxonomy":"post_tag"},"s7upf_advantage_widget-2":{"title":"BRANDS","style":"list-brand","advs":{"1":{"link":"#","title":"Philips","des":"Extra 9% Off On Non Electronics","image":"http:\/\/7uptheme.com\/wordpress\/kuteshop\/wp-content\/uploads\/2016\/10\/logo1.png"},"2":{"link":"#","title":"Canon","des":"Upto 50% + Non Electronics","image":"http:\/\/7uptheme.com\/wordpress\/kuteshop\/wp-content\/uploads\/2016\/10\/logo2.png"},"3":{"link":"#","title":"Samsung","des":"Flat 5% To 35% Off Best Price","image":"http:\/\/7uptheme.com\/wordpress\/kuteshop\/wp-content\/uploads\/2016\/10\/logo20.png"}}},"s7upf_advantage_widget-3":{"title":"SLIDE BANNER","style":"slider","advs":{"1":{"link":"#","title":"","des":"","image":"http:\/\/7uptheme.com\/wordpress\/kuteshop\/wp-content\/uploads\/2016\/12\/banner-1.jpg"},"2":{"link":"#","title":"","des":"","image":"http:\/\/7uptheme.com\/wordpress\/kuteshop\/wp-content\/uploads\/2016\/12\/banner-2-1.jpg"}}}},"woocommerce-sidebar":{"sv_category_fillter-2":{"title":"Categories","category":["beauty-health","electronics","fashion","homeware","sport"]},"s7upf_attribute_filter-2":{"title":"Color","attribute":"color"},"s7upf_attribute_filter-3":{"title":"Size","attribute":"size"},"woocommerce_price_filter-2":{"title":"Price"},"s7upf_list_products-2":{"title":"BEST SELLERS","number":"6","product_type":""},"s7upf_advantage_widget-4":{"title":"BRANDS","style":"list-brand","advs":{"1":{"link":"#","title":"Philips","des":"Extra 9% Off On Non Electronics","image":"http:\/\/7uptheme.com\/wordpress\/kuteshop\/wp-content\/uploads\/2016\/10\/logo1.png"},"2":{"link":"#","title":"Canon","des":"Upto 50% + Non Electronics","image":"http:\/\/7uptheme.com\/wordpress\/kuteshop\/wp-content\/uploads\/2016\/10\/logo2.png"},"3":{"link":"#","title":"Samsung","des":"Flat 5% To 35% Off Best Price","image":"http:\/\/7uptheme.com\/wordpress\/kuteshop\/wp-content\/uploads\/2016\/10\/logo20.png"}}}}}';
        $s7upf_config['import_category'] = '{"accessories":{"thumbnail":"1206","parent":"electronics"},"bags":{"thumbnail":"1031","parent":"fashion"},"beauty-health":{"thumbnail":"","parent":""},"ceiling-lights":{"thumbnail":"1203","parent":"homeware"},"chair":{"thumbnail":"1201","parent":"homeware"},"computer":{"thumbnail":"1208","parent":"electronics"},"cooking-tools":{"thumbnail":"1204","parent":"homeware"},"decoration-crafts":{"thumbnail":"1200","parent":"homeware"},"drinkware":{"thumbnail":"1202","parent":"homeware"},"electronics":{"thumbnail":"","parent":""},"fashion":{"thumbnail":"","parent":""},"fitness":{"thumbnail":"1196","parent":"sport"},"hiking-jackets":{"thumbnail":"1198","parent":"sport"},"hiking-shoes":{"thumbnail":"1199","parent":"sport"},"homeware":{"thumbnail":"","parent":""},"human-hair":{"thumbnail":"1213","parent":"beauty-health"},"laptop":{"thumbnail":"1205","parent":"electronics"},"lipstick":{"thumbnail":"1214","parent":"beauty-health"},"makeup":{"thumbnail":"1212","parent":"beauty-health"},"makeup-brushes":{"thumbnail":"1211","parent":"beauty-health"},"mobile-tablet":{"thumbnail":"1209","parent":"electronics"},"mouse":{"thumbnail":"1207","parent":"electronics"},"nail-polish":{"thumbnail":"1215","parent":"beauty-health"},"shirt":{"thumbnail":"1022","parent":"fashion"},"shoes":{"thumbnail":"1019","parent":"fashion"},"short":{"thumbnail":"1026","parent":"fashion"},"sleeping-bags":{"thumbnail":"1197","parent":"sport"},"smart-watches":{"thumbnail":"1210","parent":"electronics"},"sport":{"thumbnail":"","parent":""},"sunglases":{"thumbnail":"1030","parent":"fashion"},"tents":{"thumbnail":"1195","parent":"sport"}}';

        /**************************************** PLUGINS ****************************************/

        $s7upf_config['require-plugin'] = array(    
            array(
                'name'               => esc_html__('Option Tree', 'kuteshop'), // The plugin name.
                'slug'               => 'option-tree', // The plugin slug (typically the folder name).
                'required'           => true, // If false, the plugin is only 'recommended' instead of required.
            ),
            array(
                'name'      => esc_html__( 'Contact Form 7', 'kuteshop'),
                'slug'      => 'contact-form-7',
                'required'  => true,
            ),
            array(
                'name'      => esc_html__( 'Visual Composer', 'kuteshop'),
                'slug'      => 'js_composer',
                'required'  => true,
                'source'    =>get_template_directory_uri().'/plugins/js_composer.zip'
            ),
            array(
                'name'      => esc_html__( '7up Core', 'kuteshop'),
                'slug'      => '7up-core',
                'required'  => true,
                'source'    =>get_template_directory_uri().'/plugins/7up-core.zip'
            ),
            array(
                'name'      => esc_html__( 'WooCommerce', 'kuteshop'),
                'slug'      => 'woocommerce',
                'required'  => true,
            ),
            array(
                'name'      => esc_html__('MailChimp for WordPress Lite','kuteshop'),
                'slug'      => 'mailchimp-for-wp',
                'required'  => true,
            ),
            array(
                'name'      => esc_html__('Yith Woocommerce Compare','kuteshop'),
                'slug'      => 'yith-woocommerce-compare',
                'required'  => true,
            ),
            array(
                'name'      => esc_html__('Yith Woocommerce Wishlist','kuteshop'),
                'slug'      => 'yith-woocommerce-wishlist',
                'required'  => true,
            )
        );

    /**************************************** PLUGINS ****************************************/
        $s7upf_config['theme-option'] = array(
            'sections' => array(
                array(
                    'id' => 'option_general',
                    'title' => '<i class="fa fa-cog"></i>'.esc_html__(' General Settings', 'kuteshop')
                ),
                array(
                    'id' => 'option_logo',
                    'title' => '<i class="fa fa-image"></i>'.esc_html__(' Logo Settings', 'kuteshop')
                ),
                array(
                    'id' => 'option_menu',
                    'title' => '<i class="fa fa-align-justify"></i>'.esc_html__(' Menu Settings', 'kuteshop')
                ),
                array(
                    'id' => 'option_layout',
                    'title' => '<i class="fa fa-columns"></i>'.esc_html__(' Layout Settings', 'kuteshop')
                ),
                array(
                    'id' => 'option_typography',
                    'title' => '<i class="fa fa-font"></i>'.esc_html__(' Typography', 'kuteshop')
                )
            ),
            'settings' => array(
                 /*----------------Begin General --------------------*/
                array(
                    'id'          => 's7upf_header_page',
                    'label'       => esc_html__( 'Header Page', 'kuteshop' ),
                    'desc'        => esc_html__( 'Include page to Header', 'kuteshop' ),
                    'type'        => 'select',
                    'section'     => 'option_general',
                    'choices'     => s7upf_list_header_page()
                ),
                array(
                    'id'          => 's7upf_footer_page',
                    'label'       => esc_html__( 'Footer Page', 'kuteshop' ),
                    'desc'        => esc_html__( 'Include page to Footer', 'kuteshop' ),
                    'type'        => 'page-select',
                    'section'     => 'option_general'
                ),
                array(
                    'id'          => 's7upf_404_page',
                    'label'       => esc_html__( '404 Page', 'kuteshop' ),
                    'desc'        => esc_html__( 'Include page to 404 page', 'kuteshop' ),
                    'type'        => 'page-select',
                    'section'     => 'option_general'
                ),
                array(
                    'id'          => 'show_header_page',
                    'label'       => esc_html__('Header page image','kuteshop'),
                    'type'        => 'on-off',
                    'section'     => 'option_general',
                    'std'         => 'off'
                ),
                array(
                    'id'          => 'header_page_style',
                    'label'       => esc_html__('Header image style','kuteshop'),
                    'type'        => 'select',
                    'section'     => 'option_general',
                    'condition'   => 'show_header_page:is(on)',
                    'choices'     => array(
                        array(
                            'value'=>'',
                            'label'=>esc_html__('Default','kuteshop'),
                        ),
                        array(
                            'value'=>'boxed-slider radius',
                            'label'=>esc_html__('Box Radius','kuteshop'),
                        ),
                        array(
                            'value'=>'full-width',
                            'label'=>esc_html__('Full Width','kuteshop'),
                        ),
                    )
                ),
                array(
                    'id'          => 'header_page_image',
                    'label'       => esc_html__('Header page Image','kuteshop'),
                    'type'        => 'list-item',
                    'section'     => 'option_general',
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
                array(
                    'id' => 'enable_rtl',
                    'label' => esc_html__('Enqueue RTL style', 'kuteshop'),
                    'type' => 'on-off',
                    'section' => 'option_general',
                    'std' => 'off'
                ),
                array(
                    'id' => 's7upf_show_breadrumb',
                    'label' => esc_html__('Show BreadCrumb', 'kuteshop'),
                    'desc' => esc_html__('This allow you to show or hide BreadCrumb', 'kuteshop'),
                    'type' => 'on-off',
                    'section' => 'option_general',
                    'std' => 'on'
                ),
                array(
                    'id'          => 's7upf_bg_breadcrumb',
                    'label'       => esc_html__('Background Breadcrumb','kuteshop'),
                    'type'        => 'background',
                    'section'     => 'option_general',
                    'condition'   => 's7upf_show_breadrumb:is(on)',
                ),
                array(
                    'id' => 'show_scroll_top',
                    'label' => esc_html__('Show Scroll Top', 'kuteshop'),
                    'desc' => esc_html__('This allow you to show or hide Scroll top button', 'kuteshop'),
                    'type' => 'on-off',
                    'section' => 'option_general',
                    'std' => 'off'
                ),
                array(
                    'id'          => 'main_color',
                    'label'       => esc_html__('Main color','kuteshop'),
                    'type'        => 'colorpicker',
                    'section'     => 'option_general',
                ),                
                array(
                    'id'          => 'map_api_key',
                    'label'       => esc_html__('Map API key','kuteshop'),
                    'type'        => 'text',
                    'section'     => 'option_general',
                    'std'         => '',
                ),
                array(
                    'id'          => 'custom_css',
                    'label'       => esc_html__('Custom CSS','kuteshop'),
                    'type'        => 'textarea-simple',
                    'section'     => 'option_general',
                ),
                /*----------------End General ----------------------*/

                /*----------------Begin Logo --------------------*/
                array(
                    'id' => 'logo',
                    'label' => esc_html__('Logo', 'kuteshop'),
                    'desc' => esc_html__('This allow you to change logo', 'kuteshop'),
                    'type' => 'upload',
                    'section' => 'option_logo',
                ),        
                array(
                    'id' => 'favicon',
                    'label' => esc_html__('Favicon', 'kuteshop'),
                    'desc' => esc_html__('This allow you to change favicon of your website', 'kuteshop'),
                    'type' => 'upload',
                    'section' => 'option_logo'
                ),
                /*----------------End Logo ----------------------*/

                /*----------------Begin Menu --------------------*/
                array(
                    'id'          => 's7upf_menu_fixed',
                    'label'       => esc_html__('Menu Fixed','kuteshop'),
                    'desc'        => 'Menu change to fixed when scroll',
                    'type'        => 'on-off',
                    'section'     => 'option_menu',
                    'std'         => 'on',
                ),
                array(
                    'id'          => 's7upf_menu_color',
                    'label'       => esc_html__('Menu style','kuteshop'),
                    'type'        => 'typography',
                    'section'     => 'option_menu',
                ),
                array(
                    'id'          => 's7upf_menu_color_hover',
                    'label'       => esc_html__('Hover color','kuteshop'),
                    'desc'        => esc_html__('Choose color','kuteshop'),
                    'type'        => 'colorpicker',
                    'section'     => 'option_menu',
                ),
                array(
                    'id'          => 's7upf_menu_color_active',
                    'label'       => esc_html__('Background hover color','kuteshop'),
                    'desc'        => esc_html__('Choose color','kuteshop'),
                    'type'        => 'colorpicker',
                    'section'     => 'option_menu',
                ),
                array(
                    'id'          => 's7upf_menu_color2',
                    'label'       => esc_html__('Menu sub style','kuteshop'),
                    'type'        => 'typography',
                    'section'     => 'option_menu',
                ),
                array(
                    'id'          => 's7upf_menu_color_hover2',
                    'label'       => esc_html__('Hover sub color','kuteshop'),
                    'desc'        => esc_html__('Choose color','kuteshop'),
                    'type'        => 'colorpicker',
                    'section'     => 'option_menu',
                ),
                array(
                    'id'          => 's7upf_menu_color_active2',
                    'label'       => esc_html__('Background hover sub color','kuteshop'),
                    'desc'        => esc_html__('Choose color','kuteshop'),
                    'type'        => 'colorpicker',
                    'section'     => 'option_menu',
                ),
                /*----------------End Menu ----------------------*/
                

                /*----------------Begin Layout --------------------*/
                array(
                    'id'          => 's7upf_sidebar_position_blog',
                    'label'       => esc_html__('Sidebar Blog','kuteshop'),
                    'type'        => 'select',
                    'section'     => 'option_layout',
                    'desc'=>esc_html__('Left, or Right, or Center','kuteshop'),
                    'choices'     => array(
                        array(
                            'value'=>'no',
                            'label'=>esc_html__('No Sidebar','kuteshop'),
                        ),
                        array(
                            'value'=>'left',
                            'label'=>esc_html__('Left','kuteshop'),
                        ),
                        array(
                            'value'=>'right',
                            'label'=>esc_html__('Right','kuteshop'),
                        )
                    )
                ),
                array(
                    'id'          => 's7upf_sidebar_blog',
                    'label'       => esc_html__('Sidebar select display in blog','kuteshop'),
                    'type'        => 'sidebar-select',
                    'section'     => 'option_layout',
                    'condition'   => 's7upf_sidebar_position_blog:not(no)',
                ),
                array(
                    'id'          => 'sv_style_blog',
                    'label'       => esc_html__('Blog list style','kuteshop'),
                    'type'        => 'select',
                    'section'     => 'option_layout',
                    'choices'     => array(
                        array(
                            'value'=>'content',
                            'label'=>esc_html__('Default','kuteshop'),
                        ),
                        array(
                            'value'=>'large',
                            'label'=>esc_html__('Large thumbnail','kuteshop'),
                        ),
                        array(
                            'value'=>'small',
                            'label'=>esc_html__('Small thumbnail','kuteshop'),
                        ),
                        array(
                            'value'=>'masonry',
                            'label'=>esc_html__('Masonry','kuteshop'),
                        )
                    )
                ),
                /****end blog****/
                array(
                    'id'          => 's7upf_sidebar_position_page',
                    'label'       => esc_html__('Sidebar Page','kuteshop'),
                    'type'        => 'select',
                    'section'     => 'option_layout',
                    'desc'=>esc_html__('Left, or Right, or Center','kuteshop'),
                    'choices'     => array(
                        array(
                            'value'=>'no',
                            'label'=>esc_html__('No Sidebar','kuteshop'),
                        ),
                        array(
                            'value'=>'left',
                            'label'=>esc_html__('Left','kuteshop'),
                        ),
                        array(
                            'value'=>'right',
                            'label'=>esc_html__('Right','kuteshop'),
                        )
                    )
                ),
                array(
                    'id'          => 's7upf_sidebar_page',
                    'label'       => esc_html__('Sidebar select display in page','kuteshop'),
                    'type'        => 'sidebar-select',
                    'section'     => 'option_layout',
                    'condition'   => 's7upf_sidebar_position_page:not(no)',
                ),
                /****end page****/
                array(
                    'id'          => 's7upf_sidebar_position_page_archive',
                    'label'       => esc_html__('Sidebar Position on Page Archives:','kuteshop'),
                    'type'        => 'select',
                    'section'     => 'option_layout',
                    'desc'=>esc_html__('Left, or Right, or Center','kuteshop'),
                    'choices'     => array(
                        array(
                            'value'=>'no',
                            'label'=>esc_html__('No Sidebar','kuteshop'),
                        ),
                        array(
                            'value'=>'left',
                            'label'=>esc_html__('Left','kuteshop'),
                        ),
                        array(
                            'value'=>'right',
                            'label'=>esc_html__('Right','kuteshop'),
                        )
                    )
                ),
                array(
                    'id'          => 's7upf_sidebar_page_archive',
                    'label'       => esc_html__('Sidebar select display in page Archives','kuteshop'),
                    'type'        => 'sidebar-select',
                    'section'     => 'option_layout',
                    'condition'   => 's7upf_sidebar_position_page_archive:not(no)',
                ),
                // END
                array(
                    'id'          => 's7upf_sidebar_position_post',
                    'label'       => esc_html__('Sidebar Single Post','kuteshop'),
                    'type'        => 'select',
                    'section'     => 'option_layout',
                    'desc'=>esc_html__('Left, or Right, or Center','kuteshop'),
                    'choices'     => array(
                        array(
                            'value'=>'no',
                            'label'=>esc_html__('No Sidebar','kuteshop'),
                        ),
                        array(
                            'value'=>'left',
                            'label'=>esc_html__('Left','kuteshop'),
                        ),
                        array(
                            'value'=>'right',
                            'label'=>esc_html__('Right','kuteshop'),
                        )
                    )
                ),
                array(
                    'id'          => 's7upf_sidebar_post',
                    'label'       => esc_html__('Sidebar select display in single post','kuteshop'),
                    'type'        => 'sidebar-select',
                    'section'     => 'option_layout',
                    'condition'   => 's7upf_sidebar_position_post:not(no)',
                ),
                array(
                    'id'          => 's7upf_add_sidebar',
                    'label'       => esc_html__('Add SideBar','kuteshop'),
                    'type'        => 'list-item',
                    'section'     => 'option_layout',
                    'std'         => '',
                    'settings'    => array( 
                        array(
                            'id'          => 'widget_title_heading',
                            'label'       => esc_html__('Choose heading title widget','kuteshop'),
                            'type'        => 'select',
                            'std'        => 'h3',
                            'choices'     => array(
                                array(
                                    'value'=>'h1',
                                    'label'=>esc_html__('H1','kuteshop'),
                                ),
                                array(
                                    'value'=>'h2',
                                    'label'=>esc_html__('H2','kuteshop'),
                                ),
                                array(
                                    'value'=>'h3',
                                    'label'=>esc_html__('H3','kuteshop'),
                                ),
                                array(
                                    'value'=>'h4',
                                    'label'=>esc_html__('H4','kuteshop'),
                                ),
                                array(
                                    'value'=>'h5',
                                    'label'=>esc_html__('H5','kuteshop'),
                                ),
                                array(
                                    'value'=>'h6',
                                    'label'=>esc_html__('H6','kuteshop'),
                                ),
                            )
                        ),
                    ),
                ),
                /*----------------End Layout ----------------------*/

                /*----------------Begin Blog ----------------------*/       
                

                /*----------------End BLOG----------------------*/

                /*----------------Begin Typography ----------------------*/
                array(
                    'id'          => 's7upf_custom_typography',
                    'label'       => esc_html__('Add Settings','kuteshop'),
                    'type'        => 'list-item',
                    'section'     => 'option_typography',
                    'std'         => '',
                    'settings'    => array(
                        array(
                            'id'          => 'typo_area',
                            'label'       => esc_html__('Choose Area to style','kuteshop'),
                            'type'        => 'select',
                            'std'        => 'main',
                            'choices'     => array(
                                array(
                                    'value'=>'header',
                                    'label'=>esc_html__('Header','kuteshop'),
                                ),
                                array(
                                    'value'=>'main',
                                    'label'=>esc_html__('Main Content','kuteshop'),
                                ),
                                array(
                                    'value'=>'widget',
                                    'label'=>esc_html__('Widget','kuteshop'),
                                ),
                                array(
                                    'value'=>'footer',
                                    'label'=>esc_html__('Footer','kuteshop'),
                                ),
                            )
                        ),
                        array(
                            'id'          => 'typo_heading',
                            'label'       => esc_html__('Choose heading Area','kuteshop'),
                            'type'        => 'select',
                            'std'        => 'h3',
                            'choices'     => array(
                                array(
                                    'value'=>'h1',
                                    'label'=>esc_html__('H1','kuteshop'),
                                ),
                                array(
                                    'value'=>'h2',
                                    'label'=>esc_html__('H2','kuteshop'),
                                ),
                                array(
                                    'value'=>'h3',
                                    'label'=>esc_html__('H3','kuteshop'),
                                ),
                                array(
                                    'value'=>'h4',
                                    'label'=>esc_html__('H4','kuteshop'),
                                ),
                                array(
                                    'value'=>'h5',
                                    'label'=>esc_html__('H5','kuteshop'),
                                ),
                                array(
                                    'value'=>'h6',
                                    'label'=>esc_html__('H6','kuteshop'),
                                ),
                                array(
                                    'value'=>'a',
                                    'label'=>esc_html__('a','kuteshop'),
                                ),
                                array(
                                    'value'=>'a:hover',
                                    'label'=>esc_html__('a hover','kuteshop'),
                                ),
                                array(
                                    'value'=>'p',
                                    'label'=>esc_html__('p','kuteshop'),
                                ),
                                array(
                                    'value'=>'ul',
                                    'label'=>esc_html__('ul','kuteshop'),
                                ),
                                array(
                                    'value'=>'ol',
                                    'label'=>esc_html__('ol','kuteshop'),
                                ),
                            )
                        ),
                        array(
                            'id'          => 'typography_style',
                            'label'       => esc_html__('Add Style','kuteshop'),
                            'type'        => 'typography',
                            'section'     => 'option_typography',
                        ),
                    ),
                ),        
                array(
                    'id'          => 'google_fonts',
                    'label'       => esc_html__('Add Google Fonts','kuteshop'),
                    'type'        => 'google-fonts',
                    'section'     => 'option_typography',
                ),
                /*----------------End Typography ----------------------*/
            )
        );
        if(class_exists( 'WooCommerce' )){
            array_push($s7upf_config['theme-option']['sections'], array(
                                                        'id' => 'option_woo',
                                                        'title' => '<i class="fa fa-shopping-cart"></i>'.esc_html__(' Shop Settings', 'kuteshop')
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 's7upf_sidebar_position_woo',
                                                        'label'       => esc_html__('Sidebar Position WooCommerce page','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_woo',
                                                        'desc'=>esc_html__('Left, or Right, or Center','kuteshop'),
                                                        'choices'     => array(
                                                            array(
                                                                'value'=>'no',
                                                                'label'=>esc_html__('No Sidebar','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=>'left',
                                                                'label'=>esc_html__('Left','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=>'right',
                                                                'label'=>esc_html__('Right','kuteshop'),
                                                            )
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 's7upf_sidebar_woo',
                                                        'label'       => esc_html__('Sidebar select WooCommerce page','kuteshop'),
                                                        'type'        => 'sidebar-select',
                                                        'section'     => 'option_woo',
                                                        'condition'   => 's7upf_sidebar_position_woo:not(no)',
                                                        'desc'        => esc_html__('Choose one style of sidebar for WooCommerce page','kuteshop'),

                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'sv_set_time_woo',
                                                        'label'       => esc_html__('Product new in(days)','kuteshop'),
                                                        'type'        => 'text',
                                                        'section'     => 'option_woo',
                                                        'desc'        => esc_html__('Enter number to set time for product is new. Unit day. Default is 30.','kuteshop')
                                                    ));            
             array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'shop_ajax',
                                                        'label'       => esc_html__('Shop ajax','kuteshop'),
                                                        'type'        => 'on-off',
                                                        'section'     => 'option_woo',
                                                        'std'         => 'off'
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'woo_shop_number',
                                                        'label'       => esc_html__('Product Number','kuteshop'),
                                                        'type'        => 'text',
                                                        'section'     => 'option_woo',
                                                        'desc'        => esc_html__('Enter number product to display per page. Default is 12.','kuteshop')
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'woo_shop_column',
                                                        'label'       => esc_html__('Choose shop column','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_woo',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> 1,
                                                                'label'=> 1,
                                                            ),
                                                            array(
                                                                'value'=> 2,
                                                                'label'=> 2,
                                                            ),
                                                            array(
                                                                'value'=> 3,
                                                                'label'=> 3,
                                                            ),
                                                            array(
                                                                'value'=> 4,
                                                                'label'=> 4,
                                                            ),
                                                            array(
                                                                'value'=> 5,
                                                                'label'=> 5,
                                                            ),
                                                            array(
                                                                'value'=> 6,
                                                                'label'=> 6,
                                                            ),
                                                            array(
                                                                'value'=> 7,
                                                                'label'=> 7,
                                                            ),
                                                            array(
                                                                'value'=> 8,
                                                                'label'=> 8,
                                                            ),
                                                            array(
                                                                'value'=> 9,
                                                                'label'=> 9,
                                                            ),
                                                            array(
                                                                'value'=> 10,
                                                                'label'=> 10,
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'shop_box_style',
                                                        'label'       => esc_html__('Shop Block Style','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_woo',
                                                        'std'         => 'content-grid-boxed',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> 'content-grid-boxed',
                                                                'label'=> esc_html__('Default','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'content-grid-no-boxed',
                                                                'label'=> esc_html__('Style 2','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'shop_style',
                                                        'label'       => esc_html__('Shop Style','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_woo',
                                                        'std'         => '',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> '',
                                                                'label'=> esc_html__('Default','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'load-more',
                                                                'label'=> esc_html__('Load More Button','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_item_style',
                                                        'label'       => esc_html__('Product Item Style','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_woo',
                                                        'std'         => 'item-pro-color',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> 'item-pro-color',
                                                                'label'=> esc_html__('Default','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'default',
                                                                'label'=> esc_html__('Style 2','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'tab-large-item',
                                                                'label'=> esc_html__('Style 3','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'item-pro-ajax',
                                                                'label'=> esc_html__('Style 4','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_size_thumb',
                                                        'label'       => esc_html__('Product size thumbnail','kuteshop'),
                                                        'type'        => 'text',
                                                        'section'     => 'option_woo',
                                                        'std'         => '',
                                                        'desc'        => esc_html__('Enter site thumbnail to crop. [width]x[height]. Example is 300x300.','kuteshop')
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_quickview',
                                                        'label'       => esc_html__('Quickview','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_woo',
                                                        'std'         => 'show',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> 'show',
                                                                'label'=> esc_html__('Show','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'hidden',
                                                                'label'=> esc_html__('Hidden','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_quickview_style',
                                                        'label'       => esc_html__('Quickview Style','kuteshop'),
                                                        'type'        => 'select',
                                                        'std'         => '',
                                                        'section'     => 'option_woo',
                                                        'condition'   => 'product_quickview:is(show)',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> '',
                                                                'label'=> esc_html__('Border bottom','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'plus',
                                                                'label'=> esc_html__('Plus icon','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_quickview_pos',
                                                        'label'       => esc_html__('Quickview Position','kuteshop'),
                                                        'type'        => 'select',
                                                        'std'         => '',
                                                        'section'     => 'option_woo',
                                                        'condition'   => 'product_quickview:is(show)',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> '',
                                                                'label'=> esc_html__('Top','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'pos-middle',
                                                                'label'=> esc_html__('Middle','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'pos-bottom',
                                                                'label'=> esc_html__('Bottom','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_extra_link',
                                                        'label'       => esc_html__('Extra Link','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_woo',
                                                        'std'         => 'hidden',
                                                        'choices'     => array(
                                                            array(
                                                                'value'=> 'hidden',
                                                                'label'=> esc_html__('Hidden','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'show',
                                                                'label'=> esc_html__('Show','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_extra_style',
                                                        'label'       => esc_html__('Extra Link Style','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_woo',
                                                        'std'         => '',
                                                        'condition'   => 'product_extra_link:is(show)',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> '',
                                                                'label'=> esc_html__('Style 1','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'home6',
                                                                'label'=> esc_html__('Style 2','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_label',
                                                        'label'       => esc_html__('Product Label','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_woo',
                                                        'std'         => 'show',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> 'show',
                                                                'label'=> esc_html__('Show','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'hidden',
                                                                'label'=> esc_html__('Hidden','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['sections'], array(
                                                        'id' => 'option_product',
                                                        'title' => '<i class="fa fa-th-large"></i>'.esc_html__(' Product Settings', 'kuteshop')
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'ka_custom_layout',
                                                        'label'       => esc_html__('Layout','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_product',
                                                        'std'         => 'no',
                                                        'choices'     => array(
                                                            array(
                                                                'value'=>'layout1',
                                                                'label'=>esc_html__('Layout 1','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=>'layout2',
                                                                'label'=>esc_html__('Layout 2','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'kacolor_title_text',
                                                        'label'       => esc_html__('Màu chữ Title','kuteshop'),
                                                        'type'        => 'colorpicker',
                                                        'section'     => 'option_product',
//                                                        'std'         => '#fff',
                                                    ),array(
                                                        'id'          => 'kacolor_title_bg',
                                                        'label'       => esc_html__('Màu nền Title','kuteshop'),
                                                        'type'        => 'colorpicker',
                                                        'section'     => 'option_product',
//                                                        'std'         => '#059',
                                                    ),array(
                                                        'id'          => 'kacolor_addtocart_text',
                                                        'label'       => esc_html__('Màu chữ Add-to-cart','kuteshop'),
                                                        'type'        => 'colorpicker',
                                                        'section'     => 'option_product',
//                                                        'std'         => '#fff',
                                                    ),array(
                                                        'id'          => 'kacolor_addtocart_bg',
                                                        'label'       => esc_html__('Màu nền Add-to-cart','kuteshop'),
                                                        'type'        => 'colorpicker',
                                                        'section'     => 'option_product',
//                                                        'std'         => '#079c3a',
                                                    ),array(
                                                        'id'          => 'kacolor_addtocart_hovertext',
                                                        'label'       => esc_html__('Màu chữ Add-to-cart (khi hover)','kuteshop'),
                                                        'type'        => 'colorpicker',
                                                        'section'     => 'option_product',
//                                                        'std'         => '#fff',
                                                    ),array(
                                                        'id'          => 'kacolor_addtocart_hoverbg',
                                                        'label'       => esc_html__('Màu nền Add-to-cart (khi hover)','kuteshop'),
                                                        'type'        => 'colorpicker',
                                                        'section'     => 'option_product',
//                                                        'std'         => '#005599',
                                                    ),array(
                                                        'id'          => 'kacolor_border_attribute',
                                                        'label'       => esc_html__('Màu border thuộc tính','kuteshop'),
                                                        'type'        => 'colorpicker',
                                                        'section'     => 'option_product',
//                                                        'std'         => '#059',
                                                    )
                                            );
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'sv_sidebar_position_woo_single',
                                                        'label'       => esc_html__('Sidebar Position WooCommerce Single','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_product',
                                                        'desc'=>esc_html__('Left, or Right, or Center','kuteshop'),
                                                        'std'         => 'no',
                                                        'choices'     => array(
                                                            array(
                                                                'value'=>'no',
                                                                'label'=>esc_html__('No Sidebar','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=>'left',
                                                                'label'=>esc_html__('Left','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=>'right',
                                                                'label'=>esc_html__('Right','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'sv_sidebar_woo_single',
                                                        'label'       => esc_html__('Sidebar select WooCommerce Single','kuteshop'),
                                                        'type'        => 'sidebar-select',
                                                        'section'     => 'option_product',
                                                        'condition'   => 'sv_sidebar_position_woo_single:not(no)',
                                                        'desc'        => esc_html__('Choose one style of sidebar for WooCommerce page','kuteshop'),
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'attribute_style',
                                                        'label'       => esc_html__('Attribute Style','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_product',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> 'normal',
                                                                'label'=> esc_html__("Normal", 'kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'special',
                                                                'label'=> esc_html__("Special", 'kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'woo_attr_background',
                                                        'label'       => esc_html__('Product Attribute Background','kuteshop'),
                                                        'type'        => 'list-item',
                                                        'section'     => 'option_product',
                                                        'std'         => '',
                                                        'settings'    => array( 
                                                            array(
                                                                'id'          => 'attr_slug',
                                                                'label'       => esc_html__('Term Slug Attribute','kuteshop'),
                                                                'type'        => 'text',
                                                            ),
                                                            array(
                                                                'id'          => 'attr_bg',
                                                                'label'       => esc_html__('Term Attribute Background','kuteshop'),
                                                                'type'        => 'colorpicker',
                                                            ),
                                                        ),
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_tab_detail',
                                                        'label'       => esc_html__('Product Tab Style','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_product',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> '',
                                                                'label'=> esc_html__("Normal", 'kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'tab-toggle',
                                                                'label'=> esc_html__("Tab Toggle", 'kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'detail-without-sidebar',
                                                                'label'=> esc_html__("Tab Vertical", 'kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'show_single_number',
                                                        'label'       => esc_html__('Show Single Products Number','kuteshop'),
                                                        'type'        => 'text',
                                                        'section'     => 'option_product',
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_item_style_single',
                                                        'label'       => esc_html__('Product Item Style','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_product',
                                                        'std'         => 'item-pro-color',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> 'item-pro-color',
                                                                'label'=> esc_html__('Default','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'default',
                                                                'label'=> esc_html__('Style 2','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'tab-large-item',
                                                                'label'=> esc_html__('Style 3','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'item-pro-ajax',
                                                                'label'=> esc_html__('Style 4','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_size_single_box',
                                                        'label'       => esc_html__('Product size thumbnail','kuteshop'),
                                                        'type'        => 'text',
                                                        'section'     => 'option_product',
                                                        'std'         => '',
                                                        'desc'        => esc_html__('Enter site thumbnail to crop. [width]x[height]. Example is 300x300.','kuteshop')
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_extra_link_single',
                                                        'label'       => esc_html__('Extra Link','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_product',
                                                        'std'         => 'hidden',
                                                        'choices'     => array(
                                                            array(
                                                                'value'=> 'hidden',
                                                                'label'=> esc_html__('Hidden','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'show',
                                                                'label'=> esc_html__('Show','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'product_extra_style_single',
                                                        'label'       => esc_html__('Extra Link Style','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_product',
                                                        'std'         => '',
                                                        'condition'   => 'product_extra_link_single:is(show)',
                                                        'choices'     => array(                                                    
                                                            array(
                                                                'value'=> '',
                                                                'label'=> esc_html__('Style 1','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'home6',
                                                                'label'=> esc_html__('Style 2','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'show_single_lastest',
                                                        'label'       => esc_html__('Show Single Lastest Products','kuteshop'),
                                                        'type'        => 'on-off',
                                                        'section'     => 'option_product',
                                                        'std'         => 'off'
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'show_single_upsell',
                                                        'label'       => esc_html__('Show Single Upsell Products','kuteshop'),
                                                        'type'        => 'on-off',
                                                        'section'     => 'option_product',
                                                        'std'         => 'on'
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'show_single_relate',
                                                        'label'       => esc_html__('Show Single Relate Products','kuteshop'),
                                                        'type'        => 'on-off',
                                                        'section'     => 'option_product',
                                                        'std'         => 'off'
                                                    ));            
            array_push($s7upf_config['theme-option']['sections'], array(
                                                                'id' => 'option_catelog',
                                                                'title' => '<i class="fa fa-shopping-cart"></i>'.esc_html__(' WooCommerce Catalog', 'kuteshop')
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'woo_catelog',
                                                                'label'       => esc_html__('Enable WooCommerce Catalog Mode','kuteshop'),
                                                                'type'        => 'on-off',
                                                                'section'     => 'option_catelog',
                                                                'std'         => 'off'
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'hide_detail',
                                                                'label'       => esc_html__('Hide "Add to cart" button in product detail page','kuteshop'),
                                                                'type'        => 'on-off',
                                                                'section'     => 'option_catelog',
                                                                'condition'   => 'woo_catelog:is(on)',
                                                                'std'         => 'off'
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'hide_other_page',
                                                                'label'       => esc_html__('Hide "Add to cart" button in other shop pages','kuteshop'),
                                                                'type'        => 'on-off',
                                                                'section'     => 'option_catelog',
                                                                'condition'   => 'woo_catelog:is(on)',
                                                                'std'         => 'off',
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'hide_admin',
                                                                'label'       => esc_html__('Enable Catalog Mode also for administrators','kuteshop'),
                                                                'type'        => 'on-off',
                                                                'section'     => 'option_catelog',
                                                                'condition'   => 'woo_catelog:is(on)',
                                                                'std'         => 'off',
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'hide_price',
                                                                'label'       => esc_html__('Hide Price','kuteshop'),
                                                                'type'        => 'on-off',
                                                                'section'     => 'option_catelog',
                                                                'condition'   => 'woo_catelog:is(on)',
                                                                'std'         => 'off',
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'hide_minicart',
                                                                'label'       => esc_html__('Hide Mini Cart','kuteshop'),
                                                                'type'        => 'on-off',
                                                                'section'     => 'option_catelog',
                                                                'condition'   => 'woo_catelog:is(on)',
                                                                'std'         => 'off',
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'hide_shop',
                                                                'label'       => esc_html__('Disable shop','kuteshop'),
                                                                'type'        => 'on-off',
                                                                'section'     => 'option_catelog',
                                                                'condition'   => 'woo_catelog:is(on)',
                                                                'std'         => 'off',
                                                                'desc'        => esc_html__('Hide and disable "Cart" page, "Checkout" page and all "Add to Cart" buttons','kuteshop')
                                                            ));
            array_push($s7upf_config['theme-option']['sections'], array(
                                                        'id' => 'option_coupon',
                                                        'title' => '<i class="fa fa-th-large"></i>'.esc_html__(' Coupon Settings', 'kuteshop')
                                                    ));
            // Coupon
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'enable_coupon',
                                                                'label'       => esc_html__('Enable','kuteshop'),
                                                                'type'        => 'on-off',
                                                                'section'     => 'option_coupon',
                                                                'std'         => 'off'
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'default_coupon',
                                                                'label'       => esc_html__('Default Coupon','kuteshop'),
                                                                'type'        => 'text',
                                                                'section'     => 'option_coupon',
                                                                'condition'   => 'enable_coupon:is(on)',
                                                                'desc'=>esc_html__('Enter coupon code created in Woocommerce --> Coupons.','kuteshop'),
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'check_get_coupon',
                                                        'label'       => esc_html__('Coupon apply to','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_coupon',
                                                        'std'         => 'fixed_cart',
                                                        'condition'   => 'enable_coupon:is(on)',
                                                        'choices'     => array(
                                                            array(
                                                                'value'=> '',
                                                                'label'=> esc_html__('New User','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'user',
                                                                'label'=> esc_html__('All User','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'all',
                                                                'label'=> esc_html__('All Visitor','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'new_in',
                                                                'label'       => esc_html__('User new in (days)','kuteshop'),
                                                                'type'        => 'text',
                                                                'section'     => 'option_coupon',
                                                                'condition'   => 'enable_coupon:is(on)',
                                                                'std'         => '30'
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'coupon_amount',
                                                                'label'       => esc_html__('Amount','kuteshop'),
                                                                'type'        => 'text',
                                                                'section'     => 'option_coupon',
                                                                'condition'   => 'enable_coupon:is(on)',
                                                                'std'         => '100'
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'coupon_type',
                                                        'label'       => esc_html__('Coupon type','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_coupon',
                                                        'std'         => 'fixed_cart',
                                                        'condition'   => 'enable_coupon:is(on)',
                                                        'choices'     => array(
                                                            array(
                                                                'value'=> 'fixed_cart',
                                                                'label'=> esc_html__('Cart Discount','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'percent',
                                                                'label'=> esc_html__('Cart % Discount','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'fixed_product',
                                                                'label'=> esc_html__('Product Discount','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'percent_product',
                                                                'label'=> esc_html__('Product % Discount','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'coupon_date',
                                                                'label'       => esc_html__('Coupon expiry date (YYYY-MM-DD)','kuteshop'),
                                                                'type'        => 'text',
                                                                'section'     => 'option_coupon',
                                                                'condition'   => 'enable_coupon:is(on)',
                                                                'std'         => ''
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'usage_limit',
                                                                'label'       => esc_html__('Usage limit per coupon','kuteshop'),
                                                                'type'        => 'text',
                                                                'section'     => 'option_coupon',
                                                                'condition'   => 'enable_coupon:is(on)',
                                                                'std'         => ''
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'usage_limit_per_user',
                                                                'label'       => esc_html__('Usage limit per user','kuteshop'),
                                                                'type'        => 'text',
                                                                'section'     => 'option_coupon',
                                                                'condition'   => 'enable_coupon:is(on)',
                                                                'std'         => ''
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'individual_use',
                                                        'label'       => esc_html__('Individual use only','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_coupon',
                                                        'std'         => 'no',
                                                        'condition'   => 'enable_coupon:is(on)',
                                                        'choices'     => array(
                                                            array(
                                                                'value'=> 'no',
                                                                'label'=> esc_html__('No','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'yes',
                                                                'label'=> esc_html__('Yes','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                        'id'          => 'exclude_sale_items',
                                                        'label'       => esc_html__('Exclude sale items','kuteshop'),
                                                        'type'        => 'select',
                                                        'section'     => 'option_coupon',
                                                        'std'         => 'no',
                                                        'condition'   => 'enable_coupon:is(on)',
                                                        'choices'     => array(
                                                            array(
                                                                'value'=> 'no',
                                                                'label'=> esc_html__('No','kuteshop'),
                                                            ),
                                                            array(
                                                                'value'=> 'yes',
                                                                'label'=> esc_html__('Yes','kuteshop'),
                                                            ),
                                                        )
                                                    ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'coupon_out_date',
                                                                'label'       => esc_html__('Coupon Out date (YYYY-MM-DD)','kuteshop'),
                                                                'type'        => 'text',
                                                                'section'     => 'option_coupon',
                                                                'condition'   => 'enable_coupon:is(on)',
                                                                'std'         => ''
                                                            ));
            array_push($s7upf_config['theme-option']['settings'],array(
                                                                'id'          => 'reset_curent_data',
                                                                'label'       => esc_html__('Reset Coupon Data','kuteshop'),
                                                                'type'        => 'on-off',
                                                                'section'     => 'option_coupon',
                                                                'std'         => 'off'
                                                            ));
        }
        $s7upf_config = apply_filters('s7upf_config_value',$s7upf_config);
    }
}
s7upf_set_theme_config();