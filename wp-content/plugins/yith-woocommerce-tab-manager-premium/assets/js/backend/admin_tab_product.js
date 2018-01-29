/**
 * Created by Your Inspiration on 10/04/2015.
 */
jQuery(document).ready(function ($) {

    //Handle multi-dependencies
    function multi_dependencies_handler ( id, deps, values, first) {
        var result = true;

        for (var i = 0; i < deps.length; i++) {

            if( deps[i].substr( 0, 6 ) == ':radio' )
            {deps[i] = deps[i] + ':checked'; }

            var val = $( deps[i] ).val();

            if( $(deps[i]).attr('type') == 'checkbox'){
                var thisCheck = $(deps[i]);
                if ( thisCheck.is ( ':checked' ) ) {
                    val = 'yes';
                }
                else {
                    val = 'no';
                }
            }
            if( result && (val==values[i]) ){
                result=true;
            }
            else {
                result=false;
                break;
            }
        }

        if( !result ) {
            $( id + '-container' ).parent().hide();
        } else {
            $( id + '-container' ).parent().show();
        }
    }

    function isArray(myArray) {
        return myArray.constructor.toString().indexOf('Array') > -1;
    }


    $('select#_ywtm_tab_category').ajaxChosen({
        method: 		'GET',
        url: 		yith_tab_params.admin_url	,
        dataType: 		'json',
        afterTypeDelay: 100,
        minTermLength: 	3,
        data:		{
            action: 	yith_tab_params.actions.search_product_categories,
            security: 	yith_tab_params.security,
            default: 	'',
            plugin:     yith_tab_params.plugin
        }
    }, function (data) {

        var terms = {};

        $.each(data, function (i, val) {
            terms[i] = val;

        });

        return terms;
    });

    //metaboxes
    $('.metaboxes-tab [data-field]').each(function(){
        var t = $(this);

        var deps= t.data('dep').split(',');


        if(isArray(deps) && deps.length>1){
            var field = '#' + t.data('field');
            var values= t.data('value').split(',');
            multi_dependencies_handler( field, deps,values,true );
            for( var i=0; i<deps.length; i++){
                deps[i]= '#'+deps[i];
            }

            for( var i=0; i<deps.length; i++)
                $(deps[i]).on('change', function(){
                    multi_dependencies_handler( field, deps,values,false );
                }).change();
        }
    });



 $('#custom_check_map').on('click', function(){

   if( !$('#custom_check_map').is(":checked") ) {

       $('#custom_width_enable').css('display','block');
     }
     else
       $('#custom_width_enable').css('display','none');
 });

    $('.add_field_check').on('change', function(){


        var t= $(this),
            id_sub_cont= '#'+t.attr('id')+'_req';

        if(t.is(':checked')){

            $(id_sub_cont).show();
        }
        else
        {
            $(id_sub_cont).hide();
        }

    });


    /*Downloadable tab*/
    // Uploading files
    var downloadable_file_frame;
    var file_path_field;

    $(document).on( 'click', '.tab_upload_file_button', function( event ){

        var $el = $(this);

        file_path_field = $el.closest('tr').find('td.file_url input');

        event.preventDefault();

        // If the media frame already exists, reopen it.
        if ( downloadable_file_frame ) {
            downloadable_file_frame.open();
            return;
        }

        var downloadable_file_states = [
            // Main states.
            new wp.media.controller.Library({
                library:   wp.media.query(),
                multiple:  false,
                title:     $el.data('choose'),
                priority:  20,
                filterable: 'uploaded',
            })
        ];

        // Create the media frame.
        downloadable_file_frame = wp.media.frames.downloadable_file = wp.media({
            // Set the title of the modal.
            title: $el.data('choose'),
            library: {
                type: ''
            },
            button: {
                text: $el.data('update'),
            },
            multiple: false,
            states: downloadable_file_states,
        });

        // When an image is selected, run a callback.
        downloadable_file_frame.on( 'select', function() {

            var file_path = '';
            var selection = downloadable_file_frame.state().get('selection');

            selection.map( function( attachment ) {

                attachment = attachment.toJSON();

                if ( attachment.url )
                    file_path = attachment.url

            } );

            file_path_field.val( file_path );
        });


        // Finally, open the modal.
        downloadable_file_frame.open();
    });

   $('.ywtm_override_cb').on('change', function(){

        var t = $(this),
            tab_type = t.data('tab_type'),
            container_id = '#ywtm_wc_tab_content_';

        switch(tab_type){
            case 'reviews':
                container_id+='reviews';
                break;
            case 'description':
                container_id+= 'description';
                break;
            case 'additional_information':
                container_id+= 'additional_information';
                break;
        }

        if(t.is(':checked'))
            $(container_id).show();
        else
            $(container_id).hide();
    }).change();
});

