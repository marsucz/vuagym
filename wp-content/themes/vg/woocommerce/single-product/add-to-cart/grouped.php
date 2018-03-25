<?php
/**
 * Grouped product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/grouped.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $post;

do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart" method="post" enctype='multipart/form-data'>
	<div class="table-group-detail table-responsive border radius">
		<table cellspacing="0" class="group_table table">
			<tbody>
				<tr>
					<th><span><?php esc_html_e("PRODUCT NAME","kuteshop")?></span></th>
					<th><span><?php esc_html_e("price","kuteshop")?></span></th>
					<th><span><?php esc_html_e("Qty","kuteshop")?></span></th>
				</tr>
				<?php
					$quantites_required = false;

					foreach ( $grouped_products as $grouped_product ) {
						$post_object        = get_post( $grouped_product->get_id() );
						$quantites_required = $quantites_required || ( $grouped_product->is_purchasable() && ! $grouped_product->has_options() );

						setup_postdata( $GLOBALS['post'] =& $post_object );
						?>
						<tr id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
							<td>
								<div class="product-thumb">
									<a class="product-thumb-link" href="<?php esc_url(get_permalink())?>">
										<?php echo get_the_post_thumbnail($grouped_product->get_id(),array(120,120))?>
									</a>
								</div>
								<h3 class="product-title"><a href="<?php esc_url(get_permalink())?>"><?php the_title();?></a></h3>
							</td>
							<td>								
								<?php do_action( 'woocommerce_grouped_product_list_before_price', $grouped_product ); ?>
								<div class="product-price">
									<?php
										echo balanceTags($grouped_product->get_price_html());
										echo wc_get_stock_html( $grouped_product );
									?>
								</div>
							</td>
							<td>
								<?php if ( ! $grouped_product->is_purchasable() || $grouped_product->has_options() ) : ?>
									<?php woocommerce_template_loop_add_to_cart(); ?>

								<?php elseif ( $grouped_product->is_sold_individually() ) : ?>
									<input type="checkbox" name="<?php echo esc_attr( 'quantity[' . $grouped_product->get_id() . ']' ); ?>" value="1" class="wc-grouped-product-add-to-cart-checkbox" />

								<?php else : ?>
									<?php
										/**
										 * @since 3.0.0.
										 */
										do_action( 'woocommerce_before_add_to_cart_quantity' );

										woocommerce_quantity_input( array(
											'input_name'  => 'quantity[' . $grouped_product->get_id() . ']',
											'input_value' => isset( $_POST['quantity'][ $grouped_product->get_id() ] ) ? wc_stock_amount( $_POST['quantity'][ $grouped_product->get_id() ] ) : 0,
											'min_value'   => apply_filters( 'woocommerce_quantity_input_min', 0, $grouped_product ),
											'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $grouped_product->get_max_purchase_quantity(), $grouped_product ),
										) );

										/**
										 * @since 3.0.0.
										 */
										do_action( 'woocommerce_after_add_to_cart_quantity' );
									?>
								<?php endif; ?>
							</td>
						</tr>
						<?php
					}
					wp_reset_postdata();
				?>
			</tbody>
		</table>

		<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" />

		<?php if ( $quantites_required ) : ?>

			<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
			<?php 
				$html_wl = '';
	        	if(class_exists('YITH_WCWL_Init')) $html_wl = '<a href="'.esc_url(str_replace('&', '&amp;',add_query_arg( 'add_to_wishlist', get_the_ID() ))).'" class="add_to_wishlist wishlist-link" rel="nofollow" data-product-id="'.get_the_ID().'"><i class="fa fa-heart" aria-hidden="true"></i><span>'.esc_html__("Wishlist","kuteshop").'</span></a>';
	        ?>
			<div class="detail-extralink">
				<div class="product-extra-link2">
					<button type="submit" class="single_add_to_cart_button button alt"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
					<?php echo balanceTags($html_wl);?>
					<?php echo s7upf_compare_url();?>
				</div>
			</div>			

			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		<?php endif; ?>		
	</div>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' );?>
