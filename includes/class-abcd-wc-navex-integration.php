<?php
/**
 * Fichier pour l'intégration principale avec WooCommerce.
 *
 * @package Abcdo_Wc_Navex
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Classe d'intégration ABCDO Navex pour WooCommerce.
 */
class ABCD_WC_Navex_Integration extends WC_Integration {

    /**
     * Le token d'API.
     *
     * @var string
     */
    public $api_token;

    /**
     * Constructeur.
     */
    public function __construct() {
        global $woocommerce;

        $this->id                 = 'abcdo-wc-navex';
        $this->method_title       = __( 'ABCDO Navex Integration', 'abcdo-wc-navex' );
        $this->method_description = __( 'Intégration pour connecter WooCommerce à l\'API de livraison Navex.', 'abcdo-wc-navex' );

        // Charger les réglages.
        $this->init_form_fields();
        $this->init_settings();

        // Définir les variables.
        $this->api_token = $this->get_option( 'api_token' );

        // Actions.
        add_action( 'woocommerce_update_options_integration_' . $this->id, array( $this, 'process_admin_options' ) );
    }

    /**
     * Initialiser les champs du formulaire de réglages.
     */
    public function init_form_fields() {
        $this->form_fields = array(
            'api_token' => array(
                'title'       => __( 'Token d\'API Navex', 'abcdo-wc-navex' ),
                'type'        => 'text',
                'description' => __( 'Entrez votre token d\'authentification fourni par Navex. Il ressemble à `finesseratn-XXXXXXXXXXXXXXXX`', 'abcdo-wc-navex' ),
                'desc_tip'    => true,
                'default'     => '',
            ),
        );
    }
}
