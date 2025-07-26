<?php
/**
 * Handles data migration for plugin updates.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Migration
 */
class Abcdo_Wc_Navex_Migration {

    /**
     * Run the migration checks.
     */
    public static function run() {
        $migrated = get_option( 'abcdo_wc_navex_migrated_to_1_2_0', false );
        if ( $migrated ) {
            return;
        }

        self::migrate_token_from_custom_table();
        update_option( 'abcdo_wc_navex_migrated_to_1_2_0', true );
    }

    /**
     * Migrate the token from the old custom table to the new options-based storage.
     */
    private static function migrate_token_from_custom_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'abcdo_wc_navex_tokens';

        // Check if the old table exists
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) ) !== $table_name ) {
            return; // Table doesn't exist, nothing to migrate.
        }

        // Check if there's a token in the old table
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $token_row = $wpdb->get_row( "SELECT * FROM {$table_name} LIMIT 1" );

        if ( $token_row && ! empty( $token_row->api_token ) ) {
            // Decrypt using the old method (which is the same as the new one)
            $decrypted_token = Abcdo_Wc_Navex_Encryption_Service::decrypt( $token_row->api_token );

            if ( $decrypted_token ) {
                // Encrypt and save to the new option
                $encrypted_token_new = Abcdo_Wc_Navex_Encryption_Service::encrypt( $decrypted_token );
                update_option( 'abcdo_wc_navex_add_token', $encrypted_token_new );
                Abcdo_Wc_Navex_Logger::log( 'Successfully migrated API token from custom table to wp_options.' );
            }
        }

        // Drop the old table
        // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
        $wpdb->query( "DROP TABLE IF EXISTS {$table_name}" );
    }
}
