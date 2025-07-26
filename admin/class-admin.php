<?php
/**
 * Fichier pour la gestion de l'admin.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Classe pour la gestion de l'interface d'administration.
 */
class Abcdo_Wc_Navex_Admin {

    /**
     * L'icône du menu encodée en base64.
     *
     * @var string
     */
    private $menu_icon;

    /**
     * @var Abcdo_Wc_Navex_Api_Client
     */
    private $api_client;

    /**
     * Constructeur.
     */
    public function __construct() {
        $this->api_client = new Abcdo_Wc_Navex_Api_Client();

        // Définir l'icône du menu
        $this->menu_icon = 'data:image/svg+xml;base64,' . base64_encode(
            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L4 6L12 10L20 6L12 2Z" fill="black"/><path d="M4 18L12 22L20 18" fill="black"/><path d="M4 12L12 16L20 12" fill="black"/></svg>'
        );

        // Hooks principaux
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

        // Meta box sur la page de commande
        add_action( 'add_meta_boxes', array( $this, 'add_navex_meta_box' ) );

        // Actions AJAX
        add_action( 'wp_ajax_abcdo_wc_navex_send_parcel', array( $this, 'ajax_send_parcel' ) );
        add_action( 'wp_ajax_abcdo_wc_navex_get_parcels', array( $this, 'ajax_get_parcels' ) );
        add_action( 'wp_ajax_abcdo_wc_navex_get_parcel_details', array( $this, 'ajax_get_parcel_details' ) );
        add_action( 'wp_ajax_abcdo_wc_navex_delete_parcel', array( $this, 'ajax_delete_parcel' ) );
    }

    /**
     * Ajouter le menu au tableau de bord.
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Navex Dashboard', 'abcdo-wc-navex' ),
            __( 'Navex Delivery', 'abcdo-wc-navex' ),
            'manage_options',
            'abcdo-wc-navex',
            array( $this, 'render_dashboard_page' ),
            $this->menu_icon,
            56
        );
    }

    /**
     * Afficher la page du tableau de bord des colis.
     */
    public function render_dashboard_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <p><?php esc_html_e( 'Real-time tracking of your Navex parcels.', 'abcdo-wc-navex' ); ?></p>
            
            <table id="navex-parcels-table" class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th><?php esc_html_e( 'Order ID', 'abcdo-wc-navex' ); ?></th>
                        <th><?php esc_html_e( 'Navex Tracking ID', 'abcdo-wc-navex' ); ?></th>
                        <th><?php esc_html_e( 'Status', 'abcdo-wc-navex' ); ?></th>
                        <th><?php esc_html_e( 'Shipping Date', 'abcdo-wc-navex' ); ?></th>
                        <th><?php esc_html_e( 'Actions', 'abcdo-wc-navex' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td colspan="5"><?php esc_html_e( 'Loading parcels...', 'abcdo-wc-navex' ); ?></td></tr>
                </tbody>
            </table>
        </div>

        <!-- Modal pour les détails du colis -->
        <div id="navex-details-modal" style="display:none;">
            <div id="navex-details-modal-backdrop"></div>
            <div id="navex-details-modal-content">
                <h2><?php esc_html_e( 'Parcel Details', 'abcdo-wc-navex' ); ?></h2>
                <div id="navex-modal-body"><span class="spinner is-active"></span></div>
                <button id="navex-modal-close" class="button button-secondary"><?php esc_html_e( 'Close', 'abcdo-wc-navex' ); ?></button>
            </div>
        </div>
        <?php
    }

    /**
     * Ajouter le meta box sur la page de commande.
     */
    public function add_navex_meta_box() {
        $screens = array_unique( array( 'shop_order', wc_get_page_screen_id( 'shop-order' ) ) );
        foreach ( $screens as $screen ) {
            add_meta_box(
                'abcdo-wc-navex-meta-box',
                __( 'ABCDO Navex Shipping', 'abcdo-wc-navex' ),
                array( $this, 'render_meta_box_content' ),
                $screen,
                'side',
                'default'
            );
        }
    }

    /**
     * Afficher le contenu du meta box.
     */
    public function render_meta_box_content( $post_or_order_object ) {
        $order = ( $post_or_order_object instanceof WP_Post ) ? wc_get_order( $post_or_order_object->ID ) : $post_or_order_object;
        if ( ! $order ) {
            return;
        }

        $order_id = $order->get_id();
        $status = $order->get_meta( '_navex_shipping_status' );

        echo '<p>';
        if ( ! empty( $status ) ) {
            echo '<strong>' . esc_html__( 'Navex Status:', 'abcdo-wc-navex' ) . '</strong> ' . esc_html( $status );
        } else {
            echo esc_html__( 'Order has not been sent to Navex yet.', 'abcdo-wc-navex' );
        }
        echo '</p>';

        if ( 'Envoyé' !== $status ) {
            echo '<button type="button" id="abcdo-wc-navex-send-btn" class="button button-primary" data-order-id="' . esc_attr( $order_id ) . '">' . esc_html__( 'Send to Navex', 'abcdo-wc-navex' ) . '</button>';
            echo '<span class="spinner" style="float: none; margin-top: 4px;"></span>';
        }
    }

