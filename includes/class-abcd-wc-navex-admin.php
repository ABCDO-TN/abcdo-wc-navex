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

        // Charger les scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // Charger les traductions au bon moment
        add_action( 'init', array( $this, 'load_textdomain' ) );
    }

    /**
     * Charge les traductions du plugin.
     */
    public function load_textdomain() {
        load_plugin_textdomain( 'abcdo-wc-navex', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
    }

    /**
     * Charger les scripts et styles pour l'admin (compatible HPOS).
     */
    public function enqueue_scripts() {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return;
        }

        $hpos_enabled = false;
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) && method_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil', 'custom_order_tables_is_enabled' ) ) {
            $hpos_enabled = \Automattic\WooCommerce\Utilities\FeaturesUtil::custom_order_tables_is_enabled();
        }
        
        // Vérifier si nous sommes sur la bonne page de commande (traditionnelle ou HPOS)
        if ( ( $hpos_enabled && $screen->id === wc_get_page_screen_id( 'shop-order' ) ) || ( ! $hpos_enabled && $screen->id === 'shop_order' ) ) {
            wp_enqueue_script(
                'abcd-wc-navex-admin-js',
                ABCDO_WC_NAVEX_URL . 'assets/js/admin.js',
                array( 'jquery' ),
                ABCDO_WC_NAVEX_VERSION,
                true
            );
        }
    }

    /**
     * Ajouter le meta box sur la page de commande (compatible HPOS).
     */
    public function add_navex_meta_box() {
        $screen = 'shop_order';
        if ( class_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil' ) && method_exists( '\Automattic\WooCommerce\Utilities\FeaturesUtil', 'custom_order_tables_is_enabled' ) ) {
            if ( \Automattic\WooCommerce\Utilities\FeaturesUtil::custom_order_tables_is_enabled() ) {
                $screen = wc_get_page_screen_id( 'shop-order' );
            }
        }

        add_meta_box(
            'abcd-wc-navex-meta-box',
            __( 'ABCDO Navex Shipping', 'abcdo-wc-navex' ),
            array( $this, 'render_meta_box_content' ),
            $screen,
            'side',
            'core'
        );
    }

    /**
     * Afficher le contenu du meta box (compatible HPOS).
     *
     * @param WP_Post|WC_Order $post_or_order_object L'objet post ou commande.
     */
    public function render_meta_box_content( $post_or_order_object ) {
        $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;

        if ( ! $order ) {
            return;
        }

        // Ajouter un nonce pour la sécurité
        wp_nonce_field( 'abcd_wc_navex_send_parcel_action', 'abcd_wc_navex_nonce' );

        $order_id = $order->get_id();
        $status = $order->get_meta( '_navex_shipping_status' );

        echo '<p>';
        if ( ! empty( $status ) ) {
            echo '<strong>' . esc_html__( 'Statut Navex :', 'abcdo-wc-navex' ) . '</strong> ' . esc_html( $status );
        } else {
            echo esc_html__( 'La commande n\'a pas encore été envoyée à Navex.', 'abcdo-wc-navex' );
        }
        echo '</p>';

        // Ne pas afficher le bouton si la commande a déjà été envoyée
        if ( 'Envoyé' !== $status ) {
            echo '<button type="button" id="abcd-wc-navex-send-btn" class="button button-primary" data-order-id="' . esc_attr( $order_id ) . '">' . esc_html__( 'Envoyer à Navex', 'abcdo-wc-navex' ) . '</button>';
            echo '<span class="spinner" style="float: none; margin-top: 4px;"></span>';
        }
    }

    /**
     * Gérer la requête AJAX pour l'envoi manuel.
     */
    public function ajax_send_parcel() {
        // Vérifier le nonce et les permissions
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'abcd_wc_navex_send_parcel_action' ) ) {
            wp_send_json_error( array( 'message' => 'Échec de la vérification de sécurité.' ) );
        }
        if ( ! current_user_can( 'edit_shop_orders' ) ) {
            wp_send_json_error( array( 'message' => 'Vous n\'avez pas les permissions nécessaires.' ) );
        }
        if ( ! isset( $_POST['order_id'] ) ) {
            wp_send_json_error( array( 'message' => 'ID de commande manquant.' ) );
        }

        $order_id = intval( $_POST['order_id'] );
        $order = wc_get_order( $order_id );

        if ( ! $order ) {
            wp_send_json_error( array( 'message' => 'Commande non trouvée.' ) );
        }

        // Préparer les données pour l'API Navex
        $data = array(
            'prix'        => $order->get_total(),
            'nom'         => $order->get_formatted_shipping_full_name(),
            'gouvernerat' => $order->get_shipping_state(),
            'ville'       => $order->get_shipping_city(),
            'adresse'     => $order->get_shipping_address_1(),
            'tel'         => $order->get_billing_phone(),
            'tel2'        => '',
            'designation' => 'Commande #' . $order->get_order_number(),
            'nb_article'  => $order->get_item_count(),
            'msg'         => $order->get_customer_note(),
            'echange'     => '',
            'article'     => '',
            'nb_echange'  => '',
            'ouvrir'      => 'Non', // ou 'Oui' selon la configuration
        );

        // Envoyer les données à l'API
        $api = new ABCD_WC_Navex_API();
        $response = $api->send_parcel( $data );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => $response->get_error_message() ) );
        }

        // Vérifier la réponse de l'API
        if ( isset( $response['status'] ) && $response['status_message'] === 'Product Added.' ) {
            // Mettre à jour le statut dans les métadonnées de la commande (compatible HPOS)
            $order->update_meta_data( '_navex_shipping_status', 'Envoyé' );
            $order->add_order_note( 'Colis envoyé à Navex avec succès.' );
            $order->save();
            wp_send_json_success( array( 'message' => 'Colis envoyé à Navex avec succès !' ) );
        } else {
            $error_message = isset( $response['status_message'] ) ? $response['status_message'] : 'Erreur inconnue de l\'API Navex.';
            $order->add_order_note( 'Erreur lors de l\'envoi à Navex : ' . $error_message );
            wp_send_json_error( array( 'message' => $error_message ) );
        }
    }
}
