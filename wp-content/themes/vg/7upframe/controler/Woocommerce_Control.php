<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:20 AM
 */
if(class_exists("woocommerce")){
    if(function_exists('stp_reg_taxonomy')){
//    	stp_reg_taxonomy(
//    	    'product_brand',
//    	    'product',
//    	    array(
//    	        'label' => esc_html__( 'Brands', 'kuteshop' ),
//    	        'rewrite' => array( 'slug' => 'product_brand', 'kuteshop' ),
//    	        'labels'	=> array(
//    		        'all_items' => esc_html__( 'All Brands', 'kuteshop' ),
//    		        'edit_item' => esc_html__( 'Edit Brand', 'kuteshop' ),
//    		        'view_item' => esc_html__( 'View Brand', 'kuteshop' ),
//    		        'update_item' => esc_html__( 'Update Brand', 'kuteshop' ),
//    		        'add_new_item' => esc_html__( 'Add New Brand', 'kuteshop' ),
//    		        'new_item_name' => esc_html__( 'New Brand Name', 'kuteshop' ),
//    		       ),
//    	        'hierarchical' => true,
//    	        'query_var'  => true
//    	    )
//    	);
    }

    /********************************** POPUP Wishlist ************************************/

    add_action( 'wp_ajax_custom_wishlist', 's7upf_custom_wishlist' );
    add_action( 'wp_ajax_nopriv_custom_wishlist', 's7upf_custom_wishlist' );
    if(!function_exists('s7upf_custom_wishlist')){
        function s7upf_custom_wishlist() {
            $product_id = $_POST['product_id'];
            $url = YITH_WCWL()->get_wishlist_url();
            echo    '<div class="wishlist-popup">
                        <span class="popup-icon"><i class="fa fa-bullhorn" aria-hidden="true"></i></span>
                        <p class="wishlist-alert">"'.get_the_title($product_id).'" '.esc_html__("was added to wishlist","kuteshop").'</p>
                        <div class="wishlist-button">
                            <a href="#">'.esc_html__("Close","kuteshop").' (<span class="wishlist-countdown">3</span>)</a>
                            <a href="'.esc_url($url).'">'.esc_html__("View page","kuteshop").'</a>
                        </div>
                    </div>';
        }
    }

    /********************************** Shop ajax ************************************/

    add_action( 'wp_ajax_load_shop', 's7upf_load_shop' );
    add_action( 'wp_ajax_nopriv_load_shop', 's7upf_load_shop' );
    if(!function_exists('s7upf_load_shop')){
        function s7upf_load_shop() {
            $data_filter = $_POST['filter_data'];
            // var_dump($data_filter);
            extract($data_filter);
            $item_num = $column;
            $args = array(
                'post_type'         => 'product',
                'post_status'       => 'publish',
                'order'             => 'ASC',
                'posts_per_page'    => $number,
                'paged'             => $page,
            );
            if(isset($s)) if(!empty($s)){
                $args['s'] = $s;
                $args['order'] = 'DESC';
            }
            $attr_taxquery = array();
            if(is_object($category_object) && !empty($category_object)) $cats = $category_object->slug;
            if(!empty($attributes)){                
                $attr_taxquery['relation'] = 'AND';
                foreach($attributes as $attr => $term){
                    $attr_taxquery[] =  array(
                                            'taxonomy'      => 'pa_'.$attr,
                                            'terms'         => $term,
                                            'field'         => 'slug',
                                            'operator'      => 'IN'
                                        );
                }
            }
            if(!empty($cats)) {
                $attr_taxquery[]=array(
                    'taxonomy'=>'product_cat',
                    'field'=>'slug',
                    'terms'=> $cats,
                );
            }
            if ( !empty($attr_taxquery)){                
                $args['tax_query'] = $attr_taxquery;
            }
            if( isset( $price['min']) && isset( $price['max']) ){
                $min = $price['min'];
                $max = $price['max'];
                if($max != $max_price || $min != $min_price) $args['post__in'] = s7upf_filter_price($min,$max);
            }
            switch ($orderby) {
                case 'price' :
                    $args['orderby']  = "meta_value_num ID";
                    $args['order']    = 'ASC';
                    $args['meta_key'] = '_price';
                break;

                case 'price-desc' :
                    $args['orderby']  = "meta_value_num ID";
                    $args['order']    = 'DESC';
                    $args['meta_key'] = '_price';
                break;

                case 'popularity' :
                    $args['meta_key'] = 'total_sales';
                    add_filter( 'posts_clauses', array( WC()->query, 'order_by_popularity_post_clauses' ) );
                break;

                case 'rating' :
                    $args['meta_key'] = '_wc_average_rating';
                    $args['orderby'] = 'meta_value_num';
                    $args['meta_query'] = WC()->query->get_meta_query();
                    $args['tax_query'][] = WC()->query->get_tax_query();
                break;

                case 'date':
                    $args['orderby'] = 'date';
                    $args['order']    = 'DESC';
                    break;
                
                default:
                    $args['orderby'] = 'menu_order title';
                    break;
            }
            $product_query = new WP_Query($args);
            $paged = ( isset($page) ) ? absint( $page ) : 1;
            $thumb_data = array(
                'size'  => $size,
                'quickview'  => $quickview,
                'quickview_pos'  => $quickview_pos,
                'quickview_style'  => $quickview_style,
                'extra_link'  => $extra_link,
                'extra_style'  => $extra_style,
                'label'  => $label,
                );
            s7upf_shop_loop_before($product_query,$orderby,$item_style,$type,$paged,$number,$column,$thumb_data,$block_style,$shop_style);
            if(empty($size)) $size = array(195,260);
            else $size = explode('x', $size);
            $count_product = 1;
            if($product_query->have_posts()) {
                while($product_query->have_posts()) {
                    $product_query->the_post();
                    global $product;
                    if($type == 'list'){  
                        echo    s7upf_product_item(
                                    'item-product-list',
                                    1,
                                    $animation_class,
                                    $data,
                                    $style_2,
                                    array(
                                        'quickview'     => array(
                                            'status'    => $quickview,
                                            'pos'       => $quickview_pos,
                                            'style'     => $quickview_style,
                                            ),
                                        'extra-link'    => array(
                                            'status'    => $extra_link,
                                            'style'     => $extra_style,
                                            )
                                        ),
                                    $size,
                                    '',
                                    '',
                                    $label
                                );
                    }
                    else{       
                        echo    s7upf_product_item(
                                    $item_style,
                                    $item_num,
                                    $animation_class,
                                    $data,
                                    $style_2,
                                    array(
                                        'quickview'     => array(
                                            'status'    => $quickview,
                                            'pos'       => $quickview_pos,
                                            'style'     => $quickview_style,
                                            ),
                                        'extra-link'    => array(
                                            'status'    => $extra_link,
                                            'style'     => $extra_style,
                                            )
                                        ),
                                    $size,
                                    '',
                                    '',
                                    $label
                                );
                    }
                }
            }
            s7upf_shop_loop_after($product_query,$paged,$shop_style);
            wp_reset_postdata();
        }
    }

    /********************************** Shop load more ************************************/

    add_action( 'wp_ajax_load_more_shop', 's7upf_load_more_shop' );
    add_action( 'wp_ajax_nopriv_load_more_shop', 's7upf_load_more_shop' );
    if(!function_exists('s7upf_load_more_shop')){
        function s7upf_load_more_shop() {
            $data_filter = $_POST['filter_data'];
            $paged = $_POST['paged'];
            // var_dump($data_filter);
            extract($data_filter);
            $item_num = $column;
            $args = array(
                'post_type'         => 'product',
                'post_status'       => 'publish',
                'order'             => 'ASC',
                'posts_per_page'    => $number,
                'paged'             => $paged+1,
            );
            if(isset($_POST['s'])) if($_POST['s']){
                $args['s'] = $_POST['s'];
                $args['order'] = 'DESC';
            }
            if(isset($_POST['post_type'])) if($_POST['post_type']) $args['post_type'] = $_POST['post_type'];
            if(isset($_POST['cats']))  if($_POST['cats']) $cats = $_POST['cats'];
            $attr_taxquery = array();
            if(!empty($attributes)){                
                $attr_taxquery['relation'] = 'AND';
                $args['meta_query'][]  = array(
                    'key'           => '_visibility',
                    'value'         => array('catalog', 'visible'),
                    'compare'       => 'IN'
                );
                foreach($attributes as $attr => $term){
                    $attr_taxquery[] =  array(
                                            'taxonomy'      => 'pa_'.$attr,
                                            'terms'         => $term,
                                            'field'         => 'slug',
                                            'operator'      => 'IN'
                                        );
                }
            }
            if(!empty($cats)) {
                $attr_taxquery[]=array(
                    'taxonomy'=>'product_cat',
                    'field'=>'slug',
                    'terms'=> $cats
                );
            }
            if ( !empty($attr_taxquery)){                
                $args['tax_query'] = $attr_taxquery;
            }
            if( isset( $price['min']) && isset( $price['max']) ){
                $min = $price['min'];
                $max = $price['max'];
                if($max != $max_price || $min != $min_price) $args['post__in'] = s7upf_filter_price($min,$max);
            }
            switch ($orderby) {
                case 'price' :
                    $args['orderby']  = "meta_value_num ID";
                    $args['order']    = 'ASC';
                    $args['meta_key'] = '_price';
                break;

                case 'price-desc' :
                    $args['orderby']  = "meta_value_num ID";
                    $args['order']    = 'DESC';
                    $args['meta_key'] = '_price';
                break;

                case 'popularity' :
                    $args['meta_key'] = 'total_sales';
                    add_filter( 'posts_clauses', array( WC()->query, 'order_by_popularity_post_clauses' ) );
                break;

                case 'rating' :
                    $args['meta_key'] = '_wc_average_rating';
                    $args['orderby'] = 'meta_value_num';
                    $args['meta_query'] = WC()->query->get_meta_query();
                    $args['tax_query'][] = WC()->query->get_tax_query();

                break;

                case 'date':
                    $args['orderby'] = 'date';
                    $args['order']    = 'DESC';
                    break;
                
                default:
                    $args['orderby'] = 'menu_order title';
                    break;
            }            
            $product_query = new WP_Query($args);
            $paged = ( isset($page) ) ? absint( $page ) : 1;
            $thumb_data = array(
                'size'  => $size,
                'quickview'  => $quickview,
                'quickview_pos'  => $quickview_pos,
                'quickview_style'  => $quickview_style,
                'extra_link'  => $extra_link,
                'extra_style'  => $extra_style,
                'label'  => $label,
                );
            if(empty($size)) $size = array(195,260);
            else $size = explode('x', $size);
            $count_product = 1;
            if($product_query->have_posts()) {
                while($product_query->have_posts()) {
                    $product_query->the_post();
                    global $product;
                    if($type == 'list'){  
                        echo    s7upf_product_item(
                                    'item-product-list',
                                    1,
                                    $animation_class,
                                    $data,
                                    $style_2,
                                    array(
                                        'quickview'     => array(
                                            'status'    => $quickview,
                                            'pos'       => $quickview_pos,
                                            'style'     => $quickview_style,
                                            ),
                                        'extra-link'    => array(
                                            'status'    => $extra_link,
                                            'style'     => $extra_style,
                                            )
                                        ),
                                    $size,
                                    '',
                                    '',
                                    $label
                                );
                    }
                    else{       
                        echo    s7upf_product_item(
                                    $item_style,
                                    $item_num,
                                    $animation_class,
                                    $data,
                                    $style_2,
                                    array(
                                        'quickview'     => array(
                                            'status'    => $quickview,
                                            'pos'       => $quickview_pos,
                                            'style'     => $quickview_style,
                                            ),
                                        'extra-link'    => array(
                                            'status'    => $extra_link,
                                            'style'     => $extra_style,
                                            )
                                        ),
                                    $size,
                                    '',
                                    '',
                                    $label
                                );
                    }
                }
            }
            wp_reset_postdata();
        }
    }

    /*********************************** BEGIN ADD TO CART AJAX ****************************************/

	add_action( 'wp_ajax_add_to_cart', 's7upf_minicart_ajax' );
	add_action( 'wp_ajax_nopriv_add_to_cart', 's7upf_minicart_ajax' );
	if(!function_exists('s7upf_minicart_ajax')){
		function s7upf_minicart_ajax() {
			
			$product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
			$quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
			$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

			if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity ) ) {
				do_action( 'woocommerce_ajax_added_to_cart', $product_id );
				WC_AJAX::get_refreshed_fragments();
			} else {
				$this->json_headers();

				// If there was an error adding to the cart, redirect to the product page to show any errors
				$data = array(
					'error' => true,
					'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
					);
				echo json_encode( $data );
			}
			die();
		}
	}
	/*********************************** END ADD TO CART AJAX ****************************************/

	/********************************** REMOVE ITEM MINICART AJAX ************************************/

	add_action( 'wp_ajax_product_remove', 's7upf_product_remove' );
	add_action( 'wp_ajax_nopriv_product_remove', 's7upf_product_remove' );
	if(!function_exists('s7upf_product_remove')){
		function s7upf_product_remove() {
		    global $wpdb, $woocommerce;
		    $cart_item_key = $_POST['cart_item_key'];
		    if ( $woocommerce->cart->get_cart_item( $cart_item_key ) ) {
				$woocommerce->cart->remove_cart_item( $cart_item_key );
			}
		    exit();
		}
	}

	/********************************** HOOK ************************************/

	//remove woo breadcrumbs
    add_action( 'init','s7upf_remove_wc_breadcrumbs' );

    // Remove page title
    add_filter( 'woocommerce_show_page_title', 's7upf_remove_page_title');

	// remove action wrap main content
    remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);

    // Custom wrap main content
    add_action('woocommerce_before_main_content', 's7upf_add_before_main_content', 10);
    add_action('woocommerce_after_main_content', 's7upf_add_after_main_content', 10);
    add_action('woocommerce_before_shop_loop', 's7upf_before_shop_loop', 10);
    add_action('woocommerce_after_shop_loop', 's7upf_after_shop_loop', 10);

    remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
   	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
   	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );

   	add_filter( 'woocommerce_get_price_html', 's7upf_change_price_html', 100, 2 );

    if(!function_exists('s7upf_before_shop_loop')){
        function s7upf_before_shop_loop(){
            global $wp_query;
            $type = 'grid';
            if(isset($_GET['type'])){
                $type = $_GET['type'];
            }
            $orderby = 'menu_order title';
            if(isset($_GET['orderby'])){
                $orderby = $_GET['orderby'];
            }
            $column = s7upf_get_option('woo_shop_column',4);
            $number = s7upf_get_option('woo_shop_number',12);
            if(isset($_GET['column'])){
                $column = $_GET['column'];
            }
            if(isset($_GET['number'])){
                $number = $_GET['number'];
            }
            $item_style = s7upf_get_option('product_item_style');
            $size = s7upf_get_option('product_size_thumb');            
            $quickview = s7upf_get_option('product_quickview');
            $quickview_pos = s7upf_get_option('product_quickview_pos');
            $quickview_style = s7upf_get_option('product_quickview_style');
            $extra_link = s7upf_get_option('product_extra_link');
            $extra_style = s7upf_get_option('product_extra_style');
            $label = s7upf_get_option('product_label');
            $thumb_data = array(
                'size'  => $size,
                'quickview'  => $quickview,
                'quickview_pos'  => $quickview_pos,
                'quickview_style'  => $quickview_style,
                'extra_link'  => $extra_link,
                'extra_style'  => $extra_style,
                'label'  => $label,
                );            
            s7upf_shop_loop_before($wp_query,$orderby,$item_style,$type,false,$number,$column,$thumb_data);
        }
    }

    if(!function_exists('s7upf_after_shop_loop')){
        function s7upf_after_shop_loop(){
            global $wp_query;
            s7upf_shop_loop_after($wp_query);
        }
    }

   	if(!function_exists('s7upf_change_price_html')){
    	function s7upf_change_price_html($price, $product){
    		$price = str_replace('&ndash;', '<span class="slipt">&ndash;</span>', $price);
    		$price = '<div class="product-price">'.$price.'</div>';
            $show_mode = s7upf_check_catelog_mode();
            $hide_price = s7upf_get_option('hide_price');
            if($show_mode == 'on' && $hide_price == 'on') $price = '';
    		return $price;
    	}
    }

    function s7upf_add_before_main_content() {
        $col_class = 'shop-width-'.s7upf_get_option('woo_shop_column',4);
        global $count_product;
        $count_product = 1;        
        global $wp_query;
        $cats = '';
        if(isset($wp_query->query_vars['product_cat'])) $cats = $wp_query->query_vars['product_cat'];
        ?>
        <div id="main-content" class="shop-page <?php echo esc_attr($col_class);?>" data-cats="<?php echo esc_attr($cats);?>">
            <?php s7upf_header_image();?>
            <div class="container">
            	<?php
                $breadcrumb = s7upf_get_value_by_id('s7upf_show_breadrumb','on');
                if($breadcrumb == 'on'){
                	woocommerce_breadcrumb(array(
            			'delimiter'		=> ' ',
            			'wrap_before'	=> '<div class="bread-crumb radius">',
            			'wrap_after'	=> '</div>',
            		));
                }
        		?>
                <div class="row">
                	<?php s7upf_output_sidebar('left')?>
                	<div class="<?php echo esc_attr(s7upf_get_main_class()); ?>">
        <?php
    }

    function s7upf_add_after_main_content() {
        ?>
                	</div>
                	<?php s7upf_output_sidebar('right')?>
            	</div>
            </div>
        </div>
        <?php
    }

    function s7upf_remove_wc_breadcrumbs()
    {
        remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
    }

    function s7upf_remove_page_title() {
        return false;
    }
	/********************************* END REMOVE ITEM MINICART AJAX *********************************/

	/********************************** FANCYBOX POPUP CONTENT ************************************/

	add_action( 'wp_ajax_product_popup_content', 's7upf_product_popup_content' );
	add_action( 'wp_ajax_nopriv_product_popup_content', 's7upf_product_popup_content' );
	if(!function_exists('s7upf_product_popup_content')){
		function s7upf_product_popup_content() {
			$product_id = $_POST['product_id'];
			$query = new WP_Query( array(
				'post_type' => 'product',
				'post__in' => array($product_id)
				));
			if( $query->have_posts() ):
				echo '<div class="woocommerce single-product product-popup-content"><div class="product has-sidebar">';
				while ( $query->have_posts() ) : $query->the_post();	
					global $post,$product,$woocommerce;			
					s7upf_product_main_detai(true);
				endwhile;
				echo '</div></div>';
			endif;
			wp_reset_postdata();
		}
	}
	//Custom woo shop column
    add_filter( 'loop_shop_columns', 's7upf_woo_shop_columns', 1, 10 );
    function s7upf_woo_shop_columns( $number_columns ) {
        $col = s7upf_get_option('woo_shop_column',3);
        return $col;
    }
    add_filter( 'loop_shop_per_page', 's7upf_woo_shop_number', 20 );
    function s7upf_woo_shop_number( $number) {
        $col = s7upf_get_option('woo_shop_number',12);
        return $col;
    }
    // Image Header category Product
    add_action('product_cat_add_form_fields', 's7upf_product_cat_metabox_add', 10, 1);
    add_action('product_cat_edit_form_fields', 's7upf_product_cat_metabox_edit', 10, 1);    
    add_action('created_product_cat', 's7upf_product_save_category_metadata', 10, 1);    
    add_action('edited_product_cat', 's7upf_product_save_category_metadata', 10, 1);

    // Image Header category Post
    add_action('category_add_form_fields', 's7upf_product_cat_metabox_add', 10, 1);
    add_action('category_edit_form_fields', 's7upf_product_cat_metabox_edit', 10, 1);
    add_action('created_category', 's7upf_product_save_category_metadata', 10, 1);    
    add_action('edited_category', 's7upf_product_save_category_metadata', 10, 1);

    if(!function_exists('s7upf_product_cat_metabox_add')){ 
        function s7upf_product_cat_metabox_add($tag) { 
            ?>
            <div class="form-field">
                <label><?php esc_html_e('Category Header Image','kuteshop'); ?></label>
                <div class="wrap-metabox">
                    <div class="live-previews"></div>
                    <a class="button button-primary sv-button-remove"> <?php esc_html_e("Remove","kuteshop")?></a>
                    <a class="button button-primary sv-button-upload"><?php esc_html_e("Upload","kuteshop")?></a>
                    <input name="cat-header-image" type="hidden" class="sv-image-value" value=""></input>
                </div>
            </div>            
            <div class="form-field">
                <label><?php esc_html_e('Category Header Link','kuteshop'); ?></label>
                <input name="cat-header-link" type="text" value="" size="40">
            </div>
        <?php }
    }
    if(!function_exists('s7upf_product_cat_metabox_edit')){ 
        function s7upf_product_cat_metabox_edit($tag) { ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label><?php esc_html_e('Category Header Image','kuteshop'); ?></label>
                </th>
                <td>            
                    <div class="wrap-metabox">
                        <div class="live-previews">
                            <?php 
                                $image = get_term_meta($tag->term_id, 'cat-header-image', true);
                                echo '<img src="'.esc_url($image).'" />';
                            ?> 
                        </div>
                        <a class="button button-primary sv-button-remove"> <?php esc_html_e("Remove","kuteshop")?></a>
                        <a class="button button-primary sv-button-upload"><?php esc_html_e("Upload","kuteshop")?></a>
                        <input name="cat-header-image" type="hidden" class="sv-image-value" value="<?php echo esc_attr($image)?>"></input>
                    </div>            
                </td>
            </tr>            
            <tr class="form-field">
                <th scope="row"><label><?php esc_html_e('Category Header Link','kuteshop'); ?></label></th>
                <td><input name="cat-header-link" type="text" value="<?php echo get_term_meta($tag->term_id, 'cat-header-link', true)?>" size="40">
            </tr>
        <?php }
    }
    if(!function_exists('s7upf_product_save_category_metadata')){ 
        function s7upf_product_save_category_metadata($term_id)
        {
            if (isset($_POST['cat-header-image'])) update_term_meta( $term_id, 'cat-header-image', $_POST['cat-header-image']);
            if (isset($_POST['cat-header-link'])) update_term_meta( $term_id, 'cat-header-link', $_POST['cat-header-link']);
        }
    }
    //end
    // Image Header category Product
    add_action('product_brand_add_form_fields', 's7upf_product_brand_metabox_add', 10, 1);
    add_action('product_brand_edit_form_fields', 's7upf_product_brand_metabox_edit', 10, 1);    
    add_action('created_product_brand', 's7upf_product_save_brand_metadata', 10, 1);    
    add_action('edited_product_brand', 's7upf_product_save_brand_metadata', 10, 1);

    if(!function_exists('s7upf_product_brand_metabox_add')){ 
        function s7upf_product_brand_metabox_add($tag) { 
            ?>
            <div class="form-field">
                <label><?php esc_html_e('Brand Image','kuteshop'); ?></label>
                <div class="wrap-metabox">
                    <div class="live-previews"></div>
                    <a class="button button-primary sv-button-remove"> <?php esc_html_e("Remove","kuteshop")?></a>
                    <a class="button button-primary sv-button-upload"><?php esc_html_e("Upload","kuteshop")?></a>
                    <input name="brand-image" type="hidden" class="sv-image-value" value=""></input>
                </div>
            </div>
        <?php }
    }
    if(!function_exists('s7upf_product_brand_metabox_edit')){ 
        function s7upf_product_brand_metabox_edit($tag) { ?>
            <tr class="form-field">
                <th scope="row" valign="top">
                    <label><?php esc_html_e('Brand Image','kuteshop'); ?></label>
                </th>
                <td>            
                    <div class="wrap-metabox">
                        <div class="live-previews">
                            <?php 
                                $image = get_term_meta($tag->term_id, 'cat-header-image', true);
                                echo '<img src="'.esc_url($image).'" />';
                            ?> 
                        </div>
                        <a class="button button-primary sv-button-remove"> <?php esc_html_e("Remove","kuteshop")?></a>
                        <a class="button button-primary sv-button-upload"><?php esc_html_e("Upload","kuteshop")?></a>
                        <input name="brand-image" type="hidden" class="sv-image-value" value=""></input>
                    </div>            
                </td>
            </tr>
        <?php }
    }
    if(!function_exists('s7upf_product_save_brand_metadata')){ 
        function s7upf_product_save_brand_metadata($term_id)
        {
            if (isset($_POST['brand-image'])) update_term_meta( $term_id, 'brand-image', $_POST['brand-image']);
        }
    }
    //end

    // Catalog mode
    add_filter( 's7upf_tempalte_mini_cart', 's7upf_tempalte_mini_cart', 100, 2 );
    if(!function_exists('s7upf_tempalte_mini_cart')){
        function s7upf_tempalte_mini_cart($html){
            $show_mode = s7upf_check_catelog_mode();
            $hide_minicart = s7upf_get_option('hide_minicart');
            if($show_mode == 'on' && $hide_minicart == 'on') $html = '';
            return $html;
        }
    }
    add_filter( 'woocommerce_loop_add_to_cart_link', 's7upf_custom_add_to_cart_link' );
    if(!function_exists('s7upf_custom_add_to_cart_link')){
        function s7upf_custom_add_to_cart_link($content){
            $show_mode = s7upf_check_catelog_mode();
            if($show_mode == 'on') $content = '';
            return $content;
        }
    }
    add_action( 's7upf_template_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 30 );
    add_action( 's7upf_template_single_add_to_cart', 's7upf_filter_single_add_to_cart', 20 );
    // Catalog mode function
    if(!function_exists('s7upf_check_catelog_mode')){
        function s7upf_check_catelog_mode(){
            $catelog_mode = s7upf_get_option('woo_catelog');
            $hide_other_page = s7upf_get_option('hide_other_page');
            $hide_detail = s7upf_get_option('hide_detail');
            $hide_admin = s7upf_get_option('hide_admin');
            $hide_shop = s7upf_get_option('hide_shop');
            $hide_price = s7upf_get_option('hide_price');
            $show_mode = 'off';
            if($catelog_mode == 'on'){
                if($hide_other_page == 'on' && !is_super_admin() && !is_shop() && !is_single()) $show_mode = 'on';
                if($hide_other_page == 'on' && $hide_admin == 'on' && is_super_admin() && !is_shop() && !is_single() ) $show_mode = 'on';
                if(is_shop()) {
                    if($hide_shop == 'on' && !is_super_admin()) $show_mode = 'on';
                    if($hide_shop == 'on' && $hide_admin == 'on' && is_super_admin()) $show_mode = 'on';
                }
                if(is_single()) {
                    if($hide_detail == 'on' && !is_super_admin()) $show_mode = 'on';
                    if($hide_detail == 'on' && $hide_admin == 'on' && is_super_admin()) $show_mode = 'on';
                }
            }
            return $show_mode;
        }
    }
    if(!function_exists('s7upf_filter_single_add_to_cart')){
        function s7upf_filter_single_add_to_cart(){
            $show_mode = s7upf_check_catelog_mode();
            if($show_mode == 'on'){
                // S7upf_Assets::add_css('.product-available,.product-code{display:none;}');
                remove_action( 's7upf_template_single_add_to_cart', 'woocommerce_template_single_add_to_cart', 30);
            }
        }
    }

    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
    remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
    // Khoa remove add_filter( 'yith_woocompare_remove_compare_link_by_cat', 's7upf_remove_compare_link', 30, 2 );
    if(!function_exists('s7upf_remove_compare_link')){
        function s7upf_remove_compare_link(){
            return true;
        }
    }

    add_action( 'wp_ajax_get_coupon', 's7upf_get_coupon' );
    add_action( 'wp_ajax_nopriv_get_coupon', 's7upf_get_coupon' );
    if(!function_exists('s7upf_get_coupon')){
        function s7upf_get_coupon() {
            $coupon = s7upf_get_option('enable_coupon');
            $new_in = s7upf_get_option('new_in');
            $newuser = s7upf_is_newuser($new_in);
            $default_code = $_POST['default_code'];
            if($coupon == 'on' && $newuser){
                $coupon_code = s7upf_create_coupon($default_code);                
                echo balanceTags($coupon_code);
            }
            else esc_html_e("Sorry. You can't get conpon code.","kuteshop");
        }
    }
    
}
if(!function_exists('s7upf_create_coupon')){
    function s7upf_create_coupon($default_code = ''){
        $ip = $_SERVER['REMOTE_ADDR'];
        $ip = str_replace('.', '_', $ip);
        $coupon_code = uniqid().rand(1,9); // Code
        $amount = s7upf_get_option('coupon_amount'); // Amount
        $date = s7upf_get_option('coupon_date'); // Date
        $discount_type = s7upf_get_option('coupon_type'); // Type: fixed_cart, percent, fixed_product, percent_product
        $usage_limit = s7upf_get_option('usage_limit');                    
        $usage_limit_per_user = s7upf_get_option('usage_limit_per_user');                    
        $individual_use = s7upf_get_option('individual_use');                    
        $exclude_sale_items = s7upf_get_option('exclude_sale_items');       
        if(!$default_code){
            $coupon = array(
                'post_title' => $coupon_code,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type'     => 'shop_coupon'
            );
                                
            $new_coupon_id = wp_insert_post( $coupon );
                                
            // Add meta
            update_post_meta( $new_coupon_id, 'discount_type', $discount_type );
            update_post_meta( $new_coupon_id, 'coupon_amount', $amount );
            update_post_meta( $new_coupon_id, 'individual_use', $individual_use );
            update_post_meta( $new_coupon_id, 'exclude_sale_items', $exclude_sale_items );
            update_post_meta( $new_coupon_id, 'product_ids', '' );
            update_post_meta( $new_coupon_id, 'exclude_product_ids', '' );
            update_post_meta( $new_coupon_id, 'usage_limit', $usage_limit );
            update_post_meta( $new_coupon_id, 'usage_limit_per_user', $usage_limit_per_user );
            update_post_meta( $new_coupon_id, 'expiry_date', $date );
            update_post_meta( $new_coupon_id, 'apply_before_tax', 'yes' );
            update_post_meta( $new_coupon_id, 'free_shipping', 'no' );
        }
        else{
            $coupon_code = $default_code;
        }
        $current_user = wp_get_current_user();
        if($current_user->ID != 0) update_user_meta($current_user->ID, 'get_code', $coupon_code);
        else{
            $curent_data = get_option('ip_get_coupon');
            if(is_array($curent_data))$curent_data[] = $ip;
            else $curent_data = array();
            update_option( 'ip_get_coupon', $curent_data );
        }
        return $coupon_code;
    }
}