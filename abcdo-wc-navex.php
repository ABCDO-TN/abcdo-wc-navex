<?php
/**
 * Plugin Name:       ABCDO Navex Integration for WooCommerce
 * Plugin URI:        https://github.com/ABCDO-TN/abcdo-wc-navex
 * Description:       Intègre l'API de livraison Navex avec WooCommerce pour automatiser la création de colis et la synchronisation des statuts.
 * Version:           1.1.1
 * Author:            ABCDO
 * Author URI:        https://abcdo.tn
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       abcdo-wc-navex
 * Domain Path:       /languages
 *
 * @package           Abcdo_Wc_Navex
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
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

// Définir les constantes du plugin
define( 'ABCDO_WC_NAVEX_VERSION', '1.1.1' );
define( 'ABCDO_WC_NAVEX_PATH', plugin_dir_path( __FILE__ ) );
define( 'ABCDO_WC_NAVEX_URL', plugin_dir_url( __FILE__ ) );
define( 'ABCDO_WC_NAVEX_BASENAME', plugin_basename( __FILE__ ) );

/**
 * Hook d'activation du plugin.
 */
register_activation_hook( __FILE__, 'abcdo_wc_navex_activate' );
function abcdo_wc_navex_activate() {
    require_once ABCDO_WC_NAVEX_PATH . 'includes/class-cron-manager.php';
    Abcdo_Wc_Navex_Cron_Manager::schedule_event();
}

/**
 * Hook de désactivation du plugin.
 */
register_deactivation_hook( __FILE__, 'abcdo_wc_navex_deactivate' );
function abcdo_wc_navex_deactivate() {
    require_once ABCDO_WC_NAVEX_PATH . 'includes/class-cron-manager.php';
    Abcdo_Wc_Navex_Cron_Manager::unschedule_event();
}

/**
 * La fonction principale qui s'exécute au chargement du plugin.
 */
function abcdo_wc_navex_init() {
    // Charger les fichiers nécessaires
    require_once ABCDO_WC_NAVEX_PATH . 'includes/class-encryption-service.php';
    require_once ABCDO_WC_NAVEX_PATH . 'includes/class-logger.php';
    require_once ABCDO_WC_NAVEX_PATH . 'includes/class-api-client.php';
    require_once ABCDO_WC_NAVEX_PATH . 'includes/class-cron-manager.php';
    require_once ABCDO_WC_NAVEX_PATH . 'includes/class-order-sync.php';
    require_once ABCDO_WC_NAVEX_PATH . 'includes/class-migration.php';
    require_once ABCDO_WC_NAVEX_PATH . 'admin/class-admin-assets.php';
    require_once ABCDO_WC_NAVEX_PATH . 'admin/class-admin-settings.php';
    require_once ABCDO_WC_NAVEX_PATH . 'admin/class-admin.php';
    require_once ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-updater.php';

    // Lancer la migration si nécessaire
    Abcdo_Wc_Navex_Migration::run();

    // Instancier les classes
    new Abcdo_Wc_Navex_Admin();
    new Abcdo_Wc_Navex_Cron_Manager();

    if ( is_admin() ) {
        $settings = new Abcdo_Wc_Navex_Admin_Settings();
        $settings->init();

        $assets = new Abcdo_Wc_Navex_Admin_Assets();
        $assets->init();
        
        $updater = new ABCD_WC_Navex_Updater( __FILE__ );
        $updater->init();
    }
}
add_action( 'plugins_loaded', 'abcdo_wc_navex_init' );

/**
 * Charge les traductions du plugin.
 */
function abcdo_wc_navex_load_textdomain() {
    load_plugin_textdomain( 'abcdo-wc-navex', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', 'abcdo_wc_navex_load_textdomain' );
