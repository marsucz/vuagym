<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 29/02/16
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_blog'))
{
    function s7upf_vc_blog($attr)
    {
        $html = $class_nav = '';
        extract(shortcode_atts(array(
            'style'      => 'content',
            'number'     => '',
            'cats'      => '',
            'order'      => '',
            'order_by'   => '',
        ),$attr));
        $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
        $args=array(
            'post_type'         => 'post',
            'posts_per_page'    => $number,
            'orderby'           => $order_by,
            'order'             => $order,
            'paged'             => $paged,
        );
        if($order_by == 'post_views'){
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'post_views';
        }
        if($order_by == 'time_update'){
            $args['orderby'] = 'meta_value';
            $args['meta_key'] = 'time_update';
        }
        if($order_by == '_post_like_count'){
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_post_like_count';
        }
        if(!empty($cats)) {
            $custom_list = explode(",",$cats);
            $args['tax_query'][]=array(
                'taxonomy'=>'category',
                'field'=>'slug',
                'terms'=> $custom_list
            );
        }
        $query = new WP_Query($args);
        global $count;
        $count = 1;
        $count_query = $query->post_count;
        $max_page = $query->max_num_pages;
        $blog_style = s7upf_get_option('sv_style_blog');
        if(empty($blog_style)) $blog_style = 'content';
        $type = 'list';
        $big = 999999999;
        if(isset($_GET['type'])) $type = $_GET['type'];
        $html .=    '<div class="content-blog-page border radius blog-wrap-'.$style.' blog-style-'.$type.'">';
        ob_start();
        ?>
        <?php if($style != 'masonry'){?>
                        <div class="sort-pagi-bar clearfix">
                            <div class="view-type pull-left">
                                <a data-type="list" href="<?php echo esc_url(s7upf_get_key_url('type','list'))?>" class="list-view <?php if($type == 'list') echo 'active'?>"></a>
                                <a data-type="grid" href="<?php echo esc_url(s7upf_get_key_url('type','grid'))?>" class="grid-view <?php if($type == 'grid') echo 'active'?>"></a>
                            </div>                            
                            <div class="sort-pagi-bar clearfix">
                                <div class="sort-paginav pull-right">
                                    <div class="pagi-bar <?php echo esc_attr($style)?>">
                                        <?php echo paginate_links( array(
                                                'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                                                'format'       => '&page=%#%',
                                                'current'      => max( 1, $paged ),
                                                'total'        => $query->max_num_pages,
                                                'prev_text' => '<i class="fa fa-caret-left" aria-hidden="true"></i>',
                                                'next_text' => '<i class="fa fa-caret-right" aria-hidden="true"></i>',
                                                'end_size'     => 2,
                                                'mid_size'     => 1
                                            ) ); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
        <?php }?>
        <?php        
        echo            '<div class="content-blog clearfix content-blog-'.$style.'">';
        if($query->have_posts()) {
            while($query->have_posts()) {
                $query->the_post();
                if($type != 'grid') get_template_part('s7upf_templates/blog-content/'.$style);
                else get_template_part('s7upf_templates/blog-content/grid');
                $count++;
            }
        }
        $html .=    ob_get_clean();
        $html .=        '</div>';
        if($style != 'masonry'){
            $html .=        '<div class="pagi-bar bottom">';
            $html .=            paginate_links( array(
                                    'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                                    'format'       => '&page=%#%',
                                    'current'      => max( 1, $paged ),
                                    'total'        => $query->max_num_pages,
                                    'prev_text' => '<i class="fa fa-caret-left" aria-hidden="true"></i>',
                                    'next_text' => '<i class="fa fa-caret-right" aria-hidden="true"></i>',
                                    'end_size'     => 2,
                                    'mid_size'     => 1
                                ) );        
            $html .=        '</div>';
        }
        else{
            if($max_page > 1) $html .=    '<div class="btn-loadmore"><a href="#" class="masonry-ajax" data-cat="'.$cats.'" data-number="'.$number.'"  data-order="'.$order.'" data-orderby="'.$order_by.'" data-paged="1"  data-maxpage="'.$max_page.'"><i aria-hidden="true" class="fa fa-spinner"></i></a></div>';
        }
        $html .=    '</div>';
        wp_reset_postdata();
        return $html;
    }
}

