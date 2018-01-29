<?php
if( ! is_array( $form ) ){
    $form = array();
}

$col        =   isset( $form['subj']['show'] ) &&( $form['subj']['show']=='on') ? count($form) : count($form)+1;
$col        =   'ywtm_col_'.$col;
$star_name  =   (isset( $form['name']['req'] ) && $form['name']['req']=='on')   ?   '*' :    '';
$star_addr  =   (isset( $form['webaddr']['req'] ) && $form['webaddr']['req']=='on')   ?   '*' :    '';
$star_subj  =   (isset( $form['subj']['req'] ) && $form['subj']['req']=='on')   ?   '*' :    '';

?>
<div class="yit_wc_tab_manager_contact_form_container ywtm_content_tab">
    <div class="error_messages"></div>
    <form class="ywtm_contact_form" method="post">
        <fieldset>
                <div class="primary_contact_information">
                    <?php if( isset( $form['name']['show']) && $form['name']['show']=='on'):?>
                    <div class="contact_name_field <?php echo $col;?> contact_field">
                        <input type="text" name="ywtm_name_contact_field" placeholder="<?php _e('Your name', 'yith-woocommerce-tab-manager');?><?php echo $star_name;?>" />
                        <?php if ( isset( $form['name']['req'] ) && $form['name']['req']=='on' ):?>
                          <input type="hidden" name="ywtm_req_name" value="req" />
                        <?php endif;?>
                    </div>
                    <?php endif;?>

                        <div class="contact_email_field <?php echo $col;?> contact_field">
                            <input type="text" name="ywtm_email_contact_field" placeholder="<?php _e('Email', 'yith-woocommerce-tab-manager');?>*" />
                            <input type="hidden" name="ywtm_req_email" value="req" />
                        </div>

                    <?php if( isset( $form['webaddr']['show']) && $form['webaddr']['show']=='on'):?>
                        <div class="contact_webaddr_field <?php echo $col;?> contact_field">
                            <input type="text" name="ywtm_webaddr_contact_field" placeholder="<?php _e('Website', 'yith-woocommerce-tab-manager');?><?php echo $star_addr;?>" />
                            <?php if ( isset( $form['webaddr']['req'] ) && $form['webaddr']['req']=='on' ):?>
                                <input type="hidden" name="ywtm_req_webaddr" value="req" />
                            <?php endif;?>
                        </div>
                    <?php endif;?>
                </div>
                <div class="secondary_contact_information">
                    <?php if( isset( $form['subj']['show']) && $form['subj']['show']=='on'):?>
                        <div class="contact_subj_field ywtm_col_1">
                            <input type="text" name="ywtm_subj_contact_field" placeholder="<?php _e('Subject', 'yith-woocommerce-tab-manager');?><?php echo $star_subj;?>" />
                            <?php if ( isset( $form['subj']['req'] ) && $form['subj']['req']=='on' ):?>
                                <input type="hidden" name="ywtm_req_subj" value="req" />
                            <?php endif;?>
                        </div>
                    <?php endif;?>
                        <div class="contact_textarea_field ywtm_col_1">
                            <textarea name="ywtm_info_contact_field" placeholder="<?php _e('Your Message','yith-woocommerce-tab-manager');?>*"></textarea>
                            <input type="hidden" name="ywtm_req_info" value="req" />
                            <input type="hidden" name="ywtm_product_id" value="<?php echo $GLOBALS['product']->id;?>" />
                            <input type="hidden" name="ywtm_action" value="ywtm_sendermail" />
                            <div style="position:absolute; z-index:-1; <?php  echo (is_rtl() ? "margin-right:-9999999px;" : "margin-left:-9999999px;")  ;?> "><input type="text" name="ywtm_bot" class="ywtm_bot"/></div>
                            <span id="ywtm_btn_container"><input type="submit" class="ywtm_btn_sendmail" value="<?php _e('Send','yith-woocommerce-tab-manager');?>"/></span>
                            <?php wp_nonce_field( 'ywtm-sendmail' ); ?>
                        </div>

                </div>
    </fieldset>
</form>
    <span>*<?php _e( 'required', 'yith-woocommerce-tab-manager' );?></span>
</div>
