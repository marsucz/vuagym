<?php

//product main detail
if(!function_exists('s7upf_product_main_detai')){
    function s7upf_product_main_detai($ajax = false){
        global $post, $product, $woocommerce;
        s7upf_set_post_view();
        $size = 'full';
        $thumb_id = array(get_post_thumbnail_id());
        $attachment_ids = $product->get_gallery_image_ids();
        $attachment_ids = array_merge($thumb_id,$attachment_ids);
		$attachment_ids = array_unique($attachment_ids);
        $ul_block = $pager_html = $ul_block2 = ''; $i = 1;
        foreach ( $attachment_ids as $attachment_id ) {
            $image_link = wp_get_attachment_url( $attachment_id );
            if ( ! $image_link )
                continue;
            $image_title    = esc_attr( get_the_title( $attachment_id ) );
            $image_caption  = esc_attr( get_post_field( 'post_excerpt', $attachment_id ) );
            $image       = wp_get_attachment_image( $attachment_id, $size, 0, $attr = array(
                'title' => $image_title,
                'alt'   => $image_title
                ) );
            if($i == 1) $active = 'active';
            else $active = '';
            $page_index = $i-1;
            $ul_block .= '<li data-image_id="'.esc_attr($attachment_id).'"><a href="#" class="'.esc_attr($active).'">'.$image.'</a></li>';
            $i++;
        }
        // Tuan Dev
        $ka_shoppe = get_post_meta($post->ID, '_ka_shoppe', true);
        $ka_shoppe_type = get_post_meta($post->ID, '_ka_shoppe_type', true);
        
        $available_data = array();
        $ka_show_general_price = false;
        if( $product->is_type( 'variable' ) ) {
            $available_data = $product->get_available_variations();        
        } else {
            $ka_tinh_trang_sp = get_post_meta($post->ID, '_ka_tinh_trang_sp', true);
        }
        if(!empty($available_data)){
            foreach ($available_data as $available) {
//                if (empty($available['price_html'])) {
//                    $ka_show_general_price = true;
//                }
                if(!empty($available['image_id']) && !in_array($available['image_id'],$attachment_ids)){
                    $attachment_ids[] = $available['image_id'];
                    if(!empty($available['image_id'])){
                        $image_title2    = esc_attr( get_the_title( $available['image_id'] ) );
                        $image2 = wp_get_attachment_image( $available['image_id'], $size, 0, $attr = array(
                        'title' => $image_title2,
                        'alt'   => $image_title2
                        ) );
                        $ul_block .= '<li data-image_id="'.esc_attr($available['image_id']).'"><a href="#">'.$image2.'</a></li>';
                        $i++;
                    }
                }
            }
        } 
//        else {
//            $ka_show_general_price = true;
//        }
        $thumb_html =   '<div class="detail-gallery">
                            <div class="mid">
                                '.get_the_post_thumbnail(get_the_ID(),'full').'
                            </div>
                            <div class="gallery-control">
                                <a href="#" class="prev"><i class="fa fa-angle-left"></i></a>
                                <div class="carousel">
                                    <ul>
                                        '.$ul_block.'
                                    </ul>
                                </div>
                                <a href="#" class="next"><i class="fa fa-angle-right"></i></a>
                            </div>
                        </div>';
        $thumb_html .=  s7upf_get_product_detail_link();
        $sku = get_post_meta(get_the_ID(),'_sku',true);
        $stock = $product->get_availability();
        $s_class = '';
        if(is_array($stock)){
            if(!empty($stock['class'])) $s_class = $stock['class'];
            if(!empty($stock['availability'])) $stock = $stock['availability'];
            else {
                if($stock['class'] == 'in-stock') $stock = esc_html__("In stock","kuteshop");
                else $stock = esc_html__("Out of stock","kuteshop");
            }
        }
		$nutrition_fact = get_post_meta(get_the_ID(),'product_thumb_hover',true);
		$tabs = apply_filters( 'woocommerce_product_tabs', array() );
		$post_id = $product->get_id();
		$product_status = "";
		$nha_san_xuat = $product->get_attribute( 'thuong-hieu');
		$han_su_dung = $product->get_attribute( 'han-su-dung');
		$ma_san_pham = $product->get_sku();
		if($nha_san_xuat != '') {$nha_san_xuat = " từ " . $nha_san_xuat;}
		
				
		if ( 'variable' == $product->get_type() && $product->has_child() ) {
			$variation_status = -1;
			$variations = $product->get_children();
			foreach ( $variations as $variation_id ) {
				$product_status = "<font color=\"orange\">Sắp có hàng</font>";
				$pre_order_variation = new YITH_Pre_Order_Product( $variation_id );
				$var = wc_get_product( $variation_id );
				if ($var->is_in_stock() && 'no' == $pre_order_variation->get_pre_order_status()) {
					$variation_status = 1;
					break;
				} else if ('yes' == $pre_order_variation->get_pre_order_status()) {
					$variation_status = 0;
				}
			}
			if($variation_status == 1){
				$product_status = "<font color=\"#079c3a\">Còn hàng</font>";
			} else if($variation_status == 0){
				$product_status = "<font color=\"orange\">Sắp có hàng</font>";
			} else {
				$product_status = "<font color=\"#D41313\">Hết hàng</font>";
			}
		} else if ( 'simple' == $product->get_type() ) {
                        $ka_show_general_price = true;
			$pre_order = new YITH_Pre_Order_Product( $post_id );
			if ( 'yes' == $pre_order->get_pre_order_status() ) {
				$product_status = "<font color=\"orange\">Sắp có hàng</font>";
			} else if ($product->is_in_stock()) {
				$product_status = "<font color=\"#079c3a\">Còn hàng</font>";
			} else {
				$product_status = "<font color=\"#D41313\">Hết hàng</font>";
			}
		}
		
        echo        '<div class="row">
                        <div class="col-md-4 col-sm-5 col-xs-12 col-md-push-8 col-sm-push-7">
							<div class="mobileShow">
							<div class="row product-header">
								<div class="col-md-5 col-sm-12 col-xs-12">
								'.$thumb_html.'
								</div>
								<div class="col-md-7 col-sm-12 col-xs-12">
									<div class="detail-info">
										<h2 class="title-detail" style="color: #202020; font-size: 30px; padding: 0 0 0 .3em!important;">'.get_the_title().'</h2>
										<a href="#reviews">'.s7upf_get_rating_html().'</a>
										<div class="row" style="margin: 15px 0px 10px 0px;">
											<span class="genuine">Đảm bảo chính hãng</span> '.$nha_san_xuat.'
										</div>
										<div class="row">
											<div class="available" style="margin-bottom: 5px; margin-top: 5px; margin-left: 15px;">
												Trạng thái: '.$product_status.'
											</div>
										</div>';
										
											if($han_su_dung != ''){
		echo									'<div class="row">
													<div class="available" style="margin-bottom: 5px; margin-top: 5px; margin-left: 15px;">
														Hạn sử dụng: <font color=#079c3a>'.$han_su_dung.'</font>
													</div>
												</div>';
											}
		echo						'</div>
									<p class="desc">'.get_the_excerpt().'</p>
								</div>
							</div></div>
							<h2 class="title14 title-side" style="background-color: #ddd; text-align: center; color: #333; font-size: 14px; font-weight: 700;">THÔNG TIN MUA HÀNG</h2>
							<div class="row product-header">
								<div class="detail-info">';
//                                                                if ($ka_show_general_price) {
                                                                    echo $product->get_price_html();
//                                                                }
                                                                if ($ka_tinh_trang_sp){
                        echo                                            '<div class="alert alert-danger" style="padding: 0px;">
                                                                                        <div style="margin: 10px 5px 5px 5px;"><p>';
                        echo                                               $ka_tinh_trang_sp;
                        echo                                            '</p></div>
                                                                    </div>';
                                                                }
			echo					'<div class="detail-extralink' . (!$ka_show_general_price ? ' ka-remove-line' : '') . '">';
										do_action('s7upf_template_single_add_to_cart');                                    
									'</div>';
									do_action( 'woocommerce_product_meta_start' );
									do_action( 'woocommerce_product_meta_end' );
									do_action( 'woocommerce_single_product_summary' );
			echo                '
                                                    </div></div>';
                        
                            if ($ka_shoppe == 'yes') {
                                echo                    '<div class="col-md-12 col-sm-12 col-xs-12" style="padding: 0">
                                                            <div style="float:left; margin-top: 14px;">
                                                                <img style="height: 35px; margin-top: -14px;" src="' . get_template_directory_uri() . '/assets/css/images/shopee.png">
                                                                <div class="fs-tooltip">
                                                                    <span style="color: green">(Freeship <i class="fa fa-exclamation-circle"></i>)</span>
                                                                    <span class="fs-tooltiptext">Nội dung hover FreeShip</span>
                                                                </div>
                                                            </div>
                                                            <div style="float:right">';
                                if ($ka_shoppe_type == 'link') {
                                    $ka_shoppe_content = get_post_meta($post->ID, '_ka_shoppe_content', true);
                                    echo '<a href="' . $ka_shoppe_content . '" target="_blank"><button class="button btn-shoppe">Tới Shopee</button></a>';
                                } else {
                                    echo '<button data-id="' . $post_id . '" class="button btn-shoppe btn-shoppe-text">Tới Shopee</button>';
                                }
                                echo    '</div>
                                                        </div>';

                            }
                            
                        echo '</div>';

							if (array_key_exists("ywtm_5779",$tabs)){
			echo        		'<h2 class="title14 title-side" style="background-color: #ddd; text-align: center; color: #333; font-size: 14px; font-weight: 700;">THÀNH PHẦN DINH DƯỠNG</h2>
									<div class="row product-header" style="padding: 0px;">
											<div style="margin: 10px 5px 5px 5px;">';
												$tab = $tabs['ywtm_5779'];
												call_user_func( $tab['callback'], 'ywtm_5779', $tab );
			echo                    		'</div>
									</div>';
							}
							
							if (array_key_exists("ywtm_5713",$tabs)){
			echo        		'<h2 class="title14 title-side" style="background-color: #ddd; text-align: center; color: #333; font-size: 14px; font-weight: 700;">HƯỚNG DẪN SỬ DỤNG</h2>
									<div class="row product-header" style="padding-bottom: 5px; padding-top: 10px;">
											<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';
												$tab = $tabs['ywtm_5713'];
												call_user_func( $tab['callback'], 'ywtm_5713', $tab );
			echo                    		'</div>
									</div>';
							}

							if (array_key_exists("additional_information",$tabs)){
			echo        		'<h2 class="title14 title-side" style="background-color: #ddd; text-align: center; color: #333; font-size: 14px; font-weight: 700;">THÔNG SỐ SẢN PHẨM</h2>
									<div class="row product-header" style="padding-bottom: 5px; padding-top: 10px;">
											<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';								
												$tab = $tabs['additional_information'];
												call_user_func( $tab['callback'], 'additional_information', $tab );
			echo                    		'</div>
									</div>';
							}
							
			echo		'</div>
						<div class="col-md-8 col-sm-7 col-xs-12 col-md-pull-4 col-sm-pull-5">
							<div class="mobileHide">
								<div class="row product-header">
									<div class="col-md-5 col-sm-12 col-xs-12">
									'.$thumb_html.'
									</div>
									<div class="col-md-7 col-sm-12 col-xs-12">
										<div class="detail-info">
											<h2 class="title-detail" style="color: #202020; font-size: 30px; padding: 0 0 0 .3em!important;">'.get_the_title().'</h2>
											<a href="#reviews">'.s7upf_get_rating_html().'</a>
											<div class="row" style="margin: 15px 0px 10px 0px;">
												<span class="genuine">Đảm bảo chính hãng</span>'.$nha_san_xuat.'
											</div>
											<div class="row">
											<div class="available" style="margin-bottom: 5px; margin-top: 5px; margin-left: 15px;">
												Trạng thái: '.$product_status.'
											</div>
										</div>';
										
											if($han_su_dung != ''){
		echo									'<div class="row">
													<div class="available" style="margin-bottom: 5px; margin-top: 5px; margin-left: 15px;">
														Hạn sử dụng: <font color=#079c3a>'.$han_su_dung.'</font>
													</div>
												</div>';
											}
		echo						'</div>
									<p class="desc">'.get_the_excerpt().'</p>
									</div>
								</div>
							</div>';
							s7upf_single_upsell_product();
							
							if (array_key_exists("description",$tabs)){
			echo        		'<h2 class="title14 title-side" style="background-color: #ddd; text-align: center; color: #333; font-size: 14px; font-weight: 700;">GIỚI THIỆU SẢN PHẨM</h2>
									<div class="row product-header" style="padding-bottom: 0px";>
											<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';	
												$tab = $tabs['description'];
												call_user_func( $tab['callback'], 'description', $tab );
			echo                    		'</div>
									</div>';
							}
												
							if (array_key_exists("ywtm_5810",$tabs)){
			echo        		'<h2 class="title14 title-side" style="background-color: #ddd; text-align: center; color: #333; font-size: 14px; font-weight: 700;">CÂU HỎI THƯỜNG GẶP</h2>
									<div class="row product-header" style="padding-bottom: 0px";>
											<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';	
												$tab = $tabs['ywtm_5810'];
												call_user_func( $tab['callback'], 'ywtm_5810', $tab );
			echo                    		'</div>
									</div><div>';
							}
							
							if (array_key_exists("reviews",$tabs)){
			echo        		'<h2 id="reviews" class="title14 title-side" style="background-color: #ddd; text-align: center; color: #333; font-size: 14px; font-weight: 700;">ĐÁNH GIÁ</h2>
									<div class="row product-header" style="padding-bottom: 0px";>
											<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';								
												$tab = $tabs['reviews'];
												call_user_func( $tab['callback'], 'reviews', $tab );
			echo                    		'</div>
									</div>';
							}
			echo				'<h2 class="title14 title-side" style="background-color: #ddd; text-align: center; color: #333; font-size: 14px; font-weight: 700;">BÌNH LUẬN</h2>
							<div class="row product-header" style="padding-bottom: 0px";>
								<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">	
									<div class="tab-panels commentfb">
										<div class="fb-comments" data-href="';the_permalink();echo'" data-width="100%" data-numposts="10"></div>
									</div>
								</div>
							</div>
                    </div>';
    }
}