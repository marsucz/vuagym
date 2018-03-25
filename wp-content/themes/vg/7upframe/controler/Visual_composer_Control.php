<?php
/**
 * Created by Sublime Text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:20 AM
 */
if(class_exists('Vc_Manager')){
    function s7upf_add_custom_shortcode_param( $name, $form_field_callback, $script_url = null ) {
        return WpbakeryShortcodeParams::addField( $name, $form_field_callback, $script_url );
    }
    //Add brand
    s7upf_add_custom_shortcode_param('add_brand', 's7upf_add_payment', get_template_directory_uri(). '/assets/js/vc_js.js');
    function s7upf_add_payment($settings, $value)
    {
        $val = $value;
        $html = '<div class="wrap-param">';
        $html .=    '<div class="param-list">';
        
        parse_str(urldecode($value), $data);
        if(is_array($data)) 
        {
            $i = 1;
            foreach ($data as $key => $value) {
                if(!isset($value['url'])) $value['url'] = '';
                $html .=    '<div class="param-item" data-item="'.$i.'">';
                 $html .=       '<strong>'.esc_html__("Image","kuteshop").' '.$i.':</strong></br>';
                    $html .=    '<div class="wpb_el_type_attach_image edit_form_line">
                                    <input type="hidden" class="param-field wpb_vc_param_value gallery_widget_attached_images_ids images attach_images" name="'.$i.'[image]" value="'.$value['image'].'">
                                    <div class="gallery_widget_attached_images">
                                        <ul class="gallery_widget_attached_images_list ui-sortable">';
                    if(!empty($value['image'])){
                        $img = wp_get_attachment_image_src( $value['image'] );
                        $html .=            '<li class="added ui-sortable-handle">
                                                <img rel="'.$value['image'].'" src="'.esc_url($img[0]).'">
                                                <a href="#" class="icon-remove"></a>
                                            </li>';
                    }
                    $html .=            '</ul>
                                    </div>
                                    <div class="gallery_widget_site_images"></div>
                                    <a class="gallery_widget_add_images" href="#" use-single="true" title="'.esc_html__("Add images","kuteshop").'">'.esc_html__("Add images","kuteshop").'</a>
                                    <span class="vc_description vc_clearfix">'.esc_html__("Select images from media library.","kuteshop").'</span>
                                </div>';
                    $html .=    '<label>'.esc_html__("Link","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="'.$i.'[link]" value="'.$value['link'].'" type="text" >';
                    $html .=    '<a style="color:red" href="#" class="st-del-item">'.esc_html__("Delete","kuteshop").'</a>';
                $html .=    '</div>';
                $i++;
            }
        }
        $html .=    '</div>';
        $html .=    '<div class="st-add">
                        <button class="vc_btn vc_btn-primary vc_btn-sm add-param" type="button">'.esc_html__('Add Item', 'kuteshop').' </button>';
        $html .=        '<div class="param-content-copy hidden">';
        $html .=            '<div class="param-item" data-item="#key">';
        $html .=                '<div class="wpb_el_type_attach_image edit_form_line"><input type="hidden" class="param-field wpb_vc_param_value gallery_widget_attached_images_ids images attach_images" name="#key[image]" value=""><div class="gallery_widget_attached_images"><ul class="gallery_widget_attached_images_list ui-sortable"></ul></div><div class="gallery_widget_site_images"></div><a class="gallery_widget_add_images" href="#" use-single="true" title="'.esc_html__("Add images","kuteshop").'">'.esc_html__("Add images","kuteshop").'</a><span class="vc_description vc_clearfix">'.esc_html__("Select images from media library.","kuteshop").'</span></div>';            
        $html .=                '<label>'.esc_html__("Link","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="#key[link]" value="" type="text">';
        $html .=                '<a style="color:red" href="#" class="st-del-item">'.esc_html__("Delete","kuteshop").'</a>';
        $html .=            '</div>';
        $html .=        '</div>';
        $html .=    '</div>';
        $html .=    '<input name="'.$settings['param_name'].'" class="param-value wpb_vc_param_value wpb-textinput '.$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$val.'">';
        $html .='</div>';        
        return $html;
    }

    //Add adv
    s7upf_add_custom_shortcode_param('add_advantage', 's7upf_add_advantage', get_template_directory_uri(). '/assets/js/vc_js.js');
    function s7upf_add_advantage($settings, $value)
    {
        $val = $value;
        $html = '<div class="wrap-param">';
        $html .=    '<div class="param-list">';
        
        parse_str(urldecode($value), $advantage);
        if(is_array($advantage)) 
        {
            $i = 1;
            foreach ($advantage as $key => $value) {
                if(!isset($value['url'])) $value['url'] = '';
                $html .=    '<div class="param-item" data-item="'.$i.'">';
                 $html .=       '<strong>'.esc_html__("Image","kuteshop").' '.$i.':</strong></br>';
                    $html .=    '<div class="wpb_el_type_attach_image edit_form_line">
                                    <input type="hidden" class="param-field wpb_vc_param_value gallery_widget_attached_images_ids images attach_images" name="'.$i.'[image]" value="'.$value['image'].'">
                                    <div class="gallery_widget_attached_images">
                                        <ul class="gallery_widget_attached_images_list ui-sortable">';
                    if(!empty($value['image'])){
                        $img = wp_get_attachment_image_src( $value['image'] );
                        $html .=            '<li class="added ui-sortable-handle">
                                                <img rel="'.$value['image'].'" src="'.esc_url($img[0]).'">
                                                <a href="#" class="icon-remove"></a>
                                            </li>';
                    }
                    $html .=            '</ul>
                                    </div>
                                    <div class="gallery_widget_site_images"></div>
                                    <a class="gallery_widget_add_images" href="#" use-single="true" title="'.esc_html__("Add images","kuteshop").'">'.esc_html__("Add images","kuteshop").'</a>
                                    <span class="vc_description vc_clearfix">'.esc_html__("Select images from media library.","kuteshop").'</span>
                                </div>';
                    $html .=    '<label>'.esc_html__("Title","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="'.$i.'[title]" value="'.$value['title'].'" type="text" ></br>';
                    $html .=    '<label>'.esc_html__("Description 1","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="'.$i.'[des1]" value="'.$value['des1'].'" type="text" ></br>';
                    $html .=    '<label>'.esc_html__("Description 2","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="'.$i.'[des2]" value="'.$value['des2'].'" type="text" ></br>';
                    $html .=    '<label>'.esc_html__("Link","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="'.$i.'[link]" value="'.$value['link'].'" type="text" >';
                    $html .=    '<a style="color:red" href="#" class="st-del-item">'.esc_html__("Delete","kuteshop").'</a>';
                $html .=    '</div>';
                $i++;
            }
        }
        $html .=    '</div>';
        $html .=    '<div class="st-add">
                        <button class="vc_btn vc_btn-primary vc_btn-sm add-param" type="button">'.esc_html__('Add Item', 'kuteshop').' </button>';
        $html .=        '<div class="param-content-copy hidden">';
        $html .=            '<div class="param-item" data-item="#key">';
        $html .=                '<div class="wpb_el_type_attach_image edit_form_line"><input type="hidden" class="param-field wpb_vc_param_value gallery_widget_attached_images_ids images attach_images" name="#key[image]" value=""><div class="gallery_widget_attached_images"><ul class="gallery_widget_attached_images_list ui-sortable"></ul></div><div class="gallery_widget_site_images"></div><a class="gallery_widget_add_images" href="#" use-single="true" title="'.esc_html__("Add images","kuteshop").'">'.esc_html__("Add images","kuteshop").'</a><span class="vc_description vc_clearfix">'.esc_html__("Select images from media library.","kuteshop").'</span></div>';            
        $html .=                '<label>'.esc_html__("Title","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="#key[title]" value="" type="text"></br>';            
        $html .=                '<label>'.esc_html__("Description 1","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="#key[des1]" value="" type="text"></br>';
        $html .=                '<label>'.esc_html__("Description 2","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="#key[des2]" value="" type="text"></br>';
        $html .=                '<label>'.esc_html__("Link","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="param-field" name="#key[link]" value="" type="text"></br>';
        $html .=                '<a style="color:red" href="#" class="st-del-item">'.esc_html__("Delete","kuteshop").'</a>';
        $html .=            '</div>';
        $html .=        '</div>';
        $html .=    '</div>';
        $html .=    '<input name="'.$settings['param_name'].'" class="param-value wpb_vc_param_value wpb-textinput '.$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$val.'">';
        $html .='</div>';        
        return $html;
    }
    s7upf_add_custom_shortcode_param('add_icon_link', 's7upf_add_icon_link', get_template_directory_uri(). '/assets/js/vc_js.js');
    
    // function icon link
    function s7upf_add_icon_link($settings, $value)
    {
        // $dependency = vc_generate_dependencies_attributes($settings);
        $val = $value;
        $html = '<div class="st_add_icon_link">';
        
        parse_str(urldecode($value), $icon_link);
        if(is_array($icon_link)) 
        {
            $i = 1;
            foreach ($icon_link as $key => $value) {
                if(!isset($value['url'])) $value['url'] = '';
                $html .= '<div class="icon_link-item" data-item="'.$i.'">';
                    $html .= '<label>'.esc_html__("Info","kuteshop").' '.$i.':</label></br><label>'.esc_html__("Icon","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="st-icon_link sv_iconpicker" name="'.$i.'[icon]" value="'.$value['icon'].'" type="text" ></br>';
                    $html .= '<label>'.esc_html__("Title","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="st-icon_link" name="'.$i.'[title]" value="'.$value['title'].'" type="text" ></br>';
                    $html .= '<label>'.esc_html__("Link","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="st-icon_link" name="'.$i.'[url]" value="'.$value['url'].'" type="text" >';
                    $html .= '<a style="color:red" href="#" class="st-del-item">'.esc_html__("Delete","kuteshop").'</a>';
                $html .= '</div>';
                $i++;
            }
        }
        $html .= '</div>';
        $html .= '<div class="st-add"><button class="vc_btn vc_btn-primary vc_btn-sm st-button-add-icon_link" type="button">'.esc_html__('Add Link', 'kuteshop').' </button></div>';
        $html .= '<input name="'.$settings['param_name'].'" class="st-icon_link-value wpb_vc_param_value wpb-textinput '.$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$val.'">';
        return $html;
    }
    s7upf_add_custom_shortcode_param('add_social', 's7upf_add_social', get_template_directory_uri(). '/assets/js/vc_js.js');
    
    // function social
    function s7upf_add_social($settings, $value)
    {
        // $dependency = vc_generate_dependencies_attributes($settings);
        $val = $value;
        $html = '<div class="st_add_social">';
        
        parse_str(urldecode($value), $social);
        if(is_array($social)) 
        {
            $i = 1;
            foreach ($social as $key => $value) {
                if(!isset($value['url'])) $value['url'] = '';
                $html .= '<div class="social-item" data-item="'.$i.'">';
                    $html .= '<label>'.esc_html__("Social","kuteshop").' '.$i.':</label></br><label>'.esc_html__("Icon","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="st-social sv_iconpicker" name="'.$i.'[social]" value="'.$value['social'].'" type="text" ></br>';
                    $html .= '<label>'.esc_html__("Link","kuteshop").' </label><input style="width:65%;margin-right:10px;margin-bottom:15px" class="st-social" name="'.$i.'[url]" value="'.$value['url'].'" type="text" >';
                    $html .= '<a style="color:red" href="#" class="st-del-item">'.esc_html__("Delete","kuteshop").'</a>';
                $html .= '</div>';
                $i++;
            }
        }
        $html .= '</div>';
        $html .= '<div class="st-add"><button class="vc_btn vc_btn-primary vc_btn-sm st-button-add" type="button">'.esc_html__('Add social', 'kuteshop').' </button></div>';
        $html .= '<input name="'.$settings['param_name'].'" class="st-social-value wpb_vc_param_value wpb-textinput '.$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$val.'">';
        return $html;
    }

    // Mutil location param

    s7upf_add_custom_shortcode_param('add_location_map', 's7upf_add_location_map', get_template_directory_uri(). '/assets/js/vc_js.js');

    function s7upf_add_location_map($settings, $value)
    {
        // $dependency = vc_generate_dependencies_attributes($settings);
        $val = $value;
        $html = '<div class="st_add_location">';
        
        parse_str(urldecode($value), $locations);
        if(is_array($locations)) 
        {
            $i = 1;
            foreach ($locations as $key => $value) {
                $html .= '<div class="location-item" data-item="'.$i.'">';
                    $html .= '<div class="wpb_element_label">'.esc_html__("Location",'kuteshop').' '.$i.'</div>';
                    $html .= '<label>'.esc_html__("Latitude",'kuteshop').'</label><input class="st-input st-location-save st-title" name="'.$i.'[lat]" value="'.$value['lat'].'" type="text" >';
                    $html .= '<label>'.esc_html__("Longitude",'kuteshop').'</label><input class="st-input st-location-save st-des" name="'.$i.'[lon]" value="'.$value['lon'].'" type="text" >';
                    $html .= '<label>'.esc_html__("Marker Title",'kuteshop').'</label><input class="st-input st-location-save st-label" name="'.$i.'[title]" value="'.$value['title'].'" type="text" >';
                    $html .= '<label>'.esc_html__("Info Box",'kuteshop').'</label>';
                    $html .= '<label>'.esc_html__("Info Box",'kuteshop').'</label><textarea id="content'.$i.'" class="st-input st-location-save info-content" name="'.$i.'[boxinfo]">'.$value['boxinfo'].'</textarea>';
                    $html .= '<a href="#" class="st-del-item">delete</a>';
                $html .= '</div>';
                $i++;
            }
        }
        $html .= '</div>';
        $html .= '<div class="add-location"><button style="margin-top: 10px;padding: 5px 12px" class="vc_btn vc_btn-primary vc_btn-sm st-location-add-map" type="button">'.esc_html__('Add more', 'kuteshop').' </button></div>';
        $html .= '<input name="'.$settings['param_name'].'" class="st-location-value wpb_vc_param_value wpb-textinput '.$settings['param_name'].' '.$settings['type'].'_field" type="hidden" value="'.$val.'">';
        return $html;
    }

    // Mutil location param

    if(!class_exists('S7upf_VisualComposerController'))
    {
        class  S7upf_VisualComposerController
        {

            static function _init()
            {
                add_filter('vc_shortcodes_css_class',array(__CLASS__,'_change_class'), 10, 2);
            }

            static function _custom_vc_param()
            {
                
               
            }

            static function _change_class($class_string, $tag)
            {
                if($tag=='vc_row' || $tag=='vc_row_inner') {
                    $class_string = str_replace('vc_row-fluid', '', $class_string);
                }

                if(defined ('WPB_VC_VERSION'))
                {
                    if(version_compare(WPB_VC_VERSION,'4.2.3','>'))
                    {
                        if($tag=='vc_column' || $tag=='vc_column_inner') {
                            $class_string=str_replace('vc_col', 'col', $class_string);
                        }
                    }else
                    {
                        if($tag=='vc_column' || $tag=='vc_column_inner') {
                            $class_string = preg_replace('/vc_span(\d{1,2})/', 'col-lg-$1', $class_string);
                        }
                    }
                }

                return $class_string;
            }

        }    

        S7upf_VisualComposerController::_init();
        S7upf_VisualComposerController:: _custom_vc_param(); 
    }
    
}