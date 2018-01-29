<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 05/09/15
 * Time: 10:00 AM
 */
if(class_exists("woocommerce")){
    if(!function_exists('s7upf_vc_product_tab'))
    {
        function s7upf_vc_product_tab($attr, $content = false)
        {
            $html = $el_class = $html_wl = $html_cp = '';
            extract(shortcode_atts(array(
                'style'             => 'ls-ls',
                'title'             => '',
                'image'             => '',
                'color'             => '',
                'tabs'              => '',
                'number'            => '',
                'cats'              => '',
                'brands'            => '',
                'order_by'          => 'date',
                'order'             => 'DESC',
            ),$attr));
            $args=array(
                'post_type'         => 'product',
                'posts_per_page'    => $number,
                'orderby'           => $order_by,
                'order'             => $order
            );
            if(!empty($cats)) {
                $custom_list = explode(",",$cats);
                $args['tax_query'][]=array(
                    'taxonomy'=>'product_cat',
                    'field'=>'slug',
                    'terms'=> $custom_list
                );
            }
            if(!empty($brands)) {
                $custom_brands = explode(",",$brands);
                $args['tax_query']['relation'] =  'AND';
                $args['tax_query'][]=array(
                    'taxonomy'=>'product_brand',
                    'field'=>'slug',
                    'terms'=> $custom_brands
                );
            }
            $pre = rand(1,100);
            if(!empty($tabs)){
                $tabs = explode(',', $tabs);
                $tab_html = $content_html = '';
                foreach ($tabs as $key => $tab) {
                    switch ($tab) {
                        case 'bestsell':
                            $tab_title =    esc_html__("Popular","kuteshop");
                            $args['meta_key'] = 'total_sales';
                            $args['orderby'] = 'meta_value_num';
                            break;

                        case 'toprate':
                            $tab_title =    esc_html__("Most review","kuteshop");
                            unset($args['meta_key']);
                            $args['meta_key'] = '_wc_average_rating';
                            $args['orderby'] = 'meta_value_num';
                            $args['meta_query'] = WC()->query->get_meta_query();
                            $args['tax_query'][] = WC()->query->get_tax_query();
                            break;
                        
                        case 'mostview':
                            $tab_title =    esc_html__("Most View","kuteshop");
                            unset($args['no_found_rows']);
                            unset($args['meta_query']);
                            unset($args['tax_query']);
                            if(!empty($cats)) {
                                $custom_list = explode(",",$cats);
                                $args['tax_query'][]=array(
                                    'taxonomy'=>'product_cat',
                                    'field'=>'slug',
                                    'terms'=> $custom_list
                                );
                            }
                            $args['meta_key'] = 'post_views';
                            $args['orderby'] = 'meta_value_num';
                            break;

                        case 'featured':
                            $tab_title =    esc_html__("Featured","kuteshop");
                            $args['orderby'] = $order_by;
                            $args['tax_query'][] = array(
                                'taxonomy' => 'product_visibility',
                                'field'    => 'name',
                                'terms'    => 'featured',
                                'operator' => 'IN',
                            );
                            break;

                        case 'trendding':
                            unset($args['meta_key']);
                            unset($args['meta_value']);
                            $tab_title =    esc_html__("Tredding","kuteshop");
                            $args['meta_query'][] = array(
                                'key'     => 'trending_product',
                                'value'   => 'on',
                                'compare' => '=',
                            );
                            break;
                        
                        case 'onsale':
                            $tab_title =    esc_html__("On sale","kuteshop");
                            unset($args['meta_query']);
                            unset($args['meta_key']);
                            unset($args['meta_value']);
                            $args['meta_query']['relation']= 'OR';
                            $args['meta_query'][]=array(
                                'key'   => '_sale_price',
                                'value' => 0,
                                'compare' => '>',                
                                'type'          => 'numeric'
                            );
                            $args['meta_query'][]=array(
                                'key'   => '_min_variation_sale_price',
                                'value' => 0,
                                'compare' => '>',                
                                'type'          => 'numeric'
                            );
                            break;
                        
                        default:
                            $tab_title =    esc_html__("New arrivals","kuteshop");
                            $args['orderby'] = 'date';
                            break;
                    }
                    if($key == 0) $f_class = 'active';
                    else $f_class = '';
                    $product_query = new WP_Query($args);
                    $count = 1;
                    $count_query = $product_query->post_count;
                    $tab_html .=    '<li class="'.$f_class.'"><a href="'.esc_url('#'.$pre.$tab).'" data-toggle="tab">'.$tab_title.'</a></li>';
                    $content_html .=    '<div id="'.$pre.$tab.'" class="tab-pane clearfix '.$f_class.'">';
                    if($product_query->have_posts()) {
                        while($product_query->have_posts()) {
                            $product_query->the_post();
                            global $product;
                            $large_item =   '<div class="large-box">
                                                '.
                                                s7upf_product_item(
                                                    'tab-large-item',
                                                    '',
                                                    '',
                                                    '',
                                                    '',
                                                    array(
                                                        'quickview'     => array(
                                                            'status'    => 'show',
                                                            'pos'       => 'pos-bottom',
                                                            'style'     => '',
                                                            ),
                                                        'extra-link'    => array(
                                                            'status'    => 'hidden',
                                                            'style'     => '',
                                                            )
                                                        ),
                                                    array(380,380),
                                                    '',
                                                    '',
                                                    'show'
                                                )
                                                .'
                                            </div>';
                            $small_item =   s7upf_product_item(
                                                '',
                                                '',
                                                '',
                                                '',
                                                '',
                                                array(
                                                    'quickview'     => array(
                                                        'status'    => 'show',
                                                        'pos'       => 'pos-top',
                                                        'style'     => 'plus',
                                                        ),
                                                    'extra-link'    => array(
                                                        'status'    => 'show',
                                                        'style'     => '',
                                                        )
                                                    ),
                                                array(300,300),
                                                '',
                                                '',
                                                'hidden'
                                            );
                            switch ($style) {
                                case 'sl-sl':
                                    if($count % 3 == 0) $content_html .= $large_item;
                                    else{
                                        if($count % 3 == 1) $content_html .= '<div class="small-box">';
                                        $content_html .= $small_item;
                                        if($count % 3 == 2 || $count == $count_query) $content_html .= '</div>';
                                    }
                                    break;

                                case 'sl-ls':
                                    if($count % 6 == 3 || $count % 6 == 4) $content_html .= $large_item;
                                    else{
                                       if($count % 6 == 1 || $count % 6 == 5) $content_html .= '<div class="small-box">';
                                        $content_html .= $small_item;
                                        if($count % 6 == 2 || $count % 6 == 0 || $count == $count_query) $content_html .= '</div>'; 
                                    }
                                    break;

                                case 'ls-sl':
                                    if($count % 6 == 1 || $count % 6 == 0) $content_html .= $large_item;
                                    else{
                                       if($count % 6 == 2 || $count % 6 == 4) $content_html .= '<div class="small-box">';
                                        $content_html .= $small_item;
                                        if($count % 6 == 3 || $count % 6 == 5 || $count == $count_query) $content_html .= '</div>'; 
                                    }
                                    break;
                                
                                default:                                    
                                    if($count % 3 == 1) $content_html .= $large_item;
                                    else{
                                        if($count % 3 == 2) $content_html .= '<div class="small-box">';
                                        $content_html .= $small_item;
                                        if($count % 3 == 0 || $count == $count_query) $content_html .= '</div>';
                                    }
                                    break;
                            }
                            $count++;
                        }
                    }
                    $content_html .=    '</div>';
                }
                
                $html .=    '<div class="product-box2 '.esc_attr($color).'">
                                <div class="title-box1 style2">';
                $html .=        '<h2 class="title30"><span>'.wp_get_attachment_image($image,'full').'</span>'.esc_html($title).'</h2>
                                    <ul class="list-none">
                                        '.$tab_html.'
                                    </ul>
                                </div>
                                <div class="content-box2 '.esc_attr($style).' tab-content border radius">
                                    '.$content_html.'
                                </div>
                            </div>';
            }
            wp_reset_postdata();
            return $html;
        }
    }

    stp_reg_shortcode('sv_product_tab','s7upf_vc_product_tab');
    add_action( 'vc_before_init_base','s7upf_add_product_tab',10,100 );
    if ( ! function_exists( 's7upf_add_product_tab' ) ) {
        function s7upf_add_product_tab(){
            vc_map( array(
                "name"      => esc_html__("SV Product Tab", 'kuteshop'),
                "base"      => "sv_product_tab",
                "icon"      => "icon-st",
                "category"  => '7Up-theme',
                "params"    => array(
                    array(
                        "type" => "dropdown",
                        "heading" => esc_html__("Style",'kuteshop'),
                        "param_name" => "style",
                        "value"     => array(
                            esc_html__("Large-small-large-small",'kuteshop')   => 'ls-ls',
                            esc_html__("Large-small-small-large",'kuteshop')   => 'ls-sl',
                            esc_html__("Small-large-large-small",'kuteshop')   => 'sl-ls',
                            esc_html__("Small-large-small-large",'kuteshop')   => 'sl-sl',
                            )
                    ),
                    array(
                        "type" => "dropdown",
                        "heading" => esc_html__("Color",'kuteshop'),
                        "param_name" => "color",
                        "value"     => array(
                            esc_html__("Default",'kuteshop')    => '',
                            esc_html__("Blue",'kuteshop')       => 'color-blue',
                            esc_html__("Red",'kuteshop')        => 'color-red',
                            esc_html__("Orange",'kuteshop')     => 'color-orange',
                            esc_html__("Green",'kuteshop')      => 'color-green',
                            esc_html__("Maroon",'kuteshop')     => 'color-maroon',
                            )
                    ),
                    array(
                        'heading'     => esc_html__( 'Number', 'kuteshop' ),
                        'type'        => 'textfield',
                        'description' => esc_html__( 'Enter number of product. Default is 10.', 'kuteshop' ),
                        'param_name'  => 'number',
                    ),
                    array(
                        'heading'     => esc_html__( 'Title', 'kuteshop' ),
                        'type'        => 'textfield',
                        'param_name'  => 'title',
                    ),
                    array(
                        'heading'     => esc_html__( 'Title Image', 'kuteshop' ),
                        'type'        => 'attach_image',
                        'param_name'  => 'image',
                    ),
                    array(
                        'holder'     => 'div',
                        'heading'     => esc_html__( 'Product Categories', 'kuteshop' ),
                        'type'        => 'checkbox',
                        'param_name'  => 'cats',
                        'value'       => s7upf_list_taxonomy('product_cat',false)
                    ),                    
                    array(
                        'holder'     => 'div',
                        'heading'     => esc_html__( 'Product Brands', 'kuteshop' ),
                        'type'        => 'checkbox',
                        'param_name'  => 'brands',
                        'value'       => s7upf_list_taxonomy('product_brand',false)
                    ),
                    array(
                        'type' => 'dropdown',
                        'heading' => esc_html__( 'Order By', 'kuteshop' ),
                        'value' => s7upf_get_order_list(),
                        'param_name' => 'orderby',
                        'description' => esc_html__( 'Select Orderby Type ', 'kuteshop' ),
                        'edit_field_class'=>'vc_col-sm-6 vc_column',
                    ),
                    array(
                        'heading'     => esc_html__( 'Order', 'kuteshop' ),
                        'type'        => 'dropdown',
                        'param_name'  => 'order',
                        'value' => array(                   
                            esc_html__('Desc','kuteshop')  => 'DESC',
                            esc_html__('Asc','kuteshop')  => 'ASC',
                        ),
                        'description' => esc_html__( 'Select Order Type ', 'kuteshop' ),
                        'edit_field_class'=>'vc_col-sm-6 vc_column',
                    ),
                    array(
                        "type" => "checkbox",
                        "heading" => esc_html__("Tabs",'kuteshop'),
                        "param_name" => "tabs",
                        "value" => array(
                            esc_html__("New Arrivals",'kuteshop')    => 'newarrival',
                            esc_html__("Best Seller",'kuteshop')     => 'bestsell',
                            esc_html__("Most Review",'kuteshop')     => 'toprate',
                            esc_html__("Most View",'kuteshop')       => 'mostview',
                            esc_html__("Featured",'kuteshop')        => 'featured',
                            esc_html__("Trendding",'kuteshop')       => 'trendding',
                            esc_html__("On Sale",'kuteshop')         => 'onsale',
                            ),
                    ),
                )
            ));
        }
    }
}
