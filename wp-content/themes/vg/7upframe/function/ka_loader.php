<?php
/**
 * @author Tuan Dao
 */
class ka_loader {
    
    public function __construct() {
        add_action( 'woocommerce_process_product_meta', array( $this, 'mypos_update_settings' ));
        add_action( 'woocommerce_save_product_variation', array( $this, 'mypos_save_variable_fields' ), 10, 2 );
        
        //Tinh trang san pham
        add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'ka_add_variation_info_tinh_trang_sp' ), 10, 3 );
        add_action( 'woocommerce_product_options_general_product_data', array ($this, 'ka_add_simple_info_tinh_trang_sp') );
        add_filter( 'woocommerce_available_variation', array( $this, 'ka_load_variation_settings_fields' ) );
    }
    
    public function mypos_update_settings( $post_id ) { 
        //Tinh trang san pham Simple
        if (isset($_POST['_ka_tinh_trang_sp'])) {
            update_post_meta($post_id, '_ka_tinh_trang_sp', $_POST['_ka_tinh_trang_sp']);
        }
    }

    public function mypos_save_variable_fields( $post_id, $_i ) {
        //Tinh trang san pham
        if (isset( $_POST['_ka_tinh_trang_sp'][ $_i ] )) {
            $tinh_trang_sp = $_POST['_ka_tinh_trang_sp'][ $_i ];
            update_post_meta($post_id, '_ka_tinh_trang_sp', $tinh_trang_sp);
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
        $tinh_trang_sp = get_post_meta( $variations[ 'variation_id' ], '_ka_tinh_trang_sp', true );
        if ($tinh_trang_sp) {
            $tinh_trang_sp = '<p>' . $tinh_trang_sp . '</p>';
        }
        $variations['variation_tinhtrang'] = $tinh_trang_sp;
        return $variations;
    }
}
