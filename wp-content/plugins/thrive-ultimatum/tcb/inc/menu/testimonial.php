<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden
} ?>

<div id="tve-testimonial-component" class="tve-component" data-view="Testimonial">
	<div class="borders-options action-group">
		<div class="dropdown-header" data-prop="docked">
			<div class="group-description">
				<?php echo __( 'Testimonial', 'thrive-cb' ); ?>
			</div>
			<i></i>
		</div>
		<div class="dropdown-content">
			<div class="row padding-top-10">
				<div class="col-xs-12">
					<button class="tve-button click blue" data-fn="change_style"><?php echo __( 'Change style', 'thrive-cb' ); ?></button>
				</div>
			</div>
		</div>
	</div>
</div>

