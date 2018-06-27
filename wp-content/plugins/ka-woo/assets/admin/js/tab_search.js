
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
        switch (parseInt($('#kawoo_show_type').val())) {
            case 1:
                $('#kawoo_finding_code_text').show();
                $('#kawoo_finding_code_label').show();
                $("#kawoo_finding_code_text").prop('required',true);

                $('#kawoo_product_numbers_label').hide();
                $('#kawoo_number_of_products').hide();
                $("#kawoo_number_of_products").prop('required',false);

                $('.search_advance').show();
                $('.shoppe_advance').hide();
                break;
            case 2:
            case 3:
                $('#kawoo_finding_code_text').hide();
                $('#kawoo_finding_code_label').hide();
                $("#kawoo_finding_code_text").prop('required',false);

                $('#kawoo_product_numbers_label').show();
                $('#kawoo_number_of_products').show();
                $("#kawoo_number_of_products").prop('required',true);

                $('.search_advance').hide();
                $('.shoppe_advance').hide();
                break;
            case 4:
                $('#kawoo_finding_code_text').hide();
                $('#kawoo_finding_code_label').hide();
                $("#kawoo_finding_code_text").prop('required',false);

                $('#kawoo_product_numbers_label').show();
                $('#kawoo_number_of_products').show();
                $("#kawoo_number_of_products").prop('required',true);

                $('.search_advance').hide();
                $('.shoppe_advance').show();
                break;
        }
    }
});

