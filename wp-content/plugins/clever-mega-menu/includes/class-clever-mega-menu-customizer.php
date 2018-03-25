<?php
/**
 * Clever_Mega_Menu_Customizer
 *
 * @package    Clever_Mega_Menu
 */
final class Clever_Mega_Menu_Customizer
{
    /**
     * Settings
     *
     * @see    Clever_Mega_Menu::$settings
     *
     * @var    array
     */
    private $settings;

    /**
     * Constructor
     */
    function __construct($settings)
    {
        $this->settings = $settings;
        global $clever_menu_customize_data;
        $clever_menu_customize_data = array();
    }

    /**
     * Save customized data.
     *
     * @var    object    $customize    \WP_Customize_Manager
     */
    function _save($customize)
    {
        $data = $_REQUEST;
        $data = stripslashes_deep($data);
        $data = json_decode($data['customized'], true);

        foreach($data as $key => $_data) {
            if (strpos($key, 'nav_menu_item') !== false) {
                $item_id = preg_replace('/[^0-9]/','', $key);
                $this->save_menu_item($_data, $item_id);
            } elseif (strpos($key, 'nav_menu') !== false) {
                $menu_id = preg_replace('/[^0-9]/','', $key);
                $this->save_nav_menu($_data, $menu_id, false);
            }
        }
    }

    /**
     * Preview customized data.
     */
    function _preview()
    {
        $data = stripslashes_deep($_REQUEST);

        if (!isset($data['customized']))
            return;

        $data = json_decode($data['customized'], true);

        if (empty($data))
            return;

        foreach($data as $key => $_data) {
            if (strpos($key, 'nav_menu_item') !== false) {
                $item_id = preg_replace('/[^0-9]/','', $key);
                $this->save_menu_item($_data, $item_id, true);
            } elseif (strpos($key, 'nav_menu') !== false) {
                $menu_id = preg_replace('/[^0-9]/','', $key);
                $this->save_nav_menu($_data, $menu_id , true);
            }
        }
    }

    /**
     * Change menu style
     */
    function filter_css($css, $menu_id)
    {
        $key = 'clever_menu_get_nav_css_' . $menu_id;

        global $clever_menu_customize_data;

        if(isset($clever_menu_customize_data[$key]))
            return $clever_menu_customize_data[$key];

        return $css;
    }

    function filter_settings($settings, $menu_id)
    {
        $key = 'vc_nav_settings_' . $menu_id;

        global $clever_menu_customize_data;

        if(isset($clever_menu_customize_data[$key]))
            return $clever_menu_customize_data[$key];

        return $settings;
    }

    function filter_item_settings($settings, $menu_id)
    {
        $key = 'clever_menu_item_settings_' . $menu_id;

        global $clever_menu_customize_data;

        if(isset($clever_menu_customize_data[$key]))
            return $clever_menu_customize_data[$key];

        return $settings;
    }

    function filter_item_content($settings, $menu_id)
    {
        $key = 'clever_menu_item_content_' . $menu_id;

        global $clever_menu_customize_data;

        if(isset($clever_menu_customize_data[$key]))
            return $clever_menu_customize_data[$key];

        return $settings;
    }

    function filter_item_css($settings, $menu_id)
    {
        $key = 'clever_menu_item_css_' . $menu_id;

        global $clever_menu_customize_data;

        if(isset($clever_menu_customize_data[$key]))
            return $clever_menu_customize_data[$key];

        return $settings;
    }

    function save_menu_item($data , $item_id, $preview = false)
    {
        $item_meta = new Clever_Mega_Menu_Item_Meta($this->settings);

        $_d = $item_meta->_save($data['clever_menu_data'], $preview);

        if ($preview) {
            global $clever_menu_customize_data;
            $key = 'clever_menu_item_settings_' . $item_id;
            $clever_menu_customize_data[$key] = $_d['settings'];
            $key = 'clever_menu_item_content_' . $item_id;
            $clever_menu_customize_data[$key] = $_d['content'];
            $key = 'clever_menu_item_css_' . $item_id;
            $clever_menu_customize_data[$key] = $_d['css'];
            add_filter('clever_menu_get_item_settings', array($this, 'filter_item_settings'), 1000, 2);
            add_filter('clever_menu_get_mega_content', array($this, 'filter_item_content'), 1000, 2 );
            add_filter('clever_menu_get_item_css', array($this, 'filter_item_css') , 1000, 2);
        }
    }

    function save_nav_menu($data, $menu_id , $preview = false)
    {
        if (!is_array($data) || !isset($data['vc_nav_settings']))
            return false;

        $settings  = $data['vc_nav_settings'];
        $settings  = wp_parse_args($settings, array());
        $term_meta = new Clever_Mega_Menu_Term_Meta($this->settings);
        $_data     = $term_meta->_save($menu_id, $settings, $preview);

        if ($preview && $_data) {
            global $clever_menu_customize_data;
            $clever_menu_customize_data['clever_menu_get_nav_css_' . $menu_id] = $_data['css'];
            $clever_menu_customize_data['vc_nav_settings_' . $menu_id] = $_data['settings'];
            add_filter('clever_menu_nav_css', array($this, 'filter_css'), 1000, 2);
            add_filter('clever_menu_get_nav_settings', array($this, 'filter_settings'), 1000, 2);
        }
    }
}
