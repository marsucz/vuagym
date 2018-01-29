jQuery(document).ready(function($){

    //show more/less


    $('.ywtm_description.show_more').each(function(){

        var content = $(this).html(),
            max_char = $(this).data('max_char'),
            show_more_txt= $(this).data('more_text');
        if(content.length > max_char) {


            var c = content.substr(0, max_char);
            var h = content.substr(max_char, content.length - max_char);

            var html = c + '<span class="ywtm_morecontent"><span style="display: none;">' + h + '</span>&nbsp;&nbsp;<a href="" class="ywtm_morelink">' + show_more_txt + '</a></span>';

            $(this).html(html);
        }
    });

    $(".ywtm_morelink").click(function(){

        var parent = $(this).parents('.ywtm_description'),
            show_more_txt=  parent.data('more_text'),
            show_less_txt = parent.data('less_text');

        if($(this).hasClass("less")) {
            $(this).removeClass("less");
            $(this).html(show_more_txt);
        } else {
            $(this).addClass("less");
            $(this).html(show_less_txt);
        }
        $(this).parent().prev().toggle('slow');
        $(this).prev().toggle();
        return false;
    });
});