jQuery(document).ready(function($){

    $('.qty').on('change', function(){
        
        console.log('TSET');
        
        var form = $(this).closest('form');
        
        // emulates button Update cart click
        $("<input type='hidden' name='update_cart' id='update_cart' value='1'>").appendTo(form);
        
        // plugin flag
        $("<input type='hidden' name='is_wac_ajax' id='is_wac_ajax' value='1'>").appendTo(form);

        var el_qty = $(this);
        var matches = $(this).attr('name').match(/cart\[(\w+)\]/);
        var cart_item_key = matches[1];
        form.append( $("<input type='hidden' name='cart_item_key' id='cart_item_key'>").val(cart_item_key) );

        // get the form data before disable button...
        var formData = form.serialize();
        
        $("input[name='update_cart']").val('Updating...').prop('disabled', true);

        $.post( form.attr('action'), formData, function(resp) {
                // ajax response
                $('.cart-collaterals').html(resp.html);
                
                el_qty.closest('.cart_item').find('.product-subtotal').html(resp.price);
                
                $('#update_cart').remove();
                $('#is_wac_ajax').remove();
                $('#cart_item_key').remove();
                
                $("input[name='update_cart']").val(resp.update_label).prop('disabled', false);
            },
            'json'
        );
    });
    
});
