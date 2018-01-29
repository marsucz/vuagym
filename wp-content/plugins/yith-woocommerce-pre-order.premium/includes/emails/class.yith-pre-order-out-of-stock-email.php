<?php
/*
 * This file belongs to the YIT Framework.
 *
 * This source file is subject to the GNU GENERAL PUBLIC LICENSE (GPL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.txt
 */
if ( ! defined( 'YITH_WCPO_VERSION' ) ) {
	exit( 'Direct access forbidden.' );
}

/**
 *
 *
 * @class      YITH_Pre_Order_Out_Of_Stock_Email
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_Out_Of_Stock_Email' ) ) {
	/**
	 * Class YITH_Pre_Order_Out_Of_Stock_Email
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Out_Of_Stock_Email extends WC_Email {

		public function __construct() {

			$this->id = 'yith_ywpo_out_of_stock';

			$this->title         = __( 'YITH Pre-Order: Out-of-Stock products turn into Pre-Order automatically', 'yith-woocommerce-pre-order' );
			$this->description   = __( 'The administrator will receive an email when a product is out-of-stock and turned into Pre-Order.', 'yith-woocommerce-pre-order' );
			$this->heading       = __( 'A product turned into Pre-Order', 'yith-woocommerce-pre-order' );
			$this->subject       = __( 'A product turned into Pre-Order', 'yith-woocommerce-pre-order' );
			$this->template_html = 'emails/pre-order-out-of-stock.php';

			add_action( 'yith_ywpo_out_of_stock', array( $this, 'trigger' ) );

			parent::__construct();

			$this->recipient = get_option( 'admin_email' );
		}

		public function trigger( $product_id ) {
			if ( !$this->is_enabled() ) {
				return;
			}
			$this->object = $product_id;

			$this->send( $this->get_recipient(),
				$this->get_subject(),
				$this->get_content(),
				$this->get_headers(),
				$this->get_attachments() );
		}

		public function get_content_html() {
			return wc_get_template_html( $this->template_html, array(
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => true,
				'plain_text'    => false,
				'email'         => $this
			),
				'',
				YITH_WCPO_TEMPLATE_PATH );
		}

		public function get_content_plain() {
			return wc_get_template_html( $this->template_plain, array(
				'email_heading' => $this->get_heading(),
				'sent_to_admin' => true,
				'plain_text'    => true,
				'email'         => $this
			),
				'',
				YITH_WCPO_TEMPLATE_PATH );
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'    => array(
					'title'   => __( 'Enable/Disable', 'woocommerce' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'woocommerce' ),
					'default' => 'yes'
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'woocommerce' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the main heading included in the email notification. Leave blank to use the default heading: <code>%s</code>.', 'woocommerce' ), $this->heading ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true
				),
				'email_type' => array(
					'title'       => __( 'Email type', 'woocommerce' ),
					'type'        => 'select',
					'description' => __( 'Choose which format of email to send.', 'woocommerce' ),
					'default'     => 'html',
					'class'       => 'email_type wc-enhanced-select',
					'options'     => $this->get_email_type_options(),
					'desc_tip'    => true
				)
			);
		}

	}

}
return new YITH_Pre_Order_Out_Of_Stock_Email();
