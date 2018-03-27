
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

