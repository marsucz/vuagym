(function($){
    "use strict";
    //Shop Filter
    function get_shop_filter(seff){
        var filter = {};
        filter['price'] = {};
        filter['cats'] = [];
        filter['attributes'] = {};
        var terms = [];
        var min_price = $('#min_price').attr('data-min');
        var max_price = $('#max_price').attr('data-max');
        filter['min_price'] = min_price;
        filter['max_price'] = max_price;
        seff.toggleClass('active');
        if(seff.parents('.pagi-bar').hasClass('pagi-bar')){
            seff.parents('.pagi-bar').find('.page-numbers').not(seff).removeClass('current');
            seff.parents('.pagi-bar').find('.page-numbers').not(seff).removeClass('active');
            seff.addClass('current');
            seff.addClass('active');
        }
        else{
            $('.page-numbers').removeClass('current');
            $('.page-numbers').removeClass('active');
            $('.pagi-bar').find('.page-numbers').first().addClass('current active');
        }
        if(seff.attr('data-type')) seff.parents('.view-type').find('a.load-shop-ajax').not(seff).removeClass('active');        
        if($('.price_label .from')) filter['price']['min'] = $('#min_price').val();
        if($('.price_label .to')) filter['price']['max'] = $('#max_price').val();
        if($('.woocommerce-ordering')) filter['orderby'] = $('select[name="orderby"]').val();
        if(seff.hasClass('page-numbers')){
            if(seff.parent().find('.page-numbers.current')) filter['page'] = seff.parent().find('.page-numbers.current').html();
        }
        else{
            if($('.page-numbers.current')) filter['page'] = $('.page-numbers.current').html();
        }
        // if($('.page-numbers.active')) filter['page'] = $('.page-numbers.active').html();
        var data_element = $('.shop-get-data');
        if(seff.attr('data-number')) data_element.attr('data-number',seff.attr('data-number'));
        if(seff.attr('data-column')) data_element.attr('data-column',seff.attr('data-column'));
        if(data_element.attr('data-number')) filter['number'] = data_element.attr('data-number');
        if(data_element.attr('data-column')) filter['column'] = data_element.attr('data-column');
        if(data_element.attr('data-item_style')) filter['item_style'] = data_element.attr('data-item_style');
        if(data_element.attr('data-size')) filter['size'] = data_element.attr('data-size');
        if(data_element.attr('data-quickview')) filter['quickview'] = data_element.attr('data-quickview');
        if(data_element.attr('data-quickview_pos')) filter['quickview_pos'] = data_element.attr('data-quickview_pos');
        if(data_element.attr('data-quickview_style')) filter['quickview_style'] = data_element.attr('data-quickview_style');
        if(data_element.attr('data-extra_link')) filter['extra_link'] = data_element.attr('data-extra_link');
        if(data_element.attr('data-extra_style')) filter['extra_style'] = data_element.attr('data-extra_style');
        if(data_element.attr('data-label')) filter['label'] = data_element.attr('data-label');
        if(data_element.attr('data-shop_style')) filter['shop_style'] = data_element.attr('data-shop_style');
        if(data_element.attr('data-block_style')) filter['block_style'] = data_element.attr('data-block_style');
        var i = 1;
        $('.load-shop-ajax.active').each(function(){
            var seff2 = $(this);
            if(seff2.attr('data-type')){
                if(i == 1) filter['type'] = seff2.attr('data-type');
                i++;
            }
            if(seff2.attr('data-attribute') && seff2.attr('data-term')){
                if(!filter['attributes'][seff2.attr('data-attribute')]) filter['attributes'][seff2.attr('data-attribute')] = [];
                if($.inArray(seff2.attr('data-term'),filter['attributes'][seff2.attr('data-attribute')])) filter['attributes'][seff2.attr('data-attribute')].push(seff2.attr('data-term'));
            }
            if(seff2.attr('data-cat') && $.inArray(seff2.attr('data-cat'),filter['cats'])) filter['cats'].push(seff2.attr('data-cat'));
        })
        if($('.shop-page').attr('data-cats')) filter['cats'].push($('.shop-page').attr('data-cats'));
        // console.log(filter['cats']);
        var $_GET = {};
        if(document.location.toString().indexOf('?') !== -1) {
            var query = document.location
                           .toString()
                           // get the query string
                           .replace(/^.*?\?/, '')
                           // and remove any existing hash string (thanks, @vrijdenker)
                           .replace(/#.*$/, '')
                           .split('&');

            for(var i=0, l=query.length; i<l; i++) {
               var aux = decodeURIComponent(query[i]).split('=');
               $_GET[aux[0]] = aux[1];
            }
        }
        if($_GET['s']) filter['s'] = $_GET['s'];
        if($_GET['product_cat']) filter['cats'] = $_GET['product_cat'].split(',');
        return filter;
    }
    function load_ajax_shop(e){
        e.preventDefault();
        var filter = get_shop_filter($(this));
        console.log(filter);
        var content = $('.main-shop-load');
        content.addClass('loadding');
        content.append('<div class="shop-loading"><i class="fa fa-spinner fa-spin"></i></div>');
        $.ajax({
            type : "post",
            url : ajax_process.ajaxurl,
            crossDomain: true,
            data: {
                action: "load_shop",
                filter_data: filter,
            },
            success: function(data){
                if(data[data.length-1] == '0' ){
                    data = data.split('');
                    data[data.length-1] = '';
                    data = data.join('');
                }
                content.find(".shop-loading").remove();
                content.removeClass('loadding');
                content.html(data);
            },
            error: function(MLHttpRequest, textStatus, errorThrown){                    
                console.log(errorThrown);  
            }
        });
        // console.log(filter);
        return false;
    }

    $(document).ready(function() {
        // Wishlist ajax
        $('.wishlist-close').on('click',function(){
            $('.wishlist-mask').fadeOut();
        })
        $('.add_to_wishlist').live('click',function(){
            $('.wishlist-countdown').html('3');
            $(this).addClass('added');
            var product_id = $(this).attr("data-product-id");
            var product_title = $(this).attr("data-product-title");
            $('.wishlist-title').html(product_title);
            $('.wishlist-mask').fadeIn();
            var counter = 3;
            var popup;
            popup = setInterval(function() {
                counter--;
                if(counter < 0) {
                    clearInterval(popup);
                    $('.wishlist-mask').hide();
                } else {
                    $(".wishlist-countdown").text(counter.toString());
                }
            }, 1000);
        })
        
        // Shop ajax
        $('.shop-ajax-enable').on('click','.load-shop-ajax,.page-numbers,.price_slider_amount .button',load_ajax_shop);
        $('.shop-ajax-enable').on('change','select[name="orderby"]',load_ajax_shop);
        $( '.shop-ajax-enable .woocommerce-ordering' ).on( 'submit', function(e) {
            e.preventDefault();
        });
        
        $('.btn-shoppe-text').on('click', function() {
            
            var product_id = $(this).data("id");
            $.ajax({
                type : "POST",
                url : ajax_process.ajaxurl,
                data: {
                    action: "ka_get_shoppe_popup",
                    product_id: product_id,
                    crossDomain: true,
                },
                success: function(response){
                    $('#main-content').append(response.data);
                    $('#sanTMDTModal').modal('show');
                },
                error: function(data){
                    console.log(data);
                }
            });
            
        });
        
        // Shop load more
        $('.main-shop-load').on('click','.load-more-shop',function(e){
            e.preventDefault();
            var filter = get_shop_filter($(this));
            var content = $('.main-shop-load .shop-get-data .row');
            var paged = $(this).attr('data-page');
            var max_page = $(this).attr('data-maxpage');
            $(this).find('i').addClass('fa-spin');
            var seff = $(this);
            var $_GET = {};
            if(document.location.toString().indexOf('?') !== -1) {
                var query = document.location
                               .toString()
                               // get the query string
                               .replace(/^.*?\?/, '')
                               // and remove any existing hash string (thanks, @vrijdenker)
                               .replace(/#.*$/, '')
                               .split('&');

                for(var i=0, l=query.length; i<l; i++) {
                   var aux = decodeURIComponent(query[i]).split('=');
                   $_GET[aux[0]] = aux[1];
                }
            }
            var s_cat,s_posttype,s_s;
            if($_GET['s']) s_s = $_GET['s'];
            if($_GET['product_cat']) s_cat = $_GET['product_cat'];
            if($_GET['s_posttype']) s_posttype = $_GET['post_type'];
            $.ajax({
                type : "post",
                url : ajax_process.ajaxurl,
                crossDomain: true,
                data: {
                    action: "load_more_shop",
                    filter_data: filter,
                    paged: paged,
                    s: s_s,
                    cats: s_cat,
                    post_type: s_posttype,
                },
                success: function(data){
                    if(data[data.length-1] == '0' ){
                        data = data.split('');
                        data[data.length-1] = '';
                        data = data.join('');
                    }
                    content.append(data);
                    seff.find('i').removeClass('fa-spin');
                    paged = Number(paged) +1;
                    seff.attr('data-page',paged);
                    if(paged >= Number(max_page)) seff.fadeOut();
                },
                error: function(MLHttpRequest, textStatus, errorThrown){                    
                    console.log(errorThrown);  
                }
            });
        })

        //Live search
        $('.live-search-on input[name="s"]').on('change keyup',function(){
            var key = $(this).val();
            var trim_key = key.trim();
            // if(key && trim_key){
               var cat = $(this).parents('.live-search-on').find('.cat-value').val();
               var taxonomy = $(this).parents('.live-search-on').find('.cat-value').attr("name");
               var post_type = $(this).parents('.live-search-on').find('input[name="post_type"]').val();
               var seff = $(this);
               var content = seff.parent().find('.list-product-search');
               content.html('<i class="fa fa-spinner fa-spin"></i>');
               content.addClass('ajax-loading');
               $.ajax({
                    type : "post",
                    url : ajax_process.ajaxurl,
                    crossDomain: true,
                    data: {
                        action: "live_search",
                        key: key,
                        cat: cat,
                        post_type: post_type,
                        taxonomy: taxonomy,
                    },
                    success: function(data){
                        content.removeClass('ajax-loading');
                        if(data[data.length-1] == '0' ){
                            data = data.split('');
                            data[data.length-1] = '';
                            data = data.join('');
                        }
                        content.html(data);
                    },
                    error: function(MLHttpRequest, textStatus, errorThrown){                    
                        console.log(errorThrown);  
                    }
                });
           // }
        })
        /// Woocommerce Ajax
        $("body").on("click",".add_to_cart_button:not(.product_type_variable)",function(e){
            e.preventDefault();
            var product_id = $(this).attr("data-product_id");
            var seff = $(this);
            seff.append('<i class="fa fa-spinner fa-spin"></i>');
            $.ajax({
                type : "post",
                url : ajax_process.ajaxurl,
                crossDomain: true,
                data: {
                    action: "add_to_cart",
                    product_id: product_id
                },
                success: function(data){
                    seff.find('.fa-spinner').remove();
                    var cart_content = data.fragments['div.widget_shopping_cart_content'];
                    $('.mini-cart-main-content').html(cart_content);
                    $('.widget_shopping_cart_content').html(cart_content);
                    var count_item = cart_content.split("<li").length;
                    $('.cart-item-count').html(count_item-1);
                    var price = $('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();
                    $('.total-mini-cart-price').html(price);
                },
                error: function(MLHttpRequest, textStatus, errorThrown){                    
                    console.log(errorThrown);  
                }
            });
        });

        $('body').on('click', '.btn-remove', function(e){
            e.preventDefault();
            var cart_item_key = $(this).parents('.item-info-cart').attr("data-key");
            var element = $(this).parents('.item-info-cart');
            var currency = ["د.إ","лв.","kr.","Kr.","Rs.","руб."];
            var decimal = $(".num-decimal").val();
            function get_currency(pricehtml){
                var check,index,price,i;
                for(i = 0;i<6;i++){
                    if(pricehtml.search(currency[i]) != -1)  {
                        check = true;
                        index = i;
                    }
                }
                if(check) price =  pricehtml.replace(currency[index],"");
                else price = pricehtml.replace(/[^0-9\.]+/g,"");
                return price;
            }
            $.ajax({
                type: 'POST',
                url: ajax_process.ajaxurl,                
                crossDomain: true,
                data: { 
                    action: 'product_remove',
                    cart_item_key: cart_item_key
                },
                success: function(data){
                    var price_html = element.find('span.amount').html();
                    var price = get_currency(price_html);
                    var qty = element.find('.qty-product').find('span').html();
                    var price_remove = price*qty;
                    var current_total_html = $(".total-price").find(".amount").html();
                    console.log(price);
                    var current_total = get_currency(current_total_html);
                    var new_total = current_total-price_remove;
                    new_total = parseFloat(new_total).toFixed(decimal);
                    current_total_html = current_total_html.replace(',','');
                    var new_total_html = current_total_html.replace(current_total,new_total);
                    element.slideUp().remove();
                    $(".total-price").find(".amount").html(new_total_html);
                    $(".total-mini-cart-price").html(new_total_html);
                    var current_html = $('.cart-item-count').html();
                    $('.cart-item-count').html(current_html-1);
                    $('.item-info-cart[data-key="'+cart_item_key+'"]').remove();
                },
                error: function(MLHttpRequest, textStatus, errorThrown){  
                    console.log(errorThrown);  
                }
            });
            return false;
        });

        $('body').on('click','.product-quick-view', function(e){            
            $.fancybox.showLoading();
            var product_id = $(this).attr('data-product-id');
            $.ajax({
                type: 'POST',
                url: ajax_process.ajaxurl,                
                crossDomain: true,
                data: { 
                    action: 'product_popup_content',
                    product_id: product_id
                },
                success: function(res){
                    // console.log(res);
                    if(res[res.length-1] == '0' ){
                        res = res.split('');
                        res[res.length-1] = '';
                        res = res.join('');
                    }
                    $.fancybox.hideLoading();
                    $.fancybox(res, {
                        width: 1000,
                        height: 600,
                        autoSize: false,
                        onStart: function(opener) {                            
                            if ($(opener).attr('id') == 'login') {
                                $.get('/hicommon/authenticated', function(res) { 
                                    if ('yes' == res) {
                                      console.log('this user must have already authenticated in another browser tab, SO I want to avoid opening the fancybox.');
                                      return false;
                                    } else {
                                      console.log('the user is not authenticated');
                                      return true;
                                    }
                                }); 
                            }
                        },
                    });
                    /*!
 * Variations Plugin
 */
!function(t,a,i,r){var e=function(t){this.$form=t,this.$attributeFields=t.find(".variations select"),this.$singleVariation=t.find(".single_variation"),this.$singleVariationWrap=t.find(".single_variation_wrap"),this.$resetVariations=t.find(".reset_variations"),this.$product=t.closest(".product"),this.variationData=t.data("product_variations"),this.useAjax=!1===this.variationData,this.xhr=!1,this.$singleVariationWrap.show(),this.$form.off(".wc-variation-form"),this.getChosenAttributes=this.getChosenAttributes.bind(this),this.findMatchingVariations=this.findMatchingVariations.bind(this),this.isMatch=this.isMatch.bind(this),this.toggleResetLink=this.toggleResetLink.bind(this),t.on("click.wc-variation-form",".reset_variations",{variationForm:this},this.onReset),t.on("reload_product_variations",{variationForm:this},this.onReload),t.on("hide_variation",{variationForm:this},this.onHide),t.on("show_variation",{variationForm:this},this.onShow),t.on("click",".single_add_to_cart_button",{variationForm:this},this.onAddToCart),t.on("reset_data",{variationForm:this},this.onResetDisplayedVariation),t.on("reset_image",{variationForm:this},this.onResetImage),t.on("change.wc-variation-form",".variations select",{variationForm:this},this.onChange),t.on("found_variation.wc-variation-form",{variationForm:this},this.onFoundVariation),t.on("check_variations.wc-variation-form",{variationForm:this},this.onFindVariation),t.on("update_variation_values.wc-variation-form",{variationForm:this},this.onUpdateAttributes),setTimeout(function(){t.trigger("check_variations"),t.trigger("wc_variation_form")},100)};e.prototype.onReset=function(t){t.preventDefault(),t.data.variationForm.$attributeFields.val("").change(),t.data.variationForm.$form.trigger("reset_data")},e.prototype.onReload=function(t){var a=t.data.variationForm;a.variationData=a.$form.data("product_variations"),a.useAjax=!1===a.variationData,a.$form.trigger("check_variations")},e.prototype.onHide=function(t){t.preventDefault(),t.data.variationForm.$form.find(".single_add_to_cart_button").removeClass("wc-variation-is-unavailable").addClass("disabled wc-variation-selection-needed"),t.data.variationForm.$form.find(".woocommerce-variation-add-to-cart").removeClass("woocommerce-variation-add-to-cart-enabled").addClass("woocommerce-variation-add-to-cart-disabled")},e.prototype.onShow=function(t,a,i){t.preventDefault(),i?(t.data.variationForm.$form.find(".single_add_to_cart_button").removeClass("disabled wc-variation-selection-needed wc-variation-is-unavailable"),t.data.variationForm.$form.find(".woocommerce-variation-add-to-cart").removeClass("woocommerce-variation-add-to-cart-disabled").addClass("woocommerce-variation-add-to-cart-enabled")):(t.data.variationForm.$form.find(".single_add_to_cart_button").removeClass("wc-variation-selection-needed").addClass("disabled wc-variation-is-unavailable"),t.data.variationForm.$form.find(".woocommerce-variation-add-to-cart").removeClass("woocommerce-variation-add-to-cart-enabled").addClass("woocommerce-variation-add-to-cart-disabled"))},e.prototype.onAddToCart=function(i){t(this).is(".disabled")&&(i.preventDefault(),t(this).is(".wc-variation-is-unavailable")?a.alert(wc_add_to_cart_variation_params.i18n_unavailable_text):t(this).is(".wc-variation-selection-needed")&&a.alert(wc_add_to_cart_variation_params.i18n_make_a_selection_text))},e.prototype.onResetDisplayedVariation=function(t){var a=t.data.variationForm;a.$product.find(".product_meta").find(".sku").wc_reset_content(),a.$product.find(".product_weight").wc_reset_content(),a.$product.find(".product_dimensions").wc_reset_content(),a.$form.trigger("reset_image"),a.$singleVariation.slideUp(200).trigger("hide_variation")},e.prototype.onResetImage=function(t){t.data.variationForm.$form.wc_variations_image_update(!1)},e.prototype.onFindVariation=function(a){var i=a.data.variationForm,r=i.getChosenAttributes(),e=r.data;if(r.count===r.chosenCount)if(i.useAjax)i.xhr&&i.xhr.abort(),i.$form.block({message:null,overlayCSS:{background:"#fff",opacity:.6}}),e.product_id=parseInt(i.$form.data("product_id"),10),e.custom_data=i.$form.data("custom_data"),i.xhr=t.ajax({url:wc_add_to_cart_variation_params.wc_ajax_url.toString().replace("%%endpoint%%","get_variation"),type:"POST",data:e,success:function(t){t?i.$form.trigger("found_variation",[t]):(i.$form.trigger("reset_data"),i.$form.find(".single_variation").after('<p class="wc-no-matching-variations woocommerce-info">'+wc_add_to_cart_variation_params.i18n_no_matching_variations_text+"</p>"),i.$form.find(".wc-no-matching-variations").slideDown(200))},complete:function(){i.$form.unblock()}});else{i.$form.trigger("update_variation_values");var o=i.findMatchingVariations(i.variationData,e).shift();o?i.$form.trigger("found_variation",[o]):(i.$form.trigger("reset_data"),i.$form.find(".single_variation").after('<p class="wc-no-matching-variations woocommerce-info">'+wc_add_to_cart_variation_params.i18n_no_matching_variations_text+"</p>"),i.$form.find(".wc-no-matching-variations").slideDown(200))}else i.$form.trigger("update_variation_values"),i.$form.trigger("reset_data");i.toggleResetLink(r.chosenCount>0)},e.prototype.onFoundVariation=function(a,i){var r=a.data.variationForm,e=r.$product.find(".product_meta").find(".sku"),o=r.$product.find(".product_weight"),n=r.$product.find(".product_dimensions"),s=r.$singleVariationWrap.find(".quantity"),_=!0,c=!1,d="";i.sku?e.wc_set_content(i.sku):e.wc_reset_content(),i.weight?o.wc_set_content(i.weight_html):o.wc_reset_content(),i.dimensions?n.wc_set_content(i.dimensions_html):n.wc_reset_content(),r.$form.wc_variations_image_update(i),i.variation_is_visible?(c=wp.template("variation-template"),i.variation_id):c=wp.template("unavailable-variation-template"),d=(d=(d=c({variation:i})).replace("/*<![CDATA[*/","")).replace("/*]]>*/",""),r.$singleVariation.html(d),r.$form.find('input[name="variation_id"], input.variation_id').val(i.variation_id).change(),"yes"===i.is_sold_individually?(s.find("input.qty").val("1").attr("min","1").attr("max",""),s.hide()):(s.find("input.qty").attr("min",i.min_qty).attr("max",i.max_qty),s.show()),i.is_purchasable&&i.is_in_stock&&i.variation_is_visible||(_=!1),t.trim(r.$singleVariation.text())?r.$singleVariation.slideDown(200).trigger("show_variation",[i,_]):r.$singleVariation.show().trigger("show_variation",[i,_])},e.prototype.onChange=function(a){var i=a.data.variationForm;i.$form.find('input[name="variation_id"], input.variation_id').val("").change(),i.$form.find(".wc-no-matching-variations").remove(),i.useAjax?i.$form.trigger("check_variations"):(i.$form.trigger("woocommerce_variation_select_change"),i.$form.trigger("check_variations"),t(this).blur()),i.$form.trigger("woocommerce_variation_has_changed")},e.prototype.addSlashes=function(t){return t=t.replace(/'/g,"\\'"),t=t.replace(/"/g,'\\"')},e.prototype.onUpdateAttributes=function(a){var i=a.data.variationForm,r=i.getChosenAttributes().data;i.useAjax||(i.$attributeFields.each(function(a,e){var o=t(e),n=o.data("attribute_name")||o.attr("name"),s=t(e).data("show_option_none"),_=":gt(0)",c=0,d=t("<select/>"),m=o.val()||"",v=!0;if(!o.data("attribute_html")){var l=o.clone();l.find("option").removeAttr("disabled attached").removeAttr("selected"),o.data("attribute_options",l.find("option"+_).get()),o.data("attribute_html",l.html())}d.html(o.data("attribute_html"));var h=t.extend(!0,{},r);h[n]="";var g=i.findMatchingVariations(i.variationData,h);for(var f in g)if("undefined"!=typeof g[f]){var u=g[f].attributes;for(var p in u)if(u.hasOwnProperty(p)){var w=u[p],b="";p===n&&(g[f].variation_is_active&&(b="enabled"),w?(w=t("<div/>").html(w).text(),d.find('option[value="'+i.addSlashes(w)+'"]').addClass("attached "+b)):d.find("option:gt(0)").addClass("attached "+b))}}c=d.find("option.attached").length,!m||0!==c&&0!==d.find('option.attached.enabled[value="'+i.addSlashes(m)+'"]').length||(v=!1),c>0&&m&&v&&"no"===s&&(d.find("option:first").remove(),_=""),d.find("option"+_+":not(.attached)").remove(),o.html(d.html()),o.find("option"+_+":not(.enabled)").prop("disabled",!0),m?v?o.val(m):o.val("").change():o.val("")}),i.$form.trigger("woocommerce_update_variation_values"))},e.prototype.getChosenAttributes=function(){var a={},i=0,r=0;return this.$attributeFields.each(function(){var e=t(this).data("attribute_name")||t(this).attr("name"),o=t(this).val()||"";o.length>0&&r++,i++,a[e]=o}),{count:i,chosenCount:r,data:a}},e.prototype.findMatchingVariations=function(t,a){for(var i=[],r=0;r<t.length;r++){var e=t[r];this.isMatch(e.attributes,a)&&i.push(e)}return i},e.prototype.isMatch=function(t,a){var i=!0;for(var r in t)if(t.hasOwnProperty(r)){var e=t[r],o=a[r];void 0!==e&&void 0!==o&&0!==e.length&&0!==o.length&&e!==o&&(i=!1)}return i},e.prototype.toggleResetLink=function(t){t?"hidden"===this.$resetVariations.css("visibility")&&this.$resetVariations.css("visibility","visible").hide().fadeIn():this.$resetVariations.css("visibility","hidden")},t.fn.wc_variation_form=function(){return new e(this),this},t.fn.wc_set_content=function(t){void 0===this.attr("data-o_content")&&this.attr("data-o_content",this.text()),this.text(t)},t.fn.wc_reset_content=function(){void 0!==this.attr("data-o_content")&&this.text(this.attr("data-o_content"))},t.fn.wc_set_variation_attr=function(t,a){void 0===this.attr("data-o_"+t)&&this.attr("data-o_"+t,this.attr(t)?this.attr(t):""),!1===a?this.removeAttr(t):this.attr(t,a)},t.fn.wc_reset_variation_attr=function(t){void 0!==this.attr("data-o_"+t)&&this.attr(t,this.attr("data-o_"+t))},t.fn.wc_maybe_trigger_slide_position_reset=function(a){var i=t(this),r=i.closest(".product").find(".images"),e=!1,o=a&&a.image_id?a.image_id:"";i.attr("current-image")!==o&&(e=!0),i.attr("current-image",o),e&&r.trigger("woocommerce_gallery_reset_slide_position")},t.fn.wc_variations_image_update=function(i){var r=this,e=r.closest(".product"),o=e.find(".images"),n=e.find(".flex-control-nav li:eq(0) img"),s=o.find(".woocommerce-product-gallery__image, .woocommerce-product-gallery__image--placeholder").eq(0),_=s.find(".wp-post-image"),c=s.find("a").eq(0);if(i&&i.image&&i.image.src&&i.image.src.length>1){if(t('.flex-control-nav li img[src="'+i.image.thumb_src+'"]').length>0)return t('.flex-control-nav li img[src="'+i.image.thumb_src+'"]').trigger("click"),void r.attr("current-image",i.image_id);_.wc_set_variation_attr("src",i.image.src),_.wc_set_variation_attr("height",i.image.src_h),_.wc_set_variation_attr("width",i.image.src_w),_.wc_set_variation_attr("srcset",i.image.srcset),_.wc_set_variation_attr("sizes",i.image.sizes),_.wc_set_variation_attr("title",i.image.title),_.wc_set_variation_attr("alt",i.image.alt),_.wc_set_variation_attr("data-src",i.image.full_src),_.wc_set_variation_attr("data-large_image",i.image.full_src),_.wc_set_variation_attr("data-large_image_width",i.image.full_src_w),_.wc_set_variation_attr("data-large_image_height",i.image.full_src_h),s.wc_set_variation_attr("data-thumb",i.image.src),n.wc_set_variation_attr("src",i.image.thumb_src),c.wc_set_variation_attr("href",i.image.full_src)}else _.wc_reset_variation_attr("src"),_.wc_reset_variation_attr("width"),_.wc_reset_variation_attr("height"),_.wc_reset_variation_attr("srcset"),_.wc_reset_variation_attr("sizes"),_.wc_reset_variation_attr("title"),_.wc_reset_variation_attr("alt"),_.wc_reset_variation_attr("data-src"),_.wc_reset_variation_attr("data-large_image"),_.wc_reset_variation_attr("data-large_image_width"),_.wc_reset_variation_attr("data-large_image_height"),s.wc_reset_variation_attr("data-thumb"),n.wc_reset_variation_attr("src"),c.wc_reset_variation_attr("href");a.setTimeout(function(){t(a).trigger("resize"),r.wc_maybe_trigger_slide_position_reset(i),o.trigger("woocommerce_gallery_init_zoom")},20)},t(function(){"undefined"!=typeof wc_add_to_cart_variation_params&&t(".variations_form").each(function(){t(this).wc_variation_form()})})}(jQuery,window,document);
                    
                    $('.detail-gallery').each(function(){
                        $(this).find(".carousel").jCarouselLite({
                            btnNext: $(this).find(".gallery-control .next"),
                            btnPrev: $(this).find(".gallery-control .prev"),
                            speed: 800,
                            visible:3,
                        });
                        //Elevate Zoom
                        $(this).find('.mid img').elevateZoom({
                            zoomType: "inner",
                            cursor: "crosshair",
                            zoomWindowFadeIn: 500,
                            zoomWindowFadeOut: 750
                        });
                        $(this).find(".carousel a").on('click',function(event) {
                            event.preventDefault();
                            $(this).parents('.detail-gallery').find(".carousel a").removeClass('active');
                            $(this).addClass('active');
                            $(this).parents('.detail-gallery').find(".mid img").attr("src", $(this).find('img').attr("src"));
                            $(this).parents('.detail-gallery').find(".mid img").attr("alt", $(this).find('img').attr("alt"));
                            $(this).parents('.detail-gallery').find(".mid img").attr("title", $(this).find('img').attr("title"));
                            $(this).parents('.detail-gallery').find(".mid img").attr("srcset", $(this).find('img').attr("srcset"));
                            var z_url = $(this).parents('.detail-gallery').find('.mid img').attr('src');
                            $('.zoomWindow').css('background-image','url("'+z_url+'")');
                            $.removeData($('.detail-gallery .mid img'), 'elevateZoom');//remove zoom instance from image
                            $('.zoomContainer').remove();
                            $('.detail-gallery .mid img').elevateZoom({
                                zoomType: "inner",
                                cursor: "crosshair",
                                zoomWindowFadeIn: 500,
                                zoomWindowFadeOut: 750
                            });
                        });
                    });

                    //Fix product variable thumb
                    $('body input[name="variation_id"]').on('change',function(){
                        var id = $(this).val();
                        var data = $('.variations_form').attr('data-product_variations');
                        var curent_data = {};
                        data = $.parseJSON(data);
                        if(id){
                            for (var i = data.length - 1; i >= 0; i--) {
                                if(data[i].variation_id == id) curent_data = data[i];
                            };
                            if('image_id' in curent_data){
                                $('.detail-gallery .gallery-control').find('li[data-image_id="'+curent_data.image_id+'"] a').trigger( 'click' );
                            }
                            if($('.product-supper11').length > 0){
                                var slider_owl = $(this).parents('.product-supper11').find('.product-detail11 .wrap-item');
                                var index = slider_owl.find('.item[data-variation_id="'+id+'"]').attr('data-index');
                                slider_owl.trigger('owl.goTo', index);
                            }
                            if($('.trend-box18').length > 0){
                                $(this).parents('.item-detail18').find('.trend-thumb18').find('img').removeClass('active');
                                $(this).parents('.item-detail18').find('.trend-thumb18').find('div[data-variation_id="'+id+'"]').find('img').addClass('active');
                            }
                            $('.total-current').attr('data-re_price',curent_data.display_price);
                            $('.total-current').attr('data-price',curent_data.display_regular_price);
                            $('.total-current').html(curent_data.display_price);
                            $('.addcart-special').removeClass("disabled");
                        }
                        else $('.addcart-special').addClass("disabled");
                    })
                    //Fix product variable thumb        
                    $('body .variations_form select').live('change',function(){
                        var text = $(this).val();
                        $(this).parents('.attr-product').find('.current-color').html(text);            
                    })
                    // variable product
                    if($('.wrap-attr-product.special').length > 0){
                        $('.attr-filter ul li a').live('click',function(){
                            event.preventDefault();
                            var text = $(this).html();
                            $(this).parents('.attr-product').find('.current-color').html(text);
                            $(this).parents('ul').find('li').removeClass('active');
                            $(this).parents('ul').find('li a').removeClass('active');
                            $(this).parent().addClass('active');
                            $(this).addClass('active');
                            var attribute = $(this).parent().attr('data-attribute');
                            var id = $(this).parents('ul').attr('data-attribute-id');
                            $('#'+id).val(attribute);
                            $('#'+id).trigger( 'change' );
                            $('#'+id).trigger( 'focusin' );
                            return false;
                        })
                        $('.attr-hover-box').hover(function(){
                            var seff = $(this);
                            var old_html = $(this).find('ul').html();
                            var current_val = $(this).find('ul li.active').attr('data-attribute');
                            $(this).next().find('select').trigger( 'focusin' );
                            var content = '';
                            $(this).next().find('select').find('option').each(function(){
                                var val = $(this).attr('value');
                                var title = $(this).html();
                                var el_class = '';
                                var in_class = '';
                                if(current_val == val){
                                    el_class = ' class="active"';
                                    in_class = 'active';
                                }
                                if(val != ''){
                                    content += '<li'+el_class+' data-attribute="'+val+'"><a href="#" class="bgcolor-'+val+' '+in_class+'"><span></span>'+title+'</a></li>';
                                }
                            })
                            // console.log(content);
                            if(old_html != content) $(this).find('ul').html(content);
                        })
                        $('body .reset_variations').live('click',function(){
                            $('.attr-hover-box').each(function(){
                                var seff = $(this);
                                var old_html = $(this).find('ul').html();
                                var current_val = $(this).find('ul li.active').attr('data-attribute');
                                $(this).next().find('select').trigger( 'focusin' );
                                var content = '';
                                $(this).next().find('select').find('option').each(function(){
                                    var val = $(this).attr('value');
                                    var title = $(this).html();
                                    var el_class = '';
                                    var in_class = '';
                                    if(current_val == val){
                                        el_class = ' class="active"';
                                        in_class = 'active';
                                    }
                                    if(val != ''){
                                        content += '<li'+el_class+' data-attribute="'+val+'"><a href="#" class="bgcolor-'+val+' '+in_class+'"><span></span>'+title+'</a></li>';
                                    }
                                })
                                if(old_html != content) $(this).find('ul').html(content);
                                $(this).find('ul li').removeClass('active');
                            })
                        })
                    }
                    //end

                    //QUANTITY CLICK
                    $("body").on("click",".quantity .qty-up",function(){
                        var min = $(this).prev().attr("min");
                        var max = $(this).prev().attr("max");
                        var step = $(this).prev().attr("step");
                        if(step === undefined) step = 1;
                        if(max !==undefined && Number($(this).prev().val())< Number(max) || max === undefined || max === ''){ 
                            if(step!='') $(this).prev().val(Number($(this).prev().val())+Number(step));
                        }
                        $( 'div.woocommerce > form input[name="update_cart"]' ).prop( 'disabled', false );
                        return false;
                    })
                    $("body").on("click",".quantity .qty-down",function(){
                        var min = $(this).next().attr("min");
                        var max = $(this).next().attr("max");
                        var step = $(this).next().attr("step");
                        if(step === undefined) step = 1;
                        if(Number($(this).next().val()) > 1){
                            if(min !==undefined && $(this).next().val()>min || min === undefined || min === ''){
                                if(step!='') $(this).next().val(Number($(this).next().val())-Number(step));
                            }
                        }
                        $( 'div.woocommerce > form input[name="update_cart"]' ).prop( 'disabled', false );
                        return false;
                    })
                    $("body").on("keyup change","input.qty-val",function(){
                        $( 'div.woocommerce > form input[name="update_cart"]' ).prop( 'disabled', false );
                    })
                    //END
                },
                error: function(MLHttpRequest, textStatus, errorThrown){  
                    console.log(errorThrown);  
                }
            });        
            return false;
        })
        // Load product Ajax
        $("body").on("click",".ajax-loadmore-show .load-ajax-btn",function(e){
            e.preventDefault();
            var page = $(this).attr("data-page");
            var max_page = $(this).attr("data-max_page");
            var load_data = $(this).attr("data-load_data");
            // console.log(load_data);
            var seff = $(this);
            var content = seff.parents('.content-load-wrap').find('.content-load-ajax');
            seff.find('i').addClass('fa-spin');
            $.ajax({
                type : "post",
                url : ajax_process.ajaxurl,
                crossDomain: true,
                data: {
                    action: "loadmore_product",
                    load_data: load_data,
                    page: page,
                },
                success: function(data){
                    if(data[data.length-1] == '0' ){
                        data = data.split('');
                        data[data.length-1] = '';
                        data = data.join('');
                    }
                    console.log(data);
                    content.append(data);
                    seff.find('i').removeClass('fa-spin');
                    page = Number(page) +1;
                    seff.attr('data-page',page);
                    if(page >= Number(max_page)) seff.fadeOut();
                },
                error: function(MLHttpRequest, textStatus, errorThrown){                    
                    console.log(errorThrown);  
                }
            });
        });
        //Load product button home 5
        $('body').on('click', '.masonry-ajax', function(e){
            e.preventDefault();
            $(this).find('i').addClass('fa-spin');
            var current = $(this).parents('.blog-wrap-masonry');
            var data_load = $(this);
            var content = current.find('.content-blog-masonry');             
            var number = data_load.attr('data-number');
            var orderby = data_load.attr('data-orderby');
            var order = data_load.attr('data-order');
            var cats = data_load.attr('data-cat');
            var paged = data_load.attr('data-paged');
            var maxpage = data_load.attr('data-maxpage');
            $.ajax({
                type: 'POST',
                url: ajax_process.ajaxurl,                
                crossDomain: true,
                data: { 
                    action: 'load_more_post_masonry',
                    number: number,
                    orderby: orderby,
                    order: order,
                    cats: cats,
                    paged: paged,
                },
                success: function(data){
                    if(data[data.length-1] == '0' ){
                        data = data.split('');
                        data[data.length-1] = '';
                        data = data.join('');
                    }
                    var $newItem = $(data);
                    content.append($newItem).masonry( 'appended', $newItem, true );                    
                    content.imagesLoaded( function() {
                        content.masonry('layout');
                    });
                    paged = Number(paged) + 1;
                    data_load.attr('data-paged',paged);
                    data_load.find('i').removeClass('fa-spin');
                    if(Number(paged)>=Number(maxpage)){
                        data_load.parent().fadeOut();
                    }
                },
                error: function(MLHttpRequest, textStatus, errorThrown){  
                    console.log(errorThrown);  
                }
            });
            return false;
        });
        //end

        // Shop load more
        $('.coupon-light-box').on('click','.get-coupon-button',function(e){
            e.preventDefault();
            var seff = $(this);
            var default_code = seff.attr('data-code');
            seff.append('<i class="fa fa-spinner fa-spin"></i>');            
            $.ajax({
                type : "post",
                url : ajax_process.ajaxurl,
                crossDomain: true,
                data: {
                    action: "get_coupon",
                    default_code: default_code,
                },
                success: function(data){
                    if(data[data.length-1] == '0' ){
                        data = data.split('');
                        data[data.length-1] = '';
                        data = data.join('');
                    }
                    seff.find('.fa-spinner').remove();
                    seff.parent().append("<p>Your code: "+data+"</p>");
                    $('.btn-get-coupon').remove();
                    // $('.coupon-light-box').remove();
                },
                error: function(MLHttpRequest, textStatus, errorThrown){                    
                    console.log(errorThrown);  
                }
            });
        })
        // End

        // single add to cart popup
        $('body').on('click','.addcart-special.disabled', function(e){
            return false;
        })
        $('body').on('click','.addcart-special:not(.disabled)', function(e){
            $.fancybox.showLoading();
            var product_id = $(this).attr('data-product-id');            
            var qty = $('.detail-qty input[name="quantity"]').val();
            var price = $('.total-current').attr("data-price");
            var re_price = $('.total-current').attr("data-re_price");
            var variation_id = $('input[name="variation_id"]').val();
            var variations = '';
            if($('.variations_form').length > 0){
                variations = '{';
                var i = 1;
                $('.default-attribute select').each(function(){
                    if(i > 1 ) variations += ",";
                    variations += '"' + $(this).attr('name') + '" : "' + $(this).val() + '"';
                    i++;
                })
                variations += '}';
            }
            variations = $.parseJSON(variations);
            // console.log(variation_id);
            // console.log(variations);
            $.ajax({
                type: 'POST',
                url: ajax_process.ajaxurl,                
                crossDomain: true,
                data: { 
                    action: 'cart_popup_content',
                    product_id: product_id,
                    variation_id: variation_id,
                    variations: variations,
                    qty: qty,
                    price: price,
                    re_price: re_price,
                },
                success: function(res){
                    // console.log(res);
                    if(res[res.length-1] == '0' ){
                        res = res.split('');
                        res[res.length-1] = '';
                        res = res.join('');
                    }
                    $.fancybox.hideLoading();
                    $.fancybox(res, {
                        width: 1000,
                        height: 500,
                        autoSize: false,
                        closeBtn : false,
                        onStart: function(opener) {                            
                            if ($(opener).attr('id') == 'login') {
                                $.get('/hicommon/authenticated', function(res) { 
                                    if ('yes' == res) {
                                      console.log('this user must have already authenticated in another browser tab, SO I want to avoid opening the fancybox.');
                                      return false;
                                    } else {
                                      console.log('the user is not authenticated');
                                      return true;
                                    }
                                }); 
                            }
                        },
                    });
                    $('.close-light-box').on('click',function(event){
                        event.preventDefault();
                        $.fancybox.close(); 
                    })
                    var cart_content = $('.new-content-cart').html();
                    $('.new-content-cart').remove();
                    $('.mini-cart-main-content').html(cart_content);
                    $('.widget_shopping_cart_content').html(cart_content);
                    var count_item = cart_content.split("<li").length;
                    $('.cart-item-count').html(count_item-1);
                    var price_total = $('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();
                    $('.total-mini-cart-price').html(price_total);
                    var nqty = $(res).find('.detail-qty input[name="quantity"]').val();
                    var ins_price = nqty*price;
                    var symbol = $(res).find('.total-cart-popup').attr('data-symbol');
                    ins_price = '<span class="woocommerce-Price-currencySymbol">'+symbol+'</span>'+ins_price;
                    $(res).find('.product-price > span').html(ins_price);
                    $(res).find('div.product-price').html(ins_price);

                    $('.content-cart-light-box .product-remove .remove').on('click',function(){
                        var key = $('.box-addcart-special').attr('data-key');
                        $('.mini-cart-main-content .item-info-cart[data-key="'+key+'"] .btn-remove').trigger('click');
                        $(this).parents('.cart_item').remove();
                        $.fancybox.close();
                    })
                },
                error: function(MLHttpRequest, textStatus, errorThrown){                    
                    console.log(errorThrown);  
                }
            });
            return false;
        })
        // End

        // single add to cart popup
        $('body').on('click keyup change','.box-addcart-special .qty-up,.box-addcart-special .qty-down,.box-addcart-special input[name="quantity"]', function(e){
            console.log("run");
            var key = $('.box-addcart-special').attr('data-key');
            var variation_id = $('.box-addcart-special').attr('data-variation_id');
            var qty = $('.box-addcart-special input[name="quantity"]').val();
            var price = $('.box-addcart-special .pay-price').attr('data-price');
            var re_price = $('.box-addcart-special .re-price').attr('data-re_price');
            $('.box-addcart-special .re-price').html(re_price*qty);
            $('.box-addcart-special .pay-price').html(price*qty);
            var variations = '';
            if($('.variations_form').length > 0){
                variations = '{';
                var i = 1;
                $('.default-attribute select').each(function(){
                    if(i > 1 ) variations += ",";
                    variations += '"' + $(this).attr('name') + '" : "' + $(this).val() + '"';
                    i++;
                })
                variations += '}';
            }
            variations = $.parseJSON(variations);
            $.ajax({
                type: 'POST',
                url: ajax_process.ajaxurl,                
                crossDomain: true,
                data: { 
                    action: 'update_cart_popup',
                    key: key,
                    qty: qty,
                    variation_id: variation_id,
                    variations: variations,
                },
                success: function(res){
                    if(res[res.length-1] == '0' ){
                        res = res.split('');
                        res[res.length-1] = '';
                        res = res.join('');
                    }
                    var cart_content = res;
                    $('.mini-cart-main-content').html(cart_content);
                    $('.widget_shopping_cart_content').html(cart_content);
                    var count_item = cart_content.split("<li").length;
                    $('.cart-item-count').html(count_item-1);
                    var price_total = $('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();
                    $('.total-mini-cart-price').html(price_total);
                    $('.box-addcart-special .total-cart-popup').html(price_total);
                    var items = $('.new-cart-item').html();
                    $('.new-cart-item').remove();
                    $('.total-item-in-cart').html(items);
                },
                error: function(MLHttpRequest, textStatus, errorThrown){                    
                    console.log(errorThrown);  
                }
            });
            return false;
        })
        // End

    });

})(jQuery);