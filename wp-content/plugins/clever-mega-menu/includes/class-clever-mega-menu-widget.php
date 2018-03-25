<?php
/**
 * Clever_Mega_Menu_Widget
 *
 * @package    Clever_Mega_Menu
 */
final class Clever_Mega_Menu_Widget extends WP_Widget
{
	/**
	 * Register widget with WordPress.
	 */
    function __construct()
    {
		parent::__construct(
			'clever_mega_menu',
			'Clever Mega Menu',
			array('description' => esc_html__('Display a navigation menu by choosing a menu location.', 'clever-mega-menu'))
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see    WP_Widget::widget()
     *
	 * @param    array    $args        Widget arguments.
	 * @param    array    $instance    Saved values from database.
	 */
	function widget($args, $instance)
    {
		if (isset($instance['location'])) {
			$location = $instance['location'];
			echo $args['before_widget'];
			if (!empty($instance['title'])) {
				echo $args['before_title'] . $instance['title'] . $args['after_title'];
			}
	        if (has_nav_menu($location)) {
                wp_nav_menu(array('theme_location' => $location));
			}
			echo $args['after_widget'];
		}
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see    WP_Widget::update()
     *
	 * @param    array    $new_instance    Values just sent to be saved.
	 * @param    array    $old_instance    Previously saved values from database.
     *
	 * @return array Updated safe values to be saved.
	 */
	function update($new_instance, $old_instance)
    {
		$instance = array();
		$instance['location'] = sanitize_text_field($new_instance['location']);
		$instance['title'] = sanitize_text_field($new_instance['title']);

		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see    WP_Widget::form()
     *
	 * @param    array    $instance    Previously saved values from database.
	 */
	function form($instance)
    {
		$menus = get_registered_nav_menus();
		$title = !empty($instance['title']) ? $instance['title'] : '';
		$selected = !empty($instance['location']) ? $instance['location'] : '';

		?><p><?php
            if ($menus) :
                ?><p>
					<label for="<?php echo $this->get_field_id('title'); ?>"><?php esc_html_e('Title:', 'clever-mega-menu'); ?></label>
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
				</p>
				<label for="<?php echo $this->get_field_id('location'); ?>"><?php esc_html_e('Menu Location', 'clever-mega-menu'); ?>:</label>
				<select id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>"><?php
					foreach ($menus as $menu_location => $description) :
                        ?><option value="<?php echo $menu_location ?>"<?php selected($menu_location, $selected) ?>><?php echo $description ?></option><?php
					endforeach;
				?></select><?php
            else :
                esc_html_e('No menu locations found.', 'clever-mega-menu');
            endif; ?>
		</p><?php
	}
}
