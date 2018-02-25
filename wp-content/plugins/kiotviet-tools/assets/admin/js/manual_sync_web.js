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
//            $('#updateInStock_' + product_id).prop('disabled', true);
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
//            $('#updateOutOfStock_' + product_id).prop('disabled', true);
            $('#updateOutOfStock_' + product_id).removeClass('btn-danger');
            $('#updateOutOfStock_' + product_id).addClass('btn-success');
            $('#updateOutOfStock_' + product_id).html('<i class="fa fa-check"></i>  Done');
        });
};

function updateWebPrice_byKVPrice(product_id, price) {
        
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
};

function updateKVPrice_byWebPrice(product_id, price) {
        
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
};

jQuery(document).ready(function($) {
    
    $(window).keydown(function(event){
    if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
    });
    
});

