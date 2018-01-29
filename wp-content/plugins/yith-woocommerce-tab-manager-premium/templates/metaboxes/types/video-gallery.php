<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
$pos=0;
extract ( $args );

?>
<div id="<?php echo $id ?>-container" <?php if ( isset($deps) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps['ids'] ?>" data-value="<?php echo $deps['values'] ?>" <?php endif ?>>
    <div id="gallery_video" class="panel wc-metaboxes-wrapper " style="display: block;">
        <p class="toolbar">
            <a href="#" class="close_all"><?php _e('Close all', 'yith-woocommerce-tab-manager') ?></a><a href="#" class="expand_all"><?php _e('Expand all', 'yith-woocommerce-tab-manager') ?></a>
        </p>

        <div class="rm_number" style="margin-top:10px;margin-bottom:10px;">
            <label><?php _e('Column number', 'yith-woocommerce-tab-manager');?></label>
            <span class="field">
                <input  type="number" id="spinner_column" name="<?php echo $name ?>[columns]" min="1" max="4" step="1" value="<?php if(isset ($value['columns']) ): echo esc_attr($value['columns']);else: echo '1'; endif;?>" />
            </span>
            <span class="desc inline"><?php _e('Set how many columns the video gallery should have','yith-woocommerce-tab-manager');?></span>
        </div>
        <div class="gallery_videos wc-metaboxes ui-sortable" style="">
        <?php if( !empty( $value['video_info'] ) ) :?>
          <?php foreach( $value['video_info'] as $i=>$video ):?>
            <div class="gallery_video_single wc-metabox closed" data-pos="<?php echo $pos ?>" rel="0">
                <h3>
                    <button type="button" class="remove_row button"><?php _e('Remove', 'yith-woocommerce-tab-manager') ?></button>
                    <div class="handlediv" title="Click to toggle"></div>
                    <strong class="attribute_name"><?php _e('Video', 'yith-woocommerce-tab-manager'); ?></strong>
                </h3>

                <div class="woocommerce_attribute_data wc-metabox-content ">

                    <div class="sep"></div>
                    <div class="video_settings_container">
                        <p class="video_form_row">
                            <label><?php _e('Video Hosting Service', 'yith-woocommerce-tab-manager');?></label>
                            <select name="<?php echo $name?>[video_info][<?php echo $pos?>][host]">
                                <option value="youtube" <?php selected('youtube', esc_attr($video['host']));?>><?php _e('YouTube', 'yith-woocommerce-tab-manager')?></option>
                                <option value="vimeo" <?php selected('vimeo', esc_attr($video['host']));?>><?php _e('Vimeo', 'yith-woocommerce-tab-manager')?></option>
                            </select>
                        </p>
                        <p class="video_form_row">
                            <label><?php _e('Video ID', 'yith-woocommerce-tab-manager');?></label>
                            <input type="text" name="<?php echo $name?>[video_info][<?php echo $pos;?>][id]" value="<?php echo esc_attr($video['id'])?>"/>
                        </p>
                        <p class="video_form_row">
                            <label><?php _e('Video URL', 'yith-woocommerce-tab-manager');?></label>
                            <input type="text" class="yith-video-url-field" id="<?php echo $id.'_'.$pos;?>" name="<?php echo $name?>[video_info][<?php echo $pos?>][url]" value="<?php echo esc_attr($video['url'])?>">
                        </p>
                      </div>
                </div>
            </div>
            <?php $pos++;endforeach; endif;?>
        </div>
        <p class="toolbar">
            <button type="button" class="button button-primary add_video_tab"><?php _e( 'Add Video', 'yith-woocommerce-tab-manager' ) ?></button>
        </p>

        <div class="clear"></div>
    </div>
</div>

<script>

    jQuery(document).ready( function($) {


        $('button.add_video_tab').on('click', function () {

            var size = $('.gallery_videos .gallery_video_single').size() ;

            $('.gallery_videos').append('<div class="gallery_video_single wc-metabox closed" rel="0" data-pos="'+size+'">\
                                            <h3>\
                                                <button type="button" class="remove_row button"><?php _e('Remove', 'yith-woocommerce-tab-manager') ?></button>\
                                                <div class="handlediv" title="Click to toggle"></div>\
                                                <strong class="attribute_name"><?php _e('Video', 'yith-woocommerce-tab-manager'); ?></strong>\
                                             </h3>\
                                            <div class="woocommerce_attribute_data wc-metabox-content ">\
                                                <div class="sep"></div>\
                                                <div class="video_settings_container">\
                                                   <p class="video_form_row">\
                                                    <label><?php _e('Video Hosting Service', 'yith-woocommerce-tab-manager');?></label>\
                                                    <select name="<?php echo $name?>[video_info]['+size+'][host]">\
                                                        <option value="youtube" selected="selected"><?php _e('YouTube', 'yith-woocommerce-tab-manager')?></option>\
                                                        <option value="vimeo"><?php _e('Vimeo', 'yith-woocommerce-tab-manager')?></option>\
                                                    </select>\
                                                </p>\
                                                  <p class="video_form_row">\
                                                    <label><?php _e('Video ID', 'yith-woocommerce-tab-manager');?></label>\
                                                    <input type="text" name="<?php echo $name?>[video_info]['+size+'][id]" placeholder="<?php _e('Insert a valid video ID here','yith-woocommerce-tab-manager');?>"/>\
                                                </p>\
                                                  <p class="video_form_row">\
                                                    <label><?php _e('Video URL', 'yith-woocommerce-tab-manager');?></label>\
                                                    <input type="text" class="yith-video-url-field" id="<?php echo $id.'_'?>'+size+'"  placeholder="<?php _e('Insert a valid video URL here','yith-woocommerce-tab-manager');?>"  name="<?php echo $name?>[video_info]['+size+'][url]">\
                                                </p>\
                                                 </div>\
                                            </div>\
                                        </div>');



        });



        $('.gallery_videos').on('click', 'button.remove_row', function () {
            var answer = confirm("<?php _e('Do you want to remove this video?', 'yith-woocommerce-tab-manager') ?>");
            if (answer) {
                var $parent = $(this).parent().parent();

                $parent.remove();
                my_attribute_row_indexes();
            }
            return false;
        });


        // Attribute ordering
        $('.gallery_videos').sortable({
            items: '.gallery_video_single',
            cursor: 'move',
            axis: 'y',
            handle: 'h3',
            scrollSensitivity: 40,
            forcePlaceholderSize: true,
            helper: 'clone',
            opacity: 0.65,
            placeholder: 'wc-metabox-sortable-placeholder',
            start: function (event, ui) {
                ui.item.css('background-color', '#f6f6f6');
            },
            stop: function (event, ui) {
                ui.item.removeAttr('style');
                my_attribute_row_indexes();
            }
        });

        function my_attribute_row_indexes() {
            $('.gallery_videos .gallery_video_single').each(function (index, el) {
            var el       = $(el),
                newIndex = el.index('.gallery_videos .gallery_video_single'),
                oldIndex = el.data( 'pos' ),
                newVal = '[' + newIndex + ']',
                oldVal = '[' + oldIndex + ']';

            //el.data( 'pos', newIndex );

            $(':input:not(button)', el).each(function(){
                var t = $( this),
                    name = t.attr('name');
                if( typeof name !== 'undefined' ){
                    var container = t.parents( 'div.gallery_video_single');
                    container.data( 'pos', newIndex );
                    t.attr( 'name', name.replace(oldVal, newVal) );
                }
            });

            $('.attribute_position', el).val( $(el).index('.gallery_videos .gallery_video_single') );
        });
        };

        $('#gallery_video .wc-metaboxes-wrapper').on('click', '.wc-metabox h3', function (event) {

            // If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
            if ($(event.target).filter(':input, option').length) return;

            $(this).next('.wc-metabox-content').stop().slideToggle();
        })
            .on('click', '.expand_all', function (event) {

                $(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox > .wc-metabox-content').show();
                return false;
            })
            .on('click', '.close_all', function (event) {
                $(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox > .wc-metabox-content').hide();
                return false;
            });
        $('.wc-metabox.closed').each(function () {
            $(this).find('.wc-metabox-content').hide();
        });

    });



</script>