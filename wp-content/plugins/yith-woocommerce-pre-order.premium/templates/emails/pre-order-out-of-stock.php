<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$product = wc_get_product( $email->object );
$product_id = yit_get_base_product_id( $product );
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
			empty( $link ) ? $product->get_title() : '<a href="' . $link . '">' . $product->get_title() . '</a>' );
		?>
	</p>
	<div><?php echo $product->get_image() ?></div>

<?php do_action( 'woocommerce_email_footer' );