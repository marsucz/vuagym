<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 18/08/15
 * Time: 10:00 AM
 */
// Start 15/10/2016
if(!function_exists('s7upf_vc_menu'))
{
    function s7upf_vc_menu($attr,$content = false)
    {
        $html = '';
        extract(shortcode_atts(array(
            'style'     => '',
            'menu'      => '',
        ),$attr));
        if(!empty($menu)){
            $html .=    '<nav class="main-nav '.esc_attr($style).'">';
                            ob_start();
                            wp_nav_menu( array(
                                'menu' => $menu,
                                'container'=>false,
                                'walker'=>new S7upf_Walker_Nav_Menu(),
                            ));
            $html .=        @ob_get_clean();
            $html .=        '<a href="#" class="toggle-mobile-menu"><span></span></a>';
            $html .=    '</nav>';
        }
        else{
            $html .=    '<nav class="main-nav '.esc_attr($style).'">';
                            ob_start();
                            wp_nav_menu( array(
                                'theme_location' => 'primary',
                                'container'=>false,
                                'walker'=>new S7upf_Walker_Nav_Menu(),
                            ));
            $html .=        @ob_get_clean();
            $html .=        '<a href="#" class="toggle-mobile-menu"><span></span></a>';
            $html .=    '</nav>';
        }        
        return $html;
    }
}

stp_reg_shortcode('sv_menu','s7upf_vc_menu');

vc_map( array(
    "name"      => esc_html__("SV Menu", 'kuteshop'),
    "base"      => "sv_menu",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type"              => "dropdown",
            "heading"           => esc_html__("Style",'kuteshop'),
            "param_name"        => "style",
            "value"             => array(
                                    esc_html__("Default",'kuteshop')   => '',
                                    esc_html__("Home 2",'kuteshop')   => 'main-nav2',
                                    esc_html__("Home 3",'kuteshop')   => 'main-nav3',
                                    esc_html__("Home 4",'kuteshop')   => 'main-nav4',
                                    esc_html__("Home 5",'kuteshop')   => 'main-nav5',
                                    esc_html__("Home 6",'kuteshop')   => 'main-nav6',
                                    esc_html__("Home 7",'kuteshop')   => 'main-nav6 main-nav7',
                                    esc_html__("Home 8",'kuteshop')   => 'main-nav8',
                                    esc_html__("Home 9",'kuteshop')   => 'main-nav9',
                                    esc_html__("Home 10",'kuteshop')   => 'main-nav10',
                                    esc_html__("Home 11",'kuteshop')   => 'main-nav11',
                                    esc_html__("Home 12",'kuteshop')   => 'main-nav12',
                                    esc_html__("Home 16",'kuteshop')   => 'main-nav16',
                                    esc_html__("Home 17",'kuteshop')   => 'main-nav17',
                                    esc_html__("Home 18",'kuteshop')   => 'main-nav18',
                                )
        ),
        array(
            'type'              => 'dropdown',
            'holder'            => 'div',
            'heading'           => esc_html__( 'Menu name', 'kuteshop' ),
            'param_name'        => 'menu',
            'value'             => s7upf_list_menu_name(),
            'description'       => esc_html__( 'Select Menu name to display', 'kuteshop' )
        ),
    )
));