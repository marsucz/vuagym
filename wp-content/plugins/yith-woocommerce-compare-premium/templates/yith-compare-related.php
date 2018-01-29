<?php
/**
 * Compare related products template
 *
 * @author Your Inspiration Themes
 * @package YITH WooCommerce Compare
 * @version 1.0.0
 */

global $product;

?>
<div id="yith-woocompare-related" class="woocommerce" <?php if( $iframe ) echo 'data-iframe="1"' ?>>
	<h3 class="yith-woocompare-related-title"><?php echo $related_title ?></h3>
	<div class="yith-woocompare-related-wrapper">
		<ul class="related-products">
			<?php foreach( $products as $product_id ) : $product = wc_get_product( $product_id ); ?>
				<li class="ralated-product">
					<?php
					do_action( 'yith_woocompare_before_single_related_product' );
					?>
					<a href="<?php echo $product->get_permalink() ?>" target="_parent">
						<div class="product-image">
							<?php
							wc_get_template( 'loop/sale-flash.php' );
							echo $product->get_image('shop_catalog');
							?>
						</div>

						<h3 class="product-title"><?php echo $product->get_title() ?></h3>

						<div class="product-price">
							<?php echo $product->get_price_html() ?>
						</div>
					</a>

					<div class="woocommerce add-to-cart">
						<?php woocommerce_template_loop_add_to_cart() ?>
					</div>

					<?php echo do_shortcode('[yith_compare_button product="' . $product_id .' type="button"]' ); ?>

					<?php
					do_action( 'yith_woocompare_after_single_related_product' );
					?>
				</li>
			<?php endforeach; ?>
		</ul>

		<?php if( count( $products ) >= get_option( 'yith-woocompare-related-visible-num' ) && get_option('yith-woocompare-related-navigation') == 'yes' ) : ?>
			<div class="related-slider-nav">
				<div class="related-slider-nav-prev"></div>
				<div class="related-slider-nav-next"></div>
			</div>
		<?php endif ?>

	</div>
</div>