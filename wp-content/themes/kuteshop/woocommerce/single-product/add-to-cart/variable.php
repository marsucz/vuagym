<?php
/**
 * Variable product add to cart
 *
 * @author  WooThemes
 * @package WooCommerce/Templates
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

do_action( 'woocommerce_before_add_to_cart_form' ); 

$product = wc_get_product($product->get_id());
$attribute_keys = array_keys( $attributes );

//echo '<pre>';
//print_r($attributes);
//echo '<pre>';

    $available_variations = $product->get_available_variations();
?>

<form class="variations_form cart" method="post" enctype='multipart/form-data' data-product_id="<?php echo absint( $product->get_id() ); ?>" data-product_variations="<?php echo esc_attr( json_encode( $available_variations ) ) ?>">
	<?php do_action( 'woocommerce_before_variations_form' ); ?>

	<?php if ( empty( $available_variations ) && false !== $available_variations ) : ?>
		<p class="stock out-of-stock"><?php esc_html_e( 'This product is currently out of stock and unavailable.', 'kuteshop' ); ?></p>
	<?php else : ?>
		<?php 
			$attribute_style = s7upf_get_value_by_id('attribute_style');
		?>
		<div class="wrap-attr-product <?php echo esc_attr($attribute_style)?>">
			<div class="variations">
				<?php foreach ( $attributes as $attribute_name => $options ) : ?>
					<?php 	
						if($attribute_style == 'special'){?>
							<div class="attr-product attr-hover-box attr-<?php echo esc_attr($attribute_name)?>">
								<div class="attr-title">
									<strong><sup>*</sup><?php echo wc_attribute_label( $attribute_name ); ?>:</strong><span class="current-color"><?php esc_html_e("None","kuteshop");?></span>
								</div>
								<div class="attr-filter">
				                    <ul class="list-inline list-filter color-filter" data-attribute-id="<?php echo esc_attr($attribute_name)?>">
				                    	<?php
					                    	if ( ! empty( $options ) ) {
												if ( $product && taxonomy_exists( $attribute_name ) ) {
													// Get terms if this is a taxonomy - ordered. We need the names too.
													$terms = wc_get_product_terms( $product->get_id(), $attribute_name, array( 'fields' => 'all' ) );
                                                                                                        
													foreach ( $terms as $term ) {
														if ( in_array( $term->slug, $options ) ) {
															echo '<li data-attribute="' . esc_attr( $term->slug ) . '"><a href="#" class="bgcolor-' . esc_attr( $term->slug ) . '"><span></span>'.esc_html($term->name).'</a></li>';
														}
													}
												} else {
													foreach ( $options as $option ) {
														echo '<li data-attribute="' . esc_attr( $term->slug ) . '"><a href="#" class="bgcolor-' . esc_attr( $term->slug ) . '"><span></span>'.esc_html($term->name).'</a></li>';
													}
												}
											}
				                    	?>
				                    </ul>
			                	</div>
			                </div>
			        <?php
				        }
				    ?>
		            	<div class="default-attribute attr-product">
							<div class="attr-title">
								<strong><sup>*</sup><?php echo wc_attribute_label( $attribute_name ); ?>:</strong><span class="current-color"><?php esc_html_e("None","kuteshop");?></span>
							</div>
							<div class="attr-detail-box">
								<?php
									$selected = isset( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) ? wc_clean( $_REQUEST[ 'attribute_' . sanitize_title( $attribute_name ) ] ) : $product->get_variation_default_attribute( $attribute_name );
									wc_dropdown_variation_attribute_options( array( 'options' => $options, 'attribute' => $attribute_name, 'product' => $product, 'selected' => $selected ) );
									
								?>
							</div>
						</div>
		        <?php endforeach;?>
			</div>			
			<?php //echo balanceTags(s7upf_get_price_html());?>
			<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
			<div class="single_variation_wrap" style="display:none;">
				<?php
					/**
					 * woocommerce_before_single_variation Hook
					 */
					do_action( 'woocommerce_before_single_variation' );

					/**
					 * woocommerce_single_variation hook. Used to output the cart button and placeholder for variation data.
					 * @since 2.4.0
					 * @hooked woocommerce_single_variation - 10 Empty div for variation data.
					 * @hooked woocommerce_single_variation_add_to_cart_button - 20 Qty and cart button.
					 */
					do_action( 'woocommerce_single_variation' );

					/**
					 * woocommerce_after_single_variation Hook
					 */
					do_action( 'woocommerce_after_single_variation' );
				?>
				<div class="clearfix"></div>
			</div>		
		    <a class="reset_variations" href="#"><?php esc_html_e("Clear selection","kuteshop")?></a>
			<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
		</div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_after_variations_form' ); ?>
</form>

<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
