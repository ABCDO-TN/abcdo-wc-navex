<?php
/**
 * Plugin Name:       ABCDO Navex Integration for WooCommerce
 * Plugin URI:        https://github.com/ABCDO-TN/abcdo-wc-navex
 * Description:       Integrates Navex delivery API with WooCommerce to automate parcel creation.
 * Version:           1.0.21
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

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Declares compatibility with High-Performance Order Storage (HPOS).
 */
add_action( 'before_woocommerce_init', function() {
    if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
    }
} );

// Define plugin constants
define( 'ABCDO_WC_NAVEX_VERSION', '1.0.21' );
define( 'ABCDO_WC_NAVEX_PATH', plugin_dir_path( __FILE__ ) );
define( 'ABCDO_WC_NAVEX_URL', plugin_dir_url( __FILE__ ) );
define( 'ABCDO_WC_NAVEX_BASENAME', plugin_basename( __FILE__ ) );


/**
 * The main function that runs on plugin load.
 */
function abcd_wc_navex_init() {
    // Load necessary files
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-crypto.php' );
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-api.php' );
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-admin.php' );
    include_once( ABCDO_WC_NAVEX_PATH . 'includes/class-abcd-wc-navex-updater.php' );

    // Instantiate classes
    new ABCD_WC_Navex_Admin();
    
    if ( is_admin() ) {
        $updater = new ABCD_WC_Navex_Updater( __FILE__ );
        $updater->init();
    }
}
add_action( 'plugins_loaded', 'abcd_wc_navex_init' );

/**
 * Loads the plugin text domain for translation.
 */
function abcd_wc_navex_load_textdomain() {
    load_plugin_textdomain( 'abcdo-wc-navex', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'init', 'abcd_wc_navex_load_textdomain' );
