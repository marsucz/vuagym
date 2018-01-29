<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package 7up-framework
 */
?>
<?php
$sidebar = s7upf_get_sidebar();
if ( is_active_sidebar( $sidebar['id']) && $sidebar['position'] != 'no' ):?>
	<!-- Khoa edit -->
	<div class="col-md-3 col-sm-3 col-xs-12 sidebar">
		<?php dynamic_sidebar($sidebar['id']); ?>
	</div>
<?php endif;?>