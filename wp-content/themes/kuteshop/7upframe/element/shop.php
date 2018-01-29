<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('sv_vc_shop'))
{
    function sv_vc_shop($attr)
    {
        $html = '';
        extract(shortcode_atts(array(
            'title'      => '',
            'block_style'=> 'content-grid-boxed',
            'shop_style' => '',
            'style'      => 'grid',
            'number'     => '12',
            'column'     => '1',
            'cats'       => '',
            'orderby'    => 'menu_order title',
            'quickview'         => 'show',
            'quickview_pos'     => '',
            'quickview_style'   => '',
            'extra_link'        => 'hidden',
            'extra_style'       => '',
            'label'             => '',
            'size'              => '',
            'item_style'        => 'item-pro-color',
        ),$attr));
        if(!empty($cats)) $cats = str_replace(' ', '', $cats);
        $animation_class = $data = $style_2 = '';
        $type = $style;
        if(isset($_GET['orderby'])){
            $orderby = $_GET['orderby'];
        }
        if(isset($_GET['type'])){
            $type = $_GET['type'];
        }
        if(isset($_GET['number'])){
            $number = $_GET['number'];
        }
        $item_num = $column;
        $args = array(
            'post_type'         => 'product',
            'post_status'       => 'publish',
            'order'             => 'ASC',
            'posts_per_page'    => $number,
            'paged'             => 1,
            );
        $attr_taxquery = array();
        global $wpdb,$wp_query;
        $attribute_taxonomies = wc_get_attribute_taxonomies();
        if(!empty($attribute_taxonomies)){
            foreach($attribute_taxonomies as $attr){
                if(isset($_REQUEST['pa_'.$attr->attribute_name])){
                    $term = $_REQUEST['pa_'.$attr->attribute_name];
                    $term = explode(',', $term);
                    $attr_taxquery[] =  array(
                                            'taxonomy'      => 'pa_'.$attr->attribute_name,
                                            'terms'         => $term,
                                            'field'         => 'slug',
                                            'operator'      => 'IN'
                                        );
                }
            }
        }
        if(isset( $_GET['product_cat'])) $cats = $_GET['product_cat'];
        if(!empty($cats)) {
            $cats = explode(",",$cats);
            $attr_taxquery[]=array(
                'taxonomy'=>'product_cat',
                'field'=>'slug',
                'terms'=> $cats
            );
        }
        if (!empty($attr_taxquery)){
            $attr_taxquery['relation'] = 'AND';
            $args['tax_query'] = $attr_taxquery;
        }
        if( isset( $_GET['min_price']) && isset( $_GET['max_price']) ){
            $min = $_GET['min_price'];
            $max = $_GET['max_price'];
            $args['post__in'] = sv_filter_price($min,$max);
        }
        switch ($orderby) {
            case 'price' :
                $args['orderby']  = "meta_value_num ID";
                $args['order']    = 'ASC';
                $args['meta_key'] = '_price';
            break;

            case 'price-desc' :
                $args['orderby']  = "meta_value_num ID";
                $args['order']    = 'DESC';
                $args['meta_key'] = '_price';
            break;

            case 'popularity' :
                $args['meta_key'] = 'total_sales';
                add_filter( 'posts_clauses', array( WC()->query, 'order_by_popularity_post_clauses' ) );
            break;

            case 'rating' :
                $args['meta_key'] = '_wc_average_rating';
                $args['orderby'] = 'meta_value_num';
                $args['meta_query'] = WC()->query->get_meta_query();
                $args['tax_query'][] = WC()->query->get_tax_query();
            break;

            case 'date':
                $args['orderby'] = 'date';
                break;
            
            default:
                $args['orderby'] = 'menu_order title';
                break;
        }
        $grid_active = $list_active = '';
        if($type == 'grid') $grid_active = 'active'; 
        if($type == 'list') $list_active = 'active';
        $product_query = new WP_Query($args);
        $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;        
        ob_start();
        ?>
        <?php if(!empty($title)){
            echo '<h3 class="page-title">
                <span>'.esc_html($title).'</span>
            </h3>';
        }
        $thumb_data = array(
                'size'  => $size,
                'quickview'  => $quickview,
                'quickview_pos'  => $quickview_pos,
                'quickview_style'  => $quickview_style,
                'extra_link'  => $extra_link,
                'extra_style'  => $extra_style,
                'label'  => $label,
                );
        s7upf_shop_loop_before($product_query,$orderby,$item_style,$type,$paged,$number,$column,$thumb_data,$block_style,$shop_style);
        $count_product = 1;
        if(empty($size)) $size = array(300,300);
        else $size = explode('x', $size);
        if($product_query->have_posts()) {
            while($product_query->have_posts()) {
                $product_query->the_post();
                global $product;
                if($type == 'list'){  
                    echo    s7upf_product_item(
                                'item-product-list',
                                1,
                                $animation_class,
                                $data,
                                $style_2,
                                array(
                                    'quickview'     => array(
                                        'status'    => $quickview,
                                        'pos'       => $quickview_pos,
                                        'style'     => $quickview_style,
                                        ),
                                    'extra-link'    => array(
                                        'status'    => $extra_link,
                                        'style'     => $extra_style,
                                        )
                                    ),
                                $size,
                                '',
                                '',
                                $label
                            );
                }
                else{       
                    echo    s7upf_product_item(
                                $item_style,
                                $item_num,
                                $animation_class,
                                $data,
                                $style_2,
                                array(
                                    'quickview'     => array(
                                        'status'    => $quickview,
                                        'pos'       => $quickview_pos,
                                        'style'     => $quickview_style,
                                        ),
                                    'extra-link'    => array(
                                        'status'    => $extra_link,
                                        'style'     => $extra_style,
                                        )
                                    ),
                                $size,
                                '',
                                '',
                                $label
                            );
                }
            }
        }
        s7upf_shop_loop_after($product_query,$paged,$shop_style);
        $html .= ob_get_clean();
        wp_reset_postdata();
        return $html;
    }
}

