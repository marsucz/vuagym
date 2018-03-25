<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<?php
	/**
	 * woocommerce_before_single_product hook
	 *
	 * @hooked wc_print_notices - 10
	 */
	 do_action( 'woocommerce_before_single_product' );

	 if ( post_password_required() ) {
	 	echo get_the_password_form();
	 	return;
	 }
?>
<!-- Khoa Anh edit -->
<div class="product-detail-content">
	<div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>
		<?php s7upf_product_main_detai();?>
		<meta itemprop="url" content="<?php the_permalink(); ?>" />	
	</div>
	<?php do_action( 'woocommerce_after_single_product' ); ?>
</div>
<!-- end -->
