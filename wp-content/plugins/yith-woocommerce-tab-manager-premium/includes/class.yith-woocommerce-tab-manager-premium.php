<?php
if (!defined('ABSPATH')) {
    exit;
} // Exit if accessed directly

/**
 * Implements features of PREMIUM version of YITH WooCommerce Tab Manager plugin
 *
 * @class   YITH_WC_Tab_Manager_Premium
 * @package YITHEMES
 * @since   1.0.0
 * @author  Your Inspiration Themes
 *
 */

if (!class_exists('YITH_WC_Tab_Manager_Premium')) {

    class YITH_WC_Tab_Manager_Premium extends YITH_WC_Tab_Manager
    {

        /* Priority Tab penalty
        * @var int
        */
        protected $priority = 0;


        public function __construct()
        {

            parent::__construct();

            $this->includes();
            YWTM_Icon();
            YWTM_Product_Tab();


            /* filter for add custom column (type)*/
            add_filter( 'yith_add_column_tab', array( $this, 'add_tab_type_column' ), 10 );

            add_action( 'admin_init', array( $this, 'add_layout_tab_metabox' ), 15 );
            add_action( 'admin_enqueue_scripts', array( $this, 'admin_product_script' ), 20 );
            add_action( 'wp_ajax_yith_json_search_product_categories', array( $this, 'json_search_product_categories' ), 10 );
            add_filter( 'yit_fw_metaboxes_type_args', array( $this, 'add_custom_metaboxes' ) );
            add_action( 'wp_print_scripts', array( $this, 'print_custom_style' ) );

            // register plugin to licence/update system
            add_action( 'wp_loaded', array( $this, 'register_plugin_for_activation' ), 99 );
            add_action( 'admin_init', array( $this, 'register_plugin_for_updates' ) );
            add_filter( 'yith_wctm_post_type', array( $this, 'add_post_type_args' ), 10, 1 );
            add_filter( 'yith_add_column_tab', array( $this, 'add_column_tab' ), 10, 1 );
            add_action( 'ywtm_show_custom_columns', array( $this, 'show_custom_columns' ), 10, 2 );

            if( get_option( 'ywtm_enable_plugin' ) == 'yes' ) {

                //add tabs to woocommerce
                add_filter( 'woocommerce_product_tabs', array( $this, 'show_or_hide_woocommerce_tab' ), 20 );
                $hide_in_mobile = get_option( 'ywtm_hide_tab_mobile' );
                $hide_wc_in_mobile = get_option( 'ywtm_hide_wc_tab_mobile' );

                if( wp_is_mobile() ) {

                    if( 'yes' == $hide_in_mobile ) {
                        remove_filter( 'woocommerce_product_tabs', array( $this, 'add_tabs_woocommerce' ), 98 );
                    }

                    if( 'yes' == $hide_wc_in_mobile ) {
                        add_filter( 'woocommerce_product_tabs', '__return_empty_array', 10 );
                    }
                }

            }


        }

        /**
         * Returns single instance of the class
         *
         * @return YITH_WC_Tab_Manager_Premium
         * @since 1.0.0
         */
        public static function get_instance()
        {


            if( is_null( self::$instance ) ) {
                self::$instance = new self( $_REQUEST );
            }

            return self::$instance;
        }

        /**
         * includes file
         */
        private function includes()
        {

            include_once( 'class-ywtm-icon.php' );
            include_once( 'yith-tab-manager-actions.php' );
            include_once( 'class.yith-product-tab.php' );
            include_once( 'yith-tab-manager-functions.php' );

        }

        /**Include admin script
         * @author YITHEMES
         * @since 1.0.0
         * @use admin_enqueue_scripts
         */
        public function admin_product_script()
        {
            global $post;

            if( isset( $_GET['page'] ) && 'master-slider' === $_GET['page'] ) {
                wp_deregister_script( 'yit-spinner' );
            }

            $current_screen = get_current_screen();
            wp_register_script( 'ywtm_admin_table', YWTM_ASSETS_URL . 'js/backend/' . yit_load_js_file( 'ywtm_admin_table.js' ), array( 'jquery' ), YWTM_VERSION, true );
            wp_register_script( 'yit-tab-manager-script', YWTM_ASSETS_URL . 'js/backend/' . yit_load_js_file( 'admin_tab_product.js' ), array( 'jquery' ), YWTM_VERSION, true );


            if( isset( $current_screen->post_type ) && ( 'ywtm_tab' == $current_screen->post_type || 'product' == $current_screen->post_type ) ) {

                wp_enqueue_script( 'ywtm_admin_table' );
                wp_enqueue_script( 'yit-tab-manager-script' );
                wp_enqueue_script( 'wc-enhanced-select' );
                $params = array(
                    'admin_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
                    'actions' => array(
                        'search_product_categories' => 'yith_json_search_product_categories'
                    ),
                    'security' => wp_create_nonce( "search-product-category" ),
                    'plugin' => YWTM_SLUG

                );
                wp_localize_script( 'yit-tab-manager-script', 'yith_tab_params', $params );

            }

        }


        /**
         * add_tab_metabox
         * Register metabox for global tab
         * @author YITHEMES
         * @since 1.0.0
         */
        public function add_layout_tab_metabox()
        {
            $args = include_once( YWTM_INC . '/metabox/tab-layout-metabox.php' );

            if( !function_exists( 'YIT_Metabox' ) ) {
                require_once( YWTM_DIR . 'plugin-fw/yit-plugin.php' );
            }
            $metabox = YIT_Metabox( 'yit-tab-manager-setting' );
            $metabox->add_tab( $args, 'after', 'settings' );


        }

        /**Add the column "Tab Type" at the Table
         * @param $columns
         * @return mixed
         */
        public function add_tab_type_column( $columns )
        {

            unset( $columns['date'] );
            $columns['tab_type'] = __( 'Tab type', 'yith-woocommerce-tab-manager' );
            $columns['date'] = __( 'Date', 'yith-woocommerce-tab-manager' );

            return $columns;
        }


        /**
         * Print the content columns
         * @param $column
         * @param $post_id
         */
        public function custom_columns( $column, $post_id )
        {

            parent::custom_columns( $column, $post_id );

            switch ( $column ) {
                case 'tab_type' :
                    $type = get_post_meta( $post_id, '_ywtm_tab_type', true );

                    if( empty( $type ) || $type == 'global' ) {
                        echo 'global';
                    }

                    else {
                        echo $type;
                    }

                    break;
            }
        }

        /**get_tab_types
         *
         * return type tabs
         *
         * @author YITHEMES
         * @since 1.0.0
         * @return array
         */
        public function get_tab_types()
        {

            $tab_type = array(
                'global' => __( 'Global Tab', 'yith-woocommerce-tab-manager' ),
                'category' => __( 'Category Tab', 'yith-woocommerce-tab-manager' ),
                'product' => __( 'Product Tab', 'yith-woocommerce-tab-manager' )
            );

            return $tab_type;

        }

        /**return layout type of tabs
         * @author YITHEMES
         * @since 1.0.0
         * @return mixed|void
         */
        public function get_layout_types()
        {

            $tab_layout_types = apply_filters( 'yith_add_layout_tab', array(

                    'default' => __( 'Editor', 'yith-woocommerce-tab-manager' ),
                    'video' => __( 'Video Gallery', 'yith-woocommerce-tab-manager' ),
                    'gallery' => __( 'Image Gallery', 'yith-woocommerce-tab-manager' ),
                    'faq' => __( 'FAQ', 'yith-woocommerce-tab-manager' ),
                    'download' => __( 'Download', 'yith-woocommerce-tab-manager' ),
                    'map' => __( 'Map', 'yith-woocommerce-tab-manager' ),
                    'contact' => __( 'Contact', 'yith-woocommerce-tab-manager' ),
                    'shortcode' => __( 'Shortcode', 'yith-woocommerce-tab-manager' )

                )
            );

            return $tab_layout_types;
        }


        /**
         * add_tabs_woocommerce
         *
         * @since 1.0.0
         * @param $tabs
         * @return mixed
         * @use woocommerce_product_tabs filter
         */
        public function add_tabs_woocommerce( $tabs )
        {
            /**
             * @var WC_Product $product
             */
            global $product;

          
            $product_id = yit_get_product_id( $product );
         
            $yith_tabs = $this->get_tabs();
            $prefix = 'ywtm';
          
            foreach ( $yith_tabs as $tab ) {

                $tab_type = get_post_meta( $tab["id"], '_ywtm_tab_type', true );
                $icon = get_post_meta( $tab['id'], '_ywtm_icon_tab', true );


                switch ( $tab_type ) {

                    case  'product' :

                        $products = ywtm_get_meta( $tab["id"], '_ywtm_tab_product' );

                        if( $products ) {
                            foreach ( $products as $id_prod ) {
                                $key = $prefix . '_' . $tab["id"];
                                $id_prod = yit_wpml_object_id( $id_prod, 'product' );
                                if( $id_prod == $product_id && !$this->is_empty( $key ) ) {

                                    $tabs[$key] = $this->set_single_tab( $tab['title'], $icon, $tab['priority'], 'put_content_tabs' );
                                }
                            }
                        }

                        break;

                    case 'category':

                        $categories = ywtm_get_meta( $tab["id"], '_ywtm_tab_category' );

                        $cat_product = wp_get_post_terms( $product_id, 'product_cat', array( "fields" => "ids" ) );
                        if( $categories ) {
                            foreach ( $categories as $id_cat ) {
                                $key = $prefix . '_' . $tab["id"];
                                $id_cat = yit_wpml_object_id( $id_cat, 'product_cat' );
                                if( in_array( $id_cat, $cat_product ) && !$this->is_empty( $key ) ) {

                                    $tabs[$key] = $this->set_single_tab( $tab['title'], $icon, $tab['priority'], 'put_content_tabs' );
                                }
                            }
                        }

                        break;

                    default :
                        $key = $prefix . '_' . $tab["id"];
                        if( !$this->is_empty( $key ) ) {
                            $tabs[$key] = $this->set_single_tab( $tab['title'], $icon, $tab['priority'], 'put_content_tabs' );
                        }
                      
                        break;
                }

                add_filter( 'woocommerce_product_' . $prefix . '_' . $tab["id"] . '_tab_title', array( $this, 'decode_html_tab' ), 10, 2 );

            }

            return $tabs;
        }

        /** set_single_tab
         *
         * @param $title
         * @param $priority
         * @param $callback
         * @return array
         */
        protected function set_single_tab( $title, $icon, $priority, $callback )
        {
            $tab_icon = '';
            if( !empty( $icon ) ) {

                switch ( $icon['select'] ) {
                    case 'icon' :
                        $tab_icon = '<span class="ywtm_icon"' . YWTM_Icon()->get_icon_data( $icon['icon'] ) . '"></span>';
                        break;
                    case 'custom' :
                        $tab_icon = '<span class="custom_icon" ><img src="' . $icon['custom'] . '" style="max-width :27px;max-height: 25px;" /></span>';
                        break;
                }
            }
            $tab = array(
                'title' => $tab_icon . __( $title, 'yith-woocommerce-tab-manager' ),
                'priority' => $priority+$this->get_priority(),
                'callback' => array( $this, $callback )
            );

     
            return $tab;
        }

        /**print icon tab
         * @param $title
         * @param $key
         * @since 1.1.0
         */
        public function decode_html_tab( $title, $key )
        {
            $title = htmlspecialchars_decode( $title );

            return $title;
        }


        public function is_empty( $key )
        {
            global $product;


            if( substr( $key, 0, 4 ) === 'ywtm' ) {
                $key = explode( '_', $key );
                $key = $key[1];

            }
            $type_content = get_post_meta( $key, '_ywtm_enable_custom_content', true );
            $type_layout = get_post_meta( $key, '_ywtm_layout_type', true );
            $args = array();

            $is_empty = false;

            switch ( $type_layout ) {

                case 'download' :

                    if( true == $type_content ) {
                        $args['download'] = get_post_meta( $key, '_ywtm_download', true );
                    }
                    else {
                        $args['download'] = yit_get_prop( $product, $key . '_custom_list_file', true );
                    }

                    $is_empty = empty( $args['download'] );
                    break;

                case 'faq' :

                    if( true == $type_content ) {
                        $args['faqs'] = get_post_meta( $key, '_ywtm_faqs', true );
                    }

                    else {
                        $args['faqs'] = yit_get_prop( $product, $key . '_custom_list_faqs', true );
                    }

                    $is_empty = empty( $args['faqs'] );
                    break;

                case 'map' :

                    if( true == $type_content ) {
                        $address = get_post_meta( $key, '_ywtm_google_map_overlay_address', true );

                    }
                    else {
                        $args['map'] =  yit_get_prop( $product, $key . '_custom_map', true );
                        $address = isset( $args['map']['addr'] ) ? $args['map']['addr'] : '';
                    }

                    $is_empty = empty( $address );
                    break;

                case 'contact':

                    if( true == $type_content ) {
                        $args['form'] = get_post_meta( $key, '_ywtm_form_tab', true );
                    }
                    else {
                        $args['form'] =  yit_get_prop( $product, $key . '_custom_form', true );
                    }

                    $is_empty = empty( $args['form'] );
                    break;

                case 'gallery':

                    if( true == $type_content ) {

                        $gallery = get_post_meta( $key, '_ywtm_gallery', true );

                    }
                    else {

                        $result =  yit_get_prop( $product, $key . '_custom_gallery', true );
                        $gallery = isset( $result['images'] ) && !empty( $result['images'] ) ? 'gallery' : '';

                    }
                    $is_empty = empty( $gallery );
                    break;

                case 'video':

                    if( true == $type_content ) {
                        $result = get_post_meta( $key, '_ywtm_video', true );
                        $video = $result['video_info'];


                    }
                    else {

                        $result =  yit_get_prop( $product, $key . '_custom_video', true );
                        $video = $result ? 'video' : '';
                    }

                    $is_empty = empty( $video );
                    break;

                case 'shortcode':
                    if( true == $type_content ) {

                        $args['shortcode'] = get_post_meta( $key, '_ywtm_shortcode_tab', true );
                    }
                    else {
                        $args['shortcode'] =  yit_get_prop( $product, $key . '_custom_shortcode', true );
                    }

                    $is_empty = empty( $args['shortcode'] );
                    break;

                default :

                    if( true == $type_content ) {
                        $args['content'] = get_post_meta( $key, '_ywtm_text_tab', true );
                    }
                    else {

                        $args['content'] =  yit_get_prop( $product, $key . '_default_editor', true );
                    }

                    $is_empty = empty( $args['content'] );
                    break;
            }

            return $is_empty;
        }

        /**
         * put_content_tabs
         * Put the content at the tabs
         * @param $key
         * @param $tab
         */
        public function put_content_tabs( $key, $tab )
        {

            global $product;

          
            if( substr( $key, 0, 4 ) === 'ywtm' ) {
                $key = explode( '_', $key );
                $key = $key[1];

            }
            $type_content = get_post_meta( $key, '_ywtm_enable_custom_content', true );
            $type_layout = get_post_meta( $key, '_ywtm_layout_type', true );
            $args = array();

            switch ( $type_layout ) {

                case 'download' :

                    if( true == $type_content ) {
                        $args['download'] = get_post_meta( $key, '_ywtm_download', true );
                    }
                    else {
                        $args['download'] = yit_get_prop( $product, $key . '_custom_list_file', true );
                    }

                    yit_plugin_get_template( YWTM_DIR, 'download.php', $args );
                    break;

                case 'faq' :

                    if( true == $type_content ) {
                        $args['faqs'] = get_post_meta( $key, '_ywtm_faqs', true );
                    }

                    else {
                        $args['faqs'] = yit_get_prop( $product, $key . '_custom_list_faqs', true );
                    }

                    yit_plugin_get_template( YWTM_DIR, 'faq.php', $args );
                    break;

                case 'map' :

                    if( true == $type_content ) {
                        $address = get_post_meta( $key, '_ywtm_google_map_overlay_address', true );
                        $width = get_post_meta( $key, '_ywtm_google_map_width', true );
                        $height = get_post_meta( $key, '_ywtm_google_map_height', true );
                        $zoom = get_post_meta( $key, '_ywtm_google_map_overlay_zoom', true );
                        $show_w = get_post_meta( $key, '_ywtm_google_map_full_width', true );

                        /*addr, heig,wid,zoom*/
                        $map_setting = array(
                            'addr' => $address,
                            'wid' => $width,
                            'heig' => $height,
                            'zoom' => $zoom,
                            'show_width' => $show_w
                        );

                        $args['map'] = $map_setting;

                    }
                    else {
                        $args['map'] = yit_get_prop( $product, $key . '_custom_map', true );
                    }

                    yit_plugin_get_template( YWTM_DIR, 'map.php', $args );
                    break;

                case 'contact':

                    if( true == $type_content ) {
                        $args['form'] = get_post_meta( $key, '_ywtm_form_tab', true );
                    }
                    else {
                        $args['form'] = yit_get_prop( $product, $key . '_custom_form', true );
                    }

                    yit_plugin_get_template( YWTM_DIR, 'contact_form.php', $args );
                    break;

                case 'gallery':

                    if( true == $type_content ) {
                        $columns = get_post_meta( $key, '_ywtm_gallery_columns', true );
                        $gallery = get_post_meta( $key, '_ywtm_gallery', true );
                        $args['images'] = array( 'columns' => $columns, 'gallery' => $gallery );
                    }
                    else {

                        $result = yit_get_prop( $product, $key . '_custom_gallery', true );
                        if( isset( $result['settings'] ) ) {
                            $columns = $result['settings']['columns'];

                            $gallery = '';

                            foreach ( $result['images'] as $key => $image )
                                $gallery .= $image['id'] . ',';

                            if( substr( $gallery, -1 ) == ',' ) {
                                $gallery = substr( $gallery, 0, -1 );
                            }

                            $args['images'] = array( 'columns' => $columns, 'gallery' => $gallery );
                        }
                    }

                    $args['tab_id'] = $key;
                    yit_plugin_get_template( YWTM_DIR, 'image_gallery.php', $args );
                    break;

                case 'video':

                    if( true == $type_content ) {
                        $result = get_post_meta( $key, '_ywtm_video', true );

                        $columns = $result['columns'];
                        $video = $result['video_info'];
                        $args['videos'] = array( 'columns' => $columns, 'video' => $video );

                    }
                    else {

                        $result = yit_get_prop( $product, $key . '_custom_video', true );

                        if( $result ) {
                            $columns = $result['settings']['columns'];
                            $video = $result['video'];
                            $args['videos'] = array( 'columns' => $columns, 'video' => $video );
                        }

                    }

                    yit_plugin_get_template( YWTM_DIR, 'video_gallery.php', $args );
                    break;

                case 'shortcode':
                    if( true == $type_content ) {

                        $args['shortcode'] = get_post_meta( $key, '_ywtm_shortcode_tab', true );
                    }
                    else {
                        $args['shortcode'] = yit_get_prop( $product, $key . '_custom_shortcode', true );
                    }

                    yit_plugin_get_template( YWTM_DIR, 'shortcode.php', $args );
                    break;

                default :

                    if( true == $type_content ) {
                        $args['content'] = get_post_meta( $key, '_ywtm_text_tab', true );
                    }
                    else {

                        $args['content'] = yit_get_prop( $product, $key . '_default_editor', true );
                    }

                    yit_plugin_get_template( YWTM_DIR, $this->_default_layout . '.php', $args );
                    break;
            }


        }

        /** show or hide default tabs
         * @author YITHEMES
         * @since 1.0.0
         * @param $tabs
         * @return mixed
         */
        public function show_or_hide_woocommerce_tab( $tabs )
        {

            $tab_type = array( 'description', 'reviews', 'additional_information' );
            $option_name = array(
                'description' => 'ywtm_hide_wc_desc_tab_in_mobile',
                'reviews' => 'ywtm_hide_wc_reviews_tab',
                'additional_information' => 'ywtm_hide_wc_addinfo_tab'
            );
            global $product;

            foreach ( $tab_type as $type ) {

                $is_hide = yit_get_prop( $product, '_ywtm_hide_' . $type, true );
                $is_over = yit_get_prop( $product, '_ywtm_override_' . $type, true );
                $global_hide_option = get_option( $option_name[$type] );
                
              
                if( $is_hide === 'yes' || 'yes' === $global_hide_option ) {
                    unset( $tabs[$type] );
                }
                elseif( $is_over === 'yes' ) {

                    $title = yit_get_prop( $product, '_ywtm_title_tab_' . $type, true );

                    $tabs[$type]['priority'] = yit_get_prop( $product, '_ywtm_priority_tab_' . $type, true );
                    $tabs[$type]['title'] = $type === 'reviews' ? str_replace( '%d', $product->get_review_count(), $title ) : $title;

                    if( $type === 'description' ) {

                        $tabs[$type]['callback'] = array( $this, 'ywtm_custom_wc_description_content' );
                    }
                }

            }

            return $tabs;
        }

        /**
         * get custom content for description tab
         * @author YITHEMES
         * @since 1.1.0
         */
        public function ywtm_custom_wc_description_content()
        {

            global $product;
            $content = yit_get_prop( $product, '_ywtm_content_tab_description', true );

            $args = array(
                'content' => $content
            );
            yit_plugin_get_template( YWTM_DIR, $this->_default_layout . '.php', $args );
        }


        /**get_tab_categories
         *
         * Load all categories in Category chosen field
         * @author YITHEMES
         * @since 1.0.0
         * @return array
         */
        public function get_tab_categories()
        {

            $args = array( 'hide_empty' => 0 );

            $categories_term = get_terms( 'product_cat', $args );

            $categories = array();

            foreach ( $categories_term as $category ) {

                $categories[$category->term_id] = '#' . $category->term_id . ' - ' . $category->name;
            }

            return $categories;

        }

        /**json_search_product_categories
         * search product category in taxonomy 'product_cat'
         * @param string $x
         * @param array $taxonomy_types
         * @author YITHEMES
         * @return json
         */
        public function json_search_product_categories( $x = '', $taxonomy_types = array( 'product_cat' ) )
        {

            if( isset( $_GET['plugin'] ) && YWTM_SLUG == $_GET['plugin'] ) {

                global $wpdb;
                $term = (string)urldecode( stripslashes( strip_tags( $_GET['term'] ) ) );
                $term = "%" . $term . "%";


                $query_cat = $wpdb->prepare( "SELECT {$wpdb->terms}.term_id,{$wpdb->terms}.name
                                   FROM {$wpdb->terms} INNER JOIN {$wpdb->term_taxonomy} ON {$wpdb->terms}.term_id = {$wpdb->term_taxonomy}.term_id
                                   WHERE {$wpdb->term_taxonomy}.taxonomy IN (%s) AND {$wpdb->terms}.slug LIKE %s", implode( ",", $taxonomy_types ), $term );

                $product_categories = $wpdb->get_results( $query_cat );

                $to_json = array();

                foreach ( $product_categories as $product_category ) {

                    $to_json[$product_category->term_id] = "#" . $product_category->term_id . " - " . $product_category->name;
                }

                wp_send_json( $to_json );
            }

        }


        /**Enable custom metabox type
         * @author YITHEMES
         * @param $args
         * @use yit_fw_metaboxes_type_args
         * @return mixed
         */
        public function add_custom_metaboxes( $args )
        {
            $custom_types = array(
                'faqs',
                'downloads',
                'video-gallery',
                'forms',
                'iconlist',
                'ywctab-ajax-product',
                'ywctab-ajax-category'
            );
            if(  in_array( $args['type'], $custom_types ) ) {
                $args['basename'] = YWTM_DIR;
                $args['path'] = 'metaboxes/types/';
            }



            return $args;
        }

        /**
         * Register plugins for activation tab
         *
         * @return void
         * @since    2.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_activation()
        {
            if( !class_exists( 'YIT_Plugin_Licence' ) ) {
                require_once YWTM_DIR . 'plugin-fw/licence/lib/yit-licence.php';
                require_once YWTM_DIR . 'plugin-fw/licence/lib/yit-plugin-licence.php';
            }
            YIT_Plugin_Licence()->register( YWTM_INIT, YWTM_SECRET_KEY, YWTM_SLUG );
        }

        /**
         * Register plugins for update tab
         *
         * @return void
         * @since    1.0.0
         * @author   Andrea Grillo <andrea.grillo@yithemes.com>
         */
        public function register_plugin_for_updates()
        {
            if( !class_exists( 'YIT_Upgrade' ) ) {
                require_once( YWTM_DIR . 'plugin-fw/lib/yit-upgrade.php' );
            }
            YIT_Upgrade()->register( YWTM_SLUG, YWTM_INIT );
        }

        /**
         * print custom style
         * @author YITHEMES
         * @since 1.1.1
         */
        public function print_custom_style()
        {

            if( is_product() ) {
                $custom_style = get_option( 'ywtm_custom_style' );

                if( !empty( $custom_style ) ) {
                    ?>

                    <style type="text/css">
                        <?php echo $custom_style;?>
                    </style>
                    <?php
                }
            }
        }

        /**
         * add args into post type
         * @author YITHEMES
         * @since 1.1.15
         * @param $post_type_args
         */
        public function add_post_type_args( $post_type_args )
        {

            $post_type_args['supports'][] = 'excerpt';

            return $post_type_args;
        }

        /**
         * add column description
         * @author YITHEMES
         * @since 1.1.15
         * @param $columns
         * @return array
         */
        public function add_column_tab( $columns )
        {

            $desc_column = array( 'description' => __( 'Description', 'yith-woocommerce-tab-manager' ) );

            $k = array_search( 'title', array_keys( $columns ) );

            $new_columns = array_slice( $columns, 0, $k+1, true )+$desc_column+array_slice( $columns, $k, count( $columns )-1, true );

            return $new_columns;
        }

        /**
         * show the description content
         * @author YITHEMES
         * @since 1.1.15
         * @param $column
         * @param $post_id
         */
        public function show_custom_columns( $column, $post_id )
        {
            if( 'description' == $column ) {

                $post = get_post( $post_id );
                $description = $post->post_excerpt;

                if( empty( $description ) ) {
                    $description = __( 'No description', 'yith-woocommerce-tab-manager' );
                }
                $desc = sprintf( '<div class="ywtm_description show_more" data-max_char="%s" data-more_text="%s" data-less_text="%s">%s</div>', 80, __( 'Show more', 'yith-woocommerce-tab-manager' ), __( 'Show less', 'yith-woocommerce-tab-manager' ), $description );
                echo $desc;
            }
        }

    }
}