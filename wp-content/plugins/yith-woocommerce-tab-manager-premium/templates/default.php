<?php

$content = wpautop( htmlspecialchars_decode( str_replace( '\\','', $content ) )  );
?>

<div class="tab-editor-container ywtm_content_tab"> <?php echo do_shortcode( $content ); ?></div>