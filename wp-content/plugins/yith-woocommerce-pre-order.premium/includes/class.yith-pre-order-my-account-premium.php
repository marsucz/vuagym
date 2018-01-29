<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
    exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Pre_Order_My_Account_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_My_Account_Premium' ) ) {
    /**
     * Class YITH_Pre_Order_My_Account_Premium
     *
     * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
     */
    class YITH_Pre_Order_My_Account_Premium extends YITH_Pre_Order_My_Account {

        /**
         * Construct
         *
         * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
         * @since 1.0
         */
        public function __construct() {
            parent::__construct();

            add_filter( 'ywpo_my_pre_orders_columns', array( $this, 'my_pre_orders_columns' ) );
            add_filter( 'ywpo_my_pre_orders_show_row', array( $this, 'my_pre_orders_show_row' ), 10, 2 );
            add_action( 'ywpo_my_pre_orders_extra_columns', array( $this, 'my_pre_orders_release_date' ) );
        }


        public function add_pre_order_info_on_single_order_page( $item_id, $item, $order ) {
	        $is_pre_order = ! empty( $item['ywpo_item_preorder'] ) ? $item['ywpo_item_preorder'] : '';
            $timestamp = ! empty( $item['ywpo_item_for_sale_date'] ) ? $item['ywpo_item_for_sale_date'] : '';
            if ( 'yes' == $is_pre_order ) {
                echo '<div><span>' . __( 'Pre-Order product', 'yith-woocommerce-pre-order' ) . '</span>';
                echo '<div>' . __( 'Release date: ', 'yith-woocommerce-pre-order' );
                if ( $timestamp ) {
                    ?>
                    <div class="preorder-my-account" data-time="<?php echo $timestamp; ?>">
                        <span class="preorder-date">
                            <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'Y/m/d' );?>
                        </span>
                        <?php echo __( 'at', 'yith-woocommerce-pre-order' ); ?>
                        <span class="preorder-time">
                            <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'H:i' );?>
                        </span>
                        <?php
                        $gmt_offset = get_option( 'gmt_offset' );

                        if ( 0 <= $gmt_offset )
                            $offset_name = '+' . $gmt_offset;
                        else
                            $offset_name = (string)$gmt_offset;

                        $offset_name = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
                        $offset_name = '(UTC' . $offset_name . ')';
                        echo ' ' . $offset_name;
                        ?>
                    </div>
                    <?php
                } else {
                    echo __( 'N/A', 'yith-woocommerce-pre-order' );
                }
                echo '</div></div>';
            }

        }


        public function my_pre_orders_columns( $columns_array ) {
            return array(
                __( 'Product', 'yith-woocommerce-pre-order' ),
                __( 'Order', 'yith-woocommerce-pre-order' ),
	            __( 'Price', 'yith-woocommerce-pre-order' ),
	            __( 'Release date', 'yith-woocommerce-pre-order' )
            );
        }

        public function my_pre_orders_show_row( $bool, $item ) {
	        $item_is_pre_order = ! empty( $item['ywpo_item_preorder'] ) ? $item['ywpo_item_preorder'] : '';
	        $timestamp = ! empty( $item['ywpo_item_for_sale_date'] ) ? $item['ywpo_item_for_sale_date'] : '';

            return 'yes' == $item_is_pre_order && ( is_numeric( $timestamp ) && $timestamp > time() || empty( $timestamp ) );
        }

        public function my_pre_orders_release_date( $item ) {
            echo '<td>';
	        $timestamp = ! empty( $item['ywpo_item_for_sale_date'] ) ? $item['ywpo_item_for_sale_date'] : '';

            if ( $timestamp ) {
                ?>
                <div class="preorder-my-account" data-time="<?php echo $timestamp; ?>">
                    <span class="preorder-date"></span> <?php _e( 'at', 'yith-woocommerce-pre-order' ); ?>
                    <span class="preorder-time"></span>
                </div>
                <?php
            }
            else {
                echo __( 'N/A', 'yith-woocommerce-pre-order' );
            }
            echo '</td>';
        }


    }

}