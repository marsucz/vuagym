<?php
/**
 * The template for displaying all single posts.
 *
 * @package 7up-framework
 */
?>
<?php get_header();?>
    <div id="main-content"  class="main-wrapper">
        <div class="single-default page-default">
            <div class="container">
                <div class="row">
                    <?php s7upf_output_sidebar('left')?>
                    <div class="main-content <?php echo esc_attr(s7upf_get_main_class()); ?>">
                        <div class="content-single clearfix">
                            <?php
                            while ( have_posts() ) : the_post();

                                /*
                                * Include the post format-specific template for the content. If you want to
                                * use this in a child theme, then include a file called called content-___.php
                                * (where ___ is the post format) and that will be used instead.
                                */
                                get_template_part( 's7upf_templates/single-content/content',get_post_format() );
                                wp_link_pages( array(
                                    'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'kuteshop' ),
                                    'after'  => '</div>',
                                    'link_before' => '<span>',
                                    'link_after'  => '</span>',
                                ) );
                                ?>
                                <?php
                                    $previous_post = get_previous_post();
                                    $next_post = get_next_post();
                                ?>
                                <div class="single-post-control">
                                    <div class="row">
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <?php if(!empty( $previous_post )):?>
                                                <div class="post-control prev-control">
                                                    <?php if(has_post_thumbnail($previous_post->ID)):?>
                                                    <div class="post-thumb">
                                                        <a href="<?php echo esc_url(get_permalink( $previous_post->ID )); ?>" class="post-thumb-link"><?php echo get_the_post_thumbnail($previous_post->ID,array(70,70))?></a>
                                                    </div>
                                                    <?php endif;?>
                                                    <div class="post-info">
                                                        <a href="<?php echo esc_url(get_permalink( $previous_post->ID )); ?>" class="btn-control"><i class="fa fa-angle-left" aria-hidden="true"></i><?php esc_html_e("Preview","kuteshop")?></a>
                                                        <h3 class="post-title"><a href="<?php echo esc_url(get_permalink( $previous_post->ID )); ?>"><?php echo get_the_title($previous_post->ID)?></a></h3>
                                                    </div>
                                                </div>
                                            <?php endif;?>
                                        </div>
                                        <div class="col-md-6 col-sm-6 col-xs-6">
                                            <?php if(!empty( $next_post )):?>
                                                <div class="post-control next-control">
                                                    <?php if(has_post_thumbnail($next_post->ID)):?>
                                                    <div class="post-thumb">
                                                        <a href="<?php echo esc_url(get_permalink( $next_post->ID )); ?>" class="post-thumb-link"><?php echo get_the_post_thumbnail($next_post->ID,array(70,70))?></a>
                                                    </div>
                                                    <?php endif;?>
                                                    <div class="post-info">
                                                        <a href="<?php echo esc_url(get_permalink( $next_post->ID )); ?>" class="btn-control"><?php esc_html_e("Next","kuteshop")?><i class="fa fa-angle-right" aria-hidden="true"></i></a>
                                                        <h3 class="post-title"><a href="<?php echo esc_url(get_permalink( $next_post->ID )); ?>"><?php echo get_the_title($next_post->ID)?></a></h3>
                                                    </div>
                                                </div>
                                            <?php endif;?>
                                        </div> 
                                    </div> 
                                </div> 
                                <?php sv_author_box();?>
                                <?php sv_single_related_post();?>
                                <?php
                                if ( comments_open() || get_comments_number() ) { comments_template(); }
                               
                            endwhile; ?>
                        </div>
                    </div>
                    <?php s7upf_output_sidebar('right')?>
                </div>
            </div>
        </div>
    </div>
<?php get_footer();?>