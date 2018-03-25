<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package WordPress
 * @subpackage Twenty_Fifteen
 * @since Twenty Fifteen 1.0
 */

get_header(); ?>

	<div id="primary" class="page-default">
		<div class="container">
			<?php
				$page_id = s7upf_get_option('s7upf_404_page');
				if(!empty($page_id)) {
				    echo        	S7upf_Template::get_vc_pagecontent($page_id);
				}
				else{ ?>
					<main id="main" class="site-main content-blog-page border radius" role="main">				
						<div class="error-404 not-found">
							<h2 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'kuteshop' ); ?></h2>
							<div class="page-content">
								<p><?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'kuteshop' ); ?></p>

								<?php get_search_form(); ?>
							</div><!-- .page-content -->
						</div><!-- .error-404 -->
					</main><!-- .site-main -->
				<?php }
			?>
		</div>
	</div><!-- .content-area -->

<?php get_footer(); ?>
