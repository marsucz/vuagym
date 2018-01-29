<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if (!defined('YITH_WCPO_VERSION')) {
    exit('Direct access forbidden.');
}

/**
 *
 *
 * @class      YITH_Pre_Order_Admin
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if (!class_exists('YITH_Pre_Order_Admin')) {
    /**
     * Class YITH_Pre_Order_Admin
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Pre_Order_Admin
    {

        /**
         * @var $_edit_product_page YITH_Pre_Order_Admin Edit product page object
         */
        protected $_edit_product_page;
        

        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0
         */
        public function __construct() {
            require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-edit-product-page.php' );

            $this->instantiate_edit_product_page();
            
            add_filter( 'views_edit-shop_order', array( $this, 'add_pre_ordered_orders_page_view' ) );
            add_action( 'pre_get_posts', array( $this, 'filter_order_for_view' ) );
        }


        public function instantiate_edit_product_page() {
            return $this->_edit_product_page = new YITH_Pre_Order_Edit_Product_Page();
        }


        public function add_pre_ordered_orders_page_view( $views ) {
            $order_statuses = wc_get_order_statuses();
            if ( 'yes' == get_option( 'yith_wcpo_wc-completed' ) ) {
                unset( $order_statuses['wc-completed'] );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-cancelled' ) ) {
                unset( $order_statuses['wc-cancelled'] );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-refunded' ) ) {
                unset( $order_statuses['wc-refunded'] );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-failed' ) ) {
                unset( $order_statuses['wc-failed'] );
            }
            $args = array(
                'post_type'   => wc_get_order_types(),
                'post_status' => array_keys( $order_statuses ),
                'numberposts' => - 1,
                'fields'      => 'ids',
                'meta_query'  => array(
                    array(
                        'key'     => '_order_has_preorder',
                        'value'   => 'yes',
                        'compare' => '='
                    )
                ));
            // Get all Pre-Order ids
            $pre_ordered_count = get_posts( $args );

            if( $pre_ordered_count ){
                $filter_url = esc_url( add_query_arg( array( 'post_type' => 'shop_order', 'pre-ordered' => true ), admin_url( 'edit.php' ) ) );
                $filter_class = isset( $_GET['pre-ordered'] ) ? 'current' : '';

                $views[ 'pre-ordered' ] = sprintf( '<a href="%s" class="%s">%s <span class="count">(%d)</span></a>',
                    $filter_url, $filter_class, __( 'Pre-Ordered', 'yith-woocommerce-pre-order' ), count( $pre_ordered_count ) );
            }
            return $views;
        }


        public function filter_order_for_view() {
            if( isset( $_GET['pre-ordered'] ) && $_GET['pre-ordered'] ){
                add_filter( 'posts_join', array( $this, 'filter_order_join_for_view' ) );
                add_filter( 'posts_where', array( $this, 'filter_order_where_for_view' ) );
            }
        }

        /**
         * Add joins to order view query
         *
         * @param $join string Original join query section
         * @return string filtered join query section
         * @since 1.0.0
         */
        public function filter_order_join_for_view( $join ) {
            global $wpdb;

            $join .= " LEFT JOIN {$wpdb->prefix}postmeta as i ON {$wpdb->posts}.ID = i.post_id";

            return $join;
        }

        /**
         * Add conditions to order view query
         *
         * @param $where string Original where query section
         * @return string filtered where query section
         * @since 1.0.0
         */
        public function filter_order_where_for_view( $where ) {
            global $wpdb;

            $where .= $wpdb->prepare( " AND i.meta_key = %s AND i.meta_value = %s", array( '_order_has_preorder', 'yes' ) );
            if ( 'yes' == get_option( 'yith_wcpo_wc-completed' ) ) {
                $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_status != %s", array( 'wc-completed' ) );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-cancelled' ) ) {
                $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_status != %s", array( 'wc-cancelled' ) );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-refunded' ) ) {
                $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_status != %s", array( 'wc-refunded' ) );
            }
            if ( 'yes' == get_option( 'yith_wcpo_wc-failed' ) ) {
                $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_status != %s", array( 'wc-failed' ) );
            }

            return $where;
        }

    }
}