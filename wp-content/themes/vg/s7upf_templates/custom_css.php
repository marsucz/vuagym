<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 13/08/15
 * Time: 10:20 AM
 */
$main_color = s7upf_get_value_by_id('main_color');
$body_bg = s7upf_get_value_by_id('body_bg');
$data_attr = s7upf_get_option('woo_attr_background');

// Khoa Anh custom color options
$clr_title_text = s7upf_get_value_by_id('kacolor_title_text');
$clr_title_bg = s7upf_get_value_by_id('kacolor_title_bg');
$clr_addtocart_text = s7upf_get_value_by_id('kacolor_addtocart_text');
$clr_addtocart_bg = s7upf_get_value_by_id('kacolor_addtocart_bg');
$clr_addtocart_hovertext = s7upf_get_value_by_id('kacolor_addtocart_hovertext');
$clr_addtocart_hoverbg = s7upf_get_value_by_id('kacolor_addtocart_hoverbg');
$clr_border_attribute = s7upf_get_value_by_id('kacolor_border_attribute');
$clr_border_productname = s7upf_get_value_by_id('kacolor_border_productname');

?>
<?php
$style = '';
/*****BEGIN TERM BG COLOR*****/
if(!empty($data_attr)){
    foreach ($data_attr as $attr) {
        $style .= '.color-filter.list-filter .bgcolor-'.$attr['attr_slug'].'
        {padding: 1px;font-size:0;}'."\n";
        $style .=   '.termbg-'.$attr['attr_slug'].' span{background-color:'.$attr['attr_bg'].'}'."\n";
        $style .=   '.termbg-'.$attr['attr_slug'].'{font-size:0;}'."\n";
        $style .= '.color-filter.list-filter .bgcolor-'.$attr['attr_slug'].' span
        {background-color:'.$attr['attr_bg'].';
        display: block;
        width: 26px;
        height: 26px;
        border-radius: 4px;}'."\n";
        $style .= '.color-filter.list-filter a.bgcolor-'.$attr['attr_slug'].'.active::after
        {bottom: 0;
        color: #000;
        content: "\f00c";
        font-size: 14px;
        font-family: fontawesome;
        left: 0;
        position: absolute;
        right: 0;
        text-shadow: 1px 1px 1px rgba(255,255,255,.3);
        top: 0;
        display:block;}'."\n";
    }
}
/*****END TERM BG COLOR*****/

