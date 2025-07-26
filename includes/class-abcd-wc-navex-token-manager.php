<?php
/**
 * Manages the API token logic: saving, retrieving, deleting, and crypto operations.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Token_Manager
 */
class Abcdo_Wc_Navex_Token_Manager {

    /**
     * @var Abcdo_Wc_Navex_Database_Manager
     */
    private $db_manager;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->db_manager = new Abcdo_Wc_Navex_Database_Manager();
    }

    /**
     * Get the decrypted API token.
     *
     * @since 1.1.0
     * @return string The decrypted token, or an empty string if not found or on error.
     */
    public function get_decrypted_token() {
        $token_row = $this->db_manager->get_token_row();
        if ( ! $token_row || empty( $token_row->api_token ) ) {
            return '';
        }

        $decrypted = Abcdo_Wc_Navex_Crypto::decrypt( $token_row->api_token );

        return $decrypted ? $decrypted : '';
    }

    /**
     * Save the API token.
     *
     * @since 1.1.0
     * @param string $raw_token The raw API token to save.
     * @return bool True on success, false on failure.
     */
    public function save_token( $raw_token ) {
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }

        $encrypted_token = Abcdo_Wc_Navex_Crypto::encrypt( $raw_token );
        if ( false === $encrypted_token ) {
            return false;
        }

        return $this->db_manager->save_token( $encrypted_token, get_current_user_id() );
    }

    /**
     * Delete the API token.
     *
     * @since 1.1.0
     * @return bool True on success, false on failure.
     */
    public function delete_token() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }
        return $this->db_manager->delete_token();
    }

    /**
     * Check if a token is currently saved in the database.
     *
     * @since 1.1.0
     * @return bool True if a token exists, false otherwise.
     */
    public function is_token_saved() {
        $token_row = $this->db_manager->get_token_row();
        return ! empty( $token_row );
    }
}
