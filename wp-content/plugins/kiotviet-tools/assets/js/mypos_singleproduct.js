/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
    
    $('.product-detail-content').on('click', '.alert-box', function() {
        $('.alert-box').fadeOut(500, function() { $('.alert-box').remove(); });
    });
    
    
    function remove_add_to_cart_items() {
        if ($('.alert-box').length) {
            $('.alert-box').fadeOut(500, function() { $('.alert-box').remove(); });
        }
        if ($('#addToCartModal').length) {
            $('#addToCartModal').remove();
            $('.modal-backdrop').remove();
        }
    }
    
    function check_quantity_on_kiotviet(item_id, quantity) {
        
        var result = false;

        remove_add_to_cart_items();
        
        var atc_btn  = $('.single_add_to_cart_button');
        
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
                
                if (response.data.status == 2) {
                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-checkmark xoo-wsc-icon-atc');
                } else {
                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-cross xoo-wsc-icon-atc');
                }
                
                $('.mobileHide .row.product-header').prepend(response.data.alert);
                $('.mobileShow .row.product-header').prepend(response.data.alert);
                $('#main-content').append(response.data.popup);
                
//                atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-cross xoo-wsc-icon-atc');
                $('.alert-box').fadeIn();
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
////                    $('.alert-box').fadeOut();
//                } else {
//                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-cross xoo-wsc-icon-atc');
//                    $('.alert-box').fadeIn();
//                    $('#addToCartModal').modal('show');
//                }
            },
            error: function(response) {
                atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-checkmark xoo-wsc-icon-atc');
                console.log(response);
            }
        });
        
    }
    
    //Add to cart function
//    function add_to_cart(atc_btn,item_id,quantity){
//        $.ajax({
//            url: global.ajax,
//            type: 'POST',
//            data: {action: 'mypos_add_to_cart',
//                       item_id: item_id,
//                       quantity: quantity},
//            success: function(response,status,jqXHR){
//                
//                    console.log(response);
//                
//                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-checkmark xoo-wsc-icon-atc');
//                    toggle_sidecart();
//                    on_cart_success(response);
//            }
//        })
//    }

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
    
});

