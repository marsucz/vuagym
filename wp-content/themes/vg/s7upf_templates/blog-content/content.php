<?php
$data = '';
global $post;
if (has_post_thumbnail()) {
    $data .=    '<div class="post-thumb">
                    <a class="post-thumb-link" href="'. esc_url(get_the_permalink()) .'">'.get_the_post_thumbnail(get_the_ID(),'full').'</a>                
                </div>';
}
?>
<div class="item-post-large item-default">
    <?php if(!empty($data)) echo balanceTags($data);?>    
    <div class="post-info">
        <h3 class="post-title">
            <a href="<?php echo esc_url(get_the_permalink()); ?>">
                <?php the_title()?>
                <?php echo (is_sticky()) ? '<i class="fa fa-thumb-tack"></i>':''?>
            </a>
        </h3>
        <p class="desc"><?php echo get_the_excerpt(); ?></p>
        <?php s7upf_display_metabox();?>
    </div>
</div>