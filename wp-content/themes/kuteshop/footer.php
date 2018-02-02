<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package 7up-framework
 */

?>
	    <?php
	    $page_id = s7upf_get_value_by_id('s7upf_footer_page');
	    if(!empty($page_id)) {
	        s7upf_get_footer_visual($page_id);
	    }
	    else{
	        s7upf_get_footer_default();
	    }
	    s7upf_scroll_top();
	    ?>
	    <div class="wishlist-mask">
	    	<?php
	    	if(class_exists('YITH_WCWL_Init')){
		    	$url = YITH_WCWL()->get_wishlist_url();
		    	echo    '<div class="wishlist-popup">
	                        <span class="popup-icon"><i class="fa fa-bullhorn" aria-hidden="true"></i></span>
	                        <p class="wishlist-alert">"<span class="wishlist-title"></span>" '.esc_html__("was added to wishlist","kuteshop").'</p>
	                        <div class="wishlist-button">
	                            <a href="#" class="wishlist-close">'.esc_html__("Close","kuteshop").' (<span class="wishlist-countdown">3</span>)</a>
	                            <a href="'.esc_url($url).'">'.esc_html__("View page","kuteshop").'</a>
	                        </div>
	                    </div>';
	        }
	    	?>
	    </div>
            <!-- Khoa Anh Contact Widget -->
            <div class="online-support">
                <div class="dropup force-open">
                    <ul class="dropdown-2-menu dropdown-2-menu-right dropdown-2--support">
                        <li>
                            <a href="tel:12345678">
                                <i class="icon-icon-phone"></i> GỌI HOTLINE
                            </a>
                        </li>
                        <li>
                            <a href="https://m.me/orchardvn" target="_blank" rel="noopener">
                                <i class="icon-icon-chat"></i>
                                CHAT FB</a>
                        </li>
                        <li>
                            <a href="http://zalo.me/3580274298351782783" target="_blank" rel="noopener">
                                <i class="icon-icon-zalo"></i> CHAT ZALO
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
	</div>
	<div id="boxes"></div>
	<?php wp_footer(); ?>
</body>
</html>
