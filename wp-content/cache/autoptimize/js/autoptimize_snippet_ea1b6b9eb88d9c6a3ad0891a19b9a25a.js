(function($){"use strict";function get_shop_filter(seff){var filter={};filter['price']={};filter['cats']=[];filter['attributes']={};var terms=[];var min_price=$('#min_price').attr('data-min');var max_price=$('#max_price').attr('data-max');filter['min_price']=min_price;filter['max_price']=max_price;seff.toggleClass('active');if(seff.parents('.pagi-bar').hasClass('pagi-bar')){seff.parents('.pagi-bar').find('.page-numbers').not(seff).removeClass('current');seff.parents('.pagi-bar').find('.page-numbers').not(seff).removeClass('active');seff.addClass('current');seff.addClass('active');}
else{$('.page-numbers').removeClass('current');$('.page-numbers').removeClass('active');$('.pagi-bar').find('.page-numbers').first().addClass('current active');}
if(seff.attr('data-type'))seff.parents('.view-type').find('a.load-shop-ajax').not(seff).removeClass('active');if($('.price_label .from'))filter['price']['min']=$('#min_price').val();if($('.price_label .to'))filter['price']['max']=$('#max_price').val();if($('.woocommerce-ordering'))filter['orderby']=$('select[name="orderby"]').val();if(seff.hasClass('page-numbers')){if(seff.parent().find('.page-numbers.current'))filter['page']=seff.parent().find('.page-numbers.current').html();}
else{if($('.page-numbers.current'))filter['page']=$('.page-numbers.current').html();}
var data_element=$('.shop-get-data');if(seff.attr('data-number'))data_element.attr('data-number',seff.attr('data-number'));if(seff.attr('data-column'))data_element.attr('data-column',seff.attr('data-column'));if(data_element.attr('data-number'))filter['number']=data_element.attr('data-number');if(data_element.attr('data-column'))filter['column']=data_element.attr('data-column');if(data_element.attr('data-item_style'))filter['item_style']=data_element.attr('data-item_style');if(data_element.attr('data-size'))filter['size']=data_element.attr('data-size');if(data_element.attr('data-quickview'))filter['quickview']=data_element.attr('data-quickview');if(data_element.attr('data-quickview_pos'))filter['quickview_pos']=data_element.attr('data-quickview_pos');if(data_element.attr('data-quickview_style'))filter['quickview_style']=data_element.attr('data-quickview_style');if(data_element.attr('data-extra_link'))filter['extra_link']=data_element.attr('data-extra_link');if(data_element.attr('data-extra_style'))filter['extra_style']=data_element.attr('data-extra_style');if(data_element.attr('data-label'))filter['label']=data_element.attr('data-label');if(data_element.attr('data-shop_style'))filter['shop_style']=data_element.attr('data-shop_style');if(data_element.attr('data-block_style'))filter['block_style']=data_element.attr('data-block_style');var i=1;$('.load-shop-ajax.active').each(function(){var seff2=$(this);if(seff2.attr('data-type')){if(i==1)filter['type']=seff2.attr('data-type');i++;}
if(seff2.attr('data-attribute')&&seff2.attr('data-term')){if(!filter['attributes'][seff2.attr('data-attribute')])filter['attributes'][seff2.attr('data-attribute')]=[];if($.inArray(seff2.attr('data-term'),filter['attributes'][seff2.attr('data-attribute')]))filter['attributes'][seff2.attr('data-attribute')].push(seff2.attr('data-term'));}
if(seff2.attr('data-cat')&&$.inArray(seff2.attr('data-cat'),filter['cats']))filter['cats'].push(seff2.attr('data-cat'));})
if($('.shop-page').attr('data-cats'))filter['cats'].push($('.shop-page').attr('data-cats'));var $_GET={};if(document.location.toString().indexOf('?')!==-1){var query=document.location.toString().replace(/^.*?\?/,'').replace(/#.*$/,'').split('&');for(var i=0,l=query.length;i<l;i++){var aux=decodeURIComponent(query[i]).split('=');$_GET[aux[0]]=aux[1];}}
if($_GET['s'])filter['s']=$_GET['s'];if($_GET['product_cat'])filter['cats']=$_GET['product_cat'].split(',');return filter;}
function load_ajax_shop(e){e.preventDefault();var filter=get_shop_filter($(this));console.log(filter);var content=$('.main-shop-load');content.addClass('loadding');content.append('<div class="shop-loading"><i class="fa fa-spinner fa-spin"></i></div>');$.ajax({type:"post",url:ajax_process.ajaxurl,crossDomain:true,data:{action:"load_shop",filter_data:filter,},success:function(data){if(data[data.length-1]=='0'){data=data.split('');data[data.length-1]='';data=data.join('');}
content.find(".shop-loading").remove();content.removeClass('loadding');content.html(data);},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});return false;}
$(document).ready(function(){$('.wishlist-close').on('click',function(){$('.wishlist-mask').fadeOut();})
$('.add_to_wishlist').live('click',function(){$('.wishlist-countdown').html('3');$(this).addClass('added');var product_id=$(this).attr("data-product-id");var product_title=$(this).attr("data-product-title");$('.wishlist-title').html(product_title);$('.wishlist-mask').fadeIn();var counter=3;var popup;popup=setInterval(function(){counter--;if(counter<0){clearInterval(popup);$('.wishlist-mask').hide();}else{$(".wishlist-countdown").text(counter.toString());}},1000);})
$('.shop-ajax-enable').on('click','.load-shop-ajax,.page-numbers,.price_slider_amount .button',load_ajax_shop);$('.shop-ajax-enable').on('change','select[name="orderby"]',load_ajax_shop);$('.shop-ajax-enable .woocommerce-ordering').on('submit',function(e){e.preventDefault();});$('.main-shop-load').on('click','.load-more-shop',function(e){e.preventDefault();var filter=get_shop_filter($(this));var content=$('.main-shop-load .shop-get-data .row');var paged=$(this).attr('data-page');var max_page=$(this).attr('data-maxpage');$(this).find('i').addClass('fa-spin');var seff=$(this);var $_GET={};if(document.location.toString().indexOf('?')!==-1){var query=document.location.toString().replace(/^.*?\?/,'').replace(/#.*$/,'').split('&');for(var i=0,l=query.length;i<l;i++){var aux=decodeURIComponent(query[i]).split('=');$_GET[aux[0]]=aux[1];}}
var s_cat,s_posttype,s_s;if($_GET['s'])s_s=$_GET['s'];if($_GET['product_cat'])s_cat=$_GET['product_cat'];if($_GET['s_posttype'])s_posttype=$_GET['post_type'];$.ajax({type:"post",url:ajax_process.ajaxurl,crossDomain:true,data:{action:"load_more_shop",filter_data:filter,paged:paged,s:s_s,cats:s_cat,post_type:s_posttype,},success:function(data){if(data[data.length-1]=='0'){data=data.split('');data[data.length-1]='';data=data.join('');}
content.append(data);seff.find('i').removeClass('fa-spin');paged=Number(paged)+1;seff.attr('data-page',paged);if(paged>=Number(max_page))seff.fadeOut();},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});})
$('.live-search-on input[name="s"]').on('change keyup',function(){var key=$(this).val();var trim_key=key.trim();var cat=$(this).parents('.live-search-on').find('.cat-value').val();var taxonomy=$(this).parents('.live-search-on').find('.cat-value').attr("name");var post_type=$(this).parents('.live-search-on').find('input[name="post_type"]').val();var seff=$(this);var content=seff.parent().find('.list-product-search');content.html('<i class="fa fa-spinner fa-spin"></i>');content.addClass('ajax-loading');$.ajax({type:"post",url:ajax_process.ajaxurl,crossDomain:true,data:{action:"live_search",key:key,cat:cat,post_type:post_type,taxonomy:taxonomy,},success:function(data){content.removeClass('ajax-loading');if(data[data.length-1]=='0'){data=data.split('');data[data.length-1]='';data=data.join('');}
content.html(data);},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});})
$("body").on("click",".add_to_cart_button:not(.product_type_variable)",function(e){e.preventDefault();var product_id=$(this).attr("data-product_id");var seff=$(this);seff.append('<i class="fa fa-spinner fa-spin"></i>');$.ajax({type:"post",url:ajax_process.ajaxurl,crossDomain:true,data:{action:"add_to_cart",product_id:product_id},success:function(data){seff.find('.fa-spinner').remove();var cart_content=data.fragments['div.widget_shopping_cart_content'];$('.mini-cart-main-content').html(cart_content);$('.widget_shopping_cart_content').html(cart_content);var count_item=cart_content.split("<li").length;$('.cart-item-count').html(count_item-1);var price=$('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();$('.total-mini-cart-price').html(price);},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});});$('body').on('click','.btn-remove',function(e){e.preventDefault();var cart_item_key=$(this).parents('.item-info-cart').attr("data-key");var element=$(this).parents('.item-info-cart');var currency=["د.إ","лв.","kr.","Kr.","Rs.","руб."];var decimal=$(".num-decimal").val();function get_currency(pricehtml){var check,index,price,i;for(i=0;i<6;i++){if(pricehtml.search(currency[i])!=-1){check=true;index=i;}}
if(check)price=pricehtml.replace(currency[index],"");else price=pricehtml.replace(/[^0-9\.]+/g,"");return price;}
$.ajax({type:'POST',url:ajax_process.ajaxurl,crossDomain:true,data:{action:'product_remove',cart_item_key:cart_item_key},success:function(data){var price_html=element.find('span.amount').html();var price=get_currency(price_html);var qty=element.find('.qty-product').find('span').html();var price_remove=price*qty;var current_total_html=$(".total-price").find(".amount").html();console.log(price);var current_total=get_currency(current_total_html);var new_total=current_total-price_remove;new_total=parseFloat(new_total).toFixed(decimal);current_total_html=current_total_html.replace(',','');var new_total_html=current_total_html.replace(current_total,new_total);element.slideUp().remove();$(".total-price").find(".amount").html(new_total_html);$(".total-mini-cart-price").html(new_total_html);var current_html=$('.cart-item-count').html();$('.cart-item-count').html(current_html-1);$('.item-info-cart[data-key="'+cart_item_key+'"]').remove();},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});return false;});$('body').on('click','.product-quick-view',function(e){$.fancybox.showLoading();var product_id=$(this).attr('data-product-id');$.ajax({type:'POST',url:ajax_process.ajaxurl,crossDomain:true,data:{action:'product_popup_content',product_id:product_id},success:function(res){if(res[res.length-1]=='0'){res=res.split('');res[res.length-1]='';res=res.join('');}
$.fancybox.hideLoading();$.fancybox(res,{width:1000,height:600,autoSize:false,onStart:function(opener){if($(opener).attr('id')=='login'){$.get('/hicommon/authenticated',function(res){if('yes'==res){console.log('this user must have already authenticated in another browser tab, SO I want to avoid opening the fancybox.');return false;}else{console.log('the user is not authenticated');return true;}});}},});
/*!
 * Variations Plugin
 */
!function(a,b,c,d){a.fn.wc_variation_form=function(){var c=this,f=c.closest(".product"),g=parseInt(c.data("product_id"),10),h=c.data("product_variations"),i=h===!1,j=!1,k=c.find(".reset_variations");return c.unbind("check_variations update_variation_values found_variation"),c.find(".reset_variations").unbind("click"),c.find(".variations select").unbind("change focusin"),c.on("click",".reset_variations",function(){return c.find(".variations select").val("").change(),c.trigger("reset_data"),!1}).on("reload_product_variations",function(){h=c.data("product_variations"),i=h===!1}).on("reset_data",function(){var b={".sku":"o_sku",".product_weight":"o_weight",".product_dimensions":"o_dimensions"};a.each(b,function(a,b){var c=f.find(a);c.attr("data-"+b)&&c.text(c.attr("data-"+b))}),c.wc_variations_description_update(""),c.trigger("reset_image"),c.find(".single_variation_wrap").slideUp(200).trigger("hide_variation")}).on("reset_image",function(){var a=f.find("div.images img:eq(0)"),b=f.find("div.images a.zoom:eq(0)"),c=a.attr("data-o_src"),e=a.attr("data-o_title"),g=a.attr("data-o_title"),h=b.attr("data-o_href");c!==d&&a.attr("src",c),h!==d&&b.attr("href",h),e!==d&&(a.attr("title",e),b.attr("title",e)),g!==d&&a.attr("alt",g)}).on("change",".variations select",function(){if(c.find('input[name="variation_id"], input.variation_id').val("").change(),c.find(".wc-no-matching-variations").remove(),i){j&&j.abort();var b=!0,d=!1,e={};c.find(".variations select").each(function(){var c=a(this).data("attribute_name")||a(this).attr("name");0===a(this).val().length?b=!1:d=!0,e[c]=a(this).val()}),b?(e.product_id=g,j=a.ajax({url:wc_cart_fragments_params.wc_ajax_url.toString().replace("%%endpoint%%","get_variation"),type:"POST",data:e,success:function(a){a?(c.find('input[name="variation_id"], input.variation_id').val(a.variation_id).change(),c.trigger("found_variation",[a])):(c.trigger("reset_data"),c.find(".single_variation_wrap").after('<p class="wc-no-matching-variations woocommerce-info">'+wc_add_to_cart_variation_params.i18n_no_matching_variations_text+"</p>"),c.find(".wc-no-matching-variations").slideDown(200))}})):c.trigger("reset_data"),d?"hidden"===k.css("visibility")&&k.css("visibility","visible").hide().fadeIn():k.css("visibility","hidden")}else c.trigger("woocommerce_variation_select_change"),c.trigger("check_variations",["",!1]),a(this).blur();c.trigger("woocommerce_variation_has_changed")}).on("focusin touchstart",".variations select",function(){i||(c.trigger("woocommerce_variation_select_focusin"),c.trigger("check_variations",[a(this).data("attribute_name")||a(this).attr("name"),!0]))}).on("found_variation",function(a,b){var e=f.find("div.images img:eq(0)"),g=f.find("div.images a.zoom:eq(0)"),h=e.attr("data-o_src"),i=e.attr("data-o_title"),j=e.attr("data-o_alt"),k=g.attr("data-o_href"),l=b.image_src,m=b.image_link,n=b.image_caption,o=b.image_title;c.find(".single_variation").html(b.price_html+b.availability_html),h===d&&(h=e.attr("src")?e.attr("src"):"",e.attr("data-o_src",h)),k===d&&(k=g.attr("href")?g.attr("href"):"",g.attr("data-o_href",k)),i===d&&(i=e.attr("title")?e.attr("title"):"",e.attr("data-o_title",i)),j===d&&(j=e.attr("alt")?e.attr("alt"):"",e.attr("data-o_alt",j)),l&&l.length>1?(e.attr("src",l).attr("alt",o).attr("title",o),g.attr("href",m).attr("title",n)):(e.attr("src",h).attr("alt",j).attr("title",i),g.attr("href",k).attr("title",i));var p=c.find(".single_variation_wrap"),q=f.find(".product_meta").find(".sku"),r=f.find(".product_weight"),s=f.find(".product_dimensions");q.attr("data-o_sku")||q.attr("data-o_sku",q.text()),r.attr("data-o_weight")||r.attr("data-o_weight",r.text()),s.attr("data-o_dimensions")||s.attr("data-o_dimensions",s.text()),b.sku?q.text(b.sku):q.text(q.attr("data-o_sku")),b.weight?r.text(b.weight):r.text(r.attr("data-o_weight")),b.dimensions?s.text(b.dimensions):s.text(s.attr("data-o_dimensions"));var t=!1,u=!1;b.is_purchasable&&b.is_in_stock&&b.variation_is_visible||(u=!0),b.variation_is_visible||c.find(".single_variation").html("<p>"+wc_add_to_cart_variation_params.i18n_unavailable_text+"</p>"),""!==b.min_qty?p.find(".quantity input.qty").attr("min",b.min_qty).val(b.min_qty):p.find(".quantity input.qty").removeAttr("min"),""!==b.max_qty?p.find(".quantity input.qty").attr("max",b.max_qty):p.find(".quantity input.qty").removeAttr("max"),"yes"===b.is_sold_individually&&(p.find(".quantity input.qty").val("1"),t=!0),t?p.find(".quantity").hide():u||p.find(".quantity").show(),u?p.is(":visible")?c.find(".variations_button").slideUp(200):c.find(".variations_button").hide():p.is(":visible")?c.find(".variations_button").slideDown(200):c.find(".variations_button").show(),c.wc_variations_description_update(b.variation_description),p.slideDown(200).trigger("show_variation",[b])}).on("check_variations",function(c,d,f){if(!i){var g=!0,j=!1,k={},l=a(this),m=l.find(".reset_variations");l.find(".variations select").each(function(){var b=a(this).data("attribute_name")||a(this).attr("name");0===a(this).val().length?g=!1:j=!0,d&&b===d?(g=!1,k[b]=""):k[b]=a(this).val()});var n=e.find_matching_variations(h,k);if(g){var o=n.shift();o?(l.find('input[name="variation_id"], input.variation_id').val(o.variation_id).change(),l.trigger("found_variation",[o])):(l.find(".variations select").val(""),f||l.trigger("reset_data"),b.alert(wc_add_to_cart_variation_params.i18n_no_matching_variations_text))}else l.trigger("update_variation_values",[n]),f||l.trigger("reset_data"),d||l.find(".single_variation_wrap").slideUp(200).trigger("hide_variation");j?"hidden"===m.css("visibility")&&m.css("visibility","visible").hide().fadeIn():m.css("visibility","hidden")}}).on("update_variation_values",function(b,d){i||(c.find(".variations select").each(function(b,c){var e,f=a(c);f.data("attribute_options")||f.data("attribute_options",f.find("option:gt(0)").get()),f.find("option:gt(0)").remove(),f.append(f.data("attribute_options")),f.find("option:gt(0)").removeClass("attached"),f.find("option:gt(0)").removeClass("enabled"),f.find("option:gt(0)").removeAttr("disabled"),e="undefined"!=typeof f.data("attribute_name")?f.data("attribute_name"):f.attr("name");for(var g in d)if("undefined"!=typeof d[g]){var h=d[g].attributes;for(var i in h)if(h.hasOwnProperty(i)){var j=h[i];if(i===e){var k="";d[g].variation_is_active&&(k="enabled"),j?(j=a("<div/>").html(j).text(),j=j.replace(/'/g,"\\'"),j=j.replace(/"/g,'\\"'),f.find('option[value="'+j+'"]').addClass("attached "+k)):f.find("option:gt(0)").addClass("attached "+k)}}}f.find("option:gt(0):not(.attached)").remove(),f.find("option:gt(0):not(.enabled)").attr("disabled","disabled")}),c.trigger("woocommerce_update_variation_values"))}),c.trigger("wc_variation_form"),c};var e={find_matching_variations:function(a,b){for(var c=[],d=0;d<a.length;d++){var f=a[d];e.variations_match(f.attributes,b)&&c.push(f)}return c},variations_match:function(a,b){var c=!0;for(var e in a)if(a.hasOwnProperty(e)){var f=a[e],g=b[e];f!==d&&g!==d&&0!==f.length&&0!==g.length&&f!==g&&(c=!1)}return c}};a.fn.wc_variations_description_update=function(b){var c=this,d=c.find(".woocommerce-variation-description");if(0===d.length)b&&(c.find(".single_variation_wrap").prepend(a('<div class="woocommerce-variation-description" style="border:1px solid transparent;">'+b+"</div>").hide()),c.find(".woocommerce-variation-description").slideDown(200));else{var e=d.outerHeight(!0),f=0,g=!1;d.css("height",e),d.html(b),d.css("height","auto"),f=d.outerHeight(!0),Math.abs(f-e)>1&&(g=!0,d.css("height",e)),g&&d.animate({height:f},{duration:200,queue:!1,always:function(){d.css({height:"auto"})}})}},a(function(){"undefined"!=typeof wc_add_to_cart_variation_params&&a(".variations_form").each(function(){a(this).wc_variation_form().find(".variations select:eq(0)").change()})})}(jQuery,window,document);$('.detail-gallery').each(function(){$(this).find(".carousel").jCarouselLite({btnNext:$(this).find(".gallery-control .next"),btnPrev:$(this).find(".gallery-control .prev"),speed:800,visible:3,});$(this).find('.mid img').elevateZoom({zoomType:"inner",cursor:"crosshair",zoomWindowFadeIn:500,zoomWindowFadeOut:750});$(this).find(".carousel a").on('click',function(event){event.preventDefault();$(this).parents('.detail-gallery').find(".carousel a").removeClass('active');$(this).addClass('active');$(this).parents('.detail-gallery').find(".mid img").attr("src",$(this).find('img').attr("src"));$(this).parents('.detail-gallery').find(".mid img").attr("alt",$(this).find('img').attr("alt"));$(this).parents('.detail-gallery').find(".mid img").attr("title",$(this).find('img').attr("title"));$(this).parents('.detail-gallery').find(".mid img").attr("srcset",$(this).find('img').attr("srcset"));var z_url=$(this).parents('.detail-gallery').find('.mid img').attr('src');$('.zoomWindow').css('background-image','url("'+z_url+'")');$.removeData($('.detail-gallery .mid img'),'elevateZoom');$('.zoomContainer').remove();$('.detail-gallery .mid img').elevateZoom({zoomType:"inner",cursor:"crosshair",zoomWindowFadeIn:500,zoomWindowFadeOut:750});});});$('body input[name="variation_id"]').on('change',function(){var id=$(this).val();var data=$('.variations_form').attr('data-product_variations');var curent_data={};data=$.parseJSON(data);if(id){for(var i=data.length-1;i>=0;i--){if(data[i].variation_id==id)curent_data=data[i];};if('image_id'in curent_data){$('.detail-gallery .gallery-control').find('li[data-image_id="'+curent_data.image_id+'"] a').trigger('click');}
if($('.product-supper11').length>0){var slider_owl=$(this).parents('.product-supper11').find('.product-detail11 .wrap-item');var index=slider_owl.find('.item[data-variation_id="'+id+'"]').attr('data-index');slider_owl.trigger('owl.goTo',index);}
if($('.trend-box18').length>0){$(this).parents('.item-detail18').find('.trend-thumb18').find('img').removeClass('active');$(this).parents('.item-detail18').find('.trend-thumb18').find('div[data-variation_id="'+id+'"]').find('img').addClass('active');}
$('.total-current').attr('data-re_price',curent_data.display_price);$('.total-current').attr('data-price',curent_data.display_regular_price);$('.total-current').html(curent_data.display_price);$('.addcart-special').removeClass("disabled");}
else $('.addcart-special').addClass("disabled");})
$('body .variations_form select').live('change',function(){var text=$(this).val();$(this).parents('.attr-product').find('.current-color').html(text);})
if($('.wrap-attr-product.special').length>0){$('.attr-filter ul li a').live('click',function(){event.preventDefault();var text=$(this).html();$(this).parents('.attr-product').find('.current-color').html(text);$(this).parents('ul').find('li').removeClass('active');$(this).parents('ul').find('li a').removeClass('active');$(this).parent().addClass('active');$(this).addClass('active');var attribute=$(this).parent().attr('data-attribute');var id=$(this).parents('ul').attr('data-attribute-id');$('#'+id).val(attribute);$('#'+id).trigger('change');$('#'+id).trigger('focusin');return false;})
$('.attr-hover-box').hover(function(){var seff=$(this);var old_html=$(this).find('ul').html();var current_val=$(this).find('ul li.active').attr('data-attribute');$(this).next().find('select').trigger('focusin');var content='';$(this).next().find('select').find('option').each(function(){var val=$(this).attr('value');var title=$(this).html();var el_class='';var in_class='';if(current_val==val){el_class=' class="active"';in_class='active';}
if(val!=''){content+='<li'+el_class+' data-attribute="'+val+'"><a href="#" class="bgcolor-'+val+' '+in_class+'"><span></span>'+title+'</a></li>';}})
if(old_html!=content)$(this).find('ul').html(content);})
$('body .reset_variations').live('click',function(){$('.attr-hover-box').each(function(){var seff=$(this);var old_html=$(this).find('ul').html();var current_val=$(this).find('ul li.active').attr('data-attribute');$(this).next().find('select').trigger('focusin');var content='';$(this).next().find('select').find('option').each(function(){var val=$(this).attr('value');var title=$(this).html();var el_class='';var in_class='';if(current_val==val){el_class=' class="active"';in_class='active';}
if(val!=''){content+='<li'+el_class+' data-attribute="'+val+'"><a href="#" class="bgcolor-'+val+' '+in_class+'"><span></span>'+title+'</a></li>';}})
if(old_html!=content)$(this).find('ul').html(content);$(this).find('ul li').removeClass('active');})})}
$("body").on("click",".quantity .qty-up",function(){var min=$(this).prev().attr("min");var max=$(this).prev().attr("max");var step=$(this).prev().attr("step");if(step===undefined)step=1;if(max!==undefined&&Number($(this).prev().val())<Number(max)||max===undefined||max===''){if(step!='')$(this).prev().val(Number($(this).prev().val())+Number(step));}
$('div.woocommerce > form input[name="update_cart"]').prop('disabled',false);return false;})
$("body").on("click",".quantity .qty-down",function(){var min=$(this).next().attr("min");var max=$(this).next().attr("max");var step=$(this).next().attr("step");if(step===undefined)step=1;if(Number($(this).next().val())>1){if(min!==undefined&&$(this).next().val()>min||min===undefined||min===''){if(step!='')$(this).next().val(Number($(this).next().val())-Number(step));}}
$('div.woocommerce > form input[name="update_cart"]').prop('disabled',false);return false;})
$("body").on("keyup change","input.qty-val",function(){$('div.woocommerce > form input[name="update_cart"]').prop('disabled',false);})},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});return false;})
$("body").on("click",".ajax-loadmore-show .load-ajax-btn",function(e){e.preventDefault();var page=$(this).attr("data-page");var max_page=$(this).attr("data-max_page");var load_data=$(this).attr("data-load_data");var seff=$(this);var content=seff.parents('.content-load-wrap').find('.content-load-ajax');seff.find('i').addClass('fa-spin');$.ajax({type:"post",url:ajax_process.ajaxurl,crossDomain:true,data:{action:"loadmore_product",load_data:load_data,page:page,},success:function(data){if(data[data.length-1]=='0'){data=data.split('');data[data.length-1]='';data=data.join('');}
console.log(data);content.append(data);seff.find('i').removeClass('fa-spin');page=Number(page)+1;seff.attr('data-page',page);if(page>=Number(max_page))seff.fadeOut();},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});});$('body').on('click','.masonry-ajax',function(e){e.preventDefault();$(this).find('i').addClass('fa-spin');var current=$(this).parents('.blog-wrap-masonry');var data_load=$(this);var content=current.find('.content-blog-masonry');var number=data_load.attr('data-number');var orderby=data_load.attr('data-orderby');var order=data_load.attr('data-order');var cats=data_load.attr('data-cat');var paged=data_load.attr('data-paged');var maxpage=data_load.attr('data-maxpage');$.ajax({type:'POST',url:ajax_process.ajaxurl,crossDomain:true,data:{action:'load_more_post_masonry',number:number,orderby:orderby,order:order,cats:cats,paged:paged,},success:function(data){if(data[data.length-1]=='0'){data=data.split('');data[data.length-1]='';data=data.join('');}
var $newItem=$(data);content.append($newItem).masonry('appended',$newItem,true);content.imagesLoaded(function(){content.masonry('layout');});paged=Number(paged)+1;data_load.attr('data-paged',paged);data_load.find('i').removeClass('fa-spin');if(Number(paged)>=Number(maxpage)){data_load.parent().fadeOut();}},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});return false;});$('.coupon-light-box').on('click','.get-coupon-button',function(e){e.preventDefault();var seff=$(this);var default_code=seff.attr('data-code');seff.append('<i class="fa fa-spinner fa-spin"></i>');$.ajax({type:"post",url:ajax_process.ajaxurl,crossDomain:true,data:{action:"get_coupon",default_code:default_code,},success:function(data){if(data[data.length-1]=='0'){data=data.split('');data[data.length-1]='';data=data.join('');}
seff.find('.fa-spinner').remove();seff.parent().append("<p>Your code: "+data+"</p>");$('.btn-get-coupon').remove();},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});})
$('body').on('click','.addcart-special.disabled',function(e){return false;})
$('body').on('click','.addcart-special:not(.disabled)',function(e){$.fancybox.showLoading();var product_id=$(this).attr('data-product-id');var qty=$('.detail-qty input[name="quantity"]').val();var price=$('.total-current').attr("data-price");var re_price=$('.total-current').attr("data-re_price");var variation_id=$('input[name="variation_id"]').val();var variations='';if($('.variations_form').length>0){variations='{';var i=1;$('.default-attribute select').each(function(){if(i>1)variations+=",";variations+='"'+$(this).attr('name')+'" : "'+$(this).val()+'"';i++;})
variations+='}';}
variations=$.parseJSON(variations);$.ajax({type:'POST',url:ajax_process.ajaxurl,crossDomain:true,data:{action:'cart_popup_content',product_id:product_id,variation_id:variation_id,variations:variations,qty:qty,price:price,re_price:re_price,},success:function(res){if(res[res.length-1]=='0'){res=res.split('');res[res.length-1]='';res=res.join('');}
$.fancybox.hideLoading();$.fancybox(res,{width:1000,height:500,autoSize:false,closeBtn:false,onStart:function(opener){if($(opener).attr('id')=='login'){$.get('/hicommon/authenticated',function(res){if('yes'==res){console.log('this user must have already authenticated in another browser tab, SO I want to avoid opening the fancybox.');return false;}else{console.log('the user is not authenticated');return true;}});}},});$('.close-light-box').on('click',function(event){event.preventDefault();$.fancybox.close();})
var cart_content=$('.new-content-cart').html();$('.new-content-cart').remove();$('.mini-cart-main-content').html(cart_content);$('.widget_shopping_cart_content').html(cart_content);var count_item=cart_content.split("<li").length;$('.cart-item-count').html(count_item-1);var price_total=$('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();$('.total-mini-cart-price').html(price_total);var nqty=$(res).find('.detail-qty input[name="quantity"]').val();var ins_price=nqty*price;var symbol=$(res).find('.total-cart-popup').attr('data-symbol');ins_price='<span class="woocommerce-Price-currencySymbol">'+symbol+'</span>'+ins_price;$(res).find('.product-price > span').html(ins_price);$(res).find('div.product-price').html(ins_price);$('.content-cart-light-box .product-remove .remove').on('click',function(){var key=$('.box-addcart-special').attr('data-key');$('.mini-cart-main-content .item-info-cart[data-key="'+key+'"] .btn-remove').trigger('click');$(this).parents('.cart_item').remove();$.fancybox.close();})},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});return false;})
$('body').on('click keyup change','.box-addcart-special .qty-up,.box-addcart-special .qty-down,.box-addcart-special input[name="quantity"]',function(e){console.log("run");var key=$('.box-addcart-special').attr('data-key');var variation_id=$('.box-addcart-special').attr('data-variation_id');var qty=$('.box-addcart-special input[name="quantity"]').val();var price=$('.box-addcart-special .pay-price').attr('data-price');var re_price=$('.box-addcart-special .re-price').attr('data-re_price');$('.box-addcart-special .re-price').html(re_price*qty);$('.box-addcart-special .pay-price').html(price*qty);var variations='';if($('.variations_form').length>0){variations='{';var i=1;$('.default-attribute select').each(function(){if(i>1)variations+=",";variations+='"'+$(this).attr('name')+'" : "'+$(this).val()+'"';i++;})
variations+='}';}
variations=$.parseJSON(variations);$.ajax({type:'POST',url:ajax_process.ajaxurl,crossDomain:true,data:{action:'update_cart_popup',key:key,qty:qty,variation_id:variation_id,variations:variations,},success:function(res){if(res[res.length-1]=='0'){res=res.split('');res[res.length-1]='';res=res.join('');}
var cart_content=res;$('.mini-cart-main-content').html(cart_content);$('.widget_shopping_cart_content').html(cart_content);var count_item=cart_content.split("<li").length;$('.cart-item-count').html(count_item-1);var price_total=$('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();$('.total-mini-cart-price').html(price_total);$('.box-addcart-special .total-cart-popup').html(price_total);var items=$('.new-cart-item').html();$('.new-cart-item').remove();$('.total-item-in-cart').html(items);},error:function(MLHttpRequest,textStatus,errorThrown){console.log(errorThrown);}});return false;})});})(jQuery);