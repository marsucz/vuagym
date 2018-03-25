<?php
$data = $st_link_post= $s_class = '';
global $post;
$s7upf_image_blog = get_post_meta(get_the_ID(), 'format_image', true);
if(!empty($s7upf_image_blog)){
    $data .='<div class="post-thumb">
                <img alt="'.$post->post_name.'" title="'.$post->post_name.'" src="' . esc_url($s7upf_image_blog) . '"/>
            </div>';
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