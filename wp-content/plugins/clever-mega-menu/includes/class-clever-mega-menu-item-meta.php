<?php
/**
 * Clever_Mega_Menu_Item_Meta
 *
 * @package    Clever_Mega_Menu
 */
final class Clever_Mega_Menu_Item_Meta
{
    /**
     * Meta key
     *
     * @var    string
     */
    const CONTENT_META_KEY = '_clever_mega_menu_item_meta_content';

    /**
     * Meta key
     *
     * @var    string
     */
    const SETTINGS_META_KEY = '_clever_mega_menu_item_meta_settings';

    /**
     * Settings
     *
     * @see    Clever_Mega_Menu::$settings
     *
     * @var    array
     */
    private $settings;

    /**
     * Meta fields
     *
     * @var    array
     */
    public static $fields = array(
        'icon' => '',
        'width' => '',
        'class' => '',
        'enable' => '0',
        'layout' => 'full',
        'hide_title' => '0',
        'role_anyone' => '1',
        'disable_link' => '0',
        'hide_on_mobile' => '0',
        'hide_on_desktop' => '0',
        'mega_panel_img_id' => '',
        'mega_panel_img_src' => '',
        'mega_panel_img_size' => 'full',
        'hide_sub_item_on_mobile' => '0',
        'mega_panel_img_position_top' => '',
        'mega_panel_img_position_left' => '',
        'mega_panel_img_position_right' => '',
        'mega_panel_img_position_bottom' => '',
    );

    /**
     * Meta values
     *
     * @var    array
     */
    private $values;

    /**
     * Constructor
     */
    function __construct(array $settings)
    {
        $this->settings = $settings;

        $roles = wp_roles()->roles;

        foreach ($roles as $key => $value) {
            $role = 'role_'.$key;
            self::$fields[$role] = '0';
        }
    }

    /**
     * Add metabox
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @param    object    \WP_Post
     */
    function _add($post)
    {
        add_meta_box(
            'clever_menu_meta_box',
            esc_html__('Menu Item Settings', 'clever-mega-menu'),
            array($this, '_render'),
            'clever_menu',
            'normal',
            'high'
        );
    }

    /**
     * Add users' capability for VC
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     */
    function _add_vc_capability()
    {
        global $current_user;

        if ( in_array('administrator', $current_user->caps) ) {
            $cap = get_role('administrator');
            $cap->add_cap('vc_access_rules_post_types', 'custom');
            $cap->add_cap('vc_access_rules_presets', true);
            $cap->add_cap('vc_access_rules_settings', true);
            $cap->add_cap('vc_access_rules_templates', true);
            $cap->add_cap('vc_access_rules_shortcodes', true);
            $cap->add_cap('vc_access_rules_grid_builder', true);
            $cap->add_cap('vc_access_rules_post_settings', true);
            $cap->add_cap('vc_access_rules_backend_editor', true);
            $cap->add_cap('vc_access_rules_frontend_editor', true);
            $cap->add_cap('vc_access_rules_post_types/post', true);
            $cap->add_cap('vc_access_rules_post_types/page', true);
            $cap->add_cap('vc_access_rules_post_types/clever_menu', true);
        }
    }

    /**
     * Save metadata
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     */
    function _save($data = false, $preview = false)
    {
        $response = array(
            'url' => '',
            'status' => false,
            'errors' => array(),
            'is_update' => true,
        );

        if (!$data) $data = $_POST;

        $item_id = !empty($data['clever_menu_item_id']) ? intval($data['clever_menu_item_id']) : 0;
        $menu_id = !empty($data['clever_menu_id']) ? intval($data['clever_menu_id']) : 0;

        if (!$item_id) {
            $response['errors'][] = esc_html__('Menu item not exists.', 'clever-mega-menu');
            exit(json_encode($response));
        }

        $post = get_post($item_id);

        if ($post->post_type !== 'nav_menu_item') {
            if ($preview) {
                return false;
            }
            $response['errors'][] = esc_html__('Menu item not exists.', 'clever-mega-menu');
            exit(json_encode($response));
        }

        $settings = array_merge(self::$fields, $this->sanitize($data));
        $content  = !empty($data['content']) ? $data['content'] : '';
        $styles1  = $this->parse_shortcodes_css(stripslashes($content));
        $styles1  = apply_filters('vc_base_build_shortcodes_custom_css', $styles1);
        $styles2  = !empty($data['vc_post_custom_css']) ? $data['vc_post_custom_css'] : '';

        if ($preview) {
            return array(
                'css' => $styles1.$styles2,
                'content' => $content,
                'settings' => $settings,
            );
        } else {
            if ($styles1 || $styles2) {
                update_post_meta($item_id, '_vc_custom_item_css', $styles1.$styles2);
            } else {
                delete_post_meta($item_id, '_vc_custom_item_css', $styles1.$styles2);
            }
            if ($styles2) {
                update_post_meta($item_id, '_vc_custom_post_css', $styles2);
            } else {
                delete_post_meta($item_id, '_vc_custom_post_css', $styles2);
            }
            update_post_meta($item_id, self::SETTINGS_META_KEY, $settings);
            update_post_meta($item_id, self::CONTENT_META_KEY, $content);
        }

        $response['url'] = admin_url('post-new.php?post_type=clever_menu&clever_menu_id='.$menu_id.'&clever_menu_item_id='.$item_id);
        $response['settings'] = $settings;

        exit(json_encode($response));
    }

