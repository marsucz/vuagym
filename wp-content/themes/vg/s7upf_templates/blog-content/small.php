<?php
$data = '';
$size = array(360,360);
if (has_post_thumbnail()) {
    $data .=    '<div class="post-thumb">
                    <a class="post-thumb-link" href="'. esc_url(get_the_permalink()) .'">'.get_the_post_thumbnail(get_the_ID(),$size).'</a>                
                </div>';
}
?>
<div class="item-post-small">
    <div class="row">
        <div class="col-md-5 col-sm-6 col-xs-12">
            <?php if(!empty($data)) echo balanceTags($data);?>
        </div>
        <div class="col-md-7 col-sm-6 col-xs-12">
            <div class="post-info">
                <ul class="post-date-comment">
                    <li><i class="fa fa-calendar-o" aria-hidden="true"></i><span><?php echo get_the_date('M.d.Y');?></span></li>
                </ul>
                <h3 class="post-title"><a title="<?php echo esc_attr(get_the_title());?>" href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title()?></a></h3>
                <p class="desc"><?php echo get_the_excerpt(); ?></p>
                <?php s7upf_display_metabox();?>
            </div>
        </div>
    </div>
</div>