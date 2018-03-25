<?php
/**
 * Clever_Mega_Menu_Shortcode
 *
 * @package    Clever_Mega_Menu
 * @since: 1.0.6
 */
final class Clever_Mega_Menu_Shortcode
{

    /**
     * Constructor
     */
    function __construct()
    {
        add_shortcode( 'cmm', array($this, 'cmm_register_shortcode'));
    }
    /**
     * Register Shortcode
     */
    function cmm_register_shortcode($atts){
        $att = shortcode_atts( array(
            'id' => '',
        ), $atts );
        $id=$att['id'];
        return wp_nav_menu( array('menu' => $id) );
    }
}
