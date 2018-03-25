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
 * @class      YITH_Pre_Order_Edit_Product_Page_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_Edit_Product_Page_Premium' ) ) {
	/**
	 * Class YITH_Pre_Order_Edit_Product_Page_Premium
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Edit_Product_Page_Premium extends YITH_Pre_Order_Edit_Product_Page {

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since  1.0.0
		 */
		public function __construct() {
			parent::__construct();
			
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'pre_order_tab' ), 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'pre_order_tab_content' ) );
			add_action( 'load-edit.php', array( $this, 'products_bulk_actions' ) );
			add_action( 'load-edit-tags.php', array( $this, 'taxonomies_bulk_actions' ) );
			add_action( 'woocommerce_product_after_variable_attributes', array(
				$this,
				'woocommerce_product_after_variable_attributes'
			), 10, 3 );
		}

		
		/**
		 * Pre Order Tab
		 *
		 * Add a Pre Order settings tab in the product data box
		 *
		 * @return   array product_data_tabs
		 * @since    1.0.0
		 * @author   Carlos Mora <carlos.eugenio@yourinspiration.it>
		 */
		public function pre_order_tab( $product_data_tabs ) {
			$preorder_tab = array(
				'preorder' => array(
					'label'  => __( 'Pre-Order', 'yith-woocommerce-pre-order' ),
					'target' => 'preorder_product_data',
					'class'  => array( 'show_if_preorder', 'hide_if_grouped', 'hide_if_external', 'hide_if_variable' ),
				)
			);

			return array_merge( $product_data_tabs, $preorder_tab );
		}

		public function update_settings( $post_id ) {
		    $pre_order    = new YITH_Pre_Order_Product( $post_id );
			$is_pre_order = isset( $_POST['_ywpo_preorder'] ) && ! is_array( $_POST['_ywpo_preorder'] ) ? 'yes' : 'no';

			if ( 'yes' != get_option( 'yith_wcpo_enable_pre_order_auto_outofstock_notification', 'no' ) ) {
				$pre_order->set_pre_order_status( $is_pre_order );
            }

			$pre_order->set_pre_order_status( $is_pre_order );

			if ( 'yes' == $is_pre_order ) {
				$previous_sale_date = $pre_order->get_for_sale_date();
				$new_sale_date = (string) isset( $_POST['_ywpo_for_sale_date'] ) ? wc_clean( $_POST['_ywpo_for_sale_date'] ) : '';
				$pre_order->set_for_sale_date( $new_sale_date );

				// If the date is changed, an email will be sent
				if ( ! empty( $previous_sale_date ) && ! empty( $new_sale_date ) ) {
					if ( $new_sale_date !== $previous_sale_date ) {
					    yit_delete_prop( $pre_order, '_ywpo_preorder_notified' );
						WC()->mailer();
						$customers = self::get_pre_order_customers( $post_id, $new_sale_date );
						if ( $customers ) {
							foreach ( $customers as $customer ) {
								do_action( 'yith_ywpo_sale_date_changed', $customer, $post_id, $previous_sale_date, $new_sale_date );
							}
						}
					}
				}
			} else {
				yit_delete_prop( $pre_order, '_ywpo_for_sale_date' );
			}

			// Text fields
			$pre_order_label = isset( $_POST['_ywpo_preorder_label'] ) ? wc_clean( $_POST['_ywpo_preorder_label'] ) : '';
			$pre_order->set_pre_order_label( $pre_order_label );

			$pre_order_price = isset( $_POST['_ywpo_preorder_price'] ) ? wc_format_decimal( wc_clean( $_POST['_ywpo_preorder_price'] ) ) : '';
			$pre_order->set_pre_order_price( $pre_order_price );

			$adjustment_amount = isset( $_POST['_ywpo_price_adjustment_amount'] ) ? wc_clean( $_POST['_ywpo_price_adjustment_amount'] ) : '';
			$pre_order->set_pre_order_adjustment_amount( $adjustment_amount );

			// Radio groups
			$price_adjustment = isset( $_POST['_ywpo_price_adjustment'] ) ? sanitize_html_class( $_POST['_ywpo_price_adjustment'] ) : '';
			$pre_order->set_pre_order_price_adjustment( $price_adjustment );

			$adjustment_type = isset( $_POST['_ywpo_adjustment_type'] ) ? sanitize_html_class( $_POST['_ywpo_adjustment_type'] ) : '';
			$pre_order->set_pre_order_adjustment_type( $adjustment_type );
		}

		public static function get_pre_order_items() {
			global $wpdb;

			$query = "SELECT order_id, oi.order_item_id FROM {$wpdb->prefix}woocommerce_order_itemmeta woim
        		LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi
        		ON woim.order_item_id = oi.order_item_id
        		WHERE meta_key = '_ywpo_item_preorder' AND meta_value = 'yes';";

			$results = $wpdb->get_results( $query, ARRAY_A );
			return $results;
		}

		/**
		 * Get all the customers that have purchased a product in pre-order
		 *
		 * @param int $product_id
		 *
		 * @return array|bool $customers
		 * @author Lorenzo Giuffrida
		 * @since  1.0.0
		 */
		public static function get_pre_order_customers( $product_id, $new_sale_date = '' ) {
			global $wpdb;

			$wpcl_orders = '';
			$customers   = array();
			$product     = wc_get_product( $product_id );

			if ( $product->is_type( 'variation' ) ) {
				$customerquery = "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta woim 
        		LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi 
        		ON woim.order_item_id = oi.order_item_id 
        		WHERE meta_key = '_variation_id' AND meta_value = %d
        		GROUP BY order_id;";
			} else {
				$customerquery = "SELECT order_id FROM {$wpdb->prefix}woocommerce_order_itemmeta woim 
        		LEFT JOIN {$wpdb->prefix}woocommerce_order_items oi 
        		ON woim.order_item_id = oi.order_item_id 
        		WHERE meta_key = '_product_id' AND meta_value = %d
        		GROUP BY order_id;";
			}
			$order_ids = $wpdb->get_col( $wpdb->prepare( $customerquery, $product_id ) );

			if ( $order_ids ) {
				$args = array(
					'post_type'      => 'shop_order',
					'post__in'       => $order_ids,
					'posts_per_page' => 999,
					'order'          => 'ASC',
					'post_status'    => 'any',
				);
				$wpcl_orders = new WP_Query( $args );
			}
			if ( ! $wpcl_orders ) {
			    return false;
			}
			$user_ids = array();
			foreach ( $wpcl_orders->posts as $wpcl_order ) {
				$order = wc_get_order( $wpcl_order->ID );
				$user_id = $order->get_user_id();
				//Prevent duplicated customers
				if ( ! in_array( $user_id, $user_ids ) ) {
					foreach ( $order->get_items() as $item_id => $item ) {

						$item_is_pre_order = ! empty( $item['ywpo_item_preorder'] ) ? $item['ywpo_item_preorder'] : '';
						$item_timestamp = ! empty( $item['ywpo_item_for_sale_date'] ) ? $item['ywpo_item_for_sale_date'] : '';
						$item_is_notified = ! empty( $item['ywpo_item_preorder_notified'] ) ? $item['ywpo_item_preorder_notified'] : '';

						if ( 'yes' != $item_is_pre_order ) {
						    continue;
                        }

                        if ( 'yes' == $item_is_notified ) {
						    continue;
                        }

                        if ( $order instanceof WC_Data ) {
	                        $customers[] = array(
		                        'name'  => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
		                        'email' => $order->get_billing_email(),
		                        'order' => $order->get_id()
	                        );
                        } else {
	                        $customers[] = array(
		                        'name'  => $order->billing_first_name . ' ' . $order->billing_last_name,
		                        'email' => $order->billing_email,
		                        'order' => $order->get_id()
	                        );
                        }

						$user_ids[] = $user_id;

						if ( ! $new_sale_date ) {
							wc_update_order_item_meta( $item_id, '_ywpo_item_preorder_notified', 'yes' );
						}

						// Change the order item meta only for Pre-Order which haven't end its date yet.
						if ( ( $item_timestamp > time() || empty( $item_timestamp ) ) && $new_sale_date ) {
							$format_date = str_replace( '/', '-', $new_sale_date );
							$format_date = $format_date . ':00';
							$format_date = get_gmt_from_date( $format_date );
							$new_date    = strtotime( $format_date );
							wc_update_order_item_meta( $item_id, '_ywpo_item_for_sale_date', $new_date );
						}

					}
				}
			}
			return $customers;
		}


		public function pre_order_tab_content() {
			global $thepostid;
			$pre_order = new YITH_Pre_Order_Product( $thepostid );
			?>
			<div id="preorder_product_data" class="panel woocommerce_options_panel hidden">
				<div class="options_group hide_if_external">
					<?php $for_sale_date = $pre_order->get_for_sale_date(); ?>

					<p class="form-field for_sale_date_field">
						<label for="_ywpo_for_sale_date">
							<?php
							echo __( 'For sale date', 'yith-woocommerce-pre-order' );
							$gmt_offset = get_option( 'gmt_offset' );

							if ( 0 <= $gmt_offset )
								$offset_name = '+' . $gmt_offset;
							else
								$offset_name = (string)$gmt_offset;

							$offset_name = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
							$offset_name = '(UTC' . $offset_name . ')';
							echo ' ' . $offset_name;
							?>
						</label>
						<input type="text" class="short" name="_ywpo_for_sale_date" id="_ywpo_for_sale_date" value="<?php echo esc_attr( $for_sale_date ); ?>"
							   title="YYYY/MM/DD hh:mm"
							   placeholder="<?php echo _x( 'Date&hellip;', 'placeholder', 'yith-woocommerce-pre-order' ); ?> YYYY/MM/DD hh:mm" maxlength="16"  />
						<?php echo wc_help_tip( __( 'Set the date when the product will be for sale. 
						The Timezone used is the WordPress local Timezone. Settings -> General -> Timezone.',
							'yith-woocommerce-pre-order' ) ); ?>
					</p>

					<?php
					woocommerce_wp_text_input( array(
						'id'          => '_ywpo_preorder_label',
						'label'       => __( 'Pre-Order label', 'yith-woocommerce-pre-order' ),
						'desc_tip'    => 'true',
						'description' => __( 'Set a custom label to announce the pre order product status. 
						For example: Pre Order Now!. Leaving it blank, it will show the default label (Pre Order).',
							'yith-woocommerce-pre-order' )
					) );



					$has_price_adjustment_checked = $pre_order->get_pre_order_price_adjustment();
					?>

					<fieldset class="form-field _ywpo_price_adjustment_field">
						<legend>
							<?php echo __( 'Price adjustment', 'yith-woocommerce-pre-order' ); ?>
						</legend>
						<ul class="wc-radios">
							<li>
								<label>
									<input name="<?php echo esc_attr( '_ywpo_price_adjustment' ); ?>"
										   value="<?php echo esc_attr( 'manual' ); ?>" type="radio"
										   class="<?php echo esc_attr( '_ywpo_price_adjustment' ); ?>"
										<?php echo checked( empty( $has_price_adjustment_checked )
											? 'manual'
											: $has_price_adjustment_checked, esc_attr( 'manual' ), false ); ?>
									/>
									<?php echo __( 'Set the price manually', 'yith-woocommerce-pre-order' ); ?>
								</label>
							</li>
							<li>
								<label>
									<input name="<?php echo esc_attr( '_ywpo_price_adjustment' ); ?>"
										   value="<?php echo esc_attr( 'discount' ); ?>" type="radio"
										   class="<?php echo esc_attr( '_ywpo_price_adjustment' ); ?>"
										<?php echo checked( $pre_order->get_pre_order_price_adjustment(),
											esc_attr( 'discount' ), false ); ?>
									/>
									<?php echo __( 'Discount the selling price', 'yith-woocommerce-pre-order' ); ?>
								</label>
							</li>
							<li>
								<label>
									<input name="<?php echo esc_attr( '_ywpo_price_adjustment' ); ?>"
										   value="<?php echo esc_attr( 'mark-up' ); ?>" type="radio"
										   class="<?php echo esc_attr( '_ywpo_price_adjustment' ); ?>"
										<?php echo checked( $pre_order->get_pre_order_price_adjustment(),
											esc_attr( 'mark-up' ), false ); ?>
									/>
									<?php echo __( 'Mark-up the selling price', 'yith-woocommerce-pre-order' ); ?>
								</label>
							</li>
						</ul>
						<?php echo wc_help_tip( __( 'Choose if the price will edited during the pre-sale period',
							'yith-woocommerce-pre-order' ) ); ?>
					</fieldset>

					<?php

					woocommerce_wp_text_input( array(
						'id'          => '_ywpo_preorder_price',
						'label'       => __( 'Pre-Order price', 'yith-woocommerce-pre-order' ),
						'desc_tip'    => 'true',
						'data_type'   => 'price',
						'description' => __( 'Set a fixed price for the Pre-Order product. 
						Leaving it blank, the regular price will be shown.', 'yith-woocommerce-pre-order' )
					) );


					$has_adjustment_type_checked = $pre_order->get_pre_order_adjustment_type();

					?>

					<fieldset class="form-field _ywpo_adjustment_type_field">
						<legend>
							<?php echo __( 'Adjustment type', 'yith-woocommerce-pre-order' ); ?>
						</legend>
						<ul class="wc-radios">
							<li>
								<label>
									<input name="<?php echo esc_attr( '_ywpo_adjustment_type' ); ?>"
										   value="<?php echo esc_attr( 'fixed' ); ?>" type="radio"
										   class="<?php echo esc_attr( '_ywpo_adjustment_type' ); ?>"
										<?php echo checked( empty( $has_adjustment_type_checked )
											? 'fixed'
											: $has_adjustment_type_checked, esc_attr( 'fixed' ), false ); ?>
									/>
									<?php echo __( 'Set a fixed amount to apply to the selling price', 'yith-woocommerce-pre-order' ) ?>
								</label>
							</li>
							<li>
								<label>
									<input name="<?php echo esc_attr( '_ywpo_adjustment_type' ); ?>"
										   value="<?php echo esc_attr( 'percentage' ); ?>" type="radio"
										   class="<?php echo esc_attr( '_ywpo_adjustment_type' ); ?>"
										<?php echo checked( $pre_order->get_pre_order_adjustment_type(),
											esc_attr( 'percentage' ), false ); ?>
									/>
									<?php echo __( 'Set a percentage to apply to the selling price', 'yith-woocommerce-pre-order' ) ?>
								</label>
							</li>
						</ul>
						<?php echo wc_help_tip( __( 'Choose the type of adjustment to apply to the selling price',
							'yith-woocommerce-pre-order' ) ); ?>
					</fieldset>

					<?php

					woocommerce_wp_text_input( array(
						'id'          => '_ywpo_price_adjustment_amount',
						'label'       => __( 'Price adjustment amount', 'yith-woocommerce-pre-order' ),
						'desc_tip'    => 'true',
						'data_type'   => 'decimal',
						'description' => __( 'Set the quantity to apply (Fixed or percentage). 
						Type numbers only. No signs (\'%\', \'+\', \'-\', etc.).', 'yith-woocommerce-pre-order' )
					) );

					?>
				</div>
			</div>
			<?php
		}

		public function products_bulk_actions() {
			global $typenow;
			$post_type     = $typenow;
			$page          = isset( $_GET['paged'] ) ? '&paged=' . $_GET['paged'] : '';
			$url           = 'edit.php?post_type=' . $post_type . $page;
			$sendback      = admin_url( $url );
			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action        = $wp_list_table->current_action();
			if ( ( $action == 'ywpo-set-pre-order' ) || ( $action == 'ywpo-remove-pre-order' ) ) {
				$post_ids = $_GET['post'];
				check_admin_referer( 'bulk-posts' );
				if ( ! $post_ids ) {
				    return;
                }
				foreach ( $post_ids as $post_id ) {
					$pre_order = new YITH_Pre_Order_Product( $post_id );

					if ( $pre_order->product->is_type( 'simple' ) ) {
						if ( $action == 'ywpo-set-pre-order' ) {
							$pre_order->set_pre_order_status( 'yes' );
						}
						else {
							$pre_order->set_pre_order_status( 'no' );
						}
					} elseif ( $pre_order->product->is_type( 'variable' ) ) {
					    $children = $pre_order->product->get_children();
					    foreach ( $children as $child ) {
					        $pre_order_variation = new YITH_Pre_Order_Product( $child );
						    $status =  $action == 'ywpo-set-pre-order' ? 'yes' : 'no';
                            $pre_order_variation->set_pre_order_status( $status );
                        }
                    }
				}
				wp_redirect( $sendback );
				exit();
			}
		}

		public function taxonomies_bulk_actions() {
			global $typenow;
			$post_type      = $typenow;
			$current_screen = get_current_screen();
			$sendback       = '';
			$taxonomy       = '';
			if ( 'edit-product_cat' == $current_screen->id ) {
				$sendback = admin_url( "edit-tags.php?taxonomy=product_cat&post_type=$post_type" );
				$taxonomy = 'product_cat';
			}
			if ( 'edit-product_tag' == $current_screen->id ) {
				$sendback = admin_url( "edit-tags.php?taxonomy=product_tag&post_type=$post_type" );
				$taxonomy = 'product_tag';
			}
			$wp_list_table = _get_list_table( 'WP_Posts_List_Table' );
			$action        = $wp_list_table->current_action();

			if ( ( $action == 'ywpo-set-pre-order' ) || ( $action == 'ywpo-remove-pre-order' ) ) {
				$category_ids = $_POST['delete_tags'];
				check_admin_referer( 'bulk-tags' );
				foreach ( $category_ids as $category_id ) {
					$args     = array(
						'post_type'   => 'product',
						'numberposts' => - 1,
						'fields'      => 'ids',
						'tax_query'   => array(
							array(
								'taxonomy' => $taxonomy,
								'field'    => 'term_id',
								'terms'    => $category_id,
								'operator' => 'IN'
							)
						)
					);
					$post_ids = get_posts( $args );
					if ( ! $post_ids ) {
					    continue;
                    }
					foreach ( $post_ids as $post_id ) {
						$pre_order = new YITH_Pre_Order_Product( $post_id );
						if ( $pre_order->product->is_type( 'simple' ) ) {
							$status =  $action == 'ywpo-set-pre-order' ? 'yes' : 'no';
							$pre_order->set_pre_order_status( $status );
						} elseif ( $pre_order->product->is_type( 'variable' ) && $pre_order->product->has_child() ) {
							$children = $pre_order->product->get_children();
							foreach ( $children as $child ) {
								$pre_order_variation = new YITH_Pre_Order_Product( $child );
								$status =  $action == 'ywpo-set-pre-order' ? 'yes' : 'no';
								$pre_order_variation->set_pre_order_status( $status );
							}
						}
					}
				}
				wp_redirect( $sendback );
				exit();
			}
		}

		public function woocommerce_product_after_variable_attributes( $loop, $variation_data, $variation ) {
			$pre_order = new YITH_Pre_Order_Product( $variation->ID );
			?>
			<div class="show_if_variation_pre_order">
				<?php
				$for_sale_date = $pre_order->get_for_sale_date();
				?>

				<div>
					<p class="form-row form-row-first variable_for_sale_date_field">
						<label>
							<?php
							echo __( 'For sale date', 'yith-woocommerce-pre-order' );

							$gmt_offset = get_option( 'gmt_offset' );
							if ( 0 <= $gmt_offset )
								$offset_name = '+' . $gmt_offset;
							else
								$offset_name = (string)$gmt_offset;
							$offset_name = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
							$offset_name = '(UTC' . $offset_name . ')';
							echo ' ' . $offset_name;

							echo wc_help_tip( __( 'Set the date when the product will be for sale. 
							The Timezone used is the WordPress local Timezone. Settings -> General -> Timezone.',
								'yith-woocommerce-pre-order' ) ); ?>
						</label>
						<input type="text" class="short variable_ywpo_for_sale_datetimepicker"
							   name="<?php echo esc_attr( '_ywpo_for_sale_date[' . $loop . ']' ); ?>"
							   value="<?php echo esc_attr( $for_sale_date ); ?>"
							   title="YYYY/MM/DD hh:mm"
							   placeholder="<?php
							   echo _x( 'Date&hellip;', 'placeholder', 'yith-woocommerce-pre-order' ); ?> YYYY/MM/DD hh:mm"
							   maxlength="16"
						/>
					</p>

					<?php
					$pre_order_label = $pre_order->get_pre_order_label();
					?>

					<p class="form-row form-row-last">
						<label>
							<?php echo __( 'Pre-Order label', 'yith-woocommerce-pre-order' );
							echo wc_help_tip( __( 'Set a custom label to announce the pre order product status. 
						For example: Pre Order Now!. Leaving it blank, it will show the default label (Pre Order).',
								'yith-woocommerce-pre-order' ) ); ?>
						</label>
						<input type="text" size="30" name="<?php echo esc_attr( '_ywpo_preorder_label[' . $loop . ']' ); ?>"
							   placeholder="Pre-Order Now!" value="<?php echo esc_attr( $pre_order_label ); ?>"
						/>
					</p>
				</div>

				<?php
				$has_price_adjustment_checked = $pre_order->get_pre_order_price_adjustment();
				?>

				<fieldset class="form-row form-row-full">
					<p>
						<?php echo __( 'Price adjustment', 'yith-woocommerce-pre-order' );
						echo wc_help_tip( __( 'Choose if the price will be edited during the pre-sale period',
							'yith-woocommerce-pre-order' ) ); ?>
					</p>
					<ul class="wc-radios">
						<li>
							<label>
								<input
									name="<?php echo esc_attr( '_ywpo_price_adjustment[' . $loop . ']' ); ?>"
									value="<?php echo esc_attr( 'manual' ); ?>" type="radio"
									class="<?php echo esc_attr( 'variable_ywpo_price_adjustment' ); ?>"
									<?php echo checked( empty( $has_price_adjustment_checked )
										? 'manual'
										: $has_price_adjustment_checked, esc_attr( 'manual' ), false ); ?>
								/>
								<?php echo __( 'Set the price manually', 'yith-woocommerce-pre-order' ); ?>
							</label>
						</li>
						<li>
							<label>
								<input
									name="<?php echo esc_attr( '_ywpo_price_adjustment[' . $loop . ']' ); ?>"
									value="<?php echo esc_attr( 'discount' ); ?>" type="radio"
									class="<?php echo esc_attr( 'variable_ywpo_price_adjustment' ); ?>"
									<?php echo checked( $pre_order->get_pre_order_price_adjustment(),
										esc_attr( 'discount' ), false ); ?>
								/>
								<?php echo __( 'Discount the selling price', 'yith-woocommerce-pre-order' ); ?>
							</label>
						</li>
						<li>
							<label>
								<input
									name="<?php echo esc_attr( '_ywpo_price_adjustment[' . $loop . ']' ) ?>"
									value="<?php echo esc_attr( 'mark-up' ); ?>" type="radio"
									class="<?php echo esc_attr( 'variable_ywpo_price_adjustment' ); ?>"
									<?php echo checked( $pre_order->get_pre_order_price_adjustment(), esc_attr( 'mark-up' ), false ); ?>
								/>
								<?php echo __( 'Mark-up the selling price', 'yith-woocommerce-pre-order' ); ?>
							</label>
						</li>
					</ul>
				</fieldset>

				<?php
				$pre_order_price = $pre_order->get_pre_order_price();
				?>

				<p class="form-row form-row-first show_if_manual">
					<label>
						<?php echo __( 'Pre-Order price', 'yith-woocommerce-pre-order' );
						echo wc_help_tip( __( 'Set a fixed price for the Pre-Order product. 
						Leaving it blank, the regular price will be shown.', 'yith-woocommerce-pre-order' ) ); ?>
					</label>
					<input class="wc_input_price" type="text" size="10"
						   name="<?php echo esc_attr( '_ywpo_preorder_price[' . $loop . ']' ); ?>"
						   value="<?php echo $pre_order_price; ?>"
					/>
				</p>

				<?php
				$has_adjustment_type_checked = $pre_order->get_pre_order_adjustment_type();
				?>

				<fieldset class="form-row form-row-full hide_if_manual">
					<p>
						<?php echo __( 'Adjustment type', 'yith-woocommerce-pre-order' );
						echo wc_help_tip( __( 'Choose the type of adjustment to apply to the selling price',
							'yith-woocommerce-pre-order' ) ); ?>
					</p>
					<ul class="wc-radios">
						<li>
							<label>
								<input
									name="<?php echo esc_attr( '_ywpo_adjustment_type[' . $loop . ']' ); ?>"
									value="<?php echo esc_attr( 'fixed' ) ?>" type="radio"
									class="<?php echo esc_attr( 'variable_ywpo_adjustment_type' ); ?>"
									<?php echo checked( empty( $has_adjustment_type_checked )
										? 'fixed'
										: $has_adjustment_type_checked, esc_attr( 'fixed' ), false ); ?>
								/>
								<?php echo __( 'Set a fixed amount to apply to the selling price',
									'yith-woocommerce-pre-order' ); ?>
							</label>
						</li>
						<li>
							<label>
								<input
									name="<?php echo esc_attr( '_ywpo_adjustment_type[' . $loop . ']' ); ?>"
									value="<?php echo esc_attr( 'percentage' ); ?>" type="radio"
									class="<?php echo esc_attr( 'variable_ywpo_adjustment_type' ); ?>"
									<?php echo checked( $pre_order->get_pre_order_adjustment_type(), esc_attr( 'percentage' ), false ); ?>
								/>
								<?php echo __( 'Set a percentage to apply to the selling price', 'yith-woocommerce-pre-order' ); ?>
							</label>
						</li>
					</ul>
				</fieldset>

				<?php
				$price_adjustment_amount = $pre_order->get_pre_order_adjustment_amount();
				?>
				<p class="form-row form-row-first hide_if_manual">
					<label>
						<?php echo __( 'Price adjustment amount', 'yith-woocommerce-pre-order' );
						echo wc_help_tip( __( 'Set the quantity to apply (Fixed or percentage). 
						Type numbers only. No signs (\'%\', \'+\', \'-\', etc.).', 'yith-woocommerce-pre-order' ) ); ?>
					</label>
					<input class="wc_input_decimal" type="text" size="10"
						   name="<?php echo esc_attr( '_ywpo_price_adjustment_amount[' . $loop . ']' ); ?>"
						   value="<?php echo $price_adjustment_amount; ?>"
					>
				</p>
			</div>
			<?php
		}

		public function save_variable_fields( $post_id, $_i ) {
			$pre_order = new YITH_Pre_Order_Product( $post_id );

			$is_pre_order = isset( $_POST['_ywpo_preorder'][ $_i ] ) ? 'yes' : 'no';
			if ( 'yes' != get_option( 'yith_wcpo_enable_pre_order_auto_outofstock_notification', 'no' ) ) {
				$pre_order->set_pre_order_status( $is_pre_order );
			}

			if ( 'yes' == $is_pre_order ) {
				$previous_sale_date = $pre_order->get_for_sale_date();
				$new_sale_date = (string) isset( $_POST['_ywpo_for_sale_date'][ $_i ] ) ? wc_clean( $_POST['_ywpo_for_sale_date'][ $_i ] ) : '';
				$pre_order->set_for_sale_date( $new_sale_date );

				// If the date is changed, it will be sent an email
				if ( ! empty( $previous_sale_date ) && ! empty( $new_sale_date ) ) {
					if ( $new_sale_date !== $previous_sale_date ) {
					    yit_delete_prop( $pre_order, '_ywpo_preorder_notified' );
						WC()->mailer();
						$customers = self::get_pre_order_customers( $post_id, $new_sale_date );
						foreach ( $customers as $customer ) {
							do_action( 'yith_ywpo_sale_date_changed', $customer, $post_id, $previous_sale_date, $new_sale_date );
						}
					}
				}
			} else {
				yit_delete_prop( $pre_order, '_ywpo_for_sale_date' );
			}

			// Text fields
			$pre_order_label = isset( $_POST['_ywpo_preorder_label'][ $_i ] ) ? wc_clean( $_POST['_ywpo_preorder_label'][ $_i ] ) : '';
			$pre_order->set_pre_order_label( $pre_order_label );

			$pre_order_price = isset( $_POST['_ywpo_preorder_price'][ $_i ] ) ? wc_clean( $_POST['_ywpo_preorder_price'][ $_i ] ) : '';
			$pre_order->set_pre_order_price( $pre_order_price );

			$adjustment_amount = isset( $_POST['_ywpo_price_adjustment_amount'][ $_i ] ) ? wc_clean( $_POST['_ywpo_price_adjustment_amount'][ $_i ] ) : '';
			$pre_order->set_pre_order_adjustment_amount( $adjustment_amount );

			// Radio groups
			$price_adjustment = isset( $_POST['_ywpo_price_adjustment'][ $_i ] ) ? sanitize_html_class( $_POST['_ywpo_price_adjustment'][ $_i ] ) : '';
			$pre_order->set_pre_order_price_adjustment( $price_adjustment );

			$adjustment_type = isset( $_POST['_ywpo_adjustment_type'][ $_i ] ) ? sanitize_html_class( $_POST['_ywpo_adjustment_type'][ $_i ] ) : '';
			$pre_order->set_pre_order_adjustment_type( $adjustment_type );

		}


		public function enqueue_scripts( $hook_suffix ) {
			$current_screen = get_current_screen();

			if ( 'product' == $current_screen->id || 'edit-shop_order' == $current_screen->id || 'edit-product' == $current_screen->id ) {
				wp_enqueue_script( 'yith-wcpo-edit-product-page', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'edit-product-page.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
				wp_enqueue_style( 'yith-wcpo-admin-css', YITH_WCPO_ASSETS_URL . 'css/admin.css', array(), YITH_WCPO_VERSION );

				wp_enqueue_script( 'jquery-ui-datetimepicker', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'timepicker.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
				wp_enqueue_style( 'jquery-ui-datetimepicker-style', YITH_WCPO_ASSETS_URL . 'css/timepicker.css', array(), YITH_WCPO_VERSION );
			}

			if ( 'edit-product' == $current_screen->id || 'edit-product_cat' == $current_screen->id || 'edit-product_tag' == $current_screen->id ) {
				wp_register_script( 'ywbc-bulk-actions',
					YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'ywbc-bulk-actions.js' ),
					array(
						'jquery',
					),
					YITH_WCPO_VERSION,
					true );
				wp_localize_script( 'ywbc-bulk-actions', 'ywbc_bk_data', array(
					'set_pre_order_status'    => '<option value="ywpo-set-pre-order">' . __( 'Set the Pre-Order status', 'yith-woocommerce-pre-order' ) . '</option>',
					'remove_pre_order_status' => '<option value="ywpo-remove-pre-order">' . __( 'Remove the Pre-Order status', 'yith-woocommerce-pre-order' ) . '</option>',
				) );
				wp_enqueue_script( 'ywbc-bulk-actions' );
			}

		}

	}

}