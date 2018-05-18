<?php

add_shortcode('thumbnail', 'kacustom_insert_post_thumbnail_into_content');

function kacustom_insert_post_thumbnail_into_content($atts) {
    extract(shortcode_atts(array(
        'size' => 'post-thumbnail', // any of the possible post thumbnail sizes - defaults to 'thumbnail'
        'align' => 'none' // any of the alignments 'left', 'right', 'center', 'none' - defaults to 'none'
                    ), $atts));
    global $post;

    if (!get_post_thumbnail_id($post->ID))
        return false; //no thumbnail found

        
//alignment check
    if (!in_array($align, array('left', 'right', 'center', 'none')))
        $align = 'none';
    $align = 'align' . $align;

//thumbnail size check
    if (!(preg_match('|array\((([ 0-9])+,([ 0-9])+)\)|', $size) === 1) && !in_array($size, get_intermediate_image_sizes()))
        $size = 'post-thumbnail';
    if (preg_match('|array\((([ 0-9])+,([ 0-9])+)\)|', $size, $match) === 1)
        $sizewh = explode(',', $match[1]);
    $size = array(trim($sizewh[0]), trim($sizewh[1]));

//get the post thumbnail
    $thumbnail = get_the_post_thumbnail($post->ID, $size);

//integrate the alignment class
    $thumbnail = str_replace('class="', 'class="' . $align . ' ', $thumbnail); //add alignment class

    return $thumbnail;
}
