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
class Abcdo_Wc_Navex_Api {

    /**
     * L'URL de base de l'API Navex.
     *
     * @var string
     */
    private static $api_url = 'https://app.navex.tn/api/';

    /**
     * The single API token.
     * @var string
     */
    private $api_token;

    /**
     * Constructeur.
     */
    public function __construct() {
        $token_manager = new Abcdo_Wc_Navex_Token_Manager();
        $this->api_token = $token_manager->get_decrypted_token();
    }

    /**
     * Envoyer un colis à l'API Navex.
     *
     * @param array $data Les données du colis.
     * @return array|WP_Error La réponse de l'API ou une erreur.
     */
    public function send_parcel( $data ) {
        if ( empty( $this->api_token ) ) {
            return new WP_Error( 'api_token_missing', __( 'The Navex API Key is not configured.', 'abcdo-wc-navex' ) );
        }

        $endpoint = self::$api_url . $this->api_token . '/v1/post.php';

        return $this->make_request( $endpoint, $data );
    }

    /**
     * Récupérer les détails d'un colis depuis l'API Navex.
     *
     * @param string $tracking_id L'ID de suivi du colis.
     * @return array|WP_Error La réponse de l'API ou une erreur.
     */
    public function get_parcel_details( $tracking_id ) {
        if ( empty( $this->api_token ) ) {
            return new WP_Error( 'api_token_missing', __( 'The Navex API Key is not configured.', 'abcdo-wc-navex' ) );
        }

        // FIX: The correct endpoint for getting parcel details uses the tracking ID in the path.
        $endpoint = self::$api_url . $this->api_token . '/v1/get/' . urlencode( $tracking_id );

        return $this->make_request( $endpoint, array(), 'GET' );
    }

    /**
     * Supprimer un colis via l'API Navex.
     *
     * @param string $tracking_id L'ID de suivi du colis.
     * @return array|WP_Error La réponse de l'API ou une erreur.
     */
    public function delete_parcel( $tracking_id ) {
        if ( empty( $this->api_token ) ) {
            return new WP_Error( 'api_token_missing', __( 'The Navex API Key is not configured.', 'abcdo-wc-navex' ) );
        }

        $endpoint = self::$api_url . $this->api_token . '/v1/delete/' . urlencode( $tracking_id );

        return $this->make_request( $endpoint, array(), 'DELETE' );
    }

    /**
     * Fonction utilitaire pour effectuer les requêtes à l'API.
     *
     * @param string $endpoint L'URL complète de l'endpoint.
     * @param array  $data Les données à envoyer.
     * @param string $method La méthode HTTP (POST, GET).
     * @return array|WP_Error La réponse de l'API ou une erreur.
     */
    private function make_request( $endpoint, $data = array(), $method = 'POST' ) {
        $args = array(
            'method'    => $method,
            'headers'   => array(
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
            ),
            'timeout'   => 45,
        );

        if ( 'POST' === $method && ! empty( $data ) ) {
            $args['body'] = json_encode( $data );
        }

        $response = wp_remote_request( $endpoint, $args );

        if ( is_wp_error( $response ) ) {
            return $response;
        }

        $body = wp_remote_retrieve_body( $response );
        $decoded_body = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            // Pour le débogage, on peut retourner le corps brut en cas d'erreur JSON
            return new WP_Error( 'invalid_json', __( 'Réponse JSON invalide de l\'API Navex.', 'abcdo-wc-navex' ), array( 'body' => $body ) );
        }

        return $decoded_body;
    }
}