if(!empty($body_bg)){
    $style .= 'body
    {background-color:'.$body_bg.'}'."\n";
}
/*****BEGIN MAIN COLOR*****/
if(!empty($main_color)){
    $style .= 'a:focus, a:hover,.account-login a:hover, .address-box .address-toggle,.mini-cart-link .mini-cart-icon,
    .checkout-box .checkout-link, .info-price span, .mini-cart-box .mini-cart-link, .mini-cart-info h3 a:hover, .wishlist-box .wishlist-top-link,
    .main-nav>ul>li .sub-menu>li:hover>a, .main-nav>ul>li:hover>a,
    .price-from span, .product-title a:hover, .tab-pro-ajax-header li a:hover,
    .item-cat-color-more .title18 a:hover, .item-cat-color-more ul li a:hover, .service-info ul li a::before, .service-info ul li a:hover,
    .contact-footer-box.footer-box p a:hover, .list-tag-footer>a:hover, .menu-footer-box ul li a:hover,
    .product-extra-link a,.product-price ins, .quickview-link.plus:hover,.mini-cart1 .mini-cart-link .mini-cart-number,
    .color, .list-cat-icon>li:hover>a, .list-cat-mega-menu a:hover,.wrap-cat-icon1::after,
    .quickview-link.pos-bottom:hover,.title-box1 .list-none li a:hover, .title-box1 .list-none li.active a::before, .title-box1 .title30 span::after,
    .category-box1 .list-none li a:hover, .deal-percent,.product-extra-link2 .compare-link:hover, .product-extra-link2 .wishlist-link:hover,.product-price > span,
    .social-header.style2>a:hover,.flash-countdown::before, .flash-label::before,
    .item-hotcat2 .viewmore::after, .item-hotcat2 .viewmore:hover,.item-testimo3 .title14 a, .list-why .title14 strong,
    .mini-cart-edit a:hover, .smart-search2 .submit-form:hover::before, .submit-form::before,
    .product-extra-link3 a:hover,.item-popcat3 .list-none a:hover, .post-zoom-link,
    .post-date-comment a:hover, .post-date-comment i.fa, .post-title a:hover,
    .product-tab5 .owl-theme .owl-controls .owl-buttons div:hover,.morecat-info5 .seeall:hover,
    .call-phone-number::before,.hotdeal-box6 .title24 i, .trending-box6 .title24 i,
    .tab-pro-ajax-header li.active a,.header-cat-color .cat-color-link:hover,.block-quote>h3,
    .account-login.account-login8>a:hover, .mini-cart8:hover .mini-cart-link .mini-cart-icon, .whistlist-cart8 li>a:hover,
    .tab-title8 .list-none li a:hover, .tab-title8 .list-none li.active a,.deal-countdown8>span,
    .service-footer8 .item-service3 .title14 a:hover,.deal-countdown9 .flash-countdown .time_circles>div,
    .search-form9 .smart-search-form::after,.copyright9 a:hover, .inner-link-top a:hover, .login10>a:hover, .menu-box9 .list-none a:hover, .menu-footer9 .list-none li a:hover, .text-review::before, .top-header10 .currency-language10>div>a:hover,
    .related-post-slider .post-title a:hover, .item-brand-side .brand-title,.widget .tagcloud a:hover,
    .current-color, .current-size, .detail-qty>a:hover, .detail-without-sidebar .hoz-tab-title>ul li.active a, .gallery-control>a:hover, .hoz-tab-title>ul li a:hover, .percent-config,
    .tip-box ul li a:hover,.currency-language10>div a.language-current:hover, .currency-language10>div a.currency-current:hover,
    .title-tab10 .list-none li.active a, .title-tab10 .seeall:hover,.title-coupon li a,
    .footer-tags a:hover, .main-nav.main-nav11>ul>li.current-menu-item>a, .main-nav.main-nav11>ul>li:hover>a,
    .main-nav10.main-nav>ul>li:hover>a,.bestsale-slider11 .owl-theme .owl-controls .owl-buttons div:hover, .title-box11 .list-none li:hover a,
    .product-extra-link5>a,.list-coupon li a span, .top-review11 .owl-theme .owl-controls .owl-buttons div:hover,
    .wrap-cat-icon12 .title-cat-icon::after,.title-tab12 .list-none li.active a::after,
    .banner-slider.banner-slider13 .owl-theme .owl-controls .owl-page.active::after,.wishlist-popup .popup-icon,
    .social-footer15 .list-social>a:hover,.title-tab16 .list-none li.active a, .title-tab16 .list-none li.active a::before,
    .banner-slider17 .bx-pager a,.item-adv16 .product-title a:hover,
    .check-cart19 .checkout-box .dropdown-link .fa, .check-cart19 .wishlist-top-link i.fa,
    .intro-countdown .deals-cowndown::after
    {color:'.$main_color.'}'."\n";
    
    $style .= '.currency-box .currency-list li a:hover, .language-box .language-list li a:hover,
    .smart-search-form input[type=submit],.list-category-toggle li.active a,.list-category-toggle a:hover,
    .list-checkout li a:hover,.owl-theme .owl-controls .owl-buttons div:hover,.newsletter-form input[type=submit],
    .list-social>a:hover,.product-extra-link a:hover,.mini-cart1 .mini-cart-icon,.social-header>a:hover,
    .wrap-cat-icon1 .title-cat-icon,.item-banner1 .banner-info .shopnow,.quickview-link.pos-bottom span::after,
    .title-box1 .title30 span,.product-extra-link2 .addcart-link:hover,.post-zoom-link:hover,
    .owl-theme .owl-controls .owl-page.active span, .owl-theme .owl-controls .owl-page:hover span,
    .smart-search2 .submit-form:hover input[type=submit],.bg-color,.tags3 .widgettitle,
    .hotcat-slider2 .owl-theme .owl-controls .owl-buttons div:hover,.item-testimo3 .title14::before,
    .item-hotcat2 .list-none>li a::before,.arrow-style3 .owl-theme .owl-controls .owl-buttons div:hover,
    .cat-pro3 .btn-control-banner:hover, .product-extra-link3 a.addcart-link,.inner-top-banner .shopnow:hover, .service-footer3,
    .morecat-info5 .list-none a:hover::before,.account-login.account-login6>a:hover,
    .trending-box6 .seeall:hover,.item-hotdeal6 .product-extra-link3 a.addcart-link:hover,
    .block-home6 .product-tab-ajax::before,.tab-pro-ajax-header li.active a::after,
    .btn-loadmore a:hover,.title-box6::before,.hotkey-cat-color a:hover,.whyus-testimo,
    .list-brand6 .header-cat-color::after,.latest-slider6 .owl-theme .owl-controls .owl-buttons div:hover,
    .footer-box6 .list-social>a:hover, .header-nav7,.big-sale7>label,.category-box7 .header-cat-color::after,
    .testimo-slider7 .owl-theme .owl-controls .owl-page.active span,.banner-slider8 .owl-theme .owl-controls .owl-buttons div:hover,
    .product-slider8 .owl-theme .owl-controls .owl-buttons div:hover,.adv-info9 .shopnow:hover,
    .deal-countdown8 .flash-countdown .time_circles>div>.number, .header-nav9,
    .event-form input[type=submit],.mobile-phone9 i,.radius.scroll-top:hover,.flagship-link>a:hover,
    .banner-slider3 .banner-info .shopnow:hover,.tags3 .widget .tagcloud a:hover,
    .mini-cart-button a:hover,.header-nav9 .fixed-header,.view-type a.active, .view-type a:hover,
    .pagi-bar a.current, .pagi-bar span.current,.pagi-bar a.current-page, .pagi-bar a:hover,
    .woocommerce .widget_price_filter .ui-slider .ui-slider-handle,
    .woocommerce .widget_price_filter .ui-slider .ui-slider-range,.hoz-tab-title>ul li.active a::before,
    .woocommerce .price_slider_amount button.button,.woocommerce div.product form.cart .button:hover,
    .woocommerce #respond input#submit:hover, .woocommerce a.button:hover, .woocommerce button.button:hover, .woocommerce input.button:hover,
    .woocommerce #respond input#submit.alt:hover, .woocommerce a.button.alt:hover, .woocommerce button.button.alt:hover, .woocommerce input.button.alt:hover,
    .woocommerce-MyAccount-navigation ul li.is-active, .woocommerce-MyAccount-navigation ul li:hover,
    .woocommerce-account .addresses .title .edit:hover,.post-format-date i.fa,
    .item-post-masonry .readmore:hover, .tip-box ul li a::before,.post-control .btn-control:hover,
    .comment-form input[type=submit]:hover,.mini-cart10 .mini-cart-link .mini-cart-number,
    .banner-slider10 .banner-info .shopnow:hover, .deal-product10 .alldeal:hover, .widget-seller .allreview:hover,
    .title-product-type10 .list-none li.active a, .title-product-type10 .list-none li:hover a,
    .product-extra-link4>a.addcart-link:hover,.testimo-info10 .title18::before, .testimo-slider10 .owl-theme .owl-controls .owl-page.active span, .testimo-slider10 .owl-theme .owl-controls .owl-page:hover span,
    .title-box10::before,.item-superdeal11 .btn-rect:hover,.banner-slider11 .banner-info .shopnow:hover,
    .best-sale11 .title24::before,.fixed-header.main-nav11,.title-coupon li.active a,
    .hot-coupons .title24::before,.shape-title::before,.shape-title,.box-tab11 .list-none li a:hover,
    .title-box11 .list-none li.active a,.item-product11 .product-thumb:hover,.product-extra-link5>a:hover,
    .title14.title-top12::before, .wrap-cat-icon12 .title-cat-icon::before,.banner-slider.banner-slider13 .inner-banner-info,
    .banner-box12 .banner-info .shopnow, .header-box12::before, .title-tab12 .list-none li.active::after,.block-df .title-tab12 .list-none li.active:first-child::before, .title-tab12::after,
    .adv-box13 .shopnow:hover, .item-product13 .product-price .saleoff, .item-product13 .product-price del::after, .pro-deal14 .saleoff, .product-countdown .product-price .saleoff, .product-countdown .product-price del::after, .title-box13,
    .main-nav .toggle-mobile-menu span, .main-nav .toggle-mobile-menu::after, .main-nav .toggle-mobile-menu::before,
    .about-full-protec span.span-text,.wishlist-button a:hover,.banner-adv14 .banner-info .shopnow:hover,
    .banner-slider15 .owl-theme .owl-controls .owl-buttons div:hover,.item-blog15 .post-info,
    .social-header.social-header16>a:hover,.deal-pro16 .addcart-link, .deal-pro16 .saleoff, .title-tab16 .viewall:hover,
    .item-product16 .addcart-link,.item-bnadv16 .banner-info,.intro-countdown .deals-cowndown::before,
    .banner-adv17 .banner-info .shopnow:hover, .mini-cart17 .mini-cart-link,.fixed-header.main-nav17,
    .banner-slider17 .bx-pager a.active, .footer-copyright18.footer-copyright,
    .footer-copyright.footer-copyright17,.poly-slider .banner-info,.poly-slider .owl-theme .owl-controls .owl-buttons div:hover,
    .poly-slider .owl-theme .owl-controls .owl-buttons div,.item-news18,.content-popup input[type="submit"],
    .banner-slider19 .owl-theme .owl-controls .owl-page.active span, .item-product19 .saleoff, .mini-cart-box.dropdown-box:hover .mini-cart-link, .mini-cart-checkout a, .title-box19::before
    {background-color:'.$main_color.'}'."\n";

    $style .= '.smart-search,.title-cat-mega-menu,.list-social>a:hover,.wrap-cat-icon1 .list-cat-icon,
    .header-hotdeal .title-box1,.social-header.style2>a:hover,.title-box3,
    .arrow-style3 .owl-theme .owl-controls .owl-buttons div:hover,.item-trend5 .product-extra-link a:hover,
    .item-hotdeal6 .product-extra-link3 a.addcart-link:hover,.btn-loadmore a:hover,
    .hotkey-cat-color a:hover,.latest-news6,.tab-title8 .list-none li.active a,
    .product-slider8 .owl-theme .owl-controls .owl-buttons div:hover,.item-adv9:hover,
    .widget .tagcloud a:hover,.view-type a.active, .view-type a:hover,.pagi-bar a.current, .pagi-bar span.current,
    .pagi-bar a.current-page, .pagi-bar a:hover,.post-control .btn-control:hover,
    .title-product-type10 .list-none li.active a, .title-product-type10 .list-none li:hover a,
    .testimo-thumb10 a,.title-box11 .list-none,.product-extra-link5>a:hover,.box-tab11 .list-none li a:hover,
    .product-box12.product-box12-df,.deal-pro16:hover,.product-box16,.box-left16,.header-box16,
    .wrap-cat-icon17 .list-cat-icon,.banner-slider17 .bx-pager a span,.brand-box17
    {border-color: '.$main_color.'}'."\n";

    $style .= '.header-nav3,.shape-title::after,.title-coupon
    {border-bottom-color: '.$main_color.'}'."\n";

    $style .= '.mini-cart1 .mini-cart-icon::after,.big-sale7>label::after,
    .poly-slider .owl-theme .owl-controls .owl-buttons div.owl-prev:hover::after, .poly-slider .owl-theme .owl-controls .owl-buttons div.owl-prev:hover::before,
    .poly-slider .owl-theme .owl-controls .owl-buttons div.owl-prev::after, .poly-slider .owl-theme .owl-controls .owl-buttons div.owl-prev::before
    {border-left-color: '.$main_color.'}'."\n";

    $style .= '.poly-slider .owl-theme .owl-controls .owl-buttons div.owl-next:hover::after, .poly-slider .owl-theme .owl-controls .owl-buttons div.owl-next:hover::before,
    .poly-slider .owl-theme .owl-controls .owl-buttons div.owl-next::after, .poly-slider .owl-theme .owl-controls .owl-buttons div.owl-next::before
    {border-right-color: '.$main_color.'}'."\n";

    $style .= '.cat-thumb10 a:hover img
    {filter: drop-shadow(0 0 3px '.$main_color.');
    -moz-filter: drop-shadow(0 0 3px '.$main_color.');
    -webkit-filter: drop-shadow(0 0 3px '.$main_color.');}'."\n";
}
/*****END MAIN COLOR*****/

