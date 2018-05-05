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

<!-- Contact Widget (Chat FB, Chat Zalo, Goi Hotline giông orchard.vn -->
<!--<div class="online-support">
    <div class="dropup force-open">
        <ul class="dropdown-2-menu dropdown-2-menu-right dropdown-2--support">
            <li>
                <a href="tel:12345678">
                    <i class="icon-icon-phone"></i> GỌI HOTLINE
                </a>
                <ul>
                    <li><a target="_blank" href="#">Nội dung 1</a></li>
                    <li><a target="_blank" href="#">Nội dung 2</a></li>
                    <li><a target="_blank" href="#">Nội dung 3</a></li>
                    <li><a target="_blank" href="#">Nội dung 4</a></li>
                </ul>
            </li>
            <li>
                <a href="https://m.me/vuagym" target="_blank" rel="noopener">
                    <i class="icon-icon-chat"></i>
                    CHAT FB</a>
            </li>
            <li>
                <a href="http://zalo.me/2275406627518748561" target="_blank" rel="noopener">
                    <i class="icon-icon-zalo"></i> CHAT ZALO
                </a>
            </li>
        </ul>
    </div>
</div>-->

<div id="chatchat">
	<ul>
		<li><a><i class="fa fa-map-marker" aria-hidden="true" style="margin-left:5px"></i> <span>Chỉ đường</span></a>
			<ul>
                            <li><a><i class="fa fa-map-marker" aria-hidden="true" style="margin-left:5px"></i> <span>Chỉ đường</span></a></li> 
                            <li><a href="tel:1900066638"><i class="fa fa-whatsapp" style="margin-left:3px" aria-hidden="true"></i> <span>GỌI (FREE)</span></a></li>
                            <li><a target="_blank" href="https://m.me/www.fb.com/thegioilongmigantrong"><i class="fa fa-commenting-o" aria-hidden="true"></i><span>INBOX FB</span></a></li>
			</ul> 
		</li> 
		<li><a href="tel:1900066638"><i class="icon-icon-chat" style="margin-left:3px" aria-hidden="true"></i> <span>GỌI (FREE)</span></a></li>
		<li><a target="_blank" href="https://m.me/www.fb.com/thegioilongmigantrong"><i class="fa fa-commenting-o" aria-hidden="true"></i><span>INBOX FB</span></a></li>
	</ul>
</div>

	<div id="boxes"></div>
        
	<?php wp_footer(); ?>
</body>
</html>

<?php
   global $wpdb;
   echo "<pre>";
   print_r($wpdb->queries);
   echo "</pre>";
?>