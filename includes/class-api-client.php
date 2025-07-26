<?php
/**
 * Manages all communication with the Navex API.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Api_Client
 */
class Abcdo_Wc_Navex_Api_Client {

    /**
     * The base URL for the Navex API.
     * @var string
     */
    private const API_BASE_URL = 'https://app.navex.tn/api/';

    /**
     * The API tokens.
     * @var array
     */
    private $tokens = [];

    /**
     * Constructor.
     */
    public function __construct() {
        $this->load_tokens();
    }

    /**
     * Load and decrypt tokens from the database.
     */
    private function load_tokens() {
        $token_options = [
            'add'    => 'abcdo_wc_navex_add_token',
            'get'    => 'abcdo_wc_navex_get_token',
            'delete' => 'abcdo_wc_navex_delete_token',
        ];

        foreach ( $token_options as $key => $option_name ) {
            $encrypted_token = get_option( $option_name );
            if ( ! empty( $encrypted_token ) ) {
                $this->tokens[ $key ] = Abcdo_Wc_Navex_Encryption_Service::decrypt( $encrypted_token );
            } else {
                $this->tokens[ $key ] = '';
            }
        }
    }

    /**
     * Check if a specific token is available.
     *
     * @param string $token_type ('add', 'get', 'delete').
     * @return bool
     */
    public function has_token( $token_type ) {
        return ! empty( $this->tokens[ $token_type ] );
    }

    /**
     * Make a request to the Navex API.
     *
     * @param string $token_type The type of token to use ('add', 'get', 'delete').
     * @param string $endpoint The API endpoint path.
     * @param array  $data The data to send.
     * @param string $method The HTTP method.
     * @return array|WP_Error The response from the API or a WP_Error on failure.
     */
    private function make_request( $token_type, $endpoint, $data = array(), $method = 'POST' ) {
        if ( ! $this->has_token( $token_type ) ) {
            return new WP_Error( 'api_error', __( 'Navex API token is not configured.', 'abcdo-wc-navex' ) );
        }

        $url = self::API_BASE_URL . $this->tokens[ $token_type ] . '/' . $endpoint;

        $args = array(
            'method'  => $method,
            'timeout' => 45,
            'headers' => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'body'    => http_build_query( $data ),
        );

        $response = wp_remote_request( $url, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $decoded_body = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'api_error', __( 'Failed to decode API response.', 'abcdo-wc-navex' ) );
        }

        return $decoded_body;
    }

    /**
     * Create a new shipment.
     *
     * @param array $order_data The order data.
     * @return array|WP_Error
     */
    public function create_shipment( $order_data ) {
        return $this->make_request( 'add', 'v1/post.php', $order_data, 'POST' );
    }

    /**
     * Get shipment status.
     *
     * @param string $tracking_id The tracking ID.
     * @return array|WP_Error
     */
    public function get_shipment_status( $tracking_id ) {
        // Assuming the endpoint requires the tracking ID in the data.
        // The provided documentation is not clear on this.
        $data = array( 'tracking_id' => $tracking_id );
        return $this->make_request( 'get', 'v1/status.php', $data, 'POST' ); // Endpoint needs to be confirmed
    }

    /**
     * Cancel a shipment.
     *
     * @param string $tracking_id The tracking ID.
     * @return array|WP_Error
     */
    public function cancel_shipment( $tracking_id ) {
        $data = array( 'tracking_id' => $tracking_id );
        return $this->make_request( 'delete', 'v1/delete.php', $data, 'POST' ); // Endpoint needs to be confirmed
    }
}
