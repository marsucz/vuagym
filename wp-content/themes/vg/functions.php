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


// Shoppe Tab
function ka_shoppe_tab() {

    global $re_wcvt_options;

    $tabs['variations_table'] = array(
        'title' => "Sàn TMĐT",
        'priority' => 50,
        'callback' => 'ka_shoppe_tab_content'
    );

    return $tabs;
}
add_filter('woocommerce_product_tabs', 'ka_shoppe_tab');

function ka_shoppe_tab_content() {
    global $product, $post, $re_wcvt_options;
    
    echo "<pre>";
    print_r($post);
    print_r($re_wcvt_options);
    echo "</pre>";
    
}

/*===================================================   
    Options
====================================================*/

$re_wcvt_options = array(
    'tab_title' =>              'Product Variations',   // change the tile of the tab
    'sku_title' =>              'REF #',                // change the sku column heading
    'show_price' =>             'yes',                  // show price column: yes or no
    'show_description' =>       'yes',                  // show description column: yes or no
    'tab_priority' =>           '5',                    // 5 is good to make the tab appear first
);


/*===================================================   
    Add the tab
====================================================*/


add_filter( 'woocommerce_product_tabs', 're_woo_product_variations_tab' );
function re_woo_product_variations_tab() {

	global $woocommerce, $product, $post, $re_wcvt_options;
    // $available_variations = $product->get_available_variations();
    // $attributes = $product->get_attributes();
    if (is_product() and $product->product_type == 'variable') {
	
		// Adds the new tab
		
		$tabs['variations_table'] = array(
			'title' 	=> __( $re_wcvt_options['tab_title'], 'woocommerce' ),
			'priority' 	=> 50,
			'callback' 	=> 're_woo_product_variations_tab_content'
		);
	 
		return $tabs;
	}
 
}

/*===================================================   
    Build the tab content
====================================================*/

