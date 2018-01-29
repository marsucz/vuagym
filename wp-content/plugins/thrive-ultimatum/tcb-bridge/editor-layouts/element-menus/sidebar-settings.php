<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden!
}

global $design;
if ( empty( $design ) ) {
	$design = tve_ult_get_design( $_REQUEST[ TVE_Ult_Const::DESIGN_QUERY_KEY_NAME ] );
}
$design_type_details = TVE_Ult_Const::design_details( $design['post_type'] );
?>

<div class="settings-list col-xs-12" data-list="templates" style="display: none;">
	<div class="setting-item click" data-fn="setting" data-setting="tve_ult_choose_template">
		<?php tcb_icon( 'change_lp' ); ?>
		<?php echo sprintf( __( 'Change %s Template', TVE_Ult_Const::T ), $design_type_details['name'] ); ?>
	</div>
	<div class="setting-item click" data-fn="setting" data-setting="tve_ult_save_template">
		<?php tcb_icon( 'save_usertemp' ); ?>
		<?php echo sprintf( __( 'Save %s Template', TVE_Ult_Const::T ), $design_type_details['name'] ); ?>
	</div>
	<div class="setting-item click" data-fn="setting" data-setting="tve_ult_reset_template">
		<?php tcb_icon( 'revert2theme' ); ?>
		<?php echo sprintf( __( 'Reset %s Template', TVE_Ult_Const::T ), $design_type_details['name'] ); ?>
	</div>
</div>
