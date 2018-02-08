jQuery(document).ready(function($){

    // button text
    var update_button_text = $('input[name=update_cart]').val();
    var checkout_button_text = $('.checkout-button').html();
    
    $('.qty').on('change', function(){
        
        var $form = $( '.woocommerce-cart-form' );

        block( $form );
        block( $( 'div.cart_totals' ) );

        console.log('Qty changed');
        
        var input_element = $(this);
        
        var matches = $(this).attr('name').match(/cart\[(\w+)\]/);
        var cart_item_key = matches[1];
        
//        updateButton = $("input[name='update_cart']");
//        updateButton.addClass('disabled')
//                    .val( 'Đang cập nhật...' );
////        
//        $("a.checkout-button.wc-forward").addClass('disabled')
//                                         .html( 'Đang cập nhật...' );

        var max_qty = input_element.attr('max_quantity');
        var current_qty = input_element.attr('current_quantity');
        
        var do_update_quantity = false;
        
        if (typeof max_qty !== typeof undefined && max_qty !== false) {
            if (input_element.val() <= max_qty) {
                do_update_quantity = true;
            } else {
                if ($('#updateCartModal').length) {
                    input_element.val(current_qty);
                    var product_name = input_element.closest('.cart_item').find('.product-name').children().html();
                    // show pupop and don't do ajax
                    $('.modal-body').html('');
                    $('.modal-body').append('<span class="alert-message"><b>' + product_name + '</b> chỉ cho phép đặt tối đa <b>' + max_qty + ' sản phẩm</b>. <br/> Bạn đã có <b>' + current_qty + ' sản phẩm</b> này trong giỏ hàng.</span>');
                    $('#updateCartModal').modal('show');
                } else {
                    do_update_quantity = true;
                }
            }
        } else {
            do_update_quantity = true;
            max_qty = -1;
        }
        
        if (do_update_quantity === true) {
            
            if ($('#updateCartModal').length) {
                $('#updateCartModal').remove();
            }
            
            $.ajax({
                url: global.ajax,
                type: 'POST',
                data: {action: 'mypos_update_cart',
                           cart_item_key: cart_item_key,
                           quantity: input_element.val(),
                           max_quantity: max_qty
                       },
                success: function(response,status,jqXHR){
                    
                    unblock( $form );
                    unblock( $( 'div.cart_totals' ) );

//                    $("a.checkout-button.wc-forward").removeClass('disabled')
//                                             .html(checkout_button_text);
//                    $("input[name='update_cart']").removeClass('disabled')
//                                                    .val(update_button_text);

                    console.log(response);

                    input_element.attr('max_quantity', response.data.max_quantity);
                    
                    if (response.data.status === false) {
                        $('#main-content').append(response.data.alert);
                        $('#updateCartModal').modal('show');
                        input_element.val(response.data.current_quantity);
                        
                        input_element.attr('current_quantity', response.data.current_quantity);
                        
                    } else {
                        input_element.attr('current_quantity', response.data.new_quantity);
                    }

                }
            })
        } else {
            $("a.checkout-button.wc-forward").removeClass('disabled')
                                             .html(checkout_button_text);

//                $('input[name=update_cart]').prop('disabled', false);
            $("input[name='update_cart']").removeClass('disabled')
                                                .val(update_button_text);
        }
        
    });
    
    $('.product-quantity').on('click','.qty-up',function(e){
        console.log('Up');
        
//        updateButton = $("input[name='update_cart']");
//        if (updateButton.hasClass('disabled')) {
//            return false;
//        } else 
        {
            inputQty = $(this).parent().parent().parent().find('.qty');
            inputQty.val( function(i, oldval) { return ++oldval; });
//            inputQty.focus();
            
            var save_value = inputQty.val();
            
            setTimeout(function(){
                if (inputQty.val() == save_value) {
                    inputQty.change();
                } else {
                    return false;
                }
            }, 1000);
        }
        return false;
    });

    $('.product-quantity').on('click','.qty-down', function(e){
        console.log('Down');
//        updateButton = $("input[name='update_cart']");
//        if (updateButton.hasClass('disabled')) {
//            return false;
//        } else 
        {
            inputQty = $(this).parent().parent().parent().find('.qty');
            inputQty.val( function(i, oldval) { return oldval > 0 ? --oldval : 0; });
            var save_value = inputQty.val();
            
            setTimeout(function(){
                if (inputQty.val() == save_value) {
                    inputQty.change();
                } else {
                    return false;
                }
            }, 1000);
        }
        return false;
    });
    
    /**
     * Check if a node is blocked for processing.
     *
     * @param {JQuery Object} $node
     * @return {bool} True if the DOM Element is UI Blocked, false if not.
     */
    var is_blocked = function( $node ) {
            return $node.is( '.processing' ) || $node.parents( '.processing' ).length;
    };

    /**
     * Block a node visually for processing.
     *
     * @param {JQuery Object} $node
     */
    var block = function( $node ) {
            if ( ! is_blocked( $node ) ) {
                    $node.addClass( 'processing' ).block( {
                            message: null,
                            overlayCSS: {
                                    background: '#fff',
                                    opacity: 0.6
                            }
                    } );
            }
    };

    /**
     * Unblock a node after processing is complete.
     *
     * @param {JQuery Object} $node
     */
    var unblock = function( $node ) {
            $node.removeClass( 'processing' ).unblock();
    };
    
});
