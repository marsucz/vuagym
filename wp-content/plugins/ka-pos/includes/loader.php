<?php
require_once('DbModel.php');
/**
 * Description of loader
 *
 * @author Tartarus
 */
class loader {
    
    private $kho_phu = '';
    
    public function __construct() {
        
        $this->kho_phu = get_option('kiotviet2_name') ? "Kho '" . get_option('kiotviet2_name') . "'" : "Kho phụ";
        
        add_filter( 'product_type_options', array( $this, 'mypos_other_store_checkbox' ), 6 );
        add_action( 'woocommerce_variation_options', array( $this, 'add_other_store_variable_checkbox' ), 11, 4 );
        add_action( 'woocommerce_process_product_meta', array( $this, 'mypos_update_settings' ));
        add_action( 'woocommerce_save_product_variation', array( $this, 'mypos_save_variable_fields' ), 10, 2 );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        
        add_filter( 'product_type_options', array( $this, 'mypos_show_always_checkbox' ), 6 );
//        add_filter( 'woocommerce_product_is_visible', array( $this, 'kawoo_show_always'), 10, 2 );
        add_action( 'post_updated', array( $this, 'kapos_update_price_variation_field' ), 10 );
        
        //Tinh trang san pham
        add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'ka_add_variation_info_tinh_trang_sp' ), 10, 3 );
        add_action( 'woocommerce_product_options_general_product_data', array ($this, 'ka_add_simple_info_tinh_trang_sp') );
        add_filter( 'woocommerce_available_variation', array( $this, 'ka_load_variation_settings_fields' ) );
    }
    
