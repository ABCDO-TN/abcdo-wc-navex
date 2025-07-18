<?php
/**
 * Fichier pour le client API Navex.
 *
 * @package Abcdo_Wc_Navex
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Classe pour communiquer avec l'API Navex.
 */
class ABCD_WC_Navex_API {

    /**
     * L'URL de base de l'API Navex.
     *
     * @var string
     */
    private static $api_url = 'https://app.navex.tn/api/';

    /**
     * Le token d'API.
     *
     * @var string
     */
    private $api_token;

    /**
     * Constructeur.
     */
    public function __construct() {
        $this->api_token = get_option( 'abcdo_wc_navex_api_token' );
    }

    /**
     * Envoyer un colis à l'API Navex.
     *
     * @param array $data Les données du colis.
     * @return array|WP_Error La réponse de l'API ou une erreur.
     */
    public function send_parcel( $data ) {
        if ( empty( $this->api_token ) ) {
            return new WP_Error( 'api_token_missing', __( 'Le token d\'API Navex n\'est pas configuré.', 'abcdo-wc-navex' ) );
        }

        $endpoint = self::$api_url . $this->api_token . '/v1/post.php';

        $response = wp_remote_post( $endpoint, array(
            'method'    => 'POST',
            'body'      => http_build_query( $data ),
            'headers'   => array(
                'Content-Type' => 'application/x-www-form-urlencoded',
            ),
            'timeout'   => 45,
        ) );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $decoded_body = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'invalid_json', __( 'Réponse JSON invalide de l\'API Navex.', 'abcdo-wc-navex' ), array( 'body' => $body ) );
        }

        return $decoded_body;
    }
}
