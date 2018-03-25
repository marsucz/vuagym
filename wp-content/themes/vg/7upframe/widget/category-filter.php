<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 24/12/15
 * Time: 10:20 AM
 */
if(!class_exists('SV_Category_Fillter') && class_exists("woocommerce"))
{
    class SV_Category_Fillter extends WP_Widget {


        protected $default=array();

        static function _init()
        {
            add_action( 'widgets_init', array(__CLASS__,'_add_widget') );
        }

        static function _add_widget()
        {
            register_widget( 'SV_Category_Fillter' );
        }

        function __construct() {
            // Instantiate the parent object
            parent::__construct( false, esc_html__('Categories Filter','kuteshop'),
                array( 'description' => esc_html__( 'Filter product shop page', 'kuteshop' ), ));

            $this->default=array(
                'title' => '',
                'category' => array(),
            );
        }



        function widget( $args, $instance ) {
            // Widget output
            if(!is_single()){
                echo balancetags($args['before_widget']);
                if ( ! empty( $instance['title'] ) ) {
                   echo balancetags($args['before_title']) . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
                }

                $instance=wp_parse_args($instance,$this->default);
                extract($instance);
                if(is_object($category)) $category = json_decode(json_encode($category), true);
                if(is_array($category) && !empty($category)){
                    echo        '<ul>';                
                        $cat_current = '';
                        if(isset($_GET['product_cat'])) $cat_current = $_GET['product_cat'];
                        if($cat_current != '') $cat_current = explode(',', $cat_current);
                        else $cat_current = array();
                        foreach ($category as $cat_slug) {
                            $cat = get_term_by('slug',$cat_slug,'product_cat');
                            if(is_object($cat)){
                                if(in_array($cat->slug, $cat_current)) $active = 'active';
                                else $active = '';
                                echo        '<li><a data-cat='.esc_attr($cat->slug).' href="'.esc_url(s7upf_get_filter_url('product_cat',$cat->slug)).'" class="load-shop-ajax '.$active.'"> '.$cat->name.'</a></li>';
                            }
                        }
                    echo      '</ul>';
                }      
                echo balancetags($args['after_widget']);
            }
        }

        function update( $new_instance, $old_instance ) {

            // Save widget options
            $instance=array();
            $instance=wp_parse_args($instance,$this->default);
            $new_instance=wp_parse_args($new_instance,$instance);

            return $new_instance;
        }

        function form( $instance ) {
            // Output admin widget options form

            $instance=wp_parse_args($instance,$this->default);
            extract($instance);
            if(is_object($category)) $category = json_decode(json_encode($category), true);
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:' ,'kuteshop'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>
            <p>
                <label><?php esc_html_e( 'Categories:' ,'kuteshop'); ?></label></br>
                <?php 
                $cats = get_terms('product_cat');
                if(is_array($cats) && !empty($cats)){
                    foreach ($cats as $cat) {
                        if(in_array($cat->slug, $category)) $checked = 'checked="checked"';
                        else $checked = '';
                        echo '<input '.$checked.' id="'.esc_attr($this->get_field_id( 'category' )).'" type="checkbox" name="'.esc_attr($this->get_field_name( 'category' )).'[]" value="'.$cat->slug.'"><span>'.$cat->name.'</span>';
                    }
                }
                else echo esc_html__("No any category.","kuteshop");
                ?>
            </p>            
        <?php
        }
    }

    SV_Category_Fillter::_init();

}