//    public function kawoo_show_always( $is_visible, $id ) {
//        
//        $show_always_status = get_post_meta($id, '_mypos_show_always', true);
//        if ($show_always_status && $show_always_status == 'yes') {
//            $is_visible = true;
//        }
//        return $is_visible;
//    }

    public function enqueue_scripts($hook_suffix) {
        $current_screen = get_current_screen();

        if ('product' == $current_screen->id || 'edit-shop_order' == $current_screen->id || 'edit-product' == $current_screen->id) {
            wp_register_script('mypos_other_store', WC_PLUGIN_URL . 'assets/admin/js/edit-product-page.js');
            wp_enqueue_script('mypos_other_store');
            wp_register_style('mypos_other_store', WC_PLUGIN_URL . 'assets/admin/css/styles.css');
            wp_enqueue_style('mypos_other_store');
        }
    }

    public function mypos_other_store_checkbox( $product_type_options ) {
        
        global $post; 
        $is_otherstore = $this->get_store_other_status($post->ID);
        
        $other_store_checkbox = array(
                'mypos_otherstore' => array(
                        'id'            => '_mypos_other_store',
                        'wrapper_class' => 'show_if_simple ka_display_block',
                        'label'         => _x( $this->kho_phu, 'Hàng được chứa ở kho phụ.','mypos-other-store' ),
                        'description'   => __( 'Bật tùy chọn này nếu sản phẩm được chứa ở kho phụ.', 'mypos-other-store' ),
                        'default'       => $is_otherstore === 'yes' ? 'yes' : 'no'
                )
        );

        return array_merge( $product_type_options, $other_store_checkbox );

    }

    public function get_store_other_status($post_id) {
        $status = get_post_meta($post_id, '_mypos_other_store', true);
        return $status;
    }
    
    public function mypos_show_always_checkbox( $product_type_options ) {
        
        global $post; 
        $is_showalways = $this->get_show_always_status($post->ID);
        
        $other_store_checkbox = array(
                'mypos_show_always' => array(
                        'id'            => '_mypos_show_always',
                        'wrapper_class' => 'ka_display_block',
                        'label'         => __( 'Luôn hiện sản phẩm', 'Luôn hiện sản phẩm.','mypos-show-always' ),
                        'description'   => __( 'Bật tùy chọn này nếu muốn sản phẩm luôn hiện ở catalog khi hết hàng.', 'mypos-show-always' ),
                        'default'       => $is_showalways === 'yes' ? 'yes' : 'no'
                )
        );

        return array_merge( $product_type_options, $other_store_checkbox );

    }
    
    public function get_show_always_status($post_id) {
        $status = get_post_meta($post_id, '_mypos_show_always', true);
        return $status;
    }

    public function add_other_store_variable_checkbox( $loop, $variation_data, $variation ) {
        $is_otherstore = $this->get_store_other_status($variation->ID);
        ?>
        <label>
                <input type="checkbox" class="checkbox variable_is_otherstore"
                           name="_mypos_other_store[<?php echo $loop; ?>]"
                        <?php checked( $is_otherstore, esc_attr( 'yes' ) ); ?> />
                <?php _ex( $this->kho_phu, 'Hàng được chứa ở kho phụ.','mypos-other-store' ); ?>
                <?php echo wc_help_tip( __( 'Bật tùy chọn này nếu sản phẩm được chứa ở kho phụ.', 'mypos-other-store' ) ); ?>
        </label>
        <?php
    }

    public function mypos_update_settings( $post_id ) {
            $is_other_store = isset( $_POST['_mypos_other_store'] ) && ! is_array( $_POST['_mypos_other_store'] ) ? 'yes' : 'no';
            update_post_meta($post_id, '_mypos_other_store', $is_other_store);
            
            $is_show_always = isset( $_POST['_mypos_show_always'] ) && ! is_array( $_POST['_mypos_show_always'] ) ? 'yes' : 'no';
            update_post_meta($post_id, '_mypos_show_always', $is_show_always);
            if ($is_show_always == 'yes') {
                //overwrite catalog_visibility
                $_POST['_visibility'] = 'visible';
            }
            
            //Tinh trang san pham Simple
            if (isset($_POST['_ka_tinh_trang_sp'])) {
                update_post_meta($post_id, '_ka_tinh_trang_sp', $_POST['_ka_tinh_trang_sp']);
            }
    }

    public function mypos_save_variable_fields( $post_id, $_i ) {
        //Kho phu
        $is_other_store = isset( $_POST['_mypos_other_store'][ $_i ] ) ? 'yes' : 'no';
        update_post_meta($post_id, '_mypos_other_store', $is_other_store);
        
        //Tinh trang san pham
        if (isset( $_POST['_ka_tinh_trang_sp'][ $_i ] )) {
            $tinh_trang_sp = $_POST['_ka_tinh_trang_sp'][ $_i ];
            update_post_meta($post_id, '_ka_tinh_trang_sp', $tinh_trang_sp);
        }
    }
    
    function kapos_update_price_variation_field( $product_id ) {
        
        $post_type = get_post_type($product_id);
        
        if ($post_type != 'product') return;
        
        $product = wc_get_product($product_id);
    
        $dbModel = new DbModel();
        $childrens = $dbModel->get_children_ids($product_id);

        $max_int = 999999999;
        $min_int = -999999999;
        $price_min = $max_int;
        $price_max = $min_int;

        $id_min = 0;
        $id_max = 0;

        foreach ($childrens as $child) {
            $child_id = $child['ID'];
            $child_prod = wc_get_product($child_id);
            $temp_price = $child_prod->get_price();
            if ($temp_price) {
                if ($temp_price < $price_min) {
                    $price_min = $temp_price;
                    $id_min = $child_id;
                }

                if ($temp_price > $price_max) {
                    $price_max = $temp_price;
                    $id_max = $child_id;
                }
            }
        }

        $udata = array();
        if ($id_min && $id_max) {
            $udata['min'] = $id_min;
            $udata['max'] = $id_max;
        }
        if (!empty($udata)) {
            update_post_meta($product_id, '_kapos_custom_price', $udata);
        }
    }
    
    // Tinh trang san pham Variations
    public function ka_add_variation_info_tinh_trang_sp( $loop, $variation_data, $variation ) {
        woocommerce_wp_textarea_input(
            array(
                'id'            => "_ka_tinh_trang_sp{$loop}",
                'name'          => "_ka_tinh_trang_sp[{$loop}]",
                'value'         => get_post_meta( $variation->ID, '_ka_tinh_trang_sp', true ),
                'label'         => __( 'Tình trạng sản phẩm', 'woocommerce' ),
                'wrapper_class' => 'form-row form-row-full',
            )
        );
    }
    // Tinh trang san pham Simple
    public function ka_add_simple_info_tinh_trang_sp() {
        global $post;
        woocommerce_wp_textarea_input(
            array(
                'id'            => "_ka_tinh_trang_sp",
                'name'          => "_ka_tinh_trang_sp",
                'value'         => get_post_meta( $post->ID, '_ka_tinh_trang_sp', true ),
                'label'         => __( 'Tình trạng sản phẩm', 'woocommerce' ),
                'wrapper_class' => 'form-row form-row-full',
                'class'         => ''
            )
        );
    }
 
    // Load data to product info
    function ka_load_variation_settings_fields( $variations ) {
        $variations['_ka_tinh_trang_sp'] = get_post_meta( $variations[ 'variation_id' ], '_ka_tinh_trang_sp', true );
        return $variations;
    }
}
