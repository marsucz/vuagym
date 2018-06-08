<?php

// while (1) {
//
//                $currentPage++;
//
//                $loop = new WP_Query(array('post_type' => array('product'), 'posts_per_page' => $perPage, 'paged' => $currentPage));
//                
//                if (!$loop->post_count || $loop->post_count == 0) {
//                    break;
//                }
//
//                while ($loop->have_posts()) : $loop->the_post();
//
//                    $product_id = get_the_ID();
//
//                    // add product to array but don't add the parent of product variations
//                    if ($product_id) {
//                        
//                        $product = wc_get_product($product_id);
//                        
//                        $always_show_status = get_post_meta($product_id, '_mypos_show_always', true);
//                        
//                        if ($always_show_status == 'yes') {
//                            // Luon luon hien san pham nay
//                            if ($product->get_catalog_visibility() != 'visible') {
//                                $product->set_catalog_visibility('visible');
//                                $product->save();
//                                
//                                $list_show[] = $product_id;
//                            }
//                        }
//                        
//                        if ($always_show_status != 'yes') {
//                            if ($product && $product->is_type('variable') && $product->has_child()) {
//
//                                $check_in_stock = $dbModel->check_stock_by_parent_id($product_id);
//                                $visibility_status = $product->get_catalog_visibility();
//
//                                if ($check_in_stock) {
//                                    if ($visibility_status == 'visible') {
//                                        // Con hang + Dang hien thi => Khong lam gi ca
//                                        $list_none[] = $product_id;
//                                    } else {
//                                        $product->set_catalog_visibility('visible');
//                                        $product->save();
//
//                                        $list_show[] = $product_id;
//                                    }
//                                } else {
//                                    if ($visibility_status == 'visible') {
//                                        // Con hang + Dang hien thi => Khong lam gi ca
//                                        $list_none[] = $product_id;
//                                    } else {
//                                        // An trong catalog + van hien thi trong Search
//                                        $product->set_catalog_visibility('search');
//                                        $product->save();
//
//                                        $list_hidden[] = $product_id;
//                                    }
//                                }
//
//                            } elseif ($product && $product->is_type('simple')) {
//
//                                $visibility_status = $product->get_catalog_visibility();
//
//                                if ($product->is_in_stock()) {
//                                    if ($visibility_status == 'visible') {
//                                        // Con hang + Dang hien thi => Khong lam gi ca
//                                        $list_none[] = $product_id;
//                                    } else {
//                                        $product->set_catalog_visibility('visible');
//                                        $product->save();
//
//                                        $list_show[] = $product_id;
//                                    }
//                                } else {
//                                    if ($visibility_status == 'visible') {
//                                        // Con hang + Dang hien thi => Khong lam gi ca
//                                        $list_none[] = $product_id;
//                                    } else {
//                                        // An trong catalog + van hien thi trong Search
//                                        $product->set_catalog_visibility('search');
//                                        $product->save();
//
//                                        $list_hidden[] = $product_id;
//                                    }
//                                }
//                            }
//                        }
//                    }
//
//                endwhile;
//                wp_reset_query();
//            }