<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<?php

$videos = get_post_meta ( $post->ID, $tab.'_custom_video',true );

$video_settings = isset( $videos['settings'] ) ? $videos['settings'] : array();

//$height     =   isset( $video_settings['height'] )  ? $video_settings['height']   : 100;
$columns    =   isset( $video_settings['columns'] ) ? $video_settings['columns']  : 1
?>
<div id="<?php echo $tab;?>_tab" class="panel woocommerce_options_panel">
    <div class="custom_tab_options" >
        <div class="options_group">
            <p class="form-field">
                <label for="custom_columns_number"><?php _e('Videos per row','yith-woocommerce-tab-manager');?></label>
                <input type="number" id="custom_columns_number" min="1" max="4" step="1" name="<?php echo $tab?>_columns_number_video" value="<?php echo esc_attr($columns);?>"/>
                <span class="description"><?php _e('Set how many columns to show', 'yith-woocommerce-tab-manager');?></span>
            </p>
        </div>
       <div class="options_group">
        <div class="form-field downloadable_files" style="padding:10px;">
            <table class="widefat">
                <thead>
                <tr>
                    <th class="sort">&nbsp;</th>
                    <th  style="text-align: center;"><?php _e('Video Hosting Service', 'yith-woocommerce-tab-manager');?><span class="tips" data-tip="<?php _e( 'This is the service that hosts the video.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></th>
                    <th  style="text-align: center;"><?php _e( 'Video ID', 'yith-woocommerce-tab-manager' ); ?><span class="tips" data-tip="<?php _e( 'This is the video ID.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></th>
                    <th colspan="2" style="text-align: center;"><?php _e( 'Video URL', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the video URL.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php

                $videos = get_post_meta ( $post->ID, $tab.'_custom_video',true );

                if ( isset( $videos['video'] ) ) {
                    foreach ( $videos['video'] as $key => $video ) {
                        include('html-tab-video.php');
                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5">
                        <a href="#" class="button insert" data-row="<?php
                        $video = array(
                            'url'       =>  '',
                            'host'      =>  '',
                            'id'  =>  ''
                        );

                        ob_start();
                        include('html-tab-video.php');
                        echo esc_attr( ob_get_clean() );
                        ?>"><?php _e( 'Add Video', 'yith-woocommerce-tab-manager' ); ?></a>
                    </th>
                </tr>
                </tfoot>
            </table>

        </div>
      </div>
    </div>

</div>