stp_reg_shortcode('sv_shop','sv_vc_shop');
$check_add = '';
if(isset($_GET['return'])) $check_add = $_GET['return'];
if(empty($check_add)) add_action( 'vc_before_init_base','sv_admin_shop',10,100 );
if ( ! function_exists( 'sv_admin_shop' ) ) {
    function sv_admin_shop(){
        vc_map( array(
            "name"      => esc_html__("SV Shop", 'kuteshop'),
            "base"      => "sv_shop",
            "icon"      => "icon-st",
            "category"  => '7Up-theme',
            "params"    => array(
                array(
                    "type" => "textfield",
                    "holder"    => 'div',
                    "heading" => esc_html__("Title",'kuteshop'),
                    "param_name" => "title",
                    ),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Block Style",'kuteshop'),
                    "param_name" => "block_style",
                    "value"     => array(
                        esc_html__("Default",'kuteshop')   => 'content-grid-boxed',
                        esc_html__("Style 2",'kuteshop')   => 'content-grid-no-boxed',
                        ),
                    ),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Shop Style",'kuteshop'),
                    "param_name" => "shop_style",
                    "value"     => array(
                        esc_html__("Default",'kuteshop')   => '',
                        esc_html__("Load More Button",'kuteshop')   => 'load-more',
                        ),
                    ),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Default Display",'kuteshop'),
                    "param_name" => "style",
                    "value"     => array(
                        esc_html__("Grid",'kuteshop')   => 'grid',
                        esc_html__("List",'kuteshop')   => 'list',
                        ),
                    ),
                array(
                    'heading'     => esc_html__( 'Number', 'kuteshop' ),
                    'type'        => 'textfield',
                    'description' => esc_html__( 'Enter number of product. Default is 12.', 'kuteshop' ),
                    'param_name'  => 'number',
                    ),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Order By",'kuteshop'),
                    "param_name" => "orderby",
                    "value"     => array(
                        esc_html__("Default sorting",'kuteshop')   => 'menu_order title',
                        esc_html__("Sort by popularity",'kuteshop')   => 'popularity',
                        esc_html__("Sort by average rating",'kuteshop')   => 'rating',
                        esc_html__("Sort by newness",'kuteshop')   => 'date',
                        esc_html__("Sort by price: low to high",'kuteshop')   => 'price',
                        esc_html__("Sort by price: high to low",'kuteshop')   => 'price-desc',
                        ),
                    ),
                array(
                    "type"          => "textfield",
                    "heading"       => esc_html__("Size Thumbnail",'kuteshop'),
                    "param_name"    => "size",
                    "group"         => esc_html__("Thumb Settings",'kuteshop'),
                    'description'   => esc_html__( 'Enter site thumbnail to crop. [width]x[height]. Example is 300x300', 'kuteshop' ),
                ),
                array(
                    'heading'     => esc_html__( 'Product item style', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    'param_name'  => 'item_style',
                    'value'       => array(
                                        esc_html__('Default','kuteshop')        => 'item-pro-color',
                                        esc_html__('Style 2','kuteshop')        => 'default',
                                        esc_html__('Style 3','kuteshop')        => 'tab-large-item',
                                        esc_html__('Style 4','kuteshop')        => 'item-pro-ajax',
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Label', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'label',
                    'value'       => array(
                                        esc_html__('Hidden','kuteshop')        => '',
                                        esc_html__('Show','kuteshop')          => 'show',
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Quickview', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'quickview',
                    'value'       => array(
                                        esc_html__('Show','kuteshop')          => 'show',
                                        esc_html__('Hidden','kuteshop')        => 'hidden',
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Quickview Style', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'quickview_style',
                    'value'       => array(
                                        esc_html__('Border bottom','kuteshop')          => '',
                                        esc_html__('Plus icon','kuteshop')              => 'plus',
                                    ),
                    'dependency'    => array(
                        'element'   => 'quickview',
                        'value'     => 'show',
                        )
                ),
                array(
                    'heading'     => esc_html__( 'Quickview Position', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'quickview_pos',
                    'value'       => array(
                                        esc_html__('Top','kuteshop')          => '',
                                        esc_html__('Middle','kuteshop')       => 'pos-middle',
                                        esc_html__('Bottom','kuteshop')       => 'pos-bottom',
                                    ),
                    'dependency'    => array(
                        'element'   => 'quickview',
                        'value'     => 'show',
                        )
                ),
                array(
                    'heading'     => esc_html__( 'Extra link', 'kuteshop' ),
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'extra_link',
                    'value'       => array(
                                        esc_html__('Hidden','kuteshop')        => 'hidden',
                                        esc_html__('Show','kuteshop')          => 'show',
                                    )
                ),
                array(
                    'heading'     => esc_html__( 'Extra Style', 'kuteshop' ),
                    'holder'      => 'div',
                    'type'        => 'dropdown',
                    'description' => esc_html__( 'Choose style to display.', 'kuteshop' ),
                    "group"       => esc_html__("Thumb Settings",'kuteshop'),
                    'param_name'  => 'extra_style',
                    'value'       => array(
                                        esc_html__('Style 1','kuteshop')          => '',
                                        esc_html__('Style 2','kuteshop')          => 'home6',
                                    ),
                    'dependency'    => array(
                        'element'   => 'extra_link',
                        'value'     => 'show',
                        )
                ),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Column",'kuteshop'),
                    "param_name" => "column",
                    "value"         => array(
                        esc_html__("1 Column","kuteshop")          => '1',
                        esc_html__("2 Column","kuteshop")          => '2',
                        esc_html__("3 Column","kuteshop")          => '3',
                        esc_html__("4 Column","kuteshop")          => '4',
                        esc_html__("5 Column","kuteshop")          => '5',
                        esc_html__("6 Column","kuteshop")          => '6',
                        esc_html__("7 Column","kuteshop")          => '7',
                        esc_html__("8 Column","kuteshop")          => '8',
                        esc_html__("9 Column","kuteshop")          => '9',
                        esc_html__("10 Column","kuteshop")         => '10',
                        ),
                    ),            
                array(
                    'holder'     => 'div',
                    'heading'     => esc_html__( 'Product Categories', 'kuteshop' ),
                    'type'        => 'autocomplete',
                    'param_name'  => 'cats',
                    'settings' => array(
                        'multiple' => true,
                        'sortable' => true,
                        'values' => s7upf_get_product_taxonomy(),
                    ),
                    'save_always' => true,
                    'description' => esc_html__( 'List of product categories', 'kuteshop' ),
                ),
            ),
        ));
    }
}