<?php
/**
 * kuteshop functions and definitions
 *
 * @version 1.0
 *
 * @date 12.08.2015
 */

load_theme_textdomain( 'kuteshop', get_template_directory() . '/languages' );

/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) ) {
	$content_width = 640; /* pixels */
}

require_once( trailingslashit( get_template_directory() ). '/7upframe/function/function.php' );

// Tuan Dev
require_once( trailingslashit( get_template_directory() ). '/7upframe/function/ka_loader.php' );
$ka_loader = new ka_loader();

$option_tree = get_option('option_tree');
if (array_key_exists('ka_custom_layout', $option_tree) && $option_tree['ka_custom_layout'] == 'layout2') {
    require_once( trailingslashit( get_template_directory() ). '/7upframe/function/layout2.php' );
} else {
    require_once( trailingslashit( get_template_directory() ). '/7upframe/function/layout1.php' );
}

require_once( trailingslashit( get_template_directory() ). '/7upframe/config/config.php' );

// LOAD CLASS LIB

require_once( trailingslashit( get_template_directory() ). '/7upframe/class/asset.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/class/class-tgm-plugin-activation.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/class/importer.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/class/mega_menu.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/class/order-comment-field.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/class/require-plugin.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/class/temlate.php' );

// END LOAD

// LOAD CONTROLER LIB

require_once( trailingslashit( get_template_directory() ). '/7upframe/controler/BaseControl.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/controler/Customize_Control.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/controler/Metabox_Control.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/controler/Option_Tree_Control.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/controler/Visual_composer_Control.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/controler/Walker_megamenu.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/controler/Woocommerce_Control.php' );
require_once( trailingslashit( get_template_directory() ). '/7upframe/controler/Wpml_Control.php' );

// END LOAD

require_once( trailingslashit( get_template_directory() ). '/7upframe/index.php' );
// Khoa Anh add - xoa shipping trang gio hang
function disable_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
    return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99 );
// End

/* START: TUAN DEV */

/* San TMDT Shoppe */
add_action( 'woocommerce_product_write_panel_tabs', 'kawoo_add_tab_san_tmdt' );
function kawoo_add_tab_san_tmdt() {
  ?>
  <li class="san_tmdt_tab">
    <a href="#san_tmdt_panel">
      <span>Sàn TMĐT</span>
    </a>
  </li>
  <?php
}

add_action( 'woocommerce_product_data_panels', 'ka_san_tmdt_tab_panel' );
function ka_san_tmdt_tab_panel() {
    global $post;
    
    $ka_shoppe = get_post_meta($post->ID, '_ka_shoppe', true);
    $ka_shoppe_type = get_post_meta($post->ID, '_ka_shoppe_type', true);
    $ka_shoppe_content = get_post_meta($post->ID, '_ka_shoppe_content', true);
    
  ?>
  <div id="san_tmdt_panel" class="panel woocommerce_options_panel">
          <div class="options_group">
              <p class="form-field my_custom_input_field">
                  <label for="_ka_shoppe" style="width: 15%"><strong>Shoppe</strong>
                    <input type="checkbox" name="_ka_shoppe" id="_ka_shoppe" <?php echo ($ka_shoppe == 'yes')?'checked':''; ?>>
                </label>
                <select name="_ka_shoppe_type" style="margin: 0 0 0 -60px;">
                    <option value="link" <?php echo ($ka_shoppe_type == 'link') ? 'selected' : ''; ?>>Link</option>
                    <option value="text" <?php echo ($ka_shoppe_type == 'text') ? 'selected' : ''; ?>>Text</option>
                </select>
                <?php 
                    wp_editor( $ka_shoppe_content, '_ka_shoppe_content', $settings = array('textarea_rows'=> '5') );
                ?>
              </p>
          </div>
      </div>
  
<?php } 

add_action( 'woocommerce_process_product_meta', 'ka_save_san_tmdt_fields' );
function ka_save_san_tmdt_fields( $post_id ) {
  
    $ka_shoppe = isset($_POST['_ka_shoppe']) && ($_POST['_ka_shoppe'] == 'on') ? 'yes' : 'no';
    update_post_meta($post_id, '_ka_shoppe', $ka_shoppe);

    $ka_shoppe_type = isset($_POST['_ka_shoppe_type']) ? $_POST['_ka_shoppe_type'] : '';
    update_post_meta($post_id, '_ka_shoppe_type', $ka_shoppe_type);
    
    $ka_shoppe_content = isset($_POST['_ka_shoppe_content']) ? $_POST['_ka_shoppe_content'] : '';
    update_post_meta($post_id, '_ka_shoppe_content', $ka_shoppe_content);
    
}

function ka_san_TMDT_modal($message = ''){
    $return = '        
    <div class="modal fade" id="sanTMDTModal" tabindex="-1" role="dialog" aria-labelledby="sanTMDTModal" aria-hidden="true" style="padding-top: 5%;">
        <div class="modal-dialog modal-lg" id="mypos-modal-dialog">
            <div class="modal-content">
                <div class="modal-header white">
                    <div class="mypos-modal-header">
                        <span style="color: black; font-weight: bold">Thông tin sản phẩm trên Shoppe</span>
                    </div>
                </div>
                <div class="modal-body cart-title-body">
                ' . html_entity_decode($message) . '
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary mypos-btn-close" data-dismiss="modal">Thoát</button>
                </div>
            </div>
        </div>
    </div>
    ';
    return $return;
}

function ja_ajax_ka_get_shoppe_popup() {
    
    $product_id     = intval($_POST['product_id']);
    
    $shoppe_content = get_post_meta($product_id, '_ka_shoppe_content', true);
    $content = nl2br($shoppe_content);
    $return = ka_san_TMDT_modal($content);
    wp_send_json_success( $return );
}

add_action( 'wp_ajax_ka_get_shoppe_popup', 'ja_ajax_ka_get_shoppe_popup' );
add_action( 'wp_ajax_nopriv_ka_get_shoppe_popup', 'ja_ajax_ka_get_shoppe_popup' );

/* END San TMDT Shoppe */
