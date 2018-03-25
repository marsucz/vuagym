<?php
/**
 * The template for displaying all single posts.
 *
 * @package 7up-framework
 */

get_header();
?>
    <div id="main-content" class="page-default">
        <?php s7upf_header_image();?>
        <div class="container">
            <?php s7upf_display_breadcrumb();?>
            <div class="row">
                <?php s7upf_output_sidebar('left')?>
                <div class="main-content <?php echo esc_attr(s7upf_get_main_class()); ?>">
                    <div class="content-single">
                        <?php   if(get_post_meta(get_the_ID(),'show_title_page',true) != 'off'):?>
                                    <h2 class="page-title"><?php the_title()?></h2>
                        <?php   endif;?>
                        <?php
                        while ( have_posts() ) : the_post();

                            /*
                            * Include the post format-specific template for the content. If you want to
                            * use this in a child theme, then include a file called called content-___.php
                            * (where ___ is the post format) and that will be used instead.
                            */
                            ?>
                            	<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    								<div class="content-detail-text clearfix">
    									<?php the_content(); ?>
    									<?php
    										wp_link_pages( array(
    											'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kuteshop' ),
    											'after'  => '</div>',
    										) );
    									?>
    								</div><!-- .entry-content -->
    							</div><!-- #post-## -->
                            <?php

                            // If comments are open or we have at least one comment, load up the comment template.
                            if ( comments_open() || get_comments_number()) :
                                comments_template();
                            endif;

                            // End the loop.
                        endwhile; ?>
                    
                    </div>
                </div>
                <?php s7upf_output_sidebar('right')?>
            </div>

        </div>

    </div>
<?php
get_footer();