    /**
     * Gérer la requête AJAX pour l'envoi manuel.
     */
    public function ajax_send_parcel() {
        check_ajax_referer( 'abcdo_wc_navex_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'edit_shop_orders' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'abcdo-wc-navex' ) ) );
        }
        if ( ! isset( $_POST['order_id'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Missing order ID.', 'abcdo-wc-navex' ) ) );
        }

        $order_id = intval( $_POST['order_id'] );
        $order    = wc_get_order( $order_id );
        if ( ! $order ) {
            wp_send_json_error( array( 'message' => __( 'Order not found.', 'abcdo-wc-navex' ) ) );
        }

        $data = array(
            'nom'      => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
            'adresse'   => $order->get_shipping_address_1(),
            'ville'     => $order->get_shipping_city(),
            'tel' => $order->get_billing_phone(),
            'prix'    => $order->get_total(),
            'id'        => $order->get_id(),
        );

        $response = $this->api_client->create_shipment( $data );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => $response->get_error_message() ) );
        }

        if ( isset( $response['status'] ) && 'Product Added.' === $response['status_message'] ) {
            $tracking_id = isset( $response['colis']['id'] ) ? $response['colis']['id'] : 'N/A';
            $order->update_meta_data( '_navex_shipping_status', 'Envoyé' );
            $order->update_meta_data( '_navex_tracking_id', $tracking_id );
            $order->add_order_note( sprintf( __( 'Colis envoyé à Navex avec succès. ID de suivi : %s', 'abcdo-wc-navex' ), $tracking_id ) );
            $order->save();
            wp_send_json_success( array( 'message' => __( 'Colis envoyé à Navex avec succès !', 'abcdo-wc-navex' ) ) );
        } else {
            $error_message = isset( $response['status_message'] ) ? $response['status_message'] : __( 'Erreur inconnue de l\'API Navex.', 'abcdo-wc-navex' );
            $order->add_order_note( sprintf( __( 'Erreur lors de l\'envoi à Navex : %s', 'abcdo-wc-navex' ), $error_message ) );
            wp_send_json_error( array( 'message' => $error_message ) );
        }
    }

    /**
     * Gérer la requête AJAX pour récupérer les colis synchronisés avec WooCommerce.
     */
    public function ajax_get_parcels() {
        check_ajax_referer( 'abcdo_wc_navex_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'abcdo-wc-navex' ) ) );
        }

        $orders = wc_get_orders( array(
            'limit'      => -1,
            'meta_key'   => '_navex_tracking_id',
            'meta_compare' => 'EXISTS',
        ) );

        $parcels = array();
        foreach ( $orders as $order ) {
            $parcels[] = array(
                'order_id'    => $order->get_id(),
                'tracking_id' => $order->get_meta( '_navex_tracking_id' ),
                'status'      => $order->get_meta( '_navex_shipping_status' ),
                'date'        => $order->get_date_created()->format( 'Y-m-d' ),
                'actions'     => sprintf(
                    '<button class="button navex-details-btn" data-tracking-id="%1$s">%2$s</button> <button class="button button-link-delete navex-delete-btn" data-tracking-id="%1$s">%3$s</button>',
                    esc_attr( $order->get_meta( '_navex_tracking_id' ) ),
                    esc_html__( 'Details', 'abcdo-wc-navex' ),
                    esc_html__( 'Delete', 'abcdo-wc-navex' )
                ),
            );
        }

        wp_send_json_success( $parcels );
    }

    /**
     * Gérer la requête AJAX pour récupérer les détails d'un colis.
     */
    public function ajax_get_parcel_details() {
        check_ajax_referer( 'abcdo_wc_navex_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'abcdo-wc-navex' ) ) );
        }
        if ( ! isset( $_POST['tracking_id'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Missing tracking ID.', 'abcdo-wc-navex' ) ) );
        }

        $tracking_id = sanitize_text_field( $_POST['tracking_id'] );
        $response    = $this->api_client->get_shipment_status( $tracking_id );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => $response->get_error_message() ) );
        }

        $html = '<ul>';
        if( is_array($response) ) {
            foreach ( $response as $key => $value ) {
                $html .= sprintf( '<li><strong>%s:</strong> %s</li>', esc_html( ucwords( str_replace( '_', ' ', $key ) ) ), esc_html( is_array($value) ? json_encode($value) : $value ) );
            }
        }
        $html .= '</ul>';

        wp_send_json_success( array( 'html' => $html ) );
    }

    /**
     * Gérer la requête AJAX pour supprimer un colis.
     */
    public function ajax_delete_parcel() {
        check_ajax_referer( 'abcdo_wc_navex_admin_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => __( 'Permission denied.', 'abcdo-wc-navex' ) ) );
        }
        if ( ! isset( $_POST['tracking_id'] ) ) {
            wp_send_json_error( array( 'message' => __( 'Missing tracking ID.', 'abcdo-wc-navex' ) ) );
        }

        $tracking_id = sanitize_text_field( $_POST['tracking_id'] );
        $response    = $this->api_client->cancel_shipment( $tracking_id );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => $response->get_error_message() ) );
        }

        $orders = wc_get_orders( array(
            'limit' => 1,
            'meta_key' => '_navex_tracking_id',
            'meta_value' => $tracking_id
        ) );

        if ( ! empty( $orders ) ) {
            $order = $orders[0];
            $order->delete_meta_data( '_navex_shipping_status' );
            $order->delete_meta_data( '_navex_tracking_id' );
            $order->add_order_note( sprintf( __( 'Navex parcel %s has been deleted.', 'abcdo-wc-navex' ), $tracking_id ) );
            $order->save();
        }

        wp_send_json_success( array( 'message' => __( 'Parcel deleted successfully.', 'abcdo-wc-navex' ) ) );
    }
}
