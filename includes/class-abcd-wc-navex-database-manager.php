<?php
/**
 * Manages database interactions, including table creation and CRUD operations for the token.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Database_Manager
 */
class Abcdo_Wc_Navex_Database_Manager {

    /**
     * The name of the custom table.
     *
     * @var string
     */
    private static $table_name;

    /**
     * Initialize the manager.
     */
    public function __construct() {
        global $wpdb;
        self::$table_name = $wpdb->prefix . 'abcdo_wc_navex_tokens';
    }

    /**
     * Create the custom database table on plugin activation.
     *
     * @since 1.1.0
     */
    public static function create_table() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'abcdo_wc_navex_tokens';
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            api_token TEXT NOT NULL,
            user_id_last_updated BIGINT(20) UNSIGNED NOT NULL,
            last_updated_at DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) $charset_collate;";

        require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        dbDelta( $sql );
    }

    /**
     * Get the saved token row from the database.
     *
     * @since 1.1.0
     * @return object|null The token row object or null if not found.
     */
    public function get_token_row() {
        global $wpdb;
        return $wpdb->get_row( "SELECT * FROM " . self::$table_name . " LIMIT 1" );
    }

    /**
     * Save or update the token in the database.
     *
     * @since 1.1.0
     * @param string $encrypted_token The encrypted API token.
     * @param int    $user_id The ID of the user saving the token.
     * @return bool True on success, false on failure.
     */
    public function save_token( $encrypted_token, $user_id ) {
        global $wpdb;

        $existing_token = $this->get_token_row();

        $data = array(
            'api_token'            => $encrypted_token,
            'user_id_last_updated' => $user_id,
            'last_updated_at'      => current_time( 'mysql' ),
        );

        if ( $existing_token ) {
            // Update existing token
            $result = $wpdb->update( self::$table_name, $data, array( 'id' => $existing_token->id ) );
        } else {
            // Insert new token
            $result = $wpdb->insert( self::$table_name, $data );
        }

        return $result !== false;
    }

    /**
     * Delete the token from the database.
     *
     * @since 1.1.0
     * @return bool True on success, false on failure.
     */
    public function delete_token() {
        global $wpdb;
        $result = $wpdb->query( "TRUNCATE TABLE " . self::$table_name );
        return $result !== false;
    }
}
