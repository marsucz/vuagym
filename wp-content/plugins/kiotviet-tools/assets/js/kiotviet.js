/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
    
    $('#hide-alert').click(function() {
        $('#alert-box').fadeOut();
    });
    
//    var validator = $('form.checkout').validate({
//        rules: {
//            quantity: {
//                required: true,
//                max: 9999,
//                checkQuantity: true,
//            },
//            messages: {
//                quantity: {
//                    required: 'khong trong'
//                }
//            }
//        },
//         errorPlacement: function(error) {
//         },
//         success: function(label){
//             $('#alert-box').hide();
//             console.log("DONE");
//         },
//        onkeyup: false,
//        onclick: false,
//        onfocusout: false
//    });
    
    function check_quantity_on_kiotviet(item_id, quantity) {
        
        var result = false;
        
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {
                    action: 'check_quantity_cart',
                    item_id: item_id,
                    quantity: quantity
            },
            success: function(response){
                console.log(response);
                if (response.data.status == 1) {
                    $('#alert-message').html('');
                    $('#alert-max-quantity').html('');
                    result = true;
                } else {
                    if (response.data.max_quantity == 0) {
                        $('#alert-message').html('Sản phẩm bạn đặt hiện đang hết hàng. Mong bạn quay lại sau.');
                        $('#alert-max-quantity').html('');
                    } else {
                        $('#alert-message').html('Số lượng bạn đặt vượt quá giới hạn kho hàng. Tối đa: ');
                        $('#alert-max-quantity').html(response.data.new_quantity + "/" + response.data.max_quantity);
                    }
                    result = false;
                }
                
                var atc_btn  = $('.single_add_to_cart_button');
                if (result === true) {
                    add_to_cart(atc_btn,item_id,quantity);//Ajax add to cart
                    $('#alert-box').fadeOut();
                } else {
                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-cross xoo-wsc-icon-atc');
                    $('#alert-box').fadeIn();
                }
            },
            error: function(response) {
                $('#alert-message').html('Có lỗi phát sinh trong quá trình thêm sản phẩm. Bạn vui lòng thử lại.');
                $('#alert-max-quantity').html('');
                $('#alert-box').fadeIn();
                console.log(response);
                result = false;
            }
        });
        
    }
    
    //Add to cart function
    function add_to_cart(atc_btn,item_id,quantity){
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {action: 'add_to_cart',
                       item_id: item_id,
                       quantity: quantity},
            success: function(response,status,jqXHR){
                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-checkmark xoo-wsc-icon-atc');
                    toggle_sidecart();
                    on_cart_success(response);
            }
        })
    }

    $('.single_add_to_cart_button').click(function(e) {
        e.preventDefault();
        
        // Effect
        var atc_btn  = $('.single_add_to_cart_button');
        if(atc_btn.find('.xoo-wsc-icon-atc').length !== 0){
            atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-spinner xoo-wsc-icon-atc xoo-wsc-active');
        }
        else{
            atc_btn.append('<span class="xoo-wsc-icon-spinner xoo-wsc-icon-atc xoo-wsc-active"></span>');
        }
        
        // Check
        var is_variation = $('[name=variation_id]');
        
        if(is_variation.length > 0){
            var item_id = parseInt($('[name=variation_id]').val());
        }
        else{
            var item_id = parseInt($('[name=add-to-cart]').val());
        }

        var quantity = parseInt($('input[name=quantity]').val());
        
        check_quantity_on_kiotviet(item_id, quantity);
        
    });
    
        //Toggle Side Cart
    function toggle_sidecart(){
        $('.xoo-wsc-modal , body').toggleClass('xoo-wsc-active');
    }
    
    function on_cart_success(response){
        $('.xoo-wsc-content').html(response.cart_markup);
        $('.xoo-wsc-items-count').html(response.items_count);
        content_height();
        refresh_ajax_fragm(response.ajax_fragm);
    }
    
    //Refresh ajax fragments
    function refresh_ajax_fragm(ajax_fragm){
            var fragments = ajax_fragm.fragments;
            var cart_hash = ajax_fragm.cart_hash;
            var cart_html = ajax_fragm.fragments["div.widget_shopping_cart_content"];
            $('.woofc-trigger').css('transform','scale(1)');
            $('.shopping-cart-inner').html(cart_html);
            var cart_count = $('.cart_list:first').find('li').length;
            $('.shopping-cart span.counter , ul.woofc-count li').html(cart_count);
    }
    
    //Set Cart content height
    function content_height(){
        var header = $('.xoo-wsc-header').outerHeight(); 
        var footer = $('.xoo-wsc-footer').outerHeight();
        var screen = $(window).height();
        $('.xoo-wsc-body').outerHeight(screen-(header+footer));
    };
    
});

