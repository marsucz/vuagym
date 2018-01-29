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
 * @class      YITH_Pre_Order_For_Sale_Date_Changed_Email
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Carlos Mora <carlos.eugenio@yourinspiration.it>
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_For_Sale_Date_Changed_Email' ) ) {
	/**
	 * Class YITH_Pre_Order_For_Sale_Date_Changed_Email
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_For_Sale_Date_Changed_Email extends WC_Email {

		public function __construct() {

			$this->id = 'yith_ywpo_sale_date_changed';
			$this->customer_email = true;

			$this->title         = __( 'YITH Pre-Order: For sale date has changed', 'yith-woocommerce-pre-order' );
			$this->description   = __( 'If the administrator changes the "for sale" date, the user will receive an email', 'yith-woocommerce-pre-order' );
			$this->heading       = __( 'Your Pre-Order product has a new release date!', 'yith-woocommerce-pre-order' );
			$this->subject       = __( 'Your Pre-Order product has a new release date!' , 'yith-woocommerce-pre-order' );
			$this->template_html = 'emails/pre-order-date-changed.php';

			add_action( 'yith_ywpo_sale_date_changed', array( $this, 'trigger' ), 10, 4 );

			parent::__construct();
		}

		public function trigger( $customer, $product_id, $previous_sale_date, $new_sale_date ) {
			if ( !$this->is_enabled() ) {
				return;
			}

			$this->object = array(
				'customer_name'      => $customer['name'],
				'customer_email'     => $customer['email'],
				'customer_order_id'  => $customer['order'],
				'product_id'         => $product_id,
				'previous_sale_date' => $previous_sale_date,
				'new_sale_date'      => $new_sale_date,
			);

			$this->send( $this->object['customer_email'],
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
					'title'   => __( 'Enable/Disable', 'yith-woocommerce-pre-order' ),
					'type'    => 'checkbox',
					'label'   => __( 'Enable this email notification', 'yith-woocommerce-pre-order' ),
					'default' => 'yes'
				),
				'subject'    => array(
					'title'       => __( 'Subject', 'yith-woocommerce-pre-order' ),
					'type'        => 'text',
					'description' => sprintf( __( 'This controls the email subject line. Leave blank to use the default subject: <code>%s</code>.', 'woocommerce' ), $this->subject ),
					'placeholder' => '',
					'default'     => '',
					'desc_tip'    => true
				),
				'heading'    => array(
					'title'       => __( 'Email Heading', 'yith-woocommerce-pre-order' ),
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
return new YITH_Pre_Order_For_Sale_Date_Changed_Email();