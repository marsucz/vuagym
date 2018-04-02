
function get_price_popup(product_id) {
        
        if ($('#setPriceModal').length) {
            $('#setPriceModal').remove();
            $('.modal-backdrop').remove();
        }
        
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {
                    action: 'kawoo_get_price_popup',
                    product_id: product_id
            },
            success: function(response){
                console.log(response);
                $('#wpcontent').append($(response.data));
                $('#setPriceModal').modal('show');
            }
        });
};

jQuery(document).ready(function($) {
    
    $(window).keydown(function(event){
    if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
    });
    
    $('#wpcontent').on('submit', 'form.set-price-form', function(e){
        e.preventDefault();
        $('#setPriceAlert').remove();
        
        var product_id = $("#product_id").val();
        var new_regular = parseInt($('#new_regular').val());
        var new_sale = parseInt($('#new_sale').val());
        
        if (new_regular <= new_sale) {
            $('.modal-body').prepend('<div id="setPriceAlert" class="alert alert-danger">Giá gốc phải lớn hơn giá sale.</div>');
            return;
        }
        
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {
                    action: 'kawoo_set_prices',
                    product_id: product_id,
                    new_regular: new_regular,
                    new_sale: new_sale
            },
            success: function(response){
                console.log(response);
                $('#get_price_popup_' + product_id).prop('disabled', true);
                $('#get_price_popup_' + product_id).html('<i class="fa fa-check"></i>  Done');
                $('#get_price_popup_' + product_id).removeClass('btn-danger');
                $('#get_price_popup_' + product_id).addClass('btn-success');
                $('#setPriceModal').modal('hide');
                $('.modal-backdrop').remove();
            }
        });
    });
});

