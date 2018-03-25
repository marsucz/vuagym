<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 24/12/15
 * Time: 10:20 AM
 */
if(!class_exists('S7upf_Attribute_Filter') && class_exists("woocommerce"))
{
    class S7upf_Attribute_Filter extends WP_Widget {


        protected $default=array();

        static function _init()
        {
            add_action( 'widgets_init', array(__CLASS__,'_add_widget') );
        }

        static function _add_widget()
        {
            register_widget( 'S7upf_Attribute_Filter' );
        }

        function __construct() {
            // Instantiate the parent object
            parent::__construct( false, esc_html__('Attribute Filter','kuteshop'),
                array( 'description' => esc_html__( 'Filter product shop page', 'kuteshop' ), ));

            $this->default=array(
                'title' => '',
                'attribute' => '',
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
                echo    '<ul class="list-filter color-filter">';                
                $terms = get_terms("pa_".$attribute);
                $term_current = '';
                if(isset($_GET['pa_'.$attribute])) $term_current = $_GET['pa_'.$attribute];
                if($term_current != '') $term_current = explode(',', $term_current);
                else $term_current = array();  
                if(is_array($terms)){
                    foreach ($terms as $term) {
                        if(is_object($term)){
                            if(in_array($term->slug, $term_current)) $active = 'active';
                            else $active = '';
                            echo    '<li><a data-attribute="'.esc_attr($attribute).'" data-term="'.esc_attr($term->slug).'" class="load-shop-ajax '.esc_attr($active).' bgcolor-'.esc_attr($term->slug).'" href="'.esc_url(s7upf_get_filter_url('pa_'.$attribute,$term->slug)).'"><span></span>'.$term->name.'</a></li>';
                        }
                    }
                }
                echo    '</ul>';           
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
            ?>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'title' )); ?>"><?php esc_html_e( 'Title:' ,'kuteshop'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'title' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'title' )); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
            </p>            
            <p>
                <label><?php esc_html_e( 'Attribute:' ,'kuteshop'); ?></label></br>
                <select id="<?php echo esc_attr($this->get_field_id( 'attribute' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'attribute' )); ?>">
                    <?php 
                    global $wpdb;
                    $attribute_taxonomies = wc_get_attribute_taxonomies();
                    if(!empty($attribute_taxonomies)){
                        foreach($attribute_taxonomies as $attr){
                            $selected=selected($attr->attribute_name,$attribute,false);
                            echo "<option {$selected} value='{$attr->attribute_name}'>{$attr->attribute_label}</option>";
                        }
                    }
                    else echo esc_html__("No any attribute.","kuteshop");
                    ?>
                </select>                
            </p>
        <?php
        }
    }

    S7upf_Attribute_Filter::_init();

}
