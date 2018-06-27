
jQuery(document).ready(function($) {
    
    show_type_change();
    $('#_ka_shoppe_type').on('change', function() {
        show_type_change();
    });
    
    function show_type_change() {
        if ($('#_ka_shoppe_type').val() == 'link') {
            $('#wp-_ka_shoppe_content-wrap').hide();
            $('#_ka_shoppe_link').show();
        } else {
            $('#wp-_ka_shoppe_content-wrap').show();
            $('#_ka_shoppe_link').hide();
        }
    }
});

