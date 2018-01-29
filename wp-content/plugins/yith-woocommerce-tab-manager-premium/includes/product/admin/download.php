<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<div id="<?php echo $tab;?>_tab" class="panel woocommerce_options_panel">
    <div class="custom_tab_options" >
        <div class="form-field downloadable_files" style="padding: 10px;">
          <!--  <label><?php _e( 'Downloadable Files', 'woocommerce' ); ?>:</label>-->
            <table class="widefat" >
                <thead>
                <tr>
                    <th class="sort">&nbsp;</th>
                    <th><?php _e( 'Name', 'yith-woocommerce-tab-manager' ); ?> <span class="tips" data-tip="<?php _e( 'This is the name of the file that the customer will download.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></th>
                    <th><?php _e( 'File Description', 'woocommerce' ); ?> <span class="tips" data-tip="<?php _e( 'This is a short description of the file.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></th>
                    <th colspan="2"><?php _e( 'File URL', 'woocommerce' ); ?> <span class="tips" data-tip="<?php _e( 'This is the URL or the absolute path of the file available to the users.', 'yith-woocommerce-tab-manager' ); ?>">[?]</span></th>
                    <th>&nbsp;</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $download_files = get_post_meta( $post->ID, $tab. '_custom_list_file', true );


                if ( $download_files ) {
                    foreach ( $download_files as $key => $file ) {
                        include( 'html-tab-download.php' );
                                    }
                }
                ?>
                </tbody>
                <tfoot>
                <tr>
                    <th colspan="5">
                        <a href="#" class="button insert" data-row="<?php
                        $file = array(
                            'name' => '',
                            'file' => '',
                            'desc' =>''
                        );
                        ob_start();


                        include('html-tab-download.php');
                        echo esc_attr( ob_get_clean() );
                        ?>"><?php _e( 'Add File', 'yith-woocommerce-tab-manager' ); ?></a>
                    </th>
                </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>