<?php
/**
 * Thrive Themes - https://thrivethemes.com
 *
 * @package thrive-visual-editor
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}
?>

<h2 class="tcb-modal-title"><?php echo __( 'Choose Testimonial Display Template', 'thrive-cb' ) ?></h2>

<div class="tve-templates-wrapper">
	<div class="tve-tabs-content">
		<div class="tve-tab-content active" data-content="default">
			<div class="tve-default-templates-list">
				<div class="expanded-set" data-set="blank">

				</div>
				<div class="tve-template-preview"></div>
			</div>
		</div>
	</div>
</div>

<div class="tcb-modal-footer clearfix padding-top-20 row end-xs">
	<div class="col col-xs-12">
		<button type="button" class="tcb-right tve-button medium green tcb-modal-save">
			<?php echo __( 'Choose Style', 'thrive-cb' ) ?>
		</button>
	</div>
</div>

