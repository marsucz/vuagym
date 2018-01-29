<?php
/**
 * Frontend class
 *
 * @author Yithemes
 * @package YITH WooCommerce Checkout Manager
 * @version 1.0.0
 */

if ( ! defined( 'YWCCP' ) ) {
	exit;
} // Exit if accessed directly

if ( ! class_exists( 'YWCCP' ) ) {
	/**
	 * Main class.
	 * The class manage all the frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YWCCP {

		/**
		 * Single instance of the class
		 *
		 * @var \YWCCP
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YWCCP_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YWCCP_Front
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {
			// Load Plugin Framework
			add_action( 'after_setup_theme', array( $this, 'plugin_fw_loader' ), 1 );

			// Class admin
			if ( $this->is_admin() ) {
				require_once('class.ywccp-admin.php');
				YWCCP_Admin();
			}
			// Class frontend
			else {
				require_once('class.ywccp-front.php');
				YWCCP_Front();
			}
		}

		/**
		 * Load Plugin Framework
		 *
		 * @since  1.0
		 * @access public
		 * @return void
		 * @author Andrea Grillo <andrea.grillo@yithemes.com>
		 */
		public function plugin_fw_loader() {
			if ( ! defined( 'YIT_CORE_PLUGIN' ) ) {
				global $plugin_fw_data;
				if( ! empty( $plugin_fw_data ) ){
					$plugin_fw_file = array_shift( $plugin_fw_data );
					require_once( $plugin_fw_file );
				}
			}
		}
		
		/**
		 * Check if is admin
		 * 
		 * @since 1.0.5
		 * @author Francesco Licandro
		 * @return boolean
		 */
		public function is_admin() {
			$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_REQUEST['context'] ) && $_REQUEST['context'] == 'frontend';
			return apply_filters( 'yith_wccp_is_admin_filter', is_admin() && ! $is_ajax );
		}
	}
}
/**
 * Unique access to instance of YWCCP class
 *
 * @return \YWCCP
 * @since 1.0.0
 */
function YWCCP(){
	return YWCCP::get_instance();
}