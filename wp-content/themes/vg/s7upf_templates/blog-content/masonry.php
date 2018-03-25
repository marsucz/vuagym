<?php
$data = '';
$rad = rand(1,9);
if($rad % 3 == 1) $size = array(300,300);
if($rad % 3 == 2) $size = array(300,200);
if($rad % 3 == 0) $size = array(300,150);
if (has_post_thumbnail()) {
    $data .=    '<div class="post-thumb">
                    <a class="post-thumb-link" href="'. esc_url(get_the_permalink()) .'">'.get_the_post_thumbnail(get_the_ID(),$size).'</a>                
                </div>';
}
?>
<div class="item-post-masonry">
    <div class="post-item">
        <h3 class="post-title"><a title="<?php echo esc_attr(get_the_title());?>" href="<?php echo esc_url(get_the_permalink()); ?>"><?php the_title()?></a></h3>
        <?php if(!empty($data)) echo balanceTags($data);?>
        <div class="post-info">
            <ul class="post-date-comment">
                <li><i aria-hidden="true" class="fa fa-clock-o"></i><span><?php echo get_the_date('M.d. Y');?></span></li>
                <li><i aria-hidden="true" class="fa fa-comment"></i><a href="<?php echo esc_url(get_comments_link());?>"><?php echo get_comments_number();?> <?php esc_html_e("Comments","kuteshop")?> </a></li>
            </ul>
            <p class="desc"><?php echo s7upf_substr(get_the_excerpt(),0,90); ?></p>
            <a href="<?php echo esc_url(get_the_permalink()); ?>" class="readmore"><?php esc_html_e("Read more","kuteshop")?></a>
        </div>
    </div>
</div>