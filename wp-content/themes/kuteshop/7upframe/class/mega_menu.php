<?php
global $s7upf_config;
if($s7upf_config['mega_menu'] == '1'){
    class S7upf_CustomMenu
    {
        static $allFields;
        static function init()
        {
            self::$allFields=array(
                'enable_megamenu'=>array(
                    'label'=>esc_html__('Enable Megamenu','kuteshop'),
                    'type'=>'checkbox',
                    'depth'=>0,
                ),
                'image'=>array(
                    'label'=>esc_html__('Image Preview','kuteshop'),
                    'type'=>'image',
                ),
                'icon_menu1'=>array(
                    'label'=>esc_html__('Icon Menu','kuteshop'),
                    'type'=>'text',
                    'depth'=>1,
                    'class'=>'sv_iconpicker',
                ),
                'icon_menu2'=>array(
                    'label'=>esc_html__('Icon Menu','kuteshop'),
                    'type'=>'text',
                    'depth'=>2,
                    'class'=>'sv_iconpicker',
                ),
                'content1'=>array(
                    'label'=>esc_html__('Content','kuteshop'),
                    'type'=>'text_html',
                    'depth'=>1,
                ),
                'content2'=>array(
                    'label'=>esc_html__('Content','kuteshop'),
                    'type'=>'text_html',
                    'depth'=>2,
                ),
                'col_size'=>array(
                    'label'=>esc_html__('Column Size','kuteshop'),
                    'type'=>'select',
                    'choices'=>array(
                        0       =>esc_html__('-- Select --','kuteshop'),
                        1       =>'1/12',
                        2       =>'2/12',
                        3       =>'3/12',
                        4       =>'4/12',
                        5       =>'5/12',
                        6       =>'6/12',
                        7       =>'7/12',
                        8       =>'8/12',
                        9       =>'9/12',
                        10      =>'10/12',
                        11      =>'11/12',
                        12      =>'12/12'
                    ),
                    'depth' =>1
                )

            );
            //add menu custom fields
            add_filter( 'wp_setup_nav_menu_item', array( __CLASS__, 'add_custom_menu_fields' ) );
            //Add walker
            add_filter( 'wp_edit_nav_menu_walker', array( __CLASS__, 'add_menu_custom_walker'),10,2 );
            // save menu custom fields
            add_action( 'wp_update_nav_menu_item', array( __CLASS__, 'save_custom_menu_fields'), 10, 3 );

            //add_action( 'admin_enqueue_scripts', array(__CLASS__,'_add_scripts'));

        }

        static function _add_scripts()
        {
            $screen=get_current_screen();

            if($screen->base=='nav-menus')
            {
                wp_register_script( 'st-jspicker', get_template_directory_uri().'/js/colpick.js', array( 'jquery' ),'', true );
                wp_enqueue_script('st-jspicker');
                wp_register_script( 'st-custompicker', get_template_directory_uri().'/js/custompicker.js', array( 'jquery' ),'', true );
                wp_enqueue_script('st-custompicker');

                wp_enqueue_style( 'st-colorpicker', get_template_directory_uri().'/css/colpick.css' );
            }
        }
        static function add_custom_menu_fields($item)
        {
            //
            if(!empty(self::$allFields))
            {
                foreach(self::$allFields as $key=>$value)
                {
                    $item->$key=get_post_meta($item->ID,$key,TRUE);
                }
            }
            return $item;
        }
        static function save_custom_menu_fields($menu_id, $menu_item_db_id, $args )
        {
            if(!empty(self::$allFields))
            {
                foreach(self::$allFields as $key=>$value)
                {
                    if(isset($_REQUEST[$key][$menu_item_db_id]))
                    {
                        $data=$_REQUEST[$key][$menu_item_db_id];
                        update_post_meta( $menu_item_db_id, $key, $data );
                    }elseif(isset($args[$key]))
                    {
                        $data=$args[$key];
                        update_post_meta( $menu_item_db_id, $key, $data );
                    }

                    if($value['type']=='checkbox')
                    {                    
                        if(!isset($_REQUEST[$key][$menu_item_db_id]))
                        {
                            delete_post_meta($menu_item_db_id,$key);
                        }
                    }

                }
            }
        }
        static function add_menu_custom_walker()
        {
            return  'S7upf_WalkerNavMenu';
        }
        static function AdminAddFields($item, $d=0)
        {
            if(!empty(self::$allFields))
            {
                foreach(self::$allFields as $key=>$value)
                {
                    $default=array(
                        'type'  =>'',
                        'class' =>"",
                        'compare'=>'',
                        'min_depth' =>'',
                        'depth' =>''
                    );
                    $value=wp_parse_args($value,$default);

                    if($value['min_depth'] and  $d<$value['min_depth']){
                        continue;
                    }

                    if($value['depth']!=='' and  $d!=$value['depth']){
                        continue;
                    }

                    $func='_field_type_'.$value['type'];
                    if(method_exists(__CLASS__,$func)){
                        self::$func($item,$key,$value,$d);
                    }



                }
            }
        }

        // =======================================================================
        // Field helper
        /*
         *
         *
         * */
        static function _field_type_image($item,$key,$value,$d=0){
            $item_id=esc_attr( $item->ID );
            $item_value=get_post_meta($item_id,$key,true);
            ?>
            <p class="field-custom description description-wide">
                <label><?php echo esc_html($value['label']) ?></label>
                <div class="wrap-metabox">
                    <div class="live-previews">
                        <?php if(!empty($item_value)):?>
                            <img src="<?php echo wp_get_attachment_url($item_value)?>"/>
                        <?php endif;?>
                    </div>
                    <a class="button button-primary sv-button-remove"> <?php esc_html_e("Remove","kuteshop")?></a>
                    <a class="button button-primary sv-button-upload-id"><?php esc_html_e("Upload","kuteshop")?></a>
                    <input id="<?php echo esc_attr($item_id.$key) ?>" name="<?php echo esc_attr($key.'['. $item_id.']'); ?>" type="hidden" class="widefat code edit-menu-item-custom sv-image-value" value="<?php echo esc_attr($item_value)?>"></input>
                </div>
            </p>
            <?php
        }
        
        static function _field_type_text($item,$key,$value,$d=0){
            $item_id=esc_attr( $item->ID );
            $item_value=get_post_meta($item_id,$key,true);
            ?>
            <p class="field-custom description description-wide">
                <label for="<?php echo esc_attr($item_id.$key) ?>">
                    <?php echo esc_html($value['label']) ?>
                    <input type="text" id="<?php echo esc_attr($item_id.$key) ?>" class="widefat code edit-menu-item-custom <?php echo isset($value['class'])?$value['class']:false ?>" value="<?php echo esc_attr($item_value)?>"  name="<?php echo esc_attr($key.'['. $item_id.']'); ?>" />

                </label>
            </p>
            <?php
        }
        static function _field_type_text_html($item,$key,$value,$d=0){
            $item_id=esc_attr( $item->ID );
            $item_value=get_post_meta($item_id,$key,true);
            $name = $key.'['. $item_id.']';
            $wp_editor_settings = array(
                'wpautop' => false,
                'textarea_rows' => 5,
                'textarea_name' => $name,
            );
            ?>
            <div id="wp-content-wrap" class="wp-content">
                <label for="<?php echo esc_attr($item_id.$key) ?>">
                    <?php echo esc_html($value['label']) ?>
                </label>
                <?php wp_editor( $item_value, $item_id , $wp_editor_settings);?>
            </div>
            <?php
        }
        static function _field_type_checkbox($item,$key,$value,$d=0){
            $item_id=esc_attr( $item->ID );
            $default=array(
                'type'  =>'checkbox',
                'class' =>"",
                'depth'=>'',
                'label'=>''
            );
            $value=wp_parse_args($value,$default);

            if($value['depth'] and $d>$value['depth']) return;
            $item_id=esc_attr( $item->ID );
            $item_value=get_post_meta($item_id,'enable_megamenu123',true);
            if($item_value){
                update_post_meta( $item_id, 'enable_megamenu', '1' );
                update_post_meta( $item_id, 'enable_megamenu123', '' );
            }
            ?>
            <p class="field-custom description description-wide">
                <label for="<?php echo esc_attr($item_id.$key) ?>">
                    <?php echo esc_html( $value['label']) ;
                    $en_check = ($value['type']=='checkbox' and ($item->$key==1 || $item_value))?'checked':false?>
                    <input type="checkbox" id="<?php echo esc_attr($item_id.$key) ?>" class="widefat code edit-menu-item-custom <?php echo isset($value['class'])?$value['class']:false ?>" <?php echo esc_attr($en_check) ?> name="<?php echo esc_attr( $key.'['. $item_id.']'); ?>" value="1" />

                </label>
            </p>
            <?php
        }
        static function _field_type_select($item,$key,$value,$d=0){

            $default=array(
                'type'  =>'select',
                'class' =>"",
                'choices'=>array()
            );

            $value=wp_parse_args($value,$default);

            $item_id=esc_attr( $item->ID );
            ?>
            <p class="field-custom description description-wide">
                <label for="<?php echo esc_attr($item_id.$key) ?>">
                    <?php echo esc_html( $value['label']) ?>

                    <select class="widefat code edit-menu-item-custom <?php echo isset($value['class'])?$value['class']:false ?>" id="<?php echo esc_attr($item_id.$key) ?>" name="<?php echo esc_attr( $key.'['. $item_id.']'); ?>">

                        <?php
                        if(!empty($value['choices']))
                        {
                            foreach($value['choices'] as $k=>$v){

                                $select=selected($k,$item->$key,false);
                                echo "<option {$select} value='{$k}'>{$v}</option>";
                            }
                        }

                        ?>
                    </select>

                </label>
            </p>
            <?php
        }
    }

    S7upf_CustomMenu::init();
    class S7upf_WalkerNavMenu extends Walker_Nav_Menu
    {
        /**
         * Starts the list before the elements are added.
         *
         * @see Walker_Nav_Menu::start_lvl()
         *
         * @since 3.0.0
         *
         * @param string $output Passed by reference.
         * @param int    $depth  Depth of menu item. Used for padding.
         * @param array  $args   Not used.
         */
        public function start_lvl( &$output, $depth = 0, $args = array() ) {}

        /**
         * Ends the list of after the elements are added.
         *
         * @see Walker_Nav_Menu::end_lvl()
         *
         * @since 3.0.0
         *
         * @param string $output Passed by reference.
         * @param int    $depth  Depth of menu item. Used for padding.
         * @param array  $args   Not used.
         */
        public function end_lvl( &$output, $depth = 0, $args = array() ) {}

        /**
         * Start the element output.
         *
         * @see Walker_Nav_Menu::start_el()
         * @since 3.0.0
         *
         * @param string $output Passed by reference. Used to append additional content.
         * @param object $item   Menu item data object.
         * @param int    $depth  Depth of menu item. Used for padding.
         * @param array  $args   Not used.
         * @param int    $id     Not used.
         */
        public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

            global $_wp_nav_menu_max_depth;
            $_wp_nav_menu_max_depth = $depth > $_wp_nav_menu_max_depth ? $depth : $_wp_nav_menu_max_depth;

            ob_start();
            $item_id = esc_attr( $item->ID );
            $removed_args = array(
                'action',
                'customlink-tab',
                'edit-menu-item',
                'menu-item',
                'page-tab',
                '_wpnonce',
            );

            $original_title = '';
            if ( 'taxonomy' == $item->type ) {
                $original_title = get_term_field( 'name', $item->object_id, $item->object, 'raw' );
                if ( is_wp_error( $original_title ) )
                    $original_title = false;
            } elseif ( 'post_type' == $item->type ) {
                $original_object = get_post( $item->object_id );
                $original_title = get_the_title( $original_object->ID );
            }

            $classes = array(
                'menu-item menu-item-depth-' . $depth,
                'menu-item-' . esc_attr( $item->object ),
                'menu-item-edit-' . ( ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? 'active' : 'inactive'),
            );

            $title = $item->title;

            if ( ! empty( $item->_invalid ) ) {
                $classes[] = 'menu-item-invalid';
                /* translators: %s: title of menu item which is invalid */
                $title = sprintf( __( '%s (Invalid)' ,'kuteshop'), $item->title );
            } elseif ( isset( $item->post_status ) && 'draft' == $item->post_status ) {
                $classes[] = 'pending';
                /* translators: %s: title of menu item in draft status */
                $title = sprintf( esc_html__('%s (Pending)','kuteshop'), $item->title );
            }

            $title = ( ! isset( $item->label ) || '' == $item->label ) ? $title : $item->label;

            $submenu_text = '';
            if ( 0 == $depth )
                $submenu_text = 'display: none;';

            ?>
            <li id="menu-item-<?php echo esc_attr( $item_id); ?>" class="<?php echo implode(' ', $classes ); ?>">
            <dl class="menu-item-bar">
                <dt class="menu-item-handle">
                    <span class="item-title"><span class="menu-item-title"><?php echo esc_html( $title ); ?></span> <span class="is-submenu" style="<?php echo esc_attr($submenu_text); ?>"><?php esc_html_e( 'sub item' ,'kuteshop'); ?></span></span>
                        <span class="item-controls">
                            <span class="item-type"><?php echo esc_html( $item->type_label ); ?></span>
                            <span class="item-order hide-if-js">
                                <a href="<?php
                                echo wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-up-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                );
                                ?>" class="item-move-up"><abbr title="<?php esc_attr_e('Move up','kuteshop'); ?>">&#8593;</abbr></a>
                                |
                                <a href="<?php
                                echo wp_nonce_url(
                                    add_query_arg(
                                        array(
                                            'action' => 'move-down-menu-item',
                                            'menu-item' => $item_id,
                                        ),
                                        remove_query_arg($removed_args, admin_url( 'nav-menus.php' ) )
                                    ),
                                    'move-menu_item'
                                );
                                ?>" class="item-move-down"><abbr title="<?php esc_attr_e('Move down','kuteshop'); ?>">&#8595;</abbr></a>
                            </span>
                            <a class="item-edit" id="edit-<?php echo esc_attr($item_id); ?>" title="<?php esc_attr_e('Edit Menu Item','kuteshop'); ?>" href="<?php
                            echo ( isset( $_GET['edit-menu-item'] ) && $item_id == $_GET['edit-menu-item'] ) ? admin_url( 'nav-menus.php' ) : esc_url( add_query_arg( 'edit-menu-item', $item_id, remove_query_arg( $removed_args, admin_url( 'nav-menus.php#menu-item-settings-' . $item_id ) ) ));
                            ?>"><?php esc_html_e( 'Edit Menu Item' .'kuteshop'); ?></a>
                        </span>
                </dt>
            </dl>

            <div class="menu-item-settings" id="menu-item-settings-<?php echo esc_attr($item_id); ?>">
                <?php if( 'custom' == $item->type ) : ?>
                    <p class="field-url description description-wide">
                        <label for="edit-menu-item-url-<?php echo esc_attr($item_id); ?>">
                            <?php esc_html_e( 'URL' ,'kuteshop'); ?><br />
                            <input type="text" id="edit-menu-item-url-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-url" name="menu-item-url[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->url ); ?>" />
                        </label>
                    </p>
                <?php endif; ?>
                <p class="description description-thin">
                    <label for="edit-menu-item-title-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e( 'Navigation Label' ,'kuteshop'); ?><br />
                        <input type="text" id="edit-menu-item-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-title" name="menu-item-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->title ); ?>" />
                    </label>
                </p>
                <p class="description description-thin">
                    <label for="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e( 'Title Attribute' ,'kuteshop'); ?><br />
                        <input type="text" id="edit-menu-item-attr-title-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-attr-title" name="menu-item-attr-title[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->post_excerpt ); ?>" />
                    </label>
                </p>
                <p class="field-link-target description">
                    <label for="edit-menu-item-target-<?php echo esc_attr($item_id); ?>">
                        <input type="checkbox" id="edit-menu-item-target-<?php echo esc_attr($item_id); ?>" value="_blank" name="menu-item-target[<?php echo esc_attr($item_id); ?>]"<?php checked( $item->target, '_blank' ); ?> />
                        <?php esc_html_e( 'Open link in a new window/tab','kuteshop' ); ?>
                    </label>
                </p>
                <?php

                S7upf_CustomMenu::AdminAddFields($item,$depth);

                ?>
                <p class="field-css-classes description description-thin">
                    <label for="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e( 'CSS Classes (optional)' ,'kuteshop'); ?><br />
                        <input type="text" id="edit-menu-item-classes-<?php echo esc_attr($item_id); ?>" class="widefat code edit-menu-item-classes" name="menu-item-classes[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( implode(' ', $item->classes ) ); ?>" />
                    </label>
                </p>

                <p class="field-xfn description description-thin">
                    <label for="edit-menu-item-xfn-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e( 'Link Relationship (XFN)' ,'kuteshop'); ?><br />
                <p class="field-description description description-wide">
                    <label for="edit-menu-item-description-<?php echo esc_attr($item_id); ?>">
                        <?php esc_html_e( 'Description' ,'kuteshop'); ?><br />
                        <textarea id="edit-menu-item-description-<?php echo esc_attr($item_id); ?>" class="widefat edit-menu-item-description" rows="3" cols="20" name="menu-item-description[<?php echo esc_attr($item_id); ?>]"><?php echo esc_html( $item->description ); // textarea_escaped ?></textarea>
                        <span class="description"><?php esc_html_e('The description will be displayed in the menu if the current theme supports it.' ,'kuteshop'); ?></span>
                    </label>
                </p>

                <p class="field-move hide-if-no-js description description-wide">
                    <label>
                        <span><?php esc_html_e( 'Move' ,'kuteshop'); ?></span>
                        <a href="#" class="menus-move-up"><?php esc_html_e( 'Up one' ,'kuteshop'); ?></a>
                        <a href="#" class="menus-move-down"><?php esc_html_e( 'Down one' ,'kuteshop' ); ?></a>
                        <a href="#" class="menus-move-left"></a>
                        <a href="#" class="menus-move-right"></a>
                        <a href="#" class="menus-move-top"><?php esc_html_e( 'To the top' ,'kuteshop'); ?></a>
                    </label>
                </p>

                <div class="menu-item-actions description-wide submitbox">
                    <?php if( 'custom' != $item->type && $original_title !== false ) : ?>
                        <p class="link-to-original">
                            <?php printf( esc_html__('Original: %s','kuteshop'), '<a href="' . esc_attr( $item->url ) . '">' . esc_html( $original_title ) . '</a>' ); ?>
                        </p>
                    <?php endif; ?>
                    <a class="item-delete submitdelete deletion" id="delete-<?php echo esc_attr($item_id); ?>" href="<?php
                    echo wp_nonce_url(
                        add_query_arg(
                            array(
                                'action' => 'delete-menu-item',
                                'menu-item' => $item_id,
                            ),
                            admin_url( 'nav-menus.php' )
                        ),
                        'delete-menu_item_' . $item_id
                    ); ?>"><?php esc_html_e( 'Remove' ,'kuteshop'); ?></a> <span class="meta-sep hide-if-no-js"> | </span> <a class="item-cancel submitcancel hide-if-no-js" id="cancel-<?php echo esc_attr($item_id); ?>" href="<?php echo esc_url( add_query_arg( array( 'edit-menu-item' => $item_id, 'cancel' => time() ), admin_url( 'nav-menus.php' ) ) );
                    ?>#menu-item-settings-<?php echo esc_attr($item_id); ?>"><?php esc_html_e('Cancel','kuteshop'); ?></a>
                </div>

                <input class="menu-item-data-db-id" type="hidden" name="menu-item-db-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr($item_id); ?>" />
                <input class="menu-item-data-object-id" type="hidden" name="menu-item-object-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object_id ); ?>" />
                <input class="menu-item-data-object" type="hidden" name="menu-item-object[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->object ); ?>" />
                <input class="menu-item-data-parent-id" type="hidden" name="menu-item-parent-id[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_item_parent ); ?>" />
                <input class="menu-item-data-position" type="hidden" name="menu-item-position[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->menu_order ); ?>" />
                <input class="menu-item-data-type" type="hidden" name="menu-item-type[<?php echo esc_attr($item_id); ?>]" value="<?php echo esc_attr( $item->type ); ?>" />
            </div><!-- .menu-item-settings-->
            <ul class="menu-item-transport"></ul>
            <?php
            $output .= ob_get_clean();
        }

    }
}