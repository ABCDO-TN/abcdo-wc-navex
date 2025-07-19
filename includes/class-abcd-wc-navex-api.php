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
     * Le token d'API pour l'ajout.
     * @var string
     */
    private $token_add;

    /**
     * Le token d'API pour la récupération.
     * @var string
     */
    private $token_get;

    /**
     * Le token d'API pour la suppression.
     * @var string
     */
    private $token_delete;


    /**
     * Constructeur.
     */
    public function __construct() {
        $this->token_add    = get_option( 'abcdo_wc_navex_api_token_add' );
        $this->token_get    = get_option( 'abcdo_wc_navex_api_token_get' );
        $this->token_delete = get_option( 'abcdo_wc_navex_api_token_delete' );
    }

    /**
     * Envoyer un colis à l'API Navex.
     *
     * @param array $data Les données du colis.
     * @return array|WP_Error La réponse de l'API ou une erreur.
     */
    public function send_parcel( $data ) {
        if ( empty( $this->token_add ) ) {
            return new WP_Error( 'api_token_missing', __( 'Le token d\'ajout Navex n\'est pas configuré.', 'abcdo-wc-navex' ) );
        }

        $endpoint = self::$api_url . $this->token_add . '/v1/post.php';

        return $this->make_request( $endpoint, $data );
    }

    /**
     * Récupérer les statuts des colis depuis l'API Navex.
     *
     * @return array|WP_Error La réponse de l'API ou une erreur.
     */
    public function get_parcels_status() {
        if ( empty( $this->token_get ) ) {
            return new WP_Error( 'api_token_missing', __( 'Le token de récupération Navex n\'est pas configuré.', 'abcdo-wc-navex' ) );
        }

        // L'endpoint exact doit être confirmé par la documentation de Navex.
        // C'est une supposition basée sur les conventions.
        $endpoint = self::$api_url . $this->token_get . '/v1/get.php';

        // La méthode est probablement GET, donc le corps est vide.
        return $this->make_request( $endpoint, array(), 'GET' );
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
        $decoded_body = json_decode( $body, true );

        if ( json_last_error() !== JSON_ERROR_NONE ) {
            return new WP_Error( 'invalid_json', __( 'Réponse JSON invalide de l\'API Navex.', 'abcdo-wc-navex' ), array( 'body' => $body ) );
        }

        return $decoded_body;
    }
}
