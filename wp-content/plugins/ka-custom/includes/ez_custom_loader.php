<?php

function ez_custom_load_remove_notice_js() {
            wp_enqueue_script('remove_notice_js', EZ_CUSTOM_PLUGIN_URL . 'assets/admin/js/remove_notice.js');

            if (!get_option('ezcustom_remove_notice')) {
                wp_dequeue_script( 'remove_notice_js' );
            }
}

add_action('admin_head', 'ez_custom_expand_categories_list');

function ez_custom_expand_categories_list() {
    
    $max_height = 200 * (float)get_option('ezcustom_expand_categories_list');
    
    if ($max_height) {
        echo '<style>
            .categorydiv div.tabs-panel {
              max-height: ' . $max_height .  'px !important;
            } 
          </style>';
    } else {
        echo '<style>
            .categorydiv div.tabs-panel {
              max-height: unset !important;
            } 
          </style>';
    }
}

if( ! class_exists( 'EZ_Custom_Loader' ) ) {
    
    class EZ_Custom_Loader {

        public function load_assets_page_options() {

            $this->load_assets_common_admin();

            wp_enqueue_script(
                        'global',
                        EZ_CUSTOM_PLUGIN_URL . 'assets/admin/js/page_options.js',
                        array( 'jquery' ),
                        '1.0.0',
                        true
            );

        }

        public function load_assets_common_admin() {

            // JS
            wp_register_script('prefix_bootstrap', EZ_CUSTOM_PLUGIN_URL . 'assets/admin/lib/bootstrap.min.js');
            wp_enqueue_script('prefix_bootstrap');
            wp_register_script('prefix_jquery', EZ_CUSTOM_PLUGIN_URL . 'assets/admin/lib/jquery-3.3.1.min.js');
            wp_enqueue_script('prefix_jquery');

            // CSS
            wp_register_style('prefix_bootstrap', EZ_CUSTOM_PLUGIN_URL . 'assets/admin/css/bootstrap.min.css');
            wp_enqueue_style('prefix_bootstrap');
            wp_enqueue_style('my-styles', EZ_CUSTOM_PLUGIN_URL . 'assets/admin/css/styles.css' );
        }
    }
}

?>