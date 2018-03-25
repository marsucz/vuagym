<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$product = wc_get_product( $email->object );
$product_id = yit_get_base_product_id( $product );
$product_name = version_compare( WC()->version, '3.0.0', '<' ) ? $product->get_formatted_name() : $product->get_name();
$post = get_post( $product_id );
$post_type_object = get_post_type_object( $post->post_type );
if ( ($post_type_object ) && ( $post_type_object->_edit_link )) {
	$link = admin_url( sprintf( $post_type_object->_edit_link . '&action=edit', $product_id ) );
} else {
	$link = '';
}

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php _e( 'Hi admin!', 'yith-woocommerce-pre-order' ); ?></p>
	<p>
		<?php printf( __( "We would like to inform you that the product %s is now 'Out-of-Stock' and turned into a Pre-Order product.", 'yith-woocommerce-pre-order' ),
			empty( $link ) ? $product->get_title() : '<a href="' . $link . '">' . $product_name . '</a>' );
		?>
	</p>
	<div>
		<?php
		$dimensions = wc_get_image_size( 'shop_thumbnail' );
		$height     = esc_attr( $dimensions['height'] );
		$width      = esc_attr( $dimensions['width'] );
		$src        = ( $product->get_image_id() ) ? current( wp_get_attachment_image_src( $product->get_image_id(), 'shop_catalog' ) ) : wc_placeholder_img_src();

		$image = '<a href="' . $link .'"><img src="'. $src . '" height="' . $height . '" width="' . $width . '" /></a>';
		echo $image;
		?>
    </div>

<?php do_action( 'woocommerce_email_footer' );