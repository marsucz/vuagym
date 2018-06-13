 /* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function updateInStock(product_id) {
        
        $('#updateInStock_' + product_id).prop('disabled', true);
        
        $.post(
        global.ajax, 
        {   
            product_id: product_id,
            action: 'mypos_update_product_instock' 
        }, 
        function(data) {
            console.log(data);
            $('#updateInStock_' + product_id).html('<i class="fa fa-check"></i>  Done');
        });
};

function updateOutOfStock(product_id) {
        
        $('#updateOutOfStock_' + product_id).prop('disabled', true);
        
        $.post(
        global.ajax, 
        {   
            product_id: product_id,
            action: 'mypos_update_product_outofstock' 
        }, 
        function(data) {
            console.log(data);
            $('#updateOutOfStock_' + product_id).html('<i class="fa fa-check"></i>  Done');
        });
};

function enableProduct(product_id) {
        
        $('#enableProduct_' + product_id).prop('disabled', true);
        
        $.post(
        global.ajax, 
        {   
            product_id: product_id,
            action: 'mypos_update_product_enable' 
        }, 
        function(data) {
            console.log(data);
            $('#enableProduct_' + product_id).html('<i class="fa fa-check"></i>  Done');
        });
};

function setPreOrder(product_id) {
        
        $('#setPreOrder_' + product_id).prop('disabled', true);
        
        $.post(
        global.ajax, 
        {   
            product_id: product_id,
            action: 'mypos_set_pre_order' 
        }, 
        function(data) {
            console.log(data);
            $('#setPreOrder_' + product_id).html('<i class="fa fa-check"></i>  Done');
        });
};


function updateWebPrice_byKVPrice(product_id, price, confirm_text) {
        
        if (confirm_text === undefined) {
            confirm_text = 'Bạn có muốn cập nhật giá mới (' + price + ') cho sản phẩm này không?';
        }
        
        var r = confirm(confirm_text);
        
        if (r == true) {
            $('#updateWebPrice_' + product_id).prop('disabled', true);

            $.post(
            global.ajax, 
            {   
                product_id: product_id,
                price: price,
                action: 'mypos_update_webprice_by_kvprice' 
            }, 
            function(data) {
                console.log(data);
    //            $('#updateOutOfStock_' + product_id).prop('disabled', true);
                $('#updateWebPrice_' + product_id).html('<i class="fa fa-check"></i>  Done');
            });
        } 
};

function updateKVPrice_byWebPrice(product_id, price, confirm_text) {
    
        if (confirm_text === undefined) {
            confirm_text = 'Bạn có muốn cập nhật giá mới (' + price + ') cho sản phẩm này không?';
        }
        
        var r = confirm(confirm_text);
        
        if (r == true) {
        
            $('#updateKVPrice_' + product_id).prop('disabled', true);

            $.post(
            global.ajax, 
            {   
                product_id: product_id,
                price: price,
                action: 'mypos_update_kvprice_by_webprice' 
            }, 
            function(data) {
                console.log(data);
    //            $('#updateOutOfStock_' + product_id).prop('disabled', true);
                $('#updateKVPrice_' + product_id).html('<i class="fa fa-check"></i>  Done');
            });
        }
};

jQuery(document).ready(function($) {
    
    show_type_change();
    $('#sync_web_type').on('change', function() {
        show_type_change();
        var sync_web_type = $("#sync_web_type").val();
        if (sync_web_type == 1) {
            $("#sync_by_web_show_type").val(0);
        } else {
            $("#sync_by_web_show_type").val(3);
        }
    });
    
    function show_type_change() {
        $("#sync_by_web_show_type option").each(function() {
            var sync_web_type = $("#sync_web_type").val();
           if (sync_web_type == 1) {
               if ($(this).val() <= 2) {
                   $(this).show();
               } else {
                   $(this).hide();
               }
           } else {
               if ($(this).val() <= 2) {
                   $(this).hide();
               } else {
                   $(this).show();
               }
           }
        });
    }
    
    $('#wpcontent').on('submit', 'form.set-name-form', function(e){
        e.preventDefault();
        $('#setPriceAlert').remove();
        
        var product_id = $("#product_id").val();
        var new_productname = $('#new_productname').val();
        
        if (!new_productname || !product_id) {
            $('.modal-body').prepend('<div id="setPriceAlert" class="alert alert-danger">Tên sản phẩm không được để trống.</div>');
            return;
        }
        
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {
                    action: 'kapos_set_product_name',
                    product_id: product_id,
                    new_name: new_productname
            },
            success: function(response){
                console.log(response);
                $('#get_rename_popup_' + product_id).prop('disabled', true);
                $('#get_rename_popup_' + product_id).html('<i class="fa fa-check"></i>  Done');
                $('#get_rename_popup_' + product_id).removeClass('btn-danger');
                $('#get_rename_popup_' + product_id).addClass('btn-success');
                $('#setNameModal').modal('hide');
                $('.modal-backdrop').remove();
            }
        });
    });
    
    
});


function get_rename_popup(product_id, kv_name) {
        
        if ($('#setNameModal').length) {
            $('#setNameModal').remove();
            $('.modal-backdrop').remove();
        }
        
        $.ajax({
            url: global.ajax,
            type: 'POST',
            data: {
                    action: 'kapos_get_rename_popup',
                    product_id: product_id,
                    kv_name: kv_name
            },
            success: function(response){
                console.log(response);
                $('#wpcontent').append($(response.data));
                $('#setNameModal').modal('show');
            }
        });
};