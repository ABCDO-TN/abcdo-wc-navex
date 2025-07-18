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
        // Meta box sur la page de commande
        add_action( 'add_meta_boxes', array( $this, 'add_navex_meta_box' ) );

        // Action AJAX pour l'envoi manuel
        add_action( 'wp_ajax_abcd_wc_navex_send_parcel', array( $this, 'ajax_send_parcel' ) );

        // Scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

        // Traductions
        add_action( 'init', array( $this, 'load_textdomain' ) );

        // Page de configuration indépendante
        add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Ajouter le menu au tableau de bord.
     */
    public function add_admin_menu() {
        add_menu_page(
            __( 'Navex Delivery', 'abcdo-wc-navex' ),
            __( 'Navex Delivery', 'abcdo-wc-navex' ),
            'manage_options',
            'abcdo-wc-navex',
            array( $this, 'render_settings_page' ),
            'dashicons-truck',
            56
        );
    }

    /**
     * Enregistrer les réglages.
     */
    public function register_settings() {
        register_setting( 'abcdo_wc_navex_options', 'abcdo_wc_navex_api_token' );
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
                do_settings_sections( 'abcdo-wc-navex' );
                ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row"><?php _e( 'Token d\'API Navex', 'abcdo-wc-navex' ); ?></th>
                        <td>
                            <input type="text" name="abcdo_wc_navex_api_token" value="<?php echo esc_attr( get_option('abcdo_wc_navex_api_token') ); ?>" size="50" />
                            <p class="description"><?php _e( 'Entrez votre token d\'authentification fourni par Navex.', 'abcdo-wc-navex' ); ?></p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
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

        // ID de l'écran HPOS et de l'écran classique
        $hpos_screen_id = wc_get_page_screen_id( 'shop-order' );
        $classic_screen_id = 'shop_order';

        if ( $screen->id === $classic_screen_id || $screen->id === $hpos_screen_id ) {
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
        // Enregistrer la meta box pour l'écran classique et l'écran HPOS.
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

        // Vérifier la réponse de l'API (condition de succès élargie)
        if ( ( isset( $response['status'] ) && $response['status_message'] === 'Product Added.' ) || ( isset( $response['status'] ) && is_numeric( $response['status'] ) ) ) {
            // Mettre à jour le statut dans les métadonnées de la commande (compatible HPOS)
            $order->update_meta_data( '_navex_shipping_status', 'Envoyé' );
            $order->add_order_note( 'Colis envoyé à Navex avec succès. Réponse de l\'API : ' . json_encode( $response ) );
            $order->save();
            wp_send_json_success( array( 'message' => 'Colis envoyé à Navex avec succès !' ) );
        } else {
            $error_message = isset( $response['status_message'] ) ? $response['status_message'] : 'Erreur inconnue de l\'API Navex.';
            $order->add_order_note( 'Erreur lors de l\'envoi à Navex : ' . $error_message );
            wp_send_json_error( array( 'message' => $error_message ) );
        }
    }
}
