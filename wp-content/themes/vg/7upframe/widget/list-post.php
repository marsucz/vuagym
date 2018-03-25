<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:20 AM
 */
if(!class_exists('S7upf_ListPostsWidget'))
{
    class S7upf_ListPostsWidget extends WP_Widget {


        protected $default=array();

        static function _init()
        {
            add_action( 'widgets_init', array(__CLASS__,'_add_widget') );
        }

        static function _add_widget()
        {
            register_widget( 'S7upf_ListPostsWidget' );
        }

        function __construct() {
            // Instantiate the parent object
            parent::__construct( false, esc_html__('SV List Posts','kuteshop'),
                array( 'description' => esc_html__( 'Lists Posts', 'kuteshop' ), ));

            $this->default=array(
                'title'             =>esc_html__('List Posts','kuteshop'),
                'posts_per_page'    =>5,
                'category'          =>'',
                'order'             =>'desc',
                'order_by'          =>'date',
            );
        }



        function widget( $args, $instance ) {
            // Widget output
           echo balancetags($args['before_widget']);
            if ( ! empty( $instance['title'] ) ) {
               echo balancetags($args['before_title']) . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
            }

            $instance=wp_parse_args($instance,$this->default);
            extract($instance);
            $args_post = array(
                'post_type'         => 'post',
                'posts_per_page'    => $posts_per_page,
                'orderby'           => $order_by,
                'order'             => $order,
            );
            if(!empty($category)){
                $args_post['tax_query'][]=array(
                    'taxonomy'=>'category',
                    'field'=>'id',
                    'terms'=> $category
                );
            }
            $html = '';
            $html .=    '<div class="widget-recent-post">
                            <ul class="list-none">';
            $post_query = new WP_Query($args_post);
            if($post_query->have_posts()) {
                while($post_query->have_posts()) {
                    $post_query->the_post();
                    $html .=    '<li>
                                    <div class="post-thumb">
                                        <a href="'.esc_url(get_the_permalink()).'" class="post-thumb-link">
                                            '.get_the_post_thumbnail(get_the_ID(),array(70,70)).'
                                        </a>
                                    </div>
                                    <div class="post-info">
                                        <h3 class="post-title"><a href="'.esc_url(get_the_permalink()).'" title="'.esc_attr(get_the_title()).'">'.get_the_title().'</a></h3>
                                        <ul class="post-date-comment">
                                            <li><i class="fa fa-clock-o" aria-hidden="true"></i><span>'.get_the_date('M.d. Y').'</span></li>
                                            <li><i class="fa fa-comment" aria-hidden="true"></i><a href="'.esc_url( get_comments_link() ).'">'.get_comments_number().' '.esc_html__("Comments","kuteshop").' </a></li>
                                        </ul>
                                    </div>
                                </li>';
                }
            }
            $html .=        '</ul>
                        </div>';
            wp_reset_postdata();
            echo balancetags($html);
            echo balancetags($args['after_widget']);
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
                <label for="<?php echo esc_attr($this->get_field_id( 'posts_per_page' )); ?>"><?php esc_html_e( 'Post Number:' ,'kuteshop'); ?></label>
                <input class="widefat" id="<?php echo esc_attr($this->get_field_id( 'posts_per_page' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'posts_per_page' )); ?>" type="text" value="<?php echo esc_attr( $posts_per_page ); ?>">
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'order_by' )); ?>"><?php esc_html_e( 'Order By:' ,'kuteshop'); ?></label>

                <select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'order_by' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'order_by' )); ?>">
                    <?php echo s7upf_get_order_list($order_by,array('post_view'=>esc_html__('Post View','kuteshop')),'option');?>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'order' )); ?>"><?php esc_html_e( 'Order:' ,'kuteshop'); ?></label>

                <select class="widefat" id="<?php echo esc_attr($this->get_field_id( 'order' )); ?>" name="<?php echo esc_attr($this->get_field_name( 'order' )); ?>">
                    <option <?php selected('desc',$order) ?> value="desc"><?php esc_html_e("DESC","kuteshop")?>
                    </option><option <?php selected('asc',$order) ?> value="asc"><?php esc_html_e("ASC","kuteshop")?></option>
                </select>
            </p>
            <p>
                <label for="<?php echo esc_attr($this->get_field_id( 'category' )); ?>"><?php esc_html_e( 'Category:' ,'kuteshop'); ?></label>

                <?php wp_dropdown_categories(array(
                    'selected'=>$category,
                    'show_option_all'=>esc_html__('--- Select ---','kuteshop'),
                    'name'  =>$this->get_field_name( 'category' ),
                    'class' => 'widefat'
                )); ?>
            </p>


        <?php
        }
    }

    S7upf_ListPostsWidget::_init();

}
