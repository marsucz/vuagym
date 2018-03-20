<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:20 AM
 */
 
/******************************************Core Function******************************************/
//Get option
if(!function_exists('s7upf_get_option')){
	function s7upf_get_option($key,$default=NULL)
    {
        if(function_exists('ot_get_option'))
        {
            return ot_get_option($key,$default);
        }

        return $default;
    }
}

//Get list header page
if(!function_exists('s7upf_list_header_page'))
{
    function s7upf_list_header_page()
    {
        global $post;
        $page_list = array();
        $page_list[] = array(
            'value' => '',
            'label' => esc_html__('-- Choose One --','kuteshop')
        );
        $args= array(
        'post_type' => 'page',
        'posts_per_page' => -1, 
        );
        $query = new WP_Query($args);
        if($query->have_posts()): while ($query->have_posts()):$query->the_post();
            if (strpos($post->post_content, '[s7upf_logo') ||  strpos($post->post_content, '[sv_menu')) {
                $page_list[] = array(
                    'value' => $post->ID,
                    'label' => $post->post_title
                );
            }
            endwhile;
        endif;
        wp_reset_postdata();
        return $page_list;
    }
}

//Get list sidebar
if(!function_exists('s7upf_get_sidebar_ids'))
{
    function s7upf_get_sidebar_ids($for_optiontree=false)
    {
        global $wp_registered_sidebars;
        $r=array();
        $r[]=esc_html__('--Select--','kuteshop');
        if(!empty($wp_registered_sidebars)){
            foreach($wp_registered_sidebars as $key=>$value)
            {

                if($for_optiontree){
                    $r[]=array(
                        'value'=>$value['id'],
                        'label'=>$value['name']
                    );
                }else{
                    $r[$value['id']]=$value['name'];
                }
            }
        }
        return $r;
    }
}

//Get order list
if(!function_exists('s7upf_get_order_list'))
{
    function s7upf_get_order_list($current=false,$extra=array(),$return='array')
    {
        $default = array(
            esc_html__('None','kuteshop')               => 'none',
            esc_html__('Post ID','kuteshop')            => 'ID',
            esc_html__('Author','kuteshop')             => 'author',
            esc_html__('Post Title','kuteshop')         => 'title',
            esc_html__('Post Name','kuteshop')          => 'name',
            esc_html__('Post Date','kuteshop')          => 'date',
            esc_html__('Last Modified Date','kuteshop') => 'modified',
            esc_html__('Post Parent','kuteshop')        => 'parent',
            esc_html__('Random','kuteshop')             => 'rand',
            esc_html__('Comment Count','kuteshop')      => 'comment_count',
            esc_html__('View Post','kuteshop')          => 'post_views',
            esc_html__('Like Post','kuteshop')          => '_post_like_count',
            esc_html__('Custom Modified Date','kuteshop')=> 'time_update',            
        );

        if(!empty($extra) and is_array($extra))
        {
            $default=array_merge($default,$extra);
        }

        if($return=="array")
        {
            return $default;
        }elseif($return=='option')
        {
            $html='';
            if(!empty($default)){
                foreach($default as $key=>$value){
                    $selected=selected($key,$current,false);
                    $html.="<option {$selected} value='{$key}'>{$value}</option>";
                }
            }
            return $html;
        }
    }
}

// Get sidebar
if(!function_exists('s7upf_get_sidebar'))
{
    function s7upf_get_sidebar()
    {
        $default=array(
            'position'=>'right',
            'id'      =>'blog-sidebar'
        );

        return apply_filters('s7upf_get_sidebar',$default);
    }
}

//Favicon
if(!function_exists('s7upf_load_favicon') )
{
    function s7upf_load_favicon()
    {
        $value = s7upf_get_option('favicon');
        $favicon = (isset($value) && !empty($value))?$value:false;
        if($favicon)
            echo '<link rel="Shortcut Icon" href="' . esc_url( $favicon ) . '" type="image/x-icon" />' . "\n";
    }
}
if(!function_exists( 'wp_site_icon' ) ){
    add_action( 'wp_head','s7upf_load_favicon');
    add_action('login_head', 's7upf_load_favicon');
    add_action('admin_head', 's7upf_load_favicon');
}

//Fill css background
if(!function_exists('s7upf_fill_css_background'))
{
    function s7upf_fill_css_background($data)
    {
        $string = '';
        if(!empty($data['background-color'])) $string .= 'background-color:'.$data['background-color'].';';
        if(!empty($data['background-repeat'])) $string .= 'background-repeat:'.$data['background-repeat'].';';
        if(!empty($data['background-attachment'])) $string .= 'background-attachment:'.$data['background-attachment'].';';
        if(!empty($data['background-position'])) $string .= 'background-position:'.$data['background-position'].';';
        if(!empty($data['background-size'])) $string .= 'background-size:'.$data['background-size'].';';
        if(!empty($data['background-image'])) $string .= 'background-image:url("'.$data['background-image'].'");';
        if(!empty($string)) return S7upf_Assets::build_css($string);
        else return false;
    }
}

// Get list menu
if(!function_exists('s7upf_list_menu_name'))
{
    function s7upf_list_menu_name()
    {
        $menu_nav = wp_get_nav_menus();
        $menu_list = array('Default' => '');
        if(is_array($menu_nav) && !empty($menu_nav))
        {
            foreach($menu_nav as $item)
            { 
                if(is_object($item))
                {
                    $menu_list[$item->name] = $item->slug;
                }
            }
        }
        return $menu_list;
    }
}

//Display BreadCrumb
if(!function_exists('s7upf_display_breadcrumb'))
{
    function s7upf_display_breadcrumb()
    {
        $breadcrumb = s7upf_get_value_by_id('s7upf_show_breadrumb','on');
        if($breadcrumb == 'on'){ 
            $b_class = s7upf_fill_css_background(s7upf_get_option('s7upf_bg_breadcrumb'));
            ?>
            <div class="bread-crumb radius <?php echo esc_attr($b_class)?>">
                <?php 
                    if(function_exists('bcn_display')) bcn_display();
                    else s7upf_breadcrumb();
                ?>
            </div>
        <?php }
    }
}

//Custom BreadCrumb
if(!function_exists('s7upf_breadcrumb'))
{
    function s7upf_breadcrumb() {
        global $post;
        if (!is_home() || (is_home() && !is_front_page())) {
            echo '<a href="';
            echo esc_url(home_url('/'));
            echo '">';
            echo esc_html__('Home','kuteshop');
            echo '</a>'.' <span class="lnr lnr-chevron-right"></span> ';
            if(is_home() && !is_front_page()){
                echo '<span>'.esc_html__('Blog','kuteshop').'</span>'; 
            }
            if (is_category() || is_single()) {
                the_category(' <span class="lnr lnr-chevron-right"></span> ');
                if (is_single()) {
                    echo ' <span class="lnr lnr-chevron-right"></span><span style="color:red"> ';
                    the_title();
                    echo '</span>';
                }
            } elseif (is_page()) {
                if($post->post_parent){
                    $anc = get_post_ancestors( get_the_ID() );
                    $title = get_the_title();
                    foreach ( $anc as $ancestor ) {
                        $output = '<a href="'.esc_url(get_permalink($ancestor)).'" title="'.get_the_title($ancestor).'">'.get_the_title($ancestor).'</a> <span class="lnr lnr-chevron-right"></span><span> ';
                    }
                    echo balanceTags($output);
                    echo '<span> '.$title.'</span>';
                } else {
                    echo '<span> '.get_the_title().'</span>';
                }
            }
        }
        elseif (is_tag()) {single_tag_title();}
        elseif (is_day()) {echo"<span>".esc_html_e("Archive for ","kuteshop"); the_time(get_option( 'date_format' )); echo'</span>';}
        elseif (is_month()) {echo"<span>".esc_html_e("Archive for ","kuteshop"); echo get_the_time('F, Y'); echo'</span>';}
        elseif (is_year()) {echo"<span>".esc_html_e("Archive for ","kuteshop"); echo getthe_time('Y'); echo'</span>';}
        elseif (is_author()) {echo"<span>".esc_html_e("Author Archive ","kuteshop"); echo'</span>';}
        elseif (isset($_GET['paged']) && !empty($_GET['paged'])) {echo "<span>".esc_html_e("Blog Archives","kuteshop"); echo'</span>';}
        elseif (is_search()) {echo"<span>".esc_html_e("Search Results","kuteshop"); echo'</span>';}
    }
}

//Get page value by ID
if(!function_exists('s7upf_get_value_by_id'))
{   
    function s7upf_get_value_by_id($key)
    {
        if(!empty($key)){
            $id = get_the_ID();
            if(is_front_page() && is_home()) $id = (int)get_option( 'page_on_front' );
            if(!is_front_page() && is_home()) $id = (int)get_option( 'page_for_posts' );
            if(is_archive() || is_search()) $id = 0;
            if (class_exists('woocommerce')) {
                if(is_shop()) $id = (int)get_option('woocommerce_shop_page_id');
                if(is_cart()) $id = (int)get_option('woocommerce_cart_page_id');
                if(is_checkout()) $id = (int)get_option('woocommerce_checkout_page_id');
                if(is_account_page()) $id = (int)get_option('woocommerce_myaccount_page_id');
            }
            $value = get_post_meta($id,$key,true);
            if(empty($value)) $value = s7upf_get_option($key);
            return $value;
        }
        else return 'Missing a variable of this funtion';
    }
}

//Check woocommerce page
if (!function_exists('s7upf_is_woocommerce_page')) {
    function s7upf_is_woocommerce_page() {
        if(  function_exists ( "is_woocommerce" ) && is_woocommerce()){
                return true;
        }
        $woocommerce_keys   =   array ( "woocommerce_shop_page_id" ,
                                        "woocommerce_terms_page_id" ,
                                        "woocommerce_cart_page_id" ,
                                        "woocommerce_checkout_page_id" ,
                                        "woocommerce_pay_page_id" ,
                                        "woocommerce_thanks_page_id" ,
                                        "woocommerce_myaccount_page_id" ,
                                        "woocommerce_edit_address_page_id" ,
                                        "woocommerce_view_order_page_id" ,
                                        "woocommerce_change_password_page_id" ,
                                        "woocommerce_logout_page_id" ,
                                        "woocommerce_lost_password_page_id" ) ;
        foreach ( $woocommerce_keys as $wc_page_id ) {
                if ( get_the_ID () == get_option ( $wc_page_id , 0 ) ) {
                        return true ;
                }
        }
        return false;
    }
}

//navigation
if(!function_exists('s7upf_paging_nav'))
{
    function s7upf_paging_nav($style = '')
    {
        // Don't print empty markup if there's only one page.
        if ( $GLOBALS['wp_query']->max_num_pages < 2 ) {
            return;
        }

        $paged        = get_query_var( 'paged' ) ? intval( get_query_var( 'paged' ) ) : 1;
        $pagenum_link = html_entity_decode( get_pagenum_link() );
        $query_args   = array();
        $url_parts    = explode( '?', $pagenum_link );

        if ( isset( $url_parts[1] ) ) {
            wp_parse_str( $url_parts[1], $query_args );
        }

        $pagenum_link = remove_query_arg( array_keys( $query_args ), $pagenum_link );
        $pagenum_link = trailingslashit( $pagenum_link ) . '%_%';

        $format  = $GLOBALS['wp_rewrite']->using_index_permalinks() && ! strpos( $pagenum_link, 'index.php' ) ? 'index.php/' : '';
        $format .= $GLOBALS['wp_rewrite']->using_permalinks() ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%';

        // Set up paginated links.
        $links = paginate_links( array(
            'base'     => $pagenum_link,
            'format'   => $format,
            'total'    => $GLOBALS['wp_query']->max_num_pages,
            'current'  => $paged,
            'mid_size' => 1,
            'add_args' => array_map( 'urlencode', $query_args ),
            'prev_text' => '<i class="fa fa-caret-left" aria-hidden="true"></i>',
            'next_text' => '<i class="fa fa-caret-right" aria-hidden="true"></i>',
        ) );

        if ($links) : 
            switch ($style) {
                case 'bottom':
                    ?>
                    <div class="pagi-bar <?php echo esc_attr($style)?>">
                        <?php echo balanceTags($links); ?>
                    </div>
                    <?php
                    break;
                
                default:
                    ?>
                    <div class="sort-pagi-bar clearfix">
                        <div class="sort-paginav pull-right">
                            <div class="pagi-bar <?php echo esc_attr($style)?>">
                                <?php echo balanceTags($links); ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    break;
            }?>        
        <?php endif;
    }
}

