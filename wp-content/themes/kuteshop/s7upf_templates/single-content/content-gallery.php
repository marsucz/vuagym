<?php
$data = '';
$gallery = get_post_meta(get_the_ID(), 'format_gallery', true);
if (!empty($gallery)){
    $array = explode(',', $gallery);
    if(is_array($array) && !empty($array)){
        
        $data .=    '<div class="wrap-item smart-slider" data-item="1" data-speed="'.esc_attr($speed).'" data-itemres="0:1" data-prev="" data-next="" data-pagination="" data-navigation="true">';
        foreach ($array as $key => $item) {
            $thumbnail_url = wp_get_attachment_url($item);
            $data .=    '<div class="post-thumb-link">';
            $data .=    '<img src="' . esc_url($thumbnail_url) . '" alt="image-slider">';           
            $data .=    '</div>';
        }
        $data .=    '</div>';
    }
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