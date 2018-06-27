
jQuery(document).ready(function($) {
    
    $(window).keydown(function(event){
    if(event.keyCode == 13) {
          event.preventDefault();
          return false;
        }
    });
    
    show_type_change();
    $('#_ka_shoppe_type').on('change', function() {
        show_type_change();
    });
    
    function show_type_change() {
        if ($('#_ka_shoppe_type').val() == 1) {
            $('#kawoo_image_link').hide();
            $('#image_link_label').hide();
            $("#kawoo_image_link").prop('required',false);
        } else {
            $('#kawoo_image_link').show();
            $('#image_link_label').show();
            $("#kawoo_image_link").prop('required',true);
        }
    }
});

