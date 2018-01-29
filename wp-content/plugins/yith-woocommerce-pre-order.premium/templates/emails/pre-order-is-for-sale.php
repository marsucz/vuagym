<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'woocommerce_email_header', $email_heading, $email );
$product = wc_get_product( $email->object['product_id'] );
$order = wc_get_order( $email->object['customer_order_id'] );
$order_id = yit_get_order_id( $order );
?>

    <p>
		<?php
		printf( __( "Hi %s, the product '%s' you purchased in Pre-Order is now available. Your order #%s is currently in process" , 'yith-woocommerce-pre-order' ),
			$email->object['customer_name'],
			'<a href="'. $product->get_permalink() .'">' . $product->get_title() . '</a>',
			'<a href="' . $order->get_view_order_url() . '">' . $order_id . '</a>'
		);
		?>
    </p>

<?php
if ( $product->is_downloadable() && $order->is_download_permitted() ) {
	?>
    <p><?php _e( 'Download link:', 'yith-woocommerce-pre-order' ); ?></p><?php
	foreach ( $order->get_items() as $item ) {
		$item_product_id = ! empty( $item['variation_id'] ) ? $item['variation_id'] : $item['product_id'];
		if ( $email->object['product_id'] == $item_product_id ) {
			if ( $order instanceof WC_Data ) {
				wc_display_item_meta( $item );
				wc_display_item_downloads( $item );
			} else {
				$order->display_item_meta( $item );
				$order->display_item_downloads( $item );
			}
		}
	}
}

do_action( 'woocommerce_email_footer' );