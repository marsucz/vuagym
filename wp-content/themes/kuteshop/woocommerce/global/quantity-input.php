<?php
/**
 * Product quantity inputs
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>
<div class="detail-qty border radius quantity">
	<a href="#" class="qty-down"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
	<input type="text" step="<?php echo esc_attr( $step ); ?>" min="<?php echo esc_attr( $min_value ); ?>" max="<?php echo esc_attr( $max_value ); ?>" name="<?php echo esc_attr( $input_name ); ?>" value="<?php echo esc_attr( $input_value ); ?>" title="<?php echo esc_attr_x( 'Qty', 'Product quantity input tooltip', 'aloshop' ) ?>" class="input-text text qty qty-val" size="4" />
	<a href="#" class="qty-up"><i class="fa fa-caret-up" aria-hidden="true"></i></a>
</div>