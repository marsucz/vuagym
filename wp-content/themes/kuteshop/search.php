<?php
/**
 * The template for displaying search results pages.
 *
 * @package 7up-framework
 */

get_header(); ?>
	<div class="main-wrapper page-default"> 
	    <div class="container">
	        <div class="row">
	        	<div class="row">
		            <?php s7upf_output_sidebar('left')?>
		            <div class="main-content <?php echo esc_attr(s7upf_get_main_class()); ?>">
		                <div class="content-blog-page border radius">
		                    <?php if(have_posts()):?>
		                        <?php s7upf_paging_nav();?>
		                        <div class="content-blog-large">
		                        	<h2 class="page-title"><?php printf( esc_html__( 'Search Results for: %s', 'kuteshop' ), '<span>' . get_search_query() . '</span>' ); ?></h2>
		                            <?php while (have_posts()) :the_post();?>

		                                <?php get_template_part('s7upf_templates/blog-content/content');?>

		                            <?php endwhile;?>

		                        </div>
		                        <?php s7upf_paging_nav('bottom');?>
		                        <?php wp_reset_postdata();?>
		                    <?php else : ?>
		                        <div class="search-notfound">
	        					    <p><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'kuteshop' ); ?></p>
	                            </div>
		                    <?php endif;?>
		                    
		                </div>
		            </div>
		            <?php s7upf_output_sidebar('right')?>
	        	</div>
	        </div>
		</div>
	</div>
<?php get_footer(); ?>
