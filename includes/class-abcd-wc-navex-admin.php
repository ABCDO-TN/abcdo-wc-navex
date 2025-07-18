<?php
/**
 * Fichier pour la gestion de l'admin.
 *
 * @package Abcdo_Wc_Navex
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Classe pour la gestion de l'interface d'administration.
 */
class ABCD_WC_Navex_Admin {

    /**
     * Constructeur.
     */
    public function __construct() {
        // Ajouter le meta box sur la page de commande
        add_action( 'add_meta_boxes', array( $this, 'add_navex_meta_box' ) );

        // Gérer l'action d'envoi manuel
        add_action( 'wp_ajax_abcd_wc_navex_send_parcel', array( $this, 'ajax_send_parcel' ) );
    }

    /**
     * Ajouter le meta box sur la page de commande.
     */
    public function add_navex_meta_box() {
        add_meta_box(
            'abcd-wc-navex-meta-box',
            __( 'ABCDO Navex Shipping', 'abcdo-wc-navex' ),
            array( $this, 'render_meta_box_content' ),
            'shop_order',
            'side',
            'core'
        );
    }

    /**
     * Afficher le contenu du meta box.
     *
     * @param WP_Post $post L'objet post de la commande.
     */
    public function render_meta_box_content( $post ) {
        // Ajouter un nonce pour la sécurité
        wp_nonce_field( 'abcd_wc_navex_send_parcel_action', 'abcd_wc_navex_nonce' );

        $order_id = $post->ID;
        $status = get_post_meta( $order_id, '_navex_shipping_status', true );

        echo '<p>';
        if ( ! empty( $status ) ) {
            echo '<strong>' . esc_html__( 'Statut Navex :', 'abcdo-wc-navex' ) . '</strong> ' . esc_html( $status );
        } else {
            echo esc_html__( 'La commande n\'a pas encore été envoyée à Navex.', 'abcdo-wc-navex' );
        }
        echo '</p>';

        echo '<button type="button" id="abcd-wc-navex-send-btn" class="button button-primary" data-order-id="' . esc_attr( $order_id ) . '">' . esc_html__( 'Envoyer à Navex', 'abcdo-wc-navex' ) . '</button>';
        echo '<span class="spinner"></span>';
    }

    /**
     * Gérer la requête AJAX pour l'envoi manuel.
     */
    public function ajax_send_parcel() {
        // Vérifier le nonce
        check_ajax_referer( 'abcd_wc_navex_send_parcel_action', 'nonce' );

        // Logique d'envoi à implémenter
        
        wp_send_json_success( array( 'message' => 'Colis envoyé avec succès !' ) );
    }
}
