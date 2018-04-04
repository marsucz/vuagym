
jQuery(document).ready(function($) {
    
    $(window).keydown(function(event){
    if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
    });
    
    show_type_change();
    $('#kawoo_show_type').on('change', function() {
        show_type_change();
    });
    
    function show_type_change() {
        if ($('#kawoo_show_type').val() == 1) {
            $('#kawoo_finding_code_text').show();
            $('#kawoo_finding_code_label').show();
            $("#kawoo_finding_code_text").prop('required',true);
            
            $('#kawoo_product_numbers_label').hide();
            $('#kawoo_number_of_products').hide();
            $("#kawoo_number_of_products").prop('required',false);
            
        } else {
            $('#kawoo_finding_code_text').hide();
            $('#kawoo_finding_code_label').hide();
            $("#kawoo_finding_code_text").prop('required',false);
            
            $('#kawoo_product_numbers_label').show();
            $('#kawoo_number_of_products').show();
            $("#kawoo_number_of_products").prop('required',true);
            
        }
    }
});

