<?php
/**
 * Clever_Mega_Menu_Term_Meta
 *
 * Hold metadata of a nav menu (a `WP_Term` object).
 *
 * @package    Clever_Mega_Menu
 */
final class Clever_Mega_Menu_Term_Meta
{
    /**
     * Meta key
     *
     * @var    string
     */
    const META_KEY = '_clever_mega_menu_meta';

    /**
     * Settings
     *
     * @var    array
     *
     * @see    Clever_Mega_Menu::$settings
     */
    private $settings;

    /**
     * Meta fields
     *
     * @var    array
     */
    public static $fields = array(
        'enabled'   => '0',
        'se_markup' => '0',
        'theme'     => 'default-461836'
    );

    /**
     * Constructor
     */
    function __construct(array $settings)
    {
        $this->settings = $settings;
    }

    /**
     * Render
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @global    $nav_menu_selected_id    int    Selected menu ID.
     */
    function _render($menu_id = false, $html = false)
    {
        if (is_customize_preview()) {
            return;
        }

        global $nav_menu_selected_id;

        $menu_id = $menu_id ? absint($menu_id) : $nav_menu_selected_id;

        $settings = (array)get_term_meta($menu_id, self::META_KEY, true);
        $settings = array_merge(self::$fields, $settings);

        if (!$html) :
        ?><script type="text/html" id="clever-mega-menu-popup-tpl">
            <div class="clever-mega-menu-popup"></div>
        </script>
        <script type="text/html" id="clever-mega-menu-settings">
        <?php endif; ?>
            <div class="clever-mega-menu-settings">
                <h3><?php esc_html_e('Clever Mega Menu Settings', 'clever-mega-menu'); ?></h3>
                <fieldset class="menu-settings-group">
                    <legend class="menu-settings-group-name howto"><?php esc_html_e('Enable', 'clever-mega-menu'); ?></legend>
                    <div class="menu-settings-input checkbox-input">
                        <input id="clever-mega-menu-enable-checkbox" type="checkbox" value="1" <?php checked($settings['enabled']) ?> name="<?php echo $this->get_field('enabled') ?>">
                        <label for="clever-mega-menu-enable-checkbox"><?php esc_html_e('Whether to use Clever Mega Menu for this menu or not.', 'clever-mega-menu') ?></label>
                    </div>
                </fieldset>
                <fieldset class="menu-settings-group clever-mega-menu-advanced-setting">
                    <legend class="menu-settings-group-name howto"><?php esc_html_e('SEO markup', 'clever-mega-menu'); ?></legend>
                    <div class="menu-settings-input checkbox-input">
                        <input id="clever-mega-menu-enable-se-markup-checkbox" type="checkbox" value="1" <?php checked($settings['se_markup']) ?> name="<?php echo $this->get_field('se_markup') ?>">
                        <label for="clever-mega-menu-enable-se-markup-checkbox"><?php echo sprintf(esc_html__('Whether to apply %sschema markups%s for this menu or not.', 'clever-mega-menu'), '<a href="https://schema.org/SiteNavigationElement">', '</a>') ?></label>
                    </div>
                </fieldset>
                <fieldset class="menu-settings-group clever-mega-menu-advanced-setting">
                    <?php
                        $args = array(
                            'post_type'        => 'clever_menu_theme',
                            'post_status'      => 'publish',
                            'no_found_rows'    => true,
                            'suppress_filters' => true,
                        );
                        $themes = new WP_Query($args);
                    ?>
                    <legend class="menu-settings-group-name howto"><?php esc_html_e('Menu theme', 'clever-mega-menu'); ?></legend>
                    <div class="menu-settings-input checkbox-input">
                        <select id="clever-mega-menu-theme" name="<?php echo $this->get_field('theme') ?>">
                            <?php foreach ($themes->posts as $theme) : ?>
                                <option <?php selected($settings['theme'], $theme->post_name) ?> value="<?php echo $theme->post_name ?>"><?php echo $theme->post_title ?></option>
                            <?php endforeach; ?>
                        </select>
                        <label for="clever-mega-menu-theme"><?php printf(esc_html__('Select a menu theme or %screate a new one%s.', 'clever-mega-menu'), '<a href="' . admin_url('post-new.php?post_type=clever_menu_theme') . '">', '</a>') ?></label>
                    </div>
                </fieldset>
                <fieldset class="menu-settings-group clever-mega-menu-advanced-setting">
                    <legend class="menu-settings-group-name howto"><?php esc_html_e('Mega Menu Shortcode', 'clever-mega-menu'); ?></legend>
                    <div class="menu-settings-input checkbox-input">
                        <code>[cmm id="<?php echo esc_html($menu_id);?>"]</code>
                    </div>
                </fieldset>
            </div>
            <?php if (!$html) : ?>
        </script><?php endif;
    }

