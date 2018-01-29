<div class="tab-download-container ywtm_content_tab">
    <?php
    if( !empty( $download ) ) {

        foreach ($download as $key => $file):?>
            <?php
            $file_name  = isset($file['name'])? $file['name'] : __('No file name', 'yith-woocommerce-tab-manager');
            $file_desc  = isset($file['desc'])? $file['desc'] : __('No file description', 'yith-woocommerce-tab-manager');
            ?>
            <div class="single_download_container">
                <div class="file_title">
                    <h4><?php echo $file_name;?></h4>
                    <p><?php echo $file_desc;?></p>
                </div>
                <div class="button_download"><a href="<?php echo $file['file'];?>" download target="_blank"><?php _e('Download File','yith-woocommerce-tab-manager');?></a> </div>
            </div>

        <?php endforeach;
    }
    else  echo '<span>'.__('No download for this product', 'yith-woocommerce-tab-manager').'</span>';
    ?>
</div>