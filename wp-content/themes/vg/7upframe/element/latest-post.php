<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 15/12/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_lastest_post'))
{
    function s7upf_vc_lastest_post($attr)
    {
        $html = '';
        extract(shortcode_atts(array(
            'style'     => '',
            'cats'      => '',
            'title'     => '',
            'des'     => '',
            'number'    => '7',
            'order'     => 'DESC',
            'link'      => '',
        ),$attr));
        $args = array(
            'post_type'         => 'post',
            'posts_per_page'    => $number,
            'orderby'           => 'date',
            'order'             => $order,
        );
        if(!empty($cats)) {
            $custom_list = explode(",",$cats);
            $args['tax_query'][]=array(
                'taxonomy'=>'category',
                'field'=>'slug',
                'terms'=> $custom_list
            );
        }
        $query = new WP_Query($args);
        $count = 1;
        $count_query = $query->post_count;
        switch ($style) {
            case 'home18':
                $item = 2;$speed = '';$item_res = '0:1,568:2';
                $html .=    '<div class="latest-wrap18">';
                if(!empty($title)) $html .= '<h2 class="title30 white text-center">'.esc_html($title).'</h2>';
                $html .=        '<div class="latestnews-slider18 poly-slider">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($query->have_posts()) {
                    while($query->have_posts()) {
                        $query->the_post();
                        $html .=        '<div class="item-news18">
                                            <div class="row">
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                    <div class="post-thumb">
                                                        <a href="'.esc_url(get_the_permalink()).'" class="post-thumb-link">'.get_the_post_thumbnail(get_the_ID(),array(342,342)).'</a>
                                                        <a href="'.get_the_post_thumbnail_url(get_the_ID(),'full').'" class="post-zoom-link"></a>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-12 col-xs-12">
                                                    <div class="post-info white">
                                                        <h2 class="title18"><a href="'.esc_url(get_the_permalink()).'">'.get_the_title().'</a></h2>
                                                        <p>'.s7upf_substr(get_the_excerpt(),0,60).'</p>
                                                        <ul class="list-none post-date-comment">
                                                            <li><i class="fa fa-comment" aria-hidden="true"></i><a href="'.esc_url(get_comments_link()).'">'.get_comments_number().'</a></li>
                                                            <li>'.get_avatar(get_the_author_meta('email'),29).'<a href="'.esc_url(get_author_posts_url(get_the_author_meta('ID'))).'">'.get_the_author().'</a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'home17':
                $item = 3;$speed = $item_res = '';
                $html .=    '<div class="latest-news16 latest-news17 inner-latest-news6">';
                if(!empty($title)) $html .= '<h2 class="title18 bg-color">'.esc_html($title).'</h2>';
                $html .=        '<div class="latest-slider6 owl-slider17">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($query->have_posts()) {
                    while($query->have_posts()) {
                        $query->the_post();
                        $html .=        '<div class="item-latest6">
                                            <div class="post-thumb">
                                                <a href="'.esc_url(get_the_permalink()).'" class="post-thumb-link">'.get_the_post_thumbnail(get_the_ID(),array(342,342)).'</a>
                                                <a href="'.get_the_post_thumbnail_url(get_the_ID(),'full').'" class="post-zoom-link"></a>
                                            </div>
                                            <div class="post-info">
                                                <ul class="post-date-comment">
                                                    <li><i class="fa fa-calendar-check-o" aria-hidden="true"></i><span>'.get_the_date('F d Y').'</span></li>
                                                    <li><i class="fa fa-comment" aria-hidden="true"></i><a href="'.esc_url(get_comments_link()).'">'.get_comments_number().'</a></li>
                                                </ul>
                                                <h3 class="post-title"><a href="'.esc_url(get_the_permalink()).'">'.get_the_title().'</a></h3>
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'home16':
                $item = 3;$speed = $item_res = '';
                $html .=    '<div class="latest-news16 inner-latest-news6">';
                if(!empty($title)) $html .= '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=        '<div class="latest-slider6">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($query->have_posts()) {
                    while($query->have_posts()) {
                        $query->the_post();
                        $html .=        '<div class="item-latest6">
                                            <div class="post-thumb">
                                                <a href="'.esc_url(get_the_permalink()).'" class="post-thumb-link">'.get_the_post_thumbnail(get_the_ID(),array(342,342)).'</a>
                                                <a href="'.get_the_post_thumbnail_url(get_the_ID(),'full').'" class="post-zoom-link"></a>
                                            </div>
                                            <div class="post-info">
                                                <ul class="post-date-comment">
                                                    <li><i class="fa fa-calendar-check-o" aria-hidden="true"></i><span>'.get_the_date('F d Y').'</span></li>
                                                    <li><i class="fa fa-comment" aria-hidden="true"></i><a href="'.esc_url(get_comments_link()).'">'.get_comments_number().'</a></li>
                                                </ul>
                                                <h3 class="post-title"><a href="'.esc_url(get_the_permalink()).'">'.get_the_title().'</a></h3>
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;

            case 'home15':
                $item = 3;
                $speed = '';
                $item_res = '0:1,560:2,980:3';
                $html .=    '<div class="from-blog15-wrap">
                                <div class="container">';
                if(!empty($title)) $html .= '<div class="title-blog15 text-center white">
                                                <h2 class="title30">'.esc_html($title).'</h2>
                                                <p>'.esc_html($des).'</p>
                                            </div>';
                $html .=            '<div class="blog-slider15">
                                        <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($query->have_posts()) {
                    while($query->have_posts()) {
                        $query->the_post();
                        if($count % 2 == 1) $html .=            '<div class="item-blog-slider15">';
                        $html .=                '<div class="item-blog15">
                                                    <div class="post-thumb">
                                                        <a href="'.esc_url(get_the_permalink()).'" class="post-thumb-link">'.get_the_post_thumbnail(get_the_ID(),array(380,268)).'</a>
                                                        <h3 class="title18"><a href="'.esc_url(get_the_permalink()).'">'.get_the_title().'</a></h3>
                                                    </div>
                                                    <div class="post-info">
                                                        <div class="clearfix">
                                                            <a href="'.esc_url(get_author_posts_url(get_the_author_meta('ID'))).'" class="admin-link"><i class="fa fa-user" aria-hidden="true"></i> '.get_the_author().'</a>
                                                            <ul class="list-none post-date-comment">
                                                                <li><i class="fa fa-comment" aria-hidden="true"></i> '.get_comments_number().'</li>
                                                                <li><i class="fa fa-calendar-check-o" aria-hidden="true"></i> '.get_the_date('d.M').'</li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div>';
                        if($count % 2 == 0 || $count == $count_query) $html .=            '</div>';
                        $count++;
                    }
                }
                $html .=                '</div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'home6':
                $item = 3;$speed = $item_res = '';
                $html .=    '<div class="latest-news6">
                                <div class="container">
                                    <div class="inner-latest-news6">';
                if(!empty($title)) $html .= '<h2 class="title18">'.esc_html($title).'</h2>';
                $html .=                '<div class="latest-slider6">
                                            <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($query->have_posts()) {
                    while($query->have_posts()) {
                        $query->the_post();
                        $html .=                '<div class="item-latest6">
                                                    <div class="post-thumb">
                                                        <a href="'.esc_url(get_the_permalink()).'" class="post-thumb-link">'.get_the_post_thumbnail(get_the_ID(),array(342,342)).'</a>
                                                        <a href="'.get_the_post_thumbnail_url(get_the_ID(),'full').'" class="post-zoom-link"></a>
                                                    </div>
                                                    <div class="post-info">
                                                        <ul class="post-date-comment">
                                                            <li><i class="fa fa-calendar-check-o" aria-hidden="true"></i><span>'.get_the_date('F d Y').'</span></li>
                                                            <li><i class="fa fa-comment" aria-hidden="true"></i><a href="'.esc_url(get_comments_link()).'">'.get_comments_number().'</a></li>
                                                        </ul>
                                                        <h3 class="post-title"><a href="'.esc_url(get_the_permalink()).'">'.get_the_title().'</a></h3>
                                                    </div>
                                                </div>';
                    }
                }
                $html .=                    '</div>
                                        </div>
                                    </div>
                                </div>
                            </div>';
                break;

            case 'home3':
                $item = 4;$speed = $item_res = '';
                $html .=    '<div class="from-blog3 '.esc_attr($style).'">';
                if(!empty($title)) $html .= '<h2 class="title14 title-box3">'.esc_html($title).'</h2>';
                $html .=        '<div class="blog-slider3 arrow-style3">
                                    <div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="'.esc_attr($speed).'" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';
                if($query->have_posts()) {
                    while($query->have_posts()) {
                        $query->the_post();
                        $html .=        '<div class="item-blog3">
                                            <div class="post-thumb">
                                                <a href="'.esc_url(get_the_permalink()).'" class="post-thumb-link">'.get_the_post_thumbnail(get_the_ID(),array(278,278)).'</a>
                                                <a href="'.get_the_post_thumbnail_url(get_the_ID(),'full').'" class="post-zoom-link"></a>
                                            </div>
                                            <div class="post-info">
                                                <ul class="list-none post-date-comment">
                                                    <li><i class="fa fa-comment" aria-hidden="true"></i><a href="'.esc_url(get_comments_link()).'">'.get_comments_number().'</a></li>
                                                    <li>'.get_avatar(get_the_author_meta('email'),29).'<a href="'.esc_url(get_author_posts_url(get_the_author_meta('ID'))).'">'.get_the_author().'</a></li>
                                                </ul>
                                                <h3 class="title14"><a href="'.esc_url(get_the_permalink()).'">'.s7upf_substr(get_the_excerpt(),0,60).'</a></h3>
                                                <p class="post-date">'.esc_html__("Date Posted:","kuteshop").' '.human_time_diff( get_post_time('U'), current_time('timestamp') ).' '.esc_html__("ago","kuteshop").'</p>
                                            </div>
                                        </div>';
                    }
                }
                $html .=            '</div>
                                </div>
                            </div>';
                break;
            
            default:
                $html .=    '<div class="latest-news '.esc_attr($style).'">
                                <div class="list-latest-news">';
                if($query->have_posts()) {
                    while($query->have_posts()) {
                        $query->the_post();
                        $html .=    '<div class="item-latest-news">
                                        <div class="row">
                                            <div class="col-md-7 col-sm-7 col-xs-12">
                                                <div class="post-thumb zoom-image">
                                                    <a href="'.esc_url(get_the_permalink()).'">'.get_the_post_thumbnail(get_the_ID(),array(570,411)).'</a>
                                                </div>
                                            </div>
                                            <div class="col-md-5 col-sm-5 col-xs-12">
                                                <div class="post-info text-left">
                                                    <ul class="post-comment-date">
                                                        <li><i class="fa fa-calendar" aria-hidden="true"></i><span>'.get_the_date('M d, Y').'</span></li>
                                                        <li><a href="'.esc_url(get_comments_link()).'"><i class="fa fa-comment-o" aria-hidden="true"></i><span>'.get_comments_number().' '.esc_html__("Comment","kuteshop").'</span></a></li>
                                                    </ul>
                                                    <h3 class="post-title"><a href="'.esc_url(get_the_permalink()).'">'.get_the_title().'</a></h3>
                                                    <a href="'.esc_url(get_the_permalink()).'" class="btn-plus readmore">'.esc_html__("read more","kuteshop").'</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>';
                    }
                }
                $html .=        '</div>
                            </div>';
                break;
        }
       
        wp_reset_postdata();
        return $html;
    }
}

stp_reg_shortcode('sv_lastest_post','s7upf_vc_lastest_post');

vc_map( array(
    "name"      => esc_html__("SV Latest Post", 'kuteshop'),
    "base"      => "sv_lastest_post",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "dropdown",
            "holder" => "div",
            "heading" => esc_html__("Style",'kuteshop'),
            "param_name" => "style",
            "value"     => array(
                esc_html__("Default",'kuteshop')   => '',
                esc_html__("Home 3",'kuteshop')   => 'home3',
                esc_html__("Home 6",'kuteshop')   => 'home6',
                esc_html__("Home 15",'kuteshop')   => 'home15',
                esc_html__("Home 16",'kuteshop')   => 'home16',
                esc_html__("Home 17",'kuteshop')   => 'home17',
                esc_html__("Home 18",'kuteshop')   => 'home18',
                )
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__( 'Number post', 'kuteshop' ),
            'param_name'  => 'number',
            'description' => esc_html__( 'Number posts are display. Default is 7.', 'kuteshop' ),
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__( 'Title', 'kuteshop' ),
            'param_name'  => 'title',
            "dependency"    => array(
                "element"   => 'style',
                "value"   => array('home3','home6','home15','home16','home17','home18'),
                )
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__( 'Description', 'kuteshop' ),
            'param_name'  => 'des',
            "dependency"    => array(
                "element"   => 'style',
                "value"   => array('home15'),
                )
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__( 'View link', 'kuteshop' ),
            'param_name'  => 'link',
            "dependency"    => array(
                "element"   => 'style',
                "value"   => array('home7'),
                )
        ),
        array(
            'holder'     => 'div',
            'heading'     => esc_html__( 'Categories', 'kuteshop' ),
            'type'        => 'checkbox',
            'param_name'  => 'cats',
            'value'       => s7upf_list_taxonomy('category',false)
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
        ),
    )
));