function re_woo_product_variations_tab_content() {

	global $woocommerce, $product, $post, $re_wcvt_options;
    $available_variations = $product->get_available_variations();
    $attributes = $product->get_attributes();
 
	// The new tab content
 	
	//echo '<h2>Product Variations</h2>';
	//echo '<p>Here\'s your new product tab.</p>';
    
?>
            <table class="table table-striped table-hover table-bordered varations-table tablesorter">
                <thead>
                    <tr>
                        <th><?php echo $re_wcvt_options['sku_title']; ?></th>
                    <?php 
                        // Show description if option is set to yes
                        if ($re_wcvt_options['show_description'] == 'yes') : ?>
                        <th>Description</th>
                    <?php endif; ?>
                    <?php foreach ( $attributes as $name => $options) :?>
                        <th>
                        <?php 
                            //echo $woocommerce->attribute_label($name); 
                            $attr_name = $options['name'];
                            if (0 === strpos($attr_name, 'pa_')){
                                $attr_name = $woocommerce->attribute_label($attr_name);
                            }
                            echo $attr_name;
                        ?>
                        </th>
                    <?php endforeach;?>
                    <?php 
                        // Show price if option is set to yes
                        if ($re_wcvt_options['show_price'] == 'yes') : ?>
                        <th>Price</th>
                    <?php endif; ?>
                        <th class="var-qty">&nbsp;</th>
                        <th class="var-add-to-cart">&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                    /*
                    echo '<pre>';
                    print_r($re_wcvt_options);
                    echo '</pre>';
                    */
                ?>
                <?php foreach ($available_variations as $prod_variation) : ?>
                    <?php
                        // get some vars to work with
                       	$post_id = $prod_variation['variation_id'];
                        $post_object = get_post($post_id);
                        
                         
                        //echo '<pre>';
                        //print_r($prod_variation);
                        //echo '</pre>';
                        
                    ?>
                    <tr>
                        <td>
                            <?php 
                            	// echo substr($prod_variation['sku'], 5, 100) ; // output SKU but trim the first part that is added 
                            	echo $prod_variation['sku'];
                            ?>
                        </td>
                    <?php 
                    // Show description if option is set to yes
                    if ($re_wcvt_options['show_description'] == 'yes') : ?>
                        <td>
                        <?php 
                            $variation_desc = get_post_meta( $post_object->ID, '_description', true);
                            if ( !empty($post_object->post_content)){
                                $variation_desc = $post_object->post_content; // post content 
                            } elseif (!empty($variation_desc)) {
                                $variation_desc = get_post_meta( $post_object->ID, '_description', true); // get meta description
                            } else {
                                $variation_desc = get_the_title($product->id); // parent title
                            }
                            echo $variation_desc;
                        ?>
                        </td>
                    <?php endif; ?>
                    <?php foreach ($prod_variation['attributes'] as $attr_name => $attr_value) : ?>
                        <td>
                        <?php
                            // Get the correct variation values
                            if (strpos($attr_name, '_pa_')){ // variation is a pre-definted attribute
                                $attr_name = substr($attr_name, 10);
                                $attr = get_term_by('slug', $attr_value, $attr_name);
                                $attr_value = $attr->name;
                            } else { // variation is a custom attribute
                                //$attr = maybe_unserialize( get_post_meta( $post->ID, '_product_attributes' ) );
                                //$attr_value = var_dump($attr);
                                
                                //$attr = get_term_by('slug', $attr_value, $attr_name);
                                //$attr_value = $attr->name;
                            }
                            echo $attr_value;
                        ?>
                        </td>
                    <?php endforeach;?>
                    <?php 
                        // Show price if option is set to yes
                        if ($re_wcvt_options['show_price'] == 'yes') : ?>
                        <td><?php echo get_woocommerce_currency_symbol() . get_post_meta( $post_object->ID, '_price', true); ?></td>
                    <?php endif; ?>
                    	<form action="<?php echo do_shortcode('[add_to_cart_url id="'.$product->id.'"]'); ?>" class="variations_form cart" method="post" enctype="multipart/form-data" data-product_id="<?php echo $product->id; ?>">
                        <td>
                            <?php woocommerce_quantity_input(); ?>
                        </td>
                        <td>
                                <input type="hidden" name="variation_id" value="<?php echo $post_id; ?>">
                                    <?php foreach ($prod_variation['attributes'] as $attr_name => $attr_value) : ?>
                                        <input type="hidden" name="<?php echo sanitize_title($attr_name); ?>" value="<?php echo $attr_value ;?>">
                                    <?php endforeach;?>                                     
                                <button type="submit" class="btn btn-small button add-to" type="button">Add to cart</button>
                            
                        </td>
                        </form>
                    </tr>
                <?php endforeach;?>
                </tbody>
            </table>

            <?php
                //echo '<pre>';
                //print_r($prod_variation['attributes']); 
                //echo '</pre>';
            ?>
<?php
}

/*===================================================   
    Tab Position
====================================================*/


add_filter( 'woocommerce_product_tabs', 're_woo_move_variation_table_tab', 98);
function re_woo_move_variation_table_tab($tabs) {
    global $re_wcvt_options;
    if ($tabs['variations_table']) {
        $tabs['variations_table']['priority'] = $re_wcvt_options['tab_priority'];
    }
    return $tabs;
}

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
                <textarea class="" style="" name="_ka_shoppe_content" id="_ka_shoppe_content" placeholder="Nhập link/text..." rows="2" cols="20" style="min-height: 28px"><?php echo $ka_shoppe_content; ?></textarea>
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
                ' . $message . '
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
    $return = ka_san_TMDT_modal($shoppe_content);
    wp_send_json_success( $return );
}

add_action( 'wp_ajax_ka_get_shoppe_popup', 'ja_ajax_ka_get_shoppe_popup' );
add_action( 'wp_ajax_nopriv_ka_get_shoppe_popup', 'ja_ajax_ka_get_shoppe_popup' );

/* END San TMDT Shoppe */