/*****BEGIN CUSTOM CSS*****/
$custom_css = s7upf_get_option('custom_css');
if(!empty($custom_css)){
    $style .= $custom_css."\n";
}

/*****END CUSTOM CSS*****/

/*****BEGIN MENU COLOR*****/
$menu_color = s7upf_get_option('s7upf_menu_color');
$menu_hover = s7upf_get_option('s7upf_menu_color_hover');
$menu_active = s7upf_get_option('s7upf_menu_color_active');
$menu_color2 = s7upf_get_option('s7upf_menu_color2');
$menu_hover2 = s7upf_get_option('s7upf_menu_color_hover2');
$menu_active2 = s7upf_get_option('s7upf_menu_color_active2');
if(is_array($menu_color) && !empty($menu_color)){
    $style .= '.main-nav > ul > li > a{';
    if(!empty($menu_color['font-color'])) $style .= 'color:'.$menu_color['font-color'].';';
    if(!empty($menu_color['font-family'])) $style .= 'font-family:'.$menu_color['font-family'].';';
    if(!empty($menu_color['font-size'])) $style .= 'font-size:'.$menu_color['font-size'].';';
    if(!empty($menu_color['font-style'])) $style .= 'font-style:'.$menu_color['font-style'].';';
    if(!empty($menu_color['font-variant'])) $style .= 'font-variant:'.$menu_color['font-variant'].';';
    if(!empty($menu_color['font-weight'])) $style .= 'font-weight:'.$menu_color['font-weight'].';';
    if(!empty($menu_color['letter-spacing'])) $style .= 'letter-spacing:'.$menu_color['letter-spacing'].';';
    if(!empty($menu_color['line-height'])) $style .= 'line-height:'.$menu_color['line-height'].';';
    if(!empty($menu_color['text-decoration'])) $style .= 'text-decoration:'.$menu_color['text-decoration'].';';
    if(!empty($menu_color['text-transform'])) $style .= 'text-transform:'.$menu_color['text-transform'].';';
    $style .= '}'."\n";
}
if(!empty($menu_hover)){
    $style .= '.main-nav > ul > li:hover > a:focus,.main-nav > ul > li:hover > a,
    .main-nav > ul li.current-menu-ancestor > a,
    .main-nav > ul li.current-menu-item > a
    {color:'.$menu_hover.'}'."\n";
}
if(!empty($menu_active)){
    $style .= '.main-nav > ul > li:hover, .main-nav > ul >li.current-menu-ancestor,
    .main-nav > ul > li.current-menu-item
    {background-color:'.$menu_active.'}'."\n";
}

