<?php
/**
 * This file belongs to the YIT Plugin Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly


extract($args);

?>
<div id="<?php echo $id ?>-container" <?php if ( isset($deps) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps['ids'] ?>" data-value="<?php echo $deps['values'] ?>" <?php endif ?>>
    <div id="yit_custom_tabs" class="panel wc-metaboxes-wrapper" style="display: block;margin-left: -184px;">
        <p class="toolbar">
            <a href="#" class="close_all"><?php _e('Close all', 'yit') ?></a><a href="#" class="expand_all"><?php _e('Expand all', 'yit') ?></a>
        </p>

        <div class="yit_custom_tabs wc-metaboxes ui-sortable" style="">

            <?php if( !empty($value) ): ?>
                <?php foreach( $value as $i=>$faq ): ?>

                    <div class="yit_custom_tab wc-metabox closed" rel="0">
                        <h3>
                            <button type="button" class="remove_row button"><?php _e('Remove', 'yit') ?></button>
                            <div class="handlediv" title="Click to toggle"></div>
                            <strong class="attribute_name"><?php _e('FAQ', 'yit'); ?></strong>
                        </h3>

                        <table class="woocommerce_attribute_data wc-metabox-content ">
                            <tbody>
                            <tr>

                                <td>
                                    <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'Question', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the question shown to the customers.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>
                                    <input type="text" class="attribute_name" name="<?php echo $name ?>[<?php echo $i ?>][question]" value="<?php echo esc_attr( $faq['question'] ) ?>">
                                </td>

                            </tr>
                            <tr>
                                <td>
                                    <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'Answer', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the answer shown to the customers.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>
                                    <textarea  name="<?php echo $name ?>[<?php echo $i ?>][answer]" style="height:80px; resize: none;"><?php echo $faq['answer'] ?></textarea>
                                </td>
                            </tr>
                            </tbody>
                        </table>

                    </div>
                <?php endforeach ;?>
            <?php endif ?>
        </div>

        <p class="toolbar">
            <button type="button" class="button button-primary add_custom_tab"><?php _e( 'Add FAQ', 'yith-woocommerce-tab-manager' ) ?></button>
        </p>

        <div class="clear"></div>
   </div>
</div>

<script>
    jQuery(document).ready(function($){
        // Add rows
        $('button.add_custom_tab').on('click', function(){

            var size = $('.yit_custom_tabs .yit_custom_tab').size() + 1;

            // Add custom attribute row
            $('.yit_custom_tabs').append('<div class="yit_custom_tab wc-metabox">\
						<h3>\
							<button type="button" class="remove_row button"><?php _e('Remove', 'yith-woocommerce-tab-manager') ?></button>\
							<div class="handlediv" title="Click to toggle"></div>\
							<strong class="attribute_name"></strong>\
						</h3>\
						<table  class="woocommerce_attribute_data">\
							<tbody>\
								<tr>\
									<td>\
                                        <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'Question', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the question shown to the customers.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>\
                                            <input type="text" class="attribute_name" name="<?php echo $name ?>[' + size + '][question]" >\
									</td>\
                                </tr>\
                                <tr>\
                                <td>\
                                     <label style="font-weight: bolder;font-size: 13px;padding: 10px;"><?php _e( 'Answer', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the answer shown to the customers.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></label>\
                                    <textarea name="<?php echo $name ?>[' + size + '][answer]" style="height:80px; resize: none;"></textarea>\
                                    </td>\
								</tr>\
							</tbody>\
						</table>\
					</div>');

        });


        $('.yit_custom_tabs').on('click', 'button.remove_row', function() {
            var answer = confirm("<?php _e('Do you want to remove this FAQ?', 'yith-woocommerce-tab-manager') ?>");
            if (answer){
                var $parent = $(this).parent().parent();

                $parent.remove();
                attribute_row_indexes();
            }
            return false;
        });

        // Attribute ordering
        $('.yit_custom_tabs').sortable({
            items:'.yit_custom_tab',
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
            $('.yit_custom_tabs .yit_custom_tab').each(function(index, el){
                var newVal = '[' + $(el).index('.yit_custom_tabs .yit_custom_tab') + ']';
                var oldVal = '[' + $('.attribute_position', el).val() + ']';

                $(':input:not(button)', el).each(function(){
                    var name = $(this).attr('name');
                    $(this).attr('name', name.replace(oldVal, newVal));
                });

                $('.attribute_position', el).val( $(el).index('.yit_custom_tabs .yit_custom_tab') );
            });
        };

        $('.wc-metaboxes-wrapper').on('click', '.wc-metabox h3', function(event){

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



    });
</script>