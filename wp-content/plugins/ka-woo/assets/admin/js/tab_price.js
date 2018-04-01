
function set_regular_to_sale_price(product_id) {
        
//        $('#setSalePrice_' + product_id).prop('disabled', true);
        
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {
                    action: 'kawoo_get_set_price_popup',
                    product_id: product_id
            },
            success: function(response){
                console.log(response);
                $('#wpcontent').append(response.data);
                $('#setPriceModal').modal('show');
            }
        });
        
};

jQuery(document).ready(function($) {
    
    console.log("JS Loaded");
    
    $(window).keydown(function(event){
    if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
    });
    
    
});