    /**
     * Save metadata
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @see    https://developer.wordpress.org/reference/hooks/wp_update_nav_menu/
     */
    function _save($menu_id, $data = false, $update = true)
    {
      if (isset($_REQUEST['wp_customize']) && $_REQUEST['wp_customize'] === 'on') {
        return;
      }

      if (!isset($_POST[self::META_KEY]) || $_POST[self::META_KEY] === self::$fields) {
        return;
      }

      $settings = array_merge(self::$fields, $this->sanitize($_POST[self::META_KEY]));

      if ($update) {
          update_term_meta($menu_id, self::META_KEY, $settings);
      } else {
          return array('settings' => $settings);
      }
    }

    /**
     * Change navigation menus' arguments
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @see    https://developer.wordpress.org/reference/hooks/wp_nav_menu_args/
     *
     * @return    array    $args    Modified arguments.
     */
    function _change_nav_menu_args($args)
    {
        if (is_admin()) {
            return $args;
        }

        $locations = get_nav_menu_locations();

        if (!empty($locations[$args['theme_location']])) {
            $menu = wp_get_nav_menu_object($locations[$args['theme_location']]);
        } elseif (!empty($args['menu'])) {
            $menu = wp_get_nav_menu_object($args['menu']);
        } else {
            $menus = (array)wp_get_nav_menus();
            if ($menus) {
                foreach ($menus as $menu) {
                    $has_items = wp_get_nav_menu_items($menu->term_id, array(
                        'update_post_term_cache' => false
                    ));
                    if ($has_items) {
                        break;
                    }
                }
            }
        }

        if (!isset($menu) || is_wp_error($menu) || !is_object($menu)) {
            return $args;
        }

        $settings = (array)get_term_meta($menu->term_id, self::META_KEY, true);

        if (empty($settings['enabled'])) {
            return $args;
        }

        $theme = !empty($settings['theme']) ? $settings['theme'] : 'default-461836';
        $theme_meta = array();

        $menu_style = 'horizontal';
        $menu_style_classes = ' cmm';
        $args['menu_class'] = 'cmm-theme-' . $theme;

        $menu_theme = get_page_by_path($theme, OBJECT, 'clever_menu_theme');

        if ($menu_theme) {
            $theme_meta = (array)get_post_meta($menu_theme->ID, Clever_Mega_Menu_Theme_Meta::META_KEY, true);
            $menu_style = !empty($theme_meta['menubar_style']) ? $theme_meta['menubar_style'] : 'horizontal';
            if ('horizontal' === $menu_style) {
                $menu_orientation = !empty($theme_meta['menubar_horizontal_layout']) ? 'cmm-' . $theme_meta['menubar_horizontal_layout'] : 'cmm-horizontal-align-left';
                $menu_style_classes .= ' cmm-horizontal ' . $menu_orientation;
            } else {
                $menu_orientation = !empty($theme_meta['menubar_vertical_layout']) ? $theme_meta['menubar_vertical_layout'] : 'cmm-vertical-align-left';
                $menu_style_classes .= ' cmm-vertical ' . $menu_orientation;
                if (!empty($theme_meta['menubar_tab_layout'])) {
                    $menu_style_classes .= ' ' . $theme_meta['menubar_tab_layout'];
                }
            }
            $menu_effect = !empty($theme_meta['general_effect']) ? $theme_meta['general_effect'] : 'fade-in-up';
            if ('fade' === $menu_effect) {
                $menu_style_classes .= ' cmm-menu-fade';
            } elseif ('none' === $menu_effect) {
                $menu_style_classes .= ' cmm-no-effect';
            } else {
                $menu_style_classes .= ' cmm-menu-fade-up';
            }
        }

        $settings = array_merge(self::$fields, $settings);
        $theme_meta = array_merge(Clever_Mega_Menu_Theme_Meta::$fields, $theme_meta);

        $mega_config = array();

        if ('horizontal' === $menu_style) {
            $mega_config['menuStyle'] = 'horizontal';
        } else {
            $mega_config['menuStyle'] = 'vertical';
        }

        $mega_config['parentSelector'] = $theme_meta['mega_panel_css_selector'] ;
        $mega_config['breakPoint'] = $theme_meta['mobile_menu_responsive_breakpoint'];
        $mega_config = "data-options='" . esc_attr(json_encode($mega_config)) . "'";

        $mega_mobile_config = array();
        $mega_mobile_config['toggleDisable'] = $theme_meta['mobile_menu_disable_toggle'];
        $mega_mobile_config['toggleWrapper'] = $theme_meta['mobile_menu_toggle_button_wrapper'];
        $mega_mobile_config['ariaControls'] = $args['menu_id'];
        $mega_mobile_config['toggleIconOpen'] = $theme_meta['mobile_menu_open_icon'];
        $mega_mobile_config['toggleIconClose'] = $theme_meta['mobile_menu_close_icon'];
        $mega_mobile_config['toggleMenuText'] = $theme_meta['mobile_menu_toggle_text'];
        $mega_mobile_config = " data-mobile='" . esc_attr(json_encode($mega_mobile_config)) . "'";

        $items_wrap = $settings['se_markup'] ? '<ul id="%1$s" class="%2$s" ' . $mega_config . $mega_mobile_config . ' itemscope itemtype="https://schema.org/SiteNavigationElement">%3$s</ul>' : '<ul id="%1$s" class="%2$s" ' . $mega_config . $mega_mobile_config . '>%3$s</ul>';

        $this->settings['current_menu_options'] = $settings;
        $this->settings['current_menu_options']['menu_id'] = $menu->term_id;
        $this->settings['current_menu_options']['menu_location'] = $args['theme_location'];

        $args = array(
            'menu'            => $args['menu'],
            'menu_id'         => $args['menu_id'],
            'menu_class'      => $args['menu_class'] . $menu_style_classes,
            'theme_location'  => $args['theme_location'],
            'container'       => 'div',
            'container_id'    => 'cmm-' . $args['theme_location'],
            'container_class' => 'cmm-container',
            'fallback_cb'     => $args['fallback_cb'],
            'before'          => $args['before'],
            'after'           => $args['after'],
            'link_before'     => $args['link_before'],
            'link_after'      => $args['link_after'],
            'echo'            => $args['echo'],
            'depth'           => $args['depth'],
            'items_wrap'      => $items_wrap,
            'item_spacing'    => isset($args['item_spacing']) ? $args['item_spacing'] : '',
            'walker'          => new Clever_Mega_Menu_Walker($this->settings)
        );

        return $args;
    }

