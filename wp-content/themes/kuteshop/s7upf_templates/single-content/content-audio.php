<?php
$data = '';
if (get_post_meta(get_the_ID(), 'format_media', true)) {
    $media_url = get_post_meta(get_the_ID(), 'format_media', true);
    $data .= '<div class="audio">' . s7upf_remove_w3c(wp_oembed_get($media_url, array('height' => '176'))) . '</div>';
}
else{
    if (has_post_thumbnail()) {
        $data .= '<div class="post-thumb">
                    '.get_the_post_thumbnail(get_the_ID(),'full').'                
                </div>';
    }
}
?>
<div class="content-detail-item <?php echo (is_sticky()) ? 'sticky':''?>">
    <div class="main-single">
        <div class="post-format-date">
            <i aria-hidden="true" class="fa fa-calendar-o"></i>
            <span><?php echo get_the_date('M.d')?></span>
        </div>
        <h2 class="title-single"><?php the_title();?></h2>
        <?php s7upf_display_metabox();?>
    </div>
    <?php if(!empty($data)) echo balanceTags($data);?>
    <div class="content-detail-text">
        <?php the_content(); ?>
    </div>
</div>