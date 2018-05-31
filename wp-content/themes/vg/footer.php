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

<?php if (!is_product()) {      ?>
<div id="chatchat">
	<ul>
            <li><a target="_blank" href="https://google.com.vn"><i class="ka-icon-01" aria-hidden="true" style="margin-left:5px"></i> <span>Chỉ đường</span></a></li>
            <li><a target="_blank" href="https://google.com.vn"><i class="ka-icon-02" style="margin-left:3px" aria-hidden="true"></i> <span>GỌI (FREE)</span></a></li>
            <li><a target="_blank" href="https://google.com.vn"><i class="ka-icon-03" aria-hidden="true"></i><span>INBOX FB</span></a></li>
	</ul>
</div>
<?php } else { ?>
<div id="chatchat">
	<ul>
            <li id="add2cart" class="widget-mobile add2cart" style="width: 60% !important;"><a><span style="color: #fff; font-size: 1.2em; padding-left: 22px;">Thêm vào giỏ</span></a>
            <li class="widget-mobile" style="width: 40% !important"><a><span style="font-size: 1.2em;">Liên lạc</span></a>
                    <ul>
                        <li><a target="_blank" href="https://google.com.vn"><i class="ka-icon-01" aria-hidden="true" ></i> <span>Chỉ đường</span></a></li> 
                        <li><a target="_blank" href="https://google.com.vn"><i class="ka-icon-02" aria-hidden="true"></i> <span>GỌI (FREE)</span></a></li>
                        <li><a target="_blank" href="https://google.com.vn"><i class="ka-icon-03" aria-hidden="true"></i><span>INBOX FB</span></a></li>
                    </ul> 
            </li>
            <li class="widget-pc"><a target="_blank" href="https://google.com.vn"><i class="ka-icon-01" aria-hidden="true" ></i> <span>Chỉ đường</span></a></li>
            <li class="widget-pc"><a target="_blank" href="https://google.com.vn"><i class="ka-icon-02" aria-hidden="true"></i> <span>GỌI (FREE)</span></a></li>
            <li class="widget-pc"><a target="_blank" href="https://google.com.vn"><i class="ka-icon-03" aria-hidden="true"></i><span>INBOX FB</span></a></li>
	</ul>
</div>
<?php } ?>
	<div id="boxes"></div>

	<?php wp_footer(); ?>
</body>
</html>
