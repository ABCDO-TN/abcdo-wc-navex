<?php
/**
 * Plugin Name:       ABCDO Navex Integration for WooCommerce
 * Plugin URI:        https://github.com/abcd-wc-navex/abcdo-wc-navex
 * Description:       Intègre l'API de livraison Navex avec WooCommerce pour automatiser la création de colis.
 * Version:           1.0.0
 * Author:            ABCDO
 * Author URI:        https://github.com/abcd-wc-navex
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       abcdo-wc-navex
 * Domain Path:       /languages
 *
 * WC requires at least: 3.0
 * WC tested up to: 8.4
 */

// Empêcher l'accès direct au fichier
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Définir les constantes du plugin
define( 'ABCDO_WC_NAVEX_VERSION', '1.0.0' );
define( 'ABCDO_WC_NAVEX_PATH', plugin_dir_path( __FILE__ ) );
define( 'ABCDO_WC_NAVEX_URL', plugin_dir_url( __FILE__ ) );
define( 'ABCDO_WC_NAVEX_BASENAME', plugin_basename( __FILE__ ) );


/**
 * La fonction principale qui s'exécute au chargement du plugin.
 */
function abcd_wc_navex_init() {
    // Charger les fichiers nécessaires
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-integration.php' );
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-api.php' );
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-admin.php' );
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-updater.php' );

    // Ajouter la classe d'intégration à WooCommerce
    add_filter( 'woocommerce_integrations', 'abcd_wc_navex_add_integration' );

    // Instancier les classes
    new ABCD_WC_Navex_Admin();
    
    if ( is_admin() ) {
        new ABCD_WC_Navex_Updater( __FILE__ );
    }
}
add_action( 'plugins_loaded', 'abcd_wc_navex_init' );

/**
 * Ajouter l'intégration à la liste des intégrations de WooCommerce.
 *
 * @param array $integrations Les intégrations existantes.
 * @return array
 */
function abcd_wc_navex_add_integration( $integrations ) {
    $integrations[] = 'ABCD_WC_Navex_Integration';
    return $integrations;
}
