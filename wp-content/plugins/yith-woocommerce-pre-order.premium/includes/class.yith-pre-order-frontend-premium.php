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
				add_filter( 'woocommerce_product_get_sale_price', array( $this, 'empty_sale_price' ), 10, 2 );
				add_filter( 'woocommerce_product_variation_get_sale_price', array( $this, 'empty_sale_price' ), 10, 2 );
				add_filter( 'woocommerce_product_is_on_sale', array( $this, 'force_use_of_sale_price' ), 10, 2 );
			}
			add_action( 'ywpo_add_order_item_meta', array( $this, 'add_for_sale_date_order_item_meta' ), 10, 2 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'check_cart_mixing' ), 10, 4 );

			// YITH Badge Management integration
			add_filter( 'yith_wcbm_advanced_badge_info', array( $this, 'auto_badge_data' ), 10, 2 );

			// YITH WooCommerce Product Countdown integration
			add_filter( 'ywpc_timer_title', array( $this, 'product_countdown_label' ), 60, 3 );

			// Flatsome fix for showing availability date on Quick View
			add_action( 'wc_quick_view_before_single_product', array( $this, 'flatsome_fix' ), 5 );

			add_shortcode( 'yith_pre_order_products', array( $this, 'pre_order_products_loop' ) );
			add_action( 'yith_wcpo_pagination_nav', array( $this, 'pagination_nav' ) );
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
				if ( ! get_current_user_id() ) {
					switch ( get_option( 'yith_wcpo_guest_users_price', 'show_pre_order_price' ) ) {
						case 'show_regular_price' :
							return $product->get_regular_price();
						case 'hidden_price' :
							return '';
					}
				}
			    if ( 'yes' == get_option( 'yith_wcpo_show_regular_price' ) && 'manual' == $price_adjustment && $manual_price != '0' ) {
				    return $this->compute_price( $product->get_regular_price(), $price_adjustment, $manual_price, $adjustment_type, $adjustment_amount );
			    } else {
				    return $this->compute_price( $price, $price_adjustment, $manual_price, $adjustment_type, $adjustment_amount );
			    }
			}
			return $price;
		}

		/**
		 * @param $sale_price
		 * @param $product
		 *
         * @since 1.3.2
		 * @return string
		 */
		public function empty_sale_price( $sale_price, $product ) {
			$pre_order    = new YITH_Pre_Order_Product( $product );
            $is_pre_order = $pre_order->get_pre_order_status();

			$price_adjustment  = $pre_order->get_pre_order_price_adjustment();
			$manual_price      = $pre_order->get_pre_order_price();

			if ( 'manual' == $price_adjustment && empty( $manual_price ) ) {
				return $sale_price;
            }

			if ( 'yes' == $is_pre_order && 'yes' == get_option( 'yith_wcpo_show_regular_price' ) ) {
				return '0';
			}
			return $sale_price;
		}

		/**
		 * @param $on_sale
		 * @param $product
		 *
         * @since 1.3.2
		 * @return bool
		 */
		public function force_use_of_sale_price( $on_sale, $product ) {
			$pre_order    = new YITH_Pre_Order_Product( $product );
            $is_pre_order = $pre_order->get_pre_order_status();

			$price_adjustment  = $pre_order->get_pre_order_price_adjustment();
			$manual_price      = $pre_order->get_pre_order_price();

			// If the option guest_users_price is set to show_regular_price, disable the use of Sale price for only see the Regular price without a strikethrough price
			if ( 'yes' == $is_pre_order && ! get_current_user_id() && 'show_regular_price' == get_option( 'yith_wcpo_guest_users_price', 'show_pre_order_price' ) )
				return false;

			if ( 'manual' == $price_adjustment && empty( $manual_price ) ) {
				return $on_sale;
			}

			if ( 'yes' == $is_pre_order && 'yes' == get_option( 'yith_wcpo_show_regular_price' ) ) {
				$on_sale = true;
			}
			return $on_sale;
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
				if ( ! get_current_user_id() ) {
					switch ( get_option( 'yith_wcpo_guest_users_price', 'show_pre_order_price' ) ) {
						case 'show_regular_price' :
							return $variation->get_regular_price();
						case 'hidden_price' :
							return '';
					}
				}
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

		public function product_countdown_label( $label, $a, $product_id ) {

			$product          = wc_get_product( $product_id );
			$is_preorder  = yit_get_prop( $product, '_ywpo_preorder' );

			if ($is_preorder == 'yes' ){
				$option =  get_option( 'yith_wcpo_countdown_label' );
				if ( $option ) {
					$label = $option;
				}
			}

			return $label;
		}

		/*
		 * Flatsome fix for showing availability date in Quick View
		 *
		 * @since 1.3.2
		 */
		public function flatsome_fix() {
			?>
            <script type="text/javascript">
                jQuery( 'div.pre_order_single' ).each( function () {
                    var unix_time = parseInt( jQuery( this ).data( 'time' ) );
                    var date = new Date(0);
                    date.setUTCSeconds( unix_time );
                    var time = date.toLocaleTimeString();
                    time = time.slice(0, -3);
                    jQuery( this ).find( '.availability_date' ).text( date.toLocaleDateString() );
                    jQuery( this ).find( '.availability_time' ).text( time );
                });
            </script>
			<?php
		}

		/**
		 * Shortcode for displaying Pre-Order products
		 * @param $atts
		 *
		 * @return string
		 */
		public function pre_order_products_loop( $atts ) {
			$atts = shortcode_atts( array(
				'columns' => '4',
				'orderby' => 'title',
				'order'   => 'asc',
                'posts_per_page' => 8
			), $atts, 'products' );

			$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

			$query_args = array(
				'post_type'           => array( 'product', 'product_variation' ),
				'post_status'         => 'publish',
				'ignore_sticky_posts' => 1,
                'columns'             => $atts['columns'],
				'orderby'             => $atts['orderby'],
				'order'               => $atts['order'],
				'posts_per_page'      => $atts['posts_per_page'],
				'paged'               => $paged,
				'meta_query' => array(
					array(
						'key' => '_ywpo_preorder',
						'value' => 'yes',
						'compare' => '='
					)
				)
			);

			wp_register_script( 'yith-wcpo-frontend-shop-loop', YITH_WCPO_ASSETS_JS_URL . yit_load_js_file( 'frontend-shop-loop.js' ), array( 'jquery' ), YITH_WCPO_VERSION, 'true' );
			wp_enqueue_script( 'yith-wcpo-frontend-shop-loop' );

			return self::product_loop( $query_args, $atts, 'yith_pre_order_products' );
		}

		/**
		 * Loop over found products.
		 * @param  array $query_args
		 * @param  array $atts
		 * @param  string $loop_name
		 * @return string
		 */
		private static function product_loop( $query_args, $atts, $loop_name ) {
			global $woocommerce_loop;

			$products                    = new WP_Query( apply_filters( 'woocommerce_shortcode_products_query', $query_args, $atts, $loop_name ) );
			$columns                     = absint( $atts['columns'] );
			$woocommerce_loop['columns'] = $columns;
			$woocommerce_loop['name']    = $loop_name;

			ob_start();
			if ( is_singular( 'product' ) ) :

				while ( have_posts() ) : the_post();

					wc_get_template_part( 'content', 'single-product' );

				endwhile;
			else :
				if ( $products->have_posts() ) : ?>

					<?php do_action( "woocommerce_shortcode_before_{$loop_name}_loop" ); ?>

					<?php woocommerce_product_loop_start(); ?>

					<?php while ( $products->have_posts() ) : $products->the_post(); ?>

						<?php wc_get_template_part( 'content', 'product' ); ?>

					<?php endwhile; // end of the loop. ?>

					<?php woocommerce_product_loop_end(); ?>

					<?php do_action( "woocommerce_shortcode_after_{$loop_name}_loop" ); ?>
					<?php do_action( 'yith_wcpo_pagination_nav', $products->max_num_pages ); ?>

				<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

					<?php do_action( 'woocommerce_no_products_found' ); ?>

				<?php endif; ?>
			<?php endif; ?>

			<?php

			woocommerce_reset_loop();
			wp_reset_postdata();

			return '<div class="woocommerce columns-' . $columns . '">' . ob_get_clean() . '</div>';
		}

		/**
		 * Prints template for displaying navigation panel for pagination
		 *
		 * @param $max_num_pages
		 */
		public function pagination_nav( $max_num_pages ) {
			ob_start();
			wc_get_template( 'frontend/yith-pre-order-pagination-nav.php', array( 'max_num_pages' => $max_num_pages ), '', YITH_WCPO_WC_TEMPLATE_PATH );
			echo ob_get_clean();
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

			// YITH WooCommerce Subscription compatibility //
			if ( defined( 'YITH_YWSBS_VERSION' ) ) {
				$params = array(
					'add_to_cart_label' => get_option( 'ywsbs_add_to_cart_label' )
				);
			} else {
				$params = array(
					'add_to_cart_label' => get_option( 'ywsbs_add_to_cart_label' ),
					'default_cart_label' => apply_filters( 'ywsbs_add_to_cart_default_label', __( 'Add to cart', 'woocommerce' ) )
				);
			}
			wp_localize_script( 'yith_ywsbs_frontend', 'yith_ywsbs_frontend', $params );
			/////////////////////////////////////////////////
		}
		
	}
}