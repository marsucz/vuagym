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
 * @class      YITH_Pre_Order_Premium
 * @package    Yithemes
 * @since      Version 1.0.0
 * @author     Your Inspiration Themes
 *
 */

if ( ! class_exists( 'YITH_Pre_Order_Premium' ) ) {
	/**
	 * Class YITH_Pre_Order_Premium
	 *
	 * @author Carlos Mora <carlos.eugenio@yourinspiration.it>
	 */
	class YITH_Pre_Order_Premium  extends YITH_Pre_Order
	{

		/**
		 * Main Instance
		 *
		 * @var YITH_Pre_Order_Premium
		 * @since  1.0
		 * @access protected
		 */
		protected static $_instance = null;

		/**
		 * Main Cron Jobs Instance
		 *
		 * @var YITH_Pre_Order_Scheduling
		 * @since 1.0
		 */
		public $scheduling = null;

		/**
		 * Construct
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 */
		protected function __construct() {
			parent::__construct();
		}

		/**
		 * Main plugin Instance
		 *
		 * @return YITH_Pre_Order_Premium Main instance
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public static function instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		public function init_includes() {
		    parent::init_includes();
            require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-admin-premium.php' );
            require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-edit-product-page-premium.php' );
            require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-frontend-premium.php' );
            require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-my-account-premium.php' );
            require_once( YITH_WCPO_PATH . 'includes/class.yith-pre-order-scheduling.php' );
        }


		/**
		 * Class Initializzation
		 *
		 * Instance the admin or frontend classes
		 *
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 * @since  1.0
		 * @return void
		 * @access protected
		 */

		public function init() {

			$this->scheduling = new YITH_Pre_Order_Scheduling();
			$this->download_links = new YITH_Pre_Order_Download_Links();

			if ( is_admin() ) {
				$this->admin = new YITH_Pre_Order_Admin_Premium();
			}

			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				$this->frontend = new YITH_Pre_Order_Frontend_Premium();
			}
		}

		public function init_my_account() {
            new YITH_Pre_Order_My_Account_Premium();
        }

	}
}