// Sub menu
if(is_array($menu_color2) && !empty($menu_color2)){
    $style .= '.main-nav > ul > li.menu-item-has-children li > a{';
    if(!empty($menu_color2['font-color'])) $style .= 'color:'.$menu_color2['font-color'].';';
    if(!empty($menu_color2['font-family'])) $style .= 'font-family:'.$menu_color2['font-family'].';';
    if(!empty($menu_color2['font-size'])) $style .= 'font-size:'.$menu_color2['font-size'].';';
    if(!empty($menu_color2['font-style'])) $style .= 'font-style:'.$menu_color2['font-style'].';';
    if(!empty($menu_color2['font-variant'])) $style .= 'font-variant:'.$menu_color2['font-variant'].';';
    if(!empty($menu_color2['font-weight'])) $style .= 'font-weight:'.$menu_color2['font-weight'].';';
    if(!empty($menu_color2['letter-spacing'])) $style .= 'letter-spacing:'.$menu_color2['letter-spacing'].';';
    if(!empty($menu_color2['line-height'])) $style .= 'line-height:'.$menu_color2['line-height'].';';
    if(!empty($menu_color2['text-decoration'])) $style .= 'text-decoration:'.$menu_color2['text-decoration'].';';
    if(!empty($menu_color2['text-transform'])) $style .= 'text-transform:'.$menu_color2['text-transform'].';';
    $style .= '}'."\n";
}
if(!empty($menu_hover2)){
    $style .= '.main-nav > ul > li.menu-item-has-children li.menu-item-has-children > a:focus,
    .main-nav > ul > li.menu-item-has-children li.menu-item-has-children:hover > a,
    .main-nav > ul .sub-menu > li.current-menu-item > a,
    .main-nav > ul > li .sub-menu > li:hover>a,
    .main-nav > ul > li.menu-item-has-children li.current-menu-item> a,
    .main-nav > ul > li.menu-item-has-children li.current-menu-ancestor > a
    {color:'.$menu_hover2.'}'."\n";
}
if(!empty($menu_active2)){
    $style .= '.main-nav > ul > li.menu-item-has-children li.menu-item-has-children:hover,
    .main-nav > ul > li.menu-item-has-children li.current-menu-ancestor,
    .main-nav > ul > li.menu-item-has-children li.current-menu-item,
    .main-nav>ul>li:not(.has-mega-menu) .sub-menu> li:hover,
    .main-nav > ul > li.menu-item-has-children li.current-menu-ancestor
    {background-color:'.$menu_active2.'}'."\n";
}
/*****END MENU COLOR*****/

