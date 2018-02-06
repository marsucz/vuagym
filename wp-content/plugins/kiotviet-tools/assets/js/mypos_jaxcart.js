jQuery(document).ready(function($){

    $('.qty').on('change', function(){
        
        $('input[name=update_cart]').prop('disabled', true);
        $('.checkout-button').prop('disabled', true);
        
        console.log('Qty changed');
        
        if ($('#updateCartModal').length) {
           $('#updateCartModal').remove();
        }
        
        var input_element = $(this);
        var matches = $(this).attr('name').match(/cart\[(\w+)\]/);
        var cart_item_key = matches[1];
        
        // button text
        var update_button_text = $('input[name=update_cart]').val();
        var checkout_button_text = $('.checkout-button').html();
        
        $("input[name='update_cart']").val('Đang cập nhật...').prop('disabled', true);
        
        $('.checkout-button').html('Đang cập nhật...');
        
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {action: 'mypos_update_cart',
                       cart_item_key: cart_item_key,
                       quantity: input_element.val()},
            success: function(response,status,jqXHR){
                
//                $('input[name=update_cart]').val(update_button_text);
                $('.checkout-button').html(checkout_button_text);
                
//                $('input[name=update_cart]').prop('disabled', false);
                $("input[name='update_cart']").val(update_button_text).prop('disabled', false);
                $('.checkout-button').prop('disabled', false);
                
                console.log(response);
                if (response.data.status === false) {
                    $('#main-content').append(response.data.alert);
                    $('#updateCartModal').modal('show');
                    input_element.val(response.data.current_quantity);
                } else {
                }
                
            }
        })
        
    });
    
    $('.product-quantity').on('click','.qty-up',function(e){
        console.log('Up');
        inputQty = $(this).parent().parent().parent().find('.qty');
        inputQty.val( function(i, oldval) { return ++oldval; });
        inputQty.change();
        return false;
    });

    $('.product-quantity').on('click','.qty-down', function(e){
        console.log('Down');
        inputQty = $(this).parent().parent().parent().find('.qty');
        inputQty.val( function(i, oldval) { return oldval > 0 ? --oldval : 0; });
        inputQty.change();
        return false;
    });
    
});