    /**
     * Callback
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     */
    function _render()
    {
        global $post;

        $menu_id = isset($_REQUEST['clever_menu_id']) ? absint($_REQUEST['clever_menu_id']) : 0;
        $item_id = isset($_REQUEST['clever_menu_item_id']) ? absint($_REQUEST['clever_menu_item_id']) : 0;

        $settings = (array)get_post_meta($item_id, self::SETTINGS_META_KEY, true);
        $settings = array_merge(self::$fields, $settings);

        ?><input type="text" name="clever_menu_item_id" value="<?php echo $item_id ?>">
        <input type="text" name="clever_menu_id" value="<?php echo $menu_id ?>">

        <script type="text/html" id="clever-enable-mega-menu-switch">
            <div class="onoffswitch">
                <input type="checkbox" id="clever-mega-menu-item-enable" name="enable" <?php checked($settings['enable']); ?> value="1" class="onoffswitch-checkbox">
                <label for="clever-mega-menu-item-enable" class="onoffswitch-label"></label>
            </div>
        </script>

        <script type="text/html" id="clever-mega-menu-item-settings-tabs">
            <ul class="clever-mega-menu-item-setting-tabs">
                <li class="clever-mega-menu-item-setting-tab clever-mega-menu-to-content active-item-setting-tab"><span><?php esc_html_e('Mega Menu', 'clever-mega-menu') ?></span></li>
                <li class="clever-mega-menu-item-setting-tab clever-mega-menu-to-settings"><span><?php esc_html_e('Settings', 'clever-mega-menu'); ?></span></li>
                <li class="clever-mega-menu-item-setting-tab clever-mega-menu-to-design"><span><?php esc_html_e('Design', 'clever-mega-menu'); ?></span></li>
                <li class="clever-mega-menu-item-setting-tab clever-mega-menu-to-icons"><span><?php esc_html_e('Icon', 'clever-mega-menu'); ?></span></li>
            </ul>
        </script>

        <script type="text/html" id="clever-mega-menu-vc-icon-picker">
            <div class="clever-mega-menu-icon-popup-w">
                <div class="vc-icons-selector fip-vc-theme-grey clever-mega-menu-icon-popup" style="position: relative;">
                    <div class="selector">
                        <span class="selected-icon"></span>
                        <span class="selector-button toggle-list">
                            <i class="fip-fa dashicons dashicons-arrow-up-alt2"></i>
                        </span>
                        <span class="selector-button remove">
                            <i class="fip-fa dashicons dashicons-no-alt"></i>
                        </span>
                    </div>
                    <div class="selector-popup">
                        <div class="selector-search">
                            <input type="text" class="icons-search-input" placeholder="<?php esc_html_e('Search Icon', 'clever-mega-menu'); ?>" value="" name="">
                            <i class="fip-fa dashicons dashicons-search"></i>
                        </div>
                        <div class="fip-icons-container">
                            <?php echo $this->get_font_icons(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </script>

        <script type="text/html" id="clever-mega-menu-item-settings">
            <div class="clever-mega-menu-item-settings">
                <div class="clever-mega-menu-tab clever-mega-menu-tab-settings" style="display:none">
                    <div class="vc_col-xs-12 vc_column wpb_el_type_checkbox">
                        <div class="wpb_element_label"><?php esc_html_e('Top Level Item Settings', 'clever-mega-menu'); ?></div>
                        <div class="edit_form_line">
                            <label>
                                <span><?php esc_html_e('Hide title', 'clever-mega-menu'); ?></span>
                                <input type="checkbox"  value="1" <?php checked($settings['hide_title'], 1); ?> class="wpb_vc_param_value wpb-textinput" name="hide_title">
                                <span class="description"><?php esc_html_e('Whether to display item without text or not.', 'clever-mega-menu') ?></span>
                            </label>
                        </div>
                        <div class="edit_form_line">
                            <label>
                                <span><?php esc_html_e('Disable link', 'clever-mega-menu'); ?></span>
                                <input type="checkbox"  value="1" <?php checked($settings['disable_link'], 1); ?> class="wpb_vc_param_value wpb-textinput" name="disable_link">
                                <span class="description"><?php esc_html_e('Whether to disable item hyperlink or not.', 'clever-mega-menu') ?></span>
                            </label>
                        </div>
                        <div class="edit_form_line">
                            <label>
                                <span><?php esc_html_e('Hide on mobile', 'clever-mega-menu'); ?></span>
                                <input type="checkbox"  value="1" <?php checked($settings['hide_on_mobile'], 1); ?> class="wpb_vc_param_value wpb-textinput" name="hide_on_mobile">
                                <span class="description"><?php esc_html_e('Whether to hide item on mobile devices or not.', 'clever-mega-menu') ?></span>
                            </label>
                        </div>
                        <div class="edit_form_line">
                            <label>
                                <span><?php esc_html_e('Hide on desktop', 'clever-mega-menu'); ?></span>
                                <input type="checkbox"  value="1" <?php checked($settings['hide_on_desktop'], 1); ?> class="wpb_vc_param_value wpb-textinput" name="hide_on_desktop">
                                <span class="description"><?php esc_html_e('Whether to hide item on desktop screens or not.', 'clever-mega-menu') ?></span>
                            </label>
                        </div>
                    </div>
                    <div class="vc_col-xs-12 vc_column wpb_el_type_checkbox">
                        <div class="wpb_element_label"><?php esc_html_e('Sub Menu Item Settings', 'clever-mega-menu'); ?></div>
                        <div class="edit_form_line">
                            <label class="inline"><?php esc_html_e('Sub menu alignment', 'clever-mega-menu'); ?></label>
                            <?php
                            $layouts = array(
                                'full' => array('label' => esc_html__('Full Width', 'clever-mega-menu'), 'url' => $this->settings['baseuri'] . 'assets/backend/images/layouts/submenu-horizontal-full-width.jpg'),
                                'center' => array('label' => esc_html__('Default', 'clever-mega-menu'), 'url' => $this->settings['baseuri'] . 'assets/backend/images/layouts/submenu-horizontal-align-center.jpg'),
                                'right_edge_item' => array('label' => esc_html__('Left edge item', 'clever-mega-menu'), 'url' => $this->settings['baseuri'] . 'assets/backend/images/layouts/submenu-horizontal-align-left.jpg'),
                                'left_edge_item' => array('label' => esc_html__('Right edge item', 'clever-mega-menu'), 'url' => $this->settings['baseuri'] . 'assets/backend/images/layouts/submenu-horizontal-align-right.jpg'),
                            );
                            foreach($layouts as $k => $layout) : ?>
                                <span class="image-radio">
                                    <input id="layout-<?php echo esc_attr($k ); ?>" type="radio" <?php checked($settings['layout'], $k); ?> name="layout" value="<?php echo esc_attr($k); ?>">
                                    <label for="layout-<?php echo esc_attr($k); ?>"><img alt="" src="<?php echo esc_url($layout['url']) ?>"></label>
                                </span>
                            <?php endforeach ?>
                        </div>
                        <div class="edit_form_line submenu-item-with">
                            <?php $subwidth = $settings['width'] ? $settings['width'] . 'px' : '' ?>
                            <label>
                                <span><?php esc_html_e('Sub menu item width (px only)', 'clever-mega-menu'); ?></span>
                                <input type="text" value="<?php echo $subwidth ?>" class="wpb_vc_param_value wpb-textinput el_class textfield" name="width">
                            </label>
                        </div>
                        <div class="edit_form_line">
                            <label>
                                <span><?php esc_html_e('Hide sub item on mobile devices', 'clever-mega-menu'); ?></span>
                                <input type="checkbox"  value="1" <?php checked($settings['hide_sub_item_on_mobile']); ?> class="wpb_vc_param_value wpb-textinput" name="hide_sub_item_on_mobile">
                                <span class="description"><?php esc_html_e('Whether to hide sub item on mobile devices or not.', 'clever-mega-menu') ?></span>
                            </label>
                        </div>
                    </div>
                    <div class="vc_col-xs-12 vc_column wpb_el_type_textfield">
                        <div class="wpb_element_label"><?php esc_html_e('Roles & Restrictions', 'clever-mega-menu'); ?></div>
                        <div class="edit_form_line">
                            <label>
                                <span><?php esc_html_e('Anyone', 'clever-mega-menu'); ?></span>
                                <input type="checkbox" name="role_anyone" class="wpb_vc_param_value wpb-textinput" value="1"<?php checked($settings['role_anyone']); ?>>
                            </label>
                        </div><?php
                        $roles = wp_roles()->roles;
                        foreach ($roles as $role => $info) : $name = 'role_'.$role;
                        ?><div class="edit_form_line">
                            <label>
                                <span><?php echo ucfirst($role); ?></span>
                                <input type="checkbox" name="<?php echo $name ?>" class="wpb_vc_param_value wpb-textinput" value="1"<?php checked($settings[$name]); ?>>
                            </label>
                        </div>
                        <?php endforeach ?>
                    </div>
                </div>
                <div class="clever-mega-menu-tab clever-mega-menu-tab-design" style="display:none">
                    <div class="vc_col-xs-12 vc_column wpb_el_type_checkbox">
                        <div class="wpb_element_label"><span><?php esc_html_e('Mega Panel Image', 'clever-mega-menu') ?></span></div>
                        <div class="edit_form_line clever-menu-item-bg-position">
                            <label class="inline"><?php esc_html_e('Absolute position', 'clever-mega-menu'); ?></label>
                            <label><p class="description"><?php esc_html_e('Top', 'clever-mega-menu') ?></p><input type="text"  value="<?php echo esc_attr($settings['mega_panel_img_position_top'] ); ?>" name="mega_panel_img_position_top"></label>
                            <label><p class="description"><?php esc_html_e('Right', 'clever-mega-menu') ?></p><input type="text" value="<?php echo esc_attr($settings['mega_panel_img_position_right'] ); ?>" name="mega_panel_img_position_right"></label>
                            <label><p class="description"><?php esc_html_e('Bottom', 'clever-mega-menu') ?></p><input type="text" value="<?php echo esc_attr($settings['mega_panel_img_position_bottom'] ); ?>" name="mega_panel_img_position_bottom"></label>
                            <label><p class="description"><?php esc_html_e('Left', 'clever-mega-menu') ?></p><input type="text" value="<?php echo esc_attr($settings['mega_panel_img_position_left'] ); ?>" name="mega_panel_img_position_left"></label>
                        </div>
                        <div class="edit_form_line clever-menu-item-bg-image-src">
                            <label class="inline"><?php esc_html_e('Image Source', 'clever-mega-menu'); ?></label>
                            <?php
                            $feat_image_url = '';
                            if ($settings['mega_panel_img_id']) {
                                $feat_image_url = wp_get_attachment_url($settings['mega_panel_img_id'] );
                            }
                            ?>
                            <div class="item-media <?php echo ($feat_image_url !='') ? 'has-img': 'no-img'; ?>">
                                <div class="thumbnail-image">
                                    <?php if ($feat_image_url != ''){
                                        ?><img src="<?php echo esc_url($feat_image_url ); ?>" alt="">
                                    <?php
                                    } ?>
                                </div>
                                <input id="mega-panel-img-src" type="hidden" value="<?php echo esc_attr($settings['mega_panel_img_src']) ;?>" name="mega_panel_img_src">
                                <input id="mega-panel-img-id" type="hidden" value="<?php echo esc_attr($settings['mega_panel_img_id']) ;?>" name="mega_panel_img_id">
                                <a href="#" class="vc-remove-button" title="<?php esc_html_e('Remove', 'clever-mega-menu'); ?>"><span class="dashicons dashicons-no-alt"></span></a>
                                <a href="#" class="vc-add-button" title="<?php esc_html_e('Add image', 'clever-mega-menu'); ?>"><span class="dashicons dashicons-plus"></span></a>
                            </div>
                            <div class="vc_clearfix"></div>
                        </div>
                        <div class="edit_form_line clever-menu-item-bg-image-size">
                            <label class="inline"><?php esc_html_e('Image Size', 'clever-mega-menu'); ?></label>
                            <select name="mega_panel_img_size"><?php
                                global $_wp_additional_image_sizes;
                                $available_sizes = get_intermediate_image_sizes();
                                foreach ($available_sizes as $size) :
                                    if (in_array($size, array('thumbnail', 'medium', 'medium_large', 'large'))) : ?>
                                        <option value="<?php echo $size ?>" <?php selected($settings['mega_panel_img_size'], $size) ?>>
                                            <?php echo ucwords(str_replace(array('_', '-'), array(' ', ' '), $size)) . ' (' . get_option("{$size}_size_w") . 'x' . get_option("{$size}_size_h") . ')' ?>
                                        </option>
                                    <?php elseif (isset($_wp_additional_image_sizes[$size])) : ?>
                                    <option value="<?php echo $size ?>" <?php selected($settings['mega_panel_img_size'], $size) ?>>
                                        <?php echo ucwords(str_replace(array('_', '-'), array(' ', ' '), $size)) . ' (' . $_wp_additional_image_sizes[$size]['width'] . 'x' . $_wp_additional_image_sizes[$size]['height'] . ')' ?>
                                        </option><?php
                                    endif;
                                endforeach;
                                ?><option value="full" <?php selected($settings['mega_panel_img_size'], 'full') ?>><?php esc_html_e('Full Size (Original Size)', 'clever-mega-menu') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="vc_clearfix"></div>
                    <div class="vc_col-sm-12 vc_column wpb_el_type_textfield">
                        <div class="wpb_element_label"><?php esc_html_e('Extra class name', 'clever-mega-menu'); ?></div>
                        <div class="edit_form_line">
                            <input type="text"  value="<?php echo esc_attr($settings['class'] ); ?>" class="wpb_vc_param_value wpb-textinput el_class textfield" name="class">
                        </div>
                    </div>
                </div>
                <div class="clever-mega-menu-tab clever-mega-menu-tab-icons" style="display:none">
                    <div class="vc_column vc_col-xs-12 wpb_el_type_dropdown vc_wrapper-param-type-dropdown vc_shortcode-param">
                        <input type="hidden" class="vc_icon_picker" value="<?php echo esc_attr($settings['icon']); ?>" name="icon">
                    </div>
                </div>
            </div>
        </script><?php
    }

    /**
     * Add loading spinner
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @see    https://developer.wordpress.org/reference/hooks/in_admin_header/
     */
    function _add_loading_spinner()
    {
        global $post;

        if (!$post || $post->post_type !== 'clever_menu') {
            return;
        }

        ?><div class="clever-mega-menu-loading">
            <div class="vc-mdl">
                <span class="spinner"></span>
            </div>
        </div><?php
    }

    /**
     * Change post content
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @see    https://developer.wordpress.org/reference/hooks/edit_form_top/
     */
    function _change_content($post)
    {
        if (isset($_REQUEST['clever_menu_item_id']) && 'clever_menu' === $post->post_type) {
            $post_id = absint($_REQUEST['clever_menu_item_id']);
            $GLOBALS['post']->post_content = get_post_meta($post_id, self::CONTENT_META_KEY, true);
        }
    }

    /**
     * Get icons
     *
     * @return    string    $html
     */
    private function get_font_icons()
    {
        $group_icons = array();
        $group_icons['cleverfont'] = array(
            array('cs-font clever-icon-arrow-left' => esc_html__('Arrow Left', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-right' => esc_html__('Arrow Right', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-left-1' => esc_html__('Arrow Left 1', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-right-1' => esc_html__('Arrow Right 1', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-left-2' => esc_html__('Arrow Left 2', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-right-2' => esc_html__('Arrow Right 2', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-left-3' => esc_html__('Arrow Left 3', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-right-3' => esc_html__('Arrow Right 3', 'clever-mega-menu')),
            array('cs-font clever-icon-close-1' => esc_html__('Close 1', 'clever-mega-menu')),
            array('cs-font clever-icon-list-2' => esc_html__('List 2', 'clever-mega-menu')),
            array('cs-font clever-icon-grid-5' => esc_html__('Grid 5', 'clever-mega-menu')),
            array('cs-font clever-icon-menu-6' => esc_html__('Menu 6', 'clever-mega-menu')),
            array('cs-font clever-icon-morevertical' => esc_html__('More Vertical', 'clever-mega-menu')),
            array('cs-font clever-icon-list-1' => esc_html__('List 1', 'clever-mega-menu')),
            array('cs-font clever-icon-menu-5' => esc_html__('Menu 5', 'clever-mega-menu')),
            array('cs-font clever-icon-menu-4' => esc_html__('Menu 4', 'clever-mega-menu')),
            array('cs-font clever-icon-heart-1' => esc_html__('Heart 1', 'clever-mega-menu')),
            array('cs-font clever-icon-user-6' => esc_html__('User 6', 'clever-mega-menu')),
            array('cs-font clever-icon-attachment' => esc_html__('Attachment', 'clever-mega-menu')),
            array('cs-font clever-icon-bag' => esc_html__('Bag', 'clever-mega-menu')),
            array('cs-font clever-icon-ball' => esc_html__('Ball', 'clever-mega-menu')),
            array('cs-font clever-icon-battery' => esc_html__('Battery', 'clever-mega-menu')),
            array('cs-font clever-icon-briefcase' => esc_html__('Briefcase', 'clever-mega-menu')),
            array('cs-font clever-icon-car' => esc_html__('Car', 'clever-mega-menu')),
            array('cs-font clever-icon-cpu-1' => esc_html__('CPU 1', 'clever-mega-menu')),
            array('cs-font clever-icon-cpu-2' => esc_html__('CPU 2', 'clever-mega-menu')),
            array('cs-font clever-icon-dress-woman' => esc_html__('Woman dress', 'clever-mega-menu')),
            array('cs-font clever-icon-drill-tool' => esc_html__('Drill tool', 'clever-mega-menu')),
            array('cs-font clever-icon-feeding-bottle' => esc_html__('Feeding bottle', 'clever-mega-menu')),
            array('cs-font clever-icon-fruit' => esc_html__('Fruit', 'clever-mega-menu')),
            array('cs-font clever-icon-furniture-2' => esc_html__('Furniture 2', 'clever-mega-menu')),
            array('cs-font clever-icon-furniture-1' => esc_html__('Furniture 1', 'clever-mega-menu')),
            array('cs-font clever-icon-shoes-woman-2' => esc_html__('Woman Shoe 2', 'clever-mega-menu')),
            array('cs-font clever-icon-shoes-woman-1' => esc_html__('Woman Shoe 1', 'clever-mega-menu')),
            array('cs-font clever-icon-horse' => esc_html__('Horse', 'clever-mega-menu')),
            array('cs-font clever-icon-laptop' => esc_html__('Laptop', 'clever-mega-menu')),
            array('cs-font clever-icon-lipstick' => esc_html__('Lipstick', 'clever-mega-menu')),
            array('cs-font clever-icon-iron' => esc_html__('Iron', 'clever-mega-menu')),
            array('cs-font clever-icon-perfume' => esc_html__('Iron', 'clever-mega-menu')),
            array('cs-font clever-icon-baby-toy-2' => esc_html__('Baby Toy 2', 'clever-mega-menu')),
            array('cs-font clever-icon-baby-toy-1' => esc_html__('Baby Toy 1', 'clever-mega-menu')),
            array('cs-font clever-icon-paint-roller' => esc_html__('Paint roller', 'clever-mega-menu')),
            array('cs-font clever-icon-shirt' => esc_html__('Shirt', 'clever-mega-menu')),
            array('cs-font clever-icon-shoe-man-2' => esc_html__('Man Shoe 2', 'clever-mega-menu')),
            array('cs-font clever-icon-small-diamond' => esc_html__('Diamond', 'clever-mega-menu')),
            array('cs-font clever-icon-tivi' => esc_html__('TV Screen', 'clever-mega-menu')),
            array('cs-font clever-icon-smartphone' => esc_html__('Smartphone', 'clever-mega-menu')),
            array('cs-font clever-icon-lights' => esc_html__('Led buib', 'clever-mega-menu')),
            array('cs-font clever-icon-microwave' => esc_html__('Microwave', 'clever-mega-menu')),
            array('cs-font clever-icon-wardrobe' => esc_html__('Wardrobe', 'clever-mega-menu')),
            array('cs-font clever-icon-washing-machine' => esc_html__('Washing Machine', 'clever-mega-menu')),
            array('cs-font clever-icon-watch-1' => esc_html__('Watch 1', 'clever-mega-menu')),
            array('cs-font clever-icon-watch-2' => esc_html__('Watch 2', 'clever-mega-menu')),
            array('cs-font clever-icon-slider-3' => esc_html__('Slider 3', 'clever-mega-menu')),
            array('cs-font clever-icon-slider-2' => esc_html__('Slider 2', 'clever-mega-menu')),
            array('cs-font clever-icon-slider-1' => esc_html__('Slider 1', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-15' => esc_html__('Cart 15', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-14' => esc_html__('Cart 14', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-13' => esc_html__('Cart 13', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-12' => esc_html__('Cart 12', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-11' => esc_html__('Cart 11', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-10' => esc_html__('Cart 10', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-9' => esc_html__('Cart 9', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-8' => esc_html__('Cart 8', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-7' => esc_html__('Cart 7', 'clever-mega-menu')),
            array('cs-font clever-icon-online-purchase' => esc_html__('Purchase Cart', 'clever-mega-menu')),
            array('cs-font clever-icon-online-shopping' => esc_html__('ID Card', 'clever-mega-menu')),
            array('cs-font clever-icon-line-triangle2' => esc_html__('Line Triangle 2', 'clever-mega-menu')),
            array('cs-font clever-icon-plane-1' => esc_html__('Airplane', 'clever-mega-menu')),
            array('cs-font clever-icon-bag-black-fashion-model' => esc_html__('Black Fashion Bag', 'clever-mega-menu')),
            array('cs-font clever-icon-funnel-o' => esc_html__('Filter Blank', 'clever-mega-menu')),
            array('cs-font clever-icon-funnel' => esc_html__('Filter', 'clever-mega-menu')),
            array('cs-font clever-icon-grid-1' => esc_html__('Grid 1', 'clever-mega-menu')),
            array('cs-font clever-icon-contract' => esc_html__('Compress', 'clever-mega-menu')),
            array('cs-font clever-icon-expand' => esc_html__('Expand', 'clever-mega-menu')),
            array('cs-font clever-icon-quotes' => esc_html__('Quotes', 'clever-mega-menu')),
            array('cs-font clever-icon-next-arrow-1' => esc_html__('Next Arrow 1', 'clever-mega-menu')),
            array('cs-font clever-icon-prev-arrow-1' => esc_html__('Prev Arrow 1', 'clever-mega-menu')),
            array('cs-font clever-icon-reload' => esc_html__('Reload', 'clever-mega-menu')),
            array('cs-font clever-icon-truck' => esc_html__('Truck', 'clever-mega-menu')),
            array('cs-font clever-icon-wallet' => esc_html__('Wallet', 'clever-mega-menu')),
            array('cs-font clever-icon-electric-1' => esc_html__('Electric 1', 'clever-mega-menu')),
            array('cs-font clever-icon-electric-2' => esc_html__('Electric 2', 'clever-mega-menu')),
            array('cs-font clever-icon-lock' => esc_html__('Lock', 'clever-mega-menu')),
            array('cs-font clever-icon-share-1' => esc_html__('Share 1', 'clever-mega-menu')),
            array('cs-font clever-icon-check-box' => esc_html__('Check box', 'clever-mega-menu')),
            array('cs-font clever-icon-clock' => esc_html__('Clock', 'clever-mega-menu')),
            array('cs-font clever-icon-analytics-laptop' => esc_html__('Analytic Laptop', 'clever-mega-menu')),
            array('cs-font clever-icon-code-design' => esc_html__('Code', 'clever-mega-menu')),
            array('cs-font clever-icon-competitive-chart' => esc_html__('Competitive Chart', 'clever-mega-menu')),
            array('cs-font clever-icon-computer-monitor-and-cellphone' => esc_html__('Computer and Cellphone', 'clever-mega-menu')),
            array('cs-font clever-icon-consulting-message' => esc_html__('Consulting Message', 'clever-mega-menu')),
            array('cs-font clever-icon-creative-process' => esc_html__('Creative Light Buib', 'clever-mega-menu')),
            array('cs-font clever-icon-customer-reviews' => esc_html__('Customer reviews', 'clever-mega-menu')),
            array('cs-font clever-icon-data-visualization' => esc_html__('Compass', 'clever-mega-menu')),
            array('cs-font clever-icon-document-storage' => esc_html__('Folder 1', 'clever-mega-menu')),
            array('cs-font clever-icon-download-arrow' => esc_html__('Download', 'clever-mega-menu')),
            array('cs-font clever-icon-download-cloud' => esc_html__('Cloud Download', 'clever-mega-menu')),
            array('cs-font clever-icon-email-envelope' => esc_html__('Envelope', 'clever-mega-menu')),
            array('cs-font clever-icon-file-sharing' => esc_html__('File Blank', 'clever-mega-menu')),
            array('cs-font clever-icon-finger-touch-screen' => esc_html__('Touch', 'clever-mega-menu')),
            array('cs-font clever-icon-horizontal-tablet-with-pencil' => esc_html__('Tablet with Pencil', 'clever-mega-menu')),
            array('cs-font clever-icon-illustration-tool' => esc_html__('Illustration Tools', 'clever-mega-menu')),
            array('cs-font clever-icon-keyboard-and-hands' => esc_html__('Keyboard and Hands', 'clever-mega-menu')),
            array('cs-font clever-icon-landscape-image' => esc_html__('Lanscape Image', 'clever-mega-menu')),
            array('cs-font clever-icon-layout-squares' => esc_html__('Layout Square', 'clever-mega-menu')),
            array('cs-font clever-icon-mobile-app-developing' => esc_html__('Mobile with Gears', 'clever-mega-menu')),
            array('cs-font clever-icon-online-video' => esc_html__('Video with line', 'clever-mega-menu')),
            array('cs-font clever-icon-optimization-clock' => esc_html__('Performance Clock', 'clever-mega-menu')),
            array('cs-font clever-icon-optimization-clock' => esc_html__('Performance Clock', 'clever-mega-menu')),
            array('cs-font clever-icon-padlock-key' => esc_html__('Padlock', 'clever-mega-menu')),
            array('cs-font clever-icon-pc-monitor' => esc_html__('PC Monitor', 'clever-mega-menu')),
            array('cs-font clever-icon-place-localizer' => esc_html__('Map Icon Blank', 'clever-mega-menu')),
            array('cs-font clever-icon-search-results' => esc_html__('Search Results', 'clever-mega-menu')),
            array('cs-font clever-icon-search-tool' => esc_html__('Search Blank', 'clever-mega-menu')),
            array('cs-font clever-icon-settings-tools' => esc_html__('Toolset', 'clever-mega-menu')),
            array('cs-font clever-icon-sharing-symbol' => esc_html__('Sharing Symbol', 'clever-mega-menu')),
            array('cs-font clever-icon-site-map' => esc_html__('Sitemap', 'clever-mega-menu')),
            array('cs-font clever-icon-smartphone-with-double-arrows' => esc_html__('Smartphone Scale', 'clever-mega-menu')),
            array('cs-font clever-icon-tablet-with-double-arrow' => esc_html__('Tablet Scale', 'clever-mega-menu')),
            array('cs-font clever-icon-thin-expand-arrows' => esc_html__('Expand Thin Arrows', 'clever-mega-menu')),
            array('cs-font clever-icon-upload-information' => esc_html__('Cloud Upload', 'clever-mega-menu')),
            array('cs-font clever-icon-upload-to-web' => esc_html__('Upload', 'clever-mega-menu')),
            array('cs-font clever-icon-volume-off' => esc_html__('Volume Off', 'clever-mega-menu')),
            array('cs-font clever-icon-volume-on' => esc_html__('Volume On', 'clever-mega-menu')),
            array('cs-font clever-icon-web-development' => esc_html__('Config', 'clever-mega-menu')),
            array('cs-font clever-icon-web-home' => esc_html__('Home', 'clever-mega-menu')),
            array('cs-font clever-icon-web-link' => esc_html__('Link', 'clever-mega-menu')),
            array('cs-font clever-icon-web-links' => esc_html__('Links', 'clever-mega-menu')),
            array('cs-font clever-icon-website-protection' => esc_html__('Website Protection', 'clever-mega-menu')),
            array('cs-font clever-icon-work-team' => esc_html__('Work Team', 'clever-mega-menu')),
            array('cs-font clever-icon-zoom-in-symbol' => esc_html__('Zoom In Symbol', 'clever-mega-menu')),
            array('cs-font clever-icon-zoom-out-button' => esc_html__('Zoom Out Button', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-1' => esc_html__('Arrow 1', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-bold' => esc_html__('Arrow Bold', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-light' => esc_html__('Arrow Light', 'clever-mega-menu')),
            array('cs-font clever-icon-arrow-regular' => esc_html__('Arrow Regular', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-1' => esc_html__('Cart 1', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-2' => esc_html__('Cart 2', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-3' => esc_html__('Cart 3', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-4' => esc_html__('Cart 4', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-5' => esc_html__('Cart 5', 'clever-mega-menu')),
            array('cs-font clever-icon-cart-6' => esc_html__('Cart 6', 'clever-mega-menu')),
            array('cs-font clever-icon-chart' => esc_html__('Chart', 'clever-mega-menu')),
            array('cs-font clever-icon-close' => esc_html__('Close', 'clever-mega-menu')),
            array('cs-font clever-icon-compare-1' => esc_html__('Compare 1', 'clever-mega-menu')),
            array('cs-font clever-icon-compare-2' => esc_html__('Compare 2', 'clever-mega-menu')),
            array('cs-font clever-icon-compare-3' => esc_html__('Compare 3', 'clever-mega-menu')),
            array('cs-font clever-icon-compare-4' => esc_html__('Compare 4', 'clever-mega-menu')),
            array('cs-font clever-icon-compare-5' => esc_html__('Compare 5', 'clever-mega-menu')),
            array('cs-font clever-icon-compare-6' => esc_html__('Compare 6', 'clever-mega-menu')),
            array('cs-font clever-icon-compare-7' => esc_html__('Compare 7', 'clever-mega-menu')),
            array('cs-font clever-icon-down' => esc_html__('Down', 'clever-mega-menu')),
            array('cs-font clever-icon-grid' => esc_html__('Grid', 'clever-mega-menu')),
            array('cs-font clever-icon-hand' => esc_html__('Hand', 'clever-mega-menu')),
            array('cs-font clever-icon-layout-1' => esc_html__('Layout 1', 'clever-mega-menu')),
            array('cs-font clever-icon-layout' => esc_html__('Layout', 'clever-mega-menu')),
            array('cs-font clever-icon-light' => esc_html__('Light', 'clever-mega-menu')),
            array('cs-font clever-icon-line-triangle' => esc_html__('Line Triangle', 'clever-mega-menu')),
            array('cs-font clever-icon-list' => esc_html__('List', 'clever-mega-menu')),
            array('cs-font clever-icon-mail-1' => esc_html__('Mail 1', 'clever-mega-menu')),
            array('cs-font clever-icon-mail-2' => esc_html__('Mail 2', 'clever-mega-menu')),
            array('cs-font clever-icon-mail-3' => esc_html__('Mail 3', 'clever-mega-menu')),
            array('cs-font clever-icon-mail-4' => esc_html__('Mail 4', 'clever-mega-menu')),
            array('cs-font clever-icon-mail-5' => esc_html__('Mail 5', 'clever-mega-menu')),
            array('cs-font clever-icon-map-1' => esc_html__('Map 1', 'clever-mega-menu')),
            array('cs-font clever-icon-map-2' => esc_html__('Map 2', 'clever-mega-menu')),
            array('cs-font clever-icon-map-3' => esc_html__('Map 3', 'clever-mega-menu')),
            array('cs-font clever-icon-map-4' => esc_html__('Map 4', 'clever-mega-menu')),
            array('cs-font clever-icon-map-5' => esc_html__('Map 5', 'clever-mega-menu')),
            array('cs-font clever-icon-menu-1' => esc_html__('Menu 1', 'clever-mega-menu')),
            array('cs-font clever-icon-menu-2' => esc_html__('Menu 2', 'clever-mega-menu')),
            array('cs-font clever-icon-grid-3' => esc_html__('Grid 3', 'clever-mega-menu')),
            array('cs-font clever-icon-grid-4' => esc_html__('Grid 4', 'clever-mega-menu')),
            array('cs-font clever-icon-menu-3' => esc_html__('Menu 3', 'clever-mega-menu')),
            array('cs-font clever-icon-grid-2' => esc_html__('Grid 2', 'clever-mega-menu')),
            array('cs-font clever-icon-minus' => esc_html__('Minus', 'clever-mega-menu')),
            array('cs-font clever-icon-next' => esc_html__('Next', 'clever-mega-menu')),
            array('cs-font clever-icon-phone-1' => esc_html__('Phone 1', 'clever-mega-menu')),
            array('cs-font clever-icon-phone-2' => esc_html__('Phone 2', 'clever-mega-menu')),
            array('cs-font clever-icon-phone-3' => esc_html__('Phone 3', 'clever-mega-menu')),
            array('cs-font clever-icon-phone-4' => esc_html__('Phone 4', 'clever-mega-menu')),
            array('cs-font clever-icon-phone-5' => esc_html__('Phone 5', 'clever-mega-menu')),
            array('cs-font clever-icon-phone-6' => esc_html__('Phone 6', 'clever-mega-menu')),
            array('cs-font clever-icon-picture' => esc_html__('Picture', 'clever-mega-menu')),
            array('cs-font clever-icon-pin' => esc_html__('Pin', 'clever-mega-menu')),
            array('cs-font clever-icon-plus' => esc_html__('Plus', 'clever-mega-menu')),
            array('cs-font clever-icon-prev' => esc_html__('Prev', 'clever-mega-menu')),
            array('cs-font clever-icon-quickview-1' => esc_html__('Quickview 1', 'clever-mega-menu')),
            array('cs-font clever-icon-quickview-2' => esc_html__('Quickview 2', 'clever-mega-menu')),
            array('cs-font clever-icon-quickview-3' => esc_html__('Quickview 3', 'clever-mega-menu')),
            array('cs-font clever-icon-quickview-4' => esc_html__('Quickview 4', 'clever-mega-menu')),
            array('cs-font clever-icon-refresh' => esc_html__('Refresh', 'clever-mega-menu')),
            array('cs-font clever-icon-rounded-triangle' => esc_html__('Rounded Triangle', 'clever-mega-menu')),
            array('cs-font clever-icon-search-1' => esc_html__('Search 1', 'clever-mega-menu')),
            array('cs-font clever-icon-search-2' => esc_html__('Search 2', 'clever-mega-menu')),
            array('cs-font clever-icon-search-3' => esc_html__('Search 3', 'clever-mega-menu')),
            array('cs-font clever-icon-search-4' => esc_html__('Search 4', 'clever-mega-menu')),
            array('cs-font clever-icon-search-5' => esc_html__('Search 5', 'clever-mega-menu')),
            array('cs-font clever-icon-support' => esc_html__('Support', 'clever-mega-menu')),
            array('cs-font clever-icon-tablet' => esc_html__('Tablet', 'clever-mega-menu')),
            array('cs-font clever-icon-triangle' => esc_html__('Triangle', 'clever-mega-menu')),
            array('cs-font clever-icon-up' => esc_html__('Up', 'clever-mega-menu')),
            array('cs-font clever-icon-user-1' => esc_html__('User 1', 'clever-mega-menu')),
            array('cs-font clever-icon-user-2' => esc_html__('User 2', 'clever-mega-menu')),
            array('cs-font clever-icon-user-3' => esc_html__('User 3', 'clever-mega-menu')),
            array('cs-font clever-icon-user-4' => esc_html__('User 4', 'clever-mega-menu')),
            array('cs-font clever-icon-user-5' => esc_html__('User 5', 'clever-mega-menu')),
            array('cs-font clever-icon-user' => esc_html__('User', 'clever-mega-menu')),
            array('cs-font clever-icon-vector' => esc_html__('Vector', 'clever-mega-menu')),
            array('cs-font clever-icon-wishlist' => esc_html__('Wishlist', 'clever-mega-menu'))
        );

        if (function_exists('vc_iconpicker_type_fontawesome') && vc_iconpicker_type_fontawesome(array()) != '')
        {
          $group_icons['fontawesome'] = vc_iconpicker_type_fontawesome(array());
        }

        if (function_exists('vc_iconpicker_type_typicons') && vc_iconpicker_type_typicons(array()) != '')
        {
          $group_icons['typicons'] = vc_iconpicker_type_typicons(array());
        }

        if (function_exists('vc_iconpicker_type_material') && vc_iconpicker_type_material(array()) != '')
        {
          $group_icons['material'] = vc_iconpicker_type_material(array());
        }

        $group_icons['dashicons'] = array(
            array('dashicons dashicons-menu' => esc_html__('Navigation Menu', 'clever-mega-menu')),
            array('dashicons dashicons-admin-site' => esc_html__('Admin Site', 'clever-mega-menu')),
            array('dashicons dashicons-dashboard' => esc_html__('Dashboard', 'clever-mega-menu')),
            array('dashicons dashicons-admin-post' => esc_html__('Pin', 'clever-mega-menu')),
            array('dashicons dashicons-admin-media' => esc_html__('Admin Media', 'clever-mega-menu')),
            array('dashicons dashicons-admin-links' => esc_html__('Admin Link', 'clever-mega-menu')),
            array('dashicons dashicons-admin-page' => esc_html__('Admin Page', 'clever-mega-menu')),
            array('dashicons dashicons-admin-comments' => esc_html__('Admin Comment', 'clever-mega-menu')),
            array('dashicons dashicons-admin-appearance' => esc_html__('Admin Appearance', 'clever-mega-menu')),
            array('dashicons dashicons-admin-plugins' => esc_html__('Admin Plugins', 'clever-mega-menu')),
            array('dashicons dashicons-admin-users' => esc_html__('Admin Users', 'clever-mega-menu')),
            array('dashicons dashicons-admin-tools' => esc_html__('Admin Tools', 'clever-mega-menu')),
            array('dashicons dashicons-admin-network' => esc_html__('Admin Lock Key', 'clever-mega-menu')),
            array('dashicons dashicons-admin-home' => esc_html__('Admin Home', 'clever-mega-menu')),
            array('dashicons dashicons-admin-generic' => esc_html__('Admin Gear', 'clever-mega-menu')),
            array('dashicons dashicons-admin-collapse' => esc_html__('Admin Media Button', 'clever-mega-menu')),
            array('dashicons dashicons-filter' => esc_html__('Admin Filter', 'clever-mega-menu')),
            array('dashicons dashicons-admin-customizer' => esc_html__('Admin Customizer', 'clever-mega-menu')),
            array('dashicons dashicons-admin-multisite' => esc_html__('Admin Multisite', 'clever-mega-menu')),
            array('dashicons dashicons-welcome-write-blog' => esc_html__('Write Blog', 'clever-mega-menu')),
            array('dashicons dashicons-welcome-add-page' => esc_html__('Add Page', 'clever-mega-menu')),
            array('dashicons dashicons-welcome-view-site' => esc_html__('View Site', 'clever-mega-menu')),
            array('dashicons dashicons-welcome-widgets-menus' => esc_html__('Widget Menu', 'clever-mega-menu')),
            array('dashicons dashicons-welcome-comments' => esc_html__('No Comments', 'clever-mega-menu')),
            array('dashicons dashicons-welcome-learn-more' => esc_html__('Graduate Cap', 'clever-mega-menu')),
            array('dashicons dashicons-format-aside' => esc_html__('Format Aside', 'clever-mega-menu')),
            array('dashicons dashicons-format-image' => esc_html__('Format Image', 'clever-mega-menu')),
            array('dashicons dashicons-format-status' => esc_html__('Format status', 'clever-mega-menu')),
            array('dashicons dashicons-format-quote' => esc_html__('Format quote', 'clever-mega-menu')),
            array('dashicons dashicons-format-chat' => esc_html__('Format chat', 'clever-mega-menu')),
            array('dashicons dashicons-format-audio' => esc_html__('Format audio', 'clever-mega-menu')),
            array('dashicons dashicons-camera' => esc_html__('camera', 'clever-mega-menu')),
            array('dashicons dashicons-camera' => esc_html__('camera', 'clever-mega-menu')),
            array('dashicons dashicons-images-alt' => esc_html__('images-alt', 'clever-mega-menu')),
            array('dashicons dashicons-images-alt2' => esc_html__('images-alt2', 'clever-mega-menu')),
            array('dashicons dashicons-video-alt' => esc_html__('video-alt', 'clever-mega-menu')),
            array('dashicons dashicons-video-alt2' => esc_html__('video-alt2', 'clever-mega-menu')),
            array('dashicons dashicons-video-alt3' => esc_html__('video-alt3', 'clever-mega-menu')),
            array('dashicons dashicons-media-archive' => esc_html__('media-archive', 'clever-mega-menu')),
            array('dashicons dashicons-media-audio' => esc_html__('media-audio', 'clever-mega-menu')),
            array('dashicons dashicons-media-code' => esc_html__('media-code', 'clever-mega-menu')),
            array('dashicons dashicons-media-default' => esc_html__('media-default', 'clever-mega-menu')),
            array('dashicons dashicons-media-document' => esc_html__('media-document', 'clever-mega-menu')),
            array('dashicons dashicons-media-interactive' => esc_html__('media-interactive', 'clever-mega-menu')),
            array('dashicons dashicons-media-spreadsheet' => esc_html__('media-spreadsheet', 'clever-mega-menu')),
            array('dashicons dashicons-media-text' => esc_html__('media-text', 'clever-mega-menu')),
            array('dashicons dashicons-media-video' => esc_html__('media-video', 'clever-mega-menu')),
            array('dashicons dashicons-playlist-audio' => esc_html__('playlist-audio', 'clever-mega-menu')),
            array('dashicons dashicons-playlist-video' => esc_html__('playlist-video', 'clever-mega-menu')),
            array('dashicons dashicons-controls-play' => esc_html__('controls-play', 'clever-mega-menu')),
            array('dashicons dashicons-controls-pause' => esc_html__('controls-pause', 'clever-mega-menu')),
            array('dashicons dashicons-controls-forward' => esc_html__('controls-forward', 'clever-mega-menu')),
            array('dashicons dashicons-controls-skipforward' => esc_html__('controls-skipforward', 'clever-mega-menu')),
            array('dashicons dashicons-controls-back' => esc_html__('controls-back', 'clever-mega-menu')),
            array('dashicons dashicons-controls-skipback' => esc_html__('controls-skipback', 'clever-mega-menu')),
            array('dashicons dashicons-controls-repeat' => esc_html__('controls-repeat', 'clever-mega-menu')),
            array('dashicons dashicons-controls-volumeon' => esc_html__('controls-volumeon', 'clever-mega-menu')),
            array('dashicons dashicons-controls-volumeoff' => esc_html__('controls-volumeoff', 'clever-mega-menu')),
            array('dashicons dashicons-image-crop' => esc_html__('image-crop', 'clever-mega-menu')),
            array('dashicons dashicons-image-rotate' => esc_html__('image-rotate', 'clever-mega-menu')),
            array('dashicons dashicons-image-rotate-left' => esc_html__('image-rotate-left', 'clever-mega-menu')),
            array('dashicons dashicons-image-rotate-right' => esc_html__('image-rotate-right', 'clever-mega-menu')),
            array('dashicons dashicons-image-flip-vertical' => esc_html__('image-flip-vertical', 'clever-mega-menu')),
            array('dashicons dashicons-image-flip-horizontal' => esc_html__('image-flip-horizontal', 'clever-mega-menu')),
            array('dashicons dashicons-image-filter' => esc_html__('image-filter', 'clever-mega-menu')),
            array('dashicons dashicons-undo' => esc_html__('undo', 'clever-mega-menu')),
            array('dashicons dashicons-redo' => esc_html__('redo', 'clever-mega-menu')),
            array('dashicons dashicons-editor-ul' => esc_html__('editor-ul', 'clever-mega-menu')),
            array('dashicons dashicons-editor-ol' => esc_html__('editor-ol', 'clever-mega-menu')),
            array('dashicons dashicons-editor-quote' => esc_html__('editor-quote', 'clever-mega-menu')),
            array('dashicons dashicons-editor-alignleft' => esc_html__('editor-alignleft', 'clever-mega-menu')),
            array('dashicons dashicons-editor-aligncenter' => esc_html__('editor-aligncenter', 'clever-mega-menu')),
            array('dashicons dashicons-editor-alignright' => esc_html__('editor-alignright', 'clever-mega-menu')),
            array('dashicons dashicons-editor-insertmore' => esc_html__('editor-insertmore', 'clever-mega-menu')),
            array('dashicons dashicons-editor-spellcheck' => esc_html__('editor-spellcheck', 'clever-mega-menu')),
            array('dashicons dashicons-editor-expand' => esc_html__('editor-expand', 'clever-mega-menu')),
            array('dashicons dashicons-editor-contract' => esc_html__('editor-contract', 'clever-mega-menu')),
            array('dashicons dashicons-editor-kitchensink' => esc_html__('editor-kitchensink', 'clever-mega-menu')),
            array('dashicons dashicons-editor-justify' => esc_html__('editor-justify', 'clever-mega-menu')),
            array('dashicons dashicons-editor-paste-word' => esc_html__('editor-paste-word', 'clever-mega-menu')),
            array('dashicons dashicons-editor-paste-text' => esc_html__('editor-paste-text', 'clever-mega-menu')),
            array('dashicons dashicons-editor-removeformatting' => esc_html__('editor-removeformatting', 'clever-mega-menu')),
            array('dashicons dashicons-editor-paste-text' => esc_html__('editor-paste-text', 'clever-mega-menu')),
            array('dashicons dashicons-editor-video' => esc_html__('editor-video', 'clever-mega-menu')),
            array('dashicons dashicons-editor-customchar' => esc_html__('editor-customchar', 'clever-mega-menu')),
            array('dashicons dashicons-editor-outdent' => esc_html__('editor-outdent', 'clever-mega-menu')),
            array('dashicons dashicons-editor-indent' => esc_html__('editor-indent', 'clever-mega-menu')),
            array('dashicons dashicons-editor-help' => esc_html__('editor-help', 'clever-mega-menu')),
            array('dashicons dashicons-editor-indent' => esc_html__('editor-indent', 'clever-mega-menu')),
            array('dashicons dashicons-editor-unlink' => esc_html__('editor-unlink', 'clever-mega-menu')),
            array('dashicons dashicons-editor-rtl' => esc_html__('editor-rtl', 'clever-mega-menu')),
            array('dashicons dashicons-editor-break' => esc_html__('editor-break', 'clever-mega-menu')),
            array('dashicons dashicons-editor-code' => esc_html__('editor-code', 'clever-mega-menu')),
            array('dashicons dashicons-editor-paragraph' => esc_html__('editor-paragraph', 'clever-mega-menu')),
            array('dashicons dashicons-editor-table' => esc_html__('editor-table', 'clever-mega-menu')),
            array('dashicons dashicons-align-left' => esc_html__('align-left', 'clever-mega-menu')),
            array('dashicons dashicons-align-right' => esc_html__('align-right', 'clever-mega-menu')),
            array('dashicons dashicons-align-center' => esc_html__('align-center', 'clever-mega-menu')),
            array('dashicons dashicons-align-none' => esc_html__('align-none', 'clever-mega-menu')),
            array('dashicons dashicons-lock' => esc_html__('lock', 'clever-mega-menu')),
            array('dashicons dashicons-unlock' => esc_html__('unlock', 'clever-mega-menu')),
            array('dashicons dashicons-calendar' => esc_html__('calendar', 'clever-mega-menu')),
            array('dashicons dashicons-calendar-alt' => esc_html__('calendar-alt', 'clever-mega-menu')),
            array('dashicons dashicons-visibility' => esc_html__('visibility', 'clever-mega-menu')),
            array('dashicons dashicons-hidden' => esc_html__('hidden', 'clever-mega-menu')),
            array('dashicons dashicons-post-status' => esc_html__('Pin 1', 'clever-mega-menu')),
            array('dashicons dashicons-edit' => esc_html__('Pencil', 'clever-mega-menu')),
            array('dashicons dashicons-trash' => esc_html__('trash', 'clever-mega-menu')),
            array('dashicons dashicons-sticky' => esc_html__('pin 2', 'clever-mega-menu')),
            array('dashicons dashicons-external' => esc_html__('external', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-up' => esc_html__('arrow-up', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-down' => esc_html__('arrow-down', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-right' => esc_html__('arrow-right', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-left' => esc_html__('arrow-left', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-up-alt' => esc_html__('arrow-up 1', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-down-alt' => esc_html__('arrow-down 1', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-right-alt' => esc_html__('arrow-right 1', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-left-alt' => esc_html__('arrow-left 1', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-up-alt2' => esc_html__('arrow-up 2', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-down-alt2' => esc_html__('arrow-down 2', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-right-alt2' => esc_html__('arrow-right 2', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-left-alt2' => esc_html__('arrow-left 2', 'clever-mega-menu')),
            array('dashicons dashicons-arrow-left-alt2' => esc_html__('arrow-left 2', 'clever-mega-menu')),
            array('dashicons dashicons-sort' => esc_html__('sort', 'clever-mega-menu')),
            array('dashicons dashicons-leftright' => esc_html__('leftright', 'clever-mega-menu')),
            array('dashicons dashicons-randomize' => esc_html__('randomize', 'clever-mega-menu')),
            array('dashicons dashicons-list-view' => esc_html__('list-view', 'clever-mega-menu')),
            array('dashicons dashicons-exerpt-view' => esc_html__('exerpt-view', 'clever-mega-menu')),
            array('dashicons dashicons-grid-view' => esc_html__('grid-view', 'clever-mega-menu')),
            array('dashicons dashicons-move' => esc_html__('move', 'clever-mega-menu')),
            array('dashicons dashicons-share' => esc_html__('share', 'clever-mega-menu')),
            array('dashicons dashicons-share-alt' => esc_html__('share-alt', 'clever-mega-menu')),
            array('dashicons dashicons-share-alt2' => esc_html__('share-alt2', 'clever-mega-menu')),
            array('dashicons dashicons-twitter' => esc_html__('twitter', 'clever-mega-menu')),
            array('dashicons dashicons-rss' => esc_html__('rss', 'clever-mega-menu')),
            array('dashicons dashicons-email' => esc_html__('email', 'clever-mega-menu')),
            array('dashicons dashicons-email-alt' => esc_html__('email-alt', 'clever-mega-menu')),
            array('dashicons dashicons-facebook' => esc_html__('facebook', 'clever-mega-menu')),
            array('dashicons dashicons-facebook-alt' => esc_html__('facebook-alt', 'clever-mega-menu')),
            array('dashicons dashicons-googleplus' => esc_html__('googleplus', 'clever-mega-menu')),
            array('dashicons dashicons-networking' => esc_html__('networking', 'clever-mega-menu')),
            array('dashicons dashicons-hammer' => esc_html__('hammer', 'clever-mega-menu')),
            array('dashicons dashicons-art' => esc_html__('art', 'clever-mega-menu')),
            array('dashicons dashicons-migrate' => esc_html__('migrate', 'clever-mega-menu')),
            array('dashicons dashicons-performance' => esc_html__('performance', 'clever-mega-menu')),
            array('dashicons dashicons-universal-access' => esc_html__('universal-access', 'clever-mega-menu')),
            array('dashicons dashicons-universal-access-alt' => esc_html__('universal-access-alt', 'clever-mega-menu')),
            array('dashicons dashicons-tickets' => esc_html__('tickets', 'clever-mega-menu')),
            array('dashicons dashicons-nametag' => esc_html__('nametag', 'clever-mega-menu')),
            array('dashicons dashicons-clipboard' => esc_html__('clipboard', 'clever-mega-menu')),
            array('dashicons dashicons-heart' => esc_html__('heart', 'clever-mega-menu')),
            array('dashicons dashicons-megaphone' => esc_html__('megaphone', 'clever-mega-menu')),
            array('dashicons dashicons-schedule' => esc_html__('schedule', 'clever-mega-menu')),
            array('dashicons dashicons-wordpress' => esc_html__('wordpress', 'clever-mega-menu')),
            array('dashicons dashicons-wordpress-alt' => esc_html__('wordpress-alt', 'clever-mega-menu')),
            array('dashicons dashicons-pressthis' => esc_html__('pressthis', 'clever-mega-menu')),
            array('dashicons dashicons-update' => esc_html__('update', 'clever-mega-menu')),
            array('dashicons dashicons-screenoptions' => esc_html__('screenoptions', 'clever-mega-menu')),
            array('dashicons dashicons-info' => esc_html__('info', 'clever-mega-menu')),
            array('dashicons dashicons-cart' => esc_html__('cart', 'clever-mega-menu')),
            array('dashicons dashicons-feedback' => esc_html__('feedback', 'clever-mega-menu')),
            array('dashicons dashicons-cloud' => esc_html__('cloud', 'clever-mega-menu')),
            array('dashicons dashicons-translation' => esc_html__('translation', 'clever-mega-menu')),
            array('dashicons dashicons-tag' => esc_html__('tag', 'clever-mega-menu')),
            array('dashicons dashicons-category' => esc_html__('category', 'clever-mega-menu')),
            array('dashicons dashicons-archive' => esc_html__('archive', 'clever-mega-menu')),
            array('dashicons dashicons-tagcloud' => esc_html__('tagcloud', 'clever-mega-menu')),
            array('dashicons dashicons-text' => esc_html__('text', 'clever-mega-menu')),
            array('dashicons dashicons-yes' => esc_html__('yes', 'clever-mega-menu')),
            array('dashicons dashicons-no' => esc_html__('no', 'clever-mega-menu')),
            array('dashicons dashicons-no-alt' => esc_html__('no-alt', 'clever-mega-menu')),
            array('dashicons dashicons-plus' => esc_html__('plus', 'clever-mega-menu')),
            array('dashicons dashicons-plus-alt' => esc_html__('plus-alt', 'clever-mega-menu')),
            array('dashicons dashicons-plus-alt' => esc_html__('plus-alt', 'clever-mega-menu')),
            array('dashicons dashicons-minus' => esc_html__('minus', 'clever-mega-menu')),
            array('dashicons dashicons-dismiss' => esc_html__('dismiss', 'clever-mega-menu')),
            array('dashicons dashicons-marker' => esc_html__('marker', 'clever-mega-menu')),
            array('dashicons dashicons-star-filled' => esc_html__('star-filled', 'clever-mega-menu')),
            array('dashicons dashicons-star-half' => esc_html__('star-half', 'clever-mega-menu')),
            array('dashicons dashicons-star-empty' => esc_html__('star-empty', 'clever-mega-menu')),
            array('dashicons dashicons-flag' => esc_html__('flag', 'clever-mega-menu')),
            array('dashicons dashicons-warning' => esc_html__('warning', 'clever-mega-menu')),
            array('dashicons dashicons-location' => esc_html__('location', 'clever-mega-menu')),
            array('dashicons dashicons-location-alt' => esc_html__('location-alt', 'clever-mega-menu')),
            array('dashicons dashicons-vault' => esc_html__('vault', 'clever-mega-menu')),
            array('dashicons dashicons-shield' => esc_html__('shield', 'clever-mega-menu')),
            array('dashicons dashicons-shield-alt' => esc_html__('shield-alt', 'clever-mega-menu')),
            array('dashicons dashicons-sos' => esc_html__('sos', 'clever-mega-menu')),
            array('dashicons dashicons-search' => esc_html__('search', 'clever-mega-menu')),
            array('dashicons dashicons-slides' => esc_html__('slides', 'clever-mega-menu')),
            array('dashicons dashicons-analytics' => esc_html__('analytics', 'clever-mega-menu')),
            array('dashicons dashicons-chart-pie' => esc_html__('chart-pie', 'clever-mega-menu')),
            array('dashicons dashicons-chart-bar' => esc_html__('chart-bar', 'clever-mega-menu')),
            array('dashicons dashicons-chart-line' => esc_html__('chart-line', 'clever-mega-menu')),
            array('dashicons dashicons-chart-area' => esc_html__('chart-area', 'clever-mega-menu')),
            array('dashicons dashicons-groups' => esc_html__('groups', 'clever-mega-menu')),
            array('dashicons dashicons-businessman' => esc_html__('businessman', 'clever-mega-menu')),
            array('dashicons dashicons-id' => esc_html__('id card', 'clever-mega-menu')),
            array('dashicons dashicons-id-alt' => esc_html__('id card-alt', 'clever-mega-menu')),
            array('dashicons dashicons-awards' => esc_html__('awards', 'clever-mega-menu')),
            array('dashicons dashicons-forms' => esc_html__('forms', 'clever-mega-menu')),
            array('dashicons dashicons-portfolio' => esc_html__('portfolio', 'clever-mega-menu')),
            array('dashicons dashicons-book' => esc_html__('book', 'clever-mega-menu')),
            array('dashicons dashicons-book-alt' => esc_html__('book-alt', 'clever-mega-menu')),
            array('dashicons dashicons-download' => esc_html__('download', 'clever-mega-menu')),
            array('dashicons dashicons-upload' => esc_html__('upload', 'clever-mega-menu')),
            array('dashicons dashicons-backup' => esc_html__('backup', 'clever-mega-menu')),
            array('dashicons dashicons-clock' => esc_html__('clock', 'clever-mega-menu')),
            array('dashicons dashicons-lightbulb' => esc_html__('lightbulb', 'clever-mega-menu')),
            array('dashicons dashicons-microphone' => esc_html__('microphone', 'clever-mega-menu')),
            array('dashicons dashicons-desktop' => esc_html__('desktop', 'clever-mega-menu')),
            array('dashicons dashicons-laptop' => esc_html__('laptop', 'clever-mega-menu')),
            array('dashicons dashicons-tablet' => esc_html__('tablet', 'clever-mega-menu')),
            array('dashicons dashicons-smartphone' => esc_html__('smartphone', 'clever-mega-menu')),
            array('dashicons dashicons-phone' => esc_html__('phone', 'clever-mega-menu')),
            array('dashicons dashicons-index-card' => esc_html__('index-card', 'clever-mega-menu')),
            array('dashicons dashicons-carrot' => esc_html__('carrot', 'clever-mega-menu')),
            array('dashicons dashicons-building' => esc_html__('building', 'clever-mega-menu')),
            array('dashicons dashicons-store' => esc_html__('store', 'clever-mega-menu')),
            array('dashicons dashicons-album' => esc_html__('album', 'clever-mega-menu')),
            array('dashicons dashicons-palmtree' => esc_html__('palmtree', 'clever-mega-menu')),
            array('dashicons dashicons-tickets-alt' => esc_html__('tickets-alt', 'clever-mega-menu')),
            array('dashicons dashicons-money' => esc_html__('money', 'clever-mega-menu')),
            array('dashicons dashicons-thumbs-up' => esc_html__('thumbs-up', 'clever-mega-menu')),
            array('dashicons dashicons-thumbs-down' => esc_html__('thumbs-down', 'clever-mega-menu')),
            array('dashicons dashicons-layout' => esc_html__('layout', 'clever-mega-menu')),
            array('dashicons dashicons-paperclip' => esc_html__('paperclip', 'clever-mega-menu')),
        );

        ob_start();

        $i = 0;

        ?><div class="clever-mega-menu-icons-tabs">
            <a class="clever-mega-menu-icons-tab active" href="#" data-tab="clever-mega-menu-dashicons-icons"><?php esc_html_e('Dashicons', 'clever-mega-menu'); ?></a>
            <a class="clever-mega-menu-icons-tab" href="#" data-tab="clever-mega-menu-cleverfont-icons"><?php esc_html_e('CleverFont', 'clever-mega-menu'); ?></a>
            <?php if (function_exists('vc_iconpicker_type_fontawesome') && vc_iconpicker_type_fontawesome(array()) != '') : ?>
            <a class="clever-mega-menu-icons-tab" href="#" data-tab="clever-mega-menu-fontawesome-icons"><?php esc_html_e('FontAwesome', 'clever-mega-menu'); ?></a>
            <?php endif; ?>
            <?php if (function_exists('vc_iconpicker_type_typicons') && vc_iconpicker_type_typicons(array()) != '') : ?>
              <a class="clever-mega-menu-icons-tab" href="#" data-tab="clever-mega-menu-typicons-icons"><?php esc_html_e('Typicons', 'clever-mega-menu'); ?></a>
            <?php endif; ?>
            <?php if (function_exists('vc_iconpicker_type_material') && vc_iconpicker_type_material(array()) != '') : ?>
              <a class="clever-mega-menu-icons-tab" href="#" data-tab="clever-mega-menu-material-icons"><?php esc_html_e('Material', 'clever-mega-menu'); ?></a>
            <?php endif; ?>
        </div><?php
        foreach($group_icons as $gk => $all_icons) :
            ?><div id="clever-mega-menu-<?php echo sanitize_title($gk) ?>-icons" class="clever-mega-menu-icons-tab-content"><?php
                foreach ($all_icons as $k => $icons) :
                    if (is_array($icons)) :
                        foreach ($icons as $k2 => $icons2) :
                            if (is_string($icons2)) : $i++ ;
                                ?><span class="fip-box lv-2" data-icon-type="<?php echo esc_attr($gk); ?>" data-value="<?php echo esc_attr($k2); ?>" title="<?php echo esc_attr($icons2); ?>"><i class="<?php echo esc_attr($k2); ?>"></i></span><?php
                            else :
                                foreach ($icons2 as $k3 => $icons3) :
                                    if (is_string($icons3)) : $i ++ ;
                                        ?><span class="fip-box lv-3" data-icon-type="<?php echo esc_attr($gk); ?>" data-value="<?php echo esc_attr($k3); ?>" title="<?php echo esc_attr($icons3); ?>"><i class="<?php echo esc_attr($k3); ?>"></i></span><?php
                                    endif;
                                endforeach;
                            endif;
                        endforeach;
                    else : $i ++ ;
                        ?><span class="fip-box" data-icon-type="<?php echo esc_attr($gk); ?>"  data-value="<?php echo esc_attr($k2); ?>" title="<?php echo esc_attr($icons); ?>"><i class="<?php echo esc_attr($k); ?>"></i></span><?php
                    endif;
                endforeach;
            ?></div><?php
        endforeach;

        $html = ob_get_clean();

        return $html;
    }

    /**
     * Sanitize metadata
     */
    private function sanitize($data)
    {
        $settings = array();

        $roles = wp_roles()->roles;

        foreach ($roles as $key => $value) {
            $role = 'role_'.$key;
            $settings[$role] = !empty($data[$role]) ? $data[$role] : '0';
        }

        $settings['hide_title'] = !empty($data['hide_title']) ? $data['hide_title'] : '0';
        $settings['disable_link'] = !empty($data['disable_link']) ? $data['disable_link'] : '0';
        $settings['hide_on_mobile'] = !empty($data['hide_on_mobile']) ? $data['hide_on_mobile'] : '0';
        $settings['hide_on_desktop'] = !empty($data['hide_on_desktop']) ? $data['hide_on_desktop'] : '0';
        $settings['hide_sub_item_on_mobile'] = !empty($data['hide_sub_item_on_mobile']) ? $data['hide_sub_item_on_mobile'] : '0';
        $settings['role_anyone'] = !empty($data['role_anyone']) ? $data['role_anyone'] : '0';
        $settings['layout'] = !empty($data['layout']) ? $data['layout'] : 'full';
        $settings['width'] = !empty($data['width']) ? intval($data['width']) : '';
        $settings['mega_panel_img_position_top'] = !empty($data['mega_panel_img_position_top']) ? $this->get_px_value($data['mega_panel_img_position_top']) : '';
        $settings['mega_panel_img_position_right'] = !empty($data['mega_panel_img_position_right']) ? $this->get_px_value($data['mega_panel_img_position_right']) : '';
        $settings['mega_panel_img_position_bottom'] = !empty($data['mega_panel_img_position_bottom']) ? $this->get_px_value($data['mega_panel_img_position_bottom']) : '';
        $settings['mega_panel_img_position_left'] = !empty($data['mega_panel_img_position_left']) ? $this->get_px_value($data['mega_panel_img_position_left']) : '';
        $settings['mega_panel_img_src'] = !empty($data['mega_panel_img_src']) ? $data['mega_panel_img_src'] : '';
        $settings['mega_panel_img_id'] = !empty($data['mega_panel_img_id']) ? intval($data['mega_panel_img_id']) : 0;
        $settings['mega_panel_img_size'] = !empty($data['mega_panel_img_size']) ? $data['mega_panel_img_size'] : 'full';
        $settings['class'] = !empty($data['class']) ? $data['class'] : '';
        $settings['icon'] = !empty($data['icon']) ? $data['icon'] : '';
        $settings['enable'] = !empty($data['enable']) ? $data['enable'] : '0';

        return $settings;
    }

    /**
	 * Parse shortcodes custom css string.
	 *
	 * This function is used by self::buildShortcodesCustomCss and creates css string from shortcodes attributes
	 * like 'css_editor'.
	 *
	 * @see    WPBakeryVisualComposerCssEditor
	 * @since  4.2
	 * @access public
	 *
	 * @param $content
	 *
	 * @return string
	 */
    private function parse_shortcodes_css($content)
    {
        $css = '';

		if (!preg_match('/\s*(\.[^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $content)) {
			return $css;
        }

		WPBMap::addAllMappedShortcodes();

		preg_match_all('/' . get_shortcode_regex() . '/', $content, $shortcodes);

		foreach ($shortcodes[2] as $index => $tag) {
			$shortcode = WPBMap::getShortCode($tag);
			if (!empty($shortcode['params']) && is_array($shortcode['params'])) {
                $attr_array = shortcode_parse_atts(trim($shortcodes[3][$index]));
				foreach ($shortcode['params'] as $param) {
					if (isset($param['type']) && 'css_editor' === $param['type'] && isset($attr_array[$param['param_name']])) {
						$css .= $attr_array[$param['param_name']];
					}
				}
			}
		}

		foreach ($shortcodes[5] as $shortcode_content) {
			$css .= $this->parse_shortcodes_css($shortcode_content);
		}

		return $css;
    }

    /**
     * Get value in px unit
     */
    private function get_px_value($value)
    {
        return is_numeric($value) ? $value . 'px' : $value;
    }
}
