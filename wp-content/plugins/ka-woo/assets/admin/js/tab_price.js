function isEmpty(obj) {
    for(var key in obj) {
        if(obj.hasOwnProperty(key))
            return false;
    }
    return true;
}

function get_bulk_popup() {
        
        if ($('#setBulkPriceModal').length) {
            $('#setBulkPriceModal').remove();
            $('.modal-backdrop').remove();
        }
        
        var products = [];
        $('#product-price-manager-list input:checked').each(function() {
            products.push(this.value);
        });
        
        if (isEmpty(products)) {
            alert("Bạn chưa chọn sản phẩm nào.");
        } else {
            $.ajax({
                url: global.ajax,
                type: 'POST',
                data: {
                        action: 'kawoo_get_bulk_price_popup',
                        list_product: products
                },
                success: function(response){
                    console.log(response);
                    $('#wpcontent').append($(response.data));
                    $('#setBulkPriceModal').modal('show');
                }
            });
        }
};

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
    
    $('#wpcontent').on('submit', 'form.set-bulk-price-form', function(e){
        e.preventDefault();
        
        $('#bulk-price-submit').prop('disabled', true);
        $('#bulk-price-submit').html('Đang xử lý...');
        
        var product_list = $("#product_list").val();
        var ty_gia = $('#ty_gia').val();
        var lam_tron = $('#lam_tron').val();
        
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {
                    action: 'kawoo_set_bulk_prices',
                    product_list: product_list,
                    ty_gia: ty_gia,
                    lam_tron: lam_tron
            },
            success: function(response){
                
                $('#bulk-price-submit').prop('disabled', false);
                $('#bulk-price-submit').html('Save changes');
                
                $.each(response.data, function (index, value) {
                    $('#checkbox' + value.id).prop('disabled', true);
                    $('#checkbox' + value.id).prop('checked', false);
                    $('#get_price_popup_' + value.id).prop('disabled', true);
                    $('#get_price_popup_' + value.id).html('<i class="fa fa-check"></i>  Đã cập nhật giá gốc = ' + value.format_sale);
                    $('#get_price_popup_' + value.id).removeClass('btn-danger');
                    $('#get_price_popup_' + value.id).addClass('btn-success');
                });
                
                $('#setBulkPriceModal').modal('hide');
                $('.modal-backdrop').remove();
            }
        });
    });
});

