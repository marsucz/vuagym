<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:20 AM
 */

if(!defined('ABSPATH')) return;

if(!class_exists('S7upf_Template'))
{
    class S7upf_Template{

        static $_template_dir;

        static function _init()
        {
            // Init some environment
            self::$_template_dir=apply_filters('s7upf_template_dir','/s7upf_templates');

        }


        static function load_view($view_name,$slug=false,$data=array(),$echo=FALSE)
        {
            if($data) extract($data);

            if($slug){
                $template =  get_template_directory().self::$_template_dir.'/'.$view_name.'-'.$slug.'.php';
                if(!$template){
                    $template = get_template_directory().self::$_template_dir.'/'.$view_name.'.php';
                }
            }else{
                $template = get_template_directory().self::$_template_dir.'/'.$view_name.'.php';
            }

            //Allow Template be filter

            $template=apply_filters('s7upf_load_view',$template,$view_name,$slug);

            if(file_exists($template)){

                if(!$echo){
                    ob_start();
                    load_template( $template);

                    return @ob_get_clean();
                }else

                load_template( $template);
            }
        }       

        public static function get_vc_pagecontent($page_id=false)
        {
            if($page_id)
            {
                $page=get_post($page_id);

                if($page)
                {
                    $content=apply_filters('the_content', $page->post_content);

                    $content = str_replace(']]>', ']]&gt;', $content);


                    $shortcodes_custom_css = get_post_meta( $page_id, '_wpb_shortcodes_custom_css', true );

                    S7upf_Assets::add_css($shortcodes_custom_css);

                    wp_reset_postdata();

                    return $content;
                }
            }
        }

        static function remove_wpautop( $content, $autop = false ) {

            if ( $autop ) {
                $content = wpautop( preg_replace( '/<\/?p\>/', "\n", $content ) . "\n" );
            }
            return do_shortcode( shortcode_unautop( $content) );
        }
    }

    S7upf_Template::_init();
}