    /**
     * Get items' settings
     */
    private function get_item_settings($menu_id)
    {
        $items = wp_get_nav_menu_items($menu_id);

        $menus_items = array();

        if ($items) {
            $meta_key = Clever_Mega_Menu_Item_Meta::SETTINGS_META_KEY;
            $meta_fields = Clever_Mega_Menu_Item_Meta::$fields;
            foreach ($items as $item) {
                $settings = (array)get_post_meta($item->ID, $meta_key, true);
                $settings = array_merge($meta_fields, $settings);
                $menus_items[$item->ID] = array(
                    'url' =>  admin_url('post-new.php?post_type=clever_menu&clever_menu_id='.$menu_id.'&clever_menu_item_id='.$item->ID),
                    'options' => $settings,
              );
            }
        }

        return $menus_items;
    }

    /**
     * Do sanitization
     */
    private function sanitize($meta)
    {
        return $meta;
    }

    /**
     * Get field ID
     *
     * @param    string    $name    Field name.
     *
     * @return    string   $field    Field's ID.
     */
    private function get_field($name)
    {
        $field = self::META_KEY . '[' . $name . ']';

        return $field;
    }

    /**
     * Get field value
     *
     * @param    string    $name      Field name.
     * @param    array     $values    An array of values.
     *
     * @return    mixed    $value    Field's value.
     */
    private function get_value($name, $values)
    {
        $value = isset($values[$name]) ? $values[$name] : self::$fields[$name];

        return $value;
    }
}
