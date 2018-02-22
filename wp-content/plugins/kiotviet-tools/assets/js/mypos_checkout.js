/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

jQuery(document).ready(function($) {
    
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
    
});

