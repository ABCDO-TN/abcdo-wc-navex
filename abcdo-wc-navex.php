<?php
/**
 * Plugin Name:       ABCDO Navex Integration for WooCommerce
 * Plugin URI:        https://github.com/ABCDO-TN/abcdo-wc-navex
 * Description:       Intègre l'API de livraison Navex avec WooCommerce pour automatiser la création de colis.
 * Version:           1.0.23
 * Author:            ABCDO
 * Author URI:        https://abcdo.tn
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

/**
 * Déclare la compatibilité avec High-Performance Order Storage (HPOS).
 */
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( AutomatticWooCommerceUtilitiesFeaturesUtil::class ) ) {
        AutomatticWooCommerceUtilitiesFeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

// Définir les constantes du plugin
define( 'ABCDO_WC_NAVEX_VERSION', '1.0.23' );
define( 'ABCDO_WC_NAVEX_PATH', plugin_dir_path( __FILE__ ) );
define( 'ABCDO_WC_NAVEX_URL', plugin_dir_url( __FILE__ ) );
define( 'ABCDO_WC_NAVEX_BASENAME', plugin_basename( __FILE__ ) );


/**
 * La fonction principale qui s'exécute au chargement du plugin.
 */
function abcd_wc_navex_init() {
    // Charger les fichiers nécessaires
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-crypto.php' );
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-api.php' );
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-admin.php' );
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-updater.php' );

    // Instancier les classes
    new ABCD_WC_Navex_Admin();
    
    if ( is_admin() ) {
        $updater = new ABCD_WC_Navex_Updater( __FILE__ );
        $updater->init();
    }
}
add_action( 'plugins_loaded', 'abcd_wc_navex_init' );

/**
 * Charge les traductions du plugin.
 */
function abcd_wc_navex_load_textdomain() {
    load_plugin_textdomain( 'abcdo-wc-navex', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'abcd_wc_navex_load_textdomain' );