/*****BEGIN TYPOGRAPHY*****/
$typo_data = s7upf_get_option('s7upf_custom_typography');
if(is_array($typo_data) && !empty($typo_data)){
    foreach ($typo_data as $value) {
        switch ($value['typo_area']) {
            case 'header':
                $style_class = '.header-page';
                break;

            case 'footer':
                $style_class = '.footer-page';
                break;

            case 'widget':
                $style_class = '.widget';
                break;
            
            default:
                $style_class = '#main-content';
                break;
        }
        $class_array = explode(',', $style_class);
        $new_class = '';
        if(is_array($class_array)){
            foreach ($class_array as $prefix) {
                $new_class .= $prefix.' '.$value['typo_heading'].',';
            }
        }
        if(!empty($new_class)) $style .= $new_class.' .nocss{';
        if(!empty($value['typography_style']['font-color'])) $style .= 'color:'.$value['typography_style']['font-color'].' !important;';
        if(!empty($value['typography_style']['font-family'])) $style .= 'font-family:'.$value['typography_style']['font-family'].' !important;';
        if(!empty($value['typography_style']['font-size'])) $style .= 'font-size:'.$value['typography_style']['font-size'].' !important;';
        if(!empty($value['typography_style']['font-style'])) $style .= 'font-style:'.$value['typography_style']['font-style'].' !important;';
        if(!empty($value['typography_style']['font-variant'])) $style .= 'font-variant:'.$value['typography_style']['font-variant'].' !important;';
        if(!empty($value['typography_style']['font-weight'])) $style .= 'font-weight:'.$value['typography_style']['font-weight'].' !important;';
        if(!empty($value['typography_style']['letter-spacing'])) $style .= 'letter-spacing:'.$value['typography_style']['letter-spacing'].' !important;';
        if(!empty($value['typography_style']['line-height'])) $style .= 'line-height:'.$value['typography_style']['line-height'].' !important;';
        if(!empty($value['typography_style']['text-decoration'])) $style .= 'text-decoration:'.$value['typography_style']['text-decoration'].' !important;';
        if(!empty($value['typography_style']['text-transform'])) $style .= 'text-transform:'.$value['typography_style']['text-transform'].' !important;';
        $style .= '}';
        $style .= "\n";
    }
}

