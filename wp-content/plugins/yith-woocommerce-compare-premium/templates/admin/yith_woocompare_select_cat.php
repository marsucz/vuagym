<?php
/**
 * Select category type
 *
 * @package    YITH Woocommerce Compare Premium
 * @author     Francesco Licandro <francesco.licandro@yithemes.com>
 * @since      1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
?>

<tr valign="top">
	<th scope="row" class="image_upload">
		<label for="<?php echo $id ?>"><?php echo $name ?></label>
		<?php echo function_exists( 'wc_help_tip' ) ? wc_help_tip( $desc ) : ''; ?>
	</th>
	<td class="forminp forminp-color plugin-option">

		<div id="<?php echo $id ?>-container" class="yit_options rm_option rm_input rm_text rm_upload">
			<div class="option">

				<?php yit_add_select2_fields( array(
					'class'             => 'wc-product-search',
					'id'                => $id,
					'name'              => $id,
					'data-selected'     => $data_selected,
					'data-placeholder'  => __( 'Search for a category&hellip;', 'yith-woocommerce-compare' ),
					'data-multiple'     => true,
					'data-action'       => 'yith_woocompare_search_product_cat',
					'value'             => $value,
					'style'             => 'width: 50%;'
				) ); ?>
			</div>
		</div>


	</td>
</tr>