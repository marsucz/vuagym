<?php
$data = $st_link_post= $s_class = '';
global $post;
if (has_post_thumbnail()) {
    $data .= '<div class="post-thumbnail">
                '.get_the_post_thumbnail(get_the_ID(),'full',array('class'=>'blog-image')).'                
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