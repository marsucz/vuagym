<?php
$data = '';
if (has_post_thumbnail()) {
    $data .=    '<div class="post-thumb">
                    <a class="post-thumb-link" href="'. esc_url(get_the_permalink()) .'">'.get_the_post_thumbnail(get_the_ID(),'full').'</a>                
                </div>';
}
?>
<div class="item-post-large">
    <div class="post-thumb">
        <?php if(!empty($data)) echo balanceTags($data);?>
        <div class="post-format-date">
            <i class="fa fa-calendar-o" aria-hidden="true"></i>
            <span><?php echo get_the_date('M.d');?></span>
        </div>
    </div>
    <div class="post-info">
        <h3 class="post-title"><a title="<?php echo esc_attr(get_the_title());?>" href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title()?></a></h3>
        <p class="desc"><?php echo get_the_excerpt(); ?></p>
        <?php s7upf_display_metabox();?>
    </div>
</div>