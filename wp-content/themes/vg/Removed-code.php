<?php ?>
<!-- Contact Widget (Chat FB, Chat Zalo, Goi Hotline giông orchard.vn -->
<div class="online-support">
    <div class="dropup force-open">
        <ul class="dropdown-2-menu dropdown-2-menu-right dropdown-2--support">
            <!--
            <li>
                <a href="tel:12345678">
                    <i class="icon-icon-phone"></i> GỌI HOTLINE
                </a>
            </li>
            -->
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
</div>

// Đánh giá trên trang sản phẩm
if (array_key_exists("reviews",$tabs)){
echo        		'<h2 id="reviews" class="title14 white bg-color title-side" style="background-color: #059; text-align: center;">ĐÁNH GIÁ</h2>
<div class="row product-header" style="padding-bottom: 0px";>
    <div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';								
        $tab = $tabs['reviews'];
        call_user_func( $tab['callback'], 'reviews', $tab );
        echo                    		'</div>
</div>';
}