<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly


if ( ! class_exists( 'YWTM_Product_Tab' ) ) {

	class YWTM_Product_Tab {
		/**
		 * @var Single instance of the class
		 * @since 1.0.0
		 */
		protected static $instance;
		/**
		 * @var array of tabs
		 */
		protected $tabs = array();


		public function __construct() {



			add_filter( 'woocommerce_product_write_panel_tabs', array( $this, 'add_custom_tab_product_edit' ), 98 );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_product_tab_metabox' ), 30, 2 );
			add_action( 'wp_enqueue_scripts', array( $this, 'include_style_and_script' ) );

			add_filter( 'woocommerce_product_write_panel_tabs', array( $this, 'add_woocommerce_tabs_edit' ), 99 );



		}

		/**include style and script in frontend
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use wp_enqueue_scripts
		 */
		public function include_style_and_script() {

			if ( is_product() ) {

				$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

				wp_register_style( 'yit-tabmanager-frontend', YWTM_ASSETS_URL . 'css/yith-tab-manager-frontend.css', true, YWTM_VERSION );

				wp_enqueue_style( 'yit-tabmanager-frontend' );

				wp_enqueue_script( 'yit-tabmanager-script', YWTM_ASSETS_URL . 'js/frontend/tab_templates' . $suffix . '.js', array( 'jquery' ), YWTM_VERSION, true );

				$params = array(
					'admin_url' => admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ),
					'action'    => array(
						'ywtm_sendermail' => 'ywtm_sendermail'
					)
				);


				wp_localize_script( 'yit-tabmanager-script', 'ywtm_params', $params );

				wp_register_script( 'yit-tabmap-script', YWTM_ASSETS_URL . 'js/frontend/gmap3.min.js', array(
					'jquery',
					'ywtm-google-map'
				), '6.0.0', true );


				if ( ! wp_script_is( 'prettyPhoto' ) || version_compare( WC()->version, '3.0.0', '>=' ) ) {
					wp_register_script( 'prettyPhoto', YWTM_ASSETS_URL . 'js/frontend/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.6', true );
					wp_register_script( 'prettyPhoto-init', YWTM_ASSETS_URL . 'js/frontend/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array(
						'jquery',
						'prettyPhoto'
					), '3.1.6', true );
					wp_register_style( 'woocommerce_prettyPhoto_css', YWTM_ASSETS_URL . 'css/prettyPhoto/prettyPhoto.css', array(), '3.1.6' );
				}
			}


		}

		/** Add tabs in product data section
		 *
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use woocommerce_product_write_panel_tabs filter
		 */
		public function add_custom_tab_product_edit() {
			global $post;
			$save_post = $post;

			$global_tab = $this->get_global_tab();


			foreach ( $global_tab as $tab ):?>

                <li class="my-tabs <?php echo $tab->ID; ?>_tab">
                    <a href="#<?php echo $tab->ID; ?>_tab"><?php echo get_html_icon( $tab->ID ); ?><?php echo $tab->post_title; ?></a>
                </li>
				<?php $this->tabs[] = $tab->ID; ?>
			<?php endforeach;

			$categories   = wp_get_post_terms( $post->ID, 'product_cat', array( "fields" => "ids" ) );
			$category_tab = $this->get_category_tab();


			foreach ( $category_tab as $tab ) {
				$cats = ywtm_get_meta( $tab->ID, '_ywtm_tab_category' );

				if ( is_array( $cats ) && count( $cats ) > 0 ) {
					foreach ( $cats as $cat ) {
						$cat_id = yit_wpml_object_id( $cat, 'product_cat' );
						if ( in_array( $cat_id, $categories ) && ! in_array( $tab->ID, $this->tabs ) ) { ?>
                            <li class="my-tabs <?php echo $tab->ID; ?>_tab">
                                <a href="#<?php echo $tab->ID; ?>_tab"><?php echo get_html_icon( $tab->ID ); ?><?php echo $tab->post_title; ?></a>
                            </li>
							<?php $this->tabs[] = $tab->ID; ?>
							<?php
						}
					}
				}
			}

			$product_tab = $this->get_product_tab();


			foreach ( $product_tab as $tab ) {
				$prods = ywtm_get_meta( $tab->ID, '_ywtm_tab_product' );

				if ( is_array( $prods ) && count( $prods ) > 0 ) {
					foreach ( $prods as $prod ) {
						$prod_id = yit_wpml_object_id( $prod, 'product' );
						if ( $prod_id == $post->ID ) { ?>
                            <li class="my-tabs <?php echo $tab->ID; ?>_tab">
                                <a href="#<?php echo $tab->ID; ?>_tab"><?php echo get_html_icon( $tab->ID ); ?><?php echo $tab->post_title; ?></a>
                            </li>
							<?php $this->tabs[] = $tab->ID; ?>
							<?php
						}
					}
				}
			}
			$post = $save_post;

			add_action( 'woocommerce_product_data_panels', array( $this, 'write_tab_options' ) );

		}


		/**
		 * add woocommerce tabs in product edit
		 * @author YITHEMES
		 * @since 1.1.0
		 */
		public function add_woocommerce_tabs_edit() {
			?>

            <li class="my-tabs_ywtm_wc_tab">
                <a href="#ywtm_wc_tab"><?php _e( 'WooCommerce Tab', 'yith-woocommerce-tab-manager' ); ?></a>
            </li>

			<?php
			add_action( 'woocommerce_product_data_panels', array( $this, 'edit_woocommerce_tabs' ) );
		}


		/**get "global" tab type
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array
		 */
		public function get_global_tab() {

			$args = array(
				'post_type'        => 'ywtm_tab',
				'post_status'      => 'publish',
				'posts_per_page'   => - 1,
				'meta_key'         => '_ywtm_order_tab',
				'orderby'          => 'meta_value_num',
				'order'            => 'ASC',
				'meta_query'       => array(
					array(
						'key'     => '_ywtm_show_tab',
						'value'   => 1,
						'compare' => '='
					),
					array(
						'key'     => '_ywtm_enable_custom_content',
						'value'   => 0,
						'compare' => '='
					),
					array(
						'key'     => '_ywtm_tab_type',
						'value'   => 'global',
						'compare' => '='
					)
				),
				'suppress_filters' => false
			);

			if ( function_exists( 'pll_get_post_language' ) ) {
				$args = ywtm_get_tab_ppl_language( $args );
			}


			if ( isset( $_GET['lang'] ) ) {
				$args['lang'] = $_GET['lang'];
			}

			if ( function_exists( 'wpml_get_language_information' ) ) {
				$lang_info    = wpml_get_language_information();
				$args['lang'] = $lang_info['language_code'];
			}

			$q_tabs = get_posts( $args );

			return $q_tabs;

		}

		/**get "category" tab type
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array
		 */
		public function get_category_tab() {

			$args = array(
				'post_type'        => 'ywtm_tab',
				'post_status'      => 'publish',
				'posts_per_page'   => - 1,
				'meta_key'         => '_ywtm_order_tab',
				'orderby'          => 'meta_value_num',
				'order'            => 'ASC',
				'meta_query'       => array(
					array(
						'key'     => '_ywtm_show_tab',
						'value'   => 1,
						'compare' => '='
					),
					array(
						'key'     => '_ywtm_enable_custom_content',
						'value'   => 0,
						'compare' => '='
					),
					array(
						'key'     => '_ywtm_tab_type',
						'value'   => 'category',
						'compare' => '='
					)
				),
				'suppress_filters' => false
			);


			if ( function_exists( 'pll_get_post_language' ) ) {
				$args = ywtm_get_tab_ppl_language( $args );
			}

			if ( isset( $_GET['lang'] ) ) {
				$args['lang'] = $_GET['lang'];
			}

			if ( function_exists( 'wpml_get_language_information' ) ) {
				$lang_info    = wpml_get_language_information();
				$args['lang'] = $lang_info['language_code'];
			}

			$q_tabs = get_posts( $args );

			return $q_tabs;
		}

		/**get "product" tab type
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return array
		 */
		public function get_product_tab() {

			$args = array(
				'post_type'        => 'ywtm_tab',
				'post_status'      => 'publish',
				'posts_per_page'   => - 1,
				'meta_key'         => '_ywtm_order_tab',
				'orderby'          => 'meta_value_num',
				'order'            => 'ASC',
				'meta_query'       => array(
					array(
						'key'     => '_ywtm_show_tab',
						'value'   => 1,
						'compare' => '='
					),
					array(
						'key'     => '_ywtm_enable_custom_content',
						'value'   => 0,
						'compare' => '='
					),
					array(
						'key'     => '_ywtm_tab_type',
						'value'   => 'product',
						'compare' => '='
					)
				),
				'suppress_filters' => false,

			);


			if ( function_exists( 'pll_get_post_language' ) ) {
				$args = ywtm_get_tab_ppl_language( $args );
			}

			if ( isset( $_GET['lang'] ) ) {
				$args['lang'] = $_GET['lang'];
			}

			if ( function_exists( 'wpml_get_language_information' ) ) {
				$lang_info    = wpml_get_language_information();
				$args['lang'] = $lang_info['language_code'];
			}

			$q_tabs = get_posts( $args );

			return $q_tabs;
		}

		/**
		 * print tab option tab
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use woocommerce_product_write_panels
		 */
		public function write_tab_options() {

			global $post;

			foreach ( $this->tabs as $tab ) {

				$layout_tab = get_post_meta( $tab, '_ywtm_layout_type', true );

				switch ( $layout_tab ) {

					case 'gallery' :
						include( YWTM_INC . 'product/admin/gallery.php' );
						break;

					case 'download' :
						include( YWTM_INC . 'product/admin/download.php' );
						break;

					case 'map' :
						include( YWTM_INC . 'product/admin/map.php' );
						break;

					case 'faq' :
						include( YWTM_INC . 'product/admin/faq.php' );
						break;

					case 'video' :
						include( YWTM_INC . 'product/admin/video.php' );

						break;

					case 'shortcode' :
						include( YWTM_INC . 'product/admin/shortcode.php' );
						break;

					case 'contact' :
						include( YWTM_INC . 'product/admin/contact.php' );
						break;
					default :
						include( YWTM_INC . 'product/admin/default.php' );
				}
			}
		}

		/**
		 * include template for woocommerce tabs
		 * @author YITHEMES
		 * @since 1.1.0
		 */
		public function edit_woocommerce_tabs() {

			include( YWTM_INC . 'product/admin/woocommerce-tabs.php' );
		}


		/**Save the custom product tab metabox
		 * @author YITHEMES
		 * @since 1.0.0
		 * @use woocommerce_process_product_meta
		 */
		public function save_product_tab_metabox( $post_id, $post ) {

			$global_tab = $this->get_global_tab();
			$tabs       = array();


			foreach ( $global_tab as $tab ) {
				$tabs[] = $tab->ID;
			}

			$categories   = wp_get_post_terms( $post->ID, 'product_cat', array( "fields" => "ids" ) );
			$category_tab = $this->get_category_tab();

			foreach ( $category_tab as $tab ) {
				$cats = ywtm_get_meta( $tab->ID, '_ywtm_tab_category' );
				if ( ! empty( $cats ) && is_array( $cats ) ) {
					foreach ( $cats as $cat ) {
						$cat_id = yit_wpml_object_id( $cat, 'product_cat' );
						if ( in_array( $cat_id, $categories ) ) {

							$tabs[] = $tab->ID;

						}
					}
				}
			}

			$product_tab = $this->get_product_tab();


			foreach ( $product_tab as $tab ) {
				$prods = ywtm_get_meta( $tab->ID, '_ywtm_tab_product' );
				if ( ! empty( $prods ) && is_array( $prods ) ) {
					foreach ( $prods as $prod ) {
						$prod_id = yit_wpml_object_id( $prod, 'product' );
						if ( $prod_id == $post->ID ) {
							$tabs[] = $tab->ID;
						}
					}
				}
			}

			$product = wc_get_product( $post->ID );
			foreach ( $tabs as $tab ) {

				$layout_tab = get_post_meta( $tab, '_ywtm_layout_type', true );

				switch ( $layout_tab ) {

					case 'download' :

						if ( isset( $_POST[ $tab . '_file_urls' ] ) ) {
							$files         = array();
							$file_names    = isset( $_POST[ $tab . '_file_names' ] ) ? array_map( 'wc_clean', $_POST[ $tab . '_file_names' ] ) : array();
							$file_urls     = isset( $_POST[ $tab . '_file_urls' ] ) ? array_map( 'wc_clean', $_POST[ $tab . '_file_urls' ] ) : array();
							$file_desc     = isset( $_POST[ $tab . '_file_desc' ] ) ? array_map( 'wc_clean', $_POST[ $tab . '_file_desc' ] ) : array();
							$file_url_size = sizeof( $file_urls );

							for ( $i = 0; $i < $file_url_size; $i ++ ) {
								if ( ! empty( $file_urls[ $i ] ) ) {
									$files[ md5( $file_urls[ $i ] ) ] = array(
										'name' => $file_names[ $i ],
										'file' => $file_urls[ $i ],
										'desc' => $file_desc[ $i ]
									);
								}
							}

							yit_save_prop( $product, $tab . '_custom_list_file', $files );

						} else {
							yit_delete_prop( $product, $tab . '_custom_list_file' );
						}
						break;

					case 'faq':

						if ( isset( $_POST[ $tab . '_faq_questions' ] ) ) {
							$faqs             = array();
							$faqs_question    = isset( $_POST[ $tab . '_faq_questions' ] ) ? array_map( 'wc_clean', $_POST[ $tab . '_faq_questions' ] ) : array();
							$faqs_answer      = isset( $_POST[ $tab . '_faq_answers' ] ) ? array_map( 'wc_clean', $_POST[ $tab . '_faq_answers' ] ) : array();
							$faqs_answer_size = sizeof( $faqs_answer );

							for ( $i = 0; $i < $faqs_answer_size; $i ++ ) {
								if ( ! empty( $faqs_answer[ $i ] ) ) {
									$faqs[ $i ] = array(
										'question' => $faqs_question[ $i ],
										'answer'   => $faqs_answer[ $i ]
									);
								}
							}

							yit_save_prop( $product, $tab . '_custom_list_faqs', $faqs );

						} else {
							yit_delete_prop( $product, $tab . '_custom_list_faqs' );
						}
						break;

					case 'gallery' :

						if ( isset ( $_POST[ $tab . '_custom_gallery_image_ids' ] ) ) {

							$gallery = explode( ",", $_POST[ $tab . '_custom_gallery_image_ids' ] );
							$images  = array();
							$i       = 0;

							foreach ( $gallery as $image ) {
								if ( ! empty( $image ) ) {
									$images[ $i ] = array(
										'id' => $image
									);
									$i ++;
								}
							}

							$gallery_setting['columns'] = isset( $_POST[ $tab . '_columns_number' ] ) ? $_POST[ $tab . '_columns_number' ] : 100;

							$args = array(
								'settings' => $gallery_setting,
								'images'   => $images
							);
							yit_save_prop( $product, $tab . '_custom_gallery', $args );

						} else {
							yit_delete_prop( $product, $tab . '_custom_gallery' );
						}
						break;

					case 'map' :


						$address = isset( $_POST[ $tab . '_custom_map_addr' ] ) ? $_POST[ $tab . '_custom_map_addr' ] : "";
						$width   = isset( $_POST[ $tab . '_custom_map_width' ] ) ? $_POST[ $tab . '_custom_map_width' ] : "";
						$height  = isset( $_POST[ $tab . '_custom_map_height' ] ) ? $_POST[ $tab . '_custom_map_height' ] : "";
						$zoom    = isset( $_POST[ $tab . '_custom_map_zoom' ] ) ? $_POST[ $tab . '_custom_map_zoom' ] : 15;
						$show_w  = isset( $_POST[ $tab . '_enable_width' ] ) ? $_POST[ $tab . '_enable_width' ] : 0;

						if ( ! empty( $address ) ) {
							$map_setting = array(

								'addr'       => $address,
								'wid'        => $width,
								'heig'       => $height,
								'zoom'       => $zoom,
								'show_width' => $show_w
							);

							yit_save_prop( $product, $tab . '_custom_map', $map_setting );
						} else {
							yit_delete_prop( $product, $tab . '_custom_map' );
						}
						break;

					case 'video' :

						$video_urls  = isset( $_POST[ $tab . '_video_urls' ] ) ? $_POST[ $tab . '_video_urls' ] : array();
						$video_ids   = isset( $_POST[ $tab . '_video_ids' ] ) ? $_POST[ $tab . '_video_ids' ] : array();
						$video_hosts = isset( $_POST[ $tab . '_video_hosts' ] ) ? $_POST[ $tab . '_video_hosts' ] : array();

						if ( ! empty( $video_urls ) || ! empty( $video_ids ) ) {

							$video_url_size = empty( $video_urls ) ? sizeof( $video_ids ) : sizeof( $video_urls );

							$videos = array();


							for ( $i = 0; $i < $video_url_size; $i ++ ) {
								{
									$videos[ $i ] = array(
										'id'   => $video_ids[ $i ],
										'url'  => $video_urls[ $i ],
										'host' => $video_hosts[ $i ]
									);
								}
							}
							$gallery_setting['columns'] = isset( $_POST[ $tab . '_columns_number_video' ] ) ? $_POST[ $tab . '_columns_number_video' ] : 1;
							//  $gallery_setting['height']  =   isset( $_POST[$tab.'_height_gallery_video'] ) ?    $_POST[$tab.'_height_gallery_video'] :   1;

							$args = array(
								'settings' => $gallery_setting,
								'video'    => $videos
							);

							yit_save_prop( $product, $tab . '_custom_video', $args );
						} else {
							yit_delete_prop( $product, $tab . '_custom_video' );
						}
						break;

					case 'shortcode' :

						$shortcode = isset( $_POST[ $tab . '_shortcode' ] ) ? $_POST[ $tab . '_shortcode' ] : "";

						if ( ! empty( $shortcode ) ) {
							yit_save_prop( $product, $tab . '_custom_shortcode', $shortcode );
						} else {
							yit_delete_prop( $product, $tab . '_custom_shortcode' );
						}
						break;

					case 'contact'  :

						$fields['name']['show']    = isset( $_POST[ $tab . '_name_show' ] ) ? $_POST[ $tab . '_name_show' ] : '';
						$fields['webaddr']['show'] = isset( $_POST[ $tab . '_webaddr_show' ] ) ? $_POST[ $tab . '_webaddr_show' ] : '';
						$fields['subj']['show']    = isset( $_POST[ $tab . '_subj_show' ] ) ? $_POST[ $tab . '_subj_show' ] : '';
						$fields['name']['req']     = isset( $_POST[ $tab . '_name_req' ] ) ? $_POST[ $tab . '_name_req' ] : '';
						$fields['webaddr']['req']  = isset( $_POST[ $tab . '_webaddr_req' ] ) ? $_POST[ $tab . '_webaddr_req' ] : '';

						$fields['subj']['req'] = isset( $_POST[ $tab . '_subj_req' ] ) ? $_POST[ $tab . '_subj_req' ] : '';

						if ( empty( $fields['name']['show'] ) ) {
							unset( $fields['name'] );
						}

						if ( empty( $fields['webaddr']['show'] ) ) {
							unset( $fields['webaddr'] );
						}

						if ( empty( $fields['subj']['show'] ) ) {
							unset( $fields['subj'] );
						}


						yit_save_prop( $product, $tab . '_custom_form', $fields );

						break;

					default :

						if ( isset( $_POST[ $tab . '_default_editor' ] ) ) {
							$content = wp_unslash( $_POST[ $tab . '_default_editor' ] );
							yit_save_prop( $product, $tab . '_default_editor', $content );
						}
				}




			}

			$this->save_product_wc_tabs_metabox( $post->ID, $post );
		}

		/**
		 * save product default tabs meta
		 * @author YITHEMES
		 * @since 1.1.0
		 *
		 * @param $post_id
		 * @param $post
		 */
		public function save_product_wc_tabs_metabox( $post_id, $post ) {

			$tabs    = ywtm_get_default_tab( $post_id );
			$product = wc_get_product( $post_id );

			foreach ( $tabs as $key => $tab ) {

				$is_hide_key  = '_ywtm_hide_' . $key;
				$is_hide_val  = isset( $_REQUEST[ 'ywtm_hide_' . $key ] ) ? 'yes' : 'no';
				$is_over_key  = '_ywtm_override_' . $key;
				$is_over_val  = isset( $_REQUEST[ 'ywtm_override_' . $key ] ) ? 'yes' : 'no';
				$priority_key = '_ywtm_priority_tab_' . $key;
				$priority_val = isset( $_REQUEST[ 'ywtm_priority_tab_' . $key ] ) ? $_REQUEST[ 'ywtm_priority_tab_' . $key ] : '1';
				$title_key    = '_ywtm_title_tab_' . $key;
				$title_val    = isset( $_REQUEST[ 'ywtm_title_tab_' . $key ] ) ? $_REQUEST[ 'ywtm_title_tab_' . $key ] : '';

				yit_save_prop( $product, $is_hide_key, $is_hide_val );
				yit_save_prop( $product, $is_over_key, $is_over_val );
				yit_save_prop( $product, $priority_key, $priority_val );
				yit_save_prop( $product, $title_key, $title_val );

				if ( $key === 'description' ) {

					$desc_key = '_ywtm_content_tab_' . $key;
					$desc_val = isset( $_REQUEST[ 'ywtm_content_tab_' . $key ] ) ? $_REQUEST[ 'ywtm_content_tab_' . $key ] : '';
					$desc_val = wp_unslash( $desc_val );

					yit_save_prop( $product, $desc_key, $desc_val );
				}


			}
		}


		/**
		 * @author YITHEMES
		 * @since 1.0.0
		 * @return Yith_Product_Tab
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self( $_REQUEST );
			}

			return self::$instance;
		}
	}
}
/**
 * @author YITHEMES
 * @since 1.0.0
 * @return YWTM_Product_Tab
 */

function YWTM_Product_Tab() {
	return YWTM_Product_Tab::get_instance();
}


