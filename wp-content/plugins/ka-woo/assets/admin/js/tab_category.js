
function setCategories(product_id) {
        
        $('#setCategories_' + product_id).prop('disabled', true);
        
        $.post(
        global.ajax, 
        {   
            product_id: product_id,
            action: 'kawoo_set_categories' 
        }, 
        function(data) {
            console.log(data);
            $('#setCategories_' + product_id).html('<i class="fa fa-check"></i>  Done');
        });
};

jQuery(document).ready(function($) {
    
    $(window).keydown(function(event){
    if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
    });
    
    console.log("JS Loaded");
    
    $('#kawoo_selected_categories').attr('multiple','multiple');
    $('#kawoo_selected_categories').select2({
        placeholder: 'Chọn danh mục ...'
    });
    
    $('#kawoo_selected_categories').val('').trigger('change');
    
});

