<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<?php
$shortcode = get_post_meta( $post->ID, $tab.'_custom_shortcode', true );
?>
<div id="<?php echo $tab;?>_tab" class="panel woocommerce_options_panel">
    <div class="custom_tab_options" >
        <p class="form-field">
            <label for="custom_shortcode_tab"><?php _e('Shortcode', 'yith-woocommerce-tab-manager');?></label>
            <textarea name="<?php echo $tab?>_shortcode" placeholder="<?php _e('Add a shortcode here', 'yith-woocommerce-tab-manager');?>"><?php echo $shortcode;?></textarea>
        </p>
     </div>
</div>