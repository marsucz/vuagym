(function($){
    "use strict"; // Start of use strict

    /************** FUNCTION ****************/
    function fix_custom_section_item_height(){
    	if($(window).width() > 667){
	    	var item_height = $('.content-from-cat .col-md-3').height();
	    	$('.content-from-cat .col-md-3').each(function(){
	    		var current_height = $(this).height();
	    		if(current_height > item_height) item_height = current_height;
	    	})
	    	$('.content-from-cat .custom-item-large > div').css('height',item_height);
	    	$('.content-from-cat .custom-list-small > div').css('height',item_height/3);
	    }
    }
    // Letter popup
    function letter_popup(){
    	//Popup letter
		var content = $('#boxes-content').html();
		$('#boxes-content').html('');
		$('#boxes').html(content);
		if($('#boxes').html() != ''){
			var id = '#dialog';	
			//Get the screen height and width
			var maskHeight = $(document).height();
			var maskWidth = $(window).width();
		
			//Set heigth and width to mask to fill up the whole screen
			$('#mask').css({'width':maskWidth,'height':maskHeight});
			
			//transition effect		
			$('#mask').fadeIn(500);	
			$('#mask').fadeTo("slow",0.9);	
		
			//Get the window height and width
			var winH = $(window).height();
			var winW = $(window).width();
	              
			//Set the popup window to center
			$(id).css('top',  winH/2-$(id).height()/2);
			$(id).css('left', winW/2-$(id).width()/2);
		
			//transition effect
			$(id).fadeIn(2000); 	
		
			//if close button is clicked
			$('.window .close-popup').click(function (e) {
				//Cancel the link behavior
				e.preventDefault();
				
				$('#mask').hide();
				$('.window').hide();
			});		
			
			//if mask is clicked
			$('#mask').click(function () {
				$(this).hide();
				$('.window').hide();
			});
		}
		//End popup letter
    }
    // Menu fixed
    function fixed_header(){
        var menu_element;
        menu_element = $('.main-nav:not(.menu-fixed-content)').closest('.vc_row');
        var column_element = $('.main-nav:not(.menu-fixed-content)').closest('.col-sm-12');
        if(column_element.length > 0 && !column_element.hasClass('col-md-9') && !column_element.hasClass('col-md-6') && !column_element.hasClass('col-md-4'))  menu_element = $('.main-nav:not(.menu-fixed-content)').closest('.col-sm-12');
        if($('.menu-fixed-enable').length > 0 && $(window).width()>1024){           
            var menu_class = $('.main-nav').attr('class');
            var header_height = $("#header").height()+100;
            var ht = header_height + 150;
            var st = $(window).scrollTop();

            if(!menu_element.hasClass('header-fixed') && menu_element.attr('data-vc-full-width') == 'true') menu_element.addClass('header-fixed');
            if(st>header_height){               
                if(menu_element.attr('data-vc-full-width') == 'true'){
                    if(st > ht) menu_element.addClass('active');
                    else menu_element.removeClass('active');
                    menu_element.addClass('fixed-header');
                }
                else{
                    if(st > ht) menu_element.parent().parent().addClass('active');
                    else menu_element.parent().parent().removeClass('active');
                    if(!menu_element.parent().parent().hasClass('fixed-header')){
                        menu_element.wrap( "<div class='menu-fixed-content fixed-header "+menu_class+"'><div class='container'></div></div>" );
                    }
                }
            }else{
                menu_element.removeClass('active');
                if(menu_element.attr('data-vc-full-width') == 'true') menu_element.removeClass('fixed-header');
                else{
                    if(menu_element.parent().parent().hasClass('fixed-header')){
                        menu_element.unwrap();
                        menu_element.unwrap();
                    }
                }
            }
        }
        else{
            menu_element.removeClass('active');
            if(menu_element.attr('data-vc-full-width') == 'true') menu_element.removeClass('fixed-header');
            else{
                if(menu_element.parent().parent().hasClass('fixed-header')){
                    menu_element.unwrap();
                    menu_element.unwrap();
                }
            }
        }
    }

    //Slider Background
	function background(){
		$('.bg-slider .item-bn,.bg-slider .item-banner').each(function(){
			var src=$(this).find('.banner-thumb a img').attr('src');
			$(this).find('.banner-thumb a img').css('height',$(this).find('.banner-thumb a img').attr('height'));
			$(this).css('background-image','url("'+src+'")');
		});	
	}
    //Detail Gallery
	function detail_gallery(){
		if($('.detail-gallery').length>0){
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
		}
	}
    function menu_responsive(){
    	//Menu Responsive
		$('.toggle-mobile-menu').on('click',function(event){
			event.preventDefault();
			$(this).parents('.main-nav').toggleClass('active');
		});
		if($(window).width()<768){
			$('.main-nav li.menu-item-has-children>a').on('click',function(event){
				if($(window).width()<768){
					event.preventDefault();
					$(this).next().stop(true,false).slideToggle();
				}
			});
		}
    }
    
    var changing = false;
    function tuandev_change_to_another_variation(list_attributes) {
        $('body .reset_variations').click();
        $('.attr-hover-box').each(function() {
            var atrributes_clicked = false;
            $(this).find('ul').find('li a').each(function() {

                var child_attribute = $(this).parent().attr('data-attribute');
                var child_id = $(this).parents('ul').attr('data-attribute-id');

                if (changing) {
                    if (list_attributes[child_id] === child_attribute) {
                        $(this).click();
                        atrributes_clicked = true;
                        return false;
                    }
                } else {
                    return false;
                }

            });
            
            if (!atrributes_clicked) {
                $(this).find('ul').find('li a').first().click();
            }
        });
    }
    
    function clicked_default_attributes() {
        var id = $('body input[name="variation_id"]').val();
        if (!id) return;
        $('.attr-hover-box').each(function() {
            var attr_name = $(this).find('ul').attr('data-attribute-id');
            var attr_default = $('#ez-attr-default-' + attr_name).val();
            if (!attr_default) return false;
            var atrributes_clicked = false;
            $(this).find('ul').find('li a').each(function() {
                var child_attribute = $(this).parent().attr('data-attribute');
                if (attr_default === child_attribute) {
                    $(this).click();
                    atrributes_clicked = true;
                    return false;
                }
            });
            
            if (!atrributes_clicked) return false;
        });
    }
    
    function fix_variable_product(){
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
        
        var list_attributes = [];
        var all_changes = 0;
        if($('.wrap-attr-product.special').length > 0){
            $('.attr-filter ul li a').live('click',function(event){
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
                
                //Tuan Dev
                list_attributes[id] = attribute;
                var count = 0;
                $('.attr-hover-box').each(function(){
                    count++;
                    var seff = $(this);
                    if (count === 1) {
                        return true;
                    }
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
                });
                
                if (Object.keys(list_attributes).length === $('.attr-hover-box').length) {
                    var id = $('body input[name="variation_id"]').val();
                    if ((!id && !changing) || $('.single_add_to_cart_button').hasClass('disabled')) {
                        all_changes++;
                        if (all_changes > 3) {
                            all_changes = 0;
//                            console.log("STOP!");
                            return;
                        }
                        if (!changing) {
                            changing = true;
                            tuandev_change_to_another_variation(list_attributes);
                        }
                        changing = false;
                    } else {
                        all_changes = 0;
                    }
                }
            })
            
//            $('.attr-hover-box').hover(function(){
//                var seff = $(this);
//                var old_html = $(this).find('ul').html();
//                var current_val = $(this).find('ul li.active').attr('data-attribute');
//                $(this).next().find('select').trigger( 'focusin' );
//                var content = '';
//                $(this).next().find('select').find('option').each(function(){
//                    var val = $(this).attr('value');
//                    var title = $(this).html();
//                    var el_class = '';
//                    var in_class = '';
//                    if(current_val == val){
//                    	el_class = ' class="active"';
//                    	in_class = 'active';
//                    }
//                    if(val != ''){
//                        content += '<li'+el_class+' data-attribute="'+val+'"><a href="#" class="bgcolor-'+val+' '+in_class+'"><span></span>'+title+'</a></li>';
//                    }
//                })
//                // console.log(content);
//                if(old_html != content) $(this).find('ul').html(content);
//            })
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
    }
    
    function afterAction(){
		this.$elem.find('.owl-item').removeClass('active');
		this.$elem.find('.owl-item').eq(this.owl.currentItem).addClass('active');
		this.$elem.find('.owl-item').each(function(){
			var check = $(this).hasClass('active');
			if(check==true){
				$(this).find('.animated').each(function(){
					var anime = $(this).attr('data-animated');
					$(this).addClass(anime);
				});
			}else{
				$(this).find('.animated').each(function(){
					var anime = $(this).attr('data-animated');
					$(this).removeClass(anime);
				});
			}
		})
	}
    function s7upf_qty_click(){
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
    }
    
    function s7upf_owl_slider(){
    	//Carousel Slider
		if($('.sv-slider').length>0){
			$('.sv-slider').each(function(){
				var seff = $(this);
				var item = seff.attr('data-item');
				var speed = seff.attr('data-speed');
				var itemres = seff.attr('data-itemres');
				var animation = seff.attr('data-animation');
				var nav = seff.attr('data-nav');
				var text_prev = seff.attr('data-prev');
				var text_next = seff.attr('data-next');
				var pagination = false, navigation= true, singleItem = false;
				var autoplay;
				if(speed != '') autoplay = speed;
				else autoplay = false;
				// Navigation
				if(nav == 'nav-hidden'){
					pagination = false;
					navigation= false;
				}
				if(nav == 'superdeal-slider11' || nav == 'testimo-slider' || nav =='testimo-slider14'){
					pagination = true;
					navigation= false;
				}
				if(nav == 'banner-slider banner-slider13'){
					pagination = true;
					navigation= true;
				}
				if(animation != ''){
					singleItem = true;
					item = '1';
				}
				var prev_text = '<i class="fa fa-angle-left" aria-hidden="true"></i>';
				var next_text = '<i class="fa fa-angle-right" aria-hidden="true"></i>';
				if(nav == 'nav-text-data'){
					var prev_text = text_prev;
					var next_text = text_next;
				}
				if(itemres == '' || itemres === undefined){
					if(item == '1') itemres = '0:1,480:1,768:1,1200:1';
					if(item == '2') itemres = '0:1,480:1,768:2,1200:2';
					if(item == '3') itemres = '0:1,480:2,768:2,992:3';
					if(item == '4') itemres = '0:1,480:2,840:3,1200:4';
					if(item >= '5') itemres = '0:1,480:2,768:3,1024:4,1200:'+item;
				}				
				itemres = itemres.split(',');
				var i;
				for (i = 0; i < itemres.length; i++) { 
				    itemres[i] = itemres[i].split(':');
				}
				seff.owlCarousel({
					items: item,
					itemsCustom: itemres,
					autoPlay:autoplay,
					pagination: pagination,
					navigation: navigation,
					navigationText:[prev_text,next_text],
					singleItem : singleItem,
					beforeInit:background,
					// addClassActive : true,
					afterAction: afterAction,
					touchDrag: true,
					transitionStyle : animation
				});
			});			
		}
    }

    function s7upf_all_slider(seff,number){
    	if(!seff) seff = $('.smart-slider');
    	if(!number) number = '';
    	//Carousel Slider
		if(seff.length>0){
			seff.each(function(){
				var seff = $(this);
				var item = seff.attr('data-item'+number);
				var speed = seff.attr('data-speed');
				var itemres = seff.attr('data-itemres'+number);
				var text_prev = seff.attr('data-prev');
				var text_next = seff.attr('data-next');
				var pagination = seff.attr('data-pagination');
				var navigation = seff.attr('data-navigation');
				var autoplay;
				if(speed === undefined) speed = '';
				if(speed != '') autoplay = speed;
				else autoplay = false;
				if(item == '' || item === undefined) item = 1;
				if(itemres === undefined) itemres = '';
				if(text_prev == 'false') text_prev = '';
				else{
					if(text_prev == '' || text_prev === undefined) text_prev = '<i class="fa fa-angle-left" aria-hidden="true"></i>';
					else text_prev = '<i class="fa '+text_prev+'" aria-hidden="true"></i>';
				}
				if(text_next == 'false') text_next = '';
				else{
					if(text_next == '' || text_next === undefined) text_next = '<i class="fa fa-angle-right" aria-hidden="true"></i>';
					else text_next = '<i class="fa '+text_next+' aria-hidden="true"></i>';
				}
				if(pagination == 'true') pagination = true;
				else pagination = false;
				if(navigation == 'true') navigation = true;
				else navigation = false;
				// Item responsive
				if(itemres == '' || itemres === undefined){
					if(item == '1') itemres = '0:1,480:1,768:1,1200:1';
					if(item == '2') itemres = '0:1,480:1,768:2,1200:2';
					if(item == '3') itemres = '0:1,480:2,768:2,992:3';
					if(item == '4') itemres = '0:1,480:2,840:3,1200:4';
					if(item >= '5') itemres = '0:1,480:2,768:3,1200:'+item;
				}				
				itemres = itemres.split(',');
				var i;
				for (i = 0; i < itemres.length; i++) { 
				    itemres[i] = itemres[i].split(':');
				}
				seff.owlCarousel({
					items: item,
					itemsCustom: itemres,
					autoPlay:autoplay,
					pagination: pagination,
					navigation: navigation,
					navigationText:[text_prev,text_next],
					addClassActive : true,
					touchDrag: true,
					// autoHeight:true,
					// afterAction: afterAction,
				});
			});			
		}
    }

    /************ END FUNCTION **************/

	$(document).ready(function(){
		if($('.variations_form').length > 0){
			var id = $('input[name="variation_id"]').val();
			if(id == 0) $('.addcart-special').addClass("disabled");
		}
		$('.special-total-cart .qty-down').on('click',function(){
			$('.detail-extralink .qty-down').trigger('click');
			var price = $('.total-current').attr('data-price');
			var qty = $('.detail-qty input[name="quantity"]').val();
			var total = Number(price)*Number(qty);
			$('.total-current').html(total);
		})
		$('.special-total-cart .qty-up').on('click',function(){
			$('.detail-extralink .qty-up').trigger('click');
			var price = $('.total-current').attr('data-price');
			var qty = $('.detail-qty input[name="quantity"]').val();
			var total = Number(price)*Number(qty);
			$('.total-current').html(total);
		})
		// $('.addcart-special').on('click',function(){
		// 	$('.single_add_to_cart_button').trigger('click');
		// })
		//Add Cart Special
		$('.btn-get-coupon').fancybox({
			'closeBtn' : false 
		});
		//Get Coupon
		$('.btn-get-coupon').fancybox({
			'closeBtn' : false 
		});
		$('.close-light-box').on('click',function(event){
			event.preventDefault();
			$.fancybox.close(); 
		})
		letter_popup();
		menu_responsive();
		s7upf_qty_click();
		fix_variable_product();
                clicked_default_attributes();
		//menu fix home 8
		$('.title-cat-icon').on('click',function(){
			if($(this).closest('.vc_row').hasClass('fixed-header')) $(this).next().slideToggle('slow');
		})
		//Back To Top
		$('.scroll-top').on('click',function(event){
			event.preventDefault();
			$('html, body').animate({scrollTop:0}, 'slow');
		});
		//Animate
		// if($('.wow').length>0){
			new WOW().init();
		// }
		//Count item cart
        if($("#count-cart-item").length){
            var count_cart_item = $("#count-cart-item").val();
            $(".cart-item-count").html(count_cart_item);
        }
        //Widget Product Category
        $('.widget .product-categories li.cat-parent').first().addClass('active');
		$('.widget .product-categories li.cat-parent').first().find('ul').show();
		$('.widget .product-categories li.cat-parent > a').on('click',function(event){
			event.preventDefault();
			$(this).parent().toggleClass('active');
			$(this).next().slideToggle();
		});
		//Widget Toggle
		$('.widget-title').on('click',function(){
			$(this).toggleClass('active');
			$(this).next().slideToggle();
		});
		//Fix mailchimp
        $('.sv-mailchimp-form').each(function(){
            var placeholder = $(this).attr('data-placeholder');
            var submit = $(this).attr('data-submit');
            if(placeholder) $(this).find('input[name="EMAIL"]').attr('placeholder',placeholder);
            if(submit) $(this).find('input[type="submit"]').val(submit);
        })
        //Cat search
		$('.select-category .list-category-toggle li a').click(function(event){
			event.preventDefault();
			$(this).parents('.list-category-toggle').find('li').removeClass('active');
			$(this).parent().addClass('active');
			var x = $(this).attr('data-filter');
			if(x){
				x = x.replace('.','');
				$('.cat-value').val(x);
			}
			else $('.cat-value').val('');
			$('.category-toggle-link span').text($(this).text());
		});
		//Live search
		$('.live-search-on input[name="s"]').on('click',function(event){
			event.preventDefault();
			event.stopPropagation();
			$(this).parents('.live-search-on').addClass('active');
		})
		$('body').on('click',function(event){
			$('.live-search-on.active').removeClass('active');
		})
		//Flash Count Down
		if($('.flash-countdown').length>0){
			$(".flash-countdown").TimeCircles({
				fg_width: 0.01,
				bg_width: 1.2,
				text_size: 0.07,
				circle_bg_color: "#ffffff",
				time: {
					Days: {
						show: true,
						text: "",
						color: "#f9bc02"
					},
					Hours: {
						show: true,
						text: "",
						color: "#f9bc02"
					},
					Minutes: {
						show: true,
						text: "",
						color: "#f9bc02"
					},
					Seconds: {
						show: true,
						text: "",
						color: "#f9bc02"
					}
				}
			}); 
		}
		if($('.deals-cowndown').length>0){
			$(".deals-cowndown").TimeCircles({
				fg_width: 0.01,
				bg_width: 1.2,
				text_size: 0.07,
				circle_bg_color: "#ffffff",
				time: {
					Days: {
						show: true,
						text: "d",
						color: "#f9bc02"
					},
					Hours: {
						show: true,
						text: "h",
						color: "#f9bc02"
					},
					Minutes: {
						show: true,
						text: "m",
						color: "#f9bc02"
					},
					Seconds: {
						show: true,
						text: "s",
						color: "#f9bc02"
					}
				}
			}); 
		}
		//Tag Toggle
		if($('.toggle-tab').length>0){
			$('.toggle-tab').each(function(){
				$(this).find('.item-toggle-tab').first().addClass('active');
				$(this).find('.item-toggle-tab').first().find('.toggle-tab-content').show();				
			});
		}
		$('.toggle-tab-title').on('click',function(){
			$(this).parent().siblings().removeClass('active');
			$(this).parent().toggleClass('active');
			$(this).parents('.toggle-tab').find('.toggle-tab-content').not($(this).next()).slideUp();
			$(this).next().slideToggle();
		});
	});

	/************ END READY **************/

	$(window).load(function(){
            
		/* fix_custom_section_item_height(); */
		detail_gallery();
		s7upf_owl_slider();
		s7upf_all_slider();
		$('.top-banner19').each(function(){
			var src = $(this).find('img').attr('src');
			$(this).css('background-image','url('+src+')');
		})
		$('.top-banner19').on('click',function(){
			console.log('hjshn');
			window.location.href = $(this).find('a').attr('href');
		})
		$('.fix-slider-nav8').each(function(){
			var title_width = $(this).find('.title18 > span').width();
			$(this).find('.owl-controls').css('left',title_width + 20);
		})
		//img Light Box
		$('.post-zoom-link').fancybox();
		$('.price-sale').each(function(){
			var sale_html = $(this).find('.sale-content').html();
			$(this).find('.sale-content').remove();
			$(sale_html).insertAfter( $(this).find('.product-price').find('del'));
			// $(this).find('.product-price').append(sale_html);
		})
		//Control Category Banner
		if($('.cat-pro3').length>0){
			$('.cat-pro3').each(function(){
				$(this).find('.hide-cat-banner').on('click',function(event){
					event.preventDefault();
					$(this).parents('.cat-pro3').addClass('hidden-banner');
				});
				$(this).find('.show-cat-banner').on('click',function(event){
					event.preventDefault();
					$(this).parents('.cat-pro3').removeClass('hidden-banner');
				});
			});
		}
		if($('.countdown-master').length>0){
			$('.countdown-master').each(function(){
				var seconds = Number($(this).attr('data-time'));
				$(this).FlipClock(seconds,{
			        clockFace: 'HourlyCounter',
			        countdown: true,
			        autoStart: true,
			    });
			});
		}
		// menu fixed onload
		$("#header").css('min-height','');
        if($(window).width()>1024){
            $("#header").css('min-height',$("#header").height());
            fixed_header();
        }
        else{
            $("#header").css('min-height','');
        }
		if($('.rtl-enable').length > 0){
            $('*[data-vc-full-width="true"]').each(function(){
                var style = $(this).attr('style');
                style = style.replace("left","right");
                $(this).attr('style',style);
            })
            $('*[data-vc-full-width="true"] > *[data-vc-full-width="true"]').each(function(){
                var style = $(this).parent().attr('style');
                $(this).attr('style',style);
            })
        }
        //Blog Masonry 
		if($('.content-blog-masonry').length>0){
			$('.content-blog-masonry').masonry({
				// options
				itemSelector: '.item-post-masonry',
			});
		}

		//BxSlider
		if($('.bxslider-banner').length>0){
			$('.bxslider-banner').each(function(){
				$(this).find('.bxslider').bxSlider({
					controls:false,
					pagerCustom: $(this).find('.bx-pager')
				});
			});
		}
		
		//Deal Count Down
		if($('.detail-countdown').length>0){
			$(".detail-countdown").TimeCircles({
				fg_width: 0.01,
				bg_width: 1.2,
				text_size: 0.07,
				circle_bg_color: "#ffffff",
				time: {
					Days: {
						show: true,
						text: "",
						color: "#f9bc02"
					},
					Hours: {
						show: true,
						text: "",
						color: "#f9bc02"
					},
					Minutes: {
						show: true,
						text: "",
						color: "#f9bc02"
					},
					Seconds: {
						show: true,
						text: "",
						color: "#f9bc02"
					}
				}
			}); 
		}
	
	});

	/************ END LOAD **************/
	var w_width = $(window).width();
	$(window).resize(function(){
		var c_width = $(window).width();
        setTimeout(function() {
            if($('.rtl-enable').length > 0 && c_width != w_width){
                $('*[data-vc-full-width="true"]').each(function(){
                    var style = $(this).attr('style');
                    style = style.replace(" left:"," right:");
                    $(this).attr('style',style);
                })
                $('*[data-vc-full-width="true"] > *[data-vc-full-width="true"]').each(function(){
	                var style = $(this).parent().attr('style');
	                $(this).attr('style',style);
	            })
                w_width = c_width;
            }
        }, 3000);
        $.removeData($('.detail-gallery .mid img'), 'elevateZoom');//remove zoom instance from image
		$('.zoomContainer').remove();
		$('.detail-gallery .mid img').elevateZoom({
			zoomType: "inner",
			cursor: "crosshair",
			zoomWindowFadeIn: 500,
			zoomWindowFadeOut: 750
		});
	});

	/************ END RESIZE **************/

	$(window).scroll(function(){
		if($(window).width()>1024){
            $("#header").css('min-height',$("#header").height());
            fixed_header();
        }
        else{
            $("#header").css('min-height','');
        }
		//Scroll Top
		if($(this).scrollTop()>$(this).height()){
			$('.scroll-top').addClass('active');
		}else{
			$('.scroll-top').removeClass('active');
		}
		var count = 1;
		if($('#coupon-light-box').length > 0){
		  	if ($(window).scrollTop() > ($('body').height() / 2 - 250) && $(window).scrollTop() < ($('body').height() / 2 + 250)) {
		  		if (count == 1) {
			    	if (!$.cookie("first_visitor")) {
					    $('.btn-get-coupon').trigger('click');
					    count++;
					}				
					$.cookie("first_visitor", "visited");
				}
		  	}
		  }
	});

	/************ END SCROLL **************/
	
	$('.counter').each(function() {
  var $this = $(this),
      countTo = $this.attr('data-count');
  $({ countNum: $this.text()}).animate({
    countNum: countTo
  },
  {
    duration: 2000,
    easing:'linear',
    step: function() {
      $this.text(Math.floor(this.countNum));
    },
    complete: function() {
      $this.text(this.countNum);
      //alert('finished');
    }
  });
});

})(jQuery);