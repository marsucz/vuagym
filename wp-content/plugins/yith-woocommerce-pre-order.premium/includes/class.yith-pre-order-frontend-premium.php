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
 * @class      YITH_Pre_Order_Frontend_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_Frontend_Premium' ) ) {
	/**
	 * Class YITH_Pre_Order_Frontend_Premium
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Frontend_Premium extends YITH_Pre_Order_Frontend {


		 public $_product_from_availability;

		/**
		 * Construct
		 *
		 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
		 * @since  1.0
		 */
		public function __construct() {
			parent::__construct();
			if ( 'no' == get_option( 'yith_wcpo_enable_pre_order', 'no' ) ) {
				return;
			}
			add_filter( 'woocommerce_get_availability', array( $this, 'get_product_from_availability' ), 10, 2 );
			add_action( 'woocommerce_after_shop_loop_item', array( $this, 'show_date_on_loop' ), 8 );
			add_shortcode( 'yith_wcpo_availability_date', array( $this, 'availability_date_shortcode' ) );
			add_filter( 'woocommerce_cart_item_name', array( $this, 'show_date_on_cart' ), 100, 3 );
			add_filter( 'woocommerce_variation_prices_price', array( $this, 'variable_price_range' ), 10, 3 );

			if ( version_compare( WC()->version, '3.0.0', '<' ) ) {
				add_filter( 'woocommerce_stock_html', array( $this, 'show_date_on_single_product' ), 20, 3 );
				add_filter( 'woocommerce_get_price', array( $this, 'edit_price' ), 10, 2 );
			} else {
				add_filter( 'woocommerce_get_stock_html', array( $this, 'show_date_on_single_product' ), 10, 3 );
				add_filter( 'woocommerce_product_get_price', array( $this, 'edit_price' ), 10, 2 );
				add_filter( 'woocommerce_product_variation_get_price', array( $this, 'edit_price' ), 10, 2 );
				add_filter( 'woocommerce_product_get_stock_status', array( $this, 'check_stock_status' ), 10, 2 );
				add_filter( 'woocommerce_product_get_stock_quantity', array( $this, 'check_stock_quantity' ), 10, 2 );
				add_filter( 'woocommerce_product_variation_get_stock_status', array( $this, 'check_stock_status' ), 10, 2 );
				add_filter( 'woocommerce_product_variation_get_stock_quantity', array( $this, 'check_stock_quantity' ), 10, 2 );
			}
			add_filter( 'woocommerce_product_backorders_allowed', array( $this, 'allow_backorders' ), 10, 3 );
			add_filter( 'woocommerce_product_is_in_stock', array( $this, 'force_in_stock_status' ), 10, 2 );
			add_action( 'woocommerce_product_set_stock', array( $this, 'check_out_of_stock' ) );
			add_action( 'woocommerce_variation_set_stock', array( $this, 'check_out_of_stock_variations' ) );
			add_filter( 'woocommerce_email_classes', array( $this, 'register_email_classes' ) );
			add_action( 'ywpo_add_order_item_meta', array( $this, 'add_for_sale_date_order_item_meta' ), 10, 2 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'check_cart_mixing' ), 10, 4 );

			// YITH Badge Management integration
			add_filter( 'yith_wcbm_product_is_on_sale', array( $this, 'auto_badge_for_pre_order' ), 10, 2 );
			add_filter( 'yith_wcbm_advanced_badge_info', array( $this, 'auto_badge_data' ), 10, 2 );

			// YITH WooCommerce Product Countdown integration
			add_filter( 'ywpc_timer_title', array( $this, 'product_countdown_label' ), 60, 3 );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		}

		// Compatibility for themes which returns only 2 parameters of "woocommerce_stock_html" filter
		public function get_product_from_availability( $availability, $product ) {
			$this->_product_from_availability = $product;
			return $availability;
		}

		public function print_availability_date( $class, $timestamp, $style ) {
			$default_no_date_msg = get_option( 'yith_wcpo_no_date_label' );
			// Checks if there is a date set for the product.
			if ( ! empty( $timestamp ) ) {
				$automatic_date_formatting = get_option( 'yith_wcpo_enable_automatic_date_formatting' );

				$availability_label = apply_filters( 'yith_ywpo_date_time', get_option( 'yith_wcpo_default_availability_date_label' ) );
				if ( empty( $availability_label ) ) {
					$availability_label = apply_filters( 'yith_ywpo_date_time', sprintf( __( 'Available on: %s at %s', 'yith-woocommerce-pre-order' ),
						'{availability_date}', '{availability_time}' ) );
				}
				if ( 'yes' == $automatic_date_formatting ) {
					$span_date = '<span class="availability_date"></span>';
					$span_time = '<span class="availability_time"></span>';

					$availability_label = str_replace( '{availability_date}', $span_date, $availability_label );
					$availability_label = str_replace( '{availability_time}', $span_time, $availability_label );
					$availability_label = apply_filters( 'yith_ywpo_availability_date_auto', $availability_label, $span_date, $span_time );

					// Show the custom label set in the plugin options.
					return '<div class="' . $class
					       . '" style="' . apply_filters( 'ywpo_' . $class . '_style', $style )
					       . '" data-time="' . $timestamp . '">' . $availability_label . '</div>';
				} else {
					$date_format = get_option( 'date_format' );
					$date        = get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), $date_format );
					$time        = get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'H:i' );
					$gmt_offset  = get_option( 'gmt_offset' );

					if ( 0 <= $gmt_offset )
						$offset_name = '+' . $gmt_offset;
					else
						$offset_name = (string)$gmt_offset;

					$offset_name = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
					$offset_name = '(UTC' . $offset_name . ')';
					$time        = apply_filters( 'yith_ywpo_no_auto_time', $time . ' ' . $offset_name, $time, $offset_name );

					$availability_label = str_replace( '{availability_date}', $date, $availability_label );
					$availability_label = str_replace( '{availability_time}', $time, $availability_label );
					$availability_label = apply_filters( 'yith_ywpo_availability_date_no_auto', $availability_label, $date, $time );

					return '<div class="' . $class . '-no-auto-format" style="' . $style . '">' . $availability_label . '</div>';
				}
			} else if ( ! empty( $default_no_date_msg ) ) {
				// If no date is set, it shows the No date label.
				return '<div class="' . $class . '" style="' . $style . '">' . $default_no_date_msg . '</div>';
			}
			return false;
		}


		public function show_date_on_loop() {
			global $product, $sitepress;
			$id = yit_get_product_id( $product );

			$product_id  = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order   = new YITH_Pre_Order_Product( $product_id );
			$is_pre_order = $pre_order->get_pre_order_status();

			// Checks if the product is Pre-Order.
			if ( 'yes' != $is_pre_order ) {
			    return;
			}
			$timestamp              = $pre_order->get_for_sale_date_timestamp();
			$color                  = get_option( 'yith_wcpo_availability_date_color_loop' );
			$style                  = $color ? 'color: ' . $color : 'color: #b20015';

            echo $this->print_availability_date( 'pre_order_loop', $timestamp, $style );

		}


        public function availability_date_shortcode( $atts ) {
            $is_preorder = null;
	        $fields = shortcode_atts(
		        array(
			        'product_id' => 0,
		        ), $atts );
            if ( ! empty( $fields['product_id'] ) ) {
	            wp_enqueue_script( 'yith-wcpo-frontend-single-product' );
                echo $this->availability_date( $fields['product_id'] );
            }
        }

        public function availability_date( $product_id ) {
            if ( empty( $product_id ) ) {
                return false;
            }
	        $pre_order = new YITH_Pre_Order_Product( $product_id );

	        $is_pre_order = $pre_order->get_pre_order_status();

	        if ( 'yes' != $is_pre_order ) {
		        return false;
	        }
	        $timestamp = $pre_order->get_for_sale_date_timestamp();
	        $color     = get_option( 'yith_wcpo_availability_date_color_single_product' );
	        $style     = $color ? 'color: ' . $color : 'color: #a46497';

	        return $this->print_availability_date( 'pre_order_single', $timestamp, $style );


        }


		public function show_date_on_single_product( $availability_html, $availability, $product = false ) {
			global $sitepress;
			if( ! $product ) {
				$product = $this->_product_from_availability;
			}

			$id          = $product->get_id();
			$id          = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order   = new YITH_Pre_Order_Product( $id );
            $is_preorder = $pre_order->get_pre_order_status();

            if ( 'yes' == $is_preorder  ) {
	            return $this->availability_date( $id );
            }

			return $availability_html;
		}

		public function show_date_on_cart( $text, $cart_item, $cart_item_key ) {

			$pre_order    = new YITH_Pre_Order_Product( $cart_item['variation_id'] ? $cart_item['variation_id'] : $cart_item['product_id'] );
			$is_pre_order = $pre_order->get_pre_order_status();

			if ( ! is_cart() ) {
				return $text;
			}
			// Checks if the product is Pre-Order.
			if ( 'yes' != $is_pre_order ) {
				return $text;
			}
			$timestamp = $pre_order->get_for_sale_date_timestamp();
			$color = get_option( 'yith_wcpo_availability_date_color_cart' );
			$style = $color ? 'color: ' . $color : 'color: #a46497';

			return $text . $this->print_availability_date( 'pre_order_on_cart', $timestamp, $style );
		}



		public function edit_price( $price, $product ) {
			global $sitepress;

			if ( ( 'simple' != $product->get_type() && 'variation' != $product->get_type() ) || apply_filters( 'yith_wcpo_return_original_price', false, $product ) ) {
				return $price;
			}

			$id = $product->get_id();
			$id = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order = new YITH_Pre_Order_Product( $id );

			$is_pre_order      = $pre_order->get_pre_order_status();
			$price_adjustment  = $pre_order->get_pre_order_price_adjustment();
			$manual_price      = $pre_order->get_pre_order_price();
			$adjustment_type   = $pre_order->get_pre_order_adjustment_type();
			$adjustment_amount = $pre_order->get_pre_order_adjustment_amount();

			if ( 'yes' == $is_pre_order ) {
				return $this->compute_price( $price, $price_adjustment, $manual_price, $adjustment_type, $adjustment_amount );
			}
			return $price;
		}

		public function variable_price_range( $price, $variation, $product_variable ) {
			global $sitepress;

			$id                = $variation->get_id();
			$variation_id      = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order         = new YITH_Pre_Order_Product( $variation_id );
			$is_pre_order      = $pre_order->get_pre_order_status();
			$price_adjustment  = $pre_order->get_pre_order_price_adjustment();
			$manual_price      = $pre_order->get_pre_order_price();
			$adjustment_type   = $pre_order->get_pre_order_adjustment_type();
			$adjustment_amount = $pre_order->get_pre_order_adjustment_amount();

			if ( 'yes' == $is_pre_order ) {
				return $this->compute_price( $price, $price_adjustment, $manual_price, $adjustment_type, $adjustment_amount );
			}

			return $price;
		}


		public function compute_price( $price, $price_adjustment, $manual_price, $adjustment_type, $adjustment_amount ) {
			if ( 'manual' == $price_adjustment ) {
				if ( ! empty( $manual_price ) ) {
					return (string) $manual_price;
				}
			} else if ( isset( $adjustment_amount ) ) {
				if ( 'fixed' == $adjustment_type ) {
					if ( 'discount' == $price_adjustment ) {
						$price = (float) $price - (float) $adjustment_amount;
						if ( 0 > $price ) {
							$price = (string) '0';
						}
					}
					if ( 'mark-up' == $price_adjustment ) {
						$price = (float) $price + (float) $adjustment_amount;
					}

					return (string) $price;
				}
				if ( 'percentage' == $adjustment_type ) {
					if ( 'discount' == $price_adjustment ) {
						$price = (float) $price - ( ( (float) $price * (float) $adjustment_amount ) / 100 );
					}
					if ( 'mark-up' == $price_adjustment ) {
						$price = (float) $price + ( ( (float) $price * (float) $adjustment_amount ) / 100 );
					}

					return (string) $price;
				}
			}

			return $price;
		}

		public function check_stock_status( $status, $product ) {
			global $sitepress;
			$id = yit_get_product_id( $product );

			$product_id  = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order   = new YITH_Pre_Order_Product( $product_id );
			$is_pre_order = $pre_order->get_pre_order_status();

			// Checks if the product is Pre-Order.
			if ( 'yes' == $is_pre_order ) {
				$status = version_compare( WC()->version, '3.0.0', '<' ) ? true : 'instock';
			}
			return $status;
		}

		public function check_stock_quantity( $quantity, $product ) {
			global $sitepress;
			$id = yit_get_product_id( $product );

			$product_id  = $sitepress ? yit_wpml_object_id( $id, 'product', true, $sitepress->get_default_language() ) : $id;
			$pre_order   = new YITH_Pre_Order_Product( $product_id );
			$is_pre_order = $pre_order->get_pre_order_status();
			$product_qty_in_cart = WC()->cart ? WC()->cart->get_cart_item_quantities() : '';

			// Checks if the product is Pre-Order.
			if ( 'yes' == $is_pre_order ) {
				if ( ( is_checkout() || is_cart() ) && $product_qty_in_cart ) {
					$quantity = $product_qty_in_cart[ $pre_order->product->get_stock_managed_by_id() ] + 1;
				} elseif ( ! empty( $_REQUEST['quantity'] ) ) {
					$quantity = $_REQUEST['quantity'] + 1;
				} else {
					$quantity = -1;
				}
			}
			return $quantity;
		}

		public function check_out_of_stock( $product ) {
			if ( 'simple' == $product->get_type() ) {
				$this->check_out_of_stock_products( $product );
			}
		}

		public function check_out_of_stock_variations( $product ) {
			$this->check_out_of_stock_products( $product );
		}

		public function check_out_of_stock_products( $product ) {
			$pre_order = new YITH_Pre_Order_Product( $product );
			if ( 'outofstock' == yit_get_prop( $product, '_stock_status', true )
			     && 'yes' == get_option( 'yith_wcpo_enable_pre_order_auto_outofstock_notification' )
			     && 'yes' != $pre_order->get_pre_order_status()
			) {
				// Delete all possible post meta concerning pre-order
				$pre_order->clear_pre_order_product();

				$pre_order->set_pre_order_status( 'yes' );
				// If changed, notify to admin
				WC()->mailer();
				do_action( 'yith_ywpo_out_of_stock', $pre_order->id );
			}
		}

		function register_email_classes( $email_classes ) {
			if ( 'yes' == get_option( 'yith_wcpo_enable_pre_order_auto_outofstock_notification', 'no' ) ) {
				$email_classes['YITH_Pre_Order_Out_Of_Stock_Email'] = include( YITH_WCPO_PATH . 'includes/emails/class.yith-pre-order-out-of-stock-email.php' );
			}

			return $email_classes;
		}

		public function allow_backorders( $backorders, $product_id, $product = false ) {
			global $sitepress;

			if ( ! $product ) {
				return false;
			}

			if ( 'yes' != get_option( 'yith_wcpo_enable_pre_order_auto_backorders' ) ) {
				return $backorders;
			}

			$product_id = $sitepress ? yit_wpml_object_id( $product_id, 'product', true, $sitepress->get_default_language() ) : $product_id;
			$pre_order  = new YITH_Pre_Order_Product( $product_id );

			if ( 'yes' == $pre_order->get_pre_order_status() ) {
				if ( ! $backorders ) {
					return true;
				}
			}

			return $backorders;
		}

		public function force_in_stock_status( $stock_status, $_product = false ) {
			global $sitepress, $product;

			if ( ! $_product ) {
				$_product = $product;
			}

			if ( 'yes' != get_option( 'yith_wcpo_enable_pre_order_auto_backorders' ) ) {
				return $stock_status;
			}

			$product_id = $_product->get_id();
			$product_id = $sitepress ? yit_wpml_object_id( $product_id, 'product', true, $sitepress->get_default_language() ) : $product_id;
			$pre_order  = new YITH_Pre_Order_Product( $product_id );

			if ( 'yes' == $pre_order->get_pre_order_status() ) {
				if ( ! $stock_status ) {
					return true;
				}
			}

			return $stock_status;
		}

		public function add_for_sale_date_order_item_meta( $item_id, $pre_order ) {
			wc_add_order_item_meta( $item_id, '_ywpo_item_for_sale_date', $pre_order->get_for_sale_date_timestamp() );
		}

		public function check_cart_mixing( $validation, $product_id, $quantity, $variation = 0 ) {
            global $sitepress;

            if ( 'yes' == get_option( 'yith_wcpo_mixing' ) && WC()->cart->cart_contents ) {
                if ( $variation ) {
                    $id  = $sitepress ? yit_wpml_object_id( $variation, 'product', true, $sitepress->get_default_language() ) : $variation;
                } else {
                    $id  = $sitepress ? yit_wpml_object_id( $product_id, 'product', true, $sitepress->get_default_language() ) : $product_id;
                }
                $pre_order = new YITH_Pre_Order_Product( $id );
                $message = __( 'Sorry, is not possible to mix Regular Products and Pre-Order Products in the same cart', 'yith-woocommerce-pre-order' );

                if ( 'yes' == $pre_order->get_pre_order_status() && ! $this->cart_contains_pre_order_products() ) {
                    wc_add_notice( $message, 'error' );
                    return false;
                }
                if ( ( ! $pre_order->get_pre_order_status() || 'no' == $pre_order->get_pre_order_status() )
                    && $this->cart_contains_pre_order_products() ) {
                    wc_add_notice( $message, 'error' );
                    return false;
                }
            }
            return $validation;
        }

        public function cart_contains_pre_order_products() {
            global $sitepress;
		    $has_pre_order_products = false;
		    $cart = WC()->cart->cart_contents;

		    foreach ( $cart as $cart_item ) {
                if ( $cart_item['variation_id'] ) {
                    $id  = $sitepress ? yit_wpml_object_id( $cart_item['variation_id'], 'product', true, $sitepress->get_default_language() ) : $cart_item['variation_id'];
                } else {
                    $id  = $sitepress ? yit_wpml_object_id( $cart_item['product_id'], 'product', true, $sitepress->get_default_language() ) : $cart_item['product_id'];
                }
                $pre_order = new YITH_Pre_Order_Product( $id );
                if ( 'yes' == $pre_order->get_pre_order_status() ) {
                    return true;
                }
            }

            return $has_pre_order_products;
        }

        public function auto_badge_for_pre_order( $product_is_on_sale, $product ) {
            if ( $product ) {
                $product = new YITH_Pre_Order_Product( $product );
                if ( 'yes' == $product->get_pre_order_status() && 'discount' == $product->get_pre_order_price_adjustment() ) {
                    $product_is_on_sale = true;
                }
            }
            return $product_is_on_sale;
        }

        public function auto_badge_data( $data, $product ) {
            if ( ! $product ) {
            	return $data;
            }
	        $pre_order = new YITH_Pre_Order_Product( $product );
	        if ( 'yes' == $pre_order->get_pre_order_status() && 'discount' == $pre_order->get_pre_order_price_adjustment() ) {
		        $amount                    = $pre_order->get_pre_order_adjustment_amount();
		        $args                      = array( 'decimals' => 0 );
		        $price                     = $product->get_price();
		        $regular_price             = $product->get_regular_price();
		        $saved_money_float         = $regular_price - $price;
		        $saved_money               = absint( $saved_money_float );
		        $saved                     = strip_tags( wc_price( $saved_money, $args ) );

		        if ( 'fixed' == $pre_order->get_pre_order_adjustment_type() && $amount > 0 ) {
			        $data['saved_money']       = $saved_money ;
			        $data['saved_money_float'] = $saved_money_float;
			        $data['saved']             = $saved;
		        }
		        if ( 'percentage' == $pre_order->get_pre_order_adjustment_type() && $amount > 0 ) {
			        $data['percentual_sale']   = $amount;
			        $data['sale_percentage']   = $amount;
			        $data['saved_money']       = $saved_money ;
			        $data['saved_money_float'] = $saved_money_float;
			        $data['saved']             = $saved;
		        }
	        }


            return $data;
        }

		public function product_countdown_label( $label, $a, $id ) {
			$option =  get_option( 'yith_wcpo_countdown_label' );
			if ( $option ) {
				$label = $option;
			}
			return $label;
		}


		public function enqueue_scripts() {
			parent::enqueue_scripts();

			if ( is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() || apply_filters( 'yith_ywpo_enqueue_script', false ) ) {
				wp_register_script( 'yith-wcpo-frontend-shop-loop', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'frontend-shop-loop.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
				wp_enqueue_script( 'yith-wcpo-frontend-shop-loop' );
			}
			if ( is_cart() ) {
				wp_register_script( 'yith-wcpo-frontend-cart', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'frontend-cart.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
				wp_enqueue_script( 'yith-wcpo-frontend-cart' );
			}
			if ( is_account_page() || is_checkout() ) {
				wp_register_script( 'yith-wcpo-frontend-my-account', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'frontend-my-account.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
				wp_enqueue_script( 'yith-wcpo-frontend-my-account' );
			}
		}
		
	}
}