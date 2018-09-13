<?php
/**
 * Migration for creating required table for emails log
 */
defined( 'TVE_ULT_DB_UPGRADING' ) or exit( '1.0' );

/** @var $wpdb WP_Query */
global $wpdb;

$emails_table = tve_ult_table_name( 'emails' );

$sqls = array();

$sqls[] = "UPDATE {$emails_table} SET `email` = MD5(`email`)";

foreach ( $sqls as $sql ) {
	if ( $wpdb->query( $sql ) === false ) {
		return false;
	}
}

return true;
