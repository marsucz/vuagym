<?php
/**
 * Clever_Mega_Menu_Location_Post_Type
 *
 * @package    Clever_Mega_Menu
 */
final class Clever_Mega_Menu_Location_Post_Type
{
    /**
     * Post type
     *
     * @var    object
     *
     * @see    https://developer.wordpress.org/reference/functions/register_post_type/
     */
    public $post_type;

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
     * Register portfolio post type
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     */
    function _register()
    {
        $labels = array(
            'name'          => esc_html__('Menu Locations', 'clever-mega-menu'),
            'singular_name' => esc_html__('Menu Location', 'clever-mega-menu'),
            'all_items'     => esc_html__('Menu Locations', 'clever-mega-menu'),
            'add_new'       => esc_html__('Add New Menu Location', 'clever-mega-menu'),
            'add_new_item'  => esc_html__('Add New Menu Location', 'clever-mega-menu'),
            'edit_item'     => esc_html__('Edit Menu Location', 'clever-mega-menu')
        );

        $args = array(
            'labels'        => $labels,
            'public'        => false,
            'show_ui'       => true,
            'show_in_menu'  => 'class-clever-mega-menu-dashboard.php',
            'supports'      => array('title')
        );

        $this->post_type = register_post_type('clever_menu_location', $args);
    }

    /**
     * Do notification
     *
     * @internal  Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @see  https://developer.wordpress.org/reference/hooks/post_updated_messages/
     */
    function _notify($messages)
    {
        $messages['clever_menu_location'] = array(
            0  => '', // Unused. Messages start at index 1.
            1  => esc_html__('Menu location updated.', 'clever-mega-menu'),
            2  => esc_html__('Custom field updated.', 'clever-mega-menu'),
            3  => esc_html__('Custom field deleted.', 'clever-mega-menu'),
            4  => esc_html__('Menu location updated.', 'clever-mega-menu'),
            5  => isset($_GET['revision']) ? esc_html__('Menu location restored to revision from', 'clever-mega-menu') . ' ' . wp_post_revision_title(absint($_GET['revision'])) : false,
            6  => esc_html__('Menu location published.', 'clever-mega-menu'),
            7  => esc_html__('Menu location saved.', 'clever-mega-menu'),
            8  => esc_html__('Menu location submitted.', 'clever-mega-menu'),
            9  => esc_html__('Menu location scheduled.', 'clever-mega-menu'),
            10 => esc_html__('Menu location draft updated.', 'clever-mega-menu')
        );

        return $messages;
    }

    /**
     * Disable quick edit
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @see    https://developer.wordpress.org/reference/hooks/post_row_actions-5/
     */
    function _remove_quick_edit($actions, WP_Post $post)
    {
        if ('clever_menu_location' !== $post->post_type) {
            return $actions;
        }

        unset($actions['inline hide-if-no-js']);

        return $actions;
    }

    /**
     * Add shortcode column on posts list table
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @param    array    $columns    Default columns.
     *
     * @return    array    $columns    New columns.
     */
    function _add_shortcode_column($columns)
    {
        $date = $columns['date'];

        unset( $columns['date'] );

        $columns['shortcode'] = esc_html__('Shortcode', 'clever-mega-menu');

        $columns['date'] = $date;

        return $columns;
    }

    /**
     * Render shortcode
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @see    https://developer.wordpress.org/reference/functions/add_shortcode/
     */
    function _render_shortcode($atts)
    {
        if (!isset($atts['loc']))
            return false;

        if (has_nav_menu($atts['loc']))
            return wp_nav_menu(array('theme_location' => $atts['loc']));

        return sprintf(esc_html__('No menu found for the "%s" menu location', 'clever-mega-menu'), $atts['loc']);
    }

    /**
     * Get shortcode column content
     *
     * @internal    Used as a callback. PLEASE DO NOT RECALL THIS METHOD DIRECTLY!
     *
     * @param    string    $col        Column slug.
     * @param    int       $post_id    Current post's ID.
     */
    function _the_shortcode_cell_content($col, $post_id)
    {
        $post = get_post($post_id);

        echo '[clevermegamenu loc="' . $post->post_name . '"]';
    }
}
