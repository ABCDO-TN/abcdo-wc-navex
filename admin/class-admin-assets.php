<?php
/**
 * Manages admin-specific assets.
 *
 * @package Abcdo_Wc_Navex
 * @version 1.1.1
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Class Abcdo_Wc_Navex_Admin_Assets
 */
class Abcdo_Wc_Navex_Admin_Assets {

    /**
     * Initialize hooks.
     *
     * @since 1.2.0
     */
    public function init() {
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
    }

    /**
     * Enqueue admin scripts and styles.
     *
     * @since 1.2.0
     * @param string $hook_suffix The current admin page.
     */
    public function enqueue_assets( $hook_suffix ) {
        // Only load on our specific settings page and the order edit page.
        if ( 'woocommerce_page_abcdo-wc-navex-settings' !== $hook_suffix && 'post.php' !== $hook_suffix ) {
            return;
        }

        wp_enqueue_style(
            'abcdo-wc-navex-admin-styles',
            ABCDO_WC_NAVEX_URL . 'admin/assets/css/abcd-navex-admin.css',
            array(),
            ABCDO_WC_NAVEX_VERSION
        );

        wp_enqueue_script(
            'abcdo-wc-navex-admin-scripts',
            ABCDO_WC_NAVEX_URL . 'admin/assets/js/abcd-navex-admin.js',
            array( 'jquery' ),
            ABCDO_WC_NAVEX_VERSION,
            true
        );

        // Localize script for AJAX
        wp_localize_script(
            'abcdo-wc-navex-admin-scripts',
            'abcdo_wc_navex_ajax',
            array(
                'ajax_url' => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'abcdo_wc_navex_admin_nonce' ),
            )
        );
    }
}
