<?php
/**
 * Navex API client file.
 *
 * @package Abcdo_Wc_Navex
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class to communicate with the Navex API.
 */
class ABCD_WC_Navex_API {

    /**
     * The base URL for the Navex API.
     *
     * @var string
     */
    private static $api_url = 'https://app.navex.tn/api/';

    /**
     * The API token for adding parcels.
     * @var string
     */
    private $token_add;

    /**
     * The API token for retrieving parcels.
     * @var string
     */
    private $token_get;

    /**
     * The API token for deleting parcels.
     * @var string
     */
    private $token_delete;


    /**
     * Constructor.
     */
    public function __construct() {
        // Tokens are encrypted in the DB, decrypt them here.
        $this->token_add    = ABCD_WC_Navex_Crypto::decrypt( get_option( 'abcdo_wc_navex_api_token_add' ) );
        $this->token_get    = ABCD_WC_Navex_Crypto::decrypt( get_option( 'abcdo_wc_navex_api_token_get' ) );
        $this->token_delete = ABCD_WC_Navex_Crypto::decrypt( get_option( 'abcdo_wc_navex_api_token_delete' ) );
    }

    /**
     * Send a parcel to the Navex API.
     *
     * @param array $data The parcel data.
     * @return array|WP_Error The API response or an error.
     */
    public function send_parcel( $data ) {
        if ( empty( $this->token_add ) ) {
            return new WP_Error( 'api_token_missing', __( 'Navex Add Token is not configured.', 'abcdo-wc-navex' ) );
        }

        $endpoint = self::$api_url . $this->token_add . '/v1/post.php';

        return $this->make_request( $endpoint, $data );
    }

    /**
     * Get a parcel's details from the Navex API.
     *
     * @param string $tracking_id The parcel's tracking ID.
     * @return string|WP_Error The API response or an error.
     */
    public function get_parcel_details( $tracking_id ) {
        if ( empty( $this->token_get ) ) {
            return new WP_Error( 'api_token_missing', __( 'Navex Get Token is not configured.', 'abcdo-wc-navex' ) );
        }

        // The exact endpoint needs to be confirmed. This is a guess.
        // We add the tracking_id to the URL.
        $endpoint = self::$api_url . $this->token_get . '/v1/get.php?tracking_id=' . urlencode( $tracking_id );

        return $this->make_request( $endpoint, array(), 'GET' );
    }

    /**
     * Utility function to make requests to the API.
     *
     * @param string $endpoint The full endpoint URL.
     * @param array  $data The data to send.
     * @param string $method The HTTP method (POST, GET).
     * @return array|string|WP_Error The API response or an error.
     */
    private function make_request( $endpoint, $data = array(), $method = 'POST' ) {
        $args = array(
            'method'    => $method,
            'headers'   => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'timeout'   => 45,
        );

        if ( 'POST' === $method && ! empty( $data ) ) {
            $args['body'] = http_build_query( $data );
        }

        $response = wp_remote_request( $endpoint, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        
        // For the get_parcel_details method, the response is not JSON.
        // We check if the request comes from there to return the raw body.
        if ( strpos( $endpoint, '/get.php' ) !== false ) {
            return $body;
        }

        $decoded_body = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'invalid_json', __( 'Invalid JSON response from Navex API.', 'abcdo-wc-navex' ), array( 'body' => $body ) );
        }

        return $decoded_body;
    }
}
