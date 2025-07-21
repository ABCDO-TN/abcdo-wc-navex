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
     * L'icône du menu encodée en base64.
     *
     * @var string
     */
    private $menu_icon;

    /**
     * Constructeur.
     */
    public function __construct() {
        // Inclure la classe de chiffrement
        include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-crypto.php' );

        // Définir l'icône du menu
        $this->menu_icon = 'data:image/svg+xml;base64,' . base64_encode(
            '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12 2L4 6L12 10L20 6L12 2Z" fill="white"/><path d="M4 18L12 22L20 18" fill="white"/><path d="M4 12L12 16L20 12" fill="white"/></svg>'
        );

        // Hooks principaux
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // Meta box sur la page de commande
        add_action( 'add_meta_boxes', array( $this, 'add_navex_meta_box' ) );

        // Actions AJAX
        add_action( 'wp_ajax_abcd_wc_navex_send_parcel', array( $this, 'ajax_send_parcel' ) );
        add_action( 'wp_ajax_abcd_wc_navex_get_parcels', array( $this, 'ajax_get_parcels' ) );
        add_action( 'wp_ajax_abcd_wc_navex_get_parcel_details', array( $this, 'ajax_get_parcel_details' ) );
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

        add_submenu_page(
            'abcdo-wc-navex',
            __( 'Settings', 'abcdo-wc-navex' ),
            __( 'Settings', 'abcdo-wc-navex' ),
            'manage_options',
            'abcdo-wc-navex-settings',
            array( $this, 'render_settings_page' )
        );
    }

    /**
     * Enregistrer les réglages avec la Settings API.
     */
    public function register_settings() {
        add_settings_section(
            'abcdo_wc_navex_api_section',
            __( 'API Credentials', 'abcdo-wc-navex' ),
            null,
            'abcdo-wc-navex-settings'
        );

        $fields = array(
            'api_token_add'    => __( 'Token d\'ajout', 'abcdo-wc-navex' ),
            'api_token_get'    => __( 'Token de récupération', 'abcdo-wc-navex' ),
            'api_token_delete' => __( 'Token de suppression', 'abcdo-wc-navex' ),
        );

        foreach ( $fields as $id => $title ) {
            $option_name = 'abcdo_wc_navex_' . $id;
            register_setting( 'abcdo_wc_navex_options', $option_name, array( 'sanitize_callback' => array( $this, 'encrypt_setting' ) ) );
            add_settings_field(
                $option_name,
                $title,
                array( $this, 'render_text_field' ),
                'abcdo-wc-navex-settings',
                'abcdo_wc_navex_api_section',
                array(
                    'label_for' => $option_name,
                    'name'      => $option_name,
                )
            );
        }
    }

    /**
     * Chiffrer la valeur d'un réglage avant de la sauvegarder.
     */
    public function encrypt_setting( $value ) {
        if ( empty( $value ) ) {
            return '';
        }
        return ABCD_WC_Navex_Crypto::encrypt( $value );
    }

    /**
     * Afficher un champ de texte standard pour la Settings API (en déchiffrant la valeur).
     */
    public function render_text_field( $args ) {
        $option_value = get_option( $args['name'] );
        $decrypted_value = '';
        if ( ! empty( $option_value ) ) {
            $decrypted_value = ABCD_WC_Navex_Crypto::decrypt( $option_value );
        }
        printf(
            '<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" />',
            esc_attr( $args['name'] ),
            esc_attr( $decrypted_value )
        );
    }

    /**
     * Afficher la page de réglages.
     */
    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields( 'abcdo_wc_navex_options' );
                do_settings_sections( 'abcdo-wc-navex-settings' );
                submit_button( __( 'Save Settings', 'abcdo-wc-navex' ) );
                ?>
            </form>
        </div>
        <?php
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
            <div id="navex-details-modal-content">
                <h2><?php esc_html_e( 'Parcel Details', 'abcdo-wc-navex' ); ?></h2>
                <div id="navex-modal-body"><span class="spinner is-active"></span></div>
                <button id="navex-modal-close" class="button button-secondary"><?php esc_html_e( 'Close', 'abcdo-wc-navex' ); ?></button>
            </div>
            <div id="navex-modal-backdrop"></div>
        </div>
        <?php
    }

    /**
     * Charger les scripts et styles pour l'admin.
     */
    public function enqueue_scripts( $hook_suffix ) {
        $screen = get_current_screen();
        if ( ! $screen ) {
            return;
        }

        $is_navex_page = strpos( $hook_suffix, 'abcdo-wc-navex' ) !== false;

        if ( $is_navex_page ) {
            wp_enqueue_style( 'abcd-wc-navex-admin-css', ABCDO_WC_NAVEX_URL . 'assets/css/admin.css', array(), ABCDO_WC_NAVEX_VERSION );
        }

        $hpos_screen_id = wc_get_page_screen_id( 'shop-order' );
        $classic_screen_id = 'shop_order';

        if ( $screen->id === $classic_screen_id || $screen->id === $hpos_screen_id || $is_navex_page ) {
            wp_enqueue_script(
                'abcd-wc-navex-admin-js',
                ABCDO_WC_NAVEX_URL . 'assets/js/admin.js',
                array( 'jquery' ),
                ABCDO_WC_NAVEX_VERSION,
                true
            );
            wp_localize_script( 'abcd-wc-navex-admin-js', 'abcd_wc_navex_ajax', array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'abcd_wc_navex_ajax_nonce' ),
            ) );
        }
    }

    /**
     * Ajouter le meta box sur la page de commande.
     */
    public function add_navex_meta_box() {
        $screens = array_unique( array( 'shop_order', wc_get_page_screen_id( 'shop-order' ) ) );
        foreach ( $screens as $screen ) {
            add_meta_box(
                'abcd-wc-navex-meta-box',
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
            echo '<button type="button" id="abcd-wc-navex-send-btn" class="button button-primary" data-order-id="' . esc_attr( $order_id ) . '">' . esc_html__( 'Send to Navex', 'abcdo-wc-navex' ) . '</button>';
            echo '<span class="spinner" style="float: none; margin-top: 4px;"></span>';
        }
    }

    /**
     * Gérer la requête AJAX pour l'envoi manuel.
     */
    public function ajax_send_parcel() {
        check_ajax_referer( 'abcd_wc_navex_ajax_nonce', 'nonce' );
        if ( ! current_user_can( 'edit_shop_orders' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ) );
        }
        if ( ! isset( $_POST['order_id'] ) ) {
            wp_send_json_error( array( 'message' => 'Missing order ID.' ) );
        }

        $order_id = intval( $_POST['order_id'] );
        $order = wc_get_order( $order_id );
        if ( ! $order ) {
            wp_send_json_error( array( 'message' => 'Order not found.' ) );
        }

        $data = array( /* ... data preparation ... */ );
        $api = new ABCD_WC_Navex_API();
        $response = $api->send_parcel( $data );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => $response->get_error_message() ) );
        }

        // Supposons que la réponse contienne un 'tracking_id'
        $tracking_id = isset( $response['tracking_id'] ) ? $response['tracking_id'] : 'N/A';

        if ( ( isset( $response['status'] ) && $response['status_message'] === 'Product Added.' ) || ( isset( $response['status'] ) && is_numeric( $response['status'] ) ) ) {
            $order->update_meta_data( '_navex_shipping_status', 'Envoyé' );
            $order->update_meta_data( '_navex_tracking_id', $tracking_id ); // Sauvegarder l'ID de suivi
            $order->add_order_note( 'Colis envoyé à Navex avec succès. ID de suivi : ' . $tracking_id );
            $order->save();
            wp_send_json_success( array( 'message' => 'Colis envoyé à Navex avec succès !' ) );
        } else {
            $error_message = isset( $response['status_message'] ) ? $response['status_message'] : 'Erreur inconnue de l\'API Navex.';
            $order->add_order_note( 'Erreur lors de l\'envoi à Navex : ' . $error_message );
            wp_send_json_error( array( 'message' => $error_message ) );
        }
    }

    /**
     * Gérer la requête AJAX pour récupérer les colis synchronisés avec WooCommerce.
     */
    public function ajax_get_parcels() {
        check_ajax_referer( 'abcd_wc_navex_ajax_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ) );
        }

        $args = array(
            'limit' => -1, // Récupérer toutes les commandes
            'status' => 'any',
            '_navex_tracking_id' => 'EXISTS',
        );
        $orders = wc_get_orders( $args );

        $parcels = array();
        foreach ( $orders as $order ) {
            $parcels[] = array(
                'order_id'    => $order->get_id(),
                'tracking_id' => $order->get_meta( '_navex_tracking_id' ),
                'status'      => $order->get_meta( '_navex_shipping_status' ),
                'date'        => $order->get_date_created()->format( 'Y-m-d' ),
                'actions'     => sprintf(
                    '<a href="#" class="button navex-details-btn" data-tracking-id="%s">%s</a>',
                    esc_attr( $order->get_meta( '_navex_tracking_id' ) ),
                    esc_html__( 'Details', 'abcdo-wc-navex' )
                ),
            );
        }

        wp_send_json_success( $parcels );
    }

    /**
     * Gérer la requête AJAX pour récupérer les détails d'un colis.
     */
    public function ajax_get_parcel_details() {
        check_ajax_referer( 'abcd_wc_navex_ajax_nonce', 'nonce' );
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( array( 'message' => 'Permission denied.' ) );
        }
        if ( ! isset( $_POST['tracking_id'] ) ) {
            wp_send_json_error( array( 'message' => 'Missing tracking ID.' ) );
        }

        $tracking_id = sanitize_text_field( $_POST['tracking_id'] );
        $api = new ABCD_WC_Navex_API();
        $response = $api->get_parcel_details( $tracking_id );

        if ( is_wp_error( $response ) ) {
            wp_send_json_error( array( 'message' => $response->get_error_message() ) );
        }

        // La réponse est une chaîne de caractères, pas un tableau.
        // On l'enveloppe simplement dans un paragraphe.
        $html = '<p>' . esc_html( $response ) . '</p>';

        wp_send_json_success( array( 'html' => $html ) );
    }
}
