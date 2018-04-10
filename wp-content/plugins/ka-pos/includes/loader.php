<?php

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
    }
    
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
//            add_post_meta($post_id, '_mypos_other_store', $is_other_store);
            update_post_meta($post_id, '_mypos_other_store', $is_other_store);
    }

    public function mypos_save_variable_fields( $post_id, $_i ) {
            $is_other_store = isset( $_POST['_mypos_other_store'][ $_i ] ) ? 'yes' : 'no';
//            add_post_meta($post_id, '_mypos_other_store', $is_other_store);
            update_post_meta($post_id, '_mypos_other_store', $is_other_store);
    }
}
