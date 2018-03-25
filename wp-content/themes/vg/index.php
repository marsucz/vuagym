<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package 7up-framework
 */

get_header();
?>
<div id="main-content" class="main-wrapper">
    <?php do_action('s7upf_before_main_content')?>
    <div class="blog-default page-default"><!-- blog-page -->
        <?php s7upf_header_image();?>
        <div class="container">
            <?php s7upf_display_breadcrumb();?>
            <div class="row">
                <?php s7upf_output_sidebar('left')?>
                <?php
                    $blog_style = s7upf_get_option('sv_style_blog');
                    if(empty($blog_style)) $blog_style = 'content';
                    $type = 'list';
                    if(isset($_GET['type'])) $type = $_GET['type'];
                    // global $wp_query;
                    // var_dump($wp_query);
                ?>
                <div class="main-content <?php echo esc_attr(s7upf_get_main_class()); ?>">
                	<div class="content-blog-page border radius blog-wrap-<?php echo esc_attr($blog_style)?> blog-style-<?php echo esc_attr($type)?>">

                        <div class="sort-pagi-bar clearfix">
                            <div class="view-type pull-left">
                                <a data-type="list" href="<?php echo esc_url(s7upf_get_key_url('type','list'))?>" class="list-view <?php if($type == 'list') echo 'active'?>"></a>
                                <a data-type="grid" href="<?php echo esc_url(s7upf_get_key_url('type','grid'))?>" class="grid-view <?php if($type == 'grid') echo 'active'?>"></a>
                            </div>                            
                            <?php s7upf_paging_nav();?>
                        </div>

                        <?php if(have_posts()):?>
                            <div class="content-blog content-blog-<?php echo esc_attr($blog_style)?> clearfix">

        					    <?php while (have_posts()) :the_post();
                                    if($type != 'grid') get_template_part('s7upf_templates/blog-content/'.$blog_style);
                                    else get_template_part('s7upf_templates/blog-content/grid');
                                endwhile;?>

                            </div>
                            <?php s7upf_paging_nav('bottom');?>
                            <?php wp_reset_postdata();?>
    					<?php else : ?>
    					    <div class="text-center">
                                <?php esc_html_e('No post to display. Please create post !','kuteshop');?>
                            </div>
    					<?php endif;?>
                        
                    </div>
                </div>
                <?php s7upf_output_sidebar('right')?>
            </div>
        </div>
    </div>
    <?php do_action('s7upf_affter_main_content')?>
</div>
<?php get_footer(); ?>