stp_reg_shortcode('sv_blog','s7upf_vc_blog');

vc_map( array(
    "name"      => esc_html__("SV Blog", 'kuteshop'),
    "base"      => "sv_blog",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "textfield",
            "heading" => esc_html__("Number post",'kuteshop'),
            "param_name" => "number",
            'description'   => esc_html__( 'Number of post display in this element. Default is 10.', 'kuteshop' ),
        ),
        array(
            "type"          => "dropdown",
            "holder"        => "div",
            "heading"       => esc_html__("Style Post",'kuteshop'),
            "param_name"    => "style",
            "value"         => array(
                esc_html__("Default","kuteshop")           => 'content',
                esc_html__("Large Thumbnail","kuteshop")           => 'large',
                esc_html__("Small Thumbnail","kuteshop")         => 'small',
                esc_html__("Masonry","kuteshop")           => 'masonry',
                ),
            'description'   => esc_html__( 'Choose style to display.', 'kuteshop' ),
        ),     
        array(
            'holder'     => 'div',
            'heading'     => esc_html__( 'Categories', 'kuteshop' ),
            'type'        => 'checkbox',
            'param_name'  => 'cats',
            'value'       => s7upf_list_taxonomy('category',false)
        ),
        array(
            "type"          => "dropdown",
            "heading"       => esc_html__("Order",'kuteshop'),
            "param_name"    => "order",
            "value"         => array(
                esc_html__('Desc','kuteshop') => 'DESC',
                esc_html__('Asc','kuteshop')  => 'ASC',
                ),
            'edit_field_class'=>'vc_col-sm-6 vc_column'
        ),
        array(
            "type"          => "dropdown",
            "heading"       => esc_html__("Order By",'kuteshop'),
            "param_name"    => "order_by",
            "value"         => s7upf_get_order_list(),
            'edit_field_class'=>'vc_col-sm-6 vc_column'
        ),
    )
));
//Home 5
add_action( 'wp_ajax_load_more_post_masonry', 's7upf_load_more_post_masonry' );
add_action( 'wp_ajax_nopriv_load_more_post_masonry', 's7upf_load_more_post_masonry' );
if(!function_exists('s7upf_load_more_post_masonry')){
    function s7upf_load_more_post_masonry() {
        $number         = $_POST['number'];
        $order_by       = $_POST['orderby'];
        $order          = $_POST['order'];
        $cats           = $_POST['cats'];
        $paged          = $_POST['paged'];
        $html = '';
        $args   =   array(
            'post_type'         => 'post',
            'posts_per_page'    => $number,
            'orderby'           => $order_by,
            'order'             => $order,
            'paged'             => $paged + 1,
        );
        if($order_by == 'post_views'){
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'post_views';
        }
        if($order_by == 'time_update'){
            $args['orderby'] = 'meta_value';
            $args['meta_key'] = 'time_update';
        }
        if($order_by == '_post_like_count'){
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_post_like_count';
        }
        if(!empty($cats)) {
            $custom_list = explode(",",$cats);
            $args['tax_query']['relation'] = 'AND';
            $args['tax_query'][]=array(
                'taxonomy'  => 'category',
                'field'     => 'slug',
                'terms'     => $custom_list
            );
        }
                  
        $query = new WP_Query($args);
        global $count;
        $count = 1;
        $count_query = $query->post_count;
        if($query->have_posts()) {
            while($query->have_posts()) {
                $query->the_post();
                get_template_part( 's7upf_templates/blog-content/masonry' );
                $count++;
            }
        }
        echo balanceTags($html);
        wp_reset_postdata();
    }
}