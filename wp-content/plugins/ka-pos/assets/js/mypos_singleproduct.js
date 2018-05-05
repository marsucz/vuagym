jQuery(document).ready(function($) {
    
    $('.product-detail-content').on('click', '.close', function() {
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
                
                if (response.data.status == 2) {
                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-checkmark xoo-wsc-icon-atc');
                } else {
                    atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-cross xoo-wsc-icon-atc');
                }
                
                $('.mobileHide .row.product-header').prepend(response.data.alert);
                $('.mobileShow .row.product-header').prepend(response.data.alert);
                $('#main-content').append(response.data.popup);
                
                $('.alert-box').fadeIn();
                $('#addToCartModal').modal('show');
                
                console.log(response.data.fragments);
                
                if (response.data.fragments !== '') {
                    var cart_content = response.data.fragments['widget_shopping_cart_content'];
                    $('.mini-cart-main-content').html(cart_content);
                    $('.widget_shopping_cart_content').html(cart_content);
                    var count_item = cart_content.split("<li").length;
                    $('.cart-item-count').html(count_item-1);
                    var price = $('.content-mini-cart').find('.mini-cart-total').find('.total-price').html();
                    $('.total-mini-cart-price').html(price);
                }
            },
            error: function(response) {
                atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-checkmark xoo-wsc-icon-atc');
                console.log(response);
            }
        });
        
    }
    
    $('.single_add_to_cart_button').click(function(e) {
        e.preventDefault();
        
        // Effect
        var atc_btn  = $('.single_add_to_cart_button');
        
        if (atc_btn.hasClass("disabled")) {
            return;
        }
        
        if(atc_btn.find('.xoo-wsc-icon-spinner').length !== 0){
            return;
        }
        
        if(atc_btn.find('.xoo-wsc-icon-atc').length !== 0){
            atc_btn.find('.xoo-wsc-icon-atc').attr('class','xoo-wsc-icon-spinner xoo-wsc-icon-atc xoo-wsc-active');
        } else {
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

