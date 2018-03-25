<?php
/**
 * Clever_Mega_Menu_Walker
 *
 * @package    Clever_Mega_Menu
 */
final class Clever_Mega_Menu_Walker extends Walker_Nav_Menu
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
    function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Starts the list before the elements are added.
     *
     * @see Walker::start_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    function start_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<div class='cmm-sub-container'><ul class=\"sub-menu cmm-sub-wrapper\">\n";
    }

    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    function end_lvl(&$output, $depth = 0, $args = array())
    {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul></div>\n";
    }

    /**
     * Start the element output.
     *
     * @see Walker::start_el()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Menu item data object.
     * @param int    $depth  Depth of menu item. Used for padding.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     * @param int    $id     Current item ID.
     */
    function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0)
    {
        $atts = array();
        $args = (array)$args;
        $is_mobile = wp_is_mobile();
        $content   = $this->get_item_content($item->ID);
        $settings  = $this->get_item_settings($item->ID);

        if (!$this->is_viewable($settings)) {
            return;
        }

        if ($settings['hide_on_mobile'] && $is_mobile) {
            return;
        }

        if ($settings['hide_on_desktop'] && !$is_mobile) {
            return;
        }

        $indent  = $depth ? str_repeat("\t", $depth) : '';
        $classes = array();
        $default_classes = (array)$item->classes;
        $class_hide_sub = '';

        if ($this->has_children) {
            $classes[] = 'menu-item-has-children';
            if ($settings['hide_sub_item_on_mobile']) {
                $classes[] = 'cmm-hide-sub-items';
            }
            if (0 === $depth) {
                $atts['aria-haspopup'] = 'true';
                $classes[] = 'cmm-parent-menu-item';
                if(isset($settings['width'])){
                    $classes[]='cmm-custom-width';
                }
            }
        }

        if ($content && $settings['enable']) {
            $atts['aria-haspopup'] = 'true';            
        }

        if (in_array('current-menu-item', $default_classes)) {
            $classes[] = 'cmm-current-menu-item';
        }

        $classes[] = 'cmm-item-depth-'.$depth;
        $classes[] = join(' ', $this->get_item_html_classes($settings));

        $html_classes = join(' ', array_filter($classes));
        $html_classes = ($content && $settings['enable']) ? $html_classes . ' menu-item-has-children cmm-item-has-content' . $class_hide_sub : $html_classes;
        $html_classes = $html_classes ? ' class="' . $html_classes . '"' : '';

        $id = $id ? ' id="cmm-item-'.$item->ID.'"' : '';

        $item_config = array();
        $item_config['width'] = $settings['width'];
        $item_config['layout'] = $settings['layout'];

        $li_attr = " data-settings='".esc_attr(json_encode($item_config))."'";
        $output .= $indent.'<li'.$id.$html_classes.$li_attr.'>';

        $atts['title']  = !empty($item->attr_title) ? $item->attr_title : '';
        $atts['target'] = !empty($item->target)     ? $item->target     : '';
        $atts['rel']    = !empty($item->xfn)        ? $item->xfn        : '';
        $atts['href']   = !empty($item->url)        ? $item->url        : '';

        if ($settings['disable_link']) {
            unset($atts['href']);
        }

        $attributes = '';

        if (!isset($atts['class'])) {
            $atts['class'] = '';
        }

        foreach ($atts as $attr => $value) {
            if ($attr === 'class') {
                if (!empty($value)) {
                    $value .= ' cmm-nav-link';
                } else {
                    $value .= 'cmm-nav-link';
                }
            }
            if (!empty($value)) {
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }

        $se_markup = $this->settings['current_menu_options']['se_markup'];
        $item_output = isset($args['before']) ? $args['before'] : '';
        $item_output .= $settings['disable_link'] ? '<span'. $attributes .'>' : '<a'. $attributes;

        if (!$settings['disable_link']) {
            $item_output .= $se_markup ? ' itemprop="url">' : '>';
        }

        $item_output .= $this->get_font_icon($settings);

        if (!$settings['hide_title']) {
            $item_output .='<span class="cmm-item-label"';
            $item_output .= ($se_markup && !$settings['disable_link']) ? ' itemprop="name">' : '>';
            $item_output .= $item->title;
            $item_output .='</span>';
        }

        $item_output .= $settings['disable_link'] ? '</span>' : '</a>';
        $item_output .= isset($args['after']) ? $args['after'] : '';

        if ($content && $settings['enable']) {
            $custom_menus = array();
            preg_match_all('~\[vc_wp_custommenu[^\]]+\]~', $content, $matches);
            if ( !empty($matches[0]) ) {
                foreach ($matches[0] as $match) {
                    $custom_menu_id = intval(filter_var($match, FILTER_SANITIZE_NUMBER_INT));
                    $custom_menus[] = $custom_menu_id;
                }
            }
            $depth = 1;
            $item_output .= '<div class="cmm-content-container">';
            $item_output .= '<div class="cmm-content-wrapper">';
            if (!in_array($this->settings['current_menu_options']['menu_id'], $custom_menus)) {
                $content = preg_replace('/<p>(.*?)<\/p>/', '$1', $content);
                $item_output .= do_shortcode($content);
            } else {
                $item_output .= esc_html__('Warning: You cannot use current menu as a sub menu!', 'clever-mega-menu');
            }
            if (!empty($settings['mega_panel_img_id'])) {
                $img_src = wp_get_attachment_image_src($settings['mega_panel_img_id'], $settings['mega_panel_img_size']);
                if ($img_src) {
                    $style = '';
                    $image_alt = get_post_meta($settings['mega_panel_img_id'], '_wp_attachment_image_alt', true) ? : '';
                    if (!empty($settings['mega_panel_img_position_top'])) {
                        $style .= 'top:'.$settings['mega_panel_img_position_top'].';';
                    }
                    if (!empty($settings['mega_panel_img_position_right'])) {
                        $style .= 'right:'.$settings['mega_panel_img_position_right'].';';
                    }
                    if (!empty($settings['mega_panel_img_position_bottom'])) {
                        $style .= 'bottom:'.$settings['mega_panel_img_position_bottom'].';';
                    }
                    if (!empty($settings['mega_panel_img_position_left'])) {
                        $style .= 'left:'.$settings['mega_panel_img_position_left'].';';
                    }
                    $item_output .= '<img class="cmm-panel-image" src="'.$img_src[0].'" width="'.$img_src[1].'" height="'.$img_src[2].'" alt="'.$image_alt.'"';
                    if ($style) {
                        $item_output .= ' style="'.$style.'">';
                    } else {
                        $item_output .= '>';
                    }
                }
            }
            $item_output .= '</div>';
            $item_output .= '</div>';
        }

        $output .= apply_filters('clever_walker_nav_menu_start_el', $item_output, $item, $depth, $args, $settings);
    }

    /**
     * Ends the element output, if needed.
     *
     * @see Walker::end_el()
     *
     * @since 3.0.0
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param object $item   Page data object. Not used.
     * @param int    $depth  Depth of page. Not Used.
     * @param array  $args   An array of arguments. @see wp_nav_menu()
     */
    function end_el(&$output, $item, $depth = 0, $args = array())
    {
        $output .= "</li>\n";
    }

    /**
     * Get item settings
     *
     * @param    int    $id    Item's ID.
     *
     * @return    array    $settings
     */
    private function get_item_settings($item_id)
    {
        $settings    = (array)get_post_meta($item_id, Clever_Mega_Menu_Item_Meta::SETTINGS_META_KEY, true);
        $item_roles  = array('role_anyone' => 1);
        $valid_roles = wp_roles()->roles;

        foreach ($valid_roles as $role => $data) {
            $fake_role = 'role_'.$role;
            $item_roles[$fake_role] = '0';
        }

        $default_settings = Clever_Mega_Menu_Item_Meta::$fields + $item_roles;

        $settings = array_merge($default_settings, $settings);

        return $settings;
    }

    /**
     * Get item settings
     *
     * @param    int    $id    Item's ID.
     *
     * @return    mixed    $content
     */
    private function get_item_content($item_id)
    {
        $content = get_post_meta($item_id, Clever_Mega_Menu_Item_Meta::CONTENT_META_KEY, true);

        return trim($content);
    }

    /**
     * Build custom HTML classes
     *
     * @param    array    $settings    Item's settings.
     * @param    array    $classes     Default classes.
     *
     * @return    array    $classes    Custom HTML classes.
     */
    private function get_item_html_classes(array $settings, array $classes = array())
    {
        if (intval($settings['enable'])) {
            $classes[] = 'cmm-mega';
        }

        if ($settings['icon']) {
            $classes[] = 'cmm-has-icon';
        }

        if ($settings['hide_title']) {
            $classes[] = 'cmm-hide-title';
        }

        if ($settings['class']) {
            $classes[] = esc_attr($settings['class']);
        }

        if ($settings['layout']) {
            $classes[] = 'cmm-layout-'.$settings['layout'];
        }

        return $classes;
    }

    /**
     * Parse icon
     *
     * @param    array    $settings    Menu item's settings.
     *
     * @return    string    $icon    Parsed icon string.
     */
    private function get_font_icon(array $settings)
    {
        $icon = empty($settings['icon']) ? '' : trim($settings['icon']);

        if ($icon) {
            $icon = '<span class="cmm-icon"><i class="'.$icon.'"></i></span>';
        }

        return $icon;
    }

    /**
     * Check if mega menu is enabled
     *
     * @param    int    $id    Item's ID.
     *
     * return    bool
     */
    private function is_mega($item_id)
    {
        $settings = $this->get_item_settings($item_id);

        return (bool)$settings['enable'];
    }

    /**
     * Check if a menu item is viewable
     *
     * @param    array    $settings    Item's settings.
     *
     * @return    bool
     */
    private function is_viewable($item_settings)
    {
        $valid_roles = array();
        $roles = wp_roles()->roles;

        foreach ($roles as $role => $data) {
            $fake_role = 'role_'.$role;
            if (isset($item_settings[$fake_role]) && $item_settings[$fake_role]) {
                $valid_roles[] = $role;
            }
        }

        $user = wp_get_current_user();

        if ($user->exists()) {
            $user_minimal_role = $user->roles[0];
        } else {
            $user_minimal_role = 'role_anyone';
        }

        if (in_array($user_minimal_role, $valid_roles) || $item_settings['role_anyone']) {
            return true;
        }

        return false;
    }
}
