<?php
/**
 * Created by Sublime text 2.
 * User: thanhhiep992
 * Date: 12/08/15
 * Time: 10:00 AM
 */

if(!function_exists('s7upf_vc_social'))
{
    function s7upf_vc_social($attr, $content = false)
    {
        $html = $icon_html = '';
        extract(shortcode_atts(array(
            'style'         => 'footer-box',
            'title'          => '',
            'list'          => '',
            'align'         => 'text-left',
        ),$attr));
		parse_str( urldecode( $list ), $data);
        if(is_array($data)){
            foreach ($data as $key => $value) {
                $url = '#';
                if(isset($value['url'])) $url = $value['url'];
                $icon_html .= '<a href="'.esc_url($url).'"><i class="fa '.$value['social'].'"></i></a>';
            }
        }
        switch ($style) {
            case 'social-header16':
                $html .=    '<div class="social-header social-header16 '.$align.'">';
                $html .=        $icon_html;
                $html .=    '</div>';
                break;

            case 'social-footer15':
            case 'social-footer9':
                $html .=    '<div class="'.$style.' '.$align.'">';
                if(!empty($title)) $html .= '<label>'.esc_html($title).'</label>';
                $html .=        '<div class="list-social">';
                $html .=            $icon_html;
                $html .=        '</div>
                            </div>';
                break;

            case 'social-header1':
                $html .=    '<div class="social-header '.$align.'">';
                $html .=        $icon_html;
                $html .=    '</div>';
                break;

            case 'social-header2':
                $html .=    '<div class="social-header style2 '.$align.'">';
                $html .=        $icon_html;
                $html .=    '</div>';
                break;
            
            default:
                $html .=    '<div class="social-footer '.$style.' '.$align.'">';
                if(!empty($title)) $html .= '<h2 class="title14">'.esc_html($title).'</h2>';
                $html .=        '<div class="list-social">';
                $html .=            $icon_html;
                $html .=        '</div>';
                $html .=    '</div>';
                break;
        }
		return  $html;
    }
}

stp_reg_shortcode('sv_social','s7upf_vc_social');


vc_map( array(
    "name"      => esc_html__("SV Social", 'kuteshop'),
    "base"      => "sv_social",
    "icon"      => "icon-st",
    "category"  => '7Up-theme',
    "params"    => array(
        array(
            'type'        => 'dropdown',
            'heading'     => esc_html__( 'Style', 'kuteshop' ),
            'param_name'  => 'style',
            'value'       => array(
                esc_html__( 'Footer', 'kuteshop' )   => 'footer-box',
                esc_html__( 'Footer 6', 'kuteshop' )   => 'footer-box footer-box6',
                esc_html__( 'Header 2', 'kuteshop' )   => 'social-header2',
                esc_html__( 'Header 1', 'kuteshop' )   => 'social-header1',
                esc_html__( 'Footer 9', 'kuteshop' )   => 'social-footer9',
                esc_html__( 'Footer 15', 'kuteshop' )   => 'social-footer15',
                esc_html__( 'Header 16', 'kuteshop' )   => 'social-header16',
                )
        ),
        array(
            'type'        => 'textfield',
            'heading'     => esc_html__( 'Title', 'kuteshop' ),
            'param_name'  => 'title',
        ),
        array(
			'type' => 'dropdown',
			'heading' => esc_html__( 'Align', 'kuteshop' ),
			'value' => array(
				esc_html__( 'Align Left', 'kuteshop' ) => 'text-left',
				esc_html__( 'Align Center', 'kuteshop' ) => 'text-center',
				esc_html__( 'Align Right', 'kuteshop' ) => 'text-right',
			),
			'param_name' => 'align',
			'description' => esc_html__( 'Select social layout', 'kuteshop' ),
		),
		array(
            "type" => "add_social",
            "heading" => esc_html__("Add Social List",'kuteshop'),
            "param_name" => "list",
        )
    )
));