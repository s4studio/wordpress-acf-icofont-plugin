<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die;
}

delete_option( 'ACFICFNT_icon_data' );
delete_option( 'ACFICFNT_current_version' );

$timestamp = wp_next_scheduled( 'ACFICFNT_refresh_latest_icons' );

if ( $timestamp ) {
	wp_unschedule_event( $timestamp, 'ACFICFNT_refresh_latest_icons' );
}
