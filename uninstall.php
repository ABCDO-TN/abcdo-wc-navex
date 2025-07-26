<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.0
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

// Delete the migration flag option.
delete_option( 'abcdo_wc_navex_migration_complete_v1_1_0' );

// Note: The custom table 'abcdo_wc_navex_tokens' is intentionally not deleted
// to prevent data loss on accidental deactivation/reactivation.
