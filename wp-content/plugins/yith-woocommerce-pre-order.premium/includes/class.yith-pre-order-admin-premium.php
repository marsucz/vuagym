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
 * @class      YITH_Pre_Order_Admin_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if (!class_exists('YITH_Pre_Order_Admin_Premium')) {
	/**
	 * Class YITH_Pre_Order_Admin_Premium
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Admin_Premium extends YITH_Pre_Order_Admin {

		/**
		 * @var $ONE_WEEK YITH_Pre_Order_Admin A week on timestamp
		 */
		private static $ONE_WEEK = 604800;

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since 1.0
		 */
		public function __construct() {
			require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-edit-product-page-premium.php' );
			parent::__construct();

			add_filter( 'woocommerce_locate_core_template', array( $this, 'locate_core_template' ), 10, 3 );
			add_filter( 'woocommerce_email_actions', array( $this, 'add_for_sale_date_changed_trigger_action' ) );
			add_filter( 'manage_edit-shop_order_columns',  array( $this, 'add_pre_order_column' ) );
			add_filter( 'manage_edit-product_columns',  array( $this, 'add_pre_order_column' ) );
			add_action( 'manage_shop_order_posts_custom_column', array( $this, 'add_pre_order_column_content_orders' ), 10, 2 );
			add_action( 'manage_product_posts_custom_column', array( $this, 'add_pre_order_column_content_products' ), 10, 2 );
			add_filter( 'yith_wcpo_settings_options', array( $this, 'premium_settings_options' ) );
		}

		public function instantiate_edit_product_page() {
			return $this->_edit_product_page = new YITH_Pre_Order_Edit_Product_Page_Premium();
		}

		/**
		 * Locate core template file
		 *
		 * @param $core_file
		 * @param $template
		 * @param $template_base
		 *
		 * @return array Vendor capabilities
		 * @since  1.0
		 */
		public function locate_core_template( $core_file, $template, $template_base ) {
			$custom_template = array(
				//HTML Email
				'emails/pre-order-date-changed.php',
				// Plain Email
				'emails/plain/pre-order-date-changed.php',

				'emails/pre-order-date-end.php',

				'emails/pre-order-is-for-sale.php',

				'emails/pre-order-out-of-stock.php',
			);

			if ( in_array( $template, $custom_template ) ) {
				$core_file = YITH_WCPO_TEMPLATE_PATH . $template;
			}

			return $core_file;
		}

		public function add_for_sale_date_changed_trigger_action($actions) {
			$actions[] = 'yith_for_sale_date';
			return $actions;
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

		public function add_pre_order_column( $column_array ) {
			$column_array['pre_order'] = __( 'Pre-Order', 'yith-woocommerce-pre-order' );
			return $column_array;
		}

		public function add_pre_order_column_content_orders( $column_name, $post_id ) {
			if ( $column_name == 'pre_order' ) {
				$order = wc_get_order( $post_id );
				$order_has_preorder = yit_get_prop( $order, '_order_has_preorder', true );
				if ( 'yes' != $order_has_preorder ) {
					return;
				}
				$pre_order_dates = array();
				foreach ( $order->get_items() as $item ) {
					if ( $item instanceof WC_Data ) {
						if ( is_callable( array( $item, 'get_product' ) ) ) {
							$product = $item->get_product();
						} else {
							continue;
						}
					} else {
						$product = $order->get_product_from_item( $item );
					}
					if ( ! ( $product instanceof WC_Product ) ) {
						continue;
					}
					$is_pre_order = ! empty( $item['ywpo_item_preorder'] ) ? $item['ywpo_item_preorder'] : '';
					$timestamp = ! empty( $item['ywpo_item_for_sale_date'] ) ? $item['ywpo_item_for_sale_date'] : '';
					if ( 'yes' == $is_pre_order && ( $timestamp > time() || empty( $timestamp ) ) ) {
						$array_data['date'] = $timestamp;
						$array_data['product_id'] = yit_get_product_id( $product );
						$pre_order_dates[] = $array_data;
					}
				}
				if ( $pre_order_dates ) {
					sort( $pre_order_dates );
					$showing_dates = array();
					foreach ( $pre_order_dates as $date ) {
						$array_data = array();
						$array_data['product_id'] = $date['product_id'];
						if ( empty( $date['date'] ) ) {
							$array_data['date'] = __( 'No date set', 'yith-woocommerce-pre-order' );
						} else {
							$array_data['date'] = get_date_from_gmt( date( 'Y-m-d H:i:s', $date['date'] ), 'Y/m/d H:i' );
						}

						if ( empty( $date['date'] ) ) {
							$array_data['icon_color'] = 'pre_order_no_date';
						} else if ( time() > $date['date'] ) {
							$array_data['icon_color'] = 'pre_order_finished';
						} else if ( time() > $date['date'] - self::$ONE_WEEK ) {
							$array_data['icon_color'] = 'pre_order_last_week';
						} else {
							$array_data['icon_color'] = '';
						}
						$showing_dates[] = $array_data;
					}
					foreach ( $showing_dates as $date ) {
						$product = wc_get_product( $date['product_id'] );
						if ( ! $product ) {
							continue;
						}
						?><div class="pre_order_icon_orders_table <?php echo $date['icon_color']; ?>"
                               title="<?php
						       if ( 'pre_order_no_date' == $date['icon_color'] ) {
							       echo __( 'This Pre-Order product has no release date.', 'yith-woocommerce-pre-order' );
						       } else if ( 'pre_order_finished' == $date['icon_color'] ) {
							       echo __( 'The Pre-Order end date has passed.', 'yith-woocommerce-pre-order' );
						       } else if ( 'pre_order_last_week' == $date['icon_color'] ) {
							       echo __( 'The Pre-Order expires in a week or less.', 'yith-woocommerce-pre-order' );
						       } else {
							       echo __( 'Pre-Order end date.', 'yith-woocommerce-pre-order' );
						       }
						       ?>"
                        >&nbsp;
                        <a href="<?php echo get_edit_post_link( yit_get_product_id( $product ) ); ?>"
                           title="<?php echo $product->get_title(); ?>"><?php echo $product->get_formatted_name(); ?></a><?php
						echo ' - ' .  $date['date'];
						?></div><?php
					}
				} else {
					echo '<div>' . __( 'The release date for all Pre-Order products have passed.', 'yith-woocommerce-pre-order' ) . '</div>';
				}

				?>
				<?php

			}
		}

		public function add_pre_order_column_content_products( $column_name, $post_id ) {
			if ( $column_name == 'pre_order' && $post_id ) {
				if ( ! is_numeric( $post_id ) ) {
					return;
				}
				$product = wc_get_product( $post_id );
				if ( 'variable' == $product->get_type() && $product->has_child() ) {
					$variations = $product->get_children();
					foreach ( $variations as $variation_id ) {
						$pre_order_variation = new YITH_Pre_Order_Product( $variation_id );
						if ( 'yes' == $pre_order_variation->get_pre_order_status() ) {
							echo '<div>';
							echo '<img src="' . YITH_WCPO_ASSETS_URL . 'images/check-circle.png' . '">';
							echo '<span class="ywpo_pre_order_check_icon">'
							     . __( 'One or more variations', 'yith-woocommerce-pre-order' ) . '</span>';
							echo '</div>';
							break;
						}
					}
				} else if ( 'simple' == $product->get_type() ) {
					$pre_order = new YITH_Pre_Order_Product( $post_id );
					if ( 'yes' == $pre_order->get_pre_order_status() ) {
						echo '<img src="' . YITH_WCPO_ASSETS_URL . 'images/check-circle.png' . '"> </img>';
						if ( $pre_order->get_for_sale_date() ) {
							echo '<span class="ywpo_pre_order_check_icon">' . $pre_order->get_for_sale_date() . '</span>';
						} else {
							echo '<span class="ywpo_pre_order_check_icon">'
							     . __( 'No date', 'yith-woocommerce-pre-order' ) . '</span>';
						}
					}
				}
			}
		}

		public function premium_settings_options( $settings ) {
			$settings['option1'] = array(
				'title'   => _x( 'Remove Pre-Order status when the release date passes', 'Admin option: Automatic purchasable Pre-order products',
                    'yith-woocommerce-pre-order' ),
				'type'    => 'checkbox',
				'desc'    => _x( 'By enabling this option, the Pre-Order status is removed as soon as the Pre-Order date is passed. If not checked, you will have to remove the Pre-Order status first.', 'Admin option description: Automatic
				purchasable Pre-order products', 'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_enable_pre_order_purchasable',
				'default' => 'yes'
			);
			$settings['option2'] = array(
				'title'   => __( 'Automatic Pre-Order status', 'yith-woocommerce-pre-order' ),
				'type'    => 'checkbox',
				'desc'    => __( 'By enabling this option, products currently out of stock automatically acquire the Pre-Order status and the admin receives an email. When products are in stock again, they will automatically lose the Pre-Order status. If this option is enabled, changing the Pre-Order status manually will be disabled.', 'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_enable_pre_order_auto_outofstock_notification',
				'default' => 'no'
			);
			$settings['option3'] = array(
				'title'   => __( 'Allow sales of out of stock products', 'yith-woocommerce-pre-order' ),
				'type'    => 'checkbox',
				'desc'    => __( 'By enabling this option, Pre-Order products with no stock can be purchased. (Requires WooCommerce 3.0 or higher)',
                    'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_allow_out_of_stock_selling',
				'default' => 'no'
			);
			$settings['option4'] = array(
				'title'   => _x( 'Automatic date formatting', 'Admin option: automatic date formatting', 'yith-woocommerce-pre-order' ),
				'type'    => 'checkbox',
				'desc'    => __( 'Check this option in order to display automatic date format and timezone in the frontend based on user location.',
                    'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_enable_automatic_date_formatting',
				'default' => 'yes'
			);
			$settings['option5'] = array(
				'title'   => _x( 'Prevent mixing products in cart', 'Admin option', 'yith-woocommerce-pre-order' ),
				'type'    => 'checkbox',
				'desc'    => __( 'If you enable this option, the cart cannot contain Pre-Order products and regular products at the same time.', 'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_mixing',
				'default' => 'false'
			);
			$settings['option6'] = array(
				'title'   => __( 'Show Regular price crossed out', 'yith-woocommerce-pre-order' ),
				'type'    => 'checkbox',
				'desc'    => __( 'Whether to show the Regular price (crossed out) next to the Pre-Order price or Pre-Order price only. If this option is enabled, the Pre-Order price will replace the Sale price, if available. (Requires WooCommerce 3.0 or higher)', 'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_show_regular_price',
				'default' => 'yes'
			);
			$settings['option7'] = array(
				'title'   => __( 'Pre-Order price for guest users', 'yith-woocommerce-pre-order' ),
				'type'    => 'select',
				'desc'    => __( 'Select what guest users can see', 'yith-woocommerce-pre-order' ),
				'options' => array(
					'show_pre_order_price' => __( 'Show Pre-Order price', 'yith-woocommerce-pre-order' ),
					'show_regular_price'   => __( 'Show Regular price', 'yith-woocommerce-pre-order' ),
					'hidden_price'         => __( 'Hide price', 'yith-woocommerce-pre-order' )
				),
                'css'     => 'height: 35px;',
				'id'      => 'yith_wcpo_guest_users_price',
			);
			$settings['option8'] = array(
				'title'   => _x( 'Default availability date text', 'Admin option: customize Add to Cart label', 'yith-woocommerce-pre-order' ),
				'type'    => 'text',
				'desc'    => _x( 'Use {availability_date} and {availability_time} to show when to remove the Pre-Order status. By leaving it
				blank, it will look like this: \'Available on: {availability_date} at {availability_time}\'.', 'Admin option description: customize
				availability date label', 'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_default_availability_date_label',
				'default' => _x( 'Available on: {availability_date} at {availability_time}', 'Default message for availability date', 'yith-woocommerce-pre-order' )
			);
			$settings['option9'] = array(
				'title'   => _x( 'No date message', 'Admin option: customize Add to Cart label', 'yith-woocommerce-pre-order' ),
				'type'    => 'text',
				'desc'    => _x( 'Default message to be shown when no date is set on a Pre-Order product.', 'Admin option description: Default no date message', 'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_no_date_label',
				'default' => _x( 'Coming soon...', 'Default message when no date is set', 'yith-woocommerce-pre-order' )
			);
			$settings['option10'] = array(
				'title'   => _x( 'Color on shop page', 'Admin option: customize color', 'yith-woocommerce-pre-order' ),
				'type'    => 'color',
				'desc'    => __( 'Change the color of the \'availability date\' and \'no date\' messages on the shop page.',
                    'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_availability_date_color_loop',
				'default' => '#b20015'
			);
			$settings['option11'] = array(
				'title'   => _x( 'Color on single product page', 'Admin option: customize color', 'yith-woocommerce-pre-order' ),
				'type'    => 'color',
				'desc'    => __( 'Change the color of the \'availability date\' and \'no date\' messages on the single product page.', 'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_availability_date_color_single_product',
				'default' => '#a46497'
			);
			$settings['option12'] = array(
				'title'   => _x( 'Color on cart page', 'Admin option: customize color', 'yith-woocommerce-pre-order' ),
				'type'    => 'color',
				'desc'    => __( 'Change the color of the \'availability date\' and \'no date\' messages on the cart page.', 'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_availability_date_color_cart',
				'default' => '#a46497'
			);
			$settings['option13'] = array(
				'title'   => _x( 'Label for countdown', 'Admin option: countdown label', 'yith-woocommerce-pre-order' ),
				'type'    => 'text',
				'desc'    => __( 'The label which will be showed next to the countdown timer. YITH WooCommerce Product Countdown is required', 'yith-woocommerce-pre-order' ),
				'id'      => 'yith_wcpo_countdown_label',
				'default' => __( 'Available in', 'yith-woocommerce-pre-order' )
			);



			$notifications_options = array(

				'settings_notification_start' => array(
					'type' => 'sectionstart',
					'id'   => 'yith_wcpo_notification_start'
				),

				'settings_notification_title'    => array(
					'title' => _x( 'Notifications', 'Panel: Notification title', 'yith-woocommerce-pre-order' ),
					'type'  => 'title',
					'desc'  => '',
					'id'    => 'yith_wcpo_notification_title'
				),

				'settings_enable_pre_order_notification' => array(
					'title'   => _x( 'Notification before Pre-Order release date', 'Admin option: Enable purchasable pre-order products',
                        'yith-woocommerce-pre-order' ),
					'type'    => 'checkbox',
					'desc'    => _x( 'Check this option to receive a notification when a Pre-Order product date is going to pass.', 'Admin option description: Enable notification', 'yith-woocommerce-pre-order' ),
					'id'      => 'yith_wcpo_enable_pre_order_notification',
					'default' => 'yes'
				),

				'settings_notification_label' => array(
					'title'             => _x( 'Number of days', 'Admin option: customize Add to Cart label', 'yith-woocommerce-pre-order' ),
					'type'              => 'number',
					'desc'              => _x( 'Get a notification up to certain number of days before the product release date.', 'Admin
					option
					description: Number of days', 'yith-woocommerce-pre-order' ),
					'id'                => 'yith_wcpo_notification_number_days',
					'custom_attributes' => array(
						'step' => '1',
						'min'  => '0'
					),
					'default'           => '1'
				),

				'settings_enable_pre_order_notification_for_sale' => array(
					'title'   => _x( 'Notification for product availability', 'Admin option: Enable purchasable pre order products',
                        'yith-woocommerce-pre-order' ),
					'type'    => 'checkbox',
					'desc'    => _x( 'Check this option to notify users who purchased a Pre-Order product when it goes on sale.', 'Admin option
					description: Enable notification', 'yith-woocommerce-pre-order' ),
					'id'      => 'yith_wcpo_enable_pre_order_notification_for_sale',
					'default' => 'yes'
				),

				'settings_notification_end' => array(
					'type' => 'sectionend',
					'id'   => 'yith_wcpo_notification_end'
				),
			);

			return array_merge( $settings, $notifications_options );
		}
	}
}