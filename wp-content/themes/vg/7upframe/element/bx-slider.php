<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_bx_slider'))
{
    function s7upf_vc_bx_slider($attr)
    {
        $html = $logo = '';
        extract(shortcode_atts(array(
            'items'      => '',
        ),$attr));
        $data = (array) vc_param_group_parse_atts( $items );
        $bxslider_html = $pager_html = '';
        $default = array(
            'image' => '',
            'title' => '',
            'title2' => '',
            'title3' => '',
            'link' => '',
            );
        if(is_array($data)){
            foreach ($data as $key => $value) {                
                $value = array_merge($default,$value);
                $pager_html .= '<a data-slide-index="'.esc_attr($key).'" href="#"><span>'.esc_attr($key+1).'</span>'.esc_html($value['title']).'</a>';
                $bxslider_html .=   '<div class="item-banner">
                                        <div class="banner-thumb">
                                            <a href="'.esc_url($value['link']).'">'.wp_get_attachment_image($value['image'],'full').'</a>
                                        </div>
                                        <div class="banner-info white">
                                            <h3 class="white"><i>'.esc_html($value['title']).'</i></h3>
                                            <h2>'.esc_html($value['title2']).'</h2>
                                            <h3 class="white">'.esc_html($value['title3']).'</h3>
                                        </div>
                                    </div>';
            }
        }
        $html .=    '<div class="bxslider-banner banner-slider17">
                        <div class="bxslider">
                            '.$bxslider_html.'
                        </div>
                        <div class="bx-pager">
                            '.$pager_html.'
                        </div>
                    </div>';
        return $html;
    }
}

stp_reg_shortcode('s7upf_bx_slider','s7upf_vc_bx_slider');

vc_map( array(
    "name"      => esc_html__("SV Bx Slider", 'kuteshop'),
    "base"      => "s7upf_bx_slider",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            "type" => "param_group",
            "heading" => esc_html__("Add Item",'kuteshop'),
            "param_name" => "items",            
            "params"    => array(                        
                array(
                    "type" => "attach_image",
                    "heading" => esc_html__("Image",'kuteshop'),
                    "param_name" => "image",
                ),
                array(
                    "type" => "textfield",
                    "holder"    => 'h4',
                    "heading" => esc_html__("Title 1",'kuteshop'),
                    "param_name" => "title",
                ),
                array(
                    "type" => "textfield",
                    "holder"    => 'h3',
                    "heading" => esc_html__("Title 2",'kuteshop'),
                    "param_name" => "title2",
                ),
                array(
                    "type" => "textfield",
                    "holder"    => 'h4',
                    "heading" => esc_html__("Title 3",'kuteshop'),
                    "param_name" => "title3",
                ),
                array(
                    "type" => "textfield",
                    "heading" => esc_html__("Link",'kuteshop'),
                    "param_name" => "link",
                ),
            )
        ),
    )
));