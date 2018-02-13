 /* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function updateInStock(product_id) {
        
//        $('#updateInStock_' + product_id).prop('disabled', true);
        
        $.post(
        global.ajax, 
        {   
            product_id: product_id,
            action: 'mypos_update_product_instock' 
        }, 
        function(data) {
            
            console.log(data);
            
//            $('[id="' + ip + '_block"').hide();
//            $('[id="' + ip + '_block"').prop('disabled', false);
//            $('[id="' + ip + '_unblock"').show();
        });
};


jQuery(document).ready(function($) {
    
    $(window).keydown(function(event){
    if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
    });
    
    $('#dataTables-example').DataTable({
            responsive: true,
            "bDestroy": true,
            "autoWidth": false
    });
    
});