//Set post view
if(!function_exists('s7upf_set_post_view'))
{
    function s7upf_set_post_view($post_id=false)
    {
        if(!$post_id) $post_id=get_the_ID();

        $view=(int)get_post_meta($post_id,'post_views',true);
        $view++;
        update_post_meta($post_id,'post_views',$view);
    }
}

if(!function_exists('s7upf_get_post_view'))
{
    function s7upf_get_post_view($post_id=false)
    {
        if(!$post_id) $post_id=get_the_ID();

        return (int)get_post_meta($post_id,'post_views',true);
    }
}

//remove attr embed
if(!function_exists('s7upf_remove_w3c')){
    function s7upf_remove_w3c($embed_code){
        $embed_code=str_replace('webkitallowfullscreen','',$embed_code);
        $embed_code=str_replace('mozallowfullscreen','',$embed_code);
        $embed_code=str_replace('frameborder="0"','',$embed_code);
        $embed_code=str_replace('frameborder="no"','',$embed_code);
        $embed_code=str_replace('scrolling="no"','',$embed_code);
        $embed_code=str_replace('&','&amp;',$embed_code);
        return $embed_code;
    }
}

// MetaBox
if(!function_exists('s7upf_display_metabox'))
{
    function s7upf_display_metabox($type ='') {
        switch ($type) {
            case 'blog':
                break;

            default:?>
                <ul class="post-date-comment">
                    <li><i class="fa fa-user" aria-hidden="true"></i><label><?php esc_html_e("Post by:",'kuteshop')?></label><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo get_the_author(); ?></a></li>
                    <li><i aria-hidden="true" class="fa fa-comment"></i><a href="<?php echo esc_url( get_comments_link() ); ?>"><?php echo get_comments_number(); ?> 
                    <?php 
                            if(get_comments_number()>1) esc_html_e('Comments', 'kuteshop') ;
                            else esc_html_e('Comment', 'kuteshop') ;
                        ?>
                    </a></li>
                    <?php if(is_front_page() && is_home()):?>
                    <li><i class="fa fa-tags" aria-hidden="true"></i>
                        <label><?php esc_html_e('In:', 'kuteshop');?></label>
                            <?php $cats = get_the_category_list(', ');?>
                            <?php if($cats) echo balanceTags($cats); else esc_html_e("No Category",'kuteshop');?>
                        </li>
                    <?php endif;?>
                    <li class="sv_post_tag"><i class="fa fa-tags" aria-hidden="true"></i>
                        <label><?php esc_html_e('Tags:', 'kuteshop');?></label>
                        <?php $tags = get_the_tag_list(' ',', ',' ');?>
                        <?php if($tags) echo balanceTags($tags); else esc_html_e("No Tag",'kuteshop');?>
                    </li>
                </ul>               
                <?php
                break;
        }
    ?>        
    <?php
    }
}
if(!function_exists('s7upf_get_header_default')){
    function s7upf_get_header_default(){
        ?>
        <div id="header" class="header header-default main-header">
            <div class="container">
                <div class="logo">
                    <a href="<?php echo esc_url(home_url('/'));?>" title="<?php echo esc_attr__("logo","kuteshop");?>">
                        <?php $s7upf_logo=s7upf_get_option('logo');?>
                        <?php if($s7upf_logo!=''){
                            echo '<h1 class="hidden">'.get_bloginfo('name', 'display').'</h1><img src="' . esc_url($s7upf_logo) . '" alt="logo">';
                        }   else { echo '<h1>'.get_bloginfo('name', 'display').'</h1>'; }
                        ?>
                    </a>
                </div>
                <nav class="main-nav">
                    <?php if ( has_nav_menu( 'primary' ) ) {
                        wp_nav_menu( array(
                                'theme_location'    => 'primary',
                                'container'         =>false,
                                'walker'            =>new S7upf_Walker_Nav_Menu(),
                             )
                        );
                    } ?>
                    <a href="#" class="toggle-mobile-menu"><span></span></a>
                </nav>
            </div>
        </div>
        <?php
    }
}
if(!function_exists('s7upf_get_footer_default')){
    function s7upf_get_footer_default(){
        ?>
        <div id="footer" class="default-footer footer-copyright">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 col-sm-8 col-xs-12">
                        <p class="copyright"><?php esc_html_e("Copyright &copy; by 7up. All Rights Reserved.","kuteshop")?></p>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-12">
                        <p class="designby"><?php esc_html_e("Design by:","kuteshop")?> <a href="#"><?php esc_html_e("7uptheme.com","kuteshop")?></a></p>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
if(!function_exists('s7upf_get_footer_visual')){
    function s7upf_get_footer_visual($page_id){
        ?>
        <div id="footer" class="footer-page">
            <div class="container">
                <?php echo S7upf_Template::get_vc_pagecontent($page_id);?>
            </div>
        </div>
        <?php
    }
}
if(!function_exists('s7upf_get_header_visual')){
    function s7upf_get_header_visual($page_id){
        ?>
        <div id="header" class="header-page">
            <div class="container">
                <?php echo S7upf_Template::get_vc_pagecontent($page_id);?>
            </div>
        </div>
        <?php
    }
}
if(!function_exists('s7upf_get_main_class')){
    function s7upf_get_main_class(){
        $sidebar=s7upf_get_sidebar();
        $sidebar_pos=$sidebar['position'];
        $main_class = 'col-md-12';
		// Khoa edit
        if($sidebar_pos != 'no') $main_class = 'col-md-9 col-sm-9 col-xs-12';
        return $main_class;
    }
}
if(!function_exists('s7upf_output_sidebar')){
    function s7upf_output_sidebar($position){
        $sidebar = s7upf_get_sidebar();
        $sidebar_pos = $sidebar['position'];
        if($sidebar_pos == $position) get_sidebar();
    }
}
if(!function_exists('s7upf_get_import_category')){
    function s7upf_get_import_category($taxonomy){
        $cats = get_terms($taxonomy);
        $data_json = '{';
        foreach ($cats as $key => $term) {
            $thumb_cat_id = get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true );
            $term_pa = get_term_by( 'id',$term->parent, $taxonomy );
            if(isset($term_pa->slug)) $slug_pa = $term_pa->slug;
            else $slug_pa = '';
            if($key > 0) $data_json .= ',';
            $data_json .= '"'.$term->slug.'":{"thumbnail":"'.$thumb_cat_id.'","parent":"'.$slug_pa.'"}';
        }
        $data_json .= '}';
        echo balanceTags($data_json);
    }
}
if(!function_exists('s7upf_fix_import_category')){
    function s7upf_fix_import_category($taxonomy){
        global $s7upf_config;
        $data = $s7upf_config['import_category'];
        if(!empty($data)){
            $data = json_decode($data,true);
            foreach ($data as $cat => $value) {
                $parent_id = 0;
                $term = get_term_by( 'slug',$cat, $taxonomy );
                $term_parent = get_term_by( 'slug', $value['parent'], $taxonomy );
                if(isset($term_parent->term_id)) $parent_id = $term_parent->term_id;
                if($parent_id) wp_update_term( $term->term_id, $taxonomy, array('parent'=> $parent_id) );
                if($value['thumbnail']){
                    if($taxonomy == 'product_cat')  update_woocommerce_term_meta(  $term->term_id, 'thumbnail_id', absint( $value['thumbnail'] ) );
                    else{
                        update_term_meta( $term->term_id, 'thumbnail_id', $value['thumbnail']);
                    }
                }
            }
        }
    }
}
if ( ! function_exists( 's7upf_get_google_link' ) ) {
    function s7upf_get_google_link() {
        $protocol = is_ssl() ? 'https' : 'http';
        $fonts_url = '';
        $fonts  = array(
                    'Open Sans:400,300,700'
                );
        if ( $fonts ) {
            $fonts_url = add_query_arg( array(
                'family' => urlencode( implode( '|', $fonts ) ),
            ), $protocol.'://fonts.googleapis.com/css' );
        }

        return $fonts_url;
    }
}
/***************************************END Core Function***************************************/


/***************************************Add Theme Function***************************************/
//Compare URL
if(!function_exists('s7upf_compare_url')){
    function s7upf_compare_url($id = false){
        $html = '';
        $icon = '<i aria-hidden="true" class="fa fa-refresh"></i>';
        if(class_exists('YITH_Woocompare')){
            if(!$id) $id = get_the_ID();
            $cp_link = str_replace('&', '&amp;',add_query_arg( array('action' => 'yith-woocompare-add-product','id' => $id )));
			// Khoa Anh edit
            $html = '<a title="So sánh với sản phẩm khác" href="'.esc_url($cp_link).'" class="product-compare compare compare-link" data-product_id="'.get_the_ID().'">'.$icon.'<span>'.esc_html__("Compare","kuteshop").'</span></a>';
        }
        return $html;
    }
}

if ( ! function_exists( 's7upf_addtocart_link' ) ) {
    function s7upf_addtocart_link($style = ''){
        global $product;
        switch ($style) {
            case 'home18':
                $icon = '';
                $text = $product->add_to_cart_text();
                $btn_class = 'addcart-link bg-color white radius';
                break;

            case 'home16':
                $icon = '';
                $text = $product->add_to_cart_text();
                $btn_class = 'addcart-link';
                break;

            case 'home13':
                $icon = '';
                $text = $product->add_to_cart_text();
                $btn_class = 'btn-link13 addcart-link btn-rect title14 white radius';
                break;
            
            default:
                $icon = '<i class="fa fa-shopping-basket" aria-hidden="true"></i>';
                $text = "Xem Chi Tiết";
                $btn_class = 'addcart-link';                
                break;
        }
        // Khoa Anh edit
        $button_html =  apply_filters( 'woocommerce_loop_add_to_cart_link',
            sprintf( '<a title="Xem chi tiết sản phẩm, hạn sử dụng,... và đặt mua" href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" data-quantity="%s" class="%s product_type_%s">'.$icon.'<span>%s</span></a>',
                esc_url( $product->get_permalink() ),
                esc_attr( $product->get_id() ),
                esc_attr( $product->get_sku() ),
                esc_attr( isset( $quantity ) ? $quantity : 1 ),
                ''.$btn_class,
                esc_attr( $product->get_type() ),
                esc_html( $text )
            ),
        $product );
		// End
        return $button_html;
    }
}
if ( ! function_exists( 's7upf_product_link' ) ) {
    function s7upf_product_link($style='',$el_class=''){
        $html = $html_wl = '';
        if(class_exists('YITH_WCWL_Init')) $html_wl = '<a href="'.esc_url(str_replace('&', '&amp;',add_query_arg( 'add_to_wishlist', get_the_ID() ))).'" class="add_to_wishlist wishlist-link" rel="nofollow" data-product-id="'.get_the_ID().'" data-product-title="'.esc_attr(get_the_title()).'"><i class="fa fa-heart" aria-hidden="true"></i><span>'.esc_html__("Wishlist","kuteshop").'</span></a>';
        switch ($style) {
            case 'product-extra-link5-2':
                $html .=     '<div class="product product-extra-link5 '.esc_attr($el_class).'">';
                $html .=        s7upf_addtocart_link();
                $html .=        $html_wl;
                $html .=        s7upf_compare_url();
                $html .=    '</div>';
                break;

            case 'product-extra-link5':
                $html .=     '<div class="product '.esc_attr($style).' '.esc_attr($el_class).'">';
                $html .=        $html_wl;
                $html .=        s7upf_addtocart_link();
                $html .=        s7upf_compare_url();
                $html .=    '</div>';
                break;

            case 'home10':
                $html .=     '<div class="product-extra-link4 product '.esc_attr($style).' '.esc_attr($el_class).'">';
                $html .=        s7upf_addtocart_link();
                $html .=        $html_wl;
                $html .=        s7upf_compare_url();
                $html .=    '</div>';
                break;

            case 'home3':
                $html .=     '<div class="product-extra-link3 product '.esc_attr($el_class).'">';
                $html .=        s7upf_addtocart_link();
                $html .=        $html_wl;
                $html .=        s7upf_compare_url();
                $html .=    '</div>';
                break;

            case 'shop-list':
                $html .=     '<div class="product-extra-link2 product '.esc_attr($el_class).'">';
                $html .=        s7upf_addtocart_link();
                $html .=        $html_wl;
                $html .=        s7upf_compare_url();
                $html .=    '</div>';
                break;
            case 'home6':
                $html .=     '<div class="product-extra-link product-extra-link6 product '.esc_attr($el_class).'">';
                $html .=        s7upf_addtocart_link();
                $html .=        $html_wl;
                $html .=        s7upf_compare_url();
                $html .=        '<a data-product-id="'.get_the_id().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link"><i class="fa fa-search" aria-hidden="true"></i></a>';
                $html .=    '</div>';
                break;
            
            default:
                $html .=     '<div class="product-extra-link product '.esc_attr($style).' '.esc_attr($el_class).'">';
                $html .=        s7upf_addtocart_link();
                $html .=        $html_wl;
                $html .=        s7upf_compare_url();
                $html .=    '</div>';
                break;
        }
        return $html;
    }
}
if(!function_exists('s7upf_thumb_hover_product')){
    function s7upf_thumb_hover_product($size='full',$style=''){
        $html = '';
        $img_hover = get_post_meta(get_the_ID(),'product_thumb_hover',true);
        if(!empty($img_hover)) $img_hover_html = s7upf_get_image_by_url($img_hover,$size,'second-image');
        else $img_hover_html = get_the_post_thumbnail(get_the_ID(),$size,array('class'=>'second-image'));
        switch ($style) {
            case 'only-image':
                $html .=    '<a href="'.esc_url(get_the_permalink()).'" class="product-thumb-link">
                                '.get_the_post_thumbnail(get_the_ID(),$size,array('class'=>'first-image')).'
                                '.$img_hover_html.'
                            </a>';
                break;
            
            default:
                $html .=    '<div class="product-thumb">
                                <a href="'.esc_url(get_the_permalink()).'" class="product-thumb-link">
                                    '.get_the_post_thumbnail(get_the_ID(),$size,array('class'=>'first-image')).'
                                    '.$img_hover_html.'
                                </a>
                                '.s7upf_product_link('home6').'
                            </div>';
                break;
        }        
        return $html;
    }
}

if ( ! function_exists( 's7upf_thumb_product' ) ) {
    function s7upf_thumb_product($style='',$hover=array(),$size='full',$animation='',$hover_ef = '',$label='hidden'){
        $hover_default = array(
            'quickview'     => array(
                'status'    => 'show',
                'pos'       => 'pos-top',
                'style'     => 'plus',
                ),
            'extra-link'    => array(
                'status'    => 'show',
                'style'     => '',
                )
            );
        $hover = array_merge($hover_default,$hover);
        $hover_html = '';
        global $post,$product;
        if($hover['quickview']['status'] == 'show') $hover_html .= '<a data-product-id="'.get_the_id().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link '.esc_attr($hover['quickview']['style'].' '.$hover['quickview']['pos']).'"><span>'.esc_html__("quick view","kuteshop").'</span></a>';
        if($hover['extra-link']['status'] == 'show') $hover_html .= s7upf_product_link($hover['extra-link']['style']);
        $label_html = '';
        if($label == 'show'){
            $date_pro = strtotime($post->post_date);
            $date_now = strtotime('now');
            $set_timer = s7upf_get_option( 'sv_set_time_woo', 30);
            $uppsell = ($date_now - $date_pro - $set_timer*24*60*60);
            $label_html .=  '<div class="product-label">';
            if($uppsell < 0) $label_html .=  '<span class="new-label">'.esc_html__("new","kuteshop").'</span>';
            if($product->is_on_sale()) $label_html .=  '<span class="sale-label">'.esc_html__("sale","kuteshop").'</span>';
            $label_html .=  '</div>';
        }
        $html = '';
        switch ($style) {
            case 'thumb-gallery':
                $html .=    '';
                break;

            case 'thumb-hover':
                $img_hover = get_post_meta(get_the_ID(),'product_thumb_hover',true);
                if(!empty($img_hover)) $img_hover_html = s7upf_get_image_by_url($img_hover,$size,'second-image');
                else $img_hover_html = get_the_post_thumbnail(get_the_ID(),$size,array('class'=>'second-image'));
                    $html .=    '<div class="product-thumb">
                                    '.$label_html.'
                                    <a href="'.esc_url(get_the_permalink()).'" class="product-thumb-link '.esc_attr($animation).' '.esc_attr($hover_ef).'">
                                        '.get_the_post_thumbnail(get_the_ID(),$size,array('class'=>'first-image')).'
                                        '.$img_hover_html.'
                                    </a>
                                    '.$hover_html.'
                                </div>';
                break;
            
            default:
                $html .=    '<div class="product-thumb">
                                '.$label_html.'
                                <a href="'.esc_url(get_the_permalink()).'" class="product-thumb-link '.esc_attr($animation).'">
                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                </a>
                                '.$hover_html.'
                            </div>';
                break;
        }
        return $html;
    }
}
if ( ! function_exists( 's7upf_thumb_product_khoa' ) ) {
    function s7upf_thumb_product_khoa($style='',$hover=array(),$size='full',$animation='',$hover_ef = '',$label='hidden'){
        $hover_default = array(
            'quickview'     => array(
                'status'    => 'show',
                'pos'       => 'pos-top',
                'style'     => 'plus',
                ),
            'extra-link'    => array(
                'status'    => 'show',
                'style'     => '',
                )
            );
        $hover = array_merge($hover_default,$hover);
        $hover_html = '';
        global $post,$product;
        if($hover['quickview']['status'] == 'show') $hover_html .= '<a data-product-id="'.get_the_id().'" href="'.esc_url(get_the_permalink()).'" class="product-quick-view quickview-link '.esc_attr($hover['quickview']['style'].' '.$hover['quickview']['pos']).'"><span>'.esc_html__("quick view","kuteshop").'</span></a>';
        if($hover['extra-link']['status'] == 'show') $hover_html .= s7upf_product_link($hover['extra-link']['style']);
        $label_html = '';
        if($label == 'show'){
            $date_pro = strtotime($post->post_date);
            $date_now = strtotime('now');
            $set_timer = s7upf_get_option( 'sv_set_time_woo', 30);
            $uppsell = ($date_now - $date_pro - $set_timer*24*60*60);
            $label_html .=  '<div class="product-label">';
            if($uppsell < 0) $label_html .=  '<span class="new-label">'.esc_html__("new","kuteshop").'</span>';
            if($product->is_on_sale()) $label_html .=  '<span class="sale-label">'.esc_html__("sale","kuteshop").'</span>';
            $label_html .=  '</div>';
        }

        $html .=    '<div class="product-thumb">
                                '.$label_html.'
                                <a href="'.esc_url(get_the_permalink()).'" class="product-thumb-link-khoa product-thumb-link '.esc_attr($animation).'">
                                    '.get_the_post_thumbnail(get_the_ID(),$size).'
                                </a>
                                '.$hover_html.'
                            </div>';
        return $html;
    }
}
if(!function_exists('s7upf_get_list_animation'))
{
    function s7upf_get_list_animation() {
        $list = array(
            esc_html__('None','kuteshop')                   => '',
            esc_html__('bounce','kuteshop')                 => 'bounce',
            esc_html__('flash','kuteshop')                  => 'flash',
            esc_html__('pulse','kuteshop')                  => 'pulse',
            esc_html__('rubberBand','kuteshop')             => 'rubberBand',
            esc_html__('shake','kuteshop')                  => 'shake',
            esc_html__('headShake','kuteshop')              => 'headShake',
            esc_html__('swing','kuteshop')                  => 'swing',
            esc_html__('tada','kuteshop')                   => 'tada',
            esc_html__('wobble','kuteshop')                 => 'wobble',
            esc_html__('jello','kuteshop')                  => 'jello',
            esc_html__('bounceIn','kuteshop')               => 'bounceIn',
            esc_html__('bounceInDown','kuteshop')           => 'bounceInDown',
            esc_html__('bounceInLeft','kuteshop')           => 'bounceInLeft',
            esc_html__('bounceInRight','kuteshop')          => 'bounceInRight',
            esc_html__('bounceInUp','kuteshop')             => 'bounceInUp',
            esc_html__('bounceOut','kuteshop')              => 'bounceOut',
            esc_html__('bounceOutDown','kuteshop')          => 'bounceOutDown',
            esc_html__('bounceOutLeft','kuteshop')          => 'bounceOutLeft',
            esc_html__('bounceOutRight','kuteshop')         => 'bounceOutRight',
            esc_html__('bounceOutUp','kuteshop')            => 'bounceOutUp',
            esc_html__('fadeIn','kuteshop')                 => 'fadeIn',
            esc_html__('fadeInDown','kuteshop')             => 'fadeInDown',
            esc_html__('fadeInDownBig','kuteshop')          => 'fadeInDownBig',
            esc_html__('fadeInLeft','kuteshop')             => 'fadeInLeft',
            esc_html__('fadeInLeftBig','kuteshop')          => 'fadeInLeftBig',
            esc_html__('fadeInRight','kuteshop')            => 'fadeInRight',
            esc_html__('fadeInRightBig','kuteshop')         => 'fadeInRightBig',
            esc_html__('fadeInUp','kuteshop')               => 'fadeInUp',
            esc_html__('fadeInUpBig','kuteshop')            => 'fadeInUpBig',
            esc_html__('fadeOut','kuteshop')                => 'fadeOut',
            esc_html__('fadeOutDown' ,'kuteshop')           => 'fadeOutDown',
            esc_html__('fadeOutDownBig','kuteshop')         => 'fadeOutDownBig',
            esc_html__('fadeOutLeft','kuteshop')            => 'fadeOutLeft',
            esc_html__('fadeOutLeftBig','kuteshop')         => 'fadeOutLeftBig',
            esc_html__('fadeOutRight','kuteshop')           => 'fadeOutRight',
            esc_html__('fadeOutRightBig','kuteshop')        => 'fadeOutRightBig',
            esc_html__('fadeOutUp','kuteshop')              => 'fadeOutUp',
            esc_html__('fadeOutUpBig','kuteshop')           => 'fadeOutUpBig',
            esc_html__('flipInX','kuteshop')                => 'flipInX',
            esc_html__('flipInY','kuteshop')                => 'flipInY',
            esc_html__('flipOutX','kuteshop')               => 'flipOutX',
            esc_html__('flipOutY','kuteshop')               => 'flipOutY',
            esc_html__('lightSpeedIn','kuteshop')           => 'lightSpeedIn',
            esc_html__('lightSpeedOut','kuteshop')          => 'lightSpeedOut',
            esc_html__('rotateIn','kuteshop')               => 'rotateIn',
            esc_html__('rotateInDownLeft','kuteshop')       => 'rotateInDownLeft',
            esc_html__('rotateInDownRight','kuteshop')      => 'rotateInDownRight',
            esc_html__('rotateInUpLeft','kuteshop')         => 'rotateInUpLeft',
            esc_html__('rotateInUpRight','kuteshop')        => 'rotateInUpRight',
            esc_html__('rotateOut','kuteshop')              => 'rotateOut',
            esc_html__('rotateOutDownLeft','kuteshop')      => 'rotateOutDownLeft',
            esc_html__('rotateOutDownRight','kuteshop')     => 'rotateOutDownRight',
            esc_html__('rotateOutUpLeft','kuteshop')        => 'rotateOutUpLeft',
            esc_html__('rotateOutUpRight','kuteshop')       => 'rotateOutUpRight',
            esc_html__('hinge','kuteshop')                  => 'hinge',
            esc_html__('rollIn','kuteshop')                 => 'rollIn',
            esc_html__('rollOut','kuteshop')                => 'rollOut',
            esc_html__('zoomIn','kuteshop')                 => 'zoomIn',
            esc_html__('zoomInDown','kuteshop')             => 'zoomInDown',
            esc_html__('zoomInLeft','kuteshop')             => 'zoomInLeft',
            esc_html__('zoomInRight','kuteshop')            => 'zoomInRight',
            esc_html__('zoomInUp','kuteshop')               => 'zoomInUp',
            esc_html__('zoomOut','kuteshop')                => 'zoomOut',
            esc_html__('zoomOutDown','kuteshop')            => 'zoomOutDown',
            esc_html__('zoomOutLeft','kuteshop')            => 'zoomOutLeft',
            esc_html__('zoomOutRight','kuteshop')           => 'zoomOutRight',
            esc_html__('zoomOutUp','kuteshop')              => 'zoomOutUp',
            esc_html__('slideInDown','kuteshop')            => 'slideInDown',
            esc_html__('slideInLeft','kuteshop')            => 'slideInLeft',
            esc_html__('slideInRight','kuteshop')           => 'slideInRight',
            esc_html__('slideInUp','kuteshop')              => 'slideInUp',
            esc_html__('slideOutDown','kuteshop')           => 'slideOutDown',
            esc_html__('slideOutLeft','kuteshop')           => 'slideOutLeft',
            esc_html__('slideOutRight','kuteshop')          => 'slideOutRight',
            esc_html__('slideOutUp','kuteshop')             => 'slideOutUp',
            );
        return $list;
    }
}
if(!function_exists('s7upf_get_hover_animation'))
{
    function s7upf_get_hover_animation() {
        $list = array(
            esc_html__('None','kuteshop')                       => '',
            esc_html__('Grow','kuteshop')                       => 'grow',
            esc_html__('Shrink','kuteshop')                     => 'shrink',
            esc_html__('Pulse','kuteshop')                      => 'pulse',
            esc_html__('Pulse Grow','kuteshop')                 => 'pulse-grow',
            esc_html__('Pulse Shrink','kuteshop')               => 'pulse-shrink',
            esc_html__('Push','kuteshop')                       => 'push',
            esc_html__('Pop','kuteshop')                        => 'pop',
            esc_html__('Bounce In','kuteshop')                  => 'bounce-in',
            esc_html__('Bounce Out','kuteshop')                 => 'bounce-out',
            esc_html__('Rotate','kuteshop')                     => 'rotate',
            esc_html__('Grow Rotate','kuteshop')                => 'grow-rotate',
            esc_html__('Float','kuteshop')                      => 'float',
            esc_html__('Sink','kuteshop')                       => 'sink',
            esc_html__('Bob','kuteshop')                        => 'bob',
            esc_html__('Hang','kuteshop')                       => 'hang',
            esc_html__('Skew','kuteshop')                       => 'skew',
            esc_html__('Skew Forward','kuteshop')               => 'skew-forward',
            esc_html__('Skew Backward','kuteshop')              => 'skew-backward',
            esc_html__('Wobble Horizontal','kuteshop')          => 'wobble-horizontal',
            esc_html__('Wobble Vertical','kuteshop')            => 'wobble-vertical',
            esc_html__('Wobble To Bottom Right','kuteshop')     => 'wobble-to-bottom-right',
            esc_html__('Wobble To Top Right','kuteshop')        => 'wobble-to-top-right',
            esc_html__('Wobble Top','kuteshop')                 => 'wobble-top',
            esc_html__('Wobble Bottom','kuteshop')              => 'hvr-wobble-bottom',
            esc_html__('Wobble Skew','kuteshop')                => 'hvr-wobble-skew',
            esc_html__('Buzz','kuteshop')                       => 'buzz',
            esc_html__('Buzz Out','kuteshop')                   => 'buzz-out',
            );
        return $list;
    }
}
// get list taxonomy
if(!function_exists('s7upf_list_taxonomy'))
{
    function s7upf_list_taxonomy($taxonomy,$show_all = true)
    {
        if($show_all) $list = array('--Select--' => '');
        else $list = array();
        if(!isset($taxonomy) || empty($taxonomy)) $taxonomy = 'category';
        $tags = get_terms($taxonomy);
        if(is_array($tags) && !empty($tags)){
            foreach ($tags as $tag) {
                $list[$tag->name] = $tag->slug;
            }
        }
        return $list;
    }
}
if(!function_exists('s7upf_get_price_html')){
    function s7upf_get_price_html($style = ''){
        global $product;
        switch ($style) {
            case 'sale-style2':
                $from = $product->get_regular_price();
                $to = $product->get_price();
                $percent = $percent_html =  '';
                if($from != $to && $from > 0){
                    $percent = round(($from-$to)/$from*100);            
                    $percent_html = '<div class="sale-content"><span class="saleoff">-'.$percent.'%</span></div>';
                }
                $html =    '<div class="price-sale">
                                '.$product->get_price_html().'
                                '.$percent_html.'
                            </div>';
                break;

            case 'sale-style':
                $from = $product->get_regular_price();
                $to = $product->get_price();
                $percent = $percent_html =  '';
                if($from != $to && $from > 0){
                    $percent = round(($from-$to)/$from*100);            
                    $percent_html = '<div class="sale-content"><span class="saleoff5">-'.$percent.'%</span></div>';
                }
                $html =    '<div class="price-sale">
                                '.$product->get_price_html().'
                                '.$percent_html.'
                            </div>';
                break;

            case 'style2':
                $html =    '<div class="price-style2">'.$product->get_price_html().'</div>';
                break;
            
            default:                
                $html =    $product->get_price_html();
                break;
        }
        return $html;
    }
}
// product item list
if(!function_exists('s7upf_product_item'))
{
    function s7upf_product_item($item_style,$item_num,$animation_class,$data,$style='',$hover=array(),$size='full',$animation='',$hover_ef = '',$label='hidden')
    {
        switch ($item_style) {
            case 'item-pro-ajax':
			// Khoa Anh
                global $product;
				$post_id = $product->get_id();
				$product_status = "";
				
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
					$pre_order = new YITH_Pre_Order_Product( $post_id );
					if ( 'yes' == $pre_order->get_pre_order_status() ) {
						$product_status = "<font color=\"orange\">Sắp có hàng</font>";
					} else if ($product->is_in_stock()) {
						$product_status = "<font color=\"#079c3a\">Còn hàng</font>";
					} else {
						$product_status = "<font color=\"#D41313\">Hết hàng</font>";
					}
				}
				
                $html =     '<div class="list-col-item list-'.esc_attr($item_num.'-item '.$animation_class).'"'.$data.'>
                                <div class="item-product '.esc_attr($item_style).'">
                                    '.s7upf_thumb_product_khoa($style,$hover,$size,$animation,$hover_ef,$label).'
									<div class="product-info">
                                        <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                        '.s7upf_get_price_html().'
										'.$product_status.'
                                        '.s7upf_product_link('shop-list').'
                                    </div>
                                </div>
                            </div>';
                break;
				// End

            case 'item-product-list':
               $html = '<div class="list-col-item list-'.esc_attr($item_num.'-item '.$animation_class).'"'.$data.'>
                            <div class="item-product '.esc_attr($item_style).'">
                                <div class="row">
                                    <div class="col-md-3 col-sm-4 col-xs-12">
                                        <div class="item-pro-color">
                                            '.s7upf_thumb_product($style,$hover,$size,$animation,$hover_ef,$label).'
                                            <div class="list-color">
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-9 col-sm-8 col-xs-12">
                                        <div class="product-info">
                                            <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                            '.s7upf_get_price_html().'
                                            <p class="desc">'.get_the_excerpt().'</p>
                                            '.s7upf_get_rating_html().'
                                            '.s7upf_product_link('shop-list').'                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>';
                break;

            case 'item-pro-color':
			// Khoa Anh
                global $product;
				$post_id = $product->get_id();
				$product_status = "";
				
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
					$pre_order = new YITH_Pre_Order_Product( $post_id );
					if ( 'yes' == $pre_order->get_pre_order_status() ) {
						$product_status = "<font color=\"orange\">Sắp có hàng</font>";
					} else if ($product->is_in_stock()) {
						$product_status = "<font color=\"#079c3a\">Còn hàng</font>";
					} else {
						$product_status = "<font color=\"#D41313\">Hết hàng</font>";
					}
				}
				
				$html = '<div class="list-col-item list-'.esc_attr($item_num.'-item '.$animation_class).'"'.$data.'>
                        <div class="item-product '.esc_attr($item_style).'">
                            '.s7upf_thumb_product($style,$hover,$size,$animation,$hover_ef,$label).'
                            <div class="product-info">
                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                '.s7upf_get_price_html().'
								'.$product_status.'
                                '.s7upf_product_link().'
                            </div>
                        </div>
                    </div>';
                break;
			// End

            case 'item-pro-color-stock':
                global $product;
                $html = '<div class="list-col-item list-'.esc_attr($item_num.'-item '.$animation_class).'"'.$data.'>
                            <div class="item-product '.esc_attr($item_style).'">
                                '.s7upf_thumb_product($style,$hover,$size,$animation,$hover_ef,$label).'
                                <div class="product-info">
                                    <div class="list-color">
                                        
                                    </div>
                                    <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                    '.s7upf_get_price_html().'
                                    <p class="stock-status">'.$product->get_stock_status().'</p>
                                    '.s7upf_product_link().'
                                </div>
                            </div>
                        </div>';
                break;
            
            default:
			// Khoa Anh Edit
				global $product;
				$post_id = $product->get_id();
				$product_status = "";
				
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
					$pre_order = new YITH_Pre_Order_Product( $post_id );
					if ( 'yes' == $pre_order->get_pre_order_status() ) {
						$product_status = "<font color=\"orange\">Sắp có hàng</font>";
					} else if ($product->is_in_stock()) {
						$product_status = "<font color=\"#079c3a\">Còn hàng</font>";
					} else {
						$product_status = "<font color=\"#D41313\">Hết hàng</font>";
					}
				}
				
				$html = '<div class="list-col-item list-'.esc_attr($item_num.'-item '.$animation_class).'"'.$data.'>
                        <div class="item-product '.esc_attr($item_style).'">
                            '.s7upf_thumb_product($style,$hover,$size,$animation,$hover_ef,$label).'
                            <div class="product-info">
                                <h3 class="product-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                '.s7upf_get_price_html().'
								'.$product_status.'
                            </div>
                        </div>
                    </div>';
			// End
            break;
        }
        return $html;
    }
}
// Author Box function
if(!function_exists('sv_author_box')){
    function sv_author_box(){ 
        global $post;
        $des = get_the_author_meta('description');
        if(!empty($des)){
        ?>
            <div class="post-author">
                <div class="author-avatar">
                    <a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                        <?php echo get_avatar(get_the_author_meta('email'),'150'); ?>
                    </a>
                </div>
                <div class="author-info">
                    <h3><a href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>"><?php echo get_the_author(); ?></a></h3>
                    <p class="author-article"><?php esc_html_e("AUTHOR /","kuteshop")?> <span><?php echo count_user_posts( get_the_author_meta('ID') )?></span> <?php esc_html_e("ARTICLES","kuteshop")?></p>
                    <p class="desc"><?php echo get_the_author_meta('description'); ?></p>
                </div>
            </div>
        <?php
        }
    }
}
//Relate Box
if(!function_exists('sv_single_related_post')){
    function sv_single_related_post(){        
    ?>
        <?php
            $categories = get_the_category(get_the_ID());
            $category_ids = array();
            foreach($categories as $individual_category){
                $category_ids[] = $individual_category->term_id;
            }
            $args=array(
                'category__in' => $category_ids,
                'post__not_in' => array(get_the_ID()),
                'posts_per_page'=>6,
                'meta_query' => array(array('key' => '_thumbnail_id')) 
                );                                        
            $query = new wp_query($args);
            if( $query->have_posts() ) {?>
                <div class="related-post">
                    <h2><?php esc_html_e("You Might Also Like","kuteshop")?></h2>
                    <div class="related-post-slider">
                        <?php echo '<div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="0:1,568:2,768:1,980:2" data-prev="" data-next="" data-pagination="" data-navigation="true">'?>
                            <?php
                                while ($query->have_posts()) {
                                    $query->the_post();                                
                                    echo    '<div class="post-thumb">
                                                <a href="'. esc_url(get_the_permalink()) .'" title="'.esc_attr(get_the_title()).'" class="post-thumb-link">
                                                    '.get_the_post_thumbnail(get_the_ID(),array(378,250)).'
                                                </a>
                                                <h3 class="post-title"><a href="'. esc_url(get_the_permalink()) .'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                            </div>';
                                }
                                wp_reset_postdata();
                            ?>
                        </div>
                    </div>
                </div>
        <?php
            }
    }
}
if(!function_exists('s7upf_substr')){
    function s7upf_substr($string='',$start=0,$end=1){
        $output = '';
        if(!empty($string)){
            if($end < strlen($string)){
                if($string[$end] != ' '){
                    for ($i=$end; $i < strlen($string) ; $i++) { 
                        if($string[$i] == ' ' || $string[$i] == '.' || $i == strlen($string)-1){
                            $end = $i;
                            break;
                        }
                    }
                }
            }
            $output = substr($string,$start,$end);
        }
        return $output;
    }
}
//get type url
if(!function_exists('s7upf_get_key_url')){
    function s7upf_get_key_url($key,$value){
        if(function_exists('s7upf_get_current_url')) $current_url = s7upf_get_current_url();
        else $current_url = get_the_permalink();
        if(isset($_GET[$key])){
            $current_url = str_replace('&'.$key.'='.$_GET[$key], '', $current_url);
            $current_url = str_replace('?'.$key.'='.$_GET[$key], '?', $current_url);
        }
        if(strpos($current_url,'?') > -1 ){
            $current_url .= '&amp;'.$key.'='.$value;
        }
        else {
            $current_url .= '?'.$key.'='.$value;
        }
        return $current_url;
    }
}
if(!function_exists('s7upf_get_rating_html')){
    function s7upf_get_rating_html($count = false,$style = ''){
        global $product;
        $html = '';
        $star = $product->get_average_rating();
        $review_count = $product->get_review_count();
        $width = $star / 5 * 100;
        $html .=    '<div class="product-rate '.esc_attr($style).'">
                        <div class="product-rating" style="width:'.$width.'%;"></div>';
        if($count) $html .= '<span>('.$review_count.'s)</span>';
        $html .=    '</div>';
        return $html;
    }
}
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
        $available_data = array();
        if( $product->is_type( 'variable' ) ) $available_data = $product->get_available_variations();        
        if(!empty($available_data)){
            foreach ($available_data as $available) {
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
										<h2 class="title-detail" style="color: #202020; font-size: 30px; border-left: 7px solid #059; padding: 0 0 0 .3em!important;">'.get_the_title().'</h2>
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
							<h2 class="title14 white bg-color title-side" style="background-color: #059; text-align: center;">THÔNG TIN MUA HÀNG</h2>
							<div class="row product-header">
								<div class="detail-info">
									'.tuandev_process_get_price_html($product).'';
									if (array_key_exists("ywtm_6579",$tabs)){
			echo        				'<div class="alert alert-danger" style="padding: 0px;">
											<div style="margin: 10px 5px 5px 5px;">';
												$tab = $tabs['ywtm_6579'];
												call_user_func( $tab['callback'], 'ywtm_6579', $tab );
			echo        		            '</div>
										</div>';
									}
			echo					'<div class="detail-extralink">';
										do_action('s7upf_template_single_add_to_cart');                                    
									'</div>';
									do_action( 'woocommerce_product_meta_start' );
									do_action( 'woocommerce_product_meta_end' );
									do_action( 'woocommerce_single_product_summary' );
			echo                '</div></div>
							</div>';

							if (array_key_exists("ywtm_5779",$tabs)){
			echo        		'<h2 class="title14 white bg-color title-side" style="background-color: #059; text-align: center;">THÀNH PHẦN DINH DƯỠNG</h2>
									<div class="row product-header" style="padding: 0px;">
											<div style="margin: 10px 5px 5px 5px;">';
												$tab = $tabs['ywtm_5779'];
												call_user_func( $tab['callback'], 'ywtm_5779', $tab );
			echo                    		'</div>
									</div>';
							}
							
							if (array_key_exists("ywtm_5713",$tabs)){
			echo        		'<h2 class="title14 white bg-color title-side" style="background-color: #059; text-align: center;">HƯỚNG DẪN SỬ DỤNG</h2>
									<div class="row product-header" style="padding-bottom: 5px; padding-top: 10px;">
											<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';
												$tab = $tabs['ywtm_5713'];
												call_user_func( $tab['callback'], 'ywtm_5713', $tab );
			echo                    		'</div>
									</div>';
							}

							if (array_key_exists("additional_information",$tabs)){
			echo        		'<h2 class="title14 white bg-color title-side" style="background-color: #059; text-align: center;">THÔNG SỐ SẢN PHẨM</h2>
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
											<h2 class="title-detail" style="color: #202020; font-size: 30px; border-left: 7px solid #059; padding: 0 0 0 .3em!important;">'.get_the_title().'</h2>
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
			echo        		'<h2 class="title14 white bg-color title-side" style="background-color: #059; text-align: center;">GIỚI THIỆU SẢN PHẨM</h2>
									<div class="row product-header" style="padding-bottom: 0px";>
											<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';	
												$tab = $tabs['description'];
												call_user_func( $tab['callback'], 'description', $tab );
			echo                    		'</div>
									</div>';
							}
												
							if (array_key_exists("ywtm_5810",$tabs)){
			echo        		'<h2 class="title14 white bg-color title-side" style="background-color: #059; text-align: center;">CÂU HỎI THƯỜNG GẶP</h2>
									<div class="row product-header" style="padding-bottom: 0px";>
											<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';	
												$tab = $tabs['ywtm_5810'];
												call_user_func( $tab['callback'], 'ywtm_5810', $tab );
			echo                    		'</div>
									</div><div>';
							}
							
							if (array_key_exists("reviews",$tabs)){
			echo        		'<h2 id="reviews" class="title14 white bg-color title-side" style="background-color: #059; text-align: center;">ĐÁNH GIÁ</h2>
									<div class="row product-header" style="padding-bottom: 0px";>
											<div class="hoz-tab-content clearfix" style="padding-top: 0px; padding-bottom: 0px;">';								
												$tab = $tabs['reviews'];
												call_user_func( $tab['callback'], 'reviews', $tab );
			echo                    		'</div>
									</div>';
							}
			echo				'<h2 class="title14 white bg-color title-side" style="background-color: #059; text-align: center;">BÌNH LUẬN</h2>
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
if(!function_exists('s7upf_check_sidebar')){
    function s7upf_check_sidebar(){
        $sidebar = s7upf_get_sidebar();
        if($sidebar['position'] == 'no') return false;
        else return true;
    }
}
// Mini cart
if(!function_exists('s7upf_mini_cart')){
    function s7upf_mini_cart($echo = false){
        $html = '';
        if ( ! WC()->cart->is_empty() ){
            $count_item = 0; $html = '';
            $html .=    '<h2><span class="cart-item-count">0</span> '.esc_html__("ITEMS IN MY CART","kuteshop").'</h2>
                        <ul class="list-mini-cart-item list-unstyled">';                    
            foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
                $count_item++;
                $_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
                $product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
                $product_quantity = woocommerce_quantity_input( array(
                    'input_name'  => "cart[{$cart_item_key}][qty]",
                    'input_value' => $cart_item['quantity'],
                    'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
                    'min_value'   => '0'
                ), $_product, false );
                $thumb_html = '';
                if(has_post_thumbnail($product_id)) $thumb_html = $_product->get_image(array(70,93));
                $html .=    '<li class="item-info-cart" data-key="'.$cart_item_key.'">
                                <div class="mini-cart-edit">
                                    <a class="delete-mini-cart-item btn-remove" href="#"><i class="fa fa-trash-o"></i></a>
                                </div>
                                <div class="mini-cart-thumb">
                                    <a href="'.esc_url( $_product->get_permalink( $cart_item )).'">'.$thumb_html.'</a>
                                </div>
                                <div class="mini-cart-info">
                                    <h3><a href="'.esc_url( $_product->get_permalink( $cart_item )).'">'.$_product->get_title().'</a></h3>
                                    <div class="info-price">
                                        <span class="mini-cart-price">'.apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key ).'</span>
                                    </div>
                                    <div class="qty-product">
                                        <span class="qty-num">'.$cart_item['quantity'].'</span>
                                    </div>
                                </div>
                            </li>';
            }
            $html .=    '</ul><input id="count-cart-item" type="hidden" value="'.$count_item.'">';
            $html .=    '<div class="mini-cart-total">
                            <label>'.esc_html__("Subtotal","kuteshop").'</label>
                            <span class="total-price">'.WC()->cart->get_cart_total().'</span>
                        </div>
                        <div class="mini-cart-button">
                            <a href="'.esc_url(wc_get_cart_url()).'" class="mini-cart-view">'.esc_html__("View my cart ","kuteshop").'</a>
                            <a href="'.esc_url(wc_get_checkout_url()).'" class="mini-cart-checkout">'.esc_html__("Checkout","kuteshop").'</a>
                        </div>';
        }
        else $html .= '<h5 class="mini-cart-head">'.esc_html__("No Product in your cart.","kuteshop").'</h5>';
        if($echo) echo balanceTags($html);
        else return $html;
    }
}
if(!function_exists('s7upf_get_product_detail_link')){
    function s7upf_get_product_detail_link($style = ''){
        global $post;
        $html =     '<div class="detail-social">
                        <ul class="list-social-detail list-inline-block">
                            <li><a href="'.esc_url('http://pinterest.com/pin/create/button/?url='.get_the_permalink().'&amp;media='.wp_get_attachment_url(get_post_thumbnail_id())).'" class="soci-fa soc-tumblr"><i class="fa fa-pinterest" aria-hidden="true"></i></a></li>
                            <li><a href="'.esc_url('http://www.facebook.com/sharer.php?u='.get_the_permalink()).'" class="soci-fa soc-facebook"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                            <li><a href="'.esc_url('http://www.twitter.com/share?url='.get_the_permalink()).'" class="soci-fa soc-twitter"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                            <li><a href="javascript:window.print()" class="soci-fa soc-print"><i class="fa fa-print" aria-hidden="true"></i></a></li>
                            <li>
                                <div class="more-social">
                                    <a class="soci-fa add-link soc-add" href="#"><i aria-hidden="true" class="fa fa-plus"></i><span>3</span></a>
                                    <ul class="list-social-share list-none">
                                        <li><a href="'.esc_url('https://plus.google.com/share?url='.get_the_permalink()).'"><i class="fa fa-google-plus"></i><span>'.esc_html__("google","kuteshop").'</span></a></li>
                                        <li><a href="'.esc_url('http://linkedin.com/shareArticle?mini=true&amp;url='.get_the_permalink().'&amp;title='.$post->post_name).'"><i class="fa fa-linkedin"></i><span>'.esc_html__("linkedin","kuteshop").'</span></a></li>
                                        <li><a href="'.esc_url('http://pinterest.com/pin/create/button/?url='.get_the_permalink().'&amp;media='.wp_get_attachment_url(get_post_thumbnail_id())).'"><i class="fa fa-pinterest"></i><span>'.esc_html__("pinterest","kuteshop").'</span></a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>';
        return $html;
    }
}
// Khoa Anh edit
if(!function_exists('s7upf_single_upsell_product'))
{
    function s7upf_single_upsell_product($style='')
    {
        $check_show = s7upf_get_value_by_id('show_single_upsell');
        $number = s7upf_get_value_by_id('show_single_number');
        if(!$number) $number = 5;
        if($check_show == 'on' || $check_show == 'yes'){
            global $product;
            $upsells = $product->get_upsell_ids();
			if ( sizeof($upsells) == 0 ) return;
            $item = 5;
            $item_res = '0:1,320:2,480:3,980:4,1200:5';
            $animation_class = $data = $style = '';
            $item_style = s7upf_get_option('product_item_style_single');
            if(empty($item_style)) $item_style = 'item-pro-color';
            $quickview = s7upf_get_option('product_quickview');
            $quickview_pos = s7upf_get_option('product_quickview_pos');
            $quickview_style = s7upf_get_option('product_quickview_style');
            $extra_link = s7upf_get_option('product_extra_link_single');
            $extra_style = s7upf_get_option('product_extra_style_single');
            $label = s7upf_get_option('product_label');
            $size = s7upf_get_option('product_size_single_box');
            if(!empty($size)) $size = explode('x', $size);
            else $size = array(195,260);
            ?>  
            <div class="product-related border radius <?php echo esc_attr($style)?>">
                <h2 class="title18"><?php esc_html_e("Upsell Products","kuteshop")?></h2>
                <div class="product-related-slider">
                    <?php echo '<div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';?>
                    <?php
                        $meta_query = WC()->query->get_meta_query();
                        $args = array(
                            'post_type'           => 'product',
                            'ignore_sticky_posts' => 1,
                            'no_found_rows'       => 1,
                            'posts_per_page'      => $number,
                            'post__in'            => $upsells,
                            'post__not_in'        => array( $product->get_id() ),
                            'meta_query'          => $meta_query
                        );
                        $products = new WP_Query( $args );
                        if ( $products->have_posts() ) :
                            while ( $products->have_posts() ) : 
                                $products->the_post();                                  
                                global $product;
                                echo    s7upf_product_item(
                                            $item_style,
                                            1,
                                            $animation_class,
                                            $data,
                                            $style,
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
                    ?>
                    
                    <?php   endwhile;
                        endif;
                        wp_reset_postdata();
                    ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }
}
// Khoa Anh edit
if(!function_exists('s7upf_single_lastest_product'))
{
    function s7upf_single_lastest_product($style='')
    {
        $check_show = s7upf_get_value_by_id('show_single_lastest');
        $number = s7upf_get_value_by_id('show_single_number');
        if(!$number) $number = 6;
        if($check_show == 'on' || $check_show == 'yes'){
            global $product;
            // edit khoa anh
            $item = 5;
            $item_res = '0:1,320:2,480:3,980:4,1200:5';
			// end khoa anh
            $animation_class = $data = $style = '';
            $item_style = s7upf_get_option('product_item_style_single');
            if(empty($item_style)) $item_style = 'item-pro-color';
            $quickview = s7upf_get_option('product_quickview');
            $quickview_pos = s7upf_get_option('product_quickview_pos');
            $quickview_style = s7upf_get_option('product_quickview_style');
            $extra_link = s7upf_get_option('product_extra_link_single');
            $extra_style = s7upf_get_option('product_extra_style_single');
            $label = s7upf_get_option('product_label');
            $size = s7upf_get_option('product_size_single_box');
            if(!empty($size)) $size = explode('x', $size);
            else $size = array(195,260);
            ?>  
            <div class="product-related border radius <?php echo esc_attr($style)?>">
                <h2 class="title18"><?php esc_html_e("Recent Products","kuteshop")?></h2>
                <div class="product-related-slider">
                    <?php echo '<div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';?>
                        <?php
                            $args = array(
                                'post_type'           => 'product',
                                'ignore_sticky_posts' => 1,
                                'posts_per_page'      => $number,
                                'post__not_in'        => array( $product->get_id() ),
                                'orderby'             => 'date'
                            );
                            $products = new WP_Query( $args );
                            if ( $products->have_posts() ) :
                                while ( $products->have_posts() ) : 
                                    $products->the_post();                                  
                                    global $product;
                                    echo    s7upf_product_item(
                                            $item_style,
                                            1,
                                            $animation_class,
                                            $data,
                                            $style,
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
                        ?>
                        
                        <?php   endwhile;
                            endif;
                            wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }
}
// Khoa Anh edit
if(!function_exists('s7upf_single_relate_product'))
{
    function s7upf_single_relate_product($style='')
    {
        global $product;
        $check_show = s7upf_get_value_by_id('show_single_relate');
        $number = s7upf_get_value_by_id('show_single_number');
        if(!$number) $number = 6;
        $related = wc_get_related_products($product->get_id(),$number);
        if($check_show == 'on' || $check_show == 'yes'){
            // edit khoa anh
            $item = 5;
            $item_res = '0:1,320:2,480:3,980:4,1200:5';
			// end khoa anh
            $animation_class = $data = $style = '';
            $item_style = s7upf_get_option('product_item_style_single');
            if(empty($item_style)) $item_style = 'item-pro-color';
            $quickview = s7upf_get_option('product_quickview');
            $quickview_pos = s7upf_get_option('product_quickview_pos');
            $quickview_style = s7upf_get_option('product_quickview_style');
            $extra_link = s7upf_get_option('product_extra_link_single');
            $extra_style = s7upf_get_option('product_extra_style_single');
            $label = s7upf_get_option('product_label');
            $size = s7upf_get_option('product_size_single_box');
            if(!empty($size)) $size = explode('x', $size);
            else $size = array(195,260);
            ?>  
            <div class="product-related border radius <?php echo esc_attr($style)?>">
                <h2 class="title18"><?php esc_html_e("YOU MIGHT ALSO LIKE","kuteshop")?></h2>
                <div class="product-related-slider">
                    <?php echo '<div class="wrap-item smart-slider" data-item="'.esc_attr($item).'" data-speed="" data-itemres="'.esc_attr($item_res).'" data-prev="" data-next="" data-pagination="" data-navigation="true">';?>
                        <?php
                            $args = array(
                                'post_type'           => 'product',
                                'ignore_sticky_posts'  => 1,
                                'no_found_rows'        => 1,
                                'posts_per_page'       => $number,                                    
                                'orderby'              => 'ID',
                                'post__in'             => $related,
                                'post__not_in'         => array( $product->get_id() )
                            );
                            $products = new WP_Query( $args );
                            if ( $products->have_posts() ) :
                                while ( $products->have_posts() ) : 
                                    $products->the_post();                                  
                                    global $product;
                                    echo    s7upf_product_item(
                                            $item_style,
                                            1,
                                            $animation_class,
                                            $data,
                                            $style,
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
                        ?>
                        
                        <?php   endwhile;
                            endif;
                            wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        <?php
        }
    }
}
//Get all page
if(!function_exists('s7upf_list_all_page'))
{
    function s7upf_list_all_page()
    {
        global $post;
        $page_list = array(
            esc_html__('-- Choose One --','kuteshop') => '',
            );
        $args= array(
        'post_type' => 'page',
        'posts_per_page' => -1, 
        );
        $query = new WP_Query($args);
        if($query->have_posts()): while ($query->have_posts()):$query->the_post();
            $page_list[$post->post_title] = $post->ID;
            endwhile;
        endif;
        wp_reset_postdata();
        return $page_list;
    }
}
if(!function_exists('s7upf_get_label_html')){
    function s7upf_get_label_html($wrap = false){
        global $post,$product;
        $label_html = '';
        $date_pro = strtotime($post->post_date);
        $date_now = strtotime('now');
        $set_timer = s7upf_get_option( 'sv_set_time_woo', 30);
        $uppsell = ($date_now - $date_pro - $set_timer*24*60*60);
        if($wrap) $label_html .=  '<div class="product-label">';
        if($uppsell < 0) $label_html .=  '<span class="new-label">'.esc_html__("new","kuteshop").'</span>';
        if($product->is_on_sale()) $label_html .=  '<span class="sale-label">'.esc_html__("sale","kuteshop").'</span>';
        if($wrap) $label_html .=  '</div>';
        return $label_html;
    }
}
if(!function_exists('s7upf_get_deals_time')){
    function s7upf_get_deals_time($time = '0:0'){
        $curren_time = getdate();
        $time2 = explode(':', $time);
        $hours = $min = 0;
        if(isset($time2[0])) $hours = (int)$time2[0];
        if(isset($time2[1])) $min = (int)$time2[1];
        $data_h = $hours - $curren_time['hours'];
        $data_m = $min - $curren_time['minutes'];
        $time = $data_h*3600+$data_m*60+60-$curren_time['seconds'];
        return $time;
    }
}
if(!function_exists('s7upf_filter_price')){
    function s7upf_filter_price($min,$max,$filtered_posts = array()){
        global $wpdb;
        $matched_products = array( 0 );
        $matched_products_query = apply_filters( 'woocommerce_price_filter_results', $wpdb->get_results( $wpdb->prepare("
            SELECT DISTINCT ID, post_parent, post_type FROM $wpdb->posts
            INNER JOIN $wpdb->postmeta ON ID = post_id
            WHERE post_type IN ( 'product', 'product_variation' ) AND post_status = 'publish' AND meta_key = %s AND meta_value BETWEEN %d AND %d
        ", '_price', $min, $max ), OBJECT_K ), $min, $max );

        if ( $matched_products_query ) {
            foreach ( $matched_products_query as $product ) {
                if ( $product->post_type == 'product' )
                    $matched_products[] = $product->ID;
                if ( $product->post_parent > 0 && ! in_array( $product->post_parent, $matched_products ) )
                    $matched_products[] = $product->post_parent;
            }
        }

        // Filter the id's
        if ( sizeof( $filtered_posts ) == 0) {
            $filtered_posts = $matched_products;
        } else {
            $filtered_posts = array_intersect( $filtered_posts, $matched_products );
        }
        return $filtered_posts;
    }
}
//get type url
if(!function_exists('s7upf_get_filter_url')){
    function s7upf_get_filter_url($key,$value){
        if(function_exists('s7upf_get_current_url')) $current_url = s7upf_get_current_url();
        else{
            if(function_exists('woocommerce_get_page_id')) $current_url = get_permalink( woocommerce_get_page_id( 'shop' ) );
            else $current_url = get_permalink();
        }
        if(isset($_GET[$key])){
            $current_val_string = $_GET[$key];
            if($current_val_string == $value){
                $current_url = str_replace('&'.$key.'='.$_GET[$key], '', $current_url);
                $current_url = str_replace('?'.$key.'='.$_GET[$key], '?', $current_url);
            }
            $current_val_key = explode(',', $current_val_string);
            $val_encode = str_replace(',', '%2C', $current_val_string);
            if(!empty($current_val_string)){
                if(!in_array($value, $current_val_key)) $current_val_key[] = $value;
                else{
                    $pos = array_search($value, $current_val_key);
                    unset($current_val_key[$pos]);
                }            
                $new_val_string = implode('%2C', $current_val_key);
                $current_url = str_replace($key.'='.$val_encode, $key.'='.$new_val_string, $current_url);
                if (strpos($current_url, '?') == false) $current_url = str_replace('&','?',$current_url);
            }
            else $current_url = str_replace($key.'=', $key.'='.$value, $current_url);     
        }
        else{
            if(strpos($current_url,'?') > -1 ){
                $current_url .= '&amp;'.$key.'='.$value;
            }
            else {
                $current_url .= '?'.$key.'='.$value;
            }
        }
        return $current_url;
    }
}
if ( ! function_exists( 's7upf_catalog_ordering' ) ) {
    function s7upf_catalog_ordering($query,$set_orderby = '') {
        $orderby                 = isset( $_GET['orderby'] ) ? wc_clean( $_GET['orderby'] ) : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
        if(!empty($set_orderby)) $orderby = $set_orderby;
        $show_default_orderby    = 'date' === apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby' ) );
        $catalog_orderby_options = apply_filters( 'woocommerce_catalog_orderby', array(
			'date'       => __( 'Sort by newness', 'kuteshop' ),
            'menu_order' => __( 'Default sorting', 'kuteshop' ),
            'popularity' => __( 'Sort by popularity', 'kuteshop' ),
            'rating'     => __( 'Sort by average rating', 'kuteshop' ),
            'price'      => __( 'Sort by price: low to high', 'kuteshop' ),
            'price-desc' => __( 'Sort by price: high to low', 'kuteshop' )
        ) );

        if ( ! $show_default_orderby ) {
            unset( $catalog_orderby_options['date'] );
        }

        if ( 'no' === get_option( 'woocommerce_enable_review_rating' ) ) {
            unset( $catalog_orderby_options['rating'] );
        }

        wc_get_template( 'loop/orderby.php', array( 'catalog_orderby_options' => $catalog_orderby_options, 'orderby' => $orderby, 'show_default_orderby' => $show_default_orderby ) );
    }
}
if(!function_exists('s7upf_shop_loop_before')){
    function s7upf_shop_loop_before($query,$orderby = 'menu_order',$item_style = 'item-pro-color',$type = 'grid',$paged = false,$number = '',$column = '',$thumb_data=array(),$block_style = '',$shop_style = ''){
        if(!$paged) $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
        if(empty($number)) $number = 12;
        if(empty($column)) $column = 4;
        extract($thumb_data);
        if(empty($block_style)) $block_style = s7upf_get_option('shop_box_style');
        if(empty($shop_style)) $shop_style = s7upf_get_option('shop_style');
        ?>
        <div class="main-shop-load">
        <div class="<?php echo esc_attr($block_style);?> <?php echo 'product-list-item-'.esc_attr($item_style)?>">
            <div class="sort-pagi-bar clearfix">
                <div class="view-type pull-left">
                    <a data-type="grid" href="<?php echo esc_url(s7upf_get_key_url('type','grid'))?>" class="load-shop-ajax grid-view <?php if($type == 'grid') echo 'active'?>"></a>
                    <a data-type="list" href="<?php echo esc_url(s7upf_get_key_url('type','list'))?>" class="load-shop-ajax list-view <?php if($type == 'list') echo 'active'?>"></a>
                </div>
                <div class="sort-paginav pull-right">
                    <div class="sort-bar select-box">
                        <label><?php esc_html_e("Sort By:","kuteshop")?></label>
                        <?php s7upf_catalog_ordering($query,$orderby)?>
                    </div>
					<!-- Khoa remove
                    <div class="show-bar select-box">
                        <label><?php esc_html_e("Show:","kuteshop")?></label><span class="shop-show-value show-number-item"><?php echo esc_html($number)?></span>
                        <ul class="shop-dropdown-list">
                            <li><a data-number="6" class="load-shop-ajax" href="<?php echo esc_url(s7upf_get_key_url('number','6'))?>"><?php esc_html_e("6","kuteshop")?></a></li>
                            <li><a data-number="9" class="load-shop-ajax" href="<?php echo esc_url(s7upf_get_key_url('number','9'))?>"><?php esc_html_e("9","kuteshop")?></a></li>
                            <li><a data-number="12" class="load-shop-ajax" href="<?php echo esc_url(s7upf_get_key_url('number','12'))?>"><?php esc_html_e("12","kuteshop")?></a></li>
                            <li><a data-number="18" class="load-shop-ajax" href="<?php echo esc_url(s7upf_get_key_url('number','18'))?>"><?php esc_html_e("18","kuteshop")?></a></li>
                            <li><a data-number="24" class="load-shop-ajax" href="<?php echo esc_url(s7upf_get_key_url('number','24'))?>"><?php esc_html_e("24","kuteshop")?></a></li>
                            <li><a data-number="48" class="load-shop-ajax" href="<?php echo esc_url(s7upf_get_key_url('number','48'))?>"><?php esc_html_e("48","kuteshop")?></a></li>
                        </ul>
                    </div>
					-->
                    <?php if($shop_style != 'load-more'):?>
                        <div class="pagi-bar">
                            <?php
                                echo paginate_links( array(
                                    'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
                                    'format'       => '',
                                    'add_args'     => '',
                                    'current'      => max( 1, $paged ),
                                    'total'        => $query->max_num_pages,
                                    'prev_text'    => '<i class="fa fa-caret-left" aria-hidden="true"></i>',
                                    'next_text'    => '<i class="fa fa-caret-right" aria-hidden="true"></i>',
                                    'type'         => 'plain',
                                    'end_size'     => 2,
                                    'mid_size'     => 1
                                ) );
                            ?>
                        </div>
                    <?php endif;?>
                </div>
            </div>
            <div class="product-view shop-get-data <?php echo esc_attr($type)?>-pro-color" data-shop_style="<?php echo esc_attr($shop_style);?>" data-block_style="<?php echo esc_attr($block_style);?>" data-item_style="<?php echo esc_attr($item_style)?>" data-number="<?php echo esc_attr($number)?>" data-column="<?php echo esc_attr($column)?>" data-size="<?php echo esc_attr($size)?>" data-quickview="<?php echo esc_attr($quickview)?>" data-quickview_pos="<?php echo esc_attr($quickview_pos)?>" data-quickview_style="<?php echo esc_attr($quickview_style)?>" data-extra_link="<?php echo esc_attr($extra_link)?>" data-extra_style="<?php echo esc_attr($extra_style)?>" data-label="<?php echo esc_attr($label)?>">
                <div class="row">
        <?php
    }
}
if(!function_exists('s7upf_shop_loop_after')){
    function s7upf_shop_loop_after($query,$paged = false,$shop_style = ''){
        if(!$paged) $paged = ( get_query_var( 'paged' ) ) ? absint( get_query_var( 'paged' ) ) : 1;
        $max_page = $query->max_num_pages;
        if(empty($shop_style)) $shop_style = s7upf_get_option('shop_style');
        ?>
            </div>
                <?php if($shop_style != 'load-more'){?>
                    <div class="pagi-bar bottom">
                        <?php
                            echo paginate_links( array(
                                'base'         => esc_url_raw( str_replace( 999999999, '%#%', remove_query_arg( 'add-to-cart', get_pagenum_link( 999999999, false ) ) ) ),
                                'format'       => '',
                                'add_args'     => '',
                                'current'      => max( 1, $paged ),
                                'total'        => $query->max_num_pages,
                                'prev_text'    => '<i class="fa fa-caret-left" aria-hidden="true"></i>',
                                'next_text'    => '<i class="fa fa-caret-right" aria-hidden="true"></i>',
                                'type'         => 'plain',
                                'end_size'     => 2,
                                'mid_size'     => 1
                            ) );
                        ?>
                    </div>
                <?php }
                else{
                    // Khoa Edit
                    if($max_page > 1 && $max_page > $paged) echo '<div class="btn-loadmore"><a class="load-more-shop" data-maxpage="'.esc_attr($max_page).'" data-page="'.esc_attr($paged).'" href="#"><i aria-hidden="true" class="fa fa-chevron-down"></i><strong> XEM THÊM</strong></a></div>';
                }?>
            </div>
        </div>
        </div>
        <?php
    }
}
if(!function_exists('s7upf_header_image')){
    function s7upf_header_image(){        
        $header_show = s7upf_get_value_by_id('show_header_page');
        $header_images = s7upf_get_value_by_id('header_page_image');
        $header_style = s7upf_get_value_by_id('header_page_style');
        $html = '';
        if($header_show == 'on' && !is_single()){            
            if(function_exists('is_shop')) $is_shop = is_shop();
            else $is_shop = false;           
            if(is_archive() && !$is_shop){
                global $wp_query;
                $term = $wp_query->get_queried_object();
                $image = get_term_meta($term->term_id, 'cat-header-image', true);
                if(is_object($term) && !empty($image)){
                    if(isset($header_images[0])){
                        $data_cat = $header_images[0];
                        $header_images = array();
                        $header_images[0] = $data_cat;
                    }
                    else $header_images = array();
                    $title = $term->name;
                    $des = $term->description;
                    $link = get_term_meta($term->term_id, 'cat-header-link', true);
                    $header_images[0]['title'] = $title;
                    $header_images[0]['header_des'] = $des;
                    if(!empty($image)) $header_images[0]['header_image'] = $image;
                    if(!empty($link)) $header_images[0]['header_link'] = $link;
                }
            }
            if($header_style != 'full-width') $html .=    '<div class="container">';
            $html .=    '<div class="banner-page '.esc_attr($header_style).'">
                            <div class="wrap-item smart-slider" data-item="1" data-speed="" data-itemres="" data-prev="" data-next="" data-pagination="" data-navigation="true">';
            if(!empty($header_images) && is_array($header_images)){
                foreach ($header_images as $item) {
                    $html .=    '<div class="banner-shop">
                                    <div class="banner-shop-thumb">
                                        <a href="'.esc_url($item['header_link']).'"><img src="'.esc_url($item['header_image']).'" alt=""></a>
                                    </div>
                                    <div class="banner-shop-info text-center">
                                        <h2>'.esc_html($item['title']).'</h2>
                                        <p>'.esc_html($item['header_des']).'</p>
                                    </div>
                                </div>';
                }
            }
            $html .=        '</div>
                        </div>';
            if($header_style != 'full-width') $html .=    '</div>';
        }
        echo balanceTags($html);
    }
}
if(!function_exists('s7upf_product_tab_detail')){
    function s7upf_product_tab_detail(){
        $tabs = apply_filters( 'woocommerce_product_tabs', array() );
        $tab_style = s7upf_get_option('product_tab_detail');
        switch ($tab_style) {
            case 'tab-toggle':
                ?>
                <div class="tab-detal toggle-tab">
                    <?php
                        foreach ( $tabs as $key => $tab ) : 
                    ?>
                            <div class="item-toggle-tab">
                                <h2 class="toggle-tab-title title14 radius border"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></h2>
                                <div class="toggle-tab-content">
                                    <div class="content-detail-tab clearfix">
                                        <?php call_user_func( $tab['callback'], $key, $tab ); ?>
                                    </div>
                                </div>
                            </div>
                    <?php 
                        endforeach; 
                    ?>
                    <div class="item-toggle-tab">
                        <h2 class="toggle-tab-title title14 radius border"><?php esc_html_e("Tags","kuteshop")?></h2>
                        <div class="toggle-tab-content">
                            <div class="content-detail-tab">
                                <?php 
                                    global $product,$post;
                                    $tag_count = sizeof( get_the_terms( get_the_ID(), 'product_tag' ) );
                                    $tag_html = wc_get_product_tag_list( $product->get_id(), ', ', '<div class="tagged_as">' . _n( '', '', count( $product->get_tag_ids() ), 'kuteshop' ) . ' ', '</div>' );
                                    if($tag_html ) echo balanceTags($tag_html);
                                    else esc_html_e("No Tag","kuteshop");
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php 
                        $custom_tab = get_post_meta(get_the_ID(),'product_tab_data',true);
                        if(!empty($custom_tab) && is_array($custom_tab)){
                            foreach ($custom_tab as $c_tab) {
                                ?>
                                <div class="item-toggle-tab">
                                    <h2 class="toggle-tab-title title14 radius border"><?php echo esc_html($c_tab['title']);?></h2>
                                    <div class="toggle-tab-content">
                                        <div class="content-detail-tab">
                                            <?php echo apply_filters('the_content',$c_tab['tab_content']);?>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                    ?>
                </div>
                <?php
                break;
            
            default:
                ?>
                <div class="tab-tetail-wrap <?php echo esc_attr($tab_style);?>">
                    <div class="tab-detal hoz-tab-detail">
                        <div class="hoz-tab-title">
                            <ul>
                                <?php 
                                    $num=0;
                                    foreach ( $tabs as $key => $tab ) : 
                                    $num++;
                                ?>
                                        <li class="<?php if($num==1){echo 'active';}?>" role="presentation">
                                            <a href="<?php echo esc_url( '#sv-'.$key ); ?>" aria-controls="sv-<?php echo esc_attr( $key ); ?>" role="tab" data-toggle="tab"><?php echo apply_filters( 'woocommerce_product_' . $key . '_tab_title', esc_html( $tab['title'] ), $key ); ?></a>
                                        </li>
                                    
                                <?php 
                                    endforeach; 
                                ?>          
                                <!-- Khoa Anh <li role="presentation"><a href="<?php echo esc_url('#tags')?>" aria-controls="tags" role="tab" data-toggle="tab"><?php esc_html_e("Tags","kuteshop")?></a></li> -->
                                <?php 
                                    $custom_tab = get_post_meta(get_the_ID(),'product_tab_data',true);
                                    if(!empty($custom_tab) && is_array($custom_tab)){
                                        foreach ($custom_tab as $c_tab) {
                                            $tab_slug = str_replace(' ', '-', $c_tab['title']);
                                            $tab_slug = strtolower($tab_slug);
                                            echo '<li role="presentation"><a href="'.esc_url('#sv-'.$tab_slug).'" aria-controls="tags" role="tab" data-toggle="tab">'.$c_tab['title'].'</a></li>';
                                        }
                                    }
                                ?>
                            </ul>
                        </div>
                        <div class="tab-content">
                            <?php 
                                $num=0;
                                foreach ( $tabs as $key => $tab ) : 
                                $num++;
                            ?>
                                <div role="tabpanel" class="tab-pane <?php if($num==1){echo 'active';}?>" id="sv-<?php echo esc_attr( $key ); ?>">
                                    <div class="hoz-tab-content clearfix">
                                        <?php call_user_func( $tab['callback'], $key, $tab ); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>                
                            <div role="tabpanel" class="tab-pane" id="tags">
							<!-- Khoa Anh
                                <div class="hoz-tab-content">
                                    <?php 
                                        global $product,$post;
                                        $tag_count = sizeof( get_the_terms( get_the_ID(), 'product_tag' ) );
                                        $tag_html = wc_get_product_tag_list( $product->get_id(), ', ', '<div class="tagged_as">' . _n( '', '', count( $product->get_tag_ids() ), 'kuteshop' ) . ' ', '</div>' );
                                        if($tag_html ) echo balanceTags($tag_html);
                                        else esc_html_e("No Tag","kuteshop");
                                    ?>
                                </div>
                            </div>
							end -->
                            <?php 
                                if(!empty($custom_tab) && is_array($custom_tab)){
                                    foreach ($custom_tab as $c_tab) {
                                        $tab_slug = str_replace(' ', '-', $c_tab['title']);
                                        $tab_slug = strtolower($tab_slug);
                                        echo    '<div role="tabpanel" class="tab-pane" id="sv-'.$tab_slug.'">
                                                    <div class="hoz-tab-content">
                                                        '.apply_filters('the_content',$c_tab['tab_content']).'
                                                    </div>
                                                </div>';
                                    }
                                }
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                break;
        }        
    }
}
if(!function_exists('s7upf_product_share_box')){
    function s7upf_product_share_box(){
        $html =     '<div class="detail-social">
                                        <img src="images/shop/social.png" alt="">
                                    </div>

        <div class="tabs-share">
                        <div class="row">
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="single-post-tabs">
                                    <label>'.esc_html__("Tags:","kuteshop").' </label>
                                    '.$tags.'
                                </div>
                            </div>
                            <div class="col-md-6 col-sm-12 col-xs-12">
                                <div class="single-post-share">
                                    <label>'.esc_html__("Share","kuteshop").'</label>
                                    <a href="'.esc_url('http://www.facebook.com/sharer.php?u='.get_the_permalink()).'"><i class="fa fa-facebook"></i></a>
                                    <a href="'.esc_url('http://www.twitter.com/share?url='.get_the_permalink()).'"><i class="fa fa-twitter"></i></a>
                                    <a href="'.esc_url('http://linkedin.com/shareArticle?mini=true&amp;url='.get_the_permalink().'&amp;title='.$post->post_name).'"><i class="fa fa-linkedin"></i></a>
                                    <a href="'.esc_url('http://pinterest.com/pin/create/button/?url='.get_the_permalink().'&amp;media='.wp_get_attachment_url(get_post_thumbnail_id())).'"><i class="fa fa-pinterest"></i></a>
                                    <a href="'.esc_url('https://plus.google.com/share?url='.get_the_permalink()).'"><i class="fa fa-google-plus"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>';
        echo balanceTags($html);
    }
}
if(!function_exists('s7upf_get_product_taxonomy')){
    function s7upf_get_product_taxonomy($taxonomy = 'product_cat') {    
        $result = array();
        $tags = get_terms($taxonomy);
        if(is_array($tags) && !empty($tags)){
            foreach ($tags as $tag) {
                $list[$tag->name] = $tag->slug;
                $result[] = array(
                    'value' => $tag->slug,
                    'label' => $tag->name,
                );
            }
        }
        return $result;
    }
}
if(!function_exists('s7upf_scroll_top')){
    function s7upf_scroll_top(){
        $scroll_top = s7upf_get_value_by_id('show_scroll_top');
        if($scroll_top == 'on'):?>
        <a href="#" class="radius scroll-top"><i class="fa fa-angle-up" aria-hidden="true"></i></a>
        <?php endif;
    }
}
if(!function_exists('s7upf_get_icon_params')){
    function s7upf_get_icon_params($key = '',$value = ''){
        $params = array(
            array(
                'type' => 'dropdown',
                'heading' => esc_html__( 'Icon library', 'kuteshop' ),
                'value' => array(
                    esc_html__( 'Font Awesome', 'kuteshop' ) => 'fontawesome',
                    esc_html__( 'Open Iconic', 'kuteshop' ) => 'openiconic',
                    esc_html__( 'Typicons', 'kuteshop' ) => 'typicons',
                    esc_html__( 'Entypo', 'kuteshop' ) => 'entypo',
                    esc_html__( 'Linecons', 'kuteshop' ) => 'linecons',
                    esc_html__( 'Mono Social', 'kuteshop' ) => 'monosocial',
                ),
                'param_name' => 'type',
                'description' => esc_html__( 'Select icon library.', 'kuteshop' ),
                'dependency' => array(
                    'element' => $key,
                    'value' => $value,
                    )
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__( 'Icon', 'kuteshop' ),
                'param_name' => 'icon_fontawesome',
                'value' => 'fa fa-adjust', // default value to backend editor admin_label
                'settings' => array(
                    'emptyIcon' => false,
                    // default true, display an "EMPTY" icon?
                    'iconsPerPage' => 4000,
                    // default 100, how many icons per/page to display, we use (big number) to display all icons in single page
                ),
                'dependency' => array(
                    'element' => 'type',
                    'value' => 'fontawesome',
                ),
                'description' => esc_html__( 'Select icon from library.', 'kuteshop' ),
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__( 'Icon', 'kuteshop' ),
                'param_name' => 'icon_openiconic',
                'value' => 'vc-oi vc-oi-dial', // default value to backend editor admin_label
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'openiconic',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                'dependency' => array(
                    'element' => 'type',
                    'value' => 'openiconic',
                ),
                'description' => esc_html__( 'Select icon from library.', 'kuteshop' ),
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__( 'Icon', 'kuteshop' ),
                'param_name' => 'icon_typicons',
                'value' => 'typcn typcn-adjust-brightness', // default value to backend editor admin_label
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'typicons',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                'dependency' => array(
                    'element' => 'type',
                    'value' => 'typicons',
                ),
                'description' => esc_html__( 'Select icon from library.', 'kuteshop' ),
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__( 'Icon', 'kuteshop' ),
                'param_name' => 'icon_entypo',
                'value' => 'entypo-icon entypo-icon-note', // default value to backend editor admin_label
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'entypo',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                'dependency' => array(
                    'element' => 'type',
                    'value' => 'entypo',
                ),
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__( 'Icon', 'kuteshop' ),
                'param_name' => 'icon_linecons',
                'value' => 'vc_li vc_li-heart', // default value to backend editor admin_label
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'linecons',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                'dependency' => array(
                    'element' => 'type',
                    'value' => 'linecons',
                ),
                'description' => esc_html__( 'Select icon from library.', 'kuteshop' ),
            ),
            array(
                'type' => 'iconpicker',
                'heading' => esc_html__( 'Icon', 'kuteshop' ),
                'param_name' => 'icon_monosocial',
                'value' => 'vc-mono vc-mono-fivehundredpx', // default value to backend editor admin_label
                'settings' => array(
                    'emptyIcon' => false, // default true, display an "EMPTY" icon?
                    'type' => 'monosocial',
                    'iconsPerPage' => 4000, // default 100, how many icons per/page to display
                ),
                'dependency' => array(
                    'element' => 'type',
                    'value' => 'monosocial',
                ),
                'description' => esc_html__( 'Select icon from library.', 'kuteshop' ),
            ),
        );
        return $params;
    }
}
if(!function_exists('s7upf_saleoff_html')){
    function s7upf_saleoff_html($style = '',$el_class=''){
        global $product;
        $from = $product->get_regular_price();
        $to = $product->get_price();
        $percent = $percent_html =  '';
        if($from != $to && $from > 0){
            $percent = round(($from-$to)/$from*100);
            switch ($style) {
                case 'style2':
                    $percent_html = '<span class="saleoff"><b>'.$percent.'%</b>off</span>';
                    break;
                
                default:                    
                    $percent_html = '<span class="saleoff '.esc_attr($el_class).'">-'.$percent.'%</span>';
                    break;
            }
        }
        return $percent_html;
    }
}
if(!function_exists('s7upf_is_newuser')){
    function s7upf_is_newuser( $reg_days_ago = 30 ){
        if(empty($reg_days_ago)) $reg_days_ago = 30;
        $check_get_coupon = s7upf_get_option("check_get_coupon");        
        $reset_curent_data = s7upf_get_option("reset_curent_data");        
        $cu = wp_get_current_user();
        $out_date = s7upf_get_option("coupon_out_date");

        $ip = $_SERVER['REMOTE_ADDR'];
        $ip = str_replace('.', '_', $ip);
        $curent_data = get_option('ip_get_coupon');
        if(!$curent_data) update_option( 'ip_get_coupon', array() );
        if($reset_curent_data == 'on'){
            update_option( 'ip_get_coupon', array() );
            $blogusers = get_users();
            // Array of WP_User objects.
            foreach ( $blogusers as $user ) {
                update_user_meta($user->ID, 'get_code', '');
            }
            $option_name = ot_options_id();
            $curent_option = get_option($option_name);
            $curent_option = array_merge($curent_option,array('reset_curent_data'=> 'off'));
            update_option( $option_name, $curent_option);
        }
        $check_create = false;
        if($cu->ID != 0){
            $get_code = get_user_meta($cu->ID, 'get_code', true);
            if(empty($get_code)) $check_create = true;
        }
        else{
            if(!in_array($ip, $curent_data)) $check_create = true;
        }

        switch ($check_get_coupon) {
            case 'all':
                $check = true;
                if(isset( $cu->data->user_registered )){
                    $get_code = get_user_meta($cu->ID, 'get_code', true);
                    if(!empty($get_code)) $check = false;
                }
                break;

            case 'user':
                if(isset( $cu->data->user_registered )){
                    $check = true;
                    $get_code = get_user_meta($cu->ID, 'get_code', true);
                    if(!empty($get_code)) $check = false;
                }
                break;

            default:
                $check = ( isset( $cu->data->user_registered ) && strtotime( $cu->data->user_registered ) > strtotime( sprintf( '-%d days', $reg_days_ago ) ) ) ? true : false;
                $get_code = get_user_meta($cu->ID, 'get_code', true);
                if(!empty($get_code)) $check = false;
                break;
        }        
        if(!empty($out_date) && strtotime("now") > strtotime($out_date)) $check = false;
        if($check_create && $check) $check = true;
        else $check = false;
        return $check;
    }
}
/***************************************END Theme Function***************************************/
