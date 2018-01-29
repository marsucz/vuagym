<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly
extract ( $args );

if( !is_array( $value ) ){
    $value = array();
}
$value['name']['show']  = isset ($value['name']['show'])? $value['name']['show'] : 'no';
$value['webaddr']['show']  = isset ($value['webaddr']['show'])? $value['webaddr']['show'] : 'no';
//$value['email']['show']  = isset ($value['email']['show'])? $value['email']['show'] : 'no';
$value['subj']['show']  = isset ($value['subj']['show'])? $value['subj']['show'] : 'no';

$value['name']['req']  = isset ($value['name']['req'])? $value['name']['req'] : 'no';
$value['webaddr']['req']  = isset ($value['webaddr']['req'])? $value['webaddr']['req'] : 'no';
//$value['email']['req']  = isset ($value['email']['req'])? $value['email']['req'] : 'no';
$value['subj']['req']  = isset ($value['subj']['req'])? $value['subj']['req'] : 'no';

$show_req_name      =   $value['name']['show']=='no'? 'display:none' :'display:block';
$show_req_webaddr   =   $value['webaddr']['show']=='no'? 'display:none' :'display:block';
//$show_req_email     =   $value['email']['show']=='no'? 'display:none' :'display:block';
$show_req_subj      =   $value['subj']['show']=='no'? 'display:none' :'display:block';
?>
<div id="<?php echo $id ?>-container" <?php if ( isset($deps) ): ?>data-field="<?php echo $id ?>" data-dep="<?php echo $deps['ids'] ?>" data-value="<?php echo $deps['values'] ?>" <?php endif ?>>
    <div class="custom_form_field">
      <div class="form_video_row">
        <label for="<?php echo $id?>_name"><?php _e('Name','yith-woocommerce-tab-manager');?></label>
        <input type="checkbox" id="<?php echo $id?>_name" class="add_field_check" name="<?php echo $name?>[name][show]" <?php checked($value['name']['show'],'on');?> />
        <p class="sub_form_video_row" id="<?php echo $id?>_name_req" style="<?php echo $show_req_name?>">
            <label class="req">Required</label>
            <input type="checkbox"  name="<?php echo $name?>[name][req]" <?php checked($value['name']['req'],'on');?>/>
            <span class="desc inline"><?php _e( 'Check this option to make it required.', 'yith-woocommerce-tab-manager' ) ?></span>
        </p>
      </div>
      <div class="form_video_row">
          <label for="<?php echo $id?>_webaddr"><?php _e('Website','yith-woocommerce-tab-manager');?></label>
          <input type="checkbox" id="<?php echo $id?>_webaddr" class="add_field_check" name="<?php echo $name?>[webaddr][show]" <?php checked($value['webaddr']['show'],'on');?>/>
        <p class="sub_form_video_row" id="<?php echo $id?>_webaddr_req" style="<?php echo $show_req_webaddr;?>">
          <label class="req">Required</label>
          <input type="checkbox"  name="<?php echo $name?>[webaddr][req]"  <?php checked($value['webaddr']['req'],'on');?>/>
          <span class="desc inline"><?php _e( 'Check this option to make it required.', 'yith-woocommerce-tab-manager' ) ?></span>
        </p>
      </div>

      <div class="form_video_row">
          <label for="<?php echo $id?>_subj"><?php _e('Subject','yith-woocommerce-tab-manager');?></label>
          <input type="checkbox" id="<?php echo $id?>_subj" class="add_field_check" name="<?php echo $name?>[subj][show]" <?php checked($value['subj']['show'],'on');?>/>
        <p class="sub_form_video_row" id="<?php echo $id?>_subj_req" style="<?php echo $show_req_subj?>">
          <label class="req">Required</label>
          <input type="checkbox" name="<?php echo $name?>[subj][req]" <?php checked($value['subj']['req'],'on');?>/>
          <span class="desc inline"><?php _e( 'Check this option to make it required.', 'yith-woocommerce-tab-manager' ) ?></span>
        </p>
      </div>
  </div>
</div>

<script>

    jQuery(document).ready( function($) {

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
    });
 </script>