if (!empty($clr_title_text)) {
    $style .= '.title-side { color: '.$clr_title_text.' !important}'."\n";
}
if (!empty($clr_title_bg)) {
    $style .= '.title-side { background-color: '.$clr_title_bg.' !important}'."\n";
}
if (!empty($clr_addtocart_text)) {
    $style .= '.single_add_to_cart_button { color: '.$clr_addtocart_text.' !important}'."\n";
}
if (!empty($clr_addtocart_bg)) {
    $style .= '.single_add_to_cart_button { background-color: '.$clr_addtocart_bg.' !important}'."\n";
}
if (!empty($clr_addtocart_hovertext)) {
    $style .= '.single_add_to_cart_button:hover { color: '.$clr_addtocart_hovertext.' !important}'."\n";
}
if (!empty($clr_addtocart_hoverbg)) {
    $style .= '.single_add_to_cart_button:hover { background-color: '.$clr_addtocart_hoverbg.' !important}'."\n";
}
if (!empty($clr_border_productname)) {
    $style .= '.title-detail { border-left: 7px solid ' . $clr_border_productname . '; !important}'."\n";
} else {
    $style .= '.title-detail { border-left: 7px solid #059; }'."\n";
}
/*****END TYPOGRAPHY*****/
if(!empty($style)) print $style;
?>