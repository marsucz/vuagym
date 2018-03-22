jQuery(document).ready(function($){

    // button text
    var update_button_text = $('input[name=update_cart]').val();
    var checkout_button_text = $('.checkout-button').html();
    
    $('.main-content').on('change', '.qty', function(){
        
        if ($('#updateCartModal').length) {
            $('#updateCartModal').remove();
        }
        
        var $form = $( '.woocommerce-cart-form' );
        var input_element = $(this);
        var matches = $(this).attr('name').match(/cart\[(\w+)\]/);
        var cart_item_key = matches[1];
        
        updateButton = $("input[name='update_cart']");
        updateButton
                    .addClass('disabled')
                    .val( 'Đang cập nhật...' );
//        
        $("a.checkout-button.wc-forward")
                .addClass('disabled')
                .html( 'Đang cập nhật...' );

        var max_qty = input_element.attr('max_quantity');
        var current_qty = input_element.attr('current_quantity');
        
        var do_update_quantity = true;
        
       if (typeof max_qty !== typeof undefined && max_qty !== false) {
       } else {
           max_qty = -1;
       }
        
        if (do_update_quantity === true) {
            
            block( $form );
            block( $( 'div.cart_totals' ) );
            $('.qty').prop('disabled', true);
            
            $.ajax({
                url: global.ajax,
                type: 'POST',
                data: {action: 'mypos_update_cart',
                           cart_item_key: cart_item_key,
                           quantity: input_element.val(),
                           max_quantity: max_qty
                       },
                success: function(response){
                    
                    unblock( $form );
                    unblock( $( 'div.cart_totals' ) );
                    $('.qty').prop('disabled', false);
                    
                    $("a.checkout-button.wc-forward")
                                            .removeClass('disabled')
                                             .html(checkout_button_text);
                    $("input[name='update_cart']")
                                            .removeClass('disabled')
                                                    .val(update_button_text);

                    input_element.attr('max_quantity', response.data.max_quantity);
                    
                    if (response.data.status === false) {
                        $('#main-content').append(response.data.alert);
                        $('#updateCartModal').modal('show');
                        input_element.val(response.data.current_quantity);
                        input_element.attr('current_quantity', response.data.current_quantity);
                    } else {
                        input_element.attr('current_quantity', response.data.new_quantity);
                        input_element.closest('.cart_item').find('.product-subtotal').html(response.data.item_totalprice);
                        $('.cart-subtotal').find('td').html(response.data.cart_subtotal);
                        $('.order-total').find('strong').html(response.data.cart_total);
                    }
                },
                error: function(res) {
                    unblock( $form );
                    unblock( $( 'div.cart_totals' ) );
                    $('.qty').prop('disabled', false);
                    console.log('Check quantity error!');
                    console.log(res);
                }
            })
        } 
    });
    
    var CurrentInput = null;
    var updateButton = $("input[name='update_cart']");
    
    $('.main-content').on('click', '.qty', function(){
        if (CurrentInput !== null) {
            CurrentInput.change();
            CurrentInput = null;
        }
    });
    
    $('.main-content').on('click','.qty-up',function(e){
        e.preventDefault();

        if (updateButton.prop('disabled')) {
            updateButton.prop('disabled', false);
        }
//        updateButton = $("input[name='update_cart']");
//        if (updateButton.hasClass('disabled')) {
//            return false;
//        } else 
        {
            inputQty = $(this).parent().parent().parent().find('.qty');
            
            if (CurrentInput === null) {
                CurrentInput = inputQty;
            }
            
            if (!CurrentInput.is(inputQty)) {
                CurrentInput.change();
                CurrentInput = null;
                return false;
            }
            
            inputQty.val( function(i, oldval) { return ++oldval; });
            
            var save_value = inputQty.val();
            
            setTimeout(function(){
                if (inputQty.val() == save_value) {
                    inputQty.change();
                    CurrentInput = null;
                } else {
                    return false;
                }
            }, 800);
        }   
        return false;
    });
    
    $('.main-content').on('click','.qty-down', function(e){
        e.preventDefault();
        
        if (updateButton.prop('disabled')) {
            updateButton.prop('disabled', false);
        }
        
//        updateButton = $("input[name='update_cart']");
//        if (updateButton.hasClass('disabled')) {
//            return false;
//        } else 
        {
            inputQty = $(this).parent().parent().parent().find('.qty');
            
            if (CurrentInput === null) {
                CurrentInput = inputQty;
            }
            
            if (!CurrentInput.is(inputQty)) {
                CurrentInput.change();
                CurrentInput = null;
                return false;
            }
            
            inputQty.val( function(i, oldval) { return oldval > 0 ? --oldval : 0; });
            var save_value = inputQty.val();
            
            setTimeout(function(){
                if (inputQty.val() == save_value) {
                    CurrentInput = null;
                    inputQty.change();
                } else {
                    return false;
                }
            }, 800);
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