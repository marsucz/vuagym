<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_search_form'))
{
    function s7upf_vc_search_form($attr)
    {
        $html = $label_sm = '';
        extract(shortcode_atts(array(
            'style'             => 'smart-search4',
            'placeholder'       => '',
            'live_search'       => 'on',
            'cats'              => '',
            'cats_hidden'       => '',
        ),$attr));
        if(!empty($cats)) $cats = str_replace(' ', '', $cats);
        ob_start();
        $search_val = get_search_query();
        if(!empty($search_val)) $search_val = $placeholder;
        switch ($style) {
            case 'search-form10':
                ?>
                <div class="<?php echo esc_attr($style)?> cat-dropdown-<?php echo esc_attr($cats_hidden)?> live-search-<?php echo esc_attr($live_search)?>">
                    <form class="smart-search-form" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
                        <input name="s" type="text" value="<?php echo esc_attr($search_val);?>" placeholder="<?php echo esc_attr($placeholder)?>">
                        <input type="hidden" name="post_type" value="product" />
                        <div class="submit-form">
                            <input type="submit" value="">
                        </div>
                        <div class="list-product-search">
                            <p class="text-center"><?php esc_html_e("Please enter key search to display results.","kuteshop")?></p>
                        </div>
                    </form>                    
                </div>
                <?php
                break;

            case 'smart-search2':
                ?>
                <div class="wrap-search1">
                    <div class="smart-search <?php echo esc_attr($style)?> cat-dropdown-<?php echo esc_attr($cats_hidden)?> live-search-<?php echo esc_attr($live_search)?>">
                        <?php if($cats_hidden !== 'off'):?>
                        <div class="select-category">
                            <a href="#" class="category-toggle-link">
                                <span><?php esc_html_e("All Categories","kuteshop")?></span>
                            </a>
                            <ul class="list-category-toggle list-unstyled">
                                <li class="active"><a href="#" data-filter=""><?php esc_html_e("Categories",'kuteshop')?></a></li>
                                <?php 
                                    if(!empty($cats)){
                                        $custom_list = explode(",",$cats);
                                        foreach ($custom_list as $key => $cat) {
                                            $term = get_term_by( 'slug',$cat, 'product_cat' );
                                            if(!empty($term) && is_object($term)){
                                                if(!empty($term) && is_object($term)){
                                                    echo '<li><a href="#" data-filter=".'.$term->slug.'">'.$term->name.'</a></li>';
                                                }
                                            }
                                        }
                                    }
                                    else{
                                        $product_cat_list = get_terms('product_cat');
                                        if(is_array($product_cat_list) && !empty($product_cat_list)){
                                            foreach ($product_cat_list as $cat) {
                                                echo '<li><a href="#" data-filter=".'.$cat->slug.'">'.$cat->name.'</a></li>';
                                            }
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        <?php endif;?>
                        <form class="smart-search-form" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
                            <input name="s" type="text" value="<?php echo esc_attr($search_val);?>" placeholder="<?php echo esc_attr($placeholder)?>">
                            <input type="hidden" name="post_type" value="product" />
                            <div class="submit-form">
                                <input type="submit" value="">
                            </div>
                            <?php if($cats_hidden !== 'off'):?>
                            <input class="cat-value" type="hidden" name="product_cat" value="" />
                            <?php endif;?>
                            <div class="list-product-search">
                                <p class="text-center"><?php esc_html_e("Please enter key search to display results.","kuteshop")?></p>
                            </div>
                        </form>
                    </div>
                </div>
                <?php
                break;

            case 'smart-search13':
                ?>
                <div class="<?php echo esc_attr($style)?> cat-dropdown-<?php echo esc_attr($cats_hidden)?> live-search-<?php echo esc_attr($live_search)?>">
                    <form class="smart-search-form border radius" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
                        <input name="s" type="text" value="<?php echo esc_attr($search_val);?>" placeholder="<?php echo esc_attr($placeholder)?>">
                        <input type="hidden" name="post_type" value="product" />
                        <div class="submit-form">
                            <input class="radius" type="submit" value="">
                        </div>
                        <div class="list-product-search">
                            <p class="text-center"><?php esc_html_e("Please enter key search to display results.","kuteshop")?></p>
                        </div>
                    </form>
                </div>
                <?php
                break;

            case 'smart-search17':
                ?>
                <div class="smart-search <?php echo esc_attr($style)?> cat-dropdown-<?php echo esc_attr($cats_hidden)?> live-search-<?php echo esc_attr($live_search)?>">
                    <?php if($cats_hidden !== 'off'):?>
                    <div class="select-category">
                        <a href="#" class="category-toggle-link">
                            <span><?php esc_html_e("All Categories","kuteshop")?></span>
                        </a>
                        <ul class="list-category-toggle list-unstyled">
                            <li class="active"><a href="#" data-filter=""><?php esc_html_e("Categories",'kuteshop')?></a></li>
                            <?php 
                                if(!empty($cats)){
                                    $custom_list = explode(",",$cats);
                                    foreach ($custom_list as $key => $cat) {
                                        $term = get_term_by( 'slug',$cat, 'product_cat' );
                                        if(!empty($term) && is_object($term)){
                                            if(!empty($term) && is_object($term)){
                                                echo '<li><a href="#" data-filter=".'.$term->slug.'">'.$term->name.'</a></li>';
                                            }
                                        }
                                    }
                                }
                                else{
                                    $product_cat_list = get_terms('product_cat');
                                    if(is_array($product_cat_list) && !empty($product_cat_list)){
                                        foreach ($product_cat_list as $cat) {
                                            echo '<li><a href="#" data-filter=".'.$cat->slug.'">'.$cat->name.'</a></li>';
                                        }
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                    <?php endif;?>
                    <form class="smart-search-form" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
                        <input name="s" type="text" value="<?php echo esc_attr($search_val);?>" placeholder="<?php echo esc_attr($placeholder)?>">
                        <input type="hidden" name="post_type" value="product" />
                        <input type="submit" value="">
                        <?php if($cats_hidden !== 'off'):?>
                        <input class="cat-value" type="hidden" name="product_cat" value="" />
                        <?php endif;?>
                        <div class="list-product-search">
                            <p class="text-center"><?php esc_html_e("Please enter key search to display results.","kuteshop")?></p>
                        </div>
                    </form>
                </div>
                <?php
                break;
            
            default:
                ?>
                <div class="smart-search <?php echo esc_attr($style)?> cat-dropdown-<?php echo esc_attr($cats_hidden)?> live-search-<?php echo esc_attr($live_search)?>">
                    <?php if($cats_hidden !== 'off'):?>
                    <div class="select-category">
                        <a href="#" class="category-toggle-link">
                            <span><?php esc_html_e("All Categories","kuteshop")?></span>
                        </a>
                        <ul class="list-category-toggle list-unstyled">
                            <li class="active"><a href="#" data-filter=""><?php esc_html_e("Categories",'kuteshop')?></a></li>
                            <?php 
                                if(!empty($cats)){
                                    $custom_list = explode(",",$cats);
                                    foreach ($custom_list as $key => $cat) {
                                        $term = get_term_by( 'slug',$cat, 'product_cat' );
                                        if(!empty($term) && is_object($term)){
                                            if(!empty($term) && is_object($term)){
                                                echo '<li><a href="#" data-filter=".'.$term->slug.'">'.$term->name.'</a></li>';
                                            }
                                        }
                                    }
                                }
                                else{
                                    $product_cat_list = get_terms('product_cat');
                                    if(is_array($product_cat_list) && !empty($product_cat_list)){
                                        foreach ($product_cat_list as $cat) {
                                            echo '<li><a href="#" data-filter=".'.$cat->slug.'">'.$cat->name.'</a></li>';
                                        }
                                    }
                                }
                            ?>
                        </ul>
                    </div>
                    <?php endif;?>
                    <form class="smart-search-form" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
                        <input name="s" type="text" value="<?php echo esc_attr($search_val);?>" placeholder="<?php echo esc_attr($placeholder)?>">
                        <input type="hidden" name="post_type" value="product" />
                        <div class="submit-form">
                            <input type="submit" value="">
                        </div>
                        <?php if($cats_hidden !== 'off'):?>
                        <input class="cat-value" type="hidden" name="product_cat" value="" />
                        <?php endif;?>
                        <div class="list-product-search">
                            <p class="text-center"><?php esc_html_e("Please enter key search to display results.","kuteshop")?></p>
                        </div>
                    </form>
                </div>
                <?php
                break;
        }        
        $html .=    ob_get_clean();
        return $html;
    }
}

stp_reg_shortcode('sv_search_form','s7upf_vc_search_form');
$check_add = '';
if(isset($_GET['return'])) $check_add = $_GET['return'];
if(empty($check_add)) add_action( 'vc_before_init_base','sv_add_admin_search',10,100 );
if ( ! function_exists( 'sv_add_admin_search' ) ) {
    function sv_add_admin_search(){
        vc_map( array(
            "name"      => esc_html__("SV Search Form", 'kuteshop'),
            "base"      => "sv_search_form",
            "icon"      => "icon-st",
            "category"  => '7Up-theme',
            "params"    => array(
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Style",'kuteshop'),
                    "param_name" => "style",
                    "value"     => array(
                        esc_html__("Default",'kuteshop')   => 'smart-search4',
                        esc_html__("Home 1",'kuteshop')   => 'smart-search1',
                        esc_html__("Home 2",'kuteshop')   => 'smart-search2',
                        esc_html__("Home 3",'kuteshop')   => 'smart-search3',
                        esc_html__("Home 6",'kuteshop')   => 'search-form6',
                        esc_html__("Home 8",'kuteshop')   => 'smart-search8',
                        esc_html__("Home 9",'kuteshop')   => 'search-form9',
                        esc_html__("Home 10",'kuteshop')   => 'search-form10',
                        esc_html__("Home 11",'kuteshop')   => 'smart-search11',
                        esc_html__("Home 12",'kuteshop')   => 'smart-search12',
                        esc_html__("Home 13",'kuteshop')   => 'smart-search13',
                        esc_html__("Home 16",'kuteshop')   => 'smart-search16',
                        esc_html__("Home 17",'kuteshop')   => 'smart-search17',
                        esc_html__("Home 18",'kuteshop')   => 'smart-search18',
                        )
                ),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Live Search",'kuteshop'),
                    "param_name" => "live_search",
                    "value"     => array(
                        esc_html__("On",'kuteshop')   => 'on',
                        esc_html__("Off",'kuteshop')   => 'off',
                        )
                ),
                array(
                    "type" => "textfield",
                    "holder" => "div",
                    "heading" => esc_html__("Place holder input",'kuteshop'),
                    "param_name" => "placeholder",
                ),
                array(
                    "type" => "dropdown",
                    "heading" => esc_html__("Hidden Categories",'kuteshop'),
                    "param_name" => "cats_hidden",
                    "value"     => array(
                        esc_html__("On",'kuteshop')   => '',
                        esc_html__("Off",'kuteshop')   => 'off',
                        )
                ),
                array(
                    'holder'     => 'div',
                    'heading'     => esc_html__( 'Product Categories', 'kuteshop' ),
                    'type'        => 'autocomplete',
                    'param_name'  => 'cats',
                    'settings' => array(
                        'multiple' => true,
                        'sortable' => true,
                        'values' => s7upf_get_product_taxonomy(),
                    ),
                    'save_always' => true,
                    'description' => esc_html__( 'List of product categories', 'kuteshop' ),
                ),
            )
        ));
    }
}
add_action( 'wp_ajax_live_search', 's7upf_live_search' );
add_action( 'wp_ajax_nopriv_live_search', 's7upf_live_search' );
if(!function_exists('s7upf_live_search')){
    function s7upf_live_search() {
        $key = $_POST['key'];
        $cat = $_POST['cat'];
        $post_type = $_POST['post_type'];
        $taxonomy = $_POST['taxonomy'];
        $trim_key = trim($key);
        $args = array(
            'post_type' => $post_type,
            's'         => $key,
            );
        if(!empty($cat)) {
            $args['tax_query'][]=array(
                'taxonomy'  =>  $taxonomy,
                'field'     =>  'slug',
                'terms'     =>  $cat
            );
        }
        $query = new WP_Query( $args );
        if( $query->have_posts() && !empty($key) && !empty($trim_key)){
            while ( $query->have_posts() ) : $query->the_post();
                global $product;
                echo    '<div class="item-search-pro">
                            <div class="search-ajax-thumb product-thumb">
                                <a href="'.esc_url(get_the_permalink()).'" class="product-thumb-link">
                                    '.get_the_post_thumbnail(get_the_ID(),array(50,50)).'
                                </a>
                            </div>
                            <div class="search-ajax-title"><h3 class="title14"><a href="'.esc_url(get_the_permalink()).'">'.get_the_title().'</a></h3></div>
                            <div class="search-ajax-price">
                                '.s7upf_get_price_html().'
                            </div>
                        </div>';
            endwhile;
        }
        else{
            echo '<p class="text-center">'.esc_html__("No any results with this keyword.","kuteshop").'</p>';
        }
        wp_reset_postdata();
    }
}