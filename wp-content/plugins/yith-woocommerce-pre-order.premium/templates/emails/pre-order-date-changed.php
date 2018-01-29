<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$title = wc_get_product($email->object['product_id'])->get_title();


$gmt_offset = get_option( 'gmt_offset' );
if ( 0 <= $gmt_offset )
	$offset_name = '+' . $gmt_offset;
else
	$offset_name = (string)$gmt_offset;

$offset_name = str_replace( array( '.25', '.5', '.75' ), array( ':15', ':30', ':45' ), $offset_name );
$offset_name = 'UTC' . $offset_name;

?>

<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

	<p><?php _e( 'Pre-order date updated', 'yith-woocommerce-pre-order' ); ?></p>
	<p><?php printf( __( "Hi %s, we would like to inform you that the release date of the product %s you purchased in Pre-Order, has changed from %s to %s (%s)", 'yith-woocommerce-pre-order' ),
			$email->object['customer_name'],
			$title,
			$email->object['previous_sale_date'],
			$email->object['new_sale_date'],
			$offset_name );
		?></p>

<?php do_action( 'woocommerce_email_footer' );