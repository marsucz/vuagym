/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
    
//    $('#place_order').click(function(e) {
    $('.woocommerce-checkout').on('click', '#place_order', function(e) {
       e.preventDefault();
       
       console.log("Checking quantity on KiotViet...");
       
       if ($('#checkoutModal').length) {
           $('#checkoutModal').modal('show');
       } else {
           
           var $form = $('form[name=checkout]');
           
            var form_data = $form.data();
            
            if ( $form.is( '.processing' ) ) {
                return false;
            }
            
            $form.addClass( 'processing' );
            
            if ( 1 !== form_data['blockUI.isBlocked'] ) {
                    $form.block({
                            message: null,
                            overlayCSS: {
                                    background: '#fff',
                                    opacity: 0.6
                            }
                    });
            }
           
            $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {
                    action: 'check_quantity_checkout',
            },
            success: function(response){
               console.log(response.data.message);
               
               $form.removeClass( 'processing' ).unblock();
               
               if (response.data.status === false) {
                   $('#main-content').append(response.data.message);
                   $('#checkoutModal').modal('show');
               } else {
                   $form.submit();
               }
            }
        });
       }
       
    });
    
    $('.product-detail-content').on('click', '#alert-box', function() {
        $('#alert-box').fadeOut(500, function() { $('#alert-box').remove(); });
    });
    
    
    function remove_add_to_cart_items() {
        if ($('#alert-box').length) {
            $('#alert-box').fadeOut(500, function() { $('#alert-box').remove(); });
        }
        if ($('#addToCartModal').length) {
            $('#addToCartModal').remove();
            $('.modal-backdrop').remove();
        }
    }
    
    function check_quantity_on_kiotviet(item_id, quantity) {
        
        var result = false;

        remove_add_to_cart_items();
        
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {
                    action: 'check_quantity_cart',
                    item_id: item_id,
                    quantity: quantity
            },
            success: function(response){
                
                remove_add_to_cart_items();
                
                console.log(response);
                
                var atc_btn  = $('.single_add_to_cart_button');
                if (response.data.status == 2) {
                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-cross xoo-wsc-icon-atc');
                } else {
                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-checkmark xoo-wsc-icon-atc');
                }
                
                $('.mobileHide .row.product-header').prepend(response.data.alert);
                $('#main-content').append(response.data.popup);
                
//                atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-cross xoo-wsc-icon-atc');
                $('#alert-box').fadeIn();
                $('#addToCartModal').modal('show');
                
                
                
                
                
//                if (response.data.status == 1) {
//                    result = true;
//                } else {
//                    $('.mobileHide .row.product-header').prepend(response.data.alert);
//                    $('#main-content').append(response.data.popup);
//                    result = false;
//                }
//                
//                var atc_btn  = $('.single_add_to_cart_button');
//                if (result === true) {
//                    add_to_cart(atc_btn,item_id,quantity);//Ajax add to cart
////                    $('#alert-box').fadeOut();
//                } else {
//                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-cross xoo-wsc-icon-atc');
//                    $('#alert-box').fadeIn();
//                    $('#addToCartModal').modal('show');
//                }
            },
            error: function(response) {
//                var error_string = "<span class='alert-message'>Có lỗi phát sinh trong quá trình thêm sản phẩm. Bạn vui lòng thử lại.</span>";
//                $('#alert-message').html(error_string);
//                $('#alert-max-quantity').html('');
//                $('#alert-box').fadeIn();
//                $('.modal-body').append(error_string);
//                $('#addToCartModal').modal('show');
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
            data: {action: 'mypos_add_to_cart',
                       item_id: item_id,
                       quantity: quantity},
            success: function(response,status,jqXHR){
                
                    console.log(response);
                
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
        
        if(atc_btn.find('.xoo-wsc-icon-spinner').length !== 0){
            console.log("Processing. Please Wait...");
            return;
        }
        
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

