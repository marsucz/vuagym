<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
extract($args);

?>
<div id="<?php echo $id ?>-container" <?php if ( isset($deps) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps['ids'] ?>" data-value="<?php echo $deps['values'] ?>" <?php endif ?>>
    <div id="yit_download_tabs" class="panel wc-metaboxes-wrapper" style="display: block;">
        <p class="toolbar">
            <a href="#" class="close_all"><?php _e('Close all', 'yith-woocommerce-tab-manager') ?></a><a href="#" class="expand_all"><?php _e('Expand all', 'yith-woocommerce-tab-manager') ?></a>
        </p>

        <div class="yit_download_tabs wc-metaboxes ui-sortable" style="">

           <?php if( !empty($value) ): ?>
                <?php foreach( $value as $i=>$download ):?>

                   <div class="yit_download_tab wc-metabox closed" rel="0" data-pos="<?php echo $i?>">
                        <h3>
                            <button type="button" class="remove_row button"><?php _e('Remove', 'yith-woocommerce-tab-manager') ?></button>
                            <div class="handlediv" title="Click to toggle"></div>

                        </h3>

                        <table class="woocommerce_attribute_data wc-metabox-content ">
                            <tbody>
                            <tr>
                                <td>
                                    <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'File Name', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the file name shown to the customers.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>
                                    <input type="text" class="attribute_name" name="<?php echo $name ?>[<?php echo $i ?>][name]" value="<?php echo esc_attr( $download['name'] ) ?>">
                                </td>
                            </tr>
                            <tr>
                                 <td>
                                     <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'File URL', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the file URL.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>
                                     <input type="text" id="<?php echo $id?>_<?php echo $i?>" name="<?php echo $name ?>[<?php echo $i ?>][file]" value="<?php echo esc_attr( $download['file'] ) ?>"class="upload_img_url"/>
                                    <input type="button" class="button-secondary my_upload_button" id="<?php echo $id ?>_<?php echo $i?>-button" value="<?php _e( 'Upload', 'yith-woocommerce-tab-manager' ) ?>" style="min-width:100px;float:none;width:100px;margin-top:43px;"/>
                                </td>
                             </tr>
                            <tr>
                                <td>
                                   <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'File Description', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is a short description of your file.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>
                                    <textarea name="<?php echo $name?>[<?php echo $i ?>][desc]" style="height:80px; resize: none;"><?php echo esc_attr($download['desc']);?></textarea>
                                </td>
                            </tr>

                            </tbody>
                        </table>

                    </div>
               <?php endforeach; ?>

            <?php endif; ?>
        </div>

        <p class="toolbar">
            <button type="button" class="button button-primary add_download_tab"><?php _e( 'Add File', 'yith-woocommerce-tab-manager' ) ?></button>
        </p>

        <div class="clear"></div>
    </div>
</div>

<script>
    jQuery(document).ready(function($){
        // Add rows
        $('button.add_download_tab').on('click', function(){

            var size = $('.yit_download_tabs .yit_download_tab').size();

            // Add custom attribute row
            $('.yit_download_tabs').append('<div class="yit_download_tab wc-metabox" data-pos="' + size + '">\
						<h3>\
							<button type="button" class="remove_row button"><?php _e('Remove', 'yith-woocommerce-tab-manager') ?></button>\
							<div class="handlediv" title="Click to toggle"></div>\
						</h3>\
						<table class="woocommerce_attribute_data wc-metabox-content ">\
                            <tbody>\
                                <tr>\
                                    <td>\
                                        <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'File Name', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the file name shown to the customers.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>\
                                        <input type="text" name="<?php echo $name ?>['+size+'][name]" value="">\
                                   </td>\
                                </tr>\
                                <tr>\
                                    <td>\
                                        <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'File URL', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the file URL.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>\
                                        <input type="text" id="<?php echo $id?>_'+size+'" name="<?php echo $name ?>['+size+'][file]" value=""class="upload_img_url"/>\
                                        <input type="button" class="button-secondary my_upload_button" id="<?php echo $id ?>_'+size+'-button" value="<?php _e( 'Upload', 'yith-woocommerce-tab-manager' ) ?>"  style="min-width:100px;float:none;width:100px;margin-top:43px;"/>\
                                    </td>\
                                </tr>\
                                <tr>\
                                 <td>\
                                    <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'File Description', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is a short description of your file.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>\
                                    <textarea name="<?php echo $name ?>[' + size + '][desc]" style="height:80px; resize: none;" placeholder="<?php _e('Write a short description here','yith-woocommerce-tab-manager');?>"></textarea>\
                                 </td>\
                                </tr>\
                            </tbody>\
                     </table>\
                </div>');

            var butt=  $('.my_upload_button');

            $(butt).on('click', function(){ upload_file ($(this))});




        });

        $('.my_upload_button').on('click', function(){ upload_file ($(this))});

        $('.yit_download_tabs').on('click', 'button.remove_row', function() {
            var answer = confirm("<?php _e('Do you want to remove this file?', 'yith-woocommerce-tab-manager') ?>");
            if (answer){
                var $parent = $(this).parent().parent();

                $parent.remove();
                attribute_row_indexes();
            }
            return false;
        });

        // Attribute ordering
        $('.yit_download_tabs').sortable({
            items:'.yit_download_tab',
            cursor:'move',
            axis:'y',
            handle: 'h3',
            scrollSensitivity:40,
            forcePlaceholderSize: true,
            helper: 'clone',
            opacity: 0.65,
            placeholder: 'wc-metabox-sortable-placeholder',
            start:function(event,ui){
                ui.item.css('background-color','#f6f6f6');
            },
            stop:function(event,ui){
                ui.item.removeAttr('style');
                attribute_row_indexes();
            }
        });

        function attribute_row_indexes() {
            $('.yit_download_tabs .yit_download_tab').each(function(index, el){

                var el       = $(el),
                    newIndex = el.index('.yit_download_tabs .yit_download_tab'),
                    oldIndex = el.data( 'pos' ),
                    newVal = '[' + newIndex + ']',
                    oldVal = '[' + oldIndex + ']';

                //el.data( 'pos', newIndex );

                $(':input:not(button)', el).each(function(){
                    var t = $( this),
                        name = t.attr('name');
                    if( typeof name !== 'undefined' ){
                        var container = t.parents( 'div.yit_download_tab');
                        container.data( 'pos', newIndex );
                        t.attr( 'name', name.replace(oldVal, newVal) );
                    }
                });

                $('.attribute_position', el).val( $(el).index('.yit_download_tabs .yit_download_tab') );
            });
        };

        $('#yit_download_tabs .wc-metaboxes-wrapper').on('click', '.wc-metabox h3', function(event){

            // If the user clicks on some form input inside the h3, like a select list (for variations), the box should not be toggled
            if ($(event.target).filter(':input, option').length) return;

            $(this).next('.wc-metabox-content').stop().slideToggle();
        })
            .on('click', '.expand_all', function(event){

                $(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox > .wc-metabox-content').show();
                return false;
            })
            .on('click', '.close_all', function(event){
                $(this).closest('.wc-metaboxes-wrapper').find('.wc-metabox > .wc-metabox-content').hide();
                return false;
            });
        $('.wc-metabox.closed').each(function(){
            $(this).find('.wc-metabox-content').hide();
        });

        /*Download button from POST TYPE*/
        //upload

      function upload_file(button){

            var _custom_media = true,
              _orig_send_attachment = wp.media.editor.send.attachment;


            var send_attachment_bkp = wp.media.editor.send.attachment;
            var button = $(button);
            var id = button.attr('id').replace('-button', '');
            _custom_media = true;
            wp.media.editor.send.attachment = function(props, attachment){
                if ( _custom_media ) {
                    if( $("#"+id).is('input[type=text]') ) {
                        $("#"+id).val(attachment.url);
                    } else {
                        $("#"+id + '_custom').val(attachment.url);
                    }
                } else {
                    return _orig_send_attachment.apply( this, [props, attachment] );
                };
            }

            wp.media.editor.open(button);
            return false;
        }

        $('#yit_download_tabs .add_media').on('click', function(){
            _custom_media = false;
        });

    });
</script>